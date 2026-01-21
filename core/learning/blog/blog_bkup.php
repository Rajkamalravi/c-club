<?php taoh_get_header();  

 //"perpage"=>10 is not working
 $query = array("mod"=>"core", "conttype"=>"blog", "type"=>"blog", "ops"=>"list");
 $query['token'] = taoh_get_dummy_token(1);
 //print_r($query);taoh_exit();
 if(@$_GET['q']) { $query['q'] = $_GET['q']; }
 if(@$_GET['cat']) { $query['category'] = $_GET['cat']; }
 if(@$_GET['page']) { $query['page'] = $_GET['page']; }
 
 $params = http_build_query($query);

function pagination() {
    $total = 320;
    $perpage = 10;
    $items = ceil($total/$perpage);
}
//$locn = TAOH_API_PREFIX."/core.content.get?".$params;
//echo $locn;taoh_exit();
//$req = file_get_contents($locn); 

// Start Featured Blog

$url = 'core.content.get';
$taoh_vals = array(
  "mod" => 'core',
  "conttype"=> "blog", 
  "type"=> "blog", 
  "ops"=> "list",
  "token"=> taoh_get_dummy_token(1),
  "q"=> '',
  "category"=> '',
  "page"=> 1,
  "perpage" => 5,
  "sort" => 'rand',
);

if ( $taoh_vals[ 'q' ] == '' ){
  //$taoh_vals[ 'cache' ] = array ( "name" => TAOH_API_TOKEN.'_'.taoh_p2us($url).'_blog_list');
  
}
//$taoh_vals[ 'cfcache' ] = hash('sha256', $url . serialize($taoh_vals));
$req = taoh_apicall_get($url, $taoh_vals);
$res = json_decode($req, true);
$total = $res['output']['count'];
$headlist = $res['output']['list'];
//print_r($headlist);taoh_exit();
// Ends Featured Blog

$url = 'core.content.get';
$taoh_vals = array();
$taoh_vals = array(
  "mod" => 'core',
  "conttype"=>"blog", 
  "type"=>"blog", 
  "ops"=>"list",
  "token"=>taoh_get_dummy_token(1),
  "q"=> ( isset( $_GET[ 'q' ] ) ) ? $_GET[ 'q' ]:'',
  "category"=> ( isset( $_GET[ 'cat' ] ) ) ? $_GET[ 'cat' ]:1,
  "page"=> ( isset( $_GET[ 'page' ] ) ) ? $_GET[ 'page' ]:1,
  "perpage" => ( isset( $_GET[ 'perpage' ] ) ) ? $_GET[ 'perpage' ]:10,
  "sort" => ( isset( $_GET[ 'sort' ] ) ) ? $_GET[ 'sort' ]:'asc',
);

if ( $taoh_vals[ 'q' ] == '' ){
  //$taoh_vals[ 'cache' ] = array ( "name" => TAOH_API_TOKEN.'_'.taoh_p2us($url).'_blog_list');
}
//$taoh_vals[ 'cfcache' ] = hash('sha256', $url . serialize($taoh_vals));
$req = taoh_apicall_get($url, $taoh_vals);
$res = json_decode($req, true);
$total = $res['output']['count'];
$list = $res['output']['list'];

$items = array();
foreach($list as $username) {
 $items[] = $username['title'];
}
$getjstitle = json_encode($items);

$query = [];
$head_type = "Read to succeed";

$mod = "reads";
if ( defined( 'TAOH_API_TOKEN' ) ){
	$ctype = 'token';
	$code = TAOH_API_TOKEN;
} else {
	$ctype = 'secret';
	$code = TAOH_API_SECRET;
}

if (isset($_GET['q']) && $_GET['q'] == ''){
	$q = urlencode($_GET['q']);
}

if (isset($_GET['type']) && $_GET['type'] != ''){
	$type = urlencode($_GET['type']);
}

?>
<style>
    /*body{margin-top:20px;}*/
.blog-listing {
    padding-top: 30px;
    padding-bottom: 30px;
}
.gray-bg {
    background-color: #f5f5f5;
}
/* Blog 
---------------------*/


