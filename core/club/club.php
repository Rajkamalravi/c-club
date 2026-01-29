<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/raj/assets/icons/icons.php';
taoh_get_header();
$showall = 0;
defined('TAOH_CURR_APP_SLUG') || define('TAOH_CURR_APP_SLUG', '');

$pagename = 'main';
$appname = 'club';
if(taoh_user_is_logged_in()){
    $data = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];
    $ptoken = $user_ptoken = $data->ptoken;
}else{
    $ptoken = $user_ptoken = '';
}

$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
$user_info_obj = $taoh_user_is_logged_in ? taoh_user_all_info() : null;

$valid_user = $taoh_user_is_logged_in && $user_info_obj->profile_complete;
if (isset($user_info_obj->avatar_image) && $user_info_obj->avatar_image != '') {
    $avatar_image = $user_info_obj->avatar_image;
} else {
    if (isset($user_info_obj->avatar) && $user_info_obj->avatar != 'default') {
        $avatar_image = TAOH_OPS_PREFIX . '/avatar/PNG/128/' . $user_info_obj->avatar . '.png';
    } else {
        $avatar_image = TAOH_OPS_PREFIX . '/avatar/PNG/128/avatar_def.png';
    }
}

$token = taoh_get_dummy_token();
$admin_tokens = explode(',', TAOH_ADMIN_TOKENS);
//$admin_token_get = json_decode(TAOH_ADMIN_TOKENS,true);
//$admin_tokens = $admin_token_get['admin_tokens'];
$is_admin = (in_array($token, $admin_tokens))?true:false;


/* Get RSVP list */
//https://ppapi.tao.ai/events.user.rsvp?mod=core&token=C3kONdHX&ops=events
$rsvped_data = array();
if(taoh_user_is_logged_in()){


    $taoh_call = "events.user.rsvp";
    $taoh_vals = array(
        'mod' => 'core',
        'token' => taoh_get_dummy_token(),
        'ops' => 'events',
        'cache_required' => 0,
        'time' => time(),
    );
    //echo taoh_apicall_get_debug($taoh_call, $taoh_vals);exit();
    $rsvped_data = json_decode( taoh_apicall_get($taoh_call, $taoh_vals), true );
    if($rsvped_data['success']){
        $rsvped_data = $rsvped_data['output'];
    }else{
        $rsvped_data = array();
    }
}
/* End Get RSVP list */

$currencies = json_encode(taoh_get_currency_symbol('',true));
$share_link = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$club_event_header = "Browse through our Events";
$club_jobs_header = "Find a New Job ! Browse through our Job Board";
$club_learning_header = "Grow faster with learning and development corner";
$club_asks_header = "Engage on Asks and grow your expertise";
$club_announcements_header ="Latest Announcements";

if ( taoh_user_is_logged_in() && isset( $_SESSION[ TAOH_ROOT_PATH_HASH ][ 'USER_INFO' ]->type ) && $_SESSION[ TAOH_ROOT_PATH_HASH ][ 'USER_INFO' ]->type == 'employer' ){
    $club_event_header = "Attend Event, Find Top Talent, Showcase Your Brand";
    $club_jobs_header = "Get Top Talent, <strong><a href=\"".TAOH_SITE_URL_ROOT.'/jobs/post'."\" style='color: #2479D8; font-weight: bold;'>Post</a></strong> a Job Today";
    $club_learning_header = "Grow faster through learning and development reads";
    $club_asks_header = "Engage on Asks and grow your expertise";

}
?>
    <style>
        .page-body {
            background-color: #fff !important;
        }

        .reads-listing-block-row div.item-details + div {
            /* height: 200px; */
        }
        #reads_blk .reads-list-content-text .sqs-html-content h2 {
            font-size: 16px;
            line-height: 28px;
            min-height: 125px;
            max-height: 125px;
            height: 125px;
            overflow: hidden;
        }
        .yo-video .y-video {
        width: 375px;
        height: 240px;
        }


    .gallery_sec a {
        position: relative;
        transition: 0.3s ease-in-out;
        -webkit-transition: 0.3s ease-in-out;
        -moz-transition: 0.3s ease-in-out;
        -ms-transition: 0.3s ease-in-out;
        -o-transition: 0.3s ease-in-out;
    }


    .gallery_sec a::before {
        position: absolute;
        content: "";
        width: 30px;
        height: 30px;
        background: none;
        background-size: contain;
        background-repeat: no-repeat;
        top:45%;
        left:50%;
        transform:translate(-50%, -50%);
    }

    .gallery_sec img {
        width: 100%;
        max-width: 150px;
        min-width: 150px;
        height: 150px;
        margin: 0 auto;
        border: 2px solid #ddd;
        border-radius: 8px;
        padding: 10px;
        transition: 0.3s ease-in-out;
        -webkit-transition: 0.3s ease-in-out;
        -moz-transition: 0.3s ease-in-out;
        -ms-transition: 0.3s ease-in-out;
        -o-transition: 0.3s ease-in-out;
        object-fit: contain;
    }

    .gallery_sec a:hover img {
        position: relative;
        width: 100%;
    }

    .gallery_sec a:hover img {
        opacity: 0.2;
    }

    .gallery_sec a:hover::before {
        position: absolute;
        content: "";
        width: 50px;
        height: 50px;
        /* background: url(https://i.ibb.co/3fMkjjF/Resize.png); */
        background-size: contain;
        background-repeat: no-repeat;
        z-index: 99;
    }
    .club-event-container {
        width: 100%;
        height: 150px;
        position: relative;
    }
    .club-event-image {
        width: 100%;
        height: 100%;
        border-radius:10px 10px 0 0;
        object-fit: contain;
        position: relative;
        z-index: 1;
        border-bottom: 1px solid #d3d3d3;
    }
    .club-events-bg {
        position: absolute;
        width: 100%;
        height: 100%;
        background-size: cover;
        border-radius: 10px 10px 0 0;
        z-index: 0;
    }
    .club-glass-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.1); /* semi-transparent white */
        backdrop-filter: blur(10px); /* frosted glass effect */
        z-index: 1;
        border-radius: 10px 10px 0 0;
    }

    </style>
<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

defined('TAOH_BANNER_IMAGE') || define('TAOH_BANNER_IMAGE',TAOH_SITE_MAIN_HERO_IMAGE);
defined('TAOH_SITE_COLOR') || define('TAOH_SITE_COLOR',TAOH_SITE_MAIN_HERO_COLOR);
defined('TAOH_SITE_DESCRIPTION') || define('TAOH_SITE_DESCRIPTION',TAOH_SITE_MAIN_DESCRIPTION);


//echo"<br>===========".TAOH_SITE_COLOR;
//echo"<br>===TAOH_BANNER_IMAGE========".TAOH_BANNER_IMAGE;

