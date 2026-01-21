<?php
$user_is_logged_in = taoh_user_is_logged_in() ?? false;
if (!$user_is_logged_in) {
    header("Location: " . TAOH_SITE_URL_ROOT . '/login');
}

$parse_url_1 = taoh_parse_url(1);
$tab_index = max((int)$parse_url_1 - 1, 0);

taoh_add_var_to_url('noca', TAOH_MY_NOW_CODE);
$taoh_home_url = (defined('TAOH_PAGE_URL') && TAOH_PAGE_URL) ? TAOH_PAGE_URL : TAOH_SITE_URL_ROOT;

$taoh_user_keywords = defined('TAOH_USER_KEYWORDS') ? getJsonDecodedData(TAOH_USER_KEYWORDS) : [];
$show_name_slug_information = !empty($taoh_user_keywords);

$tag_category = TAOH_TAG_CATEGORY;
$tag_category_form = TAOH_TAG_CATEGORY_FORM;

taoh_get_header();

if(!isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'])){
    taoh_redirect( TAOH_SITE_URL_ROOT . '/logout');
    exit;
}

$data = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];

$ptoken = $data->ptoken ?? '';
$fname = $lname = $chat_name = '';
if (($data->profile_complete ?? '') == 1 || $data->created_via === 'social' || $data->social_id != '') {
    $fname = $data->fname ?? '';
    $lname = $data->lname ?? '';
    $chat_name = $data->chat_name ?? '';
}
$email = $data->email ?? '';
$profile_type = strtolower($data->type ?? '');
$coordinates = $data->coordinates ?? '';
$location = $data->full_location ?? '';
$country_code = $data->country_code ?? '';
$country_name = $data->country_name ?? '';
$unlist_me_dir = ($data->unlist_me_dir ?? '') === 'yes' ? 'yes' : 'no';

if (empty($country_code) && !empty($location)) {
    $full_location_expl = explode(', ', $location);
    $location_country_code = strtoupper(trim(array_pop($full_location_expl)));

    if ($location_country_code) {
        $url = TAOH_OPS_PREFIX . '/mapn/?op=country&code=' . urlencode($location_country_code);
        $country_json = taoh_url_get_content($url);
        $country_arr = json_decode($country_json, true);

        if (!empty($country_arr['success']) && filter_var($country_arr['success'], FILTER_VALIDATE_BOOLEAN)) {
            $country_code = $location_country_code;
            $country_name = $country_arr['output'] ?? '';
        }
    }
}

$avatar_image = !empty($data->avatar_image)
    ? $data->avatar_image
    : ((isset($data->avatar) && $data->avatar != 'default')
        ? TAOH_OPS_PREFIX . '/avatar/PNG/128/' . $data->avatar . '.png'
        : TAOH_OPS_PREFIX . '/avatar/PNG/128/avatar_def.png');

/* Get User Info */
$return = taoh_get_user_info($ptoken,'full',1);
$pfdata = json_decode($return, true);

$profile_user_data = $pfdata['output']['user'] ?? [];

$raw_profile_stage = $profile_user_data['profile_stage'] ?? ($profile_user_data['profile_complete'] ?? 0);
$profile_stage = max(0, is_numeric($raw_profile_stage) ? (int)$raw_profile_stage : 0);

$about_me = implode(' ', array_filter(explode(' ', (string)($profile_user_data['aboutme'] ?? ''))));
$fun_fact = implode(' ', array_filter(explode(' ', (string)($profile_user_data['funfact'] ?? ''))));

$hobbies_raw = (string)($profile_user_data['hobbies'] ?? '');
$hobbies = json_decode(implode(' ', array_filter(explode(' ', $hobbies_raw))), true);
$hobbies = is_array($hobbies) ? $hobbies : [];

if (isset($profile_user_data['education']) && is_array($profile_user_data['education'])) {
    $edu_encode = json_encode($profile_user_data['education']);
    $edu_list = json_decode($edu_encode, true);
    $edu_last_key = (int)array_key_last($edu_list);
    $edu_tot_count = $edu_last_key + 1;
} else {
    $edu_tot_count = 0;
    $edu_last_key = 0;
    $edu_list = '';
}
if (isset($profile_user_data['employee']) && is_array($profile_user_data['employee'])) {
    $emp_encode = json_encode($profile_user_data['employee']);
    $emp_list = json_decode($emp_encode, true);
    $emp_last_key = (int)array_key_last($emp_list);
    $emp_tot_count = $emp_last_key + 1;
} else {
    $emp_tot_count = 0;
    $emp_last_key = 0;
    $emp_list = '';
}

// Safe get function for tags nested objects
function safe_get($object, $field1, $field2) {
    if (!(isset($object->tags_data) && isset($object->tags_data->$field1))) {
        return '';
    }

    $tags_data = json_decode($object->tags_data->$field1, true);

    return $tags_data[$field2] ?? '';
}

function is_form_completed($fields, $data): int
{
    foreach ($fields as $field) {
        if (empty($data->$field ?? null)) {
            return 0;
        }
    }
    return 1;
}

$profile_form_2_completed = is_form_completed(
    ['chat_name', 'company', 'title', 'skill'],
    $data
);

$profile_form_5_completed = is_form_completed(
    ['aboutme', 'funfact', 'hobbies', 'mylink'],
    $data
);

switch (true) {
    case empty($data->profile_complete ?? ''):
        $unfinished_tab_index = 1;
        break;
    case empty($profile_form_2_completed):
        $unfinished_tab_index = 2;
        break;
    case empty($data->tags_data ?? ''):
        $unfinished_tab_index = 3;
        break;
    case empty($data->employee ?? ''):
        $unfinished_tab_index = 4;
        break;
    case empty($data->education ?? ''):
        $unfinished_tab_index = 5;
        break;
    case empty($profile_form_5_completed):
        $unfinished_tab_index = 6;
        break;
    default:
        $unfinished_tab_index = null;
        break;
}

$un_completed_tab_exist = !empty($unfinished_tab_index);

// Set tab_index for auto select unfinished profile tab
if (empty($tab_index) && !empty($unfinished_tab_index)) {
    $tab_index = max($unfinished_tab_index - 1, 0);
}

$save_button_text = 'Save';
if ($un_completed_tab_exist) {
    $save_button_text = 'Save and continue';
}

?>

<style>
    @media (max-width: 768px) {
        .sidebar-menu {
            display: none;
        }

        .sidebar-menu.show {
            display: block;
            position: relative !important;
            margin-bottom: 3rem;
        }
    }

    #main-tabContent {
        padding: 1rem;
        border: 1px solid #d3d3d3;
        border-radius: 0.25rem;
        min-height: 200px;
    }

    .submenu-list .nav-link {
        padding-left: 30px;
    }

    #myTabContent .tags input:checked + label {
        background-color: #2557A7 !important;
    }

    #sidebarMenu {
        position: sticky;
        top: 20px;
        height: 100%;
        width: 100%;
        /*max-width: 327px;*/
        /*min-width: 327px;*/
    }

    #sidebarMenu .fa {
        font-size: 14px;
    }

    .fa-level-right:before {
        content: "\21B3";
    }

    #sidebarMenu .nav-link {
        cursor: pointer;
        border: 1px solid #D3D3D3;
        min-height: 51px;
        display: flex;
        align-items: center;
        border-radius: 0;
        font-size: 16px;
        font-weight: 500;
        color: #000000;
    }

    #sidebarMenu .main-nav-title {
        font-size: 17px;
        color: #ffffff;
        font-weight: 500;
        line-height: 1.149;
        border: 1px solid #2557a7;
        min-height: 51px;
        display: flex;
        align-items: center;
        border-radius: 0;
        background: #2557a7;
    }

    #sidebarMenu .main-nav-title .heading {
        font-size: 17px;
        color: #ffffff;
        font-weight: 500;
    }

    #sidebarMenu .p-nav-title {
        font-size: 17px;
        color: #2557A7;
        font-weight: 500;
        line-height: 1.149;
        border: 1px solid #D3D3D3;
        min-height: 51px;
        display: flex;
        align-items: center;
        border-radius: 0;
        background: #f8f8f8;
    }

    #sidebarMenu .nav-link.active .svg-color {
        fill: #2557A7;
    }

    #sidebarMenu .nav-link .svg-color {
        fill: #000000;
    }

    .nav-link.active {
        color: #495057
    }

    .toggle-icon {
        transition: transform 0.3s ease;
    }

    .nav-heading.collapsed .toggle-icon {
        transform: rotate(180deg);
    }

    #toggleMenu {
        font-weight: bold;
        text-align: left;
        font-size: 22px;
    }

    #sidebarMenu .nav-pills .nav-link.active {
        color: #2557A7 !important;
        font-size: 16px;
        font-weight: 500;
        background: transparent;
    }

    #sidebarMenu .nav-link .fa-check-circle {
        color: #a7a7a7;
    }

    #sidebarMenu .nav-link .fa-check-circle.completed {
        color: #379D0B !important;
    }


    .modal-body {
        height: 70vh;
        overflow-y: auto;
    }
</style>


