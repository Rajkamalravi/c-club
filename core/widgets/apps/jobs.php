<!-- widget readables ========== -->
<?php
//$api = TAOH_CDN_PREFIX."/api/anony.jobs.jobs.search.get?q=&mod=all&offset=0&limit=4";
//$fetch_arr = json_decode( taoh_url_get_content( $api ) );
$taoh_call = "jobs.get";
// $taoh_vals = array(
//   'q'=>'',
//   'mod'=>'all',
//   'offset'=>0,
//   'limit'=>4,
// );

$taoh_vals = array(
  'mod'=>'jobs',
  'geohash'=>( isset( $geohash ) ) ? $geohash : '',
  //'secret' => TAOH_API_SECRET,
  'token'=>taoh_get_dummy_token(1),
  'ops'=> ( isset( $ops ) ) ? $ops : 'hires',
  'local'=>TAOH_JOBS_GET_LOCAL,
  'search'=> ( isset( $search ) ) ? $search : '',
  'limit'=>4,
  'offset'=> ( isset( $offset ) ) ? $offset : '',
  'filters'=> ( isset( $allFilters ) ) ? $allFilters : '',
  'cache_time'=>30,
  //'cfcc5h'=> 1, //cfcache newly added
  //'cache'=> array ( "name" => taoh_p2us($taoh_call).'_'.$_POST['ptoken'].'_'.TAOH_ROOT_PATH_HASH.'_'.hash('crc32',$search.$geohash.$allFilters.$offset.$limit).'_list', 'ttl' => 3600 ),
);
//$taoh_vals[ 'cfcache' ] = hash('sha256', $taoh_call . serialize($taoh_vals));
//$taoh_call = "api/anony.jobs.jobs.search.get";
$taoh_call_type = "get";
ksort($taoh_vals);
$data = taoh_apicall_get($taoh_call, $taoh_vals);
$fetch_arr = json_decode($data, true);

if ( isset( $fetch_arr->result )  && TAOH_JOBS_ENABLE){
?>
<div class="card card-item">
    <div class="card-body">
      <h3 class="fs-17 pb-3 text-color-8"> Jobs</h3>
      <div class="divider"><span></span></div>
      <div class="sidebar-questions pt-3">
        <?php foreach ( $fetch_arr->result as $key => $value ){
          foreach ( $value->company as $company_key => $value1 ){
            list( $pre, $company ) = explode( ':>', $value1 );
          }
          ?>
          <div class="media media-card media--card media--card-2">
              <div class="media-body">
                  <h5><a target="_blank" href="<?php echo TAOH_SITE_URL_ROOT."/jobs/d/"; ?><?php echo slugify2( $value->title ) ?>-<?php echo $value->conttoken; ?>"><?php echo $value->title; ?></a></h5>
                  <?php if($company) { ?>
                    <small class="meta"><span class="pr-1">by</span>
                        <a target="_blank" class="author" href="<?php echo TAOH_SITE_URL_ROOT; ?>/jobs/chat/orgchat/<?php echo $company_key."/".$company; ?>"><?php echo $company; ?> </a>
                    </small>
                <?php } ?>

              </div>
          </div><!-- end media -->
        <?php } ?>
        </div>
    </div><!-- end col-lg-4 -->
</div>
<?php
}
?>
