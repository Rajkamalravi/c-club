<?php
$current_app = taoh_parse_url(0);
$app_data = taoh_app_info($current_app);
//echo'<pre>';print_r($app_data);die();
$taoh_user_vars = taoh_user_all_info();
$taoh_url_vars = taoh_parse_url(2);
$share_link = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
@$conttoken = array_pop( explode( '-', $taoh_url_vars) );

if ( strlen( $conttoken ) < 10 || strlen( $conttoken ) > 20 || ! ctype_alnum( $conttoken ) ){ taoh_redirect( TAOH_SITE_URL_ROOT."/404" ); exit(); }

$enable_scout_job = 0;
$click_view = (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) ? 'click' : 'view';
$get_apply = false;

$ops = 'info';
$mod = 'jobs';
$taoh_call = 'jobs.job.get';
//$cache_name = $mod.'_'.$ops.'_' . $conttoken . '_' . taoh_scope_key_encode( $conttoken, 'global' );
$cache_name = 'job_details_' . $conttoken;

$taoh_vals = array(
    'token' => taoh_get_dummy_token(1),
    'ops' => $ops,
    'mod' => $mod,
    'cache_name' => $cache_name,
    'cache_time' => 7200,
   // 'cache' => array ( "name" => $cache_name,  "ttl" => 7200),
    'conttoken' => $conttoken,
	//'cfcc1d'=> 1, //cfcache newly added

);
//$taoh_vals[ 'cfcache' ] = $cache_name;
ksort($taoh_vals);
//echo taoh_apicall_get_debug($taoh_call, $taoh_vals);exit();
$response_get = taoh_apicall_get($taoh_call, $taoh_vals, TAOH_API_PREFIX, 1);
$response_decode = json_decode($response_get, true);
$response = $response_decode['output'];

$get_title = ucfirst(taoh_title_desc_decode($response['title']));
$meta_desc = taoh_title_desc_decode($response['description']);
$apply_link = isset($response['meta']['apply_link'])?$response['meta']['apply_link']:'';

/*
Configure for Google Search Console
*/
// TAO_PAGE_AUTHOR
// TAO_PAGE_DESCRIPTION
define( 'TAO_PAGE_DESCRIPTION', strip_tags($meta_desc));
// TAO_PAGE_IMAGE
define( 'TAO_PAGE_IMAGE', @$response[ 'image' ] );
// TAO_PAGE_TITLE
define( 'TAO_PAGE_TITLE', $get_title );
define( 'TAO_APP_NAME', $current_app );
// TAO_PAGE_TWITTER_SITE
// TAO_PAGE_ROBOT
//echo '<pre>';print_r($response);die();
define ( 'TAO_PAGE_ROBOT', 'index, follow' );

$additive = '';
/* if(isset($response['meta']['canonical_url']) && $response['meta']['canonical_url'] !=''){
	$additive = '<link rel="canonical" href="'.$response['meta']['canonical_url'].'"/>
	<meta name="original-source" content="'.$response['meta']['canonical_url'].'"/>';
	define ( 'TAO_PAGE_CANONICAL', $additive );
}else{
		$additive = '<link rel="canonical" href="'.$response['meta']['source'].'/'.TAOH_WERTUAL_SLUG.'/'.$app_data->slug.'/d/'.slugify2($get_title)."-".$conttoken.'"/>
		<meta name="original-source" content="'.$response['meta']['source'].'/'.TAOH_WERTUAL_SLUG.'/'.$app_data->slug.'/d/'.slugify2($get_title)."-".$conttoken.'"/>';
		// TAO_PAGE_CANONICAL
		define ( 'TAO_PAGE_CANONICAL', $additive );
} */

$site_info = $response['user_site_info'] ?? [];
if(isset($site_info['source']) && $site_info['source'] !='' && TAOH_SITE_URL_ROOT != $site_info['source']){
        $canonical_url = $site_info['source'].'/jobs/d/'.slugify2($get_title).'-'.$conttoken;
        $additive = '<link rel="canonical" href="'.$canonical_url.'"/>
        <meta name="original-source" content="'.$canonical_url.'"/>';
    }
    define ( 'TAO_PAGE_CANONICAL', $additive );

// TAO_PAGE_CATEGORY

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
    'slug' => TAO_APP_NAME,
);
//echo taoh_apicall_get_debug($taoh_call, $taoh_vals);exit();
$get_liked = json_decode( taoh_apicall_get($taoh_call, $taoh_vals), true );
$liked_arr = '';
if(isset($get_liked['conttoken_liked'])){
	$liked_arr = json_encode($get_liked['conttoken_liked']);
}
/* End check liked or not */

$taoh_home_url = (defined('TAOH_PAGE_URL') && TAOH_PAGE_URL)
        ? TAOH_PAGE_URL
        : (defined('TAOH_SITE_URL_ROOT') ? TAOH_SITE_URL_ROOT : '');

