<?php
if ( function_exists( 'taoh_user_all_info' ) ){
  $user_data = taoh_user_all_info();
  if ( !empty( $user_data->chat_name ) && !empty( $user_data->fname ) && !empty( $user_data->lname ) && !empty( $user_data->ptoken ) && !empty( $user_data->type ) ) {
    $url = TAOH_SITE_URL_ROOT.'?already=true';
    taoh_redirect($url);
    taoh_exit();
  }
}

taoh_get_header();
?>
<style>

body {
  background-color: #f1f1f1;
}

#regForm {
  background-color: #ffffff;
  margin: 100px auto;
  padding: 40px;
  width: 30%;
  min-width: 450px;
  border-radius:25px;
}

/* Mark input boxes that gets an error on validation: */
.invalid {
  background-color: #ffdddd;
}

/* Hide all steps by default: */
.tab {
  display: none;
}

/* Make circles that indicate the steps of the form: */
.step {
  height: 15px;
  width: 15px;
  margin: 0 2px;
  background-color: #bbbbbb;
  border: none;
  border-radius: 50%;
  display: inline-block;
  opacity: 0.5;
}

.step.active {
  opacity: 1;
}

/* Mark the steps that are finished and valid: */
.step.finish {
  background-color: #04AA6D;
}
.app-box {
    padding: 8pt;
    margin-left: 10px;
    text-align: center;
    box-shadow: 0px 5px 8px 0px rgba(105, 104, 104, 0.5);
    border-style: solid;
    border-color: skyblue;
    height: 215px;
}
.app-box .app-text{
  font-size: small;
  line-height: 18px;
}
#submitBtn,
#prevBtn,
#nextBtn {
  display: none;
}

</style>
<link rel="stylesheet" href="https://bug7a.github.io/iconselect.js/sample/css/lib/control/iconselect.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<form id="regForm" method="post" action="<?php echo TAOH_ACTION_URL .'/settings'; ?>">
<input name="email" type="hidden" value="<?php echo $_COOKIE[ 'tao_api_email' ]; ?>" class="form-control">
<input name="login_type" type="hidden" value="create" class="form-control">
  <div class="tab mb-3">
    <div class="d-flex justify-content-between">
      <div></div>
      <div>
      <a href="<?php echo TAOH_SITE_URL_ROOT."/settings?create=true"; ?>">
        <span style="font-size: 35px;color: black;" aria-hidden="true">&times;</span>
      </a>
      </div>
    </div>
    <hr class="mt-0">
    <img style="height: 100%;width: 460px;" src="<?php echo TAOH_SITE_URL_ROOT."/assets/images/login_img.jpeg" ?>"><br />
    <p class="text-black font-weight-bold">Ready for the next step?</p>
    <p class="text-black"><small>Create an account for tools to help you</small></p>
    <div class="radio mt-2">
      <label class="form-label text-black fw-medium m-0">My Profile Type <span style="color:red"> * </span></label>
      <div class="btn-group btn-group-toggle d-flex flex-column" data-toggle="buttons">
         <label class="btn btn-outline-primary my-1 rounded">
            <input type="radio" value="professional" name="type" id="button1" onclick="nextPrev(1)">
            Professional
         </label>
         <label class="btn btn-outline-primary my-1 rounded">
            <input type="radio" value="employer" name="type" id="button2" onclick="nextPrev(1)">
             Employer
         </label>
         <label class="btn btn-outline-primary my-1 rounded">
            <input type="radio" value="provider" name="type" id="button3" onclick="nextPrev(1)">
            Service Provider
         </label>
      </div>
   </div>
  </div>
  <div class="tab">
    <div class="d-flex justify-content-between">
      <div></div>
      <div>
      <a href="<?php echo TAOH_SITE_URL_ROOT."/settings?create=true"; ?>">
        <span style="font-size: 35px;color: black;" aria-hidden="true">&times;</span>
      </a>
      </div>
    </div>
    <hr class="mt-0">
    <div class="row">
        <p class="text-black pl-3 mb-3">Let's start with the basics. What's your name?</p>
        <div class="mb-3 col-md-12">
            <label class="form-label text-black fw-medium m-0 fs-13 lh-20">First Name <span style="color:red"> * </span></label>
            <?php echo field_fname(); ?>
        </div>
        <div class="mb-3 col-md-12">
            <label class="form-label text-black fw-medium m-0 fs-13 lh-20">Last Name <span style="color:red"> * </span></label>
            <?php echo field_lname(); ?>
        </div>
    </div>
  </div>
  <div class="tab">
    <div class="d-flex justify-content-between">
      <div></div>
      <div>
      <a href="<?php echo TAOH_SITE_URL_ROOT."/settings?create=true"; ?>">
        <span style="font-size: 35px;color: black;" aria-hidden="true">&times;</span>
      </a>
      </div>
    </div>
    <hr class="mt-0">
    <div class="row">
        <p class="text-black pl-3 mb-3">Share your public information</p>
        <div class="mb-3 col-md-12">
          <?php echo avatar_select(); ?>
          <label class="form-label text-black fw-medium fs-13 lh-20">My Avatar <span class="text-danger"> * </span></label>
          <span class="text-danger" id="avatar-error"></span>
        </div>
        <div class="mb-3 col-md-12">
            <label class="form-label text-black fw-medium m-0 fs-13 lh-20">My Public Chat Name <span style="color:red"> * </span></label>
            <input  class="form-control form--control required" type="text" name="chat_name">
        </div>
        <div class="mb-3 col-md-12">
          <label class="form-label text-black fw-medium m-0 fs-13 lh-20">My City( Only select from the suggested list ) <span style="color:red"> * </span></label>
          <?php echo field_location(); ?>
        </div>
        <div class="mb-3 col-md-12" style="display:none;">
          <label class="form-label text-black fw-medium m-0">Timezone</label>
          <?php echo field_time_zone(); ?>
        </div>
    </div>
  </div>
  <div class="tab">
    <div class="d-flex justify-content-between">
      <div></div>
      <div>
      <a href="<?php echo TAOH_SITE_URL_ROOT."/settings?create=true"; ?>">
        <span style="font-size: 35px;color: black;" aria-hidden="true">&times;</span>
      </a>
      </div>
    </div>
    <hr class="mt-0">
    <div class="row">
      <p class="text-black pl-3 mb-3">Tell Me About Yourself?</p>
      <div class="mb-3 col-md-12">
          <label class="form-label text-black fw-medium m-0 fs-13 lh-20">About Me <span style="color:red"> * </span></label>
          <textarea name="aboutme" rows="5" type="text" class="form-control required"></textarea>
      </div>
      <div class="mb-3 col-md-12">
          <label class="form-label text-black fw-medium m-0 fs-13 lh-20">Fun Fact (Great for ice-breakers)<span style="color:red"> * </span></label>
          <textarea name="funfact" rows="5" type="text" class="form-control required"></textarea>
      </div>
    </div>
  </div>

  <div class="tab">
    <div class="d-flex justify-content-between">
      <div></div>
      <div>
      <a href="<?php echo TAOH_SITE_URL_ROOT."/settings?create=true"; ?>">
        <span style="font-size: 35px;color: black;" aria-hidden="true">&times;</span>
      </a>
      </div>
    </div>
    <hr class="mt-0">
    <div class="row">
        <p class="text-black pl-3">Professional information</p>
        <div class="mb-3 col-md-12 mt-2">
          <label class="form-label text-black fw-medium m-0 fs-13 lh-20">My Core of Aspirational skills (Pick from suggested skill list for better results) <span style="color:red"> * </span></label>
          <?php echo field_skill(); ?>
        </div>
        <div class="mb-3 col-md-12">
          <label class="form-label text-black fw-medium m-0 fs-13 lh-20">Current or Last Job Role <span style="color:red"> * </span></label>
          <?php echo field_role(); ?>
        </div>
        <div class="mb-3 col-md-12">
          <label class="form-label text-black fw-medium m-0 fs-13 lh-20">Current or Last Company <span style="color:red"> * </span></label>
          <?php echo field_company(); ?>
        </div>
    </div>
  </div>
  <div style="overflow:auto;">
    <div style="float:left;">
      <button type="button" class="btn btn-danger btn-sm" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
    </div>
    <div style="float:right;">
      <button type="button" class="btn btn-primary btn-sm" id="nextBtn" onclick="nextPrev(1)">Continue</button>
      <button type="submit" class="btn btn-primary btn-sm" id="submitBtn">Submit</button>
    </div>
  </div>
  <!-- Circles which indicates the steps of the form: -->
  <div style="text-align:center;margin-top:40px;">
    <span class="step"></span>
    <span class="step"></span>
    <span class="step"></span>
    <span class="step"></span>
    <span class="step"></span>
  </div>
