<?php

if ( ! defined ( 'TAO_PAGE_TITLE' ) ) { define ( 'TAO_PAGE_TITLE', "Comprehensive Open Jobs List at ".TAOH_SITE_NAME_SLUG.": Explore and Apply to a Wide Range of Job Opportunities" ); }
if ( ! defined ( 'TAO_PAGE_DESCRIPTION' ) ) { define ( 'TAO_PAGE_DESCRIPTION', "Browse our comprehensive jobs list featuring a diverse range of job opportunities across industries. Find the perfect job that matches your skills and interests, chat with recruiters and easily apply through our user-friendly platform at ".TAOH_SITE_NAME_SLUG.". Start your job search today and take the next step in your career." ); }
if ( ! defined ( 'TAO_PAGE_KEYWORDS' ) ) { define ( 'TAO_PAGE_KEYWORDS', "Job openings at ".TAOH_SITE_NAME_SLUG.", Employment opportunities at ".TAOH_SITE_NAME_SLUG.", Job listings at ".TAOH_SITE_NAME_SLUG.", Job board at ".TAOH_SITE_NAME_SLUG.", Job search platform at ".TAOH_SITE_NAME_SLUG.", Job finder at ".TAOH_SITE_NAME_SLUG.", Job database at ".TAOH_SITE_NAME_SLUG.", Job search engine at ".TAOH_SITE_NAME_SLUG.", Job match at ".TAOH_SITE_NAME_SLUG.", Job applications at ".TAOH_SITE_NAME_SLUG.", Apply for jobs at ".TAOH_SITE_NAME_SLUG.", Job search website at ".TAOH_SITE_NAME_SLUG.", Find a job at ".TAOH_SITE_NAME_SLUG.", Job seekers at ".TAOH_SITE_NAME_SLUG.", Job alerts at ".TAOH_SITE_NAME_SLUG.", Explore job opportunities at ".TAOH_SITE_NAME_SLUG ); }
taoh_get_header_mobile();
$current_app = taoh_parse_url(0);
$app_data = taoh_app_info($current_app);
$taoh_user_vars = taoh_user_all_info();
$empty = 0;
//echo taoh_parse_url(0);taoh_exit();
$data = taoh_user_all_info();
$ptoken = $data->ptoken;
$share_link = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$conttoken = taoh_parse_url(2);
$conttoken_expl = explode('-', $conttoken);
$conttoken = array_pop($conttoken_expl);
if ( strlen( $conttoken ) < 10 || strlen( $conttoken ) > 20 || ! ctype_alnum( $conttoken ) ){ taoh_redirect( TAOH_SITE_URL_ROOT."/404" ); exit(); }

$ops = 'info';
$mod = 'jobs';
$taoh_call = 'jobs.job.get';
//$cache_name = $mod.'_'.$ops.'_' . $conttoken . '_' . taoh_scope_key_encode( $conttoken, 'global' );
$cache_name = $mod.'_'.$ops.'_' . $conttoken;
$taoh_vals = array(
    'token' => taoh_get_dummy_token(1),
    'ops' => $ops,
    'mod' => $mod,
    'cache_name' => $cache_name,
    'cache_time' => 7200,
    //'cache' => array ( "name" => $cache_name,  "ttl" => 7200),
    'conttoken' => $conttoken,
    //'cfcc1d'=> 1,//cfcache newly added

);
//$taoh_vals[ 'cfcache' ] = $cache_name;
ksort($taoh_vals);
//echo taoh_apicall_get_debug($taoh_call, $taoh_vals);exit();
$response = json_decode(taoh_apicall_get( $taoh_call,  $taoh_vals ), true);
//print_r($response);die;

$response = $response[ 'output' ];

$ownerptoken = $response[ 'ptoken' ];
//tao_debug($url, "Getting job details by id");
$title = $app_data->name_slug." | ".@$response[ 'title' ];
$response[ 'description' ] = urldecode($response[ 'description' ]);
$response[ 'title' ] = urldecode($response[ 'title' ]);

$meta_desc = @$response[ 'description' ];
if (strlen(@$response[ 'description' ]) >= 103){
	$meta_desc = substr(strip_tags(@$response[ 'description' ]), 0, 100)."...";
}
$description = str_replace("\r\n","<br>", @$response[ 'description' ]);
$click_apply = isset($response['meta']['apply_click'])?$response['meta']['apply_click']:'';
$apllicant_email = isset($response['meta']['email'])?$response['meta']['email']:'';
$company_name = $response['meta']['company'][0]['title'];
$position = $response['title'];//print_r($apllicant_email);
$likes = isset($response['metrics'][ 'likes' ])?$response['metrics'][ 'likes' ]:$response[ 'likes' ];
$views = isset($response['metrics'][ 'views' ])?$response['metrics'][ 'views' ]:$response[ 'views' ];
$comments = isset($response['metrics'][ 'comments' ])?$response['metrics'][ 'comments' ]:$response[ 'comments' ];
$share = isset($response['metrics'][ 'shares' ])?$response['metrics'][ 'shares' ]:$response[ 'shares' ];
$userliked = isset($response['metrics'][ 'userliked' ])?$response['metrics'][ 'userliked' ]:$response[ 'userliked' ];

