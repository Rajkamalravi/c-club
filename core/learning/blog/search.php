<?php
include 'reads_css.php';
taoh_get_header();
$reads_type = 'hires';
$search = ( isset( $_GET[ 'q' ] ) ) ? $_GET[ 'q' ]:'';

$category = ucwords(str_replace('-', ' ', $search));

$get_widget = taoh_wellness_widget_get($reads_type);

?>
<style>
  @media (min-width: 1280px){
  .container {
      max-width: 1021px;
  }
  .ser-image{
    width:100%;
    height:186px;
  }
  }
</style>
<section class="blog-listing gray-dark">
  <div class="container">
    <div id="intro" class="pt-3">
      <ul class="breadcrumb-list pb-2">
        <li><a href="<?php echo TAOH_SITE_URL_ROOT; ?>">Home</a><span><svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"/></svg></span></li>
        <li><a href="<?php echo TAOH_READS_URL; ?>">Blogs</a><span><svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"/></svg></span></li>
        <li><a><?php echo $category; ?></a></li>
      </ul>
        <!-- <h1 class="mb-0 text-left"><?php //echo TAO_PAGE_TITLE ?></h1> -->
    </div>
    <div class="row bg-white">
        <div class="col-lg-8 m-15px-tb">
          <div class="mt-3 sticky-top light-dark">
              <?php taoh_reads_search_widget(); ?>
          </div>
          <div id='loaderArea'></div>
          <div id="eventArea" class="row"></div>
          <div class="col-lg-12 m-4" id="pagination"></div>
        </div>
        <div class="col-lg-4 m-15px-tb border-left">
          <section id="sticky">
              <div class="border-bottom">
                  <?php tags_widget(); ?>
              </div>
              <div class="border-bottom p-4">
                  <?php if (function_exists('taoh_get_recent_jobs')) { taoh_get_recent_jobs('new');  } ?>
              </div>
              <?php //taoh_all_reads_widget( $get_widget[ 'right2' ], 'right2' ); ?>
              <?php taoh_all_reads_widget( $get_widget[ 'right3' ], 'right3' ); ?>
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
  let searchText = '<?php echo $search; ?>';
  let arr_cont = [];
  var already_rendered = false;
  var reads_list_name = '';
  var store_name = READStore;
  var search = '<?php echo $search; ?>';

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
				var reads_list_hash = queryString+search+currpage+itemsPerPage;
				reads_list_name = 'readsearch_'+crc32(reads_list_hash);
				console.log(reads_list_name);
				const datareadsrequest = db.transaction(store_name).objectStore(store_name).get(reads_list_name); // get main data
				datareadsrequest.onsuccess = ()=> {
					console.log(datareadsrequest);
					const readsstoredatares = datareadsrequest.result;
					if(readsstoredatares !== undefined && readsstoredatares !== null && readsstoredatares !== "" && readsstoredatares !== "undefined" && readsstoredatares !== "null"){
						const readsstoredata = datareadsrequest.result.values;
						already_rendered = true;
						//render_asks_template(asks, listChatRooms);
						render_blog_template(readsstoredata, eventArea);
						//taoh_blogs_init(queryString);
					}else{
						taoh_blogs_init(queryString);
						//taoh_blogs_init();
					}
				}
			}).catch((error) => {
          console.log('Getsearchreadslistdata Error:', error);
      });
	}

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
        'search': '<?php echo $search; ?>',

    };
    jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
        console.log('data',response);
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
      loader(false, loaderArea);
    })
}

  function render_blog_template(data, slot) {
  loader(false, loaderArea);
  slot.empty();
  var type_num = typeof(data.output.count);
  if(data.output === false || type_num === 'object') {
      slot.append('<p class="fs-20 ml-4 text-black">No posts to display!</p>');
      return false;
  }
  totalItems = data.output.count;

  $.each(data.output.list, function(i, v){
     arr_cont.push(v.conttoken.toString());
      var prefix = '<?php echo TAOH_CDN_PREFIX ?>';
      v.blurb.image = prefix+"/images/igcache/"+encodeURIComponent(v.title)+"/900_600/blog.jpg";
      slot.append(`
              <div class="col-lg-6">
                <div class="p-3">
                    <div class="cover-image-container">
                      <div class="glass-overlay"></div>
                      <div class="bg-image" style="background-image: url('${v.blurb.image}')"></div>
                      <a class="mt-2 dash_metrics" data-metrics="view" data-type="reads" conttoken="${v.conttoken}" href="blog/${convertToSlug(taoh_title_desc_decode(v.title))}-${v.conttoken}">
                          <img width="100%" class="ser-image main-image" style="max-height: 330px;" src="${v.blurb.image}" data-src="${v.blurb.image}" alt="Card image">
                      </a>
                    </div>
                    <h3  class="mt-2 h3-title">
                        <a class="" href="<?php echo TAOH_SITE_URL_ROOT.'/'.TAOH_CURR_APP_SLUG; ?>/blog/${convertToSlug(taoh_title_desc_decode(v.title))}-${v.conttoken}">
                          ${taoh_title_desc_decode(v.title)}
                        </a>
                    </h3>
                </div>
              </div>`

      );
  });
  if(totalItems >= 11) {
        $('#pagination').show();
        show_pagination('#pagination');
  }else{
        $('#pagination').hide();
  }
  //alert(searchText);
  if(searchText){
    taoh_metrix_ajax('reads',arr_cont);
  }
}

function indx_reads_list(readslistdata){
    var reads_taoh_data = { taoh_data:reads_list_name,values : readslistdata };
    let reads_setting_time = new Date();
    reads_setting_time = reads_setting_time.setMinutes(reads_setting_time.getMinutes() + 600);
    var reads_setting_timedata = { taoh_ttl: reads_list_name,time:reads_setting_time };
    obj_data = { [store_name]:reads_taoh_data,[TTLStore] : reads_setting_timedata };
    Object.keys(obj_data).forEach(key => {
    // console.log(key, obj_data[key]);
        IntaoDB.setItem(key,obj_data[key]).catch((err) => console.log('Storage failed', err));
    });
    return false;
} // indexed db form submit

setInterval(function(){
      <?php if(TAOH_INTAODB_ENABLE) { ?>
				checkTTL(reads_list_name,store_name);
			<?php } ?>
},30000);
$('.claimedRight').each(function (f) {
  var newstr = $(this).text().substring(0,250)+'....';
  $(this).text(newstr);
});

</script>
<?php taoh_get_footer();  ?>