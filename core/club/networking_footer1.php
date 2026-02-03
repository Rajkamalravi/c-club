<?php
$actual_curr_page = $curr_page = taoh_parse_url(0);
if ($curr_page === 'club') {
    $curr_page = taoh_parse_url(1);
}
defined('TAOH_CURR_APP_IMAGE_SQUARE') || define('TAOH_CURR_APP_IMAGE_SQUARE', TAOH_SITE_URL_ROOT . '/assets/images/nerwork_app_sq.png');
defined('TAOH_CURR_APP_IMAGE') || define('TAOH_CURR_APP_IMAGE', TAOH_SITE_URL_ROOT . '/assets/images/nerwork_app.png');

$displayMsgList = true;
$displayPrivateChat = false;
$is_user_live = false;
$open_profile_chat = false;
$room_info = [];

$key_key = 'dm-direct-message';
$keyslug = $pc_key_slug = hash('crc32', $key_key);

$ntw_ft_user_info_obj = taoh_user_all_info();

$ntw_ft_room_key = '';
$geo_enable = 1;
if ($ntw_ft_user_info_obj && $ntw_ft_user_info_obj->profile_complete) {
    if ($geo_enable) {
        $full_loc_expl = explode(', ', $ntw_ft_user_info_obj->full_location);
        $ntw_ft_room_key = hash('crc32', TAOH_SITE_ROOT_HASH . array_pop($full_loc_expl));
    } else {
        $ntw_ft_room_key = hash('crc32', TAOH_SITE_ROOT_HASH);
    }
}

$ntw_ft_raw_my_profile_stage = $ntw_ft_user_info_obj->profile_stage ?? ($ntw_ft_user_info_obj->profile_complete ?? 0);
$ntw_ft_my_profile_stage = max(0, is_numeric($ntw_ft_raw_my_profile_stage) ? (int)$ntw_ft_raw_my_profile_stage : 0);


if (isset($curr_page) && $ntw_ft_my_profile_stage > 1 && ($curr_page == 'room' || $curr_page == 'message' || $curr_page == 'events')) {
    $displayMsgList = false;
}

