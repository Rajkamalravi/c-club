<?php
$_SESSION = array();
if(isset($_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'locked']) && $_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'locked'] == 1) {
  // do nothing
}
else{
  if ( taoh_user_is_logged_in() ){
    $url = TAOH_SITE_URL_ROOT.'?uslo=1';
      taoh_redirect($url);
      taoh_exit();
  }
}
taoh_get_header();
$current_app = taoh_parse_url(1) ? taoh_parse_url(1) : TAOH_WERTUAL_SLUG;
//echo "====================".$current_app;
//die('------------');
$app_data = taoh_app_info($current_app);
$code = json_decode( taoh_url_get_content(TAOH_SITE_CAPTCHA), true );
$get_send_email_code = json_decode( taoh_url_get_content(TAOH_OPS_PREFIX.'/scripts/code.php?getkey=1'), true );

$lock_code = TAOH_LOGIC_LOCK_CODE;
$lock_text = TAOH_LOGIC_LOCK_TEXT;

?>
<style>
    .form-control:focus {
        box-shadow: none;
    }
    a {
        color: #007bff;
        text-decoration: none;
        background-color: transparent;
    }
</style>

<section class="sign-up-area pt-80px pb-80px position-relative">
  <div class="container">
    <div class="card-body row p-0">
        <div class="col-lg-6">
          <div class="hero-content"><br />
              <h2 class="section-title fs-50 pb-3 text-dark lh-65"> Join <a href="<?php echo TAOH_SITE_URL_ROOT; ?>">#<?php echo TAOH_SITE_NAME_SLUG; ?></a> Now!</h2>
              <p class="fs-30 lh-45 text-dark"><?php echo $app_data->desc; ?> <br /><strong class="text-primary">
								<?php
								if ( strtolower( $app_data->name_slug) != strtolower( TAOH_PLUGIN_PATH ) ){
									echo " <a href='".TAOH_SITE_URL_ROOT."/".$app_data->name_slug."'>#".$app_data->name_slug."</a> ";
								}
								?>
								<a href="<?php echo TAOH_SITE_URL_ROOT ; ?>">#<?php echo ucfirst(TAOH_SITE_TITLE); ?></a></strong></p>
          </div><!-- end hero-content -->
        </div><!-- end col-lg-8 -->

        <div class="col-lg-5 mx-auto">
							<div class="sidebar card px-3 py-5 p-lg-5" style="border-radius: 11px !important;">
								<div class="form-action-wrapper">
									<div class="form-card" style="text-align: center;">
									  <center>
                        <!--code is sent show verify screen -->
                        <div id="isCodeSent" style="display:none" >
    										     <h1 class="h3 mb-3 fw-normal">
                               Enter the code sent to:
                               <span id="emailSentTo"></span>
                             </h1>
    									        <div id="errorMessageEmail"></div>
    										     <div class="form-floating">
    											     <input type="text" class="form-control" id="accessCode" placeholder="Enter your code!" name="code" value="">
    											     <?php // <label for="floatingInput">Enter Code Here: </label> ?>&nbsp;
    										     </div>
                             <input type="hidden" class="login_type">
    										     <button onclick="submitCode()" class="w-100 btn btn-lg btn-primary">
                                 <span id="loadingTextEmail">Submit Code *</span>
                                 <img id="loaderEmail" width="20" src="<?php echo TAOH_LOADER_GIF; ?>">
                             </button>
                            <?php
                            // <a onclick="taoh_resend_code()" href="#">[RESEND]</a>**&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            ?>
                           <a onclick="taoh_reset()" href="#">[RESET]</a><br />
                           * Code sent to your email (it could take up to 10 min). <strong>Check SPAM or Promotions folder in your inbox.</strong>
                           <br /><br />
                           <p><strong>Not receiving the email? <a class="text-primary" href="#" onclick="sendEmailScreen()">Click Here</a></strong></p>
                            <br />
                        </div>

                         <!--user clicked issue with receving email will show below screen -->
                        <div id="sendEmail" style="display:none" >
                          <h2 class="h2 mb-3 fw-normal">Alternate Log in</h2>
                          <h6 class="h6 mb-3 fw-normal">1. Send the email to login@tao.ai</h6>

                            <p>From: <span id="fromEmail"><?php echo @$_COOKIE[ TAOH_ROOT_PATH_HASH.'_tao_api_email' ]; ?></span><br>
                              Subject: <a href="mailto:login@tao.ai?subject=<?php echo $get_send_email_code['output']; ?>"><b><?php echo $get_send_email_code['output']; ?></b></a><br /></p><br>
                              <h6 class="h6 mb-3 fw-normal">2. After 2-3 min click the verify button below</h6>
                              <div id="verifyError"></div>
                            <button onclick="verify_email()" class="w-100 btn btn-lg btn-primary">
                                <span>Verify</span>
                                <img id="loaderVerify" width="20" src="<?php echo TAOH_LOADER_GIF; ?>">
                            </button>
                        </div>

                        <!--Main login screen email with captcha-->
                        <div style="display:none" id="lockedCode" class="passcodePanel">
                            <div class="errorMessageLockCode"></div>
                            <h2 class="h2 mb-3 fw-normal">Lock Code</h2>
                            <p><?php echo $lock_text;?></p>
                            <div class="form-floating mb-3">
                              <input type="text" class="form-control form--control fs-14" id="lock_code" placeholder="Enter Passcode" maxlength="5"/>
                            </div>
                            <div class="col-12 row px-0 pl-0">
                            <div class="col-4 px-0 pr-2"></div>
                              <div class="col-4 px-0 pr-2">
                              <button onclick="passcodeSubmit()" id="pin_submit" onsubmit="passcodeSubmit()" class="fs-14 w-100 btn btn-lg btn-primary"> Submit
                                <!-- <span id="loadingText">Submit</span>-->
                              </button>
                              </div>
                            </div>
                        </div>

                        <div id="isCodeNotSent" style="display:none" >
                             <div class="errorMessage"></div>
    									       <div class="form-floating mb-3">
                                  <input type="email" class="form-control form--control fs-14" id="email" placeholder="Enter Your Email" name="we_email" value="">
                              </div>
                              <div class="form-floating text-left">
                                    <input type="hidden" id="weCode" name="we_code" value="<?php echo ( $code + strlen( TAOH_SITE_TITLE ) ); ?>">
                                    Type <img src="<?php echo TAOH_CDN_PREFIX."/captcha/img/".$code; ?>" height=20><br />
                                    <input id="captcha" type="text" class="form-control form--control fs-14" id="floatingMath" placeholder="Type captcha word here.." name="we_word">
                              </div><br>
                              <button onclick="emailSubmit()" onsubmit="emailSubmit()" data-login="login" class="w-100 btn btn-lg btn-primary">
                                <span id="loadingText"><strong>Log In</strong></span>
                              </button>
                              <hr class="border-top-gray">
                              <!--<button onclick="emailSubmit()" onsubmit="emailSubmit()" data-login="create" class="w-40 btn btn-lg btn-success">
                                <span>Create new account</span>
                              </button>-->
                              <?php if(TAOH_USE_SOCIAL_LOGIN) { ?>
                                  <a class="social_click"><img src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/images/google_login.png" width="200" /></a>
                                  <span class="social_loader"  style="margin-left: -20px;"></span>
                              <?php } ?>

                        </div>
    						</center>
    					</div><!-- end ad-card -->
				    </div>
    			</div><!-- end col-lg-5 -->
    		</div><!-- end row -->
    	</div><!-- end container -->
    	<div class="position-absolute top-0 left-0 w-100 h-100 z-index-n1">
    		<svg class="w-100 h-100" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
    			<path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" fill="#2d86eb" opacity="0.06"></path>
    		</svg>
    	</div>
