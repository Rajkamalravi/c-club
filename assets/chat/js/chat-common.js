
$('#tourbutton').on('click', function () {
    initNetworkingTour(true);
});

$(document).on('click', '.copy-message', function () {
    const parentElem = $(this).closest('.chat-list');
    const copiedMessage = parentElem.find('.ctext-content').text().trim();

    if (copiedMessage) {
        navigator.clipboard.writeText(copiedMessage).then(() => {
            const copiedNotifyElemId = (chatWindow === 'channel') ? 'copyClipBoardChannel' : 'copyClipBoard';
            const notifyElem = $('#' + copiedNotifyElemId);

            if (notifyElem.length) {
                notifyElem.addClass('show');

                setTimeout(() => {
                    notifyElem.removeClass('show');
                }, 2000);
            }
        }).catch(err => {
            console.error('Failed to copy text: ', err);
        });
    }
});

$(document).on('click', '.pin-message', function () {
    const parentElem = $(this).closest('.chat-list');
    const thisElem = $(this);
    let frmMessageId = parentElem.data('frm_message_id');
    let frmMessageKey = parentElem.data('frm_message_key');
    let channelId = $('#channel-chat').data('channel_id');
    let msgOwner = thisElem.data('msg-owner');
    
    var pinned_count = $('.pin_message_div'+channelId+' .pin_msg').length;    


    if(pinned_count >= 3 && thisElem.attr('data-action') == 1) {        
        jq_confirm_unpin(
            'Replace oldest pin?',
            'Your new pin will replace the oldest one',
            'orange',
            'Continue',
            function () {
                pinMessagePost(msgOwner, thisElem, channelId, frmMessageId, frmMessageKey, thisElem.attr('data-action'), "channel", "", 1);
            },
            true
        );      
    } else {    
        pinMessagePost(msgOwner, thisElem, channelId, frmMessageId, frmMessageKey, thisElem.attr('data-action'), "channel", "");
    }    
});

$(document).on('click', '.unpin-message', function () {
    let frmMessageKey = $(this).data('frm_message_key');
    let frmMessageId = $(this).data('frm_message_id');
    let chatWith = $(this).data('chatwith');
    let pinFrom = $(this).data('type');
    let elem = undefined;
    let channelId;
    if(pinFrom == "dm") {
        channelId = $('#user-chat').data('channel_id');
        chatWith = $('#users-chat').data('chatwith');
    } else {
        channelId = $('#channel-chat').data('channel_id');
    }
    let msgOwner = $(this).data('msg-owner');
    //alert(chatWith);
    pinMessagePost(msgOwner, elem, channelId, frmMessageId, frmMessageKey, 0, pinFrom, chatWith);       
});

$(document).on('click', '.view-profile-pin', function () {
    let userPtoken = $(this).data('user-ptoken');
    alert(userPtoken);   
});

$(document).on('click', '.pin-message-dm', function () {
    const parentElem = $(this).closest('.chat-list');
    const thisElem = $(this);
    let frmMessageId = parentElem.data('ntw_message_id');
    let frmMessageKey = parentElem.data('ntw_message_key');
    let channelId = $('#users-chat').data('channel_id');
    let chatWith = $('#users-chat').data('chatwith');    
    let msgOwner = thisElem.data('msg-owner');

    var pinned_count = $('.pin_message_div-dm'+channelId+' .pin_msg').length;    

    if(pinned_count >= 3 && thisElem.attr('data-action') == 1) {
        jq_confirm_unpin(
            'Replace oldest pin?',
            'Your new pin will replace the oldest one',
            'orange',
            'Continue',
            function () {
                pinMessagePost(msgOwner, thisElem, channelId, frmMessageId, frmMessageKey, thisElem.attr('data-action'), "dm", chatWith, 1);
            },
            true
        );      
    } else {    
        pinMessagePost(msgOwner, thisElem, channelId, frmMessageId, frmMessageKey, thisElem.attr('data-action'), "dm", chatWith);
    }    
});

