<?php
taoh_add_var_to_url('noca', TAOH_MY_NOW_CODE);
taoh_get_header();
?>

<!-- <?php //if( taoh_parse_url(0) == 'settings' ){ ?>
<script>
    let index_name = "settings";
</script>
<?php //} ?> -->
<style>
    .ts-control {
        height: 50px !important;
        border-color: rgba(127, 136, 151, 0.2) !important;
        line-height: 31px;
        font-size: 15px;
    }
    span.h5 {
        font-size: 14px !important;
    }
    span.highlight{
        color: #fff;
    }
    .error{
        color:red;
    }
    .iti--allow-dropdown{
        width:100%;
    }
    .border_top{
        border-top:4px solid #ccc;
        padding-top:10px;
    }
    .profile_type label.error{
        top: 40px;
        position: absolute;
        width: 200px;
    }

    #keywords_information_blk .ts-dropdown [data-selectable].option.selected{
        color: #fff;
        background-color: #2d86eb;
    }
    /* .custom-control-label::after {
        left: -24px !important;
    } */
</style>
<style>
        .setting-sm-text {
            font-size: 14px;
            font-weight: 400;
            line-height: 16.09px;
            color: #000000;
        }
        .setting-md-text {
            font-size: 19px;
            font-weight: 400;
            line-height: 21.83px;
            color: #000000;
        }
        .setting-lg-text {
            font-size: clamp(21px, 2vw + 1rem, 30px);
            font-weight: 400;
            line-height: 1.149;
            color: #000000;
        }
        .setting-post-btn {
            width: 100%;
            max-width: 278px;
            font-size: clamp(19px, 0.5vw + 1rem, 21px);
            font-weight: 500;
            background-color: #000000;
            border: 2px solid #000000;
            color: #ffffff;
            border-radius: 12px;
        }
        .setting-post-btn:hover {
            background-color: #ffffff;
            color: #000000;
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
<?php
$indx_db_settings = 1;

$data = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];

$login_type='update';
if(!isset($data->login_type)) $login_type='first_update';
//print_r($data);die('--------');
if (!isset($data->email)) {
    if (isset($_COOKIE['tao_api_email']) && $_COOKIE['tao_api_email']) {
        $data->email = $_COOKIE['tao_api_email'];
    } else {
        if (isset($_COOKIE['email']) && $_COOKIE['email']) {
            $data->email = $_COOKIE['email'];
        }
    }
}
$location = str_replace(",", "-", @$data->full_location);

/* if ( isset( $data->country_name ) && $data->country_name != '' ) {
     $country_name = $data->country_name;
} else {
     $country_name = 'iti__us';
} */

$taoh_user_keywords = defined('TAOH_USER_KEYWORDS') ? getJsonDecodedData(TAOH_USER_KEYWORDS) : [];
$show_name_slug_information = !empty($taoh_user_keywords);

function showUnlistmeField($data)
{
    echo '<div class="col-lg-6 mb-2">';
    echo '<div class="mt-4 pt-2">';
    echo '<label class="fs-13 text-black lh-20 fw-medium mr-2">Remove me from Directory?</label>';
    echo field_yes_no('unlist_me_dir', $data->unlist_me_dir ?? 'no');
    echo '</div>';
    echo '</div>';
}
?>
<!-- Modal -->
<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div style="background:none; border:none" class="modal-content">
         <div class="modal-body p-0 ">
            <div class="user-panel-main-bar">
               <div class="user-panel">
                  <div class="delete-account-info card card-item border border-danger">
                     <div id="deleteAccountBody" class="card-body">
                        <h3 class="fs-22 text-danger fw-bold">Delete Account</h3>
                        <p class="pb-3 pt-2 lh-22 fs-15">Before confirming that you would like your profile deleted, we'd like to take a moment to explain the implications of deletion:</p>
                        <ul class="generic-list-item generic-list-item-bullet fs-15">
                           <li>Deletion is irreversible, and you will have no way to regain any of your original content, should this deletion be carried out and you change your mind later on.</li>
                           <li>Your questions and answers will remain on the site, but will be disassociated and anonymized (the author will be listed as "user15319675") and will not indicate your authorship even if you later return to the site.</li>
                        </ul>
                        <p class="pb-3 pt-2 lh-22 fs-15">Once you delete your account, there is no going back. Please be certain.</p>
                        <div class="custom-control custom-checkbox fs-15 mb-4">
                           <input type="checkbox" class="custom-control-input" id="delete-terms">
                           <label class="custom-control-label custom--control-label lh-22" for="delete-terms">I have read the information stated above and understand the implications of having my profile deleted. I wish to proceed with the deletion of my profile.</label>
                        </div>
                        <button onclick="deleteAccount()" type="button" class="btn btn-danger fw-medium" data-toggle="modal" data-target="#deleteModal" id="delete-button"><i class="la la-trash mr-1"></i> Delete your account</button>
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
<!-- <div class="modal fade" id="jobPostModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div style="background:none; border:none" class="modal-content">
         <div class="modal-body p-0 ">
            <div class="user-panel-main-bar">
               <div class="user-panel">
                  <div class="delete-account-info card card-item border border-danger">
                     <div id="deleteAccountBody" class="card-body text-center">
                        <h3 class="fs-22 text-success fw-bold text-center mb-4">Post a FREE job Today! <?php echo $data->type . $data->login_type; ?></h3>

                        <button id="postJobButton" type="button" class="btn btn-danger fw-medium text-center" > Post a Job</button>

                     </div>
                  </div>
               </div>

            </div>

         </div>
      </div>
   </div>
</div> -->

