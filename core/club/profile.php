<?php
include_once TAOH_SITE_PATH_ROOT.'/assets/icons/icons.php';
taoh_get_header();

$pagename = 'profile';
$appname = 'club';


$ptoken = taoh_parse_url(2);
if (empty($ptoken)) {
    $user_info_obj = taoh_user_all_info();
    $ptoken = $user_info_obj->ptoken;
}

/* Get User Info */
$return = taoh_get_user_info($ptoken, 'full');
$data = json_decode($return, true);
/* Get User Info */

$user_is_logged_in = taoh_user_is_logged_in() ?? false;

$about_type = '';
$about_me = '';
$fun_fact = '';

//echo '<pre>';print_r($return);die();
if (!isset($data['output']) || !$data['success'] || $data['success'] == '') {
    taoh_set_error_message('Invalid profile!');
    taoh_redirect(TAOH_SITE_URL_ROOT);
    die();
}
/*if(!isset($data['output']['user']) || count($data['output']['user']) == 0){
    taoh_set_error_message('Invalid profile!');
    taoh_redirect(TAOH_SITE_URL_ROOT);
    die();
}*/

$sub_domain_value = $data['output']['user']['site']['sub_domain_value'] ?? '';
$source = json_decode($sub_domain_value, true)[0]['source'] ?? '';

$is_same_source = defined('TAOH_SITE_URL_ROOT') && $source === TAOH_SITE_URL_ROOT;


$about_me = implode(' ', array_filter(explode(' ', $data['output']['user']['aboutme'])));
$fun_fact = implode(' ', array_filter(explode(' ', $data['output']['user']['funfact'])));
//$user_ptoken = //$data['output']['user']['ptoken'];

if (isset($data['output']['user']['about_type']))
    $about_type = implode(' ', array_filter(explode(' ', $data['output']['user']['about_type'])));
$get_skill = $data['output']['user']['skill'];
if (!$user_is_logged_in) {
    $about_me = substr($about_me, 0, 100);
    $fun_fact = substr($fun_fact, 0, 100);
    $about_type = substr($about_type, 0, 100);
}

if (isset($data['output']['user']['avatar_image']) && $data['output']['user']['avatar_image'] != '') {
    $avatar_image = $data['output']['user']['avatar_image'];
} else {
    if (isset($data['output']['user']['avatar']) && $data['output']['user']['avatar'] != 'default') {
        $avatar_image = TAOH_OPS_PREFIX . '/avatar/PNG/128/' . $data['output']['user']['avatar'] . '.png';
    } else {
        $avatar_image = TAOH_OPS_PREFIX . '/avatar/PNG/128/avatar_def.png';
    }
}

if (isset($data['output']['user']['education']) && is_array($data['output']['user']['education'])) {
    $edu_encode = json_encode($data['output']['user']['education']);
    $edu_list = json_decode($edu_encode, true);
    $edu_tot_count = array_key_last($edu_list) + 1;
    $edu_last_key = array_key_last($edu_list);
} else {
    $edu_tot_count = 0;
    $edu_last_key = 0;
    $edu_list = '';
}
if (isset($data['output']['user']['employee']) && is_array($data['output']['user']['employee'])) {
    $emp_encode = json_encode($data['output']['user']['employee']);
    $emp_list = json_decode($emp_encode, true);
    $emp_tot_count = array_key_last($emp_list) + 1;
    $emp_last_key = array_key_last($emp_list);
} else {
    $emp_tot_count = 0;
    $emp_last_key = 0;
    $emp_list = '';
}

$data_keywords = (array)($data['output']['user']['keywords'] ?? []);

