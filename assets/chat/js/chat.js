let ntwuserInfoList = {};
let ntwroomInfoList = {};

$(document).ready(function () {
    createChannelFromTicketType();

    setInterval(() => {
        taoh_get_activities();
    }, 60000);

    setTimeout(function () {
        taoh_get_mood_status();
        taoh_get_activities();
    }, 1000);

    $('#channel_refresh').on('click', function () {
        loader(true, loaderArea);
        $('.speed_networking_hints').hide();
        loadChannelList(1);
        const channelLiElem = selectedChannel
            ? $('.channelList li[data-channel_id="' + selectedChannel + '"]')
            : $();

        (channelLiElem.length ? channelLiElem.click() : '');
            //$('.channelList li').first()).click();
    });

    $('.create_channel').on('click', function () {

        $('#createChannelModal').modal('show');
    });

    if (my_pToken && ntw_room_key) {
        const fetchAndInitialize = (keyPrefix, timestampKey, requestType) => {
            const storageKey = `${keyPrefix}_${ntw_room_key}`;
            IntaoDB.getItem(objStores.ntw_store.name, storageKey).then((intao_data) => {
                const timestamp = intao_data?.last_update_time || 0;
                getAndSetLastCheckedTimestamp(timestampKey, timestamp, 2);
            }).then(() => initializeRequest(requestType, ntw_room_key, my_pToken));
        };

        // Initial Entry - Forum
        fetchAndInitialize('ft_frm_networking_misc', 'lastFRMMsgCheckedTimestamp', 'channel');

        // Initial Entry - Direct Message
        fetchAndInitialize('ft_ntw_networking_misc', 'lastFRMMsgCheckedTimestamp', 'direct_message');
    }

    setInterval(function () {
        updateUnreadCountOld('interval');
    }, 2000);

    //alert(layout)
    if (layout == 1) { // hide agree modal on page load
        var date_last_agree_model = localStorage.getItem('date_last_agree_' + ntw_room_key);
        var current_date = get_today_date();
        if (date_last_agree_model == '' || date_last_agree_model == null) {
            // $('#agreeModal').modal('show');
            const modalEl = document.getElementById('v-overlay');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            localStorage.setItem('date_last_agree_' + ntw_room_key, current_date);
        } else if (date_last_agree_model != current_date) {
            // $('#agreeModal').modal('show');
            const modalEl = document.getElementById('v-overlay');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            localStorage.setItem('date_last_agree_' + ntw_room_key, current_date);
        } else {
            setTimeout(initNetworkingTour, 3000);
        }
    }

    $(document).on('click', '.channelList .channel_btn', async function () {
        
        const currentElem = $(this).closest('li');
        selectedChat = 'channel';
        $('.chat-like-sidebar').css('display', 'none');

        let channelId = currentElem.data('channel_id');
        
        $('[class*="pin_message_div"]').removeClass('d-flex').addClass('d-none');
        $('[class*="pin_message_div-dm"]').removeClass('d-flex').addClass('d-none');
        $(`.pin_message_div${channelId}`).removeClass('d-none').addClass('d-flex');
        
        if (!channelId) return;

        let channelSlug = currentElem.data('channel_ticket_slug');
        if(channelSlug !='' && channelSlug != 'undefined' && channelSlug != undefined && channelSlug != null){
            if(channelSlug != rsvped_ticket )
            {
                jq_confirm_alert('Warning', 'You can not access this channel. ', 'orange', 'Ok');
                return;
            }
        }    
    
        let channelPasscode = currentElem.data('channel_passcode');
        let channel_passcode_done = 'passcode_done_'+currentElem.data('channel_id');

        if (channelPasscode?.trim() && localStorage.getItem(channel_passcode_done) != 1) {
            $('#channel_password_channel_id').val(channelId);
            $('#channel_password_modal').modal('show');
        } else {
            await processChannelClick(currentElem);
        }
        

            /*if(localStorage.getItem(channel_passcode_done) != 1

            && channelPasscode !='' && channelPasscode != 'undefined' && channelPasscode != undefined && channelPasscode != null){
            
            await  $.confirm({
                    title: '',
                    type: 'orange',
                    content: '' +
                        '<form action="" class="formName">' +
                        '<div class="form-group">' +
                        'Password Protected Channel' +
                        '<br><br><input type="text" placeholder="Enter the passcode" id="passcode" class="form-control" required />'+
                        '<span class="text-danger" style="display:none;" id="passcode_error">Incorrect passcode</span>' +
                        '</div>' +
                        '</form>',
                    buttons: {
                        formSubmit: {
                            text: 'Submit',
                            btnClass: 'btn-blue',
                            action: function () {
                                $('#passcode_error').hide();
                                var passcode = this.$content.find('#passcode').val();
                                if (!passcode) {
                                    $('#passcode').addClass('is-invalid error');
                                    
                                    return false; // prevent modal from closing
                                } else {
                                    $('#passcode').removeClass('is-invalid');
                                }
                                
                                //alert(passcode,'------------',channelPasscode)
                                if (passcode != decodeBase64(channelPasscode)) {
                                    $('#passcode_error').show();
                                    $('#passcode').addClass('is-invalid error');
                                    return false; // prevent modal from closing
                                }
                                else{
                                    $('#passcode').removeClass('is-invalid');
                                    localStorage.setItem(channel_passcode_done, 1);
                                    processChannelClick(currentElem);
                                }
                                
                            }
                        },
                        cancel: function () {
                        // Close the modal
                        }
                    },
                    onContentReady: function () {
                        // bind to events
                        var jc = this;
                        this.$content.find('form').on('submit', function (e) {
                        e.preventDefault();
                        jc.$$formSubmit.trigger('click');
                        }); // trigger submit button click
                        
                        
                    }
                });
        }
        else{
            processChannelClick(currentElem);
        }*/

        
        /*var track_data = {
            'action': 'click_channel',
            'channel_id': channelId,
            'channel_name': channelName,
            'ptoken': my_pToken
        };
        taoh_track_activities(track_data);*/
        
    });

    $('#channel_password_form').on('submit', async function (e) {
        e.preventDefault();

        const channelId = $('#channel_password_channel_id').val();
        const currentElem = $(`#channel-${channelId}`);

        const channelPasscode = currentElem.data('channel_passcode');
        const channel_passcode_done = `passcode_done_${currentElem.data('channel_id')}`;

        $('#passcode_error').hide();
        $('#passcode').removeClass('is-invalid error');

        const passcode = $('#passcode').val();
        if (!passcode) {
            $('#passcode').addClass('is-invalid error');
            return false;
        }

        if (passcode !== decodeBase64(channelPasscode)) {
            $('#passcode_error').show();
            $('#passcode').addClass('is-invalid error');
            return false;
        }

        localStorage.setItem(channel_passcode_done, 1);
        $('#channel_password_modal').modal('hide');

        try {
            await processChannelClick(currentElem);
        } catch (error) {
            console.error('Error processing channel click:', error);
        }
    });
});