<section class="hero-area bg-white shadow-sm overflow-hidden pt-60px">
   <span class="stroke-shape stroke-shape-1"></span>
   <span class="stroke-shape stroke-shape-2"></span>
   <span class="stroke-shape stroke-shape-3"></span>
   <span class="stroke-shape stroke-shape-4"></span>
   <span class="stroke-shape stroke-shape-5"></span>
   <span class="stroke-shape stroke-shape-6"></span>
   <div class="container">
      <div class="row">
         <div class="col-lg-1">
            <div class="hero-content d-flex align-items-center ">
               <div class="icon-element shadow-sm flex-shrink-0 mr-3 border border-gray lh-55">
                  <svg xmlns="http://www.w3.org/2000/svg" height="32px" viewBox="0 0 24 24" width="32px" fill="#2d86eb">
                     <path d="M0 0h24v24H0V0z" fill="none"></path>
                     <path d="M19.43 12.98c.04-.32.07-.64.07-.98 0-.34-.03-.66-.07-.98l2.11-1.65c.19-.15.24-.42.12-.64l-2-3.46c-.09-.16-.26-.25-.44-.25-.06 0-.12.01-.17.03l-2.49 1c-.52-.4-1.08-.73-1.69-.98l-.38-2.65C14.46 2.18 14.25 2 14 2h-4c-.25 0-.46.18-.49.42l-.38 2.65c-.61.25-1.17.59-1.69.98l-2.49-1c-.06-.02-.12-.03-.18-.03-.17 0-.34.09-.43.25l-2 3.46c-.13.22-.07.49.12.64l2.11 1.65c-.04.32-.07.65-.07.98 0 .33.03.66.07.98l-2.11 1.65c-.19.15-.24.42-.12.64l2 3.46c.09.16.26.25.44.25.06 0 .12-.01.17-.03l2.49-1c.52.4 1.08.73 1.69.98l.38 2.65c.03.24.24.42.49.42h4c.25 0 .46-.18.49-.42l.38-2.65c.61-.25 1.17-.59 1.69-.98l2.49 1c.06.02.12.03.18.03.17 0 .34-.09.43-.25l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.65zm-1.98-1.71c.04.31.05.52.05.73 0 .21-.02.43-.05.73l-.14 1.13.89.7 1.08.84-.7 1.21-1.27-.51-1.04-.42-.9.68c-.43.32-.84.56-1.25.73l-1.06.43-.16 1.13-.2 1.35h-1.4l-.19-1.35-.16-1.13-1.06-.43c-.43-.18-.83-.41-1.23-.71l-.91-.7-1.06.43-1.27.51-.7-1.21 1.08-.84.89-.7-.14-1.13c-.03-.31-.05-.54-.05-.74s.02-.43.05-.73l.14-1.13-.89-.7-1.08-.84.7-1.21 1.27.51 1.04.42.9-.68c.43-.32.84-.56 1.25-.73l1.06-.43.16-1.13.2-1.35h1.39l.19 1.35.16 1.13 1.06.43c.43.18.83.41 1.23.71l.91.7 1.06-.43 1.27-.51.7 1.21-1.07.85-.89.7.14 1.13zM12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 6c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"></path>
                  </svg>
               </div>
            </div>
         </div>
         <div class="col-lg-7">
            <h2 class="section-title fs-30 mt-2">Settings</h2>
         </div>
         <div class="col-lg-4">
            <div class="hero-btn-box text-right py-3">
               <button type="button" class="btn btn-light text-secondary" data-toggle="modal" data-target="#delete-modal">
               Delete Account
               </button>
            </div>
         </div>
      </div>
      <!-- end row -->
      <ul class="nav nav-tabs generic-tabs generic--tabs generic--tabs-2 mt-4" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#personal-settings" role="tab" aria-controls="personal-control">Account Details</a>
        </li>
        <!--<li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#public-settings" role="tab" aria-controls="public-control">Public Information</a>
        </li>-->
         <li class="nav-item">
            <a class="nav-link"  data-toggle="tab" href="#email-settings" role="tab" aria-controls="emails-control">Email preferences</a>
        </li>
      </ul>
   </div>
   <!-- end container -->
