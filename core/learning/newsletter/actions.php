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

  $_POST['title'] = taoh_title_desc_encode($_POST['title']);
  $_POST['subtitle'] = taoh_title_desc_encode($_POST['subtitle']);
  $_POST['description'] = taoh_title_desc_encode($_POST['description']);
  $_POST['excerpt'] = taoh_title_desc_encode($_POST['excerpt']);

  if ( defined( 'TAOH_API_TOKEN' ) ){
    $_POST['description'] = htmlentities($_POST['description']);

    $taoh_vals = array(
        'token' => taoh_get_dummy_token(),
        'toenter' => $_POST,
        'ops' => 'add',
        'type' => 'newsletter',
        'mod' => 'core',
        'cache' => array('remove' => array("newsletter_" . $_POST['conttoken'], "newsletter_*")),
    );
    //print_r($taoh_vals);die;
    $url = 'core.content.post';  
    // echo taoh_apicall_post_debug($url, $taoh_vals);taoh_exit();
    $result = taoh_apicall_post($url,  $taoh_vals);

    $result = json_decode($result);
    //Sample Response
    //[success] => 1 [output] => stdClass Object ( [conttoken] => 6kdhb8to3yjv [title] => [type] => flashcard [blurb] => stdClass Object ( [body] => sssss ) [category] => [created] => 221028075021
    $success = $result->success;
    $conttoken = $result->output->conttoken;
    //print_r($result);die;
  }


  if( isset( $result ) && $success ) {
    taoh_set_success_message('Awesome,  Success!');
    header("Location: ".TAOH_READS_URL."/newsletter/d/".$conttoken);
  } else {
    taoh_set_error_message('Something went wrong!');
    header("Location: ".TAOH_READS_URL."/newsletter/post?post=".date('Ymd'));
  }
}

?>
