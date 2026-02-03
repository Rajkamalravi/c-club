<?php
//error_reporting(E_ALL);

if(isset($_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'locked']) && $_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'locked'] == 1) {
  // do nothing
}
else{
  if ( taoh_user_is_logged_in() ){
    //echo "===========";die();
    $url = TAOH_SITE_URL_ROOT.'?uslo=1';
      taoh_redirect($url); 
      taoh_exit();
  }
}
$_SESSION = array();
if ( ! defined ( 'TAOH_SITE_MAIN_TITLE' ) ) define ( 'TAOH_SITE_MAIN_TITLE', 'Elevate Your Career with '.TAOH_SITE_NAME_SLUG.'!' );
if($login == 1) {
    taoh_get_header(); 
}

$current_app = taoh_parse_url(1) ? taoh_parse_url(1) : TAOH_WERTUAL_SLUG;
//echo "====================".$current_app;

$app_data = taoh_app_info($current_app);
$code = json_decode( taoh_url_get_content(TAOH_SITE_CAPTCHA), true );
$get_send_email_code = json_decode( taoh_url_get_content(TAOH_OPS_PREFIX.'/scripts/code.php?getkey=1'), true );

$lock_code_required = TAOH_LOGIC_LOCK_CODE != 0 && TAOH_LOGIC_LOCK_CODE != 'no' ? 1 : 0;

$lock_code = $lock_code_required;
$lock_text = TAOH_LOGIC_LOCK_TEXT;


?>

