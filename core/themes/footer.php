<?php
$curr_page = taoh_parse_url(0);
$opt = taoh_parse_url(1);

// if (TAOH_JUSASK_ENABLE) {
if((taoh_user_is_logged_in() && (TAOH_ENABLE_OBVIOUSBABA || TAOH_ENABLE_SIDEKICK || TAOH_ENABLE_JUSASK))) {
    include_once('chatbot.php');
}

if (taoh_user_is_logged_in()) {
    if (NETWORKING_VERSION == 1) {
        require_once(TAOH_CORE_PATH . '/club/networking_footer1.php');
    } elseif (NETWORKING_VERSION == 4) {
        require_once(TAOH_CORE_PATH . '/club/networking_footer4.php');
    }  elseif (NETWORKING_VERSION == 5) {
        require_once(TAOH_CORE_PATH . '/club/networking_footer5.php');
    } else {
        require_once(TAOH_CORE_PATH . '/club/networking_footer1.php');
    }
}

if ($curr_page == 'events' && ($opt == 'chat' || $opt == 'd')) {
    if ($opt == 'chat') {
        $current_app = taoh_parse_url(3);
        $eventtoken = taoh_parse_url(4);
    }
    else{
        $current_app = taoh_parse_url(3);
        $parse_url_2 = taoh_parse_url(2);
        $eventtoken_expl = explode('-', $parse_url_2);
        $eventtoken = array_pop($eventtoken_expl);
    }

    require_once(TAOH_APP_PATH . '/events/events_rsvp_directory.php');

    require_once(TAOH_APP_PATH . '/events/events_exhibitors.php');
    require_once(TAOH_APP_PATH . '/events/events_speakers.php');
    require_once(TAOH_APP_PATH . '/events/events_agenda.php');
    require_once(TAOH_APP_PATH . '/events/events_rooms.php');
    require_once(TAOH_APP_PATH . '/events/events_tables.php');
}

if (in_array($curr_page, ['chat','events', 'room', 'forum', 'custom-room'])) {
    require_once TAOH_APP_PATH . '/events/event_upgrade_modal.php';
    require_once(TAOH_APP_PATH . '/events/events_footer.php');
}

function getMaxUploadSize() {

    // Get PHP configuration values
    $upload_max_filesize = ini_get('upload_max_filesize');
    $post_max_size = ini_get('post_max_size');
    $memory_limit = ini_get('memory_limit');

    // Convert to bytes
    $uploadBytes = convertToBytes($upload_max_filesize);
    $postBytes = convertToBytes($post_max_size);
    $memoryBytes = convertToBytes($memory_limit);

    // Try to get Nginx client_max_body_size
    $nginxSize = '1M'; // Default Nginx value
    $nginxBytes = convertToBytes($nginxSize);

    // Attempt to read Nginx config (may not have permissions)
    $nginxConfig = @shell_exec('grep -r client_max_body_size /etc/nginx/ 2>/dev/null | grep -v "#"');

    if (!empty($nginxConfig) && preg_match('/client_max_body_size\s+(\d+[KMG]?);/', $nginxConfig, $matches)) {
        $nginxSize = $matches[1];
        $nginxBytes = convertToBytes($nginxSize);
    } else {
        // Try nginx -T command
        $nginxTest = @shell_exec('nginx -T 2>&1 | grep client_max_body_size | grep -v "#"');
        if (!empty($nginxTest) && preg_match('/client_max_body_size\s+(\d+[KMG]?);/', $nginxTest, $matches)) {
            $nginxSize = $matches[1];
            $nginxBytes = convertToBytes($nginxSize);
        }
    }

    // Find the minimum (most restrictive) value
    $limits = [
        'nginx_client_max_body_size' => $nginxBytes,
        'php_upload_max_filesize' => $uploadBytes,
        'php_post_max_size' => $postBytes
    ];

    // Filter out unlimited values for comparison
    $realLimits = array_filter($limits, function($value) {
        return $value < PHP_INT_MAX;
    });

    if (empty($realLimits)) {
        $minBytes = PHP_INT_MAX;
        $limitingFactor = 'none_unlimited';
    } else {
        $minBytes = min($realLimits);
        $limitingFactor = array_search($minBytes, $limits);
    }

    // Format the result
    $maxUploadMB = $minBytes / 1024 / 1024;

    // Create human-readable format
    if ($minBytes >= 1024 * 1024 * 1024) {
        $formatted = round($minBytes / (1024 * 1024 * 1024), 2) . ' GB';
    } elseif ($minBytes >= 1024 * 1024) {
        $formatted = round($minBytes / (1024 * 1024), 2) . ' MB';
    } elseif ($minBytes >= 1024) {
        $formatted = round($minBytes / 1024, 2) . ' KB';
    } else {
        $formatted = $minBytes . ' bytes';
    }

    return [
        'bytes' => $minBytes,
        'mb' => $maxUploadMB,
        'formatted' => $formatted,
        'limiting_factor' => $limitingFactor,
        'details' => [
            'nginx_client_max_body_size' => [
                'value' => $nginxSize,
                'bytes' => $nginxBytes
            ],
            'php_upload_max_filesize' => [
                'value' => $upload_max_filesize,
                'bytes' => $uploadBytes
            ],
            'php_post_max_size' => [
                'value' => $post_max_size,
                'bytes' => $postBytes
            ],
            'php_memory_limit' => [
                'value' => $memory_limit,
                'bytes' => $memoryBytes
            ]
        ]
    ];
}

function convertToBytes($size) {
    if ($size == '-1') {
        return PHP_INT_MAX; // Unlimited
    }

    $size = trim($size);
    if (empty($size)) {
        return 0;
    }

    $last = strtolower($size[strlen($size)-1]);
    $size = (float)$size;

    switch($last) {
        case 'g': $size *= 1024;
        case 'm': $size *= 1024;
        case 'k': $size *= 1024;
    }
    return (int)$size;
}

function getMaxUploadSizeBytes() {
    $result = getMaxUploadSize();
    return $result['bytes'];
}

function get_max_upload_size() {
    $upload_max = getMaxUploadSizeBytes();
    // $upload_max = convert_to_bytes(ini_get('upload_max_filesize'));
    $post_max   = convert_to_bytes(ini_get('post_max_size'));
    return min($upload_max, $post_max);
}

function convert_to_bytes($val) {
    $val = trim($val);
    $unit = strtolower(substr($val, -1));
    $num = (int)$val;

    switch ($unit) {
        case 'g': $num *= 1024;
        case 'm': $num *= 1024;
        case 'k': $num *= 1024;
    }
    return $num;
}

?>

</main>

<!--<?php
//if (!taoh_user_is_logged_in()) {
   // echo '<div class="col footer-prompt" id="login-prompt" style="display: none;">';
    //echo '<h5 class="pb-2">You need to log in to see the full content!</h5>';
    //echo '<a href="' . (TAOH_LOGIN_URL ?? '') . '" class="btn theme-btn" id="login-btn"><i class="la la-sign-in mr-1"></i>Login / Signup</a>';
    //echo '</div>';
//}
?> -->
 <!-- Footer Banner  -->
<?php if(defined('TAOH_FOOTER_BANNER_AD') && TAOH_FOOTER_BANNER_AD) { 
    // file_get_contents(TAOH_OPS_PREFIX.'/images/calendar', false, stream_context_create(array( "ssl"=>array( "verify_peer"=>false, "verify_peer_name"=>false,),)))
    if(!isset($_SESSION['footer_banner'])){
        $get_banner = @file_get_contents(TAOH_CDN_ADS);
        if ($get_banner === false) {
            error_log("Banner ad fetch failed: " . TAOH_CDN_ADS);
            $_SESSION['footer_banner'] = []; // fallback to empty
        } else {
            $banner = json_decode($get_banner, true);
            $_SESSION['footer_banner'] = $banner['footer'] ?? [];
        }
    }
    if(isset($_SESSION['footer_banner']) && count($_SESSION['footer_banner']) > 0){
    foreach($_SESSION['footer_banner'] as $key=>$val){ 
        $link = str_ireplace('[TAOH_HOME_URL]',TAOH_SITE_URL_ROOT,$val['link']);
    
?>
    <div class="cover-workcongress-image">
    <div class="bg-image" style="background-image: url('<?php echo $val['image'];?>');"></div>
    <div class="glass-overlay"></div>
    <a href="<?php echo $link;?>" class="workcongress-main-image">
        <img src="<?php echo $val['image'];?>" alt="">
    </a>
</div>
<?php } } } ?>

<input type="hidden" name="global_settings" id="global_settings" />
    <input type="hidden" name="avt_img_delete" id="avt_img_delete" />
    <input type="hidden" id="max_upload_size" value="<?php echo get_max_upload_size(); ?>">
    
