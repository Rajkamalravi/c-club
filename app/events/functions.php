<?php

/**
 * Event Helper Functions
 * Common utilities for event state and display
 */

/**
 * Get event state badge HTML based on current state
 * @param string $state The event state (live, after, before, prelive, postlive)
 * @param string $style Style variant ('default', 'center', 'center2')
 * @return string HTML for the state badge
 */
function get_event_state_badge($state, $style = 'default') {
    $badges = [
        'live' => [
            'default' => '<span class="event_live_suc" style="color: #ffffff;"><span class="badge badge-md badge-success">Event Live</span></span>',
            'center' => '<span class="event_live_suc text-dark py-0"><span class="badge badge-md badge-success">Event Live</span></span>',
            'center2' => '<span class="badge badge-md badge-success event_live_suc text-dark py-0">Event Live</span>',
        ],
        'after' => [
            'default' => '<span class="badge badge-md badge-secondary" style="color: #ffffff;">Event <strong>Ended</strong></span>',
            'center' => '<span class="badge badge-md badge-secondary">Event <strong>Ended</strong></span>',
            'center2' => '<span class="badge badge-md badge-secondary">Event <strong>Ended</strong></span>',
        ],
        'before' => [
            'default' => '<span class="event_live_war" style="color: #000000;"><span class="badge badge-md badge-warning">Event <strong>Not Live</strong></span></span>',
            'center' => '<span class="event_live_war py-0"><span class="badge badge-md badge-warning">Event <strong>Not Live</strong></span></span>',
            'center2' => '<span class="badge badge-md badge-warning event_live_war py-0">Event <strong>Not Live</strong></span>',
        ],
        'prelive' => [
            'default' => '<span class="badge badge-md badge-warning event_live_war" style="color: #000000;">Event <strong>Not Live</strong></span>',
            'center' => '<span class="event_live_war py-0"><span class="badge badge-md badge-warning">Event <strong>Not Live</strong></span></span>',
            'center2' => '<span class="badge badge-md badge-warning event_live_war py-0">Event <strong>Not Live</strong></span>',
        ],
        'postlive' => [
            'default' => '<span class="badge badge-md badge-secondary" style="color: #ffffff;">Event <strong>Ended</strong></span>',
            'center' => '<span class="badge badge-md badge-secondary text-dark py-0">Event <strong>Ended</strong></span>',
            'center2' => '<span class="badge badge-md badge-secondary text-dark py-0">Event <strong>Ended</strong></span>',
        ],
    ];

    $default = '<span class="badge badge-md badge-warning event_live_war" style="color: #000000;">Not Live</span>';

    return $badges[$state][$style] ?? $default;
}

/**
 * Get venue HTML based on event type
 * @param string $event_type Event type (virtual, hybrid, in-person)
 * @param array $events_data Event data array
 * @param string $eventtoken Event token
 * @param string $format Output format ('list', 'inline')
 * @return string HTML for venue display
 */
function get_venue_html($event_type, $events_data, $eventtoken, $format = 'list') {
    $lobby_link = TAOH_CURR_APP_URL . '/chat/id/events/' . $eventtoken;
    $venue = $events_data['venue'] ?? '';
    $map_link = $events_data['map_link'] ?? '';

    $venue_link = (!empty($map_link) && filter_var($map_link, FILTER_VALIDATE_URL))
        ? '<a href="' . htmlspecialchars($map_link) . '" target="_blank" class="cursor-pointer text-underline">' . htmlspecialchars($venue) . '</a>'
        : htmlspecialchars($venue);

    switch ($event_type) {
        case 'in-person':
            return ($format === 'list')
                ? '<i class="fas fa-map-marker"></i> Venue: In-Person, ' . $venue_link
                : 'Venue: In-Person, ' . $venue_link;

        case 'hybrid':
            $join_link = '<a href="' . $lobby_link . '" title="' . $lobby_link . '" target="_blank" class="cursor-pointer text-underline">Join here</a>';
            return ($format === 'list')
                ? '<i class="fas fa-map-marker"></i> Venue: Hybrid - ' . $venue_link . ' or Virtual, ' . $join_link
                : 'Venue: Hybrid - ' . $venue_link . ' Or Virtual, ' . $join_link;

        case 'virtual':
        default:
            $join_link = '<a href="' . $lobby_link . '" title="' . $lobby_link . '" target="_blank" class="cursor-pointer text-underline">Join here</a>';
            return ($format === 'list')
                ? '<i class="fas fa-map-marker"></i> Venue: Virtual, ' . $join_link
                : 'Venue: Virtual, ' . $join_link;
    }
}

/**
 * Check if event is in active registration state
 * @param string $state Event state
 * @return bool True if registration is active
 */
function is_registration_active($state) {
    return in_array($state, ['live', 'prelive', 'before'], true);
}

function event_live_status( $start_time, $end_time, $locality = 0 ){
  $my_time = tao_timestamp( );
  if ( $locality ){
    $timezone = taoh_user_timezone();
    $my_time = tao_timestamp( $timezone );
  }

  $return = 'before';
  $start_dday = floor($start_time / 100000 ) * 1000000;
  $end_dday = floor($end_time / 1000000 ) * 1000000 + 999999;
  if ( $my_time >= $start_dday ) $return = 'prelive';
  //echo "$my_time <= $end_time( ".$end_dday." ) && $my_time >= $start_time ( ".$start_dday." )";taoh_exit();
  if ( $my_time <= $end_time && $my_time >= $start_time ){
    $return = 'live';
  } else {
    if ( $my_time > $end_time ){
      $return = 'after';
      if ( $my_time <= $end_dday ) $return = 'postlive';
    }
  }
  //echo $return;taoh_exit();
  return $return;
}

function event_live_state( $start_time, $end_time, $event_status, $locality = 0 ){
  if ( $locality ){
    $timezone = taoh_user_timezone();
    date_default_timezone_set($timezone);
    $now_stamp = date( TAOH_TIMEZONE_FORMAT, time());  
    if ( $now_stamp < $start_time ) $return = 'before';
    if ( $now_stamp > $end_time ) $return = 'after';
    if ( $now_stamp >= $start_time && $now_stamp <= $end_time ) $return = 'live';
  } else {
    //date_default_timezone_set($_COOKIE['localTimeZone']);
    //$now_stamp = $_COOKIE['localTime'];  
    //$now_stamp = date( TAOH_TIMEZONE_FORMAT, time());  


   
    /*echo $utcTime;
    echo "<br>=======now_stamp====".strtotime($utcTime);
    echo "<br>=======start_time====".strtotime($start_time);
    echo "<br>====end_time=======".strtotime($end_time);*/
    date_default_timezone_set('UTC');
    $utcTime = gmdate("Y-m-d H:i:s");
    $now_stamp = strtotime($utcTime);
    $start_time = strtotime($start_time);
    $end_time = strtotime($end_time);

    if ( $now_stamp < $start_time ) $return = 'before';
    if ( $now_stamp > $end_time ) $return = 'after';
    if ( $now_stamp >= $start_time && $now_stamp <= $end_time ) $return = 'live';

    //$timezone = taoh_user_timezone();
    /*date_default_timezone_set(TAOH_DEFAULT_TIMEZONE);
    $now_stamp = date( TAOH_TIMEZONE_FORMAT, time());  
    if ( $now_stamp < $start_time ) $return = 'before';
    if ( $now_stamp > $end_time ) $return = 'after';
    if ( $now_stamp >= $start_time && $now_stamp <= $end_time ) $return = 'live';*/
    //echo "\t\r$start_time, $end_time, $event_status, $locality, $now_stamp\t\r";taoh_exit();

    /*
    if ( $event_status == TAOH_EVENTS_EVENT_START ){
      $return = 'live';
    } else {
      if ( $event_status < TAOH_EVENTS_EVENT_START && $event_status > TAOH_EVENTS_EVENT_PUBLISHED || $event_status == TAOH_EVENTS_EVENT_EXPIRED){
        $return = 'before';

      } else if ( $event_status == TAOH_EVENTS_EVENT_STOP ) {
        $return = 'after';
      }
    }
    */
  }/*else{
    date_default_timezone_set('America/Los_Angeles'); 
    $date = new DateTime("now", new DateTimeZone('America/Los_Angeles') );
    $now_stamp = $date->format('YmdHis');
    if ( $now_stamp < $start_time ) $return = 'before';
    if ( $now_stamp > $end_time ) $return = 'after';
    if ( $now_stamp >= $start_time && $now_stamp <= $end_time ) $return = 'live';
  }*/
  //echo $return;taoh_exit();
  return $return;
}



