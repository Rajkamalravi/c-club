const ntw_chatContainer = document.getElementById('chat-conversation-direct');
const commentInput = $('#chat_input'); // :rk change to common name
let comments = usersConversationList;

let ntwChatLastTime = 0;
let ntw_newMessagesCnt = 0;
let ntw_entries_cleared = 1;
let ntw_isProcessing = false;
let ntw_msgScrollUpEnded = false;
let ntw_msgUpIndex = 0;
let ntw_msgDownIndex = 0;
let ntwChatPageNo = 1;
const ntwChatItemsPerPage = 10;
let ntw_chatBadgeUpDate = '';
let ntw_chatBadgeDownDate = '';
let ntwChatDataInterval;
let ntwChatDataFromServerInterval;
let ntwUserEntriesIntervalId;
let ntwUserEntriesInterval = 12000; // 12 seconds
let ntw_empty_dm_msg = 0;

$(document).ready(function () {

});

function taoh_ntw_post_metrics(metrics) {
    if (ntw_room_key?.trim() !== '' && my_pToken?.trim() !== '') {
        save_metrics('networking', metrics, ntw_room_key);
    }
}

function sendNTWChat(message, my_pToken, chatwith, ntw_room_key, channel_id, user_type = 'user') {
    if (ntw_room_key?.trim() === '' || channel_id?.trim() === '' || chatwith?.trim() === '') {
        let err_message = (chatwith?.trim() === '')
            ? 'Please select a user you want to chat.'
            : 'Something went wrong. Please try again.';
        jq_confirm_alert('Invalid Data', err_message, 'orange');
        return false;
    }

    if (message?.trim() === '') {
        jq_confirm_alert('Warning', 'Message seems empty! Please enter a valid message to send.', 'orange');
        return false;
    }

    const proceedWithSending = () => {
        const sent_time = new Date().getTime();


        const data = {
            'taoh_action': 'taoh_direct_send_message',
            'message': message,
            'ptoken': my_pToken,
            'other_ptoken': chatwith,
            'user_type': user_type,
            'key': ntw_room_key,
            'channel_id': channel_id,
           // 'event_token' : eventtoken,
            'chat_on': selectedChat,
            'sent_time': sent_time,
            "country": my_country_name,
            "my_link": my_link,
        };

        const chatresponse = {
            "chat": [{
                "ptoken": data.ptoken,
                "message": data.message,
                "to_ptoken": data.other_ptoken,
                "user_type": data.user_type,
                "room_hash": data.key,
                'channel_id': data.channel_id,
                'message_id': '',
                "sent_time": data.sent_time,
                "time": (data.sent_time * 1000),
                "country": my_country_name,
                "my_link": my_link
            }],
            "isTempMsg": true,
            "overallChatCount": 1,
            "firstKey": null,
            "recent_rendered_items": {}
        };

        renderNTWMessages(chatresponse);

        const chat_temp_messages_key = `cm_temp_${ntw_room_key}_${data.ptoken}`;
        const ptokenTo = data.other_ptoken;

        DMLastMsgSentTime = data.sent_time;

        IntaoDB.getItem(objStores.ntw_store.name, dojo_data_key).then((intao_data) => {
            let updatedResponse = {};
            if (intao_data?.values) {
                updatedResponse = intao_data.values;
            }
            updatedResponse.cm_last_msg_sent_time = data.sent_time;
            return IntaoDB.setItem(objStores.ntw_store.name, {
                taoh_ntw: dojo_data_key,
                values: updatedResponse,
                timestamp: Date.now()
            });
        });

        IntaoDB.getItem(objStores.ntw_store.name, chat_temp_messages_key).then((intao_data) => {
            let updatedResponse = {};
            if (intao_data?.values) {
                updatedResponse = intao_data.values;
            }
            if (!(ptokenTo in updatedResponse)) updatedResponse[ptokenTo] = {};
            Object.assign(updatedResponse[ptokenTo], { [data.sent_time]: chatresponse.chat[0] });

            return IntaoDB.setItem(objStores.ntw_store.name, {
                taoh_ntw: chat_temp_messages_key,
                values: updatedResponse,
                timestamp: Date.now()
            });
        }).then(() => {
            if (ntw_asm_enable) {
                ntw_asm_current_index++;
                if (ntw_asm_indexes.includes(ntw_asm_current_index)) {
                    // Remove current index if more than one entry exists
                    if (ntw_asm_indexes.length > 1) {
                        ntw_asm_indexes = ntw_asm_indexes.filter(item => item !== ntw_asm_current_index);
                    }

                    const ntw_asm_current_index_msg = ntw_asm_index_messages?.[ntw_asm_current_index];
                    if (ntw_asm_current_index_msg?.trim()) {
                        taoh_set_info_message(ntw_asm_current_index_msg, false);
                        // sendNTWAsm(ntw_asm_current_index_msg, my_pToken, chatwith, ntw_room_key, channel_id);
                    }

                    ntw_asm_current_index = 0;
                }
            }


            const ntw_comment_send_btn = $('#chat-send-btn');

            if (navigator.onLine) {
                ntw_comment_send_btn.prop('disabled', true).find('i').removeClass('bxs-send').addClass('bx-loader-alt bx-spin');

                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'post',
                    data: data,
                    dataType: 'json',
                    success: function (response) {
                        if (response?.success) {
                            ft_ntw_reFetchRequired = true;
                            update_stored_time_in_temp_messages(chat_temp_messages_key, response, ptokenTo);
                            taoh_ntw_post_metrics('chatpost');
                        }
                        if(ntw_empty_dm_msg == 1) {
                            loadChannelList(1);
                            ntw_empty_dm_msg = 0;
                        }

                        ntw_comment_send_btn.prop('disabled', false).find('i').removeClass('bx-loader-alt bx-spin').addClass('bxs-send');
                    },
                    error: function () {
                        console.log('Send failed. Storing offline.');
                        ntw_comment_send_btn.prop('disabled', false).find('i').removeClass('bx-loader-alt bx-spin').addClass('bxs-send');
                        // checkOfflineMessage = 1;
                        // if (typeof syncOfflineMessages === 'function') syncOfflineMessages();
                    }
                });
            } else {
                ntw_comment_send_btn.prop('disabled', false).find('i').removeClass('bx-loader-alt bx-spin').addClass('bxs-send');
                checkOfflineMessage = 1;
                if (typeof syncOfflineMessages === 'function') syncOfflineMessages();
            }
        });
    };

    // Clear message input
    commentInput.val('');

    //alert(selectedUser)

    if(chatwith == 'organizer' && channel_id == 'sidekick'){

        proceedWithSending(); // For system messages
    }
    else if (user_type !== 'system' ) {

        if (!chatwith_liveStatus) {

            //let locationPath = currentFullPath + '?chatwith=' + chatwith;
            let locationPath = currentFullPath + '?chatwith=' + my_pToken;
            taoh_set_warning_message('It appears the user is currently offline. Would you like to send a copy of this message via email?', false, 'toast-middle', [
                {
                    text: 'Yes',
                    action: () => {
                        $.post(_taoh_site_ajax_url, {
                            'taoh_action': 'taoh_post_message',
                            'message': message,
                            "ptoken": chatwith,
                            "location_path": locationPath
                        }, function () {
                            proceedWithSending();
                        });
                    },
                    class: 'dojo-v1-btn float-right mt-3 mb-3'
                },
                {
                    text: 'No',
                    action: () => {
                        proceedWithSending();
                    },
                    class: 'dojo-v1-btn float-right mt-3 mb-3 mr-2'
                }
            ]);

            // $.confirm({
            //     title: 'Confirmation',
            //     content: 'It appears the user is currently offline. Would you like to send a copy of this message via email?',
            //     type: 'orange',
            //     buttons: {
            //         cancel: {
            //             text: 'No',
            //             action: function () {
            //                 proceedWithSending();
            //             }
            //         },
            //         confirm: {
            //             text: 'Yes',
            //             btnClass: 'btn-blue',
            //             action: function () {
            //                 $.post(_taoh_site_ajax_url, {
            //                     'taoh_action': 'taoh_post_message',
            //                     'message': message,
            //                     "ptoken": chatwith,
            //                     "location_path": locationPath
            //                 }, function () {
            //                     proceedWithSending();
            //                 });
            //             }
            //         }
            //     }
            // });
        } else {
            proceedWithSending(); // User is online, proceed directly
        }
    } else {
        proceedWithSending(); // For system messages
    }
}

