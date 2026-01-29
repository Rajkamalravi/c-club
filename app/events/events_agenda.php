<?php
//echo "===ssssssssss";die();
$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
$user_info_obj = $taoh_user_is_logged_in ? taoh_user_all_info() : null;

$full_location = (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->full_location ?? '';
$user_country_array = explode(',', $full_location);
$user_country_name = trim(end($user_country_array));

$valid_user = $taoh_user_is_logged_in && in_array($user_info_obj?->profile_complete ?? null, [1, '1'], true);
$current_app = taoh_parse_url(1);

$cache_name = 'event_Saved_' . $eventtoken;
$taoh_call = "events.content.save.list";
$type = '';
$taoh_vals = array(
    'mod' => 'events',
    'token' => taoh_get_dummy_token(),
    'eventtoken' => $eventtoken,
    'cache_required' => 0
);
$data = taoh_apicall_get($taoh_call, $taoh_vals);
$data = json_decode($data, true);

$likedArr = [];
if (!empty($data['output']) && is_array($data['output'])) {
    foreach ($data['output'] as $key => $saveddataArr) {
        if (!is_array($saveddataArr)) continue;

        foreach ($saveddataArr as $dkey => $saveddata) {
            if (is_array($saveddata) && isset($saveddata['ID'])) {
                $likedArr[$key][] = $saveddata['ID'];
            }
        }
    }
}
?>
<style>
    .new-agenda-list .svg-opt-con, .new-exh-list .svg-opt-con, .spk-v2-list .svg-opt-con, .new-spk-list .svg-opt-con {
        border-radius: 50px;
    }
    .new-exh-list {
        border-left-color: #1997c9;

    }
    .new-agenda-list, .new-spk-con .new-spk-list {
        border-left: 6px solid #2557A7;
    }
    .new-agenda-list .joinus-btn {
        min-height: 39px;
        line-height: 22px;
    }
</style>
<script>
    var my_ptoken = "<?php echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>";
    var eve = "<?php echo $event_token ?? ''; ?>";
    var app_slug = '<?php echo TAO_PAGE_TYPE ?>';
    var likedJSONArr = '<?php echo json_encode($likedArr ?? []); ?>';
    var likedArr = JSON.parse(likedJSONArr);
    var current_app = '<?php echo $current_app; ?>';
    var user_country_name = '<?= $user_country_name ?? ''; ?>';
    var incomplete_sponsor_form_show = false;

     window.getEventAgenda = async function getEventAgenda(eventtoken,response,hallColorArray='',search='',tab_name='') {
        var colorArray = ['#708090','#A7C7E7','#5F9EA0','#B3A398','#A8BBA2','#C3D6B8', '#EED9C4','#D1C2E0','#F5F5F5','#748CAB','#D6D1CD','#E4C9AF', '#B8E0D2','#E6C0C0','#C8A2C8'];

        let agenda_loaderArea = $("#agenda_loaderArea");
        var agenda_list = response.output?.event_speaker ?? [];
        var exh_list = response.output?.event_exhibitor ?? [];
        var spon_list = response.output?.event_sponsor ?? [];
        let sponsorsBecomeExhibitor = response.output?.event_sponsor_deleted ?? [];
        var rsvp_sponsor_title = $("#rsvp_sponsor_title").val();
        var is_organizer = $("#is_organizer").val();
        var country_locked = $('#event_country_lock').val();
        var event_country_name = $('#event_country_name').val();
        let priceMap = {};
        let sortedExhList;

        if(spon_list !=undefined && spon_list.length > 0){

            spon_list.sort(function(a, b) {
                if (a.ptoken === my_ptoken) return -1; // a comes first
                if (b.ptoken === my_ptoken) return 1;  // b comes first
                // return parseInt(b.final_price) - parseInt(a.final_price);
                const aNoSponsor = a.sponsor_id === '';
                const bNoSponsor = b.sponsor_id === '';
                if (aNoSponsor && !bNoSponsor) return -1;
                if (!aNoSponsor && bNoSponsor) return 1;
                // return parseInt(b.final_price || 0) - parseInt(a.final_price || 0);
                const priceCompare = parseInt(b.final_price || 0) - parseInt(a.final_price || 0);
                if (priceCompare !== 0) return priceCompare;
                const aTitleLower = (a.title || '').toLowerCase();
                const bTitleLower = (b.title || '').toLowerCase();
                return aTitleLower.localeCompare(bTitleLower);
            });
            priceMap = spon_list.reduce(function(acc, sponsor) {
                acc[sponsor.ID] = Number(sponsor.final_price);
                return acc;
            }, {});

        }
        if(exh_list !=undefined && exh_list.length > 0){
            exh_list.forEach(exh => {
                if(exh.sponsor_id == ''){
                    price =  0;
                }else{
                    price = priceMap[exh.sponsor_id];
                }
                if (price !== undefined) {
                    exh.final_price = Number(price);
                } else {
                    exh.final_price = 0; // In case a sponsor_id doesn't exist in the pricemap
                }
            });

            sortedExhList = exh_list.sort(function(a, b) {
                if (a.ptoken === my_ptoken) {
                    return -1;
                }
                if (b.ptoken === my_ptoken) {
                    return 1;
                }
                const aNoSponsor = a.sponsor_id === '';
                const bNoSponsor = b.sponsor_id === '';
                if (aNoSponsor && !bNoSponsor) return -1;
                if (!aNoSponsor && bNoSponsor) return 1;
                // return parseInt(b.final_price || 0) - parseInt(a.final_price || 0);
                const priceCompare = parseInt(b.final_price || 0) - parseInt(a.final_price || 0);
                if (priceCompare !== 0) return priceCompare;
                const aTitleLower = (a.exh_session_title || '').toLowerCase();
                const bTitleLower = (b.exh_session_title || '').toLowerCase();
                return aTitleLower.localeCompare(bTitleLower);
            });
        }

        var local_timezone = '';
        var locality = '';

        let user_timezone;

        if (isLoggedIn) {
            user_timezone = '<?= taoh_user_timezone(); ?>';
        }
        if (!isLoggedIn || !user_timezone?.trim()) {
            let clientTimeZone = typeof getCookie === 'function' ? getCookie('client_time_zone') : null;
            user_timezone = convertDeprecatedTimeZone(clientTimeZone || Intl.DateTimeFormat().resolvedOptions().timeZone);
        }
        if (!isValidTimezone(user_timezone)) user_timezone = 'UTC';

        var content = 'No Agenda Found';
       // $('.for_agenda').hide();

         getEventBaseInfo({eventtoken}, false)
             .then(async ({requestData, response}) => {
                 let event_output = response.output;
                 let conttoken_data = event_output.conttoken;
                 local_timezone = event_output.local_timezone;
                 locality = conttoken_data.locality;

                 $('#agenda_list').html('');
                 var content = '';
                 var exhtitlecontent = '';
                 var is_content = 0;
                 var exh_content = 0;
                 var removeSponsor = [];
                 let incomplete_sponsor_id = null;
                 var no_image = '<?php echo TAOH_SITE_URL_ROOT . "/assets/images/event.jpg" ?>';
                 const isLoggedIn = <?= json_encode($taoh_user_is_logged_in); ?>;
                 var chat_room_status = parseInt(conttoken_data.chat_room_status || 1, 10);
                 var event_live_state = $("#event_live_state").val();
                 let event_live_status = eventLiveState(event_output.utc_start_at || '', event_output.utc_end_at || '', conttoken_data.locality, user_timezone);

                 if (event_live_state == 'live' || event_live_status == 'live') {
                     event_live_link = (chat_room_status == 2 && isValidUrl(conttoken_data.external_link))
                         ? conttoken_data.external_link : '<?php echo TAOH_SITE_URL_ROOT; ?>' + '/' + '<?php echo TAOH_CURR_APP_SLUG; ?>' + '/club/' + (conttoken_data.title) + '-' + event_output.eventtoken;

                     if (chat_room_status) {
                         chatroom_text = '<span>Event Live, ' + (!isValidUser ? 'Complete settings to' : 'Click to') + ' Join</span>';
                     } else {
                         chatroom_text = '<span>Event Live</span>';
                     }
                 }
                 var event_date_to_dispay = $('.event-day').html();

                 content += ` <!-- event networking link -->
                    <div class="new-exh-list mb-3">
                         <!-- <div class="gradient-bg-border"></div> -->

                        <div class="p-3 px-lg-5 d-flex" style="gap: 12px; flex: 1; position: relative;">
                        <a href="#"><i class="fa fa-angle-right" style="position: absolute;right: 20px;top: 42%;font-size: 25px;"></i></a>
                            <div class="d-flex flex-column flex-md-row align-items-md-center" style="gap: 16px; flex: 1;">

                                <div class="g-overlay-con">
                                    <div class="n-hall-list-bg d-md-none" style="background-image: url(${event_output.conttoken.event_image != '' ? event_output.conttoken.event_image : no_image})"></div>
                                    <!--<div class="glass-overlay d-md-none"></div>-->
                                    <img class="n-hall-list-pic" src="${conttoken_data.event_image != '' ? conttoken_data.event_image : no_image}" alt="">
                                </div>
                                <div style="flex: 1;">

                                    <div class="d-flex align-items-center justify-content-between flex-wrap" style="flex: 1; gap: 12px;">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap flex-xl-nowrap my-2" style="flex: 1; gap: 12px;">
                                            <div class="d-flex flex-column" style="gap:3px;">
                                            <p class="n-info-badge mr-2" style="color: #2557A7;" >${event_date_to_dispay}</p>
                                            <div class="d-flex align-items-center mb-1" style="gap: 10px;">
                                                <h6 class="n-exh-name mr-2" >${taoh_desc_decode(conttoken_data.title)}  - Networking</h6>
                                            </div>
                                        </div> `;

                 const is_event_suspended = parseInt(event_output?.status) === 2;
                 const is_event_freeze = parseInt(conttoken_data?.freeze_option) === 1;

                 if (!isLoggedIn) {
                     content += ` <div class="flex-shrink-lg-0 d-flex align-items-center" style="gap: 6px;">
                                        <button type="button" class="mt-3 mb-2 btn btn-primary w-100 create_referral" data-location="${location.href}" data-title="${btoa(unescape(encodeURIComponent(event_output.conttoken.title)))}" data-toggle="modal" data-target="#config-modal"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>Login & Register Now</button>
                                    </div>`;
                 } else if (is_event_freeze || is_event_suspended) {
                     content += ` <div class="flex-shrink-lg-0 d-flex align-items-center" style="gap: 6px;">
                                            <a href="${TAOH_CURR_APP_URL}" class="btn btn-secondary w-100"><i class="fa fa-calendar-times mr-2" aria-hidden="true"></i> Event Suspended</a>
                                         </div>`;
                 } else if (event_live_status == 'before' && is_user_rsvp_done) {
                     if (current_app == 'chat') {
                         content += ` <div class="flex-shrink-lg-0 d-flex align-items-center" style="gap: 6px;">
                                            <span class="btn not-live d-flex align-items-center cursor-pointer px-3" style="width:200px;gap: 12px;">
                                            <svg width="17" height="23" viewBox="0 0 17 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1.4163 0C0.632908 0 0 0.642383 0 1.4375C0 2.23262 0.632908 2.875 1.4163 2.875V3.36914C1.4163 5.27383 2.16428 7.10215 3.49206 8.44981L6.49284 11.5L3.49206 14.5502C2.16428 15.8979 1.4163 17.7262 1.4163 19.6309V20.125C0.632908 20.125 0 20.7674 0 21.5625C0 22.3576 0.632908 23 1.4163 23H2.8326H14.163H15.5793C16.3627 23 16.9956 22.3576 16.9956 21.5625C16.9956 20.7674 16.3627 20.125 15.5793 20.125V19.6309C15.5793 17.7262 14.8313 15.8979 13.5035 14.5502L10.5027 11.5L13.5079 8.44981C14.8357 7.10215 15.5837 5.27383 15.5837 3.36914V2.875C16.3671 2.875 17 2.23262 17 1.4375C17 0.642383 16.3671 0 15.5837 0H14.163H2.8326H1.4163ZM4.24889 3.36914V2.875H12.7467V3.36914C12.7467 4.22266 12.4988 5.04922 12.0385 5.75H4.95704C4.50117 5.04922 4.24889 4.22266 4.24889 3.36914ZM4.95704 17.25C5.11195 17.0119 5.29341 16.7873 5.49258 16.5807L8.49779 13.535L11.503 16.5852C11.7066 16.7918 11.8836 17.0164 12.0385 17.2545H4.95704V17.25Z" fill="#000000"/>
                                            </svg>
                                            <span>Event Not Live!</span>
                                        </span>
                                         </div>`;
                     } else {
                         content += ` <div class="flex-shrink-lg-0 d-flex align-items-center" style="gap: 6px;">
                                            <a href="${TAOH_CURR_APP_URL}/chat/id/events/${eventtoken}" class="btn btn-warning w-100"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>You have registered</a>
                                         </div>`;
                     }
                 } else if (event_live_status == 'after') {
                     content += ` <div class="flex-shrink-lg-0 d-flex align-items-center" style="gap: 6px;">
                                            <a href="${TAOH_CURR_APP_URL}" class="mr-lg-5 btn btn-secondary w-100"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>Ended</a>
                                         </div>`;
                 } else if (isLoggedIn && is_user_rsvp_done && (event_live_state == 'live' || event_live_status == 'live')) {
                     if (chat_room_status) {
                         content += ` <div class="flex-shrink-lg-0 d-flex align-items-center" style="gap: 6px;">
                                                <a target="_blank" href="${event_live_link}" class="btn btn-success w-100 metrics_action" data-metrics="event_join">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80" style="width: 36px">
                                                    <!-- Play circle -->
                                                    <circle cx="40" cy="40" r="28" fill="#fff"></circle>

                                                    <!-- Play triangle -->
                                                    <polygon points="34,28 34,52 54,40" fill="#28a745"></polygon>

                                                    <!-- Sound wave 1 -->
                                                    <path d="M78 26 C84 35, 84 46, 78 54" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>

                                                    <!-- Sound wave 2 -->
                                                    <path d="M88 10 C104 28, 104 54, 88 70" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>
                                                </svg> ${chatroom_text}</a>
                                            </div>`;
                     } else {
                         content += ` <div class="flex-shrink-lg-0 d-flex align-items-center" style="gap: 6px;">
                                             <a href="javascript:void(0)" class="btn btn-success w-100" >
                                             <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80" style="width: 36px">
                                                    <!-- Play circle -->
                                                    <circle cx="40" cy="40" r="28" fill="#fff"></circle>

                                                    <!-- Play triangle -->
                                                    <polygon points="34,28 34,52 54,40" fill="#28a745"></polygon>

                                                    <!-- Sound wave 1 -->
                                                    <path d="M78 26 C84 35, 84 46, 78 54" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>

                                                    <!-- Sound wave 2 -->
                                                    <path d="M88 10 C104 28, 104 54, 88 70" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>
                                                </svg> ${chatroom_text}</a>
                                        </div>`;
                     }

                 } else if (event_live_state == 0) {
                     content += ` <div class="dropdown">
                                     <button class="btn ${event_live_status === 'live' ? 'btn-success' : 'btn-primary'} dropdown-toggle w-100" type="button" id="choose_ticket_agenda" data-ticket_selected="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">${event_live_status === 'live' ? 'LIVE NOW!' : ''} Choose a ticket to Register</button>`;
                     content += '<ul class="ticket-list w-100 px-3 dropdown-menu" id="ticket_list_agenda" aria-labelledby="choose_ticket_agenda" style="z-index: 9999;">';

                     const ticket_types = conttoken_data.ticket_types;
                     let ticket_type_selected = false;
                     i_am_the_sponsor = false;

                     ticket_types.forEach(ticket_type => {
                         if (ticket_type.visibility === 'hidden') return;

                         const applicable = ticket_type.applicable_to || [];
                         const hasAll = applicable.includes('all');
                         if(!hasAll && !applicable.includes(user_profile_type)) {
                             return;
                         }

                         const ticket_type_slug = ticket_type.slug;
                         const ticket_type_title = ticket_type.title;
                         const ticket_type_cost = ticket_type.cost;

                         var classes = 'rsvp_ticket_' + ticket_type_title + ' rsvp_tickets ticket-item';

                         /*if(ticket_type_title == 'Sponsor'){
                             var classes = 'rsvp_ticket_' + ticket_type_title + 'rsvp_tickets ticket-item event_sponsor_right_header';
                         }*/


                         content += '<li class="' + classes + '">';
                         content += '<input type="radio" name="ticket" id="' + ticket_type_slug + '" value="' + encodeURIComponent(ticket_type_title) + '" class="rsvp_ticket_' + ticket_type_title + ' rsvp_tickets d-none"';

                         if (is_user_rsvp_done) {
                             content += ' disabled';
                             if (rsvp_slug === ticket_type_slug) {
                                 content += ' checked';
                                 ticket_type_selected = true;
                             }
                         }

                         content += '>';
                         content += '<label for="' + ticket_type_slug + '" class="item btn w-100">';
                         content += '<p class="item-title text-left">' + ticket_type_title + '</p>';
                         content += '<p class="item-cost text-left">' + (ticket_type.price === 'paid' ? 'Costs you $' + ticket_type_cost : 'Free') + '</p>';
                         content += '</label>';
                         content += '</li>';
                     });
                     content += '</ul>';
                     content += ` </div>`;
                 }
                 content += `
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    </div>
                    <!--  event networking link end --> `;
                 is_content = 1;
                 if (sortedExhList != undefined && sortedExhList.length > 0) {
                     $.each(sortedExhList, function (i, v) {
                         if (v.sponsor_id != '' && v.sponsor_id != null && v.sponsor_id != undefined) {
                             removeSponsor.push(v.sponsor_id);
                         }
                     });
                 }

                 // event_output.conttoken.enable_speaker_hall == 1 &&
                 if (agenda_list != undefined && agenda_list.length > 0) { // added enable_speaker_hall to display speaker list
                     agenda_list.sort((a, b) => new Date(a.spk_datefrom) - new Date(b.spk_datefrom));
                     $.each(agenda_list, function (i, v) {
                         var disableJoinBtn = 'disabled';
                         var displayBtn = 0;
                         var spk_desc = '';
                         if ($.trim(v.spk_desc) != '') {
                             var vdesc = taoh_desc_decode(v.spk_desc);
                             if (vdesc.length > 110) {
                                 var limitedText = vdesc.substring(0, 110);
                                 spk_desc = limitedText + `<span id="dots_${i}">...</span><span id="more_${i}" style="display:none;">${vdesc.substring(110)} </span>
                                    <button class="readmore-btn" onclick="readmore(${i})" id="morebtn_${i}">Read more</button>`;
                             } else {
                                 spk_desc = vdesc;
                             }
                         }

                         let event_timestamp_start_data = {
                             utc_datetime: v.spk_datefrom.replace(/[T:-]/g, '') + '00',
                             local_datetime: v.spk_datefrom.replace(/[T:-]/g, '') + '00',
                             timezone: v.spk_timezoneSelect,
                             locality: locality
                         };

                         let event_timestamp_end_data = {
                             utc_datetime: v.spk_dateto.replace(/[T:-]/g, '') + '00',
                             local_datetime: v.spk_dateto.replace(/[T:-]/g, '') + '00',
                             timezone: v.spk_timezoneSelect,
                             locality: locality
                         };
                         let startdate = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'dd MMM yyyy', 0); // EEEE, dd MMM yyyy
                         let starttime = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'hh:mm A', 1);

                         let enddate = format_event_timestamp(event_timestamp_end_data, user_timezone, 'date', 'dd MMM yyyy', 0);
                         let endtime = format_event_timestamp(event_timestamp_end_data, user_timezone, 'date', 'hh:mm A', 1);

                         color = colorArray[i % colorArray.length];

                         if (hallColorArray[v.spk_hall] != undefined)
                             backgroundColor = hallColorArray[v.spk_hall];
                         else {
                             color = colorArray[i % colorArray.length];
                             backgroundColor = color;
                         }

                         content += `<div class="new-agenda-list p-3 px-lg-5 mb-3 d-flex flex-column flex-md-row" style="gap: 16px;position: relative"><a href="#"><i class="fa fa-angle-right" style="position: absolute;right: 20px;top: 40%;font-size: 25px;"></i></a>`

                         if (v.spk_logo_image != '') {
                             spk_img = v.spk_logo_image;
                         } else {
                             var tt = encodeURIComponent(v.spk_title);
                             spk_img = "<?php echo TAOH_CDN_PREFIX . "/images/igcache/"?>" + tt + "/630_630/blog.jpg";

                         }
                         content += `<a title="View Speaker" target="_blank" href="${TAOH_CURR_APP_URL}/speaker/${eventtoken}/${v.ID}" data-cont="${v.ID}" data-eventtoken="${eventtoken}" data-slug="speaker">
                                <div class="g-overlay-con">
                                    <div class="n-hall-list-bg d-md-none" style="background-image: url('${spk_img}');"></div>
                                    <!--<div class="glass-overlay d-md-none"></div>-->
                                    <img class="n-hall-list-pic" src="${spk_img}" alt="">
                                </div>
                            </a>`;

                         content += ` <div style="flex:1;">
                            <div  class="d-flex flex-wrap-reverse" style="gap: 12px;">
                                <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">`;

                         if (startdate && enddate) {
                             if (startdate == enddate) {
                                 content += `<p class="n-info-badge mr-2" style="color: #3563ae;">
                                  <span style="display: none;">${v.spk_datefrom}-${v.spk_timezoneSelect} to ${v.spk_dateto}-${v.spk_timezoneSelect} <br></span>
                                  ${startdate}, ${starttime} to ${endtime}</p>`; // :rk rmv temp span
                             } else {
                                 content += `<p class="n-info-badge mr-2" style="color: #3563ae;">
                                  <span style="display: none;">${v.spk_datefrom}-${v.spk_timezoneSelect} to ${v.spk_dateto}-${v.spk_timezoneSelect} <br></span>
                                 ${startdate} ${starttime} to ${enddate} ${endtime}</p>`; // :rk rmv temp span
                             }
                         }

                         content += `<p onclick="LoadMetaWithHall('','${v.spk_hall}','${v.spk_hall}','agenda');" class="n-info-badge" style="background-color:#F9A386;color:#3563ae;" >${v.spk_hall}</p>
                                </div>
                                <div class="d-flex ml-auto" style="gap: 12px;">`;


                         if ((v.ptoken == my_ptoken && opt == 'chat') || is_organizer == 1)  {
                             content += `<a title="Edit Speaker" style="width:30px;" class="svg-opt-con btn p-0 edit_speaker metrics_action" id="edit_speaker_${v.ID}" data_id="${v.ID}" data-metrics="edit_speaker">
                                   <i class="fa-solid fa-edit"></i>
                                </a>`;
                         }

                         content += `<a title="View Speaker" target="_blank" style="width:30px;" href="${TAOH_CURR_APP_URL}/speaker/${eventtoken}/${v.ID}"
                              class="svg-opt-con btn" data-cont="${v.ID}" data-eventtoken="${eventtoken}" data-slug="speaker"><i class="fa-solid fa-eye"></i></a>`;

                         const isLikedSpeakers = likedArr && Array.isArray(likedArr.event_speaker) && likedArr.event_speaker.includes(v.ID);
                         content += `<a title="${isLikedSpeakers ? 'Saved' : 'Save'} Speaker" style="width:30px;" href="javascript:void(0)" class="svg-opt-con btn ${isLikedSpeakers ? 'already-saved' : 'speaker_like speaker_save'}" data-cont="${v.ID}" data-eventtoken="${eventtoken}" data-slug="speaker">
                                        <i class="fa ${isLikedSpeakers ? 'fa-bookmark' : 'fa-bookmark-o'}" aria-hidden="true"></i>
                                    </a>`;


                         content += `<a title="View More" style="width:30px;" id="dropdown-agenda_${i}" data-target="#agenda-content_${i}" class="svg-opt-con btn dropdown-agenda">
                                    <svg style="min-width: fit-content;" width="14" height="10" viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.29399 9.17178C6.68448 9.60983 7.31864 9.60983 7.70914 9.17178L13.7071 2.44337C14.0976 2.00532 14.0976 1.29393 13.7071 0.85588C13.3166 0.417832 12.6825 0.417832 12.292 0.85588L7 6.7923L1.70802 0.859384C1.31753 0.421336 0.683365 0.421336 0.292871 0.859384C-0.0976236 1.29743 -0.0976236 2.00882 0.292871 2.44687L6.29086 9.17529L6.29399 9.17178Z" fill="black"/>
                                    </svg>
                                </a>`;

                         content += ` ${is_organizer == 1 ? `
                            <a title="Delete Speaker" class="svg-opt-con btn p-0 delete-speaker metrics_action" id="delte_spk_${v.ID}" data-id="${v.ID}" data-type="speaker" data-metrics="delete_speaker">
                                <svg width="15" height="15" viewBox="0 0 42 37" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.25 0C2.3543 0 0 2.3543 0 5.25V31.5C0 34.3957 2.3543 36.75 5.25 36.75H36.75C39.6457 36.75 42 34.3957 42 31.5V5.25C42 2.3543 39.6457 0 36.75 0H5.25ZM14.3555 11.7305C15.1266 10.9594 16.3734 10.9594 17.1363 11.7305L20.9918 15.5859L24.8473 11.7305C25.6184 10.9594 26.8652 10.9594 27.6281 11.7305C28.391 12.5016 28.3992 13.7484 27.6281 14.5113L23.7727 18.3668L27.6281 22.2223C28.3992 22.9934 28.3992 24.2402 27.6281 25.0031C26.857 25.766 25.6102 25.7742 24.8473 25.0031L20.9918 21.1477L17.1363 25.0031C16.3652 25.7742 15.1184 25.7742 14.3555 25.0031C13.5926 24.232 13.5844 22.9852 14.3555 22.2223L18.2109 18.3668L14.3555 14.5113C13.5844 13.7402 13.5844 12.4934 14.3555 11.7305Z" fill="#FF0000"></path>
                                </svg>
                            </a>` : ''}`;

                         content += `</div>
                            </div>

                            <div class="my-2 d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between" style="gap: 12px;">
                                <div>
                                    <a title="View Speaker" target="_blank" href="${TAOH_CURR_APP_URL}/speaker/${eventtoken}/${v.ID}" data-cont="${v.ID}" data-eventtoken="${eventtoken}" data-slug="speaker">
                                        <h6 class="title line-clamp-1 mb-2" style="max-width: 752px;">${taoh_desc_decode(v.spk_title)}</h6>
                                    </a>
                                    <p id="agenda-content_${i}" class="desc-text mb-2" style="display:none">${$.trim(v.spk_desc) != '' ? taoh_desc_decode(v.spk_desc) : ''}</p>
                                    <div class="d-flex flex-wrap" style="gap: 12px;">`;

                         if (Array.isArray(v.spk_name)) {
                             v.spk_name.map(function (item, r) {
                                 let profileimg = v.spk_profileimg[r];
                                 content += `<div class="d-flex align-items-center" style="gap: 6px;">
                                    <img class="p-img" src="${profileimg}" alt="">
                                    <p class="mb-1 name-role">${item}, <span>${v.spk_desig[r]}, ${v.spk_company[r]}</span></p>
                                </div>`;
                             });
                         }
                         content += `</div></div>`;

                         if (!is_event_suspended && !is_event_freeze) {
                             if (isLoggedIn && event_live_status == 'live' && rsvp_sponsor_title != '' && rsvp_sponsor_title != undefined && isJoinEnabled(v)) { // speaker date time conditions
                                 disableJoinBtn = '';
                             }

                             if (disableJoinBtn == '' && v.spk_state != "live" && v.spk_state != "active") {
                                 disableJoinBtn = 'disabled';
                             }
                         } else {
                             disableJoinBtn = 'disabled';
                         }

                         content += `<div style="margin-top: 5px;">`;
                         if (v.enable_tao_networking == 0 && v.spk_external_video_room_link != '') {
                             content += `<a class="btn ${disableJoinBtn == 'disabled' ? 'bor-btn' : 'btn-success'}
                             joinus-btn join_video_link ${disableJoinBtn}" href="${v.spk_external_video_room_link}"
                             target="_blank">${disableJoinBtn == 'disabled' ? 'Not Live' :
                             `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80" style="width:36px">
                                    <!-- Center circle -->
                                    <circle cx="60" cy="40" r="14" fill="#000" />
                                    <path d="M78 28 C84 35, 84 45, 78 52"  fill="none"  stroke="#000" stroke-width="6" stroke-linecap="round" />
                                    <path d="M88 14 C102 30, 102 50, 88 66"  fill="none"  stroke="#000" stroke-width="6" stroke-linecap="round" />
                                    <path d="M42 28 C36 35, 36 45, 42 52" fill="none"  stroke="#000" stroke-width="6" stroke-linecap="round" />
                                     <path d="M32 14 C18 30, 18 50, 32 66" fill="none"  stroke="#000" stroke-width="6" stroke-linecap="round" />

                                </svg> <span class="color:#000000">Live</span>`
                             }</a>`;
                         } else {
                             content += `<a class="btn
                             ${disableJoinBtn == 'disabled' ? 'bor-btn' : 'btn-success'} joinus-btn join_networking ${disableJoinBtn}"
                                href="<?php echo TAOH_SITE_URL_ROOT . '/' . TAOH_CURR_APP_SLUG; ?>/club/${taoh_desc_decode(event_output.conttoken.title)}-${eventtoken}?session_id=${v.ID}&session_name=${encodeURIComponent(v.spk_title)}" target="_blank">
                                ${disableJoinBtn == 'disabled' ? 'Not Live' :
                                `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80" style="width:36px">
                                    <!-- Center circle -->
                                    <circle cx="60" cy="40" r="14" fill="#000" />
                                    <path d="M78 28 C84 35, 84 45, 78 52"  fill="none"  stroke="#000" stroke-width="6" stroke-linecap="round" />
                                    <path d="M88 14 C102 30, 102 50, 88 66"  fill="none"  stroke="#000" stroke-width="6" stroke-linecap="round" />
                                    <path d="M42 28 C36 35, 36 45, 42 52" fill="none"  stroke="#000" stroke-width="6" stroke-linecap="round" />
                                     <path d="M32 14 C18 30, 18 50, 32 66" fill="none"  stroke="#000" stroke-width="6" stroke-linecap="round" />

                                </svg> <span class="color:#000000">Live</span>`}

                            </a>`;
                         }

                         if ($.trim(v.spk_room_location) != '') {
                             content += ` <div class="text-nowrap">
                                <svg style="min-width: fit-content;" width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14.8079 3.56198C15.2836 3.56198 15.7399 3.37434 16.0762 3.04034C16.4126 2.70634 16.6016 2.25334 16.6016 1.78099C16.6016 1.30864 16.4126 0.85564 16.0762 0.52164C15.7399 0.187639 15.2836 0 14.8079 0C14.3322 0 13.876 0.187639 13.5396 0.52164C13.2033 0.85564 13.0143 1.30864 13.0143 1.78099C13.0143 2.25334 13.2033 2.70634 13.5396 3.04034C13.876 3.37434 14.3322 3.56198 14.8079 3.56198ZM11.6579 7.43934C11.6952 7.4245 11.7289 7.40966 11.7662 7.39482L11.1347 9.75091C10.9255 10.5338 11.131 11.3686 11.684 11.966L14.3259 14.8267L15.148 18.0956C15.3087 18.7301 15.9589 19.1197 16.5978 18.9601C17.2368 18.8006 17.6292 18.155 17.4685 17.5205L16.6091 14.1032C16.5381 13.8138 16.3923 13.5504 16.1905 13.3314L14.3408 11.3278L15.062 8.89752L15.4208 9.75091C15.5852 10.1442 15.8879 10.467 16.2727 10.66L17.2705 11.1534C17.8609 11.4466 18.5783 11.2091 18.8735 10.6229C19.1687 10.0366 18.9296 9.32422 18.3392 9.0311L17.5358 8.63409L16.964 7.26866C16.3213 5.74369 14.8192 4.7493 13.1526 4.7493C12.3006 4.7493 11.4598 4.9274 10.6826 5.26876L10.3836 5.39862C9.15423 5.94034 8.21257 6.97183 7.79032 8.23707L7.69316 8.52649C7.48391 9.14983 7.82395 9.82141 8.44799 10.0292C9.07202 10.237 9.75211 9.89933 9.96137 9.2797L10.0585 8.99028C10.2715 8.35581 10.7424 7.84377 11.3552 7.57291L11.6541 7.44305L11.6579 7.43934ZM10.5368 12.4521L9.60264 14.7674L7.38301 16.9713C6.91592 17.4351 6.91592 18.1884 7.38301 18.6522C7.85011 19.1159 8.60867 19.1159 9.07576 18.6522L11.3813 16.3628C11.5532 16.1922 11.6878 15.9881 11.7774 15.7655L12.3193 14.4223L10.7984 12.7749C10.705 12.6747 10.619 12.5671 10.5368 12.4558V12.4521ZM8.23126 10.1702C7.94353 10.0069 7.58106 10.1034 7.41291 10.3891L6.21715 12.4447L4.18435 11.2796C3.61263 10.9531 2.88023 11.146 2.55139 11.7137L0.159872 15.8285C-0.168962 16.3962 0.025349 17.1235 0.597072 17.45L2.66723 18.6373C3.23896 18.9638 3.97136 18.7709 4.30019 18.2032L6.69171 14.0884C6.74777 13.9919 6.78887 13.8954 6.81503 13.7915L8.45172 10.9828C8.61614 10.6971 8.51899 10.3372 8.23126 10.1702Z" fill="#2557A7"/>
                                </svg>
                                ${v.spk_room_location}</div> `;
                         }
                         content += `</div>`;
                         content += `</div>`;
                         content += `</div></div>`;
                     });
                     is_content = 1;
                 }

                 if ((spon_list != undefined && spon_list.length > 0) || (exh_list != undefined && exh_list.length > 0)) {
                     // content += `
                     exhtitlecontent = `
                        <div class="divider-container d-flex align-items-center mt-1 mb-2" style="gap: 6px;">
                            <div class="left-line"></div>
                            <p class="divider-text text-center" style="width:280px;">Exhibitors and Sponsors</p>
                            <div class="right-line" style="width:60%"></div>
                        </div>`;
                 }

                 if (spon_list != undefined && spon_list.length > 0) {
                     for (let k = 0; k < spon_list.length; k++) {
                         const v = spon_list[k];
                         if (country_locked != 1) {
                             const userInfo = await ft_getUserInfo(v.ptoken, 'public');
                             if (userInfo.full_location != '' && userInfo.full_location != undefined && userInfo.full_location != null) {
                                 var exh_country_array = userInfo.full_location.split(',');
                                 var exh_country_name = exh_country_array[exh_country_array.length - 1].trim();
                                 if (exh_country_name != user_country_name) {
                                     continue;
                                 }
                             }
                         }
                         if (v.sponsor_type != undefined && v.sponsor_type != null && !removeSponsor.includes(v.ID)) {
                             if(v.ptoken == my_ptoken) {
                                 incomplete_sponsor_id = v.ID;
                             }

                             is_content = 1;
                             exh_content = 1;
                             if (exh_content == 0) {
                                 content += exhtitlecontent;
                             }
                             var spons_desc = '';
                             if ($.trim(v.description) != '') {
                                 spons_desc = taoh_desc_decode(v.description);
                             }

                             content += `
                                <div class="new-exh-list mb-3  ${v.ptoken == my_ptoken ? 'enable_btn sponsor-highlight' : 'disable_btn'}">
                                    <!-- gray_bg -->
                                    <!-- <div class="gradient-bg-border"></div> -->

                                    <div class="p-3 px-lg-5 d-flex" style="gap: 12px; flex: 1;">
                                        <div class="d-flex flex-column flex-md-row align-items-md-center" style="gap: 16px; flex: 1;">

                                            <div class="g-overlay-con">
                                                <div class="n-hall-list-bg d-md-none" style="background-image: url(${v.image})"></div>
                                                <!--<div class="glass-overlay d-md-none"></div>-->
                                                <img class="n-hall-list-pic" id="exhi_logo_${v.ID}" src="${v.image}" alt="">
                                            </div>


                                            <div style="flex: 1;">
                                                <div class="d-flex align-items-center justify-content-between" style="gap: 12px;">
                                                    <div class="d-flex flex-wrap" style="gap: 3px;">
                                                        <p class="n-info-badge mr-2" style="background-color:#f9a386;color:#2557A7">${v.sponsor_type || ''}</p>
                                                    </div>

                                                   <!-- dropdown -->
                                                    <!-- <a href="#" class="svg-opt-con btn">
                                                        <svg style="min-width: fit-content;" width="14" height="10" viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M6.29399 9.17178C6.68448 9.60983 7.31864 9.60983 7.70914 9.17178L13.7071 2.44337C14.0976 2.00532 14.0976 1.29393 13.7071 0.85588C13.3166 0.417832 12.6825 0.417832 12.292 0.85588L7 6.7923L1.70802 0.859384C1.31753 0.421336 0.683365 0.421336 0.292871 0.859384C-0.0976236 1.29743 -0.0976236 2.00882 0.292871 2.44687L6.29086 9.17529L6.29399 9.17178Z" fill="black"></path>
                                                        </svg>
                                                    </a> -->
                                                </div>

                                                <div class="d-flex align-items-center justify-content-between flex-wrap" style="flex: 1; gap: 12px;">
                                                    <div class="d-flex align-items-center justify-content-between flex-wrap my-2" style="flex: 1; gap: 12px;">
                                                        <div class="d-flex flex-column" style="gap:3px;">
                                                        <div class="d-flex align-items-center mb-1" style="gap: 10px;">
                                                            <h6 class="n-exh-name mr-2" id="exhi_title_${v.ID}">${taoh_desc_decode(v.title)}</h6>
                                                            <p class="exhi-description" id="exhi_description_${v.ID}" style="display:none">${spons_desc}</p>
                                                            <p class="exhi-sponsor-type" page="events_agenda" id="exhi_sponsor_type_${v.ID}" style="display:none">${v.sponsor_type}</p>
                                                             <p class="exhi-sponsor-owner" id="exhi_owner_${v.ID}" style="display:none">${v.ptoken}</p>
                                                            <div class="n-spon-badge-con" style="display:none;">
                                                                <svg width="13" height="17" viewBox="0 0 13 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M5.88366 0.181775C6.25594 -0.0605917 6.74328 -0.0605917 7.11555 0.181775L7.71795 0.570226C7.92101 0.699709 8.15791 0.762791 8.3982 0.749511L9.11905 0.706349C9.56578 0.679789 9.98543 0.918835 10.1851 1.31061L10.51 1.94474C10.6183 2.15723 10.7943 2.32655 11.0075 2.4328L11.6607 2.75485C12.06 2.95073 12.3037 3.36242 12.2766 3.80067L12.2326 4.50785C12.2191 4.74358 12.2834 4.97931 12.4154 5.17519L12.8147 5.76617C13.0618 6.13138 13.0618 6.60947 12.8147 6.97468L12.4154 7.56898C12.2834 7.76818 12.2191 8.00059 12.2326 8.23632L12.2766 8.9435C12.3037 9.38175 12.06 9.79344 11.6607 9.98933L11.0143 10.3081C10.7977 10.4143 10.6251 10.5869 10.5168 10.7961L10.1885 11.4369C9.98882 11.8287 9.56917 12.0677 9.12244 12.0411L8.40158 11.998C8.1613 11.9847 7.92101 12.0478 7.72134 12.1773L7.11893 12.569C6.74666 12.8114 6.25932 12.8114 5.88705 12.569L5.28126 12.1773C5.0782 12.0478 4.8413 11.9847 4.60101 11.998L3.88016 12.0411C3.43343 12.0677 3.01378 11.8287 2.8141 11.4369L2.48921 10.8027C2.38091 10.5903 2.20493 10.4209 1.99172 10.3147L1.33855 9.99265C0.939201 9.79676 0.695532 9.38507 0.722606 8.94682L0.766602 8.23964C0.780139 8.00391 0.715838 7.76818 0.58385 7.5723L0.187887 6.978C-0.0591669 6.61279 -0.0591669 6.1347 0.187887 5.76949L0.58385 5.17851C0.715838 4.97931 0.780139 4.7469 0.766602 4.51117L0.722606 3.80399C0.695532 3.36574 0.939201 2.95405 1.33855 2.75817L1.98495 2.43944C2.20155 2.32987 2.37753 2.15723 2.48583 1.94474L2.81072 1.31061C3.01039 0.918835 3.43005 0.679789 3.87677 0.706349L4.59763 0.749511C4.83791 0.762791 5.0782 0.699709 5.27787 0.570226L5.88366 0.181775ZM9.20705 6.37375C9.20705 5.66931 8.9218 4.99373 8.41405 4.49562C7.90631 3.99751 7.21766 3.71767 6.49961 3.71767C5.78155 3.71767 5.0929 3.99751 4.58516 4.49562C4.07741 4.99373 3.79217 5.66931 3.79217 6.37375C3.79217 7.07818 4.07741 7.75376 4.58516 8.25187C5.0929 8.74998 5.78155 9.02982 6.49961 9.02982C7.21766 9.02982 7.90631 8.74998 8.41405 8.25187C8.9218 7.75376 9.20705 7.07818 9.20705 6.37375ZM0.0457464 14.6673L1.50438 11.2642C1.51115 11.2676 1.51453 11.2709 1.51792 11.2775L1.84281 11.9117C2.23877 12.6819 3.06116 13.1501 3.94108 13.1003L4.66193 13.0571C4.6687 13.0571 4.67885 13.0571 4.68562 13.0637L5.28803 13.4555C5.46063 13.5651 5.64338 13.6514 5.8329 13.7111L4.5604 16.676C4.48256 16.8586 4.30996 16.9814 4.11029 16.998C3.91062 17.0146 3.71771 16.925 3.60941 16.759L2.51967 15.1222L0.621077 15.3978C0.428172 15.4243 0.235267 15.348 0.113432 15.1985C-0.0084024 15.0491 -0.0320925 14.8433 0.0423621 14.6673H0.0457464ZM8.43881 16.6727L7.16631 13.7111C7.35583 13.6514 7.53859 13.5684 7.71118 13.4555L8.31359 13.0637C8.32036 13.0604 8.32713 13.0571 8.33728 13.0571L9.05814 13.1003C9.93805 13.1501 10.7604 12.6819 11.1564 11.9117L11.4813 11.2775C11.4847 11.2709 11.4881 11.2676 11.4948 11.2642L12.9568 14.6673C13.0313 14.8433 13.0042 15.0458 12.8858 15.1985C12.7673 15.3513 12.571 15.4276 12.3781 15.3978L10.4795 15.1222L9.3898 16.7557C9.2815 16.9217 9.0886 17.0113 8.88892 16.9947C8.68925 16.9781 8.51665 16.852 8.43881 16.6727Z" fill="#00F6FF"/>
                                                                    <defs>
                                                                    <linearGradient id="paint0_linear_7222_852" x1="6.5" y1="0" x2="6.5" y2="17" gradientUnits="userSpaceOnUse">
                                                                    <stop stop-color="#00F6FF"/>
                                                                    <stop offset="1" stop-color="#2557A7"/>
                                                                    </linearGradient>
                                                                    </defs>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                            `;

                             content += `</div>
                                    <div class="d-flex align-items-center" style="gap: 12px;">
                                         ${((v.ptoken == my_ptoken && opt == 'chat') || is_organizer == 1) ? `
                                            <a title="configure Sponsor to Exhibitor" style="min-width: unset;" class="svg-opt-con btn  edit-exhibitor metrics_action"
                                            id="edit_exh_${v.ID}" data-id="${v.ID}" data-type="sponsor" data-metrics="edit_exhibitor">
                                            <i class="fa fa-cog" aria-hidden="true"></i>
                                            </a>
                                        ` : ''}
                                        <a title="View Sponsor" target="_blank"  data-metrics="view_exhibitor" href="${_taoh_site_url_root}/events/sponsor/${v.ID}/${eventtoken}"
                                        class="svg-opt-con btn metrics_action">
                                        <i class="fa-solid fa-eye"></i>
                                        </a>
                                         ${is_organizer == 1 ? `
                                            <a title="Delete Sponsor" class="svg-opt-con btn p-0 delete-exhibitor metrics_action" id="delte_exh_${v.ID}" data-id="${v.ID}" data-type="sponsor" data-metrics="delete_sponsor">
                                                <svg width="15" height="15" viewBox="0 0 42 37" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M5.25 0C2.3543 0 0 2.3543 0 5.25V31.5C0 34.3957 2.3543 36.75 5.25 36.75H36.75C39.6457 36.75 42 34.3957 42 31.5V5.25C42 2.3543 39.6457 0 36.75 0H5.25ZM14.3555 11.7305C15.1266 10.9594 16.3734 10.9594 17.1363 11.7305L20.9918 15.5859L24.8473 11.7305C25.6184 10.9594 26.8652 10.9594 27.6281 11.7305C28.391 12.5016 28.3992 13.7484 27.6281 14.5113L23.7727 18.3668L27.6281 22.2223C28.3992 22.9934 28.3992 24.2402 27.6281 25.0031C26.857 25.766 25.6102 25.7742 24.8473 25.0031L20.9918 21.1477L17.1363 25.0031C16.3652 25.7742 15.1184 25.7742 14.3555 25.0031C13.5926 24.232 13.5844 22.9852 14.3555 22.2223L18.2109 18.3668L14.3555 14.5113C13.5844 13.7402 13.5844 12.4934 14.3555 11.7305Z" fill="#FF0000"></path>
                                                </svg>
                                            </a>` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    </div>`;
                         }
                     }
                 }

                 if (sortedExhList != undefined && sortedExhList.length > 0) {
                     for (let i = 0; i < sortedExhList.length; i++) {
                         const v = sortedExhList[i];
                         if (country_locked != 1) {
                             const userInfo = await ft_getUserInfo(v.ptoken, 'public');
                             if (userInfo.full_location != '' && userInfo.full_location != undefined && userInfo.full_location != null) {
                                 var exh_country_array = userInfo.full_location.split(',');
                                 var exh_country_name = exh_country_array[exh_country_array.length - 1].trim();
                                 if (exh_country_name != user_country_name) {
                                     continue;
                                 }
                             }
                         }

                         var disablecls = '';
                         if (v.exh_state != 'active' && v.exh_state != 'live') {
                             disablecls = 'disabled';
                         }

                         if (exh_content == 0) {
                             content += exhtitlecontent;
                         }
                         let exhibitorWebsiteUrl = v.exh_hero_button_url || '';
                         var sponsArr = null;
                         if (v.sponsor_id && sponsorsBecomeExhibitor?.length > 0) {
                             sponsArr = sponsorsBecomeExhibitor.find(item => item.ID === v.sponsor_id);
                         }

                         var sponsor_badge_name = '';
                         if (v.sponsor_type != '' && v.sponsor_type != null && v.sponsor_type != undefined) {
                             sponsor_badge_name = v.sponsor_type;
                         } else if (sponsArr && sponsArr.sponsor_type != '' && sponsArr.sponsor_type != null && sponsArr.sponsor_type != undefined) {
                             sponsor_badge_name = sponsArr.sponsor_type;
                         }

                         content += `
                    <div class="new-exh-list mb-3  ${v.ptoken == my_ptoken ? 'sponsor-highlight' : ''}">
                         <!-- <div class="gradient-bg-border"></div> -->

                        <div class="p-3 px-lg-5 d-flex" style="gap: 12px;flex: 1;">
                            <div class="d-flex flex-column flex-md-row" style="gap: 16px;flex: 1;">
                                <a title="View Exhibitor" target="_blank" data-metrics="view_exhibitor" href="${_taoh_site_url_root}/events/exhibitors/${v.ID}/${eventtoken}" class="metrics_action">
                                    <div class="g-overlay-con">
                                        <div class="n-hall-list-bg d-md-none" style="background-image: url(${v.exh_logo})"></div>
                                        <!--<div class="glass-overlay"></div>-->
                                        <img class="n-hall-list-pic" src="${v.exh_logo}" alt="">
                                    </div>
                                </a>

                                <div style="flex: 1;">
                                    <div class="d-flex align-items-center justify-content-between" style="gap: 12px;">
                                        <div class="d-flex flex-wrap" style="gap: 3px;">
                                            <p onclick="LoadMetaWithHall('','${v.exh_hall}','${v.exh_hall}','agenda');"
                                            class="n-info-badge mr-2" style="background-color:#F9A386;color:#3563ae">${v.exh_hall
                                             ? Array.isArray(v.exh_hall)
                                                 ? v.exh_hall.map(taoh_desc_decode_new).join(', ')
                                                 : taoh_desc_decode_new(v.exh_hall)
                                             : ''}</p>
                                            ${sponsor_badge_name ? `<p class="n-info-badge mr-2" style="background-color:#BEEE95;;color:#3563ae">${sponsor_badge_name}</p>` : ''}
                                        </div>
                                        <div class="d-flex align-items-center" style="gap: 12px;">
                                         `;

                         content += ((v.ptoken == my_ptoken && opt == 'chat') || is_organizer == 1) ? `
                            <a title="Edit Exhibitor" class="svg-opt-con btn p-0 edit-exhibitor metrics_action" id="edit_exh_${v.ID}" data-id="${v.ID}" data-type="exhibitor" data-metrics="edit_exhibitor">
                                <i class="fa-solid fa-edit"></i>
                            </a>  ` : '<span style="width:30px;"></span>';

                            content += `  <a title="View Exhibitor" target="_blank" data-metrics="view_exhibitor" href="${_taoh_site_url_root}/events/exhibitors/${v.ID}/${eventtoken}"
                                            class="svg-opt-con btn metrics_action">
                                            <i class="fa-solid fa-eye"></i></a>`;


                         if (likedArr && Array.isArray(likedArr.event_exhibitor) && likedArr.event_exhibitor.includes(v.ID)) {
                             content += `  <a title="Save Exhibitor" style="width:30px;" href="javascript:void(0)" class="svg-opt-con btn already-saved" data-cont="${v.ID}" data-eventtoken="${eventtoken}" data-slug="exhibitor" >
                                    <svg style="min-width: fit-content;" width="12" height="18" viewBox="0 0 12 18" fill="black" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5.53682 13.4304L0.767828 17.175C0.758399 17.1566 0.75 17.1296 0.75 17.0942V1.68243C0.75 1.08455 1.16471 0.75 1.5 0.75H10.5C10.8353 0.75 11.25 1.08455 11.25 1.68243V17.0942C11.25 17.1296 11.2416 17.1565 11.2322 17.175L6.46318 13.4304L6 13.0667L5.53682 13.4304Z" stroke="black" stroke-width="1.5"/>
                                    </svg>
                                </a>`;
                         } else {
                             content += `  <a title="Save Exhibitor" style="width:30px;" href="javascript:void(0)" class="svg-opt-con btn exhibitor_like exhibitor_save" data-cont="${v.ID}" data-eventtoken="${eventtoken}" data-slug="exhibitor" >
                                    <svg style="min-width: fit-content;" width="12" height="18" viewBox="0 0 12 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5.53682 13.4304L0.767828 17.175C0.758399 17.1566 0.75 17.1296 0.75 17.0942V1.68243C0.75 1.08455 1.16471 0.75 1.5 0.75H10.5C10.8353 0.75 11.25 1.08455 11.25 1.68243V17.0942C11.25 17.1296 11.2416 17.1565 11.2322 17.175L6.46318 13.4304L6 13.0667L5.53682 13.4304Z" stroke="black" stroke-width="1.5"/>
                                    </svg>
                                </a>`;
                         }

                        content += ` ${is_organizer == 1 ? `
                            <a title="Delete Exhibitor" class="svg-opt-con btn p-0 delete-exhibitor metrics_action" id="delte_exh_${v.ID}" data-id="${v.ID}" data-type="exhibitor" data-metrics="delete_exhibitor">
                                <svg width="15" height="15" viewBox="0 0 42 37" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.25 0C2.3543 0 0 2.3543 0 5.25V31.5C0 34.3957 2.3543 36.75 5.25 36.75H36.75C39.6457 36.75 42 34.3957 42 31.5V5.25C42 2.3543 39.6457 0 36.75 0H5.25ZM14.3555 11.7305C15.1266 10.9594 16.3734 10.9594 17.1363 11.7305L20.9918 15.5859L24.8473 11.7305C25.6184 10.9594 26.8652 10.9594 27.6281 11.7305C28.391 12.5016 28.3992 13.7484 27.6281 14.5113L23.7727 18.3668L27.6281 22.2223C28.3992 22.9934 28.3992 24.2402 27.6281 25.0031C26.857 25.766 25.6102 25.7742 24.8473 25.0031L20.9918 21.1477L17.1363 25.0031C16.3652 25.7742 15.1184 25.7742 14.3555 25.0031C13.5926 24.232 13.5844 22.9852 14.3555 22.2223L18.2109 18.3668L14.3555 14.5113C13.5844 13.7402 13.5844 12.4934 14.3555 11.7305Z" fill="#FF0000"></path>
                                </svg>
                            </a>` : ''}`;

                         content += `</div>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between flex-wrap my-2" style="flex: 1; gap: 12px;">
                                        <div class="d-flex flex-column" style="gap:3px;">
                                            <div class="d-flex align-items-center mb-2" style="gap: 10px;">
                                                <a title="View Exhibitor" target="_blank" data-metrics="view_exhibitor" href="${_taoh_site_url_root}/events/exhibitors/${v.ID}/${eventtoken}" class="metrics_action">
                                                    <h6 class="n-exh-name mr-2">${taoh_desc_decode_new(v.exh_session_title)}</h6>
                                                </a>`;

                         if (v.exh_raffles == '1') {
                             raffle_start = new Date(v.exh_raffle_start_time);
                             raffle_end = new Date(v.exh_raffle_stop_time);
                             if (v.exh_raffles_timebound_option == 0 || (v.exh_raffles_timebound_option == 1 && new Date() >= raffle_start && new Date() <= raffle_end)) { // raffle date conditions
                                 content += ` <div title="${taoh_desc_decode_new(v.exh_session_title)}" class="d-flex align-items-center">
                                                    <svg style="min-width: fit-content;" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M7.44141 2.6875L8.80078 5H8.75H5.9375C5.07422 5 4.375 4.30078 4.375 3.4375C4.375 2.57422 5.07422 1.875 5.9375 1.875H6.02344C6.60547 1.875 7.14844 2.18359 7.44141 2.6875ZM2.5 3.4375C2.5 4 2.63672 4.53125 2.875 5H1.25C0.558594 5 0 5.55859 0 6.25V8.75C0 9.44141 0.558594 10 1.25 10H18.75C19.4414 10 20 9.44141 20 8.75V6.25C20 5.55859 19.4414 5 18.75 5H17.125C17.3633 4.53125 17.5 4 17.5 3.4375C17.5 1.53906 15.9609 0 14.0625 0H13.9766C12.7305 0 11.5742 0.660156 10.9414 1.73438L10 3.33984L9.05859 1.73828C8.42578 0.660156 7.26953 0 6.02344 0H5.9375C4.03906 0 2.5 1.53906 2.5 3.4375ZM15.625 3.4375C15.625 4.30078 14.9258 5 14.0625 5H11.25H11.1992L12.5586 2.6875C12.8555 2.18359 13.3945 1.875 13.9766 1.875H14.0625C14.9258 1.875 15.625 2.57422 15.625 3.4375ZM1.25 11.25V18.125C1.25 19.1602 2.08984 20 3.125 20H8.75V11.25H1.25ZM11.25 20H16.875C17.9102 20 18.75 19.1602 18.75 18.125V11.25H11.25V20Z" fill="#FFC107"/>
                                                        <defs> <linearGradient id="paint0_linear_7222_848" x1="10" y1="0" x2="10" y2="20" gradientUnits="userSpaceOnUse"> <stop stop-color="#FFC107"/> <stop offset="1" stop-color="#FF5C00"/> </linearGradient> </defs>
                                                    </svg>
                                                </div>`;
                             }
                         }

                         content += ` </div>
                                        </div>

                                        <div class="d-flex align-items-center" style="gap: 6px;">`;
                         content += ` </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>`;


                         // });
                         // $('#exhibitors_list').html(content);
                         is_content = 1;
                         exh_content = 1;
                     }
                 }

                 var event_status = $('#event_status_hidden').val();

                 if (!is_content) {
                     $('#agenda_list').html('<div class="text-center"></div>');
                     $(".agenda_block").addClass("align-self-start lblur");

                     if (event_status == 1 || event_status == 0) {

                         $('#speaker_desc').remove();
                         $('#speaker_top').remove();
                         $('#exhibitor_desc').remove();
                         $('#exhibitor_top').remove();

                         $('#agenda_desc').remove();
                         $('#agenda_top').remove();
                         $("#desc-tab").addClass('active');
                         $("#agenda_desc").removeClass('show active');
                         $("#agenda_desc").addClass('fade');
                         $("#agenda-tab").removeClass('active');
                         $('#dashboard_desc').remove();

                     } else
                         $("#agenda_default_list,#agenda_default_banner,#my_agenda_default_banner").show();
                 } else {
                     $('#agenda_top').show();
                     $(".agenda_block").removeClass("align-self-start lblur");

                     var activeTabText = $('.hall_tabs .nav-link.active').first().text().trim();

                     if (activeTabText == 'Agenda') {
                         $("#agenda_desc").addClass('show active');
                         $("#agenda-tab").show();
                     } else {
                         $("#agenda_desc").removeClass('show active');
                         $("#agenda_desc").addClass('fade');
                     }
                     $('#agenda_list').html(content);

                     if (search != '' || (agenda_list != undefined && agenda_list.length > 0) || (spon_list != undefined && spon_list.length > 0) || (exh_list != undefined && exh_list.length > 0)) {
                         $("#dropdownMenuLink_agenda").show();
                     } else {
                         $(".agenda_hall_list, #dropdownMenuLink_agenda").hide();
                     }
                     loader(false, $("#agenda_loaderArea"));
                 }

                 if(incomplete_sponsor_id && !incomplete_sponsor_form_show) {
                     $(`#edit_exh_${incomplete_sponsor_id}`).trigger('click');
                     incomplete_sponsor_form_show = true;
                 }
             });
    }

    function isValidUrl(str) {
        var pattern = new RegExp(
            '^(https?:\\/\\/)' +              // protocol
            '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
            '((\\d{1,3}\\.){3}\\d{1,3}))' +    // OR IP (v4) address
            '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
            '(\\?[;&a-z\\d%_.~+=-]*)?' +       // query string
            '(\\#[-a-z\\d_]*)?$','i'           // fragment locator
        );
        return !!pattern.test(str);
    }

    $(document).on('click', '.show-more', function () {
        var key = $(this).attr('data-id');
        $('#more-content-'+key).hide();
        $('#less-content-'+key).show();

    });
    $(document).on('click', '.show-less', function () {
        var key = $(this).attr('data-id');
        $('#more-content-'+key).show();
        $('#less-content-'+key).hide();
    });

    $(document).on("click", ".speaker_save,.exhibitor_save", function (event) {
        event.stopPropagation(); // Stop the event from propagating to the parent

        var savetoken = $(this).attr('data-cont');
        var eventtoken = $(this).attr('data-eventtoken');
        var slug = $(this).attr('data-slug');
        let dataid = `${slug}_id`;
        $('.' + slug + '_like').find(`[data-cont='${savetoken}']`).attr('src', "<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark-fill.svg");
        $('.' + slug + '_like').find(`[data-cont='${savetoken}']`).removeClass(slug + '_save').addClass("already-saved").removeAttr("style");
        $('.' + slug + '_like').find(`[data-cont='${savetoken}']`).parent().removeAttr("style");
        localStorage.setItem(app_slug + '_' + savetoken + '_liked', 1);
        save_metrics(slug, 'like', eventtoken + "-" + savetoken);

        var data = {
            'taoh_action': 'speaker_exhibitor_save_put',
            'slug': slug,
            'eventtoken': eventtoken,
            [dataid]: savetoken,
            'ptoken': '<?php echo $my_ptoken ?? ''; ?>',
        };
        jQuery.post(_taoh_site_ajax_url, data, function (response) {
            var slug_capitalized = slug.charAt(0).toUpperCase() + slug.slice(1);

            if (response.success) {
                getEventSavedInfo({eventtoken}, true).then(() => {
                    taoh_set_success_message(slug_capitalized + ' saved successfully.', false, 'toast-middle', [
                        {
                            text: 'OK',
                            action: () => {
                                window.location.reload();
                            },
                            class: 'dojo-v1-btn float-right mt-3 mb-3'
                        }
                    ]);
                });
            } else {
                taoh_set_error_message(slug_capitalized + ' save failed.');
            }
        }).fail(function () {
            console.log("Network issue!");
        })
    });
</script>