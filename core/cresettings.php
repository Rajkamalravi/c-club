<?php taoh_get_header(); ?>
<link rel="stylesheet" href="https://bug7a.github.io/iconselect.js/sample/css/lib/control/iconselect.css">
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
</style>
<?php 
   $google_details = $_POST['google_details'];
   $email = (isset($google_details['email']))?($google_details['email']):$_COOKIE[ 'tao_api_email' ];
   $fname = (isset($google_details['given_name']))?($google_details['given_name']):'';
   $lname = (isset($google_details['family_name']))?($google_details['family_name']):'';
   $cname = $fname;
   //print_r($google_details);
?>
<?php
    $token = taoh_get_api_token();
    $data = taoh_user_all_info_settings($token);
   //print_r($data);
   if ( ! isset( $data->email ) ) {
      if ( isset( $_COOKIE[ 'tao_api_email' ] ) && $_COOKIE[ 'tao_api_email' ] ){
         $data->email = $_COOKIE[ 'tao_api_email' ];
      } else {
         if ( isset( $_COOKIE[ 'email' ] ) && $_COOKIE[ 'email' ] ){
            $data->email = $_COOKIE[ 'email' ];
         }
      }
   }
   $location = str_replace(",", "-", @$data->full_location);
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
            <a class="nav-link" data-toggle="tab" href="#general" role="tab" aria-controls="edit-profile" >My Settings</a>
         </li>
         <!-- <li class="nav-item">
            <a class="nav-link about"  data-toggle="tab" href="#about" role="tab" aria-controls="change-password" >2. Public Info</a>
            </li> -->
      </ul>
   </div>
   <!-- end container -->
