<?php
function taoh_sponsor_slider_widget($eventtoken) {
  include_once('events/sponsor.php');
}
function taoh_follow_widget($data) {
  include_once('follow/follow.php');
}

function taoh_likes_widget($data) {
    include_once('likes/likes.php');
}

function taoh_comments_widget($data) {
    include_once('comments/comments_details.php');
    include_once('comments/comments_post.php');
}

function taoh_feeds_comments_widget($data) {
  include_once('feeds/comments_details.php');
  include_once('feeds/comments_post.php');
}

function taoh_comments_widget_get($data) {
  include_once('comments/comments_details.php');
}
function taoh_comments_widget_post($data) {
  include_once('comments/comments_post.php');
}
function taoh_share_widget($data) {
  include_once('share/share.php');
}

function taoh_social_share_widget($data) {
  include_once('share/social_share.php');
}
function taoh_readables_widget($widget_type = '') {
  //echo "============";
  include_once('reads/readables.php');
}

function taoh_obviousbaba_widget() {
  include_once('reads/obviousbaba.php');
}

function taoh_jusask_widget($widget_type = '') {
  include_once('reads/jusask.php');
}

function taoh_leftmenu_widget() {
  include_once('general/leftmenu.php');
}

function taoh_video_widget($data) {
  include_once('general/video.php');
}

function taoh_copynshare_widget() {
  include_once('general/copynshare.php');
}

function taoh_calendar_widget($data) {
  include_once('general/calendar.php');
}

function taoh_reads_category_widget() {
  include_once('reads/categories.php');
}

function taoh_reads_search_widget() {
  include_once('reads/search.php');
}

function taoh_newsletter_search_widget() {
  include_once('reads/newsletter_search.php');
}

function taoh_ads_widget( $ads = 0 ) {
  include_once('general/ads.php');
  taoh_ads_general($ads);
}

function taoh_stats_widget() {
  include_once('general/stats.php');
}

function taoh_tao_widget() {
  //include_once('general/tao.php');
  //kalpana commened in order to remove tip block from everywhere
}

function taoh_wemet_video_widget() {
  include_once('general/wemet.php');
}

function taoh_jobs_widget() {
  include_once('apps/jobs.php');
}

function taoh_free_monthly_jobs_widget() {
  include_once('apps/free_monthly_jobs.php');
}

function taoh_free_promotional_jobs_widget() {
  include_once('apps/free_promotional_jobs.php');
}

function taoh_asks_widget() {
  include_once('apps/asks.php');
}

function taoh_wordSlide_widget() {
  include_once('general/wordSlider.php');
}

function taoh_invite_widget() {
 // include_once('invite/invites.php');
}

function taoh_invite_friends_widget($title,$app,$data='') {
  //echo"=============";
  if(taoh_user_is_logged_in()) {
  include_once('invite/invite_friends.php');
  }
}

function taoh_get_recent_jobs($widget_type = '') {
  include_once('general/recent_jobs.php');
}

function taoh_jobs_networking_widget() {
  include_once('general/jobs_networking.php');
}

function taoh_new_ads_widget() {
  include_once('general/new_ads.php');
}

function taoh_new_common_ads_widget($app,$type='square',$qty=1) {
  include_once('ads/common_ads.php');
}

function taoh_user_profile_short($ptoken){
  include_once('general/short_profile.php');
}

function taoh_type_widget($data = '') {
  include_once('general/type_widget.php');
}

function taoh_recent_event_widget() {
  include_once('events/recent_events.php');
}

function taoh_recent_multiple_event_widget($exclude_eventtoken='') {
  include_once('events/recent_multiple_events.php');
}


function taoh_recent_events_full_display($exclude_eventtoken='') {
  include_once('events/recent_events_full_display.php');
}
?>