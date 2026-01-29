
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

        loadRightSidebar('profile');
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
            //loadRightSidebar('members');
            $('#chat-reply-sidebar').hide();
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
    document.addEventListener("click", function (e) {
        if (e.target.closest(".chat-user-list li .channel_btn")) {
            const userChat = document.getElementById('user-chat');

            if(!userChat.classList.contains('user-chat-show')) {
                 userChat.classList.add('user-chat-show');
            }
            if(userChat.classList.contains('mobile-transform')) {
                 userChat.classList.remove('mobile-transform');
            }

        }
    });
}

let prev_userLiveStatusInterval = typeof userLiveStatusInterval !== "undefined" ? userLiveStatusInterval : 60000;
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


// document.addEventListener('DOMContentLoaded', () => {
//     // For One to One
//     if (!SimpleBar.instances.has(ntw_chatContainer)) {
//         new SimpleBar(ntw_chatContainer);
//     }
//     const ntw_simplebarInstance = SimpleBar.instances.get(ntw_chatContainer);
//     if (ntw_simplebarInstance) {
//         const scrollElement = ntw_simplebarInstance.getScrollElement();
//         let ntw_prevScrollPos = scrollElement.scrollTop;
//
//         scrollElement.addEventListener('scroll', () => {
//             const ntw_currentScrollPos = scrollElement.scrollTop;
//
//             if ((ntw_currentScrollPos < ntw_prevScrollPos) && ntw_currentScrollPos < 10 && !ntw_msgScrollUpEnded) {
//                 ntwChatPageNo++;
//                 awloader(document.getElementById('ntwChatConversationLoader'), 'show');
//                 const requestData = getNTWChatRequestData(my_pToken, chatwith, 'scrollup');
//                 throttle(fetchNTWChatData(requestData, false), (ntw_isProcessing ? 500 : 100));
//             }
//
//             // updateStickyBadgeTxt('#users-conversation-list', '#stickyBadge');
//
//             ntw_prevScrollPos = ntw_currentScrollPos;
//         });
//     }
//
//
//     // For Channel
//     if (!SimpleBar.instances.has(frm_chatContainer)) {
//         new SimpleBar(frm_chatContainer);
//     }
//     const frm_simplebarInstance = SimpleBar.instances.get(frm_chatContainer);
//     if (frm_simplebarInstance) {
//         const scrollElement = frm_simplebarInstance.getScrollElement();
//         let frm_prevScrollPos = scrollElement.scrollTop;
//
//         scrollElement.addEventListener('scroll', () => {
//             const frm_currentScrollPos = scrollElement.scrollTop;
//
//             if ((frm_currentScrollPos < frm_prevScrollPos) && frm_currentScrollPos < 10 && !frm_msgScrollUpEnded) {
//                 frmChatPageNo++;
//                 awloader(document.getElementById('frmChatConversationLoader'), 'show');
//                 const requestData = getFRMChatRequestData(my_pToken, chatwith, 'scrollup');
//                 throttle(fetchFRMChatData(requestData, false), (frm_isProcessing ? 500 : 100));
//             }
//
//             // updateStickyBadgeTxt('#channel-conversation-list', '#frm_stickyBadge');
//
//             frm_prevScrollPos = frm_currentScrollPos;
//         });
//     }
//
//
//     // For Channel Reply
//     if (!SimpleBar.instances.has(frm_reply_chatContainer)) {
//         new SimpleBar(frm_reply_chatContainer);
//     }
//     const frm_reply_simplebarInstance = SimpleBar.instances.get(frm_reply_chatContainer);
//     if (frm_reply_simplebarInstance) {
//         const scrollElement = frm_reply_simplebarInstance.getScrollElement();
//         let frm_reply_prevScrollPos = scrollElement.scrollTop;
//
//         scrollElement.addEventListener('scroll', () => {
//             const frm_reply_currentScrollPos = scrollElement.scrollTop;
//
//             if ((frm_reply_currentScrollPos < frm_reply_prevScrollPos) && frm_reply_currentScrollPos < 10 && !frm_reply_msgScrollUpEnded) {
//                 frmReplyChatPageNo++;
//                 // taohLoader(document.getElementById('frm_reply_chat_loader'), true);
//                 const requestData = getFRMChatRequestData(my_pToken, chatwith, 'scrollup');
//                 throttle(fetchFRMReplyChatData(requestData, false), (frm_reply_isProcessing ? 500 : 100));
//             }
//
//             // updateStickyBadgeTxt('#chat-reply-conversation', '#frm_reply_stickyBadge');
//
//             frm_reply_prevScrollPos = frm_reply_currentScrollPos;
//         });
//     }
//
//     document.addEventListener('visibilitychange', () => {
//         if (!document.hidden) {
//             if (layout != 2 && "participants" === chatWindow) taoh_load_network_entries();
//
//             // initializeRequest('channel', ntw_room_key, my_pToken);
//             initializeRequest('direct_message', ntw_room_key, my_pToken);
//         }
//
//         // User Live Status Update
//         userLiveStatusInterval = document.hidden ? 300000: 1000; // 5 minutes : 1 second
//         prev_userLiveStatusInterval = userLiveStatusInterval;
//         if(userLiveIntervalId) clearInterval(userLiveIntervalId);
//         userLiveStatusUpdate(userLiveStatusInterval);
//
//         stopTitleBlinking();
//     });
//
//     /* At mentions */
//     const tribute = new Tribute({
//         noMatchTemplate: () => null,
//         values: async (text, cb) => {
//             try {
//                 if (chatWindow !== 'channel') return cb([]);
//
//                 const query = text.trim().toLowerCase();
//                 const ntwChannelsKey = `ntw_channels_${ntw_room_key}`;
//                 const channelData = await IntaoDB.getItem(objStores.ntw_store.name, ntwChannelsKey);
//                 const channelList = channelData?.values?.channels || {};
//
//                 const channelId = $('#channel-chat').data('channel_id');
//                 if (!channelId || !channelList[channelId]) return cb([]);
//
//                 const memberTokens = channelList[channelId].members || [];
//                 const members = [];
//
//                 for (const token of memberTokens) {
//                     // if (token === my_pToken) continue; // Skip self
//
//                     const user = await getUserInfo(token, 'public');
//                     if (user) {
//                         const fallbackAvatar = `${_taoh_ops_prefix}/avatar/PNG/128/${user.avatar || 'default'}.png`;
//                         const avatarSrc = await buildAvatarImage(user.avatar_image, fallbackAvatar);
//                         members.push({
//                             ptoken: user.ptoken,
//                             key: user.chat_name,
//                             value: user.chat_name,
//                             img: avatarSrc
//                         });
//                     }
//                 }
//
//                 const filtered = query
//                     ? members.filter(m => m.key.toLowerCase().includes(query))
//                     : members;
//
//                 cb(filtered);
//             } catch (error) {
//                 console.error('Tribute async values error:', error);
//                 cb([]);
//             }
//         },
//         menuItemTemplate: (item) => {
//             return `<div class="menu-mention-item">
//                         <img src="${item.original.img}" alt="${item.original.key}" class="menu-mention-avatar">
//                         <span class="menu-mention-name">${item.original.key}</span>
//                     </div>`;
//         },
//     });
//
//     const editor = document.getElementById('chat_input');
//     if (editor) {
//         tribute.attach(editor);
//         editor.addEventListener('tribute-replaced', e => {
//             const selected = e.detail.item.original;
//             if (selected?.value && selected?.ptoken) {
//                 mentionUserArray[selected.value] = selected.ptoken;
//             }
//         });
//     }
//
//     const replyEditor = document.getElementById('chat_reply_input');
//     if (replyEditor) {
//         tribute.attach(replyEditor);
//         replyEditor.addEventListener('tribute-replaced', e => {
//             const selected = e.detail.item.original;
//             if (selected?.value && selected?.ptoken) {
//                 mentionUserArray[selected.value] = selected.ptoken;
//             }
//         });
//     }
//
//     /* /At mentions */
// });

function loadchatWindow(chat_window = '', data = false) {
    console.log('-----chat_window------', chat_window);
    chatWindow = chat_window;
    $('#no_dm_block').hide();
    var hh_height = win_height - 163;

    if ("participants" === chat_window || "browse_channels" === chat_window || "speed_networking" === chat_window) {
        $('.chat-leftsidebar').addClass('height-unset');
    } else {
        $('.chat-leftsidebar').removeClass('height-unset');
        // $('.chat-leftsidebar').css('height', hh_height + 'px');
        $('.chat-leftsidebar').css('overflow-y', 'auto');
        $('.chat-leftsidebar').css('overflow-x', 'hidden');
    }

    if ("participants" === chat_window) {
        hideElementsById(['browse_channels_wrapper', 'channel-chat', 'chat-input-container']);
        hideElementsById(['channel-chat', 'users-chat', 'chat-input-container','user-profile-sidebar','channelData-sidebar','speed_networking']); // old
        $('#user-chat').addClass('user-chat-show');
        $('#participants').addClass('d-block');
        $('#participants-sidebar').addClass('d-xl-block');
    } else if ("speed_networking" === chat_window) {
        $('#user-chat').addClass('user-chat-show');
        hideElementsById(['browse_channels_wrapper', 'channel-chat', 'users-chat', 'chat-input-container','user-profile-sidebar','channelData-sidebar','participants','participants-sidebar']);
        $('#speed_networking').addClass('d-block');
        $('#participants-sidebar').addClass('d-xl-block');
    } else if ("browse_channels" === chat_window) {
        $('#user-chat').addClass('user-chat-show');
        hideElementsById(['participants', 'participants-sidebar', 'channel-chat', 'chat-input-container']);
        hideElementsById(['users-chat','user-profile-sidebar','channelData-sidebar','speed_networking']); // old
        browseChannelsContainer.show();

    }  else if ("channels" === chat_window) {
        hideElementsById(['participants', 'participants-sidebar', 'browse_channels_wrapper']);
        $('#channel-chat').addClass('d-block');
        $('#chat-input-container').show();
    } else {
        hideElementsById(['participants', 'participants-sidebar', 'browse_channels_wrapper', 'channel-chat', 'chat-input-container']);
        hideElementsById(['users-chat', 'channelData-sidebar','user-profile-sidebar','speed_networking']); // old
    }

    if (selectedChat != 'watch-party') {
        loadRightSidebar();
    } else {
        if(watchPartyEnabledChannel == 1) {
            $('#channel-chat').addClass('watchPartyEnabled');
            $('.watchPartySection').addClass('watchPartyEnabled');
            chatInputContainer.addClass('watchPartyEnabled');
            $('.chat-leftsidebar').addClass('watchPartyEnabled');
        }
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
        hideElementsById(['chat-reply-sidebar', 'channelData-sidebar', 'participants-sidebar']);
        // document.getElementById("user-profile-sidebar").style.display = "block";
        $('#user-profile-sidebar').addClass('d-xl-block');
        $('#user-profile-sidebar').show();
    } else {
        hideElementsById(['chat-reply-sidebar', 'channelData-sidebar', 'user-profile-sidebar']);
    }

    // if ("reply" !== sidebar_window) {
    //     frm_message_id = 0;
    //     frm_reply_view = false;
    //     $('#reply_comment_id').val(frm_message_id);
    //     if (frmReplyChatDataInterval) clearInterval(frmReplyChatDataInterval);
    //     frm_reply_comments_list.empty();
    //     frmReplyChatPageNo = 1;
    // }
}

