<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/raj/assets/icons/icons.php';
$ptoken = (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? '';
$opt = taoh_parse_url(1);

$iamorg = isset($event_organizer_ptokens) && in_array($ptoken, $event_organizer_ptokens);
?>

<style>
    #search_halls {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,<svg fill='white' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/><path d='M0 0h24v24H0z' fill='none'/></svg>");
        background-repeat: no-repeat;
        background-position: right .3rem center;
        background-size: 1.5rem auto;
        padding-right: 2rem;
    }
</style>
<?php
if (TAO_CURRENT_APP_INNER_PAGE == 'events_lobby') {
    ?>
    <div class="upgrade-section vertical-upgrade">
        <div class="upgrade_modal_btn_wrapper" style="display:none;">
            <button class="btn btn-primary mr-2" id="upgrade_modal_btn" data-toggle="modal" data-target="#upgradeModal" data-backdrop="static" data-keyboard="false"><i class="fa-solid fa-circle-arrow-left"></i><i class="fa-solid fa-circle-arrow-up"></i> Upgrade</button>
        </div>
    </div>
    <?php
}
?>

<div class="hall_tabs">
    <ul class="nav nav-tabs new-tab-con justify-content-center" id="myTab" role="tablist">
        <li class="nav-item speaker_exhibitor d-flex align-items-center" id="agenda_top" >
            <a class="nav-link ml-1 active" id="agenda-tab" data-toggle="tab" href="#agenda_desc" role="tab" aria-controls="agenda" aria-selected="true">Agenda</a>
            <span style="color: #333333;display:none" id="dashboard-slash" >/</span>
            <a class="nav-link mr-1" id="dashboard-tab" data-toggle="tab" href="#dashboard_desc" role="tab" aria-controls="dashboard" aria-selected="false" style="display:none"> My</a>
        </li>
        <?php

        if(TAO_CURRENT_APP_INNER_PAGE != 'events_lobby'){ ?>
        <li class="nav-item" id="desc_top">
            <a class="nav-link " id="desc-tab" data-toggle="tab" href="#desc_desc" role="tab" aria-controls="desc" aria-selected="false">Description</a>

        </li>
        <?php } ?>
         <?php if(TAOH_TABLES_DISCUSSION_SHOW){ ?>
        <li class="nav-item tables" id="tables_top" style="display:none">
            <a class="nav-link" id="tables-tab" data-toggle="tab" href="#tables_desc" role="tab" aria-controls="tables"
             aria-selected="false" >Discussions</a>
        </li>
        <?php } ?>
        <?php if(TAOH_COMMENTS_SHOW){ ?>
        <li class="nav-item comments" id="comments_top" style="display:none">
            <a class="nav-link" id="comments-tab" data-toggle="tab" href="#comments_desc" role="tab" aria-controls="comments"
             aria-selected="false" >Comments</a>
        </li>
        <?php } ?>

        <li class="nav-item">
            <a class="nav-link" id="rsvp-tab" data-toggle="tab" href="#rsvp_desc" role="tab" aria-controls="rsvp"
            aria-selected="false">Attendees</a>
        </li>

        <li class="nav-item speaker_exhibitor" id="exhibitor_top" style="display:none">
            <a class="nav-link" id="exhibitors-tab" data-toggle="tab" href="#exhibitors_desc" role="tab" aria-controls="exhibitors" aria-selected="false" >Exhibitors and Sponsors</a>
        </li>

        <li class="nav-item speaker_exhibitor" id="speaker_top" style="display:none">
            <a class="nav-link" id="speakers-tab" data-toggle="tab" href="#speakers_desc" role="tab" aria-controls="speakers" aria-selected="false" >Speakers</a>
        </li>

        <li class="nav-item" style="display:none;">
            <a class="nav-link" id="rooms-tab" data-toggle="tab" href="#rooms_desc" role="tab" aria-controls="rooms" aria-selected="false" style="display:none">Rooms</a>
        </li>

        <li class="nav-item position-relative select-dw-svg" style="display:none;">
            <select name="search_halls" id="search_halls">
                <option value="" selected>-- Select Hall --</option>
            </select>
            <svg width="9" height="5" viewBox="0 0 9 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8.09863 0.0996094C8.42759 0.0997736 8.71571 0.282949 8.83887 0.556641C8.96113 0.828395 8.89625 1.1392 8.66797 1.35059L5.07031 4.68262C4.7573 4.97245 4.24571 4.97232 3.93262 4.68262L3.86426 4.62012L3.86133 4.62207L0.331055 1.35352C0.104813 1.1416 0.0381321 0.829766 0.160156 0.558594C0.283395 0.285132 0.575001 0.102539 0.901367 0.102539L8.09863 0.0996094Z" fill="white" stroke="black" stroke-width="0.2"/>
            </svg>
        </li>

        <?php
        if (TAO_CURRENT_APP_INNER_PAGE == 'events_lobby' || $iamorg) {
//            echo '<li class="nav-item upgrade_modal_btn_wrapper" style="display:none;right: 70px;position: absolute;">';
//            echo '<button class="btn btn-primary mr-2" id="upgrade_modal_btn" data-toggle="modal" data-target="#upgradeModal" data-backdrop="static" data-keyboard="false">Upgrade</button>';
//            echo '</li>';

            echo '<li class="nav-item">';
            echo '<div class="dropdown show for_agenda d-flex justify-content-end pr-1 pr-lg-3" style="right: 0;position: absolute;top: ' . (TAO_CURRENT_APP_INNER_PAGE == 'events_details' ? '40px': '70px') . ';margin-right: 20px;">';
            echo '<a class="btn" style="background-color:#cccccc" href="#" role="button" id="dropdownMenuLink_more" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">⋮</a>';
            echo '<div class="dropdown-menu agenda_more" aria-labelledby="dropdownMenuLink_more" style="z-index: 9;">';
            if (TAO_CURRENT_APP_INNER_PAGE == 'events_lobby') {
                echo '<div class="dropdown-item"><a class="pl-2 m-1" onclick="openDesc();" id="desc-tab" data-toggle="tab" href="#desc_desc" role="tab" aria-controls="desc" aria-selected="false">Description</a></div>';
            }

            if ($iamorg) {
                echo '<div class="dropdown-item"><a class="pl-2 m-1" onclick="openBanner();" id="add_banner_btn" data-toggle="tab" href="#add_banner_tab" role="tab" aria-controls="desc" aria-selected="false">Add Banner</a></div>';
            }
            echo '<div class="dropdown-item"><a class="pl-2 m-1" style="display: none;" id="org_video_modal_btn" data-toggle="modal" href="#orgVideoModal">Organizer Video Message</a></div>';
            echo '</div>';
            echo '</div>';
            echo '</li>';
        }
        ?>
    </ul>

    <div class="tab-content pt-3 px-0" id="myTabContent">
        <div class="hall-list-container mx-auto tab-pane fade show active speaker_exhibitor" id="agenda_desc" role="tabpanel" aria-labelledby="agenda-tab" style="max-height: unset;">
            <div id="agenda_default_banner" class=" mx-auto mx-xl-0" style="display:none">
                <?php include_once('includes/defaults.php'); render_agenda_default_banner(); ?>
            </div>
            <div class="agenda_block">
             <div class="dropdown show mb-3 for_agenda d-flex justify-content-end">
                <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink_agenda" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Select Hall
                </a>

                <div class="dropdown-menu agenda_hall_list" aria-labelledby="dropdownMenuLink_agenda" style="z-index: 9;">

                </div>

            </div>

            <div id="agenda_loaderArea"></div>
            <div id="agenda_list" class=" mx-auto mx-xl-0">

            </div>


            <div class="v2-banner d-none flex-column flex-md-row p-3 px-lg-5">
                <div class="v2-svg-con">
                    <svg width="60" height="54" viewBox="0 0 60 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.1152 14.1813L20.2418 19.5H20.125H13.6562C11.6707 19.5 10.0625 17.8918 10.0625 15.9062C10.0625 13.9207 11.6707 12.3125 13.6562 12.3125H13.8539C15.1926 12.3125 16.4414 13.0223 17.1152 14.1813ZM5.75 15.9062C5.75 17.2 6.06445 18.4219 6.6125 19.5H2.875C1.28477 19.5 0 20.7848 0 22.375V28.125C0 29.7152 1.28477 31 2.875 31H43.125C44.7152 31 46 29.7152 46 28.125V22.375C46 20.7848 44.7152 19.5 43.125 19.5H39.3875C39.9355 18.4219 40.25 17.2 40.25 15.9062C40.25 11.5398 36.7102 8 32.3438 8H32.1461C29.2801 8 26.6207 9.51836 25.1652 11.9891L23 15.6816L20.8348 11.998C19.3793 9.51836 16.7199 8 13.8539 8H13.6562C9.28984 8 5.75 11.5398 5.75 15.9062ZM35.9375 15.9062C35.9375 17.8918 34.3293 19.5 32.3438 19.5H25.875H25.7582L28.8848 14.1813C29.5676 13.0223 30.8074 12.3125 32.1461 12.3125H32.3438C34.3293 12.3125 35.9375 13.9207 35.9375 15.9062ZM2.875 33.875V49.6875C2.875 52.0684 4.80664 54 7.1875 54H20.125V33.875H2.875ZM25.875 54H38.8125C41.1934 54 43.125 52.0684 43.125 49.6875V33.875H25.875V54Z" fill="url(#paint0_linear_4053_12)"/>
                        <path d="M46.6528 0.421828C46.5324 0.164044 46.2779 0 45.9984 0C45.7189 0 45.4666 0.164044 45.3439 0.421828L43.8827 3.52226L40.6194 4.01908C40.3467 4.06126 40.1195 4.25812 40.0354 4.52762C39.9513 4.79712 40.0195 5.09474 40.2149 5.29394L42.5828 7.71007L42.0238 11.1245C41.9783 11.4058 42.092 11.6917 42.3169 11.858C42.5419 12.0244 42.8396 12.0455 43.085 11.9119L46.0006 10.3067L48.9162 11.9119C49.1617 12.0455 49.4594 12.0268 49.6843 11.858C49.9093 11.6893 50.0229 11.4058 49.9775 11.1245L49.4162 7.71007L51.7841 5.29394C51.9795 5.09474 52.05 4.79712 51.9636 4.52762C51.8773 4.25812 51.6523 4.06126 51.3796 4.01908L48.1141 3.52226L46.6528 0.421828Z" fill="#FFEA8F"/>
                        <path d="M56.4352 7.28122C56.3549 7.10936 56.1853 7 55.9989 7C55.8126 7 55.6444 7.10936 55.5626 7.28122L54.5885 9.34817L52.4129 9.67939C52.2311 9.70751 52.0796 9.83874 52.0236 10.0184C51.9675 10.1981 52.013 10.3965 52.1433 10.5293L53.7219 12.14L53.3492 14.4164C53.3189 14.6038 53.3946 14.7944 53.5446 14.9054C53.6946 15.0163 53.8931 15.0304 54.0567 14.9413L56.0004 13.8711L57.9442 14.9413C58.1078 15.0304 58.3062 15.0179 58.4562 14.9054C58.6062 14.7929 58.682 14.6038 58.6517 14.4164L58.2775 12.14L59.8561 10.5293C59.9864 10.3965 60.0333 10.1981 59.9758 10.0184C59.9182 9.83874 59.7682 9.70751 59.5864 9.67939L57.4094 9.34817L56.4352 7.28122Z" fill="#FFEA8F"/>
                        <defs>
                        <linearGradient id="paint0_linear_4053_12" x1="23" y1="8" x2="23" y2="54" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#2557A7"/>
                        <stop offset="1" stop-color="#AD29FF"/>
                        </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="flex-grow-1 d-flex align-items-center flex-wrap" style="gap: 12px;">
                    <div class="flex-grow-1">
                        <h6 class="mb-2">Unlock Exclusive Swag & Raffle Prizes at This Event !</h6>
                        <p class="mb-2">Claim digital goodies, limited-edition merch, and enter exciting draws — all in one place.</p>
                    </div>

                    <div>
                        <button type="button" class="btn banner-v2-btn mt-0 mr-1" data-toggle="modal" data-target="#unlockRewards">Unlock Rewards</button>
                    </div>
                </div>
            </div>


            <div id="agenda_default_list" class=" mx-auto mx-xl-0" style="display:none">
                <?php include_once('events_agenda_default_list.php'); ?>
            </div>
            </div>
        </div>

        <div class="hall-list-container mx-auto tab-pane fade " id="desc_desc" role="tabpanel" aria-labelledby="desc-tab" style="max-width: 1185px; max-height: unset;">
            <div class="event-description event_description" id="event_description_all"></div>

            <div class="tagline-v1 my-3 rsvp-tagline" style="display: <?= ($opt ?? '') == 'chat' ? 'none' : 'flex'; ?>;">
                <?= icon('calendar-check', '#2C7678', 28) ?>
                <div>
                    <p>After you register, return to this page at the scheduled start time (for virtual event)—the event will go live here. We’ll also email your join link. No apps or downloads required.</p>
                </div>
            </div>
        </div>

        <?php
        if ($iamorg):
            $banner_meta_id = '';
            $event_organizer_banner_link = '';
            if (!empty($event_organizer_banner)) {
                $banner_meta_id = $event_organizer_banner['ID'];
                $event_organizer_banner_link = $event_organizer_banner['organizer_banner_link'];
            }
        ?>
        <div class="hall-list-container mx-auto tab-pane fade " id="add_banner_tab" role="tabpanel" aria-labelledby="desc-tab" style="max-width: 1185px; max-height: unset;">
            <h5 style="color: #2A4E96;">Add Organizer Banner</h5>
            <form action="<?= taoh_site_ajax_url(); ?>" method="post" name="event_organizer_banner_form" id="event_organizer_banner_form" enctype="multipart/form-data">
                <input type="hidden" name="taoh_action" value="add_event_organizer_banner">
                <input type="hidden" name="eventtoken" value="<?= $eventtoken ?? ''; ?>">
                <input type="hidden" name="ptoken" value="<?= $ptoken ?? ''; ?>">
                <input type="hidden" name="banner_metaid" value="<?= $banner_meta_id ?? ''; ?>">

                <div class="col-md-6 mt-4">
                    <div class="form-group">
                        <label for="event_organizer_banner">Upload Image</label>
                        <div class="input-group custom-file">
                            <input type="file" class="custom-file-input" name="event_organizer_banner" id="event_organizer_banner" accept="image/*">
                            <label class="custom-file-label" for="event_organizer_banner">Choose file</label>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="image-preview-container" style="display: <?= (!empty($banner_meta_id) && !empty($event_organizer_banner_link)) ? 'block' : 'none' ?>;">
                            <div><strong>Preview:</strong></div>
                            <img src="<?= $event_organizer_banner_link ?? ''; ?>" alt="Image Preview" class="img-fluid" id="imagePreview" style="max-width: 100%; max-height: 200px; margin-top: 10px;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-danger mt-2" data-metaid="<?= $banner_meta_id ?? ''; ?>" id="remove_event_organizer_banner_btn" style="display: <?= (!empty($banner_meta_id) && !empty($event_organizer_banner_link)) ? 'inline-block' : 'none' ?>;"><i class="fa fa-trash text-white mr-2"></i> Remove Banner</button>
                    </div>
                </div>

                <div class="col-md-6 mt-3">
                    <button type="submit" class="btn btn-primary" id="event_organizer_banner_upload_btn"><i class="fa fa-save text-white mr-2"></i> Upload Banner</button>
                </div>
            </form>
        </div>
        <?php
        endif;
        ?>

        <div class="hall-list-container mx-auto tab-pane fade speaker_exhibitor" id="speakers_desc" role="tabpanel" aria-labelledby="speakers-tab" style="max-height: unset;">
            <div id="speaker_default_banner" class=" mx-auto mx-xl-0" style="display:none">
                <?php //include_once('events_speaker_default_banner.php'); ?>
            </div>

            <div class="speaker_block">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center mb-3" style="gap: 12px;">
                    <div class="speaker_filter container d-flex flex-column flex-md-row justify-content-center" id="speaker_search_block" style="z-index: 9;">
                        <input type="text" name="speaker_search" id="speaker_search" placeholder="Search..." class="mb-3 my-lg-3 form-control search-input" style="max-width: 553px;">
                        <button onclick="performSpeakerSearch();" type="button" class="btn search-btn mb-3 my-lg-3">Search</button>
                    </div>
                    <div class=" speaker_filter dropdown show for_speaker d-flex justify-content-end pr-1 pr-lg-3" style="z-index: 9;">
                        <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink_speaker" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Select Speaker Hall
                        </a>

                        <div class="dropdown-menu spk_hall_list" id="spk_hall_list" aria-labelledby="dropdownMenuLink_speaker" style="z-index: 9;">

                        </div>
                    </div>
                </div>

                <div id="speaker_loaderArea"></div>
                <!-- list 1 -->
                <div id="speaker_list" class=" mx-auto mx-xl-0 pr-1 pr-lg-3"></div>
                <div id="speaker_default_list" class=" mx-auto mx-xl-0" style="display:none">
                    <?php include_once('events_speaker_default_list.php'); ?>
                </div>
            </div>
        </div>

        <div class="hall-list-container mx-auto tab-pane fade speaker_exhibitor" id="exhibitors_desc" role="tabpanel" aria-labelledby="exhibitors-tab" style="max-height: unset;">
            <div id="exhibitor_default_banner" class="mx-auto mx-xl-0"  style="display:none">

            </div>

            <div class="exhibitor_block">
                <div class="dropdown show for_exhibitor d-flex justify-content-end pr-1 pr-lg-3">
                    <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink_exh" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Select Exhibitor Hall
                    </a>

                    <div class="dropdown-menu" id="exh_hall_list" aria-labelledby="dropdownMenuLink_exh" style="z-index: 9;">

                    </div>
                </div>

                <div id="exhibitors_loaderArea"></div>

                <div class="d-flex flex-column  justify-content-xl-center mt-3" style="gap: 36px;">
                    <div id="exhibitors_list" class="w-100 mx-auto mx-xl-0 pr-1 pr-lg-3">

                    </div>
                </div>

                <div id="raffles_list" style="display:none;" class="w-100 mx-auto mx-xl-0 pr-3">
                    <div class="hall-side-list-con">
                        <div class="others-in-event pb-2 mb-3">
                            <p class="dark-bg-title py-3 pl-3 pl-lg-5 pr-3">Raffles</p>
                            <div class="container px-3 px-xl-4">

                                <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">

                                    <ol id="raffles_slide" class="carousel-indicators custom" style="margin-bottom: -5px;">
                                    </ol>

                                    <div id="raffle_carousel" class="offer-carousel carousel-inner pt-3 pb-5">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="exhibitor_default_list" class=" mx-auto mx-xl-0" style="display:none">

                </div>
            </div>
        </div>

        <div class="hall-list-container mx-auto tab-pane fade pt-3" id="rsvp_desc" role="tabpanel" aria-labelledby="rsvp-tab" style="max-height: unset;">

            <div class="rsvp_actions flex-wrap align-items-center justify-content-between" style="gap: 9px;display: flex;">
                <div id="download_rsvp" class="btn btn-success event_download_rsvp" style="display:none" >Download</div>
                <div id="email_rsvp" class="align-items-center flex-wrap" style="gap: 6px;display:none;"></div>
            </div>

            <div class="rsvp_actions rsvp_search container flex-column flex-md-row justify-content-center" style="display: none;">
                <input type="text" id="rsvp_search" required placeholder="Search with Attendee name, Company or location"
                class="mb-3 my-lg-3 form-control search-input" style="max-width: 553px;">
                <button type="button" onclick="performRSVPSearch();" class="btn search-btn mb-3 my-lg-3">Search</button>
            </div>

            <span id="attendees_total_count"></span>

            <div id="rsvpdir_loaderArea" class="text-center"></div>
            <div id="rsvp_users_list" class="pt-4"></div>
            <div id="rsvpdir_loaderArea_btm" class="text-center"></div>
            <a href="javascript:void(0)" id="load_more_btn" onclick="loadMoreRsvpedUsers();">Load more...</a>
            <div id="rsvp_default_list" class=" mx-auto mx-xl-0" style="display:none">
                <?php include_once('events_rsvp_default_list.php'); ?>
            </div>

            <div class="tagline-v1 my-3 attendee_tagline" style="display: none;">
                <?= icon('calendar-check', '#2C7678', 28) ?>
                <div>
                    <p>You can view and connect with attendees 3 days before the event starts.</p>
                </div>
            </div>

        </div>
        <div class="hall-list-container mx-auto tab-pane fade pt-3" id="rooms_desc" role="tabpanel" aria-labelledby="rooms-tab"  style="max-height: unset;">
            <div class="rooms_block">
                <div id="rooms_list" class=" mx-auto mx-xl-0 pr-1 pr-lg-3"></div>
            </div>
        </div>
        <div class="hall-list-container mx-auto tab-pane fade pt-3" id="tables_desc" role="tabpanel" aria-labelledby="tables-tab"  style="max-height: unset;">
            <div class="tables_block">
                <div id="tables_list" class=" mx-auto mx-xl-0 pr-1 pr-lg-3"></div>
            </div>
        </div>
        <div class="hall-list-container mx-auto tab-pane fade pt-3 text-center" id="comments_desc" role="tabpanel" aria-labelledby="comments-tab"  style="max-height: unset;">
            <?php if(taoh_user_is_logged_in() && TAO_CURRENT_APP_INNER_PAGE == 'events_lobby') { ?>
            <div class="comments_block">

                    <?php
                    $current_user_name = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->fname.' '.taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->lname;

                    $currentUserData = json_encode(array(
                        "user_id" => $ptoken,
                        "user_name" => $current_user_name,
                        "avatar_url" => taoh_get_avatar_src() ,
                        "profile_link" => TAOH_SITE_URL . '/profile/' . $ptoken
                     ));


                    ?>
                    <script src="<?php echo TAOH_COMMENTS_JS;?>?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
                    <link rel="stylesheet" href="<?php echo TAOH_COMMENTS_CSS;?>?v=<?php echo TAOH_CSS_JS_VERSION; ?>">
                    <div id="comments_list" class="mx-auto mx-xl-0 pr-1 pr-lg-3">
                        <script>

                        var currentUser = {
                            user_id: '<?php echo  $ptoken; ?>',
                            user_name: '<?php echo  $current_user_name; ?>',
                            avatar_url: '<?php echo  taoh_get_avatar_src(); ?>', // Can be populated if you have avatar URLs
                            profile_link: '<?php echo  TAOH_SITE_URL . '/profile/' . $ptoken  ; ?>' // Can be populated if you have profile pages
                        };
                        // Initialize comments engine with adapter (calls mos_labs_lab.php)
                        var commentsEngine = new TaohComments('#comments_list', {
                                                            //mosLabsApiUrl: '/cdn/comments/comments_adapter.php',
                                                            mosLabsOpsKey: '<?php echo TAOH_OPS_CODE; ?>',
                                                            mosLabsApiKey: 'comment_key',
                                                            appName: 'table_<?php echo $eventtoken?>_comments',
                                                            env: '<?php echo TAOH_COMMENTS_VERSION;?>',
                                                            pageId: '<?php echo addslashes($eventtoken); ?>',
                                                            currentUser :  currentUser,
                                                            perPage: 20,
                                                            enableNestedComments: true,
                                                            maxNestingLevel: 1
                        });

                        </script>
                    </div>
            </div>
            <?php } else if(taoh_user_is_logged_in() && TAO_CURRENT_APP_INNER_PAGE != 'events_lobby') { ?>
                <!--<a class="btn btn-success banner-v2-btn mt-0 mr-1 register_now" id='' href="javascript:void(0);">Register to view or post comments</a>
            -->
                 <div class="v2-banner flex-column flex-md-row p-3 px-lg-5">

                        <div>
                            <h6 class="mb-2" >Be the first to start posting the comments.</h6>

                            <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">

                                    <span class="info" style="color:#007bff;">Register to view or post comments</span>

                            </div>
                        </div>
                    </div>

            <?php } else { ?>
                <div class="comments_block">
                    Please login to view and post comments.
                    <br>
                    <button type="button" class="mt-3 mb-2 btn btn-primary " style="width:200px;" data-location="" data-toggle="modal"
                    data-target="#config-modal"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>Login</button>
                </div>
            <?php } ?>
        </div>

        <div class="hall-list-container mx-auto tab-pane fade pt-3" id="dashboard_desc" role="tabpanel" aria-labelledby="dashboard-tab" style="max-height: unset;">

            <div id="my_agenda_default_banner" class=" mx-auto mx-xl-0" style="display:none">
                <?php render_no_saved_agenda_banner(); ?>
            </div>
            <div id="dashboard_loaderArea"></div>
            <div id="dashboard_list" class=" mx-auto mx-xl-0 pr-1 pr-lg-3">

            </div>
         </div>
        <div class="hall-list-container mx-auto tab-pane fade pt-3" id="halls_desc" role="tabpanel" aria-labelledby="halls-tab"  style="max-height: unset;">
            <div class="halls_block">
                <div id="halls_list" class=" mx-auto mx-xl-0 pr-1 pr-lg-3"></div>
            </div>
        </div>
    </div>
