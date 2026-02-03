<?php
$current_app = taoh_parse_url(0);
$app_data = taoh_app_info($current_app);
$taoh_user_vars = taoh_user_all_info();
$taoh_url_vars = taoh_parse_url(2);
$share_link = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$click_view = (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) ? 'click' : 'view';
$taoh_url_vars_expl = explode('-', $taoh_url_vars);
$conttoken = taoh_sanitizeInput(array_pop($taoh_url_vars_expl));
define( 'TAO_PAGE_TYPE', $current_app );

/*
$ops = 'info';
$mod = 'asks';
$taoh_call = 'asks.ask.get';
$cache_name = $mod.'_'.$ops.'_' . $conttoken . '_' . taoh_scope_key_encode( $conttoken, 'global' );
$taoh_vals = array(
    'token' => taoh_get_dummy_token(1),
    'ops' => $ops,
    'mod' => $mod,
    'cache_name' => $cache_name,
    'cache_time' => 7200,
    'cache' => array ( "name" => $cache_name,  "ttl" => 7200),
    'conttoken' => $conttoken,
    
);
$taoh_vals[ 'cfcache' ] = $cache_name;
ksort($taoh_vals);
//echo taoh_apicall_get_debug($taoh_call, $taoh_vals);exit();
$response_get = taoh_apicall_get($taoh_call, $taoh_vals, TAOH_API_PREFIX, 1);
$response_decode = json_decode($response_get, true);
$response = $response_decode['output'];

$get_title = ucfirst(taoh_title_desc_decode($response['title']));
$meta_desc = taoh_title_desc_decode($response['meta']['description']);

//echo "========".$conttoken;


// TAO_PAGE_AUTHOR
// TAO_PAGE_DESCRIPTION
define( 'TAO_PAGE_DESCRIPTION', strip_tags($meta_desc));
// TAO_PAGE_IMAGE
define( 'TAO_PAGE_IMAGE', @$response[ 'image' ] );
// TAO_PAGE_TITLE
define( 'TAO_PAGE_TITLE', $get_title );
define( 'TAO_PAGE_TYPE', $current_app );
// TAO_PAGE_TWITTER_SITE
// TAO_PAGE_ROBOT
//echo '<pre>';print_r($response);die();
define ( 'TAO_PAGE_ROBOT', 'index, follow' );

$additive = '';
$additive = '<link rel="canonical" href="'.$response['meta']['source'].'/'.TAOH_WERTUAL_SLUG.'/'.($app_data ? $app_data->slug : '').'/d/'.slugify2($get_title)."-".$conttoken.'"/>
		<meta name="original-source" content="'.$additive.'"/>';
				// TAO_PAGE_CANONICAL
		define ( 'TAO_PAGE_CANONICAL', $additive );
// TAO_PAGE_CATEGORY
*/
$data = taoh_user_all_info();
$user_ptoken = $ptoken =  $data->ptoken ?? '';


if(isset($_GET['comments']) && $_GET['comments']){
	$get_comm = true;
}else{
	$get_comm = false;
}

$log_nolog_token = ( taoh_user_is_logged_in()) ? $ptoken : TAOH_API_TOKEN_DUMMY;


/* check liked or not */
$taoh_call = "system.users.metrics";
$taoh_vals = array(
    'mod' => 'system',
    'token' => taoh_get_dummy_token(),
    'slug' => TAO_PAGE_TYPE,
);
//echo taoh_apicall_get_debug($taoh_call, $taoh_vals);exit();
$get_liked = json_decode( taoh_apicall_get($taoh_call, $taoh_vals), true );	
$liked_arr = '';
if(isset($get_liked['conttoken_liked'])){
	$liked_arr = json_encode($get_liked['conttoken_liked']);
}
/* End check liked or not */ 


taoh_get_header( $additive );
?>
<style>
	.error{
		color: red;
	} 
