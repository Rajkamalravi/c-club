<?php


if ( ! defined ( 'TAO_PAGE_TITLE' ) ) { define ( 'TAO_PAGE_TITLE', "Comprehensive Open Jobs List at ".TAOH_SITE_NAME_SLUG.": Explore and Apply to a Wide Range of Job Opportunities" ); }
if ( ! defined ( 'TAO_PAGE_DESCRIPTION' ) ) { define ( 'TAO_PAGE_DESCRIPTION', "Browse our comprehensive jobs list featuring a diverse range of job opportunities across industries. Find the perfect job that matches your skills and interests, chat with recruiters and easily apply through our user-friendly platform at ".TAOH_SITE_NAME_SLUG.". Start your job search today and take the next step in your career." ); }
if ( ! defined ( 'TAO_PAGE_KEYWORDS' ) ) { define ( 'TAO_PAGE_KEYWORDS', "Job openings at ".TAOH_SITE_NAME_SLUG.", Employment opportunities at ".TAOH_SITE_NAME_SLUG.", Job listings at ".TAOH_SITE_NAME_SLUG.", Job board at ".TAOH_SITE_NAME_SLUG.", Job search platform at ".TAOH_SITE_NAME_SLUG.", Job finder at ".TAOH_SITE_NAME_SLUG.", Job database at ".TAOH_SITE_NAME_SLUG.", Job search engine at ".TAOH_SITE_NAME_SLUG.", Job match at ".TAOH_SITE_NAME_SLUG.", Job applications at ".TAOH_SITE_NAME_SLUG.", Apply for jobs at ".TAOH_SITE_NAME_SLUG.", Job search website at ".TAOH_SITE_NAME_SLUG.", Find a job at ".TAOH_SITE_NAME_SLUG.", Job seekers at ".TAOH_SITE_NAME_SLUG.", Job alerts at ".TAOH_SITE_NAME_SLUG.", Explore job opportunities at ".TAOH_SITE_NAME_SLUG ); }
define( 'TAO_PAGE_ROBOT', 'noindex, nofollow' );

taoh_get_header();

$current_app = taoh_parse_url(0);
$app_data = taoh_app_info($current_app);
$taoh_user_vars = $data = taoh_user_all_info();
//$profile_complete = $data->profile_complete;
$empty = 0;
if (is_object($data)) {
    $profile_complete = $data->profile_complete ?? null;
    $ptoken = $user_ptoken = $data->ptoken ?? null;
} else {
    error_log("Unexpected data type for user data: " . print_r($data, true));
    $profile_complete = null;
    $ptoken = null;
}
//echo taoh_parse_url(0);taoh_exit();
//$data = taoh_user_all_info();
//$ptoken = $user_ptoken = $data->ptoken;
$share_link = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
define( 'TAO_PAGE_TYPE', ($app_data?->slug ?? '') );
$log_nolog_token = ( taoh_user_is_logged_in()) ? $ptoken : TAOH_API_TOKEN_DUMMY;
/* check liked or not */
$taoh_call = "system.users.metrics";
$taoh_vals = array(
    'mod' => 'system',
    'token' => taoh_get_dummy_token(),
    'slug' => TAO_PAGE_TYPE,
	'cache_name' => TAO_PAGE_TYPE.'_metrics',
	'ttl' => 3600,

);
//echo taoh_apicall_get_debug($taoh_call, $taoh_vals);exit();
$get_liked = json_decode( taoh_apicall_get($taoh_call, $taoh_vals), true );	
$liked_arr = '';
if(isset($get_liked['conttoken_liked'])){
	$liked_arr = json_encode($get_liked['conttoken_liked']);
}
/* End check liked or not */


$_SESSION[TAOH_ROOT_PATH_HASH.'_eligible_scouted_jobs'] = array();
$_SESSION[TAOH_ROOT_PATH_HASH.'_scouted_jobs'] = array();
$_SESSION[TAOH_ROOT_PATH_HASH.'_applied_jobs'] = array();

if(taoh_user_is_logged_in()){


	$taoh_call = "jobs.scout.list";
	$taoh_vals = array(
	'mod' => 'jobs',
	'ptoken' => $ptoken,
	'secret' => TAOH_API_SECRET,
	'cache_required' => 0,
	//'debug'=> 1,
	//'cache' => array ( "name" => taoh_p2us($taoh_call).'_'.$conttoken.'_apply_status', "ttl" => 3600),
	);
	//echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );die;
	$data = taoh_apicall_get($taoh_call, $taoh_vals);
	$data_res = json_decode($data,1);

	$_SESSION[TAOH_ROOT_PATH_HASH.'_eligible_scouted_jobs'] = array();

	if($data_res['success']){
		$_SESSION[TAOH_ROOT_PATH_HASH.'_eligible_scouted_jobs'] = $data_res['output'];
		
	}


	//if(!isset($_SESSION[TAOH_ROOT_PATH_HASH.'_scouted_jobs']) || !isset($_SESSION[TAOH_ROOT_PATH_HASH.'_applied_jobs'])){
    $taoh_call = "jobs.applied.job";
    $taoh_vals = array(
        'mod' => 'jobs',
        'ptoken' => $ptoken,
        'token' => taoh_get_dummy_token(),
        'secret' => TAOH_API_SECRET,
        'cache_required' => 0,
    );
		//echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );die;
		$data = taoh_apicall_get($taoh_call, $taoh_vals);
		$data_res = json_decode($data,1);

		$scout_applied = array();
		$jobs_applied = array();
		if($data_res['success']){
			foreach($data_res['output']['scout'] as $key=>$val){
				$scout_applied[$val['conttoken']] = $val['apply_id'];
			}

			foreach($data_res['output']['jobs'] as $keys=>$vals){
				$jobs_applied[$vals['conttoken']] = $vals['apply_id'];
			}
			$_SESSION[TAOH_ROOT_PATH_HASH.'_scouted_jobs'] = $scout_applied;
			$_SESSION[TAOH_ROOT_PATH_HASH.'_applied_jobs'] = $jobs_applied;
		}
	//}
}