<div class="bg-white log-in " style="position: relative; z-index: 1;">
    <div class="container py-2 px-0 px-sm-3 <?php if($login == 1) { echo 'py-5';}?>">
        <div class="login-container px-2 px-lg-0" style="<?php if($login == 1) { echo 'max-width: 1000px';}?>">
        <?php if($login == 1) { ?>
            <div class="left-container py-4 border-lg-right mx-auto" style="width: 100%; max-width: 480px;">
                <div id="carouselExampleControls" class="carousel slide" data-ride="carousel" data-interval="5000" aria-label="Image carousel with different slides">
                    <div class="carousel-inner px-lg-5">
                       
                        <div class="carousel-item active">
                            <img class="d-block w-100" src="<?php echo TAOH_SITE_URL_ROOT . '/assets/images/login_c_3.png';?>" alt="Fourth slide" style="object-fit: contain;">
                            <h5 class="text-center text-sm-title my-4">Join the Club ! Meet New People ! Expand your Professional Network !</h5>   
                            <div class="card d-none d-block px-4 py-3 shadow-card mt-3" >
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2" style="gap: 12px;">
                                        <?php if(SUPERADMIN_AVATAR_IMG !='') { ?>
                                            <img class="round-img" src="<?php echo TAOH_OPS_PREFIX.'/avatar/PNG/128/avatar0'.rand(10,60).'.png';?>" width="40" alt="">
                                        <?php } else if(SUPERADMIN_AVATAR !='') { ?>
                                            <img class="round-img" src="<?php echo TAOH_OPS_PREFIX.'/avatar/PNG/128/avatar_0'.rand(10,60).'.png';?>" alt="">
                                        <?php } else { ?>
                                            <img class="round-img" src="<?php echo TAOH_OPS_PREFIX."/avatar/PNG/128/avatar_0".rand(10,60).".png" ;?>" alt="">
                                        <?php } ?>
                                        
                                        <div>
                                            <p class="text-md-normal mb-1"><?php echo TAOH_SITE_NAME_SLUG.' Admin'?></p>
                                            <p class="text-sm-sub">&nbsp;</p>
                                        </div>
                                    </div>
                                    <p class="text-sm-desc"><?php echo 'Elevate yourself with '.TAOH_SITE_NAME_SLUG;?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--<div class="carousel-indicators-container">
                        <ol class="carousel-indicators">
                            <li data-target="#carouselExampleControls" data-slide-to="0" class="active" aria-label="Slide 1"></li>
                            <li data-target="#carouselExampleControls" data-slide-to="1" aria-label="Slide 2"></li>
                            <li data-target="#carouselExampleControls" data-slide-to="2" aria-label="Slide 3"></li>
                            <li data-target="#carouselExampleControls" data-slide-to="3" aria-label="Slide 4"></li>
                        </ol>
                    </div>-->
                </div>
            </div>
        <?php } ?>
            <div class="right-container px-3 mx-auto py-4 px-lg-4" style="width: 100%; max-width: 720px; background-color: #fff;">
                <div class="mx-auto" style="max-width: 550px;">
                    <!-- login form -->
                    <div class="d-block">
                         <div id="isCodeNotSent" style="display:none" >
                            <h3 class="text-title-medium ">Welcome! Your next step awaits !</h3>
                            <p class="text-xs-normal my-3" style="font-size:10px;">By signing up or logging in, you agree to Tao.ai's (on behalf of <?php echo TAOH_SITE_NAME_SLUG;?>) <a href="https://tao.ai/terms.php" class="text-underline">Terms and Policies</a>. 
                            You may receive marketing emails but can opt out anytime. 
                            You might need to complete your profile to access all features. 
                            Tao.ai's (on behalf of <?php echo TAOH_SITE_NAME_SLUG;?>) Terms and Policies may be updated from time to time.</p>
                        
                            <!-- <div id="social_section">
                                <?php if(TAOH_USE_SOCIAL_LOGIN) { ?>
                                    
                                    <a class="social_click"><button type="button" class="btn ggl-btn w-100"><img src="<?php echo TAOH_SITE_URL_ROOT . '/assets/images/ggl.png';?>" alt="">
                                    <span>Continue with Google</span> </button></a>   

                                    <span class="social_loader d-flex justify-content-center mt-1"  style="margin-left: 0px;"></span>

                                <?php } ?>
                                <p class="text-center text-dark" style="font-size: 16px; font-weight: 400;">Or</p>
                            </div> -->
                             <?php //if(isset($_GET['social_test'])) {  ?>
                            <div id="social_section1">
                                <?php if(TAOH_USE_SOCIAL_LOGIN) { ?>
                                    
                                    <a class="social_click1"><button type="button" class="btn ggl-btn w-100"><img src="<?php echo TAOH_SITE_URL_ROOT . '/assets/images/ggl.png';?>" alt="">
                                    <span>Continue with Google</span> </button></a>   

                                    <span class="social_loader d-flex justify-content-center mt-1"  style="margin-left: 0px;"></span>

                                <?php } ?>
                                <p class="text-center text-dark" style="font-size: 16px; font-weight: 400;">Or</p>
                            </div>
                            <?php //} ?>
                           
                                <div class="errorMessage"></div>
                                <div class="form-group">
                                    <label for="" class="text-label-md mb-1">Enter your email to continue <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="email"  name="we_email" placeholder="Enter your email">
                                </div>
                                <div class="form-group mb-0" >
                                    
                                    <!--<input type="hidden" id="weCode" name="we_code" value="<?php echo ( $code + strlen( TAOH_SITE_TITLE ) ); ?>">
                                    <label for="" class="text-label-md">Type
                                         <img src="<?php //echo TAOH_CDN_PREFIX."/captcha/img/".$code; ?>" height=20><br />
                                    </label>
                                    <input id="captcha" type="text" class="form-control form--control fs-14" id="floatingMath" placeholder="Type captcha word here.." name="we_word">
                                    -->
                                    <input type="hidden" id="weCode" name="we_code" value="157">
                                     <input id="captcha" type="hidden" id="floatingMath"
                                     value="CYCLE" name="we_word">

                                     <label for="" class="text-label-md  mb-1">Let us know you're human<span class="text-danger">*</span></label>
                                     <div style="align-items: center;  background-color: #fafafa;  border: 1px solid #e0e0e0;
                                        box-sizing: border-box;
                                        display: flex;
                                        gap: 7px;
                                        height: 45px;
                                        user-select: none;">
                                     <br><input onclick="checkHumanCheckbox();" type="checkbox" id="human" name="human" value="human"> 
                                     <label class="mb-0" for="human" id="verify_label" style="font-size: 16px;">Verify you're human</label>
                                     </div>
                                </div>
                                <div class="mt-3 d-flex align-items-center" style="gap: 12px;">
                                    <img id="loadInitial" width="40" src="<?php echo TAOH_LOADER_GIF; ?>">
                                    
                                    <button id="step1_button" disabled="true" style="display:none;width:90px;" onclick="emailSubmit()" onsubmit="emailSubmit()"  type="button" class="btn con-btn">
                                        <span id="loadingText"><strong>Next</strong></span></button>
                                </div>
                            </div>

                             <!--user clicked issue with receving email will show below screen -->
                            <div id="sendEmail" style="display:none" >
                                <h2 class="h2 mb-3 fw-normal">Alternate Log in</h2>
                                <h6 class="h6 mb-3 fw-normal">1. Send the email to login@tao.ai</h6>
                                
                                <p>From: <span id="fromEmail"><?php echo @$_COOKIE[ TAOH_ROOT_PATH_HASH.'_tao_api_email' ]; ?></span><br>
                                Subject: <a href="mailto:login@tao.ai?subject=<?php echo $get_send_email_code['output'] ?? ''; ?>"><b><?php echo $get_send_email_code['output'] ?? ''; ?></b></a><br /></p><br>
                                <h6 class="h6 mb-3 fw-normal">2. After 2-3 min click the verify button below</h6>
                                <div id="verifyError"></div>
                                <button onclick="verify_email()" class="w-100 btn btn-lg btn-primary">
                                    <span>Verify</span>
                                    <img id="loaderVerify" width="20" src="<?php echo TAOH_LOADER_GIF; ?>">
                                </button>
                            </div>

                            <!-- lock code-->
                            <div style="display:none" id="lockedCode" class="passcodePanel">
                                <div class="errorMessageLockCode"></div>
                                <h2 class="h2 mb-3 fw-normal">Lock Code</h2>
                                <p><?php echo $lock_text;?></p>
                                <div class="form-floating mb-3">
                                <input type="text" class="form-control form--control fs-14" id="lock_code" placeholder="Enter Passcode" maxlength="50"/>
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

                            <!-- verify code -->
                            <div id="isCodeSent" style="display:none;" >
                                <div>
                                        <a href="#" onclick="taoh_reset();" class="btn d-inline-flex align-items-center" style="gap: 9px;">
                                            <svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0.292903 5.29402C-0.0976345 5.6845 -0.0976345 6.31863 0.292903 6.7091L5.29179 11.7071C5.68233 12.0976 6.31656 12.0976 6.7071 11.7071C7.09763 11.3167 7.09763 10.6825 6.7071 10.2921L2.4143 6L6.70397 1.70793C7.09451 1.31745 7.09451 0.683327 6.70397 0.292854C6.31344 -0.0976181 5.6792 -0.0976181 5.28866 0.292854L0.289779 5.2909L0.292903 5.29402Z" fill="black"/>
                                            </svg>
                                            <span>Go Back</span>
                                        </a>
                                        <div class="text-center pb-3">
                                            <img class="loginmailpng" src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/loginmail.png'; ?>" alt="">
                                            <h3 class="text-title-medium mt-3" style="font-weight: 400; font-size: 16px;">
                                                We have sent an email to <strong><span id="emailSentTo" ></span></strong>, Please use the <strong>Direct link</strong> present in the email to login into the site
                                            </h3>
                                        </div>
                                        
                                        <!--<div id="errorMessageEmail"></div>
                                        <div class="form-floating my-3">
                                        <input type="text" class="form-control" id="accessCode" placeholder="Enter the Verification code you received in your email" name="code" value="">
                                        </div>
                                        <input type="hidden" class="login_type">
                                        <button onclick="submitCode()" class="btn con-btn mt-3">Verify and Proceed
                                        <img id="loaderEmail" width="20" src="<?php echo TAOH_LOADER_GIF; ?>">
                                        </button>-->
                                </div>

                                <!-- <div class="mt-4 pt-lg-5 mb-lg-5 pb-lg-5">
                                    <p class="text-xs-normal">The code may take up to 10 minutes to arrive. If you don't see it, <span style="font-weight: 700;">check your Spam or Promotions folder.</span></p>
                                    <p class="text-label-md mt-3" style="color: #000000;">Didn't Receive the email? <a href="#" onclick="sendEmailScreen()" class="text-underline" style="color: #007BFF; font-weight: 700;">Click here</a> 
                                    or <a href="#" onclick="changeEmail();" class="text-underline" style="color: #007BFF; font-weight: 700;">Change Email</a></p>
                                </div> -->
                            </div>
                       
                    </div>
                     <!--<p class="mt-5 pt-4 text-center text-lg-right text-label-md" style="color: #444444;">Having trouble? Reach us <a href="" 
                     class="text-underline">here.</a></p>-->
                </div>
            </div>
        </div>
    </div>
