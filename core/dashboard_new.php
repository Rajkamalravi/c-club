<?php
    $user_data = taoh_user_all_info();
    //print_r($user_data);exit();
    if ( ! isset( $user_data->fname ) && defined( 'TAOH_API_TOKEN' ) && defined('TAOH_SETTINGS_URL') && TAOH_API_TOKEN ) {
        taoh_redirect(TAOH_SITE_URL_ROOT.'/createacc'); 
        taoh_exit();
    }
    
    // TAO_PAGE_DESCRIPTION
    define( 'TAO_PAGE_DESCRIPTION', taoh_site_description() );
    // TAO_PAGE_IMAGE
    define( 'TAO_PAGE_IMAGE', TAOH_SITE_LOGO );
    // TAO_PAGE_TITLE
    if ( ! defined ( 'TAO_PAGE_TITLE' ) ) { define ( 'TAO_PAGE_TITLE', TAOH_SITE_NAME_SLUG." - ".TAOH_SITE_TITLE ); }
    if ( ! defined ( 'TAO_PAGE_DESCRIPTION' ) ) { define ( 'TAO_PAGE_DESCRIPTION', TAOH_SITE_DESCRIPTION); }
    if ( ! defined ( 'TAO_PAGE_KEYWORDS' ) ) { define ( 'TAO_PAGE_KEYWORDS', TAOH_SITE_NAME_SLUG." Career development events, ".TAOH_SITE_NAME_SLUG." Professional development events, ".TAOH_SITE_NAME_SLUG." Job training workshops, ".TAOH_SITE_NAME_SLUG." Industry conferences, ".TAOH_SITE_NAME_SLUG." Networking events, ".TAOH_SITE_NAME_SLUG." Skill-building seminars, ".TAOH_SITE_NAME_SLUG." Career advancement workshops, ".TAOH_SITE_NAME_SLUG." Personal branding events, ".TAOH_SITE_NAME_SLUG." Career coaching sessions, ".TAOH_SITE_NAME_SLUG." Job search strategies, ".TAOH_SITE_NAME_SLUG." Resume writing workshops, ".TAOH_SITE_NAME_SLUG." Interview preparation seminars, ".TAOH_SITE_NAME_SLUG." LinkedIn networking events, ".TAOH_SITE_NAME_SLUG." Mentoring programs, ".TAOH_SITE_NAME_SLUG." Professional certification courses, ".TAOH_SITE_NAME_SLUG." Job fair, ".TAOH_SITE_NAME_SLUG." Career exploration events, ".TAOH_SITE_NAME_SLUG." Industry-specific training, ".TAOH_SITE_NAME_SLUG." Professional networking opportunities" ); }
    taoh_get_header();
    $showall = 0;
    $app_temp = @taoh_parse_url(0) ? taoh_parse_url(0):TAOH_PLUGIN_PATH_NAME;
    
    // Get the current app
    $current_app = TAOH_SITE_CURRENT_APP_SLUG;
    
    $app_data = taoh_app_info($current_app);
    
    $about_url = TAOH_SITE_URL_ROOT."/about";
    if ( $current_app != TAOH_PLUGIN_PATH_NAME ) $about_url = TAOH_SITE_URL_ROOT."/".$current_app."/about";

    $url = "core.content.get";
    $taoh_vals = array(
        'token'=> TAOH_API_TOKEN_DUMMY,
        'mod' => 'users',
        'ops' => 'list',
        'type' => 'recipe_list',
        'tags' => 'recent',
        'cache_time' => '120',
        // 'cfcc5h' => 1 //cfcache newly added
    );
    // $cache_name = $url.'_recipe_list_' . hash('sha256', $url . serialize($taoh_vals));
    // $taoh_vals[ 'cfcache' ] = $cache_name;
    // $taoh_vals[ 'cache_name' ] = $cache_name;
    // $taoh_vals[ 'cache' ] = array ( "name" => $cache_name );
    ksort($taoh_vals);
    
    //echo taoh_apicall_get_debug($url, $taoh_vals);taoh_exit();
    $response_recipe = json_decode(taoh_apicall_get($url, $taoh_vals,'',1), true);
    $response_recipe_api = $response_recipe['output'];
    $first_six_elements = array_slice($response_recipe_api, 0, 6);
    //print_r($response_recipe_api);die;
  