$currencies = json_encode(taoh_get_currency_symbol('',true));

//print_r($_SESSION);die();

?>
<style>
	/* .ts-control{
		border-radius: 15px;
  		border-color: #2557A7 !important;
	} */
	.item{
		color: #515151;
	}
	.no_result {
		text-align: center;
	}
	.no_result_div{
		display: none;
		justify-content: center;
	}
	.no_result img {
		max-width: 50%;
		height: auto;
	}
	.no_result h1 {
		font-size: 28px;
		color: #333;
		margin: 40px 0;
	}
	.no_result p {
		margin: 40px 0;
	}
	.no_result ul {
		margin: 40px 0;
	}
	.options {
		list-style: none;
		padding: 0;
		margin: 0;
		display: flex;
		justify-content: center;
		gap: 20px;
	}
	.options li {
		font-size: 14px;
		color: #333;
	}
	#dateRangeInputs{
		display: none;
	}
	.error{
		color: red;
	}
	.w-50{
		width:120px;
	}

	/* === Jobs Page UX === */

	/* Header row — single line: tabs | search | CTA */
	.jobs-header-row {
		display: flex;
		align-items: center;
		gap: 16px;
		min-height: 40px;
	}

	/* Tab row — underline tabs (events-style) */
	.jobs-tab-row {
		display: flex;
		align-items: center;
		border-bottom: 0 !important;
		gap: 0;
		flex-shrink: 0;
		margin: 0;
		padding: 0;
	}
	.jobs-tab-row .nav-item {
		list-style: none;
	}
	.jobs-tab-row .nav-link {
		display: inline-block;
		font-size: 16px;
		font-weight: 500;
		color: #333333 !important;
		padding: 8px 20px;
		text-align: center;
		border: none !important;
		border-radius: 0 !important;
		border-bottom: 3px solid transparent !important;
		background: transparent !important;
		transition: color 0.2s, border-color 0.2s;
		white-space: nowrap;
		text-decoration: none !important;
		line-height: 1.2;
		box-sizing: border-box;
	}
	.jobs-tab-row .nav-link:hover {
		background: transparent !important;
		color: #2557A7 !important;
		border-bottom: 3px solid #2557A7 !important;
	}
	.jobs-tab-row .nav-link.active {
		background: transparent !important;
		color: #2557A7 !important;
		border-bottom: 3px solid #2557A7 !important;
		font-weight: 500;
	}

	/* Inline search */
	.jobs-header-search {
		position: relative;
		flex-shrink: 0;
		margin-left: auto;
		display: flex;
		align-items: center;
	}
	.jobs-header-search-input {
		width: 180px;
		height: 38px;
		padding: 8px 36px 8px 14px;
		border: 1px solid #bbb !important;
		border-radius: 20px !important;
		font-size: 13px;
		background: #f9f9f9;
		color: #333;
		transition: width 0.3s ease, border-color 0.2s, box-shadow 0.2s;
		outline: none;
		font-family: inherit;
		-webkit-appearance: none;
		-moz-appearance: none;
		appearance: none;
	}
	.jobs-header-search-input::placeholder {
		color: #999;
		font-weight: 400;
	}
	.jobs-header-search-input:focus {
		width: 220px;
		border-color: #2479D8;
		box-shadow: 0 0 0 3px rgba(36,121,216,0.08);
		background: #fff;
	}
	.jobs-header-search-btn {
		position: absolute;
		right: 2px;
		top: 50%;
		transform: translateY(-50%);
		background: none;
		border: none;
		padding: 4px 8px;
		font-size: 16px;
		color: #888;
		cursor: pointer;
		transition: color 0.2s;
		line-height: 1;
	}
	.jobs-header-search-btn:hover {
		color: #333;
	}
	.jobs-header-search-input:focus ~ .jobs-header-search-btn {
		color: #007bff;
	}

	/* Post CTA button */
	.jobs-post-cta {
		display: inline-flex;
		align-items: center;
		gap: 5px;
		white-space: nowrap;
		flex-shrink: 0;
		background: #0a9e01 !important;
		color: #fff !important;
		font-weight: 600 !important;
		font-size: 13px !important;
		border-radius: 20px !important;
		padding: 7px 18px !important;
		text-decoration: none !important;
		transition: background 0.2s, box-shadow 0.2s;
		box-shadow: 0 1px 3px rgba(10,158,1,0.15);
	}
	.jobs-post-cta:hover {
		background: #088a01 !important;
		color: #fff !important;
		text-decoration: none !important;
		box-shadow: 0 2px 6px rgba(10,158,1,0.25);
	}

	/* Expandable search panel (row 2) */
	.jobs-search-panel {
		max-height: 0;
		overflow: hidden;
		transition: max-height 0.35s ease, margin-top 0.35s ease, opacity 0.3s ease;
		margin-top: 0;
		opacity: 0;
	}
	.jobs-search-panel.open {
		max-height: 300px;
		margin-top: 14px;
		opacity: 1;
	}
	/* Hide duplicate search field inside panel (already in row 1) */
	.jobs-search-panel .form-row > .col.col-md-3 {
		display: none;
	}
	/* Expand remaining fields — equal widths */
	.jobs-search-panel .form-row > .col-md-4 {
		flex: 0 0 38%; max-width: 38%;
		display: block !important;
	}
	.jobs-search-panel .form-row > .col-md-3.date-range-dropdown {
		flex: 0 0 38%; max-width: 38%;
		display: block !important;
	}
	.jobs-search-panel .form-row > .col-auto.col-md-2 {
		flex: 0 0 auto;
	}
	/* Polish the search.php form inside the panel */
	.jobs-search-panel .search-filter-section {
		margin: 0 auto;
	}
	.jobs-search-panel .form-control {
		border-radius: 15px;
		font-size: 13px;
		height: 38px;
		padding: 8px 12px 8px 30px;
		border: 1px solid #bbb;
		background: #f9f9f9;
		transition: border-color 0.2s, box-shadow 0.2s;
	}
	.jobs-search-panel .form-control:focus {
		border-color: #2479D8;
		box-shadow: 0 0 0 3px rgba(36,121,216,0.08);
		background: #fff;
	}
	.jobs-search-panel .btn-primary {
		border-radius: 15px;
		font-size: 13px;
		font-weight: 600;
		padding: 8px 20px;
	}

	/* External badge */
	.jobs-external-link { opacity: 0.85; }
	.jobs-external-badge {
		font-size: 10px;
		color: #999;
		display: inline-block;
		margin-top: 2px;
		margin-left: 4px;
	}

	/* Responsive */
	@media (max-width: 768px) {
		.jobs-header-row {
			gap: 8px;
			overflow-x: auto;
			-webkit-overflow-scrolling: touch;
			scrollbar-width: none;
		}
		.jobs-header-row::-webkit-scrollbar { display: none; }
		.jobs-tab-row .nav-link { font-size: 12px; padding: 5px 10px; }
		.jobs-header-search-input { width: 120px; font-size: 12px; padding: 0 32px 0 10px; }
		.jobs-header-search-input:focus { width: 150px; }
		.jobs-post-cta { font-size: 11px !important; padding: 6px 14px !important; }
		.jobs-search-panel .form-control { font-size: 12px; }
	}
	@media (max-width: 480px) {
		.jobs-header-search-input { width: 100px; }
		.jobs-header-search-input:focus { width: 130px; }
	}
