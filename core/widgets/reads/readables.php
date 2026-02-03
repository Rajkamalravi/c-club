<!-- 1  widget readables -->
<?php

//die('-------');


$ctype = 'secret';
$code = TAOH_API_SECRET;
if (taoh_user_is_logged_in()) {
    $ctype = 'token';
    $code = taoh_get_dummy_token();
}

//$q = "&qty=4";
//echo TAOH_SITE_READS."?mod=reads&ctype=$ctype&code=$code&type=category$q";taoh_exit();
  //$readables = json_decode(taoh_url_get_content( TAOH_SITE_READS."?mod=reads&ctype=$ctype&code=$code&type=category$q" ));
  $taoh_call = "reads.get.reads";
  $taoh_vals = array(
    'q'=>'',
    'mod'=>'reads',
    'ctype'=>$ctype,
    'code'=>$code,
    'type'=>'category',
    'limit'=>4,
   'local'=>TAOH_READS_GET_LOCAL,
  );
  //echo "===========".$widget_type;die();
  $taoh_call_type = "get";

  //echo taoh_apicall_get_debug($taoh_call, $taoh_vals );die();

  $result = taoh_apicall_get($taoh_call, $taoh_vals);
  $readables = json_decode( $result );
 // echo'<pre>';print_r($readables);die();
 // 

list($readables_slug, $readables_value) = array_pad(explode('###', $readables->value ?? '', 2), 2, '');
if (is_object($readables)){
  if($widget_type == 'new'){ ?>
    <div class="mob-hide">
        <div class="">
          <h3 class="fs-17 pb-3 text-color-8">
            Reads:
            <a href="<?php echo TAOH_READS_URL."/search?q=".urlencode( $readables_value )."&type=category"; ?>">
              <?php echo $readables_value; ?>
            </a>
          </h3>
          <div class="divider"><span></span></div>
          <div class="sidebar-questions pt-3">
            <?php foreach ($readables->items as $key1 => $value1){ ?>
              <div class="media media--card-2">
                  <div class="media-body">
                      <h5>
                        <a target="_blank" href="<?php echo TAOH_READS_URL."/blog/".slugify2($value1->title)."-".$value1->conttoken; ?>"><?php echo $value1->title; ?></a>
                      </h5>
                      <small class="meta">
                        <span class="pr-1">by</span>
                          <a target="_blank" class="author" href="<?php echo TAOH_READS_URL; ?>/search?q=<?php echo $value1->author->atoken; ?>&type=author">
                            <?php echo $value1->author->name; ?>
                          </a>
                      </small>
                  </div>
              </div><!-- end media -->
            <?php } ?>
          </div>
        </div><!-- end col-lg-4 -->
    </div><!-- end row -->
  <?php }else{ ?>
    <div class="card card-item mob-hide">
        <div class="card-body">
          <h3 class="fs-17 pb-3 text-color-8">
            Reads:
            <a href="<?php echo TAOH_READS_URL."/search?q=".urlencode( $readables_value )."&type=category"; ?>">
              <?php echo $readables_value; ?>
            </a>
          </h3>
          <div class="divider"><span></span></div>
          <div class="sidebar-questions pt-3">
            <?php foreach ($readables->items as $key1 => $value1){ ?>
              <div class="media media-card media--card media--card-2">
                  <div class="media-body">
                      <h5>
                        <a target="_blank" href="<?php echo  $value1->link; ?>"><?php echo $value1->title; ?></a>
                      </h5>
                      <small class="meta">
                        <span class="pr-1">by</span>
                          <a target="_blank" class="author" href="<?php echo TAOH_READS_URL; ?>/search?q=<?php echo $value1->author->atoken; ?>&type=author">
                            <?php echo $value1->author->name; ?>
                          </a>
                      </small>
                  </div>
              </div><!-- end media -->
            <?php } ?>
          </div>
        </div><!-- end col-lg-4 -->
    </div><!-- end row -->
<?php } }?>