function escapeHtml(s) {
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

async function loadChannelsList(tabSlug) {

    const tabInfo = browse_channel_tabs.find(tab => tab.slug === tabSlug);

    if (!tabInfo) return;

    const browseChannels = $(`#browse_${tabSlug}_channels`);
    const browseChannelQuery = $('#browseChannelQuery');

    browseChannels.empty();

    const getChannelData = (ch) => {
        if (!ch) return {};
        const raw = ch.data;
        if (raw == null) return {};
        if (typeof raw === 'object') return raw;
        try { return JSON.parse(raw) || {}; } catch { return {}; }
    };

    let exh_channel_count = 0;
    let ses_channel_count = 0;

    try {
        let getNTWChannelsFormData = {
            roomslug,
            keyword: ntw_keyword,
            type: tabInfo?.channel_types_to_show,
            my_pToken,
            q: browseChannelQuery.val() || '',
            browsetabslug: tabInfo?.slug || '',
        };

        if(tabInfo?.eventtoken){
            getNTWChannelsFormData.eventtoken = tabInfo.eventtoken;
        }

        const { response } = await getNTWChannels(getNTWChannelsFormData, true);
        const channels = Array.isArray(response)
            ? response
            : Array.isArray(response?.channels)
                ? response.channels
                : [];

        let browseChannelsHtml = '';

        if (channels.length) {
            browseChannelsHtml = channels.map((channel) => {
                const data = getChannelData(channel);
                const id = channel.id ?? '';
                const type = channel.type ?? 1;
                const name = data.name ?? 'Unnamed';
                const description = taoh_desc_decode_new(data.description ?? '');
                const members_count = Number(channel.members_count ?? 0) || 0;
                const isJoined = Boolean((channel.members)?.includes(my_pToken) || false);

                if(type == taohChannelExhibitor){

                    if(data.exh_state == 'active' || data.exh_state == 'live') {
                        exh_channel_count++;
                    }

                    return `<div class="channel-item ch-list pr-lg-5 open_channel_in_home ${data.exh_state == 'suspended' || data.exh_state == 'closed' ? 'd-none' : ''}" data-channelid="${id}" data-channeltype="${type}" data-channelname="${name}">
                             <div class="d-flex align-items-sm-center flex-column flex-sm-row" style="gap: 25px;">
                                 <img class="exh-list-img" src="${data.exhibitor_logo}" alt="">
                                 <div class="flex-grow-1 d-flex flex-wrap flex-xl-nowrap align-items-center">
                                     <div class="flex-grow-1">
                                         <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 10px;">
                                             <h6 class="exh-title channel_info_view text-capitalize">${name}</h6>

                                             <span class="h-name lh-1">${data.exhibitor_hall}</span>

                                             <ul class="channel-item-top-badges dotted-list pl-1 mb-0">
                                                ${isJoined ? '<li class="join-status text-success"><i class="fa fa-check-circle text-success mr-1" aria-hidden="true"></i> Joined</li>' : ''}
                                                <li class="members_view ch-mem lh-1 cursor-pointer" data-count="${members_count}">${members_count} ${members_count === 1 ? 'Member' : 'Members'}</li>
                                            </ul>
                                         </div>
                                         <p class="exh-desc line-clamp-2 mb-2">${description}</p>
                                     </div>
                                     <div class="d-flex flex-wrap flex-xl-nowrap flex-shrink-0 gap-2">
                                        <button class="btn bor-btn py-2 exh_channel_more_info_btn" data-exhibitorid="${data.exhibitor_id}" data-eventtoken="${data.eventtoken}">More Info</button>
                                        <button class="btn bor-btn py-2 ${isJoined ? 'joined_channel_btn' : 'join_channel_btn'}" data-channelid="${id}">
                                            <i class="fa ${isJoined ? 'fa-check' : 'fa-plus-circle'} mr-1" aria-hidden="true"></i>
                                            <span>${isJoined ? 'Joined' : 'Join'}</span>
                                        </button>
                                    </div>
                                 </div>
                             </div>
                         </div>`;
                } else if(type == taohChannelSession){

                    if(data.spk_state == 'active' || data.spk_state == 'live') {
                        ses_channel_count++;
                    }

                    return `<div class="channel-item ch-list pr-lg-5 open_channel_in_home ${data.spk_state == 'suspended' || data.spk_state == 'closed' ? 'd-none' : ''}" data-channelid="${id}" data-channeltype="${type}" data-channelname="${name}">
                             <div class="d-flex align-items-sm-center flex-column flex-sm-row" style="gap: 25px;">
                                 <img class="session-list-img" src="${data.speaker_logo}" alt="">
                                 <div class="flex-grow-1 d-flex flex-wrap flex-xl-nowrap align-items-center">
                                     <div class="flex-grow-1">
                                         <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 10px;">
                                             <h6 class="session-title channel_info_view text-capitalize">${name}</h6>

                                             <span class="h-name lh-1">${data.speaker_hall}</span>

                                             <ul class="channel-item-top-badges dotted-list pl-1 mb-0">
                                                ${isJoined ? '<li class="join-status text-success"><i class="fa fa-check-circle text-success mr-1" aria-hidden="true"></i> Joined</li>' : ''}
                                                <li class="members_view ch-mem lh-1 cursor-pointer" data-count="${members_count}">${members_count} ${members_count === 1 ? 'Member' : 'Members'}</li>
                                            </ul>
                                         </div>
                                         <p class="session-desc line-clamp-2 mb-2">${description}</p>
                                     </div>
                                     <div class="d-flex flex-wrap flex-xl-nowrap flex-shrink-0 gap-2">
                                        <button class="btn bor-btn py-2 session_channel_more_info_btn" data-speakerid="${data.speaker_id}" data-eventtoken="${data.eventtoken}">More Info</button>
                                        <button class="btn bor-btn py-2 ${isJoined ? 'joined_channel_btn' : 'join_channel_btn'}" data-channelid="${id}">
                                            <i class="fa ${isJoined ? 'fa-check' : 'fa-plus-circle'} mr-1" aria-hidden="true"></i>
                                            <span>${isJoined ? 'Joined' : 'Join'}</span>
                                        </button>
                                    </div>
                                 </div>
                             </div>
                         </div>`;
                } else {
                    return `<div class="channel-item ch-list px-lg-4 d-flex flex-wrap flex-xl-nowrap align-items-center open_channel_in_home" data-channelid="${id}" data-channeltype="${type}" data-channelname="${name}">
                        <div class="flex-grow-1">
                            <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 6px;">
                                <h6 class="ch-title channel_info_view mb-0 text-capitalize"><i class="la ${channel.visibility === 'private' ? 'la-lock' : 'la-hashtag'} mr-1"></i>${name}</h6>
                                <ul class="channel-item-top-badges dotted-list pl-1 mb-0">
                                    ${isJoined ? '<li class="join-status text-success"><i class="fa fa-check-circle text-success mr-1" aria-hidden="true"></i> Joined</li>' : ''}
                                    <li class="members_view ch-mem lh-1 cursor-pointer" data-count="${members_count}">${members_count} ${members_count === 1 ? 'Member' : 'Members'}</li>
                                </ul>
                            </div>
                            <p class="ch-desc line-clamp-2 mb-1">${description}</p>
                        </div>
                        <div class="d-flex flex-wrap flex-xl-nowrap flex-shrink-0 gap-2">
<!--                            <button class="btn bor-btn py-2 view_channel_btn channel_info_view  ${isJoined ? 'd-block' : 'd-none'}">View Channel</button>-->
                            <button class="btn bor-btn py-2 ${isJoined ? 'joined_channel_btn' : 'join_channel_btn'}" data-channelid="${id}">
                                <i class="fa ${isJoined ? 'fa-check' : 'fa-plus-circle'} mr-1" aria-hidden="true"></i>
                                <span>${isJoined ? 'Joined' : 'Join'}</span>
                            </button>
                        </div>
                    </div>`;
                }

            }).join('');
        } else {
            browseChannelsHtml = '<div class="text-muted px-3 py-2">No channels to browse.</div>';
        }

        browseChannels.html(browseChannelsHtml);
        browseChannels.awloader('hide');
    } catch (err) {
        console.error('Error fetching channel list:', err);
        browseChannels.html('<div class="text-danger px-3 py-2">No channels to browse.</div>');
        browseChannels.awloader('hide');
    }

    if(tabSlug == "allchannels") {
        if(ses_channel_count == 0) {
            $('#sessions-tab').hide();
        } else {
            $('#sessions-tab').show();
        }
        if(exh_channel_count == 0) {
            $('#exhibitors-tab').hide();
        }else {
            $('#exhibitors-tab').show();
        }
    }
    $('#browse_channels_wrapper').removeClass('d-none');
    $('#user-chat').addClass('user-chat-show');
    // if(eventToken){
        // loadExhibitorChannelsList();
        // loadSponsorChannelsList();
        // loadSessionChannelsList();
    // }
}

// async function loadExhibitorChannelsList(){
//     const browseChannels = $('#browse_exhibitor_channels');
//     const browseChannelQuery = $('#browseChannelQuery');
//
//     browseChannels.empty();
//
//     const getChannelData = (ch) => {
//         if (!ch) return {};
//         const raw = ch.data;
//         if (raw == null) return {};
//         if (typeof raw === 'object') return raw;
//         try { return JSON.parse(raw) || {}; } catch { return {}; }
//     };
//
//     try {
//         const { response } = await getNTWChannels({
//             roomslug: eventToken,
//             keyword: ntw_keyword,
//             type: taohChannelExhibitor,
//             my_pToken,
//             q: browseChannelQuery.val() || '',
//         }, true);
//         const channels = Array.isArray(response)
//             ? response
//             : Array.isArray(response?.channels)
//                 ? response.channels
//                 : [];
//
//         let browseChannelsHtml = '';
//         if (channels.length) {
//             browseChannelsHtml = channels.map((channel) => {
//                 const data = getChannelData(channel);
//                 const id = channel.id ?? '';
//                 const type = channel.type ?? 1;
//                 const name = escapeHtml(data.name ?? 'Unnamed');
//                 const description = escapeHtml(data.description ?? '');
//                 const members_count = Number(channel.members_count ?? 0) || 0;
//                 const isJoined = Boolean((channel.members)?.includes(my_pToken) || false);
//
//                 return `<div class="channel-item exh-list pr-lg-5 open_channel_in_home" data-channelid="${id}" data-channeltype="${type}" data-channelname="${name}">
//                              <div class="d-flex align-items-center" style="gap: 25px;">
//                                  <img class="exh-list-img" src="${data.exhibitor_logo}" alt="">
//                                  <div class="flex-grow-1 d-flex flex-wrap flex-xl-nowrap align-items-center">
//                                      <div class="flex-grow-1">
//                                          <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 10px;">
//                                              <h6 class="exh-title channel_info_view text-capitalize">${name}</h6>
//
//                                              <span class="h-name lh-1">${data.exhibitor_hall}</span>
//
//                                              <ul class="channel-item-top-badges dotted-list pl-1 mb-0">
//                                                 ${isJoined ? '<li class="join-status text-success"><i class="fa fa-check-circle text-success mr-1" aria-hidden="true"></i> Joined</li>' : ''}
//                                                 <li class="members_view ch-mem lh-1 cursor-pointer">${members_count} ${members_count === 1 ? 'Member' : 'Members'}</li>
//                                             </ul>
//                                          </div>
//                                          <p class="exh-desc line-clamp-2 mb-1">${description}</p>
//                                      </div>
//                                      <div class="visibility-hover d-flex flex-wrap flex-xl-nowrap flex-shrink-0 gap-2">
//                                         <button class="btn bor-btn py-2 exh_channel_more_info_btn" data-exhibitorid="${data.exhibitor_id}" data-eventtoken="${data.eventtoken}">More Info</button>
//                                         <button class="btn bor-btn py-2 ${isJoined ? 'event_joined_channel_btn' : 'event_join_channel_btn'}" data-channelid="${id}">
//                                             <i class="fa ${isJoined ? 'fa-check' : 'fa-plus-circle'} mr-1" aria-hidden="true"></i>
//                                             <span>${isJoined ? 'Joined' : 'Join'}</span>
//                                         </button>
//                                     </div>
//                                  </div>
//                              </div>
//                          </div>
//             `;
//             }).join('');
//         } else {
//             browseChannelsHtml = '<div class="text-muted px-3 py-2">No channels to browse.</div>';
//         }
//
//         browseChannels.html(browseChannelsHtml);
//         browseChannels.awloader('hide');
//     } catch (err) {
//         console.error('Error fetching channel list:', err);
//         browseChannels.html('<div class="text-danger px-3 py-2">No channels to browse.</div>');
//         browseChannels.awloader('hide');
//     }
// }

// async function loadSponsorChannelsList(){
//     const browseChannels = $('#browse_sponsor_channels');
//     const browseChannelQuery = $('#browseChannelQuery');
//
//     browseChannels.empty();
//
//     const getChannelData = (ch) => {
//         if (!ch) return {};
//         const raw = ch.data;
//         if (raw == null) return {};
//         if (typeof raw === 'object') return raw;
//         try { return JSON.parse(raw) || {}; } catch { return {}; }
//     };
//
//     try {
//         const { response } = await getNTWChannels({
//             roomslug: eventToken,
//             keyword: ntw_keyword,
//             type: taohChannelSponsor,
//             my_pToken,
//             q: browseChannelQuery.val() || '',
//         }, true);
//         const channels = Array.isArray(response)
//             ? response
//             : Array.isArray(response?.channels)
//                 ? response.channels
//                 : [];
//
//         let browseChannelsHtml = '';
//         if (channels.length) {
//             browseChannelsHtml = channels.map((channel) => {
//                 const data = getChannelData(channel);
//                 const id = channel.id ?? '';
//                 const type = channel.type ?? 1;
//                 const name = escapeHtml(data.name ?? 'Unnamed');
//                 const description = escapeHtml(data.description ?? '');
//                 const members_count = Number(channel.members_count ?? 0) || 0;
//                 const isJoined = Boolean((channel.members)?.includes(my_pToken) || false);
//
//                 return `<div class="channel-item sponsor-list pr-lg-5 open_channel_in_home" data-channelid="${id}" data-channeltype="${type}" data-channelname="${name}">
//                              <div class="d-flex align-items-center" style="gap: 25px;">
//                                  <img class="sponsor-list-img" src="${data.sponsor_logo}" alt="">
//                                  <div class="flex-grow-1 d-flex flex-wrap flex-xl-nowrap align-items-center">
//                                      <div class="flex-grow-1">
//                                          <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 10px;">
//                                              <h6 class="sponsor-title channel_info_view text-capitalize">${name}</h6>
//                                              <ul class="channel-item-top-badges dotted-list pl-1 mb-0">
//                                                 ${isJoined ? '<li class="join-status text-success"><i class="fa fa-check-circle text-success mr-1" aria-hidden="true"></i> Joined</li>' : ''}
//                                                 <li class="members_view ch-mem lh-1 cursor-pointer">${members_count} ${members_count === 1 ? 'Member' : 'Members'}</li>
//                                             </ul>
//                                          </div>
//                                          <p class="sponsor-desc line-clamp-2 mb-1">${description}</p>
//                                      </div>
//                                      <div class="visibility-hover d-flex flex-wrap flex-xl-nowrap flex-shrink-0 gap-2">
//                                         <button class="btn bor-btn py-2 sponsor_channel_more_info_btn" data-sponsorid="${data.sponsor_id}" data-eventtoken="${data.eventtoken}">More Info</button>
//                                         <button class="btn bor-btn py-2 ${isJoined ? 'event_joined_channel_btn' : 'event_join_channel_btn'}" data-channelid="${id}">
//                                             <i class="fa ${isJoined ? 'fa-check' : 'fa-plus-circle'} mr-1" aria-hidden="true"></i>
//                                             <span>${isJoined ? 'Joined' : 'Join'}</span>
//                                         </button>
//                                     </div>
//                                  </div>
//                              </div>
//                          </div>
//             `;
//             }).join('');
//         } else {
//             browseChannelsHtml = '<div class="text-muted px-3 py-2">No channels to browse.</div>';
//         }
//
//         browseChannels.html(browseChannelsHtml);
//         browseChannels.awloader('hide');
//     } catch (err) {
//         console.error('Error fetching channel list:', err);
//         browseChannels.html('<div class="text-danger px-3 py-2">No channels to browse.</div>');
//         browseChannels.awloader('hide');
//     }
// }

// async function loadSessionChannelsList(){
//     const browseChannels = $('#browse_session_channels');
//     const browseChannelQuery = $('#browseChannelQuery');
//
//     browseChannels.empty();
//
//     const getChannelData = (ch) => {
//         if (!ch) return {};
//         const raw = ch.data;
//         if (raw == null) return {};
//         if (typeof raw === 'object') return raw;
//         try { return JSON.parse(raw) || {}; } catch { return {}; }
//     };
//
//     try {
//         const { response } = await getNTWChannels({
//             roomslug: eventToken,
//             keyword: ntw_keyword,
//             type: taohChannelSession,
//             my_pToken,
//             q: browseChannelQuery.val() || '',
//         }, true);
//         const channels = Array.isArray(response)
//             ? response
//             : Array.isArray(response?.channels)
//                 ? response.channels
//                 : [];
//
//         let browseChannelsHtml = '';
//         if (channels.length) {
//             browseChannelsHtml = channels.map((channel) => {
//                 const data = getChannelData(channel);
//                 const id = channel.id ?? '';
//                 const type = channel.type ?? 1;
//                 const name = escapeHtml(data.name ?? 'Unnamed');
//                 const description = escapeHtml(data.description ?? '');
//                 const members_count = Number(channel.members_count ?? 0) || 0;
//                 const isJoined = Boolean((channel.members)?.includes(my_pToken) || false);
//
//                 return `<div class="channel-item session-list pr-lg-5 open_channel_in_home" data-channelid="${id}" data-channeltype="${type}" data-channelname="${name}">
//                              <div class="d-flex align-items-center" style="gap: 25px;">
//                                  <img class="session-list-img" src="${data.speaker_logo}" alt="">
//                                  <div class="flex-grow-1 d-flex flex-wrap flex-xl-nowrap align-items-center">
//                                      <div class="flex-grow-1">
//                                          <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 10px;">
//                                              <h6 class="session-title channel_info_view text-capitalize">${name}</h6>
//
//                                              <span class="h-name lh-1">${data.speaker_hall}</span>
//
//                                              <ul class="channel-item-top-badges dotted-list pl-1 mb-0">
//                                                 ${isJoined ? '<li class="join-status text-success"><i class="fa fa-check-circle text-success mr-1" aria-hidden="true"></i> Joined</li>' : ''}
//                                                 <li class="members_view ch-mem lh-1 cursor-pointer">${members_count} ${members_count === 1 ? 'Member' : 'Members'}</li>
//                                             </ul>
//                                          </div>
//                                          <p class="session-desc line-clamp-2 mb-1">${description}</p>
//                                      </div>
//                                      <div class="visibility-hover d-flex flex-wrap flex-xl-nowrap flex-shrink-0 gap-2">
//                                         <button class="btn bor-btn py-2 session_channel_more_info_btn" data-speakerid="${data.speaker_id}" data-eventtoken="${data.eventtoken}">View Channel</button>
//                                         <button class="btn bor-btn py-2 ${isJoined ? 'event_joined_channel_btn' : 'event_join_channel_btn'}" data-channelid="${id}">
//                                             <i class="fa ${isJoined ? 'fa-check' : 'fa-plus-circle'} mr-1" aria-hidden="true"></i>
//                                             <span>${isJoined ? 'Joined' : 'Join'}</span>
//                                         </button>
//                                     </div>
//                                  </div>
//                              </div>
//                          </div>
//             `;
//             }).join('');
//         } else {
//             browseChannelsHtml = '<div class="text-muted px-3 py-2">No channels to browse.</div>';
//         }
//
//         browseChannels.html(browseChannelsHtml);
//         browseChannels.awloader('hide');
//     } catch (err) {
//         console.error('Error fetching channel list:', err);
//         browseChannels.html('<div class="text-danger px-3 py-2">No channels to browse.</div>');
//         browseChannels.awloader('hide');
//     }
// }

async function getNTWChannels(requestData, serverFetch = false, saveToDB = true) {
    return new Promise(async (resolve, reject) => {
        const {roomslug, keyword, type = '1', browsetabslug, my_pToken} = requestData || {};
        if (!roomslug || !keyword) {
            return reject(!roomslug ? "Room slug not provided." : "Keyword not provided.");
        }

        const ntwChannelListKey = ['room', keyword, roomslug, browsetabslug, 'channels'].filter(Boolean).join('_');

        // Function to handle the response after fetching or creating channels
        const handleResponse = async (response) => {
            // Channels found, return as is
            if (saveToDB) {
                IntaoDB.setItem(objStores.ntw_store.name, {
                    taoh_ntw: ntwChannelListKey,
                    values: response,
                    timestamp: Date.now()
                });
            }

            resolve({requestData, response});
        };

        // Fetch from server or DB
        if (serverFetch) {
            let ntwChannelsFormData = {
                taoh_action: 'taoh_ntw_get_channels',
                roomslug,
                keyword,
                type,
                key: my_pToken,
                q: requestData.q || ''
            };

            if(requestData?.channel_ticket_type) {
                ntwChannelsFormData.channel_ticket_type = requestData.channel_ticket_type;
            }

            if(requestData?.eventtoken) {
                ntwChannelsFormData.global_slug = requestData.eventtoken;
            }

            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'POST',
                dataType: 'json',
                data: ntwChannelsFormData,
                success: handleResponse,
                error: (xhr) => reject(`Error: ${xhr.status}`)
            });
        } else {
            IntaoDB.getItem(objStores.ntw_store.name, ntwChannelListKey)
                .then((data) => {
                    if (data?.values) {
                        handleResponse(data.values);
                    } else {
                        getNTWChannels(requestData, true, saveToDB).then(resolve).catch(reject);
                    }
                })
                .catch(reject);
        }
    });
}