</section>
<section class="user-details-area pt-40px pb-40px">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php
               if($indx_db_settings==1){ ?>
                  <form id="setting_form" method="post" action="#" class="pt-35px">
                  <input type="hidden" name="taoh_session" id="taoh_session" value="settings">
               <?php }else{ ?>
                  <form id="setting_form" method="post" action="<?php echo TAOH_ACTION_URL.'/settings' ?>" class="pt-35px">
                  <input type="hidden" name="taoh_session" id="taoh_session" value="old">
               <?php
               }
               ?>
                    <div class="tab-content mb-50px" id="myTabContent">
                        <div class="tab-pane fade show active" id="personal-settings" role="tabpanel" aria-labelledby="personal-settings-tab">
                            <div class="user-panel-main-bar">
                                <div class="user-panel">
                                    <div class="settings-item">
                                        <div class="row pt-4"></div>
                                        <div class="bg-dark p-3 rounded-rounded">
                                            <h3 class="fs-17 text-white">Personal Information </h3>
                                        </div>
                                        <div class="row pt-3">
                                            <div class="col-lg-6">
                                                <div class="input-box">
                                                    <label class="fs-13 text-black lh-20 fw-medium">First Name <span style="color:red"> * </span></label>
                                                    <?php
                                                    if(isset($data->profile_complete)
                                                    && $data->profile_complete == 0 && isset($data->fname) && $data->fname == 'TAOH_SITE_NAME_SLUG')
                                                    echo field_fname();
                                                    else
                                                    echo field_fname(@$data->fname); ?>
                                                </div>
                                            </div><!-- end col-lg-5 -->
                                            <div class="col-lg-6">
                                                <div class="input-box">
                                                    <label class="fs-13 text-black lh-20 fw-medium">Last Name <span style="color:red"> * </span></label>
                                                    <?php
                                                     if(isset($data->profile_complete)
                                                     && $data->profile_complete == 0 && isset($data->fname) && $data->fname == 'TAOH_SITE_NAME_SLUG')
                                                     echo field_lname();
                                                     else
                                                     echo field_lname(@$data->lname); ?>
                                                </div>
                                            </div><!-- end col-lg-5 -->
                                        </div><!-- end row -->
                                        <div class="row pt-1">
                                            <div class="col-lg-6">
                                                <div class="input-box">
                                                    <label class="fs-13 text-black lh-20 fw-medium">Email <span style="color:red"> * </span></label>
                                                    <?php
                                                        if(isset($data->email) && $data->email != '' ){
                                                            echo field_email(@$data->email,'');
                                                        }else{
                                                            echo field_email(@$data->email);
                                                        }
                                                    ?>
                                                </div>
                                            </div><!-- end col-lg-10 -->
                                            <div class="col-lg-6" style="display:none;">
                                                <div class="input-box">
                                                    <label class="fs-13 text-black lh-20 fw-medium">Contact Number (Enter number with country code)</label>
                                                    <div class="form-group">
                                                        <input  class="form-control form--control" id="phone" type="text" oninput="this.value = this.value.replace(/[^0-9+]/g, '');" value="<?php echo @$data->phone_number; ?>" name="phone_number">
                                                        <input type="hidden" value="<?php echo $country_name; ?>" class="country_name" name="country_name">
                                                        <p id="error-message"></p>
                                                    </div>
                                                </div>
                                            </div><!-- end col-lg-10 -->
                                        </div><!-- end row -->
                                        <div class="row edit-form-settings" style="display:none;">
                                            <div class="col-lg-6">
                                                <div class="input-box">
                                                    <label class="fs-13 text-black lh-20 fw-medium">Ethnicity/Race</label>
                                                    <?php echo field_race(@$data->race); ?>
                                                </div>
                                            </div><!-- end col-lg-10 -->
                                            <div class="col-lg-6">
                                                <div class="input-box">
                                                    <label class="fs-13 text-black lh-20 fw-medium">My Pronouns</label>
                                                    <div class="form-group">
                                                        <select  class="form-control form--control" id="pronoun" name="pronoun">
                                                            <option <?php echo (@$data->pronoun == "") ?'selected': '';?> disabled="disabled">Choose an option below</option>
                                                            <option <?php echo (@$data->pronoun == "she") ?'selected': '';?> value="she">She/her</option>
                                                            <option <?php echo (@$data->pronoun == "he") ?'selected': '';?> value="he">He/him</option>
                                                            <option <?php echo (@$data->pronoun == "them") ?'selected': '';?> value="them">They/them</option>
                                                            <option <?php echo (@$data->pronoun == "no") ?'selected': '';?> value="no">I prefer not to say</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><!-- end col-lg-10 -->
                                        </div><!-- end row -->
                                    </div><!-- end settings-item -->
                                    <!-- end col-lg-12 -->
                                </div>
                                <!-- end user-panel -->
                            </div>

                            <!--</div>
                            <div class="tab-pane fade" id="public-settings" role="tabpanel" aria-labelledby="public-settings-tab"> -->

                            <div class="user-panel-main-bar">
                                <div class="user-panel">
                                    <div class="settings-item">
                                        <div class="row pt-4"></div>
                                        <div class="bg-dark p-3 rounded-rounded">
                                            <h3 class="fs-17 text-white">Public Information</h3>
                                        </div>
                                        <div class="row pt-4">
                                            <div class="col-lg-4">
                                                <div class="input-box" id="move_avatar">
                                                    <div class="form-group">
                                                        <label class="fs-14 text-black lh-20 fw-medium mb-3">My Avatar <span class="text-danger"> * </span></label>
                                                        <span class="text-danger" id="avatar-error"></span>
                                                        <?php echo avatar_select(@$data->avatar); ?>
                                                    </div>
                                                </div>
                                                <div class="text-black lh-20"> OR </div>
                                                <?php if(TAOH_PROFILE_PICTURE_UPLOAD){ ?>
                                                    <div class="row pt-1">
                                                        <div class="col-lg-6">
                                                            <div class="input-box">
                                                                <label class="fs-14 text-black fw-medium">Upload Profile Picture</label>
                                                                <div class="custom-file mb-3">
                                                                    <input type="file" class="custom-file-input" id="custom_avatar" accept="image/*" name="">
                                                                    <label class="custom-file-label av_file" for="customFile">Choose file</label>
                                                                    <p id="av_error1" style="display:none; color:#FF0000;">
                                                                        Invalid Format! Format Must Be JPG,JPEG or PNG.
                                                                    </p>
                                                                    <input type="hidden" value="<?php echo (isset($data->avatar_image) && $data->avatar_image !='')?$data->avatar_image:'' ?>" class="avatar_image" name="avatar_image">
                                                                </div>
                                                            </div>
                                                            <input type="hidden" value="<?php echo TAOH_OPS_CODE; ?>" name="opscode">
                                                        </div><!-- end col-lg-6 -->
                                                        <div class="col-lg-6">
                                                            <div class="avatar-container">
                                                                <div class="avatar_settings">
                                                                    <?php if(isset($data->avatar_image) && $data->avatar_image !=''){

																			?>
                                                                        <img src="<?php echo $data->avatar_image; ?>" alt="Avatar">
                                                                        <div id="removeImage"  class="delete-icon"></div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div><!-- end row -->
                                                <?php } ?>
                                            </div><!-- end col-lg-4 -->
                                            <div class="col-lg-4">
                                                <div class="input-box">
                                                    <label class="fs-13 text-black lh-20 fw-medium">My Public Chat Name <span style="color:red"> * </span></label>
                                                    <div class="form-group">
                                                        <input  class="form-control form--control" required type="text"
                                                        value="<?php
                                                        if(isset($data->profile_complete)
                                                        && $data->profile_complete == 0 && isset($data->fname) && $data->fname == 'TAOH_SITE_NAME_SLUG')
                                                        echo '';
                                                        else
                                                        echo @$data->chat_name; ?>" name="chat_name" id="chat_name">
                                                    </div>
                                                </div>
                                            </div><!-- end col-lg-4 -->
                                            <div class="col-lg-4">
                                                <div class="input-box">
                                                    <div class="form-group">
                                                        <?php
                                                            if(isset($data->profile_complete)
                                                            && $data->profile_complete == 0 && isset($data->fname) && $data->fname == 'TAOH_SITE_NAME_SLUG'){
                                                                $data->type = '';
                                                            }
                                                        ?>
                                                        <label class="fs-14 text-black lh-20 fw-medium">My Profile Type <span style="color:red"> * </span></label><br>
                                                        <div class="profile_type btn-group btn--group btn-group-toggle" data-toggle="buttons">
                                                            <label class="btn ">
                                                                <input onclick="copyProfileType('professional');" <?php echo (@$data->type == "professional") ?'checked': '';?> type="radio"  name="type" value="professional" required> Professional
                                                            </label>
                                                            <label class="btn">
                                                                <input onclick="copyProfileType('employer');" <?php echo (@$data->type == "employer") ?'checked': '';?> type="radio"  name="type" value="employer" required> Employer
                                                            </label>
                                                            <label class="btn">
                                                                <input onclick="copyProfileType('provider');" <?php echo (@$data->type == "provider") ?'checked': '';?> type="radio"  name="type" value="provider"  required> Service Provider
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- end col-lg-4 -->
                                        </div><!-- end row -->
                                        <div class="row pt-1" style="display:none;">
                                            <div class="col-lg-12">
                                                <div class="input-box">
                                                    <label class="fs-13 text-black lh-20 fw-medium">About Me <span style="color:red"> * </span></label>
                                                    <div class="form-group">
                                                        <textarea  class="form-control" rows="4" maxlength="500" name="aboutme"><?php echo @$data->aboutme; ?> </textarea>
                                                    </div>
                                                </div>
                                            </div><!-- end col-lg-10 -->
                                        </div><!-- end row -->
                                        <div class="row pt-1" style="display:none;">
                                            <div class="col-lg-12">
                                                <div class="input-box">
                                                    <label class="fs-13 text-black lh-20 fw-medium">Fun Fact(Great for ice-breakers) <span style="color:red"> * </span></label>
                                                    <div class="form-group">
                                                        <textarea class="form-control" rows="4" maxlength="500" name="funfact" ><?php echo @$data->funfact; ?> </textarea>
                                                    </div>
                                                </div>
                                            </div><!-- end col-lg-10 -->
                                        </div><!-- end row -->
                                        <div class="row pt-1">
                                            <div class="col-lg-6">
                                                <div class="input-box">
                                                    <label class="fs-13 text-black lh-20 fw-medium">My City ( Only select from the suggested list ) <span style="color:red"> * </span></label>
                                                    <?php echo field_location(@$data->coordinates,@$data->full_location, @$data->geohash ,'', 1); ?>
                                                </div>
                                            </div><!-- end col-lg-5 -->
                                            <div class="col-lg-6">
                                                <div class="input-box">
                                                    <label class="fs-13 text-black lh-20 fw-medium">My Timezone <span style="color:red"> * </span></label>
                                                    <?php echo field_time_zone(@$data->local_timezone, 1,0); ?>
                                                </div>
                                            </div><!-- end col-lg-5 -->
                                        </div><!-- end row -->
                                        <div class="row pt-4">
                                            <div class="col-lg-6">
                                                <div class="input-box">
                                                    <label class="fs-13 text-black lh-20 fw-medium skills">My Core Skills ( Select from the suggested skill list for better results ) <span style="color:red"> * </span></label>
                                                    <?php echo field_skill( ( isset( $data->skill ) && $data->skill ) ? $data->skill:'' , 1); ?>
                                                </div>
                                            </div><!-- end col-lg-10 -->
                                            <div class="col-lg-6">
                                                <div class="input-box">
                                                    <label class="fs-13 text-black lh-20 fw-medium">Where to find me online? (public link e.g. LinkedIn)</label>
                                                    <div class="form-group">
                                                        <input class="form-control form--control" type="text" value="<?php echo @$data->mylink; ?>" name="mylink">
                                                    </div>
                                                </div>
                                            </div><!-- end col-lg-10 -->
                                        </div><!-- end row -->
                                        <div class="row pt-4">
                                            <div class="col-lg-6">
                                                <div class="input-box">
                                                    <label  class="fs-13 text-black lh-20 fw-medium">Current or Last Job Role <span style="color:red"> * </span></label>
                                                    <?php echo field_role( ( isset( $data->title ) && $data->title ) ? $data->title:'' , 1 ); ?>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="input-box">
                                                    <label class="fs-13 text-black lh-20 fw-medium">Current or Last Company <span style="color:red"> * </span></label>
                                                    <?php echo field_company( ( isset( $data->company ) && $data->company ) ? $data->company: '' , 1 ); ?>
                                                </div>
                                            </div><!-- end col-lg-10 -->
                                        </div><!-- end row -->
                                        <div class="row mt-3">
                                            <div class="col-lg-6">
                                                <div class="input-box">
                                                    <label  class="fs-13 text-black lh-20 fw-medium mr-2">Looking for job?</label>
                                                    <?php echo field_yes_no('look_job',@$data->look_job); ?>
                                                </div>
                                                <div class="pt-4 look_job_div" id="" style="<?php echo $data->look_job == 'yes'?'':'display:none;'?>">
                                                    <div class="row pt-1">
                                                        <div class="col-lg-12">
                                                            <div class="input-box">
                                                                <label class="fs-13 text-black lh-20 fw-medium">Role(s) pursuing (Select all that applies)</label>
                                                                <?php echo field_role_type_hire_job('job',$data->roletype_job); ?>
                                                            </div>
                                                        </div><!-- end col-lg-6 -->
                                                    </div><!-- end row -->
                                                    <div class="row pt-1">
                                                        <div class="col-lg-12">
                                                            <div class="input-box">
                                                                <label class="fs-13 text-black lh-20 fw-medium">Work Information (Select all applicable options)</label>
                                                                <?php echo field_flags_job_hire('job',$data->flags_job); ?>
                                                            </div>
                                                        </div><!-- end col-lg-6 -->
                                                    </div><!-- end row -->
                                                    <div class="row pt-1">
                                                        <div class="col-lg-12">
                                                            <div class="input-box">
                                                                <label class="fs-14 text-black fw-medium">Resume</label>
                                                                <div class="custom-file mb-3">
                                                                    <input type="file" class="custom-file-input" id="fileToUpload" name="">
                                                                    <label class="custom-file-label resume_file_name" for="customFile"><?php echo (isset($data->resume_name) && $data->resume_name !='')?$data->resume_name:'Choose File'; ?></label>
                                                                    <?php if(isset($data->resume_link) && $data->resume_link !=''){ ?>
                                                                        <div style="font-size:12px;">
                                                                            <a href="<?php echo $data->resume_link; ?>" target="_blank" class="text-primary" style="text-decoration: underline;" title="View Resume">View resume</a>&nbsp;&nbsp; | &nbsp;&nbsp;
                                                                            <a href="#" id="removeResume" class="text-danger" style="text-decoration: underline;" title="Delete Resume">Delete Resume</a>
                                                                        </div>
                                                                    <?php } ?>
                                                                    <p id="error1" style="display:none; color:#FF0000;">
                                                                        Invalid Format! Format Must Be PDF,DOC and DOCX.
                                                                    </p>
                                                                    <p id="error2" style="display:none; color:#FF0000;">
                                                                        Maximum File Size Limit is 5MB.
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" value="<?php echo TAOH_OPS_CODE; ?>" name="opscode">
                                                                <input type="hidden" value="<?php echo (isset($data->resume_link) && $data->resume_link !='')?$data->resume_link:''; ?>" name="resume_link" class="resume_link">
                                                                <input type="hidden" value="<?php echo (isset($data->resume_name) && $data->resume_name !='')?$data->resume_name:''; ?>" name="resume_name" class="resume_name">
                                                        </div><!-- end col-lg-6 -->
                                                    </div><!-- end row -->
                                                    <div id="responseMessage" style="display: none;"></div>
                                                </div>
                                            </div><!-- end col-lg-6 -->
                                            <div class="col-lg-6">
                                                <div class="input-box">
                                                    <label  class="fs-13 text-black lh-20 fw-medium mr-2">Looking to hire?</label>
                                                    <?php echo field_yes_no('hire_job',@$data->hire_job); ?>
                                                </div>
                                                <div class="pt-4 hire_job_div" id="" style="<?php echo $data->hire_job == 'yes'?'':'display:none;'?>">
                                                    <div class="row pt-1">
                                                        <div class="col-lg-12">
                                                            <div class="input-box">
                                                                <label class="fs-13 text-black lh-20 fw-medium">Role(s) pursuing (Select all that applies)</label>
                                                                <?php echo field_role_type_hire_job('hire',$data->roletype_hire); ?>
                                                            </div>
                                                        </div><!-- end col-lg-6 -->
                                                    </div><!-- end row -->
                                                    <div class="row pt-1">
                                                        <div class="col-lg-12">
                                                            <div class="input-box">
                                                                <label class="fs-13 text-black lh-20 fw-medium">Work Information (Select all applicable options)</label>
                                                                <?php echo field_flags_job_hire('hire',$data->flags_hire); ?>
                                                            </div>
                                                        </div><!-- end col-lg-6 -->
                                                    </div><!-- end row -->
                                                </div>
                                            </div><!-- end col-lg-6 -->

                                            <?php
                                            if (!$show_name_slug_information) {
                                                showUnlistmeField($data);
                                            }
                                            ?>

                                        </div><!-- end row -->
                                    <!--<div class="row pt-4">
                                            <div class="col-lg-3 mt-3">
                                                <div class="custom-control custom-checkbox">
                                                    <input <?php //echo (  ( isset( $data->is_scout_professional ) && $data->is_scout_professional == "1" ) ) ?'checked': '';?>
                                                    type="checkbox" name="is_scout_professional_check" onclick="checkProfessionalScoutBox();" class="custom-control-input" id="is_scout_professional_check"/>
                                                    <label class="custom-control-label fs-13 text-black lh-20 fw-medium" for="is_scout_professional_check">Scout - Professional</label>
                                                    <input type="hidden" class="scout_professional_check_val" name="is_scout_professional"/>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 mt-3">
                                                <div class="custom-control custom-checkbox">
                                                    <input <?php //echo (  ( isset( $data->is_scout_recruiter ) && $data->is_scout_recruiter == "1" ) ) ?'checked': '';?>
                                                    type="checkbox" name="is_scout_recruiter_check" onclick="checkRecruiterScoutBox();" class="custom-control-input" id="is_scout_recruiter_check"/>
                                                    <label class="custom-control-label fs-13 text-black lh-20 fw-medium" for="is_scout_recruiter_check">Scout - Recruiter</label>
                                                    <input type="hidden" class="scout_recruiter_check_val" name="is_scout_recruiter"/>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 mt-3">
                                                <div class="custom-control custom-checkbox">
                                                    <input <?php //echo (  ( isset( $data->status_scout ) && $data->status_scout == "1" ) ) ?'checked': '';?>
                                                    type="checkbox" name="is_scout_check" onclick="checkProfessionalScoutBox();" class="custom-control-input" id="is_scout_professional_check"/>
                                                    <label class="custom-control-label fs-13 text-black lh-20 fw-medium" for="is_scout_check">Scout</label>
                                                    <input type="hidden" class="scout_check_val" name="status_scout"/>
                                                </div>
                                            </div>
                                        </div> -->
                                    </div><!-- end settings-item -->
                                    <!-- end col-lg-12 -->
                                </div>
                                <!-- end user-panel -->
                            </div>

                            <?php
                            if ($show_name_slug_information){
                                ?>
                                <div class="user-panel-main-bar" id="keywords_information_blk">
                                    <div class="user-panel">
                                        <div class="settings-item">
                                            <div class="row pt-4"></div>
                                            <div class="bg-dark p-3 rounded-rounded">
                                                <h3 class="fs-17 text-white"><?php echo (defined('TAOH_WERTUAL_NAME_SLUG') ? ucfirst(TAOH_WERTUAL_NAME_SLUG) . ' ' : '') . 'Information' ?></h3>
                                            </div>
                                            <div class="row pt-3">
                                                <?php
                                                foreach ($taoh_user_keywords as $key => $value) {
                                                    if (isset($value['enable']) && $value['enable'] == 'true') {
                                                        $data_keywords = (array)($data->keywords ?? []);

                                                        echo '<div class="col-lg-6 mb-2">';
                                                        echo '<label for="select-' . $key . '" class="form-label fs-13 text-black lh-20 fw-medium">' . $value['label'];
                                                        if ($value['required'] == 'true') echo '<span style="color:red"> * </span>';
                                                        echo '</label>';
                                                        echo '<select name="' . $key . '" id="select-' . $key . '" class="form-select" autocomplete="off" ';
                                                        if ($value['required'] == 'true') echo 'required';
                                                        echo '>';
                                                        echo '<option value="">Select a ' . $value['label'] . '...</option>';
                                                        if (isset($data_keywords[$key])) {
                                                            echo '<option value="' . $data_keywords[$key] . '" selected>' . $data_keywords[$key] . '</option>';
                                                        }
                                                        echo '</select>';
                                                        echo '</div>';
                                                    }
                                                }

                                                showUnlistmeField($data);
                                                ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>

                        </div>

                        <div class="tab-pane fade" id="email-settings" role="tabpanel" aria-labelledby="email-settings-tab">
                            <div class="user-panel-main-bar">
                                <div class="user-panel">
                                <div class="settings-item mb-20px border-bottom border-bottom-gray pb-20px">
                                        <div class="input-box">
                                            <label class="fs-14 text-black lh-20 fw-medium mb-0">Unsubscribe</label>
                                            <span class="fs-13 d-block lh-18 pb-3">We get it—too many emails! Click the checkbox below to unsubscribe from promotional and reminder emails. You can always rejoin when you're ready!</span>
                                            <div class="form-group">
                                                <!-- <div class="btn-group btn--group btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn active">
                                                        <input <?php //echo (($data->tao_unsubscribe_emails == "0" ) ) ?'checked': '';?> type="radio" name="tao_unsubscribe_emails" value="0" class="custom-control-input"/> Off
                                                    </label>
                                                    <label class="btn">
                                                        <input <?php //echo ( ! isset( $data->tao_unsubscribe_emails ) ||  ( $data->tao_unsubscribe_emails == "1" ) ) ?'checked': '';?> type="radio" name="tao_unsubscribe_emails" value="1" class="custom-control-input"/> On
                                                    </label>
                                                </div> -->
                                                 <div class="custom-control custom-checkbox" ><input id="tao-unsubscribe" <?php echo (($data->tao_unsubscribe_emails == "1" ) ) ?'checked': '';?> value="<?php echo ($data->tao_unsubscribe_emails == '0') ? '0' : '1'; ?>" type="checkbox" name="tao_unsubscribe_emails" class="custom-control-input current_role_checkbox">
                                                 <label for="tao-unsubscribe" class="custom-control-label fs-13 text-black lh-20 fw-medium current_role_checklabel">
                                                Unsubscribe me
                                                </label></div>

                                                </div>
                                        </div>
                                    </div><!-- end settings-item -->
                                    <div class="settings-item mb-20px border-bottom border-bottom-gray pb-20px" style="display:none">
                                        <div class="input-box">
                                            <label class="fs-14 text-black lh-20 fw-medium mb-0">Important Platform Emails</label>
                                            <span class="fs-13 d-block lh-18 pb-3">Get event reminders, messages from people when you are offline etc.</span>
                                            <div class="form-group">
                                                <div class="btn-group btn--group btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn active">
                                                        <input <?php echo (($data->platform_emails == "0" ) ) ?'checked': '';?> type="radio" name="platform_emails" value="0" class="custom-control-input"/> Off
                                                    </label>
                                                    <label class="btn">
                                                        <input <?php echo ( ! isset( $data->platform_emails ) ||  ( $data->platform_emails == "1" ) ) ?'checked': '';?> type="radio" name="platform_emails" value="1" class="custom-control-input"/> On
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- end settings-item -->
                                    <div class="settings-item mb-20px border-bottom border-bottom-gray pb-20px" style="display:none">
                                        <div class="input-box">
                                            <label class="fs-14 text-black lh-20 fw-medium mb-0">Newsletter </label>
                                            <span class="fs-13 d-block lh-18 pb-3">Stay updated with the latest trends, tips, and jobs, events in career development.</span>
                                            <div class="form-group">
                                                <div class="btn-group btn--group btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn active">
                                                        <input <?php echo (($data->newsletter_subscribe == "0" ) ) ?'checked': '';?> type="radio" name="newsletter_subscribe" value="0" class="custom-control-input"/> Off
                                                    </label>
                                                    <label class="btn">
                                                        <input <?php echo ( ! isset( $data->newsletter_subscribe ) ||  ( $data->newsletter_subscribe == "1" ) ) ?'checked': '';?> type="radio" name="newsletter_subscribe" value="1" class="custom-control-input"/> On
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- end settings-item -->
                                    <div class="settings-item mb-20px border-bottom border-bottom-gray pb-20px" style="display:none">
                                        <div class="input-box">
                                            <label class="fs-14 text-black lh-20 fw-medium mb-0">Personalized Recommendations </label>
                                            <span class="fs-13 d-block lh-18 pb-3">Receive personalized career recommendations based on your profile.</span>
                                            <div class="form-group">
                                                <div class="btn-group btn--group btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn active">
                                                        <input <?php echo (($data->personalized_emails == "0" ) ) ?'checked': '';?> type="radio" name="personalized_emails" value="0" class="custom-control-input"/> Off
                                                    </label>
                                                    <label class="btn">
                                                        <input <?php echo ( ! isset( $data->personalized_emails ) ||  ( $data->personalized_emails == "1" ) ) ?'checked': '';?> type="radio" name="personalized_emails" value="1" class="custom-control-input"/> On
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- end settings-item -->
                                </div><!-- end user-panel -->
                            </div><!-- end user-panel-main-bar -->
                        </div>

                        <div class="mt-2">
                            <div class="submit-btn-box pt-3">

                                <input type="hidden" name="taoh_ptoken" value="<?php echo $data->ptoken; ?>">
                                <input type="hidden" name="profile_complete" id="profile_complete" value="1"/>
                                <input type="hidden" name="login_type" id="login_type" value="<?php echo $login_type; ?>"/>
                                <button id="save_changes" class="btn theme-btn setting_save" type="submit">Save</button>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- end row -->
    </div><!-- end container -->
</section>

<script type="text/javascript">
        function onclicksub(){
        var hiddenInput = document.getElementById("tao-unsubscribe");

            if (hiddenInput.checked) {
        this.value = "1";
        hiddenInput.value = "1"; // Set value to 1 when checked
        alert(hiddenInput.value);
    } else {
        //this.value = "0";  // Set value to 0 when unchecked
        hiddenInput.value = "0"; // Set value to 0 when unchecked
           alert(hiddenInput.value);
    }

};

    /*<!-- user keywords script -->*/
    let taoh_user_keywords = JSON.parse('<?php echo defined('TAOH_USER_KEYWORDS') ? TAOH_USER_KEYWORDS : '{}'; ?>');
    $(document).ready(function () {
        for(const[key, value] of Object.entries(taoh_user_keywords)){
            let allow_expand = ((value.allow_expand).toString() === 'true');

            if($('#select-' + key).length > 0 && (value.enable).toString() === 'true'){
                let select = new TomSelect('#select-' + key, {
                    create: allow_expand,
                    loadOnFocus: true,
                    openOnFocus: true,
                    sortField: {
                        field: "value",
                        direction: "asc"
                    },
                    labelField: 'value',
                    valueField: 'value',
                    searchField: ['value'],
                    createFilter: function (input) {
                        input = input.toLowerCase();
                        let specials = /[*|\":<>[\]{}`\\';@&$]/;
                        return !(input in this.options) && !specials.test(input);
                    },
                    load: function (query, callback) {
                        const var1FormData = new FormData();
                        var1FormData.append('taoh_action', 'taoh_get_keyword_opt');
                        var1FormData.append('query', query);
                        var1FormData.append('key', key);

                        fetch(_taoh_site_ajax_url, {
                            method: 'POST',
                            body: var1FormData
                        })
                            .then(response => response.json())
                            .then(jsonData => {
                                callback(jsonData.data);
                            }).catch(() => {
                            callback();
                        });
                    },
                    render: {
                        option: function (item, escape) {
                            return `<div class="py-2 d-flex">
                        <div class="mb-1">
                            <span class="h5">
                                ${escape(item.value)}
                            </span>
                        </div>
                    </div>`;
                        }
                    },
                    onOptionAdd: function (value, callback) {
                        if(allow_expand){
                            $.post(_taoh_site_ajax_url, {
                                'taoh_action': 'taoh_add_keyword_opt',
                                'keyword': value,
                                'key': key
                            }, function (response) {
                                console.log(response);
                            })
                        }
                    },
                    onFocus: function() {
                        const selectInstance = this;
                        if (!selectInstance.hasOptions) {
                            selectInstance.load('');
                        }
                    }
                });
            }
        }

    });
    <!-- /user keywords script -->


<?php if(isset($_GET['from']) && $_GET['from'] == 'dashboard') {    ?>

    taoh_set_error_message('complete your settings to fully use the platform.');

<?php } ?>
$(document).ready(function(){
    //phone_init();
    <?php if(!isset($data->avatar_image) || $data->avatar_image == ''){ ?>
        $('.avatar-container').hide();
    <?php } ?>
    //flag_set();
});

/* function flag_set(){
    var country_name = $('.country_name').val();
    var flag = $('.iti__flag').attr('class');
    var flag_class = flag.split(' ')[1];
    if(country_name != ''){
        $('.iti__flag').removeClass(flag_class);
        $('.iti__flag').addClass(country_name);
    }
}

function phone_init(){
    const phoneInputField = document.querySelector("#phone");
    const phoneInput = window.intlTelInput(phoneInputField, {
        utilsScript:
        "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
    });
} */

// Add event listener to validate on keyup
/* document.getElementById('phone').addEventListener('keyup', validatePhoneNumber);
function validatePhoneNumber() {
    const phoneInput = document.getElementById('phone').value;
    const errorMessage = document.getElementById('error-message');

    // Basic regex for a 10-digit phone number (adjust as needed)
    const phoneRegex = /^\d{10}$/;

    if (phoneRegex.test(phoneInput)) {
        errorMessage.textContent = "Valid phone number.";
        errorMessage.style.color = "green";
    } else {
        errorMessage.textContent = "Invalid phone number. Please enter a 10-digit number.";
        errorMessage.style.color = "red";
    }
} */

/* const phoneInputField = document.querySelector("#phone");
phoneInputField.addEventListener('countrychange', function() {
    const className = 'iti__flag'; // Class name of the selected flag element
    const elements = document.getElementsByClassName(className); // Get elements by class name
    if (elements.length > 0) {
      const element = elements[0]; // Access the first element with the class name
      const classList = element.classList; // Get the classList property of the element
      const country_name = classList[1]; // Access the second class name
      $('.country_name').val(country_name);
    } else {
      console.error('No elements found with the given class name');
      return [];
    }
}); */


$('#custom_avatar').change(function() {
    if($('#avt_img_delete').val() != ''){
        let send_array = {};
        send_array['opscode'] = '<?php echo TAOH_OPS_CODE; ?>';
        send_array['remove'] = encodeURIComponent($('#avt_img_delete').val());
        send_array['time'] = Date.now();
        fetch("<?php echo TAOH_CDN_PREFIX; ?>/cache/remove/file", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(send_array),
        })
        .then((response) => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then((data) => {
            if (data.success) {
                $('#avt_img_delete').val('');
            }
        })
        .catch((error) => {

        });
    }
    $('#custom_avatar').attr('name', 'fileToUpload');
    var av_file = this.files[0].name;
    $('.av_file').html(av_file);
    var avatar_formData = new FormData(document.getElementById("setting_form"));
    avatar_formData.append('time', Date.now());
    var avatar_file = this.files[0];
    if (avatar_file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('.avatar-container').show();
            $('.avatar_settings').html('<img src="' + e.target.result + '">');
        };
        reader.readAsDataURL(avatar_file);
    }
    fetch("<?php echo TAOH_CDN_PREFIX; ?>/cache/upload/now", {
        method: "POST",
        body: avatar_formData,
    })
    .then((response) => {
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }
        return response.json();
    })
    .then((data) => {
        if (data.success) {
            console.log(data.output);
            taoh_set_warning_message('Profile Picture Saving in process!!!');
            $('.avatar_image').val(data.output);
            //$('#custom_avatar').remove();
            $('#avatar-error').html('');
            $('input[name="avatar"]').val('');
            var data = {
                'taoh_session': $('#taoh_session').val(),
                'avatar_image' : $('.avatar_image').val(),
                'avatar' : $('input[name="avatar"]').val(),
                'taoh_ptoken' : $('input[name="taoh_ptoken"]').val(),
            };
            jQuery.post("<?php echo TAOH_ACTION_URL . '/settings'; ?>", data, function(response) {
                res = response;
                if(res){
                    taoh_set_success_message('Profile Picture has been Saved!!!');
                    $('#custom_avatar').attr('name', '');
                }
            }).fail(function() {
                console.log( "Network issue!" );
            })
        }
    })
    .catch((error) => {

    });
    $('#avatar').attr('name', 'avatar_img');
});

$('#removeImage').click(function(){
    $('#avt_img_delete').val($('.avatar_image').val());
    $('.avatar-container').hide();
    $('.avatar_image').val('');
    $('#custom_avatar').val('');
    //$('input[name="avatar"]').val('');
});

/* $('#removeImage').click(function(){
    let send_array = {};
    send_array['opscode'] = '<?php //echo TAOH_OPS_CODE; ?>';
    send_array['remove'] = encodeURIComponent($('.avatar_image').val());
    send_array['time'] = Date.now();
    fetch("<?php //echo TAOH_CDN_PREFIX; ?>/cache/remove/file", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(send_array),
    })
    .then((response) => {
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }
        return response.json();
    })
    .then((data) => {
        if (data.success) {
            taoh_set_warning_message('Profile Picture Removing in process!!!');
            console.log(data.output);
            $('.avatar-container').empty();
            $('.avatar_image').val('');
            $('#custom_avatar').val('');
            $('input[name="avatar"]').val('');
            var data = {
                'taoh_session': $('#taoh_session').val(),
                'avatar_image' : $('.avatar_image').val(),
                'avatar' : $('input[name="avatar"]').val(),
                'taoh_ptoken' : $('input[name="taoh_ptoken"]').val(),
            };
            jQuery.post("<?php //echo TAOH_ACTION_URL . '/settings'; ?>", data, function(response) {
                res = response;
                if(res){
                    taoh_set_success_message('Profile Picture has been Removed!!!');
                    setTimeout(function () {
                        window.location.reload();
                    }, 3000);
                }
            }).fail(function() {
                console.log( "Network issue!" );
            })
        }
    })
    .catch((error) => {

    });
}); */

$('input[type=radio][name=look_job]').change(function() {
    if (this.value == 'yes') {
        $('#fileToUpload').attr('name', 'fileToUpload');
        $('.look_job_div').show(1000);
    }
    else if (this.value == 'no') {
        $('.look_job_div').hide(1000);
    }
});

$('input[type=radio][name=hire_job]').change(function() {
    if (this.value == 'yes') {
        $('.hire_job_div').show(1000);
    }
    else if (this.value == 'no') {
        $('.hire_job_div').hide(1000);
    }
});

$('#fileToUpload').change(function() {
    $('#fileToUpload').attr('name', 'fileToUpload');
    var file = this.files[0].name;
    var type = this.files[0].name.split('.').pop();
    var size = this.files[0].size;
    console.log(type);
    if(size > 5242880){
        $('#error2').show();
        return false;
    }
    $('#error2').hide();
    if(type != 'pdf' && type != 'doc' && type != 'docx'){
        $('#error1').show();
        return false;
    }
    $('#error1').hide();
    $('.resume_file_name').html(file);
    $('.resume_name').val(file);
    var cdnformData = new FormData(document.querySelector('form'));
    fetch("<?php echo TAOH_CDN_PREFIX; ?>/cache/upload/now", {
        method: "POST",
        body: cdnformData,
    })
    .then((response) => {
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }
        return response.json();
    })
    .then((data) => {
        if (data.success) {
            var data_url = data.output;
            $('.resume_link').val(data_url);
            taoh_set_success_message('Resume Saved in server click on save button!!!');
            $('#fileToUpload').attr('name', '');
        } else {
            document.getElementById("responseMessage").style.color = "red";
            document.getElementById("responseMessage").innerHTML = "File upload failed: " + data.output;
        }
        document.getElementById("responseMessage").style.display = "block";
    })
    .catch((error) => {
        document.getElementById("responseMessage").style.color = "red";
        document.getElementById("responseMessage").innerHTML = "An error occurred: " + error.output;
        document.getElementById("responseMessage").style.display = "block";
    });
});

