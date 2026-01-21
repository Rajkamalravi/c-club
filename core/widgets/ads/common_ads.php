<?php
$taoh_call = 'ads.get';
$taoh_vals = array(
    'token' => taoh_get_dummy_token(),
    'type' => $type,
    'app' => $app,
    'qty' => $qty,
    'ptype' => taoh_user_type(),
);
    //echo taoh_apicall_get_debug($taoh_call, $taoh_vals);die;
    $return = json_decode(taoh_apicall_get($taoh_call, $taoh_vals),true);
    if(isset($return['success']) && $return['success']){
        foreach($return['output'] as $return){
  ?>
<div class="card card-item">
    <div class="card-body">
        <h3 class="fs-17 pb-3 text-color-8">
            <?php echo $return['title']; ?>
        </h3>
        <div class="divider"><span></span></div>
        <div class="sidebar-questions pt-3">
            <div class="media media-card media--card media--card-2">
                <div class="media-body">
                    <center>
                    <a href="<?php echo TAOH_SITE_URL_ROOT.$return['link']; ?>"><img src="<?php echo $return['image']; ?>" width=256 /></a>
                    </center>
                    <h5>
                    <a href="<?php echo TAOH_SITE_URL_ROOT.$return['link']; ?>"><?php echo $return['subtitle']; ?></a>
                    </h5>
                    <small class="meta">
                    <span class="pr-1">by</span>
                    <a target="_blank" class="author" target="_blank" href="<?php echo TAOH_SITE_URL_ROOT.$return['link']; ?>">
                        <?php echo $return['title']; ?>
                    </a>
                    </small>
                </div>
            </div><!-- end media -->
        </div>
    </div>
</div>
<?php } } ?>