$privateChatTo_ptoken = '';
$privateChatTo_chat_name = 'Private Chat';
$privateChatTo_avatar = TAOH_OPS_PREFIX . '/avatar/PNG/128/avatar_def.png';
if ($ntw_ft_user_info_obj && $ntw_ft_my_profile_stage > 1 && isset($curr_page) && ($curr_page == 'profile')) {
    $ptoken_from = $ntw_ft_user_info_obj ? $ntw_ft_user_info_obj->ptoken : TAOH_API_TOKEN;
    if ($actual_curr_page === 'club') {
        if (taoh_parse_url(1) === 'directory' && taoh_parse_url(2) === 'profile') {
            $ptoken_to = taoh_parse_url(3);
        } else {
            $ptoken_to = taoh_parse_url(2);
        }
    } else {
        $ptoken_to = taoh_parse_url(1);
    }

    if (!empty($ptoken_from) && !empty($ptoken_to) && $ptoken_from != $ptoken_to) {
        if (isset($_GET['from']) && ($_GET['from'] == 'messaging' || $_GET['from'] == 'rspchat')) $open_profile_chat = true;

        $privateChatFrom_json = taoh_get_user_info($ptoken_from, 'info');
        $privateChatFrom_array = json_decode($privateChatFrom_json, true);
        $privateChatFrom_arr = $privateChatFrom_array['output']['user']['full'] ?? [];
        if (!empty($privateChatFrom_arr)) {
            $privateChatFrom_ptoken = $privateChatFrom_arr['ptoken'];
            $privateChatFrom_chat_name = $privateChatFrom_arr['chat_name'] ?? '';
            if (!empty($privateChatFrom_arr['avatar_image'])) {
                $privateChatFrom_avatar = $privateChatFrom_arr['avatar_image'];
            } else {
                $privateChatFrom_avatar = TAOH_OPS_PREFIX . '/avatar/PNG/128/' . ((isset($privateChatFrom_arr['avatar']) &&
                        $privateChatFrom_arr['avatar'] != 'default') ? $privateChatFrom_arr['avatar'] : 'avatar_def') . '.png';
            }
            $privateChatFrom_full_location = $privateChatFrom_arr['full_location'];
            $privateChatFrom_type = $privateChatFrom_arr['type'] ?? '';
        }

        $privateChatTo_json = taoh_get_user_info($ptoken_to, 'info');
        $privateChatTo_array = json_decode($privateChatTo_json, true);
        $privateChatTo_arr = $privateChatTo_array['output']['user']['full'] ?? [];

        $ntw_ft_raw_profile_stage = $privateChatTo_arr['profile_stage'] ?? ($privateChatTo_arr['profile_complete'] ?? 0);
        $ntw_ft_profile_stage = max(0, is_numeric($ntw_ft_raw_profile_stage) ? (int)$ntw_ft_raw_profile_stage : 0);

        if (!empty($privateChatTo_arr)) {
            $privateChatTo_ptoken = $privateChatTo_arr['ptoken'];
            $privateChatTo_chat_name = $privateChatTo_arr['chat_name'] ?? '';
            if (!empty($privateChatTo_arr['avatar_image'])) {
                $privateChatTo_avatar = $privateChatTo_arr['avatar_image'];
            } else {
                $privateChatTo_avatar = TAOH_OPS_PREFIX . '/avatar/PNG/128/' . ((isset($privateChatTo_arr['avatar']) &&
                        $privateChatTo_arr['avatar'] != 'default') ? $privateChatTo_arr['avatar'] : 'avatar_def') . '.png';
            }
            $privateChatTo_full_location = $privateChatTo_arr['full_location'];
            $privateChatTo_type = $privateChatTo_arr['type'] ?? '';
        }

        if (!empty($privateChatFrom_arr) && !empty($privateChatTo_arr) && $ntw_ft_profile_stage > 1) {
            $displayPrivateChat = true;

            /*$pc_ne_array = array($privateChatFrom_ptoken, $privateChatTo_ptoken);
            sort($pc_ne_array);
            $room_token = implode(',', $pc_ne_array);
            $pc_key_slug = hash('crc32', $room_token);*/

            //$key_key = 'dm-direct-message';
            //$keyslug = $pc_key_slug = hash('crc32', $key_key);

            $channel_slug_data = [$pc_key_slug, $privateChatFrom_ptoken, $privateChatTo_ptoken];
            asort($channel_slug_data);
            $pc_channel_id = generateSecureSlug(implode('_', $channel_slug_data), 16);

            $room_kkkey = array('profile', $privateChatFrom_ptoken, $privateChatTo_ptoken);
            sort($room_kkkey);
            $room_data = implode('_', $room_kkkey);

            //$room_info = taoh_profile_room_info($pc_key_slug, $ptoken_from);

            $room_info = create_dm_room($pc_key_slug, $ptoken_from);

            if (empty($room_info) || $room_info == 'false') {
                taoh_redirect(TAOH_SITE_URL_ROOT);
            }
            updateCellInfo($pc_key_slug, $ptoken_from);
            updateCellInfo($pc_key_slug, $ptoken_to);
        }

        $networking_ajax_url = taoh_site_ajax_url();
    }

    if (!empty($ptoken_to)) {
        $user_live_status_res = taoh_post(TAOH_CACHE_CHAT_PROC_URL, [
            "ops" => 'live',
            'key' => $ptoken_to,
            'status' => 'get',
            'code' => TAOH_OPS_CODE
        ]);
        $user_live_status = json_decode($user_live_status_res, true);
        if ($user_live_status['success']) {
            $is_user_live = (bool)$user_live_status['output'];
        }
    }
}

