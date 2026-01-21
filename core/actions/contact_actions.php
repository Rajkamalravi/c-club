<?php
if ( ! taoh_user_is_logged_in() ){
	$subject = "HiresQuery";
	if (isset($_GET['q'])) $subject = "$subject, from ".$_GET['q'];
	header("Location: mailto:info@tao.ai?subject=$subject");
  taoh_exit();
}


if ( is_array($_POST) ){
    $postdata = http_build_query(
        array(
            'mod' => 'contact',
            'token' => taoh_get_dummy_token(),
            'subject' => $_POST['we_subject'] . " Posted under:" . $_POST['we_category'],
            'message' => $_POST['we_message'] . "<br />" . $_POST['we_locn'],
        )
    );
  $cmd = "users.contact.reach";
  $goto_get = "";
  $opts = array('http' =>
      array(
          'method'  => 'POST',
          'header'  => 'Content-Type: application/x-www-form-urlencoded',
          'content' => $postdata,
      )
  );
  $context  = stream_context_create($opts);
  $url = TAOH_API_PREFIX."/$cmd"."$goto_get";
  $result = taoh_url_get_content($url, false, $context);
	taoh_set_success_message("Email Sent!");
  taoh_redirect(TAOH_ABOUT_URL);
  taoh_exit();
}
?>
