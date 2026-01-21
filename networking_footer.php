<?php
$actual_curr_page = $curr_page = taoh_parse_url(0);
if($curr_page === 'club'){
    $curr_page = taoh_parse_url(1);
}
defined('TAOH_CURR_APP_IMAGE_SQUARE') || define('TAOH_CURR_APP_IMAGE_SQUARE', TAOH_CDN_MAIN_PREFIX . '/images/nerwork_app_sq.png');
defined('TAOH_CURR_APP_IMAGE') || define('TAOH_CURR_APP_IMAGE', TAOH_CDN_MAIN_PREFIX . '/images/nerwork_app.png');

$displayMsgList = true;
$displayPrivateChat = false;
$is_user_live = false;
$open_profile_chat = false;
$room_info = [];

$ntw_ft_user_info_obj = taoh_user_all_info();

if (isset($curr_page) && ($curr_page == 'room' || $curr_page == 'message' || $curr_page == 'events')) {
    $displayMsgList = false;
}

$privateChatTo_ptoken = '';
$privateChatTo_chat_name = 'Private Chat';
$privateChatTo_avatar = TAOH_OPS_PREFIX . '/avatar/PNG/128/avatar_def.png';
if ($ntw_ft_user_info_obj && $ntw_ft_user_info_obj->profile_complete && isset($curr_page) && ($curr_page == 'profile' || $curr_page == 'profile-html')) {
    $ptoken_from = $ntw_ft_user_info_obj ? $ntw_ft_user_info_obj->ptoken : TAOH_API_TOKEN;
    if($actual_curr_page === 'club'){
        $ptoken_to = taoh_parse_url(2);
    }else{
        $ptoken_to = taoh_parse_url(1);
    }

    if (!empty($ptoken_from) && !empty($ptoken_to) && $ptoken_from != $ptoken_to) {
        if (isset($_GET['from']) && $_GET['from'] == 'messaging') $open_profile_chat = true;

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

        if (!empty($privateChatFrom_arr) && !empty($privateChatTo_arr)) {
            $displayPrivateChat = true;

            $pc_ne_array = array($privateChatFrom_ptoken, $privateChatTo_ptoken);
            sort($pc_ne_array);
            $room_token = implode(',', $pc_ne_array);
            $pc_key_slug = hash('crc32', $room_token);

            $room_kkkey = array('profile', $privateChatFrom_ptoken, $privateChatTo_ptoken);
            sort($room_kkkey);
            $room_data = implode('_', $room_kkkey);

            $room_info = taoh_profile_room_info($pc_key_slug, $ptoken_from);
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
                        'slug' => 'recruiter',
                        'title' => 'Employer',
                    ),
                    array(
                        'slug' => 'seeker',
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

    /*.all-rooms-invite-avatar {
        min-width: 30px !important;
        max-width: 30px !important;
    }*/
</style>

<?php if($displayPrivateChat): ?>
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
                            <button type="button" id="profile_offline_send_message_btn" class="btn btn-sm btn-primary fw-medium m-2" data-toptoken="<?= $privateChatTo_ptoken; ?>">Send Message</button>
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
                            <span class="chat-user-head">Messaging</span>
                            <span id="allRoomsInviteUnreadCount" class="badge badge-pill badge-danger unread-count"></span>
                        </div>
                    </button>
                </h2>
            </div>
            <div id="chatListBottom" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">

                <div class="card-body">
                    <div class="custom-control custom-switch" id="custom_switch" style="display:none;">
                        <input onclick="updateAllInvitesReadStatus()" type="checkbox" class="custom-control-input" id="readSwitch">
                        <label class="custom-control-label" for="readSwitch">Make All Read</label>
                    </div>

                    <div id="messageList">
                        <div id="no_previous_chat_all">
                            <img src="<?= TAOH_CDN_MAIN_PREFIX; ?>/images/no-chat.svg" alt="no-chat">
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
    var _ntw_ft_user_is_logged_in = '<?php echo !empty($ntw_ft_user_info_obj) && !empty($ntw_ft_user_info_obj->ptoken); ?>';
    let _ntw_ft_valid_user = '<?php echo !empty($ntw_ft_user_info_obj) && $ntw_ft_user_info_obj->profile_complete; ?>';
    let _ntw_ft_user_is_ptoken = '<?php echo !empty($ntw_ft_user_info_obj->ptoken); ?>';
    let _ntw_ft_ptoken = _ntw_ft_user_is_logged_in && _ntw_ft_user_is_ptoken ? '<?php echo $ntw_ft_user_info_obj->ptoken; ?>' : '';

    var is_user_live = '<?php echo $is_user_live; ?>';
    var curr_page = '<?php echo $curr_page ?? ''; ?>';
    var displayPrivateChat = '<?php echo $displayPrivateChat; ?>';
    var open_profile_chat = '<?php echo $open_profile_chat; ?>';

    var ft_taoh_api_token = '<?php echo taoh_get_dummy_token(); ?>';

    var readSwitch = $('#readSwitch');

    let ft_userInfoList = {};
    let ft_roomInfoList = {};
    let ntwMessagesETag = null;

    let allRoomsInviteUnreadCount = 0;
    let lastAllMessageTime = 0;

    let privateChatFirstCall = 1;
    let privateChatLastTime = 0;
    let pc_isProcessing = false;
    let pc_reFetchRequired = false;
    let pc_msgUpIndex = 0;
    let pc_msgDownIndex = 0;
    let pc_msgNewEntriesCnt = 0;
    let privateChatPageNo = 1;
    const privateChatItemsPerPage = 10;
    let pc_chatBadgeUpDate = '';
    let pc_chatBadgeDownDate = '';

    if(displayPrivateChat){
        var pToken_from = '<?php echo $ptoken_from ?? ''; ?>';
        var pToken_to = '<?php echo $ptoken_to ?? ''; ?>';
    }

    let pc_room_key = '<?php echo $pc_key_slug ?? ''; ?>';

    let ft_taoh_chat_network = '<?php echo TAOH_CHAT_NETWORK ?? 0; ?>';
    let ft_taoh_chat_forum = '<?php echo TAOH_CHAT_FORUM ?? 3; ?>';


    /****************************======================== Forum Constants ==============================*********************/
    let frmMessagesETag = null;
    let frmReplyMessagesETag = null;

    /****************************======================== Forum Constants ==============================*********************/


    $(document).ready(function(){

        $('#privateChatForm').submit(function (e) {
            e.preventDefault();
            let pc_message_elem = $(this).find('input[name="pc_message"]');
            let message = pc_message_elem.val();
            if (message?.trim()) {
                pc_message_elem.val('');

                // let totalchatscnt = (parseInt(chatCount.text()) || 0) + 1;
                // chatCount.text(totalchatscnt);

                let sent_time = new Date().getTime();

                let data = {
                    'taoh_action': 'taoh_room_send_message',
                    "message": message,
                    'ptoken': pToken_from,
                    'other_ptoken': pToken_to,
                    'key': pc_room_key,
                    'sent_time': sent_time
                };

                let chatresponse = {
                    "chat": [{
                        "ptoken": data.ptoken,
                        "message": data.message,
                        "to_ptoken": data.other_ptoken,
                        "room_hash": data.key,
                        "sent_time": data.sent_time,
                        "time": (data.sent_time * 1000)
                    }],
                    "isTempMsg": true,
                    "overallChatCount": 1,
                    "recent_rendered_items": {}
                };
                renderMessages(chatresponse);

                let chat_temp_messages_key = 'cm_temp_' + pc_room_key + '_' + data.ptoken;
                let ptokenTo = data.other_ptoken;

                // Store temp messages in Indexed DB then send to server
                IntaoDB.getItem(objStores.ntw_store.name, chat_temp_messages_key).then((intao_data) => {
                    let updatedResponse = {};
                    if (intao_data?.values) {
                        updatedResponse = intao_data.values;
                    }
                    if(!(ptokenTo in updatedResponse)) updatedResponse[ptokenTo] = {};
                    Object.assign(updatedResponse[ptokenTo], {[data.sent_time]: chatresponse.chat[0]});
                    IntaoDB.setItem(objStores.ntw_store.name, {taoh_ntw: chat_temp_messages_key, values: updatedResponse, timestamp: Date.now()});
                }).then(() => {
                    $.post(_taoh_site_ajax_url, data, function (response) {
                        let returnedData = JSON.parse(response);
                        pc_reFetchRequired = true;

                        if(returnedData.success){
                            // Update stored_time in temp messages
                            IntaoDB.getItem(objStores.ntw_store.name, chat_temp_messages_key).then((intao_data) => {
                                if (intao_data?.values) {
                                    let updatedResponse = intao_data.values;
                                    if((ptokenTo in updatedResponse) && (returnedData.sent_time in updatedResponse[ptokenTo])){
                                        updatedResponse[ptokenTo][returnedData.sent_time].stored_time = returnedData.stored_time;
                                        IntaoDB.setItem(objStores.ntw_store.name, {taoh_ntw: chat_temp_messages_key, values: updatedResponse, timestamp: Date.now()});
                                    }
                                }
                            });
                        }

                        taoh_pc_post_metrics('chatpost');
                    }).fail(function () {
                        console.log("Network issue on post message!");
                    });
                });
            }
        });

        $('#privateChatList').one('shown.bs.collapse', function () {
            let chatContainer = document.getElementById('privatechat');
            chatContainer.scrollTop = chatContainer.scrollHeight;
            // this.scrollIntoView();
        });


        $('#privateChat_collapse_btn').on('click', function (e) {
            e.preventDefault();
            $('#privateChatList').collapse('toggle');
        });

        $('#pc_video').on('click', function (e) {
            e.preventDefault();

            if(displayPrivateChat){
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
                if(privateChatTo_chat_name != ''){
                    confirmMsg = 'Please confirm that you want to start a video chat with ' + privateChatTo_chat_name + '?';
                }else {
                    confirmMsg = 'Please confirm that you want to start a video chat?';
                }

                taoh_set_warning_message(confirmMsg, false, 'toast-middle', [
                    {
                        text: 'OK',
                        action: () => {
                            $.post(_taoh_site_ajax_url, data, function (response) {
                                let video_chat_link = 'Want to chat? <div class="chat-meeting"><i class="la la-video"></i><div><p>' + privateChatTo_chat_name + '\'s meeting</p><a href="' + response.other_link + '" target="_blank" class="chat-meeting-link"> Join video meeting</a></div></div>';
                                $('#pc_message').val(video_chat_link);
                                $('#pc_send_btn').trigger('click');
                                $('#pc_video').removeClass('la-spinner la-spin').addClass('la-video');
                                if(response.my_link) window.open(response.my_link);
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
                //         cancel: function (){
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
                //                     if(response.my_link) window.open(response.my_link);
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

        $('#profile_offline_send_message_btn').on('click', function () {
            let toPtoken = $(this).data('toptoken');
            let respondPtoken = _ntw_ft_ptoken ?? '';

            if(toPtoken?.trim() !== ''){
                $('#profileOfflineMessage').val('');
                $('#profileOfflineToPtoken').val(toPtoken);
                $('#profileOfflineLocationPath').val('/profile/' + respondPtoken);
                $('#profileOfflineSuccessMessage').hide();
                $('#profileOfflineMessageBlock').show();
                $('#profileOfflineMessageModal').modal('show');
            }
        });

        if (displayPrivateChat) {
            if (pToken_to) {
                setInterval(function () {
                    if (typeof getUserLiveStatus === 'function') {
                        getUserLiveStatus(pToken_to).then((networkingLiveStatus) => {
                            if (networkingLiveStatus.success) {
                                is_user_live = Boolean(networkingLiveStatus.output);

                                if (is_user_live) {
                                    $('#send_email_blk').css('display', 'none');
                                    $('#pc_video').css('display', 'block');
                                    $('#privateChatForm').css('display', 'block');
                                    $('#user_active_status').removeClass('active-status-border').addClass('active-status');
                                } else {
                                    $('#privateChatForm').css('display', 'none');
                                    $('#pc_video').css('display', 'none');
                                    $('#send_email_blk').css('display', 'block');
                                    $('#user_active_status').removeClass('active-status').addClass('active-status-border');
                                }
                            }
                        });
                    }
                }, 30000);
            }

            if(open_profile_chat){
                let requestData = {
                    "pToken_from": pToken_from,
                    "pToken_to": pToken_to,
                    "page": privateChatPageNo,
                    "itemPerPage": privateChatItemsPerPage,
                    "callFromEvent": "interval"
                };
                fetchChatData(requestData, true);
                $('#privateChatList').collapse('show');
            }

        }

        if (_ntw_ft_valid_user && _ntw_ft_ptoken !== '') {
            IntaoDB.getItem(objStores.ntw_store.name, 'ft_ntw_networking_misc').then((intao_data) => {
                if (intao_data && intao_data.last_update_time) {
                    lastNTWMsgCheckedTimestamp = intao_data.last_update_time;
                } else {
                    lastNTWMsgCheckedTimestamp = 0;
                }
                getAndSetLastCheckedTimestamp('lastNTWMsgCheckedTimestamp', lastNTWMsgCheckedTimestamp, 2);
            }).then(() => {
                const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastNTWMsgCheckedTimestamp', lastNTWMsgCheckedTimestamp, 1);
                taohNTWMessagesFromServer(getNetworkingMessagesFormData(_ntw_ft_ptoken, lastCheckedTimestamp));
            });
        }
    });

    /* Get Room Info From Server */
    async function fetchRoomInfoFromServer(formData, maxRetries = 3) {
        if (!formData.keyslug) return Promise.reject('Room key is required');

        let room_info_key = 'room_info_list';
        const delay = 2000;
        let retries = 0;

        return new Promise((resolve, reject) => {
            function sendRoomInfoRequest() {
                if (!navigator.onLine) {
                    return handleError(reject, 'offline', 'You are offline. Please check your internet connection.');
                }

                retries++;

                $.post(_taoh_cache_chat_proc_url, formData, function (response) {
                    try {
                        let srv_roominfoObj = JSON.parse(response);

                        if (srv_roominfoObj.success) {
                            let roominfoObj = srv_roominfoObj['output'];

                            if (roominfoObj?.hasOwnProperty('keyslug')) {
                                roominfoObj.last_fetch_time = Date.now();
                                roomInfoList[roominfoObj.keyslug] = roominfoObj;

                                IntaoDB.getItem(objStores.common_store.name, room_info_key).then((intao_data) => {
                                    let updatedResponse = intao_data?.values || {};

                                    updatedResponse[roominfoObj.keyslug] = roominfoObj;
                                    IntaoDB.setItem(objStores.common_store.name, {
                                        taoh_common: room_info_key,
                                        values: updatedResponse,
                                        timestamp: Date.now()
                                    });
                                });

                                return resolve(roominfoObj);
                            } else {
                                return handleError(reject, 'room_not_exist', 'Invalid room info request!');
                            }
                        } else {
                            return handleError(reject, 'fail', 'Invalid room info request!');
                        }
                    } catch (e) {
                        return handleError(reject, 'invalid_response', e);
                    }
                }).fail(function (e) {
                    return handleError(reject, 'network_failure', e);
                });
            }

            function handleError(reject, errorType, errorMessage) {
                if (retries < maxRetries && errorType !== 'room_not_exist') {
                    setTimeout(sendRoomInfoRequest, delay);
                } else {
                    reject({ error: errorType, message: errorMessage });
                }
            }

            sendRoomInfoRequest(); // Start the first request
        });
    }



    async function ft_getUserInfo(pToken_to, ops = 'public', serverFetch = false) {
        if (!pToken_to.trim()) return null;

        let userInfo = {};

        // Initialize ops object if not exists
        ft_userInfoList[ops] = ft_userInfoList[ops] || {};

        if (!serverFetch) {
            // Try to get userInfo from local variable
            userInfo = ft_userInfoList[ops][pToken_to] || userInfo;

            // Try to get userInfo from global variable
            if (!userInfo.ptoken) {
                userInfoList[ops] = userInfoList[ops] || {};
                userInfo = userInfoList[ops][pToken_to] || userInfo;
            }

            // Try to get userInfo from IndexedDB
            if (!userInfo.ptoken) {
                const user_info_key = 'user_info_list';
                const intao_data = await IntaoDB.getItem(objStores.common_store.name, user_info_key);
                if (intao_data?.values && intao_data.values[ops] && intao_data.values[ops][pToken_to]) {
                    let userInfoObj = intao_data.values[ops][pToken_to];
                    // Check if data is expired (expires after 2 day)
                    if (userInfoObj.last_fetch_time && (Date.now() - userInfoObj.last_fetch_time) <= 172800000) {
                        ft_userInfoList[ops][userInfoObj.ptoken] = userInfoObj;
                        userInfo = userInfoObj;
                    }
                }
            }
        }

        // Fetch userInfo from server if not found locally
        if (!userInfo.ptoken) {
            const formData = {
                taoh_action: 'taoh_user_info',
                ops: ops,
                ptoken: pToken_to
            };

            try {
                const srv_userInfoObj = await fetchUserInfoFromServer(formData);
                srv_userInfoObj.last_fetch_time = Date.now();
                ft_userInfoList[ops][srv_userInfoObj.ptoken] = srv_userInfoObj;
                userInfo = srv_userInfoObj;
            } catch (e) {
                console.log('getUserInfo error:', e);
            }
        }

        // If userInfo not found, set default values
        if (!userInfo.ptoken) {
            userInfo = {
                ptoken: pToken_to,
                chat_name: 'Unknown Name',
                avatar: 'default',
                full_location: 'Unknown Location',
                type: 'Unknown Type',
                is_unknown: true,
                last_fetch_time: Date.now()
            };
            ft_userInfoList[ops][userInfo.ptoken] = userInfo;
        }

        return userInfo;
    }

    async function ft_getRoomInfo(room_hash, ptoken, data = {}, serverFetch = false) {
        if (room_hash.trim() === '') {
            return {};
        }

        let roominfo = {};
        let app = data['app'] || 'custom';
        let type = data['type'] || 'detail';

        if (!serverFetch) {
            // Try to get roominfo from local variable
            if (typeof ft_roomInfoList[room_hash] !== 'undefined') {
                roominfo = ft_roomInfoList[room_hash];
            }

            // Try to get roominfo from global variable
            if (!roominfo.keyslug && typeof roomInfoList[room_hash] !== 'undefined') {
                roominfo = roomInfoList[room_hash];
            }

            // Try to get roominfo from IndexedDB
            if (!roominfo.keyslug) {
                const room_info_key = 'room_info_list';
                const intao_data = await IntaoDB.getItem(objStores.common_store.name, room_info_key);
                if (intao_data && typeof intao_data.values[room_hash] !== 'undefined') {
                    let roominfoObj = intao_data.values[room_hash];

                    // Check if data is expired (expires after 5 min ((5 * 60) * 1000))
                    if (roominfoObj.last_fetch_time && (Date.now() - roominfoObj.last_fetch_time) <= 300000) {
                        ft_roomInfoList[room_hash] = roominfoObj;
                        roominfo = roominfoObj;
                    }
                }
            }
        }

        // Fetch roominfo from server if it does not exist locally
        if (!roominfo.keyslug) {
            const formData = {
                "ops": "status",
                "status": "getroom",
                "code": _taoh_ops_code,
                "key": ptoken,
                "keyslug": room_hash,
                "app": app,
                "type": type
            };

            try {
                let srv_roominfoObj = await fetchRoomInfoFromServer(formData);
                if (srv_roominfoObj?.hasOwnProperty('keyslug')) {
                    srv_roominfoObj.last_fetch_time = Date.now();
                    ft_roomInfoList[srv_roominfoObj.keyslug] = srv_roominfoObj;
                    roominfo = srv_roominfoObj;
                }
            } catch (err) {
                if (err.error === 'room_not_exist' && _ntw_ft_ptoken?.trim()) {
                    let create_room_info = {
                        keyslug: room_hash,
                        app: 'global',
                        club: {
                            title: 'Networking Chat',
                            description: 'Welcome to the Networking Chat Room',
                            short: 'Connect with your friends.',
                            image: '<?php echo defined(' TAOH_CURR_APP_IMAGE') ? TAOH_CURR_APP_IMAGE : ''; ?>',
                            square_image: '<?php echo defined(' TAOH_CURR_APP_IMAGE_SQUARE') ? TAOH_CURR_APP_IMAGE_SQUARE : ''; ?>',
                            links: {
                                club: '/club'
                            }
                        }
                    };
                    // let create_room_data = await taoh_create_room(create_room_info, _ntw_ft_ptoken, data);
                    // if (create_room_data.success && create_room_data.output) {
                        roominfo = create_room_info;
                        ft_roomInfoList[room_hash] = roominfo;
                    // }
                } else {
                    console.log('getRoomInfo error:', err.message);
                }
            }
        }

        // If roominfo not found, set default values
        if (!roominfo.keyslug) {
            roominfo = {
                keyslug: room_hash,
                club: {
                    title: 'Networking Chat',
                    links: {
                        club: '/club'
                    }
                },
                is_unknown: true,
                last_fetch_time: Date.now()
            };
            ft_roomInfoList[room_hash] = roominfo;
        }

        return roominfo;
    }


    /* Private chat */
    const chatContainer = document.getElementById('privatechat');

    function getChatRequestData(pToken_from, pToken_to, callFromEvent = 'init') {
        return {
            "pToken_from": pToken_from,
            "pToken_to": pToken_to,
            "page": privateChatPageNo,
            "itemPerPage": privateChatItemsPerPage,
            "callFromEvent": callFromEvent
        };
    }

    function fetchChatData(requestData, serverFetch = false) {
        if (requestData.pToken_from && requestData.pToken_to) {
            // pc_isProcessing = true;
            // let formData = getNetworkingMessagesFormData(requestData.pToken_from, getAndSetLastCheckedTimestamp('lastNTWMsgCheckedTimestamp', lastNTWMsgCheckedTimestamp, 1));

            let chat_messages_key = 'cm_' + pc_room_key + '_' + requestData.pToken_from + '_' + requestData.pToken_to;

            IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key).then((intao_data) => {
                if (intao_data && intao_data.timestamp) {
                    processPrivateChatData(requestData, intao_data.values);
                } else {
                    // Sending default empty data to render chat window
                    const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastNTWMsgCheckedTimestamp', lastNTWMsgCheckedTimestamp, 1);
                    processPrivateChatData(requestData, {"chat": {}, "last_update_time": lastCheckedTimestamp, "success": true});
                }
            });
        }
    }

    function processPrivateChatData(requestData, response) {
        let processedResponse = {};
        let recentItemsObject = {};
        let recentRenderedItemsObject = {};
        let totalchats = 0;

        if (response.success) {
            let chats = response.chat ? response.chat : [];

            let allChatKeys = Object.keys(chats);
            totalchats = allChatKeys.length;
            if (totalchats > 0) {
                if (requestData.callFromEvent == "init" || pc_msgDownIndex == 0) {
                    const slice_end = (totalchats - ((requestData.page - 1) * requestData.itemPerPage));
                    if (slice_end > 0) {
                        const recentItems = Object.entries(chats).slice(-(requestData.page * requestData.itemPerPage), slice_end);
                        recentItemsObject = Object.fromEntries(recentItems);
                    }
                } else if (requestData.callFromEvent == "scrollup") {
                    let currentFirstMsgIndex = allChatKeys.indexOf(pc_msgUpIndex);
                    if (currentFirstMsgIndex > -1) {
                        const recentItems = Object.entries(chats).slice(-(requestData.page * requestData.itemPerPage), currentFirstMsgIndex);
                        recentItemsObject = Object.fromEntries(recentItems);
                    }
                } else if (requestData.callFromEvent == "interval") {
                    let currentLastMsgIndex = allChatKeys.indexOf(pc_msgDownIndex);
                    if (currentLastMsgIndex > -1 && currentLastMsgIndex < (totalchats - 1)) {
                        const recentItems = Object.entries(chats).slice(-(totalchats - (currentLastMsgIndex + 1)), totalchats);
                        recentItemsObject = Object.fromEntries(recentItems);
                    }

                    const recentRenderedItems = Object.entries(chats).slice(-20);
                    recentRenderedItemsObject = Object.fromEntries(recentRenderedItems);
                }
            }
        }

        processedResponse.isTempMsg = false;
        processedResponse.callFromEvent = requestData.callFromEvent;
        processedResponse.chat = recentItemsObject;
        processedResponse.overallChatCount = totalchats;
        processedResponse.recent_rendered_items = recentRenderedItemsObject;
        processedResponse.last_update_time = response.last_update_time;

        renderMessages(processedResponse);
    }

    async function compiledMsgHtml(cd){
        let safeMessageHtml = '';
        const message_content = cd.message;
        if (message_content.includes('chat-meeting-link')) {
            safeMessageHtml = message_content;
        } else {
            const safeMessage = document.createElement('pre');
            safeMessage.textContent = message_content;
            safeMessageHtml = safeMessage.innerHTML.replace(/\n/g, '<br>')
                .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');
        }

        return `<li class="${cd.pToken_to === cd.pToken_from ? 'right' : 'left'} ${'msg_' + (cd.time)} clearfix ${cd.isTempMsg ? 'pcTempMsg new' : ''}" id="${'msg_' + (cd.time)}">
            <div class="chat-body clearfix">
                <div class="header">
                    <strong class="text-primary pc_chat_name" title="${cd.name}">${cd.name}</strong>
                    <small class="pull-right text-muted pl-2"><i class="fa fa-clock-o"></i> ${cd.formatted_time}</small>
                </div>
                <p> ${safeMessageHtml} </p>
            </div>
        </li>`;
    }

    async function getChatMsgHtml(response, chats, tempMsgList, isTempMsg) {
        let messageHtml = '';
        let ptokenTo = pToken_to;
        let do_highlight = false;
        let compiledChatKeys = {chats: [], temp_chats: []};
        let firstChatDateupdated = false;

        for (const [key, v] of Object.entries(chats)) {
            const userInfo = await ft_getUserInfo(v.ptoken);
            let stored_time = v.hasOwnProperty('stored_time') ? v.stored_time : '';

            if(v.ptoken != pToken_from){
                if (!do_highlight && response.callFromEvent !== 'init' && response.callFromEvent != 'scrollup') do_highlight = true;
            }

            let chatDateTime_arr = formatBadgeDateTime((v.time / 1000), _taoh_user_timezone);
            if (chatDateTime_arr) {
                if(response.callFromEvent == 'scrollup' && chatDateTime_arr[0] == pc_chatBadgeUpDate){
                    $('#chat_msg_list .date-badge:first').remove();
                    pc_chatBadgeUpDate= '';
                }

                if (response.callFromEvent == 'scrollup' && chatDateTime_arr[0] != pc_chatBadgeUpDate) {
                    pc_chatBadgeUpDate = chatDateTime_arr[0];
                    messageHtml += '<li class="date-badge clearfix"><span class="badge badge-secondary">' + pc_chatBadgeUpDate + '</span></li>';
                } else if (response.callFromEvent != 'scrollup' && chatDateTime_arr[0] != pc_chatBadgeDownDate) {
                    if (!firstChatDateupdated) {
                        pc_chatBadgeUpDate = chatDateTime_arr[0];
                        firstChatDateupdated = true;
                    }
                    pc_chatBadgeDownDate = chatDateTime_arr[0];
                    messageHtml += '<li class="date-badge clearfix"><span class="badge badge-secondary">' + pc_chatBadgeDownDate + '</span></li>';
                }
            }

            let compileData = {
                pToken_to: v.ptoken,
                message: v.message,
                time: v.time,
                name: userInfo.chat_name,
                pToken_from: pToken_from,
                isTempMsg: isTempMsg,
                stored_time: stored_time,
                formatted_time: chatDateTime_arr[1] ?? ''
            };
            messageHtml += await compiledMsgHtml(compileData);

            compiledChatKeys.chats.push((isTempMsg ? (v.time).toString() : key));

            const tempMsgElem = $('#msg_' + (v.sent_time * 1000));
            if (tempMsgElem.length) tempMsgElem.removeClass('new');
        }

        if (!isTempMsg && typeof tempMsgList[ptokenTo] != "undefined") {
            let allSentTimes = Object.keys(chats).map(k => chats[k]['sent_time']);
            for (const [key, v] of Object.entries(tempMsgList[ptokenTo])) {
                const userInfo = await ft_getUserInfo(v.ptoken);
                let stored_time = v.hasOwnProperty('stored_time') ? (v.stored_time).toString() : '';

                let tempChatDateTime_arr = formatBadgeDateTime((v.time / 1000), _taoh_user_timezone);

                if (!allSentTimes.includes((v.sent_time).toString())) {
                    let compileData = {
                        pToken_to: v.ptoken,
                        message: v.message,
                        time: v.time,
                        name: userInfo.chat_name,
                        pToken_from: pToken_from,
                        isTempMsg: true,
                        stored_time: stored_time,
                        formatted_time: tempChatDateTime_arr[1] ?? ''
                    };
                    messageHtml += await compiledMsgHtml(compileData);

                    compiledChatKeys.temp_chats.push(key);
                }

                const tempMsgElem = $('#msg_' + (v.sent_time * 1000));
                if (tempMsgElem.length) tempMsgElem.removeClass('new');
            }
        }


        return {messageHtml, compiledChatKeys, do_highlight};
    }

    function renderMessages(response) {
        if (response) {
            let chats = response.chat ? response.chat : [];
            let isTempMsg = response.isTempMsg ? response.isTempMsg : false;

            let allChatKeys = Object.keys(chats);
            let totalchats = allChatKeys.length;
            if (chats && totalchats > 0) {
                $('#no_pc_message').remove();

                let chat_temp_messages_key = 'cm_temp_' + pc_room_key + '_' + pToken_from;
                IntaoDB.getItem(objStores.ntw_store.name, chat_temp_messages_key).then((intao_data) => {
                    let tempMsgList = {};
                    if (intao_data?.values) {
                        tempMsgList = intao_data.values;
                    }

                    return tempMsgList;

                }).then((tempMsgList) => {
                    getChatMsgHtml(response, chats, tempMsgList, isTempMsg).then(({messageHtml, compiledChatKeys, do_highlight}) => {
                        if (privateChatFirstCall == 1 || response.callFromEvent === 'init') {
                            $('#chat_msg_list').empty();
                        }

                        $('.pcTempMsg:not(.new)').remove();

                        if (response.callFromEvent === 'scrollup') {
                            $('#chat_msg_list').prepend(messageHtml);
                        } else {
                            $('#chat_msg_list').append(messageHtml);
                            chatContainer.scrollTop = chatContainer.scrollHeight;
                        }

                        if (response.callFromEvent === 'init') {
                            pc_msgUpIndex = allChatKeys[0];
                            pc_msgDownIndex = allChatKeys.pop();
                        } else if (response.callFromEvent === 'scrollup') {
                            pc_msgUpIndex = allChatKeys[0];
                        } else if (response.callFromEvent === 'interval') {
                            pc_msgDownIndex = allChatKeys.pop();
                            pc_msgNewEntriesCnt = Math.max(0, pc_msgNewEntriesCnt - totalchats);
                        }

                        privateChatFirstCall = 0;

                        response.compiledChatKeys = compiledChatKeys;

                        // Clearing Temp messages if already rendered
                        if ($('.pcTempMsg').length) {
                            let recent_rendered_items = response.recent_rendered_items;
                            for (const [k, value] of Object.entries(recent_rendered_items)) {
                                let tempMsgElem = $('.msg_' + (value.sent_time * 1000));
                                if (tempMsgElem.length) tempMsgElem.remove();
                            }
                        }

                        if (do_highlight) {
                            $('#privatechat').addClass("highlight_msg");
                            setTimeout(function () {
                                $('#privatechat').removeClass('highlight_msg');
                            }, 5000);
                        }

                        pc_isProcessing = false;

                    }).then(() => {
                        doAfterMsgRender(response);
                    });
                });
            } else {
                doAfterMsgRender(response);
            }
        } else {
            doAfterMsgRender(response);
        }
    }

    function doAfterMsgRender(response){
        let overallChatCountWithTemp = response.overallChatCount;
        if(typeof response.compiledChatKeys != 'undefined'){
            overallChatCountWithTemp += response.compiledChatKeys.temp_chats.length;
        }
        if (overallChatCountWithTemp > 0) {

        } else {
            $('#chat_msg_list').html("<li id='no_pc_message'>No Messages yet!</li>");
        }

        if (response.hasOwnProperty('last_update_time')) {
            privateChatLastTime = response.last_update_time || 0;
        }

        updateChatSendArea(pc_room_key, pToken_to);

        taohLoader(document.getElementById('pc_loader'), false);
    }

    function updateChatSendArea(pc_room_key, pToken_to) {
        if (pc_room_key.trim() !== '' && pToken_to.trim() !== '') {
            if (typeof getUserLiveStatus === 'function') {
                getUserLiveStatus(pToken_to).then((networkingLiveStatus) => {
                    if (networkingLiveStatus.success) {
                        is_user_live = Boolean(networkingLiveStatus.output);

                        if (is_user_live) {
                            $('#send_email_blk').css('display', 'none');
                            $('#pc_video').css('display', 'block');
                            $('#privateChatForm').css('display', 'block');
                            $('#user_active_status').removeClass('active-status-border').addClass('active-status');
                        } else {
                            $('#privateChatForm').css('display', 'none');
                            $('#pc_video').css('display', 'none');
                            $('#send_email_blk').css('display', 'block');
                            $('#user_active_status').removeClass('active-status').addClass('active-status-border');
                        }
                    }
                });
            }
        }
    }

    if (displayPrivateChat) {
        let prevScrollPos = chatContainer.scrollTop;
        chatContainer.addEventListener('scroll', () => {
            const currentScrollPos = chatContainer.scrollTop;

            if ((currentScrollPos < prevScrollPos) && currentScrollPos < 10) {
                // Trigger only if the user scrolls up to the top of the chat container
                privateChatPageNo++;

                taohLoader(document.getElementById('pc_loader'), true);
                let requestData = getChatRequestData(pToken_from, pToken_to, 'scrollup');
                throttle(fetchChatData(requestData, false), (pc_isProcessing ? 500 : 100));
            }

            // Update the previous scroll position to find the direction of scrolling
            prevScrollPos = currentScrollPos;
        });

        setInterval(function () {
            if (!pc_isProcessing) { // (pc_msgNewEntriesCnt > 0) &&
                if (pToken_from.trim() !== '' && pToken_to.trim() !== '') {
                    let requestData = getChatRequestData(pToken_from, pToken_to, 'interval');
                    fetchChatData(requestData, false);
                }
            }
        }, 3000);

        // Initial call to load the first set of messages
        let requestData = getChatRequestData(pToken_from, pToken_to, 'init');
        fetchChatData(requestData, false);

    }

    /* /Private chat */


    /* All Rooms Message Notifications */
    function updateAllInvitesReadStatus() {
        if (readSwitch.is(":checked") && _ntw_ft_ptoken.trim() !== '') {
            let my_invites_key = 'invites_' + _ntw_ft_ptoken;
            IntaoDB.getItem(objStores.ntw_store.name, my_invites_key).then((intao_data) => {
                if (intao_data?.values) {
                    let updatedResponse = intao_data.values;
                    for (const [k, value] of Object.entries(updatedResponse)) {
                        let to_ptoken = (value.ptoken_from == _ntw_ft_ptoken) ? value.ptoken_to : value.ptoken_from;
                        if (value.read == 0) {
                            value.read = 1;
                            $('.rooms_' + value.room_hash + '_' + to_ptoken).css('background', 'inherit');
                        }
                    }
                    allRoomsInviteUnreadCount = 0;
                    $('#allRoomsInviteUnreadCount').text('');
                    IntaoDB.setItem(objStores.ntw_store.name, {taoh_ntw: my_invites_key, values: updatedResponse, timestamp: Date.now()});
                }
            });
        }
    }

    function updateInvitesReadStatus(room_hash, my_pToken, ptoken_to, item_key = '') {
        let my_invites_key = 'invites_' + my_pToken;
        IntaoDB.getItem(objStores.ntw_store.name, my_invites_key).then((intao_data) => {
            if (intao_data?.values) {
                let updatedResponse = intao_data.values;
                if (item_key.toString() !== '' && item_key in updatedResponse) {
                    updatedResponse[item_key].read = 1;
                } else {
                    for (const [k, value] of Object.entries(updatedResponse)) {
                        if (value.room_hash == room_hash && value.invite_from == ptoken_to) {
                            updatedResponse[k].read = 1;
                            break;
                        }
                    }
                }
                IntaoDB.setItem(objStores.ntw_store.name, {taoh_ntw: my_invites_key, values: updatedResponse, timestamp: Date.now()});
            }

            /*$.ajax({
                url: _taoh_cache_chat_proc_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    ops: 'group_message',
                    status: 'update',
                    code: _taoh_ops_code,
                    keyslug: room_hash,
                    key: my_pToken,
                    with: ptoken_to,
                    sent_time: Date.now(),
                    read: 1
                },
                success: function (res, textStatus, jqXHR) {
                    console.log(res);
                },
                error: function (xhr, status, error) {
                    console.log(error);
                }
            });*/
        });
    }

    function getNTWAllRoomsInvites() {
        const ft_my_pToken = "<?php echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>";
        if(ft_my_pToken){
            let my_invites_key = 'invites_' + ft_my_pToken;

            IntaoDB.getItem(objStores.ntw_store.name, my_invites_key).then((intao_data) => {
                // Check if data is expired (expires after 2 week ((14 * 24) * 60 * 60 * 1000))
                if (intao_data && intao_data.timestamp && !((Date.now() - intao_data.timestamp) > 1209600000)) {
                    let roomInvitesAll = intao_data.values;

                    renderNTWAllRoomsInvites(roomInvitesAll);
                }else{
                    if (intao_data) IntaoDB.removeItem(objStores.ntw_store.name, my_invites_key);
                }
            });
        }
    }

    async function getNTWAllRoomsInvitesHtml(invites) {
        let allInvitesHtml = '';
        let allRoomsInviteUnreadCountNow = 0;

        for (const [k, invite] of Object.entries(invites)) {
            if(invite.read == 0){
                allRoomsInviteUnreadCountNow++;
            }

            const [room_info, userInfo] = await Promise.all([
                ft_getRoomInfo(invite.room_hash, _ntw_ft_ptoken).catch((e) =>{console.log(e)}),
                ft_getUserInfo(invite.invite_from).catch((e) =>{console.log(e)}),
            ]);

            let new_class_side = 'rooms_' + invite.room_hash + '_' + invite.invite_from;
            let message_content = invite.message;

            const userAvatarSrc = userInfo?.avatar_image && await checkImageExists(userInfo.avatar_image).catch(() => false)
                ? userInfo.avatar_image
                : `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;

            // read read_${invite.read}
            allInvitesHtml += `<div class="card-item rooms-invite ${new_class_side} bottom-chat-list mb-2" data-room="${invite.room_hash}" data-invitefrom="${invite.invite_from}" data-read="${invite.read}" data-item="${k}" style="${invite.read ? '' : 'background: rgba(50, 205, 50, 0.3)'} ">
                    <div class="d-flex">
                        <div class="col-2 p-0">
                            <a href="${_taoh_site_url_root + '/profile/' + invite.invite_from}">
                                <img src="${userAvatarSrc}" alt="avatar">
                            </a>
                        </div>
                        <div class="col-10 chat-user-list-bottom">
                            <div class="d-flex">
                                <h5 class="col p-0 user-title"><a target="_blank" href="${_taoh_site_url_root + '/profile/' + invite.invite_from}">${userInfo.chat_name}</a></h5>
                                <p class="chat-date-time-count" style="display:block"><span>${timeAgo(invite.message_time / 1000)}</span></p>
                            </div>
                            <div class="rooms-invite-content">
                                <p class="room_title text-info font-italic">${((typeof room_info['club'] != 'undefined') ? room_info['club'].title : 'Room - ' + room_info['keyslug'])}</p>
                                <p class="card-text" style="color:#0d233e">${message_content.includes('chat-meeting-link') ? message_content : truncateMessage(message_content, 60)}</p>
                            </div>
                        </div>
                    </div>
                </div>`;
        }

        return {allInvitesHtml,allRoomsInviteUnreadCountNow};
    }

    async function renderNTWAllRoomsInvites(invites) {
        let messageList_elem = $('#messageList');

        if (Object.keys(invites).length > 0) {
            $('#no_previous_chat_all').remove();
            $('#custom_switch').show();

            const {allInvitesHtml,allRoomsInviteUnreadCountNow} = await getNTWAllRoomsInvitesHtml(invites);

            messageList_elem.html(allInvitesHtml);

            if(allRoomsInviteUnreadCountNow > 0 && allRoomsInviteUnreadCountNow > allRoomsInviteUnreadCount){
                allRoomsInviteUnreadCount = allRoomsInviteUnreadCountNow;
                if (readSwitch.prop('checked')) readSwitch.prop('checked', false);
                $('.global_message').addClass("highlight-green");
                setTimeout(function () {
                    $('.global_message').removeClass('highlight-green');
                }, 10000);
            }

            $('#allRoomsInviteUnreadCount').text((allRoomsInviteUnreadCount > 0 ? allRoomsInviteUnreadCountNow : ''));

        } else {
            $('#allRoomsInviteUnreadCount').text('');
            $('#custom_switch').hide();
            messageList_elem.html(`<div id="no_previous_chat_all">
                <img src="${_taoh_site_url_root + '/assets/images/no-chat.svg'}" alt="no-chat">
                <div class="pt-3 pb-3">
                <p class="mb-4 fs-22">No messages yet</p>
                <p>Reach out and start a conversation to advance your career.</p>
                </div>
            </div>`);
        }
    }

    if (_ntw_ft_valid_user && curr_page !== 'room' && curr_page !== 'message') {
        getNTWAllRoomsInvites();
        setInterval(function () {
            getNTWAllRoomsInvites();
        }, 5000);
    }

    $(document).on('click', '#messageList .rooms-invite-content', function () {
        const rooms_invite_elem = $(this).closest('.rooms-invite');
        const room_hash = rooms_invite_elem.data('room');
        const invite_from = rooms_invite_elem.data('invitefrom');

        if (typeof rooms_invite_elem.data('read') !== 'undefined') {
            if (rooms_invite_elem.data('read') == 0) {
                const allRoomsInviteUnreadCount_elem = $('#allRoomsInviteUnreadCount');
                const item_key = rooms_invite_elem.data('item') ?? '';
                let allRoomsInviteUnreadCountNow = parseInt(allRoomsInviteUnreadCount_elem.text()) - 1 || 0;
                allRoomsInviteUnreadCount_elem.text((allRoomsInviteUnreadCountNow > 0 ? allRoomsInviteUnreadCountNow : ''));
                allRoomsInviteUnreadCount = allRoomsInviteUnreadCountNow;
                rooms_invite_elem.data('read', 1);
                rooms_invite_elem.css("background", "inherit");
                updateInvitesReadStatus(room_hash, _ntw_ft_ptoken, invite_from, item_key);
            }
        }

        const link = _taoh_site_url_root + '/message/chatwith/' + room_hash + '-' + invite_from + '?from=messaging';
        window.open(link, '_blank');
    });

    /* /All Rooms Message Notifications */

    function updateGlobalRoomsMessages(oldData, newData) {
        const mergedData = { ...oldData };

        for (const key in newData) {
            const newItem = newData[key];
            const existingItem = Object.values(mergedData).find((item) => {
                let conversation_users = [item.ptoken_from, item.ptoken_to];
                return item.room_hash === newItem.room_hash && conversation_users.includes(newItem.ptoken_from) && conversation_users.includes(newItem.ptoken_to);
            });

            if (existingItem) {
                // Delete the existing item and add new item to mergedData
                for (const mergedKey in mergedData) {
                    if (mergedData[mergedKey] === existingItem) {
                        delete mergedData[mergedKey];
                        mergedData[key] = newItem;
                        break;
                    }
                }
            } else {
                // Add the new item to mergedData
                mergedData[key] = newItem;
            }
        }

        return mergedData;
    }

    function updateInvitesMessages(oldData, newData) {
        const mergedData = { ...oldData };

        for (const key in newData) {
            const newItem = newData[key];
            const existingItem = Object.values(mergedData).find((item) => {
                let conversation_users = [item.ptoken_from, item.ptoken_to];
                return item.room_hash === newItem.room_hash && conversation_users.includes(newItem.ptoken_from) && conversation_users.includes(newItem.ptoken_to);
            });

            if (existingItem) {
                // Replace the existing item with the new item
                for (const mergedKey in mergedData) {
                    if (mergedData[mergedKey] === existingItem) {
                        delete mergedData[mergedKey];
                        mergedData[key] = newItem;
                        break;
                    }
                }
            } else {
                // Add the new item to mergedData
                mergedData[key] = newItem;
            }
        }

        return mergedData;
    }

    async function clearCacheProcess(cacheData) {
        let cacheFilesToRemove = [];

        for (let [key, item] of Object.entries(cacheData)) {
            let alreadyProcessed = false;
            let allowCacheFilesToRemove = true;

            if (item.action === 'update_site_config') {
                //window.location.href = '<?php //echo TAOH_SITE_URL_ROOT ;?>/?clear=config';
                alreadyProcessed = true;
                allowCacheFilesToRemove = false;
                return false;
            }
            if (item.action === 'profile_update') {
                const opsList = ['info', 'full', 'public', 'cell'];
                if (Array.isArray(item.value) && item.value.length > 0) {
                    const value = item.value.find(v => {
                        const valueArr = v.split('_');
                        return valueArr.length > 1 && (valueArr[1].trim() === 'detail' || opsList.includes(valueArr[1].trim()));
                    });

                    if (value) {
                        const user_info_list_key = 'user_info_list';
                        const ptoken = value.split('_').pop().trim();
                        const intaoData = await IntaoDB.getItem(objStores.common_store.name, user_info_list_key);
                        let userInfoList = intaoData ? intaoData.values : {};

                        opsList.forEach(ops => {
                            if (userInfoList[ops] && userInfoList[ops][ptoken]) {
                                delete userInfoList[ops][ptoken];
                            }
                        });

                        await IntaoDB.setItem(objStores.common_store.name, {
                            taoh_common: user_info_list_key,
                            values: userInfoList,
                            timestamp: Date.now()
                        });
                    }
                }

                alreadyProcessed = true;
            }
            if (item.action === 'forum_delete_message') {
                if (Object.keys(item.value).length > 0) {
                    const value = item.value;

                    let room_hash = value.room;
                    let message_key = (value.message_key).toString();
                    let parent_id = parseInt(value.parent_id) || 0;
                    let isReplyRequest = Boolean(parent_id);

                    let chat_messages_key = isReplyRequest ? 'frm_' + room_hash + '_' + parent_id : 'frm_' + room_hash;
                    const intaoData = await IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key);
                    let chatMessages = intaoData ? intaoData.values : {};

                    if (chatMessages && Object.keys(chatMessages.chat).length > 0 && chatMessages.chat[message_key]) {
                        delete chatMessages.chat[message_key];

                        const msgElem = $('#msg_' + message_key);
                        if (msgElem.length) msgElem.remove();
                    }

                    await IntaoDB.setItem(objStores.ntw_store.name, {
                        taoh_ntw: chat_messages_key,
                        values: chatMessages,
                        timestamp: Date.now()
                    });
                }

                alreadyProcessed = true;
                allowCacheFilesToRemove = false;
            }

            if (item.action === 'cache_clean') {
                // If manuaally cache cleared then remove all cache files here
                /*if (Array.isArray(item.value) && item.value.length > 0) {
                    let room_info_keyslugs = [];
                    for (let i = 0; i < item.value.length; i++) {
                        const srv_cacheKey = item.value[i];
                        if(srv_cacheKey.contains('room_info_keyslug')){
                            const room_info_keyslug = srv_cacheKey.split('_').pop().trim();
                            room_info_keyslugs.push(room_info_keyslug);
                        }
                    }
                    console.log('room_info_keyslugs', room_info_keyslugs);
                }*/

                alreadyProcessed = true;
            }

            // Clear entire store if storekey is provided and not already processed
            if (!alreadyProcessed && item.storekey?.trim()) {
                await IntaoDB.clearStore(item.storekey);
            }

            if(allowCacheFilesToRemove){
                if (Array.isArray(item.value) && item.value.length > 0) {
                    cacheFilesToRemove.push(...item.value);
                } else if (item.value && typeof item.value === 'object' && Object.keys(item.value).length > 0) {
                    cacheFilesToRemove.push(...Object.values(item.value));
                } else if (item.value && typeof item.value === 'string' && item.value.trim()) {
                    cacheFilesToRemove.push(item.value);
                }
            }
        }

        // Send request to handle cacheFilesToRemove
        if (cacheFilesToRemove.length > 0) {
            cacheFilesToRemove = [...new Set(cacheFilesToRemove)];

            const cacheFormData = new FormData();
            cacheFormData.append('taoh_action', 'remove_cache_files');
            cacheFormData.append('fileNames', JSON.stringify(cacheFilesToRemove));

            try {
                const cacheResponse = await fetch(_taoh_site_ajax_url, {
                    method: 'POST',
                    body: cacheFormData
                });
                console.log('Cache Remove Result:', cacheResponse);
                const cacheResult = await cacheResponse.json();
                // console.log('Cache Remove Result:', cacheResult);
            } catch (cacheError) {
                console.error('Error removing cache files:', cacheError.message || cacheError);
            }
        }
    }

    function getAndSetLastCheckedTimestamp(key, lastUpdate, type = 1) {
        if (type === 2) {
            localStorage.setItem(key, lastUpdate);
        }

        return localStorage.getItem(key) || lastUpdate || 0;
    }

    document.addEventListener('visibilitychange', () => {
        if (_ntw_ft_valid_user && !document.hidden && !ft_ntw_isProcessing) {
            const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastNTWMsgCheckedTimestamp', lastNTWMsgCheckedTimestamp, 1);
            taohNTWMessagesFromServer(getNetworkingMessagesFormData(_ntw_ft_ptoken, lastCheckedTimestamp));
        }
    });

    /* Sync Offline Message */
    async function syncOfflineMessages() {

        return; // :rk - Disable offline message sync temporarily due to repeated message issue


        if (!navigator.onLine) {
            checkOfflineMessage = 1;
            return setTimeout(syncOfflineMessages, syncOfflineMessagesDelay);
        }

        let leftOverMessages = 0;
        const store = await IntaoDB.getStore(objStores.ntw_store.name, 'readwrite');

        await new Promise((resolve, reject) => {
            const processCursor = async (cursor) => {
                const key = cursor.primaryKey;
                if (key.startsWith('cm_temp_')) {
                    const chat_temp_messages_key = key;
                    const intao_data = cursor.value;

                    if (intao_data?.values) {
                        const updatedResponse = intao_data.values;

                        for (const [ptokenTo, message_item] of Object.entries(updatedResponse)) {
                            for (const message_data of Object.values(message_item)) {

                                const data = {
                                    taoh_action: 'taoh_room_send_message',
                                    message: message_data.message,
                                    ptoken: message_data.ptoken,
                                    other_ptoken: message_data.to_ptoken,
                                    user_type: message_data.user_type,
                                    key: message_data.room_hash,
                                    sent_time: message_data.sent_time
                                };

                                try {
                                    const response = await $.post(_taoh_site_ajax_url, data);
                                    const returnedData = JSON.parse(response);

                                    if (returnedData.success) {
                                        const store = await IntaoDB.getStore(objStores.ntw_store.name, 'readwrite');
                                        const intao_data = await store.get(chat_temp_messages_key);
                                        if (intao_data?.values) {
                                            let updatedResponse = intao_data.values;
                                            if ((ptokenTo in updatedResponse) && (returnedData.sent_time in updatedResponse[ptokenTo])) {
                                                updatedResponse[ptokenTo][returnedData.sent_time].stored_time = returnedData.stored_time;
                                                await store.put({
                                                    taoh_ntw: chat_temp_messages_key,
                                                    values: updatedResponse,
                                                    timestamp: Date.now()
                                                });
                                            }
                                        }
                                    } else {
                                        leftOverMessages++;
                                    }
                                } catch (error) {
                                    leftOverMessages++;
                                    console.log("Offline message post failed: ", error);
                                }
                            }
                        }
                    }
                }
            }

            const request = store.openCursor();
            request.onsuccess = (event) => {
                const cursor = event.target.result;
                if (cursor) {
                    processCursor(cursor);
                    cursor.continue();
                } else {
                    resolve();
                }
            };

            request.onerror = (event) => {
                console.error('Cursor request failed:', event);
                reject(event.target.error);
            };
        });

        if (leftOverMessages > 0) {
            checkOfflineMessage = 1;
            setTimeout(syncOfflineMessages, syncOfflineMessagesDelay);
        } else {
            checkOfflineMessage = 0;
        }
    }

    if (checkOfflineMessage) {
        syncOfflineMessages();
    }

    /* /Sync Offline Message */

    function taoh_pc_post_metrics(metrics) {
        if (pToken_from.trim() !== '') {
            var con_token = '<?php echo $pc_key_slug ?? ''; ?>';
            save_metrics('profile',metrics,con_token);
          
        }
    }



    /****************************======================== Networking methods ==============================*********************/
    async function taohNTWMessagesFromServer(formData, needOverallResponse = false, needRequestData = {}) {
        if (!formData.ptoken || ft_ntw_isProcessing) return;

        try {
            if(!navigator.onLine) return {status: 0, success: false, message: 'No internet connection'};

            ft_ntw_isProcessing = true;

            const response = await new Promise((resolve, reject) => {
                $.ajax({
                    url: _taoh_cache_chat_proc_url,
                    type: 'POST',
                    dataType: 'json',
                    headers: {
                        'If-None-Match': ntwMessagesETag
                    },
                    data: {
                        "ops": "userdata",
                        "status": "userlong",
                        "code": _taoh_ops_code,
                        "token": ft_taoh_api_token,
                        "key": formData.ptoken,
                        "type": formData.type,
                        "limit": 1000,
                        "timestamp": formData.last_update_time
                    },
                    success: function (res, textStatus, jqXHR) {
                        try {
                            if (jqXHR.status === 304) {
                                resolve({status: 304, message: 'Data not modified'});
                                return;
                            }

                            ntwMessagesETag = jqXHR.getResponseHeader('taoh-etag') ?? null;

                            let resultArr = res;

                            const grpChatsArr = {};
                            const userToken = formData.ptoken;
                            const reupdateRoomHash = formData.reupdate ?? false;
                            for (const [key, item] of Object.entries(resultArr.chats)) {
                                if(!item || !item.room_hash) continue;

                                const roomHash = item.room_hash;
                                const fromPtoken = item.ptoken;
                                const toPtoken = item.to_ptoken;

                                if (reupdateRoomHash && roomHash !== reupdateRoomHash) continue;

                                const assignChat = (chatRoom, ptokenKey) => {
                                    if (!chatRoom[ptokenKey]) chatRoom[ptokenKey] = {};
                                    chatRoom[ptokenKey][key] = item;
                                };

                                if (fromPtoken === userToken || toPtoken === userToken) {
                                    if (!grpChatsArr[roomHash]) grpChatsArr[roomHash] = {};

                                    if (fromPtoken === userToken) {
                                        assignChat(grpChatsArr[roomHash], toPtoken);
                                    } else {
                                        assignChat(grpChatsArr[roomHash], fromPtoken);
                                    }
                                }
                            }

                            const response_data = {
                                status: 200,
                                success: resultArr.success,
                                room_chats: grpChatsArr,
                                last_update_time: Math.round(resultArr.last_update_time),
                            };


                            // Clear cache if any cache data found
                            if (typeof resultArr.cache !== 'undefined' && Object.keys(resultArr.cache).length > 0) {
                                clearCacheProcess(resultArr.cache);
                            }

                            resolve(response_data);

                        } catch (error) {
                            console.error('Error parsing JSON:', error);
                            reject('Error parsing messages response!');
                        }
                    },
                    error: function (xhr, status, error) {
                        reject(error);
                    }
                });
            });

            if (response.status === 200) {
                if (response.success) {
                    if (formData.reupdate) return response;

                    if (Object.keys(response.room_chats).length > 0) ft_ntw_reFetchRequired = false;

                    let globalRoomsNewResponse = {};
                    let roomsInvitesAll = {};
                    let ntw_promises = [];

                    for (const [room_hash, room_conversations] of Object.entries(response.room_chats)) {
                        for (const [to_ptoken, room_chat] of Object.entries(room_conversations)) {
                            let chat_messages_key = 'cm_' + room_hash + '_' + formData.ptoken + '_' + to_ptoken;

                            ntw_promises.push(
                                new Promise(async (resolve, reject) => {
                                    const intao_data = await IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key);
                                    let updatedResponse = {};

                                    if (intao_data?.values && formData.last_update_time != 0) {
                                        updatedResponse = intao_data.values;
                                        Object.assign(updatedResponse.chat, room_chat);
                                        updatedResponse.last_update_time = response.last_update_time;
                                        updatedResponse.success = true;
                                        await IntaoDB.setItem(objStores.ntw_store.name, {
                                            taoh_ntw: chat_messages_key,
                                            values: updatedResponse,
                                            timestamp: Date.now()
                                        });
                                    } else if (formData.last_update_time == 0) {
                                        updatedResponse.chat = room_chat;
                                        updatedResponse.last_update_time = response.last_update_time;
                                        updatedResponse.success = true;
                                        await IntaoDB.setItem(objStores.ntw_store.name, {
                                            taoh_ntw: chat_messages_key,
                                            values: updatedResponse,
                                            timestamp: Date.now()
                                        });
                                    } else if (!intao_data && formData.last_update_time != 0) {
                                        let reupdatedata = formData;
                                        reupdatedata.last_update_time = 0;
                                        reupdatedata.reupdate = room_hash;
                                        ft_ntw_isProcessing = false;
                                        const re_response = await taohNTWMessagesFromServer(reupdatedata);
                                        if (room_hash in re_response.room_chats && to_ptoken in re_response.room_chats[room_hash]) {
                                            updatedResponse.chat = re_response.room_chats[room_hash][to_ptoken];
                                            updatedResponse.last_update_time = response.last_update_time;
                                            updatedResponse.success = true;
                                            await IntaoDB.setItem(objStores.ntw_store.name, {
                                                taoh_ntw: chat_messages_key,
                                                values: updatedResponse,
                                                timestamp: Date.now()
                                            });
                                        }
                                    }

                                    let room_chat_last_key = Object.keys(room_chat).pop();
                                    let room_chat_last_value = room_chat[room_chat_last_key];

                                    // Collecting New Central Message Entries
                                    globalRoomsNewResponse[room_chat_last_value.time] = {
                                        room_hash: room_hash,
                                        ptoken_from: room_chat_last_value.ptoken,
                                        ptoken_to: room_chat_last_value.to_ptoken,
                                        message: room_chat_last_value.message,
                                        message_time: room_chat_last_value.time,
                                        read: 0
                                    };

                                    // Collecting New Invites data
                                    if (formData.ptoken != to_ptoken && to_ptoken != room_chat_last_value.to_ptoken) {
                                        roomsInvitesAll[room_chat_last_value.time] = {
                                            room_hash: room_hash,
                                            invite_from: to_ptoken,
                                            ptoken_from: room_chat_last_value.ptoken,
                                            ptoken_to: room_chat_last_value.to_ptoken,
                                            message: room_chat_last_value.message,
                                            message_time: room_chat_last_value.time,
                                            read: room_chat_last_value.read || 0
                                        };
                                    }

                                    resolve();
                                })
                            );

                            // Clearing Temp messages in Indexed DB when that msg updated from server
                            let chat_temp_messages_key = 'cm_temp_' + room_hash + '_' + formData.ptoken;
                            let temp_ptokenTo = to_ptoken;
                            await IntaoDB.getItem(objStores.ntw_store.name, chat_temp_messages_key).then((intao_data) => {
                                if (intao_data?.values) {
                                    let tempUpdatedResponse = intao_data.values;

                                    if (temp_ptokenTo in tempUpdatedResponse && Object.keys(tempUpdatedResponse[temp_ptokenTo]).length > 0) {
                                        let allSentTimes = Object.keys(room_chat).map(k => room_chat[k]['sent_time']);
                                        Object.keys(tempUpdatedResponse[temp_ptokenTo]).forEach(key => {
                                            let is_deleted = false;
                                            if ('sent_time' in tempUpdatedResponse[temp_ptokenTo][key]) {
                                                let sent_time = (tempUpdatedResponse[temp_ptokenTo][key]['sent_time']).toString();
                                                if (allSentTimes.includes(sent_time)) {
                                                    delete tempUpdatedResponse[temp_ptokenTo][key];
                                                    is_deleted = true;
                                                }
                                            }

                                            if (!is_deleted) {
                                                // Delete if data is expired (expires after 2min (2 * 60 * 1000))
                                                let sent_time = new Date(tempUpdatedResponse[temp_ptokenTo][key]['sent_time']).getTime();
                                                if ((Date.now() - sent_time) > 120000) {
                                                    delete tempUpdatedResponse[temp_ptokenTo][key];
                                                }
                                            }
                                        });

                                        IntaoDB.setItem(objStores.ntw_store.name, {
                                            taoh_ntw: chat_temp_messages_key,
                                            values: tempUpdatedResponse,
                                            timestamp: Date.now()
                                        });
                                    }
                                }
                            });

                        }
                    }

                    await Promise.all(ntw_promises).then(() => {
                        IntaoDB.getItem(objStores.ntw_store.name, 'ft_ntw_networking_misc').then((intao_data) => {
                            let response_last_update_time = 0;
                            if ((intao_data && 'last_update_time' in intao_data) || formData.last_update_time == 0) {
                                response_last_update_time = response.last_update_time;
                                lastNTWMsgCheckedTimestamp = response_last_update_time;
                                IntaoDB.setItem(objStores.ntw_store.name, {
                                    taoh_ntw: 'ft_ntw_networking_misc',
                                    last_update_time: response_last_update_time
                                });
                            } else {
                                lastNTWMsgCheckedTimestamp = response_last_update_time;
                            }
                            getAndSetLastCheckedTimestamp('lastNTWMsgCheckedTimestamp', lastNTWMsgCheckedTimestamp, 2);
                        });

                        // Create Central Messages data
                        if (Object.keys(globalRoomsNewResponse).length > 0) {
                            let mm_rooms_key = 'mm_rooms_' + formData.ptoken;
                            IntaoDB.getItem(objStores.ntw_store.name, mm_rooms_key).then((intao_data) => {
                                let updatedGlobalMsgResponse;
                                if (intao_data?.values) {
                                    updatedGlobalMsgResponse = updateGlobalRoomsMessages(intao_data.values, globalRoomsNewResponse);
                                } else {
                                    updatedGlobalMsgResponse = globalRoomsNewResponse;
                                }
                                IntaoDB.setItem(objStores.ntw_store.name, {
                                    taoh_ntw: mm_rooms_key,
                                    values: sortObjectByKey(updatedGlobalMsgResponse, true),
                                    timestamp: Date.now()
                                });
                            });
                        }


                        // Create Room Invites data
                        if (Object.keys(roomsInvitesAll).length > 0) {
                            let my_invites_key = 'invites_' + formData.ptoken;
                            IntaoDB.getItem(objStores.ntw_store.name, my_invites_key).then((intao_data) => {
                                let updatedInvitesResponse;
                                if (intao_data?.values) {
                                    updatedInvitesResponse = updateInvitesMessages(intao_data.values, roomsInvitesAll);
                                } else {
                                    updatedInvitesResponse = roomsInvitesAll;
                                }
                                IntaoDB.setItem(objStores.ntw_store.name, {
                                    taoh_ntw: my_invites_key,
                                    values: sortObjectByKey(updatedInvitesResponse, true),
                                    timestamp: Date.now()
                                });
                            });
                        }
                    });

                    if (needOverallResponse) {
                        // return complete response of single room data
                        let updatedRoomResponseAll = {};
                        const room_hash = needRequestData.room_hash;
                        const to_ptoken = needRequestData.to_ptoken;
                        let chat_messages_key = 'cm_' + room_hash + '_' + formData.ptoken + '_' + to_ptoken;
                        const intao_data = await IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key);
                        if (intao_data?.values) {
                            updatedRoomResponseAll = intao_data.values;
                        } else {
                            updatedRoomResponseAll.chat = {};
                            updatedRoomResponseAll.last_update_time = response.last_update_time;
                            updatedRoomResponseAll.success = true;
                            await IntaoDB.setItem(objStores.ntw_store.name, {
                                taoh_ntw: chat_messages_key,
                                values: updatedRoomResponseAll,
                                timestamp: Date.now()
                            });
                        }
                        ft_ntw_isProcessing = false;
                        return updatedRoomResponseAll;
                    } else {
                        ft_ntw_isProcessing = false;
                        return response;
                    }
                } else {
                    ft_ntw_isProcessing = false;
                    throw new Error('Invalid chats response!');
                }
            } else {
                ft_ntw_isProcessing = false;
                return response;
            }
        } catch (error) {
            ft_ntw_isProcessing = false;
            console.log(error);
        } finally {
            if (!document.hidden && curr_page === 'room' && (!room_activeTabId || room_activeTabId === 'connections-tab')) {
                setTimeout(() => {
                    const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastNTWMsgCheckedTimestamp', lastNTWMsgCheckedTimestamp, 1);
                    let networkingMessagesFormData = getNetworkingMessagesFormData(_ntw_ft_ptoken, lastCheckedTimestamp);
                    taohNTWMessagesFromServer(networkingMessagesFormData, needOverallResponse, needRequestData);
                }, 1000);
            }
        }
    }

    function getNetworkingMessagesFormData(ptoken, last_update_time) {
        return {
            "type": ft_taoh_chat_network,
            "ptoken": ptoken,
            "last_update_time": last_update_time
        };
    }

    /****************************======================== /Networking methods ==============================*********************/

    /****************************======================== Forum methods ==============================*********************/
    async function taohFRMMessagesFromServer(formData, needOverallResponse = false, needRequestData = {}) {
        let isReplyRequest = Boolean(parseInt(formData.parent_id));
        if (!formData.ptoken || (isReplyRequest && ft_frm_reply_isProcessing)) {
            return;
        } else if (!formData.ptoken || (!isReplyRequest && ft_frm_isProcessing)) {
            return;
        }

        try {
            if(!navigator.onLine) return {status: 0, success: false, message: 'No internet connection'};

            isReplyRequest ? ft_frm_reply_isProcessing = true : ft_frm_isProcessing = true;

            const response = await new Promise((resolve, reject) => {
                $.ajax({
                    url: _taoh_cache_chat_proc_url,
                    type: 'POST',
                    dataType: 'json',
                    headers: {
                        'If-None-Match': isReplyRequest ? frmReplyMessagesETag : frmMessagesETag
                    },
                    data: {
                        "ops": "userdata",
                        "status": "userlong",
                        "code": _taoh_ops_code,
                        "key": formData.ptoken,
                        "keyslug": formData.keyslug,
                        "parent_id": formData.parent_id ?? 0,
                        "type": formData.type,
                        "limit": 1000,
                        "timestamp": formData.last_update_time
                    },
                    success: function (res, textStatus, jqXHR) {
                        try {
                            if (jqXHR.status === 304) {
                                resolve({status: 304, message: 'Data not modified'});
                                return;
                            }

                            if (isReplyRequest) {
                                frmReplyMessagesETag = jqXHR.getResponseHeader('taoh-etag') ?? null;
                            } else {
                                frmMessagesETag = jqXHR.getResponseHeader('taoh-etag') ?? null;
                            }

                            let resultArr = res;

                            const grpChatsArr = {};
                            const userToken = formData.ptoken;
                            const reupdateRoomHash = formData.reupdate ?? false;
                            for (const [key, item] of Object.entries(resultArr.chats)) {
                                if(!item || !item.room_hash) continue;

                                const roomHash = item.room_hash;
                                // const fromPtoken = item.ptoken;
                                // const toPtoken = item.to_ptoken;

                                if (reupdateRoomHash && roomHash !== reupdateRoomHash) continue;

                                // const assignChat = (chatRoom, ptokenKey) => {
                                //     if (!chatRoom[ptokenKey]) chatRoom[ptokenKey] = {};
                                //     chatRoom[ptokenKey][key] = item;
                                // };

                                // if (fromPtoken === userToken || toPtoken === userToken) {
                                    if (!grpChatsArr[roomHash]) grpChatsArr[roomHash] = {};

                                    // if (fromPtoken === userToken) {
                                    //     assignChat(grpChatsArr[roomHash], toPtoken);
                                    // } else {
                                    //     assignChat(grpChatsArr[roomHash], fromPtoken);
                                    // }
                                        grpChatsArr[roomHash][key] = item;
                                // }
                            }

                            const response_data = {
                                status: 200,
                                success: resultArr.success,
                                room_chats: grpChatsArr,
                                last_update_time: Math.round(resultArr.last_update_time),
                            };


                            // Clear cache if any cache data found
                            if (typeof resultArr.cache !== 'undefined' && Object.keys(resultArr.cache).length > 0) {
                                clearCacheProcess(resultArr.cache);
                            }

                            resolve(response_data);

                        } catch (error) {
                            console.error('Error parsing JSON:', error);
                            reject('Error parsing messages response!');
                        }
                    },
                    error: function (xhr, status, error) {
                        reject(error);
                    }
                });
            });


            if (response.status === 200) {
                if (response.success) {
                    if (formData.reupdate) return response;

                    if (Object.keys(response.room_chats).length > 0) {
                        ft_frm_reFetchRequired = false;
                    } else {
                        const chat_messages_key_exist = isReplyRequest ? 'frm_' + formData.keyslug + '_' + formData.parent_id : 'frm_' + formData.keyslug;
                        let chat_messages_key_count = await IntaoDB.checkKeyExists(objStores.ntw_store.name, chat_messages_key_exist);
                        if (!chat_messages_key_count) {
                            IntaoDB.setItem(objStores.ntw_store.name, {
                                taoh_ntw: chat_messages_key_exist,
                                values: {
                                    "chat": {},
                                    "last_update_time": response.last_update_time,
                                    "success": true
                                },
                                timestamp: Date.now()
                            });
                        }
                    }

                    let globalRoomsNewResponse = {};
                    let roomsInvitesAll = {};
                    let ntw_promises = [];
                    let replyCountUpdatedKeys = {};

                    for (const [room_hash, room_chat] of Object.entries(response.room_chats)) {
                        // for (const [to_ptoken, room_chat] of Object.entries(room_conversations)) {
                            let chat_messages_key = isReplyRequest ? 'frm_' + room_hash + '_' + formData.parent_id : 'frm_' + room_hash;

                            ntw_promises.push(
                                new Promise(async (resolve, reject) => {
                                    const intao_data = await IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key);
                                    let updatedResponse = {};

                                    if (intao_data?.values && formData.last_update_time != 0) {
                                        updatedResponse = intao_data.values;
                                        Object.assign(updatedResponse.chat, room_chat);
                                        updatedResponse.last_update_time = response.last_update_time;
                                        updatedResponse.success = true;
                                        await IntaoDB.setItem(objStores.ntw_store.name, {
                                            taoh_ntw: chat_messages_key,
                                            values: updatedResponse,
                                            timestamp: Date.now()
                                        });
                                    } else if (formData.last_update_time == 0) {
                                        updatedResponse.chat = room_chat;
                                        updatedResponse.last_update_time = response.last_update_time;
                                        updatedResponse.success = true;
                                        await IntaoDB.setItem(objStores.ntw_store.name, {
                                            taoh_ntw: chat_messages_key,
                                            values: updatedResponse,
                                            timestamp: Date.now()
                                        });
                                    } else if (!intao_data && formData.last_update_time != 0) {
                                        let reupdatedata = formData;
                                        reupdatedata.last_update_time = 0;
                                        reupdatedata.reupdate = room_hash;
                                        isReplyRequest ? ft_frm_reply_isProcessing = false : ft_frm_isProcessing = false;
                                        const re_response = await taohFRMMessagesFromServer(reupdatedata);
                                        if (room_hash in re_response.room_chats) {
                                            updatedResponse.chat = re_response.room_chats[room_hash];
                                            updatedResponse.last_update_time = response.last_update_time;
                                            updatedResponse.success = true;
                                            await IntaoDB.setItem(objStores.ntw_store.name, {
                                                taoh_ntw: chat_messages_key,
                                                values: updatedResponse,
                                                timestamp: Date.now()
                                            });
                                        }
                                    }


                                    let room_chat_last_key = Object.keys(room_chat).pop();
                                    let room_chat_last_value = room_chat[room_chat_last_key];

                                    // // Collecting New Central Message Entries
                                    // globalRoomsNewResponse[room_chat_last_value.time] = {
                                    //     room_hash: room_hash,
                                    //     ptoken_from: room_chat_last_value.ptoken,
                                    //     ptoken_to: room_chat_last_value.to_ptoken,
                                    //     message: room_chat_last_value.message,
                                    //     message_id: room_chat_last_value.message_id,
                                    //     message_time: room_chat_last_value.time,
                                    //     read: 0
                                    // };

                                    // Collecting New Invites data
                                    // if (formData.ptoken != to_ptoken && to_ptoken != room_chat_last_value.to_ptoken) {
                                        roomsInvitesAll[room_chat_last_value.time] = {
                                            room_hash: room_hash,
                                            invite_from: room_chat_last_value.ptoken,
                                            ptoken_from: room_chat_last_value.ptoken,
                                            ptoken_to: room_chat_last_value.to_ptoken,
                                            message: room_chat_last_value.message,
                                            message_id: room_chat_last_value.message_id,
                                            message_time: room_chat_last_value.time,
                                            read: room_chat_last_value.read || 0
                                        };
                                    // }

                                    if (isReplyRequest) {
                                        const parentId = parseInt(room_chat_last_value.parent_id);
                                        if (parentId && frm_replyCountMap.has(parentId)) {
                                            let replyCountObj = frm_replyCountMap.get(parentId);
                                            replyCountObj.reply_count = room_chat_last_value.reply_count;
                                            frm_replyCountMap.set(parentId, replyCountObj);
                                            if (!replyCountUpdatedKeys[room_hash]) replyCountUpdatedKeys[room_hash] = [];
                                            replyCountUpdatedKeys[room_hash].push(replyCountObj.key);
                                        }
                                    }

                                    resolve();
                                })
                            );

                            // Clearing Temp messages in Indexed DB when that msg updated from server
                            let chat_temp_messages_key = isReplyRequest ? 'frm_temp_' + room_hash + '_' + formData.parent_id : 'frm_temp_' + room_hash;
                            await IntaoDB.getItem(objStores.ntw_store.name, chat_temp_messages_key).then((intao_data) => {
                                if (intao_data?.values) {
                                    let tempUpdatedResponse = intao_data.values;

                                    if (tempUpdatedResponse && Object.keys(tempUpdatedResponse).length > 0) {
                                        let allSentTimes = Object.keys(room_chat).map(k => room_chat[k]['sent_time']);
                                        Object.keys(tempUpdatedResponse).forEach(key => {
                                            let is_deleted = false;
                                            if ('sent_time' in tempUpdatedResponse[key]) {
                                                let sent_time = (tempUpdatedResponse[key]['sent_time']).toString();
                                                if (allSentTimes.includes(sent_time)) {
                                                    delete tempUpdatedResponse[key];
                                                    is_deleted = true;
                                                }
                                            }

                                            if (!is_deleted) {
                                                // Delete if data is expired (expires after 2min (2 * 60 * 1000))
                                                let sent_time = new Date(tempUpdatedResponse[key]['sent_time']).getTime();
                                                if ((Date.now() - sent_time) > 120000) {
                                                    delete tempUpdatedResponse[key];
                                                }
                                            }
                                        });

                                        IntaoDB.setItem(objStores.ntw_store.name, {
                                            taoh_ntw: chat_temp_messages_key,
                                            values: tempUpdatedResponse,
                                            timestamp: Date.now()
                                        });
                                    }
                                }
                            });

                        // }
                    }

                    await Promise.all(ntw_promises).then(() => {
                        let chat_networking_misc_key = isReplyRequest ? 'ft_frm_networking_misc_' + formData.keyslug + '_' + formData.parent_id
                            : 'ft_frm_networking_misc_' + formData.keyslug;
                        IntaoDB.getItem(objStores.ntw_store.name, chat_networking_misc_key).then((intao_data) => {
                            let response_last_update_time = 0;
                            if ((intao_data && 'last_update_time' in intao_data) || formData.last_update_time == 0) {
                                response_last_update_time = response.last_update_time;
                                isReplyRequest ? lastFRMReplyMsgCheckedTimestamp = response_last_update_time : lastFRMMsgCheckedTimestamp = response_last_update_time;
                                IntaoDB.setItem(objStores.ntw_store.name, {
                                    taoh_ntw: chat_networking_misc_key,
                                    last_update_time: response_last_update_time
                                });
                            } else {
                                isReplyRequest ? lastFRMReplyMsgCheckedTimestamp = response_last_update_time : lastFRMMsgCheckedTimestamp = response_last_update_time;
                            }

                            if (isReplyRequest) {
                                getAndSetLastCheckedTimestamp('lastFRMReplyMsgCheckedTimestamp', lastFRMReplyMsgCheckedTimestamp, 2)
                            } else {
                                getAndSetLastCheckedTimestamp('lastFRMMsgCheckedTimestamp', lastFRMMsgCheckedTimestamp, 2)
                            }
                        });

                        // Update reply count in Indexed DB
                        if (isReplyRequest) {
                            async function updateParentEntriesReplyCount(chat_messages_key, keys) {
                                const intao_data = await IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key);
                                if (intao_data?.values) {
                                    let updatedResponse = intao_data.values;
                                    keys.forEach(key => {
                                        if (key in updatedResponse.chat) {
                                            let messageId = updatedResponse.chat[key].message_id;
                                            if (messageId) updatedResponse.chat[key].reply_count = frm_replyCountMap.get(messageId).reply_count;

                                            const msgElem = $('#msg_' + key);
                                            if (msgElem.length) msgElem.find('.frm_reply_comment_cnt').text(updatedResponse.chat[key].reply_count).show();
                                        }
                                    });

                                    await IntaoDB.setItem(objStores.ntw_store.name, {taoh_ntw: chat_messages_key, values: updatedResponse, timestamp: Date.now()});
                                }
                            }

                            for (const [room_hash, keys] of Object.entries(replyCountUpdatedKeys)) {
                                let chat_messages_key = 'frm_' + room_hash;
                                updateParentEntriesReplyCount(chat_messages_key, keys);
                            }
                        }

                        // // Create Central Messages data
                        // if (Object.keys(globalRoomsNewResponse).length > 0) {
                        //     let mm_rooms_key = 'mm_rooms_' + formData.ptoken;
                        //     IntaoDB.getItem(objStores.ntw_store.name, mm_rooms_key).then((intao_data) => {
                        //         let updatedGlobalMsgResponse;
                        //         if (intao_data?.values) {
                        //             updatedGlobalMsgResponse = updateGlobalRoomsMessages(intao_data.values, globalRoomsNewResponse);
                        //         } else {
                        //             updatedGlobalMsgResponse = globalRoomsNewResponse;
                        //         }
                        //         IntaoDB.setItem(objStores.ntw_store.name, {
                        //             taoh_ntw: mm_rooms_key,
                        //             values: sortObjectByKey(updatedGlobalMsgResponse, true),
                        //             timestamp: Date.now()
                        //         });
                        //     });
                        // }


                        // Create Room Invites data
                        if (Object.keys(roomsInvitesAll).length > 0) {
                            let my_invites_key = isReplyRequest ? 'frm_invites_' + formData.keyslug + '_' + formData.parent_id : 'frm_invites_' + formData.keyslug;
                            IntaoDB.getItem(objStores.ntw_store.name, my_invites_key).then((intao_data) => {
                                let updatedInvitesResponse;
                                if (intao_data?.values) {
                                    updatedInvitesResponse = updateInvitesMessages(intao_data.values, roomsInvitesAll);
                                } else {
                                    updatedInvitesResponse = roomsInvitesAll;
                                }
                                IntaoDB.setItem(objStores.ntw_store.name, {
                                    taoh_ntw: my_invites_key,
                                    values: sortObjectByKey(updatedInvitesResponse, true),
                                    timestamp: Date.now()
                                });
                            });
                        }
                    });

                    if (needOverallResponse) {
                        // return complete response of single room data
                        let updatedRoomResponseAll = {};
                        const room_hash = needRequestData.room_hash;
                        // const to_ptoken = needRequestData.to_ptoken;
                        let chat_messages_key = isReplyRequest ? 'frm_' + room_hash + '_' + formData.parent_id : 'frm_' + room_hash;
                        const intao_data = await IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key);
                        if (intao_data?.values) {
                            updatedRoomResponseAll = intao_data.values;
                        } else {
                            updatedRoomResponseAll.chat = {};
                            updatedRoomResponseAll.last_update_time = response.last_update_time;
                            updatedRoomResponseAll.success = true;
                            await IntaoDB.setItem(objStores.ntw_store.name, {
                                taoh_ntw: chat_messages_key,
                                values: updatedRoomResponseAll,
                                timestamp: Date.now()
                            });
                        }
                        isReplyRequest ? ft_frm_reply_isProcessing = false : ft_frm_isProcessing = false;
                        return updatedRoomResponseAll;
                    } else {
                        isReplyRequest ? ft_frm_reply_isProcessing = false : ft_frm_isProcessing = false;
                        return response;
                    }
                } else {
                    isReplyRequest ? ft_frm_reply_isProcessing = false : ft_frm_isProcessing = false;
                    throw new Error('Invalid chats response!');
                }
            } else {
                isReplyRequest ? ft_frm_reply_isProcessing = false : ft_frm_isProcessing = false;
                return response;
            }
        } catch (error) {
            isReplyRequest ? ft_frm_reply_isProcessing = false : ft_frm_isProcessing = false;
            console.log(error);
        } finally {
            if (!document.hidden && curr_page === 'room' && (!room_activeTabId || room_activeTabId === 'conversation-tab')) {
                if ((!frm_reply_view && !isReplyRequest) || (frm_reply_view && isReplyRequest && parseInt(formData.parent_id) === parseInt(frm_message_id))) {
                    setTimeout(() => {
                        let forumMessagesFormData = getForumMessagesFormData(formData.keyslug, _ntw_ft_ptoken, formData.parent_id,
                            isReplyRequest
                                ? getAndSetLastCheckedTimestamp('lastFRMReplyMsgCheckedTimestamp', lastFRMReplyMsgCheckedTimestamp, 1)
                                : getAndSetLastCheckedTimestamp('lastFRMMsgCheckedTimestamp', lastFRMMsgCheckedTimestamp, 1));
                        taohFRMMessagesFromServer(forumMessagesFormData, needOverallResponse, needRequestData);
                    }, 1000);
                } else {
                    if (!frm_reply_view && !ft_frm_isProcessing) {
                        const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastFRMMsgCheckedTimestamp', lastFRMMsgCheckedTimestamp, 1);
                        await taohFRMMessagesFromServer(getForumMessagesFormData(frm_room_key, my_pToken, 0, lastCheckedTimestamp));
                    } else if (frm_reply_view && !ft_frm_reply_isProcessing) {
                        const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastFRMReplyMsgCheckedTimestamp', lastFRMReplyMsgCheckedTimestamp, 1);
                        await taohFRMMessagesFromServer(getForumMessagesFormData(frm_room_key, my_pToken, frm_message_id, lastCheckedTimestamp));
                    }
                }
            }
        }
    }

    function getForumMessagesFormData(keyslug, ptoken, parent_id, last_update_time){
        return {
            "keyslug": keyslug,
            "type": ft_taoh_chat_forum,
            "ptoken": ptoken,
            "parent_id": parent_id,
            "last_update_time": last_update_time
        };
    }

    /****************************======================== /Forum methods ==============================*********************/

</script>