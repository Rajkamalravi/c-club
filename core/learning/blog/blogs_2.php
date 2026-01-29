<?php taoh_get_header();

 //"perpage"=>10 is not working
 $query = array("mod"=>"core", "conttype"=>"blog", "type"=>"blog", "ops"=>"list");
 $query['token'] = taoh_get_dummy_token();
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
//$req = file_get_contents($locn);
$url = 'core.content.get';
$taoh_vals = array(
  "mod" => 'core',
  "conttype"=>"blog",
  "type"=>"blog",
  "ops"=>"list",
  'token'=>taoh_get_dummy_token(1),
  "q"=>$_GET['q'],
  "category"=>$_GET['cat'],
  "page"=>$_GET['page'],
  //'cache' => array ( "name" => taoh_get_dummy_token(1).'_'.taoh_p2us($url).'_blog_list'),
);
// $cache_name = $url.'_blog_list_' . hash('sha256', $url . serialize($taoh_vals));
// $taoh_vals[ 'cfcache' ] = $cache_name;
// $taoh_vals[ 'cache_name' ] = $cache_name;
// $taoh_vals[ 'cache' ] = array ( "name" => $cache_name );
ksort($taoh_vals);
$req = taoh_apicall_get($url, $taoh_vals);

$res = json_decode($req, true);
$total = $res['output']['count'];
$list = $res['output']['list'];

$query = [];
$head_type = "Read to succeed";

$query['mod'] = "reads";
if ( defined( 'TAOH_API_TOKEN' ) ){
	$query['ctype'] = 'token';
	$query['code'] = TAOH_API_TOKEN;
} else {
	$query['ctype'] = 'secret';
	$query['code'] = TAOH_API_SECRET;
}

if (isset($_GET['q']) && $_GET['q'] != ''){
	$query['q'] = urlencode($_GET['q']);
}

if (isset($_GET['type']) && $_GET['type'] != ''){
	$query['type'] = urlencode($_GET['type']);
}
$query = http_build_query($query, '', '&');

$api =  TAOH_SITE_READS."?".$query;

$reads = json_decode(taoh_url_get_content($api), true);
if (@$_GET['type'] == 'category'){
	list($slug, $value) = explode('###', @$reads['value']);
	$head_type = "For Category: <a href=\"/".TAOH_READS_URL."?q=".urlencode( $value )."&type=category\">$value</a>";
} else if ( @$_GET['type'] == 'author'){
	list($slug, $value) = explode('###', @$reads['value']);
	$head_type = "By <a href=\"/".TAOH_READS_URL."?q=".urlencode( $slug )."&type=author\">$value</a>";
} else if ( @$_GET['type'] == 'search'){
	$head_type = "Searching for: ".@$_GET['q'];
}
?>
<section class="hero-area pattern-bg-2 bg-white shadow-sm overflow-hidden pt-50px pb-50px">
    <span class="stroke-shape stroke-shape-1"></span>
    <span class="stroke-shape stroke-shape-2"></span>
    <span class="stroke-shape stroke-shape-3"></span>
    <span class="stroke-shape stroke-shape-4"></span>
    <span class="stroke-shape stroke-shape-5"></span>
    <span class="stroke-shape stroke-shape-6"></span>
    <div class="container">
			<div class="hero-content text-center">
					<h1 class="section-title pb-3"><a href="/<?php echo TAOH_READS_URL; ?>">Reads</a></h1>
          <?php //  echo "(".$total.")";
          ?>
					<ul class="breadcrumb-list">
							<li><?php echo $head_type; ?></li>
					</ul>
			</div><!-- end hero-content -->
    </div><!-- end container -->
</section><!-- end hero-area -->

<section class="blog-area pt-80px pb-80px">
    <div class="container">
        <div class="row">
					<div class="col-lg-2">
					<?php taoh_leftmenu_widget(); ?>
					</div><!-- end col-lg-2 -->
              <div class="col-lg-6">
                <div class="row">
									<?php
									if($total < 1 ) { ?>
										<p>No results found!</p>
									<?php	} else {

									foreach ($list as $blog ){ ?>
										<div class="col-lg-6 responsive-column-half">
												<div class="card card-item hover-y">
													 	<a href="<?php echo taoh_blog_link($blog['conttoken'], @$blog['link']); ?>" class="card-img">
																<img class="lazy" src="<?php echo $blog['blurb']['image'][0]; ?>" data-src="<?php echo $blog['blurb']['image'][0]; ?>" alt="Card image">
														</a>
														<div class="card-body pt-0">
																<a href="<?php //echo TAOH_SITE_URL_ROOT."/reads?q=".urlencode( $cat_value )."&type=category"; ?>" class="card-link"><?php echo $blog['content_type']; ?></a>
																<h5 class="card-title fw-medium">
                                  <a href="<?php echo taoh_blog_link($blog['conttoken']); ?>">
                                    <?php echo $blog['title']; ?>
                                    <?php if(isset($post->link)) {
                                      echo external_link_icon();
                                    } ?>
                                  </a>
                                  </h5>
									<div class="media media-card align-items-center shadow-none p-0 mb-0 rounded-0 mt-4 bg-transparent">
																		<a href="<?php echo TAOH_READS_URL; ?>?q=<?php echo $blog['author']['ptoken']; ?>&type=author" class="media-img media-img--sm d-block mr-2 rounded-full">
																				<img src="<?php echo $blog['author']['avatar']; ?>" alt="avatar" class="rounded-full">
																		</a>
																		<div class="media-body">
																				<h5 class="fs-14 fw-medium">By <a href="<?php echo TAOH_READS_URL; ?>/?q=<?php echo $blog['author']['ptoken']; ?>&type=author"><?php echo $blog['author']['fname']." ".$blog['author']['lname']; ?></a></h5>
																		</div>
																</div>
														</div><!-- end card-body -->
												</div><!-- end card -->
										</div><!-- end col-lg-6 -->
									<?php }
								} ?>
								</div><!-- end row -->
                <div id="pagination"></div>
            </div><!-- end col-lg-8 -->

            <div class="col-lg-4">
                <div class="sidebar">
				<?php taoh_reads_search_widget(); ?>
                <?php blog_related_widget(); ?>
				<?php taoh_obviousbaba_widget(); ?>
                <?php taoh_ads_widget(); ?>
            		</div>
        </div><!-- end row -->
    </div><!-- end container -->
</section><!-- end blog-area -->
<script type="text/javascript">
  show_pagination('#pagination')
  function show_pagination(holder) {
    return $(holder).pagination({
        items: <?php echo $total; ?>,
        itemsOnPage: 10,
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
</script>
<?php taoh_get_footer();  ?>