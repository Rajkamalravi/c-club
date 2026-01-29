<?php
   if (!defined('TAO_PAGE_TITLE')) {define('TAO_PAGE_TITLE', "Comprehensive Open Events List at " . TAOH_SITE_NAME_SLUG . ": Explore and Apply to a Wide Range of Event Opportunities");}
   if (!defined('TAO_PAGE_DESCRIPTION')) {define('TAO_PAGE_DESCRIPTION', "Browse our comprehensive events list featuring a diverse range of event opportunities across industries. Find the perfect event that matches your skills and interests, chat with recruiters and easily apply through our user-friendly platform at " . TAOH_SITE_NAME_SLUG . ". Start your event search today and take the next step in your career.");}
   if (!defined('TAO_PAGE_KEYWORDS')) {define('TAO_PAGE_KEYWORDS', "Event openings at " . TAOH_SITE_NAME_SLUG . ", Employment opportunities at " . TAOH_SITE_NAME_SLUG . ", Event listings at " . TAOH_SITE_NAME_SLUG . ", Event board at " . TAOH_SITE_NAME_SLUG . ", Event search platform at " . TAOH_SITE_NAME_SLUG . ", Event finder at " . TAOH_SITE_NAME_SLUG . ", Event database at " . TAOH_SITE_NAME_SLUG . ", Event search engine at " . TAOH_SITE_NAME_SLUG . ", Event match at " . TAOH_SITE_NAME_SLUG . ", Event applications at " . TAOH_SITE_NAME_SLUG . ", Apply for events at " . TAOH_SITE_NAME_SLUG . ", Event search website at " . TAOH_SITE_NAME_SLUG . ", Find a event at " . TAOH_SITE_NAME_SLUG . ", Event seekers at " . TAOH_SITE_NAME_SLUG . ", Event alerts at " . TAOH_SITE_NAME_SLUG . ", Explore event opportunities at " . TAOH_SITE_NAME_SLUG);}
   define('TAO_PAGE_ROBOT', 'noindex, nofollow');

   taoh_get_header_for_events_landing();


   $taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
   $user_info_obj = $taoh_user_is_logged_in ? taoh_user_all_info() : null;

   $valid_user = $taoh_user_is_logged_in && $user_info_obj->profile_complete;


   if(EVENT_DEMO_SITE)
   $current_app ='events';
   else
   $current_app =  taoh_parse_url(0);
   $app_data = taoh_app_info($current_app);
   $taoh_user_vars = $data = taoh_user_all_info();
   $profile_complete = (isset($data->profile_complete) && $data->profile_complete) ? $data->profile_complete : 0;

   $empty = 0;
   $ptoken = TAOH_API_TOKEN_DUMMY;
   if (taoh_user_is_logged_in()) {
       $ptoken = $user_ptoken = $data->ptoken;
   }

   $share_link = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
   define('TAO_PAGE_TYPE', ($app_data?->slug ?? ''));
   $log_nolog_token = (taoh_user_is_logged_in()) ? $ptoken : TAOH_API_TOKEN_DUMMY;
   /* check liked or not */

   $liked_arr = array();
   $rsvped_data = array();
   if (taoh_user_is_logged_in()) {
       $taoh_call = "system.users.metrics";
       $taoh_vals = array(
           'mod' => 'system',
           'token' => taoh_get_dummy_token(),
           'slug' => TAO_PAGE_TYPE,
       );
       //echo taoh_apicall_get_debug($taoh_call, $taoh_vals);exit();
       $get_liked = json_decode(taoh_apicall_get($taoh_call, $taoh_vals), true);

       if (isset($get_liked['conttoken_liked'])) {
           $liked_arr = $get_liked['conttoken_liked'];
       }
       /* End check liked or not */

       /* Get RSVP list */
       //https://ppapi.tao.ai/events.user.rsvp?mod=core&token=C3kONdHX&ops=events
       $taoh_call = "events.user.rsvp";
       $taoh_vals = array(
           'mod' => 'core',
           'token' => taoh_get_dummy_token(),
           'ops' => 'events',
           'cache_required' => 0,
           'time' => time(),
       );
       //echo taoh_apicall_get_debug($taoh_call, $taoh_vals);exit();
       $rsvped_data = json_decode(taoh_apicall_get($taoh_call, $taoh_vals), true);
       if ($rsvped_data['success']) {
           $rsvped_data = $rsvped_data['output'];
       } else {
           $rsvped_data = array();
       }
       /* End Get RSVP list */
   }

   ?>
<link rel="stylesheet" href="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/css/events-tao-ai.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>">
<div class="mobile-app">

<div class="new-events t-events bg-white">
   <div class="events-banner">
      <h4 class="text-center px-3">Discover Live & Upcoming Events Near You</h4>
   </div>
   <?php

        include('events_tao_search.php');

    ?>
      <!----END ROW--->
      <div class="container pb-5">
      <div class="row" style="width: 100% !important">
      <div class="col-sm-3"></div>
      <div class="col-sm-3">

      <div class="col-sm-3"></div>
      <div class="col-sm-3"></div>
        </div>

      </form>
   </div>
