<?php
if ( ! defined ( 'TAO_PAGE_TITLE' ) ) { define ( 'TAO_PAGE_TITLE', "Career Questions: Explore and Learn from the Communitys at ".TAOH_SITE_NAME_SLUG ); }
if ( ! defined ( 'TAO_PAGE_DESCRIPTION' ) ) { define ( 'TAO_PAGE_DESCRIPTION', "Browse through a wide range of career questions and gain valuable insights on career development from our vibrant community at ".TAOH_SITE_NAME_SLUG.". Find answers to your burning career-related queries and engage in knowledge-sharing discussions. Discover the collective wisdom and expertise of professionals in various fields on our career-focused Quora-like platform." ); }
if ( ! defined ( 'TAO_PAGE_KEYWORDS' ) ) { define ( 'TAO_PAGE_KEYWORDS', "Career development community at ".TAOH_SITE_NAME_SLUG.", Career insights at ".TAOH_SITE_NAME_SLUG.", Career knowledge sharing at ".TAOH_SITE_NAME_SLUG.", Professional networking platform at ".TAOH_SITE_NAME_SLUG.", Industry experts at ".TAOH_SITE_NAME_SLUG.", Career advice and guidance at ".TAOH_SITE_NAME_SLUG.", Career discussions at ".TAOH_SITE_NAME_SLUG.", Job search tips at ".TAOH_SITE_NAME_SLUG.", Resume writing tips at ".TAOH_SITE_NAME_SLUG.", Interview preparation at ".TAOH_SITE_NAME_SLUG.", Skill development at ".TAOH_SITE_NAME_SLUG.", Personal branding at ".TAOH_SITE_NAME_SLUG.", Professional growth at ".TAOH_SITE_NAME_SLUG.", Mentorship opportunities at ".TAOH_SITE_NAME_SLUG.", Industry-specific insights at ".TAOH_SITE_NAME_SLUG.", Job market trends at ".TAOH_SITE_NAME_SLUG.", Career exploration at ".TAOH_SITE_NAME_SLUG ); }
taoh_get_header();
$current_app = taoh_parse_url(1);
$app_config = taoh_app_info($current_app);
$taoh_user_vars = taoh_user_all_info();
$empty = 0;
$ptoken = $taoh_user_vars->ptoken;
$share_link = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
define( 'TAO_PAGE_TYPE', $app_config->slug );
$log_nolog_token = ( taoh_user_is_logged_in()) ? $ptoken : TAOH_API_TOKEN_DUMMY;
/* check liked or not */
$taoh_call = "system.users.metrics";
$taoh_vals = array(
    'mod' => 'system',
    'token' => taoh_get_dummy_token(),
    'slug' => TAO_PAGE_TYPE,
);
//echo taoh_apicall_get_debug($taoh_call, $taoh_vals);exit();
$get_liked = json_decode( taoh_apicall_get($taoh_call, $taoh_vals), true );
$liked_arr = json_encode($get_liked['conttoken_liked']);
/* End check liked or not */
?>
<style>

span.h5 {
  font-size: 13px !important;
}
</style>

