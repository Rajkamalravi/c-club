    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param * 1000000)
    }, 'File size must be less than {0} MB');

    var eventtoken = window._elh_cfg.eventtoken;
    /* var colorArray = ['#BA3B3B','#A1EAA3','#067CFF','#FDCC6E','#877CFF','#FEBC8F', '#FF6600','#BBBBFF','#F38400','#F6B1B1','#33CD45','#A7D8DE'];  */
    var colorArray = ['#708090', '#A7C7E7', '#5F9EA0', '#B3A398', '#A8BBA2', '#C3D6B8', '#EED9C4', '#D1C2E0', '#F5F5F5', '#748CAB', '#D6D1CD', '#E4C9AF', '#B8E0D2', '#E6C0C0', '#C8A2C8'];
    var shuffledColorArray = shuffleArray(colorArray);
    var hall_color_array = [];
    var u = 0;
    var opt = window._elh_cfg.opt;

    loader(true, $("#agenda_loaderArea"), 30);
    loader(true, $("#speaker_loaderArea"), 30);
    loader(true, $("#exhibitors_loaderArea"), 30);
    loader(true, $("#rsvpdir_loaderArea"), 30);

    setTimeout(() => {
        var event_status = $('#event_status_hidden').val();
        if(event_status == 3){
            $('#comments_top').hide();
            $('#rsvp-tab').hide();
            $('#tables_top').hide();
        }

    }, 10000);


    $(document).ready(function () {
        $('#rsvp_search').on('keydown', function (event) {
            if (event.key === 'Enter' || event.which === 13) {
                event.preventDefault(); // Prevent form submission
                performRSVPSearch();
            }
        });
        $('#speaker_search').on('keydown', function (event) {
            if (event.key === 'Enter' || event.which === 13) {
                event.preventDefault(); // Prevent form submission
                performSpeakerSearch();
            }
        });
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (event) {
            $('#dropdownMenuLink_agenda').text('Select Hall');
            $('#dropdownMenuLink_speaker').text('Select Speaker Hall');
            $('#dropdownMenuLink_exh').text('Select Exhibitor Hall');
            $("#speaker_search").val("");
            if (event.currentTarget.id == 'rsvp-tab') {
                eventCheckinList(eventtoken);
            } else if (event.currentTarget.id == 'dashboard-tab') {
                console.log('update dashboard info');
                updateDashboardInfo(eventtoken);
            } else if (event.currentTarget.id == 'conversation-tab') {
                console.log('conversation-tab clicked');
                if (typeof window.conversationsTab !== 'undefined' && typeof window.conversationsTab.initRoom === 'function') {
                    window.conversationsTab.initRoom();
                }
            } else {
                updateEventMetaInfo(eventtoken, false);
            }

        });

        $(document).on("change", "#search_halls", function () {
            const selectedUrl = $(this).val();
            if (selectedUrl) {
                window.open(selectedUrl, '_blank'); // Open in new tab
            }
        });

        const eventOrganizerBannerInput = document.getElementById('event_organizer_banner');
        if (eventOrganizerBannerInput) {
            eventOrganizerBannerInput.addEventListener('change', function(event) {
                const fileInput = event.target;
                const file = fileInput.files[0];
                const previewContainer = document.querySelector('.image-preview-container');
                const previewImage = document.getElementById('imagePreview');
                const customLabel = document.querySelector('.custom-file-label');

                // Update the custom file label with the file name
                if (file) {
                    customLabel.textContent = file.name;
                } else {
                    customLabel.textContent = 'Choose file';
                }

                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewContainer.style.display = 'block';
                    };

                    reader.readAsDataURL(file);
                } else {
                    previewImage.src = '';
                    previewContainer.style.display = 'none';
                }
            });
        }


        $('#event_organizer_banner_form').validate({
            rules: {
                event_organizer_banner: {
                    required: true,
                    extension: "jpg,jpeg,png",
                    filesize: 1,
                }
            },
            messages: {
                event_organizer_banner: {
                    required: "Organizer banner is required",
                },
            },
            errorPlacement: function (error, element) {
                if (element.closest('.input-group').length) {
                    error.insertAfter(element.closest('.input-group'));
                } else {
                    element.after(error);
                }
            },
            submitHandler: function (form, e) {
                e.preventDefault();

                let event_organizer_banner_form = $('#event_organizer_banner_form');
                let formData = new FormData(form);

                let eventtoken = event_organizer_banner_form.find('input[name="eventtoken"]').val();

                let submit_btn = $('#event_organizer_banner_upload_btn');
                let submit_btn_icon = submit_btn.find('i');
                submit_btn_icon.removeClass('fa-save').addClass('fa-spinner fa-spin');

                const previewContainer = document.querySelector('.image-preview-container');
                const previewImage = document.getElementById('imagePreview');

                $.ajax({
                    url: event_organizer_banner_form.attr('action'),
                    type: 'post',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function (response) {
                        if (response.status) {
                            $('#event_organizer_banner').val('');
                            $('#event_organizer_banner')
                                .next('.custom-file-label')
                                .text('Choose file');
                            if (previewImage && previewContainer) {
                                previewImage.src = '';
                                previewContainer.style.display = 'none';
                            }

                            submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                            submit_btn.prop('disabled', false);

                            taoh_set_success_message(response.message, false, 'toast-middle', [
                                {
                                    text: 'OK',
                                    action: () => {
                                        window.location.reload();
                                    },
                                    class: 'dojo-v1-btn float-right mt-3 mb-3'
                                }
                            ]);

                            setTimeout(() => {
                                window.location.reload(); // If another dojo msg comes up it won't reload so we force it after 3 seconds
                            }, 3000);

                            // delete_events_meta_into(eventtoken);
                            // delete_events_into('event_details_sponsor_' + eventtoken);
                            delete_events_into('event_MetaInfo_' + eventtoken);
                        } else {
                            submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                            submit_btn.prop('disabled', false);

                            taoh_set_error_message(response.message, false, 'toast-middle', [
                                {
                                    text: 'OK',
                                    action: () => {},
                                    class: 'dojo-v1-btn float-right mt-3 mb-3'
                                }
                            ]);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', xhr.status);
                        submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                        submit_btn.prop('disabled', false);

                        taoh_set_error_message('Failed to upload banner! Try Again', false, 'toast-middle', [
                            {
                                text: 'OK',
                                action: () => {},
                                class: 'dojo-v1-btn float-right mt-3 mb-3'
                            }
                        ]);
                    }
                });

                return false; // Prevent default form submission
            }
        });


        $('#remove_event_organizer_banner_btn').on('click', function () {
            let metaid = $(this).data('metaid');
            if(metaid) {
                let formData = new FormData();
                formData.append('taoh_action', 'remove_event_organizer_banner');
                formData.append('eventtoken', eventtoken);
                formData.append('metaid', metaid);

                let submit_btn = $('#remove_event_organizer_banner_btn');
                let submit_btn_icon = submit_btn.find('i');
                submit_btn_icon.removeClass('fa-trash').addClass('fa-spinner fa-spin');

                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'post',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function (response) {
                        if (response.status) {
                            submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-trash');
                            submit_btn.prop('disabled', false);

                            taoh_set_success_message(response.message, false, 'toast-middle', [
                                {
                                    text: 'OK',
                                    action: () => {
                                        window.location.reload();
                                    },
                                    class: 'dojo-v1-btn float-right mt-3 mb-3'
                                }
                            ]);

                            setTimeout(() => {
                                window.location.reload(); // If another dojo msg comes up it won't reload so we force it after 3 seconds
                            }, 3000);

                            // delete_events_meta_into(eventtoken);
                            // delete_events_into('event_details_sponsor_' + eventtoken);
                            delete_events_into('event_MetaInfo_' + eventtoken);
                        } else {
                            submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-trash');
                            submit_btn.prop('disabled', false);

                            taoh_set_error_message(response.message, false, 'toast-middle', [
                                {
                                    text: 'OK',
                                    action: () => {},
                                    class: 'dojo-v1-btn float-right mt-3 mb-3'
                                }
                            ]);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', xhr.status);
                        submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-trash');
                        submit_btn.prop('disabled', false);

                        taoh_set_error_message('Failed to remove banner! Try Again', false, 'toast-middle', [
                            {
                                text: 'OK',
                                action: () => {},
                                class: 'dojo-v1-btn float-right mt-3 mb-3'
                            }
                        ]);
                    }
                });
            }

        });

    });

    function shuffleArray(array) {
        // Create a new array with the length of the given array in the parameters
        const newArray = array.map(() => null);

        // Create a new array where each index contain the index value
        const arrayReference = array.map((item, index) => index);

        // Iterate on the array given in the parameters
        array.forEach(randomize);

        return newArray;

        function randomize(item) {
            const randomIndex = getRandomIndex();

            // Replace the value in the new array
            newArray[arrayReference[randomIndex]] = item;

            // Remove in the array reference the index used
            arrayReference.splice(randomIndex, 1);
        }

        // Return a number between 0 and current array reference length
        function getRandomIndex() {
            const min = 0;
            const max = arrayReference.length;
            return Math.floor(Math.random() * (max - min)) + min;
        }
    }

    function getEventsHall(eventtoken) {
        const eventdetailBaseInfoKey = 'event_detail_' + window._elh_cfg.eventtoken;
        const eve_tok = window._elh_cfg.eventtoken;

        IntaoDB.getItem(objStores.event_store.name, eventdetailBaseInfoKey).then((data) => {
            if (data?.values) {
                getEventsHallList(data.values);
            } else {
                getEventBaseInfo({eventtoken: eve_tok}, true);
            }
        });
    }

    async function getEventsHallList(response) {
        const event_output   = response.output || {};
        const conttoken_data = event_output.conttoken || {};

        const event_form_version = conttoken_data.event_form_version;
        const event_halls        = conttoken_data.event_halls;
        const exhibitor_halls    = conttoken_data.exhibitor_halls;
        const speaker_halls      = conttoken_data.speaker_halls;

        const hasEventHalls = Array.isArray(event_halls);

        let allowed_exhibitor_halls = [];
        let allowed_speaker_halls   = [];

        if (hasEventHalls) {
            // Exhibitor halls: accesslevel 2 or 3 (only if enabled)
            // if (conttoken_data.enable_exhibitor_hall == 1) {
                allowed_exhibitor_halls = event_halls.filter(
                    hall => hall.accesslevel === "2" || hall.accesslevel === "3"
                );
            // }
            // Speaker halls: accesslevel 1 or 3 (only if enabled)
            // if (conttoken_data.enable_speaker_hall == 1) {
                allowed_speaker_halls = event_halls.filter(
                    hall => hall.accesslevel === "1" || hall.accesslevel === "3"
                );
            // }
        } else {
            // Fallback if event_halls not defined
            allowed_exhibitor_halls =
                conttoken_data.enable_exhibitor_hall == 1 && Array.isArray(exhibitor_halls)
                    ? exhibitor_halls
                    : [];
            allowed_speaker_halls =
                conttoken_data.enable_speaker_hall == 1 && Array.isArray(speaker_halls)
                    ? speaker_halls
                    : [];
        }

        // Agenda halls: if event_halls exist, use them; else union of speaker + exhibitor
        const allowed_agenda_halls = hasEventHalls
            ? event_halls
            : [...allowed_speaker_halls, ...allowed_exhibitor_halls];

        // ---- Fetch meta info (latest speakers/exhibitors) ----
        const { response: metaResp } = await _getEventMetaInfo({ eventtoken });
        const metaOutput = metaResp?.output || {};

        const spk_list = Array.isArray(metaOutput.event_speaker)
            ? metaOutput.event_speaker
            : [];

        const exh_list = Array.isArray(metaOutput.event_exhibitor)
            ? metaOutput.event_exhibitor
            : [];

        // Speaker halls (simple list)
        const speaker_hall_list = spk_list
            .map(item => item.spk_hall)
            .filter(Boolean);

        // Exhibitor halls (flatten to single-dimension array)
        const exhibitor_hall_list = exh_list.reduce((acc, item) => {
            if (Array.isArray(item.exh_hall)) {
                acc.push(...item.exh_hall);
            } else if (item.exh_hall) {
                acc.push(item.exh_hall);
            }
            return acc;
        }, []);

        // ---- User / ticket context ----
        const sponsor_type      = $('#sponsor_type').val() || '';
        const is_organizer      = $('#is_organizer').val() || 0;
        const user_profile_type = $('#user_profile_type').val() || '';
        const rsvp_sponsor_title = $('#rsvp_sponsor_title').val() || '';

        let u = 0;

        // const ticketArr = (conttoken_data.ticket_types || []).find(
        //     ticket => ticket.title === rsvp_sponsor_title
        // ) || null;

        // Helper: should this hall be visible for this user?
        function isHallVisible(hall) {
            // In version 2 we allow all halls (per your current logic)
            if (event_form_version == 2) return 1;

            const viewaccess = hall?.viewaccess || '';
            if (!viewaccess) return 0;

            if (viewaccess.includes('all')) return 1;

            // Organizers see all halls with viewaccess defined
            if (is_organizer == 1) return 1;

            if (sponsor_type && sponsor_type !== 'undefined' && viewaccess.includes(sponsor_type)) {
                return 1;
            }
            if (user_profile_type && viewaccess.includes(user_profile_type)) {
                return 1;
            }
            if (rsvp_sponsor_title && viewaccess.includes(rsvp_sponsor_title)) {
                return 1;
            }
            return 0;
        }

        // ---------------- Exhibitor halls dropdown ----------------
        $('#exh_hall_list').empty();
        $('#exh_hall_list').append(
            `<a class="dropdown-item" onclick="LoadMetaWithHall('event_exhibitor','','All');">All</a>`
        );

        const exh_allowed_list = {};

        allowed_exhibitor_halls.forEach(hall => {
            const hall_name  = hall.name;
            const hall_id    = hall.id;
            const hall_token = btoa(hall.name);

            // Assign color
            const color = shuffledColorArray[u % shuffledColorArray.length];
            hall_color_array[hall_name] = color;
            u++;

            // Build allowed list structure
            exh_allowed_list[hall_name] = {};
            $.each(hall.profiletype, function (pfkey, pftype) {
                exh_allowed_list[hall_name][pftype] = {
                    max:     hall.hallcount[pfkey],
                    allowed: hall.hallcount[pfkey]
                };
            });

            let showhall = isHallVisible(hall);

            // Also require: hall name present in exhibitor_hall_list
            if (showhall === 1 && typeof hall_name !== 'undefined' && hall_name !== 'undefined' && $.inArray((hall_name), exhibitor_hall_list) !== -1) {
                const hall_option = `<a class="dropdown-item" onclick="LoadMetaWithHall('event_exhibitor','${hall_name}','${hall_name}');">${hall_name}</a>`;
                $('#exh_hall_list').append(hall_option);
            }
        });

        // ---------------- Speaker halls dropdown ----------------
        $('.spk_hall_list').empty();
        $('.spk_hall_list').append(
            `<a class="dropdown-item" onclick="LoadMetaWithHall('event_speaker','','All');">All</a>`
        );

        const spk_allowed_list = {};

        allowed_speaker_halls.forEach(hall => {
            const hall_name  = hall.name;
            const hall_id    = hall.id;
            const hall_token = hall.name; // (was btoa in comment)

            spk_allowed_list[hall_name] = {};
            $.each(hall.profiletype, function (pfkey, pftype) {
                spk_allowed_list[hall_name][pftype] = {
                    max:     hall.hallcount[pfkey],
                    allowed: hall.hallcount[pfkey]
                };
            });

            const color = shuffledColorArray[u % shuffledColorArray.length];
            hall_color_array[hall_name] = color;
            u++;

            let showhall = isHallVisible(hall);

            // Hall must also exist in speaker_hall_list
            if (showhall === 1 && $.inArray((hall_name), speaker_hall_list) !== -1) {
                const hall_option = `<a class="dropdown-item" onclick="LoadMetaWithHall('event_speaker','${hall_token}','${hall_name}');">${hall_name}</a>`;
                $('.spk_hall_list').append(hall_option);
            }
        });

        // Cache access map in IndexedDB
        const cachePayload = {
            success: true,
            output: {
                exhibitor: exh_allowed_list,
                speaker:   spk_allowed_list
            }
        };

        IntaoDB.setItem(objStores.event_store.name, {
            taoh_data: `event_hall_access_${eventtoken}`,
            values: cachePayload,
            timestamp: Date.now()
        });

        // ---------------- Agenda halls + search ----------------
        $('.agenda_hall_list').empty();
        $('#search_halls').empty();

        let searchhallcount = 0;

        if (allowed_agenda_halls.length > 0) {
            $('#search_halls').show();
        } else {
            $('#search_halls').hide();
        }

        $('.agenda_hall_list').append(`<a class="dropdown-item" onclick="LoadMetaWithHall('','','All','agenda');">All</a>`);

        allowed_agenda_halls.forEach(hall => {
            const hall_name  = hall.name;
            const hall_id    = hall?.id ?? '';
            const hall_token = hall.name; // (was btoa(hall.name))

            let showhall = isHallVisible(hall);

            // Must have speakers or exhibitors in this hall
            const decodedName = (hall_name);
            const hasSpeakerHall   = $.inArray(decodedName, speaker_hall_list) !== -1;
            const hasExhibitorHall = $.inArray(decodedName, exhibitor_hall_list) !== -1;

            if (showhall === 1 && typeof hall_name !== 'undefined' && hall_name !== 'undefined' && (hasSpeakerHall || hasExhibitorHall)) {
                const hall_option = `<a class="dropdown-item" onclick="LoadMetaWithHall('','${hall_token}','${hall_name}','agenda');">${hall_name}</a>`;
                $('.agenda_hall_list').append(hall_option);

                const hallLink =
                    `${TAOH_CURR_APP_URL}/hall/${eventtoken}/${encodeURIComponent(hall_name)}-${hall_id}`;

                $('#search_halls').append(
                    $('<option>', {
                        value: hallLink,
                        text: decodedName
                    })
                );

                searchhallcount++;
            }
        });

        if (searchhallcount === 0) {
            $('#search_halls').hide();
        }
    }

    async function getEventMetaInfo(eventtoken, type = '', search = '', tab_name = '') {
        var eventMetaInfoBaseInfoKey = `event_MetaInfo_${eventtoken}`;
        if (search != '') {
            eventMetaInfoBaseInfoKey = `event_MetaInfo_${eventtoken}_${type}_${search}`;
        }

        IntaoDB.getItem(objStores.event_store.name, eventMetaInfoBaseInfoKey).then((data) => {

            if (data?.values) {
                if (search == '' || type == 'agenda') {
                    getEventExhibitors(eventtoken, data.values, hall_color_array, search, tab_name);
                    getEventSpeakers(eventtoken, data.values, hall_color_array, search, tab_name);
                    getEventAgenda(eventtoken, data.values, hall_color_array, search, tab_name);
                    getEventRooms(eventtoken, data.values, search, tab_name);
                    getEventTables(eventtoken, data.values, search, tab_name);
                    updateDashboardInfo(eventtoken);
                } else {
                    if (type == 'agenda' || tab_name == 'agenda')
                        getEventAgenda(eventtoken, data.values, hall_color_array, search, tab_name);
                    if (type == 'event_speaker')
                        getEventSpeakers(eventtoken, data.values, hall_color_array, search, tab_name);
                    if (type == 'event_exhibitor')
                        getEventExhibitors(eventtoken, data.values, hall_color_array, search, tab_name);
                    if (type == 'rooms')
                        getEventRooms(eventtoken, data.values, search, tab_name);
                    if (type == 'tables')
                        getEventTables(eventtoken, data.values, search, tab_name);
                    updateDashboardInfo(eventtoken);
                }

                // getEventLobbyDashboard(eventtoken,data.values,search);
                getEventsHall(eventtoken);
            } else {
                console.log('call updateEventMetaInfo');
                updateEventMetaInfo(eventtoken, true, type, search, tab_name);
                updateDashboardInfo(eventtoken);
                getEventsHall(eventtoken);
            }
        });
    }

    async function updateEventMetaInfo(eventtoken, serverFetch = true, type = '', search = '', tab_name = '') {
        let finalSearch = search;
        if (type === 'event_speaker' && tab_name !== 'agenda') {
            const txt = $.trim($("#dropdownMenuLink_speaker").text() || '');
            finalSearch = $.trim(
                txt.replace('Select Speaker Hall', '')
                    .replace('All', '')
            );
        }
        const speakerName = (type === 'event_speaker') ? $("#speaker_search").val() : '';

        let eventMetaInfoBaseInfoKey = `event_MetaInfo_${eventtoken}`;
        if (type || finalSearch || speakerName) eventMetaInfoBaseInfoKey += `_${type}_${crc32(finalSearch + speakerName)}`;

        console.log('updateEventMetaInfo events_lobby_hall', eventMetaInfoBaseInfoKey, serverFetch, type, search, finalSearch, tab_name);

        const setNoResultUI = (resp, msg = 'No Result Found') => {
            $('#agenda_list').html(msg);
            $('#speaker_list').html(msg);
            $('#exhibitors_list').html(msg);
            $('#rooms_list').html('No Rooms Found');

            const event_status = $('#event_status_hidden').val();

            if (!finalSearch) {
                if (event_status == 1 || event_status == 0) {
                    $('#speaker_desc,#speaker_top,#exhibitor_desc,#exhibitor_top,#dashboard_desc').remove();
                } else {
                    $("#dashboard-tab,#dashboard-slash").hide();
                    $(".speaker_filter").addClass("align-self-start lblur");
                    $("#speaker_default_banner,#speaker_default_list").show();
                    $("#exhibitor_default_banner,#exhibitor_default_list").show();
                    $("#rsvp_default_list").show();
                    if ($("#is_organizer").val() == 1) $('.rsvp_actions').show();
                }
            }

            getEventAgenda(eventtoken, resp, hall_color_array);
            loader(false, $("#agenda_loaderArea"));
            loader(false, $("#speaker_loaderArea"));
            loader(false, $("#exhibitors_loaderArea"));
        };

        const runRender = (metaResp) => {
            // $("#speakers-tab, #exhibitors-tab").show();

            if (!type) {
                getEventExhibitors(eventtoken, metaResp, hall_color_array, search);
                getEventSpeakers(eventtoken, metaResp, hall_color_array, search);
                getEventAgenda(eventtoken, metaResp, hall_color_array, search);
                getEventRooms(eventtoken, metaResp, search);
                getEventTables(eventtoken, metaResp, search);
                return;
            }

            if (type === 'event_exhibitor') getEventExhibitors(eventtoken, metaResp, hall_color_array, search);
            if (type === 'event_speaker') getEventSpeakers(eventtoken, metaResp, hall_color_array, search);
            if (type === 'agenda' || tab_name === 'agenda') getEventAgenda(eventtoken, metaResp, hall_color_array, search);
            if (type === 'rooms') getEventRooms(eventtoken, metaResp, search);
            if (type === 'tables') getEventTables(eventtoken, metaResp, search);
        };

        const handleResponse = (resp) => {
            if (resp && resp.success) {
                IntaoDB.setItem(objStores.event_store.name, {
                    taoh_data: eventMetaInfoBaseInfoKey,
                    values: resp,
                    timestamp: Date.now()
                });
                runRender(resp);
                return;
            }

            setNoResultUI(resp, 'No Result Found');
        };

        if (serverFetch) {
            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'get_event_MetaInfo',
                    taoh_action: 'get_event_MetaInfo',
                    token: _taoh_ajax_token,
                    eventtoken,
                    type,
                    search: finalSearch,
                    search_speaker_name: speakerName
                },
                success: handleResponse,
                error: (xhr) => {
                    console.error('Error:', xhr.status);
                    setNoResultUI({success: 0, output: []}, 'No Result Found');
                }
            });
            return;
        }

        try {
            const data = await IntaoDB.getItem(objStores.event_store.name, eventMetaInfoBaseInfoKey);
            if (data?.values) {
                runRender(data.values);
            } else {
                updateEventMetaInfo(eventtoken, true, type, search, tab_name);
            }
        } catch (e) {
            // if IndexedDB fails, fallback to server
            updateEventMetaInfo(eventtoken, true, type, search, tab_name);
        }
    }


    async function LoadMetaWithHall(type, search, search_name, tab_name) {
        loader(true, $("#agenda_loaderArea"), 30);
        loader(true, $("#speaker_loaderArea"), 30);
        loader(true, $("#exhibitors_loaderArea"), 30);
        // loader(true, $("#rsvpdir_loaderArea"),30);

        await getEventMetaInfo(eventtoken, type, search, tab_name);

        /* Display Select hall dropdown value */
        if (type == 'event_speaker') {
            curid = 'dropdownMenuLink_speaker';
        }
        if (type == 'event_exhibitor') {
            curid = 'dropdownMenuLink_exh';
        }
        if (tab_name == 'agenda') {
            curid = 'dropdownMenuLink_agenda';
        }
        $("#" + curid).text(search_name);
        /* Display Select hall dropdown value */

        loader(false, $("#agenda_loaderArea"), 30);
        loader(false, $("#speaker_loaderArea"), 30);
        loader(false, $("#exhibitors_loaderArea"), 30);
    }

    function delete_events_meta_into(eventtoken) {
        var eventKey = `event_MetaInfo_${eventtoken}`;
        getIntaoDb(dbName).then((db) => {
            let dataStoreName = EVENTStore;
            const transaction = db.transaction(dataStoreName, 'readwrite');
            const objectStore = transaction.objectStore(dataStoreName);
            const request = objectStore.openCursor();
            request.onsuccess = (event) => {
                const cursor = event.target.result;
                if (cursor) {
                    const index_key = cursor.primaryKey;
                    if (index_key.includes(eventKey)) {
                        objectStore.delete(index_key);
                    }
                    cursor.continue();
                }
            };
        }).catch((err) => {
            console.log('Error in deleting data store');
        });
    }

    function updateDashboardInfo(eventtoken) {
        loader(true, $("#dashboard_loaderArea"));

        getEventSavedInfo({eventtoken})
            .then(({response}) => {
                if (response.success) {
                    getEventLobbyDashboard(eventtoken, response, hall_color_array);
                } else {
                    $('#dashboard_list').html('');
                    $("#dashboard-tab,#dashboard-slash").hide();
                    $('#my_agenda_default_banner').show();
                    loader(false, $("#dashboard_loaderArea"));
                }
            })
            .catch(err => {
                console.error('Failed to load event saved info:', err);
            });
    }

    function performSpeakerSearch() {
        loader(true, $("#speaker_loaderArea"), 30);
        let search = $("#speaker_search").val();
        updateEventMetaInfo(eventtoken, true, 'event_speaker', search);
    }

    /* function getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    } */

    function taoh_add_video_chat() {
        let data = {
            'taoh_action': 'taoh_add_video_chat',
            // 'my_token': ntw_room_key,
            // 'guest_token': ntw_room_key,
            // 'parent_keyslug': ntw_room_key,
            'my_pToken': my_pToken,
            'network_title': window._elh_cfg.speakerTitle
        };
        $.post(_taoh_site_ajax_url, data, function (response) {
            $('#video_room_join_now_btn i').removeClass('la-spinner la-spin').addClass('fa-video-camera');
            $("#video_room_join_confirmation").modal('hide');
            // loadCustomRooms();
            if (response.my_link) window.open(response.my_link);
        }).fail(function () {
            $('#video_room_join_now_btn i').removeClass('la-spinner la-spin').addClass('fa-video-camera');
        });
    }

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

    function openDesc() {
        $('.hall-list-container').removeClass('active');
        $('.hall-list-container').removeClass('show');
        $('#desc_desc').addClass('active');
        $('#desc_desc').addClass('show');
        const tabs = document.querySelectorAll('#myTab .nav-link');
        tabs.forEach(tab => tab.classList.remove('active'));
    }

    function openBanner() {
        $('.hall-list-container').removeClass('active show');
        $('#desc_desc').removeClass('active show');
        $('#add_banner_tab').addClass('active show');
        const tabs = document.querySelectorAll('#myTab .nav-link');
        tabs.forEach(tab => tab.classList.remove('active'));
    }
