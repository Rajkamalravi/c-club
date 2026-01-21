<?php
$user_data = taoh_user_all_info();
//print_r($user_data);exit();
if ( ! isset( $user_data->fname ) && defined( 'TAOH_API_TOKEN' ) && defined('TAOH_SETTINGS_URL') && TAOH_API_TOKEN ) {
    taoh_redirect(TAOH_SITE_URL_ROOT.'/createacc'); 
    taoh_exit();
}

// TAO_PAGE_DESCRIPTION
define( 'TAO_PAGE_DESCRIPTION', taoh_site_description() );
// TAO_PAGE_IMAGE
define( 'TAO_PAGE_IMAGE', TAOH_SITE_LOGO );
// TAO_PAGE_TITLE
if ( ! defined ( 'TAO_PAGE_TITLE' ) ) { define ( 'TAO_PAGE_TITLE', TAOH_SITE_NAME_SLUG." - ".TAOH_SITE_TITLE ); }
if ( ! defined ( 'TAO_PAGE_DESCRIPTION' ) ) { define ( 'TAO_PAGE_DESCRIPTION', TAOH_SITE_DESCRIPTION); }
if ( ! defined ( 'TAO_PAGE_KEYWORDS' ) ) { define ( 'TAO_PAGE_KEYWORDS', TAOH_SITE_NAME_SLUG." Career development events, ".TAOH_SITE_NAME_SLUG." Professional development events, ".TAOH_SITE_NAME_SLUG." Job training workshops, ".TAOH_SITE_NAME_SLUG." Industry conferences, ".TAOH_SITE_NAME_SLUG." Networking events, ".TAOH_SITE_NAME_SLUG." Skill-building seminars, ".TAOH_SITE_NAME_SLUG." Career advancement workshops, ".TAOH_SITE_NAME_SLUG." Personal branding events, ".TAOH_SITE_NAME_SLUG." Career coaching sessions, ".TAOH_SITE_NAME_SLUG." Job search strategies, ".TAOH_SITE_NAME_SLUG." Resume writing workshops, ".TAOH_SITE_NAME_SLUG." Interview preparation seminars, ".TAOH_SITE_NAME_SLUG." LinkedIn networking events, ".TAOH_SITE_NAME_SLUG." Mentoring programs, ".TAOH_SITE_NAME_SLUG." Professional certification courses, ".TAOH_SITE_NAME_SLUG." Job fair, ".TAOH_SITE_NAME_SLUG." Career exploration events, ".TAOH_SITE_NAME_SLUG." Industry-specific training, ".TAOH_SITE_NAME_SLUG." Professional networking opportunities" ); }
taoh_get_header();
$showall = 0;

$app_temp = @taoh_parse_url(0) ? taoh_parse_url(0):TAOH_PLUGIN_PATH_NAME;

// Get the current app
$current_app = TAOH_SITE_CURRENT_APP_SLUG;

$app_data = taoh_app_info($current_app);
//$array_json =  taoh_url_get_content( TAOH_CDN_PREFIX."/app/$current_app/faq.php" );
//$array = json_decode($array_json);
$about_url = TAOH_SITE_URL_ROOT."/about";
if ( $current_app != TAOH_PLUGIN_PATH_NAME ) $about_url = TAOH_SITE_URL_ROOT."/".$current_app."/about";

//$activities = TAOH_SITE_CONTENT_GET . '?mod=core&type=activity&token='.taoh_get_dummy_token().'&limit=10&page='.$_GET['page'];
//$activity = json_decode(file_get_contents($activities), true);
if(!isset($_GET['page'])){
    $_GET['page'] = '';
}

$taoh_call = "core.content.get";
$type = 'activity';
$taoh_vals = array(
    'mod' => 'core',
    'type' => $type,
    'token'=>taoh_get_dummy_token(1),
    'limit' => 10,
    'page' => $_GET['page'],
    // 'cfcc5h' => 1 //cfcache newly added
   
);
$cache_name = $taoh_call.'_' . $type. '_' . hash('sha256', $taoh_call . serialize($taoh_vals));
//$taoh_vals[ 'cfcache' ] = $cache_name;
$taoh_vals[ 'cache_name' ] = $cache_name;
//$taoh_vals[ 'cache' ] = array ( "name" => $cache_name );
ksort($taoh_vals);