function event_action_button($event_arr, $tokenkey = 0) {
  //User Action Button
  //$loggedin = TAOH_API_TOKEN;
  if( taoh_user_is_logged_in() ) {
    //print_r($event_arr);taoh_exit();
    $timezone = taoh_user_timezone();
    $live = event_live_state( $event_arr[ 'utc_start_at' ], $event_arr[ 'utc_end_at' ], $event_arr['status'],  $event_arr['locality'] );
    //$live = 'before';
    //echo "==============".$live;taoh_exit();

    if( isset( $event_arr[ 'mystatus' ][ 'rsvptoken' ] ) && $event_arr[ 'mystatus' ][ 'rsvptoken' ] ) {
      if( $live == "live" ) {
        //RSVP IS DONE AND EVENT IS LIVE GO TO LOBBY
        
        echo '

        <span class="edit-status">
            You\'re going!
            <br/>
            <a class="edit_rsvp" href="'.TAOH_CURR_APP_URL.'/rsvp/'.$event_arr[ 'eventtoken' ].'/'.$tokenkey.'"> Edit RSVP</a>
          </span>
          ';
        echo '
          <span style="align-self: center;">
          <a data-metrics="status_click" class="btn btn-success btn-block click_metrics">
          <i class="la la-calendar-check mr-1"></i>
          Event Live!</a>
            </span>
          ';
          echo taoh_calendar_widget($event_arr);
          
          
      } else if ( $live == 'before' ) {
        //if ( $event_arr[ 'mystatus' ][ 'liveable' ] )
          //RSVP IS DONE EVENT NOT LIVE GO TO EDIT RSVP
          //echo '<span><a href="'.TAOH_CURR_APP_URL.'/rsvp/'.$event_arr[ 'eventtoken' ].'" class="btn btn-success btn-block"><i class="la la-calendar-check mr-1"></i>Edit My RSVP</a></span>';
          echo '
          <span class="edit-status">
            You\'re going!
            <br/>
            <a class="edit_rsvp" href="'.TAOH_CURR_APP_URL.'/rsvp/'.$event_arr[ 'eventtoken' ].'/'.$tokenkey.'"> Edit RSVP</a>
          </span>
          ';

          echo '
            <span style="align-self: center;">
            <a data-metrics="status_click" class="btn btn-warning click_metrics">
            <i class="la la-search mr-1"></i> Event Status!</a><br />
            </span>
          ';
          echo taoh_calendar_widget($event_arr);
      } else {
        //if ( $event_arr[ 'mystatus' ][ 'liveable' ] )
          //RSVP IS DONE EVENT NOT LIVE GO TO EDIT RSVP
          //echo '<span><a href="'.TAOH_CURR_APP_URL.'/rsvp/'.$event_arr[ 'eventtoken' ].'" class="btn btn-success btn-block"><i class="la la-calendar-check mr-1"></i>Edit My RSVP</a></span>';

          echo '
          <span class="edit-status">
            Check
            <br/>
            <a  href="'.TAOH_CURR_APP_URL.'/rsvp/'.$event_arr[ 'eventtoken' ].'"  class="edit_rsvp click_metrics">Event Lobby!</a>
          </span>
          ';

          echo '
            <span style="align-self: center;">
            <a href="'.TAOH_CURR_APP_URL.'/chat/id/events/'.$event_arr[ 'eventtoken' ].'" class="btn btn-secondary ">
            <i class=\"la la-hourglass-end mr-1\"></i> Event <strong>Ended</strong></a><br />
            </span>
          ';
      }
    } else {
      if($live == "live") {
          //RSVP IS NOT DONE GO TO RSVP
          echo '
          <span class="edit-status">
            <a class="text-danger h3" href=""> <strong>LIVE</strong>!</a>
          </span>
        ';
        //RSVP IS DONE AND EVENT IS LIVE GO TO LOBBY
        if (taoh_user_is_logged_in()) {
          echo '<span class="hide-resp"><ul class="menu--main">
          <li data-metrics="rsvplink" class="btn btn-success btn-block rsvp_btn click_metrics"><i class="la la-calendar-check mr-1"></i> Select ticket
              <ul class="sub-menu">
              </ul>
          </li>
          </ul><span>';

        }else{
          $url = TAOH_LOGIN_URL ;
          echo '<span class="hide-resp"><ul class="menu--main">
          <li class="">
                    <a class="btn btn-primary  btn-sm  rsvp-login" style="color:#ffffff;" href="'.$url.'"><i class="la la-calendar-check mr-1"></i><span>Login to Register</span></a>
                      
                  </li>
          </ul><span>';

        }
        
      
      } else if ( $live == 'before' ) {
        //RSVP IS DONE EVENT NOT LIVE GO TO EDIT RSVP
        //echo '<a href="'.TAOH_CURR_APP_URL.'/rsvp/'.$event_arr[ 'eventtoken' ].'" class="btn btn-success btn-block"><i class="la la-calendar-check mr-1"></i>Select ticket</a>';

        echo '<span class="edit-status">
            
          </span>
          ';
          if (taoh_user_is_logged_in()) { 
        echo '<span class="hide-resp"><ul class="menu--main">
        <li data-metrics="rsvplink" class="btn btn-success btn-block rsvp_btn click_metrics"><i class="la la-calendar-check mr-1"></i> Select ticket
            <ul class="sub-menu">
            </ul>
        </li>
        </ul><span>';
          }else{
            echo '<span class="hide-resp"><ul class="menu--main">
            <li class="">
                    <a class="btn btn-primary  btn-sm  rsvp-login" style="color:#ffffff;" href="'.$url.'"><i class="la la-calendar-check mr-1"></i><span>Login to Register</span></a>
                      
                  </li>
          </ul><span>';
          }
      } else {
        //RSVP IS DONE EVENT NOT LIVE GO TO EDIT RSVP
        //echo '<a href="'.TAOH_CURR_APP_URL.'/rsvp/'.$event_arr[ 'eventtoken' ].'" class="btn btn-success btn-block"><i class="la la-calendar-check mr-1"></i>Select ticket</a>';        
          echo '<span class="hide-resp"><ul class="menu--main">
          <li  class="btn btn-secondary btn-block"><i class=\"la la-hourglass-end mr-1\"></i> Event <strong>Ended</strong>
          </li>
          </ul><span>';
      }
    }
  } else {
    //NOT LOGGED IN
    $url = TAOH_LOGIN_URL;
    /*echo '<a href="javascript:void(0)" onclick="rsvp_invite()" class="btn btn-primary btn-block">
        <i class="la la-calendar-check mr-1"></i>
         Signup Today To RSVP!
      </a>';*/
      if (taoh_user_is_logged_in()) {
        echo '<span class="hide-resp"><ul class="menu--main">
            <li data-metrics="rsvplink" class="btn btn-success btn-block rsvp_btn click_metrics"><i class="la la-calendar-check mr-1"></i> Select ticket
                <ul class="sub-menu">
                </ul>
            </li>
            </ul><span>';
      }else{
        echo '<span class="hide-resp"><ul class="menu--main">
          <li class="">
                  <a class="btn btn-primary  btn-sm  rsvp-login" style="color:#ffffff;" href="'.$url.'"><i class="la la-calendar-check mr-1"></i><span>Login to Register</span></a>
                    
                </li>
        </ul><span>';
      }
  }

}