if (typeof getNTWUserChannels !== 'function') {
    function getNTWUserChannels(requestData, serverFetch = false, saveToDB = true) {
        return new Promise((resolve, reject) => {
            if (!requestData.roomslug || !requestData.keyword) {
                reject(!requestData.roomslug ? "Room slug not provided." : "Keyword not provided.");
                return;
            }

            const ntwUserChannelListKey = ['room', requestData.keyword, requestData.roomslug, requestData.my_pToken, requestData.type, 'channels'].filter(Boolean).join('_');


            const handleResponse = (response, saveToDB = true) => {

                // if (response.success) {
                    if (saveToDB) {
                        IntaoDB.setItem(objStores.ntw_store.name, {
                            taoh_ntw: ntwUserChannelListKey,
                            values: response,
                            timestamp: Date.now()
                        });

                        response.channels.forEach(channel => {
                            console.log(channel.id);
                            let channelid = channel.id;
                            let ntwChannelInfoKey = ['channel', 'info', requestData.roomslug, channelid, requestData.type].filter(Boolean).join('_');

                            let update_data = {
                                channels: [channel],
                                has_more: false,
                                limit: 20,
                                offset: 0
                            };

                            IntaoDB.setItem(objStores.ntw_store.name, {
                                taoh_ntw: ntwChannelInfoKey,
                                values: update_data,
                                timestamp: Date.now()
                            });

                        });

                    }
                    resolve({ requestData, response });
                // } else {
                //     reject("Failed to fetch channel list! Try Again");
                // }
            };

            if (serverFetch) {
                let ntwUserChannelsFormData = {
                    taoh_action: 'taoh_ntw_get_user_channels',
                    roomslug: requestData.roomslug,
                    keyword: requestData.keyword,
                    type: requestData.type,
                    key: requestData.my_pToken,
                    // q: requestData.q || '',
                };

                if(requestData?.channel_ticket_type) {
                    ntwUserChannelsFormData.channel_ticket_type = requestData.channel_ticket_type;
                }

                if(requestData?.global_slug) {
                    ntwUserChannelsFormData.global_slug = requestData.global_slug;
                }

                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'POST',
                    data: ntwUserChannelsFormData,
                    dataType: 'json',
                    success: (response) => handleResponse(response, saveToDB),
                    error: (xhr) => {
                        reject(`Error: ${xhr.status}`)
                    }
                });
            } else {
                IntaoDB.getItem(objStores.ntw_store.name, ntwUserChannelListKey)
                    .then((data) => {
                        if (data?.values) {
                            handleResponse(data.values, false);
                        } else {
                            getNTWUserChannels(requestData, true, saveToDB).then(resolve).catch(reject);
                        }
                    })
                    .catch(reject);
            }
        });
    }
}

