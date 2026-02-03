<?php
function taoh_get_blog_categories_blog() {
  $api = TAOH_CDN_PREFIX.'/assets/category.php';
  $content = taoh_url_get_content($api);
  if($content != "") {
    $response = json_decode($content);
    return $response;
  }
  return array();
}

function tags_widget() { ?>
  <div class="card card-item light-dark-card">
      <div class="card-body">
          <h3 class="fs-17 pb-3">Reads Categories</h3>
          <div class="divider"><span></span></div>
          <div class="td-post-small-box pt-4 ">
              <?php foreach (taoh_get_blog_categories_blog() as $category) { ?>
              <a href="<?php echo TAOH_READS_URL."/search?q=".$category->slug; ?>&type=category"><?php echo $category->title; ?></a>
            <?php } ?>
          </div>
      </div>
  </div><!-- end card -->
<?php }

function taoh_blog_link($conttoken, $link="") {
  if(!$link) {
    return TAOH_READS_URL."/blog/".$conttoken;
  }
  return $link;
}

function blog_related_post($category = "") {
   //$api = TAOH_API_PREFIX.'/core.content.get?mod=core&ops=related&type=blog&token='.taoh_get_dummy_token().'&category='.$category.'&count=3';
   //$content = file_get_contents($api);
   $url = 'core.content.get';
   $taoh_vals = array(
    "mod" => 'core',
    "ops"=>'related',
    "type"=>'blog',
    'token'=>taoh_get_dummy_token(1),
    "category"=>$category,
    "count" => 3,
    //'cfcc1h'=> 1, //cfcache newly added
  );
  // $cache_name = $url.'_blog_related_' . hash('sha256', $url . serialize($taoh_vals));
  // $taoh_vals[ 'cfcache' ] = $cache_name;
  // $taoh_vals[ 'cache_name' ] = $cache_name;
  // $taoh_vals[ 'cache' ] = array ( "name" => $cache_name, 'ttl' => 3600);
  ksort($taoh_vals);
  
  //echo TAOH_API_PREFIX . '/' .$url.'?'.http_build_query($taoh_vals); taoh_exit();
  $content = taoh_apicall_get($url, $taoh_vals);

    if($content != "") {
      $response = json_decode($content);
      return $response;
    }
  return array();
  //https://preapi.tao.ai/core.content.get?mod=core&ops=related&type=blog&token=y2Ds3ugv&category=mindfulness&count=3
  //$api = TAOH_API_PREFIX./core.content.get?mod=core&ops=related&type=blog&token=y2Ds3ugv&category=mindfulness&count=1
}

function blog_related_widget($category = "") {
  $related = blog_related_post($category);
  if(@$related->success) { ?>
    <div class="card card-item border-bottom mb-3">
      <div class="card-body">
          <h4 class="session_title"><span>RELATED POSTS</span></h4>
          <div class="sidebar-questions pt-3">
            <?php foreach ($related->output as $post) { ?>
              <div class="media media-card media--card media--card-2 light-dark-card">
                  <div class="media-body">
                      <h3 class="h3-title"><a href="<?php echo taoh_blog_link($post->conttoken, @$post->link); ?>">
                        <?php echo ucfirst(taoh_title_desc_decode($post->title)); ?>
                        <?php if(isset( $post->link )) {
                          echo external_link_icon();
                        } ?>
                      </a>
                      </h3>
                      <div class="meta-data">
                          <span class="pr-1">Category: </span>
                            <?php echo $post->category[0]; ?>
                      </div>
                  </div>
              </div><!-- end media -->
            <?php } ?>
          </div><!-- end sidebar-questions -->
      </div>
  </div>
<?php }
}

function blog_related_side_widget($category = "") {
  $related = blog_related_post($category);
  if(@$related->success) { ?>
    <div class="card card-item border-bottom mb-3">
      <div class="card-body">
          <h4 class="session_title"><span>MOST POPULAR</span></h4>
          <div class="sidebar-questions pt-3">
            <?php //print_r($related->output);die();
            foreach ($related->output as $post) {
              if ( ! isset( $post->image[0] ) || ! $post->image[0] || stristr( $post->image[0], 'images.unsplash.com' ) ) $post->image[0] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($post->title) )."/900_600/blog.jpg";
              ?>
              <div class="d-flex mb-3">
                <a class="mt-2 dash_metrics" data-metrics="view" data-type="reads" conttoken="<?php echo $post->conttoken; ?>" href="<?php echo taoh_blog_link(slugify2(taoh_title_desc_decode($post->title))."-".$post->conttoken); ?>">
                <?php if($post->media_type == 'youtube'){ ?>
                    <img width="100%" src="http://img.youtube.com/vi/<?php echo taoh_get_youtubeId($post->media_url); ?>/maxresdefault.jpg" data-src="http://img.youtube.com/vi/<?php echo taoh_get_youtubeId($post->media_url); ?>/maxresdefault.jpg" alt="Card image">  
                  <?php }else{ ?>
                      <img width="100%" src="<?php echo $post->image[0]; ?>" data-src="<?php echo $post->image[0]; ?>" alt="Card image">
                  <?php } ?>
                </a>
                <h3 class="mt-2 sm-title">
                    <a class="" href="<?php echo taoh_blog_link(slugify2(taoh_title_desc_decode($post->title))."-".$post->conttoken); ?>">
                      <?php echo ucfirst(taoh_title_desc_decode($post->title)); ?>
                    </a>
                </h3>
              </div>
            <?php } ?>
          </div><!-- end sidebar-questions -->
      </div>
  </div>
<?php }
}

