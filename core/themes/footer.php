<?php
include_once(TAOH_SITE_PATH_ROOT . '/assets/icons/icons.php');
$curr_page = taoh_parse_url(0);
$opt = taoh_parse_url(1);

// if (TAOH_JUSASK_ENABLE) {
if((taoh_user_is_logged_in() && (TAOH_ENABLE_OBVIOUSBABA || TAOH_ENABLE_SIDEKICK || TAOH_ENABLE_JUSASK))) {
    include_once('chatbot.php');
}

if (taoh_user_is_logged_in()) {
    $nv = in_array(NETWORKING_VERSION, [4, 5]) ? NETWORKING_VERSION : 1;
    require_once(TAOH_CORE_PATH . "/club/networking_footer{$nv}.php");
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
    if ($size == '-1') return PHP_INT_MAX;
    $size = trim($size);
    if (empty($size)) return 0;
    $last = strtolower($size[strlen($size)-1]);
    $size = (float)$size;
    switch($last) {
        case 'g': $size *= 1024;
        case 'm': $size *= 1024;
        case 'k': $size *= 1024;
    }
    return (int)$size;
}

function get_max_upload_size() {
    $uploadBytes = convertToBytes(ini_get('upload_max_filesize'));
    $postBytes = convertToBytes(ini_get('post_max_size'));

    $nginxBytes = convertToBytes('1M');
    $nginxConfig = @shell_exec('grep -r client_max_body_size /etc/nginx/ 2>/dev/null | grep -v "#"');
    if (!empty($nginxConfig) && preg_match('/client_max_body_size\s+(\d+[KMG]?);/', $nginxConfig, $matches)) {
        $nginxBytes = convertToBytes($matches[1]);
    } else {
        $nginxTest = @shell_exec('nginx -T 2>&1 | grep client_max_body_size | grep -v "#"');
        if (!empty($nginxTest) && preg_match('/client_max_body_size\s+(\d+[KMG]?);/', $nginxTest, $matches)) {
            $nginxBytes = convertToBytes($matches[1]);
        }
    }

    $limits = array_filter([$nginxBytes, $uploadBytes, $postBytes], function($v) { return $v < PHP_INT_MAX; });
    return empty($limits) ? PHP_INT_MAX : min($limits);
}

?>

