<?php
taoh_get_header();

$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;

if (!$taoh_user_is_logged_in) {
    header("Location: " . TAOH_SITE_URL_ROOT . '/login');
}

$user_info_obj = taoh_user_all_info();
$valid_user = (bool) $user_info_obj->profile_complete ?? false;

$ptoken = $taoh_user_is_logged_in ? $user_info_obj->ptoken : '';
$puser_name = $taoh_user_is_logged_in ? $user_info_obj->fname : '';

function showErrorPage($base_path, $error_code = 1001, $error_from = '', $error_data = [])
{
    include_once $base_path . '/includes/error.php';
    taoh_get_footer();
    exit();
}

$appname = TAOH_CURR_APP_SLUG ?? 'events';
$speaker_id = (int) taoh_parse_url(2);
$eventtoken = taoh_parse_url(3);

if (empty($speaker_id)) {
    showErrorPage(TAOH_APP_PATH . '/' . $appname, 1001, 'event_speaker');
    taoh_exit();
}

?>

    <style>
    </style>

<div class="detail-hall aw aw-logo aw-loader">
    <div class="img-vdo-container py-4">
        <div class="container hall-detail-img">
            <div id="speaker_top_banner">
                <div id="speaker_banner_container" style="display: none;">

                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <div class="d-flex">
            <ul class="breadcrumb-nav pb-2" id="speaker_breadcrumb">
                <li><a href="<?php echo TAOH_SITE_URL_ROOT; ?>">Home</a></li>
            </ul>
             <p class="click_view small pl-2"></p>
        </div>

        <div class="mt-2 d-flex flex-row justify-content-between align-items-start flex-wrap">
            <div class="d-flex flex-wrap align-items-start mb-3" style="gap: 12px; flex: 1;">
                <div class="mr-lg-3">
                    <div class="d-flex align-items-center mb-3" id="speaker_logo_blk" style="gap: 12px;">
                        <img class="speaker-logo" id="speaker_logo" src="<?php echo TAOH_SITE_URL_ROOT . '/assets/images/placeholder-image.jpg'; ?>" alt="Speaker logo">
                        <div>
                            <h4 class="speaker-title mb-1" id="speaker_title"></h4>
                            <h6 class="speaker-subtitle" id="speaker_subtitle"></h6>
                        </div>
                    </div>

                    <div id="speaker_website_blk" style="display: none;"></div>

                    <div id="speaker_timeslot_blk"></div>

                    <div id="speaker_hall"></div>
                </div>

            </div>

            <div class="exh-d-right-rbtn-blk" id="speaker_room_btn_blk" style="display: none;">
                <p class="hall-text-sm mb-2">Find and connect with the people in this room !</p>
                <div class="d-flex flex-wrap" style="gap: 6px;">
                    <div class="mr-lg-2" id="speaker_video_room_links" style="display: none;"></div>

                    <div class="" id="speaker_tao_room_links" style="display: none;"></div>
                </div>
                
                <div class="mt-2" id="speaker_location_blk" style="display: none;"></div>
            </div>
        </div>

        <div class="d-flex flex-column flex-lg-row  justify-content-lg-between flex-wrap mt-2" style="gap: 16px;">
            <div style="max-width: 936px; flex: 1;">
                <div class="hall-text-sm mt-3" id="spk_description"></div>
                <div class="hall-text-sm mt-3" id="spk_tags"></div>
                <h3>Session Speaker's Information</h3>
                <div class="hall-text-sm mt-3" id="session_speakers"></div>
            </div>

            <div class="exh-d-right">
            
            <div class="modal fade raffleAnswerModal" id="raffleAnswerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog bg-white" role="document">
                        <div class="modal-content">
                        <div class="modal-header bg-white align-items-center " style="border: none; border-bottom: 1px solid #d3d3d3;">
                            <h3 id="raffle_question_title"></h3>
                            <div class="justify-content-end">
                                <button type="button" class="btn" data-dismiss="modal" aria-label="Close">
                                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12.6153 2.22013C13.1228 1.71256 13.1228 0.888255 12.6153 0.380681C12.1077 -0.126894 11.2834 -0.126894 10.7758 0.380681L6.5 4.66055L2.22013 0.384741C1.71256 -0.122833 0.888255 -0.122833 0.380681 0.384741C-0.126894 0.892316 -0.126894 1.71662 0.380681 2.22419L4.66055 6.5L0.384742 10.7799C-0.122833 11.2874 -0.122833 12.1117 0.384742 12.6193C0.892316 13.1269 1.71662 13.1269 2.22419 12.6193L6.5 8.33945L10.7799 12.6153C11.2874 13.1228 12.1117 13.1228 12.6193 12.6153C13.1269 12.1077 13.1269 11.2834 12.6193 10.7758L8.33945 6.5L12.6153 2.22013Z" fill="#D3D3D3"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="modal-body">
                            
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    const isLoggedIn = <?= json_encode($taoh_user_is_logged_in); ?>;
    let eventtoken = '<?= $eventtoken ?? ''; ?>';
    let speaker_id = '<?= $speaker_id ?? 0; ?>';
    const my_pToken = '<?= $ptoken ?? ''; ?>';
    const my_username = '<?= $puser_name ?? ''; ?>';
    let TAOH_CURR_APP_URL = '<?= TAOH_CURR_APP_URL; ?>';

    
    let user_timezone;

    if (isLoggedIn) {
        user_timezone = '<?= taoh_user_timezone(); ?>';
    }
    if (!isLoggedIn || !user_timezone?.trim()) {
        let clientTimeZone = typeof getCookie === 'function' ? getCookie('client_time_zone') : null;
        user_timezone = convertDeprecatedTimeZone(clientTimeZone || Intl.DateTimeFormat().resolvedOptions().timeZone);
    }
    if (!isValidTimezone(user_timezone)) user_timezone = 'UTC';

    // alert(speaker_id);
    $(document).ready(function() {
       function getEventSpeakerrInfo(requestData, serverFetch = false, callback = null) {
            // console.log(requestData);
            if (!requestData.eventtoken || !requestData.speaker_id) return;

            const event_speaker_key = `event_MetaInfo_${requestData.eventtoken}_speaker_${requestData.speaker_id}`;

            const handleResponse = (response, saveToDB = true) => {
                if (response.success) {
                    if (saveToDB) {
                        IntaoDB.setItem(objStores.event_store.name, {
                            taoh_data: event_speaker_key,
                            values: response,
                            timestamp: Date.now()
                        });
                    }

                    if (typeof callback === 'function') {
                        callback(requestData, response);
                    }
                } else {
                    console.log('Failed to fetch event speaker details! Try Again');
                }
            };

            if (serverFetch) {
                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'POST',
                    data: {
                        taoh_action: 'get_speaker_detail',
                        token: _taoh_ajax_token,
                        eventtoken: requestData.eventtoken,
                        speaker_id: requestData.speaker_id
                    },
                    dataType: 'json',
                    success: (response) => handleResponse(response, true),
                    error: (xhr) => console.error('Error:', xhr.status)
                });
            } else {

                IntaoDB.getItem(objStores.event_store.name, event_speaker_key).then((data) => {
                    if (data?.values) {
                        handleResponse(data.values, false);
                    } else {
                        getEventSpeakerrInfo(requestData, true, callback);
                    }
                });
            }
        }

        getEventSpeakerrInfo({
            eventtoken,
            speaker_id
        }, false, (requestData, response) => {
            if (response.success) {
                let event_speaker_info = response.output;
                getEventBaseInfo({ eventtoken }, false)
                    .then(({requestData, response}) => {
                        let event_output = response.output;
                        let event_owner = event_output.ptoken;
                        let conttoken_data = event_output.conttoken;
                        local_timezone = event_output.local_timezone;
                        locality = event_output.conttoken.locality;

                        let event_title = conttoken_data?.title;
                        

                        let speakerBreadcrumbHTML = `<li><a href="${_taoh_site_url_root}/events">Events</a></li>
                            <li><a href="${_taoh_site_url_root}/events/chat/id/events/${eventtoken}">${event_title}</a></li>
                            ${event_speaker_info.spk_title ? `<li>${taoh_desc_decode(event_speaker_info.spk_title)}</li>` : ''}
                        `;
                        $('#speaker_breadcrumb').append(speakerBreadcrumbHTML);

                        let event_organizer_ptokens = (conttoken_data.event_organizer_ptokens || "")
                            .split(',')
                            .map(token => token.trim())
                            .filter(token => token);

                        if(event_owner) event_organizer_ptokens.push(event_owner);

                        let event_instance_owner = conttoken_data.ptoken;
                        event_organizer_ptokens.push(event_instance_owner);

                        if(event_organizer_ptokens.includes(my_ptoken)){
                            $(".overallrating").hide();
                            $(".review_count").hide();
                        }

                        
                    // speaker_timeslot_blk
                    let event_timestamp_start_data = {
                        utc_datetime: event_speaker_info.spk_datefrom.replace(/[T:-]/g,'')+'00',
                        local_datetime: event_speaker_info.spk_datefrom.replace(/[T:-]/g,'')+'00',
                        timezone: event_speaker_info.spk_timezoneSelect,
                        locality: locality
                    };

                    let event_timestamp_end_data = {
                        utc_datetime: event_speaker_info.spk_dateto.replace(/[T:-]/g,'')+'00',
                        local_datetime: event_speaker_info.spk_dateto.replace(/[T:-]/g,'')+'00',
                        timezone: event_speaker_info.spk_timezoneSelect,
                        locality: locality
                    };
                    let startdate = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'dd MMM yyyy',0); // EEEE, dd MMM yyyy
                    let starttime = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'hh:mm A',1);
                    
                    let enddate = format_event_timestamp(event_timestamp_end_data, user_timezone, 'date', 'dd MMM yyyy',0);
                    let endtime = format_event_timestamp(event_timestamp_end_data, user_timezone, 'date', 'hh:mm A',1);

                    if(startdate == enddate){
                        $("#speaker_timeslot_blk").html(`<p>${startdate}                  
                                    ${starttime} - ${endtime}
                                    </p>`);
                    }else{
                        $("#speaker_timeslot_blk").html(`<p class="px-4 mx-1">${startdate} ${starttime} - ${enddate} ${endtime}</p>`);
                    }
                    
                });

                
                const speakerBannersArray = [event_speaker_info.spk_image].filter(url => url.trim() !== "" && isValidURL(url)).map(url => ({
                    src: url,
                    type: getMediaType(url)
                }));

                console.log(speakerBannersArray);

                const galleryContainer = document.getElementById("speaker_banner_container");
                const mainDisplay = document.createElement("div");
                mainDisplay.id = "speaker_banner_image";
                galleryContainer.before(mainDisplay);
                
                
                function formatVideoSrc(videoSrc) {
                    if (videoSrc.includes("youtube.com")) {
                        return `https://www.youtube.com/embed/${videoSrc.split("v=")[1]?.split("&")[0]}`;
                    }
                    if (videoSrc.includes("youtu.be")) {
                        const videoId = videoSrc.split("youtu.be/")[1];
                        return `https://www.youtube.com/embed/${videoId}`;
                    }
                    if (videoSrc.includes("vimeo.com")) {
                        return `https://player.vimeo.com/video/${videoSrc.split("vimeo.com/")[1]}`;
                    }
                    return videoSrc; // For other video formats
                }

                function displayMedia(media) {
                    if (!media) return;

                    mainDisplay.innerHTML = "";
                    let mediaHtml = "";

                    if (media.type === "image") {
                        mediaHtml = `
                            <div class="cover-event-image">
                                <div class="speaker-bg" style="background-image: url('${media.src}');"></div>
                                <div class="glass-overlay"></div>
                                <img src="${media.src}" class="main-image" alt="Event">
                            </div>
                        `;
                    } else if (media.type === "video") {
                        let videoSrc = formatVideoSrc(media.src);
                        mediaHtml = `<iframe src="${videoSrc}" class="main-media" allowfullscreen allow="autoplay" style="width: 100%; height: 480px;"></iframe>`;
                    }

                    mainDisplay.innerHTML = mediaHtml;
                }

                
                if (speakerBannersArray[0]) {
                    displayMedia(speakerBannersArray[0]);
                } else {
                    const noImage = _taoh_site_url_root + '/assets/images/hall-detail.png';
                    const mediaHtml = `
                            <div class="cover-event-image">
                                <div class="exhibitor-bg" style="background-image: url('${noImage}');"></div>
                                <div class="glass-overlay"></div>
                                <img src="${noImage}" class="main-image" alt="Event">
                            </div>`;
                    $('#speaker_banner_image').html(mediaHtml);
                }
                
                if(event_speaker_info.spk_logo_image && isValidURL(event_speaker_info.spk_logo_image)){
                    $('#speaker_logo').attr('src', event_speaker_info.spk_logo_image);
                }

                
                if (event_speaker_info.spk_hero_button_text && isValidURL(event_speaker_info.spk_hero_button_url)) {
                    $('#speaker_banner_image').append(`<a href="${event_speaker_info.spk_hero_button_url}" target="_blank" class="btn hero-button">${event_speaker_info.spk_hero_button_text}</a>`);
                }

                $('#speaker_title').text(taoh_desc_decode(event_speaker_info.spk_title));
                $('#speaker_subtitle').text(taoh_desc_decode(event_speaker_info.spk_sdesc));
                let speakerWebsiteUrl = event_speaker_info.spk_hero_button_url || '';
                if (speakerWebsiteUrl.trim()) {
                    if (isValidURL(speakerWebsiteUrl)) {
                        $('#speaker_website_blk').html(`<div class="hall-text-sm-bold mb-2"><i class="fa fa-globe mr-1" aria-hidden="true"></i> Website :
                            <a href="${speakerWebsiteUrl}" class="btn link" id="speaker_website"><span>${speakerWebsiteUrl}</span></a></div>`);
                    } else {
                        $('#speaker_website_blk').html(`<div class="btn link" id="exhibitor_website">
                            <i class="fa fa-globe mr-1" aria-hidden="true"></i> Website : <span title="${speakerWebsiteUrl}">${speakerWebsiteUrl}</span></div>`);
                    }
                    $('#speaker_website_blk').show();
                }

                $("#speaker_hall").html(event_speaker_info.spk_hall);
                

                let speakderLocationUrl = event_speaker_info.spk_room_location || '';
                if (speakderLocationUrl.trim()) {
                    if (isValidURL(speakderLocationUrl)) {
                        $('#speaker_location_blk').html(`<div class="hall-text-sm-bold mb-2"><i class="fa fa-map-marker mr-1" aria-hidden="true"></i> Location :
                            <a href="${speakderLocationUrl}" class="btn link" id="exhibitor_location"><span>${speakderLocationUrl}</span></a></div>`);
                    } else {
                        $('#speaker_location_blk').html(`<div class="btn link" id="exhibitor_location">
                            <i class="fa fa-map-marker mr-1" aria-hidden="true"></i> Location : <span title="${speakderLocationUrl}">${speakderLocationUrl}</span></div>`);
                    }
                    $('#speaker_location_blk').show();
                    $('#speaker_room_btn_blk').show();
                }

                let speakerExternalRoomUrl = event_speaker_info.spk_streaming_link || '';
                let enable_tao_networking = event_speaker_info.enable_tao_networking || '';

                if (speakerExternalRoomUrl.trim()) {
                    const speaker_video_room_links = $('#speaker_video_room_links');
                    if (isValidURL(speakerExternalRoomUrl)) {
                        speaker_video_room_links.html(`<a href="${speakerExternalRoomUrl}" class="btn join" id="external_video_room">
                            <span class="px-2">Join Video Room</span></a>`);
                    } else {
                        speaker_video_room_links.html(`<div class="btn join" id="external_video_room">
                           <span class="px-2" title="${speakerExternalRoomUrl}">Join Video Room: ${speakerExternalRoomUrl}</span></div>`);
                    }
                    speaker_video_room_links.show();
                    $('#speaker_room_btn_blk').show();
                }

                if ((enable_tao_networking == '1' || enable_tao_networking == 'on')) {
                    const speaker_tao_room_links = $('#speaker_tao_room_links');
                  
                    speaker_tao_room_links.html(
                            `<a href="${TAOH_CURR_APP_URL}/chat/id/events/${eventtoken}" class="btn join" id="tao_video_room">
                            <span class="px-2">Join Tao Networking Now!</span></a>`);
                            speaker_tao_room_links.show();
                    $('#speaker_room_btn_blk').show();
                }

                
                const safeMessage = document.createElement('pre');
                safeMessage.textContent = event_speaker_info.spk_desc || '';
                let safeMessageHtml = safeMessage.innerHTML.replace(/\n/g, '<br>')
                    .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');
                $('#spk_description').html(taoh_desc_decode(safeMessageHtml));

                if(event_speaker_info.spk_tags != ''){
                    $.each(event_speaker_info.spk_tags,function(k,tag){
                        $("#spk_tags").append("<span>"+tag+"</span>|");
                    });
                }

                // session_speakers
                let speakerDetails = '';
                if(event_speaker_info.spk_name != ''){
                    $.each(event_speaker_info.spk_name,function(i,speaker_name){
                        speakerDetails += `<p class="mt-2 d-flex"><img src="${event_speaker_info.spk_profileimg[i]}" width="100"></p>
                                            <p class="mt-2 d-flex bold">${speaker_name}</p>
                                            <p>${event_speaker_info.spk_company[i]},${event_speaker_info.spk_desig[i]}</p>
                                            <div>${taoh_desc_decode(event_speaker_info.spk_bio[i])}</div>`;
                    });

                    $("#session_speakers").html(speakerDetails);
                }

            }

            $('.aw').awloader('hide');

    });
    $('.aw').awloader('hide');
});

</script>

<?php taoh_get_footer(); ?>