function taoh_profile_room_info($keyslug, $ptoken, $createIfNotExist = true)
{
    $room_info_json = get_room_info($keyslug, $ptoken, ['app' => 'profile']);
    $room_info = json_decode($room_info_json, true);
    if ($createIfNotExist && $room_info['success'] && $room_info['output'] == false) {
        $title = 'Personal Chat';

        $room_data = array(
            'keyslug' => $keyslug,
            'app' => 'profile',
            'club' => array(
                'title' => $title,
                'description' => 'Welcome to the Profile Networking Chat Room',
                'short' => 'Connect with your friends.',
                'image' => TAOH_CURR_APP_IMAGE,
                'square_image' => TAOH_CURR_APP_IMAGE_SQUARE,
                'links' => array(
                    'club' => '/profile',
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
                'sponsors' => array(
                    array(
                        'title' => 'TAO.ai',
                        'sub_title' => 'TAO: Through technology, make professional connectios and career growth universally accessible.',
                        'image' => 'https://tao.ai/tao/innovative/img/TAO_AI_Logo_icon_orng.png',
                        'link' => 'https://tao.ai',
                    ),
                ),
                'breadcrumbs' => array(
                    array(
                        'title' => 'Home',
                        'link' => '',
                    )
                ),
                'live' => '',
                'geo_enable' => false,
                'owner_enable' => false,
                'owner' => '',
                'full_location' => '',
                'coordinates' => '',
                'geohash' => '',
                'longitude' => '',
                'latitude' => '',
                'faq' => array(
                    array(
                        'title' => 'What if I dont see anyone in the networking room?',
                        'description' => 'You could always expand your search radius to see other people in network.',
                    ),
                ),
            )
        );

        $room_info = create_room_info($room_data, $ptoken);
    }

    return $room_info['output'] ?? [];
}

?>

<style>
    #no_previous_chat_all {
        text-align: center;
    }

    .rooms-invite-content{
        cursor: pointer;
    }

    #chat_msg_list .date-badge .badge{
        font-size: 65% !important;
        font-weight: normal;
    }


    /* Date Badge */
    .date-badge {
        display: flex;
    }

    .date-badge:before,
    .date-badge:after {
        content: '';
        flex: 1;
        border-bottom: solid 1px #b3b3b3;
        margin-bottom: 8px;
    }

    .date-badge:before {
        margin-right: 5px;
    }

    .date-badge:after {
        margin-left: 5px;
    }

    .date-badge .badge {
        border: solid 1px #b3b3b3 !important;
        padding: .28em .8em !important;
    }

    .sticky-badge {
        position: absolute;
        margin-left: auto;
        margin-right: auto;
        left: 50%;
        text-align: center;
        background: white;
        padding: .28em .8em !important;
        border: solid 1px #b3b3b3 !important;
        transform: translate(-50%, -50%);
        z-index: 1;
    }

    /* /Date Badge */

    /*.all-rooms-invite-avatar {
        min-width: 30px !important;
        max-width: 30px !important;
    }*/
</style>

<?php
$displayPrivateChat = false;
if($displayPrivateChat): ?>
<div class="privateChat_blk" id="privateChat_blk" style="display:<?php echo $displayPrivateChat ? 'block' : 'none'; ?>">
    <div class="accordion" id="allMessages1">
        <div class="card">
            <div class="card-header global_message1" id="headingOne1">
                <h2 class="mb-0">
                    <!-- data-toggle="collapse" data-target="#privateChatList" aria-expanded="true" aria-controls="collapseOne"-->

                    <button class="btn btn-link btn-block text-left" id="privateChat_collapse_btn" type="button">
                        <div class="d-flex fixed-expand-arrow">
                                <span class="chat-user-icon">
                                  <?php echo '<img width="40" height="40" style="border-radius: 20px;" src="'.$privateChatTo_avatar.'" alt="profile">'; ?>
                                  <p class="<?= $is_user_live ? 'active-status' : 'active-status-border'; ?>" id="user_active_status"><span class="status-hidden"></span></p>
                                </span>
                            <span class="chat-user-head"><?php echo $privateChatTo_chat_name; ?></span>
                            <!-- <span style="display:none" class="counter unread-count"></span>-->
                            <i class="la la-video" id="pc_video" style="display: <?= $is_user_live ? 'block' : 'none'; ?>;"></i>
                        </div>
                    </button>
                </h2>
            </div>

            <div id="privateChatList" class="collapse" aria-labelledby="headingOne1" data-parent="#privateChatAccordion">
                <div class="card-body">
                    <div id="privatechat">
                        <div class="taoh-loader taoh-spinner show" id="pc_loader"></div>

                        <ul class="chat" id="chat_msg_list">
                            <!-- Chat messages will be loaded here -->

                        </ul>
                    </div>

                    <div class="chat-box bg-white">
                        <form action="" method="post" id="privateChatForm" style="display: <?= $is_user_live ? 'block' : 'none'; ?>;">
                            <div class="input-group">
                                <input name="pc_message" id="pc_message" class="form-control" autocomplete="off" placeholder="Type your message here">
                                <button class="btn btn-primary no-rounded" id="pc_send_btn" type="submit"><i class="fa fa-send-o"></i></button>
                            </div>
                        </form>

                        <div class="text-center" id="send_email_blk" style="display: <?= !$is_user_live ? 'block' : 'none'; ?>;">
                            <button type="button" id="profile_offline_send_message_btn" class="btn btn-sm btn-primary fw-medium m-2" data-toptoken="<?= $privateChatTo_ptoken; ?>">Create Message</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="chatareabox-outer">
        <div style="display:none" class="accordion" id="privateChatAccordion">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#chatOne" aria-expanded="true" aria-controls="collapseOne">
                            <div class="d-flex fixed-expand-arrow">
                                <span class="chat-user-head">Messaging</span>
                            </div>
                        </button>
                    </h2>
                </div>

                <div id="chatOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample"  style="height: 350px">
                    <div class="card-body">
                        <div class="user-chatarea-messages"></div>
                        <form class="chat-form">
                            <textarea placeholder="Write a message..."></textarea>
                            <i class="fa fa-paper-plane-o"></i>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php endif; ?>

