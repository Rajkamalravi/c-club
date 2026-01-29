<?php
taoh_get_header();


$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
$user_info_obj = $taoh_user_is_logged_in ? taoh_user_all_info() : null;

$valid_dir_viewer = $taoh_user_is_logged_in && $user_info_obj->profile_complete && $user_info_obj->unlist_me_dir !== 'yes';

?>

<style>

.owl-stage-outer .owl-item:hover .item {
    width: auto !important;
    transform: none !important;
    transition: none !important;
}

.owl-dots {
    display: none;
}

</style>

<div class="bg-white">
    <div class="mx-auto py-4 px-4" style="max-width: 1028px;">
        <div class="d-flex justify-content-end">
            <a href="<?php echo TAOH_SITE_URL_ROOT;?>/club/news_feed" class="d-flex align-items-center" style="gap: 0.8rem;">
                <svg width="23" height="14" viewBox="0 0 23 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0.421163 6.01171C-0.140388 6.55833 -0.140388 7.44604 0.421163 7.99266L6.17144 13.59C6.733 14.1367 7.64495 14.1367 8.2065 13.59C8.76805 13.0434 8.76805 12.1557 8.2065 11.6091L4.90908 8.39934H21.5624C22.3576 8.39934 23 7.77401 23 7C23 6.22599 22.3576 5.60066 21.5624 5.60066H4.90908L8.2065 2.39091C8.76805 1.84429 8.76805 0.956583 8.2065 0.409964C7.64495 -0.136655 6.733 -0.136655 6.17144 0.409964L0.421163 6.00734V6.01171Z" fill="#2557A7"/>
                </svg>
                <span style="color: #2557A7; font-size: clamp(16px, 2vw + 16px, 24px);">
                    BACK
                </span>
            </a>
        </div>

        <div>
            <div class="row d-flex justify-content-between flex-wrap align-items-end pt-4 pb-3">
                <h1 class="col-md-6" style="font-size: clamp(16px, 2vw + 16px, 22px); font-weight: 400; color: #000000;">
                    Happenings on Virtual Reunion
                </h1>
                <div class="col-md-6 d-flex flex-column align-items-end">
                    <h6 style="font-size: 12px; font-weight: 400; color: #9A9A9A;">Posted</h6>
                    <h4 style="font-size: clamp(16px, 2vw + 16px, 22px) ; font-weight: 400; color: #000000;">15-October-2024</h4>
                </div>
            </div>

            <div class="">
                <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/job_search.png" alt="profile" style="width: 100%; min-height: 400px; height: auto; max-height: 648px; object-fit: cover;" />
            </div>
        </div>

        <div class="pt-5 pb-4 d-flex align-items-center" style="gap: 1rem; border-bottom: 2px solid #D3D3D3;">
            <div>
                <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/job_search.png" alt="profile" style="width: 47px; height: 47px;border-radius: 50%; border: 2px solid #ddd; object-fit: contain;" />
            </div>
            <div>
                <h6 style="font-size: 12px; font-weight: 400; color: #9A9A9A;">Posted</h6>
                <h4 style="font-size: clamp(16px, 2vw + 16px, 20px) ; font-weight: 400; color: #000000;">Kavitha Krishnan</h4>
            </div>
        </div>

        <p class="pt-5" style="font-size: clamp(16px, 2vw + 1rem, 19px); color: #000000; line-height: 1.3;">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum
        </p>

    </div>

    <div class="py-5 col-10 mx-auto d-flex align-items-center justify-content-center" style="gap: 8px;">
        <a class="owl-prev" role="button" aria-label="Previous">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M32 16C32 11.7565 30.3143 7.68687 27.3137 4.68629C24.3131 1.68571 20.2435 0 16 0C11.7565 0 7.68687 1.68571 4.68629 4.68629C1.68571 7.68687 0 11.7565 0 16C0 20.2435 1.68571 24.3131 4.68629 27.3137C7.68687 30.3143 11.7565 32 16 32C20.2435 32 24.3131 30.3143 27.3137 27.3137C30.3143 24.3131 32 20.2435 32 16ZM16.9375 8.4375C17.525 7.85 18.475 7.85 19.0562 8.4375C19.6375 9.025 19.6437 9.975 19.0562 10.5562L13.6187 15.9937L19.0562 21.4312C19.6437 22.0187 19.6437 22.9688 19.0562 23.55C18.4688 24.1312 17.5187 24.1375 16.9375 23.55L10.4375 17.0625C9.85 16.475 9.85 15.525 10.4375 14.9438L16.9375 8.4375Z" fill="black"/>
            </svg>
        </a>

        <div class="owl-carousel owl-theme" style="max-width: 1097px; width: 100%; height: 259px;">
            <div class="item">
                <img style="object-fit: cover; border: 1px solid #d3d3d3; border-radius: 6px; height: 259px; width: 100%;" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/job_search.png" alt="Image 1">
            </div>
            <div class="item">
                <img style="object-fit: cover; border: 1px solid #d3d3d3; border-radius: 6px; height: 259px; width: 100%;" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/job_search.png" alt="Image 2">
            </div>
            <div class="item">
                <img style="object-fit: cover; border: 1px solid #d3d3d3; border-radius: 6px; height: 259px; width: 100%;" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/job_search.png" alt="Image 3">
            </div>
            <div class="item">
                <img style="object-fit: cover; border: 1px solid #d3d3d3; border-radius: 6px; height: 259px; width: 100%;" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/job_search.png" alt="Image 4">
            </div>
            <div class="item">
                <img style="object-fit: cover; border: 1px solid #d3d3d3; border-radius: 6px; height: 259px; width: 100%;" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/job_search.png" alt="Image 5">
            </div>
            <div class="item">
                <img style="object-fit: cover; border: 1px solid #d3d3d3; border-radius: 6px; height: 259px; width: 100%;" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/job_search.png" alt="Image 6">
            </div>
        </div>

        <a class="owl-next" role="button" aria-label="Next">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 16C0 11.7565 1.68571 7.68687 4.68629 4.68629C7.68687 1.68571 11.7565 0 16 0C20.2435 0 24.3131 1.68571 27.3137 4.68629C30.3143 7.68687 32 11.7565 32 16C32 20.2435 30.3143 24.3131 27.3137 27.3137C24.3131 30.3143 20.2435 32 16 32C11.7565 32 7.68687 30.3143 4.68629 27.3137C1.68571 24.3131 0 20.2435 0 16ZM15.0625 8.4375C14.475 7.85 13.525 7.85 12.9438 8.4375C12.3625 9.025 12.3563 9.975 12.9438 10.5562L18.3813 15.9937L12.9438 21.4312C12.3563 22.0187 12.3563 22.9688 12.9438 23.55C13.5312 24.1312 14.4813 24.1375 15.0625 23.55L21.5625 17.0625C22.15 16.475 22.15 15.525 21.5625 14.9438L15.0625 8.4375Z" fill="black"/>
            </svg>
        </a>
    </div>

    <div class="mx-auto row" style="max-width: 1028px;">
        <div class="col-lg-6">

        <?php echo taoh_comments_widget(array(
            'conttoken'=> $conttoken,
                'conttype'=> 'ask',
                'redirect'=> $share_link,
                'label'=> 'Answer')); ?>
        </div>
    </div>
</div>


<script>
$(document).ready(function(){
    $('.owl-carousel').owlCarousel({
        loop: true,
        margin: 10,
        nav: false, // Disable default nav
        responsive: {
            0: {
                items: 1 // Show 1 item on small screens
            },
            600: {
                items: 2 // Show 2 items on medium screens
            },
            1000: {
                items: 3 // Show 3 items on large screens
            }
        }
    });

    // Custom navigation
    $('.owl-prev').click(function() {
        $('.owl-carousel').trigger('prev.owl.carousel');
    });

    $('.owl-next').click(function() {
        $('.owl-carousel').trigger('next.owl.carousel');
    });
});
</script>
<?php
taoh_get_footer();