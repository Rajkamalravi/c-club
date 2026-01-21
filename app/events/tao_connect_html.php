<?php taoh_get_header(); ?>

<style>
    .connect {
        width: 100%;
        max-width: 1600px;
        margin: 0 auto;
    }
    .connect li a.view-all-link {
        font-size: 12px;
        font-weight: 400;
        color: #2557A4;
        line-height: 1px;
        min-height: 25px;
        text-decoration: underline;
    }
    .connect li a.sub-link {
        font-size: 15px;
        font-weight: 400;
        color: #212121;
        line-height: 1;
        min-height: 25px;
        display: flex;
        align-items: center;
        /* border: 0.5px solid transparent; */
    }
    .connect li a.sub-link:hover {
        background-color: #2557A7;
        color: #ffffff;
        /* border-color:  #d3d3d3; */
    }
    .connect li a.sub-link.active {
        background-color: #2557A7;
        color: #ffffff;
    }
    .connect .p-left-title {
        padding-left: 45px;
    }
    .connect .p-left-content {
        padding-left: 49px;
    }
    .connect .c-title-bg {
        background-color: #CBDFFF;
        min-height: 43px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .connect .c-title { 
        font-size: 17px;
        font-weight: 400;
        line-height: 20px;
        color: #212121;
    }
    aside#connectsidebar {
        width: 100%;
        max-width: 244px;
        border-right: 1px solid #d3d3d3;
    
    }
    .connect .bor-btn {
        border: 1px solid #212121;
        font-size: 12px;
        line-height: 1.046;
        color: #212121;
        border-radius: 12px;
        padding: 4px 12px;
        min-width: 58px;
    }
    .connect .c-pro-img {
        width: 20px;
        min-width: 20px;
        height: 20px;
        min-height: 20px;
        border-radius: 100%;
        border: 0.5px solid #d3d3d3;
        object-fit: cover;
    }
</style>

