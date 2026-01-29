<?php
$user_is_logged_in = taoh_user_is_logged_in() ?? false;

if (!$user_is_logged_in) {
    header("Location: " . TAOH_SITE_URL_ROOT . '/login');
}

$taoh_home_url = (defined('TAOH_PAGE_URL') && TAOH_PAGE_URL) ? TAOH_PAGE_URL : TAOH_SITE_URL_ROOT;
$directory_flags_to_show = defined('TAOH_DIRECTORY_FLAGS_TO_SHOW') ? TAOH_DIRECTORY_FLAGS_TO_SHOW : [];

taoh_get_header();

$data = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];
if(empty($ptoken) || $ptoken == 'stlo'){
    $ptoken = $data->ptoken;
}

$raw_my_profile_stage = $data->profile_stage ?? ($data->profile_complete ?? 0);
$my_profile_stage = max(0, is_numeric($raw_my_profile_stage) ? (int)$raw_my_profile_stage : 0);
$my_ptoken = $data->ptoken ?? '';
$my_following_count = (int)($data->tao_following_count ?? 0);
$my_followers_count = (int)($data->tao_followers_count ?? 0);

$return = taoh_get_user_info($ptoken,'full',1);
$pfdata = json_decode($return, true);

$profile_user_data = $pfdata['output']['user'] ?? [];

$is_my_profile_view = $my_ptoken === $ptoken;
$raw_profile_stage = $profile_user_data['profile_stage'] ?? ($profile_user_data['profile_complete'] ?? 0);
$profile_stage = max(0, is_numeric($raw_profile_stage) ? (int)$raw_profile_stage : 0);


$titles = array_filter($profile_user_data['title'] ?? [], function ($t) {
    return !empty($t['value']);
});
$profile_type = $profile_user_data['type'] ?? 'Professional';
$full_location = $profile_user_data['full_location'] ?? '';
$profile_tag_category = $profile_user_data['tags'] ?? [];
$my_profile_tag_category = $is_my_profile_view ? $profile_tag_category : ($data->tags ?? []);

$sub_domain_value = $profile_user_data['site']['sub_domain_value'] ?? '';
$source = json_decode($sub_domain_value, true)[0]['source'] ?? '';

$is_same_source = defined('TAOH_SITE_URL_ROOT') && $source === TAOH_SITE_URL_ROOT;

$about_me = implode(' ', array_filter(explode(' ', $profile_user_data['aboutme'] ?? '')));
$fun_fact = taoh_title_desc_decode($profile_user_data['funfact'] ?? '');
$hobbies = isset($profile_user_data['hobbies']) ? json_decode(implode(' ', array_filter(explode(' ', $profile_user_data['hobbies'])))) : [];

$get_skill = $profile_user_data['skill'] ?? '';
$data_keywords = (array)($profile_user_data['keywords'] ?? []);
$following_count = (int)($profile_user_data['tao_following_count'] ?? 0);
$followers_count = (int)($profile_user_data['tao_followers_count'] ?? 0);

if (isset($profile_user_data['avatar_image']) && $profile_user_data['avatar_image'] != '') {
    $avatar_image = $profile_user_data['avatar_image'];
} else {
    if (isset($profile_user_data['avatar']) && $profile_user_data['avatar'] != 'default') {
        $avatar_image = TAOH_OPS_PREFIX . '/avatar/PNG/128/' . $profile_user_data['avatar'] . '.png';
    } else {
        $avatar_image = TAOH_OPS_PREFIX . '/avatar/PNG/128/avatar_def.png';
    }
}

// echo "<pre>"; print_r($pfdata); echo "</pre>";

$tag_category = defined('TAOH_TAG_CATEGORY') ? TAOH_TAG_CATEGORY : [];
$tag_category_form = defined('TAOH_TAG_CATEGORY_FORM') ? TAOH_TAG_CATEGORY_FORM : [];

if (isset($profile_user_data['education']) && is_array($profile_user_data['education'])) {
    $edu_encode = json_encode($profile_user_data['education']);
    $edu_list = json_decode($edu_encode, true);
    $edu_tot_count = array_key_last($edu_list) + 1;
    $edu_last_key = array_key_last($edu_list);
} else {
    $edu_tot_count = 0;
    $edu_last_key = 0;
    $edu_list = '';
}
if (isset($profile_user_data['employee']) && is_array($profile_user_data['employee'])) {
    $emp_encode = json_encode($profile_user_data['employee']);
    $emp_list = json_decode($emp_encode, true);
    $emp_tot_count = array_key_last($emp_list) + 1;
    $emp_last_key = array_key_last($emp_list);
} else {
    $emp_tot_count = 0;
    $emp_last_key = 0;
    $emp_list = '';
}

if($edu_list != ''){
    foreach($edu_list as $edu_keys => $edu_vals){
        $edu_year[$edu_keys] = $edu_vals['edu_complete_year'];
        $edu_list[$edu_keys]['keys'] = $edu_keys;
    }
    array_multisort($edu_year, SORT_DESC, $edu_list);
}

if($emp_list != ''){
    foreach($emp_list as $ekeys => $evals){
        $emp_year[$ekeys] = $evals['emp_year_end'];
        $emp_list[$ekeys]['keys'] = $ekeys;
    }
    array_multisort($emp_year, SORT_DESC, $emp_list);
}

// Safe get function for tags nested objects
function safe_get($object, $field1, $field2) {
    if (!(isset($object['tags_data']) && isset($object['tags_data'][$field1]))) {
        return '';
    }

    $tags_data = json_decode($object['tags_data'][$field1], true);

    return $tags_data[$field2] ?? '';
}


if (!$is_my_profile_view && $my_profile_stage == 0) {
    taoh_set_error_message('Please complete your profile to gain access to view other profiles.');
    taoh_redirect(TAOH_SITE_URL_ROOT . '/settings');
}

$follow_status = 0;
if ($user_is_logged_in && !$is_my_profile_view) {
    $taoh_vals = [
        'mod' => 'core',
        'token' => taoh_get_api_token(),
        'ptoken' => $ptoken,
        'follow_type' => 'followers',
    ];
    $taoh_vals['cache_name'] = 'followup_' . $taoh_vals['follow_type'] . '_list_' . $taoh_vals['ptoken'] . '_' . hash('crc32', http_build_query($taoh_vals));

     $taoh_vals['cache_required'] = 0;
//     $taoh_vals['debug_api'] = 1;
//     echo taoh_apicall_get('core.followup.get.list', $taoh_vals);exit();

    $followup_result = taoh_apicall_get('core.followup.get.list', $taoh_vals);
    $followup_result_array = json_decode($followup_result, true);
    if ($followup_result_array && in_array($followup_result_array['success'], [true, 'true']) && !empty($followup_result_array['output'])) {
        foreach ($followup_result_array['output'] as $follower) {
            if (!empty($follower['ptoken']) && $follower['ptoken'] === $my_ptoken) {
                $follow_status = 1;
                break;
            }
        }
    }
}

?>

<style>
    .resume .std-btn {
        min-width: auto;
    }

    .profile_follow_btn {
        background-color: transparent;
    }

    .profile_follow_btn[data-follow_status="1"] {
        background-color: #2557A7 !important;
        border: 1px solid #2557A7 !important;
        color: #ffffff !important;
    }

    .my-interest-item {
        font-size: large;
    }

    #directory_flag + .select2-container {
        max-width: 300px !important;
    }

    #directory_flag + .select2-container--default .select2-selection--single {
        height: 46px !important;
        /*border: 1px solid #000000;*/
        /*border-radius: 12px;*/
    }

    #directory_flag + .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 46px !important;
        padding: 0 15px !important;
        font-size: 18px !important;
        font-weight: 400 !important;
    }

    #directory_flag + .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
    }
</style>

