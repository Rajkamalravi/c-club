<?php
//flashcard actions

if(!isset($_POST['action'])) {
  taoh_set_error_message('Invalid Action');
  header("Location: ".TAOH_FLASHCARD_URL.'/post');
  taoh_exit();
}

//flashcard save function
if($_POST['action'] == "save") {
  $error = 0;

  // $token_avail = file_get_contents(TAOH_API_PREFIX."/flashcard.token.get?mod=".TAOH_APP_SLUG."&token=".TAO_API_TOKEN);
  // if (!$token_avail){
  //   taoh_set_error_message('You are used your all free token');
  //   header("Location: /hires/learning/flashcard/post");
  //   taoh_exit();
  // }

  if ( defined( 'TAOH_API_TOKEN' ) ){
    $_POST['description'] = htmlentities($_POST['description']);

    $url = 'core.content.post';
    $taoh_vals = array(
        'token' => taoh_get_dummy_token(),
        'toenter' => $_POST,
        'ops' => 'add',
        'type' => 'flash',
        'mod' => 'core'
    );
    $result = taoh_apicall_post($url,  $taoh_vals);

    $result = json_decode($result);
    //Sample Response
    //[success] => 1 [output] => stdClass Object ( [conttoken] => 6kdhb8to3yjv [title] => [type] => flashcard [blurb] => stdClass Object ( [body] => sssss ) [category] => [created] => 221028075021
    $success = $result->success;
    $conttoken = $result->output->conttoken;
    $category = @$result->output->category[0];

    // print_r($result);
    // die();

  }

  if( $_POST[ 'debug' ] ) {
    echo '<div style="word-break: break-all; font-family: monospace;">{<b>"Endpoint": '.$url.'</b>,<br/> "<b>Postdata</b>": '.$postdata.',<br> "<b>Result</b>": '.print_r($result).'}</div>';
    die();
  }


  if( isset( $result ) && $success ) {
    taoh_set_success_message('Awesome,  Success!');
    header("Location: ".TAOH_FLASHCARD_URL."/$category/$conttoken");
  } else {
    taoh_set_error_message('Something went wrong!');
    header("Location: ".TAOH_FLASHCARD_URL."/post");
  }
}

if($_POST['action'] == "flash_delete") {
  //https://devapi.tao.ai/core.content.get?mod=core&type=reads&token=hT93oaWC&mod=core&ops=delete&status=confirm&conttoken=wfkdap91mfk9
  $conttoken = ( isset( $_POST['conttoken'] ) && $_POST['conttoken'] )? $_POST['conttoken']:'';
  $ops = ( isset( $_POST['ops'] ) && $_POST['ops'] )? $_POST['ops']:'';
  $taoh_call = "core.content.get";
  $taoh_vals = array(
      'mod'=>'core',
      'token'=>taoh_get_dummy_token(1),
      'ops'=>$ops,
      'type'=>'flash',
      'conttoken'=>$conttoken,
      'status'=>'confirm'      
  );
  $cache_name = $taoh_call.'_flash_' . hash('sha256', $taoh_call . serialize($taoh_vals));
  //$taoh_vals[ 'cfcache' ] = $cache_name;
  $taoh_vals[ 'cache_name' ] = $cache_name;
  $taoh_vals[ 'cache' ] = array ( "name" => $cache_name );
  ksort($taoh_vals);
  
  $taoh_call_type = "get";
  $data = taoh_apicall_get( $taoh_call, $taoh_vals );
  header('Content-Type: application/json; charset=utf-8');
  echo trim($data);
  die();
}

?>