$additive = '';
//kalpana added for networking
/*if(isset($conttoken)){
	setcookie("taoh_networking_".$conttoken."_title",$response[ 'title' ] , strtotime( '+1 days' ), TAOH_PLUGIN_PATH_NAME );
	$_COOKIE[ "taoh_networking_".$conttoken."_title"] = $response[ 'title' ];

	setcookie("taoh_networking_".$conttoken."_short", $meta_desc, strtotime( '+1 days' ), TAOH_PLUGIN_PATH_NAME );
	$_COOKIE[ "taoh_networking_".$conttoken."_short"] = $meta_desc;

	setcookie("taoh_networking_".$conttoken."_global", 1, strtotime( '+1 days' ), TAOH_PLUGIN_PATH_NAME );
	$_COOKIE[ "taoh_networking_".$conttoken."_global"] = 1;

	setcookie("taoh_networking_".$conttoken."_logo",'', strtotime( '+1 days' ), TAOH_PLUGIN_PATH_NAME );
	$_COOKIE[ "taoh_networking_".$conttoken."_logo"] ='' ;

	setcookie("taoh_networking_".$conttoken."_ownerptoken", $ownerptoken, strtotime( '+1 days' ), TAOH_PLUGIN_PATH_NAME );
	$_COOKIE[ "taoh_networking_".$conttoken."_ownerptoken"] = $ownerptoken;

	setcookie("taoh_networking_".$conttoken."_app", 'jobs', strtotime( '+1 days' ), TAOH_PLUGIN_PATH_NAME );
	$_COOKIE[ "taoh_networking_".$conttoken."_app"] = 'jobs';

}*/
/*
Configure for Google Search Console
*/
// TAO_PAGE_AUTHOR
// TAO_PAGE_DESCRIPTION
define( 'TAO_PAGE_DESCRIPTION', $meta_desc );
// TAO_PAGE_IMAGE
define( 'TAO_PAGE_IMAGE', @$response[ 'image' ] );
// TAO_PAGE_TITLE
define( 'TAO_PAGE_TITLE', $title );
define( 'TAO_PAGE_TYPE', ($app_data?->slug ?? '') );
// TAO_PAGE_TWITTER_SITE
// TAO_PAGE_ROBOT
define ( 'TAO_PAGE_ROBOT', 'index, follow' );
if ( isset( $response[ 'source' ] )  && mb_strtolower( $response[ 'source' ] ) != mb_strtolower( TAOH_SITE_URL ) ){
	$additive = '<link rel="canonical" href="'.$response[ 'source' ].'/'.TAOH_PLUGIN_PATH_NAME.'/'.$app_data->slug.'/d/'.slugify2($response[ 'title' ])."-".$conttoken.'"/>
	<meta name="original-source" content="'.$response[ 'source' ].'/'.TAOH_PLUGIN_PATH_NAME.'/'.$app_data->slug.'/d/'.slugify2($response[ 'title' ])."-".$conttoken.'" />';
	// TAO_PAGE_CANONICAL
	define ( 'TAO_PAGE_CANONICAL', $additive );
}
// TAO_PAGE_CATEGORY

$data = taoh_user_all_info();
if ( ! isset( $data->email ) ) {
	if ( isset( $_COOKIE[ 'tao_api_email' ] ) && $_COOKIE[ 'tao_api_email' ] ){
		$data->email = $_COOKIE[ 'tao_api_email' ];
	} else {
		if ( isset( $_COOKIE[ 'email' ] ) && $_COOKIE[ 'email' ] ){
		$data->email = $_COOKIE[ 'email' ];
		}
	}
}
$location = str_replace(",", "-", $data->full_location);
$user_ptoken = $data->ptoken;
//print_r($conttoken);

$click_view = (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) ? 'click' : 'view';
$log_nolog_token = ( taoh_user_is_logged_in()) ? $user_ptoken : TAOH_API_TOKEN_DUMMY;

//Start For Click and view count
$values = json_encode(array($conttoken,TAO_PAGE_TYPE,$log_nolog_token,$click_view,time(),TAOH_API_SECRET));
//print_r($values);die;
taoh_cacheops( 'metricspush', $values );
//End For Click and view count


if(isset($_GET['comments']) && $_GET['comments']){
	$get_comm = true;
}else{
	$get_comm = false;
}

if(isset($_GET['apply_by_form']) && $_GET['apply_by_form']){
	$get_apply = true;
}else{
	$get_apply = false;
}
//taoh_get_header( $additive );
?>

<div class="mobile-app">

<header class="sticky-top bg-white border-bottom border-bottom-gray">
    <section class="hero-area pt-20px pb-20px bg-white shadow-sm overflow-hidden">
        <div class="container">
            <div class="mobileapp-search">
              <?php
            if(taoh_user_is_logged_in()) {
                include('search.php');
              }
            ?>
            </div>
        </div>
    </section>