if ( ! taoh_user_is_logged_in() || $showall ){
?>
<section style="
<?php if ( TAOH_BANNER_IMAGE ){ ?>
    background-image: url('<?php echo TAOH_BANNER_IMAGE;?>');
    background-size: cover;
    background-position: center;
<?php } ?>
">
  <div class="main-hero" style="opacity:0.9;background:<?php echo defined('TAOH_SITE_COLOR') && TAOH_SITE_COLOR !='' ? TAOH_SITE_COLOR : TAOH_SITE_MAIN_HERO_COLOR; ?>;">
            <div class="container">
                <div class="row align-items-center">
                    <!--<div class="col-lg-6">
                        <div class="hero-content">
                            <h2 class="section-title pb-4 text-white" style="font-weight: 400;">Expand Your Professional <br>  Horizons</h2>
                            <h4 class="pb-4 text-white" style="font-weight: 300; font-size: 24px; line-height: 34px;">Welcome to Networking app, where every connection
                            is a step toward your next professional breakthrough.</h4>

                            <div class="hero-btn-box py-4">
                                <?php if (! taoh_user_is_logged_in()){ ?>
                                    <div class="nav-right-button">
                                        <a onclick="localStorage.removeItem('isCodeSent')" href="javascript:void(0);"
                                        class="btn theme-btn theme-btn mr-2 login-button " style="width: auto; background: #86bdf1" aria-pressed="true" data-toggle="modal" data-target="#config-modal"><i class="la la-sign-in mr-1"></i> Login / Sign Up And Grow Together!</a>


                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div> -->
                    <div class="col-lg-6">
                        <div class="hero-content">
                            <h4 class="pb-1 text-white"><?php echo TAOH_SITE_PRE_TITLE;?></h4>
                            <h2 class="section-title pb-3 text-white"><?php echo TAOH_SITE_MAIN_TITLE; ?></h2>
                            <p class="section-desc text-white"><?php echo TAOH_SITE_DESCRIPTION; ?></p>
                            <div class="hero-btn-box py-4">
                                <?php if (! taoh_user_is_logged_in()){ ?>
                                    <div class="nav-right-button">
                                        <a onclick="localStorage.removeItem('isCodeSent')" href="javascript:void(0);"
                                        class="btn theme-btn theme-btn mr-2 login-button " aria-pressed="true" data-toggle="modal" data-target="#config-modal" style="width: fit-content"><i class="la la-sign-in mr-1"></i> Login / Sign Up</a>
                                        <!-- And Grow Together! -->
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 mb-5">
                        <div class="hero-list">
                            <div class="d-flex align-items-center pb-30px">
                                <svg class="mr-3" width="31" height="30" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M29 0H2a2 2 0 0 0-2 2v20c0 1.1.9 2 2 2h4v5.8l6.2-5.8H29a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM19 8a1 1 0 1 0 0-2H6a1 1 0 0 0 0 2h13zm6 5a1 1 0 1 0 0-2H6a1 1 0 1 0 0 2h19zm-6 5a1 1 0 1 0 0-2H6a1 1 0 1 0 0 2h13z" fill="#fff"/></svg>
                                <p style="width:200px;margin:0 5px;" class="fs-15 text-white lh-20"><?php echo TAOH_SITE_MAIN_OPTION_1;?></p>
                            </div>
                            <div class="d-flex align-items-center pb-30px">
                                <svg class="mr-3" width="35" height="34" fill="none" xmlns="http://www.w3.org/2000/svg"><path opacity=".5" fill-rule="evenodd" clip-rule="evenodd" d="M4 1c0-.6.4-1 1-1h25a5 5 0 0 1 5 5v17.5a1 1 0 1 1-2 0V5a3 3 0 0 0-3-3H5a1 1 0 0 1-1-1z" fill="#fff"/><path fill-rule="evenodd" clip-rule="evenodd" d="M2 4h27a2 2 0 0 1 2 2v20a2 2 0 0 1-2 2h-4v5.8L18.8 28H2a2 2 0 0 1-2-2V6c0-1.1.9-2 2-2zm17 8a1 1 0 1 0 0-2H6a1 1 0 1 0 0 2h13zm6 5a1 1 0 1 0 0-2H6a1 1 0 1 0 0 2h19zm-6 5a1 1 0 1 0 0-2H6a1 1 0 1 0 0 2h13z" fill="#fff"/></svg>
                                <p style="width:200px;margin:0 5px;" class="fs-15 text-white lh-20"><?php echo TAOH_SITE_MAIN_OPTION_2;?></p>
                            </div>
                            <div class="d-flex align-items-center">
                                <svg class="mr-3" width="33" height="33" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.2 14a1 1 0 0 1-.8-1.7L10.4.7a2 2 0 0 1 3.1 0l10 11.6a1 1 0 0 1-.7 1.7H1.2z" fill="#fff"/><path opacity=".5" fill-rule="evenodd" clip-rule="evenodd" d="M3.4 21h17.2L12 31 3.4 21zm-3-.3a1 1 0 0 1 .8-1.7h21.6a1 1 0 0 1 .8 1.7l-10 11.6a2 2 0 0 1-3.1 0L.5 20.7z" fill="#fff"/></svg>
                                <p style="width:200px;margin:0 5px;" class="fs-15 text-white lh-20"><?php echo TAOH_SITE_MAIN_OPTION_3;?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 yo-video">

                        <iframe id="video" class="y-video" src="<?php echo TAOH_BANNER_VIDEO;?>" frameborder="0" allowfullscreen loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>
    <div class="bg-white club">
        <header class="sticky-top bg-white border-bottom border-bottom-gray" style="top: 0;">
            <section class="hero-area bg-white shadow-sm">
                <!-- <span class="stroke-shape stroke-shape-1"></span>
                <span class="stroke-shape stroke-shape-2"></span>
                <span class="stroke-shape stroke-shape-3"></span> -->
                <span class="stroke-shape stroke-shape-4"></span>
                <span class="stroke-shape stroke-shape-5"></span>
                <span class="stroke-shape stroke-shape-6"></span>
                <div class="container px-2">
                    <?php include 'includes/club_header.php'; ?>
                </div>
            </section>
        </header>

        <div class="">
            <div class="row justify-content-center mx-0">
                <div class="col-md-12 px-0">

                <?php if(TAOH_ANNOUNCEMENT_ENABLE) { ?>
				     <section class="club-section" style="width:100%; padding: 30px 15px;">
                        <div class="container">
                            <div class="text-center loaderArea" id="announcementlistloaderArea"></div>
                            <div id="announcements_blk" style="display:none;" class="row">


                                <div class="col-lg-12">
                                    <h2 class="section-title pb-4 pt-4 text-center"><?php echo $club_announcements_header; ?></h2>
                                    <div class="row p-2 d-flex align-items-center flex-wrap-reverse">
                                        <div class="col-lg-6 mb-3 mb-lg-0"><h4>Latest Announcements</h4></div>
                                        <div class="col-lg-6 text-lg-right" style="display:none;" >
                                            <a href="#" class="orange-text" style="font-size: 1.5rem">Explore more</a>
                                        </div>
                                    </div>


                                    <!--<div id="announementlistArea" class="mb-3"></div>-->

                                    <div class="row mt-4 upcoming-announcements p-0 " style="margin: auto;" id="announcements_list">
                                            <div class="club-announcement col-12 d-flex px-0" style="gap:12px;">
                                                <div class="d-none d-lg-flex align-items-center">
                                                    <button class="btn scroll-button" id="scroll-left"><i class="la la-angle-left"></i></button>
                                                </div>
                                                <div class="d-flex scroll-container" id="announementlistArea" style="gap: 1rem; overflow-x: auto; scroll-behavior: smooth; width: 100%;">

                                                </div>
                                                <div class="d-none d-lg-flex align-items-center">
                                                    <button class="btn scroll-button" id="scroll-right"><i class="la la-angle-right"></i></button>
                                                </div>
                                            </div>
                                    </div>


                                    <p class="text-right text-underline mt-2 pr-2"><a href="<?php echo TAOH_SITE_URL_ROOT.'/club/announcements';?>">View all <i class="la la-arrow-right ml-1"></i></a></p>
                                </div>
                            </div>
                        </div>
					</section>
                <?php } if(TAOH_EVENTS_ENABLE) { ?>
				    <section class="club-section" style="width:100%; padding: 30px 15px;">
                        <div class="container">
                            <div id="events_blk" class="row">
                                <div class="col-lg-12">
                                    <h2 class="section-title pb-4 pt-4 text-center"><?php echo $club_event_header; ?></h2>
                                    <div class="row p-2 d-flex align-items-center flex-wrap-reverse">
                                        <div class="col-lg-6 mb-3 mb-lg-0"><h4>Upcoming Events</h4></div>
                                        <div class="col-lg-6 text-lg-right">
                                            <a href="<?php echo TAOH_SITE_URL_ROOT.'/events';?>" class="orange-text" style="font-size: 1.5rem">Explore more events</a>
                                        </div>
                                    </div>
                                    <div id="event_loaderArea"></div>
                                    <div class="d-flex flex-wrap justify-content-center mt-4 upcoming-events dasdasd p-0" style="margin: auto; gap: 12px;" id="events_list">

                                    </div>
                                    <p class="text-right text-underline mt-2 pr-2"><a href="<?php echo TAOH_SITE_URL_ROOT.'/events';?>">View all Events <i class="la la-arrow-right ml-1"></i></a></p>
                                </div>
                            </div>
                        </div>
					</section>
                    <?php } if(TAOH_JOBS_ENABLE) { ?>
					<section style="width:100%;padding: 50px 15px;">
                        <div class="container">
                            <div id="jobs_blk" class="row mt-4">
                                <div class="col-md-12">
                                    <h2 class="section-title pb-4 pt-4 text-center"><?php echo $club_jobs_header; ?></h2>
                                    <h4 class="p-2">Recent Jobs</h4>
                                    <div id="job_loaderArea"></div>
                                    <div class="d-flex flex-wrap justify-content-center mt-4 p-0" style="margin: auto; gap: 12px;" id="jobs_list">

                                    </div>
                                    <p class="text-right text-underline mt-2 pr-2"><a href="<?php echo TAOH_SITE_URL_ROOT.'/jobs';?>">View all Jobs <i class="la la-arrow-right ml-1"></i></a></p>
                                </div>
                            </div>
                         </div>
					</section>
                    <?php } if(TAOH_LEARNING_ENABLE) { ?>
					<section class="club-section" style="width:100%; padding: 50px 15px;">
                        <div class="container">
                            <div id="reads_blk" class="row">
                                <div class="col-md-12">
                                    <h2 class="section-title pb-4 pt-4 text-center"><?php echo $club_learning_header; ?></h2>
                                    <h4 class="p-2">Curated Articles</h4>
                                    <div id="read_loaderArea"></div>
                                    <div class="d-flex flex-wrap justify-content-center mt-4 reads-list-content-text" style="margin: auto; gap: 12px;" id="reads_list">

                                    </div>
                                    <p class="text-right text-underline mt-2 pr-2"><a href="<?php echo TAOH_SITE_URL_ROOT.'/reads';?>">View all Reads <i class="la la-arrow-right ml-1"></i></a></p>

                                </div>
                            </div>
                        </div>
					</section>
                    <?php } if(TAOH_ASKS_ENABLE) { ?>
					<section style="width:100%;padding: 50px 15px;">
                         <div class="container">
                            <div id="asks_blk" class="row">
                                <div class="col-md-12">
                                    <h2 class="section-title pb-4 pt-4 text-center"><?php echo $club_asks_header; ?></h2>
                                    <h4 class="p-2">Recent Asks</h4>
                                    <div id="ask_loaderArea"></div>
                                    <div class="d-flex flex-wrap justify-content-center mt-4" style="margin: auto; gap: 12px;" id="asks_list">

                                    </div>
                                    <p class="text-right text-underline mt-2 pr-2"><a href="<?php echo TAOH_SITE_URL_ROOT.'/asks';?>">View all Asks <i class="la la-arrow-right ml-1"></i></a></p>

                                </div>
                            </div>
                        </div>
					</section>
                    <?php } ?>
                </div>

            </div>
        </div>
        <?php
        if (! taoh_user_is_logged_in()){
?>
        <section class="get-started-area section--padding" id="for-businesses" hidden>
            <div class="container">
                <div class="text-center">
                    <h2 class="section-title pb-3">Join Our Movement: <br>Connect, Collaborate, and Catalyze Change</h2>
                </div>
                <div class="row pt-50px">
                    <div class="col-lg-4 responsive-column-half">
                        <div class="media media-card align-items-center hover-s">
                            <div class="icon-element mr-3">
                            <svg width="84" height="84" viewBox="0 0 84 84" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="42" cy="42" r="42" fill="#00A3FF"/>
                            <g clip-path="url(#clip0_103_143)">
                            <path d="M42.5999 16.7998C39.7501 16.7998 37.4399 19.11 37.4399 21.9598C37.4399 24.8096 39.7501 27.1198 42.5999 27.1198C45.4486 27.117 47.7571 24.8084 47.7599 21.9598C47.7599 19.11 45.4498 16.7998 42.5999 16.7998ZM42.5999 25.3998C40.7001 25.3998 39.1599 23.8597 39.1599 21.9598C39.1599 20.06 40.7001 18.5198 42.5999 18.5198C44.4998 18.5198 46.0399 20.06 46.0399 21.9598C46.0399 23.8597 44.4998 25.3998 42.5999 25.3998Z" fill="white"/>
                            <path d="M46.0399 27.9797H45.8369L45.6554 28.07C43.7311 29.0282 41.4686 29.0282 39.5443 28.07L39.3628 27.9797H39.1599C36.7863 27.9825 34.8627 29.9061 34.8599 32.2797V37.4397C34.8599 38.8646 36.015 40.0197 37.4399 40.0197H47.7599C49.1848 40.0197 50.3399 38.8646 50.3399 37.4397V32.2797C50.3371 29.9061 48.4135 27.9825 46.0399 27.9797ZM48.6199 37.4397C48.6199 37.9147 48.2348 38.2997 47.7599 38.2997H37.4399C36.9649 38.2997 36.5799 37.9147 36.5799 37.4397V32.2797C36.5801 30.9261 37.6264 29.8027 38.9767 29.7066C41.2745 30.773 43.9253 30.773 46.223 29.7066C47.5732 29.8027 48.6196 30.9261 48.6199 32.2797V37.4397Z" fill="white"/>
                            <path d="M60.66 45.1797C57.8102 45.1797 55.5 47.4899 55.5 50.3397C55.5 53.1895 57.8102 55.4997 60.66 55.4997C63.5098 55.4997 65.82 53.1895 65.82 50.3397C65.8172 47.491 63.5086 45.1825 60.66 45.1797ZM60.66 53.7797C58.7602 53.7797 57.22 52.2395 57.22 50.3397C57.22 48.4398 58.7602 46.8997 60.66 46.8997C62.5598 46.8997 64.1 48.4398 64.1 50.3397C64.1 52.2395 62.5598 53.7797 60.66 53.7797Z" fill="white"/>
                            <path d="M64.0999 56.3599H63.897L63.7155 56.4459C61.7911 57.4039 59.5287 57.4039 57.6043 56.4459L57.4229 56.3599H57.2199C54.8463 56.3627 52.9227 58.2863 52.9199 60.6599V65.8199C52.9199 67.2448 54.075 68.3999 55.4999 68.3999H65.8199C67.2448 68.3999 68.3999 67.2448 68.3999 65.8199V60.6599C68.3971 58.2863 66.4735 56.3627 64.0999 56.3599ZM66.6799 65.8199C66.6799 66.2948 66.2949 66.6799 65.8199 66.6799H55.4999C55.025 66.6799 54.6399 66.2948 54.6399 65.8199V60.6599C54.6401 59.3062 55.6864 58.1828 57.0367 58.0867C59.3346 59.1531 61.9853 59.1531 64.2831 58.0867C65.6334 58.1828 66.6797 59.3062 66.6799 60.6599V65.8199Z" fill="white"/>
                            <path d="M24.5399 45.1797C21.6901 45.1797 19.3799 47.4899 19.3799 50.3397C19.3799 53.1895 21.6901 55.4997 24.5399 55.4997C27.3897 55.4997 29.6999 53.1895 29.6999 50.3397C29.6971 47.491 27.3885 45.1825 24.5399 45.1797ZM24.5399 53.7797C22.64 53.7797 21.0999 52.2395 21.0999 50.3397C21.0999 48.4398 22.64 46.8997 24.5399 46.8997C26.4397 46.8997 27.9799 48.4398 27.9799 50.3397C27.9799 52.2395 26.4397 53.7797 24.5399 53.7797Z" fill="white"/>
                            <path d="M27.9798 56.3599H27.7768L27.5954 56.4459C25.671 57.4039 23.4086 57.4039 21.4842 56.4459L21.3028 56.3599H21.0998C18.7262 56.3627 16.8026 58.2863 16.7998 60.6599V65.8199C16.7998 67.2448 17.9549 68.3999 19.3798 68.3999H29.6998C31.1247 68.3999 32.2798 67.2448 32.2798 65.8199V60.6599C32.277 58.2863 30.3534 56.3627 27.9798 56.3599ZM30.5598 65.8199C30.5598 66.2948 30.1747 66.6799 29.6998 66.6799H19.3798C18.9049 66.6799 18.5198 66.2948 18.5198 65.8199V60.6599C18.52 59.3062 19.5663 58.1828 20.9166 58.0867C23.2144 59.1531 25.8652 59.1531 28.163 58.0867C29.5133 58.1828 30.5596 59.3062 30.5598 60.6599V65.8199Z" fill="white"/>
                            <path d="M51.1323 60.4744C51.1318 60.4734 51.1314 60.4724 51.131 60.4714C50.9448 60.0344 50.4396 59.8311 50.0028 60.0173C46.1193 61.6448 41.8064 61.9417 37.7366 60.8618L38.5415 60.6253C38.9976 60.4918 39.259 60.0141 39.1255 59.558C38.992 59.102 38.5142 58.8406 38.0582 58.9741L34.6182 59.9829C34.6053 59.9829 34.5959 59.9975 34.5838 60.0018C34.5042 60.0315 34.4293 60.073 34.3619 60.1248C34.3321 60.1409 34.3033 60.159 34.2759 60.179C34.1852 60.2599 34.1133 60.3597 34.0652 60.4714C34.0186 60.5838 33.9963 60.7048 33.9999 60.8265C34.0208 60.943 34.0495 61.0579 34.0859 61.1705C34.091 61.1826 34.0859 61.1963 34.0953 61.2084L35.8153 64.4996C36.0353 64.9209 36.555 65.0842 36.9763 64.8642C37.3976 64.6443 37.5609 64.1245 37.341 63.7032L36.6409 62.3651C41.2649 63.7551 46.2294 63.4849 50.6753 61.6014C51.1126 61.4164 51.3173 60.9118 51.1323 60.4744Z" fill="white"/>
                            <path d="M65.5678 39.4119C65.2319 39.0762 64.6876 39.0762 64.3517 39.4119L63.1348 40.6288C62.4227 33.0963 57.6404 26.5596 50.6769 23.6008C50.2399 23.4146 49.7347 23.6179 49.5485 24.0549C49.3624 24.4919 49.5656 24.997 50.0026 25.1832C56.2495 27.8381 60.5918 33.6401 61.3778 40.382L60.4078 39.4119C60.0661 39.0819 59.5217 39.0913 59.1917 39.433C58.8699 39.7662 58.8699 40.2946 59.1917 40.6279L61.7717 43.2079C62.1071 43.5442 62.6517 43.5449 62.988 43.2094C62.9885 43.2089 62.9891 43.2084 62.9895 43.2079L65.5695 40.6279C65.9048 40.2917 65.904 39.7473 65.5678 39.4119Z" fill="white"/>
                            <path d="M35.695 24.2466C35.6922 24.1964 35.6848 24.1467 35.6727 24.0979C35.6727 24.0901 35.6632 24.085 35.6598 24.0764C35.6563 24.0678 35.6598 24.0617 35.6598 24.0549C35.6363 24.0131 35.6092 23.9733 35.5789 23.9362C35.5511 23.8858 35.5182 23.8383 35.4809 23.7943C35.43 23.7492 35.3737 23.7109 35.3132 23.6799C35.283 23.6542 35.2507 23.6309 35.2169 23.6102L31.7769 22.039C31.3375 21.8586 30.8351 22.0686 30.6547 22.5079C30.4841 22.9234 30.662 23.4003 31.0631 23.6025L32.8441 24.4169C26.139 28.0054 21.9551 34.9949 21.96 42.5999C21.96 43.0748 22.345 43.4599 22.82 43.4599C23.2949 43.4599 23.68 43.0748 23.68 42.5999C23.6757 35.9034 27.2168 29.7051 32.9877 26.3081L32.366 27.6075C32.161 28.0351 32.3406 28.5478 32.7676 28.7539C32.8836 28.8106 33.0109 28.84 33.14 28.8399C33.4702 28.8395 33.771 28.65 33.914 28.3523L35.634 24.7635C35.634 24.7549 35.634 24.7454 35.6408 24.7368C35.6632 24.678 35.6788 24.6169 35.6873 24.5545C35.702 24.5082 35.713 24.4608 35.72 24.4126C35.7171 24.3566 35.7088 24.301 35.695 24.2466Z" fill="white"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_103_143">
                            <rect width="51.6" height="51.6" fill="white" transform="translate(16.7998 16.7998)"/>
                            </clipPath>
                            </defs>
                            </svg>


                            </div>
                            <div class="media-body">
                                <h5 class="pb-2"><a href="<?php echo TAOH_SITE_URL_ROOT."/employers";?>">Employers</a></h5>
                                <p>Want to support your talent pool? Quickly find and share internal knowledge with Private Q&A</p>
                            </div>
                        </div>
                    </div><!-- end col-lg-4 -->
                    <div class="col-lg-4 responsive-column-half">
                        <div class="media media-card align-items-center hover-s">
                            <div class="icon-element mr-3">
                            <svg width="84" height="84" viewBox="0 0 84 84" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="Group 113">
                            <g id="Group 92">
                            <circle id="Ellipse 2" cx="42" cy="42" r="42" fill="#FFB600"/>
                            </g>
                            <g id="Group 109">
                            <path id="Vector" d="M42.6013 68.3998C35.7044 68.3998 29.2235 65.7167 24.3483 60.843C14.2837 50.7808 14.2837 34.4086 24.3483 24.3464C34.4129 14.2843 50.7867 14.2843 60.8513 24.3464C70.916 34.4086 70.916 50.7808 60.8513 60.843C55.979 65.7167 49.4952 68.3998 42.6013 68.3998ZM42.6013 19.6125C36.7108 19.6125 30.8233 21.8527 26.3379 26.3362C17.3728 35.3001 17.3728 49.8864 26.3379 58.8503C30.6808 63.1939 36.4578 65.5857 42.6013 65.5857C48.7418 65.5857 54.5188 63.1939 58.8617 58.8503C67.8268 49.8864 67.8268 35.3001 58.8617 26.3362C54.3792 21.8527 48.4888 19.6125 42.6013 19.6125Z" fill="white"/>
                            <path id="Vector_2" d="M55.9794 58.5429H30.047C29.2704 58.5429 28.6392 57.9107 28.6392 57.1358V53.5759C28.6392 45.652 35.0881 39.208 43.0118 39.208C50.9355 39.208 57.3844 45.6549 57.3844 53.5759V57.1358C57.3873 57.9107 56.7561 58.5429 55.9794 58.5429ZM31.4549 55.7287H54.5686V53.5759C54.5686 47.2047 49.3821 42.0222 43.0118 42.0222C36.6414 42.0222 31.4549 47.2076 31.4549 53.5759V55.7287Z" fill="white"/>
                            <path id="Vector_3" d="M43.0127 42.0195C37.6808 42.0195 33.3408 37.6817 33.3408 32.3535C33.3408 27.0223 37.6808 22.6846 43.0127 22.6846C48.3446 22.6846 52.6817 27.0223 52.6817 32.3535C52.6817 37.6817 48.3446 42.0195 43.0127 42.0195ZM43.0127 25.4987C39.2341 25.4987 36.1566 28.5751 36.1566 32.3535C36.1566 36.1319 39.2341 39.2053 43.0127 39.2053C46.7913 39.2053 49.866 36.1319 49.866 32.3535C49.866 28.5751 46.7913 25.4987 43.0127 25.4987Z" fill="white"/>
                            </g>
                            </g>
                            </svg>

                            </div>
                            <div class="media-body">
                                <h5 class="pb-2"><a href="<?php echo TAOH_SITE_URL_ROOT."/professionals";?>">Professionals</a></h5>
                                <p>Questions about your career? Join the conversation, gain insights, and make informed decisions</p>
                            </div>
                        </div>
                    </div><!-- end col-lg-4 -->
                    <div class="col-lg-4 responsive-column-half" style="padding-right: 8px !important;">
                        <div class="media media-card align-items-center hover-s">
                            <div class="icon-element mr-3">
                            <svg width="84" height="84" viewBox="0 0 84 84" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="Group 121">
                            <g id="Group 94">
                            <g id="Group 93">
                            <circle id="Ellipse 2" cx="42" cy="42" r="42" fill="#0F9D58"/>
                            </g>
                            </g>
                            <g id="Clip path group">
                            <mask id="mask0_103_207" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="10" y="22" width="65" height="42">
                            <g id="53633507fe">
                            <path id="Vector" d="M10.7998 22.7998H74.3998V63.0798H10.7998V22.7998Z" fill="white"/>
                            </g>
                            </mask>
                            <g mask="url(#mask0_103_207)">
                            <g id="Group">
                            <path id="Vector_2" d="M63.6594 24.3981L57.3127 28.1611C59.5751 30.2215 61.339 32.5783 62.7849 35.1322C64.2341 37.6928 65.3604 40.4405 66.3525 43.2826L72.7324 39.4998C71.7403 36.6245 70.619 33.8536 69.1747 31.3046C67.7321 28.754 65.9583 26.4154 63.6594 24.3981ZM21.8325 22.8975L29.4744 27.429C29.8156 27.6311 29.9299 28.0716 29.7278 28.4145C29.6748 28.5023 29.6069 28.5751 29.5274 28.6315C29.2823 28.8401 29.0421 29.0505 28.8086 29.2641C29.3121 29.8206 29.857 30.1403 30.5642 30.3192C31.4801 30.5527 32.6842 30.5775 34.362 30.5593C35.2945 29.6219 36.0232 29.1002 37.5321 28.6894C38.39 28.4559 39.4848 28.2687 40.6259 28.1329C41.8913 27.9822 43.2478 27.8911 44.4204 27.8646C45.5235 27.8414 46.451 27.8779 47.2774 27.954C48.1238 28.0319 48.8459 28.1478 49.5299 28.2836C49.8198 28.34 50.1428 28.4095 50.4624 28.4791C51.4446 28.6894 52.4069 28.8965 53.437 28.9296C54.1144 28.9511 54.8498 28.9031 55.5156 28.8335C55.6316 28.8203 55.7475 28.807 55.8618 28.7938C55.7989 28.7391 55.7359 28.6861 55.673 28.6315C55.5935 28.5751 55.5256 28.5023 55.4726 28.4145C55.2705 28.0716 55.3831 27.6311 55.726 27.429L63.3679 22.8975L63.3696 22.8992C63.6246 22.7501 63.9575 22.7667 64.1977 22.9687C66.8295 25.1798 68.8236 27.7702 70.4269 30.6007C72.0119 33.4015 73.211 36.4258 74.2694 39.5528C74.4002 39.8757 74.2793 40.255 73.9679 40.4389L66.326 44.9704C66.2829 44.9952 66.2365 45.0167 66.1885 45.0316C65.8109 45.1575 65.4035 44.9555 65.2776 44.5795L65.1865 44.3112C64.6614 44.8892 64.1083 45.406 63.517 45.868C62.8826 46.3633 62.2085 46.7972 61.4864 47.1798C62.0628 47.8721 62.2599 48.7052 62.1721 49.5101C62.1208 49.9938 61.9618 50.4658 61.7183 50.8865C61.4715 51.3121 61.1419 51.6865 60.7494 51.9697C60.2028 52.3639 59.5403 52.5957 58.8182 52.5659C58.8331 52.6984 58.8397 52.8342 58.8397 52.97C58.8397 53.9472 58.4439 54.83 57.8029 55.471C57.1619 56.1103 56.2759 56.5061 55.297 56.5061C55.0453 56.5061 54.7985 56.4796 54.5583 56.4266C54.5467 57.0096 54.3761 57.5744 54.0813 58.0663C53.7733 58.5781 53.331 59.0071 52.7878 59.292C52.3356 59.5288 51.8172 59.6663 51.2558 59.6663C50.9659 59.6663 50.6827 59.6282 50.4127 59.557C50.3432 60.0621 50.1494 60.5408 49.8595 60.9515C49.4919 61.4732 48.9635 61.8873 48.3292 62.1092C48.0012 62.2235 47.6501 62.2865 47.2841 62.2865C46.4576 62.2865 44.9024 62.1606 43.1749 61.7879C42.1364 61.5643 41.0334 61.2513 39.9817 60.8207L39.2993 61.672L39.3009 61.6736C38.6997 62.4206 37.8484 62.8562 36.9607 62.9539C36.0729 63.0516 35.1471 62.8131 34.3968 62.2136L34.3951 62.2119L34.3935 62.2136C33.7227 61.677 33.3037 60.9399 33.1529 60.1549C32.7737 60.3569 32.3629 60.4795 31.9373 60.5259C31.0594 60.6236 30.1286 60.3934 29.3303 59.8816C29.2442 59.8269 29.1647 59.7706 29.0918 59.7143C28.3714 59.1479 28.0153 58.3396 27.9523 57.5049C27.9457 57.4204 27.9424 57.3343 27.9424 57.2498C27.7138 57.321 27.4786 57.369 27.2418 57.3955C26.3242 57.4966 25.3653 57.2581 24.6531 56.7231C24.6332 56.7082 24.6514 56.7198 24.5819 56.6651L24.5802 56.6668C23.8067 56.0523 23.4109 55.1927 23.3413 54.3149C23.2933 53.7319 23.3927 53.1406 23.6212 52.6057C22.8478 52.6106 22.0677 52.3622 21.4184 51.8438C20.6665 51.2426 20.2292 50.3929 20.1315 49.5068C20.0338 48.6224 20.2739 47.6982 20.8752 46.9496L21.1087 46.6564C20.9398 46.2523 20.7708 45.9128 20.5307 45.5815C20.3501 45.3314 20.1216 45.0747 19.8152 44.7832C19.6346 45.0234 19.3133 45.1327 19.0119 45.0316C18.9639 45.0167 18.9175 44.9952 18.8744 44.9704L11.2325 40.4389C10.9211 40.255 10.8002 39.8757 10.931 39.5528C11.9894 36.4258 13.1885 33.4015 14.7735 30.6007C16.3768 27.7702 18.3709 25.1798 21.0027 22.9687C21.2429 22.7667 21.5758 22.7501 21.8308 22.8992L21.8325 22.8975ZM27.7751 30.2695C26.1387 31.9589 24.8021 33.8321 23.6676 35.8361C22.3409 38.1781 21.2876 40.7072 20.3551 43.3323C20.9646 43.8607 21.3787 44.3029 21.6983 44.7435C21.859 44.967 21.9931 45.184 22.1124 45.406L23.0449 44.2433V44.2416C23.6444 43.493 24.4957 43.059 25.3835 42.9613C26.2696 42.8636 27.1954 43.1021 27.9457 43.7017L27.949 43.705L27.9507 43.7033L27.954 43.7066L27.9556 43.705L27.9871 43.7331C28.5635 44.2068 28.9477 44.8296 29.1316 45.502L29.3088 45.2801C30.4798 43.8143 31.8809 43.435 33.0801 43.7215C33.567 43.8375 34.0159 44.066 34.3968 44.3758C34.7761 44.6855 35.0908 45.0764 35.3144 45.5202C35.745 46.3749 35.8493 47.4266 35.4369 48.48C36.2187 48.4617 37.0104 48.7019 37.6679 49.2153C37.701 49.2385 37.7325 49.265 37.7623 49.2948C38.4314 49.9623 38.8521 50.812 38.9482 51.6831C39.0426 52.5328 38.8322 53.3974 38.2492 54.1344L38.1664 54.242C38.8769 54.2735 39.5842 54.5236 40.1804 55.0006C40.9324 55.6002 41.3696 56.4515 41.4673 57.3359C41.5551 58.1226 41.3746 58.9408 40.9125 59.6398C41.7638 59.9594 42.6433 60.2012 43.4797 60.3818C45.0895 60.7296 46.5255 60.8472 47.2841 60.8472C47.4911 60.8472 47.6832 60.814 47.8555 60.7528C48.195 60.6335 48.4799 60.4099 48.6819 60.1234C48.8824 59.8385 48.9983 59.494 48.9983 59.1346C48.9983 59.0634 48.9933 58.9922 48.985 58.921C47.4464 58.5168 45.916 57.9653 44.3972 57.268C42.7443 56.5078 41.1029 55.5753 39.4715 54.4673C39.1436 54.2437 39.0591 53.7965 39.2827 53.4686C39.508 53.1406 39.9552 53.0562 40.2831 53.2798C41.8466 54.3414 43.4184 55.2358 45.0001 55.9629C46.5769 56.6867 48.1619 57.2448 49.7535 57.634C49.8728 57.6639 49.9771 57.7202 50.0616 57.7964C50.2223 57.9305 50.4094 58.0382 50.6115 58.1127C50.8102 58.1856 51.0272 58.227 51.2558 58.227C51.5771 58.227 51.8686 58.1508 52.117 58.02C52.4234 57.8593 52.6735 57.6175 52.8458 57.3293C53.0197 57.0411 53.1157 56.7065 53.1157 56.3604C53.1157 56.1616 53.0843 55.9596 53.0147 55.7608C49.4173 54.5899 45.4274 52.6123 41.045 49.8315C40.7088 49.6195 40.6094 49.1739 40.8214 48.8377C41.035 48.5031 41.4806 48.4038 41.8168 48.6158C46.2191 51.4099 50.2007 53.3675 53.7584 54.4872C53.8577 54.5186 53.9439 54.5683 54.0184 54.6329C54.1939 54.7687 54.3927 54.878 54.6047 54.9509C54.8167 55.0255 55.0502 55.0652 55.297 55.0652C55.8767 55.0652 56.4017 54.8317 56.7827 54.4524C57.1619 54.0731 57.3971 53.5481 57.3971 52.97C57.3971 52.7796 57.3723 52.5941 57.3243 52.4185C57.2961 52.3191 57.263 52.2231 57.2232 52.1303C56.203 51.7676 54.6676 51.0521 52.6155 49.9822C50.3746 48.8162 47.5292 47.2361 44.0809 45.2436C43.738 45.0449 43.6204 44.6043 43.8192 44.2615C44.0179 43.9186 44.4602 43.801 44.803 43.9998C48.2662 46.0005 51.0918 47.5707 53.2814 48.7118C55.4394 49.8364 56.9897 50.5503 57.9304 50.8534C57.9719 50.8666 58.0133 50.8832 58.0547 50.903C58.7669 51.2608 59.4227 51.1515 59.903 50.8053C60.1349 50.638 60.3287 50.4194 60.4711 50.1726C60.6152 49.9225 60.7096 49.6443 60.7411 49.3594C60.8057 48.7599 60.5738 48.1272 59.9146 47.6916L59.913 47.6899L59.908 47.6883L59.903 47.6833L59.8914 47.675H59.8898C59.8815 47.6684 59.8716 47.6601 59.8616 47.6535V47.6518L59.8467 47.6402L59.8434 47.6386L59.8384 47.6336L59.7656 47.5723C59.7474 47.5574 59.7291 47.5442 59.7126 47.5276C58.5267 46.5289 57.3375 45.5302 56.0854 44.5315C54.7869 43.4963 53.4271 42.4661 51.9448 41.4475C49.0298 39.4435 45.5798 37.4427 42.1199 35.4403C41.7737 35.5016 41.4259 35.5728 41.0698 35.6954C40.6375 35.8428 40.1788 36.0713 39.6686 36.4572C39.1204 36.8713 38.4778 37.4957 37.8153 38.1383C37.1429 38.7909 36.4505 39.4617 35.7698 40.0016C35.0394 40.5796 34.3206 41.0103 33.62 41.2471C32.8532 41.5071 32.1079 41.5386 31.394 41.2918L31.3907 41.2902V41.2918C30.6172 41.0235 29.9001 40.4206 29.5191 39.571C29.2028 38.8654 29.1183 37.9975 29.428 37.0237C29.9564 35.3691 31.659 33.4329 32.9989 31.9953C31.876 31.9787 30.9783 31.9108 30.2098 31.7154C29.221 31.4636 28.4641 31.0247 27.7751 30.2695ZM27.8877 28.1611L21.541 24.3981C19.2404 26.4154 17.4683 28.754 16.0257 31.3046C14.5814 33.8536 13.4601 36.6245 12.468 39.4998L18.7287 43.2114C18.7535 43.1452 18.7916 43.0806 18.838 43.021C18.8827 42.9663 18.934 42.9199 18.9887 42.8818C19.946 40.1821 21.0342 37.5719 22.4155 35.1322C23.8614 32.5783 25.6253 30.2215 27.8877 28.1611ZM25.1996 50.4029C25.2311 50.3648 25.2642 50.3316 25.299 50.3018L27.3047 47.7893C27.3246 47.7562 27.3461 47.7247 27.3726 47.6932L27.3759 47.6949C27.7221 47.2576 27.8596 46.716 27.8016 46.196C27.7453 45.6859 27.5002 45.1973 27.0778 44.8478L27.0414 44.8196L27.043 44.818L27.0414 44.8147C26.6041 44.4702 26.0625 44.3327 25.5408 44.3907C25.0191 44.447 24.5173 44.702 24.1661 45.141L24.1595 45.1476H24.1579L22.5298 47.1781C22.5082 47.2096 22.4834 47.2411 22.4569 47.2709L21.9964 47.844C21.6453 48.2812 21.5045 48.8261 21.5625 49.3495C21.6221 49.8729 21.8755 50.3747 22.3144 50.7242C22.7534 51.0736 23.2983 51.2128 23.8233 51.1548C24.3467 51.0968 24.8485 50.8418 25.1996 50.4029ZM35.1802 55.6995C35.205 55.6449 35.2365 55.5902 35.2779 55.5405C35.3541 55.4445 35.4369 55.3534 35.5214 55.2673L37.1164 53.2466C37.4509 52.8243 37.5702 52.3274 37.5172 51.8405C37.4575 51.3055 37.1909 50.7722 36.7652 50.3366C36.3313 50.0054 35.7996 49.8729 35.2879 49.9292C34.8142 49.9805 34.3604 50.1942 34.0225 50.5585L29.9614 55.6449C29.9365 55.7111 29.9001 55.7757 29.8537 55.8337C29.8123 55.8867 29.7759 55.938 29.7444 55.9877C29.4844 56.3919 29.3519 56.9036 29.3899 57.3972C29.4231 57.8543 29.6102 58.2899 29.9829 58.5831C30.0259 58.6162 30.0674 58.646 30.1088 58.6725C30.6305 59.0071 31.2267 59.1578 31.7783 59.0965C32.2652 59.0435 32.719 58.8183 33.0469 58.4025L33.5455 57.7715C33.6349 57.6158 33.7376 57.4635 33.8535 57.3194C33.9032 57.2564 33.9612 57.2051 34.0241 57.1637L35.1802 55.6995ZM34.7396 58.5831C34.5641 58.9392 34.4995 59.335 34.5425 59.7193C34.6005 60.2426 34.8556 60.7428 35.2961 61.094L35.2945 61.0956C35.7334 61.4451 36.2783 61.5842 36.8017 61.5262C37.325 61.4683 37.8269 61.2115 38.1797 60.7726V60.7743L39.1602 59.5536C39.1867 59.5122 39.2181 59.4725 39.2529 59.4377L39.604 59.0005C39.9552 58.5615 40.0943 58.0183 40.0363 57.4933C39.9784 56.9699 39.7233 56.4697 39.2844 56.1202C38.8455 55.7691 38.3006 55.63 37.7755 55.688C37.3466 55.736 36.9325 55.9165 36.6029 56.2213L34.7396 58.5831ZM32.7554 49.8397C32.767 49.8215 32.7803 49.8033 32.7935 49.7867L32.8068 49.7702C32.82 49.752 32.8349 49.7354 32.8499 49.7205L32.8515 49.7172L33.6349 48.7367C34.357 47.834 34.3869 46.8767 34.0291 46.1678C33.8966 45.9028 33.7111 45.6709 33.4892 45.4904C33.2689 45.3115 33.0171 45.1807 32.7472 45.1161C32.0515 44.9505 31.2019 45.2155 30.4367 46.1761L25.2195 52.71L25.1864 52.7514C24.8833 53.1539 24.7359 53.6822 24.7789 54.2023C24.8187 54.7058 25.0423 55.196 25.4779 55.5422L25.4762 55.5439H25.4779C25.5094 55.5704 25.463 55.5306 25.5209 55.5753C25.94 55.89 26.5197 56.0291 27.0844 55.9662C27.5979 55.9099 28.0881 55.6846 28.4177 55.2706L32.7554 49.8397ZM57.2183 30.0575C56.8257 30.1171 56.2941 30.195 55.6614 30.2612C54.9492 30.3374 54.1509 30.3887 53.3923 30.3639C52.2164 30.3258 51.2011 30.1072 50.1626 29.8852C49.8761 29.8223 49.5863 29.761 49.2534 29.6948C48.5892 29.5639 47.9068 29.453 47.1483 29.3834C46.3715 29.3105 45.4953 29.2774 44.4486 29.3006C43.3472 29.3238 42.042 29.4132 40.7949 29.5623C39.7018 29.6915 38.6765 29.8637 37.9097 30.0724C36.3909 30.4865 35.8212 31.0926 34.5955 32.3945L34.5442 32.4491C33.254 33.8205 31.293 35.9057 30.7978 37.456C30.6056 38.0588 30.6487 38.5772 30.8325 38.9863C31.0429 39.455 31.4387 39.788 31.8644 39.9354V39.937C32.2553 40.0712 32.6892 40.0447 33.1579 39.8857C33.6945 39.7051 34.2693 39.3557 34.8738 38.877C35.5081 38.3735 36.169 37.7325 36.8116 37.1098C37.4989 36.4423 38.1648 35.7964 38.8008 35.3144C39.4599 34.8159 40.0479 34.5227 40.6011 34.3339C41.1444 34.1468 41.6396 34.064 42.1364 33.9795C42.2954 33.953 42.4644 33.9795 42.6151 34.0673L42.6433 34.0822C46.1876 36.1343 49.732 38.1847 52.7629 40.2666C54.29 41.315 55.673 42.3618 56.9814 43.4069C58.1292 44.3211 59.2273 45.2387 60.3171 46.1579C61.1618 45.7538 61.9286 45.2867 62.6325 44.7368C63.3547 44.172 64.0172 43.5162 64.6349 42.7477C63.7555 40.3312 62.7617 38.0042 61.5328 35.8361C60.3519 33.7493 58.949 31.8015 57.2183 30.0575Z" fill="white"/>
                            </g>
                            </g>
                            </g>
                            </g>
                            </svg>

                            </div>
                            <div class="media-body">
                                <h5 class="pb-2"><a href="<?php echo TAOH_SITE_URL_ROOT."/partners";?>">Partners</a></h5>
                                <p>Partner with us to reach a wider audience, offer expertise, and craft experiences that leave a lasting impact</p>
                            </div>
                        </div>
                    </div><!-- end col-lg-4 -->
                </div><!-- end row -->
            </div><!-- end container -->
        </section>

        <?php
        }
        ?>


<?php if(!taoh_user_is_logged_in()) { ?>
<div class="slider-wrapper">
    <!-- slider  -->
    <div class="container py-3">

        <h3 class="text-center clients-sub-title mb-4">People, jobs, events, and more - powered by AI and a thriving community. The <strong>#FutureOfCommunity</strong> platform built for everyone.</h3>
        <p class="text-center mb-4 clients-sub-title-content">
            From an Organization that is Trusted By <span>15,000+</span> Companies
        </p>
        <!--  -->
        <div class="container mt-3" style="position: relative; overflow: hidden; height: 100px;">
            <?php
                $numbers = range(10, 60); // Creates an array with numbers from 1 to 99
                shuffle($numbers);       // Shuffles the array randomly
                for($i=0; $i<10; $i++){
                    //https://cdn.tao.ai/images/company/logo<?php echo $company_logo_arr[0]; .png
                    //https://opslogy.com/avatar/PNG/128/avatar_0'.$numbers[$i].'.png

                    echo '<div class="itemLeft item'.$i.'"><img src="https://cdn.tao.ai/images/company/logo'.$numbers[$i].'.png" alt="" style="width: 100px; height: 100px; object-fit: contain;"></div>';
                }
                ?>

        </div>
        <div class="container mt-3" style="position: relative; overflow: hidden; height: 100px;">
            <?php
                $numbers = range(10, 60); // Creates an array with numbers from 1 to 99
                shuffle($numbers);       // Shuffles the array randomly
                for($i=0; $i<10; $i++){
                    //https://cdn.tao.ai/images/company/logo<?php echo $company_logo_arr[0]; .png
                    //https://opslogy.com/avatar/PNG/128/avatar_0'.$numbers[$i].'.png

                    echo '<div class="itemRight item'.$i.'"><img src="https://cdn.tao.ai/images/company/logo'.$numbers[$i].'.png" alt="" style="width: 100px; height: 100px; object-fit: contain;"></div>';
                }
                ?>

        </div>
        <!--  -->
    </div>
</div>
<?php } ?>

</div>
    <script type="text/javascript">
        const applied_jobs = <?php echo json_encode($_SESSION[TAOH_ROOT_PATH_HASH.'_applied_jobs'] ?? new stdClass()); ?>;
        const isLoggedIn = <?= json_encode($taoh_user_is_logged_in); ?>;
        const isValidUser = <?= json_encode($valid_user); ?>;

        let hires_slug = '<?php echo TAOH_PLUGIN_PATH_NAME; ?>';
        let event_loaderArea = $('#event_loaderArea');
        let job_loaderArea = $('#job_loaderArea');
        let read_loaderArea = $('#read_loaderArea');
        let ask_loaderArea = $('#ask_loaderArea');
        let events_list = $('#events_list');
        let jobs_list = $('#jobs_list');
        let reads_list = $('#reads_list');
        let asks_list = $('#asks_list');

        let geoHash = '';
        let term = '';
        let events_totalItems = 0;
        let events_search = "";
        let events_itemsPerPage = 12;
        let events_currentPage = 0;

        let jobs_totalItems = 0;
        let jobs_search = "";
        let jobs_itemsPerPage = 4;
        let jobs_currentPage = 1;

        let reads_totalItems = 0;
        let reads__search = "";
        let reads_itemsPerPage = 4;
        let reads_currentPage = 1;
        let arr_cont = [];

        let asks_totalItems = 0;
        let asks_search = '';
        let asks_itemsPerPage = 4;
        let asks_currentPage = 1;
        let liked_check = '';
        let comment_show = '';

        var event_type = '';
        var user_ptoken = '<?php echo $ptoken; ?>';
        var rsvp_done = '';
        var rsvp_color = '';
        var fill = '';
        var get_slug = false;
        const rsvped_data = <?php echo json_encode($rsvped_data); ?>;
        const rsvp_find = new Array();
        const currencies = <?php echo $currencies; ?>;

        var events_store_name = EVENTStore;
        var events_get_slug = '';
        var events_already_rendered = '';
        var event_list_name = '';

        var jobs_store_name = JOBStore;
        var jobs_get_slug = '';
        var jobs_already_rendered = '';
        var job_list_name = '';

        var reads_store_name = READStore;
        var reads_get_slug = '';
        var reads_already_rendered = '';
        var read_list_name = '';

        var asks_store_name = ASKStore;
        var asks_get_slug = '';
        var asks_already_rendered = '';
        var ask_list_name = '';
        var event_enable = <?= json_encode((bool)TAOH_EVENTS_ENABLE) ?>;
        var job_enable = <?= json_encode((bool)TAOH_JOBS_ENABLE) ?>;
        var ask_enable = <?= json_encode((bool)TAOH_ASKS_ENABLE) ?>;
        var reads_enable = <?= json_encode((bool)TAOH_READS_ENABLE) ?>;
        var annoucement_enable = <?= json_encode((bool)TAOH_ANNOUNCEMENT_ENABLE) ?>;
        //console.log('-----kalpana--------',isLoggedIn)

        $(document).ready(function () {

            if(annoucement_enable)  taoh_feed_init();

            <?php if(TAOH_INTAODB_ENABLE) { ?>
                if(event_enable) geteventlistdata();
                if(job_enable) getjoblistdata();
                if(reads_enable) getreadslistdata();
                if(ask_enable) getasklistdata();
            <?php }else{ ?>
                if(event_enable) taoh_events_init();
                if(job_enable) taoh_jobs_init();
                if(reads_enable) taoh_blogs_init();
                if(ask_enable) taoh_asks_init();
            <?php } ?>
        });

        function cleanFontstyle(text){
            text.replace("font-size", "font-size-clean");
            text.replace("font-family", "font-family-clean");
            text.replace("<h1", "<span"); text.replace("</h1", "</span");
            text.replace("<h2", "span");text.replace("</h2", "</span");
            text.replace("<h3", "span");text.replace("</h3", "</span");
            text.replace("<h4", "span");text.replace("</h4", "</span");
            text.replace("<h5", "span");text.replace("</h5", "</span");
            text.replace("<h6", "span");text.replace("</h6", "</span");

            text.replace(/(<([^>]+)>)/ig,"");
            //text = $(text).text();
           // alert(text);
            return text;
        }

        function isEventTokenPresent(eventToken) {
            return rsvped_data.some(item => item.eventtoken === eventToken);
        }

        /* Events */
        function date_read(date, locality, timezone) {
            let options;

            if (locality === 0) {
                options = {
                    weekday: 'short',
                    month: 'short',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: "numeric"
                };
            } else {
                options = {
                    weekday: 'short',
                    month: 'short',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: "numeric"
                };

                // Use a fallback mechanism for timezone
                timezone = timezone || getCookie('client_time_zone') || Intl.DateTimeFormat().resolvedOptions().timeZone;
            }

            if (!isValidTimezone(timezone)) timezone = 'UTC';

            options.timeZone = timezone;

            let output = new Date(date);
            return output.toLocaleDateString('en-US', options).toUpperCase();
        }

        function get_liked_check(conttoken,apps_slug){
            if(!isLoggedIn) return '';
            let get_liked = false;
            let is_local = localStorage.getItem(apps_slug+'_'+conttoken+'_liked');
            if ((get_liked) || (is_local)) {
                var liked_checks = `<a class="fs-25 mr-2 ml-2 already-saved" onclick="event.stopPropagation();" style="vertical-align: text-bottom;">
                    <!-- <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark-fill.svg" alt="bookmark-saved" class="bookmark-saved" style="width: 18px"> -->

                    <svg width="20" height="20" viewBox="0 0 20 27" fill="none" xmlns="http://www.w3.org/2000/svg" class="bookmark-saved">
                        <path d="M2.5 0.5H17.5C18.6041 0.5 19.5 1.39593 19.5 2.5V25.4014C19.4998 25.823 19.156 26.167 18.7344 26.167C18.5737 26.167 18.4201 26.1185 18.2939 26.0293L18.292 26.0283L10.2871 20.4238L10 20.2227L9.71289 20.4238L1.70801 26.0283L1.70605 26.0293C1.57991 26.1185 1.4263 26.167 1.26562 26.167C0.843959 26.167 0.500177 25.823 0.5 25.4014V2.5C0.5 1.39593 1.39593 0.5 2.5 0.5Z" fill="white" stroke=""/>
                    </svg>
                </a>`;
            } else {
                var liked_checks = `<a class="fs-25 jobs_like" style="cursor: pointer;">
                <!-- <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark.svg" alt="bookmark" data-cont="${(conttoken)}" class="job_save" title="Save Job" style="width: 18px"> -->

                    <svg width="20" height="20" viewBox="0 0 20 27" fill="none" xmlns="http://www.w3.org/2000/svg" data-cont="${(conttoken)}" class="job_save" title="Save Job">
                        <path d="M2.5 0.5H17.5C18.6041 0.5 19.5 1.39593 19.5 2.5V25.4014C19.4998 25.823 19.156 26.167 18.7344 26.167C18.5737 26.167 18.4201 26.1185 18.2939 26.0293L18.292 26.0283L10.2871 20.4238L10 20.2227L9.71289 20.4238L1.70801 26.0283L1.70605 26.0293C1.57991 26.1185 1.4263 26.167 1.26562 26.167C0.843959 26.167 0.500177 25.823 0.5 25.4014V2.5C0.5 1.39593 1.39593 0.5 2.5 0.5Z" fill="" stroke="white"/>
                    </svg>
                </a>`;
            }
            //<img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark.svg" alt="bookark" style="width: 18px">
            //<i style="cursor:pointer;" data-cont="${(conttoken)}" title="Save Job" class="las la-bookmark job_save"></i>
            return liked_checks;
        }

        function get_liked_check_ask(conttoken,apps_slug){
            if(!isLoggedIn) return '';
            let get_liked = false;
            let is_local = localStorage.getItem(apps_slug+'_'+conttoken+'_liked');
            if ((get_liked) || (is_local)) {
                var liked_checks = `<a class="fs-25 mr-2 ml-2 already-saved" onclick="event.stopPropagation();" style="vertical-align: text-bottom;">
                    <!-- <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark-fill.svg" alt="bookmark-saved" class="bookmark-saved" style="width: 18px"> -->

                    <svg width="20" height="20" viewBox="0 0 20 27" fill="none" xmlns="http://www.w3.org/2000/svg" class="bookmark-saved">
                        <path d="M2.5 0.5H17.5C18.6041 0.5 19.5 1.39593 19.5 2.5V25.4014C19.4998 25.823 19.156 26.167 18.7344 26.167C18.5737 26.167 18.4201 26.1185 18.2939 26.0293L18.292 26.0283L10.2871 20.4238L10 20.2227L9.71289 20.4238L1.70801 26.0283L1.70605 26.0293C1.57991 26.1185 1.4263 26.167 1.26562 26.167C0.843959 26.167 0.500177 25.823 0.5 25.4014V2.5C0.5 1.39593 1.39593 0.5 2.5 0.5Z" fill="white" stroke=""/>
                    </svg>
                </a>`;
            } else {
                var liked_checks = `<a class="fs-25 asks_like" style="cursor: pointer;">
                <!-- <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark.svg" alt="bookmark" data-cont="${(conttoken)}" class="ask_save" title="Save Ask" style="width: 18px"> -->

                    <svg width="20" height="20" viewBox="0 0 20 27" fill="none" xmlns="http://www.w3.org/2000/svg" data-cont="${(conttoken)}" class="ask_save" title="Save Ask">
                        <path d="M2.5 0.5H17.5C18.6041 0.5 19.5 1.39593 19.5 2.5V25.4014C19.4998 25.823 19.156 26.167 18.7344 26.167C18.5737 26.167 18.4201 26.1185 18.2939 26.0293L18.292 26.0283L10.2871 20.4238L10 20.2227L9.71289 20.4238L1.70801 26.0283L1.70605 26.0293C1.57991 26.1185 1.4263 26.167 1.26562 26.167C0.843959 26.167 0.500177 25.823 0.5 25.4014V2.5C0.5 1.39593 1.39593 0.5 2.5 0.5Z" fill="" stroke="white"/>
                    </svg>
                </a>`;
            }
            //<img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark.svg" alt="bookark" style="width: 18px">
            //<i style="cursor:pointer;" data-cont="${(conttoken)}" title="Save Job" class="las la-bookmark job_save"></i>
            return liked_checks;
        }

        var liked_arr = [];
        function get_liked_check_event(eventtoken,contstoken=''){
           // alert('---------');
            if(!isLoggedIn) return '';
            if(jQuery.inArray(eventtoken,liked_arr) !== -1){
                var get_liked = 1;
            }else{
                var get_liked = 0;
            }
            let is_local = localStorage.getItem('events_'+eventtoken+'_'+contstoken+'_liked');
            if ((get_liked) || (is_local)) {
                var liked_checks = `<a class="fs-25 mr-2 ml-2 already-saved" style="vertical-align: text-bottom;">
                    <!-- <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark-fill.svg" alt="bookmark-saved" class="bookmark-saved" style="width: 18px"> -->

                    <svg width="20" height="20" viewBox="0 0 20 27" fill="none" xmlns="http://www.w3.org/2000/svg" class="bookmark-saved">
                        <path d="M2.5 0.5H17.5C18.6041 0.5 19.5 1.39593 19.5 2.5V25.4014C19.4998 25.823 19.156 26.167 18.7344 26.167C18.5737 26.167 18.4201 26.1185 18.2939 26.0293L18.292 26.0283L10.2871 20.4238L10 20.2227L9.71289 20.4238L1.70801 26.0283L1.70605 26.0293C1.57991 26.1185 1.4263 26.167 1.26562 26.167C0.843959 26.167 0.500177 25.823 0.5 25.4014V2.5C0.5 1.39593 1.39593 0.5 2.5 0.5Z" fill="white" stroke=""/>
                    </svg>
                </a>`;
            } else {
                var liked_checks = `<a class="fs-25 events_like" style="cursor: pointer;">
                <!-- <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark.svg" alt="bookmark" data-event="${(contstoken)}" data-cont="${(eventtoken)}" class="event_save" title="Save Event" style="width: 18px"> -->

                    <svg width="20" height="20" viewBox="0 0 20 27" fill="none" xmlns="http://www.w3.org/2000/svg" data-event="${(contstoken)}" data-cont="${(eventtoken)}" class="event_save" title="Save Event">
                        <path d="M2.5 0.5H17.5C18.6041 0.5 19.5 1.39593 19.5 2.5V25.4014C19.4998 25.823 19.156 26.167 18.7344 26.167C18.5737 26.167 18.4201 26.1185 18.2939 26.0293L18.292 26.0283L10.2871 20.4238L10 20.2227L9.71289 20.4238L1.70801 26.0283L1.70605 26.0293C1.57991 26.1185 1.4263 26.167 1.26562 26.167C0.843959 26.167 0.500177 25.823 0.5 25.4014V2.5C0.5 1.39593 1.39593 0.5 2.5 0.5Z" fill="" stroke="white"/>
                    </svg>
                </a>`;
            }
            //<img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark.svg" alt="bookark" style="width: 18px">
            //<i style="cursor:pointer;" data-cont="${(conttoken)}" title="Save Event" class="las la-bookmark event_save"></i>
            return liked_checks;
        }

        $(document).on("click", ".job_save", function(event) {
            event.stopPropagation(); // Stop the event from propagating to the parent
            var savetoken = $(this).attr('data-cont');
            $('.jobs_like').find(`[data-cont='${savetoken}']`).attr('src',"<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark-fill.svg");
            $('.jobs_like').find(`[data-cont='${savetoken}']`).removeClass('job_save').addClass("already-saved").removeAttr("style");
            $('.jobs_like').find(`[data-cont='${savetoken}']`).parent().removeAttr("style");
            localStorage.setItem('jobs_'+savetoken+'_liked',1);
            delete_jobs_into();
            var data = {
                'taoh_action': 'job_like_put',
                'conttoken': savetoken,
                'ptoken': user_ptoken,
            };
            jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
                if(response.success){
                    taoh_set_success_message('Job Saved Successfully.');
                }else{
                    taoh_set_error_message('Job Save Failed.');
                    console.log( "Like Failed!" );
                }
            }).fail(function() {
                console.log( "Network issue!" );
            })
        });

        $(document).on("click", ".ask_save", function(event) {
            event.stopPropagation(); // Stop the event from propagating to the parent
            var savetoken = $(this).attr('data-cont');
            $('.asks_like').find(`[data-cont='${savetoken}']`).attr('src',"<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark-fill.svg");
            $('.asks_like').find(`[data-cont='${savetoken}']`).removeClass('ask_save').addClass("already-saved").removeAttr("style");
            $('.asks_like').find(`[data-cont='${savetoken}']`).parent().removeAttr("style");
            localStorage.setItem('asks_'+savetoken+'_liked',1);
            delete_asks_into();
            var data = {
                'taoh_action': 'ask_like_put',
                'conttoken': savetoken,
                'ptoken': user_ptoken,
            };
            jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
                if(response.success){
                    taoh_set_success_message('Ask Saved Successfully.');
                }else{
                    taoh_set_error_message('Ask Save Failed.');
                    console.log( "Like Failed!" );
                }
            }).fail(function() {
                console.log( "Network issue!" );
            })
        });

        $(document).on("click", ".event_save", function(event) {
             event.stopPropagation(); // Stop the event from propagating to the parent
                var savetoken = $(this).attr('data-cont');
                var contttoken = $(this).attr('data-event');
                $('.events_like').find(`[data-cont='${savetoken}']`).attr('src',"<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark-fill.svg");
                $('.events_like').find(`[data-cont='${savetoken}']`).removeClass('event_save').addClass("already-saved").removeAttr("style");
                $('.events_like').find(`[data-cont='${savetoken}']`).parent().addClass("already-saved").removeAttr("style");
                localStorage.setItem('events_'+savetoken+'_'+contttoken+'_liked',1);
                delete_events_into();
                var data = {
                    'taoh_action': 'event_like_put',
                    'eventtoken': savetoken,
                    'contttoken': contttoken,
                    'ptoken': user_ptoken,
                };
                jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
                    if(response.success){
                        taoh_set_success_message('Event Saved Successfully.');
                    }else{
                        taoh_set_error_message('Event Save Failed.');
                        console.log( "Like Failed!" );
                    }
                }).fail(function() {
                    console.log( "Network issue!" );
            })
        });

        function delete_events_into(){
            getIntaoDb(dbName).then((db) => {
                let dataStoreName = EVENTStore;
                const transaction = db.transaction(dataStoreName, 'readwrite');
                const objectStore = transaction.objectStore(dataStoreName);
                const request = objectStore.openCursor();
                request.onsuccess = (event) => {
                const cursor = event.target.result;
                if (cursor) {
                    const index_key = cursor.primaryKey;
                    if(index_key.includes('event'))
                    {
                    objectStore.delete(index_key);
                    }
                    cursor.continue();
                }
                };
            }).catch((err) => {
                console.log('Error in deleting data store');
            });
        }


        function delete_asks_into(){
            getIntaoDb(dbName).then((db) => {
                let dataStoreName = ASKStore;
                const transaction = db.transaction(dataStoreName, 'readwrite');
                const objectStore = transaction.objectStore(dataStoreName);
                const request = objectStore.openCursor();
                request.onsuccess = (event) => {
                const cursor = event.target.result;
                if (cursor) {
                    const index_key = cursor.primaryKey;
                    if(index_key.includes('ask'))
                    {
                    objectStore.delete(index_key);
                    }
                    cursor.continue();
                }
                };
            }).catch((err) => {
                console.log('Error in deleting data store');
            });
        }

        function delete_jobs_into(){
		    getIntaoDb(dbName).then((db) => {
                let dataStoreName = JOBStore;
                const transaction = db.transaction(dataStoreName, 'readwrite');
                const objectStore = transaction.objectStore(dataStoreName);
                const request = objectStore.openCursor();
                request.onsuccess = (event) => {
                const cursor = event.target.result;
                if (cursor) {
                    const index_key = cursor.primaryKey;
                    if(index_key.includes('job'))
                    {
                    objectStore.delete(index_key);
                    }
                    cursor.continue();
                }
                };
            }).catch((err) => {
                console.log('Error in deleting data store');
            });
        }

        function geteventlistdata() {
            loader(true, event_loaderArea);
            // Open or create a database
            getIntaoDb(dbName).then((db) => {

                var event_list_hash = 'events_club'+events_currentPage+geoHash+term+events_totalItems+events_search+events_itemsPerPage+events_currentPage;
                event_list_name = 'events_club_'+crc32(event_list_hash+hires_slug);
                checkclubTTL(event_list_name, events_store_name).then(() => {
                    const datareventequest = db.transaction(events_store_name).objectStore(events_store_name).get(event_list_name); // get main data
                    datareventequest.onsuccess = ()=> {
                        console.log(datareventequest);
                        const eventstoredatares = datareventequest.result;
                        if(eventstoredatares !== undefined && eventstoredatares !== null && eventstoredatares !== "" && eventstoredatares !== "undefined" && eventstoredatares !== "null"){
                            const eventstoredata = datareventequest.result.values;
                            events_get_slug = true;
                            events_already_rendered = true;
                            render_events_grid_template(eventstoredata, events_list);
                        }else{
                            events_get_slug = false;
                            events_already_rendered = false;
                            taoh_events_init();
                        }
                    }
                });

            }).catch((error) => {
                console.log('Geteventlistdata Error:', error);
            });
	    }
        //alert(_taoh_site_ajax_url)
        function taoh_events_init(queryString = "") {
            var data = {
                'taoh_action': 'events_get',
                'ops': 'list',
                'call_from': 'club',
                'search': term,
                'geohash': geoHash,
                'offset': events_currentPage,
                'limit': events_itemsPerPage,
                'filters': queryString
            };
            jQuery.get(_taoh_site_ajax_url, data, function (response) {
                response = parseJSONSafely(response);
                <?php if(TAOH_INTAODB_ENABLE) { ?>
                    if(!events_get_slug){
                        indx_events_list(response);
                    }
                    if(!events_already_rendered){
                        render_events_grid_template(response, events_list);
                    }
                <?php }else{ ?>
                    render_events_grid_template(response, events_list);
                <?php } ?>
                render_events_grid_template(response, events_list);
            }).fail(function () {
                loader(false, event_loaderArea);
                console.log("Network issue!");

            })
        }

        function indx_events_list(eventlistdata){
            var event_taoh_data = { taoh_data:event_list_name,values : eventlistdata };
            let event_setting_time = new Date();
            event_setting_time = event_setting_time.setMinutes(event_setting_time.getMinutes() + 30);
            var event_setting_timedata = { taoh_ttl: event_list_name,time:event_setting_time };
            obj_data = { [events_store_name]:event_taoh_data,[TTLStore] : event_setting_timedata };
            Object.keys(obj_data).forEach(key => {
            // console.log(key, obj_data[key]);
                IntaoDB.setItem(key,obj_data[key]).catch((err) => console.log('Storage failed', err));
            });
            return false;
        } // indexed db form submit

        function render_events_grid_template(data, slot) {
            loader(false, event_loaderArea);
            slot.empty();
            if (data.output === false || data.success === false) {
                slot.append("<p>No data found!</p>");
                return false;
            }

            if (data.output.count == 0) {
                slot.append("<p>No data found!</p>");
                return false;
            }

            events_totalItems = data.output.count;

            let count = 0;
            const now = Date.now();
            $.each(data.output.list, function (i, v) {
                let additive = v.canonical_url?.trim() ? v.canonical_url : v.source;
                let is_expired = false;

                let user_timezone;
                if (isLoggedIn) {
                    user_timezone = '<?= taoh_user_timezone(); ?>';
                }
                if (!isLoggedIn || !user_timezone?.trim()) {
                    let clientTimeZone = typeof getCookie === 'function' ? getCookie('client_time_zone') : null;
                    user_timezone = convertDeprecatedTimeZone(clientTimeZone || Intl.DateTimeFormat().resolvedOptions().timeZone);
                }
                if (!isValidTimezone(user_timezone)) user_timezone = 'UTC';

                let event_timestamp_start_data = {
                    utc_datetime: v.utc_start_at,
                    local_datetime: v.local_start_at,
                    timezone: v.local_timezone,
                    locality: v.locality
                };
                let event_timestamp_end_data = {
                    utc_datetime: v.utc_end_at,
                    local_datetime: v.local_end_at,
                    timezone: v.local_timezone,
                    locality: v.locality
                };

                let localized_event_start_data = get_localized_event_data(event_timestamp_start_data, user_timezone);
                let localized_event_ends_data = get_localized_event_data(event_timestamp_end_data, user_timezone);

                arr_cont.push(v.eventtoken.toString());

                v.title = ucfirst(v.title);

                // var company_name_get = v.company.length ? v.company[0].name : '';

                var liked_check = get_liked_check_event(v.eventtoken, v.conttoken);
                var rsvped_token = 'rsvp_status_' + user_ptoken + '_' + v.eventtoken;

                let is_rsvp_done = false;
                let event_live_state;

                let btn_text = 'Register Now';
                let btn_class = 'btn-primary';
                let btn_icon = '<i class="fa fa-ticket mr-2" aria-hidden="true"></i>';
                let event_url = `<?php echo TAOH_SITE_URL_ROOT . "/events/d/"; ?>${convertToSlug(taoh_title_desc_decode(v.title))}-${v.eventtoken}?con=${v.conttoken}`;

                const eventEndDate = makeZonedInstant(localized_event_ends_data.datetime, localized_event_ends_data.timezone);
                if (now > eventEndDate) {
                    is_expired = true;
                    btn_text = 'Event expired';
                    btn_class = 'btn-secondary';
                    btn_icon = '<i class="fa fa-ticket mr-2" aria-hidden="true"></i>';
                } else {
                    if (isLoggedIn) {
                        event_live_state = eventLiveState(v.utc_start_at || '', v.utc_end_at || '', v.locality, user_timezone);
                        is_rsvp_done = jQuery.inArray(rsvped_token, rsvp_find) !== -1 || isEventTokenPresent(v.eventtoken);

                        const setButton = (text, cls, icon, url = '') => {
                            btn_text = text;
                            btn_class = cls;
                            btn_icon = icon;
                            if (url) event_url = url;
                        };

                        const isLive = event_live_state === 'live';

                        if (isLive) {
                            const liveText = is_rsvp_done
                                ? (isValidUser ? 'Live, Join Now!' : 'Live, Complete Settings to Join!')
                                : 'Live, Register Now!';
                            const liveUrl = is_rsvp_done
                                ? (isValidUser ? `<?php echo TAOH_SITE_URL_ROOT . "/events/chat/id/events/"; ?>${v.eventtoken}` : '<?php echo TAOH_SITE_URL_ROOT . "/settings"; ?>')
                                : '';
                            setButton(liveText, 'btn-success', `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80" style="width: 36px"><circle cx="40" cy="40" r="28" fill="#fff"></circle>

                                                                    <polygon points="34,28 34,52 54,40" fill="#28A745"></polygon>

                                                                    <path d="M78 26 C84 35, 84 46, 78 54" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>

                                                                    <path d="M88 10 C104 28, 104 54, 88 70" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>
                                                            </svg>`, liveUrl);
                        } else {
                            const regText = is_rsvp_done ? 'Registered!' : 'Register Now!';
                            const regClass = is_rsvp_done ? 'btn-warning' : 'btn-primary';
                            const regUrl = is_rsvp_done
                                ? (isValidUser ? `<?php echo TAOH_SITE_URL_ROOT . "/events/chat/id/events/"; ?>${v.eventtoken}` : '<?php echo TAOH_SITE_URL_ROOT . "/settings"; ?>')
                                : '';
                            setButton(regText, regClass, '<i class="fa fa-ticket mr-2" aria-hidden="true"></i>', regUrl);
                        }
                    } else {
                        btn_text = 'Login to Register';
                        btn_class = 'btn-primary';
                        btn_icon = '<i class="fa fa-ticket mr-2" aria-hidden="true"></i>';
                    }
                }

                let rsvp_link;
                if (isLoggedIn || is_expired) {
                    rsvp_link = `<a href="${event_url}" data-metrics="rsvp" class="btn d-flex align-items-center click_metrics ${btn_class}"
                                style="width: fit-content;">${btn_icon} <span>${btn_text}</span></a>`;
                } else {
                    rsvp_link = `<button type="button" class="btn create_referral ${btn_class}" id="register_ticket" data-location="${event_url}" data-title="${btoa(unescape(encodeURIComponent(v.title)))}" data-toggle="modal" data-target="#config-modal">${btn_icon} ${btn_text}</button>`;
                }

                var no_image = '<?php echo TAOH_SITE_URL_ROOT . "/assets/images/event.jpg" ?>';
                if (v.user_avatar != '') {
                    var sends_avatar = v.user_avatar;
                } else if (v.avatar != '') {
                    var sends_avatar = v.avatar;
                }

                var img = newavatardisplay(sends_avatar, v.avatar_image, '<?php echo TAOH_OPS_PREFIX;?>');

                let event_type = v.event_type ? (v.event_type).toLowerCase() : 'virtual';

                const costArray = v.ticket_types.map(ti => ti.price === 'paid' ? ti.cost : 0);
                const minCost = Math.min(...costArray);

                slot.append(
                    `<div style="flex: 1; flex-basis: 290px; max-width: 320px;" class="dash_metrics" data-metrics="view" conttoken="${v.eventtoken}" data-type="events" data-conttoken="${v.eventtoken}"
                        data-canonical="${additive}" event-url="${event_url}">
                        <div class="card event-listing-block-row p-0" style="">

                            <div class="club-event-container">
                                <a onclick="event.stopPropagation();" href="${_taoh_site_url_root}/events/d/${convertToSlug(taoh_title_desc_decode(v.title))}-${v.eventtoken}?con=${v.conttoken}">
                                <div class="club-events-bg" style="background-image: url(${v.event_image != '' ? v.event_image : no_image})"></div>
                                <div class="club-glass-overlay"></div>
                                <img class="club-event-image lazy" src="${v.event_image != '' ? v.event_image : no_image}" data-src="${v.event_image}" alt="${taoh_title_desc_decode(v.title)}"></a>
                           </div>
                            <div class="card-body p-3">

                                <span class="badge-detail-block w-100 d-inline-block flex-wrap-reverse justify-content-between">
                                    <ul class="d-flex flex-wrap" style="float: left;">
                                        <li class="mt-1 d-flex align-items-center" style="color: #ffffff; background: #2557A7; border-radius: 8px;">
                                            <svg class="mx-1" width="13" height="13" viewBox="0 0 7 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M2 0C2.27656 0 2.5 0.223437 2.5 0.5V1H4.5V0.5C4.5 0.223437 4.72344 0 5 0C5.27656 0 5.5 0.223437 5.5 0.5V1H6.25C6.66406 1 7 1.33594 7 1.75V2.5H0V1.75C0 1.33594 0.335938 1 0.75 1H1.5V0.5C1.5 0.223437 1.72344 0 2 0ZM0 3H7V7.25C7 7.66406 6.66406 8 6.25 8H0.75C0.335938 8 0 7.66406 0 7.25V3ZM5.14062 4.76562C5.2875 4.61875 5.2875 4.38125 5.14062 4.23594C4.99375 4.09063 4.75625 4.08906 4.61094 4.23594L3.12656 5.72031L2.39219 4.98594C2.24531 4.83906 2.00781 4.83906 1.8625 4.98594C1.71719 5.13281 1.71562 5.37031 1.8625 5.51562L2.8625 6.51562C3.00937 6.6625 3.24688 6.6625 3.39219 6.51562L5.14062 4.76562Z" fill="white"/>
                                            </svg>

                                            <span class="mx-1">${beautifyTime(localized_event_start_data.datetime, localized_event_start_data.timezone, '{week}, {month} {day}, {year}, {time} {abbr}')}</span>
                                        </li>
                                    </ul>
                                    <span class="bookmark-icon-right">${liked_check}</span>
                                </span>

                                <h4 class="my-2 b-2 fs-18" style="font-weight: 500;height: 43px;overflow: hidden;"><a href="${event_url}" data-metrics="view" class="click_metrics">${taoh_title_desc_decode(v.title)}</a></h4>
                                <p class="event-location">${event_type != 'virtual' && v.full_location ? newgenerateLocationHTML(v.full_location) : 'Attend Online'}</p>

                                <div class="event-price mb-2">
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16.0268 0.500011L16.0268 0.500014H16.0249H9.87747C9.30941 0.500014 8.76623 0.72384 8.36704 1.12303L1.12507 8.365C0.291641 9.19842 0.291643 10.5483 1.12507 11.3817L6.61827 16.8749C7.4517 17.7084 8.80159 17.7084 9.63502 16.8749L16.877 9.63297C17.2762 9.23379 17.5 8.6906 17.5 8.12254V1.97098C17.5 1.15635 16.8392 0.496945 16.0268 0.500011ZM12.1069 3.31981C12.4476 2.97911 12.9096 2.7877 13.3915 2.7877C13.8733 2.7877 14.3354 2.97911 14.6761 3.31981C15.0168 3.66051 15.2082 4.1226 15.2082 4.60443C15.2082 5.08625 15.0168 5.54834 14.6761 5.88904C14.3354 6.22974 13.8733 6.42115 13.3915 6.42115C12.9096 6.42115 12.4476 6.22974 12.1069 5.88904C11.7662 5.54834 11.5748 5.08625 11.5748 4.60443C11.5748 4.1226 11.7662 3.66051 12.1069 3.31981Z" stroke="#212121"/>
                                    </svg>
                                    <span>From ${minCost == '0' ? '$0 (free)' : '$' + minCost}</span>
                                </div>

                                <div class="type_display event-type mb-2">
                                    ${(event_type == 'in-person') ? `<svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14.8079 3.56198C15.2836 3.56198 15.7399 3.37434 16.0762 3.04034C16.4126 2.70634 16.6016 2.25334 16.6016 1.78099C16.6016 1.30864 16.4126 0.85564 16.0762 0.52164C15.7399 0.187639 15.2836 0 14.8079 0C14.3322 0 13.876 0.187639 13.5396 0.52164C13.2033 0.85564 13.0143 1.30864 13.0143 1.78099C13.0143 2.25334 13.2033 2.70634 13.5396 3.04034C13.876 3.37434 14.3322 3.56198 14.8079 3.56198ZM11.6579 7.43934C11.6952 7.4245 11.7289 7.40966 11.7662 7.39482L11.1347 9.75091C10.9255 10.5338 11.131 11.3686 11.684 11.966L14.3259 14.8267L15.148 18.0956C15.3087 18.7301 15.9589 19.1197 16.5978 18.9601C17.2368 18.8006 17.6292 18.155 17.4685 17.5205L16.6091 14.1032C16.5381 13.8138 16.3923 13.5504 16.1905 13.3314L14.3408 11.3278L15.062 8.89752L15.4208 9.75091C15.5852 10.1442 15.8879 10.467 16.2727 10.66L17.2705 11.1534C17.8609 11.4466 18.5783 11.2091 18.8735 10.6229C19.1687 10.0366 18.9296 9.32422 18.3392 9.0311L17.5358 8.63409L16.964 7.26866C16.3213 5.74369 14.8192 4.7493 13.1526 4.7493C12.3006 4.7493 11.4598 4.9274 10.6826 5.26876L10.3836 5.39862C9.15423 5.94034 8.21257 6.97183 7.79032 8.23707L7.69316 8.52649C7.48391 9.14983 7.82395 9.82141 8.44799 10.0292C9.07202 10.237 9.75211 9.89933 9.96137 9.2797L10.0585 8.99028C10.2715 8.35581 10.7424 7.84377 11.3552 7.57291L11.6541 7.44305L11.6579 7.43934ZM10.5368 12.4521L9.60264 14.7674L7.38301 16.9713C6.91592 17.4351 6.91592 18.1884 7.38301 18.6522C7.85011 19.1159 8.60867 19.1159 9.07576 18.6522L11.3813 16.3628C11.5532 16.1922 11.6878 15.9881 11.7774 15.7655L12.3193 14.4223L10.7984 12.7749C10.705 12.6747 10.619 12.5671 10.5368 12.4558V12.4521ZM8.23126 10.1702C7.94353 10.0069 7.58106 10.1034 7.41291 10.3891L6.21715 12.4447L4.18435 11.2796C3.61263 10.9531 2.88023 11.146 2.55139 11.7137L0.159872 15.8285C-0.168962 16.3962 0.025349 17.1235 0.597072 17.45L2.66723 18.6373C3.23896 18.9638 3.97136 18.7709 4.30019 18.2032L6.69171 14.0884C6.74777 13.9919 6.78887 13.8954 6.81503 13.7915L8.45172 10.9828C8.61614 10.6971 8.51899 10.3372 8.23126 10.1702Z" fill="#2557A7"/>
                                    </svg>` : (event_type == 'hybrid') ? `<svg width="25" height="25" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.48083 0.251601C6.84598 -0.0838669 7.40707 -0.0838669 7.76926 0.251601L13.9442 5.95158C14.1431 6.13564 14.25 6.39095 14.25 6.64923H9.97503C9.408 6.64923 8.89738 6.89861 8.55004 7.29345V6.17424C8.55004 5.91299 8.33629 5.69924 8.07504 5.69924H6.17505C5.9138 5.69924 5.70005 5.91299 5.70005 6.17424V8.07423C5.70005 8.33548 5.9138 8.54923 6.17505 8.54923H8.07504V12.3492H3.32506C2.53834 12.3492 1.90006 11.7109 1.90006 10.9242V7.59923H0.950065C0.558192 7.59923 0.20788 7.35876 0.0653809 6.99658C-0.0771186 6.63439 0.0178811 6.21877 0.305849 5.95158L6.48083 0.251601ZM10.45 9.02422V13.2992H16.15V9.02422H10.45ZM9.02504 8.54923C9.02504 8.02376 9.44957 7.59923 9.97503 7.59923H16.625C17.1505 7.59923 17.575 8.02376 17.575 8.54923V13.2992H18.525C18.7863 13.2992 19 13.513 19 13.7742C19 14.5609 18.3617 15.1992 17.575 15.1992H16.15H10.45H9.02504C8.23832 15.1992 7.60004 14.5609 7.60004 13.7742C7.60004 13.513 7.81379 13.2992 8.07504 13.2992H9.02504V8.54923Z" fill="#406CB2"/>
                                    </svg>` : `<svg width="20" height="13" viewBox="0 0 20 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 2.16667C0 0.971615 0.996528 0 2.22222 0H11.1111C12.3368 0 13.3333 0.971615 13.3333 2.16667V10.8333C13.3333 12.0284 12.3368 13 11.1111 13H2.22222C0.996528 13 0 12.0284 0 10.8333V2.16667ZM19.4132 1.21198C19.7743 1.40156 20 1.76719 20 2.16667V10.8333C20 11.2328 19.7743 11.5984 19.4132 11.788C19.0521 11.9776 18.6146 11.9573 18.2708 11.7339L14.9375 9.56719L14.4444 9.24557V8.66667V4.33333V3.75443L14.9375 3.43281L18.2708 1.26615C18.6111 1.04609 19.0486 1.0224 19.4132 1.21198Z" fill="#2557A7"></path>
                                    </svg>`}

                                    <span>${event_type}</span>
                                </div>
                                <div class="mt-3">
                                    ${rsvp_link}
                                </div>
                            </div>
                        </div>
                    </div>
			    `);

                count++;
                if (count >= 4) return false; // break after 4 items
            });

        }

        function show_events_pagination(holder) {
            return $(holder).pagination({
                items: events_totalItems,
                itemsOnPage: events_itemsPerPage,
                currentPage: events_currentPage,
                onInit: function() {
                    $("#pagination ul").addClass('pagination');
                    $("#pagination ul li.disabled").addClass('page-link text-gray');
                    $("#pagination ul li.active").addClass('page-link bg-primary text-white');
                },
                onPageClick: function(pageNumber, event) {
                    $("#pagination ul").addClass('pagination');
                    $("#pagination ul li.disabled").addClass('page-link text-gray');
                    $("#pagination ul li.active").addClass('page-link bg-primary text-white');
                    events_currentPage = pageNumber;
                    taoh_events_init();
                }
            });
        }

        function getCookie(cname) {
            let name = cname + "=";
            let decodedCookie = decodeURIComponent(document.cookie);
            let ca = decodedCookie.split(';');
            for(let i = 0; i <ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }

        /* /Events */

        /* Jobs */
        function getjoblistdata() {
            loader(true, job_loaderArea);
            // Open or create a database
            getIntaoDb(dbName).then((db) => {
                var job_list_hash = jobs_totalItems+jobs_search+jobs_itemsPerPage+jobs_currentPage;
                job_list_name = 'jobs_club_'+crc32(job_list_hash+hires_slug);
                console.log(job_list_name);
                checkclubTTL(job_list_name, jobs_store_name).then(() => {
                    const datarjobequest = db.transaction(jobs_store_name).objectStore(jobs_store_name).get(job_list_name); // get main data
                    datarjobequest.onsuccess = ()=> {
                        console.log(datarjobequest);
                        const jobstoredatares = datarjobequest.result;
                        if(jobstoredatares !== undefined && jobstoredatares !== null && jobstoredatares !== "" && jobstoredatares !== "undefined" && jobstoredatares !== "null"){
                            console.log('ifff');
                            const jobstoredata = datarjobequest.result.values;
                            jobs_get_slug = true;
                            jobs_already_rendered = true;
                            render_jobs_template(jobstoredata, jobs_list);
                        }else{
                            jobs_get_slug = false;
                            jobs_already_rendered = false;
                            taoh_jobs_init();
                        }
                    }
                });

            }).catch((error) => {
                console.log('Getjoblistdata Error:', error);
            });
        }

        function indx_jobs_list(joblistdata) {
            let job_setting_time = new Date();
            job_setting_time = job_setting_time.setMinutes(job_setting_time.getMinutes() + 30);

            IntaoDB.setItem(jobs_store_name, {taoh_data: job_list_name, values: joblistdata});
            IntaoDB.setItem(TTLStore, {taoh_ttl: job_list_name, time: job_setting_time});

            return false;
        }

        function taoh_jobs_init (queryString=""){
            var geohash = '';
            //geohash = geohashInput.val();
            var data = {
                'taoh_action': 'jobs_get',
                'ops': 'list',
                'search': jobs_search,
                'geohash': geohash,
                'offset': jobs_currentPage - 1,
                'limit': jobs_itemsPerPage,
                'filters': queryString
            };
            jQuery.post(_taoh_site_ajax_url, data, function(response) {
                    <?php if(TAOH_INTAODB_ENABLE) { ?>
                        if(!jobs_get_slug){
                            indx_jobs_list(response);
                        }
                        if(!jobs_already_rendered){
                            render_jobs_template(response, jobs_list);
                        }
                    <?php }else{ ?>
                        render_jobs_template(response, jobs_list);
                    <?php } ?>
            }).fail(function() {
                loader(false, job_loaderArea);
                console.log( "Network issue!" );
            })
        }


	function getCurrencySymbol(index) {
        if (index >= 0 && index < currencies.length) {
            return currencies[index].symbol;
        } else {
            return '';
        }
    }

        function render_jobs_template(data, slot) {
            loader(false, job_loaderArea);
            slot.empty();
            if (data.output === false || data.success === false) {
                slot.append("<p>No data found!</p>");
                return false;
            }

            if (data.output.count == 0) {
                slot.append("<p>No data found!</p>");
                return false;
            }

            var result = format_object(data);

            $.each(result.output.list, function(i, v){
                console.log("jobs_list",  result.output.list);

                var additive = '';
                if(v.canonical_url && v.canonical_url !='' && v.canonical_url != undefined){
                    additive = v.canonical_url;
                }
                else{
                    additive = v.source;
                }
                    var job_url = convertToSlug(taoh_title_desc_decode(v.title))+'-'+v.conttoken;

                    arr_cont.push(v.conttoken.toString());

                    v.title = ucfirst(v.title);

                    var company_name_get = v.company.length ? v.company[0].name : '';

                    var liked_check = get_liked_check(v.conttoken,'announcement');

                    var apply_email_link = '';
                    var show_scout_logo = '';

                    var btnCaption = "Apply";
                    if (applied_jobs && applied_jobs.hasOwnProperty(v.conttoken)) {
                        btnCaption = "Applied";
                    }

                    if(isLoggedIn){
                        if(v.ptoken != user_ptoken){
                            if(v.enable_scout_job == 'on'){

                                apply_email_link = `<a job-url="${job_url}" data-conttoken="${v.conttoken}" data-metrics="request_through_scout_link" class="btn theme-btn mb-3 click_metrics"
                                        style="background-color:#FF7311">Apply through Scout</a>`;

                            }else{
                                if(v.apply_link){
                                        apply_email_link = `<a onclick="event.stopPropagation();" href="${v.apply_link}" target="_blank" class="btn theme-btn w-50 mb-3">Apply Now </a>`;
                                    }else if((v.email) && (v.enable_apply)){
                                        apply_email_link = `<a data-position="${(v.title)}" data-company="${(company_name_get)}" data-fname="${(v.fname)}" data-toemail="${(v.email)}" data-conttoken="${(v.conttoken)}" data-placeType="${renderJobType(v.placeType)}" data-description="${(v.description)}" class="btn theme-btn w-50 mb-3 open_modal">Apply Now </a>`;
                                    }else{
                                        apply_email_link = `<a onclick="event.stopPropagation();" href="${'mailto:'+v.email}" class="btn theme-btn w-50 mb-3">Apply Now </a>`;
                                    }
                            }
                        }

                        apply_email_link = `<a class="btn theme-btn mb-3 create_referral" data-title="${taoh_title_desc_decode(v.title)}" href="${_taoh_site_url_root}/jobs/d/${job_url}">${btnCaption} </a>`;

                        if(v.enable_scout_job == 'on'){
                            show_scout_logo = `<a style="margin-left: 5px">
                            <img
                            data-toggle="tooltip" data-placement="top"
                        title="Please note: Scout is a specialized program that gets 6x faster result, where industry leading peers help find the best peer talent for the jobs. "

                            src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/scout_icon.png'; ?>" width="28" height="28" alt="Scout Icon"></a>`;
                        }
                    }else{
                        apply_email_link = `<a class="btn theme-btn mb-3 create_referral" data-title="${taoh_title_desc_decode(v.title)}" href="${_taoh_site_url_root}/jobs/d/${job_url}">${btnCaption} </a>`;
                    }

                    /*var payinfo = '&nbsp;';
                    if(v.payinfo && v.country_code){
                        var country_code = v.country_code - 1;
                        var cc = getCurrencySymbol(country_code);
                        if(v.paymentTerm == 'hourly'){
                            payinfo =  + cc + v.payinfo + ' per hour';
                        }else if(v.paymentTerm == 'monthly'){
                            payinfo = cc+ ' ' + v.payinfo + ' per month';
                        }else if(v.paymentTerm == 'annualy'){
                            payinfo = cc + ' ' + v.payinfo + ' per year';
                        }else if(v.paymentTerm == 'project'){
                            payinfo =  cc + ' ' + v.payinfo + ' per project';
                        }else{
                            payinfo = cc + ' ' + v.payinfo + ' per week';
                        }
                    }*/

                    var payinfo = '';
                    if(v.payinfo && v.country_code && v.payinfo !='' && v.country_code!= ''){
                        var country_code = v.country_code - 1;
                        payinfo = getCurrencySymbol(country_code) + ' ' + v.payinfo;
                        if(v.paymentTerm == 'hourly'){
                            payinfo = payinfo + ' per hour';
                        }else if(v.paymentTerm == 'monthly'){
                            payinfo = payinfo + ' per month';
                        }else if(v.paymentTerm == 'annualy'){
                            payinfo = payinfo + ' per year';
                        }else if(v.paymentTerm == 'project'){
                            payinfo = payinfo + ' per project';
                        }else if(v.paymentTerm == 'daily'){
                            payinfo = payinfo + ' per Daily';
                        }else if(v.paymentTerm == 'weekly'){
                            payinfo = payinfo + ' per week';
                        }
                    }
                    slot.append(
                    `<div style="flex: 1; flex-basis: 290px; max-width: 320px;"
                    class="dash_metrics"
                    data-metrics="view"
                    conttoken="${v.conttoken}" data-type="jobs"

                    data-conttoken="${v.conttoken}"
                    data-canonical = "${additive}"
                    job-url="${job_url}" >
                        <div class="job-listing-block-row">
                            <div><b class="jobing-company-name stop_propagation">${(v.company && v.company.length)? newgenerateCompanyHTML(v.company): '&nbsp;'}</b> <span class="bookmark-icon-right">${liked_check}</span></div>
                                <a href="${_taoh_site_url_root}/jobs/d/${job_url}"><h3 class="fs-17 mt-2 b-2" style="height:65px;font-weight: 500;overflow:hidden;">${taoh_title_desc_decode(v.title)} ${show_scout_logo}  </h3></a>
                                <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis">${v.full_location ? newgenerateLocationHTML(v.full_location): ''}</p>
                                <p>${payinfo ? payinfo : '&nbsp'} </p>

                                <div class="mt-3">
                                    ${apply_email_link}
                                </div>
                            </div>
                        </div>
                    </div>
                    `);
            });
            if(data.output.total >= 11) {
                show_jobs_pagination('#pagination')
            }
        }

        function show_jobs_pagination(holder) {
            return $(holder).pagination({
                items: jobs_totalItems,
                itemsOnPage: jobs_itemsPerPage,
                currentPage: jobs_currentPage,
                displayedPages: 3,
                onInit: function() {
                    $("#pagination ul").addClass('pagination');
                    $("#pagination ul li.disabled").addClass('page-link text-gray');
                    $("#pagination ul li.active").addClass('page-link bg-primary text-white');
                },
                onPageClick: function(pageNumber, event) {
                    $("#pagination ul").addClass('pagination');
                    $("#pagination ul li.disabled").addClass('page-link text-gray');
                    $("#pagination ul li.active").addClass('page-link bg-primary text-white');
                    jobs_currentPage = pageNumber;
                    taoh_jobs_init();
                }
            });
        }

        /* /Jobs */

        /* /Reads */
        function getreadslistdata(){
            loader(true, read_loaderArea);
            // Open or create a database
            getIntaoDb(dbName).then((db) => {
                var reads_list_hash = reads_totalItems+reads__search+reads_itemsPerPage+reads_currentPage;
                read_list_name = 'reads_club_'+crc32(reads_list_hash+hires_slug);
                console.log(read_list_name);
                checkclubTTL(read_list_name, reads_store_name).then(() => {
                    const datareadsrequest = db.transaction(reads_store_name).objectStore(reads_store_name).get(read_list_name); // get main data
                    datareadsrequest.onsuccess = ()=> {
                        console.log(datareadsrequest);
                        const readsstoredatares = datareadsrequest.result;
                        if(readsstoredatares !== undefined && readsstoredatares !== null && readsstoredatares !== "" && readsstoredatares !== "undefined" && readsstoredatares !== "null"){
                            const readsstoredata = datareadsrequest.result.values;
                            console.log('ifff');
                            reads_already_rendered = true;
                            render_blog_template(readsstoredata, reads_list);
                        }else{
                            reads_already_rendered = false
                            taoh_blogs_init();
                        }
                    }
                });
            }).catch((error) => {
            console.log('Getreadslistdata Error:', error);
            });
        }

        function indx_reads_list(readslistdata){
            var reads_taoh_data = { taoh_data:read_list_name,values : readslistdata };
            let reads_setting_time = new Date();
            reads_setting_time = reads_setting_time.setMinutes(reads_setting_time.getMinutes() + 600);
            var reads_setting_timedata = { taoh_ttl: read_list_name,time:reads_setting_time };
            obj_data = { [reads_store_name]:reads_taoh_data,[TTLStore] : reads_setting_timedata };
            Object.keys(obj_data).forEach(key => {
            // console.log(key, obj_data[key]);
                IntaoDB.setItem(key,obj_data[key]).catch((err) => console.log('Storage failed', err));
            });
            return false;
        } // indexed db form submit

        function taoh_blogs_init(queryString="") {
            var data = {
                'taoh_action': 'taoh_central_get',
                'ops': 'list',
                'offset': reads_currentPage,
                'limit': reads_itemsPerPage,
                'filters': queryString,

            };
            jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
                <?php if(TAOH_INTAODB_ENABLE) {?>
                    if(!reads_already_rendered){
                        indx_reads_list(response);
                        render_blog_template(response, reads_list);
                    }
                <?php }else{?>
                    render_blog_template(response, reads_list);
                <?php }?>
            }).fail(function() {
                console.log( "Network issue!" );
                loader(false, read_loaderArea);
            })
        }

        function render_blog_template(data, slot) {
            loader(false, read_loaderArea);
            slot.empty();
            var type_num = typeof(data.output.num_rows);
            if (data.output === false || data.success === false) {
                slot.append("<p>No data found!</p>");
                return false;
            }

            //alert(data.output.num_rows)
            if (data.output.count == 0) {
                slot.append("<p>No data found!</p>");
                return false;
            }
            if(data.output === false || type_num === 'object') {
                slot.append('<p class="fs-20 text-black">No posts to display!</p>');
                return false;
            }
            reads_totalItems = data.output.num_rows;
            // console.log('---v.title--------',data.output.list)
            $.each(data.output.list, function(i, v){

                console.log('---v.title--------',v.title)
                console.log('---v.blurb.description--------',v.blurb);
                if(v.blurb =='' ||  v.blurb == null ||  v.blurb == undefined){
                    return;
                }


                arr_cont.push(v.conttoken.toString());
                var prefix = '<?php echo TAOH_CDN_PREFIX ?>';
                if(v.blurb !='' && v.blurb !=undefined && v.blurb.media_type == 'youtube'){
                    var video_id = getYoutubeId(v.blurb.media_url);
                    v.blurb.image = "http://img.youtube.com/vi/"+video_id+"/maxresdefault.jpg";
                    var image_div = `<div class="company-details-panel mb-30px" id="company-videos">
                        <div class="pt-3 video-box">
                            <img class="w-100 rounded-rounded lazy" src="http://img.youtube.com/vi/${video_id}/maxresdefault.jpg" data-src="http://img.youtube.com/vi/${video_id}/maxresdefault.jpg" alt="video image">
                            <div class="video-content">
                                <a class="icon-element hover-y mx-auto blog_video" href="javascript:void(0);" onClick="call_iframe(event, this);" data-video="${video_id}" data-fancybox="" title="Play Video" style="padding-left:5px;">
                                <svg width="24" height="24" version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 58.752 58.752" xml:space="preserve" style="margin-top: -3px;">
                                    <path fill="#0d233e" d="M52.524,23.925L12.507,0.824c-1.907-1.1-4.376-1.097-6.276,0C4.293,1.94,3.088,4.025,3.088,6.264v46.205
                                    c0,2.24,1.204,4.325,3.131,5.435c0.953,0.555,2.042,0.848,3.149,0.848c1.104,0,2.192-0.292,3.141-0.843l40.017-23.103
                                    c1.936-1.119,3.138-3.203,3.138-5.439C55.663,27.134,54.462,25.05,52.524,23.925z M49.524,29.612L9.504,52.716
                                    c-0.082,0.047-0.18,0.052-0.279-0.005c-0.084-0.049-0.137-0.142-0.137-0.242V6.263c0-0.1,0.052-0.192,0.14-0.243
                                    c0.042-0.025,0.09-0.038,0.139-0.038c0.051,0,0.099,0.013,0.142,0.038l40.01,23.098c0.089,0.052,0.145,0.147,0.145,0.249
                                    C49.663,29.47,49.611,29.561,49.524,29.612z"></path>
                                </svg>
                                </a>
                            </div>
                        </div>
                    </div>`;
                }else{
                    var title_name = decodeURIComponent(decode(v.title)).replace(/\+/g, ' ');
                    //v.blurb.image = prefix+"/images/igcache/"+encodeURIComponent(title_name)+"//900_600/blog.jpg";
                    var blurb_img = prefix+"/images/igcache/"+(v.title)+"/900_600/blog.jpg?width=200";
                    //var blurb_img = prefix+"/images/igcache/".urlencode( $title )."//900_600/blog.jpg";
                    var image_div = `<div class="cl-image">
                        <a href="${_taoh_site_url_root}/learning/blog/${convertToSlug(v.title)}-${v.conttoken}" rel="bookmark">
                        <img class="title-hover" src="${blurb_img}" alt="${v.title}" style="border-radius: 12px 12px 0 0;"></a>
                        </div>`;
                }
                console.log('descp ---------- ',v.blurb.description);
                console.log('descp ---------- ',v);
                let Str = decode(v.blurb.description);
                let decodedStr = decodeURIComponent(Str).replace(/\+/g, ' ');
                //let decodedStr = decodedStr_1.replace(/(<([^>]+)>)/ig,"");
                var title_name = decodeURIComponent(decode(v.title)).replace(/\+/g, ' ');
                console.log('decode descp ----aaaa------ ',decodedStr);
                //alert({cleanFontstyle(decodedStr)});
                slot.append(`
                  <div style="cursor: pointer; flex: 1; flex-basis: 290px; max-width: 320px; min-height: 460px;"
                    class="dash_metrics"
                    data-metrics="view"
                    conttoken="${v.conttoken}" data-type="reads"

                    >

                        <div class="reads-listing-block-row p-0 d-flex flex-column justify-content-between">
                            <div>
                                <div class="td-post-image">
                                        ${image_div}
                                </div>
                                <div class="p-3">
                                <div class="item-details" style="height: 46px; margin-bottom: 10px;overflow: hidden">
                                    <a href="${_taoh_site_url_root}/learning/blog/${convertToSlug(v.title)}-${v.conttoken}"><h3 class="fs-17 mt-2 b-2" style="font-weight: 500;">
                                        ${cleanFontstyle(title_name)}
                                    </h3></a>
                                </div>
                                <div>
                                    <a class="line-clamp-4" href="${_taoh_site_url_root}/learning/blog/${convertToSlug(v.title)}-${v.conttoken}">
                                        ${cleanFontstyle(decodedStr)}
                                    </a>
                                </div>

                                </div>
                            </div>
                            <a class="p-3" href="${_taoh_site_url_root}/learning/blog/${convertToSlug(v.title)}-${v.conttoken}" style="font-weight: 700;">Read More...</a>
                        </div>
                  </div>
                  `
                );

            });

        }

        /* /Reads */

        /* Asks */
        function getasklistdata() {
            loader(true, ask_loaderArea);
            // Open or create a database
            getIntaoDb(dbName).then((db) => {

                var ask_list_hash = asks_totalItems+asks_search+asks_itemsPerPage+asks_currentPage;
                ask_list_name = 'asks_club_'+crc32(ask_list_hash+hires_slug);
                console.log(ask_list_name);
                checkclubTTL(ask_list_name, asks_store_name).then(() => {
                    const dataraskequest = db.transaction(asks_store_name).objectStore(asks_store_name).get(ask_list_name); // get main data
                    dataraskequest.onsuccess = ()=> {
                        console.log(dataraskequest);
                        const askstoredatares = dataraskequest.result;
                        if(askstoredatares !== undefined && askstoredatares !== null && askstoredatares !== "" && askstoredatares !== "undefined" && askstoredatares !== "null"){
                            console.log('ifff');
                            const askstoredata = dataraskequest.result.values;
                            asks_get_slug = true;
                            asks_already_rendered = true;
                            render_asks_template(askstoredata, asks_list);
                        }else{
                            asks_get_slug = false;
                            asks_already_rendered = false;
                            taoh_asks_init();
                        }
                    }
                });

            }).catch((error) => {
            console.log('Getasklistdata Error:', error);
            });
        }

        function taoh_asks_init() {

            geohash = '';//geohashInput.val();
            var data = {
                'taoh_action': 'asks_get',
                'search': asks_search,
                'offset': asks_currentPage - 1,
                'limit': asks_itemsPerPage,
                'geohash': geohash
            };
            jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
                <?php if(TAOH_INTAODB_ENABLE) { ?>
                    if(!asks_get_slug){
                        indx_asks_list(response);
                    }
                    if(!asks_already_rendered){
                        render_asks_template(response, asks_list);
                    }
                <?php }else{ ?>
                    render_asks_template(response, asks_list);
                <?php } ?>
            }).fail(function() {
                loader(false, ask_loaderArea);
                console.log( "Network issue!" );
            })
        }

        function indx_asks_list(asklistdata){
            var ask_taoh_data = { taoh_data:ask_list_name,values : asklistdata };
            let ask_setting_time = new Date();
            ask_setting_time = ask_setting_time.setMinutes(ask_setting_time.getMinutes() + 30);
            var ask_setting_timedata = { taoh_ttl: ask_list_name,time:ask_setting_time };
            obj_data = { [asks_store_name]:ask_taoh_data,[TTLStore] : ask_setting_timedata };
            Object.keys(obj_data).forEach(key => {
            // console.log(key, obj_data[key]);
                IntaoDB.setItem(key,obj_data[key]).catch((err) => console.log('Storage failed', err));
            });
            return false;
        } // indexed db form submit

        function render_asks_template(data, slot) {
            loader(false, ask_loaderArea);
            slot.empty();
            if (data.output === false || data.success === false) {
                slot.append("<p>No data found!</p>");
                return false;
            }

            if (data.output.count == 0) {
                slot.append("<p>No data found!</p>");
                return false;
            }
            totalItems = data.output.total;
            result = format_object(data);
            //console.log('format', data);
            $.each(result.output.list, function(i, v){
                console.log('-----------',v.canonical_url);
				var additive = '';
				additive = v.source;
				var ask_url = convertToSlug(taoh_title_desc_decode(v.title))+'-'+v.conttoken;

				arr_cont.push(v.conttoken.toString());

				v.title = ucfirst(v.title);


				var liked_check = get_liked_check_ask(v.conttoken,'asks');

				var answer_link = '';


				if(isLoggedIn){
					answer_link = `<a href="${_taoh_site_url_root}/asks/d/${ask_url}" class="btn theme-btn w-50 mb-3 post_answer" >Answer</a>`;
				}else{
					answer_link = `<a href="${_taoh_site_url_root}/asks/d/${ask_url}" class="btn theme-btn w-50 mb-3 post_answer" >Answer</a>`;
				}
                var display_name = 'Hires';
				if(v.user_fname != undefined ){
                   display_name = v.user_fname; // user_chatname
                }
                if(v.user_avatar != ''){
                    var send_avatar = v.user_avatar;
                }else if(v.avatar != ''){
                    var send_avatar = v.avatar;
                }
				var img = newavatardisplay(send_avatar,v.avatar_image,'<?php echo TAOH_OPS_PREFIX;?>');
				slot.append(
				`<div style="flex: 1; flex-basis: 290px; max-width: 320px;"
                class="dash_metrics"
                data-metrics="view"
                conttoken="${v.conttoken}" data-type="asks"
				data-conttoken="${v.conttoken}"
				data-canonical = "${additive}"
				ask-url="${ask_url}" >
					<div class="ask-listing-block-row">
						<div>
                            <b onclick="event.stopPropagation();" class="asking-company-name">
								Posted by, ${display_name}
								${img}
							</b>
							<span class="bookmark-icon-right">${liked_check}</span></div>
							<a href="${_taoh_site_url_root}/asks/d/${ask_url}"><h3 class="fs-17 mt-2 b-2" style="font-weight: 500; height: 65px;overflow: hidden;">${taoh_title_desc_decode(v.title)} </h3></a>
							<p style="height:50px;overflow:hidden;">${v.full_location ? newgenerateLocationHTML(v.full_location): ''}</p>
							<div style="display:none" class="skill-detail-block"> <ul class="">
								${(v.skill && v.skill.length > 0)? newgenerateSkillHTMLForAsk(v.skill): ''}
							</ul></div>
							<div class="mt-3">
								${answer_link}
							</div>
						</div>
					</div>
				</div>`);


            });
            //if(totalItems >= 11) { enable to hide pagination if no date below 10
            show_asks_pagination('#pagination')
            //}
        }

        function show_asks_pagination(holder) {
            return $(holder).pagination({
                items: asks_totalItems,
                itemsOnPage: asks_itemsPerPage,
                currentPage: asks_currentPage,
                onInit: function() {
                    $("#pagination ul").addClass('pagination');
                    $("#pagination ul li.disabled").addClass('page-link text-gray');
                    $("#pagination ul li.active").addClass('page-link bg-primary text-white');
                },
                onPageClick: function(pageNumber, event) {
                    $("#pagination ul").addClass('pagination');
                    $("#pagination ul li.disabled").addClass('page-link text-gray');
                    $("#pagination ul li.active").addClass('page-link bg-primary text-white');
                    asks_currentPage = pageNumber;
                    taoh_asks_init();
                }
            });
        }

        /* /Asks */

        function checkclubTTL(index_name, store_name = dataStore) {
            const clTTLStoreName = objStores.ttl_store.name;
            return getIntaoDb(dbName).then((db) => {
                if (db.objectStoreNames.contains(clTTLStoreName)) {
                    return new Promise((resolve) => {
                        const clrequest = db.transaction(clTTLStoreName).objectStore(clTTLStoreName).get(index_name);
                        clrequest.onsuccess = () => {
                            const clTTLdata = clrequest.result;
                            if (clTTLdata) {
                                let current_time = new Date().getTime();

                                // Check if TTL exists or not (5 minutes)
                                if (current_time > clTTLdata.time) {
                                    let obj_data = {
                                        [store_name]: '',
                                        [objStores.ttl_store.name]: '',
                                        [objStores.api_store.name]: ''
                                    };
                                    const removePromises = Object.keys(obj_data).map(key => {
                                        return IntaoDB.removeItem(key, index_name);
                                    });

                                    // Wait for all removeItem calls to complete
                                    Promise.all(removePromises)
                                        .then(() => {
                                            console.log('Items stored');
                                            resolve(); // Resolve the promise here
                                        })
                                        .catch((err) => {
                                            console.log('Storage failed', err);
                                            resolve(); // Still resolve the promise
                                        });
                                } else {
                                    console.log('TTL is not expired');
                                    resolve(); // Resolve if TTL is not expired
                                }
                            } else {
                                resolve(); // Resolve if no TTL data found
                            }
                        };
                    });
                } else {
                    return Promise.resolve(); // Resolve if store does not exist
                }
            });
        }

    </script>

