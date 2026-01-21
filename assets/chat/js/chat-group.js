const frm_chatContainer = document.getElementById('chat-conversation-channel');
// const stickyBadge = document.getElementById('stickyBadge');

const frm_commentInput= $('#chat_input'); // :rk change to common name
let frm_comments_list = channelConversationList;
let frm_ReplycommentInput = $('#chat_reply_input');
let frm_reply_comments_list = channelReplyConversationList;

let frmChatLastTime = 0;
let frm_newMessagesCnt = 0;
let frm_entries_cleared = 1;
let frm_isProcessing = false;
let frm_msgScrollUpEnded = false;
let frm_msgUpIndex = 0;
let frm_msgDownIndex = 0;
let frmChatPageNo = 1;
const frmChatItemsPerPage = 10;
let frm_chatBadgeUpDate = '';
let frm_chatBadgeDownDate = '';
let frmChatDataInterval;
let frmChatDataFromServerInterval;
var my_liked_messages = [];

let frm_my_recent_message_timestamp = 0;
let frm_commentDelayTimeInSeconds = 7200; // 2 hours
let frm_checkCommentDelayInterval = null;

const frm_reply_chatContainer = document.getElementById('chat-reply-conversation');

let frmReplyChatLastTime = 0;
let frm_reply_newMessagesCnt = 0;
let frm_reply_entries_cleared = 1;
let frm_reply_isProcessing = false;
let frm_reply_msgScrollUpEnded = false;
let frm_reply_msgUpIndex = 0;
let frm_reply_msgDownIndex = 0;
let frmReplyChatPageNo = 1;
const frmReplyChatItemsPerPage = 10;
let frm_reply_chatBadgeUpDate = '';
let frm_reply_chatBadgeDownDate = '';
let frmReplyChatDataInterval;
let frmReplyChatDataFromServerInterval;

$(document).on('click', '.frm-delete-item', function () {
    const parentElem = $(this);
    let frmMessageId = parentElem.data('frm_message_id');
    let frmMessageKey = parentElem.data('frm_message_key');
    let channelId = $('#users-chat').data('channel_id');

    $('#delete_confirmation_msg').text("Are you sure to delete the message?");

    $('#deleteModal').data('parent-elem', parentElem);
    $('#delete_message_id').val(frmMessageId);
    $('#delete_message_key').val(frmMessageKey);
    $('#delete_channel_id').val(channelId);

    $('#deleteModal').modal('show');
});

function sendFRMChat(message, my_pToken, parent_id, ntw_room_key, channel_id, user_type = 'user', is_default = 0) {
    if (ntw_room_key?.trim() === '' || channel_id?.trim() === '') {
        jq_confirm_alert('Invalid Data', 'Something went wrong. Please try again.', 'orange');
        return false;
    }

    if (message?.trim() === '') {
        jq_confirm_alert('Warning', 'Message seems empty! Please enter valid message to send.', 'orange');
        return false;
    }

    let isReplyRequest = Boolean(parseInt(parent_id, 10));

    if (user_type !== 'system') {
        if (isReplyRequest) {
            frm_ReplycommentInput.val('');
        } else {
            frm_commentInput.val('');
        }
    }

    let sent_time = new Date().getTime();

    const converted_message = convertMentionsToLinks(message);

    var event_token_send = '';
    if(_can_delete_all_msg == 1 && selectedChat == 'organizer')
        user_type  = 'organizer';
    if(selectedChat == 'organizer')
        event_token_send = eventtoken;
    
    let data = {
        'taoh_action': 'taoh_channel_send_message',
        'message': converted_message,
        'ptoken': my_pToken,
        'other_ptoken': '',
        'parent_id': parent_id,
        'user_type': user_type,
        'key': ntw_room_key,
        'channel_id': channel_id,
        'event_token' : event_token_send,
        'sent_time': sent_time,
        "country": my_country_name,
        "my_link": my_link,
    };


    let chatresponse = {
        "chat": [{
            "ptoken": data.ptoken,
            "message": data.message,
            "to_ptoken": data.other_ptoken,
            'parent_id': data.parent_id,
            'reply_count': 0,
            'new_reply_count': 0,
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
        'channel_id': data.channel_id,
        "overallChatCount": 1,
        "firstKey": null,
        "recent_rendered_items": {}
    };

    isReplyRequest ? renderFRMReplyMessages(chatresponse) : renderFRMMessages(chatresponse);

    let chat_temp_messages_key = `frm_temp_${ntw_room_key}_${data.channel_id}${Boolean(parseInt(data.parent_id, 10)) ? '_' + data.parent_id : ''}`;
    // let ptokenTo = data.other_ptoken;    

    IntaoDB.getItem(objStores.ntw_store.name, dojo_data_key).then((intao_data) => {         
        let updatedResponse = {};
        if (intao_data?.values) {
            updatedResponse = intao_data.values;
        }        
        updatedResponse.frm_last_msg_sent_time = data.sent_time;
        return IntaoDB.setItem(objStores.ntw_store.name, {
            taoh_ntw: dojo_data_key,
            values: updatedResponse,
            timestamp: Date.now()
        });        
    });


    // Store temp messages in Indexed DB then send to server
    IntaoDB.getItem(objStores.ntw_store.name, chat_temp_messages_key).then((intao_data) => {
        let updatedResponse = {};
        if (intao_data?.values) {
            updatedResponse = intao_data.values;
        }
        // if (!(ptokenTo in updatedResponse)) updatedResponse[ptokenTo] = {};
        Object.assign(updatedResponse, {[data.sent_time]: chatresponse.chat[0]});
        IntaoDB.setItem(objStores.ntw_store.name, {
            taoh_ntw: chat_temp_messages_key,
            values: updatedResponse,
            timestamp: Date.now()
        });
    }).then(() => {
        if (frm_asm_enable && !isReplyRequest) {
            frm_asm_current_index++;
            if (frm_asm_indexes.includes(frm_asm_current_index)) {
                // Remove current index if more than one entry exists
                if (frm_asm_indexes.length > 1) {
                    frm_asm_indexes = frm_asm_indexes.filter(item => item !== frm_asm_current_index);
                }

                const frm_asm_current_index_msg = frm_asm_index_messages?.[frm_asm_current_index];
                if (frm_asm_current_index_msg?.trim()) {
                    taoh_set_info_message(frm_asm_current_index_msg, false);
                    // sendFRMAsm(frm_asm_current_index_msg, my_pToken, parent_id, ntw_room_key, channel_id);
                }

                frm_asm_current_index = 0;
            }
        }

        if (navigator.onLine) {
            const frm_comment_send_btn = isReplyRequest ? $('#chat-reply-send-btn') : $('#chat-send-btn');
            frm_comment_send_btn.prop('disabled', true);
            frm_comment_send_btn.find('i').removeClass('bxs-send').addClass('bx-loader-alt bx-spin');

            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'post',
                data: data,
                dataType: 'json',
                success: function (response) {

                    mentionUserArray = {};

                    if(!isReplyRequest && !response.success && response.output === 'topic_already_exist') {
                        // Delete temp message from Indexed DB
                        IntaoDB.getItem(objStores.ntw_store.name, chat_temp_messages_key)
                            .then((intao_data) => {
                                if (!intao_data?.values || !intao_data.values[data.sent_time]) {
                                    return; // Exit early if no relevant data
                                }

                                // Clone existing values and remove the target entry
                                const updatedResponse = { ...intao_data.values };
                                delete updatedResponse[data.sent_time];

                                // Update IndexedDB with the modified data
                                IntaoDB.setItem(objStores.ntw_store.name, {
                                    taoh_ntw: chat_temp_messages_key,
                                    values: updatedResponse,
                                    timestamp: Date.now()
                                });
                            });

                        if (!is_default) alert('It seems to be the same topic already exists. Please try with a different topic.');
                    }

                    if (response?.success) {
                        ft_frm_reFetchRequired = true;
                        frm_update_stored_time_in_temp_messages(chat_temp_messages_key, response, isReplyRequest);
                    }

                    frm_comment_send_btn.prop('disabled', false).find('i').removeClass('bx-loader-alt bx-spin').addClass('bxs-send');
                },
                error: function () {
                    console.log('Send failed. Storing offline.');
                    frm_comment_send_btn.prop('disabled', false).find('i').removeClass('bx-loader-alt bx-spin').addClass('bxs-send');
                    // checkOfflineMessage = 1;
                    // if (typeof syncOfflineMessages === 'function') syncOfflineMessages();
                }
            });
        } else {
            frm_comment_send_btn.find('i').removeClass('bx-loader-alt bx-spin').addClass('bxs-send');
            frm_comment_send_btn.prop('disabled', false);
            // checkOfflineMessage = 1;
            // if (typeof syncOfflineMessages === 'function') {
            //     syncOfflineMessages();
            // }
        }
    });
}

