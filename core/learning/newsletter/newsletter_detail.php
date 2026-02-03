<?php
$reads_type = 'hires';
$get_widget = taoh_wellness_widget_get($reads_type);

$conttoken = taoh_parse_url(3);
@$conttoken = array_pop( explode( '-', $conttoken) );
if ( strlen( $conttoken ) < 10 || strlen( $conttoken ) > 20 || ! ctype_alnum( $conttoken ) ){ taoh_redirect( TAOH_SITE_URL_ROOT."/404" ); exit(); }
$share_link = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$url = "core.content.get";
$taoh_vals = array(
	'token'=> taoh_get_dummy_token(1),
	'mod' => 'core',
	'ops' => 'detail',
	'type' => 'newsletter',
	'conttoken' => $conttoken,
	'cache_time'=>600,
  //'cfcc1d'=> 1, //cfcache newly added
);
// $cache_name = $url.'_newsletter_' . hash('sha256', $url . serialize($taoh_vals));
// $taoh_vals[ 'cfcache' ] = $cache_name;
// $taoh_vals[ 'cache_name' ] = $cache_name;
// $taoh_vals[ 'cache' ] = array ( "name" => $cache_name );
ksort($taoh_vals);

//$taoh_vals['cache']['name'] = taoh_get_dummy_token(1).'_'.taoh_p2us($url).'_'.$conttoken.'_newsletter_details';
//echo $taoh_vals['cache']['name'];exit();
//print_r($taoh_vals);taoh_exit();
// echo taoh_apicall_get_debug($url, $taoh_vals);taoh_exit();
$response = json_decode(taoh_apicall_get($url, $taoh_vals, '', 1), true);
//echo taoh_apicall_get_debug($url, $taoh_vals);taoh_exit();
//print_r($response);taoh_exit();
//$locn = TAOH_SITE_CONTENT_GET."/?mod=core&ops=detail&type=blog&conttoken=".$conttoken."&token=".taoh_get_dummy_token();
//echo $locn;taoh_exit();
//$response = json_decode(taoh_url_get_content($locn), true);
if(!$response['success'] && !isset($response['output']) && !$response['output']){
  taoh_redirect( TAOH_SITE_URL_ROOT."/learning/newsletter" ); taoh_exit();
}
$response['output'][ 'description' ] = urldecode($response['output'][ 'description' ]);
$response['output'][ 'title' ] = urldecode($response['output'][ 'title' ]);

$success = $response['success'];
$title = $response['output']['title'];
$categories = $response['output']['category'];
$description = $response['output']['description'];
$video_link  = '';

if($response['output']['media_type'] == 'youtube'){
$video_link = @$response['output']['media_url'] ? $response['output']['media_url'] : "";
}else{
$image = $response['output']['media_url'] ? $response['output']['media_url'] : "";
}
$date = $response['output']['created'];
if ( ! isset( $image ) || ! $image || stristr( $image, 'images.unsplash.com' ) ) $image = TAOH_CDN_PREFIX."/images/igcache/".urlencode( $title )."/900_600/blog.jpg";


$related_posts = ""; //$related['output;
//Missing fields
$author = ucwords($response['output']['author']['chat_name']);
$profile_picture = $response['output']['author']['avatar'];
$ptoken = $response['output']['author']['ptoken'];

$taoh_user_vars = taoh_user_all_info();
$user_ptoken = $taoh_user_vars->ptoken;

//print_r($response['output']);taoh_exit();

/*
Configure for Google Search Console
*/
// TAO_PAGE_AUTHOR
define('TAO_PAGE_AUTHOR', $author);
// TAO_PAGE_DESCRIPTION
define('TAO_PAGE_DESCRIPTION', substr(strip_tags($description),0,240));
// TAO_PAGE_IMAGE
define('TAO_PAGE_IMAGE', $image);
define('TAO_PAGE_AUTHOR_IMAGE',TAOH_OPS_PREFIX."/avatar/PNG/128/".$profile_picture.".png");

// TAO_PAGE_TITLE
define('TAO_PAGE_TITLE', $title);

