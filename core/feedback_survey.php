<?php
$form = 1;
if ( isset( $_POST[ 'ptoken' ] ) ){
        
    $taoh_call = 'core.content.post';
    $taoh_call_type = 'POST';
    $taoh_vals = array(
        'token'=>taoh_get_dummy_token(),
        'ptoken'=>$_POST['ptoken'],
        'mod' => 'tao_tao',
        'ops' => 'add',
        'type' => 'feedback',
        'toenter' => $_POST,
    );
    //echo taoh_apicall_post_debug($taoh_call, $taoh_vals);die();
    $result = taoh_apicall_post($taoh_call, $taoh_vals);
    $return = json_decode($result, true);
    //print_r($return);exit();
    //echo $return['success'];exit();
    if(isset($return['success']) && $return['success']){
        taoh_set_success_message("Thank you so much for your time to give us feedback. ");
        // taoh_redirect(TAOH_SITE_URL_ROOT);
        //taoh_exit();
        $form = 0;
    }

}

?>

<?php taoh_get_header(); ?>
<link rel="stylesheet" href="https://bug7a.github.io/iconselect.js/sample/css/lib/control/iconselect.css">
<?php

$notvalid = 0;
// check pt
if (strpos(TAOH_REDIRECT_URL, "/pt/") !== false) {
    $parts = explode("/pt/", TAOH_REDIRECT_URL);
    if ( isset($parts[1]) && strlen($parts[1]) > 10 ) {
        $ptoken = explode("/", $parts[1])[0];
    } else {
        $notvalid = 1;
    }
} else {
    $notvalid = 1;
}

if ($notvalid) {
    $url = TAOH_SITE_URL_ROOT;
    taoh_redirect($url);
    taoh_exit();
}

$taoh_home_url = ( defined( 'TAOH_PAGE_URL' ) && TAOH_PAGE_URL ) ? TAOH_PAGE_URL:TAOH_SITE_URL_ROOT;
//if ( taoh_user_is_logged_in() ){ 
    // check app name
    $conttoken = '';$detail_name=''; $app_name=''; $title=''; 
    if (strpos(TAOH_REDIRECT_URL, "/app/") !== false) {
        $parts = explode("/app/", TAOH_REDIRECT_URL);
        if (isset($parts[1])) {
            $app_name = explode("/", $parts[1])[0];
        } 
    }     
    // check conttoken
    if (strpos(TAOH_REDIRECT_URL, "/conttoken/") !== false) {
        $parts = explode("/conttoken/", TAOH_REDIRECT_URL);
        if (isset($parts[1])) {
            $conttoken = explode("/", $parts[1])[0];
            $taoh_call = "core.content.get";
            $taoh_vals = array(
                'token'=>taoh_get_dummy_token(1),
                'mod' => 'core',
                'type'=> 'detail',
                'ops' => 'detail',
                'code' => $conttoken,
                'app_slug' => $app_name,
                 //'cfcc1h' => 1 //cfcache newly added
            );
            $cache_name = $taoh_call.'_detail_' . hash('sha256', $taoh_call . serialize($taoh_vals));
            //$taoh_vals[ 'cfcache' ] = $cache_name;
            $taoh_vals[ 'cache_name' ] = $cache_name;
            ksort($taoh_vals);
            
            //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );die();
            $response = json_decode(taoh_apicall_get( $taoh_call, $taoh_vals ), true);
            $result_arr = $response['output'][0];
        } 
    } 
//}else{
    // check pt name
    /*if (strpos(TAOH_REDIRECT_URL, "/pt/") !== false) {
        $parts = explode("/pt/", TAOH_REDIRECT_URL);
        if (isset($parts[1])) {
            $utoken = explode("/", $parts[1])[0];
        } 
    }else{
        if($conttoken){
            if($app_name == 'events'){
                $detail_name = '/next/'.$conttoken;
            }else{
                $detail_name = '/d/'.$conttoken;
            }
        }
        $url = $taoh_home_url."/support/".$app_name.$detail_name;
        taoh_redirect($url);taoh_exit();
    }*/
//}

