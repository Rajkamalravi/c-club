<link rel="stylesheet" href="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/css/events-exhibitor-form.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>">

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

<script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/js/events-exhibitor-form.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