define( 'TAO_PAGE_TYPE', 'newsletter' );
// TAO_PAGE_TWITTER_SITE
// TAO_PAGE_ROBOT
define ( 'TAO_PAGE_ROBOT', 'index, follow' );
$additive = '';
if ( isset( $response['output'][ 'source' ] )  && mb_strtolower( $response['output'][ 'source' ] ) != mb_strtolower( TAOH_SITE_URL ) ){
	$additive = '<link rel="canonical" href="'.$response['output'][ 'source' ].'/hires/learning/newsletter/d/'.slugify2($title)."-".$conttoken.'"/>
	<meta name="original-source" content="'.$response['output'][ 'source' ].'/hires/learning/newsletter/d/'.slugify2($title)."-".$conttoken.'" />';
	// TAO_PAGE_CANONICAL
	define ( 'TAO_PAGE_CANONICAL', $additive );
}

$click_view = (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) ? 'click' : 'view';
$log_nolog_token = ( taoh_user_is_logged_in()) ? $user_ptoken : TAOH_API_TOKEN_DUMMY;

/* check liked or not */
$taoh_call = "system.users.metrics";
$taoh_vals = array(
    'mod' => 'system',
    'token' => taoh_get_dummy_token(),
    'conttoken' => $conttoken,
    'slug' => TAO_PAGE_TYPE,
);
//echo taoh_apicall_get_debug($taoh_call, $taoh_vals);exit();
$get_liked = json_decode( taoh_apicall_get($taoh_call, $taoh_vals), true );
$userliked_already = isset($get_liked['userliked'])?$get_liked['userliked']:'0';
/* End check liked or not */

taoh_get_header($additive); 
include 'reads_css.php';

?>
  <!--Main Navigation-->
<style>
.td-post-small-box a
{
  background-color: #a5a5a5;
    margin: 2px;
    padding: 3px 3px 3px 3px;
    color: #fff;
    /* display: inline-block; */
    font-size: 11px;
    text-transform: uppercase;
    line-height: 1;
}
.menu--main {
  display: block;
  bottom: 0;
}

  .menu--main li {
    display: inline-block;
    position: relative;
    cursor: pointer;
    
	
  }
  .menu--main>li:hover {
      background-color: #218838;
	  border-color: #1e7e34;
    }


    .menu--main li:hover .sub-menu {      
      max-height: 300px;
      visibility: visible;
      bottom: 100%;  /* align to top of parent element*/
      transition: all 0.4s linear;
    }
	
  
  .sub-menu {
    display: block;
    visibility: hidden;
    position: absolute;
    /*top: 100%;   align to bottom of*/
    left: 0;
    box-shadow: none;
    max-height: 0;
    width: 150px;
    overflow: hidden;
	background-color: whitesmoke;
	white-space: nowrap;
  }
  
.sub-menu li {
	display: block;
	padding: 10px 0px;
}

.sub-menu li a{
	color: black;
}
.menu-video-main {
	margin-left: 20px;
}
@media (min-width: 1280px){
.container {
    max-width: 1021px;
}
}
.adddiv .card-body{
		height: 60vh;
		overflow-y: auto;
	}

</style>
<header class="sticky-top bg-white border-bottom border-bottom-gray">
<section class="hero-area bg-white shadow-sm overflow-hidden pt-5px pb-5px">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-9">
        <div class="hero-content">
					<div class="media media-card align-items-center shadow-none p-1 pt-3 pb-3 mb-0 rounded-0 light-dark-card">
						<div class="media-body">
              <div style="display: flex; white-space: nowrap;">    
                <h4> <?php echo $response['output']['title']; ?></h4>
              </div>
              <?php if ( taoh_user_is_logged_in()) { ?>
                <div class="mt-2" style="display: flex;">
                  <a class="text-center like_html" style="font-size: 20px;margin: 0 10px;"></a>
                  <a class="text-center" style="font-size: 20px;margin: 0 10px;"><i title="Comment" class="la la-comment-o ml-1 comment_go" style="cursor:pointer;"></i>&nbsp;<?php if (TAOH_METRICS_COUNT_SHOW) { ?><span id="commentCount" class="badge text-dark fs-14 p-0"></span><?php } ?></a>
                  <a class="text-center" data-toggle="modal" data-target="#exampleModal1" style="font-size: 20px;margin: 0 10px;"><i title="Share" class="la la-share ml-1 text-primary" style="cursor:pointer;"></i>&nbsp;<?php if (TAOH_METRICS_COUNT_SHOW) { ?><span id="shareCount" class="badge text-dark fs-14 p-0"></span><?php } ?></a>
                  <?php if ( TAOH_METRICS_EYE_SHOW) { ?><a class="text-center" style="font-size: 20px;margin: 0 10px;"><i title="View" class="la la-eye text-primary"></i>&nbsp;<?php if (TAOH_METRICS_COUNT_SHOW) { ?><span id="viewCount" class="badge text-dark fs-14 p-0"></span><?php } ?></a><?php } ?>
                </div>
              <?php } ?>			
						</div>
					</div>
        </div><!-- end hero-content -->
      </div><!-- end col-lg-9 -->
    </div><!-- end row -->
  </div><!-- end container -->