if($result_arr['title']){
    $title = urldecode($result_arr['title']);
    if($app_name == 'events'){
        $event_start_at = @$result_arr['local_start_at'];
        $event_start = date("d-m-Y", strtotime($event_start_at));
    }else{
        $event_start_at = @$result_arr['created'];
        $event_start = date("d-m-Y", strtotime($event_start_at));
    }
}else if( isset($app_name) && ($app_name != 'stlo') ) {
    $title = ucfirst($app_name);
}else{
    $title = TAOH_SITE_NAME_SLUG;
}
//echo '====================='.$title;die();
//print_r($data);die();
?>
<style>
   .ts-control {
      height: 50px !important;
      border-color: rgba(127, 136, 151, 0.2) !important;
      line-height: 31px;
      font-size: 15px;
   }
   span.h5 {
      font-size: 13px !important;
   }
   .error{
      color:red;
      /*top:0;*/
      /*position:absolute;*/
      /*left:108px;*/
      /*white-space:nowrap;*/
   }
    .custom-radios div, .expectations_radio div {
        display: inline-block;
    }
    .custom-radios input[type="radio"], .expectations_radio input[type="radio"]{
        /*display: none;*/
        /*position: absolute;*/
        opacity: 0;
        width: 0;
        height: 0;
    }
    .custom-radios input[type="radio"] + label{
        color: #333;
        font-size: 14px;
        font-weight: 600;
    }
    .custom-radios input[type="radio"] + label span {
        display: inline-block;
        width: 30px;
        height: 30px;
        margin: -1px 4px 0 0;
        vertical-align: middle;
        cursor: pointer;
        /*border-radius: 50%;*/
        /*border: 2px solid #fff;*/
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.33);
        background-repeat: no-repeat;
        background-position: center;
        text-align: center;
        line-height: 30px;
    }
    .custom-radios input[type="radio"] + label span img, .expectations_radio input[type="radio"] + label span img {
        opacity: 1;
        transition: all 0.3s ease;
        margin: 4px;
    }
    .custom-radios input[type="radio"].recommend_app + label span{
        background-color: #e9a0e161;
    }
    .custom-radios input[type="radio"]:checked + label span, .expectations_radio input[type="radio"]:checked + label span img{
        opacity: 1;
        background-color: #1bacff;
        backface-visibility: hidden;
        border: darkblue 2px solid;
        border-radius:6px;
    }

    #expectations_met_error label, #recommend_app_error label{
        color:red !important;
        display: flex !important;
        margin-top: 30px !important;
    }
    .hero-content.feedback-success {
        font-size: clamp(17px, 2vw + 1rem, 21px);
        background-color: green;
        color: white;
        margin-top: 100px;
        margin-bottom: 100px;
        padding: 15px 25px;
        width: 100%;
        max-width: 1024px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.5;
    }
</style>
<header class="sticky-top bg-white border-bottom border-bottom-gray">
<section class="hero-area bg-white shadow-sm overflow-hidden pt-3 pb-3">
    <div class="container">
        <div class="row align-items-center event-mobile-width">
            <div class="col-lg-8">
                <div class="hero-content">
					<div class="media media-card align-items-center shadow-none p-0 mb-0 rounded-0">
						<div class="media-body">
							<div class="pb-3"> 
								<h4><?php echo $title; ?></h4>
                                <?php  if($result_arr['title']){ ?>
                                    <span class="fs-10">Posted Date: <?php echo $event_start; ?></span>
                                <?php } ?>
							</div>
						</div>
					</div>
                </div><!-- end hero-content -->
            </div><!-- end col-lg-9 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section>
</header>

