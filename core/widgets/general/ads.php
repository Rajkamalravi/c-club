<?php
// if ( isset( $taoh_user_vars ) && is_array( $taoh_user_vars ) ){
//   $ads = taoh_url_get( TAOH_CDN_PREFIX."/app/hires/ads/".$taoh_user_vars[ 'tao' ]->type );
// } else {
//   $ads = taoh_url_get( TAOH_CDN_PREFIX."/app/hires/ads/seeker" );
// }

function taoh_ads_general( $ads_count = 0 ){
  $ads = taoh_url_get_content( TAOH_CDN_PREFIX."/app/ads.php");
  $ads = str_replace('[TAOH_CDN_PREFIX]', TAOH_CDN_PREFIX, $ads);
  $ads = str_replace('[TAOH_SITE_URL_ROOT]', TAOH_SITE_URL_ROOT, $ads);
  $ads = str_replace('[TAOH_HOME_URL]', TAOH_HOME_URL, $ads);
  $ads_arr = json_decode( $ads, true );
  if ( $ads_count  ){
    if ( isset( $ads_arr[ $ads_count ] ) ){
      $return = $ads_arr[ $ads_count ];
    } else {
      shuffle( $ads_arr );
      $return = $ads_arr[ 0 ];
    }
  } else {
    shuffle( $ads_arr );
    $return = $ads_arr[ 0 ];
  }
  //echo $return;

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
            <a href="<?php echo $return[ 'link' ]; ?>"><img src="<?php echo $return[ 'image' ]; ?>" width="256"></a>
          </center>
          <h5>
            <a href="<?php echo $return[ 'image' ]; ?>"><?php echo $return[ 'short' ]; ?></a>
          </h5>
          <small class="meta"><br />
          <center>
          <a href="<?php echo $return[ 'link' ]; ?>" class="btn btn-primary btn-lg active" role="button" aria-pressed="true" style="color: <?php echo $return[ 'color' ]; ?>; background-color:<?php echo $return[ 'background' ]; ?>;"><?php echo $return[ 'button_text' ]; ?></a>
          </center>
          </small>
        </div>
      </div><!-- end media -->
    </div>
  </div>
</div>
  <?php
  /*
  switch( $ads_count ){
    case 1:
      $taoh_type = 'seeker';
      if ( isset( $taoh_user_vars[ 'tao' ]->type ) ){ $taoh_type = $taoh_user_vars[ 'tao' ]->type;}
      
      $ads = taoh_url_get_content( TAOH_SITE_ADS."/$taoh_type");
        if ($ads) { ?>
            <div class="card card-item text-center">
              <div class="card-body">
                <?php echo $ads; ?>
              </div>
            </div>
        <?php 
        } 
      break;
    case 2:
      $ads_class = 'col-md-6';
      break;
    case 3:
      $ads_class = 'col-md-4';
      break;
    case 4:
      $ads_class = 'col-md-3';
      break;
    default:
      $taoh_type = 'seeker';
      if ( isset( $taoh_user_vars[ 'tao' ]->type ) ){ $taoh_type = $taoh_user_vars[ 'tao' ]->type;}
      
      $ads = taoh_url_get_content( TAOH_SITE_ADS."/$taoh_type");
        if ($ads) { ?>
            <div class="card card-item text-center">
              <div class="card-body">
                <?php echo $ads; ?>
              </div>
            </div>
        <?php 
        } 
  
      break;
  }
  */
  return 1;  
}

  
?>
