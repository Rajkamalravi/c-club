
let ft_roomInfoList = {};
let ft_userInfoList = {};
let allRoomsInviteUnreadCount = 0;

let ntwMessagesETag = null;
let frmMessagesETag = null;
let frmReplyMessagesETag = null;

const messageTypeConfig = {
    ntw: {
        timestampCheckKey: 'lastNTWMsgCheckedTimestamp',
        miscKey: 'ft_ntw_networking_misc',
    },
    pc: {
        timestampCheckKey: 'lastPCMsgCheckedTimestamp',
        miscKey: 'ft_pc_networking_misc',
    }
};

var readSwitch = $('#readSwitch');

$(document).ready(function () {

    if (_ntw_ft_valid_user && _ntw_ft_ptoken && _ntw_ft_room_key) {
        // const fetchAndInitialize = (keyPrefix, timestampKey, requestType) => {
        //     const storageKey = `${keyPrefix}_${_ntw_ft_room_key}`;
        //     IntaoDB.getItem(objStores.ntw_store.name, storageKey).then((intao_data) => {
        //         const timestamp = intao_data?.last_update_time || 0;
        //         getAndSetLastCheckedTimestamp(timestampKey, timestamp, 2);
        //     }).then(() => initializeRequest(requestType, _ntw_ft_room_key, _ntw_ft_ptoken));
        // };

        // if (curr_page !== 'room') {
        //     // Initial Entry - Forum
        //     // fetchAndInitialize('ft_frm_networking_misc', 'lastFRMMsgCheckedTimestamp', 'channel');
        // }

        // if (!['room', 'profile', 'message'].includes(curr_page)) {
        //     // Initial Entry - Direct Message
        //     fetchAndInitialize('ft_ntw_networking_misc', 'lastNTWMsgCheckedTimestamp', 'direct_message');
        // }

        
    }

    if (_ntw_ft_valid_user && curr_page !== 'room' && curr_page !== 'message') {
        getNTWAllRoomsInvites();
        setInterval(function () {
            getNTWAllRoomsInvites();
        }, 5000);
    }

    setInterval(() => {        
        if (typeof pagename === 'undefined' || pagename !== 'networking') {
            syncDmRoomStamp({
                roomslug: pc_room_key,
                keyword: "dm",
                my_pToken: _ntw_ft_ptoken
            });
        }
    }, 10000);

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


/* All Rooms Message Notifications */
function updateAllInvitesReadStatus() {
        $('#allRoomsInviteUnreadCount').text('');
        $('#custom_switch').hide();
        $('#messageList').html(`<div id="no_previous_chat_all">
            <img src="${taoh_cdn_main_prefix + '/images/no-chat.svg'}" alt="no-chat">
            <div class="pt-3 pb-3">
            <p class="mb-4 fs-22">No messages yet</p>
            <p>Reach out and start a conversation to advance your career.</p>
            </div>
        </div>`);
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
    if (_ntw_ft_ptoken) {
        let my_invites_key = `invites_${_ntw_ft_ptoken}`;
        IntaoDB.getItem(objStores.ntw_store.name, my_invites_key).then((intao_data) => {
            // Check if data is expired (expires after 2 week ((14 * 24) * 60 * 60 * 1000))
            if (intao_data && intao_data.timestamp && !((Date.now() - intao_data.timestamp) > 1209600000)) {
                renderNTWAllRoomsInvites(intao_data.values);
            } else {
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

        let chatUri = `${_taoh_site_url_root}${room_info?.club?.links?.club ?? '/club/networking'}?channel_id=${invite.channel_id}`;

        const messageHtml = decodeURIComponent(invite.message.replace(/\+/g, ' '));
        const message_content = linkifyWithJQuery(messageHtml);

        const fallbackSrc = `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;
        const userAvatarSrc = await buildAvatarImage(userInfo.avatar_image, fallbackSrc);

        // read read_${invite.read}
        allInvitesHtml += `<div class="card-item rooms-invite ${new_class_side} bottom-chat-list mb-2" data-room="${invite.room_hash}" data-invitefrom="${invite.invite_from}"
                        data-chat_uri="${chatUri}" data-read="${invite.read}" data-item="${k}" style="${invite.read ? '' : 'background: rgba(50, 205, 50, 0.3)'}">
                    <div class="d-flex">
                        <div class="col-2 p-0">
                            <span data-profile_token="${invite.invite_from}" class="openProfileModal">
                                <img src="${userAvatarSrc}" alt="avatar">
                            </span>
                        </div>
                        <div class="col-10 chat-user-list-bottom">
                            <div class="d-flex">
                                <h5 class="col p-0 user-title"><span data-profile_token="${invite.invite_from}" class="openProfileModal">${userInfo.chat_name}</span></h5>
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
                <img src="${taoh_cdn_main_prefix + '/images/no-chat.svg'}" alt="no-chat">
                <div class="pt-3 pb-3">
                <p class="mb-4 fs-22">No messages yet</p>
                <p>Reach out and start a conversation to advance your career.</p>
                </div>
            </div>`);
    }
}

$(document).on('click', '#messageList .rooms-invite-content', function () {
    const rooms_invite_elem = $(this).closest('.rooms-invite');
    const room_hash = rooms_invite_elem.data('room');
    const invite_from = rooms_invite_elem.data('invitefrom');
    const chatUri = rooms_invite_elem.data('chat_uri');

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

    if(chatUri) window.open(chatUri, '_blank');
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


async function storeRepliesCount(newMessages, my_ptoken) {

    console.log("storeRepliesCount", newMessages);    

    const grouped = {};
    let replyCountModifiedKeys = [];

    // Group messages by metaId
    for (const [key, msg] of Object.entries(newMessages)) {
        const metaId = `frm_${msg.room_hash}_${msg.channel_id}`;
        if (!grouped[metaId]) grouped[metaId] = {};
        grouped[metaId][key] = msg;
    }

    const metaIds = Object.keys(grouped);
    const metas = await IntaoDB.getItems(objStores.ntw_store.name, metaIds);

    const updates = [];

    for (let i = 0; i < metaIds.length; i++) {
        const metaId = metaIds[i];
        const msgList = grouped[metaId];
        let meta = metas[i]?.values?.chat;

        console.log("msg List", msgList);        

        if (meta) {
            const parentIdToKeyMap = {}; // Build-on-demand cache
            const metaEntries = Object.entries(meta);
            const metaKeysReversed = [...metaEntries].reverse(); // recent entries first

            const domUpdates = [];

            for (const msg of Object.values(msgList)) {
                let foundKey = parentIdToKeyMap[msg.parent_id];

                if (!foundKey) {
                    // Check recent to old
                    for (const [key, parentMsg] of metaKeysReversed) {
                        parentIdToKeyMap[parentMsg.message_id] = key; // Cache as you see
                        if (parseInt(parentMsg.message_id, 10) === parseInt(msg.parent_id, 10)) {
                            foundKey = key;
                            break;
                        }
                    }
                }

                if (foundKey) {
                    meta[foundKey].reply_count = msg.reply_count;
                    meta[foundKey].like_count = msg.like_count;
                    meta[foundKey].new_reply_count = (parseInt(meta[foundKey].new_reply_count, 10) || 0) + 1;
                    replyCountModifiedKeys.push(foundKey);

                    // Push DOM update to batch
                    domUpdates.push({
                        key: foundKey,
                        reply_count: msg.reply_count,
                        like_count: msg.like_count,
                        new_reply_count: meta[foundKey].new_reply_count,
                        ptoken: msg.ptoken
                    });
                }
            }

            updates.push({
                ...metas[i],
                values: {
                    ...metas[i].values,
                    chat: meta
                }
            });

            console.log("updates", updates);
            console.log("domUpdates", domUpdates);
            

            // Batch DOM update after processing all messages in msgList
            for (const update of domUpdates) {
                const msgElem = $('#msg_' + update.key);
                if (msgElem.length) {
                    const messageReplyCount = parseInt(update.reply_count, 10) || 0;
                    const messageNewReplyCount = parseInt(update.new_reply_count, 10) || 0;
                    const messageLikeCount = parseInt(update.like_count, 10) || 0;
                    const conversationReplyCountElem = msgElem.find('.conversation-reply-count');
                    const conversationLikeCountElem = msgElem.find('.conversation-like-count');
                    conversationReplyCountElem.attr('data-count', messageReplyCount);
                    conversationLikeCountElem.attr('data-count', messageLikeCount);
                    conversationReplyCountElem.text(messageReplyCount + ' ' + (messageReplyCount === 1 ? 'reply' : 'replies'));
                    if(messageLikeCount > 0) {
                        conversationLikeCountElem.text(messageLikeCount + ' ' + (messageLikeCount === 1 ? 'like' : 'likes'));
                        conversationLikeCountElem.removeClass('d-none');
                    }                    
                    if (my_ptoken !== update.ptoken && messageNewReplyCount > 0) conversationReplyCountElem.addClass('has-new-replies');
                }
            }
        }
    }

    if (updates.length > 0) {
        try {
            await IntaoDB.setItems(objStores.ntw_store.name, updates);
        } catch (error) {
            console.error('Failed to update IndexedDB:', error);
        }
    }
}

async function storeUnreadCount(newMessages, last_update_time, my_ptoken) {
    const grouped = {};
    let unreadModifiedMeta = false;

    // Group messages by metaId
    for (const [_, msg] of Object.entries(newMessages)) {
        if(msg.ptoken === my_ptoken) continue;

        const metaId = `ntw_meta_${msg.room_hash}_${msg.channel_id}`;
        if (!grouped[metaId]) grouped[metaId] = [];
        grouped[metaId].push(msg);
    }

    const metaIds = Object.keys(grouped);
    const metas = await IntaoDB.getItems(objStores.ntw_meta_store.name, metaIds);

    const updates = [];

    for (let i = 0; i < metaIds.length; i++) {
        const metaId = metaIds[i];
        const msgList = grouped[metaId];
        let meta = metas[i] || {
            id: metaId,
            lastview: 0,
            unread: 0,
            last_update_time: 0,
            timestamp: 0
        };

        if (last_update_time > meta.last_update_time) {
            meta.last_update_time = last_update_time;
            for (const msg of msgList) {
                if ((msg.time / 1000) > meta.lastview) {
                    meta.unread += 1;

                    unreadModifiedMeta = metaId;
                }
            }
        }

        meta.timestamp = Date.now();
        updates.push(meta);
    }

    await IntaoDB.setItems(objStores.ntw_meta_store.name, updates).then(() => {
        if(unreadModifiedMeta) ntw_unreadCountModified[unreadModifiedMeta] = true;
    });
}

async function resetUnreadCount(metaId, updateLastView = 0) {
    let unreadModifiedMeta = false;
    const intaoData = await IntaoDB.getItem(objStores.ntw_meta_store.name, metaId);
    const meta = intaoData ? intaoData : {id: metaId, lastview: 0, unread: 0, last_update_time: 0, timestamp: 0};
    if(updateLastView) meta.lastview = Date.now();
    if (meta.unread) unreadModifiedMeta = metaId;
    meta.unread = 0;

    meta.timestamp = Date.now();
    await IntaoDB.setItem(objStores.ntw_meta_store.name, meta).then(() => {
        if(unreadModifiedMeta) ntw_unreadCountModified[unreadModifiedMeta] = true;
    });
}

async function getUnreadCount(metaId) {
    const meta = await IntaoDB.getItem(objStores.ntw_meta_store.name, metaId);
    return meta?.unread || 0;
}

async function updateSpeedNetworkingConnect(ntw_room_key, chatFrom, chatWith) {

    const chat_messages_key = `sn_${ntw_room_key}`;
    let intao_data = await IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key);

    const chatWith_userInfo = await getUserInfo(chatWith, 'public');

    let updatedResponse = {};
    if (intao_data?.values) {
        updatedResponse = intao_data.values;
    }
    if (!Array.isArray(updatedResponse[chatWith])) {
        updatedResponse[chatWith] = [];
    }

    const exists = updatedResponse[chatWith].find(item => item?.ptoken === chatFrom);
    if (!exists) {
        updatedResponse[chatWith].push({
            ptoken: chatFrom,
            status: 0
        });
    }

    await IntaoDB.setItem(objStores.ntw_store.name, {
        taoh_ntw: chat_messages_key,
        values: updatedResponse,
        timestamp: Date.now()
    });
    if (chatWith == my_pToken) {
    }

    if (chatFrom == my_pToken) {
    }
    console.log("--------->updated my ptoken", my_pToken);
}

async function updateConnectionRequestStatus(chatwith, chatFrom, input_status, channelId, videolink, videoname) {
    let data = {
        'taoh_action': 'taoh_ntw_speed_networking_connect_user_update',
        'key': chatFrom,
        'ptoken': chatFrom,
        'keyslug': ntw_room_key,
        'chatwith': chatwith,
        'status': input_status,
        'channel_id': channelId,
        'keyword': ntw_keyword,
        'channel_type': taohChannelSpeedNtw,
        'videolink': videolink
    };

    const response = await new Promise((resolve, reject) => {
        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'post',
            data: data,
            dataType: 'json',

            success: async function (res) {
                $('#connectModal').modal('hide');
                $('.countDownDiv').addClass('d-none');
                $('.successMatchDiv').addClass('d-none');

                $('.connect_btn').prop('disabled', false).text('Connect');
                $('.not_interested_btn').prop('disabled', false).text('Not interested');

                $('.speed_networking_div').removeClass('d-none');
                resolve({
                    status: 200,
                    success: true,
                    action: input_status
                });
            },
            error: function (xhr, status, error) {
                resolve({
                    status: 201,
                    success: false
                });
            }
        });
    });

    if (response.status === 200) {        
        if(response.action == 1) {            

            loadDmWindow(chatFrom, 1, videolink, videoname, false);            

            $('.chat-input-bottom').removeClass('d-flex').addClass('d-none');
            $('#browse_channels_wrapper').hide();
            $('#participants').hide();
            $('.speed_networking_div').addClass('d-none');
            $('.zeroday-speed').addClass('d-none');
            $('#successMatchDivHeading2').removeClass('d-none');
            $('#successMatchDivHeading1').addClass('d-none');
            $('.successMatchDiv').removeClass('d-none');
            $('.openchatacc_chat_now_btn').html("Join Now!");
            $('.openchatacc_chat_now_btn').attr("video_link", videolink);
            $('.watchPartySection').hide();
            $('.watchPartySection').removeClass('watchPartyEnabled');            

            var track_data = {
                'action': 'speed_networking_connect',
                'channel_id': channelId,
                'ptoken': my_pToken,
                'chatwith': chatwith
            };
            taoh_track_activities(track_data);

        }  else {
            updateSpeedNetworkingCarousel();
        }                     
    } else {
        console.warn("Connection request failed or no user available");
    }
    
}

async function clearCacheProcess(cacheData) {
    let cacheFilesToRemove = [];
    let lastCleanCacheProcessTime = localStorage.getItem('lastCleanCacheProcessTime') || 0;

    for (let [key, item] of Object.entries(cacheData)) {
        if (key <= lastCleanCacheProcessTime) continue;

        let alreadyProcessed = false;
        let allowCacheFilesToRemove = true;

        if (item.action === 'update_site_config') {
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

        localStorage.setItem('lastCleanCacheProcessTime', key);
    }

    // Send request to handle cacheFilesToRemove
    /*if (cacheFilesToRemove.length > 0) {
        cacheFilesToRemove = [...new Set(cacheFilesToRemove)];

        console.log('Cache files to remove:', cacheFilesToRemove);

        const cacheFormData = new FormData();
        cacheFormData.append('taoh_action', 'remove_cache_files');
        cacheFormData.append('fileNames', JSON.stringify(cacheFilesToRemove));

        try {
            const cacheResponse = await fetch(_taoh_site_ajax_url, {
                method: 'POST',
                body: cacheFormData
            });

            if (!cacheResponse.ok) {
                console.error('Cache Remove Failed Response:', cacheResponse);
            } else {
                // const cacheResult = await cacheResponse.json();
            }
        } catch (cacheError) {
            console.error('Error removing cache files:', cacheError.message || cacheError);
        }
    }*/

}

async function speedNetworkingProcess(cacheData) {
    let cacheFilesToRemove = [];
    let lastCleanSpeedNetworkingProcessTime = localStorage.getItem('lastCleanSpeedNetworkingProcessTime') || 0;

    for (let [key, item] of Object.entries(cacheData)) {
        if (key <= lastCleanSpeedNetworkingProcessTime) continue;

        let alreadyProcessed = false;
        let allowCacheFilesToRemove = true;        

        if (item.action === 'speed_networking_connect') {
            if (Object.keys(item.value).length > 0) {
                const value = item.value;
                let room_hash = value.room;
                let chat_from = value.chat_from;
                let chat_with = value.chat_with;
                updateSpeedNetworkingConnect(room_hash, chat_from, chat_with);
            }
            alreadyProcessed = true;
            allowCacheFilesToRemove = false;
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

        localStorage.setItem('lastCleanSpeedNetworkingProcessTime', key);
    }
}

function getAndSetLastCheckedTimestamp(key, lastUpdate, type = 1) {
    if (type === 2) {
        localStorage.setItem(key, lastUpdate);
    }
    return localStorage.getItem(key) || lastUpdate || 0;
}

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


/****************************======================== Networking methods ==============================*********************/
async function taohNTWMessagesFromServer(formData, needOverallResponse = false, needRequestData = {}) {
    const config = messageTypeConfig[formData.module];
    if (!config) return console.warn(`Invalid message type:`);

    const {timestampCheckKey, miscKey} = config;
    const isImmediate = parseInt(formData.immediate ?? 0, 10) || 0;

    if (!formData.ptoken || (ft_ntw_isProcessing && !isImmediate)) return;

    try {
        if(!navigator.onLine) return {status: 0, success: false, message: 'No internet connection'};

        ft_ntw_isProcessing = true;

        const response = await new Promise((resolve, reject) => {
            $.ajax({
                url: _taoh_cache_chat_url,
                type: 'GET',
                dataType: 'json',
                headers: {
                    'If-None-Match': ntwMessagesETag
                },
                data: {
                    "ops": "channel_message",
                    "action": "get_message",
                    "code": _taoh_ops_code,
                    "key": formData.ptoken, // formData.ptoken
                    "keyslug": formData.keyslug,
                    "type": formData.type,
                    // "token": ft_taoh_api_token,
                    "limit": 1000,
                    "immediate": formData.immediate ?? 0,
                    "timestamp": formData.last_update_time,
                    "cfcc10": 1
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
                            try {
                                clearCacheProcess(resultArr.cache);
                            } catch (error) {
                                console.error('Error while clearing cache:', error);
                            }
                        }

                        if (typeof resultArr.speed_networking !== 'undefined' && Object.keys(resultArr.speed_networking).length > 0) {
                            try {
                                speedNetworkingProcess(resultArr.speed_networking);
                            } catch (error) {
                                console.error('Error while clearing speed_networking:', error);
                            }
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
                // console.log('NTW response', formData.last_update_time, response.last_update_time, formData, response);
                if (formData.reupdate) return response;

                if (Object.keys(response.room_chats).length > 0) ft_ntw_reFetchRequired = false;

                let roomsInvitesAll = {};
                let ntw_promises = [];

                for (const [room_hash, room_conversations] of Object.entries(response.room_chats)) {
                    for (const [to_ptoken, room_chat] of Object.entries(room_conversations)) {
                        let chat_messages_key = `cm_${room_hash}_${formData.ptoken}_${to_ptoken}`;

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

                                await storeUnreadCount(room_chat, response.last_update_time, _ntw_ft_ptoken);

                                let room_chat_last_key = Object.keys(room_chat).pop();
                                let room_chat_last_value = room_chat[room_chat_last_key];

                                // Collecting New Invites data
                                if (room_chat_last_value.ptoken !== _ntw_ft_ptoken) {
                                    roomsInvitesAll[room_chat_last_value.time] = {
                                        room_hash: room_hash,
                                        channel_id: room_chat_last_value.channel_id,
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
                        let chat_temp_messages_key = `cm_temp_${room_hash}_${formData.ptoken}`;
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
                    const roomMiscKey = `${miscKey}_${formData.keyslug}`;
                    IntaoDB.getItem(objStores.ntw_store.name, roomMiscKey).then((intao_data) => {
                        let response_last_update_time = 0;
                        if ((intao_data && 'last_update_time' in intao_data) || formData.last_update_time == 0) {
                            response_last_update_time = response.last_update_time;
                            formData.module === 'pc' ? lastPCMsgCheckedTimestamp = response_last_update_time
                                : lastNTWMsgCheckedTimestamp = response_last_update_time;

                            IntaoDB.setItem(objStores.ntw_store.name, {
                                taoh_ntw: roomMiscKey,
                                last_update_time: response_last_update_time
                            });
                        } else {
                            formData.module === 'pc' ? lastPCMsgCheckedTimestamp = response_last_update_time
                                : lastNTWMsgCheckedTimestamp = response_last_update_time;
                        }

                        if (formData.module === 'pc') {
                            getAndSetLastCheckedTimestamp('lastPCMsgCheckedTimestamp', lastPCMsgCheckedTimestamp, 2);
                        } else {
                            getAndSetLastCheckedTimestamp('lastNTWMsgCheckedTimestamp', lastNTWMsgCheckedTimestamp, 2);
                        }
                    });


                    // Create Room Invites data
                    if (Object.keys(roomsInvitesAll).length > 0) {
                        let my_invites_key = `invites_${formData.ptoken}`;
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
        if (!document.hidden && !isImmediate) {
            // && ['room', 'profile', 'message'].includes(curr_page)
            //  && (!chat_activeTabId || chat_activeTabId === 'direct_message')
            if (formData.module === 'pc') {
                const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastPCMsgCheckedTimestamp', lastPCMsgCheckedTimestamp, 1);
                const delayms = formData.last_update_time === lastCheckedTimestamp ? 10000 : 0; // delay for 10 sec (cfcc10)
                setTimeout(() => {
                    let networkingMessagesFormData = getNetworkingMessagesFormData(formData.module, formData.keyslug, _ntw_ft_ptoken, 0,
                        getAndSetLastCheckedTimestamp('lastPCMsgCheckedTimestamp', lastPCMsgCheckedTimestamp, 1));
                    taohNTWMessagesFromServer(networkingMessagesFormData, needOverallResponse, needRequestData);
                }, (delayms + 1000));
            }

            if (formData.module !== 'pc') {
                const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastNTWMsgCheckedTimestamp', lastNTWMsgCheckedTimestamp, 1);
                const delayms = formData.last_update_time === lastCheckedTimestamp ? 10000 : 0; // delay for 10 sec (cfcc10)
                setTimeout(() => {
                    let networkingMessagesFormData = getNetworkingMessagesFormData(formData.module, formData.keyslug, _ntw_ft_ptoken, 0,
                        getAndSetLastCheckedTimestamp('lastNTWMsgCheckedTimestamp', lastNTWMsgCheckedTimestamp, 1));
                    taohNTWMessagesFromServer(networkingMessagesFormData, needOverallResponse, needRequestData);
                }, (delayms + 1000));
            }
        }
    }
}

function getNetworkingMessagesFormData(module, keyslug, ptoken, immediate = 0, last_update_time) {
    return {
        type: ft_taoh_chat_network,
        module,
        keyslug,
        ptoken,
        immediate,
        last_update_time
    };
}

/****************************======================== /Networking methods ==============================*********************/


/****************************======================== Forum methods ==============================*********************/
async function taohFRMMessagesFromServer(formData, needOverallResponse = false, needRequestData = {}) {
    const isReplyRequest = Boolean(parseInt(formData.parent_id, 10));
    const isImmediate = parseInt(formData.immediate ?? 0, 10) || 0;

    if (!formData.ptoken || (isReplyRequest && ft_frm_reply_isProcessing && !isImmediate)) {
        return;
    } else if (!formData.ptoken || (!isReplyRequest && ft_frm_isProcessing && !isImmediate)) {
        return;
    }

    try {
        if(!navigator.onLine) return {status: 0, success: false, message: 'No internet connection'};

        isReplyRequest ? ft_frm_reply_isProcessing = true : ft_frm_isProcessing = true;

        const response = await new Promise((resolve, reject) => {
            $.ajax({
                url: _taoh_cache_chat_url,
                type: 'GET',
                dataType: 'json',
                headers: {
                    'If-None-Match': isReplyRequest ? frmReplyMessagesETag : frmMessagesETag
                },
                data: {
                    "ops": "channel_message",
                    "action": "get_message",
                    "code": _taoh_ops_code,
                    "key": formData.ptoken, // formData.ptoken
                    "keyslug": formData.keyslug,
                    "parent_id": formData.parent_id ?? 0,
                    "type": formData.type,
                    "limit": 1000,
                    "immediate": formData.immediate ?? 0,
                    "timestamp": formData.last_update_time,
                    "cfcc10": 1
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
                        const reupdateRoomHash = formData.reupdate ?? false;
                        for (const [key, item] of Object.entries(resultArr.chats)) {
                            if(!item || !item.room_hash || !item.channel_id) continue;

                            const roomHash = item.room_hash;
                            const channelId = item.channel_id;

                            if (reupdateRoomHash && roomHash !== reupdateRoomHash) continue;

                            if (!grpChatsArr[roomHash]) grpChatsArr[roomHash] = {};
                            if (!grpChatsArr[roomHash][channelId]) grpChatsArr[roomHash][channelId] = {};

                            grpChatsArr[roomHash][channelId][key] = item;
                        }

                        const response_data = {
                            status: 200,
                            success: resultArr.success,
                            room_chats: grpChatsArr,
                            last_update_time: Math.round(resultArr.last_update_time),
                        };

                        // Both request taohFRMMessagesFromServer and taohNTWMessagesFromServer running so commented
                        // Clear cache if any cache data found
                        /*if (typeof resultArr.cache !== 'undefined' && Object.keys(resultArr.cache).length > 0) {
                            try {
                                clearCacheProcess(resultArr.cache);
                            } catch (error) {
                                console.error('Error while clearing cache:', error);
                            }
                        }*/

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
                // console.log(`FRM ${isReplyRequest?'reply ':''}response`, formData.last_update_time, response.last_update_time, formData, response);
                if (formData.reupdate) return response;

                if (Object.keys(response.room_chats).length > 0) {
                    ft_frm_reFetchRequired = false;
                }

                let roomsInvitesAll = {};
                let ntw_promises = [];
                let replyCountUpdatedKeys = {};

                for (const [room_hash, room_channels] of Object.entries(response.room_chats)) {
                    for (const [channel_id, room_chat] of Object.entries(room_channels)) {
                        let chat_messages_key = `frm_${room_hash}_${channel_id}${isReplyRequest ? '_' + formData.parent_id : ''}`;

                        // Create and update empty response in indexed db if not exist
                        let chat_messages_key_count = await IntaoDB.checkKeyExists(objStores.ntw_store.name, chat_messages_key);
                        if (!chat_messages_key_count) {
                            IntaoDB.setItem(objStores.ntw_store.name, {
                                taoh_ntw: chat_messages_key,
                                values: {
                                    "chat": {},
                                    "last_update_time": response.last_update_time,
                                    "success": true
                                },
                                timestamp: Date.now()
                            });
                        }

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

                                await storeUnreadCount(room_chat, response.last_update_time, _ntw_ft_ptoken);

                                if (isReplyRequest) await storeRepliesCount(room_chat, _ntw_ft_ptoken);

                                let room_chat_last_key = Object.keys(room_chat).pop();
                                let room_chat_last_value = room_chat[room_chat_last_key];

                                // Collecting New Invites data
                                if (room_chat_last_value.ptoken !== _ntw_ft_ptoken) {
                                    roomsInvitesAll[room_chat_last_value.time] = {
                                        room_hash: room_hash,
                                        channel_id: room_chat_last_value.channel_id,
                                        invite_from: room_chat_last_value.ptoken,
                                        ptoken_from: room_chat_last_value.ptoken,
                                        ptoken_to: room_chat_last_value.to_ptoken,
                                        message: room_chat_last_value.message,
                                        message_id: room_chat_last_value.message_id,
                                        message_time: room_chat_last_value.time,
                                        read: room_chat_last_value.read || 0
                                    };
                                }


                                resolve();
                            })
                        );

                        // Clearing Temp messages in Indexed DB when that msg updated from server
                        let chat_temp_messages_key = `frm_temp_${room_hash}_${channel_id}${isReplyRequest ? '_' + formData.parent_id : ''}`;
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

                    }
                }

                await Promise.all(ntw_promises).then(() => {
                    let chat_networking_misc_key = `ft_frm_networking_misc_${formData.keyslug}${isReplyRequest ? '_' + formData.parent_id : ''}`;
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


                    // Create Room Invites data
                    if (Object.keys(roomsInvitesAll).length > 0) {
                        let my_invites_key = `invites_${_ntw_ft_ptoken}`;
                        // let my_invites_key = isReplyRequest ? `frm_invites_${formData.keyslug}_${formData.parent_id}` : `frm_invites_${formData.keyslug}`;
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
        if (!document.hidden && !isImmediate) {
            // && curr_page === 'room'
            //  && (!chat_activeTabId || chat_activeTabId === 'channel')
            if (frm_reply_view && isReplyRequest && parseInt(formData.parent_id, 10) === parseInt(frm_message_id, 10)) {
                const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastFRMReplyMsgCheckedTimestamp', lastFRMReplyMsgCheckedTimestamp, 1);
                const delayms = formData.last_update_time === lastCheckedTimestamp ? 10000 : 0; // delay for 10 sec (cfcc10)
                setTimeout(() => {
                    let forumMessagesFormData = getForumMessagesFormData(formData.keyslug, _ntw_ft_ptoken, formData.parent_id, 0,
                        getAndSetLastCheckedTimestamp('lastFRMReplyMsgCheckedTimestamp', lastFRMReplyMsgCheckedTimestamp, 1));
                    taohFRMMessagesFromServer(forumMessagesFormData, needOverallResponse, needRequestData);
                }, (delayms + 1000));
            }

            if (!isReplyRequest) {
                const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastFRMMsgCheckedTimestamp', lastFRMMsgCheckedTimestamp, 1);
                const delayms = formData.last_update_time === lastCheckedTimestamp ? 10000 : 0; // delay for 10 sec (cfcc10)
                setTimeout(() => {
                    let forumMessagesFormData = getForumMessagesFormData(formData.keyslug, _ntw_ft_ptoken, formData.parent_id, 0,
                        getAndSetLastCheckedTimestamp('lastFRMMsgCheckedTimestamp', lastFRMMsgCheckedTimestamp, 1));
                    taohFRMMessagesFromServer(forumMessagesFormData, needOverallResponse, needRequestData);
                }, (delayms + 1000));
            }
        }
    }
}

function getForumMessagesFormData(keyslug, ptoken, parent_id, immediate = 0, last_update_time = 0) {
    return {
        "type": ft_taoh_chat_forum,
        keyslug,
        ptoken,
        parent_id,
        immediate,
        last_update_time
    };
}

/****************************======================== /Forum methods ==============================*********************/


/****************************======================== Helper Functions ==============================*********************/

function paginateChat(chatObj, cursor = null, direction = 'after', limit = 20) {
    if (!chatObj || typeof chatObj !== 'object') return {
        data: {}, total: 0, hasMore: false, nextCursor: null, prevCursor: null, firstCursor: null
    };

    const sortedKeys = Object.keys(chatObj).sort((a, b) => Number(a) - Number(b));
    const total = sortedKeys.length;

    let startIdx = 0, endIdx = total;
    let hasMore = false;
    let firstKey = total > 0 ? sortedKeys[0] : null;
    let sliceKeys = [];

    if (cursor !== null) {
        const cursorIndex = sortedKeys.indexOf(String(cursor));
        if (cursorIndex === -1) return {
            data: {}, total, hasMore: false, nextCursor: null, prevCursor: null, firstCursor: firstKey
        };

        if (direction === 'before') {
            endIdx = cursorIndex;
            startIdx = Math.max(0, endIdx - limit);
            hasMore = startIdx > 0;
        } else if (direction === 'after') {
            startIdx = cursorIndex + 1;
            endIdx = Math.min(total, startIdx + limit);
            hasMore = endIdx < total;
        }

        sliceKeys = sortedKeys.slice(startIdx, endIdx);
    } else {
        // No cursor: load last `limit` messages
        startIdx = Math.max(0, total - limit);
        sliceKeys = sortedKeys.slice(startIdx, total);
        hasMore = startIdx > 0;
    }

    const data = Object.fromEntries(sliceKeys.map(key => [key, chatObj[key]]));

    return {
        data,
        total,
        hasMore,
        nextCursor: sliceKeys.length > 0 ? sliceKeys[sliceKeys.length - 1] : null,
        prevCursor: sliceKeys.length > 0 ? sliceKeys[0] : null,
        firstCursor: firstKey
    };
}

async function syncDmRoomStamp({roomslug, keyword, my_pToken}) {

    const res = await getRoomStamp({roomslug, keyword, my_pToken}, true, false);
    const requestData = res?.requestData ?? {};
    let roomStamp = res?.response ?? {};

    const ntwRoomStamp = ['room', requestData.keyword, requestData.roomslug, 'stamp']
        .filter(Boolean).join('_');
    const store = objStores.ntw_store.name;

    const existing = await IntaoDB.getItem(store, ntwRoomStamp);

    // Once read existing, always overwrite with fresh
    await IntaoDB.setItem(store, { taoh_ntw: ntwRoomStamp, values: roomStamp, timestamp: Date.now() });

    const oldStamp = parseJSONSafely(existing?.values);
    const dmChanged = !oldStamp || oldStamp.dm !== roomStamp?.dm;

    if (dmChanged) {
        console.log("DM Chnaged");        
        stopChannelUpdate = false;
        const dmBeforeTimestamp = parseInt(oldStamp?.dm || 0, 10);
        const freshDMStamp = await getRoomChannelStamp({
            roomslug,
            keyword,
            channel_type: taohChannelDmMsg,
            timestamp: dmBeforeTimestamp,
            my_pToken
        }, true, false);

        console.log("DM Chnaged response:", freshDMStamp);

        //console.log('Fetched fresh room dm stamp:', freshDMStamp);
        for (const channelId in freshDMStamp.response) {
            if (stopChannelUpdate) {
                console.log("CHANNEL UPDATE LOOP STOPPED (USER SWITCHED CHANNEL)");
                break;
            }
            // get and update channel last fetch timestamp in intaodb
            const channelLastStamp = freshDMStamp.response[channelId];
            const channelKey = ['channel', channelId].filter(Boolean).join('_');
            const channelExisting = await IntaoDB.getItem(store, channelKey);      
            console.log("channelExisting channelLastStamp", channelExisting, channelLastStamp);

            const payload = {
                ...(channelExisting && typeof channelExisting === 'object' ? channelExisting : {}),
                taoh_ntw: channelKey,
                last_noti_read_stamp: channelLastStamp,
            };
            await IntaoDB.setItem(store, payload);

            let hasUpdates = Number(channelLastStamp) > Number(channelExisting?.last_noti_read_stamp ?? 0);
            console.log("channelExisting channelLastStamp hasUpdates", hasUpdates);

            if(hasUpdates) {
                getDmMessages(roomslug, keyword, channelId, channelExisting?.last_noti_read_stamp ?? 0, my_pToken);                
            }
           
        }
    }

    return { requestData, ntwRoomStamp, roomStamp };
}

async function getDmMessages(roomslug, ntw_keyword, channel_id, lastTimestamp, my_pToken) {

    return new Promise((resolve, reject) => {
        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'post',
            data: {
                'roomslug': roomslug,
                'taoh_action': 'taoh_ntw_get_messages',
                'channel_id': channel_id,
                'keyword': ntw_keyword,
                'channel_type': taohChannelDmMsg,
                'last_message_id': 0,
                'last_timestamp': lastTimestamp,
                'key': my_pToken
            },
            dataType: 'json',
            success: async function (res) {
                try {
                    let unreadCount = parseInt($('#allRoomsInviteUnreadCount').text(), 10) || 0;
                    if(Array.isArray(res) && res.length > 0) {
                        
                        $('#messageList #no_previous_chat_all').remove();
                        $('#custom_switch').show();
                        
                        for (const msg of res) {
                            let timestamp = msg?.timestamp;
                            if (timestamp) {
                                let date = new Date(msg.timestamp);
                                let timeString = date.toLocaleTimeString('en-US', {
                                    hour: 'numeric',
                                    minute: '2-digit',
                                    hour12: true
                                });
                                var chatInfo = await getUserInfo(msg.ptoken, 'public');
                                if (chatInfo.avatar_image != '' && chatInfo.avatar_image != undefined) {
                                    var avatar_image = chatInfo.avatar_image;
                                } else if (chatInfo.avatar != undefined && chatInfo.avatar != 'default') {
                                    var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/' + chatInfo.avatar + '.png';
                                } else {
                                    var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/avatar_def.png';
                                }
                                var chat_name = chatInfo.chat_name;

                                if(msg.ptoken == my_pToken) {
                                    continue;
                                }                                
                                if ($(`#messageList .card-item[data-msg-key="${msg.timestamp}"]`).length === 0) {

                                    const existingMsg = $(`#messageList .card-item[data-invitefrom="${msg.ptoken}"]`);
                                    const messageHtml = `
                                    <div class="card-item dm-card-item rooms-invite bottom-chat-list mb-2"
                                        data-msg-key="${msg.timestamp}"
                                        data-invitefrom="${msg.ptoken}">
                                        <div class="d-flex">
                                            <div class="col-2 p-0">
                                                <span data-profile_token="${msg.ptoken}" class="openProfileModal">
                                                    <img src="${avatar_image}" alt="avatar">
                                                </span>
                                            </div>
                                            <div class="col-10 chat-user-list-bottom">
                                                <div class="d-flex">
                                                    <h5 class="col p-0 user-title">
                                                        <span data-profile_token="${msg.ptoken}" class="openProfileModal">
                                                            ${chat_name}
                                                        </span>
                                                    </h5>
                                                    <p class="chat-date-time-count">
                                                        <span>${timeString}</span>
                                                    </p>
                                                </div>
                                                <div class="rooms-invite-content">
                                                    <p class="card-text" style="color:#0d233e">${msg.text}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    `;

                                    if (existingMsg.length > 0) {
                                        existingMsg.replaceWith(messageHtml);
                                    } else {
                                        // ➕ Add new message
                                        $('#messageList').append(messageHtml);
                                        unreadCount += 1;
                                    }                                   
                                }
                            }
                        }    
                        if(unreadCount > 0) {
                            $('#allRoomsInviteUnreadCount').text(unreadCount);
                        } else {
                            $('#allRoomsInviteUnreadCount').text('');
                        }                                                                  
                    }
                    resolve({
                        status: 200,
                        success: true,
                        response: res
                    });
                } catch (err) {
                    console.error("Error getting messages:", err);
                    reject(err);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching messages:", error);
                reject({
                    status: 201,
                    success: false,
                    error: error
                });
            }
        });
    });
}