$('#removeResume').click(function(){
    let send_array = {};
    send_array['opscode'] = '<?php echo TAOH_OPS_CODE; ?>';
    send_array['remove'] = encodeURIComponent($('.resume_link').val());
    send_array['time'] = Date.now();
    fetch("<?php echo TAOH_CDN_PREFIX; ?>/cache/remove/file", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(send_array),
    })
    .then((response) => {
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }
        return response.json();
    })
    .then((data) => {
        if (data.success) {
            taoh_set_warning_message('Resume Removing in process!!!');
            console.log(data.output);
            $('.resume_link').val('');
            $('.resume_name').val('');
            $('#fileToUpload').val('');
            var data = {
                'taoh_session': $('#taoh_session').val(),
                'resume_link' : $('.resume_link').val(),
                'resume_name' : $('.resume_name').val(),
                'taoh_ptoken' : $('input[name="taoh_ptoken"]').val(),
            };
            jQuery.post("<?php echo TAOH_ACTION_URL . '/settings'; ?>", data, function(response) {
                res = response;
                if(res){
                    taoh_set_success_message('Resume has been Removed!!!');
                    setTimeout(function () {
                        window.location.reload();
                    }, 3000);
                }
            }).fail(function() {
                console.log( "Network issue!" );
            })
        }
    })
    .catch((error) => {

    });
});

