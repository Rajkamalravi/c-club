<?php
  $user_type = taoh_user_type();
  $new_ads = taoh_url_get_content( TAOH_CDN_PREFIX."/app/ads.php?type=".$user_type);
  $return = json_decode( $new_ads, true );
  if(isset($return[ 'title' ]) && $return['title'] != ''){
  ?>
<div class="card card-item">
  <div class="card-body">
    <h3 class="fs-17 pb-3 text-color-8">
      <?php echo $return[ 'title' ]; ?>
    </h3>
    <div class="divider"><span></span></div>
    <div class="sidebar-questions pt-3">
      <div class="media media-card media--card media--card-2">
        <div class="media-body">
          <center>
            <a href="<?php echo $return[ 'link_url' ]; ?>"><img src="<?php echo $return[ 'image' ]; ?>" width="256"></a>
          </center>
          <h5>
            <a href="<?php echo $return[ 'image' ]; ?>"><?php echo $return[ 'subtitle' ]; ?></a>
          </h5>
          <small class="meta"><br />
          <center>
          <a href="<?php echo $return[ 'link_url' ]; ?>" class="btn btn-primary btn-lg active" role="button" aria-pressed="true" style="color: <?php echo $return[ 'color' ]; ?>; background-color:<?php echo $return[ 'background' ]; ?>;"><?php echo $return[ 'button_text' ]; ?></a>
          </center>
          </small>
        </div>
      </div><!-- end media -->
    </div>
  </div>
</div>
<?php } ?>
