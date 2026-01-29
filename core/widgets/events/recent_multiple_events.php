<?php
    $ops = 'list';
    $limit = 3;
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
        'cache_time' => 120,
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
        // echo "<pre>";print_r($event_detail);die;
        $event_title = ucfirst(taoh_title_desc_decode($event_detail['title']));
        $event_image = $event_detail['event_image'];
        $event_locality = $event_detail['locality'] !='' ? $event_detail['locality'] : 0;
        $event_timezone = $event_detail['local_timezone'];
        $event_utc_start = $event_detail['utc_start_at'];
        $event_local_start = $event_detail['local_start_at'];
        $conttoken = $event_detail['conttoken'];
        $eventtoken = $event_detail['eventtoken'];
        if($event_image == ''){
            $event_image = TAOH_SITE_URL_ROOT."/assets/images/event.jpg";
        }
        $share_link = TAOH_SITE_URL_ROOT.'/events/d/'.slugify2($event_title).'-'.$eventtoken;
        $event_type = strtolower($events_data['event_type'] ?? 'virtual');

        //echo "=========".$event_type;
?>
<a class="event" href ="<?php echo $share_link; ?>" target="_blank">
    <div class="date-box">
        <div class="day"><?php
                 echo event_time_display($event_utc_start,$event_locality,$event_timezone, "date", "j");
            ?></div>
            <div class="month"><?php
                 echo event_time_display($event_utc_start,$event_locality,$event_timezone, "date", "M");
            ?></div>
        </div>
        <div class="event-details">
            <div class="title"><?php echo $event_title; ?></div>
            <div class="info">
                    <?php echo (!empty($event_type) ? ucfirst($event_type) : 'Virtual') ;?> • <?php
                echo event_time_display($event_utc_start,$event_locality,$event_timezone, "date", "h:i A");
            ?>
            </div>
        </div>

    <!-- events new widget html end -->
</a>
<?php } } ?>