function update_stored_time_in_temp_messages(chat_temp_messages_key, returnedData, ptokenTo) {
    // Update stored_time in temp messages
    IntaoDB.getItem(objStores.ntw_store.name, chat_temp_messages_key).then((intaoData) => {
        if (!intaoData?.values) return;

        const updatedResponse = intaoData.values;
        const ptokenData = updatedResponse[ptokenTo];

        if (!ptokenData || !ptokenData[returnedData.sent_time]) return;

        // const chat_messages_key = `cm_${ntw_room_key}_${ptokenData[returnedData.sent_time].room_hash}_${ptokenData[returnedData.sent_time].ptoken}_${ptokenData[returnedData.sent_time].to_ptoken}`;

        ptokenData[returnedData.sent_time].message_id = returnedData.message_id;
        ptokenData[returnedData.sent_time].stored_time = returnedData.stored_time;

        IntaoDB.setItem(objStores.ntw_store.name, {
            taoh_ntw: chat_temp_messages_key,
            values: updatedResponse,
            timestamp: Date.now()
        });
    }).then(() => {
        const tempMsgElem = $('#msg_' + (returnedData.sent_time * 1000));
        if (tempMsgElem.length) {
            tempMsgElem.setSyncedData('ntw_message_id', returnedData.message_id);
            tempMsgElem.find('.check-message-icon i').removeClass('bx-sync bx-spin'); // .addClass('bx-check-double')
        }
    });
}