<?php if( NETWORKING_VERSION == 5 || (taoh_parse_url(1) != 'room' && NETWORKING_VERSION != 5)) { ?>
<footer class="page-footer">
    
    
    <section class="footer-area pt-30px position-relative font-light" style="background: #1E1C1C;">
      <div class="container">
        <div class="row align-items-center pb-4 copyright-wrap">
          <div class="col-lg-12">

                <?php if(taoh_user_is_logged_in()) {
                $ptokn_track = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken;
                $full_url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                $encoded_path = urlencode($full_url);
                //echo "==========".$footer_tracking_link;die();
                //$encoded_path = "aaaa";
                if(!isset($footer_tracking_link) || $footer_tracking_link ==''){
                    $footer_tracking_link = $encoded_path;
                }
            ?>
            <img src="<?php echo TAOH_CDN_PREFIX;?>/images/igtracker/<?php echo $footer_tracking_link;?>/<?php echo $ptokn_track;?>/pixel.png" />
            <?php } ?>
            
            <?php if(TAOH_FOOTER_MENU_ARRAY !='')  { 
                     $footer_array = json_decode(TAOH_FOOTER_MENU_ARRAY,1);  
                
                ?>
           
                <div class="col-xl-5 mx-auto px-0">
                    <ul class="nav justify-content-center" style="margin-bottom: -10px;">
                        <?php foreach($footer_array as $key=>$val){ ?>
                            <li class="nav-item text-center footer-link-text"><a class="nav-link " title="<?php echo $val[2];?>" href="<?php echo $val[0];?>" target="_blank" style="color: #ffffff;"><?php echo $val[1];?></a></li>
                        <?php } ?>
                    </ul>

                </div>
                
            <?php } else { ?>

                <div class="col-xl-5 mx-auto px-0">
                    <ul class="nav justify-content-center" style="margin-bottom: -10px;">
                    <li class="nav-item text-center footer-link-text"><a class="nav-link " href="<?php echo TAOH_SITE_URL_ROOT."/professionals";?>" target="_blank" style="color: #ffffff;">Professionals</a></li>
                    <li class="nav-item text-center footer-link-text"><a class="nav-link " href="<?php echo TAOH_SITE_URL_ROOT."/partners";?>" target="_blank" style="color: #ffffff;">Partners</a></li>
                    <li class="nav-item text-center footer-link-text"><a class="nav-link " href="<?php echo TAOH_SITE_URL_ROOT."/employers";?>" target="_blank" style="color: #ffffff;">Employers</a></li>
                    </ul>
                </div>
           <?php } ?>


            <div class="footer-menu-item-container my-3 px-3">
                <a href="<?php echo TAOH_SITE_URL_ROOT;?>" target="_blank" class="footer-menu-item mx-lg-5">

                    <div class="svg-container">
                        <svg width="16" height="16" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.99738 4.99023C9.99738 5.3418 9.73694 5.61719 9.44178 5.61719H8.88617L8.89833 8.74609C8.89833 8.79883 8.89485 8.85156 8.88964 8.9043V9.21875C8.88964 9.65039 8.57885 10 8.19514 10H7.91734C7.89824 10 7.87914 10 7.86004 9.99805C7.83574 10 7.81143 10 7.78712 10H7.22284H6.80613C6.42242 10 6.11163 9.65039 6.11163 9.21875V8.75V7.5C6.11163 7.1543 5.86335 6.875 5.55603 6.875H4.44482C4.1375 6.875 3.88922 7.1543 3.88922 7.5V8.75V9.21875C3.88922 9.65039 3.57843 10 3.19472 10H2.77801H2.22415C2.1981 10 2.17206 9.99805 2.14602 9.99609C2.12518 9.99805 2.10435 10 2.08351 10H1.80571C1.422 10 1.11121 9.65039 1.11121 9.21875V7.03125C1.11121 7.01367 1.11121 6.99414 1.11294 6.97656V5.61719H0.555603C0.243076 5.61719 0 5.34375 0 4.99023C0 4.81445 0.0520878 4.6582 0.173626 4.52148L4.62539 0.15625C4.74693 0.0195312 4.88583 0 5.00737 0C5.12891 0 5.26781 0.0390625 5.37198 0.136719L9.80639 4.52148C9.94529 4.6582 10.0147 4.81445 9.99738 4.99023Z" fill="#1E1C1C"/>
                        </svg>
                    </div>
                    <span>Home</span>
                </a>
                <!--<div class="terms-menu" style="position: relative;">
                    <a class="footer-menu-item mx-lg-5" id="termsLink" style="cursor: pointer;">
                        <div class="svg-container">
                            <svg width="16" height="16" viewBox="0 0 10 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0 1.11111C0 0.498264 0.498264 0 1.11111 0H3.88889V2.22222C3.88889 2.52951 4.13715 2.77778 4.44444 2.77778H6.66667V3.44792C5.38368 3.81076 4.44444 4.98958 4.44444 6.38889C4.44444 7.41493 4.94965 8.32118 5.72396 8.87674C5.6684 8.88542 5.61285 8.88889 5.55556 8.88889H1.11111C0.498264 8.88889 0 8.39062 0 7.77778V1.11111ZM6.66667 2.22222H4.44444V0L6.66667 2.22222ZM7.5 3.88889C8.16304 3.88889 8.79893 4.15228 9.26777 4.62112C9.73661 5.08996 10 5.72585 10 6.38889C10 7.05193 9.73661 7.68782 9.26777 8.15666C8.79893 8.6255 8.16304 8.88889 7.5 8.88889C6.83696 8.88889 6.20107 8.6255 5.73223 8.15666C5.26339 7.68782 5 7.05193 5 6.38889C5 5.72585 5.26339 5.08996 5.73223 4.62112C6.20107 4.15228 6.83696 3.88889 7.5 3.88889ZM7.5 8.05556C7.61051 8.05556 7.71649 8.01166 7.79463 7.93352C7.87277 7.85538 7.91667 7.7494 7.91667 7.63889C7.91667 7.52838 7.87277 7.4224 7.79463 7.34426C7.71649 7.26612 7.61051 7.22222 7.5 7.22222C7.38949 7.22222 7.28351 7.26612 7.20537 7.34426C7.12723 7.4224 7.08333 7.52838 7.08333 7.63889C7.08333 7.7494 7.12723 7.85538 7.20537 7.93352C7.28351 8.01166 7.38949 8.05556 7.5 8.05556ZM6.38889 5.58333V5.69444C6.38889 5.84722 6.51389 5.97222 6.66667 5.97222C6.81944 5.97222 6.94444 5.84722 6.94444 5.69444V5.58333C6.94444 5.49132 7.0191 5.41667 7.11111 5.41667H7.81424C7.94792 5.41667 8.05556 5.52431 8.05556 5.65799C8.05556 5.74826 8.00521 5.82986 7.92708 5.87153L7.37153 6.16319C7.27951 6.21181 7.22222 6.30556 7.22222 6.40972V6.66667C7.22222 6.81944 7.34722 6.94444 7.5 6.94444C7.65278 6.94444 7.77778 6.81944 7.77778 6.66667V6.57812L8.18576 6.36458C8.44792 6.22743 8.61111 5.95486 8.61111 5.65972C8.61111 5.21875 8.25347 4.86285 7.81424 4.86285H7.11111C6.71181 4.86285 6.38889 5.18576 6.38889 5.58507V5.58333Z" fill="#1E1C1C"/>
                            </svg>
                        </div>
                        <span>Terms and Policies</span>
                    </a>
                    <ul id="termsList" style="display: none;">
                        <li><a href="https://tao.ai/privacy.php" target="_BLANK" class="term-item">Privacy Policy</a></li>
                        <li><a href="https://tao.ai/terms.php" target="_BLANK" class="term-item">Terms & Conditions</a></li>
                        <li><a href="https://tao.ai/conduct.php" target="_BLANK" class="term-item">Code of Conduct</a></li>
                    </ul>
                </div>-->

                <div class="dropdown terms-menu">
                   
                    <a class="footer-menu-item mx-lg-5 dropdown-toggle removecaret text-wrap" id="termsLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" classss="" id="termsLink" style="cursor: pointer;">
                        <div class="svg-container">
                            <svg width="16" height="16" viewBox="0 0 10 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0 1.11111C0 0.498264 0.498264 0 1.11111 0H3.88889V2.22222C3.88889 2.52951 4.13715 2.77778 4.44444 2.77778H6.66667V3.44792C5.38368 3.81076 4.44444 4.98958 4.44444 6.38889C4.44444 7.41493 4.94965 8.32118 5.72396 8.87674C5.6684 8.88542 5.61285 8.88889 5.55556 8.88889H1.11111C0.498264 8.88889 0 8.39062 0 7.77778V1.11111ZM6.66667 2.22222H4.44444V0L6.66667 2.22222ZM7.5 3.88889C8.16304 3.88889 8.79893 4.15228 9.26777 4.62112C9.73661 5.08996 10 5.72585 10 6.38889C10 7.05193 9.73661 7.68782 9.26777 8.15666C8.79893 8.6255 8.16304 8.88889 7.5 8.88889C6.83696 8.88889 6.20107 8.6255 5.73223 8.15666C5.26339 7.68782 5 7.05193 5 6.38889C5 5.72585 5.26339 5.08996 5.73223 4.62112C6.20107 4.15228 6.83696 3.88889 7.5 3.88889ZM7.5 8.05556C7.61051 8.05556 7.71649 8.01166 7.79463 7.93352C7.87277 7.85538 7.91667 7.7494 7.91667 7.63889C7.91667 7.52838 7.87277 7.4224 7.79463 7.34426C7.71649 7.26612 7.61051 7.22222 7.5 7.22222C7.38949 7.22222 7.28351 7.26612 7.20537 7.34426C7.12723 7.4224 7.08333 7.52838 7.08333 7.63889C7.08333 7.7494 7.12723 7.85538 7.20537 7.93352C7.28351 8.01166 7.38949 8.05556 7.5 8.05556ZM6.38889 5.58333V5.69444C6.38889 5.84722 6.51389 5.97222 6.66667 5.97222C6.81944 5.97222 6.94444 5.84722 6.94444 5.69444V5.58333C6.94444 5.49132 7.0191 5.41667 7.11111 5.41667H7.81424C7.94792 5.41667 8.05556 5.52431 8.05556 5.65799C8.05556 5.74826 8.00521 5.82986 7.92708 5.87153L7.37153 6.16319C7.27951 6.21181 7.22222 6.30556 7.22222 6.40972V6.66667C7.22222 6.81944 7.34722 6.94444 7.5 6.94444C7.65278 6.94444 7.77778 6.81944 7.77778 6.66667V6.57812L8.18576 6.36458C8.44792 6.22743 8.61111 5.95486 8.61111 5.65972C8.61111 5.21875 8.25347 4.86285 7.81424 4.86285H7.11111C6.71181 4.86285 6.38889 5.18576 6.38889 5.58507V5.58333Z" fill="#1E1C1C"/>
                            </svg>
                        </div>
                        <span>Terms</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="dropdown-item"><a href="https://tao.ai/privacy.php" target="_BLANK" class="term-item">Privacy Policy</a></li>
                        <li class="dropdown-item"><a href="https://tao.ai/terms.php" target="_BLANK" class="term-item">Terms & Conditions</a></li>
                        <li class="dropdown-item"><a href="https://tao.ai/conduct.php" target="_BLANK" class="term-item">Code of Conduct</a></li>
                    </ul>
                </div>
                
                <a  href="https://tao.ai" target="_blank" class="footer-menu-item mx-lg-5">
                    <svg width="30" height="30" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5341 4.42451L8.4495 4.39261C8.24982 5.34678 7.3313 10.9602 8.343 11.5608C9.20029 12.0685 9.89517 11.1648 10.4383 11.1303C11.1944 13.3868 6.82811 14.7556 6.5033 11.7788C6.30362 9.93955 7.55228 5.18199 7.34461 4.32882C5.92823 4.29161 4.07788 4.02849 4.06723 5.7242C4.0619 6.44447 4.46392 6.69697 3.72644 6.76873C2.77331 5.31489 3.48949 3.36934 4.76743 2.82448C5.75784 2.40454 9.26152 2.68627 10.7258 2.68627C11.2157 2.68627 12.2274 2.51882 12.5496 2.65438C13.1805 4.26769 11.7748 4.42451 10.5341 4.42451ZM16 8.02855C16 6.8777 15.6645 5.62851 15.3051 4.79129C14.9271 3.90888 14.3067 3.01319 13.6757 2.38859C11.4473 0.174598 7.80254 -0.73173 4.81269 0.668958C3.48949 1.28824 2.31272 2.17862 1.5007 3.40123C0.896336 4.31022 0.47568 5.04113 0.249378 6.17869C-0.0567951 7.71759 -0.0967307 8.04716 0.21743 9.62061C0.459706 10.8352 0.773867 11.4492 1.40485 12.4645C2.69344 14.535 5.45699 15.9436 7.94897 15.9995C8.31372 16.0101 9.29081 15.8613 9.65023 15.7815C12.1369 15.2446 14.1789 13.6021 15.2465 11.3057C15.4276 10.9176 15.6486 10.317 15.7684 9.81198C15.8616 9.40267 16 8.44052 16 8.02855Z" fill="white"/>
                    </svg>
                    <span>By Tao.ai</span>
                </a>
                <?php if(taoh_user_is_logged_in()){ ?>
                <a class="feedback-page footer-menu-item mx-lg-5" target="_blank" style="cursor:pointer;">
                    <div class="svg-container">
                        <svg width="16" height="16" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.25 0C0.560547 0 0 0.560547 0 1.25V8.75C0 9.43945 0.560547 10 1.25 10H6.25C6.93945 10 7.5 9.43945 7.5 8.75V8.37305C7.44727 8.39453 7.39453 8.41211 7.33984 8.42578L6.16602 8.71875C6.10742 8.73242 6.04883 8.74219 5.99023 8.74609C5.97266 8.74805 5.95508 8.75 5.9375 8.75H4.6875C4.56836 8.75 4.46094 8.68359 4.4082 8.57812L4.23633 8.23242C4.20312 8.16602 4.13672 8.125 4.06445 8.125C3.99219 8.125 3.92383 8.16602 3.89258 8.23242L3.7207 8.57812C3.66406 8.69336 3.54102 8.76172 3.41406 8.75C3.28711 8.73828 3.17773 8.65039 3.14258 8.5293L2.8125 7.44141L2.62109 8.08203C2.50195 8.47852 2.13672 8.75 1.72266 8.75H1.5625C1.39062 8.75 1.25 8.60938 1.25 8.4375C1.25 8.26562 1.39062 8.125 1.5625 8.125H1.72266C1.86133 8.125 1.98242 8.03516 2.02148 7.90234L2.3125 6.93555C2.37891 6.71484 2.58203 6.5625 2.8125 6.5625C3.04297 6.5625 3.24609 6.71484 3.3125 6.93555L3.53906 7.68945C3.68359 7.56836 3.86719 7.5 4.0625 7.5C4.37305 7.5 4.65625 7.67578 4.79492 7.95312L4.88086 8.125H5.05469C4.99414 7.95312 4.98242 7.76562 5.02734 7.58203L5.32031 6.4082C5.375 6.1875 5.48828 5.98828 5.64844 5.82812L7.5 3.97656V3.125H5C4.6543 3.125 4.375 2.8457 4.375 2.5V0H1.25ZM5 0V2.5H7.5L5 0ZM10.7383 2.72852C10.4336 2.42383 9.93945 2.42383 9.63281 2.72852L9.05859 3.30273L10.4453 4.68945L11.0195 4.11523C11.3242 3.81055 11.3242 3.31641 11.0195 3.00977L10.7383 2.72852ZM6.0918 6.26953C6.01172 6.34961 5.95508 6.44922 5.92773 6.56055L5.63477 7.73438C5.60742 7.8418 5.63867 7.95312 5.7168 8.03125C5.79492 8.10938 5.90625 8.14062 6.01367 8.11328L7.1875 7.82031C7.29688 7.79297 7.39844 7.73633 7.47852 7.65625L10.002 5.13086L8.61523 3.74414L6.0918 6.26953Z" fill="#1E1C1C"/>
                        </svg>
                    </div>
                    <span>Feedback</span>
                </a>
                <?php } ?>
                <?php if ( taoh_user_is_logged_in() && defined( 'TAOH_SITE_DONATE_ENABLE' ) && TAOH_SITE_DONATE_ENABLE ) { ?>
                <a href="<?php echo TAOH_SITE_URL_ROOT."/donate"; ?>" class="footer-menu-item mx-lg-5">
                    <div class="svg-container">
                        <svg width="16" height="16" viewBox="0 0 11 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.68724 6.83475C2.65604 6.58123 3.45611 6.5761 4.86577 7.44936L6.74895 7.06267C7.03741 7.00377 7.20341 7.08316 7.30682 7.20864C7.37214 7.29059 7.39663 7.38534 7.37214 7.48522C7.34764 7.58509 7.28505 7.66192 7.18708 7.70801L5.1624 8.67346C4.98007 8.78358 5.10525 8.96797 5.2522 8.90394L7.57079 7.81045C7.66604 7.76435 7.7368 7.69777 7.78306 7.60558C7.83476 7.50314 7.91096 7.42119 8.01437 7.35205L9.89211 6.11515C10.3683 5.80016 10.7412 6.23807 10.5915 6.39684L8.29739 8.58383L5.8509 9.76952C5.52706 9.92573 5.20594 9.98207 4.844 9.9411L1.72534 9.61075C1.6056 9.59794 1.51579 9.50319 1.51579 9.39051V7.04987C1.51579 6.94743 1.58383 6.86292 1.68724 6.83475ZM5.85907 1.29557C5.86451 1.30581 5.87811 1.31349 5.89172 1.31349C5.90533 1.31349 5.91621 1.30581 5.92438 1.29557C6.45232 0.419748 7.26056 -0.256324 8.36815 0.0945161C9.21993 0.363408 9.83768 1.15472 9.83768 2.08944C9.83768 3.41598 8.3872 4.49411 7.4565 5.34944L6.14481 6.48647C6.00058 6.6094 5.78015 6.6094 5.63591 6.48647L4.32695 5.34944C3.39624 4.49411 1.94576 3.41598 1.94576 2.08944C1.94576 1.12911 2.59617 0.322434 3.48333 0.074029C4.56642 -0.228155 5.34745 0.447918 5.85907 1.29557ZM0.236757 6.85268H0.990571C1.1212 6.85268 1.22733 6.95255 1.22733 7.07291V9.55697C1.22733 9.80025 1.01506 10 0.756535 10H0.236757C0.106133 10 0 9.90013 0 9.7772V7.07291C0 6.95255 0.106133 6.85268 0.236757 6.85268Z" fill="black"/>
                        </svg>
                    </div>
                    <span>Donate</span>
                </a>
                <?php } ?>
                <!-- <a target="_blank" href="<?php echo TAOH_SITE_URL_ROOT."/bugreport"; ?>" class="report-bug-page footer-menu-item mx-lg-5" style="cursor:pointer;">
                    <div class="svg-container">
                        <svg width="16" height="16" viewBox="0 0 64 59" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M45 14H43V11C43 8.08262 41.8411 5.28472 39.7782 3.22182C37.7153 1.15892 34.9174 0 32 0C29.0826 0 26.2847 1.15892 24.2218 3.22182C22.1589 5.28472 21 8.08262 21 11V14H19C17.6953 14.0011 16.4168 14.3669 15.3089 15.056C14.2011 15.7452 13.3078 16.7302 12.73 17.9L0 11.72V18.39L12 24.21V32H0V38H12C12.0051 40.4388 12.4593 42.8558 13.34 45.13L0 51.61V58.28L16.31 50.37C18.1755 52.7486 20.5574 54.6719 23.2756 55.9945C25.9938 57.3171 28.9771 58.0044 32 58.0044C35.0229 58.0044 38.0062 57.3171 40.7244 55.9945C43.4426 54.6719 45.8245 52.7486 47.69 50.37L64 58.28V51.61L50.66 45.13C51.5407 42.8558 51.9949 40.4388 52 38H64V32H52V24.22L64 18.4V11.72L51.27 17.9C50.6922 16.7302 49.7989 15.7452 48.6911 15.056C47.5832 14.3669 46.3047 14.0011 45 14ZM27 11C27 9.67392 27.5268 8.40215 28.4645 7.46447C29.4021 6.52678 30.6739 6 32 6C33.3261 6 34.5979 6.52678 35.5355 7.46447C36.4732 8.40215 37 9.67392 37 11V14H27V11ZM46 38C45.9989 41.191 44.9077 44.2859 42.9072 46.772C40.9067 49.2581 38.1169 50.9862 35 51.67V28H29V51.67C25.8831 50.9862 23.0933 49.2581 21.0928 46.772C19.0923 44.2859 18.0011 41.191 18 38V21C18 20.7348 18.1054 20.4804 18.2929 20.2929C18.4804 20.1054 18.7348 20 19 20H45C45.2652 20 45.5196 20.1054 45.7071 20.2929C45.8946 20.4804 46 20.7348 46 21V38Z" fill="black"/>
                        </svg>
                    </div>
                    <span>Report a Bug</span>
                </a> -->
            </div>
           
            <p class="text-center text-muted" style="color: #999999;">
              <strong style="color: #6C757D;">&copy; <?php echo date('Y'). "</strong> | <strong>".TAOH_SITE_NAME_SLUG."</strong> | "."<a href='https://theworkcompany.com' target='_blank' class='twc-logo' style='color: #fff;'>The<b>W</b><img src='https://theworkcompany.com/assets/images/theworkcompany_sq.png' alt='O' height='14'><b style='color: #fff;'>RK</b>Company</a>"; ?> | <strong style="color: #6C757D;">All Rights Reserved</strong>  <?php if(taoh_user_is_logged_in()) { ?> |  <strong style="color: #6C757D;"><a class="text-primary cursor-pointer" data-toggle="modal" data-target="#reportBugModal">Report an issue</a> <?php } ?>
              </strong><br>
            </p>
          </div><!-- end col-lg-12 -->
        </div><!-- end row -->
      </div><!-- end container -->
    </section>
    <section class="extraSpace"></section>
</footer>
<?php } ?>

