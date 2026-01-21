<?php  taoh_get_header(); 
$current_status = "<a itemprop=\"".TAOH_SITE_URL_ROOT."/settings\" style=\"font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #ffffff; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: block; border-radius: 5px; text-transform: capitalize; background-color: #32A250; margin: 0; border-color: #32A250; border-style: solid; border-width: 10px 20px;\"> Checkout Settings!</a>";

if( ! taoh_user_is_logged_in() ) {
  $current_status = "<a href=\"".TAOH_SITE_URL_ROOT."/login\" itemprop=\"url\" style=\"font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #eaf0f7; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: block; border-radius: 5px; text-transform: capitalize; background-color: #0a80ff; margin: 0; border-color: #0a80ff; border-style: solid; border-width: 10px 20px;\" target=\"_BLANK\">Login/Sign Up *</a><right>* Link opens in new tab</right>";
} 
$current_app = TAOH_SITE_CURRENT_APP_SLUG;  
$app_data = taoh_app_info($current_app);
$array_json =  taoh_url_get_content( TAOH_CDN_PREFIX."/app/$current_app/faq.php" );
$array = json_decode($array_json);
$about_url = TAOH_SITE_URL_ROOT."/about";
if ( $current_app != TAOH_PLUGIN_PATH_NAME ) $about_url = TAOH_SITE_URL_ROOT."/".$current_app."/about";

?>
<section class="hero-area pattern-bg-2 bg-white shadow-sm overflow-hidden pt-30px pb-30px">
    <span class="stroke-shape stroke-shape-1"></span>
    <span class="stroke-shape stroke-shape-2"></span>
    <span class="stroke-shape stroke-shape-3"></span>
    <span class="stroke-shape stroke-shape-4"></span>
    <span class="stroke-shape stroke-shape-5"></span>
    <span class="stroke-shape stroke-shape-6"></span>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5">
                <div class="hero-content py-5">
                    <h3 class="fs-19 fw-medium pb-3"><?php echo $app_data->name_slug; ?></h3>
                    <h2 class="section-title fs-40 pb-3 lh-55"><?php echo $app_data->short; ?></h2>
                    <p class="section-desc pb-4"><?php echo $app_data->desc; ?></p>
                    <?php
                    if ( ! taoh_user_is_logged_in() ){
                        ?>
                        <a href="<?php echo TAOH_SITE_URL_ROOT."/login"; ?>" class="btn theme-btn">Get Started <i class="la la-sign-in mr-1"></i></i></a>
                        <?php
                    }
                    ?>
                </div><!-- end hero-content -->
            </div><!-- end col-lg-5 -->
             <div class="col-lg-5 ml-auto">
                 <div class="video-box">
                     <img class="w-100 rounded-rounded lazy" src="<?php echo $app_data->thumbnail; ?>" data-src="<?php echo $app_data->thumbnail; ?>" alt="video image">
                     <div class="video-content text-center">
                         <a class="icon-element icon-element-lg hover-y mx-auto" href="<?php echo $app_data->video; ?>" data-fancybox="" title="Play Video">
                             <svg width="24" height="24" version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 58.752 58.752" xml:space="preserve">
                                        <path fill="#0d233e" d="M52.524,23.925L12.507,0.824c-1.907-1.1-4.376-1.097-6.276,0C4.293,1.94,3.088,4.025,3.088,6.264v46.205c0,2.24,1.204,4.325,3.131,5.435c0.953,0.555,2.042,0.848,3.149,0.848c1.104,0,2.192-0.292,3.141-0.843l40.017-23.103c1.936-1.119,3.138-3.203,3.138-5.439C55.663,27.134,54.462,25.05,52.524,23.925z M49.524,29.612L9.504,52.716c-0.082,0.047-0.18,0.052-0.279-0.005c-0.084-0.049-0.137-0.142-0.137-0.242V6.263c0-0.1,0.052-0.192,0.14-0.243c0.042-0.025,0.09-0.038,0.139-0.038c0.051,0,0.099,0.013,0.142,0.038l40.01,23.098c0.089,0.052,0.145,0.147,0.145,0.249C49.663,29.47,49.611,29.561,49.524,29.612z"/>
                             </svg>
                         </a>
                         <p class="mt-2 badge badge-light">Watch our video</p>
                     </div>
                 </div>
            </div><!-- end col-lg-5 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section><!-- end hero-area -->