$(document).on('click', '.openchatacc', async function () {
    const currentElem = $(this);
    // let chatWindow = currentElem.closest('.network_entries').data('name');
    chatwith = currentElem.data("chatwith").toString();

    $('.' + chatwith + '_loader').addClass('show');
    $(this).hide();

    const input = [ntw_room_key, my_pToken, chatwith].sort().join('_');
    var channel_Id = await generateSecureSlug(input, 16);

    const targetUserLi = $('.usersList').find('#dm-' + channel_Id);
    if (targetUserLi.length) {
        targetUserLi.trigger('click');
    } else {
        // alert(2);
        loader(true, loaderArea);
        //await createOnetoOneChannel(chatwith,channel_Id);
        await loadDirectMessage(chatwith);
        loader(false, loaderArea);

    }
});


    async function processChannelClick(currentElem){

            $('#user-chat').removeClass('mobile-transform');
            $('#channel_of_type').val('channel');


            let chatWindow = currentElem.data('name');
            let channelId = currentElem.data('channel_id');

             

            if (!channelId  || channelId.trim() === '') {
                const input = [ntw_room_key, my_pToken, 'organizer'].sort().join('_');
                channelId = await generateSecureSlug(input, 16);

                await createOrganizerChannel(channelId);
                selectedChat = 'organizer';
            }


            // if (!channelId) return;

             let channelName = currentElem.find('.channel_name').text();
            let membersCount = currentElem.find('.members_count').data('count');

            chat_activeTabId = chatWindow; // for footer fn

            console.log('-------------',channelInfoData)
            addRemoveActive(currentElem);
            var channelInfoVal = channelInfoData[channelId];

            if(channelInfoVal != undefined && channelInfoVal != null && channelInfoVal != ''){
                $('.channnel_collapsible').html(channelInfoVal.description);

                var video_html = '';
                if (channelInfoVal.channel_video_url != undefined && channelInfoVal.channel_video_url != '' && channelInfoVal.channel_video == 1) {
                    video_html = `<a href="${channelInfoVal.channel_video_url}" target="_blank" class="btn nav-btn chat-video"><i class="bx bx-video"></i></a>`;
                } else if (channelInfoVal.channel_video != undefined && channelInfoVal.channel_video == 1) {
                    video_html = `<button type="button" class="btn nav-btn chat-video" id="channel-chat-video" data-type="1"><i class="bx bx-video"></i></button`;
                }
                $('#channel_video_btn').html(video_html);
                $('#channel_video_btn').show();

            }
           // console.log(channelInfoVal);
            $('#channel-chat').setSyncedData('channel_id', channelId);
            
            $('.cw_channel_title').text(channelName);
            $('.channnel_collapsible').removeClass('open');
            $('.channel_toggle').attr('toggle_text','open')
            $('.channel-drp-dwn-svg').css('transform', 'rotate(0deg)');
            
            $('.cw_channel_icon .username').text(getInitials(channelName));

            if (membersCount > 0)
                $('.channel_members_count').text(membersCount + ' Member(s)');
            else
                $('.channel_members_count').text('');

            frm_asm_queue = {};
            frm_asm_current_index = 0;
            frm_asm_indexes = [...frm_asm_actual_indexes];

            selectedUser = '';
            selectedChannel = channelId;
            selectedChannelName = channelName;
            console.log('----chat_window-----------2')
            loadchatWindow(chatWindow);
            updateForumWindow();
            
            if(selectedChat == 'channel'){
                loadRightSidebar('members');
                showMembersList(channelId, channelName);
            }
            

            channelConversationList.awloader('show');
            clearUnreadCount(ntw_room_key, channelId, 0);

            

        }
        $(document).on('click', '.orgChannelList .channel_btn', async function () {

            selectedChat = 'organizer';
            const currentElem = $(this).closest('li');
            
            await processChannelClick(currentElem);

         });
        
        $(document).on('click', '.watchpartyChannel .channel_btn', async function () {

            selectedChat = 'watch-party';
            const currentElem = $(this).closest('li');
            $('.watchPartySection').show();
            await processChannelClick(currentElem);

        });        


        
$(document).on('click', '.usersList li', function () {
    $('#user-chat').removeClass('mobile-transform');
    $('.chat-like-sidebar').css('display', 'none');
    $('#channel_of_type').val('user');
    const currentElem = $(this);
    let chatWindow = currentElem.data('name');
    let channelId = currentElem.data('channel_id');
    let channelName = currentElem.find('.chat-username').text();

    $('#welcome-page').hide();

    $('[class*="pin_message_div"]').removeClass('d-flex').addClass('d-none');
    $('[class*="pin_message_div-dm"]').removeClass('d-flex').addClass('d-none');
    $(`.pin_message_div-dm${channelId}`).removeClass('d-none').addClass('d-flex');

    chat_activeTabId = chatWindow; // for footer fn

    chatwith = currentElem.data('ptoken').toString();

    $('#user-chat-topbar-info').removeClass('d-none');
    $('.chat-input-bottom').removeClass('d-none').addClass('d-flex');

    //alert(channelName);
    //alert(chatwith);
    if (ntw_room_key ) {
        addRemoveActive(currentElem);
        $('#user_name').text(channelName);

        loadDirectMessage(chatwith, channelId,chatWindow);
    }
});

$(document).on('click', '.teamDojoCall', function (e) {
    //alert();
    if (typeof side_openForm === 'function') {
        side_openForm(e,'open');
    }
});

$(document).on('click', '#sidekickList li', function () {
    if (typeof side_openForm === 'function') {
        side_openForm();
    } else {
        $('#user-chat').removeClass('mobile-transform');
        const currentElem = $(this);

        let chatWindow = 'dm'
        let channelId = 'sidekick';
        let channelName = 'SideKick';

        chat_activeTabId = chatWindow; // for footer fn

        chatwith = currentElem.data('ptoken').toString();

        if (ntw_room_key && channelId) {
            addRemoveActive(currentElem);
            $('#user_name').text(channelName);

            loadDirectMessage(chatwith, channelId);
        }
    }
});

$('#chatForm').on('submit', function (e) {

    e.preventDefault();

    let chatForm = $(this);
    let message = chatForm.find('#chat_input').val();

    simpleBarScrollToBottom('#chat-conversation-direct');

    if (chatWindow === 'channel') {
        const channel_id = $('#channel-chat').data('channel_id');
        sendFRMChat(message, my_pToken, 0, ntw_room_key, channel_id, 'user');
        clearUnreadCount(ntw_room_key, channel_id, 1);
    } else if (chatWindow === 'direct_message') {
        const usersChat = $('#users-chat');
        const channel_id = usersChat.data('channel_id');
        const toToken = usersChat.data('chatwith');
        sendNTWChat(message, my_pToken, toToken, ntw_room_key, channel_id, 'user');
        clearUnreadCount(ntw_room_key, channel_id, 1);
    } else {

    }
});

$('#chatReplyForm').on('submit', function (e) {
    e.preventDefault();

    let chatReplyForm = $(this);
    let message = chatReplyForm.find('input[name="chat_reply_input"]').val();
    let chat_reply_id = chatReplyForm.find('input[name="reply_comment_id"]').val();
    if (!chat_reply_id) {
        jq_confirm_alert('Warning', 'Invalid reply ID. Please try again.', 'orange', 'Ok');
    }

    if (chatWindow === 'channel') {
        let channel_id = $('#channel-chat').data('channel_id');
        sendFRMChat(message, my_pToken, chat_reply_id, ntw_room_key, channel_id, 'user');
        clearUnreadCount(ntw_room_key, channel_id, 0);
    } else {

    }
});

