          <?php
          $url = "content.get.taglist";
          $taoh_vals = array(
              'secret' => TAOH_API_SECRET,
              'token' => taoh_get_dummy_token(),
              'cache_name' => 'tags_list_footer_' . taoh_get_dummy_token() . '_' . TAOH_ROOT_PATH_HASH,
              'cache_time' => 120,
              //'cfcc5h'=> 1, //cfcache newly added
          );
            //echo taoh_apicall_get_debug($url, $taoh_vals);taoh_exit();
            $response_tag_ft = json_decode(taoh_apicall_get($url, $taoh_vals,'',1), true);
            if(isset($response_tag_ft['success']) && $response_tag_ft['success']) {
              $response_tags = $response_tag_ft['output'];
            }else{
              $response_tags = array();
            }
          ?>
          <div class="e3lan e3lan-bottom">
            <?php
              $add_banner2 = json_decode(TAOH_ADS_BANNERS,true);
              $banner_two = $add_banner2[1];
              //print_r($add_banner1);
              echo '<a href="'.$banner_two['OUTSIDEURL'].'" title="'.$banner_two['TITLE'].'" target="_blank">
                        <img src="'.$banner_two['IMAGEURL'].'" alt="'.$banner_two['ALT_TEXT'].'">
                    </a>';
            ?>
          </div>
          <footer id="theme-footer">
            <div class="container">
              <div id="footer-widget-area" class="footer-3c">
                <div id="footer-first" class="footer-widgets-box">
                  <div id="posts-list-widget-2" class="footer-widget posts-list"><div class="footer-widget-top"><h4 class="grey-col"><?php echo ucwords($response_tags[0]); ?>		</h4></div>
                    <div class="footer-widget-container">
                      <ul>
                        <?php
                          $learn_foot_rand = blog_lp_related_post($response_tags[0],3);
                          if($learn_foot_rand['success'] && $learn_foot_rand['output']['list']) {
                          foreach ($learn_foot_rand['output']['list'] as $first_key => $first_val){ 
                            if ( ! isset( $first_val['image']) || ! $first_val['image'] || stristr( $first_val['image'], 'images.unsplash.com' ) ) $first_val['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( $first_val['title'] )."/900_600/blog.jpg";
                        ?>
                        <li>
                          <div class="post-thumbnail tie-appear">
                            <a href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>" title="<?php echo $first_val['title']; ?>" rel="bookmark">
                              <img width="110" height="75" src="<?php echo $first_val['image']; ?>" class="attachment-tie-small size-tie-small wp-post-image" alt="" decoding="async" loading="lazy" srcset="<?php echo $first_val['image']; ?> 110w, <?php echo $first_val['image']; ?> 220w, <?php echo $first_val['image']; ?> 330w" sizes="(max-width: 110px) 100vw, 110px"><span class="fa overlay-icon"></span>
                            </a>
                          </div><!-- post-thumbnail /-->
                          <h3 style="overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; text-overflow: ellipsis;"><a class="css-font" href="<?php echo taoh_lp_blog_link(slugify2($first_val['title'])."-".$first_val['conttoken']); ?>"><?php echo $first_val['title']; ?></a></h3>
                          <!-- <span class="tie-date"><i class="fa fa-clock-o"></i>January 22, 2024</span>	 -->						
                        </li>
                        <?php }
                          } 
                        ?>
                      </ul>
                      <div class="clear"></div>
                    </div>
                  </div><!-- .widget /-->		
                </div>
                <div id="footer-second" class="footer-widgets-box">
                  <div id="posts-list-widget-3" class="footer-widget posts-list"><div class="footer-widget-top"><h4 class="grey-col"><?php echo ucwords($response_tags[1]); ?>		</h4></div>
                    <div class="footer-widget-container">				
                      <ul>
                        <?php
                          $res_foot_rand = blog_lp_related_post($response_tags[1],3);
                          if($res_foot_rand['success'] && $res_foot_rand['output']['list']) {
                          foreach ($res_foot_rand['output']['list'] as $res_first_key => $res_first_val){ 
                            if ( ! isset( $res_first_val['image']) || ! $res_first_val['image'] || stristr( $res_first_val['image'], 'images.unsplash.com' ) ) $res_first_val['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( $res_first_val['title'] )."/900_600/blog.jpg";
                        ?>
                        <li>
                          <div class="post-thumbnail tie-appear">
                            <a href="<?php echo taoh_lp_blog_link(slugify2($res_first_val['title'])."-".$res_first_val['conttoken']); ?>" title="<?php echo $res_first_val['title']; ?>" rel="bookmark">
                              <img width="110" height="75" src="<?php echo $res_first_val['image']; ?>" class="attachment-tie-small size-tie-small wp-post-image" alt="" decoding="async" loading="lazy" srcset="<?php echo $res_first_val['image']; ?> 110w, <?php echo $res_first_val['image']; ?> 220w, <?php echo $res_first_val['image']; ?> 330w" sizes="(max-width: 110px) 100vw, 110px"><span class="fa overlay-icon"></span>
                            </a>
                          </div><!-- post-thumbnail /-->
                          <h3 style="overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; text-overflow: ellipsis;"><a class="css-font" href="<?php echo taoh_lp_blog_link(slugify2($res_first_val['title'])."-".$res_first_val['conttoken']); ?>"><?php echo $res_first_val['title']; ?></a></h3>
                          <!-- <span class="tie-date"><i class="fa fa-clock-o"></i>January 22, 2024</span>	 -->						
                        </li>
                        <?php }
                          } 
                        ?>
                      </ul>
                      <div class="clear"></div>
                    </div>
                  </div><!-- .widget /-->		
                </div><!-- #second .widget-area -->
                <div id="footer-third" class="footer-widgets-box">
                  <div id="posts-list-widget-4" class="footer-widget posts-list"><div class="footer-widget-top"><h4 class="grey-col"><?php echo ucwords($response_tags[2]); ?>		</h4></div>
                    <div class="footer-widget-container">				
                      <ul>
                        <?php
                          $brand_foot_rand = blog_lp_related_post($response_tags[2],3);
                          if($brand_foot_rand['success'] && $brand_foot_rand['output']['list']) {
                          foreach ($brand_foot_rand['output']['list'] as $brand_first_key => $brand_first_val){ 
                            if ( ! isset( $brand_first_val['image']) || ! $brand_first_val['image'] || stristr( $brand_first_val['image'], 'images.unsplash.com' ) ) $brand_first_val['image'] = TAOH_CDN_PREFIX."/images/igcache/".urlencode( $brand_first_val['title'] )."/900_600/blog.jpg";
                        ?>
                        <li>
                          <div class="post-thumbnail tie-appear">
                            <a href="<?php echo taoh_lp_blog_link(slugify2($brand_first_val['title'])."-".$brand_first_val['conttoken']); ?>" title="<?php echo $brand_first_val['title']; ?>" rel="bookmark">
                              <img width="110" height="75" src="<?php echo $brand_first_val['image']; ?>" class="attachment-tie-small size-tie-small wp-post-image" alt="" decoding="async" loading="lazy" srcset="<?php echo $brand_first_val['image']; ?> 110w, <?php echo $brand_first_val['image']; ?> 220w, <?php echo $brand_first_val['image']; ?> 330w" sizes="(max-width: 110px) 100vw, 110px"><span class="fa overlay-icon"></span>
                            </a>
                          </div><!-- post-thumbnail /-->
                          <h3 style="overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; text-overflow: ellipsis;"><a class="css-font" href="<?php echo taoh_lp_blog_link(slugify2($brand_first_val['title'])."-".$brand_first_val['conttoken']); ?>"><?php echo $brand_first_val['title']; ?></a></h3>
                          <!-- <span class="tie-date"><i class="fa fa-clock-o"></i>January 22, 2024</span>	 -->						
                        </li>
                        <?php }
                          } 
                        ?>
                      </ul>
                      <div class="clear"></div>
                    </div>
                  </div><!-- .widget /-->		
                </div><!-- #third .widget-area -->
              </div><!-- #footer-widget-area -->
            </div>
            <div class="clear"></div>
          </footer>
          <div class="clear"></div>
          <div class="footer-bottom">
            <div class="container footer-cont-bottom">
              <div class="row align-items-center pb-4 copyright-wrap">
                <div class="col-lg-12">
                  <ul class="nav justify-content-center" style="margin-bottom: -10px;">
                    <?php
                      $foot_line1 = json_decode(TAOH_MENU_FOOTER_LINE_1,true);
                      //print_r($foot_line1);
                      foreach($foot_line1 as $foot_line1_keys => $foot_line1_val) {
                        echo '<li class="nav-item">
                          <a class="nav-link " href="'.$foot_line1_val.'" target="_blank" style="color: #999999;">'.$foot_line1_keys.'</a>
                        </li>';
                      }
                    ?> 
                  </ul>
                  <ul class="nav justify-content-center border-bottom pb-3 mb-3">
                    <?php
                      $foot_line2 = json_decode(TAOH_MENU_FOOTER_LINE_2,true);
                      //print_r($foot_line1);
                      foreach($foot_line2 as $foot_line2_keys => $foot_line2_val) {
                        echo '<li class="nav-item">
                          <a class="nav-link " href="'.TAOH_TEMP_SITE_URL.'/'.$foot_line2_val.'" target="_blank" style="color: #999999;">'.$foot_line2_keys.'</a>
                        </li>';
                      }
                    ?>
                  </ul>
                  <p class="text-center text-muted" style="color: #999999;">
                    <strong style="color: #6C757D;">&copy; <?php echo date('Y'); ?>
                      <a href="https://jushires.com" style="color: #6C757D;font-size: 1.17em;">#Hires</a>
                        [ by
                        <a href="https://tao.ai" style="color: #6C757D;font-size: 1.17em;">TAO.ai</a>
                        ] | All Rights Reserved |
                    </strong><br>
                    <?php
                      $foot_line3 = json_decode(TAOH_MENU_FOOTER_LINE_3,true);
                      //print_r($foot_line1);
                      foreach($foot_line3 as $foot_line3_keys => $foot_line3_val) {
                        echo '<a href="'.$foot_line3_val.'" target="_BLANK" style="color: #6C757D;font-size: 1.17em;">'.$foot_line3_keys.'</a> | ';
                      }
                    ?>
                </div><!-- end col-lg-12 -->
              </div><!-- end row -->
            </div>
            <!-- .Container -->
          </div>
          <!-- .Footer bottom -->
        </div>
        <!-- .inner-Wrapper -->
    </div>
    <!-- #Wrapper -->
  </div>
   <!-- .Wrapper-outer -->
   <div id="topcontrol" class="fa fa-angle-up" title="Scroll To Top" style="bottom: -100px;"></div>
   <div id="fb-root"></div>
   <div id="reading-position-indicator"></div>
</body>
</html>