<!-- ================================
         START BENEFITS AREA
================================= -->
<section class="benefits-area pb-90px bg-light">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title pb-3"><br />Benefits</h2>
            <p class="section-desc w-75 mx-auto">Asks provides you with a success network, so you are never alone in your career pursuits.</p>
        </div>
        <div class="row pt-50px">
            <div class="col-lg-4 responsive-column-half">
                <div class="card card-item hover-y">
                    <div class="card-body">
                        <div class="icon-element icon-element-lg bg-1 shadow-none">
                            <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 0 24 24" width="40px" fill="#ffffff"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M21 3H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h5v2h8v-2h5c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 14H3V5h18v12zm-2-9H8v2h11V8zm0 4H8v2h11v-2zM7 8H5v2h2V8zm0 4H5v2h2v-2z"/></svg>
                        </div>
                        <h5 class="card-title pt-4 pb-2">Career Questions Solved</h5>
                        <p class="card-text">Ask your career questions from a trusted peer group of workers to grow.</p>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col-lg-4 -->
            <div class="col-lg-4 responsive-column-half">
                <div class="card card-item hover-y">
                    <div class="card-body">
                        <div class="icon-element icon-element-lg bg-2 shadow-none">
													<svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="40px" viewBox="0 0 24 24" width="40px" fill="#ffffff"><g><rect fill="none" height="24" width="24"/></g><g><path d="M6.05,8.05c-2.73,2.73-2.73,7.17,0,9.9C7.42,19.32,9.21,20,11,20s3.58-0.68,4.95-2.05C19.43,14.47,20,4,20,4 S9.53,4.57,6.05,8.05z M14.54,16.54C13.59,17.48,12.34,18,11,18c-0.89,0-1.73-0.25-2.48-0.68c0.92-2.88,2.62-5.41,4.88-7.32 c-2.63,1.36-4.84,3.46-6.37,6c-1.48-1.96-1.35-4.75,0.44-6.54C9.21,7.72,14.04,6.65,17.8,6.2C17.35,9.96,16.28,14.79,14.54,16.54z"/></g></svg>
                        </div>
                        <h5 class="card-title pt-4 pb-2">Build Growth Community</h5>
                        <p class="card-text">Everyone needs a professional peer group to grow and succeed. Build yours!</p>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col-lg-4 -->
            <div class="col-lg-4 responsive-column-half">
                <div class="card card-item hover-y">
                    <div class="card-body">
                        <div class="icon-element icon-element-lg bg-3 shadow-none">
													<svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="40px" viewBox="0 0 24 24" width="40px" fill="#ffffff"><g><rect fill="none" height="24" width="24"/></g><g><g/><g><path d="M16.67,13.13C18.04,14.06,19,15.32,19,17v3h4v-3C23,14.82,19.43,13.53,16.67,13.13z"/><path d="M15,12c2.21,0,4-1.79,4-4c0-2.21-1.79-4-4-4c-0.47,0-0.91,0.1-1.33,0.24C14.5,5.27,15,6.58,15,8s-0.5,2.73-1.33,3.76 C14.09,11.9,14.53,12,15,12z"/><path d="M9,12c2.21,0,4-1.79,4-4c0-2.21-1.79-4-4-4S5,5.79,5,8C5,10.21,6.79,12,9,12z M9,6c1.1,0,2,0.9,2,2c0,1.1-0.9,2-2,2 S7,9.1,7,8C7,6.9,7.9,6,9,6z"/><path d="M9,13c-2.67,0-8,1.34-8,4v3h16v-3C17,14.34,11.67,13,9,13z M15,18H3l0-0.99C3.2,16.29,6.3,15,9,15s5.8,1.29,6,2V18z"/></g></g></svg>
                        </div>
                        <h5 class="card-title pt-4 pb-2">Support Others Success</h5>
                        <p class="card-text">Give back and feel happy by supporting other's success by answering questions.</p>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col-lg-4 -->
            <div class="col-lg-4 responsive-column-half">
                <div class="card card-item hover-y">
                    <div class="card-body">
                        <div class="icon-element icon-element-lg bg-4 shadow-none">
                            <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 0 24 24" width="40px" fill="#ffffff"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/></svg>
                        </div>
                        <h5 class="card-title pt-4 pb-2">Learn From Peers
