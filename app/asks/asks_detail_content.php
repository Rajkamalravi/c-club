<?php
$user = taoh_user_all_info();
$ptoken = $user->ptoken;
$taoh_url_vars = taoh_parse_url(2);
$share_link = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$click_view = (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) ? 'click' : 'view';

if(!isset($conttoken))
    $conttoken = '';

$ops = 'info';
$mod = 'asks';
$taoh_call = 'asks.ask.get';
//$cache_name = $mod.'_'.$ops.'_' . $conttoken . '_' . taoh_scope_key_encode( $conttoken, 'global' );
$cache_name = $mod.'_'.$ops.'_' . $conttoken;

$cache_name = 'ask_details_' . $conttoken;
$taoh_vals = array(
    'token' => taoh_get_dummy_token(1),
    'ops' => $ops,
    'mod' => $mod,
    'cache_name' => $cache_name,
    'cfcc1d'=> 1,
    'conttoken' => $conttoken,

);
//$taoh_vals[ 'cfcache' ] = $cache_name;
ksort($taoh_vals);
//echo taoh_apicall_get_debug($taoh_call, $taoh_vals);exit();
$response = taoh_apicall_get($taoh_call, $taoh_vals, TAOH_API_PREFIX, 1);
$response = json_decode($response, true);
//print_r($response);die;
if($response['success']){
    $array_data = array();
    $array_data['success'] = true;
    $ask = $response['output'];


    $ask_title = ucfirst(taoh_title_desc_decode($ask['title']));
    $ask_description_modal = taoh_title_desc_decode($ask['description']);
    $ask_description = taoh_title_desc_decode($ask['description']);
    $ask_company = isset($ask['meta']['company'])? $ask['meta']['company'] : '';
    $ask_company_name = isset($ask['meta']['company'])?$ask['meta']['company']['0']['title']: '';
    $ask_location = $ask['meta']['full_location'];
    $ask_created = $ask['created'];
    /* $ask_placeType = $ask['meta']['placeType'];
    $ask_roltype = $ask['meta']['roletype']; */


    $owner_ptoken = $ask['ptoken'];
    $share_link = TAOH_SITE_URL_ROOT.'/asks/d/'.slugify2($ask_title).'-'.$conttoken;
    $taoh_url_vars = slugify2($ask_title).'-'.$conttoken;

    if($from == 'detail'){
        $get_title = ucfirst(taoh_title_desc_decode($ask['title']));
        $meta_desc = taoh_title_desc_decode($ask['meta']['description']);


        define( 'TAO_PAGE_DESCRIPTION', strip_tags($meta_desc));
        define( 'TAO_PAGE_IMAGE', @$ask[ 'image' ] );
        define( 'TAO_PAGE_TITLE', $get_title );

        define ( 'TAO_PAGE_ROBOT', 'index, follow' );

        $additive = '';
        $additive = '<link rel="canonical" href="'.$response['meta']['source'].'/'.TAOH_WERTUAL_SLUG.'/'.($app_data ? $app_data->slug : '').'/d/'.slugify2($get_title)."-".$conttoken.'"/>
                <meta name="original-source" content="'.$additive.'"/>';

        define ( 'TAO_PAGE_CANONICAL', $additive );

    }
    if(taoh_user_is_logged_in()){
        $answer_btn = '<a class="btn theme-btn mb-3 button-width post_answer" >Answer</a>';
    }else{

        $answer_btn = '<a class="btn theme-btn button-width mb-3 create_referral"
					data-location="'.$share_link.'" data-title="'.$ask_title.'"
					data-sharelink="'.$share_link.'">Answer</a>';
        /*$answer_btn = '<a class="btn theme-btn mb-3 login-button" aria-pressed="true" data-location="'.$share_link.'"
         data-toggle="modal" data-target="#config-modal"  data-title="'.$ask_title.'" data-sharelink="'.$share_link.'"><i class="icon-line-awesome-wrench"></i> Signup Here to Answer <i class="icon-material-outline-arrow-right-alt"></i></a>';
        */
    }

    $liked_check = '<span class="like_render ml-1"></span>';
    $shares_count = '<!--<a class="" style="cursor:pointer; vertical-align: text-bottom">
    <img  class="share_box"
    title="Share" data-conttoken="'.$conttoken.'" data-title="'.$ask_title.'"
    data-ptoken = "'.$ptoken.'" data-share = "'.$share_link.'"
     src="'.TAOH_SITE_URL_ROOT.'/assets/images/share-fill.svg" alt="Share" style="width: 18px"></a> -->

    <svg class="share_box drk-lgt-svg-share" title="Share" data-conttoken="'.$conttoken.'" data-title="'.$ask_title.'" data-ptoken = "'.$ptoken.'" data-share = "'.$share_link.'" width="18" height="18" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M7.85714 4.28571C9.04018 4.28571 10 3.32589 10 2.14286C10 0.959821 9.04018 0 7.85714 0C6.67411 0 5.71429 0.959821 5.71429 2.14286C5.71429 2.23214 5.71875 2.32143 5.72991 2.40848L3.62946 3.45759C3.24554 3.08482 2.72098 2.85714 2.14286 2.85714C0.959821 2.85714 0 3.81696 0 5C0 6.18304 0.959821 7.14286 2.14286 7.14286C2.72098 7.14286 3.24554 6.91518 3.62946 6.54241L5.72991 7.59152C5.71875 7.67857 5.71429 7.76562 5.71429 7.85714C5.71429 9.04018 6.67411 10 7.85714 10C9.04018 10 10 9.04018 10 7.85714C10 6.67411 9.04018 5.71429 7.85714 5.71429C7.27902 5.71429 6.75446 5.94196 6.37054 6.31473L4.27009 5.26562C4.28125 5.17857 4.28571 5.09152 4.28571 5C4.28571 4.90848 4.28125 4.82143 4.27009 4.73438L6.37054 3.68527C6.75446 4.05804 7.27902 4.28571 7.85714 4.28571Z" />
    </svg>';
    ?>
    <div class="light-dark-card p-3 right-detail-tab <?php echo 'from_'.$from;?> desktop-ask-list">
        <div class="light-dark-card sticky">
                <?php if($from == 'detail' && isset($app_data)) { ?>
                    <div class="">
                        <div  style="float:left" class="mt-3" id="go_back">
                            <a href="<?php echo TAOH_SITE_URL_ROOT.'/'.($app_data?->slug ?? '').'/'; ?>" class="back-btn"><i class="las la-arrow-left"></i> Back</a>
                        </div>
                        <div style="float:right" class="mt-3" id="answer_button"><?php echo $answer_btn;?></div>
                    </div>
                <?php } ?>
                <div class="clear">
                    <h4 class="fs-19 mt-2 mb-2" style="font-weight: 500">
                        <?php echo $ask_title.' '.$liked_check.' '.$shares_count?>
                        <span class="fs-14 m-2" style="float: right;">
                                Posted <?php echo taohFullyearConvert($ask_created)?>
                        </span>

                </div>
                <div id="asksDetailLocation" class="asks-detail-location">
                    <p class="companytags">
                        <span><?php
                        if($ask_company && $ask_company !='')
                        echo newgenerateCompanyHTML($ask_company,true).' | ';
                        echo newgenerateLocationHTML($ask_location);?></span>
                    </p>

                </div>
                <?php if($from == 'listing') { ?>
                    <div class="mt-3" id="answer_button"><?php echo $answer_btn;?></div>
                <?php } else { ?>
                    <div class="mt-2" >&nbsp;</div>
                <?php } ?>
        </div>
        <div class="skill-detail-block pt-3">
            <h3 class="fs-17"><i class="las la-shapes"></i> Skills</h3>
            <div class="divider"><span></span></div>
            <ul class=""><?php echo newgenerateSkillHTML($ask['meta']['skill']);?></ul>
        </div>
        <div class="ask_desc">
            <h3 class="fs-17"><i class="las la-id-card"></i> Description</h3>
            <div class="divider"><span></span></div>
            <div class="desc" style=""><?php echo $ask_description;?></div>
        </div>

        <hr>
            <?php // if ( taoh_user_is_logged_in()) {
                // if ( $user->profile_complete != 0) { ?>
                <div class="ask_answers" id="scroll_show">
                <div class="container">
                    <div class="">
                        <div class="comment-tags">
                            <div class="collapse show" id="collapseExample">
                                <div class="card-body" id="scroll_id">
                                    <section class="mb-3 mt-3">
                                        <?php echo taoh_comments_widget(array(
                                            'conttoken'=> $conttoken,
                                             'conttype'=> 'ask',
                                             'redirect'=> $share_link,
                                              'label'=> 'Answer')); ?>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div><!-- end hero-content -->
                </div><!-- end container -->
            </div><!-- end hero-area -->
            <?php // } } ?>


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