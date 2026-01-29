<?php
if (strpos($_SERVER['REQUEST_URI'], 'ajax') === false) {
    taoh_add_var_to_url('noca', TAOH_MY_NOW_CODE);
}
taoh_get_header();
$ptoken = taoh_parse_url(1);
//$api = 'https://preapi.tao.ai/users.user.get?mod=taoai&token=hT93oaWC&ops=info&ptoken=oyeuyy9vnx1u';
//echo"<pre>"; print_r(get_explode_names(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->skill));die();
//$taoh_user_vars = taoh_user_all_info();
/* Get User Info */
//echo "========";
$return = taoh_get_user_info($ptoken,'full',1);
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

        @media (min-width: 1280px) {
            .container {
                max-width: 1021px;
            }
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

.custom-control-label::after {
            top: 2px; /* Fine-tune checkbox position */
    left: -26px; /* Align with label */
    width: 20px; /* Checkbox width */
    height: 20px; /* Checkbox height */
        }
    </style>

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


<!-- html new template profile start -->
<!-- <section class="blog-area py-5" style="background: #ffffff">
  <div class="container">
    <h2 class="" style="font-size: 33px; font-weight: 500;">My Profile</h2>

    <div class="mx-1 my-4 p-3 shadow  px-md-4 py-md-5" style="border: 1.6px solid #D3D3D3; border-radius: 8px;">
        <div class="row mx-0 d-flex flex-wrap-reverse">
            <div class="col-md-10 d-flex flex-wrap align-items-center" style="gap: 2.8rem">
                <div class="">
                    <img class=""  src="<?php echo $avatar_image;?>" alt="" style="width: 9rem; height: 9rem; border-radius: 50%;">
                </div>

                <div>
                    <div class="d-flex flex-wrap align-items-center">
                        <h1 class="d-flex align-items-center py-3"><span class="text-black mr-4" style="font-size: 33px; font-weight: 500;"><?php echo $data['output']['user']['chat_name'];?></span></h1>
                        <p><span class="prof-link px-4 text-capitalize" style="border-radius: 12px; font-size: 21px; background: #6DC0CB9C; color: #000000;"><?php echo $data['output']['user']['type'];?></span></p>
                    </div>

                    <div>
                        <h2 style="color: #727272; font-size: 29px; font-weight: 400;">Job Title</h2>
                        <h4 style="color: #727272; font-size: 21px; font-weight: 400;"><?php echo $data['output']['user']['full_location'];?></h4>
                    </div>
                </div>
            </div>

            <div class="col-md-2 d-flex justify-content-end ">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M26 0H1.99375C0.89375 0 0 0.90625 0 2.01875V25.9813C0 27.0938 0.89375 28 1.99375 28H26C27.1 28 28 27.0938 28 25.9813V2.01875C28 0.90625 27.1 0 26 0ZM8.4625 24H4.3125V10.6375H8.46875V24H8.4625ZM6.3875 8.8125C5.05625 8.8125 3.98125 7.73125 3.98125 6.40625C3.98125 5.08125 5.05625 4 6.3875 4C7.7125 4 8.79375 5.08125 8.79375 6.40625C8.79375 7.7375 7.71875 8.8125 6.3875 8.8125ZM24.0187 24H19.8687V17.5C19.8687 15.95 19.8375 13.9563 17.7125 13.9563C15.55 13.9563 15.2188 15.6438 15.2188 17.3875V24H11.0688V10.6375H15.05V12.4625H15.1062C15.6625 11.4125 17.0188 10.3062 19.0375 10.3062C23.2375 10.3062 24.0187 13.075 24.0187 16.675V24Z" fill="black"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="mx-1 my-4 p-3 shadow  px-md-4 py-md-5" style="border: 1.6px solid #D3D3D3; border-radius: 8px;">
        <div class="row mx-0 d-flex flex-wrap-reverse mb-4">
            <div class="col-md-10">
                <h1 style="font-size: 33px;"> Personal Information</h1>
            </div>

            <div class="col-md-2 d-flex align-items-center justify-content-end">
                <a href="#" class="btn py-0 d-inline-flex align-items-center" style="border: 1px solid #000000; font-size: 16px; gap: 0.5rem;">
                    <span>Edit</span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.8909 0.518624C14.1994 -0.172875 13.0817 -0.172875 12.3902 0.518624L11.4397 1.46588L14.531 4.55711L15.4814 3.60669C16.1729 2.91519 16.1729 1.79742 15.4814 1.10593L14.8909 0.518624ZM5.44358 7.46519C5.25097 7.6578 5.10257 7.89462 5.01732 8.15669L4.08269 10.9606C3.99112 11.2321 4.06374 11.5321 4.26583 11.7373C4.46791 11.9426 4.76787 12.012 5.04258 11.9205L7.84647 10.9858C8.10538 10.9006 8.3422 10.7522 8.53796 10.5596L13.8205 5.27387L10.7261 2.17949L5.44358 7.46519ZM3.03123 1.85426C1.35774 1.85426 0 3.212 0 4.88549V12.9688C0 14.6423 1.35774 16 3.03123 16H11.1145C12.788 16 14.1457 14.6423 14.1457 12.9688V9.93754C14.1457 9.37866 13.6942 8.92713 13.1353 8.92713C12.5764 8.92713 12.1249 9.37866 12.1249 9.93754V12.9688C12.1249 13.5277 11.6734 13.9792 11.1145 13.9792H3.03123C2.47235 13.9792 2.02082 13.5277 2.02082 12.9688V4.88549C2.02082 4.32661 2.47235 3.87508 3.03123 3.87508H6.06246C6.62134 3.87508 7.07287 3.42355 7.07287 2.86467C7.07287 2.30579 6.62134 1.85426 6.06246 1.85426H3.03123Z" fill="black"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="row mx-0">
            <div class="col-md-6 mb-4">
                <h6 class="d-flex align-items-center" style="gap: 6px;">
                    <svg width="18" height="21" viewBox="0 0 18 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 10.5C10.364 10.5 11.6721 9.94688 12.6365 8.96231C13.601 7.97775 14.1429 6.64239 14.1429 5.25C14.1429 3.85761 13.601 2.52226 12.6365 1.53769C11.6721 0.553123 10.364 0 9 0C7.63603 0 6.32792 0.553123 5.36345 1.53769C4.39898 2.52226 3.85714 3.85761 3.85714 5.25C3.85714 6.64239 4.39898 7.97775 5.36345 8.96231C6.32792 9.94688 7.63603 10.5 9 10.5ZM7.16384 12.4688C3.20625 12.4688 0 15.7418 0 19.7818C0 20.4545 0.534375 21 1.1933 21H16.8067C17.4656 21 18 20.4545 18 19.7818C18 15.7418 14.7938 12.4688 10.8362 12.4688H7.16384Z" fill="#ABABAB"/>
                    </svg>

                    <span style="color: #727272; font-size: 21px;">First Name</span>
                </h6>
                <p class="mt-3" style="font-size: 26px; color: #000000;">
                    <?php echo $data['output']['user']['chat_name'];?>
                </p>
            </div>
            <div class="col-md-6 mb-4">
                <h6 class="d-flex align-items-center" style="gap: 6px;">
                    <svg width="18" height="21" viewBox="0 0 18 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 10.5C10.364 10.5 11.6721 9.94688 12.6365 8.96231C13.601 7.97775 14.1429 6.64239 14.1429 5.25C14.1429 3.85761 13.601 2.52226 12.6365 1.53769C11.6721 0.553123 10.364 0 9 0C7.63603 0 6.32792 0.553123 5.36345 1.53769C4.39898 2.52226 3.85714 3.85761 3.85714 5.25C3.85714 6.64239 4.39898 7.97775 5.36345 8.96231C6.32792 9.94688 7.63603 10.5 9 10.5ZM7.16384 12.4688C3.20625 12.4688 0 15.7418 0 19.7818C0 20.4545 0.534375 21 1.1933 21H16.8067C17.4656 21 18 20.4545 18 19.7818C18 15.7418 14.7938 12.4688 10.8362 12.4688H7.16384Z" fill="#ABABAB"/>
                    </svg>

                    <span style="color: #727272; font-size: 21px;">Last Name</span>
                </h6>
                <p class="mt-3" style="font-size: 26px; color: #000000;">
                    <?php echo $data['output']['user']['chat_name'];?>
                </p>
            </div>
            <div class="col-md-6 mb-4">
                <h6 class="d-flex align-items-center" style="gap: 6px;">
                    <svg width="24" height="18" viewBox="0 0 24 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.25 0C1.00781 0 0 1.00781 0 2.25C0 2.95781 0.332813 3.62344 0.9 4.05L11.1 11.7C11.6344 12.0984 12.3656 12.0984 12.9 11.7L23.1 4.05C23.6672 3.62344 24 2.95781 24 2.25C24 1.00781 22.9922 0 21.75 0H2.25ZM0 5.25V15C0 16.6547 1.34531 18 3 18H21C22.6547 18 24 16.6547 24 15V5.25L13.8 12.9C12.7312 13.7016 11.2688 13.7016 10.2 12.9L0 5.25Z" fill="#ABABAB"/>
                    </svg>


                    <span style="color: #727272; font-size: 21px;">Email Address</span>
                </h6>
                <p class="mt-3" style="font-size: 26px; color: #000000;">
                    mail@mail.com
                </p>
            </div>
            <div class="col-md-6 mb-4">
                <h6 class="d-flex align-items-center" style="gap: 6px;">
                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3.14286 0C1.40937 0 0 1.40937 0 3.14286V18.8571C0 20.5906 1.40937 22 3.14286 22H18.8571C20.5906 22 22 20.5906 22 18.8571V3.14286C22 1.40937 20.5906 0 18.8571 0H3.14286ZM7.59687 4.74866C8.07321 4.62098 8.57411 4.86161 8.76071 5.3183L9.74286 7.67545C9.90982 8.07812 9.79196 8.53973 9.45804 8.81473L8.25 9.8067C9.06518 11.5353 10.4647 12.9348 12.1933 13.75L13.1853 12.5371C13.4603 12.2031 13.9219 12.0853 14.3246 12.2522L16.6817 13.2344C17.1384 13.4259 17.379 13.9219 17.2513 14.3982L16.6621 16.5589C16.5442 16.9911 16.1562 17.2857 15.7143 17.2857C9.63973 17.2857 4.71429 12.3603 4.71429 6.28571C4.71429 5.84375 5.00893 5.4558 5.43616 5.33795L7.59687 4.74866Z" fill="#ABABAB"/>
                    </svg>


                    <span style="color: #727272; font-size: 21px;">Phone</span>
                </h6>
                <p class="mt-3" style="font-size: 26px; color: #000000;">
                    9999999999
                </p>
            </div>
            <div class="col-12 mb-4">
                <h6 class="d-flex align-items-center" style="gap: 6px;">
                    <svg width="26" height="21" viewBox="0 0 26 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11.5556 0H14.4444C15.2434 0 15.8889 0.586523 15.8889 1.3125V3.9375C15.8889 4.66348 15.2434 5.25 14.4444 5.25H11.5556C10.7566 5.25 10.1111 4.66348 10.1111 3.9375V1.3125C10.1111 0.586523 10.7566 0 11.5556 0ZM2.88889 2.625H8.66667V4.59375C8.66667 5.68066 9.63715 6.5625 10.8333 6.5625H15.1667C16.3628 6.5625 17.3333 5.68066 17.3333 4.59375V2.625H23.1111C24.7045 2.625 26 3.80215 26 5.25V18.375C26 19.8229 24.7045 21 23.1111 21H2.88889C1.29549 21 0 19.8229 0 18.375V5.25C0 3.80215 1.29549 2.625 2.88889 2.625ZM7.94444 17.9361C7.94444 18.1781 8.16111 18.375 8.42743 18.375H17.5726C17.8389 18.375 18.0556 18.1781 18.0556 17.9361C18.0556 16.7262 16.9767 15.75 15.6497 15.75H10.3503C9.01875 15.75 7.94444 16.7303 7.94444 17.9361ZM13 14.4375C13.7662 14.4375 14.501 14.1609 15.0428 13.6687C15.5845 13.1764 15.8889 12.5087 15.8889 11.8125C15.8889 11.1163 15.5845 10.4486 15.0428 9.95634C14.501 9.46406 13.7662 9.1875 13 9.1875C12.2338 9.1875 11.499 9.46406 10.9572 9.95634C10.4155 10.4486 10.1111 11.1163 10.1111 11.8125C10.1111 12.5087 10.4155 13.1764 10.9572 13.6687C11.499 14.1609 12.2338 14.4375 13 14.4375Z" fill="#ABABAB"/>
                    </svg>


                    <span style="color: #727272; font-size: 21px;">About</span>
                </h6>
                <p class="mt-3" style="font-size: 18px; color: #000000; text-align: justify;">
                    <?php if(!empty($about_me)){ ?>
                        <?php if (!$user_is_logged_in) { ?>
                            <a href="#" data-toggle="tooltip" data-placement="top" title="Login to see the full details!">
                                <div class="mt-2 mb-2" id="loading">
                                    <?php echo $about_me.'......'; ?>
                                </div>
                            </a>
                        <?php }else{ ?>
                                <?php echo $about_me; ?>
                        <?php } ?>
                    <?php } ?>
                </p>
            </div>
            <div class="col-12 mb-4">
                <h6 class="d-flex align-items-center" style="gap: 6px;">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 20C12.6522 20 15.1957 18.9464 17.0711 17.0711C18.9464 15.1957 20 12.6522 20 10C20 7.34784 18.9464 4.8043 17.0711 2.92893C15.1957 1.05357 12.6522 0 10 0C7.34784 0 4.8043 1.05357 2.92893 2.92893C1.05357 4.8043 0 7.34784 0 10C0 12.6522 1.05357 15.1957 2.92893 17.0711C4.8043 18.9464 7.34784 20 10 20ZM6.41016 12.7148C7.10938 13.5234 8.30469 14.375 10 14.375C11.6953 14.375 12.8906 13.5234 13.5898 12.7148C13.8164 12.4531 14.2109 12.4258 14.4727 12.6523C14.7344 12.8789 14.7617 13.2734 14.5352 13.5352C13.6641 14.5352 12.1523 15.625 10 15.625C7.84766 15.625 6.33594 14.5352 5.46484 13.5352C5.23828 13.2734 5.26562 12.8789 5.52734 12.6523C5.78906 12.4258 6.18359 12.4531 6.41016 12.7148ZM5.64062 8.125C5.64062 7.79348 5.77232 7.47554 6.00674 7.24112C6.24116 7.0067 6.5591 6.875 6.89062 6.875C7.22215 6.875 7.54009 7.0067 7.77451 7.24112C8.00893 7.47554 8.14062 7.79348 8.14062 8.125C8.14062 8.45652 8.00893 8.77446 7.77451 9.00888C7.54009 9.2433 7.22215 9.375 6.89062 9.375C6.5591 9.375 6.24116 9.2433 6.00674 9.00888C5.77232 8.77446 5.64062 8.45652 5.64062 8.125ZM11.75 9.125C11.543 9.40234 11.1523 9.45703 10.875 9.25C10.5977 9.04297 10.543 8.65234 10.75 8.375C11.9375 6.79297 14.3125 6.79297 15.5 8.375C15.707 8.65234 15.6523 9.04297 15.375 9.25C15.0977 9.45703 14.707 9.40234 14.5 9.125C13.8125 8.20703 12.4375 8.20703 11.75 9.125Z" fill="#ABABAB"/>
                    </svg>

                    <span style="color: #727272; font-size: 21px;">Fun Fact</span>
                </h6>
                <p class="mt-3" style="font-size: 18px; color: #000000; text-align: justify;">
                    <?php if(!empty($fun_fact)){ ?>
                        <?php if (!$user_is_logged_in) { ?>
                            <a href="#" data-toggle="tooltip" data-placement="top" title="Login to see the full details!">
                                <div class="mt-2 mb-2" id="loading">
                                    <?php echo $fun_fact.'......'; ?>
                                </div>
                            </a>
                        <?php }else{ ?>

                                <?php echo $fun_fact; ?>

                        <?php } ?>
                    <?php } ?>
                </p>
            </div>
        </div>


    </div>

    <div class="mx-1 my-4 p-3 shadow  px-md-4 py-md-5" style="border: 1.6px solid #D3D3D3; border-radius: 8px;">
        <div class="row mx-0 d-flex flex-wrap-reverse mb-4">
            <div class="col-md-10">
                <h1 style="font-size: 33px"> Location</h1>
            </div>

            <div class="col-md-2 d-flex align-items-center justify-content-end">
                <a href="#" class="btn py-0 d-inline-flex align-items-center" style="border: 1px solid #000000; font-size: 16px; gap: 0.5rem;">
                    <span>Edit</span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.8909 0.518624C14.1994 -0.172875 13.0817 -0.172875 12.3902 0.518624L11.4397 1.46588L14.531 4.55711L15.4814 3.60669C16.1729 2.91519 16.1729 1.79742 15.4814 1.10593L14.8909 0.518624ZM5.44358 7.46519C5.25097 7.6578 5.10257 7.89462 5.01732 8.15669L4.08269 10.9606C3.99112 11.2321 4.06374 11.5321 4.26583 11.7373C4.46791 11.9426 4.76787 12.012 5.04258 11.9205L7.84647 10.9858C8.10538 10.9006 8.3422 10.7522 8.53796 10.5596L13.8205 5.27387L10.7261 2.17949L5.44358 7.46519ZM3.03123 1.85426C1.35774 1.85426 0 3.212 0 4.88549V12.9688C0 14.6423 1.35774 16 3.03123 16H11.1145C12.788 16 14.1457 14.6423 14.1457 12.9688V9.93754C14.1457 9.37866 13.6942 8.92713 13.1353 8.92713C12.5764 8.92713 12.1249 9.37866 12.1249 9.93754V12.9688C12.1249 13.5277 11.6734 13.9792 11.1145 13.9792H3.03123C2.47235 13.9792 2.02082 13.5277 2.02082 12.9688V4.88549C2.02082 4.32661 2.47235 3.87508 3.03123 3.87508H6.06246C6.62134 3.87508 7.07287 3.42355 7.07287 2.86467C7.07287 2.30579 6.62134 1.85426 6.06246 1.85426H3.03123Z" fill="black"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="row mx-0">
            <div class="col-md-6 mb-4">
                <h6 class="d-flex align-items-center" style="gap: 6px;">
                    <svg width="15" height="20" viewBox="0 0 15 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 7.51946C15 10.9424 10.4297 17.0363 8.42578 19.5506C7.94531 20.1498 7.05469 20.1498 6.57422 19.5506C4.57031 17.0363 0 10.9424 0 7.51946C0 3.36809 3.35938 0 7.5 0C11.6406 0 15 3.36809 15 7.51946Z" fill="#ABABAB"/>
                    </svg>


                    <span style="color: #727272; font-size: 21px;">City</span>
                </h6>
                <p class="mt-3" style="font-size: 26px; color: #000000;">
                    <?php echo $data['output']['user']['full_location'];?>
                </p>
            </div>

            <div class="col-md-6 mb-4">
                <h6 class="d-flex align-items-center" style="gap: 6px;">
                    <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11.5 0C14.55 0 17.4751 1.2116 19.6317 3.36827C21.7884 5.52494 23 8.45001 23 11.5C23 14.55 21.7884 17.4751 19.6317 19.6317C17.4751 21.7884 14.55 23 11.5 23C8.45001 23 5.52494 21.7884 3.36827 19.6317C1.2116 17.4751 0 14.55 0 11.5C0 8.45001 1.2116 5.52494 3.36827 3.36827C5.52494 1.2116 8.45001 0 11.5 0ZM10.4219 5.39062V11.5C10.4219 11.8594 10.6016 12.1963 10.9025 12.3984L15.215 15.2734C15.7092 15.6059 16.3785 15.4711 16.7109 14.9725C17.0434 14.4738 16.9086 13.809 16.41 13.4766L12.5781 10.925V5.39062C12.5781 4.79316 12.0975 4.3125 11.5 4.3125C10.9025 4.3125 10.4219 4.79316 10.4219 5.39062Z" fill="#ABABAB"/>
                    </svg>

                    <span style="color: #727272; font-size: 21px;">Time Zone</span>
                </h6>
                <p class="mt-3" style="font-size: 26px; color: #000000;">
                    America/ New_York
                </p>
            </div>

        </div>
    </div>

    <div class="mx-1 my-4 p-3 shadow px-md-4 py-md-5" style="border: 1.6px solid #D3D3D3; border-radius: 8px;">
        <div class="row mx-0 d-flex flex-wrap-reverse mb-4">
            <div class="col-md-10">
                <h1 style="font-size: 33px"> Core Skills</h1>
            </div>

            <div class="col-md-2 d-flex align-items-center justify-content-end">
                <a href="#" class="btn py-0 d-inline-flex align-items-center" style="border: 1px solid #000000; font-size: 16px; gap: 0.5rem;">
                    <span>Edit</span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.8909 0.518624C14.1994 -0.172875 13.0817 -0.172875 12.3902 0.518624L11.4397 1.46588L14.531 4.55711L15.4814 3.60669C16.1729 2.91519 16.1729 1.79742 15.4814 1.10593L14.8909 0.518624ZM5.44358 7.46519C5.25097 7.6578 5.10257 7.89462 5.01732 8.15669L4.08269 10.9606C3.99112 11.2321 4.06374 11.5321 4.26583 11.7373C4.46791 11.9426 4.76787 12.012 5.04258 11.9205L7.84647 10.9858C8.10538 10.9006 8.3422 10.7522 8.53796 10.5596L13.8205 5.27387L10.7261 2.17949L5.44358 7.46519ZM3.03123 1.85426C1.35774 1.85426 0 3.212 0 4.88549V12.9688C0 14.6423 1.35774 16 3.03123 16H11.1145C12.788 16 14.1457 14.6423 14.1457 12.9688V9.93754C14.1457 9.37866 13.6942 8.92713 13.1353 8.92713C12.5764 8.92713 12.1249 9.37866 12.1249 9.93754V12.9688C12.1249 13.5277 11.6734 13.9792 11.1145 13.9792H3.03123C2.47235 13.9792 2.02082 13.5277 2.02082 12.9688V4.88549C2.02082 4.32661 2.47235 3.87508 3.03123 3.87508H6.06246C6.62134 3.87508 7.07287 3.42355 7.07287 2.86467C7.07287 2.30579 6.62134 1.85426 6.06246 1.85426H3.03123Z" fill="black"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="row mx-0">
            <div class="col-12">
                <?php if(!empty($get_skill)){ ?>

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

                            <?php foreach($get_skill as $keys => $vals){
                                if(!empty($vals['value'])){?>
                                <span class="skill-link px-5 py-2" style="font-size: 20px; background: #F2F1F1; border: 2px solid #D3D3D3;"><?php echo $vals['value']; ?></span>
                            <?php } } ?>

                    <?php } ?>

                <?php } ?>
            </div>
        </div>
    </div>

    <div class="mx-1 my-4 p-3 shadow px-md-4 py-md-5" style="border: 1.6px solid #D3D3D3; border-radius: 8px;">
        <div class="row mx-0 d-flex flex-wrap-reverse mb-4">
            <div class="col-md-10">
                <h1 style="font-size: 33px"> Professional Experience</h1>
            </div>

            <div class="col-md-2 d-flex align-items-center justify-content-end">
                <a href="#" class="btn py-0 d-inline-flex align-items-center" style="border: 1px solid #000000; font-size: 16px; gap: 0.5rem;">
                    <span>Edit</span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.8909 0.518624C14.1994 -0.172875 13.0817 -0.172875 12.3902 0.518624L11.4397 1.46588L14.531 4.55711L15.4814 3.60669C16.1729 2.91519 16.1729 1.79742 15.4814 1.10593L14.8909 0.518624ZM5.44358 7.46519C5.25097 7.6578 5.10257 7.89462 5.01732 8.15669L4.08269 10.9606C3.99112 11.2321 4.06374 11.5321 4.26583 11.7373C4.46791 11.9426 4.76787 12.012 5.04258 11.9205L7.84647 10.9858C8.10538 10.9006 8.3422 10.7522 8.53796 10.5596L13.8205 5.27387L10.7261 2.17949L5.44358 7.46519ZM3.03123 1.85426C1.35774 1.85426 0 3.212 0 4.88549V12.9688C0 14.6423 1.35774 16 3.03123 16H11.1145C12.788 16 14.1457 14.6423 14.1457 12.9688V9.93754C14.1457 9.37866 13.6942 8.92713 13.1353 8.92713C12.5764 8.92713 12.1249 9.37866 12.1249 9.93754V12.9688C12.1249 13.5277 11.6734 13.9792 11.1145 13.9792H3.03123C2.47235 13.9792 2.02082 13.5277 2.02082 12.9688V4.88549C2.02082 4.32661 2.47235 3.87508 3.03123 3.87508H6.06246C6.62134 3.87508 7.07287 3.42355 7.07287 2.86467C7.07287 2.30579 6.62134 1.85426 6.06246 1.85426H3.03123Z" fill="black"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="row mx-0">
            <div class="col-md-6 mb-4">
                <h6 class="d-flex align-items-center" style="gap: 6px;">
                    <svg width="32" height="29" viewBox="0 0 32 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.2222 0H17.7778C18.7611 0 19.5556 0.794444 19.5556 1.77778V5.33333C19.5556 6.31667 18.7611 7.11111 17.7778 7.11111H14.2222C13.2389 7.11111 12.4444 6.31667 12.4444 5.33333V1.77778C12.4444 0.794444 13.2389 0 14.2222 0ZM3.55556 3.55556H10.6667V6.22222C10.6667 7.69444 11.8611 8.88889 13.3333 8.88889H18.6667C20.1389 8.88889 21.3333 7.69444 21.3333 6.22222V3.55556H28.4444C30.4056 3.55556 32 5.15 32 7.11111V24.8889C32 26.85 30.4056 28.4444 28.4444 28.4444H3.55556C1.59444 28.4444 0 26.85 0 24.8889V7.11111C0 5.15 1.59444 3.55556 3.55556 3.55556ZM9.77778 24.2944C9.77778 24.6222 10.0444 24.8889 10.3722 24.8889H21.6278C21.9556 24.8889 22.2222 24.6222 22.2222 24.2944C22.2222 22.6556 20.8944 21.3333 19.2611 21.3333H12.7389C11.1 21.3333 9.77778 22.6611 9.77778 24.2944ZM16 19.5556C16.943 19.5556 17.8474 19.181 18.5142 18.5142C19.181 17.8474 19.5556 16.943 19.5556 16C19.5556 15.057 19.181 14.1526 18.5142 13.4858C17.8474 12.819 16.943 12.4444 16 12.4444C15.057 12.4444 14.1526 12.819 13.4858 13.4858C12.819 14.1526 12.4444 15.057 12.4444 16C12.4444 16.943 12.819 17.8474 13.4858 18.5142C14.1526 19.181 15.057 19.5556 16 19.5556Z" fill="#D3D3D3"/>
                    </svg>
                    <span style="color: #727272; font-size: 21px;">Current or Last Job Role</span>
                </h6>
                <p class="mt-3" style="font-size: 26px; color: #000000;">

                </p>
            </div>
            <div class="col-md-6 mb-4">
                <h6 class="d-flex align-items-center" style="gap: 6px;">
                    <svg width="32" height="25" viewBox="0 0 32 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3.55556 0C1.59444 0 0 1.59444 0 3.55556V21.3333C0 23.2944 1.59444 24.8889 3.55556 24.8889H28.4444C30.4056 24.8889 32 23.2944 32 21.3333V3.55556C32 1.59444 30.4056 0 28.4444 0H3.55556ZM8 14.2222H11.5556C14.0111 14.2222 16 16.2111 16 18.6667C16 19.1556 15.6 19.5556 15.1111 19.5556H4.44444C3.95556 19.5556 3.55556 19.1556 3.55556 18.6667C3.55556 16.2111 5.54444 14.2222 8 14.2222ZM6.22222 8.88889C6.22222 7.9459 6.59682 7.04153 7.26362 6.37473C7.93042 5.70794 8.83479 5.33333 9.77778 5.33333C10.7208 5.33333 11.6251 5.70794 12.2919 6.37473C12.9587 7.04153 13.3333 7.9459 13.3333 8.88889C13.3333 9.83188 12.9587 10.7363 12.2919 11.403C11.6251 12.0698 10.7208 12.4444 9.77778 12.4444C8.83479 12.4444 7.93042 12.0698 7.26362 11.403C6.59682 10.7363 6.22222 9.83188 6.22222 8.88889ZM20.4444 7.11111H27.5556C28.0444 7.11111 28.4444 7.51111 28.4444 8C28.4444 8.48889 28.0444 8.88889 27.5556 8.88889H20.4444C19.9556 8.88889 19.5556 8.48889 19.5556 8C19.5556 7.51111 19.9556 7.11111 20.4444 7.11111ZM20.4444 10.6667H27.5556C28.0444 10.6667 28.4444 11.0667 28.4444 11.5556C28.4444 12.0444 28.0444 12.4444 27.5556 12.4444H20.4444C19.9556 12.4444 19.5556 12.0444 19.5556 11.5556C19.5556 11.0667 19.9556 10.6667 20.4444 10.6667ZM20.4444 14.2222H27.5556C28.0444 14.2222 28.4444 14.6222 28.4444 15.1111C28.4444 15.6 28.0444 16 27.5556 16H20.4444C19.9556 16 19.5556 15.6 19.5556 15.1111C19.5556 14.6222 19.9556 14.2222 20.4444 14.2222Z" fill="#D3D3D3"/>
                    </svg>

                    <span style="color: #727272; font-size: 21px;">Current or Last Company</span>
                </h6>
                <p class="mt-3" style="font-size: 26px; color: #000000;">

                </p>
            </div>

        </div>

    </div>
  </div>