<?php
require_once TAOH_SITE_PATH_ROOT . '/core/basic-settings-modal.php';
?>


<!-- dojo toast like popup -->
<div class="dojo-popup success d-none" id="dojo-popup">
    <h6 class="popup-title-text d-flex align-items-center border-bottom" style="gap: 8px;">
        <img class="sm-img" src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/Group 194.svg';?>" alt="">
        <strong>
            Dojo Suggestion
        </strong>
    </h6>
    <div class="popup-text">
        <p class="sm-text dojo-sugg-msg"></p>
    </div>
</div>

<!-- dojo toast like popup end -->



<button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#dojoV1Modal">
  Launch Modal
</button>

<!-- dojo pop up V1 modal -->
<div class="modal fade dojo-v1-modal" id="dojoV1Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog light-dark" role="document">
        <div class="modal-content" style="border: 1px solid #ccc; border-radius: 8px;">
            <div class="modal-header py-4" style="border: none; position: relative;">
                
                <button type="button" class="btn dojo-v1-close shadow-none" data-dismiss="modal" aria-label="Close" style="background: none; border: none;">
                    <svg width="10" height="10" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19.4172 3.41719C20.1984 2.63594 20.1984 1.36719 19.4172 0.585938C18.6359 -0.195312 17.3672 -0.195312 16.5859 0.585938L10.0047 7.17344L3.41719 0.592187C2.63594 -0.189063 1.36719 -0.189063 0.585938 0.592187C-0.195312 1.37344 -0.195312 2.64219 0.585938 3.42344L7.17344 10.0047L0.592188 16.5922C-0.189062 17.3734 -0.189062 18.6422 0.592188 19.4234C1.37344 20.2047 2.64219 20.2047 3.42344 19.4234L10.0047 12.8359L16.5922 19.4172C17.3734 20.1984 18.6422 20.1984 19.4234 19.4172C20.2047 18.6359 20.2047 17.3672 19.4234 16.5859L12.8359 10.0047L19.4172 3.41719Z" fill="white"/>
                    </svg>
                </button>
               
                <h5 class="modal-title w-100 heading" id="myModalLabel">Dojo Says !</h5>

                <!-- dojo svg -->
                <svg class="dojo-v1-svg" width="69" height="51" viewBox="0 0 69 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M49.5658 17C49.5658 17 44.022 11.0179 45.1519 0L58 2.08076C58 2.08076 57.4602 11.3992 49.5658 17Z" fill="#FF3B38"/>
                    <path d="M52 19.4342C52 19.4342 57.9821 24.978 69 23.8481L66.9192 11C66.9192 11 57.6008 11.5398 52 19.4342Z" fill="#FF3B38"/>
                    <path d="M66 41C66 36.6664 65.1464 32.3752 63.488 28.3714C61.8296 24.3677 59.3989 20.7298 56.3345 17.6655C53.2702 14.6011 49.6323 12.1704 45.6286 10.512C41.6248 8.85357 37.3336 8 33 8C28.6664 8 24.3752 8.85357 20.3714 10.512C16.3677 12.1704 12.7298 14.6011 9.66547 17.6655C6.60114 20.7298 4.17038 24.3677 2.51197 28.3714C0.853569 32.3752 -3.78857e-07 36.6664 0 41L33 41H66Z" fill="url(#paint0_linear_7568_2)"/>
                    <path d="M50 41C50 38.7675 49.5603 36.5569 48.706 34.4944C47.8516 32.4318 46.5994 30.5578 45.0208 28.9792C43.4422 27.4006 41.5682 26.1484 39.5056 25.294C37.4431 24.4397 35.2325 24 33 24C30.7675 24 28.5569 24.4397 26.4944 25.294C24.4318 26.1484 22.5578 27.4006 20.9792 28.9792C19.4006 30.5578 18.1484 32.4318 17.294 34.4944C16.4397 36.5569 16 38.7675 16 41L33 41H50Z" fill="url(#paint1_linear_7568_2)"/>
                    <path d="M32.6953 33.0198C32.7581 32.9963 32.8268 32.9995 32.9012 33.0011C32.9987 33.0044 33.0194 33.0919 33.0293 33.1681C33.045 33.2921 33.0698 33.4258 33.1054 33.5684C33.1153 33.6097 33.1211 33.6519 33.1244 33.6932C33.1351 33.8764 33.1483 34.0182 33.2004 34.177C33.2475 34.3237 33.2484 34.4785 33.2938 34.6179C33.3195 34.6957 33.3567 34.8188 33.3674 34.9226C33.3773 35.0158 33.4005 35.1009 33.436 35.1795C33.4865 35.2897 33.4832 35.4218 33.5294 35.5377C33.5824 35.6706 33.6055 35.8019 33.6568 35.9307C33.6882 36.0126 33.7105 36.0928 33.7345 36.1747C33.746 36.2119 33.7576 36.2443 33.7708 36.2711C33.8204 36.3748 33.8502 36.4818 33.9023 36.5879C33.9883 36.7614 34.0709 36.9753 34.1768 37.169C34.2594 37.3205 34.3685 37.5118 34.4769 37.6552C34.5645 37.7744 34.619 37.8797 34.7125 37.9883C34.8547 38.148 35.0539 38.4032 35.244 38.5953C35.4143 38.7671 35.5962 38.9025 35.8293 39.0913C35.926 39.1691 36.0286 39.2444 36.1368 39.3182C36.3766 39.4795 36.5866 39.6091 36.7626 39.7039C37.1471 39.9122 37.5802 40.1051 38.063 40.2801C38.0887 40.2899 38.1523 40.3101 38.2515 40.3417C38.3375 40.3677 38.4226 40.4041 38.5053 40.4268C38.6144 40.456 38.7012 40.4827 38.7657 40.5087C38.8294 40.5346 38.8996 40.554 38.9774 40.5686C39.1038 40.5913 39.2287 40.6562 39.3659 40.6756C39.4403 40.6853 39.5205 40.7064 39.6098 40.7412C39.7395 40.7907 39.8677 40.7891 40.0041 40.8247C40.0091 40.8255 40.0487 40.8377 40.1231 40.8604C40.2372 40.8936 40.3315 40.9058 40.4555 40.9236C40.5555 40.9382 40.687 40.9965 40.8093 41.016C40.8192 41.0168 40.873 41.0233 40.9722 41.0338C41.0019 41.037 41.0325 41.0419 41.0623 41.046C41.0871 41.0492 41.1152 41.0557 41.1507 41.0662C41.2722 41.1043 41.3963 41.1294 41.5219 41.1424C41.693 41.1594 41.7831 41.1748 41.8989 41.2729C42.0105 41.3669 42.0675 41.567 41.8658 41.6076C41.8162 41.6189 41.693 41.6448 41.4938 41.687C41.4128 41.704 41.3293 41.7162 41.24 41.721C41.0978 41.7299 41.0127 41.7356 40.9854 41.7372C40.9068 41.7437 40.8572 41.7486 40.8366 41.7518C40.6845 41.7688 40.4927 41.8328 40.3877 41.8361C40.2381 41.8401 40.1355 41.8491 40.0818 41.862C39.8768 41.9123 39.7925 41.9398 39.637 41.9568C39.5403 41.9674 39.4775 41.9771 39.4477 41.986C39.3088 42.0282 39.2204 42.0533 39.184 42.0581C39.0071 42.0897 38.8856 42.1181 38.8194 42.1424C38.7359 42.174 38.6698 42.1829 38.5681 42.2129C38.4135 42.2583 38.2317 42.3158 38.0225 42.3823C37.8844 42.4269 37.7604 42.4731 37.6488 42.5217C37.5959 42.5444 37.4736 42.5979 37.2818 42.6805C36.8445 42.8685 36.4113 43.1262 36.0442 43.4423C35.7731 43.6757 35.5151 43.9342 35.2679 44.2154C35.1464 44.3532 35.0191 44.5469 34.9216 44.6709C34.8455 44.7681 34.7777 44.8929 34.6959 45.0072C34.6587 45.0615 34.6289 45.1069 34.6066 45.1425C34.4669 45.3799 34.3842 45.5266 34.3586 45.5817C34.2966 45.7179 34.2189 45.8889 34.123 46.0939C33.9948 46.3686 33.9056 46.5826 33.8568 46.7358C33.827 46.8298 33.8055 46.893 33.7906 46.9238C33.7559 47.0008 33.7336 47.0624 33.7245 47.1102C33.7071 47.2017 33.6435 47.3217 33.6203 47.4351C33.6005 47.53 33.5889 47.5907 33.5476 47.692C33.5187 47.7617 33.5021 47.8112 33.4988 47.8412C33.4831 47.9514 33.4765 48.0259 33.4451 48.1216C33.4087 48.2253 33.3839 48.3355 33.3698 48.4522C33.3508 48.6086 33.293 48.7577 33.2706 48.8939C33.25 49.0227 33.25 49.2026 33.221 49.342C33.1979 49.4482 33.1756 49.5292 33.164 49.6338C33.1284 49.9344 33.1342 50.2197 33.1144 50.543C33.1061 50.6638 33.0598 51.0779 32.8375 50.9871C32.6944 50.9296 32.6647 50.791 32.6597 50.6573C32.6581 50.5925 32.6556 50.4822 32.6531 50.3299C32.6498 50.103 32.5522 49.8882 32.5398 49.6662C32.5365 49.6111 32.5299 49.5325 32.52 49.4296C32.5076 49.3031 32.4737 49.218 32.4415 49.0884C32.4109 48.9652 32.42 48.7764 32.3803 48.6548C32.3472 48.5543 32.3125 48.4441 32.3018 48.308C32.2951 48.2067 32.2629 48.0705 32.2067 47.8987C32.1951 47.8606 32.1827 47.7812 32.1728 47.6597C32.1695 47.6256 32.1505 47.5551 32.1141 47.4506C32.0926 47.3866 32.0761 47.325 32.0645 47.2634C32.0471 47.1661 32.0372 47.0908 32.0075 47.0146C31.9545 46.8785 31.938 46.7326 31.8909 46.5867C31.8347 46.4109 31.7884 46.2674 31.7528 46.1548C31.6991 45.983 31.647 45.8403 31.5982 45.7277C31.4808 45.4538 31.3858 45.2471 31.3163 45.111C31.1932 44.8719 31.0948 44.7058 31.022 44.6126C30.8699 44.4173 30.76 44.2366 30.612 44.0834C30.4268 43.8889 30.2516 43.7195 30.0308 43.5712C29.9101 43.4894 29.801 43.3937 29.6588 43.3087C29.4422 43.1798 29.2562 43.0761 29.1008 42.9967C28.8553 42.8719 28.6379 42.7932 28.3245 42.6757C28.265 42.6539 28.2088 42.6433 28.1542 42.619C28.0856 42.589 28.012 42.5639 27.9335 42.5436C27.8219 42.5145 27.7483 42.4942 27.7095 42.4796C27.5524 42.4245 27.4284 42.4197 27.2755 42.3645C27.1994 42.337 27.0828 42.3135 26.9249 42.294C26.8398 42.2843 26.6902 42.2252 26.5736 42.2065C26.4711 42.1903 26.3297 42.1814 26.2165 42.149C26.1437 42.1279 26.0817 42.1101 26.0329 42.0955C25.9949 42.0841 25.9023 42.076 25.7552 42.0696C25.7014 42.0671 25.6287 42.0534 25.5386 42.0274C25.3641 41.9756 25.2261 41.9658 25.0169 41.9512C24.9582 41.9472 24.9045 41.9383 24.8375 41.9188C24.6722 41.8694 24.5317 41.8556 24.3498 41.8394C24.2721 41.8313 24.2159 41.8216 24.1836 41.8078C24.1134 41.7778 24.0059 41.7033 24.0009 41.6222C23.991 41.4764 24.0605 41.3969 24.2109 41.384C24.3324 41.3743 24.4308 41.3629 24.5044 41.3483C24.6259 41.3256 24.7359 41.3013 24.8359 41.2754C24.8747 41.2657 24.9574 41.2576 25.0864 41.2527C25.1666 41.2495 25.2716 41.2243 25.4014 41.1789C25.5138 41.14 25.6609 41.1498 25.775 41.1238C25.7998 41.1182 25.856 41.1003 25.9453 41.0679C26.009 41.0452 26.0941 41.029 26.1999 41.0209C26.233 41.0193 26.3198 40.9917 26.462 40.9415C26.5389 40.9139 26.6769 40.9107 26.7588 40.8783C26.8133 40.8556 26.8894 40.8224 26.9613 40.807C27.0448 40.7891 27.1167 40.7673 27.1754 40.7438C27.306 40.6919 27.4119 40.6708 27.5358 40.6157C27.583 40.5955 27.6301 40.576 27.678 40.5574C28.1228 40.3848 28.5659 40.1805 28.9718 39.9309C29.0809 39.8637 29.1735 39.8175 29.2636 39.7551C29.4182 39.6489 29.6001 39.5355 29.7621 39.4082C29.9143 39.2883 30.1003 39.1692 30.221 39.0557C30.3739 38.9123 30.5483 38.7421 30.7435 38.5492C30.8972 38.3969 30.9964 38.2867 31.0411 38.2194C31.089 38.144 31.1452 38.0662 31.208 37.9836C31.2858 37.8847 31.3403 37.8053 31.3759 37.7486C31.5305 37.4901 31.6569 37.2494 31.7561 37.0241C31.7735 36.9868 31.7958 36.9325 31.8223 36.8644C31.8727 36.734 31.9157 36.6529 31.9455 36.533C31.9612 36.473 31.981 36.4146 32.005 36.3595C32.0562 36.2445 32.0604 36.1351 32.1042 36.0184C32.1472 35.9025 32.1786 35.7858 32.1959 35.6683C32.2001 35.6432 32.2158 35.5945 32.2431 35.5224C32.3009 35.366 32.2918 35.2088 32.3439 35.0524C32.3555 35.0135 32.3737 34.9576 32.3968 34.883C32.4084 34.8425 32.4183 34.7444 32.4266 34.5888C32.4282 34.5524 32.4514 34.4729 32.4944 34.353C32.5109 34.3076 32.5233 34.2217 32.5308 34.0937C32.5456 33.8603 32.5539 33.7371 32.5572 33.7242C32.5853 33.6285 32.6151 33.5199 32.6267 33.4113C32.6448 33.2574 32.6564 33.1407 32.6655 33.0588C32.668 33.0402 32.6779 33.0271 32.6953 33.0198Z" fill="white"/>
                    <defs>
                    <linearGradient id="paint0_linear_7568_2" x1="33" y1="8" x2="33" y2="74" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FDCC6E"/>
                    <stop offset="1" stop-color="#FF6600"/>
                    </linearGradient>
                    <linearGradient id="paint1_linear_7568_2" x1="33" y1="24" x2="33" y2="58" gradientUnits="userSpaceOnUse">
                    <stop offset="0.1" stop-color="#FD1D1D"/>
                    <stop offset="0.9" stop-color="#FF6600"/>
                    </linearGradient>
                    </defs>
                </svg>
                <!-- dojo svg end-->
            </div>

            <div class="modal-body">
                <p class="sm-text">Start a one to one conversation ! Find your match from Participants from the list !</p>
                <button type="button" class="btn dojo-v1-btn">Find People !</button>
            </div>
        </div>
    </div>