/* Blog Sidebar
-------------------*/
.blog-aside .widget {
  box-shadow: 0 0 30px rgba(31, 45, 61, 0.125);
  border-radius: 5px;
  overflow: hidden;
  background: #ffffff;
  margin-top: 15px;
  margin-bottom: 15px;
  height: 100%;
  display: inline-block;
  vertical-align: top;
}
.blog-aside .widget-body {
  padding: 15px;
}
.blog-aside .widget-title {
  padding: 15px;
  border-bottom: 1px solid #eee;
}
.blog-aside .widget-title h3 {
  font-size: 20px;
  font-weight: 700;
  color: #fc5356;
  margin: 0;
}
.blog-aside .widget-author .media {
  margin-bottom: 15px;
}
.blog-aside .widget-author p {
  font-size: 16px;
  margin: 0;
}
.blog-aside .widget-author .avatar {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  overflow: hidden;
}
.blog-aside .widget-author h6 {
  font-weight: 600;
  color: #20247b;
  font-size: 22px;
  margin: 0;
  padding-left: 20px;
}
.blog-aside .post-aside {
  margin-bottom: 15px;
}
.blog-aside .post-aside .post-aside-title h5 {
  margin: 0;
}
.blog-aside .post-aside .post-aside-title a {
  font-size: 18px;
  color: #20247b;
  font-weight: 600;
}
.blog-aside .post-aside .post-aside-meta {
  padding-bottom: 10px;
}
.blog-aside .post-aside .post-aside-meta a {
  color: #6F8BA4;
  font-size: 12px;
  text-transform: uppercase;
  display: inline-block;
  margin-right: 10px;
}
.blog-aside .latest-post-aside + .latest-post-aside {
  border-top: 1px solid #eee;
  padding-top: 15px;
  margin-top: 15px;
}
.blog-aside .latest-post-aside .lpa-right {
  height: 90px;
}
.blog-aside .latest-post-aside .lpa-right img {
  border-radius: 3px;
}
.blog-aside .latest-post-aside .lpa-left {
  padding-right: 15px;
}
.blog-aside .latest-post-aside .lpa-title h5 {
  margin: 0;
  font-size: 15px;
}
.blog-aside .latest-post-aside .lpa-title a {
  color: #20247b;
  font-weight: 600;
}
.blog-aside .latest-post-aside .lpa-meta a {
  color: #6F8BA4;
  font-size: 12px;
  text-transform: uppercase;
  display: inline-block;
  margin-right: 10px;
}

