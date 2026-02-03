<?php 
include_once('head_lp.php');
$category = '';
if(taoh_parse_url_lp(1) == 'category' && taoh_parse_url_lp(2) != '') {
   $category = taoh_parse_url_lp(2);
}
$url = "core.content.get";
$taoh_vals = array(
    'token' => taoh_get_dummy_token(1),
    'mod' => 'users',
    'ops' => 'list',
    'type' => 'tags_list',
    'tags' => 'latest',
    'cache_time' => 120,
    //'cfcc5h'=> 1, //cfcache newly added
);

// $cache_name = $url.'_tags_list_' . hash('sha256', $url . serialize($taoh_vals));
// $taoh_vals[ 'cfcache' ] = $cache_name;
// $taoh_vals[ 'cache_name' ] = $cache_name;
ksort($taoh_vals);


//echo taoh_apicall_get_debug($url, $taoh_vals);taoh_exit();
$response_tag = json_decode(taoh_apicall_get($url, $taoh_vals, '', 1), true);
if(isset($response_tag['success']) && $response_tag['success']) {
   $response_tags_api = $response_tag['output'];
   $slice_array = array_slice($response_tags_api, 0, 7, true);
}else{
   $slice_array = array();
}
?>
<style>
   .header-res-height {
      height: 170px;
  }
  @media (min-width: 375px) {
    .header-res-height {
        height: 185px;
    }
  }
  @media (min-width: 425px) {
    .header-res-height {
        height: 190px;
    }
  }
  @media (min-width: 500px) {
    .header-res-height {
        height: 200px;
    }
  }
  @media (min-width: 768px) {
    .header-res-height {
        height: 228px;
    }
  }
  @media (min-width: 900px) {
    .header-res-height {
        height: 280px;
    }
  }
  @media (min-width: 1024px) {
    .header-res-height {
        height: 228px;
    }
  }

