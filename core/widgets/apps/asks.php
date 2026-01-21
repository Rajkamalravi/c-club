<!-- widget readables -->
<?php

//$api = TAOH_CDN_PREFIX."/api/anony.asks.asks.search.get?mod=asks&offset=0&limit=4";
//$fetch_arr = json_decode( taoh_url_get_content( $api ) );

$taoh_vals = array(
  'mod'=>'asks',
  'offset'=>0,
  'limit'=>4,
);
$taoh_call = "api/anony.asks.asks.search.get";
$taoh_call_type = "get";
$fetch_arr = json_decode( taoh_apicall_get($taoh_call, $taoh_vals ) );

if ( isset( $fetch_arr->result ) ){
?>
<div class="card card-item">
    <div class="card-body">
      <h3 class="fs-17 pb-3 text-color-8">Asks</h3>
      <div class="divider"><span></span></div>
      <div class="sidebar-questions pt-3">
        <?php
        foreach ( $fetch_arr->result as $key => $value ){

          ?>
          <div class="media media-card media--card media--card-2">
              <div class="media-body">
                  <h5><a target="_blank" href="<?php echo TAOH_SITE_URL_ROOT."/asks/d/"; ?><?php echo slugify2( $value->title ) ?>-<?php echo $value->conttoken; ?>"><?php echo $value->title; ?></a></h5>
              </div>
          </div><!-- end media -->
        <?php }
        ?>
        </div>
    </div><!-- end col-lg-4 -->
</div>

<?php

}

?>
