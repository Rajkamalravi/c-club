<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/raj/assets/icons/icons.php';
// Session Slot Modal
require_once('events_session_form.php');
?>
<div class="modal speaker-detail-modal fade" id="speakerDetailModal" tabindex="-1" role="dialog"
        aria-labelledby="mySpeakerModalLabel" aria-hidden="true">
    <div class="modal-dialog bg-white" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white align-items-center py-3" style="border: none; border-bottom: 1.8px solid #d3d3d3;">
                <h4 class="text-heading d-flex align-items-center" style="gap: 12px;">
                    <svg width="40" height="40" viewBox="0 0 79 79" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="39.5" cy="39.5" r="39.5" fill="#457BE7"/>
                        <path d="M32.1364 27.3125V39.5C32.1364 43.5371 35.4347 46.8125 39.5 46.8125C43.5653 46.8125 46.8636 43.5371 46.8636 39.5H40.7273C40.0523 39.5 39.5 38.9516 39.5 38.2812C39.5 37.6109 40.0523 37.0625 40.7273 37.0625H46.8636V34.625H40.7273C40.0523 34.625 39.5 34.0766 39.5 33.4062C39.5 32.7359 40.0523 32.1875 40.7273 32.1875H46.8636V29.75H40.7273C40.0523 29.75 39.5 29.2016 39.5 28.5312C39.5 27.8609 40.0523 27.3125 40.7273 27.3125H46.8636C46.8636 23.2754 43.5653 20 39.5 20C35.4347 20 32.1364 23.2754 32.1364 27.3125ZM49.3182 38.2812V39.5C49.3182 44.8854 44.923 49.25 39.5 49.25C34.077 49.25 29.6818 44.8854 29.6818 39.5V36.4531C29.6818 35.44 28.8611 34.625 27.8409 34.625C26.8207 34.625 26 35.44 26 36.4531V39.5C26 46.2869 31.0778 51.8932 37.6591 52.7844V55.3438H33.9773C32.9571 55.3438 32.1364 56.1588 32.1364 57.1719C32.1364 58.185 32.9571 59 33.9773 59H39.5H45.0227C46.0429 59 46.8636 58.185 46.8636 57.1719C46.8636 56.1588 46.0429 55.3438 45.0227 55.3438H41.3409V52.7844C47.9222 51.8932 53 46.2869 53 39.5V36.4531C53 35.44 52.1793 34.625 51.1591 34.625C50.1389 34.625 49.3182 35.44 49.3182 36.4531V38.2812Z" fill="white"/>
                    </svg>
                    <span>Speaker details</span>
                </h4>

                <button type="button" class="btn" data-dismiss="modal" aria-label="Close">
                    <?= icon('close', '#D3D3D3', 13) ?>
                </button>

            </div>
            <div id="speakerdet_loaderArea" style="text-align: center;"></div>
            <div class="modal-body p-3 px-lg-5 pb-lg-5 pt-lg-4" id="speaker_info">

            </div>
        </div>
    </div>
</div>
<script>
    var my_ptoken = "<?php echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>";
    var eve = "<?php echo $event_token ?? ''; ?>";
    window._spk_ptoken = "<?php echo $ptoken ?? ''; ?>";
    window._spk_user_timezone = "<?php echo taoh_user_is_logged_in() ? taoh_user_timezone() : ''; ?>";
</script>
<script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/js/events-speakers.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