</div>

 <!-- Modal -->
<div class="modal fade" id="unlockRewards" tabindex="-1" role="dialog" aria-labelledby="unlockRewardsLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header position-relative border-0 light-dark p-0">
        <button type="button" class="btn v1-modal-close-btn" data-dismiss="modal" aria-label="Close">
            <?= icon('close', '#555555', 12) ?>
        </button>
      </div>

      <div class="modal-body px-lg-4">
            <div class="v2-svg-con mx-auto">
                <svg width="84" height="76" viewBox="0 0 84 76" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M24.1846 19.7344L28.6025 27.25H28.4375H19.2969C16.4912 27.25 14.2188 24.9775 14.2188 22.1719C14.2188 19.3662 16.4912 17.0938 19.2969 17.0938H19.5762C21.4678 17.0938 23.2324 18.0967 24.1846 19.7344ZM8.125 22.1719C8.125 24 8.56934 25.7266 9.34375 27.25H4.0625C1.81543 27.25 0 29.0654 0 31.3125V39.4375C0 41.6846 1.81543 43.5 4.0625 43.5H60.9375C63.1846 43.5 65 41.6846 65 39.4375V31.3125C65 29.0654 63.1846 27.25 60.9375 27.25H55.6562C56.4307 25.7266 56.875 24 56.875 22.1719C56.875 16.002 51.873 11 45.7031 11H45.4238C41.374 11 37.6162 13.1455 35.5596 16.6367L32.5 21.8545L29.4404 16.6494C27.3838 13.1455 23.626 11 19.5762 11H19.2969C13.127 11 8.125 16.002 8.125 22.1719ZM50.7812 22.1719C50.7812 24.9775 48.5088 27.25 45.7031 27.25H36.5625H36.3975L40.8154 19.7344C41.7803 18.0967 43.5322 17.0938 45.4238 17.0938H45.7031C48.5088 17.0938 50.7812 19.3662 50.7812 22.1719ZM4.0625 47.5625V69.9062C4.0625 73.2705 6.79199 76 10.1562 76H28.4375V47.5625H4.0625ZM36.5625 76H54.8438C58.208 76 60.9375 73.2705 60.9375 69.9062V47.5625H36.5625V76Z" fill="url(#paint0_linear_4053_12)"/>
                    <path d="M65.4249 0.597589C65.2542 0.232396 64.8937 0 64.4977 0C64.1017 0 63.7444 0.232396 63.5705 0.597589L61.5005 4.98987L56.8775 5.6937C56.4912 5.75346 56.1692 6.03233 56.0501 6.41413C55.931 6.79592 56.0276 7.21755 56.3044 7.49975L59.659 10.9226L58.867 15.7598C58.8027 16.1582 58.9636 16.5632 59.2823 16.7989C59.6011 17.0346 60.0228 17.0645 60.3705 16.8753L64.5009 14.6011L68.6313 16.8753C68.979 17.0645 69.4008 17.0379 69.7195 16.7989C70.0382 16.5599 70.1992 16.1582 70.1348 15.7598L69.3396 10.9226L72.6942 7.49975C72.971 7.21755 73.0708 6.79592 72.9485 6.41413C72.8262 6.03233 72.5074 5.75346 72.1211 5.6937L67.4949 4.98987L65.4249 0.597589Z" fill="#FFEA8F"/>
                    <path d="M79.0984 10.3867C78.988 10.1504 78.7547 10 78.4985 10C78.2423 10 78.0111 10.1504 77.8986 10.3867L76.5591 13.2287L73.5678 13.6842C73.3178 13.7228 73.1095 13.9033 73.0324 14.1503C72.9553 14.3974 73.0178 14.6702 73.197 14.8528L75.3676 17.0676L74.8551 20.1975C74.8135 20.4553 74.9176 20.7174 75.1239 20.8699C75.3301 21.0224 75.603 21.0417 75.828 20.9193L78.5006 19.4478L81.1732 20.9193C81.3982 21.0417 81.6711 21.0245 81.8773 20.8699C82.0835 20.7152 82.1877 20.4553 82.146 20.1975L81.6315 17.0676L83.8021 14.8528C83.9813 14.6702 84.0458 14.3974 83.9667 14.1503C83.8875 13.9033 83.6813 13.7228 83.4313 13.6842L80.4379 13.2287L79.0984 10.3867Z" fill="#FFEA8F"/>
                    <defs>
                    <linearGradient id="paint0_linear_4053_12" x1="32.5" y1="11" x2="32.5" y2="76" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#2557A7"/>
                    <stop offset="1" stop-color="#AD29FF"/>
                    </linearGradient>
                    </defs>
                </svg>
            </div>
            <p class="modal-v1-text-sm">When you claim swag or enter a raffle, we’ll share your email with the sponsor giving you the prize so they can get it to you. They may reach out once about your reward, and you’re free to opt out anytime.</p>

            <div class="d-flex justify-content-center">
                <a href="#" class="btn v1-dark-btn-lg ">I agree, Unlock Rewards</a>
            </div>
      </div>

    </div>
  </div>
</div>
<div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="post" action="" name="event_email_form" id="event_email_form">
                <div class="modal-header">
                    <h5 class="modal-title" id="emailModalLongTitle">Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="ptoken" id="ptoken" value="<?= $ptoken; ?>">
                    <input type="hidden" name="email_type" id="email_type" value="">
                    <input type="hidden" name="event_token" id="event_token" value="">
                    <input type="hidden" name="ticket_type_slug" id="ticket_type_slug" value="">

                    <div class="form-group">
                        <label for="email_title">Title</label>
                        <input type="text" name="email_title" id="email_title" class="form-control" placeholder="Enter Your Title" required>
                    </div>
                    <div class="form-group">
                        <label for="email_description">Description</label>
                        <textarea name="email_description" id="email_description" class="form-control summernote" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn theme-btn-primary">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param * 1000000)
    }, 'File size must be less than {0} MB');

    var eventtoken = "<?php echo $eventtoken; ?>";
    /* var colorArray = ['#BA3B3B','#A1EAA3','#067CFF','#FDCC6E','#877CFF','#FEBC8F', '#FF6600','#BBBBFF','#F38400','#F6B1B1','#33CD45','#A7D8DE'];  */
    var colorArray = ['#708090', '#A7C7E7', '#5F9EA0', '#B3A398', '#A8BBA2', '#C3D6B8', '#EED9C4', '#D1C2E0', '#F5F5F5', '#748CAB', '#D6D1CD', '#E4C9AF', '#B8E0D2', '#E6C0C0', '#C8A2C8'];
    var shuffledColorArray = shuffleArray(colorArray);
    var hall_color_array = [];
    var u = 0;
    var opt = '<?php echo $opt ?? ''; ?>';

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
        const eventdetailBaseInfoKey = `event_detail_<?php echo $eventtoken;?>`;
        const eve_tok = "<?php echo $eventtoken;?>";

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
            'network_title': '<?php echo $speaker_data['spk_title'] ?? 'Video-Chat'; ?>'
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
</script>