/*function sendNTWAsm(message, my_pToken, chatwith, ntw_room_key, channel_id, user_type = 'system') {
    if (ntw_room_key?.trim() === '' || channel_id?.trim() === '' || chatwith?.trim() === '') {
        return false;
    }

    if (message?.trim() === '') {
        return false;
    }

    const proceedWithSending = () => {
        const sent_time = new Date().getTime();

        const data = {
            'message': message,
            'ptoken': my_pToken,
            'other_ptoken': chatwith,
            'user_type': user_type,
            'key': ntw_room_key,
            'channel_id': channel_id,
            // 'event_token' : eventtoken,
            'chat_on': selectedChat,
            'sent_time': sent_time
        };

        const chatresponse = {
            "chat": [{
                "ptoken": data.ptoken,
                "message": data.message,
                "to_ptoken": data.other_ptoken,
                "user_type": data.user_type,
                "room_hash": data.key,
                'channel_id': data.channel_id,
                'message_id': '',
                "sent_time": data.sent_time,
                "time": (data.sent_time * 1000)
            }],
            "isTempMsg": true,
            "overallChatCount": 1,
            "firstKey": null,
            "recent_rendered_items": {}
        };

        // renderNTWMessages(chatresponse);

        ntw_asm_queue[data.sent_time] = chatresponse.chat[0];
    };

    proceedWithSending();
}*/

function getNTWChatRequestData(pToken_from, pToken_to, callFromEvent = 'init') {
    const channelId = $('#users-chat').data('channel_id');

    return {
        pToken_from,
        pToken_to,
        channel_id: channelId,
        page: ntwChatPageNo,
        itemPerPage: ntwChatItemsPerPage,
        callFromEvent
    };
}

function fetchNTWChatData(requestData) {
    if (!requestData.channel_id || !requestData.pToken_from || !requestData.pToken_to) return;

    const chat_messages_key = `cm_${ntw_room_key}_${requestData.pToken_from}_${requestData.pToken_to}`;

    IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key)
        .then((intao_data) => {
            if (!intao_data) {
                ntw_empty_dm_msg = 1;
                $('#ntwChatConversationLoader').awloader('hide');
                usersConversationList.awloader('hide');

                $('.pin-message-v2-dm').addClass('d-none');

                return;
            }

            $('.pin-message-v2-dm').removeClass('d-none');

            ntw_empty_dm_msg = 0;
            if (ntw_isProcessing) {
                setTimeout(() => fetchNTWChatData(requestData), 2000);
                return;
            }

            ntw_isProcessing = true;

            const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastNTWMsgCheckedTimestamp', lastNTWMsgCheckedTimestamp, 1);

            processNTWChatData(
                requestData,
                intao_data.timestamp ? intao_data.values : {
                    "chat": {},
                    "last_update_time": lastCheckedTimestamp,
                    "success": true
                }
            );
        })
        .catch((error) => {
            console.error("Error fetching NTWChatData:", error);
        });
}

function processNTWChatData(requestData, response) {
    let processedResponse = {};
    let recentItemsObject = {};
    let recentRenderedItemsObject = {};
    let totalChats = 0;
    let firstKey = null;

    if (response.success) {
        let chats = response.chat ? response.chat : [];

        let allChatKeys = Object.keys(chats);
        totalChats = allChatKeys.length;
        if (totalChats > 0) {
            let direction;
            if (requestData.callFromEvent === "init" || ntw_msgDownIndex === 0) {
                direction = 'before';
                let recentItems = paginateChat(response.chat, null, direction, requestData.itemPerPage);
                recentItemsObject = recentItems.data;
                firstKey = recentItems.firstCursor;
            } else if (requestData.callFromEvent === "scrollup") {
                direction = 'before';
                let recentItems = paginateChat(response.chat, ntw_msgUpIndex, direction, requestData.itemPerPage);
                recentItemsObject = recentItems.data;
                firstKey = recentItems.firstCursor;
            } else if (requestData.callFromEvent === "interval") {
                direction = 'after';
                let recentItems = paginateChat(response.chat, ntw_msgDownIndex, direction, requestData.itemPerPage);
                recentItemsObject = recentItems.data;
                firstKey = recentItems.firstCursor;

                const recentRenderedItems = Object.entries(chats).slice(-20);
                recentRenderedItemsObject = Object.fromEntries(recentRenderedItems);
            }
        }
    }

    processedResponse.isTempMsg = false;
    processedResponse.channel_id = requestData.channel_id;
    processedResponse.callFromEvent = requestData.callFromEvent;
    processedResponse.chat = recentItemsObject;
    processedResponse.firstKey = firstKey;
    processedResponse.overallChatCount = totalChats;
    processedResponse.recent_rendered_items = recentRenderedItemsObject;
    processedResponse.last_update_time = response.last_update_time;

    renderNTWMessages(processedResponse);
}