</h5>
                        <p class="card-text">Learning is a two way street. Learn as you engage with others on the platform.</p>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col-lg-4 -->
            <div class="col-lg-4 responsive-column-half">
                <div class="card card-item hover-y">
                    <div class="card-body">
                        <div class="icon-element icon-element-lg bg-5 shadow-none">
                            <svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="40px" viewBox="0 0 24 24" width="40px" fill="#ffffff"><g><rect fill="none" height="24" width="24"></rect></g><g><g><g><g><path d="M16,13c3.09-2.81,6-5.44,6-7.7C22,3.45,20.55,2,18.7,2c-1.04,0-2.05,0.49-2.7,1.25C15.34,2.49,14.34,2,13.3,2 C11.45,2,10,3.45,10,5.3C10,7.56,12.91,10.19,16,13z M13.3,4c0.44,0,0.89,0.21,1.18,0.55L16,6.34l1.52-1.79 C17.81,4.21,18.26,4,18.7,4C19.44,4,20,4.56,20,5.3c0,1.12-2.04,3.17-4,4.99c-1.96-1.82-4-3.88-4-4.99C12,4.56,12.56,4,13.3,4z"></path><path d="M19,16h-2c0-1.2-0.75-2.28-1.87-2.7L8.97,11H1v11h6v-1.44l7,1.94l8-2.5v-1C22,17.34,20.66,16,19,16z M3,20v-7h2v7H3z M13.97,20.41L7,18.48V13h1.61l5.82,2.17C14.77,15.3,15,15.63,15,16c0,0-1.99-0.05-2.3-0.15l-2.38-0.79l-0.63,1.9l2.38,0.79 c0.51,0.17,1.04,0.26,1.58,0.26H19c0.39,0,0.74,0.23,0.9,0.56L13.97,20.41z"></path></g></g></g></g></svg>
                        </div>
                        <h5 class="card-title pt-4 pb-2">Build Your Reputation	</h5>
                        <p class="card-text">Build your professional reputation for specific skills as you support others queries.</p>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col-lg-4 -->
            <div class="col-lg-4 responsive-column-half">
                <div class="card card-item hover-y">
                    <div class="card-body">
                        <div class="icon-element icon-element-lg bg-6 shadow-none">
                            <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 0 24 24" width="40px" fill="#ffffff"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path></svg>
                        </div>
                        <h5 class="card-title pt-4 pb-2">Never Be Alone</h5>
                        <p class="card-text">Grow together and make your career journey a win- win value proposition for all.</p>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col-lg-4 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section><!-- end benefits-area -->
<!-- ================================
         END BENEFITS AREA
================================= -->


<!-- ================================
         START CTA AREA