$(document).on('click', '.conversation-mention', async function () {
    const ptoken = $(this).attr('data-ptoken');

    if (ptoken) {
        // const profileUrl = `${_taoh_site_url_root}/profile/${ptoken}`;
        // window.open(profileUrl, '_blank');

        const [userLiveStatus, userInfo] = await Promise.all([
            getUserLiveStatus(ptoken).catch((e) => {console.log(e)}),
            getUserInfo(ptoken, 'full').catch((e) => {console.log(e)}),
        ]);

        await updateProfileInfo(userInfo, userLiveStatus);
    }
});

// Open profile side bar on click on channel user
$(document).on('click', '.open_profile_sidebar', async function () {
    chatwith = $(this).attr('data-chatwith');
    const [userLiveStatus, userInfo] = await Promise.all([
        getUserLiveStatus(chatwith).catch((e) => {console.log(e)}),
        getUserInfo(chatwith, 'full').catch((e) => {console.log(e)}),
    ]);

    console.log("user info comm", userInfo);    

    await updateProfileInfo(userInfo, userLiveStatus);

    selectedChannel = '';
    loadRightSidebar('profile');
});

$(document).on('click', '.user-chat-remove', function () {
    $('#user-chat').removeClass('user-chat-show');
    // $('#user-chat').addClass('mobile-transform');
});

const userProfileSidebar = document.querySelector(".user-profile-sidebar");
/*document.querySelectorAll(".user-profile-show").forEach(function (e) {
    e.addEventListener("click", function (e) {
        userProfileSidebar.classList.toggle("d-block")
    })
});*/
$(document).on('click', '#user-profile-sidebar .user-profile-show', function () {
    userProfileSidebar.style.display = "none";
});

const userProfileSidebarToggle = document.querySelector("#user-profile-sidebar");
if (userProfileSidebarToggle) {
    document.addEventListener("click", function (e) {
        if (e.target.closest(".user-profile-sidebar-show")) {
            userProfileSidebarToggle.style.display = userProfileSidebarToggle.style.display === "block" ? "none" : "block";
        }
    });
}

const chatReplySidebar = document.querySelector("#chat-reply-sidebar");
if (chatReplySidebar) {
    document.addEventListener("click", function (e) {
        if (e.target.closest(".chat-reply-show")) {
            loadRightSidebar('members');
            initializeRequest(chatWindow, ntw_room_key, my_pToken);
        }
    });
}

const channelSidebar = document.querySelector("#channelData-sidebar");
if (channelSidebar) {
    document.addEventListener("click", function (e) {
        if (e.target.closest(".channel-sidebar-show")) {
            channelSidebar.style.display = channelSidebar.style.display === "block" ? "none" : "block";
        }
    });
}

const participantsSidebar = document.querySelector("#participants-sidebar");
if (participantsSidebar) {
    document.addEventListener("click", function (e) {
        if (e.target.closest(".networking-sidebar-show")) {
            // participantsSidebar.classList.toggle("d-block")
            participantsSidebar.style.display = participantsSidebar.style.display === "block" ? "none" : "block";
        }
    });
}

const chatRoomList = document.querySelector(".chat-room-list");
if (chatRoomList) {
    document.addEventListener("click", function (e) {
        if (e.target.closest(".chatlist-sidebar-show")) {

            const participantsSidebar = document.querySelector("#participants-sidebar");
            const userChat = document.getElementById('user-chat');
            
            if(!userChat.classList.contains('user-chat-show')) {
                 userChat.classList.add('user-chat-show');
            }
            if(userChat.classList.contains('mobile-transform')) {
                 userChat.classList.remove('mobile-transform');
            }
            
            participantsSidebar.style.display = participantsSidebar.style.display === "block" ? " " : "block";
        }
    });
}