</div>
<!-- dojo pop up V1 modal end -->

<!-- chat bot -->
<div class="chatbot-acc d-none">
    <div style="" class="accordion" id="accordionExample">
        <div class="card" style="overflow: unset; border-radius: 8px; border-color: #2557A7;">
            <!-- side-kick-svg.svg -->
            <div class="card-header" id="headingOne">
                <div class="title-con">
                    <img class="bot-img" src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/side-kick-svg.svg';?>" alt="">
                    <div>
                        <h6 class="bot-title mb-0">Side Kick</h6>
                        <p class="bot-description mb-0">AI-Powered Career Coach assists you with everything around career !</p>
                    </div>
                </div>
                <button class="btn p-0" type="button" data-toggle="collapse" data-target="#chatOne" aria-expanded="true" aria-controls="collapseOne">
                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5.95898 9.26562H12.5723" stroke="white" stroke-width="1.24" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9.26667 17.5333C13.8133 17.5333 17.5333 13.8133 17.5333 9.26667C17.5333 4.72 13.8133 1 9.26667 1C4.72 1 1 4.72 1 9.26667C1 13.8133 4.72 17.5333 9.26667 17.5333Z" stroke="white" stroke-width="1.24" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                </button>
                <div class="dropdown text-right">
                    <button class="btn dropdown-toggle text-white py-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#">Support</a>
                        <a class="dropdown-item" href="#">Obvious Baba</a>
                    </div>
                </div>
            </div>

            <div id="chatOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample"  style="height: 350px">
                <div class="card-body px-2">
                    <div class="user-chatarea-messages pr-2" style="max-height: 210px;">
                        <div class="acc-user-chat">
                            <img class="user-img" src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/profile_room_1.png';?>" alt="">
                            <p class="chat-message">What is a super perfect resume !</p>

                            <span class="time-stamp">7:20</span>
                        </div>
                        <div class="bot-chat">
                            <img class="bot-img" src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/side-kick-svg.svg';?>" alt="">
                            <p class="chat-message">The secret to super perfect resume is keep it simple don’t over do it. Do you need help to create one !</p>

                            <span class="time-stamp">7:20</span>
                        </div>
                    </div>
                    <form class="chat-form py-2 align-items-center">
                        <input type="text" class="mb-0 form-control" placeholder="Ask Your Career Coach !">
                        <button type="submit" class="btn">
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.04768 2.03133L13.1239 5.56946C16.2983 7.15666 16.2983 9.75239 13.1239 11.3396L6.04768 14.8777C1.28608 17.2585 -0.656589 15.3076 1.72421 10.5543L2.44341 9.12413C2.62528 8.76039 2.62528 8.15692 2.44341 7.79319L1.72421 6.35479C-0.656589 1.60146 1.29434 -0.349475 6.04768 2.03133Z" stroke="#4361EE" stroke-width="1.24" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- chat bot end -->

<!--bug Modal -->
<div class="modal fade" id="reportBugModal" tabindex="-1" role="dialog" aria-labelledby="reportBugModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="reportBugModalLabel">Report an issue</h5>
        <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
          X
        </button>
      </div>
      <div class="modal-body h-auto">
        <form name="bugForm" id="bugForm" enctype="multipart/form-data" method="post">
          <div class="form-group">
            <!-- <label for="bugDescription">Describe the bug</label> -->
            <textarea class="form-control" name="description" id="bugDescription"  rows="4" placeholder="Describe the issue..." required></textarea>
          </div>
          <?php if(!taoh_user_is_logged_in()) { ?>
          <div class="form-group">
            <input type="email" class="form-control" name="bugemail" id="bugemail"  rows="4" placeholder="Enter email" required>
          </div>
          <div class="form-group">
            <label for="" class="text-label-md  mb-1">Let us know you're human</label>
            <div style="align-items: center;  background-color: #fafafa;  border: 1px solid #e0e0e0;
                box-sizing: border-box;
                display: flex;
                gap: 7px;
                height: 45px;
                user-select: none;">
                <br><input onclick="checkReportHumanCheckbox();" type="checkbox" id="human_report" name="human_report" value="human"> 
                <label class="mb-0" for="human_report" id="verify_label" style="font-size: 16px;">Verify you're human</label>
            </div>
          </div>
          <?php } ?>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" id="bugsubmit" <?php if(!taoh_user_is_logged_in()) { ?> disabled="true" <?php } ?> class="btn btn-primary"><i></i>Submit Report</button>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="indexedDBWarningModal" tabindex="-1" role="dialog" aria-labelledby="indexedDBWarningModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="indexedDBWarningModalLabel">Warning</h5>
        </div>
        <div class="modal-body">
            <p>Trouble Viewing the Page? Please refresh or try a different browser for a better experience. We apologize for the inconvenience!</p>
        </div>
    </div>
    </div>
</div>