$('#createVideoForm11').validate({
    rules: {
        video_name: {
            required: true,
        },
        /*video_desc: {
                required: true,
                maxlength: 350
            },*/
        ext_link : {
            required: function () {
                return $("input[name=room-choice]:checked").val() == "1" ? true : false;
            }
        }
    },
    messages: {
        video_name: {
            required: "Video name is required"
        },
        /* video_desc: {
             required: "Video description is required"
         },*/
        ext_link : {
            required: "External room is required"
        }
    },
    submitHandler: function (form) {

        var room_choice = $("input[name=room-choice]:checked").val();
        var video_desc = $("#video_desc").val();
        var video_name = $("input[name=video_name]").val();

        var channel_of_type = $("input[name=channel_of_type]").val();


        let createVideoForm = $('#createVideoForm');

        let formData = new FormData(form);
        formData.append('taoh_action', 'taoh_create_video');
        formData.append('key', my_pToken);
        formData.append('ptoken', my_pToken);
        formData.append('room_id', ntw_room_key);


        let submit_btn = createVideoForm.find('button[type="submit"]');
        submit_btn.prop('disabled', true);

        let submit_btn_icon = submit_btn.find('i');
        submit_btn_icon.removeClass('fa-play-circle-o').addClass('fa-spinner fa-spin');
        // alert(room_choice)

        if(channel_of_type == 'channel'){
            let channel_id_a = $('#channel-chat').attr('data-channel_id');
            let channel_name = $('.cw_channel_title').text() || 'Channel';

            var track_data = {
                'action': 'create_video',
                'channel_id': channel_id_a,
                'channel_name': channel_name,
                'video_name': video_name,
                'video_link': '',
                'ptoken': my_pToken
            };
        } else {
            var track_data = {
                'action': 'create_video',
                'video_name': video_name,
                'video_link': '',
                'ptoken': my_pToken
            };
        }

        console.log("track_data", track_data);        

        if(room_choice == 1){
            var ext_link_org =  $("input[name=ext_link]").val();

            var ext_link = normalizeUrl(ext_link_org);

            track_data.video_link = ext_link;
            var videoChatLinkData = `
                                    <div class="ctext-wrap mb-0">
                                        <div class="">
                                            <!--<h6 class="mb-0 ctext-name fs-13 fw-500">${chatname}</h6>--->
                                            <p class="mb-0 ctext-content fs-12 fw-400">
                                                
                                                Join 
                                                <a href="${ext_link}" video_name="${video_name}" link="${ext_link}" channel_of_type="${channel_of_type}"
                                                target="_blank" class="d-inline-flex align-items-center join-v-link" style="gap: 4px;">
                                                ${video_name}
                                                </a>
                                                 - Video Room
                                            </p>

                                            <p class="mb-0 ctext-content fs-12 fw-400 text-black">${video_desc}</p>
                                            
                                        </div>
                                    </div>`;
            commentInput.val(videoChatLinkData);
            $('#chat-send-btn').trigger('click');
            //$('#createVideoForm').reset();
            taoh_track_activities(track_data);

            document.getElementById("createVideoForm").reset();
            submit_btn.prop('disabled', false);
            submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-play-circle-o');
            $('#v-channel-room').modal('hide');
            //alert(ext_link);
            window.open(ext_link);
        }
        else{
            $.ajax({
                url: createVideoForm.attr('action'),
                type: 'post',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                cache: false,
                success: function (response) {
                    console.log('-----response-------',response)
                    if(response.my_link){
                        //alert(response.my_link);
                        var link = response.my_link;
                        track_data.video_link = link;
                        taoh_track_activities(track_data);
                        var videoChatLinkData = `
                                                        <div class="ctext-wrap mb-0">
                                                            <div class="">
                                                                <!--<h6 class="mb-0 ctext-name fs-13 fw-500">${chatname}</h6>-->
                                                                <p class="mb-0 ctext-content fs-12 fw-400">
                                                
                                                                    Join 
                                                                    <a href="${link}" video_name="${video_name}" link="${link}" channel_of_type="${channel_of_type}"
                                                                    target="_blank" class="d-inline-flex align-items-center join-v-link" style="gap: 4px;">
                                                                    ${video_name}
                                                                    </a>
                                                                     - Video Room
                                                                </p>

                                                                <p class="mb-0 ctext-content fs-12 fw-400 text-black">${video_desc}</p>
                                                                
                                                            </div>
                                                        </div>`;
                        commentInput.val(videoChatLinkData);
                        $('#chat-send-btn').trigger('click');
                        document.getElementById("createVideoForm").reset();
                        submit_btn.prop('disabled', false);
                        submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-play-circle-o');
                        $('#v-channel-room').modal('hide');
                        window.open(link);//alert(link);
                    }
                    else{
                        // alert(2)
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error:', xhr.status);
                    submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-play-circle-o');
                    submit_btn.prop('disabled', false);
                }
            });
        }
    }
});

    $('#createVideoForm').validate({
        rules: {
            video_name: {
                required: true,
            },
            /*video_desc: {
                    required: true,
                    maxlength: 350
                },*/
            ext_link : {
                required: function () {
                    return $("input[name=room-choice]:checked").val() == "1" ? true : false;
                }
            }
        },
        messages: {
            video_name: {
                required: "Video name is required"
            },
            /* video_desc: {
                required: "Video description is required"
            },*/
            ext_link : {
                required: "External room is required"
            }
        },
        submitHandler: async function (form, event) {

            event.preventDefault();

            var room_choice = $("input[name=room-choice]:checked").val();
            var video_desc = $("#video_desc").val();
            var video_name = $("input[name=video_name]").val();
            var channel_of_type = $("input[name=channel_of_type]").val();
            let createVideoForm = $('#createVideoForm');

            let formData = new FormData(form);
            formData.append('taoh_action', 'taoh_create_video');
            formData.append('key', my_pToken);
            formData.append('ptoken', my_pToken);
            formData.append('room_id', ntw_room_key);

            let submit_btn = createVideoForm.find('button[type="submit"]');
            submit_btn.prop('disabled', true);

            let submit_btn_icon = submit_btn.find('i');
            submit_btn_icon.removeClass('fa-play-circle-o').addClass('fa-spinner fa-spin');
            // alert(room_choice)

            if(channel_of_type == 'channel'){
                let channel_id_a = $('#channel-chat').attr('data-channel_id');
                let channel_name = $('.cw_channel_title').text() || 'Channel';

                var track_data = {
                    'action': 'create_video',
                    'channel_id': channel_id_a,
                    'channel_name': channel_name,
                    'video_name': video_name,
                    'video_link': '',
                    'ptoken': my_pToken
                };
            } else {
                var track_data = {
                    'action': 'create_video',
                    'video_name': video_name,
                    'video_link': '',
                    'ptoken': my_pToken
                };
            }

            console.log("track_data", track_data);

            if(room_choice == 1) {
                var ext_link_org =  $("input[name=ext_link]").val();

                var ext_link = normalizeUrl(ext_link_org);

                track_data.video_link = ext_link;
                var videoChatLinkData = `<div class="ctext-wrap mb-0">
                        <div class="">
                            <p class="mb-0 ctext-content fs-12 fw-400">
                                Join
                                <a href="${ext_link}" video_name="${video_name}" link="${ext_link}" channel_of_type="${channel_of_type}"
                                target="_blank" class="d-inline-flex align-items-center join-v-link" style="gap: 4px;">
                                ${video_name}
                                </a>
                                - Video Room
                            </p>
                            <p class="mb-0 ctext-content fs-12 fw-400 text-black">${video_desc}</p>
                        </div>
                    </div>`;
                $('#chat_input').val(videoChatLinkData);
                $('#chat-send-btn').trigger('click');
                //$('#createVideoForm').reset();
                taoh_track_activities(track_data);

                document.getElementById("createVideoForm").reset();
                submit_btn.prop('disabled', false);
                submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-play-circle-o');
                $('#v-channel-room').modal('hide');
                window.open(ext_link);
            }
            else{

                var gmeet_data = await createGoogleMeet(video_name);
                var link = gmeet_data?.data?.meet_link || "";
                console.log("gmeet_data gmeet_data", link);                
                
                 if(!link) {
                    // Fallback to AJAX only if no GMeet link
                    try {
                        const response = await $.ajax({
                            url: createVideoForm.attr('action'),
                            type: 'post',
                            data: formData,
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                            cache: false,
                        });
                        if(response.my_link) {
                            link = response.my_link;
                            console.log("jitsi link:", link);
                        }
                    } catch (err) {
                        console.error("Error creating Jitsi link:", err);
                    }
                }

                if(link) {
                    track_data.video_link = link;
                    taoh_track_activities(track_data);
                    var videoChatLinkData = `<div class="ctext-wrap mb-0">
                            <div class="">
                                <p class="mb-0 ctext-content fs-12 fw-400">                        
                                    Join 
                                    <a href="${link}" video_name="${video_name}" link="${link}" channel_of_type="${channel_of_type}"
                                    target="_blank" class="d-inline-flex align-items-center join-v-link" style="gap: 4px;">
                                    ${video_name}
                                    </a>
                                    - Video Room
                                </p>
                                <p class="mb-0 ctext-content fs-12 fw-400 text-black">${video_desc}</p>                
                            </div>
                        </div>`;
                    $('#chat_input').val(videoChatLinkData);                            
                    $('#chat-send-btn').trigger('click');
                    document.getElementById("createVideoForm").reset();
                    submit_btn.prop('disabled', false);
                    submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-play-circle-o');
                    $('#v-channel-room').modal('hide');
                    window.open(link);
                } else {
                    alert("Error creating video link. Please try again.");
                    submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-play-circle-o');
                    submit_btn.prop('disabled', false);
                }
            }
        }
    });

    async function createGoogleMeet(summary) {
        
        let now = new Date();
        let oneHourLater = new Date(now.getTime() + 60 * 60 * 1000);
        let start_datetime = now.toISOString().split('.')[0];
        let end_datetime = oneHourLater.toISOString().split('.')[0];
        let timezone = "America/New_York";

        let data = {
            'taoh_action': 'taoh_create_google_meet_link',
            summary,
            start_datetime,
            end_datetime,
            timezone
        };
        const response = await new Promise((resolve, reject) => {
            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'post',
                data: data,
                dataType: 'json',
                success: function (res) {
                    console.log("taoh_create_google_meet_link", res);     
                    resolve(res);              
                },
                error: function (xhr, status, error) {
                    console.log('Error:', xhr.status);
                    resolve(status);
                }
            });
        });
        return response;
    }

