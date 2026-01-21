<?php 
if ( function_exists( 'taoh_user_all_info' ) ){
  $user_data = taoh_user_all_info();
  if ( !empty( $user_data->chat_name ) && !empty( $user_data->fname ) && !empty( $user_data->lname ) && !empty( $user_data->ptoken ) && !empty( $user_data->type ) ) {
    $url = TAOH_SITE_URL_ROOT;
    taoh_set_error_message( 'Account already exists. Re-directing you to home page.' );
    taoh_redirect($url); 
    taoh_exit();
  }    
}

taoh_get_header(); //die();
?>
<style>  

body {
    color: #000;
    overflow-x: hidden;
    height: 100%;
    /* background: linear-gradient(-45deg, #2196F3 50%, #EEEEEE 50%); */
    background-color: #EEEEEE;
    background-repeat: no-repeat
}

.card {
    background-color: #FFF;
    border-radius: 25px;
    box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2);
    padding: 40px;
    z-index: 0
}

.heading {
    font-weight: normal
}

.desc {
    font-size: 14px
}

#progressbar {
    margin-bottom: 30px;
    overflow: hidden;
    color: lightgrey;
    padding-left: 0px
}

#progressbar .active {
    color: #673AB7
}

#progressbar li {
    list-style-type: none;
    font-size: 15px;
    width: 20%;
    float: left;
    position: relative;
    font-weight: 400
}

#progressbar .step0:before {
    content: ""
}

#progressbar li:before {
    width: 40px;
    height: 40px;
    line-height: 45px;
    display: block;
    font-size: 20px;
    background: #E0E0E0;
    border-radius: 50%;
    margin: auto;
    padding: 2px
}

#progressbar li:after {
    content: '';
    width: 100%;
    height: 10px;
    background: #E0E0E0;
    position: absolute;
    left: 0;
    top: 17px;
    z-index: -1
}

#progressbar li:last-child:after {
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px
}

#progressbar li:first-child:after {
    border-top-left-radius: 10px;
    border-bottom-left-radius: 10px
}

#progressbar li.active:before,
#progressbar li.active:after {
    background: #F9A825
}

.sub-heading {
    font-weight: 500
}

.yellow-text {
    color: #F9A825
}

fieldset.show {
    display: block
}

fieldset {
    display: none
}

.radio {
    display: inline-block;
    border-radius: 0;
    box-sizing: border-box;
    cursor: pointer;
    color: #BDBDBD;
    font-weight: 500;
    -webkit-filter: grayscale(100%);
    -moz-filter: grayscale(100%);
    -o-filter: grayscale(100%);
    -ms-filter: grayscale(100%);
    filter: grayscale(100%)
}

.radio:hover {
    box-shadow: 1px 1px 2px 2px rgba(0, 0, 0, 0.1)
}

.radio.selected {
    border: 1px solid #F9A825;
    box-shadow: 0px 8px 16px 0px #EEEEEE;
    color: #29B6F6 !important;
    -webkit-filter: grayscale(0%);
    -moz-filter: grayscale(0%);
    -o-filter: grayscale(0%);
    -ms-filter: grayscale(0%);
    filter: grayscale(0%)
}

.card-block {
    border: 1px solid #CFD8DC;
    width: 45%;
    margin: 2.5%;
    padding: 20px 25px 15px 25px
}

@media screen and (max-width: 768px) {
    .card-block {
        padding: 20px 20px 0px 20px;
        height: 250px
    }
}

.icon {
    width: 85px;
    height: 100px
}

.image-icon {
    width: 85px;
    height: 100px;
    margin-left: auto;
    margin-right: auto;
    margin-bottom: 20px
}

select,
input,
textarea,
button {
    padding: 8px 15px 8px 15px;
    border-radius: 0px;
    margin-bottom: 25px;
    margin-top: 2px;
    width: 100%;
    box-sizing: border-box;
    color: #2C3E50;
    background-color: #ECEFF1;
    border: 1px solid #ccc;
    font-size: 16px;
    letter-spacing: 1px
}

select:focus,
input:focus,
textarea:focus {
    -moz-box-shadow: none !important;
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
    border: 1px solid skyblue !important;
    outline-width: 0
}

button:focus {
    -moz-box-shadow: none !important;
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
    outline-width: 0
}

textarea {
    height: 100px
}

button {
    width: 120px;
    letter-spacing: 2px
}

.fit-image {
    width: 100%;
    object-fit: cover
}

