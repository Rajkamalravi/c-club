<?php
include_once 'functions.php';
$contoken = $data['token'];
$type = $data['type'];

$token = TAOH_API_SECRET;
if ( taoh_user_is_logged_in() ){
	$token = TAOH_API_TOKEN;
}

$res = get_follow( $contoken, $type);
//if($res->success) {
  $followed = $res->output->followed;
  $follow_count = $res->output->follow_count;

  $follow_class = $followed ? 'text-disabled' : 'text-success';
  $onclick = $followed ? '' : "doFollow('".$user_token."')";
 ?>
    <?php if($followed ) { ?>
        <span onclick="unFollow()" style="cursor:pointer" class="btn btn-primary">
            <span id="followCount">(<?php echo $follow_count;?>)</span>  Un Follow
        </span>
    <?php  } else { ?>
        <span onclick="doFollow()" style="cursor:pointer" class="btn btn-primary">
            <span id="followCount">(<?php echo $follow_count;?>)</span>  Follow
        </span>
    <?php } ?>
 <?php //} ?>
    

<script type="text/javascript">
  function doFollow() {
    var data = {
        'action': 'do_follow',
        'conttoken': '<?php echo $contoken; ?>',
        'conttype': '<?php echo $type; ?>'
		 };
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
            $('#followCount').html('<?php echo $follow_count + 1; ?>');
		}).fail(function(e) {
            console.log(e.status);
	  })
  }

  function unFollow() {
    var data = {
        'action': 'un_follow',
        'conttoken': '<?php echo $contoken; ?>',
        'conttype': '<?php echo $type; ?>'
		 };
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
            $('#followCount').html('<?php echo $follow_count - 1; ?>');
		}).fail(function(e) {
            console.log(e.status);
	  })
  }
</script>
