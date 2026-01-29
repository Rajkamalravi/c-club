<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="article-content-type" content="Digital <?php echo TAOH_APP_SLUG; ?>"/>
    <meta name="article-type" content="premium"/>
    <meta name="author" property="article:author" content="<?php echo (defined('TAO_PAGE_AUTHOR')) ? TAO_PAGE_AUTHOR : '#TeamTAO'; ?>"/>
    <meta name="Description" content="<?php echo htmlspecialchars(strip_tags(( defined( 'TAO_PAGE_DESCRIPTION' ) ) ?  TAO_PAGE_DESCRIPTION:taoh_site_description()), ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="item-name" content="<?php echo (defined('TAO_PAGE_TITLE')) ? TAO_PAGE_TITLE : TAOH_SITE_TITLE; ?>"/>
    <?php echo (defined('TAO_PAGE_KEYWORDS') && TAO_PAGE_KEYWORDS) ? '<meta name="keywords" content="' . TAO_PAGE_KEYWORDS . '"/>' : ''; ?>
    <meta name="msapplication-config" content="none"/>
    <?php $meta_url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>
    <?php echo (defined('TAO_PAGE_CANONICAL')) ? TAO_PAGE_CANONICAL : ''; ?>
    <?php echo (defined('TAO_PAGE_TYPE')) ? '<meta name="page-type" content="' . TAO_PAGE_TYPE . '"/>' : ''; ?>
    <?php echo (defined('TAO_PAGE_CATEGORY')) ? '<meta name="page-category-name" content="' . TAO_PAGE_CATEGORY . '"/>' : ''; ?>
    <meta name="parsely-title" content="<?php echo (defined('TAO_PAGE_TITLE')) ? TAO_PAGE_TITLE : TAOH_SITE_TITLE; ?>"/>
    <meta name="robots" content="<?php echo (defined('TAO_PAGE_ROBOT')) ? TAO_PAGE_ROBOT : 'index,follow'; ?>"/>
    <meta name="sailthru.author" content="<?php echo (defined('TAO_PAGE_AUTHOR')) ? TAO_PAGE_AUTHOR : '#TeamTAO'; ?>"/>
    <meta name="sailthru.title" content="<?php echo (defined('TAO_PAGE_TITLE')) ? TAO_PAGE_TITLE : TAOH_SITE_TITLE; ?>"/>
    <meta name="theme-color" content="#131318">
    <meta name="twitter:card" content="summary_large_image"/>
    <meta name="twitter:creator" content="<?php echo (defined('TAOH_SITE_NAME_SLUG')) ? TAOH_SITE_NAME_SLUG : '@TAOHQ'; ?>"/>
    <meta name="twitter:data1" content="<?php echo (defined('TAO_PAGE_AUTHOR')) ? TAO_PAGE_AUTHOR : '#TeamTAO'; ?>"/>
    <meta name="twitter:description" content="<?php echo htmlspecialchars(strip_tags(( defined( 'TAO_PAGE_DESCRIPTION' ) ) ?  TAO_PAGE_DESCRIPTION:taoh_site_description()), ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:domain" content="<?php echo (defined('TAOH_SITE_URL')) ? TAOH_SITE_URL : 'https://tao.ai'; ?>">
    <meta name="twitter:image:alt" content="<?php echo (defined('TAO_PAGE_TITLE')) ? TAO_PAGE_TITLE : TAOH_SITE_TITLE; ?>">
    <meta name="twitter:image" content="<?php echo (defined('TAO_PAGE_IMAGE')) ? TAO_PAGE_IMAGE : TAOH_SITE_LOGO; ?>"/>
    <meta name="twitter:label1" content="Written by <?php echo (defined('TAO_PAGE_AUTHOR')) ? TAO_PAGE_AUTHOR : '#TeamTAO'; ?>"/>
    <meta name="twitter:site" content="<?php echo (defined('TAOH_SITE_NAME_SLUG')) ? TAOH_SITE_NAME_SLUG : v; ?>">
    <meta name="twitter:title" property="og:title" content="<?php echo (defined('TAO_PAGE_TITLE')) ? TAO_PAGE_TITLE : TAOH_SITE_TITLE; ?>">
    <meta name="twitter:url" content="<?php echo $meta_url; ?>">
    <meta property="fb:app_id" content="1271794846576386"/>
    <meta property="og:description" content="<?php echo htmlspecialchars(strip_tags(( defined( 'TAO_PAGE_DESCRIPTION' ) ) ?  TAO_PAGE_DESCRIPTION:taoh_site_description()), ENT_QUOTES, 'UTF-8'); ?>" />
    <meta property="og:image:height" content="637"/>
    <meta property="og:image:type" content="image/jpeg"/>
    <meta property="og:image:width" content="300"/>
    <meta property="og:image" content="<?php echo (defined('TAO_PAGE_IMAGE')) ? TAO_PAGE_IMAGE : TAOH_SITE_LOGO; ?>"/>
    <meta property="og:locale" content="en_US"/>
    <meta property="og:site_name" content="<?php echo (defined('TAOH_SITE_NAME_SLUG')) ? TAOH_SITE_NAME_SLUG : TAOH_PLUGIN_PATH_NAME; ?>"/>
    <meta property="og:title" content="<?php echo (defined('TAO_PAGE_TITLE')) ? TAO_PAGE_TITLE : TAOH_SITE_TITLE; ?>"/>
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="<?php echo $meta_url; ?>"/>
    <meta property="schema:url" content="<?php echo $meta_url; ?>"/>
    <!-- <meta name="google-signin-client_id" content="963068544994-mv75dfaik3c7sll3chnp601jonns7s9v.apps.googleusercontent.com">
    <script src="https://apis.google.com/js/platform.js" async defer></script>  -->
    <!-- <script src="https://accounts.google.com/gsi/client" async defer></script>   -->
    <script>
        let userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

        // Get current timezone from the cookie
        let cookies = document.cookie.split("; ");
        let storedTimeZone = cookies.find(row => row.startsWith("client_time_zone="))?.split("=")[1];

        // Only update the cookie if the timezone has changed or doesn't exist
        if (!storedTimeZone || storedTimeZone !== userTimezone) {
            document.cookie = "client_time_zone=" + userTimezone + "; path=/; max-age=" + (60 * 60 * 24);
        }
    </script>
    <?php
    /*
    Configure for Google Search Console
    */
    // TAO_PAGE_AUTHOR
    // TAO_PAGE_DESCRIPTION
    // TAO_PAGE_IMAGE
    // TAO_PAGE_TITLE
    // TAO_PAGE_TWITTER_SITE
    // TAO_PAGE_ROBOT
    // TAO_PAGE_CANONICAL
    // TAO_PAGE_CATEGORY
    /*
        <meta name="google-site-verification" content="[HIDEIFNOTGIVEN]" />
    */

    $taoh_site_favicon = (defined('TAOH_PAGE_FAVICON')) ? TAOH_PAGE_FAVICON : TAOH_SITE_FAVICON;

    ?>

    <!-- Favicon -->
    <link rel="icon" href="<?php echo $taoh_site_favicon; ?>" type="image/png">

    <!-- Record last location to redirect after the login -->
    <?php echo taoh_user_record_history(); ?>
    <link rel="apple-touch-icon" href="<?php echo $taoh_site_favicon; ?>" sizes="180x180">
    <link rel="icon" href="<?php echo $taoh_site_favicon; ?>" sizes="32x32" type="image/png">
    <link rel="icon" href="<?php echo $taoh_site_favicon; ?>" sizes="16x16" type="image/png">
    <link rel="icon" href="<?php echo $taoh_site_favicon; ?>">
    <link rel="manifest" href="<?php echo TAOH_SITE_URL_ROOT . '/manifest.json'; ?>">
    <title><?php echo (defined('TAO_PAGE_TITLE')) ? TAO_PAGE_TITLE : TAOH_SITE_TITLE; ?></title>

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css" rel="stylesheet">
    <link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/font-awesome-line-awesome/css/all.min.css" rel="stylesheet">
    <link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link type="text/css" href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link type="text/css" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" rel="stylesheet">
    <link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" rel="stylesheet">
    <link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.css" rel="stylesheet">
    <link type="text/css" href="https://bug7a.github.io/iconselect.js/sample/css/lib/control/iconselect.css" rel="stylesheet">
    <link type="text/css" href="https://cdn.tao.ai/script/css/play3/style.css" rel="stylesheet">
      <!-- Intro.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/intro.js/minified/introjs.min.css">
    <link href="<?php echo TAOH_CDN_CSS_PREFIX; ?>/jquery-confirm.min.css" type="text/css" rel="stylesheet">
    <link href="<?php echo TAOH_SITE_URL_ROOT; ?>/styles_config.php?v=<?php echo TAOH_CSS_JS_VERSION; ?>" type="text/css" rel="stylesheet">
    <link href="<?php echo TAOH_CDN_CSS_PREFIX; ?>/mobile_style.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>" type="text/css"  rel="stylesheet">
    <link href="<?php echo TAOH_CDN_CSS_PREFIX; ?>/slick.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>" type="text/css"  rel="stylesheet">
    <link href="<?php echo TAOH_CDN_CSS_PREFIX; ?>/taoh.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>" type="text/css" rel="stylesheet">
    <link href="<?php echo TAOH_CDN_CSS_PREFIX; ?>/styles_internal.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/chat/css/icons.min.css">
    <!-- <link href="<?php echo TAOH_CDN_CSS_PREFIX; ?>/theme.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>" type="text/css" rel="stylesheet"> -->

    <?php
    $segments = explode('/', TAOH_SITE_URL_ROOT); // Split the URL by "/"
    $lastParam = end($segments);
    $h_session_user_info = array();
    $user_info = array();

    if (function_exists('taoh_user_is_logged_in') && taoh_user_is_logged_in() && isset($_SESSION[TAOH_ROOT_PATH_HASH]['USER_INFO'])) {
        $user_info = (array)taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];
        if (!empty($user_info['profile_complete'])) {
            $h_session_user_info = $user_info;
            $session_country_get = explode(', ', $h_session_user_info['full_location']);
            $session_country = array_pop($session_country_get);
            $user_timezone = getValidTimezone($h_session_user_info['local_timezone']);
        }
    }

    ?>

    <script>
        var _taoh_site_url_root = '<?php echo TAOH_SITE_URL_ROOT; ?>';
        var _taoh_site_root_hash = '<?php echo TAOH_SITE_ROOT_HASH ?? ''; ?>';
        var _taoh_root_path_hash = '<?php echo TAOH_ROOT_PATH_HASH ?? ''; ?>';
        var _taoh_cdn_prefix = '<?php echo TAOH_CDN_PREFIX; ?>';
        var _taoh_ops_prefix = '<?php echo TAOH_OPS_PREFIX; ?>';
        var _taoh_ops_code = '<?php echo TAOH_OPS_CODE; ?>';
        var _action_url = '<?php echo TAOH_ACTION_URL; ?>';
        var _intao_db_version = '<?php echo max(TAOH_INDEXEDDB_VERSION, 1); ?>';
        var _taoh_site_ajax_url = '<?php echo taoh_site_ajax_url(1); ?>';
        var _taoh_dash_site_ajax_url = '<?php echo taoh_site_ajax_url(2); ?>';
        var _taoh_ajax_token = '<?php echo taoh_get_api_token(1); ?>';
        var _taoh_ajax_secret = '<?php echo TAOH_AJAX_SECRET; ?>';
        var _taoh_cache_chat_proc_url = '<?php echo TAOH_CACHE_CHAT_PROC_URL; ?>';
        var _taoh_cache_chat_url = '<?php echo TAOH_CACHE_CHAT_URL; ?>';
        //var _taoh_live_chat_url = '<?php echo TAOH_LIVE_NOW_URL; ?>';
        var _taoh_site_logo = '<?php echo TAOH_SITE_LOGO; ?>';
        var _taoh_site_sq_logo = '<?php echo TAOH_SITE_FAVICON; ?>';
        var _taoh_site_name = '<?php echo TAOH_SERVER_NAME; ?>';
        var _taoh_plugin_name = '<?php echo $lastParam; ?>';


        /* user info */
        var _is_logged_in = <?= json_encode(taoh_user_is_logged_in() ?? false); ?>;

        var _is_profile_complete = <?= json_encode((isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->profile_complete) && taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->profile_complete != 0) ?? false); ?>;
        if (_is_logged_in && _is_profile_complete) {

            var _taoh_get_skill = '<?php if(isset($h_session_user_info['skill'])) echo json_encode($h_session_user_info['skill']); ?>';
            var _taoh_get_company = '<?php if(isset($h_session_user_info['company'])) echo json_encode($h_session_user_info['company']); ?>';
            var _taoh_get_title = '<?php if(isset($h_session_user_info['title'])) echo json_encode($h_session_user_info['title']); ?>';
            var _taoh_get_country = '<?php if(isset($session_country)) echo $session_country; ?>';
            var _taoh_user_timezone = '<?php if(isset($user_timezone)) echo $user_timezone; ?>';

           // alert(_taoh_get_company)

        } else {

            var _taoh_get_skill = '<?php echo TAOH_DEFAULT_SKILL; ?>';
            var _taoh_get_company = '<?php echo TAOH_DEFAULT_COMPANY; ?>';
            var _taoh_get_title = '<?php echo TAOH_DEFAULT_TITLE; ?>';
            var _taoh_get_country = '<?php echo TAOH_DEFAULT_COUNTRY; ?>';
            var _taoh_user_timezone = '<?php echo defined('TAOH_DEFAULT_TIMEZONE') ? TAOH_DEFAULT_TIMEZONE : 'UTC'; ?>';
        }


        // User state
        var d_user_logged = _is_logged_in;     // 0 = not logged in, 1 = logged in
        var d_user_profile_completed = _is_profile_complete;         // 0 = incomplete, 1 = complete
        var d_user_profile_type = 'professional';
        if (_is_logged_in && _is_profile_complete) {
        d_user_profile_type = '<?php echo taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->type;?>'; // 'professional' or other types
        }
        // Event state
        var d_is_sponsorship_available = 0;       // 0 = no, 1 = yes
        var d_rsvp_done = 1;                      // 0 = not registered, 1 = registered
        var d_is_session_added = 0;               // 0 = no session, 1 = session added
        var d_is_exhibitor_added = 0;             // 0 = no exhibit, 1 = exhibit added
        var d_is_session_allowed = 0;             // 0 = no exhibit, 1 = session allowed
        var d_is_exhibitor_allowed = 0;             // 0 = no exhibit, 1 = exhibit allowed
        var d_is_event_live = 0;                  // 0 = not live, 1 = live

        // Links for CTAs (use empty string or actual URLs)
        var SETTINGSLINK = '<a href="<?php echo TAOH_SITE_URL_ROOT; ?>/settings">here</a>';
        var EVENTREGISTERLINK = '<a href="#" onclick="OpenRegsiterDropdown(event)">here</a>';
        var SESSIONREGISTERLINK = '<a href="#">here</a>';
        var EXHIBITREGISTERLINK = '<a href="#">here</a>';
        var EVENTSLINK = '<a href="<?php echo TAOH_SITE_URL_ROOT; ?>/events">here</a>';
        var COMMENTLINK = '<a href="#comments">here</a>';

        var CURRENT_PAGE = '<?php echo (defined('TAOH_SITE_CURRENT_APP_SLUG')) ? TAOH_SITE_CURRENT_APP_SLUG : ''; ?>';
        var CURRENT_INNER_PAGE = '<?php echo (defined('TAO_CURRENT_APP_INNER_PAGE')) ? TAO_CURRENT_APP_INNER_PAGE : ''; ?>';


        var CURRENT_BLOCK_VISITING = '';
        var CHANNEL_MEMBERS_COUNT = 0;
        var PARTICIPANT_MEMBERS_COUNT = 1;
        var PARTICIPANT_VISITING_COUNT = 0;
        var NO_MESSAGE_POSTED_IN_CHANNEL_FOR_5MIN = 0;
        var VIDEO_POSTED_RECENTLY = 0;
        var CURRENT_CHANNEL_ID = '';


        //alert(CURRENT_PAGE)
            function OpenRegsiterDropdown(e) {
                e.preventDefault();
                $('#choose_ticket').trigger('click');
                const $btn = $('#choose_ticket');
                const $menu = $('#ticket_list');
                $menu.toggleClass('show');

            }
    </script>

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script type="text/javascript" src="<?php echo TAOH_CDN_JS_PREFIX; ?>/form_validation.js"></script>
    <script type="text/javascript" src="<?php echo TAOH_CDN_JS_PREFIX; ?>/shares.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
    <script type="text/javascript" src="<?php echo TAOH_CDN_JS_PREFIX; ?>/luxon.min.js"></script>
    <script type="text/javascript" src="<?php echo TAOH_CDN_JS_PREFIX; ?>/taoh.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
    <script type="text/javascript" src="<?php echo TAOH_CDN_JS_PREFIX; ?>/hires.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
    <script type="text/javascript" src="<?php echo TAOH_CDN_PREFIX; ?>/assets/pagination.js"></script>
    <script type="text/javascript" src="<?php echo TAOH_CDN_JS_PREFIX; ?>/intao.js?v=<?php echo max(TAOH_INDEXEDDB_VERSION, 1); ?>"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.11/jquery.lazy.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/additional-methods.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script type="text/javascript" src="<?php echo TAOH_CDN_JS_PREFIX; ?>/jquery-confirm.min.js"></script>
    <script type="text/javascript" src="<?php echo TAOH_CDN_JS_PREFIX; ?>/jquery-mod.repeatable.js"></script>

    <link type="text/css"  rel="stylesheet" href="<?php echo TAOH_CDN_CSS_PREFIX; ?>/chatbot.css">
    <script type="text/javascript" src="<?php echo TAOH_CDN_JS_PREFIX; ?>/chatbot.js"></script>

    <link type="text/css" rel="stylesheet" href="<?php echo TAOH_CDN_PREFIX; ?>/assets/wertual/summernote/summernote-bs4.css">
    <script type="text/javascript" src="<?php echo TAOH_CDN_PREFIX; ?>/assets/wertual/summernote/summernote-bs4.js" referrerpolicy="origin"></script>
    <script type="text/javascript" src="<?php echo TAOH_CDN_JS_PREFIX; ?>/text_editor.js"></script>

    <link type="text/css" rel="stylesheet" href="<?php echo TAOH_CDN_CSS_PREFIX; ?>/emojionearea.min.css">
    <script type="text/javascript" src="<?php echo TAOH_CDN_JS_PREFIX; ?>/emojionearea.min.js"></script>

    <script type="text/javascript">
        var userInfoList = {};
        var roomInfoList = {};
        var room_activeTabId = 'connections-tab';
        var lastPCMsgCheckedTimestamp = 0;
        var lastNTWMsgCheckedTimestamp = 0;
        var ft_ntw_reFetchRequired = false;
        var ft_ntw_isProcessing = false;
        var imageExistMap = new Map();
        var ntw_unreadCountModified = {};
        var checkOfflineMessage = 1;
        var syncOfflineMessagesDelay = 3000; // 3 seconds

        var frm_message_id = 0;
        var frm_reply_view = false;
        var lastFRMMsgCheckedTimestamp = 0;
        var ft_frm_reFetchRequired = false;
        var ft_frm_isProcessing = false;
        var frm_replyCountMap = new Map();

        var lastFRMReplyMsgCheckedTimestamp = 0;
        var ft_frm_reply_isProcessing = false;

        var chat_activeTabId = 'channel';
    </script>

    <script type="text/javascript">
        <?php if(isset($_GET['intao_delete'])){ ?>
                intao_delete('<?php echo $_GET['intao_delete']; ?>');
        <?php } ?>
        <?php if(isset($_GET['intao_delete1'])){ ?>
                intao_delete1('<?php echo $_GET['intao_delete1']; ?>');
        <?php } ?>

        let job_delete = '<?php echo (isset($_GET['action_jobs']) ? 1 : 0);  ?>';
        let ask_delete = '<?php echo (isset($_GET['action_asks']) ? 1 : 0);  ?>';
        let event_delete = '<?php echo (isset($_GET['action_events']) ? 1 : 0);  ?>';
        //alert(event_delete)
        if(job_delete == 1 || ask_delete == 1 || event_delete == 1){
            getIntaoDb(dbName).then((db) => {
                return false; //kalpana added.
                let dataStoreName = '';
                if(job_delete == 1){
                dataStoreName = JOBStore;
                }else if(ask_delete == 1){
                dataStoreName = ASKStore;
                }else if(event_delete == 1){
                dataStoreName = EVENTStore;
                }
                const transaction = db.transaction(dataStoreName, 'readwrite');
                const objectStore = transaction.objectStore(dataStoreName);
                const request = objectStore.openCursor();
                request.onsuccess = (event) => {
                    const cursor = event.target.result;
                    if (cursor) {
                        const index_key = cursor.primaryKey;
                        if(
                        (job_delete == 1 && index_key.includes('job')) ||
                        (ask_delete == 1 && index_key.includes('ask')) ||
                        (event_delete == 1 && index_key.includes('event'))
                        ){

                       // alert(index_key);
                        objectStore.delete(index_key);
                        }
                        cursor.continue();
                    }
                };
            }).catch((err) => {
                console.log('Error in deleting data store');
            });
            if(job_delete == 1){
                <?php taoh_delete_local_cache('jobs',array("jobs_*","jobs_")); ?>
            }else if(ask_delete == 1){
                <?php taoh_delete_local_cache('asks',array("asks_*","asks_")); ?>
            }else if(event_delete == 1){
                <?php taoh_delete_local_cache('events',array("events_*","events_")); ?>
            }
            let newUrl = new URL(location.href);
            newUrl.searchParams.delete('action_jobs');
            newUrl.searchParams.delete('action_asks');
            newUrl.searchParams.delete('action_events');
            newUrl.searchParams.delete('cs');
            window.history.replaceState({}, document.title, newUrl.href);
        }
	</script>

    <?php
    if (defined('TAOH_CUSTOM_HEAD')) {
        echo TAOH_CUSTOM_HEAD;
    }
    ?>