if (!function_exists('event_time_display')) {
    function event_time_display($input_date, $locality = 0, $event_timezone_abbr = '', $input = 'date', $format = 'D, M d, Y h:i A')
    {
      $user_timezone = new DateTimeZone(taoh_user_timezone());
        if ($locality) {
            // Global event
            $datetime = DateTime::createFromFormat('YmdHis', $input_date, $user_timezone);
        } else {
            // Local event
            $datetime = DateTime::createFromFormat('YmdHis', $input_date, new DateTimeZone('UTC'));
            $datetime->setTimezone($user_timezone);

            //kalpana
           /* $user_timezone = new DateTimeZone($event_timezone_abbr);
            $datetime = DateTime::createFromFormat('YmdHis', $input_date, $user_timezone);*/
        }

        return $datetime->format($format); // Output: Mon, Nov 25, 2024 09:20
    }
}

function event_state_widget($event_arr, $hide_rsvp = 1)
{
    $events_data = $event_arr['conttoken'] ?? [];

    $event_type = strtolower($events_data['event_type'] ?? 'virtual');

    $ticket_types = $event_arr['conttoken']['ticket_types'];
    $tokentyp = $event_arr['mystatus']['rsvp_slug'];
    foreach ($ticket_types as $k => $v) {
        if (strtolower(trim($v['slug'])) == strtolower(trim($tokentyp))) {
            $tokenkey = $v['title'];
        }
    }

    $state = event_live_state($event_arr['utc_start_at'], $event_arr['utc_end_at'], $event_arr['status'], $event_arr['locality']);

    if ($state == 'live') $event_state = '<span class=" event_live_suc" style="color: #ffffff;"><span class="badge badge-md  badge-success">Event Live</span></span>';
    else if ($state == 'after') $event_state = '<span class="badge badge-md  badge-secondary " style="color: #ffffff;">Event <strong>Ended</strong></span>';
    else if ($state == 'before') $event_state = '<span class=" event_live_war" style="color: #000000;"><span class="badge badge-md badge-warning">Event <strong>Not Live</strong></span></span>';
    else if ($state == 'prelive') $event_state = '<span class="badge badge-md  badge-warning event_live_war" style="color: #000000;">Event <strong>Not Live</strong></span>';
    else if ($state == 'postlive') $event_state = '<span class="badge badge-md  badge-secondary " style="color: #ffffff;">Event<strong>Ended </strong></span>';
    else $event_state = '<span class="badge badge-md  badge-warning event_live_war " style="color: #000000;">Not Live</span>';


    $event_start_at = $event_arr['local_start_at'];
    $event_end_at = $event_arr['local_end_at'];

    $event_locality = $event_arr['locality'];
    $event_timezone = $event_arr['local_timezone'];
    //$event_locality =  1;
    //echo "====================".$event_timezone;

    $event_attendie_count = $event_arr['attendees'];

    $rsvp_state = '';
    //echo $event_arr[ 'mystatus' ][ 'rsvptoken' ];exit();
    if (taoh_user_is_logged_in()) {
        if (($state == 'live' || $state == 'prelive' || $state == 'before')) {
            $rsvp_state = '
            <span class="d-flex align-items-center">
              <i class="la la-calendar-check mr-1" style="font-size: 20px;"></i> <span class="mr-1">Registration status :</span>
            </span>
            <span style="display: inline-block;">
              <ul class="menu--main">
                <li data-metrics="rsvplink" class="btn btn-success rsvp_btn click_metrics mb-0">
                <i class="la la-calendar-check mr-1"></i><span>Select ticket</span>
                    <ul class="sub-menu mobile-left">
                    </ul>
                </li>
              </ul>
            </span>';
        }
    } else {
        //NOT LOGGED IN
//     
      $url = TAOH_LOGIN_URL;
        $rsvp_state = '<i class="la la-calendar-check mr-1" style="font-size: 20px;"></i> Registration status :<span style="display: inline-block;">
            <ul class="menu--main">
                <li class="">
                  <a class="btn btn-primary  btn-sm  rsvp-login" style="color:#ffffff;" href="'.$url.'"><i class="la la-calendar-check mr-1"></i><span>Login to Register</span></a>
                    
                </li>
            </ul>
        </span>';
    }
    ?>
    <div class="card card-item">
        <div class="card-body pr-0">
            <div class="d-flex card-title justify-content-between">
                <div>
                    <h3 class="fs-19 fw-semi-bold">Event Details</h3>
                </div>
                <div>
                    <?php echo taoh_calendar_widget($event_arr); ?>
                </div>
            </div>
            <div class="divider"><span></span></div>
            <br/>
            <ul class="generic-list-item pt-3 fs-15">
                <?php
                if ($rsvp_state && $hide_rsvp) {
                    echo '<li class="edit-status-js d-flex align-items-center flex-wrap">' . $rsvp_state . '</li>';
                }
                //echo 'event local ======================'.  $event_arr['locality'];


                ?>
                <li class="d-flex align-items-center flex-wrap" style="gap: 4px;"><i class="fas fa-hourglass"></i> <?php echo "<span>Event Status: </span>" . $event_state; ?></li>
                <li>
                    <i class="fas fa-calendar-days"></i> <?php echo " Starts: " . event_time_display($event_arr['utc_start_at'], $event_locality, $event_timezone); ?>
                </li>
                <li>
                    <i class="fas fa-calendar-days"></i> <?php echo " Ends: " . event_time_display($event_arr['utc_end_at'], $event_locality, $event_timezone); ?>
                </li>
                <?php
                /*if ( $event_arr['conttoken']['visibility'] ) $timezone = $event_arr['local_timezone'];
                if ( isset( $event_arr['conttoken']['visibility'] ) ){
                  if($event_arr['locality'])
                    $timezone = $event_arr['local_timezone'];
                  else
                    $timezone = taoh_user_timezone();
                  echo "<li><i class='fas fa-clock-o' ></i> Timezone: ".$timezone."</li>";
                } */
                $timezone = taoh_user_timezone();
                echo "<li><i class='fas fa-clock-o' ></i> Timezone: " . $timezone . "</li>";

                if ($event_type === 'in-person') {
                    $event_venue_loc = (!empty($events_data['map_link']) && filter_var($events_data['map_link'], FILTER_VALIDATE_URL)) ?
                        '<a href="' . $events_data['map_link'] . '" target="_blank" class="cursor-pointer text-underline">' . $events_data['venue'] . '</a>' : $events_data['venue'];

                    echo '<li><div><i class="fas fa-map-marker"></i> Venue : In-Person, '. $event_venue_loc .'</div></li>';

                } elseif ($event_type === 'hybrid') {
                    $lobby_link = TAOH_CURR_APP_URL . '/chat/id/events/' . $event_arr['eventtoken'];
                    $event_venue_loc = (!empty($events_data['map_link']) && filter_var($events_data['map_link'], FILTER_VALIDATE_URL)) ?
                        '<a href="' . $events_data['map_link'] . '" target="_blank" class="cursor-pointer text-underline">' . $events_data['venue'] . '</a>' : $events_data['venue'];

                    echo '<li><div class="d-flex align-items-center flex-wrap" style="gap: 4px;"><i class="fas fa-map-marker"></i>
                        Venue: Hybrid - ' . $event_venue_loc . ' or Virtual, 
                        <a href="' . $lobby_link . '" title="'.$lobby_link.'" target="_blank" class="cursor-pointer text-underline">
                        Join here </a> Or  </div></li>';

                } elseif ($event_type === 'virtual') {
                    $lobby_link = TAOH_CURR_APP_URL . '/chat/id/events/' . $event_arr['eventtoken'];

                    echo '<li><div class="d-flex align-items-center flex-wrap" style="gap: 4px;">
                    <i class="fas fa-map-marker"></i> 
                    Venue: Virtual, <a href="' . $lobby_link . '" title="'.$lobby_link.'" target="_blank" 
                    class="cursor-pointer text-underline">Join here</a></div></li>';
                }

//                if (isset($event_arr['conttoken']['ticket_types']) && !empty($event_arr['conttoken']['ticket_types'])) {
//                    $types = [];
//                    foreach ($event_arr['conttoken']['ticket_types'] as $links) {
//                        if(!empty($links['title'])) $types[] = trim($links['title']);
//                    }
//
//                    echo '<li><i class="fas fa-user"></i>Profile Types: ' . implode(', ', $types) . '</li>';
//                }

                if (isset($event_arr['conttoken']['link']) && count($event_arr['conttoken']['link']) && 0) {
                    echo "<li>Attendees: Upto <?php echo @$event_attendie_count; ?> Participants per room</li><li>Links: ";
                    foreach ($event_arr['conttoken']['link'] as $links) {
                        echo "<a href=\"" . $links['value'] . "\" target=_blank style=\"color: #999999;\">" . $links['label'] . "</a> ";
                    }
                    echo "</li>";
                }
                ?>
                </li>
            </ul>
        </div>

    </div>
    <?php
    return 1;
}

  function event_state_center_widget($event_arr,$hide_rsvp=1){
      $events_data = $event_arr['conttoken'] ?? [];

      $ticket_types = $events_data['ticket_types'];
      $tokentyp = $event_arr['mystatus']['rsvp_slug'];
      foreach ($ticket_types as $k => $v) {
          if (strtolower(trim($v['slug'])) == $tokentyp) {
              $tokenkey = $v['title'];
          }
      }

      $event_type = strtolower($events_data['event_type'] ?? 'virtual');

      $state = event_live_state( $event_arr[ 'utc_start_at' ], $event_arr[ 'utc_end_at' ], $event_arr['status'],  $event_arr['locality'] );
      //$state = 'before';
      if ( $state == 'live' ) $event_state = '<span class=" event_live_suc text-dark py-0"><span class="badge badge-md badge-success">Event Live<span></span>';
      else if ( $state == 'after' ) $event_state = '<span class="badge badge-md  badge-secondary ">Event <strong>Ended</strong></span>';
      else if ( $state == 'before' ) $event_state = '<span class=" event_live_war py-0"><span class="badge badge-md badge-warning">Event <strong>Not Live</strong><span></span>';
      else if ( $state == 'prelive' ) $event_state = '<span class=" event_live_war py-0"><span class="badge badge-md badge-warning">Event <strong>Not Live</strong></span></span>';
      else if ( $state == 'postlive' ) $event_state = '<span class="badge badge-md  badge-secondary text-dark py-0">Event<strong>Ended </strong></span>';
      else $event_state = '<span class="event_live_war py-0"><span class="badge badge-md badge-warning ">Not Live</span></span>';


      $event_start_at = $event_arr['local_start_at'];
      $event_end_at = $event_arr['local_end_at'];

      $event_locality = $event_arr['locality'];
      $event_timezone = $event_arr['local_timezone']; 
      $event_attendie_count = $event_arr['attendees'];

      $rsvp_state = '';
      //echo $state;exit();
      if( taoh_user_is_logged_in() ) {
        if ( ( $state == 'live' || $state == 'prelive' || $state == 'before' ) ){
             $rsvp_state = '
            <span class="d-flex align-items-center" style="gap: 6px;"> 
              <svg width="21" height="20" viewBox="0 0 21 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M6 0C6.82969 0 7.5 0.698242 7.5 1.5625V3.125H13.5V1.5625C13.5 0.698242 14.1703 0 15 0C15.8297 0 16.5 0.698242 16.5 1.5625V3.125H18.75C19.9922 3.125 21 4.1748 21 5.46875V7.8125H0V5.46875C0 4.1748 1.00781 3.125 2.25 3.125H4.5V1.5625C4.5 0.698242 5.17031 0 6 0ZM0 9.375H21V22.6562C21 23.9502 19.9922 25 18.75 25H2.25C1.00781 25 0 23.9502 0 22.6562V9.375ZM15.4219 14.8926C15.8625 14.4336 15.8625 13.6914 15.4219 13.2373C14.9812 12.7832 14.2688 12.7783 13.8328 13.2373L9.37969 17.876L7.17656 15.5811C6.73594 15.1221 6.02344 15.1221 5.5875 15.5811C5.15156 16.04 5.14687 16.7822 5.5875 17.2363L8.5875 20.3613C9.02812 20.8203 9.74063 20.8203 10.1766 20.3613L15.4219 14.8926Z" fill="#2557A7"/>
              </svg>
              <span class="text-dark text-nowrap" style="color: #000000; font-size: 16px;">Registration status :</span>
            </span>  
            <span style="display: inline-block;">
              <ul class="menu--main">
              <li data-metrics="rsvplink" class="btn btn-sm btn-success rsvp_btn click_metrics py-0 mb-0 text-nowrap d-flex align-items-center">
                  <i class="la la-calendar-check mr-1" style="font-size: 20px;"></i>                  
                  <span> Select ticket</span>
                  <ul class="sub-menu">
                  </ul>
              </li>
              </ul>
            </span>';
        }
      } else {
        //NOT LOGGED IN
        $url = TAOH_LOGIN_URL;
       
      $rsvp_state = '
            <span class="d-flex aligm-items-center" style="gap: 6px;">
              <svg width="21" height="25" viewBox="0 0 21 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M6 0C6.82969 0 7.5 0.698242 7.5 1.5625V3.125H13.5V1.5625C13.5 0.698242 14.1703 0 15 0C15.8297 0 16.5 0.698242 16.5 1.5625V3.125H18.75C19.9922 3.125 21 4.1748 21 5.46875V7.8125H0V5.46875C0 4.1748 1.00781 3.125 2.25 3.125H4.5V1.5625C4.5 0.698242 5.17031 0 6 0ZM0 9.375H21V22.6562C21 23.9502 19.9922 25 18.75 25H2.25C1.00781 25 0 23.9502 0 22.6562V9.375ZM15.4219 14.8926C15.8625 14.4336 15.8625 13.6914 15.4219 13.2373C14.9812 12.7832 14.2688 12.7783 13.8328 13.2373L9.37969 17.876L7.17656 15.5811C6.73594 15.1221 6.02344 15.1221 5.5875 15.5811C5.15156 16.04 5.14687 16.7822 5.5875 17.2363L8.5875 20.3613C9.02812 20.8203 9.74063 20.8203 10.1766 20.3613L15.4219 14.8926Z" fill="#2557A7"></path>
              </svg>
              <span class="text-nowrap" style="color: #000000; font-size: 16px;">Registration status :</span> 
            </span>
            <span style="display: inline-block;">
              <ul class="menu--main">
              <li class="">
                      <a class="btn btn-primary  btn-sm  rsvp-login d-flex align-items-center" style="gap: 6px;color:#ffffff;" href="'.$url.'"><i class="la la-calendar-check mr-1"></i><span class="text-nowrap">Login to Register</span></a>
              </li>
              </ul>
            </span>';
      }
      //echo $event_state;
      //echo $rsvp_state;
      //print_r($event_arr);
    ?>
    <div class=" event_details_center">
      <div class="card-body px-0">
        
        <ul class="generic-list-item d-flex flex-wrap pt-2 fs-15 w-100" style="gap: 12px;">
            <?php
            if ($rsvp_state && $hide_rsvp) {
                echo '<li class="edit-status-js mr-3 d-flex flex-wrap align-items-center" style="gap: 6px;">' . $rsvp_state . '</li>';
            }
            ?>
         
            <li class="d-flex flex-wrap align-items-center" style="gap: 6px;">
              <div class="d-flex align-items-center" style="gap: 6px;">
                <svg width="18" height="18" viewBox="0 0 18 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M0 1.5625C0 0.698242 0.670138 0 1.49961 0H2.99922H14.9961H16.4957C17.3252 0 17.9953 0.698242 17.9953 1.5625C17.9953 2.42676 17.3252 3.125 16.4957 3.125V3.66211C16.4957 5.73242 15.7037 7.71973 14.2978 9.18457L11.1205 12.5L14.3025 15.8154C15.7084 17.2803 16.5004 19.2676 16.5004 21.3379V21.875C17.3299 21.875 18 22.5732 18 23.4375C18 24.3018 17.3299 25 16.5004 25H14.9961H2.99922H1.49961C0.670138 25 0 24.3018 0 23.4375C0 22.5732 0.670138 21.875 1.49961 21.875V21.3379C1.49961 19.2676 2.29159 17.2803 3.69747 15.8154L6.87477 12.5L3.69747 9.18457C2.29159 7.71973 1.49961 5.73242 1.49961 3.66211V3.125C0.670138 3.125 0 2.42676 0 1.5625ZM4.49883 3.125V3.66211C4.49883 4.90723 4.97214 6.09863 5.81567 6.97754L8.99766 10.2881L12.1796 6.97266C13.0232 6.09375 13.4965 4.90234 13.4965 3.65723V3.125H4.49883ZM4.49883 21.875H13.4965V21.3379C13.4965 20.0928 13.0232 18.9014 12.1796 18.0225L8.99766 14.7119L5.81567 18.0273C4.97214 18.9062 4.49883 20.0977 4.49883 21.3428V21.8799V21.875Z" fill="#2557A7"/>
                </svg>
                <span class="text-nowrap" style="color: #000000; font-size: 16px;">Event Status :</span> 
              </div>
              <?php echo $event_state; ?>
            </li>


          <?php
          if (isset($events_data['link']) && count($events_data['link']) && 0) {
              echo "<li>Attendees: Upto <?php echo @$event_attendie_count; ?> Participants per room</li>";

              echo "<li>Links: ";
              foreach ($events_data['link'] as $links) {
                  echo "<a href=\"" . $links['value'] . "\" target=_blank style=\"color: #999999;\">" . $links['label'] . "</a>";
              }
              echo "</li>";
          }
          ?>
        
          </li>
        </ul>


          <div class="d-flex flex-column flex-xl-row pt-3 pb-3" style="border-bottom: 2px solid #F3F3F3; gap: 12px;">
              <div class="events-border pr-2">
                  <div class="d-flex align-items-center text-black mb-2" style="gap: 8px;">
                      <svg width="21" height="25" viewBox="0 0 21 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M6 0C6.82969 0 7.5 0.698242 7.5 1.5625V3.125H13.5V1.5625C13.5 0.698242 14.1703 0 15 0C15.8297 0 16.5 0.698242 16.5 1.5625V3.125H18.75C19.9922 3.125 21 4.1748 21 5.46875V7.8125H0V5.46875C0 4.1748 1.00781 3.125 2.25 3.125H4.5V1.5625C4.5 0.698242 5.17031 0 6 0ZM0 9.375H21V22.6562C21 23.9502 19.9922 25 18.75 25H2.25C1.00781 25 0 23.9502 0 22.6562V9.375ZM15.4219 14.8926C15.8625 14.4336 15.8625 13.6914 15.4219 13.2373C14.9812 12.7832 14.2688 12.7783 13.8328 13.2373L9.37969 17.876L7.17656 15.5811C6.73594 15.1221 6.02344 15.1221 5.5875 15.5811C5.15156 16.04 5.14687 16.7822 5.5875 17.2363L8.5875 20.3613C9.02812 20.8203 9.74063 20.8203 10.1766 20.3613L15.4219 14.8926Z" fill="#2557A7"/>
                      </svg>
                      <div>Event Starts at</div>
                  </div>

                  <div class="text-nowrap"><?= event_time_display($event_arr['utc_start_at'], $event_locality, $event_timezone); ?></div>
              </div>

              <div class="events-border pr-2">
                  <div class="d-flex align-items-center text-black mb-2" style="gap: 8px;">
                      <svg width="21" height="28" viewBox="0 0 21 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M6 0C6.82969 0 7.5 0.772961 7.5 1.7297V3.45941H13.5V1.7297C13.5 0.772961 14.1703 0 15 0C15.8297 0 16.5 0.772961 16.5 1.7297V3.45941H18.75C19.9922 3.45941 21 4.62155 21 6.05396V8.64851H0V6.05396C0 4.62155 1.00781 3.45941 2.25 3.45941H4.5V1.7297C4.5 0.772961 5.17031 0 6 0ZM0 10.3782H21V25.0807C21 26.5131 19.9922 27.6752 18.75 27.6752H2.25C1.00781 27.6752 0 26.5131 0 25.0807V10.3782ZM14.625 20.324C15.2484 20.324 15.75 19.7456 15.75 19.0267C15.75 18.3078 15.2484 17.7295 14.625 17.7295H6.375C5.75156 17.7295 5.25 18.3078 5.25 19.0267C5.25 19.7456 5.75156 20.324 6.375 20.324H14.625Z" fill="#2557A7"/>
                      </svg>
                      <div>Event Ends at</div>
                  </div>

                  <div class="text-nowrap"><?= event_time_display($event_arr['utc_end_at'],$event_locality,$event_timezone); ?></div>
              </div>

              <div>
                  <?php
                  echo '<div class="d-flex align-items-center text-black mb-2" style="gap: 8px;">';
                  if ($event_type === 'in-person') {
                      echo '<svg width="21" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14.8079 3.56198C15.2836 3.56198 15.7399 3.37434 16.0762 3.04034C16.4126 2.70634 16.6016 2.25334 16.6016 1.78099C16.6016 1.30864 16.4126 0.85564 16.0762 0.52164C15.7399 0.187639 15.2836 0 14.8079 0C14.3322 0 13.876 0.187639 13.5396 0.52164C13.2033 0.85564 13.0143 1.30864 13.0143 1.78099C13.0143 2.25334 13.2033 2.70634 13.5396 3.04034C13.876 3.37434 14.3322 3.56198 14.8079 3.56198ZM11.6579 7.43934C11.6952 7.4245 11.7289 7.40966 11.7662 7.39482L11.1347 9.75091C10.9255 10.5338 11.131 11.3686 11.684 11.966L14.3259 14.8267L15.148 18.0956C15.3087 18.7301 15.9589 19.1197 16.5978 18.9601C17.2368 18.8006 17.6292 18.155 17.4685 17.5205L16.6091 14.1032C16.5381 13.8138 16.3923 13.5504 16.1905 13.3314L14.3408 11.3278L15.062 8.89752L15.4208 9.75091C15.5852 10.1442 15.8879 10.467 16.2727 10.66L17.2705 11.1534C17.8609 11.4466 18.5783 11.2091 18.8735 10.6229C19.1687 10.0366 18.9296 9.32422 18.3392 9.0311L17.5358 8.63409L16.964 7.26866C16.3213 5.74369 14.8192 4.7493 13.1526 4.7493C12.3006 4.7493 11.4598 4.9274 10.6826 5.26876L10.3836 5.39862C9.15423 5.94034 8.21257 6.97183 7.79032 8.23707L7.69316 8.52649C7.48391 9.14983 7.82395 9.82141 8.44799 10.0292C9.07202 10.237 9.75211 9.89933 9.96137 9.2797L10.0585 8.99028C10.2715 8.35581 10.7424 7.84377 11.3552 7.57291L11.6541 7.44305L11.6579 7.43934ZM10.5368 12.4521L9.60264 14.7674L7.38301 16.9713C6.91592 17.4351 6.91592 18.1884 7.38301 18.6522C7.85011 19.1159 8.60867 19.1159 9.07576 18.6522L11.3813 16.3628C11.5532 16.1922 11.6878 15.9881 11.7774 15.7655L12.3193 14.4223L10.7984 12.7749C10.705 12.6747 10.619 12.5671 10.5368 12.4558V12.4521ZM8.23126 10.1702C7.94353 10.0069 7.58106 10.1034 7.41291 10.3891L6.21715 12.4447L4.18435 11.2796C3.61263 10.9531 2.88023 11.146 2.55139 11.7137L0.159872 15.8285C-0.168962 16.3962 0.025349 17.1235 0.597072 17.45L2.66723 18.6373C3.23896 18.9638 3.97136 18.7709 4.30019 18.2032L6.69171 14.0884C6.74777 13.9919 6.78887 13.8954 6.81503 13.7915L8.45172 10.9828C8.61614 10.6971 8.51899 10.3372 8.23126 10.1702Z" fill="#2557A7"/>
                            </svg>';
                  } elseif ($event_type === 'hybrid') {
                      echo '<svg width="30" height="30" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.48083 0.251601C6.84598 -0.0838669 7.40707 -0.0838669 7.76926 0.251601L13.9442 5.95158C14.1431 6.13564 14.25 6.39095 14.25 6.64923H9.97503C9.408 6.64923 8.89738 6.89861 8.55004 7.29345V6.17424C8.55004 5.91299 8.33629 5.69924 8.07504 5.69924H6.17505C5.9138 5.69924 5.70005 5.91299 5.70005 6.17424V8.07423C5.70005 8.33548 5.9138 8.54923 6.17505 8.54923H8.07504V12.3492H3.32506C2.53834 12.3492 1.90006 11.7109 1.90006 10.9242V7.59923H0.950065C0.558192 7.59923 0.20788 7.35876 0.0653809 6.99658C-0.0771186 6.63439 0.0178811 6.21877 0.305849 5.95158L6.48083 0.251601ZM10.45 9.02422V13.2992H16.15V9.02422H10.45ZM9.02504 8.54923C9.02504 8.02376 9.44957 7.59923 9.97503 7.59923H16.625C17.1505 7.59923 17.575 8.02376 17.575 8.54923V13.2992H18.525C18.7863 13.2992 19 13.513 19 13.7742C19 14.5609 18.3617 15.1992 17.575 15.1992H16.15H10.45H9.02504C8.23832 15.1992 7.60004 14.5609 7.60004 13.7742C7.60004 13.513 7.81379 13.2992 8.07504 13.2992H9.02504V8.54923Z" fill="#2557A7"/>
                            </svg>';
                  } elseif ($event_type === 'virtual') {
                      echo '<svg width="21" height="13" viewBox="0 0 20 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0 2.16667C0 0.971615 0.996528 0 2.22222 0H11.1111C12.3368 0 13.3333 0.971615 13.3333 2.16667V10.8333C13.3333 12.0284 12.3368 13 11.1111 13H2.22222C0.996528 13 0 12.0284 0 10.8333V2.16667ZM19.4132 1.21198C19.7743 1.40156 20 1.76719 20 2.16667V10.8333C20 11.2328 19.7743 11.5984 19.4132 11.788C19.0521 11.9776 18.6146 11.9573 18.2708 11.7339L14.9375 9.56719L14.4444 9.24557V8.66667V4.33333V3.75443L14.9375 3.43281L18.2708 1.26615C18.6111 1.04609 19.0486 1.0224 19.4132 1.21198Z" fill="#2557A7"></path>
                            </svg>';
                  }
                  echo '<div>' . (!empty($event_type) ? ucfirst($event_type) : 'Virtual') . ' Event Type</div>';
                  echo '</div>';

//                  echo '<div>' . (!empty($event_type) ? ucfirst($event_type) : 'Virtual') . '</div>';

                  echo '<div>';
                  if ($event_type === 'in-person') {
                      $event_venue_loc = (!empty($events_data['map_link']) && filter_var($events_data['map_link'], FILTER_VALIDATE_URL)) ?
                          '<a href="' . $events_data['map_link'] . '" target="_blank" class="cursor-pointer text-underline">' . $events_data['venue'] . '</a>' : $events_data['venue'];

                      echo '<p style="line-height: 1.3;"><span class="theme-blue-clr">Venue: In-Person, <span>' . $event_venue_loc . '</span></span></p>';

                  } elseif ($event_type === 'hybrid') {
                      $lobby_link = TAOH_CURR_APP_URL . '/chat/id/events/' . $event_arr['eventtoken'];
                      $event_venue_loc = (!empty($events_data['map_link']) && filter_var($events_data['map_link'], FILTER_VALIDATE_URL)) ?
                          '<a href="' . $events_data['map_link'] . '" target="_blank" class="cursor-pointer">' . $events_data['venue'] . '</a>' : $events_data['venue'];

                      echo '<p style="line-height: 1.3;"><span class="theme-blue-clr">
                      Venue: Hybrid - <span>' . $event_venue_loc . '</span> Or 
                      <span>Virtual, <a href="' . $lobby_link . '" title="'.$lobby_link.'" target="_blank" class="cursor-pointer text-underline">
                       Join here</a></span></span></p>';

                  } elseif ($event_type === 'virtual') {
                      $lobby_link = TAOH_CURR_APP_URL . '/chat/id/events/' . $event_arr['eventtoken'];

                      echo '<p style="line-height: 1.3;"><span class="theme-blue-clr">
                      Venue: Virtual, <a href="' . $lobby_link . '" title="'.$lobby_link.'" target="_blank" class="cursor-pointer text-underline">
                      Join here</a></span></p>';
                  }
                  echo '</div>'

                  ?>
              </div>

              <?php
              /*if (isset($events_data['ticket_types']) && !empty($events_data['ticket_types'])) {
                  $types = [];
                  foreach ($events_data['ticket_types'] as $links) {
                      if(!empty($links['title'])) $types[] = trim($links['title']);
                  }

                  echo '<div class="col-12 col-sm-6 col-xl-3 px-2">
                            <div class="d-flex align-items-center" style="gap: 4px;">
                                <i class="fas fa-user" style="color: #2557A7; font-size: 24px;"></i>
                                <span style="color: #000000; font-size: 16px;">Profile Types</span>
                            </div>' . implode(', ', $types) . '</div>';
              }*/
              ?>

          </div>

      </div>

    </div><!-- end card-item -->
      <?php
      return 1;
  }


