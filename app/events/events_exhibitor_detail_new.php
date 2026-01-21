<?php
ob_start();
taoh_get_header();

$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;

/* if (!$taoh_user_is_logged_in) {
    header("Location: " . TAOH_SITE_URL_ROOT . '/login');
    taoh_exit();
} */
$user_info_obj = taoh_user_all_info();
$valid_user = isset($user_info_obj->profile_complete) ? (bool) $user_info_obj->profile_complete : false;

$ptoken = $taoh_user_is_logged_in ? $user_info_obj->ptoken : '';
$puser_name = $taoh_user_is_logged_in ? $user_info_obj->fname : '';
$puser_email = $taoh_user_is_logged_in ? $user_info_obj->email : '';

$click_view = (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) ? 'click' : 'view';


function showErrorPage($base_path, $error_code = 1001, $error_from = '', $error_data = [])
{
    include_once $base_path . '/includes/error.php';
    taoh_get_footer();
    exit();
}

$appname = TAOH_CURR_APP_SLUG ?? 'events';
$exhibitor_id = (int) taoh_parse_url(2);
$eventtoken = taoh_parse_url(3);

if (empty($exhibitor_id)) {
    showErrorPage(TAOH_APP_PATH . '/' . $appname, 1001, 'event_exhibitor');
    taoh_exit();
}

if(defined('TAOH_API_TOKEN')){
    // Get RSVP status
    $taoh_vals = array(
        'ops' => 'status',
        'mod' => 'events',
        'token' => TAOH_API_TOKEN,
        'eventtoken' => $eventtoken,
        'cache_required' => 0,
    );
    $rsvp_status_result = taoh_apicall_get('events.rsvp.get', $taoh_vals);
    $rsvp_status_response = taoh_get_array($rsvp_status_result, true);

    $is_user_rsvp_done = $rsvp_status_response['success'];
}else{
    $is_user_rsvp_done =  false;
}

?>

    <style>
        #external_video_room,
        #exhibitor_website,
        #exhibitor_location {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        #exhibitor_website_blk i,
        #exhibitor_location_blk i {
            color: #2557a7;
            font-size: 14px;
        }

        #exhibitor_website_blk a:hover span,
        #exhibitor_location_blk a:hover span {
            text-decoration: underline;
        }
        /*  */
        .exh-d-right, .exh-d-right-rbtn-blk {
            width: 100%;
            max-width: 100%;
        }
        .winner_profile.card {
            width: 100%;
            max-width: 480px;

            padding: 0;
            border: 2px solid #d3d3d3;
            /* background-image: linear-gradient(#ffffff, #ffffff), linear-gradient(90deg, #396AFC 0%, #2948FF 50%);
            background-origin: border-box;
            background-clip: content-box, border-box; */
            border-radius: 6px;
        } 
        .exh-d-right button {
            font-size: 14px;
        }
        @media (min-width: 768px) {
            .exh-d-right {
                max-width: 361px;
            }
            .winner_profile.card {
                max-width: 361px;
            } 
        }
        @media (min-width: 1024px) {
            .exh-d-right-rbtn-blk {
                max-width: 362px;
            }
        }
        .star-con input {
            display: none;
        }
    </style>




 




