<?php
function taoh_all_lp_reads_widget( $val_arr, $design ){
  //return '123';
  switch ( $design ){
    case 'center1':
      taoh_all_lp_central_widget1($val_arr);
      break;
    case 'center2':
      taoh_all_lp_central_widget2($val_arr);
      break;
    case 'center3':
      taoh_all_lp_central_widget3($val_arr);
      break;
    case 'center4':
      taoh_all_lp_central_widget4($val_arr);
      break;
    case 'right1':
      taoh_all_lp_right_widget1($val_arr);
      break;
    case 'right2':
      taoh_all_lp_right_widget2($val_arr);
      break;
    case 'right3':
      taoh_all_lp_right_widget3($val_arr);
      break;
    case 'right_ad1':
      taoh_lp_right_ad1($val_arr);
      break;
    case 'right_ad2':
      taoh_lp_right_ad2($val_arr);
      break;
    case 'right_ad3':
      taoh_lp_right_ad3($val_arr);
      break;
    }
  return 1;
}
function taoh_lp_get_widget(){
  $url = 'core.content.get';
  $taoh_vals = array(
    "mod" => 'users',
    "type"=> "landing", 
    //"ops"=> 'hires',
    'token'=>TAOH_API_TOKEN_DUMMY,
    "cache_time" => 120,
    //'cfcc5h' => 1 //cfcache newly added
  );
  // $cache_name = $url.'_landing_' . hash('sha256', $url . serialize($taoh_vals));
  // $taoh_vals[ 'cfcache' ] = $cache_name;
  // $taoh_vals[ 'cache_name' ] = $cache_name;
  // $taoh_vals[ 'cache' ] = array ( "name" => $cache_name );
  ksort($taoh_vals);
  
  $req = taoh_apicall_get($url, $taoh_vals, '', 1);
  //echo taoh_apicall_get_debug($url, $taoh_vals);exit();
  $res = json_decode($req, true);
  return $res['output'];
}
function taoh_lp_blog_link($conttoken, $link="") {
  if(!$link) {
    return TAOH_READS_LP_URL.'/d/'.$conttoken;
  }
  return $link;
}
function taoh_lp_category_link() {
    return TAOH_READS_LP_URL."/category";
}
function blog_lp_related_post($tags = "", $count, $debug = 0) {
  $url = 'core.content.get';
  $taoh_vals = array(
   "mod" => 'core',
   "ops"=> 'list',
   "type"=> 'reads',
   "tags"=> $tags,
   'token'=> TAOH_API_TOKEN_DUMMY,
   "category"=> 'uncategorized',
   "perpage" => $count,
   'sub_secret' => TAOH_TEMP_SITE_FILE_PATH_SECRET,
   "cache_time" => 120,
   //'cfcc1d' => 1 //cfcache newly added
 );
 $cache_name = $url.'_reads_list_' . $tags . '_' . hash('sha256', $url . serialize($taoh_vals));
// $taoh_vals[ 'cfcache' ] = $cache_name;
// $taoh_vals[ 'cache_name' ] = $cache_name;
// $taoh_vals[ 'cache' ] = array ( "name" => $cache_name );
ksort($taoh_vals);

 //echo TAOH_API_PREFIX . '/' .$url.'?'.http_build_query($taoh_vals); taoh_exit();
 if($debug) {
  echo taoh_apicall_get_debug($url, $taoh_vals);
 }  
 //echo taoh_apicall_get_debug($url, $taoh_vals);die;
 $content = taoh_apicall_get($url, $taoh_vals, '', 1);
  if($content != "") {
    $response = json_decode($content,true);
    return $response;
  }
 return array();
}
function taoh_side_bar(){ ?>
  <div id="" class="theiaStickySidebar" style="">
    <div class="widget" id="tabbed-widget">
      <div class="widget-container">
          <div class="widget-top">
            <ul class="tabs posts-taps">
                <li class="tabs active"><a data-href="#tab2"><?php echo ucwords('Recent'); ?></a></li>
                <li class="tabs"><a data-href="#tab1"><?php echo ucwords('Popular'); ?></a></li>
            </ul>
          </div>
          <div id="tab2" class="tabs-wrap" style="display: block;">
            <ul>
                <?php
                  $job_rand = blog_lp_related_post('',5);
                      if($job_rand['success'] && $job_rand['output']['list']) {
                        taoh_job_releated($job_rand['output']['list']);
                      } 
                ?>
            </ul>
          </div>
          <div id="tab1" class="tabs-wrap" style="display: none;">
            <ul>
                <?php
                  $rand_rand = blog_lp_related_post('',5);
                      if($rand_rand['success'] && $rand_rand['output']['list']) {
                        taoh_rand_releated($rand_rand['output']['list']);
                      } 
                ?>
            </ul>
          </div>
      </div>
    </div>
    <!-- .widget /-->
    <!-- <section class="cat-box column tie-cat-52">
      <div class="cat-box-title">
        <h2>#JusASK, The Career Coach</h2>
        <div class="stripe-line"></div>
      </div>
      <div class="cat-box-content">
        <div class="inner-content">
          <div class="post-thumbnail tie-appear">
            <a href="http://localhost/lp/blog/eight-039-s-test-t-duhwkvj4jemv" rel="bookmark">
            <img width="310" height="165" src="https://pcdn.tao.ai/app/jusask/images/jusask_sq_256.png" class="attachment-tie-medium size-tie-medium wp-post-image tie-appear" alt="" decoding="async"  sizes="(max-width: 310px) 100vw, 310px">
            </a>
          </div>
          												
          <div class="entry">
            <p>Talk to AI powered #CareerCoach to get your career questions answered!</p>
            <a class="more-link" href="http://localhost/lp/blog/eight-039-s-test-t-duhwkvj4jemv">Read More »</a>
          </div>
        </div>
      </div>
    </section> -->
    <div class="widget social-icons-widget">
      <div class="social-icons social-colored">
          <!-- <a class="ttip-none" title="Rss" href="https://grants.club/feed/"><i class="fa fa-rss"></i></a> -->
          <a class="ttip-none" title="Facebook" href="https://www.facebook.com/taoaihq"><i class="fa fa-facebook"></i></a>
          <a class="ttip-none" title="Twitter" href="https://twitter.com/taoaihq"><i class="fa fa-twitter"></i></a>
      </div>
    </div>
  </div>
<?php return 1; }
function taoh_all_lp_central_widget1($center1){
  foreach ($center1 as $first_key => $first_val){ 
    if ( ! isset( $first_val['image']) || ! $first_val['image'] || stristr( $first_val['image'], 'images.unsplash.com' ) ) $first_val['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($first_val['title']) )."/900_600/blog.jpg";
    //print_r($first_key);
    ?>
    <?php if($first_key == 0){ ?>
      <li class="first-news">
        <div class="post-thumbnail tie-appear">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark">
            <img width="310" height="165" src="<?php echo $first_val['image']; ?>" class="attachment-tie-medium size-tie-medium wp-post-image tie-appear" alt="" decoding="async" fetchpriority="high" srcset="<?php echo $first_val['image']; ?> 310w, <?php echo $first_val['image']; ?> 620w" sizes="(max-width: 310px) 100vw, 310px"><span class="fa overlay-icon"></span>
          </a>
        </div><!-- post-thumbnail /-->        
        <h2 class="post-box-title">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><?php echo $first_val['title']; ?></a>
        </h2>       
        <div class="entry">
          <p style="overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; text-overflow: ellipsis;"><?php echo html_entity_decode($first_val['blurb']['description']); ?> …</p>
          <a class="more-link" href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>">Read More »</a>
        </div>
      </li>
    <?php }else{ ?>
      <li class="other-news" style="display: flex; align-items: center;">
        <div class="post-thumbnail tie-appear">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark">
            <img width="110" height="75" src="<?php echo $first_val['image']; ?>" class="attachment-tie-small size-tie-small wp-post-image tie-appear" alt="" decoding="async" srcset="<?php echo $first_val['image']; ?> 110w, <?php echo $first_val['image']; ?> 220w, <?php echo $first_val['image']; ?> 330w" sizes="(max-width: 110px) 100vw, 110px"><span class="fa overlay-icon"></span>
          </a>
        </div><!-- post-thumbnail /-->				
        <h3 class="post-box-title">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><?php echo $first_val['title']; ?></a>
        </h3>
      </li>
    <?php } ?>
    <?php } 
  return 1;
}
function taoh_all_lp_central_widget2($center_2){
  $center2 = array_slice($center_2, 0, 3);
  foreach ($center2 as $first_key => $first_val){ 
    if ( ! isset( $first_val['image']) || ! $first_val['image'] || stristr( $first_val['image'], 'images.unsplash.com' ) ) $first_val['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($first_val['title']) )."/900_600/blog.jpg";
    //print_r($first_key);
    ?>
    <?php if($first_key == 0){ ?>
      <li class="first-news">
        <div class="inner-content">
            <div class="post-thumbnail tie-appear">
              <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark" style="border-radius: 12px; text-align: center;">
              <img width="310" height="165" src="<?php echo $first_val['image']; ?>" class="attachment-tie-medium size-tie-medium wp-post-image tie-appear" alt="" decoding="async" srcset="<?php echo $first_val['image']; ?> 310w, <?php echo $first_val['image']; ?> 620w" sizes="(max-width: 310px) 100vw, 310px">
              <span class="fa overlay-icon"></span>
              </a>
            </div>
            <!-- post-thumbnail /-->												
            <h2 class="post-box-title"><a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><?php echo $first_val['title']; ?></a></h2>
            <!-- <p class="post-meta">	
              <span class="tie-date"><i class="fa fa-clock-o"></i>January 23, 2024</span>	
              <span class="post-comments"><i class="fa fa-comments"></i><a href="<?php //echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>">0</a></span>
            </p> -->
            <div class="entry">
              <p><?php echo html_entity_decode($first_val['blurb']['description']); ?> …</p>
              <a class="more-link" href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>">Read More »</a>
            </div>
        </div>
      </li>
    <?php }else{ ?>
      <li class="other-news">
        <div class="post-thumbnail tie-appear">
            <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><img width="110" height="75" src="<?php echo $first_val['image']; ?>" class="attachment-tie-small size-tie-small wp-post-image tie-appear" alt="" decoding="async" srcset="<?php echo $first_val['image']; ?> 110w, <?php echo $first_val['image']; ?> 220w, <?php echo $first_val['image']; ?> 330w" sizes="(max-width: 110px) 100vw, 110px"><span class="fa overlay-icon"></span></a>
        </div>
        <!-- post-thumbnail /-->			
        <h3 class="post-box-title"><a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><?php echo $first_val['title']; ?></a></h3>
      </li>
    <?php } ?>
    <?php } 
  return 1;
}
function taoh_all_lp_central_widget4($center_4){
  $center4 = array_slice($center_4, -3, 3, true);
  $key_i = 0;
  foreach ($center4 as $first_key => $first_val){ 
    if ( ! isset( $first_val['image']) || ! $first_val['image'] || stristr( $first_val['image'], 'images.unsplash.com' ) ) $first_val['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($first_val['title']) )."/900_600/blog.jpg";
    //print_r($first_key);
    ?>
    <?php if($key_i == 0){ ?>
      <li class="first-news">
        <div class="inner-content">
            <div class="post-thumbnail tie-appear">
              <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark">
              <img width="310" height="165" src="<?php echo $first_val['image']; ?>" class="attachment-tie-medium size-tie-medium wp-post-image tie-appear" alt="" decoding="async" srcset="<?php echo $first_val['image']; ?> 310w, <?php echo $first_val['image']; ?> 620w" sizes="(max-width: 310px) 100vw, 310px"><span class="fa overlay-icon"></span>
              </a>
            </div>
            <!-- post-thumbnail /-->												
            <h2 class="post-box-title"><a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><?php echo $first_val['title']; ?></a></h2>
            <!-- <p class="post-meta">	
              <span class="tie-date"><i class="fa fa-clock-o"></i>January 23, 2024</span>	
              <span class="post-comments"><i class="fa fa-comments"></i><a href="<?php //echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>">0</a></span>
            </p> -->
            <div class="entry">
              <p><?php echo html_entity_decode($first_val['blurb']['description']); ?> …</p>
              <a class="more-link" href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>">Read More »</a>
            </div>
        </div>
      </li>
    <?php }else{ ?>
      <li class="other-news">
        <div class="post-thumbnail tie-appear">
            <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><img width="110" height="75" src="<?php echo $first_val['image']; ?>" class="attachment-tie-small size-tie-small wp-post-image tie-appear" alt="" decoding="async" srcset="<?php echo $first_val['image']; ?> 110w, <?php echo $first_val['image']; ?> 220w, <?php echo $first_val['image']; ?> 330w" sizes="(max-width: 110px) 100vw, 110px"><span class="fa overlay-icon"></span></a>
        </div>
        <!-- post-thumbnail /-->			
        <h3 class="post-box-title"><a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><?php echo $first_val['title']; ?></a></h3>
      </li>
    <?php } $key_i++;?>
    <?php } 
  return 1;
}
function taoh_all_lp_central_widget3($center3){
  //print_r($center3);die;
  foreach ($center3 as $first_key => $first_val){ 
    if ( ! isset( $first_val['image']) || ! $first_val['image'] || stristr( $first_val['image'], 'images.unsplash.com' ) ) $first_val['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($first_val['title']) )."/900_600/blog.jpg";
  ?>
    <div class="scroll-item">
        <div class="post-thumbnail tie-appear">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark">
          <img width="310" height="165" src="<?php echo $first_val['image']; ?>" class="attachment-tie-medium size-tie-medium wp-post-image tie-appear" alt="" decoding="async" srcset="<?php echo $first_val['image']; ?> 310w, <?php echo $first_val['image']; ?> 620w" sizes="(max-width: 310px) 100vw, 310px"><span class="fa overlay-icon"></span>
          </a>
        </div>
        <!-- post-thumbnail /-->
        <h3 class="post-box-title"><a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><?php echo $first_val['title']; ?></a></h3>
        <!-- <p class="post-meta">
          <span class="tie-date"><i class="fa fa-clock-o"></i>January 23, 2024</span>						
        </p> -->
    </div>
  <?php } 
  return 1;
}
function taoh_lp_blog_satart($hero) { ?>
  <div id="featured-posts" class="tie-appear">
    <div class="featured-posts-single-slide flex-active-slide" style="width: 100%; float: left; margin-right: -100%; position: relative; opacity: 1; display: block; z-index: 2;">
        <?php
              foreach( $hero as $ind => $rand_blog){
              if ( ! isset( $rand_blog[$ind]['image'] ) || ! $rand_blog[$ind]['image'] || stristr( $rand_blog[$ind]['image'], 'images.unsplash.com' ) ) $rand_blog[$ind]['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($rand_blog['title']) )."/900_600/blog.jpg";
        ?>
        <div class="featured-post featured-post-<?php echo $ind+1; ?> fea-<?php echo $ind+1; ?>">
          <div class="featured-post-inner" style="background-image:url(<?php echo $rand_blog[$ind]['image']; ?>);">
              <div class="featured-cover" style="background: -webkit-linear-gradient(top, rgba(0, 0, 0, 0.7) 50%, rgba(0, 0, 0, 0.7) 70%, rgba(0, 0, 0, 1) 100%);">
                <a href="<?php echo taoh_lp_blog_link(slugify2($rand_blog['title'])."-".$rand_blog['conttoken']); ?>"><span><?php echo $rand_blog['title']; ?></span></a>
              </div>
              <div class="featured-title">
                <!-- <span class="tie-date"><i class="fa fa-clock-o"></i></span> -->
                <h2 style="overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; text-overflow: ellipsis;"><a href="<?php echo taoh_lp_blog_link(slugify2($rand_blog['title'])."-".$rand_blog['conttoken']); ?>"><?php echo $rand_blog['title']; ?></a></h2>
                <h3 style="overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; text-overflow: ellipsis;"><?php echo $rand_blog['blurb']['description']; ?> …</h3>
              </div>
          </div>
        </div>
        <?php } ?>
    </div>
  </div>
<?php return 1; }
function taoh_releated_widget1($related){
  //print_r($related);die;
  foreach ($related as $first_key => $first_val){
    if ( ! isset( $first_val['image']) || ! $first_val['image'] || stristr( $first_val['image'], 'images.unsplash.com' ) ) $first_val['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($first_val['title']) )."/900_600/blog.jpg";
  ?>
    <div class="related-item">
      <div class="post-thumbnail tie-appear">
        <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>">
        <img width="310" height="165" src="<?php echo $first_val['image']; ?>" class="attachment-tie-medium size-tie-medium wp-post-image tie-appear" alt="" decoding="async" srcset="<?php echo $first_val['image']; ?> 310w, <?php echo $first_val['image']; ?> 620w" sizes="(max-width: 310px) 100vw, 310px"><span class="fa overlay-icon"></span>
        </a>
      </div>
      <!-- post-thumbnail /-->
      <h3><a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><?php echo $first_val['title']; ?></a></h3>
      <p class="post-meta"><span class="tie-date"><i class="fa fa-clock-o"></i> <?php echo $first_val['category'][0]; ?></span></p>
    </div>
  <?php } 
  return 1;
}
function taoh_releated_widget2($related){
  //print_r($related);die;
  foreach ($related as $first_key => $first_val){
    if ( ! isset( $first_val['image']) || ! $first_val['image'] || stristr( $first_val['image'], 'images.unsplash.com' ) ) $first_val['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($first_val['title']) )."/900_600/blog.jpg";
  ?>
    <p class="jp-relatedposts-post jp-relatedposts-post<?php echo $first_key; ?>">
      <span class="jp-relatedposts-post-title">
          <a class="jp-relatedposts-post-a" href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" title="<?php echo $first_val['title']; ?>"><?php echo $first_val['title']; ?></a>
      </span>
      <time class="jp-relatedposts-post-date mt-2" style="display:block"><?php echo $first_val['category'][0]; ?></time>
    </p>
  <?php } 
  return 1;
}
function taoh_int_releated($int_related){
  //print_r($int_related);die;
  $key_i = 0;
  foreach ($int_related as $first_key => $first_val){
    if ( ! isset( $first_val['image']) || ! $first_val['image'] || stristr( $first_val['image'], 'images.unsplash.com' ) ) $first_val['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($first_val['title']) )."/900_600/blog.jpg";
    if($key_i == 0){
      ?>
      <li class="first-pic">
        <div class="post-thumbnail tie-appear">
            <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" class="ttip" original-title="">
            <img width="310" height="205" src="<?php echo $first_val['image']; ?>" class="attachment  tie-appear" alt="" decoding="async" srcset="<?php echo $first_val['image']; ?> 310w, <?php echo $first_val['image']; ?> 620w" sizes="(max-width: 310px) 100vw, 310px">
            <span class="fa overlay-icon"></span>
            </a>
        </div>
        <!-- post-thumbnail /-->
      </li>
    <?php }else{ ?> 
      <li class="">
        <div class="post-thumbnail tie-appear">
            <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" class="ttip" original-title="">
              <img width="110" height="75" src="<?php echo $first_val['image']; ?>" class="attachment  tie-appear" alt="" decoding="async" srcset="<?php echo $first_val['image']; ?> 110w, <?php echo $first_val['image']; ?> 220w, <?php echo $first_val['image']; ?> 330w" sizes="(max-width: 110px) 100vw, 110px">
              <span class="fa overlay-icon"></span>
            </a>
        </div>
        <!-- post-thumbnail /-->
      </li>
  <?php } 
  $key_i++;
  }
  return 1;
}
function taoh_resume_releated($res_related){
  //print_r($int_related);die;
  foreach ($res_related as $first_key => $first_val){
    if ( ! isset( $first_val['image']) || ! $first_val['image'] || stristr( $first_val['image'], 'images.unsplash.com' ) ) $first_val['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($first_val['title']) )."/900_600/blog.jpg";
      ?>
      <li class="first-pic">
        <div class="post-thumbnail tie-appear">
            <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" class="ttip" original-title="">
              <img width="110" height="75" src="<?php echo $first_val['image']; ?>" class="attachment-tie-small size-tie-small wp-post-image tie-appear" alt="" decoding="async" srcset="<?php echo $first_val['image']; ?> 110w, <?php echo $first_val['image']; ?> 220w, <?php echo $first_val['image']; ?> 330w" sizes="(max-width: 110px) 100vw, 110px">
            <span class="fa overlay-icon"></span>
            </a>
        </div>
        <!-- post-thumbnail /-->
      </li>
  <?php 
  }
  return 1;
}
function taoh_brand_releated($brand_related){
  $key_i = 0;
  foreach ($brand_related as $first_key => $first_val){
    if ( ! isset( $first_val['image']) || ! $first_val['image'] || stristr( $first_val['image'], 'images.unsplash.com' ) ) $first_val['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($first_val['title']) )."/900_600/blog.jpg";
    if($key_i == 0){
      ?>
      <li class="first-news">
        <div class="inner-content">
            <div class="post-thumbnail tie-appear">
              <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark">
              <img width="310" height="165" src="<?php echo $first_val['image']; ?>" class="attachment-tie-medium size-tie-medium wp-post-image tie-appear" alt="" decoding="async" srcset="<?php echo $first_val['image']; ?> 310w, <?php echo $first_val['image']; ?> 620w" sizes="(max-width: 310px) 100vw, 310px"><span class="fa overlay-icon"></span>
              </a>
            </div>
            <!-- post-thumbnail /-->
            <h2 class="post-box-title"><a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><?php echo $first_val['title']; ?></a></h2>
            <!-- <p class="post-meta">
              <span class="tie-date"><i class="fa fa-clock-o"></i>January 23, 2024</span>	
              <span class="post-comments"><i class="fa fa-comments"></i><a href="<?php //echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>">0</a></span>
            </p> -->
            <div class="entry">
              <p  style="overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; text-overflow: ellipsis;"><?php $first_val['blurb']['description']; ?>…</p>
              <a class="more-link" href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>">Read More »</a>
            </div>
        </div>
      </li>
    <?php }else{ ?> 
      <li class="other-news">
        <div class="post-thumbnail tie-appear">
            <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><img width="110" height="75" src="<?php echo $first_val['image']; ?>" class="attachment-tie-small size-tie-small wp-post-image tie-appear" alt="" decoding="async" srcset="<?php echo $first_val['image']; ?> 110w, <?php echo $first_val['image']; ?> 220w, <?php echo $first_val['image']; ?> 330w" sizes="(max-width: 110px) 100vw, 110px"><span class="fa overlay-icon"></span></a>
        </div>
        <!-- post-thumbnail /-->
        <h3 class="post-box-title"><a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><?php echo $first_val['title']; ?></a></h3>
        <!-- <p class="post-meta">
            <span class="tie-date"><i class="fa fa-clock-o"></i>January 23, 2024</span>	
            <span class="post-comments"><i class="fa fa-comments"></i><a href="<?php //echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>">0</a></span>
        </p> -->
      </li>
  <?php } 
  $key_i++;
  }
  return 1;
}
function taoh_job_releated($job_related){
  //print_r($job_related);die;
  foreach ($job_related as $first_key => $first_val){
    if ( ! isset( $first_val['image']) || ! $first_val['image'] || stristr( $first_val['image'], 'images.unsplash.com' ) ) $first_val['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($first_val['title']) )."/900_600/blog.jpg";
      ?>
      <li>
        <div class="post-thumbnail tie-appear">
            <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark">
            <img width="110" height="75" src="<?php echo $first_val['image']; ?>" class="attachment-tie-small size-tie-small wp-post-image tie-appear" alt="" decoding="async" srcset="<?php echo $first_val['image']; ?> 110w, <?php echo $first_val['image']; ?> 220w, <?php echo $first_val['image']; ?> 330w" sizes="(max-width: 110px) 100vw, 110px"><span class="fa overlay-icon"></span>
          </a>
        </div>
        <!-- post-thumbnail /-->
        <h3><a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>"><?php echo $first_val['title']; ?></a></h3>
        <!-- <span class="tie-date"><i class="fa fa-clock-o"></i>February 1, 2024</span>	 -->	
      </li>
  <?php 
  }
  return 1;
}
function taoh_rand_releated($rand_related){
  //print_r($rand_related);die;
  foreach ($rand_related as $first_key => $first_val){
    if ( ! isset( $first_val['image']) || ! $first_val['image'] || stristr( $first_val['image'], 'images.unsplash.com' ) ) $first_val['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($first_val['title']) )."/900_600/blog.jpg";
      ?>
      <li>
        <div class="post-thumbnail tie-appear">
            <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark">
            <img width="110" height="75" src="<?php echo $first_val['image']; ?>" class="attachment-tie-small size-tie-small wp-post-image tie-appear" alt="" decoding="async" srcset="<?php echo $first_val['image']; ?> 110w, <?php echo $first_val['image']; ?> 220w, <?php echo $first_val['image']; ?> 330w" sizes="(max-width: 110px) 100vw, 110px"><span class="fa overlay-icon"></span>
          </a>
        </div>
        <!-- post-thumbnail /-->
        <h3><a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>"><?php echo $first_val['title']; ?></a></h3>
        <span class="tie-date"><i class="fa fa-clock-o"></i> <?php echo $first_val['category'][0]; ?></span>		
      </li>
  <?php 
  }
  return 1;
}
function taoh_learn_releated($learn_rand){
  //print_r($learn_rand);
  foreach ($learn_rand as $first_key => $first_val){ 
    if ( ! isset( $first_val['image']) || ! $first_val['image'] || stristr( $first_val['image'], 'images.unsplash.com' ) ) $first_val['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($first_val['title']) )."/900_600/blog.jpg";
     if($first_key == 0){ ?>
      <li class="first-news">
        <div class="post-thumbnail tie-appear">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark">
            <img width="310" height="165" src="<?php echo $first_val['image']; ?>" class="attachment-tie-medium size-tie-medium wp-post-image tie-appear" alt="" decoding="async" fetchpriority="high" srcset="<?php echo $first_val['image']; ?> 310w, <?php echo $first_val['image']; ?> 620w" sizes="(max-width: 310px) 100vw, 310px"><span class="fa overlay-icon"></span>
          </a>
        </div><!-- post-thumbnail /-->        
        <h2 class="post-box-title">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><?php echo $first_val['title']; ?></a>
        </h2>       
        <div class="entry">
          <p style="overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; text-overflow: ellipsis;"><?php echo html_entity_decode($first_val['blurb']['description']); ?> …</p>
          <a class="more-link" href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>">Read More »</a>
        </div>
      </li>
    <?php }else{ ?>
      <li class="other-news">
        <div class="post-thumbnail tie-appear">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark">
            <img width="110" height="75" src="<?php echo $first_val['image']; ?>" class="attachment-tie-small size-tie-small wp-post-image tie-appear" alt="" decoding="async" srcset="<?php echo $first_val['image']; ?> 110w, <?php echo $first_val['image']; ?> 220w, <?php echo $first_val['image']; ?> 330w" sizes="(max-width: 110px) 100vw, 110px"><span class="fa overlay-icon"></span>
          </a>
        </div><!-- post-thumbnail /-->				
        <h3 class="post-box-title">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><?php echo $first_val['title']; ?></a>
        </h3>
      </li>
    <?php } ?>
    <?php } 
  return 1;
}
function taoh_mind_releated($mind_rand){
  //print_r($mind_rand);
  foreach ($mind_rand as $first_key => $first_val){ 
    if ( ! isset( $first_val['image']) || ! $first_val['image'] || stristr( $first_val['image'], 'images.unsplash.com' ) ) $first_val['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($first_val['title']) )."/900_600/blog.jpg";
     if($first_key == 0){ ?>
      <li class="first-news">
        <div class="post-thumbnail tie-appear">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark">
            <img width="310" height="165" src="<?php echo $first_val['image']; ?>" class="attachment-tie-medium size-tie-medium wp-post-image tie-appear" alt="" decoding="async" fetchpriority="high" srcset="<?php echo $first_val['image']; ?> 310w, <?php echo $first_val['image']; ?> 620w" sizes="(max-width: 310px) 100vw, 310px"><span class="fa overlay-icon"></span>
          </a>
        </div><!-- post-thumbnail /-->        
        <h2 class="post-box-title">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><?php echo $first_val['title']; ?></a>
        </h2>       
        <div class="entry">
          <p style="overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; text-overflow: ellipsis;"><?php echo html_entity_decode($first_val['blurb']['decription']); ?> …</p>
          <a class="more-link" href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>">Read More »</a>
        </div>
      </li>
    <?php }else{ ?>
      <li class="other-news">
        <div class="post-thumbnail tie-appear">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark">
            <img width="110" height="75" src="<?php echo $first_val['image']; ?>" class="attachment-tie-small size-tie-small wp-post-image tie-appear" alt="" decoding="async" srcset="<?php echo $first_val['image']; ?> 110w, <?php echo $first_val['image']; ?> 220w, <?php echo $first_val['image']; ?> 330w" sizes="(max-width: 110px) 100vw, 110px"><span class="fa overlay-icon"></span>
          </a>
        </div><!-- post-thumbnail /-->				
        <h3 class="post-box-title">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><?php echo $first_val['title']; ?></a>
        </h3>
      </li>
    <?php } ?>
    <?php } 
  return 1;
}
function taoh_prod_releated($prod_rand){
  //print_r($prod_rand);
  foreach ($prod_rand as $first_key => $first_val){ 
    if ( ! isset( $first_val['image']) || ! $first_val['image'] || stristr( $first_val['image'], 'images.unsplash.com' ) ) $first_val['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($first_val['title']) )."/900_600/blog.jpg";
     if($first_key == 0){ ?>
      <li class="first-news">
        <div class="post-thumbnail tie-appear">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark">
            <img width="310" height="165" src="<?php echo $first_val['image']; ?>" class="attachment-tie-medium size-tie-medium wp-post-image tie-appear" alt="" decoding="async" fetchpriority="high" srcset="<?php echo $first_val['image']; ?> 310w, <?php echo $first_val['image']; ?> 620w" sizes="(max-width: 310px) 100vw, 310px"><span class="fa overlay-icon"></span>
          </a>
        </div><!-- post-thumbnail /-->        
        <h2 class="post-box-title">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><?php echo $first_val['title']; ?></a>
        </h2>       
        <div class="entry">
          <p style="overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; text-overflow: ellipsis;"><?php echo html_entity_decode($first_val['blurb']['decription']); ?> …</p>
          <a class="more-link" href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>">Read More »</a>
        </div>
      </li>
    <?php }else{ ?>
      <li class="other-news">
        <div class="post-thumbnail tie-appear">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark">
            <img width="110" height="75" src="<?php echo $first_val['image']; ?>" class="attachment-tie-small size-tie-small wp-post-image tie-appear" alt="" decoding="async" srcset="<?php echo $first_val['image']; ?> 110w, <?php echo $first_val['image']; ?> 220w, <?php echo $first_val['image']; ?> 330w" sizes="(max-width: 110px) 100vw, 110px"><span class="fa overlay-icon"></span>
          </a>
        </div><!-- post-thumbnail /-->				
        <h3 class="post-box-title">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><?php echo $first_val['title']; ?></a>
        </h3>
      </li>
    <?php } ?>
    <?php } 
  return 1;
}
function taoh_net_releated($net_rand){
  //print_r($net_rand);
  foreach ($net_rand as $first_key => $first_val){ 
    if ( ! isset( $first_val['image']) || ! $first_val['image'] || stristr( $first_val['image'], 'images.unsplash.com' ) ) $first_val['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($first_val['title']) )."/900_600/blog.jpg";
     if($first_key == 0){ ?>
      <li class="first-news">
        <div class="post-thumbnail tie-appear">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark">
            <img width="310" height="165" src="<?php echo $first_val['image']; ?>" class="attachment-tie-medium size-tie-medium wp-post-image tie-appear" alt="" decoding="async" fetchpriority="high" srcset="<?php echo $first_val['image']; ?> 310w, <?php echo $first_val['image']; ?> 620w" sizes="(max-width: 310px) 100vw, 310px"><span class="fa overlay-icon"></span>
          </a>
        </div><!-- post-thumbnail /-->        
        <h2 class="post-box-title">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><?php echo $first_val['title']; ?></a>
        </h2>       
        <div class="entry">
          <p style="overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; text-overflow: ellipsis;"><?php echo html_entity_decode($first_val['blurb']['decription']); ?> …</p>
          <a class="more-link" href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>">Read More »</a>
        </div>
      </li>
    <?php }else{ ?>
      <li class="other-news">
        <div class="post-thumbnail tie-appear">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark">
            <img width="110" height="75" src="<?php echo $first_val['image']; ?>" class="attachment-tie-small size-tie-small wp-post-image tie-appear" alt="" decoding="async" srcset="<?php echo $first_val['image']; ?> 110w, <?php echo $first_val['image']; ?> 220w, <?php echo $first_val['image']; ?> 330w" sizes="(max-width: 110px) 100vw, 110px"><span class="fa overlay-icon"></span>
          </a>
        </div><!-- post-thumbnail /-->				
        <h3 class="post-box-title">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><?php echo $first_val['title']; ?></a>
        </h3>
      </li>
    <?php } ?>
    <?php } 
  return 1;
}
function taoh_stress_releated($stress_rand){
  //print_r($stress_rand);die;
  foreach ($stress_rand as $first_key => $first_val){ 
    if ( ! isset( $first_val['image']) || ! $first_val['image'] || stristr( $first_val['image'], 'images.unsplash.com' ) ) $first_val['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($first_val['title']) )."/900_600/blog.jpg";
     if($first_key == 0){ ?>
      <li class="first-news">
        <div class="post-thumbnail tie-appear">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark">
            <img width="310" height="165" src="<?php echo $first_val['image']; ?>" class="attachment-tie-medium size-tie-medium wp-post-image tie-appear" alt="" decoding="async" fetchpriority="high" srcset="<?php echo $first_val['image']; ?> 310w, <?php echo $first_val['image']; ?> 620w" sizes="(max-width: 310px) 100vw, 310px"><span class="fa overlay-icon"></span>
          </a>
        </div><!-- post-thumbnail /-->        
        <h2 class="post-box-title">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><?php echo $first_val['title']; ?></a>
        </h2>       
        <div class="entry">
          <p style="overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; text-overflow: ellipsis;"><?php echo html_entity_decode($first_val['blurb']['decription']); ?> …</p>
          <a class="more-link" href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>">Read More »</a>
        </div>
      </li>
    <?php }else{ ?>
      <li class="other-news">
        <div class="post-thumbnail tie-appear">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark">
            <img width="110" height="75" src="<?php echo $first_val['image']; ?>" class="attachment-tie-small size-tie-small wp-post-image tie-appear" alt="" decoding="async" srcset="<?php echo $first_val['image']; ?> 110w, <?php echo $first_val['image']; ?> 220w, <?php echo $first_val['image']; ?> 330w" sizes="(max-width: 110px) 100vw, 110px"><span class="fa overlay-icon"></span>
          </a>
        </div><!-- post-thumbnail /-->				
        <h3 class="post-box-title">
          <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" rel="bookmark"><?php echo $first_val['title']; ?></a>
        </h3>
      </li>
    <?php } ?>
    <?php } 
  return 1;
}
function get_tags_list_lp() {
  $url = "core.content.get";
   $taoh_vals = array(
      'token'=> TAOH_API_TOKEN_DUMMY,
      'mod' => 'users',
      'ops' => 'list',
      'type' => 'tags_list',
      'tags' => 'latest',
      'cache_time' => 120,
       //'cfcc5h' => 1 //cfcache newly added
   );
   $cache_name = $url.'_users_tag_list_' . hash('sha256', $url . serialize($taoh_vals));
  //  $taoh_vals[ 'cfcache' ] = $cache_name;
  //  $taoh_vals[ 'cache_name' ] = $cache_name;
  //  $taoh_vals[ 'cache' ] = array ( "name" => $cache_name );
   ksort($taoh_vals);
   
   //echo taoh_apicall_get_debug($url, $taoh_vals);taoh_exit();
   $response_tag = json_decode(taoh_apicall_get($url, $taoh_vals, '', 1), true);
   if(isset($response_tag['output']) && $response_tag['output'] != ""){
    return $response_tag['output'];
   }
   return array();
}

function get_footer_tags_list_lp() {
    $url = "content.get.taglist";
    $taoh_vals = array(
        'secret' => TAOH_API_SECRET,
        'token' => taoh_get_dummy_token(),
        'cache_name' => 'tags_list_footer_' . taoh_get_dummy_token() . '_' . TAOH_TEMP_SITE_FILE_PATH_SECRET,
        'cache_time' => 120,
         //'cfcc1d' => 1 //cfcache newly added
    );
   //echo taoh_apicall_get_debug($url, $taoh_vals);taoh_exit();
   $response_tag = json_decode(taoh_apicall_get($url, $taoh_vals, '', 1), true);
   if(isset($response_tag['output']) && $response_tag['output'] != ""){
    return $response_tag['output'];
   }
   return array();
}
?>
