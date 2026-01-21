<?php
if(isset($_REQUEST['taoh_action'])) {
    if(function_exists($_REQUEST['taoh_action'])) {
        if(taoh_is_wp() == 1) {
            add_action( 'wp_ajax_nopriv_'.$_REQUEST['taoh_action'], $_REQUEST['taoh_action'] );
            add_action( 'wp_ajax_'.$_REQUEST['taoh_action'], $_REQUEST['taoh_action'] );
        } else {
            return $_REQUEST['taoh_action']();taoh_exit();
        }
    } else {
        header("HTTP/1.0 404 NotFound");
        echo "No method defined";
    }
}

function taoh_user_profile_details(){
    $user_data = json_decode($_POST['user_data'], true);
    include_once('profile_data.php');
}