async function getNTWChannelById(requestData, serverFetch = false, saveToDB = true) {
    return new Promise(async (resolve, reject) => {
        const {roomslug, keyword, channel_id, type = '1', my_pToken} = requestData || {};
        if (!roomslug || !keyword) {
            return reject(!roomslug ? "Room slug not provided." : "Keyword not provided.");
        }

        if (!channel_id) {
            return reject("Channel ID not provided.");
        }

        const ntwChannelInfoKey = ['channel', 'info', roomslug, channel_id, type].filter(Boolean).join('_');

        // Function to handle the response after fetching or creating channels
        const handleResponse = async (response) => {

            // Channels found, return as is
            if (saveToDB) {
                IntaoDB.setItem(objStores.ntw_store.name, {
                    taoh_ntw: ntwChannelInfoKey,
                    values: response,
                    timestamp: Date.now()
                });
            }

            resolve({requestData, response});
        };

        // Fetch from server or DB
        if (serverFetch) {

            let ntwChannelInfoFormData = {
                taoh_action: 'taoh_ntw_get_channels',
                roomslug,
                keyword,
                channel_id,
                type,
                key: my_pToken,
                q: requestData.q || ''
            };

            if(requestData?.channel_ticket_type) {
                ntwChannelInfoFormData.channel_ticket_type = requestData.channel_ticket_type;
            }

            if(requestData?.global_slug) {
                ntwChannelInfoFormData.global_slug = requestData.global_slug;
            }

            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'POST',
                dataType: 'json',
                data: ntwChannelInfoFormData,
                success: handleResponse,
                error: (xhr) => reject(`Error: ${xhr.status}`)
            });
        } else {

            IntaoDB.getItem(objStores.ntw_store.name, ntwChannelInfoKey)
                .then((data) => {

                    if (data?.values) {
                        handleResponse(data.values);
                    } else {
                        getNTWChannelById(requestData, true, saveToDB).then(resolve).catch(reject);
                    }
                })
                .catch(reject);
        }
    });
}

function addChannelMembers(roomslug, eventToken, channel_id, channel_type, members = []) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'post',
            data: {
                taoh_action: 'taoh_ntw_add_channel_members',
                roomslug: roomslug,
                global_slug: eventToken,
                keyword: ntw_keyword,
                channel_id: channel_id,
                channel_type: channel_type,
                members: members,
                key: my_pToken,
            },
            dataType: 'json',
            success: function (data) {
                console.log("ch_data", data);
                resolve(data);
            },
            error: function (xhr, status, error) {
                resolve({
                    success: false,
                    error: error
                });
            },
            complete: function () {
                // setTimeout(fetchTimestamps, pollingInterval);
            }
        });
    });
}

function removeChannelMembers(roomslug, eventToken, channel_id, channel_type, members = []) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'post',
            data: {
                taoh_action: 'taoh_ntw_remove_channel_members',
                roomslug: roomslug,
                global_slug: eventToken,
                keyword: ntw_keyword,
                channel_id: channel_id,
                channel_type: channel_type,
                members: members,
                key: my_pToken,
            },
            dataType: 'json',
            success: function (data) {
                console.log("ch_data", data);
                resolve(data);
            },
            error: function (xhr, status, error) {
                resolve({
                    success: false,
                    error: error
                });
            },
            complete: function () {
                // setTimeout(fetchTimestamps, pollingInterval);
            }
        });
    });
}

function joinChannel(roomslug, channel_id, channel_type, ptoken = my_pToken) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'post',
            data: {
                taoh_action: 'taoh_ntw_join_channel',
                roomslug: roomslug,
                keyword: ntw_keyword,
                channel_id: channel_id,
                channel_type: channel_type,
                ptoken: ptoken,
                key: my_pToken,
            },
            dataType: 'json',
            success: function (data) {
                console.log("ch_data", data);
                resolve(data);
            },
            error: function (xhr, status, error) {
                resolve({
                    success: false,
                    error: error
                });
            },
            complete: function () {
                // setTimeout(fetchTimestamps, pollingInterval);
            }
        });
    });
}

$(document).on('click', '.join_channel_btn', function (e) {
    e.stopPropagation();

    const currentElem = $(this);
    const currentElemIcon = currentElem.find('i');
    const channelItem = currentElem.closest('.channel-item');
    const channelId = channelItem.getSyncedData('channelid');
    const channelType = channelItem.getSyncedData('channeltype') || 1;

    if (!channelId) {
        console.error('Channel ID not found.');
        return;
    }

    let channelRoomSlug;
    if (channelType == taohChannelExhibitor || channelType == taohChannelSponsor || channelType == taohChannelSession) {
        channelRoomSlug = eventToken;
    } else {
        channelRoomSlug = roomslug;
    }

    currentElem.prop('disabled', true);
    currentElemIcon.removeClass('fa-plus-circle').addClass('fa-spinner fa-spin');
    joinChannel(channelRoomSlug, channelId, channelType, my_pToken).then((response) => {
        if (response.success) {
            const member_add_status = response?.add_status;

            currentElem.removeClass('join_channel_btn').addClass('joined_channel_btn');
            currentElem.html('<i class="fa fa-check mr-1" aria-hidden="true"></i> Joined');
            channelItem.addClass('joined-channel');
            channelItem.find('ul.channel-item-top-badges').prepend(`<li class="join-status text-success">
                <i class="fa fa-check-circle text-success mr-1" aria-hidden="true"></i> Joined</li>`);

            channelItem.find('.view_channel_btn').removeClass('d-none').addClass('d-block');

            getNTWUserChannels({
                roomslug: roomslug,
                global_slug: eventToken,
                keyword: ntw_keyword,
                type: channelType,
                my_pToken
            }, true).then(({requestData, response}) => {
                if (channelType == taohChannelExhibitor) {
                    renderExhibitorChannelList(response.channels);
                } else if (channelType == taohChannelSponsor) {
                    // renderSponsorChannelList(response.channels);
                } else if (channelType == taohChannelSession) {
                    renderSessionChannelList(response.channels);
                } else {
                    renderChannelList(response.channels, tempUserChannelArray);
                }
            }).then(() => {
                if (member_add_status === 'member_added') {
                    const membersViewElem = channelItem.find('.members_view');
                    const new_members_count = (Number(membersViewElem.getSyncedData('count')) || 0) + 1;
                    membersViewElem.setSyncedData('count', new_members_count);
                    membersViewElem.text(`${new_members_count} ${new_members_count === 1 ? 'Member' : 'Members'}`);
                }

                // $(`#channel-${channelId}`).trigger('click');
            });
        } else {
            currentElemIcon.removeClass('fa-spinner fa-spin').addClass('fa-plus-circle');
            console.error('Failed to join channel:', response.error || 'Unknown error');
        }
        currentElem.prop('disabled', false);
    });
});

