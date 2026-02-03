<?php
$taoh_call = "content.get.randomjobs";
$taoh_vals = array(
    'mod' => 'jobs',
    'secret' => TAOH_API_SECRET,
    'local' => TAOH_JOBS_GET_LOCAL,
    'cache_name' => 'jobs_recent_' . TAOH_API_SECRET . '_' . taoh_get_dummy_token(),
    'cache_time' => 30,
    // 'cfcc5h'=> 1 //cfcache newly added
    // 'cache_required' => '0',
);

//echo taoh_apicall_get_debug($taoh_call, $taoh_vals);die;
$content = json_decode(taoh_apicall_get($taoh_call, $taoh_vals), true);
if (isset($content['success']) && $content['success']) {
    $job_list = array_slice($content['list'], 0, 3);
    //print_r($job_list);die;
    $head_title = 'Recent Jobs';
    ?>
<?php if($widget_type == 'new' && TAOH_JOBS_ENABLE){ ?>

    <!-- new widget html -->
    <div class="recent-jobs-v1-widget mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3><?php echo $head_title; ?></h3>

           <?php if (!stristr($_SERVER['REQUEST_URI'], TAOH_PLUGIN_PATH_NAME . '/jobs')) {
            ?>
                <a href="<?php echo TAOH_SITE_URL_ROOT . '/jobs'; ?>" class="btn bor-btn">View All Jobs</a>
            <?php } ?>
            
        </div>
        <div class="divider mb-0"><span></span></div>

        <?php
            foreach ($job_list as $key => $value) {
                $wid_job_title = ucfirst(taoh_title_desc_decode($value['title']));
                $wid_job_conttoken = $value['conttoken'];
                $wid_job_location = $value['full_location'];
                $detail_url = TAOH_SITE_URL_ROOT . '/jobs/d/' . slugify2($wid_job_title) . "-" . $wid_job_conttoken;
        ?>

        <a class="job"  target="_blank" href="<?php echo $detail_url; ?>">
            <div class="job-details">
                <div class="title d-flex align-items-start justify-content-between">
                    <?php echo $wid_job_title; ?>

                    <?php if (isset($value['enable_scout_job']) && $value['enable_scout_job'] == 'on') { ?>
                        <a class="d-flex align-items-start justify-content-end" style="margin-left: 5px;">
                            <img data-toggle="tooltip" data-placement="top"
                                    title="Please note: Scout is a specialized program that gets 6x faster results, where industry-leading peers help find the best peer talent for the jobs."
                                    src="<?php echo TAOH_SITE_URL_ROOT . '/assets/images/scout_icon.png'; ?>"
                                    width="28" height="28" style="max-width: none;" alt="Scout Icon">
                        </a>
                    <?php } ?>
                </div>
                <div class="info">
                     <?php echo $wid_job_location; ?> 
                </div>
            </div>
        </a>
        <?php } ?>
        <?php
            if (taoh_user_is_logged_in() && isset($_SESSION[TAOH_ROOT_PATH_HASH]['USER_INFO']->type) && $_SESSION[TAOH_ROOT_PATH_HASH]['USER_INFO']->type == 'employer') { ?>
            <div class="text-center mt-3">
                <a href="<?php echo TAOH_SITE_URL_ROOT.'/jobs/post'; ?>" type="button" class="btn btn-primary">+ Post a Job</a>
            </div>
        <?php } ?>
        
    </div>
<?php } }?>
