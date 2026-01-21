<?php


$taoh_id = taoh_parse_url(2); //conttoken
$current_app = taoh_parse_url(0);
$slug = taoh_parse_url(0);

/*if ( taoh_user_is_logged_in() ){

if($response['success']){
    if(isset($_COOKIE[ "taoh_networking_".$taoh_id."_title"]))
    $_COOKIE[ "taoh_networking_".$taoh_id."_title"] = $response['title'];
    if(isset($_COOKIE[ "taoh_networking_".$taoh_id."_short"]))
    $_COOKIE[ "taoh_networking_".$taoh_id."_short"] = $response['description'];
    if(isset($_COOKIE[ "taoh_networking_".$taoh_id."_global"]))
    $_COOKIE[ "taoh_networking_".$taoh_id."_global"] = $response['publish_global'];
    if(isset($_COOKIE[ "taoh_networking_".$taoh_id."_logo"]))
    $_COOKIE[ "taoh_networking_".$taoh_id."_logo"] = $response[''];
    if(isset($_COOKIE[ "taoh_networking_".$taoh_id."_app"]))
    $_COOKIE[ "taoh_networking_".$taoh_id."_app"] = $response['type_slug'];
    if(isset($_COOKIE[ "taoh_networking_".$taoh_id."_type"]))
    $_COOKIE[ "taoh_networking_".$taoh_id."_type"] = $response['type'];
    if(isset($_COOKIE[ "taoh_networking_".$taoh_id."_ownerptoken"]))
    $_COOKIE[ "taoh_networking_".$taoh_id."_ownerptoken"] = $response['ptoken'];
}


$forward_url =TAOH_SITE_URL_ROOT."/".TAOH_NETWORKPAGE_NAME."/d/".$taoh_id;
taoh_redirect( $forward_url );taoh_exit();

}else{
    $forward_url = TAOH_SITE_URL_ROOT."/$current_app";
    taoh_redirect( $forward_url );taoh_exit();
}*/
?>