img {
    max-width: 95%;
}
img {
    vertical-align: middle;
    border-style: none;
}
.title-hover:hover {
    opacity: 0.5;
    filter: alpha(opacity=50);
}
@media screen and (min-width: 800px) {
   #changeText{
      text-align:left;
   }
}
.blog-news{
    text-align: center;
    background: #f43d2a;
    font-size: 16px;
    color:white;
    border-radius:10px;
}
.alt-title{
  position: absolute;
  padding: 28px;
  text-align:left;
}
@media only screen and (max-device-width: 480px) { 
  .alt-title{
    padding: 10px;
    font-size: 10px;
  }
  .blog-news{
    margin: 0% 20%;
  }
}
.circle img:hover {
  -webkit-transform: scale(0.8);
  -ms-transform: scale(0.8);
  transform: scale(0.8);
  transition: 1s ease;
}
</style>
<section class="hero-area pattern-bg-2 bg-white shadow-sm overflow-hidden pt-30px pb-30px">
    <div class="container">
			<div class="hero-content">

  <div class="row mb-2">
    <div class="p-4 p-md-5 mb-4 rounded bg-dark" style="background-image: url('<?php echo $headlist[0]['blurb']['image'][0]; ?>'); background-size: cover; opacity: 0.6;">
      <div class="col-md-6 px-0">
        <h1 class="display-4 bold-italic text-white"><?php echo ucwords($headlist[0]['title']); ?></h1>
        <p class="lead my-3 text-white"><?php echo ( isset( $headlist[0]['blurb']['description'] ) ) ? str_replace("\\","",substr(html_entity_decode($headlist[0]['blurb']['description']), 0, 150)). "...":''; ?></p>
        <p class="lead mb-0 text-white"><a href="<?php echo taoh_blog_link($headlist[0]['conttoken']); ?>#" class="text-white fw-bold">Continue reading...</a></p>
      </div>
    </div>
    <div class="col-md-6">
      <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative" style="background-image: url('<?php echo $headlist[1]['blurb']['image'][0]; ?>'); background-size: cover; opacity: 0.6;">
        <div class="col p-4 d-flex flex-column position-static">
          <strong class="d-inline-block mb-2 text-primary"><?php $headlist[1]['category'][0] ?></strong>
          <h3 class="mb-0"><?php echo ( isset( $headlist[1]['title'] ) ) ? str_replace("\\","",substr(html_entity_decode($headlist[1]['title']), 0, 50)). "...":''; ?></h3>
          <p class="card-text mb-auto"><?php echo ( isset( $headlist[1]['blurb']['description'] ) ) ? str_replace("\\","",substr(html_entity_decode($headlist[1]['blurb']['description']), 0, 150)). "...":''; ?></p>
          <a href="#" class="stretched-link">Continue reading</a>
        </div>
        <div class="col-auto d-none d-lg-block"><br />
        <img  src="<?php echo $headlist[1]['blurb']['image'][0]; ?>" data-src="<?php echo $headlist[1]['blurb']['image'][0]; ?>" alt="<?php echo $headlist[1]['title']; ?>" width=350>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
        <div class="col p-4 d-flex flex-column position-static">
          <strong class="d-inline-block mb-2 text-success">Design</strong>
          <h3 class="mb-0"><?php echo ( isset( $headlist[2]['title'] ) ) ? str_replace("\\","",substr(html_entity_decode($headlist[2]['title']), 0, 50)). "...":''; ?></h3>
          <p class="mb-auto"><?php echo ( isset( $headlist[2]['blurb']['description'] ) ) ? str_replace("\\","",substr(html_entity_decode($headlist[2]['blurb']['description']), 0, 150)). "...":''; ?></p>
          <a href="#" class="stretched-link">Continue reading</a>
        </div>
        <div class="col-auto d-none d-lg-block"><br />
          <img  src="<?php echo $headlist[2]['blurb']['image'][0]; ?>" data-src="<?php echo $headlist[2]['blurb']['image'][0]; ?>" alt="<?php echo $headlist[2]['title']; ?>" width=350>
        </div>
      </div>
    </div>
  </div>





        <div class="row">
          <div class="col-md-2 order-md-1">
            <div class="blog-news">BREAKING NEWS</div>
          </div>
          <div class="col-md-10 order-md-2">
            <div class="w-100 font-weight-bold" id="changeText"></div>
          </div>
        </div>
          <div class="row mt-5">
                <?php 
                foreach( array_splice( $headlist, 0, 2 ) as $rand_blog){
                  if ( ! isset( $rand_blog['blurb']['image'][0] ) || ! $rand_blog['blurb']['image'][0] || stristr( $rand_blog['blurb']['image'][0], 'images.unsplash.com' ) ) $rand_blog['blurb']['image'][0] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($rand_blog['title']) )."/900_600/blog.jpg";
                ?>
                <div class="col-6 col-sm-6">
                <h3 class="alt-title">
                  <a class="text-white" href="<?php echo taoh_blog_link($rand_blog['conttoken']); ?>"><?php echo $rand_blog['title']; ?></a>
                </h3>
                <a href="<?php echo taoh_blog_link($rand_blog['conttoken']); ?>" class="circle">
                    <img src="<?php echo $rand_blog['blurb']['image'][0]; ?>" data-src="<?php echo $rand_blog['blurb']['image'][0]; ?>" alt="<?php echo $rand_blog['title']; ?>">
                </a>
                </div> 
                <?php } ?>
            </div>
            <div class="row mt-1">
            <?php 
                foreach(array_splice( $headlist, 0, 3 ) as $rand_blog1){
                  if ( ! isset( $rand_blog1['blurb']['image'][0] ) || ! $rand_blog1['blurb']['image'][0] || stristr( $rand_blog1['blurb']['image'][0], 'images.unsplash.com' ) ) $rand_blog1['blurb']['image'][0] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($rand_blog1['title']) )."/900_600/blog.jpg";
                ?>
                <div class="col-4">
                <h3 class="alt-title">
                  <a class="text-white" href="<?php echo taoh_blog_link($rand_blog1['conttoken']); ?>"><?php echo $rand_blog1['title']; ?></a>
                </h3>
                    <a href="<?php echo taoh_blog_link($rand_blog1['conttoken']); ?>" class="circle">
                      <img  src="<?php echo $rand_blog1['blurb']['image'][0]; ?>" data-src="<?php echo $rand_blog1['blurb']['image'][0]; ?>" alt="<?php echo $rand_blog1['title']; ?>">
                    </a>
                </div> 
                <?php } ?>  
            </div>          
			</div><!-- end hero-content -->
    </div><!-- end container -->