/*$(document).on('click', '.event_join_channel_btn', function (e) {
    e.stopPropagation();

    const currentElem = $(this);
    const currentElemIcon = currentElem.find('i');
    const channelItem = currentElem.closest('.channel-item');
    const channelId = channelItem.getSyncedData('channelid');
    const channelType = channelItem.getSyncedData('channeltype') || 1;

    if (!channelId) {
        console.error('Channel ID not found.');
        return;
    }

    currentElem.prop('disabled', true);
    currentElemIcon.removeClass('fa-plus-circle').addClass('fa-spinner fa-spin');
    joinChannel(eventToken, channelId, channelType, my_pToken).then((response) => {
        if (response.success) {
            currentElem.removeClass('event_join_channel_btn').addClass('event_joined_channel_btn');
            currentElem.html('<i class="fa fa-check mr-1" aria-hidden="true"></i> Joined');
            channelItem.addClass('joined-channel');
            channelItem.find('ul.channel-item-top-badges').prepend(`<li class="join-status text-success">
                <i class="fa fa-check-circle text-success mr-1" aria-hidden="true"></i> Joined</li>`);

            // channelItem.find('.view_channel_btn').removeClass('d-none').addClass('d-block');

            getNTWUserChannels({roomslug: eventToken, keyword: ntw_keyword, type: channelType, my_pToken}, true).then(({requestData, response}) => {
                if (channelType == taohChannelExhibitor) {
                    renderExhibitorChannelList(response);
                } else if (channelType == taohChannelSponsor) {
                    // renderSponsorChannelList(response);
                } else if (channelType == taohChannelSession) {
                    renderSessionChannelList(response);
                }
            }).then(() => {
                // $(`#channel-${channelId}`).trigger('click');
            });
        } else {
            currentElemIcon.removeClass('fa-spinner fa-spin').addClass('fa-plus-circle');
            console.error('Failed to join channel:', response.error || 'Unknown error');
        }
        currentElem.prop('disabled', false);
    });
});*/

$(document).on('click', '.session_channel_more_info_btn', function (e) {
    e.stopPropagation();

    const currentElem = $(this);
    const speakerId = currentElem.getSyncedData('speakerid');
    const speakerEventToken = currentElem.getSyncedData('eventtoken');

    if (!speakerId || !speakerEventToken) {
        console.error('Speaker ID or Event Token not found.');
        return;
    }

    window.open(`${_taoh_site_url_root}/events/speaker/${speakerEventToken}/${speakerId}`, '_blank');
});

$(document).on('click', '.exh_channel_more_info_btn', function (e) {
    e.stopPropagation();

    const currentElem = $(this);
    const exhibitorId = currentElem.getSyncedData('exhibitorid');
    const exhibitorEventToken = currentElem.getSyncedData('eventtoken');

    if (!exhibitorId || !exhibitorEventToken) {
        console.error('Exhibitor ID or Event Token not found.');
        return;
    }

    window.open(`${_taoh_site_url_root}/events/exhibitors/${exhibitorId}/${exhibitorEventToken}`, '_blank');
});

$(document).on('click', '.sponsor_channel_more_info_btn', function (e) {
    e.stopPropagation();

    const currentElem = $(this);
    const sponsorId = currentElem.getSyncedData('sponsorid');
    const sponsorEventToken = currentElem.getSyncedData('eventtoken');

    if (!sponsorId || !sponsorEventToken) {
        console.error('Sponsor ID or Event Token not found.');
        return;
    }

    window.open(`${_taoh_site_url_root}/events/sponsor/${sponsorId}/${sponsorEventToken}`, '_blank');
});

$(document).on('click', '.open_channel_in_home', async function () {
    const currentElem = $(this);
    const channelId = currentElem.getSyncedData('channelid');
    const channelType = currentElem.getSyncedData('channeltype');
    if (!channelId) {
        console.error('Channel ID not found.');
        return;
    }

    const channelElem = $(`#channel-${channelId}`);
    if (channelElem.length) {
        channelElem.trigger('click');
    } else {
        return; // :rk temporary disable opening channel directly if not joined yet

        // const ntwRoomChannels = ['room', ntw_keyword, roomslug, channelType, 'channels']
        //     .filter(Boolean).join('_');
        // const store = objStores.ntw_store.name;
        // const [channelData] = await getIntaoDataById(store, ntwRoomChannels, channelId);

        /*let channelInfoResponse = await getNTWChannelById({
            roomslug,
            keyword: ntw_keyword,
            channel_id: channelId,
            type: channelType,
            my_pToken
        });
        const channelData = channelInfoResponse?.response?.channels?.find(c => String(c?.id) === channel_id);
        if (channelData) {
            channelData.is_temp = true;
            tempUserChannelArray = [channelData];

            const channelType = channelData.type || 1;

            getNTWUserChannels({roomslug, keyword: ntw_keyword, type: channelType, my_pToken}).then(({requestData, response}) => {
                renderChannelList(response, tempUserChannelArray);
            }).then(() => {
                $(`#channel-${channelId}`).trigger('click');
            });
        }*/
    }
});

