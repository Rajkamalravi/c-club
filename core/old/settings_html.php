<?php
$user_is_logged_in = taoh_user_is_logged_in() ?? false;
if (!$user_is_logged_in) {
    header("Location: " . TAOH_SITE_URL_ROOT . '/login');
}

taoh_add_var_to_url('noca', TAOH_MY_NOW_CODE);
$taoh_home_url = ( defined( 'TAOH_PAGE_URL' ) && TAOH_PAGE_URL ) ? TAOH_PAGE_URL:TAOH_SITE_URL_ROOT;

taoh_get_header();
$indx_db_settings = 1;

$data = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];

$ptoken = $data->ptoken;
/*
$return = taoh_get_user_info($ptoken,'full',1);
$result = json_decode($return, true);
if (!isset($result['output']) || !$result['success'] || $result['success'] == '') {
    taoh_set_error_message('Invalid profile!');
    taoh_redirect(TAOH_SITE_URL_ROOT);
    die();
}
else{
    $user_data = $result['output']['user'];
   // echo'<pre>';print_r($user_data);echo'</pre>';
}
*/
//echo'<pre>';print_r($data);echo'</pre>';die();

$login_type='update';
if(!isset($data->login_type)) $login_type='first_update';

if ( isset( $data->full_location ) && $data->full_location != '' ) {
    $location = str_replace(",", "-", @$data->full_location);
}
$taoh_user_keywords = defined('TAOH_USER_KEYWORDS') ? getJsonDecodedData(TAOH_USER_KEYWORDS) : [];
$show_name_slug_information = !empty($taoh_user_keywords);


if (isset($data->avatar_image) && $data->avatar_image != '') {
    $avatar_image = $data->avatar_image;
} else {
    if (isset($data->avatar) && $data->avatar != 'default') {
        $avatar_image = TAOH_OPS_PREFIX . '/avatar/PNG/128/' . $data->avatar . '.png';
    } else {
        $avatar_image = TAOH_OPS_PREFIX . '/avatar/PNG/128/avatar_def.png';
    }
}

/* Get User Info */
//echo "========";
$return = taoh_get_user_info($ptoken,'full',1);
$pfdata = json_decode($return, true);
/* Get User Info */
// echo "<pre>"; print_r($data); echo "</pre>";
// echo "<pre style='color:red'>"; print_r($pfdata); echo "</pre>";
$about_me = (implode(' ', array_filter(explode(' ', $pfdata['output']['user']['aboutme']))));
$fun_fact = implode(' ', array_filter(explode(' ', $pfdata['output']['user']['funfact'])));
$hobbies = json_decode(implode(' ', array_filter(explode(' ', $pfdata['output']['user']['hobbies']))));

// echo "<pre>"; print_r(($fun_fact)); echo "</pre>";

if (isset($pfdata['output']['user']['education']) && is_array($pfdata['output']['user']['education'])) {
    $edu_encode = json_encode($pfdata['output']['user']['education']);
    $edu_list = json_decode($edu_encode, true);
    $edu_tot_count = array_key_last($edu_list) + 1;
    $edu_last_key = array_key_last($edu_list);
} else {
    $edu_tot_count = 0;
    $edu_last_key = 0;
    $edu_list = '';
}
if (isset($pfdata['output']['user']['employee']) && is_array($pfdata['output']['user']['employee'])) {
    $emp_encode = json_encode($pfdata['output']['user']['employee']);
    $emp_list = json_decode($emp_encode, true);
    $emp_tot_count = array_key_last($emp_list) + 1;
    $emp_last_key = array_key_last($emp_list);
} else {
    $emp_tot_count = 0;
    $emp_last_key = 0;
    $emp_list = '';
}
// echo "<pre>"; print_r($edu_list); echo "</pre>";
// echo "<pre style='color:blue'>"; print_r($emp_list); echo "</pre>";

function showUnlistmeField($data)
{
    // echo "<pre>"; print_r($data); echo "</pre>";
    echo '<div>';
    echo '<div>';
    echo '<h6 class="p-field-title d-flex align-items-center m-0" style="gap: 8px;"><svg style="min-width: fit-content;" width="21" height="21" viewBox="0 0 28 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.2 5.5C4.2 4.04131 4.79 2.64236 5.8402 1.61091C6.89041 0.579463 8.31479 0 9.8 0C11.2852 0 12.7096 0.579463 13.7598 1.61091C14.81 2.64236 15.4 4.04131 15.4 5.5C15.4 6.95869 14.81 8.35764 13.7598 9.38909C12.7096 10.4205 11.2852 11 9.8 11C8.31479 11 6.89041 10.4205 5.8402 9.38909C4.79 8.35764 4.2 6.95869 4.2 5.5ZM0 20.7238C0 16.4914 3.49125 13.0625 7.80062 13.0625H11.7994C16.1087 13.0625 19.6 16.4914 19.6 20.7238C19.6 21.4285 19.0181 22 18.3006 22H1.29938C0.581875 22 0 21.4285 0 20.7238ZM20.65 8.59375H26.95C27.5319 8.59375 28 9.05352 28 9.625C28 10.1965 27.5319 10.6562 26.95 10.6562H20.65C20.0681 10.6562 19.6 10.1965 19.6 9.625C19.6 9.05352 20.0681 8.59375 20.65 8.59375Z" fill="#2557A7"/></svg><span>Remove from the directory?</span></h6>';
    echo '<hr class="my-2" style="border-top: 1px solid #D3D3D3;">';
    echo '<p class="list-text-nml my-3">By selecting Yes you will be unlisted from our directory at the same time you can\'t see other users too</p>';
    echo field_yes_no('unlist_me_dir', $data->unlist_me_dir ? 'no' : 'no');
    echo '</div>';
    echo '</div>';
}

$tag_category = TAOH_TAG_CATEGORY;
$tag_category_form = TAOH_TAG_CATEGORY_FORM;
//echo "<pre>"; print_r($data); echo "</pre>";
// echo "<pre style='color:red'>"; print_r($tag_category_form); echo "</pre>";

?>
<style>
    .modal-body {
        height: 70vh;
        overflow-y: auto;
    }
</style>

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

