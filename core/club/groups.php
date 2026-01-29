<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/raj/assets/icons/icons.php';
taoh_get_header();

$pagename = 'groups';

$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
$user_info_obj = $taoh_user_is_logged_in ? taoh_user_all_info() : null;

$allow_manageroom = ($_GET['admin'] ?? '') === 'manageroom';

$valid_rooms_viewer = $taoh_user_is_logged_in && $user_info_obj->profile_complete;

$contslug = taoh_parse_url(1);
$contslug_expl = explode('-', $contslug);
$keytoken = array_pop($contslug_expl);
define('TAOH_ROOM_KEY', $keytoken);
if ( ! defined ( 'TAO_PAGE_TITLE' ) ) { define ( 'TAO_PAGE_TITLE', "Comprehensive Open Networking Rooms at ".TAOH_SITE_NAME_SLUG.": Explore and Apply to a Wide Range" ); }
define('TAOH_ROOM_LIVE', $keytoken . '_live');
define('THIS_PAGE_URL', TAOH_SITE_URL_ROOT . '/' . TAOH_NETWORKPAGE_NAME);
define('THIS_PAGE_AJAX_NAME', taoh_site_ajax_url());

$current_app = taoh_parse_url(0);
//$app_data = taoh_app_info($current_app);

$ptoken = $user_info_obj?->ptoken ?? '';
$keywords = (array) ($user_info_obj?->keywords ?? []);
?>
    <style>
        .page-body {
            background-color: #fff !important;
        }

        #login-prompt {
            display: block !important;
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .card-body {
            display: flex;
            flex-direction: column;
        }

        .card-body .card-text {
            color: #080911;
            font-size: 14px;
            font-weight: 300;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: normal;
            min-height: 48px;
            line-height: 1.5rem;
        }

        .card-body .mt-auto {
            margin-top: auto;
        }

        .flag {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            font-size: 0.8rem;
            border-radius: 3px;
        }

        .card {
            position: relative;
        }
    </style>

    <div class="bg-white groups">

        <header class="sticky-top bg-white border-bottom border-bottom-gray" style="top: 0; z-index: 99;">
            <section class="hero-area bg-white shadow-sm">
                <!-- <span class="stroke-shape stroke-shape-1"></span>
                <span class="stroke-shape stroke-shape-2"></span>
                <span class="stroke-shape stroke-shape-3"></span> -->
                <span class="stroke-shape stroke-shape-4"></span>
                <span class="stroke-shape stroke-shape-5"></span>
                <span class="stroke-shape stroke-shape-6"></span>
                <div class="container">
                    <?php include 'includes/club_header.php'; ?>
                </div>
            </section>
        </header>

        <div class="mobile-app groups-nav py-3">
            <ul class="nav nav-tabs justify-content-center border-0" id="room_tabs" role="tablist">
                    <?php
                    $active = 'active';
                    $show = '';
                    if(!empty($keywords)) {
                        $active = '';
                        $show = 'none';
                        ?>
                        <li class="nav-item">
                            <a class="nav-link rooms active" id="profile_rooms_tab" onclick="loadRoom('profile_rooms');">My Preferred Groups</a>
                        </li>
                    <?php } ?>

                    <?php if ( taoh_user_is_logged_in() ){ ?>
                        <li class="nav-item">
                            <a class="nav-link rooms <?php echo  $active ;?> " id="custom_rooms_tab" onclick="loadRoom('custom_rooms');">General Groups</a>
                        </li>
                    <?php } ?>


            </ul>
        </div>
        <section class="question-area pb-40px groups-body-bg">
            <div class="container pt-5">

                <div class="row" id="loaderArea"></div>
                <div class="row" id="CustomRoomArea" style="display:<?php echo $show;?>;"></div>
                <div class="row justify-content-center" id="ProfileRoomArea" >

                    <?php
                    $array = array_values($keywords);
                    $com = getCombinations($array);
                    $option = array(
                        'width' => 400,
                        'height' => 250,
                        'gradientStart' => '#ff7e5f',
                        'gradientEnd' => '#feb47b',
                        'circleColors' => array('#ff6f61', '#4ecdc4', '#3d84a8'),
                        'circleCount' => 5,
                        'minRadius' => 5,
                        'maxRadius' => 20,
                        'logoUrl' => TAOH_CDN_PREFIX . '/images/logo/set/logo__000000.png',
                        'logoWidth' => 40,
                        'logoHeight' => 40
                    );

                    foreach ($com as $a => $y) {
                        $room_keyslug = hash('crc32', $y);
                        $option['title'] = $y;
                        $options = json_encode($option);
                        ?>
                            <div class="col-md-6 col-lg-4 mb-4 ">
                                <div class="card shadow-sm h-100 mx-auto" style="max-width: 396px; border: 2px solid #D3D3D3;">
                                    <img id="room_image_<?php echo $room_keyslug;?>" src="" class="card-img-top" alt="Event Image" style="object-fit: cover;">
                                    <div class="card-body d-flex flex-column">
                                        <!--<div>
                                            <div class="room-profile-pic">
                                                <img src="https://localhost/hires-i/assets/images/profile_room_4.png" class="" alt="profile pics" style="object-fit: cover; width: 53.78px; height: 53.78px; border-radius: 50%; ">
                                                <img src="https://localhost/hires-i/assets/images/profile_room_3.png" class="" alt="profile pics" style="object-fit: cover; width: 53.78px; height: 53.78px; border-radius: 50%; ">
                                                <img src="https://localhost/hires-i/assets/images/profile_room_2.png" class="" alt="profile pics" style="object-fit: cover; width: 53.78px; height: 53.78px; border-radius: 50%; ">
                                                <img src="https://localhost/hires-i/assets/images/profile_room_1.png" class="" alt="profile pics" style="object-fit: cover; width: 53.78px; height: 53.78px; border-radius: 50%; ">
                                            </div>
                                            <p style="font-size: clamp(12px, 3vw + 12px, 15px); color: #333333;">Sherin, Joesph, Harris and 30 Others are here</p>
                                        </div>-->
                                        <h5 class="card-title mt-3">
                                            <a class="text-primary text-capitalize d-flex align-items-start" style="gap: 8px;" target="_blank" href=<?= TAOH_SITE_URL_ROOT . '/club/forum/' . taoh_slugify($y).'-'.$room_keyslug. '?t=' . base64url_encode($y); ?>">
                                                <svg class="mt-1" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="min-width: fit-content;">
                                                    <circle cx="12" cy="12" r="11.5" fill="#1C52A8" stroke="#2557A7"></circle>
                                                    <path d="M12.7617 5.45698C12.6211 5.17771 12.3242 5 11.9981 5C11.672 5 11.3777 5.17771 11.2345 5.45698L9.5298 8.81578L5.72263 9.354C5.40448 9.3997 5.13936 9.61296 5.04126 9.90492C4.94317 10.1969 5.02271 10.5193 5.25071 10.7351L8.0133 13.3526L7.36109 17.0516C7.30807 17.3562 7.44063 17.666 7.7031 17.8462C7.96557 18.0265 8.31288 18.0493 8.59922 17.9046L12.0007 16.1655L15.4023 17.9046C15.6886 18.0493 16.0359 18.029 16.2984 17.8462C16.5609 17.6634 16.6934 17.3562 16.6404 17.0516L15.9855 13.3526L18.7481 10.7351C18.9761 10.5193 19.0583 10.1969 18.9576 9.90492C18.8568 9.61296 18.5944 9.3997 18.2762 9.354L14.4664 8.81578L12.7617 5.45698Z" fill="white"></path>
                                                </svg>

                                                <span style="font-size: clamp(16px, 2vw + 1rem, 24px); font-weight: 400;"
                                                ><strong><?php echo $y;?></strong> Group</span>
                                            </a>
                                        </h5>
                                        <p class="card-text" style="font-size: clamp(12px, 3vw + 12px, 15px); font-weight: 400; text-align: justify;">
                                            </p>
                                        <div class="d-flex justify-content-end mt-auto pt-2">

                                            <a class="btn" style="background: #2557A7; color: #fff; font-size: 12px; border-radius: 6px;"
                                             target="_blank" href="<?= TAOH_SITE_URL_ROOT . '/club/forum/' . taoh_slugify($y).'-'.$room_keyslug. '?t=' . base64url_encode($y); ?>">Join Now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                generatePoster('<?php echo $options;?>').then((poster) => {
                                    $('#room_image_<?php echo $room_keyslug;?>').attr('src', poster.toDataURL());
                                });
                            </script>
                            <?php
                        }
                    ?>


                </div>
                <div class="<?php echo (!$valid_rooms_viewer ? 'lblur' : ''); ?>" id="pagination"></div>
            </div>
        </section>

    </div>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
    <script>
        let user_is_logged_in = <?= json_encode($taoh_user_is_logged_in); ?>;
        let valid_rooms_viewer = <?= json_encode($valid_rooms_viewer ?? false); ?>;
        let allow_manageroom = <?= json_encode($allow_manageroom ?? false); ?>;
        let loaderArea = $('#loaderArea');
        let eventArea = $('#CustomRoomArea');
        var current_tab = 'custom_rooms';
        //let currentMod = '<?php //echo $app_data->slug; ?>';
        let networkTitle = '<?php echo $contslug;?>';
        let geohash = "";
        let search = "";
        let locationClear = $('#locationClear');
        let searchClear = $('#searchClear');
        let listUpdatedAt = 0;
        let activeChatList = $('#activeChatList');
        let totalItems = 0; //this will be rewriiten on response of jobs on line 363
        let itemsPerPage = 12;
        let currentPage = 1;

        //Initial run
        $(document).ready(function () {
            $('.ts-control').css('height', '37px');
           // taoh_rooms_init();
            //taoh_profile_rooms_init();
            <?php if(!empty($keywords)) { ?>
                loadRoom('profile_rooms');
            <?php } else { ?>
                loadRoom('custom_rooms');
            <?php } ?>
            change_url();
        })

        function loadRoom(room) {
            current_tab = room;
            $('.rooms').removeClass('active');
            $('#' + room + '_tab').addClass('active');
            if (room == 'custom_rooms') {
                taoh_rooms_init();
            } else {
                taoh_profile_rooms_init();
            }
        }

        function taoh_profile_rooms_init(){
            loader(true, loaderArea);
            $('#ProfileRoomArea').show();
            $('#CustomRoomArea').hide();
            loader(false, loaderArea);
        }

        $(document).on('click', '.room_delete_btn', function () {
            let current_elem = $(this);
            let room_keyslug = $(this).data('keyslug');
            let room_title = $(this).data('room_title');

            if(room_keyslug){
                current_elem.prop('disabled', true);
                taoh_set_warning_message('Are you certain you want to permanently delete the <i>' + room_title + ' - ' + room_keyslug + '</i> room? This action cannot be undone.', false, 'toast-middle', [
                    {
                        text: 'Yes',
                        action: () => {
                            current_elem.text('Deleting...');

                            $.post(_taoh_site_ajax_url, {
                                'taoh_action': 'taoh_room_delete',
                                'keyslug': room_keyslug,
                                'ptoken': '<?php echo $ptoken; ?>',
                            }, function (response) {
                                taoh_rooms_init();
                            }).fail(function () {
                                current_elem.text('Delete');
                                current_elem.prop('disabled', false);
                            });
                        },
                        class: 'dojo-v1-btn float-right mt-3 mb-3'
                    },
                    {
                        text: 'cancel',
                        action: () => {
                            current_elem.text('Delete');
                            current_elem.prop('disabled', false);
                        },
                        class: 'dojo-v1-btn float-right mt-3 mb-3 mr-2'
                    }
                ]);
                /*$.confirm({
                    title: 'Confirmation!',
                    content: 'Are you certain you want to permanently delete the <i>' + room_title + ' - ' + room_keyslug + '</i> room? This action cannot be undone.',
                    type: 'danger',
                    buttons: {
                        cancel: function () {
                            current_elem.text('Delete');
                            current_elem.prop('disabled', false);
                        },
                        confirm: {
                            text: 'Yes',
                            action: function () {
                                current_elem.text('Deleting...');

                                $.post(_taoh_site_ajax_url, {
                                    'taoh_action': 'taoh_room_delete',
                                    'keyslug': room_keyslug,
                                    'ptoken': '<?php //echo $ptoken; ?>',
                                }, function (response) {
                                    taoh_rooms_init();
                                }).fail(function () {
                                    current_elem.text('Delete');
                                    current_elem.prop('disabled', false);
                                });
                            }
                        }
                    }
                });*/
            }else{
                taoh_set_error_message('Room keyslug not found!', false);
            }
        });

        function show_pagination(holder) {
            console.log(totalItems)
            return $(holder).pagination({
                items: totalItems,
                itemsOnPage: itemsPerPage,
                currentPage: currentPage,
                displayedPages: 3,
                onInit: function () {
                    $("#pagination ul").addClass('pagination');
                    $("#pagination ul li.disabled").addClass('page-link text-gray');
                    $("#pagination ul li.active").addClass('page-link bg-primary text-white');
                },
                onPageClick: function (pageNumber, event) {
                    $("#pagination ul").addClass('pagination');
                    $("#pagination ul li.disabled").addClass('page-link text-gray');
                    $("#pagination ul li.active").addClass('page-link bg-primary text-white');
                    currentPage = pageNumber;
                    console.log('--show_pagination----------');
                    taoh_rooms_init();
                }
            });
        }

        /* function search_submit(){
            loader(true, loaderArea);
            search = $('#query').val();
            geohash = $('#geohash').val();
            console.log('search_submit', search, geohash);
            if(search) {
                searchClear.show();
            } else {
                searchClear.hide();
            }
            if(geohash) {
                locationClear.show();
            } else {
                locationClear.hide();
            }
            taoh_rooms_init();
        }

        function clearBtn(type) {
            loader(true, loaderArea);
            $('.'+type+'_badge').hide();
            if(type == "search") {
                search = "";
                $('#query').val("");
            }
            if(type == "geohash") {
                geohash = "";
                $('.ts-control div.item').html('');
            }
            taoh_rooms_init();
        } */

        function taoh_rooms_init() {
            loader(true, loaderArea);
            $('#CustomRoomArea').show();
            $('#ProfileRoomArea').hide();

            let data = {
                'taoh_action': 'taoh_rooms_get',
                'ops': 'list',
                'search': search,
                'offset': currentPage - 1,
                'limit': itemsPerPage,
                'ptoken': '<?php echo $ptoken; ?>',

            };
            jQuery.post(_taoh_site_ajax_url, data, function (response) {
                render_rooms_template(response, eventArea);
                loader(false, loaderArea);
            }).fail(function () {
                loader(false, loaderArea);
                console.log("Network issue!");
            })
        }

        // Function to generate the event poster
        async function generateEventPoster(title) {
                    const eventPosterCanvas = await generatePoster({
                        width: 400,
                        height: 250,
                        gradientStart: '#ff7e5f',
                        gradientEnd: '#feb47b',
                        circleColors: ['#ff6f61', '#4ecdc4', '#3d84a8'],
                        circleCount: 5,
                        minRadius: 5,
                        maxRadius: 20,
                        title: title,
                        logoUrl: _taoh_cdn_prefix + '/images/logo/set/logo__000000.png',
                        logoWidth: 40,
                        logoHeight: 40
                    });

                    return eventPosterCanvas.toDataURL();
        }

        function render_rooms_template(data, slot) {
            slot.empty();
            if (data.output === false) {
                slot.append("<p>No data found!</p>");
                $('#pagination').hide();
                return false;
            }
            if (data.success === false) {

                let noRoomsHTML = `<div class="col-md-6 col-lg-4 mb-4 lblur">
                            <div class="card shadow-sm h-100 mx-auto" style="max-width: 396px; border: 2px solid #D3D3D3;">
                                <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/no-room.png" class="card-img-top" alt="Event Image" style="object-fit: cover;">
                                <div class="card-body d-flex flex-column">

                                    <h5 class="card-title mt-3">
                                        <a class="text-primary text-capitalize d-flex align-items-start" style="gap: 8px;" target="_blank" href="#">
                                            <svg class="mt-1" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="min-width: fit-content;">
                                                <circle cx="12" cy="12" r="11.5" fill="#1C52A8" stroke="#2557A7"></circle>
                                                <path d="M12.7617 5.45698C12.6211 5.17771 12.3242 5 11.9981 5C11.672 5 11.3777 5.17771 11.2345 5.45698L9.5298 8.81578L5.72263 9.354C5.40448 9.3997 5.13936 9.61296 5.04126 9.90492C4.94317 10.1969 5.02271 10.5193 5.25071 10.7351L8.0133 13.3526L7.36109 17.0516C7.30807 17.3562 7.44063 17.666 7.7031 17.8462C7.96557 18.0265 8.31288 18.0493 8.59922 17.9046L12.0007 16.1655L15.4023 17.9046C15.6886 18.0493 16.0359 18.029 16.2984 17.8462C16.5609 17.6634 16.6934 17.3562 16.6404 17.0516L15.9855 13.3526L18.7481 10.7351C18.9761 10.5193 19.0583 10.1969 18.9576 9.90492C18.8568 9.61296 18.5944 9.3997 18.2762 9.354L14.4664 8.81578L12.7617 5.45698Z" fill="white"></path>
                                            </svg>

                                            <span style="font-size: clamp(16px, 2vw + 1rem, 17px); font-weight: 500; line-height: 1.6;">Boston Startups Group !</span>
                                        </a>
                                    </h5>
                                    <p class="card-text" style="font-size: clamp(12px, 3vw + 12px, 14px); font-weight: 400; text-align: justify;">Startup owners from Boston ! here is your group to join. Join with the fellow owners and expand your network !</p>
                                    <div class="d-flex justify-content-end mt-auto pt-2">
                                        <a class="btn" style="background: #2557A7; color: #fff; font-size: 12px; border-radius: 6px;" target="_blank" href="#">Join Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                        for (let i = 0; i < 3; i++) {
                            slot.append(noRoomsHTML);
                        }
                $('#pagination').hide();
                return false;
            }

            var sub_secret_token = '<?php echo TAOH_ROOT_PATH_HASH; ?>';
            $.each(data.output, function (kk, ll) {
                var l = JSON.parse(ll.meta_value);

                if (l.club.room_visiblity === 0 || (l.club.room_publish === 0 && l.club.room_private === 1 && sub_secret_token !== l.club.sub_secret_token)) {
                    return;
                }

                /*var start_display = '';
                var end_display = '';

                if (l.club.start_date_time != '' && l.club.utc_start != null) {
                    start_display = timeconversion(l.club.utc_start, l.club.geo_enable);
                }
                if (l.club.end_date_time != '' && l.club.utc_end != null) {
                    end_display = timeconversion(l.club.utc_end, l.club.geo_enable);
                }*/

                let title = l.club.title ? l.club.title : '';
                let desc = l.club.description ? new DOMParser().parseFromString(htmlDecode(l.club.description), 'text/html').body.textContent : '';
                desc = desc.charAt(0).toUpperCase() + desc.slice(1);
                // Generate the poster or use the club image
                checkImageExists(l.club.image).then(async (isValid) => {
                    let imageSrc = isValid ? l.club.image : await generateEventPoster(title);

                    let roomsHTML = `<div class="col-md-6 col-lg-4 mb-4 ${!valid_rooms_viewer ? 'lblur' : ''}">
                            <div class="card shadow-sm h-100 mx-auto" style="max-width: 396px; border: 2px solid #D3D3D3;">
                                <img src="${imageSrc}" class="card-img-top" alt="Event Image" style="object-fit: cover;">
                                <div class="card-body d-flex flex-column">

                                    <h5 class="card-title mt-3">
                                        <a class="text-primary text-capitalize d-flex align-items-start" style="gap: 8px;" target="_blank" href="${_taoh_site_url_root}/club/lobby/${l.club.keyword}">
                                            <svg class="mt-1" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="min-width: fit-content;">
                                                <circle cx="12" cy="12" r="11.5" fill="#1C52A8" stroke="#2557A7"></circle>
                                                <path d="M12.7617 5.45698C12.6211 5.17771 12.3242 5 11.9981 5C11.672 5 11.3777 5.17771 11.2345 5.45698L9.5298 8.81578L5.72263 9.354C5.40448 9.3997 5.13936 9.61296 5.04126 9.90492C4.94317 10.1969 5.02271 10.5193 5.25071 10.7351L8.0133 13.3526L7.36109 17.0516C7.30807 17.3562 7.44063 17.666 7.7031 17.8462C7.96557 18.0265 8.31288 18.0493 8.59922 17.9046L12.0007 16.1655L15.4023 17.9046C15.6886 18.0493 16.0359 18.029 16.2984 17.8462C16.5609 17.6634 16.6934 17.3562 16.6404 17.0516L15.9855 13.3526L18.7481 10.7351C18.9761 10.5193 19.0583 10.1969 18.9576 9.90492C18.8568 9.61296 18.5944 9.3997 18.2762 9.354L14.4664 8.81578L12.7617 5.45698Z" fill="white"></path>
                                            </svg>

                                            <span style="font-size: clamp(16px, 2vw + 1rem, 17px); font-weight: 500; line-height: 1.6;">${title}</span>
                                        </a>
                                    </h5>
                                    <p class="card-text" style="font-size: clamp(12px, 3vw + 12px, 14px); font-weight: 400; text-align: justify;">${desc}</p>
                                    <div class="d-flex justify-content-end mt-auto pt-2">
                                        ${allow_manageroom ? `<button class="btn btn-danger mr-2 room_delete_btn" data-keyslug="${l.keyslug}" data-room_title="${title}">Delete</button>` : ''}
                                        <a class="btn" style="background: #2557A7; color: #fff; font-size: 12px; border-radius: 6px;" target="_blank" href="${_taoh_site_url_root}/club/lobby/${l.club.keyword}">Join Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                    slot.append(roomsHTML);
                });
            });

            totalItems = data.total;
            console.log(totalItems);
            if (totalItems > itemsPerPage) {
                $('#pagination').show();
                show_pagination('#pagination');
            } else {
                $('#pagination').hide();
            }
        }

        function timeconversion(utcdate, geo_enable) {

            // Assuming UTC date is in 'YYYY-MM-DDTHH:mm:ssZ' format
            //let utcDate = '2022-01-01T00:00:00Z';
            if (geo_enable == '1') var timezone = '<?php echo taoh_user_timezone(); ?>';
            else var timezone = 'America/New_York';


            var dating = new Date(utcdate);
            let dateInIndia = new Date(dating.toLocaleString("en-US", {timeZone: timezone}));
            console.log('---utcdate----------' + utcdate);
            console.log('----India----------' + dateInIndia);

            //let date = dating;
            let date = dateInIndia;

            //return false;


            /*
            // Create a Date object from the timestamp
            //let date = new Date(timestamp);
            // Get current date
            let now = new Date();

            // Create date strings in two different timezones
            let dateInNewYork = new Date(now.toLocaleString("en-US", {timeZone: "America/New_York"}));
            let dateInIndia = new Date(now.toLocaleString("en-US", {timeZone: "Asia/Kolkata"}));

            // Get timezone offsets in minutes
            let offsetNewYork = dateInNewYork.getTime();
            let offsetIndia = dateInIndia.getTime();

            // Calculate the difference in hours
            let diff = (offsetIndia - offsetNewYork)/1000 ;

            console.log(diff);
            // Assuming timestamp is in milliseconds
            let timestamp = unixtimestamp;

            timestamp = parseInt(unixtimestamp) + parseInt(diff);
            console.log('-----------'+code+'----timestamp------>>>>'+timestamp)
            let timestamp_milli = timestamp;
            let date = new Date(timestamp_milli);

            */
            // Months array
            var months_arr = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];


            // Convert timestamp to milliseconds
            //	var date = new Date(unixtimestamp*1000);

            // Year
            var year = date.getFullYear();

            // Month
            var month = months_arr[date.getMonth()];

            // Day
            var day = date.getDate();

            // Hours
            var hours = date.getHours();

            // Minutes
            var minutes = "0" + date.getMinutes();

            // Seconds
            var seconds = "0" + date.getSeconds();

            var med = 'AM';
            // Display date time in MM-dd-yyyy h:m:s format
            if (hours > 12) {
                hours = hours - 12;
                med = 'PM';

            }

            return month + ' ' + day + '-' + year + ' ' + hours + ':' + minutes.substr(-2) + ' ' + med;
        }

        function change_url() {
            const newUrl = new URL(location.href);
            newUrl.searchParams.delete('ops');
            newUrl.searchParams.delete('mod');
            window.history.replaceState({}, document.title, newUrl.href);
        }
    </script>
<?php

if ($taoh_user_is_logged_in && !$valid_rooms_viewer) {
    echo '<div class="col footer-prompt">';
    echo '<h5 class="pb-2">Complete your settings to fully use the platform.</h5>';
    echo '<a href="' . (TAOH_SETTINGS_URL ?? '') . '" class="btn theme-btn" id="login-btn"><i class="la la-cog mr-1"></i>Complete Settings</a>';
    echo '</div>';
}

taoh_get_footer();