<?php
//echo "hi";die;
$action = taoh_parse_url(1);

//echo $action;taoh_exit();
if($action == "settings") {
  include_once('settings_actions.php');
  die;

} else if($action == "contact") {
  include_once('contact_actions.php');
  die;

}else if($action == "bulk") {
  include_once('bulk_actions.php');
  die;

} else if($action == "flashcard") {
  include_once(TAOH_PLUGIN_PATH.'/core/learning/flashcards/actions.php');
  die;

} else if($action == "tips") {
  include_once(TAOH_PLUGIN_PATH.'/core/learning/tips/actions.php');
    die;
} else if($action == "blog") {
  include_once(TAOH_PLUGIN_PATH.'/core/learning/blog/actions.php');
  die;
}
else if($action == "newsletter") {
  include_once(TAOH_PLUGIN_PATH.'/core/learning/newsletter/actions.php');
  die;
}
else if($action == "candy") {
  include_once(TAOH_PLUGIN_PATH.'/core/candy/action.php');
  die;

} else if($action == "comments") {
  include_once(TAOH_PLUGIN_PATH.'/core/widgets/comments/action.php');
  die;
}else if($action == "feed_comments") {
  include_once(TAOH_PLUGIN_PATH.'/core/widgets/feeds/action.php');
  die;
}else if($action == "support") {
  include_once('support_actions.php');
  die;
}else if($action == "feedback") {
  include_once('feedback_actions.php');
  die;
}else if($action == "referral") {
  include_once('referral_actions.php');
  die;
}

//Actions from app folder
if (in_array($action, taoh_available_apps())) {
    $url = TAOH_PLUGIN_PATH."/app/".$action."/actions/main.php";
    if(file_exists($url)) {
        include_once $url;
        die;
    }
}

?>