let deleteAccountBody = $('#deleteAccountBody');
//Delete account
function deleteAccount() {
    var data = {
    'taoh_action': 'taoh_delete_account',
    };
    deleteAccountBody.html(`
    <h3 class="fs-22 text-danger fw-bold">Delete Request Initiated</h3>
    <h4 class="pb-3 pt-3 fs-20">Please Wait...</h4>
    `)

    jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
    //data = JSON.parse(response);
    deleteAccountBody.html(`
        <h3 class="fs-22 text-danger fw-bold">Delete Account</h3>
        <h4 class="pb-3 pt-3 fs-20">Delete request received, please check your inbox and follow the instruction</h4>
    `)
    localStorage.removeItem("isCodeSent");
    setTimeout(function() {
        deleteAccountBody.html(`
        <h3 class="fs-22 text-danger fw-bold">You are logging out</h3>
        <h4 class="pb-3 pt-3 fs-20">Delete request received, please check your inbox and follow the instruction</h4>
        `)
        window.location = "<?php echo TAOH_LOGOUT_URL; ?>";
    }, 2000)

    }).fail(function() {
        console.log( "Network issue!" );
        //comments.append("<p>Server Error!</p>");
    })
}



    /*function checkProfessionalScoutBox() {
        if ($('#is_scout_professional_check').is(":checked")){
            $('.scout_professional_check_val').val(1);
        }else{
            $('.scout_professional_check_val').val(0);
        }
    }

    function checkRecruiterScoutBox() {
        if ($('#is_scout_recruiter_check').is(":checked")){
            $('.scout_recruiter_check_val').val(1);
        }else{
            $('.scout_recruiter_check_val').val(0);
        }
    }*/