<div style="display:<?= ($ntw_ft_user_info_obj && $ntw_ft_user_info_obj->profile_complete && $displayMsgList) ? 'block' : 'none'; ?>" class="chat-area-fixed">
    <div class="accordion" id="allMessages">
        <div class="card">
            <div class="card-header global_message" id="headingOne">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#chatListBottom" aria-expanded="true" aria-controls="collapseOne">
                        <div class="d-flex fixed-expand-arrow">
                <span class="chat-user-icon">
                  <?php echo taoh_get_profile_image(); ?>
                  <!--<img width="32" height="32" src="https://opslogy.com/avatar/PNG/128/avatar_def.png" alt="">-->
                  <p class="active-status"><span class="status-hidden"></span></p>
                </span>
                            <span class="chat-user-head">Notifications</span>
                            <span id="allRoomsInviteUnreadCount" class="badge badge-pill badge-danger unread-count"></span>
                        </div>
                    </button>
                </h2>
            </div>
            <div id="chatListBottom" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">

                <div class="card-body">
                    <div class="custom-control mb-4" id="custom_switch" style="display:none;">
                        <span onclick="updateAllInvitesReadStatus()" style="float: right;cursor: pointer;color: #fff;background: #3396CC;padding: 0px 8px;border-radius: 5px;font-size: 12px;"><i class="la la-trash"></i> Clear</span>
                    </div>

                    <div id="messageList">
                        <div id="no_previous_chat_all">
                            <img src="<?= TAOH_SITE_URL_ROOT; ?>/assets/images/no-chat.svg" alt="no-chat">
                            <div class="pt-3 pb-3">
                                <p class="mb-4 fs-22">No messages yet</p>
                                <p>Reach out and start a conversation to advance your career.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="chatareabox-outer">
        <div style="display:none" class="accordion" id="accordionExample">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#chatOne" aria-expanded="true" aria-controls="collapseOne">

                            <div class="d-flex fixed-expand-arrow">
                                <span class="chat-user-head">Messaging</span>
                            </div>
                        </button>
                    </h2>
                </div>

                <div id="chatOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample"  style="height: 350px">
                    <div class="card-body">
                        <div class="user-chatarea-messages">
                        </div>
                        <form class="chat-form">
                            <textarea placeholder="Write a message..."></textarea>
                            <i class="fa fa-paper-plane-o"></i>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript">
    let _ntw_ft_valid_user = <?php echo (!empty($ntw_ft_user_info_obj) && in_array($ntw_ft_user_info_obj->profile_complete ?? null, [1, '1'], true)) ? 'true' : 'false'; ?>;
    let _ntw_ft_ptoken = "<?php echo (!empty($ntw_ft_user_info_obj) && !empty($ntw_ft_user_info_obj->ptoken)) ? $ntw_ft_user_info_obj->ptoken : ''; ?>";

    let _ntw_ft_room_key = '<?php echo $ntw_ft_room_key ?? ''; ?>';

    var is_user_live = '<?php echo $is_user_live; ?>';
    var curr_page = '<?php echo $curr_page ?? ''; ?>';
    var displayPrivateChat = '<?php echo $displayPrivateChat; ?>';
    var open_profile_chat = '<?php echo $open_profile_chat; ?>';

    var myptoken = '<?php echo $ntw_ft_user_info_obj->ptoken; ?>';

    let _taoh_curr_app_image = '<?php echo defined(' TAOH_CURR_APP_IMAGE') ? TAOH_CURR_APP_IMAGE : ''; ?>';
    let _taoh_curr_app_image_square = '<?php echo defined(' TAOH_CURR_APP_IMAGE_SQUARE') ? TAOH_CURR_APP_IMAGE_SQUARE : ''; ?>';

    var ft_taoh_api_token = '<?php echo taoh_get_dummy_token(); ?>';

    let lastAllMessageTime = 0;

    if (displayPrivateChat) {
        var pToken_from = '<?php echo $ptoken_from ?? ''; ?>';
        var pToken_to = '<?php echo $ptoken_to ?? ''; ?>';
    } else {
        var pToken_from = '<?php echo $ptoken_from ?? ''; ?>';
        var pToken_to = '<?php echo $ptoken_to ?? ''; ?>';
    }

    let pc_room_key = '<?php echo $pc_key_slug ?? ''; ?>';
    let pc_channel_id = '<?php echo $pc_channel_id ?? ''; ?>';

    //alert(pc_room_key);

    let ft_taoh_chat_network = '<?php echo TAOH_CHAT_NETWORK ?? 0; ?>';
    let ft_taoh_chat_forum = '<?php echo TAOH_CHAT_FORUM ?? 3; ?>';

    let taohChannelDmMsg = '<?= defined('TAOH_CHANNEL_DM') ? TAOH_CHANNEL_DM : 4; ?>';

    var taoh_cdn_main_prefix = '<?php echo TAOH_CDN_MAIN_PREFIX; ?>';

    $(document).ready(function () {
        $('#pc_video').on('click', function (e) {
            e.preventDefault();

            if (displayPrivateChat) {
                $('#privateChatList').collapse('show');
                $('#pc_video').removeClass('la-video').addClass('la-spinner la-spin');

                let data = {
                    'taoh_action': 'taoh_add_video_chat',
                    'my_token': pToken_from,
                    'guest_token': pToken_to,
                    'network_title': '<?php echo isset($room_info['club']) && isset($room_info['club']['title']) ? $room_info['club']['title'] : 'Personal Chat'; ?>'
                };

                let privateChatTo_chat_name = '<?php echo $privateChatTo_chat_name ?? ''; ?>';
                let confirmMsg;
                if (privateChatTo_chat_name != '') {
                    confirmMsg = 'Please confirm that you want to start a video chat with ' + privateChatTo_chat_name + '?';
                } else {
                    confirmMsg = 'Please confirm that you want to start a video chat?';
                }

                taoh_set_warning_message(confirmMsg, false, 'toast-middle', [
                    {
                        text: 'Yes',
                        action: () => {
                            $.post(_taoh_site_ajax_url, data, function (response) {
                                let video_chat_link = 'Want to chat? <div class="chat-meeting"><i class="la la-video"></i><div><p>' + privateChatTo_chat_name + '\'s meeting</p><a href="' + response.other_link + '" target="_blank" class="chat-meeting-link"> Join video meeting</a></div></div>';
                                $('#pc_message').val(video_chat_link);
                                $('#pc_send_btn').trigger('click');
                                $('#pc_video').removeClass('la-spinner la-spin').addClass('la-video');
                                if (response.my_link) window.open(response.my_link);
                            }).fail(function () {
                                $('#pc_video').removeClass('la-spinner la-spin').addClass('la-video');
                            });
                        },
                        class: 'dojo-v1-btn float-right mt-3 mb-3'
                    },
                    {
                        text: 'cancel',
                        action: () => {
                            $('#pc_video').removeClass('la-spinner la-spin').addClass('la-video');
                        },
                        class: 'dojo-v1-btn float-right mt-3 mb-3 mr-2'
                    }
                ]);

                // $.confirm({
                //     title: 'Confirmation!',
                //     content: confirmMsg,
                //     type: 'warning',
                //     buttons: {
                //         cancel: function () {
                //             $('#pc_video').removeClass('la-spinner la-spin').addClass('la-video');
                //         },
                //         confirm: {
                //             text: 'Yes',
                //             action: function () {
                //                 $.post(_taoh_site_ajax_url, data, function (response) {
                //                     let video_chat_link = 'Want to chat? <div class="chat-meeting"><i class="la la-video"></i><div><p>' + privateChatTo_chat_name + '\'s meeting</p><a href="' + response.other_link + '" target="_blank" class="chat-meeting-link"> Join video meeting</a></div></div>';
                //                     $('#pc_message').val(video_chat_link);
                //                     $('#pc_send_btn').trigger('click');
                //                     $('#pc_video').removeClass('la-spinner la-spin').addClass('la-video');
                //                     if (response.my_link) window.open(response.my_link);
                //                 }).fail(function () {
                //                     $('#pc_video').removeClass('la-spinner la-spin').addClass('la-video');
                //                 });
                //             }
                //         }
                //     }
                // });
            }

            e.stopPropagation();
        });

    }); 

    document.addEventListener('DOMContentLoaded', () => {
        if (!_ntw_ft_ptoken) return;

        const maybeUpdateStatus = () => {
            if (!document.hidden) updateUserLiveStatus(_ntw_ft_ptoken);
        };       

        if(typeof updateUserLiveStatus === 'function') {
            updateUserLiveStatus(_ntw_ft_ptoken);
        }
        document.addEventListener('visibilitychange', maybeUpdateStatus);
        setInterval(maybeUpdateStatus, 120000); // every 2 minutes       
    });