================================= -->
<section class="cta-area pt-60px pb-30px">
    <div class="container">
      <div class="text-center">
          <h2 class="section-title pb-3">Process</h2>
      </div>
        <div class="row">
            <div class="col-lg-4 responsive-column-half">
                <div class="info-box px-3">
                    <div class="icon-element mb-4 shadow-sm rounded-rounded">
                        <span class="info-number">1</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="35px" viewBox="0 0 24 24" width="35px" fill="#8C43FF"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 5.9c1.16 0 2.1.94 2.1 2.1s-.94 2.1-2.1 2.1S9.9 9.16 9.9 8s.94-2.1 2.1-2.1m0 9c2.97 0 6.1 1.46 6.1 2.1v1.1H5.9V17c0-.64 3.13-2.1 6.1-2.1M12 4C9.79 4 8 5.79 8 8s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 9c-2.67 0-8 1.34-8 4v3h16v-3c0-2.66-5.33-4-8-4z"/></svg>
                    </div>
                    <div class="info-body">
                        <h3 class="fs-18 pb-3 fw-bold">Ask A Question</h3>
                        <p>Ask your most pressing career related questions from community.</p>
                    </div>
                </div><!-- end info-box -->
            </div><!-- end col-lg-4 -->
            <div class="col-lg-4 responsive-column-half">
                <div class="info-box px-3">
                    <div class="icon-element mb-4 shadow-sm rounded-rounded">
                        <span class="info-number">2</span>
                        <svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="35px" viewBox="0 0 24 24" width="35px" fill="#28d5a7"><g><rect fill="none" height="24" width="24"/></g><g><path d="M18,15v3H6v-3H4v3c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2v-3H18z M7,9l1.41,1.41L11,7.83V16h2V7.83l2.59,2.58L17,9l-5-5L7,9z"/></g></svg>
                    </div>
                    <div class="info-body">
                        <h3 class="fs-18 pb-3 fw-bold">Answer Community Questions</h3>
                        <p>Help others by answering their questions and share your expertise.</p>
                    </div>
                </div><!-- end info-box -->
            </div><!-- end col-lg-4 -->
            <div class="col-lg-4 responsive-column-half">
                <div class="info-box px-3">
                    <div class="icon-element mb-4 shadow-sm rounded-rounded">
                        <span class="info-number">3</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="35px" viewBox="0 0 24 24" width="35px" fill="#f9b851"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M14 6V4h-4v2h4zM4 8v11h16V8H4zm16-2c1.11 0 2 .89 2 2v11c0 1.11-.89 2-2 2H4c-1.11 0-2-.89-2-2l.01-11c0-1.11.88-2 1.99-2h4V4c0-1.11.89-2 2-2h4c1.11 0 2 .89 2 2v2h4z"/></svg>
                    </div>
                    <div class="info-body">
                        <h3 class="fs-18 pb-3 fw-bold">Build Success Network</h3>
                        <p>Grow your work network and build your community reputation.</p>
                    </div>
                </div><!-- end info-box -->
            </div><!-- end col-lg-4 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section><!-- end cta-area -->
<!-- ================================
         END CTA AREA
================================= -->

