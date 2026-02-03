<link rel="stylesheet" href="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/events/css/events-session-form.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>">

<div class="modal speaker-slot fade" id="speakerSlotModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog bg-white" role="document">
        <div class="modal-content session-form-outer">
            <div class="modal-header bg-white align-items-center " style="border: none;">
                <h3 style="font-size: 21px; width: 50%;" class="session-form-header">Session Form</h3>
                <div class="justify-content-end">
                    <button type="button" class="btn" data-dismiss="modal" aria-label="Close">
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6153 2.22013C13.1228 1.71256 13.1228 0.888255 12.6153 0.380681C12.1077 -0.126894 11.2834 -0.126894 10.7758 0.380681L6.5 4.66055L2.22013 0.384741C1.71256 -0.122833 0.888255 -0.122833 0.380681 0.384741C-0.126894 0.892316 -0.126894 1.71662 0.380681 2.22419L4.66055 6.5L0.384742 10.7799C-0.122833 11.2874 -0.122833 12.1117 0.384742 12.6193C0.892316 13.1269 1.71662 13.1269 2.22419 12.6193L6.5 8.33945L10.7799 12.6153C11.2874 13.1228 12.1117 13.1228 12.6193 12.6153C13.1269 12.1077 13.1269 11.2834 12.6193 10.7758L8.33945 6.5L12.6153 2.22013Z" fill="#D3D3D3"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <form action="<?= taoh_site_ajax_url(1); ?>" method="post" name="spk_form" id="spk_form" enctype="multipart/form-data">
                    <input type="hidden" name="taoh_action" id="taoh_action" value="event_save_speaker">
                    <input type="hidden" name="eventtoken" id="eventtoken" value="<?= $eventtoken ?? ''; ?>">
                    <input type="hidden" name="ptoken" id="ptoken" value="<?= $ptoken ?? ''; ?>">
                    <input type="hidden" name="speaker_id" id="speaker_id" data-dynamic="1">
                    <input type="hidden" name="country_locked" value="<?= $country_locked ?? 0; ?>">
                    <input type="hidden" name="spk_timezoneSelect" id="local_timezoneSelect_session" value="" data-dynamic="1">

                    <div class="row p-4">
                        <div class="col-sm-2">
                            <p>
                                <a class="session-form" data-toggle="collapse" href="#sessionInfoCollapse" role="button" aria-expanded="true" aria-controls="sessionInfoCollapse">
                                    Session Info <span class="text-danger">*</span>
                                    <svg style="min-width: fit-content;" width="14" height="10" viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.29399 9.17178C6.68448 9.60983 7.31864 9.60983 7.70914 9.17178L13.7071 2.44337C14.0976 2.00532 14.0976 1.29393 13.7071 0.85588C13.3166 0.417832 12.6825 0.417832 12.292 0.85588L7 6.7923L1.70802 0.859384C1.31753 0.421336 0.683365 0.421336 0.292871 0.859384C-0.0976236 1.29743 -0.0976236 2.00882 0.292871 2.44687L6.29086 9.17529L6.29399 9.17178Z" fill="black"></path>
                                    </svg>
                                </a>
                            </p>
                        </div>
                        <div class="col-sm-10 collapse show" id="sessionInfoCollapse">
                            <div class="card card-body border-0 py-0">
                                <div class="mb-3 row">
                                    <label for="spk_title" class="col-sm-2 col-form-label">Title <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="spk_title" id="spk_title" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="spk_sdesc" class="col-sm-2 col-form-label">Subtitle <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="spk_sdesc" id="spk_sdesc" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="spk_desc" class="col-sm-2 col-form-label">Description <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <textarea name="spk_desc" id="spk_desc" class="no-resize" style="resize: none;" required></textarea>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="spk_datefrom" class="col-sm-2 col-form-label">TimeSlot <span class="text-danger">*</span> <br /><span id="spk_timeslot_timezone_txt" class="text-muted" style="font-size: 12px"></span></label>
                                    <div class="col-sm-5">
                                        <input type="datetime-local" class="form-control" name="spk_datefrom" id="spk_datefrom" placeholder="From" required>
                                    </div>
                                    <div class="col-sm-5">
                                        <input type="datetime-local" class="form-control" name="spk_dateto" id="spk_dateto" placeholder="To" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="spk_hero_button_text" class="col-sm-2 col-form-label">Hero Button Text <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="spk_hero_button_text" id="spk_hero_button_text" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="spk_hero_button_url" class="col-sm-2 col-form-label">Hero Button URL <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="url" class="form-control" name="spk_hero_button_url" id="spk_hero_button_url" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="spk_logo_upload" class="col-sm-2 col-form-label">Logo / Banner</label>
                                    <div class="col-sm-5">
                                        <input type="file" class="form-control" name="spk_logo_upload" id="spk_logo_upload" placeholder="Size 300 x 300">
                                        <input type="hidden" name="spk_logo_image" id="spk_logo_image">
                                        <div id="spk_logo_image_preview"></div>
                                    </div>
                                    <div class="col-sm-5">
                                        <input type="file" class="form-control" name="spk_image_upload" id="spk_image_upload" placeholder="Size 1920 x 1080">
                                        <input type="hidden" name="spk_image" id="spk_image">
                                        <div id="spk_image_preview"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row p-4">
                        <div class="col-sm-2">
                            <p>
                                <a class="session-form" data-toggle="collapse" href="#speaker_blk" role="button" aria-expanded="false" aria-controls="speaker_blk">
                                    Speaker Info <span class="text-danger">*</span>
                                    <svg style="min-width: fit-content;" width="14" height="10" viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.29399 9.17178C6.68448 9.60983 7.31864 9.60983 7.70914 9.17178L13.7071 2.44337C14.0976 2.00532 14.0976 1.29393 13.7071 0.85588C13.3166 0.417832 12.6825 0.417832 12.292 0.85588L7 6.7923L1.70802 0.859384C1.31753 0.421336 0.683365 0.421336 0.292871 0.859384C-0.0976236 1.29743 -0.0976236 2.00882 0.292871 2.44687L6.29086 9.17529L6.29399 9.17178Z" fill="black"></path>
                                    </svg>
                                </a>
                            </p>
                        </div>
                        <div class="col-sm-10 collapse" id="speaker_blk">
                            <div id="repeatable_speaker">

                            </div>

                            <div class="mb-3">
                                <button type="button" class="btn std-btn mt-3 px-4 speaker_add">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 24C15.1826 24 18.2348 22.7357 20.4853 20.4853C22.7357 18.2348 24 15.1826 24 12C24 8.8174 22.7357 5.76516 20.4853 3.51472C18.2348 1.26428 15.1826 0 12 0C8.8174 0 5.76516 1.26428 3.51472 3.51472C1.26428 5.76516 0 8.8174 0 12C0 15.1826 1.26428 18.2348 3.51472 20.4853C5.76516 22.7357 8.8174 24 12 24ZM10.875 16.125V13.125H7.875C7.25156 13.125 6.75 12.6234 6.75 12C6.75 11.3766 7.25156 10.875 7.875 10.875H10.875V7.875C10.875 7.25156 11.3766 6.75 12 6.75C12.6234 6.75 13.125 7.25156 13.125 7.875V10.875H16.125C16.7484 10.875 17.25 11.3766 17.25 12C17.25 12.6234 16.7484 13.125 16.125 13.125H13.125V16.125C13.125 16.7484 12.6234 17.25 12 17.25C11.3766 17.25 10.875 16.7484 10.875 16.125Z" fill="white"></path>
                                    </svg>
                                    <span class="ml-2">Add More Speaker/(s)</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row p-4">
                        <div class="col-sm-2">
                            <p>
                                <a class="session-form" data-toggle="collapse" href="#sessionStateCollapse" role="button" aria-expanded="false" aria-controls="sessionStateCollapse">
                                    Session State <span class="text-danger">*</span>
                                    <svg style="min-width: fit-content;" width="14" height="10" viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.29399 9.17178C6.68448 9.60983 7.31864 9.60983 7.70914 9.17178L13.7071 2.44337C14.0976 2.00532 14.0976 1.29393 13.7071 0.85588C13.3166 0.417832 12.6825 0.417832 12.292 0.85588L7 6.7923L1.70802 0.859384C1.31753 0.421336 0.683365 0.421336 0.292871 0.859384C-0.0976236 1.29743 -0.0976236 2.00882 0.292871 2.44687L6.29086 9.17529L6.29399 9.17178Z" fill="black"></path>
                                    </svg>
                                </a>
                            </p>
                        </div>
                        <div class="col-sm-10 collapse" id="sessionStateCollapse">
                            <div class="card card-body border-0 py-0">
                                <div class="mb-3 row">
                                    <label for="spk_state" class="col-sm-2 col-form-label">Status <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <select name="spk_state" id="spk_state" class="form-control">
                                            <option value="active">Active</option>
                                            <option value="live">Live</option>
                                            <option value="suspended">Suspended</option>
                                            <option value="closed">Closed</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="spk_template" class="col-sm-2 col-form-label">Template <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <select name="spk_template" id="spk_template" class="form-control">
                                            <option value="online">Online</option>
                                            <option value="offline">Offline</option>
                                            <option value="hybrid">Hybrid</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="spk_hall" class="col-sm-2 col-form-label">Room <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <select name="spk_hall" id="spk_hall" class="form-control">
                                            <option value="">Select Session Room</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="spk_tags" class="col-sm-2 col-form-label">Associated Tags</label>
                                    <div class="col-sm-10">
                                        <select name="spk_tags[]" id="spk_tags" multiple class="select2 form-control tags-field">

                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="spk_tags" class="col-sm-2 col-form-label">Enable TAO Powered Networking Room with this Session</label>
                                    <div class="col-sm-10">
                                        <div class="d-flex align-items-center mt-2 mt-lg-3" style="gap:30px;">
                                            <div>
                                                <input type="radio" name="enable_tao_networking" id="session_enable_tao_networking_yes" value="1" checked>
                                                <label for="session_enable_tao_networking_yes" class="ml-1 mb-0">YES</label>
                                            </div>

                                            <div>
                                                <input type="radio" name="enable_tao_networking" id="session_enable_tao_networking_no" value="0">
                                                <label for="session_enable_tao_networking_no" class="ml-1 mb-0">NO</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 row spk_streaming_link_wrapper">
                                    <label for="spk_streaming_link" class="col-sm-2 col-form-label">Streaming Link</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="spk_streaming_link" id="spk_streaming_link" class="form-control" placeholder="Video conference link URL">
                                        <small class="form-text text-muted" style="font-size:12px">Supports: YouTube (watch/shorts/youtu.be/embed/playlist), GMeet, Jitsi, Vimeo, Twitch (video/channel/clip), Dailymotion, Loom, Wistia and other meeting link</small>
                                    </div>
                                </div>

                                <div class="mb-3 row spk_external_video_room" style="display:none;">
                                    <label for="spk_external_video_room_link" class="col-sm-2 col-form-label">Video URL <small class="text-muted">[if not hosted by TAO] </small>.</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="spk_external_video_room_link" id="spk_external_video_room_link" class="form-control" placeholder="Video conference link URL">
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="spk_room_location" class="col-sm-2 col-form-label">Enter the location here, if applicable.</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="spk_room_location" id="spk_room_location" class="form-control" placeholder="Location">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2 row p-4">
                        <div class="col-12">
                            <button type="submit" name="spk_submit" id="spk_submit" class="btn btn-submit"><i class="fa-solid fa-hourglass-half" style="font-size: 20px; margin-right: 10px;color: #fff"></i> Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- speaker Slot modal end -->