function frm_update_stored_time_in_temp_messages(chat_temp_messages_key, returnedData, isReplyRequest) {
    // Update stored_time in temp messages
    IntaoDB.getItem(objStores.ntw_store.name, chat_temp_messages_key).then((intaoData) => {
        if (!intaoData?.values) return;

        const updatedResponse = intaoData.values;

        if (!updatedResponse[returnedData.sent_time]) return;

        updatedResponse[returnedData.sent_time].message_id = returnedData.message_id;
        updatedResponse[returnedData.sent_time].stored_time = returnedData.stored_time;

        IntaoDB.setItem(objStores.ntw_store.name, {
            taoh_ntw: chat_temp_messages_key,
            values: updatedResponse,
            timestamp: Date.now()
        });
    }).then(() => {
        const tempMsgElem = $('#msg_' + (returnedData.sent_time * 1000));
        if (tempMsgElem.length) {
            const message_id_key = isReplyRequest ? 'frm_reply_message_id' : 'frm_message_id';
            tempMsgElem.setSyncedData(message_id_key, returnedData.message_id);
            tempMsgElem.find('.check-message-icon i').removeClass('bx-sync bx-spin'); // .addClass('bx-check-double')
        }
    });
}

/*function sendFRMAsm(message, my_pToken, parent_id, ntw_room_key, channel_id, user_type = 'system') {
    if (ntw_room_key?.trim() === '' || channel_id?.trim() === '') {
        return false;
    }

    if (message?.trim() === '') {
        return false;
    }

    let isReplyRequest = Boolean(parseInt(parent_id, 10));

    let sent_time = new Date().getTime();

    let data = {
        'message': message,
        'ptoken': my_pToken,
        'other_ptoken': '',
        'parent_id': parent_id,
        'user_type': user_type,
        'key': ntw_room_key,
        'channel_id': channel_id,
        'sent_time': sent_time
    };

    let chatresponse = {
        "chat": [{
            "ptoken": data.ptoken,
            "message": data.message,
            "to_ptoken": data.other_ptoken,
            'parent_id': data.parent_id,
            'reply_count': 0,
            'new_reply_count': 0,
            "user_type": data.user_type,
            "room_hash": data.key,
            'channel_id': data.channel_id,
            'message_id': '',
            "sent_time": data.sent_time,
            "time": (data.sent_time * 1000)
        }],
        "isTempMsg": true,
        'channel_id': data.channel_id,
        "overallChatCount": 1,
        "firstKey": null,
        "recent_rendered_items": {}
    };

    // isReplyRequest ? renderFRMReplyMessages(chatresponse) : renderFRMMessages(chatresponse);

    frm_asm_queue[data.sent_time] = chatresponse.chat[0];
}*/

function getFRMChatRequestData(pToken_from, parent_id, callFromEvent = 'init') {
    const isReplyRequest = Boolean(parseInt(parent_id, 10));
    const channelId = $('#channel-chat').data('channel_id');

    return {
        pToken_from,
        pToken_to: '',
        parent_id,
        channel_id: channelId,
        page: isReplyRequest ? frmReplyChatPageNo : frmChatPageNo,
        itemPerPage: isReplyRequest ? frmReplyChatItemsPerPage : frmChatItemsPerPage,
        callFromEvent
    };
}

function fetchFRMChatData(requestData) {
    if (!requestData.channel_id || !requestData.pToken_from) return;

    const chat_messages_key = `frm_${ntw_room_key}_${requestData.channel_id}`;

    IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key)
        .then((intao_data) => {
            if (!intao_data) {
                $('.load_more_dots').removeClass('d-lg-inline-block');
                $('.load_more_dots').addClass('d-none');
                $('#frmChatConversationLoader').awloader('hide');
                channelConversationList.awloader('hide');
                
                $('.pin-message-v2').addClass('d-none');
                
                return;
            }

            $('.pin-message-v2').removeClass('d-none');

            $('.load_more_dots').addClass('d-lg-inline-block');
            $('.load_more_dots').removeClass('d-none');

            if (frm_isProcessing) {
                setTimeout(() => fetchFRMChatData(requestData), 2000);
                return;
            }

            frm_isProcessing = true;

            const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastFRMMsgCheckedTimestamp', lastFRMMsgCheckedTimestamp, 1);

            processFRMChatData(
                requestData,
                intao_data.timestamp ? intao_data.values : {
                    "chat": {},
                    "last_update_time": lastCheckedTimestamp,
                    "success": true
                }
            );
        })
        .catch((error) => {
            console.error("Error fetching FRMChatData:", error);
        });
}