<div class="resume pb-5 mb-5">
    <div class="bg-div"></div>

    <div class="px-3">
        <div class="container res-con px-3 py-4 p-lg-5" style="margin-top: -160px;">

        <!--Alert -- Tell your Professional Story ! -->
        <!-- <div class="pro-alert flex-column flex-md-row px-4 py-3 my-3">
            <svg style="min-width: fit-content;" class="pl-lg-2" width="50" height="47" viewBox="0 0 50 47" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.9688 4.7H32.0312C32.4609 4.7 32.8125 5.0525 32.8125 5.48333V9.4H17.1875V5.48333C17.1875 5.0525 17.5391 4.7 17.9688 4.7ZM12.5 5.48333V9.4H6.25C2.80273 9.4 0 12.2102 0 15.6667V25.0667H18.75H31.25H50V15.6667C50 12.2102 47.1973 9.4 43.75 9.4H37.5V5.48333C37.5 2.45771 35.0488 0 32.0312 0H17.9688C14.9512 0 12.5 2.45771 12.5 5.48333ZM50 28.2H31.25V31.3333C31.25 33.0665 29.8535 34.4667 28.125 34.4667H21.875C20.1465 34.4667 18.75 33.0665 18.75 31.3333V28.2H0V40.7333C0 44.1898 2.80273 47 6.25 47H43.75C47.1973 47 50 44.1898 50 40.7333V28.2Z" fill="#379D0B"/>
            </svg>
            <div>
                <h5>Tell your Professional Story !</h5>
                <p>Add your education, experience and other key details to make better connections and stand out when others search.</p>
            </div>
        </div> -->

            <?php
            if ($is_my_profile_view || $my_profile_stage > 0) {
                ?>
                <div class="mt-5 " >

                    <div class="r-right" style="flex: 1;">
                        <div class="d-flex flex-wrap align-items-center flex-md-nowrap" style="gap: 12px;"><!-- b-btm -->
                            <?php
                            if ($is_my_profile_view || $my_profile_stage >= 1) {
                                ?>
                                <div class="r-left" style="max-width: 138px;">
                                    <img class="profile-img mb-3" src="<?php echo $avatar_image;?>" alt="">

                                    <?php
                                    if (($is_my_profile_view || $my_profile_stage >= 2) && !empty($profile_user_data['mylink'] ?? '')) {
                                        ?>
                                        <div class="d-flex align-items-center justify-content-center" style="gap: 12px;">
                                            <a href="<?= $profile_user_data['mylink']; ?>" target="_blank">
                                                <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M21.3571 0H1.63772C0.734152 0 0 0.74442 0 1.65826V21.3417C0 22.2556 0.734152 23 1.63772 23H21.3571C22.2607 23 23 22.2556 23 21.3417V1.65826C23 0.74442 22.2607 0 21.3571 0ZM6.95134 19.7143H3.54241V8.73795H6.95647V19.7143H6.95134ZM5.24687 7.23884C4.15335 7.23884 3.27031 6.35067 3.27031 5.26228C3.27031 4.17388 4.15335 3.28571 5.24687 3.28571C6.33527 3.28571 7.22344 4.17388 7.22344 5.26228C7.22344 6.3558 6.3404 7.23884 5.24687 7.23884ZM19.7297 19.7143H16.3208V14.375C16.3208 13.1018 16.2951 11.4641 14.5496 11.4641C12.7732 11.4641 12.5011 12.8502 12.5011 14.2826V19.7143H9.09219V8.73795H12.3625V10.2371H12.4087C12.8656 9.37455 13.9797 8.46585 15.6379 8.46585C19.0879 8.46585 19.7297 10.7402 19.7297 13.6973V19.7143Z" fill="#53B2BE"/>
                                                </svg>
                                            </a>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            ?>

                            <div class=" flex-grow-1">

                                <div class="d-flex flex-wrap align-items-center flex-xl-nowrap pb-3 mb-lg-3 border-bottom" style="gap: 12px;">
                                    <h6 class="name flex-grow-1">
                                        <span class="first-name"><?php echo ucfirst($profile_user_data['fname']); ?></span>
                                        <a href="#" class="btn std-btn py-1"><?php echo !empty($profile_user_data['type']) ? ucfirst($profile_user_data['type']) : 'Professional'; ?></a>
                                        <!--<br>
                                        <?#php
                                        #if (($is_my_profile_view || $my_profile_stage >= 2) && !empty($profile_user_data['company'])) {
                                            #echo '<span class="last-name">';
                                            // $company = get_explode_names($profile_user_data['company']);
                                            #foreach ($profile_user_data['company'] as $ckey => $companyDet) {
                                                #echo $companyDet['value'] ?? '';
                                            #}
                                            #echo '</span>';
                                        #}
                                        ?>-->
                                    </h6>

                                    <div class="d-flex flex-wrap align-items-center" style="gap: 12px;">
                                        <?php if( $user_is_logged_in && (isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken) && taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken != $ptoken)) { ?>
                                            <!-- <a class="btn std-btn py-1 profile_send_email_btn" data-toptoken="<?= $ptoken ?? ''; ?>">Message</a> -->
                                        <?php } ?>

                                        <!-- <a href="<?php // echo TAOH_SITE_URL_ROOT.'/resume-download/'.$ptoken; ?>" target="_blank" class="btn nrm-btn">
                                        <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12.9375 1.4375C12.9375 0.642383 12.2951 0 11.5 0C10.7049 0 10.0625 0.642383 10.0625 1.4375V12.34L6.76523 9.04277C6.20371 8.48125 5.2918 8.48125 4.73027 9.04277C4.16875 9.6043 4.16875 10.5162 4.73027 11.0777L10.4803 16.8277C11.0418 17.3893 11.9537 17.3893 12.5152 16.8277L18.2652 11.0777C18.8268 10.5162 18.8268 9.6043 18.2652 9.04277C17.7037 8.48125 16.7918 8.48125 16.2303 9.04277L12.9375 12.34V1.4375ZM2.875 15.8125C1.28926 15.8125 0 17.1018 0 18.6875V20.125C0 21.7107 1.28926 23 2.875 23H20.125C21.7107 23 23 21.7107 23 20.125V18.6875C23 17.1018 21.7107 15.8125 20.125 15.8125H15.5654L13.5305 17.8475C12.4074 18.9705 10.5881 18.9705 9.46504 17.8475L7.43457 15.8125H2.875ZM19.4062 18.3281C19.6922 18.3281 19.9664 18.4417 20.1686 18.6439C20.3708 18.8461 20.4844 19.1203 20.4844 19.4062C20.4844 19.6922 20.3708 19.9664 20.1686 20.1686C19.9664 20.3708 19.6922 20.4844 19.4062 20.4844C19.1203 20.4844 18.8461 20.3708 18.6439 20.1686C18.4417 19.9664 18.3281 19.6922 18.3281 19.4062C18.3281 19.1203 18.4417 18.8461 18.6439 18.6439C18.8461 18.4417 19.1203 18.3281 19.4062 18.3281Z" fill="black"/>
                                        </svg>
                                        <span>Download Resume</span>
                                        </a> -->

                                        <?php
                                        if($is_my_profile_view && !empty($my_profile_tag_category)) {
                                            ?>
                                            <div>
                                                <h6 class="rec-peo-title">Recommended People to Network With</h6>
                                                <div>
                                                    <select name="directory_flag" id="directory_flag" class="select2 form-control">
                                                        <option value=""></option>
                                                        <?php
                                                        if (!empty($directory_flags_to_show)) {
                                                            $flags_to_show = [];
                                                            foreach ($my_profile_tag_category as $tag) {
                                                                $kebab_selected_flag = str_replace(' ', '-', strtolower($tag));
                                                                if (array_key_exists($kebab_selected_flag, $directory_flags_to_show)) {
                                                                    $flags_to_show = array_merge($flags_to_show, $directory_flags_to_show[$kebab_selected_flag]);
                                                                }
                                                            }

                                                            if (!empty($flags_to_show)) {
                                                                $flags_to_show = array_values(array_unique($flags_to_show));

                                                                foreach ($flags_to_show as $flag) {
                                                                    $kebab_flag = str_replace(' ', '-', strtolower($flag));
                                                                    echo '<option value="' . $kebab_flag . '">' . htmlspecialchars($flag) . '</option>';
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>

                            </div>

                                </div>

                                <div class="pb-3 pt-3 pt-xl-0 profile_stage_valid">
                                    <?php
                                    if (($is_my_profile_view || $my_profile_stage >= 2) && !empty($titles)) {
                                        ?>
                                        <p class="p-info" style="gap: 12px;">
                                            <span class="svg-con">
                                                <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M6.10938 1.6H10.8906C11.0367 1.6 11.1562 1.72 11.1562 1.86667V3.2H5.84375V1.86667C5.84375 1.72 5.96328 1.6 6.10938 1.6ZM4.25 1.86667V3.2H2.125C0.95293 3.2 0 4.15667 0 5.33333V8.53333H6.375H10.625H17V5.33333C17 4.15667 16.0471 3.2 14.875 3.2H12.75V1.86667C12.75 0.836667 11.9166 0 10.8906 0H6.10938C5.0834 0 4.25 0.836667 4.25 1.86667ZM17 9.6H10.625V10.6667C10.625 11.2567 10.1502 11.7333 9.5625 11.7333H7.4375C6.8498 11.7333 6.375 11.2567 6.375 10.6667V9.6H0V13.8667C0 15.0433 0.95293 16 2.125 16H14.875C16.0471 16 17 15.0433 17 13.8667V9.6Z" fill="#40A6B4"/>
                                                </svg>
                                            </span>
                                            <span>
                                                <?php foreach ($titles as $t) echo $t['value']; ?>
                                                <?php
                                                    if (($is_my_profile_view || $my_profile_stage >= 2) && !empty($profile_user_data['company'])) {

                                                        foreach ($profile_user_data['company'] as $ckey => $companyDet) {
                                                            // echo $companyDet['value'] ?? '';
                                                            if (!empty($companyDet['value'])) {
                                                                echo 'at ' . $companyDet['value'];
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </span>

                                        </p>
                                        <?php
                                    }

                                    if (($is_my_profile_view || $my_profile_stage >= 1) && !empty($full_location)) {
                                        ?>
                                        <div class="d-flex flex-wrap mt-3" style="gap: 12px;">
                                            <p class="p-info mr-lg-3" style="gap: 12px;">
                                                <span class="svg-con">
                                                    <svg width="14" height="19" viewBox="0 0 14 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M7.86406 18.5731C9.73438 16.1845 14 10.3953 14 7.14349C14 3.19969 10.8646 0 7 0C3.13542 0 0 3.19969 0 7.14349C0 10.3953 4.26562 16.1845 6.13594 18.5731C6.58437 19.1423 7.41562 19.1423 7.86406 18.5731ZM7 4.76232C7.61884 4.76232 8.21233 5.0132 8.64992 5.45975C9.0875 5.9063 9.33333 6.51196 9.33333 7.14349C9.33333 7.77501 9.0875 8.38067 8.64992 8.82722C8.21233 9.27378 7.61884 9.52465 7 9.52465C6.38116 9.52465 5.78767 9.27378 5.35008 8.82722C4.9125 8.38067 4.66667 7.77501 4.66667 7.14349C4.66667 6.51196 4.9125 5.9063 5.35008 5.45975C5.78767 5.0132 6.38116 4.76232 7 4.76232Z" fill="#008799" fill-opacity="0.61"/>
                                                    </svg>
                                                </span>
                                                <span><?php echo $profile_user_data['full_location'];?></span>
                                            </p>

                                            <p class="p-info mr-lg-3" style="gap: 12px;">
                                                <span class="svg-con">
                                                    <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M2.4 3.25C2.4 2.38805 2.73714 1.5614 3.33726 0.951903C3.93737 0.34241 4.75131 0 5.6 0C6.44869 0 7.26263 0.34241 7.86274 0.951903C8.46286 1.5614 8.8 2.38805 8.8 3.25C8.8 4.11195 8.46286 4.9386 7.86274 5.5481C7.26263 6.15759 6.44869 6.5 5.6 6.5C4.75131 6.5 3.93737 6.15759 3.33726 5.5481C2.73714 4.9386 2.4 4.11195 2.4 3.25ZM0 12.2459C0 9.74492 1.995 7.71875 4.4575 7.71875H6.7425C9.205 7.71875 11.2 9.74492 11.2 12.2459C11.2 12.6623 10.8675 13 10.4575 13H0.7425C0.3325 13 0 12.6623 0 12.2459ZM12.6 7.92188V6.29688H11C10.6675 6.29688 10.4 6.0252 10.4 5.6875C10.4 5.3498 10.6675 5.07812 11 5.07812H12.6V3.45312C12.6 3.11543 12.8675 2.84375 13.2 2.84375C13.5325 2.84375 13.8 3.11543 13.8 3.45312V5.07812H15.4C15.7325 5.07812 16 5.3498 16 5.6875C16 6.0252 15.7325 6.29688 15.4 6.29688H13.8V7.92188C13.8 8.25957 13.5325 8.53125 13.2 8.53125C12.8675 8.53125 12.6 8.25957 12.6 7.92188Z" fill="#40A6B4"/>
                                                    </svg>
                                                </span>

                                                <span>
                                                    <span class="mr-2 followers-count-view <?= $is_my_profile_view ? 'profile_followers_btn' : '' ?>" data-ptoken="<?= $ptoken ?? ''; ?>" data-fscount="<?= $followers_count ?? 0; ?>"><?= $followers_count ?? 0; ?> Followers</span>
                                                    <span class="mr-2 following-count-view <?= $is_my_profile_view ? 'profile_following_btn' : '' ?>" data-ptoken="<?= $ptoken ?? ''; ?>" data-fgcount="<?= $following_count ?? 0; ?>"><?= $following_count ?? 0; ?> Following</span>
                                                </span>
                                            </p>
                                        </div>
                                        <?php
                                    }

                                    ?>
                                </div>


                                <!--<div class="pb-3 d-flex align-items-center mutual-con" style="gap: 9px;">
                                    <div class="mutual-pic">
                                        <img src="<?php /*echo TAOH_SITE_URL_ROOT.'/assets/images/profile_room_1.png';*/?>" alt="">
                                        <img src="<?php /*echo TAOH_SITE_URL_ROOT.'/assets/images/profile_room_2.png';*/?>" alt="">
                                    </div>
                                    <p>John Jacob, Maria and 3 other mutual connections</p>
                                </div>-->


                                <div>
                                    <?php
                                    if (!$is_my_profile_view) {
                                        echo '<button type="button" class="btn std-btn mr-2 profile_send_message_btn" data-toptoken="'.($ptoken ?? '').'" data-respondptoken="'.($my_ptoken ?? '').'">Message</button>';
                                        echo '<button type="button" class="btn bor-btn px-3 profile_follow_btn" data-ptoken="'.($ptoken ?? '').'" data-follow_status="'.($follow_status ?? 0).'"  data-page="profile"><i class="fas fa-user-plus follow-user-plus-icon" aria-hidden="true"></i></button>';
                                    }
                                    ?>
                                </div>

                            </div>
                        </div>

                        <div id="profile_stage_invalid" class="col-12 mt-4 text-center" style='display:none'>
                            <a class="btn s-btn" target="_blank" href="<?php echo TAOH_SITE_URL_ROOT; ?>/settings">Complete your profile to see more information</a>
                        </div>
                        <?php
                        if ($is_my_profile_view || $my_profile_stage >= 2) {
                            ?>
                            <!-- about me -->
                            <div class="mt-4 profile_stage_valid">
                                <?php if(trim($about_me) != '' || !empty($get_skill)){ ?>
                                    <h6 class="sec-title pb-2 b-2-btm pl-lg-4">
                                <span class="svg-con">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 9C9.19347 9 10.3381 8.52589 11.182 7.68198C12.0259 6.83807 12.5 5.69347 12.5 4.5C12.5 3.30653 12.0259 2.16193 11.182 1.31802C10.3381 0.474106 9.19347 0 8 0C6.80653 0 5.66193 0.474106 4.81802 1.31802C3.97411 2.16193 3.5 3.30653 3.5 4.5C3.5 5.69347 3.97411 6.83807 4.81802 7.68198C5.66193 8.52589 6.80653 9 8 9ZM5.04063 10C2.25625 10 0 12.2562 0 15.0406C0 15.5719 0.43125 16 0.959375 16H15.0406C15.5719 16 16 15.5687 16 15.0406C16 12.2562 13.7438 10 10.9594 10H5.04063Z" fill="#40A6B4"/>
                                    </svg>
                                </span>
                                        <span>About me</span>
                                    </h6>
                                <?php } ?>
                                <div class="sec-content-con">
                                    <div class="sec-content">
                                        <?php
                                        if (trim($about_me) != '') {
                                            echo '<p class="content-text pt-2">' . taoh_title_desc_decode($about_me) . '</p>';
                                        }

                                        if($get_skill != ''){ ?>
                                            <div class="d-flex skill-con align-items-center flex-wrap py-4" style="gap: 12px;">
                                                <p class="info-title mr-2">Skills:</p>
                                                <?php
                                                foreach ($get_skill as $s_key => $skill) {
                                                    if (!empty($skill['value'])) {
                                                        echo '<span class="btn skill-list skill_directory" data-skillid="' . $s_key . '" data-skillslug="' . $skill['slug'] . '">' . htmlspecialchars($skill['value']) . '</span>';
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>

                            </div>

                            <!-- Interests -->
                            <?php
                        if (!empty($profile_tag_category)) {
                            ?>
                                <div class="mt-3 profile_stage_valid">
                                    <h6 class="sec-title pb-2 b-2-btm pl-lg-4 justify-content-between">
                                        <div style="display: flex; gap: 12px;">
                                            <span class="svg-con">
                                                <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8 0C9.40558 4.84301 11.1833 6.63041 16 8.04366C11.1833 9.45692 9.40558 11.2443 8 16.0873C6.59442 11.2451 4.81673 9.45692 0 8.04366C4.81673 6.63041 6.59442 4.84379 8 0Z" fill="#40A6B4"/>
                                                </svg>
                                            </span>
                                            <span>Interests</span>
                                        </div>
                                    </h6>

                                    <div class="sec-content-con">
                                        <div class="sec-content">
                                            <ul class="nav nav-tabs my-4 interests-tabs" id="myTab" role="tablist" style="gap: 12px;">
                                                <?php
                                                $isFirstTag = true;
                                                foreach ($profile_tag_category as $tag):
                                                    $tag_id = strtolower(str_replace(' ', '', $tag));

                                                    $classes = ['nav-link', 'interests-tab'];
                                                    if ($user_is_logged_in && !$is_my_profile_view && in_array($tag, $my_profile_tag_category)) {
                                                        $classes[] = 'common-border';
                                                    }
                                                    if ($isFirstTag) {
                                                        $classes[] = 'active';
                                                        $isFirstTag = false;
                                                    }
                                                    ?>
                                                    <li class="nav-item">
                                                        <a class="<?= implode(' ', $classes) ?>" data-toggle="tab" href="#<?= htmlspecialchars($tag_id) ?>" role="tab" aria-controls="<?= htmlspecialchars($tag_id) ?>" aria-selected="true">
                                                            <span class="px-3 py-1"><?= htmlspecialchars($tag) ?></span>
                                                        </a>
                                                    </li>
                                                    <?php
                                                endforeach;
                                                ?>
                                            </ul>

                                            <div class="tab-content px-0" id="myTabContent">
                                                <?php
                                                $isFirstTagTab = true;
                                                foreach ($profile_tag_category as $tag):
                                                    $tag_id = strtolower(str_replace(' ', '', $tag));
                                                    $pane_classes = $isFirstTagTab ? 'show active' : '';
                                                    $isFirstTagTab = false;
                                                    ?>
                                                    <div id="<?= htmlspecialchars($tag_id) ?>" class="tab-pane fade <?= $pane_classes ?>" aria-labelledby="<?= htmlspecialchars($tag_id) ?>">
                                                        <?php
                                                        if (!empty($profile_user_data) && !empty($tag_category_form[$tag])):
                                                            foreach ($tag_category_form[$tag] as $category_key => $category_form):
                                                                $category_key_id = strtolower(str_replace(' ', '_', $tag));
                                                                $category_form_val = safe_get($profile_user_data, $category_key_id, $category_form['field_name']);
                                                                ?>
                                                                <div role="tabpanel">
                                                                    <?php if (!empty($category_form['field_value']) && !empty($category_form_val)): ?>
                                                                        <p class="content-text pt-2"><?= htmlspecialchars($category_form['field_value']) ?></p>
                                                                        <p><?= htmlspecialchars($category_form_val) ?></p>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <?php
                                                            endforeach;
                                                        endif;
                                                        ?>
                                                    </div>
                                                    <?php
                                                endforeach;
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                        }

                        if($edu_list != '' && count($edu_list) > 0){ ?>
                            <!-- Educational Qualifications -->
                            <div class="mt-5 profile_stage_valid">
                                <h6 class="sec-title pb-2 b-2-btm pl-lg-4">
                                <span class="svg-con">
                                    <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7.83295 0.0158219C7.94366 -0.00527395 8.05793 -0.00527395 8.16864 0.0158219L15.3108 1.42221C15.7107 1.49956 16 1.84764 16 2.24846C16 2.64928 15.7107 2.99736 15.3108 3.07471L12.5718 3.61617V5.62379C12.5718 8.10958 10.5255 10.1242 8.00079 10.1242C5.47605 10.1242 3.42982 8.10958 3.42982 5.62379V3.61617L1.71571 3.27864V5.56754L2.27636 8.32405C2.3085 8.4893 2.26565 8.66159 2.15852 8.79168C2.05139 8.92177 1.88712 8.99912 1.71571 8.99912H0.572961C0.40155 8.99912 0.240852 8.92529 0.130148 8.79168C0.019445 8.65807 -0.0234078 8.4893 0.0123029 8.32405L0.572961 5.56754V3.04307C0.233709 2.92704 0.00158968 2.61061 0.00158968 2.24846C0.00158968 1.84764 0.290847 1.49956 0.690807 1.42221L7.83295 0.0158219ZM3.99762 11.5201C4.37258 11.4005 4.77611 11.5341 5.04752 11.8189L7.58298 14.4735C7.80796 14.7091 8.19006 14.7091 8.41504 14.4735L10.9505 11.8189C11.2219 11.5341 11.6254 11.4005 12.0004 11.5201C14.3216 12.2549 16 14.3926 16 16.9206C16 17.5183 15.5072 18 14.9037 18H1.09791C0.494398 18 0.00158968 17.5148 0.00158968 16.9206C0.00158968 14.3926 1.67999 12.2549 3.99762 11.5201Z" fill="#40A6B4"/>
                                    </svg>
                                </span>
                                    <span>Educational Qualifications</span>
                                </h6>

                                <?php
                                foreach($edu_list as $edu_keys => $edu_vals){
                                    $ed_name = $edu_vals['company'];
                                    foreach ( $ed_name as $ed_key => $ed_value ){
                                        if($ed_value != '' && !is_array($ed_value)){
                                            list ( $ed_pre, $ed_post ) = explode( ':>', $ed_value );
                                        }else{
                                            foreach ( $ed_value as $edu_key => $edu_value ){
                                                list ( $ed_pre, $ed_post ) = explode( ':>', $edu_value );
                                            }
                                        }
                                    }
                                    ?>

                                    <div class="sec-content-con">
                                        <div class="sec-content">
                                            <div class=" pt-3 pb-2 mb-3 d-flex flex-wrap align-items-center" style="gap: 12px;">
                                                <div class="d-flex align-items-center mr-lg-5" style="gap: 12px;">
                                                    <svg width="32" height="23" viewBox="0 0 32 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M16.0008 0C15.5958 0 15.1958 0.0699999 14.8158 0.205L0.79077 5.27C0.31577 5.445 0.000769613 5.895 0.000769613 6.4C0.000769613 6.905 0.31577 7.355 0.79077 7.53L3.68577 8.575C2.86577 9.865 2.40077 11.39 2.40077 12.995V14.4C2.40077 15.82 1.86077 17.285 1.28577 18.44C0.96077 19.09 0.59077 19.73 0.16077 20.32C0.000769615 20.535 -0.0442304 20.815 0.0457696 21.07C0.13577 21.325 0.34577 21.515 0.60577 21.58L3.80577 22.38C4.01577 22.435 4.24077 22.395 4.42577 22.28C4.61077 22.165 4.74077 21.975 4.78077 21.76C5.21077 19.62 4.99577 17.7 4.67577 16.325C4.51577 15.615 4.30077 14.89 4.00077 14.225V12.995C4.00077 11.485 4.51077 10.06 5.39577 8.92C6.04077 8.145 6.87577 7.52 7.85577 7.135L15.7058 4.05C16.1158 3.89 16.5808 4.09 16.7408 4.5C16.9008 4.91 16.7008 5.375 16.2908 5.535L8.44077 8.62C7.82077 8.865 7.27577 9.24 6.83077 9.7L14.8108 12.58C15.1908 12.715 15.5908 12.785 15.9958 12.785C16.4008 12.785 16.8008 12.715 17.1808 12.58L31.2108 7.53C31.6858 7.36 32.0008 6.905 32.0008 6.4C32.0008 5.895 31.6858 5.445 31.2108 5.27L17.1858 0.205C16.8058 0.0699999 16.4058 0 16.0008 0ZM6.40077 18.8C6.40077 20.565 10.7008 22.4 16.0008 22.4C21.3008 22.4 25.6008 20.565 25.6008 18.8L24.8358 11.53L17.7258 14.1C17.1708 14.3 16.5858 14.4 16.0008 14.4C15.4158 14.4 14.8258 14.3 14.2758 14.1L7.16577 11.53L6.40077 18.8Z" fill="url(#paint0_linear_6968_424)"/>
                                                        <defs>
                                                            <linearGradient id="paint0_linear_6968_424" x1="16.0004" y1="0" x2="16.0004" y2="22.4044" gradientUnits="userSpaceOnUse">
                                                                <stop stop-color="#176FB3"/>
                                                                <stop offset="1" stop-color="#1377B7"/>
                                                            </linearGradient>
                                                        </defs>
                                                    </svg>
                                                    <div>
                                                        <h6 class="text-sm-b mb-1"><?php echo taoh_title_desc_decode($edu_vals['edu_specalize']); ?></h6>
                                                        <p class="text-xs"><?php echo get_month_from_number($edu_vals['edu_start_month']).' '.$edu_vals['edu_start_year'].' to '.get_month_from_number($edu_vals['edu_end_month']).' '.$edu_vals['edu_complete_year']; ?></p>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center" style="gap: 12px;">
                                                    <svg width="32" height="28" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M15.2153 0.159391L1.21699 6.15998C0.34209 6.53501 -0.145352 7.4726 0.0483748 8.39769C0.242102 9.32278 1.05451 9.99785 2.0044 9.99785V10.4979C2.0044 11.3292 2.67307 11.998 3.50422 11.998H28.5013C29.3324 11.998 30.0011 11.3292 30.0011 10.4979V9.99785C30.951 9.99785 31.7696 9.32903 31.9571 8.39769C32.1446 7.46635 31.6572 6.52876 30.7885 6.15998L16.7902 0.159391C16.2902 -0.0531302 15.7153 -0.0531302 15.2153 0.159391ZM8.00369 13.9982H4.00416V26.2682C3.96666 26.2869 3.92917 26.3119 3.89167 26.3369L0.892026 28.3371C0.160862 28.8247 -0.170349 29.7373 0.0858704 30.5811C0.34209 31.4249 1.12325 32 2.0044 32H30.0011C30.8823 32 31.6572 31.4249 31.9134 30.5811C32.1696 29.7373 31.8446 28.8247 31.1072 28.3371L28.1076 26.3369C28.0701 26.3119 28.0326 26.2932 27.9951 26.2682V13.9982H24.0018V25.9994H21.5021V13.9982H17.5026V25.9994H14.5029V13.9982H10.5034V25.9994H8.00369V13.9982ZM16.0028 3.99727C16.5331 3.99727 17.0418 4.208 17.4168 4.58311C17.7918 4.95822 18.0025 5.46698 18.0025 5.99746C18.0025 6.52795 17.7918 7.0367 17.4168 7.41181C17.0418 7.78692 16.5331 7.99766 16.0028 7.99766C15.4724 7.99766 14.9637 7.78692 14.5887 7.41181C14.2137 7.0367 14.003 6.52795 14.003 5.99746C14.003 5.46698 14.2137 4.95822 14.5887 4.58311C14.9637 4.208 15.4724 3.99727 16.0028 3.99727Z" fill="#1474B6"/>
                                                    </svg>
                                                    <div>
                                                        <h6 class="text-sm-b mb-1"><?php echo $ed_post; ?></h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if(!empty($edu_vals['edu_grade'])){ ?>
                                                <!-- grade -->
                                                <h6 class="text-sm-b mb-2">Grade: <span><?php echo $edu_vals['edu_grade']; ?></span></h6>
                                            <?php } ?>

                                            <?php if(!empty($edu_vals['skill'])){ ?>
                                                <!-- skills -->
                                                <div class="d-flex flex-wrap align-items-center mb-3" style="gap:12px;">
                                                    <h6 class="text-sm-b">Skills :</h6>
                                                    <?php
                                                    $d_skills = $edu_vals['skill'];
                                                    $d_items = '';
                                                    foreach ($d_skills as $d_keys => $d_vals){
                                                        if(!is_array($ed_value)){
                                                            list ( $skill_pre, $skill_name ) = explode(':>',$d_vals); ?>
                                                            <span class="skill-badge grey-light"><?php echo $skill_name; ?></span>
                                                        <?php }  else{
                                                            foreach ($d_vals as $ed_keys => $ed_vals){
                                                                list ( $skill_pre, $skill_name ) = explode(':>',$ed_vals); ?>
                                                                <span class="skill-badge grey-light"><?php echo $skill_name; ?></span>
                                                            <?php }
                                                        }
                                                    } ?>
                                                </div>
                                            <?php } ?>
                                            <!-- Activities -->
                                            <?php if(trim(taoh_title_desc_decode($edu_vals['edu_activities'])) != ''){ ?>
                                                <h6 class="text-sm-b mb-3">Activities : <span><?php echo taoh_title_desc_decode($edu_vals['edu_activities']); ?></span></h6>
                                            <?php } ?>
                                            <!-- Description -->
                                            <?php if(trim(taoh_title_desc_decode($edu_vals['edu_description'])) != ''){ ?>
                                                <div>
                                                    <h6 class="text-sm-b mb-2">Description :</h6>
                                                    <p class="content-text-xs"><?php echo taoh_title_desc_decode($edu_vals['edu_description']); ?></p>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <!-- 1 -->
                                <!-- <div class="sec-content-con pb-3">
                                    <div class="sec-content b-2-btm pb-4">

                                        <div class="b-2-btm pt-3 pb-2 mb-3 d-flex align-items-center" style="gap: 12px;">
                                            <div class="d-flex align-items-center mr-lg-5" style="gap: 12px;">
                                                <svg width="32" height="23" viewBox="0 0 32 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M16.0008 0C15.5958 0 15.1958 0.0699999 14.8158 0.205L0.79077 5.27C0.31577 5.445 0.000769613 5.895 0.000769613 6.4C0.000769613 6.905 0.31577 7.355 0.79077 7.53L3.68577 8.575C2.86577 9.865 2.40077 11.39 2.40077 12.995V14.4C2.40077 15.82 1.86077 17.285 1.28577 18.44C0.96077 19.09 0.59077 19.73 0.16077 20.32C0.000769615 20.535 -0.0442304 20.815 0.0457696 21.07C0.13577 21.325 0.34577 21.515 0.60577 21.58L3.80577 22.38C4.01577 22.435 4.24077 22.395 4.42577 22.28C4.61077 22.165 4.74077 21.975 4.78077 21.76C5.21077 19.62 4.99577 17.7 4.67577 16.325C4.51577 15.615 4.30077 14.89 4.00077 14.225V12.995C4.00077 11.485 4.51077 10.06 5.39577 8.92C6.04077 8.145 6.87577 7.52 7.85577 7.135L15.7058 4.05C16.1158 3.89 16.5808 4.09 16.7408 4.5C16.9008 4.91 16.7008 5.375 16.2908 5.535L8.44077 8.62C7.82077 8.865 7.27577 9.24 6.83077 9.7L14.8108 12.58C15.1908 12.715 15.5908 12.785 15.9958 12.785C16.4008 12.785 16.8008 12.715 17.1808 12.58L31.2108 7.53C31.6858 7.36 32.0008 6.905 32.0008 6.4C32.0008 5.895 31.6858 5.445 31.2108 5.27L17.1858 0.205C16.8058 0.0699999 16.4058 0 16.0008 0ZM6.40077 18.8C6.40077 20.565 10.7008 22.4 16.0008 22.4C21.3008 22.4 25.6008 20.565 25.6008 18.8L24.8358 11.53L17.7258 14.1C17.1708 14.3 16.5858 14.4 16.0008 14.4C15.4158 14.4 14.8258 14.3 14.2758 14.1L7.16577 11.53L6.40077 18.8Z" fill="url(#paint0_linear_6968_424)"/>
                                                    <defs>
                                                    <linearGradient id="paint0_linear_6968_424" x1="16.0004" y1="0" x2="16.0004" y2="22.4044" gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#176FB3"/>
                                                    <stop offset="1" stop-color="#1377B7"/>
                                                    </linearGradient>
                                                    </defs>
                                                </svg>
                                                <div>
                                                    <h6 class="text-sm-b mb-1">MBA in Marketing Management</h6>
                                                    <p class="text-xs">May 2020 to June 2022</p>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center" style="gap: 12px;">
                                                <svg width="32" height="28" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M15.2153 0.159391L1.21699 6.15998C0.34209 6.53501 -0.145352 7.4726 0.0483748 8.39769C0.242102 9.32278 1.05451 9.99785 2.0044 9.99785V10.4979C2.0044 11.3292 2.67307 11.998 3.50422 11.998H28.5013C29.3324 11.998 30.0011 11.3292 30.0011 10.4979V9.99785C30.951 9.99785 31.7696 9.32903 31.9571 8.39769C32.1446 7.46635 31.6572 6.52876 30.7885 6.15998L16.7902 0.159391C16.2902 -0.0531302 15.7153 -0.0531302 15.2153 0.159391ZM8.00369 13.9982H4.00416V26.2682C3.96666 26.2869 3.92917 26.3119 3.89167 26.3369L0.892026 28.3371C0.160862 28.8247 -0.170349 29.7373 0.0858704 30.5811C0.34209 31.4249 1.12325 32 2.0044 32H30.0011C30.8823 32 31.6572 31.4249 31.9134 30.5811C32.1696 29.7373 31.8446 28.8247 31.1072 28.3371L28.1076 26.3369C28.0701 26.3119 28.0326 26.2932 27.9951 26.2682V13.9982H24.0018V25.9994H21.5021V13.9982H17.5026V25.9994H14.5029V13.9982H10.5034V25.9994H8.00369V13.9982ZM16.0028 3.99727C16.5331 3.99727 17.0418 4.208 17.4168 4.58311C17.7918 4.95822 18.0025 5.46698 18.0025 5.99746C18.0025 6.52795 17.7918 7.0367 17.4168 7.41181C17.0418 7.78692 16.5331 7.99766 16.0028 7.99766C15.4724 7.99766 14.9637 7.78692 14.5887 7.41181C14.2137 7.0367 14.003 6.52795 14.003 5.99746C14.003 5.46698 14.2137 4.95822 14.5887 4.58311C14.9637 4.208 15.4724 3.99727 16.0028 3.99727Z" fill="#1474B6"/>
                                                </svg>
                                                <div>
                                                    <h6 class="text-sm-b mb-1">Boston University</h6>
                                                    <p class="text-xs">Boston, MA</p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-#- grade -#->
                                        <h6 class="text-sm-b mb-2">Grade: <span>A+</span></h6>
                                        <!-#- skills -#->
                                        <div class="d-flex align-items-center mb-3" style="gap:12px;">
                                            <h6 class="text-sm-b">Skills :</h6>
                                            <span class="skill-badge grey-light">Skill 1</span>
                                            <span class="skill-badge grey-light">Skill 2</span>
                                            <span class="skill-badge grey-light">Skill 3</span>
                                            <span class="skill-badge grey-light">Skill 4</span>
                                        </div>
                                        <!-#- Activities -#->
                                        <h6 class="text-sm-b mb-3">Activities : <span>Society of Management Professionals</span></h6>
                                        <!-#- Description -#->
                                        <div>
                                            <h6 class="text-sm-b mb-2">Description :</h6>
                                            <p class="content-text-xs">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book <a class="read-more" style="font-weight: 400;">Read more...</a></p>
                                        </div>
                                    </div>
                                </div> -->
                                <?php } ?>

                            <?php
                            if($emp_list != '' && count($emp_list) > 0){ ?>
                                <!-- Experience Details -->
                                <div class="mt-5 profile_stage_valid">
                                    <h6 class="sec-title pb-2 b-2-btm pl-lg-4">
                                        <span class="svg-con">
                                            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0 0V15H15V0H0ZM10.5971 10.8884L7.5 13.8583L4.4029 10.8884L6.5625 4.72768L4.4029 1.82812H10.5937L8.4375 4.72768L10.5971 10.8884Z" fill="#40A6B4"/>
                                            </svg>
                                        </span>
                                        <span>Experience Details</span>
                                    </h6>

                                    <?php
                                    foreach($emp_list as $emp_keys => $emp_vals){
                                        // echo "<pre>"; print_r($emp_vals); echo "</br>";
                                        $em_title = (isset($emp_vals['emp_title']))?$emp_vals['emp_title'] : $emp_vals['title'];
                                        foreach ( $em_title as $em_key => $em_value ){
                                            if(!is_array($em_value)){
                                                list ( $em_pre, $em_post ) = explode( ':>', $em_value );
                                            }else{
                                                foreach ( $em_value as $emp_key => $emp_value ){
                                                    list ( $em_pre, $em_post ) = explode( ':>', $emp_value );
                                                }
                                            }
                                        }
                                        $em_company = (isset($emp_vals['emp_company']))?$emp_vals['emp_company'] : $emp_vals['company'];
                                        foreach ( $em_company as $em_cmp_key => $em_cmp_value ){
                                            if(!is_array($em_cmp_value)){
                                                list ( $em_cmp_pre, $em_cmp_post ) = explode( ':>', $em_cmp_value );
                                            }else{
                                                foreach ( $em_cmp_value as $emp_cmp_key => $emp_cmp_value ){
                                                    list ( $em_cmp_pre, $em_cmp_post ) = explode( ':>', $emp_cmp_value );
                                                }
                                            }
                                        }

                                        $get_present_not = (($emp_vals['current_role'] ?? '') == 'on')?' Present':get_month_from_number($emp_vals['emp_end_month']).' '.$emp_vals['emp_year_end'];
                                        $current_year = date('Y');   // Outputs: 2025 (Current Year)
                                        $current_month = date('n');  // Outputs: 3 (Current Month - Without leading zero)

                                        $end_month = !empty($emp_vals['emp_end_month']) ?$emp_vals['emp_end_month'] : $current_month;
                                        $end_year = !empty($emp_vals['emp_year_end']) ? $emp_vals['emp_year_end'] : $current_year;

                                        $emp_placeType = $emp_vals['emp_placeType'];
                                        if($emp_placeType == 'rem'){
                                            $emp_placeType = 'Remote';
                                        }else if($emp_placeType == 'ons'){
                                            $emp_placeType = 'Onsite';
                                        }else if($emp_placeType == 'hyb'){
                                            $emp_placeType = 'Hybrid';
                                        }else{
                                            $emp_placeType = '';
                                        }

                                        $roletype_arr = array(
                                            "remo" => "Remote Work",
                                            "full" => "Full Time",
                                            "part" => "Part Time",
                                            "temp" => "Temporary",
                                            "free" => "Freelance",
                                            "cont" => "Contract",
                                            "pdin" => "Paid Internship",
                                            "unin" => "Unpaid Internship",
                                            "voln" => "Volunteer",
                                        );
                                        $roletype = $emp_vals['emp_roletype'] ?? [];
                                        $role_items = '';
                                        foreach ($roletype as $key => $value){
                                            if(empty($value)) continue;
                                            $role_items = $roletype_arr[$value] ?? '';
                                        }

                                        $industry_arr = array(
                                            "agri" => "Agriculture & Forestry",
                                            "arts" => "Arts & Entertainment",
                                            "auto" => "Automotive",
                                            "aero" => "Aviation & Aerospace",
                                            "bank" => "Banking & Finance",
                                            "bio" => "Biotechnology",
                                            "che" => "Chemicals",
                                            "cons" => "Construction",
                                            "good" => "Consumer Goods & Services",
                                            "space" => "Defense & Space",
                                            "edu" => "Education",
                                            "ene" => "Energy & Utilities",
                                            "engi" => "Engineering",
                                            "envi" => "Environmental Services",
                                            "fash" => "Fashion & Apparel",
                                            "food" => "Food & Beverages",
                                            "govt" => "Government & Public Sector",
                                            "heal" => "Healthcare & Pharmaceuticals",
                                            "tour" => "Hospitality & Tourism",
                                            "tech" => "Information Technology",
                                            "ins" => "Insurance",
                                            "legal" => "Legal Services",
                                            "manu" => "Manufacturing",
                                            "mari" => "Maritime",
                                            "mark" => "Marketing & Advertising",
                                            "media" => "Media & Communications",
                                            "mini" => "Mining & Metals",
                                            "non" => "Non-Profit & NGO",
                                            "prof" => "Professional Services",
                                            "real" => "Real Estate",
                                            "ret" => "Retail",
                                            "sports" => "Sports & Recreation",
                                            "tele" => "Telecommunications",
                                            "logi" => "Transport & Logistics",
                                            "other" => "Others (for industries not listed above)"
                                        );
                                        ?>
                                        <div class="sec-content-con ">
                                            <div class="sec-content ">
                                                <div class=" pt-3 pb-2 mb-3 d-flex flex-wrap align-items-center" style="gap: 12px;">
                                                    <div class="d-flex align-items-center mr-lg-5" style="gap: 12px;">
                                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M10.7812 3H19.2188C19.4766 3 19.6875 3.225 19.6875 3.5V6H10.3125V3.5C10.3125 3.225 10.5234 3 10.7812 3ZM7.5 3.5V6H3.75C1.68164 6 0 7.79375 0 10V16H11.25H18.75H30V10C30 7.79375 28.3184 6 26.25 6H22.5V3.5C22.5 1.56875 21.0293 0 19.2188 0H10.7812C8.9707 0 7.5 1.56875 7.5 3.5ZM30 18H18.75V20C18.75 21.1063 17.9121 22 16.875 22H13.125C12.0879 22 11.25 21.1063 11.25 20V18H0V26C0 28.2062 1.68164 30 3.75 30H26.25C28.3184 30 30 28.2062 30 26V18Z" fill="#1573B5"/>
                                                        </svg>
                                                        <div>
                                                            <h6 class="text-sm-b mb-1"><?php echo $em_cmp_post; ?></h6>
                                                            <p class="text-xs"><?php echo $emp_vals['emp_full_location']; ?></p>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex align-items-center" style="gap: 12px;">
                                                        <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M0 0V28H28V0H0ZM19.7812 20.325L14 25.8687L8.21875 20.325L12.25 8.825L8.21875 3.4125H19.775L15.75 8.825L19.7812 20.325Z" fill="#1573B5"/>
                                                        </svg>
                                                        <div>
                                                            <h6 class="text-sm-b mb-1"><?php echo $em_post; ?></h6>
                                                            <p class="text-xs"><?php echo get_month_from_number($emp_vals['emp_start_month']).' '.$emp_vals['emp_year_start'].' to '.$get_present_not ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php if(trim($emp_placeType) != '' ){ ?>
                                                    <!-- Location -->
                                                    <h6 class="text-sm-b mb-2">Location: <span><?php echo $emp_placeType.', '.$role_items; ?></span></h6>
                                                <?php } ?>
                                                <?php if(trim($emp_vals['emp_industry']) != ''){ ?>
                                                    <!-- Industry -->
                                                    <h6 class="text-sm-b mb-3">Industry : <span><?php echo $industry_arr[$emp_vals['emp_industry']]; ?></span></h6>
                                                <?php } ?>

                                                <!-- skills -->
                                                <?php if (!empty($emp_vals['skill']) && is_array($emp_vals['skill'])){  ?>
                                                    <div class="d-flex flex-wrap align-items-center mb-3" style="gap:12px;">
                                                        <h6 class="text-sm-b">Skills :</h6>
                                                        <?php
                                                        $skills = $emp_vals['skill'];
                                                        $items = '';
                                                        foreach ($skills as $s_keys => $s_vals){
                                                            if(!is_array($s_vals)){
                                                                $items = explode(':>',$s_vals); ?>
                                                                <span class="skill-badge"><?php echo $items[1]; ?></span>
                                                            <?php } else{
                                                                foreach ($s_vals as $es_keys => $es_vals){
                                                                    $items = explode(':>',$es_vals); ?>
                                                                    <span class="skill-badge grey-light"><?php echo $items[1]; ?></span>
                                                                <?php }
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                <?php } ?>

                                                <!-- Responsibilities -->
                                                <?php if(trim(taoh_title_desc_decode($emp_vals['emp_responsibilities'])) != ''){ ?>
                                                    <div>
                                                        <h6 class="text-sm-b mb-2">Responsibilities</h6>
                                                        <p class="content-text-xs"><?php echo taoh_title_desc_decode($emp_vals['emp_responsibilities']); ?></p>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <?php
                                    } ?>

                                    <!-- Hobbies & Interests  ---- Club Information  --- -->
                                    <?php if(trim($fun_fact) != ''){ ?>
                                        <div class="profile_stage_valid">

                                            <div class="py-3" style="width: 100%;">
                                                <h6 class="sec-title pb-2 b-2-btm px-3">
                                    <span class="svg-con">
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.9309 15.473L14.8543 15.2668C14.6309 15.2539 14.4117 15.3141 14.2313 15.4473C14.0508 15.5805 13.9219 15.7652 13.8703 15.9844L13.1141 18.975C12.4395 19.1555 11.7305 19.25 11 19.25C10.2695 19.25 9.56055 19.1555 8.88594 18.975L8.12969 15.9844C8.07383 15.7695 7.94492 15.5805 7.76875 15.4473C7.59258 15.3141 7.36914 15.2539 7.1457 15.2668L4.06914 15.473C3.31289 14.3043 2.84453 12.9293 2.76289 11.4512L5.37109 9.80977C5.56016 9.68945 5.69766 9.50898 5.76641 9.29844C5.83516 9.08789 5.82656 8.86016 5.74492 8.65391L4.59766 5.79219C5.5 4.69219 6.67305 3.82422 8.02227 3.3043L10.3941 5.28086C10.566 5.42266 10.7809 5.5 11 5.5C11.2191 5.5 11.4383 5.42266 11.6059 5.28086L13.9777 3.3043C15.3227 3.82422 16.5 4.69219 17.398 5.79219L16.2508 8.65391C16.1691 8.86016 16.1605 9.08789 16.2293 9.29844C16.298 9.50898 16.4398 9.68945 16.6246 9.80977L19.2328 11.4512C19.1512 12.9293 18.6828 14.3043 17.9266 15.473H17.9309ZM11 22C13.9174 22 16.7153 20.8411 18.7782 18.7782C20.8411 16.7153 22 13.9174 22 11C22 8.08262 20.8411 5.28473 18.7782 3.22183C16.7153 1.15893 13.9174 0 11 0C8.08262 0 5.28473 1.15893 3.22183 3.22183C1.15893 5.28473 0 8.08262 0 11C0 13.9174 1.15893 16.7153 3.22183 18.7782C5.28473 20.8411 8.08262 22 11 22ZM11.6059 8.00508C11.2449 7.74297 10.7551 7.74297 10.3941 8.00508L8.33594 9.49609C7.975 9.7582 7.82461 10.2223 7.96211 10.6477L8.74844 13.0668C8.88594 13.4922 9.28125 13.7801 9.72812 13.7801H12.2719C12.7188 13.7801 13.1141 13.4922 13.2516 13.0668L14.0379 10.6477C14.1754 10.2223 14.025 9.7582 13.6641 9.49609L11.6059 8.00078V8.00508Z" fill="#40A6B4"/>
                                        </svg>
                                    </span>
                                                    <span>Fun Fact</span>
                                                </h6>

                                                <div class="sec-content-con">
                                                    <div class="d-flex flex-wrap pt-3 px-2 sec-content" style="gap: 6px;">
                                                        <span class="hobby-list" style="background-color: transparent;"><?php echo taoh_title_desc_decode(trim($fun_fact)); ?></span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    <?php } ?>
                                    <!-- Hobbies & Interests  ---- Club Information  --- -->
                                    <div class="profile_stage_valid">

                                        <?php if($hobbies != '' && is_array($hobbies) && count($hobbies) > 0){ ?>
                                            <div class="py-3" style="width: 100%;">
                                                <h6 class="sec-title pb-2 b-2-btm px-3">
                                            <span class="svg-con">
                                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M17.9309 15.473L14.8543 15.2668C14.6309 15.2539 14.4117 15.3141 14.2313 15.4473C14.0508 15.5805 13.9219 15.7652 13.8703 15.9844L13.1141 18.975C12.4395 19.1555 11.7305 19.25 11 19.25C10.2695 19.25 9.56055 19.1555 8.88594 18.975L8.12969 15.9844C8.07383 15.7695 7.94492 15.5805 7.76875 15.4473C7.59258 15.3141 7.36914 15.2539 7.1457 15.2668L4.06914 15.473C3.31289 14.3043 2.84453 12.9293 2.76289 11.4512L5.37109 9.80977C5.56016 9.68945 5.69766 9.50898 5.76641 9.29844C5.83516 9.08789 5.82656 8.86016 5.74492 8.65391L4.59766 5.79219C5.5 4.69219 6.67305 3.82422 8.02227 3.3043L10.3941 5.28086C10.566 5.42266 10.7809 5.5 11 5.5C11.2191 5.5 11.4383 5.42266 11.6059 5.28086L13.9777 3.3043C15.3227 3.82422 16.5 4.69219 17.398 5.79219L16.2508 8.65391C16.1691 8.86016 16.1605 9.08789 16.2293 9.29844C16.298 9.50898 16.4398 9.68945 16.6246 9.80977L19.2328 11.4512C19.1512 12.9293 18.6828 14.3043 17.9266 15.473H17.9309ZM11 22C13.9174 22 16.7153 20.8411 18.7782 18.7782C20.8411 16.7153 22 13.9174 22 11C22 8.08262 20.8411 5.28473 18.7782 3.22183C16.7153 1.15893 13.9174 0 11 0C8.08262 0 5.28473 1.15893 3.22183 3.22183C1.15893 5.28473 0 8.08262 0 11C0 13.9174 1.15893 16.7153 3.22183 18.7782C5.28473 20.8411 8.08262 22 11 22ZM11.6059 8.00508C11.2449 7.74297 10.7551 7.74297 10.3941 8.00508L8.33594 9.49609C7.975 9.7582 7.82461 10.2223 7.96211 10.6477L8.74844 13.0668C8.88594 13.4922 9.28125 13.7801 9.72812 13.7801H12.2719C12.7188 13.7801 13.1141 13.4922 13.2516 13.0668L14.0379 10.6477C14.1754 10.2223 14.025 9.7582 13.6641 9.49609L11.6059 8.00078V8.00508Z" fill="#40A6B4"/>
                                                </svg>
                                            </span>
                                                    <span>Hobbies & Interests</span>
                                                </h6>
                                                <div class="sec-content-con">
                                                    <div class="d-flex flex-wrap pt-3 px-2 sec-content" style="gap: 6px;">
                                                        <?php
                                                        foreach ($hobbies as $f_keys => $hobby) {
                                                            echo '<span class="btn hobby-list profile_hobby_directory" data-hobbyslug="'.$hobby.'">' . PROFESSIONAL_HOBBIES[$hobby] . '</span>';
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>

                                    </div>

                                    <!-- Hobbies & Interests  ---- Club Information  --- -->
                                    <div class="profile_stage_valid">

                                        <?php if(trim($fun_fact) != '' && is_array($fun_fact)){ ?>
                                            <div class="card-list py-3" style="width: 100%; ">
                                                <h6 class="sec-title pb-2 b-2-btm px-3">
                                            <span class="svg-con">
                                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M17.9309 15.473L14.8543 15.2668C14.6309 15.2539 14.4117 15.3141 14.2313 15.4473C14.0508 15.5805 13.9219 15.7652 13.8703 15.9844L13.1141 18.975C12.4395 19.1555 11.7305 19.25 11 19.25C10.2695 19.25 9.56055 19.1555 8.88594 18.975L8.12969 15.9844C8.07383 15.7695 7.94492 15.5805 7.76875 15.4473C7.59258 15.3141 7.36914 15.2539 7.1457 15.2668L4.06914 15.473C3.31289 14.3043 2.84453 12.9293 2.76289 11.4512L5.37109 9.80977C5.56016 9.68945 5.69766 9.50898 5.76641 9.29844C5.83516 9.08789 5.82656 8.86016 5.74492 8.65391L4.59766 5.79219C5.5 4.69219 6.67305 3.82422 8.02227 3.3043L10.3941 5.28086C10.566 5.42266 10.7809 5.5 11 5.5C11.2191 5.5 11.4383 5.42266 11.6059 5.28086L13.9777 3.3043C15.3227 3.82422 16.5 4.69219 17.398 5.79219L16.2508 8.65391C16.1691 8.86016 16.1605 9.08789 16.2293 9.29844C16.298 9.50898 16.4398 9.68945 16.6246 9.80977L19.2328 11.4512C19.1512 12.9293 18.6828 14.3043 17.9266 15.473H17.9309ZM11 22C13.9174 22 16.7153 20.8411 18.7782 18.7782C20.8411 16.7153 22 13.9174 22 11C22 8.08262 20.8411 5.28473 18.7782 3.22183C16.7153 1.15893 13.9174 0 11 0C8.08262 0 5.28473 1.15893 3.22183 3.22183C1.15893 5.28473 0 8.08262 0 11C0 13.9174 1.15893 16.7153 3.22183 18.7782C5.28473 20.8411 8.08262 22 11 22ZM11.6059 8.00508C11.2449 7.74297 10.7551 7.74297 10.3941 8.00508L8.33594 9.49609C7.975 9.7582 7.82461 10.2223 7.96211 10.6477L8.74844 13.0668C8.88594 13.4922 9.28125 13.7801 9.72812 13.7801H12.2719C12.7188 13.7801 13.1141 13.4922 13.2516 13.0668L14.0379 10.6477C14.1754 10.2223 14.025 9.7582 13.6641 9.49609L11.6059 8.00078V8.00508Z" fill="#40A6B4"/>
                                                </svg>
                                            </span>
                                                    <span>Hobbies & Interests</span>
                                                </h6>

                                                <div class="d-flex flex-wrap pt-3 px-2" style="gap: 6px;">
                                                    <?php // $fun_factArr = explode("##$##",$fun_fact);
                                                    foreach ($fun_fact as $f_keys => $f_vals){ ?>
                                                        <span class="hobby-list"><?php echo PROFESSIONAL_HOBBIES[$f_vals] ?? ''; ?></span>
                                                    <?php } ?>
                                                    <!-- <span class="hobby-list">Hobby 1</span>
                                                    <span class="hobby-list">Hobby 2</span>
                                                    <span class="hobby-list">Hobby 3</span> -->
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <!--  -->
                                        <?php if($data_keywords != '' && count($data_keywords) > 0){ ?>
                                            <div class="py-3" style="flex: 1;">
                                                <h6 class="sec-title pb-2 b-2-btm px-3">
                                            <span class="svg-con">
                                                <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4.5 0C5.16304 0 5.79893 0.263392 6.26777 0.732233C6.73661 1.20107 7 1.83696 7 2.5C7 3.16304 6.73661 3.79893 6.26777 4.26777C5.79893 4.73661 5.16304 5 4.5 5C3.83696 5 3.20107 4.73661 2.73223 4.26777C2.26339 3.79893 2 3.16304 2 2.5C2 1.83696 2.26339 1.20107 2.73223 0.732233C3.20107 0.263392 3.83696 0 4.5 0ZM16 0C16.663 0 17.2989 0.263392 17.7678 0.732233C18.2366 1.20107 18.5 1.83696 18.5 2.5C18.5 3.16304 18.2366 3.79893 17.7678 4.26777C17.2989 4.73661 16.663 5 16 5C15.337 5 14.7011 4.73661 14.2322 4.26777C13.7634 3.79893 13.5 3.16304 13.5 2.5C13.5 1.83696 13.7634 1.20107 14.2322 0.732233C14.7011 0.263392 15.337 0 16 0ZM0 9.33438C0 7.49375 1.49375 6 3.33437 6H4.66875C5.16562 6 5.6375 6.10938 6.0625 6.30312C6.02187 6.52812 6.00313 6.7625 6.00313 7C6.00313 8.19375 6.52812 9.26562 7.35625 10C7.35 10 7.34375 10 7.33437 10H0.665625C0.3 10 0 9.7 0 9.33438ZM12.6656 10C12.6594 10 12.6531 10 12.6438 10C13.475 9.26562 13.9969 8.19375 13.9969 7C13.9969 6.7625 13.975 6.53125 13.9375 6.30312C14.3625 6.10625 14.8344 6 15.3313 6H16.6656C18.5063 6 20 7.49375 20 9.33438C20 9.70312 19.7 10 19.3344 10H12.6656ZM7 7C7 6.20435 7.31607 5.44129 7.87868 4.87868C8.44129 4.31607 9.20435 4 10 4C10.7956 4 11.5587 4.31607 12.1213 4.87868C12.6839 5.44129 13 6.20435 13 7C13 7.79565 12.6839 8.55871 12.1213 9.12132C11.5587 9.68393 10.7956 10 10 10C9.20435 10 8.44129 9.68393 7.87868 9.12132C7.31607 8.55871 7 7.79565 7 7ZM4 15.1656C4 12.8656 5.86562 11 8.16562 11H11.8344C14.1344 11 16 12.8656 16 15.1656C16 15.625 15.6281 16 15.1656 16H4.83437C4.375 16 4 15.6281 4 15.1656Z" fill="#40A6B4"/>
                                                </svg>
                                            </span>
                                                    <span>Club Information</span>
                                                </h6>

                                                <div class="sec-content-con">

                                                    <div class="d-flex flex-wrap pt-3 px-3 sec-content" style="gap: 6px;">
                                                        <?php foreach ($data_keywords as $k => $keyword) {
                                                            if(!empty($keyword)){ ?>
                                                                <span class="club-info-list"><?php echo $keyword; ?></span>
                                                            <?php }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>

                                    </div>

                                    <div>
                                        <div class="sec-content-con">
                                            <div class="sec-content d-flex align-items-center" style="gap: 12px;">
                                                <?php if( $user_is_logged_in && (isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken) && taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken != $ptoken)) { ?>
                                                    <!-- <a  class="btn std-btn profile_send_email_btn" data-toptoken="<?= $ptoken ?? ''; ?>">Message</a> -->
                                                <?php } ?>
                                                <!-- <a href="#" class="btn std-btn">Add to my network</a> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }

                        ?>

                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

</div>

<?php
if (!$is_my_profile_view) {
    $show_cs_button = false;
    $show_profile_stage_prompt = false;
    $show_profile_stage_prompt_msg = '';
    if ($my_profile_stage == 1) {
        $show_cs_button = true;
        $show_profile_stage_prompt = true;
        $show_profile_stage_prompt_msg = "To view more, please ensure your profile general settings are completed.";
    }

    if (!$show_profile_stage_prompt && $my_profile_stage >= 2 && $profile_stage < 2) {
        $show_profile_stage_prompt = true;
        $show_profile_stage_prompt_msg = "";
    }

//    if (!empty($show_profile_stage_prompt) && !empty($show_profile_stage_prompt_msg)) {
//        echo '<div class="col footer-prompt" id="profile-stage-prompt">';
//        echo '<h5 class="pb-2">' . $show_profile_stage_prompt_msg . '</h5>';
//        if ($show_cs_button) {
//            echo '<a href="' . (TAOH_SITE_URL_ROOT ?? '') . '/settings" class="btn theme-btn"><i class="fa fa-gears mr-1"></i>Complete Settings</a>';
//        }
//        echo '</div>';
//    }
}
?>


<!-- Profile Page Offline Message Modal -->
<div class="modal fade" id="profileOfflineMessageModal" tabindex="-1" role="dialog" aria-labelledby="profileOfflineMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div style="background:none;border:none" class="modal-content">
            <div class="modal-body p-0">
                <div class="card card-item">
                    <div class="card-body">
                        <div id="profileOfflineMessageBlock">
                            <h3 class="fs-22 fw-bold">Type your message</h3>
                            <div class="row fs-15 mt-4 mb-4">
                                <div class="col-10">
                                    <textarea name="profileOfflineMessage" id="profileOfflineMessage" rows="5" maxlength="500" placeholder="Say something" required></textarea>
                                </div>
                                <input type="hidden" id="profileOfflineLocationPath" value="">
                                <input type="hidden" id="profileOfflineToPtoken" value="">
                            </div>
                            <button type="button" class="btn btn-primary fw-medium" id="profile_message_send_button">Send</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                        <div id="profileOfflineSuccessMessage" class="alert text-success mt-3" style="display: none;">
                            Your message has been sent successfully!
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$dm_room_slug = hash('crc32', 'dm-direct-message');
?>


<script type="text/javascript">
    let user_is_logged_in = <?= json_encode(taoh_user_is_logged_in() ?? false); ?>;
    let profile_ptoken = '<?php echo $ptoken ?? ''; ?>';
    let profile_complete = '<?php echo $profile_user_data['profile_complete'] ?? ''; ?>';
    let my_profile_complete = '<?php echo $data->profile_complete ?? ''; ?>';

    let is_my_profile_view = <?= json_encode($is_my_profile_view ?? true); ?>;
    let my_profile_stage = parseInt('<?= $my_profile_stage ?? 0; ?>', 10) || 0;
    let my_pToken = '<?php echo $my_ptoken ?? ''; ?>';
    let dm_room_slug = '<?php echo $dm_room_slug ?? ''; ?>';


    $(document).ready(function () {
        $("#directory_flag").select2({
            placeholder: 'Suggested Connections',
            allowClear: true,
            width: '100%',
        }).on('select2:select', function (e) {
            let selectedValue = e.params.data.id;
            if (selectedValue) {
                window.open(_taoh_site_url_root + '/directory/profile/flag/' + selectedValue, '_blank');
            }
        });
        if(!is_my_profile_view) {
            let show_profile_stage_prompt = <?= json_encode($show_profile_stage_prompt ?? false); ?>;
            let show_profile_stage_prompt_msg = <?= json_encode($show_profile_stage_prompt_msg ?? ''); ?>;
            let show_cs_button = <?= json_encode($show_cs_button ?? false); ?>;
            if (show_profile_stage_prompt && show_profile_stage_prompt_msg?.trim()) {
                let cs_buttons = [];
                if (show_cs_button) {
                    cs_buttons.push({
                        text: 'Complete Settings',
                        action: () => {
                            window.location.href = _taoh_site_url_root + '/settings';
                        },
                        class: 'dojo-v1-btn float-right mt-3 mb-3'
                    });
                } else {
                    cs_buttons.push({
                        text: 'Ok',
                        action: () => {},
                        class: 'dojo-v1-btn float-right mt-3 mb-3'
                    });
                }
                taoh_set_warning_message(show_profile_stage_prompt_msg, false, 'toast-middle', cs_buttons);
            }
        }

        //if ((my_profile_complete == 0 || (my_profile_complete == 1 && my_profile_stage < 2)) && my_pToken != profile_ptoken) {
        //    $(".res-con").hide();
        //    taoh_set_error_message("Please complete your profile when u try to see other's profile");
        //    window.location = "<?php //echo $taoh_home_url; ?>//";
        //} else if (profile_complete == 0 && my_pToken != profile_ptoken) {
        //    $(".res-con").hide();
        //    taoh_set_error_message("Requested profile is not completed.. So you cant see!");
        //    window.location = "<?php //echo $taoh_home_url; ?>//";
        //}

        /* if(my_profile_complete == 1 && my_profile_stage < 2 && my_pToken != profile_ptoken){
            $("#profile_stage_invalid").show();
            $(".profile_stage_valid").hide();
        } */
        /* $('.profile_send_email_btn').on('click', function () {

            let toPtoken = $(this).data('toptoken');
            let respondPtoken = '<?php //echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>';

            if(toPtoken?.trim() !== ''){
                $('#profileOfflineMessage').val('');
                $('#profileOfflineToPtoken').val(toPtoken);
                $('#profileOfflineLocationPath').val('/profile/' + respondPtoken);
                $('#profileOfflineSuccessMessage').hide();
                $('#profileOfflineMessageBlock').show();
                $('#profileOfflineMessageModal').modal('show');
            }
        }); */
    });

</script>

<?php taoh_get_footer(); ?>
