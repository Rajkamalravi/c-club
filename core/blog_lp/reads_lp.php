<?php
   taoh_lp_get_header();
   $ajax_url = TAOH_TEMP_SITE_FILE_PARSE.TAOH_TEMP_SITE_URL.'/ajax';
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

   $cent1_rand = blog_lp_related_post($response_tags[0],5);
   $cent2_rand = blog_lp_related_post($response_tags[1],3);
   $cent3_rand = blog_lp_related_post($response_tags[2],3);
   $cent4_rand = blog_lp_related_post($response_tags[3],3);
   $int_rand = blog_lp_related_post($response_tags[4],10);
   $brandrand = blog_lp_related_post($response_tags[5],5);
   $res_rand = blog_lp_related_post($response_tags[6],12);


?>
<!-- <div id="breaking-news" class="breaking-news container">
   <span class="breaking-news-title"><i class="fa fa-bolt"></i> <span>Breaking News</span></span>
   <ul class="innerFade" style="position: relative; height: 31.2px;">
      <li id="changeText"></li>
   </ul>
</div> -->
<!-- .breaking-news -->
<div id="main-content" class="container">
   <?php
      if($hero){
         taoh_lp_blog_satart($hero);
      }
   ?>
   <div class="content">
      <?php
         if($cent1_rand['success'] && $cent1_rand['output']['list']) { ?>
         <section class="cat-box list-box tie-cat-76">
            <div class="cat-box-title">
               <h2><?php echo ucwords(str_replace("_"," ",$response_tags[0])); ?></h2>
               <div class="stripe-line"></div>
            </div>
            <div class="cat-box-content">
               <ul>
                  <?php
                     taoh_all_lp_central_widget1($cent1_rand['output']['list']);
                  ?>
               </ul>
            </div>
         </section>
         <?php } ?>
      <!-- List Box -->
       <?php
         if($cent2_rand['success'] && $cent2_rand['output']['list']) { ?>
            <section class="cat-box column2 tie-cat-52" style="width: 100%;">
               <div class="cat-box-title">
                  <h2><?php echo ucwords(str_replace("_"," ",$response_tags[1])); ?></h2>
                  <div class="stripe-line"></div>
               </div>
               <div class="cat-box-content">
                  <ul>
                     <?php
                        taoh_all_lp_central_widget2($cent2_rand['output']['list']);
                     ?>
                  </ul>
               </div>
               <!-- .cat-box-content /-->
            </section>
         <?php } ?>
      <!-- Two Columns -->
       <?php
         if($cent3_rand['success'] && $cent3_rand['output']['list']) { ?>
            <section class="cat-box column2 tie-cat-50 last-column" style="width: 100%;">
               <div class="cat-box-title">
                  <h2><?php echo ucwords(str_replace("_"," ",$response_tags[2])); ?></h2>
                  <div class="stripe-line"></div>
               </div>
               <div class="cat-box-content">
                  <ul>
                     <?php
                        taoh_all_lp_central_widget2($cent3_rand['output']['list']);
                     ?>
                  </ul>
               </div>
               <!-- .cat-box-content /-->
            </section>
         <?php } ?>
      <!-- Two Columns -->
       <?php
         if($cent4_rand['success'] && $cent4_rand['output']['list']) { ?>
            <section class="cat-box scroll-box tie-cat-49">
               <div class="cat-box-title">
                  <h2><?php echo ucwords(str_replace("_"," ",$response_tags[3])); ?></h2>
                  <div class="stripe-line"></div>
               </div>
               <div class="cat-box-content">
                  <div id="slideshow49" class="group_items-box" style="position: relative; width: 620px; height: 200px; overflow: hidden;">
                     <div class="group_items" style="background-color: rgb(0, 0, 0); position: absolute; top: 0px; left: 0px; z-index: 3; opacity: 1; display: block;">
                        <?php
                           taoh_all_lp_central_widget3($cent4_rand['output']['list']);
                        ?>
                     </div>
                     <div class="clear"></div>
                  </div>
                  <div id="nav49" class="scroll-nav"><!-- <a href="#" class="activeSlide">1</a><a href="#" class="">2</a> --></div>
               </div>
               <!-- .cat-box-content /-->
            </section>
         <?php } ?>
      <div class="clear"></div>
      <?php if($int_rand['success'] && $int_rand['output']['list']) { ?>
         <section class="cat-box pic-box tie-cat-41 clear">
            <div class="cat-box-title">
               <h2><?php echo ucwords(str_replace("_"," ",$response_tags[4])); ?></h2>
               <div class="stripe-line"></div>
            </div>
            <div class="cat-box-content">
               <ul>
                  <?php
                     taoh_int_releated($int_rand['output']['list']);
                  ?>
               </ul>
               <div class="clear"></div>
            </div>
            <!-- .cat-box-content /-->
         </section>
      <?php } ?>
      <?php if($brandrand['success'] && $brandrand['output']['list']) { ?>
         <section class="cat-box wide-box tie-cat-57">
            <div class="cat-box-title">
               <h2><?php echo ucwords(str_replace("_"," ",$response_tags[5])); ?></h2>
               <div class="stripe-line"></div>
            </div>
            <div class="cat-box-content">
               <ul>
                  <?php
                     taoh_brand_releated($brandrand['output']['list']);
                  ?>
               </ul>
               <div class="clear"></div>
            </div>
            <!-- .cat-box-content /-->
         </section>
      <?php } ?>
      <!-- Wide Box -->
      <?php if($res_rand['success'] && $res_rand['output']['list']) { ?>
         <section class="cat-box pic-box tie-cat-50 clear pic-grid">
            <div class="cat-box-title">
               <h2><?php echo ucwords(str_replace("_"," ",$response_tags[6])); ?></h2>
               <div class="stripe-line"></div>
            </div>
            <div class="cat-box-content">
               <ul>
                  <?php
                     taoh_resume_releated($res_rand['output']['list']);
                  ?>
               </ul>
               <div class="clear"></div>
            </div>
            <!-- .cat-box-content /-->
         </section>
      <?php } ?>

      <div class="cat-box-content clear cat-box">
         <div class="cat-tabs-header">
            <ul>
               <li class="active"><a data-href="#catab49"><?php echo ucwords(str_replace("_"," ",$response_tags[7])); ?></a></li>
               <li><a data-href="#catab57"><?php echo ucwords(str_replace("_"," ",$response_tags[8])); ?></a></li>
               <li><a data-href="#catab80"><?php echo ucwords(str_replace("_"," ",$response_tags[9])); ?></a></li>
               <li><a data-href="#catab01"><?php echo ucwords(str_replace("_"," ",$response_tags[10])); ?></a></li>
               <li><a data-href="#catab03"><?php echo ucwords(str_replace("_"," ",$response_tags[11])); ?></a></li>
            </ul>
         </div>
         <div id="catab49" class="cat-tabs-wrap cat-tabs-wrap1">
            <ul>
               <?php
                $learn_rand = blog_lp_related_post($response_tags[7],5);
                  if($learn_rand['success'] && $learn_rand['output']['list']) {
                     taoh_learn_releated($learn_rand['output']['list']);
                  }
               ?>
            </ul>
            <div class="clear"></div>
         </div>
         <div id="catab57" class="cat-tabs-wrap cat-tabs-wrap2" style="display: none;">
            <ul>
               <?php
                $mind_rand = blog_lp_related_post($response_tags[8],5);
                  if($mind_rand['success'] && $mind_rand['output']['list']) {
                     taoh_mind_releated($mind_rand['output']['list']);
                  }
               ?>
            </ul>
            <div class="clear"></div>
         </div>
         <div id="catab80" class="cat-tabs-wrap cat-tabs-wrap3" style="display: none;">
            <ul>
               <?php
                  $prod_rand = blog_lp_related_post($response_tags[9],5);
                  if($prod_rand['success'] && $prod_rand['output']['list']) {
                     taoh_prod_releated($prod_rand['output']['list']);
                  }
               ?>
            </ul>
            <div class="clear"></div>
         </div>
         <div id="catab01" class="cat-tabs-wrap cat-tabs-wrap3" style="display: none;">
            <ul>
               <?php
                  $net_rand = blog_lp_related_post($response_tags[10],5);
                  if($net_rand['success'] && $net_rand['output']['list']) {
                     taoh_net_releated($net_rand['output']['list']);
                  }
               ?>
            </ul>
            <div class="clear"></div>
         </div>
         <div id="catab03" class="cat-tabs-wrap cat-tabs-wrap3" style="display: none;">
            <ul>
               <?php
                  $stress_rand = blog_lp_related_post($response_tags[11],5);
                  if($stress_rand['success'] && $stress_rand['output']['list']) {
                     taoh_stress_releated($stress_rand['output']['list']);
                  }
               ?>
            </ul>
            <div class="clear"></div>
         </div>
      </div>
      <!-- #cats-tabs-box /-->
      <section class="cat-box recent-box recent-blog">
         <div class="cat-box-title">
            <h2>Recent Posts</h2>
            <div class="stripe-line"></div>
         </div>
         <div class="cat-box-content">
            <div class="loaderArea"></div>
            <div id="recentArea"></div>
            <!-- .item-list -->
            <div id="pagination" class="pag-pad" style="overflow-x: auto;"></div>
            <div class="clear"></div>
         </div>
         <!-- .cat-box-content /-->
      </section>
      <div class="clear"></div>
   </div>
   <!-- .content /-->
   <aside id="sidebar" style="position: relative; overflow: visible; box-sizing: border-box; min-height: 1px;">
      <?php taoh_side_bar(); ?>
      <!-- .theiaStickySidebar /-->
   </aside>
   <!-- #sidebar /-->
   <div class="clear"></div>
