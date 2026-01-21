<?php
taoh_get_header();


$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
$user_info_obj = $taoh_user_is_logged_in ? taoh_user_all_info() : null;

$valid_dir_viewer = $taoh_user_is_logged_in && $user_info_obj->profile_complete && $user_info_obj->unlist_me_dir !== 'yes';

?>


<style>
  .company-header-title {
    font-size: clamp(24px, 3vw + 1rem, 30px);
    color: #444444;
    font-weight: 400;
  }
  .company-url {
    font-size: clamp(15px, 16px, 17px);
  }
  .intro {
    font-size: 16px;
    color: #444444;
    font-weight: 500;
  }
  .intro-content {
    font-size: 15px;
  }
  .company-title {
    font-size: clamp(21px, 3vw + 1rem, 24px);
    color: #444444;
    font-weight: 400;
  }
  .tagline-text {
    font-size: 15px;
    color: #707070;
    font-weight: 400;
  }
  .review-text {
    font-size: 10.2px;
    color: #000000;
  }
  .follow-btn {
    font-size: clamp(16px, 3vw + 1rem, 18px);
    color: #fff;
    font-weight: 400;
  }
  .company-info-tabs .company-info {
    font-size: clamp(21px, 3vw + 1rem, 24px);
    color: #000000;
    font-weight: 400;
    transition: color 0.3s ease, border-bottom 0.1s ease, font-weight 0.1s ease;
  }
  .headline {
    font-size: clamp(24px, 3vw + 1rem, 26px);
    color: #333ABF;
    font-weight: 700;
  }
  .content-detail {
    font-size: 16px;
    color: #000000;
    font-weight: 400;
  }
  .content-detail.read-more {
    font-size: 16px;
    color: #333ABF;
    font-weight: 400;
  }
  .social-link-text {
    font-size: 19px;
    color: #444444;
    font-weight: 400;
  }
  .carousel-control-next, .carousel-control-prev {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    top: 50%;
    transform: translateY(-50%);
  }
   
   .carousel-control-next {
        right: 10px;
  }
  .carousel-control-prev {
    left: 10px;
  }

  .company-info-tabs .nav-link {
        border: none;
    }

    .company-info-tabs .company-info:hover {
       color: #333ABF; 
       font-weight: 500;
       border-bottom: 2px solid #333ABF;
    }

    
    .company-info-tabs .company-info.active {
        border-bottom: 2px solid #333ABF;
    }

    .company-address h4 {
        font-size: 17px;
        font-weight: 700;
        color: #000000;
    }
    .company-address p {
        font-size: 16px;
        line-height: 1.4;
        font-weight: 400;
        color: #444444;
    }

    .left-job-list .card, .main-job-detail .card {
        border: 2px solid #D3D3D3;
        border-radius: 12px;
    }
   
    .left-job-list .card .company {
        font-size: 12px;
        font-weight: 700;
        color: #000000;
        text-transform: uppercase;
    }
    .left-job-list .card .job-title {
        font-size: clamp(18px , 2vw + 1rem, 21px);
        font-weight: 700;
        color: #000000;
    }
    .left-job-list .card .location {
        font-size: 15px;
        font-weight: 400;
        color: #000000;
    }
    .left-job-list .card .pay {
        font-size: 16px;
        font-weight: 400;
        color: #000000;
    }
    .left-job-list .card .apply-job, .main-job-detail .card .apply-job {
        font-size: 15px;
        font-weight: 400;
        color: #000000;
        min-width: fit-content;
        width: 100%;
        max-width: 211px;
        background-color: #6DC0CB9C;
        border-radius: 12px;
        transition: all 500ms ease;
    }
    .left-job-list .card .apply-job:hover, .main-job-detail .card .apply-job:hover  {
        background-color: #6DC0CB;
    }

    .left-job-list .card .apply-job a, .main-job-detail .card .apply-job a {
        color: #000000;
    }

    .main-job-detail .job-title {
        font-size: clamp(21px , 2vw + 1rem, 26px);
        font-weight: 700;
        color: #000000;
    }
    .main-job-detail .posted {
        font-size: clamp(18px , 2vw + 1rem, 20px);
        font-weight: 400;
        color: #000000;
    }
    .main-job-detail .info {
        font-size: 16px;
        font-weight: 400;
        text-transform: capitalize;
    }
    .main-job-detail .company.info {
        color: #333ABF;
        text-decoration: underline;
    }
    .main-job-detail .pay {
        font-size: clamp(18px , 2vw + 1rem, 21px);
        font-weight: 400;
        color: #000000;
    }
    .main-job-detail .skill-lable {
        font-size: 17px;
        font-weight: 400;
        color: #000000;
        background-color: #A6D8DF;
        border-radius: 8px;
    }
    .main-job-detail .skill {
        font-size: 17px;
        font-weight: 400;
        color: #000000;
        background-color: #9FCBD19C;
        border-radius: 8px;
        padding: 3px 12px;
    }

    

    .main-job-detail .divider {
        background-color: #D3D3D3;
        height: 2px;
        width: 70%;
        border: none;
    }
    .main-job-detail .divider span {
        background-color: #1C52A8;
        height: 2px;
        width: 10%;
    }

    .main-job-detail .label-title, .deadline {
        font-size: 17px;
        font-weight: 400;
        color: #000000;
    }
    .main-job-detail .desc-detail, .main-job-detail .desc-detail p, .main-job-detail .paid-per{
        font-size: 17px;
        font-weight: 300;
        color: #000000;
    }
    .main-job-detail .desc-detail p:not(:first-child) {
        margin-top: 12px;
    }

    .main-job-detail .paid-per {
        font-weight: 400;
    }
    .main-job-detail .paid-per span {
        font-weight: 700;
    }

    .scroll-container::-webkit-scrollbar {
        display: none;
    }
  
    .scroll-container {
        scrollbar-width: none;
    }

    .left-job-list {
        height: 800px;
        overflow-y: auto;
    }
    .perks .perks-heading {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .perks .perks-heading h6 {
        font-size: 17px;
        color: #1C52A8;
        font-weight: 700;
        line-height: 1.2;
    }
    .perks p {
        font-size: 16px;
        font-weight: 400;
        color: #000000;
        line-height: 1.5;
        margin-top: 16px;
    }

    .social-links h6 {
        font-size: 19px;
        color: #444444;
        font-weight: 400;
    } 

</style>


<div class="bg-white employer-branding">
    <section class="px-xl-5" style="background: #DFECF0;">
        <div class="container py-5">
            <h1 class="company-header-title">Company Name</h1>
            <p class="d-flex align-items-center" style="gap: 12px;">
                <a href="" class="company-url">https://www.companyname.com</a>
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.2857 0C11.2312 0 12 0.76875 12 1.71429V10.2857C12 11.2312 11.2312 12 10.2857 12H1.71429C0.76875 12 0 11.2312 0 10.2857V1.71429C0 0.76875 0.76875 0 1.71429 0H10.2857ZM4.28571 3C3.92946 3 3.64286 3.28661 3.64286 3.64286C3.64286 3.99911 3.92946 4.28571 4.28571 4.28571H6.80625L3.1875 7.90179C2.93571 8.15357 2.93571 8.56071 3.1875 8.80982C3.43929 9.05893 3.84643 9.06161 4.09554 8.80982L7.71161 5.19375V7.92857C7.71161 8.28482 7.99821 8.57143 8.35446 8.57143C8.71071 8.57143 8.99732 8.28482 8.99732 7.92857V3.64286C8.99732 3.28661 8.71071 3 8.35446 3H4.28571Z" fill="black"/>
                </svg>
            </p>
            <div class="row py-5 mb-5">
                <div class="col-md-6 col-xl-3">
                    <div class="d-flex" style="gap: 12px;">
                        <svg width="32" height="26" viewBox="0 0 32 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.4 0C1.075 0 0 1.075 0 2.4V23.2C0 24.525 1.075 25.6 2.4 25.6H7.2V21.6C7.2 20.275 8.275 19.2 9.6 19.2C10.925 19.2 12 20.275 12 21.6V25.6H16.8C17.555 25.6 18.225 25.255 18.665 24.71C17.02 23.11 16 20.875 16 18.4C16 15.665 17.245 13.225 19.2 11.61V2.4C19.2 1.075 18.125 0 16.8 0H2.4ZM3.2 12C3.2 11.56 3.56 11.2 4 11.2H5.6C6.04 11.2 6.4 11.56 6.4 12V13.6C6.4 14.04 6.04 14.4 5.6 14.4H4C3.56 14.4 3.2 14.04 3.2 13.6V12ZM8.8 11.2H10.4C10.84 11.2 11.2 11.56 11.2 12V13.6C11.2 14.04 10.84 14.4 10.4 14.4H8.8C8.36 14.4 8 14.04 8 13.6V12C8 11.56 8.36 11.2 8.8 11.2ZM12.8 12C12.8 11.56 13.16 11.2 13.6 11.2H15.2C15.64 11.2 16 11.56 16 12V13.6C16 14.04 15.64 14.4 15.2 14.4H13.6C13.16 14.4 12.8 14.04 12.8 13.6V12ZM4 4.8H5.6C6.04 4.8 6.4 5.16 6.4 5.6V7.2C6.4 7.64 6.04 8 5.6 8H4C3.56 8 3.2 7.64 3.2 7.2V5.6C3.2 5.16 3.56 4.8 4 4.8ZM8 5.6C8 5.16 8.36 4.8 8.8 4.8H10.4C10.84 4.8 11.2 5.16 11.2 5.6V7.2C11.2 7.64 10.84 8 10.4 8H8.8C8.36 8 8 7.64 8 7.2V5.6ZM13.6 4.8H15.2C15.64 4.8 16 5.16 16 5.6V7.2C16 7.64 15.64 8 15.2 8H13.6C13.16 8 12.8 7.64 12.8 7.2V5.6C12.8 5.16 13.16 4.8 13.6 4.8ZM32 18.4C32 16.4904 31.2414 14.6591 29.8912 13.3088C28.5409 11.9586 26.7096 11.2 24.8 11.2C22.8904 11.2 21.0591 11.9586 19.7088 13.3088C18.3586 14.6591 17.6 16.4904 17.6 18.4C17.6 20.3096 18.3586 22.1409 19.7088 23.4912C21.0591 24.8414 22.8904 25.6 24.8 25.6C26.7096 25.6 28.5409 24.8414 29.8912 23.4912C31.2414 22.1409 32 20.3096 32 18.4ZM28.165 16.235C28.475 16.545 28.475 17.055 28.165 17.365L24.565 20.965C24.255 21.275 23.745 21.275 23.435 20.965L21.435 18.965C21.125 18.655 21.125 18.145 21.435 17.835C21.745 17.525 22.255 17.525 22.565 17.835L24 19.27L27.035 16.235C27.345 15.925 27.855 15.925 28.165 16.235Z" fill="#444444"/>
                        </svg>
                        <div class="d-flex flex-column" style="gap: 4px;">
                            <span class="intro">Domain</span>
                            <span class="intro-content">Consulting, E Commerce</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="d-flex" style="gap: 12px;">
                        <svg width="24" height="32" viewBox="0 0 24 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.4812 31.2C16.6875 27.1875 24 17.4625 24 12C24 5.375 18.625 0 12 0C5.375 0 0 5.375 0 12C0 17.4625 7.3125 27.1875 10.5188 31.2C11.2875 32.1562 12.7125 32.1562 13.4812 31.2ZM12 8C13.0609 8 14.0783 8.42143 14.8284 9.17157C15.5786 9.92172 16 10.9391 16 12C16 13.0609 15.5786 14.0783 14.8284 14.8284C14.0783 15.5786 13.0609 16 12 16C10.9391 16 9.92172 15.5786 9.17157 14.8284C8.42143 14.0783 8 13.0609 8 12C8 10.9391 8.42143 9.92172 9.17157 9.17157C9.92172 8.42143 10.9391 8 12 8Z" fill="#444444"/>
                        </svg>

                        <div class="d-flex flex-column" style="gap: 4px;">
                            <span class="intro">Location</span>
                            <span class="intro-content">Tokyo Japan and at 12 More Places</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="d-flex" style="gap: 12px;">
                        <svg width="32" height="26" viewBox="0 0 32 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.8 6.4C4.8 4.70261 5.47428 3.07475 6.67452 1.87452C7.87475 0.674284 9.50261 0 11.2 0C12.8974 0 14.5253 0.674284 15.7255 1.87452C16.9257 3.07475 17.6 4.70261 17.6 6.4C17.6 8.09739 16.9257 9.72525 15.7255 10.9255C14.5253 12.1257 12.8974 12.8 11.2 12.8C9.50261 12.8 7.87475 12.1257 6.67452 10.9255C5.47428 9.72525 4.8 8.09739 4.8 6.4ZM0 24.115C0 19.19 3.99 15.2 8.915 15.2H13.485C18.41 15.2 22.4 19.19 22.4 24.115C22.4 24.935 21.735 25.6 20.915 25.6H1.485C0.665 25.6 0 24.935 0 24.115ZM30.465 25.6H23.57C23.84 25.13 24 24.585 24 24V23.6C24 20.565 22.645 17.84 20.51 16.01C20.63 16.005 20.745 16 20.865 16H23.935C28.39 16 32 19.61 32 24.065C32 24.915 31.31 25.6 30.465 25.6ZM21.6 12.8C20.05 12.8 18.65 12.17 17.635 11.155C18.62 9.825 19.2 8.18 19.2 6.4C19.2 5.06 18.87 3.795 18.285 2.685C19.215 2.005 20.36 1.6 21.6 1.6C24.695 1.6 27.2 4.105 27.2 7.2C27.2 10.295 24.695 12.8 21.6 12.8Z" fill="#444444"/>
                        </svg>


                        <div class="d-flex flex-column" style="gap: 4px;">
                            <span class="intro">Company Size</span>
                            <span class="intro-content">200 to 500 Employees</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="d-flex" style="gap: 12px;">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.9062 4.3L14.0813 8H14H9.5C8.11875 8 7 6.88125 7 5.5C7 4.11875 8.11875 3 9.5 3H9.6375C10.5687 3 11.4375 3.49375 11.9062 4.3ZM4 5.5C4 6.4 4.21875 7.25 4.6 8H2C0.89375 8 0 8.89375 0 10V14C0 15.1062 0.89375 16 2 16H30C31.1063 16 32 15.1062 32 14V10C32 8.89375 31.1063 8 30 8H27.4C27.7812 7.25 28 6.4 28 5.5C28 2.4625 25.5375 0 22.5 0H22.3625C20.3687 0 18.5188 1.05625 17.5063 2.775L16 5.34375L14.4937 2.78125C13.4812 1.05625 11.6312 0 9.6375 0H9.5C6.4625 0 4 2.4625 4 5.5ZM25 5.5C25 6.88125 23.8813 8 22.5 8H18H17.9188L20.0938 4.3C20.5688 3.49375 21.4312 3 22.3625 3H22.5C23.8813 3 25 4.11875 25 5.5ZM2 18V29C2 30.6562 3.34375 32 5 32H14V18H2ZM18 32H27C28.6562 32 30 30.6562 30 29V18H18V32Z" fill="#444444"/>
                        </svg>



                        <div class="d-flex flex-column" style="gap: 4px;">
                            <span class="intro">Perks & Benefits</span>
                            <span class="intro-content">Internet Reimbursement and more</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container px-xl-5"  style="margin-top: -100px;">
        <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="d-block w-100" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png" alt="First slide" style="object-fit: cover;">
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png" alt="Second slide" style="object-fit: cover;">
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png" alt="Third slide" style="object-fit: cover;">
                </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M32 16C32 20.2435 30.3143 24.3131 27.3137 27.3137C24.3131 30.3143 20.2435 32 16 32C11.7565 32 7.68687 30.3143 4.68629 27.3137C1.68571 24.3131 0 20.2435 0 16C0 11.7565 1.68571 7.68687 4.68629 4.68629C7.68687 1.68571 11.7565 0 16 0C20.2435 0 24.3131 1.68571 27.3137 4.68629C30.3143 7.68687 32 11.7565 32 16ZM16.9375 23.5625C17.525 24.15 18.475 24.15 19.0562 23.5625C19.6375 22.975 19.6437 22.025 19.0562 21.4438L13.6187 16.0063L19.0562 10.5688C19.6437 9.98125 19.6437 9.03125 19.0562 8.45C18.4688 7.86875 17.5188 7.8625 16.9375 8.45L10.4375 14.9375C9.85 15.525 9.85 16.475 10.4375 17.0562L16.9375 23.5625Z" fill="black"/>
                </svg>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 16C0 20.2435 1.68571 24.3131 4.68629 27.3137C7.68687 30.3143 11.7565 32 16 32C20.2435 32 24.3131 30.3143 27.3137 27.3137C30.3143 24.3131 32 20.2435 32 16C32 11.7565 30.3143 7.68687 27.3137 4.68629C24.3131 1.68571 20.2435 0 16 0C11.7565 0 7.68687 1.68571 4.68629 4.68629C1.68571 7.68687 0 11.7565 0 16ZM15.0625 23.5625C14.475 24.15 13.525 24.15 12.9438 23.5625C12.3625 22.975 12.3563 22.025 12.9438 21.4438L18.3813 16.0063L12.9438 10.5688C12.3563 9.98125 12.3563 9.03125 12.9438 8.45C13.5312 7.86875 14.4812 7.8625 15.0625 8.45L21.5625 14.9375C22.15 15.525 22.15 16.475 21.5625 17.0562L15.0625 23.5625Z" fill="#0A0A0A"/>
                </svg>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>



    <div class="container pt-5 px-lg-5">
        <div class="row">
            <div class="col-lg-6 pt-3">
                <h2 class="company-title">Company Name</h2>
                <p class="tagline-text">Company Tagline Here</p>
            </div>
            <div class="col-lg-6 pt-3 d-flex align-items-start justify-content-end" style="gap: 30px;">
                <div>
                    <div class="d-flex align-items-center" style="gap: 4px;">
                        <a href="" class="d-flex align-items-center justify-content-center" style="background: #FF6600; width: 30px; height: 30px;">
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.42486 0.579483C9.25424 0.225355 8.89367 0 8.49769 0C8.10171 0 7.74436 0.225355 7.57051 0.579483L5.50047 4.83869L0.877481 5.52119C0.491159 5.57914 0.169224 5.84956 0.0501075 6.21979C-0.0690086 6.59001 0.0275719 6.99887 0.304436 7.27252L3.659 10.5917L2.86704 15.2823C2.80265 15.6686 2.96362 16.0613 3.28234 16.2899C3.60105 16.5185 4.02279 16.5475 4.37048 16.364L8.50091 14.1587L12.6313 16.364C12.979 16.5475 13.4008 16.5217 13.7195 16.2899C14.0382 16.0581 14.1992 15.6686 14.1348 15.2823L13.3396 10.5917L16.6942 7.27252C16.971 6.99887 17.0708 6.59001 16.9485 6.21979C16.8262 5.84956 16.5074 5.57914 16.1211 5.52119L11.4949 4.83869L9.42486 0.579483Z" fill="white"/>
                            </svg>
                        </a>
                        <a href="" class="d-flex align-items-center justify-content-center" style="background: #FF6600; width: 30px; height: 30px;">
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.42486 0.579483C9.25424 0.225355 8.89367 0 8.49769 0C8.10171 0 7.74436 0.225355 7.57051 0.579483L5.50047 4.83869L0.877481 5.52119C0.491159 5.57914 0.169224 5.84956 0.0501075 6.21979C-0.0690086 6.59001 0.0275719 6.99887 0.304436 7.27252L3.659 10.5917L2.86704 15.2823C2.80265 15.6686 2.96362 16.0613 3.28234 16.2899C3.60105 16.5185 4.02279 16.5475 4.37048 16.364L8.50091 14.1587L12.6313 16.364C12.979 16.5475 13.4008 16.5217 13.7195 16.2899C14.0382 16.0581 14.1992 15.6686 14.1348 15.2823L13.3396 10.5917L16.6942 7.27252C16.971 6.99887 17.0708 6.59001 16.9485 6.21979C16.8262 5.84956 16.5074 5.57914 16.1211 5.52119L11.4949 4.83869L9.42486 0.579483Z" fill="white"/>
                            </svg>
                        </a>
                        <a href="" class="d-flex align-items-center justify-content-center" style="background: #FF6600; width: 30px; height: 30px;">
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.42486 0.579483C9.25424 0.225355 8.89367 0 8.49769 0C8.10171 0 7.74436 0.225355 7.57051 0.579483L5.50047 4.83869L0.877481 5.52119C0.491159 5.57914 0.169224 5.84956 0.0501075 6.21979C-0.0690086 6.59001 0.0275719 6.99887 0.304436 7.27252L3.659 10.5917L2.86704 15.2823C2.80265 15.6686 2.96362 16.0613 3.28234 16.2899C3.60105 16.5185 4.02279 16.5475 4.37048 16.364L8.50091 14.1587L12.6313 16.364C12.979 16.5475 13.4008 16.5217 13.7195 16.2899C14.0382 16.0581 14.1992 15.6686 14.1348 15.2823L13.3396 10.5917L16.6942 7.27252C16.971 6.99887 17.0708 6.59001 16.9485 6.21979C16.8262 5.84956 16.5074 5.57914 16.1211 5.52119L11.4949 4.83869L9.42486 0.579483Z" fill="white"/>
                            </svg>
                        </a>
                        <a href="" class="d-flex align-items-center justify-content-center" style="background: #FF6600; width: 30px; height: 30px;">
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.42486 0.579483C9.25424 0.225355 8.89367 0 8.49769 0C8.10171 0 7.74436 0.225355 7.57051 0.579483L5.50047 4.83869L0.877481 5.52119C0.491159 5.57914 0.169224 5.84956 0.0501075 6.21979C-0.0690086 6.59001 0.0275719 6.99887 0.304436 7.27252L3.659 10.5917L2.86704 15.2823C2.80265 15.6686 2.96362 16.0613 3.28234 16.2899C3.60105 16.5185 4.02279 16.5475 4.37048 16.364L8.50091 14.1587L12.6313 16.364C12.979 16.5475 13.4008 16.5217 13.7195 16.2899C14.0382 16.0581 14.1992 15.6686 14.1348 15.2823L13.3396 10.5917L16.6942 7.27252C16.971 6.99887 17.0708 6.59001 16.9485 6.21979C16.8262 5.84956 16.5074 5.57914 16.1211 5.52119L11.4949 4.83869L9.42486 0.579483Z" fill="white"/>
                            </svg>
                        </a>
                        <a href="" class="d-flex align-items-center justify-content-center" style="background: #D3D3D3; width: 30px; height: 30px;">
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.42486 0.579483C9.25424 0.225355 8.89367 0 8.49769 0C8.10171 0 7.74436 0.225355 7.57051 0.579483L5.50047 4.83869L0.877481 5.52119C0.491159 5.57914 0.169224 5.84956 0.0501075 6.21979C-0.0690086 6.59001 0.0275719 6.99887 0.304436 7.27252L3.659 10.5917L2.86704 15.2823C2.80265 15.6686 2.96362 16.0613 3.28234 16.2899C3.60105 16.5185 4.02279 16.5475 4.37048 16.364L8.50091 14.1587L12.6313 16.364C12.979 16.5475 13.4008 16.5217 13.7195 16.2899C14.0382 16.0581 14.1992 15.6686 14.1348 15.2823L13.3396 10.5917L16.6942 7.27252C16.971 6.99887 17.0708 6.59001 16.9485 6.21979C16.8262 5.84956 16.5074 5.57914 16.1211 5.52119L11.4949 4.83869L9.42486 0.579483Z" fill="white"/>
                            </svg>
                        </a>
                    </div>
                    <p class="review-text mt-2">Rated 4.0 based on 102 Reviews</p>
                </div>
                <div>
                    <button type="button" class="btn px-3" style="background: #333ABF; border-radius: 12px;"><a href="" class="follow-btn " >Follow</a></button>
                </div>
            </div>
        </div>

        <!-- company info tabs -->
        <div id="" class="row mt-3" style="overflow-x: hidden;">  
            <!-- Nav Tabs -->
            <ul class="nav nav-tabs col-12 mx-auto d-flex flex-nowrap justify-content-between company-info-tabs scroll-container" role="tablist" style="gap: 12px; overflow-x: auto; border-bottom: 1px solid #D3D3D3">
                <li class="nav-item">
                    <a class="nav-link company-info active py-3 px-2 text-nowrap" id="about-tab" data-toggle="tab" href="#about" role="tab" aria-controls="about" aria-selected="true">About</a>
                </li>
                <li class="nav-item">
                    <a href="#jobs" class="nav-link company-info py-3 px-2 text-nowrap d-inline-flex align-items-center" id="jobs-tab" data-toggle="tab" href="#jobs" role="tab" aria-controls="jobs" aria-selected="false">Jobs <span class="ml-2 d-inline-flex align-items-center justify-content-center" style="width: 30px; height: 30px; border-radius: 50%; background: #333ABF; color: #fff;font-size: 12px;">21</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link company-info py-3 px-2 text-nowrap" id="locations-tab" data-toggle="tab" href="#locations" role="tab" aria-controls="locations" aria-selected="false">Locations</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link company-info py-3 px-2 text-nowrap" id="culture-tab" data-toggle="tab" href="#culture" role="tab" aria-controls="culture" aria-selected="false">Culture</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link company-info py-3 px-2 text-nowrap" id="perks-tab" data-toggle="tab" href="#perks" role="tab" aria-controls="perks" aria-selected="false">Perks & Benefits</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- tab contents -->
    <div class="tab-content">

        <!--  about page -->
        <div class="container pb-5 px-lg-5 tab-pane fade show active" id="about" role="tabpanel" aria-labelledby="about-tab">
            <div class="py-5 row mx-0">
            
                <h2 class="headline">Headline or Mission of the Company</h2>
                <p class="content-detail mt-3">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna 1 aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                </p>
                <p class="content-detail mt-3">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna 1 aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    <br>
                </p>
                <a href="#" class="content-detail read-more mt-3">Read More ...</a>
            
            </div>

            <div class="row d-flex align-items-center justify-content-between">
                <div class="col-xl-7" style="position: relative; max-height: 566px;">
                    <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_gallery_1.png" alt="" style="width: 100%; object-fit: cover; border-radius: 6px; max-height: 550px;">
                    <svg style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);" width="121" height="121" viewBox="0 0 121 121" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 60.5C0 44.4544 6.37409 29.066 17.72 17.72C29.066 6.37409 44.4544 0 60.5 0C76.5456 0 91.934 6.37409 103.28 17.72C114.626 29.066 121 44.4544 121 60.5C121 76.5456 114.626 91.934 103.28 103.28C91.934 114.626 76.5456 121 60.5 121C44.4544 121 29.066 114.626 17.72 103.28C6.37409 91.934 0 76.5456 0 60.5ZM44.5006 34.7639C42.7045 35.7564 41.5938 37.6707 41.5938 39.7031V81.2969C41.5938 83.3529 42.7045 85.2436 44.5006 86.2361C46.2967 87.2287 48.4709 87.2051 50.2434 86.118L84.2746 65.3211C85.9525 64.2812 86.9924 62.4615 86.9924 60.4764C86.9924 58.4912 85.9525 56.6715 84.2746 55.6316L50.2434 34.8348C48.4945 33.7713 46.2967 33.724 44.5006 34.7166V34.7639Z" fill="#CACACA"/>
                    </svg>
                </div>

                <div class="col-xl-5 row mx-auto mt-4 mt-xl-0 pt-lg-0 d-flex flex-row flex-xl-column align-items-between px-0" style="max-height: 566px;">
                    <div class="col-md px-xl-0 mb-4" style=" max-height: 263px;">
                        <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_gallery_2.png" alt="" style="height: 100%;  width: 100%; object-fit: cover; border-radius: 6px;">
                    </div>

                    <!-- gallery col -->
                    <div class="col-md px-0 mx-3 mx-md-0 mb-4 mb-xl-0" style="position: relative; max-height: 263px;">
                        <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_gallery_2.png" alt="" style="height: 100%; width: 100%; object-fit: cover; border-radius: 6px;">
                        <div style="position: absolute; inset: 0; background: #4F4F4FBA; border-radius: 8px;">
                            <a href="" class="d-flex flex-column align-items-center justify-content-center" style="width: 100%; height: 100%;">
                                <svg width="32" height="25" viewBox="0 0 32 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.88889 0C6.92778 0 5.33333 1.59444 5.33333 3.55556V16C5.33333 17.9611 6.92778 19.5556 8.88889 19.5556H28.4444C30.4056 19.5556 32 17.9611 32 16V3.55556C32 1.59444 30.4056 0 28.4444 0H8.88889ZM22 5.92778L27.3333 13.9278C27.6056 14.3389 27.6333 14.8611 27.4 15.2944C27.1667 15.7278 26.7167 16 26.2222 16H18.2222H15.5556H11.1111C10.6 16 10.1333 15.7056 9.91111 15.2444C9.68889 14.7833 9.75 14.2333 10.0722 13.8333L13.6278 9.38889C13.8833 9.07222 14.2611 8.88889 14.6667 8.88889C15.0722 8.88889 15.4556 9.07222 15.7056 9.38889L16.6667 10.5889L19.7778 5.92222C20.0278 5.55556 20.4444 5.33333 20.8889 5.33333C21.3333 5.33333 21.75 5.55556 22 5.92778ZM10.6667 5.33333C10.6667 4.86184 10.854 4.40965 11.1874 4.07625C11.5208 3.74286 11.9729 3.55556 12.4444 3.55556C12.9159 3.55556 13.3681 3.74286 13.7015 4.07625C14.0349 4.40965 14.2222 4.86184 14.2222 5.33333C14.2222 5.80483 14.0349 6.25701 13.7015 6.59041C13.3681 6.92381 12.9159 7.11111 12.4444 7.11111C11.9729 7.11111 11.5208 6.92381 11.1874 6.59041C10.854 6.25701 10.6667 5.80483 10.6667 5.33333ZM2.66667 4.88889C2.66667 4.15 2.07222 3.55556 1.33333 3.55556C0.594444 3.55556 0 4.15 0 4.88889V17.3333C0 21.5056 3.38333 24.8889 7.55556 24.8889H25.3333C26.0722 24.8889 26.6667 24.2944 26.6667 23.5556C26.6667 22.8167 26.0722 22.2222 25.3333 22.2222H7.55556C4.85556 22.2222 2.66667 20.0333 2.66667 17.3333V4.88889Z" fill="white"/>
                                </svg>
                                <p style="color: #fff;">View our Gallery</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!--  jobs page -->
        <div class="container pb-5 pt-3 px-lg-5 tab-pane fade" id="jobs" role="tabpanel" aria-labelledby="jobs-tab">
          <!-- search -->
            <section class="py-5">
                    <div  class="container">
                        <div class="search-filter-section" style="max-width: 1000px; margin: 0 auto">
                            <form id="searchFilter"  class="search-form p-0 rounded-0 bg-transparent shadow-none position-relative z-index-1">
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <span id="searchClear" style="display:none" onclick="clearBtn('search')" class="badge badge-danger">
                                            <i class="la la-close"></i>
                                        </span>
                                        <div class="icon-position-form">
                                            <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/search.svg' ?>" style="width: 14px; position: absolute; left: 10px; top: 10px;" alt="Search">
                                            <input type="search" class="form-control" id="query" name="query" placeholder="Search with Job Title, Company Name">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <span id="locationClear" style="display:none" onclick="clearBtn('geohash')" class="badge badge-danger">
                                            <i class="la la-close"></i>
                                        </span>
                                        <div class="icon-position-form">
                                            <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/geo-alt-fill.svg'; ?>" style="width: 14px; position: absolute; left: 10px; top: 12px; z-index: 2;" alt="Location">
                                            <input type="search" class="form-control" id="query" name="query" placeholder="Search by Location">
                                        </div>
                                    </div>
                                    <div class="col-md-3 date-range-dropdown">
                                        <div class="icon-position-form">
                                        <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/calendar3.svg';?>" style="width: 14px; position: absolute; left: 10px; top: 12px; z-index: 2;" alt="Date">
                                            <select id="postdate" name="post_date" class="form-control">
                                                <option value="" selected>Select Post Date</option>
                                                <option value="today">Today</option>
                                                <option value="yesterday">Yesterday</option>
                                                <option value="last_week">Last Week</option>
                                                <option value="last_month">Last Month</option>
                                                <option value="date_range">Date Range</option>
                                            </select>
                                        </div>
                                        <div class="form-row" id="dateRangeInputs" style="display: none;">
                                            <div class="col-6">
                                                <div class="icon-position-form">
                                                <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/calendar3.svg';?>" style="width: 14px; position: absolute; left: 10px; top: 12px; z-index: 2;" alt="Date">
                                                    <input type="date" id="from_date" name="from_date" class="form-control" placeholder=" Filter by Date">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="icon-position-form">
                                                <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/calendar3.svg';?>" style="width: 14px; position: absolute; left: 10px; top: 12px; z-index: 2;" alt="Date">
                                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder=" Filter by Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button style="border-radius:15px;" class="btn btn-primary btn-block">Search <i class="la la-search ml-1"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
            </section>

            <!-- job listing and job detail -->
             <div class="row">
                <!-- listing -->
                <div class="col-lg-4 left-job-list d-flex flex-column scroll-container" style="gap: 12px;">
                    <div class="card px-3 py-3">
                        <h6 class="company pb-2">COMPANY NAME</h6>
                        <h4 class="job-title pb-1">Job Title / Position  </h4>
                        <p class="location pb-1">Remote or Location of the Company</p>
                        <p class="pay pb-1">₹ 50 - 65 K Per Month</p>
                        <button type="button" class="btn apply-job mt-1"><a href="#">Apply</a></button>
                    </div>
                    <div class="card px-3 py-3">
                        <h6 class="company pb-2">COMPANY NAME</h6>
                        <h4 class="job-title pb-1">Job Title / Position  </h4>
                        <p class="location pb-1">Remote or Location of the Company</p>
                        <p class="pay pb-1">₹ 50 - 65 K Per Month</p>
                        <button type="button" class="btn apply-job mt-1"><a href="#">Apply</a></button>
                    </div>
                    <div class="card px-3 py-3">
                        <h6 class="company pb-2">COMPANY NAME</h6>
                        <h4 class="job-title pb-1">Job Title / Position  </h4>
                        <p class="location pb-1">Remote or Location of the Company</p>
                        <p class="pay pb-1">₹ 50 - 65 K Per Month</p>
                        <button type="button" class="btn apply-job mt-1"><a href="#">Apply</a></button>
                    </div>
                </div>

                <!-- row -->
                 <div class="col-lg-8 main-job-detail">
                    <div class="card px-4 py-4">
                        <div class="d-flex align-items-start justify-content-between flex-wrap">
                            <div class="" style="">
                                <span class="job-title">JOB TITLE / POSITION</span>

                                <span class="d-inline-flex align-items-center">
                                    <img class="mx-2" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark.svg" alt="">
                                </span>

                                <span class="d-inline-flex align-items-center">
                                    <svg width="18" height="18" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16.2672 7.19106L16.5848 7.03239L16.5397 6.68017C16.5117 6.46201 16.5 6.23383 16.5 6C16.5 2.96364 18.9636 0.5 22 0.5C25.0364 0.5 27.5 2.96364 27.5 6C27.5 9.03636 25.0364 11.5 22 11.5C20.5151 11.5 19.1702 10.9158 18.1858 9.96002L17.9314 9.71297L17.6141 9.87144L11.7328 12.8089L11.4152 12.9676L11.4603 13.3198C11.4883 13.5382 11.5 13.7602 11.5 14C11.5 14.2398 11.4883 14.4618 11.4603 14.6802L11.4152 15.0324L11.7328 15.1911L17.6141 18.1286L17.9314 18.287L18.1858 18.04C19.1702 17.0842 20.5151 16.5 22 16.5C25.0364 16.5 27.5 18.9636 27.5 22C27.5 25.0364 25.0364 27.5 22 27.5C18.9636 27.5 16.5 25.0364 16.5 22C16.5 21.7602 16.5117 21.5382 16.5397 21.3198L16.5848 20.9676L16.2672 20.8089L10.3859 17.8714L10.0686 17.713L9.8142 17.96C8.82976 18.9158 7.48492 19.5 6 19.5C2.96364 19.5 0.5 17.0364 0.5 14C0.5 10.9636 2.96364 8.5 6 8.5C7.48492 8.5 8.82976 9.08416 9.8142 10.04L10.0686 10.287L10.3859 10.1286L16.2672 7.19106Z" fill="#333333" stroke="#333333"/>
                                    </svg>
                                </span>
                            </div>
                            <p>
                                <time class="posted" datetime="">Posted 6 Days ago</time>
                            </p>
                        </div>
                        <div class="py-2">
                            <span class="company info">Company Name</span>
                            <span class="info px-3">Remote | Full Time</span>
                            <span class="info">Company Location</span>
                        </div>
                        
                        <p class="pay">₹40,000 - 50,0000</p>
                        <button type="button" class="btn apply-job my-3"><a href="#">Apply</a></button>

                        <!-- skills -->
                        <div class="mt-2">
                            <div>
                                <svg width="23" height="19" viewBox="0 0 23 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.6678 1.93841L11.8228 0.226932C11.6832 -0.0702495 11.2464 -0.080991 11.0924 0.226932L10.2474 1.93841L8.37479 2.20695C8.04181 2.25708 7.89858 2.66883 8.14564 2.91589L9.50623 4.24067L9.18398 6.10611C9.13386 6.4391 9.47759 6.6969 9.78551 6.54294L11.4648 5.65497L13.1333 6.52861C13.4412 6.68258 13.7885 6.42478 13.7348 6.09179L13.4126 4.22635L14.7731 2.91589C15.0166 2.67241 14.877 2.26066 14.544 2.20695L12.6714 1.93841H12.6678ZM9.16608 9.16744C8.53233 9.16744 8.02032 9.67945 8.02032 10.3132V17.1878C8.02032 17.8215 8.53233 18.3335 9.16608 18.3335H13.7491C14.3829 18.3335 14.8949 17.8215 14.8949 17.1878V10.3132C14.8949 9.67945 14.3829 9.16744 13.7491 9.16744H9.16608ZM1.14576 11.459C0.512012 11.459 0 11.971 0 12.6047V17.1878C0 17.8215 0.512012 18.3335 1.14576 18.3335H5.7288C6.36255 18.3335 6.87456 17.8215 6.87456 17.1878V12.6047C6.87456 11.971 6.36255 11.459 5.7288 11.459H1.14576ZM16.0406 14.8962V17.1878C16.0406 17.8215 16.5527 18.3335 17.1864 18.3335H21.7694C22.4032 18.3335 22.9152 17.8215 22.9152 17.1878V14.8962C22.9152 14.2625 22.4032 13.7505 21.7694 13.7505H17.1864C16.5527 13.7505 16.0406 14.2625 16.0406 14.8962Z" fill="#1C52A8"/>
                                </svg>
                                <span class="skill-lable px-3 py-1 ml-2">Skills</span>
                            </div>

                            <div class="d-flex flex-wrap col-lg-6 px-0 mt-3" style="gap: 15px;">
                                <span class="skill">Skill no 1</span>
                                <span class="skill">Skill no 2</span>
                                <span class="skill">Skill no 3</span>
                                <span class="skill">Skill no 4</span>
                                <span class="skill">Skill no 5</span>
                            </div>

                        </div>

                        <!-- job desc -->
                         <div class="mt-4">
                            <div class="d-flex align-items-center">
                                <svg width="32" height="25" viewBox="0 0 32 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3.55556 0C1.59444 0 0 1.59444 0 3.55556V21.3333C0 23.2944 1.59444 24.8889 3.55556 24.8889H28.4444C30.4056 24.8889 32 23.2944 32 21.3333V3.55556C32 1.59444 30.4056 0 28.4444 0H3.55556ZM8 14.2222H11.5556C14.0111 14.2222 16 16.2111 16 18.6667C16 19.1556 15.6 19.5556 15.1111 19.5556H4.44444C3.95556 19.5556 3.55556 19.1556 3.55556 18.6667C3.55556 16.2111 5.54444 14.2222 8 14.2222ZM6.22222 8.88889C6.22222 7.9459 6.59682 7.04153 7.26362 6.37473C7.93042 5.70794 8.83479 5.33333 9.77778 5.33333C10.7208 5.33333 11.6251 5.70794 12.2919 6.37473C12.9587 7.04153 13.3333 7.9459 13.3333 8.88889C13.3333 9.83188 12.9587 10.7363 12.2919 11.403C11.6251 12.0698 10.7208 12.4444 9.77778 12.4444C8.83479 12.4444 7.93042 12.0698 7.26362 11.403C6.59682 10.7363 6.22222 9.83188 6.22222 8.88889ZM20.4444 7.11111H27.5556C28.0444 7.11111 28.4444 7.51111 28.4444 8C28.4444 8.48889 28.0444 8.88889 27.5556 8.88889H20.4444C19.9556 8.88889 19.5556 8.48889 19.5556 8C19.5556 7.51111 19.9556 7.11111 20.4444 7.11111ZM20.4444 10.6667H27.5556C28.0444 10.6667 28.4444 11.0667 28.4444 11.5556C28.4444 12.0444 28.0444 12.4444 27.5556 12.4444H20.4444C19.9556 12.4444 19.5556 12.0444 19.5556 11.5556C19.5556 11.0667 19.9556 10.6667 20.4444 10.6667ZM20.4444 14.2222H27.5556C28.0444 14.2222 28.4444 14.6222 28.4444 15.1111C28.4444 15.6 28.0444 16 27.5556 16H20.4444C19.9556 16 19.5556 15.6 19.5556 15.1111C19.5556 14.6222 19.9556 14.2222 20.4444 14.2222Z" fill="#1C52A8"/>
                                </svg>

                                <span class="label-title ml-2">Job Description</span>
                            </div>

                            <div class="divider"><span></span></div>

                            <div class="desc-detail py-2">
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.Aenean ante nunc, ultrices ac lobortis cursus, dictum in nibh. Nullam pretium semper felis, vitae laoreet eros aliquam at. Praesent tellus sem, faucibus id dapibus ut, cursus elementum turpis. Phasellus a gravida ligula. Quisque sed aliquam tortor, eu varius libero. Vivamus euismod sapien nisi, non gravida leo tempor in. Curabitur molestie felis a ipsum gravida congue. Etiam at eros nulla. Nulla sagittis ultrices aliquet. Quisque dictum neque quis justo ullamcorper, in tristique lacus Suspendisse scelerisque gravida ante vitae fringilla.</p>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.Aenean ante nunc, ultrices ac lobortis cursus, dictum in nibh. Nullam pretium semper felis, vitae laoreet eros aliquam at. Praesent tellus sem, faucibus id dapibus ut, cursus elementum turpis.</p>
                            </div>

                         </div>

                         <!-- payment and application deadline -->
                          <div class="row d-flex flex-wrap justify-content-between mt-4">
                                <div class="col-lg-6 mt-2">
                                    <div class="d-flex align-items-center">
                                        <svg width="25" height="21" viewBox="0 0 25 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1.875 0.5C0.839844 0.5 0 1.33984 0 2.375V18.625C0 19.6602 0.839844 20.5 1.875 20.5H5.625V17.375C5.625 16.3398 6.46484 15.5 7.5 15.5C8.53516 15.5 9.375 16.3398 9.375 17.375V20.5H13.125C14.1602 20.5 15 19.6602 15 18.625V2.375C15 1.33984 14.1602 0.5 13.125 0.5H1.875ZM2.5 9.875C2.5 9.53125 2.78125 9.25 3.125 9.25H4.375C4.71875 9.25 5 9.53125 5 9.875V11.125C5 11.4688 4.71875 11.75 4.375 11.75H3.125C2.78125 11.75 2.5 11.4688 2.5 11.125V9.875ZM6.875 9.25H8.125C8.46875 9.25 8.75 9.53125 8.75 9.875V11.125C8.75 11.4688 8.46875 11.75 8.125 11.75H6.875C6.53125 11.75 6.25 11.4688 6.25 11.125V9.875C6.25 9.53125 6.53125 9.25 6.875 9.25ZM10 9.875C10 9.53125 10.2812 9.25 10.625 9.25H11.875C12.2188 9.25 12.5 9.53125 12.5 9.875V11.125C12.5 11.4688 12.2188 11.75 11.875 11.75H10.625C10.2812 11.75 10 11.4688 10 11.125V9.875ZM3.125 4.25H4.375C4.71875 4.25 5 4.53125 5 4.875V6.125C5 6.46875 4.71875 6.75 4.375 6.75H3.125C2.78125 6.75 2.5 6.46875 2.5 6.125V4.875C2.5 4.53125 2.78125 4.25 3.125 4.25ZM6.25 4.875C6.25 4.53125 6.53125 4.25 6.875 4.25H8.125C8.46875 4.25 8.75 4.53125 8.75 4.875V6.125C8.75 6.46875 8.46875 6.75 8.125 6.75H6.875C6.53125 6.75 6.25 6.46875 6.25 6.125V4.875ZM10.625 4.25H11.875C12.2188 4.25 12.5 4.53125 12.5 4.875V6.125C12.5 6.46875 12.2188 6.75 11.875 6.75H10.625C10.2812 6.75 10 6.46875 10 6.125V4.875C10 4.53125 10.2812 4.25 10.625 4.25ZM17.5 0.5C16.8086 0.5 16.25 1.05859 16.25 1.75V20.5H18.75V8H24.375C24.7188 8 25 7.71875 25 7.375V2.375C25 2.03125 24.7188 1.75 24.375 1.75H18.75C18.75 1.05859 18.1914 0.5 17.5 0.5Z" fill="#1C52A8"/>
                                        </svg>
                                        <span class="label-title ml-3">Payment Details</span>
                                    </div>
                                    <div class="divider d-flex"><span></span><span></span></div>

                                    <div>
                                        <p class="pay">₹40,000 - 50,0000</p>
                                        <p class="paid-per mt-1">paid on per <span>month</span> basis</p>
                                    </div>


                                </div>
                                <div class="col-lg-6 mt-2">
                                    <div class="d-flex align-items-center">
                                        <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5.14286 0C5.85402 0 6.42857 0.558594 6.42857 1.25V2.5H11.5714V1.25C11.5714 0.558594 12.146 0 12.8571 0C13.5683 0 14.1429 0.558594 14.1429 1.25V2.5H16.0714C17.1362 2.5 18 3.33984 18 4.375V6.25H0V4.375C0 3.33984 0.863839 2.5 1.92857 2.5H3.85714V1.25C3.85714 0.558594 4.4317 0 5.14286 0ZM0 7.5H18V18.125C18 19.1602 17.1362 20 16.0714 20H1.92857C0.863839 20 0 19.1602 0 18.125V7.5ZM3.21429 10C2.86071 10 2.57143 10.2812 2.57143 10.625V14.375C2.57143 14.7188 2.86071 15 3.21429 15H7.07143C7.425 15 7.71429 14.7188 7.71429 14.375V10.625C7.71429 10.2812 7.425 10 7.07143 10H3.21429Z" fill="#1C52A8"/>
                                        </svg> 
                                        <span class="label-title ml-3">Application Deadline</span>
                                    </div>
                                    <div class="divider d-flex"><span></span><span></span></div>
                                    <div>
                                        <p class="d-flex align-items-center">
                                            <svg width="18" height="21" viewBox="0 0 18 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5.14286 0C5.85402 0 6.42857 0.586523 6.42857 1.3125V2.625H11.5714V1.3125C11.5714 0.586523 12.146 0 12.8571 0C13.5683 0 14.1429 0.586523 14.1429 1.3125V2.625H16.0714C17.1362 2.625 18 3.50684 18 4.59375V6.5625H0V4.59375C0 3.50684 0.863839 2.625 1.92857 2.625H3.85714V1.3125C3.85714 0.586523 4.4317 0 5.14286 0ZM0 7.875H18V19.0312C18 20.1182 17.1362 21 16.0714 21H1.92857C0.863839 21 0 20.1182 0 19.0312V7.875ZM13.2188 12.5098C13.5964 12.1242 13.5964 11.5008 13.2188 11.1193C12.8411 10.7379 12.2304 10.7338 11.8567 11.1193L8.03973 15.0158L6.15134 13.0881C5.77366 12.7025 5.16295 12.7025 4.78929 13.0881C4.41562 13.4736 4.41161 14.0971 4.78929 14.4785L7.36071 17.1035C7.73839 17.4891 8.34911 17.4891 8.72277 17.1035L13.2188 12.5098Z" fill="#1C52A8"/>
                                            </svg>
                                            <span class="deadline ml-3">December 12, 2024</span>
                                        </p>

                                    </div>


                                </div>

                          </div>
                    </div>
                 </div>
             </div>

        </div>

        <!-- location -->
        <div class="container py-5 px-lg-5 tab-pane fade" id="locations" role="tabpanel" aria-labelledby="locations-tab">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mt-4" style="border: 1px solid #D3D3D3; border-radius: 0;">
                        <div class="card-body company-address">
                            <h4 class="mb-3">Country Name</h4>
                            <p>Address Line 1, Address Line 2</p>
                            <p>Address Line 3</p>
                            <p>City Name, State or Province</p>
                            <p> Country Name Zip Code</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mt-4" style="border: 1px solid #D3D3D3; border-radius: 0;">
                        <div class="card-body company-address">
                            <h4 class="mb-3">Country Name</h4>
                            <p>Address Line 1, Address Line 2</p>
                            <p>Address Line 3</p>
                            <p>City Name, State or Province</p>
                            <p> Country Name Zip Code</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mt-4" style="border: 1px solid #D3D3D3; border-radius: 0;">
                        <div class="card-body company-address">
                            <h4 class="mb-3">Country Name</h4>
                            <p>Address Line 1, Address Line 2</p>
                            <p>Address Line 3</p>
                            <p>City Name, State or Province</p>
                            <p> Country Name Zip Code</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- culture -->

        <div class="container pb-5 px-lg-5 tab-pane fade" id="culture" role="tabpanel" aria-labelledby="culture-tab">
            <div class="py-5 row mx-0">
            
            <h2 class="headline">Highlight of the Culture</h2>
            <p class="content-detail mt-3">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna 1 aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
            </p>
            <p class="content-detail mt-3">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna 1 aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                <br>
            </p>
            <a href="#" class="content-detail read-more mt-3">Read More ...</a>
            
            </div>

            <div class="row d-flex align-items-center justify-content-between">
                <div class="col-xl-7" style="position: relative; max-height: 566px;">
                    <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_gallery_1.png" alt="" style="width: 100%; object-fit: cover; border-radius: 6px; max-height: 550px;">
                    <svg style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);" width="121" height="121" viewBox="0 0 121 121" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 60.5C0 44.4544 6.37409 29.066 17.72 17.72C29.066 6.37409 44.4544 0 60.5 0C76.5456 0 91.934 6.37409 103.28 17.72C114.626 29.066 121 44.4544 121 60.5C121 76.5456 114.626 91.934 103.28 103.28C91.934 114.626 76.5456 121 60.5 121C44.4544 121 29.066 114.626 17.72 103.28C6.37409 91.934 0 76.5456 0 60.5ZM44.5006 34.7639C42.7045 35.7564 41.5938 37.6707 41.5938 39.7031V81.2969C41.5938 83.3529 42.7045 85.2436 44.5006 86.2361C46.2967 87.2287 48.4709 87.2051 50.2434 86.118L84.2746 65.3211C85.9525 64.2812 86.9924 62.4615 86.9924 60.4764C86.9924 58.4912 85.9525 56.6715 84.2746 55.6316L50.2434 34.8348C48.4945 33.7713 46.2967 33.724 44.5006 34.7166V34.7639Z" fill="#CACACA"/>
                    </svg>
                </div>

                <div class="col-xl-5 row mx-auto mt-4 mt-xl-0 pt-lg-0 d-flex flex-row flex-xl-column align-items-between px-0" style="max-height: 566px;">
                    <div class="col-md px-xl-0 mb-4" style=" max-height: 263px;">
                        <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_gallery_2.png" alt="" style="height: 100%;  width: 100%; object-fit: cover; border-radius: 6px;">
                    </div>

                    <!-- gallery col -->
                    <div class="col-md px-0 mx-3 mx-md-0 mb-4 mb-xl-0" style="position: relative; max-height: 263px;">
                        <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_gallery_2.png" alt="" style="height: 100%; width: 100%; object-fit: cover; border-radius: 6px;">
                        <div style="position: absolute; inset: 0; background: #4F4F4FBA; border-radius: 8px;">
                            <a href="" class="d-flex flex-column align-items-center justify-content-center" style="width: 100%; height: 100%;">
                                <svg width="32" height="25" viewBox="0 0 32 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.88889 0C6.92778 0 5.33333 1.59444 5.33333 3.55556V16C5.33333 17.9611 6.92778 19.5556 8.88889 19.5556H28.4444C30.4056 19.5556 32 17.9611 32 16V3.55556C32 1.59444 30.4056 0 28.4444 0H8.88889ZM22 5.92778L27.3333 13.9278C27.6056 14.3389 27.6333 14.8611 27.4 15.2944C27.1667 15.7278 26.7167 16 26.2222 16H18.2222H15.5556H11.1111C10.6 16 10.1333 15.7056 9.91111 15.2444C9.68889 14.7833 9.75 14.2333 10.0722 13.8333L13.6278 9.38889C13.8833 9.07222 14.2611 8.88889 14.6667 8.88889C15.0722 8.88889 15.4556 9.07222 15.7056 9.38889L16.6667 10.5889L19.7778 5.92222C20.0278 5.55556 20.4444 5.33333 20.8889 5.33333C21.3333 5.33333 21.75 5.55556 22 5.92778ZM10.6667 5.33333C10.6667 4.86184 10.854 4.40965 11.1874 4.07625C11.5208 3.74286 11.9729 3.55556 12.4444 3.55556C12.9159 3.55556 13.3681 3.74286 13.7015 4.07625C14.0349 4.40965 14.2222 4.86184 14.2222 5.33333C14.2222 5.80483 14.0349 6.25701 13.7015 6.59041C13.3681 6.92381 12.9159 7.11111 12.4444 7.11111C11.9729 7.11111 11.5208 6.92381 11.1874 6.59041C10.854 6.25701 10.6667 5.80483 10.6667 5.33333ZM2.66667 4.88889C2.66667 4.15 2.07222 3.55556 1.33333 3.55556C0.594444 3.55556 0 4.15 0 4.88889V17.3333C0 21.5056 3.38333 24.8889 7.55556 24.8889H25.3333C26.0722 24.8889 26.6667 24.2944 26.6667 23.5556C26.6667 22.8167 26.0722 22.2222 25.3333 22.2222H7.55556C4.85556 22.2222 2.66667 20.0333 2.66667 17.3333V4.88889Z" fill="white"/>
                                </svg>
                                <p style="color: #fff;">View our Gallery</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- perks and benifits -->

        <div class="container py-5 px-lg-5 tab-pane fade" id="perks" role="tabpanel" aria-labelledby="perks-tab">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mt-4" style="border: 1px solid #D3D3D3; border-radius: 0;">
                        <div class="card-body perks">
                            <div class="perks-heading">
                                <svg width="32" height="23" viewBox="0 0 32 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.70969 8.545C6.15969 5.235 10.8397 3.2 15.9997 3.2C21.1597 3.2 25.8397 5.235 29.2897 8.545C29.9297 9.155 30.9397 9.135 31.5497 8.5C32.1597 7.865 32.1397 6.85 31.5047 6.24C27.4847 2.375 22.0197 0 15.9997 0C9.97969 0 4.51469 2.375 0.489693 6.235C-0.145307 6.85 -0.165307 7.86 0.444693 8.5C1.05469 9.14 2.06969 9.16 2.70469 8.545H2.70969ZM15.9997 11.2C18.8397 11.2 21.4297 12.255 23.4097 14C24.0747 14.585 25.0847 14.52 25.6697 13.86C26.2547 13.2 26.1897 12.185 25.5297 11.6C22.9897 9.36 19.6497 8 15.9997 8C12.3497 8 9.00969 9.36 6.47469 11.6C5.80969 12.185 5.74969 13.195 6.33469 13.86C6.91969 14.525 7.92969 14.585 8.59469 14C10.5697 12.255 13.1597 11.2 16.0047 11.2H15.9997ZM19.1997 19.2C19.1997 18.3513 18.8626 17.5374 18.2624 16.9373C17.6623 16.3371 16.8484 16 15.9997 16C15.151 16 14.3371 16.3371 13.737 16.9373C13.1368 17.5374 12.7997 18.3513 12.7997 19.2C12.7997 20.0487 13.1368 20.8626 13.737 21.4627C14.3371 22.0629 15.151 22.4 15.9997 22.4C16.8484 22.4 17.6623 22.0629 18.2624 21.4627C18.8626 20.8626 19.1997 20.0487 19.1997 19.2Z" fill="#1C52A8"/>
                                </svg>
                                <h6>Internet Re-Imbursement</h6>
                           </div>
                           <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna 1 aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mt-4" style="border: 1px solid #D3D3D3; border-radius: 0;">
                        <div class="card-body perks">
                            <div class="perks-heading">
                                <svg width="32" height="29" viewBox="0 0 32 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.2389 14.8333L15.9 13.6167L11.8889 24.6222H1.77778C0.794444 24.6222 0 25.4167 0 26.4C0 27.3833 0.794444 28.1778 1.77778 28.1778H30.2222C31.2056 28.1778 32 27.3833 32 26.4C32 25.4167 31.2056 24.6222 30.2222 24.6222H15.6722L19.2333 14.8333H19.2389ZM25.9667 14.8222L25.7833 15.3278L29.5444 16.6944C30.55 17.0611 31.6556 16.4611 31.7444 15.3944C32.1056 11.0333 30.4167 6.75556 27.2556 3.81111C27.3667 4.25556 27.4333 4.71667 27.4444 5.18889L27.4556 5.52222C27.5556 8.68889 27.05 11.8444 25.9667 14.8222ZM25.6667 5.23889C25.6056 3.32778 24.4167 1.63889 22.6444 0.938889C22.5944 0.916667 22.5389 0.9 22.4889 0.877778C20.6556 0.227778 18.6111 0.744445 17.3167 2.2L17.0944 2.45C15.1333 4.63889 13.6111 7.18889 12.6 9.95556L12.4167 10.4611L24.1111 14.7167L24.2944 14.2111C25.3 11.4444 25.7722 8.51667 25.6778 5.57222L25.6667 5.23889ZM5.95556 6.00556C5.33889 6.87778 5.8 8.05 6.80556 8.41667L10.75 9.85L10.9333 9.34444C12.0167 6.36667 13.6611 3.62222 15.7722 1.26111L15.9944 1.01111C16.3389 0.627778 16.7222 0.288889 17.1333 0C12.7111 0.138889 8.53889 2.34444 5.95556 6V6.00556Z" fill="#1C52A8"/>
                                </svg>
                                <h6>Paid Vacation / Walk out</h6>
                            </div>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna 1 aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>

                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mt-4" style="border: 1px solid #D3D3D3; border-radius: 0;">
                        <div class="card-body perks">
                            <div class="perks-heading">
                                <svg width="32" height="28" viewBox="0 0 32 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 2C4 0.89375 3.10625 0 2 0C0.89375 0 0 0.89375 0 2V23C0 25.7625 2.2375 28 5 28H30C31.1063 28 32 27.1063 32 26C32 24.8937 31.1063 24 30 24H5C4.45 24 4 23.55 4 23V2ZM29.4125 7.4125C30.1938 6.63125 30.1938 5.3625 29.4125 4.58125C28.6313 3.8 27.3625 3.8 26.5812 4.58125L20 11.1687L16.4125 7.58125C15.6313 6.8 14.3625 6.8 13.5813 7.58125L6.58125 14.5812C5.8 15.3625 5.8 16.6313 6.58125 17.4125C7.3625 18.1938 8.63125 18.1938 9.4125 17.4125L15 11.8313L18.5875 15.4188C19.3687 16.2 20.6375 16.2 21.4188 15.4188L29.4188 7.41875L29.4125 7.4125Z" fill="#333ABF"/>
                                </svg>
                                <h6>Professional Development</h6>
                           </div>
                           <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna 1 aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>

                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mt-4" style="border: 1px solid #D3D3D3; border-radius: 0;">
                        <div class="card-body perks">
                            <div class="perks-heading">
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M29.4188 0.5875C29.6063 0.78125 29.75 1 29.85 1.23125C29.95 1.4625 30 1.71875 30 1.99375V2V8C30 9.10625 29.1063 10 28 10C26.8937 10 26 9.10625 26 8V6.83125L19.4125 13.4125C18.675 14.15 17.4875 14.2 16.6938 13.5188L11 8.63125L5.3 13.5188C4.4625 14.2375 3.2 14.1375 2.48125 13.3C1.7625 12.4625 1.8625 11.2 2.7 10.4812L9.7 4.48125C10.45 3.8375 11.5562 3.8375 12.3062 4.48125L17.9 9.275L23.1688 4H22C20.8937 4 20 3.10625 20 2C20 0.89375 20.8937 0 22 0H28C28.55 0 29.05 0.225 29.4125 0.58125L29.4188 0.5875ZM0 19C0 17.3438 1.34375 16 3 16H29C30.6562 16 32 17.3438 32 19V29C32 30.6562 30.6562 32 29 32H3C1.34375 32 0 30.6562 0 29V19ZM3 26V29H6C6 27.3438 4.65625 26 3 26ZM6 19H3V22C4.65625 22 6 20.6562 6 19ZM29 26C27.3438 26 26 27.3438 26 29H29V26ZM26 19C26 20.6562 27.3438 22 29 22V19H26ZM20 24C20 22.9391 19.5786 21.9217 18.8284 21.1716C18.0783 20.4214 17.0609 20 16 20C14.9391 20 13.9217 20.4214 13.1716 21.1716C12.4214 21.9217 12 22.9391 12 24C12 25.0609 12.4214 26.0783 13.1716 26.8284C13.9217 27.5786 14.9391 28 16 28C17.0609 28 18.0783 27.5786 18.8284 26.8284C19.5786 26.0783 20 25.0609 20 24Z" fill="#1C52A8"/>
                                </svg>
                                <h6>Option to Buy Company Shares</h6>
                           </div>
                           <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna 1 aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <!-- social media -->
     <div class="container py-5 px-xl-5 social-links d-flex justify-content-end">
        <div>
            <h6 class="mb-3">Social Links</h6>
            <div class="d-flex align-items-center" style="gap: 8px;">
                <!-- linkedin -->
                <a href="https://linkedin.com/company/taoaihq">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M26 0H1.99375C0.89375 0 0 0.90625 0 2.01875V25.9813C0 27.0938 0.89375 28 1.99375 28H26C27.1 28 28 27.0938 28 25.9813V2.01875C28 0.90625 27.1 0 26 0ZM8.4625 24H4.3125V10.6375H8.46875V24H8.4625ZM6.3875 8.8125C5.05625 8.8125 3.98125 7.73125 3.98125 6.40625C3.98125 5.08125 5.05625 4 6.3875 4C7.7125 4 8.79375 5.08125 8.79375 6.40625C8.79375 7.7375 7.71875 8.8125 6.3875 8.8125ZM24.0187 24H19.8687V17.5C19.8687 15.95 19.8375 13.9563 17.7125 13.9563C15.55 13.9563 15.2188 15.6438 15.2188 17.3875V24H11.0688V10.6375H15.05V12.4625H15.1062C15.6625 11.4125 17.0188 10.3062 19.0375 10.3062C23.2375 10.3062 24.0187 13.075 24.0187 16.675V24Z" fill="#1C52A8"/>
                    </svg>
                </a>
                <!-- X -->
                <a href="https://x.com/taoaihq">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 0C1.79375 0 0 1.79375 0 4V24C0 26.2062 1.79375 28 4 28H24C26.2062 28 28 26.2062 28 24V4C28 1.79375 26.2062 0 24 0H4ZM22.5688 5.25L16.0812 12.6625L23.7125 22.75H17.7375L13.0625 16.6313L7.70625 22.75H4.7375L11.675 14.8188L4.35625 5.25H10.4812L14.7125 10.8438L19.6 5.25H22.5688ZM20.2062 20.975L9.5875 6.93125H7.81875L18.5562 20.975H20.2H20.2062Z" fill="black"/>
                    </svg>
                </a>
                <!-- facebook -->
                <a href="https://www.facebook.com/taoaihq">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 0C1.79375 0 0 1.79375 0 4V24C0 26.2062 1.79375 28 4 28H10.1375V18.8875H6.8375V14H10.1375V11.8938C10.1375 6.45 12.6 3.925 17.95 3.925C18.9625 3.925 20.7125 4.125 21.4312 4.325V8.75C21.0562 8.7125 20.4 8.6875 19.5812 8.6875C16.9562 8.6875 15.9438 9.68125 15.9438 12.2625V14H21.1688L20.2687 18.8875H15.9375V28H24C26.2062 28 28 26.2062 28 24V4C28 1.79375 26.2062 0 24 0H4Z" fill="#1C52A8"/>
                    </svg>
                </a>
                <!-- instagram -->
                <a href="#">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.15 11.2312C11.786 11.4746 11.4735 11.7873 11.2303 12.1514C10.9871 12.5155 10.8181 12.924 10.7328 13.3535C10.5605 14.2208 10.7398 15.1211 11.2312 15.8563C11.7227 16.5914 12.4861 17.1012 13.3535 17.2735C14.2208 17.4458 15.1211 17.2665 15.8562 16.775C16.5914 16.2835 17.1012 15.5201 17.2735 14.6528C17.4458 13.7854 17.2665 12.8851 16.775 12.15C16.2835 11.4149 15.5201 10.9051 14.6528 10.7328C13.7854 10.5605 12.8851 10.7398 12.15 11.2312ZM21.0438 6.95625C20.7188 6.63125 20.325 6.375 19.8937 6.20625C18.7625 5.7625 16.2938 5.78125 14.7 5.8C14.4437 5.8 14.2063 5.80625 14 5.80625C13.7937 5.80625 13.55 5.80625 13.2875 5.8C11.6938 5.78125 9.2375 5.75625 8.10625 6.20625C7.675 6.375 7.2875 6.63125 6.95625 6.95625C6.625 7.28125 6.375 7.675 6.20625 8.10625C5.7625 9.2375 5.7875 11.7125 5.8 13.3062C5.8 13.5625 5.80625 13.8 5.80625 14C5.80625 14.2 5.80625 14.4375 5.8 14.6938C5.7875 16.2875 5.7625 18.7625 6.20625 19.8937C6.375 20.325 6.63125 20.7125 6.95625 21.0438C7.28125 21.375 7.675 21.625 8.10625 21.7938C9.2375 22.2375 11.7063 22.2188 13.3 22.2C13.5563 22.2 13.7937 22.1938 14 22.1938C14.2063 22.1938 14.45 22.1938 14.7125 22.2C16.3062 22.2188 18.7625 22.2438 19.8937 21.7938C20.325 21.625 20.7125 21.3688 21.0438 21.0438C21.375 20.7188 21.625 20.325 21.7938 19.8937C22.2438 18.7687 22.2188 16.3062 22.2 14.7062C22.2 14.4437 22.1938 14.2 22.1938 13.9937C22.1938 13.7875 22.1938 13.55 22.2 13.2812C22.2188 11.6875 22.2438 9.225 21.7938 8.09375C21.625 7.6625 21.3688 7.275 21.0438 6.94375V6.95625ZM16.85 9.7375C17.9805 10.4934 18.7644 11.6674 19.0293 13.0012C19.2942 14.3351 19.0184 15.7195 18.2625 16.85C17.8882 17.4098 17.4074 17.8903 16.8474 18.2642C16.2874 18.6382 15.6592 18.8981 14.9988 19.0293C13.6649 19.2942 12.2805 19.0184 11.15 18.2625C10.0195 17.5075 9.23527 16.3343 8.96979 15.001C8.70431 13.6677 8.97934 12.2836 9.73438 11.1531C10.4894 10.0226 11.6626 9.23839 12.9959 8.97291C14.3291 8.70743 15.7133 8.98246 16.8438 9.7375H16.85ZM18.675 9.65625C18.4812 9.525 18.325 9.3375 18.2313 9.11875C18.1375 8.9 18.1187 8.6625 18.1625 8.425C18.2063 8.1875 18.325 7.98125 18.4875 7.8125C18.65 7.64375 18.8687 7.53125 19.1 7.4875C19.3313 7.44375 19.575 7.4625 19.7938 7.55625C20.0125 7.65 20.2 7.8 20.3312 7.99375C20.4625 8.1875 20.5312 8.41875 20.5312 8.65625C20.5312 8.8125 20.5 8.96875 20.4438 9.1125C20.3875 9.25625 20.2938 9.3875 20.1875 9.5C20.0812 9.6125 19.9437 9.7 19.8 9.7625C19.6562 9.825 19.5 9.85625 19.3438 9.85625C19.1063 9.85625 18.875 9.7875 18.6812 9.65625H18.675ZM28 4C28 1.79375 26.2062 0 24 0H4C1.79375 0 0 1.79375 0 4V24C0 26.2062 1.79375 28 4 28H24C26.2062 28 28 26.2062 28 24V4ZM22.3125 22.3125C21.1437 23.4813 19.725 23.85 18.125 23.9312C16.475 24.025 11.525 24.025 9.875 23.9312C8.275 23.85 6.85625 23.4813 5.6875 22.3125C4.51875 21.1437 4.15 19.725 4.075 18.125C3.98125 16.475 3.98125 11.525 4.075 9.875C4.15625 8.275 4.51875 6.85625 5.6875 5.6875C6.85625 4.51875 8.28125 4.15 9.875 4.075C11.525 3.98125 16.475 3.98125 18.125 4.075C19.725 4.15625 21.1437 4.51875 22.3125 5.6875C23.4813 6.85625 23.85 8.275 23.925 9.875C24.0187 11.5188 24.0187 16.4625 23.925 18.1187C23.8438 19.7188 23.4813 21.1375 22.3125 22.3062V22.3125Z" fill="url(#paint0_linear_2368_1242)"/>
                    <defs>
                    <linearGradient id="paint0_linear_2368_1242" x1="14" y1="0" x2="14" y2="28" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FF6600"/>
                    <stop offset="1" stop-color="#F72222"/>
                    </linearGradient>
                    </defs>
                    </svg>
                </a>
            </div>
        </div>
     </div>
    
</div>

<?php



taoh_get_footer();