let prev_userLiveStatusInterval = userLiveStatusInterval;
function userLiveStatusUpdate(interval) {
    userLiveIntervalId = setInterval(function() {
        if (ntw_room_key.trim() !== '' && chatwith.trim() !== '' && typeof getUserLiveStatus === 'function') {
            getUserLiveStatus(chatwith).then((userLiveStatus) => {
                if (userLiveStatus.success && chatwith != sidekick_ptoken) {
                    chatwith_liveStatus = Boolean(userLiveStatus.output) ? 1 : 0;

                    const userChatTopbarInfo = $('#user-chat-topbar-info');
                    if (chatwith_liveStatus) {
                        userChatTopbarInfo.find('.chat-user-img').addClass('online').removeClass('away');
                        userChatTopbarInfo.find('.user-status-text').text('Online');

                        $('.user-profile-status').html('<i class="bx bxs-circle fs-10 text-success me-1 ms-0"></i> Online');
                    } else {
                        userChatTopbarInfo.find('.chat-user-img').addClass('away').removeClass('online');
                        userChatTopbarInfo.find('.user-status-text').text('Away');
                        $('.user-profile-status').html('<i class="bx bxs-circle fs-10 text-warning me-1 ms-0"></i> Away');
                    }

                    if (document.visibilityState === 'visible') userLiveStatusInterval = chatwith_liveStatus ? 60000 : 120000; // 1 minute : 2 minutes
                    if (prev_userLiveStatusInterval !== userLiveStatusInterval) {
                        prev_userLiveStatusInterval = userLiveStatusInterval;
                        clearInterval(userLiveIntervalId);
                        userLiveStatusUpdate(userLiveStatusInterval);
                    }
                }
            });
        }
    }, interval);
}


