<?php 
if ( ! defined ( 'TAO_PAGE_TITLE' ) ) { define ( 'TAO_PAGE_TITLE', "Work Resources at ".TAOH_SITE_NAME_SLUG.": Explore Insights and Strategies for Career Success and Workplace Excellence" ); }
if ( ! defined ( 'TAO_PAGE_DESCRIPTION' ) ) { define ( 'TAO_PAGE_DESCRIPTION', "Discover our collection of Work Resources, offering valuable insights, strategies, and resources to excel in your career and thrive in the workplace at ".TAOH_SITE_NAME_SLUG.". Stay updated with the latest trends, uncover effective work-life balance techniques, enhance your productivity, and foster professional growth. Elevate your career journey with our comprehensive blog platform." ); }
if ( ! defined ( 'TAO_PAGE_KEYWORDS' ) ) { define ( 'TAO_PAGE_KEYWORDS', "Work blogs at ".TAOH_SITE_NAME_SLUG.", Career success insights at ".TAOH_SITE_NAME_SLUG.", Workplace excellence strategies at ".TAOH_SITE_NAME_SLUG.", Professional growth resources at ".TAOH_SITE_NAME_SLUG.", Work-life balance tips at ".TAOH_SITE_NAME_SLUG.", Productivity enhancement techniques at ".TAOH_SITE_NAME_SLUG.", Leadership development insights at ".TAOH_SITE_NAME_SLUG.", Workplace communication skills at ".TAOH_SITE_NAME_SLUG.", Time management strategies at ".TAOH_SITE_NAME_SLUG.", Team collaboration techniques at ".TAOH_SITE_NAME_SLUG.", Career advancement tips at ".TAOH_SITE_NAME_SLUG.", Workforce trends at ".TAOH_SITE_NAME_SLUG.", Workplace diversity and inclusion at ".TAOH_SITE_NAME_SLUG.", Workplace wellness at ".TAOH_SITE_NAME_SLUG.", Effective decision-making in the workplace at ".TAOH_SITE_NAME_SLUG.", Work ethics and professionalism at ".TAOH_SITE_NAME_SLUG.", Building strong work relationships at ".TAOH_SITE_NAME_SLUG.", Managing workplace stress at ".TAOH_SITE_NAME_SLUG.", Effective goal setting at work at ".TAOH_SITE_NAME_SLUG.", Workplace learning and development" ); }
taoh_get_header();
include 'reads_css.php';
$reads_type = 'work';

//https://preapi.tao.ai/core.content.get?mod=users&token=ZBPEKKTn&ops=wellness&type=landing

$get_widget = taoh_wellness_widget_get($reads_type);
$hero = $get_widget['hero'];
$trending_bar = $get_widget['trending_bar'];
$items = array();
foreach($trending_bar as $username) {
 $items[] = "<a class='js-title' href='".taoh_blog_link(slugify2($username['title'])."-".$username['conttoken'])."'>".ucfirst(taoh_title_desc_decode($username['title']))."</a>";
}
$getjstitle = json_encode($items);


$current_app = taoh_parse_url(0);
$app_data = taoh_app_info($current_app);
$taoh_user_vars = taoh_user_all_info();
$ptoken = ( taoh_user_is_logged_in()) ? $taoh_user_vars->ptoken : TAOH_API_TOKEN_DUMMY;
define( 'TAO_PAGE_TYPE', ($app_data?->slug ?? '') );
$log_nolog_token = $ptoken;

