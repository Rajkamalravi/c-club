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
?>

<style>

span.h5 {
  font-size: 13px !important;
}
</style>
<!--======================================
        START HERO AREA
======================================-->
<section class="hero-area pt-80px pb-80px hero-bg-jobs">
    <div class="overlay"></div>
    <span class="stroke-shape stroke-shape-1"></span>
    <span class="stroke-shape stroke-shape-2"></span>
    <span class="stroke-shape stroke-shape-3"></span>
    <span class="stroke-shape stroke-shape-4"></span>
    <span class="stroke-shape stroke-shape-5"></span>
    <span class="stroke-shape stroke-shape-6"></span>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h4 class="pb-1 text-white">PROFESSIONAL</h4>
                    <h2 class="section-title pb-3 text-white">Is your next career milestone calling?</h2>
                    <p class="section-desc text-white">Explore a curated selection of job openings, internships, and freelance positions.</p>
                    <div class="hero-btn-box py-4 dark-btn">
                        <!-- <a href="<?php echo TAOH_LOGIN_URL; ?>" class="btn theme-btn theme-btn mr-2">Sign up today</a> -->
                        <a onclick="localStorage.removeItem('isCodeSent')" href="javascript:void(0);"
                         class="btn theme-btn theme-btn mr-2 login-button " aria-pressed="true" data-toggle="modal" data-target="#config-modal">Sign up today</a>
                    </div>
                </div><!-- end hero-content -->
            </div><!-- end col-lg-9 -->
            <div class="col-lg-6 text-right">
            <div class="hero-content">
                    <h4 class="pb-1 text-white">EMPLOYER</h4>
                    <h2 class="section-title pb-3 text-white">In search of exceptional talent?</h2>
                    <p class="section-desc text-white">Partner with us to connect with professionals who can propel your organization forward.</p>
                    <div class="hero-btn-box py-4 light-btn">
                        <!-- <a href="<?php echo TAOH_LOGIN_URL; ?>" class="btn theme-btn theme-btn mr-2">Find your next Hire</a> -->
                        <a onclick="localStorage.removeItem('isCodeSent')" href="javascript:void(0);"
                         class="btn theme-btn theme-btn mr-2 login-button " aria-pressed="true" data-toggle="modal" data-target="#config-modal">Find your next Hire</a>
                    </div>
                </div><!-- end hero-content -->
            </div>
        </div><!-- end row -->
    </div><!-- end container -->
</section>
<!--======================================
        END HERO AREA
======================================-->
<!-- ================================
         START FUNFACT AREA
================================= -->
<section class="funfact-area funfact-section-area">
    <div class="container">
        <div class="counter-box bg-white shadow-md rounded-rounded px-4">
            <div class="row">
                <div class="col responsive-column-half border-right border-right-gray">
                    <div class="media media-card text-center px-0 py-4 shadow-none rounded-0 bg-transparent counter-item mb-0">
                        <div class="media-body">
                            <h5 class="fw-semi-bold pb-2">5+ Million</h5>
                            <p class="lh-20">Visitors on our network</p>
                        </div>
                    </div>
                </div><!-- end col -->
                <div class="col responsive-column-half border-right border-right-gray">
                    <div class="media media-card text-center px-0 py-4 shadow-none rounded-0 bg-transparent counter-item mb-0">
                        <div class="media-body">
                            <h5 class="fw-semi-bold pb-2">2+ Million</h5>
                            <p class="lh-20">Career events attended</p>
                        </div>
                    </div>
                </div><!-- end col -->
                <div class="col responsive-column-half border-right border-right-gray">
                    <div class="media media-card text-center px-0 py-4 shadow-none rounded-0 bg-transparent counter-item mb-0">
                        <div class="media-body">
                            <h5 class="fw-semi-bold pb-2">1+ Million</h5>
                            <p class="lh-20">Connections created</p>
                        </div>
                    </div>
                </div><!-- end col -->
                <div class="col responsive-column-half border-right border-right-gray">
                    <div class="media media-card text-center px-0 py-4 shadow-none rounded-0 bg-transparent counter-item mb-0">
                        <div class="media-body">
                            <h5 class="fw-semi-bold pb-2">10,000+</h5>
                            <p class="lh-20">Recruiters on platform</p>
                        </div>
                    </div>
                </div><!-- end col -->
                <div class="col responsive-column-half">
                    <div class="media media-card text-center px-0 py-4 shadow-none rounded-0 bg-transparent counter-item mb-0">
                        <div class="media-body">
                            <h5 class="fw-semi-bold pb-2">150+</h5>
                            <p class="lh-20">Communities served</p>
                        </div>
                    </div>
                </div><!-- end col -->
            </div><!-- end row -->
        </div><!-- end counter-box -->
    </div><!-- end container -->
    <div class="container abt-job-outer">
        <div class="row">
            <div class="col-lg-7">
                <div class="right-padd about-job">
                    <!--<h3 style="color:#0d233e;">Your new job starts here.</h3>-->
                    <h2 class="">Your Career Breakthrough Awaits</h2><br/>
                    <p>At Jobs app, we're revolutionizing the job search. It's not just about applying for a position; it's about engaging in dialogue that matters. We bridge the gap between job seekers and employers for a match that's beyond just qualifications—it's about the right fit.</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="how-works">
                    <h3>How Does Jobs app Work?</h3>
                    <ul>
                        <li>Discover Opportunities</li>
                        <li>Engage with Recruiters</li>
                        <li>Apply with Confidence</li>
                    </ul>
                </div>
            </div>
      </div>
    </div>
</section>
<!-- ================================
         END FUNFACT AREA
================================= -->
<!-- ================================
         START BLUE SECTION AREA
================================= -->
<section class="blue-bg">
  <div class="container">
    <div class="row">
      <div class="col-lg-4">
          <h3>Valued <br>Connections</h3>
          <p>Stand out by building meaningful relationships with recruiters who are seeking proactive candidates.</p>
      </div>
      <div class="col-lg-4">
          <h3>Exclusive <br>Insights</h3>
          <p>Gain insider knowledge about company cultures, team dynamics, connect with employers, and gain potential for growth.</p>
      </div>
      <div class="col-lg-4">
          <h3>Informed <br>Decisions</h3>
          <p>Apply to jobs that are more than just a paycheck—find the ones that align with your career trajectory.</p>
      </div>
    </div>
  </div>
</section>
<!-- ================================
         END BLUE SECTION AREA
================================= -->
<!-- ================================
         END AVAILABLE JOB AREA