</header>
<section>
    <div class="container">
        <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Job feed</a>
        </li>

        </ul>
        <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
        <div class="">
            <div class="row d-flex">
                <div class="col-lg-6">
                    <p class="p-3">Jobs based on your activity on Indeed</p>
                    <div id='loaderArea'></div>
                    <div id="eventArea">Loading ...</div>
                </div>
                <div class="col-lg-6">
                    <div class="right-detail-tab">
                      <div style="display: flex; white-space: nowrap;">
                        <h4>Testing</h4>
                      </div>
                      <div id="jobsDetailLocation" class="jobd-detail-location">
                        <p class="companytags"><a target="_BLANK" href="http://localhost/hires-i/asks/chat/id/company/8675/tao-ai/tao-ai" class="badge text-primary fs-14"><span title="Join the role chat for TAO.ai">TAO.ai</span></a></p>
                        <p class="companytags"><a target="_BLANK" class="badge text-muted fs-14">Pondicherry Courts, Pondicherry, IN</a></p>
                        <p class="skilltags"><a target="_BLANK" href="http://localhost/hires-i/asks/chat/id/skill/12877/safe/safe" class="badge text-dark fs-14"><span title="Join the role chat for SAFe">SAFe</span></a></p>

                      </div>
                      <div class="row">
                          <div class="col">
                            <a onclick="job_apply();" class="btn theme-btn mb-3"><i class="icon-line-awesome-wechat"></i> Apply Now <i class="icon-material-outline-arrow-right-alt"></i></a>
                          </div>
                      </div>
                      <div class="job-details-panel mt-30px mb-30px job_apply_form" style="display:none;" id="job-apply">
                            <form id="fileUploadForm" method="POST" enctype="multipart/form-data" class="career-form MultiFile-intercepted">
                              <div class="hidden">
                                <input type="hidden" name="ops" value="apply"/>
                                <input type="hidden" name="slug" value="<?php echo $conttoken; ?>"/>
                                <input type="hidden" name="to_email" value="<?php echo $apllicant_email; ?>"/>
                                <input type="hidden" name="opscode" value="<?php echo TAOH_OPS_CODE; ?>"/>
                                <input type="hidden" name="ptoken" value="<?php echo $user_ptoken; ?>"/>
                                <input type="hidden" name="company_name" value="<?php echo $company_name; ?>"/>
                                <input type="hidden" name="position_title" value="<?php echo $position; ?>"/>
                              </div>
                              <div class="mb-40px">
                                <h5 class="fs-14 text-uppercase mb-3 text-gray">1. Personal Details</h5>
                                <div class="form-group">
                                  <label class="fs-14 text-black fw-medium">First Name <span style="color:red;">*</span></label>
                                  <?php echo field_fname($data->fname); ?>
                                </div><!-- end form-group -->
                                <div class="form-group">
                                  <label class="fs-14 text-black fw-medium">Last Name <span style="color:red;">*</span></label>
                                  <?php echo field_lname($data->lname); ?>
                                </div><!-- end form-group -->
                                <div class="form-group">
                                  <label class="fs-14 text-black fw-medium">Email <span style="color:red;">*</span></label>
                                  <?php echo field_email($data->email); ?>
                                </div><!-- end form-group -->
                                <div class="form-group">
                                  <label class="fs-14 text-black fw-medium">Place of residence <span style="color:red;">*</span></label>
                                  <?php echo field_location($data->coordinates,$data->full_location, $data->geohash); ?>
                                </div><!-- end form-group -->
                                <div class="form-group">
                                  <label class="fs-14 text-black fw-medium">Current Company</label>
                                  <?php echo field_company( ( isset( $data->company ) && $data->company ) ? $data->company: '' ); ?>
                                </div><!-- end form-group -->
                                <div class="form-group">
                                  <label class="fs-14 text-black fw-medium">Resume <span style="color:red;">*</span></label>
                                  <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" id="fileToUpload" name="fileToUpload">
                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                    <p id="error1" style="display:none; color:#FF0000;">
                                      Invalid Image Format! Image Format Must Be JPG, JPEG, PNG or GIF.
                                    </p>
                                    <p id="error2" style="display:none; color:#FF0000;">
                                      Maximum File Size Limit is 5MB.
                                    </p>
                                  </div>
                                </div><!-- end form-group -->
                                <div class="form-group">
                                  <label class="fs-14 text-black fw-medium">Cover Letter</label>
                                  <textarea class="summernote" id="cover_letter_id" name="cover_letter" rows="10" cols="80" required></textarea>
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
                                <button class="btn theme-btn mt-2 submit" type="submit">Submit Application</button>
                              </div>
                            </form>
                            <div id="responseMessage" style="display: none;"></div>
                          </div>
                        <br /><br />
                        <hr class="border-top-gray">
                        <div class="job-details-panel-main-bar">
                        <div class="job-details-panel mb-30px">
							             <h3 class="fs-20 pb-3 fw-bold">Description</h3>
                        </div>
                        <div class="">
                        <div class="fs-15">
								            <p class="ipl-richtexteditor-block" data-block="true" data-editor="4k1g4" data-offset-key="dhlnp-0-0"></p>
                            <p data-offset-key="dhlnp-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                              <span data-offset-key="dhlnp-0-0">Most companies say </span>
                              <span data-offset-key="dhlnp-0-1" style="font-weight: bold;">employees are Number One</span>
                              <span data-offset-key="dhlnp-0-2">. We really mean it. Let’s start with what’s in it for YOU. </span>
                              <span data-offset-key="dhlnp-0-3" style="font-weight: bold;">Why choose RDI</span>
                              <span data-offset-key="dhlnp-0-4">?</span>
                            </p>
                            <p></p>
                            <ul class="public-DraftStyleDefault-ul" data-offset-key="4ukfv-0-0">
                              <li class="ipl-richtexteditor-listitem public-DraftStyleDefault-unorderedListItem public-DraftStyleDefault-reset public-DraftStyleDefault-depth0 public-DraftStyleDefault-listLTR" data-block="true" data-editor="4k1g4" data-offset-key="4ukfv-0-0">
                                <p data-offset-key="4ukfv-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                                  <span data-offset-key="4ukfv-0-0" style="font-weight: bold;">We</span>
                                  <span data-offset-key="4ukfv-0-2" style="font-weight: bold;">ENCOURAGE your health and wellness</span>
                                  <span data-offset-key="4ukfv-0-3">, both physical and mental! Stop by our onsite Fitness Center for a FREE Personal Training Session, take advantage of our corporate gym membership discounts, and participate in our hundreds of onsite and virtual RDI company events each year!</span>
                                </p>
                              </li>
                              <li class="ipl-richtexteditor-listitem public-DraftStyleDefault-unorderedListItem public-DraftStyleDefault-depth0 public-DraftStyleDefault-listLTR" data-block="true" data-editor="4k1g4" data-offset-key="d1l9m-0-0">
                                <p data-offset-key="d1l9m-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                                  <span data-offset-key="d1l9m-0-0" style="font-weight: bold;">We CELEBRATE you</span>
                                  <span data-offset-key="d1l9m-0-1">. Join any of our Affinity Groups including Young Professionals Network, Woman Nation, Black Professionals Network, PRIDE, Hispanics Unidos, and more! </span>
                                  <span data-offset-key="d1l9m-0-2" style="font-weight: bold;">EMBRACE DIVERSITY.</span>
                                </p>
                              </li>
                              <li class="ipl-richtexteditor-listitem public-DraftStyleDefault-unorderedListItem public-DraftStyleDefault-depth0 public-DraftStyleDefault-listLTR" data-block="true" data-editor="4k1g4" data-offset-key="1jgm7-0-0">
                                <p data-offset-key="1jgm7-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                                  <span data-offset-key="1jgm7-0-0">We want you to </span>
                                  <span data-offset-key="1jgm7-0-1" style="font-weight: bold;">become our next LEADER</span>
                                  <span data-offset-key="1jgm7-0-2">. Take advantage of RDI- University Online Courses, tuition discounts, Supervisor Boot Camp Training, and a hands on approach to developing YOU to be the best you possible!</span>
                                </p>
                              </li>
                              <li class="ipl-richtexteditor-listitem public-DraftStyleDefault-unorderedListItem public-DraftStyleDefault-depth0 public-DraftStyleDefault-listLTR" data-block="true" data-editor="4k1g4" data-offset-key="e7mbk-0-0">
                                <p data-offset-key="e7mbk-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                                  <span data-offset-key="e7mbk-0-0">And of course </span>
                                  <span data-offset-key="e7mbk-0-1" style="font-weight: bold;">we offer all the necessities</span>
                                  <span data-offset-key="e7mbk-0-2">: Competitive pay, Medical, Dental, and Vision Insurance; 401k with company match; Company Paid Holidays and PTO, and SO much more!</span>
                                </p>
                              </li>
                              <li class="ipl-richtexteditor-listitem public-DraftStyleDefault-unorderedListItem public-DraftStyleDefault-depth0 public-DraftStyleDefault-listLTR" data-block="true" data-editor="4k1g4" data-offset-key="23uqr-0-0">
                                <p data-offset-key="23uqr-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                                  <span data-offset-key="23uqr-0-0">We care about our </span>
                                  <span data-offset-key="23uqr-0-1" style="font-weight: bold;">communities and we have integrity</span>
                                  <span data-offset-key="23uqr-0-2">. We support 20  community charities through volunteer efforts and donations. We’ve also been voted as Best Places to Work, Business of the Year, just to name a few.</span>
                                </p>
                              </li>
                              <li class="ipl-richtexteditor-listitem public-DraftStyleDefault-unorderedListItem public-DraftStyleDefault-depth0 public-DraftStyleDefault-listLTR" data-block="true" data-editor="4k1g4" data-offset-key="294vf-0-0">
                                <p data-offset-key="294vf-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                                  <span data-offset-key="294vf-0-0">Here at RDI, you’ll </span>
                                  <span data-offset-key="294vf-0-1" style="font-weight: bold;">Earn Well. Learn Well. Live Well. Dream Well</span>
                                  <span data-offset-key="294vf-0-2">. And </span>
                                  <span data-offset-key="294vf-0-3" style="font-weight: bold;">#leaveitbetter</span>
                                </p>
                              </li>
                            </ul>
                            <p class="ipl-richtexteditor-block" data-block="true" data-editor="4k1g4" data-offset-key="1efee-0-0"></p>
                            <p data-offset-key="1efee-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                              <span data-offset-key="1efee-0-0" style="font-weight: bold;">Who are we and what do we do here at RDI? A lot.</span>
                            </p>
                            <p></p>
                            <ul class="public-DraftStyleDefault-ul" data-offset-key="4bemm-0-0">
                              <li class="ipl-richtexteditor-listitem public-DraftStyleDefault-unorderedListItem public-DraftStyleDefault-reset public-DraftStyleDefault-depth0 public-DraftStyleDefault-listLTR" data-block="true" data-editor="4k1g4" data-offset-key="4bemm-0-0">
                                <p data-offset-key="4bemm-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                                  <span data-offset-key="4bemm-0-0">Founded in 1978 and headquartered in Cincinnati, OH, we’re Family-Owned with more than 3,500 employees across the nation.</span>
                                </p>
                              </li>
                              <li class="ipl-richtexteditor-listitem public-DraftStyleDefault-unorderedListItem public-DraftStyleDefault-depth0 public-DraftStyleDefault-listLTR" data-block="true" data-editor="4k1g4" data-offset-key="abtsr-0-0">
                                <p data-offset-key="abtsr-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                                  <span data-offset-key="abtsr-0-0">Mainly, we provide Call Center services on behalf of industries ranging from Insurance, Medical Devices, Retail, Financial Services and Banking, Outdoor/Recreational, Cable/Radio, and many more!</span>
                                </p>
                              </li>
                              <li class="ipl-richtexteditor-listitem public-DraftStyleDefault-unorderedListItem public-DraftStyleDefault-depth0 public-DraftStyleDefault-listLTR" data-block="true" data-editor="4k1g4" data-offset-key="2p629-0-0">
                                <p data-offset-key="2p629-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                                  <span data-offset-key="2p629-0-0">In addition to our Contact Center Solutions, we also offer Market Research, IT Support, Marketing and Digital Consultation (and you guessed it, WAY MORE!) to Fortune 500 Companies!</span>
                                </p>
                              </li>
                            </ul>
                            <p class="ipl-richtexteditor-block" data-block="true" data-editor="4k1g4" data-offset-key="73tdj-0-0"></p>
                            <p data-offset-key="73tdj-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                              <span data-offset-key="73tdj-0-0" style="font-weight: bold;">Here’s what you’ll do as a Call Center Customer Service Representative at RDI Corporation:</span>
                            </p>
                            <p></p>
                            <ul class="public-DraftStyleDefault-ul" data-offset-key="9ahi3-0-0">
                              <li class="ipl-richtexteditor-listitem public-DraftStyleDefault-unorderedListItem public-DraftStyleDefault-reset public-DraftStyleDefault-depth0 public-DraftStyleDefault-listLTR" data-block="true" data-editor="4k1g4" data-offset-key="9ahi3-0-0">
                                <p data-offset-key="9ahi3-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                                  <span data-offset-key="9ahi3-0-0">Act as the go to person for our customers by providing information regarding customer’s products and services, answering questions, and resolving any customer inquiries or concerns via phone, email, and/or chat</span>
                                </p>
                              </li>
                              <li class="ipl-richtexteditor-listitem public-DraftStyleDefault-unorderedListItem public-DraftStyleDefault-depth0 public-DraftStyleDefault-listLTR" data-block="true" data-editor="4k1g4" data-offset-key="5iljv-0-0">
                                <p data-offset-key="5iljv-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                                  <span data-offset-key="5iljv-0-0">Receive inbound calls or make outbound calls; identify and assess customers’ needs to exceed customer satisfaction at first point of contact</span>
                                </p>
                              </li>
                              <li class="ipl-richtexteditor-listitem public-DraftStyleDefault-unorderedListItem public-DraftStyleDefault-depth0 public-DraftStyleDefault-listLTR" data-block="true" data-editor="4k1g4" data-offset-key="efqs2-0-0">
                                <p data-offset-key="efqs2-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                                  <span data-offset-key="efqs2-0-0">Meet personal/customer service targets, KPIs, and call handling quotas based off program specifics</span>
                                </p>
                              </li>
                              <li class="ipl-richtexteditor-listitem public-DraftStyleDefault-unorderedListItem public-DraftStyleDefault-depth0 public-DraftStyleDefault-listLTR" data-block="true" data-editor="4k1g4" data-offset-key="3tb95-0-0">
                                <p data-offset-key="3tb95-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                                  <span data-offset-key="3tb95-0-0">Utilize computer and business software programs such as email, internet, chat tools, and CRMs to track customer interactions and notes</span>
                                </p>
                              </li>
                            </ul>
                            <p class="ipl-richtexteditor-block" data-block="true" data-editor="4k1g4" data-offset-key="e6moe-0-0"></p>
                            <p data-offset-key="e6moe-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                              <span data-offset-key="e6moe-0-0" style="font-weight: bold;">What you already have to be an awesome Call Center Customer Service Representative:</span>
                            </p>
                            <p></p>
                            <ul class="public-DraftStyleDefault-ul" data-offset-key="7t6sb-0-0">
                              <li class="ipl-richtexteditor-listitem public-DraftStyleDefault-unorderedListItem public-DraftStyleDefault-reset public-DraftStyleDefault-depth0 public-DraftStyleDefault-listLTR" data-block="true" data-editor="4k1g4" data-offset-key="7t6sb-0-0">
                                <p data-offset-key="7t6sb-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                                  <span data-offset-key="7t6sb-0-0" style="font-weight: bold;">A passion for talking on the phone. </span>
                                  <span data-offset-key="7t6sb-0-1">It's the core of our industry.</span>
                                </p>
                              </li>
                              <li class="ipl-richtexteditor-listitem public-DraftStyleDefault-unorderedListItem public-DraftStyleDefault-depth0 public-DraftStyleDefault-listLTR" data-block="true" data-editor="4k1g4" data-offset-key="d8le2-0-0">
                                <p data-offset-key="d8le2-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                                  <span data-offset-key="d8le2-0-0" style="font-weight: bold;">Customer Service or Call Center experience</span>
                                  <span data-offset-key="d8le2-0-1"> and ability to ensure customer satisfaction in each interaction.</span>
                                </p>
                              </li>
                              <li class="ipl-richtexteditor-listitem public-DraftStyleDefault-unorderedListItem public-DraftStyleDefault-depth0 public-DraftStyleDefault-listLTR" data-block="true" data-editor="4k1g4" data-offset-key="elg22-0-0">
                                <p data-offset-key="elg22-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                                  <span data-offset-key="elg22-0-0" style="font-weight: bold;">Smile and Dial: </span>
                                  <span data-offset-key="elg22-0-1">Positive attitude, professional communication skills and a desire to go above and beyond.</span>
                                </p>
                              </li>
                              <li class="ipl-richtexteditor-listitem public-DraftStyleDefault-unorderedListItem public-DraftStyleDefault-depth0 public-DraftStyleDefault-listLTR" data-block="true" data-editor="4k1g4" data-offset-key="65rko-0-0">
                                <p data-offset-key="65rko-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                                  <span data-offset-key="65rko-0-0">You'll be helping our customers via phone, email, and chat so you should be pretty good at </span>
                                  <span data-offset-key="65rko-0-1" style="font-weight: bold;">multitasking.</span>
                                </p>
                              </li>
                              <li class="ipl-richtexteditor-listitem public-DraftStyleDefault-unorderedListItem public-DraftStyleDefault-depth0 public-DraftStyleDefault-listLTR" data-block="true" data-editor="4k1g4" data-offset-key="1bisp-0-0">
                                <p data-offset-key="1bisp-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                                  <span data-offset-key="1bisp-0-0">Must attend all scheduled training, </span>
                                  <span data-offset-key="1bisp-0-1" style="font-weight: bold;">100%</span>
                                  <span data-offset-key="1bisp-0-3" style="font-weight: bold;">attendance</span>
                                  <span data-offset-key="1bisp-0-4"> is a must.</span>
                                </p>
                              </li>
                              <li class="ipl-richtexteditor-listitem public-DraftStyleDefault-unorderedListItem public-DraftStyleDefault-depth0 public-DraftStyleDefault-listLTR" data-block="true" data-editor="4k1g4" data-offset-key="1ue2c-0-0">
                                <p data-offset-key="1ue2c-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                                  <span data-offset-key="1ue2c-0-0">Strong people skills — you are friendly, </span>
                                  <span data-offset-key="1ue2c-0-1" style="font-weight: bold;">empathetic</span>
                                    <span data-offset-key="1ue2c-0-2">, and a good listener.</span></p></li><li class="ipl-richtexteditor-listitem public-DraftStyleDefault-unorderedListItem public-DraftStyleDefault-depth0 public-DraftStyleDefault-listLTR" data-block="true" data-editor="4k1g4" data-offset-key="d7kmd-0-0"><p data-offset-key="d7kmd-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr"><span data-offset-key="d7kmd-0-0" style="font-weight: bold;">Tech-savvy. </span><span data-offset-key="d7kmd-0-1">You’ll need know how to comfortably and simultaneously toggle between multiple computer systems including Microsoft Office Suite and client CRM systems.</span></p></li></ul><p class="ipl-richtexteditor-block" data-block="true" data-editor="4k1g4" data-offset-key="gsat-0-0"></p>
                              <p data-offset-key="gsat-0-0" class="public-DraftStyleDefault-block public-DraftStyleDefault-ltr">
                                    <span data-offset-key="gsat-0-0" style="font-weight: bold;">***This position is based out of Connersville, IN and is a ONSITE role.</span></p><p></p><p></p><br><br>												</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