</section>
</header>

  <!--Main layout-->
  <section class="bg-gray pb-40px pt-40px">
    <div class="container bg-white">
      <div id="intro" class="pt-3">
        <ul class="breadcrumb-list pb-2">
            <li><a href="<?php echo TAOH_SITE_URL_ROOT; ?>">Home</a><span><svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"/></svg></span></li>
            <li><a href="<?php echo TAOH_NEWSLETTER_URL; ?>">Newsletter</a><span><svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"/></svg></span></li>
            <li><?php echo @$title; ?></li>
        </ul>
        <!-- <h1 class="mb-0 text-left"><?php //echo TAO_PAGE_TITLE ?></h1> -->
      </div>
      <section class="border-bottom">
            <?php if(isset( $video_link ) && $video_link != '') {
                if(is_stream_link($video_link)) {?>
                <iframe src="<?php echo play_url($video_link); ?>" class="rounded-5 shadow-1-strong me-2" width="100%" height="350px"></iframe>
                <?php } else {?>
                <p>Media Link: <a href="<?php echo $video_link; ?>"><?php echo $video_link; ?></a></p>
                <?php } ?>
                <?php
                } else {
                    echo "<div><img src=\"".TAO_PAGE_IMAGE."\" width=100%></div>";
                }
                if ( isset($response['output'][ 'link' ]) ){
                    echo "<h3><a href=\"".$response['output']['link']."\">Read Here</a></h3>";
                }
            ?>
            <!--<div class="td-post-template-5">
              <header>
                <div style="display: flex;">
                  <a target="_blank" href="#" style="font-size: 20px;margin: 0 10px;"> <i class="la la-thumbs-up ml-1"></i></a>
                  <a data-toggle="modal" data-target="#myModal" href="#" style="font-size: 20px;margin: 0 10px;"><i class="la la-comment-o ml-1"></i></a>
                  <a target="_blank" href="#" style="font-size: 20px;margin: 0 10px;"><i class="la la-share ml-1"></i></a>
              </div>
              </header>
            </div> -->

            <div class="row mt-4 mb-4">
                <div class="col-lg-6 text-lg-start mb-3 m-lg-0 pt-2">
                  <img src="<?php echo TAO_PAGE_AUTHOR_IMAGE; ?>" class="rounded-5 shadow-1-strong me-2"
                    height="35" alt="" loading="lazy" />
                  <span> Published by</span>
                  <a href="" class="text-dark"><?php echo TAO_PAGE_AUTHOR ?></a>
                </div>
                <div class="col-lg-6 text-center mb-3 m-lg-0 pt-2">
                  <span> Category :</span>
                  <a href="<?php echo TAOH_READS_URL."/search?q=".$categories[0]; ?>&type=category" class="text-dark"><?php echo $categories[0]; ?></a>
                </div>
            </div>
          </section>
      <!--Grid row-->
      <div class="row">
        <!--Grid column-->
        <div class="col-md-8 mb-4">
          <!--Section: Post data-mdb-->
          <!--Section: Post data-mdb-->

          <!--Section: Text-->
          <section class="td-post-content">
            
            <p>
              <?php echo html_content($description) ?>
            </p>

            
          </section>

          <section class="border-bottom mb-4 pb-4 mt-t pt-4">
            <div class="row">
              <div class="col-3">
                <img src="<?php echo TAO_PAGE_AUTHOR_IMAGE; ?>" class="img-fluid shadow-1-strong rounded-5" alt="">
              </div>

              <div class="col-9 td-post-content">
                <h5 class="mb-2"><strong><?php echo TAO_PAGE_AUTHOR; ?></strong></h5>
                <a href="https://www.facebook.com/theworktimes" class="text-dark" target="_blank"><i class="fab fa-facebook-f me-1"></i></a>
                <a href="https://twitter.com/theworktimes" class="text-dark" target="_blank"><i class="fab fa-twitter me-1"></i></a>
                <a href="https://www.linkedin.com/company/theworktimes" class="text-dark" target="_blank"><i class="fab fa-linkedin me-1"></i></a>
                <p>
                Your source for engaging, insightful learning and development trends. Managed by experienced editorial teams for top-notch industry information.
                </p>
              </div>
            </div>
          </section>
          <!--Section: Text-->

          <section class="border-bottom pb-4 mb-4">
                <?php tags_widget(); ?>
            </section>

            <section class="hero-area bg-white shadow-sm overflow-hidden" id="scroll_id" style="display:none;">
              <div class="container">
                  <div class="hero-content d-flex flex-wrap align-items-center justify-content-between">
                <div class="comment-tags">
                  <div class="" id="collapseExample">
                    <div class="card-body">
                      <section class="mb-3 mt-3">
                        <?php echo taoh_comments_widget(array('conttoken'=> $conttoken, 'conttype'=> 'newsletter', 'label'=> 'Comment')); ?>
                      </section>
                    </div>
                  </div>
                </div>
                  </div><!-- end hero-content -->
              </div><!-- end container -->
          </section><!-- end hero-area -->
        <?php if(taoh_user_is_logged_in() ) { ?>
          <!--Section: Comments-->
          <!--Section: Reply-->
          <?php $related = newsletter_related_post($categories[0]);
                if($related->success) {
          ?>
          <section>
          <h2 class="section-title fs-30">Related posts</h2> 
            <div class="row">
              <?php foreach ($related->output as $post ){ 
                if ( ! isset( $post->image[0] ) || ! $post->image[0] || stristr( $post->image[0], 'images.unsplash.com' ) ) $post->image[0] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( $post->title )."/900_600/blog.jpg";
                ?>
              <div class="col-lg-4">
                <div class="p-3">
                  <a class="mt-2" href="<?php echo taoh_newsletter_link($post->conttoken); ?>">
                      <img width="100%" src="<?php echo $post->image[0]; ?>" data-src="<?php echo $post->image[0]; ?>" alt="Card image">
                  </a>
                  <h3  class="mt-2 h3-title">
                      <a class="" href="<?php echo taoh_newsletter_link($post->conttoken); ?>">
                        <?php echo ucfirst(taoh_title_desc_decode($post->title)); ?>
                      </a>
                  </h3>
                  <div class="mt-2 descrip">
                    <?php //echo substr(str_replace("\\","",html_entity_decode($first['blurb']['description'])),0,100); ?>
                  </div>
                </div>
              </div>
              <?php } ?>
            </div>
          </section>
          <?php } } ?>
        </div>
        <!--Grid column-->

        <!--Grid column-->
        <div class="col-md-4 mb-4 border-left" style="z-index: 999;">
          <!--Section: Sidebar-->
          <section class="sticky-top" style="top: 80px;">
          <?php if(taoh_user_is_logged_in() ) { 
            if($taoh_user_vars->ptoken == $ptoken){ ?>
            <section class="border-bottom pb-4 mb-4">
              <div class="card-body">
                <div class="d-flex">
                  <div class="ml-2">
                    <a href="<?php echo TAOH_SITE_URL_ROOT."/learning/newsletter/edit/".$conttoken; ?>" target="_blank" class="btn btn-outline-primary" style="border-radius: 15px;">Edit Newsletter</a>
                  </div>
                  <!-- <div class="ml-5">
                    <a class="btn btn-outline-danger" onclick="blogDelete();" style="border-radius: 15px;">Delete Blog</a>
                  </div> -->
                </div>
              </div>
            </section>
          <?php } } ?>
            <section class="border-bottom pb-4 mb-4">
              <?php // taoh_all_reads_widget( $get_widget[ 'right2' ], 'right2' ); ?>
              <?php if (function_exists('taoh_get_recent_jobs')) { taoh_get_recent_jobs('new');  } ?>
            </section>
            <section class="pb-4 mb-4">
                <?php side_widget4(); ?>
            </section>
            <!--Section: Ad-->
            <?php taoh_copynshare_widget(); ?>


          </section>
          <!--Section: Sidebar-->
        </div>
        <!--Grid column-->
      </div>
      <!--Grid row-->
    </div>
  </section>
  <div class="modal fade" id="deleteAlert" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
        <button type="button" style="padding:0" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
		
      </div>
      <div class="modal-body">
        Are you sure, Do you want to delete?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="deleteConfirm()">Yes, I want to delete</button>
      </div>
    </div>
  </div>