<div class="profile-n bg-white pt-5">
    <!--<div class="float-right pr-5">
        <a class="nav-link d-flex flex-column text-center red text-white" aria-current="page" style="width:20px;" href="<?php /*echo $taoh_home_url; */?>">X</a>
    </div>-->
    <div class="container d-flex flex-column flex-lg-row" style="gap: 20px;">
        <div class="profile-right">
            <div class="p-banner mb-5">
                <?php if(@$data->avatar_image != '' || !empty($avatar_image)){ ?>
                <div class="p-banner-img-con">
                    <div class="p-banner-bg" style="background-image: url(<?php echo $avatar_image; ?>)"></div>
                    <div class="glass-overlay"></div>
                    <img class="main-img" src="<?php echo $avatar_image; ?>" alt="">
                </div>
                <?php } ?>
                <div class="d-flex flex-wrap mt-4 pl-4 pl-lg-5" style="gap: 24px;">
                    <div class="p-img-right">
                        <img src="<?php echo $avatar_image;?>" alt="">
                    </div>
                    <div>
                        <div class="d-flex align-items-center flex-wrap mb-4" style="gap: 12px;">
                            <h6 class="p-banner-name mr-lg-3"><?php echo ucfirst($data?->fname ?? '');?></h6>
                            <?php if(($data?->profile_complete ?? '') == 1) { ?>
                            <p class="p-batch"><?php echo  ucfirst($data?->type ?? 'Professional'); ?></p>
                            <?php } ?>
                        </div>
                        <?php
                        if(!empty($profile_form_2_completed)) {
                            ?>
                            <div class="your-nje flex-wrap" style="gap: 12px;">
                                <div class="your-nje mb-0 gap-1">
                                    <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7.54688 2H13.4531C13.6336 2 13.7812 2.15 13.7812 2.33333V4H7.21875V2.33333C7.21875 2.15 7.36641 2 7.54688 2ZM5.25 2.33333V4H2.625C1.17715 4 0 5.19583 0 6.66667V10.6667H7.875H13.125H21V6.66667C21 5.19583 19.8229 4 18.375 4H15.75V2.33333C15.75 1.04583 14.7205 0 13.4531 0H7.54688C6.27949 0 5.25 1.04583 5.25 2.33333ZM21 12H13.125V13.3333C13.125 14.0708 12.5385 14.6667 11.8125 14.6667H9.1875C8.46152 14.6667 7.875 14.0708 7.875 13.3333V12H0V17.3333C0 18.8042 1.17715 20 2.625 20H18.375C19.8229 20 21 18.8042 21 17.3333V12Z" fill="#2557A7"/>
                                    </svg>
                                    <span>
                                        <?php
                                        $company = get_explode_names(@$data->company);
                                        $company = $company[0] ?? '';

                                        $role = '';
                                        if (!empty($data->title)) {
                                            $roleArr = (array)$data->title;
                                            $titlePart = explode(':>', reset($roleArr));
                                            $role = $titlePart[1] ?? '';
                                        }

                                        $output = trim($role . ($role && $company ? ', ' : '') . $company);
                                        if ($output !== '') {
                                            echo $output;
                                        }
                                        ?>
                                    </span>
                                </div>

                                <div class="your-nje mb-0 gap-1">
                                    <svg width="21" height="20" viewBox="0 0 15 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.42578 19.5506C10.4297 17.0363 15 10.9424 15 7.51946C15 3.36809 11.6406 0 7.5 0C3.35938 0 0 3.36809 0 7.51946C0 10.9424 4.57031 17.0363 6.57422 19.5506C7.05469 20.1498 7.94531 20.1498 8.42578 19.5506ZM7.5 5.01297C8.16304 5.01297 8.79893 5.27705 9.26777 5.74711C9.73661 6.21716 10 6.8547 10 7.51946C10 8.18422 9.73661 8.82176 9.26777 9.29181C8.79893 9.76187 8.16304 10.0259 7.5 10.0259C6.83696 10.0259 6.20107 9.76187 5.73223 9.29181C5.26339 8.82176 5 8.18422 5 7.51946C5 6.8547 5.26339 6.21716 5.73223 5.74711C6.20107 5.27705 6.83696 5.01297 7.5 5.01297Z" fill="#2557A7"/>
                                    </svg>
                                    <span><?php echo @$data->full_location;?></span>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="container pb-5">
        <?php
        if ($profile_stage > 0 && $un_completed_tab_exist) {
            switch ($unfinished_tab_index) {
                case 2:
                    $current_profile_stage_name = 'basic settings';
                    break;
                case 3:
                    $current_profile_stage_name = 'general settings';
                    break;
                case 4:
                    $current_profile_stage_name = 'profile flags';
                    break;
                case 5:
                    $current_profile_stage_name = 'experience details';
                    break;
                case 6:
                    $current_profile_stage_name = 'education details';
                    break;
                case 7:
                    $current_profile_stage_name = 'about, hobbies & interests';
                    break;
                default:
                    $current_profile_stage_name = 'basic settings';
            }
            ?>
            <div class="col-12 all-set mb-4">
                <svg style="min-width: fit-content;" width="30" height="30" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5.57143 0C2.49844 0 0 2.49844 0 5.57143V33.4286C0 36.5016 2.49844 39 5.57143 39H33.4286C36.5016 39 39 36.5016 39 33.4286V5.57143C39 2.49844 36.5016 0 33.4286 0H5.57143ZM29.3371 15.4085L18.1942 26.5513C17.3759 27.3696 16.0527 27.3696 15.2431 26.5513L9.67165 20.9799C8.85335 20.1616 8.85335 18.8384 9.67165 18.0288C10.49 17.2192 11.8132 17.2105 12.6228 18.0288L16.7143 22.1203L26.3772 12.4487C27.1955 11.6304 28.5188 11.6304 29.3283 12.4487C30.1379 13.267 30.1467 14.5902 29.3283 15.3998L29.3371 15.4085Z" fill="#379D0B"/>
                </svg>
                Your <?= $current_profile_stage_name ?> are now complete. You can join the event or explore the site, and finish the rest of your setup at any time.
            </div>
            <?php
        }
        ?>

        <!-- Mobile Menu Toggle -->
        <div class="bg-light d-md-none py-2 border-bottom">
            <button class="btn btn-outline-primary w-100 text-start px-3 py-3" id="toggleMenu"><i class="fa fa-bars mr-3" aria-hidden="true"></i> Menu</button>
        </div>

        <div class="col-md-12 px-0 px-md-3">
            <div class="row">
                <!-- Sidebar Menu -->
                <div class="col-md-3 col-lg-3 sidebar-menu" id="sidebarMenu">
                    <ul class="nav flex-column nav-pills" id="main-tabs" role="tablist">
                        <li class="nav-heading"><h6 class="main-nav-title pl-3">Settings</h6></li>
                        <li class="nav-item">
                            <a class="nav-link active py-2" data-toggle="tab" href="#form-block-1" role="tab">
                                <i class="fa fa-gear mr-2" aria-hidden="true"></i> Basic <i class="fa fa-check-circle pl-2 <?= ($data->profile_complete ?? '') == 1 ? 'completed' : '' ?>" aria-hidden="true"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-2" data-toggle="tab" href="#form-block-2" role="tab">
                                <i class="fa fa-plus-square mr-2" aria-hidden="true"></i> General <i class="fa fa-check-circle pl-2 <?= !empty($profile_form_2_completed) ? 'completed' : '' ?>" aria-hidden="true"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-2" data-toggle="tab" href="#form-block-2-1" role="tab">
                                <i class="fa fa-flag mr-2" aria-hidden="true"></i> Profile Flags <i class="fa fa-check-circle pl-2 <?= !empty($data->tags_data ?? '')  ? 'completed' : '' ?>" aria-hidden="true"></i>
                            </a>

                            <div id="submenu2">
                                <ul class="nav flex-column submenu-list">
                                    <!-- Dynamic submenu items will go here -->
                                </ul>
                            </div>
                        </li>

                        <!-- Advanced Settings -->
                        <li class="nav-heading">
                            <div class="accordion" id="advancedAccordion">
                                <div class="nav-heading collapsed" data-toggle="collapse" data-target="#advancedCollapse" aria-expanded="false" aria-controls="advancedCollapse">
                                    <div class="main-nav-title d-flex justify-content-between align-items-center">
                                        <h6 class="heading pl-3">Advanced Settings</h6>
                                        <span class="pr-3"><i class="fa fa-chevron-up toggle-icon" id="advancedToggleIcon"></i></span>
                                    </div>
                                </div>

                                <div id="advancedCollapse" class="collapse" aria-labelledby="advancedHeading" data-parent="#advancedAccordion">
                                    <ul class="nav flex-column">
                                        <li class="nav-heading"><h6 class="p-nav-title pl-3">Professional</h6></li>
                                        <li class="nav-item">
                                            <a class="nav-link py-2" data-toggle="tab" href="#form-block-3" role="tab">
                                                <i class="fa fa-briefcase mr-2" aria-hidden="true"></i> Experience Details <i class="fa fa-check-circle pl-2 <?= !empty($data->employee) ? 'completed' : '' ?>" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link py-2" data-toggle="tab" href="#form-block-4" role="tab">
                                                <i class="fa fa-graduation-cap mr-2" aria-hidden="true"></i> Education Details <i class="fa fa-check-circle pl-2 <?= !empty($data->education) ? 'completed' : '' ?>" aria-hidden="true"></i>
                                            </a>
                                        </li>

                                        <li class="nav-heading"><h6 class="p-nav-title pl-3">Misc</h6></li>
                                        <li class="nav-item">
                                            <a class="nav-link py-2" data-toggle="tab" href="#form-block-5" role="tab">
                                                <i class="fa fa-user-plus mr-2" aria-hidden="true"></i> About, Hobbies & Interests <i class="fa fa-check-circle pl-2 <?= !empty($profile_form_5_completed) ? 'completed' : '' ?>" aria-hidden="true"></i>
                                            </a>
                                        </li>

                                        <li class="nav-heading"><h6 class="p-nav-title pl-3">Privacy / Security</h6></li>
                                        <li class="nav-item">
                                            <a class="nav-link py-2" data-toggle="tab" href="#form-block-6" role="tab">
                                                <i class="fa fa-shield mr-2" aria-hidden="true"></i> Privacy <i class="fa fa-check-circle pl-2 <?= ($data->profile_complete ?? '') == 1 ? 'completed' : '' ?>" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link py-2" data-toggle="tab" href="#form-block-7" role="tab">
                                                <i class="fa fa-unlock-alt mr-2" aria-hidden="true"></i> Security <i class="fa fa-check-circle pl-2 <?= ($data->profile_complete ?? '') == 1 ? 'completed' : '' ?>" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Tab Content -->
                <div class="col-md-9 col-lg-9">
                    <div class="tab-content px-0 px-lg-3" id="main-tabContent">
                        <div class="tab-pane fade show active" id="form-block-1">
                            <div class="container">
                                <form action="<?= TAOH_ACTION_URL . '/settings'; ?>" method="post" class="g-3" name="profile_form_1" id="profile_form_1" enctype="multipart/form-data" autocomplete="off">
                                    <h5 class="p-field-title py-3">Personal Information</h5>
                                    <hr class="mt-0" style="border-top: 1px solid #D3D3D3;">

                                    <div class="col-md-12 px-0 px-md-3">
                                        <label for="avatarSelect" class="form-label">My avatar <span class="text-danger">*</span></label>
                                        <div class="d-flex flex-wrap">
                                            <div class="profile-image" id="move_avatar" style="<?= !empty($data->avatar_image) ? 'display:none' : '' ?>">
                                                <?php echo avatar_select(@$data->avatar); ?>
                                            </div>
                                            <span class="text-danger" id="avatar-error"></span>
                                            <div class="avatar-container" style="<?= empty($data->avatar_image) ? 'display:none' : '' ?>">
                                                <div class="avatar_settings">
                                                    <?php
                                                    if (!empty($data->avatar_image)) {
                                                        echo '<img src="' . $data->avatar_image . '" alt="Avatar">';
                                                        echo '<div id="removeImage" class="delete-icon"></div>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if (TAOH_PROFILE_PICTURE_UPLOAD) { ?>
                                            <p class="text-center my-2" style="color: #000000; font-weight: 500; max-width: 110px;">OR</p>

                                            <div class="col-md-6 pr-0 pl-0 pb-3">
                                                <label for="profile_picture" class="form-label">Upload Profile Picture</label>
                                                <div class="form-group mb-3">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input cursor-pointer" name="profile_picture" id="profile_picture" accept=".jpg, .jpeg, .png">
                                                        <!-- file_my_validation -->
                                                        <label class="custom-file-label profile_picture_label" for="profile_picture">Choose file</label>
                                                    </div>
                                                </div>
                                                <input type="hidden" value="<?= !empty($data->avatar_image ?? '') ? $data->avatar_image : '' ?>" name="avatar_image">
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div class="col-md-12 px-0 px-md-3">
                                        <div class="row">
                                            <input type="hidden" name="taoh_ptoken" id="taoh_ptoken" value="<?= $data->ptoken ?? ''; ?>">
                                            <input type="hidden" name="current_profile_stage" class="current_profile_stage" value="<?= $data->profile_stage ?? ''; ?>">

                                            <div class="col-md-6">
                                                <label for="fname" class="form-label">First Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="fname" id="fname" value="<?= $fname ?? ''; ?>" placeholder="First Name" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="lname" class="form-label">Last Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="lname" id="lname" value="<?= $lname ?? ''; ?>" placeholder="Last Name" required>
                                            </div>

                                            <input type="hidden" name="chat_name" value="<?= $chat_name ?? ''; ?>">

                                            <div class="col-md-6">
                                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" name="email" id="email" value="<?= $email ?? ''; ?>" placeholder="Email" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="type" class="form-label">My Profile Type <span class="text-danger">*</span></label>
                                                <div class="p_type btn-group btn-group-toggle w-100" data-toggle="buttons">
                                                    <label class="btn <?= ($profile_type == "professional") ? 'active' : ''; ?>">
                                                        <input type="radio" name="type" value="professional" <?= ($profile_type == "professional") ? 'checked' : ''; ?> required> Professional
                                                    </label>
                                                    <label class="btn <?= ($profile_type == "employer") ? 'active' : ''; ?>">
                                                        <input type="radio" name="type" value="employer" <?= ($profile_type == "employer") ? 'checked' : ''; ?> required> Employer
                                                    </label>
                                                    <label class="btn <?= ($profile_type == "provider") ? 'active' : ''; ?>">
                                                        <input type="radio" name="type" value="provider" <?= ($profile_type == "provider") ? 'checked' : ''; ?> required> Service Provider
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="country_code" class="form-label">Country <span class="text-danger">*</span></label>
                                                <select class="country_code" name="country_code" id="country_code" autocomplete="new-password" required>
                                                    <?php
                                                    if($country_code && $country_name) {
                                                        echo '<option value="'.$country_code.'" selected>'.$country_name.'</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="my_city" class="form-label">My City <span class="text-danger">*</span></label>
                                                <select class="my_city" name="coordinates" id="my_city" autocomplete="new-password" required>
                                                    <?php
                                                    if($coordinates && $location) {
                                                        echo '<option value="'.$coordinates.'" selected>'.$location.'</option>';
                                                    }
                                                    ?>
                                                </select>
                                                <label class="error my_city_order_error" for="my_city" style="display: none;"></label>
                                            </div>

                                            <input type="hidden" id="coordinateLocation" name="full_location" value="<?= $location ?? ''; ?>">
                                            <input type="hidden" id="geohash" name="geohash" value="<?= $data->geohash ?? ''; ?>">

                                            <div class="col-md-6">
                                                <label for="local_timezone" class="form-label">Timezone <span class="text-danger">*</span></label>
                                                <input type="text" name="local_timezone" id="local_timezone" value="<?= $data->local_timezone ?? ''; ?>" placeholder="Type to select" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="profile_company" class="form-label">Enter your organization name, or N/A <span class="text-danger">*</span></label>
                                                <select class="profile_company" name="company:company[]" id="profile_company" placeholder="Type to select" required>
                                                    <?php
                                                    if ($data->company) {
                                                        foreach ($data->company as $key => $value) {
                                                            list ($pre, $post) = explode(':>', $value);
                                                            echo '<option value="' . $key . '" data-slug="' . $pre . '" selected>' . $post . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="profile_role" class="form-label">Enter your role, or N/A <span class="text-danger">*</span></label>
                                                <select class="profile_role" name="title:title[]" id="profile_role" placeholder="Type to select" required>
                                                    <?php
                                                    if ($data->title) {
                                                        foreach ($data->title as $key => $value) {
                                                            list ($pre, $post) = explode(':>', $value);
                                                            echo '<option value="' . $key . '" data-slug="' . $pre . '" selected>' . $post . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-check mt-3">
                                                    <input type="checkbox" class="form-check-input" name="currently_working_on" id="currently_working_on" value="1" <?= (($data->currently_working_on ?? 0) == 1) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-label ml-3 mt-2" for="currently_working_on">I currently work here</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-4 p-0 px-md-3">
                                        <button type="submit" class="btn s-btn"><i class="fa fa-save mr-1"></i> <?= $save_button_text ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="form-block-2">
                            <div class="container">
                                <form action="<?= TAOH_ACTION_URL . '/settings'; ?>" method="post" class="g-3" name="profile_form_2" id="profile_form_2">
                                    <div class="mb-4">
                                        <h5 class="p-field-title py-3">General Information</h5>
                                        <hr class="mt-0" style="border-top: 1px solid #D3D3D3;">

                                        <div class="col-md-12 px-0 px-md-3">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="chat_name" class="form-label">My Public Chat Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="chat_name" id="chat_name" value="<?= $chat_name ?? ''; ?>" placeholder="Chat Name" pattern=".*[A-Za-z0-9].*" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="phone_number" class="form-label">Contact Number </label>
                                                    <input type="text" class="form-control" name="phone_number" id="phone_number" value="<?= $data->phone_number ?? ''; ?>" placeholder="Contact Number" oninput="this.value = this.value.replace(/[^0-9+]/g, '');">
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="skill" class="form-label">What are your Core Skills <span class="text-danger">*</span></label>
                                                    <?php echo field_skill($data->skill ?? '', 1); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <?php
                                    if ($show_name_slug_information) {
                                        ?>
                                        <div class="mb-4">
                                            <h5 class="p-field-title py-3"><?php echo (defined('TAOH_WERTUAL_NAME_SLUG') ? ucfirst(TAOH_WERTUAL_NAME_SLUG) . ' ' : '') . 'Information' ?></h5>
                                            <hr class="mt-0" style="border-top: 1px solid #D3D3D3;">

                                            <div class="col-md-12 px-0 px-md-3">
                                                <div class="row rm-p-b">
                                                    <?php
                                                    foreach ($taoh_user_keywords as $key => $value) {
                                                        if (isset($value['enable']) && $value['enable'] == 'true') {
                                                            $data_keywords = (array)($data->keywords ?? []);

                                                            echo '<div class="col-lg-5 mb-2">';
                                                            echo '<label for="select-' . $key . '" class="form-label">' . $value['label'];
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
                                        </div>
                                        <?php
                                    }
                                    ?>


                                    <div class="col-12 mt-5 p-0">
                                        <button type="submit" class="btn s-btn"><i class="fa fa-save mr-1"></i> <?= $save_button_text ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="form-block-2-1">
                            <div class="container">
                                <form action="<?= TAOH_ACTION_URL . '/settings'; ?>" method="post" class="g-3" name="profile_form_2_1" id="profile_form_2_1">
                                    <div>
                                        <h5 class="p-field-title py-3">Select the tags that suits you in each category</h5>
                                        <hr class="mt-0" style="border-top: 1px solid #D3D3D3;">

                                        <div class="col-12">
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
                                                <?php
                                                foreach ($tag_category as $category_key => $categories) {
                                                    ?>
                                                    <div class="tab-pane <?php echo ($category_key == 'hiring-talent' ) ? 'active show' : 'fade' ; ?>" id="<?php echo $category_key; ?>" role="tabpanel" aria-labelledby="<?php echo $category_key; ?>">
                                                        <div class="tags" style="max-width: 986px;">
                                                            <?php
                                                            foreach ($categories as $ckey => $category) {
                                                                $category_key_id = str_replace(" ", "_", strtolower($category));
                                                                echo '<input type="checkbox" id="' . $category_key_id . '" name="tags[]" data-key="tab_' . $category_key_id . '" '.
                                                                    'value="' . $category . '" ' . ((!empty($data->tags ?? []) && in_array($category, $data->tags)) ? 'checked' : '') . '>';
                                                                echo '<label for="' . $category_key_id . '" class="tag-label">' . $category . '</label>';
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>

                                            <p class="sel-status" id="selection-status">You have selected 0 out of 5 profile tags!</p>
                                            <div class="selected-tags mb-3" id="selected-tags"></div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-5 p-0">
                                        <button type="submit" class="btn s-btn"><i class="fa fa-save mr-1"></i> <?= $save_button_text ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <?php
                        foreach ($tag_category_form as $category_key => $category_form) {
                            $category_key_id = str_replace(" ", "_", strtolower($category_key));
                            ?>
                            <div class="tab-pane fade" id="<?= 'tab_'. $category_key_id; ?>">
                                <div class="container">
                                    <form action="<?= TAOH_ACTION_URL . '/settings'; ?>" method="post" class="g-3 profile_tags_form" name="profile_tags_form_<?= $category_key_id ?>" id="profile_tags_form_<?= $category_key_id ?>">
                                        <div>
                                            <h5 class="p-field-title py-3"><?= $category_key; ?></h5>
                                            <hr class="mt-0" style="border-top: 1px solid #D3D3D3;">

                                            <?php
                                            echo '<input type="hidden" name="tags_data[' . $category_key_id . '][category_value]" value="' . $category_key . '">';
                                            foreach ($category_form as $ckey => $cform) {
                                                if ($ckey % 2 == 0) {
                                                    echo '<div class="row">';
                                                }

                                                echo '<div class="form-group col-lg-5">';
                                                echo '<label class="form-label" for="' . $cform['field_name'] . '">' . $cform['field_value'] . '</label>';

                                                if ($cform['field_type'] == 'text') {
                                                    echo '<input class="form-control" type="text" id="' . $cform['field_name'] . '" name="tags_data[' . $category_key_id . '][' . $cform['field_name'] . ']"' .
                                                            'value="' . safe_get($data, $category_key_id, $cform['field_name']) . '">';
                                                } elseif ($cform['field_type'] == 'number') {
                                                    echo '<input class="form-control" type="number" id="' . $cform['field_name'] . '" name="tags_data[' . $category_key_id . '][' . $cform['field_name'] . ']"' .
                                                            'value="' . safe_get($data, $category_key_id, $cform['field_name']) . '"';
                                                            if($cform['field_name'] === 'salary_expectations'){
                                                                echo ' max="9999999"';
                                                            }
                                                            echo '>';
                                                } elseif ($cform['field_type'] == 'dropdown') {
                                                    echo '<select class="form-control" name="tags_data[' . $category_key_id . '][' . $cform['field_name'] . ']">';
                                                    echo '<option value="">--Select--</option>';
                                                    foreach ($cform['dropdown_value'] as $dkey => $dval) {
                                                        echo '<option value="' . $dval . '" ' . ((safe_get($data, $category_key_id, $cform['field_name']) == $dval) ? 'selected' : '') . '>' . $dval . '</option>';
                                                    }
                                                    echo '</select>';
                                                }

                                                echo '</div>';


                                                if ($ckey % 2 != 0 || $ckey == count($category_form) - 1) {
                                                    echo '</div>';
                                                }
                                            }
                                            ?>
                                        </div>

                                        <div class="col-12 mt-4 p-0">
                                            <button type="submit" class="btn s-btn"><i class="fa fa-save mr-1"></i> <?= $save_button_text ?></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php
                        }
                        ?>


                        <!-- Experience Details -->
                        <div class="tab-pane fade" id="form-block-3">
                            <div class="container">
                                <div class="modal fade profile-n-modal" id="add_edit_employee">
                                    <div class="modal-dialog modal-lg d-flex justify-content-center align-items-center">
                                        <div class="modal-content">
                                            <form action="<?= TAOH_ACTION_URL . '/settings'; ?>" method="post" class="g-3" name="profile_experience_form" id="profile_experience_form">
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
                                                    <div class="d-flex justify-content-end">
                                                        <button type="submit" class="btn s-btn" name="emp_btnSave" id="emp_btnSave"><i class="fa fa-save mr-1"></i> Save</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h5 class="p-field-title py-3">Experience Details</h5>
                                    <hr class="mt-0" style="border-top: 1px solid #D3D3D3;">

                                    <?php
                                    if (is_array($emp_list)) {
                                        if (count($emp_list) > 0 && is_array($emp_list[$emp_last_key]['title'])) {

                                            $emp_year = array();
                                            foreach ($emp_list as $ekeys => $evals) {
                                                $emp_year[$ekeys] = $evals['emp_year_end'];
                                                $emp_list[$ekeys]['keys'] = $ekeys;
                                            }
                                            array_multisort($emp_year, SORT_DESC, $emp_list);

                                            foreach ($emp_list as $emp_keys => $emp_vals) {
                                                $em_title = !empty($emp_vals['emp_title']) ? $emp_vals['emp_title'] : $emp_vals['title'];
                                                foreach ($em_title as $em_key => $em_value) {
                                                    if (!is_array($em_value)) {
                                                        list ($em_pre, $em_post) = explode(':>', $em_value);
                                                    } else {
                                                        foreach ($em_value as $emp_key => $emp_value) {
                                                            list ($em_pre, $em_post) = explode(':>', $emp_value);
                                                        }
                                                    }
                                                }

                                                $em_company = !empty($emp_vals['emp_company']) ? $emp_vals['emp_company'] : $emp_vals['company'];
                                                foreach ($em_company as $em_cmp_key => $em_cmp_value) {
                                                    if (!is_array($em_cmp_value)) {
                                                        list ($em_cmp_pre, $em_cmp_post) = explode(':>', $em_cmp_value);
                                                    } else {
                                                        foreach ($em_cmp_value as $emp_cmp_key => $emp_cmp_value) {
                                                            list ($em_cmp_pre, $em_cmp_post) = explode(':>', $emp_cmp_value);
                                                        }
                                                    }
                                                }

                                                $get_present_not = (($emp_vals['current_role'] ?? '') == 'on') ? ' Present' : get_month_from_number($emp_vals['emp_end_month']) . ' ' . $emp_vals['emp_year_end'];
                                                $current_year = date('Y');   // Outputs: 2025 (Current Year)
                                                $current_month = date('n');  // Outputs: 3 (Current Month - Without leading zero)

                                                $end_month = !empty($emp_vals['emp_end_month']) ? $emp_vals['emp_end_month'] : $current_month;
                                                $end_year = !empty($emp_vals['emp_year_end']) ? $emp_vals['emp_year_end'] : $current_year;


                                                $emp_placeType = $emp_vals['emp_placeType'];
                                                if ($emp_placeType == 'rem') {
                                                    $emp_placeType = ' . ' . 'Remote';
                                                } else if ($emp_placeType == 'ons') {
                                                    $emp_placeType = '. ' . 'Onsite';
                                                } else if ($emp_placeType == 'hyb') {
                                                    $emp_placeType = '. ' . 'Hybrid';
                                                } else {
                                                    $emp_placeType = '';
                                                }

                                                $skills = $emp_vals['skill'] ?? [];

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
                                                $roletype = $emp_vals['emp_roletype'] ?? [];
                                                $role_items = '';
                                                foreach ($roletype as $key => $value) {
                                                    if(empty($value)) continue;
                                                    $role_items = $roletype_arr[$value] ?? '';
                                                }

                                                $industry_categories = defined('TAOH_INDUSTRY_CATEGORIES')? TAOH_INDUSTRY_CATEGORIES : [];

                                                if (!$user_is_logged_in) {
                                                    ?>
                                                    <div class="profile-card-list mb-5">
                                                        <div class="heading px-4">
                                                            <h5 class="d-flex align-items-center" style="gap: 16px;">
                                                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                                                                     xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.7812 3H19.2188C19.4766 3 19.6875 3.225 19.6875 3.5V6H10.3125V3.5C10.3125 3.225 10.5234 3 10.7812 3ZM7.5 3.5V6H3.75C1.68164 6 0 7.79375 0 10V16H11.25H18.75H30V10C30 7.79375 28.3184 6 26.25 6H22.5V3.5C22.5 1.56875 21.0293 0 19.2188 0H10.7812C8.9707 0 7.5 1.56875 7.5 3.5ZM30 18H18.75V20C18.75 21.1063 17.9121 22 16.875 22H13.125C12.0879 22 11.25 21.1063 11.25 20V18H0V26C0 28.2062 1.68164 30 3.75 30H26.25C28.3184 30 30 28.2062 30 26V18Z"
                                                                          fill="#1573B5"/>
                                                                </svg>
                                                                <span><?php echo $em_post ?? ''; ?></span>
                                                            </h5>
                                                        </div>

                                                        <div class="px-4 pt-4 pb-5 d-flex" style="gap: 16px;">
                                                            <svg style="min-width: fit-content;" width="28" height="28"
                                                                 viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M0 0V28H28V0H0ZM19.7812 20.325L14 25.8687L8.21875 20.325L12.25 8.825L8.21875 3.4125H19.775L15.75 8.825L19.7812 20.325Z"
                                                                      fill="#1573B5"/>
                                                            </svg>

                                                            <div>
                                                                <div class="d-flex align-items-center mb-1" style="gap: 12px;">
                                                                    <h5 class="j-title"><?php echo $em_cmp_post ?? ''; ?></h5>
                                                                </div>
                                                                <p class="duration mb-2"><?php echo get_month_from_number($emp_vals['emp_start_month']) . ' ' . $emp_vals['emp_year_start'] . ' to ' . $get_present_not ?>
                                                                    .
                                                                    <span><?php echo get_diff_dates($emp_vals['emp_year_start'], $emp_vals['emp_start_month'], $end_year, $end_month); ?></span>
                                                                </p>
                                                                <h6 class="list-text-xs mb-2">Responsibilities</h6>
                                                                <p class="list-text-xxs"><?php echo taoh_title_desc_decode($emp_vals['emp_responsibilities']); ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <div class="profile-card-list mb-5">
                                                        <div class="heading px-4">
                                                            <h5 class="d-flex align-items-center" style="gap: 16px;">
                                                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                                                                     xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.7812 3H19.2188C19.4766 3 19.6875 3.225 19.6875 3.5V6H10.3125V3.5C10.3125 3.225 10.5234 3 10.7812 3ZM7.5 3.5V6H3.75C1.68164 6 0 7.79375 0 10V16H11.25H18.75H30V10C30 7.79375 28.3184 6 26.25 6H22.5V3.5C22.5 1.56875 21.0293 0 19.2188 0H10.7812C8.9707 0 7.5 1.56875 7.5 3.5ZM30 18H18.75V20C18.75 21.1063 17.9121 22 16.875 22H13.125C12.0879 22 11.25 21.1063 11.25 20V18H0V26C0 28.2062 1.68164 30 3.75 30H26.25C28.3184 30 30 28.2062 30 26V18Z"
                                                                          fill="#1573B5"/>
                                                                </svg>
                                                                <span><?php echo $em_post ?? ''; ?></span>
                                                            </h5>
                                                            <div class="d-flex align-items-center" style="gap: 12px;">
                                                                <?php
                                                                if ($user_is_logged_in && (isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken) && taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken == $ptoken)) { ?>
                                                                <a class="add_edit_emp" data-add-edit="Edit" data-add-edit="Edit" data-employee="<?php echo $emp_keys; ?>" data-emp-delete="<?php echo $emp_keys; ?>" data-emp-edit-delete= <?php echo $emp_vals['keys']; ?>>
                                                                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M29.475 1.02656C28.1063 -0.342188 25.8937 -0.342188 24.525 1.02656L22.6437 2.90156L28.7625 9.02031L30.6437 7.13906C32.0125 5.77031 32.0125 3.55781 30.6437 2.18906L29.475 1.02656ZM10.775 14.7766C10.3937 15.1578 10.1 15.6266 9.93125 16.1453L8.08125 21.6953C7.9 22.2328 8.04375 22.8266 8.44375 23.2328C8.84375 23.6391 9.4375 23.7766 9.98125 23.5953L15.5312 21.7453C16.0438 21.5766 16.5125 21.2828 16.9 20.9016L27.3563 10.4391L21.2313 4.31406L10.775 14.7766ZM6 3.67031C2.6875 3.67031 0 6.35781 0 9.67031V25.6703C0 28.9828 2.6875 31.6703 6 31.6703H22C25.3125 31.6703 28 28.9828 28 25.6703V19.6703C28 18.5641 27.1063 17.6703 26 17.6703C24.8937 17.6703 24 18.5641 24 19.6703V25.6703C24 26.7766 23.1063 27.6703 22 27.6703H6C4.89375 27.6703 4 26.7766 4 25.6703V9.67031C4 8.56406 4.89375 7.67031 6 7.67031H12C13.1062 7.67031 14 6.77656 14 5.67031C14 4.56406 13.1062 3.67031 12 3.67031H6Z" fill="url(#paint0_linear_6710_358)"/>
                                                                        <defs><linearGradient id="paint0_linear_6710_358" x1="15.8352" y1="0" x2="15.8352" y2="31.6703" gradientUnits="userSpaceOnUse"><stop stop-color="#2557A7"/><stop offset="1" stop-color="#176FB3"/></linearGradient></defs>
                                                                    </svg>
                                                                </a>
                                                                <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>

                                                        <div class="px-4 pt-4 pb-5 d-flex" style="gap: 16px;">
                                                            <svg style="min-width: fit-content;" width="28" height="28"
                                                                 viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M0 0V28H28V0H0ZM19.7812 20.325L14 25.8687L8.21875 20.325L12.25 8.825L8.21875 3.4125H19.775L15.75 8.825L19.7812 20.325Z"
                                                                      fill="#1573B5"/>
                                                            </svg>

                                                            <div>
                                                                <div class="d-flex align-items-center mb-1" style="gap: 12px;">
                                                                    <h5 class="j-title"><?php echo $em_cmp_post ?? ''; ?></h5>
                                                                    <?php if ($role_items != '') { ?>
                                                                        <span class="j-type-badge py-1"><?php echo $role_items; ?></span>
                                                                    <?php } ?>
                                                                </div>
                                                                <p class="duration mb-2"><?php echo get_month_from_number($emp_vals['emp_start_month']) . ' ' . $emp_vals['emp_year_start'] . ' to ' . $get_present_not ?>
                                                                    .
                                                                    <span><?php echo get_diff_dates($emp_vals['emp_year_start'], $emp_vals['emp_start_month'], $end_year, $end_month); ?></span>
                                                                </p>
                                                                <div class="d-flex align-items-center flex-wrap mb-3"
                                                                     style="gap: 12px;">
                                                                    <?php if (trim($emp_vals['emp_full_location']) != '' && trim($emp_placeType) != '') { ?>
                                                                        <p class="list-text-xs mr-lg-4">
                                                                            Location: <?php echo $emp_vals['emp_full_location'] . $emp_placeType; ?></p>
                                                                    <?php } ?>
                                                                    <p class="list-text-xs">
                                                                        Industry: <?php echo $industry_categories[$emp_vals['emp_industry']]; ?></p>
                                                                </div>
                                                                <?php if (!empty($emp_vals['skill']) && is_array($emp_vals['skill'])) { ?>
                                                                    <div class="d-flex flex-wrap align-items-center mb-2"
                                                                         style="gap: 8px;">
                                                                        <p class="list-text-xs mr-1">Skills:</p>
                                                                        <?php foreach ($skills as $s_keys => $s_vals) {
                                                                            if (!is_array($s_vals)) {
                                                                                $items = explode(':>', $s_vals); ?>
                                                                                <span class="skill-badge"><?php echo $items[1]; ?></span>
                                                                            <?php } else {
                                                                                foreach ($s_vals as $es_keys => $es_vals) {
                                                                                    $items = explode(':>', $es_vals); ?>
                                                                                    <span class="skill-badge"><?php echo $items[1]; ?></span>
                                                                                <?php }
                                                                            }
                                                                        } ?>

                                                                    </div>
                                                                <?php } ?>
                                                                <?php if (trim($emp_vals['emp_responsibilities']) != '') { ?>
                                                                    <h6 class="list-text-xs mb-2">Responsibilities</h6>
                                                                    <p class="list-text-xxs"><?php echo taoh_title_desc_decode($emp_vals['emp_responsibilities']); ?></p>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                        }
                                    }
                                    ?>


                                    <div class="profile-card-list">
                                        <div class="heading px-4 px-lg-5 justify-content-center">
                                            <h5 class="text-center">Add Your Experience Details</h5>
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
                                </div>
                            </div>
                        </div>

                        <!-- Education Details -->
                        <div class="tab-pane fade" id="form-block-4">
                            <div class="container">
                                <div class="modal fade profile-n-modal" id="add_edit_education">
                                    <div class="modal-dialog modal-lg d-flex justify-content-center align-items-center">
                                        <div class="modal-content">
                                            <form action="<?= TAOH_ACTION_URL . '/settings'; ?>" method="post" class="g-3" name="profile_education_form" id="profile_education_form">
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
                                                    <div class="d-flex justify-content-end">
                                                        <button type="submit" class="btn s-btn" name="edu_btnSave" id="edu_btnSave"><i class="fa fa-save mr-1"></i> Save</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h5 class="p-field-title py-3">Education Details</h5>
                                    <hr class="mt-0" style="border-top: 1px solid #D3D3D3;">

                                    <?php
                                    if (is_array($edu_list)) {
                                        if (is_array($edu_list[$edu_last_key]['company'])) {
                                            ?>
                                            <div>
                                                <?php
                                                $edu_year = array();
                                                foreach($edu_list as $edu_keys => $edu_vals){
                                                    $edu_year[$edu_keys] = $edu_vals['edu_complete_year'];
                                                    $edu_list[$edu_keys]['keys'] = $edu_keys;
                                                }
                                                array_multisort($edu_year, SORT_DESC, $edu_list);

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

                                                    $d_skills = $edu_vals['skill'] ?? [];
                                                    /* $d_items = '';
                                                    foreach ($d_skills as $d_keys => $d_vals){
                                                        $d_items = explode(':>',$d_vals);
                                                    } */
                                                    ?>
                                                    <?php if (!$user_is_logged_in) { ?>
                                                        <div class="profile-card-list mb-5">
                                                            <div class="heading px-4">
                                                                <h5 class="d-flex align-items-center" style="gap: 16px;">
                                                                    <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M9.98507 0.1046L0.798648 4.04248C0.224497 4.2886 -0.0953875 4.9039 0.031746 5.51099C0.158879 6.11808 0.69202 6.56109 1.31538 6.56109V6.88925C1.31538 7.43481 1.7542 7.87372 2.29964 7.87372H18.704C19.2494 7.87372 19.6882 7.43481 19.6882 6.88925V6.56109C20.3116 6.56109 20.8488 6.12218 20.9719 5.51099C21.0949 4.89979 20.775 4.2845 20.205 4.04248L11.0185 0.1046C10.6905 -0.0348667 10.3132 -0.0348667 9.98507 0.1046ZM5.25242 9.18635H2.62773V17.2385C2.60312 17.2508 2.57852 17.2672 2.55391 17.2836L0.585392 18.5962C0.105565 18.9162 -0.111792 19.5151 0.0563525 20.0689C0.224497 20.6226 0.737132 21 1.31538 21H19.6882C20.2665 21 20.775 20.6226 20.9432 20.0689C21.1113 19.5151 20.898 18.9162 20.4141 18.5962L18.4456 17.2836C18.421 17.2672 18.3964 17.2549 18.3718 17.2385V9.18635H15.7512V17.0621H14.1108V9.18635H11.4861V17.0621H9.51755V9.18635H6.89285V17.0621H5.25242V9.18635ZM10.5018 2.62321C10.8499 2.62321 11.1837 2.7615 11.4298 3.00766C11.6759 3.25383 11.8142 3.5877 11.8142 3.93583C11.8142 4.28396 11.6759 4.61784 11.4298 4.864C11.1837 5.11017 10.8499 5.24846 10.5018 5.24846C10.1537 5.24846 9.81995 5.11017 9.57384 4.864C9.32772 4.61784 9.18946 4.28396 9.18946 3.93583C9.18946 3.5877 9.32772 3.25383 9.57384 3.00766C9.81995 2.7615 10.1537 2.62321 10.5018 2.62321Z" fill="#1474B6"/>
                                                                    </svg>
                                                                    <span><?php echo $ed_post ?? ''; ?></span>
                                                                </h5>
                                                            </div>

                                                            <div class="px-4 pt-4 pb-5 d-flex" style="gap: 16px;">
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
                                                    <?php } else {  ?>
                                                        <div class="profile-card-list mb-5">
                                                            <div class="heading px-4">
                                                                <h5 class="d-flex align-items-center" style="gap: 16px;">
                                                                    <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M9.98507 0.1046L0.798648 4.04248C0.224497 4.2886 -0.0953875 4.9039 0.031746 5.51099C0.158879 6.11808 0.69202 6.56109 1.31538 6.56109V6.88925C1.31538 7.43481 1.7542 7.87372 2.29964 7.87372H18.704C19.2494 7.87372 19.6882 7.43481 19.6882 6.88925V6.56109C20.3116 6.56109 20.8488 6.12218 20.9719 5.51099C21.0949 4.89979 20.775 4.2845 20.205 4.04248L11.0185 0.1046C10.6905 -0.0348667 10.3132 -0.0348667 9.98507 0.1046ZM5.25242 9.18635H2.62773V17.2385C2.60312 17.2508 2.57852 17.2672 2.55391 17.2836L0.585392 18.5962C0.105565 18.9162 -0.111792 19.5151 0.0563525 20.0689C0.224497 20.6226 0.737132 21 1.31538 21H19.6882C20.2665 21 20.775 20.6226 20.9432 20.0689C21.1113 19.5151 20.898 18.9162 20.4141 18.5962L18.4456 17.2836C18.421 17.2672 18.3964 17.2549 18.3718 17.2385V9.18635H15.7512V17.0621H14.1108V9.18635H11.4861V17.0621H9.51755V9.18635H6.89285V17.0621H5.25242V9.18635ZM10.5018 2.62321C10.8499 2.62321 11.1837 2.7615 11.4298 3.00766C11.6759 3.25383 11.8142 3.5877 11.8142 3.93583C11.8142 4.28396 11.6759 4.61784 11.4298 4.864C11.1837 5.11017 10.8499 5.24846 10.5018 5.24846C10.1537 5.24846 9.81995 5.11017 9.57384 4.864C9.32772 4.61784 9.18946 4.28396 9.18946 3.93583C9.18946 3.5877 9.32772 3.25383 9.57384 3.00766C9.81995 2.7615 10.1537 2.62321 10.5018 2.62321Z" fill="#1474B6"/>
                                                                    </svg>
                                                                    <span><?php echo $ed_post ?? ''; // $edu_vals['edu_specalize']; ?></span>
                                                                </h5>
                                                                <div class="d-flex align-items-center" style="gap: 12px;">
                                                                    <?php if( $user_is_logged_in && (isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken) && taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken == $ptoken)) { ?>
                                                                        <a class="add_edit_edu" data-add-edit="Edit" data-education="<?php echo $edu_keys; ?>" data-edu-edit-delete="<?php echo $edu_vals['keys']; ?>">
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
                                                                        <!--                                                        <a class="add_edit_edu" data-add-edit="Add" data-education="--><?php //echo $edu_tot_count ?? '0'; ?><!--">-->
                                                                        <!--                                                            <svg width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg">-->
                                                                        <!--                                                                <circle cx="19.5" cy="19.5" r="19.5" fill="#2557A7"/>-->
                                                                        <!--                                                                <path d="M21.1154 10.6154C21.1154 9.72187 20.3935 9 19.5 9C18.6065 9 17.8846 9.72187 17.8846 10.6154V17.8846H10.6154C9.72187 17.8846 9 18.6065 9 19.5C9 20.3935 9.72187 21.1154 10.6154 21.1154H17.8846V28.3846C17.8846 29.2781 18.6065 30 19.5 30C20.3935 30 21.1154 29.2781 21.1154 28.3846V21.1154H28.3846C29.2781 21.1154 30 20.3935 30 19.5C30 18.6065 29.2781 17.8846 28.3846 17.8846H21.1154V10.6154Z" fill="#F6F6F6"/>-->
                                                                        <!--                                                                <defs>-->
                                                                        <!--                                                                    <linearGradient id="paint0_linear_6485_153" x1="19.5" y1="0" x2="19.5" y2="39" gradientUnits="userSpaceOnUse">-->
                                                                        <!--                                                                        <stop stop-color="#2557A7"/>-->
                                                                        <!--                                                                        <stop offset="1" stop-color="#1377B7"/>-->
                                                                        <!--                                                                    </linearGradient>-->
                                                                        <!--                                                                </defs>-->
                                                                        <!--                                                            </svg>-->
                                                                        <!--                                                        </a>-->
                                                                    <?php } ?>
                                                                </div>
                                                            </div>

                                                            <div class="px-4 pt-4 pb-5 d-flex" style="gap: 16px;">
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
                                                                    <?php if(!empty($edu_vals['skill']) && is_array($edu_vals['skill'])){
                                                                        $d_items = '';
                                                                        $d_skills = $edu_vals['skill'];
                                                                        ?>
                                                                        <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 8px;">
                                                                            <p class="list-text-xs mr-1">Skills:</p>
                                                                            <?php
                                                                            foreach ($d_skills as $d_keys => $d_vals){
                                                                                if(isset($ed_value) && !is_array($ed_value)){
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
                                            <?php
                                        }
                                    }
                                    ?>

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
                                </div>
                            </div>
                        </div>

                        <!-- About Hobbies Interests -->
                        <div class="tab-pane fade" id="form-block-5">
                            <div class="container">
                                <form action="<?= TAOH_ACTION_URL . '/settings'; ?>" method="post" class="g-3" name="profile_form_5" id="profile_form_5">
                                    <div>
                                        <h5 class="p-field-title py-3">About, Hobbies & Interests</h5>
                                        <hr class="mt-0" style="border-top: 1px solid #D3D3D3;">

                                        <div class="row mt-4">
                                            <div class="form-group col-lg-6">
                                                <label class="form-label" for="aboutme">About Me</label>
                                                <textarea  class="form-control form--control" rows="8" maxlength="500" name="aboutme" id="aboutme" style="min-height: 200px;"><?php echo taoh_title_desc_decode(trim($data?->aboutme ?? '')); ?> </textarea>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label class="form-label" for="funfact">Fun Fact (Great for ice-breakers)</label>
                                                <textarea class="form-control form--control" rows="8" maxlength="500" name="funfact" id="funfact" style="min-height: 200px;" ><?php echo taoh_title_desc_decode(trim($fun_fact ?? '')); ?> </textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-6">
                                                <label class="form-label" for="hobbies">Hobbies and Interests</label>
                                                <select name="hobbies[]" id="hobbies" class="select2 form-control hobbies-field" multiple>
                                                    <?php foreach(PROFESSIONAL_HOBBIES as $hkey=>$hobby){ ?>
                                                        <option value="<?php echo $hkey; ?>" <?php echo (is_array($hobbies) && in_array($hkey,$hobbies)) ? 'selected' : ''; ?>><?php echo $hobby; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label class="form-label" for="mylink">Where to find me online? <span style="font-size: 16px;">(public link e.g. LinkedIn)</span></label>
                                                <input class="form-control form--control" type="url" value="<?php echo @$data->mylink; ?>" name="mylink" id="mylink" placeholder="https://www.linkedin.com/">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-5 p-0">
                                        <button type="submit" class="btn s-btn"><i class="fa fa-save mr-1"></i> <?= $save_button_text ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="form-block-6">
                            <div class="container">
                                <form action="<?= TAOH_ACTION_URL . '/settings'; ?>" method="post" class="g-3" name="profile_form_6" id="profile_form_6">
                                    <div>
                                        <h5 class="p-field-title py-3">Setup your email preferences</h5>
                                        <hr class="mt-0" style="border-top: 1px solid #D3D3D3;">

                                        <p class="list-text-nml my-3">We get it—too many emails! Click the checkbox below to unsubscribe from promotional and reminder emails. You can always rejoin when you're ready!</p>
                                        <div class="form-check mt-3">
                                            <input type="checkbox" class="form-check-input" name="tao_unsubscribe_emails" id="tao-unsubscribe" value="1" <?= (($data->tao_unsubscribe_emails ?? 0) == 1) ? 'checked' : ''; ?>>
                                            <label for="tao-unsubscribe" class="form-check-label ml-3 mt-2">Unsubscribe me</label>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <h5 class="p-field-title py-3">Remove from the directory?</h5>
                                        <hr class="mt-0" style="border-top: 1px solid #D3D3D3;">

                                        <p class="list-text-nml my-3">By selecting Yes you will be unlisted from our directory at the same time you can't see other users too.</p>
                                        <div class="btn-group btn-group-toggle btn--group" data-toggle="buttons">
                                            <label class="btn <?= ($unlist_me_dir == "no") ? 'active' : ''; ?>">
                                                <input type="radio" name="unlist_me_dir" value="no" <?= ($unlist_me_dir == "no") ? 'checked' : ''; ?>> No
                                            </label>
                                            <label class="btn <?= ($unlist_me_dir == "yes") ? 'active' : ''; ?>">
                                                <input type="radio" name="unlist_me_dir" value="yes" <?= ($unlist_me_dir == "yes") ? 'checked' : ''; ?>> Yes
                                            </label>
                                        </div>
                                    </div>


                                    <div class="col-12 mt-5 p-0">
                                        <button type="submit" class="btn s-btn"><i class="fa fa-save mr-1"></i> <?= $save_button_text ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="form-block-7">
                            <div class="container">
                                <div class="mb-4">
                                    <h5 class="p-field-title py-3">Delete your account</h5>
                                    <hr class="mt-0" style="border-top: 1px solid #D3D3D3;">

                                    <p class="list-text-nml my-3">If you choose to delete your account! We respect your choice. You can delete your account by clicking the delete account button here.</p>
                                    <button type="button" class="btn d-btn" data-toggle="modal" data-target="#delete-modal">Delete Account</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div style="background:none; border:none" class="modal-content">
                <div class="modal-body p-0 ">
                    <div class="user-panel-main-bar">
                        <div class="user-panel">
                            <div class="delete-account-info card card-item border border-danger mb-0">
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


<script type="application/javascript">
    let my_ptoken = '<?php echo $ptoken ?? ''; ?>';
    let tabIndex = parseInt('<?php echo $tab_index ?? 0; ?>', 10) || 0;
    let taoh_user_keywords = JSON.parse('<?php echo defined('TAOH_USER_KEYWORDS') ? TAOH_USER_KEYWORDS : '{}'; ?>');

    $(document).ready(function () {
        $(".hobbies-field").select2({width: '100%'});

        for (const [key, value] of Object.entries(taoh_user_keywords)) {
            let allow_expand = ((value.allow_expand).toString() === 'true');

            if ($('#select-' + key).length > 0 && (value.enable).toString() === 'true') {
                new TomSelect('#select-' + key, {
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
                        let specials = /[*|":<>[\]{}`\\';@&$]/;
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
                        if (allow_expand) {
                            $.post(_taoh_site_ajax_url, {
                                'taoh_action': 'taoh_add_keyword_opt',
                                'keyword': value,
                                'key': key
                            }, function (response) {
                                console.log(response);
                            })
                        }
                    },
                    onFocus: function () {
                        const selectInstance = this;
                        if (!selectInstance.hasOptions) {
                            selectInstance.load('');
                        }
                    }
                });
            }
        }

        $('#profile_form_2').validate({
            rules: {
                chat_name: {
                    required: true,
                }
            },
            messages: {
                chat_name: {
                    required: "Chat name is required",
                }
            },
            errorPlacement: function (error, element) {
                if (element.closest('.form-group').length) {
                    error.insertAfter(element.closest('.form-group'));
                } else if (element.hasClass('ts-hidden-accessible')) {
                    error.insertAfter(element.next('.ts-wrapper'));
                } else {
                    element.after(error);
                }
            },
            submitHandler: function (form) {
                // const ts = document.querySelector('#country_code')?.tomselect;

                let profile_form_2 = $('#profile_form_2');
                let submit_btn = profile_form_2.find('button[type="submit"]');
                let submit_btn_icon = submit_btn.find('i');

                let formData = new FormData(form);
                formData.append('taoh_action', 'general_profile_update');
                formData.append('taoh_ptoken', my_ptoken);
                // if (ts) {
                //     const selectedValue = ts.getValue();
                //     const selectedData = ts.options[selectedValue];
                //
                //     formData.append('country_name', selectedData.name);
                // }

                submit_btn_icon.removeClass('fa-save').addClass('fa-spinner fa-spin');
                submit_btn.prop('disabled', true);

                $.ajax({
                    url: profile_form_2.attr('action'),
                    type: 'post',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function (response) {
                        if (response.status) {
                            let profileStage = parseInt(response?.data?.profile_stage, 10) || 0;

                            let successMessage = 'Your general settings have been successfully completed.';
                            if(profileStage > 0 && profileStage < 3) {
                                successMessage += ' You are now eligible to join the event or explore the site, and you can complete the remaining setup at your convenience.';
                            }
                            taoh_set_success_message(successMessage, false, 'toast-middle', [
                                {
                                    text: 'OK',
                                    action: () => {
                                        window.location.reload();
                                    },
                                    class: 'dojo-v1-btn float-right mt-3 mb-3'
                                }
                            ]);

                            // $.confirm({
                            //     title: 'Success',
                            //     content: successMessage,
                            //     type: 'green',
                            //     buttons: {
                            //         confirm: {
                            //             text: 'OK',
                            //             action: function () {
                            //                 window.location.reload();
                            //             }
                            //         }
                            //     }
                            // });
                        } else {
                            taoh_set_error_message('Failed to update your general profile info! Try Again', false, 'toast-middle', [
                                {
                                    text: 'OK',
                                    action: () => {},
                                    class: 'dojo-v1-btn float-right mt-3 mb-3'
                                }
                            ]);

                            // $.alert('Failed to update your general profile info! Try Again');
                        }
                        submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                        submit_btn.prop('disabled', false);
                    },
                    error: function (xhr) {
                        console.log('General Info create error:', xhr.status);
                        submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                        submit_btn.prop('disabled', false);
                    }
                });

            }
        });

        $('#profile_form_2_1').validate({
            errorPlacement: function (error, element) {
                if (element.closest('.btn-group').length) {
                    error.insertAfter(element.closest('.btn-group'));
                } else if (element.hasClass('ts-hidden-accessible')) {
                    error.insertAfter(element.next('.ts-wrapper'));
                } else {
                    element.after(error);
                }
            },
            submitHandler: function (form) {
                let profile_form_2_1 = $('#profile_form_2_1');
                let submit_btn = profile_form_2_1.find('button[type="submit"]');
                let submit_btn_icon = submit_btn.find('i');

                let formData = new FormData(form);
                formData.append('taoh_action', 'profile_tags_update');
                formData.append('taoh_ptoken', my_ptoken);

                submit_btn_icon.removeClass('fa-save').addClass('fa-spinner fa-spin');
                submit_btn.prop('disabled', true);

                $.ajax({
                    url: profile_form_2_1.attr('action'),
                    type: 'post',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function (response) {
                        if (response.status) {
                            let profileStage = parseInt(response?.data?.profile_stage, 10) || 0;

                            let successMessage = 'Your profile flags have been successfully updated. Please continue updating the respective profile flag forms.';
                            if (profileStage > 0 && profileStage < 3) {
                                successMessage += ' You are now eligible to join the event or explore the site, and you can complete the remaining setup at your convenience.';
                            }
                            taoh_set_success_message(successMessage, false, 'toast-middle', [
                                {
                                    text: 'OK',
                                    action: () => {
                                        window.location.reload();
                                    },
                                    class: 'dojo-v1-btn float-right mt-3 mb-3'
                                }
                            ]);

                            // $.confirm({
                            //     title: 'Success',
                            //     content: successMessage,
                            //     type: 'green',
                            //     buttons: {
                            //         confirm: {
                            //             text: 'OK',
                            //             action: function () {
                            //                 window.location.reload();
                            //             }
                            //         }
                            //     }
                            // });
                        } else {
                            taoh_set_error_message('Failed to update your profile flags! Try Again', false, 'toast-middle', [
                                {
                                    text: 'OK',
                                    action: () => {},
                                    class: 'dojo-v1-btn float-right mt-3 mb-3'
                                }
                            ]);
                            // $.alert('Failed to update your profile flags! Try Again');
                        }
                        submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                        submit_btn.prop('disabled', false);
                    },
                    error: function (xhr) {
                        console.log('Profile flags create error:', xhr.status);
                        submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                        submit_btn.prop('disabled', false);
                    }
                });

            }
        });

        $('.profile_tags_form').each(function () {
            const formId = this.id;

            let options = {
                errorPlacement: function (error, element) {
                    if (element.closest('.btn-group').length) {
                        error.insertAfter(element.closest('.btn-group'));
                    } else if (element.hasClass('ts-hidden-accessible')) {
                        error.insertAfter(element.next('.ts-wrapper'));
                    } else {
                        element.after(error);
                    }
                },
                submitHandler: function (form) {
                    const selectedTagElements = $('#myTabContent input[type="checkbox"]:checked');

                    let profile_tags_form = $(form);
                    let submit_btn = profile_tags_form.find('button[type="submit"]');
                    let submit_btn_icon = submit_btn.find('i');

                    let formData = new FormData(form);
                    formData.append('taoh_action', 'profile_tags_update');
                    formData.append('form_id', formId);
                    formData.append('taoh_ptoken', my_ptoken);
                    selectedTagElements.each(function () {
                        formData.append('tags[]', this.value);
                    });

                    submit_btn_icon.removeClass('fa-save').addClass('fa-spinner fa-spin');
                    submit_btn.prop('disabled', true);

                    $.ajax({
                        url: profile_tags_form.attr('action'),
                        type: 'post',
                        data: formData,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function (response) {
                            if (response.status) {
                                let profileStage = parseInt(response?.data?.profile_stage, 10) || 0;

                                let successMessage = 'Your profile flags have been successfully completed.';
                                if(profileStage > 0 && profileStage < 3) {
                                    successMessage += ' You are now eligible to join the event or explore the site, and you can complete the remaining setup at your convenience.';
                                }
                                taoh_set_success_message(successMessage, false, 'toast-middle', [
                                    {
                                        text: 'OK',
                                        action: () => {
                                            window.location.reload();
                                        },
                                        class: 'dojo-v1-btn float-right mt-3 mb-3'
                                    }
                                ]);

                                // $.confirm({
                                //     title: 'Success',
                                //     content: successMessage,
                                //     type: 'green',
                                //     buttons: {
                                //         confirm: {
                                //             text: 'OK',
                                //             action: function () {
                                //                 window.location.reload();
                                //             }
                                //         }
                                //     }
                                // });
                            } else {
                                taoh_set_error_message('Failed to update your profile info! Try Again', false, 'toast-middle', [
                                    {
                                        text: 'OK',
                                        action: () => {},
                                        class: 'dojo-v1-btn float-right mt-3 mb-3'
                                    }
                                ]);
                                // $.alert('Failed to update your profile info! Try Again');
                            }
                            submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                            submit_btn.prop('disabled', false);
                        },
                        error: function (xhr) {
                            console.log('Profile Info create error:', xhr.status);
                            submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                            submit_btn.prop('disabled', false);
                        }
                    });
                }
            };

            $(this).validate(options);
        });

        $('#profile_experience_form, #profile_education_form').each(function () {
            const formId = this.id;

            let options = {
                errorPlacement: function (error, element) {
                    if (element.closest('.btn-group').length) {
                        error.insertAfter(element.closest('.btn-group'));
                    } else if (element.hasClass('ts-hidden-accessible')) {
                        error.insertAfter(element.next('.ts-wrapper'));
                    } else {
                        element.after(error);
                    }
                },
                submitHandler: function (form) {
                    let profile_form = $(form);

                    let formData = new FormData(form);
                    formData.append('taoh_action', 'profile_update');
                    formData.append('form_id', formId);
                    formData.append('taoh_ptoken', my_ptoken);

                    let submit_btn;
                    if (formData.has('emp_btnDelete')) {
                        submit_btn = profile_form.find('button[name="emp_btnDelete"]');
                    } else if (formData.has('edu_btnDelete')) {
                        submit_btn = profile_form.find('button[name="edu_btnDelete"]');
                    } else {
                        submit_btn = profile_form.find('.s-btn[type="submit"]');
                    }

                    let submit_btn_icon = submit_btn.find('i');
                    let submit_btn_icon_class = submit_btn_icon.attr('class');

                    submit_btn_icon.removeClass(submit_btn_icon_class).addClass('fa fa-spinner fa-spin');
                    submit_btn.prop('disabled', true);

                    $.ajax({
                        url: profile_form.attr('action'),
                        type: 'post',
                        data: formData,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function (response) {
                            if (response.status) {
                                let profileStage = parseInt(response?.data?.profile_stage, 10) || 0;

                                let successMessage = `Your ${formId === 'profile_experience_form' ? 'experience' : 'education'} details have been successfully completed.`;
                                if(profileStage > 0 && profileStage < 3) {
                                    successMessage += ' You are now eligible to join the event or explore the site, and you can complete the remaining setup at your convenience.';
                                }
                                taoh_set_success_message(successMessage, false, 'toast-middle', [
                                    {
                                        text: 'OK',
                                        action: () => {
                                            window.location.reload();
                                        },
                                        class: 'dojo-v1-btn float-right mt-3 mb-3'
                                    }
                                ]);

                                // $.confirm({
                                //     title: 'Success',
                                //     content: successMessage,
                                //     type: 'green',
                                //     buttons: {
                                //         confirm: {
                                //             text: 'OK',
                                //             action: function () {
                                //                 window.location.reload();
                                //             }
                                //         }
                                //     }
                                // });
                            } else {
                                taoh_set_error_message('Failed to update your profile info! Try Again', false, 'toast-middle', [
                                    {
                                        text: 'OK',
                                        action: () => {},
                                        class: 'dojo-v1-btn float-right mt-3 mb-3'
                                    }
                                ]);
                                // $.alert('Failed to update your profile info! Try Again');
                            }
                            submit_btn_icon.removeClass('fa fa-spinner fa-spin').addClass(submit_btn_icon_class);
                            submit_btn.prop('disabled', false);
                        },
                        error: function (xhr) {
                            console.log('Profile Info create error:', xhr.status);
                            submit_btn_icon.removeClass('fa fa-spinner fa-spin').addClass(submit_btn_icon_class);
                            submit_btn.prop('disabled', false);
                        }
                    });
                }
            };

            $(this).validate(options);
        });

        $('#profile_form_5, #profile_form_6').each(function () {
            const formId = this.id;

            let options = {
                errorPlacement: function (error, element) {
                    if (element.closest('.btn-group').length) {
                        error.insertAfter(element.closest('.btn-group'));
                    } else if (element.hasClass('ts-hidden-accessible')) {
                        error.insertAfter(element.next('.ts-wrapper'));
                    } else {
                        element.after(error);
                    }
                },
                submitHandler: function (form) {
                    let profile_form = $(form);
                    let submit_btn = profile_form.find('button[type="submit"]');
                    let submit_btn_icon = submit_btn.find('i');

                    let formData = new FormData(form);
                    formData.append('taoh_action', 'profile_update');
                    formData.append('form_id', formId);
                    formData.append('taoh_ptoken', my_ptoken);

                    submit_btn_icon.removeClass('fa-save').addClass('fa-spinner fa-spin');
                    submit_btn.prop('disabled', true);

                    $.ajax({
                        url: profile_form.attr('action'),
                        type: 'post',
                        data: formData,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function (response) {
                            if (response.status) {
                                let profileStage = parseInt(response?.data?.profile_stage, 10) || 0;

                                let successMessage = `Your ${formId === 'profile_form_5' ? 'about, hobbies & interests details' : 'privacy settings'} have been successfully completed.`;
                                if(profileStage > 0 && profileStage < 3) {
                                    successMessage += ' You are now eligible to join the event or explore the site, and you can complete the remaining setup at your convenience.';
                                }
                                taoh_set_success_message(successMessage, false, 'toast-middle', [
                                    {
                                        text: 'OK',
                                        action: () => {
                                            window.location.reload();
                                        },
                                        class: 'dojo-v1-btn float-right mt-3 mb-3'
                                    }
                                ]);

                                // $.confirm({
                                //     title: 'Success',
                                //     content: 'Your profile information was saved successfully.',
                                //     type: 'green',
                                //     buttons: {
                                //         confirm: {
                                //             text: 'OK',
                                //             action: function () {
                                //                 window.location.reload();
                                //             }
                                //         }
                                //     }
                                // });
                            } else {
                                taoh_set_error_message('Failed to update your profile info! Try Again', false, 'toast-middle', [
                                    {
                                        text: 'OK',
                                        action: () => {},
                                        class: 'dojo-v1-btn float-right mt-3 mb-3'
                                    }
                                ]);
                                // $.alert('Failed to update your profile info! Try Again');
                            }
                            submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                            submit_btn.prop('disabled', false);
                        },
                        error: function (xhr) {
                            console.log('Profile Info create error:', xhr.status);
                            submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                            submit_btn.prop('disabled', false);
                        }
                    });
                }
            };

            $(this).validate(options);
        });

        // Tab activation with mobile toggle label update
        $('#main-tabs').on('shown.bs.tab', '.nav-link', function (e) {
            let $clicked = $(e.target);
            $('#main-tabs .nav-link').removeClass('active');
            $clicked.addClass('active');

            if ($('#toggleMenu').length) {
                $('#toggleMenu').html('<i class="fa fa-bars mr-3" aria-hidden="true"></i>' + $clicked.text().trim());
            }

            // Set initial label on mobile
            let initial = $('#main-tabs .nav-link.active').first();
            if (initial.length && $('#toggleMenu').length) {
                $('#toggleMenu').html('<i class="fa fa-bars mr-3" aria-hidden="true"></i>' + initial.text().trim());
            }
        });

        // Auto open tab based on URL tabIndex
        const mainTabs = $('#main-tabs > li.nav-item');
        if (tabIndex >= 0 && tabIndex < mainTabs.length) {
            // if (tabIndex === 2) {
            //     const selectedTagElements = $('#myTabContent input[type="checkbox"]:checked');
            //     if (selectedTagElements.length > 0) {
            //         const profileFlagSubTabs = $('#submenu2 li.nav-item');
            //         if (profileFlagSubTabs.length) {
            //             profileFlagSubTabs.first().find('a.nav-link').tab('show');
            //             return; // Exit after showing sub-tab
            //         }
            //     }
            // }

            mainTabs.eq(tabIndex).find('a.nav-link').tab('show');
        }

        updateProfileTagSelection();
    });

    $(document).on('change', '#myTabContent input[type="checkbox"]', function () {
        const maxAllowed = 5;
        const selectedTagElementsCount = $('#myTabContent input[type="checkbox"]:checked').length;

        if (selectedTagElementsCount > maxAllowed) {
            $(this).prop('checked', false);
            taoh_set_error_message(`You can only select up to ${maxAllowed} profile tags.`, false, 'toast-middle', [
                {
                    text: 'OK',
                    action: () => {},
                    class: 'dojo-v1-btn float-right mt-3 mb-3'
                }
            ]);
            // $.alert(`You can only select up to ${maxAllowed} profile tags.`);
            return false;
        } else {
            updateProfileTagSelection();
        }
    });

    $(document).on('click', '.add_edit_emp', function () {
        let data_emp = $(this).attr("data-employee");
        let emp_add_edit = $(this).attr("data-add-edit");
        // let emp_delete = $(this).attr("data-emp-delete");
        let emp_edt_delete = $(this).attr("data-emp-edit-delete");

        $('#add_edit_employee .modal-title').html(emp_add_edit + ' Experience');
        $('#add_edit_employee .modal-body').html('<img class="loader" style="width:10% !important; display: block; background: none; top: auto; height:auto;" src="<?php echo TAOH_LOADER_GIF; ?>" />');
        $('#add_edit_employee .emp_del_btn').empty();
        $('#add_edit_employee').modal('show');

        jQuery.post(_taoh_site_ajax_url, {
            'taoh_action': 'add_edit_employee',
            'id': data_emp,
            'emp_edit_del_id': emp_edt_delete,
            'add_or_edit': emp_add_edit,
            'post_data': '<?php echo json_encode($emp_list); ?>',
        }, function (response) {
            const res = response.split('~');
            $('#add_edit_employee .modal-body').html(res[0]);
            $('#add_edit_employee .emp_del_btn').html(res[1]);
        }).fail(function () {
            console.log("Network issue!");
        });
    });

    $(document).on('click', '.add_edit_edu', function () {
        let data_edu = $(this).attr("data-education");
        let edu_add_edit = $(this).attr("data-add-edit");
        // let edu_delete = $(this).attr("data-edu-delete");
        let edu_edt_delete = $(this).attr("data-edu-edit-delete");

        $('#add_edit_education .modal-title').html(edu_add_edit + ' Education');
        $('#add_edit_education .modal-body').html('<img class="loader" style="width:10% !important; display: block; background: none; top: auto; height:auto;" src="<?php echo TAOH_LOADER_GIF; ?>" />');
        $('#add_edit_education .edu_del_btn').empty();
        $('#add_edit_education').modal('show');

        jQuery.post(_taoh_site_ajax_url, {
            'taoh_action': 'add_edit_education',
            'id': data_edu,
            'edu_edit_del_id': edu_edt_delete,
            'add_or_edit': edu_add_edit,
            'post_data': '<?php echo json_encode($edu_list); ?>',
        }, function (response) {
            const res = response.split('~');
            $('#add_edit_education .modal-body').html(res[0]);
            $('#add_edit_education .edu_del_btn').html(res[1]);
        }).fail(function () {
            console.log("Network issue!");
        });
    });

    $(document).on('change', "#emp_year_starts", function () {
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

    $(document).on('change', "#edu_year_starts", function () {
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

    $('#sidebarMenu').on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        const target = $(e.target).attr('href');
        const tabPane = document.querySelector(target);
        if (tabPane) {
            tabPane.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    /* Sidebar Menu */
    const sidebarMenu = document.getElementById('main-tabs');
    const toggleBtn = document.getElementById('toggleMenu');
    const sidebar = document.getElementById('sidebarMenu');

    toggleBtn.addEventListener('click', function () {
        sidebar.classList.toggle('show');
    });

    sidebarMenu.addEventListener('click', function (e) {
        const target = e.target.closest('a.nav-link');
        if (target) {
            const text = target.textContent.trim();
            toggleBtn.innerHTML = '<i class="fa fa-bars mr-3" aria-hidden="true"></i>' + text;

            if (window.innerWidth <= 768) {
                sidebar.classList.remove('show');
            }
        }
    });

    const activeTab = document.querySelector('#main-tabs a.nav-link.active');
    if (activeTab) {
        toggleBtn.innerHTML = '<i class="fa fa-bars mr-3" aria-hidden="true"></i>' + activeTab.textContent.trim();
    }

    /* /Sidebar Menu */


    function updateProfileTagSelection() {
        const submenu = $('#submenu2 .submenu-list');
        const selectionStatus = $('#selection-status');
        const selectedTagElements = $('#myTabContent input[type="checkbox"]:checked');

        submenu.empty();
        if(selectedTagElements.length > 0) {
            selectionStatus.text(`You have selected ${selectedTagElements.length} out of 5 profile tags!`)

            selectedTagElements.each(function () {
                const selectedTagElem = $(this);
                const tagId = selectedTagElem.attr('data-key');
                const tagValue = selectedTagElem.val();

                // Append only if not already added
                if ($('#main-tabs a[href="#' + tagId + '"]').length === 0) {
                    submenu.append(`
                          <li class="nav-item">
                            <a class="nav-link py-2 child-tab" data-toggle="tab" href="#${tagId}" role="tab"><i class="fa fa-level-right mr-2 mb-2" aria-hidden="true"></i> ${tagValue}</a>
                          </li>
                        `);
                }
            });
        }
    }

    function check_still_working(exp_id) {
        const endDateSelect = document.getElementById('emp_end_month_' + exp_id);
        const endYearSelect = document.getElementById('emp_year_end_' + exp_id);
        if ($('#still_working_' + exp_id).is(":checked")) {
            $('.emp_end_month_block_' + exp_id).hide();
            endDateSelect.removeAttribute('required');
            endYearSelect.removeAttribute('required');
        } else {
            $('.emp_end_month_block_' + exp_id).show();
            endDateSelect.setAttribute('required', 'true');
            endYearSelect.setAttribute('required', 'true');
        }
        // currentCheckbox.prop('checked', !currentCheckbox.prop('checked'));
    }

    function deleteAccount() {
        const deleteAccountBody = $('#deleteAccountBody');

        if ($('#delete-terms').is(':checked')) {
            deleteAccountBody.html(`
                <h3 class="fs-22 text-danger fw-bold">Delete Request Initiated</h3>
                <h4 class="pb-3 pt-3 fs-20">Please Wait...</h4>
            `);

            jQuery.post(_taoh_site_ajax_url, {
                'taoh_action': 'taoh_delete_account',
            }, function () {
                deleteAccountBody.html(`
                    <h3 class="fs-22 text-danger fw-bold">Delete Account</h3>
                    <h4 class="pb-3 pt-3 fs-20">Delete request received, please check your inbox and follow the instruction</h4>
                `);
                localStorage.removeItem("isCodeSent");
                setTimeout(function () {
                    deleteAccountBody.html(`
                        <h3 class="fs-22 text-danger fw-bold">You are logging out</h3>
                        <h4 class="pb-3 pt-3 fs-20">Delete request received, please check your inbox and follow the instruction</h4>
                    `);
                    window.location = "<?php echo TAOH_LOGOUT_URL; ?>";
                }, 2000);
            });
        } else {
            taoh_set_error_message('To proceed with account deletion, please confirm by selecting the checkbox above.', false, 'toast-middle', [
                {
                    text: 'OK',
                    action: () => {},
                    class: 'dojo-v1-btn float-right mt-3 mb-3'
                }
            ]);
            // $.alert("To proceed with account deletion, please confirm by selecting the checkbox above.");
        }
    }

    function clearTomSelect(selector) {
        const ts = document.querySelector(selector)?.tomselect;
        if (ts) {
            ts.clear();              // Clears any selected value
            ts.clearOptions();       // Removes all dropdown options
            ts.setTextboxValue('');  // Clears typed input
        }
    }

</script>

<?php
taoh_get_footer();