function processFRMChatData(requestData, response) {
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
            if (requestData.callFromEvent === "init" || frm_msgDownIndex === 0) {
                direction = 'before';
                let recentItems = paginateChat(response.chat, null, direction, requestData.itemPerPage);
                recentItemsObject = recentItems.data;
                firstKey = recentItems.firstCursor;
            } else if (requestData.callFromEvent === "scrollup") {
                direction = 'before';
                let recentItems = paginateChat(response.chat, frm_msgUpIndex, direction, requestData.itemPerPage);
                recentItemsObject = recentItems.data;
                firstKey = recentItems.firstCursor;
            } else if (requestData.callFromEvent === "interval") {
                direction = 'after';
                let recentItems = paginateChat(response.chat, frm_msgDownIndex, direction, requestData.itemPerPage);
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

    renderFRMMessages(processedResponse);
}

function isWithinLast15Days(microseconds) {
    let result = false;
    const now = new Date();
    const timestampDate = new Date(microseconds / 1000);
    const diffMs = now.getTime() - timestampDate.getTime();
    const diffDays = diffMs / (1000 * 60 * 60 * 24);  
    if(diffDays <= 15 && diffDays >= 0) {
        result = true;
    }
    return result;
}


async function compiledFRMMsgHtml(cd) {
    let compiledMMMsgHtml;
    let safeMessageHtml = '';
    const messageHtml = decodeURIComponent(cd.message.replace(/\+/g, ' '));

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
        compiledMMMsgHtml = `<li class="badge-system clearfix ${'msg_' + (cd.time)} ${cd.isTempMsg ? 'temp_msg new' : ''}" data-frm_message_key="${cd.time}" id="${'msg_' + (cd.time)}"><span class="badge badge-secondary">${safeMessageHtml}</span></li>`;
    } else {
        const messageReplyCount = parseInt(cd?.reply_count ?? 0, 10) || 0;
        const messageLikeCount = parseInt(cd?.like_count ?? 0, 10) || 0;

        const my_like = cd?.mylike;
        
        const messageNewReplyCount = parseInt(cd?.new_reply_count ?? 0, 10) || 0;

        const replyText = `${messageReplyCount} ${messageReplyCount === 1 ? 'reply' : 'replies'}`;
        const likeText = `${messageLikeCount} ${messageLikeCount === 1 ? 'like' : 'likes'}`;

        if(cd && cd.pin == 1) {
            var pinned_by;

            var userInfo = await getUserInfo(cd?.ptokenTo, 'public');
            console.log("TEST -->>-->>-->",userInfo);

            var avatar_image = "";
            if(userInfo) {
                if (userInfo.avatar_image != '' && userInfo.avatar_image != undefined) {
                    avatar_image = userInfo.avatar_image;
                } else if (userInfo.avatar != undefined && userInfo.avatar != 'default') {
                    avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/' + userInfo.avatar + '.png';
                } else {
                    avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/avatar_def.png';
                }
            }      
            
            let decoded = decodeURIComponent(safeMessageHtml);
            decoded = decoded.replace(/\+/g, '');
            const match = decoded.match(/<a [^>]*>.*?<\/a>/i);
            const aTag = match ? match[0] : "";           
            if(aTag != "") {
                var msgHTML = "Join "+aTag+" - Video Room";
            } else {
                let msg = decodeURIComponent(safeMessageHtml.replace(/\+/g, ' '));
                if (msg.length > 100) {
                    var visibleText = msg.slice(0, 100);
                    var hiddenText = msg.slice(100);
                    var msgHTML = `${visibleText}<span class="d-none">${hiddenText}</span> <button type="button" class="btn btn-link p-0 shadow-none show_more_btn">Show More</button>`;
                } else {
                    var msgHTML = msg;
                }
            }

            let $parent = $('.pin-message-v2');
            if ($parent.find('.pin_message_div'+cd.channel_id).length === 0) {
                $parent.append(`<div class="pb-2 d-flex align-items-center comm_pin_message_div pin_message_div${cd.channel_id}" style="gap: 12px;">
                    <div class="nav-vertical-dots flex-shrink-0 pin_message_dot_div"> </div>
                    <div class="flex-grow-1 pin_message_msg_div"> </div>
                </div>`);
            }

            var pin_msg_count = $(`.pin_message_div${channelId} .pin_message_msg_div pin_msg`).length;
            var activeClass = "";
            if(pin_msg_count > 0) {
                activeClass = "active";
            }
            
            if ($(`.pin_message_div${cd.channel_id} .pin_message_msg_div [data-frm_message_id="${cd.message_id}"]`).length === 0) {
                
                $(`.pin_message_div${cd.channel_id} .pin_message_dot_div`).append(`<div class="message-item-dot ${activeClass}" data-channel_id="${cd.channel_id}" data-frm_message_id="${cd.message_id}"></div>`);

                $(`.pin_message_div${cd.channel_id} .pin_message_msg_div`).append(`<div class="pin_msg flex-grow-1 ${(activeClass == "active") ? 'd-flex' : 'd-none'}" data-channel_id="${cd.channel_id}" data-frm_message_id="${cd.message_id}">
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
                            <a class="dropdown-item d-flex align-items-center justify-content-between goto-message" data-frm_message_key="${cd.time}">Go to Message</a>
                            <a class="dropdown-item d-flex align-items-center justify-content-between unpin-message" data-type="channel" data-frm_message_id="${cd.message_id}" data-frm_message_key="${cd.time}" >Unpin</a>
                            <a data-chatwith="${cd.pinned_by}" data-profile_token="${cd.pinned_by}" class="dropdown-item ${cd.pinned_by === my_pToken ? 'd-none' : 'd-flex'} align-items-center justify-content-between openProfileModal">View Profile</a>
                        </div>
                    </div>
                </div>`);
            }
        }        
        
        try {
            if (decodeURIComponent(safeMessageHtml).includes(_job_search_string) && isWithinLast15Days(cd.time)) {                      
                jobUrlData[ntw_room_key] = true;
                console.log("Job url found", jobUrlData); 
            }
        } catch (e) {
            console.warn("Malformed URI, skipping decode:", e);
        }     

        compiledMMMsgHtml = `<li class="chat-list ${cd.ptokenTo === cd.ptokenFrom ? 'right' : 'left'} ${'msg_' + (cd.time)} ${cd.isTempMsg ? 'temp_msg new' : ''}" data-frm_message_id="${cd.message_id}" data-frm_message_key="${cd.time}" id="${'msg_' + (cd.time)}">
                    <div class="conversation-list">
                        <div class="chat-avatar openProfileModal" data-chatwith="${cd.ptokenTo}" data-profile_token="${cd.ptokenTo}">
                            <img src="${cd.avatar}" alt="profile">
                        </div>                        

                        <div class="reaction-popup">
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
                                    <h6 class="mb-1 ctext-name"><span class="openProfileModal" data-chatwith="${cd.ptokenTo}" data-profile_token="${cd.ptokenTo}">${cd.name} ${cd.userType == 'organizer' ? ` (${cd.userType})` : ''}</span></h6>
                                    <p class="mb-0 ctext-content">${safeMessageHtml}</p>
                                </div>
                                <div class="align-self-start message-box-drop d-flex">                                    
                                    <div class="dropdown">
                                        <a class="conversation-reply channel-reply" href="#" role="button"><i class="bx bx-share mt-1 fs-20" data-bs-toggle="tooltip" data-bs-placement="top" title="Reply to thread"></i></a>
                                    </div>
                                    <div class="dropdown">
                                        <a class="" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ri-more-2-fill fs-20" data-bs-toggle="tooltip" data-bs-placement="top" title="More actions"></i></a>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item d-flex align-items-center justify-content-between copy-message" href="#" id="copy-message-0">Copy <i class="bx bx-copy text-muted ms-2"></i></a>
                                            <a data-frm_message_id="${cd.message_id}" data-msg-owner="${cd.ptokenTo}" class="dropdown-item d-flex align-items-center justify-content-between pin-message" href="#" id="pin-message-0" data-action=${(cd.pin == 1) ? '0' : '1'} >${(cd.pin == 1) ? 'Unpin <i class="bx bx-unlink text-muted ms-2"></i>' : 'Pin <i class="bx bx-pin text-muted ms-2"></i>'} </a>
                                            ${(cd.ptokenTo === cd.ptokenFrom || _can_delete_all_msg == 1) ? '<a class="dropdown-item d-flex align-items-center justify-content-between frm-delete-item" href="#">Delete <i class="bx bx-trash text-muted ms-2"></i></a>' : ''}
                                        </div>
                                    </div>                                    
                                </div>
                            </div>
                            <div class="conversation-name">
                                <span class="text-success check-message-icon"><i class="bx ${parseInt(cd.message_id, 10) ? '' : 'bx-sync bx-spin'}"></i></span> <!--bx-check-double-->
                                <small class="text-muted time">${cd.formatted_time}</small>  
                                
                                <span class="emoji_btn" data-frm_message_id="${cd.message_id}" data-frm_message_key="${cd.time}">
                                    <i class="far fa-smile text-muted emoji_placeholder ${(cd.reactions === undefined) ? '' : 'd-none'}"></i>
                                    <div class="message-reactions">${formatReactions(cd.reactions)}</div>
                                </span> 
                                                                                                
                                <a class="like-button ${(my_like == 1) ? 'd-none' : ''} ${(_like_enable == 1) ? '' : 'd-none'}" style="cursor: pointer;" data-frm_message_id="${cd.message_id}" data-frm_message_key="${cd.time}">
                                    <i class="bx bx-heart mt-1 fs-20" data-bs-toggle="tooltip" data-bs-placement="top" title="Like"></i>                                        
                                </a>
                                <a class="liked-button ${(my_like == 1) ? '' : 'd-none'} ${(_like_enable == 1) ? '' : 'd-none'}" data-frm_message_id="${cd.message_id}" data-frm_message_key="${cd.time}">
                                    <i class="bx bxs-heart mt-1 fs-20 text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Liked"></i>
                                </a>
                                <small data-frm_message_id="${cd.message_id}" style="cursor: pointer;" class="conversation-like-count channel-like ${messageLikeCount > 0 ? '' : 'd-none'} ${(_like_enable == 1) ? '' : 'd-none'}" data-count="${messageLikeCount}" data-like_count="${messageLikeCount}"> ${likeText} </small>

                                <small class="conversation-reply-count channel-reply ${messageNewReplyCount > 0 ? 'has-new-replies' : ''}" data-count="${messageReplyCount}" data-new_reply_count="${messageNewReplyCount}"> ${replyText} </small>
                                <span class="conversation-loader-icon"><i class="bx bx-sync bx-spin bx-sm"></i></span>
                            </div>
                        </div>
                    </div>
                </li>`;
    }

    return compiledMMMsgHtml;
}

function formatReactions(reactions) {
    if (!reactions || !Array.isArray(reactions)) return '';
    
    const counts = {};
    reactions.forEach(r => {
        if (r !== undefined && r !== null && r !== '') {
            counts[r] = (counts[r] || 0) + 1;
        }
    });

    return Object.entries(counts)
        .map(([emoji, count]) => {
            if (emoji !== undefined && emoji !== null && emoji !== '') {
                return `<span class="reaction">${emoji}${count}</span>`;
            }
            return '';
        })
        .filter(Boolean) // Remove empty strings
        .join('   ');
}

