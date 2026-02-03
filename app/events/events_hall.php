<?php
include_once TAOH_SITE_PATH_ROOT.'/assets/icons/icons.php';
taoh_get_header();
?>
<div class="events-hall pb-5">
    <div class="blue-banner">
        <div class="container">
            <p class="banner-text-sm mb-4">You are in Events / First Friday Virtual Job Fair /Speakers Hall List</p>
            <h4 class="banner-text-title">Speakers Hall List</h4>
        </div>
    </div>

    <div class="container d-flex flex-column flex-md-row justify-content-center py-4 py-lg-5">
        <input type="text" class="mb-3 my-lg-3 form-control search-input" style="max-width: 553px;">
        <button type="button" class="btn search-btn mb-3 my-lg-3">Search</button>
    </div>

    <div class="container d-flex flex-column flex-xl-row justify-content-xl-center" style="gap: 36px;">
        <div class="hall-list-container mx-auto mx-xl-0">
            <!-- list 1 -->
            <div class="hall-list d-flex flex-column flex-md-row mb-3">
                <div class="time d-flex flex-md-column align-items-center justify-content-center p-2 p-md-1" style="gap: 12px;">
                    <?= icon('clock-solid', '#ffffff', 32) ?>
                    <p>10 am to 11 am</p>
                </div>
                <div class="info d-flex flex-column flex-md-row align-items-md-center justify-content-between px-3 py-4" style="flex: 1; border: 1px solid #d3d3d3; gap: 12px;">
                    <div class="d-flex align-items-center" style="gap: 16px;">
                        <div>
                            <img class="hall-list-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/profile_room_3.png" alt="">
                        </div>
                        <div>
                            <p class="hall-text-md mb-2">Topic of the Talk</p>
                            <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 12px;">
                                <p class="hall-text-xs d-flex align-items-center" style="gap: 6px;">
                                    <?= icon('user', '#5E5E5E', 16) ?>
                                    <span>Speaker Name</span>
                                </p>
                                <p class="hall-text-xs d-flex align-items-center" style="gap: 6px;">
                                    <?= icon('briefcase', '#5E5E5E', 15) ?>
                                    <span>VP Marketing at TamQ</span>
                                </p>
                            </div>
                            <p class="hall-text-xs line-clamp-2">Loreum ipsum donor amit,Loreum ipsum donor amit Loreum ipsum donor amit Loreum ipsum donor amit,Loreum ipsum donor amit Loreum ipsum donor amit Loreum ipsum donor amit,Loreum ipsum donor amit Loreum ipsum donor amit</p>
                        </div>
                    </div>
                    <a href="#" class="btn live">Live Join Now</a>
                    <!-- <a href="#" class="btn h-soon">Happening Soon</a> -->
                    <!-- <a href="#" class="btn ended">Session Ended !</a> -->
                </div>
            </div>
            <!-- list 2 -->
            <div class="hall-list d-flex flex-column flex-md-row mb-3">
                <div class="time d-flex flex-md-column align-items-center justify-content-center p-2 p-md-1" style="gap: 12px;">
                    <?= icon('clock-solid', '#ffffff', 32) ?>
                    <p>10 am to 11 am</p>
                </div>
                <div class="info d-flex flex-column flex-md-row align-items-md-center justify-content-between px-3 px-lg-5 py-4" style="flex: 1; border: 1px solid #d3d3d3; gap: 12px;">
                    <div class="d-flex align-items-center" style="gap: 16px;">
                        <div>
                            <img class="hall-list-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/profile_room_2.png" alt="">
                        </div>
                        <div>
                            <p class="spk-title">Keynote Speaker</p>
                            <h4 class="spk-name py-2">Speaker 1</h4>
                            <p class="spk-position">VP Marketing at TamQ</p>
                        </div>
                    </div>
                    <!-- <a href="#" class="btn live">Live Join Now</a> -->
                    <a href="#" class="btn h-soon">Happening Soon</a>
                    <!-- <a href="#" class="btn ended">Session Ended !</a> -->
                </div>
            </div>
            <!-- list 3 -->
            <div class="hall-list d-flex flex-column flex-md-row mb-3">
                <div class="time d-flex flex-md-column align-items-center justify-content-center p-2 p-md-1" style="gap: 12px;">
                    <?= icon('clock-solid', '#ffffff', 32) ?>
                    <p>10 am to 11 am</p>
                </div>
                <div class="info d-flex flex-column flex-md-row align-items-md-center justify-content-between px-3 px-lg-5 py-4" style="flex: 1; border: 1px solid #d3d3d3; gap: 12px;">
                    <div class="d-flex align-items-center" style="gap: 16px;">
                        <div>
                            <img class="hall-list-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/profile_room_1.png" alt="">
                        </div>
                        <div>
                            <p class="spk-title">Keynote Speaker</p>
                            <h4 class="spk-name py-2">Speaker 1</h4>
                            <p class="spk-position">VP Marketing at TamQ</p>
                        </div>
                    </div>
                    <!-- <a href="#" class="btn h-soon">Happening Soon</a> -->
                    <!-- <a href="#" class="btn live">Live Join Now</a> -->
                    <a href="#" class="btn ended">Session Ended !</a>
                </div>
            </div>

            <!-- become a sponsor dark banner -->
             <div class="sponsor-dark-banner px-3 px-lg-5 pb-3 pt-4">
                <h4 class="sponsor-title mb-2">Become a Sponsor !</h4>
                <p class="sponsor-desc mb-3">Exhibit your products or services and secure a speaker slot to showcase your expertise</p>
                <a href="#" class="btn sponsor-btn">Sponsor Now</a>
             </div>
        </div>

        <div class="hall-side-list-con d-flex flex-wrap justify-content-center justify-content-xl-start align-items-start">
            <div class="others-in-event mb-3">
                <p class="dark-bg-title py-3 pl-3 pl-lg-5 pr-3">Exhibitors in the event !</p>
                <div class="px-3 px-xl-4">
                    <!-- list 1 -->
                    <div class="side-card-list">
                        <div class="logo-cont px-3 d-flex align-items-center" style="background-color: #00762B;">
                            <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/hall-logo-1.png" alt="">
                        </div>
                        <div class="py-4">
                            <h4 class="side-card-title mb-2">Exhibitor One LLC</h4>
                            <p class="side-card-location">
                                <?= icon('location', '#000000', 15) ?>
                                <span>Tokyo, Japan</span>
                            </p>
                        </div>
                    </div>
                     <!-- list 2 -->
                    <div class="side-card-list">
                        <div class="logo-cont px-3 d-flex align-items-center" style="background-color: #1897C9;">
                            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M24 12C24 18.6274 18.6274 24 12 24H0V12C0 5.37258 5.37258 0 12 0C18.6274 0 24 5.37258 24 12Z" fill="white"/>
                                <path d="M24 36C24 29.3726 29.3726 24 36 24H48V36C48 42.6274 42.6274 48 36 48C29.3726 48 24 42.6274 24 36Z" fill="white"/>
                                <path d="M0 36C0 42.6274 5.37258 48 12 48H24V36C24 29.3726 18.6274 24 12 24C5.37258 24 0 29.3726 0 36Z" fill="white"/>
                                <path d="M48 12C48 5.37258 42.6274 0 36 0H24V12C24 18.6274 29.3726 24 36 24C42.6274 24 48 18.6274 48 12Z" fill="white"/>
                            </svg>

                        </div>
                        <div class="py-4">
                            <h4 class="side-card-title mb-2">Exhibitor Two LLC</h4>
                            <p class="side-card-location">
                                <?= icon('location', '#000000', 15) ?>
                                <span>Tokyo, Japan</span>
                            </p>
                        </div>
                    </div>
                     <!-- list 3 -->
                    <div class="side-card-list">
                        <div class="logo-cont px-3 d-flex align-items-center" style="background-color: #BC1558;">
                            <svg width="57" height="57" viewBox="0 0 57 57" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M30.9986 46.5331C31.0488 40.7341 29.7469 36.0184 28.0909 36.0001C26.4348 35.9819 25.0515 40.668 25.0014 46.4669C24.9512 52.2659 26.2531 56.9818 27.9091 56.9999C29.5652 57.0183 30.9485 52.3321 30.9986 46.5331Z" fill="white"/>
                                <path d="M20.6445 45.2606C23.6182 40.6689 24.8448 36.1584 23.3843 35.1861C21.9237 34.2138 18.3291 37.1478 15.3555 41.7394C12.3818 46.3311 11.1552 50.8414 12.6157 51.8139C14.0763 52.7862 17.6708 49.8522 20.6445 45.2606Z" fill="white"/>
                                <path d="M11.8908 39.2623C16.8219 36.8935 20.2196 33.6835 19.48 32.0924C18.7403 30.5014 14.1433 31.1318 9.21239 33.5005C4.28134 35.8693 0.883653 39.0792 1.6232 40.6704C2.36291 42.2614 6.95979 41.631 11.8908 39.2623Z" fill="white"/>
                                <path d="M20.9869 26.7225C21.2744 24.7293 16.8123 22.3422 11.0206 21.3909C5.22883 20.4396 0.300636 21.2843 0.0131475 23.2776C-0.274341 25.2709 4.18773 27.6578 9.97949 28.6091C15.7712 29.5604 20.6994 28.7157 20.9869 26.7225Z" fill="white"/>
                                <path d="M21.7109 22.5436C22.846 21.1242 20.5378 16.8198 16.5553 12.9297C12.5728 9.03962 8.42422 7.03682 7.28912 8.45635C6.15401 9.87591 8.46226 14.1802 12.4448 18.0703C16.4272 21.9605 20.5759 23.9632 21.7109 22.5436Z" fill="white"/>
                                <path d="M26.4679 19.958C28.336 19.4561 28.5216 14.5908 26.8825 9.09123C25.2434 3.5916 22.4002 -0.459868 20.5321 0.042026C18.664 0.54392 18.4784 5.40911 20.1175 10.9088C21.7566 16.4083 24.5998 20.4599 26.4679 19.958Z" fill="white"/>
                                <path d="M36.7725 11.9769C38.4784 6.48089 38.3962 1.58809 36.5888 1.04856C34.7814 0.509022 31.9333 4.52705 30.2274 10.0231C28.5216 15.5191 28.6038 20.4119 30.4112 20.9514C32.2186 21.491 35.0667 17.473 36.7725 11.9769Z" fill="white"/>
                                <path d="M44.4813 18.4836C48.4753 14.8702 50.8259 10.8291 49.7316 9.45743C48.6373 8.08577 44.5125 9.90306 40.5187 13.5163C36.5247 17.1297 34.1741 21.1709 35.2684 22.5426C36.3627 23.9142 40.4875 22.097 44.4813 18.4836Z" fill="white"/>
                                <path d="M46.9526 28.6937C52.7462 27.8475 57.2401 25.5077 56.9901 23.4677C56.7402 21.4277 51.8409 20.4601 46.0474 21.3062C40.2539 22.1525 35.7599 24.4922 36.0099 26.5323C36.26 28.5722 41.1591 29.54 46.9526 28.6937Z" fill="white"/>
                                <path d="M54.892 40.1185C55.676 38.5533 52.1066 35.4404 46.9195 33.1658C41.7325 30.8911 36.8919 30.3162 36.108 31.8814C35.324 33.4468 38.8934 36.5596 44.0805 38.8343C49.2675 41.1088 54.1081 41.6839 54.892 40.1185Z" fill="white"/>
                                <path d="M44.2824 51.8288C45.9204 50.8961 44.6594 46.411 41.4659 41.8111C38.2724 37.2112 34.3556 34.2385 32.7176 35.1712C31.0796 36.1039 32.3406 40.589 35.5341 45.1889C38.7276 49.7888 42.6444 52.7616 44.2824 51.8288Z" fill="white"/>
                            </svg>
                        </div>
                        <div class="py-4">
                            <h4 class="side-card-title mb-2">Exhibitor Three LLC</h4>
                            <p class="side-card-location">
                                <?= icon('location', '#000000', 15) ?>
                                <span>Tokyo, Japan</span>
                            </p>
                        </div>
                    </div>
                    

                    <div class="d-flex justify-content-end my-1">
                        <a href="<?php echo TAOH_SITE_URL_ROOT.'/events/eventshallexhibitors'; ?>" class="all-link">View all Exhibitors</a>
                    </div>
                </div>
            </div>


            <div class="others-in-event pb-2 mb-3">
                <p class="dark-bg-title py-3 pl-3 pl-lg-5 pr-3">Find Exclusive Offers in the event !</p>
                <div class="container px-3 px-xl-4">
                    <!-- Bootstrap Carousel -->
                    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                        <!-- Indicators (Dots) -->
                        <ol class="carousel-indicators custom" style="margin-bottom: -5px;">
                        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                        <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                        <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                        </ol>
                        
                        <!-- Carousel Inner -->
                        <div class="offer-carousel carousel-inner pt-3 pb-5">
                            <div class="carousel-item active">
                                <div class="d-flex justify-content-center" style="gap: 12px;">
                                    <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/Rectangle 561.png" class="d-block" alt="...">
                                    <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/Rectangle 561.png" class="d-block" alt="...">
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="d-flex justify-content-center" style="gap: 12px;">
                                    <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/Rectangle 561.png" class="d-block" alt="...">
                                    <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/Rectangle 561.png" class="d-block" alt="...">
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="d-flex justify-content-center" style="gap: 12px;">
                                    <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/Rectangle 561.png" class="d-block" alt="...">
                                    <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/Rectangle 561.png" class="d-block" alt="...">
                                </div>
                            </div>
                        </div>

                        <!-- Controls -->
                        <!-- <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php taoh_get_footer(); ?>