<?php
taoh_get_header();


$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
$user_info_obj = $taoh_user_is_logged_in ? taoh_user_all_info() : null;

$valid_dir_viewer = $taoh_user_is_logged_in && $user_info_obj->profile_complete && $user_info_obj->unlist_me_dir !== 'yes';

?>

<style>
    .the-start-club .title {
        font-size: clamp(16px, 4vw + 1rem, 29px);
        font-weight: 500; 
        color: #000000;
    }

    .the-start-club .post-btn {
        background: #2557A7; 
        border-radius: 12px;
    }
    .the-start-club .post-btn a {
        color: #fff;
        font-size: clamp(14px, 2vw + 1rem, 17px);
    }

    .the-start-club .input-container {
        position: relative;
        width: 100%;
    }

    .the-start-club .input-container input {
        height: 55px; 
        border: 2px solid #2557A7 !important; 
        border-radius: 24px 0 0 24px; 
        padding-left: 40px;
    }

    .the-start-club .input-container svg {
        position: absolute; 
        left: 15px; 
        top: 50%; 
        transform: translateY(-50%);
    }

    .club-title {
        font-size: clamp(16px, 3vw + 1rem, 19px);
        color: #2557A7;
        font-weight: 500;
    }
    .club-content {
        font-size: clamp(16px, 2vw + 1rem, 17px);
        color: #000000;

        /* Limit to 3 lines */
        display: -webkit-box;        
        -webkit-box-orient: vertical; 
        overflow: hidden;           
        -webkit-line-clamp: 4;      
    }

    .club-card-img img {
        width: 100%;
        height: 150px; 
        object-fit: cover;
    }

    @media (min-width: 1200px) { 
        .club-card-img img {
            width: 140px;
            height: 140px;
        }
    }
</style>