</style>

<div class="mobile-app">

<?php if(!taoh_user_is_logged_in()) { ?>
<!--======================================
        START HERO AREA
======================================-->
<section class="hero-area pt-80px pb-80px hero-bg-jobs">
    <div class="overlay"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h4 class="pb-1 text-white">PROFESSIONAL</h4>
                    <h2 class="section-title pb-3 text-white">Hire &amp; Get Hired through Our Community</h2>
                    <p class="section-desc text-white">Explore job openings, internships, and freelance positions — or post your own to reach event-connected talent.</p>
                    <div class="hero-btn-box  fit py-4 dark-btn">
                        <!-- <a href="<?php echo TAOH_LOGIN_URL; ?>" class="btn theme-btn theme-btn mr-2">Sign up today</a> -->
                        <a onclick="localStorage.removeItem('isCodeSent')" href="javascript:void(0);"
                         class="btn theme-btn theme-btn mr-2 login-button " aria-pressed="true" data-toggle="modal" data-target="#config-modal">Sign up today</a>
                    </div>
                </div><!-- end hero-content -->
            </div><!-- end col-lg-9 -->
            <div class="col-lg-6 text-right">
            <div class="hero-content">
                    <h4 class="pb-1 text-white">EMPLOYER</h4>
                    <h2 class="section-title pb-3 text-white">Hire from Our Events &amp; Community</h2>
                    <p class="section-desc text-white">Post a job and reach professionals who attend career events — engaged, vetted, and ready to work.</p>
                    <div class="hero-btn-box py-4 light-btn fit">
                        <!-- <a href="<?php echo TAOH_LOGIN_URL; ?>" class="btn theme-btn theme-btn mr-2">Find your next Hire</a> -->
                        <a onclick="localStorage.removeItem('isCodeSent')" href="javascript:void(0);"
                         class="btn theme-btn theme-btn mr-2 login-button " aria-pressed="true" data-toggle="modal" data-target="#config-modal">Find your next Hire</a>
                    </div>
                </div><!-- end hero-content -->
            </div>
        </div><!-- end row -->
    </div><!-- end container -->
</section>
<!--======================================
        END HERO AREA
======================================-->
<!-- ================================
         START FUNFACT AREA
================================= -->
<section class="funfact-area funfact-section-area mb-5">
    <div class="container">
        <div class="counter-box bg-white shadow-md rounded-rounded px-4">
            <div class="row">
                <div class="col responsive-column-half border-right border-right-gray">
                    <div class="media media-card text-center px-0 py-4 shadow-none rounded-0 bg-transparent counter-item mb-0">
                        <div class="media-body">
                            <h5 class="fw-semi-bold pb-2">5+ Million</h5>
                            <p class="lh-20">Visitors on our network</p>
                        </div>
                    </div>
                </div><!-- end col -->
                <div class="col responsive-column-half border-right border-right-gray">
                    <div class="media media-card text-center px-0 py-4 shadow-none rounded-0 bg-transparent counter-item mb-0">
                        <div class="media-body">
                            <h5 class="fw-semi-bold pb-2">2+ Million</h5>
                            <p class="lh-20">Career events attended</p>
                        </div>
                    </div>
                </div><!-- end col -->
                <div class="col responsive-column-half border-right border-right-gray">
                    <div class="media media-card text-center px-0 py-4 shadow-none rounded-0 bg-transparent counter-item mb-0">
                        <div class="media-body">
                            <h5 class="fw-semi-bold pb-2">1+ Million</h5>
                            <p class="lh-20">Connections created</p>
                        </div>
                    </div>
                </div><!-- end col -->
                <div class="col responsive-column-half border-right border-right-gray">
                    <div class="media media-card text-center px-0 py-4 shadow-none rounded-0 bg-transparent counter-item mb-0">
                        <div class="media-body">
                            <h5 class="fw-semi-bold pb-2">10,000+</h5>
                            <p class="lh-20">Recruiters on platform</p>
                        </div>
                    </div>
                </div><!-- end col -->
                <div class="col responsive-column-half">
                    <div class="media media-card text-center px-0 py-4 shadow-none rounded-0 bg-transparent counter-item mb-0">
                        <div class="media-body">
                            <h5 class="fw-semi-bold pb-2">150+</h5>
                            <p class="lh-20">Communities served</p>
                        </div>
                    </div>
                </div><!-- end col -->
            </div><!-- end row -->
        </div><!-- end counter-box -->
    </div><!-- end container -->
</section>
<!-- ================================
         END FUNFACT AREA
================================= -->
<?php } ?>
        