<!-- Speaker Banner Template -->
<script type="text/template" id="speaker_template">
    <div class="speakeritem card card-body" id="speaker_item_{?}" data-moreSpeakerIndex="{?}">
        <div class="py-3 d-flex justify-content-end align-items-center py-3">
            <button type="button" class="btn p-0 speaker_delete">
                <svg width="25" height="25" viewBox="0 0 42 37" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5.25 0C2.3543 0 0 2.3543 0 5.25V31.5C0 34.3957 2.3543 36.75 5.25 36.75H36.75C39.6457 36.75 42 34.3957 42 31.5V5.25C42 2.3543 39.6457 0 36.75 0H5.25ZM14.3555 11.7305C15.1266 10.9594 16.3734 10.9594 17.1363 11.7305L20.9918 15.5859L24.8473 11.7305C25.6184 10.9594 26.8652 10.9594 27.6281 11.7305C28.391 12.5016 28.3992 13.7484 27.6281 14.5113L23.7727 18.3668L27.6281 22.2223C28.3992 22.9934 28.3992 24.2402 27.6281 25.0031C26.857 25.766 25.6102 25.7742 24.8473 25.0031L20.9918 21.1477L17.1363 25.0031C16.3652 25.7742 15.1184 25.7742 14.3555 25.0031C13.5926 24.232 13.5844 22.9852 14.3555 22.2223L18.2109 18.3668L14.3555 14.5113C13.5844 13.7402 13.5844 12.4934 14.3555 11.7305Z" fill="#FF0000"></path>
                </svg>
            </button>
        </div>

        <div class="mb-3 row">
            <label for="spk_name_{?}" class="col-sm-2 col-form-label">Name <span class="text-danger">*</span></label>
            <div class="col-sm-10">
                <input type="text" class="form-control spk_name" name="spk_name[{?}]" id="spk_name_{?}">
            </div>
        </div>
        <div class="mb-3 row">
            <label for="spk_desig_{?}" class="col-sm-2 col-form-label">Job Title <span class="text-danger">*</span></label>
            <div class="col-sm-10">
                <input type="text" class="form-control spk_desig" name="spk_desig[{?}]" id="spk_desig_{?}">
            </div>
        </div>
        <div class="mb-3 row">
            <label for="spk_company_{?}" class="col-sm-2 col-form-label">Company <span class="text-danger">*</span></label>
            <div class="col-sm-10">
                <input type="text" class="form-control spk_company" name="spk_company[{?}]" id="spk_company_{?}">
            </div>
        </div>
        <div class="mb-3 row">
            <label for="spk_bio_{?}" class="col-sm-2 col-form-label">Brief Bio <span class="text-danger">*</span></label>
            <div class="col-sm-10">
                <textarea name="spk_bio[{?}]" id="spk_bio_{?}" class="no-resize spk_bio" style="resize: none;"></textarea>
            </div>
        </div>
        <div class="mb-3 row">
            <label for="spk_linkedin_{?}" class="col-sm-2 col-form-label">LinkedIn URL</label>
            <div class="col-sm-10">
                <input type="url" class="form-control spk_linkedin" name="spk_linkedin[{?}]" id="spk_linkedin_{?}">
            </div>
        </div>
        <div class="mb-3 row">
            <label for="spk_profileimg_upload_{?}" class="col-sm-2 col-form-label">Profile Picture <span class="text-danger">*</span></label>
            <div class="col-sm-10">
                <input type="file" class="form-control spk_profileimg" name="spk_profileimg_upload[{?}]" id="spk_profileimg_upload_{?}" placeholder="size 300 x 300">
                <input type="hidden" name="spk_profileimg[{?}]" id="spk_profileimg_{?}">
                <div class="spk_profileimg_preview" id="spk_profileimg_preview_{?}"></div>
            </div>
        </div>
    </div>
</script>
<!-- /Speaker Banner Template -->

<script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/events/js/events-session-form.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
