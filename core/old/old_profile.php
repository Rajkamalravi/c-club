<?php 
taoh_get_header();
$ptoken = taoh_parse_url(1);
//$api = 'https://preapi.tao.ai/users.user.get?mod=taoai&token=hT93oaWC&ops=info&ptoken=oyeuyy9vnx1u';

/* Get User Info */
$taoh_call = "users.user.get";
$taoh_vals = array(
    'mod' => 'taoai',
    'token' => taoh_get_dummy_token(),
    'ops' => 'info',
    'ptoken' => $ptoken,
);
//echo taoh_apicall_get_debug($taoh_call, $taoh_vals);exit();
$return = taoh_apicall_get($taoh_call, $taoh_vals);
/* Get User Info */

$about_type  = '';
$about_me = '';
$fun_fact = '';
$data = json_decode($return,true);
//print_r($data);die();
if(!isset($data['output'])){
    taoh_set_error_message('Invalid profile!');
    return taoh_redirect(TAOH_SITE_URL_ROOT);
    die();
}
$about_me = implode(' ',array_filter(explode(' ',$data['output']['user']['about_me'])));
$fun_fact = implode(' ',array_filter(explode(' ',$data['output']['user']['fun_fact'])));
if(isset($data['output']['user']['about_type']))
$about_type = implode(' ',array_filter(explode(' ',$data['output']['user']['about_type'])));
$get_skill = $data['output']['user']['skill'];
//print_r($get_skill);die();
//$skill = explode(",",$get_skill);
$edu_list = isset($data['output']['user']['education'])?$data['output']['user']['education']:'';
$emp_list = isset($data['output']['user']['employee'])?$data['output']['user']['employee']:'';
if (!taoh_user_is_logged_in()) {
    $about_me = substr($about_me, 0, 100);
    $fun_fact = substr($fun_fact, 0, 100);
    $about_type = substr($about_type, 0, 100);
}
?>
<style>
.skill-link {
    color: #6c727c;
    background-color: powderblue;
    margin-right: 5px;
    margin-bottom: 7px;
    text-align: center;
    display: inline-block;
    font-size: 12px;
    line-height: 16px;
    padding: 7px 15px;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 6px;
    -webkit-transition: all 0.2s;
    -moz-transition: all 0.2s;
    -ms-transition: all 0.2s;
    -o-transition: all 0.2s;
    transition: all 0.2s;
    /* border: 1px solid rgba(121, 127, 135, 0.05);*/
}
.prof-link {
    color: #fff;
    background-color: #131a4c;
    /* margin-right: 5px; */
    margin-bottom: 7px;
    text-align: center;
    /* display: inline-block; */
    font-size: 12px;
    line-height: 30px;
    padding: 7px 15px;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 20px;
    -webkit-transition: all 0.2s;
    -moz-transition: all 0.2s;
    -ms-transition: all 0.2s;
    -o-transition: all 0.2s;
    transition: all 0.2s;
    /* border: 1px solid rgba(121, 127, 135, 0.05); */
}
.colored{
    height:150px;
    background-color:black;
    border-radius: 8px;
}
.profile{
    position:absolute;
    margin-top:-58px;
}
@media (min-width: 1280px){
  .container {
      max-width: 1021px;
  }
}
#loading {
  filter: blur(3px);
}
.emp_response h3{
    font-size: medium;
}
</style>
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
--><!-- Modal -->
<div class="modal fade" id="message-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div style="background:none; border:none" class="modal-content">
         <div class="modal-body p-0 ">
            <div class="user-panel-main-bar">
               <div class="user-panel">
                  <div class="delete-account-info Message-area card card-item border border-danger">
                  
                     <div id="deleteAccountBody" class="card-body">
                        <h3 class="fs-22 text-danger fw-bold">Type your message</h3>
                        <div class="row fs-15 mt-4 mb-4">
                            <div class="col-10">
                                <textarea required style="width: 100%;height: 150px;" maxlength="500" placeholder="Say something"  id="message" name="message"></textarea>
                            </div>
                        </div>
                        <button onclick="postMessage()" type="button" class="btn btn-primary fw-medium" data-toggle="modal" data-target="#messageModal" id="message-button">Send</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                     </div>
                  </div>
               </div>
               <!-- end user-panel -->
            </div>
            <!-- end user-panel-main-bar -->
         </div>
      </div>
   </div>