?>
<style>
  .yo-video .y-video{
  width: 375px;
  height: 240px;
  }
  @media only screen and (min-width: 1000px) and (max-width: 1400px){
  .yo-video .y-video{
  width: 237px;
  height: 240px;
  }
  }
  @media only screen and (min-width: 370px) and (max-width: 920px){
  .yo-video .y-video{
  width:350px;
  padding-top:15px;
  height:285px;
  }
  }
  .app-box {
  padding: 8pt;
  margin-left: 10px;
  text-align: center;
  box-shadow: 0px 5px 8px 0px rgba(105, 104, 104, 0.5);
  border-style: solid;
  border-color: skyblue;
  height: 215px;
  background-color: #fff;
  }
  .app-box .app-text{
  font-size: small;
  line-height: 18px;
  height: 35px;
  }
  .large-12 .heading{
    /* width:920px; */
    font-size: 20px;
  }
  .owl-item.cloned.active {
    display: none;
  }
</style>
<!--======================================
  START HERO AREA
  ======================================-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="https://fonts.cdnfonts.com/css/hobo-bt" rel="stylesheet">
<?php if(isset($_GET[ 'already' ]) && $_GET[ 'already' ]){?>
<div class="alert alert-danger alert-dismissable m-0 text-center" id="flash-msg">
  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
  <h4>! Account already exists. Re-directing you to home page.</h4>
</div>
<?php } ?>
<?php
  if ( ! taoh_user_is_logged_in() || $showall ){
?>
<section class="hero-area pt-80px pb-80px hero-bg-1">
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
                    <h4 class="pb-1 text-white">Bring Career Home</h4>
                    <h2 class="section-title pb-3 text-white">Elevate Your Career with<br /><?php echo TAOH_SITE_NAME_SLUG; ?>!</h2>
                    <p class="section-desc text-white">
                        <?php
                        echo "
                        Are you ready to advance your career trajectory? You're in the right<br />
                        place! Join as member of one of the fastest-growing ".strtolower(TAOH_CLUB_TARGET)."<br />
                        communities online. At ".TAOH_SITE_NAME_SLUG.",<br />
                        we're not just about job listings—we're about fostering a vibrant,<br />inclusive network where career growth thrives.
                        ";
                        ?>
                
                    </p>
                    <div class="hero-btn-box py-4">
                        <?php if (! taoh_user_is_logged_in()){ ?>
                            <div class="nav-right-button">

                                <!-- <a href="<?php //echo TAOH_LOGIN_URL."?redirect_url=".TAOH_REDIRECT_URL; ?>" class="btn theme-btn theme-btn mr-2"><i class="la la-sign-in mr-1"></i> Login / Sign Up And Grow Together!</a> -->
                                <a onclick="localStorage.removeItem('isCodeSent')" href="javascript:void(0);"
                                class="btn theme-btn theme-btn mr-2 login-button " aria-pressed="true" data-toggle="modal" data-target="#config-modal"><i class="la la-sign-in mr-1"></i> Login / Sign Up And Grow Together!</a>

                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="hero-list hero-list-bg">
                    <div class="d-flex align-items-center pb-30px">
                        <svg class="mr-3" width="31" height="30" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M29 0H2a2 2 0 0 0-2 2v20c0 1.1.9 2 2 2h4v5.8l6.2-5.8H29a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM19 8a1 1 0 1 0 0-2H6a1 1 0 0 0 0 2h13zm6 5a1 1 0 1 0 0-2H6a1 1 0 1 0 0 2h19zm-6 5a1 1 0 1 0 0-2H6a1 1 0 1 0 0 2h13z" fill="#fff"/></svg>
                        <p class="fs-15 text-white lh-20">Get Answers: Have your<br />burning career questions <br />addressed by experts <br />and peers.</p>
                    </div>
                    <div class="d-flex align-items-center pb-30px">
                        <svg class="mr-3" width="35" height="34" fill="none" xmlns="http://www.w3.org/2000/svg"><path opacity=".5" fill-rule="evenodd" clip-rule="evenodd" d="M4 1c0-.6.4-1 1-1h25a5 5 0 0 1 5 5v17.5a1 1 0 1 1-2 0V5a3 3 0 0 0-3-3H5a1 1 0 0 1-1-1z" fill="#fff"/><path fill-rule="evenodd" clip-rule="evenodd" d="M2 4h27a2 2 0 0 1 2 2v20a2 2 0 0 1-2 2h-4v5.8L18.8 28H2a2 2 0 0 1-2-2V6c0-1.1.9-2 2-2zm17 8a1 1 0 1 0 0-2H6a1 1 0 1 0 0 2h13zm6 5a1 1 0 1 0 0-2H6a1 1 0 1 0 0 2h19zm-6 5a1 1 0 1 0 0-2H6a1 1 0 1 0 0 2h13z" fill="#fff"/></svg>
                        <p class="fs-15 text-white lh-20">Find Opportunities: Connect<br />with a plethora of job<br />openings that align with your<br />skills and aspirations.</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <svg class="mr-3" width="33" height="33" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.2 14a1 1 0 0 1-.8-1.7L10.4.7a2 2 0 0 1 3.1 0l10 11.6a1 1 0 0 1-.7 1.7H1.2z" fill="#fff"/><path opacity=".5" fill-rule="evenodd" clip-rule="evenodd" d="M3.4 21h17.2L12 31 3.4 21zm-3-.3a1 1 0 0 1 .8-1.7h21.6a1 1 0 0 1 .8 1.7l-10 11.6a2 2 0 0 1-3.1 0L.5 20.7z" fill="#fff"/></svg>
                        <p class="fs-15 text-white lh-20">Network Effectively: Participate<br />in events that are not just<br />about networking, but growing<br />and learning together.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 yo-video">
                <?php 
                    //$video_link = 'https://www.youtube.com/embed/o9qbgV0Aotk?rel=0';
                    //taoh_video_widget($video_link); 
                ?>
                <iframe id="video" class="y-video" src="https://www.youtube.com/embed/o9qbgV0Aotk?rel=0" frameborder="0" allowfullscreen loading="lazy"></iframe>
            </div>
        </div>
    </div>
</section>
<!--======================================
        END HERO AREA
======================================-->


<!-- ================================
         START FUNFACT AREA
================================= -->
<section class="funfact-area">
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
                </div>
                <div class="col responsive-column-half border-right border-right-gray">
                    <div class="media media-card text-center px-0 py-4 shadow-none rounded-0 bg-transparent counter-item mb-0">
                        <div class="media-body">
                            <h5 class="fw-semi-bold pb-2">2+ Million</h5>
                            <p class="lh-20">Career events attended</p>
                        </div>
                    </div>
                </div>
                <div class="col responsive-column-half border-right border-right-gray">
                    <div class="media media-card text-center px-0 py-4 shadow-none rounded-0 bg-transparent counter-item mb-0">
                        <div class="media-body">
                            <h5 class="fw-semi-bold pb-2">1+ Million</h5>
                            <p class="lh-20">Connections created</p>
                        </div>
                    </div>
                </div>
                <div class="col responsive-column-half border-right border-right-gray">
                    <div class="media media-card text-center px-0 py-4 shadow-none rounded-0 bg-transparent counter-item mb-0">
                        <div class="media-body">
                            <h5 class="fw-semi-bold pb-2">10,000+</h5>
                            <p class="lh-20">Recruiters on platform</p>
                        </div>
                    </div>
                </div>
                <div class="col responsive-column-half">
                    <div class="media media-card text-center px-0 py-4 shadow-none rounded-0 bg-transparent counter-item mb-0">
                        <div class="media-body">
                            <h5 class="fw-semi-bold pb-2">150+</h5>
                            <p class="lh-20">Communities served</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
  }
?>
<section class="faq-area pt-80px pb-80px">
  <div class="container">
    <div class="row">
      <div class="col-lg-2">
        <?php taoh_leftmenu_widget(); ?>
      </div>
      <!-- end col-lg-2 -->
      <div class="col-lg-10">
        <div class="">
          <div class="question-main-bar">
            <div class="questions-snippet">
              <?php foreach($first_six_elements as $key => $value){ 
                $recipe_id = $value['ID'];
                $id_plus_one = $key + 1;
                $recipe_name = $value['name'];
                ?>
                <section class="caro-slider-section" id="recipe_sec_<?php echo $recipe_id; ?>">
                    <div class="row">
                        <div class="large-12 columns">
                            <h4 class="heading"><?php echo ucwords($recipe_name); ?></h4>
                            <div class="owl-carousel owl-theme" id="intRecipe<?php echo $id_plus_one; ?>">
                                
                                
                            </div>
                        </div>
                    </div>
                </section>
                <?php } ?>
            </div>
          </div>
        </div>
      </div>
      <!-- end col-lg-7 -->
    </div>
    <!-- end row -->
  </div>
  <!-- end container -->
</section>

<script>
  let itemsPerPage = 10;
  let currentPage = 1;
  let totalItems = 0; //this will be rewriiten on response of jobs on line 363
  let activityArea = $('#activityArea');
  let loaderArea = $("#loaderArea");
  let intRecipe1 = $("#intRecipe1");
  let intRecipe2 = $("#intRecipe2");
  let intRecipe3 = $("#intRecipe3");
  let intRecipe4 = $("#intRecipe4");
  let intRecipe5 = $("#intRecipe5");
  let intRecipe6 = $("#intRecipe6");
  let prefix = '<?php echo TAOH_CDN_PREFIX ?>';
  let already_rendered_one = false;
  let already_rendered_two = false;
  let already_rendered_three = false;
  let already_rendered_four = false;
  let already_rendered_five = false;
  let already_rendered_six = false;
  let reads_recipe_list_one = '';
  let reads_recipe_list_two = '';
  let reads_recipe_list_three = '';
  let reads_recipe_list_four = '';
  let reads_recipe_list_five = '';
  let reads_recipe_list_six = '';
  let store_name = READStore;
  
  //Initial run
  $(document).ready(function(){
        $("#flash-msg").delay(3000).fadeOut("slow");
        <?php if(TAOH_INTAODB_ENABLE) { ?>
          getrecipelistdata1();
          getrecipelistdata2();
          getrecipelistdata3();
          getrecipelistdata4();
          getrecipelistdata5();
          getrecipelistdata6();
        <?php }else{ ?>
          taoh_recipe_init1();
          taoh_recipe_init2();
          taoh_recipe_init3();
          taoh_recipe_init4();
          taoh_recipe_init5();
          taoh_recipe_init6();
        <?php } ?>
  })

  function getrecipelistdata1(){
		var id = '<?php echo $first_six_elements[0]['ID']; ?>';
		getIntaoDb(dbName).then((db) => {
      reads_recipe_list_one = 'reads_recipe_list_'+crc32('list_'+id);     
      const datareadswellrequest = db.transaction(store_name).objectStore(store_name).get(reads_recipe_list_one); // get main data
      datareadswellrequest.onsuccess = ()=> {
          console.log(datareadswellrequest);
          const readswellstoredatares = datareadswellrequest.result;
          if(readswellstoredatares !== undefined && readswellstoredatares !== null && readswellstoredatares !== "" && readswellstoredatares !== "undefined" && readswellstoredatares !== "null"){
              const readswellstoredata = datareadswellrequest.result.values;
              already_rendered_one = true;
              loader(false, loaderArea);
              render_recipe_blog_template(readswellstoredata, intRecipe1, 1);
          }else{
              loader(true, loaderArea);
              taoh_recipe_init1();
          }
      }
		}).catch((error) => {
        console.log('Getreadsrecipelistdata Error:', error);
    });
	}

  function taoh_recipe_init1() {
    var data = {
        'taoh_action': 'taoh_blog_recipe_get_ajax',
        'category': '',
        'count': 5,
        'id': '<?php echo $first_six_elements[0]['ID']; ?>',  
    };
    jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response1) {
        if (response1.success) {
          <?php if(TAOH_INTAODB_ENABLE) { ?>
            indx_reads_recipe_list_one(response1);
          if(!already_rendered_one){
            render_recipe_blog_template(response1, intRecipe1, 1);
          }
          <?php }else{ ?>
            render_recipe_blog_template(response1, intRecipe1, 1);
          <?php } ?>
        } else {
          $("#recipe_sec_<?php echo $first_six_elements[0]['ID']; ?>").hide();
        }
    }).fail(function() {
        console.log( "Network issue!" );
    })
  }

  function indx_reads_recipe_list_one(readslistdata){
    var reads_taoh_data = { taoh_data:reads_recipe_list_one,values : readslistdata };
    let reads_setting_time = new Date();
    reads_setting_time = reads_setting_time.setMinutes(reads_setting_time.getMinutes() + 600);
    var reads_setting_timedata = { taoh_ttl: reads_recipe_list_one,time:reads_setting_time };
    obj_data = { [store_name]:reads_taoh_data,[TTLStore] : reads_setting_timedata };
    Object.keys(obj_data).forEach(key => {
    // console.log(key, obj_data[key]);
        IntaoDB.setItem(key,obj_data[key]).catch((err) => console.log('Storage failed', err));
    });
    return false; 
  }

  function getrecipelistdata2(){
    var id = '<?php echo $first_six_elements[1]['ID']; ?>';
    getIntaoDb(dbName).then((db) => {
      reads_recipe_list_two = 'reads_recipe_list_'+crc32('list_'+id);     
      const datareadswellrequest = db.transaction(store_name).objectStore(store_name).get(reads_recipe_list_two); // get main data
      datareadswellrequest.onsuccess = ()=> {
          console.log(datareadswellrequest);
          const readswellstoredatares = datareadswellrequest.result;
          if(readswellstoredatares !== undefined && readswellstoredatares !== null && readswellstoredatares !== "" && readswellstoredatares !== "undefined" && readswellstoredatares !== "null"){
              const readswellstoredata = datareadswellrequest.result.values;
              already_rendered_two = true;
              loader(false, loaderArea);
              render_recipe_blog_template(readswellstoredata, intRecipe2, 2);
          }else{
              loader(true, loaderArea);
              taoh_recipe_init2();
          }
      }
    }).catch((error) => {
        console.log('Getreadsrecipelistdata Error:', error);
    });
  }

  function taoh_recipe_init2() {
    var data = {
        'taoh_action': 'taoh_blog_recipe_get_ajax',
        'category': '',
        'count': 5,
        'id': '<?php echo $first_six_elements[1]['ID']; ?>',  
    };
    jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response2) {
        if (response2.success) {
          <?php if(TAOH_INTAODB_ENABLE) { ?>
            indx_reads_recipe_list_two(response2);
            if(!already_rendered_two){
              render_recipe_blog_template(response2, intRecipe2, 2);
            }
          <?php }else{ ?>
            render_recipe_blog_template(response2, intRecipe2, 2);
          <?php } ?>
        } else {
          intRecipe2.html('<p class="fs-20 text-black">No posts to display!</p>');
        }
    }).fail(function() {
      $("#recipe_sec_<?php echo $first_six_elements[1]['ID']; ?>").hide();
    })
  }

  function indx_reads_recipe_list_two(readslistdata){
    console.log(readslistdata);
    var reads_taoh_data = { taoh_data:reads_recipe_list_two,values : readslistdata };
    let reads_setting_time = new Date();
    reads_setting_time = reads_setting_time.setMinutes(reads_setting_time.getMinutes() + 600);
    var reads_setting_timedata = { taoh_ttl: reads_recipe_list_two,time:reads_setting_time };
    obj_data = { [store_name]:reads_taoh_data,[TTLStore] : reads_setting_timedata };
    Object.keys(obj_data).forEach(key => {
    // console.log(key, obj_data[key]);
        IntaoDB.setItem(key,obj_data[key]).catch((err) => console.log('Storage failed', err));
    });
    return false; 
  }

  function getrecipelistdata3(){
    var id = '<?php echo $first_six_elements[2]['ID']; ?>';
    getIntaoDb(dbName).then((db) => {
      reads_recipe_list_three = 'reads_recipe_list_'+crc32('list_'+id);     
      const datareadswellrequest = db.transaction(store_name).objectStore(store_name).get(reads_recipe_list_three); // get main data
      datareadswellrequest.onsuccess = ()=> {
          console.log(datareadswellrequest);
          const readswellstoredatares = datareadswellrequest.result;
          if(readswellstoredatares !== undefined && readswellstoredatares !== null && readswellstoredatares !== "" && readswellstoredatares !== "undefined" && readswellstoredatares !== "null"){
              const readswellstoredata = datareadswellrequest.result.values;
              already_rendered_three = true;
              loader(false, loaderArea);
              render_recipe_blog_template(readswellstoredata, intRecipe3, 3);
          }else{
              loader(true, loaderArea);
              taoh_recipe_init3();
          }
      }
    }).catch((error) => {
        console.log('Getreadsrecipelistdata Error:', error);
    });
  }

  function taoh_recipe_init3() {
    var data = {
        'taoh_action': 'taoh_blog_recipe_get_ajax',
        'category': '',
        'count': 5,
        'id': '<?php echo $first_six_elements[2]['ID']; ?>',  
    };
    jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response3) {
        if (response3.success) {
          <?php if(TAOH_INTAODB_ENABLE) { ?>
            indx_reads_recipe_list_three(response3);
            if(!already_rendered_three){
              render_recipe_blog_template(response3, intRecipe3, 3);
            }
          <?php }else{ ?>
            render_recipe_blog_template(response3, intRecipe3, 3);
          <?php } ?>
        } else {
          $("#recipe_sec_<?php echo $first_six_elements[2]['ID']; ?>").hide();
        }
    }).fail(function() {
        console.log( "Network issue!" );
    })
  }

  function indx_reads_recipe_list_three(readslistdata){
    var reads_taoh_data = { taoh_data:reads_recipe_list_three,values : readslistdata };
    let reads_setting_time = new Date();
    reads_setting_time = reads_setting_time.setMinutes(reads_setting_time.getMinutes() + 600);
    var reads_setting_timedata = { taoh_ttl: reads_recipe_list_three,time:reads_setting_time };
    obj_data = { [store_name]:reads_taoh_data,[TTLStore] : reads_setting_timedata };
    Object.keys(obj_data).forEach(key => {
    // console.log(key, obj_data[key]);
        IntaoDB.setItem(key,obj_data[key]).catch((err) => console.log('Storage failed', err));
    });
    return false; 
  }

  function getrecipelistdata4(){
    var id = '<?php echo $first_six_elements[3]['ID']; ?>';
    getIntaoDb(dbName).then((db) => {
      reads_recipe_list_four = 'reads_recipe_list_'+crc32('list_'+id);     
      const datareadswellrequest = db.transaction(store_name).objectStore(store_name).get(reads_recipe_list_four); // get main data
      datareadswellrequest.onsuccess = ()=> {
          console.log(datareadswellrequest);
          const readswellstoredatares = datareadswellrequest.result;
          if(readswellstoredatares !== undefined && readswellstoredatares !== null && readswellstoredatares !== "" && readswellstoredatares !== "undefined" && readswellstoredatares !== "null"){
              const readswellstoredata = datareadswellrequest.result.values;
              already_rendered_four = true;
              loader(false, loaderArea);
              render_recipe_blog_template(readswellstoredata, intRecipe4, 4);
          }else{
              loader(true, loaderArea);
              taoh_recipe_init4();
          }
      }
    }).catch((error) => {
        console.log('Getreadsrecipelistdata Error:', error);
    });
  }

  function taoh_recipe_init4() {
    var data = {
        'taoh_action': 'taoh_blog_recipe_get_ajax',
        'category': '',
        'count': 5,
        'id': '<?php echo $first_six_elements[3]['ID']; ?>',  
    };
    jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response4) {
        if (response4.success) {
          <?php if(TAOH_INTAODB_ENABLE) { ?>
            indx_reads_recipe_list_four(response4);
            if(!already_rendered_four){
              render_recipe_blog_template(response4, intRecipe4, 4);
            }
          <?php }else{ ?>
            render_recipe_blog_template(response4, intRecipe4, 4);
          <?php } ?>
        } else {
          $("#recipe_sec_<?php echo $first_six_elements[3]['ID']; ?>").hide();
        }
    }).fail(function() {
        console.log( "Network issue!" );
    })
  }

  function indx_reads_recipe_list_four(readslistdata){
    var reads_taoh_data = { taoh_data:reads_recipe_list_four,values : readslistdata };
    let reads_setting_time = new Date();
    reads_setting_time = reads_setting_time.setMinutes(reads_setting_time.getMinutes() + 600);
    var reads_setting_timedata = { taoh_ttl: reads_recipe_list_four,time:reads_setting_time };
    obj_data = { [store_name]:reads_taoh_data,[TTLStore] : reads_setting_timedata };
    Object.keys(obj_data).forEach(key => {
    // console.log(key, obj_data[key]);
        IntaoDB.setItem(key,obj_data[key]).catch((err) => console.log('Storage failed', err));
    });
    return false; 
  }

  function getrecipelistdata5(){
    var id = '<?php echo $first_six_elements[4]['ID']; ?>';
    getIntaoDb(dbName).then((db) => {
      reads_recipe_list_five = 'reads_recipe_list_'+crc32('list_'+id);     
      const datareadswellrequest = db.transaction(store_name).objectStore(store_name).get(reads_recipe_list_five); // get main data
      datareadswellrequest.onsuccess = ()=> {
          console.log(datareadswellrequest);
          const readswellstoredatares = datareadswellrequest.result;
          if(readswellstoredatares !== undefined && readswellstoredatares !== null && readswellstoredatares !== "" && readswellstoredatares !== "undefined" && readswellstoredatares !== "null"){
              const readswellstoredata = datareadswellrequest.result.values;
              already_rendered_five = true;
              loader(false, loaderArea);
              render_recipe_blog_template(readswellstoredata, intRecipe5, 5);
          }else{
              loader(true, loaderArea);
              taoh_recipe_init5();
          }
      }
    }).catch((error) => {
        console.log('Getreadsrecipelistdata Error:', error);
    });
  }

  function taoh_recipe_init5() {
    var data = {
        'taoh_action': 'taoh_blog_recipe_get_ajax',
        'category': '',
        'count': 5,
        'id': '<?php echo $first_six_elements[4]['ID']; ?>',  
    };
    jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response5) {
        if (response5.success) {
          <?php if(TAOH_INTAODB_ENABLE) { ?>
            indx_reads_recipe_list_five(response5);
            if(!already_rendered_five){
              render_recipe_blog_template(response5, intRecipe5, 5);
            }
          <?php }else{ ?>
            render_recipe_blog_template(response5, intRecipe5, 5);
          <?php } ?>
        } else {
          $("#recipe_sec_<?php echo $first_six_elements[4]['ID']; ?>").hide();
        }
    }).fail(function() {
        console.log( "Network issue!" );
    })
  }

  function indx_reads_recipe_list_five(readslistdata){
    var reads_taoh_data = { taoh_data:reads_recipe_list_five,values : readslistdata };
    let reads_setting_time = new Date();
    reads_setting_time = reads_setting_time.setMinutes(reads_setting_time.getMinutes() + 600);
    var reads_setting_timedata = { taoh_ttl: reads_recipe_list_five,time:reads_setting_time };
    obj_data = { [store_name]:reads_taoh_data,[TTLStore] : reads_setting_timedata };
    Object.keys(obj_data).forEach(key => {
    // console.log(key, obj_data[key]);
        IntaoDB.setItem(key,obj_data[key]).catch((err) => console.log('Storage failed', err));
    });
    return false; 
  }

  function getrecipelistdata6(){
    var id = '<?php echo $first_six_elements[5]['ID']; ?>';
    getIntaoDb(dbName).then((db) => {
      reads_recipe_list_six = 'reads_recipe_list_'+crc32('list_'+id);     
      const datareadswellrequest = db.transaction(store_name).objectStore(store_name).get(reads_recipe_list_six); // get main data
      datareadswellrequest.onsuccess = ()=> {
          console.log(datareadswellrequest);
          const readswellstoredatares = datareadswellrequest.result;
          if(readswellstoredatares !== undefined && readswellstoredatares !== null && readswellstoredatares !== "" && readswellstoredatares !== "undefined" && readswellstoredatares !== "null"){
              const readswellstoredata = datareadswellrequest.result.values;
              already_rendered_six = true;
              loader(false, loaderArea);
              render_recipe_blog_template(readswellstoredata, intRecipe6, 6);
          }else{
              loader(true, loaderArea);
              taoh_recipe_init6();
          }
      }
    }).catch((error) => {
        console.log('Getreadsrecipelistdata Error:', error);
    });
  }

  function taoh_recipe_init6() {
    var data = {
        'taoh_action': 'taoh_blog_recipe_get_ajax',
        'category': '',
        'count': 5,
        'id': '<?php echo $first_six_elements[5]['ID']; ?>',  
    };
    jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response6) {
        if (response6.success) {
          <?php if(TAOH_INTAODB_ENABLE) { ?>
            indx_reads_recipe_list_six(response6);
            if(!already_rendered_six){
              render_recipe_blog_template(response6, intRecipe6, 6);
            }
          <?php }else{ ?>
            render_recipe_blog_template(response6, intRecipe6, 6);
          <?php } ?>
        } else {
          $("#recipe_sec_<?php echo $first_six_elements[5]['ID']; ?>").hide();
        }
    }).fail(function() {
        console.log( "Network issue!" );
    })
  }

  function indx_reads_recipe_list_six(readslistdata){
    var reads_taoh_data = { taoh_data:reads_recipe_list_six,values : readslistdata };
    let reads_setting_time = new Date();
    reads_setting_time = reads_setting_time.setMinutes(reads_setting_time.getMinutes() + 600);
    var reads_setting_timedata = { taoh_ttl: reads_recipe_list_six,time:reads_setting_time };
    obj_data = { [store_name]:reads_taoh_data,[TTLStore] : reads_setting_timedata };
    Object.keys(obj_data).forEach(key => {
    // console.log(key, obj_data[key]);
        IntaoDB.setItem(key,obj_data[key]).catch((err) => console.log('Storage failed', err));
    });
    return false; 
  }

  function remove_cloned_items(){
    console.log('Removing cloned items');
    var clonedItems = document.querySelectorAll('.owl-item.cloned.active');
    clonedItems.forEach(function(item){
      item.remove();
    });
  }

  function render_recipe_blog_template(data, slot, id) {
    slot.empty();
    if(data.success === false) {
        slot.append('<p class="fs-20 text-black">No posts to display!</p>');
        return false;
    }
    totalItems = data.length;

    $.each(data.output, function(i, v){
        if(v.media_url){
          var image = v.media_url;
        } else {
          var image = prefix+"/images/igcache/"+encodeURIComponent(v.title)+"/900_600/blog.jpg";
        }
        let decodedStr = decode(v.description);
        slot.append(`
                <div class="item" style="width:230px">
                  <a href="<?php echo TAOH_SITE_URL_ROOT; ?>/learning/blog/${convertToSlug(v.title)}-${v.conttoken}">
                  <div class="card">
                      <div class="card-body">
                      <div class="card-top-img">
                          <img src="${image}" alt="${image}">
                          <!-- <p>6hrs <span>20mins</span></p> -->
                      </div>
                      <div class="card-body-content">
                          <p class="category-type">${v.category[0]}</p>
                          <h4 class="card-title">${v.title}</h4>
                          <div class="card-desc">
                            <p class="card-text">${v.decodedStr}</p>
                          </div>
                      </div>
                      </div>
                  </div>
                  </a>
              </div>`
        );
    });
    $('#intRecipe'+id).owlCarousel({
      items:1, // Number of items to show
      autoWidth:true,
      loop: true, // Enable looping
      margin: 10,
      nav: true,
    });
    $('#intRecipe'+id).trigger('refresh.owl.carousel');
  } 
  /* $('#myCarousel').carousel({
   interval: 5000
  }) */
  
</script>
<?php taoh_get_footer();  ?>