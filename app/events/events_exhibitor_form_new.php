<style>
    .exhibitor-form {
        white-space: nowrap;
    }
    .exhibitor-form-header:after {
        display: block;
        content: "";
        height: 3px;
        background-color: #000;
        margin-top: 10px;
    }
    a.exhibitor-form svg {
        margin-left: 8px;
        margin-bottom: 2px;
    }
    a.exhibitor-form[aria-expanded="true"] svg {
        transform: rotate(180deg);
    }
</style>

<div class="modal exhibitor-slot fade" id="exhibitorSlotModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="padding-left: 12px;">
    <div class="modal-dialog bg-white" role="document">
        <div class="modal-content" id="ExhibitorFormTooltip" style="display:none;overflow-y:auto;">
            <button type="button" style="text-align:right" class="btn" onclick="closeExhibitorFormTooltip();">
                <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.6153 2.22013C13.1228 1.71256 13.1228 0.888255 12.6153 0.380681C12.1077 -0.126894 11.2834 -0.126894 10.7758 0.380681L6.5 4.66055L2.22013 0.384741C1.71256 -0.122833 0.888255 -0.122833 0.380681 0.384741C-0.126894 0.892316 -0.126894 1.71662 0.380681 2.22419L4.66055 6.5L0.384742 10.7799C-0.122833 11.2874 -0.122833 12.1117 0.384742 12.6193C0.892316 13.1269 1.71662 13.1269 2.22419 12.6193L6.5 8.33945L10.7799 12.6153C11.2874 13.1228 12.1117 13.1228 12.6193 12.6153C13.1269 12.1077 13.1269 11.2834 12.6193 10.7758L8.33945 6.5L12.6153 2.22013Z" fill="#D3D3D3"></path>
                </svg>
            </button>

            <img alt="Exhibitor Form Tooltip" style="width:100%; height:auto;overflow-y:auto;" src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/exhibitor_tooltip.jpg';?>"/>

        </div>

        <div class="modal-content exhibitor-form-outer" id="ExhibitorFormContent">
            <div class="modal-header bg-white align-items-center" style="border: none;">
                <h3 style="font-size: 21px; width: 50%;" class="exhibitor-form-header">Exhibitor Form</h3>
                <div class="justify-content-end">
                    <button type="button" class="btn" data-dismiss="modal" aria-label="Close">
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6153 2.22013C13.1228 1.71256 13.1228 0.888255 12.6153 0.380681C12.1077 -0.126894 11.2834 -0.126894 10.7758 0.380681L6.5 4.66055L2.22013 0.384741C1.71256 -0.122833 0.888255 -0.122833 0.380681 0.384741C-0.126894 0.892316 -0.126894 1.71662 0.380681 2.22419L4.66055 6.5L0.384742 10.7799C-0.122833 11.2874 -0.122833 12.1117 0.384742 12.6193C0.892316 13.1269 1.71662 13.1269 2.22419 12.6193L6.5 8.33945L10.7799 12.6153C11.2874 13.1228 12.1117 13.1228 12.6193 12.6153C13.1269 12.1077 13.1269 11.2834 12.6193 10.7758L8.33945 6.5L12.6153 2.22013Z" fill="#D3D3D3"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <form action="<?= taoh_site_ajax_url(1); ?>" method="post" name="setup_exhibitor_slot_form" id="setup_exhibitor_slot_form" enctype="multipart/form-data">
                    <input type="hidden" name="taoh_action" id="taoh_action" value="save_exhibitor_slot">
                    <input type="hidden" name="eventtoken" id="eventtoken" value="<?= $eventtoken ?? ''; ?>">
                    <input type="hidden" name="ptoken" id="ptoken" value="<?= $ptoken ?? ''; ?>">
                    <input type="hidden" name="sponsor_id" id="sponsor_id" data-dynamic="1">
                    <input type="hidden" name="display_type" id="display_type">
                    <input type="hidden" name="exhibitor_id" id="exhibitor_id" data-dynamic="1">
                    <input type="hidden" name="country_locked" value="<?= $country_locked ?? 0; ?>">

                    <div class="form-group col-lg-7 exh_sponsor_levels_wrapper" style="display: none">
                        <label for="exh_sponsor_levels">Sponsor Levels <span class="text-danger">*</span></label>
                        <select name="sponsor_type" id="exh_sponsor_levels" class="form-control"></select>
                    </div>

                    <div class="row p-4">
                        <div class="col-sm-2">
                            <p>
                                <a class="exhibitor-form" data-toggle="collapse" href="#exhibitorInfoCollapse" role="button" aria-expanded="true" aria-controls="exhibitorInfoCollapse">
                                    Exhibitor Info <span class="text-danger">*</span> <i class="fa fa-info-circle ml-2 cursor-pointer" style="color: #15a4f7; font-size: 16px;" title="View details" onclick="OpenExhibitorFormTooltip();"></i>
                                    <svg style="min-width: fit-content;" width="14" height="10" viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.29399 9.17178C6.68448 9.60983 7.31864 9.60983 7.70914 9.17178L13.7071 2.44337C14.0976 2.00532 14.0976 1.29393 13.7071 0.85588C13.3166 0.417832 12.6825 0.417832 12.292 0.85588L7 6.7923L1.70802 0.859384C1.31753 0.421336 0.683365 0.421336 0.292871 0.859384C-0.0976236 1.29743 -0.0976236 2.00882 0.292871 2.44687L6.29086 9.17529L6.29399 9.17178Z" fill="black"></path>
                                    </svg>
                                </a>
                            </p>
                        </div>
                        <div class="col-sm-10 collapse show" id="exhibitorInfoCollapse">
                            <div class="card card-body border-0 py-0">
                                <div class="mb-3 row">
                                    <label for="exh_session_title" class="col-sm-2 col-form-label">Title <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="exh_session_title" id="exh_session_title" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="exh_subtitle" class="col-sm-2 col-form-label">Subtitle</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="exh_subtitle" id="exh_subtitle">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="exh_description" class="col-sm-2 col-form-label">Description <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <textarea name="exh_description" id="exh_description" class="no-resize" style="resize: none;" required></textarea>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="exh_hall" class="col-sm-2 col-form-label">Associated Hall <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <select name="exh_hall[]" id="exh_hall" multiple class="select2 form-control hall-field" required>

                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="exh_logo_upload" class="col-sm-2 col-form-label text-nowrap">Logo / Banner <span class="text-danger">*</span></label>
                                    <div class="col-sm-5">
                                        <input type="file" class="form-control" name="exh_logo_upload" id="exh_logo_upload" placeholder="Size 300 x 300">
                                        <input type="hidden" name="exh_logo" id="exh_logo">
                                        <div id="exh_logo_preview"></div>
                                    </div>
                                    <div class="col-sm-5">
                                        <input type="file" class="form-control" name="exh_banner_upload" id="exh_banner_upload" placeholder="Size 1920 x 1080">
                                        <input type="hidden" name="exh_banner" id="exh_banner">
                                        <div id="exh_banner_preview"></div>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="exh_hero_button_text" class="col-sm-2 col-form-label">Hero Button Text</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="exh_hero_button_text" id="exh_hero_button_text">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="exh_hero_button_url" class="col-sm-2 col-form-label">Hero Button URL</label>
                                    <div class="col-sm-10">
                                        <input type="url" class="form-control" name="exh_hero_button_url" id="exh_hero_button_url">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="exh_contact_email" class="col-sm-2 col-form-label">Contact Us Email <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" name="exh_contact_email" id="exh_contact_email">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row p-4" id="raffle_slot">
                        <div class="col-sm-2">
                            <p>
                                <a class="exhibitor-form" data-toggle="collapse" href="#exhibitorRaffleCollapse" role="button" aria-expanded="false" aria-controls="exhibitorRaffleCollapse">
                                    Exhibitor Raffle <i class="fa fa-info-circle ml-2 cursor-pointer" style="color: #15a4f7; font-size: 16px;" title="View details" onclick="OpenExhibitorFormTooltip();"></i>
                                    <svg style="min-width: fit-content;" width="14" height="10" viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.29399 9.17178C6.68448 9.60983 7.31864 9.60983 7.70914 9.17178L13.7071 2.44337C14.0976 2.00532 14.0976 1.29393 13.7071 0.85588C13.3166 0.417832 12.6825 0.417832 12.292 0.85588L7 6.7923L1.70802 0.859384C1.31753 0.421336 0.683365 0.421336 0.292871 0.859384C-0.0976236 1.29743 -0.0976236 2.00882 0.292871 2.44687L6.29086 9.17529L6.29399 9.17178Z" fill="black"></path>
                                    </svg>
                                </a>
                            </p>
                        </div>
                        <div class="col-sm-10 collapse" id="exhibitorRaffleCollapse">
                            <div class="card card-body border-0 py-0">
                                <div class="mb-3 row">
                                    <label for="exh_raffles" class="col-sm-2 col-form-label">Do you want to drop raffles?</label>
                                    <div class="col-sm-10">
                                        <div class="d-flex align-items-center mt-2 mt-lg-3" style="gap:30px;">
                                            <div>
                                                <input type="radio" name="exh_raffles" id="exh_raffles_yes" value="1" onchange="updateraffle()">
                                                <label for="exh_raffles_yes" class="ml-1 mb-0">YES</label>
                                            </div>

                                            <div>
                                                <input type="radio" name="exh_raffles" id="exh_raffles_no" value="0" checked onchange="updateraffle()">
                                                <label for="exh_raffles_no" class="ml-1 mb-0">NO</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="exh_raffle_options" style="display: none;">
                                    <div class="mb-3 row">
                                        <label for="exh_raffle_title" class="col-sm-2 col-form-label">Raffle Title <span class="text-danger">*</span></label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="exh_raffle_title" id="exh_raffle_title" placeholder="Raffle Title" required>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="exh_raffle_description" class="col-sm-2 col-form-label">Raffle Description <span class="text-danger">*</span></label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="exh_raffle_description" id="exh_raffle_description" placeholder="Raffle Description" required>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="exh_raffle_ques" class="col-sm-2 col-form-label">Raffle Question</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="exh_raffle_ques" id="exh_raffle_ques" placeholder="Raffle Question">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="exh_raffle_announce_time" class="col-sm-2 col-form-label">Raffle Announcement Time <br /><span id="raffle_timeslot_timezone_txt" class="text-muted" style="font-size: 12px">in America/New_York</span></label>
                                        <div class="col-sm-5">
                                            <input type="datetime-local" class="form-control" name="exh_raffle_announce_time" id="exh_raffle_announce_time" placeholder="Announcement Time" required>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="exh_winner_profile" class="col-sm-2 col-form-label">Rafffle Winner Profile</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="exh_winner_profile" id="exh_winner_profile" placeholder="Type to winner profile (ptoken)">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="exh_raffles" class="col-sm-2 col-form-label">Do you want the raffle to be timebound?</label>
                                        <div class="col-sm-10">
                                            <div class="d-flex align-items-center mt-2 mt-lg-3" style="gap:30px;">
                                                <div>
                                                    <input type="radio" name="exh_raffles_timebound_option" id="exh_raffles_timebound_yes" value="1" onchange="updateraffletimebound()">
                                                    <label for="exh_raffles_timebound_yes" class="ml-1 mb-0">YES</label>
                                                </div>

                                                <div>
                                                    <input type="radio" name="exh_raffles_timebound_option" id="exh_raffles_timebound_no" value="0" checked onchange="updateraffletimebound()">
                                                    <label for="exh_raffles_timebound_no" class="ml-1 mb-0">NO</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 row" id="exh_raffle_time_bound_time" style="display: none;">
                                        <label for="exh_raffle_start_time" class="col-sm-2 col-form-label">TimeSlot <span class="text-danger">*</span> <br /><span id="exh_raffle_timeslot_timezone_txt" class="text-muted" style="font-size: 12px"></span></label>
                                        <div class="col-sm-5">
                                            <input type="datetime-local" class="form-control" name="exh_raffle_start_time" id="exh_raffle_start_time" placeholder="From" required>
                                        </div>
                                        <div class="col-sm-5">
                                            <input type="datetime-local" class="form-control" name="exh_raffle_stop_time" id="exh_raffle_stop_time" placeholder="To" required>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="exh_raffle_status" class="col-sm-2 col-form-label">Raffle Status <span class="text-danger">*</span></label>
                                        <div class="col-sm-10">
                                            <select name="exh_raffle_status" id="exh_raffle_status" class="form-control">
                                                <option value="">Select Raffle Status</option>
                                                <option value="open">Open</option>
                                                <option value="closed">Closed</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row lead-raffle" style="display:none">
                                        <div class="col-12 my-2">
                                            <a target="_blank" class="d-v2-btn" href="javascript:void(0)" id="download_raffle">
                                                <i class="fa fa-download"></i>
                                                <span>Download Raffle Entries</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row p-4">
                        <div class="col-sm-2">
                            <p>
                                <a class="exhibitor-form" data-toggle="collapse" href="#exhibitorStateCollapse" role="button" aria-expanded="false" aria-controls="exhibitorStateCollapse">
                                    Exhibitor State <span class="text-danger">*</span> <i class="fa fa-info-circle ml-2 cursor-pointer" style="color: #15a4f7; font-size: 16px;" title="View details" onclick="OpenExhibitorFormTooltip();"></i>
                                    <svg style="min-width: fit-content;" width="14" height="10" viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.29399 9.17178C6.68448 9.60983 7.31864 9.60983 7.70914 9.17178L13.7071 2.44337C14.0976 2.00532 14.0976 1.29393 13.7071 0.85588C13.3166 0.417832 12.6825 0.417832 12.292 0.85588L7 6.7923L1.70802 0.859384C1.31753 0.421336 0.683365 0.421336 0.292871 0.859384C-0.0976236 1.29743 -0.0976236 2.00882 0.292871 2.44687L6.29086 9.17529L6.29399 9.17178Z" fill="black"></path>
                                    </svg>
                                </a>
                            </p>
                        </div>
                        <div class="col-sm-10 collapse" id="exhibitorStateCollapse">
                            <div class="card card-body border-0 py-0">
                                <div class="mb-3 row">
                                    <label for="exh_state" class="col-sm-2 col-form-label">Status <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <select name="exh_state" id="exh_state" class="form-control" required>
                                            <option value="active">Active</option>
                                            <option value="live">Live</option>
                                            <option value="suspended">Suspended</option>
                                            <option value="closed">Closed</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="exh_tags" class="col-sm-2 col-form-label">Associated Tags</label>
                                    <div class="col-sm-10">
                                        <select name="exh_tags[]" id="exh_tags" multiple class="select2 form-control tags-field">

                                        </select>
                                    </div>
                                </div>

                                <!--<div class="mb-3 row">
                                    <label for="exh_tags" class="col-sm-2 col-form-label">Enable TAO Powered Networking Room with this Session</label>
                                    <div class="col-sm-10">
                                        <div class="d-flex align-items-center mt-2 mt-lg-3" style="gap:30px;">
                                            <div>
                                                <input type="radio" name="enable_tao_networking" id="exh_enable_tao_networking_yes" value="1" checked>
                                                <label for="exh_enable_tao_networking_yes" class="ml-1 mb-0">YES</label>
                                            </div>

                                            <div>
                                                <input type="radio" name="enable_tao_networking" id="exh_enable_tao_networking_no" value="0">
                                                <label for="exh_enable_tao_networking_no" class="ml-1 mb-0">NO</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>-->

                                <div class="mb-3 row exh_streaming_link_wrapper">
                                    <label for="exh_streaming_link" class="col-sm-2 col-form-label">Streaming Link</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="exh_streaming_link" id="exh_streaming_link" class="form-control" placeholder="Video conference link URL">
                                        <small class="form-text text-muted" style="font-size:12px">Supports: YouTube (watch/shorts/youtu.be/embed/playlist), GMeet, Jitsi, Vimeo, Twitch (video/channel/clip), Dailymotion, Loom, Wistia and other meeting link</small>
                                    </div>
                                </div>

                                <!--<div class="mb-3 row exh_external_video_room" style="display:none;">
                                    <label for="exh_external_video_room_link" class="col-sm-2 col-form-label">Video URL <small class="text-muted">[if not hosted by TAO] </small>.</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="exh_external_video_room_link" id="exh_external_video_room_link" class="form-control" placeholder="Video conference link URL">
                                    </div>
                                </div>-->

                                <div class="mb-3 row">
                                    <label for="exh_room_location" class="col-sm-2 col-form-label">Enter the location here, if applicable.</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="exh_room_location" id="exh_room_location" class="form-control" placeholder="Location">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2 row p-4">
                        <div class="col-12">
                            <button type="submit" name="exh_submit" id="exh_submit" class="btn btn-submit"><i class="fa-solid fa-hourglass-half" style="font-size: 20px; margin-right: 10px;color: #fff"></i> Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
	</div>