<div class ="ramainder-chats">
  <div class="row m-0">
    <div class="col-md-3 col-lg-3 p-0 left-chat-content">
      <div class="sec-head-block">
        <input type="search">
        <i class="la la-angle-right text-black"></i>
      </div>
      <div class="leftchatlist">
        <div class="row unread-chat">
          <div class="col-sm-1 col-md-3 col-lg-3 pl-0">
            <div class="comment-avatar">
              <img width="40" class="lazy" src="https://opslogy.com/avatar/PNG/128/default.png" alt="avatar" style="">
            </div>
          </div>
          <div class="col-sm-11 col-md-9 col-lg-9 pl-0">
            <p>Airbnb Support <span style="display: none">. Burlington</span></p>
            <p>Can you describe your issue in a few sentences? This will help our</p>
            <p style="display: none">Trip completed <span>. Dec 22 - 27, 2022</span></p>
          </div>
        </div>
        <div class="row selected-chat">
          <div class="col-sm-1 col-md-3 col-lg-3 pl-0">
            <div class="comment-avatar">
              <img width="40" class="lazy" src="https://opslogy.com/avatar/PNG/128/default.png" alt="avatar" style="">
            </div>
          </div>
          <div class="col-sm-11 col-md-9 col-lg-9 pl-0">
            <p>Gaurav <span>. Burlington</span></p>
            <p>Airbnb update: Remainder - Leave a review</p>
            <p>Trip completed <span>. Jul 19 - 20, 2023</span></p>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-1 col-md-3 col-lg-3 pl-0">
            <div class="comment-avatar">
              <img width="40" class="lazy" src="https://opslogy.com/avatar/PNG/128/default.png" alt="avatar" style="">
            </div>
          </div>
          <div class="col-sm-11 col-md-9 col-lg-9 pl-0">
            <p>Airbnb Support <span>. Burlington</span></p>
            <p>Can you describe your issue in a few sentences? This will help our</p>
            <p>Last message sent July 19</p>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-1 col-md-3 col-lg-3 pl-0">
            <div class="comment-avatar">
              <img width="40" class="lazy" src="https://opslogy.com/avatar/PNG/128/default.png" alt="avatar" style="">
            </div>
          </div>
          <div class="col-sm-11 col-md-9 col-lg-9 pl-0">
            <p>Airbnb Support <span>. Burlington</span></p>
            <p>Can you describe your issue in a few sentences? This will help our</p>
            <p>Trip completed <span>. Dec 22 - 27, 2022</span></p>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-1 col-md-3 col-lg-3 pl-0">
            <div class="comment-avatar">
              <img width="40" class="lazy" src="https://opslogy.com/avatar/PNG/128/default.png" alt="avatar" style="">
            </div>
          </div>
          <div class="col-sm-11 col-md-9 col-lg-9 pl-0">
            <p>Airbnb Support <span>. Burlington</span></p>
            <p>Can you describe your issue in a few sentences? This will help our</p>
            <p>Trip completed <span>. Dec 22 - 27, 2022</span></p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-6 mid-chat-content p-0">
      <div class="sec-head-block">
        <div class="row">
          <div class="col-sm-6 col-md-5 col-lg-5 pr-0">
            <p class="headerusername">Gaurav</p>
            <p>Response time: 1hour</p>
          </div>
          <div class="col-sm-6 col-md-7 col-lg-7">
            <div class="d-flex justify-content-end usericonright">
              <a href="#"><i class="la la-language text-black"></i></a>
              <a href="#"><i class="la la-shopping-basket text-black"></i></a>
              <a href="#" data-metrics="post" class="btn theme-btn">Show details</a>
            </div>
          </div>
        </div>
      </div>
      <div class="text-center">July 17, 2023</div>
      <div class="midcenternotes">
        <p><i class="la la-home text-black"></i> To protect your payment, always communicate and pay through the Airbnb website or app.</p>
        <p><i class="la la-home text-black"></i> Host's can't see your photo untill after your booking is confirmed.<a href="#">Learn more</a></p>
        <div class="row midchatuser ml-0 mr-0 mb-20px">
          <div class="col-sm-1 col-md-2 col-lg-1 p-0"><img src="https://opslogy.com/avatar/PNG/128/default.png" width="50" height="50"></div>
          <div class="col-sm-11 col-md-10 col-lg-11">
            <p><b>Vishal</b> <span>8.36 </span><span>AM</span></p>
            <p>I am Interested</p>
          </div>
        </div>
        <p><i class="la la-home text-black"></i> Your request to book has been sent. The Host has 24 hours to respond. <a href="#">Show request</a></p>
        <p><i class="la la-home text-black"></i> Your recervation is confirmed for 6 guests on July 19 - 20, 2023. <a href="#">Show reservation</a></p>
      </div>
    </div>
    <div class="col-md-3 col-lg-3 p-0">
      <div class="sec-head-block">
        <p class="rightheaderchat">Details <span style="float: right"><i class="la la-angle-right text-black"></i></span></p>
      </div>
      <div class="rightcontentsec mb-20px">
        <h2>Home care coordinators</h2>
        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. </p>
      </div>
      <div class="row m-0 rightbtn-bottom">
        <div class="col-sm-4 col-lg-4">
          <a href="#" class="btn theme-btn">Detail</a>
        </div>
        <div class="col-sm-4 col-lg-4">
          <a href="#" class="btn theme-btn">Apply</a>
        </div>
        <div class="col-sm-4 col-lg-4">
          <a href="#" class="btn theme-btn">Detail</a>
        </div>
    </div>
    </div>
  </div>
</div>
<?php taoh_get_footer(); ?>