document.addEventListener('DOMContentLoaded', () => {
    // For One to One
    if (!SimpleBar.instances.has(ntw_chatContainer)) {
        new SimpleBar(ntw_chatContainer);
    }
    const ntw_simplebarInstance = SimpleBar.instances.get(ntw_chatContainer);
    if (ntw_simplebarInstance) {
        const scrollElement = ntw_simplebarInstance.getScrollElement();
        let ntw_prevScrollPos = scrollElement.scrollTop;

        scrollElement.addEventListener('scroll', () => {
            const ntw_currentScrollPos = scrollElement.scrollTop;

            if ((ntw_currentScrollPos < ntw_prevScrollPos) && ntw_currentScrollPos < 10 && !ntw_msgScrollUpEnded) {
                ntwChatPageNo++;
                awloader(document.getElementById('ntwChatConversationLoader'), 'show');
                const requestData = getNTWChatRequestData(my_pToken, chatwith, 'scrollup');
                throttle(fetchNTWChatData(requestData, false), (ntw_isProcessing ? 500 : 100));
            }

            // updateStickyBadgeTxt('#users-conversation-list', '#stickyBadge');

            ntw_prevScrollPos = ntw_currentScrollPos;
        });
    }


    // For Channel
    if (!SimpleBar.instances.has(frm_chatContainer)) {
        new SimpleBar(frm_chatContainer);
    }
    const frm_simplebarInstance = SimpleBar.instances.get(frm_chatContainer);
    if (frm_simplebarInstance) {
        const scrollElement = frm_simplebarInstance.getScrollElement();
        let frm_prevScrollPos = scrollElement.scrollTop;

        scrollElement.addEventListener('scroll', () => {
            const frm_currentScrollPos = scrollElement.scrollTop;

            if ((frm_currentScrollPos < frm_prevScrollPos) && frm_currentScrollPos < 10 && !frm_msgScrollUpEnded) {
                frmChatPageNo++;
                awloader(document.getElementById('frmChatConversationLoader'), 'show');
                const requestData = getFRMChatRequestData(my_pToken, chatwith, 'scrollup');
                throttle(fetchFRMChatData(requestData, false), (frm_isProcessing ? 500 : 100));
            }

            // updateStickyBadgeTxt('#channel-conversation-list', '#frm_stickyBadge');

            frm_prevScrollPos = frm_currentScrollPos;
        });
    }


    // For Channel Reply
    if (!SimpleBar.instances.has(frm_reply_chatContainer)) {
        new SimpleBar(frm_reply_chatContainer);
    }
    const frm_reply_simplebarInstance = SimpleBar.instances.get(frm_reply_chatContainer);
    if (frm_reply_simplebarInstance) {
        const scrollElement = frm_reply_simplebarInstance.getScrollElement();
        let frm_reply_prevScrollPos = scrollElement.scrollTop;

        scrollElement.addEventListener('scroll', () => {
            const frm_reply_currentScrollPos = scrollElement.scrollTop;

            if ((frm_reply_currentScrollPos < frm_reply_prevScrollPos) && frm_reply_currentScrollPos < 10 && !frm_reply_msgScrollUpEnded) {
                frmReplyChatPageNo++;
                // taohLoader(document.getElementById('frm_reply_chat_loader'), true);
                const requestData = getFRMChatRequestData(my_pToken, chatwith, 'scrollup');
                throttle(fetchFRMReplyChatData(requestData, false), (frm_reply_isProcessing ? 500 : 100));
            }

            // updateStickyBadgeTxt('#chat-reply-conversation', '#frm_reply_stickyBadge');

            frm_reply_prevScrollPos = frm_reply_currentScrollPos;
        });
    }

    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            if (layout != 2 && "participants" === chatWindow) taoh_load_network_entries();

            initializeRequest('channel', ntw_room_key, my_pToken);
            initializeRequest('direct_message', ntw_room_key, my_pToken);
        }

        // User Live Status Update
        userLiveStatusInterval = document.hidden ? 300000: 1000; // 5 minutes : 1 second
        prev_userLiveStatusInterval = userLiveStatusInterval;
        if(userLiveIntervalId) clearInterval(userLiveIntervalId);
        userLiveStatusUpdate(userLiveStatusInterval);

        stopTitleBlinking();
    });

    /* At mentions */
    const tribute = new Tribute({
        noMatchTemplate: () => null,
        values: async (text, cb) => {
            try {
                if (chatWindow !== 'channel') return cb([]);

                const query = text.trim().toLowerCase();
                const ntwChannelsKey = `ntw_channels_${ntw_room_key}`;
                const channelData = await IntaoDB.getItem(objStores.ntw_store.name, ntwChannelsKey);
                const channelList = channelData?.values?.channels || {};

                const channelId = $('#channel-chat').data('channel_id');
                if (!channelId || !channelList[channelId]) return cb([]);

                const memberTokens = channelList[channelId].members || [];
                const members = [];

                for (const token of memberTokens) {
                    // if (token === my_pToken) continue; // Skip self

                    const user = await getUserInfo(token, 'public');
                    if (user) {
                        const fallbackAvatar = `${_taoh_ops_prefix}/avatar/PNG/128/${user.avatar || 'default'}.png`;
                        const avatarSrc = await buildAvatarImage(user.avatar_image, fallbackAvatar);
                        members.push({
                            ptoken: user.ptoken,
                            key: user.chat_name,
                            value: user.chat_name,
                            img: avatarSrc
                        });
                    }
                }

                const filtered = query
                    ? members.filter(m => m.key.toLowerCase().includes(query))
                    : members;

                cb(filtered);
            } catch (error) {
                console.error('Tribute async values error:', error);
                cb([]);
            }
        },
        menuItemTemplate: (item) => {
            return `<div class="menu-mention-item">
                        <img src="${item.original.img}" alt="${item.original.key}" class="menu-mention-avatar">
                        <span class="menu-mention-name">${item.original.key}</span>
                    </div>`;
        },
    });

    const editor = document.getElementById('chat_input');
    if (editor) {
        tribute.attach(editor);
        editor.addEventListener('tribute-replaced', e => {
            const selected = e.detail.item.original;
            if (selected?.value && selected?.ptoken) {
                mentionUserArray[selected.value] = selected.ptoken;
            }
        });
    }

    const replyEditor = document.getElementById('chat_reply_input');
    if (replyEditor) {
        tribute.attach(replyEditor);
        replyEditor.addEventListener('tribute-replaced', e => {
            const selected = e.detail.item.original;
            if (selected?.value && selected?.ptoken) {
                mentionUserArray[selected.value] = selected.ptoken;
            }
        });
    }

    /* /At mentions */
});

