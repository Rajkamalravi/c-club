<?php
taoh_get_header();

if ( ! defined ( 'TAO_PAGE_TITLE' ) ) { define ( 'TAO_PAGE_TITLE', "Comprehensive Open Jobs List at ".TAOH_SITE_NAME_SLUG.": Explore and Apply to a Wide Range of Job Opportunities" ); }
if ( ! defined ( 'TAO_PAGE_DESCRIPTION' ) ) { define ( 'TAO_PAGE_DESCRIPTION', "Browse our comprehensive jobs list featuring a diverse range of job opportunities across industries. Find the perfect job that matches your skills and interests, chat with recruiters and easily apply through our user-friendly platform at ".TAOH_SITE_NAME_SLUG.". Start your job search today and take the next step in your career." ); }
if ( ! defined ( 'TAO_PAGE_KEYWORDS' ) ) { define ( 'TAO_PAGE_KEYWORDS', "Job openings at ".TAOH_SITE_NAME_SLUG.", Employment opportunities at ".TAOH_SITE_NAME_SLUG.", Job listings at ".TAOH_SITE_NAME_SLUG.", Job board at ".TAOH_SITE_NAME_SLUG.", Job search platform at ".TAOH_SITE_NAME_SLUG.", Job finder at ".TAOH_SITE_NAME_SLUG.", Job database at ".TAOH_SITE_NAME_SLUG.", Job search engine at ".TAOH_SITE_NAME_SLUG.", Job match at ".TAOH_SITE_NAME_SLUG.", Job applications at ".TAOH_SITE_NAME_SLUG.", Apply for jobs at ".TAOH_SITE_NAME_SLUG.", Job search website at ".TAOH_SITE_NAME_SLUG.", Find a job at ".TAOH_SITE_NAME_SLUG.", Job seekers at ".TAOH_SITE_NAME_SLUG.", Job alerts at ".TAOH_SITE_NAME_SLUG.", Explore job opportunities at ".TAOH_SITE_NAME_SLUG ); }

$current_app = taoh_parse_url(0);
$app_data = taoh_app_info($current_app);
$taoh_user_vars = $data = taoh_user_all_info();
$empty = 0;
//echo taoh_parse_url(0);taoh_exit();
//$data = taoh_user_all_info();
$ptoken = $user_ptoken = $data->ptoken;
$share_link = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
define( 'TAO_PAGE_TYPE', ($app_data?->slug ?? '') );
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
$_SESSION[TAOH_ROOT_PATH_HASH.'_applied_jobs'] = array();

if($data_res['success']){
	$_SESSION[TAOH_ROOT_PATH_HASH.'_eligible_scouted_jobs'] = $data_res['output'];

}

//list of applied job contoken
$taoh_call = "jobs.appliedjob.list";
$taoh_vals = array(
    'mod' => 'jobs',
    'ptoken' => $ptoken,
    'token' => taoh_get_dummy_token(),
    'secret' => TAOH_API_SECRET,
    'cache_required' => 0,
    //'debug'=> 1,
    //'cache' => array ( "name" => taoh_p2us($taoh_call).'_'.$conttoken.'_apply_status', "ttl" => 3600),
);

//echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );die;
$applied = taoh_apicall_get($taoh_call, $taoh_vals);
$applied_res = json_decode($applied,1);
$apply_result = array();
if($applied_res['success']){

  foreach($applied_res['output'] as $key=>$val){
	$apply_result[$val['conttoken']] = $val['apply_id'];

  }
  $_SESSION[TAOH_ROOT_PATH_HASH.'_applied_jobs'] = $apply_result;
}

//print_r($_SESSION);die();

?>

<style>

span.h5 {
  font-size: 13px !important;
}
.search-form {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.search-btn-box {
    margin-top: 15px;
    text-align: right;
}
#dateRangeInputs {
    display: none;
}
.error{
	color: red;
}

</style>
<header class="sticky-top bg-white border-bottom border-bottom-gray">
<section class="hero-area pt-20px pb-20px bg-white shadow-sm overflow-hidden">
    <span class="stroke-shape stroke-shape-1"></span>
    <span class="stroke-shape stroke-shape-2"></span>
    <span class="stroke-shape stroke-shape-3"></span>
    <span class="stroke-shape stroke-shape-4"></span>
    <span class="stroke-shape stroke-shape-5"></span>
    <span class="stroke-shape stroke-shape-6"></span>
		<div class="container">
        <div class="hero-content d-flex flex-wrap align-items-center justify-content-between jobs-mobile-header">
            <div class="col-lg-7">
				<h2 class="section-title fs-24 mb-1"><?php echo $app_data->name_slug; ?></h2>
				<p class="section-desc"><?php echo $app_data->short; ?></p>
            </div>

			<div class="hero-btn-box col-lg-3" >
				<?php
				if(taoh_user_is_logged_in()) {
                	if( $taoh_user_vars->type == 'employer') {
						//echo "<a href=\"".TAOH_DASH_URL."?app=".$app_data->slug."&from=dash&to=".$app_data->slug."\" class=\"btn theme-btn w-100 mb-3\" style=\"background-color: #38B653;\"><i class=\"icon-line-awesome-wechat\"></i>My ".$app_data->slug." <i class=\"icon-material-outline-arrow-right-alt\"></i></a><a href=\"".TAOH_DASH_URL."?app=".$app_data->slug."&from=dash&to=".$app_data->slug."/post\" data-metrics=\"post\" class=\"btn theme-btn w-100 mb-3\"><i class=\"icon-line-awesome-wrench\"></i> Post ".$app_data->name_slug." <i class=\"icon-material-outline-arrow-right-alt\"></i></a>"; http://localhost/wpl/hires-i/jobs/dash
						echo "<a href=\"".TAOH_SITE_URL_ROOT."/".$app_data->slug."/dash\" class=\"btn theme-btn w-100 mb-3\" style=\"background-color: #38B653;\"><i class=\"icon-line-awesome-wechat\"></i>My ".$app_data->slug." <i class=\"icon-material-outline-arrow-right-alt\"></i></a>
						<a href=\"".TAOH_SITE_URL_ROOT."/".$app_data->slug."/post\" data-metrics=\"post\" class=\"btn theme-btn w-100 mb-3\"><i class=\"icon-line-awesome-wrench\"></i> Post ".$app_data->name_slug." <i class=\"icon-material-outline-arrow-right-alt\"></i></a>
						";
                	}
				} else {
					echo "
					<a href=\"".TAOH_LOGIN_URL."/".$app_data->slug."\" class=\"btn theme-btn mb-3\"><i class=\"la la-sign-in mr-1\"></i> Login / Signup</a>
					";
				}
				?>
			</div>
        </div><!-- end hero-content -->
    </div><!-- end container -->