</section> -->
<!-- html new template profile end -->

<section class="blog-area pt-80px pb-80px">
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
                            <span class="prof-link text-capitalize"><?php echo !empty($data['output']['user']['type']) ? $data['output']['user']['type'] : 'professional'; ?></span>
                            <div class="mt-1"><?php echo $data['output']['user']['full_location'];?></div>
                            <div class="mt-1">
                                                <svg width="8" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1 0C0.447917 0 0 0.461914 0 1.03125V9.96875C0 10.5381 0.447917 11 1 11H3V9.28125C3 8.71191 3.44792 8.25 4 8.25C4.55208 8.25 5 8.71191 5 9.28125V11H7C7.55208 11 8 10.5381 8 9.96875V1.03125C8 0.461914 7.55208 0 7 0H1ZM1.33333 5.15625C1.33333 4.96719 1.48333 4.8125 1.66667 4.8125H2.33333C2.51667 4.8125 2.66667 4.96719 2.66667 5.15625V5.84375C2.66667 6.03281 2.51667 6.1875 2.33333 6.1875H1.66667C1.48333 6.1875 1.33333 6.03281 1.33333 5.84375V5.15625ZM3.66667 4.8125H4.33333C4.51667 4.8125 4.66667 4.96719 4.66667 5.15625V5.84375C4.66667 6.03281 4.51667 6.1875 4.33333 6.1875H3.66667C3.48333 6.1875 3.33333 6.03281 3.33333 5.84375V5.15625C3.33333 4.96719 3.48333 4.8125 3.66667 4.8125ZM5.33333 5.15625C5.33333 4.96719 5.48333 4.8125 5.66667 4.8125H6.33333C6.51667 4.8125 6.66667 4.96719 6.66667 5.15625V5.84375C6.66667 6.03281 6.51667 6.1875 6.33333 6.1875H5.66667C5.48333 6.1875 5.33333 6.03281 5.33333 5.84375V5.15625ZM1.66667 2.0625H2.33333C2.51667 2.0625 2.66667 2.21719 2.66667 2.40625V3.09375C2.66667 3.28281 2.51667 3.4375 2.33333 3.4375H1.66667C1.48333 3.4375 1.33333 3.28281 1.33333 3.09375V2.40625C1.33333 2.21719 1.48333 2.0625 1.66667 2.0625ZM3.33333 2.40625C3.33333 2.21719 3.48333 2.0625 3.66667 2.0625H4.33333C4.51667 2.0625 4.66667 2.21719 4.66667 2.40625V3.09375C4.66667 3.28281 4.51667 3.4375 4.33333 3.4375H3.66667C3.48333 3.4375 3.33333 3.28281 3.33333 3.09375V2.40625ZM5.66667 2.0625H6.33333C6.51667 2.0625 6.66667 2.21719 6.66667 2.40625V3.09375C6.66667 3.28281 6.51667 3.4375 6.33333 3.4375H5.66667C5.48333 3.4375 5.33333 3.28281 5.33333 3.09375V2.40625C5.33333 2.21719 5.48333 2.0625 5.66667 2.0625Z" fill="#636161"></path>
                                                </svg>
                                                <?php echo displayCompanyName($data['output']['user']['company']);?>
                                        <span style="margin-left:20px">&nbsp;</span>
                                             <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0 0V10H10V0H0ZM7.06473 7.25893L5 9.23884L2.93527 7.25893L4.375 3.15179L2.93527 1.21875H7.0625L5.625 3.15179L7.06473 7.25893Z" fill="#636161"></path>
                                                </svg>
                                                <?php echo displayTitleName($data['output']['user']['title']);?>
                            </div>

                            <div class="mt-1">
                                <ul><?php echo $data['output']['user']['mylink'];?></ul>

                            </div>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" data-supported-dps="24x24" fill="currentColor" class="mercado-match" width="24" height="24" focusable="false">
                                        <path d="M21 13h-8v8h-2v-8H3v-2h8V3h2v8h8z"></path>
                                    </svg> Add New Work Experience
                                </a>
                            </div>
                            <div class="mt-3">
                                <a class="add_edit_edu add_hover" style="cursor: pointer;" data-add-edit="Add" data-education="<?php echo $edu_tot_count; ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" data-supported-dps="24x24" fill="currentColor" class="mercado-match" width="24" height="24" focusable="false">
                                        <path d="M21 13h-8v8h-2v-8H3v-2h8V3h2v8h8z"></path>
                                    </svg> Add New Education
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
                            if(!empty($keyword)) echo '<span class="keywords-link" data-keyword_key="'.$k.'" data-keyword_value="'.$keyword.'">' . $keyword . '</span>';
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
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" data-supported-dps="24x24" fill="currentColor" class="mercado-match" width="24" height="24" focusable="false">
                            <path d="M21 13h-8v8h-2v-8H3v-2h8V3h2v8h8z"></path>
                        </svg>
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
            $current_year = date('Y');   // Outputs: 2025 (Current Year)
            $current_month = date('n');  // Outputs: 3 (Current Month - Without leading zero)
            // Check for empty values and assign current month/year if empty

