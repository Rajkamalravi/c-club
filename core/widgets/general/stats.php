<?php
if ( ! taoh_user_is_logged_in()) {
$stats = json_decode(taoh_url_get_content(TAOH_SITE_STATS));
$counter = rand(1,5);
?>
<div class="card card-item mob-hide">
  <div class="card-body">
    <h3 class="fs-17 pb-3">Hires In Numbers</h3>
    <div class="divider"><span></span></div>
    <div class="row no-gutters text-center">
      <?php foreach ($stats as $key => $value){ $counter++; ?>
        <div class="col-lg-6 responsive-column-half">
          <div class="icon-box pt-3">
              <span class="fs-20 fw-bold text-color-<?php echo $counter; ?>"><?php echo $value; ?></span>
              <p class="fs-14"><?php echo $key; ?></p>
          </div>
        </div>
      <?php } ?>
      </div>
    </div>
  </div>
<?php
}
?>
