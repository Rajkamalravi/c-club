<?php
taoh_lp_get_header();
$conttoken = taoh_parse_url_lp(2);
@$conttoken = array_pop( explode( '-', $conttoken) );
$url = "core.content.get";
$taoh_vals = array(
	'token'=> TAOH_API_TOKEN_DUMMY,
	'mod' => 'core',
	'ops' => 'detail',
	'type' => 'reads',
	'conttoken' => $conttoken,
   'cache_time' => 600,
   //'cfcc1d' => 1 //cfcache newly added
);
// $cache_name = $url.'_detail_reads_' . hash('sha256', $url . serialize($taoh_vals));
// $taoh_vals[ 'cfcache' ] = $cache_name;
// $taoh_vals[ 'cache_name' ] = $cache_name;
// $taoh_vals[ 'cache' ] = array ( "name" => $cache_name );
ksort($taoh_vals);


//echo taoh_apicall_get_debug($url, $taoh_vals);taoh_exit();
$response = json_decode(taoh_apicall_get($url, $taoh_vals,'',1), true);
if(isset($response['output']) && $response['output'] !='')
{
//print_r($response['output']);taoh_exit();
$description = html_entity_decode(urldecode($response['output'][ 'description' ]));
$title = urldecode($response['output'][ 'title' ]);
$categories = $response['output']['category'];
$tags = $response['output']['tags'];
$video_link  = '';

if($response['output']['media_type'] == 'youtube'){
$video_link = @$response['output']['media_url'] ? $response['output']['media_url'] : "";
}else{
$image = isset($response['output']['media_url']) ? $response['output']['media_url'] : "";
}
$date = $response['output']['created'];
if ( ! isset( $image ) || ! $image || stristr( $image, 'images.unsplash.com' ) ) $image = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($title) )."/900_600/blog.jpg";


$related_posts = ""; //$related['output;
//Missing fields
$author = ucwords($response['output']['author']['chat_name']);
$profile_picture = $response['output']['author']['avatar'];
$ptoken = $response['output']['author']['ptoken'];

$response_tags = get_tags_list_lp();
$new_rand = blog_lp_related_post('',5);
if($new_rand['success'] && $new_rand['output']['list']) {
   $trending_bar = $new_rand['output']['list'];
   $hero = $new_rand['output']['list'];
   $items = array();
   foreach($trending_bar as $username) {
   $items[] = "<a href='".taoh_lp_blog_link(slugify2($username['title'])."-".$username['conttoken'])."' title='".$username['title']."'>".$username['title']."</a>";
   }
   $getjstitle = json_encode($items);
}
}
?>
<style>
   #related_posts .related-item {
      /* float: left; */
      width: 100%;
      margin: 0 3% 10px 0;
   }
</style>
<div id="breaking-news" class="breaking-news">
   <span class="breaking-news-title"><i class="fa fa-bolt"></i> <span>Breaking News</span></span>
   <ul class="innerFade" style="position: relative; height: 31.2px;">
      <li id="changeText"></li>
   </ul>
</div>
<!-- .breaking-news -->
<div id="main-content" class="container">
   <div class="content">
      <article class="post-listing post-1080 post type-post status-publish format-standard has-post-thumbnail  category-impact-measurement category-mental-health category-nonprofit-management" id="the-post">
         <div class="single-post-thumb">
            <img width="660" height="330" src="<?php echo $image; ?>" class="attachment-slider size-slider wp-post-image tie-appear" alt="" decoding="async" fetchpriority="high">
         </div>
         <div class="post-inner">
            <h1 class="name post-title entry-title"><span itemprop="name"><?php echo $title; ?></span></h1>
            <!-- <p class="post-meta">
               <span class="post-cats"><i class="fa fa-folder"></i><a href="https://grants.club/category/impact-measurement/" rel="category tag">Impact Measurement</a>, <a href="https://grants.club/category/mental-health/" rel="category tag">Mental Health</a>, <a href="https://grants.club/category/nonprofit-management/" rel="category tag">Nonprofit Management</a></span>
               <span class="post-views"><i class="fa fa-eye"></i>4 Views</span>
            </p> -->
            <div class="clear"></div>
            <div class="entry">
               <!-- .share-post -->
               <p><?php echo $description; ?></p>
            </div>
            <!-- .entry /-->
            <!-- .share-post -->
            <div class="clear"></div>
         </div>
         <!-- .post-inner -->
      </article>
      <!-- .post-listing -->
      <!-- #comments -->
   </div>
   <!-- .content -->
   <aside id="sidebar" style="position: relative; overflow: visible; box-sizing: border-box; min-height: 1px;">
      <?php taoh_side_bar(); ?>
      <section id="related_posts">
         <div class="block-head">
            <h3>Related Articles</h3>
            <div class="stripe-line"></div>
         </div>
         <div class="post-listing">
            <?php $related = blog_lp_related_post($tags, 3);
               if($related['success'] && $related['output']['list']) {
                  taoh_releated_widget1($related['output']['list']);
               }
            ?>
            <div class="clear"></div>
         </div>
      </section>
   </aside>
   <!-- #sidebar /-->
   <div class="clear"></div>
</div>
<!-- .container /-->
<script>
   $(document).ready(function(){
      $('.widget-top ul li').click(function(){
         $('.widget-top ul li').removeClass('active');
         $(this).addClass('active');
         var tab = $(this).find('a').attr('data-href');
         $('.tabs-wrap').hide();
         $(tab).show();
      });
   });

   //Trending Bar Start
   var text = <?php echo $getjstitle ?>;
      var counter = 0;
      var elem = document.getElementById("changeText");
      var inst = setInterval(change, 3000);
      function change() {
         elem.innerHTML = text[counter];
         //$('#changeText').val(text[counter]);
         counter++;
         if (counter >= text.length) {
            counter = 0;
            // clearInterval(inst); // uncomment this if you want to stop refreshing after one cycle
         }
      }
   //Trending Bar End
</script>
<?php
   taoh_lp_get_footer();
?>