.btn-block {
    border-radius: 5px;
    height: 50px;
    font-weight: 500;
    cursor: pointer
}

.fa-long-arrow-right {
    float: right;
    margin-top: 4px
}

.fa-long-arrow-left {
    float: left;
    margin-top: 4px
}
.ts-control {
   height: 50px !important;
   line-height: 31px;
   font-size: 15px;
}
span.h5 {
    font-size: 14px !important;
}
span.highlight{
    color: #fff;
}
 
</style>
<?php
$email = '';
    if ( isset( $_COOKIE[ 'tao_api_email' ] ) && $_COOKIE[ 'tao_api_email' ] ){
       $email = $_COOKIE[ 'tao_api_email' ];
    } else {
       if ( isset( $_COOKIE[ 'email' ] ) && $_COOKIE[ 'email' ] ){
          $email = $_COOKIE[ 'email' ];
       }
    }

?>
<link rel="stylesheet" href="https://bug7a.github.io/iconselect.js/sample/css/lib/control/iconselect.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<div class="container-fluid px-1 py-5 mx-auto bg-radial-gradient-gray">
    <div class="row d-flex justify-content-center">
        <div class="col-xl-5 col-lg-6 col-md-7">
            <div class="card b-0" style="border-radius: 2.25rem;">
                <div class="d-flex justify-content-between">
                    <div></div>
                    <div>
                    <a style="text-decoration: none;" href="<?php echo TAOH_SITE_URL_ROOT."/settings"; ?>">
                        <span style="font-size: 35px;color: black;" aria-hidden="true">&times;</span>
                    </a>
                    </div>
                </div>
                <ul id="progressbar" class="text-center mt-2">
                    <li class="active step0" id="step1"></li>
                    <li class="step0" id="step2"></li>
                    <li class="step0" id="step3"></li>
                    <li class="step0" id="step4"></li>
                    <li class="step0" id="step5"></li>
                </ul>
                <form id="regForm" method="post" action="<?php echo TAOH_ACTION_URL .'/settings'; ?>">
                    <input name="email" type="hidden" value="<?php echo $email; ?>" class="form-control">
                    <input name="login_type" type="hidden" value="create" class="form-control">
                    <input type="hidden" name="profile_complete" id="profile_complete" value="1"/>
                    <fieldset class="show firstdiv">
                        <div class="form-card">
                            <img style="width: 100%;" src="<?php echo TAOH_SITE_URL_ROOT."/assets/images/login_img.jpg" ?>">
                            <span class="text-black font-weight-bold">Ready for the next step?</span>
                            <p class="text-black"><small>Create an account for tools to help you</small></p>
                            <div class="mt-2 w-100">
                                <label class="form-label text-black fw-medium m-0">My Profile Type <span class="text-danger"> * </span></label> <span class="text-danger" id="type-error"></span>
                                <div class="btn-group btn-group-toggle d-flex flex-column" data-toggle="buttons">
                                    <label class="btn btn-outline-primary my-1 rounded">
                                        <input type="radio" value="professional" name="type" onclick="validate0()" class="check">
                                        Professional
                                    </label>
                                    <label class="btn btn-outline-primary my-1 rounded">
                                        <input type="radio" value="employer" name="type" onclick="validate0()" class="check">
                                        Employer
                                    </label>
                                    <label class="btn btn-outline-primary my-1 rounded">
                                        <input type="radio" value="provider" name="type" onclick="validate0()" class="check">
                                        Service Provider
                                    </label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="seconddiv">
                        <div class="form-card">
                            <h5 class="sub-heading mb-4">Let's start with the basics. What's your name?</h5>
                            <label class="text-danger mb-3">* Required</label>
                            <?php echo field_fname(); ?>
                            <?php echo field_lname(); ?> 
                            <button id="next2" class="btn-block btn-primary mt-3 mb-1 next mt-4" data-next = "3" onclick="validate1(0)">NEXT<span class="fa fa-long-arrow-right"></span></button> <button class="btn-block btn-secondary mt-3 mb-1 prev" data-prev = "2"><span class="fa fa-long-arrow-left"></span>PREVIOUS</button>
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-card">
                            <h5 class="sub-heading mb-4">Share your public information</h5> 
                            <label class="text-danger mb-3">* Required</label>
                            <div class="form-group"> <label class="form-control-label">My Avatar : <span class="text-danger"> * </span></label> <span class="text-danger" id="avatar-error"></span><?php echo avatar_select(); ?></div>
                            <?php echo field_cname(); ?>
                            <div class="form-group"> <label class="form-control-label">My City( Only select from the suggested list ) :<span style="color:red"> * </span></label> <?php echo field_location(); ?> </div>
                            <div class="form-group" style="display:none;"> <label class="form-control-label">Timezone</label> <?php echo field_time_zone(); ?> </div>
                            <button id="next3" class="btn-block btn-primary mt-3 mb-1 next mt-4" data-next = "4" onclick="validate2(0)">NEXT<span class="fa fa-long-arrow-right"></span></button> <button class="btn-block btn-secondary mt-3 mb-1 prev" data-prev = "3"><span class="fa fa-long-arrow-left"></span>PREVIOUS</button>
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-card">
                            <h5 class="sub-heading mb-4">Tell Me About Yourself?</h5> 
                            <label class="text-danger mb-3">* Required</label>
                            <?php echo field_about_me(); ?>
                            <?php echo field_funfact(); ?> 
                            <button id="next4" class="btn-block btn-primary mt-3 mb-1 next mt-4" data-next = "5" onclick="validate3(0)">NEXT<span class="fa fa-long-arrow-right"></span></button> <button class="btn-block btn-secondary mt-3 mb-1 prev" data-prev = "4"><span class="fa fa-long-arrow-left"></span>PREVIOUS</button>
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-card">
                            <h5 class="sub-heading mb-4">Professional information</h5>
                            <div class="form-group"> <label class="form-control-label">My Core Skills (Pick from suggested skill list for better results) :<span style="color:red"> * </span></label> <?php echo field_skill(); ?> </div>
                            <div class="form-group"> <label class="form-control-label">Current or Last Job Role :<span style="color:red"> * </span></label> <?php echo field_role(); ?> </div>
                            <div class="form-group"> <label class="form-control-label">Current or Last Company :<span style="color:red"> * </span></label> <?php echo field_company(); ?> </div>
                            <div class="row ml-2">
                                <div class="custom-control custom-checkbox mt-2">
                                    <input <?php echo ( ! isset( $data->newsletter_subscribe ) ||  ( isset( $data->newsletter_subscribe ) && $data->newsletter_subscribe == "1" ) ) ?'checked': '';?> type="checkbox" name="newsletter" onclick="checkBox();" class="custom-control-input" id="defaultChecked2"/>
                                    <label class="custom-control-label fs-13 text-black lh-20 fw-medium" for="defaultChecked2" style="margin-right: 2.5rem;">Subscribe to Our Newsletter</label>
                                    <input type="hidden" class="check_val" name="newsletter_subscribe"/>
                                </div>
                                <div class="custom-control custom-checkbox mt-2">
                                    <input <?php echo ( ! isset( $data->email_sub ) ||  ( isset( $data->email_sub ) && $data->email_sub == "1" ) ) ?'checked': '';?> type="checkbox" name="email_sub" onclick="email_checkBox();" class="custom-control-input" id="defaultChecked3"/>
                                    <label class="custom-control-label fs-13 text-black lh-20 fw-medium" for="defaultChecked3">Subscribe to Important Emails</label>
                                    <input type="hidden" class="email_check_val" name="email_sub"/>
                                </div>
                            </div>
                            <button type="submit" class="btn-block btn-primary mt-3 mb-1 mt-4">SUBMIT<span class="fa fa-long-arrow-right"></span></button> <button class="btn-block btn-secondary mt-3 mb-1 prev" data-prev = "5"><span class="fa fa-long-arrow-left"></span>PREVIOUS</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