<script>
    // Scroll container and buttons
    const scrollContainer = document.querySelector('.scroll-container');
    const scrollLeftButton = document.getElementById('scroll-left');
    const scrollRightButton = document.getElementById('scroll-right');

    function taoh_feed_init () {

        let loaderArea = $('#announcementlistloaderArea');

        loader(true, loaderArea);
        let listArea = $('#announementlistArea');
		var data = {
            'taoh_action': 'taoh_get_feed_list',
            'ops': 'all',
            'type': 'announcement',
            'search': '',
            'offset': 0,
            'limit': 3,
		};
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
			render_feed_template(response, listArea);
		    loader(false, loaderArea);
		}).fail(function() {
			loader(false, loaderArea);
			console.log( "Network issue!" );
		})
  	}

    function render_feed_template(data, slot) {
		slot.empty();
        let token = '<?php echo taoh_get_dummy_token(); ?>';
        let isLoggedIn = "<?php echo taoh_user_is_logged_in(); ?>";
        let is_admin = '<?php echo $is_admin; ?>';

		if(data.output === false || data.success  === false) {
			slot.append("No Feeds Found");

			return false;
		}
        totalItems = data.total;
        $.each(data.output, function(i, v){
            arr_cont.push(v.conttoken.toString());
            let show_files = '';
            let pined_post = '';
            load_feeds_comment(v.conttoken);
            if((token == v.token || is_admin) && isLoggedIn){
                edit_btn = `<div class="dropdown float-right">
                                <button class="btn custom dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <div class="dot"></div>
                                    <div class="dot"></div>
                                    <div class="dot"></div>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="#" onclick="feedEdit('${v.conttoken}');">Edit</a>
                                    <a href="#" class="dropdown-item" onclick="feedDelete('${v.conttoken}');">Delete</a>
                                </div>
                            </div>`;
            }
            edit_btn = '';//kalpana added
            /* if(v.meta.is_pin){
                pined_post = `<div class="col-xl-4 mb-1 d-flex align-items-center justify-content-end" style="gap: 0.5rem;">
                                    <svg width="17" height="23" viewBox="0 0 17 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.42467 1.4375C1.42467 0.642383 2.05751 0 2.84082 0H14.17C14.9533 0 15.5861 0.642383 15.5861 1.4375C15.5861 2.23262 14.9533 2.875 14.17 2.875H12.8645L13.369 9.53242C14.9931 10.4264 16.2765 11.9223 16.8872 13.7865L16.9314 13.9213C17.0775 14.3615 17.0022 14.8422 16.7367 15.215C16.4712 15.5879 16.0419 15.8125 15.5861 15.8125H1.42467C0.968853 15.8125 0.54401 15.5924 0.274058 15.215C0.00410543 14.8377 -0.0667017 14.357 0.079338 13.9213L0.123592 13.7865C0.734304 11.9223 2.01768 10.4264 3.64182 9.53242L4.14632 2.875H2.84082C2.05751 2.875 1.42467 2.23262 1.42467 1.4375ZM7.08925 17.25H9.92153V21.5625C9.92153 22.3576 9.28869 23 8.50539 23C7.72208 23 7.08925 22.3576 7.08925 21.5625V17.25Z" fill="#2557A7"/>
                                    </svg>
                                    <span style="font-size: clamp(16px, 2vw + 1rem, 21px); color: #B9B9B9; font-weight: 500;">
                                        Pinned Post
                                    </span>
                                </div>`;
            } */
            var img = feedavatardisplay(v.avatar,v.avatar_image,'<?php echo TAOH_OPS_PREFIX;?>');

            if(Array.isArray(v.meta.images)){
                $.each(v.meta.images, function(index, value) {
                    show_files += `<div class="py-2">
                                        <a href="${value}" data-fancybox="gallery" data-conttoken="${v.conttoken}" class="fancy_box fancybox-item${v.conttoken}">
                                            <img width="120" src="${value}" />
                                            </a>
                                    </div>`;
                });
            }
            if(Array.isArray(v.meta.files)){
                var get_src = '';
                $.each(v.meta.files, function(indexs, values) {
                    var urld = values;
                    var extensiond = urld.split('.').pop();
                    if(extensiond == 'pdf'){
                        get_src = '<?php echo TAOH_SITE_URL_ROOT . '/assets/images/pdf.png'; ?>';
                    }else if(extensiond == 'doc' || extensiond == 'docx'){
                        get_src = '<?php echo TAOH_SITE_URL_ROOT . '/assets/images/word.png'; ?>';
                    }
                    show_files += `<div class="px-3 py-2">
                                        <a href="${urld}" data-fancybox="gallery" data-conttoken="${v.conttoken}" class="fancy_box fancybox-item${v.conttoken}">
                                            <img width="120" src="${get_src}" />
                                            </a>
                                    </div>`;
                });
            }
            if(isLoggedIn){
                liked_check = get_liked_check_feed(v.conttoken);
                comment_show = `<a data-conttoken="${v.conttoken}" class="cmtoggle" style="cursor: pointer;"><div class="d-flex align-items-center" style="gap: 6px;">
                                        <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M18 7.07143C18 10.9777 13.9713 14.1429 9.00052 14.1429C7.6963 14.1429 6.45887 13.9253 5.34097 13.5343C4.92263 13.8301 4.24064 14.2346 3.43209 14.5746C2.58839 14.9282 1.57244 15.2308 0.56351 15.2308C0.335008 15.2308 0.131113 15.0982 0.0432276 14.8942C-0.044658 14.6902 0.00455792 14.459 0.162752 14.3026L0.173298 14.2924C0.183844 14.2822 0.197906 14.2686 0.218999 14.2448C0.257668 14.2041 0.31743 14.1395 0.391254 14.0511C0.535387 13.8811 0.728735 13.6295 0.925598 13.3167C1.27714 12.7524 1.61111 12.0112 1.6779 11.1783C0.623272 10.0224 0.0010425 8.6047 0.0010425 7.07143C0.0010425 3.16514 4.02972 0 9.00052 0C13.9713 0 18 3.16514 18 7.07143Z" fill="#D3D3D3"/>
                                        </svg>
                                        <span class="comment_count${v.conttoken}" style="color: #555555;">Comment</span>
                                    </div></a>`;
            }
            slot.append(`<div class="club-announcement-card" style="border: 1px solid #d3d3d3; border-radius: 6px; background: #fff;">


                                        <div class="px-3 pb-3 pt-2" style="">
                                            ${edit_btn}
                                            <div class="row px-3 py-3" style="border-bottom: 1px solid #D3D3D3;">
                                                <div class="announcement-heading-container col-12 d-flex align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <span data-profile_token="${v.ptoken}" class="openProfileModal">
                                                        <img src="${img}" alt="profile" style="width: 55px; height: 55px; border-radius: 50%; border: 2px solid #ddd;" /></span>
                                                    </div>
                                                    <div class="pl-4 d-flex flex-column justify-content-center">
                                                        <span data-profile_token="${v.ptoken}" class="openProfileModal">
                                                        <h4 class="announcement-from mb-1">${v.fname} ${v.lname}</h4></span>
                                                        <p class="announcement-date">${format_Timestamp(v.created)}</p>
                                                    </div>
                                                </div>
                                            </div>


                                            <div style="min-height: 380px; max-height: 380px; overflow-y: auto; scrollbar-width: thin;">
                                                <h4 class="pl-lg-3 py-3 px-3">
                                                    <a class="text-capitalize" style="color: #2557A7; font-size: clamp(16px, 2vw + 1rem, 17px); font-weight: 600;">
                                                    ${taoh_title_desc_decode(v.title)}</a>
                                                </h4>
                                                <section class="gallery_sec py-0">
                                                    <div class="container">
                                                        <div class="d-flex" style="gap: 12px; overflow-x: auto; scrollbar-width: thin;">
                                                            ${show_files}
                                                        </div>
                                                    </div>
                                                </section>
                                                <div class="py-3 px-3" style="font-size: 16px; line-height: 32px;
                                                font-weight: 400; color: #555555;">


                                                ${taoh_desc_decode(v.description)}
                                                </div>
                                            </div>
                                            <div class="">
                                                ${liked_check}
                                                ${comment_show}
                                            </div>
                                            <div class="${v.conttoken}content1 px-3" style="display:none;">
                                                <div id="${v.conttoken}comment_append"></div>
                                            </div>
                                        </div>
                                    </div>


                            </div>`
                    );

                $('#announcements_blk').show();
                const cardWidth = document.querySelector('.club-announcement-card').offsetWidth;
                    const gap = parseFloat(getComputedStyle(document.documentElement).fontSize);

                    // Scroll 800px to the left
                    scrollLeftButton.addEventListener('click', function() {
                        scrollContainer.scrollLeft -= cardWidth + gap;
                    });

                    // Scroll 800px to the right
                    scrollRightButton.addEventListener('click', function() {
                        scrollContainer.scrollLeft += cardWidth + gap;
                    });
            });

	}

    function feedavatardisplay(avatar,img,path){
        var avatar_img = '';
        if(img !='' && img!= undefined ){
            avatar_img = ` ${img}`;
        }
        else if(avatar!='' && avatar!= undefined){
            avatar_img = `${path}/avatar/PNG/128/${avatar}.png`;
        }
        else
        avatar_img = ` ${path}/avatar/PNG/128/avatar_def.png`;

        return avatar_img;
    }

    function get_liked_check_feed(conttoken){
		var get_liked = 0;
		let is_local = localStorage.getItem('announcement_'+conttoken+'_liked');
		if ((get_liked) || (is_local)) {
			var liked_checks = `<a data-conttoken="${conttoken}">
                <div class="row mx-0 px-3 py-3 d-flex align-items-center" style="gap: 12px;">
                    <div class="d-flex align-items-center" style="gap: 6px;">
                        <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.67344 9.07907L8.02617 15.0099C8.28984 15.256 8.63789 15.3931 9 15.3931C9.36211 15.3931 9.71016 15.256 9.97383 15.0099L16.3266 9.07907C17.3953 8.08415 18 6.68845 18 5.22946V5.02555C18 2.56813 16.2246 0.47282 13.8023 0.0685232C12.1992 -0.198664 10.568 0.325164 9.42188 1.47126L9 1.89313L8.57812 1.47126C7.43203 0.325164 5.80078 -0.198664 4.19766 0.0685232C1.77539 0.47282 0 2.56813 0 5.02555V5.22946C0 6.68845 0.604687 8.08415 1.67344 9.07907Z" fill="#FF0808"/>
                        </svg>
                        <span id="likeCount" data-conts="${(conttoken)}" class="p-0 met_like"></span>
                    <span style="color: #555555;">Like(s)</span>
                </div>
            </a>`;
		} else {
			var liked_checks = `
            <a data-conttoken="${conttoken}" class="feed_liked" style="cursor: pointer;">
                <div class="row mx-0 px-3 py-3 d-flex align-items-center" style="gap: 12px;">
                    <div class="d-flex align-items-center" style="gap: 6px;">
                        <svg width="18" height="16" viewBox="0 0 18 16" fill="none" class="${conttoken}_filled" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.67344 9.07907L8.02617 15.0099C8.28984 15.256 8.63789 15.3931 9 15.3931C9.36211 15.3931 9.71016 15.256 9.97383 15.0099L16.3266 9.07907C17.3953 8.08415 18 6.68845 18 5.22946V5.02555C18 2.56813 16.2246 0.47282 13.8023 0.0685232C12.1992 -0.198664 10.568 0.325164 9.42188 1.47126L9 1.89313L8.57812 1.47126C7.43203 0.325164 5.80078 -0.198664 4.19766 0.0685232C1.77539 0.47282 0 2.56813 0 5.02555V5.22946C0 6.68845 0.604687 8.08415 1.67344 9.07907Z" fill="#D3D3D3"/>
                        </svg>
                        <span id="likeCount" data-conts="${(conttoken)}" class="p-0 met_like"></span>
                    <span style="color: #555555;">Like(s)</span>
                </div>
            </a>`;
		}
		return liked_checks;
	}

    $(document).on("click", ".feed_liked", function(event) {
        //event.stopPropagation(); // Stop the event from propagating to the parent
        var savetoken = $(this).attr('data-conttoken');
        var likes = $('.met_like[data-conts="'+savetoken+'"]').html();
		var count_like = (likes==''?0:parseInt(likes)) + parseInt(1);
		$('.met_like[data-conts="'+savetoken+'"]').html(count_like > like_min ? (count_like):'');
		$('.'+savetoken+'_filled path').attr('fill', '#FF0808');
		$(this).removeAttr("style");
		localStorage.setItem(app_slug+'_'+savetoken+'_liked',1);
		var data = {
			 'taoh_action': 'feed_like_put',
			 'conttoken': savetoken,
			 'ptoken': '<?php echo $ptoken; ?>',
             'slug': 'announcement',
		};
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
			if(response.success){
				taoh_set_success_message('Liked Successfully.');
			}else{
				taoh_set_error_message('Like Failed.');
				console.log( "Like Failed!" );
			}
		}).fail(function() {
			console.log( "Network issue!" );
		})
    });

    $(document).on("click", ".cmtoggle", function(event) {
        let cl_conttoken = $(this).attr('data-conttoken');
        $('.'+cl_conttoken+'content').slideToggle(); // Toggle visibility with slide effect
    });

    function load_feeds_comment(com_conttoken,scroll_btm=false){
        const functionName = 'taoh_feeds_comments_widget'; // Change this to call a different function
        const myArray = {
            conttoken: com_conttoken,
            conttype: 'announcement',
            label: 'Comment',
            avatar: '<?php echo $avatar_image; ?>',
            redirect: window.location.href
        };
        var data = {
            'taoh_action': 'post_commentsform',
            'data': myArray,
		};
        $.ajax({
            type: 'POST',
            url: '<?php echo taoh_site_ajax_url(); ?>', // URL of your PHP script
            data: data,
            success: function(response) {
                //console.log('Response:', response);
                $('#'+com_conttoken+'comment_append').html(response);
                var com_count = $('.get_comment'+com_conttoken).text();
                if(com_count > 0){
                    $('.comment_count'+com_conttoken).html(com_count+' '+'Comments');
                }
                if(scroll_btm){
                    const commentsSection = document.getElementById('comments'+com_conttoken);
                    commentsSection.scrollTop = commentsSection.scrollHeight;
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
	}

    if(document.getElementById("close-support-btn")){
        document.getElementById("close-support-btn").addEventListener("click", function() {
            var supportButton = document.querySelector(".support-right");
            var icon = document.getElementById("toggle-icon");

            // Toggle the sliding effect
            supportButton.classList.toggle("slide-out");

            // Toggle between "-" and "+"
            if (supportButton.classList.contains("slide-out")) {
                icon.textContent = "+"; // Change the icon to "+"
            } else {
                icon.textContent = "-"; // Change the icon to "-"
            }
        });
    }


    $(document).on("click", ".create_referral_event", function () {
        let event_title = $(this).data("title");
        let link = $(this).data("location");
        let data = {
            'taoh_action': 'taoh_invite_rsvp_type',
            'from_link' : link,
            'detail_link': window.location.href,
            'event_title' : event_title,
        };
        $.post(_taoh_site_ajax_url, data, function(response) {});
    });

</script>


<?php
taoh_get_footer();