?>
    <style>
        .page-body {
            background-color: #fff !important;
        }

        #login-prompt {
            display: block !important;
        }

        .skill-link, .keywords-link {
            color: #000000;
            background-color: powderblue;
            margin-right: 5px;
            margin-bottom: 7px;
            text-align: center;
            display: inline-block;
            font-size: 12px;
            line-height: 16px;
            padding: 7px 15px;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 6px;
            -webkit-transition: all 0.2s;
            -moz-transition: all 0.2s;
            -ms-transition: all 0.2s;
            -o-transition: all 0.2s;
            transition: all 0.2s;
            /* border: 1px solid rgba(121, 127, 135, 0.05);*/
        }

        .keywords-link {
            cursor: pointer;
        }

        .keywords-link:hover {
            font-weight: bold;
        }

        .prof-link {
            color: #fff;
            background-color: #131a4c;
            /* margin-right: 5px; */
            margin-bottom: 7px;
            text-align: center;
            /* display: inline-block; */
            font-size: 12px;
            line-height: 30px;
            padding: 7px 15px;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 20px;
            -webkit-transition: all 0.2s;
            -moz-transition: all 0.2s;
            -ms-transition: all 0.2s;
            -o-transition: all 0.2s;
            transition: all 0.2s;
            /* border: 1px solid rgba(121, 127, 135, 0.05); */
        }

        .colored {
            height: 150px;
            background-color: black;
            border-radius: 8px;
        }

        .profile {
            position: absolute;
            margin-top: -58px;
        }

        #loading {
            filter: blur(3px);
        }

        .emp_response h3 {
            font-size: medium;
        }

        /* Important part */
        .modal-dialog {
            overflow-y: initial !important
        }

        .modal-body {
            height: 70vh;
            overflow-y: auto;
        }

        .modal-footer-css {
            /* display: flex; */
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            -ms-flex-align: center;
            align-items: center;
            -ms-flex-pack: end;
            justify-content: flex-end;
            padding: 0.75rem;
            border-top: 1px solid #dee2e6;
            border-bottom-right-radius: calc(0.3rem - 1px);
            border-bottom-left-radius: calc(0.3rem - 1px);
        }

        .modal-body .loader {
            width: 15%;
            /* padding: 50px 20px; */
            position: absolute;
            top: 50%;
            left: 50%;
        }

        .add_hover:hover {
            text-decoration: none;
            background-color: lightblue;
            padding: 8px;
            border-radius: 8px;
        }

        .lh-10 {
            line-height: 10px;
        }

        /*.keywords .keywords-link:not(:last-child)::after {
            content: ', ';
        }

        .keywords-link {
            cursor: pointer;
        }

        .keywords-link:hover {
            color: #007bff;
            font-weight: bold;
        }*/
    </style>

    <div class="bg-white">
        <header class="sticky-top bg-white border-bottom border-bottom-gray">
            <section class="hero-area bg-white shadow-sm overflow-hidden">
                <span class="stroke-shape stroke-shape-1"></span>
                <span class="stroke-shape stroke-shape-2"></span>
                <span class="stroke-shape stroke-shape-3"></span>
                <span class="stroke-shape stroke-shape-4"></span>
                <span class="stroke-shape stroke-shape-5"></span>
                <span class="stroke-shape stroke-shape-6"></span>
                <div class="container">
                    <?php include 'includes/club_header.php'; ?>
                </div>
            </section>
        </header>

        <section class="blog-area pt-40px pb-40px">
            <div class="container">
                <div class="media media-card p-0">
                    <div class="media-body">
                        <div class="colored"></div>
                        <div class="media-card mb-0">
                            <div class="profile">
                                <img width="48" height="48" style="border-radius: 20px;" src="<?php echo $avatar_image;?>" alt="">
                            </div>
                            <div class="d-flex justify-content-between">
                                <div class="col-lg-8">
                                    <span class="text-black mr-3"><?php echo $data['output']['user']['chat_name'];?></span>
                                    <span class="prof-link text-capitalize"><?php echo $data['output']['user']['type'] ?? 'professional'; ?></span>
                            
                                    <div class="mt-1"><?php echo $data['output']['user']['full_location'];?></div>
                                </div>
                                <div class="col-lg-4">
                                    <?php if( $user_is_logged_in && (isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken) && taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken != $ptoken)) { ?>
                                        <div>
                                            <div class="hero-btn-box text-right py-3">
                                                <button type="button" id="profile_send_email_btn" class="btn btn-primary fw-medium" data-toptoken="<?= $ptoken ?? ''; ?>">Send Email</button>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div>
                                <?php if( $user_is_logged_in && (isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken) && taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken == $ptoken)) { ?>
                                    <div class="mt-3">
                                        <a class="add_edit_emp add_hover" style="cursor: pointer;" data-add-edit="Add" data-employee="<?php echo $emp_tot_count; ?>">
                                            <?= icon('plus', 'currentColor', 24) ?> Add New Work Experience
                                        </a>
                                    </div>
                                    <div class="mt-3">
                                        <a class="add_edit_edu add_hover" style="cursor: pointer;" data-add-edit="Add" data-education="<?php echo $edu_tot_count; ?>">
                                            <?= icon('plus', 'currentColor', 24) ?> Add New Education
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if(!empty($about_me)){ ?>
                    <div class="media media-card">
                        <div class="media-body">
                            <div class="mb-2"><h5>About</h5></div>
                            <?php if (!$user_is_logged_in) { ?>
                                <a href="#" data-toggle="tooltip" data-placement="top" title="Login to see the full details!">
                                    <div class="mt-2 mb-2" id="loading">
                                        <?php echo $about_me.'......'; ?>
                                    </div>
                                </a>
                            <?php }else{ ?>
                                <div class="mt-2 mb-2">
                                    <?php echo $about_me; ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <?php if(!empty($fun_fact)){ ?>
                    <div class="media media-card">
                        <div class="media-body">
                            <div class="mb-2"><h5>Fun Fact</h5></div>
                            <?php if (!$user_is_logged_in) { ?>
                                <a href="#" data-toggle="tooltip" data-placement="top" title="Login to see the full details!">
                                    <div class="mt-2 mb-2" id="loading">
                                        <?php echo $fun_fact.'......'; ?>
                                    </div>
                                </a>
                            <?php }else{ ?>
                                <div class="mt-2 mb-2">
                                    <?php echo $fun_fact; ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <?php if(!empty($get_skill)){ ?>
                    <div class="media media-card">
                        <div class="media-body">
                            <div class="mb-2"><h5>Skills</h5></div>
                            <?php if (!$user_is_logged_in) { ?>
                                <a href="#" data-toggle="tooltip" data-placement="top" title="Login to see the full details!">
                                    <div class="mt-2 mb-2" id="loading">
                                        <?php foreach($get_skill as $keys => $vals){
                                            if(!empty($vals['value'])){?>
                                                <span class="skill-link"><?php echo $vals['value']; ?></span>
                                            <?php } } ?>
                                    </div>
                                </a>
                            <?php }else{ ?>
                                <div class="mt-2 mb-2">
                                    <?php foreach($get_skill as $keys => $vals){
                                        if(!empty($vals['value'])){?>
                                            <span class="skill-link"><?php echo $vals['value']; ?></span>
                                        <?php } } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <?php if($user_is_logged_in && $is_same_source && !empty($data_keywords)){ ?>
                    <div class="media media-card">
                        <div class="media-body">
                            <div class="mb-2"><h5><?php echo (defined('TAOH_WERTUAL_NAME_SLUG') ? TAOH_WERTUAL_NAME_SLUG . ' ' : '') . 'Information' ?></h5></div>
                            <div class="mt-2 mb-2">
                                <?php foreach ($data_keywords as $k => $keyword) {
                                    echo '<span class="keywords-link" data-keyword_key="'.$k.'" data-keyword_value="'.$keyword.'">' . $keyword . '</span>';
                                } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <?php if(!empty($about_type)){ ?>
                    <div class="media media-card">
                        <div class="media-body">
                            <div class="mb-2"><h5>About Profile Type</h5></div>
                            <?php if (!$user_is_logged_in) { ?>
                                <a href="#" data-toggle="tooltip" data-placement="top" title="Login to see the full details!">
                                    <div class="mt-2 mb-2" id="loading">
                                        <?php echo $about_type.'......'; ?>
                                    </div>
                                </a>
                            <?php }else{ ?>
                                <div class="mt-2 mb-2">
                                    <?php echo $about_type; ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <?php if(is_array($emp_list)){
                    if(count($emp_list) > 0 && is_array($emp_list[$emp_last_key]['title'])){ ?>
                        <div class="media-card">
                            <div class="mb-5">
                                <h5 class="float-left">Experience</h5>
                                <?php if( $user_is_logged_in && (isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken) && taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken == $ptoken)) { ?>
                                    <a class="float-right add_edit_emp" style="cursor: pointer;" data-add-edit="Add" data-employee="<?php echo $emp_tot_count; ?>">
                                        <?= icon('plus', 'currentColor', 24) ?>
                                    </a>
                                <?php } ?>
                            </div>
                            <?php
                            $emp_year = array();
                            foreach($emp_list as $ekeys => $evals){
                                $emp_year[$ekeys] = $evals['emp_year_end'];
                                $emp_list[$ekeys]['keys'] = $ekeys;
                            }
                            array_multisort($emp_year, SORT_DESC, $emp_list);
                            //echo"<pre>";print_r($emp_list);die();
                            foreach($emp_list as $emp_keys => $emp_vals){
                                //print_r($emp_vals);
                                $em_title = ($emp_vals['emp_title'])?$emp_vals['emp_title'] : $emp_vals['title'];
                                foreach ( $em_title as $em_key => $em_value ){
                                    list ( $em_pre, $em_post ) = explode( ':>', $em_value );
                                }
                                $em_company = ($emp_vals['emp_company'])?$emp_vals['emp_company'] : $emp_vals['company'];
                                foreach ( $em_company as $em_cmp_key => $em_cmp_value ){
                                    list ( $em_cmp_pre, $em_cmp_post ) = explode( ':>', $em_cmp_value );
                                }
                                $get_present_not = ($emp_vals['current_role'] == 'on')?' Present':get_month_from_number($emp_vals['emp_end_month']).' '.$emp_vals['emp_year_end'];

                                $emp_placeType = $emp_vals['emp_placeType'];
                                if($emp_placeType == 'rem'){
                                    $emp_placeType = ' . '.'Remote';
                                }else if($emp_placeType == 'ons'){
                                    $emp_placeType = '. '.'Onsite';
                                }else if($emp_placeType == 'hyb'){
                                    $emp_placeType = '. '.'Hybrid';
                                }else{
                                    $emp_placeType = '';
                                }

                                $skills = $emp_vals['skill'];
                                $items = '';
                                foreach ($skills as $s_keys => $s_vals){
                                    $items = explode(':>',$s_vals);
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
                                $roletype = $emp_vals['emp_roletype'];
                                $role_items = '';
                                foreach ($roletype as $key => $value){
                                    $role_items = ' . '.$roletype_arr[$value];
                                }
                                ?>
                                <?php if (!$user_is_logged_in) { ?>
                                    <a href="#" data-toggle="tooltip" data-placement="top" title="Login to see the full details!">
                                        <div class="mt-2 mb-2" id="loading">
                                            <div class="d-flex mt-3">
                            <span style="height:45px;width:45px;" class="media-img d-block">
                                <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/work.png'; ?>" alt="company logo">
                            </span>
                                                <div class="media-body border-left-0 emp_response">
                                                    <h5 class="mb-1 fs-16 fw-medium"><a><?php echo $em_post; ?></a></h5>
                                                    <p class="mb-1 fs-13 font-weight-bold"><?php echo $em_cmp_post; ?></p>
                                                    <p class="mb-4 lh-20 fs-13"><?php echo get_month_from_number($emp_vals['emp_start_month']).' '.$emp_vals['emp_year_start'].' - '.$get_present_not ?> . <span><?php echo get_diff_dates($emp_vals['emp_year_start'],$emp_vals['emp_start_month'],$emp_vals['emp_year_end'],$emp_vals['emp_end_month']); ?></span></p>
                                                    <p class="lh-20 fs-13"><?php echo (strlen($emp_vals['emp_responsibilities'])<=200)?$emp_vals['emp_responsibilities']:mb_substr($emp_vals['emp_responsibilities'], 0, 200).'......'; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                <?php }else{ ?>
                                    <div class="mt-5 mb-2">
                                        <div class="d-flex mt-3">
                                            <div style="height:45px;width:45px;" class="media-img d-block">
                                                <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/work.png'; ?>" alt="company logo">
                                            </div>
                                            <div class="media-body border-left-0 emp_response">
                                                <div>
                                                    <h5 class="mb-1 fs-16 fw-medium float-left"><a><?php echo $em_post; ?></a></h5>
                                                    <?php if( $user_is_logged_in && (isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken) && taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken == $ptoken)) { ?>
                                                        <a class="float-right add_edit_emp" style="cursor: pointer;" data-add-edit="Edit" data-employee="<?php echo $emp_keys; ?>" data-emp-delete="<?php echo $emp_keys; ?>" data-emp-edit-delete = <?php echo $emp_vals['keys']; ?>><?= icon('edit', 'currentColor', 16) ?></a>
                                                    <?php } ?>
                                                </div><br>
                                                <p class="lh-20 fs-13 font-weight-bold"><?php echo $em_cmp_post.$role_items; ?></p>
                                                <p class="lh-20 fs-13"><?php echo get_month_from_number($emp_vals['emp_start_month']).' '.$emp_vals['emp_year_start'].' - '.$get_present_not ?> . <span><?php echo get_diff_dates($emp_vals['emp_year_start'],$emp_vals['emp_start_month'],$emp_vals['emp_year_end'],$emp_vals['emp_end_month']); ?></span></p>
                                                <p class="mb-2 lh-20 fs-13"><?php echo $emp_vals['emp_full_location'].$emp_placeType; ?></p>
                                                <p class="lh-20 mb-3 fs-13"><?php echo (strlen($emp_vals['emp_responsibilities'])<=200)?$emp_vals['emp_responsibilities']:mb_substr($emp_vals['emp_responsibilities'], 0, 200).'......'; ?></p>
                                                <?php if(is_array($emp_vals['skill'])){?>
                                                    <p class="lh-20 fs-13"><span class="lh-20 fs-13 font-weight-bold">Skills: </span><?php echo $items[1]; ?></p>
                                                <?php }?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } }?>

                <?php if(is_array($edu_list)){
                    if(is_array($edu_list[$edu_last_key]['company'])){?>
                        <div class="media-card">
                            <div class="mb-5">
                                <h5 class="float-left">Education</h5>
                                <?php if( $user_is_logged_in && (isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken) && taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken == $ptoken)) { ?>
                                    <a class="float-right add_edit_edu" style="cursor: pointer;" data-add-edit="Add" data-education="<?php echo $edu_tot_count; ?>">
                                        <?= icon('plus', 'currentColor', 24) ?>
                                    </a>
                                <?php } ?>
                            </div>
                            <?php
                            $edu_year = array();
                            foreach($edu_list as $edu_keys => $edu_vals){
                                $edu_year[$edu_keys] = $edu_vals['edu_complete_year'];
                                $edu_list[$edu_keys]['keys'] = $edu_keys;
                            }
                            //echo"<pre>";print_r($edu_list);
                            array_multisort($edu_year, SORT_DESC, $edu_list);
                            //echo"<pre>";print_r($edu_list);die();

                            foreach($edu_list as $edu_keys => $edu_vals){
                                $ed_name = $edu_vals['company'];
                                foreach ( $ed_name as $ed_key => $ed_value ){
                                    list ( $ed_pre, $ed_post ) = explode( ':>', $ed_value );
                                }

                                $degeree_arr = array(
                                    "highschool" => "High School Diploma or GED",
                                    "vocational" => "Vocational/Technical Diploma",
                                    "associate" => "Associate Degree",
                                    "bachelor" => "Bachelor's Degree",
                                    "master" => "Master's Degree",
                                    "doctorate" => "Doctorate or Professional Degree",
                                    "other" => "Other (for degeree not listed above)"
                                );
                                $degree_get = $edu_vals['edu_degree'];
                                $degree_items = '';
                                foreach ($degree_get as $d_key => $d_value){
                                    $degree_items = $degeree_arr[$d_value];
                                }

                                $d_skills = $edu_vals['skill'];
                                $d_items = '';
                                foreach ($d_skills as $d_keys => $d_vals){
                                    $d_items = explode(':>',$d_vals);
                                }
                                ?>
                                <?php if (!$user_is_logged_in) { ?>
                                    <a href="#" data-toggle="tooltip" data-placement="top" title="Login to see the full details!">
                                        <div class="mt-2 mb-2" id="loading">
                                            <div class="d-flex mt-3">
                            <span style="height:45px;width:45px;" class="media-img d-block">
                                <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/education.png'; ?>" alt="company logo">
                            </span>
                                                <div class="media-body border-left-0">
                                                    <h5 class="mb-1 fs-16 fw-medium"><a><?php echo $ed_post; ?></a></h5>
                                                    <p class="mb-1 fs-13 font-weight-bold"><?php echo $edu_vals['edu_specalize']; ?></p>
                                                    <p class="mb-4 lh-20 fs-13"><?php echo get_month_from_number($edu_vals['edu_start_month']).' '.$edu_vals['edu_start_year'].' - '.get_month_from_number($edu_vals['edu_end_month']).' '.$edu_vals['edu_complete_year']; ?></p>
                                                    <p class="lh-20 fs-13"><?php echo (strlen($edu_vals['edu_description'])<=200)?$edu_vals['edu_description']:mb_substr($edu_vals['edu_description'], 0, 200).'......'; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                <?php }else{ ?>
                                    <div class="mt-5 mb-2">
                                        <div class="d-flex mt-3">
                        <span style="height:45px;width:45px;" class="media-img d-block">
                            <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/education.png'; ?>" alt="company logo">
                        </span>
                                            <div class="media-body border-left-0">
                                                <div>
                                                    <h5 class="mb-1 fs-16 fw-medium float-left"><a><?php echo $ed_post; ?></a></h5>
                                                    <?php if( $user_is_logged_in && (isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken) && taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken == $ptoken)) { ?>
                                                        <a class="float-right add_edit_edu" style="cursor: pointer;" data-add-edit="Edit" data-education="<?php echo $edu_keys; ?>" data-edu-edit-delete = <?php echo $edu_vals['keys']; ?>><?= icon('edit', 'currentColor', 16) ?></a>
                                                    <?php } ?>
                                                </div><br>
                                                <p class="lh-20 fs-13 font-weight-bold"><?php echo $degree_items.', '.$edu_vals['edu_specalize']; ?></p>
                                                <p class="lh-20 fs-13"><?php echo get_month_from_number($edu_vals['edu_start_month']).' '.$edu_vals['edu_start_year'].' - '.get_month_from_number($edu_vals['edu_end_month']).' '.$edu_vals['edu_complete_year']; ?></p>
                                                <?php if($edu_vals['edu_grade'] != ''){?>
                                                    <p class="lh-20 fs-13 font-weight-bold"><span class="lh-20 fs-13">Grade: </span><?php echo $edu_vals['edu_grade']; ?></p>
                                                <?php }?>
                                                <?php if($edu_vals['edu_activities'] != ''){?>
                                                    <p class="mt-2 lh-20 fs-13"><span class="lh-20 fs-13 font-weight-bold">Activities and societies: </span><?php echo $edu_vals['edu_activities']; ?></p>
                                                <?php }?>
                                                <p class="mt-2 lh-20 fs-13"><?php echo (strlen($edu_vals['edu_description'])<=200)?$edu_vals['edu_description']:mb_substr($edu_vals['edu_description'], 0, 200).'......'; ?></p>
                                                <?php if(is_array($edu_vals['skill'])){?>
                                                    <p class="mt-2 lh-20 fs-13"><span class="lh-20 fs-13 font-weight-bold">Skills: </span><?php echo $d_items[1]; ?></p>
                                                <?php }?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } }?>
            </div>
        </section>

        <form method="post" id ="post_form" action="<?php echo TAOH_ACTION_URL .'/settings'; ?>" onsubmit="showLoading(event)">
            <div class="hidden">
                <input type="hidden" name="taoh_action" value="old_profile">
                <input type="hidden" name="taoh_ptoken" value="<?php echo $ptoken; ?>">
            </div>
            <div class="modal fade" id="add_edit_employee">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title"></h4>
                            <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                        </div>
                        <!-- Modal body -->
                        <div class="modal-body" id="emp_modal">

                        </div>
                        <!-- Modal footer -->
                        <div class="modal-footer-css">
                            <div class="emp_del_btn float-left"></div>
                            <div class="float-right"><button type="submit" class="btn btn-primary show_loader" id="emp_btnSave" name="emp_btnSave">Save</button></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="add_edit_education">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title"></h4>
                            <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                        </div>
                        <!-- Modal body -->
                        <div class="modal-body" id="edu_modal">

                        </div>
                        <!-- Modal footer -->
                        <div class="modal-footer-css">
                            <div class="edu_del_btn float-left"></div>
                            <div class="float-right"><button type="submit" class="btn btn-primary show_loader" id="edu_btnSave" name="edu_btnSave">Save</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

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

    </div>

    <script type="text/javascript">
        var user_is_logged_in = <?= json_encode(taoh_user_is_logged_in() ?? false); ?>;
        var profile_ptoken = '<?php echo $ptoken ?? ''; ?>';
        if(user_is_logged_in){
            var my_pToken = '<?php echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>';
        }

        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip();

            $('#profile_send_email_btn').on('click', function () {
                let toPtoken = $(this).data('toptoken');
                let respondPtoken = '<?php echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>';

                if(toPtoken?.trim() !== ''){
                    $('#profileOfflineMessage').val('');
                    $('#profileOfflineToPtoken').val(toPtoken);
                    $('#profileOfflineLocationPath').val('/profile/' + respondPtoken);
                    $('#profileOfflineSuccessMessage').hide();
                    $('#profileOfflineMessageBlock').show();
                    $('#profileOfflineMessageModal').modal('show');
                }
            });

            $('#profile_message_send_button').on('click', function () {
                let message = $('#profileOfflineMessage').val();
                let locationPath = $('#profileOfflineLocationPath').val();
                let toPtoken = $('#profileOfflineToPtoken').val();
                let profile_message_send_button_elem = $('#profile_message_send_button');

                if(message.trim() === ''){
                    alert('Please enter message');
                    return false;
                }

                profile_message_send_button_elem.attr('disabled', 'disabled');
                profile_message_send_button_elem.html('Sending <i class="fa fa-circle-o-notch fa-spin"></i>');
                $.post(_taoh_site_ajax_url, {
                    'taoh_action': 'taoh_post_message',
                    'message': message,
                    "ptoken": toPtoken,
                    "location_path": locationPath
                }, function (response) {
                    $('#profileOfflineMessage').val('');
                    $('#profileOfflineMessageBlock').hide();
                    $('#profileOfflineSuccessMessage').show();
                    setTimeout(function () {
                        profile_message_send_button_elem.removeAttr('disabled');
                        profile_message_send_button_elem.text('Send');
                        $('#profileOfflineMessageModal').modal('hide');
                    }, 1500);
                });

                if ($('#privateChatForm').length > 0) {
                    $('#pc_message').val(message);
                    $('#pc_send_btn').trigger('click');
                }
            });
        });

        function showLoading(event) {
            event.preventDefault();
            // Get the submit button that was clicked
            const submitButton = event.submitter;
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

            // Create a hidden input to hold the button name and value
            const hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = submitButton.name;
            document.getElementById("post_form").appendChild(hiddenInput);

            // Submit the form
            setTimeout(function () {
                document.getElementById("post_form").submit();
            }, 1000); // Delay submission for demonstration purposes
        }

        $(document).on('click', '.keywords-link', function () {
            let current_elem = $(this);
            let keyword_key = current_elem.data('keyword_key');
            let keyword_value = current_elem.data('keyword_value');

            if(user_is_logged_in){
                const formData = {
                    taoh_action: 'get_keywords_room',
                    room_type: 'keyword',
                    keyword_key: keyword_key,
                    keyword_value: keyword_value
                };

                current_elem.html(keyword_value + ' <i class="fa fa-spinner fa-spin"></i>');
                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            let room_data = response.room_info;

                            if (room_data['club']['links']['club']) {
                                window.location.href = _taoh_site_url_root + room_data['club']['links']['club'];
                            } else {
                                current_elem.text(keyword_value);
                                alert('Room not found');
                            }
                        } else {
                            current_elem.text(keyword_value);
                            alert('Room not found');
                        }
                    }
                });
            }
        });

        $(document).on('click', '.current_role_checklabel', function () {
            if ($('.current_role_checkbox').is(':checked')) {
                $('.current_role_checkbox').prop('checked', false);
            } else {
                $('.current_role_checkbox').prop('checked', true);
            }
        });

        $(document).on('click', '.add_edit_emp', function () {
            var data_emp = $(this).attr("data-employee");
            var emp_add_edit = $(this).attr("data-add-edit");
            var emp_delete = $(this).attr("data-emp-delete");
            var emp_edt_delete = $(this).attr("data-emp-edit-delete");
            $('#add_edit_employee .modal-title').html(emp_add_edit + ' Experience');
            $('#add_edit_employee .modal-body').html('');
            $('#add_edit_employee .emp_del_btn').html('');
            $('#add_edit_employee').modal('show');
            $('#add_edit_employee .modal-body').html('<img class="loader" style="width:10% !important; display: block; background: none; top: auto; height:auto;" src="<?php echo TAOH_LOADER_GIF; ?>" />');
            var data = {
                'taoh_action': 'add_edit_employee',
                'id': data_emp,
                'emp_edit_del_id': emp_edt_delete,
                'add_or_edit': emp_add_edit,
                'post_data': '<?php echo json_encode($emp_list); ?>',
            };
            jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function (response) {
                res = response.split('~');
                console.log(res[1]);
                $('#add_edit_employee .modal-body').html(res[0]);
                $('#add_edit_employee .emp_del_btn').html(res[1]);
            }).fail(function () {
                console.log("Network issue!");

            })
        });

        $(document).on('click', '.add_edit_edu', function () {
            var data_edu = $(this).attr("data-education");
            var edu_add_edit = $(this).attr("data-add-edit");
            var edu_delete = $(this).attr("data-edu-delete");
            var edu_edt_delete = $(this).attr("data-edu-edit-delete");
            $('#add_edit_education .modal-title').html(edu_add_edit + ' Education');
            $('#add_edit_education .modal-body').html('');
            $('#add_edit_education .edu_del_btn').html('');
            $('#add_edit_education').modal('show');
            $('#add_edit_education .modal-body').html('<img class="loader" style="width:10% !important; display: block; background: none; top: auto; height:auto;" src="<?php echo TAOH_LOADER_GIF; ?>" />');
            var data = {
                'taoh_action': 'add_edit_education',
                'id': data_edu,
                'edu_edit_del_id': edu_edt_delete,
                'add_or_edit': edu_add_edit,
                'post_data': '<?php echo json_encode($edu_list); ?>',
            };
            jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function (response) {
                res = response.split('~');
                console.log(res[1]);
                $('#add_edit_education .modal-body').html(res[0]);
                $('#add_edit_education .edu_del_btn').html(res[1]);
            }).fail(function () {
                console.log("Network issue!");

            })
        });

        $(document).on('change', "#emp_year_starts", function (e) {
            var emp_startYear = $(this).children(':selected').val();
            $('#emp_hidden_end').children().appendTo('#emp_year_ends');
            $('#emp_year_ends option').each(function () {
                if ($(this).val() < emp_startYear) $(this).appendTo('#emp_hidden_end');
            })
            var emp_options = $('#emp_year_ends option').sort(function (emp_a, emp_b) {
                return (emp_a.value > emp_b.value) ? -1 : 1
            });
            emp_options.appendTo($('#emp_year_ends'));
            $('#emp_year_ends option:selected').removeAttr('selected');
            $('#emp_year_ends option:first-child').attr('selected', 'selected')
        });

        $(document).on('change', "#edu_year_starts", function (e) {
            var startYear = $(this).children(':selected').val();
            $('#edu_hidden_end').children().appendTo('#edu_year_ends');
            $('#edu_year_ends option').each(function () {
                if ($(this).val() < startYear) $(this).appendTo('#edu_hidden_end');
            })
            var options = $('#edu_year_ends option').sort(function (a, b) {
                return (a.value > b.value) ? -1 : 1
            });
            options.appendTo($('#edu_year_ends'));
            $('#edu_year_ends option:selected').removeAttr('selected');
            $('#edu_year_ends option:first-child').attr('selected', 'selected')
        });
    </script>

<?php
taoh_get_footer();