//echo TAOH_API_PREFIX.'/'.$taoh_call."?".http_build_query($taoh_vals);taoh_exit();
//$activity = json_decode(taoh_apicall( "core.content.get", 'GET', $taoh_vals ), true);
$activity_json = taoh_apicall_get($taoh_call , $taoh_vals );
$activity = json_decode(taoh_apicall_get($taoh_call , $taoh_vals ), true);
//print_r($activity);
?>
<style>
    .yo-video .y-video{
        width: 375px;
        height: 240px;
    }
    @media only screen and (min-width: 1000px) and (max-width: 1400px){
        .yo-video .y-video{
            width: 237px;
            height: 240px;
        }
    }
    @media only screen and (min-width: 370px) and (max-width: 920px){
        .yo-video .y-video{
            width:350px;
            padding-top:15px;
            height:285px;
        }
    }
    .app-box {
    padding: 8pt;
    margin-left: 10px;
    text-align: center;
    box-shadow: 0px 5px 8px 0px rgba(105, 104, 104, 0.5);
    border-style: solid;
    border-color: skyblue;
    height: 215px;
    background-color: #fff;
}
.app-box .app-text{
    font-size: small;
    line-height: 18px;
    height: 35px;
}
</style>

<!--======================================
        START HERO AREA
======================================-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="https://fonts.cdnfonts.com/css/hobo-bt" rel="stylesheet">
<?php if(isset($_GET[ 'already' ]) && $_GET[ 'already' ]){?>
<div class="alert alert-danger alert-dismissable m-0 text-center" id="flash-msg">
<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
<h4>! Account already exists. Re-directing you to home page.</h4>
</div>
<?php } ?>

<?php
if ( ! taoh_user_is_logged_in() || $showall ){
?>
<?php
}

?>

<section class="faq-area pt-80px pb-80px">
    <div class="container">
        <div class="row">

                    <div class="col-lg-2">
                    <?php taoh_leftmenu_widget(); ?>
					</div><!-- end col-lg-2 -->
                    <div class="col-lg-7">
                    <div class="">
                        <div class="question-main-bar">
                             <div class="questions-snippet">
                                <div id="loaderArea"></div> 
                                <div id="activityArea">Loading ...</div>
                                <div id="pagination"></div>
                                <!-- <div class="media-card media--card align-items-center">
                                    <div class="pad30">
                                        <div class="usr-detail-top">
                                            <div class="votes" style="width: 70px">
                                                <img width="51px" src="./assets/images/avatar1.png" alt="">
                                            </div>
                                            <div><p class="user-name-title">Editorsdesk<span style="display: block">2 mins ago</span></p></div>
                                        </div>                                        
                                        <div class="dec-expand">
                                            <p>How to Write a Compelling Resume <br>What People are talking about resume writing <br>“How can I tailor my resume to align better with the specific job I'm applying for and ensure it resonates with potential employers?” <br>“Could you provide guidance on the most effective ways to highlight my achievements and skills on my resume without it seeming too verbose or cluttered?” <br>“What's the best way to handle gaps in employment history or other potential red flags on my resume? ”... <a href="#">See more</a></p>
                                        </div>
                                    </div>
                                    
                                    <div class="act-content-img">
                                        <img src="./assets/images/activity1.png" alt="">
                                        <h2>How to Write a Compelling Resume</h2>
                                    </div>
                                    <div class="bottom-share-link">
                                            <div class="row">
                                                <div class="col-lg-4 text-center"><a href="#"><i class="la la-thumbs-up mr-1 text-black"></i><span>3</span> Like(s)</a></div>
                                                <div class="col-lg-4 text-center"><a href="#"><i class="la la-comment mr-1 text-black"></i><span>7</span> Comment(S)</a></div>
                                                <div class="col-lg-4 text-center"><a href="#"><i class="la la-share mr-1 text-black"></i><span>5</span> Share(s)</a></div>
                                            </div>
                                    </div>  
                                </div> -->
                            </div>
                        </div>
                    </div>
                    
            </div><!-- end col-lg-7 -->

			<div class="col-lg-3">
            <?php if (function_exists('taoh_ads_widget')) { taoh_ads_widget(1);  } ?>
			<?php taoh_copynshare_widget(); ?>
                <?php //if ( taoh_user_is_logged_in() ){ ?>
                    <!-- <div class="sidebar">
                        <div class="card card-item">

                            <form action="<?php //echo TAOH_ACTION_URL."/contact"; ?>" method="post">
                                <div class="card-body">
                                        <div class="form-group">
                                                <h3 class="fs-17 pb-3 text-info">Need further help? fill the form below</h3>
                                                <div class="divider"><span></span></div>
                                                <?php
                                                //if (isset($_GET[ 'we_status' ]) && $_GET[ 'we_status' ] == 'success' ){?>
                                                   
                                                    <div class="alert alert-success" role="alert" id="success-alert">
                                                        Thank you for contacting us. We will get in touch within 24-48hours.
                                                    </div>
                                                    
                                                    <?php //} ?>
                                        </div>
                                        <div class="form-group">
                                                <label class="fs-14 text-black fw-medium lh-20">Subject<span class="text-gray fs-13"></span></label>
                                                <input type="text" class="form-control form--control fs-14" placeholder="e.g. Subject Here" name="we_subject">
                                                <input type="hidden" name="we_locn" value="<?php //echo TAOH_SITE_URL;?>">
                                        </div>
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
                                        </div>
                                        <div class="form-group">
                                                <label class="fs-14 text-black fw-medium lh-20">Message</label>
                                                <textarea class="form-control form--control fs-14" rows="6" placeholder="Tell us how we can help you." name="we_message"></textarea>
                                        </div>
                                        <div class="form-group mb-0">
                                                <button class="btn theme-btn mt-2" type="submit">Send Message <i class="la la-arrow-right icon ml-1"></i></button>
                                        </div>
                                </div>
                            </form>
                        </div>
                    </div> -->
                <?php //} ?>
            
            </div><!-- end col-lg-3 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section>