$('#createChannelForm').validate({
    rules: {
        channelname: {
            required: true,
        },
        channeldescription: {
            required: true,
            maxlength: 350
        }

    },
    messages: {
        channelname: {
            required: "Channel name is required"
        },
        channeldescription: {
            required: "Channel description is required"
        }
    },
    submitHandler: function (form) {

        var is_video_there = $("input[name=channel_video]").val();

        let createChannelForm = $('#createChannelForm');

        let formData = new FormData(form);
        formData.append('taoh_action', 'taoh_create_channel');
        formData.append('key', my_pToken);
        formData.append('ptoken', my_pToken);
        formData.append('room_id', ntw_room_key);
        formData.append('channel_type', _taoh_channel_disscussion);

        let submit_btn = createChannelForm.find('button[type="submit"]');
        submit_btn.prop('disabled', true);

        let submit_btn_icon = submit_btn.find('i');
        submit_btn_icon.removeClass('fa-play-circle-o').addClass('fa-spinner fa-spin');

        $.ajax({
            url: createChannelForm.attr('action'),
            type: 'post',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            cache: false,
            success: function (response) {
                if(response.success){
                    loadChannelList();

                    clearModalForm($('#createChannelModal'));

                    submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-play-circle-o');
                    submit_btn.prop('disabled', false);

                    $('#createChannelModal').modal('hide');

                    jq_confirm_alert('Success', 'We are creating your channel. It will show up on the left bar soon!', 'green', 'Ok');
                } else {
                    if (response.error == 'channel_id_already_exist') {
                        jq_confirm_alert('Warning', 'Channel name already exist. Try something different.', 'orange', 'Ok');
                    } else {
                        taoh_set_error_message('Failed to create channel! Try Again', false);
                    }

                    submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-play-circle-o');
                    submit_btn.prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                console.log('Error:', xhr.status);
                submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-play-circle-o');
                submit_btn.prop('disabled', false);
            }
        });
    }
});


/*$("#query").on('keyup', function (event) {
    searchFilter();
    // event.preventDefault();
    // if ($('#search').length > 0) $("#search").trigger('click');
});*/

$("#search").on('click', function () {

 searchFilter();

});

$(".agree-btn").on('click', function () {
    const modalEl = document.getElementById('v-overlay');
    const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    modalInstance.hide();

    setTimeout(initNetworkingTour, 3000);
});

/* My Status */
$(document).on("click", "#my_status", function () {
    let emojisrc = $('#loadEmojiImg').attr('src');
    let my_status_text = $('#my_status').val();
    if (my_status_text) $('#current_status').val(my_status_text.trim());
    if (emojisrc) $('#selected-emoji img').attr('src', emojisrc);
    $('#status-modal').modal('show');
});

$(document).on("click", ".close-status-modal", function () {
    $('#status-modal').modal('hide');
});

$(document).on("click", ".texty_single_line_input", function () {
    $(this).addClass("focus");
});

$(document).on("click", ".status-save", function () {
    var customer_status = $('.ql-editor').text();
    $('#my_status').val(customer_status.trim());
    $('#my_status').attr('disabled', 'disabled');
    $('#network_status').hide();
});

$(document).on("click", function (a) {
    if (!$(a.target).closest('#emoji_section').length) {
        $("#emoji_section").hide();
    }
    if (a.target.className == 'emoji-place') {
        $("#emoji_section").show();
    }
});

function openStatusModal() {
    $('#status-modal').modal('show');
}

function copyToStatus(status_val) {
    $('#current_status').val(status_val.trim());
}

function saveStatus() {
    var customer_status = $('#current_status').val();
    var choosen_emoji = $('#choosen_emoji').val();
    var mood_status_message = customer_status + "###" + choosen_emoji;
    if (customer_status?.trim() === '' && choosen_emoji?.trim() === '') {
        mood_status_message = '';
    }
    $('#status-modal').modal('hide');
    taoh_update_mood_status(ntw_room_key, my_pToken, mood_status_message);
}

function update_mood_status(mood_status) {
    const default_emoji = 'default';
    let [statusText, emoji] = mood_status ? mood_status.split('###') : ['', default_emoji];
    if (!emoji) emoji = default_emoji;
    $('#loadEmojiImg').attr('src', _taoh_site_url_root + '/assets/images/emojis/' + emoji + '.svg');
    $('#my_status').val(statusText.trim());
}

function taoh_update_mood_status(room_hash, ptoken, mood_status = '') {
    var data = {
        'ops': 'moodstatus',
        'status': 'post',
        'code': _taoh_ops_code,
        'key': ptoken,
        'keyslug': room_hash,
        'mood_status': mood_status
    };

    $.ajax({
        url: _taoh_cache_chat_proc_url,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function (response, textStatus, jqXHR) {
            if (response.success && response.output['mood_status']) {
                update_mood_status(response.output['mood_status']);
            } else {
                update_mood_status('');
            }
        }
    });
}

function taoh_get_mood_status() {
    var data = {
        'ops': 'moodstatus',
        'status': 'get',
        'code': _taoh_ops_code,
        'key': my_pToken,
        'keyslug': ntw_room_key,
        'cfcc90': 1
    };

    $.ajax({
        url: _taoh_cache_chat_proc_url,
        type: 'GET',
        dataType: 'json',
        data: data,
        success: function (response, textStatus, jqXHR) {
            if (response.success && response.output['mood_status']) {
                update_mood_status(response.output['mood_status']);
            } else {
                update_mood_status('');
            }
        }
    });
}

function showEmoji() {
    $('#emoji_section').show();
}

function removeEmoji() {
    $('#selected-emoji').html(`<img class="emoji-place" src="${_taoh_site_url_root + '/assets/images/emojis/default.svg'}" alt="emoji">`);
    $('#choosen_emoji').val('');
}

function chooseEmoji(id) {
    $('#selected-emoji').html(`<img class="emoji-place" src="${_taoh_site_url_root + '/assets/images/emojis/' + id + '.svg'}" alt="emoji">`);
    $('#choosen_emoji').val(id);
}

/* /My Status */

function searchFilter() {
    ntwEntriesETag = null;
    opt_search = 1;
    let queryString = $("#query").val();
    if (queryString.trim() == '') {
        opt_search = 0;
    }
    networkArea.empty();
    loader(true, loaderArea, 75);
    taoh_load_network_entries();
    // }
}

function updateNTWUserEntriesInterval() {
    if (ntwUserEntriesIntervalId) clearInterval(ntwUserEntriesIntervalId);
    ntwUserEntriesIntervalId = setInterval(function () {
        if (!document.hidden) {
            //taoh_load_network_entries();
        }
    }, ntwUserEntriesInterval);
}



/* ------------------------------------- */
async function createOrganizerChannel(channelId=''){
    // alert(channelId)
    if (listChannelsArray.includes(channelId)) {
        $('#channel-' + channelId).click();
        return;
    }
    

    let data = {
        'taoh_action': 'taoh_create_channel_with_organizer',
        'key': my_pToken,
        'ptoken': my_pToken,
        'room_id': ntw_room_key,
        'channel_id': channelId,
        'chatwith': 'organizer',
    };

    $.ajax({
        url: _taoh_site_ajax_url,
        type: 'post',
        data: data,
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                loadChannelList(1, 0, channelId);
            }
        },
        error: function (xhr, status, error) {
            console.log('Error:', xhr.status);
        }
    });
    
}
async function createOnetoOneChannel(chatwith, channelId = '', openChatWindow = 1) {

    // alert(channelId)
    if (listChannelsArray.includes(channelId)) {       
        $('#dm-' + channelId).click();
        return;
    }


    const senderUserInfo = await getUserInfo(my_pToken);

    if(chatwith != 'organizer'){
        const targetUserInfo = await getUserInfo(chatwith, 'public');
        var targetChatName = targetUserInfo.chat_name;
    }
    else{
        var  targetChatName = 'Organizer';
    }
        
    //alert(targetChatName)
    /*if (targetUserInfo == null || targetUserInfo == undefined || targetUserInfo == '') {
        return;
    }*/

    if (senderUserInfo ) {
        const senderChatName = senderUserInfo.chat_name;
        

        let data = {
            'taoh_action': 'taoh_create_channel_for_1_1',
            'key': my_pToken,
            'ptoken': my_pToken,
            'room_id': ntw_room_key,
            'chatwith': chatwith,
            'chatwith_chatname': targetChatName,
            'loggedin_chatname': senderChatName
        };

        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'post',
            data: data,
            dataType: 'json',
            success: function (response) {
                if (response.success) {                   

                    if(openChatWindow == 1) {
                        loadChannelList(1, 0, channelId);
                        console.log("dm opening");
                    } else {
                        console.log("dm not opening");
                    }

                }
            },
            error: function (xhr, status, error) {
                console.log('Error:', xhr.status);
            }
        });
    }
}