<div class="connect">
    <aside id="connectsidebar">
        <ul>
            <li class="">
                <div class="c-title-bg pr-4 p-left-title" style="gap: 32px;">
                    <h6 class="c-title">Members</h6>
                    <a href="#" class="btn bor-btn">Invite</a>
                </div>
                <ul>
                    <li>
                        <a href="#" class="pr-3 p-left-content sub-link">All Members</a>
                    </li>
                    <li>
                        <a href="#" class="pr-3 p-left-content sub-link active">My Network</a>
                    </li>
                </ul>
            </li>
            <li class="">
                <div class="c-title-bg px-4" style="gap: 30px;">
                    <h6 class="c-title d-flex align-items-center" style="gap: 12px;">
                        <svg width="19" height="14" viewBox="0 0 19 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3.16667 1.5C3.16667 0.671875 3.87587 0 4.75 0H14.25C15.1241 0 15.8333 0.671875 15.8333 1.5V11H3.16667V1.5ZM13.4913 2.96875C13.1813 2.675 12.6799 2.675 12.3731 2.96875L8.71163 6.4375L7.16128 4.96875C6.85122 4.675 6.34983 4.675 6.04306 4.96875C5.73628 5.2625 5.73299 5.7375 6.04306 6.02812L8.15417 8.02812C8.46424 8.32187 8.96563 8.32187 9.2724 8.02812L13.4913 4.03125C13.8014 3.7375 13.8014 3.2625 13.4913 2.97187V2.96875ZM0 9.5C0 8.67188 0.709201 8 1.58333 8H2.11111V12H16.8889V8H17.4167C18.2908 8 19 8.67188 19 9.5V12.5C19 13.3281 18.2908 14 17.4167 14H1.58333C0.709201 14 0 13.3281 0 12.5V9.5Z" fill="#212121"/>
                        </svg>
                        <span>Rooms</span>
                    </h6>
                    <a href="#" class="btn bor-btn">Create</a>
                </div>
                <ul>
                    <li>
                        <a href="#" class="pr-3 p-left-content sub-link">Friday Job Fair</a>
                    </li>
                    <li>
                        <a href="#" class="pr-3 p-left-content sub-link">Neo Capital Hiring</a>
                    </li>
                    <li>
                        <a href="#" class="pr-3 p-left-content sub-link">Technology & IT</a>
                    </li>
                    <li>
                        <a href="#" class="pr-3 p-left-content sub-link">Health Care Professionals</a>
                    </li>
                    <li>
                        <a href="#" class="pr-1 p-left-content sub-link">Construction Professionals</a>
                    </li>
                    <li>
                        <a href="#" class="pr-3 p-left-content sub-link">Energy & Utilities</a>
                    </li>
                </ul>
            </li>
            <li class="">
                <div class="c-title-bg px-4">
                    <h6 class="c-title d-flex align-items-center" style="gap: 12px;">
                        <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.50056 11C10.0913 11 13.0008 8.5375 13.0008 5.5C13.0008 2.4625 10.0913 0 6.50056 0C2.90982 0 0.000342025 2.4625 0.000342025 5.5C0.000342025 6.70625 0.459733 7.82187 1.23788 8.73125C1.12851 9.025 0.966 9.28438 0.794119 9.50313C0.644114 9.69688 0.490984 9.84688 0.37848 9.95C0.322228 10 0.275351 10.0406 0.2441 10.0656C0.228475 10.0781 0.215974 10.0875 0.209724 10.0906L0.203474 10.0969C0.0315931 10.225 -0.0434095 10.45 0.0253429 10.6531C0.0940952 10.8562 0.284727 11 0.500359 11C1.18163 11 1.86916 10.825 2.44105 10.6094C2.72856 10.5 2.99732 10.3781 3.2317 10.2531C4.19111 10.7281 5.30677 11 6.50056 11ZM14.0008 5.5C14.0008 9.00937 10.9038 11.6531 7.23496 11.9688C7.99436 14.2937 10.5132 16 13.5008 16C14.6946 16 15.8103 15.7281 16.7728 15.2531C17.0072 15.3781 17.2728 15.5 17.5603 15.6094C18.1322 15.825 18.8197 16 19.501 16C19.7166 16 19.9104 15.8594 19.976 15.6531C20.0416 15.4469 19.9698 15.2219 19.7948 15.0938L19.7885 15.0875C19.7823 15.0812 19.7698 15.075 19.7541 15.0625C19.7229 15.0375 19.676 15 19.6198 14.9469C19.5073 14.8438 19.3541 14.6938 19.2041 14.5C19.0322 14.2812 18.8697 14.0187 18.7604 13.7281C19.5385 12.8219 19.9979 11.7063 19.9979 10.4969C19.9979 7.59687 17.3447 5.21875 13.9789 5.0125C13.9914 5.17188 13.9977 5.33437 13.9977 5.49687L14.0008 5.5Z" fill="#212121"/>
                        </svg>
                        <span>Direct Messages</span>
                    </h6>
                </div>
                <ul>
                    <li>
                        <a href="#" class="pl-4 pr-3 sub-link d-flex align-items-center" style="gap: 10px;">
                            <img class="c-pro-img" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/profile_room_1.png" alt="">
                            <span>Liam</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="pl-4 pr-3 sub-link d-flex align-items-center" style="gap: 10px;">
                            <img class="c-pro-img" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/profile_room_2.png" alt="">
                            <span>James Theodre</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="pl-4 pr-3 sub-link d-flex align-items-center" style="gap: 10px;">
                            <img class="c-pro-img" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/profile_room_3.png" alt="">
                            <span>Lucifer</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="pl-4 pr-3 sub-link d-flex align-items-center" style="gap: 10px;">
                            <img class="c-pro-img" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/profile_room_4.png" alt="">
                            <span>Oliver</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="pl-4 pr-3 sub-link d-flex align-items-center" style="gap: 10px;">
                            <img class="c-pro-img" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/profile_room_5.png" alt="">
                            <span>Rafel Rachel</span>
                        </a>
                    </li>

                    <!-- View All DM’s -->
                    <li>
                        <a href="#" class="pl-4 pr-3 view-all-link " style="gap: 10px;">
                            View All DM’s
                        </a>
                    </li>
                </ul>
            </li>
            <li class="mt-3">
                <div class="c-title-bg px-4">
                    <a href="#" class="c-title d-flex align-items-center" style="gap: 12px;">
                        <svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.9584 5.20727C15.0579 5.4792 14.974 5.78238 14.7595 5.97617L13.4136 7.20766C13.4478 7.46708 13.4664 7.73276 13.4664 8.00156C13.4664 8.27037 13.4478 8.53604 13.4136 8.79547L14.7595 10.027C14.974 10.2207 15.0579 10.5239 14.9584 10.7959C14.8217 11.1678 14.6569 11.5241 14.4673 11.8679L14.3212 12.1211C14.1161 12.4649 13.886 12.79 13.6343 13.0963C13.4509 13.3214 13.1463 13.3964 12.8727 13.3088L11.1414 12.7556C10.7249 13.0776 10.2649 13.3464 9.77374 13.5495L9.3852 15.3342C9.32303 15.6187 9.10545 15.8437 8.81948 15.8906C8.39053 15.9625 7.94915 16 7.49845 16C7.04774 16 6.60636 15.9625 6.17741 15.8906C5.89144 15.8437 5.67386 15.6187 5.61169 15.3342L5.22315 13.5495C4.73204 13.3464 4.27201 13.0776 3.85549 12.7556L2.12727 13.312C1.85373 13.3995 1.54912 13.3214 1.36573 13.0994C1.11395 12.7931 0.883937 12.4681 0.678788 12.1242L0.532697 11.8711C0.343089 11.5273 0.178348 11.1709 0.0415818 10.799C-0.0578845 10.5271 0.0260402 10.2239 0.240514 10.0301L1.58642 8.79859C1.55223 8.53604 1.53358 8.27037 1.53358 8.00156C1.53358 7.73276 1.55223 7.46708 1.58642 7.20766L0.240514 5.97617C0.0260402 5.78238 -0.0578845 5.4792 0.0415818 5.20727C0.178348 4.83532 0.343089 4.479 0.532697 4.13518L0.678788 3.88201C0.883937 3.53819 1.11395 3.21313 1.36573 2.90682C1.54912 2.68177 1.85373 2.60676 2.12727 2.69428L3.8586 3.24751C4.27512 2.92557 4.73515 2.65677 5.22626 2.4536L5.6148 0.668881C5.67697 0.38445 5.89455 0.159406 6.18052 0.112522C6.60947 0.0375073 7.05085 0 7.50155 0C7.95226 0 8.39364 0.0375073 8.82259 0.109396C9.10856 0.156281 9.32614 0.381324 9.38831 0.665755L9.77685 2.45048C10.268 2.65364 10.728 2.92245 11.1445 3.24438L12.8758 2.69115C13.1494 2.60363 13.454 2.68177 13.6374 2.90369C13.8892 3.21 14.1192 3.53507 14.3243 3.87888L14.4704 4.13206C14.66 4.47587 14.8248 4.83219 14.9615 5.20414L14.9584 5.20727ZM7.50155 10.5021C8.16106 10.5021 8.79355 10.2386 9.25989 9.76968C9.72623 9.30074 9.98821 8.66473 9.98821 8.00156C9.98821 7.33839 9.72623 6.70238 9.25989 6.23345C8.79355 5.76452 8.16106 5.50107 7.50155 5.50107C6.84205 5.50107 6.20956 5.76452 5.74322 6.23345C5.27688 6.70238 5.0149 7.33839 5.0149 8.00156C5.0149 8.66473 5.27688 9.30074 5.74322 9.76968C6.20956 10.2386 6.84205 10.5021 7.50155 10.5021Z" fill="black"/>
                        </svg>
                        <span>Settings & Status</span>
                    </a>
                </div>
            </li>
            <li class="">
                <div class="c-title-bg px-4" style="background-color: transparent;">
                    <a href="#" class="c-title d-flex align-items-center" style="gap: 12px;">
                        <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.5 17C10.7543 17 12.9163 16.1045 14.5104 14.5104C16.1045 12.9163 17 10.7543 17 8.5C17 6.24566 16.1045 4.08365 14.5104 2.48959C12.9163 0.895533 10.7543 0 8.5 0C6.24566 0 4.08365 0.895533 2.48959 2.48959C0.895533 4.08365 0 6.24566 0 8.5C0 10.7543 0.895533 12.9163 2.48959 14.5104C4.08365 16.1045 6.24566 17 8.5 17ZM5.44863 10.8076C6.04297 11.4949 7.05898 12.2188 8.5 12.2188C9.94102 12.2188 10.957 11.4949 11.5514 10.8076C11.7439 10.5852 12.0793 10.5619 12.3018 10.7545C12.5242 10.9471 12.5475 11.2824 12.3549 11.5049C11.6145 12.3549 10.3295 13.2812 8.5 13.2812C6.67051 13.2812 5.38555 12.3549 4.64512 11.5049C4.45254 11.2824 4.47578 10.9471 4.69824 10.7545C4.9207 10.5619 5.25605 10.5852 5.44863 10.8076ZM4.79453 6.90625C4.79453 6.62446 4.90647 6.35421 5.10573 6.15495C5.30499 5.95569 5.57524 5.84375 5.85703 5.84375C6.13882 5.84375 6.40907 5.95569 6.60833 6.15495C6.80759 6.35421 6.91953 6.62446 6.91953 6.90625C6.91953 7.18804 6.80759 7.45829 6.60833 7.65755C6.40907 7.85681 6.13882 7.96875 5.85703 7.96875C5.57524 7.96875 5.30499 7.85681 5.10573 7.65755C4.90647 7.45829 4.79453 7.18804 4.79453 6.90625ZM11.1695 5.84375C11.4513 5.84375 11.7216 5.95569 11.9208 6.15495C12.1201 6.35421 12.232 6.62446 12.232 6.90625C12.232 7.18804 12.1201 7.45829 11.9208 7.65755C11.7216 7.85681 11.4513 7.96875 11.1695 7.96875C10.8877 7.96875 10.6175 7.85681 10.4182 7.65755C10.219 7.45829 10.107 7.18804 10.107 6.90625C10.107 6.62446 10.219 6.35421 10.4182 6.15495C10.6175 5.95569 10.8877 5.84375 11.1695 5.84375Z" fill="url(#paint0_linear_7436_1252)"/>
                            <defs>
                            <linearGradient id="paint0_linear_7436_1252" x1="8.5" y1="0" x2="8.5" y2="17" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#FFD200"/>
                            <stop offset="1" stop-color="#E5D37F"/>
                            </linearGradient>
                            </defs>
                        </svg>
                        <span>What’s on your mind?</span>
                    </a>
                </div>
            </li>
        </ul>
    </aside>
</div>

<?php taoh_get_footer(); ?>