<?php 
if ( ! defined ( 'TAO_PAGE_TITLE' ) ) { define ( 'TAO_PAGE_TITLE', "Career Resources at ".TAOH_SITE_NAME_SLUG.": Explore Invaluable Resources and Insights for Professional Development" ); }
if ( ! defined ( 'TAO_PAGE_DESCRIPTION' ) ) { define ( 'TAO_PAGE_DESCRIPTION', "Discover a wealth of career blogs offering invaluable resources, tips, and insights for your professional development at ".TAOH_SITE_NAME_SLUG.". Stay updated with the latest industry trends, enhance your skills, and gain inspiration from our diverse collection of career-focused articles. Elevate your career journey with our comprehensive learning platform." ); }
if ( ! defined ( 'TAO_PAGE_KEYWORDS' ) ) { define ( 'TAO_PAGE_KEYWORDS', "Career blogs at ".TAOH_SITE_NAME_SLUG.", Professional development resources at ".TAOH_SITE_NAME_SLUG.", Career insights at ".TAOH_SITE_NAME_SLUG.", Industry trends at ".TAOH_SITE_NAME_SLUG.", Skill enhancement tips at ".TAOH_SITE_NAME_SLUG.", Career inspiration at ".TAOH_SITE_NAME_SLUG.", Career growth articles at ".TAOH_SITE_NAME_SLUG.", Personal branding strategies at ".TAOH_SITE_NAME_SLUG.", Leadership development insights at ".TAOH_SITE_NAME_SLUG.", Job search strategies at ".TAOH_SITE_NAME_SLUG.", Resume writing tips at ".TAOH_SITE_NAME_SLUG.", Interview preparation advice at ".TAOH_SITE_NAME_SLUG.", Networking guidance at ".TAOH_SITE_NAME_SLUG.", Professional growth articles, Career exploration resources at ".TAOH_SITE_NAME_SLUG.", Industry-specific insights at ".TAOH_SITE_NAME_SLUG.", Workplace success tips at ".TAOH_SITE_NAME_SLUG.", Work-life balance strategies at ".TAOH_SITE_NAME_SLUG.", Career transition advice at ".TAOH_SITE_NAME_SLUG.", Continuous learning resources at ".TAOH_SITE_NAME_SLUG ); }
taoh_get_header();
include 'reads_css.php';
$reads_type = 'work';

//https://preapi.tao.ai/core.content.get?mod=users&token=ZBPEKKTn&ops=wellness&type=landing

$get_widget = taoh_wellness_widget_get($reads_type);
$hero = $get_widget['hero'];
$trending_bar = $get_widget['trending_bar'];
$items = array();
foreach($trending_bar as $username) {
 $items[] = "<a class='js-title' href='".taoh_newsletter_link(slugify2($username['title'])."-".$username['conttoken'])."'>".$username['title']."</a>";
}
$getjstitle = json_encode($items);
?>
<style>
  @media (min-width: 1280px){
  .container {
      max-width: 1021px;
  }
  }