<?php

if ( ! taoh_user_is_logged_in() || $showall){
?>

<!-- ================================
         START CTA AREA
================================= -->
<section class="get-started-area pt-80px pb-50px pattern-bg">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title">Hires professionals community helps you find your success faster! <br> Here's how</h2>
        </div>
        <div class="row pt-50px">
            <div class="col-lg-4 responsive-column-half">
                <div class="card card-item hover-y text-center">
                    <div class="card-body">
                        <div class="mb-4">
                            <svg class="svg-icon-color-4" width="40" viewBox="-26 0 512 512.001" xmlns="http://www.w3.org/2000/svg"><path d="m457.085938 68.828125-21.878907-19.265625 21.878907-19.261719c3.402343-3 4.566406-7.675781 2.96875-11.917969-1.605469-4.246093-5.570313-6.988281-10.105469-6.988281h-90.539063v-3.890625c0-4.144531-3.359375-7.503906-7.503906-7.503906s-7.503906 3.359375-7.503906 7.503906v101.203125l-61.628906 33.464844c-.339844-2.710937-1.152344-5.375-2.4375-7.882813l-8.683594-16.953124c-5.960938-11.640626-20.28125-16.253907-31.914063-10.296876l-6.714843 3.4375c-5.640626 2.890626-9.816407 7.796876-11.761719 13.828126-1.945313 6.027343-1.425781 12.453124 1.464843 18.089843l8.683594 16.957031c2.027344 3.957032 5.023438 7.089844 8.546875 9.285157-4.609375 6.207031-6.796875 13.839843-9.046875 21.769531l-1.617187 5.726562c-1.128907 3.988282 1.191406 8.136719 5.179687 9.261719.679688.195313 1.367188.285157 2.042969.285157 3.273437 0 6.28125-2.15625 7.21875-5.464844l1.617187-5.714844c3.289063-11.597656 5.308594-17.808594 11.738282-21.300781l11.527344-6.261719 1.421874-.726562c.472657-.242188.914063-.515626 1.363282-.785157l82.15625-44.613281c-.519532 10.269531-6.046875 19.972656-15.148438 25.5l-53.425781 32.460938c-.0625.035156-.125.070312-.183594.109374l-16.628906 10.101563c-3.539063 2.152344-4.667969 6.769531-2.515625 10.3125 2.027344 3.339844 6.246094 4.527344 9.6875 2.851563l-12.617188 38.140624-9.222656 3.78125-25.078125 10.277344 7.226563-25.546875c1.125-3.988281-1.191406-8.136719-5.179688-9.265625-3.992187-1.132812-8.136718 1.191406-9.265625 5.179688l-11.417969 40.378906c-.148437.519531-.246093 1.089844-.269531 1.632812l-2.988281 54.890626c-.089844 2.09375.355469 6.050781-.613281 8.09375l-33.1875 69.820312h-24.117188c-14.128906 0-25.621093 11.492188-25.621093 25.617188v21.660156c-3.234376-1.480469-6.824219-2.308594-10.609376-2.308594h-61.246093c-14.125 0-25.617188 11.488281-25.617188 25.617188v26.875h-18.015625c-4.144531 0-7.503906 3.359374-7.503906 7.503906 0 4.144531 3.359375 7.503906 7.503906 7.503906h442.4375c4.144532 0 7.503906-3.359375 7.503906-7.503906 0-4.144532-3.359374-7.503906-7.503906-7.503906h-19.515625v-193.417969c0-14.125-11.492187-25.617188-25.617187-25.617188h-45.398438v-130.910156c8.320313-11.277344 11.402344-26.058594 7.539063-40.105469-.605469-2.207031-2.1875-4.019531-4.296875-4.914062-1.035156-.441406-2.144532-.636719-3.242188-.59375v-13.699219h90.535156c4.539063 0 8.503907-2.742187 10.105469-6.984375 1.601563-4.242188.4375-8.921875-2.964843-11.921875zm-221.539063 60.085937c.714844-2.214843 2.246094-4.019531 4.316406-5.078124l6.714844-3.4375c1.265625-.648438 2.617187-.957032 3.953125-.957032 3.164062 0 6.222656 1.730469 7.761719 4.734375l8.6875 16.957031c2.027343 3.960938.714843 8.761719-2.890625 11.1875l-2.445313 1.324219-5.160156 2.644531c-4.269531 2.1875-9.527344.492188-11.714844-3.78125l-8.6875-16.953124c-1.058593-2.070313-1.25-4.429688-.535156-6.640626zm-15.078125 272.925782c-2.550781-1.164063-5.324219-1.925782-8.238281-2.195313l8.238281-10.941406zm35.628906-60.464844c3.609375-4.324219 4.605469-9.375 5.410156-13.445312.203126-.96875 3.90625-21.195313 3.90625-21.195313s3.503907 2.183594 3.871094 2.339844c.253906.113281.433594.363281.460938.65625l4.082031 41.496093h-25.148437zm44.375-41.417969-6.335937 51.234375h-5.230469l-4.222656-42.929687c-.539063-5.5-3.90625-10.300781-8.839844-12.6875l-7.546875-4.785157 5.027344-27.863281s21.179687 25.160157 21.671875 25.800781c4.382812 5.699219 5.882812 7.980469 5.476562 11.230469zm-81.054687 36.316407c1.515625-4.5625 2.21875-9.207032 2.101562-13.929688l2.726563-50.0625 34.707031-14.222656-6.308594 34.945312c-.019531.09375-.039062.191406-.050781.285156l-5.21875 28.894532c-.679688 2.992187-1.273438 7.53125-3.140625 9.996094l-50.707031 67.351562h-4.179688zm-178.890625 133.839843c0-5.847656 4.757812-10.605469 10.609375-10.605469h61.246093c5.851563 0 10.613282 4.757813 10.613282 10.605469v26.878907h-82.46875zm97.472656 0v-44.964843c0-5.851563 4.761719-10.609376 10.613281-10.609376h61.246094c5.851563 0 10.609375 4.757813 10.609375 10.609376v71.84375h-82.464844v-26.878907zm277.417969-166.539062v193.417969h-82.464844v-44.992188c0-4.144531-3.359375-7.503906-7.503906-7.503906-4.148438 0-7.507813 3.359375-7.507813 7.503906v44.992188h-82.464844v-120.152344c0-5.847656 4.757813-10.605469 10.609376-10.605469h61.246093c5.851563 0 10.609375 4.757813 10.609375 10.605469v37.617187c0 4.144531 3.359375 7.503907 7.503906 7.503907 4.148438 0 7.503907-3.359376 7.503907-7.503907v-110.882812c0-5.851563 4.761719-10.609375 10.613281-10.609375h8.265625c.023437 0 .050781.003906.078125.003906.023438 0 .050781-.003906.074219-.003906h52.824219c5.851562 0 10.613281 4.761718 10.613281 10.609375zm-71.855469-25.617188c-14.125 0-25.617188 11.492188-25.617188 25.617188v49.960937c-2.6875-1.226562-5.617187-2.003906-8.699218-2.230468l6.121094-49.5c1.203124-9.648438-3.71875-16.042969-8.476563-22.230469-.503906-.65625-29.671875-35.289063-29.671875-35.289063l17.894531-54.101562 49.289063-29.945313v117.71875zm15.847656-205.234375v-46.316406h79.386719l-17.097656 15.054688c-2.328125 2.050781-3.664063 5.003906-3.664063 8.105468 0 3.101563 1.335938 6.054688 3.664063 8.105469l17.097656 15.054687h-79.386719zm0 0"/></svg>

                        </div>
                        <h5 class="card-title pt-4 pb-2">Expert communities.</h5>
                        <p class="card-text">Access the best community to get your most pressing career growth questions answered, publicly or privately.</p>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col-lg-4 -->
            <div class="col-lg-4 responsive-column-half">
                <div class="card card-item hover-y text-center">
                    <div class="card-body">
                    <div class="mb-4">
                    <svg class="svg-icon-color-2" width="42" version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve">
                            <g>
                                <g>
                                    <path d="M500.71,446.149l-92.7-92.7c2.548-3.879,2.118-9.141-1.293-12.552c-3.41-3.41-8.674-3.841-12.552-1.293l-24.737-24.737
                                        c18.489-27.447,30.128-59.238,33.495-92.395c4.611-45.412-6.37-91.445-30.92-129.622c-2.987-4.645-9.174-5.988-13.818-3.002
                                        c-4.644,2.987-5.988,9.174-3.002,13.818c45.911,71.392,35.6,166.844-24.517,226.961c-70.95,70.95-186.394,70.95-257.345,0
                                        c-70.95-70.949-70.95-186.393,0-257.344c60.181-60.181,152.594-70.842,224.73-25.931c4.686,2.917,10.853,1.485,13.772-3.203
                                        c2.919-4.687,1.484-10.853-3.203-13.772C270.552,6.677,224.901-3.693,180.068,1.174C134.551,6.117,91.62,26.705,59.18,59.143
                                        c-78.746,78.747-78.746,206.878,0,285.624c39.374,39.374,91.093,59.06,142.813,59.06c39.403,0,78.798-11.44,112.741-34.292
                                        l24.613,24.613c-3.698,3.918-3.639,10.089,0.195,13.924c1.953,1.953,4.511,2.929,7.07,2.929c2.468,0,4.93-0.917,6.854-2.733
                                        l92.563,92.563c7.45,7.45,17.244,11.169,27.065,11.169c9.911,0,19.85-3.791,27.423-11.364
                                        C515.591,485.56,515.679,461.117,500.71,446.149z M335.366,361.889l26.402-26.402l18.11,18.11l-26.402,26.402L335.366,361.889z
                                         M486.376,486.496c-7.28,7.279-19.036,7.367-26.207,0.194l-92.553-92.553l26.402-26.402l92.552,92.553
                                        C493.742,467.461,493.655,479.217,486.376,486.496z"/>
                                </g>
                            </g>
                            <g>
                                <g>
                                    <path d="M345.044,59.381l-0.217-0.217c-3.892-3.916-10.223-3.935-14.14-0.043c-3.916,3.892-3.936,10.223-0.043,14.14l0.26,0.261
                                        c1.953,1.953,4.511,2.929,7.07,2.929s5.118-0.976,7.07-2.929C348.948,69.618,348.948,63.287,345.044,59.381z"/>
                                </g>
                            </g>
                            <g>
                                <g>
                                    <path d="M201.993,47.981c-84.902,0-153.975,69.073-153.975,153.975s69.073,153.975,153.975,153.975
                                        s153.975-69.073,153.975-153.975S286.895,47.981,201.993,47.981z M201.993,335.934c-73.876,0-133.978-60.102-133.978-133.978
                                        S128.117,67.978,201.993,67.978s133.978,60.102,133.978,133.978S275.869,335.934,201.993,335.934z"/>
                                </g>
                            </g>
                            <g>
                                <g>
                                    <path d="M240.041,188.963c6.228-8.096,9.944-18.22,9.944-29.201v-13.096c0-26.463-21.529-47.992-47.992-47.992
                                        s-47.992,21.529-47.992,47.992v13.096c0,10.981,3.715,21.106,9.944,29.201c-31.8,5.997-55.937,33.966-55.937,67.484v5.166
                                        c0,5.522,4.476,9.998,9.998,9.998h167.972c5.522,0,9.998-4.476,9.998-9.998v-5.166
                                        C295.978,222.93,271.841,194.961,240.041,188.963z M173.998,146.666c0-15.436,12.559-27.995,27.995-27.995
                                        s27.995,12.559,27.995,27.995v13.096c0,15.436-12.559,27.995-27.995,27.995s-27.995-12.559-27.995-27.995V146.666z
                                         M128.243,251.614c2.434-24.589,23.236-43.86,48.455-43.86h50.591c25.219,0,46.021,19.271,48.455,43.86H128.243z"/>
                                </g>
                            </g>
                            <g>
                                <g>
                                    <path d="M442.305,11.987c-5.522,0-9.998,4.476-9.998,9.998v12.428c0,5.521,4.476,9.998,9.998,9.998s9.998-4.476,9.998-9.998
                                        V21.986C452.303,16.463,447.827,11.987,442.305,11.987z"/>
                                </g>
                            </g>
                            <g>
                                <g>
                                    <path d="M442.305,71.547c-5.522,0-9.998,4.476-9.998,9.998v12.428c0,5.522,4.476,9.998,9.998,9.998s9.998-4.476,9.998-9.998
                                        V81.546C452.303,76.024,447.827,71.547,442.305,71.547z"/>
                                </g>
                            </g>
                            <g>
                                <g>
                                    <path d="M478.299,47.981h-12.428c-5.522,0-9.998,4.476-9.998,9.998s4.476,9.998,9.998,9.998h12.428
                                        c5.522,0,9.998-4.476,9.998-9.998S483.821,47.981,478.299,47.981z"/>
                                </g>
                            </g>
                            <g>
                                <g>
                                    <path d="M418.738,47.981H406.31c-5.522,0-9.998,4.476-9.998,9.998s4.476,9.998,9.998,9.998h12.428
                                        c5.522,0,9.998-4.476,9.998-9.998S424.261,47.981,418.738,47.981z"/>
                                </g>
                            </g>
                            <g>
                                <g>
                                    <path d="M61.937,412.008c-5.522,0-9.998,4.476-9.998,9.998v12.428c0,5.522,4.476,9.998,9.998,9.998s9.998-4.476,9.998-9.998
                                        v-12.428C71.935,416.484,67.459,412.008,61.937,412.008z"/>
                                </g>
                            </g>
                            <g>
                                <g>
                                    <path d="M61.937,471.568c-5.522,0-9.998,4.476-9.998,9.998v12.428c0,5.522,4.476,9.998,9.998,9.998s9.998-4.476,9.998-9.998
                                        v-12.428C71.935,476.044,67.459,471.568,61.937,471.568z"/>
                                </g>
                            </g>
                            <g>
                                <g>
                                    <path d="M97.931,448.002H85.503c-5.522,0-9.998,4.476-9.998,9.998s4.476,9.998,9.998,9.998h12.428
                                        c5.522,0,9.998-4.476,9.998-9.998S103.453,448.002,97.931,448.002z"/>
                                </g>
                            </g>
                            <g>
                                <g>
                                    <path d="M38.371,448.002H25.943c-5.522,0-9.998,4.476-9.998,9.998s4.476,9.998,9.998,9.998h12.428
                                        c5.522,0,9.998-4.476,9.998-9.998S43.893,448.002,38.371,448.002z"/>
                                </g>
                            </g>
                        </svg>
                    </div>
                        <h5 class="card-title pt-4 pb-2">Right opportunities.</h5>
                        <p class="card-text">Find the jobs, and get the opportunity to chat with recruiters on the jobs to get your most pressing jobs concerns addressed.</p>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col-lg-4 -->
            <div class="col-lg-4 responsive-column-half">
                <div class="card card-item hover-y text-center">
                    <div class="card-body">
                        <div class="mb-4">
                            <svg class="svg-icon-color-3" width="40" version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve">
                                <g>
                                    <path d="M346,319c-5.522,0-10,4.477-10,10v69c0,27.57-22.43,50-50,50H178.032c-5.521,0-9.996,4.473-10,9.993l-0.014,19.882
                                            l-23.868-23.867c-1.545-3.547-5.081-6.008-9.171-6.008H70c-27.57,0-50-22.43-50-50V244c0-27.57,22.43-50,50-50h101
                                            c5.522,0,10-4.477,10-10s-4.478-10-10-10H70c-38.598,0-70,31.402-70,70v154c0,38.598,31.402,70,70,70h59.858l41.071,41.071
                                            c1.913,1.913,4.47,2.929,7.073,2.929c1.287,0,2.586-0.249,3.821-0.76c3.737-1.546,6.174-5.19,6.177-9.233L188.024,468H286
                                            c38.598,0,70-31.402,70-70v-69C356,323.477,351.522,319,346,319z"/>
                                </g>
                                <g>
                                    <path d="M366.655,0h-25.309C261.202,0,196,65.202,196,145.346s65.202,145.345,145.345,145.345h25.309
                                            c12.509,0,24.89-1.589,36.89-4.729l37.387,37.366c1.913,1.911,4.469,2.927,7.071,2.927c1.289,0,2.589-0.249,3.826-0.762
                                            c3.736-1.548,6.172-5.194,6.172-9.238v-57.856c15.829-12.819,28.978-29.012,38.206-47.102
                                            C506.687,190.751,512,168.562,512,145.346C512,65.202,446.798,0,366.655,0z M441.983,245.535
                                            c-2.507,1.889-3.983,4.847-3.983,7.988v38.6l-24.471-24.458c-1.904-1.902-4.458-2.927-7.07-2.927c-0.98,0-1.97,0.145-2.936,0.442
                                            c-11.903,3.658-24.307,5.512-36.868,5.512h-25.309c-69.117,0-125.346-56.23-125.346-125.346S272.23,20,341.346,20h25.309
                                            C435.771,20,492,76.23,492,145.346C492,185.077,473.77,221.595,441.983,245.535z"/>
                                </g>
                                <g>
                                    <path d="M399.033,109.421c-1.443-20.935-18.319-37.811-39.255-39.254c-11.868-0.815-23.194,3.188-31.863,11.281
                                            c-8.55,7.981-13.453,19.263-13.453,30.954c0,5.523,4.478,10,10,10c5.522,0,10-4.477,10-10c0-6.259,2.522-12.06,7.1-16.333
                                            c4.574-4.269,10.552-6.382,16.842-5.948c11.028,0.76,19.917,9.649,20.677,20.676c0.768,11.137-6.539,20.979-17.373,23.403
                                            c-8.778,1.964-14.908,9.592-14.908,18.549v24.025c0,5.523,4.478,10,10,10c5.523,0,10-4.477,9.999-10v-23.226
                                            C386.949,148.68,400.468,130.242,399.033,109.421z"/>
                                </g>
                                <g>
                                    <path d="M363.87,209.26c-1.86-1.86-4.44-2.93-7.07-2.93s-5.21,1.07-7.07,2.93c-1.86,1.86-2.93,4.44-2.93,7.07
                                            c0,2.64,1.071,5.22,2.93,7.08c1.86,1.86,4.44,2.92,7.07,2.92s5.21-1.06,7.07-2.92c1.86-1.87,2.93-4.44,2.93-7.08
                                            C366.8,213.7,365.729,211.12,363.87,209.26z"/>
                                </g>
                                <g>
                                    <path d="M275,310H64c-5.522,0-10,4.477-10,10s4.478,10,10,10h211c5.523,0,10-4.477,10-10S280.522,310,275,310z"/>
                                </g>
                                <g>
                                    <path d="M282.069,368.93C280.21,367.07,277.63,366,275,366s-5.21,1.07-7.07,2.93c-1.861,1.86-2.93,4.44-2.93,7.07
                                            s1.07,5.21,2.93,7.07c1.86,1.86,4.44,2.93,7.07,2.93s5.21-1.07,7.069-2.93c1.861-1.86,2.931-4.43,2.931-7.07
                                            C285,373.37,283.929,370.79,282.069,368.93z"/>
                                </g>
                                <g>
                                    <path d="M235.667,366H64c-5.522,0-10,4.477-10,10s4.478,10,10,10h171.667c5.523,0,10-4.477,10-10S241.189,366,235.667,366z"/>
                                </g>
                                <g>
                                    <path d="M210,254H64c-5.522,0-10,4.477-10,10s4.478,10,10,10h146c5.523,0,10-4.477,10-10S215.522,254,210,254z"/>
                                </g>
                            </svg>
                        </div>
                        <h5 class="card-title pt-4 pb-2">Growth career events</h5>
                        <p class="card-text">Career development centric events to put your career success on hyperdrive with easy access to experts, peers, and opportunities.</p>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col-lg-4 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section>
<!-- ================================
         END CTA AREA
================================= -->
<!-- ================================
         START CTA AREA
================================= -->
<section class="get-started-area pt-70px pb-70px position-relative z-index-1">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 py-4">
                <h2 class="section-title fs-30 lh-40">Build your personal / private success community and put your career growth on hyperdrive!</h2>
            </div>
            <div class="col-lg-5 text-right">
                <a href="<?php echo TAOH_LOGIN_URL."?redirect_url=".TAOH_REDIRECT_URL; ?>" class="btn theme-btn theme-btn mr-2"><i class="la la-sign-in mr-1"></i> Login / Sign Up And Grow Together!</a>
            </div>
        </div>
    </div><!-- end container -->
</section>
<!-- ================================
         END CTA AREA
================================= -->
<?php
}
?>