</head>
<style>
    /* Color of the links BEFORE scroll */
    .navbar-scroll .nav-link,
    .navbar-scroll .navbar-toggler-icon,
    .navbar-scroll .navbar-brand {
        color: #262626;
    }

    /* Color of the navbar BEFORE scroll */
    .navbar-scroll {
        background-color: #FFC017;
    }

    /* Color of the links AFTER scroll */
    .navbar-scrolled .nav-link,
    .navbar-scrolled .navbar-toggler-icon,
    .navbar-scroll .navbar-brand {
        color: #262626;
    }

    /* Color of the navbar AFTER scroll */
    .navbar-scrolled {
        background-color: #fff;
    }

    /* An optional height of the navbar AFTER scroll */
    .navbar.navbar-scroll.navbar-scrolled {
        padding-top: auto;
        padding-bottom: auto;
    }

    .navbar-brand {
        font-size: unset;
        height: 3.5rem;
    }

    .navbar-collapse {
        -ms-flex-preferred-size: 100%;
        flex-basis: 100%;
        -ms-flex-positive: 1;
        flex-grow: 0;
        -ms-flex-align: center;
        align-items: center;
    }

    .text-center {
        align-items: center;
    }

    .modal.left .modal-dialog {
        position: fixed;
        right: 0;
        margin: auto;
        width: 320px;
        height: 100%;
        -webkit-transform: translate3d(0%, 0, 0);
        -ms-transform: translate3d(0%, 0, 0);
        -o-transform: translate3d(0%, 0, 0);
        transform: translate3d(0%, 0, 0);
    }

    .modal.left .modal-content {
        overflow: inherit;
    }
    .modal.left .modal-body {
        height: 100%;
        overflow-y: auto;
    }
    /*.modal.left .modal-content {*/
    /*    height: 100%;*/
    /*    overflow-y: auto;*/
    /*}*/

    .modal.right .modal-body {
        padding: 15px 15px 80px;
    }

    .modal.right.fade .modal-dialog {
        left: -320px;
        -webkit-transition: opacity 0.3s linear, left 0.3s ease-out;
        -moz-transition: opacity 0.3s linear, left 0.3s ease-out;
        -o-transition: opacity 0.3s linear, left 0.3s ease-out;
        transition: opacity 0.3s linear, left 0.3s ease-out;
    }

    .modal.right.fade.show .modal-dialog {
        right: 0;
    }

    /* ----- MODAL STYLE ----- */
    .modal-content {
        border-radius: 0;
        border: none;
    }

    .modal-header {
        border-bottom-color: #eeeeee;
        background-color: #fafafa;
    }

    .modal .flex-row a {
        color: black;
    }

    .modal .flex-row a:hover {
        color: red;
    }
     @media (min-width: 992px) {
        .navbar-nav .active {
            border-bottom: 2px solid black;
        }
    }

    @media (min-width: 1280px) {

        .bor-right {
            border-right: 2px solid #dee2e6 !important;
        }
    }

    .navbar-expand-lg ul li a {
        min-width: 80px;
    }

    @media only screen and (max-width: 992px) {
        .navbar-expand-lg ul li span {
            display: none;
        }

        .navbar-expand-lg ul li a {
            min-width: 49px;
        }

        .navbar-nav {
            display: -ms-flexbox;
            display: flex;
            -ms-flex-direction: row;
            flex-direction: row;
            padding-left: 0;
            margin-bottom: 0;
            list-style: none;
        }

        .navbar-nav .dropdown-menu {
            position: absolute;
            float: none;
        }
    }

    .main-box {
        border: 1px solid rgb(233, 230, 230);
        padding: 20px;
        border-radius: 8pt;
    }

    .borders {
        border-radius: 8pt;
        padding: 8pt;
        margin-left: 10px;
        height: 60px;
        text-align: center;
        width: 80px;
    }

    .borders:hover {
        box-shadow: 0px 5px 8px 0px rgba(105, 104, 104, 0.5)

    }

    .mod-title {
        font-size: 9px;
        text-align: center;
        line-height: 13px;
        font-weight: 600;
        padding-top: 7px;
    }

    .learn-title {
        margin-top: 5px;
    }

    .learn-title h3 {
        font-size: 17px;
        margin-left: -10px;
    }

    .btn-donate-info {
        font-size: 13px;
        letter-spacing: 2px;
        line-height: 20px;
        border: 2px solid #2d86eb;
        border-radius: 40px;
        color: #2d86eb;
        background: transparent;
        transition: all 0.3s ease 0s;
    }

    .btn-donate-info:hover {
        color: #FFF;
        background: #2d86eb;
        border: 2px solid #2d86eb;
    }

    /*====== off-canvas-menu-toggle =======*/
    .search-menu-toggle,
    .off-canvas-menu-toggle,
    .user-off-canvas-menu-toggle {
        display: none;
        cursor: pointer;
    }

    @media (max-width: 1199px) {
        .search-menu-toggle,
        .off-canvas-menu-toggle,
        .user-off-canvas-menu-toggle {
            display: inline-block;
        }
    }

    .search-menu-toggle:hover,
    .off-canvas-menu-toggle:hover,
    .user-off-canvas-menu-toggle:hover {
        color: #2d86eb;
    }

    /*====== off-canvas-menu =======*/
    .off-canvas-menu,
    .user-off-canvas-menu {
        position: fixed;
        top: 0;
        left: 0;
        width: 320px;
        height: 100%;
        background-color: #fff;
        overflow-x: hidden;
        z-index: 1035;
        -webkit-transition: all 0.4s cubic-bezier(0.7, 0, 0.2, 1);
        -moz-transition: all 0.4s cubic-bezier(0.7, 0, 0.2, 1);
        -ms-transition: all 0.4s cubic-bezier(0.7, 0, 0.2, 1);
        -o-transition: all 0.4s cubic-bezier(0.7, 0, 0.2, 1);
        transition: all 0.4s cubic-bezier(0.7, 0, 0.2, 1);
        -webkit-box-shadow: 0 0 40px rgba(82, 85, 90, 0.1);
        -moz-box-shadow: 0 0 40px rgba(82, 85, 90, 0.1);
        box-shadow: 0 0 40px rgba(82, 85, 90, 0.1);
        -webkit-transform: translateX(-100%);
        -moz-transform: translateX(-100%);
        -ms-transform: translateX(-100%);
        -o-transform: translateX(-100%);
        transform: translateX(-100%);
    }

    .off-canvas-menu.active,
    .user-off-canvas-menu.active {
        -webkit-transform: translateX(0);
        -moz-transform: translateX(0);
        -ms-transform: translateX(0);
        -o-transform: translateX(0);
        transform: translateX(0);
    }

    /*====== off-canvas-menu-list ========*/
    .off-canvas-menu-list > li {
        margin-bottom: 0;
        font-size: 15px;
    }

    .off-canvas-menu-list > li > a {
        color: #0d233e;
        font-weight: 500;
        text-transform: capitalize;
        display: -webkit-flex;
        display: -ms-flex;
        display: flex;
        -ms-flex-align: center;
        align-items: center;
        -ms-flex-pack: justify;
        justify-content: space-between;
        padding: 6px 20px;
    }

    .off-canvas-menu-list > li.active a {
        color: #2d86eb;
    }

    .off-canvas-menu-list > li .sub-menu {
        display: none;
        padding-left: 30px;
        text-transform: capitalize;
    }

    .off-canvas-menu-list > li .sub-menu li a {
        color: #6c727c;
    }

    .off-canvas-menu-list > li .sub-menu li a:hover {
        color: #2d86eb;
    }

    /*====== off-canvas-menu-close =======*/
    .off-canvas-menu-close,
    .user-off-canvas-menu-close {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 1;
    }

    .off-canvas-menu-close,
    .user-off-canvas-menu-close,
    .search-bar-close {
        cursor: pointer;
    }

    .off-canvas-menu-close .la,
    .user-off-canvas-menu-close .la,
    .search-bar-close .la {
        -webkit-transition: all 0.3s;
        -moz-transition: all 0.3s;
        -ms-transition: all 0.3s;
        -o-transition: all 0.3s;
        transition: all 0.3s;
    }

    .off-canvas-menu-close:hover .la,
    .user-off-canvas-menu-close:hover .la,
    .search-bar-close:hover .la {
        color: #2d86eb;
        -webkit-transform: rotate(90deg);
        -moz-transform: rotate(90deg);
        -ms-transform: rotate(90deg);
        -o-transform: rotate(90deg);
        transform: rotate(90deg);
    }

    .toast {
        transform: translateX(calc(100% + 30px));
        transition: all 0.5s cubic-bezier(0.68, -0.55, 0.25, 1.35);
    }

    .toast_active {
        transform: translateX(0);
    }

    .info_class {
        background-color: #e08600;
        color: #ffff;
        -webkit-box-shadow: 1px 1px 4px rgba(0, 0, 0, 0.4);
        box-shadow: 1px 1px 4px rgba(0, 0, 0, 0.4);
        padding: 10px 31px;
    }

    .success_class {
        background-color: green;
        color: #ffff;
        -webkit-box-shadow: 1px 1px 4px rgba(0, 0, 0, 0.4);
        box-shadow: 1px 1px 4px rgba(0, 0, 0, 0.4);
        padding: 10px 31px;
    }

    .toasterror_class {
        background-color: #df2100;
        color: #ffff;
        -webkit-box-shadow: 1px 1px 4px rgba(0, 0, 0, 0.4);
        box-shadow: 1px 1px 4px rgba(0, 0, 0, 0.4);
        padding: 10px 31px;
    }


    .toast_dismiss {
        font-size: 26px;
        float: right;
        padding-left: 25px;
        cursor: pointer
    }

    .info_icon {
        font-size: 19px;
        float: left;
        margin-top: 5px;
    }

    .toast-body {
        padding: 0px;
    }

</style>

<body >
<div id="loader" class="loader">
    <img src="<?php echo TAOH_LOADER_GIF; ?>" width="35">&nbsp;
    <span id="error_textmsg"></span>
</div>

<!-- Toast message -->
<div id="toast_container" aria-live="polite" aria-atomic="true" style="position: relative;">  <!-- class="toast-middle-con" when toast comes in middle add this to parent div -->
    <div class="dojo toast toast-bottom-right" style="" id="toast">
        <div id="toast_error"></div>
    </div>
    <div class="dojo-message-container" id="dojo-message-container">


    </div>
</div>


<!-- END Toast message -->
 <!--
toast-top-right : top- 10%;
toast-middle-right: bottom- 17%;
toast-bottom-right: bottom-0%;
toast-middle: center;
 -->