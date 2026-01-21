<?php
function taoh_wellness_widget_get($reads_type){
    $url = 'core.content.get';
    $taoh_vals = array(
      "mod" => 'users',
      "type"=> "landing", // landing 
      "ops"=> $reads_type,
      "blog_type" => 'newsletter',
      'key' => defined('TAOH_EVENTS_GET_LOCAL') && TAOH_EVENTS_GET_LOCAL ? TAOH_API_SECRET : TAOH_API_DUMMY_SECRET,
      'token'=>taoh_get_dummy_token(1),
      //'cfcc5h'=> 1, //cfcache newly added
      
      'local'=>TAOH_READS_GET_LOCAL,
      'cache_time'=>600
    );
    // $cache_name = $url.'_' . $reads_type . '_landing_'. hash('sha256', $url . serialize($taoh_vals));
    // $taoh_vals[ 'cfcache' ] = $cache_name;
    // $taoh_vals[ 'cache_name' ] = $cache_name;
    // $taoh_vals[ 'cache' ] = array ( "name" => $cache_name );
    ksort($taoh_vals);
    
  //https://preapi.tao.ai/core.content.get?mod=users&type=landing&ops=wellness&token=ZBPEKKTn&cache%5Bname%5D=DVrIy1cu_core_content_get_wellness_reads
  //https://preapi.tao.ai/core.content.get?mod=users&type=landing&ops=jobs&token=ZBPEKKTn&cache%5Bname%5D=DVrIy1cu_core_content_get_jobs_reads
  //https://preapi.tao.ai/core.content.get?mod=users&type=landing&ops=wellness&token=ZBPEKKTn&cache%5Bname%5D=DVrIy1cu_core_content_get_wellness_reads

    $req = taoh_apicall_get($url, $taoh_vals, '', 1);
    // echo taoh_apicall_get_debug($url, $taoh_vals);exit();
    $res = json_decode($req, true);
    return $res['output'];
  }

  function taoh_all_reads_widget( $val_arr, $design ){
    switch ( $design ){
      case 'center1':
        taoh_all_central_widget1($val_arr);
        break;
      case 'center2':
        taoh_all_central_widget2($val_arr);
        break;
      case 'center3':
        taoh_all_central_widget3($val_arr);
        break;
      case 'right1':
        taoh_all_right_widget1($val_arr);
        break;
      case 'right2':
        taoh_all_right_widget2($val_arr);
        break;
      case 'right3':
        taoh_all_right_widget3($val_arr);
        break;
      case 'right_ad1':
        taoh_right_ad1($val_arr);
        break;
      case 'right_ad2':
        taoh_right_ad2($val_arr);
        break;
      case 'right_ad3':
        taoh_right_ad3($val_arr);
        break;
      }
    return 1;
  }

  function taoh_all_central_widget3($center3){
  $first_get = array_slice($center3, 0, 3, true); 
?>
<h4 class="ml-3 session_title mt-3"><span>RANDOM READS</span></h4>
  <div class="row p-3 mb-3 border-bottom">
  <?php foreach ($first_get as $first ){ 
          if ( ! isset( $first['image'] ) || ! $first['image'] || stristr( $first['image'], 'images.unsplash.com' ) ) $first['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($first['title']) )."/900_600/blog.jpg";
          ?> 
      <div class="col-md-4">
          <div class="card card-item">
            <a class="dash_metrics" data-metrics="view" data-type="newsletter" conttoken="<?php echo $first['conttoken']; ?>" href="<?php echo taoh_newsletter_link(slugify2($first['title'])."-".$first['conttoken']); ?>">
              <img class="card-img-top" src="<?php echo $first['image']; ?>" data-src="<?php echo $first['image']; ?>" alt="Card image">
            </a>
            <div class="mt-2">
              <h3 class="h3-title">
                <a href="<?php echo taoh_newsletter_link(slugify2($first['title'])."-".$first['conttoken']); ?>">
                  <?php echo ucfirst(taoh_title_desc_decode($first['title'])); ?>
                </a>
              </h3>
              <div class="meta-data">
                  Category: <?php echo $first['category'][0]; ?>
              </div>
            </div>
          </div>
      </div>
      <?php } ?>
    </div>
<?php 
  return 1;
}
function taoh_all_central_widget2($center2){ 
  //$widget2 = taoh_wellness_widget_get($reads_type);
  //$center2 = $widget2['center2'];
  $first_get = array_slice($center2, 0, 2, true);
  $second_get = array_slice($center2, 1, 4, true); ?>
  <h4 class="ml-3 session_title"><span>EDITOR'S PICK</span></h4>
  <div class="row">
    <?php foreach ($first_get as $first ){ 
      if ( ! isset( $first['image'] ) || ! $first['image'] || stristr( $first['image'], 'images.unsplash.com' ) ) $first['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($first['title']) )."/900_600/blog.jpg";
      ?>
    <div class="col-lg-6">
      <div class="p-3">
        <a class="mt-2 dash_metrics" data-metrics="view" data-type="newsletter" conttoken="<?php echo $first['conttoken']; ?>" href="<?php echo taoh_newsletter_link(slugify2($first['title'])."-".$first['conttoken']); ?>">
            <img width="100%" src="<?php echo $first['image']; ?>" data-src="<?php echo $first['image']; ?>" alt="Card image">
        </a>
        <h3  class="mt-2 h3-title">
            <a class="" href="<?php echo taoh_newsletter_link(slugify2(taoh_title_desc_decode($first['title']))."-".$first['conttoken']); ?>">
              <?php echo ucfirst(taoh_title_desc_decode($first['title'])); ?>
            </a>
        </h3>
        <div class="mt-2 descrip">
          <?php echo html_entity_decode($first['short']); ?>
        </div>
      </div>
    </div>
    <?php } ?>
  </div>
<div class="row border-bottom">
  <?php foreach ($second_get as $second ){ 
    if ( ! isset( $second['image'] ) || ! $second['image'] || stristr( $second['image'], 'images.unsplash.com' ) ) $second['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($second['title']) )."/900_600/blog.jpg";
    ?>
  <div class="col-sm-6">
    <div class="d-flex p-3">
      <a class="mt-2 dash_metrics" data-metrics="view" data-type="newsletter" conttoken="<?php echo $second['conttoken']; ?>" href="<?php echo taoh_newsletter_link(slugify2($second['title'])."-".$second['conttoken']); ?>">
          <img width="100" src="<?php echo $second['image']; ?>" data-src="<?php echo $second['image']; ?>" alt="Card image">
      </a>
      <h3 class="mt-2 sm-title">
          <a class="" href="<?php echo taoh_newsletter_link(slugify2(taoh_title_desc_decode($second['title']))."-".$second['conttoken']); ?>">
            <?php echo ucfirst(taoh_title_desc_decode($second['title'])); ?>
          </a>
      </h3>
    </div>
  </div>
  <?php } ?>
</div>
<?php 
  return 1;
} 
function taoh_all_central_widget1($center1){ 
    $first_get = array_slice($center1, 0, 1, true);
    $second_get = array_slice($center1, 1, 4, true); ?>
    <h4 class="ml-3 session_title"><span>WHAT'S NEW</span></h4>
    <div class="row mb-3 border-bottom">
      <?php foreach ($first_get as $first ){ 
        if ( ! isset( $first['image']) || ! $first['image'] || stristr( $first['image'], 'images.unsplash.com' ) ) $first['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($first['title']) )."/900_600/blog.jpg";
        ?>
      <div class="col-lg-6">
        <div class="p-3">
          <a class="mt-2 dash_metrics" data-metrics="view" data-type="newsletter" conttoken="<?php echo $first['conttoken']; ?>" href="<?php echo taoh_newsletter_link(slugify2($first['title'])."-".$first['conttoken']); ?>">
            <img width="100%" src="<?php echo $first['image']; ?>" data-src="<?php echo $first['image']; ?>" alt="Card image">
          </a>
          <h3  class="mt-2 h3-title">
              <a class="" href="<?php echo taoh_newsletter_link(slugify2(taoh_title_desc_decode($first['title']))."-".$first['conttoken']); ?>">
                <?php echo ucfirst(taoh_title_desc_decode($first['title'])); ?>
              </a>
          </h3>
          <div class="mt-2 descrip">
            <?php echo html_entity_decode($first['short']); ?>
          </div>
        </div>
      </div>
      <?php } ?>
      <div class="col-lg-6">
        <div class="p-3">
          <?php foreach ($second_get as $second ){ 
            if ( ! isset( $second['image']) || ! $second['image'] || stristr( $second['image'], 'images.unsplash.com' ) ) $second['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($second['title']) )."/900_600/blog.jpg";
          ?>
          <div class="d-flex mb-3">
            <a class="mt-2 dash_metrics" data-metrics="view" data-type="newsletter" conttoken="<?php echo $second['conttoken']; ?>" href="<?php echo taoh_newsletter_link(slugify2($second['title'])."-".$second['conttoken']); ?>">
                <img width="100" src="<?php echo $second['image']; ?>" data-src="<?php echo $second['image']; ?>" alt="Card image">
            </a>
            <h3 class="mt-2 sm-title">
                <a class="" href="<?php echo taoh_newsletter_link(slugify2(taoh_title_desc_decode($second['title']))."-".$second['conttoken']); ?>">
                  <?php echo ucfirst(taoh_title_desc_decode($second['title'])); ?>
                </a>
            </h3>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
  <?php 
    return 1;
}

function taoh_right_ad1($taoh_right_ad1){ 
    $url = TAOH_SITE_URL_ROOT.$taoh_right_ad1['link']; ?>
        <div class="card-body">
            <h3 class="fs-17 pb-3">
                <?php echo $taoh_right_ad1['title']; ?>
            </h3>
            <div class="sidebar-questions pt-3">
                <div class="media media-card media--card media--card-2">
                    <div class="media-body">
                        <div>
                            <a href="<?php echo $url;?>"><img src="<?php echo $taoh_right_ad1['image']; ?>" /></a>
                        </div>
                        <h5>
                            <a href="<?php echo $url;?>"><?php echo $taoh_right_ad1['subtitle']; ?></a>
                        </h5>
                        <small class="meta">
                        <span class="pr-1">by</span>
                        <a target="_blank" class="author" target="_blank" href="<?php echo $url;?>">
                            #JusASKTheCoach
                        </a>
                        </small>
                    </div>
                </div><!-- end media -->
            </div>
        </div>
  <?php 
  return 1;  
} 

function taoh_right_ad2($taoh_right_ad2){ 
    //$widget5 = taoh_wellness_widget_get($reads_type);
    //$taoh_right_ad2 = $widget5['taoh_right_ad2'];
    $url = TAOH_SITE_URL_ROOT.$taoh_right_ad2['link']; ?>
        <div class="card-body">
            <h3 class="fs-17 pb-3">
                <?php echo $taoh_right_ad2['title']; ?>
            </h3>
            <div class="sidebar-questions pt-3">
                <div class="media media-card media--card media--card-2">
                    <div class="media-body">
                        <div>
                            <a href="<?php echo $url;?>"><img src="<?php echo $taoh_right_ad2['image']; ?>" width="250px" /></a>
                        </div>
                        <h5>
                            <a href="<?php echo $url;?>"><?php echo $taoh_right_ad2['subtitle']; ?></a>
                        </h5>
                        <small class="meta">
                            <span class="pr-1">by</span>
                            <a target="_blank" class="author" target="_blank" href="<?php echo $url; ?>">
                            Obvious Baba
                            </a>
                        </small>
                    </div>
                </div><!-- end media -->
            </div>
        </div>
  <?php 
  return 1;  
} 

function taoh_right_ad3($taoh_right_ad3){ 
    //$widget6 = taoh_wellness_widget_get($reads_type);
    //$taoh_right_ad3 = $widget6['taoh_right_ad3'];
    $url = TAOH_SITE_URL_ROOT.$taoh_right_ad3['link']; ?>
        <div class="card-body">
            <h3 class="fs-17 pb-3">
                <?php echo $taoh_right_ad3['title']; ?>
            </h3>
            <div class="sidebar-questions pt-3">
                <div class="media media-card media--card media--card-2">
                    <div class="media-body">
                        <div>
                            <a href="<?php echo $url;?>"><img src="<?php echo $taoh_right_ad3['image']; ?>" /></a>
                        </div>
                        <h5>
                            <a href="<?php echo $url;?>"><?php echo $taoh_right_ad3['subtitle']; ?></a>
                        </h5>
                        <small class="meta">
                        <span class="pr-1">by</span>
                        <a target="_blank" class="author" target="_blank" href="<?php echo $url;?>">
                            #JusASKTheCoach
                        </a>
                        </small>
                    </div>
                </div><!-- end media -->
            </div>
        </div>
  <?php 
    return 1; 
  } 
  
  function taoh_all_right_widget2($right2) {
  //$related = taoh_wellness_widget_get($reads_type);
  //$right2 = $related['right2'];//print_r($right2); ?>
    <div class="card-body">
        <h4 class="session_title"><span>APPLY TO JOBS</span></h4>
        <div class="sidebar-questions pt-3">
        <?php foreach ($right2 as $post) { ?>
            <div class="media media-card media--card media--card-2">
                <div class="media-body">
                    <h3 class="h3-title"><a href="<?php echo TAOH_SITE_URL_ROOT."/jobs/d/".slugify2($post['title'])."-".$post['conttoken']?>">
                    <?php echo ucfirst(taoh_title_desc_decode($post['title'])); ?>
                    </a>
                    </h3>
                    <?php
                    if ( isset(  $post->category[0] ) ){
                    ?>
                    <div class="meta-data">
                        <span class="pr-1">Category: </span>
                        <?php echo $post->category[0]; ?>
                    </div>
                    <?php
                    }
                    ?>
                </div>
            </div><!-- end media -->
        <?php } ?>
        </div><!-- end sidebar-questions -->
    </div>
<?php 
  return 1;
} 

function taoh_all_right_widget1($related1) { 
$right1 = $related1[0]; 
?> 
    <div class="card-body">
    <h4 class="session_title"><span>RELATED</span></h4>
    <div class="mt-3 mb-3">
        <?php 
        if ( ! isset( $right1['image'] ) || ! $right1['image'] || stristr( $right1['image'], 'images.unsplash.com' ) ) $right1['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($right1['title']) )."/900_600/blog.jpg";
        ?>
        <div class="mb-3">
        <a class="mt-2 dash_metrics" data-metrics="view" data-type="newsletter" conttoken="<?php echo $right1['conttoken']; ?>" href="<?php echo taoh_newsletter_link(slugify2($right1['title'])."-".$right1['conttoken']); ?>">
            <img width="100%" src="<?php echo $right1['image']; ?>" data-src="<?php echo $right1['image']; ?>" alt="Card image">
        </a>
        <h3  class="mt-2 sm-title">
            <a class="" href="<?php echo taoh_newsletter_link(slugify2(taoh_title_desc_decode($right1['title']))."-".$right1['conttoken']); ?>">
                <?php echo ucfirst(taoh_title_desc_decode($right1['title'])); ?>
            </a>
        </h3>
        <div class="mt-2 descrip claimedRight">
            <?php echo $right1['short']; ?>
        </div>
        </div>
    </div>
    </div>
<?php 
  return 1;
} 

function taoh_all_right_widget3($related3) { 
$right3 = $related3[0]; 
?> 
    <div class="card-body">
    <h4 class="session_title"><span>RELATED</span></h4>
    <div class="mt-3 mb-3">
        <?php 
        if ( ! isset( $right3['image'] ) || ! $right3['image'] || stristr( $right3['image'], 'images.unsplash.com' ) ) $right3['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($right3['title']) )."/900_600/blog.jpg";
        ?>
        <div class="mb-3">
        <a class="mt-2 dash_metrics" data-metrics="view" data-type="newsletter" conttoken="<?php echo $right3['conttoken']; ?>" href="<?php echo taoh_newsletter_link(slugify2($right3['title'])."-".$right3['conttoken']); ?>">
            <img width="100%" src="<?php echo $right3['image']; ?>" data-src="<?php echo $right3['image']; ?>" alt="Card image">
        </a>
        <h3  class="mt-2 sm-title">
            <a class="" href="<?php echo taoh_newsletter_link(slugify2(taoh_title_desc_decode($right3['title']))."-".$right3['conttoken']); ?>">
                <?php echo ucfirst(taoh_title_desc_decode($right3['title'])); ?>
            </a>
        </h3>
        <div class="mt-2 descrip claimedRight">
            <?php echo $right3['short']; ?>
        </div>
        </div>
    </div>
    </div>
<?php 
  return 1;
} ?>