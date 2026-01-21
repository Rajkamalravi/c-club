
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

$(document).ready(function () {
    async function create_1_1_channel(pToken_from, pToken_to, room_id) {
        const data = {
            taoh_action: 'taoh_create_channel_for_1_1',
            key: pToken_from,
            ptoken: pToken_from,
            room_id,
            chatwith: pToken_to,
            chatwith_chatname: pToken_to,
            loggedin_chatname: pToken_from
        };

        return new Promise((resolve, reject) => {
            $.post(_taoh_site_ajax_url, data, (response) => {
                if (response?.channel_id) {
                    resolve(response.channel_id);
                } else {
                    reject('Channel creation failed');
                }
            }).fail(reject);
        });
    }

    $('#privateChatForm').submit(async function (e) {
        e.preventDefault();

        const $form = $(this);
        const $input = $form.find('input[name="pc_message"]');
        const message = $input.val()?.trim();

        if (!message) return;

        $input.val(''); // Clear the input
        const sent_time = Date.now();

        try {
            const channel_id = await create_1_1_channel(pToken_from, pToken_to, pc_room_key);

            const messageData = {
                taoh_action: 'taoh_direct_send_message',
                message,
                ptoken: pToken_from,
                other_ptoken: pToken_to,
                user_type: 'user',
                key: pc_room_key,
                channel_id,
                sent_time
            };

            const tempMessage = {
                ptoken: messageData.ptoken,
                message: messageData.message,
                to_ptoken: messageData.other_ptoken,
                user_type: messageData.user_type,
                room_hash: messageData.key,
                channel_id: messageData.channel_id,
                sent_time: messageData.sent_time,
                time: messageData.sent_time * 1000
            };

            const chatresponse = {
                chat: [tempMessage],
                isTempMsg: true,
                overallChatCount: 1,
                recent_rendered_items: {}
            };

            renderMessages(chatresponse);

            const chat_temp_key = `cm_temp_${pc_room_key}_${pToken_from}`;
            const ptokenTo = messageData.other_ptoken;

            const intao_data = await IntaoDB.getItem(objStores.ntw_store.name, chat_temp_key);
            const updatedMessages = intao_data?.values || {};
            if (!(ptokenTo in updatedMessages)) updatedMessages[ptokenTo] = {};
            updatedMessages[ptokenTo][sent_time] = tempMessage;

            await IntaoDB.setItem(objStores.ntw_store.name, {
                taoh_ntw: chat_temp_key,
                values: updatedMessages,
                timestamp: Date.now()
            });

            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'POST',
                data: messageData,
                dataType: 'json',
                success: function (response) {
                    if (response?.success) {
                        pc_reFetchRequired = true;
                        update_stored_time_in_temp_messages_pc(chat_temp_key, response, ptokenTo);
                        taoh_pc_post_metrics('chatpost', pc_room_key);
                    }
                },
                error: function () {
                    console.error('PC send failed.');
                }
            });

        } catch (err) {
            console.error('Failed to create or send message:', err);
        }
    });

    $('#profile_offline_send_message_btn').on('click', function () {
        let toPtoken = $(this).data('toptoken');
        let respondPtoken = _ntw_ft_ptoken ?? '';

        showOfflineMessageModal(toPtoken, respondPtoken);
    });

    $('.profile_send_message_btn').on('click', function () {
        let toPtoken = $(this).data('toptoken');
        let respondPtoken = $(this).data('respondptoken');

        showOfflineMessageModal(toPtoken, respondPtoken);
    });

    $('#profile_message_send_button').on('click', function () {
        let message = $('#profileOfflineMessage').val();
        let locationPath = $('#profileOfflineLocationPath').val();
        let toPtoken = $('#profileOfflineToPtoken').val();
        let profile_message_send_button_elem = $('#profile_message_send_button');

        if(message.trim() === ''){
            alert('Please enter message');
            return false;
        }

        profile_message_send_button_elem.attr('disabled', 'disabled');
        profile_message_send_button_elem.html('Sending <i class="fa fa-circle-o-notch fa-spin"></i>');
        $.post(_taoh_site_ajax_url, {
            'taoh_action': 'taoh_ntw_post_message',
            'message': message,
            "chatwith": toPtoken,
            'key': _ntw_ft_ptoken,
            'roomslug': dm_room_slug,
            "location_path": locationPath
        }, function (response) {
            $('#profileOfflineMessage').val('');
            $('#profileOfflineMessageBlock').hide();
            $('#profileOfflineSuccessMessage').show();
            setTimeout(function () {
                profile_message_send_button_elem.removeAttr('disabled');
                profile_message_send_button_elem.text('Send');
                $('#profileOfflineMessageModal').modal('hide');
            }, 1500);
        });

        if ($('#privateChatForm').length > 0) {
            $('#pc_message').val(message);
            $('#pc_send_btn').trigger('click');
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

    if (displayPrivateChat) {
        setInterval(function () {
            if (!pc_isProcessing) { // (pc_msgNewEntriesCnt > 0) &&
                if (pToken_from.trim() !== '' && pToken_to.trim() !== '') {
                    let requestData = getChatRequestData(pToken_from, pToken_to, 'interval');
                    fetchChatData(requestData);
                }
            }
        }, 3000);

        // Initial call to load the first set of messages
        let requestData = getChatRequestData(pToken_from, pToken_to, 'init');
        fetchChatData(requestData);

        if (pToken_to) {
            setInterval(function () {
                if (!document.hidden && typeof getUserLiveStatus === 'function') {
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

        if (open_profile_chat) {
            $('#privateChatList').collapse('show');
        }

        if (_ntw_ft_valid_user && _ntw_ft_ptoken !== '' && pc_room_key) {
            // Initial Entry - Private Chat
            const chat_pc_misc_key = `ft_pc_networking_misc_${pc_room_key}`;
            IntaoDB.getItem(objStores.ntw_store.name, chat_pc_misc_key).then((intao_data) => {
                if (intao_data && intao_data.last_update_time) {
                    lastPCMsgCheckedTimestamp = intao_data.last_update_time;
                } else {
                    lastPCMsgCheckedTimestamp = 0;
                }
                getAndSetLastCheckedTimestamp('lastPCMsgCheckedTimestamp', lastPCMsgCheckedTimestamp, 2);
            }).then(() => initializeRequest('private_message', pc_room_key, _ntw_ft_ptoken));
        }
    }
});

function showOfflineMessageModal(toPtoken, respondPtoken){
    if (toPtoken?.trim() && respondPtoken?.trim()) {
        $('#profileOfflineMessage').val('');
        $('#profileOfflineToPtoken').val(toPtoken);
        $('#profileOfflineLocationPath').val('/profile/' + respondPtoken);
        $('#profileOfflineSuccessMessage').hide();
        $('#profileOfflineMessageBlock').show();
        $('#profileOfflineMessageModal').modal('show');
    }
}

const chatContainer = document.getElementById('privatechat');

function getChatRequestData(pToken_from, pToken_to, callFromEvent = 'init') {
    return {
        pToken_from,
        pToken_to,
        channel_id: pc_channel_id,
        page: privateChatPageNo,
        itemPerPage: privateChatItemsPerPage,
        callFromEvent
    };
}

function fetchChatData(requestData) {
    if (!requestData.channel_id || !requestData.pToken_from || !requestData.pToken_to) return;

    const chat_messages_key = `cm_${pc_room_key}_${requestData.pToken_from}_${requestData.pToken_to}`;

    IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key)
        .then((intao_data) => {
            if (!intao_data) {
                taohLoader(document.getElementById('pc_loader'), false);
                // $('#ntwChatConversationLoader').awloader('hide');
                // usersConversationList.awloader('hide');
                return;
            }

            if (pc_isProcessing) {
                setTimeout(() => fetchChatData(requestData), 2000);
                return;
            }

            pc_isProcessing = true;

            const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastPCMsgCheckedTimestamp', lastPCMsgCheckedTimestamp, 1);

            processPrivateChatData(
                requestData,
                intao_data.timestamp ? intao_data.values : {
                    "chat": {},
                    "last_update_time": lastCheckedTimestamp,
                    "success": true
                }
            );
        })
        .catch((error) => {
            console.error("Error fetching PcChatData:", error);
        });
}

function processPrivateChatData(requestData, response) {
    let processedResponse = {};
    let recentItemsObject = {};
    let recentRenderedItemsObject = {};
    let totalchats = 0;
    let firstKey = null;

    if (response.success) {
        let chats = response.chat ? response.chat : [];

        let allChatKeys = Object.keys(chats);
        totalchats = allChatKeys.length;
        if (totalchats > 0) {
            let direction;
            if (requestData.callFromEvent === "init" || pc_msgDownIndex === 0) {
                direction = 'before';
                let recentItems = paginateChat(response.chat, null, direction, requestData.itemPerPage);
                recentItemsObject = recentItems.data;
                firstKey = recentItems.firstCursor;
            } else if (requestData.callFromEvent === "scrollup") {
                direction = 'before';
                let recentItems = paginateChat(response.chat, pc_msgUpIndex, direction, requestData.itemPerPage);
                recentItemsObject = recentItems.data;
                firstKey = recentItems.firstCursor;
            } else if (requestData.callFromEvent == "interval") {
                direction = 'after';
                let recentItems = paginateChat(response.chat, pc_msgDownIndex, direction, requestData.itemPerPage);
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
    processedResponse.overallChatCount = totalchats;
    processedResponse.recent_rendered_items = recentRenderedItemsObject;
    processedResponse.last_update_time = response.last_update_time;

    renderMessages(processedResponse);
}

async function compiledMsgHtml(cd){
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
        const msgTimeInMilliSeconds = Math.floor(v.time / 1000);
        let stored_time = v.hasOwnProperty('stored_time') ? v.stored_time : '';

        if(v.ptoken != pToken_from){
            if (!do_highlight && response.callFromEvent !== 'init' && response.callFromEvent != 'scrollup') do_highlight = true;
        }

        let chatDateTime_arr = formatBadgeDateTime(msgTimeInMilliSeconds, _taoh_user_timezone);
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
            const msgTimeInMilliSeconds = Math.floor(v.time / 1000);
            let stored_time = v.hasOwnProperty('stored_time') ? (v.stored_time).toString() : '';

            let tempChatDateTime_arr = formatBadgeDateTime(msgTimeInMilliSeconds, _taoh_user_timezone);

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

    pc_isProcessing = false;
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

function update_stored_time_in_temp_messages_pc(chat_temp_messages_key, returnedData, ptokenTo) {
    // Update stored_time in temp messages
    IntaoDB.getItem(objStores.ntw_store.name, chat_temp_messages_key).then((intaoData) => {
        if (!intaoData?.values) return;

        const updatedResponse = intaoData.values;
        const ptokenData = updatedResponse[ptokenTo];

        if (!ptokenData || !ptokenData[returnedData.sent_time]) return;

        ptokenData[returnedData.sent_time].message_id = returnedData.message_id;
        ptokenData[returnedData.sent_time].stored_time = returnedData.stored_time;

        IntaoDB.setItem(objStores.ntw_store.name, {
            taoh_ntw: chat_temp_messages_key,
            values: updatedResponse,
            timestamp: Date.now()
        });
    });
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
            throttle(fetchChatData(requestData), (pc_isProcessing ? 500 : 100));
        }

        // Update the previous scroll position to find the direction of scrolling
        prevScrollPos = currentScrollPos;
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden && displayPrivateChat) {
            initializeRequest('private_message', pc_room_key, _ntw_ft_ptoken);
        }
    });
});


function taoh_pc_post_metrics(metrics, con_token) {
    if (pToken_from?.trim() !== '') {
        save_metrics('profile', metrics, con_token);
    }
}