function event_state_center_widget2($event_arr, $hide_rsvp = 1)
{
    $user_timezone = taoh_user_timezone();
    if (empty($user_timezone)) $user_timezone = 'America/New_York';

    $state = event_live_state($event_arr['utc_start_at'], $event_arr['utc_end_at'], $event_arr['status'], $event_arr['locality']);

    if ($state == 'live') $event_state = '<span class="badge badge-md badge-success event_live_suc text-dark py-0">Event Live</span>';
    else if ($state == 'after') $event_state = '<span class="badge badge-md  badge-secondary ">Event <strong>Ended</strong></span>';
    else if ($state == 'before') $event_state = '<span class="badge badge-md badge-warning  event_live_war py-0">Event <strong>Not Live</strong></span>';
    else if ($state == 'prelive') $event_state = '<span class="badge badge-md badge-warning event_live_war py-0">Event <strong>Not Live</strong></span>';
    else if ($state == 'postlive') $event_state = '<span class="badge badge-md  badge-secondary text-dark py-0">Event<strong>Ended </strong></span>';
    else $event_state = '<span class="badge badge-md  badge-warning event_live_war py-0">Not Live</span>';

    $event_start_at = $event_arr['local_start_at'];
    $event_end_at = $event_arr['local_end_at'];

    $event_locality = $event_arr['locality'];
    $event_timezone = $event_arr['local_timezone'];

    $event_attendie_count = $event_arr['attendees'];

    $rsvp_state = '';
    if (taoh_user_is_logged_in()) {
        if (($state == 'live' || $state == 'prelive' || $state == 'before')) {
            $rsvp_state = '<i class="la la-calendar-check mr-1" style="font-size: 24px; color: #2557A7;"></i>
                <span class="text-dark"> <span style="color: #000000; font-size: 16px;">Registration status :</span></span>  
                <span style="display: inline-block;">
                  <ul class="menu--main">
                      <li data-metrics="rsvplink" class="btn btn-sm btn-success rsvp_btn click_metrics py-0 ml-2 mb-0">
                          <i class="la la-calendar-check mr-1" style="font-size: 20px;"></i><span> Select ticket</span>
                          <ul class="sub-menu"> </ul>
                      </li>
                  </ul>
                </span>';
        }
    } else {
        //NOT LOGGED IN
        $url = TAOH_LOGIN_URL ;
        $rsvp_state = '<i class="la la-calendar-check mr-1" style="font-size: 20px;"></i> <span style="color: #000000; font-size: 16px;">Registration status :</span>  
            <span style="display: inline-block;">
                <ul class="menu--main">
                    <li data-metrics="rsvplink" class="btn btn-success  btn-sm rsvp_btn click_metrics"><i class="la la-calendar-check mr-1"></i><span> Select ticket </span>
                        <ul class="sub-menu"> </ul>
                    </li>
                </ul>
            </span>';
    }

    ?>

    <div class="event_details_center">
        <div class="card-body">
            <ul class="generic-list-item pt-3 fs-15">
                <?php
                if ($rsvp_state && $hide_rsvp) {
                    echo '<li class="edit-status-js row px-2 d-flex align-items-center">' . $rsvp_state . '</li>';
                }
                ?>

                <li class="row px-2 d-flex align-items-center mt-3" style="">
                    <svg width="18" height="18" viewBox="0 0 18 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 1.5625C0 0.698242 0.670138 0 1.49961 0H2.99922H14.9961H16.4957C17.3252 0 17.9953 0.698242 17.9953 1.5625C17.9953 2.42676 17.3252 3.125 16.4957 3.125V3.66211C16.4957 5.73242 15.7037 7.71973 14.2978 9.18457L11.1205 12.5L14.3025 15.8154C15.7084 17.2803 16.5004 19.2676 16.5004 21.3379V21.875C17.3299 21.875 18 22.5732 18 23.4375C18 24.3018 17.3299 25 16.5004 25H14.9961H2.99922H1.49961C0.670138 25 0 24.3018 0 23.4375C0 22.5732 0.670138 21.875 1.49961 21.875V21.3379C1.49961 19.2676 2.29159 17.2803 3.69747 15.8154L6.87477 12.5L3.69747 9.18457C2.29159 7.71973 1.49961 5.73242 1.49961 3.66211V3.125C0.670138 3.125 0 2.42676 0 1.5625ZM4.49883 3.125V3.66211C4.49883 4.90723 4.97214 6.09863 5.81567 6.97754L8.99766 10.2881L12.1796 6.97266C13.0232 6.09375 13.4965 4.90234 13.4965 3.65723V3.125H4.49883ZM4.49883 21.875H13.4965V21.3379C13.4965 20.0928 13.0232 18.9014 12.1796 18.0225L8.99766 14.7119L5.81567 18.0273C4.97214 18.9062 4.49883 20.0977 4.49883 21.3428V21.8799V21.875Z"
                              fill="#2557A7"/>
                    </svg>
                    <span style="color: #000000; font-size: 16px;">Event Status : <?php echo $event_state; ?></span>
                </li>
                <div class="row pb-3 mt-4 mb-3" style="border-bottom : 2px solid #F3F3F3;">
                    <li class="col-12 col-sm-6  col-xl-3  events-border px-2" style="">
                        <div class="d-flex align-items-center" style="gap: 4px;">
                            <svg width="21" height="25" viewBox="0 0 21 25" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 0C6.82969 0 7.5 0.698242 7.5 1.5625V3.125H13.5V1.5625C13.5 0.698242 14.1703 0 15 0C15.8297 0 16.5 0.698242 16.5 1.5625V3.125H18.75C19.9922 3.125 21 4.1748 21 5.46875V7.8125H0V5.46875C0 4.1748 1.00781 3.125 2.25 3.125H4.5V1.5625C4.5 0.698242 5.17031 0 6 0ZM0 9.375H21V22.6562C21 23.9502 19.9922 25 18.75 25H2.25C1.00781 25 0 23.9502 0 22.6562V9.375ZM15.4219 14.8926C15.8625 14.4336 15.8625 13.6914 15.4219 13.2373C14.9812 12.7832 14.2688 12.7783 13.8328 13.2373L9.37969 17.876L7.17656 15.5811C6.73594 15.1221 6.02344 15.1221 5.5875 15.5811C5.15156 16.04 5.14687 16.7822 5.5875 17.2363L8.5875 20.3613C9.02812 20.8203 9.74063 20.8203 10.1766 20.3613L15.4219 14.8926Z"
                                      fill="#2557A7"/>
                            </svg>
                            <span style="color: #000000; font-size: 16px;">Event Starts at</span>
                        </div>

                        <span>
                              <?php echo event_time_display($event_arr['utc_start_at'], $event_locality, $event_timezone); ?>
                            </span>
                    </li>
                    <li class="col-12 col-sm-6  col-xl-3  events-border px-2" style="">
                        <div class="d-flex align-items-center" style="gap: 4px;">
                            <svg width="21" height="28" viewBox="0 0 21 28" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 0C6.82969 0 7.5 0.772961 7.5 1.7297V3.45941H13.5V1.7297C13.5 0.772961 14.1703 0 15 0C15.8297 0 16.5 0.772961 16.5 1.7297V3.45941H18.75C19.9922 3.45941 21 4.62155 21 6.05396V8.64851H0V6.05396C0 4.62155 1.00781 3.45941 2.25 3.45941H4.5V1.7297C4.5 0.772961 5.17031 0 6 0ZM0 10.3782H21V25.0807C21 26.5131 19.9922 27.6752 18.75 27.6752H2.25C1.00781 27.6752 0 26.5131 0 25.0807V10.3782ZM14.625 20.324C15.2484 20.324 15.75 19.7456 15.75 19.0267C15.75 18.3078 15.2484 17.7295 14.625 17.7295H6.375C5.75156 17.7295 5.25 18.3078 5.25 19.0267C5.25 19.7456 5.75156 20.324 6.375 20.324H14.625Z"
                                      fill="#2557A7"/>
                            </svg>
                            <span style="color: #000000; font-size: 16px;">Event Ends at</span>
                        </div>

                        <span>
                              <?php echo event_time_display($event_arr['utc_end_at'], $event_locality, $event_timezone); ?>
                            </span>
                    </li>
                    <li class="col-12 col-sm-6  col-xl-3  events-border px-2" style="">
                        <div class="d-flex align-items-center" style="gap: 4px;">
                            <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.5 25C15.8152 25 18.9946 23.683 21.3388 21.3388C23.683 18.9946 25 15.8152 25 12.5C25 9.18479 23.683 6.00537 21.3388 3.66117C18.9946 1.31696 15.8152 0 12.5 0C9.18479 0 6.00537 1.31696 3.66117 3.66117C1.31696 6.00537 0 9.18479 0 12.5C0 15.8152 1.31696 18.9946 3.66117 21.3388C6.00537 23.683 9.18479 25 12.5 25ZM10.1855 17.4463L8.28125 16.7871C7.96387 16.6797 7.61719 16.6748 7.2998 16.7725L6.55273 17.0117C5.64941 17.2998 4.67285 16.8945 4.2334 16.0596L4.07227 15.7568C3.55469 14.7754 3.95996 13.5596 4.96094 13.0859L6.68457 12.2656C6.79687 12.2119 6.89941 12.1289 6.97266 12.0312L7.23145 11.6895C7.58301 11.2207 8.13965 10.9424 8.72559 10.9424C9.31152 10.9424 9.86816 11.2207 10.2197 11.6895L10.4443 11.9873C10.542 12.1143 10.6836 12.207 10.8398 12.2363C11.2207 12.3145 11.6064 12.1631 11.8359 11.8506L12.3438 11.1572C12.4414 11.0205 12.6025 10.9424 12.7686 10.9424C12.9834 10.9424 13.1787 11.0742 13.2568 11.2744L13.75 12.5391C13.8867 12.8906 14.0771 13.2227 14.3115 13.5254L15.1855 14.6387C15.4688 15 15.625 15.4492 15.625 15.9082C15.625 16.3672 15.4688 16.8164 15.1855 17.1777L14.5996 17.9297C14.1943 18.4473 13.5742 18.75 12.9199 18.75C12.5098 18.75 12.1094 18.6328 11.7627 18.4082L10.5225 17.6074C10.415 17.5391 10.3027 17.4854 10.1855 17.4414V17.4463ZM13.3691 6.95801L14.4531 8.04199C14.9463 8.53516 14.5947 9.375 13.9014 9.375H12.4414C12.168 9.375 11.8994 9.31641 11.6504 9.20898L9.56055 8.28125C8.8623 7.97363 8.97949 6.94824 9.72656 6.80176L11.6064 6.42578C12.2461 6.29883 12.9102 6.49902 13.3691 6.95801ZM12.1094 21.0938C12.1094 20.6641 12.4609 20.3125 12.8906 20.3125H13.6719C14.1016 20.3125 14.4531 20.6641 14.4531 21.0938C14.4531 21.5234 14.1016 21.875 13.6719 21.875H12.8906C12.4609 21.875 12.1094 21.5234 12.1094 21.0938ZM21.0547 14.5947L21.4453 15.7666C21.582 16.1768 21.3623 16.6162 20.9521 16.7529C20.542 16.8896 20.1025 16.6699 19.9658 16.2598L19.5752 15.0879C19.4385 14.6777 19.6582 14.2383 20.0684 14.1016C20.4785 13.9648 20.918 14.1846 21.0547 14.5947ZM20.083 18.5205L18.5205 20.083C18.2178 20.3857 17.7197 20.3857 17.417 20.083C17.1143 19.7803 17.1143 19.2822 17.417 18.9795L18.9795 17.417C19.2822 17.1143 19.7803 17.1143 20.083 17.417C20.3857 17.7197 20.3857 18.2178 20.083 18.5205Z"
                                      fill="#2557A7"/>
                            </svg>
                            <span style="color: #000000; font-size: 16px;">Time Zone</span>
                        </div>
                        <span><?php echo $user_timezone; ?></span>
                    </li>

                    <?php
                    if (isset($event_arr['conttoken']['ticket_types']) && !empty($event_arr['conttoken']['ticket_types'])) {
                        $types = [];
                        foreach ($event_arr['conttoken']['ticket_types'] as $links) {
                            if(!empty($links['title'])) $types[] = trim($links['title']);
                        }

                        echo '<li class="col-12 col-sm-6 col-xl-3 px-2">
                            <div class="d-flex align-items-center" style="gap: 4px;">
                                <i class="fas fa-user" style="color: #2557A7;font-size: 24px;"></i>
                                <span style="color: #000000;font-size: 16px;">Profile Types</span>
                            </div>' . implode(', ', $types) . '</li>';
                    }
                    ?>
                </div>

                <?php
                if (isset($event_arr['conttoken']['link']) && count($event_arr['conttoken']['link']) && 0) {
                    echo "<li>Attendees: Upto <?php echo @$event_attendie_count; ?> Participants per room</li><li>Links: ";
                    foreach ($event_arr['conttoken']['link'] as $links) {
                        echo "<a href=\"" . $links['value'] . "\" target=_blank style=\"color: #999999;\">" . $links['label'] . "</a> ";
                    }
                    echo "</li>";
                }
                ?>

                </li>
            </ul>
        </div>

    </div>
    <?php
}