<div class="bg-white the-start-club">
    <header class="container mx-auto row d-flex flex-wrap pt-4" style="border-bottom: 1px solid #D3D3D3;">
        <div class="col-md-9 d-flex align-items-center pb-4" style="gap: 0.5rem;">
            <h1 class="title" style="">TheStart.club</h1>
        </div>
        <div class="col-md-3 d-flex align-items-center justify-content-end pb-4">
            <button type="button" class="btn px-5 text-nowrap post-btn" style="">
                <a href="#" style="">POST JOBS</a>
            </button>
        </div>
    </header>


    <section class="pb-4" style="">
        <div class="container row mx-auto pt-5" style="min-height: 300px;">
            
        
            <div class="col-lg-6 col-xl-5 py-2 d-flex align-items-center">
                <div class="ml-lg-5">
                    <h1 class="pb-4" style="color: #2557A7; font-weight: 600; font-size: clamp(21px, 2vw + 1rem, 30px);">Find Where you Belong !</h1>
                
                    <p class="pb-4" style="font-size: clamp(17px, 1.5vw + 0.5rem, 19px); font-weight: 400; color: #444444; text-align: justify; line-height: 1.5;">Join communities that share your interests, passions, and values. Connect with like-minded individuals and start building meaningful relationships.</p>

                    <div class="mt-1">
                        <button class="btn px-4" style="background: #2557A7; color: white; font-size:  clamp(17px, 1.5vw + 0.5rem, 19px); height: 47px; border-radius: 11px;">
                            <a class="text-white" href="">Start Exploring</a>
                        </button>
                    </div>
                </div>
            </div>


            <div class="col-lg-6 col-xl-7 d-flex justify-content-center align-items-end py-3">
                <div class="">
                    <img src="https://localhost/hires-i/assets/images/TheStartClub.png" alt="" style="max-height: 316px; width: 774px; object-fit: cover;">
                </div>
            </div>

        
        </div>
    </section>


    <div class="container row py-5 mx-auto">
        <div class="d-flex col-md-10 col-lg-8 mx-auto">
            <div class="input-container" style="">
                <input type="text" class="form-control pl-lg-5" id="search-input" style="" placeholder="Search by club name, role or company name">
                <svg style="" width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0.285595 18.1558L4.37515 14.067C4.55974 13.8824 4.80995 13.7799 5.07247 13.7799H5.74107C4.60896 12.3322 3.93625 10.5113 3.93625 8.53042C3.93625 3.81818 7.75509 0 12.4681 0C17.1812 0 21 3.81818 21 8.53042C21 13.2427 17.1812 17.0608 12.4681 17.0608C10.4869 17.0608 8.6657 16.3882 7.21774 15.2563V15.9248C7.21774 16.1873 7.1152 16.4375 6.93061 16.622L2.84105 20.7109C2.45548 21.0964 1.832 21.0964 1.45052 20.7109L0.289698 19.5502C-0.0958786 19.1647 -0.0958786 18.5414 0.285595 18.1558ZM12.4681 13.7799C15.3681 13.7799 17.7185 11.434 17.7185 8.53042C17.7185 5.6309 15.3722 3.28093 12.4681 3.28093C9.56811 3.28093 7.21774 5.62679 7.21774 8.53042C7.21774 11.4299 9.56401 13.7799 12.4681 13.7799Z" fill="#EBE9E9"/>
                </svg>
            </div>

            <button type="button" class="btn d-flex align-items-center" style="gap: 8px; background: #2557A7; color: #fff; border-radius: 0 24px 24px 0; height: 55px;">
                <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0.285595 18.1558L4.37515 14.067C4.55974 13.8824 4.80995 13.7799 5.07247 13.7799H5.74107C4.60896 12.3322 3.93625 10.5113 3.93625 8.53042C3.93625 3.81818 7.75509 0 12.4681 0C17.1812 0 21 3.81818 21 8.53042C21 13.2427 17.1812 17.0608 12.4681 17.0608C10.4869 17.0608 8.6657 16.3882 7.21774 15.2563V15.9248C7.21774 16.1873 7.1152 16.4375 6.93061 16.622L2.84105 20.7109C2.45548 21.0964 1.832 21.0964 1.45052 20.7109L0.289698 19.5502C-0.0958786 19.1647 -0.0958786 18.5414 0.285595 18.1558ZM12.4681 13.7799C15.3681 13.7799 17.7185 11.434 17.7185 8.53042C17.7185 5.6309 15.3722 3.28093 12.4681 3.28093C9.56811 3.28093 7.21774 5.62679 7.21774 8.53042C7.21774 11.4299 9.56401 13.7799 12.4681 13.7799Z" fill="#EBE9E9"/>
                </svg>
                <span>Search</span>
            </button>
        </div>
    </div>


    <div class="container pb-3">
        <div class="row mx-0">
            <div class="col-md-6 col-xl-4" style="">
                <div class="pb-3 mx-auto mx-xl-0 mt-3" style=" border: 1px solid #D3D3D3;">
                    <div class="d-flex flex-column flex-xl-row mx-auto col-12 row px-0" style="">
                        <div class="club-card-img col-xl-5 pt-3">
                            <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/placeholder.png" alt="" style="">
                        </div>
                        <div class="col-xl-7 pt-3 pl-xl-0">
                            <h4 class="club-title">
                                Analytics Club
                            </h4>
                            <p class="club-content  pt-2">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua
                            </p>
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end mt-3">
                        <button type="button" class="btn" style="background: #2557A7; border-radius: 12px; height: 42px;"><a href="" style="color: #fff;">Explore More</a></button>
                    </div>
                </div>
            </div>
            <!-- card 2 -->
            <div class="col-md-6 col-xl-4" style="">
                <div class="pb-3 mx-auto mx-xl-0 mt-3" style=" border: 1px solid #D3D3D3;">
                    <div class="d-flex flex-column flex-xl-row mx-auto col-12 row px-0" style="">
                        <div class="club-card-img col-xl-5 pt-3">
                            <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/placeholder.png" alt="" style="">
                        </div>
                        <div class="col-xl-7 pt-3 pl-xl-0">
                            <h4 class="club-title">
                                Analytics Club
                            </h4>
                            <p class="club-content  pt-2">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua
                            </p>
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end mt-3">
                        <button type="button" class="btn" style="background: #2557A7; border-radius: 12px; height: 42px;"><a href="" style="color: #fff;">Explore More</a></button>
                    </div>
                </div>
            </div>
            
            <!-- card 3 -->
            <div class="col-md-6 col-xl-4" style="">
                <div class="pb-3 mx-auto mx-xl-0 mt-3" style=" border: 1px solid #D3D3D3;">
                    <div class="d-flex flex-column flex-xl-row mx-auto col-12 row px-0" style="">
                        <div class="club-card-img col-xl-5 pt-3">
                            <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/placeholder.png" alt="" style="">
                        </div>
                        <div class="col-xl-7 pt-3 pl-xl-0">
                            <h4 class="club-title">
                                Analytics Club
                            </h4>
                            <p class="club-content  pt-2">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua
                            </p>
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end mt-3">
                        <button type="button" class="btn" style="background: #2557A7; border-radius: 12px; height: 42px;"><a href="" style="color: #fff;">Explore More</a></button>
                    </div>
                </div>
            </div>
            <!-- card-4 -->
            <div class="col-md-6 col-xl-4" style="">
                <div class="pb-3 mx-auto mx-xl-0 mt-3" style=" border: 1px solid #D3D3D3;">
                    <div class="d-flex flex-column flex-xl-row mx-auto col-12 row px-0" style="">
                        <div class="club-card-img col-xl-5 pt-3">
                            <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/placeholder.png" alt="" style="">
                        </div>
                        <div class="col-xl-7 pt-3 pl-xl-0">
                            <h4 class="club-title">
                                Analytics Club
                            </h4>
                            <p class="club-content  pt-2">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua
                            </p>
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end mt-3">
                        <button type="button" class="btn" style="background: #2557A7; border-radius: 12px; height: 42px;"><a href="" style="color: #fff;">Explore More</a></button>
                    </div>
                </div>
            </div>
        </div>
    
    </div>


</div>

<?php
taoh_get_footer();