</section>

    </div>
    <script>

let isLoggedIn = "<?php echo taoh_user_is_logged_in(); ?>";
let loaderArea = $('#loaderArea');
  let searchQuery = $('#searchQuery');
let eventArea = $('#eventArea');
let jobsDetailLocation = $('#jobsDetailLocation');
let locationSelectInput = $('#locationSelect');
let geohashInput = $('#geohash');
  let currentMod = '<?php echo ($app_data?->slug ?? ''); ?>';
//let geohash = "";
let geohash = "";
let search = "";
  let locationClear = $('#locationClear');
  let searchClear = $('#searchClear');

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

loader(true, loaderArea);
//Initial run
$(document).ready(function(){
  if(isLoggedIn == true) {
    taoh_taoh_room_get_member_active_chat_init();
  }
  $('.ts-control').css('height', '37px');
    taoh_jobs_init();
})

function searchFilter() {
  var queryString = $('#searchFilter').serialize();
  console.log(queryString);
  geohash = geohashInput.val();
  //eventArea.empty();
  $('#pagination').empty();
  console.log('--searchFilter----------');
  taoh_jobs_init(queryString);
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
        taoh_jobs_init();
      }
  });
}

function taoh_taoh_room_get_member_active_chat_init() {
  if(isLoggedIn == true) {
    loader(true, activeListloaderArea);
    var data = {
       'taoh_action': 'get_member_active_chat',
       'mod': "jobs",
       'app': "jobs",
       'ops': "memberrooms",
       'token': "<?php echo defined( 'TAOH_API_TOKEN' ) ? TAOH_API_TOKEN: '';?>",
       'url': "<?php echo taoh_site_ajax_url(); ?>",
     };
    jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
      data = response;
      render_active_chat_list_template(data.output, activeChatList);
      listUpdatedAt = data.ctime;
      loader(false, activeListloaderArea);
    }).fail(function() {
        loader(false, activeListloaderArea);
        console.log( "Network issue!" );
        //comments.append("<p>Server Error!</p>");
    })
  }
}
function clearBtn(type) {
  if(type == "search") {
    search = "";
    $('#query').val("");
  }
  if(type == "geohash") {
    geohash = "";
    $('.ts-control div.item').html('');
  }
//eventArea.empty();
$('#pagination').empty();
  taoh_jobs_init();
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
  loader(true, loaderArea);
console.log('----search------',$('#query').val());
console.log('----geohash------',$('#geohash').val());
  var data = {
     'taoh_action': 'jobs_get',
     'ops': 'list',
     'search': search,
     'geohash': geohash,
     'offset': currentPage - 1,
     'limit': itemsPerPage,
   'filters': queryString
   };
  jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
    res = response;

    render_jobs_template(res, eventArea);
    loader(false, loaderArea);
  }).fail(function() {
      loader(false, loaderArea);
      console.log( "Network issue!" );

  })
}