</section>
			</header>
<section class="question-area pt-40px pb-40px">
    <div class="container">

				<!-- <div class="filters">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="d-flex flex-wrap align-items-center flex-grow-1">
                            <div class="form-group mr-3 flex-grow-1">
							<label for="">Search Keyword <span style="display:none" id="searchClear" onclick="clearBtn('search')" class="badge badge-danger"><i class="la la-close"></i> Clear</span></label>

							<?php //echo field_search('do_search'); ?>

                            </div>
								<div class="form-group mr-3 flex-grow-1">
                                <label for="">Search Location <span style="display:none" id="locationClear" onclick="clearBtn('geohash')" class="badge badge-danger"><i class="la la-close"></i> Clear</span></label>

                            </div>
                        </div> end d-flex -->
                        <!-- <div class="search-btn-box mb-3">
                            <button onclick='search()' class="btn theme-btn">Search </button>
                        </div> -->

						<!-- end search-btn-box -->
                    <!-- </div> -->


                    <!-- <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <p id='jobCount' class="fs-14 fw-medium"></p> -->
                        <!-- <div class="d-flex align-items-center lh-1">
                            <label for="sort" class="mb-0 mr-2 fs-13">Sort by:</label>
                            <select id="sort" class="custom-select w-100px">
                                <option value="i" selected="">Matches</option>
                                <option value="p">Newest</option>
                                <option value="y">Salary</option>
                            </select>
                        </div> -->
                    <!-- </div> -->

				<!-- </div> -->
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="jobs" role="tabpanel" aria-labelledby="jobs-tab">

                <div class="row mt-4">
					<div class="col-lg-2">
					<?php taoh_leftmenu_widget(); ?>
					</div><!-- end col-lg-2 -->
                    <div class="col-lg-6">
                      <!-- <div class="form-group row">
                        <div class="col-6">
                          <label for="">Search Keyword <span style="display:none" id="searchClear" onclick="clearBtn('search')" class="badge badge-danger"><i class="la la-close"></i> Clear</span></label>
                          <?php //echo field_search('do_search'); ?>
                        </div>
                         <div class="col-6">
                            <label for="">Search Location <span style="display:none" id="locationClear" onclick="clearBtn('geohash')" class="badge badge-danger"><i class="la la-close"></i> Clear</span></label>
                         </div>
                      </div> -->
					  <?php
				if(taoh_user_is_logged_in()) {
                	include('search.php');
				}
			?>
					  <div id='loaderArea'></div>
                      <div id="eventArea">Loading ...</div>
					  <div id="pagination"></div>

                    </div><!-- end col-lg-8 -->
                    <div class="col-lg-4">
                        <div class="sidebar">
							<?php if ( taoh_user_is_logged_in() && $empty ){ ?>
								<div class="card card-item">
										<div class="card-body">
											<h3 class="fs-17 pb-3">My Active Job Chats <span id="activeListloaderArea"></span></h3>
											<div class="divider mb-4"><span></span></div>
											<div id="activeChatList" style="height:450px;overflow:auto;"></div>
										</div>
								</div><!-- end card -->
								<?php } ?>

								<?php //if (function_exists('taoh_stats_widget')) { taoh_stats_widget();  } ?>
								<div class="sidebar">
<?php