<!-- jobPostModal Modal -->
<div class="modal fade post-option" id="jobPostModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog bg-white mx-auto" role="document" style="width: 95%; max-width: 800px;">
        <div class="modal-content">
            <div class="modal-header bg-white justify-content-end" style="border: none;">
                <button type="button" class="btn" data-dismiss="modal" aria-label="Close">
                    <svg width="9" height="9" viewBox="0 0 9 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.73364 1.53701C9.08504 1.18562 9.08504 0.614946 8.73364 0.263548C8.38224 -0.0878494 7.81157 -0.0878494 7.46017 0.263548L4.5 3.22653L1.53701 0.266359C1.18562 -0.0850383 0.614946 -0.0850383 0.263548 0.266359C-0.0878494 0.617757 -0.0878494 1.18843 0.263548 1.53982L3.22653 4.5L0.26636 7.46299C-0.0850382 7.81438 -0.0850382 8.38505 0.26636 8.73645C0.617757 9.08785 1.18843 9.08785 1.53982 8.73645L4.5 5.77346L7.46299 8.73364C7.81438 9.08504 8.38505 9.08504 8.73645 8.73364C9.08785 8.38224 9.08785 7.81157 8.73645 7.46017L5.77347 4.5L8.73364 1.53701Z" fill="black"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body d-flex flex-wrap justify-content-center align-items-start" style="gap: 24px;">
                <div class="d-flex justify-content-between w-100">
                    <div>
                        <svg style="width: 100%; max-width: 202px" viewBox="0 0 202 159" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M32.4723 121.112C35.3675 116.679 40.3842 112.91 43.6772 109.461C48.9127 103.913 53.9112 98.124 58.6845 92.1299C64.0985 85.2929 69.1805 78.166 73.9176 70.833C76.2565 67.1364 79.3113 62.2149 78.8612 57.6334C80.3176 57.8373 81.6746 58.4707 82.8952 59.6645C85.4466 63.1289 84.8379 67.1395 83.1547 71.0211C80.6299 76.8436 76.4478 82.2094 72.7818 87.3287C68.6557 93.1133 64.3277 98.7646 59.7627 104.175C54.485 110.451 48.9228 116.463 43.1236 122.233C40.2177 125.159 36.948 128.681 36.185 132.582L32.4723 121.112Z" fill="url(#paint0_linear_122_2)"/>
                            <path d="M142.95 33.8061C143.903 32.3469 145.554 31.1062 146.638 29.9707C148.362 28.1444 150.007 26.2387 151.578 24.2655C153.361 22.0148 155.034 19.6687 156.593 17.2547C157.363 16.0378 158.369 14.4177 158.22 12.9095C158.7 12.9767 159.147 13.1852 159.548 13.5782C160.388 14.7186 160.188 16.0389 159.634 17.3167C158.803 19.2334 157.426 20.9997 156.219 22.685C154.861 24.5892 153.436 26.4496 151.933 28.2306C150.196 30.2968 148.365 32.2756 146.456 34.1751C145.499 35.1386 144.423 36.2977 144.172 37.5821L142.95 33.8061Z" fill="url(#paint1_linear_122_2)"/>
                            <path d="M82.4921 59.1586C82.6106 59.2792 82.6931 59.4114 82.764 59.5076C82.8232 59.5679 82.8348 59.6038 82.8941 59.6641C81.6734 58.4702 80.3164 57.8369 78.8601 57.633C75.6015 57.1385 72.0006 58.8998 69.1749 60.8473C63.2 64.8469 57.7889 69.9749 52.2275 74.5159C45.6668 79.8968 33.1677 93.3174 24.0712 85.2184C22.4105 83.7697 20.7287 82.01 19.2383 80.2282L15.1532 67.6072C15.5802 65.2445 16.4852 62.8859 17.5795 60.7441C22.197 52.0594 32.7633 42.363 28.981 32.0275C26.8371 31.1721 24.4955 31.0561 21.9626 30.9623C18.2345 30.8581 14.6174 30.3604 11.0415 29.2536C7.54912 28.1594 4.25234 26.565 1.22203 24.5665L0 20.791C2.58284 22.0207 5.28727 23.0124 8.04243 23.6698C13.3624 24.8875 20.4153 23.3594 25.0491 26.5073C27.9123 28.4804 30.6911 31.9109 32.7707 34.6541C36.0436 38.9972 36.0634 44.9494 34.1085 49.8327C30.296 59.4088 17.4178 69.6946 21.2785 80.6404C29.9259 84.0384 39.7328 73.7138 45.824 68.7232C51.2288 64.3122 56.457 59.601 62.0629 55.4428C65.3951 52.9739 70.1539 50.0035 74.467 51.547C77.6601 52.6983 80.4104 56.6545 82.4921 59.1586Z" fill="url(#paint2_linear_122_2)"/>
                            <path d="M159.417 13.4116C159.456 13.4513 159.484 13.4948 159.507 13.5265C159.526 13.5463 159.53 13.5581 159.55 13.578C159.148 13.185 158.701 12.9765 158.222 12.9093C157.149 12.7465 155.964 13.3264 155.034 13.9675C153.067 15.2841 151.285 16.9722 149.455 18.4671C147.295 20.2384 143.18 24.6564 140.186 21.9902C139.639 21.5133 139.085 20.9341 138.595 20.3475L137.25 16.1928C137.391 15.415 137.689 14.6386 138.049 13.9335C139.569 11.0746 143.047 7.88257 141.802 4.4802C141.096 4.19864 140.325 4.16044 139.492 4.12956C138.264 4.09526 137.074 3.93143 135.896 3.56707C134.747 3.20688 133.662 2.682 132.664 2.02412L132.262 0.78125C133.112 1.18604 134.002 1.5125 134.909 1.72893C136.661 2.12978 138.982 1.62673 140.508 2.663C141.45 3.31253 142.365 4.44183 143.05 5.34486C144.127 6.77459 144.134 8.73402 143.49 10.3415C142.235 13.4939 137.996 16.8799 139.266 20.4832C142.113 21.6018 145.341 18.203 147.347 16.5602C149.126 15.1081 150.847 13.5572 152.692 12.1883C153.789 11.3756 155.356 10.3978 156.776 10.9059C157.827 11.2849 158.732 12.5872 159.417 13.4116Z" fill="url(#paint3_linear_122_2)"/>
                            <path d="M158.337 98.4629C158.563 98.3873 158.79 98.2739 159.017 98.1983C159.017 98.2361 158.979 98.2361 158.979 98.2361C159.13 98.1983 159.281 98.1228 159.395 98.085C159.395 98.1983 159.357 98.3117 159.281 98.4629C159.206 98.6141 158.866 98.8408 158.526 99.0676C158.677 99.0298 158.828 99.0298 158.941 98.992C158.904 99.0298 158.866 99.0676 158.866 99.0676C154.179 102.998 155.313 109.083 157.052 114.185C159.433 121.177 162.154 128.055 164.686 135.009C166.765 140.641 170.015 147.33 167.407 153.339C166.311 155.872 164.421 157.27 162.267 158.253C160.793 158.631 159.319 158.668 157.921 158.442C162.381 153.113 158.677 143.891 156.485 137.882C154.671 133.006 152.894 128.131 151.08 123.256C149.568 119.136 147.414 114.714 147.225 110.255C146.961 103.792 151.345 100.957 156.749 99.0298C157.241 98.8408 157.694 98.6897 158.185 98.5007C158.223 98.5007 158.299 98.4629 158.337 98.4629ZM156.334 99.3322L156.409 99.2944C156.371 99.3322 156.334 99.3322 156.334 99.3322Z" fill="url(#paint4_linear_122_2)"/>
                            <path d="M200.211 107.005C202.366 107.421 200.551 106.929 200.589 106.929C200.06 106.967 197.944 106.891 197.641 106.854C195.411 106.74 193.219 106.173 191.141 105.417C184.489 102.961 178.707 98.0099 171.639 96.6871C169.334 96.2714 167.18 96.3092 165.063 96.6493H165.025C163.589 96.8383 161.095 97.5186 160.641 97.6698C160.566 97.6698 160.566 97.6698 160.566 97.6698C158.411 98.2745 154.821 99.7862 154.821 99.7862C155.426 99.4839 157.164 99.3327 158.147 99.2949C158.147 99.2949 158.147 99.2949 158.109 99.2949C158.487 99.2571 158.789 99.2571 158.865 99.2571C161.321 99.1815 163.778 99.5594 166.121 100.315C176.59 103.717 184.98 112.523 196.734 108.706C197.15 108.592 197.566 108.441 197.981 108.328C198.284 107.798 199.644 106.929 200.211 107.005Z" fill="url(#paint5_linear_122_2)"/>
                            <defs>
                            <linearGradient id="paint0_linear_122_2" x1="34.0033" y1="117.484" x2="84.5346" y2="71.5974" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#F69CD2"/>
                            <stop offset="0.980392" stop-color="#BC1558"/>
                            <stop offset="1" stop-color="#BC1558"/>
                            </linearGradient>
                            <linearGradient id="paint1_linear_122_2" x1="143.454" y1="32.6119" x2="160.088" y2="17.5064" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#BC1558"/>
                            <stop offset="0.980392" stop-color="#BC1558"/>
                            <stop offset="1" stop-color="#BC1558"/>
                            </linearGradient>
                            <linearGradient id="paint2_linear_122_2" x1="20.1901" y1="66.4139" x2="66.147" y2="24.6812" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#F69CD2"/>
                            <stop offset="0.0117647" stop-color="#F599D0"/>
                            <stop offset="1" stop-color="#BC1558"/>
                            </linearGradient>
                            <linearGradient id="paint3_linear_122_2" x1="138.908" y1="15.7999" x2="154.037" y2="2.06188" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#F599D0"/>
                            <stop offset="0.0117647" stop-color="#F599D0"/>
                            <stop offset="1" stop-color="#BC1558"/>
                            </linearGradient>
                            <linearGradient id="paint4_linear_122_2" x1="168.377" y1="128.334" x2="147.212" y2="128.334" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#1897C9"/>
                            <stop offset="0.160784" stop-color="#1897C9"/>
                            <stop offset="0.329412" stop-color="#18B7C9"/>
                            <stop offset="0.819608" stop-color="#19D8C9"/>
                            <stop offset="1" stop-color="#19D8C9"/>
                            </linearGradient>
                            <linearGradient id="paint5_linear_122_2" x1="165.744" y1="98.7272" x2="190.338" y2="107.315" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#1897C9"/>
                            <stop offset="0.160784" stop-color="#1897C9"/>
                            <stop offset="0.490196" stop-color="#18B7C9"/>
                            <stop offset="0.909804" stop-color="#19D8C9"/>
                            <stop offset="1" stop-color="#19D8C9"/>
                            </linearGradient>
                            </defs>
                        </svg>
                    </div>
                    <div>
                        <svg  style="width: 100%; max-width: 149px" viewBox="0 0 149 152" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M146.916 83.0234C148.328 85.1918 149 87.6288 149 90.0371C149 94.3835 146.8 98.634 142.795 101.052C138.905 103.403 136.58 107.596 136.58 112.057C136.58 112.604 136.619 113.16 136.686 113.717C136.763 114.273 136.801 114.83 136.801 115.377C136.801 121.911 131.825 127.533 125.15 128.167C122.604 128.416 120.26 129.395 118.349 130.92C116.428 132.446 114.949 134.509 114.151 136.936C112.355 142.348 107.312 145.754 101.924 145.754C100.444 145.754 98.9363 145.495 97.4667 144.957C96.0162 144.42 94.5082 144.161 93.0193 144.161C89.5902 144.161 86.2283 145.533 83.7597 148.085C81.2238 150.695 77.8619 152 74.5 152C71.1381 152 67.7762 150.695 65.2403 148.085C62.7717 145.533 59.4098 144.161 55.9807 144.161C54.4918 144.161 52.9838 144.42 51.5333 144.957C50.0637 145.495 48.5556 145.754 47.0764 145.754C41.6877 145.754 36.6449 142.348 34.8486 136.936C34.0514 134.509 32.5721 132.446 30.651 130.92C28.7396 129.395 26.3958 128.416 23.8504 128.167C17.1746 127.533 12.1989 121.911 12.1989 115.377C12.1989 114.83 12.2374 114.273 12.3142 113.717C12.3814 113.16 12.4199 112.604 12.4199 112.057C12.4199 107.596 10.0953 103.403 6.20513 101.052C2.19965 98.634 0 94.3835 0 90.0371C0 87.6288 0.672383 85.1918 2.08439 83.0234C3.47718 80.8837 4.17838 78.4467 4.17838 76C4.17838 73.5533 3.47718 71.1067 2.08439 68.9766C0.672383 66.8082 0 64.3712 0 61.9629C0 57.6165 2.19965 53.366 6.20513 50.9481C10.0953 48.5878 12.4199 44.3949 12.4199 39.9429C12.4199 39.396 12.3814 38.8395 12.3142 38.283C12.2374 37.7265 12.1989 37.1701 12.1989 36.6136C12.1989 30.0891 17.1746 24.4666 23.8504 23.8238C26.3958 23.5839 28.7396 22.6052 30.651 21.0797C32.5721 19.5541 34.0514 17.4912 34.8486 15.0638C36.6449 9.65232 41.6877 6.24618 47.0764 6.24618C48.5556 6.24618 50.0637 6.50524 51.5333 7.04254C52.9838 7.57985 54.4918 7.83891 55.9807 7.83891C59.4098 7.83891 62.7717 6.46686 65.2403 3.91466C67.7762 1.30489 71.1381 0 74.5 0C77.8619 0 81.2238 1.30489 83.7597 3.91466C87.3041 7.57985 92.6831 8.80798 97.4667 7.04254C98.9363 6.50524 100.444 6.24618 101.924 6.24618C107.312 6.24618 112.355 9.65232 114.151 15.0638C114.949 17.4912 116.428 19.5541 118.349 21.0797C120.26 22.6052 122.604 23.5839 125.15 23.8238C131.825 24.4666 136.801 30.0891 136.801 36.6136C136.801 37.1701 136.763 37.7265 136.686 38.283C136.619 38.8395 136.58 39.396 136.58 39.9429C136.58 44.3949 138.905 48.5878 142.795 50.9481C146.8 53.366 149 57.6165 149 61.9629C149 64.3712 148.328 66.8082 146.916 68.9766C145.523 71.1067 144.822 73.5533 144.822 76C144.822 78.4467 145.523 80.8837 146.916 83.0234Z" fill="url(#paint0_linear_5452_2308)"/>
                            <path class="checkmarksvg" d="M63.4662 109L38 83.6334L48.9266 72.7495L62.2684 86.0391L99.0021 41L111 50.7101L63.4662 109Z" fill="#FEFEFE"/>
                            <defs>
                                <linearGradient id="paint0_linear_5452_2308" x1="31.9003" y1="34.3338" x2="117.007" y2="117.761" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#59BDB5"/>
                                <stop offset="1" stop-color="#37509C"/>
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                    <div>
                        <svg style="width: 100%; max-width: 202px" viewBox="0 0 202 158" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M168.795 37.4702C165.9 41.903 160.883 45.6719 157.59 49.1212C152.355 54.6691 147.356 60.458 142.583 66.4521C137.169 73.2892 132.087 80.416 127.35 87.749C125.011 91.4457 121.956 96.3671 122.406 100.949C120.95 100.745 119.593 100.111 118.372 98.9175C115.821 95.4531 116.43 91.4426 118.113 87.5609C120.638 81.7385 124.82 76.3727 128.486 71.2534C132.612 65.4688 136.94 59.8174 141.505 54.4071C146.783 48.1306 152.345 42.1195 158.144 36.3495C161.05 33.4227 164.32 29.9015 165.083 25.9999L168.795 37.4702Z" fill="url(#paint0_linear_122_2)"/>
                            <path d="M58.318 124.776C57.365 126.235 55.7135 127.476 54.6295 128.611C52.906 130.438 51.2605 132.343 49.6892 134.317C47.9069 136.567 46.234 138.913 44.6746 141.327C43.9046 142.544 42.899 144.164 43.0472 145.672C42.5677 145.605 42.121 145.397 41.7192 145.004C40.8793 143.863 41.0797 142.543 41.6338 141.265C42.4649 139.349 43.8416 137.582 45.0485 135.897C46.4067 133.993 47.8315 132.132 49.3342 130.351C51.0716 128.285 52.9027 126.306 54.8117 124.407C55.7683 123.443 56.8447 122.284 57.0959 121L58.318 124.776Z" fill="url(#paint1_linear_122_2)"/>
                            <path d="M118.775 99.4234C118.657 99.3028 118.574 99.1706 118.504 99.0744C118.444 99.0141 118.433 98.9782 118.374 98.9179C119.594 100.112 120.951 100.745 122.407 100.949C125.666 101.444 129.267 99.6822 132.093 97.7348C138.068 93.7352 143.479 88.6072 149.04 84.0661C155.601 78.6852 168.1 65.2646 177.196 73.3637C178.857 74.8124 180.539 76.572 182.029 78.3539L186.114 90.9748C185.687 93.3376 184.782 95.6962 183.688 97.838C179.071 106.523 168.504 116.219 172.287 126.555C174.43 127.41 176.772 127.526 179.305 127.62C183.033 127.724 186.65 128.222 190.226 129.328C193.718 130.423 197.015 132.017 200.046 134.016L201.268 137.791C198.685 136.561 195.98 135.57 193.225 134.912C187.905 133.695 180.852 135.223 176.218 132.075C173.355 130.102 170.576 126.671 168.497 123.928C165.224 119.585 165.204 113.633 167.159 108.749C170.972 99.1733 183.85 88.8874 179.989 77.9417C171.342 74.5437 161.535 84.8683 155.444 89.8589C150.039 94.2698 144.811 98.9811 139.205 103.139C135.873 105.608 131.114 108.579 126.801 107.035C123.607 105.884 120.857 101.928 118.775 99.4234Z" fill="url(#paint2_linear_122_2)"/>
                            <path d="M41.8501 145.17C41.8111 145.131 41.7839 145.087 41.7606 145.056C41.7411 145.036 41.7373 145.024 41.7178 145.004C42.1196 145.397 42.5663 145.606 43.0457 145.673C44.1184 145.835 45.3038 145.256 46.234 144.615C48.2009 143.298 49.9822 141.61 51.813 140.115C53.9727 138.344 58.0873 133.926 61.0818 136.592C61.6285 137.069 62.1821 137.648 62.6728 138.235L64.0175 142.389C63.877 143.167 63.5791 143.943 63.2188 144.649C61.6988 147.507 58.2204 150.699 59.4655 154.102C60.1713 154.383 60.9421 154.422 61.776 154.452C63.0032 154.487 64.1939 154.651 65.3711 155.015C66.5207 155.375 67.606 155.9 68.6036 156.558L69.0059 157.801C68.1556 157.396 67.2653 157.07 66.3584 156.853C64.6071 156.452 62.2853 156.955 60.7599 155.919C59.8173 155.27 58.9026 154.14 58.218 153.237C57.1406 151.807 57.1341 149.848 57.7776 148.24C59.0326 145.088 63.272 141.702 62.0011 138.099C59.1545 136.98 55.9261 140.379 53.921 142.022C52.1417 143.474 50.4206 145.025 48.5752 146.394C47.4783 147.206 45.9117 148.184 44.4919 147.676C43.4407 147.297 42.5354 145.995 41.8501 145.17Z" fill="url(#paint3_linear_122_2)"/>
                            <path d="M42.931 60.1191C42.7042 60.1947 42.4775 60.3081 42.2507 60.3837C42.2507 60.3459 42.2885 60.3459 42.2885 60.3459C42.1373 60.3837 41.9861 60.4593 41.8728 60.4971C41.8728 60.3837 41.9106 60.2703 41.9861 60.1191C42.0617 59.968 42.4019 59.7412 42.742 59.5144C42.5908 59.5522 42.4397 59.5522 42.3263 59.59C42.3641 59.5522 42.4019 59.5144 42.4019 59.5144C47.0883 55.5839 45.9545 49.4991 44.216 44.3969C41.835 37.4051 39.1138 30.5266 36.5816 23.5726C34.503 17.9413 31.2527 11.2518 33.8605 5.2426C34.9565 2.71041 36.8462 1.31204 39.0004 0.329407C40.4744 -0.0485306 41.9483 -0.0863266 43.3467 0.140438C38.8871 5.46936 42.5908 14.691 44.7829 20.7002C46.597 25.5756 48.3733 30.451 50.1874 35.3264C51.6991 39.4459 53.8534 43.8678 54.0423 48.3275C54.3069 54.7902 49.9228 57.6247 44.5183 59.5522C44.027 59.7412 43.5735 59.8924 43.0822 60.0813C43.0444 60.0813 42.9688 60.1191 42.931 60.1191ZM44.9341 59.2499L44.8585 59.2877C44.8963 59.2499 44.9341 59.2499 44.9341 59.2499Z" fill="url(#paint4_linear_122_2)"/>
                            <path d="M1.05631 51.5772C-1.09793 51.1615 0.716167 51.6528 0.678374 51.6528C1.20749 51.615 3.32394 51.6906 3.62629 51.7284C5.85612 51.8418 8.04816 52.4087 10.1268 53.1646C16.7785 55.6212 22.561 60.5721 29.6284 61.8949C31.9338 62.3106 34.0881 62.2729 36.2045 61.9327H36.2423C37.6785 61.7437 40.1729 61.0635 40.6264 60.9123C40.702 60.9123 40.702 60.9123 40.702 60.9123C42.8562 60.3076 46.4466 58.7958 46.4466 58.7958C45.8419 59.0982 44.1034 59.2494 43.1208 59.2871C43.1208 59.2871 43.1208 59.2871 43.1586 59.2871C42.7806 59.3249 42.4783 59.3249 42.4027 59.3249C39.9461 59.4005 37.4895 59.0226 35.1463 58.2667C24.6774 54.8653 16.2872 46.0593 4.53334 49.8765C4.11761 49.9899 3.70187 50.1411 3.28614 50.2544C2.98379 50.7836 1.62322 51.6528 1.05631 51.5772Z" fill="url(#paint5_linear_122_2)"/>
                            <defs>
                            <linearGradient id="paint0_linear_122_2" x1="167.264" y1="41.098" x2="116.733" y2="86.9846" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#F69CD2"/>
                            <stop offset="0.980392" stop-color="#BC1558"/>
                            <stop offset="1" stop-color="#BC1558"/>
                            </linearGradient>
                            <linearGradient id="paint1_linear_122_2" x1="57.814" y1="125.97" x2="41.1795" y2="141.076" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#BC1558"/>
                            <stop offset="0.980392" stop-color="#BC1558"/>
                            <stop offset="1" stop-color="#BC1558"/>
                            </linearGradient>
                            <linearGradient id="paint2_linear_122_2" x1="181.077" y1="92.1682" x2="135.121" y2="133.901" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#F69CD2"/>
                            <stop offset="0.0117647" stop-color="#F599D0"/>
                            <stop offset="1" stop-color="#BC1558"/>
                            </linearGradient>
                            <linearGradient id="paint3_linear_122_2" x1="62.3594" y1="142.782" x2="47.2308" y2="156.52" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#F599D0"/>
                            <stop offset="0.0117647" stop-color="#F599D0"/>
                            <stop offset="1" stop-color="#BC1558"/>
                            </linearGradient>
                            <linearGradient id="paint4_linear_122_2" x1="32.8907" y1="30.2481" x2="54.056" y2="30.2481" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#1897C9"/>
                            <stop offset="0.160784" stop-color="#1897C9"/>
                            <stop offset="0.329412" stop-color="#18B7C9"/>
                            <stop offset="0.819608" stop-color="#19D8C9"/>
                            <stop offset="1" stop-color="#19D8C9"/>
                            </linearGradient>
                            <linearGradient id="paint5_linear_122_2" x1="35.5238" y1="59.8548" x2="10.9292" y2="51.2673" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#1897C9"/>
                            <stop offset="0.160784" stop-color="#1897C9"/>
                            <stop offset="0.490196" stop-color="#18B7C9"/>
                            <stop offset="0.909804" stop-color="#19D8C9"/>
                            <stop offset="1" stop-color="#19D8C9"/>
                            </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
                <div class="d-flex flex-column align-items-center mb-4 text-center">
                    <h6 class="setting-sm-text mb-4">Thanks! Your Profile Settings is now complete!</h6>
                    <h3 class="setting-lg-text mb-2">Actively Hiring? Find Top Talent Here!</h3>
                    <h5 class="setting-md-text mb-4">Post a free Job and get a Hiring badge </h5>
                    <button type="button" class="btn s-btn setting-post-btn" id="postJobButton">Post a Free Job</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(!taoh_user_is_logged_in()) { ?>
    <div class="modal top fade" id="config-modal" tabindex="" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
         <div class="vertical-alignment-helper">
            <div class="modal-dialog modal-lg vertical-align-center" role="document">
                <div class="modal-content log-in-modal-content">
                <div class="modal-header blue_bg">
                    <h4 class="modal-title">Login</h4>
                    <button type="button" class="close" style="padding:0;margin:0;" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body p-0">
                    <div class="main-box p-0" style="border: 0;">
                    <?php 
                        $login = 0;
                        include_once(TAOH_SITE_DOC_ROOT.'/core/new_login.php');
                    ?>
                    </div>
                </div>
                <!-- <div class="modal-footer">
                    
                </div> -->
                </div>
            </div>
        </div>
    </div>
<?php } ?>

</div><!-- end class="wrapper" -->

<!-- dojo popup starts -->
<div id="goalModal">
  <div id="goalModalContent">
     <span id="closeGoalModal" class="close-btn">&times;</span>
    <h2>What are you planning to achieve?</h2>

    <div class="goal-option">
      <label><input type="radio" name="user_goal" value="Grow">Grow – Learn, SideKick, HAPI</label>
    </div>
    <div class="goal-option">
      <label><input type="radio" name="user_goal" value="Hire">Hire – Jobs, Marketplace</label>
    </div>
    <div class="goal-option">
      <label><input type="radio" name="user_goal" value="Work">Work – Matches, Coaching</label>
    </div>
    <div class="goal-option">
      <label><input type="radio" name="user_goal" value="Connect">Connect – Events, Networking</label>
    </div>   

    <button id="submitGoalBtn">Submit</button>
  </div>
</div>
<!-- dojo popup--ends -->

<!-- Contact host popup - start -->

<div class="modal fade" id="contactusModal" tabindex="-1" role="dialog" aria-labelledby=contactusModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="contactusModalLabel">Contact Us</h5>
        <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
          X
        </button>
      </div>
      <div class="modal-body h-auto">
        <form name="contactusForm" id="contactusForm" enctype="multipart/form-data" method="post">
            <div class="form-group">
                <input type="hidden" name="contact_email" id="contact_email" value="">
                <input type="hidden" name="contact_addtitle" id="contact_addtitle" value="">
                <input type="text" class="form-control" name="title" id="title" placeholder="Title" required>
            </div>
            <div class="form-group">
            <textarea class="form-control" name="description" id="description"  rows="4" placeholder="Describe here..." required></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" id="contactusSubmit" class="btn btn-primary"><i></i>Submit Report</button>
      </div>
    </div>
  </div>
</div>
<!-- Contact host popup - end -->

<style>
    @keyframes highlight-green {
        0% {
            background: #3ee632;
        }
        100% {
            background: #ffff99;
        }
    }

    .highlight-green {
        animation: highlight-green 10s;
    }

/* use specific modal class if needed
   .modal-lg {
    max-width: 80% !important;
    display: table-cell;
    vertical-align: middle;
}*/

    .vertical-alignment-helper {
    display:table;
    height: 100%;
    pointer-events:none;
    margin: auto;
}

    .vertical-align-center {
    /* To center vertically */
    display: table-cell;
    vertical-align: middle;
    pointer-events:none;
}
.close-btn {
    position: absolute;
    top: 12px;
    right: 16px;
    font-size: 22px;
    font-weight: bold;
    cursor: pointer;
    color: #888;
  }

  .close-btn:hover {
    color: #000;
  }

  #goalModalContent {
    position: relative; /* So close button positions inside */
  }

/*    .modal-content {
    !* Bootstrap sets the size of the modal in the modal-dialog class, we need to inherit it *!
    width:inherit;
    max-width:inherit; !* For Bootstrap 4 - to avoid the modal window stretching
    full width *!
    height:inherit;
    !* To center horizontally *!
    margin: 0 auto;
    pointer-events:all;}*/
</style>
<?php
 $session_data = taoh_session_get(TAOH_ROOT_PATH_HASH);
if (defined('TAOH_CUSTOM_FOOT')) {
    echo TAOH_CUSTOM_FOOT;

    var_dump(get_defined_vars());
}

if (defined('TAOH_SITE_GA_ENABLE') && TAOH_SITE_GA_ENABLE) {
    if (defined('TAOH_GA_CODE')) {
        echo TAOH_GA_CODE;
    } else {
        ?>
        <!-- Google tag (gtag.js) -->
        <script async
                src="https://www.googletagmanager.com/gtag/js?id=<?php echo (defined('TAOH_PAGE_GA') && TAOH_PAGE_GA) ? TAOH_PAGE_GA : TAOH_SITE_GA; ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());

            gtag('config', '<?php  echo (defined('TAOH_PAGE_GA') && TAOH_PAGE_GA) ? TAOH_PAGE_GA : TAOH_SITE_GA; ?>');
        </script>
    <?php }
}

if (!@$_COOKIE['client_time_zone']) { ?>
    <script>
        var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        document.cookie = "client_time_zone=" + timezone;
    </script>
<?php } ?>
<?php echo @$hook;
// <script src="https://unmeta.net/script/js/play3/main.js"></script>
?>

<script src="<?php echo TAOH_CDN_JS_PREFIX; ?>/basic-settings.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
<script src="<?php echo TAOH_CDN_JS_PREFIX; ?>/footer.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
<script src="<?php echo TAOH_CDN_JS_PREFIX; ?>/dojo.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>

<!-- Intro.js -->
<script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script type="text/javascript">
    const ft_isLoggedIn = <?= json_encode(taoh_user_is_logged_in() ?? false); ?>;

    // Get the elements
   
    if(document.getElementById('termsLink')){
         const termsLink = document.getElementById('termsLink');
        const termsList = document.getElementById('termsList');
        const termItems = document.querySelectorAll('.term-item');

        // Toggle visibility of the terms list when the anchor is clicked
        termsLink.addEventListener('click', (event) => {
            event.preventDefault(); // Prevent default anchor behavior
            termsList.style.display = (termsList.style.display === 'none' || termsList.style.display === '') ? 'block' : 'none';
        });

        // Hide the terms list when any of the list items is clicked
        termItems.forEach(item => {
            item.addEventListener('click', () => {
                termsList.style.display = 'none'; // Hide the list
            });
        });
    }


    var loopTime = '<?php echo TAOH_NOTIFICATION_LOOP_TIME_INTERVAL;?>';

    let profileRSLiveStatusInterval;

    $(document).ready(function () {
        // Set a threshold for maximum load time (e.g., 15000 milliseconds = 15 seconds)
        //const loadTimeThreshold = <?php //echo TAOH_HEALTH_TIMEOUT; ?>// * 1000;

        // Set a timeout function that redirects the user if the page takes too long to load
        /*const timeoutHandle = setTimeout(function() {   // Redirect the user to a different page if the load time exceeds the threshold
            window.location.href = '<?php //echo TAOH_SITE_URL_ROOT."/down.html";  ?>';
        }, loadTimeThreshold);

        // Listen for the window's 'load' event
        window.addEventListener('load', function() {  // If the page loads within the threshold, clear the timeout to prevent redirection
            clearTimeout(timeoutHandle);
        });*/

        // var loadTime = window.performance.timing.domContentLoadedEventEnd - window.performance.timing.navigationStart;
        // console.log('Load Time ------------', loadTime);
        // console.log(loadTime + " <= " + loadTimeThreshold);
        //if(loadTime >= loadTimeThreshold){
        //window.location.href = '<?php //echo TAOH_SITE_URL_ROOT."/down.php";  ?>';
        //}
        
        <?php if($curr_page != 'login' && $curr_page != 'createacc' && taoh_user_is_logged_in()){ ?>
            //checkReferralStatus();
            <?php if(!taoh_user_is_logged_in() ) { ?>
            setInterval(function () {
                checkReferralStatus();

            }, 60000);
            <?php } ?>
        <?php } ?>

        <?php if(taoh_user_is_logged_in() ) { ?>
            setInterval(function () {
                moveMetricstoRedis();
            }, 10000);
            <?php } ?>


        <?php
        if(taoh_user_is_logged_in() ) {
            if(TAOH_NOTIFICATION_ENABLED && TAOH_NOTIFICATION_STATUS == 2){
                ?>
                setTimeout(function () {
                    taoh_notification_init(1);
                }, 3000);

                setInterval(function () {
                    taoh_notification_init(0);

                }, loopTime);
                <?php
            }

            ?>
            // checksitemap(); //commented on 20/5 to stop old sitemap file call

           <?php if($curr_page != 'settings'){ ?>
                setInterval(function () {
                    checkProfileCompletion();
                  
                }, 60000);

                
            <?php } ?>
            checksuperadminInit();
            setInterval(function () {
                    checksuperadminInit();
            }, 60000);

            savetaodata();
            setInterval(function () {
               // console.log('----savetaodata------------')
                if(typeof index_name !== 'undefined') checkTTL(index_name);
                savetaodata();
            }, 10000);

            <?php
        }
        ?>


    // Get the current URL
    var currentUrl = window.location.href;

    // Retrieve the list of previously accessed URLs from localStorage, or initialize an empty array
    var visitedUrls = JSON.parse(localStorage.getItem('visitedUrls')) || [];

    // Add the current URL to the list
    visitedUrls.push(currentUrl);

    // Keep only the last 5 URLs (if there are more than 5)
    if (visitedUrls.length > 5) {
        visitedUrls.shift(); // Remove the oldest URL if there are more than 5
    }

    // Store the updated list back to localStorage
    localStorage.setItem('visitedUrls', JSON.stringify(visitedUrls));

    $(document).on('click', '#bugsubmit', function(e) {
        e.preventDefault(); // Prevent form submission
        if($("#bugForm").valid()){
            const formData = new FormData($("#bugForm")[0]);
            formData.append('taoh_action', 'taoh_report_bug');
            var currentUrl = window.location.href;
            var visitedUrls = JSON.parse(localStorage.getItem('visitedUrls')) || [];
            console.log(visitedUrls);
            formData.append('visited_url', visitedUrls);
            formData.append('current_url', currentUrl);

            let submit_btn = $(this);
            submit_btn.prop('disabled', true);

            // let submit_btn_icon = submit_btn.find('i');
            // submit_btn_icon.removeClass('fa-play-circle-o').addClass('fa-spinner fa-spin');

            $.ajax({
                url: '<?php echo taoh_site_ajax_url(); ?>',
                type: 'post',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                cache: false,
                success: function (response) {
                    if(response.success){
                        $("#reportBugModal").modal("hide");
                        $("#reportBugModal").hide();
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        document.getElementById("bugForm").reset();
                        submit_btn.prop('disabled', false);
                        // $.alert(response.output);
                        taoh_set_success_message('<h5>Thanks!</h5>Issue report submitted successfully.', false);
                    }
                    // location.reload();
                },
                error: function (xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
    });

        $('#contactusModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // The button that triggered the modal
            var email = button.data('email'); // Extract info from data-* attributes
            $('#contact_email').val(email); // Update the hidden input field
            var addtitle = button.data('addtitle') || ''; // Extract info from data-* attributes
            $('#contact_addtitle').val(addtitle); // Update the hidden input field
        });

        $(document).on('click', '#contactusSubmit', async function(e) {
            e.preventDefault(); // Prevent form submission
            let to_email = $('#contact_email').val();
            if(to_email == ''){
                taoh_set_error_message('Error on sending email. Please check after sometime', false);
                return false;
            }else{
                if($("#contactusForm").valid()){
                    const formData = new FormData($("#contactusForm")[0]);
                    formData.append('taoh_action', 'taoh_contact_us');
                    formData.append('eventtoken', eventToken);
                    formData.append('to_email', to_email);
                    
                    let submit_btn = $(this);
                    submit_btn.prop('disabled', true);

                    $.ajax({
                        url: '<?php echo taoh_site_ajax_url(); ?>',
                        type: 'post',
                        data: formData,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function (response) {
                            if(response.success){
                                $("#contactusModal").modal("hide");
                                document.getElementById("contactusForm").reset();
                                submit_btn.prop('disabled', false);
                                taoh_set_success_message('Thanks! Mail sent successfully.', false);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.log(xhr.responseText);
                        }
                    });
                }
            }
        });
    });



    function checksitemap() {
        <?php
        $currentDate = date("Ymd");
        $filename = "sitemap_" . $currentDate . ".sitemap";
        if(!file_exists($filename)){
        //$myfile = fopen($filename, "a") or die("Unable to open file!");
        ?>
        jQuery.get("<?php echo TAOH_SITE_URL_ROOT . '/sitemap'; ?>", {
            'taoh_action': 'taoh_sitemap_call',
        }, function (response) {
            res = response;
            //render_events_template(res, eventArea);
        }).fail(function () {
            console.log("Network issue!");
        })
        <?php
        }  ?>
    }

    function checkProfileCompletion(){
        <?php if(isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->profile_complete)  &&
        taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->profile_complete == 0 &&
        isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->fname) &&
        taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->fname == TAOH_SITE_NAME_SLUG){ ?>
            if (typeof showBasicSettingsModal === 'function') {
                const $completeModal = $('#completeSettingsModal');
                if ($completeModal.length === 0 || !$completeModal.hasClass('show')) {
                    showBasicSettingsModal();
                }
            }
             // $('#toast').toast('show');
             //    $('#toast').show();
             //    $("#toast").addClass("toast_active");
             //    var msg = "complete your settings to fully use the platform.";
             //    $("#toast_error").html("<div class='toasterror_class'><span><i class='las la-exclamation-circle info_icon'></i> "+msg+"&nbsp;<span class='toast_dismiss' aria-hidden='true'  data-dismiss='toast' aria-label='Close'>&times;</span></span></div>");
             //    setTimeout(function () {
             //        $("#toast").removeClass("toast_active");
             //    }, 8000);
        <?php } ?>       
    }

    function checksuperadminInit(){
        <?php if(isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->is_super_admin) && 
        taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->is_super_admin == 1 && 
        taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->site_status == 'init'){ ?>
            var msg = 'Please complete your site settings. Click Manage Button on the header menu and proceed to fill the site data.';
            taoh_set_error_message(msg,8000);
        <?php } ?>       
    }

    function checkReferralStatus(){
        <?php  if(isset($_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'refer_token']) && $_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'refer_token']!=''
                 && isset($_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'referral_back_url']) && $_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'referral_back_url'] != '')
             { ?>
                $('#toast').toast('show');
                $('#toast').show();
                $("#toast").addClass("toast_active");
                var msg = "Sorry, You haven't logged in the site. You will be redirecting in few secs.";
                $("#toast_error").html("<div class='toasterror_class'><span><i class='las la-exclamation-circle info_icon'></i> "+msg+"&nbsp;<span class='toast_dismiss' aria-hidden='true'  data-dismiss='toast' aria-label='Close'>&times;</span></span></div>");
                //$("#loader").show();
                //$("#error_textmsg").html(msg);

                setTimeout(function () {

                    $("#toast").removeClass("toast_active");
                    $("#toast_container").removeClass("toast-middle-con");
                    $("#loader").hide();

                    //window.location.href = '<?php //echo TAOH_SITE_URL_ROOT.'/login' ?>';
                    localStorage.setItem('email', '');
                    $('#config-modal').modal({show:true});
                }, 8000);
        <?php } ?>

        /*var datas = {
            'taoh_action': 'taoh_check_referral_status',
        };

        jQuery.post("<?php echo taoh_site_ajax_url(); ?>", datas, function (response) {
            data = response;//JSON.parse(response);
            // alert(data);
            if (data == 0) {

                $('#toast').toast('show');
                $('#toast').show();
                $("#toast").addClass("toast_active");
                var msg = "Sorry, You haven't logged in the site. You will be redirecting in few secs.";
                $("#toast_error").html("<div class='toasterror_class'><span><i class='las la-exclamation-circle info_icon'></i> "+msg+"&nbsp;<span class='toast_dismiss' aria-hidden='true'  data-dismiss='toast' aria-label='Close'>&times;</span></span></div>");
                //$("#loader").show();
                //$("#error_textmsg").html(msg);
                    
                setTimeout(function () {

                    $("#toast").removeClass("toast_active");
                    $("#loader").hide();
                    
                    //window.location.href = '<?php //echo TAOH_SITE_URL_ROOT.'/login' ?>';
                    $('#config-modal').modal({show:true});
                }, 8000);
                

            } else {

            }


        });*/
    }

    function updateStatus(process) {
        <?php  if(taoh_user_is_logged_in() ) { ?>

        $('#userMenuDropdownarea').addClass('stay_open');

        var my_status = $('#my_status').val();

        if (my_status == '') {
            return false;
        }
        if (process == 0) {
            $('#my_status').val('');
            my_status = '';
        }
        if (my_status != '') { //add
            $('#status_save').hide();
            $('#status_remove').show();
        } else { //remove
            $('#status_save').show();
            $('#status_remove').hide();
        }
        var data = {
            'taoh_action': 'taoh_update_status',
            "process": process,
            "my_status": my_status,
            "ptoken": "<?php echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>",
        };

        jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function (response) {
            data = response;//JSON.parse(response);
            if (process) {


            } else {

            }


        });

        setTimeout(function () {
            $('#userMenuDropdownarea').removeClass('stay_open');
        }, 5000);


        <?php } ?>
    }

    function taoh_counter_init(call_at) {

        <?php  if(taoh_user_is_logged_in() && TAOH_NOTIFICATION_ENABLED ) { ?>
        //alert('----2------');
        $('#badge_count').hide();
        $('#badge_count').html('');
        $('.notification_row').removeClass('bold');
        <?php  if ( taoh_user_is_logged_in() ) {  ?>
        var data = {
            'taoh_action': 'taoh_get_notification_counter',
            'mod': 'core',
            'ops': "get",
            "type": "notify",
            "token": "<?php echo TAOH_API_TOKEN; ?>",
            "ptoken": "<?php echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>",
            "call_at": call_at

        };
        jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function (response) {
            data = response;//JSON.parse(response);
            if (data.status) {
                if (data.total_num > 0) {
                    $('#badge_count').show();
                    $('#badge_count').html(data.total_num);
                } else {
                    $('#badge_count').hide();
                    $('#badge_count').html('');
                }

            } else {
                $('#badge_count').hide();
                $('#badge_count').html('');
            }
        });
        <?php } ?>
        <?php } ?>
    }

    <?php  if ( taoh_user_is_logged_in() && TAOH_NOTIFICATION_ENABLED ) {  ?>
    function taoh_notification_init(call_from = 0) {
        // $('#loaderChat').show();
        /*var data = {
         'taoh_action': 'taoh_get_notification',
         'mod': 'core',
         'ops': "get",
         "type" : "notify",
         "token" : "<?php //echo TAOH_API_TOKEN; ?>",
     "ptoken" :  "<?php //echo $_SESSION[TAOH_ROOT_PATH_HASH]['USER_INFO']->ptoken; ?>",
     "call_from" : call_from
   };*/
        var data = {
            'taoh_action': 'taoh_get_notification',
            'mod': 'notify',
            'ops': "webnotify",
            "type": "notify",
            "token": "<?php echo TAOH_API_TOKEN; ?>",
            "ptoken": "<?php echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>",
            "call_from": 0, //call_from
        };
        jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function (response) {
            data = response; //JSON.parse(response);
            // console.log(data);
            if (data.status) {
                if (data.total_num > 0) {
                    $('#notifications-list').css('height', '250px');
                    render_notification_list_template(data.output, call_from);
                    if (!call_from) {
                        $('#badge_count').show();
                        //$('#badge_count').html(data.total_num);
                        var old = $('#badge_count').html();
                        if (old == '') old = 0;
                        var total = data.output.length + parseInt(old);
                        $('#badge_count').html(total);
                    }
                } else {
                    if (call_from) {
                        $('#notifications-list').html('<li class="no-result">No Result Found</li>');
                    }
                }
                if (data.total_num > 10) {
                    $('#notification_load_more').show();
                }
            } else {
                if (call_from) {
                    $('#badge_count').hide();
                    $('#notifications-list').html('<li class="no-result">No Result Found</li>');
                    $('#notification_load_more').hide();
                }
            }
            $('#loaderChat').hide();
            if (call_from) {
                taoh_counter_init(1);
            }

        });

    }

    function render_notification_list_template(data, call_from) {
        var notification_data = '';
        // console.log('------call_from------', call_from);
        var class_add = '';
        if (call_from == 0) {
            class_add = 'bold';
        }
        $.each(data, function (i, v) {

            notification_data += `<li class="notification_row ${class_add}">
            <div class="row m-2" style="font-size:12px;">
              <div class="col-lg-2" style="padding-left:2px;padding-right:2px;">
                <div class='bgimage ' style="">
                 <img width="50" class="lazy" src="https://opslogy.com/avatar/PNG/128/${v.avatar ? v.avatar : 'default'}.png" alt="avatar" style=""></div>

                </div>

                <div class="col-lg-8 fs-12" style="padding-left: 5px;">
                  <p><span>${v.title}</span><p>
                  <span>${v.message}</span>
               </div>
               <div class="col-lg-2 fs-12" style="padding:0px;margin:0px;">
                 <span class="notify_time">${v.timestamp}</span>
               </div>
            </div>
            <div class="dropdown-divider"></div>
          </li>`;


        });
        //<span><h3>${v.title}</h3></span>
        if (call_from == 1) {
            $('#notifications-list').html(notification_data);
        } else {
            $('#notifications-list').prepend(notification_data);
        }
    }
    <?php  }  ?>

    $(document).on('click','.media_share', function(event) {
		var click =  $(this).attr("data-click");
		var dataconttoken = $(this).attr("data-gconntoken");
		save_metrics('jobs','share',dataconttoken);
	});


    function taoh_metrix_ajax(app,arr_cont) {
        $.each(arr_cont, function(i, v){
            save_metrics(app,'view',v);
        });
    }

    function save_metrics(app,metrics_type,conttoken){
        var store_name = METIRCSStore;

        var metrics = {
            "conttoken": conttoken,
            "met_type" : app,
            "ptoken": '<?php echo isset($session_data['USER_INFO']) ? $session_data['USER_INFO']->ptoken : '' ; ?>',
            'met_action': metrics_type,
            'time': Date.now(),
            'secret':'<?php echo TAOH_API_SECRET; ?>',
            'type': 'metrics',
        }

        let metrics_setting_time = Date.now()+'_'+conttoken;
        //metrics_setting_time = metrics_setting_time.setMinutes(metrics_setting_time.getMilliseconds());

        let name = app+'_'+metrics_setting_time;
        var metrics_data = { taoh_data:name,values : metrics };

        obj_data = { [store_name]:metrics_data };
       // console.log('--1---store_name---------',store_name);
       // console.log('--1---metrics_data---------',metrics_data);
        Object.keys(obj_data).forEach(key => {
            IntaoDB.setItem(key,obj_data[key]).catch((err) => console.log('Storage failed', err));
        });
        return false;
    }

    var mertricsLoad = function () {
        $('.dash_metrics').each(function (f) {
            var conttoken = $(this).attr("conttoken");
            var metrics = $(this).attr("data-metrics");
            var app = $(this).attr("data-type");
            if (metrics == '') {
                metrics = 'view';
            }

            save_metrics(app, metrics, conttoken);


        });
    }

    function moveMetricstoRedis() {
        var store_name = METIRCSStore;

        const MetricsStoreName = METIRCSStore;
        let metricsPush = [];
        getIntaoDb(dbName).then((db) => {
            if (db.objectStoreNames.contains(MetricsStoreName)) {
                const request = db.transaction(MetricsStoreName).objectStore(MetricsStoreName).getAll();

                request.onsuccess = () => {
                    const metricsData = request.result;

                    //console.log('----metricsData-----',metricsData);
                    if (metricsData && metricsData.length > 0) {
                        metricsData.forEach((data) => {
                            let metrics_data = data.values;
                            let metrics_key = data.taoh_data;
                            //console.log('----metrics_key-----',metrics_key);

                            metricsPush.push(data.values);
                            IntaoDB.removeItem(store_name, data.taoh_data).catch((err) => console.log('Storage failed', err));


                        });

                        //console.log('----metricsPush-----',metricsPush);

                        var data = {
                            'taoh_action': 'toah_metrics_push',
                            'metrics_data': JSON.stringify(metricsPush),
                            
                        };
                        jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function (response) {
                            //success
                        }).fail(function () {
                            console.log("Network issue!");
                        })

                    }
                }

                //console.log('----metricsPush-----',metricsPush);
            }
        });
    }
    window.onload = function () {
        setTimeout(mertricsLoad, 8000);
    }


    function triggerNextRequest(callback, ttl = 3000) {
        setTimeout(callback, ttl);
    }

    $(document).on("click", '.toast_dismiss', function () {
        // $("#toast").toggle();
        $("#toast").removeClass("toast_active");
        $("#toast_container").removeClass("toast-middle-con");
    });




    <?php if(isset($_GET['clear']) && $_GET['clear'] == 'config') { ?>
        
        const newUrl = new URL(location.href);
        newUrl.searchParams.delete('clear');
        window.history.replaceState({}, document.title, newUrl.href);
    <?php } ?>


    $('.li-modal').on('click', function(e){
      e.preventDefault();
      $('#theModal').modal('show').find('.modal-content').load($(this).attr('href'));
    });

    

