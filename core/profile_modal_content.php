<?php
$user_is_logged_in = taoh_user_is_logged_in() ?? false;

if (!$user_is_logged_in) {
//    header("Location: " . TAOH_SITE_URL_ROOT . '/login');
    exit();
}

$taoh_home_url = (defined('TAOH_PAGE_URL') && TAOH_PAGE_URL) ? TAOH_PAGE_URL : TAOH_SITE_URL_ROOT;
$directory_flags_to_show = defined('TAOH_DIRECTORY_FLAGS_TO_SHOW') ? TAOH_DIRECTORY_FLAGS_TO_SHOW : [];
$professional_hobbies = defined('PROFESSIONAL_HOBBIES') ? PROFESSIONAL_HOBBIES : [];

$data = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];

if (!isset($pagename)) {
    $pagename = "";
}

$ptoken = $_GET['profile_token'] ?? $data->ptoken;
$isViewMore = isset($_GET['view_more']) && $_GET['view_more'] == 'true';

$path = isset($_GET['path']) ? base64_decode(urldecode($_GET['path'])) : '';
$url_path = parse_url($path, PHP_URL_PATH);
$url_query = parse_url($path, PHP_URL_QUERY);

$urlPathSections = explode('/', trim($url_path, '/'));
parse_str($url_query ?? '', $queryParams); // Parse the query string into an associative array