if ( isset( $taoh_user_vars->type ) && $taoh_user_vars->type == 'employer'){
?> <div class="card card-item p-4">
	<h3 class="fs-17 pb-3 text-color-2">Post jobs and find candidates</h3>
	<div class="divider"><span></span></div>
		<p class="fs-14 lh-22 pb-2 pt-3">Post jobs, engage candidates and find the best match</p>
		<div id="accordion" class="generic-accordion pt-4">
			<div class="card">
				<div class="card-header" id="headingOne">
					<button class="btn btn-link fs-15" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
						<span><span class="pr-2 fs-16">1.</span> Post open roles</span>
						<i class="la la-angle-down collapse-icon"></i>
					</button>
				</div>
				<div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
					<div class="card-body">
						<ul class="generic-list-item generic-list-item-bullet generic-list-item--bullet-2 fs-14">
							<li class="lh-18 text-black-50 mb-0">Click post a job button and add 1 or multiple jobs. Add job details.</li>
						</ul>
					</div>
				</div>
			</div><!-- end card -->
			<div class="card">
				<div class="card-header" id="headingTwo">
					<button class="btn btn-link collapsed fs-15" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
						<span><span class="pr-2 fs-16">2.</span> Engage with candidates</span>
						<i class="la la-angle-down collapse-icon"></i>
					</button>
				</div>
				<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
					<div class="card-body">
						<p class="fs-14 lh-22 text-black-50">
							Candidates may reach out to you if they have questions about specific jobs through JobChat or through email. Answer their queries and engage them to get the suitable candidates to apply.
						</p>
					</div>
				</div>
			</div><!-- end card -->
			<div class="card">
				<div class="card-header" id="headingThree">
					<button class="btn btn-link collapsed fs-15" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
						<span><span class="pr-2 fs-16">3.</span> Select best candidates</span>
						<i class="la la-angle-down collapse-icon"></i>
					</button>
				</div>
				<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
					<div class="card-body">
						<p class="fs-14 lh-22 text-black-50">
							Once candidates apply through your preferred means, select the best suited.
						</p>
					</div>
				</div>
			</div><!-- end card -->
		</div><!-- end accordion -->
</div><!-- end card -->
<?php
} else {
?>
<div class="card card-item p-4 mob-hide">
	<h3 class="fs-17 pb-3 text-color-2">Search for jobs and apply</h3>
	<div class="divider"><span></span></div>
	<p class="fs-14 lh-22 pb-2 pt-3">Chat with a recruiter and apply to a job for informed candidacy and better outcomes.</p>
	<div id="accordion" class="generic-accordion pt-4">
			<div class="card">
					<div class="card-header" id="headingOne">
							<button class="btn btn-link fs-15" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
									<span><span class="pr-2 fs-16">1.</span> Search for jobs</span>
									<i class="la la-angle-down collapse-icon"></i>
							</button>
					</div>
					<div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
							<div class="card-body">
									<ul class="generic-list-item generic-list-item-bullet generic-list-item--bullet-2 fs-14">
											<li class="lh-18 text-black-50 mb-0">Fill job role and location in the search box at the top of the page and click Search to find open roles.</li>
									</ul>
							</div>
					</div>
			</div><!-- end card -->
			<div class="card">
					<div class="card-header" id="headingTwo">
							<button class="btn btn-link collapsed fs-15" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
									<span><span class="pr-2 fs-16">2.</span> Chat with recruiters</span>
									<i class="la la-angle-down collapse-icon"></i>
							</button>
					</div>
					<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
							<div class="card-body">
									<p class="fs-14 lh-22 text-black-50">
											Click the jobs that you are interested in to see the details. If you have any questions, click the JobChat button to send a message to the recruiter. The recruiter will answer your questions, so you only apply to jobs that are the best fit to improve your success rate.
									</p>
							</div>
					</div>
			</div><!-- end card -->
			<div class="card">
					<div class="card-header" id="headingThree">
							<button class="btn btn-link collapsed fs-15" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
									<span><span class="pr-2 fs-16">3.</span> Apply for jobs</span>
									<i class="la la-angle-down collapse-icon"></i>
							</button>
					</div>
					<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
							<div class="card-body">
									<p class="fs-14 lh-22 text-black-50">
											The Job description page will have the details of how to apply for the job. Check out other resources and apps on Hires to help with job application, networking with other professionals and preparing for job interviews.
									</p>
							</div>
					</div>
			</div><!-- end card -->
	</div><!-- end accordion -->
</div><!-- end card -->

<?php
}
?>
			</div>
						<?php if (function_exists('taoh_new_common_ads_widget')) { taoh_new_common_ads_widget(TAO_PAGE_TYPE);  } ?>
						<?php if (function_exists('taoh_jobs_networking_widget')) { taoh_jobs_networking_widget();  } ?>
						<?php if (function_exists('taoh_get_recent_jobs')) { taoh_get_recent_jobs();  } ?>
						<?php if (function_exists('taoh_invite_friends_widget')) { taoh_invite_friends_widget('','jobs');  } ?>
						<?php if (function_exists('taoh_jusask_widget')) { taoh_jusask_widget();  } ?>

						<?php if (function_exists('taoh_tao_widget')) { taoh_tao_widget();  } ?>
						<?php if (function_exists('taoh_asks_widget')) { taoh_asks_widget();  } ?>

						<?php if (function_exists('taoh_readables_widget')) { taoh_readables_widget();  } ?>

						<?php if (function_exists('taoh_ads_widget')) { taoh_ads_widget();  } ?>
						<?php if (function_exists('taoh_new_ads_widget')) { taoh_new_ads_widget();  } ?>

                    </div><!-- end col-lg-4 -->
                </div><!-- end row -->
            </div><!-- end tab-pane -->
        </div><!-- end tab-content -->
    </div><!-- end container -->
</section><!-- end question-area -->