function changeDateTimezone($date, $to, $from = 'America/New_York', $targetFormat = "Y-m-d H:i:s", $dstcheck = true)
{
    if (empty($to)) $to = 'America/New_York';

    $theTime = strtotime($date);
    $tz = new DateTimeZone($to);
    $transition = $tz->getTransitions($theTime, $theTime);

    $offset = $transition[0]['offset'];
    $isdst = $transition[0]['isdst'];

    $date = new DateTime($date, new DateTimeZone($from));

    if ($isdst && $dstcheck && strcasecmp($from, $to) !== 0) {
        $interval = ($offset < 0) ? 'PT' . abs($offset) . 'S' : '-PT' . abs($offset) . 'S';
        $date->modify($interval);
    } else {
        $date->setTimezone($tz);
    }

    return $date->format($targetFormat);
}

if (!function_exists('field_locations')) {
function field_locations($coordinates="", $location="", $geohash="", $js="") {
  $str ='<select id="locationSelect" placeholder="Search with Location" onchange="validate2(2)" autocomplete="off" class="form-control mb-lg-0 required" name="coordinates">';
  if($coordinates && $location) {
    $str .='<option value="'.$coordinates.'">'.$location.'</option>';
  }
  $str .='</select>';
  $str .='<input id="coordinateLocation" type="hidden" name="full_location" value="'.$location.'">';
  $str .='<input id="geohash" type="hidden" name="geohash" value="'.$geohash.'">';
  if($js) {
    $str .='<script>locationSelect();</script>';
  } else {
    $str .='<script>locationSelect();</script>';
  }
  return $str;
}
}