async function compiledNTWMsgHtml(cd) {
    let compiledMMMsgHtml;
    let safeMessageHtml = '';
    //const messageHtml = decodeURIComponent(cd.message.replace(/\+/g, ' '));
    const messageHtml = decodeURIComponent(cd.message.replace(/%(?![0-9A-Fa-f]{2})/g, '%25').replace(/\+/g, ' '));

    const message_content = linkifyWithJQuery(messageHtml);
    if (message_content.includes('chat-meeting-link') || cd.userType === 'system') {
        safeMessageHtml = message_content;
    } else {
        /*const safeMessage = document.createElement('pre');
        safeMessage.textContent = message_content;
        safeMessageHtml = safeMessage.innerHTML.replace(/\n/g, '<br>')
            .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');*/
        safeMessageHtml = message_content;
    }

    if (cd.userType === 'system') {
        compiledMMMsgHtml = `<li class="badge-system clearfix ${'msg_' + (cd.time)} ${cd.isTempMsg ? 'temp_msg new' : ''}" data-ntw_message_key="${cd.time}" id="${'msg_' + (cd.time)}"><span class="badge badge-secondary">${safeMessageHtml}</span></li>`;
    } else {

        if(cd.pin == 1) {

            var pinned_by;

            var userInfo = await getUserInfo(cd?.ptokenTo, 'public');
            console.log("TEST -->>-->>-->",userInfo);

            if (userInfo.avatar_image != '' && userInfo.avatar_image != undefined) {
                var avatar_image = userInfo.avatar_image;
            } else if (userInfo.avatar != undefined && userInfo.avatar != 'default') {
                var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/' + userInfo.avatar + '.png';
            } else {
                var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/avatar_def.png';
            }

            let msg = decodeURIComponent(safeMessageHtml.replace(/\+/g, ' '));
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

            let $parent = $('.pin-message-v2-dm');
            if ($parent.find('.pin_message_div-dm'+cd.channel_id).length === 0) {
                $parent.append(`<div class="pb-2 d-flex align-items-center comm_pin_message_div_dm pin_message_div-dm${cd.channel_id}" style="gap: 12px;">
                    <div class="nav-vertical-dots flex-shrink-0 pin_message_dot_div"> </div>
                    <div class="flex-grow-1 pin_message_msg_div"> </div>
                </div>`);
            }

            var pin_msg_count = $(`.pin_message_div-dm${channelId} .pin_message_msg_div pin_msg`).length;
            var activeClass = "";
            if(pin_msg_count > 0) {
                activeClass = "active";
            }

            let chat_with = $('#users-chat').data('chatwith');

            if ($(`.pin_message_div-dm${cd.channel_id} .pin_message_msg_div [data-frm_message_id="${cd.message_id}"]`).length === 0) {

                $(`.pin_message_div-dm${cd.channel_id} .pin_message_dot_div`).append(`<div class="message-item-dot ${activeClass}" data-channel_id="${cd.channel_id}" data-frm_message_id="${cd.message_id}"></div>`);

                $(`.pin_message_div-dm${cd.channel_id} .pin_message_msg_div`).append(`<div class="pin_msg flex-grow-1 ${(activeClass == "active") ? 'd-flex' : 'd-none'}" data-channel_id="${cd.channel_id}" data-frm_message_id="${cd.message_id}">
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
                            <a class="dropdown-item d-flex align-items-center justify-content-between goto-message" data-frm_message_key="${cd.time}" data-frm_message_id="${cd.message_id}">Go to Message</a>
                            <a class="dropdown-item d-flex align-items-center justify-content-between unpin-message" data-type="dm" data-chatwith="${chat_with}" data-frm_message_id="${cd.message_id}" data-frm_message_key="${cd.time}" >Unpin</a>
                            <a data-chatwith="${cd.pinned_by}" data-profile_token="${cd.pinned_by}" class="dropdown-item ${cd.pinned_by === my_pToken ? 'd-none' : 'd-flex'} align-items-center justify-content-between openProfileModal">View Profile</a>
                        </div>
                    </div>
                </div>`);
            }
        }

        let chat_with = $('#users-chat').data('chatwith');

        compiledMMMsgHtml = `<li class="chat-list ${cd.ptokenTo === cd.ptokenFrom ? 'right' : 'left'} ${'msg_' + (cd.time)} ${cd.isTempMsg ? 'temp_msg new' : ''}" data-ntw_message_id="${cd.message_id}" data-ntw_message_key="${cd.time}" id="${'msg_' + (cd.time)}">
                    <div class="conversation-list">
                        <div class="chat-avatar">
                            <img src="${cd.avatar}" alt="profile">
                        </div>

                        <div class="reaction-popup" data-type="dm" data-chatfrom="${my_pToken}" data-chatwith="${chat_with}">
                            <div class="quick-reactions">
                                <span class="quick-reaction" data-emoji="👍">👍</span>
                                <span class="quick-reaction" data-emoji="❤️">❤️</span>
                                <span class="quick-reaction" data-emoji="😂">😂</span>
                                <span class="quick-reaction" data-emoji="😮">😮</span>
                                <span class="quick-reaction" data-emoji="😢">😢</span>
                                <span class="quick-reaction" data-emoji="😡">😡</span>
                                <span class="quick-reaction" data-emoji="➕">➕</span>
                            </div>
                        </div>

                        <div class="user-chat-content">
                            <div class="ctext-wrap">
                                <div class="ctext-wrap-content">
                                    <p class="mb-0 ctext-content">${safeMessageHtml}</p>
                                </div>
                                <div class="align-self-start message-box-drop d-flex">
                                    <div class="dropdown">
                                        <a class="" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ri-more-2-fill fs-20" data-bs-toggle="tooltip" data-bs-placement="top" title="More actions"></i></a>
                                        <div class="dropdown-menu">
                                            <a data-frm_message_id="${cd.message_id}" data-msg-owner="${cd.ptokenTo}" class="dropdown-item d-flex align-items-center justify-content-between pin-message-dm" href="#" id="pin-message-0" data-action=${(cd.pin == 1) ? '0' : '1'} >${(cd.pin == 1) ? 'Unpin <i class="bx bx-unlink text-muted ms-2"></i>' : 'Pin <i class="bx bx-pin text-muted ms-2"></i>'} </a>
                                            ${(cd.ptokenTo === cd.ptokenFrom || _can_delete_all_msg == 1) ? `<a data-frm_message_id="${cd.message_id}" data-frm_message_key="${cd.time}" data-msg-owner="${cd.ptokenTo}" class="dropdown-item d-flex align-items-center justify-content-between frm-delete-item" href="#">Delete <i class="bx bx-trash text-muted ms-2"></i></a>` : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="conversation-name">
                                <small class="text-muted time">${cd.formatted_time}</small>

                                <span class="emoji_btn" data-frm_message_id="${cd.message_id}" data-frm_message_key="${cd.time}">
                                    <i class="far fa-smile text-muted emoji_placeholder ${(cd.reactions === undefined) ? '' : 'd-none'}"></i>
                                    <div class="message-reactions">${formatReactions(cd.reactions)}</div>
                                </span>

                                <span class="text-success check-message-icon"><i class="bx ${parseInt(cd.message_id, 10) ? '' : 'bx-sync bx-spin'}"></i></span> <!--bx-check-double-->
                            </div>
                        </div>
                    </div>
                </li>`;
    }

    return compiledMMMsgHtml;
}