$('.login-button').click(function(e){    
    
    var locc = $(location).attr('href');
    var days = 1;
    var name  = '<?php echo TAOH_ROOT_PATH_HASH.'_'.'referral_back_url';?>';
    var value = locc;
    //alert(locc);
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
    localStorage.setItem('email', '');
    $('#config-modal').modal({show:true});

});
$(document).on('click', '.login-button1', function (e) {
    
  //window.location.href = '<?php //echo TAOH_SITE_URL_ROOT; ?>/login';
 // var login_url = '<?php //echo TAOH_SITE_URL_ROOT; ?>/login';
   // e.preventDefault();
   //   $('#config-modal').modal('show').find('.modal-content').load(login_url);
});

if (localStorage.getItem('show_jobPostModal') == 1) {
    
    $('#jobPostModal').modal('show');
    localStorage.setItem('show_jobPostModal', 0);
}

// Close the modal and redirect to the "Post a Job" form when the button is clicked
$('#postJobButton').click(function() {
    window.location.href = "<?php echo TAOH_SITE_URL_ROOT.'/jobs/post'; ?>";  // Redirect after a short delay
});

    $(document).on('show.bs.modal', '#config-modal', function (e) {

        $('#isCodeSent').hide();
        $('#isCodeNotSent').show();
        localStorage.setItem('isCodeSent', 'false');
        if (!e.relatedTarget) return; // Stop execution if triggered via jQuery

        const key = _taoh_root_path_hash + '_referral_back_url';
        let value = $(e.relatedTarget).data('location');
        if (key && value) {
            const expires = new Date(Date.now() + 86400000).toUTCString(); // 1 day in milliseconds
            document.cookie = `${key}=${value}; expires=${expires}; path=/`;
        }
    });

    const formatObjectAndReunOnlyValue = (obj) => {
        if (typeof obj !== "undefined" && typeof obj === "object") {
            return Object.entries(obj).map(([key, value]) => {
                if (typeof value === "string" && value.includes(":>")) {
                    const [slug, name] = value.split(":>");
                    return name;
                } else {
                    if (value['id'] != undefined)
                        return value['name'];
                    else
                        return value['value'];
                }
            });
        }
        return {};
    };

    function get_to_date() {
        var currentDate = new Date()
        var day = currentDate.getDate();
        var month = currentDate.getMonth() + 1;
        var year = currentDate.getFullYear();
        var my_date = month + "-" + day + "-" + year;
        return my_date;
        //localStorage.setItem('date_last_agree', my_date);
    }


    var date_lat_intao_delete  = localStorage.getItem('date_lat_intao_delete');
    var current_date = get_to_date();
    if(date_lat_intao_delete == '' || date_lat_intao_delete == null){
        deleteIntaoData();
        localStorage.setItem('date_lat_intao_delete', current_date);
    }
    else if(date_lat_intao_delete != current_date){
        deleteIntaoData();
        localStorage.setItem('date_lat_intao_delete', current_date);
    }

    function deleteIntaoData() {
        var db_version = parseInt(_intao_db_version) || 1;
       var db_name = 'intaodb_'+_taoh_plugin_name;

        notifyRecreateIntaoDb(db_name, db_version);
        recreateIntaoDb(db_name, db_version);
    }

    function checkReportHumanCheckbox(){
        if($('#human_report').is(':checked')){
            $('#human_report').val(1);

            $('#bugsubmit').animate({
            width: '200px'
            }, 2000, function() {        
                $('#bugsubmit').attr({'disabled': false});
            });
            
        }else{
            $('#human_report').val(0);
        }
    }
     function checkdojotracker() {
       <?php if(taoh_user_is_logged_in() ) { ?>
       /*  IntaoDB.getItem(objStores.event_store.name, eventBaseInfoKey)
            .then((data) => {
                if (data?.values) {
                    handleResponse(data.values, false);
                } else {
                    getEventBaseInfo(requestData, true).then(resolve).catch(reject);
                }
            })
            .catch(reject); */
        <?php } ?>
     }


    
   
    function isGoalStale(timestamp) {
        let reaffirm_interval_days = 7;
        if (!timestamp) return true;
        const daysElapsed = (Date.now() - new Date(timestamp).getTime()) / (1000 * 60 * 60 * 24);
        return daysElapsed > reaffirm_interval_days;
    } 

        async function shouldPromptGoal() {
            try {
                //console.log('--logged user details---22222------')
                var existing = await IntaoDB.getItem(objStores.dojo_store.name,objStores.dojo_store.name);
                //console.log(existing);
                if(!existing){
                    
                     jQuery.get("<?php echo taoh_site_ajax_url(); ?>", {
                        'taoh_action': 'check_dojo_goal',
                    }, function (response) {
                        res = response;
                        if(res.dojo_goal != undefined){
                            var payload = res.dojo_goal;
                            IntaoDB.setItem(objStores.dojo_store.name,payload);
                            existing = payload;
                        }
                            //render_events_template(res, eventArea);
                    }).fail(function () {
                        console.log("Network issue!");
                    }) 
                }
                return !existing || isGoalStale(existing.timestamp);
            } catch {
                return true;
            }
         }

        function showGoalModal() {
            //if (shouldPromptGoal()) {
                   document.getElementById("goalModal").style.display = "block";
            //}
            
        }
         document.getElementById("closeGoalModal").addEventListener("click", function () {
            document.getElementById("goalModal").style.display = "none";
        });

        function hideGoalModal() {
            document.getElementById("goalModal").style.display = "none";
        }

        document.getElementById("submitGoalBtn").addEventListener("click", async () => {
            const selected = document.querySelector('input[name="user_goal"]:checked');
            if (!selected) {
                alert("Please select a goal");
                return;
            }
            let savetime =  Date.now();            
            const goal = selected.value;
            const payload = {
                taoh_dojo_goal: objStores.dojo_store.name,
                values: {
                goal,
                success: true,
                timestamp : savetime,
                output: "Goal saved"
                },
                timestamp: savetime
            };

            try {
                IntaoDB.setItem(objStores.dojo_store.name,payload);
                    hideGoalModal();
                var data = {
                    'taoh_action': 'update_dojo_tracker_status',
                    'mod': 'core',
                    "token": "<?php echo taoh_get_api_token(); ?>",
                    "ptoken": "<?php echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>",
                    "data" : payload
                };
               
                jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function (response) {
                    data = response;//JSON.parse(response);
                    console.log(data);
                   /*  if (data.status) {
                        if (data.total_num > 0) {
                            $('#badge_count').show();
                            $('#badge_count').html(data.total_num);
                        } else {
                            $('#badge_count').hide();
                            $('#badge_count').html('');
                        }

                    } else {
                        $('#badge_count').hide();
                        $('#badge_count').html('');
                    } */
                }); 
                // Optional: send to server too
                /* fetch("/api/save_user_goal.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ goal })
                }); */
            } catch (err) {
                console.error("Failed to save goal", err);
            }
            });

            //window.addEventListener("load", showGoalModal());
            <?php if(TAOH_DOJO_TRACKER_ENABLE){ ?>
            window.addEventListener("load", async () => {
                <?php // if (taoh_parse_url(0) == 'login' || taoh_parse_url(0) == 'settings') { ?>
                 //showGoalModal();
                if (await shouldPromptGoal()) {
                    showGoalModal();
                } 
            });
            <?php } ?>