taoh_get_header( $additive );
?>
<style>
	.error{
		color: red;
	}
</style>
<section class="jobs-area pt-5x pb-40px">
	<div class="container">
		<div class="row">
			<div class="col-lg-9">
				<section class="jobs-area pt-10px pb-40px">
					<?php
					$from = 'detail';
					require_once('common_job_details.php'); ?>
				</section>
				<section class="hero-area bg-white shadow-sm overflow-hidden" style="display:none;" id="scroll_show">
					<div class="container">
						<div class="hero-content d-flex flex-wrap align-items-center justify-content-between">
							<div class="comment-tags">
								<div class="" id="collapseExample">
									<div class="card-body" id="scroll_id">
										<section class="mb-3 mt-3" id="share_icon">
											<?php echo taoh_comments_widget(array('conttoken'=> $conttoken, 'conttype'=> 'jobs', 'label'=> 'Comment')); ?>
										</section>
									</div>
								</div>
							</div>
						</div><!-- end hero-content -->
					</div><!-- end container -->
				</section><!-- end hero-area -->
			</div><!-- end col-lg-9 -->

			<div class="col-lg-3 mt-3">
				<div class="sidebar">

					<?php if (function_exists('jobs_networking_widget')) { jobs_networking_widget();  } ?>
					<?php if (function_exists('taoh_invite_friends_widget')) { taoh_invite_friends_widget($get_title,'jobs');  } ?>

				</div><!-- end sidebar -->
			</div><!-- end col-lg-3 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section><!-- end jobs-area -->