</div>
<section class="blog-area pt-80px pb-80px">
    <div class="container">
        <div class="media media-card p-0">
            <div class="media-body">
                <div class="colored"></div>    
                <div class="media-card mb-0">
                    <div class="profile">
                        <img width="48" height="48" src="<?php echo TAOH_OPS_PREFIX.'/avatar/PNG/128/'.$data['output']['user']['avatar'].'.png';?>" alt="">
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="col-lg-8">
                            <span class="text-black mr-3"><?php echo $data['output']['user']['chat_name'];?></span>
                            <span class="prof-link"><?php echo $data['output']['user']['type'];?></span>
                        </div>
                        <div class="col-lg-4">

                            <?php if( taoh_user_is_logged_in() && (isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken) && taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken != $ptoken)) { ?>
                            <div >
                                <div class="hero-btn-box text-right py-3">
                                <button type="button" class="btn btn-primary fw-medium" data-toggle="modal" data-target="#message-modal">
                                Send Email
                                </button>
                                </div>
                            </div>
                            <?php }else{ 
                                if (!taoh_user_is_logged_in()) {?>
                                    <div>
                                        <div class="hero-btn-box text-right py-3">
                                            <a href="<?php echo TAOH_LOGIN_URL; ?>" class="btn btn-primary fw-medium">
                                                Login / Sign Up
                                            </a>
                                        </div>
                                    </div>
                                <?php } 
                            }?>
                        </div>
                    </div>
                    <div>
                        <div class="mt-1"><?php echo $data['output']['user']['full_location'];?></div>
                        <!-- <div class="mt-1"><?php //echo $data['output']['user']['timezone'];?></div> -->
                        <!-- <div class="mt-2"><a href="" class="btn btn-primary btn-sm" style="border-radius: 15px;"><i class="fa-solid fa-message fa-sm"></i> Message</a></div> -->
                    </div>
                </div>    
            </div>
        </div>
        <?php if(!empty($about_me)){ ?>
        <div class="media media-card">
            <div class="media-body">
                <div class="mb-2"><h5>About</h5></div>
                <?php if (!taoh_user_is_logged_in()) { ?>
                    <a href="#" data-toggle="tooltip" data-placement="top" title="Login to see the full details!">
                        <div class="mt-2 mb-2" id="loading">
                            <?php echo $about_me.'......'; ?>
                        </div>
                    </a>
                <?php }else{ ?>
                    <div class="mt-2 mb-2">
                        <?php echo $about_me; ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
        <?php if(!empty($fun_fact)){ ?>
        <div class="media media-card">
            <div class="media-body">
                <div class="mb-2"><h5>Fun Fact</h5></div>
                <?php if (!taoh_user_is_logged_in()) { ?>
                    <a href="#" data-toggle="tooltip" data-placement="top" title="Login to see the full details!">
                        <div class="mt-2 mb-2" id="loading">
                            <?php echo $fun_fact.'......'; ?>
                        </div>
                    </a>
                <?php }else{ ?>
                    <div class="mt-2 mb-2">
                        <?php echo $fun_fact; ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
        <div class="media media-card">
            <div class="media-body">
                <div class="mb-2"><h5>Skills</h5></div>
                <?php if (!taoh_user_is_logged_in()) { ?>
                    <a href="#" data-toggle="tooltip" data-placement="top" title="Login to see the full details!">
                        <div class="mt-2 mb-2" id="loading">
                            <?php foreach($get_skill as $keys => $vals){
                                if(!empty($vals['name'])){?>
                                <span class="skill-link"><?php echo $vals['name']; ?></span>
                            <?php } } ?>
                        </div>
                    </a>
                <?php }else{ ?>
                    <div class="mt-2 mb-2">
                        <?php foreach($get_skill as $keys => $vals){
                            if(!empty($vals['name'])){?>
                            <span class="skill-link"><?php echo $vals['name']; ?></span>
                        <?php } } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php if(!empty($about_type)){ ?>
        <div class="media media-card">
            <div class="media-body">
                <div class="mb-2"><h5>About Profile Type</h5></div>
                <?php if (!taoh_user_is_logged_in()) { ?>
                    <a href="#" data-toggle="tooltip" data-placement="top" title="Login to see the full details!">
                        <div class="mt-2 mb-2" id="loading">
                            <?php echo $about_type.'......'; ?>
                        </div>
                    </a>
                <?php }else{ ?>
                    <div class="mt-2 mb-2">
                        <?php echo $about_type; ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>

        <?php if(is_array($emp_list)){ ?>
        <div class="media-card">
            <div class="mb-4"><h5>Experience</h5></div>
            <?php foreach($emp_list as $emp_keys => $emp_vals){
            $em_title = $emp_vals['title'];
            foreach ( $em_title as $em_key => $em_value ){
                list ( $em_pre, $em_post ) = explode( ':>', $em_value );    
            }
            $em_company = $emp_vals['company'];
            foreach ( $em_company as $em_cmp_key => $em_cmp_value ){
                list ( $em_cmp_pre, $em_cmp_post ) = explode( ':>', $em_cmp_value );    
            }
            $get_present_not = ($emp_vals['current_role'] == 'on')?' Present':get_month_from_number($emp_vals['emp_end_month']).' '.$emp_vals['emp_year_end'];
            //print_r($get_present_not);
            ?>
            <?php if (!taoh_user_is_logged_in()) { ?>
                <a href="#" data-toggle="tooltip" data-placement="top" title="Login to see the full details!">
                    <div class="mt-2 mb-2" id="loading">
                        <div class="d-flex mt-3">
                            <span style="height:45px;width:45px;" class="media-img d-block">
                                <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/work.png'; ?>" alt="company logo">
                            </span>
                            <div class="media-body border-left-0 emp_response">
                                <h5 class="mb-1 fs-16 fw-medium"><a><?php echo $em_post; ?></a></h5>
                                <p class="mb-1 fs-13 font-weight-bold"><?php echo $em_cmp_post; ?></p>
                                <p class="mb-4 lh-20 fs-13"><?php echo get_month_from_number($emp_vals['emp_start_month']).' '.$emp_vals['emp_year_start'].' - '.$get_present_not ?> . <span><?php echo get_diff_dates($emp_vals['emp_year_start'],$emp_vals['emp_start_month'],$emp_vals['emp_year_end'],$emp_vals['emp_end_month']); ?></span></p>
                                <p class="lh-20 fs-13"><?php echo (strlen($emp_vals['emp_responsibilities'])<=200)?$emp_vals['emp_responsibilities']:mb_substr($emp_vals['emp_responsibilities'], 0, 200).'......'; ?></p>
                            </div>
                        </div>
                    </div>
                </a>
            <?php }else{ ?>
                <div class="mt-2 mb-2">
                    <div class="d-flex mt-3">
                        <span style="height:45px;width:45px;" class="media-img d-block">
                            <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/work.png'; ?>" alt="company logo">
                        </span>
                        <div class="media-body border-left-0 emp_response">
                            <h5 class="mb-1 fs-16 fw-medium"><a><?php echo $em_post; ?></a></h5>
                            <p class="mb-1 fs-13 font-weight-bold"><?php echo $em_cmp_post; ?></p>
                            <p class="mb-4 lh-20 fs-13"><?php echo get_month_from_number($emp_vals['emp_start_month']).' '.$emp_vals['emp_year_start'].' - '.$get_present_not ?> . <span><?php echo get_diff_dates($emp_vals['emp_year_start'],$emp_vals['emp_start_month'],$emp_vals['emp_year_end'],$emp_vals['emp_end_month']); ?></span></p>
                            <p class="lh-20 fs-13"><?php echo (strlen($emp_vals['emp_responsibilities'])<=200)?$emp_vals['emp_responsibilities']:mb_substr($emp_vals['emp_responsibilities'], 0, 200).'......'; ?></p>
                        </div>
                    </div>
                </div>
            <?php } ?>           
            <?php } ?>           
        </div> 
        <?php } ?>

        <?php if(is_array($edu_list)){ ?>
        <div class="media-card">
            <div class="mb-4"><h5>Education</h5></div>
            <?php foreach($edu_list as $edu_keys => $edu_vals){
            $ed_name = $edu_vals['company'];
            foreach ( $ed_name as $ed_key => $ed_value ){
                list ( $ed_pre, $ed_post ) = explode( ':>', $ed_value );    
            }
            ?>
            <?php if (!taoh_user_is_logged_in()) { ?>
                <a href="#" data-toggle="tooltip" data-placement="top" title="Login to see the full details!">
                    <div class="mt-2 mb-2" id="loading">
                        <div class="d-flex mt-3">
                            <span style="height:45px;width:45px;" class="media-img d-block">
                                <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/education.png'; ?>" alt="company logo">
                            </span>
                            <div class="media-body border-left-0">
                                <h5 class="mb-1 fs-16 fw-medium"><a><?php echo $ed_post; ?></a></h5>
                                <p class="mb-1 fs-13 font-weight-bold"><?php echo $edu_vals['edu_specalize']; ?></p>
                                <p class="mb-4 lh-20 fs-13"><?php echo $edu_vals['edu_start_year'].' - '.$edu_vals['edu_complete_year']; ?></p>
                                <p class="lh-20 fs-13"><?php echo (strlen($edu_vals['edu_description'])<=200)?$edu_vals['edu_description']:mb_substr($edu_vals['edu_description'], 0, 200).'......'; ?></p>
                            </div>
                        </div>
                    </div>
                </a>
            <?php }else{ ?>
                <div class="mt-2 mb-2">
                    <div class="d-flex mt-3">
                        <span style="height:45px;width:45px;" class="media-img d-block">
                            <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/education.png'; ?>" alt="company logo">
                        </span>
                        <div class="media-body border-left-0">
                            <h5 class="mb-1 fs-16 fw-medium"><a><?php echo $ed_post; ?></a></h5>
                            <p class="mb-1 fs-13 font-weight-bold"><?php echo $edu_vals['edu_specalize']; ?></p>
                            <p class="mb-4 lh-20 fs-13"><?php echo $edu_vals['edu_start_year'].' - '.$edu_vals['edu_complete_year']; ?></p>
                            <p class="lh-20 fs-13"><?php echo (strlen($edu_vals['edu_description'])<=200)?$edu_vals['edu_description']:mb_substr($edu_vals['edu_description'], 0, 200).'......'; ?></p>
                        </div>
                    </div>
                </div>
            <?php } ?>            
            <?php } ?>           
        </div> 
        <?php } ?>     
    </div>
</section>
<script type="text/javascript">
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();
});

function postMessage() {
    var message = $('#message').val();
     var data = {
        'taoh_action': 'taoh_post_message',
        'message' :message,
	    "ptoken" : "<?php echo $ptoken; ?>",
      };
      $('#message').val('');
      $('.Message-area').html('<p style="color:green;margin:100px;">Message Sent Successfully!</p>');
      setTimeout(function(){
            $('#message-modal').modal('hide')

	},1500);
      

     
     jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {

     }).fail(function() {
         console.log( "Message - Network issue!" );
         //comments.append("<p>Server Error!</p>");
     })
   }
</script>
<?php taoh_get_footer(); ?>