function createChannelFromTicketType() {
    let ntw_channels_key = 'ntw_channels_' + ntw_room_key;

    IntaoDB.getItem(objStores.ntw_store.name, ntw_channels_key).then((intao_data) => {

        // Check if data is expired (expires after 10sec (10 * 1000))
        if (intao_data) {
            // no need to call the create tickettype channel call
            loadChannelList();
        } else {
            createTicketChannel();
        }
    });
}

async function showMembersList(channelId, channelName) {

    if(selectedChat == 'organizer')
        return;
    var chndata = '';
    var channelInfo = '';

    chndata = `
                    <div class="p-3 border-bottom">
                        <div class="pb-4 border-bottom border-bottom-dashed mb-4">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h5 class="fs-16 text-muted text-uppercase">Members</h5>
                                </div>
                            </div>

                            <ul id="members_list" class="list-unstyled chat-list mx-n4">
                            </ul>
                        </div>
                    </div>`;

    $("#members_block").html(chndata);

    $('.chat-like-sidebar').hide();

    loadRightSidebar('members');

    channelInfo = channelInfoData[channelId];

    if (channelInfo.members != undefined && channelInfo.members.length > 0) {      
        $.each(channelInfo.members, async function (mkey, memtoken) {            
            var d_data = await getUserInfo(memtoken, 'public');
            if (d_data.avatar_image != '' && d_data.avatar_image != undefined) {
                var avatar_image = d_data.avatar_image;
            } else if (d_data.avatar != undefined && d_data.avatar != 'default') {
                var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/' + d_data.avatar + '.png';
            } else {
                var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/avatar_def.png';
            }
            if ($('#members_list').find(`#member_${memtoken}`).length === 0) {
                var members = `  <li id="member_${memtoken}">
                                    <a href="javascript: void(0);">
                                        <div class="d-flex align-items-center">
                                            <img src="${avatar_image}" alt="" class="avatar-sm rounded-circle me-3">
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h6 class="text-truncate mb-0">${d_data.chat_name}</h6>
                                            </div>
                                            ${memtoken != my_pToken ?
                                        `<div>
                                            <div  class="${memtoken}_loader taoh-loader taoh-spinner" id="pc_loader"
                                            style="width:20px;height:20px;display:none;"
                                            ></div>
                                            <button type="button" id="${memtoken}" class="btn btn-sm openchatacc mr-2" data-chatwith="${memtoken}" data-chatname="${d_data.chat_name}" data-live="" style="white-space: nowrap;font-size: small;">
                                                    Chat <i class="la la-angle-double-right"></i></button></div>` : ''}
                                        </div>
                                    </a>
                                </li>`;
                
                $('#members_list').append(members);
            }

        });
        $('#members_block').show();
    } else {
        $('#members_list').append(`<li><a href="javascript: void(0);">
                    <div class="d-flex align-items-center"><div class="flex-grow-1 overflow-hidden">
                    <h6 class="text-truncate mb-0" style="text-aign:center">No Members</h6></div></div></a></li>`);

        $('#members_block').hide();
    }
}