<header class="bg-white shadow-sm sticky-top" style="top: 0; padding: 10px 0;">
	<div class="container">

		<!-- Row 1: tabs + search + CTA -->
		<div class="jobs-header-row">
			<ul class="nav nav-tabs flex-nowrap text-nowrap jobs-tab-row" id="myTab" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" onclick="get_job_type()" aria-selected="true">Jobs</a>
				</li>
				<?php if(taoh_user_is_logged_in()) { ?>
				<li class="nav-item">
					<a class="nav-link" id="home-tab2" data-toggle="tab" href="#home2" role="tab" aria-controls="home2" onclick="get_job_type('applied')" aria-selected="false">Applied</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="home-tab3" data-toggle="tab" href="#home3" role="tab" aria-controls="home3" onclick="get_job_type('saved')" aria-selected="false">Saved</a>
				</li>
				<?php
				}
					$page_sel = "jobs";
					include_once('job_tabs.php');
				?>
				<?php if(taoh_user_is_logged_in()) { ?>
					<li class="nav-item">
						<a class="nav-link" href="<?php echo TAOH_SITE_URL_ROOT.'/jobs/dash'; ?>">My Jobs</a>
					</li>
				<?php } else { ?>
					<li class="nav-item">
						<a class="btn theme-btn login-button py-1" aria-pressed="true" data-toggle="modal" data-target="#config-modal">
						<i class="la la-sign-in mr-1"></i> Login / Signup</a>
					</li>
				<?php }  ?>
			</ul>

			<form class="jobs-header-search" onsubmit="searchFilter();return false">
				<input type="search" id="jobsQuickSearch" placeholder="Search jobs..." class="jobs-header-search-input"
					onfocus="document.getElementById('jobsSearchPanel').classList.add('open')"
					oninput="document.getElementById('query').value=this.value">
				<button type="submit" class="jobs-header-search-btn"><i class="la la-search"></i></button>
			</form>

			<?php if(taoh_user_is_logged_in()) { ?>
				<a class="jobs-post-cta" href="<?php echo TAOH_SITE_URL_ROOT.'/jobs/post'; ?>"><i class="la la-plus" style="font-size:12px"></i> Post a Job</a>
			<?php } ?>
		</div>

		<!-- Row 2: expandable filter panel -->
		<div class="jobs-search-panel" id="jobsSearchPanel">
			<?php include('search.php'); ?>
		</div>

	</div>
</header>

<section>
    <div class="container sticky-container">
        <div class="tab-content" id="myTabContent">
        	<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
        		<div class="" >
					<div class="row justify-content-center">
						<div  class="loaderArea"  id='listloaderArea'></div>
					</div>
					<div class="row d-flex justify-content-between" > 
						<div class="col-lg-3 mb-3 result_div">
							<div id="joblistArea"></div>
							<div id="pagination"></div>
						</div>
						<div class="col-lg-6 mb-3 result_div">
							<div class="detail_tab sticky-detail" style="z-index: 0;"></div>
						</div>
						<div class="row col-lg-9 no_result_div mt-5">
							<div class="no_result">
								<img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/no_jobs_found.png'; ?>" alt="No Results Illustration">
								<div class="noresult_html"></div>
								<a href="<?php echo TAOH_SITE_URL_ROOT.'/jobs'; ?>" class="btn theme-btn">BROWSE OUR JOB BOARD</a>
							</div>
						</div>
						<div class="col-lg-3 mb-3">
							<?php if (function_exists('taoh_get_recent_jobs')) { taoh_get_recent_jobs('new');  } ?>
							<?php if(TAOH_ENABLE_JUSASK) { ?>
								<div class="light-dark-card right-side-block">
									<?php if (function_exists('taoh_jusask_widget')) { taoh_jusask_widget('new');  } ?>
								</div>
							<?php } ?>
							<?php if(TAOH_LEARNING_WIDGET_ENABLE) { ?>
								
								<?php if (function_exists('taoh_readables_widget')) { 
									?>
									<div class="right-side-block mob-hide">									
									<?php
									taoh_readables_widget('new');  
									?>
									</div>
									<?php
									} 
									?>
								
							<?php } ?>
						</div>
            		</div>
        		</div>
      		</div>  
		</div>
    </div>
</section>
<div id="myModal" class="modal fade apply_modal" role="dialog">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header">
				<div class="d-flex justify-content-between align-items-center py-0" style="height: 90px; width: 100%;">
					<div>
						 <a href="<?php echo ($taoh_home_url ?? '') . "/../"; ?>" class="logo">
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
													if(isset($taoh_user_vars->profile_complete) 
													&& $taoh_user_vars->profile_complete == 0 && isset($taoh_user_vars->fname) && $taoh_user_vars->fname == TAOH_SITE_NAME_SLUG)
													echo field_fname();
													else
													echo field_fname($taoh_user_vars->fname); ?>
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
											<p class="full-text mb-3" style="font-size: 18px; line-height: 21px; font-weight: 400; max-height: 600px; overflow-y: auto;">
											</p>
											<a href="#" class="text-uppercase toggle-link" style="cursor: pointer; color: #1C52A8; font-weight: 500; font-size: 18px;">Show More</a>
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
<div class="modal" id="exampleModal1" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content light-dark-card">
      <div class="modal-header light-dark-card">
        <h5 class="modal-title">Share</h5>
      </div>
      <div class="modal-body">
	  	<section class="mb-3 mt-3" id="share_icon">
			
		</section>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- <span class="fs-12 mb-3" style="float: right;">${taohFullyearConvert(timetotimestamp(v.created))}</span> for days show  -->