async function showChannelInfoModal(channelId, channelType, tabSelector = '') {
    if (channelId && channelType) {
        // let activeTabSlug = $("#browseChannelTab .nav-link.active").attr("data-bc_slug");
        // let ntwRoomChannels = ['room', ntw_keyword, roomslug, activeTabSlug, 'channels'].filter(Boolean).join('_');
        //
        // // let ntwRoomChannels;
        // // if (channelType == taohChannelExhibitor || channelType == taohChannelSponsor || channelType == taohChannelSession) {
        // //     ntwRoomChannels = ['room', ntw_keyword, eventToken, channelType, 'channels'].filter(Boolean).join('_');
        // // } else {
        // //     ntwRoomChannels = ['room', ntw_keyword, roomslug, channelType, 'channels'].filter(Boolean).join('_');
        // // }
        // const store = objStores.ntw_store.name;
        // const [channelData] = await getIntaoDataById(store, ntwRoomChannels, channelId);
        let channelInfoResponse = await getNTWChannelById({
            roomslug,
            global_slug: eventToken,
            keyword: ntw_keyword,
            channel_id: channelId,
            type: channelType,
            my_pToken
        });
        const channelData = channelInfoResponse?.response?.channels?.find(c => String(c?.id) === channelId);

        if (channelData) {
            const channelInfoData = channelData.data || {};
            const channelMembers = channelData.members || [];
            const channelMembersData = channelData.members_data || {};

            let channelCreatedByName = '';
            if (channelInfoData.source === 'admin') {
                channelCreatedByName = 'Admin';
            } else {
                if (channelInfoData.ptoken) {
                    let channelCreatedUserInfo = await getUserInfo(channelInfoData.ptoken);
                    channelCreatedByName = channelCreatedUserInfo.chat_name || 'Admin';
                } else {
                    channelCreatedByName = 'System';
                }
            }

            const channelCreatedTime = Math.floor(channelData.created_at);
            const d = new Date(channelCreatedTime);
            const channelCreatedDTString = formatTimestamp(d, 'LLLL dd, yyyy');

            const membersModal = $('#membersModal');
            membersModal.setSyncedData('channelid', channelId);
            membersModal.setSyncedData('channeltype', channelType);
            membersModal.find('.add_channel_members').setSyncedData('channelid', channelId);
            membersModal.find('.add_channel_members').setSyncedData('channeltype', channelType);
            membersModal.find('.channel_title').html(`${channelInfoData.name}`);
            membersModal.find('.channel_description').html(`${taoh_desc_decode_new(channelInfoData.description)}`);
            membersModal.find('.channel_created_by').text(`${channelCreatedByName} on ${channelCreatedDTString}`);

            const membersListContainer = membersModal.find('#membersList');
            membersListContainer.empty();
            if (channelMembers.length) {
                const maxSkillsToShow = 3;
                for (const member_ptoken of channelMembers) {

                    let memberJson = channelMembersData[member_ptoken];
                    var member = null;
                    if(memberJson) {
                        let parsedMember = JSON.parse(memberJson.trim());
                        member = parsedMember?.output?.user;
                    }
                    if(!member || member == null) {
                        member = await getUserInfo(member_ptoken);
                    }

                    //let member = await getUserInfo(member_ptoken);
                    const memberPtoken = member.ptoken || '';
                    const memberName = member.chat_name || 'Unknown';
                    const fallbackSrc = `${_taoh_ops_prefix}/avatar/PNG/128/${member?.avatar?.trim() || 'default'}.png`;
                    const memberAvatarSrc = buildAvatarImageOptimistic(member.avatar_image, fallbackSrc, (updatedSrc) => {
                        const avatarImgElem = membersListContainer.find(`.profile-pic[data-ptoken="${memberPtoken}"]`);
                        if (avatarImgElem.length && avatarImgElem.attr('src') !== updatedSrc) {
                            avatarImgElem.attr('src', updatedSrc);
                        }
                    });

                    // Split skills into visible and remaining
                    const skills = member.skill
                        ? Object.entries(member.skill).filter(([id, skill]) => skill.value?.trim())
                        : [];

                    const visibleSkills = skills.slice(0, maxSkillsToShow);
                    const remainingSkills = skills.slice(maxSkillsToShow);

                    let skillHTML = visibleSkills.map(([id, skill]) => `<span class="btn skill-list skill_directory" data-skillid="${id}" data-skillslug="${skill.slug}">${skill.value}</span>`).join(' ');

                    if (remainingSkills.length > 0) {
                        // Container for the remaining skills
                        skillHTML += `<span class="remaining-skills-container" style="display: none;">` +
                            remainingSkills.map(([id, skill]) => `<span class="btn skill-list skill_directory" data-skillid="${id}" data-skillslug="${skill.slug}">${skill.value}</span>`).join(' ') +
                            `</span>`;

                        // Add the remaining skill count
                        skillHTML += ` <span class="remaining-skills rounded-pill cursor-pointer" data-count="${remainingSkills.length}" style="color: #6f42c1;">+${remainingSkills.length}</span>`;
                    }

                    const companyContent = member.company ? Object.values(member.company)
                        .filter((company) => company.value?.trim())
                        .map(company => company.value)
                        .join(', ') : '';

                    const roleContent = member.title ? Object.values(member.title)
                        .filter((role) => role.value?.trim())
                        .map(role => role.value)
                        .join(', ') : '';

                    let isFollowing = false;
                    if (Array.isArray(my_following_ptoken_list) && my_following_ptoken_list.includes(memberPtoken)) {
                        isFollowing = true;
                    }

                    const memberItem = $(`
                        <div class="member-list com-v1-strip position-relative px-3 py-2 mb-3 d-flex flex-wrap align-items-center profile-${memberPtoken}" style="gap: 12px;">
                            <a class="d-flex flex-column align-items-center chat-username" style="gap: 3px;" href="javascript:void(0);">
                                <img class="lazy profile-pic" src="${memberAvatarSrc}" alt="${memberName}" data-ptoken="${memberPtoken}">
                                <p class="text-capitalize p-type-badge">${member.type || 'Professional'}</p>
                            </a>

                            <div style="flex: 1;min-width: 230px;">
                                <div class="d-flex flex-wrap flex-xl-nowrap justify-content-between flex-column flex-lg-row align-items-lg-center my-1" style="gap: 12px;">
                                    <div>
                                        <span class="strip-name text-capitalize"> ${memberName} </span>

                                        <div class="d-flex align-items-center flex-wrap lh-1" style="gap: 12px;">
                                            <p class="strip-followers mb-1 d-flex align-items-center">
                                                <svg class="mr-1" width="15" height="11" viewBox="0 0 15 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M2.205 2.94C2.205 2.16026 2.51475 1.41246 3.06611 0.861106C3.61746 0.309749 4.36526 0 5.145 0C5.92474 0 6.67254 0.309749 7.22389 0.861106C7.77525 1.41246 8.085 2.16026 8.085 2.94C8.085 3.71974 7.77525 4.46754 7.22389 5.01889C6.67254 5.57025 5.92474 5.88 5.145 5.88C4.36526 5.88 3.61746 5.57025 3.06611 5.01889C2.51475 4.46754 2.205 3.71974 2.205 2.94ZM0 11.0778C0 8.81541 1.83291 6.9825 4.09533 6.9825H6.19467C8.45709 6.9825 10.29 8.81541 10.29 11.0778C10.29 11.4545 9.98452 11.76 9.60783 11.76H0.682172C0.305484 11.76 0 11.4545 0 11.0778ZM11.5763 7.16625V5.69625H10.1062C9.80077 5.69625 9.555 5.45048 9.555 5.145C9.555 4.83952 9.80077 4.59375 10.1062 4.59375H11.5763V3.12375C11.5763 2.81827 11.822 2.5725 12.1275 2.5725C12.433 2.5725 12.6788 2.81827 12.6788 3.12375V4.59375H14.1488C14.4542 4.59375 14.7 4.83952 14.7 5.145C14.7 5.45048 14.4542 5.69625 14.1488 5.69625H12.6788V7.16625C12.6788 7.47173 12.433 7.7175 12.1275 7.7175C11.822 7.7175 11.5763 7.47173 11.5763 7.16625Z" fill="#555555"></path>
                                                </svg>

                                                <span>
                                                    <span class="mr-2 followers-count-view" data-ptoken="${memberPtoken}" data-fscount="${safeParseInt(member.tao_followers_count, 0)}">${safeParseInt(member.tao_followers_count, 0)} Followers</span>
                                                    <span class="mr-2 following-count-view" data-ptoken="${memberPtoken}" data-fgcount="${safeParseInt(member.tao_following_count, 0)}">${safeParseInt(member.tao_following_count, 0)} Following</span>
                                                </span>
                                            </p>
                                        </div>

                                        ${member.full_location ? `<p class="strip-loc mb-1 d-flex align-items-center">
                                            <svg class="mr-1" width="11" height="13" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="#636161"></path>
                                            </svg>
                                            ${member.full_location}
                                        </p>` : ''}

                                        <p class="strip-company mb-1 d-flex align-items-center lh-1">
                                            ${companyContent.trim() ? `<svg class="mr-1" width="11" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1 0C0.447917 0 0 0.461914 0 1.03125V9.96875C0 10.5381 0.447917 11 1 11H3V9.28125C3 8.71191 3.44792 8.25 4 8.25C4.55208 8.25 5 8.71191 5 9.28125V11H7C7.55208 11 8 10.5381 8 9.96875V1.03125C8 0.461914 7.55208 0 7 0H1ZM1.33333 5.15625C1.33333 4.96719 1.48333 4.8125 1.66667 4.8125H2.33333C2.51667 4.8125 2.66667 4.96719 2.66667 5.15625V5.84375C2.66667 6.03281 2.51667 6.1875 2.33333 6.1875H1.66667C1.48333 6.1875 1.33333 6.03281 1.33333 5.84375V5.15625ZM3.66667 4.8125H4.33333C4.51667 4.8125 4.66667 4.96719 4.66667 5.15625V5.84375C4.66667 6.03281 4.51667 6.1875 4.33333 6.1875H3.66667C3.48333 6.1875 3.33333 6.03281 3.33333 5.84375V5.15625C3.33333 4.96719 3.48333 4.8125 3.66667 4.8125ZM5.33333 5.15625C5.33333 4.96719 5.48333 4.8125 5.66667 4.8125H6.33333C6.51667 4.8125 6.66667 4.96719 6.66667 5.15625V5.84375C6.66667 6.03281 6.51667 6.1875 6.33333 6.1875H5.66667C5.48333 6.1875 5.33333 6.03281 5.33333 5.84375V5.15625ZM1.66667 2.0625H2.33333C2.51667 2.0625 2.66667 2.21719 2.66667 2.40625V3.09375C2.66667 3.28281 2.51667 3.4375 2.33333 3.4375H1.66667C1.48333 3.4375 1.33333 3.28281 1.33333 3.09375V2.40625C1.33333 2.21719 1.48333 2.0625 1.66667 2.0625ZM3.33333 2.40625C3.33333 2.21719 3.48333 2.0625 3.66667 2.0625H4.33333C4.51667 2.0625 4.66667 2.21719 4.66667 2.40625V3.09375C4.66667 3.28281 4.51667 3.4375 4.33333 3.4375H3.66667C3.48333 3.4375 3.33333 3.28281 3.33333 3.09375V2.40625ZM5.66667 2.0625H6.33333C6.51667 2.0625 6.66667 2.21719 6.66667 2.40625V3.09375C6.66667 3.28281 6.51667 3.4375 6.33333 3.4375H5.66667C5.48333 3.4375 5.33333 3.28281 5.33333 3.09375V2.40625C5.33333 2.21719 5.48333 2.0625 5.66667 2.0625Z" fill="#636161"></path>
                                            </svg>
                                            <span>${companyContent}</span>` : ''}

                                            ${roleContent.trim() ? `<svg class="${companyContent.trim() ? 'ml-2' : ''} mr-1" width="11" height="11" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0 0V10H10V0H0ZM7.06473 7.25893L5 9.23884L2.93527 7.25893L4.375 3.15179L2.93527 1.21875H7.0625L5.625 3.15179L7.06473 7.25893Z" fill="#636161"></path>
                                            </svg>
                                            <span>${roleContent}</span>` : ''}
                                        </p>

                                        <div class="skill-con skills-v2-con mt-2">${skillHTML}</div>
                                    </div>
                                    <div class="d-flex bor-btn-con">
                                        ${my_pToken !== memberPtoken ? `
                                        <button type="button" class="btn btn-sm remove_channel_member text-nowrap mr-2 fs-12" data-channelid="${channelId}" data-channeltype="${channelType}" data-ptoken="${memberPtoken}" data-chatname="${memberName}">
                                                    Remove <i class="la la-times-circle ml-1"></i></button>
                                        <button type="button" class="btn btn-sm openchatacc ch_member_open_chat_btn members_chat text-nowrap mr-2 fs-12" data-channel_type="${taohChannelDm}" data-avatar_src="${memberAvatarSrc}" data-channel_name="${memberName}" data-chatwith="${memberPtoken}" data-chatname="${memberName}" data-live="">
                                                    Chat <i class="la la-angle-double-right"></i></button>
                                        <button type="button" class="btn bor-btn profile_follow_btn" data-ptoken="${memberPtoken}" data-follow_status="${isFollowing ? 1 : 0}" data-page="directory" title="${isFollowing ? 'Following' : 'Click to Follow'}">
                                            <i class="fas fa-user-plus fa-sm follow-user-plus-icon" aria-hidden="true"></i>
                                        </button>` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                    membersListContainer.append(memberItem);
                }
            }

            membersModal.modal('show');

            if (tabSelector === 'members') {
                $("#members-tab").tab('show');
            } else {
                $("#ab-channel-tab").tab('show');
            }
        }
    }
}

async function showChannelInfoModalPopup(channelId, channelType, tabSelector = '', activeTabSlug) {
    if (channelId && channelType) {
        // let activeTabSlug = $("#browseChannelTab .nav-link.active").attr("data-bc_slug");
        // let ntwRoomChannels = ['room', ntw_keyword, roomslug, activeTabSlug, 'channels'].filter(Boolean).join('_');
        //
        // // let ntwRoomChannels;
        // // if (channelType == taohChannelExhibitor || channelType == taohChannelSponsor || channelType == taohChannelSession) {
        // //     ntwRoomChannels = ['room', ntw_keyword, eventToken, channelType, 'channels'].filter(Boolean).join('_');
        // // } else {
        // //     ntwRoomChannels = ['room', ntw_keyword, roomslug, channelType, 'channels'].filter(Boolean).join('_');
        // // }
        // const store = objStores.ntw_store.name;
        // const [channelData] = await getIntaoDataById(store, ntwRoomChannels, channelId);

        let channelInfoResponse = await getNTWChannelById({
            roomslug,
            global_slug: eventToken,
            keyword: ntw_keyword,
            channel_id: channelId,
            type: channelType,
            my_pToken
        });
        const channelData = channelInfoResponse?.response?.channels?.find(c => String(c?.id) === channelId);

        if (channelData) {
            const channelInfoData = channelData.data || {};
            const channelMembers = channelData.members || [];

            let channelCreatedByName = '';
            if (parseInt(channelData?.isDefault, 10) === 1) {
                channelCreatedByName = 'Admin';
            } else {
                if (channelInfoData.ptoken) {
                    let channelCreatedUserInfo = await getUserInfo(channelInfoData.ptoken);
                    channelCreatedByName = channelCreatedUserInfo.chat_name || 'Admin';
                }
                channelCreatedByName = 'Admin';
            }

            const channelCreatedTime = Math.floor(channelData.created_at);
            const d = new Date(channelCreatedTime);
            const channelCreatedDTString = formatTimestamp(d, 'LLLL dd, yyyy');

            const membersModal = $('#membersModal');
            membersModal.setSyncedData('channelid', channelId);
            membersModal.setSyncedData('channeltype', channelType);
            membersModal.find('.add_channel_members').setSyncedData('channelid', channelId);
            membersModal.find('.add_channel_members').setSyncedData('channeltype', channelType);
            membersModal.find('.channel_title').text(`${channelInfoData.name}`);
            membersModal.find('.channel_description').text(`${taoh_desc_decode_new(channelInfoData.description)}`);
            membersModal.find('.channel_created_by').text(`${channelCreatedByName} on ${channelCreatedDTString}`);

            const membersListContainer = membersModal.find('#membersList');
            membersListContainer.empty();
            if (channelMembers.length) {
                const maxSkillsToShow = 3;
                for (const member_ptoken of channelMembers) {
                    let member = await getUserInfo(member_ptoken);
                    const memberName = member.chat_name || 'Unknown';
                    const fallbackSrc = `${_taoh_ops_prefix}/avatar/PNG/128/${member?.avatar?.trim() || 'default'}.png`;
                    const memberAvatarSrc = buildAvatarImageOptimistic(member.avatar_image, fallbackSrc, (updatedSrc) => {
                        const avatarImgElem = membersListContainer.find(`.profile-pic[data-ptoken="${member.ptoken}"]`);
                        if (avatarImgElem.length && avatarImgElem.attr('src') !== updatedSrc) {
                            avatarImgElem.attr('src', updatedSrc);
                        }
                    });

                    // Split skills into visible and remaining
                    const skills = member.skill
                        ? Object.entries(member.skill).filter(([id, skill]) => skill.value?.trim())
                        : [];

                    const visibleSkills = skills.slice(0, maxSkillsToShow);
                    const remainingSkills = skills.slice(maxSkillsToShow);

                    let skillHTML = visibleSkills.map(([id, skill]) => `<span class="btn skill-list skill_directory" data-skillid="${id}" data-skillslug="${skill.slug}">${skill.value}</span>`).join(' ');

                    if (remainingSkills.length > 0) {
                        // Container for the remaining skills
                        skillHTML += `<span class="remaining-skills-container" style="display: none;">` +
                            remainingSkills.map(([id, skill]) => `<span class="btn skill-list skill_directory" data-skillid="${id}" data-skillslug="${skill.slug}">${skill.value}</span>`).join(' ') +
                            `</span>`;

                        // Add the remaining skill count
                        skillHTML += ` <span class="remaining-skills rounded-pill cursor-pointer" data-count="${remainingSkills.length}" style="color: #6f42c1;">+${remainingSkills.length}</span>`;
                    }

                    const companyContent = member.company ? Object.values(member.company)
                        .filter((company) => company.value?.trim())
                        .map(company => company.value)
                        .join(', ') : '';

                    const roleContent = member.title ? Object.values(member.title)
                        .filter((role) => role.value?.trim())
                        .map(role => role.value)
                        .join(', ') : '';

                    let isFollowing = false;
                    if (Array.isArray(my_following_ptoken_list) && my_following_ptoken_list.includes(member.ptoken)) {
                        isFollowing = true;
                    }

                    const memberItem = $(`
                        <div class="member-list com-v1-strip position-relative px-3 py-2 mb-3 d-flex flex-wrap align-items-center profile-${member.ptoken}" style="gap: 12px;">
                            <a class="d-flex flex-column align-items-center chat-username" style="gap: 3px;" href="javascript:void(0);">
                                <img class="lazy profile-pic" src="${memberAvatarSrc}" alt="${memberName}" data-ptoken="${member.ptoken}">
                                <p class="text-capitalize p-type-badge">${member.type || 'Professional'}</p>
                            </a>

                            <div style="flex: 1;min-width: 230px;">
                                <div class="d-flex flex-wrap flex-xl-nowrap justify-content-between flex-column flex-lg-row align-items-lg-center my-1" style="gap: 12px;">
                                    <div>
                                        <span class="strip-name text-capitalize"> ${memberName} </span>

                                        <div class="d-flex align-items-center flex-wrap lh-1" style="gap: 12px;">
                                            <p class="strip-followers mb-1 d-flex align-items-center">
                                                <svg class="mr-1" width="15" height="11" viewBox="0 0 15 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M2.205 2.94C2.205 2.16026 2.51475 1.41246 3.06611 0.861106C3.61746 0.309749 4.36526 0 5.145 0C5.92474 0 6.67254 0.309749 7.22389 0.861106C7.77525 1.41246 8.085 2.16026 8.085 2.94C8.085 3.71974 7.77525 4.46754 7.22389 5.01889C6.67254 5.57025 5.92474 5.88 5.145 5.88C4.36526 5.88 3.61746 5.57025 3.06611 5.01889C2.51475 4.46754 2.205 3.71974 2.205 2.94ZM0 11.0778C0 8.81541 1.83291 6.9825 4.09533 6.9825H6.19467C8.45709 6.9825 10.29 8.81541 10.29 11.0778C10.29 11.4545 9.98452 11.76 9.60783 11.76H0.682172C0.305484 11.76 0 11.4545 0 11.0778ZM11.5763 7.16625V5.69625H10.1062C9.80077 5.69625 9.555 5.45048 9.555 5.145C9.555 4.83952 9.80077 4.59375 10.1062 4.59375H11.5763V3.12375C11.5763 2.81827 11.822 2.5725 12.1275 2.5725C12.433 2.5725 12.6788 2.81827 12.6788 3.12375V4.59375H14.1488C14.4542 4.59375 14.7 4.83952 14.7 5.145C14.7 5.45048 14.4542 5.69625 14.1488 5.69625H12.6788V7.16625C12.6788 7.47173 12.433 7.7175 12.1275 7.7175C11.822 7.7175 11.5763 7.47173 11.5763 7.16625Z" fill="#555555"></path>
                                                </svg>

                                                <span>
                                                    <span class="mr-2 followers-count-view" data-ptoken="${member.ptoken}" data-fscount="${safeParseInt(member.tao_followers_count, 0)}">${safeParseInt(member.tao_followers_count, 0)} Followers</span>
                                                    <span class="mr-2 following-count-view" data-ptoken="${member.ptoken}" data-fgcount="${safeParseInt(member.tao_following_count, 0)}">${safeParseInt(member.tao_following_count, 0)} Following</span>
                                                </span>
                                            </p>
                                        </div>

                                        ${member.full_location ? `<p class="strip-loc mb-1 d-flex align-items-center">
                                            <svg class="mr-1" width="11" height="13" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="#636161"></path>
                                            </svg>
                                            ${member.full_location}
                                        </p>` : ''}

                                        <p class="strip-company mb-1 d-flex align-items-center lh-1">
                                            ${companyContent.trim() ? `<svg class="mr-1" width="11" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1 0C0.447917 0 0 0.461914 0 1.03125V9.96875C0 10.5381 0.447917 11 1 11H3V9.28125C3 8.71191 3.44792 8.25 4 8.25C4.55208 8.25 5 8.71191 5 9.28125V11H7C7.55208 11 8 10.5381 8 9.96875V1.03125C8 0.461914 7.55208 0 7 0H1ZM1.33333 5.15625C1.33333 4.96719 1.48333 4.8125 1.66667 4.8125H2.33333C2.51667 4.8125 2.66667 4.96719 2.66667 5.15625V5.84375C2.66667 6.03281 2.51667 6.1875 2.33333 6.1875H1.66667C1.48333 6.1875 1.33333 6.03281 1.33333 5.84375V5.15625ZM3.66667 4.8125H4.33333C4.51667 4.8125 4.66667 4.96719 4.66667 5.15625V5.84375C4.66667 6.03281 4.51667 6.1875 4.33333 6.1875H3.66667C3.48333 6.1875 3.33333 6.03281 3.33333 5.84375V5.15625C3.33333 4.96719 3.48333 4.8125 3.66667 4.8125ZM5.33333 5.15625C5.33333 4.96719 5.48333 4.8125 5.66667 4.8125H6.33333C6.51667 4.8125 6.66667 4.96719 6.66667 5.15625V5.84375C6.66667 6.03281 6.51667 6.1875 6.33333 6.1875H5.66667C5.48333 6.1875 5.33333 6.03281 5.33333 5.84375V5.15625ZM1.66667 2.0625H2.33333C2.51667 2.0625 2.66667 2.21719 2.66667 2.40625V3.09375C2.66667 3.28281 2.51667 3.4375 2.33333 3.4375H1.66667C1.48333 3.4375 1.33333 3.28281 1.33333 3.09375V2.40625C1.33333 2.21719 1.48333 2.0625 1.66667 2.0625ZM3.33333 2.40625C3.33333 2.21719 3.48333 2.0625 3.66667 2.0625H4.33333C4.51667 2.0625 4.66667 2.21719 4.66667 2.40625V3.09375C4.66667 3.28281 4.51667 3.4375 4.33333 3.4375H3.66667C3.48333 3.4375 3.33333 3.28281 3.33333 3.09375V2.40625ZM5.66667 2.0625H6.33333C6.51667 2.0625 6.66667 2.21719 6.66667 2.40625V3.09375C6.66667 3.28281 6.51667 3.4375 6.33333 3.4375H5.66667C5.48333 3.4375 5.33333 3.28281 5.33333 3.09375V2.40625C5.33333 2.21719 5.48333 2.0625 5.66667 2.0625Z" fill="#636161"></path>
                                            </svg>
                                            <span>${companyContent}</span>` : ''}

                                            ${roleContent.trim() ? `<svg class="${companyContent.trim() ? 'ml-2' : ''} mr-1" width="11" height="11" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0 0V10H10V0H0ZM7.06473 7.25893L5 9.23884L2.93527 7.25893L4.375 3.15179L2.93527 1.21875H7.0625L5.625 3.15179L7.06473 7.25893Z" fill="#636161"></path>
                                            </svg>
                                            <span>${roleContent}</span>` : ''}
                                        </p>

                                        <div class="skill-con skills-v2-con mt-2">${skillHTML}</div>
                                    </div>
                                    <div class="d-flex bor-btn-con">
                                        ${my_pToken !== member.ptoken ? `
                                        <button type="button" class="btn btn-sm openchatacc members_chat text-nowrap mr-2 fs-12" data-chatwith="${member.ptoken}" data-channel_type="${taohChannelDm}" data-avatar_src="${memberAvatarSrc}" data-channel_name="${memberName}" data-chatname="${memberName}" data-live="">
                                                    Chat <i class="la la-angle-double-right"></i></button>
                                        <button type="button" class="btn bor-btn profile_follow_btn" data-ptoken="${member.ptoken}" data-follow_status="${isFollowing ? 1 : 0}" data-page="directory" title="${isFollowing ? 'Following' : 'Click to Follow'}">
                                            <i class="fas fa-user-plus fa-sm follow-user-plus-icon" aria-hidden="true"></i>
                                        </button>` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                    membersListContainer.append(memberItem);
                }
            }

            membersModal.modal('show');

            if (tabSelector === 'members') {
                $("#members-tab").tab('show');
            } else {
                $("#ab-channel-tab").tab('show');
            }
        }
    }
}

$(document).on('click', '.channel_info_view', async function (e) {
    e.stopPropagation();
    const currentElem = $(this);
    const channelItem = currentElem.closest('.channel-item');
    const channelId = channelItem.getSyncedData('channelid');
    const channelType = channelItem.getSyncedData('channeltype') || 1;

    showChannelInfoModal(channelId, channelType, 'about');
});

$(document).on('click', '.members_view', async function (e) {
    e.stopPropagation();
    const currentElem = $(this);
    const channelItem = currentElem.closest('.channel-item');
    const channelId = channelItem.getSyncedData('channelid');
    const channelType = channelItem.getSyncedData('channeltype') || 1;

    showChannelInfoModal(channelId, channelType, 'members');
});

function showAddChannelMembersModal(channelId, channelType) {
    const addChannelMembersModal = $('#addChannelMembersModal');

    if (channelId && channelType) {
        addChannelMembersModal.find('#members_to_add').val([]).trigger('change');
        addChannelMembersModal.find('input[name="channel_id"]').val(channelId);
        addChannelMembersModal.find('input[name="channel_type"]').val(channelType);
        addChannelMembersModal.modal('show');
    }
}

$(document).on("click", ".add_channel_members", function(e) {
    const currentElem = $(this);
    const channelId = currentElem.getSyncedData('channelid');
    const channelType = currentElem.getSyncedData('channeltype') || 1;

    taoh_load_network_entries();
    showAddChannelMembersModal(channelId, channelType);
});

$(document).on("click", ".remove_channel_member", function(e) {
    const currentElem = $(this);
    const ptoken = currentElem.getSyncedData('ptoken');
    const channelId = currentElem.getSyncedData('channelid');
    const channelType = currentElem.getSyncedData('channeltype') || 1;

    removeChannelMembers(roomslug, eventToken, channelId, channelType, [ptoken]).then((response) => {
        if(response.success) {
            // Clear relevant cache
            const ntwChannelInfoKey = ['channel', 'info', roomslug, channelId, channelType].filter(Boolean).join('_');
            IntaoDB.removeItem(objStores.ntw_store.name, ntwChannelInfoKey);

            currentElem.closest('.member-list').remove();
            $('#browseMoreChannels').trigger('click');
        }
    });
});

$(document).on("click", ".star_channel", function(e) {
    const currentElem = $(this);
    const membersModal = $('#membersModal');
    const channelId = membersModal.getSyncedData('channelid');
    const channelType = membersModal.getSyncedData('channeltype') || 1;

    if (channelId && channelType) {
        console.log('Starring channel:', channelId, channelType);
    }
});

$(document).on("click", ".leave-btn", function(e) {
    e.stopPropagation();
    leave_channel($(this).data("channel"));
});


if (typeof getRoomStamp !== 'function') {
    function getRoomStamp(requestData, serverFetch = false, saveToDB = true) {  // :rk need to remove serverFetch nd saveToDB
        return new Promise((resolve, reject) => {
            if (!requestData.roomslug || !requestData.keyword) {
                reject(!requestData.roomslug ? "Room slug not provided." : "Keyword not provided.");
                return;
            }

            const ntwRoomStamp = `room_${requestData.keyword}_${requestData.roomslug}_stamp`;

            const handleResponse = (response, saveToDB = true) => {
                // if (response.success) {
                if (saveToDB) {
                    IntaoDB.setItem(objStores.ntw_store.name, {
                        taoh_ntw: ntwRoomStamp,
                        values: response,
                        timestamp: Date.now()
                    });
                }
                resolve({ requestData, response });
                // } else {
                //     reject("Failed to fetch channel list! Try Again");
                // }
            };

            if (serverFetch) {
                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'POST',
                    data: {
                        taoh_action: 'taoh_ntw_get_room_stamp',
                        roomslug: requestData.roomslug,
                        keyword: requestData.keyword,
                        key: '1', // requestData.my_pToken
                    },
                    dataType: 'json',
                    success: (response) => handleResponse(response, saveToDB),
                    error: (xhr) => {
                        reject(`Error: ${xhr.status}`)
                    }
                });
            } else {
                IntaoDB.getItem(objStores.ntw_store.name, ntwRoomStamp)
                    .then((data) => {
                        if (data?.values) {
                            handleResponse(data.values, false);
                        } else {
                            getRoomStamp(requestData, true, saveToDB).then(resolve).catch(reject);
                        }
                    })
                    .catch(reject);
            }
        });
    }
}

