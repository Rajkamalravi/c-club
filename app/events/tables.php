<?php

$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
    if (!taoh_user_is_logged_in()) {
        header("Location: " . TAOH_SITE_URL_ROOT . "/login");    
        taoh_exit();
    }

//echo '<pre>';print_r($_GET);echo'</pre>';die();
 // Get user info for building the redirect URL
     $current_url = TAOH_SITE_URL_ROOT . '/'.$app_url;
     $email = $user_data->email ??  '';     
     $userfname = $user_data->fname ?? '';

     $userlname = $user_data->lname ?? '';
     
    if($email != '' && $userfname != '' && $userlname != ''){

        $siteData = [];
        $siteData['site_url'] = TAOH_SITE_URL_ROOT;
        $siteData['site_name_slug'] = TAOH_SITE_NAME_SLUG;
        $siteData['site_name_logo'] = TAOH_SITE_LOGO;
        $siteData['site_name_logo_sq'] = TAOH_SITE_FAVICON;
        $siteData['site_current_url'] = $current_url;
        $siteData['back_url'] = $current_url;
        $siteData['site_desc'] = TAOH_SITE_DESCRIPTION;
        $siteData['site_keywords'] = TAOH_SITE_KEYWORDS;
        $siteData['site_app'] = 'site';

        $payload = array(
            'first_name' => $userfname,
            'last_name' => $userlname
        );

        if(isset($_GET['eventtoken']) && $_GET['eventtoken'] !=''){
                $eventtoken = $_GET['eventtoken'];


                $taoh_vals = array(
                    'token' => taoh_get_dummy_token(1),
                    'ops' => 'baseinfo',
                    'mod' => 'events',
                    'eventtoken' => $eventtoken ?? '',
                    'cache_name' => 'event_detail_' . $eventtoken,
                    //'cfcc2h' =>1 //cfcache newly added
                );
                //echo taoh_apicall_get_debug('events.event.get', $taoh_vals);die();

                $result = taoh_apicall_get('events.event.get', $taoh_vals);
                $response = taoh_get_array($result, true);

                if (!$response['success']) {
                    taoh_redirect($current_url);
                    exit();
                }

                $event_arr = $response['output'];
                $events_data = $event_arr['conttoken'] ?? [];

                $event_description_clean = strip_tags(html_entity_decode($event_arr['conttoken']['description'], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                // Remove any remaining HTML entities and normalize whitespace
                $event_description_clean = preg_replace('/\s+/', ' ', trim($event_description_clean));
                $event_short = strlen($event_description_clean) > 157 ? substr($event_description_clean, 0, 157) . '...' : $event_description_clean;
                //echo "=====".$event_short;
                //$event_short = htmlspecialchars($event_short);
                if($event_arr['conttoken'][ 'event_image' ] != ''){
                    $event_image = $event_arr['conttoken'][ 'event_image' ];
                }else{
                    $event_image = TAOH_SITE_URL_ROOT.'/assets/images/event.jpg';
                }

                $siteData['site_app'] = 'events';
                $siteData['back_url'] = $events_data['source'].'/events/d/'.slugify2($events_data['title'])."-".$eventtoken;
                $siteData['event_details']['event_title'] = displayTaohFormatted($events_data['title']);
                $siteData['event_details']['event_desc'] = $event_description_clean;
                $siteData['event_details']['event_short_desc'] = $event_short;
                $siteData['event_details']['event_image'] = $event_image;
                
               
                write_site_details_to_redis($eventtoken, $siteData);

                 $userslug = $userfname.'_'.$userlname.'_'.$email.'_'.strtolower(TAO_TABLES_NAME).'.'.$eventtoken;       
                $userslugencode = $userslug ? md5((string)$userslug) : '';

                if(isset($_GET['to_page']) && $_GET['to_page'] !='' && $_GET['to_page'] !='stlo'){
                    $to_page = '/'.$_GET['to_page'];
                }

                // Build redirect URL to tables.tao.ai with user info and back_url
                $redirect_url = TAO_TABLES_URL.'/k/'.$eventtoken.$to_page.'/lg/' . $userfname. '/' . $userlname. '/' .
                $email. '/'.$userslugencode;

                //https://login.tao.ai/login.php?email=kalaiselvi.k.tao.ai@gmail.com&user_key=03187c05b55b4c8dd8bf5c1533b558c2&callfwdurl=https://tables.tao.ai/k/{eventtoken}&payload={'first_name':'','last_name':''}
                
                $redirect_url = TAOH_MOAT_LOGIN_URL.'?email='.$email.'&user_key='.$userslugencode.'&callfwdurl='.urlencode(TAO_TABLES_URL.'/k/'.$eventtoken.$to_page).'&payload='.urlencode(json_encode($payload));
        }
        else{
			$kk = strtolower(TAO_TABLES_KEYWORD);
            write_site_details_to_redis($kk , $siteData);

            $userslug = $userfname.'_'.$userlname.'_'.$email.'_'.strtolower(TAO_TABLES_NAME).'.'.strtolower(TAO_TABLES_KEYWORD);       
            $userslugencode = $userslug ? md5((string)$userslug) : '';

            // Build redirect URL to tables.tao.ai with user info and back_url
            $redirect_url = TAO_TABLES_URL.'/k/'.strtolower(TAO_TABLES_KEYWORD).'/lg/' . $userfname. '/' . $userlname. '/' .
            $email. '/'.$userslugencode;

             $redirect_url = TAOH_MOAT_LOGIN_URL.'?email='.$email.'&user_key='.$userslugencode.'&callfwdurl='.urlencode(TAO_TABLES_URL.'/k/'.strtolower(TAO_TABLES_KEYWORD)).'&payload='.urlencode(json_encode($payload));

        }

       // echo "========".$redirect_url;die();

        
        //die('-----');

         if(isset($iframe) && $iframe == 0){
            taoh_redirect($redirect_url);  taoh_exit();
         }
           
        
    }else{
       $redirect_url  = $current_url;
      
    }

function write_site_details_to_redis($userslugencode,$room_data = array()) {
    $taoh_vals = array(
        'ops' => 'site_details',
        'app' => 'tables',
        'status' => 'post',
        'code' => TAOH_OPS_CODE,
        'key' => $userslugencode,        
        'value' => $room_data,
       // 'debug' => 1
    );

    //echo'<pre>'; print_r($room_data); echo'</pre>';die();
    

    $room_data_json = taoh_post(TAOH_TABLE_REDIS_URL, $taoh_vals);
   // echo'<pre>'; print_r($room_data_json); echo'</pre>';
   // die('-----aaaaaaaa----');
    return true;

}
taoh_get_header();
?>
   
    <div class="">
          
        <div class="mx-auto" style="max-width: 1215px;">
            <div class="container">

               
                <!-- main elements -->
                <div class="main">
                    <!--<iframe src="<?php echo $redirect_url.'&footer=no'; ?>" style="width:100%; height:90vh; border:none;"></iframe>
-->
                    <iframe src="https://meet.google.com/xrv-omza-fmx" style="width:100%; height:90vh; border:none;"></iframe>
                </div> 
            </div> 
        </div> 
                   
</div>   

        
<?php
taoh_get_footer();