<?php require_once('jobs_common_js.php'); ?>
<script>

	let isLoggedIn = "<?php echo taoh_user_is_logged_in(); ?>";
	let loaderArea = $('#listloaderArea');
	let detailloaderArea = $('#detailloaderArea');
  	let searchQuery = $('#searchQuery');
	let joblistArea = $('#joblistArea');
	let locationSelectInput = $('#locationSelect');
	let geohashInput = $('#geohash');
  	let currentMod = '<?php echo ($app_data?->slug ?? ''); ?>';
	let geohash = "";
	let search = "";
  	let locationClear = $('#locationClear');
  	let searchClear = $('#searchClear');
	let postDate = $('#postdate').val();
	let from_date = $('#from_date').val();
	let to_date = $('#to_date').val();
	let activeListloaderArea = $('#activeListloaderArea'); 
	let listUpdatedAt = 0;
	let activeChatList = $('#activeChatList');
	let jobCount = $('#jobCount');
	let totalItems = 0; //this will be rewriiten on response of jobs on line 363
	let itemsPerPage = 10;
	let currentPage = 1;
	let like_min = '<?php echo TAOH_SOCIAL_LIKES_THRESHOLD; ?>';
	let comment_min = '<?php echo TAOH_SOCIAL_COMMENTS_THRESHOLD; ?>';
	let share_min = '<?php echo TAOH_SOCIAL_SHARES_THRESHOLD; ?>';
	//let app_slug = 'jobs';
	let arr_cont = [];
	let liked_arr = '<?php echo $liked_arr; ?>';
	//var job_list_name = "jobs_list";
	var already_rendered = false;
	var get_slug = false;
	var job_list_name = "";
	var store_name = JOBStore;
	
	var det_slot = $('.detail_tab');
	var job_type = '';
	var applied_jobs = '<?php echo json_encode($_SESSION[TAOH_ROOT_PATH_HASH.'_applied_jobs']); ?>';
	var scouted_jobs = '<?php echo json_encode($_SESSION[TAOH_ROOT_PATH_HASH.'_scouted_jobs']); ?>';
	var eligible_scouted_jobs = '<?php echo json_encode($_SESSION[TAOH_ROOT_PATH_HASH.'_eligible_scouted_jobs']); ?>';
	var profile_complete = '<?php echo $profile_complete; ?>';
	applied_jobs = JSON.parse(applied_jobs);
	scouted_jobs = JSON.parse(scouted_jobs);
	eligible_scouted_jobs = JSON.parse(eligible_scouted_jobs);
	
	const currencies = <?php echo $currencies; ?>;

	loader(true, loaderArea);
	$('#dateRangeInputs').hide();
	$('.no_result_div').hide();
	//Initial run
	$(document).ready(function(){

		$('[data-toggle="tooltip"]').tooltip();


    	$('.ts-control').css('height', '37px');
		//taoh_scout_list();
		<?php if(TAOH_INTAODB_ENABLE) { ?>
			console.log('list ajax start time:', new Date().getTime());
			getjoblistdata();
		<?php }else{ ?>
			taoh_jobs_init();
		<?php } ?>
	})

	$('body').tooltip({
			selector: '[data-toggle="tooltip"]'
		});

	/* function delete_jobs_into(){
		getIntaoDb(dbName).then((db) => {
			let dataStoreName = JOBStore;
			const transaction = db.transaction(dataStoreName, 'readwrite');
			const objectStore = transaction.objectStore(dataStoreName);
			const request = objectStore.openCursor();
			request.onsuccess = (event) => {
			const cursor = event.target.result;
			if (cursor) {
				const index_key = cursor.primaryKey;
				if(index_key.includes('job')) 
				{
				objectStore.delete(index_key);
				}
				cursor.continue();
			}
			};
		}).catch((err) => {
			console.log('Error in deleting data store');
		});
	} */

	function get_job_type(job_type_get = ''){

		if(job_type_get == 'applied'){
			job_type = job_type_get;
		}else if(job_type_get == 'saved'){
			job_type = job_type_get;
		}else{
			job_type = '';
		}
		/*for clearing search and paging data */ 
		currentPage = 1;
		$('#postdate').val('');
		$('#from_date').val('');
		$('#to_date').val('');
		$('#query').val('');
		
		$('#locationClear').hide();
        $('#coordinateLocation').val("");
        $('#geohash').val("");
        geohash = "";
        $('.ts-control div.item').html('');
		$('.ts-wrapper').removeClass('full has-items input-hidden');
		/*for clearing search and paging data */ 
		
		<?php if(TAOH_INTAODB_ENABLE) { ?>
			getjoblistdata('', job_type);
		<?php }else{ ?>
			taoh_jobs_init('',job_type);
		<?php } ?>
	}

	document.addEventListener("DOMContentLoaded", function() {
		const fromDateInput = document.getElementById('from_date');
		const toDateInput = document.getElementById('to_date');
		if (fromDateInput && toDateInput) {

			fromDateInput.addEventListener('change', function() {
				// Get the selected from date
				const fromDate = new Date(fromDateInput.value);
				// Set the minimum selectable date for the 'To' date
				toDateInput.min = fromDate.toISOString().split("T")[0];
			});

			toDateInput.addEventListener('change', function() {
				// Get the selected to date
				const toDate = new Date(toDateInput.value);
				// Set the maximum selectable date for the 'From' date
				fromDateInput.max = toDate.toISOString().split("T")[0];
			});
		}
	});


    const postdateSelect = document.getElementById('postdate');
	if(postdateSelect){
		postdateSelect.addEventListener('change', function () {
			if (this.value === 'date_range') {
				$('#dateRangeInputs').css('display','flex');
			} else {
				$('#dateRangeInputs').hide();
				$('#from_date').val('');
				$('#to_date').val('');
			}
		});
	}
    

	function getCurrencySymbol(index) {
        if (index >= 0 && index < currencies.length) {
            return currencies[index].symbol;
        } else {
            return '';
        }
    }


	// Close search panel when clicking outside
	document.addEventListener('click', function(e) {
		var panel = document.getElementById('jobsSearchPanel');
		var searchInput = document.getElementById('jobsQuickSearch');
		if (panel && panel.classList.contains('open')) {
			if (!panel.contains(e.target) && e.target !== searchInput && !searchInput.contains(e.target)) {
				panel.classList.remove('open');
			}
		}
	});

	function searchFilter() {
		currentPage = 1;
		// Sync quick search - hidden query field
		var qs = document.getElementById('jobsQuickSearch');
		if (qs && qs.value) {
			document.getElementById('query').value = qs.value;
		}
		var queryString = $('#searchFilter').serialize();
		console.log(queryString);
		geohash = geohashInput.val();
        search = $('#query').val();
        console.log('----search------',search);
        if(search) {
            searchClear.show();
        }
        if(geohash) {
            locationClear.show();
        }
		already_rendered = false;
		job_type = job_type;
		<?php if(TAOH_INTAODB_ENABLE) { ?>
			getjoblistdata(queryString, job_type);
		<?php }else{ ?>
			taoh_jobs_init('',job_type);
		<?php } ?>
	}

	function getjoblistdata(queryString, job_type_get = '') {
		// Open or create a database
		getIntaoDb(dbName).then((db) => {
			var currpage = currentPage-1;
			job_type = job_type_get;

			var job_list_hash = search+geohash+queryString+currpage+itemsPerPage+postDate+from_date+to_date+job_type;
			job_list_name = 'jobs_'+crc32(job_list_hash);
			console.log(job_list_name);
			const datarjobequest = db.transaction(store_name).objectStore(store_name).get(job_list_name); // get main data
			datarjobequest.onsuccess = ()=> {
				if (datarjobequest.result?.values?.output?.list) {
					const jobs = datarjobequest.result.values.output.list;
					const jsonLd = {
						"@context": "https://schema.org",
						"@graph": jobs.map(jobs => ({
							"@type": "JobPosting",
							"title": jobs.title,
							"payinfo": jobs.payinfo,
							"paymentTerm": jobs.paymentTerm,
							"expiry": jobs.expiry,
							"created": jobs.created,
							"full_location": jobs.full_location,
						}))
					};
					const script = document.createElement('script');
					script.type = "application/ld+json";
					script.text = JSON.stringify(jsonLd, null, 2);
					document.head.appendChild(script);
				}

				const jobstoredatares = datarjobequest.result;
				if(jobstoredatares !== undefined && jobstoredatares !== null && jobstoredatares !== "" && jobstoredatares !== "undefined" && jobstoredatares !== "null"){
					console.log('list ajax intaodb call start time:', new Date().getTime());
					const jobstoredata = datarjobequest.result.values;
					get_slug = true;
					already_rendered = true;
					loader(false, loaderArea);
					render_jobs_template(jobstoredata, joblistArea, job_type);
					//taoh_jobs_init(queryString);
					console.log('ifff');
				}else{
					get_slug = false;
					already_rendered = false;
					loader(true, loaderArea);
					job_type = job_type;
					taoh_jobs_init(queryString, job_type);
					console.log('else');
				}				
			}
			
		}).catch((error) => {
           console.log('Getjoblistdata Error:', error);
       	});
	}

	function show_pagination(holder) {
		return $(holder).pagination({
				items: totalItems,
				itemsOnPage: itemsPerPage,
				currentPage: currentPage,
				displayedPages: 3,
				onInit: function() {
					$("#pagination ul").addClass('pagination');
					$("#pagination ul li.disabled").addClass('page-link text-gray');
					$("#pagination ul li.active").addClass('page-link bg-primary text-white');
				},
				onPageClick: function(pageNumber, event) {
					$("#pagination ul").addClass('pagination');
					$("#pagination ul li.disabled").addClass('page-link text-gray');
					$("#pagination ul li.active").addClass('page-link bg-primary text-white');
					currentPage = pageNumber;
					console.log('--show_pagination----------');
					//taoh_jobs_init();
					already_rendered = false;
					console.log(already_rendered);
					<?php if(TAOH_INTAODB_ENABLE) { ?>
						getjoblistdata('',job_type);
						<?php }else{ ?>
							taoh_jobs_init('',job_type);
						<?php } ?>
				}
		});
	}

  function clearBtn(type) {
    loader(true, loaderArea);
    if(type == "search") {
        $('#searchClear').hide();
        search = "";
        $('#query').val("");
    }
    if(type == "geohash") {
        $('#locationClear').hide();
        $('#coordinateLocation').val("");
        $('#geohash').val("");
        geohash = "";
        $('.ts-control div.item').html('');
		$('.ts-wrapper').removeClass('full has-items input-hidden');
    }
	already_rendered = false;
	job_type = job_type;
	<?php if(TAOH_INTAODB_ENABLE) { ?>
		getjoblistdata('',job_type);
	<?php }else{ ?>
		taoh_jobs_init('',job_type);
	<?php } ?>
}

