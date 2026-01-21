<?php
// Health check
$event_health_status = true;
$event_health_result_arr = [];
$taoh_vals= array(
    'token' => taoh_get_api_token(1),
    'ops' => 'healthcheck',
    'mod' => 'events',
    'code' => $eventtoken ?? '',
);
$event_health_check_link = rtrim(TAOH_CDN_PREFIX, '/') . '/club/health.php';
$event_health_result = taoh_get($event_health_check_link, $taoh_vals);
if ($event_health_result != '1') {
    $event_health_status = false;
    $event_health_result_arr = json_decode($event_health_result, true);
}
// /Health check

$event_health_defaults = [
    'title'       => 'Health Club',
    'description' => 'Join our Health Club to access exclusive health tips and resources.',
    'banner'      => '',
    'square'      => '',
    'cta'         => '', // if empty, hide the button
    'cta_link'    => 'https://cdn.tao.ai/club/join/health', // full URL or {{baseurl}}/path
    'dismissible' => true, // true/false
    'cta_target'  => '_self', // _blank, _self
    'state'       => 'info', // info, acknowledge, warning, critical, success
];

// Merge defaults with whatever you already have in $event_health_result_arr
$event_health_result_arr = array_merge(
    $event_health_defaults,
    $event_health_result_arr ?? []
);

// Only show overlay when $event_health_status is false
if (!$event_health_status && !empty($event_health_result_arr)) {

    $banner_link = $event_health_result_arr['banner'] ?? '';

    // Replace {{baseurl}} if present
    if (strpos($banner_link, '{{baseurl}}') !== false) {
        $banner_link = str_replace('{{baseurl}}', rtrim(TAOH_SITE_URL_ROOT, '/'), $banner_link);
    } elseif ($banner_link && !preg_match('#^https?://#i', $banner_link)) {
        // Treat as relative path under TAOH_SITE_URL_ROOT
        $banner_link = rtrim(TAOH_SITE_URL_ROOT, '/') . '/' . ltrim($banner_link, '/');
    }

    $title       = $event_health_result_arr['title'];
    $description = $event_health_result_arr['description'];
    $cta         = trim($event_health_result_arr['cta']);
    $cta_link    = $event_health_result_arr['cta_link'] ?: 'javascript:void(0);';
    $cta_target  = $event_health_result_arr['cta_target'] ?: '_self';
    $dismissible = !empty($event_health_result_arr['dismissible']);
    $state       = $event_health_result_arr['state'] ?: 'info';

    $backdrop = $dismissible ? 'true' : 'static';
    $keyboard = $dismissible ? 'true' : 'false';
    ?>

    <div class="modal exhibitor-slot fade" id="healthModal"
         tabindex="-1"
         role="dialog"
         aria-labelledby="healthModalLabel"
         aria-hidden="true"
         data-backdrop="<?= htmlspecialchars($backdrop, ENT_QUOTES, 'UTF-8'); ?>"
         data-keyboard="<?= htmlspecialchars($keyboard, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="modal-dialog bg-white" role="document">
            <div class="modal-content">
                <div class="modal-header bg-white align-items-center " style="border: none; border-bottom: 1px solid #d3d3d3;">
                    <?php if ($state): ?>
                        <h4 class="health-badge health-badge-<?= htmlspecialchars($state, ENT_QUOTES, 'UTF-8'); ?>">
                            <?= ucfirst(htmlspecialchars($state, ENT_QUOTES, 'UTF-8')); ?>
                        </h4>
                    <?php endif; ?>

                    <div class="justify-content-end">
                        <?php if ($dismissible): ?>
                            <button type="button" class="btn" data-dismiss="modal" aria-label="Close">
                                <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.6153 2.22013C13.1228 1.71256 13.1228 0.888255 12.6153 0.380681C12.1077 -0.126894 11.2834 -0.126894 10.7758 0.380681L6.5 4.66055L2.22013 0.384741C1.71256 -0.122833 0.888255 -0.122833 0.380681 0.384741C-0.126894 0.892316 -0.126894 1.71662 0.380681 2.22419L4.66055 6.5L0.384742 10.7799C-0.122833 11.2874 -0.122833 12.1117 0.384742 12.6193C0.892316 13.1269 1.71662 13.1269 2.22419 12.6193L6.5 8.33945L10.7799 12.6153C11.2874 13.1228 12.1117 13.1228 12.6193 12.6153C13.1269 12.1077 13.1269 11.2834 12.6193 10.7758L8.33945 6.5L12.6153 2.22013Z" fill="#D3D3D3"></path>
                                </svg>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-body p-4">
                    <div>
                        <?php if ($banner_link !== ''): ?>
                            <img src="<?= htmlspecialchars($banner_link, ENT_QUOTES, 'UTF-8'); ?>"
                                 alt="Health Club Banner"
                                 class="img-fluid mb-4 rounded">
                        <?php endif; ?>
                    </div>

                    <h2 id="healthModalLabel" class="h3 mb-3">
                        <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>
                    </h2>
                    <p class="mb-4 text-dark">
                        <?= nl2br(htmlspecialchars($description, ENT_QUOTES, 'UTF-8')); ?>
                    </p>

                    <?php if ($cta !== ''): ?>
                        <a href="<?= htmlspecialchars($cta_link, ENT_QUOTES, 'UTF-8'); ?>"
                           target="<?= htmlspecialchars($cta_target, ENT_QUOTES, 'UTF-8'); ?>"
                           class="btn btn-light">
                            <i class="fa fa-external-link mr-1"></i>
                            <?= htmlspecialchars($cta, ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php
}
?>


<script type="application/javascript">
    let event_health_status = '<?= $event_health_status ?? true ?>';

    $(document).ready(function() {
        if (!event_health_status) {
            $('#healthModal').modal('show');
        }
    });
</script>