function initializeRequest(chat_window, room_hash, my_pToken) {
    
}

/****************************======================== /Helper Functions ==============================*********************/


function linkifyText(text) {
    const urlRegex = /(\bhttps?:\/\/[^\s]+)/gi;

    return text.replace(urlRegex, function(url) {
        return `<a href="${url}" target="_blank" rel="noopener noreferrer">${url}</a>`;
    });
}

function linkifyWithJQuery(html, type = '') {
    const $container = $('<div>').html(html);

    function linkifyNode(node) {
        if (node.nodeType === 3) { // Text node
            const urlRegex = /(\b(?:https?:\/\/|www\.|(?:[a-zA-Z0-9-]+\.)+[a-zA-Z]{2,})(?:[^\s<]*)\b)/gi;
            const parts = node.nodeValue.split(urlRegex);

            const newNodes = $.map(parts, function (part) {
                if (urlRegex.test(part)) {
                    let href = part;
                    if (!/^https?:\/\//i.test(href)) {
                        href = 'http://' + href;
                    }
                    var classvalue = '';
                    var text = part;
                    if (type == 'video') {
                        classvalue = "chat-meeting-link";//Join video meeting
                        text = 'Join video meeting';
                    }
                    return $('<a>', {
                        href: href,
                        text: text,
                        target: '_blank',
                        class: classvalue,
                        rel: 'noopener noreferrer'
                    })[0];
                } else {
                    return document.createTextNode(part);
                }
            });

            $(node).replaceWith(newNodes);
        } else if (node.nodeType === 1 && node.tagName.toLowerCase() !== 'a') {
            $(node).contents().each(function () {
                linkifyNode(this);
            });
        }
    }

    $container.contents().each(function () {
        linkifyNode(this);
    });

    return $container.html();
}