async function getFRMChatMsgHtml(response, tempMsgList) {
    const chats = response.chat || {};
    const isTempMsg = response.isTempMsg || false;
    const lastChatItemKey = Object.keys(response.chat || {}).pop();
    const compiledChatKeys = { chats: [], temp_chats: [] };
    let messageHtml = '';
    let isNewBadgeAdded = false;

    const orderedChats = Object.entries(chats).map(([key, v]) => ({
        key,
        ...v,
        sent_time: v.sent_time ?? Math.floor(v.time / 1000),
        isTempMsg
    }));

    const mergedChats = [...orderedChats];

    // Merge - insert temp messages into correct position
    if(response.callFromEvent !== 'scrollup') {
        const tempChats = Object.entries(tempMsgList || {}).map(([key, v]) => ({
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
    if (frm_asm_enable && !isTempMsg && response.callFromEvent !== 'scrollup') {
        const asmChats = Object.entries(frm_asm_queue || {}).map(([key, v]) => ({
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
                    if(!asmMsg.isTempMsg) delete frm_asm_queue[asmMsg.sent_time];
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

        if (v.ptoken == my_pToken && (!v.isTempMsg || response.callFromEvent !== 'scrollup')) {
            frm_my_recent_message_timestamp = msgTime;
        }

        if (v.ptoken != my_pToken && response.callFromEvent !== 'init' && response.callFromEvent !== 'scrollup') {
            frm_newMessagesCnt++;
        }

        if (badgeArr?.[0]) {
            const badgeDate = badgeArr[0];

            if (response.callFromEvent === 'scrollup' && v.key === lastChatItemKey) {
                removeSameBadge('#chat-conversation-channel', badgeDate);
            }

            if (response.callFromEvent === 'scrollup' && badgeDate !== frm_chatBadgeUpDate) {
                frm_chatBadgeUpDate = badgeDate;
                messageHtml += `<li class="date-badge mb-3 clearfix" data-timestamp="${v.time}">
                            <span class="badge text-muted">${badgeDate}</span>
                        </li>`;
            } else if (response.callFromEvent !== 'scrollup' && badgeDate !== frm_chatBadgeDownDate) {
                frm_chatBadgeDownDate = badgeDate;
                messageHtml += `<li class="date-badge mb-3 clearfix" data-timestamp="${v.time}">
                            <span class="badge text-muted">${badgeDate}</span>
                        </li>`;
            }
        }

        if (!isNewBadgeAdded && response.callFromEvent === 'init' && response.channel_id) {
            const target = $(`.channelList li#channel-${response.channel_id} .unread-count`);
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
            is_parent: v.parent_id == 0,
            time: v.time,
            name: userInfo.chat_name,
            avatar,
            ptokenFrom: my_pToken,
            userType: v.user_type,
            isTempMsg: v.isTempMsg,
            stored_time: v.stored_time ?? '',
            formatted_time: badgeArr?.[1] ?? '',
            like_count: v.like_count,
            reactions: v.reactions,
            mylike: v.mylike,
            pin: v.pin,
            pinned_by: v.pinned_by,
            channel_id: v.channel_id
        };

        if (compileData.is_parent) {
            compileData.reply_count = v.reply_count;
            compileData.new_reply_count = v.new_reply_count ?? 0;
        }

        messageHtml += await compiledFRMMsgHtml(compileData);

        if (v.isTempMsg) {
            compiledChatKeys.temp_chats.push(v.key);
        } else {
            compiledChatKeys.chats.push(v.key);
        }

        $(`#msg_${v.sent_time * 1000}`).removeClass('new');
    }

    return { messageHtml, compiledChatKeys };
}

function renderFRMMessages(response) {
    if (!response) {
        doAfterFRMMsgRender(response);
        return;
    }

    // // Reverse the keys and assign back to response.chat
    // response.chat = Object.fromEntries(
    //     Object.entries(response.chat).reverse()
    // );

    let channelId = response.channel_id;
    let chats = response.chat || {};
    let allChatKeys = Object.keys(chats);

    let chatTempMessagesKey = `frm_temp_${ntw_room_key}_${channelId}`;
    IntaoDB.getItem(objStores.ntw_store.name, chatTempMessagesKey)
        .then(intao_data => intao_data?.values || {})
        .then(tempMsgList => {
            return getFRMChatMsgHtml(response, tempMsgList);
        })
        .then(({ messageHtml, compiledChatKeys }) => {
            if (response.callFromEvent === 'init') {
                frm_comments_list.empty();
            }

            $("#channel-conversation-list .temp_msg:not(.new)").remove();

            if (compiledChatKeys.chats.length > 0 || compiledChatKeys.temp_chats.length > 0) {
                $('#frm_no_message').remove();

                if (response.callFromEvent === 'scrollup') {
                    prependAndRestoreScrollPositionWithSimpleBar('#chat-conversation-channel', () => {
                        frm_comments_list.prepend(messageHtml);
                    });
                } else {
                    frm_comments_list.append(messageHtml);
                    if(compiledChatKeys.chats.length > 0 || compiledChatKeys.temp_chats.length > 0){
                        if (isScrolledUpSimplebar('#chat-conversation-channel', 600) && response.callFromEvent !== 'init') {
                            // if (frm_newMessagesCnt > 0) {
                            //     newmessages_btn.find('span').text(frm_newMessagesCnt + ' new messages');
                            //     newmessages_btn_grp.show();
                            // }
                        } else {
                            simpleBarScrollToBottom('#chat-conversation-channel');
                            frm_newMessagesCnt = 0;
                        }
                    }

                    checkCommentFormTime();
                }
            }

            if (compiledChatKeys.chats.length > 0 && !response.isTempMsg) {
                // Scroll Up Event Code
                if (response.callFromEvent === 'init') {
                    frm_msgUpIndex = allChatKeys[0];
                    frm_msgDownIndex = allChatKeys.pop();
                } else if (response.callFromEvent === 'scrollup') {
                    frm_msgUpIndex = allChatKeys[0];
                } else if (response.callFromEvent === 'interval') {
                    frm_msgDownIndex = allChatKeys.pop();
                }

                /*// Scroll Down Event Code
                if (response.callFromEvent === 'init') {
                    frm_msgUpIndex = allChatKeys[0];
                    frm_msgDownIndex = allChatKeys.pop();
                } else if (response.callFromEvent === 'scrolldown') {
                    frm_msgDownIndex = allChatKeys.pop();
                } else if (response.callFromEvent === 'interval') {
                    frm_msgUpIndex = allChatKeys[0];
                }*/
            }

            response.compiledChatKeys = compiledChatKeys;

            // Clearing left over temp messages if already rendered
            if ($('#channel-conversation-list .temp_msg').length) {
                let recentRenderedItems = response.recent_rendered_items;
                for (const [k, value] of Object.entries(recentRenderedItems)) {
                    let tempMsgElem = $('.msg_' + (value.sent_time * 1000));
                    if (tempMsgElem.length) tempMsgElem.remove();
                }
            }

            // Scroll Up Event Code
            let overallChatFirstKey = response.firstKey;
            let compiledChatFirstKey = compiledChatKeys.chats.length ? compiledChatKeys.chats[0] : null;
            if (compiledChatFirstKey && response.callFromEvent === "scrollup" && overallChatFirstKey === compiledChatFirstKey) {
                frm_msgScrollUpEnded = true;
            }
        })
        .then(() => {
            doAfterFRMMsgRender(response);
        });
}

function doAfterFRMMsgRender(response) {
    let overallChatCountWithTemp = response.overallChatCount;
    if (typeof response.compiledChatKeys != 'undefined') {
        overallChatCountWithTemp += response.compiledChatKeys.temp_chats.length;
    }
    // updateStickyBadgeTxt('#channel-conversation-list', '#frm_stickyBadge');

    if (overallChatCountWithTemp > 0) {
        // chatCount.text(overallChatCountWithTemp);
        // $('#message_helper').hide();
        // $('#message_count').show();
    } else {
        frm_msgUpIndex = 0;
        frm_msgDownIndex = 0;
        // $('#message_count').hide();
        // if (chatwith_liveStatus == 1) $('#message_helper').show();
        frm_comments_list.html(`<div id="frm_no_message" class="col-lg-12" style="text-align: center;"></div>`);
    }

    if (response.hasOwnProperty('last_update_time')) {
        frmChatLastTime = response.last_update_time || 0;
    }

    // Clear Unread count
    const channelId = response.channel_id;
    const targetElement = $(`.channelList li#channel-${channelId} .unread-count`);
    if (targetElement?.length && targetElement.attr('data-count')) {
        clearUnreadCount(ntw_room_key, channelId, (response.callFromEvent === "init" ? 1 : 0));
    }    

    const $dot = $(`.pin_message_div${channelId} .pin_message_dot_div .message-item-dot`);
    if ($dot.length && !$dot.hasClass('active')) {
        $('[class*="pin_message_div"]').removeClass('d-flex').addClass('d-none');
        $(`.pin_message_div${channelId}`).removeClass('d-none').addClass('d-flex');

        $(`.pin_message_div${channelId} .pin_message_msg_div .pin_msg`).removeClass('d-flex').addClass('d-none');
        $(`.pin_message_div${channelId} .pin_message_msg_div .pin_msg`).first().removeClass('d-none').addClass('d-flex');

        $(`.pin_message_div${channelId} .pin_message_dot_div .message-item-dot`).removeClass('active');
        $(`.pin_message_div${channelId} .pin_message_dot_div .message-item-dot`).first().addClass('active');
    }

    const pinCount = $(`.pin_message_div${channelId} .pin_msg`).length;
    console.log("pinCount ======", pinCount);
    
    if(pinCount == 0) {
        $('.pin-message-v2').hide();
    } else{
        $('.pin-message-v2').show();
    }

    let video_chat_count = $('.join-v-link[video_name]').length;
    $('#channel-chat').attr('video_chat_count', video_chat_count);

    frm_isProcessing = false;
    $('#frmChatConversationLoader').awloader('hide');
    channelConversationList.awloader('hide');
}

function initFRMChatDataInterval() {
    if (frmChatDataInterval) clearInterval(frmChatDataInterval);
    frmChatDataInterval = setInterval(function () {
        if (!frm_isProcessing && chatWindow === 'channel') {
            if (my_pToken?.trim() !== '') {
                let requestData = getFRMChatRequestData(my_pToken, 0, 'interval');
                fetchFRMChatData(requestData, false);
            }
        }
    }, 3000);
}

async function updateForumWindow() {
    if (frmChatDataInterval) clearInterval(frmChatDataInterval);
    channelConversationList.empty();
    frm_msgUpIndex = 0;
    frm_msgDownIndex = 0;
    frmChatLastTime = 0;
    frm_msgScrollUpEnded = false;
    frmChatPageNo = 1;

    userLiveStatusInterval = 3000;
    if (userLiveIntervalId) clearInterval(userLiveIntervalId);

    initializeRequest(chatWindow, ntw_room_key, my_pToken);

    let requestData = getFRMChatRequestData(my_pToken, 0, 'init');
    fetchFRMChatData(requestData);

    initFRMChatDataInterval();
}

$(document).on('click', '.like-button', function () {
    let parentElem = $(this);
    parentElem.attr('disabled', true);
    let frmMessageId = parentElem.data('frm_message_id');
    let frmMessageKey = parentElem.data('frm_message_key');
    let channelId = $('#channel-chat').data('channel_id');

    const likedElem = parentElem.siblings('.liked-button');
    const likeTextElem = parentElem.siblings('.conversation-like-count');

    if (channelId && frmMessageId) {
        likeMessage(parentElem, channelId, frmMessageId, frmMessageKey, 1, likedElem, likeTextElem);
    } else {
        jq_confirm_alert('Warning', 'Message ID not found for like. Please try again later.', 'orange', 'Ok');
        parentElem.show();
    }
});

/*================================================= Forum Reply =================================================================*/

$(document).on('click', '.channel-reply', function () {
    let parentElem = $(this).closest('.chat-list');
    let frmMessageId = parentElem.data('frm_message_id');
    let frmMessageKey = parentElem.data('frm_message_key');
    let channelId = $('#channel-chat').data('channel_id');

    if (frmMessageId) {
        frm_message_id = frmMessageId;
        // taoh_Loader($('#frm_reply_chat_loader'), true);
        frm_reply_view = true;
        if (frmReplyChatDataInterval) clearInterval(frmReplyChatDataInterval);
        frm_reply_comments_list.empty();
        frm_reply_msgUpIndex = 0;
        frm_reply_msgDownIndex = 0;

        $('#reply_comment_id').val(frm_message_id);

        const channelReplyMessageBlockElem = $('#channel-reply-message-block');
        channelReplyMessageBlockElem.find('.conversation-name').html(parentElem.find('.ctext-name').html());
        channelReplyMessageBlockElem.find('.conversation-text').html(parentElem.find('.ctext-content').html());

        loadRightSidebar('reply');
        channelReplyConversationList.awloader('show');

        if (my_pToken !== '') {
            const chat_networking_misc_key = `ft_frm_networking_misc_${ntw_room_key}_${frm_message_id}`;
            IntaoDB.getItem(objStores.ntw_store.name, chat_networking_misc_key).then((intao_data) => {
                if (intao_data && intao_data.last_update_time) {
                    lastFRMReplyMsgCheckedTimestamp = intao_data.last_update_time;
                } else {
                    lastFRMReplyMsgCheckedTimestamp = 0;
                }
                getAndSetLastCheckedTimestamp('lastFRMReplyMsgCheckedTimestamp', lastFRMReplyMsgCheckedTimestamp, 2);
            }).then(() => {
                const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastFRMReplyMsgCheckedTimestamp', lastFRMReplyMsgCheckedTimestamp, 1);
                taohFRMMessagesFromServer(getForumMessagesFormData(ntw_room_key, my_pToken, frm_message_id, 1, lastCheckedTimestamp)).then(() => {
                    taohFRMMessagesFromServer(getForumMessagesFormData(ntw_room_key, my_pToken, frm_message_id, 0, lastCheckedTimestamp));

                    fetchFRMReplyChatData(getFRMChatRequestData(my_pToken, frm_message_id, 'init'));
                    initFRMReplyChatDataInterval();
                });
            });
        }

        try {
            clearNewRepliesCount(ntw_room_key, channelId, frmMessageId, frmMessageKey);
        } catch (error) {
            console.error('Failed to clearNewRepliesCount:', error);
        }
    }
});

$(document).on('click', '.channel-like', function () {
    let parentElem = $(this);
    let frmMessageId = parentElem.data('frm_message_id');

    if (frmMessageId) {
        frm_message_id = frmMessageId;
        // taoh_Loader($('#frm_reply_chat_loader'), true);
        frm_reply_view = true;
        if (frmReplyChatDataInterval) clearInterval(frmReplyChatDataInterval);
        frm_reply_comments_list.empty();
        frm_reply_msgUpIndex = 0;
        frm_reply_msgDownIndex = 0;

        $('#reply_comment_id').val(frm_message_id);

        const channelReplyMessageBlockElem = $('#channel-like-message-block');
        channelReplyMessageBlockElem.find('.conversation-name').html(parentElem.find('.ctext-name').html());
        channelReplyMessageBlockElem.find('.conversation-text').html(parentElem.find('.ctext-content').html());
        loadRightSidebar('like');
        channelLikeConversationList.awloader('show');

        showLikeList(frmMessageId);

        // if (my_pToken !== '') {
        //     const chat_networking_misc_key = `ft_frm_networking_misc_${ntw_room_key}_${frm_message_id}`;
        //     IntaoDB.getItem(objStores.ntw_store.name, chat_networking_misc_key).then((intao_data) => {
        //         if (intao_data && intao_data.last_update_time) {
        //             lastFRMReplyMsgCheckedTimestamp = intao_data.last_update_time;
        //         } else {
        //             lastFRMReplyMsgCheckedTimestamp = 0;
        //         }
        //         getAndSetLastCheckedTimestamp('lastFRMReplyMsgCheckedTimestamp', lastFRMReplyMsgCheckedTimestamp, 2);
        //     }).then(() => {
        //         const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastFRMReplyMsgCheckedTimestamp', lastFRMReplyMsgCheckedTimestamp, 1);
        //         taohFRMMessagesFromServer(getForumMessagesFormData(ntw_room_key, my_pToken, frm_message_id, 1, lastCheckedTimestamp)).then(() => {
        //             taohFRMMessagesFromServer(getForumMessagesFormData(ntw_room_key, my_pToken, frm_message_id, 0, lastCheckedTimestamp));

        //             fetchFRMReplyChatData(getFRMChatRequestData(my_pToken, frm_message_id, 'init'));
        //             initFRMReplyChatDataInterval();
        //         });
        //     });
        // }

        // try {
        //     clearNewRepliesCount(ntw_room_key, channelId, frmMessageId, frmMessageKey);
        // } catch (error) {
        //     console.error('Failed to clearNewRepliesCount:', error);
        // }
    }
});

$(document).on('click', '.frm-reply-delete-item', function () {
    const parentElem = $(this).closest('.chat-list');
    let frmMessageId = parentElem.data('frm_reply_message_id');
    let frmMessageKey = parentElem.data('frm_reply_message_key');
    let channelId = $('#channel-chat').data('channel_id');

    if (channelId && frmMessageId) {
        deleteComment(parentElem, channelId, frmMessageId, frmMessageKey, frm_message_id);
    } else {
        jq_confirm_alert('Warning', 'Message ID not found for deletion. Please try again later.', 'orange', 'Ok');
    }
});

function fetchFRMReplyChatData(requestData) {
    if (!(requestData.channel_id && requestData.pToken_from && parseInt(requestData.parent_id, 10))) return;

    const chat_messages_key = `frm_${ntw_room_key}_${requestData.channel_id}_${requestData.parent_id}`;

    IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key)
        .then((intao_data) => {
            if (!intao_data) {
                channelReplyConversationList.awloader('hide');
                return;
            }

            if (frm_reply_isProcessing) {
                setTimeout(() => fetchFRMReplyChatData(requestData), 2000);
                return;
            }

            frm_reply_isProcessing = true;

            const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastFRMReplyMsgCheckedTimestamp', lastFRMReplyMsgCheckedTimestamp, 1);

            processFRMReplyChatData(
                requestData,
                intao_data.timestamp ? intao_data.values : {
                    "chat": {},
                    "last_update_time": lastCheckedTimestamp,
                    "success": true
                }
            );
        })
        .catch((error) => {
            console.error("Error fetching FRMReplyChatData:", error);
        });
}

function processFRMReplyChatData(requestData, response) {
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
            if (requestData.callFromEvent === "init" || frm_reply_msgDownIndex === 0) {
                direction = 'before';
                let recentItems = paginateChat(response.chat, null, direction, requestData.itemPerPage);
                recentItemsObject = recentItems.data;
                firstKey = recentItems.firstCursor;
            } else if (requestData.callFromEvent === "scrollup") {
                direction = 'before';
                let recentItems = paginateChat(response.chat, frm_reply_msgUpIndex, direction, requestData.itemPerPage);
                recentItemsObject = recentItems.data;
                firstKey = recentItems.firstCursor;
            } else if (requestData.callFromEvent === "interval") {
                direction = 'after';
                let recentItems = paginateChat(response.chat, frm_reply_msgDownIndex, direction, requestData.itemPerPage);
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

    renderFRMReplyMessages(processedResponse);
}

async function compiledFRMReplyMsgHtml(cd) {
    let compiledMMMsgHtml;
    let safeMessageHtml = '';
    const messageHtml = decodeURIComponent(cd.message.replace(/\+/g, ' '));
    const message_content = linkifyWithJQuery(messageHtml);

    if (message_content.includes('chat-meeting-link') || cd.userType === 'system') {
        safeMessageHtml = message_content;
    } else {
        /*const safeMessage = document.createElement('pre');
        safeMessage.textContent = message_content;
        safeMessageHtml = safeMessage.innerHTML.replace(/\n/g, '<br>')
            .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');*/
        safeMessageHtml = message_content;
        //RK did this..
    }

    if (cd.userType === 'system') {
        compiledMMMsgHtml = `<li class="badge-system clearfix"><span class="badge badge-secondary">${safeMessageHtml}</span></li>`;
    } else {
        compiledMMMsgHtml = `<li class="chat-list ${cd.ptokenTo === cd.ptokenFrom ? 'right' : 'left'} ${'msg_' + (cd.time)} ${cd.isTempMsg ? 'temp_msg new' : ''}" data-frm_reply_message_id="${cd.message_id}" data-frm_reply_message_key="${cd.time}" id="${'msg_' + (cd.time)}">
                    <div class="conversation-list">
                        <div class="chat-avatar">
                            <img src="${cd.avatar}" alt="profile">
                        </div>
                        <div class="user-chat-content">
                            <div class="ctext-wrap">
                                <div class="ctext-wrap-content">
                                    <h6 class="mb-1 ctext-name">${cd.name} ${cd.userType == 'organizer' ? ` (${cd.userType})` : ''}</h6>
                                    <p class="mb-0 ctext-content">${safeMessageHtml}</p>
                                </div>
                                <div class="align-self-start message-box-drop d-flex">
                                    <div class="dropdown">
                                        <a class="" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ri-emotion-happy-line fs-20"></i> </a>
                                        <div class="dropdown-menu emoji-dropdown-menu">
                                            <div class="hstack align-items-center gap-2 px-2 fs-22">
                                                <a href="javascript:void(0);">💛</a>
                                                <a href="javascript:void(0);">🤣</a>
                                                <a href="javascript:void(0);">😜</a>
                                                <a href="javascript:void(0);">😘</a>
                                                <a href="javascript:void(0);">😍</a>
                                                <div class="avatar-xs">
                                                    <a href="javascript:void(0);" class="avatar-title bg-soft-primary rounded-circle fs-19 text-white">+</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <a class="" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ri-more-2-fill fs-20"></i></a>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item d-flex align-items-center justify-content-between copy-message" href="#" id="copy-message-0">Copy <i class="bx bx-copy text-muted ms-2"></i></a>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between pin-message" href="#" id="pin-message-0">Pin <i class="bx bx-pin text-muted ms-2"></i></a>
                                            ${cd.ptokenTo === cd.ptokenFrom ? '<a class="dropdown-item d-flex align-items-center justify-content-between frm-reply-delete-item" href="#">Delete <i class="bx bx-trash text-muted ms-2"></i></a>' : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="conversation-name">
                                <span class="text-success check-message-icon"><i class="bx ${parseInt(cd.message_id, 10) ? '' : 'bx-sync bx-spin'}"></i></span> <!--bx-check-double-->
                                <small class="text-muted time">${cd.formatted_time}</small>
                                <span class="conversation-loader-icon"><i class="bx bx-sync bx-spin bx-sm"></i></span>
                            </div>
                        </div>
                    </div>
                </li>`;
    }

    return compiledMMMsgHtml;
}

async function getFRMReplyChatMsgHtml(response, tempMsgList) {
    const chats = response.chat || {};
    const isTempMsg = response.isTempMsg || false;
    const lastChatItemKey = Object.keys(response.chat || {}).pop();
    const compiledChatKeys = { chats: [], temp_chats: [] };
    let messageHtml = '';

    const orderedChats = Object.entries(chats).map(([key, v]) => ({
        key,
        ...v,
        sent_time: v.sent_time ?? Math.floor(v.time / 1000),
        isTempMsg
    }));

    const mergedChats = [...orderedChats];

    // Merge - insert temp messages into correct position
    if(response.callFromEvent !== 'scrollup') {
        const tempChats = Object.entries(tempMsgList || {}).map(([key, v]) => ({
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

    // Process merged chats
    for (const v of mergedChats) {
        const userInfo = await getUserInfo(v.ptoken, 'public');
        const fallbackSrc = `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;
        const avatar = await buildAvatarImage(userInfo.avatar_image, fallbackSrc);
        const msgTime = Math.floor(v.time / 1000);
        const badgeArr = formatBadgeDateTime(msgTime, _taoh_user_timezone);

        /*if (v.ptoken == my_pToken && (!v.isTempMsg || response.callFromEvent !== 'scrollup')) {
            frm_my_recent_message_timestamp = msgTime;
        }

        if (v.ptoken != my_pToken && response.callFromEvent !== 'init' && response.callFromEvent !== 'scrollup') {
            frm_newMessagesCnt++;
        }*/

        if (badgeArr?.[0]) {
            const badgeDate = badgeArr[0];

            if (response.callFromEvent === 'scrollup' && v.key === lastChatItemKey) {
                removeSameBadge('#chat-reply-conversation-channel', badgeDate);
            }

            if (response.callFromEvent === 'scrollup' && badgeDate !== frm_reply_chatBadgeUpDate) {
                frm_reply_chatBadgeUpDate = badgeDate;
                messageHtml += `<li class="date-badge mb-3 clearfix" data-timestamp="${v.time}">
                            <span class="badge text-muted">${badgeDate}</span>
                        </li>`;
            } else if (response.callFromEvent !== 'scrollup' && badgeDate !== frm_reply_chatBadgeDownDate) {
                frm_reply_chatBadgeDownDate = badgeDate;
                messageHtml += `<li class="date-badge mb-3 clearfix" data-timestamp="${v.time}">
                            <span class="badge text-muted">${badgeDate}</span>
                        </li>`;
            }
        }

        const compileData = {
            ptokenTo: v.ptoken,
            message: v.message,
            message_id: v.message_id,
            is_parent: v.parent_id == 0,
            time: v.time,
            name: userInfo.chat_name,
            avatar,
            ptokenFrom: my_pToken,
            userType: v.user_type,
            isTempMsg: v.isTempMsg,
            stored_time: v.stored_time ?? '',
            formatted_time: badgeArr?.[1] ?? ''
        };

        messageHtml += await compiledFRMReplyMsgHtml(compileData);

        if (v.isTempMsg) {
            compiledChatKeys.temp_chats.push(v.key);
        } else {
            compiledChatKeys.chats.push(v.key);
        }

        $(`#msg_${v.sent_time * 1000}`).removeClass('new');
    }

    return { messageHtml, compiledChatKeys };
}

function renderFRMReplyMessages(response) {
    if (!response) {
        doAfterFRMReplyMsgRender(response);
        return;
    }

    let channelId = response.channel_id;
    let chats = response.chat || {};
    let allChatKeys = Object.keys(chats);

    let chatTempMessagesKey = `frm_temp_${ntw_room_key}_${channelId}_${frm_message_id}`;
    IntaoDB.getItem(objStores.ntw_store.name, chatTempMessagesKey)
        .then(intao_data => intao_data?.values || {})
        .then(tempMsgList => {
            return getFRMReplyChatMsgHtml(response, tempMsgList);
        })
        .then(({ messageHtml, compiledChatKeys }) => {
            if (response.callFromEvent === 'init') {
                frm_reply_comments_list.empty();
            }

            $("#chat-reply-conversation .temp_msg:not(.new)").remove();

            if (compiledChatKeys.chats.length > 0 || compiledChatKeys.temp_chats.length > 0) {
                $('#frm_reply_no_message').remove();
                // $('#message_helper').hide();

                if (response.callFromEvent === 'scrollup') {
                    prependAndRestoreScrollPositionWithSimpleBar('#chat-reply-conversation', () => {
                        frm_reply_comments_list.prepend(messageHtml);
                    });
                } else {
                    frm_reply_comments_list.append(messageHtml);
                    if (compiledChatKeys.chats.length > 0 || compiledChatKeys.temp_chats.length > 0) {
                        if (isScrolledUpSimplebar('#chat-reply-conversation', 600) && response.callFromEvent !== 'init') {
                            if (frm_reply_newMessagesCnt > 0) {
                                // newmessages_btn.find('span').text(frm_newMessagesCnt + ' new messages');
                                // newmessages_btn_grp.show();
                            }
                        } else {
                            simpleBarScrollToBottom('#chat-reply-conversation');
                            frm_reply_newMessagesCnt = 0;
                        }
                    }
                }
            }

            if (compiledChatKeys.chats.length > 0 && !response.isTempMsg) {
                if (response.callFromEvent === 'init') {
                    frm_reply_msgUpIndex = allChatKeys[0];
                    frm_reply_msgDownIndex = allChatKeys.pop();
                } else if (response.callFromEvent === 'scrollup') {
                    frm_reply_msgUpIndex = allChatKeys[0];
                } else if (response.callFromEvent === 'interval') {
                    frm_reply_msgDownIndex = allChatKeys.pop();
                }
            }

            response.compiledChatKeys = compiledChatKeys;

            // Clearing left over temp messages if already rendered
            if ($('#chat-reply-conversation-list .temp_msg').length) {
                let recentRenderedItems = response.recent_rendered_items;
                for (const [k, value] of Object.entries(recentRenderedItems)) {
                    let tempMsgElem = $('.msg_' + (value.sent_time * 1000));
                    if (tempMsgElem.length) tempMsgElem.remove();
                }
            }

            // Scroll Up Event Code
            let overallChatFirstKey = response.firstKey;
            let compiledChatFirstKey = compiledChatKeys.chats.length ? compiledChatKeys.chats[0] : null;
            if (compiledChatFirstKey && response.callFromEvent === "scrollup" && overallChatFirstKey === compiledChatFirstKey) {
                frm_reply_msgScrollUpEnded = true;
            }
        })
        .then(() => {
            doAfterFRMReplyMsgRender(response);
        });
}

function doAfterFRMReplyMsgRender(response) {
    let overallChatCountWithTemp = response.overallChatCount;
    if (typeof response.compiledChatKeys != 'undefined') {
        overallChatCountWithTemp += response.compiledChatKeys.temp_chats.length;
    }
    // updateStickyBadgeTxt('#channel-conversation-list', '#frm_stickyBadge');

    if (overallChatCountWithTemp > 0) {
        // chatCount.text(overallChatCountWithTemp);
        // $('#message_helper').hide();
    } else {
        frm_reply_msgUpIndex = 0;
        frm_reply_msgDownIndex = 0;
        // if (chatwith_liveStatus == 1) $('#message_helper').show();
        frm_reply_comments_list.html(`<div id="frm_reply_no_message" class="col-lg-12" style="text-align: center;">
                    <img class="no-network-place" src="${_taoh_site_url_root + '/assets/images/empty_network.png'}" width="300" alt="no-network">
                </div>`);
    }

    if (response.hasOwnProperty('last_update_time')) {
        frmReplyChatLastTime = response.last_update_time || 0;
    }

    frm_reply_isProcessing = false;
    channelReplyConversationList.awloader('hide');
}

function initFRMReplyChatDataInterval() {
    if (frmReplyChatDataInterval) clearInterval(frmReplyChatDataInterval);
    frmReplyChatDataInterval = setInterval(function () {
        if (!frm_reply_isProcessing) {
            if (my_pToken?.trim() !== '') {
                let requestData = getFRMChatRequestData(my_pToken, frm_message_id, 'interval');
                fetchFRMReplyChatData(requestData, false);
            }
        }
    }, 3000);
}

/*================================================= /Forum Reply =================================================================*/


function deleteComment(elem, channel_id, frmMessageId, frmMessageKey = '', parent_id = 0) {
    let isReplyRequest = Boolean(parseInt(parent_id, 10));
    let sent_time = new Date().getTime();

    var event_token_send = '';
    if(selectedChat == 'organizer'){

        event_token_send = eventtoken;
    }

    let data = {
        'roomslug': ntw_room_key,
        'taoh_action': 'taoh_channel_delete_message',
        'message_id': frmMessageId,
        'message_key': frmMessageKey,
        'ptoken': my_pToken,
        'other_ptoken': '',
        'parent_id': parent_id,
        'channel_id': channel_id,
        'user_type': 'user',
        'key': ntw_room_key,
        'event_token': event_token_send,
        'sent_time': sent_time
    };

    let loaderElem = elem.find('.conversation-loader-icon');
    loaderElem.show();

    $.ajax({
        url: _taoh_site_ajax_url,
        type: 'post',
        data: data,
        dataType: 'json',
        success: function (response) {
            console.log("delete response:", response);
            if (response.success && response.output) {
                console.log("delete response11:", '#msg_' + frmMessageKey);

                const msgElem = $('#msg_' + frmMessageKey);
                if (msgElem.length) msgElem.remove();

                let chat_messages_key = `frm_${data.key}_${data.channel_id}${isReplyRequest ? '_' + data.parent_id : ''}`;
                IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key).then((intao_data) => {
                    if (intao_data?.values) {
                        let updatedResponse = intao_data.values;
                        if (updatedResponse && Object.keys(updatedResponse.chat).length > 0) {
                            let is_deleted = false;
                            let deletedEntryKey = null;
                            for (const key in updatedResponse.chat) {
                                if (updatedResponse.chat[key].message_id === data.message_id) {
                                    deletedEntryKey = key;
                                    delete updatedResponse.chat[key];
                                    is_deleted = true;
                                    break;
                                }
                            }

                            if (is_deleted) {
                                const lastMatchingEntry = Object.values(updatedResponse.chat).reverse().find(entry => entry.ptoken === data.ptoken);
                                if (lastMatchingEntry) frm_my_recent_message_timestamp = lastMatchingEntry.time / 1000;
                                if(deletedEntryKey){
                                    elem.remove();

                                    console.log("test", deletedEntryKey);
                                    

                                    const msgElem = $('#msg_' + deletedEntryKey);
                                    if (msgElem.length) msgElem.remove();
                                }

                                IntaoDB.setItem(objStores.ntw_store.name, {taoh_ntw: chat_messages_key, values: updatedResponse, timestamp: Date.now()});
                            }
                        }
                    }
                }).then(() => {
                    loaderElem.hide();
                    checkCommentFormTime();
                });
            } else {
                loaderElem.hide();
                console.log('Error deleting comment:', response);
            }
        },
        error: function (xhr, status, error) {
            loaderElem.hide();
            console.log('Error deleting comment:', xhr.status);
        }
    });
}

async function likeMessage(elem, channel_id, frmMessageId, frmMessageKey, isLiked, likedElem, likeTextElem) {

    let sent_time = new Date().getTime();

    let data = {
        'taoh_action': 'taoh_channel_like_message',
        'message_id': frmMessageId,
        'message_key': frmMessageKey,
        'ptoken': my_pToken,
        'other_ptoken': '',
        'channel_id': channel_id,
        'user_type': 'user',
        'key': ntw_room_key,
        'sent_time': sent_time,
        'like': isLiked,
    };

    let loaderElem = elem.find('.conversation-loader-icon');
    loaderElem.show();

    const response = await new Promise((resolve, reject) => {
        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'post',
            data: data,
            dataType: 'json',
            success: function (res) {
                console.log(res);
                if (res.status == "success") {                
                    likedElem.removeClass('d-none');
                    elem.addClass('d-none');               

                    const chat_messages_key = `frm_${ntw_room_key}_${channel_id}`;

                    IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key).then((intao_data) => {

                        if (!intao_data?.values || !intao_data.values.chat[frmMessageKey]) {
                            console.log("not updated");
                            return;
                        }		

                        //let likeCount = parseInt(likeTextElem.attr("data-like_count")) + 1;
                        
                        const updatedResponse = { ...intao_data.values };	

                        let likeCount = updatedResponse.chat[frmMessageKey].like_count + 1;
                        likeTextElem.attr("data-like_count", likeCount);

                        updatedResponse.chat[frmMessageKey].like_count = likeCount;
                        updatedResponse.chat[frmMessageKey].mylike = 1;
                        
                        let likeTextSuffix = (likeCount > 1) ? " likes" : " like";

                        likeTextElem.text(likeCount+likeTextSuffix);
                        likeTextElem.removeClass('d-none');

                        IntaoDB.setItem(objStores.ntw_store.name, {
                            taoh_ntw: chat_messages_key,
                            values: updatedResponse,
                            timestamp: Date.now()
                        });
                        console.log("updated");
                        const response_data = {
                            status: 200,
                            success: res.status
                        };
                        resolve(response_data);
                    });
                    
                } else {
                    elem.attr('disabled', false);
                    loaderElem.hide();
                    console.log('Error :', response);
                }
            },
            error: function (xhr, status, error) {
                elem.attr('disabled', false);
                loaderElem.hide();
                console.log('Error :', xhr.status);
            }
        });
    });    
}

async function pinMessagePost(msgOwner, elem, channel_id, frmMessageId, frmMessageKey, pin, pinFrom = "channel", chatWith, unpinOld = 0) {

    let sent_time = new Date().getTime();

    if(elem == undefined) {
        elem = $(`.pin_msg[data-frm_message_id="${frmMessageId}"]`);
    }    

    var userInfo = await getUserInfo(msgOwner, 'public');

    var myInfo = await getUserInfo(my_pToken, 'public');
    let my_name;
    if (!myInfo) {
        my_name = "";
    } else {
        my_name = myInfo.chat_name;
        my_name = my_name.charAt(0).toUpperCase() + my_name.slice(1);
    }

    let data = {
        'taoh_action': 'taoh_channel_pin_message',
        'message_id': frmMessageId,
        'message_key': frmMessageKey,
        'ptoken': my_pToken,
        'other_ptoken': '',
        'channel_id': channel_id,
        'user_type': 'user',
        'key': ntw_room_key,
        'sent_time': sent_time,
        'pin': pin,
        'pinFrom': pinFrom,
        'chatWith': chatWith,
        'unpinOld': unpinOld,
    };

    const response = await new Promise((resolve, reject) => {
        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'post',
            data: data,
            dataType: 'json',
            success: function (res) {
                console.log(res);
                if (res && res.status == "success") {
                    //likedElem.removeClass('d-none');
                    //elem.addClass('d-none');    
                    var chat_messages_key;

                    var $parent;
                    var msg_div;

                    if(pinFrom == "channel") {
                        $parent = $('.pin-message-v2');
                        msg_div = "pin_message_div";
                        chat_messages_key = `frm_${ntw_room_key}_${channel_id}`;
                        if(pin == 1) {
                            sendFRMChat(my_name+" pinned a message", my_pToken, 0, ntw_room_key, channel_id, 'system');
                        }                        
                    } else {
                        $parent = $('.pin-message-v2-dm');
                        msg_div = "pin_message_div-dm";
                        chat_messages_key = `cm_${ntw_room_key}_${my_pToken}_${chatWith}`;
                        if(pin == 1) {
                            sendNTWChat(my_name+" pinned a message", my_pToken, chatWith, ntw_room_key, channel_id, 'system');
                        }
                    }

                    IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key).then((intao_data) => {

                        if (!intao_data?.values || !intao_data.values.chat[frmMessageKey]) {
                            chat_messages_key = `cm_${ntw_room_key}_${chatWith}_${my_pToken}`;
                            intao_data = IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key);
                            if (!intao_data?.values || !intao_data.values.chat[frmMessageKey]) {
                                console.log("not updated ==>>", chat_messages_key);
                                return;
                            }
                        }		
                        
                        const updatedResponse = { ...intao_data.values };	
                        updatedResponse.chat[frmMessageKey].pin = pin;
                        updatedResponse.chat[frmMessageKey].pinned_by = my_pToken;

                        let message = updatedResponse.chat[frmMessageKey].message;
                        message = decodeURIComponent(message.replace(/\+/g, ' '));
                        
                        if(pin == 1) {                            

                            if (userInfo.avatar_image != '' && userInfo.avatar_image != undefined) {
                                var avatar_image = userInfo.avatar_image;
                            } else if (userInfo.avatar != undefined && userInfo.avatar != 'default') {
                                var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/' + userInfo.avatar + '.png';
                            } else {
                                var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/avatar_def.png';
                            }

                            console.log("^^^^^^^^^^=======^^^^^^^^^", message);
                            

                            let decoded = decodeURIComponent(message);
                            decoded = decoded.replace(/\+/g, '');
                            const match = decoded.match(/<a [^>]*>.*?<\/a>/i);
                            const aTag = match ? match[0] : "";           
                            if(aTag != "") {
                                var msgHTML = "Join "+aTag+" - Video Room";
                            } else {
                                let msg = decodeURIComponent(message.replace(/\+/g, ' '));
                                if (msg.length > 100) {
                                    var visibleText = msg.slice(0, 100);
                                    var hiddenText = msg.slice(100);
                                    var msgHTML = `${visibleText}<span class="d-none">${hiddenText}</span> <button type="button" class="btn btn-link p-0 shadow-none show_more_btn">Show More</button>`;
                                } else {
                                    var msgHTML = msg;
                                }
                            }                              
                            

                            if ($parent.find('.'+msg_div+channel_id).length === 0) {
                                $parent.append(`<div class="pb-2 d-flex align-items-center comm_pin_message_div ${msg_div}${channel_id}" style="gap: 12px;">
                                    <div class="nav-vertical-dots flex-shrink-0 pin_message_dot_div"> </div>
                                    <div class="flex-grow-1 pin_message_msg_div"> </div>
                                </div>`);
                            }

                            var pin_msg_count = $(`.${msg_div}${channelId} .pin_message_msg_div pin_msg`).length;
                            var activeClass = "";
                            if(pin_msg_count > 0) {
                                activeClass = "active";
                            }
                            
                            if ($(`.${msg_div}${channel_id} .pin_message_msg_div [data-frm_message_id="${frmMessageId}"]`).length === 0) {
                                
                                $(`.${msg_div}${channel_id} .pin_message_dot_div`).append(`<div class="message-item-dot ${activeClass}" data-channel_id="${channel_id}" data-frm_message_id="${frmMessageId}"></div>`);

                                $(`.${msg_div}${channel_id} .pin_message_msg_div`).append(`<div class="pin_msg flex-grow-1 ${(activeClass == "active") ? 'd-flex' : 'd-none'}" data-channel_id="${channel_id}" data-frm_message_id="${frmMessageId}">
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
                                            <a class="dropdown-item d-flex align-items-center justify-content-between unpin-message" data-type="${pinFrom}" data-frm_message_id="${frmMessageId}" data-frm_message_key="${frmMessageKey}" >Unpin</a>
                                        </div>
                                    </div>
                                </div>`);

                                elem.attr('data-action', 0);
                                elem.html('Unpin <i class="bx bx-unlink text-muted ms-2"></i>');
                            }                            
                        } else {
                            $('.pin_msg[data-frm_message_id="' + frmMessageId + '"]').remove();
                            $('.message-item-dot[data-frm_message_id="' + frmMessageId + '"]').remove();                            
                            elem.attr('data-action', 1);
                            elem.html('Pin <i class="bx bx-pin text-muted ms-2"></i>');
                        }                        

                        IntaoDB.setItem(objStores.ntw_store.name, {
                            taoh_ntw: chat_messages_key,
                            values: updatedResponse,
                            timestamp: Date.now()
                        });
                        console.log("pin updated");
                        const response_data = {
                            status: 200,
                            success: true
                        };
                        resolve(response_data);
                    });
                    
                } else {
                    elem.attr('disabled', false);
                    console.log('Error :');
                }
            },
            error: function (xhr, status, error) {
                elem.attr('disabled', false);
                //loaderElem.hide();
                console.log('Error :', xhr.status);
            }
        });
    });    
}

lastJobPostedDate();

async function lastJobPostedDate() {

    let data = {
        'taoh_action': 'taoh_last_job_posted_date',
        'ptoken': my_pToken,
    };

    const response = await new Promise((resolve, reject) => {
        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'post',
            data: data,
            dataType: 'json',
            success: function (res) {
                console.log(res);
                if (res.success) {         
                    _taoh_last_job_post_date = res.output?.last_post_date;               
                } else {
                    console.log('Error :');
                }
            },
            error: function (xhr, status, error) {
                console.log('Error :', xhr.status);
            }
        });
    });   
   
}

async function emojiPost(data_type, chatfrom, chatwith, elem, channel_id, frmMessageId, frmMessageKey, emoji) {

    let sent_time = new Date().getTime();

    let data = {
        'taoh_action': 'taoh_channel_like_message',
        'message_id': frmMessageId,
        'message_key': frmMessageKey,
        'ptoken': my_pToken,
        'other_ptoken': '',
        'channel_id': channel_id,
        'user_type': 'user',
        'key': ntw_room_key,
        'sent_time': sent_time,
        'emoji': emoji,
        'emoji_from': data_type,        
    };

    let loaderElem = elem.find('.conversation-loader-icon');
    loaderElem.show();

    const response = await new Promise((resolve, reject) => {
        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'post',
            data: data,
            dataType: 'json',
            success: function (res) {
                console.log(res);
                if (res.status == "success") {                

                    var chat_messages_key;
                    if(data_type == "dm") {
                        chat_messages_key = `cm_${ntw_room_key}_${chatfrom}_${chatwith}`;
                    } else {
                        chat_messages_key = `frm_${ntw_room_key}_${channel_id}`;
                    }
                    

                    IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key).then((intao_data) => {

                        if (!intao_data?.values || !intao_data.values.chat[frmMessageKey]) {
                            console.log("not updated ==>>><<<", chat_messages_key);
                            return;
                        }		
                        
                        const updatedResponse = { ...intao_data.values };

                        updatedResponse.chat[frmMessageKey].reactions = updatedResponse.chat[frmMessageKey].reactions || {};
                        updatedResponse.chat[frmMessageKey].reactions[my_pToken] = emoji;

                        IntaoDB.setItem(objStores.ntw_store.name, {
                            taoh_ntw: chat_messages_key,
                            values: updatedResponse,
                            timestamp: Date.now()
                        });
                        console.log("updated");
                        const response_data = {
                            status: 200,
                            success: true
                        };
                        resolve(response_data);
                    });
                    
                } else {
                    elem.attr('disabled', false);
                    loaderElem.hide();
                    console.log('Error :', res);
                }
            },
            error: function (xhr, status, error) {
                elem.attr('disabled', false);
                loaderElem.hide();
                console.log('Error :', xhr.status);
            }
        });
    });    
}

