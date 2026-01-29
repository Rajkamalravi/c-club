<?php
//include_once('head.php');

$current_app = TAOH_SITE_CURRENT_APP_SLUG;
$taoh_home_url = ( defined( 'TAOH_PAGE_URL' ) && TAOH_PAGE_URL ) ? TAOH_PAGE_URL:TAOH_SITE_URL_ROOT;


//echo $current_app;
$about_url = $taoh_home_url."/about";
if ( $current_app != TAOH_PLUGIN_PATH_NAME ) $about_url = $taoh_home_url."/".$current_app."/about";

include_once('head.php');


//echo $current_app;
$about_url = $taoh_home_url."/about";
if ( $current_app != TAOH_PLUGIN_PATH_NAME ) $about_url = $taoh_home_url."/".$current_app."/about";

$data = taoh_user_all_info();
$ptoken = $data->ptoken;

?>
<!-- Navbar -->
<!-- Navbar -->

<style>
.notifier {
  position: relative;
  display: inline-block;
}

.bell {
  font-size: 26px;
  color: #FFF;
  transition: 0.3s;
}

.bell:hover {
  color: #EF476F;
}

.badges {
  position: absolute;
  top: -5px;
  left: 24px;
  padding: 0 5px;
  font-size: 16px;
  line-height: 22px;
  height: 22px;
  background: #EF476F;
  color: #FFF;
  border-radius: 11px;
  white-space: nowrap;
}

.notifier.green .badges {
  background: #06D6A0;
}

.notifier.green .bell:hover {
  color: #06D6A0;
}

.notifier.new .badges {
  animation: pulse 2s ease-out;
  animation-iteration-count: infinite;
}

@keyframes pulse {
  40% {
    transform: scale3d(1, 1, 1);
  }

  50% {
    transform: scale3d(1.3, 1.3, 1.3);
  }

  55% {
    transform: scale3d(1, 1, 1);
  }

  60% {
    transform: scale3d(1.3, 1.3, 1.3);
  }

  65% {
    transform: scale3d(1, 1, 1);
  }
}
</style>

<script>
function openNav() {
  document.getElementById("mySidebar").style.width = "250px";
  document.getElementById("main").style.marginLeft = "250px";
}

function closeNav() {
  document.getElementById("mySidebar").style.width = "0";
  document.getElementById("main").style.marginLeft= "0";
}


</script>