?>
<style>
  @media (min-width: 1280px){
  .container {
      max-width: 1021px;
  }
  }
  .sqs-block-content .sqs-html-content p,  .sqs-block-content .sqs-html-content h1,
  .sqs-block-content .sqs-html-content h2,  .sqs-block-content .sqs-html-content h3,  .sqs-block-content .sqs-html-content h4, 
  .sqs-block-content .sqs-html-content h5,  .sqs-block-content .sqs-html-content h6 {
    display: -webkit-box;        
    -webkit-box-orient: vertical; 
    overflow: hidden;           
    -webkit-line-clamp: 1;   
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
            <div class="cover-image-container image-box col-md-<?php echo ($ind > 1) ? '4' : '6' ?> col-lg-<?php echo ($ind > 1) ? '4' : '6' ?> p-1 <?php echo ($ind > 1) ? 'hero-secondrow' : 'hero-firstrow' ?>" >
                <div class="glass-overlay"></div>
                <div class="bg-image" style="background-image: url('<?php echo $rand_blog[$ind]['image']; ?>')"></div>
                <a href="<?php echo taoh_blog_link(slugify2($rand_blog['title'])."-".$rand_blog['conttoken']); ?>" class="parentContainer dash_metrics" data-type="reads" data-metrics="view" conttoken="<?php echo $rand_blog['conttoken']; ?>">
                    <img src="<?php echo $rand_blog[$ind]['image']; ?>" alt="" class="banner-image main-image" style="max-height: 330px;">
                    <div class="top-left text-white d-flex align-content-end flex-wrap">
                    <h4 class="text-white line-clamp-2"><?php echo ucfirst(taoh_title_desc_decode($rand_blog['title'])); ?></h4>
                    </div>
                </a>
            </div>
            <?php } ?>
            </div>        
        </div><!-- end hero-content -->
        <div class="row bg-white"> 
            <div class="col-lg-8 m-15px-tb"> 
                <div class="mt-3 sticky-top light-dark">
                    <?php taoh_reads_search_widget(); ?>
                </div>  
                <div class="">
                    <?php taoh_all_reads_widget( $get_widget[ 'center1' ], 'center1' ); ?> 
                    <?php taoh_all_reads_widget( $get_widget[ 'center2' ], 'center2' ); ?> 
                    <?php taoh_all_reads_widget( $get_widget[ 'center3' ], 'center3' ); ?>
                    <div class="blured"> 
                        
                        <div>
                            <h4 class="session_title ml-3"><span>LATEST ARTICLES</span></h4>
                            <div id='loaderArea'></div>
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
                <div class="border-bottom p-4">
                    <?php if (function_exists('taoh_get_recent_jobs')) { taoh_get_recent_jobs('new');  } ?>
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
                <!-- <div class="border-bottom">
                    <?php //taoh_all_reads_widget( $get_widget[ 'right2' ], 'right2' ); ?>
                </div> -->           
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
  let arr_cont = [];
  var already_rendered = false;
  var readswork_list_name = '';
  var store_name = READStore;

  $(document).ready(function(){
    //taoh_blogs_init();
    <?php if(TAOH_INTAODB_ENABLE) { ?>
			getreadslistdata();
		<?php }else{ ?>
			taoh_blogs_init();
		<?php } ?>
  });

  function getreadslistdata(queryString=''){
    loader(true, loaderArea);
		// Open or create a database
		getIntaoDb(dbName).then((db) => {
            var currpage = currentPage-1;
            var readswork_list_hash = queryString+currpage+itemsPerPage;
            readswork_list_name = 'readswork_'+crc32(readswork_list_hash);
            console.log(readswork_list_name);     
            const datareadsworkrequest = db.transaction(store_name).objectStore(store_name).get(readswork_list_name); // get main data
            datareadsworkrequest.onsuccess = ()=> {
                console.log(datareadsworkrequest);
                const readsworkstoredatares = datareadsworkrequest.result;
                if(readsworkstoredatares !== undefined && readsworkstoredatares !== null && readsworkstoredatares !== "" && readsworkstoredatares !== "undefined" && readsworkstoredatares !== "null"){
                    const readsworkstoredata = datareadsworkrequest.result.values;
                    already_rendered = true;
                    //render_asks_template(asks, listChatRooms);
                    render_blog_template(readsworkstoredata, eventArea);
                    //taoh_blogs_init(queryString);
                }else{
                    taoh_blogs_init(queryString);
                    //taoh_blogs_init();
                }
            }
		}).catch((error) => {
           console.log('Getreadsworklistdata Error:', error);
       	});
	}

  jQuery.ajaxSetup({
    beforeSend: function() {
        $('.spinner-border').show();
    },
    complete: function(){
        $('.spinner-border').hide();
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
          //taoh_blogs_init();
          already_rendered = false;
          <?php if(TAOH_INTAODB_ENABLE) { ?>
			getreadslistdata();
		<?php }else{ ?>
			taoh_blogs_init();
		<?php } ?>
          console.log(pageNumber);
        }
    });
  }

  function taoh_blogs_init(queryString="") {
    var data = {
        'taoh_action': 'taoh_central_get',
        'ops': 'list',
        'offset': currentPage,
        'limit': itemsPerPage,
		'filters': queryString,
        
    };console.log(data);
    jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
        console.log(response);
        //render_blog_template(response, eventArea);
        <?php if(TAOH_INTAODB_ENABLE) { ?>
          indx_reads_list(response);
				if(!already_rendered){
					render_blog_template(response, eventArea);
				}
			<?php }else{ ?>
				render_blog_template(response, eventArea);
			<?php } ?>
    }).fail(function() {
        console.log( "Network issue!" );
    })
}

