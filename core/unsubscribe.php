<?php
taoh_add_var_to_url('noca', TAOH_MY_NOW_CODE);
taoh_get_header_iframe();
//print_r($_SESSION);die();
$path = $_SERVER['REQUEST_URI']; // Example: /club/unsubscribe/pt/4lzigvukqx9o
 $ptToken = taoh_parse_url(2);
 $taoh_home_url = ( defined( 'TAOH_PAGE_URL' ) && TAOH_PAGE_URL ) ? TAOH_PAGE_URL:TAOH_SITE_URL_ROOT;


if(isset($_REQUEST['pt']) && $_REQUEST['pt'] !=''){

    $pt = $ptToken = $_REQUEST['pt'];

   $remove = array("profile_detail_" . $pt,
          "profile_short_" . taoh_get_api_token(),
          "profile_info_" . $pt,
          "profile_cell_" . $pt,
          "profile_full_" . $pt,
          "profile_public_" . $pt,
          "*_networking_cell_" . $pt,
          "users_*",
      );
 $enter = array(

          'tao_unsubscribe_emails' => '1',
    );
  $taoh_call = 'users.unsubscribe.update';
  $taoh_call_type = 'POST';
  $taoh_vals = array(
    'token' => taoh_get_dummy_token(),
    'mod' => 'tao_tao',
    'toenter' => $enter,
    'redis_action' => 'profile_update',
    'redis_store' => 'taoh_intaodb_common',
    'ptoken' => $pt,
    'cache' => array('remove' => $remove),
    //'debug' => false,
  );
  //echo taoh_apicall_post_debug($taoh_call, $taoh_vals);die; // Unsub API Call
  //$result = taoh_apicall_post($taoh_call, $taoh_vals);
  $result = taoh_apicall_post($taoh_call, $taoh_vals);
  //echo $result; // For testing
  $res = json_decode($result, true);
  if($res['success']){
    $unsubscribed_now = true;
    unset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']);
  }
 else{
    $unsubscribed_now = false;
 }
}
?>
<style>
    .blur{
        filter: blur(0.7px);
    }
    .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after {
    display: block; /* Ensure the tick mark is visible */
}
        .custom-control-label::after {
            top: 2px; /* Fine-tune checkbox position */
    left: -26px; /* Align with label */
    width: 20px; /* Checkbox width */
    height: 20px; /* Checkbox height */
        }
</style>
<!-- ================================
         START ERROR AREA
