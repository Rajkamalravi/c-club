<?php

//print_r($_POST);

if($_POST['action'] == "delete_my_rsvp") {

  //echo"==========".taoh_get_dummy_token();
 // print_r($_POST);die();
    $taoh_vals = array(
        //"cache_remove"=>1,
        "mod" => 'events',
        'rsvptoken' => $_POST['rsvptoken'],
        'token' => taoh_get_dummy_token(),
        'ops' => 'delete',
        'status' => 'confirm',
        'cache_required' => 0,
        //'cache' => array ( "name" => taoh_p2us("events.rsvp").'_'.$_POST['rsvptoken'].'_delete', "ttl" => 3600),
        //'cache' => array("remove"=>array('event_rsvp_'.$_POST['ptoken']'_'.$_POST['rsvptoken'],'event_rsvp_*')),
    );

	$url = 'events.rsvp.get';
  //die('-----------');
  //echo taoh_apicall_get_debug($url,  $taoh_vals);die();
  $data = taoh_apicall_get($url,  $taoh_vals);

  header('Content-Type: application/json; charset=utf-8');
  //echo json_encode($data);
  echo $data;
  die();
}


if($_POST['action'] == "addrsvp") {

/*
  $taoh_call = 'users.app.add';
  $taoh_call_type = 'post';
  $taoh_vals = array(
    'mod' => 'events',
    'token' => taoh_get_api_token(),
    'toenter' => $_POST,
    'ops' => $_POST['action'],
  );
  $result = taoh_apicall2( $taoh_call, $taoh_call_type, $taoh_vals );
*/
//$postdata = http_build_query(
    $postdata = [
        'mod' => 'events',
        'token' => taoh_get_dummy_token(),
        'toenter' => $_POST,
        'ops' => $_POST['action'],
    ];

    $cmd = "events.rsvp.post";
    $goto_get = "";
    $opts = array( 'http' =>
      array(
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => $postdata,
       )
     );

    $context = stream_context_create( $opts );
    $url = TAOH_API_PREFIX."/$cmd"."$goto_get";
    //echo taoh_apicall_post_debug($cmd,  $postdata);die();
    $result = taoh_apicall_post( $cmd,  $postdata);
    //$result = 0;
    //$result = taoh_url_get( $url, false, $context );
    //$result = file_get_contents( $url, false, $context);
     //print_r($result);
     //die();

  if(@$_POST['debug']) {
    echo '<div style="word-break: break-all; font-family: monospace;">{<b>"Endpoint": '.$url.'</b>,
      <br/> "<b>Postdata</b>": '.'<pre>';
      print_r($postdata);
      echo '</pre>,<br> "<b>Result</b>": '.$result.'}</div>';
    die();
  }

  taoh_set_success_message("RSVP request has been successfully received");
  //header("Location: ".TAOH_SITE_URL_ROOT."/events/rsvp/".$_POST['eventtoken']."#".$_POST['slug']);
  header("Location: ".TAOH_SITE_URL_ROOT."/events/confirmation/".$_POST['eventtoken']."/".$_POST['slug']);
}

?>