function loadchatWindow(chat_window = '', data = false) {

    console.log('-----chat_window------', chat_window);
    chatWindow = chat_window;
    $('#no_dm_block').hide();
    var hh_height = win_height - 163;

    if("participants" === chat_window){
         $('.chat-leftsidebar').addClass('height-unset');
        // $('.chat-leftsidebar').css('height', 'auto');
        
    }
    else{
        $('.chat-leftsidebar').removeClass('height-unset');
        // $('.chat-leftsidebar').css('height', hh_height + 'px');
        $('.chat-leftsidebar').css('overflow-y', 'auto');
        $('.chat-leftsidebar').css('overflow-x', 'hidden');
    }

    //alert(selectedChat)
    if(selectedChat != 'watch-party'){
         $('#channel-chat').removeClass('watchPartyEnabled');
        $('.watchPartySection').removeClass('watchPartyEnabled');
        chatInputContainer.removeClass('watchPartyEnabled');
        $('.chat-leftsidebar').removeClass('watchPartyEnabled');
    }

   

    if ("channel" === chat_window) {
        //alert('------selectedChat-------'+selectedChat)
       
       if(selectedChat == 'watch-party'){
            $('#user-chat').addClass('user-chat-show');

            hideElementsById(['participants', 'users-chat','user-profile-sidebar','participants-sidebar','channelData-sidebar','speed_networking']);
            document.getElementById("channel-chat").style.display = "block";
            $('#channel-chat').addClass('watchPartyEnabled');
            $('.watchPartySection').addClass('watchPartyEnabled');
            chatInputContainer.addClass('watchPartyEnabled');
            $('.chat-leftsidebar').addClass('watchPartyEnabled');

            $('.chat-leftsidebar').removeClass('open');

            chatInputContainer.show();
       }
       else{
            $('#user-chat').addClass('user-chat-show');

            hideElementsById(['participants', 'users-chat','user-profile-sidebar','participants-sidebar','speed_networking']);
            document.getElementById("channel-chat").style.display = "block";
            chatInputContainer.show();
       }
        
        
    } 
    else if ("direct_message" === chat_window) {
        $('#user-profile-sidebar').addClass('d-xl-block');
        $('#user-chat').addClass('user-chat-show');

        hideElementsById(['participants', 'channel-chat','channelData-sidebar','participants-sidebar','speed_networking']);
        document.getElementById("users-chat").style.display = "block";
        // document.getElementById("user-profile-sidebar").style.display = "block";

        chatInputContainer.show();
    } else if ("participants" === chat_window) {
        $('#user-chat').addClass('user-chat-show');

        hideElementsById(['channel-chat', 'users-chat', 'chat-input-container','user-profile-sidebar','channelData-sidebar','speed_networking']);
        $('#participants').addClass('d-block');
        //    document.getElementById("participants-sidebar").style.display = "block";
        $('#participants-sidebar').addClass('d-xl-block');
    } else if ("speed_networking" === chat_window) {
        $('#user-chat').addClass('user-chat-show');
        hideElementsById(['channel-chat', 'users-chat', 'chat-input-container','user-profile-sidebar','channelData-sidebar','participants']);
        $('#speed_networking').addClass('d-block');
        //$('#participants-sidebar').addClass('d-xl-block');
    } else {
        hideElementsById(['participants','participants-sidebar', 'channel-chat', 'users-chat', 'chat-input-container',
            'channelData-sidebar','user-profile-sidebar','speed_networking']);
        //if(selectedUser == '')
        //    $('#no_dm_block').show();
    }

    if(selectedChat != 'watch-party')
        loadRightSidebar();
    else
    {
            $('#channel-chat').addClass('watchPartyEnabled');
            $('.watchPartySection').addClass('watchPartyEnabled');
            chatInputContainer.addClass('watchPartyEnabled');
            $('.chat-leftsidebar').addClass('watchPartyEnabled');
    }
}

function loadRightSidebar(sidebar_window = '', data = false) {
    if ("reply" === sidebar_window) {
        hideElementsById(['channelData-sidebar', 'user-profile-sidebar']);
        document.getElementById("chat-reply-sidebar").style.display = "block";
    } else if ("like" === sidebar_window) {
        hideElementsById(['channelData-sidebar', 'user-profile-sidebar']);
        document.getElementById("chat-like-sidebar").style.display = "block";
    } else if ("members" === sidebar_window) {
        hideElementsById(['chat-reply-sidebar', 'user-profile-sidebar']);
        // document.getElementById("channelData-sidebar").style.display = "block";
        $('#channelData-sidebar').addClass('d-xl-block');

    } else if ("profile" === sidebar_window) {
        hideElementsById(['chat-reply-sidebar', 'channelData-sidebar']);
        // document.getElementById("user-profile-sidebar").style.display = "block";
        $('#user-profile-sidebar').addClass('d-xl-block');
        $('#user-profile-sidebar').show();
    } else {
        hideElementsById(['chat-reply-sidebar', 'channelData-sidebar', 'user-profile-sidebar']);
    }

    if ("reply" !== sidebar_window) {
        frm_message_id = 0;
        frm_reply_view = false;
        $('#reply_comment_id').val(frm_message_id);
        if (frmReplyChatDataInterval) clearInterval(frmReplyChatDataInterval);
        frm_reply_comments_list.empty();
        frmReplyChatPageNo = 1;
    }
}