async function getNTWChatMsgHtml(response, tempMsgList) {
    const chats = response.chat || {};
    const isTempMsg = response.isTempMsg || false;
    const lastChatItemKey = Object.keys(response.chat || {}).pop();
    const compiledChatKeys = { chats: [], temp_chats: [] };
    let messageHtml = '';
    let isNewBadgeAdded = false;

    const ptokenTo = chatwith;

    const orderedChats = Object.entries(chats).map(([key, v]) => ({
        key,
        ...v,
        sent_time: v.sent_time ?? Math.floor(v.time / 1000),
        isTempMsg
    }));

    const mergedChats = [...orderedChats];

    // Merge - insert temp messages into correct position
    if(response.callFromEvent !== 'scrollup') {
        const tempChats = Object.entries(tempMsgList?.[ptokenTo] || {}).map(([key, v]) => ({
            key,
            ...v,
            sent_time: v.sent_time ?? Math.floor(v.time / 1000),
            isTempMsg: true
        }));

        for (const tempMsg of tempChats) {
            let inserted = false;
            for (let i = mergedChats.length - 1; i >= 0; i--) {
                if (tempMsg.sent_time >= mergedChats[i].sent_time) {
                    mergedChats.splice(i + 1, 0, tempMsg);
                    inserted = true;
                    break;
                }
            }
            if (!inserted) mergedChats.unshift(tempMsg);
        }
    }

    // Merge - insert asm queue messages into correct position
    if (ntw_asm_enable && !isTempMsg && response.callFromEvent !== 'scrollup') {
        const asmChats = Object.entries(ntw_asm_queue || {}).map(([key, v]) => ({
            key,
            ...v,
            sent_time: v.sent_time ?? Math.floor(v.time / 1000),
            isTempMsg: true
        }));

        for (const asmMsg of asmChats) {
            let inserted = false;
            for (let i = mergedChats.length - 1; i >= 0; i--) {
                if (asmMsg.sent_time >= mergedChats[i].sent_time) {
                    asmMsg.key = (mergedChats[i].isTempMsg ? asmMsg.sent_time : asmMsg.time).toString();
                    asmMsg.isTempMsg = mergedChats[i].isTempMsg;
                    mergedChats.splice(i + 1, 0, asmMsg);
                    if(!asmMsg.isTempMsg) delete ntw_asm_queue[asmMsg.sent_time];
                    inserted = true;
                    break;
                }
            }
        }
    }


    // Process merged chats
    for (const v of mergedChats) {
        const userInfo = await getUserInfo(v.ptoken, 'public');
        const fallbackSrc = `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;
        const avatar = await buildAvatarImage(userInfo.avatar_image, fallbackSrc);
        const msgTime = Math.floor(v.time / 1000);
        const badgeArr = formatBadgeDateTime(msgTime, _taoh_user_timezone);

        if (v.ptoken != my_pToken && response.callFromEvent !== 'init' && response.callFromEvent !== 'scrollup') {
            ntw_newMessagesCnt++;
        }

        if (badgeArr?.[0]) {
            const badgeDate = badgeArr[0];

            if (response.callFromEvent === 'scrollup' && v.key === lastChatItemKey) {
                removeSameBadge('#chat-conversation-direct', badgeDate);
            }

            if (response.callFromEvent === 'scrollup' && badgeDate !== ntw_chatBadgeUpDate) {
                ntw_chatBadgeUpDate = badgeDate;
                messageHtml += `<li class="date-badge mb-3 clearfix" data-timestamp="${v.time}">
                            <span class="badge text-muted">${badgeDate}</span>
                        </li>`;
            } else if (response.callFromEvent !== 'scrollup' && badgeDate !== ntw_chatBadgeDownDate) {
                ntw_chatBadgeDownDate = badgeDate;
                messageHtml += `<li class="date-badge mb-3 clearfix" data-timestamp="${v.time}">
                            <span class="badge text-muted">${badgeDate}</span>
                        </li>`;
            }
        }

        if (!isNewBadgeAdded && response.callFromEvent === 'init' && response.channel_id) {
            const target = $(`.usersList li#dm-${response.channel_id} .unread-count`);
            const unreadCount = parseInt(target.attr('data-count'), 10) || 0;
            const lastViewTime = parseInt(target.attr('data-lastview'), 10) || 0;

            if (unreadCount > 0 && lastViewTime > 0 && msgTime > lastViewTime) {
                messageHtml += `<li class="new-badge mb-3 clearfix" data-timestamp="${v.time}">
                                    <span class="badge">New</span>
                                </li>`;
                isNewBadgeAdded = true;
            }
        }

        const compileData = {
            ptokenTo: v.ptoken,
            message: v.message,
            message_id: v.message_id,
            time: v.time,
            name: userInfo.chat_name,
            avatar,
            ptokenFrom: my_pToken,
            userType: v.user_type,
            isTempMsg: v.isTempMsg,
            stored_time: v.stored_time ?? '',
            formatted_time: badgeArr?.[1] ?? '',
            pin: v.pin,
            pinned_by: v.pinned_by,
            channel_id: v.channel_id,
            ptoken_to: v.to_ptoken,
            reactions: v.reactions,
        };

        messageHtml += await compiledNTWMsgHtml(compileData);

        if (v.isTempMsg) {
            compiledChatKeys.temp_chats.push(v.key);
        } else {
            compiledChatKeys.chats.push(v.key);
        }

        $(`#msg_${v.sent_time * 1000}`).removeClass('new');
    }

    console.log("get NTW Chat");


    return { messageHtml, compiledChatKeys };
}