<div class="detail-hall aw aw-logo aw-loader">

    <div class="light-dark">
        <!-- new template start-->
            <div class="pt-4 sticky-top light-dark shadow-sm border-bottom" style="z-index: 99;">
                <div class="container">
                    <ul class="nav nav-tabs justify-content-left border-0 mt-3" id="exhibitor_breadcrumb" role="tablist" style="line-height: 1.143;">
                        <li class="nav-item">
                            <a href="<?php echo TAOH_SITE_URL_ROOT; ?>">Home</a>
                            <svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"></path></svg>
                        </li>
                        <!-- <li class="nav-item">
                            <a href="#">Events</a>
                            <svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"></path></svg>
                        </li>
                        <li class="nav-item">
                            First Friday Fair Virtual Job Fair Career Expo Event
                        </li>   -->                              
                    </ul>

                    <div class="d-flex align-items-start flex-column flex-lg-row py-3" style="gap: 9px;">
                        <div class="flex-grow-1">
                            <h5 class="e-v2-title mb-1" id="event_title"></h5>
                            <div class="d-flex align-items-center mb-1" style="gap: 12px;">
                                <svg style="min-width: fit-content;" width="25" height="25" viewBox="0 0 21 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 0C6.82969 0 7.5 0.698242 7.5 1.5625V3.125H13.5V1.5625C13.5 0.698242 14.1703 0 15 0C15.8297 0 16.5 0.698242 16.5 1.5625V3.125H18.75C19.9922 3.125 21 4.1748 21 5.46875V7.8125H0V5.46875C0 4.1748 1.00781 3.125 2.25 3.125H4.5V1.5625C4.5 0.698242 5.17031 0 6 0ZM0 9.375H21V22.6562C21 23.9502 19.9922 25 18.75 25H2.25C1.00781 25 0 23.9502 0 22.6562V9.375ZM15.4219 14.8926C15.8625 14.4336 15.8625 13.6914 15.4219 13.2373C14.9812 12.7832 14.2688 12.7783 13.8328 13.2373L9.37969 17.876L7.17656 15.5811C6.73594 15.1221 6.02344 15.1221 5.5875 15.5811C5.15156 16.04 5.14687 16.7822 5.5875 17.2363L8.5875 20.3613C9.02812 20.8203 9.74063 20.8203 10.1766 20.3613L15.4219 14.8926Z" fill="#2557A7"></path>
                                </svg>
                                <p class="e-v2-info"><span id="event_start_datetime"></span> 
                                <!--<span id="event_end_datetime"></span>-->
                                </p>
                            </div>
                        </div>

                        <span id="liveLink"></span>
                        <!-- <button type="button" class="e-d-v2-btn btn btn-success lh-1 py-2">
                            LIVE NOW! Click to Join
                        </button> -->
                        
                    </div>

                </div>
            </div>

            <!-- carousle start -->
            <div class="container exhibitor-carousel-con pt-4" id="exhibitor_banner_container">
                <!-- <a class="exh-owl-prev" role="button" aria-label="Previous">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M32 16C32 11.7565 30.3143 7.68687 27.3137 4.68629C24.3131 1.68571 20.2435 0 16 0C11.7565 0 7.68687 1.68571 4.68629 4.68629C1.68571 7.68687 0 11.7565 0 16C0 20.2435 1.68571 24.3131 4.68629 27.3137C7.68687 30.3143 11.7565 32 16 32C20.2435 32 24.3131 30.3143 27.3137 27.3137C30.3143 24.3131 32 20.2435 32 16ZM16.9375 8.4375C17.525 7.85 18.475 7.85 19.0562 8.4375C19.6375 9.025 19.6437 9.975 19.0562 10.5562L13.6187 15.9937L19.0562 21.4312C19.6437 22.0187 19.6437 22.9688 19.0562 23.55C18.4688 24.1312 17.5187 24.1375 16.9375 23.55L10.4375 17.0625C9.85 16.475 9.85 15.525 10.4375 14.9438L16.9375 8.4375Z" fill="black"/>
                    </svg>
                </a> -->

                <div class="exhibitor-carousel owl-carousel owl-theme" id="exhibitor_banner_image">

                </div>

                <!-- <a class="exh-owl-next" role="button" aria-label="Next">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 16C0 11.7565 1.68571 7.68687 4.68629 4.68629C7.68687 1.68571 11.7565 0 16 0C20.2435 0 24.3131 1.68571 27.3137 4.68629C30.3143 7.68687 32 11.7565 32 16C32 20.2435 30.3143 24.3131 27.3137 27.3137C24.3131 30.3143 20.2435 32 16 32C11.7565 32 7.68687 30.3143 4.68629 27.3137C1.68571 24.3131 0 20.2435 0 16ZM15.0625 8.4375C14.475 7.85 13.525 7.85 12.9438 8.4375C12.3625 9.025 12.3563 9.975 12.9438 10.5562L18.3813 15.9937L12.9438 21.4312C12.3563 22.0187 12.3563 22.9688 12.9438 23.55C13.5312 24.1312 14.4813 24.1375 15.0625 23.55L21.5625 17.0625C22.15 16.475 22.15 15.525 21.5625 14.9438L15.0625 8.4375Z" fill="black"/>
                    </svg>
                </a> -->
            </div>
            <!-- carousel end -->


            <div class="container d-flex flex-column flex-lg-row">
                <div class="exh-d-v2-left flex-grow-1 py-5 pr-md-3">
                    <div class="d-flex align-items-start" style="gap: 12px;">
                        <img class="c-v2-img" id="exhibitor_logo" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/exh-v2-3.png" alt="Exhibitor logo">
                        <div> 
                            <h6 class="c-v2-name mb-1" id="exhibitor_title"></h6>
                            <p class="c-v2-tag mb-2" id="exhibitor_subtitle"></p>
                            <input type="hidden" id="raffle_question" name="raffle_question" value="">
                            <a href="#" class="c-v2-site">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.375 9C12.375 9.78047 12.3328 10.5328 12.259 11.25H5.74102C5.66367 10.5328 5.625 9.78047 5.625 9C5.625 8.21953 5.66719 7.46719 5.74102 6.75H12.259C12.3363 7.46719 12.375 8.21953 12.375 9ZM13.3875 6.75H17.7152C17.9016 7.4707 18 8.22305 18 9C18 9.77695 17.9016 10.5293 17.7152 11.25H13.3875C13.4613 10.5258 13.5 9.77344 13.5 9C13.5 8.22656 13.4613 7.47422 13.3875 6.75ZM17.3461 5.625H13.2434C12.8918 3.37852 12.1957 1.49766 11.2992 0.295312C14.052 1.02305 16.2914 3.01992 17.3426 5.625H17.3461ZM12.1043 5.625H5.8957C6.11016 4.34531 6.44062 3.21328 6.84492 2.2957C7.21406 1.46602 7.62539 0.864844 8.02266 0.485156C8.41641 0.1125 8.74336 0 9 0C9.25664 0 9.58359 0.1125 9.97734 0.485156C10.3746 0.864844 10.7859 1.46602 11.1551 2.2957C11.5629 3.20977 11.8898 4.3418 12.1043 5.625ZM4.75664 5.625H0.653906C1.70859 3.01992 3.94453 1.02305 6.70078 0.295312C5.8043 1.49766 5.1082 3.37852 4.75664 5.625ZM0.284766 6.75H4.6125C4.53867 7.47422 4.5 8.22656 4.5 9C4.5 9.77344 4.53867 10.5258 4.6125 11.25H0.284766C0.0984375 10.5293 0 9.77695 0 9C0 8.22305 0.0984375 7.4707 0.284766 6.75ZM6.84492 15.7008C6.43711 14.7867 6.11016 13.6547 5.8957 12.375H12.1043C11.8898 13.6547 11.5594 14.7867 11.1551 15.7008C10.7859 16.5305 10.3746 17.1316 9.97734 17.5113C9.58359 17.8875 9.25664 18 9 18C8.74336 18 8.41641 17.8875 8.02266 17.5148C7.62539 17.1352 7.21406 16.534 6.84492 15.7043V15.7008ZM4.75664 12.375C5.1082 14.6215 5.8043 16.5023 6.70078 17.7047C3.94453 16.977 1.70859 14.9801 0.653906 12.375H4.75664ZM17.3461 12.375C16.2914 14.9801 14.0555 16.977 11.3027 17.7047C12.1992 16.5023 12.8918 14.6215 13.2469 12.375H17.3461Z" fill="#FF6600"/>
                                </svg>
                                <span id="exhibitor_website_blk"></span>
                            </a>
                        </div>
                    </div>

                    <hr style="max-width: 385px; border-top: 2px solid #d3d3d3;">

                    <div class="exh-v2-desc" id="exh_description"></div>

                    <div class="exh-v2-contact mt-5">

                    <div class="d-flex align-items-center" style="gap: 12px;">
                        <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.71429 0C2.11406 0 0 2.11406 0 4.71429V28.2857C0 30.8859 2.11406 33 4.71429 33H28.2857C30.8859 33 33 30.8859 33 28.2857V4.71429C33 2.11406 30.8859 0 28.2857 0H4.71429ZM16.058 17.6565L4.72902 10.342C4.86161 9.16339 5.85603 8.25 7.07143 8.25H25.9286C27.144 8.25 28.1384 9.16339 28.271 10.342L16.942 17.6565C16.8094 17.7449 16.6547 17.7891 16.5 17.7891C16.3453 17.7891 16.1906 17.7449 16.058 17.6565ZM18.2237 19.6379L28.2857 13.1411V22.3929C28.2857 23.6967 27.2324 24.75 25.9286 24.75H7.07143C5.76763 24.75 4.71429 23.6967 4.71429 22.3929V13.1411L14.7763 19.6379C15.292 19.9694 15.8886 20.1462 16.5 20.1462C17.1114 20.1462 17.708 19.9694 18.2237 19.6379Z" fill="#FF6600"/>
                        </svg>
                        <p>Feel free to reach out and drop your enquiries here</p>
                     </div>
                     <div class="d-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 12px; flex: 1;">
                        <a href="#" class="btn v2-room-btn contact-us" id="exhibitor_contact_us" data-toggle="modal" data-target="#contactusModal" style="max-width: 143px; display: none;">Contact Us</a>
                        <a href="#" class="btn v2-room-btn px-0 add-comment-btn" id="write_review_blk" style="max-width: 157px;" data-toggle="modal" data-target="#writereviewModal">Add your Comments</a>
                      </div>

                    </div>
                </div>


                <div class="exh-d-v2-right py-5">

                    <div class="d-flex align-items-end justify-content-between pl-md-3 flex-wrap" style="gap: 9px; ">
                        <div class="rating mt-1">
                            <h6 class="exh-r-v2-title mb-2">Rate Exhibitor</h6>
                            <div class="star-con">
                                <input type="radio" id="star1" name="rating" value="1" required>
                                <label class="star" for="star1">
                                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.42486 0.579483C9.25424 0.225355 8.89367 0 8.49769 0C8.10171 0 7.74436 0.225355 7.57051 0.579483L5.50047 4.83869L0.877481 5.52119C0.491159 5.57914 0.169224 5.84956 0.0501075 6.21979C-0.0690086 6.59001 0.0275719 6.99887 0.304436 7.27252L3.659 10.5917L2.86704 15.2823C2.80265 15.6686 2.96362 16.0613 3.28234 16.2899C3.60105 16.5185 4.02279 16.5475 4.37048 16.364L8.50091 14.1587L12.6313 16.364C12.979 16.5475 13.4008 16.5217 13.7195 16.2899C14.0382 16.0581 14.1992 15.6686 14.1348 15.2823L13.3396 10.5917L16.6942 7.27252C16.971 6.99887 17.0708 6.59001 16.9485 6.21979C16.8262 5.84956 16.5074 5.57914 16.1211 5.52119L11.4949 4.83869L9.42486 0.579483Z" fill="white"/>
                                    </svg>
                                </label>
                                <input type="radio" id="star2" name="rating" value="2">
                                <label class="star" for="star2">
                                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.42486 0.579483C9.25424 0.225355 8.89367 0 8.49769 0C8.10171 0 7.74436 0.225355 7.57051 0.579483L5.50047 4.83869L0.877481 5.52119C0.491159 5.57914 0.169224 5.84956 0.0501075 6.21979C-0.0690086 6.59001 0.0275719 6.99887 0.304436 7.27252L3.659 10.5917L2.86704 15.2823C2.80265 15.6686 2.96362 16.0613 3.28234 16.2899C3.60105 16.5185 4.02279 16.5475 4.37048 16.364L8.50091 14.1587L12.6313 16.364C12.979 16.5475 13.4008 16.5217 13.7195 16.2899C14.0382 16.0581 14.1992 15.6686 14.1348 15.2823L13.3396 10.5917L16.6942 7.27252C16.971 6.99887 17.0708 6.59001 16.9485 6.21979C16.8262 5.84956 16.5074 5.57914 16.1211 5.52119L11.4949 4.83869L9.42486 0.579483Z" fill="white"/>
                                    </svg>
                                </label>
                                <input type="radio" id="star3" name="rating" value="3">
                                <label class="star" for="star3">
                                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.42486 0.579483C9.25424 0.225355 8.89367 0 8.49769 0C8.10171 0 7.74436 0.225355 7.57051 0.579483L5.50047 4.83869L0.877481 5.52119C0.491159 5.57914 0.169224 5.84956 0.0501075 6.21979C-0.0690086 6.59001 0.0275719 6.99887 0.304436 7.27252L3.659 10.5917L2.86704 15.2823C2.80265 15.6686 2.96362 16.0613 3.28234 16.2899C3.60105 16.5185 4.02279 16.5475 4.37048 16.364L8.50091 14.1587L12.6313 16.364C12.979 16.5475 13.4008 16.5217 13.7195 16.2899C14.0382 16.0581 14.1992 15.6686 14.1348 15.2823L13.3396 10.5917L16.6942 7.27252C16.971 6.99887 17.0708 6.59001 16.9485 6.21979C16.8262 5.84956 16.5074 5.57914 16.1211 5.52119L11.4949 4.83869L9.42486 0.579483Z" fill="white"/>
                                    </svg>
                                </label>
                                <input type="radio" id="star4" name="rating" value="4">
                                <label class="star" for="star4">
                                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.42486 0.579483C9.25424 0.225355 8.89367 0 8.49769 0C8.10171 0 7.74436 0.225355 7.57051 0.579483L5.50047 4.83869L0.877481 5.52119C0.491159 5.57914 0.169224 5.84956 0.0501075 6.21979C-0.0690086 6.59001 0.0275719 6.99887 0.304436 7.27252L3.659 10.5917L2.86704 15.2823C2.80265 15.6686 2.96362 16.0613 3.28234 16.2899C3.60105 16.5185 4.02279 16.5475 4.37048 16.364L8.50091 14.1587L12.6313 16.364C12.979 16.5475 13.4008 16.5217 13.7195 16.2899C14.0382 16.0581 14.1992 15.6686 14.1348 15.2823L13.3396 10.5917L16.6942 7.27252C16.971 6.99887 17.0708 6.59001 16.9485 6.21979C16.8262 5.84956 16.5074 5.57914 16.1211 5.52119L11.4949 4.83869L9.42486 0.579483Z" fill="white"/>
                                    </svg>
                                </label>
                                <input type="radio" id="star5" name="rating" value="5">
                                <label class="star" for="star5">
                                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.42486 0.579483C9.25424 0.225355 8.89367 0 8.49769 0C8.10171 0 7.74436 0.225355 7.57051 0.579483L5.50047 4.83869L0.877481 5.52119C0.491159 5.57914 0.169224 5.84956 0.0501075 6.21979C-0.0690086 6.59001 0.0275719 6.99887 0.304436 7.27252L3.659 10.5917L2.86704 15.2823C2.80265 15.6686 2.96362 16.0613 3.28234 16.2899C3.60105 16.5185 4.02279 16.5475 4.37048 16.364L8.50091 14.1587L12.6313 16.364C12.979 16.5475 13.4008 16.5217 13.7195 16.2899C14.0382 16.0581 14.1992 15.6686 14.1348 15.2823L13.3396 10.5917L16.6942 7.27252C16.971 6.99887 17.0708 6.59001 16.9485 6.21979C16.8262 5.84956 16.5074 5.57914 16.1211 5.52119L11.4949 4.83869L9.42486 0.579483Z" fill="white"/>
                                    </svg>
                                </label>
                            </div>
                            <h6 class="rating-text">Rated <span id="average_count"></span> based on <span id="review_count"></span> Reviews</h6>
                        </div>

                        <div class="d-flex align-items-start flex-wrap" style="gap: 12px;">
                            <div id="exhibitor_video_room_links" style="display: none;"></div>
                            <!-- <a href="#" class="btn v2-room-btn py-2" >Join Discussion Room</a> -->
                            <div class="" id="exhibitor_streaming_links" style="display: none;"></div>
                        </div>
                    </div>

                    <hr>

                    <div class="exhibitor_raffle_blk mb-3 pl-md-3" id="exhibitor_raffle_blk">
                        <p class="raf-v2-title my-3">Claim Your Raffle</p>
                        <div class="exhibitor_raffle exhibitor-v2-raffle" id="exhibitor_raffle">
                            <div class="mb-3" style="gap: 12px;">
                                <div class="d-flex align-items-end py-2 px-3" style="gap: 6px;">
                                    <svg width="44" height="40" viewBox="0 0 44 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12.2783 11.4344L14.5213 15.25H14.4375H9.79688C8.37246 15.25 7.21875 14.0963 7.21875 12.6719C7.21875 11.2475 8.37246 10.0938 9.79688 10.0938H9.93867C10.899 10.0938 11.7949 10.6029 12.2783 11.4344ZM4.125 12.6719C4.125 13.6 4.35059 14.4766 4.74375 15.25H2.0625C0.92168 15.25 0 16.1717 0 17.3125V21.4375C0 22.5783 0.92168 23.5 2.0625 23.5H30.9375C32.0783 23.5 33 22.5783 33 21.4375V17.3125C33 16.1717 32.0783 15.25 30.9375 15.25H28.2563C28.6494 14.4766 28.875 13.6 28.875 12.6719C28.875 9.53945 26.3355 7 23.2031 7H23.0613C21.0053 7 19.0975 8.08926 18.0533 9.86172L16.5 12.5107L14.9467 9.86816C13.9025 8.08926 11.9947 7 9.93867 7H9.79688C6.66445 7 4.125 9.53945 4.125 12.6719ZM25.7812 12.6719C25.7812 14.0963 24.6275 15.25 23.2031 15.25H18.5625H18.4787L20.7217 11.4344C21.2115 10.6029 22.101 10.0938 23.0613 10.0938H23.2031C24.6275 10.0938 25.7812 11.2475 25.7812 12.6719ZM2.0625 25.5625V36.9062C2.0625 38.6143 3.44824 40 5.15625 40H14.4375V25.5625H2.0625ZM18.5625 40H27.8438C29.5518 40 30.9375 38.6143 30.9375 36.9062V25.5625H18.5625V40Z" fill="#FA6C2C"></path>
                                        <path d="M35.4352 0.246066C35.3549 0.0956924 35.1853 0 34.9989 0C34.8126 0 34.6444 0.0956924 34.5626 0.246066L33.5885 2.05465L31.4129 2.34446C31.2311 2.36907 31.0796 2.4839 31.0236 2.64111C30.9675 2.79832 31.013 2.97193 31.1433 3.08813L32.7219 4.49754L32.3492 6.48931C32.3189 6.65336 32.3946 6.82014 32.5446 6.91719C32.6946 7.01425 32.8931 7.02656 33.0567 6.94864L35.0004 6.01222L36.9442 6.94864C37.1078 7.02656 37.3062 7.01562 37.4562 6.91719C37.6062 6.81877 37.682 6.65336 37.6517 6.48931L37.2775 4.49754L38.8561 3.08813C38.9864 2.97193 39.0333 2.79832 38.9758 2.64111C38.9182 2.4839 38.7682 2.36907 38.5864 2.34446L36.4094 2.05465L35.4352 0.246066Z" fill="#FA6C2C"></path>
                                        <path d="M41.772 7.14061C41.7218 7.05468 41.6158 7 41.4993 7C41.3829 7 41.2778 7.05468 41.2266 7.14061L40.6178 8.17409L39.2581 8.33969C39.1445 8.35375 39.0498 8.41937 39.0147 8.50921C38.9797 8.59904 39.0081 8.69825 39.0895 8.76465L40.0762 9.57002L39.8432 10.7082C39.8243 10.8019 39.8717 10.8972 39.9654 10.9527C40.0591 11.0081 40.1832 11.0152 40.2854 10.9706L41.5003 10.4356L42.7151 10.9706C42.8174 11.0152 42.9414 11.0089 43.0351 10.9527C43.1289 10.8964 43.1762 10.8019 43.1573 10.7082L42.9234 9.57002L43.91 8.76465C43.9915 8.69825 44.0208 8.59904 43.9848 8.50921C43.9489 8.41937 43.8551 8.35375 43.7415 8.33969L42.3809 8.17409L41.772 7.14061Z" fill="#FA6C2C"></path>
                                        <defs>
                                        <linearGradient id="paint0_linear_7016_156" x1="16.5" y1="7" x2="16.5" y2="40" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#FF5C00"></stop>
                                        <stop offset="1" stop-color="#F2836D"></stop>
                                        </linearGradient>
                                        </defs>
                                    </svg>
                                    <h4 id='raffle_title'></h4>
                                </div>
                                <hr class="my-0" style="border-top: 1px solid #d3d3d3;">
                                <div class="px-3">
                                    <p class="py-3" id='raffle_description'></p>
                                
                                    <a href="#" class="btn redeem-v2 avail_now" id="avail_now">Avail Now</a>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="raffle-winner raffle-v2-winner mb-3 pl-md-3 winner_profile">
                        <h6 class="raf-v2-title mb-3">
                            <svg width="27" height="23" viewBox="0 0 27 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.81348 4.82188L9.24082 7.25H9.1875H6.23438C5.32793 7.25 4.59375 6.51582 4.59375 5.60938C4.59375 4.70293 5.32793 3.96875 6.23438 3.96875H6.32461C6.93574 3.96875 7.50586 4.29277 7.81348 4.82188ZM2.625 5.60938C2.625 6.2 2.76855 6.75781 3.01875 7.25H1.3125C0.586523 7.25 0 7.83652 0 8.5625V11.1875C0 11.9135 0.586523 12.5 1.3125 12.5H19.6875C20.4135 12.5 21 11.9135 21 11.1875V8.5625C21 7.83652 20.4135 7.25 19.6875 7.25H17.9812C18.2314 6.75781 18.375 6.2 18.375 5.60938C18.375 3.61602 16.759 2 14.7656 2H14.6754C13.367 2 12.1529 2.69316 11.4885 3.82109L10.5 5.50684L9.51152 3.8252C8.84707 2.69316 7.63301 2 6.32461 2H6.23438C4.24102 2 2.625 3.61602 2.625 5.60938ZM16.4062 5.60938C16.4062 6.51582 15.6721 7.25 14.7656 7.25H11.8125H11.7592L13.1865 4.82188C13.4982 4.29277 14.0643 3.96875 14.6754 3.96875H14.7656C15.6721 3.96875 16.4062 4.70293 16.4062 5.60938ZM1.3125 13.8125V21.0312C1.3125 22.1182 2.19434 23 3.28125 23H9.1875V13.8125H1.3125ZM11.8125 23H17.7188C18.8057 23 19.6875 22.1182 19.6875 21.0312V13.8125H11.8125V23Z" fill="url(#paint0_linear_51_103)"/>
                                <path d="M21.772 2.14061C21.7218 2.05468 21.6158 2 21.4993 2C21.3829 2 21.2778 2.05468 21.2266 2.14061L20.6178 3.17409L19.2581 3.33969C19.1445 3.35375 19.0498 3.41937 19.0147 3.50921C18.9797 3.59904 19.0081 3.69825 19.0895 3.76465L20.0762 4.57002L19.8432 5.70818C19.8243 5.80192 19.8717 5.89722 19.9654 5.95268C20.0591 6.00814 20.1832 6.01518 20.2854 5.97065L21.5003 5.43555L22.7151 5.97065C22.8174 6.01518 22.9414 6.00893 23.0351 5.95268C23.1289 5.89644 23.1762 5.80192 23.1573 5.70818L22.9234 4.57002L23.91 3.76465C23.9915 3.69825 24.0208 3.59904 23.9848 3.50921C23.9489 3.41937 23.8551 3.35375 23.7415 3.33969L22.3809 3.17409L21.772 2.14061Z" fill="#FA6C2C"/>
                                <path d="M25.6632 0.0703046C25.6331 0.0273407 25.5695 0 25.4996 0C25.4297 0 25.3667 0.0273407 25.336 0.0703046L24.9707 0.587044L24.1548 0.669847C24.0867 0.676877 24.0299 0.709686 24.0088 0.754603C23.9878 0.79952 24.0049 0.849124 24.0537 0.882323L24.6457 1.28501L24.5059 1.85409C24.4946 1.90096 24.523 1.94861 24.5792 1.97634C24.6355 2.00407 24.7099 2.00759 24.7713 1.98532L25.5002 1.71778L26.2291 1.98532C26.2904 2.00759 26.3648 2.00446 26.4211 1.97634C26.4773 1.94822 26.5057 1.90096 26.4944 1.85409L26.354 1.28501L26.946 0.882323C26.9949 0.849124 27.0125 0.79952 26.9909 0.754603C26.9693 0.709686 26.9131 0.676877 26.8449 0.669847L26.0285 0.587044L25.6632 0.0703046Z" fill="#FA6C2C"/>
                                <defs>
                                <linearGradient id="paint0_linear_51_103" x1="10.5" y1="2" x2="10.5" y2="23" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#FF5C00"/>
                                <stop offset="1" stop-color="#F2836D"/>
                                </linearGradient>
                                </defs>
                            </svg>
                            <span>Raffle Winner !</span>
                        </h6>
                         
                        <div class="winner-card">
                            <img class="winner-img n-participants-img" src="" alt="avatar">
                            <div>
                                <h6 class="w-name mb-1 n-participants-name"></h6>
                                <!-- <svg width="10" height="13" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="#636161"></path>
                                </svg> -->
                                <p class="w-loc" id="n-full-location"></p>
                            </div>
                        </div>
                    </div>


                    <div class="pl-md-3" id="perform_download"  style="display:none;">
                        <h6 class="raf-v2-title mb-3">
                            <svg width="15" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15 1.60586C15 2.78387 10.5149 3.21173 7.5 3.21173C4.4851 3.21102 0 2.78387 0 1.60586C0 0.427855 4.4851 0 7.5 0C10.5149 0 15 0.427855 15 1.60586ZM9.3183 11.9408L14.3925 2.70243C13.92 2.90927 13.2753 3.0814 12.4428 3.22308C11.0908 3.45258 9.24306 3.58929 7.5 3.58929C5.75694 3.58929 3.90929 3.45258 2.55718 3.22308C1.7247 3.08211 1.07926 2.90997 0.607481 2.70243L5.6817 11.9408H9.3183ZM6.05519 16.8994C6.1501 16.966 6.26259 17 6.37509 17C6.44188 17 6.50868 16.988 6.57267 16.9639L8.8226 16.1138C9.04125 16.0317 9.1875 15.8185 9.1875 15.5833V12.3184H5.81261V16.4332C5.81261 16.6188 5.90331 16.7932 6.05519 16.8994Z" fill="url(#paint0_linear_9180_83)"/>
                                <defs>
                                <linearGradient id="paint0_linear_9180_83" x1="7.5" y1="0" x2="7.5" y2="17" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#F47952"/>
                                <stop offset="1" stop-color="#F37F62"/>
                                </linearGradient>
                                </defs>
                            </svg>
                            <span>Your Event Leads and Raffle Participants</span>
                        </h6>
                        <div class="mt-3 p-3 leads-raffle leads-v2-raffle" style="">
                            <div class="">
                                <a target="_blank" class="d-v2-btn mb-2" href="<?php echo TAOH_SITE_URL_ROOT . '/events/export_raffle_entries/?eventtoken='.$eventtoken.'&exh='.$exhibitor_id;?>" id="download_raffle">
                                    <i class="fas fa-download"></i>
                                    <span>Download Raffle Entries</span>
                                </a>
                                <a target="_blank" class="d-v2-btn mb-2" href="<?php echo TAOH_SITE_URL_ROOT . '/events/export_raffle_feedback/?eventtoken='.$eventtoken.'&exh='.$exhibitor_id;?>" id="download_leadgen"> 
                                    <i class="fas fa-download"></i>
                                    <span>Download Lead gen</span>
                                </a>
                            </div>        
                        </div>
                    </div>


                </div>
            </div>

        <!-- new template end-->
    </div>

    <div class="modal fade raffleAnswerModal" id="raffleAnswerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog bg-white" role="document">
            <div class="modal-content">
            <div class="modal-header bg-white align-items-center " style="border: none; border-bottom: 1px solid #d3d3d3;">
                <h3 id="raffle_question_title"></h3>
                <div class="justify-content-end">
                    <button type="button" class="btn" data-dismiss="modal" aria-label="Close">
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6153 2.22013C13.1228 1.71256 13.1228 0.888255 12.6153 0.380681C12.1077 -0.126894 11.2834 -0.126894 10.7758 0.380681L6.5 4.66055L2.22013 0.384741C1.71256 -0.122833 0.888255 -0.122833 0.380681 0.384741C-0.126894 0.892316 -0.126894 1.71662 0.380681 2.22419L4.66055 6.5L0.384742 10.7799C-0.122833 11.2874 -0.122833 12.1117 0.384742 12.6193C0.892316 13.1269 1.71662 13.1269 2.22419 12.6193L6.5 8.33945L10.7799 12.6153C11.2874 13.1228 12.1117 13.1228 12.6193 12.6153C13.1269 12.1077 13.1269 11.2834 12.6193 10.7758L8.33945 6.5L12.6153 2.22013Z" fill="#D3D3D3"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                
                    <div class="row mx-0">
                        <div class="form-group col-lg-10">
                            <label for="">Post your reply</label>
                            <textarea name="raffle_answer" id="raffle_answer" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="px-3 mb-4">
                        <button type="button" id="submitRaffleAnswer" class="btn btn-primary">Submit</button>
                    </div>
                
            </div>
            </div>
        </div>
    </div>

    <!-- <div class="modal fade writereview" id="writereviewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog bg-white" role="document">
            <div class="modal-content">
            <div class="modal-header bg-white align-items-center " style="border: none; border-bottom: 1px solid #d3d3d3;">
                <h3>Comments / questions</h3>
                <div class="justify-content-end">
                    <button type="button" class="btn" data-dismiss="modal" aria-label="Close">
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6153 2.22013C13.1228 1.71256 13.1228 0.888255 12.6153 0.380681C12.1077 -0.126894 11.2834 -0.126894 10.7758 0.380681L6.5 4.66055L2.22013 0.384741C1.71256 -0.122833 0.888255 -0.122833 0.380681 0.384741C-0.126894 0.892316 -0.126894 1.71662 0.380681 2.22419L4.66055 6.5L0.384742 10.7799C-0.122833 11.2874 -0.122833 12.1117 0.384742 12.6193C0.892316 13.1269 1.71662 13.1269 2.22419 12.6193L6.5 8.33945L10.7799 12.6153C11.2874 13.1228 12.1117 13.1228 12.6193 12.6153C13.1269 12.1077 13.1269 11.2834 12.6193 10.7758L8.33945 6.5L12.6153 2.22013Z" fill="#D3D3D3"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <form  action="<?= taoh_site_ajax_url(1); ?>" id="review_form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="taoh_action" value="add_exhibitor_comments">
                    <input type="hidden" name="eventtoken" value="<?= $eventtoken ?? ''; ?>">
                    <input type="hidden" name="ptoken" value="<?= $ptoken ?? ''; ?>">
                    <input type="hidden" name="exhibitor_id" value="<?= $exhibitor_id ?? ''; ?>">
                
                    <div class="row mx-0">
                        <div class="form-group col-lg-10">
                            <label for="">Write comments / questions</label>
                            <textarea name="exh_review" id="exh_review" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="px-3 mb-4">
                        <button type="submit" name="review_submit"  id="review_submit" class="btn btn-submit">Submit</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div> -->

    <div class="modal fade writereview" id="writereviewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog bg-white" role="document">
            <div class="modal-content">
            <div class="modal-header bg-white align-items-center " style="border: none; border-bottom: 1px solid #d3d3d3;">
                <h3 class="fs-20 fw-500">Interested? Let's Talk!</h3>
                <div class="justify-content-end">
                    <button type="button" class="btn" data-dismiss="modal" aria-label="Close">
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6153 2.22013C13.1228 1.71256 13.1228 0.888255 12.6153 0.380681C12.1077 -0.126894 11.2834 -0.126894 10.7758 0.380681L6.5 4.66055L2.22013 0.384741C1.71256 -0.122833 0.888255 -0.122833 0.380681 0.384741C-0.126894 0.892316 -0.126894 1.71662 0.380681 2.22419L4.66055 6.5L0.384742 10.7799C-0.122833 11.2874 -0.122833 12.1117 0.384742 12.6193C0.892316 13.1269 1.71662 13.1269 2.22419 12.6193L6.5 8.33945L10.7799 12.6153C11.2874 13.1228 12.1117 13.1228 12.6193 12.6153C13.1269 12.1077 13.1269 11.2834 12.6193 10.7758L8.33945 6.5L12.6153 2.22013Z" fill="#D3D3D3"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <form  action="<?= taoh_site_ajax_url(1); ?>" id="review_form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="taoh_action" value="add_exhibitor_comments">
                    <input type="hidden" name="eventtoken" value="<?= $eventtoken ?? ''; ?>">
                    <input type="hidden" name="ptoken" value="<?= $ptoken ?? ''; ?>">
                    <input type="hidden" name="exhibitor_id" value="<?= $exhibitor_id ?? ''; ?>">
                
                    <div class="row mx-0">
                        <div class="form-group col-xl-6">
                            <label for="">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="leadgen_username" id="leadgen_username" class="form-control" value="<?php echo $puser_name;?>" required>
                        </div>
                        <div class="form-group col-xl-6">
                            <label for="">Email Address <span class="text-danger">*</span></label>
                            <input type="text" name="leadgen_useremail" id="leadgen_useremail" class="form-control" value="<?php echo $puser_email;?>" required>
                        </div>
                    </div>
                    <div class="row mx-0">
                        <div class="form-group col-xl-6">
                            <label for="">Current/Past Organization</label>
                            <?php echo field_company($user_info_obj->company ?? '', 1); ?>
                        </div>
                        <div class="form-group col-xl-6">
                            <label for="">Mobile Number</label>
                            <input type="text" name="leadgen_mobile" id="leadgen_mobile" class="form-control" value="">
                        </div>
                    </div>
                    <div class="row mx-0">
                        <div class="form-group col-xl-6">
                            <label for="">Purpose of Enquiry <span class="text-danger">*</span></label>
                            <select class="form-control" name="leadgen_purpose" id="leadgen_purpose" onchange="toggleOtherPurpose()" required>
                                <option value="">--Select--</option>
                                <option value="product_info">Product Information</option>
                                <option value="pricing">Pricing or Quote Request</option>
                                <option value="request_demo">Request a Demo</option>
                                <option value="partnership_opportunity">Partnership Opportunity</option>
                                <option value="tech_support">Technical Support</option>
                                <option value="other">Other(Please Specify)</option>
                            </select>
                            <input type="text" class="form-control" name="other_purpose" id="other_purpose" value="" placeholder="Please specify..." style="display:none;" >
                        </div>
                        <div class="form-group col-xl-6">
                            <label for="">Your Message <span class="text-danger">*</span></label>
                            <textarea name="leadgen_message" id="leadgen_message" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="row mx-0">
                        <div class="form-group col-lg-12">
                            <input type="checkbox" name="leadgen_updates_required" id="leadgen_updates_required" value="1" checked>
                            <label for="leadgen_updates_required">Keep me posted about updates and offers</label>
                        </div>
                    </div>
                    <div class="px-3 mb-4">
                        <button type="submit" name="review_submit"  id="review_submit" class="btn btn-submit">Submit Enquiry</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>
    

</div>


    <script type="text/javascript">
        const isLoggedIn = <?= json_encode($taoh_user_is_logged_in); ?>;
        let eventtoken = '<?= $eventtoken ?? ''; ?>';
        let eventToken = '<?= $eventtoken ?? ''; ?>';
        let exhibitor_id = '<?= $exhibitor_id ?? 0; ?>';
        const my_pToken = '<?= $ptoken ?? ''; ?>';
        const my_username = '<?= $puser_name ?? ''; ?>';
        let TAOH_CURR_APP_URL = '<?= TAOH_CURR_APP_URL; ?>';
        let is_user_rsvp_done = <?= json_encode($is_user_rsvp_done); ?>;
        const isValidUser = <?= json_encode($valid_user); ?>;
        let click_view = '<?= $click_view ?? 'view'; ?>';


        $(document).ready(function () {
            if (isLoggedIn) {
                save_metrics('exhibitor', click_view, eventToken);
            }

            function getEventExhibitorInfo(requestData, serverFetch = false, callback = null) {
                if (!requestData.eventtoken || !requestData.exhibitor_id) return;

                const event_exhibitor_key = `event_MetaInfo_${requestData.eventtoken}_exhibitor_${requestData.exhibitor_id}`;

                const handleResponse = (response, saveToDB = true) => {
                    console.log(response);
                    if (response.success) {
                        if (saveToDB) {
                            IntaoDB.setItem(objStores.event_store.name, {
                                taoh_data: event_exhibitor_key,
                                values: response,
                                timestamp: Date.now()
                            });
                        }

                        if (typeof callback === 'function') {
                            callback(requestData, response);
                        }
                    } else {
                        console.log('Failed to fetch event exhibitor details! Try Again');
                    }
                };

                if (serverFetch) {
                    $.ajax({
                        url: _taoh_site_ajax_url,
                        type: 'GET',
                        data: {
                            taoh_action: 'get_event_exhibitor',
                            token: _taoh_ajax_token,
                            eventtoken: requestData.eventtoken,
                            exhibitor_id: requestData.exhibitor_id
                        },
                        dataType: 'json',
                        success: (response) => handleResponse(response, true),
                        error: (xhr) => console.error('Error:', xhr.status)
                    });
                } else {

                    IntaoDB.getItem(objStores.event_store.name, event_exhibitor_key).then((data) => {
                        if (data?.values) {
                            handleResponse(data.values, false);
                        } else {
                            getEventExhibitorInfo(requestData, true, callback);
                        }
                    });
                }
            }

            getEventExhibitorInfo({
                eventtoken,
                exhibitor_id
            }, false, (requestData, response) => {
                if (response.success) {
                    let event_exhibitor_info = response.output;
                    let event_exhibitor_room_status = parseInt(event_exhibitor_info.exh_room_status) === 1;

                    let user_timezone;

                    if (isLoggedIn) {
                        user_timezone = '<?= taoh_user_timezone(); ?>';
                    }
                    if (!isLoggedIn || !user_timezone?.trim()) {
                        let clientTimeZone = typeof getCookie === 'function' ? getCookie('client_time_zone') : null;
                        user_timezone = convertDeprecatedTimeZone(clientTimeZone || Intl.DateTimeFormat().resolvedOptions().timeZone);
                    }
                    if (!isValidTimezone(user_timezone)) user_timezone = 'UTC';

                    const exhibitorBannersArray = [event_exhibitor_info.exh_banner].filter(url => typeof url === 'string' && url.trim() !== "" && isValidURL(url)).map(url => ({
                        src: url,
                        type: getMediaType(url)
                    }));

                    const galleryContainer = document.getElementById("exhibitor_banner_container");
                    // const mainDisplay = document.createElement("div");
                    const mainDisplay = document.getElementById("exhibitor_banner_image");
                    // mainDisplay.id = "exhibitor_banner_image";
                    // galleryContainer.before(mainDisplay);

                    function formatVideoSrc(videoSrc) {
                        if (videoSrc.includes("youtube.com")) {
                            return `https://www.youtube.com/embed/${videoSrc.split("v=")[1]?.split("&")[0]}`;
                        }
                        if (videoSrc.includes("youtu.be")) {
                            const videoId = videoSrc.split("youtu.be/")[1];
                            return `https://www.youtube.com/embed/${videoId}`;
                        }
                        if (videoSrc.includes("vimeo.com")) {
                            return `https://player.vimeo.com/video/${videoSrc.split("vimeo.com/")[1]}`;
                        }
                        return videoSrc; // For other video formats
                    }

                    function displayMedia(media) {
                        if (!media) return;

                        mainDisplay.innerHTML = "";
                        let mediaHtml = "";

                        if (media.type === "image") {
                            mediaHtml = `
                            <div class="item">
                                <div class="cover-event-image">
                                    <div class="exhibitor-bg" style="background-image: url('${media.src}');"></div>
                                    <div class="glass-overlay"></div>
                                    <img class="carou-main-img" src="${media.src}" alt="Image 2">
                                </div>
                            </div>
                        `;
                            /* <div class="cover-event-image">
                                    <div class="exhibitor-bg" style="background-image: url('${media.src}');"></div>
                                    <div class="glass-overlay"></div>
                                    <img src="${media.src}" class="main-image" alt="Event">
                                </div> */
                        } else if (media.type === "video") {
                            let videoSrc = formatVideoSrc(media.src);
                            mediaHtml = `<iframe src="${videoSrc}" class="main-media" allowfullscreen allow="autoplay" style="width: 100%; height: 480px;"></iframe>`;
                        }

                        mainDisplay.innerHTML = mediaHtml;
                    }

                    if (exhibitorBannersArray[0]) {
                        displayMedia(exhibitorBannersArray[0]);
                    } else {
                        const noImage = _taoh_site_url_root + '/assets/images/hall-detail.png';
                        const mediaHtml = `
                        <div class="item">
                            <div class="cover-event-image">
                                <div class="exhibitor-bg" style="background-image: url('${noImage}');"></div>
                                <div class="glass-overlay"></div>
                                <img src="${noImage}" class="carou-main-img" alt="Event">
                            </div>
                        </div>`;
                        $('#exhibitor_banner_image').html(mediaHtml);
                    }
                    if (event_exhibitor_info.exh_hero_button_text && isValidURL(event_exhibitor_info.exh_hero_button_url)) {
                        $('#exhibitor_banner_image').append(`<a href="${event_exhibitor_info.exh_hero_button_url}" target="_blank" class="btn hero-button">${event_exhibitor_info.exh_hero_button_text}</a>`);
                    }

                    if (event_exhibitor_info.exh_logo && isValidURL(event_exhibitor_info.exh_logo)) {
                        $('#exhibitor_logo').attr('src', event_exhibitor_info.exh_logo);
                    }

                    $('#exhibitor_title').text(taoh_desc_decode_new(event_exhibitor_info.exh_session_title));
                    $('#exhibitor_subtitle').text(taoh_desc_decode_new(event_exhibitor_info.exh_subtitle));

                    let exhibitorWebsiteUrl = event_exhibitor_info.exh_hero_button_url || '';
                    if (exhibitorWebsiteUrl.trim()) {
                        if (isValidURL(exhibitorWebsiteUrl)) {
                            $('#exhibitor_website_blk').html(`<div class="hall-text-sm-bold mb-2">
                            <a href="${exhibitorWebsiteUrl}" class="btn link" target="_blank" id="exhibitor_website"><span>${exhibitorWebsiteUrl}</span></a></div>`);
                        } else {
                            $('#exhibitor_website_blk').html(`<div class="btn link" id="exhibitor_website">
                            <span title="${exhibitorWebsiteUrl}">${exhibitorWebsiteUrl}</span></div>`);
                        }
                        $('#exhibitor_website_blk').show();
                    }

                    let exhibitorLocationUrl = event_exhibitor_info.exh_room_location || '';
                    if (event_exhibitor_room_status && exhibitorLocationUrl.trim()) {
                        if (isValidURL(exhibitorLocationUrl)) {
                            $('#exhibitor_location_blk').html(`<div class="hall-text-sm-bold mb-2"><i class="fa fa-map-marker mr-1" aria-hidden="true"></i> Location :
                            <a href="${exhibitorLocationUrl}" class="btn link" id="exhibitor_location"><span>${exhibitorLocationUrl}</span></a></div>`);
                        } else {
                            $('#exhibitor_location_blk').html(`<div class="btn link" id="exhibitor_location">
                            <i class="fa fa-map-marker mr-1" aria-hidden="true"></i> Location : <span title="${exhibitorLocationUrl}">${exhibitorLocationUrl}</span></div>`);
                        }
                        $('#exhibitor_location_blk').show();
                        $('#exhibitor_room_btn_blk').show();
                    }

                    if(!isLoggedIn || !is_user_rsvp_done) {
                        $("#exhibitor_contact_us,#write_review_blk,.star-con").addClass('disabled');
                    }

                    if ($.trim(event_exhibitor_info.exh_raffle_announce_time) != '') {
                        exh_raffle_announce_time = new Date(event_exhibitor_info.exh_raffle_announce_time);
                        let raffle_time = {
                            utc_datetime: event_exhibitor_info.exh_raffle_announce_time.replace(/[T:-]/g, '') + '00',
                            local_datetime: event_exhibitor_info.exh_raffle_announce_time.replace(/[T:-]/g, '') + '00',
                            timezone: event_exhibitor_info.exh_raffle_timezoneSelect,
                            locality: ''
                        };
                        let raffle_announce_time = format_event_timestamp(raffle_time, user_timezone, 'date', 'EEEE, dd MMM yyyy, hh:mm A', 1);

                        $('.time_timezone').html(raffle_announce_time);
                    }

                    if (event_exhibitor_info.ptoken != undefined && my_ptoken == event_exhibitor_info.ptoken) {
                        $('#perform_download').show();
                    }
                    $('#raffle_question').val(event_exhibitor_info.exh_raffle_ques);
                    $('#raffle_question_title').html(event_exhibitor_info.exh_raffle_ques + '?');
                    let exhRaffles = event_exhibitor_info.exh_raffles;
                    if (exhRaffles == 1) {
                        raffle_start = new Date(event_exhibitor_info.exh_raffle_start_time);
                        raffle_end = new Date(event_exhibitor_info.exh_raffle_stop_time);
                        const now = new Date(new Date().toLocaleString("en-US", {timeZone: user_timezone}));

                        if (event_exhibitor_info.exh_raffles_timebound_option == 0 || (event_exhibitor_info.exh_raffles_timebound_option == 1 && now >= raffle_start && now <= raffle_end)) { // raffle date conditions
                            $("#raffle_title").text(taoh_desc_decode(event_exhibitor_info.exh_raffle_title));
                            $("#raffle_description").text(taoh_desc_decode(event_exhibitor_info.exh_raffle_description));
                            $("#exhibitor_raffle_blk").show();
                        } else {
                            $("#exhibitor_raffle_blk").hide();
                        }
                    } else {
                        $("#exhibitor_raffle_blk").hide();
                        $("#download_raffle").hide();
                    }

                    /* Start : Update rating */
                    $(".rating-text,.rating-block,#avg_review").hide();
                    if (event_exhibitor_info.metrics) {
                        if (event_exhibitor_info.metrics.view && event_exhibitor_info.ptoken != undefined && my_ptoken == event_exhibitor_info.ptoken) {
                            $(".click_view").html(`(${event_exhibitor_info.metrics.view} views)`);
                        }
                        if (event_exhibitor_info.metrics.user_rating) {
                            $(`input[name="rating"][value="${event_exhibitor_info.metrics.user_rating}"]`).prop('checked', true);
                            for (i = 1; i <= event_exhibitor_info.metrics.user_rating; i++) {
                                $(`label[for="star${i}"]`).addClass('like');
                            }
                        }
                        if (event_exhibitor_info.metrics.rating_avg > 0 && event_exhibitor_info.ptoken != undefined && my_ptoken == event_exhibitor_info.ptoken) {
                            $(".rating-text,.rating-block,#avg_review").show();
                            // $("#review_count").text('('+event_exhibitor_info.metrics.rating_count+' ratings)');
                            $("#review_count").text(event_exhibitor_info.metrics.rating_count);
                            $("#average_count").text(Number(event_exhibitor_info.metrics.rating_avg.toFixed(1)));
                            /* let average = event_exhibitor_info.metrics.rating_avg;
                            $(".average-stars span").each(function (index) {
                                let starValue = index + 1; // Star position (1 to 5)
                                $(this).removeClass("filled half-filled"); // Reset previous classes

                                if (average >= starValue) {
                                    $(this).addClass("filled"); // Full star
                                } else if (average >= starValue-1 && (average - (starValue - 1)) > 0) {
                                    fillPercent = (average - (starValue - 1)) * 100;
                                    $(this).css({
                                        "background": `linear-gradient(to right, gold ${fillPercent}%, #ccc ${100-fillPercent}%)`,
                                        "-webkit-background-clip": "text",
                                        "-webkit-text-fill-color": "transparent"
                                    });
                                }
                            }); */
                        }
                    }
                    /* End : Update rating */
                    getEventBaseInfo({eventtoken}, false)
                        .then(({requestData, response}) => {
                            let event_output = response.output;
                            let event_owner = event_output.ptoken;
                            let conttoken_data = event_output.conttoken;

                            let event_title = conttoken_data?.title;

                            let event_timestamp_start_data = {
                                utc_datetime: event_output.utc_start_at,
                                local_datetime: event_output.local_start_at,
                                timezone: event_output.local_timezone,
                                locality: event_output.conttoken.locality
                            };
                            let event_timestamp_end_data = {
                                utc_datetime: event_output.utc_end_at,
                                local_datetime: event_output.local_end_at,
                                timezone: event_output.local_timezone,
                                locality: event_output.conttoken.locality
                            };
                            let event_start_at = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'EEEE, dd MMM yyyy, hh:mm A');
                            let event_end_at = format_event_timestamp(event_timestamp_end_data, user_timezone, 'date', 'EEEE, dd MMM yyyy, hh:mm A');
                            let event_live_state = eventLiveState(event_output.utc_start_at || '', event_output.utc_end_at || '', event_output.conttoken.locality, user_timezone);
                            var chat_room_status = parseInt(event_output.conttoken.chat_room_status || 1, 10);
                            var liveLink = '';
                            if (event_live_state == 'live') {
                                event_live_link = (chat_room_status == 2 && isValidUrl(event_output.conttoken.external_link))
                                    ? event_output.conttoken.external_link : '<?php echo TAOH_SITE_URL_ROOT; ?>' + '/' + '<?php echo TAOH_CURR_APP_SLUG; ?>' + '/club/' + (event_output.conttoken.title) + '-' + event_output.eventtoken;
                                if (chat_room_status) {
                                    chatroom_text = '<span>Event Live, ' + (!isValidUser ? 'Complete settings to' : 'Click to') + ' Join</span>';
                                } else {
                                    chatroom_text = '<span>Event Live</span>';
                                }
                            }

                            if (!isLoggedIn) {
                                liveLink = ` <div class="d-flex align-items-center" style="gap: 6px;">
                                        <button type="button" class="mt-3 mb-2 btn btn-primary w-100 create_referral" data-location="${location.href}" data-title="${btoa(unescape(encodeURIComponent(event_output.conttoken.title)))}" data-toggle="modal" data-target="#config-modal"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>Login & Register Now</button>
                                    </div>`;
                            } else if (event_live_state == 'before' && is_user_rsvp_done) {
                                liveLink = ` <div class="event-new-flow d-flex align-items-center" style="gap: 6px;">
                                            <span class="btn not-live d-flex align-items-center cursor-pointer px-3" style="width:200px;gap: 12px;">
                                            <svg width="17" height="23" viewBox="0 0 17 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1.4163 0C0.632908 0 0 0.642383 0 1.4375C0 2.23262 0.632908 2.875 1.4163 2.875V3.36914C1.4163 5.27383 2.16428 7.10215 3.49206 8.44981L6.49284 11.5L3.49206 14.5502C2.16428 15.8979 1.4163 17.7262 1.4163 19.6309V20.125C0.632908 20.125 0 20.7674 0 21.5625C0 22.3576 0.632908 23 1.4163 23H2.8326H14.163H15.5793C16.3627 23 16.9956 22.3576 16.9956 21.5625C16.9956 20.7674 16.3627 20.125 15.5793 20.125V19.6309C15.5793 17.7262 14.8313 15.8979 13.5035 14.5502L10.5027 11.5L13.5079 8.44981C14.8357 7.10215 15.5837 5.27383 15.5837 3.36914V2.875C16.3671 2.875 17 2.23262 17 1.4375C17 0.642383 16.3671 0 15.5837 0H14.163H2.8326H1.4163ZM4.24889 3.36914V2.875H12.7467V3.36914C12.7467 4.22266 12.4988 5.04922 12.0385 5.75H4.95704C4.50117 5.04922 4.24889 4.22266 4.24889 3.36914ZM4.95704 17.25C5.11195 17.0119 5.29341 16.7873 5.49258 16.5807L8.49779 13.535L11.503 16.5852C11.7066 16.7918 11.8836 17.0164 12.0385 17.2545H4.95704V17.25Z" fill="#000000"/>
                                            </svg>
                                            <span>Event Not Live!</span>
                                        </span>
                                         </div>`;

                            } else if (event_live_state == 'after') {
                                liveLink = ` <div class="d-flex align-items-center" style="gap: 6px;">
                                            <a href="${TAOH_CURR_APP_URL}" class="mr-lg-5 btn btn-secondary w-100"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>Ended</a>
                                         </div>`;
                            } else if (event_output.conttoken.freeze_option == 1) {
                                liveLink = ` <div class="d-flex align-items-center" style="gap: 6px;">
                                            <a href="${TAOH_CURR_APP_URL}" class="mr-lg-5 btn btn-secondary w-100"><i class="fa fa-calendar-times mr-2" aria-hidden="true"></i> Event Suspended</a>
                                         </div>`;
                            } else if (isLoggedIn && is_user_rsvp_done && (event_live_state == 'live')) {
                                if (chat_room_status) {
                                    liveLink = ` <div class="d-flex align-items-center" style="gap: 6px;">
                                            <a target="_blank" href="${event_live_link}" class="mr-lg-5 btn btn-success w-100 metrics_action" data-metrics="event_join">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80" style="width: 36px"><circle cx="40" cy="40" r="28" fill="#fff"></circle>
                                                                    
                                                    <polygon points="34,28 34,52 54,40" fill="#28A745"></polygon>
                                                    
                                                    <path d="M78 26 C84 35, 84 46, 78 54" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>
                                                    
                                                    <path d="M88 10 C104 28, 104 54, 88 70" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>
                                            </svg>
                                            ${chatroom_text}</a>
                                         </div>`;
                                } else {
                                    liveLink = ` <div class="d-flex align-items-center" style="gap: 6px;">
                                            <a href="javascript:void(0)" class="mr-lg-5 btn btn-success w-100">
                                            
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80" style="width: 36px"><circle cx="40" cy="40" r="28" fill="#fff"></circle>
                                                                    
                                                                    <polygon points="34,28 34,52 54,40" fill="#28A745"></polygon>
                                                                    
                                                                    <path d="M78 26 C84 35, 84 46, 78 54" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>
                                                                    
                                                                    <path d="M88 10 C104 28, 104 54, 88 70" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>
                                                            </svg>
                                                             ${chatroom_text}</a>
                                         </div>`;
                                }
                            }
                            // console.log('liveLink : '+liveLink);
                            $("#liveLink").html(liveLink);

                            let exhibitorBreadcrumbHTML = `<li class="nav-item"><a href="${_taoh_site_url_root}/events">Events</a><svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"></path></svg></li>
                            <li class="nav-item"><a href="${_taoh_site_url_root}/events/chat/id/events/${eventtoken}">${event_title}</a><svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"></path></svg></li>
                            ${event_exhibitor_info.exh_session_title ? `<li class="nav-item">${taoh_desc_decode(event_exhibitor_info.exh_session_title)}</li>` : ''}
                        `;
                            $('#exhibitor_breadcrumb').append(exhibitorBreadcrumbHTML);
                            $("#event_title").text(event_title);

                            let localized_event_start_data = get_localized_event_data(event_timestamp_start_data, user_timezone);
                            let localized_event_ends_data = get_localized_event_data(event_timestamp_end_data, user_timezone);

                            
                            $('#event_start_datetime').text(formatEventDateTime(localized_event_start_data, localized_event_ends_data));
                            
                            //$("#event_start_datetime").text(event_start_at);
                            $("#event_end_datetime").text(event_end_at);

                            let event_organizer_ptokens = (conttoken_data.event_organizer_ptokens || "")
                                .split(',')
                                .map(token => token.trim())
                                .filter(token => token);

                            if(event_owner) event_organizer_ptokens.push(event_owner);

                            let event_instance_owner = conttoken_data.ptoken;
                            event_organizer_ptokens.push(event_instance_owner);

                            if (event_organizer_ptokens.includes(my_ptoken)) {
                                $(".overallrating").hide();
                                $(".review_count").hide();
                            }


                            let exhibitorExternalRoomUrl = event_exhibitor_info.exh_external_video_room_link || '';
                            let enable_tao_networking = event_exhibitor_info.enable_tao_networking || '';
                            let exhStreamingLinkUrl = event_exhibitor_info.exh_streaming_link || '';

                            let disableJoinBtn = 'disabled';
                            if (isLoggedIn && is_user_rsvp_done && event_live_state == 'live') {
                                disableJoinBtn = '';
                            }

                            if(disableJoinBtn == '' && event_exhibitor_info.exh_state != "live" && event_exhibitor_info.exh_state != "active") {
                                disableJoinBtn = 'disabled';
                            }

                            if (event_exhibitor_room_status && (exhibitorExternalRoomUrl.trim() || (enable_tao_networking == '1' || enable_tao_networking == 'on') || (exhStreamingLinkUrl.trim() && exhStreamingLinkUrl != ''))) {
                                const exhibitor_video_room_links = $('#exhibitor_video_room_links');

                                if ((enable_tao_networking == 0 || enable_tao_networking == 'off') && exhibitorExternalRoomUrl != '') {
                                    if (isValidURL(exhibitorExternalRoomUrl)) {
                                        exhibitor_video_room_links.html(`<a target="_blank" href="${exhibitorExternalRoomUrl}" class="btn v2-room-btn py-2 join_video_link" id="external_video_room" ${disableJoinBtn}>
                                <span class="px-2">Join ${taoh_desc_decode(event_exhibitor_info.exh_session_title)} Room</span></a>`); // Video
                                    } else {
                                        exhibitor_video_room_links.html(`<div class="btn v2-room-btn py-2" id="external_video_room" ${disableJoinBtn}>
                                <span class="px-2" title="${exhibitorExternalRoomUrl}">Join ${taoh_desc_decode(event_exhibitor_info.exh_session_title)} Room ${exhibitorExternalRoomUrl}</span></div>`); // Video
                                    }
                                } else {
                                    exhibitor_video_room_links.html(`<a target="_blank" href="<?php echo TAOH_SITE_URL_ROOT; ?>/<?php echo TAOH_CURR_APP_SLUG; ?>/club/${eventtoken}-${eventtoken}?exhbitor_id=${exhibitor_id}&exhbitor_name=${encodeURIComponent(event_exhibitor_info.exh_session_title)}" class="btn v2-room-btn py-2 join_networking" id="external_video_room" ${disableJoinBtn}>
                            <span class="px-2">Join ${taoh_desc_decode(event_exhibitor_info.exh_session_title)} Room</span></a>`);
                                }
                                exhibitor_video_room_links.show();
                                $('#exhibitor_room_btn_blk').show();
                            }
                        });

                    const safeMessage = document.createElement('pre');
                    safeMessage.textContent = taoh_desc_decode_new(event_exhibitor_info.exh_description) || '';
                    // let safeMessageHtml = event_exhibitor_info.exh_description || '';
                    /* let safeMessageHtml = safeMessage.innerHTML.replace(/\n/g, '<br>')
                        .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;').replace(/\r\n/g, '<br>'); */
                    let safeMessageHtml = safeMessage.innerHTML
                        .replace(/\r\n/g, '<br>')
                        .replace(/\n/g, '<br>')
                        .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');

                    $('#exh_description').html((safeMessageHtml));

                    if (event_exhibitor_info.exh_tags != '') {
                        $.each(event_exhibitor_info.exh_tags, function (k, tag) {
                            $("#exh_tags").append("<span>" + tag + "</span>|");
                        });
                    }


                    let exhibitorContactEmail = event_exhibitor_info.exh_contact_email || '';
                    if (event_exhibitor_info.enable_leadgen_form != 1 && exhibitorContactEmail.trim()) {
                        // $('#exhibitor_contact_us').attr('href', 'mailto:' + exhibitorContactEmail);
                        $('#exhibitor_contact_us').attr('data-email', exhibitorContactEmail);
                        $('#exhibitor_contact_us').show();
                        $('#write_review_blk').hide();
                        $("#download_leadgen").hide();
                    } else {
                        $('#write_review_blk').show();
                    }

                    if ($.trim(event_exhibitor_info.exh_winner_profile) != '' && event_exhibitor_info.exh_winner_profile != undefined) {
                        getUserInfo(event_exhibitor_info.exh_winner_profile).then((event_userinfo) => {
                            if (!event_userinfo.is_unknown) {
                                user_info_type = event_userinfo.type;

                                $(".emp-badge").html(user_info_type);
                                if (event_userinfo.avatar_image != '') {
                                    $(".n-participants-img").attr("src", event_userinfo.avatar_image);
                                } else if (event_userinfo.avatar != '') {
                                    $(".n-participants-img").attr("src", '<?php echo TAOH_OPS_PREFIX . '/avatar/PNG/128/'; ?>' + event_userinfo.avatar + '.png');
                                }
                                $(".n-participants-name").html(event_userinfo.chat_name);
                                $("#n-full-location").html(event_userinfo.full_location);
                                $.each(event_userinfo.title, function (i, title) {
                                    $("#n-role").html(title.value);
                                    $("#n-role").attr('title', title.slug);
                                });
                                $.each(event_userinfo.skill, function (i, skills) {
                                    $("#skill-list").append(`<p class="less-content-z3mbltb5mrf1"><span class="btn btn-sm skill_list skill_directory" style="margin-right:5px;background-color:#797f871a; font-size:12px;" data-skillid="${i}" data-skillslug="${skills.slug}">${skills.value}</span></p>`);
                                });

                                $('.winner_profile').show();
                                $('#exhibitor_raffle_blk').addClass('disabled');
                            } else {
                                $('.winner_profile').hide();
                            }
                        });
                    } else {
                        $('.winner_profile').hide();
                    }

                    if (event_exhibitor_info.enable_leadgen_form != 1 && exhibitorContactEmail.trim() && exhRaffles != 1) {
                        $("#perform_download").hide();
                    }
                    if (event_exhibitor_info.exh_raffle_status != undefined && event_exhibitor_info.exh_raffle_status == 'closed') {
                        $("#avail_now").attr('disabled', true);
                        $(".avail_now").addClass('disabled');
                    }
                }

                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'POST',
                    data: {
                        taoh_action: 'get_event_exhibitor_raffle',
                        token: _taoh_ajax_token,
                        ptoken: my_pToken,
                        username: my_username,
                        eventtoken: eventtoken,
                        exhibitor_id: exhibitor_id
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            // console.log(response.output);
                            var result = $.grep(response.output, function (obj) {
                                return obj["ptoken"] === my_pToken;
                            });
                            if (result.length > 0) {
                                $("#avail_now").attr('disabled', true);
                                $(".avail_now").addClass('disabled');
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log('Error:', xhr.status + '   error : ' + error);
                    }
                });

                if (!isLoggedIn || !is_user_rsvp_done) {
                    $("#avail_now").attr('disabled', true);
                    $(".avail_now").addClass('disabled');
                }
                $('.aw').awloader('hide');

                $('input[name="rating"]').on('change', function () {
                    save_metrics('exhibit_rating', 'click', eventtoken);
                    $('.aw').awloader('show');
                    $.ajax({
                        url: _taoh_site_ajax_url,
                        type: 'post',
                        data: {
                            'action': 'update_exhibitor_rating',
                            'taoh_action': 'update_exhibitor_rating',
                            'exhibitor_id': exhibitor_id,
                            'eventtoken': eventtoken,
                            'ptoken': my_pToken,
                            'rating': $(this).val()
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                taoh_set_success_message('Rating updated successfully', false);
                                $('.aw').awloader('hide');
                                event_exhibitor_key = `event_MetaInfo_${eventtoken}_exhibitor_${exhibitor_id}`;
                                IntaoDB.removeItem(objStores.event_store.name, event_exhibitor_key);
                                location.reload();
                            } else {
                                taoh_set_error_message('Failed to process your data! Try Again', false);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.log('Error:', xhr.status);
                        }
                    });
                });
            });

            $('#review_form').validate({
                rules: {
                    exh_review: {
                        required: true,
                    }
                },
                messages: {
                    exh_review: {
                        required: "Comments is required",
                    }
                },
                submitHandler: function (form) {

                    let review_form = $('#review_form');
                    let formData = new FormData(form);

                    let submit_btn = review_form.find('button[type="submit"]');
                    submit_btn.prop('disabled', true);

                    let submit_btn_icon = submit_btn.find('i');
                    submit_btn_icon.removeClass('fa-arrow-circle-o-right').addClass('fa-spinner fa-spin');

                    $("#review_submit").attr('disabled', true);
                    $.ajax({
                        url: review_form.attr('action'),
                        type: 'post',
                        data: formData,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function (response) {
                            // console.log(response);
                            if (response.success) {
                                submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-arrow-circle-o-right');
                                submit_btn.prop('disabled', false);
                                taoh_set_success_message('Comments Saved Successfully.');
                                $('#review_form')[0].reset();
                                $("#writereviewModal").modal("hide");
                                location.reload();
                            } else {
                                submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-arrow-circle-o-right');
                                submit_btn.prop('disabled', false);
                                taoh_set_error_message('Failed to process your data! Try Again', false);
                            }
                        },
                        error: function (xhr, status, error) {
                            submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-arrow-circle-o-right');
                            submit_btn.prop('disabled', false);
                            console.log('Error:', xhr.status);
                        }
                    });


                }
            });

            <!-- carousel script -->
            $('.exhibitor-carousel').owlCarousel({
                loop: true,
                margin: 10,
                nav: false, // Disable default nav
                dots: false, // Disable dots
                items: 1
            });

            // Custom navigation
            $('.exh-owl-prev').click(function () {
                $('.owl-carousel').trigger('prev.owl.carousel');
            });

            $('.exh-owl-next').click(function () {
                $('.owl-carousel').trigger('next.owl.carousel');
            });
            <!-- /carousel script -->

            <!-- star hover script -->
            const stars = document.querySelectorAll('.star-con .star');
            stars.forEach((star, index) => {
                star.addEventListener('mouseenter', () => {
                    for (let i = 0; i <= index; i++) {
                        stars[i].classList.add('hovered');
                    }
                });

                star.addEventListener('mouseleave', () => {
                    stars.forEach(s => s.classList.remove('hovered'));
                });
            });
            <!-- /star hover script -->
        });

        function submitRaffleQuestion() {
            var my_reply = '';
            var raffle_question = $('#raffle_question').val();
            if (raffle_question != '') {
                my_reply = $('#raffle_answer').val();
                if (my_reply == '') {
                    taoh_set_error_message('Please enter your reply!', false);
                    return false;
                }
            }


            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'POST',
                data: {
                    taoh_action: 'update_event_exhibitor_raffle',
                    token: _taoh_ajax_token,
                    ptoken: my_pToken,
                    username: my_username,
                    eventtoken: eventtoken,
                    exhibitor_id: exhibitor_id,
                    answer: my_reply
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $("#avail_now").attr('disabled', true);
                        $(".avail_now").addClass('disabled');
                        $('#raffleAnswerModal').modal('hide');
                        $('#rafflesDetailModal').modal('show');
                        taoh_set_success_message('You’ve successfully entered the raffle! Good luck!', false);
                    }
                },
                error: function (xhr, status, error) {
                    console.log('Error:', xhr.status + '   error : ' + error);
                }
            });

        }

        $(document).on('click', '#avail_now', function () {
            save_metrics('exhibit_avail_raffle', 'click', eventToken);
            var raffle_question = $('#raffle_question').val();
            if (raffle_question != '') {
                $('#raffleAnswerModal').modal('show');
            } else {
                submitRaffleQuestion();
            }

        });

        $(document).on('click', '#submitRaffleAnswer', function () {
            submitRaffleQuestion();
        });

        async function getUserInfo(pToken_to, ops = 'public', serverFetch = false) {
            if (!pToken_to?.trim()) return null;

            let userInfo = {};

            if (!serverFetch) {
                // Try to get userInfo from IndexedDB
                if (!userInfo.ptoken) {
                    const user_info_key = 'user_info_list';
                    const intao_data = await IntaoDB.getItem(objStores.common_store.name, user_info_key);
                    if (intao_data?.values && intao_data.values[ops] && intao_data.values[ops][pToken_to]) {
                        let userInfoObj = intao_data.values[ops][pToken_to];
                        // Check if data is expired (expires after 2 day)
                        if (userInfoObj.last_fetch_time && (Date.now() - userInfoObj.last_fetch_time) <= 172800000) {
                            userInfo = userInfoObj;
                        }
                    }
                }
            }

            // Fetch userInfo from server if not found locally
            if (!userInfo.ptoken) {
                const formData = {
                    taoh_action: 'taoh_user_info',
                    ops: ops,
                    ptoken: pToken_to
                };

                try {
                    const srv_userInfoObj = await fetchUserInfoFromServer(formData);
                    srv_userInfoObj.last_fetch_time = Date.now();
                    userInfo = srv_userInfoObj;
                } catch (e) {
                    console.log('getUserInfo error:', e);
                }
            }

            // If userInfo not found, set default values
            if (!userInfo.ptoken) {
                userInfo = {
                    ptoken: pToken_to,
                    chat_name: 'Unknown Name',
                    avatar: 'default',
                    full_location: 'Unknown Location',
                    type: 'Unknown Type',
                    is_unknown: true,
                    last_fetch_time: Date.now()
                };
            }

            return userInfo;
        }

        function toggleOtherPurpose() {
            const select = document.getElementById("leadgen_purpose");
            const otherInput = document.getElementById("other_purpose");

            if (select.value === "other") {
                otherInput.style.display = "inline";
                otherInput.setAttribute("required", "required");
            } else {
                otherInput.style.display = "none";
                otherInput.removeAttribute("required");
                otherInput.value = "";
            }
        }

        $(document).on('click', '#exhibitor_contact_us', function () {
            save_metrics('exhibit_contact_us', 'click', eventToken);
        });

        $(document).on('click', '#write_review_blk', function () {
            save_metrics('exhibit_write_review', 'click', eventToken);
        });

        $(document).on('click', '.join_networking', function () {
            save_metrics('exhibit_join_networking', 'click', eventToken);
        });

        $(document).on('click', '.join_video_link', function () {
            save_metrics('exhibit_join_video_link', 'click', eventToken);
        });

        $(document).on('click', '.metrics_action', function () {
            let action = $(this).data('metrics');
            save_metrics(action, 'click', eventToken);
        });
    </script>

<?php taoh_get_footer(); ?>