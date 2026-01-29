<?php $ctype = 'secret';
$code = TAOH_API_SECRET;
if ( taoh_user_is_logged_in() ){
	$ctype = 'token';
	$code = TAOH_API_TOKEN;
}
$q = '';
if (@$_GET['q']){
	$q = "&q=".urlencode($_GET['q']);
} ?>

<div class="card card-item">
    <div class="card-body">
        <h3 class="fs-17 pb-3">Categories</h3>
        <div class="divider"><span></span></div>
        <div class="category-list pt-4">
          <?php $q = "&qty=5";
            //$categories = json_decode(taoh_url_get_content( TAOH_SITE_READS."?mod=reads&ctype=$ctype&code=$code&type=catlist$q" ));
            $taoh_call = "reads.get.reads";
            $taoh_vals = array(
              'mod'=>'reads',
              'ctype'=>$ctype,
              'code'=>$code,
              'type'=>'catlist',
              'qty'=>5,

            );
            $taoh_call_type = "get";
            $categories = json_decode( taoh_apicall_get( $taoh_call, $taoh_vals ) );
              foreach ($categories->items as $key => $value){
                if ($key){ ?>
                  <a href="<?php echo TAOH_READS_URL."/search?q=".urlencode($value)."&type=category"; ?>" class="cat-item d-flex align-items-center justify-content-between mb-3 hover-y">
                      <span class="cat-title"><?php echo $value; ?></span>
                      <span class="cat-number"></span>
                  </a>
                <?php }
              } ?>
        </div>
    </div>
</div><!-- end card -->