<script>
    
	let itemsPerPage = 10;
	let currentPage = 1;
    let totalItems = 0; //this will be rewriiten on response of jobs on line 363
    let activityArea = $('#activityArea');
	let loaderArea = $("#loaderArea");

	//Initial run
	$(document).ready(function(){
        $("#flash-msg").delay(3000).fadeOut("slow");
    	taoh_activity_init();
	})

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
					taoh_activity_init();
				}
		});
	}

function taoh_activity_init (){
    var data = {
        'taoh_action': 'activity_get',
        'offset': currentPage,
        'limit': itemsPerPage,
    };
    loader(true, loaderArea);
    jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
        render_activity_template(response, activityArea);
        loader(false, loaderArea);
    }).fail(function() {
        console.log( "Network issue!" );
        loader(false, loaderArea);

    })
}

function render_activity_template(data, slot) {
    console.log('----joblisting-------',data);
    totalItems = data.output.total;
    slot.empty();
    $.each(data.output.items, function(i, v){
        slot.append(
			`<div class="media-card media--card align-items-center">
                <div class="pad30">
                    <div class="usr-detail-top">
                        <div class="votes" style="width: 70px">
                            <img width="51px" src="${(v.user.avatar)}" alt="avatar">
                        </div>
                        <div><p class="user-name-title">${(v.activity.title)}<span style="display: block">2 mins ago</span></p></div>
                    </div>                                        
                    <div class="dec-expand">
                        <p>How to Write a Compelling Resume <br>What People are talking about resume writing <br>“How can I tailor my resume to align better with the specific job I'm applying for and ensure it resonates with potential employers?” <br>“Could you provide guidance on the most effective ways to highlight my achievements and skills on my resume without it seeming too verbose or cluttered?” <br>“What's the best way to handle gaps in employment history or other potential red flags on my resume? ”... <a href="#">See more</a></p>
                    </div>
                </div>
                
                <div class="act-content-img">
                    <img src="./assets/images/activity1.png" alt="">
                    <h2>How to Write a Compelling Resume</h2>
                </div>
                <div class="bottom-share-link">
                        <div class="row">
                            <div class="col-lg-4 text-center"><a href="#"><i class="la la-thumbs-up mr-1 text-black"></i><span>3</span> Like(s)</a></div>
                            <div class="col-lg-4 text-center"><a href="#"><i class="la la-comment mr-1 text-black"></i><span>7</span> Comment(S)</a></div>
                            <div class="col-lg-4 text-center"><a href="#"><i class="la la-share mr-1 text-black"></i><span>5</span> Share(s)</a></div>
                        </div>
                </div>   
        </div>`);
    });
    if(totalItems > 20) {
        show_pagination('#pagination')
    }
}