</form>
<script type='text/javascript' src="<?php echo TAOH_CDN_PREFIX ?>/assets/iconselect/iconselect.js"></script>
<script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
<script>

var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the current tab

function showTab(n) {
  // This function will display the specified tab of the form...
  var x = document.getElementsByClassName("tab");
  x[n].style.display = "block";
  //... and fix the Previous/Next buttons:
  if (n == (x.length - 1)) {
    // document.getElementById("nextBtn").innerHTML = "Submit";
    document.getElementById("submitBtn").style.display = "inline";
    document.getElementById("nextBtn").style.display = "none";
  } else {
    if (n == 0) {
      document.getElementById("submitBtn").style.display = "none";
      document.getElementById("prevBtn").style.display = "none";
      document.getElementById("nextBtn").style.display = "none";
    }else{
      document.getElementById("prevBtn").style.display = "inline";
      document.getElementById("nextBtn").style.display = "inline";
      document.getElementById("submitBtn").style.display = "none";
    }
  }
  //... and run a function that will display the correct step indicator:
  fixStepIndicator(n)
}

function nextPrev(n) {
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("tab");
  // Exit the function if any field in the current tab is invalid:
  if (n == 1 && !validateForm()) return false;
  // Hide the current tab:
  x[currentTab].style.display = "none";
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  // if you have reached the end of the form...
  if (currentTab >= x.length) {
    // ... the form gets submitted:
    // document.getElementById("regForm").submit();
    return false;
  }
  else{
    // Otherwise, display the correct tab:
    showTab(currentTab);
  }
}

function validateForm() {
  // This function deals with validation of the form fields
  var x, y, i, valid = true;
  x = document.getElementsByClassName("tab");
  y = x[currentTab].getElementsByClassName("required");
  // A loop that checks every input field in the current tab:
  for (i = 0; i < y.length; i++) {
    // If a field is empty...
    if (y[i].value == "") {
      // add an "invalid" class to the field:
      y[i].className += " invalid";
      // and set the current valid status to false
      valid = false;
    }
  }
  // If the valid status is true, mark the step as finished and valid:
  if (valid) {
    document.getElementsByClassName("step")[currentTab].className += " finish";
  }
  return valid; // return the valid status
}

function fixStepIndicator(n) {
  // This function removes the "active" class of all steps...
  var i, x = document.getElementsByClassName("step");
  for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" active", "");
  }
  //... and adds the "active" class on the current step:
  x[n].className += " active";
}
</script>

<?php taoh_get_footer(); ?>
