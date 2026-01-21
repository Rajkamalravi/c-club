<!-- widget obviousbaba -->
<?php
//$api = TAOH_OBVIOUS_API;
//$response = taoh_url_get_content($api);

$taoh_vals = array(
);
$taoh_call = "/api/worklessons";
$taoh_call_type = "get";
$response = taoh_apicall_get( $taoh_call, $taoh_vals, $prefix=TAOH_OBVIOUS_PREFIX );

$response = json_decode($response);
$color = "";
$qoute = "";
if($response->quote) {
  $color = $response->color;
  $qoute = $response->quote;
}
?>

<div class="card card-item">
    <div class="card-body">
      <h3 class="fs-17 pb-3 text-color-8">
        Obvious Baba [#funlessons]
      </h3>
      <div class="divider"><span></span></div>
      <div class="sidebar-questions pt-3">
        <div class="media media-card media--card media--card-2">
            <div class="media-body">
                <center>
                  <img src="<?php echo TAOH_OBVIOUS_PREFIX."/images/obviousbaba.png"; ?>" width=250 />
                </center>
                <h5>
                  <a href="<?php echo TAOH_OBVIOUS_URL;?>"><?php echo $qoute; ?></a>
                </h5>
                <small class="meta">
                  <span class="pr-1">by</span>
                    <a target="_blank" class="author" target="_blank" href="<?php echo TAOH_OBVIOUS_PREFIX; ?>">
                      Obvious Baba
                    </a>
                </small>
            </div>
        </div><!-- end media -->
      </div>
    </div>
</div>