</section>
<script src="https://cdn.jsdelivr.net/gh/mgalante/jquery.redirect@master/jquery.redirect.js"></script>

<script type="text/javascript">

  $ = jQuery;
    let loadingText = $("#loadingText");
    let loadingTextEmail = $("#loadingTextEmail");
    let loaderVerify = $("#loaderVerify");

    let loaderEmail = $("#loaderEmail");
    let isCodeSent = $("#isCodeSent");
    let isCodeNotSent = $("#isCodeNotSent");
    let emailSentTo = $("#emailSentTo");
    let fromEmail = $("#fromEmail");
    let sendEmail = $("#sendEmail");

    let retry = '<?php echo (isset($_COOKIE[TAOH_ROOT_PATH_HASH."_tao_login_try"]) ? $_COOKIE[TAOH_ROOT_PATH_HASH."_tao_login_try"] : 1); ?>';
	  let lock_pin = '<?php echo TAOH_LOGIC_LOCK_CODE; ?>';
    let enableLock ='<?php echo TAOH_LOGIC_LOCK_CODE ? 1 : 0 ?>';
    let show_only_lock = <?php echo (isset($_COOKIE[TAOH_ROOT_PATH_HASH."_enable_lock_screen"])  ? $_COOKIE[TAOH_ROOT_PATH_HASH."_enable_lock_screen"] : 0); ?>;


    emailSentTo.html("<?php echo @$_COOKIE[ TAOH_ROOT_PATH_HASH.'_tao_api_email' ]; ?>");
    <?php if(!@$_COOKIE[TAOH_ROOT_PATH_HASH.'_tao_api_email']) { ?>
      taoh_reset();
    <?php } ?>

    $("#email").change(function(){
      $(".errorMessage #errorEmail").remove();
    })

    $("#captcha").change(function(){
      $(".errorMessage #errorCaptcha").remove();
    })
    taoh_check_login_state_init();
    //alert('------------');
    function taoh_reset() {
      localStorage.removeItem('isCodeSent');
      taoh_check_login_state_init();
    }

    $('.social_click').click(function(){
        $(".social_loader").html('<img style="width:28px;" width="20" src="<?php echo TAOH_LOADER_GIF; ?>">');
        window.location.href = "<?php echo TAOH_SITE_URL_ROOT; ?>/social";
    });
    function taoh_check_login_state_init() {
        // Cache jQuery selectors
        const $sendEmail = $('#sendEmail');
        const $loaderVerify = $('#loaderVerify');
        const $loaderEmail = $('#loaderEmail');
        const $lockedCode = $('#lockedCode');
        const $isCodeSent = $('#isCodeSent');
        const $isCodeNotSent = $('#isCodeNotSent');
        const $loadingText = $('#loadingText');

        // Hide elements initially
        $sendEmail.hide();
        $loaderVerify.hide();
        $loaderEmail.hide();

        // Check if only lock screen should be shown
        if (show_only_lock) {
            enableLock = 1;
            $lockedCode.show();
            $isCodeSent.hide();
            $isCodeNotSent.hide();
            $loadingText.show();
        } else {
            // Show or hide elements based on local storage
            if (localStorage.getItem('isCodeSent')) {
                $isCodeSent.show();
                $isCodeNotSent.hide();
            } else {
                $isCodeSent.hide();
                $isCodeNotSent.show();
            }
        }
    }

    $(document).keypress(function (e) {
       var key = e.which;
       if(key == 13)  // the enter key code
        {
          if(localStorage.getItem('isCodeSent') === 'true') {
            console.log("submit code")
            submitCode();
          } else {
            console.log("submit email")
            emailSubmit();
          }
        }
      });

      function passcodeSubmit() {
          const $errorMessageLockCode = $(".errorMessageLockCode");
          const code = $('#lock_code').val();

          // Clear any previous error messages
          $errorMessageLockCode.html('');

          if (code === '' || code !== lock_pin) {
              // Display error message if the code is invalid
              $errorMessageLockCode.append('<span class="text-danger" id="errorCode">Invalid Code</span>');
          } else {
              // Prepare data for the AJAX request
              const data = {
                  'taoh_action': 'update_lock_status',
              };

              // Make AJAX request to update lock status
              jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
                  if (response.status === 1) {
                      // Hide error message and redirect on success
                      $errorMessageLockCode.hide();
                      $.redirect('<?php echo TAOH_SITE_URL_ROOT . '/login_fwd/home/lock'; ?>');
                  } else {
                      // Handle the error case
                      $errorMessageLockCode.append('<span class="text-danger" id="errorCode">Failed to update lock status</span>');
                  }
              }).fail(function() {
                  // Handle AJAX failure
                  $errorMessageLockCode.append('<span class="text-danger" id="errorCode">AJAX request failed</span>');
              });
          }
      }

     function submitCode() {
        // Cache jQuery selectors to avoid repeated DOM lookups
        let accessCode = $("#accessCode");
        let errorMessageEmail = $("#errorMessageEmail");
        let loaderEmail = $("#loaderEmail"); // Assuming loaderEmail selector is defined somewhere
        let loadingText = $("#loadingText"); // Assuming loadingText selector is defined somewhere
        let loadingTextEmail = $("#loadingTextEmail"); // Assuming loadingTextEmail selector is defined somewhere

        var login_type = $(".login_type").val();
        var retry = parseInt(localStorage.getItem('retry') || '0');

        const data = {
            'taoh_action': 'check_access_code',
            'id': accessCode.val(),
            'slug': '<?php echo $app_data?->slug ?? 'default-slug'; ?>',
            'app': '<?php echo $current_app ?? ''; ?>',
            'retry': ++retry,
            'login_type': login_type,
        };

        if (accessCode.val() !== "") {
            loaderEmail.show();
            loadingText.hide();

            $.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
                console.log(response);

                // Hide loader and show loading text as soon as possible
                loaderEmail.hide();
                loadingText.show();

                if (response == 0) {
                    if (errorMessageEmail.is(':empty')) {
                        errorMessageEmail.append('<span class="text-danger" id="errorCode">Invalid Code</span>');
                    }
                    return false;
                } else if (response == 2) {
                    enableLock = 1;
                    $("#lockedCode").show();
                    $("#isCodeSent").hide();
                    return false;
                } else if (response == 1) {
                    localStorage.removeItem('isCodeSent');
                    $.redirect('<?php echo TAOH_SITE_URL_ROOT.'/login_fwd/home'; ?>');
                }

                loadingTextEmail.show();
            });
        }
    }

    function validateEmail(email) {
      var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      return regex.test(email);
    }

    function emailSubmit() {
        console.log('--------');

        // Cache jQuery selectors to avoid repeated DOM lookups
        let analystID = $("button:focus").attr('data-login');
        let $loginType = $('.login_type');
        let $email = $("#email");
        let $captcha = $("#captcha");
        let $errorMessage = $(".errorMessage");
        let email = $email.val();
        let captcha = $captcha.val();
        let emailError = $("#errorEmail").text();
        let captchaError = $("#errorCaptcha").text();
        let $loadingText = $("#loadingText"); // Assuming loadingText selector is defined somewhere

        // Validate email and captcha
        if ((!email && emailError === "") || !validateEmail(email)) {
            if (emailError === "") {
                $errorMessage.append('<span class="text-danger" id="errorEmail">Email Required</span>');
            }
            return false;
        }

        if (!captcha && captchaError === "") {
            if (captchaError === "") {
                $errorMessage.append('<span class="text-danger" id="errorCaptcha">Captcha Required</span>');
            }
            return false;
        }

        // Set login type value
        $loginType.val(analystID);

        // Prepare data for AJAX request
        let weCode = $("#weCode").val();
        let data = {
            'taoh_action': 'check_captcha',
            'we_code': weCode,
            'we_word': captcha,
            'email': email,
            'slug': '<?php echo $app_data?->slug ?? 'default-slug'; ?>',
            'app': '<?php echo $current_app; ?>',
            'site_title': '<?php echo TAOH_SITE_TITLE; ?>',
        };

        if (email && captcha) {
            // Show loading text
            $loadingText.show();

            // Make AJAX request
            jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
                console.log('response', response);

                if (response.output != 1) {
                    // Show captcha error
                    if (captchaError === "") {
                        $errorMessage.html('<span class="text-danger" id="errorCaptcha">Captcha Invalid</span>');
                    }
                    $loadingText.show();
                    return false;
                } else {
                    // Handle successful response
                    let $emailSentTo = $("#emailSentTo"); // Assuming emailSentTo selector is defined somewhere
                    let $isCodeSent = $("#isCodeSent"); // Assuming isCodeSent selector is defined somewhere
                    let $isCodeNotSent = $("#isCodeNotSent"); // Assuming isCodeNotSent selector is defined somewhere

                    $emailSentTo.html(email);
                    localStorage.setItem('isCodeSent', true);
                    localStorage.setItem('email', email);
                    $isCodeSent.show();
                    $isCodeNotSent.hide();
                }
                $loadingText.show();
            });
        }
    }


    function sendEmailScreen() {
      fromEmail.html(localStorage.getItem('email'));
      isCodeSent.hide();
      sendEmail.show();

    }


    function verify_email() {
        const loaderVerify = $('#loaderVerify'); // Assuming you have an element with this ID for showing the loader
        const verifyError = $("#verifyError");

        const data = {
            'taoh_action': 'verify_email',
            'email_code': '<?php echo $get_send_email_code['output']; ?>',
            'email': localStorage.getItem('email'),
            'slug': '<?php echo $app_data?->slug ?? 'default-slug'; ?>'
        };

        loaderVerify.show();

        jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
            loaderVerify.hide();

            if (response.status == 1) {
                window.location.replace('<?php echo TAOH_SETTINGS_URL; ?>');
            } else {
                verifyError.html('<span class="text-danger" id="errorCode">Verification Failed! Send email again or wait for few minutes before retry.</span>');
            }
        }).fail(function() {
            loaderVerify.hide();
            verifyError.html('<span class="text-danger" id="errorCode">AJAX request failed. Please try again later.</span>');
        });
    }

</script>
<?php taoh_get_footer(); ?>
