<?php
taoh_get_header();


$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
$user_info_obj = $taoh_user_is_logged_in ? taoh_user_all_info() : null;

$valid_dir_viewer = $taoh_user_is_logged_in && $user_info_obj->profile_complete && $user_info_obj->unlist_me_dir !== 'yes';

?>

<div class="bg-white">
    <header class="container mx-auto row d-flex flex-wrap-reverse" style="border-bottom: 1px solid #D3D3D3;">
        <div class="col-md-9 d-flex align-items-center" style="gap: 0.5rem;">
            <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/alum_logo.png" alt="logo"  style="width: 174px; height: 174px; object-fit: contain;"/>
            <h1 style="font-size: clamp(16px, 2vw + 1rem, 29px); font-weight: 600; color: #000000;">CHRIST UNIVERSITY</h1>
        </div>
        <div class="col-md-3 d-flex align-items-center justify-content-end">
            <button type="button" class="btn px-5 text-nowrap" style="background: #2557A7; height: 49px; border-radius: 12px;">
                <a href="#" style="color: #fff; font-size: clamp(16px, 2vw + 17px, 19px);">SIGN UP</a>
            </button>
        </div>
    </header>


</div>

<?php
taoh_get_footer();