================================= -->
<section class="available-jobs">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 light-bg-outer">
                <div class="text-center">
                    <h2 class="section-title">Browse our Available Jobs</h2>
                </div>
                <div class="jobs-list pt-60px"></div>
                <div id='loaderArea'></div> 
                <div id="pagination" class="dark-blue"></div>
                <div class="job-signup dark-btn">
                    <!-- <a href="<?php echo TAOH_LOGIN_URL."?redirect_url=".TAOH_REDIRECT_URL;?>" class="btn theme-btn theme-btn mr-2">Sign up to apply <i class="la la-arrow-right ml-1"></i></a> -->
                    <a onclick="localStorage.removeItem('isCodeSent')" href="javascript:void(0);"
                         class="btn theme-btn theme-btn mr-2 login-button " aria-pressed="true" data-toggle="modal" data-target="#config-modal">Sign up to apply <i class="la la-arrow-right ml-1"></i></a>
                    
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ================================
         END AVAILABLE JOB AREA
================================= -->
<!-- ================================
         START JOBS APPLY AREA
================================= -->
<section class="get-started-area section--padding pattern-bg jobs-apply-section">
    <div class="container">
    <div class="row align-items-center">
            <div class="col-lg-6 dark-blue">
                <div class="info-box text-center">
                    <div class="info-body">
                        <h5 class="pb-4">Apply With Us</h5>
                        <p class="pb-4">Save time by focusing on positions <br>that truly match your aspirations</p>
                       <!-- <a href="<?php echo TAOH_LOGIN_URL; ?>" class="btn theme-btn">Apply now <i class="la la-arrow-right icon ml-1"></i></a> -->
                       <a onclick="localStorage.removeItem('isCodeSent')" href="javascript:void(0);"
                         class="btn theme-btn theme-btn mr-2 login-button " aria-pressed="true" data-toggle="modal" data-target="#config-modal">Apply now <i class="la la-arrow-right icon ml-1"></i></a>
                    </div>
                </div>
            </div><!-- end col-lg-5 -->
            <div class="col-lg-6 light-blue">
                <div class="info-box text-center">
                    <div class="info-body">
                        <h5 class="pb-4">Post a Job</h5>
                        <p class="pb-4">Fill out an application so we <br>can look for your next job</p>
                        <!-- <a href="<?php echo TAOH_LOGIN_URL; ?>" class="btn theme-btn">Post now <i class="la la-arrow-right icon ml-1"></i></a> -->
                        <a onclick="localStorage.removeItem('isCodeSent')" href="javascript:void(0);"
                         class="btn theme-btn theme-btn mr-2 login-button " aria-pressed="true" data-toggle="modal" data-target="#config-modal">Post now <i class="la la-arrow-right icon ml-1"></i></a>
                    </div>
                </div>
            </div><!-- end col-lg-5 -->
        </div>
    </div>
</section>
<!-- ================================
         END JOBS APPLY AREA
================================= -->
<!-- ================================
         START TESTIMONIAL AREA
================================= -->
<section class="testimonial-area section--padding">
    <div class="container">
        <div class="testimonial-carousel owl-carousel owl-theme owl-action-styled owl-loaded owl-drag">
            <!-- end carousel-card -->
            <!-- end carousel-card -->
        <div class="owl-stage-outer">
            <div class="owl-stage" style="transform: translate3d(-2530px, 0px, 0px); transition: all 0s ease 0s; width: 7590px;">
            <div class="owl-item cloned" style="width: 1250px; margin-right: 15px;">
                <div class="carousel-card text-center">
                    <h2>What our friends from recruiting say</h2>
                    <p class="section-desc w-75 mx-auto">Found the quality of candidates to be higher and the platform provides access to a newer channel to find talent. Shortened our hiring process, my team is really happy.</p>
                    <div class="divider bg-transparent my-4"><span class="mx-auto"></span></div>
                    <h3 class="pb-1 fs-17">Talent Manager</h3>
                    <span>Fedex</span>
                </div>
            </div>
            <div class="owl-item cloned" style="width: 1250px; margin-right: 15px;">
                <div class="carousel-card text-center">
                    <h2>What our friends from recruiting say</h2>
                    <p class="section-desc w-75 mx-auto">Thanks for letting us post jobs. Got a few leads that may be converting soon. Really useful and happy to see what you guys offered. I would be using you again.</p>
                    <div class="divider bg-transparent my-4"><span class="mx-auto"></span></div>
                    <h3 class="pb-1 fs-17">Logistics Manager</h3>
                    <span>Amazon</span>
            </div></div>
            <div class="owl-item active" style="width: 1250px; margin-right: 15px;">
                <div class="carousel-card text-center">
                    <h2>What our friends from recruiting say</h2>
                    <p class="section-desc w-75 mx-auto">Thank you for your jobfair event. It was timely and I was able to connect with strong candidates. I would recommend to my other teammates for a broader partnership. Keep up the good work.</p>
                    <div class="divider bg-transparent my-4"><span class="mx-auto"></span></div>
                    <h3 class="pb-1 fs-17">General Mechandise Lead</h3>
                    <span>Walmart</span>
                </div>
            </div>
            <div class="owl-item" style="width: 1250px; margin-right: 15px;">
                <div class="carousel-card text-center">
                    <h2>What our friends from recruiting say</h2>
                    <p class="section-desc w-75 mx-auto">Thanks for helping recruit some new team members. I would be using you again for future hiring!</p>
                    <div class="divider bg-transparent my-4"><span class="mx-auto"></span></div>
                    <h3 class="pb-1 fs-17">Transportation Manager</h3>
                    <span>Staples</span>
                </div>
            </div>
            <div class="owl-item cloned" style="width: 1250px; margin-right: 15px;">
                <div class="carousel-card text-center">
                    <h2>What our friends from recruiting say</h2>
                    <p class="section-desc w-75 mx-auto">Got their support to hire some associates. Would work with them again. I would love to see how technology could be used to improve the outcome.</p>
                    <div class="divider bg-transparent my-4"><span class="mx-auto"></span></div>
                    <h3 class="pb-1 fs-17">Store Manager</h3>
                    <span>Apple</span>
                </div>
            </div>
            <div class="owl-item cloned" style="width: 1250px; margin-right: 15px;">
                <div class="carousel-card text-center">
                    <h2>What our friends from recruiting say</h2>
                    <p class="section-desc w-75 mx-auto">I have attended their events, and I am impressed with their community and use of technology to help with career development. Will participate again..</p>
                    <div class="divider bg-transparent my-4"><span class="mx-auto"></span></div>
                    <h3 class="pb-1 fs-17">Store Supervisor</h3>
                    <span>UPS</span>
                </div>
            </div>
        </div>
    </div>
    <div class="owl-nav">
        <button type="button" role="presentation" class="owl-prev">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12l4.58-4.59z"></path></svg></button><button type="button" role="presentation" class="owl-next"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"></path></svg>
        </button>
    </div>
    <div class="owl-dots disabled"></div>
</div><!-- end owl-carousel -->
</div><!-- end container -->
</section>
<!-- ================================
         END TESTIMONIAL AREA