document.addEventListener('DOMContentLoaded', function () {
    const maxSize = parseInt(document.getElementById('max_upload_size').value);

    document.querySelectorAll('input[type="file"]:not(.file_my_validation)').forEach(function (input) {
        input.addEventListener('change', function () {
            for (let file of input.files) {
                if (file.size > maxSize) {
                    taoh_set_error_message(`"${file.name}" is too large. Max allowed is ${formatBytes(maxSize)}.`,false);
                    input.value = ''; // Clear the input
                    break; // stop checking further files
                }
            }
        });
    });

    function formatBytes(bytes) {
        const sizes = ['B', 'KB', 'MB', 'GB'];
        if (bytes === 0) return '0 B';
        const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
    }
});

function updateRSProfileLiveStatus() {
    const ptoken = $('#profileModalContent').getSyncedData('ptoken');
    if (!ptoken || typeof getUserLiveStatus !== 'function') return;

    const $statusContainer = $('#profileModal').find('.rs_profile_live_status');
    if (!$statusContainer.length) return false; // return false if not yet available

    getUserLiveStatus(ptoken).then((userLiveStatus) => {
        const isOnline = userLiveStatus.success && Boolean(userLiveStatus.output);

        $statusContainer.find('.status-con').toggleClass('active', isOnline);
        $statusContainer.find('.status-text').text(isOnline ? 'Online' : 'Away');
        $statusContainer.show();
    }).catch(console.error);

    return true; // element exists and update triggered
}