</div>
<div class="modal" id="exampleModal1" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content light-dark-card">
      <div class="modal-header light-dark-card">
        <h5 class="modal-title">Share</h5>
      </div>
      <div class="modal-body">
        <section class="mb-3 mt-3">
          <?php echo taoh_share_widget(array('share_data'=> $share_link,'conttoken'=> $conttoken,'conttype'=> 'newsletter','ptoken'=>$user_ptoken)); ?>
        </section>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
  <!--Main layout-->
<script type="text/javascript">
  let arr_cont = [];
  let conttoken = '<?php echo $conttoken; ?>';
  let like_min = '<?php echo TAOH_SOCIAL_LIKES_THRESHOLD; ?>';
  let app_slug = '<?php echo TAO_PAGE_TYPE; ?>';
	let is_local = localStorage.getItem(app_slug+'_'+conttoken+'_liked');

  $(document).ready(function(){
		save_metrics('newsletter','<?php echo $click_view ?>',conttoken);  
		
	});
  if((<?php echo $userliked_already ?>) || (is_local)){
		var like_html = `<i title="Like" class="la la-heart text-danger ml-1"></i>&nbsp;<?php if (TAOH_METRICS_COUNT_SHOW) { ?><span id="likeCount" class="badge text-dark fs-14 p-0"></span><?php } ?>`;
	}else{
		var like_html = `<i title="Like" class="la la-heart text-gray ml-1 remove newsletters_like" style="cursor:pointer;"></i>&nbsp;<?php if (TAOH_METRICS_COUNT_SHOW) { ?><span id="likeCount" class="badge text-dark fs-14 p-0"></span><?php } ?>`;
	}
	$('.like_html').html(like_html);


  $("div").removeClass("card card-item");
  $(".adddiv").addClass("card card-item");
  $('.claimedRight').each(function (f) {

var newstr = $(this).text().substring(0,250)+'....';
$(this).text(newstr);

});


