<?php
$conttoken = $data['conttoken'];
$type = $data['type'];
$token = TAOH_API_SECRET;
if ( taoh_user_is_logged_in() ){
	$token = TAOH_API_TOKEN;
}
//$api = TAOH_SITE_CONTENT_GET."?mod=core&ops=get&type=stats&conttoken=$conttoken&conttype=$type&token=$token";
//$req = file_get_contents($api);
$url = 'core.content.get';
$taoh_vals = array(
  'mod' => 'core',
  'token' => taoh_get_dummy_token(1),
  'ops' =>  'get',
  'type' => 'stats',
  'conntoken' => $conttoken,
  'conttype' => $type,
 
);
// $cache_name = $url.'_stats_' . hash('sha256', $url . serialize($taoh_vals));
// $taoh_vals[ 'cfcache' ] = $cache_name;
// $taoh_vals[ 'cache_name' ] = $cache_name;
ksort($taoh_vals);

$req = taoh_apicall_get($url, $taoh_vals);
$res = json_decode($req);

if($res->success) {
  $likes = $res->output->likes;
  $views = $res->output->views;
  $liked = $res->output->liked;
  $liked_class = $liked ? 'text-danger' : 'text-gray';
  $onclick = $liked ? '' : "doLike('".$conttoken."')";
 ?>
  <i style="cursor:pointer" class="la la-eye text-info"></i> <?php echo $views;?>
  <?php if($liked) { ?>
    <i  class="la la-heart text-danger"></i> <span id="likeCount"><?php echo $likes;?></span>
  <?php } else { ?>
    <i  onclick="doLike()" style="cursor:pointer" class="la la-heart text-gray"></i> <span id="likeCount"><?php echo $likes;?></span>
<?php }
} ?>
<script type="text/javascript">
  function doLike() {
    var data = {
			 'taoh_action': 'taoh_like',
			 'conttoken': '<?php echo $conttoken; ?>',
			 'type': '<?php echo $type; ?>',
		 };
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
      $('#likeCount').html('<?php echo $likes + 1; ?>');
      $('.la-heart').removeClass('text-gray').addClass('text-danger');
		}).fail(function(e) {
      console.log(e.status);
	  })
  }
</script>