$(document).on('click', '.openProfileModal', function() {
    if(ft_isLoggedIn) {
        const currentElem = $(this);
        let currentFullPath = (window.location.href).replace(_taoh_site_url_root, '');

        var pagename = currentElem.attr('data-pagename');

        var profile_token = currentElem.attr('data-profile_token');
        var view_more = currentElem.hasClass('view_more');
        var height_pop = window.innerHeight; // Adjust height as needed

        $('.profileModalBody').css('height', height_pop + 'px');
        $('#profileModalContent').css('height', height_pop - 100 + 'px');
        $('#profileModalContent').css('overflow', 'auto');


        $('#profileModalContent').html('<div class="d-flex align-items-center justify-content-center h-100"><img class="loader-gif" src="https://cdn.tao.ai/assets/wertual/images/taoh_loader.gif"></div>'); // Optional loading text
        $('#profileModalContent').setSyncedData('ptoken', profile_token);

        $('#profileModal').modal('show');

        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'GET',
            data: {
                taoh_action: 'get_profile_data',
                profile_token: profile_token,
                pagename,
                view_more,
                path: encodeURIComponent(btoa(currentFullPath)),
            },
            success: function(response) {
                $('#profileModalContent').html(response);
            }
        });
    }
});

$(document).on('shown.bs.modal', '#profileModal', function() {
    // Try every 500ms until the element exists
    let waitForStatus = setInterval(() => {
        if (updateRSProfileLiveStatus()) {
            clearInterval(waitForStatus);

            // Start regular 5-minute updates
            profileRSLiveStatusInterval = setInterval(updateRSProfileLiveStatus, 300000);
        }
    }, 500);
});

$(document).on('hide.bs.modal', '#profileModal', function() {
    if (profileRSLiveStatusInterval) {
        clearInterval(profileRSLiveStatusInterval);
        profileRSLiveStatusInterval = null;
    }
});

$(document).on('shown.bs.collapse', '#profile_rs_view_more', function () {
    this.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    $('[data-target="#profile_rs_view_more"]').text('View Less');
}).on('hidden.bs.collapse', function () {
    $('[data-target="#profile_rs_view_more"]').text('View More');
});

$(document).on('click', '.remaining-skills', function () {
    let currentElem = $(this);
    let remainingSkillsCount = currentElem.data('count');
    if (remainingSkillsCount > 0) {
        let remainingSkillsContainer = currentElem.siblings('.remaining-skills-container'); // Get the sibling container
        remainingSkillsContainer.toggle();
        currentElem.text(remainingSkillsContainer.is(':visible') ? `- ${remainingSkillsCount}` : `+${remainingSkillsCount}`);
    }
});

</script>

</body>
</html>
<?php

if (isset($_GET['secret_delete']) && $_GET['secret_delete']) {
    $url = TAOH_PLUGIN_PATH . "/cache/general/subsecret_" .  $_GET['secret_delete'] . ".cache";
    unlink($url);
    taoh_set_error_message('Error in Operation. Please try again.',8000,1);

}

if (isset($_GET['secret_delete_force']) &&  $_GET['secret_delete_force'] == 1) {

    $url = TAOH_PLUGIN_PATH . "/cache/general/subsecret_" .  TAOH_SITE_ROOT_HASH . ".cache";
    unlink($url);
   // taoh_set_error_message('Error in Operation. Please try again.',8000,1);

}

if ( ! isset( $_COOKIE[ 'client_time_zone' ] ) && stristr( $_SERVER[ 'REQUEST_URI' ], '/events/' ) ){
    header("Location: ".$_SERVER[ 'REQUEST_URI' ]); taoh_exit();
}

taoh_cacheops('logpush');
taoh_exit();

?>
