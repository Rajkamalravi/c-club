<?php 
$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
$user_info_obj = taoh_user_all_info();
$valid_user = isset($user_info_obj->profile_complete) ? (bool) $user_info_obj->profile_complete : false;

$urlArr = taoh_parse_url_parse();
$eventtoken = taoh_parse_url(2);
// $eventhall = taoh_title_desc_decode($urlArr[3]);
$eventhall = taoh_parse_url(3);
$parts = explode('-', $eventhall);
$hall_id = array_pop($parts);
$eventhall = implode('-', $parts);
$eventhall = taoh_title_desc_decode($eventhall);
if($hall_id != '' && $hall_id != crc32($eventhall)){
    taoh_set_error_message('Hall ID is invalid or has changed. Please select a valid hall.');
    echo "<script>window.close();</script>";
}

$taoh_vals = array(
    'token' => taoh_get_dummy_token(1),
    'ops' => 'baseinfo',
    'mod' => 'events',
    'eventtoken' => $eventtoken ?? '',
    'cache_name' => 'event_detail_' . $eventtoken,
    //'cfcc5h' => 1 ////cfcache newly added
);
//echo taoh_apicall_get_debug('events.event.get', $taoh_vals);die();

$result = taoh_apicall_get('events.event.get', $taoh_vals);
$response = taoh_get_array($result, true);
$rsvp_slug = '';
if($taoh_user_is_logged_in){
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

    $is_user_rsvp_done = $rsvp_status_response['success'] ?? '';
    $rsvp_slug = $rsvp_status_response['output']['rsvp_slug'] ?? '';
}

$event_arr = $response['output'];
$events_data = $event_arr['conttoken'] ?? [];
$ticket_types = $events_data['ticket_types'] ?? [];
// echo "<pre>"; print_r($ticket_types); echo "</pre>";
$current_ticket_types = [];
foreach ($ticket_types as $item) {
    if ($item['slug'] === $rsvp_slug) {
        $current_ticket_types[] = $item;
    }
}

$current_ticket_type = array_values($current_ticket_types)[0];
// echo "Current Ticket Type : <pre>"; print_r($current_ticket_type); echo "</pre>";