<?php if(!$form){ ?>
    <div class="container">
        <div class="row align-items-center event-mobile-width">
            <div class="col-lg-12">
                <div class="hero-content feedback-success">
                            Thank you so much for your time to give us feedback.
                            </div><!-- end hero-content -->
            </div><!-- end col-lg-9 -->
        </div><!-- end row -->
    </div><!-- end container -->
<?php 

} else { ?>


<section class="p-3" style="background-color: #f5f5f5;">
   <div class="container">
        <div class="row p-2 mt-2">
        <div class=" col-lg-2"></div>
            <div class="card col-lg-8 z-depth-5 mb-4">
				<div class="card-body">
                    <div class="d-flex justify-content-between">
						<div>
							<h3>Feedback Survey</h3>
						</div>
					</div>
					<hr>
                    <div class="row">
                        <div class="col-lg-12">
                            <p class="section-desc fs-15">In today's rapidly evolving job market, we stand firm in our commitment that <strong>No worker should be left behind</strong>. Our products aim to empower, enlighten, and equip you with the tools and knowledge necessary for success in your career pursuits.</p><br/>
                            <h2 class="section-title fs-17 mb-1">Your Voice Matters</h2>
                            <p class="section-desc fs-15">Your feedback is crucial. It helps us gauge our effectiveness and identify areas where we can do better.</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <form id="setting_form" method="post" action="<?php //echo TAOH_ACTION_URL .'/feedback'; ?>" class="pt-35px">
                                <div class="user-panel">
                                    <div class="settings-item">
                                        <input class="form-control form--control" value="<?php echo $ptoken; ?>" type="hidden" id="ptoken" name="ptoken">
                                        <input class="form-control form--control" value="<?php echo $conttoken; ?>" type="hidden" id="eventtoken" name="eventtoken">
                                        <input class="form-control form--control" value="<?php echo $app_name; ?>" placeholder="App Name" type="hidden" id="app_name" name="app_name">
                                        <div class="settings-item"> 
                                            <h2 class="section-title fs-18 mb-2">Feedback Questions</h2>
                                            <div class="row">
                                            <!-- end col-6 -->
                                            <div class="col-lg-12">
                                                <div class="input-box">
                                                    <div>
                                                        <label class="fs-13 text-black lh-20 fw-medium">Were your expectations met? <span class="text-danger">*</span></label>
                                                        <div class="form-group">
                                                            <div class="expectations_radio">
                                                                <?php
                                                                for ($i=1; $i <= 5 ; $i++) {
                                                                    echo '<div>
                                                                    <input type="radio" id="expectations_'.$i.'" class="expectations_met" name="expectations_met" data-error_id="#expectations_met_error"  value="'.$i.'" '.($i == 1 ? 'required':'').'>
                                                                    <label style="margin-bottom:0px;" for="expectations_'.$i.'"> 
                                                                        <span><img id="smiley'.$i.'" width="40" src="'.TAOH_SITE_URL_ROOT.'/assets/images/'.$i.'.png" alt="smiley '.$i.'" /></span> 
                                                                    </label>
                                                                </div>';

                                                                }
                                                                ?>
                                                            </div>
                                                            <div class="fs-10">
                                                                <span style="font-size: 11px; float: left;">Pathetic</span>
                                                                <span style="font-size: 11px;padding-left: 170px;position: absolute;">Fabulous</span>
                                                            </div>
                                                        </div>
                                                        <div id="expectations_met_error"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end col-6 -->
                                            <div class="col-lg-12">
                                            <div class="input-box">
                                                <div>
                                                    <label class="fs-13 text-black lh-20 fw-medium">How likely are you going to recommend this app to a friend or colleague? <span class="text-danger">*</span></label>
                                                    <div class="form-group">
                                                        <div class="custom-radios">
                                                            <?php
                                                            for ($i=1; $i <= 10 ; $i++) {
                                                                $rtext ='';
                                                                if($i==1){$rtext = "(Not at all Likely)";}
                                                                if($i==10){$rtext = "(Extremely Likely)";}
                                                                echo '<div>
                                                            <input type="radio" id="recommend_'.$i.'" class="recommend_app" name="recommend_app" data-error_id="#recommend_app_error" value="'.$i . '" ' . ($i == 1 ? 'required' : '') . '>
                                                            <label for="recommend_'.$i.'"> <span>'.$i.'</span> </label>
                                                            </div>';
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="fs-10"><span style="font-size: 11px; float: left;">Not at all Likely</span> <span style="margin-top: -3px;font-size: 11px;padding-left: 177px;position: absolute;">Extremely Likely</span></div>
                                                    </div>
                                                    <div id="recommend_app_error"></div>
                                                </div>
                                            </div>
                                            </div>
                                            <!-- end col-lg-6 -->
                                            <div class="col-lg-12">
                                            <div class="input-box">
                                                <label class="fs-13 text-black lh-20 fw-medium">What is one thing that we could provide to help you overcome your career-related challenge?</label>
                                                <div class="form-group">
                                                    <textarea  class="form-control form--control" rows="8" maxlength="5000" name="overcome_challenge"> </textarea>
                                                </div>
                                            </div>
                                            </div>
                                            <!-- end col-lg-12 -->
                                            <div class="col-lg-12">
                                            <div class="input-box">
                                                <label class="fs-13 text-black lh-20 fw-medium">Any specific suggestions or comments.</label>
                                                <div class="form-group">
                                                    <textarea  class="form-control form--control" rows="8" maxlength="5000" name="comments"> </textarea>
                                                </div>
                                            </div>
                                            </div>
                                            <!-- end col-lg-12 -->
                                        </div> 
                                        <div class="settings-item"> 
                                            <h2 class="section-title fs-18 mb-2">Interest in Supporting our Mission</h2>
                                            <div class="row">
                                            <div class="col-lg-12">
                                                <div class="input-box mb-2">
                                                    <label class="fs-13 text-black lh-20 fw-medium">Would you be interested in providing additional feedback to support our mission?</label>
                                                    <div class="form-group mb-0">
                                                        <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" id="additional_feedback_yes" name="additional_feedback" data-error_id="#additional_feedback_error" value="yes" >
                                                        <label class="form-check-label" for="additional_feedback_yes">Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" id="additional_feedback_no" name="additional_feedback" data-error_id="#additional_feedback_error" value="no">
                                                        <label class="form-check-label" for="additional_feedback_no">No</label>
                                                        </div>
                                                    </div>
                                                    <div id="additional_feedback_error"></div>
                                                </div>
                                            </div>
                                            <!-- end col-12 -->
                                            <div class="col-lg-12 contact_time" style="display: none;">
                                            <div class="input-box">
                                                <label class="fs-13 text-black lh-20 fw-medium">Best time to contact (if interested)?</label>
                                                <div class="form-group">
                                                    <input  class="form-control form--control" name="contact_time" placeholder="Best time to contact"/> 
                                                </div>
                                            </div>
                                            </div>
                                            <!-- end col-lg-12 -->
                                            <div class="col-lg-12">
                                                <div class="input-box mb-2">
                                                    <label class="fs-13 text-black lh-20 fw-medium">Would you want to volunteer to support our mission of "No Worker Left Behind"?</label>
                                                    <div class="form-group mb-0">
                                                        <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" id="volunteer1" name="volunteer" data-error_id="#volunteer_error" value="yes">
                                                        <label class="form-check-label" for="volunteer1">Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" id="volunteer2" name="volunteer" data-error_id="#volunteer_error" value="no">
                                                        <label class="form-check-label" for="volunteer2">No</label>
                                                        </div>
                                                    </div>
                                                    <div id="volunteer_error"></div>
                                                </div>
                                            </div>
                                            <!-- end col-12 -->
                                        </div> 
                                        <hr>
                                        <div class="row col-lg-12">
                                            <div class="submit-btn-box pt-2 pb-3">
                                                <button class="btn theme-btn btn-rounded" style="border-radius: 1.25rem;" type="submit">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2"></div>
        </div>
    </div>
</section>

<?php } ?>
<script type="text/javascript">

    
	let $ = jQuery;
    var type = '<?php echo $type; ?>';

    var app_name = $('#app_name').val();
    var start_date = '<?php echo $event_start ?? ''; ?>';

    if (start_date) {
        let tmp = new Date(start_date);
        if (!isNaN(tmp)) {
            let dateInputFormatted = tmp.toISOString().split('T')[0];
            $('#event_date').val(dateInputFormatted);
        }
    }

    $('.edit-form-settings').hide();

    <?php if(!$form){ ?>

        setTimeout(function () {
            window.location.href = '<?php echo TAOH_SITE_URL_ROOT; ?>';
        }, 5000);

    <?php } ?>
    //$('.contact_time').hide();
    if(app_name == 'events'){
        $('.edit-form-settings').show(); 
    }

    // $('.expectations_met input[type=radio]:checked').change(function(){
    //     //alert('sfdsf');
    //     //alert($(this).val());
    // });
    // $('input[type=radio][name=expectations_met]').change(function(){
    //     //alert('sfdsf');
    //     //alert(this.value);
    //     if(this.value == '1'){
    //     }
    // });

    // document.querySelector('form').addEventListener('submit', function (event) {
    //     if (!this.checkValidity()) {
    //         event.preventDefault();
    //         const invalidInput = this.querySelector(':invalid');
    //         if (invalidInput) invalidInput.focus();
    //     }
    // });

    $(document).ready(function(){
        $("#setting_form").validate({
            rules: {
                expectations_met: {
                    required: true
                },
                recommend_app: {
                    required: true
                }
            },
            messages: {
                expectations_met: {
                    required: "Please select were your expectations met?"
                },
                recommend_app: {
                    required: "Please select would you recommend this app to others?"
                }
            },
            errorPlacement: function (error, element) {
                if (element.parent().hasClass('form-group')) {
                    error.insertAfter(element.parent('.form-group'));
                } else if (typeof element.data('error_id') !== 'undefined' && element.data('error_id') !== false) {
                    $(element.data('error_id')).html(error);
                } else {
                    element.after(error);
                }
            },
            submitHandler: function (form) {
                let setting_form = $('#setting_form');
                let submit_btn = setting_form.find('button[type="submit"]');
                submit_btn.prop('disabled', true);

                // let submit_btn_icon = submit_btn.find('i');
                // submit_btn_icon.removeClass('fa-arrow-circle-o-right').addClass('fa-spinner fa-spin');
                form.submit();
            }
        });
    });

    /*$('input[type=radio][name=additional_feedback]').change(function(){
        if(this.value == 'yes'){
            $("#expectations_yes").prop("checked", true);
        }else{
            $("#expectations_no").prop("checked", true);
        }
    });

    $('#app_name').load(function(){
        if(this.value == 'events'){
            $('.edit-form-settings').show();
        }else{
            $('.edit-form-settings').hide();
        }
    });*/
    /*$('#participant option').each(function() {
        if($(this).text() == type){
            $(this).attr("selected","selected");
        }
    });*/
</script>
<?php taoh_get_footer();  ?>
