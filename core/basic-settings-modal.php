<?php
require_once TAOH_SITE_PATH_ROOT . '/core/form_fields.php';

$bsm_curr_page = taoh_parse_url(0);

if (taoh_user_is_logged_in() && $bsm_curr_page !== "basic-settings" && $bsm_curr_page !== "settings") {
    $ft_bs_data = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null;
    if (!empty($ft_bs_data?->ptoken)) {
        $ptoken = $ft_bs_data->ptoken;
        $ft_bs_fname = $ft_bs_lname = $ft_bs_chat_name = '';
        if (($ft_bs_data->profile_complete ?? '') == 1 || $ft_bs_data->created_via === 'social') {
            $ft_bs_fname = $ft_bs_data->fname ?? '';
            $ft_bs_lname = $ft_bs_data->lname ?? '';
            $ft_bs_chat_name = $ft_bs_data->chat_name ?? '';
        }
        $ft_bs_email = $ft_bs_data->email ?? '';
        $profile_type = $ft_bs_data->type ?? '';
        $coordinates = $ft_bs_data->coordinates ?? '';
        $location = $ft_bs_data->full_location ?? '';
        $country_code = $ft_bs_data->country_code ?? '';
        $country_name = $ft_bs_data->country_name ?? '';
        ?>
        <!-- Basic Profile Settings Modal -->
        <div class="settings-modal modal fade" id="basicSettingsModal" tabindex="-1" role="dialog" aria-labelledby="basicSettingsModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header px-3 px-lg-5 py-4">
                        <h5 class="modal-title" id="basicSettingsModalLabel"><span class="text-sm">"Just a Minute to Go!"</span> <br> Help us personalize your experience — complete your profile</h5>
                        <button type="button" class="btn basicSettingsModalCloseBtn" data-dismiss="modal" aria-label="Close">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.4172 3.41719C20.1984 2.63594 20.1984 1.36719 19.4172 0.585938C18.6359 -0.195312 17.3672 -0.195312 16.5859 0.585938L10.0047 7.17344L3.41719 0.592187C2.63594 -0.189063 1.36719 -0.189063 0.585938 0.592187C-0.195312 1.37344 -0.195312 2.64219 0.585938 3.42344L7.17344 10.0047L0.592188 16.5922C-0.189062 17.3734 -0.189062 18.6422 0.592188 19.4234C1.37344 20.2047 2.64219 20.2047 3.42344 19.4234L10.0047 12.8359L16.5922 19.4172C17.3734 20.1984 18.6422 20.1984 19.4234 19.4172C20.2047 18.6359 20.2047 17.3672 19.4234 16.5859L12.8359 10.0047L19.4172 3.41719Z" fill="black"/>
                            </svg>
                        </button>
                    </div>

                    <div class="modal-body px-3 px-lg-5 pb-0">
                        <form action="<?= TAOH_ACTION_URL . '/settings'; ?>" method="post" class="row g-3" name="profile_form_1" id="profile_form_1" enctype="multipart/form-data" autocomplete="off">
                            <div class="col-md-12">
                                <label for="avatarSelect" class="form-label">My avatar <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap">
                                    <div class="profile-image" id="move_avatar" style="<?= !empty($ft_bs_data->avatar_image) ? 'display:none' : '' ?>">
                                        <?php echo avatar_select(@$ft_bs_data->avatar); ?>
                                    </div>
                                    <span class="text-danger" id="avatar-error"></span>
                                    <div class="avatar-container" style="<?= empty($ft_bs_data->avatar_image) ? 'display:none' : '' ?>">
                                        <div class="avatar_settings">
                                            <?php
                                            if (!empty($ft_bs_data->avatar_image)) {
                                                echo '<img src="' . $ft_bs_data->avatar_image . '" alt="Avatar">';
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
                                                <input type="file" class="custom-file-input cursor-pointer file_my_validation" name="profile_picture" id="profile_picture" accept=".jpg, .jpeg, .png">
                                                <label class="custom-file-label profile_picture_label" for="profile_picture">Choose file</label>
                                            </div>
                                        </div>
                                        <input type="hidden" value="<?= !empty($ft_bs_data->avatar_image ?? '') ? $ft_bs_data->avatar_image : '' ?>" name="avatar_image">
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    <input type="hidden" name="taoh_ptoken" id="taoh_ptoken" value="<?= $ft_bs_data->ptoken ?? ''; ?>">
                                    <input type="hidden" name="current_profile_stage" class="current_profile_stage" value="<?= $ft_bs_data->profile_stage ?? ''; ?>">

                                    <div class="col-md-6">
                                        <label for="fname" class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="fname" id="fname" value="<?= $ft_bs_fname ?? ''; ?>" placeholder="First Name" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="lname" class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="lname" id="lname" value="<?= $ft_bs_lname ?? ''; ?>" placeholder="Last Name" required>
                                    </div>

                                    <input type="hidden" name="chat_name" id="chat_name" value="<?= $ft_bs_chat_name ?? ''; ?>">

                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email" id="email" value="<?= $ft_bs_email ?? ''; ?>" placeholder="Email" required>
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
                                                echo '<option value="'.$country_code.'">'.$country_name.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="my_city" class="form-label">My City <span class="text-danger">*</span></label>
                                        <select class="my_city" name="coordinates" id="my_city" autocomplete="new-password" required>
                                            <?php
                                            if($coordinates && $location) {
                                                echo '<option value="'.$coordinates.'">'.$location.'</option>';
                                            }
                                            ?>
                                        </select>
                                        <label class="error my_city_order_error" for="my_city" style="display: none;"></label>
                                    </div>

                                    <input type="hidden" id="coordinateLocation" name="full_location" value="<?= $location ?? ''; ?>">
                                    <input type="hidden" id="geohash" name="geohash" value="<?= $ft_bs_data->geohash ?? ''; ?>">

                                    <div class="col-md-6">
                                        <label for="local_timezone" class="form-label">Timezone <span class="text-danger">*</span></label>
                                        <input type="text" name="local_timezone" id="local_timezone" value="<?= $ft_bs_data->local_timezone ?? ''; ?>" placeholder="Type to select" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="profile_company" class="form-label">Enter your organization name, or N/A <span class="text-danger">*</span></label>
                                        <select class="profile_company" name="company:company[]" id="profile_company" placeholder="Type to select" required>
                                            <?php
                                            if ($ft_bs_data->company) {
                                                foreach ($ft_bs_data->company as $key => $value) {
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
                                            if ($ft_bs_data->title) {
                                                foreach ($ft_bs_data->title as $key => $value) {
                                                    list ($pre, $post) = explode(':>', $value);
                                                    echo '<option value="' . $key . '" data-slug="' . $pre . '" selected>' . $post . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check mt-3">
                                            <input type="checkbox" class="form-check-input" name="currently_working_on" id="currently_working_on" value="1" <?= (($ft_bs_data->currently_working_on ?? 0) == 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label text-label ml-3 mt-2" for="currently_working_on">I currently work here</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn s-btn"><i class="fa fa-save mr-1"></i> Save</button>
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer justify-content-start px-3 px-lg-5 border-0 pb-4"></div>
                </div>
            </div>
        </div>
        <!-- /Basic Profile Settings Modal -->
        <?php
    }
}