// echo 'eventtoken : '.$eventtoken."===#".urldecode($eventhall).'#==='.$hall_id.'==='.crc32($eventhall);
taoh_get_header(); 
?>
<link rel="stylesheet" href="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/events/css/events-hall-listing.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>">

    <div class="detail-hall light-dark d-noe">
        <!-- new template start-->
            <div class="pt-4 sticky-top light-dark shadow-sm border-bottom" style="z-index: 99;">
                <div class="container">
                    <ul class="nav nav-tabs justify-content-left border-0 mt-3" id="hall_breadcrumb" role="tablist" style="line-height: 1.143;">
                        <li class="nav-item">
                            <a href="<?php echo TAOH_SITE_URL_ROOT; ?>">Home</a>
                            <svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"></path></svg>
                        </li>                            
                    </ul>

                    <div class="d-flex align-items-start flex-column flex-lg-row py-3" style="gap: 9px;">
                        <div class="flex-grow-1">
                            <h5 class="e-v2-title mb-1" id="event_title"></h5>
                            <div class="d-flex align-items-center mb-1" style="gap: 12px;">
                                <svg style="min-width: fit-content;" width="25" height="25" viewBox="0 0 21 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 0C6.82969 0 7.5 0.698242 7.5 1.5625V3.125H13.5V1.5625C13.5 0.698242 14.1703 0 15 0C15.8297 0 16.5 0.698242 16.5 1.5625V3.125H18.75C19.9922 3.125 21 4.1748 21 5.46875V7.8125H0V5.46875C0 4.1748 1.00781 3.125 2.25 3.125H4.5V1.5625C4.5 0.698242 5.17031 0 6 0ZM0 9.375H21V22.6562C21 23.9502 19.9922 25 18.75 25H2.25C1.00781 25 0 23.9502 0 22.6562V9.375ZM15.4219 14.8926C15.8625 14.4336 15.8625 13.6914 15.4219 13.2373C14.9812 12.7832 14.2688 12.7783 13.8328 13.2373L9.37969 17.876L7.17656 15.5811C6.73594 15.1221 6.02344 15.1221 5.5875 15.5811C5.15156 16.04 5.14687 16.7822 5.5875 17.2363L8.5875 20.3613C9.02812 20.8203 9.74063 20.8203 10.1766 20.3613L15.4219 14.8926Z" fill="#2557A7"></path>
                                </svg>
                                <p class="e-v2-info"><span id="event_start_datetime"></span> to <span id="event_end_datetime"></span></p>
                            </div>
                        </div>

                        <span class="flex-shrink-lg-0" id="liveLink"></span>
                        <!-- <button type="button" class="e-d-v2-btn btn btn-success lh-1 py-2">
                            LIVE NOW! Click to Join
                        </button> -->
                        
                    </div>

                </div>
            </div>

            <!-- carousel start -->
            <div class="container exhibitor-carousel-con pt-4" id="exhibitor_banner_container">
                <div id="hall_banner_image" class="carousel slide exhibitor-carousel" data-ride="carousel">
                    
                </div>
            </div>
            <!-- carousel end -->

            <div class="container">
                <div class="exh-d-v2-left flex-grow-1 py-5 px-md-3">
                  
                    <div class="flex-grow-1 d-flex align-items-center" style="gap: 12px;">
                        <!-- logo  -->
                        <img class="c-v2-img d-none" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/exh-v2-3.png" alt="speaker logo">

                        <div class="flex-grow-1 d-flex align-items-start flex-column flex-lg-row py-3" style="gap: 9px;"> 
                            <div class="flex-grow-1">
                                <h6 class="c-v2-name" id="hall_title"></h6>
                            </div>
                            <div class="hall-tabs-con d-flex align-items-center">
                                <select class="m-0" name="search_halls" id="search_halls">
                                    <option disabled>-- Select Hall --</select>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-0 mb-4" style="max-width: 385px; border-top: 2px solid #d3d3d3;">

                    <div class="exh-v2-desc" id="hall_description"></div>

                    <br>

                    <div class="hall-list-container mx-auto tab-pane " id="hall_details_list" style="max-height: unset;">
                        
                    </div>

                   
                </div>
            </div>

        <!-- new template end-->
    </div>
    <input type="hidden" name="is_organizer" id="is_organizer" value="0" >
    <input type="hidden" name="user_profile_type" id="user_profile_type" value="" >
    <input type="hidden" name="rsvp_sponsor_title" id="rsvp_sponsor_title" value="<?php echo $current_ticket_type['title'] ?? ''; ?>">


    <script>
        window._ehl_cfg = {
            isLoggedIn: <?= json_encode($taoh_user_is_logged_in); ?>,
            isValidUser: <?= json_encode($valid_user); ?>,
            isUserRsvpDone: <?= json_encode($is_user_rsvp_done ?? ''); ?>,
            eventtoken: <?= json_encode($eventtoken ?? ''); ?>,
            eventhall: <?= json_encode($eventhall ?? ''); ?>,
            currAppUrl: <?= json_encode(TAOH_CURR_APP_URL); ?>,
            userProfileType: <?= json_encode($user_info_obj->type ?? ''); ?>,
            userTimezone: <?= json_encode($taoh_user_is_logged_in ? taoh_user_timezone() : ''); ?>,
            rsvpSponsorTitle: <?= json_encode($current_ticket_type['title'] ?? ''); ?>,
            cdnPrefix: <?= json_encode(TAOH_CDN_PREFIX); ?>,
            appSlug: <?= json_encode(TAOH_CURR_APP_SLUG); ?>
                    };
    </script>
    <script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/events/js/events-hall-listing.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>

<?php taoh_get_footer(); ?>