function render_jobs_template(data, slot) {
    console.log("render", data.output);

  //console.log("render", data)
  slot.empty();
  //console.log("output", "location trigered");
  if(data.output === false) {
    slot.append("<p>No data found!</p>");
    return false;
  }
  if(data.success  === false) {
    slot.append("<p>No data found!</p>");
    return false;
  }

  totalItems = data.output.total
  jobCount.append(totalItems + ' jobs Found');

  var result = format_object(data);
console.log('----joblisting-------',result.items)
  $.each(result.items, function(i, v){

    let is_local = localStorage.getItem(app_slug+'_'+v.conttoken+'_liked');
    if(v.metrics){
              if ((v.metrics.userliked) || (is_local)) {
                  var liked_check = `<a style="font-size:20px;" class=""><i title="Likes" class="la la-heart text-danger"></i> <span id="likeCount" class="badge text-dark fs-14 p-0">&nbsp; ${(v.metrics.likes != 0 && v.metrics.likes > like_min) ? v.metrics.likes : ''}</span></a>`
              } else {
                  var liked_check = `<a style="font-size:20px;" class=""><i style="cursor:pointer;" title="Like" data-cont="${(v.conttoken)}" data-likes="${(v.metrics.likes)}" class="la la-heart text-gray jobs_like"></i> <span data-conts="${(v.conttoken)}" id="likeCount" class="badge text-dark fs-14 p-0">&nbsp; ${(v.metrics.likes != 0 && v.metrics.likes > like_min) ? v.metrics.likes : ''}</span></a>`
              }
              var shares_count = (v.metrics.shares !=0 && v.metrics.shares > share_min) ? v.metrics.shares : '';
          }else{
              if ((v.userliked) || (is_local)) {
                  var liked_check = `<a style="font-size:20px;" class=""><i title="Likes" class="la la-heart text-danger"></i> <span id="likeCount" class="badge text-dark fs-14 p-0">&nbsp; ${(v.likes != 0 && v.likes > like_min) ? v.likes : ''}</span></a>`
              } else {
                  var liked_check = `<a style="font-size:20px;" class=""><i style="cursor:pointer;" title="Like" data-cont="${(v.conttoken)}" data-likes="${(v.likes)}" class="la la-heart text-gray jobs_like"></i> <span data-conts="${(v.conttoken)}" id="likeCount" class="badge text-dark fs-14 p-0">&nbsp; ${(v.likes != 0 && v.likes > like_min) ? v.likes : ''}</span></a>`
              }
              var shares_count = (v.shares !=0 && v.shares > share_min) ? v.shares : '';
          }

    var apply_email_link = '';
    if(v.apply_link){
      apply_email_link = `<a href="${v.apply_link}" target="_blank"  class="tag-link text-primary">APPLY</a>`
    }else if((v.email) && (v.enable_apply)){
      apply_email_link = `<a href="<?php echo TAOH_SITE_URL_ROOT."/".$app_data->slug."/d/"; ?>${convertToSlug(v.title)}-${v.conttoken+'?apply_by_form=true'}" target="_blank"  class="tag-link text-primary">APPLY</a>`
    }else{
      apply_email_link = `<a href="${'mailto:'+v.email}" target="_blank"  class="tag-link text-primary">APPLY</a>`
    }


    slot.append(
    `<div class="col-lg-12  media media-card media--card align-items-center">
      <div class="media-body border-left-0">
        <h5 class="pb-1">
          <a target='_blank' href="<?php echo TAOH_SITE_URL_ROOT."/".$app_data->slug."/d/"; ?>${convertToSlug(v.title)}-${v.conttoken}">${v.title}</a>
          &nbsp;&nbsp;
          <?php

          if ( taoh_user_is_logged_in()) { ?>
            ${apply_email_link}
            <div class="tags float-right">
              ${liked_check}
              <a class=""><i title="Share" style="cursor:pointer;font-size:20px;" data-conttoken="${(v.conttoken)}" data-title="${(v.title)}" data-ptoken = "<?php echo $ptoken; ?>" data-share = "<?php echo $share_link; ?>" class="la la-share share_box"></i> <span id="shareCount" class="badge text-dark fs-14 p-0">&nbsp;&nbsp; ${shares_count}</span></a>
            </div>
          <?php }

          ?>
        </h5>
        ${v.rolechat ? `<p class="rolechattags"><span class="badge text-success fs-14 cursor-pointer role_directory" data-roleid="${v.rolechat.id}" data-roleslug="${v.rolechat.slug}">${v.rolechat.name}</span></p>`: ''}

        ${v.company ? `<p class="companytags"><span class="company_directory cursor-pointer underline-on-hover" data-companyid="${v.company.id}" data-companyslug="${v.company.slug}">${v.company.name}</span></p>`: ''}

        ${v.full_location ? `<p class="companytags"><a target=_BLANK class=\"badge text-muted fs-14\">${v.full_location}</a></p>`: ''}
        ${v.skill ? `<p class="skilltags"><span class="badge text-dark fs-14 cursor-pointer skill_directory" data-skillid="${v.skill.id}" data-skillslug="${v.skill.slug}">${v.skill.name}</span></p>`: ''}
      </div>
    </div>`);
  });
  if(data.output.total >= 11) {
    show_pagination('#pagination')
  }
}

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
  var link_share = "https://www.linkedin.com/shareArticle?mini=true&url="+share_link+"&title="+title+"&summary="+desc;
  var email_share = "mailto:?subject=I wanted you to see this site&amp;body=Check out this site "+title+share_link;

  $("#share_icon").html(`
          <div class="social-icon-box d-flex text-center" data-ajax="${(dat_ajax)}" data-conttype="<?php echo $app_data->slug; ?>">
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
  var likes = $(this).attr("data-likes");
  var data = {
     'taoh_action': 'job_like_put',
     'conttoken': conttoken,
     'ptoken': '<?php echo $ptoken; ?>',
  };
  jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
    if(response.success){
      var count_like = parseInt(likes) + parseInt(1);
      $(".tags a").find(`[data-conts='${conttoken}']`).html(count_like > like_min ? (count_like):'&nbsp;');
      $(".tags a").find(`[data-cont='${conttoken}']`).removeClass('text-gray').addClass('text-danger');
      $(".tags a").find(`[data-cont='${conttoken}']`).removeAttr("onclick");
      $(".tags a").find(`[data-cont='${conttoken}']`).removeAttr("style");
      localStorage.setItem(app_slug+'_'+conttoken+'_liked',1);
    }else{
      console.log( "Like Failed!" );
    }
  }).fail(function() {
    console.log( "Network issue!" );
  })
});


locationSelectInput.on("change", function() {
var latlong = $('.item').attr("data-value");
  var split = latlong.split("::");
  loader(true, geoloaderArea);
  var data = {
    'taoh_action': 'taoh_get_geohash',
    'lon': split[1],
    'lat': split[0],
  };
  jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
    console.log(response.geohash);
    geohashInput.val(response.geohash);
    loader(false, geoloaderArea);
  }).fail(function() {
    console.log( "Network issue!" );
    loader(false, geoloaderArea);
  })

})

</script>
<?php
  taoh_get_footer();
?>