<script type='text/javascript' src="<?php echo TAOH_CDN_PREFIX ?>/assets/iconselect/iconselect.js"></script>
<script type='text/javascript' src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<script>
$(document).ready(function(){
    checkBox();
    email_checkBox();
})

function checkBox() {
    if ($('#defaultChecked2').is(":checked")){
        $('.check_val').val(1);
    }else{
        $('.check_val').val(0);
    }
}

function email_checkBox() {
    if ($('#defaultChecked3').is(":checked")){
        $('.email_check_val').val(1);
    }else{
        $('.email_check_val').val(0);
    }
}


function validate0() {
    var current_fs1, next_fs1;
    current_fs1 = $('.firstdiv');
    next_fs1 = $('.seconddiv');

    $(current_fs1).removeClass("show");
    $(next_fs1).addClass("show");
    
    $('#step2').addClass("active");
    //$("#progressbar li").eq($("fieldset").index(next_fs1)).addClass("active");

    current_fs1.animate({}, {
    step: function() {

    current_fs1.css({
    'display': 'none',
    'position': 'relative'
    });

    next_fs1.css({
    'display': 'block'
    });
    }
    });
}

function validate1(val) {
v1 = document.getElementById("fname");
v2 = document.getElementById("lname");

flag1 = true;
flag2 = true;

if(val>=1 || val==0) {
if(v1.value == "") {
v1.style.borderColor = "red";
flag1 = false;
}
else {
v1.style.borderColor = "green";
flag1 = true;
}
}

if(val>=2 || val==0) {
if(v2.value == "") {
v2.style.borderColor = "red";
flag2 = false;
}
else {
v2.style.borderColor = "green";
flag2 = true;
}
}



flag = flag1 && flag2;

return flag;
}

