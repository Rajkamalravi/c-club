<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/raj/assets/icons/icons.php';
taoh_get_header();


$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
$user_info_obj = $taoh_user_is_logged_in ? taoh_user_all_info() : null;

$valid_dir_viewer = $taoh_user_is_logged_in && $user_info_obj->profile_complete && $user_info_obj->unlist_me_dir !== 'yes';

?>

<style>
    .setup-title {
        font-size: clamp(24px, 3vw + 1rem, 30px);
        color: #444444;
        font-weight: 400;
        line-height: 1.15;
    }

    .setup-desc {
        font-size: 16px;
        color: #000000;
        font-weight: 400;
        line-height: 1.5;
        width: 100%;
        max-width: 968px;
    }
    .employer-branding-form .divider {
        height: 1px;
        background-color: #444444;
        width: 100%;
        max-width: 742px;
        border: none;
    }

    .employer-branding-form .sub-title {
        font-size: 19px;
        font-weight: 400;
        color: #2557A7;
    }
    .employer-branding-form .view {
        color: #2557A7;
        text-decoration: underline;
        font-size: 12px;
    }
    label .req {
        color: #F72222;
    }

    .employer-branding-form form label {
        font-size: 16px;
        color: #000000;
        font-weight: 400;
        line-height: 1.5;
        margin-bottom: 0;
    }
    .employer-branding-form form input, .employer-branding-form form select {
        height: clamp(50px, 60px, 77px);
        border: 1.5px solid #D3D3D3 !important;
        transition: all 500ms ease;
    }
    .employer-branding-form form input:hover, .employer-branding-form form select:hover {
        background: transparent;
        border-color: #2557A7 !important;
    }
    .employer-branding-form form input:focus, .employer-branding-form form select:focus {
        border: 0.5px solid #D3D3D3 !important;
    }
    .employer-branding-form form .guide-text {
        font-size: 10px;
        color: #444444;
        font-weight: 400;
        line-height: 24px;
    }

    /* for removing default dropdown icon */
    /* .employer-branding-form form select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
    } */

    /* .employer-branding-form form .select-icon {
        position: relative;
    }
    .employer-branding-form form .select-icon svg {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
    } */

    .employer-branding-form form .add-more, .employer-branding-form form .setup-btn {
        font-size: 17.7px;
        background-color: #2557A7;
        line-height: 45px;
        color: #fff;
    }
    .employer-branding-form form .add-more span {
        font-size: 19px;
    }

</style>