function external_link_icon() {
  return '<svg style="width: 10px; fill: blue;" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 194.818 194.818" style="enable-background:new 0 0 194.818 194.818;" xml:space="preserve">
<g>
	<path d="M185.818,2.161h-57.04c-4.971,0-9,4.029-9,9s4.029,9,9,9h35.312l-86.3,86.3c-3.515,3.515-3.515,9.213,0,12.728
		c1.758,1.757,4.061,2.636,6.364,2.636s4.606-0.879,6.364-2.636l86.3-86.3v35.313c0,4.971,4.029,9,9,9s9-4.029,9-9v-57.04
		C194.818,6.19,190.789,2.161,185.818,2.161z"/>
	<path d="M149,77.201c-4.971,0-9,4.029-9,9v88.456H18v-122h93.778c4.971,0,9-4.029,9-9s-4.029-9-9-9H9c-4.971,0-9,4.029-9,9v140
		c0,4.971,4.029,9,9,9h140c4.971,0,9-4.029,9-9V86.201C158,81.23,153.971,77.201,149,77.201z"/>
</g>';
}

function blog_search_widget() { ?>
    <form action="/<?php echo TAOH_READS_URL; ?>" method="get" class="pt-4">
              <div class="form-group mb-0">
                  <input class="form-control form--control form--control-bg-gray" value="<?php echo @$_GET['q']; ?>" type="text" name="q" placeholder="Type your search words...">

                  <button class="form-btn" type="submit"><i class="la la-search"></i></button>
              </div>
          </form>
<?php }
function side_widget4(){ 
  $widget1 = array_slice(taoh_central_widget_get(), 0, 5, true);
  $first_get = array_slice($widget1, 0, 1, true);
  $second_get = array_slice($widget1, 1, 4, true); ?> 
    <div class="border-bottom card card-item">
      <div class="card-body">
      <h4 class="session_title"><span>FEATURED</span></h4>
        <div class="mt-3 mb-3">
            <?php foreach ($first_get as $first ){ 
            if ( ! isset( $first['blurb']['image'][0] ) || ! $first['blurb']['image'][0] || stristr( $first['blurb']['image'][0], 'images.unsplash.com' ) ) $first['blurb']['image'][0] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($first['title']) )."/900_600/blog.jpg";
            ?>
          <div class="mb-3">
            <div class="cover-image-container">
              <div class="glass-overlay"></div>
              <?php if($first['blurb']['media_type'] == 'youtube') { ?>
                  <div class="bg-image" style="background-image: url('https://img.youtube.com/vi/<?php echo $first['blurb']['media_url']; ?>/hqdefault.jpg')"></div>
                <?php } else { ?>
                  <div class="bg-image" style="background-image: url('<?php echo $first['blurb']['image'][0]; ?>')"></div>
              <?php } ?>
              <a class="mt-2 dash_metrics" data-metrics="view" data-type="reads" contoken="<?php echo $first['conttoken']; ?>" href="<?php echo taoh_blog_link(slugify2(taoh_title_desc_decode($first['title']))."-".$first['conttoken']); ?>">
                <?php if($first['blurb']['media_type'] == 'youtube') { ?>
                  <img class="main-image" width="100%" src="https://img.youtube.com/vi/<?php echo $first['blurb']['media_url']; ?>/hqdefault.jpg" data-src="https://img.youtube.com/vi/<?php echo $first['blurb']['media_url']; ?>/hqdefault.jpg" alt="Card image">
                <?php } else { ?>
                  <img class="main-image" width="100%" src="<?php echo $first['blurb']['image'][0]; ?>" data-src="<?php echo $first['blurb']['image'][0]; ?>" alt="Card image">
                <?php } ?>
              </a>
            </div>
            <h3  class="mt-2 sm-title">
                <a class="" href="<?php echo taoh_blog_link(slugify2(taoh_title_desc_decode($first['title']))."-".$first['conttoken']); ?>">
                  <?php echo ucfirst(taoh_title_desc_decode($first['title'])); ?>
                </a>
            </h3>
            <div class="mt-2 descrip claimedRight">
              <?php echo html_entity_decode((['blurb']['description'])); ?>
            </div>
          </div>
          <?php } ?>
          <!-- <div class="td-next-prev-wrap">
            <a class="btn p-0"><i class="fas fa-chevron-circle-left fs-30"></i></a>
            <a class="btn p-0"><i class="fas fa-chevron-circle-right fs-30"></i></a>
          </div> -->
        </div>
      </div>
    </div>
<?php }  
function taoh_central_widget_get(){
  $url = 'core.content.get';
  $taoh_vals = array(
    "mod" => 'core',
    "conttype"=> "blog", 
    "type"=> "reads", 
    "ops"=> "list",
    'token'=>taoh_get_dummy_token(1),
    "q"=> '',
    "category"=> '',
    "page"=> 1,
    "perpage" => 10,
    "sort" => 'rand',
   //'cfcc1h'=> 1, //cfcache newly added
  );
  if ( $taoh_vals[ 'q' ] == '' && 0 ){
    //$taoh_vals[ 'cache' ] = array ( "name" => taoh_p2us('core.content').'_blog_list');
  }
  //$taoh_vals[ 'cfcache' ] = hash('sha256', $url . serialize($taoh_vals));
  $req = taoh_apicall_get($url, $taoh_vals);
  $res = json_decode($req, true);
  shuffle($res['output']['list']);
  return $res['output']['list'];
} 
function field_tags($options = "") {
  $str = '<select id="tagsSelect" multiple name="tags[]" placeholder="Type to select">';
  if(@$options) {
    foreach ( $options as $key => $value ){
      $str .= "<option value='$value' selected='selected'>$value</option>";
    }
  }
$str .='</select><script>tagsSelect();</script>';
return $str;
}
?>