$end_month = !empty($emp_vals['emp_end_month']) ?$emp_vals['emp_end_month'] : $current_month;
$end_year = !empty($emp_vals['emp_year_end']) ? $emp_vals['emp_year_end'] : $current_year;


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
                                    <a class="float-right add_edit_emp" style="cursor: pointer;" data-add-edit="Edit" data-employee="<?php echo $emp_keys; ?>" data-emp-delete="<?php echo $emp_keys; ?>" data-emp-edit-delete = <?php echo $emp_vals['keys']; ?>><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                                    </svg></a>
                                <?php } ?>
                            </div><br>
                            <p class="lh-20 fs-13 font-weight-bold"><?php echo $em_cmp_post.$role_items; ?></p>
                            <!-- <p class="lh-20 fs-13"><?php //echo get_month_from_number($emp_vals['emp_start_month']).' '.$emp_vals['emp_year_start'].' - '.$get_present_not ?> . <span><?php //echo get_diff_dates($emp_vals['emp_year_start'],$emp_vals['emp_start_month'],$emp_vals['emp_year_end'],$emp_vals['emp_end_month']); ?></span></p> -->
                            <p class="lh-20 fs-13"><?php echo get_month_from_number($emp_vals['emp_start_month']).' '.$emp_vals['emp_year_start'].' - '.$get_present_not ?> . <span><?php echo get_diff_dates($emp_vals['emp_year_start'],$emp_vals['emp_start_month'],$end_year,$end_month); ?></span></p>
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
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" data-supported-dps="24x24" fill="currentColor" class="mercado-match" width="24" height="24" focusable="false">
                            <path d="M21 13h-8v8h-2v-8H3v-2h8V3h2v8h8z"></path>
                        </svg>
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
                                    <a class="float-right add_edit_edu" style="cursor: pointer;" data-add-edit="Edit" data-education="<?php echo $edu_keys; ?>" data-edu-edit-delete = <?php echo $edu_vals['keys']; ?>><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                                    </svg></a>
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

    // $(document).on('click', '.current_role_checklabels', function () {
    //     if ($('.current_role_checkbox').is(':checked')) {
    //         $('.current_role_checkbox').prop('checked', false);
    //     } else {
    //         $('.current_role_checkbox').prop('checked', true);
    //     }
    // });

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

    function check_still_working(exp_id){
        const endDateSelect = document.getElementById('emp_end_month_'+exp_id);
        const endYearSelect = document.getElementById('emp_year_end_'+exp_id);
        if ($('#still_working_'+exp_id).is(":checked")){
            $('.emp_end_month_block_'+exp_id).hide();
            endDateSelect.removeAttribute('required');
            endYearSelect.removeAttribute('required');
        }else{
            $('.emp_end_month_block_'+exp_id).show();
            endDateSelect.setAttribute('required', 'true');
            endYearSelect.setAttribute('required', 'true');
        }
        currentCheckbox.prop('checked', !currentCheckbox.prop('checked'));
    }


</script>

<?php
taoh_get_footer();
?>