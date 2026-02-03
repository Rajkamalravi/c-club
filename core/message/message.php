<?php
$contslug = taoh_parse_url(1);

$user_info_obj = taoh_user_all_info();
$profile_complete = ( isset( $user_info_obj->profile_complete ) && $user_info_obj->profile_complete ) ? $user_info_obj->profile_complete:0;
if(!$profile_complete){
    taoh_set_error_message('complete your settings to fully use the platform.');
    $url = TAOH_SITE_URL_ROOT . '/settings';
    taoh_redirect($url);
    taoh_exit();
}

$messaging_chat_arr = [];
if (($contslug == 'chatwith') && !empty(taoh_parse_url(2))) {
    $messaging_chat_arr = explode('-', taoh_parse_url(2));
}

$ptoken = $user_info_obj->ptoken;

define('THIS_PAGE_URL', TAOH_SITE_URL_ROOT . '/' . TAOH_MESSAGEPAGE_NAME);
define('THIS_PAGE_AJAX_NAME', taoh_site_ajax_url());


taoh_get_header();
?>

    <style>
        #central-message ::-webkit-scrollbar {
            width: 10px;
        }

        #central-message ::-webkit-scrollbar-track {
            box-shadow: inset 0 0 5px grey;
        }

        #central-message ::-webkit-scrollbar-thumb {
            background: #919191;
        }

        #central-message ::-webkit-scrollbar-thumb:hover {
            background: #7e7e7e;
        }

        #central-message {
            max-width: 1400px;
            margin: 0 auto;
        }

        #central-message .mm-card {
            -webkit-border-radius: 8px;
            -moz-border-radius: 8px;
            border-radius: 8px;
            -webkit-transition: all 0.3s;
            -moz-transition: all 0.3s;
            -ms-transition: all 0.3s;
            -o-transition: all 0.3s;
            transition: all 0.3s;
            -webkit-box-shadow: 0 0 8px rgba(82, 85, 90, 0.1);
            -moz-box-shadow: 0 0 8px rgba(82, 85, 90, 0.1);
            box-shadow: 0 0 8px rgba(82, 85, 90, 0.1);
            border: 0;
        }

        #left_users_list .card-item {
            margin-bottom: 15px !important;
            -webkit-box-shadow: 0 0 8px rgb(82 85 90 / 25%);
            -moz-box-shadow: 0 0 8px rgb(82 85 90 / 25%);
            box-shadow: 0 0 8px rgb(82 85 90 / 25%) !important;
        }

        #profileMoreInfo {
            min-height: 350px;
            max-height: 590px;
            overflow-y: auto;
        }

        #profileMoreInfo .media-card{
            margin: 15px 4px !important;
        }

        #profile_info .media-card{
            padding: 20px !important;
        }

        #query:focus{
            box-shadow: none !important;
        }
        #comments {
            min-height: 350px;
            max-height: 500px;
            overflow: auto;
            width: 100%;
        }

        #comments li {
            list-style-type: none;
            display: flex;
        }

        .comment-body {
            padding: 0 15px!important;
        }

        #left_users_list {
            min-height: 500px;
            max-height: 590px;
            overflow-y: auto;
        }

        #left_users_list div.bottom-chat-list {
            padding: 10px;
        }

        #left_users_list div.bottom-chat-list:hover {
            background: #e9e9e9;
        }

        #left_users_list div.bottom-chat-list.active {
            background: #e6f9ff;
        }

        #left_users_list .bottom-chat-list .active-status,
        #left_users_list .bottom-chat-list .active-status-border {
            top: 58px !important;
            right: 8px !important;
        }

        #left_users_list .chat-user-list-bottom,
        #left_users_list .user-detail-chat-outer {
            width: 100%;
        }

        /*.left_users_list .network_entries .active-status,*/
        /*.left_users_list .network_entries .active-status-border {*/
        /*    right: 40px;*/
        /*}*/

        .no_comments_img {
            display: block;
            margin: auto;
            width: 300px;
        }

        /*#mm_center_section .sec-foot-block {*/
        /*    position: absolute;*/
        /*    width: 100%;*/
        /*    bottom: 0;*/
        /*}*/

        .has-search .form-control {
            padding-left: 2.375rem;
        }

        .has-search .form-control-feedback {
            position: absolute;
            z-index: 2;
            display: block;
            width: 2.375rem;
            height: 2.375rem;
            line-height: 2.375rem;
            text-align: center;
            pointer-events: none;
            color: #aaa;
        }

        .profilemoreinfo_accordion{
            align-items:flex-end;
            display:flex;
            justify-content:flex-end;
            cursor:pointer;
        }

        #current_chat_with_info  .active-status, #current_chat_with_info  .active-status-border {
            top: 27px;
            width: 10px;
            height: 10px;
        }
        /* rnew */
        #left_users_list .card-item .chat-user-list-bottom .card-body, #left_users_list .card-item .chat-user-list-bottom .row{
            padding: 0 !important;
        }
        #mm_center_section #comments .comment-body .left_user_msg{
            white-space: nowrap;
        }
        #mm_center_section #comments .comment-body .left_user_videoicon{
            padding-top: 6px;
            padding-left: 10px;
        }
        #mm_center_section #comments .comment-body .left_user_videoicon{
            padding-top: 15px;
            padding-left: 10px;
        }

        #left_users_list .card-item .chat-user-list-bottom .left_user_msg{
            word-wrap: normal;
        }

        #left_users_list .chat-user-avatar,
        #profile_info .chat-user-avatar {
            -webkit-border-radius: 100%;
            -moz-border-radius: 100%;
            border-radius: 100%;
            width: 100%;
            height: 100%;
            max-width: 60px;
            max-height: 60px;
            border: 1px solid #d4d4d4;
        }

        #windowchatavatar .comment-avatar {
            width: 50px;
            height: 50px;
        }

        #comments li.mine .comment-body{
            background: #e6f9ff;
        }

        .comment-body:before,
        .comment-body:after {
            position: absolute;
            top: 10px;
            display: inline-block;
            width: 12px;
            height: 12px;
        }

        #comments li.others .comment-body:before {
            content: '';
            left: -7px;
            border-top: 1px solid #d5d5d5;
            border-right: 1px solid #d5d5d5;
            background: #fff;
            transform: rotate(-135deg);
        }

        #comments li.mine .comment-body:after {
            content: '';
            right: -7px;
            border-top: 1px solid #d5d5d5;
            border-right: 1px solid #d5d5d5;
            background: #e6f9ff;
            transform: rotate(45deg);
        }

        .chat-area #sendChat{
            right: 10px !important;
            bottom: 10px !important;
        }

        #newmessages_btn_grp {
            position: absolute;
            bottom: 120px;
            margin-left: auto;
            margin-right: auto;
            left: 50%;
            text-align: center;
            transform: translate(-50%, -50%);
            overflow: hidden;
            z-index: 999;
        }
    </style>

    <div class="theme-bg">
        <div id="central-message">
            <div class="row m-0">
                <div class="col-md-3 col-lg-3" id="mm_left_section">
                    <div class="mt-4 mb-4">
                        <div class="card mm-card">
                            <div class="card-body">
                                <div class="card-body-header">
                                    <div class="row">
                                        <div class="col-6">
                                            <h3 class="fs-17">All Participants</h3>
                                        </div>
                                        <div class="col-6">
                                            <div class="custom-control custom-switch float-right mb-0" id="mm_custom_switch">
                                                <input onclick="mm_makeAllRead()" type="checkbox" class="custom-control-input" id="mm_readSwitch">
                                                <label class="custom-control-label pl-2" for="mm_readSwitch">Read All</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="divider mb-2"></div>
                                </div>
                                <div class="mb-2">
                                    <div class="form-group has-search">
                                        <span class="fa fa-search form-control-feedback"></span>
                                        <input type="search" name="room_query" class="form-control" id="room_query" placeholder="SEARCH" aria-label="SEARCH">
                                    </div>
                                </div>

                                <div class="taoh-loader taoh-spinner show" id="mm_rooms_loader"></div>
                                <div id="left_users_list" class="left_users_list">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-6 p-0" id="mm_center_section">
                    <div class="mt-4 mb-4">


                        <div class="card mm-card">
                            <div class="card-body">
                                <div class="card-body-header">
                                    <div class="row" id="chatArea">
                                        <div class="col-8">
                                            <div id="windowchatavatar"></div>
                                            <h3 class="fs-17" id="current_chat_with_info">Choose anyone to continue the conversation.</h3>
                                        </div>
                                        <div class="col-4">
                                            <div class="d-flex justify-content-end">
                                                <!--                                        <a href="#"><i class="la la-language text-black"></i></a>-->
                                                <!--                                        <a href="#" data-metrics="post" class="btn theme-btn">Show details</a>-->
                                                <i class="la la-video fs-30" id="mm_video" style="display: none;"></i>
                                                <!--                                        <span class="fs-15" id="message_count" style="display:none;"><span id="chatCount">0</span> Message(s)</span>-->
                                            </div>
                                        </div>
                                    </div>

                                    <div class="divider mb-4"></div>
                                    <input type="hidden" id="lastchatwith">
                                </div>
                                <div class="taoh-loader taoh-spinner show" id="mm_chat_loader"></div>
                                <ul class="midcenternotes" id="comments">
                                    <!-- Chat comments goes here -->
                                    <img src="<?php echo TAOH_SITE_URL_ROOT . '/assets/images/no-chat.svg'; ?>" alt="No Chat Available" class="no_comments_img">
                                </ul>

                                <div id="newmessages_btn_grp" class="btn-group rounded-pill" role="group" aria-label="New Messages" style="display: none;">
                                    <button type="button" id="newmessages_btn" class="btn btn-sm theme-btn" title="Click to scroll down"><i class="fa fa-arrow-down mr-2"></i><span>new messages</span></button>
                                    <button type="button" id="newmessages_close_btn" class="btn btn-sm theme-btn"><i class="fa fa-times"></i></button>
                                </div>

                                <div class="card-body-footer">
                                    <div class="chat-area" id="chat-area">
                                        <div class="col-12 p-0 sender_part" style="display:none">
                                            <textarea id="commentInput" placeholder="Introduce yourself and get the discussion started" class="form-control" name="message"></textarea>
                                            <button id="sendChat" class="btn theme-btn" type="submit" title="Send"><i class="fa fa-paper-plane-o"></i></button>
                                        </div>
                                    </div>
                                    <div class="message-area" id="message-area" style="display:none">
                                        <div class="col-12 text-center p-4 bg-gray">
                                            <div class="fs-15 fw-bold text-center pb-2">Since the user is offline, you can't chat now but instead send a message.</div>
                                            <button id="messages_offline_send_message_btn" class="btn theme-btn" type="button">Send Message</button>
                                        </div>
                                    </div>
                                    <div class="p-2" id="message_helper" style="display:none">
                                        <div class="fs-15 fw-bold text-center pb-2">You can click any one to start your conversation</div>
                                        <ul style="list-style-type:disc" class="fs-14 lh-22 text-black-50">
                                            <li onclick="copyToMessage('What brought you to this event?');">What brought you to this event?</li>
                                            <li onclick="copyToMessage('What do you do?');">What do you do</li>
                                            <li onclick="copyToMessage('What\'s your favorite thing to do outside of work?');">What's your favorite thing to do outside of work?</li>
                                            <li onclick="copyToMessage('What\'s your ideal career?');">What's your ideal career?</li>
                                            <li onclick="copyToMessage('What is keeping you up at night?');">What is keeping you up at night?</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-lg-3" id="mm_right_section">
                    <div class="mt-4 mb-4">
                        <div class="container pl-0 pt-2" id="profile_info">


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Message Page Offline Message Modal -->
    <div class="modal fade" id="messagesOfflineMessageModal" tabindex="-1" role="dialog" aria-labelledby="messagesOfflineMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div style="background:none;border:none" class="modal-content">
                <div class="modal-body p-0">
                    <div class="card card-item">
                        <div class="card-body">
                            <div id="messagesOfflineMessageBlock">
                                <h3 class="fs-22 fw-bold">Type your message</h3>
                                <div class="row fs-15 mt-4 mb-4">
                                    <div class="col-10">
                                        <textarea name="messagesOfflineMessage" id="messagesOfflineMessage" rows="5" maxlength="500" placeholder="Say something" required></textarea>
                                    </div>
                                    <input type="hidden" id="messagesOfflineLocationPath" value="">
                                    <input type="hidden" id="messagesOfflineToPtoken" value="">
                                </div>
                                <button type="button" class="btn btn-primary fw-medium" id="messages_message_send_button">Send</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                            <div id="messagesOfflineSuccessMessage" class="alert text-success mt-3" style="display: none;">
                                Your message has been sent successfully!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script type="application/javascript">
        var _taoh_site_message_ajax_url = '<?php echo taoh_site_message_ajax_url(); ?>';

        let totalItems = 0;
        let itemsPerPage = 1000;
        let currentPage = 1;
        var doappend = 1;
        var liveonly = 0;
        var left_users_list = $('#left_users_list');
        var mm_readSwitch = $('#mm_readSwitch');

        let entriesList = $('#entriesList');
        let comments = $("#comments");
        let commentInput = $('#commentInput');
        let newmessages_btn_grp = $('#newmessages_btn_grp');
        let newmessages_btn = $('#newmessages_btn');
        let chatCount = $("#chatCount");
        let sendChat = $("#sendChat");


        /* Rooms Chat variables */
        let currentRoomMsgList = {};
        let lastAllRoomsMessageTime = 0;
        let mm_rooms_isProcessing = false;
        let mm_rooms_reFetchRequired = false;

        /* Network Chat variables */
        let chat_window = <?php echo $chat_window ?? 0; ?>;
        let mm_room_key = '';
        let chatwith = '';
        let chatwith_liveStatus = 0;
        let chatname = '';

        let messaging_chat_user_upd = 0;
        let messaging_chat_arr = JSON.parse('<?= json_encode(($messaging_chat_arr ?? [])); ?>');

        const mm_chatContainer = document.getElementById('comments');

        let mmChatFirstCall = 1;
        let mmChatLastTime = 0;
        let mm_newMessagesCnt = 0;
        let mm_isProcessing = false;
        let mm_msgScrollUpEnded = false;
        let mm_msgUpIndex = 0;
        let mm_msgDownIndex = 0;
        let mmChatPageNo = 1;
        const mmChatItemsPerPage = 10;
        let mmChatDataInterval;
        let mmChatDataFromServerInterval;
        let mmuserInfoList = {};
        let mmroomInfoList = {};
        let userInfoTimeout;
        let userLiveIntervalId;
        let userLiveStatusInterval = 60000; // 1 minute
        let updateSenderArea = false;

        var my_pToken = '<?php echo $ptoken ?? ''; ?>';

        /* /Network Chat variables */


        $(document).ready(function () {

            $("#room_query").on("input", function () {
                renderMMRoomsData(currentRoomMsgList);
            });

            taoh_Loader($('#mm_chat_loader'), false);

            $('#mm_video').on('click', function (e) {
                e.preventDefault();

                if (mm_room_key.trim() != '' && chatwith.trim() != '') {
                    $('#mm_video').removeClass('la-video').addClass('la-spinner la-spin');

                    getRoomInfo(mm_room_key, my_pToken).then((room_info) => {
                        let data = {
                            'taoh_action': 'taoh_add_video_chat',
                            'my_token': my_pToken,
                            'guest_token': chatwith,
                            'network_title': ((typeof room_info['club'] != 'undefined') ? room_info['club'].title : 'Message Chat')
                        };

                        let confirmMsg;
                        if (chatname != '') {
                            confirmMsg = 'Please confirm that you want to start a video chat with ' + chatname + ' ?';
                        } else {
                            confirmMsg = 'Please confirm that you want to start a video chat?';
                        }

                        taoh_set_warning_message(confirmMsg, false, 'toast-middle', [
                            {
                                text: 'Yes',
                                action: () => {
                                    $.post(_taoh_site_ajax_url, data, function (response) {
                                        let video_chat_link = 'Want to chat? <div class="chat-meeting"><i class="la la-video"></i><div><p>' + chatname + '\'s meeting</p><a href="' + response.other_link + '" target="_blank" class="chat-meeting-link"> Join video meeting</a></div></div>';
                                        commentInput.val(video_chat_link);
                                        sendChat.trigger('click');
                                        $('#mm_video').removeClass('la-spinner la-spin').addClass('la-video');
                                        if (response.my_link) window.open(response.my_link);
                                    }).fail(function () {
                                        $('#mm_video').removeClass('la-spinner la-spin').addClass('la-video');
                                    });
                                },
                                class: 'dojo-v1-btn float-right mt-3 mb-3'
                            },
                            {
                                text: 'cancel',
                                action: () => {
                                    $('#mm_video').removeClass('la-spinner la-spin').addClass('la-video');
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
                        //             $('#mm_video').removeClass('la-spinner la-spin').addClass('la-video');
                        //         },
                        //         confirm: {
                        //             text: 'Yes',
                        //             action: function () {
                        //                 $.post(_taoh_site_ajax_url, data, function (response) {
                        //                     let video_chat_link = 'Want to chat? <div class="chat-meeting"><i class="la la-video"></i><div><p>' + chatname + '\'s meeting</p><a href="' + response.other_link + '" target="_blank" class="chat-meeting-link"> Join video meeting</a></div></div>';
                        //                     commentInput.val(video_chat_link);
                        //                     sendChat.trigger('click');
                        //                     $('#mm_video').removeClass('la-spinner la-spin').addClass('la-video');
                        //                     if (response.my_link) window.open(response.my_link);
                        //                 }).fail(function () {
                        //                     $('#mm_video').removeClass('la-spinner la-spin').addClass('la-video');
                        //                 });
                        //             }
                        //         }
                        //     }
                        // });
                    }).catch((e) => {
                        console.log(e);
                        $('#mm_video').removeClass('la-spinner la-spin').addClass('la-video');
                    });

                } else {
                    taoh_set_error_message('Please select a user to chat with.', false);
                }
            });

            sendChat.on('click', function () {
                let message = commentInput.val();
                if (message.trim() === '') {
                    alert('Message seems empty! Please enter valid message to send.');
                    return false;
                }
                if (message !== "") {
                    let toToken = $("#lastchatwith").val();

                    $('#message_helper').hide();
                    sendMMChat(message, my_pToken, toToken, mm_room_key, 'user');
                }
            });

            $('#messages_message_send_button').on('click', function () {
                let message = $('#messagesOfflineMessage').val();
                let locationPath = $('#messagesOfflineLocationPath').val();
                let toPtoken = $('#messagesOfflineToPtoken').val();
                let messages_message_send_button_elem = $('#messages_message_send_button');

                if(message.trim() === ''){
                    alert('Please enter message');
                    return false;
                }

                if(toPtoken?.trim() !== '') {
                    messages_message_send_button_elem.attr('disabled', 'disabled');
                    messages_message_send_button_elem.html('Sending <i class="fa fa-circle-o-notch fa-spin"></i>');
                    $.post(_taoh_site_ajax_url, {
                        'taoh_action': 'taoh_post_message',
                        'message': message,
                        "ptoken": toPtoken,
                        "location_path": locationPath
                    }, function (response) {
                        $('#messagesOfflineMessage').val('');
                        $('#messagesOfflineMessageBlock').hide();
                        $('#messagesOfflineSuccessMessage').show();
                        setTimeout(function () {
                            messages_message_send_button_elem.removeAttr('disabled');
                            messages_message_send_button_elem.text('Send');
                            $('#messagesOfflineMessageModal').modal('hide');
                        }, 1500);
                    });
                }

                if(mm_room_key?.trim() !== '' && toPtoken?.trim() !== '') {
                    let sent_time = new Date().getTime();
                    let mc_data = {
                        'taoh_action': 'taoh_room_send_message',
                        'ptoken': my_pToken,
                        'other_ptoken': toPtoken,
                        "message": message,
                        'key': mm_room_key,
                        'sent_time': sent_time
                    };

                    $.post(_taoh_site_ajax_url, mc_data, function (response) {});
                }
            });

            newmessages_btn.on('click', function () {
                comments.animate({scrollTop: comments.prop("scrollHeight")}, 1000);
                newmessages_btn_grp.hide();
            });

            $('#newmessages_close_btn').on('click', function () {
                newmessages_btn_grp.hide();
            });

            comments.on('scroll', function () {
                if(!isScrolledUp(mm_chatContainer)){
                    newmessages_btn_grp.hide();
                }
            });

            // Initial Call
            let init_requestData = getMMRoomsRequestData(my_pToken, lastAllRoomsMessageTime);
            fetchMMRoomsData(init_requestData, false);

            setInterval(function () {
                if (!mm_rooms_isProcessing) {
                    let requestData = getMMRoomsRequestData(my_pToken, lastAllRoomsMessageTime);
                    fetchMMRoomsData(requestData, false);
                }
            }, 5000);
        });

        $(document).on('click', '#messages_offline_send_message_btn', function () {
            if(mm_room_key.trim() !== '' && chatwith.trim() !== ''){
                let respondLocationPath = '<?php echo '/' . TAOH_MESSAGEPAGE_NAME; ?>/chatwith/' + mm_room_key + '-' + my_pToken + '?from=messaging';

                $('#messagesOfflineMessage').val('');
                $('#messagesOfflineToPtoken').val(chatwith);
                $('#messagesOfflineLocationPath').val(respondLocationPath);
                $('#messagesOfflineSuccessMessage').hide();
                $('#messagesOfflineMessageBlock').show();
                $('#messagesOfflineMessageModal').modal('show');
            }
        });

        let prev_userLiveStatusInterval = userLiveStatusInterval;
        function userLiveStatusUpdate(interval) {
            userLiveIntervalId = setInterval(function() {
                if (mm_room_key.trim() !== '' && chatwith.trim() !== '' && typeof getUserLiveStatus === 'function') {
                    getUserLiveStatus(chatwith).then((userLiveStatus) => {
                        if (userLiveStatus.success) {
                            chatwith_liveStatus = Boolean(userLiveStatus.output) ? 1 : 0;

                            const chatArea = $('#chatArea');
                            chatArea.find('.userlivestatus').addClass(chatwith_liveStatus ? 'active-status' : 'active-status-border');
                            chatArea.find('.userlivestatus').removeClass(chatwith_liveStatus ? 'active-status-border' : 'active-status');
                            chatArea.find('.userlivestatus_txt').text(chatwith_liveStatus ? 'Active' : 'Away');


                            if (document.visibilityState === 'visible') userLiveStatusInterval = chatwith_liveStatus ? 60000 : 180000; // 1 minute : 3 minutes
                            if (prev_userLiveStatusInterval !== userLiveStatusInterval) {
                                prev_userLiveStatusInterval = userLiveStatusInterval;
                                clearInterval(userLiveIntervalId);
                                userLiveStatusUpdate(userLiveStatusInterval);
                            }
                        }
                    }).then(() => {
                        if(updateSenderArea) updateMMChatSendArea(mm_room_key, chatwith);
                    });
                }
            }, interval);
        }

        document.addEventListener('visibilitychange', () => {
            userLiveStatusInterval = document.hidden ? 300000: 1000; // 5 minutes : 1 second
            prev_userLiveStatusInterval = userLiveStatusInterval;
            if(userLiveIntervalId) clearInterval(userLiveIntervalId);
            userLiveStatusUpdate(userLiveStatusInterval);
        });

        /* Get User Information */
        async function getUserInfo(pToken_to, ops = 'public', serverFetch = false) {
            if (!pToken_to?.trim()) return null;

            let userInfo = {};

            // Initialize ops object if not exists
            mmuserInfoList[ops] = mmuserInfoList[ops] || {};

            if (!serverFetch) {
                // Try to get userInfo from local variable
                userInfo = mmuserInfoList[ops][pToken_to] || userInfo;

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
                            mmuserInfoList[ops][userInfoObj.ptoken] = userInfoObj;
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
                    mmuserInfoList[ops][srv_userInfoObj.ptoken] = srv_userInfoObj;
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
                mmuserInfoList[ops][userInfo.ptoken] = userInfo;
            }

            return userInfo;
        }

        /* /Get User Information */


        /* Get Room Info */
        async function getRoomInfo(room_hash, ptoken, data = {}, serverFetch = false) {
            if (room_hash.trim() === '') {
                return {};
            }

            let roominfo = {};
            let app = data['app'] || 'custom';
            let type = data['type'] || 'detail';

            if (!serverFetch) {
                // Try to get roominfo from local variable
                if (typeof mmroomInfoList[room_hash] !== 'undefined') {
                    roominfo = mmroomInfoList[room_hash];
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
                            mmroomInfoList[room_hash] = roominfoObj;
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
                        mmroomInfoList[srv_roominfoObj.keyslug] = srv_roominfoObj;
                        roominfo = srv_roominfoObj;
                    }
                } catch (err) {
                    if (err.error === 'room_not_exist' && my_pToken?.trim()) {
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
                        // let create_room_data = await taoh_create_room(create_room_info, my_pToken, data);
                        // if (create_room_data.success && create_room_data.output) {
                            roominfo = create_room_info;
                            mmroomInfoList[room_hash] = roominfo;
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
                mmroomInfoList[room_hash] = roominfo;
            }

            return roominfo;
        }

        /* /Get Room Info */


        /* Rooms Entries */
        function fetchMMRoomsData(requestData, serverFetch = false) {
            if (requestData.ptoken) {
                mm_rooms_isProcessing = true;
                let mm_rooms_messages_key = 'mm_rooms_' + my_pToken;
                IntaoDB.getItem(objStores.ntw_store.name, mm_rooms_messages_key).then((intao_data) => {
                    if (intao_data?.values) {
                        processMMRoomsData(requestData, intao_data.values);
                    }else{
                        processMMRoomsData(requestData, {});
                    }
                });
            }
        }

        function processMMRoomsData(requestData, response) {
            if (response.length == 0) {
                doAfterMMRoomsRender();
            } else {
                // Clearing old rooms from currentRoomMsgList
                for (const key in response) {
                    const newItem = response[key];
                    const existingItem = Object.values(currentRoomMsgList).find((item) => {
                        let conversation_users = [item.ptoken_from, item.ptoken_to];
                        return item.room_hash === newItem.room_hash && conversation_users.includes(newItem.ptoken_from) && conversation_users.includes(newItem.ptoken_to);
                    });

                    if (existingItem) {
                        // Delete the existing item and add new item to currentRoomMsgList
                        for (const mergedKey in currentRoomMsgList) {
                            if (currentRoomMsgList[mergedKey] === existingItem) {
                                delete currentRoomMsgList[mergedKey];
                                currentRoomMsgList[key] = newItem;
                                break;
                            }
                        }
                    } else {
                        // Add the new item to currentRoomMsgList
                        currentRoomMsgList[key] = newItem;
                    }
                }

                renderMMRoomsData(currentRoomMsgList);
            }
        }

        async function renderMMRoomsData(roomMsgList) {
            // let sortDataObject = Object.keys(roomMsgList).sort().reverse().reduce((r, k) => (r[k] = roomMsgList[k], r), {});

            let newObj = {};
            var searchTerm = $("#room_query").val().trim().toLowerCase();
            if (searchTerm.trim() !== "") {
                for (const [key, value] of Object.entries(roomMsgList)) {
                    let to_ptoken = (value.ptoken_from == my_pToken) ? value.ptoken_to : value.ptoken_from;
                    await getUserInfo(to_ptoken).then((userInfo) => {
                        if ((userInfo.chat_name).toLowerCase().includes(searchTerm)) {
                            newObj[key] = value;
                        }
                    }).catch((e) => {
                        console.log(e)
                    });
                }
            } else {
                newObj = roomMsgList;
            }

            let updateUnreadElemClass = '';
            let isUnreadElemExist = false;
            var msg_html = '';

            for await (const [ii, v] of Object.entries(newObj)) {
                let to_ptoken = (v.ptoken_from == my_pToken) ? v.ptoken_to : v.ptoken_from;

                if(!to_ptoken) continue;

                let safeMessageHtml = '';
                const message_content = v.message;
                if (message_content.includes('chat-meeting-link')) {
                    safeMessageHtml = message_content;
                } else {
                    const safeMessage = document.createElement('pre');
                    safeMessage.textContent = truncateMessage(message_content, 60);
                    safeMessageHtml = safeMessage.innerHTML.replace(/\n/g, '<br>')
                        .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');
                }

                var new_class = 'mm_' + v.room_hash + '_' + to_ptoken;
                // $('.' + new_class).addClass('old');

                if (!v.read && !isUnreadElemExist) isUnreadElemExist = true;

                const [room_info, userInfo] = await Promise.all([
                    getRoomInfo(v.room_hash, my_pToken).catch((e) => (console.log(e), null)),
                    getUserInfo(to_ptoken).catch((e) => (console.log(e), null)),
                ]);

                // v.live = 1; // temp added to fix
                // <span class="${(v.live ? 'active-status' : 'active-status-border')} userlivestatus"></span>

                const userAvatarSrc = userInfo?.avatar_image && await checkImageExists(userInfo.avatar_image).catch(() => false)
                    ? userInfo.avatar_image
                    : `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;

                msg_html += `<div class="card-item ${new_class} bottom-chat-list ${((to_ptoken == chatwith && v.room_hash == mm_room_key) ? 'active' : '')} read read_${v.read} openchatacc" data-item="${ii}" data-read="${(!v.read) ? 0 : 1}" data-room="${v.room_hash}" data-chatwith="${to_ptoken}" data-chatname="${userInfo?.chat_name}" style="${v.read ? '' : 'background: rgba(50, 205, 50, 0.3)'}">
                    <div class="d-flex">
                        <div class="col-3 p-0">
                            <span class="openProfileModal" data-profile_token="${to_ptoken}">
                                <img src="${userAvatarSrc}" width="100" class="chat-user-avatar" alt="avatar">
                            </span>
                        </div>
                        <div class="col-9 chat-user-list-bottom">
                            <div class="d-flex">
                                <h5 class="col p-0 user-title"><span data-profile_token="${to_ptoken}" class="openProfileModal">${userInfo?.chat_name}</span></h5>
                                <p class="chat-date-time-count" style="display:block"><span>${timeAgo(v.message_time / 1000)}</span></p>
                            </div>
                            <p class="room_title text-info font-italic">${((typeof room_info['club'] != 'undefined') ? room_info['club'].title : 'Room - ' + room_info['keyslug'])}</p>
                            <p class="card-text" style="color:#0d233e">${safeMessageHtml}</p>
                        </div>
                    </div>
                </div>`;

                if (!v.read && to_ptoken == chatwith && v.room_hash == mm_room_key) {
                    updateChatRoomReadStatus(chatwith, mm_room_key, ii);
                    updateUnreadElemClass = new_class;
                }
            }

            left_users_list.empty();
            $('.no_comments_img').remove();

            left_users_list.html(msg_html);

            if (updateUnreadElemClass.trim() !== '') {
                const updateUnreadElem = $('.' + updateUnreadElemClass);
                if (updateUnreadElem.length > 0) {
                    updateUnreadElem.data('read', 1);
                    updateUnreadElem.css('background', 'inherit');
                }
            }

            if (messaging_chat_user_upd == 0 && messaging_chat_arr.length >= 2) {
                const roomElem = $('.mm_' + messaging_chat_arr[0] + '_' + messaging_chat_arr[1]);
                if (roomElem.length > 0) {
                    roomElem.trigger('click');
                    messaging_chat_user_upd = 1;
                }
            }

            if (mm_readSwitch.prop('checked') && isUnreadElemExist) mm_readSwitch.prop('checked', false);

            doAfterMMRoomsRender();
        }

        function doAfterMMRoomsRender() {
            if ($('#left_users_list').find('.openchatacc').length == 0) {
                $('#left_users_list').html('<div class="fs-16" id="no_users_list">No results found</div>');

                $('#current_chat_with_info').text('Choose anyone to continue the conversation.');

                $('#chat-area').hide();
                $('#message-area').hide();
                $('#message_helper').hide();
                // $('#message_count').hide();
                $('#mm_video').hide();
                comments.empty();

                $('#mm_right_section').hide();
                $('#profile_info').empty();
            } else {
                $('#no_users_list').remove();
            }

            if(mm_room_key.trim() === '' || chatwith.trim() === '' || $('#left_users_list').find('.openchatacc').length === 0){
                comments.html(`<img src="${_taoh_site_url_root + '/assets/images/no-chat.svg'}" class="no_comments_img" alt="No comments">`);
            }else{
                $('.no_comments_img').remove();
            }

            mm_rooms_isProcessing = false;
            taoh_Loader($('#mm_rooms_loader'), false);
        }

        function getMMRoomsRequestData(pToken, lastAllRoomsMessageTime) {
            return {
                'ptoken': pToken,
                'lastAllMessageTime': lastAllRoomsMessageTime
            };
        }

        /* /Rooms Entries */


        /* MM chat */

        function getMMChatRequestData(pToken_from, pToken_to, key, callFromEvent = 'init') {
            return {
                "pToken_from": pToken_from,
                "pToken_to": pToken_to,
                "page": mmChatPageNo,
                "itemPerPage": mmChatItemsPerPage,
                "key": key,
                "callFromEvent": callFromEvent
            };
        }

        function updateUserProfileInfo(chatwith, serverFetch = false) {
            $('#mm_right_section').show();
            $("#profile_info").html('<h5 class="text-center">Loading profile information...</h5>');

            getUserInfo(chatwith, 'full', serverFetch).then((userInfo) => {
                $.post(_taoh_site_message_ajax_url, {
                    'taoh_action': 'taoh_user_profile_details',
                    'user_data': JSON.stringify(userInfo)
                }, function (response) {
                    $("#profile_info").html(response);
                });
            }).catch((e) => {
                console.log('updateUserProfileInfo error:', e);
            });
        }

        function copyToMessage(msg) {
            commentInput.val(msg);
        }

        function sendMMChat(message, my_pToken, chatwith, mm_room_key, user_type = 'user') {

            if (mm_room_key.trim() === '' || chatwith.trim() === '') {
                alert('Please select a user you want to chat.');
                return false;
            }

            if (message.trim() === '') {
                alert('Message seems empty! Please enter valid message to send.');
                return false;
            }

            if(user_type !== 'system'){
                commentInput.val('');
                commentInput.empty();
                commentInput.text('');

                $('#message_helper').hide();
            }

            let totalchatscnt = (parseInt(chatCount.text()) || 0) + 1;
            chatCount.text(totalchatscnt);

            let sent_time = new Date().getTime();

            var data = {
                'taoh_action': 'taoh_room_send_message',
                'message': message,
                'ptoken': my_pToken,
                'other_ptoken': chatwith,
                'user_type': user_type,
                'key': mm_room_key,
                'sent_time': sent_time
            };

            let chatresponse = {
                "chat": [{
                    "ptoken": data.ptoken,
                    "message": data.message,
                    "to_ptoken": data.other_ptoken,
                    "user_type": data.user_type,
                    "room_hash": data.key,
                    "sent_time": data.sent_time,
                    "time": (data.sent_time * 1000)
                }],
                "isTempMsg": true,
                "overallChatCount": totalchatscnt,
                "recent_rendered_items": {}
            };
            renderMMMessages(chatresponse);

            let chat_temp_messages_key = 'cm_temp_' + mm_room_key + '_' + data.ptoken;
            let ptokenTo = data.other_ptoken;

            // Store temp messages in Indexed DB then send to server
            IntaoDB.getItem(objStores.ntw_store.name, chat_temp_messages_key).then((intao_data) => {
                let updatedResponse = {};
                if (intao_data?.values) {
                    updatedResponse = intao_data.values;
                }
                if (!(ptokenTo in updatedResponse)) updatedResponse[ptokenTo] = {};
                Object.assign(updatedResponse[ptokenTo], {[data.sent_time]: chatresponse.chat[0]});
                IntaoDB.setItem(objStores.ntw_store.name, {taoh_ntw: chat_temp_messages_key, values: updatedResponse, timestamp: Date.now()});
            }).then(() => {
                if (navigator.onLine) {
                    $.ajax({
                        url: _taoh_site_ajax_url,
                        type: 'post',
                        data: data,
                        dataType: 'json',
                        success: function (response) {
                            ft_ntw_reFetchRequired = true;
                            update_stored_time_in_temp_messages(chat_temp_messages_key, response, ptokenTo);

                            taoh_mm_post_metrics('chatpost');
                        },
                        error: function (xhr, status, error) {
                            console.log('Error:', xhr.status);
                            checkOfflineMessage = 1;
                            if (typeof syncOfflineMessages === 'function') {
                                syncOfflineMessages();
                            }
                        }
                    });
                } else {
                    checkOfflineMessage = 1;
                    if (typeof syncOfflineMessages === 'function') {
                        syncOfflineMessages();
                    }
                }
            });
        }

        function update_stored_time_in_temp_messages(chat_temp_messages_key, returnedData, ptokenTo) {
            if (returnedData.success) {
                // Update stored_time in temp messages
                IntaoDB.getItem(objStores.ntw_store.name, chat_temp_messages_key).then((intao_data) => {
                    if (intao_data?.values) {
                        let updatedResponse = intao_data.values;
                        if ((ptokenTo in updatedResponse) && (returnedData.sent_time in updatedResponse[ptokenTo])) {
                            updatedResponse[ptokenTo][returnedData.sent_time].stored_time = returnedData.stored_time;
                            IntaoDB.setItem(objStores.ntw_store.name, {taoh_ntw: chat_temp_messages_key, values: updatedResponse, timestamp: Date.now()});
                        }
                    }
                });
            }
        }

        function fetchMMChatData(requestData, serverFetch = false) {
            if (requestData.pToken_from && requestData.pToken_to) {
                if(mm_isProcessing){
                    setTimeout(() => {
                        fetchMMChatData(requestData, serverFetch);
                    }, 1000);
                    return;
                }

                mm_isProcessing = true;

                lastchatwith = $("#lastchatwith").val();
                // if (lastchatwith != requestData.pToken_to) lastNTWMsgCheckedTimestamp = getAndSetLastCheckedTimestamp('lastNTWMsgCheckedTimestamp', 0, 1);

                // let formData = getNetworkingMessagesFormData('ntw', mm_room_key, requestData.pToken_from, 0, getAndSetLastCheckedTimestamp('lastNTWMsgCheckedTimestamp', lastNTWMsgCheckedTimestamp, 1));

                $("#lastchatwith").val(requestData.pToken_to);

                let chat_messages_key = 'cm_' + mm_room_key + '_' + requestData.pToken_from + '_' + requestData.pToken_to;

                IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key).then((intao_data) => {
                    if (intao_data && intao_data.timestamp) {
                        processMMChatData(requestData, intao_data.values);
                    } else {
                        // Sending default empty data to render chat window
                        const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastNTWMsgCheckedTimestamp', lastNTWMsgCheckedTimestamp, 1);
                        processMMChatData(requestData, {"chat": {}, "last_update_time": lastCheckedTimestamp, "success": true});
                    }
                });
            }
        }

        function processMMChatData(requestData, response) {
            let processedResponse = {};
            let recentItemsObject = {};
            let recentRenderedItemsObject = {};
            let totalchats = 0;

            if (response.success) {
                let chats = response.chat ? response.chat : [];

                let allChatKeys = Object.keys(chats);
                totalchats = allChatKeys.length;
                if (totalchats > 0) {
                    if (requestData.callFromEvent == "init" || mm_msgDownIndex == 0) {
                        const slice_end = (totalchats - ((requestData.page - 1) * requestData.itemPerPage));
                        if (slice_end > 0) {
                            const recentItems = Object.entries(chats).slice(-(requestData.page * requestData.itemPerPage), slice_end);
                            recentItemsObject = Object.fromEntries(recentItems);
                        }
                    } else if (requestData.callFromEvent == "scrollup") {
                        let currentFirstMsgIndex = allChatKeys.indexOf(mm_msgUpIndex);
                        if (currentFirstMsgIndex > -1) {
                            const recentItems = Object.entries(chats).slice(-(requestData.page * requestData.itemPerPage), currentFirstMsgIndex);
                            recentItemsObject = Object.fromEntries(recentItems);
                        }
                    } else if (requestData.callFromEvent == "interval") {
                        let currentLastMsgIndex = allChatKeys.indexOf(mm_msgDownIndex);
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

            renderMMMessages(processedResponse);
        }

        async function compiledMMMsgHtml(cd) {
            let compiledMMMsgHtml;
            let safeMessageHtml = '';
            const message_content = cd.message;
            if (message_content.includes('chat-meeting-link') || cd.userType === 'system') {
                safeMessageHtml = message_content;
            } else {
                const safeMessage = document.createElement('pre');
                safeMessage.textContent = message_content;
                safeMessageHtml = safeMessage.innerHTML.replace(/\n/g, '<br>')
                    .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');
            }

            if (cd.userType === 'system') {
                compiledMMMsgHtml = `<li class="badge-system clearfix"><span class="badge badge-secondary">${safeMessageHtml}</span></li>`;
            } else {
                compiledMMMsgHtml = `<li class="chat-item align-items-end border-0 ${cd.ptokenTo === cd.ptokenFrom ? 'mine' : 'others'} ${'msg_' + (cd.time)} ${cd.isTempMsg ? 'temp_msg new':''} py-2" id="${'msg_' + (cd.time)}">
                <div class="comment-body pt-0 card">
                    <div class="d-flex justify-content-between py-2">
                        <span class="chatname pr-3">
                            <span style="cursor: pointer" class="comment-user text-primary font-weight-bold">${cd.name}</span>
                        </span>
                        <small class="text-muted">${timeAgo(cd.time / 1000)}</small>
                    </div>

                    <p class="comment-text pt-1 pb-2 lh-22">${safeMessageHtml}</p>
                </div>
            </li>`;
            }

            return compiledMMMsgHtml;
        }

        async function getMMChatMsgHtml(response, tempMsgList) {
            let chats = response.chat || {};
            let isTempMsg = response.isTempMsg || false;

            let messageHtml = '';
            let ptokenTo = chatwith;
            let do_highlight = false;
            let compiledChatKeys = {chats: [], temp_chats: []};

            for (const [key, v] of Object.entries(chats)) {
                const userInfo = await getUserInfo(v.ptoken);
                let stored_time = v.hasOwnProperty('stored_time') ? v.stored_time : '';

                if (v.ptoken != my_pToken) {
                    if (!do_highlight && response.callFromEvent !== 'init' && response.callFromEvent !== 'scrollup') do_highlight = true;
                    if (response.callFromEvent !== 'init' && response.callFromEvent !== 'scrollup') mm_newMessagesCnt++;
                }

                let compileData = {
                    ptokenTo: v.ptoken,
                    message: v.message,
                    time: v.time,
                    name: userInfo.chat_name,
                    ptokenFrom: my_pToken,
                    userType: v.user_type,
                    isTempMsg: isTempMsg,
                    stored_time: stored_time
                };
                messageHtml += await compiledMMMsgHtml(compileData);

                compiledChatKeys.chats.push(isTempMsg ? (v.time).toString() : key);

                const tempMsgElem = $('#msg_' + (v.sent_time * 1000));
                if (tempMsgElem.length) tempMsgElem.removeClass('new');
            }

            if (!isTempMsg && typeof tempMsgList[ptokenTo] != 'undefined') {
                let allSentTimes = Object.keys(chats).map(k => chats[k]['sent_time']);
                for (const [key, v] of Object.entries(tempMsgList[ptokenTo])) {
                    const userInfo = await getUserInfo(v.ptoken);
                    let stored_time = v.hasOwnProperty('stored_time') ? (v.stored_time).toString() : '';

                    if (!allSentTimes.includes((v.sent_time).toString())) {
                        let compileData = {
                            ptokenTo: v.ptoken,
                            message: v.message,
                            time: v.time,
                            name: userInfo.chat_name,
                            ptokenFrom: my_pToken,
                            userType: v.user_type,
                            isTempMsg: true,
                            stored_time: stored_time
                        };
                        messageHtml += await compiledMMMsgHtml(compileData);

                        compiledChatKeys.temp_chats.push(key);
                    }

                    const tempMsgElem = $('#msg_' + (v.sent_time * 1000));
                    if (tempMsgElem.length) tempMsgElem.removeClass('new');
                }
            }

            return {messageHtml, compiledChatKeys, do_highlight};
        }

        function renderMMMessages(response) {
            if (!response) {
                doAfterMMMsgRender(response);
                return;
            }

            let chats = response.chat || {};
            let allChatKeys = Object.keys(chats);
            // let totalChats = allChatKeys.length;

            // if (totalChats === 0) {
            //     if (response.callFromEvent === "scrollup") {
            //         mm_msgScrollUpEnded = true;
            //     }
            //     doAfterMMMsgRender(response);
            //     return;
            // }
            let chat_temp_messages_key = 'cm_temp_' + mm_room_key + '_' + my_pToken;
            IntaoDB.getItem(objStores.ntw_store.name, chat_temp_messages_key)
                .then(intao_data => intao_data?.values || {})
                .then(tempMsgList => {
                    return getMMChatMsgHtml(response, tempMsgList);
                })
                .then(({messageHtml, compiledChatKeys, do_highlight}) => {
                    if (mmChatFirstCall === 1 || response.callFromEvent === 'init') {
                        comments.empty();
                    }

                    $(".temp_msg:not(.new)").remove();

                    if (compiledChatKeys.chats.length > 0 || compiledChatKeys.temp_chats.length > 0) {
                        $('#no_message').remove();
                        $('#message_helper').hide();

                        if (response.callFromEvent === 'scrollup') {
                            comments.prepend(messageHtml);
                        } else {
                            comments.append(messageHtml);
                            if (isScrolledUp(mm_chatContainer) && mmChatFirstCall !== 1) {
                                if (mm_newMessagesCnt > 0) {
                                    newmessages_btn.find('span').text(mm_newMessagesCnt + ' new messages');
                                    newmessages_btn_grp.show();
                                }
                            } else {
                                mm_chatContainer.scrollTop = mm_chatContainer.scrollHeight;
                                mm_newMessagesCnt = 0;
                            }
                        }
                    }

                    if (compiledChatKeys.chats.length > 0 && !response.isTempMsg) {
                        if (response.callFromEvent === 'init') {
                            mm_msgUpIndex = allChatKeys[0];
                            mm_msgDownIndex = allChatKeys.pop();
                        } else if (response.callFromEvent === 'scrollup') {
                            mm_msgUpIndex = allChatKeys[0];
                        } else if (response.callFromEvent === 'interval') {
                            mm_msgDownIndex = allChatKeys.pop();
                        }
                    }

                    mmChatFirstCall = 0;
                    response.compiledChatKeys = compiledChatKeys;

                    // Clearing left over temp messages if already rendered
                    if ($('.temp_msg').length){
                        let recentRenderedItems = response.recent_rendered_items;
                        for (const [k, value] of Object.entries(recentRenderedItems)) {
                            let tempMsgElem = $('.msg_' + (value.sent_time * 1000));
                            if (tempMsgElem.length) tempMsgElem.remove();
                        }
                    }

                    if (do_highlight) highlightMessages();
                })
                .then(() => {
                    doAfterMMMsgRender(response);
                });

            if(response.callFromEvent === "scrollup"){
                mm_msgScrollUpEnded = false;
            }
        }

        function doAfterMMMsgRender(response) {
            let overallChatCountWithTemp = response.overallChatCount;
            if(typeof response.compiledChatKeys != 'undefined'){
                overallChatCountWithTemp += response.compiledChatKeys.temp_chats.length;
            }

            if (overallChatCountWithTemp > 0) {
                chatCount.text(overallChatCountWithTemp);
                $('#message_helper').hide();
            } else {
                mm_msgUpIndex = 0;
                mm_msgDownIndex = 0;
                if (chatwith_liveStatus == 1) $('#message_helper').show();
                comments.html("<p id='no_message'>No Messages yet!</p>");
            }

            if (response.hasOwnProperty('last_update_time')) {
                mmChatLastTime = response.last_update_time || 0;
            }

            updateMMChatSendArea(mm_room_key, chatwith);

            mm_isProcessing = false;
            taoh_Loader($('#mm_chat_loader'), false);
        }

        function updateMMChatSendArea(mm_room_key, chatwith) {
            if (mm_room_key.trim() !== '' && chatwith.trim() !== '') {
                if (chatwith_liveStatus == 1) {
                    $('#message-area').hide();
                    $('#chat-area').show();
                    $('#mm_video').show();
                    if (!$('.sender_part').is(":visible")) {
                        $('.sender_part').show();
                    }
                } else {
                    $('#chat-area').hide();
                    $('.sender_part').hide();
                    $('#message_helper').hide();
                    $('#mm_video').hide();
                    $('#message-area').show();
                }
                updateSenderArea = true;
            }
        }

        let mm_prevScrollPos = mm_chatContainer.scrollTop;
        mm_chatContainer.addEventListener('scroll', () => {
            const mm_currentScrollPos = mm_chatContainer.scrollTop;

            if ((mm_currentScrollPos < mm_prevScrollPos) && mm_currentScrollPos < 10 && !mm_msgScrollUpEnded) {
                // Trigger only if the user scrolls up to the top of the chat container
                mmChatPageNo++;

                taohLoader(document.getElementById('mm_chat_loader'), true);
                let requestData = getMMChatRequestData(my_pToken, chatwith, mm_room_key, 'scrollup');
                throttle(fetchMMChatData(requestData, false), (mm_isProcessing ? 500 : 100));
            }

            // Update the previous scroll position to find the direction of scrolling
            mm_prevScrollPos = mm_currentScrollPos;
        });

        function initMMChatDataInterval() {
            if (mmChatDataInterval) clearInterval(mmChatDataInterval);
            mmChatDataInterval = setInterval(function () {
                if (!mm_isProcessing) {
                    if (my_pToken.trim() !== '' && chatwith.trim() !== '') {
                        let requestData = getMMChatRequestData(my_pToken, chatwith, mm_room_key, 'interval');
                        fetchMMChatData(requestData, false);
                    }
                }
            }, 3000);
        }

        if(chatwith.trim() !== '') {
            initMMChatDataInterval();
        }


        function updateChatRoomReadStatus(ptoken_to, mm_room_key, item_key = '') {
            if (item_key != '') {
                let mm_rooms_entries_key = 'mm_rooms_' + my_pToken;
                IntaoDB.getItem(objStores.ntw_store.name, mm_rooms_entries_key).then((intao_data) => {
                    if (intao_data?.values) {
                        let updatedResponse = intao_data.values;
                        if (item_key in updatedResponse) {
                            updatedResponse[item_key].read = 1;
                            IntaoDB.setItem(objStores.ntw_store.name, {taoh_ntw: mm_rooms_entries_key, values: updatedResponse, timestamp: Date.now()});
                        }
                    }
                });
            }
        }

        async function updateChatWindowHeader(chatwith) {
            const [userLiveStatus, userInfo] = await Promise.all([
                getUserLiveStatus(chatwith).catch((e) => {console.log(e)}),
                getUserInfo(chatwith, 'public').catch((e) => {console.log(e)}),
            ]);
            chatwith_liveStatus = Boolean(userLiveStatus.output) ? 1 : 0;

            let liveStatus = (chatwith_liveStatus ? 'active-status' : 'active-status-border');
            let statusName = (chatwith_liveStatus ? 'Active' : 'Away');

            const userAvatarSrc = userInfo?.avatar_image && await checkImageExists(userInfo.avatar_image).catch(() => false)
                ? userInfo.avatar_image
                : `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;

            $("#current_chat_with_info").html(' ' + userInfo.chat_name + '<br><span class="userlivestatus ' + liveStatus + '"></span><small class="userlivestatus_txt ml-3">' + statusName + '</small>');
            $('#windowchatavatar').html(`<div class="comment-avatar chat_entries mr-2" style="background:#52514f;vertical-align:middle;margin-right:1em;">
            <img width="40" class="lazy" src="${userAvatarSrc}" alt="User Avatar"></div>`);
        }

        $(document).on('click', '.openchatacc', async function () {
            mm_room_key = $(this).data("room").toString();
            chatwith = $(this).data("chatwith").toString();

            $("#current_chat_with_info").html('');
            $('#windowchatavatar').html('');
            $("#profile_info").html('');
            $("#comments").empty();

            $('#message_helper').hide();
            $('#mm_video').hide();
            $('.sender_part').hide();
            $('#message-area').hide();

            taoh_Loader($('#mm_chat_loader'), true);

            if (mmChatDataInterval) clearInterval(mmChatDataInterval);

            userLiveStatusInterval = 3000;
            if (userLiveIntervalId) clearInterval(userLiveIntervalId);

            updateUserProfileInfo(chatwith, false);

            await updateChatWindowHeader(chatwith).then(() => {
                chat_window = 1;
                mmChatFirstCall = 1;
                mmChatLastTime = 0;
                mm_msgNewEntriesCnt = 0;
                mm_msgScrollUpEnded = false;
                mmChatPageNo = 1;
                updateSenderArea = false;
                lastChatRoomTime = 0;

                let requestData = getMMChatRequestData(my_pToken, chatwith, mm_room_key, 'init');
                fetchMMChatData(requestData);

                initMMChatDataInterval();

                userLiveStatusUpdate(userLiveStatusInterval);
            });

            // Highlight the chatting user in the entries list
            let mm_entry_active_cls = '.mm_' + mm_room_key + '_' + chatwith;
            $('#left_users_list').find(".bottom-chat-list.active").removeClass('active');
            $('#left_users_list').find(mm_entry_active_cls).addClass('active');

            // Update chat room read status
            if (typeof $(this).data('read') !== 'undefined') {
                if ($(this).data('read') == 0) {
                    let item_key = $(this).data('item') ?? '';
                    $(this).data('read', 1);
                    $(this).addClass('read_1')
                    $(this).removeClass('read_0');
                    $(this).css("background", "inherit");
                    updateChatRoomReadStatus(chatwith, mm_room_key, item_key);
                }
            }
        });

        $('#commentInput').keydown(function (event) {
            if (event.key === 'Enter' && !event.shiftKey && !event.ctrlKey) {
                event.preventDefault();
                if ($('#sendChat').length > 0) $("#sendChat").trigger('click');
            } else if (event.key === 'Enter' && event.ctrlKey) {
                // Insert a new line when Ctrl + Enter is pressed
                const textarea = $(this)[0];
                const cursorPosition = textarea.selectionStart;
                const textBeforeCursor = textarea.value.substring(0, cursorPosition);
                const textAfterCursor = textarea.value.substring(cursorPosition);
                textarea.value = textBeforeCursor + '\n' + textAfterCursor;
                textarea.selectionStart = textarea.selectionEnd = cursorPosition + 1;
                event.preventDefault();
            }
        });

        function highlightMessages() {
            $('.chatBlock').addClass("highlight_msg");
            setTimeout(() => {
                $('.chatBlock').removeClass('highlight_msg');
            }, 5000);
        }

        /* /MM chat */

        function mm_makeAllRead() {
            if (mm_readSwitch.is(":checked")) {
                let mm_rooms_entries_key = 'mm_rooms_' + my_pToken;
                IntaoDB.getItem(objStores.ntw_store.name, mm_rooms_entries_key).then((intao_data) => {
                    if (intao_data?.values) {
                        let updatedResponse = intao_data.values;
                        for (const [k, value] of Object.entries(updatedResponse)) {
                            let to_ptoken = (value.ptoken_from == my_pToken) ? value.ptoken_to : value.ptoken_from;
                            if (value.read == 0) {
                                value.read = 1;
                                $('.mm_' + value.room_hash + '_' + to_ptoken).addClass('read_1');
                                $('.mm_' + value.room_hash + '_' + to_ptoken).removeClass('read_0');
                                $('.mm_' + value.room_hash + '_' + to_ptoken).css('background', 'inherit');
                            }
                        }
                        IntaoDB.setItem(objStores.ntw_store.name, {taoh_ntw: mm_rooms_entries_key, values: updatedResponse, timestamp: Date.now()});
                    }
                });
            }
        }

        function taoh_mm_post_metrics(metrics) {
            if (mm_room_key.trim() !== '' && my_pToken.trim() !== '') {

                save_metrics('central_networking',metrics,mm_room_key);	
                
            }
        }

    </script>

<?php
taoh_get_footer();
?>