function initNetworkingTour(mustShow = false) {
    // Skip if screen width is less than 1200px
    if (window.innerWidth < 1200) {
        return;
    }

    const ntwTourKey = `taoh_networking_tour_${ntw_tour_version}`;
    if (mustShow || localStorage.getItem(ntwTourKey) !== 'completed') {
        localStorage.setItem(ntwTourKey, 'completed');
        introJs().start();
    }
}

async function updateUnreadCountOld(callFromEvent = 'update') {
    if (!ntw_room_key) return;

    if (callFromEvent === 'interval' && isEmptyObject(ntw_unreadCountModified)) {
        return;
    }

    try {
        const metaIds = Object.keys(ntw_unreadCountModified);
        const ntw_channels_key = `ntw_channels_${ntw_room_key}`;
        const intaoData = await IntaoDB.getItem(objStores.ntw_store.name, ntw_channels_key);
        const channelList = intaoData?.values?.channels || {};

        for (const [channelId, channelInfo] of Object.entries(channelList)) {
            const metaId = `ntw_meta_${ntw_room_key}_${channelId}`;

            if (callFromEvent === 'interval' && !metaIds.includes(metaId)) {
                continue;
            }

            const meta = await IntaoDB.getItem(objStores.ntw_meta_store.name, metaId);
            const channelUnreadCount = meta?.unread || 0;
            const channelLastView = meta?.lastview || 0;

            let targetElement;

            if (channelInfo.type === '1') {
                // Channel Message
                targetElement = $(`.channelList li#channel-${channelId} .unread-count`);
            } else if (channelInfo.type === '2') {
                // Direct Message
                targetElement = $(`.usersList li#dm-${channelId} .unread-count`);
            }

            if (targetElement?.length) {
                targetElement.setSyncedData('lastview', channelLastView);
                targetElement.setSyncedData('count', channelUnreadCount).text(channelUnreadCount);
            }

            delete ntw_unreadCountModified[metaId];
        }
    } catch (error) {
        console.error('Error updating unread counts:', error);
    }
}

function clearUnreadCount(room_hash, channel_id, updateLastView = 0) {
    if (!room_hash || !channel_id) return;

    const metaId = `ntw_meta_${room_hash}_${channel_id}`;
    resetUnreadCount(metaId, updateLastView);
}

async function clearNewRepliesCount(ntw_room_key, channelId, frmMessageId, frmMessageKey) {
    const metaId = `frm_${ntw_room_key}_${channelId}`;

    // Remove class from DOM if element exists
    if (frmMessageId && frmMessageKey) {
        const messageElem = $(`.chat-list[data-frm_message_id="${frmMessageId}"]`);
        if (messageElem.length) {
            messageElem.find('.conversation-reply-count').removeClass('has-new-replies');
        }
    }

    const intaoData = await IntaoDB.getItem(objStores.ntw_store.name, metaId);

    if (intaoData?.values?.chat?.[frmMessageKey]) {
        intaoData.values.chat[frmMessageKey].new_reply_count = 0;

        await IntaoDB.setItem(objStores.ntw_store.name, {
            ...intaoData,
            values: {
                ...intaoData.values,
                chat: {
                    ...intaoData.values.chat
                }
            },
            timestamp: Date.now()
        });
    }
}

/*======================== Helper Functions ==========================*/