<div class="profile-n bg-white pt-5">
    <div class="float-right pr-5">
        <a class="nav-link d-flex flex-column text-center red text-white" aria-current="page" style="width:20px;" href="<?php echo $taoh_home_url; ?>">X</a>
    </div>
    <div class="container d-flex flex-column flex-lg-row" style="gap: 20px;">
        <!-- <div class="profile-left" style="flex: 1;">
            <div class="p-d-con mb-4">
                <div class="p-img-con">
                    <img src="<?php echo $avatar_image;?>" alt="">
                </div>
                <div>
                    <h6 class="p-name"><?php echo ucfirst(@$data->fname);?></h6>
                    <p class="p-type"><?php echo ucfirst(@$data->type);?></p>
                </div>
            </div>
            <a class="your-nje" href="#">
                <svg width="19" height="17" viewBox="0 0 19 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.85 4.69999C2.85 3.69217 3.25036 2.72563 3.96299 2.01299C4.67563 1.30035 5.64218 0.899994 6.65 0.899994C7.65782 0.899994 8.62437 1.30035 9.33701 2.01299C10.0496 2.72563 10.45 3.69217 10.45 4.69999C10.45 5.70782 10.0496 6.67436 9.33701 7.387C8.62437 8.09964 7.65782 8.49999 6.65 8.49999C5.64218 8.49999 4.67563 8.09964 3.96299 7.387C3.25036 6.67436 2.85 5.70782 2.85 4.69999ZM0 15.2183C0 12.2941 2.36906 9.92499 5.29328 9.92499H8.00672C10.9309 9.92499 13.3 12.2941 13.3 15.2183C13.3 15.7051 12.9052 16.1 12.4183 16.1H0.881719C0.394844 16.1 0 15.7051 0 15.2183ZM18.0886 16.1H13.9947C14.155 15.8209 14.25 15.4973 14.25 15.15V14.9125C14.25 13.1105 13.4455 11.4925 12.1778 10.4059C12.2491 10.403 12.3173 10.4 12.3886 10.4H14.2114C16.8566 10.4 19 12.5434 19 15.1886C19 15.6933 18.5903 16.1 18.0886 16.1ZM12.825 8.49999C11.9047 8.49999 11.0734 8.12593 10.4708 7.52328C11.0556 6.73359 11.4 5.75687 11.4 4.69999C11.4 3.90437 11.2041 3.15328 10.8567 2.49421C11.4089 2.09046 12.0887 1.84999 12.825 1.84999C14.6627 1.84999 16.15 3.33734 16.15 5.17499C16.15 7.01265 14.6627 8.49999 12.825 8.49999Z" fill="#2557A7"/>
                </svg>
                <span>Your Network</span>
            </a>
           <a class="your-nje" href="<?php //echo $taoh_home_url."/jobs"; ?>" targe="_blank">
                <svg width="19" height="17" viewBox="0 0 13 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.0136 16.4417L1.01162 16.4431C0.960006 16.4789 0.894396 16.5 0.822656 16.5C0.635955 16.5 0.5 16.3528 0.5 16.1932V1.59375C0.5 0.999118 0.99481 0.5 1.625 0.5H11.375C12.0052 0.5 12.5 0.999118 12.5 1.59375V16.1932C12.5 16.3528 12.364 16.5 12.1773 16.5C12.1056 16.5 12.04 16.4789 11.9884 16.4431L11.9864 16.4417L6.78301 12.8691L6.5 12.6747L6.21699 12.8691L1.0136 16.4417Z" fill="#2557A7" stroke="#2557A7"/>
                </svg>
                <span>Your Jobs</span>
            </a>
            <a class="your-nje" href="<?php // echo $taoh_home_url."/events"; ?>" targe="_blank">
                <svg width="19" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.28571 0C4.87835 0 5.35714 0.474805 5.35714 1.0625V2.125H9.64286V1.0625C9.64286 0.474805 10.1217 0 10.7143 0C11.3069 0 11.7857 0.474805 11.7857 1.0625V2.125H13.3929C14.2801 2.125 15 2.83887 15 3.71875V5.3125H0V3.71875C0 2.83887 0.719866 2.125 1.60714 2.125H3.21429V1.0625C3.21429 0.474805 3.69308 0 4.28571 0ZM0 6.375H15V15.4062C15 16.2861 14.2801 17 13.3929 17H1.60714C0.719866 17 0 16.2861 0 15.4062V6.375ZM11.0156 10.127C11.3304 9.81484 11.3304 9.31016 11.0156 9.00137C10.7009 8.69258 10.192 8.68926 9.88058 9.00137L6.69978 12.1557L5.12612 10.5951C4.81138 10.283 4.30245 10.283 3.99107 10.5951C3.67969 10.9072 3.67634 11.4119 3.99107 11.7207L6.13393 13.8457C6.44866 14.1578 6.95759 14.1578 7.26897 13.8457L11.0156 10.127Z" fill="#2557A7"/>
                </svg>
                <span>Your Events</span>
            </a>

        </div> -->
        <div class="profile-right">
            <div class="p-banner mb-5">
                <?php if(@$data->avatar_image != '' || $avatar_image != ''){ ?>
                <div class="p-banner-img-con">
                    <div class="p-banner-bg" style="background-image: url(<?php echo $avatar_image; ?>)"></div>
                    <div class="glass-overlay"></div>
                    <img class="main-img" src="<?php echo $avatar_image; // echo TAOH_SITE_URL_ROOT.'/assets/images/Rectangle-dummy.png';?>" alt="">
                </div>
                <?php } ?>
                <div class="d-flex flex-wrap mt-4 pl-4 pl-lg-5" style="gap: 24px;">
                    <div class="p-img-right">
                        <img src="<?php echo $avatar_image;?>" alt="">
                    </div>
                    <div>
                        <div class="d-flex align-items-center flex-wrap mb-4" style="gap: 12px;">
                            <h6 class="p-banner-name mr-lg-3"><?php echo ucfirst(@$data->fname);?></h6>
                            <?php if(isset($data->profile_complete) && $data->profile_complete ==1 ) { ?>
                            <p class="p-batch text-capitalize"><?php echo !empty($data->type) ? ucfirst($data->type) : 'Professional'; ?></p>
                            <?php } ?>
                        </div>
                        <?php if(isset($data->profile_complete) && $data->profile_complete ==1 ) { ?>
                        <div class="your-nje" style="gap: 12px;">
                            <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.54688 2H13.4531C13.6336 2 13.7812 2.15 13.7812 2.33333V4H7.21875V2.33333C7.21875 2.15 7.36641 2 7.54688 2ZM5.25 2.33333V4H2.625C1.17715 4 0 5.19583 0 6.66667V10.6667H7.875H13.125H21V6.66667C21 5.19583 19.8229 4 18.375 4H15.75V2.33333C15.75 1.04583 14.7205 0 13.4531 0H7.54688C6.27949 0 5.25 1.04583 5.25 2.33333ZM21 12H13.125V13.3333C13.125 14.0708 12.5385 14.6667 11.8125 14.6667H9.1875C8.46152 14.6667 7.875 14.0708 7.875 13.3333V12H0V17.3333C0 18.8042 1.17715 20 2.625 20H18.375C19.8229 20 21 18.8042 21 17.3333V12Z" fill="#2557A7"/>
                            </svg>
                            <span><?php
                                $company = get_explode_names(@$data->company);
                                $company = $company[0] ?? '';

                                if((array)$data->title ) {
                                    $roleArr = (array)$data->title;
                                    $role = explode(':>', reset($roleArr))[1];
                                }
                                echo $role.', '.$company; ?>
                            </span>
                        <!-- </div>
                        <div class="your-nje" style="gap: 12px;"> -->
                            <svg width="21" height="20" viewBox="0 0 15 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.42578 19.5506C10.4297 17.0363 15 10.9424 15 7.51946C15 3.36809 11.6406 0 7.5 0C3.35938 0 0 3.36809 0 7.51946C0 10.9424 4.57031 17.0363 6.57422 19.5506C7.05469 20.1498 7.94531 20.1498 8.42578 19.5506ZM7.5 5.01297C8.16304 5.01297 8.79893 5.27705 9.26777 5.74711C9.73661 6.21716 10 6.8547 10 7.51946C10 8.18422 9.73661 8.82176 9.26777 9.29181C8.79893 9.76187 8.16304 10.0259 7.5 10.0259C6.83696 10.0259 6.20107 9.76187 5.73223 9.29181C5.26339 8.82176 5 8.18422 5 7.51946C5 6.8547 5.26339 6.21716 5.73223 5.74711C6.20107 5.27705 6.83696 5.01297 7.5 5.01297Z" fill="#2557A7"/>
                            </svg>
                            <span><?php echo @$data->full_location;?></span>
                        </div>
                        <?php } ?>
                        <!-- small screen visibile -->
                        <p class="p-text-xs d-flex align-items-center d-xl-none mb-2" style="gap: 12px;">
                            <a class="your-nje" href="<?php echo $taoh_home_url."/jobs"; ?>" target="_blank">
                                <svg width="19" height="17" viewBox="0 0 13 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.0136 16.4417L1.01162 16.4431C0.960006 16.4789 0.894396 16.5 0.822656 16.5C0.635955 16.5 0.5 16.3528 0.5 16.1932V1.59375C0.5 0.999118 0.99481 0.5 1.625 0.5H11.375C12.0052 0.5 12.5 0.999118 12.5 1.59375V16.1932C12.5 16.3528 12.364 16.5 12.1773 16.5C12.1056 16.5 12.04 16.4789 11.9884 16.4431L11.9864 16.4417L6.78301 12.8691L6.5 12.6747L6.21699 12.8691L1.0136 16.4417Z" fill="#2557A7" stroke="#2557A7"/>
                                </svg>
                                <span>My Jobs</span>
                            </a>

                            <!-- <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.54688 2H13.4531C13.6336 2 13.7812 2.15 13.7812 2.33333V4H7.21875V2.33333C7.21875 2.15 7.36641 2 7.54688 2ZM5.25 2.33333V4H2.625C1.17715 4 0 5.19583 0 6.66667V10.6667H7.875H13.125H21V6.66667C21 5.19583 19.8229 4 18.375 4H15.75V2.33333C15.75 1.04583 14.7205 0 13.4531 0H7.54688C6.27949 0 5.25 1.04583 5.25 2.33333ZM21 12H13.125V13.3333C13.125 14.0708 12.5385 14.6667 11.8125 14.6667H9.1875C8.46152 14.6667 7.875 14.0708 7.875 13.3333V12H0V17.3333C0 18.8042 1.17715 20 2.625 20H18.375C19.8229 20 21 18.8042 21 17.3333V12Z" fill="#2557A7"/>
                            </svg>
                            <span><?php
                            /* $company = get_explode_names(@$data->company);

                            $company = $company[0] ?? '';
                            echo $company; */?></span> -->
                        </p>
                        <!-- small screen visibile -->
                        <p class="p-text-xs d-flex align-items-center d-xl-none" style="gap: 12px;">
                            <a class="your-nje" href="<?php echo $taoh_home_url."/events?creator=1"; ?>" target="_blank">
                                <svg width="19" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.28571 0C4.87835 0 5.35714 0.474805 5.35714 1.0625V2.125H9.64286V1.0625C9.64286 0.474805 10.1217 0 10.7143 0C11.3069 0 11.7857 0.474805 11.7857 1.0625V2.125H13.3929C14.2801 2.125 15 2.83887 15 3.71875V5.3125H0V3.71875C0 2.83887 0.719866 2.125 1.60714 2.125H3.21429V1.0625C3.21429 0.474805 3.69308 0 4.28571 0ZM0 6.375H15V15.4062C15 16.2861 14.2801 17 13.3929 17H1.60714C0.719866 17 0 16.2861 0 15.4062V6.375ZM11.0156 10.127C11.3304 9.81484 11.3304 9.31016 11.0156 9.00137C10.7009 8.69258 10.192 8.68926 9.88058 9.00137L6.69978 12.1557L5.12612 10.5951C4.81138 10.283 4.30245 10.283 3.99107 10.5951C3.67969 10.9072 3.67634 11.4119 3.99107 11.7207L6.13393 13.8457C6.44866 14.1578 6.95759 14.1578 7.26897 13.8457L11.0156 10.127Z" fill="#2557A7"/>
                                </svg>
                                <span>My Events</span>
                            </a>
                            <!-- <svg width="21" height="20" viewBox="0 0 15 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.42578 19.5506C10.4297 17.0363 15 10.9424 15 7.51946C15 3.36809 11.6406 0 7.5 0C3.35938 0 0 3.36809 0 7.51946C0 10.9424 4.57031 17.0363 6.57422 19.5506C7.05469 20.1498 7.94531 20.1498 8.42578 19.5506ZM7.5 5.01297C8.16304 5.01297 8.79893 5.27705 9.26777 5.74711C9.73661 6.21716 10 6.8547 10 7.51946C10 8.18422 9.73661 8.82176 9.26777 9.29181C8.79893 9.76187 8.16304 10.0259 7.5 10.0259C6.83696 10.0259 6.20107 9.76187 5.73223 9.29181C5.26339 8.82176 5 8.18422 5 7.51946C5 6.8547 5.26339 6.21716 5.73223 5.74711C6.20107 5.27705 6.83696 5.01297 7.5 5.01297Z" fill="#2557A7"/>
                            </svg>
                            <span><?php //echo @$data->full_location;?></span> -->
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
        <!-- Progress Bar --> <!-- old  -->
    <div class="container d-none">
        <div class="row justify-content-end">
            <div class="col-lg-11 ">
                <h4 class="p-text-lg mb-3">Setup your profile for more efficient results ! It’s Fast and Simple !</h4>
                <ul class="nav nav-tabs mb-4" id="myTab" role="tablist" style="gap: 12px;">
                    <!-- Tab Links -->
                    <li class="nav-item">
                        <a class="nav-link progress-card active py-2 d-flex flex-column justify-content-between" data-toggle="tab" href="#form-block-1" role="tab" aria-controls="block1" aria-selected="true">
                            <div>
                                <svg width="13" height="13" viewBox="0 0 13 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.964 4.55636C13.0502 4.7943 12.9774 5.05958 12.7916 5.22915L11.6251 6.3067C11.6547 6.5337 11.6709 6.76617 11.6709 7.00137C11.6709 7.23657 11.6547 7.46904 11.6251 7.69603L12.7916 8.77359C12.9774 8.94315 13.0502 9.20844 12.964 9.44638C12.8454 9.77183 12.7027 10.0836 12.5383 10.3845L12.4117 10.606C12.2339 10.9068 12.0346 11.1912 11.8164 11.4593C11.6574 11.6562 11.3934 11.7218 11.1564 11.6452L9.65588 11.1612C9.2949 11.4429 8.89621 11.6781 8.47057 11.8558L8.13384 13.4175C8.07996 13.6663 7.89139 13.8633 7.64355 13.9043C7.2718 13.9672 6.88927 14 6.49865 14C6.10804 14 5.72551 13.9672 5.35375 13.9043C5.10592 13.8633 4.91735 13.6663 4.86347 13.4175L4.52673 11.8558C4.1011 11.6781 3.70241 11.4429 3.34143 11.1612L1.84363 11.648C1.60657 11.7246 1.34257 11.6562 1.18363 11.462C0.965426 11.194 0.766079 10.9096 0.588283 10.6087L0.46167 10.3872C0.297344 10.0863 0.154568 9.77457 0.0360376 9.44911C-0.0501666 9.21117 0.0225681 8.94589 0.208446 8.77632L1.3749 7.69877C1.34526 7.46904 1.3291 7.23657 1.3291 7.00137C1.3291 6.76617 1.34526 6.5337 1.3749 6.3067L0.208446 5.22915C0.0225681 5.05958 -0.0501666 4.7943 0.0360376 4.55636C0.154568 4.2309 0.297344 3.91913 0.46167 3.61828L0.588283 3.39676C0.766079 3.09592 0.965426 2.81149 1.18363 2.54347C1.34257 2.34655 1.60657 2.28091 1.84363 2.35749L3.34412 2.84157C3.7051 2.55988 4.1038 2.32467 4.52943 2.1469L4.86616 0.585271C4.92004 0.336394 5.10861 0.13948 5.35645 0.0984567C5.7282 0.0328189 6.11073 0 6.50135 0C6.89196 0 7.27449 0.0328189 7.64625 0.0957218C7.89408 0.136745 8.08265 0.333659 8.13653 0.582536L8.47327 2.14417C8.8989 2.32194 9.29759 2.55714 9.65857 2.83884L11.1591 2.35476C11.3961 2.27818 11.6601 2.34655 11.8191 2.54073C12.0373 2.80875 12.2366 3.09318 12.4144 3.39402L12.541 3.61555C12.7054 3.91639 12.8481 4.22817 12.9667 4.55362L12.964 4.55636ZM6.50135 9.18929C7.07292 9.18929 7.62108 8.95878 8.02524 8.54847C8.4294 8.13815 8.65645 7.58164 8.65645 7.00137C8.65645 6.42109 8.4294 5.86459 8.02524 5.45427C7.62108 5.04395 7.07292 4.81344 6.50135 4.81344C5.92978 4.81344 5.38162 5.04395 4.97746 5.45427C4.5733 5.86459 4.34624 6.42109 4.34624 7.00137C4.34624 7.58164 4.5733 8.13815 4.97746 8.54847C5.38162 8.95878 5.92978 9.18929 6.50135 9.18929Z" fill="#333333"/>
                                </svg>
                                <h6 class="pc-title pt-0">General Settings</h6> <!-- Setup  -->
                                <hr class="my-1" style="border-top: 1px solid #9A9999;">
                            </div>
                            <div>
                                <!-- <p class="pc-content pt-1">Essential settings required to access the site.</p> -->
                                <?php if((isset($data->profile_complete) && $data->profile_complete ==1 ) ||
                                (isset($data->step1) && $data->step1 == 1)) { ?>
                                <div class="pt-2">
                                    <p class="d-flex align-items-center justify-content-end" style="gap: 6px;">
                                        <span class="pc-content">Completed</span>
                                        <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z"
                                            fill="#379D0B"/>
                                        </svg>
                                    </p>
                                </div>
                               <?php }else{ ?>
                                <div class="pt-2">
                                    <p class="d-flex align-items-center justify-content-end" style="gap: 6px;">
                                        <span class="pc-content">Pending</span>
                                        <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#A7A7A7"/>
                                        </svg>
                                    </p>
                                </div>
                                <?php } ?>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link progress-card py-2 d-flex flex-column justify-content-between" data-toggle="tab" href="#form-block-2" role="tab" aria-controls="block2" aria-selected="true">
                            <div>
                                <svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.5 0C8.81773 4.60086 10.4843 6.29889 15 7.64148C10.4843 8.98407 8.81773 10.6821 7.5 15.283C6.18227 10.6828 4.51568 8.98407 0 7.64148C4.51568 6.29889 6.18227 4.6016 7.5 0Z" fill="black"/>
                                </svg>
                                <h6 class="pc-title pt-0">Status</h6><!-- Setup your  -->
                                <hr class="my-1" style="border-top: 1px solid #9A9999;">
                            </div>
                            <div>
                                <!-- <p class="pc-content pt-1">Setup your current status to spark better connections</p> -->
                                <?php // if(isset($data->step2) && $data->step2 == 2) {
                                if( isset( $data->skill ) && $data->skill){
                                ?>
                                    <div class="pt-2">
                                        <p class="d-flex align-items-center justify-content-end" style="gap: 6px;">
                                            <span class="pc-content">Completed</span>
                                            <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#379D0B"/>
                                            </svg>
                                        </p>
                                    </div>
                                <?php } else { ?>
                                    <!-- pending -->
                                    <div class="pt-2">
                                        <p class="d-flex align-items-center justify-content-end" style="gap: 6px;">
                                            <span class="pc-content">Pending</span>
                                            <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#A7A7A7"/>
                                            </svg>
                                        </p>
                                    </div>
                                <?php } ?>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link progress-card py-2 d-flex flex-column justify-content-between" data-toggle="tab" href="#form-block-3" role="tab" aria-controls="block3" aria-selected="true">
                            <div>
                                <svg width="13" height="14" viewBox="0 0 13 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.67188 1.4H8.32812C8.43984 1.4 8.53125 1.505 8.53125 1.63333V2.8H4.46875V1.63333C4.46875 1.505 4.56016 1.4 4.67188 1.4ZM3.25 1.63333V2.8H1.625C0.728711 2.8 0 3.63708 0 4.66667V7.46667H4.875H8.125H13V4.66667C13 3.63708 12.2713 2.8 11.375 2.8H9.75V1.63333C9.75 0.732083 9.1127 0 8.32812 0H4.67188C3.8873 0 3.25 0.732083 3.25 1.63333ZM13 8.4H8.125V9.33333C8.125 9.84958 7.76191 10.2667 7.3125 10.2667H5.6875C5.23809 10.2667 4.875 9.84958 4.875 9.33333V8.4H0V12.1333C0 13.1629 0.728711 14 1.625 14H11.375C12.2713 14 13 13.1629 13 12.1333V8.4Z" fill="black"/>
                                </svg>
                                <h6 class="pc-title pt-0">Experience Details</h6> <!-- Setup  -->
                                <hr class="my-1" style="border-top: 1px solid #9A9999;">
                            </div>
                            <div>
                                <!-- <p class="pc-content pt-1">Showcase your career journey and expertise.</p> -->
                                <?php // if(isset($data->step3) && $data->step3 == 3) {
                                if(is_array($emp_list) && count($emp_list) > 0 ){ ?>
                                    <div class="pt-2">
                                        <p class="d-flex align-items-center justify-content-end" style="gap: 6px;">
                                            <span class="pc-content">Completed</span>
                                            <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#379D0B"/>
                                            </svg>
                                        </p>
                                    </div>
                                <?php }  else { ?>
                                    <!-- pending -->
                                    <div class="pt-2">
                                        <p class="d-flex align-items-center justify-content-end" style="gap: 6px;">
                                            <span class="pc-content">Pending</span>
                                            <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#A7A7A7"/>
                                            </svg>
                                        </p>
                                    </div>
                                <?php } ?>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link progress-card py-2 d-flex flex-column justify-content-between"  data-toggle="tab" href="#form-block-4" role="tab" aria-controls="block4" aria-selected="true">
                            <div>
                                <svg width="18" height="12" viewBox="0 0 18 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.00022 0C8.77241 0 8.54741 0.0374926 8.33367 0.1098L0.444797 2.82266C0.177616 2.91639 0.000432897 3.15742 0.000432897 3.4279C0.000432897 3.69838 0.177616 3.93941 0.444797 4.03314L2.0732 4.59285C1.61196 5.28379 1.3504 6.10059 1.3504 6.96024V7.71277C1.3504 8.47334 1.04666 9.25801 0.723228 9.87664C0.54042 10.2248 0.3323 10.5676 0.0904307 10.8836C0.000432898 10.9987 -0.024879 11.1487 0.0257448 11.2853C0.0763686 11.4219 0.194491 11.5236 0.340737 11.5584L2.14069 11.9869C2.25882 12.0164 2.38538 11.995 2.48944 11.9334C2.5935 11.8718 2.66662 11.77 2.68912 11.6549C2.93099 10.5087 2.81005 9.48028 2.63006 8.74382C2.54006 8.36354 2.41912 7.97522 2.25038 7.61904V6.96024C2.25038 6.15147 2.53725 5.38823 3.03505 4.77764C3.39785 4.36254 3.86753 4.02778 4.41876 3.82157L8.83428 2.16922C9.0649 2.08352 9.32646 2.19064 9.41646 2.41024C9.50645 2.62984 9.39396 2.8789 9.16334 2.9646L4.74782 4.61695C4.39908 4.74818 4.09252 4.94903 3.84222 5.19541L8.33086 6.73797C8.5446 6.81027 8.7696 6.84777 8.9974 6.84777C9.22521 6.84777 9.45021 6.81027 9.66395 6.73797L17.5556 4.03314C17.8228 3.94208 18 3.69838 18 3.4279C18 3.15742 17.8228 2.91639 17.5556 2.82266L9.66676 0.1098C9.45302 0.0374926 9.22802 0 9.00022 0ZM3.60035 10.0695C3.60035 11.0148 6.01904 11.9976 9.00022 11.9976C11.9814 11.9976 14.4001 11.0148 14.4001 10.0695L13.9698 6.17558L9.97051 7.55209C9.65833 7.65921 9.32927 7.71277 9.00022 7.71277C8.67116 7.71277 8.3393 7.65921 8.02993 7.55209L4.03065 6.17558L3.60035 10.0695Z" fill="black"/>
                                </svg>
                                <h6 class="pc-title pt-0">Education Details</h6><!-- Setup  -->
                                <hr class="my-1" style="border-top: 1px solid #9A9999;">
                            </div>
                            <div>
                                <!-- <p class="pc-content pt-1" style="max-width: 133px;">Highlight your academic journey and credentials.</p> -->
                                <?php if(is_array($edu_list) && count($edu_list) > 0 ){ ?>
                                <div class="pt-2">
                                    <p class="d-flex align-items-center justify-content-end pr-1" style="gap: 6px;">
                                        <span class="pc-content">Completed</span>
                                        <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#379D0B"/>
                                        </svg>
                                    </p>
                                </div>

                                <?php } else { ?>
                                <!-- pending -->
                                <div class="pt-2">
                                    <p class="d-flex align-items-center justify-content-end pr-1" style="gap: 6px;">
                                        <span class="pc-content">Pending</span>
                                        <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#A7A7A7"/>
                                        </svg>
                                    </p>
                                </div>
                                <?php } ?>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link progress-card py-2 d-flex flex-column justify-content-between"  data-toggle="tab" href="#form-block-5" role="tab" aria-controls="block5" aria-selected="true">
                            <div>
                                <svg width="15" height="12" viewBox="0 0 15 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.25082 6C6.04659 6 6.80978 5.68393 7.37247 5.12132C7.93517 4.55871 8.25129 3.79565 8.25129 3C8.25129 2.20435 7.93517 1.44129 7.37247 0.87868C6.80978 0.316071 6.04659 0 5.25082 0C4.45505 0 3.69187 0.316071 3.12917 0.87868C2.56647 1.44129 2.25035 2.20435 2.25035 3C2.25035 3.79565 2.56647 4.55871 3.12917 5.12132C3.69187 5.68393 4.45505 6 5.25082 6ZM4.17956 7.125C1.8706 7.125 0 8.99531 0 11.3039C0 11.6883 0.311767 12 0.696203 12H7.56681C7.49414 11.7937 7.48007 11.5688 7.53399 11.3484L7.88561 9.93984C7.95124 9.675 8.0872 9.43594 8.27942 9.24375L9.2241 8.29922C8.47164 7.57266 7.4496 7.125 6.31974 7.125H4.17956ZM14.3882 5.52422C14.0225 5.15859 13.4294 5.15859 13.0614 5.52422L12.3722 6.21328L14.0366 7.87734L14.7257 7.18828C15.0914 6.82266 15.0914 6.22969 14.7257 5.86172L14.3882 5.52422ZM8.81153 9.77344C8.71542 9.86953 8.64744 9.98906 8.61463 10.1227L8.26301 11.5312C8.23019 11.6602 8.2677 11.7938 8.36146 11.8875C8.45523 11.9813 8.58884 12.0188 8.71777 11.9859L10.1266 11.6344C10.2579 11.6016 10.3797 11.5336 10.4759 11.4375L13.5045 8.40703L11.8401 6.74297L8.81153 9.77344Z" fill="black"/>
                                </svg>
                                <h6 class="pc-title pt-0">About, Hobbies & Interests</h6>
                                <hr class="my-1" style="border-top: 1px solid #9A9999;">
                            </div>
                            <div>
                                <!-- <p class="pc-content pt-1" style="max-width: 133px;">You have the control, Control What others see about you !</p> -->
                                <?php // if(isset($data->step5) && $data->step5 == 5) {
                                if($about_me != '' || $fun_fact != ''){
                                ?>
                                <div class="pt-2">
                                    <p class="d-flex align-items-center justify-content-end pr-1" style="gap: 6px;">
                                        <span class="pc-content">Completed</span>
                                        <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#379D0B"/>
                                        </svg>
                                    </p>
                                </div>
                                <?php } else { ?>
                                <!-- pending -->
                                <div class="pt-2">
                                    <p class="d-flex align-items-center justify-content-end pr-1" style="gap: 6px;">
                                        <span class="pc-content">Pending</span>
                                        <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#A7A7A7"/>
                                        </svg>
                                    </p>
                                </div>
                                <?php } ?>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link progress-card py-2 d-flex flex-column justify-content-between"  data-toggle="tab" href="#form-block-6" role="tab" aria-controls="block6" aria-selected="true">
                            <div>
                                <svg width="15" height="18" viewBox="0 0 15 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.82143 5.0625C4.82143 3.50859 6.02009 2.25 7.5 2.25C8.56808 2.25 9.48884 2.90391 9.92076 3.85664C10.1752 4.41914 10.8147 4.6582 11.3471 4.39102C11.8795 4.12383 12.1105 3.45234 11.856 2.89336C11.0826 1.18477 9.42522 0 7.5 0C4.83817 0 2.67857 2.26758 2.67857 5.0625V6.75H2.14286C0.960938 6.75 0 7.75898 0 9V15.75C0 16.991 0.960938 18 2.14286 18H12.8571C14.0391 18 15 16.991 15 15.75V9C15 7.75898 14.0391 6.75 12.8571 6.75H4.82143V5.0625Z" fill="black"/>
                                </svg>
                                <h6 class="pc-title pt-0">Privacy</h6> <!-- Setup your  -->
                                <hr class="my-1" style="border-top: 1px solid #9A9999;">
                            </div>
                            <div>
                                <!-- <p class="pc-content pt-1" style="max-width: 133px;">You have the control, Control What others see about you !</p> -->
                                <?php // if(isset($data->step6) && $data->step6 == 6) {
                                // if((isset($data->tao_unsubscribe_emails) && $data->tao_unsubscribe_emails == 1) ){ // || (isset($data->unlist_me_dir) || $data->unlist_me_dir == 1)
                                ?>
                                <div class="pt-2">
                                    <p class="d-flex align-items-center justify-content-end pr-1" style="gap: 6px;">
                                        <span class="pc-content">Completed</span>
                                        <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#379D0B"/>
                                        </svg>
                                    </p>
                                </div>
                                <?php // } else { ?>
                                <!-- pending -->
                                <!-- <div class="pt-2">
                                    <p class="d-flex align-items-center justify-content-end pr-1" style="gap: 6px;">
                                        <span class="pc-content">Pending</span>
                                        <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#A7A7A7"/>
                                        </svg>
                                    </p>
                                </div> -->
                                <?php // } ?>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div id="settings_loaderArea" class="text-center"></div>

    <?php if(isset($data->profile_complete) && $data->profile_complete ==1 ){ ?>
        <div class="container">
            <div class="all-set pl-lg-4">
                <svg width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5.57143 0C2.49844 0 0 2.49844 0 5.57143V33.4286C0 36.5016 2.49844 39 5.57143 39H33.4286C36.5016 39 39 36.5016 39 33.4286V5.57143C39 2.49844 36.5016 0 33.4286 0H5.57143ZM29.3371 15.4085L18.1942 26.5513C17.3759 27.3696 16.0527 27.3696 15.2431 26.5513L9.67165 20.9799C8.85335 20.1616 8.85335 18.8384 9.67165 18.0288C10.49 17.2192 11.8132 17.2105 12.6228 18.0288L16.7143 22.1203L26.3772 12.4487C27.1955 11.6304 28.5188 11.6304 29.3283 12.4487C30.1379 13.267 30.1467 14.5902 29.3283 15.3998L29.3371 15.4085Z" fill="#379D0B"/>
                </svg>
                You're all set! Join the event or Explore the site - finish the rest of your setup anytime !!
            </div>
        </div>
    <?php } ?>
    <div class="container d-flex flex-row pb-5" style="gap: 20px;">
        <ul class="nav nav-tabs p-main-tabs d-none d-xl-flex flex-column" id="myTab" role="tablist" style="position: sticky; top: 20px; height: 100%; width: 100%; max-width: 327px; min-width: 327px;">
            <li>
                <h6 class="p-nav-title nav-link">Your Settings</h6>
            </li>
            <!-- Tab Links -->
            <li class="nav-item">
                <a class="nav-link active py-2" data-toggle="tab" href="#form-block-1" role="tab" aria-controls="block1" aria-selected="true">
                    <div class="d-flex align-items-center" style="gap: 12px;">
                        <svg class="svg-color" width="13" height="13" viewBox="0 0 13 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.964 4.55636C13.0502 4.7943 12.9774 5.05958 12.7916 5.22915L11.6251 6.3067C11.6547 6.5337 11.6709 6.76617 11.6709 7.00137C11.6709 7.23657 11.6547 7.46904 11.6251 7.69603L12.7916 8.77359C12.9774 8.94315 13.0502 9.20844 12.964 9.44638C12.8454 9.77183 12.7027 10.0836 12.5383 10.3845L12.4117 10.606C12.2339 10.9068 12.0346 11.1912 11.8164 11.4593C11.6574 11.6562 11.3934 11.7218 11.1564 11.6452L9.65588 11.1612C9.2949 11.4429 8.89621 11.6781 8.47057 11.8558L8.13384 13.4175C8.07996 13.6663 7.89139 13.8633 7.64355 13.9043C7.2718 13.9672 6.88927 14 6.49865 14C6.10804 14 5.72551 13.9672 5.35375 13.9043C5.10592 13.8633 4.91735 13.6663 4.86347 13.4175L4.52673 11.8558C4.1011 11.6781 3.70241 11.4429 3.34143 11.1612L1.84363 11.648C1.60657 11.7246 1.34257 11.6562 1.18363 11.462C0.965426 11.194 0.766079 10.9096 0.588283 10.6087L0.46167 10.3872C0.297344 10.0863 0.154568 9.77457 0.0360376 9.44911C-0.0501666 9.21117 0.0225681 8.94589 0.208446 8.77632L1.3749 7.69877C1.34526 7.46904 1.3291 7.23657 1.3291 7.00137C1.3291 6.76617 1.34526 6.5337 1.3749 6.3067L0.208446 5.22915C0.0225681 5.05958 -0.0501666 4.7943 0.0360376 4.55636C0.154568 4.2309 0.297344 3.91913 0.46167 3.61828L0.588283 3.39676C0.766079 3.09592 0.965426 2.81149 1.18363 2.54347C1.34257 2.34655 1.60657 2.28091 1.84363 2.35749L3.34412 2.84157C3.7051 2.55988 4.1038 2.32467 4.52943 2.1469L4.86616 0.585271C4.92004 0.336394 5.10861 0.13948 5.35645 0.0984567C5.7282 0.0328189 6.11073 0 6.50135 0C6.89196 0 7.27449 0.0328189 7.64625 0.0957218C7.89408 0.136745 8.08265 0.333659 8.13653 0.582536L8.47327 2.14417C8.8989 2.32194 9.29759 2.55714 9.65857 2.83884L11.1591 2.35476C11.3961 2.27818 11.6601 2.34655 11.8191 2.54073C12.0373 2.80875 12.2366 3.09318 12.4144 3.39402L12.541 3.61555C12.7054 3.91639 12.8481 4.22817 12.9667 4.55362L12.964 4.55636ZM6.50135 9.18929C7.07292 9.18929 7.62108 8.95878 8.02524 8.54847C8.4294 8.13815 8.65645 7.58164 8.65645 7.00137C8.65645 6.42109 8.4294 5.86459 8.02524 5.45427C7.62108 5.04395 7.07292 4.81344 6.50135 4.81344C5.92978 4.81344 5.38162 5.04395 4.97746 5.45427C4.5733 5.86459 4.34624 6.42109 4.34624 7.00137C4.34624 7.58164 4.5733 8.13815 4.97746 8.54847C5.38162 8.95878 5.92978 9.18929 6.50135 9.18929Z"/>
                        </svg>
                        <h6 class="pc-title pt-0">General Settings</h6>
                        <?php if((isset($data->profile_complete) && $data->profile_complete ==1 ) ||
                        (isset($data->step1) && $data->step1 == 1)) { ?>
                            <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z"
                                fill="#379D0B"/>
                            </svg>
                        <?php }else{ ?>
                            <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#A7A7A7"/>
                            </svg>
                        <?php } ?>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-2" data-toggle="tab" href="#form-block-2" role="tab" aria-controls="block2" aria-selected="true">
                    <div class="d-flex align-items-center" style="gap: 12px;">
                        <svg class="svg-color" width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.5 0C8.81773 4.60086 10.4843 6.29889 15 7.64148C10.4843 8.98407 8.81773 10.6821 7.5 15.283C6.18227 10.6828 4.51568 8.98407 0 7.64148C4.51568 6.29889 6.18227 4.6016 7.5 0Z" />
                        </svg>
                        <h6 class="pc-title pt-0">Status</h6>
                        <?php // if(isset($data->step2) && $data->step2 == 2) {
                        if( isset( $data->skill ) && $data->skill){
                        ?>
                            <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#379D0B"/>
                            </svg>
                        <?php } else { ?>
                            <!-- pending -->
                            <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#A7A7A7"/>
                            </svg>
                        <?php } ?>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-2" data-toggle="tab" href="#form-block-3" role="tab" aria-controls="block3" aria-selected="true">
                    <div class="d-flex align-items-center" style="gap: 12px;">
                        <svg class="svg-color" width="13" height="14" viewBox="0 0 13 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.67188 1.4H8.32812C8.43984 1.4 8.53125 1.505 8.53125 1.63333V2.8H4.46875V1.63333C4.46875 1.505 4.56016 1.4 4.67188 1.4ZM3.25 1.63333V2.8H1.625C0.728711 2.8 0 3.63708 0 4.66667V7.46667H4.875H8.125H13V4.66667C13 3.63708 12.2713 2.8 11.375 2.8H9.75V1.63333C9.75 0.732083 9.1127 0 8.32812 0H4.67188C3.8873 0 3.25 0.732083 3.25 1.63333ZM13 8.4H8.125V9.33333C8.125 9.84958 7.76191 10.2667 7.3125 10.2667H5.6875C5.23809 10.2667 4.875 9.84958 4.875 9.33333V8.4H0V12.1333C0 13.1629 0.728711 14 1.625 14H11.375C12.2713 14 13 13.1629 13 12.1333V8.4Z"/>
                        </svg>
                        <h6 class="pc-title pt-0">Experience Details</h6> <!-- Setup  -->
                        <?php // if(isset($data->step3) && $data->step3 == 3) {
                        if(is_array($emp_list) && count($emp_list) > 0 ){ ?>
                            <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#379D0B"/>
                            </svg>
                        <?php }  else { ?>
                            <!-- pending -->
                            <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#A7A7A7"/>
                            </svg>
                        <?php } ?>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-2"  data-toggle="tab" href="#form-block-4" role="tab" aria-controls="block4" aria-selected="true">
                    <div class="d-flex align-items-center" style="gap: 12px;">
                        <svg class="svg-color" width="18" height="12" viewBox="0 0 18 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.00022 0C8.77241 0 8.54741 0.0374926 8.33367 0.1098L0.444797 2.82266C0.177616 2.91639 0.000432897 3.15742 0.000432897 3.4279C0.000432897 3.69838 0.177616 3.93941 0.444797 4.03314L2.0732 4.59285C1.61196 5.28379 1.3504 6.10059 1.3504 6.96024V7.71277C1.3504 8.47334 1.04666 9.25801 0.723228 9.87664C0.54042 10.2248 0.3323 10.5676 0.0904307 10.8836C0.000432898 10.9987 -0.024879 11.1487 0.0257448 11.2853C0.0763686 11.4219 0.194491 11.5236 0.340737 11.5584L2.14069 11.9869C2.25882 12.0164 2.38538 11.995 2.48944 11.9334C2.5935 11.8718 2.66662 11.77 2.68912 11.6549C2.93099 10.5087 2.81005 9.48028 2.63006 8.74382C2.54006 8.36354 2.41912 7.97522 2.25038 7.61904V6.96024C2.25038 6.15147 2.53725 5.38823 3.03505 4.77764C3.39785 4.36254 3.86753 4.02778 4.41876 3.82157L8.83428 2.16922C9.0649 2.08352 9.32646 2.19064 9.41646 2.41024C9.50645 2.62984 9.39396 2.8789 9.16334 2.9646L4.74782 4.61695C4.39908 4.74818 4.09252 4.94903 3.84222 5.19541L8.33086 6.73797C8.5446 6.81027 8.7696 6.84777 8.9974 6.84777C9.22521 6.84777 9.45021 6.81027 9.66395 6.73797L17.5556 4.03314C17.8228 3.94208 18 3.69838 18 3.4279C18 3.15742 17.8228 2.91639 17.5556 2.82266L9.66676 0.1098C9.45302 0.0374926 9.22802 0 9.00022 0ZM3.60035 10.0695C3.60035 11.0148 6.01904 11.9976 9.00022 11.9976C11.9814 11.9976 14.4001 11.0148 14.4001 10.0695L13.9698 6.17558L9.97051 7.55209C9.65833 7.65921 9.32927 7.71277 9.00022 7.71277C8.67116 7.71277 8.3393 7.65921 8.02993 7.55209L4.03065 6.17558L3.60035 10.0695Z"/>
                        </svg>
                        <h6 class="pc-title pt-0">Education Details</h6>

                        <?php if(is_array($edu_list) && count($edu_list) > 0 ){ ?>
                            <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#379D0B"/>
                            </svg>

                        <?php } else { ?>
                        <!-- pending -->
                            <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#A7A7A7"/>
                            </svg>
                        <?php } ?>

                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-2"  data-toggle="tab" href="#form-block-5" role="tab" aria-controls="block5" aria-selected="true">
                    <div class="d-flex align-items-center" style="gap: 12px;">
                        <svg class="svg-color" width="15" height="12" viewBox="0 0 15 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5.25082 6C6.04659 6 6.80978 5.68393 7.37247 5.12132C7.93517 4.55871 8.25129 3.79565 8.25129 3C8.25129 2.20435 7.93517 1.44129 7.37247 0.87868C6.80978 0.316071 6.04659 0 5.25082 0C4.45505 0 3.69187 0.316071 3.12917 0.87868C2.56647 1.44129 2.25035 2.20435 2.25035 3C2.25035 3.79565 2.56647 4.55871 3.12917 5.12132C3.69187 5.68393 4.45505 6 5.25082 6ZM4.17956 7.125C1.8706 7.125 0 8.99531 0 11.3039C0 11.6883 0.311767 12 0.696203 12H7.56681C7.49414 11.7937 7.48007 11.5688 7.53399 11.3484L7.88561 9.93984C7.95124 9.675 8.0872 9.43594 8.27942 9.24375L9.2241 8.29922C8.47164 7.57266 7.4496 7.125 6.31974 7.125H4.17956ZM14.3882 5.52422C14.0225 5.15859 13.4294 5.15859 13.0614 5.52422L12.3722 6.21328L14.0366 7.87734L14.7257 7.18828C15.0914 6.82266 15.0914 6.22969 14.7257 5.86172L14.3882 5.52422ZM8.81153 9.77344C8.71542 9.86953 8.64744 9.98906 8.61463 10.1227L8.26301 11.5312C8.23019 11.6602 8.2677 11.7938 8.36146 11.8875C8.45523 11.9813 8.58884 12.0188 8.71777 11.9859L10.1266 11.6344C10.2579 11.6016 10.3797 11.5336 10.4759 11.4375L13.5045 8.40703L11.8401 6.74297L8.81153 9.77344Z"/>
                        </svg>
                        <h6 class="pc-title pt-0">About, Hobbies & Interests</h6>
                        <?php // if(isset($data->step5) && $data->step5 == 5) {
                        if($about_me != '' || $fun_fact != ''){
                        ?>
                            <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#379D0B"/>
                            </svg>
                        <?php } else { ?>
                        <!-- pending -->
                            <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#A7A7A7"/>
                            </svg>
                        <?php } ?>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-2"  data-toggle="tab" href="#form-block-6" role="tab" aria-controls="block6" aria-selected="true">
                    <div class="d-flex align-items-center" style="gap: 12px;">
                        <svg class="svg-color" width="15" height="18" viewBox="0 0 15 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.82143 5.0625C4.82143 3.50859 6.02009 2.25 7.5 2.25C8.56808 2.25 9.48884 2.90391 9.92076 3.85664C10.1752 4.41914 10.8147 4.6582 11.3471 4.39102C11.8795 4.12383 12.1105 3.45234 11.856 2.89336C11.0826 1.18477 9.42522 0 7.5 0C4.83817 0 2.67857 2.26758 2.67857 5.0625V6.75H2.14286C0.960938 6.75 0 7.75898 0 9V15.75C0 16.991 0.960938 18 2.14286 18H12.8571C14.0391 18 15 16.991 15 15.75V9C15 7.75898 14.0391 6.75 12.8571 6.75H4.82143V5.0625Z"/>
                        </svg>
                        <h6 class="pc-title pt-0">Privacy</h6> <!-- Setup your  -->

                        <svg style="min-width: fit-content;" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#379D0B"/>
                        </svg>

                    </div>
                </a>
            </li>


            <li>
                <h6 class="p-nav-title px-3">Your Space</h6>
            </li>
             <li class="nav-item d-none">
                <a class="nav-link py-2"  data-toggle="tab" href="#form-block-7" role="tab" aria-controls="block7" aria-selected="true">
                    <div class="d-flex align-items-center" style="gap: 12px;">
                        <svg class="svg-color" width="19" height="16" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.85 3.8C2.85 2.79218 3.25036 1.82563 3.96299 1.11299C4.67563 0.400356 5.64218 0 6.65 0C7.65782 0 8.62437 0.400356 9.33701 1.11299C10.0496 1.82563 10.45 2.79218 10.45 3.8C10.45 4.80782 10.0496 5.77437 9.33701 6.48701C8.62437 7.19964 7.65782 7.6 6.65 7.6C5.64218 7.6 4.67563 7.19964 3.96299 6.48701C3.25036 5.77437 2.85 4.80782 2.85 3.8ZM0 14.3183C0 11.3941 2.36906 9.025 5.29328 9.025H8.00672C10.9309 9.025 13.3 11.3941 13.3 14.3183C13.3 14.8052 12.9052 15.2 12.4183 15.2H0.881719C0.394844 15.2 0 14.8052 0 14.3183ZM18.0886 15.2H13.9947C14.155 14.9209 14.25 14.5973 14.25 14.25V14.0125C14.25 12.2105 13.4455 10.5925 12.1778 9.50594C12.2491 9.50297 12.3173 9.5 12.3886 9.5H14.2114C16.8566 9.5 19 11.6434 19 14.2886C19 14.7933 18.5903 15.2 18.0886 15.2ZM12.825 7.6C11.9047 7.6 11.0734 7.22594 10.4708 6.62328C11.0556 5.83359 11.4 4.85687 11.4 3.8C11.4 3.00437 11.2041 2.25328 10.8567 1.59422C11.4089 1.19047 12.0887 0.95 12.825 0.95C14.6627 0.95 16.15 2.43734 16.15 4.275C16.15 6.11266 14.6627 7.6 12.825 7.6Z"/>
                        </svg>
                        <h6 class="pc-title pt-0">My Network</h6>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-2 your_space" href="<?php echo $taoh_home_url."/jobs"; ?>" target="_blank" >
                    <div class="d-flex align-items-center" style="gap: 12px;">
                        <svg width="13" height="17" viewBox="0 0 13 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.625 0.5H11.375C12.0052 0.5 12.5 0.999118 12.5 1.59375V16.1934C12.4999 16.3528 12.3642 16.4998 12.1777 16.5C12.1419 16.5 12.107 16.4951 12.0752 16.4854L11.9883 16.4434L11.9863 16.4414L6.7832 12.8691L6.5 12.6748L6.2168 12.8691L1.01367 16.4414L1.01172 16.4434C0.960114 16.4791 0.893976 16.5 0.822266 16.5C0.635841 16.4998 0.50011 16.3528 0.5 16.1934V1.59375C0.5 0.999118 0.99481 0.5 1.625 0.5Z" fill="black" stroke="black"/>
                        </svg>
                        <h6 class="pc-title pt-0">My Jobs</h6>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-2 your_space" href="<?php echo $taoh_home_url."/events?creator=1"; ?>" target="_blank" >
                    <div class="d-flex align-items-center" style="gap: 12px;">
                        <svg style="min-width: fit-content;" width="15" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.9688 6.40625V15.4062C14.9688 16.2686 14.2628 16.9688 13.3926 16.9688H1.60742C0.737157 16.9688 0.03125 16.2686 0.03125 15.4062V6.40625H14.9688ZM11.0371 8.97949C10.731 8.67916 10.2479 8.65642 9.92188 8.92188L9.8584 8.97949L6.69922 12.1113L5.14844 10.5732C4.84213 10.2695 4.35819 10.2498 4.03223 10.5156L3.96875 10.5732C3.66571 10.8772 3.6437 11.3572 3.91211 11.6807L3.96875 11.7432L6.1123 13.8682C6.41882 14.1718 6.90266 14.191 7.22852 13.9248L7.29102 13.8682L11.0381 10.1494C11.3445 9.84544 11.3631 9.36537 11.0947 9.04199L11.0371 8.97949ZM10.7139 0.03125C11.2895 0.03125 11.7549 0.49231 11.7549 1.0625V2.15625H13.3926C14.2628 2.15625 14.9688 2.85637 14.9688 3.71875V5.28125H0.03125V3.71875C0.03125 2.85637 0.737157 2.15625 1.60742 2.15625H3.24512V1.0625C3.24512 0.49231 3.71051 0.03125 4.28613 0.03125C4.86156 0.0314735 5.32617 0.492448 5.32617 1.0625V2.15625H9.67383V1.0625C9.67383 0.492448 10.1384 0.031473 10.7139 0.03125Z" fill="black" stroke="black" stroke-width="0.0625"/>
                        </svg>
                        <h6 class="pc-title pt-0">My Events</h6>
                    </div>
                </a>
            </li>


        </ul>
        <div class="profile-right px-4" style="max-width: 1032px;  border: 1px solid #d3d3d3;">
                <!-- Tab Content -->
                <div class="tab-content px-0" id="myTabContent">
                    <div class="tab-pane fade show active fb-1" id="form-block-1" role="tabpanel" aria-labelledby="block-1-tab" style="max-width: 953px;">
                        <?php
                        if($indx_db_settings==1){ ?>
                            <form id="setting_form" method="post" action="#" class="">
                            <input type="hidden" name="taoh_session" id="taoh_session" value="settings">
                        <?php }else{ ?>
                            <form id="setting_form" method="post" action="<?php echo TAOH_ACTION_URL.'/settings' ?>" class="">
                            <input type="hidden" name="taoh_session" id="taoh_session" value="old">
                        <?php
                        }
                        ?>
                        <input type="hidden" value="<?php echo TAOH_OPS_CODE; ?>" name="opscode">


                        <div>

                            <div class="d-flex align-items-center d-xl-none p-3" style="gap: 12px; margin: 0 -24px; background-color: #2557A7;">
                                <svg style="min-width: fit-content;" width="16" height="16" viewBox="0 0 13 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.964 4.55636C13.0502 4.7943 12.9774 5.05958 12.7916 5.22915L11.6251 6.3067C11.6547 6.5337 11.6709 6.76617 11.6709 7.00137C11.6709 7.23657 11.6547 7.46904 11.6251 7.69603L12.7916 8.77359C12.9774 8.94315 13.0502 9.20844 12.964 9.44638C12.8454 9.77183 12.7027 10.0836 12.5383 10.3845L12.4117 10.606C12.2339 10.9068 12.0346 11.1912 11.8164 11.4593C11.6574 11.6562 11.3934 11.7218 11.1564 11.6452L9.65588 11.1612C9.2949 11.4429 8.89621 11.6781 8.47057 11.8558L8.13384 13.4175C8.07996 13.6663 7.89139 13.8633 7.64355 13.9043C7.2718 13.9672 6.88927 14 6.49865 14C6.10804 14 5.72551 13.9672 5.35375 13.9043C5.10592 13.8633 4.91735 13.6663 4.86347 13.4175L4.52673 11.8558C4.1011 11.6781 3.70241 11.4429 3.34143 11.1612L1.84363 11.648C1.60657 11.7246 1.34257 11.6562 1.18363 11.462C0.965426 11.194 0.766079 10.9096 0.588283 10.6087L0.46167 10.3872C0.297344 10.0863 0.154568 9.77457 0.0360376 9.44911C-0.0501666 9.21117 0.0225681 8.94589 0.208446 8.77632L1.3749 7.69877C1.34526 7.46904 1.3291 7.23657 1.3291 7.00137C1.3291 6.76617 1.34526 6.5337 1.3749 6.3067L0.208446 5.22915C0.0225681 5.05958 -0.0501666 4.7943 0.0360376 4.55636C0.154568 4.2309 0.297344 3.91913 0.46167 3.61828L0.588283 3.39676C0.766079 3.09592 0.965426 2.81149 1.18363 2.54347C1.34257 2.34655 1.60657 2.28091 1.84363 2.35749L3.34412 2.84157C3.7051 2.55988 4.1038 2.32467 4.52943 2.1469L4.86616 0.585271C4.92004 0.336394 5.10861 0.13948 5.35645 0.0984567C5.7282 0.0328189 6.11073 0 6.50135 0C6.89196 0 7.27449 0.0328189 7.64625 0.0957218C7.89408 0.136745 8.08265 0.333659 8.13653 0.582536L8.47327 2.14417C8.8989 2.32194 9.29759 2.55714 9.65857 2.83884L11.1591 2.35476C11.3961 2.27818 11.6601 2.34655 11.8191 2.54073C12.0373 2.80875 12.2366 3.09318 12.4144 3.39402L12.541 3.61555C12.7054 3.91639 12.8481 4.22817 12.9667 4.55362L12.964 4.55636ZM6.50135 9.18929C7.07292 9.18929 7.62108 8.95878 8.02524 8.54847C8.4294 8.13815 8.65645 7.58164 8.65645 7.00137C8.65645 6.42109 8.4294 5.86459 8.02524 5.45427C7.62108 5.04395 7.07292 4.81344 6.50135 4.81344C5.92978 4.81344 5.38162 5.04395 4.97746 5.45427C4.5733 5.86459 4.34624 6.42109 4.34624 7.00137C4.34624 7.58164 4.5733 8.13815 4.97746 8.54847C5.38162 8.95878 5.92978 9.18929 6.50135 9.18929Z" fill="#ffffff"/>
                                </svg>
                                <h6 class="p-field-title text-white">General Settings</h6>
                                <?php if((isset($data->profile_complete) && $data->profile_complete ==1 ) ||
                                (isset($data->step1) && $data->step1 == 1)) { ?>
                                    <svg style="min-width: fit-content;" width="18" height="18" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z"
                                        fill="#ffffff"/>
                                    </svg>
                                <?php }else{ ?>
                                    <!-- <svg style="min-width: fit-content;" width="18" height="18" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#A7A7A7"/>
                                    </svg> -->
                                <?php } ?>
                            </div>


                            <h5 class="p-field-title py-3">Personal Information</h5>
                            <hr class="mt-0" style="border-top: 1px solid #D3D3D3;">

                            <div class="row mt-4">
                                <div class="form-group col-lg-5">
                                    <label class="text-label" for="">First Name <span class="text-req">*</span></label>
                                    <input type="text" name="fname" id="fname" value="<?php echo @$data->fname;?>" class="form-control">
                                </div>
                                <div class="form-group col-lg-5">
                                    <label class="text-label" for="">Last Name <span class="text-req">*</span></label>
                                    <input type="text" name="lname" id="lname" value="<?php echo @$data->lname;?>" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-5">
                                    <label class="text-label" for="">Email <span class="text-req">*</span></label>
                                    <input type="email" name="email" id="email" value="<?php echo @$data->email;?>" class="form-control">
                                </div>
                                <div class="form-group col-lg-5">
                                    <!-- <label class="text-label d-flex align-items-center flex-wrap" style="gap: 4px;" for=""><span class="text-nowrap">Contact Number</span><span class="ml-2" style="font-size: 13px;"> (Enter number with country code)</span></label> -->
                                   <label class="text-label d-flex align-items-center text-nowrap" style="gap: 4px;" for=""><!-- <span class="text-nowrap"> -->Contact Number<!-- </span>  --><span class="ml-2" style="font-size: 13px;"> (Enter number with country code)</span></label>
                                    <input  class="form-control form--control" id="phone" type="text" oninput="this.value = this.value.replace(/[^0-9+]/g, '');" value="<?php echo @$data->phone_number; ?>" name="phone_number">
                                    <input type="hidden" value="<?php echo $country_name; ?>" class="country_name" name="country_name">
                                    <p id="error-message"></p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h5 class="p-field-title">Public Information</h5>
                            <hr style="border-top: 1px solid #D3D3D3;">
                            <div>
                                <div class="form-group">
                                    <label for="" class="text-label mb-2">My avatar</label>
                                    <div class="d-flex flex-wrap" style="gap: 12px;">
                                        <div class="profile-image" id="move_avatar" style="<?php if(isset($data->avatar_image) && $data->avatar_image !=''){ echo "display:none"; } ?>">
                                            <?php echo avatar_select(@$data->avatar); ?>
                                        </div>
                                        <span class="text-danger" id="avatar-error"></span>
                                        <div class="avatar-container" style="<?php if(!isset($data->avatar_image) || $data->avatar_image ==''){ echo "display:none"; }?>">
                                            <div class="avatar_settings" >
                                                <?php if(isset($data->avatar_image) && $data->avatar_image !=''){

                                                        ?>
                                                    <img src="<?php echo $data->avatar_image; ?>" alt="Avatar">
                                                    <div id="removeImage"  class="delete-icon"></div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if(TAOH_PROFILE_PICTURE_UPLOAD){ ?>
                                <p class="text-center my-2" style="color: #000000; font-weight: 500; max-width: 110px;">OR</p>

                                <label for="" class="text-label">Upload Profile Picture</label>
                                <div class="row">
                                    <div class="form-group col-lg-5">
                                        <input type="file" class="custom-file-input form-control pt-3" id="custom_avatar" accept=".jpg, .jpeg, .png" name="" style="cursor: pointer;"> <!-- image/* -->
                                        <label class="custom-file-label av_file" for="customFile">Choose file</label>
                                        <p id="av_error1" style="display:none; color:#FF0000;">
                                            Invalid Format! Format Must Be JPG,JPEG or PNG.
                                        </p>
                                        <input type="hidden" value="<?php echo (isset($data->avatar_image) && $data->avatar_image !='')?$data->avatar_image:'' ?>" class="avatar_image" name="avatar_image">
                                        <input type="hidden" id="avt_img_delete" name="avt_img_delete">
                                    </div>

                                </div>
                                <?php } ?>
                                <div class="row">
                                    <div class="form-group col-lg-5">
                                        <label for="" class="text-label">My Public Chat Name <span class="text-req">*</span></label>
                                        <input  class="form-control form--control" required type="text"  pattern=".*[A-Za-z0-9].*"
                                        value="<?php
                                        echo @$data->chat_name; ?>" name="chat_name" id="chat_name">
                                    </div>
                                    <div class="form-group col-lg-6">
                                    <?php
                                                            if(isset($data->profile_complete)
                                                            && $data->profile_complete == 0 && isset($data->fname) && $data->fname == TAOH_SITE_NAME_SLUG){
                                                                $data->type = '';
                                                            }
                                                        ?>

                                        <label for="" class="text-label">My Profile Type <span class="text-req">*</span></label>
                                        <div class="p_type btn-group btn-group-toggle w-100" data-toggle="buttons">
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
                                <div class="row">
                                    <div class="form-group col-lg-5">
                                        <label for="" class="text-label">My City <span class="text-req">*</span></label>
                                        <?php echo field_location(@$data->coordinates,@$data->full_location, @$data->geohash ,'', 1); ?>
                                    </div>
                                    <div class="form-group col-lg-5">
                                        <label for="" class="text-label">Time zone <span class="text-req">*</span></label>
                                        <?php echo field_time_zone(@$data->local_timezone, 1); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-5">
                                        <label for="" class="text-label">Company Name <span class="text-req">*</span></label>
                                        <?php echo field_company( ( isset( $data->company ) && $data->company ) ? $data->company: '' , 1 ); ?>
                                    </div>
                                    <div class="form-group col-lg-5">
                                        <label for="" class="text-label">Role <span class="text-req">*</span></label>
                                        <?php echo field_role( ( isset( $data->title ) && $data->title ) ? $data->title:'' , 1 ); ?>
                                    </div>
                                    <div class="form-group col-lg-12 d-flex align-items-center" style="gap: 6px;">
                                        <input type="checkbox" name="currently_working_on" id="currently_working_on" value="1" <?php echo (@$data->currently_working_on == 1) ?'checked': ''; ?>>
                                        <label for="currently_working_on" class="text-label mb-0">I currently work here</label>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <input type="hidden" name="taoh_ptoken" id="taoh_ptoken" value="<?php echo $data->ptoken; ?>">
                        <input type="hidden" name="profile_complete" id="profile_complete" value="1"/>
                        <input type="hidden" name="step1" id="step1" value="1"/>
                        <input type="hidden" name="login_type" id="login_type" value="<?php echo $login_type; ?>"/>
                        <div style="text-align: right;">
                            <button id="save_changes" name="save_changes"  type="submit" class="btn s-btn px-3 mb-4 mt-3">Save</button> <!--  and Continue -->
                        </div>

                        </form>

                         <!-- for mobile screen -->
                        <!-- Mobile Navigation Buttons -->
                        <div class="d-flex justify-content-between d-xl-none mb-4">
                            <button type="button" class="btn tab-nav-btn ml-auto next-btn" data-direction="next">Next</button>
                        </div>
                    </div>

                    <!-- frm-blc-2 -->
                    <div class="tab-pane pb-5 fade" id="form-block-2" role="tabpanel" aria-labelledby="block-2-tab" >


                        <div class="d-flex align-items-center d-xl-none p-3" style="gap: 12px; margin: 0 -24px; background-color: #2557A7;">
                            <svg style="min-width: fit-content;" width="16" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.5 0C8.81773 4.60086 10.4843 6.29889 15 7.64148C10.4843 8.98407 8.81773 10.6821 7.5 15.283C6.18227 10.6828 4.51568 8.98407 0 7.64148C4.51568 6.29889 6.18227 4.6016 7.5 0Z" fill="#ffffff" />
                            </svg>
                            <h6 class="p-field-title text-white">Status</h6>
                            <?php // if(isset($data->step2) && $data->step2 == 2) {
                                if( isset( $data->skill ) && $data->skill){
                            ?>
                                <svg style="min-width: fit-content;" width="18" height="18" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z"
                                    fill="#ffffff"/>
                                </svg>
                            <?php }else{ ?>
                                <!-- <svg style="min-width: fit-content;" width="18" height="18" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#A7A7A7"/>
                                </svg> -->
                            <?php } ?>
                        </div>


                        <form name="step2_form" id="step2_form" action="" style="max-width: 953px;">

                            <div class="form-group">
                                <h4 class="p-field-title py-3">What are your Core Skills <span class="text-req">&nbsp; * &nbsp;</span> (Choose from the suggested list for better results)</h4>
                                <hr class="mt-0" style="border-top: 1px solid #D3D3D3;">
                                <div class="row">
                                    <div class="col-lg-10">
                                        <?php echo field_skill( ( isset( $data->skill ) && $data->skill ) ? $data->skill:'' , 1); ?>
                                    </div>
                                </div>

                            </div>
                        <!-- <hr class="my-4" style="border-top: 1px solid #d3d3d3;"> -->

                        <?php
                            if ($show_name_slug_information){
                                ?>


                        <div>
                            <h5 class="p-field-title mt-5"><?php echo (defined('TAOH_WERTUAL_NAME_SLUG') ? ucfirst(TAOH_WERTUAL_NAME_SLUG) . ' ' : '') . 'Information' ?></h5>
                            <hr style="border-top: 1px solid #D3D3D3;">

                            <div class="row rm-p-b">

                                <?php
                                    foreach ($taoh_user_keywords as $key => $value) {
                                        if (isset($value['enable']) && $value['enable'] == 'true') {
                                            $data_keywords = (array)($data->keywords ?? []);

                                            echo '<div class="col-lg-5 form-group">';
                                            echo '<label for="select-' . $key . '" class="text-label">' . $value['label'];
                                            if ($value['required'] == 'true') echo '<span class="text-req"> * </span>';
                                            echo '</label>';
                                            echo '<select name="' . $key . '" id="select-' . $key . '" class="form-control form-select" autocomplete="off" ';
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


                                    ?>
                            </div>
                        </div>

                        <!-- <div class="profile-card-list mb-5">
                            <div class="heading px-4 px-lg-5">
                                <h5>
                                    <?php echo (defined('TAOH_WERTUAL_NAME_SLUG') ? ucfirst(TAOH_WERTUAL_NAME_SLUG) . ' ' : '') . 'Information' ?>
                                </h5>
                            </div>


                            <div class="row mx-0 pt-5 pb-4 px-lg-4 rm-p-b">

                                <?php
                                    foreach ($taoh_user_keywords as $key => $value) {
                                        if (isset($value['enable']) && $value['enable'] == 'true') {
                                            $data_keywords = (array)($data->keywords ?? []);

                                            echo '<div class="col-lg-4 form-group">';
                                            echo '<label for="select-' . $key . '" class="text-label">' . $value['label'];
                                            if ($value['required'] == 'true') echo '<span class="text-req"> * </span>';
                                            echo '</label>';
                                            echo '<select name="' . $key . '" id="select-' . $key . '" class="form-control form-select" autocomplete="off" ';
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


                                    ?>
                            </div>
                        </div> -->
                            <?php
                            }
                            ?>

                        <h2 class="p-field-title mt-4">Select the tags that suits you in each category <!-- <span class="req">*</span> --></h2>
                        <hr style="border-top: 1px solid #D3D3D3;">

                        <ul class="nav nav-tabs my-4 category-tabs" id="myTab" role="tablist" style="gap: 12px;">
                            <!-- Tab Links -->
                            <li class="nav-item">
                                <a class="nav-link category-tab active" data-toggle="tab" href="#hiring-talent" role="tab" aria-controls="hiring-talent" aria-selected="true">
                                    Hiring & Talent
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link category-tab" data-toggle="tab" href="#career-navigation" role="tab" aria-controls="career-navigation" aria-selected="true">
                                    Career Navigation
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link category-tab" data-toggle="tab" href="#growth-exchange" role="tab" aria-controls="growth-exchange" aria-selected="true">
                                    Growth & Exchange
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link category-tab"  data-toggle="tab" href="#collaboration-exchange" role="tab" aria-controls="collaboration-exchange" aria-selected="true">
                                    Collaboration & Exchange
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link category-tab"  data-toggle="tab" href="#startup-funding" role="tab" aria-controls="startup-funding" aria-selected="true">
                                    Startup & Funding
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content px-0" id="myTabContent">
                            <?php foreach($tag_category as $category_key=>$categories){ ?>
                                <div class="tab-pane <?php echo ($category_key == 'hiring-talent' ) ? 'active show' : 'fade' ; ?>" id="<?php echo $category_key; ?>" role="tabpanel" aria-labelledby="<?php echo $category_key; ?>">
                                    <div class="tags" style="max-width: 986px;">
                                        <?php foreach($categories as $ckey=>$category){ ?>
                                            <input  <?php if(isset($data->tags) && in_array($category,$data->tags)) { echo 'checked="true"';} ?> type="checkbox" id="<?php echo $category; ?>" name="tags[]" value="<?php echo $category; ?>">
                                            <label for="<?php echo $category; ?>"><?php echo $category; ?></label>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <p class="sel-status" id="selection-status">You have selected 0 out of 5 Profile Tags! Please select 5 More or you can proceed from <a href="#" style="text-decoration: underline;">here.</a></p>
                        <div class="selected-tags mb-3" id="selected-tags"></div>
<?php // echo "<pre>"; print_r($data); echo "</pre>"; ?>
                        <div class="tab-content px-0" id="myTabForm">
                            <?php foreach($tag_category_form as $category_key=>$category_form){
                                // echo "<br> category_key:  ". $category_key; ?>

                                <div class="tab-pane fade" id="<?php echo 'frm_'.str_replace(" ","",$category_key); ?>" role="tabpanel" aria-labelledby="<?php echo $category_key; ?>">

                                <fieldset class="form-group field-set" style="max-width: 986px; border: 2px solid #d3d3d3; padding: 20px; border-radius: 5px;">
                                    <legend class="text-label px-2" style="color: #2557A7; width: fit-content;"><?php echo $category_key; ?></legend>


                                    <div class="form-group mb-0" style="max-width: 986px;">
                                        <?php foreach($category_form as $ckey=>$cform){
                                            // echo "<br> Ckey : ".$ckey;
                                            if($ckey %2 == 0){ ?>
                                                <div class="row">
                                            <?php }
                                                // echo "<pre>"; print_r($cform); echo "</pre>";
                                                if($cform['field_type'] == 'text'){
                                            ?>
                                            <div class="form-group col-lg-5">
                                                <label class="text-label" for="<?php echo $cform['field_name']; ?>"><?php echo $cform['field_value']; ?></label>
                                                <input class="form-control" type="text" id="<?php echo $cform['field_name']; ?>" name="<?php echo $cform['field_name']; ?>" value="<?php echo (isset($data->{$cform['field_name']})) ? $data->{$cform['field_name']} : ''; ?>">
                                            </div>
                                            <?php }elseif($cform['field_type'] == 'dropdown'){ ?>
                                                <div class="form-group col-lg-5">
                                                    <label class="text-label" for="<?php echo $cform['field_name']; ?>"><?php echo $cform['field_value']; ?></label>
                                                    <select class="form-control" name="<?php echo $cform['field_name']; ?>">
                                                        <option value="">--Select--</option>
                                                        <?php foreach($cform['dropdown_value'] as $dkey=>$dval){ ?>
                                                            <option value="<?php echo $dval; ?>" <?php echo ((isset($data->{$cform['field_name']})) && $data->{$cform['field_name']} == $dval) ? 'selected' : ''; ?>  ><?php echo $dval; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            <?php } ?>

                                            <?php if($ckey %2 != 0 || $ckey == count($category_form)-1){ ?>
                                                </div>
                                            <?php }
                                        } ?>
                                    </div>

                                </fieldset>
                                </div>
                            <?php } ?>
                        </div>
                        <!-- <div class="tags" style="max-width: 986px;">
                            <input type="checkbox" id="act-hiring" name="tags" value="Actively Hiring">
                            <label for="act-hiring">Actively Hiring</label>

                            <input type="checkbox" id="job-seeker" name="tags" value="Job Seeker">
                            <label for="job-seeker">Job Seeker</label>

                            <input type="checkbox" id="need-mentor" name="tags" value="Need Mentor">
                            <label for="need-mentor">Need Mentor</label>

                            <input type="checkbox" id="open-to-work" name="tags" value="Open to Work">
                            <label for="open-to-work">Open to Work</label>

                            <input type="checkbox" id="bartering" name="tags" value="Bartering">
                            <label for="bartering">Bartering</label>

                            <input type="checkbox" id="volunteering" name="tags" value="Volunteering">
                            <label for="volunteering">Volunteering</label>

                            <input type="checkbox" id="remote-work" name="tags" value="Remote work">
                            <label for="remote-work">Remote work</label>

                            <input type="checkbox" id="can-refer" name="tags" value="Can Refer">
                            <label for="can-refer">Can Refer</label>

                            <input type="checkbox" id="need-cofounder" name="tags" value="Need Cofounder">
                            <label for="need-cofounder">Need Cofounder</label>

                            <input type="checkbox" id="Upskilling" name="tags" value="Upskilling">
                            <label for="Upskilling">Upskilling</label>

                        </div> -->
                        <!-- <hr style="border-top: 2px solid #d3d3d3;"> -->

                        <input type="hidden" name="taoh_ptoken" value="<?php echo $data->ptoken; ?>">
                        <div style="text-align: right;">
                            <button name="step2" id="step2" class="btn s-btn mt-3" value="2">Save</button> <!--  and Continue -->
                        </div>
                        </form>


                          <!-- for mobile screen -->
                        <!-- Mobile Navigation Buttons -->
                        <div class="d-flex justify-content-between d-xl-none mt-4">
                            <button type="button" class="btn tab-nav-btn prev-btn" data-direction="prev">Previous</button>
                            <button type="button" class="btn tab-nav-btn ml-auto next-btn" data-direction="next">Next</button>
                        </div>
                    </div>

                    <!-- frm-blc-3 -->
                    <div class="tab-pane fade pb-4 pt-xl-4" id="form-block-3" role="tabpanel" aria-labelledby="block-3-tab" >


                        <div class="d-flex align-items-center d-xl-none p-3 mb-4" style="gap: 12px; margin: 0 -24px; background-color: #2557A7;">
                            <svg style="min-width: fit-content;" width="16" height="16" viewBox="0 0 13 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.67188 1.4H8.32812C8.43984 1.4 8.53125 1.505 8.53125 1.63333V2.8H4.46875V1.63333C4.46875 1.505 4.56016 1.4 4.67188 1.4ZM3.25 1.63333V2.8H1.625C0.728711 2.8 0 3.63708 0 4.66667V7.46667H4.875H8.125H13V4.66667C13 3.63708 12.2713 2.8 11.375 2.8H9.75V1.63333C9.75 0.732083 9.1127 0 8.32812 0H4.67188C3.8873 0 3.25 0.732083 3.25 1.63333ZM13 8.4H8.125V9.33333C8.125 9.84958 7.76191 10.2667 7.3125 10.2667H5.6875C5.23809 10.2667 4.875 9.84958 4.875 9.33333V8.4H0V12.1333C0 13.1629 0.728711 14 1.625 14H11.375C12.2713 14 13 13.1629 13 12.1333V8.4Z" fill="#ffffff" />
                            </svg>
                            <h6 class="p-field-title text-white">Experience Details</h6>
                            <?php // if(isset($data->step3) && $data->step3 == 3) {
                                if(is_array($emp_list) && count($emp_list) > 0 ){ ?>

                                <svg style="min-width: fit-content;" width="18" height="18" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z"
                                    fill="#ffffff"/>
                                </svg>
                            <?php }else{ ?>
                                <!-- <svg style="min-width: fit-content;" width="18" height="18" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#A7A7A7"/>
                                </svg> -->
                            <?php } ?>
                        </div>

                    <?php if(is_array($emp_list)){
                            if(count($emp_list) > 0 && is_array($emp_list[$emp_last_key]['title'])){ ?>
                    <?php

            $emp_year = array();
            foreach($emp_list as $ekeys => $evals){
                $emp_year[$ekeys] = $evals['emp_year_end'];
                $emp_list[$ekeys]['keys'] = $ekeys;
            }
            array_multisort($emp_year, SORT_DESC, $emp_list);
            // echo"<pre>";print_r($emp_list); //die();
            foreach($emp_list as $emp_keys => $emp_vals){
                //print_r($emp_vals);
            $em_title = ($emp_vals['emp_title'])?$emp_vals['emp_title'] : $emp_vals['title'];
            foreach ( $em_title as $em_key => $em_value ){
                if(!is_array($em_value)){
                    list ( $em_pre, $em_post ) = explode( ':>', $em_value );
                }else{
                    // echo"<pre>";print_r($em_value); //die();
                    foreach ( $em_value as $emp_key => $emp_value ){
                        // echo "<br> Emp value :".$emp_value;
                        list ( $em_pre, $em_post ) = explode( ':>', $emp_value );
                    }
                }
            }
            $em_company = ($emp_vals['emp_company'])?$emp_vals['emp_company'] : $emp_vals['company'];
            foreach ( $em_company as $em_cmp_key => $em_cmp_value ){
                if(!is_array($em_cmp_value)){
                    list ( $em_cmp_pre, $em_cmp_post ) = explode( ':>', $em_cmp_value );
                }else{
                    // echo"<pre>";print_r($em_value); //die();
                    foreach ( $em_cmp_value as $emp_cmp_key => $emp_cmp_value ){
                        // echo "<br> Emp value :".$emp_value;
                        list ( $em_cmp_pre, $em_cmp_post ) = explode( ':>', $emp_cmp_value );
                    }
                }

            }
            $get_present_not = ($emp_vals['current_role'] == 'on')?' Present':get_month_from_number($emp_vals['emp_end_month']).' '.$emp_vals['emp_year_end'];
            $current_year = date('Y');   // Outputs: 2025 (Current Year)
            $current_month = date('n');  // Outputs: 3 (Current Month - Without leading zero)
            // Check for empty values and assign current month/year if empty

$end_month = !empty($emp_vals['emp_end_month']) ?$emp_vals['emp_end_month'] : $current_month;
$end_year = !empty($emp_vals['emp_year_end']) ? $emp_vals['emp_year_end'] : $current_year;


            $emp_placeType = $emp_vals['emp_placeType'];
            if($emp_placeType == 'rem'){
                $emp_placeType = ' . '.'Remote';
            }else if($emp_placeType == 'ons'){
                $emp_placeType = '. '.'Onsite';
            }else if($emp_placeType == 'hyb'){
                $emp_placeType = '. '.'Hybrid';
            }else{
                $emp_placeType = '';
            }

            $skills = $emp_vals['skill'];
           /*  $items = '';
            foreach ($skills as $s_keys => $s_vals){
                $items = explode(':>',$s_vals);
            } */

            $roletype_arr = array(
                "remo" => "Remote Work",
                "full" => "Full Time",
                "part" => "Part Time",
                "temp" => "Temporary",
                "free" => "Freelance",
                "cont" => "Contract",
                "pdin" => "Paid Internship",
                "unin" => "Unpaid Internship",
                "voln" => "Volunteer",
            );
            $roletype = $emp_vals['emp_roletype'];
            $role_items = '';
            foreach ($roletype as $key => $value){
                $role_items = $roletype_arr[$value]; // ' . '.
            }

            $industry_arr = array(
                "agri" => "Agriculture & Forestry",
                "arts" => "Arts & Entertainment",
                "auto" => "Automotive",
                "aero" => "Aviation & Aerospace",
                "bank" => "Banking & Finance",
                "bio" => "Biotechnology",
                "che" => "Chemicals",
                "cons" => "Construction",
                "good" => "Consumer Goods & Services",
                "space" => "Defense & Space",
                "edu" => "Education",
                "ene" => "Energy & Utilities",
                "engi" => "Engineering",
                "envi" => "Environmental Services",
                "fash" => "Fashion & Apparel",
                "food" => "Food & Beverages",
                "govt" => "Government & Public Sector",
                "heal" => "Healthcare & Pharmaceuticals",
                "tour" => "Hospitality & Tourism",
                "tech" => "Information Technology",
                "ins" => "Insurance",
                "legal" => "Legal Services",
                "manu" => "Manufacturing",
                "mari" => "Maritime",
                "mark" => "Marketing & Advertising",
                "media" => "Media & Communications",
                "mini" => "Mining & Metals",
                "non" => "Non-Profit & NGO",
                "prof" => "Professional Services",
                "real" => "Real Estate",
                "ret" => "Retail",
                "sports" => "Sports & Recreation",
                "tele" => "Telecommunications",
                "logi" => "Transport & Logistics",
                "other" => "Others (for industries not listed above)"
              );
            ?>
            <?php if (!$user_is_logged_in) { ?>
                <div class="profile-card-list mb-5">
                    <div class="heading px-4 px-lg-5">
                        <h5 class="d-flex align-items-center" style="gap: 16px;">
                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.7812 3H19.2188C19.4766 3 19.6875 3.225 19.6875 3.5V6H10.3125V3.5C10.3125 3.225 10.5234 3 10.7812 3ZM7.5 3.5V6H3.75C1.68164 6 0 7.79375 0 10V16H11.25H18.75H30V10C30 7.79375 28.3184 6 26.25 6H22.5V3.5C22.5 1.56875 21.0293 0 19.2188 0H10.7812C8.9707 0 7.5 1.56875 7.5 3.5ZM30 18H18.75V20C18.75 21.1063 17.9121 22 16.875 22H13.125C12.0879 22 11.25 21.1063 11.25 20V18H0V26C0 28.2062 1.68164 30 3.75 30H26.25C28.3184 30 30 28.2062 30 26V18Z" fill="#1573B5"/>
                            </svg>
                            <span><?php echo $em_post; ?></span>
                        </h5>
                    </div>

                    <div class="px-4 px-lg-5 pt-4 pb-5 d-flex" style="gap: 16px;">
                        <svg style="min-width: fit-content;" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 0V28H28V0H0ZM19.7812 20.325L14 25.8687L8.21875 20.325L12.25 8.825L8.21875 3.4125H19.775L15.75 8.825L19.7812 20.325Z" fill="#1573B5"/>
                        </svg>

                        <div>
                            <div class="d-flex align-items-center mb-1" style="gap: 12px;">
                                <h5 class="j-title"><?php echo $em_cmp_post; ?></h5>
                            </div>
                            <p class="duration mb-2"><?php echo get_month_from_number($emp_vals['emp_start_month']).' '.$emp_vals['emp_year_start'].' to '.$get_present_not ?> . <span><?php echo get_diff_dates($emp_vals['emp_year_start'],$emp_vals['emp_start_month'],$end_year,$end_month); ?></span></p>
                            <h6 class="list-text-xs mb-2">Responsibilities</h6>
                            <p class="list-text-xxs"><?php echo taoh_title_desc_decode($emp_vals['emp_responsibilities']); ?></p>
                        </div>
                    </div>
                </div>


            <?php }else{ ?>
                <div class="profile-card-list mb-5">
                    <div class="heading px-4 px-lg-5">
                        <h5 class="d-flex align-items-center" style="gap: 16px;">
                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.7812 3H19.2188C19.4766 3 19.6875 3.225 19.6875 3.5V6H10.3125V3.5C10.3125 3.225 10.5234 3 10.7812 3ZM7.5 3.5V6H3.75C1.68164 6 0 7.79375 0 10V16H11.25H18.75H30V10C30 7.79375 28.3184 6 26.25 6H22.5V3.5C22.5 1.56875 21.0293 0 19.2188 0H10.7812C8.9707 0 7.5 1.56875 7.5 3.5ZM30 18H18.75V20C18.75 21.1063 17.9121 22 16.875 22H13.125C12.0879 22 11.25 21.1063 11.25 20V18H0V26C0 28.2062 1.68164 30 3.75 30H26.25C28.3184 30 30 28.2062 30 26V18Z" fill="#1573B5"/>
                            </svg>
                            <span><?php echo $em_post; ?></span>
                        </h5>
                        <div class="d-flex align-items-center" style="gap: 12px;">
                        <?php if( $user_is_logged_in && (isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken) && taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken == $ptoken)) { ?>
                            <a class="add_edit_emp" data-add-edit="Edit"  data-add-edit="Edit" data-employee="<?php echo $emp_keys; ?>" data-emp-delete="<?php echo $emp_keys; ?>" data-emp-edit-delete = <?php echo $emp_vals['keys']; ?>>
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M29.475 1.02656C28.1063 -0.342188 25.8937 -0.342188 24.525 1.02656L22.6437 2.90156L28.7625 9.02031L30.6437 7.13906C32.0125 5.77031 32.0125 3.55781 30.6437 2.18906L29.475 1.02656ZM10.775 14.7766C10.3937 15.1578 10.1 15.6266 9.93125 16.1453L8.08125 21.6953C7.9 22.2328 8.04375 22.8266 8.44375 23.2328C8.84375 23.6391 9.4375 23.7766 9.98125 23.5953L15.5312 21.7453C16.0438 21.5766 16.5125 21.2828 16.9 20.9016L27.3563 10.4391L21.2313 4.31406L10.775 14.7766ZM6 3.67031C2.6875 3.67031 0 6.35781 0 9.67031V25.6703C0 28.9828 2.6875 31.6703 6 31.6703H22C25.3125 31.6703 28 28.9828 28 25.6703V19.6703C28 18.5641 27.1063 17.6703 26 17.6703C24.8937 17.6703 24 18.5641 24 19.6703V25.6703C24 26.7766 23.1063 27.6703 22 27.6703H6C4.89375 27.6703 4 26.7766 4 25.6703V9.67031C4 8.56406 4.89375 7.67031 6 7.67031H12C13.1062 7.67031 14 6.77656 14 5.67031C14 4.56406 13.1062 3.67031 12 3.67031H6Z" fill="url(#paint0_linear_6710_358)"/>
                                    <defs>
                                    <linearGradient id="paint0_linear_6710_358" x1="15.8352" y1="0" x2="15.8352" y2="31.6703" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#2557A7"/>
                                    <stop offset="1" stop-color="#176FB3"/>
                                    </linearGradient>
                                    </defs>
                                </svg>
                            </a>
                        <?php } ?>
                        <?php if( $user_is_logged_in && (isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken) && taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken == $ptoken)) { ?>
                            <a class="add_edit_emp"  data-add-edit="Add" data-employee="<?php echo $emp_tot_count; ?>">
                                <svg width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="19.5" cy="19.5" r="19.5" fill="url(#paint0_linear_6485_153)"/>
                                    <path d="M21.1154 10.6154C21.1154 9.72187 20.3935 9 19.5 9C18.6065 9 17.8846 9.72187 17.8846 10.6154V17.8846H10.6154C9.72187 17.8846 9 18.6065 9 19.5C9 20.3935 9.72187 21.1154 10.6154 21.1154H17.8846V28.3846C17.8846 29.2781 18.6065 30 19.5 30C20.3935 30 21.1154 29.2781 21.1154 28.3846V21.1154H28.3846C29.2781 21.1154 30 20.3935 30 19.5C30 18.6065 29.2781 17.8846 28.3846 17.8846H21.1154V10.6154Z" fill="#F6F6F6"/>
                                    <defs>
                                    <linearGradient id="paint0_linear_6485_153" x1="19.5" y1="0" x2="19.5" y2="39" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#2557A7"/>
                                    <stop offset="1" stop-color="#1377B7"/>
                                    </linearGradient>
                                    </defs>
                                </svg>
                            </a>
                        <?php } ?>
                        </div>
                    </div>

                    <div class="px-4 px-lg-5 pt-4 pb-5 d-flex" style="gap: 16px;">
                        <svg style="min-width: fit-content;" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 0V28H28V0H0ZM19.7812 20.325L14 25.8687L8.21875 20.325L12.25 8.825L8.21875 3.4125H19.775L15.75 8.825L19.7812 20.325Z" fill="#1573B5"/>
                        </svg>

                        <div>
                            <div class="d-flex align-items-center mb-1" style="gap: 12px;">
                                <h5 class="j-title"><?php echo $em_cmp_post; ?></h5>
                                <?php if($role_items != ''){ ?>
                                <span class="j-type-badge py-1"><?php echo $role_items; ?></span>
                                <?php } ?>
                            </div>
                            <p class="duration mb-2"><?php echo get_month_from_number($emp_vals['emp_start_month']).' '.$emp_vals['emp_year_start'].' to '.$get_present_not ?> . <span><?php echo get_diff_dates($emp_vals['emp_year_start'],$emp_vals['emp_start_month'],$end_year,$end_month); ?></span></p>
                            <div class="d-flex align-items-center flex-wrap mb-3" style="gap: 12px;">
                                <?php if(trim($emp_vals['emp_full_location']) != '' && trim($emp_placeType) != ''){ ?>
                                <p class="list-text-xs mr-lg-4">Location: <?php echo $emp_vals['emp_full_location'].$emp_placeType; ?></p>
                                <?php } ?>
                                <p class="list-text-xs">Industry: <?php echo $industry_arr[$emp_vals['emp_industry']]; ?></p>
                            </div>
                            <?php if(is_array($emp_vals['skill'])){ ?>
                                <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 8px;">
                                    <p class="list-text-xs mr-1">Skills:</p>
                                    <?php foreach ($skills as $s_keys => $s_vals){
                                        if(!is_array($s_vals)){
                                        $items = explode(':>',$s_vals); ?>
                                        <span class="skill-badge"><?php echo $items[1]; ?></span>
                                    <?php } else{
                                        foreach ($s_vals as $es_keys => $es_vals){
                                            $items = explode(':>',$es_vals); ?>
                                        <span class="skill-badge"><?php echo $items[1]; ?></span>
                                    <?php }
                                    }
                                    } ?>

                                </div>
                            <?php } ?>
                            <?php if(trim($emp_vals['emp_responsibilities']) != ''){ ?>
                            <h6 class="list-text-xs mb-2">Responsibilities</h6>
                            <p class="list-text-xxs"><?php echo taoh_title_desc_decode($emp_vals['emp_responsibilities']); ?></p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php } ?>

                    <?php }
                            } ?>
                        <!-- <h5 class="p-field-title mb-4">Can you tell us more about your Career journey !</h5> -->
                        <!-- <div class="profile-card-list mb-5">
                            <div class="heading px-4 px-lg-5">
                                <h5 class="d-flex align-items-center" style="gap: 16px;">
                                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.7812 3H19.2188C19.4766 3 19.6875 3.225 19.6875 3.5V6H10.3125V3.5C10.3125 3.225 10.5234 3 10.7812 3ZM7.5 3.5V6H3.75C1.68164 6 0 7.79375 0 10V16H11.25H18.75H30V10C30 7.79375 28.3184 6 26.25 6H22.5V3.5C22.5 1.56875 21.0293 0 19.2188 0H10.7812C8.9707 0 7.5 1.56875 7.5 3.5ZM30 18H18.75V20C18.75 21.1063 17.9121 22 16.875 22H13.125C12.0879 22 11.25 21.1063 11.25 20V18H0V26C0 28.2062 1.68164 30 3.75 30H26.25C28.3184 30 30 28.2062 30 26V18Z" fill="#1573B5"/>
                                    </svg>
                                    <span>Boston Analytics</span>
                                </h5>
                                <div class="d-flex align-items-center" style="gap: 12px;">
                                    <a href="#">
                                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M29.475 1.02656C28.1063 -0.342188 25.8937 -0.342188 24.525 1.02656L22.6437 2.90156L28.7625 9.02031L30.6437 7.13906C32.0125 5.77031 32.0125 3.55781 30.6437 2.18906L29.475 1.02656ZM10.775 14.7766C10.3937 15.1578 10.1 15.6266 9.93125 16.1453L8.08125 21.6953C7.9 22.2328 8.04375 22.8266 8.44375 23.2328C8.84375 23.6391 9.4375 23.7766 9.98125 23.5953L15.5312 21.7453C16.0438 21.5766 16.5125 21.2828 16.9 20.9016L27.3563 10.4391L21.2313 4.31406L10.775 14.7766ZM6 3.67031C2.6875 3.67031 0 6.35781 0 9.67031V25.6703C0 28.9828 2.6875 31.6703 6 31.6703H22C25.3125 31.6703 28 28.9828 28 25.6703V19.6703C28 18.5641 27.1063 17.6703 26 17.6703C24.8937 17.6703 24 18.5641 24 19.6703V25.6703C24 26.7766 23.1063 27.6703 22 27.6703H6C4.89375 27.6703 4 26.7766 4 25.6703V9.67031C4 8.56406 4.89375 7.67031 6 7.67031H12C13.1062 7.67031 14 6.77656 14 5.67031C14 4.56406 13.1062 3.67031 12 3.67031H6Z" fill="url(#paint0_linear_6710_358)"/>
                                            <defs>
                                            <linearGradient id="paint0_linear_6710_358" x1="15.8352" y1="0" x2="15.8352" y2="31.6703" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#2557A7"/>
                                            <stop offset="1" stop-color="#176FB3"/>
                                            </linearGradient>
                                            </defs>
                                        </svg>
                                    </a>
                                    <a href="#">
                                        <svg width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="19.5" cy="19.5" r="19.5" fill="url(#paint0_linear_6485_153)"/>
                                            <path d="M21.1154 10.6154C21.1154 9.72187 20.3935 9 19.5 9C18.6065 9 17.8846 9.72187 17.8846 10.6154V17.8846H10.6154C9.72187 17.8846 9 18.6065 9 19.5C9 20.3935 9.72187 21.1154 10.6154 21.1154H17.8846V28.3846C17.8846 29.2781 18.6065 30 19.5 30C20.3935 30 21.1154 29.2781 21.1154 28.3846V21.1154H28.3846C29.2781 21.1154 30 20.3935 30 19.5C30 18.6065 29.2781 17.8846 28.3846 17.8846H21.1154V10.6154Z" fill="#F6F6F6"/>
                                            <defs>
                                            <linearGradient id="paint0_linear_6485_153" x1="19.5" y1="0" x2="19.5" y2="39" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#2557A7"/>
                                            <stop offset="1" stop-color="#1377B7"/>
                                            </linearGradient>
                                            </defs>
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <div class="px-4 px-lg-5 pt-4 pb-5 d-flex" style="gap: 16px;">
                                <svg style="min-width: fit-content;" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 0V28H28V0H0ZM19.7812 20.325L14 25.8687L8.21875 20.325L12.25 8.825L8.21875 3.4125H19.775L15.75 8.825L19.7812 20.325Z" fill="#1573B5"/>
                                </svg>

                                <div>
                                    <div class="d-flex align-items-center mb-1" style="gap: 12px;">
                                        <h5 class="j-title">Senior Data Analyst</h5>
                                        <span class="j-type-badge py-1">Full Time</span>
                                    </div>
                                    <p class="duration mb-2">May 2020 to Present</p>
                                    <div class="d-flex align-items-center flex-wrap mb-3" style="gap: 12px;">
                                        <p class="list-text-xs mr-lg-4">Location: Remote</p>
                                        <p class="list-text-xs">Industry: Information Technology</p>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 8px;">
                                        <p class="list-text-xs mr-1">Skills:</p>
                                        <span class="skill-badge">Skill 1</span>
                                        <span class="skill-badge">Skill 2</span>
                                        <span class="skill-badge">Skill 3</span>
                                        <span class="skill-badge">Skill 4</span>
                                        <span class="skill-badge">Skill 5</span>
                                    </div>
                                    <h6 class="list-text-xs mb-2">Responsibilities</h6>
                                    <p class="list-text-xxs">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book <a href="#" style="color: #2557A7;">Read More...</a></p>
                                </div>
                            </div>
                        </div> -->


                        <div class="profile-card-list">
                            <div class="heading px-4 px-lg-5 justify-content-center">
                                <h5 class="text-center">
                                    Add Your Experience Details
                                </h5>
                            </div>

                            <div class="px-4 px-lg-5 pt-4 pb-5 d-flex flex-column align-items-center" style="gap: 16px;">
                                <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.0938 4.2H26.9062C27.2672 4.2 27.5625 4.515 27.5625 4.9V8.4H14.4375V4.9C14.4375 4.515 14.7328 4.2 15.0938 4.2ZM10.5 4.9V8.4H5.25C2.3543 8.4 0 10.9113 0 14V22.4H15.75H26.25H42V14C42 10.9113 39.6457 8.4 36.75 8.4H31.5V4.9C31.5 2.19625 29.441 0 26.9062 0H15.0938C12.559 0 10.5 2.19625 10.5 4.9ZM42 25.2H26.25V28C26.25 29.5487 25.077 30.8 23.625 30.8H18.375C16.923 30.8 15.75 29.5487 15.75 28V25.2H0V36.4C0 39.4888 2.3543 42 5.25 42H36.75C39.6457 42 42 39.4888 42 36.4V25.2Z" fill="#1573B5"/>
                                </svg>
                                <h5 class="text-center list-text-md my-3"  style="max-width: 522px;">"Showcase your journey! Add your education to unlock better opportunities."</h5>
                                <button class="btn continue-btn add_edit_emp" type="button" data-add-edit="Add" data-employee="<?php echo $emp_tot_count ?? '0'; ?>">
                                    <span>Add Experience Details</span>
                                </button>
                            </div>
                        </div>


                         <!-- for mobile screen -->
                        <!-- Mobile Navigation Buttons -->
                        <div class="d-flex justify-content-between d-xl-none mt-4">
                            <button type="button" class="btn tab-nav-btn prev-btn" data-direction="prev">Previous</button>
                            <button type="button" class="btn tab-nav-btn ml-auto next-btn" data-direction="next">Next</button>
                        </div>

                    </div>

                    <!-- frm-blc-4 -->
                    <div class="tab-pane pb-4 pt-xl-4 fade" id="form-block-4" role="tabpanel" aria-labelledby="block-4-tab" >

                        <div class="d-flex align-items-center d-xl-none p-3 mb-4" style="gap: 12px; margin: 0 -24px; background-color: #2557A7;">
                            <svg style="min-width: fit-content;" width="16" height="16" viewBox="0 0 18 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.00022 0C8.77241 0 8.54741 0.0374926 8.33367 0.1098L0.444797 2.82266C0.177616 2.91639 0.000432897 3.15742 0.000432897 3.4279C0.000432897 3.69838 0.177616 3.93941 0.444797 4.03314L2.0732 4.59285C1.61196 5.28379 1.3504 6.10059 1.3504 6.96024V7.71277C1.3504 8.47334 1.04666 9.25801 0.723228 9.87664C0.54042 10.2248 0.3323 10.5676 0.0904307 10.8836C0.000432898 10.9987 -0.024879 11.1487 0.0257448 11.2853C0.0763686 11.4219 0.194491 11.5236 0.340737 11.5584L2.14069 11.9869C2.25882 12.0164 2.38538 11.995 2.48944 11.9334C2.5935 11.8718 2.66662 11.77 2.68912 11.6549C2.93099 10.5087 2.81005 9.48028 2.63006 8.74382C2.54006 8.36354 2.41912 7.97522 2.25038 7.61904V6.96024C2.25038 6.15147 2.53725 5.38823 3.03505 4.77764C3.39785 4.36254 3.86753 4.02778 4.41876 3.82157L8.83428 2.16922C9.0649 2.08352 9.32646 2.19064 9.41646 2.41024C9.50645 2.62984 9.39396 2.8789 9.16334 2.9646L4.74782 4.61695C4.39908 4.74818 4.09252 4.94903 3.84222 5.19541L8.33086 6.73797C8.5446 6.81027 8.7696 6.84777 8.9974 6.84777C9.22521 6.84777 9.45021 6.81027 9.66395 6.73797L17.5556 4.03314C17.8228 3.94208 18 3.69838 18 3.4279C18 3.15742 17.8228 2.91639 17.5556 2.82266L9.66676 0.1098C9.45302 0.0374926 9.22802 0 9.00022 0ZM3.60035 10.0695C3.60035 11.0148 6.01904 11.9976 9.00022 11.9976C11.9814 11.9976 14.4001 11.0148 14.4001 10.0695L13.9698 6.17558L9.97051 7.55209C9.65833 7.65921 9.32927 7.71277 9.00022 7.71277C8.67116 7.71277 8.3393 7.65921 8.02993 7.55209L4.03065 6.17558L3.60035 10.0695Z" fill="#ffffff" />
                            </svg>
                            <h6 class="p-field-title text-white">Education Details</h6>
                            <?php if(is_array($edu_list) && count($edu_list) > 0 ){ ?>

                                <svg style="min-width: fit-content;" width="18" height="18" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z"
                                    fill="#ffffff"/>
                                </svg>
                            <?php }else{ ?>
                                <!-- <svg style="min-width: fit-content;" width="18" height="18" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#A7A7A7"/>
                                </svg> -->
                            <?php } ?>
                        </div>

                    <?php if(is_array($edu_list)){
                        if(is_array($edu_list[$edu_last_key]['company'])){?>
                        <div>
                        <div>
                            <!-- <h5 class="float-left">Education</h5> -->
                            <!-- <?php if( $user_is_logged_in && (isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken) && taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken == $ptoken)) { ?>
                                <a class="float-right add_edit_edu" style="cursor: pointer;" data-add-edit="Add" data-education="<?php echo $edu_tot_count; ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" data-supported-dps="24x24" fill="currentColor" class="mercado-match" width="24" height="24" focusable="false">
                                        <path d="M21 13h-8v8h-2v-8H3v-2h8V3h2v8h8z"></path>
                                    </svg>
                                </a>
                            <?php } ?> -->
                        </div>
                        <?php
                        $edu_year = array();
                        foreach($edu_list as $edu_keys => $edu_vals){
                            $edu_year[$edu_keys] = $edu_vals['edu_complete_year'];
                            $edu_list[$edu_keys]['keys'] = $edu_keys;
                        }
                        // echo"<pre>";print_r($edu_list);
                        array_multisort($edu_year, SORT_DESC, $edu_list);
                        // echo"<pre>";print_r($edu_list);die();

                        foreach($edu_list as $edu_keys => $edu_vals){
                        $ed_name = $edu_vals['company'];
                        foreach ( $ed_name as $ed_key => $ed_value ){
                            if(!is_array($ed_value)){
                                list ( $ed_pre, $ed_post ) = explode( ':>', $ed_value );
                            }else{
                                foreach ( $ed_value as $edu_key => $edu_value ){
                                    list ( $ed_pre, $ed_post ) = explode( ':>', $edu_value );
                                }
                            }
                        }

                        $degeree_arr = array(
                            "highschool" => "High School Diploma or GED",
                            "vocational" => "Vocational/Technical Diploma",
                            "associate" => "Associate Degree",
                            "bachelor" => "Bachelor's Degree",
                            "master" => "Master's Degree",
                            "doctorate" => "Doctorate or Professional Degree",
                            "other" => "Other (for degeree not listed above)"
                        );
                        $degree_get = $edu_vals['edu_degree'];
                        $degree_items = '';
                        foreach ($degree_get as $d_key => $d_value){
                            $degree_items = $degeree_arr[$d_value];
                        }

                        $d_skills = $edu_vals['skill'];
                        /* $d_items = '';
                        foreach ($d_skills as $d_keys => $d_vals){
                            $d_items = explode(':>',$d_vals);
                        } */
                        ?>
                        <?php if (!$user_is_logged_in) { ?>
                            <div class="profile-card-list mb-5">
                                <div class="heading px-4 px-lg-5">
                                    <h5 class="d-flex align-items-center" style="gap: 16px;">
                                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.98507 0.1046L0.798648 4.04248C0.224497 4.2886 -0.0953875 4.9039 0.031746 5.51099C0.158879 6.11808 0.69202 6.56109 1.31538 6.56109V6.88925C1.31538 7.43481 1.7542 7.87372 2.29964 7.87372H18.704C19.2494 7.87372 19.6882 7.43481 19.6882 6.88925V6.56109C20.3116 6.56109 20.8488 6.12218 20.9719 5.51099C21.0949 4.89979 20.775 4.2845 20.205 4.04248L11.0185 0.1046C10.6905 -0.0348667 10.3132 -0.0348667 9.98507 0.1046ZM5.25242 9.18635H2.62773V17.2385C2.60312 17.2508 2.57852 17.2672 2.55391 17.2836L0.585392 18.5962C0.105565 18.9162 -0.111792 19.5151 0.0563525 20.0689C0.224497 20.6226 0.737132 21 1.31538 21H19.6882C20.2665 21 20.775 20.6226 20.9432 20.0689C21.1113 19.5151 20.898 18.9162 20.4141 18.5962L18.4456 17.2836C18.421 17.2672 18.3964 17.2549 18.3718 17.2385V9.18635H15.7512V17.0621H14.1108V9.18635H11.4861V17.0621H9.51755V9.18635H6.89285V17.0621H5.25242V9.18635ZM10.5018 2.62321C10.8499 2.62321 11.1837 2.7615 11.4298 3.00766C11.6759 3.25383 11.8142 3.5877 11.8142 3.93583C11.8142 4.28396 11.6759 4.61784 11.4298 4.864C11.1837 5.11017 10.8499 5.24846 10.5018 5.24846C10.1537 5.24846 9.81995 5.11017 9.57384 4.864C9.32772 4.61784 9.18946 4.28396 9.18946 3.93583C9.18946 3.5877 9.32772 3.25383 9.57384 3.00766C9.81995 2.7615 10.1537 2.62321 10.5018 2.62321Z" fill="#1474B6"/>
                                        </svg>
                                        <span><?php echo $ed_post; ?></span>
                                    </h5>
                                </div>

                                <div class="px-4 px-lg-5 pt-4 pb-5 d-flex" style="gap: 16px;">
                                    <svg style="min-width: fit-content;" width="32" height="23" viewBox="0 0 32 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16.0008 0C15.5958 0 15.1958 0.0699999 14.8158 0.205L0.79077 5.27C0.31577 5.445 0.000769613 5.895 0.000769613 6.4C0.000769613 6.905 0.31577 7.355 0.79077 7.53L3.68577 8.575C2.86577 9.865 2.40077 11.39 2.40077 12.995V14.4C2.40077 15.82 1.86077 17.285 1.28577 18.44C0.96077 19.09 0.59077 19.73 0.16077 20.32C0.000769615 20.535 -0.0442304 20.815 0.0457696 21.07C0.13577 21.325 0.34577 21.515 0.60577 21.58L3.80577 22.38C4.01577 22.435 4.24077 22.395 4.42577 22.28C4.61077 22.165 4.74077 21.975 4.78077 21.76C5.21077 19.62 4.99577 17.7 4.67577 16.325C4.51577 15.615 4.30077 14.89 4.00077 14.225V12.995C4.00077 11.485 4.51077 10.06 5.39577 8.92C6.04077 8.145 6.87577 7.52 7.85577 7.135L15.7058 4.05C16.1158 3.89 16.5808 4.09 16.7408 4.5C16.9008 4.91 16.7008 5.375 16.2908 5.535L8.44077 8.62C7.82077 8.865 7.27577 9.24 6.83077 9.7L14.8108 12.58C15.1908 12.715 15.5908 12.785 15.9958 12.785C16.4008 12.785 16.8008 12.715 17.1808 12.58L31.2108 7.53C31.6858 7.36 32.0008 6.905 32.0008 6.4C32.0008 5.895 31.6858 5.445 31.2108 5.27L17.1858 0.205C16.8058 0.0699999 16.4058 0 16.0008 0ZM6.40077 18.8C6.40077 20.565 10.7008 22.4 16.0008 22.4C21.3008 22.4 25.6008 20.565 25.6008 18.8L24.8358 11.53L17.7258 14.1C17.1708 14.3 16.5858 14.4 16.0008 14.4C15.4158 14.4 14.8258 14.3 14.2758 14.1L7.16577 11.53L6.40077 18.8Z" fill="#176FB3"/>
                                        <defs>
                                        <linearGradient id="paint0_linear_6910_909" x1="16.0004" y1="0" x2="16.0004" y2="22.4044" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#176FB3"/>
                                        <stop offset="1" stop-color="#1377B7"/>
                                        </linearGradient>
                                        </defs>
                                    </svg>
                                    <div>
                                        <div>
                                            <h5 class="j-title mb-1"><?php echo $edu_vals['edu_specalize']; ?></h5>
                                        </div>
                                        <p class="duration mb-2"><?php echo get_month_from_number($edu_vals['edu_start_month']).' '.$edu_vals['edu_start_year'].' to '.get_month_from_number($edu_vals['edu_end_month']).' '.$edu_vals['edu_complete_year']; ?></p>
                                        <?php if($edu_vals['edu_description'] != ''){?>
                                            <h6 class="list-text-xs mb-2">Description:</h6>
                                            <p class="list-text-xxs"><?php echo $edu_vals['edu_description']; ?></p>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                        <?php }else{  ?>
                            <div class="profile-card-list mb-5">
                                <div class="heading px-4 px-lg-5">
                                    <h5 class="d-flex align-items-center" style="gap: 16px;">
                                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.98507 0.1046L0.798648 4.04248C0.224497 4.2886 -0.0953875 4.9039 0.031746 5.51099C0.158879 6.11808 0.69202 6.56109 1.31538 6.56109V6.88925C1.31538 7.43481 1.7542 7.87372 2.29964 7.87372H18.704C19.2494 7.87372 19.6882 7.43481 19.6882 6.88925V6.56109C20.3116 6.56109 20.8488 6.12218 20.9719 5.51099C21.0949 4.89979 20.775 4.2845 20.205 4.04248L11.0185 0.1046C10.6905 -0.0348667 10.3132 -0.0348667 9.98507 0.1046ZM5.25242 9.18635H2.62773V17.2385C2.60312 17.2508 2.57852 17.2672 2.55391 17.2836L0.585392 18.5962C0.105565 18.9162 -0.111792 19.5151 0.0563525 20.0689C0.224497 20.6226 0.737132 21 1.31538 21H19.6882C20.2665 21 20.775 20.6226 20.9432 20.0689C21.1113 19.5151 20.898 18.9162 20.4141 18.5962L18.4456 17.2836C18.421 17.2672 18.3964 17.2549 18.3718 17.2385V9.18635H15.7512V17.0621H14.1108V9.18635H11.4861V17.0621H9.51755V9.18635H6.89285V17.0621H5.25242V9.18635ZM10.5018 2.62321C10.8499 2.62321 11.1837 2.7615 11.4298 3.00766C11.6759 3.25383 11.8142 3.5877 11.8142 3.93583C11.8142 4.28396 11.6759 4.61784 11.4298 4.864C11.1837 5.11017 10.8499 5.24846 10.5018 5.24846C10.1537 5.24846 9.81995 5.11017 9.57384 4.864C9.32772 4.61784 9.18946 4.28396 9.18946 3.93583C9.18946 3.5877 9.32772 3.25383 9.57384 3.00766C9.81995 2.7615 10.1537 2.62321 10.5018 2.62321Z" fill="#1474B6"/>
                                        </svg>
                                        <span><?php echo $ed_post; // $edu_vals['edu_specalize']; ?></span>
                                    </h5>
                                    <div class="d-flex align-items-center" style="gap: 12px;">
                                    <?php if( $user_is_logged_in && (isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken) && taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken == $ptoken)) { ?>
                                        <a class="add_edit_edu" data-add-edit="Edit" data-education="<?php echo $edu_keys; ?>" data-edu-edit-delete="<?php echo $edu_keys; ?>">
                                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M29.475 1.02656C28.1063 -0.342188 25.8937 -0.342188 24.525 1.02656L22.6437 2.90156L28.7625 9.02031L30.6437 7.13906C32.0125 5.77031 32.0125 3.55781 30.6437 2.18906L29.475 1.02656ZM10.775 14.7766C10.3937 15.1578 10.1 15.6266 9.93125 16.1453L8.08125 21.6953C7.9 22.2328 8.04375 22.8266 8.44375 23.2328C8.84375 23.6391 9.4375 23.7766 9.98125 23.5953L15.5312 21.7453C16.0438 21.5766 16.5125 21.2828 16.9 20.9016L27.3563 10.4391L21.2313 4.31406L10.775 14.7766ZM6 3.67031C2.6875 3.67031 0 6.35781 0 9.67031V25.6703C0 28.9828 2.6875 31.6703 6 31.6703H22C25.3125 31.6703 28 28.9828 28 25.6703V19.6703C28 18.5641 27.1063 17.6703 26 17.6703C24.8937 17.6703 24 18.5641 24 19.6703V25.6703C24 26.7766 23.1063 27.6703 22 27.6703H6C4.89375 27.6703 4 26.7766 4 25.6703V9.67031C4 8.56406 4.89375 7.67031 6 7.67031H12C13.1062 7.67031 14 6.77656 14 5.67031C14 4.56406 13.1062 3.67031 12 3.67031H6Z" fill="#2557A7"/>
                                                <defs>
                                                <linearGradient id="paint0_linear_6710_358" x1="15.8352" y1="0" x2="15.8352" y2="31.6703" gradientUnits="userSpaceOnUse">
                                                <stop stop-color="#2557A7"/>
                                                <stop offset="1" stop-color="#176FB3"/>
                                                </linearGradient>
                                                </defs>
                                            </svg>
                                        </a>
                                        <a class="add_edit_edu" data-add-edit="Add" data-education="<?php echo $edu_tot_count ?? '0'; ?>">
                                            <svg width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="19.5" cy="19.5" r="19.5" fill="#2557A7"/>
                                                <path d="M21.1154 10.6154C21.1154 9.72187 20.3935 9 19.5 9C18.6065 9 17.8846 9.72187 17.8846 10.6154V17.8846H10.6154C9.72187 17.8846 9 18.6065 9 19.5C9 20.3935 9.72187 21.1154 10.6154 21.1154H17.8846V28.3846C17.8846 29.2781 18.6065 30 19.5 30C20.3935 30 21.1154 29.2781 21.1154 28.3846V21.1154H28.3846C29.2781 21.1154 30 20.3935 30 19.5C30 18.6065 29.2781 17.8846 28.3846 17.8846H21.1154V10.6154Z" fill="#F6F6F6"/>
                                                <defs>
                                                <linearGradient id="paint0_linear_6485_153" x1="19.5" y1="0" x2="19.5" y2="39" gradientUnits="userSpaceOnUse">
                                                <stop stop-color="#2557A7"/>
                                                <stop offset="1" stop-color="#1377B7"/>
                                                </linearGradient>
                                                </defs>
                                            </svg>
                                        </a>
                                    <?php } ?>
                                    </div>
                                </div>

                                <div class="px-4 px-lg-5 pt-4 pb-5 d-flex" style="gap: 16px;">
                                    <svg style="min-width: fit-content;" width="32" height="23" viewBox="0 0 32 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16.0008 0C15.5958 0 15.1958 0.0699999 14.8158 0.205L0.79077 5.27C0.31577 5.445 0.000769613 5.895 0.000769613 6.4C0.000769613 6.905 0.31577 7.355 0.79077 7.53L3.68577 8.575C2.86577 9.865 2.40077 11.39 2.40077 12.995V14.4C2.40077 15.82 1.86077 17.285 1.28577 18.44C0.96077 19.09 0.59077 19.73 0.16077 20.32C0.000769615 20.535 -0.0442304 20.815 0.0457696 21.07C0.13577 21.325 0.34577 21.515 0.60577 21.58L3.80577 22.38C4.01577 22.435 4.24077 22.395 4.42577 22.28C4.61077 22.165 4.74077 21.975 4.78077 21.76C5.21077 19.62 4.99577 17.7 4.67577 16.325C4.51577 15.615 4.30077 14.89 4.00077 14.225V12.995C4.00077 11.485 4.51077 10.06 5.39577 8.92C6.04077 8.145 6.87577 7.52 7.85577 7.135L15.7058 4.05C16.1158 3.89 16.5808 4.09 16.7408 4.5C16.9008 4.91 16.7008 5.375 16.2908 5.535L8.44077 8.62C7.82077 8.865 7.27577 9.24 6.83077 9.7L14.8108 12.58C15.1908 12.715 15.5908 12.785 15.9958 12.785C16.4008 12.785 16.8008 12.715 17.1808 12.58L31.2108 7.53C31.6858 7.36 32.0008 6.905 32.0008 6.4C32.0008 5.895 31.6858 5.445 31.2108 5.27L17.1858 0.205C16.8058 0.0699999 16.4058 0 16.0008 0ZM6.40077 18.8C6.40077 20.565 10.7008 22.4 16.0008 22.4C21.3008 22.4 25.6008 20.565 25.6008 18.8L24.8358 11.53L17.7258 14.1C17.1708 14.3 16.5858 14.4 16.0008 14.4C15.4158 14.4 14.8258 14.3 14.2758 14.1L7.16577 11.53L6.40077 18.8Z" fill="#176FB3"/>
                                        <defs>
                                        <linearGradient id="paint0_linear_6910_909" x1="16.0004" y1="0" x2="16.0004" y2="22.4044" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#176FB3"/>
                                        <stop offset="1" stop-color="#1377B7"/>
                                        </linearGradient>
                                        </defs>
                                    </svg>


                                    <div>
                                        <div>
                                            <h5 class="j-title mb-1"><?php echo $degree_items; ?></h5>
                                        </div>
                                        <p class="duration mb-2"><?php echo get_month_from_number($edu_vals['edu_start_month']).' '.$edu_vals['edu_start_year'].' to '.get_month_from_number($edu_vals['edu_end_month']).' '.$edu_vals['edu_complete_year']; ?></p>
                                        <?php if($edu_vals['edu_grade'] != ''){ ?>
                                            <div class="mb-3">
                                                <p class="list-text-xs">Grade: <?php echo $edu_vals['edu_grade']; ?></p>
                                            </div>
                                        <?php } ?>
                                        <?php if(is_array($edu_vals['skill'])){
                                                $d_items = '';
                                                $d_skills = $edu_vals['skill'];
                                        ?>
                                            <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 8px;">
                                                <p class="list-text-xs mr-1">Skills:</p>
                                                <?php
                                                foreach ($d_skills as $d_keys => $d_vals){
                                                    if(!is_array($ed_value)){
                                                        list ( $skill_pre, $skill_name ) = explode(':>',$d_vals);
                                                             $d_items = explode(':>',$d_vals); ?>
                                                      <span class="skill-badge"><?php echo $skill_name; ?></span>
                                                      <?php }  else{
                                            foreach ($d_vals as $ed_keys => $ed_vals){
                                                list ( $skill_pre, $skill_name ) = explode(':>',$ed_vals); ?>
                                                <span class="skill-badge "><?php echo $skill_name; ?></span>
                                            <?php }
                                            }
                                            } ?>
                                            </div>
                                        <?php  }?>
                                        <?php if(taoh_title_desc_decode($edu_vals['edu_activities']) != ''){?>
                                            <h6 class="list-text-xs mb-2">Activities:
                                            <span><?php echo taoh_title_desc_decode($edu_vals['edu_activities']); ?></span></h6>
                                        <?php }?>
                                        <?php if(taoh_title_desc_decode($edu_vals['edu_description']) != ''){?>
                                            <h6 class="list-text-xs mb-2">Description:</h6>
                                            <p class="list-text-xxs"><?php echo taoh_title_desc_decode($edu_vals['edu_description']); ?></p>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php } ?>
                    </div>
                    <?php } }?>
                        <!-- <h5 class="p-field-title mb-4">Can you tell us more about your Career journey !</h5> -->
                        <!-- <div class="profile-card-list mb-5">
                            <div class="heading px-4 px-lg-5">
                                <h5 class="d-flex align-items-center" style="gap: 16px;">
                                    <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.98507 0.1046L0.798648 4.04248C0.224497 4.2886 -0.0953875 4.9039 0.031746 5.51099C0.158879 6.11808 0.69202 6.56109 1.31538 6.56109V6.88925C1.31538 7.43481 1.7542 7.87372 2.29964 7.87372H18.704C19.2494 7.87372 19.6882 7.43481 19.6882 6.88925V6.56109C20.3116 6.56109 20.8488 6.12218 20.9719 5.51099C21.0949 4.89979 20.775 4.2845 20.205 4.04248L11.0185 0.1046C10.6905 -0.0348667 10.3132 -0.0348667 9.98507 0.1046ZM5.25242 9.18635H2.62773V17.2385C2.60312 17.2508 2.57852 17.2672 2.55391 17.2836L0.585392 18.5962C0.105565 18.9162 -0.111792 19.5151 0.0563525 20.0689C0.224497 20.6226 0.737132 21 1.31538 21H19.6882C20.2665 21 20.775 20.6226 20.9432 20.0689C21.1113 19.5151 20.898 18.9162 20.4141 18.5962L18.4456 17.2836C18.421 17.2672 18.3964 17.2549 18.3718 17.2385V9.18635H15.7512V17.0621H14.1108V9.18635H11.4861V17.0621H9.51755V9.18635H6.89285V17.0621H5.25242V9.18635ZM10.5018 2.62321C10.8499 2.62321 11.1837 2.7615 11.4298 3.00766C11.6759 3.25383 11.8142 3.5877 11.8142 3.93583C11.8142 4.28396 11.6759 4.61784 11.4298 4.864C11.1837 5.11017 10.8499 5.24846 10.5018 5.24846C10.1537 5.24846 9.81995 5.11017 9.57384 4.864C9.32772 4.61784 9.18946 4.28396 9.18946 3.93583C9.18946 3.5877 9.32772 3.25383 9.57384 3.00766C9.81995 2.7615 10.1537 2.62321 10.5018 2.62321Z" fill="#1474B6"/>
                                    </svg>
                                    <span>Boston University</span>
                                </h5>
                                <div class="d-flex align-items-center" style="gap: 12px;">
                                    <a href="#">
                                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M29.475 1.02656C28.1063 -0.342188 25.8937 -0.342188 24.525 1.02656L22.6437 2.90156L28.7625 9.02031L30.6437 7.13906C32.0125 5.77031 32.0125 3.55781 30.6437 2.18906L29.475 1.02656ZM10.775 14.7766C10.3937 15.1578 10.1 15.6266 9.93125 16.1453L8.08125 21.6953C7.9 22.2328 8.04375 22.8266 8.44375 23.2328C8.84375 23.6391 9.4375 23.7766 9.98125 23.5953L15.5312 21.7453C16.0438 21.5766 16.5125 21.2828 16.9 20.9016L27.3563 10.4391L21.2313 4.31406L10.775 14.7766ZM6 3.67031C2.6875 3.67031 0 6.35781 0 9.67031V25.6703C0 28.9828 2.6875 31.6703 6 31.6703H22C25.3125 31.6703 28 28.9828 28 25.6703V19.6703C28 18.5641 27.1063 17.6703 26 17.6703C24.8937 17.6703 24 18.5641 24 19.6703V25.6703C24 26.7766 23.1063 27.6703 22 27.6703H6C4.89375 27.6703 4 26.7766 4 25.6703V9.67031C4 8.56406 4.89375 7.67031 6 7.67031H12C13.1062 7.67031 14 6.77656 14 5.67031C14 4.56406 13.1062 3.67031 12 3.67031H6Z" fill="#2557A7"/>
                                            <defs>
                                            <linearGradient id="paint0_linear_6710_358" x1="15.8352" y1="0" x2="15.8352" y2="31.6703" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#2557A7"/>
                                            <stop offset="1" stop-color="#176FB3"/>
                                            </linearGradient>
                                            </defs>
                                        </svg>
                                    </a>
                                    <a href="#">
                                        <svg width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="19.5" cy="19.5" r="19.5" fill="#2557A7"/>
                                            <path d="M21.1154 10.6154C21.1154 9.72187 20.3935 9 19.5 9C18.6065 9 17.8846 9.72187 17.8846 10.6154V17.8846H10.6154C9.72187 17.8846 9 18.6065 9 19.5C9 20.3935 9.72187 21.1154 10.6154 21.1154H17.8846V28.3846C17.8846 29.2781 18.6065 30 19.5 30C20.3935 30 21.1154 29.2781 21.1154 28.3846V21.1154H28.3846C29.2781 21.1154 30 20.3935 30 19.5C30 18.6065 29.2781 17.8846 28.3846 17.8846H21.1154V10.6154Z" fill="#F6F6F6"/>
                                            <defs>
                                            <linearGradient id="paint0_linear_6485_153" x1="19.5" y1="0" x2="19.5" y2="39" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#2557A7"/>
                                            <stop offset="1" stop-color="#1377B7"/>
                                            </linearGradient>
                                            </defs>
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <div class="px-4 px-lg-5 pt-4 pb-5 d-flex" style="gap: 16px;">
                                <svg style="min-width: fit-content;" width="32" height="23" viewBox="0 0 32 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.0008 0C15.5958 0 15.1958 0.0699999 14.8158 0.205L0.79077 5.27C0.31577 5.445 0.000769613 5.895 0.000769613 6.4C0.000769613 6.905 0.31577 7.355 0.79077 7.53L3.68577 8.575C2.86577 9.865 2.40077 11.39 2.40077 12.995V14.4C2.40077 15.82 1.86077 17.285 1.28577 18.44C0.96077 19.09 0.59077 19.73 0.16077 20.32C0.000769615 20.535 -0.0442304 20.815 0.0457696 21.07C0.13577 21.325 0.34577 21.515 0.60577 21.58L3.80577 22.38C4.01577 22.435 4.24077 22.395 4.42577 22.28C4.61077 22.165 4.74077 21.975 4.78077 21.76C5.21077 19.62 4.99577 17.7 4.67577 16.325C4.51577 15.615 4.30077 14.89 4.00077 14.225V12.995C4.00077 11.485 4.51077 10.06 5.39577 8.92C6.04077 8.145 6.87577 7.52 7.85577 7.135L15.7058 4.05C16.1158 3.89 16.5808 4.09 16.7408 4.5C16.9008 4.91 16.7008 5.375 16.2908 5.535L8.44077 8.62C7.82077 8.865 7.27577 9.24 6.83077 9.7L14.8108 12.58C15.1908 12.715 15.5908 12.785 15.9958 12.785C16.4008 12.785 16.8008 12.715 17.1808 12.58L31.2108 7.53C31.6858 7.36 32.0008 6.905 32.0008 6.4C32.0008 5.895 31.6858 5.445 31.2108 5.27L17.1858 0.205C16.8058 0.0699999 16.4058 0 16.0008 0ZM6.40077 18.8C6.40077 20.565 10.7008 22.4 16.0008 22.4C21.3008 22.4 25.6008 20.565 25.6008 18.8L24.8358 11.53L17.7258 14.1C17.1708 14.3 16.5858 14.4 16.0008 14.4C15.4158 14.4 14.8258 14.3 14.2758 14.1L7.16577 11.53L6.40077 18.8Z" fill="#176FB3"/>
                                    <defs>
                                    <linearGradient id="paint0_linear_6910_909" x1="16.0004" y1="0" x2="16.0004" y2="22.4044" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#176FB3"/>
                                    <stop offset="1" stop-color="#1377B7"/>
                                    </linearGradient>
                                    </defs>
                                </svg>


                                <div>
                                    <div>
                                        <h5 class="j-title mb-1">MBA in Marketing Management</h5>
                                    </div>
                                    <p class="duration mb-2">May 2020 to June 2022</p>
                                    <div class="mb-3">
                                        <p class="list-text-xs">Grade: A+</p>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 8px;">
                                        <p class="list-text-xs mr-1">Skills:</p>
                                        <span class="skill-badge">Skill 1</span>
                                        <span class="skill-badge">Skill 2</span>
                                        <span class="skill-badge">Skill 3</span>
                                        <span class="skill-badge">Skill 4</span>
                                        <span class="skill-badge">Skill 5</span>
                                    </div>
                                    <h6 class="list-text-xs mb-2">Activities: <span>Society of Management Professionals</span></h6>
                                    <h6 class="list-text-xs mb-2">Description:</h6>
                                    <p class="list-text-xxs">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book <a href="#" style="color: #2557A7;">Read More...</a></p>
                                </div>
                            </div>
                        </div> -->


                        <div class="profile-card-list">
                            <div class="heading px-4 px-lg-5 justify-content-center">
                                <h5 class="text-center">
                                    Add Your Education Details
                                </h5>
                            </div>

                            <div class="px-4 px-lg-5 pt-4 pb-5 d-flex flex-column align-items-center" style="gap: 16px;">
                                <svg width="48" height="34" viewBox="0 0 48 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M24.0006 0C23.3931 0 22.7931 0.104146 22.2231 0.305L1.18613 7.84073C0.473643 8.10109 0.00115439 8.7706 0.00115439 9.52194C0.00115439 10.2733 0.473643 10.9428 1.18613 11.2032L5.52852 12.7579C4.29855 14.6772 3.60107 16.9461 3.60107 19.334V21.4244C3.60107 23.5371 2.79109 25.7167 1.92861 27.4351C1.44112 28.4022 0.886133 29.3544 0.241149 30.2322C0.0011544 30.552 -0.066344 30.9686 0.0686528 31.348C0.20365 31.7274 0.518642 32.0101 0.908633 32.1068L5.70852 33.297C6.02351 33.3789 6.361 33.3194 6.63849 33.1483C6.91599 32.9772 7.11098 32.6945 7.17098 32.3746C7.81597 29.1907 7.49347 26.3341 7.01349 24.2884C6.77349 23.2321 6.451 22.1534 6.00101 21.164V19.334C6.00101 17.0874 6.76599 14.9673 8.09346 13.2712C9.06094 12.1182 10.3134 11.1883 11.7834 10.6155L23.5581 6.02561C24.1731 5.78756 24.8706 6.08512 25.1105 6.69512C25.3505 7.30512 25.0506 7.99694 24.4356 8.23499L12.6609 12.8249C11.7309 13.1894 10.9134 13.7473 10.2459 14.4317L22.2156 18.7166C22.7856 18.9174 23.3856 19.0216 23.9931 19.0216C24.6006 19.0216 25.2006 18.9174 25.7705 18.7166L46.815 11.2032C47.5275 10.9502 48 10.2733 48 9.52194C48 8.7706 47.5275 8.10109 46.815 7.84073L25.778 0.305C25.208 0.104146 24.6081 0 24.0006 0ZM9.60092 27.9707C9.60092 30.5967 16.0508 33.3268 24.0006 33.3268C31.9504 33.3268 38.4002 30.5967 38.4002 27.9707L37.2528 17.1544L26.588 20.978C25.7555 21.2756 24.8781 21.4244 24.0006 21.4244C23.1231 21.4244 22.2381 21.2756 21.4131 20.978L10.7484 17.1544L9.60092 27.9707Z" fill="#176FB3"/>
                                    <defs>
                                    <linearGradient id="paint0_linear_6910_1039" x1="24" y1="0" x2="24" y2="33.3333" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#176FB3"/>
                                    <stop offset="1" stop-color="#1377B7"/>
                                    </linearGradient>
                                    </defs>
                                </svg>

                                <h5 class="text-center list-text-md my-3"  style="max-width: 522px;">Adding your academic journey can help you connect with the right opportunities—add it now!</h5>
                                <button class="btn continue-btn add_edit_edu" type="button" data-add-edit="Add" data-education="<?php echo $edu_tot_count ?? '0'; ?>">
                                    <span>Add Education Details</span>
                                </button>
                            </div>
                        </div>


                         <!-- for mobile screen -->
                        <!-- Mobile Navigation Buttons -->
                        <div class="d-flex justify-content-between d-xl-none mt-4">
                            <button type="button" class="btn tab-nav-btn prev-btn" data-direction="prev">Previous</button>
                            <button type="button" class="btn tab-nav-btn ml-auto next-btn" data-direction="next">Next</button>
                        </div>
                    </div>

                    <!-- frm-blc-5 -->
                    <div class="tab-pane pb-5 fade" id="form-block-5" role="tabpanel" aria-labelledby="block-5-tab" style="max-width: 953px;">
                        <div class="d-flex align-items-center d-xl-none p-3 mb-4" style="gap: 12px; margin: 0 -24px; background-color: #2557A7;">
                            <svg style="min-width: fit-content;" width="16" height="16" viewBox="0 0 15 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.25082 6C6.04659 6 6.80978 5.68393 7.37247 5.12132C7.93517 4.55871 8.25129 3.79565 8.25129 3C8.25129 2.20435 7.93517 1.44129 7.37247 0.87868C6.80978 0.316071 6.04659 0 5.25082 0C4.45505 0 3.69187 0.316071 3.12917 0.87868C2.56647 1.44129 2.25035 2.20435 2.25035 3C2.25035 3.79565 2.56647 4.55871 3.12917 5.12132C3.69187 5.68393 4.45505 6 5.25082 6ZM4.17956 7.125C1.8706 7.125 0 8.99531 0 11.3039C0 11.6883 0.311767 12 0.696203 12H7.56681C7.49414 11.7937 7.48007 11.5688 7.53399 11.3484L7.88561 9.93984C7.95124 9.675 8.0872 9.43594 8.27942 9.24375L9.2241 8.29922C8.47164 7.57266 7.4496 7.125 6.31974 7.125H4.17956ZM14.3882 5.52422C14.0225 5.15859 13.4294 5.15859 13.0614 5.52422L12.3722 6.21328L14.0366 7.87734L14.7257 7.18828C15.0914 6.82266 15.0914 6.22969 14.7257 5.86172L14.3882 5.52422ZM8.81153 9.77344C8.71542 9.86953 8.64744 9.98906 8.61463 10.1227L8.26301 11.5312C8.23019 11.6602 8.2677 11.7938 8.36146 11.8875C8.45523 11.9813 8.58884 12.0188 8.71777 11.9859L10.1266 11.6344C10.2579 11.6016 10.3797 11.5336 10.4759 11.4375L13.5045 8.40703L11.8401 6.74297L8.81153 9.77344Z" fill="#ffffff" />
                            </svg>
                            <h6 class="p-field-title text-white">About, Hobbies & Interests</h6>
                            <?php // if(isset($data->step5) && $data->step5 == 5) {
                                if($about_me != '' || $fun_fact != ''){
                            ?>

                                <svg style="min-width: fit-content;" width="18" height="18" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z"
                                    fill="#ffffff"/>
                                </svg>
                            <?php }else{ ?>
                                <!-- <svg style="min-width: fit-content;" width="18" height="18" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z" fill="#A7A7A7"/>
                                </svg> -->
                            <?php } ?>
                        </div>


                    <form method="post" name="step5_form" id="step5_form" action="<?php echo TAOH_ACTION_URL .'/settings'; ?>" style="max-width: 953px;" onsubmit="showLoading(event)">
                        <input type="hidden" name="taoh_action" value="old_profile">
                        <input type="hidden" name="taoh_ptoken" value="<?php echo $ptoken; ?>">
                        <div>
                            <h5 class="p-field-title py-3">About, Hobbies & Interests</h5>
                            <hr class="mt-0" style="border-top: 1px solid #D3D3D3;">

                            <div class="row mt-4">
                                <div class="form-group col-lg-6">
                                    <label class="text-label" for="">About Me</label>
                                    <textarea  class="form-control form--control" rows="8" maxlength="500" name="aboutme" style="min-height: 200px;"><?php echo taoh_title_desc_decode(trim(@$data->aboutme)); ?> </textarea>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label class="text-label" for="">Fun Fact (Great for ice-breakers)</label>
                                    <textarea class="form-control form--control" rows="8" maxlength="500" name="funfact" style="min-height: 200px;" ><?php echo taoh_title_desc_decode(trim($fun_fact)); ?> </textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label class="text-label" for="">Hobbies and Interests</label>
                                    <select name="hobbies[]" id="hobbies" multiple="hobbies"  class="select2 form-control hobbies-field">
                                        <?php foreach(PROFESSIONAL_HOBBIES as $hkey=>$hobby){ ?>
                                            <option value="<?php echo $hkey; ?>" <?php echo (is_array($hobbies) && in_array($hkey,$hobbies)) ? 'selected' : ''; ?>><?php echo $hobby; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label class="text-label" for="">Where to find me online? <span style="font-size: 16px;">(public link e.g. LinkedIn)</span></label>
                                    <input class="form-control form--control" type="url" value="<?php echo @$data->mylink; ?>" name="mylink" id="mylink" placeholder="https://www.linkedin.com/">
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <button name="step5" id="step5" class="btn s-btn my-3" value="5">Save</button> <!--  and Continue -->
                            </div>
                        </div>
                    </form>

                        <!-- <div class="profile-card-list mb-4 p-3 p-lg-4" style="max-width: 783px;">
                            <h6 class="list-text-md mb-3" style="font-weight: 500;">About me</h6>
                            <!-#- <p class="list-text-nml my-3">Tell who you are to employers and your fellow professionals and tell what makes you stand out from others !</p> -#->
                            <p class="list-text-nml mb-3"><?php // echo $about_me; ?></p>
                            <a class="btn add-btn add_edit_aboutme">Add About me</a>
                        </div>

                        <div class="profile-card-list mb-4 p-3 p-lg-4" style="max-width: 783px;">
                            <h6 class="list-text-md mb-3" style="font-weight: 500;">Fun Fact</h6>
                            <p class="list-text-nml mb-3"><?php // echo $fun_fact; ?></p>
                            <a class="btn add-btn add_edit_funfact">Add Fun Fact</a>
                        </div>

                        <div class="profile-card-list mb-4 p-3 p-lg-4" style="max-width: 783px;">
                            <h6 class="list-text-md mb-3" style="font-weight: 500;">Hobbies and Interests</h6>
                            <!-#- <p class="list-text-nml my-3">Tell who you are to employers and your fellow professionals and tell what makes you stand out from others !</p> -#->
                            <?php
                                // $fun_factArr = explode("##$##",$fun_fact);
                                // foreach ($hobbies as $f_keys => $f_vals){ ?>
                                    <p class="list-text-nml mb-3"><?php // echo PROFESSIONAL_HOBBIES[$f_vals]; ?></p>
                               <?php // } ?>
                            <!-#- <p class="list-text-nml my-3"><?php // echo $fun_fact; ?></p> -#->
                            <a class="btn add-btn add_edit_hobbies">Add Interests</a>
                        </div>

                        <div class="profile-card-list mb-4 p-3 p-lg-4" style="max-width: 783px;">
                            <h6 class="list-text-md" style="font-weight: 500;">Where to find me online? (public link e.g. LinkedIn)</h6>
                            <!-#- <p class="list-text-nml my-3">Tell who you are to employers and your fellow professionals and tell what makes you stand out from others !</p> -#->
                            <p class="list-text-nml my-3"><input class="form-control form--control" type="text" value="<?php // echo @$data->mylink; ?>" name="mylink" id="mylink"></p>
                        </div> -->


                         <!-- for mobile screen -->
                        <!-- Mobile Navigation Buttons -->
                        <div class="d-flex justify-content-between d-xl-none mt-4">
                            <button type="button" class="btn tab-nav-btn prev-btn" data-direction="prev">Previous</button>
                            <button type="button" class="btn tab-nav-btn ml-auto next-btn" data-direction="next">Next</button>
                        </div>

                     </div>

                <!-- form-block-6 -->
                <div class="tab-pane pb-5 fade" id="form-block-6" role="tabpanel" aria-labelledby="block-6-tab" style="max-width: 953px;">
                    <div class="d-flex align-items-center d-xl-none p-3 mb-4" style="gap: 12px; margin: 0 -24px; background-color: #2557A7;">
                        <svg style="min-width: fit-content;" width="16" height="16" viewBox="0 0 15 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.82143 5.0625C4.82143 3.50859 6.02009 2.25 7.5 2.25C8.56808 2.25 9.48884 2.90391 9.92076 3.85664C10.1752 4.41914 10.8147 4.6582 11.3471 4.39102C11.8795 4.12383 12.1105 3.45234 11.856 2.89336C11.0826 1.18477 9.42522 0 7.5 0C4.83817 0 2.67857 2.26758 2.67857 5.0625V6.75H2.14286C0.960938 6.75 0 7.75898 0 9V15.75C0 16.991 0.960938 18 2.14286 18H12.8571C14.0391 18 15 16.991 15 15.75V9C15 7.75898 14.0391 6.75 12.8571 6.75H4.82143V5.0625Z" fill="#ffffff" />
                        </svg>
                        <h6 class="p-field-title text-white">Privacy</h6>
                        <svg style="min-width: fit-content;" width="18" height="18" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10ZM7.20703 4.08203L4.70703 6.58203C4.52344 6.76562 4.22656 6.76562 4.04492 6.58203L2.79492 5.33203C2.61133 5.14844 2.61133 4.85156 2.79492 4.66992C2.97852 4.48828 3.27539 4.48633 3.45703 4.66992L4.375 5.58789L6.54297 3.41797C6.72656 3.23438 7.02344 3.23438 7.20508 3.41797C7.38672 3.60156 7.38867 3.89844 7.20508 4.08008L7.20703 4.08203Z"
                            fill="#ffffff"/>
                        </svg>
                    </div>

                    <div class="profile-card-list mb-4" style="border: none;">
                        <h6 class="p-field-title d-flex align-items-center py-3" style="font-weight: 500; gap: 8px;"><svg style="min-width: fit-content;" width="21" height="21" viewBox="0 0 15 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.25082 6C6.04659 6 6.80978 5.68393 7.37247 5.12132C7.93517 4.55871 8.25129 3.79565 8.25129 3C8.25129 2.20435 7.93517 1.44129 7.37247 0.87868C6.80978 0.316071 6.04659 0 5.25082 0C4.45505 0 3.69187 0.316071 3.12917 0.87868C2.56647 1.44129 2.25035 2.20435 2.25035 3C2.25035 3.79565 2.56647 4.55871 3.12917 5.12132C3.69187 5.68393 4.45505 6 5.25082 6ZM4.17956 7.125C1.8706 7.125 0 8.99531 0 11.3039C0 11.6883 0.311767 12 0.696203 12H7.56681C7.49414 11.7937 7.48007 11.5688 7.53399 11.3484L7.88561 9.93984C7.95124 9.675 8.0872 9.43594 8.27942 9.24375L9.2241 8.29922C8.47164 7.57266 7.4496 7.125 6.31974 7.125H4.17956ZM14.3882 5.52422C14.0225 5.15859 13.4294 5.15859 13.0614 5.52422L12.3722 6.21328L14.0366 7.87734L14.7257 7.18828C15.0914 6.82266 15.0914 6.22969 14.7257 5.86172L14.3882 5.52422ZM8.81153 9.77344C8.71542 9.86953 8.64744 9.98906 8.61463 10.1227L8.26301 11.5312C8.23019 11.6602 8.2677 11.7938 8.36146 11.8875C8.45523 11.9813 8.58884 12.0188 8.71777 11.9859L10.1266 11.6344C10.2579 11.6016 10.3797 11.5336 10.4759 11.4375L13.5045 8.40703L11.8401 6.74297L8.81153 9.77344Z" fill="#2557A7"></path>
                            </svg><span>Setup your email preferences</span>
                        </h6>
                        <hr class="mt-0" style="border-top: 1px solid #D3D3D3;">
                        <p class="list-text-nml my-3">We get it—too many emails! Click the checkbox below to unsubscribe from promotional and reminder emails. You can always rejoin when you're ready!</p>
                        <p class="list-text-nml my-3"><div class="custom-control custom-checkbox" ><input id="tao-unsubscribe" <?php echo (($data->tao_unsubscribe_emails == "1" ) ) ?'checked': '';?> value="<?php echo ($data->tao_unsubscribe_emails == '0') ? '0' : '1'; ?>" type="checkbox" name="tao_unsubscribe_emails" class="custom-control-input current_role_checkbox">
                            <label for="tao-unsubscribe" class="custom-control-label fs-13 text-black lh-20 fw-medium current_role_checklabel">
                            Unsubscribe me
                        </label></div></p>
                    </div>

                    <div class="profile-card-list mb-5" style="border: none;">
                        <!-- <h6 class="list-text-md" style="font-weight: 500;"><svg width="15" height="12" viewBox="0 0 15 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.25082 6C6.04659 6 6.80978 5.68393 7.37247 5.12132C7.93517 4.55871 8.25129 3.79565 8.25129 3C8.25129 2.20435 7.93517 1.44129 7.37247 0.87868C6.80978 0.316071 6.04659 0 5.25082 0C4.45505 0 3.69187 0.316071 3.12917 0.87868C2.56647 1.44129 2.25035 2.20435 2.25035 3C2.25035 3.79565 2.56647 4.55871 3.12917 5.12132C3.69187 5.68393 4.45505 6 5.25082 6ZM4.17956 7.125C1.8706 7.125 0 8.99531 0 11.3039C0 11.6883 0.311767 12 0.696203 12H7.56681C7.49414 11.7937 7.48007 11.5688 7.53399 11.3484L7.88561 9.93984C7.95124 9.675 8.0872 9.43594 8.27942 9.24375L9.2241 8.29922C8.47164 7.57266 7.4496 7.125 6.31974 7.125H4.17956ZM14.3882 5.52422C14.0225 5.15859 13.4294 5.15859 13.0614 5.52422L12.3722 6.21328L14.0366 7.87734L14.7257 7.18828C15.0914 6.82266 15.0914 6.22969 14.7257 5.86172L14.3882 5.52422ZM8.81153 9.77344C8.71542 9.86953 8.64744 9.98906 8.61463 10.1227L8.26301 11.5312C8.23019 11.6602 8.2677 11.7938 8.36146 11.8875C8.45523 11.9813 8.58884 12.0188 8.71777 11.9859L10.1266 11.6344C10.2579 11.6016 10.3797 11.5336 10.4759 11.4375L13.5045 8.40703L11.8401 6.74297L8.81153 9.77344Z" fill="black"></path>
                            </svg>Unlist yourself from directory?</h6>
                        <p class="list-text-nml my-3">By selecting Yes you will be unlisted from our directory at the same time you can't see other users too</p>
                        <p class="list-text-nml my-3"><?php // echo field_yes_no('unlist_dir',@$data->unlist_dir); ?></p> -->

                        <?php
                            // if (!$show_name_slug_information) {
                                showUnlistmeField($data);
                            // }
                        ?>
                    </div>

                    <div class="profile-card-list mb-4"  style="border: none;">
                        <h6 class="p-field-title d-flex align-items-center m-0" style="gap: 8px;">
                            <svg width="22" height="25" viewBox="0 0 22 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.63929 0.864258C6.90446 0.332031 7.44955 0 8.04375 0H13.9563C14.5504 0 15.0955 0.332031 15.3607 0.864258L15.7143 1.5625H20.4286C21.2978 1.5625 22 2.26074 22 3.125C22 3.98926 21.2978 4.6875 20.4286 4.6875H1.57143C0.702232 4.6875 0 3.98926 0 3.125C0 2.26074 0.702232 1.5625 1.57143 1.5625H6.28571L6.63929 0.864258ZM1.57143 6.25H20.4286V21.875C20.4286 23.5986 19.0192 25 17.2857 25H4.71429C2.9808 25 1.57143 23.5986 1.57143 21.875V6.25ZM6.28571 9.375C5.85357 9.375 5.5 9.72656 5.5 10.1562V21.0938C5.5 21.5234 5.85357 21.875 6.28571 21.875C6.71786 21.875 7.07143 21.5234 7.07143 21.0938V10.1562C7.07143 9.72656 6.71786 9.375 6.28571 9.375ZM11 9.375C10.5679 9.375 10.2143 9.72656 10.2143 10.1562V21.0938C10.2143 21.5234 10.5679 21.875 11 21.875C11.4321 21.875 11.7857 21.5234 11.7857 21.0938V10.1562C11.7857 9.72656 11.4321 9.375 11 9.375ZM15.7143 9.375C15.2821 9.375 14.9286 9.72656 14.9286 10.1562V21.0938C14.9286 21.5234 15.2821 21.875 15.7143 21.875C16.1464 21.875 16.5 21.5234 16.5 21.0938V10.1562C16.5 9.72656 16.1464 9.375 15.7143 9.375Z" fill="#2557A7"/>
                            </svg>
                            <span>Delete your account</span>
                        </h6>
                        <hr class="my-2" style="border-top: 1px solid #D3D3D3;">
                        <p class="list-text-nml my-3">If you choose to delete your account! We respect your choice. You can delete your account by clicking the delete account button here.</p>
                        <button type="button" class="btn d-btn" data-toggle="modal" data-target="#delete-modal">
                            Delete Account
                        </button>
                    </div>


                     <!-- for mobile screen -->
                        <!-- Mobile Navigation Buttons -->
                        <div class="d-flex justify-content-between d-xl-none mt-4">
                            <button class="btn tab-nav-btn" data-direction="prev">Previous</button>
                            <!-- <button type="button" class="btn tab-nav-btn ml-auto next-btn" data-direction="next">Next</button> remove commented after form block 7 added -->
                        </div>

                </div>

                <!-- form block 7 -->
                <div class="tab-pane pb-5 fade" id="form-block-7" role="tabpanel" aria-labelledby="block-7-tab" >
                    <h5 class="p-field-title py-3">My Network</h5>
                    <hr class="mt-0" style="border-top: 1px solid #D3D3D3;">


                    <div class="d-flex flex-column flex-sm-row">
                        <input type="text" class="form-control ntw-search-input" placeholder="Search by name, skill, location or company name">
                        <button type="button" class="btn ntw-search-btn">Search</button>
                    </div>

                    <ul class="my-ntw-lists mt-3">
                        <li class="ntw-list mb-3 px-3 py-2">
                            <div>
                                <a href="#" class="mb-3">
                                    <img class="ntw-pro-img" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/profile_room_1.png" alt="">
                                </a>
                                <p class="p-type-badge mt-2">Professional</p>
                            </div>
                            <div class="d-flex flex-wrap align-items-center justify-content-between" style="flex: 1; gap: 12px;">
                                <div>
                                    <p class="pro-text-sm mb-2">Andrew</p>
                                    <p class="pro-text-sm mb-2">
                                        <svg width="10" height="13" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="#212121" fill-opacity="0.8"/>
                                        </svg>
                                        Boston,Massachusetts, US
                                    </p>
                                    <div class="d-flex align-items-center flex-wrap mb-2" style="gap: 12px;">
                                        <p class="pro-text-sm d-flex align-items-center" style="gap: 8px">
                                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0 0V13H13V0H0ZM9.18415 9.43661L6.5 12.0105L3.81585 9.43661L5.6875 4.09732L3.81585 1.58437H9.18125L7.3125 4.09732L9.18415 9.43661Z" fill="#212121" fill-opacity="0.8"/>
                                            </svg>
                                            Data Analyst
                                        </p>
                                        <p class="pro-text-sm d-flex align-items-center" style="gap: 8px">
                                            <svg width="13" height="17" viewBox="0 0 13 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1.625 0C0.727865 0 0 0.713867 0 1.59375V15.4062C0 16.2861 0.727865 17 1.625 17H4.875V14.3438C4.875 13.4639 5.60286 12.75 6.5 12.75C7.39714 12.75 8.125 13.4639 8.125 14.3438V17H11.375C12.2721 17 13 16.2861 13 15.4062V1.59375C13 0.713867 12.2721 0 11.375 0H1.625ZM2.16667 7.96875C2.16667 7.67656 2.41042 7.4375 2.70833 7.4375H3.79167C4.08958 7.4375 4.33333 7.67656 4.33333 7.96875V9.03125C4.33333 9.32344 4.08958 9.5625 3.79167 9.5625H2.70833C2.41042 9.5625 2.16667 9.32344 2.16667 9.03125V7.96875ZM5.95833 7.4375H7.04167C7.33958 7.4375 7.58333 7.67656 7.58333 7.96875V9.03125C7.58333 9.32344 7.33958 9.5625 7.04167 9.5625H5.95833C5.66042 9.5625 5.41667 9.32344 5.41667 9.03125V7.96875C5.41667 7.67656 5.66042 7.4375 5.95833 7.4375ZM8.66667 7.96875C8.66667 7.67656 8.91042 7.4375 9.20833 7.4375H10.2917C10.5896 7.4375 10.8333 7.67656 10.8333 7.96875V9.03125C10.8333 9.32344 10.5896 9.5625 10.2917 9.5625H9.20833C8.91042 9.5625 8.66667 9.32344 8.66667 9.03125V7.96875ZM2.70833 3.1875H3.79167C4.08958 3.1875 4.33333 3.42656 4.33333 3.71875V4.78125C4.33333 5.07344 4.08958 5.3125 3.79167 5.3125H2.70833C2.41042 5.3125 2.16667 5.07344 2.16667 4.78125V3.71875C2.16667 3.42656 2.41042 3.1875 2.70833 3.1875ZM5.41667 3.71875C5.41667 3.42656 5.66042 3.1875 5.95833 3.1875H7.04167C7.33958 3.1875 7.58333 3.42656 7.58333 3.71875V4.78125C7.58333 5.07344 7.33958 5.3125 7.04167 5.3125H5.95833C5.66042 5.3125 5.41667 5.07344 5.41667 4.78125V3.71875ZM9.20833 3.1875H10.2917C10.5896 3.1875 10.8333 3.42656 10.8333 3.71875V4.78125C10.8333 5.07344 10.5896 5.3125 10.2917 5.3125H9.20833C8.91042 5.3125 8.66667 5.07344 8.66667 4.78125V3.71875C8.66667 3.42656 8.91042 3.1875 9.20833 3.1875Z" fill="#212121" fill-opacity="0.8"/>
                                            </svg>
                                            SAQ Analytics
                                        </p>
                                    </div>

                                    <ul class="skill-lists d-inline-flex flex-wrap" style="gap: 6px;">
                                        <li class="skill-list pro-text-sm">Skill 1</li>
                                        <li class="skill-list pro-text-sm">Skill 2</li>
                                        <li class="skill-list pro-text-sm">Skill 3</li>
                                    </ul>
                                </div>

                                <div>
                                    <button type="button" class="btn bor-btn px-4 mr-2">Chat</button>
                                    <button type="button" class="btn bor-btn">
                                        <svg width="24" height="19" viewBox="0 0 24 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3.6 4.75C3.6 3.49022 4.10571 2.28204 5.00589 1.39124C5.90606 0.500445 7.12696 0 8.4 0C9.67304 0 10.8939 0.500445 11.7941 1.39124C12.6943 2.28204 13.2 3.49022 13.2 4.75C13.2 6.00978 12.6943 7.21796 11.7941 8.10876C10.8939 8.99956 9.67304 9.5 8.4 9.5C7.12696 9.5 5.90606 8.99956 5.00589 8.10876C4.10571 7.21796 3.6 6.00978 3.6 4.75ZM0 17.8979C0 14.2426 2.9925 11.2812 6.68625 11.2812H10.1138C13.8075 11.2812 16.8 14.2426 16.8 17.8979C16.8 18.5064 16.3013 19 15.6862 19H1.11375C0.49875 19 0 18.5064 0 17.8979ZM17.7 7.42188H23.1C23.5988 7.42188 24 7.81895 24 8.3125C24 8.80606 23.5988 9.20312 23.1 9.20312H17.7C17.2013 9.20312 16.8 8.80606 16.8 8.3125C16.8 7.81895 17.2013 7.42188 17.7 7.42188Z" fill="black"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </li>
                        <li class="ntw-list mb-3 px-3 py-2">
                            <div>
                                <a href="#" class="mb-3">
                                    <img class="ntw-pro-img" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/profile_room_1.png" alt="">
                                </a>
                                <p class="p-type-badge mt-2">Professional</p>
                            </div>
                            <div class="d-flex flex-wrap align-items-center justify-content-between" style="flex: 1; gap: 12px;">
                                <div>
                                    <p class="pro-text-sm mb-2">Andrew</p>
                                    <p class="pro-text-sm mb-2">
                                        <svg width="10" height="13" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="#212121" fill-opacity="0.8"/>
                                        </svg>
                                        Boston,Massachusetts, US
                                    </p>
                                    <div class="d-flex align-items-center flex-wrap mb-2" style="gap: 12px;">
                                        <p class="pro-text-sm d-flex align-items-center" style="gap: 8px">
                                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0 0V13H13V0H0ZM9.18415 9.43661L6.5 12.0105L3.81585 9.43661L5.6875 4.09732L3.81585 1.58437H9.18125L7.3125 4.09732L9.18415 9.43661Z" fill="#212121" fill-opacity="0.8"/>
                                            </svg>
                                            Data Analyst
                                        </p>
                                        <p class="pro-text-sm d-flex align-items-center" style="gap: 8px">
                                            <svg width="13" height="17" viewBox="0 0 13 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1.625 0C0.727865 0 0 0.713867 0 1.59375V15.4062C0 16.2861 0.727865 17 1.625 17H4.875V14.3438C4.875 13.4639 5.60286 12.75 6.5 12.75C7.39714 12.75 8.125 13.4639 8.125 14.3438V17H11.375C12.2721 17 13 16.2861 13 15.4062V1.59375C13 0.713867 12.2721 0 11.375 0H1.625ZM2.16667 7.96875C2.16667 7.67656 2.41042 7.4375 2.70833 7.4375H3.79167C4.08958 7.4375 4.33333 7.67656 4.33333 7.96875V9.03125C4.33333 9.32344 4.08958 9.5625 3.79167 9.5625H2.70833C2.41042 9.5625 2.16667 9.32344 2.16667 9.03125V7.96875ZM5.95833 7.4375H7.04167C7.33958 7.4375 7.58333 7.67656 7.58333 7.96875V9.03125C7.58333 9.32344 7.33958 9.5625 7.04167 9.5625H5.95833C5.66042 9.5625 5.41667 9.32344 5.41667 9.03125V7.96875C5.41667 7.67656 5.66042 7.4375 5.95833 7.4375ZM8.66667 7.96875C8.66667 7.67656 8.91042 7.4375 9.20833 7.4375H10.2917C10.5896 7.4375 10.8333 7.67656 10.8333 7.96875V9.03125C10.8333 9.32344 10.5896 9.5625 10.2917 9.5625H9.20833C8.91042 9.5625 8.66667 9.32344 8.66667 9.03125V7.96875ZM2.70833 3.1875H3.79167C4.08958 3.1875 4.33333 3.42656 4.33333 3.71875V4.78125C4.33333 5.07344 4.08958 5.3125 3.79167 5.3125H2.70833C2.41042 5.3125 2.16667 5.07344 2.16667 4.78125V3.71875C2.16667 3.42656 2.41042 3.1875 2.70833 3.1875ZM5.41667 3.71875C5.41667 3.42656 5.66042 3.1875 5.95833 3.1875H7.04167C7.33958 3.1875 7.58333 3.42656 7.58333 3.71875V4.78125C7.58333 5.07344 7.33958 5.3125 7.04167 5.3125H5.95833C5.66042 5.3125 5.41667 5.07344 5.41667 4.78125V3.71875ZM9.20833 3.1875H10.2917C10.5896 3.1875 10.8333 3.42656 10.8333 3.71875V4.78125C10.8333 5.07344 10.5896 5.3125 10.2917 5.3125H9.20833C8.91042 5.3125 8.66667 5.07344 8.66667 4.78125V3.71875C8.66667 3.42656 8.91042 3.1875 9.20833 3.1875Z" fill="#212121" fill-opacity="0.8"/>
                                            </svg>
                                            SAQ Analytics
                                        </p>
                                    </div>

                                    <ul class="skill-lists d-inline-flex flex-wrap" style="gap: 6px;">
                                        <li class="skill-list pro-text-sm">Skill 1</li>
                                        <li class="skill-list pro-text-sm">Skill 2</li>
                                        <li class="skill-list pro-text-sm">Skill 3</li>
                                    </ul>
                                </div>

                                <div>
                                    <button type="button" class="btn bor-btn px-4 mr-2">Chat</button>
                                    <button type="button" class="btn bor-btn">
                                        <svg width="24" height="19" viewBox="0 0 24 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3.6 4.75C3.6 3.49022 4.10571 2.28204 5.00589 1.39124C5.90606 0.500445 7.12696 0 8.4 0C9.67304 0 10.8939 0.500445 11.7941 1.39124C12.6943 2.28204 13.2 3.49022 13.2 4.75C13.2 6.00978 12.6943 7.21796 11.7941 8.10876C10.8939 8.99956 9.67304 9.5 8.4 9.5C7.12696 9.5 5.90606 8.99956 5.00589 8.10876C4.10571 7.21796 3.6 6.00978 3.6 4.75ZM0 17.8979C0 14.2426 2.9925 11.2812 6.68625 11.2812H10.1138C13.8075 11.2812 16.8 14.2426 16.8 17.8979C16.8 18.5064 16.3013 19 15.6862 19H1.11375C0.49875 19 0 18.5064 0 17.8979ZM17.7 7.42188H23.1C23.5988 7.42188 24 7.81895 24 8.3125C24 8.80606 23.5988 9.20312 23.1 9.20312H17.7C17.2013 9.20312 16.8 8.80606 16.8 8.3125C16.8 7.81895 17.2013 7.42188 17.7 7.42188Z" fill="black"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </li>
                    </ul>


                    <!-- for mobile screen -->
                    <!-- Mobile Navigation Buttons -->
                    <div class="d-flex justify-content-between d-xl-none mt-4">
                        <button class="btn tab-nav-btn" data-direction="prev">Previous</button>
                    </div>
                </div>
    </div>

</div>


<form method="post" id ="post_form" action="<?php echo TAOH_ACTION_URL .'/settings'; ?>" onsubmit="showLoading(event)">
    <div class="hidden">
        <input type="hidden" name="taoh_action" value="old_profile">
        <input type="hidden" name="taoh_ptoken" value="<?php echo $ptoken; ?>">
    </div>
    <div class="modal fade profile-n-modal" id="add_edit_employee">
        <div class="modal-dialog modal-lg d-flex justify-content-center align-items-center">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="btn close-btn" data-dismiss="modal">X</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body" id="emp_modal">

                </div>
                <!-- Modal footer -->
                <div class="modal-footer modal-footer-css d-flex justify-content-between">
                    <div class="emp_del_btn"></div>
                    <div class="d-flex justify-content-end"><button type="submit" class="btn s-btn px-4 show_loader" id="emp_btnSave" name="emp_btnSave">Save</button></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade profile-n-modal" id="add_edit_education">
        <div class="modal-dialog modal-lg d-flex justify-content-center align-items-center">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="btn close-btn" data-dismiss="modal">X</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body" id="edu_modal">

                </div>
                <!-- Modal footer -->
                <div class="modal-footer modal-footer-css d-flex justify-content-between">
                    <div class="edu_del_btn"></div>
                    <div class="d-flex justify-content-end"><button type="submit" class="btn s-btn show_loader" id="edu_btnSave" name="edu_btnSave">Save</button></div>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="modal fade profile-n-modal" id="add_edit_about_me">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-#- Modal Header -#->
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title">About Me</h4>
                    <button type="button" class="btn close-btn" data-dismiss="modal">X</button>
                </div>
                <!-#- Modal body -#->
                <div class="modal-body" id="aboutme_modal" style="height: unset;">
                    <div class="col-12 mt-3">
                        <div class="input-box">
                            <label class="fs-13 text-black lh-20 fw-medium">About Me <span style="color:red"> * </span></label>
                            <div class="form-group">
                                <textarea  class="form-control form--control" rows="8" maxlength="500" name="aboutme"><?php // echo @$data->aboutme; ?> </textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <!-#- Modal footer -#->
                <div class="modal-footer modal-footer-css d-flex justify-content-between">
                    <div class="emp_del_btn"></div>
                    <div class="d-flex justify-content-end"><button type="submit" class="btn s-btn px-4 show_loader" id="about_btnSave" name="about_btnSave">Save</button></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade profile-n-modal" id="add_edit_funfact">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-#- Modal Header -#->
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title">Fun Fact</h4>
                    <button type="button" class="btn close-btn" data-dismiss="modal">X</button>
                </div>
                <!-#- Modal body -#->
                <div class="modal-body" id="aboutme_modal" style="height: unset;">
                    <div class="col-12 mt-3">
                        <div class="input-box">
                            <label class="fs-13 text-black lh-20 fw-medium">Fun Fact(Great for ice-breakers) <!-#- <span style="color:red"> * </span> -#-></label>
                            <div class="form-group">
                            <textarea class="form-control" rows="4" maxlength="500" name="funfact" ><?php echo $fun_fact; ?> </textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <!-#- Modal footer -#->
                <div class="modal-footer modal-footer-css d-flex justify-content-between">
                    <div class="emp_del_btn"></div>
                    <div class="d-flex justify-content-end"><button type="submit" class="btn s-btn px-4 show_loader" id="funfact_btnSave" name="funfact_btnSave">Save</button></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade profile-n-modal" id="add_edit_hobbies">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-#- Modal Header -#->
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title">Hobbies</h4>
                    <button type="button" class="btn close-btn" data-dismiss="modal">X</button>
                </div>
                <!-#- Modal body -#->
                <div class="modal-body" id="aboutme_modal" style="height: unset;">
                    <div class="col-12 mt-3">
                        <div class="input-box">
                            <label class="fs-13 text-black lh-20 fw-medium">Hobbies<!-#- <span style="color:red"> * </span> -#-></label>
                            <div class="form-group">
                            <select name="hobbies[]" id="hobbies" multiple="hobbies"  class="select2 form-control hobbies-field">
                                <?php // foreach(PROFESSIONAL_HOBBIES as $hkey=>$hobby){ ?>
                                    <option value="<?php // echo $hkey; ?>" <?php // echo (is_array($hobbies) && in_array($hkey,$hobbies)) ? 'selected' : ''; ?>><?php // echo $hobby; ?></option>
                                <?php // } ?>
                            </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-#- Modal footer -#->
                <div class="modal-footer modal-footer-css d-flex justify-content-between">
                    <div class="emp_del_btn"></div>
                    <div class="d-flex justify-content-end"><button type="submit" class="btn s-btn px-4 show_loader" id="hobbies_btnSave" name="hobbies_btnSave">Save</button></div>
                </div>
            </div>
        </div>
    </div> -->
</form>


<script>
var indx_db_settings = <?php echo $indx_db_settings; ?>;
let taoh_user_keywords = JSON.parse('<?php echo defined('TAOH_USER_KEYWORDS') ? TAOH_USER_KEYWORDS : '{}'; ?>');
var userType = "<?php echo isset($data->type) ? $data->type : ''; ?>";
var savedTags = { ...<?php echo json_encode(@$data->tags);?> }
var loginType = "<?php echo $login_type; ?>";
let isFormModified = false;

$('document').ready(function() {

    $( ".hobbies-field" ).select2({width: '100%'});

    $("#fname").keyup(function () {
        $('#chat_name').val($(this).val());
    });


        $('#setting_form, #step2_form, #step5_form').on('change input', 'input, textarea', function() {
            isFormModified = true;
        });
        $('#myTab a').on('click', function(e) {
            if(!$(this).hasClass('your_space')){
                e.preventDefault();
                e.stopImmediatePropagation();
                if (isFormModified) {
                    if (confirm('You have unsaved changes. Are you sure you want to leave without saving?')) {
                        isFormModified = false;  // Reset the flag after confirming
                        $(this).tab('show');
                    }
                } else {
                    $(this).tab('show');
                }
            }
        });

    /* var hash = window.location.hash;
    if (hash) {
        var tabLink = $('a[href="' + hash + '"]');
        if (tabLink.length) {
            tabLink.tab('show');
        }
    }

    // Update hash when tab is clicked
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash;
    }); */

$("#step2_form").validate({
        rules: {
            skill: {required:true},
        },
        messages: {
            skill: {required : "Skill is required"},
        },
        submitHandler: function (form) {
              if(indx_db_settings==1){
                  step2_submit(form);
               }else{
                form.submit();
               }
               return false;
        }

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
        console.log('condition',condition);

        if(condition) {
            // alert('if condition true');
            // Scroll to the element with the ID 'myElement'
            document.getElementById('move_avatar').scrollIntoView({ behavior: 'smooth', block: 'center' });

            $('#avatar-error').html('Profile Picture is required');
            $('#avatar-error').show();
            return false;
        }
        else {
            $('#avatar-error').html('');
            $('#avatar-error').hide();
            $("#save_changes").html('<img style="width:28px;" width="20" src="<?php echo TAOH_LOADER_GIF; ?>"> While saving in progress, you can continue to explore other pages.');
            $("#save_changes").attr('disabled', true);

           if(indx_db_settings==1){
              indxform_submit(form);
           }else{
              form.submit();
           }
           <?php $tabname = '#form-block-2';
                if( !isset( $data->skill ) || $data->skill == ''){
                    $tabname = '#form-block-2';
                }else if(!is_array($emp_list) || count($emp_list) == 0 ){
                    $tabname = '#form-block-3';
                }else if(!is_array($edu_list) || count($edu_list) == 0 ){
                    $tabname = '#form-block-4';
                }else if($about_me == '' || $fun_fact == ''){
                    $tabname = '#form-block-5';
                }
           ?>
        //    window.location.replace('<?php // echo TAOH_SITE_URL_ROOT . '/settings'.$tabname; ?>');
           return false;
        }
    }
});
function createSession(formData,e){
    e.preventDefault();


    var roleSelect ={};var companySelect ={};


    $("#roleSelect option:selected").map(function () {
        id = $(this).val();
        roleSelect[id] = $(this).attr("data-slug")+":>"+$(this).text();
    });
    $("#companySelect option:selected").map(function () {
        id = $(this).val();
        companySelect[id] = $(this).attr("data-slug")+":>"+$(this).text();
    });
    $.ajax({
        url: '<?php echo taoh_site_ajax_url(); ?>',
        type: 'POST',
        data: formData,
        success: function(response) {
            console.log(response);
        },
    });
}

function step2_submit(form){

    //alert(userType);
    let user_keywords_field_names = Object.keys(taoh_user_keywords);
    taoh_set_warning_message('Saving changes are in progress!!!');
    var formData = new FormData(form);
    formData.append('taoh_action','taoh_indb_session_step2');

    console.log(formData)

    // Convert FormData to object
    var settingsdata_2 = {};
    settingsdata_2.keywords = {};
    settingsdata_2.tags = []
    formData.forEach(function (value, key) {
        if (user_keywords_field_names.includes(key)) {
            settingsdata_2.keywords[key] = value;
        }
        if (key == "tags[]"){
            settingsdata_2.tags.push(value);
        }
        else if (key == "skill:skill[]" ) {
            if (!settingsdata_2.hasOwnProperty(key)) {
                settingsdata_2[key] = {};
            }
            settingsdata_2[key] = value;
        } else {
            settingsdata_2[key] = value;
        }
    });

    /*console.log('----aaaa---settingsdata_2---------',settingsdata_2);

    let setting_time = new Date();
    setting_time = setting_time.setMinutes(setting_time.getMinutes() + 5);

    IntaoDB.setItem(APIStore,{ taoh_api: index_name,value:'2' });
    IntaoDB.setItem(dataStore, { taoh_data:index_name,values : settingsdata_2 });
    IntaoDB.setItem(TTLStore, { taoh_ttl: index_name,time:setting_time });*/
    saveStep2Data(settingsdata_2,form);

}
function saveStep2Data(settingsdata_2,form){
    var skill_text={};
    // var formData = new FormData(form);
    var skill_selected = $("#skillSelect option:selected").map(function () {
            return $(this).val();
        }).get();
        $.each(skill_selected, function(index, value) {
            skill_text[index] = value;
        });
        settingsdata_2['skill:skill'] = skill_text;


            var data = {
                'step2': 1,

                'taoh_ptoken' : $('input[name="taoh_ptoken"]').val(),
            };
            jQuery.post("<?php echo TAOH_ACTION_URL . '/settings'; ?>", settingsdata_2, function(response) {
                res = response;
                if(res){
                    taoh_set_success_message('settings data has been saved!!!');
                    window.location.reload(true);
                    <?php $tabname = '#form-block-3';
                            if(!is_array($emp_list) || count($emp_list) == 0 ){
                                $tabname = '#form-block-3';
                            }else if(!is_array($edu_list) || count($edu_list) == 0 ){
                                $tabname = '#form-block-4';
                            }else if($about_me == '' || $fun_fact == ''){
                                $tabname = '#form-block-5';
                            }
                    ?>
                    // window.location.replace('<?php // echo TAOH_SITE_URL_ROOT . '/settings'.$tabname; ?>');

                }
            }).fail(function() {

            });

}
function indxform_submit(form){
    taoh_set_warning_message('Saving changes are in progress!!!');
    var formData = new FormData(form);
    formData.append('taoh_action','taoh_indb_session');
    if($('input[name="tao_unsubscribe_emails"]').length > 0 && $('input[name="tao_unsubscribe_emails"]').is(':checked')){
        formData.append('tao_unsubscribe_emails','1');
    }else{
        formData.append('tao_unsubscribe_emails','0');
    }

    // Instead of directly appending fileToUpload.files[0], let's check if it exists and if it's a File object.
    var fileInput = document.getElementById('fileToUpload'); // Assuming your file input has id 'fileToUpload'
    if (fileInput && fileInput.files && fileInput.files.length > 0 && fileInput.files[0] instanceof File) {
        formData.append('fileToUpload', fileInput.files[0]);
    }
    // user data
    // Convert FormData to object
    var settingsdata = {};
    settingsdata.keywords = {};
    formData.forEach(function (value, key) {

        if (key == "title:title[]" || key == "company:company[]") {
            if (!settingsdata.hasOwnProperty(key)) {
                settingsdata[key] = {};
            }
            settingsdata[key] = value;
        } else if (key == "fileToUpload" ) {
            if($("#fileToUpload").val()!='' && $("#fileToUpload").val() != undefined){
                settingsdata[key] = $("#fileToUpload").val().split('\\').pop();
            }
        } else {
            settingsdata[key] = value;
        }
    });
    // console.log('-------settingsdata---------',settingsdata);

    let setting_time = new Date();
    setting_time = setting_time.setMinutes(setting_time.getMinutes() + 5);

    IntaoDB.setItem(APIStore,{ taoh_api: index_name,value:'1' });
    IntaoDB.setItem(dataStore, { taoh_data:index_name,values : settingsdata });
    IntaoDB.setItem(TTLStore, { taoh_ttl: index_name,time:setting_time });
    $('#global_settings').val(1);
    // console.log('-------loginType---------',loginType);
    // console.log('-------userType---------',userType);

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

    document.addEventListener("DOMContentLoaded", function() {
        const checkboxes = document.querySelectorAll('.tags input');
        console.log(checkboxes);
        const selectedTagsContainer = document.getElementById('selected-tags');
        const selectionStatus = document.getElementById('selection-status');
        let selectedTags = [];

        loadTags(savedTags);
        //updateStatus();

        // Update the selected tags
        function updateSelectedTags() {

            selectedTagsContainer.innerHTML = '';
            // $("#myTabForm").find(".tab-pane").addClass("fade");
            // $("#myTabForm").find(".tab-pane").removeClass("active show");
            selectedTags.forEach(tag => {
                const span = document.createElement('span');
                span.textContent = tag;
                const removeIcon = document.createElement('span');
                removeIcon.textContent = '✖';
                removeIcon.classList.add('remove-tag');
                removeIcon.addEventListener('click', () => {
                    selectedTags = selectedTags.filter(t => t !== tag);
                    // Uncheck the corresponding checkbox
                    const checkbox = document.querySelector(`input[value="${tag}"]`);
                    checkbox.checked = false;
                    // Remove the active background from the label
                    const label = document.querySelector(`label[for="${checkbox.id}"]`);
                    label.style.backgroundColor = '#000000'; // Reset to default background

                    $("#frm_"+tag.replaceAll(" ","")).find('input, textarea, select').val('');
                    $("#frm_"+tag.replaceAll(" ","")).addClass('fade');
                    $("#frm_"+tag.replaceAll(" ","")).removeClass('active show');
                    updateSelectedTags();
                    updateStatus();
                });
                span.appendChild(removeIcon);
                selectedTagsContainer.appendChild(span);

                // $("#"+tag.replaceAll(" ","")).removeClass('fade');
                // $("#"+tag.replaceAll(" ","")).addClass('active show');

                // $('#selected-tags span').on('click', function() {
                $('#selected-tags').on('click', 'span', function (e) {
                    // if($(this).hasClass('remove-tag')){
                    if ($(e.target).hasClass('remove-tag')) {
                        return;
                    }
                    $("#myTabForm").find(".tab-pane").addClass('fade');
                    $("#myTabForm").find(".tab-pane").removeClass('active show');
                    tag = $(this).contents().first().text();
                    $("#frm_"+tag.replaceAll(" ","")).removeClass('fade');
                    $("#frm_"+tag.replaceAll(" ","")).addClass('active show');
                });
            });
        }

        function loadTags(savedTags){
            // console.log(savedTags)
            // console.log(checkboxes)
            $(".tags input").each(function() {
                // console.log('-----------',$(this).val());
                // console.log('-----------',$(this).attr('id'));
                // alert('test tags');

                    const tag = $(this).val();
                    const label = document.querySelector(`label[for="${$(this).val()}"]`);

                    if ( $(this).attr('checked') && !selectedTags.includes(tag)) {
                        selectedTags.push(tag);
                        label.style.backgroundColor = '#2557A7'; // Active background
                    } else if (!$(this).attr('checked') && selectedTags.includes(tag)) {
                        selectedTags = selectedTags.filter(t => t !== tag);
                        label.style.backgroundColor = '#000000'; // Reset background
                    }
                    updateSelectedTags();
                    updateStatus();
               // });
            });
            console.log('---selectedTags--------',selectedTags);
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                console.log('load tag');
                console.log(selectedTags.length);
                if (selectedTags.length >= 5) return false;
                console.log('after return');
                const tag = this.value;
                const label = document.querySelector(`label[for="${this.value}"]`);

                if (this.checked && !selectedTags.includes(tag)) {
                    selectedTags.push(tag);
                    $("#myTabForm").find(".tab-pane").addClass('fade');
                    $("#myTabForm").find(".tab-pane").removeClass('active show');
                    $("#frm_"+tag.replaceAll(" ","")).removeClass('fade');
                    $("#frm_"+tag.replaceAll(" ","")).addClass('active show');
                    label.style.backgroundColor = '#2557A7'; // Active background
                } else if (!this.checked && selectedTags.includes(tag)) {
                    selectedTags = selectedTags.filter(t => t !== tag);
                    label.style.backgroundColor = '#000000'; // Reset background
                }
                updateSelectedTags();
                updateStatus();
            });
        });

        // Update the selection status message
        function updateStatus() {
            if (selectedTags.length < 5) {
                selectionStatus.innerHTML = `You have selected ${selectedTags.length} out of 5 Profile Tags! Please select ${5 - selectedTags.length} more or you can proceed from <a href="#" style="text-decoration: underline;">here</a>.`;
            } else {
                selectionStatus.innerHTML = `You have selected 5 out of 5 Profile Tags! Please proceed from <a href="#" style="text-decoration: underline;">here</a>`;
            }
        }


    });


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
    if (this.files && this.files[0]) {
        var av_file = this.files[0].name;
        $('.av_file').html(av_file);
    }
    var avatar_formData = new FormData(document.getElementById("setting_form"));
    avatar_formData.append('time', Date.now());
    var avatar_file = this.files[0];
    if (avatar_file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('.avatar-container').show();
            $('.avatar_settings').html('<img src="' + e.target.result + '">');
            $(".profile-image").hide();
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
            if($('input[name="avatar"]').val() != ''){
                $('input[name="avatar"]').val('default');
            }
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
    $(".profile-image").show();
    $('.avatar_image').val('');
    $('#custom_avatar').val('');
    $('#custom_avatar').change();
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
});  */

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

function copyProfileType(type){
    userType = type;
}

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

/*<!-- user keywords script -->*/

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

        $(document).on('click', '.add_edit_edu', function () {
            var data_edu = $(this).attr("data-education");
            var edu_add_edit = $(this).attr("data-add-edit");
            var edu_delete = $(this).attr("data-edu-delete");
            var edu_edt_delete = $(this).attr("data-edu-edit-delete");
            $('#add_edit_education .modal-title').html(edu_add_edit + ' Education');
            $('#add_edit_education .modal-body').html('');
            $('#add_edit_education .edu_del_btn').html('');
            $('#add_edit_education').modal('show');
            $('#add_edit_education .modal-body').html('<img class="loader" style="width:10% !important; display: block; background: none; top: auto; height:auto;" src="<?php echo TAOH_LOADER_GIF; ?>" />');
            var data = {
                'taoh_action': 'add_edit_education',
                'id': data_edu,
                'edu_edit_del_id': edu_edt_delete,
                'add_or_edit': edu_add_edit,
                'post_data': '<?php echo json_encode($edu_list); ?>',
            };
            jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function (response) {
                res = response.split('~');
                console.log(res[1]);
                $('#add_edit_education .modal-body').html(res[0]);
                $('#add_edit_education .edu_del_btn').html(res[1]);
            }).fail(function () {
                console.log("Network issue!");

            })
        });

        $(document).on('click', '.add_edit_emp', function () {
            var data_emp = $(this).attr("data-employee");
            var emp_add_edit = $(this).attr("data-add-edit");
            var emp_delete = $(this).attr("data-emp-delete");
            var emp_edt_delete = $(this).attr("data-emp-edit-delete");
            $('#add_edit_employee .modal-title').html(emp_add_edit + ' Experience');
            $('#add_edit_employee .modal-body').html('');
            $('#add_edit_employee .emp_del_btn').html('');
            $('#add_edit_employee').modal('show');
            $('#add_edit_employee .modal-body').html('<img class="loader" style="width:10% !important; display: block; background: none; top: auto; height:auto;" src="<?php echo TAOH_LOADER_GIF; ?>" />');
            var data = {
                'taoh_action': 'add_edit_employee',
                'id': data_emp,
                'emp_edit_del_id': emp_edt_delete,
                'add_or_edit': emp_add_edit,
                'post_data': '<?php echo json_encode($emp_list); ?>',
            };
            jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function (response) {
                res = response.split('~');
                console.log(res[1]);
                $('#add_edit_employee .modal-body').html(res[0]);
                $('#add_edit_employee .emp_del_btn').html(res[1]);
            }).fail(function () {
                console.log("Network issue!");

            })
        });

        $(document).on('click', '.add_edit_aboutme', function () {
            $("#add_edit_about_me").modal('show');
        });

        $(document).on('click', '.add_edit_funfact', function () {
            $("#add_edit_funfact").modal('show');
        });

        $(document).on('click', '.add_edit_hobbies', function () {
            $("#add_edit_hobbies").modal('show');
        });

        /* $(document).on('change', '#mylink', function () {
            loader(true, $("#settings_loaderArea"));
            var data = {
                'taoh_action': 'old_profile',
                'taoh_ptoken' : $("#taoh_ptoken").val(),
                'mylink': $(this).val(),
            };
            jQuery.post("<?php echo TAOH_ACTION_URL.'/settings' ?>", data, function (response) {
                $.alert('Settings updated successfully');
                location.reload();
            }).fail(function () {
                console.log("Network issue!");

            })
        }); */
        $(document).on('change', '#tao-unsubscribe', function () {
            loader(true, $("#settings_loaderArea"));
            var data = {
                'taoh_action': 'old_profile',
                'taoh_ptoken' : $("#taoh_ptoken").val(),
                'tao_unsubscribe_emails': $(this).is(":checked") ? 1 : 0,
            };
            jQuery.post("<?php echo TAOH_ACTION_URL.'/settings' ?>", data, function (response) {
                $.alert('Settings updated successfully');
                // location.reload();
            }).fail(function () {
                console.log("Network issue!");

            })
        });


        $(document).on('change', "[name='unlist_me_dir']", function () {
            loader(true, $("#settings_loaderArea"));
            var data = {
                'taoh_action': 'old_profile',
                'taoh_ptoken' : $("#taoh_ptoken").val(),
                'unlist_me_dir': $(this).is(":checked") ? 1 : 0,
            };
            jQuery.post("<?php echo TAOH_ACTION_URL.'/settings' ?>", data, function (response) {
                $.alert('Settings updated successfully');
                location.reload();
            }).fail(function () {
                console.log("Network issue!");

            })
        });


        $(document).on('change', "#emp_year_starts", function (e) {
            var emp_startYear = $(this).children(':selected').val();
            $('#emp_hidden_end').children().appendTo('#emp_year_ends');

            $('#emp_year_ends option').each(function () {
                if ($(this).val() < emp_startYear) $(this).appendTo('#emp_hidden_end');
            })
            var emp_options = $('#emp_year_ends option').sort(function (emp_a, emp_b) {
                return (emp_a.value > emp_b.value) ? -1 : 1
            });
            emp_options.appendTo($('#emp_year_ends'));
            $('#emp_year_ends option:selected').removeAttr('selected');
            $('#emp_year_ends option:first-child').attr('selected', 'selected')
        });

        $(document).on('change', "#edu_year_starts", function (e) {
            var startYear = $(this).children(':selected').val();
            $('#edu_hidden_end').children().appendTo('#edu_year_ends');
            $('#edu_year_ends option').each(function () {
                if ($(this).val() < startYear) $(this).appendTo('#edu_hidden_end');
            })
            var options = $('#edu_year_ends option').sort(function (a, b) {
                return (a.value > b.value) ? -1 : 1
            });
            options.appendTo($('#edu_year_ends'));
            $('#edu_year_ends option:selected').removeAttr('selected');
            $('#edu_year_ends option:first-child').attr('selected', 'selected')
        });
    });
    /*user keywords script -->*/

    function check_still_working(exp_id){
        const endDateSelect = document.getElementById('emp_end_month_'+exp_id);
        const endYearSelect = document.getElementById('emp_year_end_'+exp_id);
        if ($('#still_working_'+exp_id).is(":checked")){
            $('.emp_end_month_block_'+exp_id).hide();
            endDateSelect.removeAttribute('required');
            endYearSelect.removeAttribute('required');
        }else{
            $('.emp_end_month_block_'+exp_id).show();
            endDateSelect.setAttribute('required', 'true');
            endYearSelect.setAttribute('required', 'true');
        }
        currentCheckbox.prop('checked', !currentCheckbox.prop('checked'));
    }

    /* function showLoading(event) {
            event.preventDefault();
            // Get the submit button that was clicked
            const submitButton = event.submitter;
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

            // Create a hidden input to hold the button name and value
            const hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = submitButton.name;
            document.getElementById("post_form").appendChild(hiddenInput);

            // Submit the form
            setTimeout(function () {
                document.getElementById("post_form").submit();
            }, 1000); // Delay submission for demonstration purposes
        } */