async function showLikeList(frmMessageId) {
    var chndata = '';
    var channelInfo = '';

    chndata = `<div class="row"><div class="col-12"><div class="user-chat-nav p-2 d-xl-none">
        <div class="d-flex w-100">
            <div class="flex-grow-1">
                <button type="button" class="btn nav-btn channel-sidebar-show bg-white rounded-circle" style="border: 1px solid #d3d3d3;">
                    <i class="bx bx-x"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="p-3 border-bottom">
        <div class="pb-4 border-bottom border-bottom-dashed mb-4">
            <div class="d-flex">
                <div class="flex-grow-1">
                    <h5 class="fs-16 text-muted text-uppercase">Likes</h5>
                </div>
            </div>

            <ul id="like_list" class="list-unstyled chat-list mx-n4">
            </ul>
        </div>
    </div></div></div>`;

    $("#channel-like-message-block").html(chndata);

    let data = {
        ops: 'channel_message',
        action: 'get_like',
        code: _taoh_ops_code,
        key: my_pToken,
        keyslug: ntw_room_key,
        message_id: frmMessageId,
        cfcc60: 1
    };

    const options = {
        day: '2-digit',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
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
                $.each(response.likelist, async function (mkey, memtoken) {

                    let milliTimestamp = Math.floor(memtoken.created_date / 1000);
                    let created_date = new Date(milliTimestamp);
                    created_formatted_date = created_date.toLocaleString('en-US', options).replace(',', '');

                    var d_data = await getUserInfo(memtoken.ptoken, 'public');
                    if (d_data.avatar_image != '' && d_data.avatar_image != undefined) {
                        var avatar_image = d_data.avatar_image;
                    } else if (d_data.avatar != undefined && d_data.avatar != 'default') {
                        var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/' + d_data.avatar + '.png';
                    } else {
                        var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/avatar_def.png';
                    }
                    var members = `  <li>
                        <a href="javascript: void(0);">
                            <div class="d-flex align-items-center">
                                <img src="${avatar_image}" alt="" class="avatar-sm rounded-circle me-3">
                                <div class="flex-grow-1 overflow-hidden">
                                    <h6 class="text-truncate mb-0">${d_data.chat_name}</h6>
                                    <span class="text-truncate mb-0">${created_formatted_date}</span>
                                </div>
                                ${memtoken.ptoken != my_pToken ?
                                `<div>
                                <div  class="${memtoken.ptoken}_loader taoh-loader taoh-spinner" id="pc_loader"
                                style="width:20px;height:20px;display:none;"
                                ></div>
                                <button type="button" id="${memtoken.ptoken}" class="btn btn-sm openchatacc mr-2" data-chatwith="${memtoken.ptoken}" data-chatname="${d_data.chat_name}" data-live="" style="white-space: nowrap;font-size: small;">
                                        Chat <i class="la la-angle-double-right"></i></button></div>` : ''}
                            </div>
                        </a>
                    </li>`;
                    $('#like_list').append(members);
                });
                $('#chat-like-conversation-list').removeClass('aw-logo');
            } else {

            }
        },
        error: function (xhr, status, err) {
            console.error('Error Fetching activity list : ' + err);
        }
    });    
}

$('#toggleMore').click(function () {
      $('#extraFields').slideToggle();
      $('#extraFields').toggleClass('visible');
     // const isVisible = $('#extraFields').is(':visible');
     // $(this).text(isVisible ? 'Show Less' : 'Show More');

      if($('#extraFields').hasClass('visible')) {
          $(this).html('<i class="bx bx-minus"></i> Show Less');
      }
      else{
            $(this).html('<i class="bx bx-plus"></i> Show More Options');
      }
});

$('#toggleMoreOption').click(function () {
      $('#extraRoomFields').slideToggle();
      $('#extraRoomFields').toggleClass('visible');
     // const isVisible = $('#extraFields').is(':visible');
     // $(this).text(isVisible ? 'Show Less' : 'Show More');

      if($('#extraRoomFields').hasClass('visible')) {
          $(this).html('<i class="bx bx-minus"></i> Show Less');
      }
      else{
            $(this).html('<i class="bx bx-plus"></i> Show More Options');
      }
});

function readmore(i) {
    var dots = document.getElementById("dots_" + i);
    var moreText = document.getElementById("more_" + i);
    var btnText = document.getElementById("morebtn_" + i);

    if (dots.style.display === "none") {
        dots.style.display = "inline";
        btnText.innerHTML = "Read more";
        moreText.style.display = "none";
    } else {
        dots.style.display = "none";
        btnText.innerHTML = "Read less";
        moreText.style.display = "inline";
    }
}

/* async function loadMembersList(channelName){
    let ntw_channels_key = 'ntw_channels_' + ntw_room_key;
    IntaoDB.getItem(objStores.ntw_store.name, ntw_channels_key).then((intao_data) => {
        // Check if data is expired (expires after 10sec (10 * 1000))
        if (intao_data ) {
            if(intao_data.values.name == channelName){
                channelInfo = intao_data.value;
            }
        }
    });
} */

function get_today_date() {
    var currentDate = new Date()
    var day = currentDate.getDate();
    var month = currentDate.getMonth() + 1;
    var year = currentDate.getFullYear();
    var my_date = month + "-" + day + "-" + year;
    return my_date;
    //localStorage.setItem('date_last_agree', my_date);
}

function normalizeUrl(url) {
    // Remove leading/trailing spaces
    url = $.trim(url);

    // Check if it already has http or https
    if (!/^https?:\/\//i.test(url)) {
        // If it starts with "www.", prepend "http://"
        if (/^www\./i.test(url)) {
            url = "http://" + url;
        } else {
            // Otherwise, add "http://www." by default
            url = "http://www." + url;
        }
    }

    return url;
}

$(document).on('click', '.join-v-link', async function (e) {
    e.preventDefault();
    var video_name = $(this).attr('video_name');
    var video_link = $(this).attr('link');
    var channel_of_type = $(this).attr('channel_of_type');

    await trackOnVideoLink(video_name, video_link, channel_of_type);
});

async function trackOnVideoLink(video_name, video_link, channel_of_type) {


    if (channel_of_type == 'channel') {
        let channel_id_a = $('#channel-chat').attr('data-channel_id');

        let channel_name = $('.cw_channel_title').text() || 'Channel';

        var track_data = {
            'action': 'joined_video',
            'channel_id': channel_id_a,
            'channel_name': channel_name,
            'video_name': video_name,
            'video_link': video_link,
            'ptoken': my_pToken
        };

    } else {
        let channel_id_a = $('#users-chat').attr('data-channel_id');
        let channel_name = $('.cw_direct_message_title').text() || 'User';

        var track_data = {
            'action': 'joined_video',
            'channel_id': channel_id_a,
            'channel_name': channel_name,
            'video_name': video_name,
            'video_link': video_link,
            'ptoken': my_pToken
        };
    }


    window.open(video_link, '_blank');
    taoh_track_activities(track_data);
}