async function getUserInfo(pToken_to, ops = 'public', serverFetch = false) {

    if (typeof pToken_to !== 'string' || !pToken_to.trim()) return null;

    if(pToken_to == 'organizer') return null;

    let userInfo = {};

    // Initialize ops object if not exists
    ntwuserInfoList[ops] = ntwuserInfoList[ops] || {};

    if (!serverFetch) {
        // Try to get userInfo from local variable
        userInfo = ntwuserInfoList[ops][pToken_to] || userInfo;

        // Try to get userInfo from global variable
        if (!userInfo.ptoken) {
            ntwuserInfoList[ops] = ntwuserInfoList[ops] || {};
            userInfo = ntwuserInfoList[ops][pToken_to] || userInfo;
        }

        // Try to get userInfo from IndexedDB
        if (!userInfo.ptoken) {
            const user_info_key = 'user_info_list';
            const intao_data = await IntaoDB.getItem(objStores.common_store.name, user_info_key);
            if (intao_data?.values && intao_data.values[ops] && intao_data.values[ops][pToken_to]) {
                let userInfoObj = intao_data.values[ops][pToken_to];
                // Check if data is expired (expires after 2 day)
                if (userInfoObj.last_fetch_time && (Date.now() - userInfoObj.last_fetch_time) <= 172800000) {
                    ntwuserInfoList[ops][userInfoObj.ptoken] = userInfoObj;
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
            ntwuserInfoList[ops][srv_userInfoObj.ptoken] = srv_userInfoObj;
            userInfo = srv_userInfoObj;
        } catch (e) {
            console.log('getUserInfo - ' + pToken_to + '  error:', e);
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
        ntwuserInfoList[ops][userInfo.ptoken] = userInfo;
    }

    if (userInfo.ptoken === sidekick_ptoken && sidekick_avatar) {
        userInfo.avatar_image = sidekick_avatar;
    }

    return userInfo;
}

function convertMentionsToLinks(message) {
    const mentionPattern = new RegExp('@(\\w+)', 'g');
    return message.replace(mentionPattern, (match, username) => {
        const mention_ptoken = mentionUserArray[username] || '';

        if (mention_ptoken) {
            taoh_track_activities({
                action: 'mention',
                mention_name: username,
                mention_ptoken: mention_ptoken,
                ptoken: my_pToken
            });
        }

        // No link if no valid ptoken
        return `<span class="conversation-mention" data-ptoken="${mention_ptoken}">@${username}</span>`;
    });
}

function addRemoveActive(currentElem = false) {
    $('.channelList li.active').removeClass('active');
    $('.usersList li.active').removeClass('active');
    $('.orgChannelList li.active').removeClass('active');
    $('#sidekickList li.active').removeClass('active');

    if(currentElem) currentElem.addClass('active');
}

function hideElementsById(ids) {
    ids.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.style.display = "none";
            el.classList.remove('d-block');
            el.classList.remove('d-xl-block');
        }
    });
}

function toggleVisibility(el, show = true) {
    if (!el || !(el instanceof HTMLElement)) return;

    if (show) {
        el.classList.remove('d-none');
        el.classList.add('d-block');
    } else {
        el.classList.remove('d-block');
        el.classList.add('d-none');
    }
}

function updateStickyBadgeTxt(badgeContainerId, stickyBadgeId) {
    const badgeContainer = document.querySelector(badgeContainerId);
    const stickyBadge = document.querySelector(stickyBadgeId);

    const scrollPosition = badgeContainer.scrollTop;

    const dateBadges = badgeContainer.querySelectorAll('.date-badge');

    dateBadges.forEach((badge) => {
        const badgeTop = badge.getBoundingClientRect().top;
        const containerTop = badgeContainer.getBoundingClientRect().top;

        if (badgeTop <= containerTop) {
            stickyBadge.textContent = badge.textContent;
            stickyBadge.style.display = (scrollPosition < 20) ? 'none' : 'block';
        }
    });
}

function removeSameBadge(badgeContainerId, daytxt) {
    const badgeContainer = document.querySelector(badgeContainerId);
    const badges = document.querySelectorAll('.date-badge');
    if (badges.length > 0 && daytxt == badges[0].textContent) {
        const scrollPosition = badgeContainer.scrollTop;
        badges[0].remove();

        // Restore the scroll position
        badgeContainer.scrollTop = scrollPosition
    }
}

