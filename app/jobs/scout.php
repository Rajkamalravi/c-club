<?php 

if ( ! defined ( 'TAO_PAGE_TITLE' ) ) { define ( 'TAO_PAGE_TITLE', "Comprehensive Open Jobs List at ".TAOH_SITE_NAME_SLUG.": Explore and Apply to a Wide Range of Job Opportunities" ); }
if ( ! defined ( 'TAO_PAGE_DESCRIPTION' ) ) { define ( 'TAO_PAGE_DESCRIPTION', "Browse our comprehensive jobs list featuring a diverse range of job opportunities across industries. Find the perfect job that matches your skills and interests, chat with recruiters and easily apply through our user-friendly platform at ".TAOH_SITE_NAME_SLUG.". Start your job search today and take the next step in your career." ); }
if ( ! defined ( 'TAO_PAGE_KEYWORDS' ) ) { define ( 'TAO_PAGE_KEYWORDS', "Job openings at ".TAOH_SITE_NAME_SLUG.", Employment opportunities at ".TAOH_SITE_NAME_SLUG.", Job listings at ".TAOH_SITE_NAME_SLUG.", Job board at ".TAOH_SITE_NAME_SLUG.", Job search platform at ".TAOH_SITE_NAME_SLUG.", Job finder at ".TAOH_SITE_NAME_SLUG.", Job database at ".TAOH_SITE_NAME_SLUG.", Job search engine at ".TAOH_SITE_NAME_SLUG.", Job match at ".TAOH_SITE_NAME_SLUG.", Job applications at ".TAOH_SITE_NAME_SLUG.", Apply for jobs at ".TAOH_SITE_NAME_SLUG.", Job search website at ".TAOH_SITE_NAME_SLUG.", Find a job at ".TAOH_SITE_NAME_SLUG.", Job seekers at ".TAOH_SITE_NAME_SLUG.", Job alerts at ".TAOH_SITE_NAME_SLUG.", Explore job opportunities at ".TAOH_SITE_NAME_SLUG ); }
taoh_get_header();
$current_app = taoh_parse_url(0);
$app_data = taoh_app_info($current_app);
$taoh_user_vars = taoh_user_all_info();
$empty = 0;
//echo taoh_parse_url(0);taoh_exit();
$top_array = range(1, 30);
$top_random = array_intersect_key($top_array, array_flip(array_rand($top_array, 10)));
$top_random = array_values($top_random);
$btm_array = range(31, 60);
$btm_random = array_intersect_key($btm_array, array_flip(array_rand($btm_array, 10)));
$btm_random = array_values($btm_random);
//print_r($top_random);
?>

<style>
    .scout-title {
        font-size: clamp(26px, 3vw + 1rem, 30px);
    }
    .scout-content {
        font-size: clamp(16px, 2vw + 1rem, 18px);
        line-height: 1.5;
    }
    .get-started-btn {
        color: #fff; 
        font-size: clamp(14px, 2vw + 1rem, 16px); 
        font-weight: 400; 
        display: flex;
        align-items: center;
        gap: 6px; 
        background: #333333; 
        width: fit-content;
        border-radius: 12px;
        padding: 8px 14px;
    }
    .get-started-btn:hover {
        color: #fff;
    }

span.h5 {
  font-size: 13px !important;
}
</style>
<!--======================================
        START HERO AREA
