<?php
taoh_get_header();

$contslug = taoh_parse_url(1);
$contslug_expl = explode('-', $contslug);
$keytoken = array_pop($contslug_expl);
define('TAOH_ROOM_KEY', $keytoken);
if (!defined('TAO_PAGE_TITLE')) {
    define('TAO_PAGE_TITLE', "Comprehensive Open Networking Rooms at " . TAOH_SITE_NAME_SLUG . ": Explore and Apply to a Wide Range");
}
define('TAOH_ROOM_LIVE', $keytoken . '_live');
define('THIS_PAGE_URL', TAOH_SITE_URL_ROOT . '/' . TAOH_NETWORKPAGE_NAME);
define('THIS_PAGE_AJAX_NAME', taoh_site_ajax_url());

$skill_popup = 0;

$keyword = str_replace('%20', ' ', taoh_parse_url(2, 0));
$keyword = explode('?', $keyword)[0];

$keyslug = hash('crc32', $keyword);

$sess_user_info = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];

/* $taoh_call = 'core.network.get';
$taoh_vals = array(
    'mod' => 'networking_club',
	'token'=>taoh_get_dummy_token(),
	'key_slug' => $keyslug,
    'cache_name' => 'networking_club_'.$keyslug,
	'cache_time'=>15,
);

echo taoh_apicall_get_debug($taoh_call, $taoh_vals);die;
$get_result = json_decode(taoh_apicall_get($taoh_call, $taoh_vals,'',1),true); */
$get_result = json_decode(get_room_info($keyslug, $sess_user_info->ptoken, ['app' => 'custom']), true);
if ($get_result['success'] && isset($get_result['output']['club'])) {
    //$room_info_arr = json_decode($get_result['output'][0]['meta_value'],true);
    $club_info = $get_result['output']['club'];


    /* if(is_array($room_info_arr))
        $club_info = $room_info_arr['club'];
    else
        $club_info = json_decode($room_info_arr,true)['club']; */

    $room_keyslug = hash('crc32', $keyword);

    $full_loc_expl = explode(', ', $sess_user_info->full_location);
    $country = array_pop($full_loc_expl);
    $session_company = $sess_user_info->company;
    $session_company_id = taoh_get_id($session_company);
    $session_title = $sess_user_info->title;
    $session_title_id = taoh_get_id($session_title);
    $session_skill = $sess_user_info->skill;
    if (isset($_GET['skill_select']) && $_GET['skill_select'] != '') {
        $session_skill_id = array($_GET['skill_select']);
    } else {
        $session_skill_id = taoh_get_skill_keys($session_skill);
        $session_vals_skill = taoh_get_session_skill_vals($session_skill);
    }
    //print_r($session_skill_id);die;

    if ($club_info['geo_enable']) {
        $room_keyslug = hash('crc32', $keyword . $country);
    } else {
        $room_keyslug = hash('crc32', $keyword);
    }

    if (!$club_info['room_publish']) {
        if ($club_info['room_private']) {
            if ($club_info['sub_secret_token'] != TAOH_ROOT_PATH_HASH) {
                taoh_set_error_message('It is not allowed to access this room from your domain');
                taoh_redirect(TAOH_SITE_URL_ROOT . '/' . TAOH_CURR_APP_SLUG . '/groups/');
                die();
            }
        } else {
            $room_keyslug = hash('crc32', $room_keyslug . TAOH_ROOT_PATH_HASH);
        }
    }

    if ($club_info['make_country_specific']) {
        if ($country == $club_info['country_name']) {
            $room_keyslug = hash('crc32', $room_keyslug . $country);
        } else {
            taoh_set_error_message('It is not allowed to access this room from your location');
            taoh_redirect(TAOH_SITE_URL_ROOT . '/' . TAOH_CURR_APP_SLUG . '/groups/');
            die();
        }
    } else {
        if ($club_info['make_country_split']) {
            $room_keyslug = hash('crc32', $room_keyslug . $country);
        }
    }

    if ($club_info['make_cmp_specific']) {
        if ($session_company_id == $club_info['company_name'][0]) {
            $room_keyslug = hash('crc32', $room_keyslug . $session_company_id);
        } else {
            taoh_set_error_message('It is not allowed to access this room from your company');
            taoh_redirect(TAOH_SITE_URL_ROOT . '/' . TAOH_CURR_APP_SLUG . '/groups/');
            die();
        }
    } else {
        if ($club_info['make_cmp_split']) {
            $room_keyslug = hash('crc32', $room_keyslug . $session_company_id);
        }
    }

    if ($club_info['make_title_specific']) {
        if ($session_title_id == $club_info['title_name'][0]) {
            $room_keyslug = hash('crc32', $room_keyslug . $session_title_id);
        } else {
            taoh_set_error_message('It is not allowed to access this room from your title');
            taoh_redirect(TAOH_SITE_URL_ROOT . '/' . TAOH_CURR_APP_SLUG . '/groups/');
            die();
        }
    } else {
        if ($club_info['make_title_split']) {
            $room_keyslug = hash('crc32', $room_keyslug . $session_title_id);
        }
    }

    if ($club_info['make_skill_specific']) {
        $club_skill = $club_info['skill_name'];
        $for_keyslug = taoh_get_skill_vals($club_info['skill_name']);
        $room_keyslug = hash('crc32', $room_keyslug . $for_keyslug);
        foreach ($session_skill_id as $skill) {
            if (!in_array($skill, $club_skill)) {
                taoh_set_error_message('It is not allowed to access this room from your skill');
                taoh_redirect(TAOH_SITE_URL_ROOT . '/' . TAOH_CURR_APP_SLUG . '/groups/');
                die();
            }
        }
    } else {
        if ($club_info['make_skill_split']) {
            $room_keyslug_key = count($session_skill_id);
            if ($room_keyslug_key > 1) {
                $array_combine = array_combine($session_skill_id, $session_vals_skill);
                $skill_popup = 1;
            } else {
                $for_keyslug = $session_skill_id[0];
                $room_keyslug = hash('crc32', $room_keyslug . $for_keyslug);
            }
        }
    }

    if (!$skill_popup) {
        $room_status = get_room_info($room_keyslug, $sess_user_info->ptoken, ['app' => 'custom']);
        $room_status_arr = json_decode($room_status, true);
        if (isset($room_status_arr['output']) && $room_status_arr['output']) {
            $return = $room_status_arr['output'];
            $link = $return['club']['links']['club'];
            $room_link = TAOH_SITE_URL_ROOT . $link;

        } else {
            $title = $club_info['title'];
            $return = array(
                'keyslug' => $room_keyslug,
                'app' => 'sub_custom',
                'club' => array(
                    'title' => $club_info['title'],
                    'description' => $club_info['description'],
                    'more_info' => $club_info['more_info'],
                    'msg_from_owner' => $club_info['msg_from_owner'],
                    'keyword' => $club_info['keyword'],
                    'image' => $club_info['image'],
                    'square_image' => $club_info['square_image'],
                    'links' => array(
                        'club' => '/club/room/' . taoh_slugify($club_info['title']) . '-' . $room_keyslug,
                        'networking' => '/' . TAOH_SITE_CURRENT_APP_SLUG . '/kw/' . $club_info['title'],
                        'detail' => '/' . TAOH_SITE_CURRENT_APP_SLUG . '/club/' . $club_info['keyword'],
                        'lobby' => '/' . TAOH_SITE_CURRENT_APP_SLUG . '/lobby/' . $club_info['keyword'],
                    ),
                    'profile_types' => array(
                        array(
                            'slug' => 'employer',
                            'title' => 'Employer',
                        ),
                        array(
                            'slug' => 'professional',
                            'title' => 'Professional',
                        ),
                        array(
                            'slug' => 'provider',
                            'title' => 'Provider',
                        ),
                    ),
                    'skill' => '',
                    'company' => '',
                    'roles' => '',
                    'breadcrumbs' => array(
                        array(
                            'title' => 'Home',
                            'link' => '',
                        ),
                        array(
                            'title' => ucfirst(TAOH_SITE_CURRENT_APP_SLUG),
                            'link' => '/' . TAOH_SITE_CURRENT_APP_SLUG,
                        ),
                        array(
                            'title' => $title,
                            'link' => '/' . TAOH_SITE_CURRENT_APP_SLUG . '/' . taoh_slugify($title) . '-' . $keyslug,
                        ),
                    ),
                    'live' => '',
                    'geo_enable' => $club_info['geo_enable'],
                    'lock_required' => $club_info['lock_required'],
                    'lock_code' => $club_info['lock_code'],
                    'date_time_lock_required' => $club_info['date_time_lock_required'],
                    'start_date_time' => $club_info['start_date_time'],
                    'end_date_time' => $club_info['end_date_time'],
                    'utc_start' => $club_info['utc_start'],
                    'utc_end' => $club_info['utc_end'],
                    'chat_room_status' => $club_info['chat_room_status'],
                    'external_link' => $club_info['external_link'],
                    'live_cast_link' => $club_info['live_cast_link'],
                    'room_visiblity' => $club_info['room_visiblity'],
                    'owner_enable' => $club_info['owner_enable'],
                    'owner' => $club_info['owner'],
                    'make_cmp_split' => $club_info['make_cmp_split'],
                    'make_cmp_specific' => $club_info['make_cmp_specific'],
                    'company_name' => $club_info['company_name'],
                    'make_title_split' => $club_info['make_title_split'],
                    'make_title_specific' => $club_info['make_title_specific'],
                    'title_name' => $club_info['title_name'],
                    'make_country_split' => $club_info['make_country_split'],
                    'make_country_specific' => $club_info['make_country_specific'],
                    'country_name' => $club_info['country_name'],
                    'geohash' => $club_info['geohash'],
                    'make_skill_split' => $club_info['make_skill_split'],
                    'make_skill_specific' => $club_info['make_skill_specific'],
                    'skill_name' => $club_info['skill_name'],
                    'room_publish' => $club_info['room_publish'],
                    'room_private' => $club_info['room_private'],
                    'sub_secret_token' => $club_info['sub_secret_token'],
                    'custom_room' => $club_info['custom_room'],
                ),
            );

            $room_link = TAOH_SITE_URL_ROOT . '/' . TAOH_SITE_CURRENT_APP_SLUG . '/groups/' . taoh_slugify($club_info['title']) . '-' . $room_keyslug;
            create_room_info($return, $sess_user_info->ptoken);
        }


        $user_info = taoh_networking_getcell($room_keyslug, $club_info['owner']['user']['ptoken']);
        $user_info_arr = json_decode($user_info, true);
        if (!isset($user_info_arr['success']) || !$user_info_arr['success']) {

            $owner_info['keyslug'] = $room_keyslug;
            $owner_info['user'] = $club_info['owner']['user'];

            taoh_networking_postcell($owner_info, $club_info['owner']['user']['ptoken']);
        }

        $user_info = taoh_networking_getcell($room_keyslug);
        $user_info_arr = json_decode($user_info, true);
        if (!isset($user_info_arr['success']) || !$user_info_arr['success']) {
            $owner_info = array(
                'keyslug' => $room_keyslug,
                'user' => array(
                    'ptoken' => $sess_user_info->ptoken,
                    'chat_name' => $sess_user_info->chat_name,
                    'avatar' => $sess_user_info->avatar,
                    'full_location' => $sess_user_info->full_location,
                    'coordinates' => $sess_user_info->coordinates,
                    'geohash' => $sess_user_info->geohash,
                    'local_timezone' => $sess_user_info->local_timezone,
                    'profile_type' => $sess_user_info->type,
                    'site' => array(
                        'source' => '/',
                        'name' => TAOH_SITE_NAME_SLUG,
                    )
                ),
            );
            $coordinates = $owner_info['user']['coordinates'];
            if ($coordinates != '') {
                $co_array = explode('::', $coordinates);
                $lat = $co_array[0];
                $long = $co_array[1];
            }
            $owner_info['user']['longitude'] = $long;
            $owner_info['user']['latitude'] = $lat;
            taoh_networking_postcell($owner_info);
        }

        $title = $club_info['title'];

        $lock_required = $club_info['lock_required'] ?? false;
        $lock_code = $club_info['lock_code'] ?? '';
        $date_time_lock = $club_info['date_time_lock_required'] ?? '';
        if (isset($lock_code) && $lock_required) {
            if (!isset($_COOKIE[$room_keyslug . "_lock_checked"])) {
                setcookie($room_keyslug . "_lock_code", $lock_code, '0', '/');
                setcookie($room_keyslug . "_lock_required", $lock_required, '0', '/');
                setcookie($room_keyslug . "_lock_checked", 1, '0', '/');
            }
        }
        $start_display = '';
        $end_display = '';

        if (isset($club_info['start_date_time']) && $club_info['start_date_time'] != '') {
            //$start_display = timeconversion($club_info['start_date_time']);
            $start_date_formatted = event_time_display_lobby($club_info['utc_start'], $club_info['geo_enable']);
            $start_display = 'Starts At : ' . $start_date_formatted;

        }
        if (isset($club_info['end_date_time']) && $club_info['end_date_time'] != '') {
            $end_date_formatted = event_time_display_lobby($club_info['utc_end'], $club_info['geo_enable']);
            $end_display = 'Ends At : ' . $end_date_formatted;
        }

        date_default_timezone_set('UTC');

        $utcTime = gmdate("Y-m-d H:i:s");
        $currentTime = strtotime($utcTime);
        $startTime = strtotime($club_info['utc_start']);
        $endTime = strtotime($club_info['utc_end']);

        /*
        
        $timezone = taoh_user_timezone(); 
        $local_tz = new DateTimeZone( $timezone);//user's tiemzone - india
        $currentTime = new DateTime('now', $local_tz);

        $startTime = new DateTime($start_date_formatted);
        $endTime = new DateTime($end_date_formatted);

        */

        // Check if current time is between start time and end time
        if ($currentTime > $startTime && $currentTime < $endTime) {
            $live = 1;
        } else {
            $live = 0;
        }

        if (!$club_info['date_time_lock_required']) {
            $live = 1;
        }

    }

} else {
    taoh_set_error_message('Invalid Room');
    taoh_redirect(TAOH_SITE_URL_ROOT . '/' . TAOH_CURR_APP_SLUG . '/groups/');
}
if (isset($_GET['open_network']) && $_GET['open_network'] == 'true') {
    taoh_redirect($room_link);
    exit();
}
//die('-------------');
?>

    <style>
        .error {
            color: red;
        }

        span.h5 {
            font-size: 13px !important;
        }
    </style>
