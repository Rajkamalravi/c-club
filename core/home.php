<?php taoh_get_header(); 
if (taoh_user_is_logged_in() ){
	$title_text = "Welcome ".taoh_user_nice_name().", apps for you";
} else {
	$title_text = "Welcome Guest, apps for you";
}
?>
<section class="hero-area bg-white shadow-sm pt-80px pb-80px">
    <span class="icon-shape icon-shape-1"></span>
    <span class="icon-shape icon-shape-2"></span>
    <span class="icon-shape icon-shape-3"></span>
    <span class="icon-shape icon-shape-4"></span>
    <span class="icon-shape icon-shape-5"></span>
    <span class="icon-shape icon-shape-6"></span>
    <span class="icon-shape icon-shape-7"></span>
    <div class="container">
      <div class="hero-content text-center">
        <h2 class="section-title pb-3"><?php echo $title_text; ?></h2>
        <p class="section-desc"><?php echo  TAOH_WERTUAL_DESCRIPTION; ?></p>
				<?php
					// if(taoh_is_logged_in()) {
					// 	$health_alert = taoh_url_get(TAOH_CDN_PREFIX.TAOH_WERTUAL_SLUG."/alert.php?health=".TAOH_API_TOKEN);
					// 	if ($health_alert) {
					// 		list($status, $alert_title) = explode('::', $health_alert);
					// 		echo "<br /><h4 class=\"section-title pb-5\">Alert: $alert_title</h4>";
					// 	}
					// }
				?>
        </div><!-- end hero-content -->
    </div><!-- end container -->
</section>
<section class="get-started-area pt-80px pb-50px pattern-bg">
    <div class="container">
        <div class="row pt-50px">
		
			<?php 
		
			foreach (taoh_available_apps() as $app) {
              $app_config = json_decode( taoh_url_get_content( TAOH_CDN_PREFIX."/app/$app/config" ) );
			?>
				<div class="col-lg-4 responsive-column-half">
                	<div class="card card-item hover-y text-center">
						<a href="<?php echo TAOH_SITE_URL_ROOT."/".$app_config->slug; ?>">
							<div class="card-body">
								<img src="<?php echo $app_config->logo_sq_small; ?>" alt="<?php echo $app_config->name_slug; ?>" width=80>
								<h5 class="card-title pt-4 pb-2"><?php echo $app_config->name_slug; ?>&nbsp;&nbsp;</h5>
								<p class="card-text text-gray"><?php echo $app_config->desc; ?></p>
								<br />
							</div>
						</a>
						<div class="m-3">
							<a class="btn btn--lg theme-btn-outline" href="<?php echo TAOH_ABOUT_URL."/".$app_config->slug; ?>" style="font-size:small">More..</a>
						</div>
                </div><!-- end card -->
            </div><!-- end col-lg-4 -->
					<?php } ?>

					<?php if(taoh_user_is_logged_in()) { ?>
						<div class="col-lg-4 responsive-column-half">
							<?php taoh_ads_widget(); ?>
						</div>
					<?php } ?>
        </div><!-- end row -->
    </div><!-- end container -->
</section>
<?php taoh_get_footer(); ?>