function simpleBarScrollToBottom(selector) {
    const scrollElement = document.querySelector(selector);
    if (!scrollElement) return;

    let simplebarInstance = SimpleBar.instances.get(scrollElement);
    if (!simplebarInstance) {
        simplebarInstance = new SimpleBar(scrollElement);
    }

    if (simplebarInstance) {
        simplebarInstance.recalculate();
        const scrollElement = simplebarInstance.getScrollElement();

        scrollElement.scrollTo({
            top: scrollElement.scrollHeight + 100,
            behavior: 'smooth'
        });
    }
}

function isScrolledUpSimplebar(selector, thresholdPx = 100) {
    const container = document.querySelector(selector);
    if (!container) return false;

    const simplebarInstance = SimpleBar.instances.get(container);
    if (!simplebarInstance) return false;

    const scrollElement = simplebarInstance.getScrollElement();
    const distanceFromBottom = scrollElement.scrollHeight - scrollElement.scrollTop - scrollElement.clientHeight;

    // If near the bottom (within thresholdPx), treat as not scrolled up
    return distanceFromBottom > thresholdPx;
}

function prependAndRestoreScrollPositionWithSimpleBar(containerSelector, prependCallback) {
    const container = document.querySelector(containerSelector);
    if (!container) {
        prependCallback();
        return;
    }

    const simplebarInstance = SimpleBar.instances.get(container);

    // If SimpleBar is available, track scroll position
    let previousScrollHeight = 0;
    let previousScrollTop = 0;
    let scrollElement = null;

    if (simplebarInstance && typeof simplebarInstance.getScrollElement === 'function') {
        scrollElement = simplebarInstance.getScrollElement();
        previousScrollHeight = scrollElement.scrollHeight;
        previousScrollTop = scrollElement.scrollTop;
    }

    prependCallback();

    // Restore scroll position if applicable
    if (scrollElement) {
        requestAnimationFrame(() => {
            const newScrollHeight = scrollElement.scrollHeight;
            const scrollDiff = newScrollHeight - previousScrollHeight;
            scrollElement.scrollTop = previousScrollTop + scrollDiff;

            if (typeof simplebarInstance.recalculate === 'function') {
                simplebarInstance.recalculate();
            }
        });
    }
}

function clearModalForm(modalElem){
    modalElem.on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $(this).find('input, textarea, select').val('').prop('checked', false).prop('selected', false);
    });
}

function jq_confirm_unpin(
    title = 'Success',
    content = '',
    type = 'green',
    confirmButton = 'OK',
    onConfirm = () => {},
    showCancel = false
) {
    let buttons = {
        confirm: {
            text: confirmButton,
            action: onConfirm
        }
    };

    if (showCancel) {
        buttons.cancel = {
            text: 'Cancel',
            action: function () {
                // optional: do something on cancel
            }
        };
    }

    // Convert buttons object to an array
    let buttonArray = Object.values(buttons).map((button, index) => ({
        text: button.text,
        action: button.action,
        class: `dojo-v1-btn float-right mt-3 mb-3 ${index > 0 ? 'mr-2' : ''}`
    }));

    let contentHtml = `<b>${title}</b><br><br>${content}`;
    taoh_set_warning_message(contentHtml, false, 'toast-middle', buttonArray);

    // $.confirm({
    //     title: title,
    //     content: content,
    //     type: type,
    //     buttons: buttons
    // });
}

function jq_confirm_alert(title = 'success', content = '', type = 'green', confirmButton = 'Ok') {
    const buttonConfig = [
        {
            text: confirmButton,
            action: () => {},
            class: 'dojo-v1-btn float-right mt-3 mb-3'
        }
    ];

    switch (type) {
        case 'green':
            taoh_set_success_message(content, false, 'toast-middle', buttonConfig);
            break;
        case 'orange':
            taoh_set_warning_message(content, false, 'toast-middle', buttonConfig);
            break;
        case 'red':
            taoh_set_error_message(content, false, 'toast-middle', buttonConfig);
            break;
        default:
            taoh_set_info_message(content, false, 'toast-middle', buttonConfig);
            break;
    }

    // $.confirm({
    //     title: title,
    //     content: content,
    //     type: type,
    //     buttons: {
    //         confirm: {
    //             text: confirmButton,
    //             action: function () {}
    //         }
    //     }
    // });
}

/*======================== /Helper Functions ==========================*/