================================= -->

<section class="blog-area pt-80px pb-80px blog-article-section events-blog">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title pb-4">Lead with insightful ways to get your DREAM job</h2>
        </div>
        <div class="col-lg-12">
            <div class="row articles_list">
            
            </div>
        </div><!-- end row -->
    </div><!-- end container -->
</section>
<section class="get-started-area section--padding" id="for-businesses" hidden>
    <div class="container">
        <div class="text-center">
            <h2 class="section-title pb-3">Join Our Movement: <br>Connect, Collaborate, and Catalyze Change</h2>
        </div>
        <div class="row pt-50px">
            <div class="col-lg-4 responsive-column-half">
                <div class="media media-card align-items-center hover-s">
                    <div class="icon-element mr-3">
                    <svg width="84" height="84" viewBox="0 0 84 84" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="42" cy="42" r="42" fill="#00A3FF"/>
                    <g clip-path="url(#clip0_103_143)">
                    <path d="M42.5999 16.7998C39.7501 16.7998 37.4399 19.11 37.4399 21.9598C37.4399 24.8096 39.7501 27.1198 42.5999 27.1198C45.4486 27.117 47.7571 24.8084 47.7599 21.9598C47.7599 19.11 45.4498 16.7998 42.5999 16.7998ZM42.5999 25.3998C40.7001 25.3998 39.1599 23.8597 39.1599 21.9598C39.1599 20.06 40.7001 18.5198 42.5999 18.5198C44.4998 18.5198 46.0399 20.06 46.0399 21.9598C46.0399 23.8597 44.4998 25.3998 42.5999 25.3998Z" fill="white"/>
                    <path d="M46.0399 27.9797H45.8369L45.6554 28.07C43.7311 29.0282 41.4686 29.0282 39.5443 28.07L39.3628 27.9797H39.1599C36.7863 27.9825 34.8627 29.9061 34.8599 32.2797V37.4397C34.8599 38.8646 36.015 40.0197 37.4399 40.0197H47.7599C49.1848 40.0197 50.3399 38.8646 50.3399 37.4397V32.2797C50.3371 29.9061 48.4135 27.9825 46.0399 27.9797ZM48.6199 37.4397C48.6199 37.9147 48.2348 38.2997 47.7599 38.2997H37.4399C36.9649 38.2997 36.5799 37.9147 36.5799 37.4397V32.2797C36.5801 30.9261 37.6264 29.8027 38.9767 29.7066C41.2745 30.773 43.9253 30.773 46.223 29.7066C47.5732 29.8027 48.6196 30.9261 48.6199 32.2797V37.4397Z" fill="white"/>
                    <path d="M60.66 45.1797C57.8102 45.1797 55.5 47.4899 55.5 50.3397C55.5 53.1895 57.8102 55.4997 60.66 55.4997C63.5098 55.4997 65.82 53.1895 65.82 50.3397C65.8172 47.491 63.5086 45.1825 60.66 45.1797ZM60.66 53.7797C58.7602 53.7797 57.22 52.2395 57.22 50.3397C57.22 48.4398 58.7602 46.8997 60.66 46.8997C62.5598 46.8997 64.1 48.4398 64.1 50.3397C64.1 52.2395 62.5598 53.7797 60.66 53.7797Z" fill="white"/>
                    <path d="M64.0999 56.3599H63.897L63.7155 56.4459C61.7911 57.4039 59.5287 57.4039 57.6043 56.4459L57.4229 56.3599H57.2199C54.8463 56.3627 52.9227 58.2863 52.9199 60.6599V65.8199C52.9199 67.2448 54.075 68.3999 55.4999 68.3999H65.8199C67.2448 68.3999 68.3999 67.2448 68.3999 65.8199V60.6599C68.3971 58.2863 66.4735 56.3627 64.0999 56.3599ZM66.6799 65.8199C66.6799 66.2948 66.2949 66.6799 65.8199 66.6799H55.4999C55.025 66.6799 54.6399 66.2948 54.6399 65.8199V60.6599C54.6401 59.3062 55.6864 58.1828 57.0367 58.0867C59.3346 59.1531 61.9853 59.1531 64.2831 58.0867C65.6334 58.1828 66.6797 59.3062 66.6799 60.6599V65.8199Z" fill="white"/>
                    <path d="M24.5399 45.1797C21.6901 45.1797 19.3799 47.4899 19.3799 50.3397C19.3799 53.1895 21.6901 55.4997 24.5399 55.4997C27.3897 55.4997 29.6999 53.1895 29.6999 50.3397C29.6971 47.491 27.3885 45.1825 24.5399 45.1797ZM24.5399 53.7797C22.64 53.7797 21.0999 52.2395 21.0999 50.3397C21.0999 48.4398 22.64 46.8997 24.5399 46.8997C26.4397 46.8997 27.9799 48.4398 27.9799 50.3397C27.9799 52.2395 26.4397 53.7797 24.5399 53.7797Z" fill="white"/>
                    <path d="M27.9798 56.3599H27.7768L27.5954 56.4459C25.671 57.4039 23.4086 57.4039 21.4842 56.4459L21.3028 56.3599H21.0998C18.7262 56.3627 16.8026 58.2863 16.7998 60.6599V65.8199C16.7998 67.2448 17.9549 68.3999 19.3798 68.3999H29.6998C31.1247 68.3999 32.2798 67.2448 32.2798 65.8199V60.6599C32.277 58.2863 30.3534 56.3627 27.9798 56.3599ZM30.5598 65.8199C30.5598 66.2948 30.1747 66.6799 29.6998 66.6799H19.3798C18.9049 66.6799 18.5198 66.2948 18.5198 65.8199V60.6599C18.52 59.3062 19.5663 58.1828 20.9166 58.0867C23.2144 59.1531 25.8652 59.1531 28.163 58.0867C29.5133 58.1828 30.5596 59.3062 30.5598 60.6599V65.8199Z" fill="white"/>
                    <path d="M51.1323 60.4744C51.1318 60.4734 51.1314 60.4724 51.131 60.4714C50.9448 60.0344 50.4396 59.8311 50.0028 60.0173C46.1193 61.6448 41.8064 61.9417 37.7366 60.8618L38.5415 60.6253C38.9976 60.4918 39.259 60.0141 39.1255 59.558C38.992 59.102 38.5142 58.8406 38.0582 58.9741L34.6182 59.9829C34.6053 59.9829 34.5959 59.9975 34.5838 60.0018C34.5042 60.0315 34.4293 60.073 34.3619 60.1248C34.3321 60.1409 34.3033 60.159 34.2759 60.179C34.1852 60.2599 34.1133 60.3597 34.0652 60.4714C34.0186 60.5838 33.9963 60.7048 33.9999 60.8265C34.0208 60.943 34.0495 61.0579 34.0859 61.1705C34.091 61.1826 34.0859 61.1963 34.0953 61.2084L35.8153 64.4996C36.0353 64.9209 36.555 65.0842 36.9763 64.8642C37.3976 64.6443 37.5609 64.1245 37.341 63.7032L36.6409 62.3651C41.2649 63.7551 46.2294 63.4849 50.6753 61.6014C51.1126 61.4164 51.3173 60.9118 51.1323 60.4744Z" fill="white"/>
                    <path d="M65.5678 39.4119C65.2319 39.0762 64.6876 39.0762 64.3517 39.4119L63.1348 40.6288C62.4227 33.0963 57.6404 26.5596 50.6769 23.6008C50.2399 23.4146 49.7347 23.6179 49.5485 24.0549C49.3624 24.4919 49.5656 24.997 50.0026 25.1832C56.2495 27.8381 60.5918 33.6401 61.3778 40.382L60.4078 39.4119C60.0661 39.0819 59.5217 39.0913 59.1917 39.433C58.8699 39.7662 58.8699 40.2946 59.1917 40.6279L61.7717 43.2079C62.1071 43.5442 62.6517 43.5449 62.988 43.2094C62.9885 43.2089 62.9891 43.2084 62.9895 43.2079L65.5695 40.6279C65.9048 40.2917 65.904 39.7473 65.5678 39.4119Z" fill="white"/>
                    <path d="M35.695 24.2466C35.6922 24.1964 35.6848 24.1467 35.6727 24.0979C35.6727 24.0901 35.6632 24.085 35.6598 24.0764C35.6563 24.0678 35.6598 24.0617 35.6598 24.0549C35.6363 24.0131 35.6092 23.9733 35.5789 23.9362C35.5511 23.8858 35.5182 23.8383 35.4809 23.7943C35.43 23.7492 35.3737 23.7109 35.3132 23.6799C35.283 23.6542 35.2507 23.6309 35.2169 23.6102L31.7769 22.039C31.3375 21.8586 30.8351 22.0686 30.6547 22.5079C30.4841 22.9234 30.662 23.4003 31.0631 23.6025L32.8441 24.4169C26.139 28.0054 21.9551 34.9949 21.96 42.5999C21.96 43.0748 22.345 43.4599 22.82 43.4599C23.2949 43.4599 23.68 43.0748 23.68 42.5999C23.6757 35.9034 27.2168 29.7051 32.9877 26.3081L32.366 27.6075C32.161 28.0351 32.3406 28.5478 32.7676 28.7539C32.8836 28.8106 33.0109 28.84 33.14 28.8399C33.4702 28.8395 33.771 28.65 33.914 28.3523L35.634 24.7635C35.634 24.7549 35.634 24.7454 35.6408 24.7368C35.6632 24.678 35.6788 24.6169 35.6873 24.5545C35.702 24.5082 35.713 24.4608 35.72 24.4126C35.7171 24.3566 35.7088 24.301 35.695 24.2466Z" fill="white"/>
                    </g>
                    <defs>
                    <clipPath id="clip0_103_143">
                    <rect width="51.6" height="51.6" fill="white" transform="translate(16.7998 16.7998)"/>
                    </clipPath>
                    </defs>
                    </svg>

                    </div>
                    <div class="media-body">
                        <h5 class="pb-2"><a href="<?php echo TAOH_SITE_URL_ROOT."/employers";?>">Employers</a></h5>
                        <p>The right fit is about synergy. Engage directly with professionals eager to contribute and grow</p>
                    </div>
                </div>
            </div><!-- end col-lg-4 -->
            <div class="col-lg-4 responsive-column-half">
                <div class="media media-card align-items-center hover-s">
                    <div class="icon-element mr-3">
                    <svg width="84" height="84" viewBox="0 0 84 84" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g id="Group 113">
                    <g id="Group 92">
                    <circle id="Ellipse 2" cx="42" cy="42" r="42" fill="#FFB600"/>
                    </g>
                    <g id="Group 109">
                    <path id="Vector" d="M42.6013 68.3998C35.7044 68.3998 29.2235 65.7167 24.3483 60.843C14.2837 50.7808 14.2837 34.4086 24.3483 24.3464C34.4129 14.2843 50.7867 14.2843 60.8513 24.3464C70.916 34.4086 70.916 50.7808 60.8513 60.843C55.979 65.7167 49.4952 68.3998 42.6013 68.3998ZM42.6013 19.6125C36.7108 19.6125 30.8233 21.8527 26.3379 26.3362C17.3728 35.3001 17.3728 49.8864 26.3379 58.8503C30.6808 63.1939 36.4578 65.5857 42.6013 65.5857C48.7418 65.5857 54.5188 63.1939 58.8617 58.8503C67.8268 49.8864 67.8268 35.3001 58.8617 26.3362C54.3792 21.8527 48.4888 19.6125 42.6013 19.6125Z" fill="white"/>
                    <path id="Vector_2" d="M55.9794 58.5429H30.047C29.2704 58.5429 28.6392 57.9107 28.6392 57.1358V53.5759C28.6392 45.652 35.0881 39.208 43.0118 39.208C50.9355 39.208 57.3844 45.6549 57.3844 53.5759V57.1358C57.3873 57.9107 56.7561 58.5429 55.9794 58.5429ZM31.4549 55.7287H54.5686V53.5759C54.5686 47.2047 49.3821 42.0222 43.0118 42.0222C36.6414 42.0222 31.4549 47.2076 31.4549 53.5759V55.7287Z" fill="white"/>
                    <path id="Vector_3" d="M43.0127 42.0195C37.6808 42.0195 33.3408 37.6817 33.3408 32.3535C33.3408 27.0223 37.6808 22.6846 43.0127 22.6846C48.3446 22.6846 52.6817 27.0223 52.6817 32.3535C52.6817 37.6817 48.3446 42.0195 43.0127 42.0195ZM43.0127 25.4987C39.2341 25.4987 36.1566 28.5751 36.1566 32.3535C36.1566 36.1319 39.2341 39.2053 43.0127 39.2053C46.7913 39.2053 49.866 36.1319 49.866 32.3535C49.866 28.5751 46.7913 25.4987 43.0127 25.4987Z" fill="white"/>
                    </g>
                    </g>
                    </svg>
                    </div>
                    <div class="media-body">
                        <h5 class="pb-2"><a href="<?php echo TAOH_SITE_URL_ROOT."/professionals";?>">Professionals</a></h5>
                        <p>Your next opportunity is just a connection away. Dive in, converse, and carve your path</p>
                    </div>
                </div>
            </div><!-- end col-lg-4 -->
            <div class="col-lg-4 responsive-column-half">
                <div class="media media-card align-items-center hover-s">
                    <div class="icon-element mr-3">
                    <svg width="84" height="84" viewBox="0 0 84 84" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g id="Group 121">
                    <g id="Group 94">
                    <g id="Group 93">
                    <circle id="Ellipse 2" cx="42" cy="42" r="42" fill="#0F9D58"/>
                    </g>
                    </g>
                    <g id="Clip path group">
                    <mask id="mask0_103_207" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="10" y="22" width="65" height="42">
                    <g id="53633507fe">
                    <path id="Vector" d="M10.7998 22.7998H74.3998V63.0798H10.7998V22.7998Z" fill="white"/>
                    </g>
                    </mask>
                    <g mask="url(#mask0_103_207)">
                    <g id="Group">
                    <path id="Vector_2" d="M63.6594 24.3981L57.3127 28.1611C59.5751 30.2215 61.339 32.5783 62.7849 35.1322C64.2341 37.6928 65.3604 40.4405 66.3525 43.2826L72.7324 39.4998C71.7403 36.6245 70.619 33.8536 69.1747 31.3046C67.7321 28.754 65.9583 26.4154 63.6594 24.3981ZM21.8325 22.8975L29.4744 27.429C29.8156 27.6311 29.9299 28.0716 29.7278 28.4145C29.6748 28.5023 29.6069 28.5751 29.5274 28.6315C29.2823 28.8401 29.0421 29.0505 28.8086 29.2641C29.3121 29.8206 29.857 30.1403 30.5642 30.3192C31.4801 30.5527 32.6842 30.5775 34.362 30.5593C35.2945 29.6219 36.0232 29.1002 37.5321 28.6894C38.39 28.4559 39.4848 28.2687 40.6259 28.1329C41.8913 27.9822 43.2478 27.8911 44.4204 27.8646C45.5235 27.8414 46.451 27.8779 47.2774 27.954C48.1238 28.0319 48.8459 28.1478 49.5299 28.2836C49.8198 28.34 50.1428 28.4095 50.4624 28.4791C51.4446 28.6894 52.4069 28.8965 53.437 28.9296C54.1144 28.9511 54.8498 28.9031 55.5156 28.8335C55.6316 28.8203 55.7475 28.807 55.8618 28.7938C55.7989 28.7391 55.7359 28.6861 55.673 28.6315C55.5935 28.5751 55.5256 28.5023 55.4726 28.4145C55.2705 28.0716 55.3831 27.6311 55.726 27.429L63.3679 22.8975L63.3696 22.8992C63.6246 22.7501 63.9575 22.7667 64.1977 22.9687C66.8295 25.1798 68.8236 27.7702 70.4269 30.6007C72.0119 33.4015 73.211 36.4258 74.2694 39.5528C74.4002 39.8757 74.2793 40.255 73.9679 40.4389L66.326 44.9704C66.2829 44.9952 66.2365 45.0167 66.1885 45.0316C65.8109 45.1575 65.4035 44.9555 65.2776 44.5795L65.1865 44.3112C64.6614 44.8892 64.1083 45.406 63.517 45.868C62.8826 46.3633 62.2085 46.7972 61.4864 47.1798C62.0628 47.8721 62.2599 48.7052 62.1721 49.5101C62.1208 49.9938 61.9618 50.4658 61.7183 50.8865C61.4715 51.3121 61.1419 51.6865 60.7494 51.9697C60.2028 52.3639 59.5403 52.5957 58.8182 52.5659C58.8331 52.6984 58.8397 52.8342 58.8397 52.97C58.8397 53.9472 58.4439 54.83 57.8029 55.471C57.1619 56.1103 56.2759 56.5061 55.297 56.5061C55.0453 56.5061 54.7985 56.4796 54.5583 56.4266C54.5467 57.0096 54.3761 57.5744 54.0813 58.0663C53.7733 58.5781 53.331 59.0071 52.7878 59.292C52.3356 59.5288 51.8172 59.6663 51.2558 59.6663C50.9659 59.6663 50.6827 59.6282 50.4127 59.557C50.3432 60.0621 50.1494 60.5408 49.8595 60.9515C49.4919 61.4732 48.9635 61.8873 48.3292 62.1092C48.0012 62.2235 47.6501 62.2865 47.2841 62.2865C46.4576 62.2865 44.9024 62.1606 43.1749 61.7879C42.1364 61.5643 41.0334 61.2513 39.9817 60.8207L39.2993 61.672L39.3009 61.6736C38.6997 62.4206 37.8484 62.8562 36.9607 62.9539C36.0729 63.0516 35.1471 62.8131 34.3968 62.2136L34.3951 62.2119L34.3935 62.2136C33.7227 61.677 33.3037 60.9399 33.1529 60.1549C32.7737 60.3569 32.3629 60.4795 31.9373 60.5259C31.0594 60.6236 30.1286 60.3934 29.3303 59.8816C29.2442 59.8269 29.1647 59.7706 29.0918 59.7143C28.3714 59.1479 28.0153 58.3396 27.9523 57.5049C27.9457 57.4204 27.9424 57.3343 27.9424 57.2498C27.7138 57.321 27.4786 57.369 27.2418 57.3955C26.3242 57.4966 25.3653 57.2581 24.6531 56.7231C24.6332 56.7082 24.6514 56.7198 24.5819 56.6651L24.5802 56.6668C23.8067 56.0523 23.4109 55.1927 23.3413 54.3149C23.2933 53.7319 23.3927 53.1406 23.6212 52.6057C22.8478 52.6106 22.0677 52.3622 21.4184 51.8438C20.6665 51.2426 20.2292 50.3929 20.1315 49.5068C20.0338 48.6224 20.2739 47.6982 20.8752 46.9496L21.1087 46.6564C20.9398 46.2523 20.7708 45.9128 20.5307 45.5815C20.3501 45.3314 20.1216 45.0747 19.8152 44.7832C19.6346 45.0234 19.3133 45.1327 19.0119 45.0316C18.9639 45.0167 18.9175 44.9952 18.8744 44.9704L11.2325 40.4389C10.9211 40.255 10.8002 39.8757 10.931 39.5528C11.9894 36.4258 13.1885 33.4015 14.7735 30.6007C16.3768 27.7702 18.3709 25.1798 21.0027 22.9687C21.2429 22.7667 21.5758 22.7501 21.8308 22.8992L21.8325 22.8975ZM27.7751 30.2695C26.1387 31.9589 24.8021 33.8321 23.6676 35.8361C22.3409 38.1781 21.2876 40.7072 20.3551 43.3323C20.9646 43.8607 21.3787 44.3029 21.6983 44.7435C21.859 44.967 21.9931 45.184 22.1124 45.406L23.0449 44.2433V44.2416C23.6444 43.493 24.4957 43.059 25.3835 42.9613C26.2696 42.8636 27.1954 43.1021 27.9457 43.7017L27.949 43.705L27.9507 43.7033L27.954 43.7066L27.9556 43.705L27.9871 43.7331C28.5635 44.2068 28.9477 44.8296 29.1316 45.502L29.3088 45.2801C30.4798 43.8143 31.8809 43.435 33.0801 43.7215C33.567 43.8375 34.0159 44.066 34.3968 44.3758C34.7761 44.6855 35.0908 45.0764 35.3144 45.5202C35.745 46.3749 35.8493 47.4266 35.4369 48.48C36.2187 48.4617 37.0104 48.7019 37.6679 49.2153C37.701 49.2385 37.7325 49.265 37.7623 49.2948C38.4314 49.9623 38.8521 50.812 38.9482 51.6831C39.0426 52.5328 38.8322 53.3974 38.2492 54.1344L38.1664 54.242C38.8769 54.2735 39.5842 54.5236 40.1804 55.0006C40.9324 55.6002 41.3696 56.4515 41.4673 57.3359C41.5551 58.1226 41.3746 58.9408 40.9125 59.6398C41.7638 59.9594 42.6433 60.2012 43.4797 60.3818C45.0895 60.7296 46.5255 60.8472 47.2841 60.8472C47.4911 60.8472 47.6832 60.814 47.8555 60.7528C48.195 60.6335 48.4799 60.4099 48.6819 60.1234C48.8824 59.8385 48.9983 59.494 48.9983 59.1346C48.9983 59.0634 48.9933 58.9922 48.985 58.921C47.4464 58.5168 45.916 57.9653 44.3972 57.268C42.7443 56.5078 41.1029 55.5753 39.4715 54.4673C39.1436 54.2437 39.0591 53.7965 39.2827 53.4686C39.508 53.1406 39.9552 53.0562 40.2831 53.2798C41.8466 54.3414 43.4184 55.2358 45.0001 55.9629C46.5769 56.6867 48.1619 57.2448 49.7535 57.634C49.8728 57.6639 49.9771 57.7202 50.0616 57.7964C50.2223 57.9305 50.4094 58.0382 50.6115 58.1127C50.8102 58.1856 51.0272 58.227 51.2558 58.227C51.5771 58.227 51.8686 58.1508 52.117 58.02C52.4234 57.8593 52.6735 57.6175 52.8458 57.3293C53.0197 57.0411 53.1157 56.7065 53.1157 56.3604C53.1157 56.1616 53.0843 55.9596 53.0147 55.7608C49.4173 54.5899 45.4274 52.6123 41.045 49.8315C40.7088 49.6195 40.6094 49.1739 40.8214 48.8377C41.035 48.5031 41.4806 48.4038 41.8168 48.6158C46.2191 51.4099 50.2007 53.3675 53.7584 54.4872C53.8577 54.5186 53.9439 54.5683 54.0184 54.6329C54.1939 54.7687 54.3927 54.878 54.6047 54.9509C54.8167 55.0255 55.0502 55.0652 55.297 55.0652C55.8767 55.0652 56.4017 54.8317 56.7827 54.4524C57.1619 54.0731 57.3971 53.5481 57.3971 52.97C57.3971 52.7796 57.3723 52.5941 57.3243 52.4185C57.2961 52.3191 57.263 52.2231 57.2232 52.1303C56.203 51.7676 54.6676 51.0521 52.6155 49.9822C50.3746 48.8162 47.5292 47.2361 44.0809 45.2436C43.738 45.0449 43.6204 44.6043 43.8192 44.2615C44.0179 43.9186 44.4602 43.801 44.803 43.9998C48.2662 46.0005 51.0918 47.5707 53.2814 48.7118C55.4394 49.8364 56.9897 50.5503 57.9304 50.8534C57.9719 50.8666 58.0133 50.8832 58.0547 50.903C58.7669 51.2608 59.4227 51.1515 59.903 50.8053C60.1349 50.638 60.3287 50.4194 60.4711 50.1726C60.6152 49.9225 60.7096 49.6443 60.7411 49.3594C60.8057 48.7599 60.5738 48.1272 59.9146 47.6916L59.913 47.6899L59.908 47.6883L59.903 47.6833L59.8914 47.675H59.8898C59.8815 47.6684 59.8716 47.6601 59.8616 47.6535V47.6518L59.8467 47.6402L59.8434 47.6386L59.8384 47.6336L59.7656 47.5723C59.7474 47.5574 59.7291 47.5442 59.7126 47.5276C58.5267 46.5289 57.3375 45.5302 56.0854 44.5315C54.7869 43.4963 53.4271 42.4661 51.9448 41.4475C49.0298 39.4435 45.5798 37.4427 42.1199 35.4403C41.7737 35.5016 41.4259 35.5728 41.0698 35.6954C40.6375 35.8428 40.1788 36.0713 39.6686 36.4572C39.1204 36.8713 38.4778 37.4957 37.8153 38.1383C37.1429 38.7909 36.4505 39.4617 35.7698 40.0016C35.0394 40.5796 34.3206 41.0103 33.62 41.2471C32.8532 41.5071 32.1079 41.5386 31.394 41.2918L31.3907 41.2902V41.2918C30.6172 41.0235 29.9001 40.4206 29.5191 39.571C29.2028 38.8654 29.1183 37.9975 29.428 37.0237C29.9564 35.3691 31.659 33.4329 32.9989 31.9953C31.876 31.9787 30.9783 31.9108 30.2098 31.7154C29.221 31.4636 28.4641 31.0247 27.7751 30.2695ZM27.8877 28.1611L21.541 24.3981C19.2404 26.4154 17.4683 28.754 16.0257 31.3046C14.5814 33.8536 13.4601 36.6245 12.468 39.4998L18.7287 43.2114C18.7535 43.1452 18.7916 43.0806 18.838 43.021C18.8827 42.9663 18.934 42.9199 18.9887 42.8818C19.946 40.1821 21.0342 37.5719 22.4155 35.1322C23.8614 32.5783 25.6253 30.2215 27.8877 28.1611ZM25.1996 50.4029C25.2311 50.3648 25.2642 50.3316 25.299 50.3018L27.3047 47.7893C27.3246 47.7562 27.3461 47.7247 27.3726 47.6932L27.3759 47.6949C27.7221 47.2576 27.8596 46.716 27.8016 46.196C27.7453 45.6859 27.5002 45.1973 27.0778 44.8478L27.0414 44.8196L27.043 44.818L27.0414 44.8147C26.6041 44.4702 26.0625 44.3327 25.5408 44.3907C25.0191 44.447 24.5173 44.702 24.1661 45.141L24.1595 45.1476H24.1579L22.5298 47.1781C22.5082 47.2096 22.4834 47.2411 22.4569 47.2709L21.9964 47.844C21.6453 48.2812 21.5045 48.8261 21.5625 49.3495C21.6221 49.8729 21.8755 50.3747 22.3144 50.7242C22.7534 51.0736 23.2983 51.2128 23.8233 51.1548C24.3467 51.0968 24.8485 50.8418 25.1996 50.4029ZM35.1802 55.6995C35.205 55.6449 35.2365 55.5902 35.2779 55.5405C35.3541 55.4445 35.4369 55.3534 35.5214 55.2673L37.1164 53.2466C37.4509 52.8243 37.5702 52.3274 37.5172 51.8405C37.4575 51.3055 37.1909 50.7722 36.7652 50.3366C36.3313 50.0054 35.7996 49.8729 35.2879 49.9292C34.8142 49.9805 34.3604 50.1942 34.0225 50.5585L29.9614 55.6449C29.9365 55.7111 29.9001 55.7757 29.8537 55.8337C29.8123 55.8867 29.7759 55.938 29.7444 55.9877C29.4844 56.3919 29.3519 56.9036 29.3899 57.3972C29.4231 57.8543 29.6102 58.2899 29.9829 58.5831C30.0259 58.6162 30.0674 58.646 30.1088 58.6725C30.6305 59.0071 31.2267 59.1578 31.7783 59.0965C32.2652 59.0435 32.719 58.8183 33.0469 58.4025L33.5455 57.7715C33.6349 57.6158 33.7376 57.4635 33.8535 57.3194C33.9032 57.2564 33.9612 57.2051 34.0241 57.1637L35.1802 55.6995ZM34.7396 58.5831C34.5641 58.9392 34.4995 59.335 34.5425 59.7193C34.6005 60.2426 34.8556 60.7428 35.2961 61.094L35.2945 61.0956C35.7334 61.4451 36.2783 61.5842 36.8017 61.5262C37.325 61.4683 37.8269 61.2115 38.1797 60.7726V60.7743L39.1602 59.5536C39.1867 59.5122 39.2181 59.4725 39.2529 59.4377L39.604 59.0005C39.9552 58.5615 40.0943 58.0183 40.0363 57.4933C39.9784 56.9699 39.7233 56.4697 39.2844 56.1202C38.8455 55.7691 38.3006 55.63 37.7755 55.688C37.3466 55.736 36.9325 55.9165 36.6029 56.2213L34.7396 58.5831ZM32.7554 49.8397C32.767 49.8215 32.7803 49.8033 32.7935 49.7867L32.8068 49.7702C32.82 49.752 32.8349 49.7354 32.8499 49.7205L32.8515 49.7172L33.6349 48.7367C34.357 47.834 34.3869 46.8767 34.0291 46.1678C33.8966 45.9028 33.7111 45.6709 33.4892 45.4904C33.2689 45.3115 33.0171 45.1807 32.7472 45.1161C32.0515 44.9505 31.2019 45.2155 30.4367 46.1761L25.2195 52.71L25.1864 52.7514C24.8833 53.1539 24.7359 53.6822 24.7789 54.2023C24.8187 54.7058 25.0423 55.196 25.4779 55.5422L25.4762 55.5439H25.4779C25.5094 55.5704 25.463 55.5306 25.5209 55.5753C25.94 55.89 26.5197 56.0291 27.0844 55.9662C27.5979 55.9099 28.0881 55.6846 28.4177 55.2706L32.7554 49.8397ZM57.2183 30.0575C56.8257 30.1171 56.2941 30.195 55.6614 30.2612C54.9492 30.3374 54.1509 30.3887 53.3923 30.3639C52.2164 30.3258 51.2011 30.1072 50.1626 29.8852C49.8761 29.8223 49.5863 29.761 49.2534 29.6948C48.5892 29.5639 47.9068 29.453 47.1483 29.3834C46.3715 29.3105 45.4953 29.2774 44.4486 29.3006C43.3472 29.3238 42.042 29.4132 40.7949 29.5623C39.7018 29.6915 38.6765 29.8637 37.9097 30.0724C36.3909 30.4865 35.8212 31.0926 34.5955 32.3945L34.5442 32.4491C33.254 33.8205 31.293 35.9057 30.7978 37.456C30.6056 38.0588 30.6487 38.5772 30.8325 38.9863C31.0429 39.455 31.4387 39.788 31.8644 39.9354V39.937C32.2553 40.0712 32.6892 40.0447 33.1579 39.8857C33.6945 39.7051 34.2693 39.3557 34.8738 38.877C35.5081 38.3735 36.169 37.7325 36.8116 37.1098C37.4989 36.4423 38.1648 35.7964 38.8008 35.3144C39.4599 34.8159 40.0479 34.5227 40.6011 34.3339C41.1444 34.1468 41.6396 34.064 42.1364 33.9795C42.2954 33.953 42.4644 33.9795 42.6151 34.0673L42.6433 34.0822C46.1876 36.1343 49.732 38.1847 52.7629 40.2666C54.29 41.315 55.673 42.3618 56.9814 43.4069C58.1292 44.3211 59.2273 45.2387 60.3171 46.1579C61.1618 45.7538 61.9286 45.2867 62.6325 44.7368C63.3547 44.172 64.0172 43.5162 64.6349 42.7477C63.7555 40.3312 62.7617 38.0042 61.5328 35.8361C60.3519 33.7493 58.949 31.8015 57.2183 30.0575Z" fill="white"/>
                    </g>
                    </g>
                    </g>
                    </g>
                    </svg>
                    </div>
                    <div class="media-body">
                        <h5 class="pb-2"><a href="<?php echo TAOH_SITE_URL_ROOT."/partners";?>">Partners</a></h5>
                        <p>Collaboration breeds innovation. Let's redefine the professional landscape and craft meaningful opportunities</p>
                    </div>
                </div>
            </div><!-- end col-lg-4 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section>