function taoh_jobs_init (queryString="", job_type_get="") {
	search = $('#query').val();
	geohash = geohashInput.val();
    if(search) {
      searchClear.show();
    } else {
      searchClear.hide();
    }
    if(geohash) {
      locationClear.show();
    } else {
      locationClear.hide();
    }
	geohash = geohash
	/* if($('#locationSelect-ts-control').val() == ''){
		$('#coordinateLocation').val('');
		geohashInput.val('');
	} */
	postDate = $('#postdate').val();
	from_date = $('#from_date').val();
	to_date = $('#to_date').val();
	job_type = job_type_get;

    var data = {
       'taoh_action': 'jobs_get',
       'ops': 'list',
       'search': search,
       'geohash': geohash,
       'offset': currentPage - 1,
       'limit': itemsPerPage,
	   'postDate': postDate,
	   'from_date': from_date,
	   'to_date': to_date,
	   'filter_type': job_type,
	   'ptoken': '<?php echo $ptoken; ?>',
	   'filters': queryString,
     };
    jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(listresponse) {
	  	<?php if(TAOH_INTAODB_ENABLE) { ?>
			if(!get_slug){
				indx_jobs_list(listresponse);
			}
			if(!already_rendered){
				render_jobs_template(listresponse, joblistArea, job_type);
			}
		<?php }else{ ?>
			render_jobs_template(listresponse, joblistArea, job_type);
		<?php } ?>
	  loader(false, loaderArea);
    }).fail(function() {
        loader(false, loaderArea);
        console.log( "Network issue!" );
    })
  }

  function render_jobs_template(data, slot, job_type_get) {
	slot.empty();
	if(data.output === false || data.success  === false) {
		slot.append("");
		$('#pagination').hide();
		job_detail_execute('0');
		if(job_type_get == 'applied'){
			var noresult = `<h1>Your job search is just getting started</h1>
							<p>It looks like you haven't applied for any jobs yet. That's okay! We're here to help<br> you get started. Find a job that suits you from our job board.</p>`;
		}else if(job_type_get == 'saved'){
			var noresult = `<h1>Your job search is off to a great start</h1>
							<p>It looks like you haven't saved any jobs yet, but that's no problem.<br>Saving jobs can help you keep track of opportunities you're interested in.</p>
							<ul class="options">
								<li>1. Find a Job You Like</li>
								<li>2. Click the Bookmark Icon to save it</li>
							</ul>
							<p>By saving jobs, you can easily revisit them later and apply at your convenience.</p>`;
		}else{
			var noresult = `<h1>We couldn't find exactly what you were looking for</h1>
							<p>It seems your search didn't yield any results.<br> Don't worry, we can help you find what you need.</p>
							<ul class="options">
								<li>1. Adjust your Search Terms</li>
								<li>2. Explore Other Jobs that might suit you</li>
								<li>3. Refine Your Search Criteria</li>
							</ul>`;
		}
		$('.noresult_html').html(noresult);
		$('.no_result_div').show();
		return false;
	}
	$('.no_result_div').hide();

	totalItems = data.output.total
	if(!get_slug){	
    	var result = format_object(data);
	}else{
		var result = data;
	}
	console.log('format', result);
		$.each(result.output.list, function(i, v){

			console.log('-----------',v.canonical_url);
			var additive = '';
			/* if(v.canonical_url && v.canonical_url !='' && v.canonical_url != undefined){
				additive = v.canonical_url;
			}
			else{
				additive = v.source;
			} */
			var job_url = convertToSlug(taoh_title_desc_decode(v.title))+'-'+v.conttoken;

			if(i == 0){
			
				job_detail_execute(v.conttoken);
				updateCanonical(additive,job_url);
			}
			arr_cont.push(v.conttoken.toString());

			v.title = ucfirst(v.title);

			var company_name_get = v.company.length ? v.company[0].name : '';

			var liked_check = get_liked_check(v.conttoken);

			var apply_email_link = '';
			var show_scout_logo = '';
			//console.log('----kalpana--------',eligible_scouted_jobs);
			//console.log('---kalpana---------',v.conttoken);
			if(isLoggedIn){
				if(profile_complete == 0){
					apply_email_link = `<a class="btn theme-btn w-50 mb-3 profile_incomplete">Apply Now </a>`;
				}else{
					if(v.ptoken != '<?php echo $ptoken; ?>'){
						if(v.enable_scout_job == 'on'){
							if(Object.keys(scouted_jobs).includes(v.conttoken)){
								apply_email_link = `<a data-action="view_application" apply-id="${scouted_jobs[v.conttoken]}"
								job-url="${job_url}" 
								data-conttoken="${v.conttoken}" class="btn theme-btn mb-3  success">Applied! View Application Status </a>`;
							}
							else{
								if(eligible_scouted_jobs.includes(v.conttoken)){
									apply_email_link = `<a 
									job-url="${job_url}" data-conttoken="${v.conttoken}" 
									data-action="apply_through_scout_link" class="btn theme-btn mb-3  click_action" style="background-color:#FF7311">Apply through Scout (referred)</a>`;
								
								}else{
									apply_email_link = `<a job-url="${job_url}" data-conttoken="${v.conttoken}" 
									data-action="request_through_scout_link" class="btn theme-btn mb-3 click_action " style="background-color:#FF7311">Apply through Scout</a>`;
								}
							}
						}else{
							if(Object.keys(applied_jobs).includes(v.conttoken)){
								apply_email_link = `<a onclick="event.stopPropagation();" class="btn theme-btn mb-3 success">Applied! </a>`;
							}else {
								if(v.apply_link){
									apply_email_link = `<a onclick="event.stopPropagation();" href="${v.apply_link}" target="_blank"
									data-action="apply"
									class="btn theme-btn w-50 mb-3 click_action jobs-external-link">Apply Now </a><span class="jobs-external-badge">↗ External</span>`;
								}else if((v.email) && (v.enable_apply)){
									apply_email_link = `
									<a data-position="${(v.title)}" data-company="${(company_name_get)}" data-fname="${(v.fname)}" 
									data-toemail="${(v.email)}" data-conttoken="${(v.conttoken)}"  data-action="apply" 
									data-placeType="${renderJobType(v.placeType)}" data-description="${(v.description)}" 
									class="btn theme-btn mb-3 open_modal click_action">Apply Now</a>`;
								}else{
									apply_email_link = `<a onclick="event.stopPropagation();" href="${'mailto:'+v.email}" data-action="apply" 
									 class="btn theme-btn w-50 mb-3 click_action">Apply Now </a>`;
								}
							}
						}
					}
				}
				
				if(v.enable_scout_job == 'on'){
					show_scout_logo = `<a style="margin-left: 5px">
					<img 
					data-toggle="tooltip" data-placement="top"
                 title="Please note: Scout is a specialized program that gets 6x faster result, where industry leading peers help find the best peer talent for the jobs. " 
                
					src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/scout_icon.png'; ?>" width="28" height="28" alt="Scout Icon"></a>`;
				}
			}else{
				apply_email_link = `<a class="btn theme-btn-outline w-50 mb-3 create_referral" data-title="${taoh_title_desc_decode(v.title)}" data-sharelink="<?php echo $share_link; ?>">Apply </a>`;
			}

			var payinfo = '';
			if(v.payinfo && v.country_code && v.payinfo !='' && v.country_code!= ''){
				var country_code = v.country_code - 1;
				payinfo = getCurrencySymbol(country_code) + ' ' + v.payinfo;
				if(v.paymentTerm == 'hourly'){
					payinfo = payinfo + ' per hour';
				}else if(v.paymentTerm == 'monthly'){
					payinfo = payinfo + ' per month';
				}else if(v.paymentTerm == 'annualy'){
					payinfo = payinfo + ' per year';
				}else if(v.paymentTerm == 'project'){
					payinfo = payinfo + ' per project';
				}else if(v.paymentTerm == 'daily'){
					payinfo = payinfo + ' per Daily';
				}else if(v.paymentTerm == 'weekly'){
					payinfo = payinfo + ' per week';
				}	
			}
			slot.append(
			`<div class="light-dark-card dash_metrics job-listing-block-row ${i == 0 ? 'active':''}" style="cursor: pointer;"
			 data-conttoken="${v.conttoken}"  
			 data-canonical = "${additive}"
			 job-url="${job_url}"
			 dash_metrics data-metrics="view" conttoken="${v.conttoken}" data-type="jobs" 
			  >
				<div class="">
					<div><b class="jobing-company-name stop_propagation">${(v.company && v.company.length)? newgenerateCompanyHTML(v.company): ''}</b> <span class="bookmark-icon-right">${liked_check}</span></div>
						<a href="${_taoh_site_url_root}/jobs/d/${job_url}" class="mobile-job-list">
							<h3 class="fs-17 mt-2 b-2" style="font-weight: 500;">${taoh_title_desc_decode(v.title)}</h3>
						</a>
						<h3 class="fs-17 mt-2 b-2 desktop-job-list" style="font-weight: 500;">${taoh_title_desc_decode(v.title)}   </h3>
						<p>${v.full_location ? newgenerateLocationHTML(v.full_location): ''}</p>
						<p>${payinfo}</p>
						
						<div class="col-12 row px-0 mx-auto d-flex flex-wrap">
							<div class="mt-3 col-10 px-0">
								${apply_email_link} 
							</div>
							<div class="col d-flex justify-content-end align-items-center px-0">
							${show_scout_logo}
							</div>
						</div>
					</div>
				</div>
			</div>
			`);
		});
		if(data.output.total >= 11) {
			$('#pagination').show();
			show_pagination('#pagination');
		}else{
			$('#pagination').hide();
		}
				
		if(search){
			taoh_metrix_ajax('jobs',arr_cont);					
		}
		
	}
	function updateCanonical(newCanonicalUrl,job_url){
		console.log('-----------'+newCanonicalUrl)
		//<meta name="original-source" content="'.$response['meta']['canonical_url'].'"/>';
		if(newCanonicalUrl !=''){
			// Update or add the canonical link
			var $canonicalLink = $('link[rel="canonical"]');					
			if ($canonicalLink.length) {
				// If the canonical link already exists, update it
				$canonicalLink.attr('href', newCanonicalUrl);
			} else {
				// If no canonical link exists, create and append one
				$('<link>')
					.attr({
						rel: 'canonical',
						href: newCanonicalUrl
					})
					.appendTo('head');
			}

			var $sourceLink = $('meta[name="original-source"]');					
			if ($sourceLink.length) {
				// If the canonical link already exists, update it
				$sourceLink.attr('content', newCanonicalUrl);
			} else {
				// If no canonical link exists, create and append one
				$('<meta>')
					.attr({
						name: 'original-source',
						content: newCanonicalUrl
					})
					.appendTo('head');
			}

		}

		let homeurl = "<?php echo TAOH_SITE_URL_ROOT.'/'.$app_data->slug.'/'; ?>";
		let url = homeurl+'d/'+job_url+'/?q=main';
		window.history.pushState("","", url);
	}

	$(document).on("click", ".job-listing-block-row", function() {
        var conttoken = $(this).attr("data-conttoken");
		var job_url = $(this).attr("job-url");
		var newCanonicalUrl = $(this).attr("data-canonical");

  		$(this).addClass('active').siblings().removeClass('active');
        job_detail_execute(conttoken);
		save_metrics('jobs','click',conttoken);
		updateCanonical(newCanonicalUrl,job_url);
    });


	function job_detail_execute(conttoken){
		det_slot.empty();
		if(conttoken == '0'){
			det_slot.append("");
			return false;
		}
		loader(true, loaderArea);
		var data = {
            'taoh_action': 'jobs_get_detail',
            'ops': 'detail',
            'conttoken': conttoken,
            'ptoken': '<?php echo $ptoken; ?>',
        };
        jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
			console.log(response)
			loader(false, loaderArea);
			det_slot.html(response);
			var detail_like = get_liked_check(conttoken);
			$('.like_render').html(detail_like);

        }).fail(function() {
            console.log( "Network issue on response!" );
        })
	}

function indx_jobs_list(joblistdata){
    var job_taoh_data = { taoh_data:job_list_name,values : joblistdata };
	let job_setting_time = new Date();
    job_setting_time = job_setting_time.setMinutes(job_setting_time.getMinutes() + 30);
    var job_setting_timedata = { taoh_ttl: job_list_name,time:job_setting_time };
	obj_data = { [store_name]:job_taoh_data,[TTLStore] : job_setting_timedata };
    Object.keys(obj_data).forEach(key => {
    // console.log(key, obj_data[key]);
        IntaoDB.setItem(key,obj_data[key]).catch((err) => console.log('Storage failed', err));
    });
    return false;
} // indexed db form submit

setInterval(function(){
	<?php if(TAOH_INTAODB_ENABLE) { ?>
		checkTTL(job_list_name,store_name);
	<?php } ?>
},30000);
</script>
<?php
taoh_get_footer();
?>