function renderNTWMessages(response) {
    if (!response) {
        doAfterNTWMsgRender(response);
        return;
    }

    let chats = response.chat || {};
    let allChatKeys = Object.keys(chats);

    let chatTempMessagesKey = `cm_temp_${ntw_room_key}_${my_pToken}`;
    IntaoDB.getItem(objStores.ntw_store.name, chatTempMessagesKey)
        .then(intao_data => intao_data?.values || {})
        .then(tempMsgList => {
            return getNTWChatMsgHtml(response, tempMsgList);
        })
        .then(({ messageHtml, compiledChatKeys }) => {
            if (response.callFromEvent === 'init') {
                comments.empty();
            }

            $("#users-conversation-list .temp_msg:not(.new)").remove();

            if (compiledChatKeys.chats.length > 0 || compiledChatKeys.temp_chats.length > 0) {
                $('#no_message').remove();
                // $('#message_helper').hide();

                if (response.callFromEvent === 'scrollup') {
                    prependAndRestoreScrollPositionWithSimpleBar('#chat-conversation-direct', () => {
                        comments.prepend(messageHtml);
                    });
                } else {
                    comments.append(messageHtml);
                    if (compiledChatKeys.chats.length > 0 || compiledChatKeys.temp_chats.length > 0) {
                        if (isScrolledUpSimplebar('#chat-conversation-direct', 600) && response.callFromEvent !== 'init') {
                            // if (ntw_newMessagesCnt > 0) {
                            //     newmessages_btn.find('span').text(ntw_newMessagesCnt + ' new messages');
                            //     newmessages_btn_grp.show();
                            // }
                        } else {
                            simpleBarScrollToBottom('#chat-conversation-direct');
                            ntw_newMessagesCnt = 0;
                        }
                    }
                }
            }

            if (compiledChatKeys.chats.length > 0 && !response.isTempMsg) {
                if (response.callFromEvent === 'init') {
                    ntw_msgUpIndex = allChatKeys[0];
                    ntw_msgDownIndex = allChatKeys.pop();
                } else if (response.callFromEvent === 'scrollup') {
                    ntw_msgUpIndex = allChatKeys[0];
                } else if (response.callFromEvent === 'interval') {
                    ntw_msgDownIndex = allChatKeys.pop();
                }
            }

            response.compiledChatKeys = compiledChatKeys;

            // Clearing left over temp messages if already rendered
            if ($('#users-conversation-list .temp_msg').length) {
                let recentRenderedItems = response.recent_rendered_items;
                for (const [k, value] of Object.entries(recentRenderedItems)) {
                    let tempMsgElem = $('.msg_' + (value.sent_time * 1000));
                    if (tempMsgElem.length) tempMsgElem.remove();
                }
            }

            let overallChatFirstKey = response.firstKey;
            let compiledChatFirstKey = compiledChatKeys.chats.length ? compiledChatKeys.chats[0] : null;
            if (compiledChatFirstKey && response.callFromEvent === "scrollup" && overallChatFirstKey === compiledChatFirstKey) {
                ntw_msgScrollUpEnded = true;
            }
        })
        .then(() => {
            doAfterNTWMsgRender(response);
        });
}