function checkCommentFormTime() {
    /*const d = new Date(frm_my_recent_message_timestamp);
    if (isNaN(d.getTime())){
        document.getElementById('frm_comment_send_btn').disabled = false;
        return;
    }

    const currentTime = new Date();
    let timeDifference = getTimeDifferenceInSeconds(currentTime, d);

    if (timeDifference < frm_commentDelayTimeInSeconds) {
        const delayTimeInSeconds = frm_commentDelayTimeInSeconds - timeDifference;

        const hours = Math.floor(delayTimeInSeconds / 3600).toString().padStart(2, '0');
        const minutes = Math.floor((delayTimeInSeconds % 3600) / 60).toString().padStart(2, '0');
        const seconds = (delayTimeInSeconds % 60).toString().padStart(2, '0');

        let timeString = `${hours}:${minutes}:${seconds}`;

        let frm_commentDelayTimeInHours = (frm_commentDelayTimeInSeconds / 3600);
        frm_commentDelayTimeInHours = frm_commentDelayTimeInHours % 1 ? frm_commentDelayTimeInHours.toFixed(2) : frm_commentDelayTimeInHours.toFixed(0);

        document.getElementById('comment_form_note').textContent =
            `Note: You can post again after ${frm_commentDelayTimeInHours} hours. Since your last comment was less than ${frm_commentDelayTimeInHours} hours ago, you will be able to comment again in ${timeString}.`;
        document.getElementById('comment_form_note_blk').style.display = 'block';
        document.getElementById('frm_comment_send_btn').disabled = true;
        if (!frm_checkCommentDelayInterval) {
            frm_checkCommentDelayInterval = setInterval(checkCommentFormTime, 1000); // Check every second
        }
    } else {
        document.getElementById('comment_form_note_blk').style.display = 'none';
        document.getElementById('frm_comment_send_btn').disabled = false;
        if (frm_checkCommentDelayInterval) clearInterval(frm_checkCommentDelayInterval);
    }*/
}
    