function validate2(val) {
v1 = document.getElementById("chat_name");
v3 = document.getElementById("coordinateLocation");
v4 = document.getElementsByClassName("ts-control");
v5 = $("input[name=avatar]").val();

flag1 = true;
flag2 = true;
flag3 = true;

if(val>=1 || val==0) {
if(v1.value == "") {
v1.style.borderColor = "red";
flag1 = false;
}
else {
v1.style.borderColor = "green";
flag1 = true;
}
}

if(val>=2 || val==0) {
if(v3.value == "") {
v4[0].style.borderColor = "red";
flag2 = false;
}
else {
v4[0].style.borderColor = "green";
flag2 = true;
}
}

if(val>=3 || val==0) {
if(v5 == "default") {
$('#avatar-error').html('Avatar is required');
$('#avatar-error').show();
flag3 = false;
}
else {
$('#avatar-error').html('');
$('#avatar-error').hide();
flag3 = true;
}
}

flag = flag1 && flag2 && flag3;

return flag;
}

function validate3(val) {
v1 = document.getElementById("aboutme");
v2 = document.getElementById("funfact");

flag1 = true;
flag2 = true;

if(val>=1 || val==0) {
if(v1.value == "") {
v1.style.borderColor = "red";
flag1 = false;
}
else {
v1.style.borderColor = "green";
flag1 = true;
}
}

if(val>=2 || val==0) {
if(v2.value == "") {
v2.style.borderColor = "red";
flag2 = false;
}
else {
v2.style.borderColor = "green";
flag2 = true;
}
}



flag = flag1 && flag2;

return flag;
}

$(document).ready(function(){

var current_fs, next_fs, previous_fs;

$(".next").click(function(){

var str2 = "next2";
var str3 = "next3";
var str4 = "next4";

if(!str2.localeCompare($(this).attr('id')) && validate1(0) == true) {
    var val2 = true;
}
else {
    var val2 = false;
}

if(!str3.localeCompare($(this).attr('id')) && validate2(0) == true) {
    var val3 = true;
}
else {
    var val3 = false;
}

if(!str4.localeCompare($(this).attr('id')) && validate3(0) == true) {
    var val4 = true;
}
else {
    var val4 = false;
}

if((!str2.localeCompare($(this).attr('id')) && val2 == true) || (!str3.localeCompare($(this).attr('id')) && val3 == true) || (!str4.localeCompare($(this).attr('id')) && val4 == true)) {
current_fs = $(this).parent().parent();
next_fs = $(this).parent().parent().next();

$(current_fs).removeClass("show");
$(next_fs).addClass("show");

var next_id = $(this).attr("data-next");
$('#step'+next_id).addClass("active");
//$("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

current_fs.animate({}, {
step: function() {

current_fs.css({
'display': 'none',
'position': 'relative'
});

next_fs.css({
'display': 'block'
});
}
});
}
});

$(".prev").click(function(){

current_fs = $(this).parent().parent();
previous_fs = $(this).parent().parent().prev();

$(current_fs).removeClass("show");
$(previous_fs).addClass("show");

var prev_id = $(this).attr("data-prev");
$('#step'+prev_id).removeClass("active");
//$("#progressbar li").eq($("fieldset").index(next_fs)).removeClass("active");

current_fs.animate({}, {
step: function() {

current_fs.css({
'display': 'none',
'position': 'relative'
});

previous_fs.css({
'display': 'block'
});
}
});
});

$('.radio-group .radio').click(function(){
$(this).toggleClass('selected');
});

});
</script>

<?php 
//die('----------------');
taoh_get_footer(); ?>