</main>

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
                if(!isset($footer_tracking_link) || $footer_tracking_link ==''){
                    $footer_tracking_link = $encoded_path;
                }
    $encoded_urlencode = str_replace('.', '%2E', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    $encoded_urlencode = str_replace('~', '%7E', $encoded_urlencode);

            ?>
            <img src="<?php echo TAOH_CDN_PREFIX;?>/images/igtracker/<?php echo rawurlencode($encoded_urlencode);?>/<?php echo $ptokn_track;?>/pixel.png" />
            <?php } ?>
            
            <?php if(TAOH_FOOTER_MENU_ARRAY !='')  { 
                $footer_array = json_decode(TAOH_FOOTER_MENU_ARRAY,1); ?>
           
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

                    <div class="svg-container"><?= icon('home', '#1E1C1C') ?></div>
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
                   
                    <a class="footer-menu-item mx-lg-5 dropdown-toggle removecaret text-wrap" id="termsLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="svg-container"><?= icon('file-question', '#1E1C1C') ?></div>
                        <span>Terms</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="dropdown-item"><a href="https://tao.ai/privacy.php" target="_BLANK" class="term-item">Privacy Policy</a></li>
                        <li class="dropdown-item"><a href="https://tao.ai/terms.php" target="_BLANK" class="term-item">Terms & Conditions</a></li>
                        <li class="dropdown-item"><a href="https://tao.ai/conduct.php" target="_BLANK" class="term-item">Code of Conduct</a></li>
                    </ul>
                </div>
                
                <a  href="https://tao.ai" target="_blank" class="footer-menu-item mx-lg-5">
                    <?= icon('tao-logo', '#fff', 30) ?>
                    <span>By Tao.ai</span>
                </a>
                <?php if(taoh_user_is_logged_in()){ ?>
                <a class="feedback-page footer-menu-item mx-lg-5" target="_blank">
                    <div class="svg-container"><?= icon('feedback', '#1E1C1C') ?></div>
                    <span>Feedback</span>
                </a>
                <?php } ?>
                <?php if ( taoh_user_is_logged_in() && defined( 'TAOH_SITE_DONATE_ENABLE' ) && TAOH_SITE_DONATE_ENABLE ) { ?>
                <a href="<?php echo TAOH_SITE_URL_ROOT."/donate"; ?>" class="footer-menu-item mx-lg-5">
                    <div class="svg-container"><?= icon('donate', '#000') ?></div>
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
           
            <p class="text-center text-muted">
              <strong>&copy; <?php echo date('Y'). "</strong> | <strong>".TAOH_SITE_NAME_SLUG."</strong> | "."<a href='https://theworkcompany.com' target='_blank' class='twc-logo'>The<b>W</b><img src='https://theworkcompany.com/assets/images/theworkcompany_sq.png' alt='O' height='14'><b>RK</b>Company</a>"; ?> | <strong>All Rights Reserved</strong>  <?php if(taoh_user_is_logged_in()) { ?> |  <strong><a class="text-primary cursor-pointer" data-toggle="modal" data-target="#reportBugModal">Report an issue</a> <?php } ?>
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
    <h6 class="popup-title-text d-flex align-items-center border-bottom">
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

<!-- chat bot -->
<div class="chatbot-acc d-none">
    <div class="accordion" id="accordionExample">
        <div class="card">
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
                        <?= icon('chatbot-minimize', '#fff', 19) ?>
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

            <div id="chatOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                <div class="card-body px-2">
                    <div class="user-chatarea-messages pr-2">
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
                            <?= icon('chat-send', '#4361EE', 17) ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- chat bot end -->

<?php if(taoh_user_is_logged_in()) { ?>
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
<?php } ?>

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

<!-- jobPostModal - injected via JS in footer-main.js only when needed -->
<div id="jobPostModalContainer"></div>

<?php if(!taoh_user_is_logged_in()) { ?>
    <div class="modal top fade" id="config-modal" tabindex="" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
         <div class="vertical-alignment-helper">
            <div class="modal-dialog modal-lg vertical-align-center" role="document">
                <div class="modal-content log-in-modal-content">
                <div class="modal-header blue_bg">
                    <h4 class="modal-title">Login</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body p-0">
                    <div class="main-box p-0 border-0">
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

<link rel="stylesheet" href="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/css/footer.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>">
<?php
 $session_data = taoh_session_get(TAOH_ROOT_PATH_HASH);
if (defined('TAOH_CUSTOM_FOOT')) {
    echo TAOH_CUSTOM_FOOT;
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
        function gtag(){ dataLayer.push(arguments); }
            gtag('js', new Date());
        gtag('config', '<?= (defined('TAOH_PAGE_GA') && TAOH_PAGE_GA) ? TAOH_PAGE_GA : TAOH_SITE_GA ?>');
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

            <?php
$_ft_user_info = $session_data['USER_INFO'] ?? null;
$_ft_ptoken = $_ft_user_info->ptoken ?? '';
$_ft_isLoggedIn = taoh_user_is_logged_in();
$_ft_notificationEnabled = defined('TAOH_NOTIFICATION_ENABLED') && TAOH_NOTIFICATION_ENABLED;
$_ft_notificationStatus = defined('TAOH_NOTIFICATION_STATUS') ? TAOH_NOTIFICATION_STATUS : 0;
$_ft_profileIncomplete = $_ft_isLoggedIn && isset($_ft_user_info->profile_complete) && $_ft_user_info->profile_complete == 0 && isset($_ft_user_info->fname) && $_ft_user_info->fname == TAOH_SITE_NAME_SLUG;
$_ft_isSuperAdminInit = $_ft_isLoggedIn && isset($_ft_user_info->is_super_admin) && $_ft_user_info->is_super_admin == 1 && $_ft_user_info->site_status == 'init';
$_ft_hasReferralCookie = isset($_COOKIE[TAOH_ROOT_PATH_HASH.'_refer_token']) && $_COOKIE[TAOH_ROOT_PATH_HASH.'_refer_token'] != '' && isset($_COOKIE[TAOH_ROOT_PATH_HASH.'_referral_back_url']) && $_COOKIE[TAOH_ROOT_PATH_HASH.'_referral_back_url'] != '';
$_ft_enableReferralCheck = $curr_page != 'login' && $curr_page != 'createacc' && $_ft_isLoggedIn;
$_ft_sitemapNeeded = !file_exists("sitemap_" . date("Ymd") . ".sitemap");
        ?>
<script>
    const ft_isLoggedIn = <?= json_encode($_ft_isLoggedIn); ?>;
    window._ft_loopTime = <?= json_encode(TAOH_NOTIFICATION_LOOP_TIME_INTERVAL); ?>;
    window._ft_ptoken = <?= json_encode($_ft_ptoken); ?>;
    window._ft_apiToken = <?= json_encode(defined('TAOH_API_TOKEN') ? TAOH_API_TOKEN : ''); ?>;
    window._ft_apiSecret = <?= json_encode(defined('TAOH_API_SECRET') ? TAOH_API_SECRET : ''); ?>;
    window._ft_notificationEnabled = <?= json_encode($_ft_notificationEnabled); ?>;
    window._ft_notificationStatus = <?= json_encode($_ft_notificationStatus); ?>;
    window._ft_profileIncomplete = <?= json_encode($_ft_profileIncomplete); ?>;
    window._ft_isSuperAdminInit = <?= json_encode($_ft_isSuperAdminInit); ?>;
    window._ft_hasReferralCookie = <?= json_encode($_ft_hasReferralCookie); ?>;
    window._ft_enableReferralCheck = <?= json_encode($_ft_enableReferralCheck); ?>;
    window._ft_isSettingsPage = <?= json_encode($curr_page == 'settings'); ?>;
    window._ft_sitemapNeeded = <?= json_encode($_ft_sitemapNeeded); ?>;
    window._ft_clearConfig = <?= json_encode(isset($_GET['clear']) && $_GET['clear'] == 'config'); ?>;
    window._ft_dojoTrackerEnabled = <?= json_encode(defined('TAOH_DOJO_TRACKER_ENABLE') && TAOH_DOJO_TRACKER_ENABLE); ?>;
</script>
<script src="<?php echo TAOH_CDN_JS_PREFIX; ?>/footer-main.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>

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