function doAfterNTWMsgRender(response) {
    let overallChatCountWithTemp = response.overallChatCount;
    if (typeof response.compiledChatKeys != 'undefined') {
        overallChatCountWithTemp += response.compiledChatKeys.temp_chats.length;
    }
    // updateStickyBadgeTxt('#users-conversation-list', '#stickyBadge');

    if (overallChatCountWithTemp > 0) {
        // chatCount.text(overallChatCountWithTemp);
        // $('#message_helper').hide();
        // $('#message_count').show();
    } else {
        ntw_msgUpIndex = 0;
        ntw_msgDownIndex = 0;
        // $('#message_count').hide();
        // if (chatwith_liveStatus == 1) $('#message_helper').show();
        comments.html("<p id='no_message'>No Messages yet!</p>");
    }

    if (response.hasOwnProperty('last_update_time')) {
        ntwChatLastTime = response.last_update_time || 0;
    }

    // Clear Unread count
    const channelId = response.channel_id;
    const targetElement = $(`.usersList li#dm-${channelId} .unread-count`);
    if (targetElement?.length && targetElement.attr('data-count')) {
        clearUnreadCount(ntw_room_key, channelId, (response.callFromEvent === "init" ? 1 : 0));
    }

    // $('.pin_message_dm_div .pin_msg').removeClass('d-none').addClass('d-flex');
    // $('.pin_message_dm_div .pin_msg').not(`[data-channel_id="${channelId}"]`).removeClass('d-flex').addClass('d-none');

    const $dot = $(`.pin_message_div-dm${channelId} .pin_message_dot_div .message-item-dot`);
    if ($dot.length && !$dot.hasClass('active')) {
        $('[class*="pin_message_div-dm"]').removeClass('d-flex').addClass('d-none');
        $(`.pin_message_div-dm${channelId}`).removeClass('d-none').addClass('d-flex');

        $(`.pin_message_div-dm${channelId} .pin_message_msg_div .pin_msg`).removeClass('d-flex').addClass('d-none');
        $(`.pin_message_div-dm${channelId} .pin_message_msg_div .pin_msg`).first().removeClass('d-none').addClass('d-flex');

        $(`.pin_message_div-dm${channelId} .pin_message_dot_div .message-item-dot`).removeClass('active');
        $(`.pin_message_div-dm${channelId} .pin_message_dot_div .message-item-dot`).first().addClass('active');
    }

    if(channelId !== undefined) {
        const pinCount = $(`.pin_message_div-dm${channelId} .pin_msg`).length;
        console.log("pinCount DM ====== ", channelId+" "+pinCount);
        if(pinCount == 0) {
            $('.pin-message-v2-dm').hide();
        } else{
            $('.pin-message-v2-dm').show();
        }
    }


    ntw_isProcessing = false;
    $('#ntwChatConversationLoader').awloader('hide');
    usersConversationList.awloader('hide');
}