</script>
<!-- for mobile screen -->
<script>
    // mobile screen next and prev tabs
    document.addEventListener("DOMContentLoaded", function () {
        const tabNavButtons = document.querySelectorAll('.tab-nav-btn');

        tabNavButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                // console.log('clicked prenext :'+isFormModified);
                let nxttabflag = 0;
            if (isFormModified) {
                if (confirm('You have unsaved changes. Are you sure you want to leave without saving?')) {
                    isFormModified = false;  // Reset the flag after confirming
                    $(this).tab('show');
                    nxttabflag = 1;
                }
            } else {
                nxttabflag = 1;
            }
            if(nxttabflag == 1){
                const direction = this.getAttribute('data-direction');
                const tabLinks = Array.from(document.querySelectorAll('.p-main-tabs .nav-link'));
                const currentTab = tabLinks.find(tab => tab.classList.contains('active'));

                if (!currentTab) return;

                let currentIndex = tabLinks.indexOf(currentTab);
                let newIndex = direction === 'next' ? currentIndex + 1 : currentIndex - 1;

                if (newIndex >= 0 && newIndex < tabLinks.length) {
                    const nextTab = tabLinks[newIndex];
                    $(nextTab).tab('show');

                    // Scroll to the top of the content pane
                    const targetSelector = nextTab.getAttribute('href');
                    const targetPane = document.querySelector(targetSelector);
                    if (targetPane) {
                        setTimeout(() => {
                            targetPane.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 100);
                    }
                }
            }
            });
        });
    });
    // for mobile screen, scroll to top
    document.addEventListener('DOMContentLoaded', function () {

        // Select all tab navigation buttons for mobile
        document.querySelectorAll('.d-xl-none .tab-nav-btn').forEach(function (tabLink) {
            tabLink.addEventListener('click', function () {
                // Timeout ensures the tab content switches before scroll
                setTimeout(function () {
                    const activeTab = document.querySelector('.tab-pane.active');
                    if (activeTab) {
                        const tabTop = activeTab.getBoundingClientRect().top + window.pageYOffset;
                        const offset = 150;

                        window.scrollTo({
                            top: tabTop - offset,
                            behavior: 'smooth'
                        });
                    } else {
                        // Fallback scroll to top with offset
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                }, 100); // wait for tab to activate
            });
        });
    });

</script>

<?php taoh_get_footer(); ?>