<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/raj/assets/icons/icons.php';

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
              ' . icon('calendar-check', '#2557A7', 21) . '
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
              '.icon('calendar-check', '#2557A7', 21).'
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
                <?= icon('hourglass', '#2557A7', 18) ?>
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
                      <?= icon('calendar-check', '#2557A7', 21) ?>
                      <div>Event Starts at</div>
                  </div>

                  <div class="text-nowrap"><?= event_time_display($event_arr['utc_start_at'], $event_locality, $event_timezone); ?></div>
              </div>

              <div class="events-border pr-2">
                  <div class="d-flex align-items-center text-black mb-2" style="gap: 8px;">
                      <?= icon('calendar-days', '#2557A7', 21) ?>
                      <div>Event Ends at</div>
                  </div>

                  <div class="text-nowrap"><?= event_time_display($event_arr['utc_end_at'],$event_locality,$event_timezone); ?></div>
              </div>

              <div>
                  <?php
                  echo '<div class="d-flex align-items-center text-black mb-2" style="gap: 8px;">';
                  if ($event_type === 'in-person') {
                      echo icon('location', '#2557A7', 21);
                  } elseif ($event_type === 'hybrid') {
                      echo icon('laptop', '#2557A7', 21);
                  } elseif ($event_type === 'virtual') {
                      echo icon('video', '#2557A7', 21);
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
                    <?= icon('hourglass', '#2557A7', 18) ?>
                    <span style="color: #000000; font-size: 16px;">Event Status : <?php echo $event_state; ?></span>
                </li>
                <div class="row pb-3 mt-4 mb-3" style="border-bottom : 2px solid #F3F3F3;">
                    <li class="col-12 col-sm-6  col-xl-3  events-border px-2" style="">
                        <div class="d-flex align-items-center" style="gap: 4px;">
                            <?= icon('calendar-check', '#2557A7', 21) ?>
                            <span style="color: #000000; font-size: 16px;">Event Starts at</span>
                        </div>

                        <span>
                              <?php echo event_time_display($event_arr['utc_start_at'], $event_locality, $event_timezone); ?>
                            </span>
                    </li>
                    <li class="col-12 col-sm-6  col-xl-3  events-border px-2" style="">
                        <div class="d-flex align-items-center" style="gap: 4px;">
                            <?= icon('calendar-days', '#2557A7', 21) ?>
                            <span style="color: #000000; font-size: 16px;">Event Ends at</span>
                        </div>

                        <span>
                              <?php echo event_time_display($event_arr['utc_end_at'], $event_locality, $event_timezone); ?>
                            </span>
                    </li>
                    <li class="col-12 col-sm-6  col-xl-3  events-border px-2" style="">
                        <div class="d-flex align-items-center" style="gap: 4px;">
                            <?= icon('globe', '#2557A7', 25) ?>
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