<div class="bg-white employer-branding-form">
    <div class="section row py-5 mx-0" style="background-color: #DFECF0;">
        <div class="container col-xl-11 mx-auto my-4">
            <h1 class="setup-title">Setup Your Organization Page</h1>
            <div class="divider my-4"></div>
            <p class="setup-desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna 1 aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
        </div>
    </div>

    <div class="row mx-0">
        <div class="container col-xl-11 mx-auto my-4">
            <form action="#">
                <!-- Basic Organization Details -->
                <div>
                    <div class="d-flex justify-content-between align-items-center flex-wrap-reverse pb-4 mb-4 mt-5" style="border-bottom: 1px solid #D3D3D3;">
                        <h4 class="sub-title">Basic Organization Details</h4>
                        <a href="#" class="d-flex align-items-center justify-content-end view" style="gap: 6px;">
                            <span class="">View Original Page</span>
                            <?= icon('external-link', '#2557A7', 12) ?>
                        </a>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="organization_name">Enter Organization Name<span class="req">*</span></label>
                                <p class="guide-text">As per your legal definition</p>
                                <input type="text" name="" id="" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="organization_tag_line">Enter Organization Tag Line</label>
                                <p class="guide-text">Optional</p>
                                <input type="text" name="" id="" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="organization_website">Enter Organization Website<span class="req">*</span></label>
                                <p class="guide-text">Please use proper format https://www.yoururl.com</p>
                                <input type="text" name="" id="" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="organization_tag_line">Enter Company Size (0-100, 100-200, More than 200 employees)<span class="req">*</span></label>
                                <p class="guide-text">Enter Number of Employees</p>
                                <input type="text" name="" id="" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="organization_website">Select Your Organization Domain<span class="req">*</span></label>
                                <p class="guide-text">Select your primary industry !</p>
                                <div class="select-icon">
                                    <select name="" id="" class="form-control pr-2">
                                        <option value="" disabled selected>Select</option>
                                        <option value=""></option>
                                    </select>
                                    <!-- <svg class="select-icon-svg" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16 0C11.7565 0 7.68687 1.68571 4.68629 4.68629C1.68571 7.68687 0 11.7565 0 16C0 20.2435 1.68571 24.3131 4.68629 27.3137C7.68687 30.3143 11.7565 32 16 32C20.2435 32 24.3131 30.3143 27.3137 27.3137C30.3143 24.3131 32 20.2435 32 16C32 11.7565 30.3143 7.68687 27.3137 4.68629C24.3131 1.68571 20.2435 0 16 0ZM8.4375 15.0625C7.85 14.475 7.85 13.525 8.4375 12.9438C9.025 12.3625 9.975 12.3563 10.5562 12.9438L15.9937 18.3813L21.4312 12.9438C22.0187 12.3563 22.9688 12.3563 23.55 12.9438C24.1312 13.5312 24.1375 14.4812 23.55 15.0625L17.0625 21.5625C16.475 22.15 15.525 22.15 14.9438 21.5625L8.4375 15.0625Z" fill="#B4B1B1"/>
                                    </svg> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Setup about Section -->
                 <div class="mt-4 pt-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap-reverse pb-4 mb-4" style="border-bottom: 1px solid #D3D3D3;">
                        <h4 class="sub-title">Setup about Section</h4>
                        <a href="#" class="d-flex align-items-center justify-content-end view" style="gap: 6px;">
                            <span class="">View Original Page</span>
                            <?= icon('external-link', '#2557A7', 12) ?>
                        </a>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="emphasize_about">Enter what do you want to emphasize in your about Section<span class="req">*</span></label>
                                <p class="guide-text d-flex align-items-center" style="gap: 6px;">
                                    <span>refer original page</span>
                                    <a href="#" class="d-flex align-items-center justify-content-end view" style="gap: 6px;">
                                        <span class="">View Original Page</span>
                                        <?= icon('external-link', '#2557A7', 12) ?>
                                    </a>
                                </p>
                                <div class="mt-3 col-lg-9 px-0">
                                    <textarea class="summernote" id="" rows="10" cols="80" required="" aria-required="true" style="display: none;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="about">Write your about us section !</label>
                                <p class="guide-text d-flex align-items-center" style="gap: 6px;">
                                    <span>refer original page</span>
                                    <a href="#" class="d-flex align-items-center justify-content-end view" style="gap: 6px;">
                                        <span class="">View Original Page</span>
                                        <?= icon('external-link', '#2557A7', 12) ?>
                                    </a>
                                </p>
                                <div class="mt-3 col-lg-9 px-0">
                                    <textarea class="summernote" id="" rows="10" cols="80" required="" aria-required="true" style="display: none;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="images">Upload Images</label>
                                <p class="guide-text">
                                    You can upload a maximum of 6 images
                                </p>
                                <input type="file" name="" id="" class="form-control" style="padding: 15px 10px;">
                            </div>

                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="video_url">Enter Video url if any</label>
                                <p class="guide-text">
                                    explainer or any video url that explains what you do, culture etc
                                </p>
                                <input type="text" name="" id="" class="form-control">
                            </div>
                        </div>
                    </div>

                 </div>
                <!-- Setup Culture Section -->
                 <div class="mt-4 pt-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap-reverse pb-4 mb-4" style="border-bottom: 1px solid #D3D3D3;">
                        <h4 class="sub-title">Setup Culture Section</h4>
                        <a href="#" class="d-flex align-items-center justify-content-end view" style="gap: 6px;">
                            <span class="">View Original Page</span>
                            <?= icon('external-link', '#2557A7', 12) ?>
                        </a>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="emphasize_culture">Enter what do you want to emphasize in your culture Section<span class="req">*</span></label>
                                <p class="guide-text d-flex align-items-center" style="gap: 6px;">
                                    <span>refer original page</span>
                                    <a href="#" class="d-flex align-items-center justify-content-end view" style="gap: 6px;">
                                        <span class="">View Original Page</span>
                                        <?= icon('external-link', '#2557A7', 12) ?>
                                    </a>
                                </p>
                                <div class="mt-3 col-lg-9 px-0">
                                    <textarea class="summernote" id="" rows="10" cols="80" required="" aria-required="true" style="display: none;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="culture_images">Upload Images</label>
                                <p class="guide-text">
                                    You can upload a maximum of 6 images
                                </p>
                                <input type="file" name="" id="" class="form-control" style="padding: 15px 10px;">
                            </div>

                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="culture_video_url">Enter Video url if any</label>
                                <p class="guide-text">
                                    explainer or any video url that explains what you do, culture etc
                                </p>
                                <input type="text" name="" id="" class="form-control">
                            </div>
                        </div>
                    </div>

                 </div>
                <!-- Setup Location Section -->
                 <div class="mt-4 pt-3">
                    <div class="d-flex justify-content-between align-items-start flex-wrap-reverse pb-3 mb-4" style="border-bottom: 1px solid #D3D3D3;">
                        <div>
                            <h4 class="sub-title">Setup Location Section</h4>
                            <p class="guide-text">You can add multiple locations using the add location button !</p>
                        </div>
                        <a href="#" class="d-flex align-items-center justify-content-end view" style="gap: 6px;">
                            <span class="">View Original Page</span>
                            <?= icon('external-link', '#2557A7', 12) ?>
                        </a>
                    </div>

                    <div class="col-12 py-3 px-3" style="border: 1px solid #ddd; border-radius: 8px;">
                        <!-- address input field area -->
                        <div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="Country">Enter Country<span class="req">*</span></label>
                                        <input type="text" name="" id="" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="address_line_1">Enter Address Line 1<span class="req">*</span></label>
                                        <input type="text" name="" id="" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="address_line_2">Enter Address Line 2<span class="req">*</span></label>
                                        <input type="text" name="" id="" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="address_line_3">Enter Address Line 3<span class="req">*</span></label>
                                        <input type="text" name="" id="" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="state_or_province*">Enter State or Province<span class="req">*</span></label>
                                        <input type="text" name="" id="" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="zip_code">Enter Zip Code<span class="req">*</span></label>
                                        <input type="text" name="" id="" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- add more button -->
                        <div class="row mt-4">
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group">
                                    <button type="button" class="btn add-more"><span>+</span> Add Another Location</button>
                                </div>
                            </div>
                        </div>
                    </div>

                 </div>

                  <!-- Setup Location Section -->
                <div class="mt-4 pt-3">
                    <div class="d-flex justify-content-between align-items-start flex-wrap-reverse pb-3 mb-4" style="border-bottom: 1px solid #D3D3D3;">
                        <div>
                            <h4 class="sub-title">Setup Benefits and Perks Section</h4>
                            <p class="guide-text">Just select the Benefits that you are giving out ! we will setup the section for you ! You can select multiple options.</p>
                        </div>
                        <a href="#" class="d-flex align-items-center justify-content-end view" style="gap: 6px;">
                            <span class="">View Original Page</span>
                            <?= icon('external-link', '#2557A7', 12) ?>
                        </a>
                    </div>

                    <div class="row mt-2">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="benefits">Select Benefits which you are giving out !</label>
                                <select name="" id="" class="form-control">
                                    <option value="" disabled selected>Select</option>
                                </select>
                            </div>

                        </div>
                    </div>


                </div>


                <button type="button" class="btn setup-btn my-5">Set up Organization Page !</button>


            </form>
        </div>
    </div>

</div>

<?php
taoh_get_footer();