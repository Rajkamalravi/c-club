<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/raj/assets/icons/icons.php';
taoh_get_header();
?>

<div class="bg-white classifieds">
    <div class="classified-banner">
        <h4 class="text-center px-3">Discover Talent, Recruiters & Services</h4>
    </div>
    <div class="container">
        <!-- Nav Tabs -->
        <!-- <ul class="nav nav-tabs d-flex justify-content-between mb-5" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="recruiters-tab" data-toggle="tab" href="#recruiters" role="tab" aria-controls="recruiters" aria-selected="true">Recruiters</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="professionals-tab" data-toggle="tab" href="#professionals" role="tab" aria-controls="professionals" aria-selected="false">Professionals</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="service-tab" data-toggle="tab" href="#service" role="tab" aria-controls="service" aria-selected="false">Service Providers</a>
            </li>
        </ul> -->

        <!-- Tab Content -->
        <!-- <div class="tab-content pt-3 pb-5" id="myTabContent">
            <div class="tab-pane fade show active" id="recruiters" role="tabpanel" aria-labelledby="recruiters-tab">
                <p class="text-md text-center mb-4 pb-1">Connect with a recruiter ! Search with title,company name or location</p>
                <div class="d-flex flex-column flex-sm-row recruiter-search mb-5">
                    <input type="text" class="form-control">
                    <button type="button" class="btn search-btn">Search</button>
                </div>

                <div class="classifieds-card-container recruiters d-flex justify-content-center flex-wrap" style="gap: 40px; max-width: 1200px; margin: auto;">

                    <div class="classifieds-card d-flex flex-column justify-content-between">
                        <div>
                            <div class="classifieds-bg-banner"></div>
                            <div class="d-flex justify-content-center">
                                <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                            </div>
                            <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                            <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                                <a href="">
                                    <?= icon('info', '#7E7E7E', 12) ?>
                                </a>
                                <a href="">
                                    <?= icon('user-plus', '#7E7E7E', 14) ?>
                                </a>
                            </div>
                            <div class="px-3">
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('location', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('building', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('briefcase', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Admin Manager</span>
                                </div>
                                <div class=" d-flex align-items-center flex-wrap py-2" style="gap: 6px;">

                                    <div class="svg-circle" style="margin-right: 6px;">
                                        <?= icon('grid', '#ffffff', 11) ?>
                                    </div>

                                    <a href="#" class="btn skill-b">Budgeting</a>
                                    <a href="#" class="btn skill-b">Maintenance</a>
                                    <a href="#" class="btn skill-b">Book Keeping</a>

                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center py-3 px-3">
                            <a href="#" class="btn connect-dark-btn">Connect</a>
                        </div>


                        <p class="hiring">
                            Hiring
                        </p>
                    </div>

                    <div class="classifieds-card d-flex flex-column justify-content-between">
                        <div>
                            <div class="classifieds-bg-banner"></div>
                            <div class="d-flex justify-content-center">
                                <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                            </div>
                            <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                            <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                                <a href="">
                                    <?= icon('info', '#7E7E7E', 12) ?>
                                </a>
                                <a href="">
                                    <?= icon('user-plus', '#7E7E7E', 14) ?>
                                </a>
                            </div>
                            <div class="px-3">
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('location', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('building', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('briefcase', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Admin Manager</span>
                                </div>

                            </div>
                        </div>

                        <div class="d-flex justify-content-center py-3 px-3">
                            <a href="#" class="btn connect-dark-btn">Connect</a>
                        </div>

                        <p class="hiring">
                            Hiring
                        </p>
                    </div>

                    <div class="classifieds-card d-flex flex-column justify-content-between">
                        <div>
                            <div class="classifieds-bg-banner"></div>
                            <div class="d-flex justify-content-center">
                                <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                            </div>
                            <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                            <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                                <a href="">
                                    <?= icon('info', '#7E7E7E', 12) ?>
                                </a>
                                <a href="">
                                    <?= icon('user-plus', '#7E7E7E', 14) ?>
                                </a>
                            </div>
                            <div class="px-3">
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('location', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('building', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('briefcase', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Admin Manager</span>
                                </div>

                            </div>
                        </div>

                        <div class="d-flex justify-content-center py-3 px-3">
                            <a href="#" class="btn connect-dark-btn">Connect</a>
                        </div>

                        <p class="hiring">
                            Hiring
                        </p>
                    </div>

                    <div class="classifieds-card d-flex flex-column justify-content-between">
                        <div>
                            <div class="classifieds-bg-banner"></div>
                            <div class="d-flex justify-content-center">
                                <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                            </div>
                            <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                            <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                                <a href="">
                                    <?= icon('info', '#7E7E7E', 12) ?>
                                </a>
                                <a href="">
                                    <?= icon('user-plus', '#7E7E7E', 14) ?>
                                </a>
                            </div>
                            <div class="px-3">
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('location', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('building', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('briefcase', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Admin Manager</span>
                                </div>
                                <div class=" d-flex align-items-center flex-wrap py-2" style="gap: 6px;">

                                    <div class="svg-circle" style="margin-right: 6px;">
                                        <?= icon('grid', '#ffffff', 11) ?>
                                    </div>

                                    <a href="#" class="btn skill-b">Budgeting</a>
                                    <a href="#" class="btn skill-b">Maintenance</a>
                                    <a href="#" class="btn skill-b">Book Keeping</a>

                                </div>

                            </div>
                        </div>

                        <div class="d-flex justify-content-center py-3 px-3">
                            <a href="#" class="btn connect-dark-btn">Connect</a>
                        </div>


                        <p class="hiring">
                            Hiring
                        </p>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="professionals" role="tabpanel" aria-labelledby="professionals-tab">
                <p class="text-md text-center mb-4 pb-1">Connect with a recruiter ! Search with title,company name or location</p>
                <div class="d-flex flex-column flex-sm-row recruiter-search mb-5">
                    <input type="text" class="form-control">
                    <button type="button" class="btn search-btn">Search</button>
                </div>

                <div class="classifieds-card-container professionals d-flex justify-content-center flex-wrap" style="gap: 40px; max-width: 1200px; margin: auto;">

                    <div class="classifieds-card d-flex flex-column justify-content-between">
                        <div>
                            <div class="classifieds-bg-banner"></div>
                            <div class="d-flex justify-content-center">
                                <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                            </div>
                            <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                            <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                                <a href="">
                                    <?= icon('info', '#7E7E7E', 12) ?>
                                </a>
                                <a href="">
                                    <?= icon('user-plus', '#7E7E7E', 14) ?>
                                </a>
                            </div>
                            <div class="px-3">
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('location', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('building', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('briefcase', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Admin Manager</span>
                                </div>
                                <div class=" d-flex align-items-center flex-wrap py-2" style="gap: 6px;">

                                    <div class="svg-circle" style="margin-right: 6px;">
                                        <?= icon('grid', '#ffffff', 11) ?>
                                    </div>

                                    <a href="#" class="btn skill-b">Budgeting</a>
                                    <a href="#" class="btn skill-b">Maintenance</a>
                                    <a href="#" class="btn skill-b">Book Keeping</a>

                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center py-3 px-3">
                            <a href="#" class="btn connect-dark-btn">Connect</a>
                        </div>


                        <p class="hiring">
                            Hiring
                        </p>
                    </div>

                    <div class="classifieds-card d-flex flex-column justify-content-between">
                        <div>
                            <div class="classifieds-bg-banner"></div>
                            <div class="d-flex justify-content-center">
                                <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                            </div>
                            <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                            <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                                <a href="">
                                    <?= icon('info', '#7E7E7E', 12) ?>
                                </a>
                                <a href="">
                                    <?= icon('user-plus', '#7E7E7E', 14) ?>
                                </a>
                            </div>
                            <div class="px-3">
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('location', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('building', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('briefcase', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Admin Manager</span>
                                </div>

                            </div>
                        </div>

                        <div class="d-flex justify-content-center py-3 px-3">
                            <a href="#" class="btn connect-dark-btn">Connect</a>
                        </div>


                        <p class="hiring">
                            Hiring
                        </p>
                    </div>

                    <div class="classifieds-card d-flex flex-column justify-content-between">
                        <div>
                            <div class="classifieds-bg-banner"></div>
                            <div class="d-flex justify-content-center">
                                <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                            </div>
                            <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                            <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                                <a href="">
                                    <?= icon('info', '#7E7E7E', 12) ?>
                                </a>
                                <a href="">
                                    <?= icon('user-plus', '#7E7E7E', 14) ?>
                                </a>
                            </div>
                            <div class="px-3">
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('location', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('building', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('briefcase', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Admin Manager</span>
                                </div>

                            </div>
                        </div>

                        <div class="d-flex justify-content-center py-3 px-3">
                            <a href="#" class="btn connect-dark-btn">Connect</a>
                        </div>


                        <p class="hiring">
                            Hiring
                        </p>
                    </div>

                    <div class="classifieds-card d-flex flex-column justify-content-between">
                        <div>
                            <div class="classifieds-bg-banner"></div>
                            <div class="d-flex justify-content-center">
                                <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                            </div>
                            <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                            <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                                <a href="">
                                    <?= icon('info', '#7E7E7E', 12) ?>
                                </a>
                                <a href="">
                                    <?= icon('user-plus', '#7E7E7E', 14) ?>
                                </a>
                            </div>
                            <div class="px-3">
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('location', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('building', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('briefcase', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Admin Manager</span>
                                </div>
                                <div class=" d-flex align-items-center flex-wrap py-2" style="gap: 6px;">

                                    <div class="svg-circle" style="margin-right: 6px;">
                                        <?= icon('grid', '#ffffff', 11) ?>
                                    </div>

                                    <a href="#" class="btn skill-b">Budgeting</a>
                                    <a href="#" class="btn skill-b">Maintenance</a>
                                    <a href="#" class="btn skill-b">Book Keeping</a>

                                </div>

                            </div>
                        </div>

                        <div class="d-flex justify-content-center py-3 px-3">
                            <a href="#" class="btn connect-dark-btn">Connect</a>
                        </div>


                        <p class="hiring">
                            Hiring
                        </p>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="service" role="tabpanel" aria-labelledby="service-tab">
                <p class="text-md text-center mb-4 pb-1">Connect with a recruiter ! Search with title,company name or location</p>
                <div class="d-flex flex-column flex-sm-row recruiter-search mb-5">
                    <input type="text" class="form-control">
                    <button type="button" class="btn search-btn">Search</button>
                </div>

                <div class="classifieds-card-container service d-flex justify-content-center flex-wrap" style="gap: 40px; max-width: 1200px; margin: auto;">

                    <div class="classifieds-card d-flex flex-column justify-content-between">
                        <div>
                            <div class="classifieds-bg-banner"></div>
                            <div class="d-flex justify-content-center">
                                <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                            </div>
                            <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                            <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                                <a href="">
                                    <?= icon('info', '#7E7E7E', 12) ?>
                                </a>
                                <a href="">
                                    <?= icon('user-plus', '#7E7E7E', 14) ?>
                                </a>
                            </div>
                            <div class="px-3">
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('location', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('building', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('briefcase', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Admin Manager</span>
                                </div>
                                <div class=" d-flex align-items-center flex-wrap py-2" style="gap: 6px;">

                                    <div class="svg-circle" style="margin-right: 6px;">
                                        <?= icon('grid', '#ffffff', 11) ?>
                                    </div>

                                    <a href="#" class="btn skill-b">Budgeting</a>
                                    <a href="#" class="btn skill-b">Maintenance</a>
                                    <a href="#" class="btn skill-b">Book Keeping</a>

                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center py-3 px-3">
                            <a href="#" class="btn connect-dark-btn">Connect</a>
                        </div>


                        <p class="hiring">
                            Hiring
                        </p>
                    </div>

                    <div class="classifieds-card d-flex flex-column justify-content-between">
                        <div>
                            <div class="classifieds-bg-banner"></div>
                            <div class="d-flex justify-content-center">
                                <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                            </div>
                            <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                            <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                                <a href="">
                                    <?= icon('info', '#7E7E7E', 12) ?>
                                </a>
                                <a href="">
                                    <?= icon('user-plus', '#7E7E7E', 14) ?>
                                </a>
                            </div>
                            <div class="px-3">
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('location', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('building', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('briefcase', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Admin Manager</span>
                                </div>

                            </div>
                        </div>

                        <div class="d-flex justify-content-center py-3 px-3">
                            <a href="#" class="btn connect-dark-btn">Connect</a>
                        </div>


                        <p class="hiring">
                            Hiring
                        </p>
                    </div>

                    <div class="classifieds-card d-flex flex-column justify-content-between">
                        <div>
                            <div class="classifieds-bg-banner"></div>
                            <div class="d-flex justify-content-center">
                                <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                            </div>
                            <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                            <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                                <a href="">
                                    <?= icon('info', '#7E7E7E', 12) ?>
                                </a>
                                <a href="">
                                    <?= icon('user-plus', '#7E7E7E', 14) ?>
                                </a>
                            </div>
                            <div class="px-3">
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('location', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('building', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('briefcase', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Admin Manager</span>
                                </div>

                            </div>
                        </div>

                        <div class="d-flex justify-content-center py-3 px-3">
                            <a href="#" class="btn connect-dark-btn">Connect</a>
                        </div>


                        <p class="hiring">
                            Hiring
                        </p>
                    </div>

                    <div class="classifieds-card d-flex flex-column justify-content-between">
                        <div>
                            <div class="classifieds-bg-banner"></div>
                            <div class="d-flex justify-content-center">
                                <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                            </div>
                            <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                            <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                                <a href="">
                                    <?= icon('info', '#7E7E7E', 12) ?>
                                </a>
                                <a href="">
                                    <?= icon('user-plus', '#7E7E7E', 14) ?>
                                </a>
                            </div>
                            <div class="px-3">
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('location', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('building', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                                </div>
                                <div class=" d-flex align-items-center" style="gap: 12px;">
                                    <div class="svg-circle">
                                        <?= icon('briefcase', '#ffffff', 10) ?>
                                    </div>
                                    <span class="classifieds-card-text py-2">Admin Manager</span>
                                </div>
                                <div class=" d-flex align-items-center flex-wrap py-2" style="gap: 6px;">

                                    <div class="svg-circle" style="margin-right: 6px;">
                                        <?= icon('grid', '#ffffff', 11) ?>
                                    </div>

                                    <a href="#" class="btn skill-b">Budgeting</a>
                                    <a href="#" class="btn skill-b">Maintenance</a>
                                    <a href="#" class="btn skill-b">Book Keeping</a>

                                </div>

                            </div>
                        </div>

                        <div class="d-flex justify-content-center py-3 px-3">
                            <a href="#" class="btn connect-dark-btn">Connect</a>
                        </div>


                        <p class="hiring">
                            Hiring
                        </p>
                    </div>
                </div>
            </div>
        </div> -->
    </div>


    <!-- new changes -->
    <div class="container">
        <div class="d-flex flex-column flex-sm-row recruiter-search mb-5">
            <input type="text" class="form-control" placeholder="Search with title, company name or location and Skills">
            <button type="button" class="btn search-btn">Search</button>
        </div>
    </div>

    <div class="d-flex container align-items-start" style="gap: 20px; position: relative;">
        <div class="filter mb-5" style="position: sticky; top: 100px;">
            <div class="filter-label">
                <svg width="30" height="20" viewBox="0 0 30 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <line y1="4.5" x2="30" y2="4.5" stroke="white"/>
                    <line y1="10.5" x2="30" y2="10.5" stroke="white"/>
                    <line y1="17.5" x2="30" y2="17.5" stroke="white"/>
                    <circle cx="10" cy="4" r="4" fill="white"/>
                    <ellipse cx="22.5" cy="10" rx="3.5" ry="4" fill="white"/>
                    <ellipse cx="10" cy="16.5" rx="4" ry="3.5" fill="white"/>
                </svg>
                <span>Filters</span>
            </div>
            <div class="px-3 px-xl-4 my-4">
                <div class="form-group mb-4">
                    <label for="role" class="label dark">
                        <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 0V17H17V0H0ZM12.01 12.3402L8.5 15.706L4.98996 12.3402L7.4375 5.35804L4.98996 2.07187H12.0063L9.5625 5.35804L12.01 12.3402Z" fill="black"/>
                        </svg>
                        <span>Filter by Role</span>
                    </label>
                    <div class="px-xl-4">
                        <select name="role" id="role" class="form-control">
                            <option value="">Select</option>
                            <option value="">Employer</option>
                            <option value="">Professional</option>
                            <option value="">Service Providers</option>
                        </select>
                    </div>
                </div>
                <div class="form-group mb-4">
                    <label for="role" class="label dark">
                        <svg width="17" height="23" viewBox="0 0 17 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.54922 22.4832C11.8203 19.5917 17 12.5837 17 8.64738C17 3.8733 13.1927 0 8.5 0C3.80729 0 0 3.8733 0 8.64738C0 12.5837 5.17969 19.5917 7.45078 22.4832C7.99531 23.1723 9.00469 23.1723 9.54922 22.4832ZM8.5 5.76492C9.25145 5.76492 9.97212 6.06861 10.5035 6.60917C11.0348 7.14974 11.3333 7.8829 11.3333 8.64738C11.3333 9.41185 11.0348 10.145 10.5035 10.6856C9.97212 11.2262 9.25145 11.5298 8.5 11.5298C7.74855 11.5298 7.02788 11.2262 6.49653 10.6856C5.96518 10.145 5.66667 9.41185 5.66667 8.64738C5.66667 7.8829 5.96518 7.14974 6.49653 6.60917C7.02788 6.06861 7.74855 5.76492 8.5 5.76492Z" fill="black"/>
                        </svg>
                        <span>Filter By Location</span>
                    </label>
                    <div class="px-xl-4">
                        <select name="location" id="location" class="form-control">
                            <option value="">Select</option>
                        </select>
                    </div>
                </div>
                <div class="form-group mb-4">
                    <label for="role" class="label dark">
                        <svg width="17" height="23" viewBox="0 0 17 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.125 0C0.951823 0 0 0.96582 0 2.15625V20.8438C0 22.0342 0.951823 23 2.125 23H6.375V19.4062C6.375 18.2158 7.32682 17.25 8.5 17.25C9.67318 17.25 10.625 18.2158 10.625 19.4062V23H14.875C16.0482 23 17 22.0342 17 20.8438V2.15625C17 0.96582 16.0482 0 14.875 0H2.125ZM2.83333 10.7812C2.83333 10.3859 3.15208 10.0625 3.54167 10.0625H4.95833C5.34792 10.0625 5.66667 10.3859 5.66667 10.7812V12.2188C5.66667 12.6141 5.34792 12.9375 4.95833 12.9375H3.54167C3.15208 12.9375 2.83333 12.6141 2.83333 12.2188V10.7812ZM7.79167 10.0625H9.20833C9.59792 10.0625 9.91667 10.3859 9.91667 10.7812V12.2188C9.91667 12.6141 9.59792 12.9375 9.20833 12.9375H7.79167C7.40208 12.9375 7.08333 12.6141 7.08333 12.2188V10.7812C7.08333 10.3859 7.40208 10.0625 7.79167 10.0625ZM11.3333 10.7812C11.3333 10.3859 11.6521 10.0625 12.0417 10.0625H13.4583C13.8479 10.0625 14.1667 10.3859 14.1667 10.7812V12.2188C14.1667 12.6141 13.8479 12.9375 13.4583 12.9375H12.0417C11.6521 12.9375 11.3333 12.6141 11.3333 12.2188V10.7812ZM3.54167 4.3125H4.95833C5.34792 4.3125 5.66667 4.63594 5.66667 5.03125V6.46875C5.66667 6.86406 5.34792 7.1875 4.95833 7.1875H3.54167C3.15208 7.1875 2.83333 6.86406 2.83333 6.46875V5.03125C2.83333 4.63594 3.15208 4.3125 3.54167 4.3125ZM7.08333 5.03125C7.08333 4.63594 7.40208 4.3125 7.79167 4.3125H9.20833C9.59792 4.3125 9.91667 4.63594 9.91667 5.03125V6.46875C9.91667 6.86406 9.59792 7.1875 9.20833 7.1875H7.79167C7.40208 7.1875 7.08333 6.86406 7.08333 6.46875V5.03125ZM12.0417 4.3125H13.4583C13.8479 4.3125 14.1667 4.63594 14.1667 5.03125V6.46875C14.1667 6.86406 13.8479 7.1875 13.4583 7.1875H12.0417C11.6521 7.1875 11.3333 6.86406 11.3333 6.46875V5.03125C11.3333 4.63594 11.6521 4.3125 12.0417 4.3125Z" fill="black"/>
                        </svg>
                        <span>Filter By Company</span>
                    </label>
                    <div class="px-xl-4">
                        <select name="company" id="company" class="form-control">
                            <option value="">Select</option>
                        </select>
                    </div>
                </div>
                <div class="form-group mb-4">
                    <label for="role" class="label dark">
                        <svg width="17" height="15" viewBox="0 0 17 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.55556 0H9.44444C9.96684 0 10.3889 0.418945 10.3889 0.9375V2.8125C10.3889 3.33105 9.96684 3.75 9.44444 3.75H7.55556C7.03316 3.75 6.61111 3.33105 6.61111 2.8125V0.9375C6.61111 0.418945 7.03316 0 7.55556 0ZM1.88889 1.875H5.66667V3.28125C5.66667 4.05762 6.30122 4.6875 7.08333 4.6875H9.91667C10.6988 4.6875 11.3333 4.05762 11.3333 3.28125V1.875H15.1111C16.153 1.875 17 2.71582 17 3.75V13.125C17 14.1592 16.153 15 15.1111 15H1.88889C0.847049 15 0 14.1592 0 13.125V3.75C0 2.71582 0.847049 1.875 1.88889 1.875ZM5.19444 12.8115C5.19444 12.9844 5.33611 13.125 5.51024 13.125H11.4898C11.6639 13.125 11.8056 12.9844 11.8056 12.8115C11.8056 11.9473 11.1002 11.25 10.2325 11.25H6.76753C5.89687 11.25 5.19444 11.9502 5.19444 12.8115ZM8.5 10.3125C9.00096 10.3125 9.48141 10.115 9.83565 9.76332C10.1899 9.41169 10.3889 8.93478 10.3889 8.4375C10.3889 7.94022 10.1899 7.46331 9.83565 7.11167C9.48141 6.76004 9.00096 6.5625 8.5 6.5625C7.99904 6.5625 7.51859 6.76004 7.16435 7.11167C6.81012 7.46331 6.61111 7.94022 6.61111 8.4375C6.61111 8.93478 6.81012 9.41169 7.16435 9.76332C7.51859 10.115 7.99904 10.3125 8.5 10.3125Z" fill="black"/>
                        </svg>
                        <span>Filter By Designation</span>
                    </label>
                    <div class="px-xl-4">
                        <select name="designation" id="designation" class="form-control">
                            <option value="">Select</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="classifieds-card-container d-flex justify-content-center flex-wrap pb-5" style="gap: 20px;">
            <!-- professional -->
            <div class="classifieds-card d-flex flex-column justify-content-between">
                <div>
                    <div class="classifieds-bg-banner professionals"></div>
                    <div class="d-flex justify-content-center">
                        <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                    </div>
                    <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                    <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                        <a href="">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 12C7.5913 12 9.11742 11.3679 10.2426 10.2426C11.3679 9.11742 12 7.5913 12 6C12 4.4087 11.3679 2.88258 10.2426 1.75736C9.11742 0.632141 7.5913 0 6 0C4.4087 0 2.88258 0.632141 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 7.5913 0.632141 9.11742 1.75736 10.2426C2.88258 11.3679 4.4087 12 6 12ZM5.0625 7.875H5.625V6.375H5.0625C4.75078 6.375 4.5 6.12422 4.5 5.8125C4.5 5.50078 4.75078 5.25 5.0625 5.25H6.1875C6.49922 5.25 6.75 5.50078 6.75 5.8125V7.875H6.9375C7.24922 7.875 7.5 8.12578 7.5 8.4375C7.5 8.74922 7.24922 9 6.9375 9H5.0625C4.75078 9 4.5 8.74922 4.5 8.4375C4.5 8.12578 4.75078 7.875 5.0625 7.875ZM6 3C6.19891 3 6.38968 3.07902 6.53033 3.21967C6.67098 3.36032 6.75 3.55109 6.75 3.75C6.75 3.94891 6.67098 4.13968 6.53033 4.28033C6.38968 4.42098 6.19891 4.5 6 4.5C5.80109 4.5 5.61032 4.42098 5.46967 4.28033C5.32902 4.13968 5.25 3.94891 5.25 3.75C5.25 3.55109 5.32902 3.36032 5.46967 3.21967C5.61032 3.07902 5.80109 3 6 3Z" fill="#7E7E7E"/>
                            </svg>
                        </a>
                        <a href="">
                            <svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.1 2.88235C2.1 2.11791 2.395 1.38477 2.9201 0.844222C3.4452 0.303676 4.15739 0 4.9 0C5.64261 0 6.3548 0.303676 6.8799 0.844222C7.405 1.38477 7.7 2.11791 7.7 2.88235C7.7 3.6468 7.405 4.37994 6.8799 4.92048C6.3548 5.46103 5.64261 5.76471 4.9 5.76471C4.15739 5.76471 3.4452 5.46103 2.9201 4.92048C2.395 4.37994 2.1 3.6468 2.1 2.88235ZM0 10.8606C0 8.64255 1.74563 6.84559 3.90031 6.84559H5.89969C8.05437 6.84559 9.8 8.64255 9.8 10.8606C9.8 11.2299 9.50906 11.5294 9.15031 11.5294H0.649688C0.290938 11.5294 0 11.2299 0 10.8606ZM11.025 7.02573V5.58456H9.625C9.33406 5.58456 9.1 5.34361 9.1 5.04412C9.1 4.74462 9.33406 4.50368 9.625 4.50368H11.025V3.0625C11.025 2.76301 11.2591 2.52206 11.55 2.52206C11.8409 2.52206 12.075 2.76301 12.075 3.0625V4.50368H13.475C13.7659 4.50368 14 4.74462 14 5.04412C14 5.34361 13.7659 5.58456 13.475 5.58456H12.075V7.02573C12.075 7.32523 11.8409 7.56618 11.55 7.56618C11.2591 7.56618 11.025 7.32523 11.025 7.02573Z" fill="#7E7E7E"/>
                            </svg>
                        </a>
                    </div>
                    <div class="px-3">
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="8" height="11" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                        </div>
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="8" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 0C0.447917 0 0 0.461914 0 1.03125V9.96875C0 10.5381 0.447917 11 1 11H3V9.28125C3 8.71191 3.44792 8.25 4 8.25C4.55208 8.25 5 8.71191 5 9.28125V11H7C7.55208 11 8 10.5381 8 9.96875V1.03125C8 0.461914 7.55208 0 7 0H1ZM1.33333 5.15625C1.33333 4.96719 1.48333 4.8125 1.66667 4.8125H2.33333C2.51667 4.8125 2.66667 4.96719 2.66667 5.15625V5.84375C2.66667 6.03281 2.51667 6.1875 2.33333 6.1875H1.66667C1.48333 6.1875 1.33333 6.03281 1.33333 5.84375V5.15625ZM3.66667 4.8125H4.33333C4.51667 4.8125 4.66667 4.96719 4.66667 5.15625V5.84375C4.66667 6.03281 4.51667 6.1875 4.33333 6.1875H3.66667C3.48333 6.1875 3.33333 6.03281 3.33333 5.84375V5.15625C3.33333 4.96719 3.48333 4.8125 3.66667 4.8125ZM5.33333 5.15625C5.33333 4.96719 5.48333 4.8125 5.66667 4.8125H6.33333C6.51667 4.8125 6.66667 4.96719 6.66667 5.15625V5.84375C6.66667 6.03281 6.51667 6.1875 6.33333 6.1875H5.66667C5.48333 6.1875 5.33333 6.03281 5.33333 5.84375V5.15625ZM1.66667 2.0625H2.33333C2.51667 2.0625 2.66667 2.21719 2.66667 2.40625V3.09375C2.66667 3.28281 2.51667 3.4375 2.33333 3.4375H1.66667C1.48333 3.4375 1.33333 3.28281 1.33333 3.09375V2.40625C1.33333 2.21719 1.48333 2.0625 1.66667 2.0625ZM3.33333 2.40625C3.33333 2.21719 3.48333 2.0625 3.66667 2.0625H4.33333C4.51667 2.0625 4.66667 2.21719 4.66667 2.40625V3.09375C4.66667 3.28281 4.51667 3.4375 4.33333 3.4375H3.66667C3.48333 3.4375 3.33333 3.28281 3.33333 3.09375V2.40625ZM5.66667 2.0625H6.33333C6.51667 2.0625 6.66667 2.21719 6.66667 2.40625V3.09375C6.66667 3.28281 6.51667 3.4375 6.33333 3.4375H5.66667C5.48333 3.4375 5.33333 3.28281 5.33333 3.09375V2.40625C5.33333 2.21719 5.48333 2.0625 5.66667 2.0625Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                        </div>
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 0V10H10V0H0ZM7.06473 7.25893L5 9.23884L2.93527 7.25893L4.375 3.15179L2.93527 1.21875H7.0625L5.625 3.15179L7.06473 7.25893Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">Admin Manager</span>
                        </div>
                        <div class=" d-flex align-items-center flex-wrap py-2" style="gap: 6px;">

                            <div class="svg-circle" style="margin-right: 6px;">
                                <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.77617 0.333008C6.65371 0.126758 6.42812 0 6.1875 0C5.94688 0 5.72129 0.126758 5.59883 0.333008L3.53633 3.77051C3.40957 3.9832 3.40527 4.24746 3.52773 4.4623C3.6502 4.67715 3.87793 4.81035 4.125 4.81035H8.25C8.49707 4.81035 8.72695 4.67715 8.84727 4.4623C8.96758 4.24746 8.96543 3.9832 8.83867 3.77051L6.77617 0.333008ZM6.1875 6.70312V9.79688C6.1875 10.2717 6.57207 10.6562 7.04688 10.6562H10.1406C10.6154 10.6562 11 10.2717 11 9.79688V6.70312C11 6.22832 10.6154 5.84375 10.1406 5.84375H7.04688C6.57207 5.84375 6.1875 6.22832 6.1875 6.70312ZM2.75 11C3.47935 11 4.17882 10.7103 4.69454 10.1945C5.21027 9.67882 5.5 8.97935 5.5 8.25C5.5 7.52065 5.21027 6.82118 4.69454 6.30546C4.17882 5.78973 3.47935 5.5 2.75 5.5C2.02065 5.5 1.32118 5.78973 0.805456 6.30546C0.289731 6.82118 0 7.52065 0 8.25C0 8.97935 0.289731 9.67882 0.805456 10.1945C1.32118 10.7103 2.02065 11 2.75 11Z" fill="white"/>
                                </svg>
                            </div>

                            <a href="#" class="btn skill-b">Budgeting</a>
                            <a href="#" class="btn skill-b">Maintenance</a>
                            <a href="#" class="btn skill-b">Book Keeping</a>

                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center py-3 px-3">
                    <a href="#" class="btn connect-dark-btn">Message</a>
                </div>


                <!-- <p class="hiring">
                    Hiring
                </p> -->

                <p class="professional-badge">Professional</p>
            </div>
            <div class="classifieds-card d-flex flex-column justify-content-between">
                <div>
                    <div class="classifieds-bg-banner professionals"></div>
                    <div class="d-flex justify-content-center">
                        <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                    </div>
                    <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                    <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                        <a href="">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 12C7.5913 12 9.11742 11.3679 10.2426 10.2426C11.3679 9.11742 12 7.5913 12 6C12 4.4087 11.3679 2.88258 10.2426 1.75736C9.11742 0.632141 7.5913 0 6 0C4.4087 0 2.88258 0.632141 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 7.5913 0.632141 9.11742 1.75736 10.2426C2.88258 11.3679 4.4087 12 6 12ZM5.0625 7.875H5.625V6.375H5.0625C4.75078 6.375 4.5 6.12422 4.5 5.8125C4.5 5.50078 4.75078 5.25 5.0625 5.25H6.1875C6.49922 5.25 6.75 5.50078 6.75 5.8125V7.875H6.9375C7.24922 7.875 7.5 8.12578 7.5 8.4375C7.5 8.74922 7.24922 9 6.9375 9H5.0625C4.75078 9 4.5 8.74922 4.5 8.4375C4.5 8.12578 4.75078 7.875 5.0625 7.875ZM6 3C6.19891 3 6.38968 3.07902 6.53033 3.21967C6.67098 3.36032 6.75 3.55109 6.75 3.75C6.75 3.94891 6.67098 4.13968 6.53033 4.28033C6.38968 4.42098 6.19891 4.5 6 4.5C5.80109 4.5 5.61032 4.42098 5.46967 4.28033C5.32902 4.13968 5.25 3.94891 5.25 3.75C5.25 3.55109 5.32902 3.36032 5.46967 3.21967C5.61032 3.07902 5.80109 3 6 3Z" fill="#7E7E7E"/>
                            </svg>
                        </a>
                        <a href="">
                            <svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.1 2.88235C2.1 2.11791 2.395 1.38477 2.9201 0.844222C3.4452 0.303676 4.15739 0 4.9 0C5.64261 0 6.3548 0.303676 6.8799 0.844222C7.405 1.38477 7.7 2.11791 7.7 2.88235C7.7 3.6468 7.405 4.37994 6.8799 4.92048C6.3548 5.46103 5.64261 5.76471 4.9 5.76471C4.15739 5.76471 3.4452 5.46103 2.9201 4.92048C2.395 4.37994 2.1 3.6468 2.1 2.88235ZM0 10.8606C0 8.64255 1.74563 6.84559 3.90031 6.84559H5.89969C8.05437 6.84559 9.8 8.64255 9.8 10.8606C9.8 11.2299 9.50906 11.5294 9.15031 11.5294H0.649688C0.290938 11.5294 0 11.2299 0 10.8606ZM11.025 7.02573V5.58456H9.625C9.33406 5.58456 9.1 5.34361 9.1 5.04412C9.1 4.74462 9.33406 4.50368 9.625 4.50368H11.025V3.0625C11.025 2.76301 11.2591 2.52206 11.55 2.52206C11.8409 2.52206 12.075 2.76301 12.075 3.0625V4.50368H13.475C13.7659 4.50368 14 4.74462 14 5.04412C14 5.34361 13.7659 5.58456 13.475 5.58456H12.075V7.02573C12.075 7.32523 11.8409 7.56618 11.55 7.56618C11.2591 7.56618 11.025 7.32523 11.025 7.02573Z" fill="#7E7E7E"/>
                            </svg>
                        </a>
                    </div>
                    <div class="px-3">
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="8" height="11" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                        </div>
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="8" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 0C0.447917 0 0 0.461914 0 1.03125V9.96875C0 10.5381 0.447917 11 1 11H3V9.28125C3 8.71191 3.44792 8.25 4 8.25C4.55208 8.25 5 8.71191 5 9.28125V11H7C7.55208 11 8 10.5381 8 9.96875V1.03125C8 0.461914 7.55208 0 7 0H1ZM1.33333 5.15625C1.33333 4.96719 1.48333 4.8125 1.66667 4.8125H2.33333C2.51667 4.8125 2.66667 4.96719 2.66667 5.15625V5.84375C2.66667 6.03281 2.51667 6.1875 2.33333 6.1875H1.66667C1.48333 6.1875 1.33333 6.03281 1.33333 5.84375V5.15625ZM3.66667 4.8125H4.33333C4.51667 4.8125 4.66667 4.96719 4.66667 5.15625V5.84375C4.66667 6.03281 4.51667 6.1875 4.33333 6.1875H3.66667C3.48333 6.1875 3.33333 6.03281 3.33333 5.84375V5.15625C3.33333 4.96719 3.48333 4.8125 3.66667 4.8125ZM5.33333 5.15625C5.33333 4.96719 5.48333 4.8125 5.66667 4.8125H6.33333C6.51667 4.8125 6.66667 4.96719 6.66667 5.15625V5.84375C6.66667 6.03281 6.51667 6.1875 6.33333 6.1875H5.66667C5.48333 6.1875 5.33333 6.03281 5.33333 5.84375V5.15625ZM1.66667 2.0625H2.33333C2.51667 2.0625 2.66667 2.21719 2.66667 2.40625V3.09375C2.66667 3.28281 2.51667 3.4375 2.33333 3.4375H1.66667C1.48333 3.4375 1.33333 3.28281 1.33333 3.09375V2.40625C1.33333 2.21719 1.48333 2.0625 1.66667 2.0625ZM3.33333 2.40625C3.33333 2.21719 3.48333 2.0625 3.66667 2.0625H4.33333C4.51667 2.0625 4.66667 2.21719 4.66667 2.40625V3.09375C4.66667 3.28281 4.51667 3.4375 4.33333 3.4375H3.66667C3.48333 3.4375 3.33333 3.28281 3.33333 3.09375V2.40625ZM5.66667 2.0625H6.33333C6.51667 2.0625 6.66667 2.21719 6.66667 2.40625V3.09375C6.66667 3.28281 6.51667 3.4375 6.33333 3.4375H5.66667C5.48333 3.4375 5.33333 3.28281 5.33333 3.09375V2.40625C5.33333 2.21719 5.48333 2.0625 5.66667 2.0625Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                        </div>
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 0V10H10V0H0ZM7.06473 7.25893L5 9.23884L2.93527 7.25893L4.375 3.15179L2.93527 1.21875H7.0625L5.625 3.15179L7.06473 7.25893Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">Admin Manager</span>
                        </div>
                        <div class=" d-flex align-items-center flex-wrap py-2" style="gap: 6px;">

                            <div class="svg-circle" style="margin-right: 6px;">
                                <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.77617 0.333008C6.65371 0.126758 6.42812 0 6.1875 0C5.94688 0 5.72129 0.126758 5.59883 0.333008L3.53633 3.77051C3.40957 3.9832 3.40527 4.24746 3.52773 4.4623C3.6502 4.67715 3.87793 4.81035 4.125 4.81035H8.25C8.49707 4.81035 8.72695 4.67715 8.84727 4.4623C8.96758 4.24746 8.96543 3.9832 8.83867 3.77051L6.77617 0.333008ZM6.1875 6.70312V9.79688C6.1875 10.2717 6.57207 10.6562 7.04688 10.6562H10.1406C10.6154 10.6562 11 10.2717 11 9.79688V6.70312C11 6.22832 10.6154 5.84375 10.1406 5.84375H7.04688C6.57207 5.84375 6.1875 6.22832 6.1875 6.70312ZM2.75 11C3.47935 11 4.17882 10.7103 4.69454 10.1945C5.21027 9.67882 5.5 8.97935 5.5 8.25C5.5 7.52065 5.21027 6.82118 4.69454 6.30546C4.17882 5.78973 3.47935 5.5 2.75 5.5C2.02065 5.5 1.32118 5.78973 0.805456 6.30546C0.289731 6.82118 0 7.52065 0 8.25C0 8.97935 0.289731 9.67882 0.805456 10.1945C1.32118 10.7103 2.02065 11 2.75 11Z" fill="white"/>
                                </svg>
                            </div>

                            <a href="#" class="btn skill-b">Budgeting</a>
                            <a href="#" class="btn skill-b">Maintenance</a>
                            <a href="#" class="btn skill-b">Book Keeping</a>

                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center py-3 px-3">
                    <a href="#" class="btn connect-dark-btn">Message</a>
                </div>


                <!-- <p class="hiring">
                    Hiring
                </p> -->

                <p class="professional-badge">Professional</p>
            </div>
            <div class="classifieds-card d-flex flex-column justify-content-between">
                <div>
                    <div class="classifieds-bg-banner professionals"></div>
                    <div class="d-flex justify-content-center">
                        <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                    </div>
                    <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                    <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                        <a href="">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 12C7.5913 12 9.11742 11.3679 10.2426 10.2426C11.3679 9.11742 12 7.5913 12 6C12 4.4087 11.3679 2.88258 10.2426 1.75736C9.11742 0.632141 7.5913 0 6 0C4.4087 0 2.88258 0.632141 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 7.5913 0.632141 9.11742 1.75736 10.2426C2.88258 11.3679 4.4087 12 6 12ZM5.0625 7.875H5.625V6.375H5.0625C4.75078 6.375 4.5 6.12422 4.5 5.8125C4.5 5.50078 4.75078 5.25 5.0625 5.25H6.1875C6.49922 5.25 6.75 5.50078 6.75 5.8125V7.875H6.9375C7.24922 7.875 7.5 8.12578 7.5 8.4375C7.5 8.74922 7.24922 9 6.9375 9H5.0625C4.75078 9 4.5 8.74922 4.5 8.4375C4.5 8.12578 4.75078 7.875 5.0625 7.875ZM6 3C6.19891 3 6.38968 3.07902 6.53033 3.21967C6.67098 3.36032 6.75 3.55109 6.75 3.75C6.75 3.94891 6.67098 4.13968 6.53033 4.28033C6.38968 4.42098 6.19891 4.5 6 4.5C5.80109 4.5 5.61032 4.42098 5.46967 4.28033C5.32902 4.13968 5.25 3.94891 5.25 3.75C5.25 3.55109 5.32902 3.36032 5.46967 3.21967C5.61032 3.07902 5.80109 3 6 3Z" fill="#7E7E7E"/>
                            </svg>
                        </a>
                        <a href="">
                            <svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.1 2.88235C2.1 2.11791 2.395 1.38477 2.9201 0.844222C3.4452 0.303676 4.15739 0 4.9 0C5.64261 0 6.3548 0.303676 6.8799 0.844222C7.405 1.38477 7.7 2.11791 7.7 2.88235C7.7 3.6468 7.405 4.37994 6.8799 4.92048C6.3548 5.46103 5.64261 5.76471 4.9 5.76471C4.15739 5.76471 3.4452 5.46103 2.9201 4.92048C2.395 4.37994 2.1 3.6468 2.1 2.88235ZM0 10.8606C0 8.64255 1.74563 6.84559 3.90031 6.84559H5.89969C8.05437 6.84559 9.8 8.64255 9.8 10.8606C9.8 11.2299 9.50906 11.5294 9.15031 11.5294H0.649688C0.290938 11.5294 0 11.2299 0 10.8606ZM11.025 7.02573V5.58456H9.625C9.33406 5.58456 9.1 5.34361 9.1 5.04412C9.1 4.74462 9.33406 4.50368 9.625 4.50368H11.025V3.0625C11.025 2.76301 11.2591 2.52206 11.55 2.52206C11.8409 2.52206 12.075 2.76301 12.075 3.0625V4.50368H13.475C13.7659 4.50368 14 4.74462 14 5.04412C14 5.34361 13.7659 5.58456 13.475 5.58456H12.075V7.02573C12.075 7.32523 11.8409 7.56618 11.55 7.56618C11.2591 7.56618 11.025 7.32523 11.025 7.02573Z" fill="#7E7E7E"/>
                            </svg>
                        </a>
                    </div>
                    <div class="px-3">
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="8" height="11" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                        </div>
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="8" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 0C0.447917 0 0 0.461914 0 1.03125V9.96875C0 10.5381 0.447917 11 1 11H3V9.28125C3 8.71191 3.44792 8.25 4 8.25C4.55208 8.25 5 8.71191 5 9.28125V11H7C7.55208 11 8 10.5381 8 9.96875V1.03125C8 0.461914 7.55208 0 7 0H1ZM1.33333 5.15625C1.33333 4.96719 1.48333 4.8125 1.66667 4.8125H2.33333C2.51667 4.8125 2.66667 4.96719 2.66667 5.15625V5.84375C2.66667 6.03281 2.51667 6.1875 2.33333 6.1875H1.66667C1.48333 6.1875 1.33333 6.03281 1.33333 5.84375V5.15625ZM3.66667 4.8125H4.33333C4.51667 4.8125 4.66667 4.96719 4.66667 5.15625V5.84375C4.66667 6.03281 4.51667 6.1875 4.33333 6.1875H3.66667C3.48333 6.1875 3.33333 6.03281 3.33333 5.84375V5.15625C3.33333 4.96719 3.48333 4.8125 3.66667 4.8125ZM5.33333 5.15625C5.33333 4.96719 5.48333 4.8125 5.66667 4.8125H6.33333C6.51667 4.8125 6.66667 4.96719 6.66667 5.15625V5.84375C6.66667 6.03281 6.51667 6.1875 6.33333 6.1875H5.66667C5.48333 6.1875 5.33333 6.03281 5.33333 5.84375V5.15625ZM1.66667 2.0625H2.33333C2.51667 2.0625 2.66667 2.21719 2.66667 2.40625V3.09375C2.66667 3.28281 2.51667 3.4375 2.33333 3.4375H1.66667C1.48333 3.4375 1.33333 3.28281 1.33333 3.09375V2.40625C1.33333 2.21719 1.48333 2.0625 1.66667 2.0625ZM3.33333 2.40625C3.33333 2.21719 3.48333 2.0625 3.66667 2.0625H4.33333C4.51667 2.0625 4.66667 2.21719 4.66667 2.40625V3.09375C4.66667 3.28281 4.51667 3.4375 4.33333 3.4375H3.66667C3.48333 3.4375 3.33333 3.28281 3.33333 3.09375V2.40625ZM5.66667 2.0625H6.33333C6.51667 2.0625 6.66667 2.21719 6.66667 2.40625V3.09375C6.66667 3.28281 6.51667 3.4375 6.33333 3.4375H5.66667C5.48333 3.4375 5.33333 3.28281 5.33333 3.09375V2.40625C5.33333 2.21719 5.48333 2.0625 5.66667 2.0625Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                        </div>
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 0V10H10V0H0ZM7.06473 7.25893L5 9.23884L2.93527 7.25893L4.375 3.15179L2.93527 1.21875H7.0625L5.625 3.15179L7.06473 7.25893Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">Admin Manager</span>
                        </div>
                        <div class=" d-flex align-items-center flex-wrap py-2" style="gap: 6px;">

                            <div class="svg-circle" style="margin-right: 6px;">
                                <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.77617 0.333008C6.65371 0.126758 6.42812 0 6.1875 0C5.94688 0 5.72129 0.126758 5.59883 0.333008L3.53633 3.77051C3.40957 3.9832 3.40527 4.24746 3.52773 4.4623C3.6502 4.67715 3.87793 4.81035 4.125 4.81035H8.25C8.49707 4.81035 8.72695 4.67715 8.84727 4.4623C8.96758 4.24746 8.96543 3.9832 8.83867 3.77051L6.77617 0.333008ZM6.1875 6.70312V9.79688C6.1875 10.2717 6.57207 10.6562 7.04688 10.6562H10.1406C10.6154 10.6562 11 10.2717 11 9.79688V6.70312C11 6.22832 10.6154 5.84375 10.1406 5.84375H7.04688C6.57207 5.84375 6.1875 6.22832 6.1875 6.70312ZM2.75 11C3.47935 11 4.17882 10.7103 4.69454 10.1945C5.21027 9.67882 5.5 8.97935 5.5 8.25C5.5 7.52065 5.21027 6.82118 4.69454 6.30546C4.17882 5.78973 3.47935 5.5 2.75 5.5C2.02065 5.5 1.32118 5.78973 0.805456 6.30546C0.289731 6.82118 0 7.52065 0 8.25C0 8.97935 0.289731 9.67882 0.805456 10.1945C1.32118 10.7103 2.02065 11 2.75 11Z" fill="white"/>
                                </svg>
                            </div>

                            <a href="#" class="btn skill-b">Budgeting</a>
                            <a href="#" class="btn skill-b">Maintenance</a>
                            <a href="#" class="btn skill-b">Book Keeping</a>

                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center py-3 px-3">
                    <a href="#" class="btn connect-dark-btn">Message</a>
                </div>


                <!-- <p class="hiring">
                    Hiring
                </p> -->

                <p class="professional-badge">Professional</p>
            </div>

            <!-- employer -->
            <div class="classifieds-card d-flex flex-column justify-content-between">
                <div>
                    <div class="classifieds-bg-banner employer"></div>
                    <div class="d-flex justify-content-center">
                        <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                    </div>
                    <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                    <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                        <a href="">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 12C7.5913 12 9.11742 11.3679 10.2426 10.2426C11.3679 9.11742 12 7.5913 12 6C12 4.4087 11.3679 2.88258 10.2426 1.75736C9.11742 0.632141 7.5913 0 6 0C4.4087 0 2.88258 0.632141 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 7.5913 0.632141 9.11742 1.75736 10.2426C2.88258 11.3679 4.4087 12 6 12ZM5.0625 7.875H5.625V6.375H5.0625C4.75078 6.375 4.5 6.12422 4.5 5.8125C4.5 5.50078 4.75078 5.25 5.0625 5.25H6.1875C6.49922 5.25 6.75 5.50078 6.75 5.8125V7.875H6.9375C7.24922 7.875 7.5 8.12578 7.5 8.4375C7.5 8.74922 7.24922 9 6.9375 9H5.0625C4.75078 9 4.5 8.74922 4.5 8.4375C4.5 8.12578 4.75078 7.875 5.0625 7.875ZM6 3C6.19891 3 6.38968 3.07902 6.53033 3.21967C6.67098 3.36032 6.75 3.55109 6.75 3.75C6.75 3.94891 6.67098 4.13968 6.53033 4.28033C6.38968 4.42098 6.19891 4.5 6 4.5C5.80109 4.5 5.61032 4.42098 5.46967 4.28033C5.32902 4.13968 5.25 3.94891 5.25 3.75C5.25 3.55109 5.32902 3.36032 5.46967 3.21967C5.61032 3.07902 5.80109 3 6 3Z" fill="#7E7E7E"/>
                            </svg>
                        </a>
                        <a href="">
                            <svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.1 2.88235C2.1 2.11791 2.395 1.38477 2.9201 0.844222C3.4452 0.303676 4.15739 0 4.9 0C5.64261 0 6.3548 0.303676 6.8799 0.844222C7.405 1.38477 7.7 2.11791 7.7 2.88235C7.7 3.6468 7.405 4.37994 6.8799 4.92048C6.3548 5.46103 5.64261 5.76471 4.9 5.76471C4.15739 5.76471 3.4452 5.46103 2.9201 4.92048C2.395 4.37994 2.1 3.6468 2.1 2.88235ZM0 10.8606C0 8.64255 1.74563 6.84559 3.90031 6.84559H5.89969C8.05437 6.84559 9.8 8.64255 9.8 10.8606C9.8 11.2299 9.50906 11.5294 9.15031 11.5294H0.649688C0.290938 11.5294 0 11.2299 0 10.8606ZM11.025 7.02573V5.58456H9.625C9.33406 5.58456 9.1 5.34361 9.1 5.04412C9.1 4.74462 9.33406 4.50368 9.625 4.50368H11.025V3.0625C11.025 2.76301 11.2591 2.52206 11.55 2.52206C11.8409 2.52206 12.075 2.76301 12.075 3.0625V4.50368H13.475C13.7659 4.50368 14 4.74462 14 5.04412C14 5.34361 13.7659 5.58456 13.475 5.58456H12.075V7.02573C12.075 7.32523 11.8409 7.56618 11.55 7.56618C11.2591 7.56618 11.025 7.32523 11.025 7.02573Z" fill="#7E7E7E"/>
                            </svg>
                        </a>
                    </div>
                    <div class="px-3">
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="8" height="11" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                        </div>
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="8" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 0C0.447917 0 0 0.461914 0 1.03125V9.96875C0 10.5381 0.447917 11 1 11H3V9.28125C3 8.71191 3.44792 8.25 4 8.25C4.55208 8.25 5 8.71191 5 9.28125V11H7C7.55208 11 8 10.5381 8 9.96875V1.03125C8 0.461914 7.55208 0 7 0H1ZM1.33333 5.15625C1.33333 4.96719 1.48333 4.8125 1.66667 4.8125H2.33333C2.51667 4.8125 2.66667 4.96719 2.66667 5.15625V5.84375C2.66667 6.03281 2.51667 6.1875 2.33333 6.1875H1.66667C1.48333 6.1875 1.33333 6.03281 1.33333 5.84375V5.15625ZM3.66667 4.8125H4.33333C4.51667 4.8125 4.66667 4.96719 4.66667 5.15625V5.84375C4.66667 6.03281 4.51667 6.1875 4.33333 6.1875H3.66667C3.48333 6.1875 3.33333 6.03281 3.33333 5.84375V5.15625C3.33333 4.96719 3.48333 4.8125 3.66667 4.8125ZM5.33333 5.15625C5.33333 4.96719 5.48333 4.8125 5.66667 4.8125H6.33333C6.51667 4.8125 6.66667 4.96719 6.66667 5.15625V5.84375C6.66667 6.03281 6.51667 6.1875 6.33333 6.1875H5.66667C5.48333 6.1875 5.33333 6.03281 5.33333 5.84375V5.15625ZM1.66667 2.0625H2.33333C2.51667 2.0625 2.66667 2.21719 2.66667 2.40625V3.09375C2.66667 3.28281 2.51667 3.4375 2.33333 3.4375H1.66667C1.48333 3.4375 1.33333 3.28281 1.33333 3.09375V2.40625C1.33333 2.21719 1.48333 2.0625 1.66667 2.0625ZM3.33333 2.40625C3.33333 2.21719 3.48333 2.0625 3.66667 2.0625H4.33333C4.51667 2.0625 4.66667 2.21719 4.66667 2.40625V3.09375C4.66667 3.28281 4.51667 3.4375 4.33333 3.4375H3.66667C3.48333 3.4375 3.33333 3.28281 3.33333 3.09375V2.40625ZM5.66667 2.0625H6.33333C6.51667 2.0625 6.66667 2.21719 6.66667 2.40625V3.09375C6.66667 3.28281 6.51667 3.4375 6.33333 3.4375H5.66667C5.48333 3.4375 5.33333 3.28281 5.33333 3.09375V2.40625C5.33333 2.21719 5.48333 2.0625 5.66667 2.0625Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                        </div>
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 0V10H10V0H0ZM7.06473 7.25893L5 9.23884L2.93527 7.25893L4.375 3.15179L2.93527 1.21875H7.0625L5.625 3.15179L7.06473 7.25893Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">Admin Manager</span>
                        </div>

                    </div>
                </div>

                <div class="d-flex justify-content-center py-3 px-3">
                    <a href="#" class="btn connect-dark-btn">Message</a>
                </div>


                <!-- <p class="hiring">
                    Hiring
                </p> -->
                <p class="employer-badge">
                    Employer
                </p>

            </div>
            <div class="classifieds-card d-flex flex-column justify-content-between">
                <div>
                    <div class="classifieds-bg-banner employer"></div>
                    <div class="d-flex justify-content-center">
                        <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                    </div>
                    <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                    <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                        <a href="">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 12C7.5913 12 9.11742 11.3679 10.2426 10.2426C11.3679 9.11742 12 7.5913 12 6C12 4.4087 11.3679 2.88258 10.2426 1.75736C9.11742 0.632141 7.5913 0 6 0C4.4087 0 2.88258 0.632141 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 7.5913 0.632141 9.11742 1.75736 10.2426C2.88258 11.3679 4.4087 12 6 12ZM5.0625 7.875H5.625V6.375H5.0625C4.75078 6.375 4.5 6.12422 4.5 5.8125C4.5 5.50078 4.75078 5.25 5.0625 5.25H6.1875C6.49922 5.25 6.75 5.50078 6.75 5.8125V7.875H6.9375C7.24922 7.875 7.5 8.12578 7.5 8.4375C7.5 8.74922 7.24922 9 6.9375 9H5.0625C4.75078 9 4.5 8.74922 4.5 8.4375C4.5 8.12578 4.75078 7.875 5.0625 7.875ZM6 3C6.19891 3 6.38968 3.07902 6.53033 3.21967C6.67098 3.36032 6.75 3.55109 6.75 3.75C6.75 3.94891 6.67098 4.13968 6.53033 4.28033C6.38968 4.42098 6.19891 4.5 6 4.5C5.80109 4.5 5.61032 4.42098 5.46967 4.28033C5.32902 4.13968 5.25 3.94891 5.25 3.75C5.25 3.55109 5.32902 3.36032 5.46967 3.21967C5.61032 3.07902 5.80109 3 6 3Z" fill="#7E7E7E"/>
                            </svg>
                        </a>
                        <a href="">
                            <svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.1 2.88235C2.1 2.11791 2.395 1.38477 2.9201 0.844222C3.4452 0.303676 4.15739 0 4.9 0C5.64261 0 6.3548 0.303676 6.8799 0.844222C7.405 1.38477 7.7 2.11791 7.7 2.88235C7.7 3.6468 7.405 4.37994 6.8799 4.92048C6.3548 5.46103 5.64261 5.76471 4.9 5.76471C4.15739 5.76471 3.4452 5.46103 2.9201 4.92048C2.395 4.37994 2.1 3.6468 2.1 2.88235ZM0 10.8606C0 8.64255 1.74563 6.84559 3.90031 6.84559H5.89969C8.05437 6.84559 9.8 8.64255 9.8 10.8606C9.8 11.2299 9.50906 11.5294 9.15031 11.5294H0.649688C0.290938 11.5294 0 11.2299 0 10.8606ZM11.025 7.02573V5.58456H9.625C9.33406 5.58456 9.1 5.34361 9.1 5.04412C9.1 4.74462 9.33406 4.50368 9.625 4.50368H11.025V3.0625C11.025 2.76301 11.2591 2.52206 11.55 2.52206C11.8409 2.52206 12.075 2.76301 12.075 3.0625V4.50368H13.475C13.7659 4.50368 14 4.74462 14 5.04412C14 5.34361 13.7659 5.58456 13.475 5.58456H12.075V7.02573C12.075 7.32523 11.8409 7.56618 11.55 7.56618C11.2591 7.56618 11.025 7.32523 11.025 7.02573Z" fill="#7E7E7E"/>
                            </svg>
                        </a>
                    </div>
                    <div class="px-3">
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="8" height="11" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                        </div>
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="8" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 0C0.447917 0 0 0.461914 0 1.03125V9.96875C0 10.5381 0.447917 11 1 11H3V9.28125C3 8.71191 3.44792 8.25 4 8.25C4.55208 8.25 5 8.71191 5 9.28125V11H7C7.55208 11 8 10.5381 8 9.96875V1.03125C8 0.461914 7.55208 0 7 0H1ZM1.33333 5.15625C1.33333 4.96719 1.48333 4.8125 1.66667 4.8125H2.33333C2.51667 4.8125 2.66667 4.96719 2.66667 5.15625V5.84375C2.66667 6.03281 2.51667 6.1875 2.33333 6.1875H1.66667C1.48333 6.1875 1.33333 6.03281 1.33333 5.84375V5.15625ZM3.66667 4.8125H4.33333C4.51667 4.8125 4.66667 4.96719 4.66667 5.15625V5.84375C4.66667 6.03281 4.51667 6.1875 4.33333 6.1875H3.66667C3.48333 6.1875 3.33333 6.03281 3.33333 5.84375V5.15625C3.33333 4.96719 3.48333 4.8125 3.66667 4.8125ZM5.33333 5.15625C5.33333 4.96719 5.48333 4.8125 5.66667 4.8125H6.33333C6.51667 4.8125 6.66667 4.96719 6.66667 5.15625V5.84375C6.66667 6.03281 6.51667 6.1875 6.33333 6.1875H5.66667C5.48333 6.1875 5.33333 6.03281 5.33333 5.84375V5.15625ZM1.66667 2.0625H2.33333C2.51667 2.0625 2.66667 2.21719 2.66667 2.40625V3.09375C2.66667 3.28281 2.51667 3.4375 2.33333 3.4375H1.66667C1.48333 3.4375 1.33333 3.28281 1.33333 3.09375V2.40625C1.33333 2.21719 1.48333 2.0625 1.66667 2.0625ZM3.33333 2.40625C3.33333 2.21719 3.48333 2.0625 3.66667 2.0625H4.33333C4.51667 2.0625 4.66667 2.21719 4.66667 2.40625V3.09375C4.66667 3.28281 4.51667 3.4375 4.33333 3.4375H3.66667C3.48333 3.4375 3.33333 3.28281 3.33333 3.09375V2.40625ZM5.66667 2.0625H6.33333C6.51667 2.0625 6.66667 2.21719 6.66667 2.40625V3.09375C6.66667 3.28281 6.51667 3.4375 6.33333 3.4375H5.66667C5.48333 3.4375 5.33333 3.28281 5.33333 3.09375V2.40625C5.33333 2.21719 5.48333 2.0625 5.66667 2.0625Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                        </div>
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 0V10H10V0H0ZM7.06473 7.25893L5 9.23884L2.93527 7.25893L4.375 3.15179L2.93527 1.21875H7.0625L5.625 3.15179L7.06473 7.25893Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">Admin Manager</span>
                        </div>

                    </div>
                </div>

                <div class="d-flex justify-content-center py-3 px-3">
                    <a href="#" class="btn connect-dark-btn">Message</a>
                </div>


                <!-- <p class="hiring">
                    Hiring
                </p> -->
                <p class="employer-badge">
                    Employer
                </p>

            </div>
            <div class="classifieds-card d-flex flex-column justify-content-between">
                <div>
                    <div class="classifieds-bg-banner employer"></div>
                    <div class="d-flex justify-content-center">
                        <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                    </div>
                    <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                    <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                        <a href="">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 12C7.5913 12 9.11742 11.3679 10.2426 10.2426C11.3679 9.11742 12 7.5913 12 6C12 4.4087 11.3679 2.88258 10.2426 1.75736C9.11742 0.632141 7.5913 0 6 0C4.4087 0 2.88258 0.632141 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 7.5913 0.632141 9.11742 1.75736 10.2426C2.88258 11.3679 4.4087 12 6 12ZM5.0625 7.875H5.625V6.375H5.0625C4.75078 6.375 4.5 6.12422 4.5 5.8125C4.5 5.50078 4.75078 5.25 5.0625 5.25H6.1875C6.49922 5.25 6.75 5.50078 6.75 5.8125V7.875H6.9375C7.24922 7.875 7.5 8.12578 7.5 8.4375C7.5 8.74922 7.24922 9 6.9375 9H5.0625C4.75078 9 4.5 8.74922 4.5 8.4375C4.5 8.12578 4.75078 7.875 5.0625 7.875ZM6 3C6.19891 3 6.38968 3.07902 6.53033 3.21967C6.67098 3.36032 6.75 3.55109 6.75 3.75C6.75 3.94891 6.67098 4.13968 6.53033 4.28033C6.38968 4.42098 6.19891 4.5 6 4.5C5.80109 4.5 5.61032 4.42098 5.46967 4.28033C5.32902 4.13968 5.25 3.94891 5.25 3.75C5.25 3.55109 5.32902 3.36032 5.46967 3.21967C5.61032 3.07902 5.80109 3 6 3Z" fill="#7E7E7E"/>
                            </svg>
                        </a>
                        <a href="">
                            <svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.1 2.88235C2.1 2.11791 2.395 1.38477 2.9201 0.844222C3.4452 0.303676 4.15739 0 4.9 0C5.64261 0 6.3548 0.303676 6.8799 0.844222C7.405 1.38477 7.7 2.11791 7.7 2.88235C7.7 3.6468 7.405 4.37994 6.8799 4.92048C6.3548 5.46103 5.64261 5.76471 4.9 5.76471C4.15739 5.76471 3.4452 5.46103 2.9201 4.92048C2.395 4.37994 2.1 3.6468 2.1 2.88235ZM0 10.8606C0 8.64255 1.74563 6.84559 3.90031 6.84559H5.89969C8.05437 6.84559 9.8 8.64255 9.8 10.8606C9.8 11.2299 9.50906 11.5294 9.15031 11.5294H0.649688C0.290938 11.5294 0 11.2299 0 10.8606ZM11.025 7.02573V5.58456H9.625C9.33406 5.58456 9.1 5.34361 9.1 5.04412C9.1 4.74462 9.33406 4.50368 9.625 4.50368H11.025V3.0625C11.025 2.76301 11.2591 2.52206 11.55 2.52206C11.8409 2.52206 12.075 2.76301 12.075 3.0625V4.50368H13.475C13.7659 4.50368 14 4.74462 14 5.04412C14 5.34361 13.7659 5.58456 13.475 5.58456H12.075V7.02573C12.075 7.32523 11.8409 7.56618 11.55 7.56618C11.2591 7.56618 11.025 7.32523 11.025 7.02573Z" fill="#7E7E7E"/>
                            </svg>
                        </a>
                    </div>
                    <div class="px-3">
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="8" height="11" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                        </div>
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="8" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 0C0.447917 0 0 0.461914 0 1.03125V9.96875C0 10.5381 0.447917 11 1 11H3V9.28125C3 8.71191 3.44792 8.25 4 8.25C4.55208 8.25 5 8.71191 5 9.28125V11H7C7.55208 11 8 10.5381 8 9.96875V1.03125C8 0.461914 7.55208 0 7 0H1ZM1.33333 5.15625C1.33333 4.96719 1.48333 4.8125 1.66667 4.8125H2.33333C2.51667 4.8125 2.66667 4.96719 2.66667 5.15625V5.84375C2.66667 6.03281 2.51667 6.1875 2.33333 6.1875H1.66667C1.48333 6.1875 1.33333 6.03281 1.33333 5.84375V5.15625ZM3.66667 4.8125H4.33333C4.51667 4.8125 4.66667 4.96719 4.66667 5.15625V5.84375C4.66667 6.03281 4.51667 6.1875 4.33333 6.1875H3.66667C3.48333 6.1875 3.33333 6.03281 3.33333 5.84375V5.15625C3.33333 4.96719 3.48333 4.8125 3.66667 4.8125ZM5.33333 5.15625C5.33333 4.96719 5.48333 4.8125 5.66667 4.8125H6.33333C6.51667 4.8125 6.66667 4.96719 6.66667 5.15625V5.84375C6.66667 6.03281 6.51667 6.1875 6.33333 6.1875H5.66667C5.48333 6.1875 5.33333 6.03281 5.33333 5.84375V5.15625ZM1.66667 2.0625H2.33333C2.51667 2.0625 2.66667 2.21719 2.66667 2.40625V3.09375C2.66667 3.28281 2.51667 3.4375 2.33333 3.4375H1.66667C1.48333 3.4375 1.33333 3.28281 1.33333 3.09375V2.40625C1.33333 2.21719 1.48333 2.0625 1.66667 2.0625ZM3.33333 2.40625C3.33333 2.21719 3.48333 2.0625 3.66667 2.0625H4.33333C4.51667 2.0625 4.66667 2.21719 4.66667 2.40625V3.09375C4.66667 3.28281 4.51667 3.4375 4.33333 3.4375H3.66667C3.48333 3.4375 3.33333 3.28281 3.33333 3.09375V2.40625ZM5.66667 2.0625H6.33333C6.51667 2.0625 6.66667 2.21719 6.66667 2.40625V3.09375C6.66667 3.28281 6.51667 3.4375 6.33333 3.4375H5.66667C5.48333 3.4375 5.33333 3.28281 5.33333 3.09375V2.40625C5.33333 2.21719 5.48333 2.0625 5.66667 2.0625Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                        </div>
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 0V10H10V0H0ZM7.06473 7.25893L5 9.23884L2.93527 7.25893L4.375 3.15179L2.93527 1.21875H7.0625L5.625 3.15179L7.06473 7.25893Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">Admin Manager</span>
                        </div>

                    </div>
                </div>

                <div class="d-flex justify-content-center py-3 px-3">
                    <a href="#" class="btn connect-dark-btn">Message</a>
                </div>


                <!-- <p class="hiring">
                    Hiring
                </p> -->
                <p class="employer-badge">
                    Employer
                </p>

            </div>

            <!-- service provider -->
            <div class="classifieds-card d-flex flex-column justify-content-between">
                <div>
                    <div class="classifieds-bg-banner service"></div>
                    <div class="d-flex justify-content-center">
                        <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                    </div>
                    <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                    <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                        <a href="">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 12C7.5913 12 9.11742 11.3679 10.2426 10.2426C11.3679 9.11742 12 7.5913 12 6C12 4.4087 11.3679 2.88258 10.2426 1.75736C9.11742 0.632141 7.5913 0 6 0C4.4087 0 2.88258 0.632141 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 7.5913 0.632141 9.11742 1.75736 10.2426C2.88258 11.3679 4.4087 12 6 12ZM5.0625 7.875H5.625V6.375H5.0625C4.75078 6.375 4.5 6.12422 4.5 5.8125C4.5 5.50078 4.75078 5.25 5.0625 5.25H6.1875C6.49922 5.25 6.75 5.50078 6.75 5.8125V7.875H6.9375C7.24922 7.875 7.5 8.12578 7.5 8.4375C7.5 8.74922 7.24922 9 6.9375 9H5.0625C4.75078 9 4.5 8.74922 4.5 8.4375C4.5 8.12578 4.75078 7.875 5.0625 7.875ZM6 3C6.19891 3 6.38968 3.07902 6.53033 3.21967C6.67098 3.36032 6.75 3.55109 6.75 3.75C6.75 3.94891 6.67098 4.13968 6.53033 4.28033C6.38968 4.42098 6.19891 4.5 6 4.5C5.80109 4.5 5.61032 4.42098 5.46967 4.28033C5.32902 4.13968 5.25 3.94891 5.25 3.75C5.25 3.55109 5.32902 3.36032 5.46967 3.21967C5.61032 3.07902 5.80109 3 6 3Z" fill="#7E7E7E"/>
                            </svg>
                        </a>
                        <a href="">
                            <svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.1 2.88235C2.1 2.11791 2.395 1.38477 2.9201 0.844222C3.4452 0.303676 4.15739 0 4.9 0C5.64261 0 6.3548 0.303676 6.8799 0.844222C7.405 1.38477 7.7 2.11791 7.7 2.88235C7.7 3.6468 7.405 4.37994 6.8799 4.92048C6.3548 5.46103 5.64261 5.76471 4.9 5.76471C4.15739 5.76471 3.4452 5.46103 2.9201 4.92048C2.395 4.37994 2.1 3.6468 2.1 2.88235ZM0 10.8606C0 8.64255 1.74563 6.84559 3.90031 6.84559H5.89969C8.05437 6.84559 9.8 8.64255 9.8 10.8606C9.8 11.2299 9.50906 11.5294 9.15031 11.5294H0.649688C0.290938 11.5294 0 11.2299 0 10.8606ZM11.025 7.02573V5.58456H9.625C9.33406 5.58456 9.1 5.34361 9.1 5.04412C9.1 4.74462 9.33406 4.50368 9.625 4.50368H11.025V3.0625C11.025 2.76301 11.2591 2.52206 11.55 2.52206C11.8409 2.52206 12.075 2.76301 12.075 3.0625V4.50368H13.475C13.7659 4.50368 14 4.74462 14 5.04412C14 5.34361 13.7659 5.58456 13.475 5.58456H12.075V7.02573C12.075 7.32523 11.8409 7.56618 11.55 7.56618C11.2591 7.56618 11.025 7.32523 11.025 7.02573Z" fill="#7E7E7E"/>
                            </svg>
                        </a>
                    </div>
                    <div class="px-3">
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="8" height="11" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                        </div>
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="8" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 0C0.447917 0 0 0.461914 0 1.03125V9.96875C0 10.5381 0.447917 11 1 11H3V9.28125C3 8.71191 3.44792 8.25 4 8.25C4.55208 8.25 5 8.71191 5 9.28125V11H7C7.55208 11 8 10.5381 8 9.96875V1.03125C8 0.461914 7.55208 0 7 0H1ZM1.33333 5.15625C1.33333 4.96719 1.48333 4.8125 1.66667 4.8125H2.33333C2.51667 4.8125 2.66667 4.96719 2.66667 5.15625V5.84375C2.66667 6.03281 2.51667 6.1875 2.33333 6.1875H1.66667C1.48333 6.1875 1.33333 6.03281 1.33333 5.84375V5.15625ZM3.66667 4.8125H4.33333C4.51667 4.8125 4.66667 4.96719 4.66667 5.15625V5.84375C4.66667 6.03281 4.51667 6.1875 4.33333 6.1875H3.66667C3.48333 6.1875 3.33333 6.03281 3.33333 5.84375V5.15625C3.33333 4.96719 3.48333 4.8125 3.66667 4.8125ZM5.33333 5.15625C5.33333 4.96719 5.48333 4.8125 5.66667 4.8125H6.33333C6.51667 4.8125 6.66667 4.96719 6.66667 5.15625V5.84375C6.66667 6.03281 6.51667 6.1875 6.33333 6.1875H5.66667C5.48333 6.1875 5.33333 6.03281 5.33333 5.84375V5.15625ZM1.66667 2.0625H2.33333C2.51667 2.0625 2.66667 2.21719 2.66667 2.40625V3.09375C2.66667 3.28281 2.51667 3.4375 2.33333 3.4375H1.66667C1.48333 3.4375 1.33333 3.28281 1.33333 3.09375V2.40625C1.33333 2.21719 1.48333 2.0625 1.66667 2.0625ZM3.33333 2.40625C3.33333 2.21719 3.48333 2.0625 3.66667 2.0625H4.33333C4.51667 2.0625 4.66667 2.21719 4.66667 2.40625V3.09375C4.66667 3.28281 4.51667 3.4375 4.33333 3.4375H3.66667C3.48333 3.4375 3.33333 3.28281 3.33333 3.09375V2.40625ZM5.66667 2.0625H6.33333C6.51667 2.0625 6.66667 2.21719 6.66667 2.40625V3.09375C6.66667 3.28281 6.51667 3.4375 6.33333 3.4375H5.66667C5.48333 3.4375 5.33333 3.28281 5.33333 3.09375V2.40625C5.33333 2.21719 5.48333 2.0625 5.66667 2.0625Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                        </div>
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 0V10H10V0H0ZM7.06473 7.25893L5 9.23884L2.93527 7.25893L4.375 3.15179L2.93527 1.21875H7.0625L5.625 3.15179L7.06473 7.25893Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">Admin Manager</span>
                        </div>

                    </div>
                </div>

                <div class="d-flex justify-content-center py-3 px-3">
                    <a href="#" class="btn connect-dark-btn">Message</a>
                </div>


                <!-- <p class="hiring">
                    Hiring
                </p> -->
                <p class="service-badge">
                    Service Provider
                </p>
            </div>
            <div class="classifieds-card d-flex flex-column justify-content-between">
                <div>
                    <div class="classifieds-bg-banner service"></div>
                    <div class="d-flex justify-content-center">
                        <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                    </div>
                    <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                    <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                        <a href="">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 12C7.5913 12 9.11742 11.3679 10.2426 10.2426C11.3679 9.11742 12 7.5913 12 6C12 4.4087 11.3679 2.88258 10.2426 1.75736C9.11742 0.632141 7.5913 0 6 0C4.4087 0 2.88258 0.632141 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 7.5913 0.632141 9.11742 1.75736 10.2426C2.88258 11.3679 4.4087 12 6 12ZM5.0625 7.875H5.625V6.375H5.0625C4.75078 6.375 4.5 6.12422 4.5 5.8125C4.5 5.50078 4.75078 5.25 5.0625 5.25H6.1875C6.49922 5.25 6.75 5.50078 6.75 5.8125V7.875H6.9375C7.24922 7.875 7.5 8.12578 7.5 8.4375C7.5 8.74922 7.24922 9 6.9375 9H5.0625C4.75078 9 4.5 8.74922 4.5 8.4375C4.5 8.12578 4.75078 7.875 5.0625 7.875ZM6 3C6.19891 3 6.38968 3.07902 6.53033 3.21967C6.67098 3.36032 6.75 3.55109 6.75 3.75C6.75 3.94891 6.67098 4.13968 6.53033 4.28033C6.38968 4.42098 6.19891 4.5 6 4.5C5.80109 4.5 5.61032 4.42098 5.46967 4.28033C5.32902 4.13968 5.25 3.94891 5.25 3.75C5.25 3.55109 5.32902 3.36032 5.46967 3.21967C5.61032 3.07902 5.80109 3 6 3Z" fill="#7E7E7E"/>
                            </svg>
                        </a>
                        <a href="">
                            <svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.1 2.88235C2.1 2.11791 2.395 1.38477 2.9201 0.844222C3.4452 0.303676 4.15739 0 4.9 0C5.64261 0 6.3548 0.303676 6.8799 0.844222C7.405 1.38477 7.7 2.11791 7.7 2.88235C7.7 3.6468 7.405 4.37994 6.8799 4.92048C6.3548 5.46103 5.64261 5.76471 4.9 5.76471C4.15739 5.76471 3.4452 5.46103 2.9201 4.92048C2.395 4.37994 2.1 3.6468 2.1 2.88235ZM0 10.8606C0 8.64255 1.74563 6.84559 3.90031 6.84559H5.89969C8.05437 6.84559 9.8 8.64255 9.8 10.8606C9.8 11.2299 9.50906 11.5294 9.15031 11.5294H0.649688C0.290938 11.5294 0 11.2299 0 10.8606ZM11.025 7.02573V5.58456H9.625C9.33406 5.58456 9.1 5.34361 9.1 5.04412C9.1 4.74462 9.33406 4.50368 9.625 4.50368H11.025V3.0625C11.025 2.76301 11.2591 2.52206 11.55 2.52206C11.8409 2.52206 12.075 2.76301 12.075 3.0625V4.50368H13.475C13.7659 4.50368 14 4.74462 14 5.04412C14 5.34361 13.7659 5.58456 13.475 5.58456H12.075V7.02573C12.075 7.32523 11.8409 7.56618 11.55 7.56618C11.2591 7.56618 11.025 7.32523 11.025 7.02573Z" fill="#7E7E7E"/>
                            </svg>
                        </a>
                    </div>
                    <div class="px-3">
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="8" height="11" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                        </div>
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="8" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 0C0.447917 0 0 0.461914 0 1.03125V9.96875C0 10.5381 0.447917 11 1 11H3V9.28125C3 8.71191 3.44792 8.25 4 8.25C4.55208 8.25 5 8.71191 5 9.28125V11H7C7.55208 11 8 10.5381 8 9.96875V1.03125C8 0.461914 7.55208 0 7 0H1ZM1.33333 5.15625C1.33333 4.96719 1.48333 4.8125 1.66667 4.8125H2.33333C2.51667 4.8125 2.66667 4.96719 2.66667 5.15625V5.84375C2.66667 6.03281 2.51667 6.1875 2.33333 6.1875H1.66667C1.48333 6.1875 1.33333 6.03281 1.33333 5.84375V5.15625ZM3.66667 4.8125H4.33333C4.51667 4.8125 4.66667 4.96719 4.66667 5.15625V5.84375C4.66667 6.03281 4.51667 6.1875 4.33333 6.1875H3.66667C3.48333 6.1875 3.33333 6.03281 3.33333 5.84375V5.15625C3.33333 4.96719 3.48333 4.8125 3.66667 4.8125ZM5.33333 5.15625C5.33333 4.96719 5.48333 4.8125 5.66667 4.8125H6.33333C6.51667 4.8125 6.66667 4.96719 6.66667 5.15625V5.84375C6.66667 6.03281 6.51667 6.1875 6.33333 6.1875H5.66667C5.48333 6.1875 5.33333 6.03281 5.33333 5.84375V5.15625ZM1.66667 2.0625H2.33333C2.51667 2.0625 2.66667 2.21719 2.66667 2.40625V3.09375C2.66667 3.28281 2.51667 3.4375 2.33333 3.4375H1.66667C1.48333 3.4375 1.33333 3.28281 1.33333 3.09375V2.40625C1.33333 2.21719 1.48333 2.0625 1.66667 2.0625ZM3.33333 2.40625C3.33333 2.21719 3.48333 2.0625 3.66667 2.0625H4.33333C4.51667 2.0625 4.66667 2.21719 4.66667 2.40625V3.09375C4.66667 3.28281 4.51667 3.4375 4.33333 3.4375H3.66667C3.48333 3.4375 3.33333 3.28281 3.33333 3.09375V2.40625ZM5.66667 2.0625H6.33333C6.51667 2.0625 6.66667 2.21719 6.66667 2.40625V3.09375C6.66667 3.28281 6.51667 3.4375 6.33333 3.4375H5.66667C5.48333 3.4375 5.33333 3.28281 5.33333 3.09375V2.40625C5.33333 2.21719 5.48333 2.0625 5.66667 2.0625Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                        </div>
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 0V10H10V0H0ZM7.06473 7.25893L5 9.23884L2.93527 7.25893L4.375 3.15179L2.93527 1.21875H7.0625L5.625 3.15179L7.06473 7.25893Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">Admin Manager</span>
                        </div>

                    </div>
                </div>

                <div class="d-flex justify-content-center py-3 px-3">
                    <a href="#" class="btn connect-dark-btn">Message</a>
                </div>


                <!-- <p class="hiring">
                    Hiring
                </p> -->
                <p class="service-badge">
                    Service Provider
                </p>
            </div>
            <div class="classifieds-card d-flex flex-column justify-content-between">
                <div>
                    <div class="classifieds-bg-banner service"></div>
                    <div class="d-flex justify-content-center">
                        <img class="classifieds-pro-pic" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="">
                    </div>
                    <h5 class="classifieds-name text-center mt-2">Amanda G</h5>
                    <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                        <a href="">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 12C7.5913 12 9.11742 11.3679 10.2426 10.2426C11.3679 9.11742 12 7.5913 12 6C12 4.4087 11.3679 2.88258 10.2426 1.75736C9.11742 0.632141 7.5913 0 6 0C4.4087 0 2.88258 0.632141 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 7.5913 0.632141 9.11742 1.75736 10.2426C2.88258 11.3679 4.4087 12 6 12ZM5.0625 7.875H5.625V6.375H5.0625C4.75078 6.375 4.5 6.12422 4.5 5.8125C4.5 5.50078 4.75078 5.25 5.0625 5.25H6.1875C6.49922 5.25 6.75 5.50078 6.75 5.8125V7.875H6.9375C7.24922 7.875 7.5 8.12578 7.5 8.4375C7.5 8.74922 7.24922 9 6.9375 9H5.0625C4.75078 9 4.5 8.74922 4.5 8.4375C4.5 8.12578 4.75078 7.875 5.0625 7.875ZM6 3C6.19891 3 6.38968 3.07902 6.53033 3.21967C6.67098 3.36032 6.75 3.55109 6.75 3.75C6.75 3.94891 6.67098 4.13968 6.53033 4.28033C6.38968 4.42098 6.19891 4.5 6 4.5C5.80109 4.5 5.61032 4.42098 5.46967 4.28033C5.32902 4.13968 5.25 3.94891 5.25 3.75C5.25 3.55109 5.32902 3.36032 5.46967 3.21967C5.61032 3.07902 5.80109 3 6 3Z" fill="#7E7E7E"/>
                            </svg>
                        </a>
                        <a href="">
                            <svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.1 2.88235C2.1 2.11791 2.395 1.38477 2.9201 0.844222C3.4452 0.303676 4.15739 0 4.9 0C5.64261 0 6.3548 0.303676 6.8799 0.844222C7.405 1.38477 7.7 2.11791 7.7 2.88235C7.7 3.6468 7.405 4.37994 6.8799 4.92048C6.3548 5.46103 5.64261 5.76471 4.9 5.76471C4.15739 5.76471 3.4452 5.46103 2.9201 4.92048C2.395 4.37994 2.1 3.6468 2.1 2.88235ZM0 10.8606C0 8.64255 1.74563 6.84559 3.90031 6.84559H5.89969C8.05437 6.84559 9.8 8.64255 9.8 10.8606C9.8 11.2299 9.50906 11.5294 9.15031 11.5294H0.649688C0.290938 11.5294 0 11.2299 0 10.8606ZM11.025 7.02573V5.58456H9.625C9.33406 5.58456 9.1 5.34361 9.1 5.04412C9.1 4.74462 9.33406 4.50368 9.625 4.50368H11.025V3.0625C11.025 2.76301 11.2591 2.52206 11.55 2.52206C11.8409 2.52206 12.075 2.76301 12.075 3.0625V4.50368H13.475C13.7659 4.50368 14 4.74462 14 5.04412C14 5.34361 13.7659 5.58456 13.475 5.58456H12.075V7.02573C12.075 7.32523 11.8409 7.56618 11.55 7.56618C11.2591 7.56618 11.025 7.32523 11.025 7.02573Z" fill="#7E7E7E"/>
                            </svg>
                        </a>
                    </div>
                    <div class="px-3">
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="8" height="11" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">Charlotte, North Carolina, US</span>
                        </div>
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="8" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 0C0.447917 0 0 0.461914 0 1.03125V9.96875C0 10.5381 0.447917 11 1 11H3V9.28125C3 8.71191 3.44792 8.25 4 8.25C4.55208 8.25 5 8.71191 5 9.28125V11H7C7.55208 11 8 10.5381 8 9.96875V1.03125C8 0.461914 7.55208 0 7 0H1ZM1.33333 5.15625C1.33333 4.96719 1.48333 4.8125 1.66667 4.8125H2.33333C2.51667 4.8125 2.66667 4.96719 2.66667 5.15625V5.84375C2.66667 6.03281 2.51667 6.1875 2.33333 6.1875H1.66667C1.48333 6.1875 1.33333 6.03281 1.33333 5.84375V5.15625ZM3.66667 4.8125H4.33333C4.51667 4.8125 4.66667 4.96719 4.66667 5.15625V5.84375C4.66667 6.03281 4.51667 6.1875 4.33333 6.1875H3.66667C3.48333 6.1875 3.33333 6.03281 3.33333 5.84375V5.15625C3.33333 4.96719 3.48333 4.8125 3.66667 4.8125ZM5.33333 5.15625C5.33333 4.96719 5.48333 4.8125 5.66667 4.8125H6.33333C6.51667 4.8125 6.66667 4.96719 6.66667 5.15625V5.84375C6.66667 6.03281 6.51667 6.1875 6.33333 6.1875H5.66667C5.48333 6.1875 5.33333 6.03281 5.33333 5.84375V5.15625ZM1.66667 2.0625H2.33333C2.51667 2.0625 2.66667 2.21719 2.66667 2.40625V3.09375C2.66667 3.28281 2.51667 3.4375 2.33333 3.4375H1.66667C1.48333 3.4375 1.33333 3.28281 1.33333 3.09375V2.40625C1.33333 2.21719 1.48333 2.0625 1.66667 2.0625ZM3.33333 2.40625C3.33333 2.21719 3.48333 2.0625 3.66667 2.0625H4.33333C4.51667 2.0625 4.66667 2.21719 4.66667 2.40625V3.09375C4.66667 3.28281 4.51667 3.4375 4.33333 3.4375H3.66667C3.48333 3.4375 3.33333 3.28281 3.33333 3.09375V2.40625ZM5.66667 2.0625H6.33333C6.51667 2.0625 6.66667 2.21719 6.66667 2.40625V3.09375C6.66667 3.28281 6.51667 3.4375 6.33333 3.4375H5.66667C5.48333 3.4375 5.33333 3.28281 5.33333 3.09375V2.40625C5.33333 2.21719 5.48333 2.0625 5.66667 2.0625Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">ABC Pvt Ltd</span>
                        </div>
                        <div class=" d-flex align-items-center" style="gap: 12px;">
                            <div class="svg-circle">
                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 0V10H10V0H0ZM7.06473 7.25893L5 9.23884L2.93527 7.25893L4.375 3.15179L2.93527 1.21875H7.0625L5.625 3.15179L7.06473 7.25893Z" fill="white"/>
                                </svg>
                            </div>
                            <span class="classifieds-card-text py-2">Admin Manager</span>
                        </div>

                    </div>
                </div>

                <div class="d-flex justify-content-center py-3 px-3">
                    <a href="#" class="btn connect-dark-btn">Message</a>
                </div>


                <!-- <p class="hiring">
                    Hiring
                </p> -->
                <p class="service-badge">
                    Service Provider
                </p>
            </div>

        </div>

    </div>
</div>

<?php taoh_get_footer(); ?>