</div>
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
    let social_section = $("#social_section1");

    let retry = '<?php echo (isset($_COOKIE[TAOH_ROOT_PATH_HASH."_tao_login_try"]) ? $_COOKIE[TAOH_ROOT_PATH_HASH."_tao_login_try"] : 1); ?>';
	  let lock_pin = '<?php echo TAOH_LOGIC_LOCK_CODE; ?>';
    let enableLock ='<?php echo $lock_code_required ? 1 : 0 ?>';
    let show_only_lock = <?php echo (isset($_COOKIE[TAOH_ROOT_PATH_HASH."_enable_lock_screen"])  ? $_COOKIE[TAOH_ROOT_PATH_HASH."_enable_lock_screen"] : 0); ?>;
    

    emailSentTo.html("<?php echo @$_COOKIE[ TAOH_ROOT_PATH_HASH.'_tao_api_email' ]; ?>");
    
    var limi_rand = (Math.floor(Math.random() * (7-3)) + 3 ) * 1000;
    //alert(limi_rand);
    setTimeout(function() {
        $('#step1_button').show();  
        $('#loadInitial').hide();  
              
      }, limi_rand);

    <?php if(!@$_COOKIE[TAOH_ROOT_PATH_HASH.'_tao_api_email']) { ?>
      taoh_reset();
        isCodeSent.hide();
        isCodeNotSent.show();
        social_section.show();
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
      localStorage.removeItem('email');
      taoh_check_login_state_init();
    }

    $('.social_click').click(function(){
        $(".social_loader").html('<img style="width:50px;" src="<?php echo TAOH_LOADER_GIF; ?>">');
        window.location.href = "<?php echo TAOH_SITE_URL_ROOT; ?>/social";
    });

    $('.social_click1').click(function(){
        $(".social_loader").html('<img style="width:50px;" src="<?php echo TAOH_LOADER_GIF; ?>">');
        localStorage.removeItem('isCodeSent');
        localStorage.removeItem('email');

        openGoogleLoginPopup();
    });

    function openGoogleLoginPopup() {
        // Build popup URL for dash OAuth
        var site_secret = "<?php echo TAOH_API_SECRET; ?>";
        var site_lock_code = "<?php echo TAOH_LOGIC_LOCK_CODE; ?>";

        var returnUrl = encodeURIComponent("<?php echo TAOH_SITE_URL_ROOT; ?>/new_login?social_callback=1");
        //alert(returnUrl);
        var popupUrl = "<?php echo TAOH_DASH_PREFIX; ?>/login-social?sub_secret_token=<?php echo TAOH_SITE_ROOT_HASH; ?>&popup=1&siteLockCode=<?php echo TAOH_LOGIC_LOCK_CODE; ?>&siteSecret=<?php echo TAOH_API_SECRET; ?>&source=<?php echo TAOH_SITE_URL_ROOT; ?>&return_url=" + returnUrl;
        //alert(popupUrl);
        // Open popup window
        var popup = window.open(
            popupUrl,
            'googleLogin',
            'width=500,height=650,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no'
        );

        // Center the popup
        if (popup) {
            var left = (screen.width - 500) / 2;
            var top = (screen.height - 650) / 2;
            popup.moveTo(left, top);
        }

        // Handle popup completion
        handlePopupCompletion(popup);
    }

    function handlePopupCompletion(popup) {
        var checkInterval = setInterval(function() {
            try {
                // Check if popup is closed
                if (popup.closed) {
                    clearInterval(checkInterval);
                    $(".social_loader").html('');

                    // Check if login was successful
                    setTimeout(function() {
                        checkLoginStatus();
                    }, 1000);
                    return;
                }

                // Try to detect successful login redirect
                try {
                    var popupUrl = popup.location.href;
                    //alert(popupUrl);
                    if ((popupUrl.indexOf('social_callback=1') > -1) || popupUrl.indexOf('login_success') > -1) {
                        clearInterval(checkInterval);
                        popup.close();
                        $(".social_loader").html('');

                        // Login successful - reload page
                        setTimeout(function() {
                            window.location.reload();
                        }, 500);
                        return;
                    }
                } catch (e) {
                    // Cross-origin access blocked during OAuth flow - this is normal
                }
            } catch (e) {
                // Handle errors gracefully
            }
        }, 1000);

        // Fallback timeout (5 minutes)
        setTimeout(function() {
            if (!popup.closed) {
                clearInterval(checkInterval);
                popup.close();
                $(".social_loader").html('');
                showLoginFallback();
            }
        }, 300000);
    }

    function checkLoginStatus() {
        // Simple reload to check login state
        window.location.reload();
    }

    function showLoginFallback() {
        // Show fallback option for redirect-based login
        if (confirm('Login popup was closed. Would you like to continue with redirect-based login?')) {
            window.location.href = "<?php echo TAOH_SITE_URL_ROOT; ?>/social";
        } else {
            $(".social_loader").html('');
        }
    }
    function checkHumanCheckbox(){
        if($('#human').is(':checked')){
            $('#human').val(1);

            $('#step1_button').animate({
            width: '200px'
            }, 2000, function() {        
                $('#step1_button').attr({'disabled': false});
                
                $('#step1_button').html('<span id="loadingText"><strong>Submit</strong></span>');
                $('#verify_label').css('color', '#007BFF');
            
            });
            
        }else{
            $('#human').val(0);
            $('#step1_button').attr({'disabled': true});
            $('#step1_button').html('<span id="loadingText"><strong>Next</strong></span>');
        }
    }
    function taoh_check_login_state_init_old() {
        // Cache jQuery selectors
        const $sendEmail = $('#sendEmail');
        const $loaderVerify = $('#loaderVerify');
        const $loaderEmail = $('#loaderEmail');
        const $lockedCode = $('#lockedCode');
        const $isCodeSent = $('#isCodeSent');
        const $isCodeNotSent = $('#isCodeNotSent');
        const $loadingText = $('#loadingText');
        const $social_section = $('#social_section1');

        // Hide elements initially
        $sendEmail.hide();
        $loaderVerify.hide();
        $loaderEmail.hide();

        // Check if only lock screen should be shown
        if (show_only_lock) {
            enableLock = 1;
            $lockedCode.show();
            $isCodeSent.hide();
            $social_section.hide();
            $isCodeNotSent.hide();
            $loadingText.show();
        } else {
            // Show or hide elements based on local storage
            if(localStorage.getItem('isCodeSent') === 'true') {
                console.log('-------i ma here to check login issue-----------')
                $isCodeSent.show();
                $isCodeNotSent.hide();
                $social_section.hide();
            } else {
                $isCodeSent.hide();
                $isCodeNotSent.show();
                $social_section.show();
            }
        }
    }

    function taoh_check_login_state_init() {
      // Cache jQuery selectors
      const $sendEmail = $('#sendEmail');
      const $loaderVerify = $('#loaderVerify');
      const $loaderEmail = $('#loaderEmail');
      const $lockedCode = $('#lockedCode');
      const $isCodeSent = $('#isCodeSent');
      const $isCodeNotSent = $('#isCodeNotSent');
      const $loadingText = $('#loadingText');
      const $social_section = $('#social_section1');

      // Hide elements initially
      $sendEmail.hide();
      $loaderVerify.hide();
      $loaderEmail.hide();

      // Initialize localStorage if not set
      if (localStorage.getItem('isCodeSent') === null) {
          localStorage.setItem('isCodeSent', 'false');
      }

      // Check if only lock screen should be shown
      if (show_only_lock) {
          enableLock = 1;
          $lockedCode.show();
          $isCodeSent.hide();
          $social_section.hide();
          $isCodeNotSent.hide();
          $loadingText.show();
      } else {
          // Show or hide elements based on local storage
          if(localStorage.getItem('isCodeSent') === 'true') {
              console.log('Code already sent, showing verification screen');
              $isCodeSent.show();
              $isCodeNotSent.hide();
              $social_section.hide();
          } else {
              console.log('Initializing login form');
              $isCodeSent.hide();
              $isCodeNotSent.show();
              $social_section.show();
              // Ensure button is properly initialized
              /* setTimeout(function() {
                  initializeSubmitButton();
              }, 100); */
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
        else{
            $('#errorMessageEmail').html('<span class="text-danger" id="errorCode">Code Required</span>');
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
        let $social_section = $("#social_section1");

        
        // Validate email and captcha
        if ((!email && emailError === "") || !validateEmail(email)) {
            if (emailError === "") {
                $errorMessage.append('<span class="text-danger" id="errorEmail">Email Required or Invalid</span>');
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
    //     $slug = isset($app_data->slug) && !empty($app_data->slug) && preg_match('/^[a-zA-Z0-9\-]+$/', $app_data->slug)
    // ? $app_data->slug 
    // : 'default-slug';
    // $slug = isset($app_data->slug) && is_string($app_data->slug) ? trim($app_data->slug) : '';

// if (empty($slug) || !preg_match('/^[a-zA-Z0-9-]+$/', $slug)) {
//     $slug = 'default-slug';
// }

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
            $('#loadInitial').show();  
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
                    // Force reload after a short delay to ensure cookies are set
                    setTimeout(function() {
                        window.location.reload();
                    }, 500);
                    $isCodeSent.show();
                    $isCodeNotSent.hide();
                    $social_section.hide();
                }
                $loadingText.show();
                $('#loadInitial').hide();
            });
        }
    }

    
    function sendEmailScreen() {
      fromEmail.html(localStorage.getItem('email'));
      isCodeSent.hide();
      social_section.hide();
      sendEmail.show();
      
    }

    function changeEmail(){
        isCodeNotSent.show();
        isCodeSent.hide();
        sendEmail.hide();
        social_section.show();
        localStorage.removeItem('isCodeSent');
        taoh_check_login_state_init();
    }


    function verify_email() {
        const loaderVerify = $('#loaderVerify'); // Assuming you have an element with this ID for showing the loader
        const verifyError = $("#verifyError");

        const data = {
            'taoh_action': 'verify_email',
            'email_code': '<?php echo $get_send_email_code['output'] ?? ''; ?>',
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
    // Listen for messages from popup
    window.addEventListener('message', function(event) {
       
        // Verify origin for security
       /*  if (event.origin !== '<?php //echo TAOH_DASH_PREFIX; ?>') {
            return;
        }
 */
       /*  if (event.data.type === 'OAUTH_SUCCESS') {
            // Login successful
            $(".social_loader").html('');
            localStorage.setItem('email',event.data.email )
            const data = {
                'taoh_action': 'social_login_success',
                'output': event.data.token,
                'success': 1               
            };
            jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {               
                if (response.status == 1) {
                    window.location.replace('<?php echo TAOH_SETTINGS_URL; ?>');
                } 
            }).fail(function() {
                alert('login failed');
                window.location.reload();
            });
            setTimeout(function() {
                window.location.reload();
            }, 500);
        }  */
        
        if (event.data.type === 'OAUTH_SUCCESS') {
            // Login successful
            $(".social_loader").html('');
            localStorage.setItem('email', event.data.email);

            const data = {
                'taoh_action': 'social_login_success',
                'output': event.data.token,
                'email': event.data.email,
                'success': 1
            };

            jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
                if (response.status == 1) {
                    // Add delay to ensure cookies are set before redirect
                    setTimeout(function() {
                       // window.location.replace('<?php //echo TAOH_SETTINGS_URL; ?>');
                        window.location.reload();
                    }, 800);
                } else {
                    alert('Login failed. Please try again.');
                    window.location.reload();
                }
            }).fail(function() {
                alert('Login failed');
                window.location.reload();
            });
            // Removed the competing setTimeout reload
        }       
        else if (event.data.type === 'OAUTH_ERROR') {
            // Login failed
            $(".social_loader").html('');
            alert('Login failed. Please try again.');
            window.location.reload();
        }
    }, false);


</script>
<?php
if($login == 1) {
    taoh_get_footer();
}

?>
