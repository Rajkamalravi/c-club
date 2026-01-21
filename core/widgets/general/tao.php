<!-- 2 widget readables -->
<?php
$bgcolor = array("bg-gray", "bg-light", "bg-3");// , "bg-2" , "bg-6"

shuffle( $bgcolor );
//$rand_keys = array_rand($bgcolor);

if ( taoh_user_is_logged_in() ){
  //$api = TAOH_SITE_TAO_GET;
  $taoh_call = "tao.get";
  $taoh_vals = array(
    'mod'=>'tao',
    'token'=>taoh_get_dummy_token(),
  
  );
  $fetch_arr = json_decode( taoh_apicall_get( $taoh_call, $taoh_vals ) );
} else {
  //$api = TAOH_SITE_CDN_TAO_GET;
  $taoh_vals = array(
    
  );
  $taoh_call = "app/tao/tao.php";
  $fetch_arr = json_decode( taoh_apicall_get( $taoh_call,  $taoh_vals ) );
}
if ( isset( $fetch_arr->success ) ){
?>
<div class="card card-item <?php echo $bgcolor[0]; ?>">
    <div class="card-body">
      <h3 class="fs-17 pb-3 text-color-9">TAO Tips & Tricks</h3>
      <div class="divider"><span></span></div>
      <div class="sidebar-questions pt-3">
        <?php
        if(is_array($fetch_arr->output)){
        foreach ( $fetch_arr->output as $key => $value ){
          ?>
          <div class="media media-card media--card media--card-2 <?php echo $bgcolor[0]; ?>">
              <div class="media-body">
                  <h5><a class=" text-color-1" target="_blank" href="<?php echo $value->url; ?>"><?php echo $value->title; ?></a>&nbsp;&nbsp;<small class="meta"><span class="pr-1">by</span>
                      <a target="_blank" class="author" href="<?php echo $value->url; ?>"><?php echo ucfirst($value->app); ?> </a>
                  </small></h5>

              </div>
          </div><!-- end media -->
        <?php }
          }
         ?>
        <small class="meta">* TAO is your career development ally, providing you tips and suggestion to help you grow rapidly with minimal effort.</small>
        </div>
    </div><!-- end col-lg-4 -->
</div>
<?php
}
?>