function taoh_get_activities() {
    let data = {
        ops: 'activity',
        action: 'getActivity',
        code: _taoh_ops_code,
        key: my_pToken,
        room_id: ntw_room_key,
        cfcc60: 1
    };
    $.ajax({
        url: _taoh_cache_chat_url,
        type: 'GET',
        dataType: 'json',
        //headers: {'If-None-Match': ntwChannelListETag},
        data: data,
        success: function (response, textStatus, jqXHR) {
            loader(false, loaderArea);
            if (jqXHR.status === 304) return;
            if (response.success) {
                renderActivities(response.activities);
                renderVideoActivities(response.videoactivities);
            } else {

            }
        },
        error: function (xhr, status, err) {
            console.error('Error Fetching activity list : ' + err);
        }
    });
}

async function renderVideoActivities(videoactivities) {
    var activities_video_html = '';

    activities_video_html += `<div>
                                <div class="d-flex align-items-center" style="gap: 12px;">
                                    <div class="count-container">
                                        <svg width="54" height="54" viewBox="0 0 54 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="27" cy="27" r="27" fill="black"/>
                                            <path d="M14.8 13C12.6489 13 10.9 14.7297 10.9 16.8571V32.2857H14.8V16.8571H38.2V32.2857H42.1V16.8571C42.1 14.7297 40.3511 13 38.2 13H14.8ZM8.17 34.2143C7.52406 34.2143 7 34.7326 7 35.3714C7 37.9268 9.09625 40 11.68 40H41.32C43.9038 40 46 37.9268 46 35.3714C46 34.7326 45.4759 34.2143 44.83 34.2143H8.17Z" fill="white"/>
                                            <rect x="15" y="17" width="23" height="15" fill="white"/>
                                            <path d="M23.15 20C23.6141 20 24.0592 20.1975 24.3874 20.5492C24.7156 20.9008 24.9 21.3777 24.9 21.875C24.9 22.3723 24.7156 22.8492 24.3874 23.2008C24.0592 23.5525 23.6141 23.75 23.15 23.75C22.6859 23.75 22.2408 23.5525 21.9126 23.2008C21.5844 22.8492 21.4 22.3723 21.4 21.875C21.4 21.3777 21.5844 20.9008 21.9126 20.5492C22.2408 20.1975 22.6859 20 23.15 20ZM31.2 20C31.6641 20 32.1092 20.1975 32.4374 20.5492C32.7656 20.9008 32.95 21.3777 32.95 21.875C32.95 22.3723 32.7656 22.8492 32.4374 23.2008C32.1092 23.5525 31.6641 23.75 31.2 23.75C30.7359 23.75 30.2908 23.5525 29.9626 23.2008C29.6344 22.8492 29.45 22.3723 29.45 21.875C29.45 21.3777 29.6344 20.9008 29.9626 20.5492C30.2908 20.1975 30.7359 20 31.2 20ZM20 27.0008C20 25.6203 21.0456 24.5 22.3341 24.5H23.2681C23.6159 24.5 23.9462 24.582 24.2438 24.7273C24.2153 24.8961 24.2022 25.0719 24.2022 25.25C24.2022 26.1453 24.5697 26.9492 25.1494 27.5C25.145 27.5 25.1406 27.5 25.1341 27.5H20.4659C20.21 27.5 20 27.275 20 27.0008ZM28.8659 27.5C28.8616 27.5 28.8572 27.5 28.8506 27.5C29.4325 26.9492 29.7978 26.1453 29.7978 25.25C29.7978 25.0719 29.7825 24.8984 29.7562 24.7273C30.0538 24.5797 30.3841 24.5 30.7319 24.5H31.6659C32.9544 24.5 34 25.6203 34 27.0008C34 27.2773 33.79 27.5 33.5341 27.5H28.8659ZM24.9 25.25C24.9 24.6533 25.1212 24.081 25.5151 23.659C25.9089 23.2371 26.443 23 27 23C27.557 23 28.0911 23.2371 28.4849 23.659C28.8788 24.081 29.1 24.6533 29.1 25.25C29.1 25.8467 28.8788 26.419 28.4849 26.841C28.0911 27.2629 27.557 27.5 27 27.5C26.443 27.5 25.9089 27.2629 25.5151 26.841C25.1212 26.419 24.9 25.8467 24.9 25.25ZM22.8 31.3742C22.8 29.6492 24.1059 28.25 25.7159 28.25H28.2841C29.8941 28.25 31.2 29.6492 31.2 31.3742C31.2 31.7188 30.9397 32 30.6159 32H23.3841C23.0625 32 22.8 31.7211 22.8 31.3742Z" fill="black"/>
                                        </svg>

                                        <div class="vdo-act-count"></div>
                                    </div>`;
    activities_video_html += `

                                            <div class="top_portion"></div>
                                            </div>

                                            <ul class="mt-2 vdo-room-lists collapsible" id="">`;
    var usernameArray = [];
    if (videoactivities.length > 0) {
        var video_count = 0;
        for (let activity of videoactivities) {
            const userInfo = await getUserInfo(activity.ptoken, 'public');
            const fallbackSrc = `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;
            const userAvatarSrc = await buildAvatarImage(userInfo.avatar_image, fallbackSrc);
            const userChatName = userInfo.chat_name;

            if (activity.action == 'create_video') {
                video_count++;
                if (video_count < 3) {
                    usernameArray.push(userChatName);
                }
                activities_video_html += `

                                                                                
                                                                <li class="d-flex align-items-center" style="gap: 6px;">
                                                                <img class="round-profile-24" src="${userAvatarSrc}" alt="">
                                                                <div>
                                                                    <span class="mr-1">
                                                                    <span class="fw-500 text-capitalize">${userChatName} </span>
                                                                    created a video room  <b>${activity.video_name}</b> on 
                                                                    <a href="javascript:void(0);"
                                                                    click_channel_id="${activity.channel_id}"
                                                                    class="click_channel a-link">
                                                                    ${activity.channel_name}</a>
                                                                </span> 
                                                                    <a href="${activity.video_link}" target="_blank" class="text-underline">Check Video Room</a>
                                                                    </div>
                                                                </li>
                                                                
                                                            `;
            }

        }
        activities_video_html += `</ul>
                                            
                                            </div>`;


        var top_data = '';

        top_data += `
                                                    <p class="fw-500 text-capitalize text-black lh-16 mb-2 sentence" >
                                                    </p> 
                                                    <button 
                                                    type="button" 
                                                    class="toggle-btn btn bor-btn toggle-vdo-lists">
                                                        <span class="toggleText">More Details</span>
                                                        <svg class="drp-dwn-svg" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0ZM2.63672 4.70703C2.45312 4.52344 2.45312 4.22656 2.63672 4.04492C2.82031 3.86328 3.11719 3.86133 3.29883 4.04492L4.99805 5.74414L6.69727 4.04492C6.88086 3.86133 7.17773 3.86133 7.35938 4.04492C7.54102 4.22852 7.54297 4.52539 7.35938 4.70703L5.33203 6.73828C5.14844 6.92188 4.85156 6.92188 4.66992 6.73828L2.63672 4.70703Z" fill="black"/>
                                                        </svg>
                                                    </button>
                                                `;


        $('.video-room-activity').html(activities_video_html);
        $('.video-room-activity').removeClass('d-none');
        $('.top_portion').html(top_data);
        $('.vdo-act-count').html(video_count);
        const uniqueMemmbers = [...new Set(usernameArray)];
        const usernameData = uniqueMemmbers.join(", ");
        const totalMembers = uniqueMemmbers.length;
        console.log(usernameData)

        var sent = `${usernameData} `;
        if (totalMembers > 2) {
            var remaining = totalMembers - 2;
            sent += `and ${remaining} Others `;

        }
        sent += `Created Video Room(s) !`;

        $('.sentence').html(sent);
    } else {
        //$('.video-room-activity').hide();
        $('.video-room-activity').addClass('d-none');
    }


}

$(document).on('click', '.toggle-btn', function () {
    //$('.vdo-room-lists').slideToggle(300);
    const toggleText = $(this).find('.toggleText');
    if (toggleText.text() === 'More Details') {
        toggleText.text('Less Details');
        $('.collapsible').addClass('open');
        $(this).find('.drp-dwn-svg').css('transform', 'rotate(180deg)');
    } else {
        toggleText.text('More Details');
        $('.collapsible').removeClass('open');
        $(this).find('.drp-dwn-svg').css('transform', 'rotate(0deg)');
    }
});

$(document).on('click', '.channel_toggle', function () {
    //$('.vdo-room-lists').slideToggle(300);
    const toggleText = $(this).attr('toggle_text');
    if (toggleText == 'open') {
        $(this).attr('toggle_text','close')
        $('.channnel_collapsible').addClass('open');
        $(this).find('.channel-drp-dwn-svg').css('transform', 'rotate(180deg)');
    } else {
        $(this).attr('toggle_text','open')
        $('.channnel_collapsible').removeClass('open');
        $(this).find('.channel-drp-dwn-svg').css('transform', 'rotate(0deg)');
    }
});



/*$('.toggle-btn').each(function(index) {
    alert();
  $(this).on('click', function() {
    $('.collapsible').eq(index).slideToggle(300);
  });
});*/

/*document.querySelectorAll('.toggle-btn').forEach((btn, index) => {
    btn.addEventListener('click', () => {
        alert();
    const collapsible = document.querySelectorAll('.collapsible')[index];
    collapsible.classList.toggle('open');
    });
});*/

async function renderActivities(activities) {
    var activities_html = '';
    if (activities.length > 0) {
        for (let activity of activities) {
            const userInfo = await getUserInfo(activity.ptoken, 'public');
            const fallbackSrc = `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;
            const userAvatarSrc = await buildAvatarImage(userInfo.avatar_image, fallbackSrc);
            const userChatName = userInfo.chat_name;

            if (activity.action == 'click_1_1') {
                activities_html += `<li class="d-flex align-items-center activity-list" style="gap: 12px;">
                                            <img class="round-profile-24" src="${userAvatarSrc}" alt="">
                                            <div>
                                                <p class="fs-12 fw-400 mb-0" style="line-height: 1.149;">
                                                <span class="fw-500 text-capitalize">
                                                ${userChatName}
                                                </span> Started a One on One Chat !</p>
                                                <!--<div style="line-height: 1;">
                                                    <a href="javascript:void(0);" class="participants_refresh a-link">Check Participants !</a>
                                                </div>-->
                                            </div>
                                        </li>`;
            } else if (activity.action == 'joined_video') {
                activities_html += `<li class="d-flex align-items-center activity-list" style="gap: 12px;">
                                            <img class="round-profile-24" src="${userAvatarSrc}" alt="">
                                            <div>
                                                <p class="fs-12 fw-400 mb-0" style="line-height: 1.149;">
                                                <span class="fw-500 text-capitalize">
                                                ${userChatName}</span> Joined <span class="fw-500 text-underline">${activity.video_name}</span></p>
                                                <div style="line-height: 1;">
                                                    <a target="_blank" href="${activity.video_link}"
                                                    class="load_video a-link">Check Video Room</a>
                                                </div>
                                            </div>
                                        </li>`;
            } else if (activity.action == 'joined_from') {
                activities_html += `<li class="d-flex align-items-center activity-list" style="gap: 12px;">
                                            <img class="round-profile-24" src="${userAvatarSrc}" alt="">
                                            <div>
                                                <p class="fs-12 fw-400 mb-0" style="line-height: 1.149;">
                                                <span class="fw-500 text-capitalize">${userChatName}</span>
                                                 Joined from <span class="fw-500">${activity.location}</span></p>
                                                <div class="d-flex align-items-center" style="gap: 6px;">
                                                    <span data-profile_token="${activity.ptoken}" class="a-link openProfileModal">View Profile</span>
                                                    <a href="#" class="openchatacc a-link  capitalize-first"
                                                    data-chatwith="${activity.ptoken}"
                                                    data-chatname="${userChatName}"
                                                    >Chat</a>

                                                </div>
                                            </div>
                                        </li>`;
            } else if (activity.action == 'click_channel') {
                activities_html += `

                                        <li class="d-flex align-items-center activity-list" style="gap: 12px;">
                                            <img class="round-profile-24" src="${userAvatarSrc}" alt="">
                                            <div>
                                                <p class="fs-12 fw-400 mb-0" style="line-height: 1.149;">
                                                <span class="fw-500 text-capitalize">${userChatName}</span> joined <span class="fw-500 text-underline">
                    ${activity.channel_name} Channel !</span></p>
                                                <div style="line-height: 1;">
                                                    <a href="javascript:void(0);"
                                                    click_channel_id="${activity.channel_id}"
                                                    class="click_channel a-link">Check What's Happening !</a>

                                                </div>
                                            </div>
                                        </li>
                                    `;
            } else if (activity.action == 'mention') {
                activities_html += `

                                        <li class="d-flex align-items-center activity-list" style="gap: 12px;">
                                            <img class="round-profile-24" src="${userAvatarSrc}" alt="">
                                            <div>
                                                <p class="fs-12 fw-400 mb-0" style="line-height: 1.149;">
                                                <span class="fw-500 text-capitalize">${userChatName}</span> mention 
                                                <span class="fw-500 text-underline">                    
                                                      @${activity.mention_name}</a>
                                                </span></p>
                                                <div class="d-flex align-items-center" style="gap: 6px;">
                                                    <span data-profile_token="${activity.ptoken}" class="a-link openProfileModal">View Profile</span>
                                                    <a href="#" class="openchatacc a-link  capitalize-first"
                                                    data-chatwith="${activity.ptoken}"
                                                    data-chatname="${userChatName}">Chat</a>
                                                </div>
                                               
                                            </div>
                                        </li>
                                    `;
            }
        }
        $('#activities-list').html(activities_html);
        $('#activities-list1').html(activities_html);

    } else {
        activities_html = '';
        $("#activities_block").hide();
        $("#activities_block1").hide();
    }

}

$(document).on('click', '.click_channel', function () {
    // alert();
    var channelId = $(this).attr('click_channel_id');
    $('#channel-' + channelId).click();
    return;

});

$(document).on('click', '.chat-like-show', function () {
    $('#chat-like-sidebar').hide();
});