if (empty($ptoken) || $ptoken == 'stlo') {
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

$user_company_names = array_filter($profile_user_data['company'] ?? [], function ($t) {
    return !empty($t['value']);
});
$user_company_roles = array_filter($profile_user_data['title'] ?? [], function ($t) {
    return !empty($t['value']);
});
$user_profile_type = $profile_user_data['type'] ?? '';
$full_location = $profile_user_data['full_location'] ?? '';

$maxTagsToShow = 5;
$profile_tag_category = $profile_user_data['tags'] ?? [];
$my_profile_tag_category = $is_my_profile_view ? $profile_tag_category : ($data->tags ?? []);

$sub_domain_value = $profile_user_data['site']['sub_domain_value'] ?? '';
$source = json_decode($sub_domain_value, true)[0]['source'] ?? '';

$is_same_source = defined('TAOH_SITE_URL_ROOT') && $source === TAOH_SITE_URL_ROOT;

$about_me = implode(' ', array_filter(explode(' ', $profile_user_data['aboutme'] ?? '')));
$fun_fact = trim(taoh_title_desc_decode($profile_user_data['funfact'] ?? ''));
$hobbies = isset($profile_user_data['hobbies']) ? json_decode(implode(' ', array_filter(explode(' ', $profile_user_data['hobbies'])))) : [];

$maxSkillsToShow = 3;
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

//     $taoh_vals['cache_required'] = 0;
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
    .profile_follow_btn {
        background-color: transparent;
    }

    .profile_follow_btn[data-follow_status="1"] {
        background-color: #2557A7 !important;
        border: 1px solid #2557A7 !important;
        color: #ffffff !important;
    }

    .gap-1 {
        gap: .25rem !important;
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

<div class="profile-v1-right-modal">
    <div class="p-v1-header">
        <div class="d-flex justify-content-center">
            <img class="p-v1-img" src="<?= $avatar_image ?>" alt="profile image"> <!--($is_my_profile_view || $my_profile_stage >= 1)-->
        </div>
        <div class="d-flex align-items-center" style="gap: 8px;">
            <p class="name"><?= ucfirst($profile_user_data['fname']); ?></p>
            <span class="p-type"><?= ucfirst($user_profile_type) ?></span>
        </div>
        <div class="align-items-center mt-7px rs_profile_live_status" data-ptoken="<?= $ptoken ?>" style="display: none;">
            <div class="d-flex align-items-center" style="gap: 3px;">
                <span class="status-con"></span>
                <span class="status-text">Away</span>
            </div>
        </div>

        <!-- :rk Add linkedin link here -->
        <!-- (($is_my_profile_view || $my_profile_stage >= 2) && !empty($profile_user_data['mylink'] ?? '')) -->


        <?php if (!$is_my_profile_view): ?>
        <button type="button" class="btn add-to-follow-btn profile_follow_btn" data-ptoken="<?= $ptoken ?>" data-follow_status="<?= $follow_status ?? 0 ?>"  data-page="<?= $urlPathSections[0] ?? 'club' ?>" title="<?= ($follow_status ?? 0) == 1 ? 'Following' : 'Click to Follow' ?>">
            <i class="fas fa-user-plus fa-sm follow-user-plus-icon" aria-hidden="true"></i>
        </button>
        <?php endif; ?>
    </div>

    <div class="p-v1-body">
        <?php if ($my_profile_stage < 2): ?>
            <p class="message-restriction">To see <span class="text-capitalize"><?= ucfirst($profile_user_data['fname']); ?></span> profile, complete your settings
                <a href="<?= TAOH_SITE_URL_ROOT . '/settings'; ?>" target="_blank">now</a></p>
        <?php endif; ?>

        <?php
        // if ($my_profile_stage >= 2 && $profile_stage < 2) {
        //     echo '<p class="message-restriction">This profile is incomplete, so you\'re currently unable to connect.</p>';
        // }
        ?>

        <div class="d-flex align-items-center justify-content-center" style="gap: 9px;">
            <?php
            if (!$is_my_profile_view && $my_profile_stage >= 2 && $pagename != "networking" ) {
                if (($urlPathSections[0] == 'club' && $urlPathSections[1] == 'room')) {
                    $queryParams['chatwith'] = $ptoken;
                    echo '<a href="' . TAOH_SITE_URL_ROOT . $url_path . '?' . http_build_query($queryParams) . '" target="_blank" class="btn std-btn">Chat</a>';
                } else {
                    echo '<a href="' . TAOH_SITE_URL_ROOT . '/profile/' . $ptoken . '?from=rspchat" target="_blank" class="btn std-btn">Chat</a>';
                }
            }

            if ($my_profile_stage >= 2) {
                echo '<button type="button" class="btn bor-btn d-none" data-toggle="collapse" data-target="#profile_rs_view_more" aria-expanded="' . ($isViewMore ? 'true' : 'false') . '" aria-controls="profile_rs_view_more">View ' . ($isViewMore ? 'Less' : 'More') . '</button>';
            }
            ?>
        </div>

        <?php if (($is_my_profile_view || $my_profile_stage >= 2) && !empty($get_skill)): ?>
            <div class="p-v1-card">
                <div class="p-v1-title">Skills</div>

                <div class="skill-con d-flex flex-wrap align-items-center mt-2 gap-1">
                    <?php
                    $visibleSkills = array_slice($get_skill, 0, $maxSkillsToShow, true);
                    $remainingSkills = array_slice($get_skill, $maxSkillsToShow, null, true);
                    foreach ($visibleSkills as $s_key => $skill) {
                        echo '<span class="btn skill-list skill_directory" data-skillid="' . $s_key . '" data-skillslug="' . $skill['slug'] . '">' . htmlspecialchars($skill['value']) . '</span>';
                    }
                    if (!empty($remainingSkills)) {
                        $remainingSkillscount = count($remainingSkills);
                        echo '<span class="remaining-skills-container" style="display: none;">';
                        foreach ($remainingSkills as $s_key => $skill) {
                            echo '<span class="btn skill-list skill_directory" data-skillid="' . $s_key . '">' . htmlspecialchars($skill['value']) . '</span>';
                        }
                        echo '</span>';
                        echo '<span class="remaining-skills rounded-pill cursor-pointer" data-count="'.$remainingSkillscount.'" style="color: #6f42c1;">+'.$remainingSkillscount.'</span>';
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (($is_my_profile_view || $my_profile_stage >= 2) && !empty($profile_tag_category)): ?>
            <div class="p-v1-card">
                <div class="p-v1-title">Interests</div>

                <div class="skill-cat-con d-flex flex-wrap align-items-center mt-2 gap-1">
                    <?php
                    $visibleTags = array_slice($profile_tag_category, 0, $maxTagsToShow, true);
                    $remainingTags = array_slice($profile_tag_category, $maxTagsToShow, null, true);
                    foreach ($visibleTags as $tag) {
                        $kebab_flag = str_replace(' ', '-', strtolower($tag));
                        echo '<span class="btn fs-11 category category-' . mt_rand(1, 4) . ' profile_flag_directory" data-flagslug="'. $kebab_flag .'">' . htmlspecialchars($tag) . '</span>';
                    }
                    if (!empty($remainingTags)) {
                        $remainingTagscount = count($remainingTags);
                        echo '<span class="remaining-skills-container" style="display: none;">';
                        foreach ($remainingTags as $tag) {
                            $kebab_flag = str_replace(' ', '-', strtolower($tag));
                            echo '<span class="btn fs-11 category category-' . mt_rand(1, 4) . ' profile_flag_directory" data-flagslug="'. $kebab_flag .'">' . htmlspecialchars($tag) . '</span>';
                            // <span class="fs-11 category category-spl"><span class="spl-p"></span></span>
                        }
                        echo '</span>';
                        echo '<span class="remaining-skills rounded-pill cursor-pointer" data-count="'.$remainingTagscount.'" style="color: #6f42c1;">+'.$remainingTagscount.'</span>';
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (($is_my_profile_view || $my_profile_stage >= 2) && !empty($user_company_names)): ?>
            <div class="p-v1-card">
                <div class="p-v1-title">Role, Company</div>
                <div class="p-v1-content">
                    <?php
                    // Roles
                    if (!empty($user_company_roles)) {                        
                        foreach ($user_company_roles as $k => $role) {
                            echo '<span class="role_directory cursor-pointer underline-on-hover" data-roleid="' . $k . '" data-roleslug="' . $role['slug'] . '">' . htmlspecialchars($role['value']) . '</span>';
                            if ($k < count($user_company_roles) - 1) echo ', ';
                        }
                    }
                    // Companies
                    if (!empty($user_company_names)) {
                        echo ', ';
                        foreach ($user_company_names as $k => $company) {
                            echo '<span class="company_directory cursor-pointer underline-on-hover" data-companyid="' . $k . '" data-companyslug="' . $company['slug'] . '">' . htmlspecialchars($company['value']) . '</span>';
                            if ($k < count($user_company_names) - 1) echo ', ';
                        }
                    }                    
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="p-v1-card">
            <div class="p-v1-title">Network</div>
            <div class="p-v1-content">
                <span>
                    <span class="mr-2 followers-count-view <?= $is_my_profile_view ? 'profile_followers_btn' : '' ?>" data-ptoken="<?= $ptoken ?? ''; ?>" data-fscount="<?= $followers_count ?? 0; ?>"><?= $followers_count ?? 0; ?> Followers</span>
                    <span class="mr-2 following-count-view <?= $is_my_profile_view ? 'profile_following_btn' : '' ?>" data-ptoken="<?= $ptoken ?? ''; ?>" data-fgcount="<?= $following_count ?? 0; ?>"><?= $following_count ?? 0; ?> Following</span>
                </span>
            </div>
        </div>

        <?php if (($is_my_profile_view || $my_profile_stage >= 1) && !empty($full_location)): ?>
            <div class="p-v1-card">
                <div class="p-v1-title">Location</div>
                <div class="p-v1-content"><?= $full_location ?></div>
            </div>
        <?php endif; ?>

        <?php if (($is_my_profile_view || $my_profile_stage >= 2) && !empty($about_me)): ?>
        <div class="p-v1-card">
            <div class="p-v1-title">About</div>
            <div class="p-v1-content">
                <?= taoh_title_desc_decode($about_me) ?>
                <!--   :rk add read more     <span class="v1-read-more">Read more</span>-->
            </div>
        </div>
        <?php endif; ?>

        <?php if ($my_profile_stage >= 2 && $profile_stage >= 2): ?>
        <div class="collapse <?= $isViewMore ? 'show' : '' ?>" id="profile_rs_view_more">
            <?php
            if (!empty($emp_list) && ($is_my_profile_view || $my_profile_stage >= 2)) {
                $industry_categories = defined('TAOH_INDUSTRY_CATEGORIES')? TAOH_INDUSTRY_CATEGORIES : [];

                $emp_first_vals = reset($emp_list);

                $emp_first_company = $emp_first_vals['emp_company'] ?? $emp_first_vals['company'];
                $emp_first_title = $emp_first_vals['emp_title'] ?? $emp_first_vals['title'];
                ?>
                <div class="p-v1-card">
                    <div class="p-v1-title d-flex align-items-center justify-content-between">
                        Experience Details

                        <button class="btn shadow-none p-0" type="button" data-toggle="collapse" data-target="#viewExperience" aria-expanded="false" aria-controls="viewExperience">
                            <i class="fa fa-angle-down"></i>
                        </button>
                    </div>

                    <div class="p-v1-content">
                        <?php
                        $emp_first_cmp_pre = $emp_first_cmp_post = '';
                        foreach ($emp_first_company as $company) {
                            if (is_array($company)) {
                                $company = reset($company); // Get the first element if it's an array
                            }

                            $company_parts = explode(':>', $company);
                            if (count($company_parts) == 2) {
                                list($emp_first_cmp_pre, $emp_first_cmp_post) = $company_parts;
                            } else {
                                // Handle invalid format
                                $emp_first_cmp_pre = $company;
                                $emp_first_cmp_post = '';
                            }
                        }

                        $emp_first_pre = $emp_first_post = '';
                        foreach ($emp_first_title as $title) {
                            if (is_array($title)) {
                                $title = reset($title); // Get the first element if it's an array
                            }

                            $title_parts = explode(':>', $title);
                            if (count($title_parts) == 2) {
                                list($emp_first_pre, $emp_first_post) = $title_parts;
                            } else {
                                // Handle invalid format
                                $emp_first_pre = $title;
                                $emp_first_post = '';
                            }
                        }

                        $emp_first_start_month = get_month_from_number($emp_first_vals['emp_start_month']);
//                            $emp_first_end_month = get_month_from_number($emp_first_vals['emp_end_month']);

                        $get_first_present_not = ($emp_first_vals['current_role'] ?? '') == 'on' ? 'Present' : get_month_from_number($emp_first_vals['emp_end_month']) . ' ' . $emp_first_vals['emp_year_end'];

                        echo '<p><b>' . $emp_first_cmp_post . (!empty($emp_first_post) ? ' - ' . $emp_first_post : '') . '</b></p>';
                        echo '<p>' . $emp_first_start_month . ' ' . $emp_first_vals['emp_year_start'] . ' to ' . $get_first_present_not . '</p>';
                        ?>
                        <div class="collapse" id="viewExperience">
                            <?php
                            $isExpFirstIteration = true;
                            foreach ($emp_list as $emp_keys => $emp_vals) {
                                $emp_company = $emp_vals['emp_company'] ?? $emp_vals['company'];
                                $emp_title = $emp_vals['emp_title'] ?? $emp_vals['title'];

                                $emp_cmp_pre = $emp_cmp_post = '';
                                foreach ($emp_company as $company) {
                                    if (is_array($company)) {
                                        $company = reset($company); // Get the first element if it's an array
                                    }

                                    $company_parts = explode(':>', $company);
                                    if (count($company_parts) == 2) {
                                        list($emp_cmp_pre, $emp_cmp_post) = $company_parts;
                                    } else {
                                        // Handle invalid format
                                        $emp_cmp_pre = $company;
                                        $emp_cmp_post = '';
                                    }
                                }

                                $emp_pre = $emp_post = '';
                                foreach ($emp_title as $title) {
                                    if (is_array($title)) {
                                        $title = reset($title); // Get the first element if it's an array
                                    }

                                    $title_parts = explode(':>', $title);
                                    if (count($title_parts) == 2) {
                                        list($emp_pre, $emp_post) = $title_parts;
                                    } else {
                                        // Handle invalid format
                                        $emp_pre = $title;
                                        $emp_post = '';
                                    }
                                }

                                $emp_start_month = get_month_from_number($emp_vals['emp_start_month']);
//                                $emp_end_month = get_month_from_number($emp_vals['emp_end_month']);

                                // Determine employment status
                                $get_present_not = ($emp_vals['current_role'] ?? '') == 'on' ? 'Present' : get_month_from_number($emp_vals['emp_end_month']) . ' ' . $emp_vals['emp_year_end'];

                                // Determine the work location type
                                $emp_placeType = match ($emp_vals['emp_placeType']) {
                                    'rem' => 'Remote',
                                    'ons' => 'Onsite',
                                    'hyb' => 'Hybrid',
                                    default => '',
                                };

                                // Role type mapping
                                $roletype_arr = defined('TAOH_ROLE_TYPES')? TAOH_ROLE_TYPES : [];

                                $role_items = '';
                                foreach ($emp_vals['emp_roletype'] ?? [] as $value) {
                                    if ($value) {
                                        $role_items .= $roletype_arr[$value] ?? '';
                                    }
                                }

                                // Industry mapping
                                $industry_arr = defined('TAOH_INDUSTRY_CATEGORIES')? TAOH_INDUSTRY_CATEGORIES : [];

                                if (!$isExpFirstIteration) {
                                    echo '<hr class="my-2">';

                                    echo '<p><b>' . $emp_cmp_post . (!empty($emp_post) ? ' - ' . $emp_post : '') . '</b></p>';
                                    echo '<p>' . $emp_start_month . ' ' . $emp_vals['emp_year_start'] . ' to ' . $get_present_not . '</p>';
                                }

                                if(!empty($emp_vals['emp_full_location'])) {
                                    echo '<p>' . $emp_vals['emp_full_location'] . '</p>';
                                }

                                if(!empty($emp_placeType)) {
                                    echo '<p>' . $emp_placeType . (!empty($role_items) ? ', ' . $role_items : '') . '</p>';
                                }

                                if (!empty($emp_vals['skill'])) {
                                    echo '<div class="skill-con d-flex flex-wrap align-items-center mt-2 gap-1">';
                                    foreach ($emp_vals['skill'] as $s_vals) {
                                        $items = is_array($s_vals) ? $s_vals : explode(':>', $s_vals);
                                        if(!empty($items[1])) echo '<span class="btn skill-list">' . $items[1] . '</span>';
                                    }
                                    echo '</div>';
                                }

                                if (!empty($industry_arr[$emp_vals['emp_industry']])) {
                                    echo '<p>Industry: ' . $industry_arr[$emp_vals['emp_industry']] . '</p>';
                                }

                                if (!empty($emp_vals['emp_responsibilities']) && trim(taoh_title_desc_decode($emp_vals['emp_responsibilities']))) {
                                    $responsibilities = taoh_title_desc_decode($emp_vals['emp_responsibilities']);
                                    echo "<p class='mt-2'><b>Responsibilities</b></p>";
                                    echo "<p class='mt-1'>{$responsibilities}</p>";  // :rk add read more <span class="v1-read-more">Read more</span>
                                }

                                $isExpFirstIteration = false;
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>

            <?php
            if (!empty($edu_list) && ($is_my_profile_view || $my_profile_stage >= 2)) {
                $edu_first_vals = reset($edu_list);
                $edu_first_company = $edu_first_vals['company'];
                ?>
                <div class="p-v1-card">
                <div class="p-v1-title d-flex align-items-center justify-content-between">
                    Educational Qualifications

                    <button class="btn shadow-none p-0" type="button" data-toggle="collapse" data-target="#viewEduQua" aria-expanded="false" aria-controls="viewEduQua">
                        <i class="fa fa-angle-down"></i>
                    </button>
                </div>

                <div class="p-v1-content">
                    <?php
                    $edu_first_cmp_pre = $edu_first_cmp_post = '';
                    foreach ($edu_first_company as $company) {
                        if (is_array($company)) {
                            $company = reset($company); // Get the first element if it's an array
                        }

                        $company_parts = explode(':>', $company);
                        if (count($company_parts) == 2) {
                            list($edu_first_cmp_pre, $edu_first_cmp_post) = $company_parts;
                        } else {
                            // Handle invalid format
                            $edu_first_cmp_pre = $company;
                            $edu_first_cmp_post = '';
                        }
                    }
                    $edu_first_start_month = get_month_from_number($edu_first_vals['edu_start_month']);
                    $edu_first_end_month = get_month_from_number($edu_first_vals['edu_end_month']);

                    echo '<p><b>' . taoh_title_desc_decode($edu_first_vals['edu_specalize']) . '</b></p>';
                    echo '<p>' . $edu_first_start_month . ' ' . $edu_first_vals['edu_start_year'] . ' to ' . $edu_first_end_month . ' ' . $edu_first_vals['edu_complete_year'] . '</p>';

                    ?>

                    <div class="collapse" id="viewEduQua">
                        <?php
                        $isEduFirstIteration = true;
                        foreach ($edu_list as $edu_keys => $edu_vals) {
                            $edu_company = $edu_vals['company'];

                            $edu_cmp_pre = $edu_cmp_post = '';
                            foreach ($edu_company as $company) {
                                if (is_array($company)) {
                                    $company = reset($company); // Get the first element if it's an array
                                }

                                $company_parts = explode(':>', $company);
                                if (count($company_parts) == 2) {
                                    list($edu_cmp_pre, $edu_cmp_post) = $company_parts;
                                } else {
                                    // Handle invalid format
                                    $edu_cmp_pre = $company;
                                    $edu_cmp_post = '';
                                }
                            }

                            $edu_start_month = get_month_from_number($edu_vals['edu_start_month']);
                            $edu_end_month = get_month_from_number($edu_vals['edu_end_month']);

                            if (!$isEduFirstIteration) {
                                echo '<hr class="my-2">';

                                echo '<p><b>' . taoh_title_desc_decode($edu_vals['edu_specalize']) . '</b></p>';
                                echo '<p>' . $edu_start_month . ' ' . $edu_vals['edu_start_year'] . ' to ' . $edu_end_month . ' ' . $edu_vals['edu_complete_year'] . '</p>';
                            }

                            if(!empty($edu_vals['edu_grade'])) {
                                echo '<p>Grade: ' . $edu_vals['edu_grade'] . '</p>';
                            }

                            if (!empty($edu_vals['skill'])) {
                                echo '<div class="skill-con d-flex flex-wrap align-items-center mt-2 gap-1">';
                                foreach ($edu_vals['skill'] as $s_vals) {
                                    $items = is_array($s_vals) ? $s_vals : explode(':>', $s_vals);
                                    if(!empty($items[1])) echo '<span class="btn skill-list">' . $items[1] . '</span>';
                                }
                                echo '</div>';
                            }

                            if (!empty($edu_vals['edu_activities']) && trim(taoh_title_desc_decode($edu_vals['edu_activities']))) {
                                $activities = taoh_title_desc_decode($edu_vals['edu_activities']);
                                echo "<p class='mt-2'><b>Activities</b></p>";
                                echo "<p class='mt-1'>{$activities}</p>";  // :rk add read more <span class="v1-read-more">Read more</span>
                            }

                            if (!empty($edu_vals['edu_description']) && trim(taoh_title_desc_decode($edu_vals['edu_description']))) {
                                $description = taoh_title_desc_decode($edu_vals['edu_description']);
                                echo "<p class='mt-2'><b>Description</b></p>";
                                echo "<p class='mt-1'>{$description}</p>";  // :rk add read more <span class="v1-read-more">Read more</span>
                            }

                            $isEduFirstIteration = false;
                        }
                        ?>
                    </div>
                </div>
            </div>
                <?php
            }
            ?>

            <?php if (!empty($hobbies)): ?>
            <div class="p-v1-card">
                <div class="p-v1-title">Hobbies</div>

                <div class="hobby-v1-con d-flex flex-wrap align-items-center mt-2 gap-1">
                    <?php
                    foreach ($hobbies as $hobby) {
                        if (isset($professional_hobbies[$hobby])) {
                            echo '<span class="btn hobby-v1-list profile_hobby_directory" data-hobbyslug="'.$hobby.'">' . $professional_hobbies[$hobby] . '</span>';
                        }
                    }
                    ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($fun_fact)): ?>
                <div class="p-v1-card">
                    <div class="p-v1-title">Fun Fact</div>
                    <div class="p-v1-content"><?= $fun_fact ?></div>
                    <!--   :rk add read more     <span class="v1-read-more">Read more</span>-->
                </div>
            <?php endif; ?>

            <?php if (!empty($data_keywords)): ?>
            <div class="p-v1-card">
                <div class="p-v1-title">Club Information</div>

                <div class="club-v1-con d-flex flex-wrap align-items-center mt-2 gap-1">
                    <?php
                    foreach ($data_keywords as $keyword) {
                        echo '<span class="club-v1-list club-info-list">' . htmlspecialchars($keyword) . '</span>';
                    }
                    ?>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <!-- <button class="btn btn-v1-link shadow-none mt-3" type="button" data-toggle="collapse" data-target="#profile_rs_view_more" aria-expanded="<?= $isViewMore ? 'true' : 'false' ?>" aria-controls="profile_rs_view_more">
            View <?= $isViewMore ? 'Less' : 'More' ?>
        </button> -->
        <?php endif; ?>
       
    </div>
</div>