================================= -->
<section class="user-details-area pt-40px pb-40px mt-5" >
<?php if(isset($_REQUEST['pt']) && $_REQUEST['pt'] !=''){ ?>


    <div class="container" style="border : 1px solid #d3d3d3; padding: 20px;">
        <?php if($unsubscribed_now) { ?>
            <div style="text-align: center;color:green;">
                You have successfully unsubscribed from receiving emails from our platform.
            </div>
        <?php }else{ ?>
            <div style="text-align: center;color:red;">
                It seems you have already unsubscribed from receiving mail!
            </div>
        <?php } ?>
        <div style="text-align: center;">
            If you clicked the unsubscribe link by mistake, please log in to the platform to start receiving emails again
            or submit below form to resubscribe.
        </div>
    </div>

<div class="container" style="border : 1px solid #d3d3d3; padding: 20px;">
         <div class="logo-box" style="text-align: left; display: flex; align-items: center;">
          <a href="<?php echo $taoh_home_url . "/../"; ?>" class="logo">
            <img src="<?php echo (defined('TAOH_PAGE_LOGO')) ? TAOH_PAGE_LOGO : TAOH_SITE_LOGO; ?>" alt="logo" style="max-height: 45px; width: auto;">
          </a>
          <?php
          if (defined('TAOH_SITE_LOGO_2') && TAOH_SITE_LOGO_2) {
            $logo_2_arr = json_decode(TAOH_SITE_LOGO_2);
            echo '&nbsp;&nbsp;<a href="' . $logo_2_arr[1] . '" target="_blank"><img src="' . $logo_2_arr[0] . '" alt="logo" style="max-height: 40px; width: auto; margin-left: 10px;"></a>';
          }
          ?>
        </div>

        <div class="row mt-4" style="width:600px:align:center;">
            <div class="col-lg-12">
                <h1 class="section-title fs-24 mb-1">Re-subscribe to receive mail </h1>
                  <form id="subscribe_form" method="post" action="#" class="pt-35px">
                    <input type="hidden" name="taoh_session" id="taoh_session" value="settings">
                    <div class="user-panel">
                        <div class="settings-item mb-20px border-bottom border-bottom-gray pb-20px">
                            <div class="input-box">

                            <div class="form-group">

                                                <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/loginmail.png" width="50" height=""/>
                                                &nbsp;&nbsp;&nbsp;
                                                Click on the Subscribe Again button to start receiving any emails from us.
                                </div>
                            </div>
                        </div><!-- end settings-item -->

                    </div><!-- end user-panel -->
                    <div class="mt-2">
                        <div class="submit-btn-box pt-3">
                            <input type="hidden" name="taoh_ptoken" value="<?php echo $ptToken; ?>">
                            <button id="unsubscribe_changes_0" class="btn theme-btn" onclick="submitForm(0)"  type="button">Subscribe Again</button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- end row -->
    </div>

<?php }else{ ?>
    <div class="container" style="border : 1px solid #d3d3d3; padding: 20px;">
         <div class="logo-box" style="text-align: left; display: flex; align-items: center;">
          <a href="<?php echo $taoh_home_url . "/../"; ?>" class="logo">
            <img src="<?php echo (defined('TAOH_PAGE_LOGO')) ? TAOH_PAGE_LOGO : TAOH_SITE_LOGO; ?>" alt="logo" style="max-height: 45px; width: auto;">
          </a>
          <?php
          if (defined('TAOH_SITE_LOGO_2') && TAOH_SITE_LOGO_2) {
            $logo_2_arr = json_decode(TAOH_SITE_LOGO_2);
            echo '&nbsp;&nbsp;<a href="' . $logo_2_arr[1] . '" target="_blank"><img src="' . $logo_2_arr[0] . '" alt="logo" style="max-height: 40px; width: auto; margin-left: 10px;"></a>';
          }
          ?>
        </div>

        <div class="row mt-4" style="width:600px:align:center;">
            <div class="col-lg-12">
                <h1 class="section-title fs-24 mb-1">Unsubscribe to receive mail </h1>
                  <form id="unsubscribe_form" method="post" action="#" class="pt-35px">
                    <input type="hidden" name="taoh_session" id="taoh_session" value="settings">
                    <div class="user-panel">
                        <div class="settings-item mb-20px border-bottom border-bottom-gray pb-20px">
                            <div class="input-box">

                            <div class="form-group">

                                                <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/icons/000000/unsubscripe_png_360.png" width="" height=""/>
                                                &nbsp;&nbsp;&nbsp;

                                                We are sorry to see you go.

                                                 Click on the unsubscribe button to stop receiving any emails from us.
                                </div>
                            </div>
                        </div><!-- end settings-item -->

                    </div><!-- end user-panel -->
                    <div class="mt-2">
                        <div class="submit-btn-box pt-3">
                            <input type="hidden" name="taoh_ptoken" value="<?php echo $ptToken; ?>">
                            <button id="unsubscribe_changes_1" class="btn theme-btn" onclick="submitForm(1)"  type="button">Unsubscribe</button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- end row -->
    </div><!-- end container -->
<?php } ?>
</section>
<div class="modal fade" id="unsubscribeAlert" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">You have been successfully unsubscribed! </h5>
      </div>
      <div class="modal-body">
      You have successfully unsubscribed from receiving emails from our platform. If you log in to the platform,
      you will start receiving emails again.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal" id="redirectButton" >Ok</button>
      </div>
    </div>
  </div>
</div>
<!-- ================================
         END ERROR AREA
================================= -->
<script>
      document.getElementById('redirectButton').onclick = function() {
        //window.location.href = '<?php //echo TAOH_SITE_URL_ROOT ?>//?logout=1'; // Replace with your desired URL
        window.location.href = '<?php echo trim(TAOH_SITE_URL_ROOT, '/'); ?>/logout'; // Replace with your desired URL
    };


    function submitForm(tao_unsubscribe_emails) {


       //var tao_unsubscribe_emails = '1';
        var data = {
            'taoh_action': 'update_unsub',
            'taoh_session': 'settings',
            'taoh_ptoken': '<?php echo $ptToken; ?>',
            'tao_unsubscribe_emails': tao_unsubscribe_emails,
		};

        $('#unsubscribe_changes_'+tao_unsubscribe_emails).prop("disabled", true);
        $('#unsubscribe_changes_'+tao_unsubscribe_emails).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

        if(tao_unsubscribe_emails)
            taoh_set_warning_message('Email unsubscribe in process!!!');
        else
            taoh_set_success_message('Email subscribe in process!!!');

            jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {

                res = response;
                if(res){

                    $('#unsubscribe_changes_'+tao_unsubscribe_emails).prop("disabled", false);
                    $('#unsubscribe_changes_'+tao_unsubscribe_emails).html('Done!');

                    if(tao_unsubscribe_emails)
                    $('#unsubscribeAlert').modal('show');
                    else{
                        <?php
                           unset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']);
                        ?>
                        window.location.href = '<?php echo trim(TAOH_SITE_URL_ROOT, '/'); ?>';
                    }


                }
            }).fail(function() {
                console.log( "Network issue!" );
                taoh_set_error_message('Network issue!');
                $('#unsubscribe_changes_'+tao_unsubscribe_emails).prop("disabled", false);
                $('#unsubscribe_changes_'+tao_unsubscribe_emails).html('Done!');
            })
    };

</script>
<?php //taoh_get_footer();  ?>