</style>
<body id="top">
   <div class="wrapper-outer">
      <div class=""></div>
      <aside id="slide-out">
         <div class="search-mobile">
            <form method="get" id="searchform-mobile" action="<?php echo TAOH_READS_LP_URL; ?>/search">
               <button class="search-button" type="submit" value="Search"><i class="fa fa-search"></i></button>
               <input type="text" id="s-mobile" name="s" title="Search" value="Search" onfocus="if (this.value == 'Search') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search';}">
            </form>
         </div>
         <!-- .search-mobile /-->
         <div class="social-icons">
            <!-- <a class="ttip-none" title="Rss" href="https://grants.club/feed/" target="_blank"><i class="fa fa-rss"></i></a> -->
            <a class="ttip-none" title="Facebook" href="https://www.facebook.com/taoaihq" target="_blank"><i class="fa fa-facebook"></i></a>
            <a class="ttip-none" title="Twitter" href="https://twitter.com/taoaihq" target="_blank"><i class="fa fa-twitter"></i></a>
         </div>
         <div id="mobile-menu">
            <div class="main-menu">
               <ul id="menu-primary-menu" class="menu">
                  <li id="menu-item-1061" class="menu-item menu-item-type-custom menu-item-object-custom current-menu-item current_page_item menu-item-home menu-item-1061"><a href="<?php echo TAOH_READS_LP_URL; ?>">Home</a></li>
                  <?php
                     foreach($slice_array as $header2_keys => $header2_val) {
                        echo '<li id="menu-item-1063" class="menu-item menu-item-type-taxonomy menu-item-object-category menu-item-1063"><a href="'.taoh_lp_category_link().'/'.$header2_val.'">'.ucwords(str_replace("_"," ",$header2_val)).'</a></li>';
                     }  
                  ?>
               </ul>
            </div>
         </div>
      </aside>
      <!-- #slide-out /-->
      <div id="wrapper" class="boxed">
         <div class="inner-wrapper">
            <div class="header-res-height">
               <header style="position: fixed; top: 0; width: 100%; background: #ffffff; z-index: 100001;">
                  <div id="theme-header" class="container theme-header mb-0" style="margin-bottom: 0;">
                     <div id="top-nav" class="top-nav">
                        <div class="container">
                           <div class="top-menu">
                              <ul id="menu-top-menu" class="menu">
                              <?php
                              $headers1 = json_decode(TAOH_MENU_HEADER_1);
                              //print_r($headers1->title);
                              foreach($headers1 as $header1_keys => $header1_val) {
                                 echo '<li id="menu-item-1072" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1072"><a href="'.$header1_val.'">'.$header1_keys.'</a></li>';
                              }  
                              ?>
                              </ul>
                           </div>
                           <div class="search-block">
                              <form method="get" id="searchform-header" action="<?php echo TAOH_READS_LP_URL; ?>/search">
                                 <button class="search-button" type="submit" value="Search"><i class="fa fa-search"></i></button>
                                 <input class="search-live" type="text" id="s-header" name="q" title="Search" value="Search" onfocus="if (this.value == 'Search') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search';}" autocomplete="off">
                              </form>
                           </div>
                           <!-- .search-block /-->
                           <div class="social-icons">
                              <!-- <a class="ttip-none" title="Rss" href="https://grants.club/feed/" target="_blank"><i class="fa fa-rss"></i></a> -->
                              <a class="ttip-none" title="Facebook" href="https://www.facebook.com/taoaihq" target="_blank"><i class="fa fa-facebook"></i></a>
                              <a class="ttip-none" title="Twitter" href="https://twitter.com/taoaihq" target="_blank"><i class="fa fa-twitter"></i></a>
                           </div>
                        </div>
                        <!-- .container /-->
                     </div>
                     <!-- .top-menu /-->	
                     <div class="header-content" style="position: relative;">
                        <a id="slide-out-open" class="slide-out-open" href="#top-nav"><span></span></a>
                        <div class="logo">
                           <a href="<?php echo $taoh_home_url . "/../"; ?>" class="logo">
                           <img src="<?php echo (defined('TAOH_PAGE_LOGO')) ? TAOH_PAGE_LOGO : TAOH_SITE_LOGO; ?>" alt="logo" style="max-height: 33px; width: auto;">
                           </a>                        
                        </div>
                        <!-- .logo /-->
                        <div class="e3lan e3lan-top">
                           <?php
                              $add_banner1 = json_decode(TAOH_ADS_BANNERS,true);
                              $banner_first = $add_banner1[0];
                              //print_r($add_banner1);
                              echo '<a href="'.$banner_first['OUTSIDEURL'].'" title="'.$banner_first['TITLE'].'" target="_blank">
                                       <img src="'.$banner_first['IMAGEURL'].'" alt="'.$banner_first['ALT_TEXT'].'">
                                    </a>';
                           ?>  
                        </div>
                        <div class="clear"></div>
                     </div>
                     <nav id="main-nav">
                        <div class="container header-flex">
                           <div class="main-menu">
                              <ul id="menu-primary-menu" class="menu">
                              <li id="menu-item-1061" class="menu-item menu-item-type-custom menu-item-object-custom current_page_item menu-item-home menu-item-1061"><a href="<?php echo TAOH_LP_HOME_URL; ?>">Home</a></li>
                              <?php
                                 foreach($slice_array as $header2_keys => $header2_val) {
                                    if(strtolower($header2_val) == strtolower($category)){
                                       echo '<li id="menu-item-1063" class="menu-item menu-item-type-taxonomy menu-item-object-category menu-item-1063 current-menu-item"><a href="'.taoh_lp_category_link().'/'.$header2_val.'">'.ucwords(str_replace("_"," ",$header2_val)).'</a></li>';
                                    }else{
                                       echo '<li id="menu-item-1063" class="menu-item menu-item-type-taxonomy menu-item-object-category menu-item-1063 "><a href="'.taoh_lp_category_link().'/'.$header2_val.'">'.ucwords(str_replace("_"," ",$header2_val)).'</a></li>';
                                    }
                                 }  
                              ?>
                              <?php if(count($slice_array) > 1){ 
                                 if(taoh_parse_url_lp(1) == ''){ ?>
                                 <li id="menu-item-1063" class="menu-item menu-item-type-taxonomy menu-item-object-category menu-item-1063 current-menu-item"><a href="<?php echo TAOH_SITE_DOC_ROOT_FILE.'blog'; ?>">Blogs</a></li> 
                              <?php }else{ ?>
                                 <li id="menu-item-1063" class="menu-item menu-item-type-taxonomy menu-item-object-category menu-item-1063"><a href="<?php echo TAOH_SITE_DOC_ROOT_FILE.'blog'; ?>">Blogs</a></li>
                              <?php } } ?>
                              </ul>
                           </div>
                           <!-- <a href="https://grants.club/?tierand=1" class="random-article ttip" original-title="Random Article">
                              <i class="fa fa-random"></i>
                           </a> -->						
                        </div>
                     </nav>
                     <!-- .main-nav /-->
                  </div>
               </header>
            </div>
            
            
            <!-- #header /-->
<!-- <script>
   window.onscroll = function() { myFunction(); };
   var header_lp = document.getElementById("theme-header");
   var sticky = header_lp.offsetTop; 
   
   function myFunction() {
      if (window.pageYOffset > sticky) {
         header_lp.classList.add("fixed-header-lp");
      } else {
         header_lp.classList.remove("fixed-header-lp");
      }
   }
</script>	 -->