/**
 * URL Path Manipulation Functions
 * Moved from chat.php for reusability
 */

if (!function_exists('removePathSegment')) {
/**
 * Remove a path segment from URL by index
 * @param string $url The URL to modify
 * @param int $index The index of the segment to remove
 * @return string Modified URL
 */
function removePathSegment($url, $index) {
    $parts = parse_url($url);
    $pathSegments = explode('/', trim($parts['path'], '/'));

    if (isset($pathSegments[$index])) {
        unset($pathSegments[$index]);
    }

    $newPath = '/' . implode('/', $pathSegments);
    $newUrl = $parts['scheme'] . '://' . $parts['host'];

    if (isset($parts['port'])) {
        $newUrl .= ':' . $parts['port'];
    }
    $newUrl .= $newPath;

    if (!empty($parts['query'])) {
        $newUrl .= '?' . $parts['query'];
    }

    return $newUrl;
}
}

if (!function_exists('addPathSegment')) {
/**
 * Add a path segment to URL before specified parameter
 * @param string $url The URL to modify
 * @param string $oldparam The parameter before which to add
 * @param string $newParam The new parameter to add
 * @return string Modified URL
 */
function addPathSegment($url, $oldparam, $newParam) {
    $parsed = parse_url($url);
    $path = rtrim($parsed['path'], '/');
    $segments = explode('/', $path);
    $found = false;

    foreach ($segments as $i => $seg) {
        if ($seg === $oldparam) {
            array_splice($segments, $i, 0, $newParam);
            $found = true;
            break;
        }
    }

    if (!$found) {
        $segments[] = $newParam;
        $segments[] = 'stlo';
    }

    $newPath = implode('/', $segments);
    $newUrl = $parsed['scheme'] . "://" . $parsed['host'] . $newPath;

    if (isset($parsed['query'])) {
        $newUrl .= "?" . $parsed['query'];
    }

    return $newUrl;
}
}

if (!function_exists('find_title_slug')) {
/**
 * Find a field value in ticket types by slug
 * @param string $slug The slug to search for
 * @param array $ticket_types Array of ticket types
 * @param string $field The field to return (default: 'slug')
 * @return string|null The field value or null
 */
function find_title_slug($slug, $ticket_types, $field = 'slug') {
    foreach ($ticket_types as $element) {
        if ($slug == $element['slug']) {
            return string_to_id($element[$field]);
        }
    }
    return null;
}
}

if (!function_exists('edit_prefill')) {
/**
 * Get prefill value for form field
 * @param string $tab The tab identifier
 * @param string $field The field name
 * @param object|null $response The response object
 * @param array $ticket_types Array of ticket types
 * @return string The prefill value
 */
function edit_prefill($tab, $field, $response, $ticket_types) {
    $return = "";
    if ($response) {
        $return = $response->$field;
    }
    return $return;
}
}

if (!function_exists('string_to_id')) {
/**
 * Convert a string to a valid ID format
 * @param string $string The string to convert
 * @return string Lowercase string with spaces removed
 */
function string_to_id($string) {
    return strtolower(preg_replace('/\s+/', '', $string));
}
}
?>