</div>
   <!-----------------------------------------Near EVENTS----------------------------->
   <div class="container pb-5">
      <div class="d-flex justify-content-between align-items-end flex-wrap mb-4" style="gap: 12px;">
         <p class="event-info">Recent Events Near You</p>
         <!-- <div style="width: 100%; max-width: 237px;">
            <p class="select-label mb-1">Change Location</p>
            <select name="loaction" id="" class="form-control loc-sel">
               <option value="">Select</option>
            </select>
         </div> -->
      </div>
      <div class="d-flex justify-content-center flex-wrap px-0" style="gap: 12px;" id="eventlistArea" >
         <div  class="loaderArea" class="listloaderArea"></div>
      </div>
      <div class="d-flex justify-content-end mt-2" >
         <a id="recent_all" href="<?php echo TAOH_SITE_URL_ROOT . "/all/near"; ?>" class="v-events mt-3">View all Events</a>
      </div>

   </div>
   <!-----------------------------------------POPULAR EVENTS----------------------------->
   <div class="bg-even py-5">
      <div class="container">
         <p class="event-info mb-4">Popular Events</p>
         <div class="d-flex justify-content-center flex-wrap px-0" style="gap: 12px;" id="eventlistAreaPopular" >
         <div class="listloaderArea"></div>
         </div>
         <div class="d-flex justify-content-end mt-2" >
            <a id="popular_all" href="<?php echo TAOH_SITE_URL_ROOT . "/all/popular"; ?>" class="v-events mt-3">View all Events</a>
         </div>

      </div>
   </div>
   <!-----------------------------------------FREE EVENTS----------------------------->

   <div class="container pb-5 mt-4">
      <div class="d-flex justify-content-between align-items-end flex-wrap mb-4" style="gap: 12px;">
         <p class="event-info">Top Free Events</p>
      </div>

      <div class="d-flex justify-content-center flex-wrap px-0" style="gap: 12px;" id="eventlistAreaFree" >
      <div class="listloaderArea"></div>
      </div>
      <div class="d-flex justify-content-end mt-2"  >
         <a id="free_all" href="<?php echo TAOH_SITE_URL_ROOT . "/all/free"; ?>" class="v-events mt-3">View all Events</a>
      </div>
   </div>
   <!-----------------------------------------SOON EVENTS----------------------------->
   <div class="bg-even py-5">
      <div class="container">
         <p class="event-info mb-4">Happening Soon</p>
         <div class="d-flex justify-content-center flex-wrap px-0" style="gap: 12px;" id="eventlistAreaSoon" >
         <div class="listloaderArea"></div>
         </div>
         <div class="d-flex justify-content-end mt-2" >
            <a id="soon_all" href="<?php echo TAOH_SITE_URL_ROOT . "/all/soon"; ?>" class="v-events mt-3">View all Events</a>
         </div>
      </div>
   </div>
   <div class="modal" id="eventShareModal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title">Share</h5>
            </div>
            <div class="modal-body">
               <section class="mb-3 mt-3" id="share_icon"></section>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
         </div>
      </div>
   </div>
</div>
<script>
    window._eta_cfg = {
        isLoggedIn: <?= json_encode($taoh_user_is_logged_in); ?>,
        isValidUser: <?= json_encode($valid_user); ?>,
        appSlug: <?= json_encode($app_data?->slug ?? ''); ?>,
        likeMin: <?= json_encode(TAOH_SOCIAL_LIKES_THRESHOLD); ?>,
        commentMin: <?= json_encode(TAOH_SOCIAL_COMMENTS_THRESHOLD); ?>,
        shareMin: <?= json_encode(TAOH_SOCIAL_SHARES_THRESHOLD); ?>,
        likedArr: <?= json_encode($liked_arr ?? []); ?>,
        userPtoken: <?= json_encode($ptoken); ?>,
        rsvpedData: <?= json_encode($rsvped_data); ?>,
        staticAjaxToken: <?= json_encode(taoh_get_dummy_token(1)); ?>,
        intaoDbEnable: <?= json_encode((bool)TAOH_INTAODB_ENABLE); ?>,
        userTimezone: <?= json_encode($taoh_user_is_logged_in ? taoh_user_timezone() : ''); ?>,
        opsPrefix: <?= json_encode(TAOH_OPS_PREFIX); ?>,
        pageImage: <?= json_encode(defined('TAO_PAGE_IMAGE') ? TAO_PAGE_IMAGE : ''); ?>,
        pageDesc: <?= json_encode(defined('TAO_PAGE_DESCRIPTION') ? urlencode(substr(TAO_PAGE_DESCRIPTION, 0, 240)) : ''); ?>,
        pageTitle: <?= json_encode(defined('TAO_PAGE_TITLE') ? TAO_PAGE_TITLE : ''); ?>
    };
</script>
<script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/js/events-tao-ai.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
<?php
taoh_get_footer_events_landing();
