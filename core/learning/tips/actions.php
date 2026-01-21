<?php
if ( taoh_user_is_logged_in() ){
  $_POST[ 'token' ] = TAOH_API_TOKEN;
  $_POST[ 'mod' ] = 'tips';
//echo taoh_apicall_post_debug("core.tips.post", $_POST);die();
  taoh_apicall_post("core.tips.post", $_POST);


  taoh_set_success_message("Tips Posted!");
  header( "Location: ".TAOH_TIPS_URL );taoh_exit();
}
taoh_exit();
?>