function call_iframe(e,el) {
    console.log('video --- ',$(el).closest('.blog_video').attr('data-video'));
    console.log('video id --- ',$(el).attr('data-video'));
    video_id = $(el).attr('data-video');
    $(el).closest('.blog_video').css('display','none');
    var image_div = `<iframe  src="https://www.youtube.com/embed/`+video_id+`?rel=0&autoplay=1&mute=1" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" class="rounded-5 shadow-1-strong me-2" width="100%" height="350px"></iframe>`;
    $(el).closest('.td-post-image').html(image_div);
}

function render_blog_template(data, slot) {
    loader(false, loaderArea);
  slot.empty();
  var type_num = typeof(data.output.count);
  if(data.output === false || type_num === 'object') {
      slot.append('<p class="fs-20 text-black">No posts to display!</p>');
      return false;
  }
  totalItems = data.output.count;

  $.each(data.output.list, function(i, v){
    arr_cont.push(v.conttoken.toString());
      var prefix = '<?php echo TAOH_CDN_PREFIX ?>';
      if(v.blurb.media_type == 'youtube'){
        var video_id = getYoutubeId(v.blurb.media_url);
        v.blurb.image = "http://img.youtube.com/vi/"+video_id+"/maxresdefault.jpg";
        var image_div = `<div class="company-details-panel mb-30px" id="company-videos">
                <div class="pt-3 video-box">
                    <img class="w-100 rounded-rounded lazy" src="http://img.youtube.com/vi/${video_id}/maxresdefault.jpg" data-src="http://img.youtube.com/vi/${video_id}/maxresdefault.jpg" alt="video image">
                    <div class="video-content">
                        <a class="icon-element hover-y mx-auto blog_video" href="javascript:void(0);" onClick="call_iframe(event, this);" data-video="${video_id}" data-fancybox="" title="Play Video" style="padding-left:5px;">
                        <svg width="24" height="24" version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 58.752 58.752" xml:space="preserve" style="margin-top: -3px;">
                            <path fill="#0d233e" d="M52.524,23.925L12.507,0.824c-1.907-1.1-4.376-1.097-6.276,0C4.293,1.94,3.088,4.025,3.088,6.264v46.205
                            c0,2.24,1.204,4.325,3.131,5.435c0.953,0.555,2.042,0.848,3.149,0.848c1.104,0,2.192-0.292,3.141-0.843l40.017-23.103
                            c1.936-1.119,3.138-3.203,3.138-5.439C55.663,27.134,54.462,25.05,52.524,23.925z M49.524,29.612L9.504,52.716
                            c-0.082,0.047-0.18,0.052-0.279-0.005c-0.084-0.049-0.137-0.142-0.137-0.242V6.263c0-0.1,0.052-0.192,0.14-0.243
                            c0.042-0.025,0.09-0.038,0.139-0.038c0.051,0,0.099,0.013,0.142,0.038l40.01,23.098c0.089,0.052,0.145,0.147,0.145,0.249
                            C49.663,29.47,49.611,29.561,49.524,29.612z"></path>
                        </svg>
                        </a>
                    </div>
                </div>
            </div>`;
      }else{
        v.blurb.image = prefix+"/images/igcache/"+encodeURIComponent(v.title)+"/900_600/blog.jpg";
        var image_div = `<div class="cl-image cover-image-container">
                            <div class="glass-overlay"></div>
                            <div class="bg-image" style="background-image: url('${v.blurb.image}')"></div>
                            <a href="<?php echo TAOH_SITE_URL_ROOT.'/'.TAOH_CURR_APP_SLUG; ?>/learning/blog/${convertToSlug(taoh_title_desc_decode(v.title))}-${v.conttoken}" rel="bookmark">
                                <img class="title-hover latest-art-image" src="${v.blurb.image}" alt="${v.blurb.image}">
                            </a>
                        </div>`;
      }
      console.log('descp ---------- ',v.blurb.description);
      let Str = decode(v.blurb.description); 
      let decodedStr = decodeURIComponent(Str).replace(/\+/g, ' ');
      console.log('decode descp ---------- ',decodedStr);
      slot.append(`


                    <div class="td_module_12 mt-3 row align-items-start dash_metrics"
                    data-type="reads" data-metrics="view" conttoken="${v.conttoken}"                     
                    >

                        <div class="col-lg-6 mb-2 mb-lg-0">
                            <div class="td-post-image">
                                    ${image_div}
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="item-details">

                                <h3 class="line-clamp-2">
                                    <a class="" conttoken="${v.conttoken}" href="<?php echo TAOH_SITE_URL_ROOT.'/'.TAOH_CURR_APP_SLUG; ?>/blog/${convertToSlug(taoh_title_desc_decode(v.title))}-${v.conttoken}">

                                        ${taoh_title_desc_decode(v.title)}
                                    </a>
                                </h3>
                            </div>
                            <p class="line-clamp-3">
                                 ${decodedStr}
                            </p>
                            <div class="td-read-more mt-2">
                                <a href="<?php echo TAOH_SITE_URL_ROOT.'/'.TAOH_CURR_APP_SLUG; ?>/blog/${convertToSlug(taoh_title_desc_decode(v.title))}-${v.conttoken}">Read more</a>
                            </div>
                        </div>
                    </div>
                  <div>
                      <hr class="hr hr-blurry" />
                  </div>`
      );
  });
  if(totalItems >= 11) {
        $('#pagination').show();
        show_pagination('#pagination');
  }else{
        $('#pagination').hide();
  }
  //  taoh_metrix_ajax('reads',arr_cont);	 //kalpana check
}


function indx_reads_list(readslistdata){
    var reads_taoh_data = { taoh_data:readswork_list_name,values : readslistdata };
    let reads_setting_time = new Date();
    reads_setting_time = reads_setting_time.setMinutes(reads_setting_time.getMinutes() + 600);
    var reads_setting_timedata = { taoh_ttl: readswork_list_name,time:reads_setting_time };
    obj_data = { [store_name]:reads_taoh_data,[TTLStore] : reads_setting_timedata };
    Object.keys(obj_data).forEach(key => {
    // console.log(key, obj_data[key]);
        IntaoDB.setItem(key,obj_data[key]).catch((err) => console.log('Storage failed', err));
    });
    return false;
} // indexed db form submit

setInterval(function(){
    <?php if(TAOH_INTAODB_ENABLE) { ?>
        checkTTL(readswork_list_name,store_name);
    <?php } ?>
},30000);

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