</script>
<script>

var userType = "<?php echo isset($data->type) ? $data->type : ''; ?>";
var loginType = "<?php echo $login_type; ?>";
//alert('------userType-----',userType)

$('document').ready(function() {

    $("#fname").keyup(function () {

        $('#chat_name').val($(this).val());
    });

    $("#setting_form").validate({
        rules: {
            fname: {required:true},
            lname: {required:true},
            email : {
                required : false,
                email : true
            },
            type:"required",
            chat_name:"required",
            //aboutme:"required",
            //funfact:"required",
        },
        messages: {
            fname: {required : "First Name is required"},
            lname: {required : "Last Name is required"},
            email:{
                url : "Please enter vaild email"
            },
            type:"Profile type is required",
            chat_name:"Chat name is required",
           // aboutme:"About Me is required",
           // funfact:"Fun Fact is required",
        },
        submitHandler: function (form) {
            var v5 = $("input[name=avatar]").val();
            var condition = v5 == 'default';
            <?php if(TAOH_PROFILE_PICTURE_UPLOAD){ ?>
                var v6 = $("input[name=avatar_image]").val();
                condition = condition && v6 == '';
            <?php } ?>
            //console.log('v5',v5+'------ v6'+v6);
            //console.log('condition',condition);
            if(condition) {
                // Scroll to the element with the ID 'myElement'
                document.getElementById('move_avatar').scrollIntoView({ behavior: 'smooth', block: 'center' });

                $('#avatar-error').html('Profile Picture is required');
                $('#avatar-error').show();
            }
            else {
                $('#avatar-error').html('');
                $('#avatar-error').hide();
                $("#save_changes").html('<img style="width:28px;" width="20" src="<?php echo TAOH_LOADER_GIF; ?>"> While saving in progress, you can continue to explore other pages.');
                $("#save_changes").attr('disabled', true);
                var indx_db_settings = <?php echo $indx_db_settings; ?>;

               if(indx_db_settings==1){
                  indxform_submit(form);
               }else{
                  form.submit();
               }
               return false;
            }
        }
    });
    function createSession(formData,e){
        e.preventDefault();
        var skill_text={};var roleSelect ={};var companySelect ={};
        // var formData = new FormData(form);
        var skill_selected = $("#skillSelect option:selected").map(function () {
            return $(this).val();
        }).get();
        $.each(skill_selected, function(index, value) {
            skill_text[index] = value;
        });

        console.log('-------skill_selected---------',skill_text);
        $("#roleSelect option:selected").map(function () {
            id = $(this).val();
            roleSelect[id] = $(this).attr("data-slug")+":>"+$(this).text();
        });
        $("#companySelect option:selected").map(function () {
            id = $(this).val();
            companySelect[id] = $(this).attr("data-slug")+":>"+$(this).text();
        });

         formData['skill:skill'] = skill_text;
        /* formData['title'] = roleSelect;
        formData['company'] = companySelect; */

        $.ajax({
            url: '<?php echo taoh_site_ajax_url(); ?>',
            type: 'POST',
            data: formData,
            success: function(response) {
                console.log(response);
            },
        });
    }

    function indxform_submit(form){

        //alert(userType);
        let user_keywords_field_names = Object.keys(taoh_user_keywords);

        taoh_set_warning_message('Saving changes are in progress!!!');
        var formData = new FormData(form);
        formData.append('taoh_action','taoh_indb_session');

        if($('input[name="tao_unsubscribe_emails"]').is(':checked')){
            formData.append('tao_unsubscribe_emails','1');
        }else{
            formData.append('tao_unsubscribe_emails','0');
        }

        // Instead of directly appending fileToUpload.files[0], let's check if it exists and if it's a File object.
        var fileInput = document.getElementById('fileToUpload'); // Assuming your file input has id 'fileToUpload'
        if (fileInput.files.length > 0 && fileInput.files[0] instanceof File) {
            formData.append('fileToUpload', fileInput.files[0]);
        }

        // user data
        // Convert FormData to object
        var settingsdata = {};
        settingsdata.keywords = {};
        formData.forEach(function (value, key) {
            if (user_keywords_field_names.includes(key)) {
                settingsdata.keywords[key] = value;
            }

            if (key == "skill:skill[]" || key == "title:title[]" || key == "company:company[]") {
                if (!settingsdata.hasOwnProperty(key)) {
                    settingsdata[key] = {};
                }
                settingsdata[key] = value;
            } else if (key == "fileToUpload") {
                settingsdata[key] = $("#fileToUpload").val().split('\\').pop();
            } else {
                settingsdata[key] = value;
            }
        });

        console.log('-------settingsdata---------',settingsdata);

        let setting_time = new Date();
        setting_time = setting_time.setMinutes(setting_time.getMinutes() + 5);

        IntaoDB.setItem(APIStore,{ taoh_api: index_name,value:'1' });
        IntaoDB.setItem(dataStore, { taoh_data:index_name,values : settingsdata });
        IntaoDB.setItem(TTLStore, { taoh_ttl: index_name,time:setting_time });
        $('#global_settings').val(1);
        console.log('-------loginType---------',loginType);
        console.log('-------userType---------',userType);

        if (loginType == "first_update" && userType == "employer") {
            localStorage.setItem('show_jobPostModal', 1);
        }

        createSession(settingsdata,event);
       // alert(loginType);
       // alert(userType);
        // After the form submission, show the modal using Bootstrap's modal method
        // Only show the modal if the user is a "recruiter"
        // After submitting the form, check again if the conditions are met to show the modal

        return false;
    }

});

function copyProfileType(type){
   // alert(type);
    userType = type;
  //  alert(userType)
}
</script>
<?php taoh_get_footer(); ?>