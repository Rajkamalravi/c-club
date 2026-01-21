<?php
//Comment actions
//echo'<pre>';print_r($_POST);die();
if(!isset($_POST['action'])) {
  taoh_set_error_message('Invalid Action');
  header("Location: ".$_POST['redirect']);
  taoh_exit();
}

if($_POST['action'] == 'save') {
  if(!$_POST['comment']) {
    taoh_set_error_message('Comments should not be empty!');
    header("Location: ".$_POST['redirect']);
    taoh_exit();
  } else {
    add_comments();
  }
}


//add comments
function add_comments()
{
  $error = 0;
  $success = '';
  if (taoh_user_is_logged_in()) {

    if (isset($_POST['parentid']) && $_POST['parentid'] != '') {
      $_POST['parentid'] = $_POST['parentid'];
    } else {
      $_POST['parentid'] = 0;
    }

    $_POST['comment'] = taoh_title_desc_encode($_POST['comment']);
    
    $remove = array($_POST['conttype'] . "_comments_" . $_POST['conttoken']);
    $taoh_vals = array(
        'token' => taoh_get_dummy_token(),
        'toenter' => $_POST,
        'ops' => 'add',
        'type' => 'comment',
        'mod' => 'core',
        'conttoken' => $_POST['conttoken'],
        'parentid' => $_POST['parentid'],
        'conttype' => $_POST['conttype'],
        'redis_action'=>'comments_post',
        'redis_store'=>'taoh_intaodb_asks',
        'cache' => array( 'remove' => $remove ),
       
    );
    $url = 'core.content.post';
    //echo taoh_apicall_post_debug($url,  $taoh_vals);die();
    $result = taoh_apicall_post($url, $taoh_vals);

    $result = json_decode($result);
    $success = $result->success;
  }


  if (isset($result) && $success) {
    taoh_set_success_message('Your comment posted successfully!');
    header("Location: " . $_POST['redirect']);
  } else {
    taoh_set_error_message('Something went wrong!');
    header("Location: " . $_POST['redirect']);
  }
}

?>