<?php if (!$skill_popup) { ?>
    <header class="sticky-top bg-white border-bottom border-bottom-gray">
        <section class="hero-area pt-20px pb-20px bg-white shadow-sm overflow-hidden">
            <span class="stroke-shape stroke-shape-1"></span>
            <span class="stroke-shape stroke-shape-2"></span>
            <span class="stroke-shape stroke-shape-3"></span>
            <span class="stroke-shape stroke-shape-4"></span>
            <span class="stroke-shape stroke-shape-5"></span>
            <span class="stroke-shape stroke-shape-6"></span>
            <div class="container">
                <div class="hero-content d-flex flex-wrap align-items-center justify-content-between jobs-mobile-header">
                    <div class="col-lg-7">
                        <h2 class="section-title fs-24 mb-1">Networking Lobby</h2>
                        <p class="section-desc"><?php echo $title; ?></p>
                    </div>

                    <div class="hero-btn-box col-lg-3">
                            <a href="<?php echo TAOH_SITE_URL_ROOT . '/' . TAOH_CURR_APP_SLUG . '/groups/';?>" title="Go Back to the Rooms">
                                <span class="gobackwindow"><i class="las la-chevron-circle-left"></i> Go Back</span></a>
                    </div>
                </div><!-- end hero-content -->
            </div><!-- end container -->
        </section>
    </header>
    <section class="">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="company-details-panel-main-bar pt-30px" style="word-wrap: break-word;">
                        <div class="company-details-panel mb-30px" id="tech-stack">
                            <img width="40%" src="<?php echo $club_info['square_image']; ?>" class="img-fluid"
                                 alt="<?php echo ucfirst($club_info['title']); ?>"/>

                            <p><?php echo html_entity_decode($club_info['more_info']); ?></p>


                        </div><!-- end company-details-panel -->
                    </div><!-- end company-details-panel-main-bar -->
                </div><!-- end col-lg-8 -->
                <div class="col-lg-4">
                    <!-- Event Side widget  Details -->
                    <div class="sidebar pt-40px pl-30px">
                        <!-- Network Detail -->
                        <div class="card card-item">
                            <div class="card-body">
                                <div class="d-flex card-title justify-content-between">
                                    <div>
                                        <h3 class="fs-19 fw-semi-bold">Network Details</h3>
                                    </div>
                                    <div>
                                        <?php //echo taoh_calendar_widget($event_arr); ?>
                                    </div>
                                </div>
                                <div class="divider"><span></span></div>
                                <br/>
                                <ul class="generic-list-item pt-3 fs-15">
                                    <li><i class="fas fa-hourglass"></i> <?php echo "Room Status: "; ?>
                                        <input type="hidden" id="live" value="<?php echo $live; ?>">
                                        <?php if ($live) { ?>
                                            <span class="badge badge-md  badge-success status_btn"><strong>LIVE</strong></span>
                                        <?php } else { ?>
                                            <span class="badge badge-md  badge-warning status_btn"><strong>NOT LIVE</strong></span>
                                        <?php } ?>


                                    </li>
                                    <li><?php echo ($start_display) ? '<i class="fas fa-calendar-days" ></i>' . $start_display : ''; ?></li>
                                    <li><?php echo ($end_display) ? '<i class="fas fa-calendar-days" ></i>' . $end_display : ''; ?></li>
                                    <li><i class='fas fa-map-marker'></i> Timezone: <?php echo taoh_user_timezone(); ?>
                                    </li>

                                </ul>
                                <?php
                                //echo "============".$live;
                                $open = 'block';
                                if (!$live) {
                                    $open = 'none';
                                    ?>
                                    <span class="message">Network Room Not LIVE. Please check back later.</span>

                                <?php }

                                ?>
                                <!--- Lock Code -->
                                <div class="mt-20px lock_lobby">

                                    <?php

                                    if (isset($lock_code) && $lock_required && !isset($_COOKIE[$room_keyslug . "_lock_checked"])) {
                                        $open = 'none';
                                        ?>
                                        Room is locked. Please enter the lock code to access the room.
                                        <div style="border:1px solid #3e3e3e;" class="mb-30px mt-20px p-5">
                                            <h5 class="center" style="text-align:center">Lock Code</h5>
                                            <form id="lock_code_form" method="POST" enctype="multipart/form-data">
                                                <div class="hidden">
                                                    <input type="hidden" value="tc2asi3iida2" name="opscode">
                                                </div>
                                                <div class="mb-10px">
                                                    <div class="form-group">
                                                        <label class="fs-14 text-black fw-medium">Enter Lock
                                                            Code</label>
                                                        <input type="text" class="form-control form--control fs-14"
                                                               id="room_lock_code" name="room_lock_code"
                                                               placeholder="Enter Lock Code">
                                                        <label id="lock_error" class="error" for="lock_code"></label>
                                                    </div><!-- end form-group -->
                                                </div>
                                                <button type="button" class="btn btn-primary lock_submit">Submit
                                                </button>
                                            </form>
                                        </div>
                                    <?php } ?>
                                </div>
                                <?php
                                if (isset($club_info['external_link']) && $club_info['external_link'] != '')
                                    $link = $club_info['external_link'];
                                else{
                                    $link = $room_link;


                                    //kalpana added
                                    $link = TAOH_SITE_URL_ROOT . '/' . TAOH_SITE_CURRENT_APP_SLUG . '/forum/' . taoh_slugify($club_info['title']) . '-' . $room_keyslug;
                                }
                                ?>
                                <div style="display:<?php echo $open; ?>" id="open_link">
                                    <a target="_blank" href="<?php echo $link; ?>" class="btn theme-btn mb-3 "><i
                                                class="la la-sign-in mr-1"></i> Join</a>
                                </div>
                            </div>
                        </div><!-- end card-item -->
                        <?php if (function_exists('taoh_invite_friends_widget')) {
                            taoh_invite_friends_widget($club_info['title'], 'Networking');
                        } ?>
                    </div>
                </div><!-- end sidebar -->
            </div><!-- end col-lg-4 -->
        </div><!-- end row -->
        </div>
    </section>
    <?php
}
?>
<?php if ($skill_popup) { ?>
    <div class="modal fade" id="skillAlert" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Your Skills</h5>
                </div>
                <div class="modal-body">
                    <p>You have more than one skill.So,you must choose which room skill you want to go?</p><br>
                    <?php foreach ($array_combine as $key => $val) { ?>
                        <a href="<?php echo TAOH_SITE_URL_ROOT . '/' . TAOH_CURR_APP_SLUG . '/lobby/' . $club_info['keyword'] . '?skill_select=' . $key; ?>"
                           class="btn btn-primary"><?php echo $val; ?></a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
    <script type="text/javascript">

        <?php if($skill_popup){ ?>
        var skill_vals = '<?php echo json_encode($array_combine); ?>';
        console.log(skill_vals);
        $(document).ready(function () {
            $('#skillAlert').modal({
                backdrop: 'static',
                keyboard: true,
                show: true
            });
        });
        <?php }else{ ?>
        var api_lock_code = '<?php echo $lock_code; ?>';
        var api_date_time_lock = '<?php echo $date_time_lock; ?>';
        var api_lock_code = '<?php echo $lock_code; ?>';
        $('.lock_submit').click(function () {
            var lock_code = $('#room_lock_code').val();
            var live = $('#live').val();
            console.log(lock_code);
            if (lock_code == '') {
                $('#lock_error').show();
                $('#lock_error').html('Please enter the lock code');
                return false;
            }
            $('.lock_submit').prop("disabled", true);
            // add spinner to button
            $('.lock_submit').html(
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`
            )
            if (lock_code != api_lock_code) {
                $('#lock_error').show();
                $('#lock_error').html('Invalid lock code');
                $('.lock_submit').prop("disabled", false);
                $('.lock_submit').html(
                    `Submit`
                )
                return false;
            } else {

                $('#lock_error').html('');
                $('#lock_error').hide();
                setCookie('<?php echo $room_keyslug; ?>_lock_checked', '0');
                if (live == 1) {
                    $('#open_link').show();
                } else {
                    $('#open_link').hide();
                }

                $('.lock_lobby').hide();
            }


        });

        function setCookie(name, value, days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }
        <?php } ?>
        //  });
    </script>
<?php
taoh_get_footer();
?>