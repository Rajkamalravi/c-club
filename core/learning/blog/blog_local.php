<?php taoh_get_header();
include 'reads_css.php';
$reads_type = 'work';

//https://preapi.tao.ai/core.content.get?mod=users&token=ZBPEKKTn&ops=wellness&type=landing

$get_widget = taoh_wellness_widget_get($reads_type);
$total = 10;
$hero = $get_widget['hero'];
$trending_bar = $get_widget['trending_bar'];
$items = array();
foreach($trending_bar as $username) {
  $items[] = "<a class='js-title' href='".taoh_blog_link(slugify2($username['title'])."-".$username['conttoken'])."'>".$username['title']."</a>";
}
$getjstitle = json_encode($items);

$list = taoh_central_widget_get();//print_r($taoh_vals);

taoh_metrics_call_for_reads($hero);
taoh_metrics_call_for_reads($trending_bar);

?>
<style>
  @media (min-width: 1280px){
  .container {
      max-width: 1021px;
  }
  }
</style>
<section class="blog-listing gray-dark">
  <div class="container">
      <div class="row bg-white">
        <div class="hero-content text-center bg-white">
          <div class="row mx-0 p-3">
            <div class="col-md-2 order-md-1">
              <div class="blog-news">TRENDING NOW</div>
            </div>
            <div class="col-md-10 p-0 order-md-2 mt-1" style="height:26px;">
              <div class="w-100" style="line-height:19px;font-size: 14px;font-weight:500;color: #444;" id="changeText"></div>
            </div>
          </div>
          <div class="row mx-0">
            <?php
              foreach( $hero as $ind => $rand_blog){
                if ( ! isset( $rand_blog[$ind]['image'] ) || ! $rand_blog[$ind]['image'] || stristr( $rand_blog[$ind]['image'], 'images.unsplash.com' ) ) $rand_blog[$ind]['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($rand_blog['title']) )."/900_600/blog.jpg";
            ?>
              <div class="image-box col-md-<?php echo ($ind > 1) ? '4' : '6' ?> col-lg-<?php echo ($ind > 1) ? '4' : '6' ?> p-1 <?php echo ($ind > 1) ? 'hero-secondrow' : 'hero-firstrow' ?>" >
                <a href="<?php echo taoh_blog_link(slugify2($rand_blog['title'])."-".$rand_blog['conttoken']); ?>" class="parentContainer">
                  <img src="<?php echo $rand_blog[$ind]['image']; ?>" alt="" class="banner-image">
                  <div class="top-left text-white d-flex align-content-end flex-wrap">
                    <h4 class="text-white top-title"><?php echo $rand_blog['title']; ?></h4>
                  </div>
                </a>
              </div>
            <?php } ?>
          </div>        
        </div><!-- end hero-content --> 
          <div class="col-lg-8 m-15px-tb"> 
          <div class="mt-3 sticky-top light-dark">
            <?php taoh_reads_search_widget(); ?>
          </div>  
            <div class="">
              <?php taoh_all_reads_widget( $get_widget[ 'center1' ], 'center1' ); ?> 
              <?php taoh_all_reads_widget( $get_widget[ 'center2' ], 'center2' ); ?> 
              <?php taoh_all_reads_widget( $get_widget[ 'center3' ], 'center3' ); ?> 
              <?php //central_widget3(); ?>
              
              <?php
                  if($total < 1 ) { ?>
                      <p>No results found!</p>
                  <?php	} else { ?>
                    <h4 class="session_title ml-3"><span>LATEST ARTICLES</span></h4>
                      <?php 
                          foreach ($list as $blog ){
                            if ( ! isset( $blog['blurb']['image'][0] ) || ! $blog['blurb']['image'][0] || stristr( $blog['blurb']['image'][0], 'images.unsplash.com' ) ) $blog['blurb']['image'][0] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( taoh_title_desc_decode($blog['title']) )."/900_600/blog.jpg";
                      ?>
                      <div class="td_module_12 mt-3">
                        <div class="item-details">
                          <h3 class="lat-title">
                            <a href="<?php echo taoh_blog_link(slugify2($blog['title'])."-".$blog['conttoken']); ?>">
                              <?php echo $blog['title']; ?>
                            </a>
                          </h3>
                          <div class="mt-3 cl-image">
                            <a href="<?php echo taoh_blog_link(slugify2($blog['title'])."-".$blog['conttoken']); ?>" rel="bookmark">
                              <img class="title-hover" src="<?php echo $blog['blurb']['image'][0]; ?>" alt="<?php echo $blog['blurb']['image'][0]; ?>">
                            </a>
                          </div>                
                          <div class="mt-2 descrip claimedRight">
                            <?php echo html_entity_decode($blog['blurb']['description']); ?>
                          </div>
                            <div class="td-read-more mt-2">
                                <a href="<?php echo taoh_blog_link(slugify2($blog['title'])."-".$blog['conttoken']); ?>">Read more</a>
                            </div>
                        </div>
                      </div>
                      <div>
                          <hr class="hr hr-blurry" />
                      </div>
                      <?php } ?> 
            </div> 
                  <?php } ?>   
            <div class="col-lg-12 m-4" id="pagination"></div>
          </div>
          <div class="col-lg-4 m-15px-tb border-left">
            <div class="border-bottom">
              <?php tags_widget(); ?>
            </div>
            <div class="border-bottom">
                <?php taoh_all_reads_widget( $get_widget[ 'right_ad1' ], 'right_ad1' ); ?>
            </div>
            <div class="border-bottom">
                <?php taoh_all_reads_widget( $get_widget[ 'right1' ], 'right1' ); ?>
            </div>
            <div class="border-bottom">
                <?php taoh_all_reads_widget( $get_widget[ 'right_ad2' ], 'right_ad2' ); ?>
            </div>
            <div class="border-bottom">
                <?php taoh_all_reads_widget( $get_widget[ 'right2' ], 'right2' ); ?>
            </div>           
            <section id="sticky">
                <div class="border-bottom">
                    <?php taoh_all_reads_widget( $get_widget[ 'right_ad3' ], 'right_ad3' ); ?>
                </div>
                <div class="">
                    <?php taoh_all_reads_widget( $get_widget[ 'right3' ], 'right3' ); ?>
                </div>
            </section>
          </div>
      </div>
    </div>
</section>
<script type="text/javascript">
  $("div").removeClass("card card-item");
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

  //Trending Bar Start
  var text = <?php echo $getjstitle ?>;console.log(text);
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
  
$('.claimedRight').each(function (f) {

    var newstr = $(this).text().substring(0,250)+'....';
    $(this).text(newstr);

  });

</script>
<?php taoh_get_footer();  ?>