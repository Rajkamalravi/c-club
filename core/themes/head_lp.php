<!DOCTYPE html>
<?php
        $taoh_site_favicon = (defined('TAOH_PAGE_FAVICON')) ? TAOH_PAGE_FAVICON : TAOH_SITE_FAVICON;
?>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="article-content-type" content="Digital <?php echo TAOH_APP_SLUG; ?>"/>
    <META HTTP-EQUIV="Access-Control-Allow-Origin" CONTENT="<?php echo TAOH_DASH_PREFIX;?>">
    <meta name="article-type" content="premium"/>
    <meta name="author" property="article:author" content="<?php echo ( defined( 'TAO_PAGE_AUTHOR' ) ) ?  TAO_PAGE_AUTHOR:'#TeamTAO';  ?>" />
    <meta name="Description" content="<?php echo ( defined( 'TAO_PAGE_DESCRIPTION' ) ) ?  TAO_PAGE_DESCRIPTION:taoh_site_description(); ?>">
    <meta name="item-name" content="<?php echo ( defined( 'TAO_PAGE_TITLE' ) ) ?  TAO_PAGE_TITLE:TAOH_SITE_TITLE;  ?>"/>
    <?php echo ( defined( 'TAO_PAGE_KEYWORDS' )  && TAO_PAGE_KEYWORDS ) ? '<meta name="keywords" content="'.TAO_PAGE_KEYWORDS.'"/>':''; ?>
    <meta name="msapplication-config" content="none" />
    <?php echo ( defined( 'TAO_PAGE_CANONICAL' ) ) ? TAO_PAGE_CANONICAL:''; ?>    
    <?php echo ( defined( 'TAO_PAGE_TYPE' ) ) ? '<meta name="page-type" content="'.TAO_PAGE_TYPE.'"/>':''; ?>
    <?php echo ( defined( 'TAO_PAGE_CATEGORY' ) ) ? '<meta name="page-category-name" content="'.TAO_PAGE_CATEGORY.'"/>':''; ?>
    <meta name="parsely-title" content="<?php echo ( defined( 'TAO_PAGE_TITLE' ) ) ?  TAO_PAGE_TITLE:TAOH_SITE_TITLE;  ?>" />
    <meta name="robots" content="<?php echo ( defined('TAO_PAGE_ROBOT') )? TAO_PAGE_ROBOT:'index,follow'; ?>" />
    <meta name="sailthru.author" content="<?php echo ( defined( 'TAO_PAGE_AUTHOR' ) ) ?  TAO_PAGE_AUTHOR:'#TeamTAO';  ?>" />
    <meta name="sailthru.title" content="<?php echo ( defined( 'TAO_PAGE_TITLE' ) ) ?  TAO_PAGE_TITLE:TAOH_SITE_TITLE;  ?>" />
    <meta name="theme-color" content="#131318">
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:creator" content="<?php echo ( defined( 'TAO_PAGE_TWITTER_SITE' ) ) ?  TAO_PAGE_TWITTER_SITE:'@TAOHQ'; ?>" />
    <meta name="twitter:data1" content="<?php echo ( defined( 'TAO_PAGE_AUTHOR' ) ) ?  TAO_PAGE_AUTHOR:'#TeamTAO';  ?>" />
    <meta name="twitter:description" content="<?php echo ( defined( 'TAO_PAGE_DESCRIPTION' ) ) ?  TAO_PAGE_DESCRIPTION:taoh_site_description(); ?>">
    <meta name="twitter:domain" content="<?php echo ( defined( 'TAOH_SITE_SOURCE' ) ) ?  TAOH_SITE_SOURCE:'https://tao.ai'; ?>">
    <meta name="twitter:image:alt" content="<?php echo ( defined( 'TAO_PAGE_TITLE' ) ) ?  TAO_PAGE_TITLE:TAOH_SITE_TITLE;  ?>">
    <meta name="twitter:image" content="<?php echo $taoh_site_favicon; ?>"/>
    <meta name="twitter:label1" content="Written by <?php echo ( defined( 'TAO_PAGE_AUTHOR' ) ) ?  TAO_PAGE_AUTHOR:'#TeamTAO';  ?>" />
    <meta name="twitter:site" content="<?php echo ( defined( 'TAO_PAGE_TWITTER_SITE' ) ) ?  TAO_PAGE_TWITTER_SITE:'@TAOHQ'; ?>">
    <meta name="twitter:title" property="og:title" content="<?php echo ( defined( 'TAO_PAGE_TITLE' ) ) ?  TAO_PAGE_TITLE:TAOH_SITE_TITLE;  ?>">
    <meta name="twitter:url" content="<?php echo $_SERVER[ 'REQUEST_SCHEME' ]."://".$_SERVER[ 'HTTP_HOST' ].$_SERVER[ 'REQUEST_URI' ]; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="fb:app_id" content="1271794846576386" />
    <meta property="og:description" content="<?php echo ( defined( 'TAO_PAGE_DESCRIPTION' ) ) ?  TAO_PAGE_DESCRIPTION:taoh_site_description(); ?>" />
    <meta property="og:image:height" content="637" />
    <meta property="og:image:type" content="image/jpeg" />
    <meta property="og:image:width" content="300" />
    <meta property="og:image" content="<?php echo $taoh_site_favicon; ?>" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:site_name" content="<?php echo ( defined( 'TAOH_SITE_NAME_SLUG' ) ) ?  TAOH_SITE_NAME_SLUG:TAOH_PLUGIN_PATH_NAME;  ?>" />
    <meta property="og:title" content="<?php echo ( defined( 'TAO_PAGE_TITLE' ) ) ?  TAO_PAGE_TITLE:TAOH_SITE_TITLE;  ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" property="schema:url" content="<?php echo $_SERVER[ 'REQUEST_SCHEME' ]."://".$_SERVER[ 'HTTP_HOST' ].$_SERVER[ 'REQUEST_URI' ]; ?>" />
    <!-- <meta name="google-signin-client_id" content="963068544994-mv75dfaik3c7sll3chnp601jonns7s9v.apps.googleusercontent.com">
    <script src="https://apis.google.com/js/platform.js" async defer></script>  -->
    <!-- <script src="https://accounts.google.com/gsi/client" async defer></script>   -->
    <!-- Favicon -->
    <link rel="icon" href="<?php echo $taoh_site_favicon; ?>" type="image/png">
    <link rel="apple-touch-icon" href="<?php echo $taoh_site_favicon; ?>" sizes="180x180">
    <link rel="icon" href="<?php echo $taoh_site_favicon; ?>" sizes="32x32" type="image/png">
    <link rel="icon" href="<?php echo $taoh_site_favicon; ?>" sizes="16x16" type="image/png">
    <link rel="icon" href="<?php echo $taoh_site_favicon; ?>">
    <title><?php echo ( defined( 'TAO_PAGE_TITLE' ) ) ?  TAO_PAGE_TITLE:TAOH_SITE_TITLE;  ?></title>

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/font-awesome-line-awesome/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.tao.ai/script/css/play3/style.css">
    <link rel="stylesheet" href="<?php echo TAOH_CDN_CSS_PREFIX; ?>/reads_lp_css.css">
    <link rel="stylesheet" href="<?php echo TAOH_CDN_CSS_PREFIX; ?>/jet_pack.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.0.2/dist/css/tom-select.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <script>
      var _intao_db_version = '<?php echo max(TAOH_INDEXEDDB_VERSION, 1); ?>';
      var _taoh_site_ajax_url = '<?php echo taoh_site_ajax_url(1); ?>';
      var _taoh_dash_site_ajax_url = '<?php echo taoh_site_ajax_url(2); ?>';
      var _taoh_site_url_root = '<?php echo TAOH_TEMP_SITE_URL; ?>';
      var _taoh_plugin_name = '<?php echo TAOH_TEMP_SITE_URL; ?>';
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>  
	<script src="https://cdn.jsdelivr.net/npm/tom-select@2.0.2/dist/js/tom-select.complete.min.js"></script>
    <script type="text/javascript" src="<?php echo TAOH_CDN_JS_PREFIX; ?>/taoh.js"></script>
    <script type="text/javascript" src="<?php echo TAOH_CDN_JS_PREFIX; ?>/hires.js"></script>
    <script type="text/javascript" src="<?php echo TAOH_CDN_JS_PREFIX; ?>/intao.js"></script>
    <script type="text/javascript" src="<?php echo TAOH_CDN_PREFIX; ?>/assets/pagination.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.11/jquery.lazy.min.js"></script>
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
    <?php if ( defined ( 'TAOH_CUSTOM_HEAD' ) ) { echo TAOH_CUSTOM_HEAD; } ?>
  </head>
  <style>
    a:hover{
      text-decoration: none;
    }
    .css-font {
      font-size: 1.17em;
    }
    .post-box-title, .widget-container h3{
      overflow: hidden; 
      display: -webkit-box; 
      -webkit-line-clamp: 3; 
      -webkit-box-orient: vertical; 
      text-overflow: ellipsis;
    }
    .column2 li.other-news, .list-box li.other-news, .wide-box li.other-news, .cat-tabs-wrap li.other-news {
      display: flex;
      align-items: center;
    }
    .post-thumbnail a {
      text-align: center;
    }
    /* .cover-image-container {
        position: relative;
        overflow: hidden;
        max-height: 165px;
    }
    .cover-image-container .bg-image {
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: cover;
        background-size: cover;
        border-radius: 12px;
        z-index: 0;
    }
    .glass-overlay{
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.1); 
        backdrop-filter: blur(10px); 
        z-index: 0;
        border-radius: 12px;
    }
    .cover-image-container .main-image {
        position: relative;
        width: 100%; 
        object-fit: contain; 
        border-radius: 12px;
        z-index: 1;
        max-height: 165px;
    } */
    #tabbed-widget ul.tabs li.active a {
      background: #F88C00;
      color: #ffffff;
    }
    .cat-tabs-header {
      padding: 8px 10px 8px;
      min-height: 46px;
      height: 100%;
    }
    .cat-tabs-header li {
      padding-left: 20px;
      padding-right: 20px;
    }
    .cat-tabs-header li.active {
      background: #F88C00;
      color: #ffffff;
      border-radius: 19px;
    }
    .cat-tabs-header li.active a {
      color: #ffffff;
    }
    @media only screen and (max-width: 1045px) {
    #wrapper.boxed #main-content {
        padding: 15px !important;
      }
    }
  </style>