<div id="myModal" class="modal fade apply_modal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Job Apply Form</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <div class="job-details-panel mt-30px mb-30px job_apply_form" id="job-apply">
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
                            <div class="mb-40px">
                                <h5 class="fs-14 text-uppercase mb-3 text-gray">1. Personal Details</h5>
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium">First Name <span style="color:red;">*</span></label>
                                    <?php
                                    if(isset($taoh_user_vars->profile_complete)
                                    && $taoh_user_vars->profile_complete == 0 && isset($taoh_user_vars->fname) && $taoh_user_vars->fname == TAOH_SITE_NAME_SLUG)
                                    echo field_fname();
                                    else
                                    echo field_fname($taoh_user_vars->fname); ?>
                                </div><!-- end form-group -->
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium">Last Name <span style="color:red;">*</span></label>
                                    <?php
                                    if(isset($taoh_user_vars->profile_complete)
                                    && $taoh_user_vars->profile_complete == 0 && isset($taoh_user_vars->fname) && $taoh_user_vars->fname == TAOH_SITE_NAME_SLUG)
                                    echo field_lname();
                                    else
                                    echo field_lname($taoh_user_vars->lname); ?>
                                </div><!-- end form-group -->
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium">Email <span style="color:red;">*</span></label>
                                    <?php echo field_email($taoh_user_vars->email); ?>
                                </div><!-- end form-group -->
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium">Place of residence <span style="color:red;">*</span></label>
                                    <?php echo field_job_location($taoh_user_vars->coordinates,$taoh_user_vars->full_location, $taoh_user_vars->geohash); ?>
                                </div><!-- end form-group -->
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium">Current Company</label>
                                    <?php echo field_company( ( isset( $taoh_user_vars->company ) && $taoh_user_vars->company ) ? $taoh_user_vars->company: '' ); ?>
                                </div><!-- end form-group -->
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium">Resume <span style="color:red;">*</span></label>
                                    <div class="custom-file mb-3">
                                        <input type="file" class="custom-file-input" id="fileToUpload" name="fileToUpload">
                                        <label class="custom-file-label" for="customFile">Choose file</label>
                                        <div id="responseMessage" style="display: none;"></div>
                                        <p id="error1" style="display:none; color:#FF0000;">
                                            Invalid Image Format! Image Format Must Be JPG, JPEG, PNG or GIF.
                                        </p>
                                        <p id="error2" style="display:none; color:#FF0000;">
                                            Maximum File Size Limit is 5MB.
                                        </p>
                                    </div>
                                    <input type="hidden" name="resume_link" class="resume_link">
                                </div><!-- end form-group -->
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium">Cover Letter</label>
                                    <textarea class="summernote" id="cover_letter_id" rows="10" cols="80" required></textarea>
                                </div><!-- end form-group -->
                            </div>
                            <div class="mb-40px">
                                <h5 class="fs-14 text-uppercase mb-3 text-gray">2. links</h5>
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium">LinkedIn URL</label>
                                    <input type="text" class="form-control form--control fs-14" name="linkedin_url" placeholder="LinkedIn URL">
                                </div><!-- end form-group -->
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium">GitHub URL</label>
                                    <input type="text" class="form-control form--control fs-14" name="github_url" placeholder="GitHub URL">
                                </div><!-- end form-group -->
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium">Portfolio URL</label>
                                    <input type="text" class="form-control form--control fs-14" name="port_url" placeholder="Portfolio URL">
                                </div><!-- end form-group -->
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium">Website URL</label>
                                    <input type="text" class="form-control form--control fs-14" name="web_url" placeholder="Website URL">
                                </div><!-- end form-group -->
                            </div>
                            <div class="mb-40px">
                                <h5 class="fs-14 text-uppercase mb-3 text-gray">3. additional information</h5>
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium">Let the company know about your interest in the organization.</label>
                                    <textarea class="form-control form--control fs-14" name="addntl_info" rows="5"></textarea>
                                </div><!-- end form-group -->
                                <input type="hidden" name="enable_scout_apply" id="enable_scout_apply" value="" >
                                <button class="btn theme-btn mt-2 submit" type="submit">Submit Application</button>
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
    <div class="modal-content">
      <div class="modal-header">
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
<script>

	let isLoggedIn = "<?php echo taoh_user_is_logged_in(); ?>";
	let loaderArea = $('#loaderArea');
  	let searchQuery = $('#searchQuery');
	let eventArea = $('#eventArea');
	let locationSelectInput = $('#locationSelect');
	let geohashInput = $('#geohash');
  	let currentMod = '<?php echo ($app_data?->slug ?? ''); ?>';
	//let geohash = "";
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
	let app_slug = 'jobs';
	let arr_cont = [];
	let liked_arr = '<?php echo $liked_arr; ?>';
	//var job_list_name = "jobs_list";
	var already_rendered = false;
	var get_slug = false;
	var job_list_name = "";
	var store_name = JOBStore;

	loader(true, loaderArea);
	//Initial run
	$(document).ready(function(){
    	$('.ts-control').css('height', '37px');
		//taoh_scout_list();
		<?php if(TAOH_INTAODB_ENABLE) { ?>
			console.log('list ajax start time:', new Date().getTime());
			getjoblistdata();
		<?php }else{ ?>
			taoh_jobs_init();
		<?php } ?>
		//taoh_jobs_init();
	})

    const postdateSelect = document.getElementById('postdate');
    const dateRangeInputs = document.getElementById('dateRangeInputs');

    postdateSelect.addEventListener('change', function () {
        if (this.value === 'date_range') {
            dateRangeInputs.style.display = 'flex';
        } else {
            dateRangeInputs.style.display = 'none';
			$('#from_date').val('');
			$('#to_date').val('');
        }
    });

	function searchFilter() {
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
		<?php if(TAOH_INTAODB_ENABLE) { ?>
			getjoblistdata(queryString);
		<?php }else{ ?>
			taoh_jobs_init();
		<?php } ?>
	}

	function getjoblistdata(queryString=''){
		// Open or create a database
		getIntaoDb(dbName).then((db) => {
			var currpage = currentPage-1;
			var job_list_hash = search+geohash+queryString+currpage+itemsPerPage+postDate+from_date+to_date;
			job_list_name = 'jobs_'+crc32(job_list_hash);
			console.log(job_list_name);
			const datarjobequest = db.transaction(store_name).objectStore(store_name).get(job_list_name); // get main data
			datarjobequest.onsuccess = ()=> {
				console.log(datarjobequest);
				const jobstoredatares = datarjobequest.result;
				if(jobstoredatares !== undefined && jobstoredatares !== null && jobstoredatares !== "" && jobstoredatares !== "undefined" && jobstoredatares !== "null"){
					console.log('list ajax intaodb call start time:', new Date().getTime());
					const jobstoredata = datarjobequest.result.values;
					get_slug = true;
					already_rendered = true;
					loader(false, loaderArea);
					render_jobs_template(jobstoredata, eventArea);
					//taoh_jobs_init(queryString);
				}else{
					console.log('list ajax api call start time:', new Date().getTime());
					get_slug = false;
					loader(true, loaderArea);
					taoh_jobs_init(queryString);
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
						getjoblistdata();
						<?php }else{ ?>
							taoh_jobs_init();
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
        $('#locationSelect').val("");
        $('#geohash').val("");
        geohash = "";
        $('.ts-control div.item').html('');
    }
	//eventArea.empty();
	//$('#pagination').empty();
	already_rendered = false;
	<?php if(TAOH_INTAODB_ENABLE) { ?>
		getjoblistdata();
	<?php }else{ ?>
		taoh_jobs_init();
	<?php } ?>
  }
function taoh_scout_list(){
	var data = {
       'taoh_action': 'jobs_scout_list',
       'mod': 'jobs',
	   'ptoken': '<?php echo $ptoken; ?>',
     };
    jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
      	res = JSON.stringify(response['output']);
		localStorage.setItem('scout_list', res);
		const str = localStorage.getItem('scout_list');
		const parsedArray = JSON.parse(str);

		console.log('scout_list',parsedArray);
    }).fail(function() {
        console.log( "Network issue!" );
    })
}

function taoh_jobs_init (queryString=""){
	search = $('#query').val();
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
	if($('#locationSelect-ts-control').val() == ''){
		$('#coordinateLocation').val('');
		geohashInput.val('');
	}
	postDate = $('#postdate').val();
	from_date = $('#from_date').val();
	to_date = $('#to_date').val();
	console.log('----search------',$('#query').val());
	console.log('----geohash------',$('#geohash').val());
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
	   'ptoken': '<?php echo $ptoken; ?>',
	   'filters': queryString,
     };
    jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(listresponse) {
	  	<?php if(TAOH_INTAODB_ENABLE) { ?>
			if(!get_slug){
				indx_jobs_list(listresponse);
			}
			if(!already_rendered){
				render_jobs_template(listresponse, eventArea);
			}
		<?php }else{ ?>
			render_jobs_template(listresponse, eventArea);
		<?php } ?>
	  loader(false, loaderArea);
    }).fail(function() {
        loader(false, loaderArea);
        console.log( "Network issue!" );
    })
  }

  function render_jobs_template(data, slot) {
    console.log('list ajax html construct start time:', new Date().getTime());
    //console.log("render", data)
	slot.empty();
    //console.log("output", "location trigered");
	if(data.output === false) {
		slot.append("<p>No data found!</p>");
		$('#pagination').hide();
		return false;
	}
	if(data.success  === false) {
		slot.append("<p>No data found!</p>");
		$('#pagination').hide();
		return false;
	}

	totalItems = data.output.total
	jobCount.append(totalItems + ' jobs Found');
	if(!get_slug){
    	var result = format_object(data);
	}else{
		var result = data;
	}
	console.log('format', result);
	var profile = '<?php echo $_SESSION[TAOH_ROOT_PATH_HASH]['USER_INFO']->type;?>';
		$.each(result.output.list, function(i, v){
			arr_cont.push(v.conttoken.toString());
			if(jQuery.inArray(v.conttoken,liked_arr) !== -1){
				var get_liked = 1;
			}else{
				var get_liked = 0;
			}
			let is_local = localStorage.getItem(app_slug+'_'+v.conttoken+'_liked');
			if ((get_liked) || (is_local)) {
				var liked_check = `<a style="font-size:20px;" class=""><i title="Like" class="la la-heart text-danger"></i></a>&nbsp;<?php if (TAOH_METRICS_COUNT_SHOW) { ?><span id="likeCount" data-conts="${(v.conttoken)}" class="badge text-dark fs-14 p-0 met_like"></span><?php } ?>`
			} else {
				var liked_check = `<a style="font-size:20px;" class=""><i style="cursor:pointer;" title="Like" data-cont="${(v.conttoken)}" class="la la-heart text-gray jobs_like"></i></a>&nbsp;<?php if (TAOH_METRICS_COUNT_SHOW) { ?><span id="likeCount" data-conts="${(v.conttoken)}" class="badge text-dark fs-14 p-0 met_like"></span><?php } ?>`
			}
			var shares_count = `<a class=""><i title="Share" style="cursor:pointer;font-size:20px;" data-conttoken="${(v.conttoken)}" data-title="${(displayTaohFormatted(v.title))}" data-ptoken = "<?php echo $ptoken; ?>" data-share = "<?php echo $share_link; ?>" class="la la-share text-primary share_box"></i></a>&nbsp;<?php if (TAOH_METRICS_COUNT_SHOW) { ?><span id="shareCount" data-conts="${(v.conttoken)}" class="badge text-dark fs-14 p-0 met_share"></span><?php } ?>`

			var scout_list = localStorage.getItem('scout_list');
			scout_list = JSON.parse(scout_list);
            console.log(scout_list);
			var apply_email_link = '';
			/* $.each(scout_list, function(i, con){
				console.log('con',con);
				if(con == v.conttoken){
					//v.enable_scout_job = true;
					apply_email_link = `<a href="#" target="_blank"  class="tag-link text-primary">APPLY</a>`
				}else if(v.apply_link){
					apply_email_link = `<a href="${v.apply_link}" target="_blank"  class="tag-link text-primary">APPLY</a>`
				}else if((v.email) && (v.enable_apply)){
					apply_email_link = `<a href="#" class="tag-link text-primary open_modal" data-position="${(v.title)}" data-company="${(v.company)}" data-fname="${(v.fname)}" data-toemail="${(v.email)}" data-conttoken="${(v.conttoken)}">APPLY</a>`
				}else{
					apply_email_link = `<a href="${'mailto:'+v.email}" target="_blank"  class="tag-link text-primary">APPLY</a>`
				}
			}); */

				if(v.enable_scout_job == 'on'){
					apply_email_link = ``
				}else{
					if(v.apply_link){
						apply_email_link = `<a href="${v.apply_link}" target="_blank"  class="tag-link text-primary">APPLY</a>`
					}else if((v.email) && (v.enable_apply)){
						apply_email_link = `<a href="#" class="tag-link text-primary open_modal" data-position="${(v.title)}" data-company="${(v.company)}" data-fname="${(v.fname)}" data-toemail="${(v.email)}" data-conttoken="${(v.conttoken)}">APPLY</a>`
					}else{
						apply_email_link = `<a href="${'mailto:'+v.email}" target="_blank"  class="tag-link text-primary">APPLY</a>`
					}
				}

			/*if(v.enable_scout_job && profile != 'recruiter'){
				apply_email_link += `<a href="<?php echo TAOH_SITE_URL_ROOT."/".($app_data?->slug ?? '')."/scouts-dashboard"; ?>" target="_blank"  class="tag-link text-primary">Apply through Scout</a>`
			}*/
			/* <a class="dash_metrics" data-metrics="view" conttoken="${v.conttoken}" data-type="jobs" onclick="taoh_jobs_detail('${(v.conttoken)}');">${displayTaohFormatted(v.title)}</a> */ //job detail api call
			slot.append(
			`<div class="col-lg-12  media media-card media--card align-items-center">
				<div class="media-body border-left-0">
					<h5 class="pb-1">
						<a class="dash_metrics" data-metrics="view" conttoken="${v.conttoken}" data-type="jobs" target='_blank' href="<?php echo TAOH_SITE_URL_ROOT."/".($app_data?->slug ?? '')."/d/"; ?>${convertToSlug(displayTaohFormatted(v.title))}-${v.conttoken}">${displayTaohFormatted(v.title)}</a>
						&nbsp;&nbsp;
						<?php

						if ( taoh_user_is_logged_in()) { ?>
							${apply_email_link}
							<div class="tags float-right">
								${liked_check}
								${shares_count}
							</div>
						<?php }

						?>
					</h5>
					<div class="col-lg-12">
						${(v.skill && v.skill.length > 0)? generateSkillHTML(v.skill): ''}
						${(v.rolechat && v.rolechat.length > 0)? generateRoleHTML(v.rolechat): ''}
						${(v.company && v.company.length)? generateCompanyHTML(v.company): ''}
					</div>
					<div class="col-lg-12">
						${v.full_location ? generateLocationHTML(v.full_location): ''}
					</div>
					</div>
			</div>`);
			/*<div class="col-lg-4">
						<button class="btn btn-primary" onclick="refer(this, ${v.conttoken})">Refer</button>

					</div>*/
		});
		if(data.output.total >= 11) {
			$('#pagination').show();
			show_pagination('#pagination');
		}else{
			$('#pagination').hide();
		}

		if(search){
			mertricsLoad();
		}
	}

    function taoh_jobs_detail(conttoken){
        var data = {
            'taoh_action': 'jobs_get_detail',
            'ops': 'detail',
            'conttoken': conttoken,
            'ptoken': '<?php echo $ptoken; ?>',
        };
        jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
            if(response.success){
                console.log(response);
            }
        }).fail(function() {
            console.log( "Network issue!" );
        })
    }


    $(document).on('click','.open_modal', function(event) {
        var mod_conttoken = $(this).attr("data-conttoken");
        var mod_position = $(this).attr("data-position");
        var mod_company_get = $(this).attr("data-company");
        var mod_company = mod_company_get[0].name;console.log(mod_company);
        var mod_toemail = $(this).attr("data-toemail");
        var mod_fname = $(this).attr("data-fname");
        $('.mod_conntoken').val(mod_conttoken);
        $('.recruiter_fname').val(mod_fname);
        $('.to_email').val(mod_toemail);
        $('.position_title').val(mod_position);
        $('.company_name').val(mod_company);
        $('.apply_modal').modal('show');
    });

    $('#fileToUpload').change(function() { //alert('file changed');
		var file = $('#fileToUpload')[0].files[0].name;
		$('.custom-file-label').html(file);
        var type = $('#fileToUpload')[0].files[0].name.split('.').pop();
        var size = $('#fileToUpload')[0].files[0].size;
        console.log(type);
        if(size > 5242880){
            $('#error2').show();
            return false;
        }
        $('#error2').hide();
        if(type != 'pdf' && type != 'doc' && type != 'docx'){
            $('#error1').show();
            return false;
        }
        $('#error1').hide();
        $('.error').hide();

        // Reference the form by its ID
        var form = document.getElementById('fileUploadForm');
        // Create a FormData object using the form ID
        var formData = new FormData(form);

        fetch("<?php echo TAOH_CDN_PREFIX; ?>/cache/upload/now", {
            method: "POST",
            body: formData,
        })
        .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((data) => {
                if (data.success) {
					var data_url = data.output;
                    $('.resume_link').val(data_url);
                } else {
                    document.getElementById("responseMessage").style.color = "red";
                    document.getElementById("responseMessage").innerHTML = "File upload failed: " + data.output;
                }
                document.getElementById("responseMessage").style.display = "block";
            })
            .catch((error) => {
                console.error("Error:", error);
                document.getElementById("responseMessage").style.color = "red";
                document.getElementById("responseMessage").innerHTML = "An error occurred: " + error.output;
                document.getElementById("responseMessage").style.display = "block";
            });
	});

    document.getElementById("fileUploadForm").addEventListener("submit", function (event) {
        event.preventDefault();
		if($("#fileUploadForm").valid()){
			$('.submit').prop("disabled", true);
			// add spinner to button
			$('.submit').html(
				`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`
			);
			var serialize = $('#fileUploadForm').serialize();
			var editor_val = $('.summernote').summernote('code');

			var apply_method = enable_scout_apply ? 'scout_apply' : 'apply';
			var datas = {
				'taoh_action': 'toah_metrics_push',
				'conttoken': $('.mod_conntoken').val(),
				'ptoken': '<?php echo $log_nolog_token; ?>',
				'met_action': 'applyform',
				'met_type': '<?php echo TAO_PAGE_TYPE ?>',
				'apply_method':apply_method,

			};
			jQuery.post("<?php echo taoh_site_ajax_url(); ?>", datas, function(response) {
				if(response.success){

				}else{
					console.log( "Like Failed!" );
				}
				var r_data = serialize+"&cover_letter=" + encodeURIComponent(editor_val);
				jQuery.post("<?php echo TAOH_ACTION_URL .'/jobs?uslo=2'; ?>", r_data, function(responses) {
					res = responses;console.log(res.success);
					if(res.success){
						$('.submit').prop("disabled", false);
						// add spinner to button
						$('.submit').html(
							`Submit`
						);
						$('.apply_modal').modal('hide');
						taoh_set_success_message('Your application has been submitted.');
						window.location.reload();
					}
					else{
						$('.submit').prop("disabled", true);
						// add spinner to button
						$('.submit').html(
							`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`
						);
					}
					}).fail(function() {
						console.log( "Network issue111!" );
					})
			}).fail(function() {
				console.log( "Network issue!" );
			})
		}
    });

    $("#fileUploadForm").validate({
        rules: {
            fname:"required",
            lname : "required",
            email : {
                required : true,
                email : true
            },
            coordinates:"required",
            fileToUpload:{
                required: true,
                extension: "pdf,doc,docx",
                filesize: 5242880 // <- 5 MB
            }
        },
        messages: {
            fname: "First Name is required",
            lname : "Last Name is required",
            email:{
                url : "Please enter vaild email"
            },
            coordinates:"Location is required",
            fileToUpload:{
                filesize:" file size must be less than 1MB.",
                extension:"Please upload .pdf or .doc or .docx file of notice.",
                required:"Please upload file."
            }
        },
    });


	$(document).on('click','.share_box', function(event) {
		var datatitle = $(this).attr("data-title");console.log(datatitle);
		var dataptoken = $(this).attr("data-ptoken");
		var datashare = $(this).attr("data-share");
		var dataconttoken = $(this).attr("data-conttoken");
		var share_link = datashare+'/d/'+dataconttoken;
		var dat_ajax = '<?php echo TAOH_SITE_URL_ROOT.'/ajax'?>';
		var image = '<?php echo ( defined( 'TAO_PAGE_IMAGE' )? TAO_PAGE_IMAGE : '' )?>';
		var desc = '<?php echo (urlencode( defined( 'TAO_PAGE_DESCRIPTION' )? substr(TAO_PAGE_DESCRIPTION, 0, 240) : '' ))?>';
		var title = '<?php echo ( defined( 'TAO_PAGE_TITLE' )? TAO_PAGE_TITLE : '' )?>';
		var fb_share = "http://www.facebook.com/sharer.php?s=100&p[url]="+share_link+"&p[images][0]="+image+"&p[title]="+title+"&p[summary]="+desc;
		var tw_share = "https://twitter.com/intent/tweet?text="+title+"&url="+share_link;
		var link_share = "https://www.linkedin.com/shareArticle?mini=true&url="+share_link+"&title="+title+"&summary="+desc+"&images="+image;
		var email_share = "mailto:?subject=I wanted you to see this site&amp;body=Check out this site "+title+share_link;

		$("#share_icon").html(`
						<div class="social-icon-box d-flex text-center" data-ajax="${(dat_ajax)}" data-conttype="<?php echo ($app_data?->slug ?? ''); ?>">
							<a data-gtitle="${(datatitle)}" data-gptoken="${(dataptoken)}" data-gshare="${(datashare)}" data-gconntoken="${(dataconttoken)}" data-social="${(fb_share)}" class="ml-3 icon-element icon-element-sm shadow-sm text-gray hover-y share_count" data-click="facebook" style="background-color:#365899; margin-bottom:10px;cursor:pointer;" target="_blank" title="Share on Facebook">
								<svg focusable="false" class="svg-inline--fa fa-facebook-f fa-w-10" style="color: white;" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"></path></svg>
							</a>
							<a data-gtitle="${(datatitle)}" data-gptoken="${(dataptoken)}" data-gshare="${(datashare)}" data-gconntoken="${(dataconttoken)}" data-social="${(tw_share)}" class="ml-3 icon-element icon-element-sm shadow-sm text-gray hover-y share_count" data-click="twitter" style="background-color:#00acee; margin-bottom:10px;cursor:pointer;" target="_blank" title="Share on Twitter">
								<svg focusable="false" class="svg-inline--fa fa-twitter fa-w-16" style="color: white;" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"></path></svg>
							</a>
							<a data-gtitle="${(datatitle)}" data-gptoken="${(dataptoken)}" data-gshare="${(datashare)}" data-gconntoken="${(dataconttoken)}" data-social="${(link_share)}" class="ml-3 icon-element icon-element-sm shadow-sm text-gray hover-y share_count" data-click="linkedin" style="background-color:#0A66C2; margin-bottom:10px;cursor:pointer;" target="_blank" title="Share on Linkedin">
								<svg focusable="false" class="svg-inline--fa fa-linkedin fa-w-14" style="color: white;" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M416 32H31.9C14.3 32 0 46.5 0 64.3v383.4C0 465.5 14.3 480 31.9 480H416c17.6 0 32-14.5 32-32.3V64.3c0-17.8-14.4-32.3-32-32.3zM135.4 416H69V202.2h66.5V416zm-33.2-243c-21.3 0-38.5-17.3-38.5-38.5S80.9 96 102.2 96c21.2 0 38.5 17.3 38.5 38.5 0 21.3-17.2 38.5-38.5 38.5zm282.1 243h-66.4V312c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9V416h-66.4V202.2h63.7v29.2h.9c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9V416z"></path></svg>
							</a>
							<a data-gtitle="${(datatitle)}" data-gptoken="${(dataptoken)}" data-gshare="${(datashare)}" data-gconntoken="${(dataconttoken)}" data-social="${(email_share)}" class="ml-3 icon-element icon-element-sm shadow-sm text-gray hover-y share_count" data-click="email" style="background-color:#B23121; margin-bottom:10px;cursor:pointer;" target="_blank" title="Share vai Email">
								<svg focusable="false" class="svg-inline--fa fa-envelope fa-w-16" style="color: white;" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"></path></svg>
							</a>
						</div>
						<div class="text-center mt-2 mb-2"> or </div>
							<span class="text-success-message">Link Copied!</span>
								<input type="text" style="display:none;" class="form-control form--control form--control-bg-gray copys-input" id="copys-input" value="${(share_link)}">
								<div class="copys-btn text-center" style="cursor: pointer;"> <i class="fas fa-copy"></i> Copy URL</div>
					`);
		$("#exampleModal1").modal('show');
	});

	$(document).on('click','.jobs_like', function(event) {
		var conttoken = $(this).attr("data-cont");console.log(conttoken);
		var likes = $('.met_like[data-conts="'+conttoken+'"]').html();
		var count_like = (likes==''?0:parseInt(likes)) + parseInt(1);
		$('.met_like[data-conts="'+conttoken+'"]').html(count_like > like_min ? (count_like):'');
		$(".tags a").find(`[data-cont='${conttoken}']`).removeClass('text-gray').addClass('text-danger').removeClass('jobs_like');
		$(".tags a").find(`[data-cont='${conttoken}']`).removeAttr("onclick");
		$(".tags a").find(`[data-cont='${conttoken}']`).removeAttr("style");
		localStorage.setItem(app_slug+'_'+conttoken+'_liked',1);
		var data = {
			 'taoh_action': 'job_like_put',
			 'conttoken': conttoken,
			 'ptoken': '<?php echo $ptoken; ?>',
		};
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
			if(response.success){

			}else{
				console.log( "Like Failed!" );
			}
		}).fail(function() {
			console.log( "Network issue!" );
		})
	});


  /* locationSelectInput.on("change", function() {
	var latlong = $('.item').attr("data-value");
		var split = latlong.split("::");
		loader(true, geoloaderArea);
		var data = {
			'taoh_action': 'taoh_get_geohash',
			'lon': split[1],
			'lat': split[0],
		};
		jQuery.post("<?php //echo taoh_site_ajax_url(); ?>", data, function(response) {
			console.log(response.geohash);
			geohashInput.val(response.geohash);
			loader(false, geoloaderArea);
		}).fail(function() {
			console.log( "Network issue!" );
			loader(false, geoloaderArea);
		})

	}) */


  /*function do_search(e) {
		search = $(e).val();
		eventArea.empty();
		$('#pagination').empty();
    	taoh_jobs_init();
	}*/

/*$(document).on('click','.dash_metrics', function(event) {
	var metrics = $(this).attr("data-metrics");
	var data = {
		'taoh_action': 'toah_metrics_push',
		'conttoken': conttoken,
		'ptoken': '<?php echo $log_nolog_token; ?>',
		'met_action': metrics,
		'met_type': '<?php echo TAO_PAGE_TYPE ?>',
		'met_main_var': '<?php echo TAO_PAGE_TYPE.'_'.$conttoken.'_';?>'+metrics,
	};
	jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
		if(response.success){
			if(metrics == 'post'){
				window.location.href = '<?php //echo TAOH_DASH_URL."?app=".$app_data->slug."&from=dash&to=".$app_data->slug."/post"; ?>';
			}
		}else{
			console.log( "Like Failed!" );
		}
	}).fail(function() {
		console.log( "Network issue!" );
	})
});*/

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