/* (function () {
    // check for IndexedDB support
    if (!window.indexedDB) {
        console.log(`Your browser doesn't support IndexedDB`);
        return;
    }
    // Open the database
    const request = indexedDB.open('DB_name', 1);
    
    request.onerror = function(event) {  
        console.log(" The function shows error! ");  
    };

    request.onsuccess = function(event) {  
        console.log(" The function shows success! ");  
    };

      
    // create the Names object store and indexes  
     request.onupgradeneeded = (event) => {  
        let db_variable = event.target.result;  
        // create the Names object store  
        // with auto-increment id  
        let store_variable = db_variable.createObjectStore('Names', {  
            autoIncrement: true  
        });  
        // create an index on the email property,  
        let index_data = store_variable.createIndex('email', 'email', {  
            unique: true  
        });  
    };

    function insertContact(db_variable, name) {  
        // create a new transaction for the database  
        const text_data = db_variable.transaction('Names', 'readwrite');  
        // get the Names object store_variable  
        const store_variable = text_data.objectStore('Names');  
        let db_que = store_variable.put(name);  
        // handle success of the transaction  
        db_que.onsuccess = function (event) {  
            console.log(event);  
        };  
        // handle the error of the transaction  
        db_que.onerror = function (event) {  
            console.log(event.target.errorCode);  
        }  
        // close the database when the transaction completes  
        text_data.oncomplete = function () {  
            db_variable.close();  
        };  
    }
    
    function getAllContacts(db) {
        const txn = db.transaction('Names', "readonly");
        const objectStore = txn.objectStore('Names');

        objectStore.openCursor().onsuccess = (event) => {
            let cursor = event.target.result;
            if (cursor) {
                let contact = cursor.value;
                console.log(contact);
                // continue next record
                cursor.continue();
            }
        };
        // close the database connection
        txn.oncomplete = function () {
            db.close();
        };
    }

    function deleteContact(db, id) {
        // create a new transaction
        const txn = db.transaction('Names', 'readwrite');

        // get the Contacts object store
        const store = txn.objectStore('Names');
        //
        let query = store.delete(id);

        // handle the success case
        query.onsuccess = function (event) {
            console.log(event);
        };

        // handle the error case
        query.onerror = function (event) {
            console.log(event.target.errorCode);
        }

        // close the database once the 
        // transaction completes
        txn.oncomplete = function () {
            db.close();
        };

    }

    request.onsuccess = (event) => {  
        const db_variable = event.target.result;  
        insertContact(db_variable, {
            email: 'john.doe@outlook.com',
            firstName: 'John',
            lastName: 'Doe'
        });

        insertContact(db_variable, {
            email: 'jane.doe@gmail.com',
            firstName: 'Jane',
            lastName: 'Doe'
        });
        
        // get all contacts
        getAllContacts(db_variable);
        //deleteContact(db_variable, 1);
    };
})();  */ 

</script>

<?php taoh_get_footer();  ?>
