<?php

$user = taoh_user_all_info();
$ptoken = $user->ptoken;
//$taoh_url_vars = taoh_parse_url(2);
//$conttoken_expl = explode('-', $taoh_url_vars);
//$conttoken = array_pop($conttoken_expl);

$ops = 'info';
$mod = 'jobs';
$taoh_call = 'jobs.job.get';
//$cache_name = $mod. '_'. $mod.'_'.$ops.'_' . $conttoken . '_' . taoh_scope_key_encode( $conttoken, 'global' );
$cache_name = 'job_details_' . $conttoken;
$taoh_vals = array(
    'token' => taoh_get_dummy_token(1),
    'ops' => $ops,
    'mod' => $mod,
    'cache_name' => $cache_name,
    'cfcc1d'=> 1,
    //'cache' => array ( "name" => $cache_name,  "ttl" => 7200),
    'conttoken' => $conttoken,

);
//$taoh_vals[ 'cfcache' ] = $cache_name;
ksort($taoh_vals);

//echo taoh_apicall_get_debug($taoh_call, $taoh_vals);exit();
$response_get = taoh_apicall_get($taoh_call, $taoh_vals, TAOH_API_PREFIX, 1);
$response = json_decode($response_get, true);
//print_r($response);die;
if($response['success']){
    $array_data = array();
    $array_data['success'] = true;
    $job = $response['output'];
    $job_title = ucfirst(taoh_title_desc_decode($job['title']));
    $job_description_modal = $job['description'];
    $job_description = taoh_title_desc_decode($job['description']);
    $job_company = $job['meta']['company'];
    $job_company_name = $job['meta']['company']['0']['title'];
    $job_location = $job['meta']['full_location'] ?? '';
    $job_created = $job['created'];
    $job_placeType = $job['meta']['placeType'];
    $job_roltype = $job['meta']['roletype'];
    $job_payinfo = '';
    $job_deadline = '';
    $job_payterm = '';
    $btm_job_payterm = '';
    $top_payinfo = '';
    $enable_scout_apply = 0;
    $apply_btn = '';
    $scout_logo = '';
    $job_scouted = $job['meta']['enable_scout_job'] ?? '';
    $apply_link = $job['meta']['apply_link'] ?? '';
    $apply_email = $job['meta']['email'];
    $enable_apply = $job['meta']['enable_apply'];
    $owner_ptoken = $job['ptoken'];
    $share_link = TAOH_SITE_URL_ROOT.'/jobs/d/'.slugify2($job_title).'-'.$conttoken;
    $taoh_url_vars = slugify2($job_title).'-'.$conttoken;
    //print_r($job_scouted);die;
    if(!empty($job['meta']['payinfo']) && !empty($job['meta']['country_code'])){
        $country_code = $job['meta']['country_code'] - 1;
        if($job['meta']['paymentTerm'] == 'monthly'){
            $job_payterm = ' per month';
            $btm_job_payterm = ' paid on per Month basis';
        }else if($job['meta']['paymentTerm'] == 'hourly'){
            $job_payterm = ' per hour';
            $btm_job_payterm = ' paid on per Hour basis';
        }else if($job['meta']['paymentTerm'] == 'annualy'){
            $job_payterm = ' per year';
            $btm_job_payterm = ' paid on per Year basis';
        }else if($job['meta']['paymentTerm'] == 'weekly'){
            $job_payterm = ' per week';
            $btm_job_payterm = ' paid on per Week basis';
        }else if($job['meta']['paymentTerm'] == 'daily'){
            $job_payterm = ' per Daily';
            $btm_job_payterm = ' paid on per Day basis';
        }else if($job['meta']['paymentTerm'] == 'project'){
            $job_payterm = ' per project';
            $btm_job_payterm = ' paid on per Project basis';
        }
        $job_payinfo = '<div class="col-lg-6">
                            <h3 class="fs-17"><img src="'.TAOH_SITE_URL_ROOT.'/assets/images/payment-title.svg'.'" alt="" width="20" style="margin-right: 10px"> Payment Details</h3>
                            <div class="divider"><span></span></div>
                            <p><img src="'.TAOH_SITE_URL_ROOT.'/assets/images/payment-details.svg'.'" alt="" width="20" style="margin-right: 10px"> <span>'.taoh_get_currency_symbol($country_code).' '.$job['meta']['payinfo'].'</span>'.$btm_job_payterm.'</p>
                        </div>';
        $top_payinfo = '<p>'.taoh_get_currency_symbol($country_code).' '.$job['meta']['payinfo'].$job_payterm.'</p>';
    }
    if(!empty($job['meta']['taoh_jobs_application_deadline'])){
        $job_deadline = '<div class="col-lg-6">
                            <h3 class="fs-17"><img src="'.TAOH_SITE_URL_ROOT.'/assets/images/application-title.svg'.'" alt="" width="20" style="margin-right: 10px"> Application Deadline</h3>
                            <div class="divider"><span></span></div>
                            <p><img src="'.TAOH_SITE_URL_ROOT.'/assets/images/application-deadline.svg'.'" alt="" width="20" style="margin-right: 10px">'.formatDate($job['meta']['taoh_jobs_application_deadline']).' </p>
                        </div>';
    }
    if(taoh_user_is_logged_in()){
        if($ptoken != $owner_ptoken){

            if(!isset($_SESSION[TAOH_ROOT_PATH_HASH.'_eligible_scouted_jobs'])){
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
            }
            if(!isset($_SESSION[TAOH_ROOT_PATH_HASH.'_scouted_jobs']) || !isset($_SESSION[TAOH_ROOT_PATH_HASH.'_applied_jobs'])){
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
                }
                $_SESSION[TAOH_ROOT_PATH_HASH.'_scouted_jobs'] = $scout_applied;
                $_SESSION[TAOH_ROOT_PATH_HASH.'_applied_jobs'] = $jobs_applied;
            }
            //print_r($_SESSION[TAOH_ROOT_PATH_HASH.'_applied_jobs']);die;
            if(isset($user->profile_complete)   && $user->profile_complete == 0){
                $apply_btn = '<a class="btn theme-btn mb-3 profile_incomplete">Apply Now </a>';
            }else{
                if($job_scouted == 'on'){
                    if(array_key_exists($conttoken, $_SESSION[TAOH_ROOT_PATH_HASH.'_scouted_jobs'])){
                        $apply_btn = '<a data-metrics="view_application" job-url="'.$taoh_url_vars.'" data-conttoken="'.$conttoken.'" apply-id="'.$_SESSION[TAOH_ROOT_PATH_HASH.'_applied_jobs'][$conttoken].'" class="btn theme-btn mb-3 click_metrics success">
                            Applied! View Application Status </a>';
                    }else{
                    if(in_array($conttoken, $_SESSION[TAOH_ROOT_PATH_HASH.'_eligible_scouted_jobs'])){
                            $apply_btn = '<a data-metrics="apply_through_scout_link" data-conttoken="'.$conttoken.'" job-url="'.$taoh_url_vars.'" class="kalpana btn theme-btn mb-3 click_metrics "
                            style="background-color:#FF7311" >Apply through Scout(Referred)</a>';
                        }
                        else{
                        $apply_btn = '<a data-metrics="request_through_scout_link" data-conttoken="'.$conttoken.'" job-url="'.$taoh_url_vars.'" class="kalpana btn theme-btn mb-3 click_metrics "
                        style="background-color:#FF7311" >Apply through Scout</a>';
                         }

                    }
                }else{
                    if(array_key_exists($conttoken,$_SESSION[TAOH_ROOT_PATH_HASH.'_applied_jobs'])){
                        $apply_btn = '<a class="btn theme-btn mb-3 click_metrics success"> Applied! </a>';
                    }else {
                        if($apply_link != ''){
                            $apply_btn = '<a href="'.$apply_link.'" class="btn theme-btn mb-3">Apply Now </a>';
                        }else if($apply_email != '' && $enable_apply){
                            $apply_btn = '<a data-position="'.$job_title.'" data-company="'.$job_company_name.'" data-fname="'.$job['meta']['fname'].'"
                            data-toemail="'.$apply_email.'" data-conttoken="'.$conttoken.'" data-placeType="'.renderJobType($job_placeType).'" data-description="'.$job_description_modal.'"  class="btn theme-btn mb-3 open_modal">Apply Now </a>';
                        }else{
                            $apply_btn = '<a href="mailto:'.$apply_email.'" class="btn theme-btn mb-3">Apply Now </a>';
                        }
                    }
                }
             }
        }
        if($job_scouted == 'on'){
            $scout_logo = '<a data-toggle="tooltip" data-placement="top"
             title="Please note: Scout is a specialized program that gets 6x faster result, where industry leading peers help find the best peer talent for the jobs. "
             style="margin-left: 5px; vertical-align: text-bottom;"><img src="'.TAOH_SITE_URL_ROOT.'/assets/images/scout_icon.png" width="28" height="28" alt="Scout Icon"></a>';
        }
    }else{
        $apply_btn = '<a class="btn theme-btn mb-3 create_referral" data-title="'.$job_title.'" data-sharelink="'.$share_link.'">
        <i class="icon-line-awesome-wrench"></i> Signup Here to Apply <i class="icon-material-outline-arrow-right-alt"></i></a>';
    }
    $liked_check = '<span class="like_render ml-1"></span>';
    $shares_count = '<a class="fs-25 mr-1 ml-1" style="cursor:pointer; vertical-align: text-bottom">
    <!-- <img  class="share_box"
    title="Share" data-conttoken="'.$conttoken.'" data-title="'.$job_title.'"
    data-ptoken = "'.$ptoken.'" data-share = "'.$share_link.'"
     src="'.TAOH_SITE_URL_ROOT.'/assets/images/share-fill.svg" alt="Share" style="width: 18px"> -->
    <svg class="share_box drk-lgt-svg-share" title="Share" data-conttoken="'.$conttoken.'" data-title="'.$job_title.'"
    data-ptoken = "'.$ptoken.'" data-share = "'.$share_link.'" width="18" height="18" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M7.85714 4.28571C9.04018 4.28571 10 3.32589 10 2.14286C10 0.959821 9.04018 0 7.85714 0C6.67411 0 5.71429 0.959821 5.71429 2.14286C5.71429 2.23214 5.71875 2.32143 5.72991 2.40848L3.62946 3.45759C3.24554 3.08482 2.72098 2.85714 2.14286 2.85714C0.959821 2.85714 0 3.81696 0 5C0 6.18304 0.959821 7.14286 2.14286 7.14286C2.72098 7.14286 3.24554 6.91518 3.62946 6.54241L5.72991 7.59152C5.71875 7.67857 5.71429 7.76562 5.71429 7.85714C5.71429 9.04018 6.67411 10 7.85714 10C9.04018 10 10 9.04018 10 7.85714C10 6.67411 9.04018 5.71429 7.85714 5.71429C7.27902 5.71429 6.75446 5.94196 6.37054 6.31473L4.27009 5.26562C4.28125 5.17857 4.28571 5.09152 4.28571 5C4.28571 4.90848 4.28125 4.82143 4.27009 4.73438L6.37054 3.68527C6.75446 4.05804 7.27902 4.28571 7.85714 4.28571Z" />
    </svg>
     </a>';
    ?>
    <div class="light-dark-card p-3 right-detail-tab <?php echo 'from_'.$from;?> desktop-job-list">
        <div class="light-dark-card sticky">
                <?php if($from == 'detail' && isset($app_data)) { ?>
                    <div class="">
                        <div  style="float:left" class="mt-3" id="go_back">
                            <a href="<?php echo TAOH_SITE_URL_ROOT.'/'.$app_data->slug.'/'; ?>" class="back-btn"><i class="las la-arrow-left"></i> Back</a>
                        </div>
                        <div style="float:right" class="mt-3" id="apply_button"><?php echo $apply_btn;?></div>
                    </div>
                <?php } ?>
                <div class="clear">
                    <h4 class="fs-19 mt-2 mb-2" style="font-weight: 500">
                        <?php echo $job_title.' '.$liked_check.' '.$shares_count?>
                        <span class="fs-14 m-2" style="float: right;">
                                Posted <?php echo taohFullyearConvert($job_created)?>
                        </span>
                    <?php echo $scout_logo;?></h4>
                </div>
                <div id="jobsDetailLocation" class="jobs-detail-location">
                    <p class="companytags">
                        <span><?php echo newgenerateCompanyHTML($job_company,true).' | '.newgenerateLocationHTML($job_location);?></span>
                        <span style="float:right"><?php echo renderRoleType($job_roltype).' | '.renderJobType($job_placeType);?></span>
                    </p>
                    <?php echo $top_payinfo; ?>
                </div>
                <?php if($from == 'listing') { ?>
                    <div class="mt-3" id="apply_button"><?php echo $apply_btn;?></div>
                <?php } else { ?>
                    <div class="mt-2" >&nbsp;</div>
                <?php } ?>
        </div>
        <div class="skill-detail-block pt-3">
            <h3 class="fs-17"><i class="las la-shapes"></i> Skills</h3>
            <div class="divider"><span></span></div>
            <ul class=""><?php echo newgenerateSkillHTML($job['meta']['skill']);?></ul>
        </div>
        <div class="job_desc">
            <h3 class="fs-17"><i class="las la-id-card"></i> Job Description</h3>
            <div class="divider"><span></span></div>
            <div class="desc" style=""><?php echo $job_description;?></div>
        </div>
        <div class="job_pay">
            <div class="row">
                <?php echo $job_payinfo; ?>
                <?php echo $job_deadline;?>
            </div>
        </div>
        <?php if($from == 'listing') { ?>
                    <div class="mt-3" id="apply_button"><?php echo $apply_btn;?></div>
        <?php } ?>
        <input type="hidden" id="hideconttoken" value="<?php echo $conttoken;?>" />
    </div>
    <?php
}else{
    ?>
        <div class="error_data">No data found</div>
    <?php
    //    echo json_encode(array('error' => 'No data found'));die;
}
?>