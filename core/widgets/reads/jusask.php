<?php

$url = TAOH_SITE_URL_ROOT."/learning/jusask";
?>
<!-- widget jusask -->
 <?php if($widget_type == 'new'){ ?>
<div class="">
    <div class="">
        <h3 class="fs-17 pb-3 text-color-8">
        #JusASK, The Career Coach
        </h3>
        <div class="divider"><span></span></div>
        <div class="sidebar-questions pt-3">
            <div class="media media--card-2">
                <div class="media-body">
                    <center>
                    <a href="<?php echo $url;?>"><img src="<?php echo TAOH_CDN_PREFIX."/app/jusask/images/jusask_sq_256.png"; ?>" width=256 /></a>
                    </center>
                    <h5>
                    <a href="<?php echo $url;?>"><?php echo "Talk to AI powered #CareerCoach to get your career questions answered!"; ?></a>
                    </h5>
                    <small class="meta">
                    <span class="pr-1">by</span>
                    <a target="_blank" class="author" target="_blank" href="<?php echo $url;?>">
                    #JusASKTheCoach
                    </a>
                    </small>
                </div>
            </div><!-- end media -->
        </div>
    </div>
</div>
<?php }else{ ?>
<div class="card card-item light-dark-card">
    <div class="card-body">
        <h3 class="fs-17 pb-3 text-color-8">
        #JusASK, The Career Coach
        </h3>
        <div class="divider"><span></span></div>
        <div class="sidebar-questions pt-3">
            <div class="media media-card media--card media--card-2 light-dark-card">
                <div class="media-body">
                    <center>
                    <a href="<?php echo $url;?>"><img src="<?php echo TAOH_CDN_PREFIX."/app/jusask/images/jusask_sq_256.png"; ?>" width=256 /></a>
                    </center>
                    <h5>
                    <a href="<?php echo $url;?>"><?php echo "Talk to AI powered #CareerCoach to get your career questions answered!"; ?></a>
                    </h5>
                    <small class="meta">
                    <span class="pr-1">by</span>
                    <a target="_blank" class="author" target="_blank" href="<?php echo $url;?>">
                    #JusASKTheCoach
                    </a>
                    </small>
                </div>
            </div><!-- end media -->
        </div>
    </div>
</div>
<?php } ?>