</section>
<section class="user-details-area pt-40px pb-40px">
   <div class="container">
      <div class="row">
         <div class="col-lg-12">
            <form method="post" class="pt-35px" action="<?php echo TAOH_ACTION_URL .'/settings'; ?>">
               <div class="tab-content mb-50px" id="myTabContent">
                  <div class="tab-pane fade" id="general" role="tabpanel" aria-labelledby="general-tab">
                     <div class="user-panel-main-bar">
                        <div class="user-panel">
                           <div class="settings-item">
                              <div class="row pt-4"></div>
                              <div class="bg-dark p-3 rounded-rounded">
                                 <h3 class="fs-17 text-white">Private Information (We will use this information to provide you better products and it will not be shared with others) </h3>
                              </div>
                              <div class="row pt-4">
                                 <div class="col-lg-4">
                                    <div class="input-box">
                                       <label class="fs-13 text-black lh-20 fw-medium">First Name <span style="color:red"> * </span></label>
                                       <?php echo field_fname(@$data->fname); ?>
                                    </div>
                                 </div>
                                 <!-- end col-lg-4 -->
                                 <div class="col-lg-4">
                                    <div class="input-box">
                                       <label class="fs-13 text-black lh-20 fw-medium">Last Name <span style="color:red"> * </span></label>
                                       <?php echo field_lname(@$data->lname); ?>
                                    </div>
                                 </div>
                                 <!-- end col-lg-4 -->
                                 <div class="col-lg-4">
                                    <div class="input-box">
                                       <label class="fs-13 text-black lh-20 fw-medium">Email <span style="color:red"> * </span></label>
                                       <?php echo field_email(@$data->email); ?>
                                    </div>
                                 </div>
                                 <!-- end col-lg-4 -->
                                 <!-- end col-lg-6 -->
                                 <div class="col-lg-4">
                                    <div class="input-box">
                                       <label class="fs-13 text-black lh-20 fw-medium">Ethnicity/Race</label>
                                       <?php echo field_race(@$data->race); ?>
                                    </div>
                                 </div>
                                 <!-- end col-lg-4 -->
                                 <div class="col-4">
                                    <div class="input-box">
                                       <label  class="fs-13 text-black lh-20 fw-medium">Current or Last Job Role <span style="color:red"> * </span></label>
                                       <?php echo field_role( ( isset( $data->title ) && $data->title ) ? $data->title:'' ); ?>
                                    </div>
                                 </div>
                                 <!-- end col-lg-4 -->
                                 <div class="col-4">
                                    <div class="input-box">
                                       <label class="fs-13 text-black lh-20 fw-medium">Current or Last Company <span style="color:red"> * </span></label>
                                       <?php echo field_company( ( isset( $data->company ) && $data->company ) ? $data->company: '' ); ?>
                                    </div>
                                 </div>
                                 <!-- end col-lg-4 -->
                                 <div class="col-lg-4">
                                    <div class="input-box">
                                       <label class="fs-13 text-black lh-20 fw-medium">Where to find me online? (public link e.g. LinkedIn)</label>
                                       <div class="form-group">
                                          <input class="form-control form--control" type="text" value="<?php echo @$data->mylink; ?>" name="mylink">
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-lg-4">
                                    <div class="input-box">
                                       <label class="fs-13 text-black lh-20 fw-medium">Role(s) pursuing (Select all that applies)</label>
                                       <div class="form-group">
                                          <?php echo field_role_type(@$data->roletype); ?>
                                       </div>
                                    </div>
                                 </div>
                                 <!-- end col-lg-6 -->
                                 <div class="col-lg-4">
                                    <div class="input-box">
                                       <label class="fs-13 text-black lh-20 fw-medium">Work Information (Select all applicable options)</label>
                                       <?php echo field_flags(@$data->flags); ?>
                                    </div>
                                 </div>
                                 <!-- end row -->
                              </div>
                              <!-- end settings-item -->
                              <div class="settings-item">
                                 <div class="row pt-4"></div>
                                 <div class="row pt-4"></div>
                                 <div class="bg-secondary p-3 rounded-rounded">
                                    <h3 class="fs-17 text-white">Public Information (This information could be public on My profile page) </h3>
                                 </div>
                                 <div class="row pt-4">
                                    <div class="col-lg-6">
                                       <div class="input-box">
                                          <div class="form-group">
                                             <label class="fs-14 text-black lh-20 fw-medium mb-3">My Avatar<span class="text-danger"> * </span></label>
                                             <span class="text-danger" id="avatar-error"></span>
                                             <?php echo avatar_select(@$data->avatar); ?>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-lg-6">
                                       <div class="input-box">
                                          <div class="form-group">
                                             <label class="fs-14 text-black lh-20 fw-medium mb-3">My Profile Type <span style="color:red"> * </span></label>
                                             <div class="btn-group btn--group btn-group-toggle" data-toggle="buttons">
                                                <label class="btn ">
                                                <input <?php echo (@$data->type == "professional") ?'checked': '';?> type="radio"  name="type" value="professional" onchange="checkType();" required> Professional</label>
                                                <label class="btn">
                                                <input <?php echo (@$data->type == "employer") ?'checked': '';?> type="radio"  name="type" value="employer" onchange="checkType();" required> Employer</label>
                                                <label class="btn">
                                                <input <?php echo (@$data->type == "provider") ?'checked': '';?> type="radio"  name="type" value="provider" onchange="checkType();" required> Service Provider</label>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="row pt-4">
                                    <div class="col-6">
                                       <div class="input-box">
                                          <label class="fs-13 text-black lh-20 fw-medium">My Public Chat Name <span style="color:red"> * </span></label>
                                          <div class="form-group">
                                             <input  class="form-control form--control" required type="text" value="<?php echo @$data->chat_name; ?>" name="chat_name">
                                          </div>
                                       </div>
                                    </div>
                                    <!-- end col-lg-6 -->
                                    <div class="col-6">
                                       <div class="input-box">
                                          <label class="fs-13 text-black lh-20 fw-medium">My Pronouns</label>
                                          <div class="form-group">
                                             <select  class="form-control form--control" id="pronoun" name="pronoun" value="no">
                                                <option  disabled="disabled">Choose an option below</option>
                                                <option <?php echo (@$data->type == "she") ?'selected': '';?> value="she">She/her</option>
                                                <option <?php echo (@$data->type == "he") ?'selected': '';?> value="he">He/him</option>
                                                <option <?php echo (@$data->type == "them") ?'selected': '';?> value="them">They/them</option>
                                                <option <?php echo (@$data->type == "no") ?'selected': '';?> value="no" selected="">I prefer not to say</option>
                                             </select>
                                          </div>
                                       </div>
                                    </div>
                                    <!-- end col-lg-6 -->
                                    <div class="col-12 mt-3">
                                       <div class="input-box">
                                          <label class="fs-13 text-black lh-20 fw-medium">My Core Skills ( Select from the suggested skill list for better results ) <span style="color:red"> * </span></label>
                                          <?php echo field_skill( ( isset( $data->skill ) && $data->skill ) ? $data->skill:'' , 1); ?>
                                       </div>
                                    </div>
                                    <!-- end col-lg-6 -->
                                    <div class="col-lg-6 mt-3">
                                       <div class="input-box">
                                          <label class="fs-13 text-black lh-20 fw-medium">My City ( Only select from the suggested list ) <span style="color:red"> * </span></label>
                                          <?php echo field_location(@$data->coordinates,@$data->full_location, @$data->geohash); ?>
                                       </div>
                                    </div>
                                    <!-- end col-lg-4 -->
                                    <div class="col-lg-6 mt-3">
                                       <div class="input-box">
                                          <label class="fs-13 text-black lh-20 fw-medium">My Timezone <span style="color:red"> * </span></label>
                                          <?php echo field_time_zone(@$data->local_timezone, 1); ?>
                                       </div>
                                    </div>
                                    <!-- end col-lg-4 -->
                                    <div class="col-12 mt-3">
                                       <div class="input-box">
                                          <label class="fs-13 text-black lh-20 fw-medium">About Me <span style="color:red"> * </span></label>
                                          <div class="form-group">
                                             <textarea  class="form-control form--control" rows="8" maxlength="500" name="aboutme"><?php echo @$data->aboutme; ?> </textarea>
                                          </div>
                                       </div>
                                    </div>
                                    <!-- end col-lg-12 -->
                                    <div class="col-12">
                                       <div class="input-box">
                                          <label class="fs-13 text-black lh-20 fw-medium">Fun Fact(Great for ice-breakers) <span style="color:red"> * </span></label>
                                          <div class="form-group">
                                             <textarea class="form-control form--control" rows="8" maxlength="500" name="funfact" ><?php echo @$data->funfact; ?> </textarea>
                                          </div>
                                       </div>
                                    </div>
                                    <!-- end col-lg-12 -->
                                    <!-- 
                                       <div class="col-lg-12">
                                           <div class="submit-btn-box pt-3">
                                           <a href="#" onclick="next()" class="btn theme-btn">Next step</a>
                                           </div>
                                       </div> -->
                                    <!-- end col-lg-12 -->
                                 </div>
                                 <!-- end row -->
                              </div>
                              <!-- end settings-item -->
                              <div class="settings-item">
                                 <div class="row pt-4"></div>
                                 <div class="row pt-4"></div>
                                 <div class="bg-secondary p-3 rounded-rounded">
                                    <h3 class="fs-17 text-white proftype">Profile Pulic Information</h3>
                                 </div>
                                 <div class="row pt-4">
                                    <div class="col-12 mt-3">
                                       <div class="input-box">
                                          <label class="fs-13 text-black lh-20 fw-medium profstype">Profile Pulic Information</label>
                                          <div class="form-group">
                                             <textarea  class="form-control form--control" rows="8" maxlength="500" name="about_type"><?php echo @$data->about_type; ?> </textarea>
                                          </div>
                                       </div>
                                    </div>
                                    <!-- end col-lg-12 -->
                                 </div>
                                 <!-- end row -->
                              </div>
                              <!-- end settings-item -->                                
                              <div class="col-12">
                                 <div class="submit-btn-box pt-3">
                                    <button class="btn theme-btn" type="submit">Save changes</button>
                                 </div>
                              </div>
                              <!-- end col-lg-12 -->
                           </div>
                           <!-- end user-panel -->
                        </div>
                        <!-- end user-panel-main-bar -->
                     </div>
                     <!-- end tab-pane -->
                     <?php
                        /*               
                        					<div class="tab-pane fade" id="notification" role="tabpanel" aria-labelledby="notification-tab">
                                                <div class="user-panel-main-bar">
                                                    <div class="user-panel">
                                                        <form method="post" class="pt-20px MultiFile-intercepted" action="/hires/actions/settings">
                                                          <input type="hidden" name="tab" value="notification">
                                                          <!-- Apps Notification -->
                                                          <?php foreach (taoh_ available_apps() as $app) { ?>
                     <div class="settings-item mb-20px  border-bottom border-bottom-gray pb-20px">
                        <div class="input-box">
                           <label class="fs-22 text-black lh-20 fw-medium mb-0 text-capitalize"> <?php echo $app; ?></label>
                           <span class="fs-13 d-block lh-18 pb-3">An email rounding up the best news, entertainment, and culture from the world of software development</span>
                           <div class="input-box row">
                              <div class="col-9">
                                 <label class="fs-14 text-black lh-20 fw-medium mb-0">Web Notification</label>
                                 <span class="fs-13 d-block lh-18 pb-3">New chat and reply chat</span>
                              </div>
                              <div class="col-3">
                                 <div class="form-group float-right">
                                    <div class="btn-group btn--group btn-group-toggle" data-toggle="buttons">
                                       <?php
                                          $key = "notify_".$app."_web";
                                          $checked = @$data->$key ;
                                          ?>
                                       <label class="btn">
                                       <input type="radio" name="notify_<?php echo $app; ?>_web" value="off"
                                          <?php echo $checked == 'off' ? 'checked': ''; ?>> Off
                                       </label>
                                       <label class="btn">
                                       <input type="radio" name="notify_<?php echo $app; ?>_web" value="on"
                                          <?php echo $checked == 'on' ? 'checked': ''; ?>> On
                                       </label>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="input-box row ">
                              <div class="col-6">
                                 <label class="fs-14 text-black lh-20 fw-medium mb-0">Email Notification</label>
                                 <span class="fs-13 d-block lh-18 pb-3">New chat and reply chat</span>
                              </div>
                              <div class="col-6">
                                 <div class="btn-group btn--group btn-group-toggle" data-toggle="buttons">
                                    <?php
                                       $key = "notify_".$app."_email";
                                       $checked = @$data->$key ;
                                       ?>
                                    <label class="btn">
                                    <input type="radio" name="notify_<?php echo $app; ?>_email" value="off"
                                       <?php echo $checked == 'off' ? 'checked': ''; ?>> Off
                                    </label>
                                    <label class="btn active">
                                    <input type="radio" name="notify_<?php echo $app; ?>_email" value="send_email_immediately"
                                       <?php echo $checked == 'send_email_immediately' ? 'checked': ''; ?>> Send Email Immediately
                                    </label>
                                    <label class="btn">
                                    <input type="radio" name="notify_<?php echo $app; ?>_email" value="once_daily"
                                       <?php echo $checked == 'once_daily' ? 'checked': ''; ?>> Once Daily
                                    </label>
                                    <label class="btn">
                                    <input type="radio" name="notify_<?php echo $app; ?>_email" value="once_a_week"
                                       <?php echo $checked == 'once_a_week' ? 'checked': ''; ?>> Once a Week
                                    </label>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <!-- end settings-item -->
                     <?php } ?>
                     <!-- Chat Notification -->
                     <div class="settings-item mb-20px  border-bottom border-bottom-gray pb-20px">
                        <div class="input-box">
                           <label class="fs-22 text-black lh-20 fw-medium mb-0">Chat</label>
                           <span class="fs-13 d-block lh-18 pb-3">An email rounding up the best news, entertainment, and culture from the world of software development</span>
                           <div class="input-box row">
                              <div class="col-9">
                                 <label class="fs-14 text-black lh-20 fw-medium mb-0">Web Notification</label>
                                 <span class="fs-13 d-block lh-18 pb-3">New chat and reply chat</span>
                              </div>
                              <div class="col-3">
                                 <div class="form-group float-right">
                                    <div class="btn-group btn--group btn-group-toggle" data-toggle="buttons">
                                       <label class="btn active">
                                       <input type="radio" name="notify_chat_web" value="off"
                                          <?php echo @$data->notify_chat_web == 'off' ? 'checked': ''; ?>> Off
                                       </label>
                                       <label class="btn">
                                       <input type="radio" name="notify_chat_web" value="on"
                                          <?php echo @$data->notify_chat_web == 'on' ? 'checked': ''; ?>> On
                                       </label>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="input-box row ">
                              <div class="col-6">
                                 <label class="fs-14 text-black lh-20 fw-medium mb-0">Email Notification</label>
                                 <span class="fs-13 d-block lh-18 pb-3">New chat and reply chat</span>
                              </div>
                              <div class="col-6">
                                 <div class="btn-group btn--group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn">
                                    <input type="radio" name="notify_chat_email" value="off"
                                       <?php echo @$data->notify_chat_email == 'off' ? 'checked': ''; ?>> Off
                                    </label>
                                    <label class="btn active">
                                    <input type="radio" name="notify_chat_email" value="send_email_immediately"
                                       <?php echo @$data->notify_chat_email == 'send_email_immediately' ? 'checked': ''; ?>> Send Email Immediately
                                    </label>
                                    <label class="btn">
                                    <input type="radio" name="notify_chat_email" value=""="once_daily"
                                    <?php echo @$data->notify_chat_email == 'once_daily' ? 'checked': ''; ?>> Once Daily
                                    </label>
                                    <label class="btn">
                                    <input type="radio" name="notify_chat_email" value="once_a_week"
                                       <?php echo @$data->notify_chat_email == 'once_a_week' ? 'checked': ''; ?>> Once a Week
                                    </label>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <!-- end settings-item -->
                     <!-- Newsletter Notification -->
                     <div class="settings-item mb-20px  border-bottom border-bottom-gray pb-20px">
                        <div class="input-box">
                           <label class="fs-22 text-black lh-20 fw-medium mb-0">Newsletter</label>
                           <span class="fs-13 d-block lh-18 pb-3">An email rounding up the best news, entertainment, and culture from the world of software development</span>
                           <div class="input-box row">
                              <div class="col-9">
                                 <label class="fs-14 text-black lh-20 fw-medium mb-0">Web Notification</label>
                                 <span class="fs-13 d-block lh-18 pb-3">New chat and reply chat</span>
                              </div>
                              <div class="col-3">
                                 <div class="form-group float-right">
                                    <div class="btn-group btn--group btn-group-toggle" data-toggle="buttons">
                                       <label class="btn active">
                                       <input type="radio" name="notify_newsletter_web" value="off"
                                          <?php echo @$data->notify_newsletter_web == 'off' ? 'checked': ''; ?>> Off
                                       </label>
                                       <label class="btn">
                                       <input type="radio" name="notify_newsletter_web" value="on"
                                          <?php echo @$data->notify_newsletter_web == 'on' ? 'checked': ''; ?>> On
                                       </label>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="input-box row ">
                              <div class="col-6">
                                 <label class="fs-14 text-black lh-20 fw-medium mb-0">Email Notification</label>
                                 <span class="fs-13 d-block lh-18 pb-3">New chat and reply chat</span>
                              </div>
                              <div class="col-6">
                                 <div class="btn-group btn--group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn">
                                    <input type="radio" name="notify_newsletter_email" value="off"
                                       <?php echo @$data->notify_newsletter_email == 'off' ? 'checked': ''; ?>> Off
                                    </label>
                                    <label class="btn active">
                                    <input type="radio" name="notify_newsletter_email" value="send_email_immediately"
                                       <?php echo @$data->notify_newsletter_email == 'send_email_immediately' ? 'checked': ''; ?>> Send Email Immediately
                                    </label>
                                    <label class="btn">
                                    <input type="radio" name="notify_newsletter_email" value="once_daily"
                                       <?php echo @$data->notify_newsletter_email == 'once_daily' ? 'checked': ''; ?>> Once Daily
                                    </label>
                                    <label class="btn">
                                    <input type="radio" name="notify_newsletter_email" value="once_a_week"
                                       <?php echo @$data->notify_newsletter_email == 'once_a_week' ? 'checked': ''; ?>> Once a Week
                                    </label>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <!-- end settings-item -->
                     <div class="col-12">
                        <div class="submit-btn-box pt-3">
                           <button class="btn theme-btn" type="submit">Save changes</button>
                        </div>
                     </div>
                     <!-- end col-lg-12 -->
            </form>
            </div><!-- end user-panel -->
            </div><!-- end user-panel-main-bar -->
         </div>
         <!-- end tab-pane -->
         */
         ?>                                  
      </div>
      </form>
   </div>
   <!-- end col-lg-12 -->
   <?php
      /*
                  <div class="col-lg-3">
                      <div class="sidebar">
                        <?php taoh_stats_widget(); ?>
   <?php taoh_readables_widget(); ?>
   </div><!-- end sidebar -->
   </div><!-- end col-lg-3 -->
   */
   ?>
   </div><!-- end row -->
   </div><!-- end container -->
</section>
<script type="text/javascript">
   $(document).ready(function(){
     let selected =   $(location).prop('hash');
     if(selected) {
       $('#myTab .nav-item a[href$="'+selected+'"]').addClass('active');
       $(selected).addClass('show active');
     } else {
       $('#myTab .nav-item a[href$="#general"]').addClass('active');
       $('#general').addClass('show active');
     }
     checkType();
   })
   
   function checkType() {
       let chktype = $('input[name="type"]:checked').val();
       if(chktype == 'professional'){
           $('.proftype').html('Professional Public Information');
           $('.profstype').html('Professional Public Information');
       }else if(chktype == 'employer'){
           $('.proftype').html('Employer Public Information');
           $('.profstype').html('Employer Public Information');
       }else if(chktype == 'provider'){
           $('.proftype').html('Service Provider Public Information'); 
           $('.profstype').html('Service Provider Public Information');
       }
   }
</script>
<!-- <script type="text/javascript" src="https://bug7a.github.io/iconselect.js/sample/lib/control/iconselect.js"></script>
   <script type="text/javascript" src="https://bug7a.github.io/iconselect.js/sample/lib/iscroll.js"></script> -->
<?php taoh_get_footer(); ?>