</section><!-- end hero-area -->
<section class="blog-listing gray-dark">
        <div class="container">
            <div class="row align-items-start">
                <div class="card card-item col-lg-2 mt-3">
                    <div class="widget-body">
                        <div class="media align-items-center">
                            <div class="media-body">
                            <?php taoh_leftmenu_widget(); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 m-15px-tb">
                  <div class="mt-3">
                    <?php taoh_reads_search_widget(); ?>
                  </div>
                        <?php
                            if($total < 1 ) { ?>
                                <p>No results found!</p>
                            <?php	} else { ?>
                            <div class="card mt-3">
                                <?php 
                                    foreach ($list as $blog ){
                                      if ( ! isset( $blog['blurb']['image'][0] ) || ! $blog['blurb']['image'][0] || stristr( $blog['blurb']['image'][0], 'images.unsplash.com' ) ) $blog['blurb']['image'][0] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($blog['title']) )."/900_600/blog.jpg";
                                ?>
                                <div class="ml-5 mt-4">
                                    <h5>
                                        <a href="<?php echo taoh_blog_link($blog['conttoken']); ?>" class="text-body">
                                            <?php echo $blog['title']; ?>
                                            <?php if(isset($post->link)) {
                                                echo external_link_icon();
                                            } ?>
                                        </a>
                                    </h5>
                                </div>
                                <div class="ml-5 mt-4 mr-5 mb-5">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <a href="<?php echo taoh_blog_link($blog['conttoken'], @$blog['link']); ?>">
                                                <img class="title-hover" src="<?php echo $blog['blurb']['image'][0]; ?>" data-src="<?php echo $blog['blurb']['image'][0]; ?>" alt="Card image">
                                            </a></div>
                                        <div class="col-lg-6">
                                            <p><?php echo str_replace("\\","",substr(html_entity_decode($blog['blurb']['description']), 0, 150)); ?>....<br>
                                                <a href="<?php echo taoh_blog_link($blog['conttoken']); ?>" class="btn btn-danger btn-sm">
                                                    <span>Read More</span>
                                                    <i class="arrow"></i>
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-5 mr-5">
                                    <hr class="hr hr-blurry" />
                                </div>
                                <?php } ?> 
                            </div> 
                            <?php } ?>   
                        <div class="col-12 mt-4" id="pagination"></div>
                </div>

                <div class="col-lg-3 m-15px-tb blog-aside mt-3">
                    <!-- Trending Post -->
                        
                    <!-- End Trending Post -->
<?php
/*
?>

                    <!-- Author -->
                    <div class="widget widget-author">
                        <div class="widget-title">
                            <h3>Author</h3>
                        </div>
                        <div class="widget-body">
                            <div class="media align-items-center">
                                <div class="avatar">
                                    <img src="https://bootdey.com/img/Content/avatar/avatar6.png" title="" alt="">
                                </div>
                                <div class="media-body">
                                    <h6>Hello, I'm<br> Rachel Roth</h6>
                                </div>
                            </div>
                            <p>I design and develop services for customers of all sizes, specializing in creating stylish, modern websites, web services and online stores</p>
                        </div>
                    </div>
                    <!-- End Author -->
<?php
*/
?>
    						<?php if (function_exists('taoh_jusask_widget')) { taoh_jusask_widget();  } ?>
                    <!-- Latest Post -->
                        <?php blog_related_widget(); ?>
                    <!-- End Latest Post -->

                    <!-- widget Tags -->
                        <?php taoh_obviousbaba_widget(); ?>
                    <!-- End widget Tags -->

                    <!-- widget Tags -->
                        <?php taoh_ads_widget(); ?>
                    <!-- End widget Tags -->
                </div>
            </div>
        </div>
    </section>
<script type="text/javascript">
  show_pagination('#pagination')
  function show_pagination(holder) {
    return $(holder).pagination({
        items: <?php echo $total; ?>,
        itemsOnPage: 10,
        displayedPages: 3,
        currentPage: "<?php echo (@$_GET['page'] ? $_GET['page'] : 1); ?>",
        onInit: function() {
          $("#pagination ul").addClass('pagination');
          $("#pagination ul li.disabled").addClass('page-link text-gray');
          $("#pagination ul li.active").addClass('page-link bg-primary text-white');
        },
        onPageClick: function(pageNumber, event) {
          $("#pagination ul").addClass('pagination');
          $("#pagination ul li.disabled").addClass('page-link text-gray');
          $("#pagination ul li.active").addClass('page-link bg-primary text-white');
          search = "?page="+pageNumber;
          window.location.replace(window.location.search = search);
          //currentPage = pageNumber;
          //taoh_jobs_init();
        }
    });
  }
 
var text = <?php echo $getjstitle ?>;console.log(text);
var counter = 0;
var elem = document.getElementById("changeText");
var inst = setInterval(change, 1000);

function change() {
  elem.innerHTML = text[counter];
  //$('#changeText').val(text[counter]);
  counter++;
  if (counter >= text.length) {
    counter = 0;
    // clearInterval(inst); // uncomment this if you want to stop refreshing after one cycle
  }
}
</script>
<?php taoh_get_footer();  ?>