function linkifyWithJQuery1(html) {
    // Wrap the HTML in a temporary container
    const $container = $('<div>').html(html);

    // Recursive function to process text nodes
    function linkifyNode(node) {
        if (node.nodeType === 3) { // Text node
            //const urlRegex = /(\bhttps?:\/\/[^\s<]+)/gi;
            const urlRegex = /(\b(?:https?:\/\/|www\.|(?:[a-zA-Z0-9-]+\.)+[a-zA-Z]{2,})(?:[^\s<]*)\b)/g;
            const parts = node.nodeValue.split(urlRegex);
            const newNodes = $.map(parts, function(part) {
                if (urlRegex.test(part)) {
                    return $('<a>', {
                        href: part,
                        text: part,
                        target: '_blank'
                    })[0];
                } else {
                    return document.createTextNode(part);
                }
            });
            $(node).replaceWith(newNodes);
        } else if (node.nodeType === 1 && node.tagName.toLowerCase() !== 'a') {
            $(node).contents().each(function() {
                linkifyNode(this);
            });
        }
    }

    // Start processing
    $container.contents().each(function() {
        linkifyNode(this);
    });

    return $container.html();
}


// watch party collapse height adjustment in side bars
$(document).ready(function () {
    // Delay initial check slightly
    setTimeout(function () {
        if ($('#collapseVideo').hasClass('show')) {
            $('.chat-layout .chat-leftsidebar').addClass('adjust-height');
            $('.chat-layout .user-profile-desc').addClass('adjust-height');
            $('.chat-layout .user-profile-sidebar').addClass('adjust-height');
            $('.chat-layout .channelData-sidebar').addClass('adjust-height');
            $('.chat-layout .chat-conversation').addClass('adjust-height');
        }
    }, 0); // 0 ms delay to let Bootstrap finish DOM setup

    // add class
    $('#collapseVideo').on('shown.bs.collapse', function () {
        $('.chat-layout .chat-leftsidebar').addClass('adjust-height');
        // $('.chat-layout .user-profile-desc').addClass('adjust-height');
        $('.chat-layout .user-profile-sidebar').addClass('adjust-height');
        $('.chat-layout .channelData-sidebar').addClass('adjust-height');
        $('.chat-layout .chat-conversation').addClass('adjust-height');
    });
    // remove class
    $('#collapseVideo').on('hidden.bs.collapse', function () {
        $('.chat-layout .chat-leftsidebar').removeClass('adjust-height');
        // $('.chat-layout .user-profile-desc').removeClass('adjust-height');
        $('.chat-layout .user-profile-sidebar').removeClass('adjust-height');
        $('.chat-layout .channelData-sidebar').removeClass('adjust-height');
        $('.chat-layout .chat-conversation').removeClass('adjust-height');
    });
});

$(document).ready(function () {
    if ($('#accordion.watch-party').length > 0) {
        $('.chat-layout .chat-conversation').addClass('wp-hht');
    }
});

$(document).ready(function () {

    // Toggle sidebar open/close on button click
    $('.btn.wp-en').on('click', function () {
        $('.chat-leftsidebar.watchPartyEnabled').toggleClass('open');
    });

    $('.dm-card-item').on('click', function () {
        let invitefrom = $(this).attr('data-invitefrom');
        location.href = _taoh_site_url_root + '/dm/networking';
    });

});