function initNTWChatDataInterval() {
    if (ntwChatDataInterval) clearInterval(ntwChatDataInterval);
    ntwChatDataInterval = setInterval(function () {
        if (!ntw_isProcessing && chatWindow === 'direct_message') {
            if (my_pToken.trim() !== '' && chatwith.trim() !== '') {
                let requestData = getNTWChatRequestData(my_pToken, chatwith, 'interval');
                fetchNTWChatData(requestData, false);
            }
        }
    }, 3000);
}

async function loadDirectMessage(chatwith, channelId = '', chaton='dm', openChatWindow = 1) {

    ntw_asm_queue = {};
    ntw_asm_current_index = 0;
    ntw_asm_indexes = [...ntw_asm_actual_indexes];

    selectedChannel = '';
    selectedUser = chatwith;
    selectedChat = chaton;

    if (ntw_room_key && my_pToken && chatwith) {
        if (ntwChatDataInterval) clearInterval(ntwChatDataInterval);
        usersConversationList.empty();
        ntw_msgUpIndex = 0;
        ntw_msgDownIndex = 0;

        userLiveStatusInterval = 3000;
        if (userLiveIntervalId) clearInterval(userLiveIntervalId);

        chatWindow = 'direct_message';
        chat_activeTabId = chatWindow; // for footer fn

        ntwChatLastTime = 0;
        ntw_msgScrollUpEnded = false;
        ntwChatPageNo = 1;

        if (!channelId  || channelId.trim() === '') {
            const input = [ntw_room_key, my_pToken, chatwith].sort().join('_');
            channelId = await generateSecureSlug(input, 16);

            await createOnetoOneChannel(chatwith, channelId, openChatWindow);
        }

        console.log("openChatWindow", openChatWindow);

        if(openChatWindow == 1) {
            $('#users-chat').setSyncedData({ channel_id: channelId, chatwith: chatwith });
            const userChatTopbarInfo = $('#user-chat-topbar-info');

            const [userLiveStatus, userInfo] = await Promise.all([
            getUserLiveStatus(chatwith).catch((e) => {console.log(e)}),
            getUserInfo(chatwith, 'full').catch((e) => {console.log(e)}),
            ]);
            chatwith_liveStatus = Boolean(userLiveStatus.output) ? 1 : 0;
            chatname = userInfo.chat_name;

            var userAvatarSrc = userInfo?.avatar_image && await checkImageExists(userInfo.avatar_image).catch(() => false)
                ? userInfo.avatar_image
                : `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;


            if (chatwith_liveStatus) {
                userChatTopbarInfo.find('.chat-user-img').addClass('online').removeClass('away');
                userChatTopbarInfo.find('.user-status-text').text('Online');
            } else {
                userChatTopbarInfo.find('.chat-user-img').addClass('away').removeClass('online');
                userChatTopbarInfo.find('.user-status-text').text('Away');
            }

            userLiveStatusUpdate(userLiveStatusInterval);

            userChatTopbarInfo.find('.cw_direct_message_title').text(chatname);
            userChatTopbarInfo.find('.chat-user-img img').attr('src', userAvatarSrc);

            initializeRequest(chatWindow, ntw_room_key, my_pToken);

            let requestData = getNTWChatRequestData(my_pToken, chatwith, 'init');
            fetchNTWChatData(requestData);

            initNTWChatDataInterval();

            console.log('----chat_window-----------1')
            loadchatWindow(chatWindow);
            usersConversationList.awloader('show');
            clearUnreadCount(ntw_room_key, channelId, 0);

            var track_data = {
                'action': 'click_1_1',
                'channel_id': channelId,
                'channel_name': chatwith,
                'ptoken': my_pToken
            };
            taoh_track_activities(track_data);

            //console.log("user info comm 123", userInfo);

            /*if(chatwith != 'organizer' && chatwith != 'sidekick') {
            await updateProfileInfo(userInfo, userLiveStatus);
            loadRightSidebar('profile');
            }
            else{
                $('.user-profile-status').hide();
            }*/
            // alert(1);
            await updateProfileInfo(userInfo, userLiveStatus);
            loadRightSidebar('profile');
        }

    }
}

document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        taoh_ntw_post_metrics('view');
    }, 5000);
});