<section class="faq-area pt-80px pb-80px">
    <div class="container">
        <div class="row">
                    <div class="col-lg-2">
                    <?php taoh_leftmenu_widget(); ?>
					</div><!-- end col-lg-2 -->
                    <div class="col-lg-6">
							<h2 id="faq" class="fs-27 pb-3 text-primary">Frequently Asked Questions</h2><br />
                <div id="accordion" class="generic-accordion">
									<?php $counter = 0;
										foreach ( $array as $key => $value ){
											if ( $key ) {
												echo "<div><h5>&nbsp;&nbsp;&nbsp;&nbsp;<STRONG>$key</STRONG></h5></div>";
											}
											if ( $value ){
												$serial = 1;
												foreach ( $value as $key_faq => $value_value ){
													$counter++;
													if ($key_faq){
														echo "<div class=\"card\">
																    <div class=\"card-header\" id=\"heading$counter\">
																        <button class=\"btn btn-link collapsed\" data-toggle=\"collapse\" data-target=\"#collapse$counter\" aria-expanded=\"true\" aria-controls=\"collapse$counter\">
																            <span> $key_faq</span>
																            <i class=\"la la-angle-up collapse-icon\"></i>
																        </button>
																    </div>
																    <div id=\"collapse$counter\" class=\"collapse\" aria-labelledby=\"heading$counter\" data-parent=\"#accordion\">
																        <div class=\"card-body\">
																            <p class=\"fs-15 lh-24\">$value_value</p>
																        </div>
																    </div>
																</div><!-- end card -->
														";
													}
												}
											}
											echo "<BR />";
										}?>
                </div><!-- end accordion -->
            </div><!-- end col-lg-8 -->

						<div class="col-lg-4">
							<?php if ( taoh_user_is_logged_in() ){ ?>
								<div class="sidebar">
									<div class="card card-item">
											<div class="card-body">
													<h3 class="fs-17 pb-3 text-success">Current Status</h3>
													<div class="divider"><span></span></div>
													<div class="sidebar-questions pt-3">
															<div class="media media-card media--card media--card-2">
																	<div class="media-body">
																		<div class=\"nav-right-button\">
																			<?php echo $current_status;	?>
																		</div><!-- end nav-right-button -->
																	</div>
															</div><!-- end media -->
													</div><!-- end sidebar-questions -->
											</div>
									</div><!-- end card -->
									<div class="card card-item">
                                   
										<form action="<?php echo TAOH_ACTION_URL."/contact"; ?>" method="post">
											<div class="card-body">
													<div class="form-group">
															<h3 class="fs-17 pb-3 text-info">Need further help? fill the form below</h3>
															<div class="divider"><span></span></div>
															<?php
															if (isset($_GET[ 'we_status' ]) && $_GET[ 'we_status' ] == 'success' ){
																echo "

																<div class=\"alert alert-success\" role=\"alert\" id=\"success-alert\">
																  Thank you for contacting us. We will get in touch within 24-48hours.
																</div>
																";
															}
															 ?>
													</div><!-- end form-group -->
													<div class="form-group">
															<label class="fs-14 text-black fw-medium lh-20">Subject<span class="text-gray fs-13"></span></label>
															<input type="text" class="form-control form--control fs-14" placeholder="e.g. Subject Here" name="we_subject">
															<input type="hidden" name="we_locn" value="<?php echo TAOH_SITE_URL;?>">
													</div><!-- end form-group -->
													<div class="form-group">
														<label class="fs-14 text-black fw-medium mb-0">Category</label>
														<p class="fs-13 pb-3 lh-20">Please choose the appropriate category for the question.</p>
														<div class="form-group">
																<select class="form-control form--control fs-14" data-placeholder="Select a Category" name="we_category">
																	<option selected value="">Select a Category</option>
																	<option value="feedback">Feedback/Suggestion</option>
																	<option value="partner">Partnership Query</option>
																	<option value="process">Process Issue</option>
																	<option value="tech">Technical Issue</option>
																	<option value="recruit">Recruiter Query</option>
																	<option value="volun">Volunteer/Intern with Hires & TAO</option>
																	<option value="nwlb">Help with #NoWorkerLeftBehindCause</option>
																	<option value="other">Other</option>
																</select>
														</div>
													</div><!-- end form-group -->
													<div class="form-group">
															<label class="fs-14 text-black fw-medium lh-20">Message</label>
															<textarea class="form-control form--control fs-14" rows="6" placeholder="Tell us how we can help you." name="we_message"></textarea>
													</div><!-- end form-group -->
													<div class="form-group mb-0">
															<button class="btn theme-btn mt-2" type="submit">Send Message <i class="la la-arrow-right icon ml-1"></i></button>
													</div><!-- end form-group -->
											</div><!-- end card-body -->
										</form>
									</div><!-- end card -->
								</div><!-- end sidebar -->
							<?php } else { ?>
								<div class="sidebar">
									<div class="card card-item">
											<div class="card-body">
													<h3 class="fs-17 pb-3 text-success">Current Status</h3>
													<div class="divider"><span></span></div>
													<div class="sidebar-questions pt-3">
															<div class="media media-card media--card media--card-2">
																<div class=\"nav-right-button\">
                                                                <?php echo $current_status;	?>
																</div><!-- end nav-right-button -->
															</div><!-- end media -->
													</div><!-- end sidebar-questions -->
											</div>
									</div><!-- end card -->
								</div><!-- end sidebar -->
						<?php } ?>
						<?php if (function_exists('taoh_tao_widget')) { taoh_tao_widget();  } ?>
                        <?php //if ( ! taoh_user_is_logged_in() && function_exists('taoh_stats_widget')) { taoh_stats_widget();  } ?>
						<?php if (function_exists('taoh_jobs_widget')) { taoh_jobs_widget();  } ?>
						<?php if (function_exists('taoh_asks_widget')) { taoh_asks_widget();  } ?>
						<?php if (function_exists('taoh_readables_widget')) { taoh_readables_widget();  } ?>
 						<?php if (function_exists('taoh_ads_widget')) { taoh_ads_widget();  } ?>
						<?php if (function_exists('taoh_obviousbaba_widget')) { taoh_obviousbaba_widget();  } ?>

            </div><!-- end col-lg-4 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section>

<?php taoh_get_footer();  ?>