<script>

	//let isLoggedIn = "<?php //echo taoh_user_is_logged_in(); ?>";
	let loaderArea = $('#loaderArea');
  	//let searchQuery = $('#searchQuery');
	//let eventArea = $('#eventArea');
    let jobs_list = $('.jobs-list');
    let articles_list = $('.articles_list');
	//let locationSelectInput = $('#locationSelect');
    //let geohashInput = $('#geohash');
    //let jobCount = $('#jobCount');
    let totalItems = 0; 
    let search = "";
	let itemsPerPage = 10;
	let currentPage = 1;
	loader(true, loaderArea);

    $(document).ready(function(){
       // alert('----');
        taoh_jobs_init();
        taoh_articles_init();
    });
    function show_pagination(holder) {
		return $(holder).pagination({
            items: totalItems,
            itemsOnPage: itemsPerPage,
            currentPage: currentPage,
            displayedPages: 3,
            onInit: function() {
                $("#pagination ul").addClass('pagination');
                $("#pagination ul li.disabled").addClass('page-link text-gray');
                $("#pagination ul li.active").addClass('page-link bg-primary text-white');
            },
            onPageClick: function(pageNumber, event) {
                $("#pagination ul").addClass('pagination');
                $("#pagination ul li.disabled").addClass('page-link text-gray');
                $("#pagination ul li.active").addClass('page-link bg-primary text-white');
                currentPage = pageNumber;
                taoh_jobs_init();
            }
		});
	}

    function taoh_jobs_init (queryString=""){
    var geohash = '';
	//geohash = geohashInput.val();
    //alert('-----aaaa-----')
    loader(true, loaderArea);
    var data = {
       'taoh_action': 'jobs_get',
       'ops': 'list',
       'search': search,
       'geohash': geohash,
       'offset': currentPage - 1,
       'limit': itemsPerPage,
	   'filters': queryString
     };
    jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
      res = response;

      render_jobs_template(res, jobs_list);
      //alert('----111------');
      loader(false, loaderArea);
    }).fail(function() {
        //alert('---222-------');
        loader(false, loaderArea);
        console.log( "Network issue!" );

    })
  }

  function taoh_articles_init (){
		loader(true, loaderArea);
		var data = {
			 'taoh_action': 'articles_get',
			 'ops': 'front',
			 'type': 'reads',
			 'mod': 'core',
			 'category': 'jobs',
		 };
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
			res = response;
            console.log(res,'articles data');
			render_articles_grid_template(res, articles_list);
			loader(false, loaderArea);
		}).fail(function() {
			loader(false, loaderArea);
	    	console.log( "Network issue!" );
	  })
	}

    function render_articles_grid_template(data, slot) {
		slot.empty();
		if(data.output === false ) {
			slot.append("<p>No data found!</p>");
			return false;
		}
		if(data.output.count == 0 ) {
			slot.append("<p>No data found!</p>");
			return false;
		}
		totalItems = data.output.count;
		$.each(data.output.list, function(i, v){
            slot.append(
				`<div class="col-lg-3 responsive-column-half">
                    <div class="card card-item hover-y">
                        <a href="<?php echo TAOH_READS_URL."/blog/"; ?>${convertToSlug(v.title)}-${v.conttoken}" class="card-img">
                            <img class="lazy" src="${v.imgurl}" alt="${v.title}">
                        </a>
                        <div class="card-body pt-0">
                            <a href="<?php echo TAOH_SITE_URL_ROOT."/".($app_data?->slug ?? ''); ?>" class="card-link">JOBS</a>
                            <h5 class="card-title fw-medium"><a target="_blank" href="<?php echo TAOH_READS_URL."/blog/"; ?>${convertToSlug(v.title)}-${v.conttoken}">${v.title}</a></h5>
                            <div class="media media-card align-items-center shadow-none p-0 mb-0 rounded-0 mt-4 bg-transparent">
                            <span class="blog-detail"><a target="_blank" href="<?php echo TAOH_READS_URL."/blog/"; ?>${convertToSlug(v.title)}-${v.conttoken}">READ MORE <i class="la la-arrow-right ml-1"></i></a></span>
                        </div>
                    </div>
                </div>`);
		});
	}

  function render_jobs_template(data, slot) {
    console.log(data)
		slot.empty();
		if(data.output === false) {
			slot.append("<p>No data found!</p>");
			return false;
		}
		totalItems = data.output.total
		//jobCount.append(totalItems + ' jobs Found');
		
        result = format_object(data);
		$.each(result.output.list, function(i, v){
			slot.append(
				`<div class="col-lg-12  media media-card align-items-center hover-s">
                    <div class="icon-element mr-3 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 0 24 24" width="30px" fill="#0d233e"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M14 6V4h-4v2h4zM4 8v11h16V8H4zm16-2c1.11 0 2 .89 2 2v11c0 1.11-.89 2-2 2H4c-1.11 0-2-.89-2-2l.01-11c0-1.11.88-2 1.99-2h4V4c0-1.11.89-2 2-2h4c1.11 0 2 .89 2 2v2h4z"/></svg>
                    </div>
					<div class="media-body">
						<h5 class="pb-1"><a target='_blank' href="<?php echo TAOH_SITE_URL_ROOT."/".$current_app."/d/"; ?>${convertToSlug(v.title)}-${v.conttoken}">${v.title}</a>

						&nbsp;&nbsp;
						<?php if ( taoh_user_is_logged_in()) { ?>
							<a href="<?php echo TAOH_SITE_URL_ROOT."/".$current_app."/d/"; ?>${convertToSlug(v.title)}-${v.conttoken}" class="tag-link text-primary">APPLY</a> 
						<?php } else { ?>
							<a href="<?php echo TAOH_SITE_URL_ROOT."/".$current_app."/d/"; ?>${convertToSlug(v.title)}-${v.conttoken}" class="tag-link text-primary">APPLY</a>  
						<?php } ?>
						</h5>

						${(v.skill && v.skill.length > 0)? generateSkillHTML(v.skill): ''}
						${(v.rolechat && v.rolechat.length > 0)? generateRoleHTML(v.rolechat): ''}
						${(v.company && v.company.length)? generateCompanyHTML(v.company): ''}
						${v.full_location ? generateLocationHTML(v.full_location): ''}
					</div>
                    <span class="text-underline fs-18 text-black fw-medium"><a target='_blank' href="<?php echo TAOH_SITE_URL_ROOT."/".$current_app."/d/"; ?>${convertToSlug(v.title)}-${v.conttoken}">Show detail <i class="la la-arrow-right ml-1"></a></i></span>
			</div>`);
		});
		if(data.output.total >= 11) {
			show_pagination('#pagination')
		}
	}
</script>
<script>
    var testimonialCarousel = $('.testimonial-carousel');
     /*==== Testimonial carousel =====*/
        if (testimonialCarousel.length) {
            testimonialCarousel.owlCarousel({
                items: 1,
                loop: true,
                margin: 15,
                smartSpeed: 800,
                dots: false,
                nav: true,
                navText: ["<svg xmlns=\"http://www.w3.org/2000/svg\" height=\"24px\" viewBox=\"0 0 24 24\" width=\"24px\" fill=\"#000000\"><path d=\"M0 0h24v24H0V0z\" fill=\"none\"/><path d=\"M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12l4.58-4.59z\"/></svg>", "<svg xmlns=\"http://www.w3.org/2000/svg\" height=\"24px\" viewBox=\"0 0 24 24\" width=\"24px\" fill=\"#000000\"><path d=\"M0 0h24v24H0V0z\" fill=\"none\"/><path d=\"M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z\"/></svg>"],
            });
        }
</script>
<?php
taoh_get_footer();
?>
