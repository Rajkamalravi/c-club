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

function showErrorPage($base_path, $error_code = 1001, $error_from = '', $error_data = [])
{
    include_once $base_path . '/includes/error.php';
    taoh_get_footer();
    exit();
}

$appname = TAOH_CURR_APP_SLUG ?? 'events';
$sponsor_id = (int) taoh_parse_url(2);
$eventtoken = taoh_parse_url(3);

if (empty($sponsor_id)) {
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
    $rsvp_slug = $rsvp_status_response['output']['rsvp_slug'];

    $is_user_rsvp_done = $rsvp_status_response['success'];
}else{
    $is_user_rsvp_done =  false;
}

?>

    <link rel="stylesheet" href="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/events/css/events-sponsor-detail.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>">
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
                                <p class="e-v2-info"><span id="event_start"></span></p> <!-- Friday September 13 04:00 pm IST -->
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
                    <!-- <div class="item">

                        <div class="cover-event-image">
                            <div class="exhibitor-bg" style="background-image: url('<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png');"></div>
                            <div class="glass-overlay"></div>
                            <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png" class="carou-main-img" alt="Image 1">
                        </div>


                    </div>
                    <div class="item">
                         <div class="cover-event-image">
                            <div class="exhibitor-bg" style="background-image: url('<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png');"></div>
                            <div class="glass-overlay"></div>
                            <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png" class="carou-main-img" alt="Image 2">
                        </div>
                    </div>
                    <div class="item">
                         <div class="cover-event-image">
                            <div class="exhibitor-bg" style="background-image: url('<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png');"></div>
                            <div class="glass-overlay"></div>
                            <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png" class="carou-main-img" alt="Image 3">
                        </div>

                    </div>
                    <div class="item">
                         <div class="cover-event-image">
                            <div class="exhibitor-bg" style="background-image: url('<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png');"></div>
                            <div class="glass-overlay"></div>
                            <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png" class="carou-main-img" alt="Image 4">
                        </div>

                    </div> -->
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
                            <a href="#" class="c-v2-site exhibitor_website_blk_div">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.375 9C12.375 9.78047 12.3328 10.5328 12.259 11.25H5.74102C5.66367 10.5328 5.625 9.78047 5.625 9C5.625 8.21953 5.66719 7.46719 5.74102 6.75H12.259C12.3363 7.46719 12.375 8.21953 12.375 9ZM13.3875 6.75H17.7152C17.9016 7.4707 18 8.22305 18 9C18 9.77695 17.9016 10.5293 17.7152 11.25H13.3875C13.4613 10.5258 13.5 9.77344 13.5 9C13.5 8.22656 13.4613 7.47422 13.3875 6.75ZM17.3461 5.625H13.2434C12.8918 3.37852 12.1957 1.49766 11.2992 0.295312C14.052 1.02305 16.2914 3.01992 17.3426 5.625H17.3461ZM12.1043 5.625H5.8957C6.11016 4.34531 6.44062 3.21328 6.84492 2.2957C7.21406 1.46602 7.62539 0.864844 8.02266 0.485156C8.41641 0.1125 8.74336 0 9 0C9.25664 0 9.58359 0.1125 9.97734 0.485156C10.3746 0.864844 10.7859 1.46602 11.1551 2.2957C11.5629 3.20977 11.8898 4.3418 12.1043 5.625ZM4.75664 5.625H0.653906C1.70859 3.01992 3.94453 1.02305 6.70078 0.295312C5.8043 1.49766 5.1082 3.37852 4.75664 5.625ZM0.284766 6.75H4.6125C4.53867 7.47422 4.5 8.22656 4.5 9C4.5 9.77344 4.53867 10.5258 4.6125 11.25H0.284766C0.0984375 10.5293 0 9.77695 0 9C0 8.22305 0.0984375 7.4707 0.284766 6.75ZM6.84492 15.7008C6.43711 14.7867 6.11016 13.6547 5.8957 12.375H12.1043C11.8898 13.6547 11.5594 14.7867 11.1551 15.7008C10.7859 16.5305 10.3746 17.1316 9.97734 17.5113C9.58359 17.8875 9.25664 18 9 18C8.74336 18 8.41641 17.8875 8.02266 17.5148C7.62539 17.1352 7.21406 16.534 6.84492 15.7043V15.7008ZM4.75664 12.375C5.1082 14.6215 5.8043 16.5023 6.70078 17.7047C3.94453 16.977 1.70859 14.9801 0.653906 12.375H4.75664ZM17.3461 12.375C16.2914 14.9801 14.0555 16.977 11.3027 17.7047C12.1992 16.5023 12.8918 14.6215 13.2469 12.375H17.3461Z" fill="#FF6600"/>
                                </svg>
                                <span id="exhibitor_website_blk"></span>
                            </a>
                        </div>
                    </div>

                    <hr style="max-width: 385px; border-top: 2px solid #d3d3d3;">

                    <div class="exh-v2-desc" id="exh_description"></div>

                    <div class="exh-v2-contact mt-5 configure_now" style="display:none">
                        <img class="c-v2-img" id="exhibitor_logo-configure" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/exh-v2-3.png" alt="Exhibitor logo">
                        <p>Your exhibitor slot is not configured yet!</p>
                        <a class="btn v2-room-btn ml-auto edit-exhibitor" id="exhibitor_configure_now" target="_blank" style="max-width: 143px;" data-id="<?php echo $sponsor_id; ?>" data-type="sponsor">Configure Now</a>
                    </div>

                </div>

            </div>

        <!-- new template end-->
    </div>

</div>
<?php
require_once('events_exhibitor_form_new.php');
?>

 <script>
    window._esd_cfg = {
        isLoggedIn: <?= json_encode($taoh_user_is_logged_in); ?>,
        eventtoken: <?= json_encode($eventtoken ?? ''); ?>,
        sponsorId: <?= json_encode($sponsor_id ?? 0); ?>,
        myPtoken: <?= json_encode($ptoken ?? ''); ?>,
        myUsername: <?= json_encode($puser_name ?? ''); ?>,
        currAppUrl: <?= json_encode(TAOH_CURR_APP_URL); ?>,
        isUserRsvpDone: <?= json_encode($is_user_rsvp_done); ?>,
        rsvpSlug: <?= json_encode($rsvp_slug ?? ''); ?>,
        isValidUser: <?= json_encode($valid_user); ?>,
        userTimezone: <?= json_encode($taoh_user_is_logged_in ? taoh_user_timezone() : ''); ?>,
        currAppSlug: <?= json_encode(TAOH_CURR_APP_SLUG); ?>
    };
</script>
<script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/events/js/events-sponsor-detail.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
<!-- carousel script end -->

<?php taoh_get_footer(); ?>