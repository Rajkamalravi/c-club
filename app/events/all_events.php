<?php
  $event_type = taoh_parse_url(1);

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

<link rel="stylesheet" href="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/css/all-events.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>">


<div class="new-events t-events bg-white">
    <div class="events-banner">
        <h4 class="text-center px-3">All Events Happening Around you !</h4>
    </div>

    <?php
   // if (taoh_user_is_logged_in()) {
        include('events_tao_search.php');
   // }
    ?>

    <div class="container py-5">
    <div class="row justify-content-center">
                            <div  class="loaderArea" id="listloaderArea"></div>
                        </div>
    <div class="d-flex justify-content-center flex-wrap px-0" style="gap: 12px;" id="eventlistArea">

    </div>
    <div class="d-flex justify-content-center mb-5">
                                <div id="pagination"></div>
                            </div>


</div>

<script>
    window._ale_cfg = {
        isLoggedIn: <?= json_encode($taoh_user_is_logged_in); ?>,
        isValidUser: <?= json_encode($valid_user); ?>,
        appSlug: <?= json_encode($app_data?->slug ?? ''); ?>,
        siteUrlRoot: <?= json_encode(TAOH_SITE_URL_ROOT); ?>,
        siteAjaxUrl: <?= json_encode(taoh_site_ajax_url()); ?>,
        likeMin: <?= json_encode(TAOH_SOCIAL_LIKES_THRESHOLD); ?>,
        commentMin: <?= json_encode(TAOH_SOCIAL_COMMENTS_THRESHOLD); ?>,
        shareMin: <?= json_encode(TAOH_SOCIAL_SHARES_THRESHOLD); ?>,
        likedArr: <?= json_encode($liked_arr ?? []); ?>,
        userPtoken: <?= json_encode($ptoken); ?>,
        rsvpedData: <?= json_encode($rsvped_data); ?>,
        staticAjaxToken: <?= json_encode(taoh_get_dummy_token(1)); ?>,
        intaoDbEnable: <?= json_encode((bool)TAOH_INTAODB_ENABLE); ?>,
        eventType: <?= json_encode($event_type ?? ''); ?>,
        userTimezone: <?= json_encode($taoh_user_is_logged_in ? taoh_user_timezone() : ''); ?>,
        opsPrefix: <?= json_encode(TAOH_OPS_PREFIX); ?>,
        pageImage: <?= json_encode(defined('TAO_PAGE_IMAGE') ? TAO_PAGE_IMAGE : ''); ?>,
        pageDesc: <?= json_encode(defined('TAO_PAGE_DESCRIPTION') ? urlencode(substr(TAO_PAGE_DESCRIPTION, 0, 240)) : ''); ?>,
        pageTitle: <?= json_encode(defined('TAO_PAGE_TITLE') ? TAO_PAGE_TITLE : ''); ?>
    };
</script>
<script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/js/all-events.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>

<?php taoh_get_footer_events_landing(); ?>