</style>
<section class="asks-area pt-5x pb-40px">
	<div class="container">
		<div class="row">
			<div class="col-lg-9">
				<section class="asks-area pt-10px pb-40px">
					<?php 
					$from = 'detail';
					require_once('asks_detail_content.php'); ?>
				</section>
				
			</div><!-- end col-lg-9 -->

			<div class="col-lg-3">
				<div class="sidebar pt-2">
				
				<?php if (function_exists('taoh_user_profile_short')) { taoh_user_profile_short($owner_ptoken);  } ?>
			<?php if (function_exists('taoh_invite_friends_widget')) { taoh_invite_friends_widget($raw_title,'asks');  } ?>
			<div class="sidebar-widget <?php echo ($ask_info_flag) ? '':'hide'; ?>">
						<div class="card card-item p-4">
	                        <h3 class="fs-17 pb-3">Asks Info</h3>
	                        <div class="divider"><span></span></div>
	                        <div class="sidebar-items-list pt-3">
													<?php
													if (isset($locn) && $locn && $locn!= 'Not Specified'){
													echo "
													<div class=\"media media-card media--card media--card-2\">
														<div class=\"media-body\">
															<h5><a href=\"#\">Location</a></h5>
															<small class=\"meta d-block lh-20\">
																<span class=\"pr-1\">$locn</span>
															</small>
														</div>
													</div><!-- end media -->
														";
													}
													if (isset($rolechat) && $rolechat && $rolechat!= 'Not Specified'){
													echo "
													<div class=\"media media-card media--card media--card-2\">
														<div class=\"media-body\">
															<h5><a href=\"#\">Ask Type</a></h5>
															<small class=\"meta d-block lh-20\">
																<span class=\"pr-1\"><a href=\"/".TAOH_ASKS_URL."/chat/rolechat/$rolechat/$rolechat_key\">$rolechat</a></span>
															</small>
														</div>
													</div><!-- end media -->
													";
													}
													?>
													</ul>
												</div>
											</div>
										</div>
										
					<?php if (function_exists('taoh_jusask_widget')) { taoh_jusask_widget();  } ?>
				</div><!-- end sidebar -->
			</div><!-- end col-lg-3 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section><!-- end asks-area -->
<div class="modal" id="exampleModal1" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content light-dark-card">
      <div class="modal-header light-dark-card">
        <h5 class="modal-title">Share</h5>
      </div>
      <div class="modal-body">
	  	<section class="mb-3 mt-3">
		  <?php echo taoh_share_widget(array('share_data'=> $share_link,'conttoken'=> $conttoken,'conttype'=> 'asks','ptoken'=>$user_ptoken)); ?>
		</section>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php require_once('ask_common_js.php'); ?>
<script type="text/javascript">
let arr_cont = [];
//let isLoggedIn = "<?php //echo taoh_user_is_logged_in(); ?>";
let get_comment = '<?php echo $get_comm ?>';
let like_min = '<?php echo TAOH_SOCIAL_LIKES_THRESHOLD; ?>';
let conttoken = '<?php echo $conttoken; ?>';
let askResponse = '<?php echo json_encode($askDetailsResponse); ?>';
let app_slug = '<?php echo TAO_PAGE_TYPE; ?>';
let is_local = localStorage.getItem(app_slug+'_'+conttoken+'_liked');

let liked_arr = '<?php echo $liked_arr; ?>';

	$(document).ready(function(){
		
		
		var detail_like = get_liked_check(conttoken);
		$('.like_render').html(detail_like);
		$('[data-toggle="tooltip"]').tooltip();
		
	});
	function delete_asks_into(){
		getIntaoDb(dbName).then((db) => {
			let dataStoreName = ASKStore;
			const transaction = db.transaction(dataStoreName, 'readwrite');
			const objectStore = transaction.objectStore(dataStoreName);
			const request = objectStore.openCursor();
			request.onsuccess = (event) => {
			const cursor = event.target.result;
			if (cursor) {
				const index_key = cursor.primaryKey;
				if(index_key.includes('ask')) 
				{
				objectStore.delete(index_key);
				}
				cursor.continue();
			}
			};
		}).catch((err) => {
			console.log('Error in deleting data store');
		});
	}

	if(localStorage.getItem("Status_"+conttoken))    {
        $('.alert-success').show();
        localStorage.removeItem("Status_"+conttoken);
    }

		
	if(get_comment){
		$("#scroll_show").show();
		$('html, body').animate({
			scrollTop: $("#scroll_id").offset().top
		}, 2000);
	}

</script>
<?php
taoh_get_footer();
?>