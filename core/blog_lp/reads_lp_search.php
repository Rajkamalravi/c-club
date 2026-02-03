<?php 
    taoh_lp_get_header();
    $ajax_url = TAOH_TEMP_SITE_FILE_PARSE.TAOH_TEMP_SITE_URL.'/ajax';
    $action = taoh_parse_url_lp(1);
    $category = taoh_parse_url_lp(2);
    //print_r($action);die;
    $search = ( isset( $_GET[ 'q' ] ) ) ? $_GET[ 'q' ]:urlencode($category);
    if(isset($action) && str_contains($action,'search')){
        $text = 'Search results for "'.$search.'"';
    }else{
        $text = ucwords($category);
    }

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
?>
<div id="breaking-news" class="breaking-news">
   <span class="breaking-news-title"><i class="fa fa-bolt"></i> <span>Breaking News</span></span>
   <ul class="innerFade" style="position: relative; height: 31.2px;">
      <li id="changeText"></li>
   </ul>
</div>
<!-- .breaking-news -->
<div id="main-content" class="container">
    <div class="content">
        <div class="page-head">
            <h1 class="page-title">
                <?php echo $text; ?>			
            </h1>
            <div class="stripe-line"></div>
        </div>
        <div class="post-listing archive-box">
            <div id="loaderArea"></div>
            <div id="searchArea"></div>
            <!-- .item-list -->
            <div id="pagination" class="pag-pad"></div>
        </div>
    </div>
    <!-- .content -->
    <aside id="sidebar" style="position: relative; overflow: visible; box-sizing: border-box; min-height: 1px;">
        <?php taoh_side_bar(); ?>
    </aside>
    <!-- #sidebar /-->	
    <div class="clear"></div>