======================================-->
<!-- new template start -->
<div class="mobile-app">
    <header class="bg-white border-bottom border-bottom-gray sticky-top">
        <section class="hero-area pt-20px pb-20px bg-white shadow-sm overflow-hidden">
            <div class="container">			
                <div class="hero-content d-flex flex-wrap align-items-center justify-content-between jobs-mobile-header">
                    
                    <div class="col-lg-12 text-center mb-3">
                                            
                            <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist" style="border-bottom: 0;">
                            <?php 
                                $page_sel = "scout";
                                include_once('job_tabs.php'); ?>
                            </ul>
                        
                    </div>
                </div><!-- end hero-content -->	
            </div><!-- end container -->
        </section>
    </header>

    <?php if( !taoh_user_is_logged_in() ) { ?>
    <section class="pb-4" style="background: #E0E0E029;">
        <div class="container row mx-auto d-flex align-items-center">
            
        
            <div class="col-lg-6 py-2">
                <h1 class="pb-3 section-title" style="color: #000000; font-weight: 600;">Get the Best Talent Through Expert Professionals</h1>

                <p class="pb-4 section-desc" style="font-weight: 400; color: #3F3F3F; text-align: justify; line-height: 1.7;">Introducing Scout, A specialized program that enables faster hiring with the power of great communities and talented individuals.</p>

                <div>
                    <button class="btn px-4"  style="background: #215281; color: white; font-size: clamp(12px, 2vw + 1rem, 15px);">
                        <a class="text-white" href="">Sign up for Scout</a>
                    </button>
                </div>
            </div>


            <div class="col-lg-6 d-flex justify-content-center align-items-center py-3">
                <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/job_search.png';?>" alt="" style="max-height: 328px; width: 570px; object-fit: cover;">
            </div>

        
        </div>
    </section>

    <!--  -->
    <section style="background: #fff;">
        <div class="container pb-4">
            <div class="counter-box px-4 res-border-radius" style="background: #FFFFFF;  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);">
                <div class="row">
                    <div class="col responsive-column-half">
                        <div class="media media-card text-center px-0 py-4 shadow-none rounded-0 bg-transparent counter-item mb-0">
                            <div class="media-body border-right border-right-gray" style="border-right-width: 2px !important;">
                                <h5 class="pb-4" style="font-size: clamp(1rem, 2vw + 0.375rem, 1.375rem); color: #000000; font-weight: 400;">5+ Million</h5>
                                <p class="" style="font-size: clamp(0.875rem, 1vw + 0.125rem, 1rem); color: #767676; font-weight: 400;">Visitors on our network</p>
                            </div>
                        </div>
                    </div>
                    <div class="col responsive-column-half ">
                        <div class="media media-card text-center px-0 py-4 shadow-none rounded-0 bg-transparent counter-item mb-0">
                            <div class="media-body border-right border-right-gray" style="border-right-width: 2px !important;">
                                <h5 class="pb-4" style="font-size: clamp(1rem, 2vw + 0.375rem, 1.375rem); color: #000000; font-weight: 400;">2+ Million</h5>
                                <p  style="font-size: clamp(0.875rem, 1vw + 0.125rem, 1rem); color: #767676; font-weight: 400;">Career events attended</p>
                            </div>
                        </div>
                    </div>
                    <div class="col responsive-column-half ">
                        <div class="media media-card text-center px-0 py-4 shadow-none rounded-0 bg-transparent counter-item mb-0">
                            <div class="media-body border-right border-right-gray" style="border-right-width: 2px !important;">
                                <h5 class="pb-4" style="font-size: clamp(1rem, 2vw + 0.375rem, 1.375rem); color: #000000; font-weight: 400;">1+ Million</h5>
                                <p  style="font-size: clamp(0.875rem, 1vw + 0.125rem, 1rem); color: #767676; font-weight: 400;">Connections created</p>
                            </div>
                        </div>
                    </div>
                    <div class="col responsive-column-half ">
                        <div class="media media-card text-center px-0 py-4 shadow-none rounded-0 bg-transparent counter-item mb-0">
                            <div class="media-body border-right border-right-gray" style="border-right-width: 2px !important;">
                                <h5 class="pb-4" style="font-size: clamp(1rem, 2vw + 0.375rem, 1.375rem); color: #000000; font-weight: 400;">10,000+</h5>
                                <p style="font-size: clamp(0.875rem, 1vw + 0.125rem, 1rem); color: #767676; font-weight: 400;">Recruiters on platform</p>
                            </div>
                        </div>
                    </div>
                    <div class="col responsive-column-half">
                        <div class="media media-card text-center px-0 py-4 shadow-none rounded-0 bg-transparent counter-item mb-0">
                            <div class="media-body">
                                <h5 class="pb-4" style="font-size: clamp(1rem, 2vw + 0.375rem, 1.375rem); color: #000000; font-weight: 400;">150+</h5>
                                <p  style="font-size: clamp(0.875rem, 1vw + 0.125rem, 1rem); color: #767676; font-weight: 400;">Communities served</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php } ?>


    <section class="py-5 px-3" style="background: #fff;">
        <div class="container row mx-auto d-flex align-items-center py-4 pb-5 px-lg-4" style="background: #D8E0F9; border-radius: 6px;">
            
        
            <div class="col-lg-6 py-3">
                <span class="px-3 py-2" style="background: #333333; color: #fff; border-radius: 12px; font-size:  clamp(14px, 2vw + 1rem, 16px);">For employers</span>

                <!-- font-size: clamp(1rem, 2vw + 1rem, 1.706rem); -->
                <h4 class="mt-4 col-lg-10 px-0 scout-title" style="color: #333333; font-weight: 400; line-height: 1.2;">Discover a Fresh Dimension of Hiring, where you can hire 6X Faster than traditional methods</h4>
                <p class="py-4 scout-content" style="color: #555555; text-align: justify; line-height: 1.4;">Scout is a specialized program which redefines your hiring experience for ever with the power of Community and AI saving your valuable time and effort.</p>

                <div>
                    <a href="<?php echo TAOH_SITE_URL_ROOT."/jobs/scouts-signup-employer"; ?>" class="get-started-btn btn" style="">
                    <span>Get Started</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 12C0 15.1826 1.26428 18.2348 3.51472 20.4853C5.76516 22.7357 8.8174 24 12 24C15.1826 24 18.2348 22.7357 20.4853 20.4853C22.7357 18.2348 24 15.1826 24 12C24 8.8174 22.7357 5.76516 20.4853 3.51472C18.2348 1.26428 15.1826 0 12 0C8.8174 0 5.76516 1.26428 3.51472 3.51472C1.26428 5.76516 0 8.8174 0 12ZM11.2969 17.6719C10.8562 18.1125 10.1438 18.1125 9.70781 17.6719C9.27188 17.2313 9.26719 16.5188 9.70781 16.0828L13.7859 12.0047L9.70781 7.92656C9.26719 7.48594 9.26719 6.77344 9.70781 6.3375C10.1484 5.90156 10.8609 5.89687 11.2969 6.3375L16.1719 11.2031C16.6125 11.6438 16.6125 12.3562 16.1719 12.7922L11.2969 17.6719Z" fill="#ffffff"/>
                        </svg>

                    </a>
                </div>
            </div>


            <div class="col-lg-6 d-flex justify-content-center align-items-center py-3">
                <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/scout_landing_employer.png';?>" alt="" style="max-height: 328px; width: 442.06px; object-fit: cover;">
            </div>

        
        </div>
    </section>
    <section class="py-5 px-3"  style="background: #fff;">
        <div class="container row mx-auto d-flex px-0" style="gap: 2rem;">
            
        
            <div class="col-lg p-4 p-lg-5" style="background: #F7EE9E; border-radius: 8px;">
                <span class="px-3 py-2" style="background: #333333; color: #fff; border-radius: 12px; font-size:  clamp(14px, 2vw + 1rem, 16px);">For Professionals</span>
                <h4 class="mt-4 col-lg-11 px-0 scout-title" style="color: #000000; font-weight: 400; line-height: 1.2;">Landing your dream role at your ideal company, hassle-free, is now a reality!</h4>
                <p class="py-4 scout-content" style=" color: #555555; text-align: justify; line-height: 1.4;">Browse through our job board, find a scout job and choose a scout and apply ! Congrats, you are a step closer to your dream job.</p>

                <div>
                    <a href="<?php echo TAOH_SITE_URL_ROOT."/jobs/scouts-signup-professional"; ?>" class="get-started-btn btn" style="">
                    <span>Get Started</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 12C0 15.1826 1.26428 18.2348 3.51472 20.4853C5.76516 22.7357 8.8174 24 12 24C15.1826 24 18.2348 22.7357 20.4853 20.4853C22.7357 18.2348 24 15.1826 24 12C24 8.8174 22.7357 5.76516 20.4853 3.51472C18.2348 1.26428 15.1826 0 12 0C8.8174 0 5.76516 1.26428 3.51472 3.51472C1.26428 5.76516 0 8.8174 0 12ZM11.2969 17.6719C10.8562 18.1125 10.1438 18.1125 9.70781 17.6719C9.27188 17.2313 9.26719 16.5188 9.70781 16.0828L13.7859 12.0047L9.70781 7.92656C9.26719 7.48594 9.26719 6.77344 9.70781 6.3375C10.1484 5.90156 10.8609 5.89687 11.2969 6.3375L16.1719 11.2031C16.6125 11.6438 16.6125 12.3562 16.1719 12.7922L11.2969 17.6719Z" fill="#ffffff"/>
                        </svg>

                    </a>
                </div>
            </div>


            <div class="col-lg p-4 p-lg-5" style="background: #A8EAC9CC; border-radius: 8px;">
                <span class="px-3 py-2 mt-1" style="background: #333333; color: #fff; border-radius: 12px; font-size:  clamp(14px, 2vw + 1rem, 16px);">For Scouts</span>
                <h4 class="mt-4 col-lg-11 px-0 scout-title" style="color: #000000; font-weight: 400; line-height: 1.2;">Put your hiring skills to work and earn by referring top talent to companies!</h4>
                <p class="py-4 scout-content" style="color: #555555; text-align: justify; line-height: 1.4;">Explore our Job Board ! Find scout jobs that matches your skills and put your hiring skills to work to recommend top talent for the listing and get paid !</p>

                <div>
                    <a href="<?php echo TAOH_SITE_URL_ROOT."/jobs/scouts-signup"; ?>" class="get-started-btn btn" style="">
                    <span>Get Started</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 12C0 15.1826 1.26428 18.2348 3.51472 20.4853C5.76516 22.7357 8.8174 24 12 24C15.1826 24 18.2348 22.7357 20.4853 20.4853C22.7357 18.2348 24 15.1826 24 12C24 8.8174 22.7357 5.76516 20.4853 3.51472C18.2348 1.26428 15.1826 0 12 0C8.8174 0 5.76516 1.26428 3.51472 3.51472C1.26428 5.76516 0 8.8174 0 12ZM11.2969 17.6719C10.8562 18.1125 10.1438 18.1125 9.70781 17.6719C9.27188 17.2313 9.26719 16.5188 9.70781 16.0828L13.7859 12.0047L9.70781 7.92656C9.26719 7.48594 9.26719 6.77344 9.70781 6.3375C10.1484 5.90156 10.8609 5.89687 11.2969 6.3375L16.1719 11.2031C16.6125 11.6438 16.6125 12.3562 16.1719 12.7922L11.2969 17.6719Z" fill="#ffffff"/>
                        </svg>

                    </a>
                </div>
            </div>

        
        </div>
    </section>




    <section style="background: #fff;">
        <h1 class="text-center pt-5 pb-4 px-2 scout-title" style="font-weight: 600;">Trusted by Leading Companies</h1>

            <div class="container"  style="position: relative; overflow: hidden; height: 100px;">
                <?php foreach($top_random as $top_key => $top_vals){ ?>
                    <div class="itemLeft item<?php echo $top_key+1; ?>">
                        <img src="<?php echo TAOH_CDN_PREFIX.'/images/company/logo'.$top_vals.'.png';?>" alt="" style="width: 100px; height: 100px; object-fit: contain;">
                    </div>
                <?php } ?>
            </div>
            <div class="container mt-2" style="position: relative; overflow: hidden; height: 100px;">
                <?php foreach($btm_random as $btm_key => $btm_vals){ ?>
                    <div class="itemRight item<?php echo $btm_key+1; ?>">
                        <img src="<?php echo TAOH_CDN_PREFIX.'/images/company/logo'.$btm_vals.'.png';?>" alt="" style="width: 100px; height: 100px; object-fit: contain;">
                    </div>
                <?php } ?>
            </div>

    

    </section>

    <!-- companies image slider ends  -->


    <section  style="background: #fff;">
        <h1 class="text-center pt-5 pb-4  scout-title" style="font-weight: 600;">How does this work?</h1>
        <div class="mt-5 container row  mx-auto">
            <ul class=" nav scout nav-tabs  pb-3 col-12 d-flex flex-nowrap scroll-container" id="myTab" role="tablist" style="border-bottom: 1px solid #ACACAC; overflow-x: auto; justify-content: space-around;">

                <li class="nav-item">
                    <a class="nav-link active text-center text-nowrap" id="employers_tab" data-toggle="tab" href="#employers" role="tab" aria-controls="employers" aria-selected="true" style="font-size: 23px; min-width: 200px;">For Employers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-center text-nowrap " id="professionals-tab" data-toggle="tab" href="#professionals" role="tab" aria-controls="professionals" aria-selected="false" style="font-size: 23px; min-width: 200px;">For Professionals</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-center text-nowrap " id="scouts-tab" data-toggle="tab" href="#scouts" role="tab" aria-controls="scouts" aria-selected="false" style="font-size: 23px; min-width: 200px;">For Scouts</a>
                </li>


            </ul>
            <!-- employer tab -->
            <div class="tab-content py-5" id="myTabContent">
                <div class="tab-pane fade show active" id="employers" role="tabpanel" aria-labelledby="employers_tab">

                    <div class="container row mx-auto d-flex align-items-center ">

                        <div class="col-lg-6 d-flex justify-content-start align-items-center py-3">
                            <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/semp_maxresdefault.jpg';?>" alt="" style="max-height: 312px; width: 385px; object-fit: contain;">
                        </div>
                    
                        <div class="col-lg-6 py-2">
                            <span class="btn px-4 py-1" style="color: #000000;background: #CEEBF5; border-radius: 20px; font-size: clamp(1rem, 2vw + 0.25rem, 1.25rem);">Step 1</span>
                            <h1 class="my-4  scout-title" style="color: #2557A7; font-weight: 400;">SIGNUP & POST A SCOUT JOB</h1>
                            <p class="scout-content" style=" color: #3F3F3F; text-align: justify; line-height: 1.4;">Signup to scout, This will give you access to our network of experienced professionals who will help you find the best candidates for your job openings. Once signed up, post a detailed job listing and enable scout for the listing. You are Done ! You can get started in minutes.</p>
                        </div>

                    </div>
                    <div class="container row mx-auto d-flex align-items-center  flex-wrap-reverse">

                        <div class="col-lg-6 py-2">
                            <span class="btn px-4 py-1" style="color: #000000;background: #CEEBF5; border-radius: 20px; font-size: clamp(1rem, 2vw + 0.25rem, 1.25rem);">Step 2</span>
                            <h1 class="my-4 scout-title" style="color: #2557A7; font-weight: 400;">THE SCOUT EFFECT</h1>
                            <p class="scout-content" style=" color: #3F3F3F; text-align: justify; line-height: 1.4;">Our platform connects you with expert scouts who specialize in finding top-tier talent. They'll review your job posting and present you with qualified candidates who perfectly align with your needs</p>
                        </div>

                        <div class="col-lg-6 d-flex justify-content-end align-items-center py-3">
                            <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/semp_2.jpg';?>" alt="" style="max-height: 312px; width: 385px; object-fit: contain;">
                        </div>

                    </div>
                    <div class="container row mx-auto d-flex align-items-center ">

                        <div class="col-lg-6 d-flex justify-content-start align-items-center py-3">
                            <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/semp_3.jpg';?>" alt="" style="max-height: 312px; width: 385px; object-fit: contain;">
                        </div>
                    
                        <div class="col-lg-6 py-2">
                            <span class="btn px-4 py-1" style="color: #000000;background: #CEEBF5; border-radius: 20px; clamp(1rem, 2vw + 0.25rem, 1.25rem);">Step 3</span>
                            <h1 class="my-4 scout-title" style="color: #2557A7; font-weight: 400;">FIND YOUR HIRE !</h1>
                            <p class="scout-content" style=" color: #3F3F3F; text-align: justify; line-height: 1.4;">Interview the referred candidates and select the best fit for your organization. Pay the fees as per the contract after 30 days of candidate hiring.</p>
                        </div>

                    </div>
                    
                </div>
                <!-- professional tab -->
                <div class="tab-pane fade" id="professionals" role="tabpanel" aria-labelledby="professionals-tab">
                    <div class="container row mx-auto d-flex align-items-center ">

                        <div class="col-lg-6 d-flex justify-content-start align-items-center py-3">
                            <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/semp_maxresdefault.jpg';?>" alt="" style="max-height: 312px; width: 385px; object-fit: contain;">
                        </div>
                    
                        <div class="col-lg-6 py-2">
                            <span class="btn px-4 py-1" style="color: #000000;background: #CEEBF5; border-radius: 20px; font-size: clamp(1rem, 2vw + 0.25rem, 1.25rem);">Step 1</span>
                            <h1 class="my-4 scout-title" style="color: #2557A7; font-weight: 400;">SIGNUP & FIND A SCOUT JOB</h1>
                            <p class="scout-content" style=" color: #3F3F3F; text-align: justify; line-height: 1.4;">Signup with scout as a professional and improve your hiring prospects ! You can get started in minutes. Search a scout job on the listing that matches your skill set and select a scout who has a strong track record in your field and make a connection.</p>
                        </div>

                    </div>
                    <div class="container row mx-auto d-flex align-items-center  flex-wrap-reverse">

                        <div class="col-lg-6 py-2">
                            <span class="btn px-4 py-1" style="color: #000000;background: #CEEBF5; border-radius: 20px; font-size: clamp(1rem, 2vw + 0.25rem, 1.25rem);">Step 2</span>
                            <h1 class="my-4 scout-title" style="color: #2557A7; font-weight: 400;">THE SCOUT EFFECT</h1>
                            <p class="scout-content" style=" color: #3F3F3F; text-align: justify; line-height: 1.4;">Engage with your scout to get referred to the most relevant jobs. This improves your chances of success in your job applications and leverages the power of expert professionals to help you with career guidance.</p>
                        </div>

                        <div class="col-lg-6 d-flex justify-content-end align-items-center py-3">
                            <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/semp_2.jpg';?>" alt="" style="max-height: 312px; width: 385px; object-fit: contain;">
                        </div>

                    </div>
                    <div class="container row mx-auto d-flex align-items-center ">

                        <div class="col-lg-6 d-flex justify-content-start align-items-center py-3">
                            <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/semp_3.jpg';?>" alt="" style="max-height: 312px; width: 385px; object-fit: contain;">
                        </div>
                    
                        <div class="col-lg-6 py-2">
                            <span class="btn px-4 py-1" style="color: #000000;background: #CEEBF5; border-radius: 20px; font-size: clamp(1rem, 2vw + 0.25rem, 1.25rem);">Step 3</span>
                            <h1 class="my-4 scout-title" style="color: #2557A7; font-weight: 400;">APPLY AND GET HIRED</h1>
                            <p class="scout-content" style=" color: #3F3F3F; text-align: justify; line-height: 1.4;">Use these referrals to apply for jobs and connect with potential employers and get landed in your dream job with less effort than traditional application process. Professionals with referrals are 4x likely to be hired.</p>
                        </div>

                    </div>
                </div>
                <!-- scout tab -->
                <div class="tab-pane fade" id="scouts" role="tabpanel" aria-labelledby="scouts-tab">
                    <div class="container row mx-auto d-flex align-items-center ">

                        <div class="col-lg-6 d-flex justify-content-start align-items-center py-3">
                            <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/semp_maxresdefault.jpg';?>" alt="" style="max-height: 312px; width: 385px; object-fit: contain;">
                        </div>
                    
                        <div class="col-lg-6 py-2">
                            <span class="btn px-4 py-1" style="color: #000000;background: #CEEBF5; border-radius: 20px; font-size: clamp(1rem, 2vw + 0.25rem, 1.25rem);">Step 1</span>
                            <h1 class="my-4 scout-title" style="color: #2557A7; font-weight: 400;">SIGNUP & FIND A SCOUT JOB</h1>
                            <p class="scout-content" style=" color: #3F3F3F; text-align: justify; line-height: 1.4;">Signup as a scout, With scout your professional network is more valuable than ever. Refer your friends, colleagues, peers who are a great fit for open roles and earn a scouting bonus for each successful hire. Find a scout Job that matches your area of expertise. Recommend professionals in your network who match the job requirements.</p>
                        </div>

                    </div>
                    <div class="container row mx-auto d-flex align-items-center  flex-wrap-reverse">

                        <div class="col-lg-6 py-2">
                            <span class="btn px-4 py-1" style="color: #000000;background: #CEEBF5; border-radius: 20px; font-size: clamp(1rem, 2vw + 0.25rem, 1.25rem);">Step 2</span>
                            <h1 class="my-4 scout-title" style="color: #2557A7; font-weight: 400;">REFER WITH EASE</h1>
                            <p class="scout-content" style=" color: #3F3F3F; text-align: justify; line-height: 1.4;">Leverage our user friendly process and recommend professionals in your network who match the job requirements and co-ordinate between the employer and professional for interview and hiring process.</p>
                        </div>

                        <div class="col-lg-6 d-flex justify-content-end align-items-center py-3">
                            <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/semp_2.jpg';?>" alt="" style="max-height: 312px; width: 385px; object-fit: contain;">
                        </div>

                    </div>
                    <div class="container row mx-auto d-flex align-items-center ">

                        <div class="col-lg-6 d-flex justify-content-start align-items-center py-3">
                            <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/semp_3.jpg';?>" alt="" style="max-height: 312px; width: 385px; object-fit: contain;">
                        </div>
                    
                        <div class="col-lg-6 py-2">
                            <span class="btn px-4 py-1" style="color: #000000;background: #CEEBF5; border-radius: 20px; font-size: clamp(1rem, 2vw + 0.25rem, 1.25rem);">Step 3</span>
                            <h1 class="my-4 scout-title" style="color: #2557A7; font-weight: 400;">EARN REWARDS !</h1>
                            <p class="scout-content" style=" color: #3F3F3F; text-align: justify; line-height: 1.4;">Receive a bonus for every referral that results in a successful hire after the candidates completes 30 successful days at work. Turn your professional connections into rewarding opportunities.</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="py-5" style="background: #F8F8F8;">
        <div class="container row mx-auto">
            <h1 class="text-center col-12 scout-title" style="color: #434141; font-weight: 600;">By the Numbers: See our Impact in Action</h1>
            <div class="col-lg-4 p-4 p-lg-5">
                <h1 class="pb-4 " style="font-size: 20px; color: #215281; font-weight: 700;">88%</h1>
                <p class="pr-lg-4 scout-content" style=" color: #3A3A3A;">of Employers consider referred candidates as the best source of applicants</p>
            </div>
            <div class="col-lg-4 p-4 p-lg-5">
                <h1 class="pb-4 " style="font-size: 20px; color: #215281; font-weight: 700;">55%</h1>
                <p class="pr-lg-4 scout-content" style=" color: #3A3A3A;">faster recruiting than non-referral programs.</p>
            </div>
            <div class="col-lg-4 p-4 p-lg-5">
                <h1 class="pb-4 " style="font-size: 20px; color: #215281; font-weight: 700;">30 to 50%</h1>
                <p class="pr-lg-4 scout-content" style=" color: #3A3A3A;">Referred candidates accounts for 30 to 50% of new workers</p>
            </div>
        </div>
    </section>


    <section class="py-5" style="background: #ffffff;">
        <h1 class="text-center mb-5" style="font-size: clamp(1.25rem, 2.5vw + 0.625rem,  1.875rem); font-weight: 700; color: #434141;">Why Choose us?</h1>

        <div class="container row mx-auto py-2" style="gap:2rem;">
            <div class="col-md p-5" style="border: 0.3px solid #00000038; border-radius: 6px;">
                <div class="d-flex justify-content-center">
                    <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/emp_scout.png';?>" alt="" style="max-height: 97px; width: 154px; object-fit: contain;" >
                </div>
                <h4 class="py-4 text-center" style="color: #434141; font-size: clamp(1rem, 2vw + 0.25rem, 1.25rem); font-weight: 700;">Empowerment</h4>
                <p class="text-center scout-content" style=" color: #3A3A3A; font-weight: 400;">We're here to empower you on your career journey, providing the tools and knowledge you need to succeed.</p>
            </div>
            <div class="col-md p-5" style="border: 0.3px solid #00000038; border-radius: 6px;">
                <div class="d-flex justify-content-center">
                    <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/community_scout.png';?>" alt="" style="max-height: 97px; width: 154px; object-fit: contain;" >
                </div>
                <h4 class="py-4 text-center" style="color: #434141; font-size: clamp(1rem, 2vw + 0.25rem, 1.25rem); font-weight: 700;">Community</h4>
                <p class="text-center scout-content" style=" color: #3A3A3A; font-weight: 400;">Join a supportive community of like-minded individuals who are passionate about helping each other grow.</p>
            </div>
            <div class="col-md p-5" style="border: 0.3px solid #00000038; border-radius: 6px;">
                <div class="d-flex justify-content-center">
                    <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/mission_scout.png';?>" alt="" style="max-height: 97px; width: 154px; object-fit: contain;" >
                </div>
                <h4 class="py-4 text-center" style="color: #434141; font-size: clamp(1rem, 2vw + 0.25rem, 1.25rem); font-weight: 700;">Mission-Driven</h4>
                <p class="text-center scout-content" style=" color: #3A3A3A; font-weight: 400;">Our mission is to ensure that no worker is left behind. We're committed to your career development.</p>
            </div>

            <?php if( !taoh_user_is_logged_in() ) { ?>
            <div class="col-12 d-flex justify-content-center mt-5">
                <button class="btn px-4" style="font-size: clamp(14, 2vw + 1rem, 15px); color: #fff; background: #434141;">Join Us Today</button>
            </div>
            <?php } ?>
        </div>

    </section>
</div>



<!-- new template end -->
</script>
<?php
taoh_get_footer();
?>