<div class="modal" id="exampleModal1" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content light-dark-card">
      <div class="modal-header light-dark-card">
        <h5 class="modal-title">Share</h5>
      </div>
      <div class="modal-body">
	  	<section class="mb-3 mt-3">
		  <?php echo taoh_share_widget(array('share_data'=> $share_link,'conttoken'=> $conttoken,'conttype'=> 'jobs','ptoken'=>$user_ptoken)); ?>
		</section>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div id="myModal" class="modal fade apply_modal" role="dialog">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header">
				<div class="d-flex justify-content-between align-items-center py-0" style="height: 90px; width: 100%;">
					<div>
						<a href="<?php echo ($taoh_home_url ?? '') . "/../"; ?>">
							<img src="<?php echo (defined('TAOH_PAGE_LOGO')) ? TAOH_PAGE_LOGO : TAOH_SITE_LOGO; ?>" alt="logo" class="modal-logo">
						</a>
					</div>
					<div>
						<button type="button" class="btn custom-apply-modal-btn" data-dismiss="modal">
							<i class="las la-angle-double-left"></i> BACK TO JOBS
						</button>
					</div>
				</div>

			</div>
			<!-- Modal body -->
			<div class="modal-body">
				<div class="job-details-panel job_apply_form" id="job-apply">
					<form id="fileUploadForm" method="POST" enctype="multipart/form-data" class="career-form MultiFile-intercepted">
						<div class="hidden">
							<input type="hidden" name="ops" value="apply"/>
							<input type="hidden" name="slug" value="" class="mod_conntoken" value=""/>
							<input type="hidden" name="to_email" class="to_email" value=""/>
							<input type="hidden" name="recruiter_fname" class="recruiter_fname" value=""/>
							<input type="hidden" name="opscode" value="<?php echo TAOH_OPS_CODE; ?>"/>
							<input type="hidden" name="ptoken" class="ptoken" value="<?php echo $user_ptoken; ?>"/>
							<input type="hidden" name="company_name" class="company_name" value=""/>
							<input type="hidden" name="position_title" class="position_title" value=""/>
							<input type="hidden" name="conttoken" value="" class="mod_conntoken"/>
						</div>
						<h1 class="pb-5 apply-form-heading">
							Good Luck ! <span><?php echo taoh_user_full_name(); ?></span>
						</h1>

						<div class="row">
							<div class="col-lg-6">
								<div class="mb-5">
									<label for="personal-details" class="title-lable">Personal Details</label>
									<div class="card apply-form-card px-3 py-4">
										<div class="row">
											<div class="col-lg-6  form-group">
												<label for="fname" class="sub-lable">First Name <span style="color:red;">*</span></label>

												<?php

													if (is_object($taoh_user_vars) && isset($taoh_user_vars->profile_complete)
													&& $taoh_user_vars->profile_complete == 0
													&& isset($taoh_user_vars->fname) && $taoh_user_vars->fname == TAOH_SITE_NAME_SLUG) {
													echo field_fname();
												} else {
													echo field_fname(is_object($taoh_user_vars) ? ($taoh_user_vars->fname ?? '') : '');
												}
												?>
											</div>
											<div class="col-lg-6  form-group">
												<label for="lname" class="sub-lable">Last Name <span style="color:red;">*</span></label>

												<?php
													if(isset($taoh_user_vars->profile_complete)
													&& $taoh_user_vars->profile_complete == 0 && isset($taoh_user_vars->fname) && $taoh_user_vars->fname == TAOH_SITE_NAME_SLUG)
													echo field_lname();
													else
													echo field_lname($taoh_user_vars->lname); ?>
											</div>
										</div>
									</div>
								</div>
								<div class="mb-5">
									<label for="contact-experience-details" class="title-lable">Contact & Experience Details</label>
									<div class="card apply-form-card px-3 py-4">
										<div class="row">
											<div class="col-lg-6 form-group">
												<label for="email" class="sub-lable">Email <span style="color:red;">*</span></label>
												<?php echo field_email($taoh_user_vars->email); ?>
											</div>

											<div class="col-lg-6 form-group">
												<label class="sub-lable">Place of Residence <span style="color:red;">*</span></label>
												<?php echo field_job_location($taoh_user_vars->coordinates,$taoh_user_vars->full_location, $taoh_user_vars->geohash); ?>
											</div>

											<div class="col-lg-6 form-group">
												<label for="company" class="sub-lable">Current Company</label>
												<?php echo field_company( ( isset( $taoh_user_vars->company ) && $taoh_user_vars->company ) ? $taoh_user_vars->company: '' ); ?>
											</div>
										</div>
									</div>
								</div>
								<div class="mb-5">
									<label for="fileToUpload" class="title-lable">Resume Upload <span style="color:red;">*</span></label>
									<div class="card apply-form-card px-3 py-4">
										<div class="row">
											<div class="col-md-12 py-4 apply-form-file-input-container">
												<svg class="file-input-icon" width="20" height="20" viewBox="0 0 28 30" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M16.1875 22.1463H11.8125C11.0852 22.1463 10.5 21.5291 10.5 20.7619V11.071H5.70391C4.73047 11.071 4.24375 9.83079 4.93281 9.10398L13.2508 0.324472C13.6609 -0.108157 14.3336 -0.108157 14.7438 0.324472L23.0672 9.10398C23.7562 9.83079 23.2695 11.071 22.2961 11.071H17.5V20.7619C17.5 21.5291 16.9148 22.1463 16.1875 22.1463ZM28 21.6849V28.1455C28 28.9127 27.4148 29.5299 26.6875 29.5299H1.3125C0.585156 29.5299 0 28.9127 0 28.1455V21.6849C0 20.9177 0.585156 20.3004 1.3125 20.3004H8.75V20.7619C8.75 22.5443 10.1227 23.9922 11.8125 23.9922H16.1875C17.8773 23.9922 19.25 22.5443 19.25 20.7619V20.3004H26.6875C27.4148 20.3004 28 20.9177 28 21.6849ZM21.2188 26.761C21.2188 26.1265 20.7266 25.6074 20.125 25.6074C19.5234 25.6074 19.0312 26.1265 19.0312 26.761C19.0312 27.3956 19.5234 27.9147 20.125 27.9147C20.7266 27.9147 21.2188 27.3956 21.2188 26.761ZM24.7188 26.761C24.7188 26.1265 24.2266 25.6074 23.625 25.6074C23.0234 25.6074 22.5312 26.1265 22.5312 26.761C22.5312 27.3956 23.0234 27.9147 23.625 27.9147C24.2266 27.9147 24.7188 27.3956 24.7188 26.761Z" fill="#7A7979"/>
												</svg>
												<input type="file" class="form-control py-2"  id="fileToUpload" name="fileToUpload">

											</div>
											<div id="responseMessage" style="display: none;"></div>
											<p id="error1" style="display:none; color:#FF0000;">
												Invalid Image Format! Image Format Must Be JPG, JPEG, PNG or GIF.
											</p>
											<p id="error2" style="display:none; color:#FF0000;">
												Maximum File Size Limit is 5MB.
											</p>
											<input type="hidden" name="resume_link" class="resume_link">
										</div>
									</div>
								</div>
								<div class="mb-5">
									<label for="cover-letter_id" class="title-lable">Cover Letter</label>
									<div class="card apply-form-card px-3 py-4">
										<div class="row">
											<div class="col-md-12 py-4">
												<textarea class="summernote" id="cover_letter_id" rows="10" cols="80" required></textarea>
											</div>
										</div>
									</div>
								</div>
								<div class="mb-5">
									<div class="row">
										<div class="col-md-12 mb-5">
											<label for="links" class="title-lable">Links</label>
											<div class="card apply-form-card px-3 py-4 w-100">
												<div class="row">
													<div class="col-md-6 form-group">
														<label for="linkedin_url" class="sub-lable">LinkedIn URL</label>
														<input type="text" class="form-control py-2 form--control" name="linkedin_url" placeholder="LinkedIn URL">
													</div>
													<div class="col-md-6 form-group">
														<label for="github_url" class="sub-lable">GitHub URL</label>
														<input type="text" class="form-control py-2 form--control" name="github_url" placeholder="GitHub URL">
													</div>
													<div class="col-md-6 form-group">
														<label for="port_url" class="sub-lable">Portfolio URL</label>
														<input type="text" class="form-control py-2 form--control" name="port_url" placeholder="Portfolio URL">
													</div>
													<div class="col-md-6 form-group">
														<label for="web_url" class="sub-lable">Website URL</label>
														<input type="text" class="form-control py-2 form--control"  name="web_url" placeholder="Website URL">
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="mb-5">
									<div class="row">
										<div class="col-md-12">
											<label for="addntl_info" class="title-lable" >Additional Information</label>
											<p class="" style="font-size: 20px; line-height: 24px;">Let the company know about your interest in the organization.</p>
											<textarea name="addntl_info" class="form-control form--control my-2" id="" rows="8"></textarea>
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-6 mb-4 job-detail-card">
								<div class="card apply-form-card sticky-detail" style="top: 30px;">
									<div class="card-body">
										<h6 class="text-uppercase px-3 py-2 company_name" style="background-color: #6DC0CB9C; width: fit-content; font-weight: 700;"></h6>
										<h4 class="card-title text-uppercase mt-3 mb-4 position_title" style="font-weight: 500;"></h4>
										<p class="text-uppercase pb-3 placeType" style="color: #9A9999; font-size: 15px; font-weight: 700; line-height: 18px;"></p>
										<div class="text_content">
											<p class="text-justify full-text" style="font-size: 18px; line-height: 21px; font-weight: 400; max-height: 600px; overflow-y: auto;">
											</p>
											<a href="#" class="text-uppercase toggle-link" style="cursor: pointer; color: #1C52A8; font-weight: 500; font-size: 18px;"> Show More</a>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div>
							<input type="hidden" name="enable_scout_apply" id="enable_scout_apply" value="" >
							<button type="submit" class="text-uppercase btn px-4 py-2 submit" style="border-radius: 12px; margin: 5rem 0; background-color: #1C52A8; color: #FFFFFF; font-size: 17px; font-weight: 500;">submit application</button>
						</div>
					</form>
				</div>
			</div>
			<!-- Modal footer -->
		</div>
	</div>