</div>

<script>
    let event_exhibitor_cache = [];

    function OpenExhibitorFormTooltip(){
        $("#ExhibitorFormContent").hide();
        $("#ExhibitorFormTooltip").show();
    }

     function closeExhibitorFormTooltip(){
        $("#ExhibitorFormContent").show();
        $("#ExhibitorFormTooltip").hide();
    }

    $(document).ready(function(){
        $(".hall-field").select2({width: '100%'});

        // $('#setup_exhibitor_slot_form input[name="enable_tao_networking"]').on('change', function () {
        //     if($('#setup_exhibitor_slot_form input[name="enable_tao_networking"]:checked').val() == 1){
        //         $("#exh_external_video_room_link").val('');
        //         $("#video_conference_on_exhibit").hide();
        //         $(".exh_streaming_link_wrapper").show();
        //     } else{
        //         $("#exh_streaming_link").val('');
        //         $(".exh_streaming_link_wrapper").hide();
        //         $("#video_conference_on_exhibit").show();
        //     }
        // });

        _getEventMetaInfo({ eventtoken: eventToken }, true)
            .then(({ requestData, response }) => {
                event_exhibitor_cache = (response && response.output && response.output.event_exhibitor) || [];
            })
            .catch(err => {
                console.error('Failed to load event exhibitor meta info:', err);
                event_exhibitor_cache = [];
            });

        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param * 1000000)
        }, 'File size must be less than {0} MB');

        $.validator.addMethod('unique_exhibitor_title', function (value, element) {
            if (this.optional(element)) return true; // let required handle empty

            const title = (value || '').toLowerCase().trim();
            const exhToken = $('#setup_exhibitor_slot_form #exhibitor_id').val() || '';

            let is_unique = true;

            event_exhibitor_cache.forEach(function (exhibitor) {
                if (!exhibitor || !exhibitor.exh_session_title) return;

                const existingTitle = exhibitor.exh_session_title.toLowerCase().trim();
                const existingId    = String(exhibitor.ID);

                // same title but different ID => not unique
                if (existingTitle === title && existingId !== String(exhToken)) {
                    is_unique = false;
                }
            });

            return is_unique;
        }, 'This title is already taken. Please use another title');

        $('#setup_exhibitor_slot_form').validate({
            rules: {
                exh_session_title: {
                    required: true,
                    unique_exhibitor_title: true
                },
                exh_subtitle: {
                    required: false
                },
                exh_description: {
                    required: true
                },
                'exh_hall[]': {
                    required: true
                },
                exh_logo_upload: {
                    required: function () {
                        return ($('#exh_logo_upload').val() === '') && ($('#exh_logo').val() === '');
                    },
                    extension: "jpg|jpeg|png",
                    filesize: 5
                },
                exh_banner_upload: {
                    required: function () {
                        return false; // ($('#exh_banner_upload').val() === '') && ($('#exh_banner').val() === '');
                    },
                    extension: "jpg|jpeg|png",
                    filesize: 5
                },
                exh_hero_button_text: {
                    required: false,
                    // maxlength: 20,
                    minlength: 2
                },
                exh_hero_button_url: {
                    required: false,
                    url: true
                },
                exh_raffle_status: {
                    required: true,
                },
                exh_raffle_title: {
                    required: true,
                    // maxlength: 150,
                    minlength: 3
                },
                exh_raffle_description: {
                    required: true,
                    // maxlength: 500,
                },
                exh_raffle_start_time: {
                    required: function () {
                        return $('input[name="exh_raffles_timebound_option"]:checked').val() === '1';
                    }
                },
                exh_raffle_stop_time: {
                    required: function () {
                        return $('input[name="exh_raffles_timebound_option"]:checked').val() === '1';
                    }
                },
                exh_contact_email: {
                    required: function () {
                        return $("#is_organizer").val() != 1;
                    },
                    email: true
                }
            },
            messages: {
                exh_session_title: {
                    required: "Title is required"
                },
                exh_subtitle: {
                    required: "Subtitle is required"
                },
                exh_description: {
                    required: "Description is required"
                },
                'exh_hall[]': {
                    required: "Associated Hall is required"
                },
                exh_logo_upload: {
                    required: "Logo image is required",
                    extension: "Only JPG, JPEG, or PNG files are allowed."
                },
                exh_banner_upload: {
                    required: "Banner image is required",
                    extension: "Only JPG, JPEG, or PNG files are allowed."
                },
                exh_hero_button_text: {
                    required: "Button text is required"
                },
                exh_hero_button_url: {
                    required: "Button URL is required"
                },
                exh_raffle_status: {
                    required: "Raffle Status is required"
                },
                exh_raffle_title: {
                    required: "Raffle Title is required"
                },
                exh_raffle_description: {
                    required: "Raffle Description is required"
                },
                exh_raffle_start_time: {
                    required: "Raffle Start time is required"
                },
                exh_raffle_stop_time: {
                    required: "Raffle Stop time is required"
                },
                exh_contact_email: {
                    required: "Contact Email is required",
                }
            },
            errorPlacement: function (error, element) {
                if (element.is(":checkbox")) {
                    error.appendTo(element.parent());
                } else if (typeof element.data('error_id') !== 'undefined' && element.data('error_id') !== false) {
                    $(element.data('error_id')).html(error);
                } else if (element.hasClass('select2-hidden-accessible')) {
                    error.insertAfter(element.siblings('.select2'));
                } else {
                    element.after(error);
                }
            },
            submitHandler: function (form) {
                const is_organizer = $("#is_organizer").val();

                let setup_exhibitor_slot_form = $('#setup_exhibitor_slot_form');
                let formData = new FormData(form);
                formData.append('is_organizer', is_organizer);

                let submit_btn = setup_exhibitor_slot_form.find('button[type="submit"]');
                submit_btn.prop('disabled', true);

                let submit_btn_icon = submit_btn.find('i');
                submit_btn_icon.removeClass('fa-save').addClass('fa-spinner fa-spin');

                $.ajax({
                    url: setup_exhibitor_slot_form.attr('action'),
                    type: 'post',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function (response) {
                        if (response.success) {
                            delete_events_meta_into(eventtoken);
                            delete_events_into('event_details_sponsor_' + eventtoken);
                            delete_events_into('event_MetaInfo_' + eventtoken);
                            form.reset();
                            $('#exh_logo_preview').empty();
                            $('#exh_banner_preview').empty();
                            submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                            submit_btn.prop('disabled', false);
                            $('#exhibitorSlotModal').modal('hide');
                            taoh_set_success_message('Exhibitor slot added successfully', false);
                            location.reload();
                        } else {
                            submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                            submit_btn.prop('disabled', false);
                            taoh_set_error_message('Failed to process your data! Try Again', false);
                        }
                        // loader(false, $("#addexh_loaderArea"));
                    },
                    error: function (xhr, status, error) {
                        console.log('Error:', xhr.status);
                        submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                        submit_btn.prop('disabled', false);
                    }
                });

            }
        });

        $(document).on('change', '#exh_logo_upload', function(e) {
            let file = e.target.files[0];
            const maxSize = 1024 * 1024; // 1MB in bytes

            if (file && file.size > maxSize) {
                taoh_set_error_message("File size exceeds 1MB limit!",false);
                this.value = ''; // Clear the input
            }else{
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#exh_logo_preview').html(`<img src="${e.target.result}" class="img-fluid" alt="Exhibitor Logo" />`);
                }
                reader.readAsDataURL(file);
            }
        });

        $(document).on('change', '#exh_banner_upload', function(e) {
            let file = e.target.files[0];
            const maxSize = 1024 * 1024; // 1MB in bytes

            if (file && file.size > maxSize) {
                taoh_set_error_message("File size exceeds 1MB limit!",false);
                this.value = ''; // Clear the input
            }else{
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#exh_banner_preview').html(`<img src="${e.target.result}" class="img-fluid" alt="Exhibitor Banner" />`);
                }
                reader.readAsDataURL(file);
            }
        });
    });

    $('#exhibitorSlotModal').on('show.bs.modal', () => {
          $("#ExhibitorFormContent").show();
        $("#ExhibitorFormTooltip").hide();
    });

</script>