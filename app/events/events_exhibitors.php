<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/raj/assets/icons/icons.php';
require_once('events_exhibitor_form_new.php');
//echo "===ssssssssss";die();
// $click_view = (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) ? 'click' : 'view';
// echo "<pre>"; print_r(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null); echo "</pre>";

$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
$user_info_obj = $taoh_user_is_logged_in ? taoh_user_all_info() : null;
$valid_user = $taoh_user_is_logged_in && in_array($user_info_obj?->profile_complete ?? null, [1, '1'], true);
$ptoken = $taoh_user_is_logged_in ? ($user_info_obj?->ptoken ?? '') : '';
$ref_param =  taoh_parse_url(3);
$ref_slug = taoh_parse_url(4);

$trackingtoken = '';

if($taoh_user_is_logged_in && $ptoken != ''){
    
    $trackingtoken = hash('sha256',(string)$ptoken);
    
}

$social_token = '';
if (isset($ref_param) && $ref_param != '' && $ref_param != 'stlo') {
    
    $hashptoken =  hash('sha256',(string)$ptoken); 
    if ( $ptoken !== '' && $hashptoken === (string)$ref_param) {
        $social_token = $ref_param;
    }
    
}

$success_discount_amt   = (string)($GLOBALS['success_discount_amt']   ?? '');
$success_sponsor_title  = (string)($GLOBALS['success_sponsor_title']  ?? '');
$success_redirect       = (string)($GLOBALS['success_redirect']       ?? '');