</div>
<?php require_once('jobs_common_js.php'); ?>
<script type="text/javascript">
let arr_cont = [];
let isLoggedIn = "<?php echo taoh_user_is_logged_in(); ?>";
let get_comment = '<?php echo $get_comm ?>';
let get_apply = '<?php echo $get_apply ?>';
let like_min = '<?php echo TAOH_SOCIAL_LIKES_THRESHOLD; ?>';
let conttoken = '<?php echo $conttoken; ?>';
//let jobResponse = '<?php //echo json_encode($jobDetailsResponse); ?>';
//let app_slug = '<?php echo TAO_APP_NAME; ?>';
let is_local = localStorage.getItem(app_slug+'_'+conttoken+'_liked');
let enable_scout_apply = '<?php echo $enable_scout_apply ?>';
let liked_arr = '<?php echo $liked_arr; ?>';

	$(document).ready(function(){
		<?php if(taoh_user_is_logged_in()) { ?>
			save_metrics('jobs','<?php echo $click_view ?>',conttoken);

		<?php } ?>


		var detail_like = get_liked_check(conttoken);
		$('.like_render').html(detail_like);
		$('[data-toggle="tooltip"]').tooltip();
		//setIndexedDbItem(conttoken,jobResponse,300000);
		//var job_data = getIndexedDbItemFromKey(conttoken);
		//console.log('-------job_datajob_data-------------',job_data);
	});
    //CKEDITOR.replace( 'cover_letter_id' );

	function job_apply(){
		$("#job-apply").slideToggle("slow");
	}
	if(localStorage.getItem("Status_"+conttoken))    {
        $('.alert-success').show();
        localStorage.removeItem("Status_"+conttoken);
    }

	$(document).on('click','.comment_go', function(event) {
		$("#scroll_show").show();
		$('html, body').animate({
			scrollTop: $("#scroll_id").offset().top
		}, 2000);
	});
	if(get_comment){
		$("#scroll_show").show();
		$('html, body').animate({
			scrollTop: $("#scroll_id").offset().top
		}, 2000);
	}
	if(get_apply){
		$(".job_apply_form").show();
		$('html, body').animate({
			scrollTop: $(".job_apply_form").offset().top
		}, 2000);
	}
</script>
<?php
taoh_get_footer();
?>