async function getPinData(ntw_room_key, channelId, frmMessageId, frmMessageKey, pinFrom, chatWith, chatFrom) {

    let data = {
        ops: 'channel_message',
        action: 'get_pinned_message',
        code: _taoh_ops_code,
        key: my_pToken,
        keyslug: ntw_room_key,
        message_id: frmMessageId,
        pin_from: pinFrom,
        cfcc60: 1
    };

    $.ajax({
        url: _taoh_cache_chat_url,
        type: 'GET',
        dataType: 'json',
        //headers: {'If-None-Match': ntwChannelListETag},
        data: data,
        success: function (response, textStatus, jqXHR) {
            (async () => {
                console.log(response);            
                if (jqXHR.status === 304) return;
                if (response.status == "success") {
                    console.log("pinned_by", response.messages[0].pinned_by);
                    
                    //const chat_messages_key = `frm_${ntw_room_key}_${channelId}`;
                    var $parent;
                    var msg_div;
                    var chat_messages_key;
                    var common_class_name;
                    if(pinFrom == "channel") {
                        $parent = $('.pin-message-v2');
                        msg_div = "pin_message_div";
                        chat_messages_key = `frm_${ntw_room_key}_${channelId}`;
                        common_class_name = "comm_pin_message_div";
                    } else {
                        $parent = $('.pin-message-v2-dm');
                        msg_div = "pin_message_div-dm";
                        chat_messages_key = `cm_${ntw_room_key}_${chatWith}_${chatFrom}`;
                        common_class_name = "comm_pin_message_div_dm";
                    }

                    console.log("chat_messages_key", chat_messages_key);                    

                    var intao_data;
                    intao_data = await IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key);
                    if (!intao_data?.values || !intao_data.values.chat[frmMessageKey]) {
                        chat_messages_key = `cm_${ntw_room_key}_${chatFrom}_${chatWith}`;
                        intao_data = await IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key);
                        if (!intao_data?.values || !intao_data.values.chat[frmMessageKey]) {
                            console.log("not updated ==> ", chat_messages_key);
                            return;
                        }                        
                    }

                    const updatedResponse = { ...intao_data.values };	
                    updatedResponse.chat[frmMessageKey].pin = response.messages[0].pin;
                    updatedResponse.chat[frmMessageKey].pinned_by = response.messages[0].pinned_by;

                    var pinned_by;
                    var pinned_by_ptoken = response.messages[0].ptoken;
                    const userInfo = await getUserInfo(response.messages[0].ptoken, 'public');
                    
                    console.log(" !! "+pinFrom+" !! "+response.messages[0].pin+" !! "+frmMessageId);

                    let message = updatedResponse.chat[frmMessageKey].message;
                    let msg = decodeURIComponent(message.replace(/\+/g, ' '));

                    //if(pinFrom == "channel") {
                        var elem = $(`.pin-message[data-frm_message_id="${frmMessageId}"]`);
                        if (response.messages[0].pin == 1) {

                            if (userInfo.avatar_image != '' && userInfo.avatar_image != undefined) {
                                var avatar_image = userInfo.avatar_image;
                            } else if (userInfo.avatar != undefined && userInfo.avatar != 'default') {
                                var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/' + userInfo.avatar + '.png';
                            } else {
                                var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/avatar_def.png';
                            }

                            let decoded = decodeURIComponent(msg);
                            decoded = decoded.replace(/\+/g, '');
                            const match = decoded.match(/<a [^>]*>.*?<\/a>/i);
                            const aTag = match ? match[0] : "";           
                            if(aTag != "") {
                                var msgHTML = "Join "+aTag+" - Video Room";
                            } else {
                                msg = decodeURIComponent(msg.replace(/\+/g, ' '));
                                if (msg.length > 100) {
                                    var visibleText = msg.slice(0, 100);
                                    var hiddenText = msg.slice(100);
                                    var msgHTML = `${visibleText}<span class="d-none">${hiddenText}</span> <button type="button" class="btn btn-link p-0 shadow-none show_more_btn">Show More</button>`;
                                } else {
                                    var msgHTML = msg;
                                }
                            }
                                
                            // if (msg.length > 100) {
                            //     var visibleText = msg.slice(0, 100);
                            //     var hiddenText = msg.slice(100);
                            //     var msgHTML = `${visibleText}<span class="d-none">${hiddenText}</span> <button type="button" class="btn btn-link p-0 shadow-none show_more_btn">Show More</button>`;
                            // } else {
                            //     var msgHTML = msg;
                            // }

                            if ($parent.find('.'+msg_div+channelId).length === 0) {
                                $parent.append(`<div class="d-flex align-items-center ${common_class_name} ${msg_div}${channelId}" style="gap: 12px;">
                                    <div class="nav-vertical-dots flex-shrink-0 pin_message_dot_div"> </div>
                                    <div class="flex-grow-1 pin_message_msg_div"> </div>
                                </div>`);
                            }

                            var pin_msg_count = $(`.${msg_div}${channelId} .pin_message_msg_div pin_msg`).length;
                            var activeClass = "";
                            if(pin_msg_count > 0) {
                                activeClass = "active";
                            }
                            
                            if ($(`.${msg_div}${channelId} .pin_message_msg_div [data-frm_message_id="${frmMessageId}"]`).length === 0) {
                                
                                $(`.${msg_div}${channelId} .pin_message_dot_div`).append(`<div class="message-item-dot ${activeClass}" data-channel_id="${channelId}" data-frm_message_id="${frmMessageId}"></div>`);

                                $(`.${msg_div}${channelId} .pin_message_msg_div`).append(`<div class="pin_msg flex-grow-1 ${(activeClass == "active") ? 'd-flex' : 'd-none'} " data-channel_id="${channelId}" data-frm_message_id="${frmMessageId}">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center" style="gap: 12px;">
                                            <img style="width: 28px; height: 28px; border-radius: 100%;" src="${avatar_image}" alt="">
                                            <div class="p-message">                                
                                                ${msgHTML}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 dropdown mb-auto">
                                        <a class="" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="ri-more-2-fill fs-20" data-bs-toggle="tooltip" data-bs-placement="top" title="More actions"></i>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item d-flex align-items-center justify-content-between goto-message" data-frm_message_key="${frmMessageKey}">Go to Message</a>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between unpin-message" data-chatwith="${chatWith}" data-type="${pinFrom}" data-frm_message_id="${frmMessageId}" data-frm_message_key="${frmMessageKey}" >Unpin</a>
                                        </div>
                                    </div>
                                </div>`);

                                elem.attr('data-action', 0);
                                elem.html('Unpin <i class="bx bx-unlink text-muted ms-2"></i>');
                            }                            
                        } else {
                            //$(`.pin_message_div [data-frm_message_id="${frmMessageId}"]`).remove();

                            $('.pin_msg[data-frm_message_id="' + frmMessageId + '"]').remove();
                            $('.message-item-dot[data-frm_message_id="' + frmMessageId + '"]').remove();                            

                            elem.attr('data-action', 1);
                            elem.html('Pin <i class="bx bx-pin text-muted ms-2"></i>');
                        }
                    // } else {
                    //     var elem = $(`.pin-message-dm[data-frm_message_id="${frmMessageId}"]`);
                    //     if (response.messages[0].pin == 1) {
                    //         if ($(`.pin_message_dm_div [data-frm_message_id="${frmMessageId}"]`).length === 0) {
                    //             console.log("hit inside");
                                
                    //             //$('.pin_message_dm_div').append(`<div data-channel_id="${channelId}" data-frm_message_id="${frmMessageId}"><i class="bx bx-pin text-muted ms-2"></i> ${pinned_by}: ${msg}</div>`);

                    //             $('.pin_message_dm_div').append(`<div data-channel_id="${channelId}" data-frm_message_id="${frmMessageId}" class="pin_msg d-flex justify-content-between align-items-start">
                    //                 <div>
                    //                     <i class="bx bx-pin text-muted ms-2"></i> ${pinned_by}: ${msg}
                    //                 </div>
                    //                 <div class="dropdown">
                    //                     <a class="" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    //                         <i class="ri-more-2-fill fs-20" data-bs-toggle="tooltip" data-bs-placement="top" title="More actions"></i>
                    //                     </a>
                    //                     <div class="dropdown-menu">
                    //                         <a class="dropdown-item d-flex align-items-center justify-content-between goto-message" data-frm_message_key="${frmMessageKey}">Go to Message</a>
                    //                         <a data-frm_message_id="${frmMessageId}" data-frm_message_key="${frmMessageKey}" class="dropdown-item d-flex align-items-center justify-content-between unpin-dm-message">Unpin</a>
                    //                         <a data-chatwith="${pinned_by_ptoken}" class="dropdown-item ${pinned_by_ptoken === my_pToken ? 'd-none' : 'd-flex'} align-items-center justify-content-between open_profile_sidebar">View Profile</a>
                    //                     </div>
                    //                 </div>
                    //             </div>`);

                    //             elem.attr('data-action', 0);
                    //             elem.html('Unpin <i class="bx bx-unlink text-muted ms-2"></i>');
                    //         }
                    //     } else {
                    //         $(`.pin_message_dm_div [data-frm_message_id="${frmMessageId}"]`).remove();
                    //         elem.attr('data-action', 1);
                    //         elem.html('Pin <i class="bx bx-pin text-muted ms-2"></i>');
                    //     }
                    // }

                    await IntaoDB.setItem(objStores.ntw_store.name, {
                        taoh_ntw: chat_messages_key,
                        values: updatedResponse,
                        timestamp: Date.now()
                    });

                    console.log("updated");

                } else {
                    console.warn("Failed response");
                }
            })();
        },
        error: function (xhr, status, err) {
            console.error('Error Fetching activity list : ' + err);
        }
    });    
}