function blogDelete() {
	$('#deleteAlert').modal('show');
}

function deleteConfirm(){
    var data = {
        'action': 'blog_delete',
        'ops': 'delete',
        'conttoken': conttoken,
    };
    jQuery.post("<?php echo TAOH_ACTION_URL .'/blog'; ?>", data, function(response) {
      console.log(response.success);
      if(response.success){
        $('#deleteAlert').modal('hide');
        location.href = '<?php echo TAOH_READS_URL; ?>';
        
      }
      else{console.log('false');
        $('#deleteAlert').modal('hide');      
      }
    }).fail(function() {
        console.log( "Network issue!" );
    })
}

$(document).on('click','.newsletters_like', function(event) {
    var likes = $('#likeCount').html();
		var count_like = (likes==''?0:parseInt(likes)) + parseInt(1);
    $('#likeCount').html(count_like > like_min ? (count_like):'');
    $('.la-heart').removeClass('text-gray').addClass('text-danger').removeClass('newsletters_like');
    $(".remove").removeAttr("onclick");
    $(".remove").removeAttr("style");
    //setCookie('blog_'+conttoken+'_liked',1,1);
    localStorage.setItem(app_slug+'_'+conttoken+'_liked',1);
    save_metrics('newsletter','like',conttoken);
		
	});

  $(document).on('click','.click_metrics', function(event) {
        var metrics = $(this).attr("data-metrics");
        save_metrics('newsletter',metrics,conttoken);  
        
        if(metrics == 'comment_click'){
            $('.command_form').attr('action','<?php echo TAOH_ACTION_URL .'/comments'; ?>');
            $('.command_form').submit();
        }
    });


  $(".comment_go").click(function() {
		$("#scroll_id").show();
		$('html, body').animate({
			scrollTop: $("#scroll_id").offset().top
		}, 2000);
	});
</script>
<?php taoh_get_footer(); ?>