if (typeof getRoomChannelStamp !== 'function') {
    function getRoomChannelStamp(requestData, serverFetch = false, saveToDB = true) {  // :rk need to remove serverFetch nd saveToDB
        return new Promise((resolve, reject) => {
            if (!requestData.roomslug || !requestData.keyword) {
                reject(!requestData.roomslug ? "Room slug not provided." : "Keyword not provided.");
                return;
            }

            if (!requestData.channel_type) {
                reject("Channel type not provided.");
                return;
            }

            const ntwRoomChannelStamp = `room_${requestData.keyword}_${requestData.roomslug}_${requestData.channel_type}_channel_stamp`;

            const handleResponse = (response, saveToDB = true) => {
                // if (response.success) {
                if (saveToDB) {
                    IntaoDB.setItem(objStores.ntw_store.name, {
                        taoh_ntw: ntwRoomChannelStamp,
                        values: response,
                        timestamp: Date.now()
                    });
                }
                resolve({ requestData, response });
                // } else {
                //     reject("Failed to fetch channel list! Try Again");
                // }
            };

            if (serverFetch) {
                let ntwChannelsStampFormData = {
                    taoh_action: 'taoh_ntw_get_room_channel_stamp',
                    roomslug: requestData.roomslug,
                    keyword: requestData.keyword,
                    channel_type: requestData.channel_type,
                    timestamp: requestData.timestamp || 0,
                    key: requestData.my_pToken,
                };

                if(requestData?.global_slug) {
                    ntwChannelsStampFormData.global_slug = requestData.global_slug;
                }

                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'POST',
                    data: ntwChannelsStampFormData,
                    dataType: 'json',
                    success: (response) => handleResponse(response, saveToDB),
                    error: (xhr) => {
                        reject(`Error: ${xhr.status}`)
                    }
                });
            } else {
                IntaoDB.getItem(objStores.ntw_store.name, ntwRoomChannelStamp)
                    .then((data) => {
                        if (data?.values) {
                            handleResponse(data.values, false);
                        } else {
                            getRoomChannelStamp(requestData, true, saveToDB).then(resolve).catch(reject);
                        }
                    })
                    .catch(reject);
            }
        });
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
                targetElement = $(`.dmChannelList li#dm-${channelId} .unread-count`);
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

    if (typeof ntwuserInfoList === "undefined") {
        ntwuserInfoList = {};
    }

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

    // if (userInfo.ptoken === sidekick_ptoken && sidekick_avatar) {
    //     userInfo.avatar_image = sidekick_avatar;
    // }

    return userInfo;
}

