<?php
$conttoken = $data['conttoken'];
if ( taoh_user_is_logged_in() ){
  //$api = TAOH_SITE_CONTENT_GET."?mod=core&token=".taoh_get_dummy_token(1)."&conttoken=".$conttoken."&conttype=blog&type=comment&ops=get&cacheo=0&cache_remove=1";
  //$req = file_get_contents($api);
  $taoh_call = 'core.content.get';  
  $taoh_vals = array(
    'mod' => 'core',
    'token'=>taoh_get_dummy_token(1),
    'conttoken' => $conttoken,
    'type' => 'comment',
    'conttype' => $conttoken,
    'ops'=>'get',
  );
  // $cache_name = $taoh_call.'_comment_' . hash('sha256', $taoh_call . serialize($taoh_vals));
  // $taoh_vals[ 'cfcache' ] = $cache_name;
  // $taoh_vals[ 'cache_name' ] = $cache_name;
  ksort($taoh_vals);
  

  //echo taoh_apicall_get_debug($taoh_call, $taoh_vals);die;
  $comments = json_decode(taoh_apicall_get($taoh_call, $taoh_vals,'',1),true);
  //print_r($comments);
  $total = ( isset( $comments['output']['total'] ) )? $comments['output']['total']:0;
  if ( $total ){
    foreach($comments['output']['comment'] as $comments){
      if($comments['parentid'] == 0 || $comments['parentid'] == ''){
        $comment_array[$comments['commentid']] = $comments;
      } else {
        $comment_array[$comments['parentid']]['reply'][] = $comments;
      }
    }
  //echo '<pre>';print_r($comment_array);die();
  ?>
    <div class="card card-item adddiv col-md-12">
        <div class="">
          <div id="accordion" class="generic-accordion">
            <div class="">
              <div class="card-header" id="headingOne">
                <!-- <button style="box-shadow: none !important;" class="btn btn-link fs-15" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                  
                  <i class="la la-angle-down collapse-icon"></i>
                </button> -->
                <h4 class="pb-3 fs-20" style="display:none;"><span class="get_comment<?php echo $conttoken; ?>"><?php echo $total; ?></span> <?php echo $data['label'].'(s)'; ?></h4>
              </div>
              <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion" style="">
                <div class="card-body scrollable-div" id="comments<?php echo $conttoken; ?>">
                <?php
                    if (isset($comment_array) && is_array($comment_array)) {
                    foreach ($comment_array as $comment) { 

                    if (isset($comment['avatar_image']) && $comment['avatar_image']) {
                      $avatar_image = $comment['avatar_image'];
                    } else {
                        if (isset($comment['avatar']) && $comment['avatar'] != 'default') {
                            $avatar_image = TAOH_OPS_PREFIX . '/avatar/PNG/128/' . $comment['avatar'] . '.png';
                        } else {
                            $avatar_image = TAOH_OPS_PREFIX . '/avatar/PNG/128/avatar_def.png';
                        }
                    }
                ?>
                  <div class="">
                      <div class="col-12 px-0 pt-3 d-flex align-items-center" style="gap: 14px;">
                            <img src="<?php echo $avatar_image; ?>" alt="profile" style="min-width: 40px; max-width: 40px; height: 40px; border-radius: 50%; border: 2px solid #ddd;" />
                            <p class="" style="font-size: 15px; color: #33333380; line-height: 1.2"><?php echo taoh_title_desc_decode($comment['comment']); ?></p>
                      </div>
                  </div>
                <?php } } ?>
                </div>
              </div>
            </div><!-- end card -->
          </div>
        </div><!-- end card-body -->
    </div>
  <?php
  }
}
?>
