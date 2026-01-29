<?php
//flashcard actions

if(!isset($_POST['action'])) {
  taoh_set_error_message('Invalid Action');
  header("Location: ".TAOH_READS_URL.'/post');
  taoh_exit();
}


//flashcard save function
if($_POST['action'] == "save") {
  if(!$_POST['title'] || !$_POST['description']) {
    taoh_set_error_message('Please fill all the fields!');
    header("Location: ".TAOH_READS_URL.'/post');
    return false;
  }
  $error = 0;


  if ( taoh_user_is_logged_in() ){
    $_POST['title'] = taoh_title_desc_encode($_POST['title']);
    $_POST['description'] = taoh_title_desc_encode($_POST['description']);
    $remove = array("read_".$_POST['conttoken'],"read_*","recipe_*","tags_*","recipe_","tags_");
    $redis_action = 'read_add';
    if(isset($_POST['conttoken']) && !empty($_POST['conttoken'])) {
    $redis_action = 'read_update';
    }
    $taoh_vals = array(
        'token' => taoh_get_dummy_token(),
        'toenter' => $_POST,
        'ops' => 'add',
        'type' => 'reads',
        'mod' => 'core',
        'redis_action' => $redis_action,
        'redis_store' => 'taoh_intaodb_reads',
        'cache' => array('remove' => $remove),
      //'debug' => '1',
    );
    $intao_delete_name = '?intao_delete=read';
    if(isset($_POST['recipe_title']) && !empty($_POST['recipe_title'])) {
      $taoh_vals['recipe_name'] = $_POST['recipe_title'];
      unset($taoh_vals['toenter']['recipe_title']);
      $intao_delete_name = '?intao_delete=recipe';
    }
    if(isset($_POST['tags']) && !empty($_POST['tags'])) {
      $taoh_vals['tags'] = $_POST['tags'];
      unset($taoh_vals['toenter']['tags']);
      $intao_delete_name = '?intao_delete=tags';
    }
    if(isset($_POST['tags']) && !empty($_POST['tags']) && isset($_POST['recipe_title']) && !empty($_POST['recipe_title'])) {
      $intao_delete_name = '?intao_delete=recipe&intao_delete1=tags';
    }
    $url = 'core.content.post';
    //echo taoh_apicall_post_debug($url,  $taoh_vals);die();
    $result = taoh_apicall_post($url,  $taoh_vals);

    $result = json_decode($result,true);
    taoh_delete_local_cache('core',$remove);
    //Sample Response
    //[success] => 1 [output] => stdClass Object ( [conttoken] => 6kdhb8to3yjv [title] => [type] => flashcard [blurb] => stdClass Object ( [body] => sssss ) [category] => [created] => 221028075021
    $success = $result['success'];
    $conttoken = $result['output']['conttoken'];

  }

  if( $_POST[ 'debug' ] ) {
    echo '<div style="word-break: break-all; font-family: monospace;">{<b>"Endpoint": '.$url.'</b>,<br/> "<b>Postdata</b>": '.$postdata.',<br> "<b>Result</b>": '.print_r($result).'}</div>';
    die();
  }

  if( isset( $result ) && $success ) {
    taoh_set_success_message('Awesome,  Success!');
    header("Location: ".TAOH_READS_URL."/blog/".( ( isset( $result['output']['title']  ) && $result['output']['title'] ) ? taoh_slugify($result['output']['title']).'-':'' ).$conttoken.$intao_delete_name);
  } else {
    taoh_set_error_message('Something went wrong!');
    header("Location: ".TAOH_READS_URL."/post?post=".date('Ymd'));
  }
}

if($_POST['action'] == "blog_delete") {
  //https://devapi.tao.ai/core.content.get?mod=core&type=reads&token=hT93oaWC&mod=core&ops=delete&status=confirm&conttoken=wfkdap91mfk9
  $conttoken = ( isset( $_POST['conttoken'] ) && $_POST['conttoken'] )? $_POST['conttoken']:'';
  $ops = ( isset( $_POST['ops'] ) && $_POST['ops'] )? $_POST['ops']:'';
  // $taoh_call = "core.content.get";
  $taoh_call = "core.content.post";
  $remove = array("read_".$_POST['conttoken'],"read_*","recipe_*","tags_*","recipe_","tags_","users_","users_*");
  $taoh_vals = array(
      'mod'=>'core',
      'token'=>taoh_get_dummy_token(),
      'ops'=>$ops,
      'type'=>'reads',
      'conttoken'=>$conttoken,
      'status'=>'confirm',
      'redis_action'=>'read_update',
      'cache' => array( 'remove' => $remove ),
  );
  //$taoh_vals[ 'cfcache' ] = hash('sha256', $taoh_call . serialize($taoh_vals));
  $taoh_call_type = "get";
  // echo taoh_apicall_post_debug($taoh_call, $taoh_vals);die;
  $data = taoh_apicall_post( $taoh_call, $taoh_vals );
  taoh_delete_local_cache('core',$remove);
  header('Content-Type: application/json; charset=utf-8');
  echo trim($data);
  die();
}

?>