</div>
<!-- .container /-->
<script type="text/javascript">
    let recentArea = $('#searchArea');
    let loaderArea = $('#loaderArea');
    let itemsPerPage = 10;
    let currentPage = 1;
    let totalItems = 0; //this will be rewriiten on response of assks on line 307
    let searchText = '<?php echo $search; ?>';
    var blog_already_rendered = false;
	var blog_list_name = "";
    let store_name = READStore;

    $(document).ready(function(){
        <?php if(TAOH_INTAODB_ENABLE) { ?>			
            get_lp_bloglistdata();
        <?php }else{ ?>
            taoh_blogs_init();
        <?php } ?>
    });

    function get_lp_bloglistdata(){
		// Open or create a database
		getIntaoDb(dbName).then((db) => {
            var currpage = currentPage-1;
            var blog_list_hash = currpage+itemsPerPage+searchText;
            blog_list_name = 'tags_lp_search_'+crc32(blog_list_hash);
            const datablogrequest = db.transaction(store_name).objectStore(store_name).get(blog_list_name); // get main data
            datablogrequest.onsuccess = ()=> {
                const blogtoredatares = datablogrequest.result;
                if(blogtoredatares !== undefined && blogtoredatares !== null && blogtoredatares !== "" && blogtoredatares !== "undefined" && blogtoredatares !== "null"){
                    const blogstoredata = datablogrequest.result.values;
                    var blog_already_rendered = true;
                    loader(false, loaderArea);
                    render_blog_template(blogstoredata, recentArea);
                }else{
                    loader(true, loaderArea);
                    taoh_blogs_init();
                }
            }
		}).catch((error) => {
           console.log('Getlpsearchreadslistdata Error:', error);
       	});
	}

    <?php if($new_rand['success'] && $new_rand['output']['list']) { ?>
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
    <?php } ?>

    $('.widget-top ul li').click(function(){
      $('.widget-top ul li').removeClass('active');
      $(this).addClass('active');
      var tab = $(this).find('a').attr('data-href');
      $('.tabs-wrap').hide();
      $(tab).show();
   });

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
            blog_already_rendered = false;
            <?php if(TAOH_INTAODB_ENABLE) { ?>
                get_lp_bloglistdata();
            <?php }else{ ?>
                taoh_blogs_init();
            <?php } ?>
            }
        });
    }

    function taoh_blogs_init() {
        loader(true, loaderArea);
        var data = {
            'taoh_action': 'taoh_central_tag_get',
            'ops': 'list',
            'offset': currentPage,
            'limit': itemsPerPage,
            <?php if($_GET['q']){ ?>
                'search': searchText,
            <?php }else{ ?>
                'tags': searchText,
            <?php } ?>   
        };
        jQuery.post("<?php echo $ajax_url; ?>", data, function(response) {
            console.log('data',response);
            loader(false, loaderArea);
            <?php if(TAOH_INTAODB_ENABLE) { ?>
				indx_blogs_lp_list(response);
				if(!blog_already_rendered){
					render_blog_template(response, recentArea);
				}
			<?php }else{ ?>
				render_blog_template(response, recentArea);
			<?php } ?>
        }).fail(function() {
            console.log( "Network issue!" );
            loader(false, loaderArea);
        })
    }

  function render_blog_template(data, slot) {
    slot.empty();
    var type_num = typeof(data.output.num_rows);
    if(data.output === false || type_num === 'object') {
        slot.append('<p class="fs-20 ml-4 text-black">No posts to display!</p>');
        return false;
    }
    totalItems = data.output.count;

    $.each(data.output.list, function(i, v){
        var prefix = '<?php echo TAOH_CDN_PREFIX ?>';
        if(v.blurb.image){
            v.blurb.image = v.blurb.image;
        }else{
            v.blurb.image = prefix+"/images/igcache/"+encodeURIComponent(v.title)+"/900_600/blog.jpg";
        }
        let decodedStr = htmlDecode(v.blurb.description); 
        slot.append(`
                <article class="item-list">
                    <h2 class="post-box-title">
                        <a href="<?php echo TAOH_READS_LP_URL;?>/d/${convertToSlug(v.title)}-${v.conttoken}">${v.title}</a>
                    </h2>
                    <div class="post-thumbnail tie-appear">
                        <a href="<?php echo TAOH_READS_LP_URL;?>/d/${convertToSlug(v.title)}-${v.conttoken}">
                        <img width="310" height="165" src="${v.blurb.image}" class="attachment-tie-medium size-tie-medium wp-post-image tie-appear"><span class="fa overlay-icon"></span>
                        </a>
                    </div>
                    <!-- post-thumbnail /-->
                    <div class="entry">
                        <p class="claimedRight">${decodedStr.slice(0,500)} …</p>
                        <a class="more-link" href="<?php echo TAOH_READS_LP_URL;?>/d/${convertToSlug(v.title)}-${v.conttoken}">Read More »</a>
                    </div>
                    <div class="clear"></div>
                </article>`
                    
        );
    });
    if(totalItems >= 11){
        show_pagination('#pagination');
    }
}

function indx_blogs_lp_list(bloglistdata){
      var blog_taoh_data = { taoh_data:blog_list_name,values : bloglistdata };
      let blog_setting_time = new Date();
      blog_setting_time = blog_setting_time.setHours(blog_setting_time.getHours() + 2);
      var blog_setting_timedata = { taoh_ttl: blog_list_name,time:blog_setting_time };
      obj_data = { [store_name]:blog_taoh_data,[TTLStore] : blog_setting_timedata };
      Object.keys(obj_data).forEach(key => {
      // console.log(key, obj_data[key]);
         IntaoDB.setItem(key,obj_data[key]).catch((err) => console.log('Storage failed', err));
      });
      return false;
   } // indexed db form submit

    setInterval(function(){
        <?php if(TAOH_INTAODB_ENABLE) { ?>
            checkTTL(blog_list_name,store_name);
        <?php } ?>
    },10000);

</script>
<?php 
   taoh_lp_get_footer();
?> 