async function getLikeData(ntw_room_key, channelId, frmMessageId, frmMessageKey) {

    let data = {
        ops: 'channel_message',
        action: 'get_like',
        code: _taoh_ops_code,
        key: my_pToken,
        keyslug: ntw_room_key,
        message_id: frmMessageId,
        cfcc60: 1
    };

    $.ajax({
        url: _taoh_cache_chat_url,
        type: 'GET',
        dataType: 'json',
        //headers: {'If-None-Match': ntwChannelListETag},
        data: data,
        success: function (response, textStatus, jqXHR) {
            console.log(response);            
            if (jqXHR.status === 304) return;
            if (response.success) {
                console.log(response);
                
                const chat_messages_key = `frm_${ntw_room_key}_${channelId}`;

                    IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key).then((intao_data) => {

                    if (!intao_data?.values || !intao_data.values.chat[frmMessageKey]) {
                        console.log("not updated");
                        return;
                    }
                    
                    const updatedResponse = { ...intao_data.values };
                    updatedResponse.chat[frmMessageKey].reactions = updatedResponse.chat[frmMessageKey].reactions || [];

                    var elem = $('.emoji_btn[data-frm_message_id="'+frmMessageId+'"]');

                    const grouped = {};
                    response.likelist.forEach(item => {
                        const emoji = item.emoji;
                        updatedResponse.chat[frmMessageKey].reactions.push(item.emoji);
                        grouped[emoji] = (grouped[emoji] || 0) + 1;
                    });

                    for (const emoji in grouped) {                   
                        addReactionToMessage(elem, frmMessageId, frmMessageKey, channelId, emoji, 0, grouped[emoji]);
                    }
                    
                    // response.likelist.forEach(function (likeItem) {
                        
                    // });                   

                    IntaoDB.setItem(objStores.ntw_store.name, {
                        taoh_ntw: chat_messages_key,
                        values: updatedResponse,
                        timestamp: Date.now()
                    });
                    console.log("updated");
                });
                
            } else {

            }
        },
        error: function (xhr, status, err) {
            console.error('Error Fetching activity list : ' + err);
        }
    });    
}
</script>

<script src="<?php echo TAOH_CDN_JS_PREFIX; ?>/chat4/chat-profile.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
<script src="<?php echo TAOH_CDN_JS_PREFIX; ?>/chat4/chat-footer.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
<script src="<?php echo TAOH_CDN_JS_PREFIX; ?>/chat4/chat-common.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>