</div>
<!-- .container /-->
<script type="text/javascript">
   let recentArea = $('#recentArea');
   let loaderArea = $('.loaderArea');
   let itemsPerPage = 5;
   let currentPage = 1;
   let totalItems = 0; //this will be rewriiten on response of assks on line 307
   var blog_already_rendered = false;
	var blog_list_name = "";
   let store_name = READStore;
   let category = 'uncategorized';

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
         var blog_list_hash = 'list'+currpage+itemsPerPage+category;
         blog_list_name = 'tags_lp_'+crc32(blog_list_hash);
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
         console.log('Getlpreadslistdata Error:', error);
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

   $('.cat-tabs-header ul li').click(function(){
      $('.cat-tabs-header ul li').removeClass('active');
      $(this).addClass('active');
      var tab = $(this).find('a').attr('data-href');
      $('.cat-tabs-wrap').hide();
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
         onPageClick: function(pageNumber, blog) {
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
         'category': category,
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
                     <h2 class="post-box-title" style="margin-bottom: 10px;"><a href="<?php echo TAOH_READS_LP_URL;?>/blog/${convertToSlug(v.title)}-${v.conttoken}" rel="bookmark">${v.title}</a></h2>
                     <div class="post-thumbnail tie-appear">
                        <a href="<?php echo TAOH_READS_LP_URL;?>/blog/${convertToSlug(v.title)}-${v.conttoken}" rel="bookmark">
                        <img width="310" height="165" src="${v.blurb.image}" class="attachment-tie-medium size-tie-medium wp-post-image tie-appear" alt="${v.blurb.image}"><span class="fa overlay-icon"></span>
                        </a>
                     </div>
                     <!-- post-thumbnail /-->
                     <div class="entry">
                        <p>${decodedStr.slice(0,500)} …</p>
                        <a class="more-link" href="<?php echo TAOH_READS_LP_URL;?>/blog/${convertToSlug(v.title)}-${v.conttoken}">Read More »</a>
                     </div>
                     <div class="clear"></div>
                  </article>
                  `
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