$full_location = (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->full_location ?? '';
$user_country_array = explode(',', $full_location);
$user_country_name = trim(end($user_country_array));
// echo 'user_country_name : '.$user_country_name."====".$full_location;
?>
<style>

       .exhibitor_website {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
             text-decoration: underline;
        }

        .exhibitor_website  i {
            color: #2557a7;
            font-size: 14px;
        }

       

</style>
<div class="modal fade" id="sponsorDetailModal" tabindex="-1" role="dialog" aria-labelledby="sponsorDetailModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center" style="gap: 12px;">
                        <div class="n-spon-badge-con">
                            <svg width="13" height="17" viewBox="0 0 13 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.88366 0.181775C6.25594 -0.0605917 6.74328 -0.0605917 7.11555 0.181775L7.71795 0.570226C7.92101 0.699709 8.15791 0.762791 8.3982 0.749511L9.11905 0.706349C9.56578 0.679789 9.98543 0.918835 10.1851 1.31061L10.51 1.94474C10.6183 2.15723 10.7943 2.32655 11.0075 2.4328L11.6607 2.75485C12.06 2.95073 12.3037 3.36242 12.2766 3.80067L12.2326 4.50785C12.2191 4.74358 12.2834 4.97931 12.4154 5.17519L12.8147 5.76617C13.0618 6.13138 13.0618 6.60947 12.8147 6.97468L12.4154 7.56898C12.2834 7.76818 12.2191 8.00059 12.2326 8.23632L12.2766 8.9435C12.3037 9.38175 12.06 9.79344 11.6607 9.98933L11.0143 10.3081C10.7977 10.4143 10.6251 10.5869 10.5168 10.7961L10.1885 11.4369C9.98882 11.8287 9.56917 12.0677 9.12244 12.0411L8.40158 11.998C8.1613 11.9847 7.92101 12.0478 7.72134 12.1773L7.11893 12.569C6.74666 12.8114 6.25932 12.8114 5.88705 12.569L5.28126 12.1773C5.0782 12.0478 4.8413 11.9847 4.60101 11.998L3.88016 12.0411C3.43343 12.0677 3.01378 11.8287 2.8141 11.4369L2.48921 10.8027C2.38091 10.5903 2.20493 10.4209 1.99172 10.3147L1.33855 9.99265C0.939201 9.79676 0.695532 9.38507 0.722606 8.94682L0.766602 8.23964C0.780139 8.00391 0.715838 7.76818 0.58385 7.5723L0.187887 6.978C-0.0591669 6.61279 -0.0591669 6.1347 0.187887 5.76949L0.58385 5.17851C0.715838 4.97931 0.780139 4.7469 0.766602 4.51117L0.722606 3.80399C0.695532 3.36574 0.939201 2.95405 1.33855 2.75817L1.98495 2.43944C2.20155 2.32987 2.37753 2.15723 2.48583 1.94474L2.81072 1.31061C3.01039 0.918835 3.43005 0.679789 3.87677 0.706349L4.59763 0.749511C4.83791 0.762791 5.0782 0.699709 5.27787 0.570226L5.88366 0.181775ZM9.20705 6.37375C9.20705 5.66931 8.9218 4.99373 8.41405 4.49562C7.90631 3.99751 7.21766 3.71767 6.49961 3.71767C5.78155 3.71767 5.0929 3.99751 4.58516 4.49562C4.07741 4.99373 3.79217 5.66931 3.79217 6.37375C3.79217 7.07818 4.07741 7.75376 4.58516 8.25187C5.0929 8.74998 5.78155 9.02982 6.49961 9.02982C7.21766 9.02982 7.90631 8.74998 8.41405 8.25187C8.9218 7.75376 9.20705 7.07818 9.20705 6.37375ZM0.0457464 14.6673L1.50438 11.2642C1.51115 11.2676 1.51453 11.2709 1.51792 11.2775L1.84281 11.9117C2.23877 12.6819 3.06116 13.1501 3.94108 13.1003L4.66193 13.0571C4.6687 13.0571 4.67885 13.0571 4.68562 13.0637L5.28803 13.4555C5.46063 13.5651 5.64338 13.6514 5.8329 13.7111L4.5604 16.676C4.48256 16.8586 4.30996 16.9814 4.11029 16.998C3.91062 17.0146 3.71771 16.925 3.60941 16.759L2.51967 15.1222L0.621077 15.3978C0.428172 15.4243 0.235267 15.348 0.113432 15.1985C-0.0084024 15.0491 -0.0320925 14.8433 0.0423621 14.6673H0.0457464ZM8.43881 16.6727L7.16631 13.7111C7.35583 13.6514 7.53859 13.5684 7.71118 13.4555L8.31359 13.0637C8.32036 13.0604 8.32713 13.0571 8.33728 13.0571L9.05814 13.1003C9.93805 13.1501 10.7604 12.6819 11.1564 11.9117L11.4813 11.2775C11.4847 11.2709 11.4881 11.2676 11.4948 11.2642L12.9568 14.6673C13.0313 14.8433 13.0042 15.0458 12.8858 15.1985C12.7673 15.3513 12.571 15.4276 12.3781 15.3978L10.4795 15.1222L9.3898 16.7557C9.2815 16.9217 9.0886 17.0113 8.88892 16.9947C8.68925 16.9781 8.51665 16.852 8.43881 16.6727Z" fill="#ffffff"></path>
                            </svg>
                        </div>
                        <span id="sponsorDetailModalTitle">Event Ticket</span>
                    </h5>
                    <button type="button" class="btn rounded-circle border" data-dismiss="modal" aria-label="Close">
                        <?= icon('close', '#D3D3D3', 13) ?>
                    </button>
                </div>
                <div class="modal-body p-3 px-lg-5 pb-lg-5 pt-lg-4">
                    <div class="sponsor-detail-content">
                        <div class="sponsor-detail-header d-flex" style="gap: 12px;">
                            <img src="" alt="" id="sponsorDetailLogo" class="sponsor-logo">
                            <h3 class="name" id="sponsorDetailName"></h3>
                        </div>
                        <div class="sponsor-detail-body">
                            <p class="text-xs description my-3" id="sponsorDetailDescription">

                            </p>
                            <a class="visit-link btn" href="#" id="sponsorDetailWebsite" target="_blank">Visit Website</a>
                        </div>
                    </div>  
                </div>

            </div>
        </div>
    </div>
    
<script>
    var my_ptoken = "<?php echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>";
    var eve = "<?php echo $event_token ?? ''; ?>";
    var sponsorPtokenArray = {};
    // let click_view = '<?= $click_view ?? 'view'; ?>';
    var user_country_name = '<?= $user_country_name ?? ''; ?>';

    $(document).on('click', '.open_sponsor_detail', function () {
        var sponsor_id = $(this).attr('data_id');
        var sponsor_name = $(this).attr('data_name');
        
        var spondesc = $('#exhi_description_'+sponsor_id).text();
        var spon_logo = $('#exhi_logo_'+sponsor_id).attr('src');

        var sponlink = $('#exhi_link_'+sponsor_id).attr('href');
        //alert(spondesc)
        $('#sponsorDetailModalTitle').text(sponsor_name);
        $('#sponsorDetailDescription').text(spondesc);
        $('#sponsorDetailLogo').attr('src',spon_logo);
        $('#sponsorDetailWebsite').attr('href', sponlink);

        $('#sponsorDetailModal').modal('show');
     });


    async function getEventExhibitors(eventtoken,response,hallColorArray='',search='',tab_name='') {
        var exh_list = response.output?.event_exhibitor ?? [];
        var spon_list = response.output?.event_sponsor ?? [];
        let sponsorsBecomeExhibitor = response.output?.event_sponsor_deleted ?? [];

        const isEmpty = v =>
            !v || (Array.isArray(v) ? v.length === 0 : Object.keys(v).length === 0);

        if (search == '' && isEmpty(exh_list) && isEmpty(spon_list)) {
            $('#exhibitors-tab').hide();
        }

        let priceMap = {};
        let sortedExhList;

        var is_organizer = $("#is_organizer").val();
        var country_locked = $('#event_country_lock').val();
        var event_country_name = $('#event_country_name').val();

        var eventHallAccess = [];
        var eventHallAccessKey = `event_hall_access_${eventtoken}`;
        const data = await IntaoDB.getItem(objStores.event_store.name, eventHallAccessKey);
        if (data?.values) {
            eventHallAccess = data?.values.output;
        }

        if (spon_list != undefined && spon_list.length > 0) {
            spon_list.sort(function (a, b) {
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
            priceMap = spon_list.reduce(function (acc, sponsor) {
                acc[sponsor.ID] = sponsor.final_price;
                return acc;
            }, {});
        }

        if (exh_list != undefined && exh_list.length > 0) {
            exh_list.forEach(exh => {
                const id = exh.sponsor_id;
                const price = id ? priceMap[id] : 0;
                exh.final_price = Number(price ?? 0);
            });

            sortedExhList = exh_list.sort(function (a, b) {
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
        } else {
            console.warn('exh_list was empty or undefined.');
        }

        $('#exhibitors_list').html('');
        var content = '';
        var removeSponsor = [];
        if(sortedExhList !=undefined && sortedExhList.length > 0){
            $.each(sortedExhList, function (i, v) {
                if(v.sponsor_id !='' && v.sponsor_id != null && v.sponsor_id != undefined){
                       removeSponsor.push(v.sponsor_id);
                   }
            });
        }
        var is_content = 0;
        var raffle_array = [];
        var banner = `<div class="d-flex blue-banner exh-banner" style="gap: 12px;min-height:130px !important">
                        <div class="d-flex flex-column align-items-center justify-content-center" style="gap: 6px;width:15%">
                        <svg width="26" height="21" viewBox="0 0 26 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.95 0C0.873437 0 0 0.881836 0 1.96875V19.0312C0 20.1182 0.873437 21 1.95 21H5.85V17.7188C5.85 16.6318 6.72344 15.75 7.8 15.75C8.87656 15.75 9.75 16.6318 9.75 17.7188V21H13.4022C13.1462 20.5816 13 20.0895 13 19.5604C13 17.6367 14.0481 15.9592 15.6 15.0814V11.148V1.96875C15.6 0.881836 14.7266 0 13.65 0H1.95ZM2.6 9.84375C2.6 9.48281 2.8925 9.1875 3.25 9.1875H4.55C4.9075 9.1875 5.2 9.48281 5.2 9.84375V11.1562C5.2 11.5172 4.9075 11.8125 4.55 11.8125H3.25C2.8925 11.8125 2.6 11.5172 2.6 11.1562V9.84375ZM7.15 9.1875H8.45C8.8075 9.1875 9.1 9.48281 9.1 9.84375V11.1562C9.1 11.5172 8.8075 11.8125 8.45 11.8125H7.15C6.7925 11.8125 6.5 11.5172 6.5 11.1562V9.84375C6.5 9.48281 6.7925 9.1875 7.15 9.1875ZM10.4 9.84375C10.4 9.48281 10.6925 9.1875 11.05 9.1875H12.35C12.7075 9.1875 13 9.48281 13 9.84375V11.1562C13 11.5172 12.7075 11.8125 12.35 11.8125H11.05C10.6925 11.8125 10.4 11.5172 10.4 11.1562V9.84375ZM3.25 3.9375H4.55C4.9075 3.9375 5.2 4.23281 5.2 4.59375V5.90625C5.2 6.26719 4.9075 6.5625 4.55 6.5625H3.25C2.8925 6.5625 2.6 6.26719 2.6 5.90625V4.59375C2.6 4.23281 2.8925 3.9375 3.25 3.9375ZM6.5 4.59375C6.5 4.23281 6.7925 3.9375 7.15 3.9375H8.45C8.8075 3.9375 9.1 4.23281 9.1 4.59375V5.90625C9.1 6.26719 8.8075 6.5625 8.45 6.5625H7.15C6.7925 6.5625 6.5 6.26719 6.5 5.90625V4.59375ZM11.05 3.9375H12.35C12.7075 3.9375 13 4.23281 13 4.59375V5.90625C13 6.26719 12.7075 6.5625 12.35 6.5625H11.05C10.6925 6.5625 10.4 6.26719 10.4 5.90625V4.59375C10.4 4.23281 10.6925 3.9375 11.05 3.9375ZM23.4 11.1562C23.4 10.286 23.0576 9.45141 22.4481 8.83606C21.8386 8.2207 21.012 7.875 20.15 7.875C19.288 7.875 18.4614 8.2207 17.8519 8.83606C17.2424 9.45141 16.9 10.286 16.9 11.1562C16.9 12.0265 17.2424 12.8611 17.8519 13.4764C18.4614 14.0918 19.288 14.4375 20.15 14.4375C21.012 14.4375 21.8386 14.0918 22.4481 13.4764C23.0576 12.8611 23.4 12.0265 23.4 11.1562ZM14.3 19.5686C14.3 20.3602 14.9337 21 15.7178 21H24.5822C25.3662 21 26 20.3602 26 19.5686C26 17.4604 24.3059 15.75 22.2178 15.75H18.0822C15.9941 15.75 14.3 17.4604 14.3 19.5686Z" fill="white"/>
                        </svg>
                        </div>
                        <div class="vertical-line"></div>
                        <div class="text-center" style='width:80%'>
                            <div>We are Settings up things for you</div>
                            <div style="font-size: 20px;">Unlock your Exhibiting Slot</div>
                            <div>
                                <a class="btn speaker-default-btn event_sponsor_right_header" 
                                href="javascript:void(0)" data-toggle="modal" data-target="#sponsorInfo">Become a Sponsor !</a>
                                <button type="button" class="btn speaker-default-btn get-started" data-toggle="modal" data-target="#sponsorInfo">
                                More Info !</button>
                                <a class="btn speaker-default-btn" href="#">Contact us !</a>
                            </div>
                        </div>
                    </div>`;
        if(search == ''){
         //   content += banner;
        }

        var exh_count = 0;
       
        if(sortedExhList !=undefined && sortedExhList.length > 0){
            var sponsor_type = $('#sponsor_type').val();

            // var user_profile_type = $("#user_profile_type").val();
            var rsvp_sponsor_title = $("#rsvp_sponsor_title").val();
            var exhibitor_count = 0;

            content = `<div id="exhibitor_main_title" class="divider-container d-flex align-items-center mt-1 mb-2" style="gap: 6px;">
                            <div class="left-line" style="max-width:40%"></div>
                            <p class="divider-text text-center" style="width:300px;">Exhibitors</p>
                            <div class="right-line" style="width:50%"></div>
                        </div>`;


            // $.each( sortedExhList, async function (i, v) {
            for (let i = 0; i < sortedExhList.length; i++) {
                const v = sortedExhList[i];
                if (country_locked != 1) {
                    const userInfo = await ft_getUserInfo(v.ptoken, 'public');
                    if (userInfo.full_location != '' && userInfo.full_location != undefined && userInfo.full_location != null) {
                        var exh_country_array = userInfo.full_location.split(',');
                        var exh_country_name = exh_country_array[exh_country_array.length - 1].trim();
                    
                        if(exh_country_name != user_country_name){
                            continue;
                        }
                    }
                }
                if(v.exh_raffles == '1'){
                    var raffle_data = {
                        exh_raffle_status : v.exh_raffle_status,
                        exh_raffle_title : v.exh_raffle_title,
                        exh_raffle_description : v.exh_raffle_description,
                        exh_raffles_timebound_option : v.exh_raffles_timebound_option,
                        exh_raffle_start_time : v.exh_raffle_start_time,
                        exh_raffle_stop_time : v.exh_raffle_stop_time,
                        exh_hero_button_url : v.exh_hero_button_url,
                    }
                    raffle_array.push(raffle_data);
                }

                if(v.ptoken == my_ptoken){
                    exh_count++;
                    hall_name = v.exh_hall;
                    if(typeof eventHallAccess['exhibitor'] !== "undefined" && typeof eventHallAccess['exhibitor'][hall_name] !== "undefined" ){
                        if(is_organizer == 1){
                            if(typeof eventHallAccess['exhibitor'][hall_name]["organizer"] !== "undefined"){
                                eventHallAccess['exhibitor'][hall_name]['organizer']['allowed'] = eventHallAccess['exhibitor'][hall_name]['organizer']['allowed'] - 1;
                            }
                        }
                        if(sponsor_type != '' && sponsor_type != undefined){
                            if(typeof eventHallAccess['exhibitor'][hall_name][sponsor_type] !== "undefined"){
                                eventHallAccess['exhibitor'][hall_name][sponsor_type]['allowed'] = eventHallAccess['exhibitor'][hall_name][sponsor_type]['allowed'] - 1;
                            }
                        }
                        if(user_profile_type != ''){
                            if(typeof eventHallAccess['exhibitor'][hall_name][user_profile_type] !== "undefined"){
                                eventHallAccess['exhibitor'][hall_name][user_profile_type]['allowed'] = eventHallAccess['exhibitor'][hall_name][user_profile_type]['allowed'] - 1;
                            }
                        }
                        if(rsvp_sponsor_title != ''){
                            if(typeof eventHallAccess['exhibitor'][hall_name][rsvp_sponsor_title] !== "undefined"){
                                eventHallAccess['exhibitor'][hall_name][rsvp_sponsor_title]['allowed'] = eventHallAccess['exhibitor'][hall_name][rsvp_sponsor_title]['allowed'] - 1;
                            }
                        }
                    }
                }
                // raffle_array.push(v.raffle_id);
               // $.each(ddata, function (k, v) {
                if(search == ''){
                   //  if(i % 5 == 0)
                    // content += banner;
                }
                var disablecls = '';
                if(v.exh_state != 'active' && v.exh_state != 'live'){
                    disablecls = 'disabled';
                }

                exhibitor_count++;
                var exhibitorWebsiteUrl = v.exh_hero_button_url || '';
                var sponsArr = null;
                if(v.sponsor_id && sponsorsBecomeExhibitor?.length > 0){
                    sponsArr = sponsorsBecomeExhibitor.find(item => item.ID === v.sponsor_id );
                }
                var sponsor_badge_name = '';
                 if(v.sponsor_type !='' && v.sponsor_type != null  && v.sponsor_type != undefined ){
                    sponsor_badge_name = v.sponsor_type;
                }
                else if(sponsArr && sponsArr.sponsor_type !='' && sponsArr.sponsor_type != null  && sponsArr.sponsor_type != undefined ){
                    sponsor_badge_name = sponsArr.sponsor_type;
                }
                var site_link_data  = '';
                var exhibitorWebsiteUrl = v.exh_hero_button_url || '';
                if (exhibitorWebsiteUrl.trim()) {
                    if (isValidURL(exhibitorWebsiteUrl)) {
                        site_link_data = `<div class1="hall-text-sm-bold mb-2" class="d-flex align-items-center" style="gap: 6px;">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.375 9C12.375 9.78047 12.3328 10.5328 12.259 11.25H5.74102C5.66367 10.5328 5.625 9.78047 5.625 9C5.625 8.21953 5.66719 7.46719 5.74102 6.75H12.259C12.3363 7.46719 12.375 8.21953 12.375 9ZM13.3875 6.75H17.7152C17.9016 7.4707 18 8.22305 18 9C18 9.77695 17.9016 10.5293 17.7152 11.25H13.3875C13.4613 10.5258 13.5 9.77344 13.5 9C13.5 8.22656 13.4613 7.47422 13.3875 6.75ZM17.3461 5.625H13.2434C12.8918 3.37852 12.1957 1.49766 11.2992 0.295312C14.052 1.02305 16.2914 3.01992 17.3426 5.625H17.3461ZM12.1043 5.625H5.8957C6.11016 4.34531 6.44062 3.21328 6.84492 2.2957C7.21406 1.46602 7.62539 0.864844 8.02266 0.485156C8.41641 0.1125 8.74336 0 9 0C9.25664 0 9.58359 0.1125 9.97734 0.485156C10.3746 0.864844 10.7859 1.46602 11.1551 2.2957C11.5629 3.20977 11.8898 4.3418 12.1043 5.625ZM4.75664 5.625H0.653906C1.70859 3.01992 3.94453 1.02305 6.70078 0.295312C5.8043 1.49766 5.1082 3.37852 4.75664 5.625ZM0.284766 6.75H4.6125C4.53867 7.47422 4.5 8.22656 4.5 9C4.5 9.77344 4.53867 10.5258 4.6125 11.25H0.284766C0.0984375 10.5293 0 9.77695 0 9C0 8.22305 0.0984375 7.4707 0.284766 6.75ZM6.84492 15.7008C6.43711 14.7867 6.11016 13.6547 5.8957 12.375H12.1043C11.8898 13.6547 11.5594 14.7867 11.1551 15.7008C10.7859 16.5305 10.3746 17.1316 9.97734 17.5113C9.58359 17.8875 9.25664 18 9 18C8.74336 18 8.41641 17.8875 8.02266 17.5148C7.62539 17.1352 7.21406 16.534 6.84492 15.7043V15.7008ZM4.75664 12.375C5.1082 14.6215 5.8043 16.5023 6.70078 17.7047C3.94453 16.977 1.70859 14.9801 0.653906 12.375H4.75664ZM17.3461 12.375C16.2914 14.9801 14.0555 16.977 11.3027 17.7047C12.1992 16.5023 12.8918 14.6215 13.2469 12.375H17.3461Z" fill="#FF6600"></path>
                                </svg>
                                <a href="${exhibitorWebsiteUrl}" class="btn link exhibitor_website " target="_blank" id=""><span>${exhibitorWebsiteUrl}</span></a></div>`;
                    } else {
                        site_link_data = `<div class1="btn link exhibitor_website" class="d-flex align-items-center" style="gap: 6px;">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.375 9C12.375 9.78047 12.3328 10.5328 12.259 11.25H5.74102C5.66367 10.5328 5.625 9.78047 5.625 9C5.625 8.21953 5.66719 7.46719 5.74102 6.75H12.259C12.3363 7.46719 12.375 8.21953 12.375 9ZM13.3875 6.75H17.7152C17.9016 7.4707 18 8.22305 18 9C18 9.77695 17.9016 10.5293 17.7152 11.25H13.3875C13.4613 10.5258 13.5 9.77344 13.5 9C13.5 8.22656 13.4613 7.47422 13.3875 6.75ZM17.3461 5.625H13.2434C12.8918 3.37852 12.1957 1.49766 11.2992 0.295312C14.052 1.02305 16.2914 3.01992 17.3426 5.625H17.3461ZM12.1043 5.625H5.8957C6.11016 4.34531 6.44062 3.21328 6.84492 2.2957C7.21406 1.46602 7.62539 0.864844 8.02266 0.485156C8.41641 0.1125 8.74336 0 9 0C9.25664 0 9.58359 0.1125 9.97734 0.485156C10.3746 0.864844 10.7859 1.46602 11.1551 2.2957C11.5629 3.20977 11.8898 4.3418 12.1043 5.625ZM4.75664 5.625H0.653906C1.70859 3.01992 3.94453 1.02305 6.70078 0.295312C5.8043 1.49766 5.1082 3.37852 4.75664 5.625ZM0.284766 6.75H4.6125C4.53867 7.47422 4.5 8.22656 4.5 9C4.5 9.77344 4.53867 10.5258 4.6125 11.25H0.284766C0.0984375 10.5293 0 9.77695 0 9C0 8.22305 0.0984375 7.4707 0.284766 6.75ZM6.84492 15.7008C6.43711 14.7867 6.11016 13.6547 5.8957 12.375H12.1043C11.8898 13.6547 11.5594 14.7867 11.1551 15.7008C10.7859 16.5305 10.3746 17.1316 9.97734 17.5113C9.58359 17.8875 9.25664 18 9 18C8.74336 18 8.41641 17.8875 8.02266 17.5148C7.62539 17.1352 7.21406 16.534 6.84492 15.7043V15.7008ZM4.75664 12.375C5.1082 14.6215 5.8043 16.5023 6.70078 17.7047C3.94453 16.977 1.70859 14.9801 0.653906 12.375H4.75664ZM17.3461 12.375C16.2914 14.9801 14.0555 16.977 11.3027 17.7047C12.1992 16.5023 12.8918 14.6215 13.2469 12.375H17.3461Z" fill="#FF6600"></path>
                                </svg>
                        <span title="${exhibitorWebsiteUrl}">${exhibitorWebsiteUrl}</span></div>`;
                    }
                }

                content += `
                    <div class="new-exh-list mb-3 ${v.ptoken == my_ptoken ? 'sponsor-highlight' : ''}">
                        <div class="p-3 px-lg-5 d-flex" style="gap: 12px; flex: 1;">
                            <div class="d-flex flex-column flex-md-row" style="gap: 16px; flex: 1;">
                                <a target="_blank" title="View exhibitor" data-metrics="view_exhibitor" href="${_taoh_site_url_root}/events/exhibitors/${v.ID}/${eventtoken}" class="metrics_action">
                                    <div class="g-overlay-con">
                                        <div class="n-hall-list-bg d-md-none" style="background-image: url(${v.exh_logo})"></div>
                                        <!--<div class="glass-overlay d-md-none"></div>-->
                                        <img class="n-hall-list-pic" src="${v.exh_logo}" alt="">
                                    </div>
                                </a>

                                <div style="flex: 1;">
                                    <div class="d-flex align-items-center justify-content-between" style="gap: 12px;">
                                        <div class="d-flex flex-wrap" style="gap: 3px;">
                                            <p onclick="LoadMetaWithHall('event_exhibitor','${v.exh_hall}','${v.exh_hall}');"
                                            class="n-info-badge mr-2" style="background-color:#F9A386;;color:#000000">
                                                ${v.exh_hall ? (Array.isArray(v.exh_hall) ? v.exh_hall.map(taoh_desc_decode_new).join(', ') : taoh_desc_decode_new(v.exh_hall)) : ''}
                                            </p>
                                            ${sponsor_badge_name ? `<p class="n-info-badge mr-2" style="background-color:#BEEE95;;color:#000000">${sponsor_badge_name}</p>` : ''}
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between flex-wrap my-2" style="flex: 1; gap: 12px;">
                                        <div class="d-flex flex-column" style="gap:3px;">    
                                            <div class="d-flex align-items-center mb-1" style="gap: 10px;">
                                                <a target="_blank" title="View exhibitor" data-metrics="view_exhibitor" href="${_taoh_site_url_root}/events/exhibitors/${v.ID}/${eventtoken}" class="metrics_action">
                                                    <h6 class="n-exh-name mr-2">${taoh_desc_decode_new(v.exh_session_title)}</h6>
                                                </a>`;
                                                
                                                if(v.exh_raffles == '1') {
                                                    raffle_start = new Date(v.exh_raffle_start_time);
                                                    raffle_end = new Date(v.exh_raffle_stop_time);
                                                    if(v.exh_raffles_timebound_option == 0 || (v.exh_raffles_timebound_option == 1 && new Date() >= raffle_start && new Date() <= raffle_end )){ // raffle date conditions
                                                        content += ` <div class="d-flex align-items-center" title="${taoh_desc_decode_new(v.exh_session_title)}">
                                                                        <svg style="min-width: fit-content;" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M7.44141 2.6875L8.80078 5H8.75H5.9375C5.07422 5 4.375 4.30078 4.375 3.4375C4.375 2.57422 5.07422 1.875 5.9375 1.875H6.02344C6.60547 1.875 7.14844 2.18359 7.44141 2.6875ZM2.5 3.4375C2.5 4 2.63672 4.53125 2.875 5H1.25C0.558594 5 0 5.55859 0 6.25V8.75C0 9.44141 0.558594 10 1.25 10H18.75C19.4414 10 20 9.44141 20 8.75V6.25C20 5.55859 19.4414 5 18.75 5H17.125C17.3633 4.53125 17.5 4 17.5 3.4375C17.5 1.53906 15.9609 0 14.0625 0H13.9766C12.7305 0 11.5742 0.660156 10.9414 1.73438L10 3.33984L9.05859 1.73828C8.42578 0.660156 7.26953 0 6.02344 0H5.9375C4.03906 0 2.5 1.53906 2.5 3.4375ZM15.625 3.4375C15.625 4.30078 14.9258 5 14.0625 5H11.25H11.1992L12.5586 2.6875C12.8555 2.18359 13.3945 1.875 13.9766 1.875H14.0625C14.9258 1.875 15.625 2.57422 15.625 3.4375ZM1.25 11.25V18.125C1.25 19.1602 2.08984 20 3.125 20H8.75V11.25H1.25ZM11.25 20H16.875C17.9102 20 18.75 19.1602 18.75 18.125V11.25H11.25V20Z" fill="#FFC107"/>
                                                                            <defs>
                                                                            <linearGradient id="paint0_linear_7222_848" x1="10" y1="0" x2="10" y2="20" gradientUnits="userSpaceOnUse">
                                                                            <stop stop-color="#FFC107"/>
                                                                            <stop offset="1" stop-color="#FF5C00"/>
                                                                            </linearGradient>
                                                                            </defs>
                                                                        </svg>
                                                                    </div>
                                                                `;
                                                    }
                                                }

                content += ` </div>
                                        </div>
                                    
                                        <div class="mr-lg-5 d-flex align-items-center" style="gap: 6px;">
                                        <!--  v.ptoken == my_ptoken-->
                                            ${((v.ptoken == my_ptoken  && opt == 'chat') || is_organizer == 1) ? `
                                                <a title="Edit Exhibitor" style="width:30px;" class="svg-opt-con btn edit-exhibitor metrics_action" id="edit_exh_${v.ID}" data-id="${v.ID}" data-type="exhibitor" 
                                            data-metrics="edit_exhibitor"><i class="fa-solid fa-edit"></i> </a> `:''}

                                            <p class="exhi-sponsor-type" page="events_exh1" id="exhi_sponsor_type_${v.ID}" style="display:none">${sponsor_badge_name}</p>
                                            <p class="exhi-sponsor-owner" id="exhi_owner_${v.ID}" style="display:none">${v.ptoken}</p>
                                                        
                                            <a target="_blank" title="View exhibitor" data-metrics="view_exhibitor" href="${_taoh_site_url_root}/events/exhibitors/${v.ID}/${eventtoken}" 
                                            class="svg-opt-con btn metrics_action"><i class="fa-solid fa-eye"></i></a>

                                             ${is_organizer == 1 ? `
                                            <a title="Delete Exhibitor" class="svg-opt-con  btn p-0 delete-exhibitor metrics_action" id="delte_exh_${v.ID}" data-id="${v.ID}" data-type="exhibitor" data-metrics="delete_exhibitor">
                                                <svg width="15" height="15" viewBox="0 0 42 37" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M5.25 0C2.3543 0 0 2.3543 0 5.25V31.5C0 34.3957 2.3543 36.75 5.25 36.75H36.75C39.6457 36.75 42 34.3957 42 31.5V5.25C42 2.3543 39.6457 0 36.75 0H5.25ZM14.3555 11.7305C15.1266 10.9594 16.3734 10.9594 17.1363 11.7305L20.9918 15.5859L24.8473 11.7305C25.6184 10.9594 26.8652 10.9594 27.6281 11.7305C28.391 12.5016 28.3992 13.7484 27.6281 14.5113L23.7727 18.3668L27.6281 22.2223C28.3992 22.9934 28.3992 24.2402 27.6281 25.0031C26.857 25.766 25.6102 25.7742 24.8473 25.0031L20.9918 21.1477L17.1363 25.0031C16.3652 25.7742 15.1184 25.7742 14.3555 25.0031C13.5926 24.232 13.5844 22.9852 14.3555 22.2223L18.2109 18.3668L14.3555 14.5113C13.5844 13.7402 13.5844 12.4934 14.3555 11.7305Z" fill="#FF0000"></path>
                                                </svg>
                                            </a>`:''}

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
            }

            if(exhibitor_count > 0){
                $('#exhibitors_list').html(content);
                $('#exhibitor_main_title').show();
                is_content =1;
            }

            $(".exhibitor_block").removeClass("align-self-start lblur");
            if(eventHallAccess > 0){
                response = {'success':true,output:eventHallAccess};
                IntaoDB.setItem(objStores.event_store.name, {
                    taoh_data: eventHallAccessKey,
                    values: response,
                    timestamp: Date.now()
                });
            }

            // is_content =1;
        }

        if (spon_list != undefined && spon_list.length > 0) {
            var sponsor_count = 0;
            content += `<div id="sponsor_main_title_strip" class="sponsor_main_title_strip divider-container d-flex align-items-center mt-1 mb-2" style="gap: 6px;display:none !important;">
                            <div class="left-line" style="max-width:40%"></div>
                            <p class="divider-text text-center" style="width:300px;">Sponsors</p>
                            <div class="right-line" style="width:50%"></div>
                        </div>`;

            for (let k = 0; k < spon_list.length; k++) {
                const v = spon_list[k];

                if (v.ptoken == my_ptoken && !removeSponsor.includes(v.ID)) {
                    exh_count++;
                }

                if (country_locked != 1) {
                    const userInfo = await ft_getUserInfo(v.ptoken, 'public');
                    if (userInfo.full_location != '' && userInfo.full_location != undefined && userInfo.full_location != null) {
                        var exh_country_array = userInfo.full_location.split(',');
                        var exh_country_name = exh_country_array[exh_country_array.length - 1].trim();
                    
                        if(exh_country_name != user_country_name){
                            continue;
                        }
                    }
                }
                var exh_class = '';
                if(v.sponsor_type != undefined && v.sponsor_type != null && !removeSponsor.includes(v.ID) ){

                        sponsor_count++;
                        is_content =1;
                        sponsorPtokenArray[v.ptoken] =  v.sponsor_type;
                        

                        var site_link_data = `<div class="d-flex align-items-center" style="gap: 6px;">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.375 9C12.375 9.78047 12.3328 10.5328 12.259 11.25H5.74102C5.66367 10.5328 5.625 9.78047 5.625 9C5.625 8.21953 5.66719 7.46719 5.74102 6.75H12.259C12.3363 7.46719 12.375 8.21953 12.375 9ZM13.3875 6.75H17.7152C17.9016 7.4707 18 8.22305 18 9C18 9.77695 17.9016 10.5293 17.7152 11.25H13.3875C13.4613 10.5258 13.5 9.77344 13.5 9C13.5 8.22656 13.4613 7.47422 13.3875 6.75ZM17.3461 5.625H13.2434C12.8918 3.37852 12.1957 1.49766 11.2992 0.295312C14.052 1.02305 16.2914 3.01992 17.3426 5.625H17.3461ZM12.1043 5.625H5.8957C6.11016 4.34531 6.44062 3.21328 6.84492 2.2957C7.21406 1.46602 7.62539 0.864844 8.02266 0.485156C8.41641 0.1125 8.74336 0 9 0C9.25664 0 9.58359 0.1125 9.97734 0.485156C10.3746 0.864844 10.7859 1.46602 11.1551 2.2957C11.5629 3.20977 11.8898 4.3418 12.1043 5.625ZM4.75664 5.625H0.653906C1.70859 3.01992 3.94453 1.02305 6.70078 0.295312C5.8043 1.49766 5.1082 3.37852 4.75664 5.625ZM0.284766 6.75H4.6125C4.53867 7.47422 4.5 8.22656 4.5 9C4.5 9.77344 4.53867 10.5258 4.6125 11.25H0.284766C0.0984375 10.5293 0 9.77695 0 9C0 8.22305 0.0984375 7.4707 0.284766 6.75ZM6.84492 15.7008C6.43711 14.7867 6.11016 13.6547 5.8957 12.375H12.1043C11.8898 13.6547 11.5594 14.7867 11.1551 15.7008C10.7859 16.5305 10.3746 17.1316 9.97734 17.5113C9.58359 17.8875 9.25664 18 9 18C8.74336 18 8.41641 17.8875 8.02266 17.5148C7.62539 17.1352 7.21406 16.534 6.84492 15.7043V15.7008ZM4.75664 12.375C5.1082 14.6215 5.8043 16.5023 6.70078 17.7047C3.94453 16.977 1.70859 14.9801 0.653906 12.375H4.75664ZM17.3461 12.375C16.2914 14.9801 14.0555 16.977 11.3027 17.7047C12.1992 16.5023 12.8918 14.6215 13.2469 12.375H17.3461Z" fill="#FF6600"></path>
                                </svg>
                            <a href="${v.link}" class="btn link exhibitor_website" target="_blank" id=""><span>${v.link}</span></a></div>`;

                            var spons_desc = '';
                            if($.trim(v.description) != ''){
                                var spons_desc = taoh_desc_decode(v.description);
                                if(spons_desc.length > 75){
                                    var limitedText = spons_desc.substring(0, 75);
                                    spons_desc = limitedText + `<span id="dots_es${k}">...</span><span id="more_es${k}" style="display:none;">${spons_desc.substring(75)} </span>
                                    <button class="readmore-btn" onclick="readmore('es'+${k})" id="morebtn_es${k}">Read more</button>`;
                                }else{
                                    spons_desc = spons_desc;
                                }
                            }
                            var badge = '';
                        /* if(v.display_type == 'full')
                                badge= `<img class="lazy sponsor-badge mb-3" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/gold.svg" alt="avatar" style="margin-top: -10px;">`;
                            else if(v.display_type == 'semi')   
                                badge= `<img class="lazy sponsor-badge mb-3" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/silver.svg" alt="avatar" style="margin-top: -10px;">`;
                            else if(v.display_type == 'logos')
                                badge= `<img class="lazy sponsor-badge mb-3" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bronze.svg" alt="avatar" style="margin-top: -10px;">`;
                            */  
                        content += ` <!-- new exh list -->
                            <div class="new-exh-list mb-3  ${v.ptoken == my_ptoken ? 'enable_btn sponsor-highlight' : 'disable_btn'}">
                                <!-- gray_bg -->
                                <!-- <div class="gradient-bg-border"></div> -->

                                <div class="p-3 px-lg-5 d-flex" style="gap: 12px; flex: 1;">
                                    <div class="d-flex flex-wrap align-items-center" style="gap: 12px; flex: 1;">
                                        <div class="g-overlay-con mr-3">
                                            <!--<div class="n-hall-list-bg" style="background-image: url(${v.image})"></div>
                                            <div class="glass-overlay"></div>-->
                                            <img class="n-hall-list-pic" id="exhi_logo_${v.ID}" src="${v.image}" alt="">
                                        </div>

                                        <div style="flex: 1;">
                                            <div class="d-flex align-items-center justify-content-between" style="gap: 12px;">
                                                <div class="d-flex flex-wrap" style="gap: 3px;">
                                                    <p class="n-info-badge mr-2" style="background-color:#BEEE95;;color:#000000">${v.sponsor_type || ''}</p>
                                                </div>

                                            </div>

                                            <div class="d-flex align-items-center justify-content-between flex-wrap" style="flex: 1; gap: 12px;">
                                                <div class="d-flex align-items-center justify-content-between flex-wrap my-2" style="flex: 1; gap: 12px;">
                                                    <div class="d-flex flex-column" style="gap:3px;">
                                                    <div class="d-flex ml-auto" style="gap: 10px;">
                                                        <h6 class="n-exh-name mr-2" id="exhi_title_${v.ID}">${taoh_desc_decode(v.title)}</h6>
                                                        
                                                        <p class="exhi-description" id="exhi_description_${v.ID}" style="display:none">${ taoh_desc_decode(v.description)}</p>
                                                        <p class="exhi-sponsor-type" page="events_exh2" id="exhi_sponsor_type_${v.ID}" style="display:none">${v.sponsor_type}</p>
                                                        <p class="exhi-sponsor-owner" id="exhi_owner_${v.ID}" style="display:none">${v.ptoken}</p>
                                                        <div style="display:none;" class="n-spon-badge-con">
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
                                            </div> `;

                                    content += ` 
                                                    
                                                <div class="mr-lg-5 d-flex align-items-center" style="gap: 6px;">

                                                    
                                                    ${((v.ptoken == my_ptoken && opt == 'chat') || is_organizer == 1) ? `
                                                            <a title="Configure Sponsor to Exhibitor" style="width:30px" 
                                                            class="svg-opt-con btn edit-exhibitor metrics_action" id="edit_exh_${v.ID}" data-id="${v.ID}" data-type="sponsor" 
                                                            data-metrics="configure_exhibitor"> <i class="fa fa-cog" aria-hidden="true"></i></a>
                                                        `:''}
                                                   <a target="_blank" title="View exhibitor"  data-metrics="view_exhibitor" href="${_taoh_site_url_root}/events/sponsor/${v.ID}/${eventtoken}" 
                                                   class="svg-opt-con  btn  metrics_action">
                                                  <i class="fa-solid fa-eye"></i></a>


                                                     ${is_organizer == 1 ? `
                                                        <a class="svg-opt-con  btn p-0 delete-exhibitor metrics_action" id="delte_exh_${v.ID}" data-id="${v.ID}" data-type="exhibitor" data-metrics="delete_exhibitor">
                                                            <svg width="15" height="15" viewBox="0 0 42 37" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M5.25 0C2.3543 0 0 2.3543 0 5.25V31.5C0 34.3957 2.3543 36.75 5.25 36.75H36.75C39.6457 36.75 42 34.3957 42 31.5V5.25C42 2.3543 39.6457 0 36.75 0H5.25ZM14.3555 11.7305C15.1266 10.9594 16.3734 10.9594 17.1363 11.7305L20.9918 15.5859L24.8473 11.7305C25.6184 10.9594 26.8652 10.9594 27.6281 11.7305C28.391 12.5016 28.3992 13.7484 27.6281 14.5113L23.7727 18.3668L27.6281 22.2223C28.3992 22.9934 28.3992 24.2402 27.6281 25.0031C26.857 25.766 25.6102 25.7742 24.8473 25.0031L20.9918 21.1477L17.1363 25.0031C16.3652 25.7742 15.1184 25.7742 14.3555 25.0031C13.5926 24.232 13.5844 22.9852 14.3555 22.2223L18.2109 18.3668L14.3555 14.5113C13.5844 13.7402 13.5844 12.4934 14.3555 11.7305Z" fill="#FF0000"></path>
                                                            </svg>
                                                        </a>`:''}
                                                
                                               </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                            </div>
                            <!-- new exh list end --> `;
                            // ${v.link}
                            
                                /* content += `
                                
                                        <div id="sponsor_exh_${v.ID}" class="hall-list d-flex flex-column flex-md-row mb-3" style="position: relative;">
                                            
                                            <div class="exhibitor-main-logo d-flex align-items-center 
                                            justify-content-center" style="background-color: #fff;border:1px solid #d3d3d3; ">
                                                <div class="exhibitor-bg" style="background-image: url(${v.image})"></div>
                                                <div class="glass-overlay"></div>
                                                <img class="main-img" id="exhi_logo_${v.ID}" src="${v.image}" alt="">
                                            </div>
                                            <div class="info d-flex flex-column flex-md-row align-items-md-center justify-content-between px-3 py-2 px-lg-4" style="flex: 1; border: 1px solid #d3d3d3; gap: 12px;">
                                                <div>
                                                    <p class="exhi-title" id="exhi_title_${v.ID}" >${v.title}</p>
                                                    <a href="#" id="exhi_link_${v.ID}" class="exhi-link">${v.link}</a>
                                                    <p class="exhi-description" id="exhi_description_${v.ID}">${spons_desc}</p>
                                                    <p class="exhi-display_type" id="exhi_display_type_${v.ID}" >${v.sponsor_type}</p>
                                                </div>
                                                ${v.ptoken == my_ptoken ? `
                                                <a id="edit_exh_${v.ID}" data-id="${v.ID}" class="btn more-info edit-exhibitor">
                                                
                                                    <i class="fas fa-edit"></i>
                                                    <span>Edit</span>
                                                </a>`:''}
                                            </div>
                                            ${badge}


                                        
                                            </div>
                            
                                    `; */
                        
                }
                
            // });
            }
             
            if(sponsor_count > 0){
                $('#exhibitors_list').html(content);
                $('.sponsor_main_title_strip').css('display','block');
                $('.sponsor_main_title_strip').show();
            }

            $(".exhibitor_block").removeClass("align-self-start lblur");
        }

        $("#exh_count").val(exh_count);

        var event_status = $('#event_status_hidden').val();

        if (!is_content) {
            if (search != '') {
                $('#exhibitors_list').html('<div class="text-center">No Exhibitors Found</div>');
            } else {
                $(".exhibitor_block").addClass("align-self-start lblur");
                if (event_status == 1 || event_status == 0) {
                    $('#exhibitor_desc,#exhibitor_top').remove();
                } else {
                    $("#exhibitor_default_banner,#exhibitor_default_list").show();
                }
            }
        } else {
            var sponsor_type = $('#sponsor_type').val();
            if((event_status == 2 || typeof event_status === "undefined" ) && sponsor_type == ''){ // not live and not rsvped as sponsor
                $('#exhibitors_list .new-exh-list').each(function (index) {
                if ((index + 1) % 2 === 0 || $('#exhibitors_list .new-exh-list').length == 1) { // show 0 day banner for every 2nd block
                    $(this).after(`
                    <div class="v2-banner flex-column flex-md-row p-3 px-lg-5">
                        <div class="v2-svg-con">
                            <svg width="59" height="62" viewBox="0 0 59 62" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M49 5.5C49 2.46773 46.3079 0 43 0C39.6921 0 37 2.46773 37 5.5C37 8.53227 39.6921 11 43 11C46.3079 11 49 8.53227 49 5.5Z" fill="#2557A7"/>
                                <path d="M20 5.5C20 2.46773 17.5323 0 14.5 0C11.4677 0 9 2.46773 9 5.5C9 8.53227 11.4677 11 14.5 11C17.5323 11 20 8.53227 20 5.5Z" fill="#2557A7"/>
                                <path d="M21.9888 33H21.9611H0V40H59V33H21.9888Z" fill="#2557A7"/>
                                <path d="M31 16.7506V30.9924H35.5208V19.9691C35.5208 19.5797 35.8194 19.2666 36.1907 19.2666C36.562 19.2666 36.8606 19.5797 36.8606 19.9691V29.7475C36.8759 29.8078 36.8874 29.868 36.8874 29.9322V31H50.1394V19.9736C50.1394 19.5842 50.438 19.2711 50.8093 19.2711C51.1806 19.2711 51.4792 19.5842 51.4792 19.9736V31H56V16.7582C56 16.3809 55.889 16.0236 55.6823 15.7266C55.4181 15.3452 55.0162 15.0923 54.576 15.0241C54.4803 15.008 54.3999 15 54.3195 15H32.684C31.7577 15 31.0036 15.7908 31.0036 16.7622L31 16.7506Z" fill="#2557A7"/>
                                <path d="M1 16.7506V30.9924H5.70167V19.9691C5.70167 19.5797 6.0122 19.2666 6.39836 19.2666C6.78453 19.2666 7.09504 19.5797 7.09504 19.9691V29.7475C7.11097 29.8078 7.12291 29.868 7.12291 29.9322V31H20.905V19.9736C20.905 19.5842 21.2155 19.2711 21.6016 19.2711C21.9878 19.2711 22.2983 19.5842 22.2983 19.9736V31H27V16.7582C27 16.3809 26.8845 16.0236 26.6696 15.7266C26.3949 15.3452 25.9769 15.0923 25.519 15.0241C25.4195 15.008 25.3359 15 25.2523 15H2.75138C1.78796 15 1.00374 15.7908 1.00374 16.7622L1 16.7506Z" fill="#2557A7"/>
                                <path d="M4 41H56V62H4V41Z" fill="#2557A7"/>
                                <circle cx="16.5" cy="51.5" r="6.5" fill="#5DC1F1"/>
                                <rect x="27" y="46" width="23" height="4" rx="2" fill="#5DC1F1"/>
                                <rect x="27" y="51" width="23" height="5" rx="2.5" fill="#5DC1F1"/>
                            </svg>
                        </div>
                        <div>
                            <h6 class="mb-2">Unlock your Exhibiting Slot</h6>
                            <p class="mb-2">Show case your Brand ! Exhibit your Products !</p>
                            <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                                <button type="button" class="btn banner-v2-btn mt-0 mr-1 event_sponsor_right_header" id='sponsor_contactus' data-toggle="modal" data-target="#sponsorInfo">Become a Sponsor !</button>

                                <button type="button" class="btn banner-v2-btn mt-0 mr-1 get-started event_sponsor_right_header" data-toggle="modal" data-target="#sponsorInfo">More Info !</button>
                                <!-- <button type="button" class="btn banner-v2-btn mt-0 mr-1" id='exhibitor_contactus'>Contact us !</button> -->
                                <a class="btn banner-v2-btn mt-0 mr-1" id='exhibitor_contactus' data-toggle="modal" data-target="#contactusModal">Contact us !</a>
                            </div>
                        </div>
                    </div>
                    `);
                }
                });
                if(!isLoggedIn){
                    $(".get-started, #sponsor_contactus").hide();
                }
                getEventBaseInfo({ eventtoken: eventToken }, false)
                    .then(({requestData, response}) => {
                    let event_output = response.output;
                    let conttoken_data = event_output.conttoken;
                    var event_status = $('#event_status_hidden').val();

                    /* Event Sponsor popup*/
                    let eventSponsorWidgetType = conttoken_data.event_sponsor_levels || {};
                    let eventTicketType = conttoken_data.ticket_types || {};
                    let eventSponsorWidgetTypeStatusList = Object.values(eventSponsorWidgetType).map(
                        widget => widget.quantity > 0 ? 1 : 0
                    );
                    let event_form_version = conttoken_data.event_form_version ?? 1;
                    let social_share_status = '<?php echo $social_token;?>';
                    let trackingtoken = '<?php echo $trackingtoken; ?>';
                    let is_social_share_enabled = conttoken_data.event_social_sharing;
                    constructSponsorInfoPopup(eventToken, eventSponsorWidgetType,user_profile_type,conttoken_data.org_email,social_share_status,eventTicketType,event_form_version,is_social_share_enabled,trackingtoken,isLoggedIn);
                    if (event_status == 2 && eventSponsorWidgetTypeStatusList.includes(1)) {
                        $('.event_sponsor_right_header, .get-started').show();
                    }else{
                        $('.event_sponsor_right_header, .get-started').hide();
                    }
                });
            }
        }
       

        // console.log('Raffle Array',raffle_array);   
        loader(false, $("#exhibitors_loaderArea"));
        if(raffle_array.length > 0){
            // console.log('----raffle_array-------',raffle_array)
            // getRaffles(raffle_array);
        }
    }

    function getRaffles(raffle_array){
        if(raffle_array !=undefined && raffle_array.length > 0){
            var raffles_slide = ''; 
            var raffle_carousel = ''; 
            //raffles_slide //raffle_carousel
            /*exh_raffle_title : v.exh_raffle_title,
                        exh_raffle_description : v.exh_raffle_description,
                        exh_raffles_timebound_option : v.exh_raffles_timebound_option,
                        exh_raffle_start_time : v.exh_raffle_start_time,
                        exh_raffle_stop_time : v.exh_raffle_stop_time, */
            $.each(raffle_array, function (i, v) {
                //console.log('---vv---------',v)
                raffle_start = new Date(v.exh_raffle_start_time);
                raffle_end = new Date(v.exh_raffle_stop_time);
                if(v.exh_raffles_timebound_option == 0 || (v.exh_raffles_timebound_option == 1 && new Date() >= raffle_start && new Date() <= raffle_end )){ // raffle date conditions
                    let exhibitorWebsiteUrl = v.exh_hero_button_url || '';
                    raffles_slide += `<li data-target="#carouselExampleIndicators" data-slide-to="${i}" class="${i== 0 ?'active':''}"></li>`;
                    raffle_carousel +=`  <div class="carousel-item ${i== 0 ?'active':''}">
                                            <div class="raffle-container">
                                                    <div class="raffle-top pt-4">
                                                        <p class="text-sm text-center pt-2">${taoh_desc_decode(v.exh_raffle_title)}</p>
                                                        <hr style="border-top: 2px solid #ffffff;">
                                                        <p class="text-sm line-clamp-7">${taoh_desc_decode(v.exh_raffle_description)}</p>
                                                    </div>
                                                    <div class="raffle-bottom">
                                                        <svg width="346" height="110" viewBox="0 0 346 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect width="346" height="91" fill="#84CECA"/>
                                                            <circle cx="54.5" cy="90.5" r="19.5" fill="#ffffff"/>
                                                            <circle cx="101.5" cy="90.5" r="19.5" fill="#ffffff"/>
                                                            <circle cx="147.5" cy="90.5" r="19.5" fill="#ffffff"/>
                                                            <circle cx="194.5" cy="90.5" r="19.5" fill="#ffffff"/>
                                                            <circle cx="241.5" cy="90.5" r="19.5" fill="#ffffff"/>
                                                            <circle cx="288.5" cy="90.5" r="19.5" fill="#ffffff"/>
                                                        </svg>
                                                        <a href="${exhibitorWebsiteUrl !='' ? exhibitorWebsiteUrl : 'javascript:void(0)'}" >
                                                            <p class="redeem">Redeem Now !</p>
                                                        </a>
                                                    </div>

                                            </div>
                                     </div> `;
                }
    
            });
            //console.log('------------raffles_slide--------',raffles_slide);
            //console.log('------------raffle_carousel--------',raffle_carousel);
            $('#raffles_slide').html(raffles_slide);
            $('#raffle_carousel').html(raffle_carousel);
            $('#raffles_list').show();
            $('#raffle_carousel').carousel({
                interval: 2000
            });
       
        }
    }



    $(document).on('click', '.edit-exhibitor', async function () {
        let exh_id = $(this).data('id');
        let exh_type = $(this).data('type');

        // setTimeout(() => {
            getEventsHall(eventToken);
        // }, 1000);

    setTimeout(async () => {
            var eventHallAccess = [];
            var eventHallAccessKey = `event_hall_access_${eventToken}`;
            const data = await IntaoDB.getItem(objStores.event_store.name, eventHallAccessKey); // await 
            if (data?.values) {
                eventHallAccess = data?.values.output;
            }

        $('#exh_tags').val([]).trigger('change');
        if (parseInt(exh_id)) {
            getEventBaseInfo({ eventtoken: eventToken }, false)
                .then(async ({requestData, response}) => {
                    let event_output = response.output;
                    let event_owner = event_output.ptoken;
                    let conttoken_data = event_output.conttoken;
                    var room_keywords_count = 1;
                    var rsvp_sponsor_title = $("#rsvp_sponsor_title").val();
                    let event_form_version = conttoken_data.event_form_version ?? 1;
                    var TicketArr = conttoken_data.ticket_types.find(ticket => taoh_title_desc_decode(ticket.title) === rsvp_sponsor_title) || {};

                    var  event_organizer_ptokens = (conttoken_data.event_organizer_ptokens || "")
                        .split(',').concat(event_owner)
                        .map(token => token.trim())
                        .filter(token => token);

                    let event_instance_owner = conttoken_data.ptoken;
                    event_organizer_ptokens.push(event_instance_owner);

                    // let exhibitor_halls = conttoken_data.exhibitor_halls;
                    // let allowed_exhibitor_halls = Array.isArray(exhibitor_halls)
                    //     ? exhibitor_halls.filter(hall => (hall.status === '1' && event_organizer_ptokens.includes(my_pToken)) || hall.status === '2')
                    //     : [];
                    const exh_allowed = new Set(["2", "3"]);
                    const allowed_exhibitor_halls = (Array.isArray(conttoken_data?.event_halls) ? conttoken_data.event_halls : [])
                        .filter(h => Number(h?.id) > 0 && h?.name && exh_allowed.has(h.accesslevel));

                    if (!allowed_exhibitor_halls.length) {
                        taoh_set_error_message('No Exhibitor Hall available for setup');
                        return;
                    }

                    var sponsor_type = $('#sponsor_type').val();
                    var is_organizer = $("#is_organizer").val();
                    var user_profile_type = $("#user_profile_type").val();
                    var rsvp_sponsor_title = $("#rsvp_sponsor_title").val();
                    var hall_exist = 0;

                    const form = $('#setup_exhibitor_slot_form');
                    if (!form.length) return;

                    // Clear all standard inputs, selects, and textareas in the form
                    form[0].reset();
                    form.find('input[type="hidden"][data-dynamic="1"]').val('');
                    if (form.data('validator')) {
                        form.validate().resetForm();
                        form.find('.is-invalid, .is-valid, .error').removeClass('is-invalid is-valid error');
                    }

                    const tagsArr = conttoken_data?.event_tags ? conttoken_data.event_tags.split(",") : [];
                    $(".tags-field").select2({
                        data: tagsArr,
                        width: '100%'
                    });

                    /* $('#exh_tags_input').on('keyup', function() {
                        var query = $(this).val().toLowerCase(); // Get the input value
                        var matchedSuggestions = tagsArr.filter(function(item) {
                            return item.toLowerCase().includes(query); // Filter suggestions
                        });

                        // Show suggestions if there are matches
                        if (query.length > 0 && matchedSuggestions.length > 0) {
                            var suggestionList = '';
                            matchedSuggestions.forEach(function(item) {
                                suggestionList += '<li class="list-group-item list-group-item-action">' + item + '</li>';
                            });
                            $('#exh_tags_dropdown').html(suggestionList).show(); // Show the dropdown with suggestions
                        } else {
                            $('#exh_tags_dropdown').hide(); // Hide the dropdown if no matches
                        }
                    }); */

                    $(document).on('click', '.list-group-item', function () {
                        var contentToPrepend = "<div class='item active last-active'>" + $(this).text() + "</div>";
                        $('.exh_tags_ts_control').prepend(contentToPrepend);
                        // $('#exh_tags_input').val(); // Set input value to selected suggestion
                        $('#exh_tags_dropdown').hide(); // Hide the dropdown after selection
                        // $("#exh_tags").append("<option value='"+$(this).text()+"' selected>"+$(this).text()+"</option>");
                        var tagValue = $(this).text();
                        if ($("#exh_tags option[value='" + tagValue + "']").length === 0) {
                            $("#exh_tags").append("<option value='" + tagValue + "' selected>" + tagValue + "</option>");
                        } else {
                            $("#exh_tags option[value='" + tagValue + "']").prop('selected', true);
                        }
                        $('#exh_tags').select2('destroy');
                        $('#exh_tags').select2({width: '100%'});
                        $("#exh_tags_input").val("");
                    });

                    if (event_organizer_ptokens.includes(my_pToken)) {
                        room_keywords_count = 3;
                    }
                    const exhibitorslotmodal_elem = $('#exhibitorSlotModal');
                    //alert(exh_type)
                    if (exh_type == 'exhibitor') {
                        exhibitorslotmodal_elem.find('#exhibitor_id').val(exh_id);

                        var data = {
                            'taoh_action': 'get_event_exhibitor',
                            'token': _taoh_ajax_token,
                            'eventtoken': eventToken,
                            'exhibitor_id': exh_id
                        };
                        jQuery.get(_taoh_site_ajax_url, data, function (response) {
                            if (response.success) {
                                exhibitor_data = v = response.output;
                                if (exhibitor_data.exh_tags != '') {
                                    $.each(exhibitor_data.exh_tags, function (i, tags) {
                                        console.log(tags);
                                        var contentToPrepend = "<div class='tag item active last-active'>" + tags + "</div>";
                                        $('.exh_tags_ts_control').prepend(contentToPrepend); // Set input value to selected suggestion
                                        $('#exh_tags_dropdown').hide(); // Hide the dropdown after selection
                                        // $("#exh_tags").prepend("<option value='"+tags+"' selected>"+tags+"</option>");
                                        var tagValue = tags;
                                        if ($("#exh_tags option[value='" + tagValue + "']").length === 0) {
                                            $("#exh_tags").append("<option value='" + tagValue + "' selected>" + tagValue + "</option>");
                                        } else {
                                            $("#exh_tags option[value='" + tagValue + "']").prop('selected', true);
                                        }
                                        $('#exh_tags').select2('destroy');
                                        $('#exh_tags').select2({width: '100%'});
                                    });
                                }

                                let exh_sponsor_owner = $('#exhi_owner_' + exh_id).html();
                                let exh_sponsor_type = $('#exhi_sponsor_type_' + exh_id).html();
                                //alert('-----aaaaaaaaa------'+exh_sponsor_type);

                                exhibitorslotmodal_elem.find('#ptoken').val(exhibitor_data.ptoken);
                                exhibitorslotmodal_elem.find('#sponsor_id').val(exhibitor_data.sponsor_id);
                                exhibitorslotmodal_elem.find('#exh_session_title').val(taoh_desc_decode(exhibitor_data.exh_session_title));
                                exhibitorslotmodal_elem.find('#exh_subtitle').val(taoh_desc_decode(exhibitor_data.exh_subtitle));
                                exhibitorslotmodal_elem.find('#exh_description').val(taoh_desc_decode(exhibitor_data.exh_description));
                                exhibitorslotmodal_elem.find('#exh_hall').val(exhibitor_data.exh_hall);
                                if (exhibitor_data.exh_room_status == 1) {
                                    exhibitorslotmodal_elem.find('#exh_room_status').attr("checked", true);
                                    $(".rooms_block").removeClass('gray_bg');
                                } else {
                                    $(".rooms_block").addClass('gray_bg');
                                }
                                exhibitorslotmodal_elem.find('#exh_hero_button_url').val(exhibitor_data.exh_hero_button_url);
                                exhibitorslotmodal_elem.find('#exh_logo').val(exhibitor_data.exh_logo);
                                exhibitorslotmodal_elem.find('#exh_logo_preview').html(`<img src="${exhibitor_data.exh_logo}" class="img-fluid" alt="Exhibitor Logo" />`);

                                if (exhibitor_data.exh_banner) {
                                    exhibitorslotmodal_elem.find('#exh_banner').val(exhibitor_data.exh_banner);
                                    exhibitorslotmodal_elem.find('#exh_banner_preview').html(`<img src="${exhibitor_data.exh_banner}" class="img-fluid" alt="Exhibitor Banner" />`);
                                } else {
                                    exhibitorslotmodal_elem.find('#exh_banner').val('');
                                    exhibitorslotmodal_elem.find('#exh_banner_preview').html('');
                                }

                                exhibitorslotmodal_elem.find('#exh_hero_button_text').val(exhibitor_data.exh_hero_button_text);
                                exhibitorslotmodal_elem.find('#exh_hero_button_url').val(exhibitor_data.exh_hero_button_url);
                                exhibitorslotmodal_elem.find('#exh_external_video_room_link').val(exhibitor_data.exh_external_video_room_link);
                                exhibitorslotmodal_elem.find('#exh_room_location').val(exhibitor_data.exh_room_location);

                                if (exhibitor_data.exh_contact_email) {
                                    exhibitorslotmodal_elem.find('#exh_contact_email').val(exhibitor_data.exh_contact_email);
                                }

                                if (exhibitor_data.exh_raffles == 1 || exhibitor_data.exh_raffles == 'on') {
                                    $("#exh_raffles_yes").prop('checked', true);
                                } else {
                                    exhibitorslotmodal_elem.find('#exh_raffles_no').prop('checked', true);
                                }
                                updateraffle();
                                exhibitorslotmodal_elem.find('#exh_raffle_status').val(exhibitor_data.exh_raffle_status);
                                exhibitorslotmodal_elem.find('#exh_raffle_title').val(taoh_desc_decode(exhibitor_data.exh_raffle_title));
                                exhibitorslotmodal_elem.find('#exh_raffle_description').val(taoh_desc_decode(exhibitor_data.exh_raffle_description));
                                // alert(exhibitor_data.exh_raffles+'==='+exhibitor_data.exh_raffles_timebound_option);
                                if (exhibitor_data.exh_raffles_timebound_option == 1 || exhibitor_data.exh_raffles_timebound_option == 'on') {
                                    exhibitorslotmodal_elem.find('#exh_raffles_timebound_yes').prop('checked', true);
                                } else {
                                    exhibitorslotmodal_elem.find('#exh_raffles_timebound_no').prop('checked', true);
                                }
                                updateraffletimebound();

                                exhibitorslotmodal_elem.find('#exh_raffle_start_time').val(exhibitor_data.exh_raffle_start_time);
                                exhibitorslotmodal_elem.find('#exh_raffle_stop_time').val(exhibitor_data.exh_raffle_stop_time);

                                exhibitorslotmodal_elem.find('#exh_raffle_ques').val(v.exh_raffle_ques);
                                exhibitorslotmodal_elem.find('#exh_raffle_announce_time').val(v.exh_raffle_announce_time);
                                exhibitorslotmodal_elem.find('#local_timezoneSelect').val(v.exh_raffle_timezoneSelect);
                                exhibitorslotmodal_elem.find('#local_timezoneSelect-ts-control').val(v.exh_raffle_timezoneSelect);
                                if (typeof timeZoneInstance !== 'undefined') {
                                    timeZoneInstance.addOption({name: v.exh_raffle_timezoneSelect});
                                    timeZoneInstance.setValue(v.exh_raffle_timezoneSelect);
                                }
                                exhibitorslotmodal_elem.find('#exh_winner_profile').val(v.exh_winner_profile);
                                exhibitorslotmodal_elem.find('#exh_state').val(v.exh_state);
                                exhibitorslotmodal_elem.find('#exh_external_video_room_link').val(v.exh_external_video_room_link);
                                exhibitorslotmodal_elem.find('#exh_streaming_link').val(v.exh_streaming_link);


                                /*if(v.enable_leadgen_form == 1 ||v.enable_leadgen_form == 'on'){
                                    exhibitorslotmodal_elem.find('#enable_leadgen_form_yes').attr('checked', true);
                                    exhibitorslotmodal_elem.find('#enable_leadgen_form_no').attr('checked', false);
                                }
                                else{
                                    exhibitorslotmodal_elem.find('#enable_leadgen_form_yes').attr('checked', false);
                                    exhibitorslotmodal_elem.find('#enable_leadgen_form_no').attr('checked', true);
                                }*/


                                // if (v.enable_tao_networking == 1) {
                                //     exhibitorslotmodal_elem.find('#exh_enable_tao_networking_yes').prop('checked', true);
                                //     $("#video_conference_on_exhibit").hide();
                                //     $(".exh_streaming_link_wrapper").show();
                                // } else {
                                //     exhibitorslotmodal_elem.find('#exh_enable_tao_networking_no').prop('checked', true);
                                //     $(".exh_streaming_link_wrapper").hide();
                                //     $("#video_conference_on_exhibit").show();
                                // }
                            }

                            $('#exh_hall').empty();
                            allowed_exhibitor_halls.forEach(hall => {
                                if(!hall.id || !hall.name) return;

                                let hall_id = hall.id;
                                let hall_name = hall.name;
                                let hall_token = (hall.name); // btoa
                                var showhall = 0;
                                /* Start: check for count */
                                if (event_form_version == 2) {
                                    console.log(TicketArr, hall_name);
                                    if (is_organizer == 1) {
                                        showhall = 1;
                                    }
                                    if (TicketArr && typeof TicketArr.max_exhibits_allowed !== 'undefined' && TicketArr.max_exhibits_allowed > 0 && (TicketArr.exhibitor_halls == 'All' || TicketArr.exhibitor_halls.includes(hall_name) || TicketArr.exhibitor_halls.includes(hall_id))) {
                                        showhall = 1;
                                    }
                                } else {
                                    if (typeof eventHallAccess['exhibitor'] !== 'undefined' && typeof eventHallAccess['exhibitor'][hall_name] !== "undefined") {
                                        // console.log(exhibitor_data.exh_hall+'===='+hall_name);
                                        if (exhibitor_data.exh_hall == hall_name) {
                                            showhall = 1;
                                        }
                                        if (is_organizer == 1) {
                                            showhall = 1;
                                            if (typeof eventHallAccess['exhibitor'][hall_name]["organizer"] !== "undefined" && eventHallAccess['exhibitor'][hall_name]["organizer"]["allowed"] > 0) {
                                                showhall = 1;
                                            }
                                        }
                                        if (sponsor_type != '' && sponsor_type != undefined) {
                                            if (typeof eventHallAccess['exhibitor'][hall_name][sponsor_type] !== "undefined" && eventHallAccess['exhibitor'][hall_name][sponsor_type]["allowed"] > 0) {
                                                showhall = 1;
                                            }
                                        }
                                        if (user_profile_type != '') {
                                            if (typeof eventHallAccess['exhibitor'][hall_name][user_profile_type] !== "undefined" && eventHallAccess['exhibitor'][hall_name][user_profile_type]["allowed"] > 0) {
                                                showhall = 1;
                                            }
                                        }
                                        if (rsvp_sponsor_title != '') {
                                            if (typeof eventHallAccess['exhibitor'][hall_name][rsvp_sponsor_title] !== "undefined" && eventHallAccess['exhibitor'][hall_name][rsvp_sponsor_title]["allowed"] > 0) {
                                                showhall = 1;
                                            }
                                        }
                                    }
                                }
                                /* End: check for count */
                                if (showhall == 1) {
                                    hall_exist = 1;
                                    let hall_option = `<option value="${hall_token}">${hall_name}</option>`;
                                    $('#exh_hall').append(hall_option);
                                }
                            });
                            if (hall_exist === 0) {
                                taoh_set_error_message('No Exhibitor Hall available for setup');
                                return;
                            }
                            exhibitorslotmodal_elem.find('#exh_hall').val(exhibitor_data.exh_hall);

                            if (exhibitor_data.sponsor_id != '') {
                                $(".lead-raffle").show();
                                $("#download_raffle").attr('href', '<?php echo TAOH_SITE_URL_ROOT ?>/events/export_raffle_entries/?eventtoken=' + eventToken + '&exh=' + exh_id);
                            } else {
                                $(".lead-raffle").hide();
                            }
                            loadSponsorTypeDropdown(exh_id,conttoken_data,event_organizer_ptokens);

                            console.log('exhibitor_data', exhibitor_data);
                            
                        });

                    } else {
                        // let exh_ptoken = $('#ptoken').val();
                        let exh_title = $('#exhi_title_' + exh_id).html();
                        let exh_sponsor_type = $('#exhi_sponsor_type_' + exh_id).html();
                        //alert('----bbbbbbbbbb-------' + exh_sponsor_type);
                        let exh_sponsor_owner = $('#exhi_owner_' + exh_id).html();
                        //alert(exh_sponsor_owner)
                        let exh_description = $('#exhi_description_' + exh_id).html();
                        let exh_link = $('#exhi_link_' + exh_id).html();
                        let exh_logo = $('#exhi_logo_' + exh_id).attr('src');
                        let exh_display_type = $('#exhi_display_type_' + exh_id).html();

                        exhibitorslotmodal_elem.find('#ptoken').val(exh_sponsor_owner);
                        exhibitorslotmodal_elem.find('#sponsor_id').val(exh_id);
                        exhibitorslotmodal_elem.find('#display_type').val(exh_display_type);
                        exhibitorslotmodal_elem.find('#exh_session_title').val(taoh_desc_decode(exh_title));
                        exhibitorslotmodal_elem.find('#exh_description').val(taoh_desc_decode(exh_description));
                        exhibitorslotmodal_elem.find('#exh_hero_button_url').val(exh_link);
                        exhibitorslotmodal_elem.find('#exh_logo').val(exh_logo);
                        exhibitorslotmodal_elem.find('#exh_logo_preview').html(`<img src="${exh_logo}" class="img-fluid" alt="Exhibitor Logo" />`);

                        $('#exh_hall').empty();
                        allowed_exhibitor_halls.forEach(hall => {
                            if(!hall.id || !hall.name) return;

                            let hall_id = hall.id;
                            let hall_name = hall.name;
                            let hall_token = (hall.name); // btoa
                            var showhall = 0;

                            /* Start: check for count */
                            if (event_form_version == 2) {
                                if (is_organizer == 1) {
                                    showhall = 1;
                                }
                                if (TicketArr && typeof TicketArr.max_exhibits_allowed !== 'undefined' && TicketArr.max_exhibits_allowed > 0 && (TicketArr.exhibitor_halls == 'All' || TicketArr.exhibitor_halls.includes(hall_name) || TicketArr.exhibitor_halls.includes(hall_id))) {
                                    showhall = 1;
                                }
                            } else {
                                if (typeof eventHallAccess['exhibitor'] !== 'undefined' && typeof eventHallAccess['exhibitor'][hall_name] !== "undefined") {
                                    // console.log(exhibitor_data.exh_hall+'===='+hall_name);
                                    if (is_organizer == 1) {
                                        showhall = 1;
                                        if (typeof eventHallAccess['exhibitor'][hall_name]["organizer"] !== "undefined" && eventHallAccess['exhibitor'][hall_name]["organizer"]["allowed"] > 0) {
                                            showhall = 1;
                                        }
                                    }
                                    if (sponsor_type != '' && sponsor_type != undefined) {
                                        if (typeof eventHallAccess['exhibitor'][hall_name][sponsor_type] !== "undefined" && eventHallAccess['exhibitor'][hall_name][sponsor_type]["allowed"] > 0) {
                                            showhall = 1;
                                        }
                                    }
                                    if (user_profile_type != '') {
                                        if (typeof eventHallAccess['exhibitor'][hall_name][user_profile_type] !== "undefined" && eventHallAccess['exhibitor'][hall_name][user_profile_type]["allowed"] > 0) {
                                            showhall = 1;
                                        }
                                    }
                                    if (rsvp_sponsor_title != '') {
                                        if (typeof eventHallAccess['exhibitor'][hall_name][rsvp_sponsor_title] !== "undefined" && eventHallAccess['exhibitor'][hall_name][rsvp_sponsor_title]["allowed"] > 0) {
                                            showhall = 1;
                                        }
                                    }
                                }
                            }
                            /* End: check for count */
                            if (showhall == 1) {
                                hall_exist = 1;
                                let hall_option = `<option value="${hall_token}">${hall_name}</option>`;
                                $('#exh_hall').append(hall_option);
                            }

                        });
                        loadSponsorTypeDropdown(exh_id,conttoken_data,event_organizer_ptokens);
                        if (hall_exist === 0) {
                            taoh_set_error_message('No Exhibitor Hall available for setup');
                            return;
                        }
                    
                        // if (exh_ptoken) {
                        //     const exh_contact_info = await ft_getUserInfo(exh_ptoken, 'full');
                        //     if (exh_contact_info?.email?.trim()) {
                        //         exhibitorslotmodal_elem.find('#exh_contact_email').val(exh_contact_info?.email);
                        //     }
                        // }
                    }
                    $('label[for="exh_contact_email"] .text-danger').css('display', ((is_organizer == 1) ? 'none' : 'inline-block'));

                       

                    $('#exhibitorSlotModal').modal('show');
                }).catch(error => console.error("Error fetching event info:", error));
        }
    }, 1000);

    });

    function loadSponsorTypeDropdown(exh_id,conttoken_data,event_organizer_ptokens){

        let exh_sponsor_type = $('#exhi_sponsor_type_' + exh_id).html();
        const exh_sponsor_levels = $('#exh_sponsor_levels');

        if (event_organizer_ptokens.includes(my_pToken)) {
                const sponsor_levels = conttoken_data.event_sponsor_levels || [];
                console.log('sponsor_levels', sponsor_levels);
                if (sponsor_levels.length > 0) {
                    exh_sponsor_levels.empty();
                     exh_sponsor_levels.append(new Option('select', '', false, false));
                    sponsor_levels.forEach(level => {
                        var spo_title = taoh_desc_decode(level.title);
                        if(exh_sponsor_type == level.title || exh_sponsor_type == spo_title){
                            
                            exh_sponsor_levels.append(new Option(spo_title, spo_title, true, true));
                        }else{
                            exh_sponsor_levels.append(new Option(spo_title,spo_title, false, false));
                        }
                    });

                    $('.exh_sponsor_levels_wrapper').show();
                } else {
                    exh_sponsor_levels.empty();
                    $('.exh_sponsor_levels_wrapper').hide();
                    
                }
            } else {
                exh_sponsor_levels.empty();
                $('.exh_sponsor_levels_wrapper').hide();
            }
    }

    $(document).on('click', '.delete-exhibitor', async function () {
        let meta_id = $(this).data('id');
        let meta_type = $(this).data('type');

        if (meta_id) {
            taoh_set_warning_message('Do you want to delete this exhibitor?', false, 'toast-middle', [
                {
                    text: 'Yes',
                    action: () => {
                        jQuery.post(_taoh_site_ajax_url, {
                            'taoh_action': 'delete_event_meta',
                            'token': _taoh_ajax_token,
                            'eventtoken': eventToken,
                            'meta_id': meta_id,
                            'meta_type': meta_type
                        }, function (response) {
                            if(response.success){
                                taoh_set_success_message('Exhibitor slot deleted successfully', false);
                                let event_meta_key = `event_MetaInfo_${eventToken}`;
                                IntaoDB.removeItem(objStores.event_store.name,  event_meta_key);
                                location.reload();
                            }
                        });
                    },
                    class: 'dojo-v1-btn float-right mt-3 mb-3'
                },
                {
                    text: 'No',
                    action: () => {

                    },
                    class: 'dojo-v1-btn float-right mt-3 mb-3 mr-2'
                }
            ]);
        }
    });

    $(document).on('click', '.more_info', function () {
        if (isLoggedIn) {
            click_view = 'view';
            save_metrics('exhibitor', click_view, eventToken+"-"+$(this).data('exhid'));
        }
    });

</script>