async function getIntaoDataById(store, key, ids) {
    const item = await IntaoDB.getItem(store, key);
    let vals = item?.values;
    if (typeof vals === 'string') {
        try { vals = JSON.parse(vals); } catch { return []; }
    }

    const channels = Array.isArray(vals?.channels) ? vals.channels : [];
    if (ids == null || (Array.isArray(ids) && ids.length === 0)) return [];

    const idSet = new Set((Array.isArray(ids) ? ids : [ids]).map(String));
    return channels.filter(ch => ch && idSet.has(String(ch.id)));
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

        if (mention_ptoken != '') {
            return `<span class="conversation-mention test1" data-ptoken="${mention_ptoken}">@${username}</span>`;
        } else {
            // keep the original text if no token found
            return match;
        }

        // No link if no valid ptoken
        //return `<span class="conversation-mention" data-ptoken="${mention_ptoken}">@${username}</span>`;
    });
}

function addRemoveActive(currentElem = false) {
    $('.channelList li.active').removeClass('active');
    $('.dmChannelList li.active').removeClass('active');
    $('.exhibitorChannelList li.active').removeClass('active');
    $('.sponsorChannelList li.active').removeClass('active');
    $('.sessionChannelList li.active').removeClass('active');
    $('.organizerChannelList li.active').removeClass('active');
    $('#sidekickList li.active').removeClass('active');
    $('#browseMoreChannels.active').removeClass('active');

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

function getPeerPToken(channelMembers, my_pToken) {
    const list = Array.isArray(channelMembers) ? channelMembers : [];
    return list.find(m => String(m) !== String(my_pToken)) ?? null;
}

function toggleCollapseIcon(listSelector, iconSelector) {
    const $list = $(listSelector);
    const $icon = $(iconSelector);

    if ($list.find('.channel-item').length > 0) {
        $icon.show();
    } else {
        $icon.hide();
    }
}

function resetFormIfExists(selector) {
    const form = document.querySelector(selector);
    if (form) form.reset();
}

function get_today_date() {
    const d = new Date();
    return `${d.getMonth() + 1}-${d.getDate()}-${d.getFullYear()}`;
}

/*======================== /Helper Functions ==========================*/