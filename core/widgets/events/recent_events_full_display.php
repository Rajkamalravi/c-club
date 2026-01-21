<?php
    $ops = 'list';
    $limit = 4;
    $offset =  0;
 
    $taoh_call = "events.get";
    $taoh_vals = array(
        'mod' => 'events',
        'key' => (defined('TAOH_EVENTS_GET_LOCAL') && TAOH_EVENTS_GET_LOCAL) ? TAOH_API_SECRET : TAOH_API_DUMMY_SECRET,
        'token' => taoh_get_dummy_token(1),
        'local' => TAOH_EVENTS_GET_LOCAL,
        'ops' => $ops,
        'limit' => $limit,
        'offset' => $offset,
        'cfcc5h'=> 1,
        //'cfcc5h'=> 1, //cfcache newly added
    );
    if($exclude_eventtoken !=''){
        $taoh_vals['exclude_eventtoken'] = $exclude_eventtoken;
    }
    //$taoh_vals[ 'cfcache' ] = hash('sha256', $taoh_call . serialize($taoh_vals));
    //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals);die;
    $data = json_decode(taoh_apicall_get($taoh_call, $taoh_vals, TAOH_API_PREFIX, 1),true);
    //==print_r($data);die;
    if($data['success']) {
        $event_details = $data['output']['list'];
        foreach($event_details as $event_detail) {
        //echo "<pre>";print_r($event_detail);die;
        $event_title = ucfirst(taoh_title_desc_decode($event_detail['title']));
        $event_image = $event_detail['event_image'];
        $event_locality = $event_detail['locality'] !='' ? $event_detail['locality'] : 0;
        $event_timezone = $event_detail['local_timezone'];
        $event_utc_start = $event_detail['utc_start_at'];
        $event_utc_end = $event_detail['utc_end_at'];
        $event_local_start = $event_detail['local_start_at'];
        $conttoken = $event_detail['conttoken'];
        $eventtoken = $event_detail['eventtoken'];
        if($event_image == ''){
            $event_image = TAOH_SITE_URL_ROOT."/assets/images/event.jpg";
        }
        $share_link = TAOH_SITE_URL_ROOT.'/events/d/'.slugify2($event_title).'-'.$eventtoken;
        $lobby_link = TAOH_SITE_URL_ROOT.'/events/chat/id/events/'.$eventtoken;
        $event_type = strtolower($events_data['event_type'] ?? 'virtual');

        //echo "=========".$event_type;
?>
                    <div style="flex: 1; flex-basis: 150px; max-width: 280px;" class="dash_metrics" data-metrics="view" 
                    conttoken="<?php echo $conttoken; ?>" data-type="events" data-conttoken="<?php echo $conttoken; ?>" 
                    event-url="<?php echo $share_link; ?>">
                        <div class="card event-listing-block-row p-0" style="">

                            <div class="club-event-container">
                                <a onclick="event.stopPropagation();" href="<?php echo $share_link; ?>" target="_blank" >
                                <div class="club-events-bg" style="background-image: url(<?php echo $event_image; ?>)"></div>
                                <div class="club-glass-overlay"></div>
                                <img class="club-event-image lazy" src="<?php echo $event_image; ?>" data-src="<?php echo $event_image; ?>" alt="<?php echo $event_title; ?>"></a>
                           </div>
                            <div class="card-body p-3">
                                
                                <span class="badge-detail-block w-100 d-inline-block flex-wrap-reverse justify-content-between">
                                    <ul class="d-flex flex-wrap" style="float: left;">
                                        <li class="mt-1 d-flex align-items-center" style="color: #ffffff; background: #2557A7; border-radius: 8px;">
                                            <svg class="mx-1" width="13" height="13" viewBox="0 0 7 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M2 0C2.27656 0 2.5 0.223437 2.5 0.5V1H4.5V0.5C4.5 0.223437 4.72344 0 5 0C5.27656 0 5.5 0.223437 5.5 0.5V1H6.25C6.66406 1 7 1.33594 7 1.75V2.5H0V1.75C0 1.33594 0.335938 1 0.75 1H1.5V0.5C1.5 0.223437 1.72344 0 2 0ZM0 3H7V7.25C7 7.66406 6.66406 8 6.25 8H0.75C0.335938 8 0 7.66406 0 7.25V3ZM5.14062 4.76562C5.2875 4.61875 5.2875 4.38125 5.14062 4.23594C4.99375 4.09063 4.75625 4.08906 4.61094 4.23594L3.12656 5.72031L2.39219 4.98594C2.24531 4.83906 2.00781 4.83906 1.8625 4.98594C1.71719 5.13281 1.71562 5.37031 1.8625 5.51562L2.8625 6.51562C3.00937 6.6625 3.24688 6.6625 3.39219 6.51562L5.14062 4.76562Z" fill="white"></path>
                                            </svg>

                                            <span class="mx-1"><?php 
                                                echo event_time_display($event_utc_start,$event_locality,$event_timezone);
                                            ?></span>
                                        </li>
                                    </ul>
                                    <span class="bookmark-icon-right"></span>
                                </span>

                                <h4 class="my-2 b-2 fs-18" style="font-weight: 500;height: 43px;overflow: hidden;">
                                    <a href="<?php echo $share_link;?>" data-metrics="view" class="click_metrics">
                                    <?php echo $event_title; ?>
                                    </a>
                                </h4>                             
                                
                                <div class="type_display event-type ">
                                   
                                    <span>
                                        <?php
                    
                                            if ($event_type === 'in-person') {
                                                echo '<svg width="21" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M14.8079 3.56198C15.2836 3.56198 15.7399 3.37434 16.0762 3.04034C16.4126 2.70634 16.6016 2.25334 16.6016 1.78099C16.6016 1.30864 16.4126 0.85564 16.0762 0.52164C15.7399 0.187639 15.2836 0 14.8079 0C14.3322 0 13.876 0.187639 13.5396 0.52164C13.2033 0.85564 13.0143 1.30864 13.0143 1.78099C13.0143 2.25334 13.2033 2.70634 13.5396 3.04034C13.876 3.37434 14.3322 3.56198 14.8079 3.56198ZM11.6579 7.43934C11.6952 7.4245 11.7289 7.40966 11.7662 7.39482L11.1347 9.75091C10.9255 10.5338 11.131 11.3686 11.684 11.966L14.3259 14.8267L15.148 18.0956C15.3087 18.7301 15.9589 19.1197 16.5978 18.9601C17.2368 18.8006 17.6292 18.155 17.4685 17.5205L16.6091 14.1032C16.5381 13.8138 16.3923 13.5504 16.1905 13.3314L14.3408 11.3278L15.062 8.89752L15.4208 9.75091C15.5852 10.1442 15.8879 10.467 16.2727 10.66L17.2705 11.1534C17.8609 11.4466 18.5783 11.2091 18.8735 10.6229C19.1687 10.0366 18.9296 9.32422 18.3392 9.0311L17.5358 8.63409L16.964 7.26866C16.3213 5.74369 14.8192 4.7493 13.1526 4.7493C12.3006 4.7493 11.4598 4.9274 10.6826 5.26876L10.3836 5.39862C9.15423 5.94034 8.21257 6.97183 7.79032 8.23707L7.69316 8.52649C7.48391 9.14983 7.82395 9.82141 8.44799 10.0292C9.07202 10.237 9.75211 9.89933 9.96137 9.2797L10.0585 8.99028C10.2715 8.35581 10.7424 7.84377 11.3552 7.57291L11.6541 7.44305L11.6579 7.43934ZM10.5368 12.4521L9.60264 14.7674L7.38301 16.9713C6.91592 17.4351 6.91592 18.1884 7.38301 18.6522C7.85011 19.1159 8.60867 19.1159 9.07576 18.6522L11.3813 16.3628C11.5532 16.1922 11.6878 15.9881 11.7774 15.7655L12.3193 14.4223L10.7984 12.7749C10.705 12.6747 10.619 12.5671 10.5368 12.4558V12.4521ZM8.23126 10.1702C7.94353 10.0069 7.58106 10.1034 7.41291 10.3891L6.21715 12.4447L4.18435 11.2796C3.61263 10.9531 2.88023 11.146 2.55139 11.7137L0.159872 15.8285C-0.168962 16.3962 0.025349 17.1235 0.597072 17.45L2.66723 18.6373C3.23896 18.9638 3.97136 18.7709 4.30019 18.2032L6.69171 14.0884C6.74777 13.9919 6.78887 13.8954 6.81503 13.7915L8.45172 10.9828C8.61614 10.6971 8.51899 10.3372 8.23126 10.1702Z" fill="#2557A7"/>
                                                        </svg>';
                                            } elseif ($event_type === 'hybrid') {
                                                echo '<svg width="21" height="30" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M6.48083 0.251601C6.84598 -0.0838669 7.40707 -0.0838669 7.76926 0.251601L13.9442 5.95158C14.1431 6.13564 14.25 6.39095 14.25 6.64923H9.97503C9.408 6.64923 8.89738 6.89861 8.55004 7.29345V6.17424C8.55004 5.91299 8.33629 5.69924 8.07504 5.69924H6.17505C5.9138 5.69924 5.70005 5.91299 5.70005 6.17424V8.07423C5.70005 8.33548 5.9138 8.54923 6.17505 8.54923H8.07504V12.3492H3.32506C2.53834 12.3492 1.90006 11.7109 1.90006 10.9242V7.59923H0.950065C0.558192 7.59923 0.20788 7.35876 0.0653809 6.99658C-0.0771186 6.63439 0.0178811 6.21877 0.305849 5.95158L6.48083 0.251601ZM10.45 9.02422V13.2992H16.15V9.02422H10.45ZM9.02504 8.54923C9.02504 8.02376 9.44957 7.59923 9.97503 7.59923H16.625C17.1505 7.59923 17.575 8.02376 17.575 8.54923V13.2992H18.525C18.7863 13.2992 19 13.513 19 13.7742C19 14.5609 18.3617 15.1992 17.575 15.1992H16.15H10.45H9.02504C8.23832 15.1992 7.60004 14.5609 7.60004 13.7742C7.60004 13.513 7.81379 13.2992 8.07504 13.2992H9.02504V8.54923Z" fill="#406CB2"/>
                                                        </svg>';
                                            } elseif ($event_type === 'virtual') {
                                                echo '<svg width="21" height="13" viewBox="0 0 20 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M0 2.16667C0 0.971615 0.996528 0 2.22222 0H11.1111C12.3368 0 13.3333 0.971615 13.3333 2.16667V10.8333C13.3333 12.0284 12.3368 13 11.1111 13H2.22222C0.996528 13 0 12.0284 0 10.8333V2.16667ZM19.4132 1.21198C19.7743 1.40156 20 1.76719 20 2.16667V10.8333C20 11.2328 19.7743 11.5984 19.4132 11.788C19.0521 11.9776 18.6146 11.9573 18.2708 11.7339L14.9375 9.56719L14.4444 9.24557V8.66667V4.33333V3.75443L14.9375 3.43281L18.2708 1.26615C18.6111 1.04609 19.0486 1.0224 19.4132 1.21198Z" fill="#2557A7"></path>
                                                        </svg>';
                                            }
                                            echo ' '.(!empty($event_type) ? ucfirst($event_type) : 'Virtual') ;
                                            
                                            

                                            ?>
                                    </span>
                                </div>	
                               <div class="mt-3">
                                    
                                    
                                    <span id="event_badge_<?php echo $eventtoken;?>"></span>
                                </div>
                                <script >
                                        //echo event_time_display($event_utc_start,$event_locality,$event_timezone);
                                        var user_timezone = '<?= taoh_user_timezone(); ?>';
                                        if (!isValidTimezone(user_timezone)) user_timezone = 'UTC';

                                        var  event_live_state = eventLiveState('<?php echo $event_utc_start; ?>' || '', '<?php echo $event_utc_end; ?>' || '', '<?php echo $event_locality;?>', user_timezone);
                                        if(event_live_state == 'live'){
                                            var event_badge = `<a href="<?php echo $lobby_link;?>" target="_blank" rel="noopener"
                                                            data-metrics="rsvp" class="btn d-flex align-items-center click_metrics btn-success" 
                                                            style="width: fit-content;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80" style="width: 36px">
                                                                    <!-- Play circle -->
                                                                    <circle cx="40" cy="40" r="28" fill="#fff"></circle>
                                                                    <!-- Play triangle -->
                                                                    <polygon points="34,28 34,52 54,40" fill="#28A745"></polygon>
                                                                    <!-- Sound wave 1 -->
                                                                    <path d="M78 26 C84 35, 84 46, 78 54" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>
                                                                    <!-- Sound wave 2 -->
                                                                    <path d="M88 10 C104 28, 104 54, 88 70" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>
                                                            </svg>
                                                            Live, Join Now!</a>`;
                                        } else if(event_live_state == 'before'){
                                            var event_badge = `<a href="<?php echo $lobby_link;?>" target="_blank" rel="noopener"
                                                                data-metrics="rsvp" class="btn d-flex align-items-center click_metrics btn-warning" 
                                                                style="width: fit-content;">
                                                                <svg width="17" height="23" viewBox="0 0 17 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M1.4163 0C0.632908 0 0 0.642383 0 1.4375C0 2.23262 0.632908 2.875 1.4163 2.875V3.36914C1.4163 5.27383 2.16428 7.10215 3.49206 8.44981L6.49284 11.5L3.49206 14.5502C2.16428 15.8979 1.4163 17.7262 1.4163 19.6309V20.125C0.632908 20.125 0 20.7674 0 21.5625C0 22.3576 0.632908 23 1.4163 23H2.8326H14.163H15.5793C16.3627 23 16.9956 22.3576 16.9956 21.5625C16.9956 20.7674 16.3627 20.125 15.5793 20.125V19.6309C15.5793 17.7262 14.8313 15.8979 13.5035 14.5502L10.5027 11.5L13.5079 8.44981C14.8357 7.10215 15.5837 5.27383 15.5837 3.36914V2.875C16.3671 2.875 17 2.23262 17 1.4375C17 0.642383 16.3671 0 15.5837 0H14.163H2.8326H1.4163ZM4.24889 3.36914V2.875H12.7467V3.36914C12.7467 4.22266 12.4988 5.04922 12.0385 5.75H4.95704C4.50117 5.04922 4.24889 4.22266 4.24889 3.36914ZM4.95704 17.25C5.11195 17.0119 5.29341 16.7873 5.49258 16.5807L8.49779 13.535L11.503 16.5852C11.7066 16.7918 11.8836 17.0164 12.0385 17.2545H4.95704V17.25Z" fill="#000000"></path>
                                                                </svg>
                                                                Event Not live</a>`;
                                        } else {
                                            var event_badge = `<a href="<?php echo $lobby_link;?>" target="_blank" rel="noopener"
                                                                data-metrics="rsvp" class="btn d-flex align-items-center click_metrics btn-secondary" 
                                                                style="width: fit-content;"><i class="fa fa-ticket mr-2" aria-hidden="true"></i></i>Event Expired</a>`;
                                        }
                                        //alert(event_badge)

                                        $('#event_badge_<?php echo $eventtoken;?>').html(event_badge);
                                    </script>
                            </div>
                        </div>
                    </div>
			    

<?php } } ?>