</style>
<section class="blog-listing gray-dark">
    <div class="container bg-white">
        <div class="hero-content text-center ">
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
                <a href="<?php echo taoh_newsletter_link(slugify2($rand_blog['title'])."-".$rand_blog['conttoken']); ?>" class="parentContainer">
                    <img src="<?php echo $rand_blog[$ind]['image']; ?>" alt="" class="banner-image dash_metrics" data-type="newsletter" data-metrics="view" conttoken="<?php echo $rand_blog['conttoken']; ?>">
                    <div class="top-left text-white d-flex align-content-end flex-wrap">
                    <h4 class="text-white top-title"><?php echo ucfirst(taoh_title_desc_decode($rand_blog['title'])); ?></h4>
                    </div>
                </a>
            </div>
            <?php } ?>
            </div>        
        </div><!-- end hero-content -->
        <div class="row bg-white"> 
            <div class="col-lg-8 m-15px-tb"> 
                <div class="mt-3 sticky-top light-dark">
                    <?php taoh_newsletter_search_widget(); // taoh_reads_search_widget(); ?>
                </div>  
                <div class="">
                    <?php taoh_all_reads_widget( $get_widget[ 'center1' ], 'center1' ); ?> 
                    <?php taoh_all_reads_widget( $get_widget[ 'center2' ], 'center2' ); ?> 
                    <?php taoh_all_reads_widget( $get_widget[ 'center3' ], 'center3' ); ?>
                    <div class="blured"> 
                        <div id='loaderArea'></div>
                        <div>
                            <h4 class="session_title ml-3"><span>LATEST ARTICLES</span></h4>
                            <div id="eventArea">Loading ...</div> 
                        </div>
                        <div class="mb-4" id="pagination"></div>
                    </div>
                </div>
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
                    <?php // taoh_all_reads_widget( $get_widget[ 'right2' ], 'right2' ); ?>
                    <?php if (function_exists('taoh_get_recent_jobs')) { taoh_get_recent_jobs('new');  } ?>
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
  let loaderArea = $('#loaderArea');
  let eventArea = $('#eventArea');
  let itemsPerPage = 10;
  let currentPage = 1;
  let totalItems = 0; //this will be rewriiten on response of assks on line 307

  $(document).ready(function(){
    taoh_newsletter_init();
  });

  jQuery.ajaxSetup({
    beforeSend: function() {
       loader(true, loaderArea)
    },
    complete: function(){
        loader(false, loaderArea)
    },
    success: function() {}
  });

  $("div").removeClass("card card-item");
  //show_pagination('#pagination')
  function show_pagination(holder) {
    return $(holder).pagination({
        items: totalItems,
        itemsOnPage: itemsPerPage,
        currentPage: currentPage,
        onInit: function() {
          $("#pagination ul").addClass('pagination');
          $("#pagination ul li.disabled").addClass('page-link text-gray');
          $("#pagination ul li.active").addClass('page-link bg-primary text-white');
        },
        onPageClick: function(pageNumber, event) {
          $("#pagination ul").addClass('pagination');
          $("#pagination ul li.disabled").addClass('page-link text-gray');
          $("#pagination ul li.active").addClass('page-link bg-primary text-white');
          currentPage = pageNumber;
          taoh_newsletter_init();
        }
    });
  }

  function taoh_newsletter_init() {
    var data = {
        'taoh_action': 'taoh_central_newsletter_get',
        'ops': 'list',
        'offset': currentPage,
        'limit': itemsPerPage,
        
    };
    // console.log(data);
    jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
        console.log(response);
        render_newsletter_template(response, eventArea);
    }).fail(function() {
        console.log( "Network issue!" );
    })
}

function render_newsletter_template(data, slot) {
  slot.empty();
  var type_num = typeof(data.output.num_rows);
  if(data.output === false || type_num === 'object') {
      slot.append('<p class="fs-20 text-black">No posts to display!</p>');
      return false;
  }
  totalItems = data.output.num_rows;

  $.each(data.output.list, function(i, v){
      var prefix = '<?php echo TAOH_CDN_PREFIX ?>';
      v.blurb.image = prefix+"/images/igcache/"+encodeURIComponent(v.title)+"/900_600/blog.jpg"; 
      slot.append(`
                  <div class="td_module_12 mt-3">
                      <div class="item-details">
                          <h3 class="lat-title">
                          <a class="dash_metrics" data-type="newsletter" data-metrics="view" conttoken="${v.conttoken}" href="newsletter/d/${convertToSlug(v.title)}-${v.conttoken}">
                              ${taoh_title_desc_decode(v.title)}
                          </a>
                          </h3>
                      </div>
                      <div class="mt-3 cl-image">
                          <a href="newsletter/d/${convertToSlug(v.title)}-${v.conttoken}" rel="bookmark">
                              <img class="title-hover" src="${v.blurb.image}" alt="${v.blurb.image}">
                          </a>
                      </div>
                      <div class="mt-2 descrip claimedRight">
                          ${(v.blurb.description != "" && v.blurb.description !== undefined) ? taoh_title_desc_decode(v.blurb.description.substring(0,245))+"...." : taoh_title_desc_decode(v.blurb.description)}
                      </div>
                      <div class="td-read-more mt-2">
                          <a href="newsletter/d/${convertToSlug(v.title)}-${v.conttoken}">Read more</a>
                      </div>
                  </div>
                  <div>
                      <hr class="hr hr-blurry" />
                  </div>`
      );
  });
  if(totalItems >= 11){
    show_pagination('#pagination');
  }
  
}


  //Trending Bar Start
  var text = <?php echo taoh_title_desc_decode($getjstitle); ?>;console.log(text);
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