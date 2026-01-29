<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/raj/assets/icons/icons.php';
require_once('events_exhibitor_form_new.php');
//echo "===ssssssssss";die();
// $click_view = (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) ? 'click' : 'view';
// echo "<pre>"; print_r(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null); echo "</pre>";

$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
$user_info_obj = $taoh_user_is_logged_in ? taoh_user_all_info() : null;
$valid_user = $taoh_user_is_logged_in && in_array($user_info_obj?->profile_complete ?? null, [1, '1'], true);
$ptoken = $taoh_user_is_logged_in ? ($user_info_obj?->ptoken ?? '') : '';
$ref_param =  taoh_parse_url(3);
$ref_slug = taoh_parse_url(4);

$trackingtoken = '';

if($taoh_user_is_logged_in && $ptoken != ''){

    $trackingtoken = hash('sha256',(string)$ptoken);

}

$social_token = '';
if (isset($ref_param) && $ref_param != '' && $ref_param != 'stlo') {

    $hashptoken =  hash('sha256',(string)$ptoken);
    if ( $ptoken !== '' && $hashptoken === (string)$ref_param) {
        $social_token = $ref_param;
    }

}

$success_discount_amt   = (string)($GLOBALS['success_discount_amt']   ?? '');
$success_sponsor_title  = (string)($GLOBALS['success_sponsor_title']  ?? '');
$success_redirect       = (string)($GLOBALS['success_redirect']       ?? '');


$full_location = (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->full_location ?? '';
$user_country_array = explode(',', $full_location);
$user_country_name = trim(end($user_country_array));
// echo 'user_country_name : '.$user_country_name."====".$full_location;
?>
<?php // exhibitors css ?>
<link rel="stylesheet" href="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/css/events-exhibitors.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>">
<div class="modal fade" id="sponsorDetailModal" tabindex="-1" role="dialog" aria-labelledby="sponsorDetailModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center" style="gap: 12px;">
                        <div class="n-spon-badge-con">
                            <svg width="13" height="17" viewBox="0 0 13 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.88366 0.181775C6.25594 -0.0605917 6.74328 -0.0605917 7.11555 0.181775L7.71795 0.570226C7.92101 0.699709 8.15791 0.762791 8.3982 0.749511L9.11905 0.706349C9.56578 0.679789 9.98543 0.918835 10.1851 1.31061L10.51 1.94474C10.6183 2.15723 10.7943 2.32655 11.0075 2.4328L11.6607 2.75485C12.06 2.95073 12.3037 3.36242 12.2766 3.80067L12.2326 4.50785C12.2191 4.74358 12.2834 4.97931 12.4154 5.17519L12.8147 5.76617C13.0618 6.13138 13.0618 6.60947 12.8147 6.97468L12.4154 7.56898C12.2834 7.76818 12.2191 8.00059 12.2326 8.23632L12.2766 8.9435C12.3037 9.38175 12.06 9.79344 11.6607 9.98933L11.0143 10.3081C10.7977 10.4143 10.6251 10.5869 10.5168 10.7961L10.1885 11.4369C9.98882 11.8287 9.56917 12.0677 9.12244 12.0411L8.40158 11.998C8.1613 11.9847 7.92101 12.0478 7.72134 12.1773L7.11893 12.569C6.74666 12.8114 6.25932 12.8114 5.88705 12.569L5.28126 12.1773C5.0782 12.0478 4.8413 11.9847 4.60101 11.998L3.88016 12.0411C3.43343 12.0677 3.01378 11.8287 2.8141 11.4369L2.48921 10.8027C2.38091 10.5903 2.20493 10.4209 1.99172 10.3147L1.33855 9.99265C0.939201 9.79676 0.695532 9.38507 0.722606 8.94682L0.766602 8.23964C0.780139 8.00391 0.715838 7.76818 0.58385 7.5723L0.187887 6.978C-0.0591669 6.61279 -0.0591669 6.1347 0.187887 5.76949L0.58385 5.17851C0.715838 4.97931 0.780139 4.7469 0.766602 4.51117L0.722606 3.80399C0.695532 3.36574 0.939201 2.95405 1.33855 2.75817L1.98495 2.43944C2.20155 2.32987 2.37753 2.15723 2.48583 1.94474L2.81072 1.31061C3.01039 0.918835 3.43005 0.679789 3.87677 0.706349L4.59763 0.749511C4.83791 0.762791 5.0782 0.699709 5.27787 0.570226L5.88366 0.181775ZM9.20705 6.37375C9.20705 5.66931 8.9218 4.99373 8.41405 4.49562C7.90631 3.99751 7.21766 3.71767 6.49961 3.71767C5.78155 3.71767 5.0929 3.99751 4.58516 4.49562C4.07741 4.99373 3.79217 5.66931 3.79217 6.37375C3.79217 7.07818 4.07741 7.75376 4.58516 8.25187C5.0929 8.74998 5.78155 9.02982 6.49961 9.02982C7.21766 9.02982 7.90631 8.74998 8.41405 8.25187C8.9218 7.75376 9.20705 7.07818 9.20705 6.37375ZM0.0457464 14.6673L1.50438 11.2642C1.51115 11.2676 1.51453 11.2709 1.51792 11.2775L1.84281 11.9117C2.23877 12.6819 3.06116 13.1501 3.94108 13.1003L4.66193 13.0571C4.6687 13.0571 4.67885 13.0571 4.68562 13.0637L5.28803 13.4555C5.46063 13.5651 5.64338 13.6514 5.8329 13.7111L4.5604 16.676C4.48256 16.8586 4.30996 16.9814 4.11029 16.998C3.91062 17.0146 3.71771 16.925 3.60941 16.759L2.51967 15.1222L0.621077 15.3978C0.428172 15.4243 0.235267 15.348 0.113432 15.1985C-0.0084024 15.0491 -0.0320925 14.8433 0.0423621 14.6673H0.0457464ZM8.43881 16.6727L7.16631 13.7111C7.35583 13.6514 7.53859 13.5684 7.71118 13.4555L8.31359 13.0637C8.32036 13.0604 8.32713 13.0571 8.33728 13.0571L9.05814 13.1003C9.93805 13.1501 10.7604 12.6819 11.1564 11.9117L11.4813 11.2775C11.4847 11.2709 11.4881 11.2676 11.4948 11.2642L12.9568 14.6673C13.0313 14.8433 13.0042 15.0458 12.8858 15.1985C12.7673 15.3513 12.571 15.4276 12.3781 15.3978L10.4795 15.1222L9.3898 16.7557C9.2815 16.9217 9.0886 17.0113 8.88892 16.9947C8.68925 16.9781 8.51665 16.852 8.43881 16.6727Z" fill="#ffffff"></path>
                            </svg>
                        </div>
                        <span id="sponsorDetailModalTitle">Event Ticket</span>
                    </h5>
                    <button type="button" class="btn rounded-circle border" data-dismiss="modal" aria-label="Close">
                        <?= icon('close', '#D3D3D3', 13) ?>
                    </button>
                </div>
                <div class="modal-body p-3 px-lg-5 pb-lg-5 pt-lg-4">
                    <div class="sponsor-detail-content">
                        <div class="sponsor-detail-header d-flex" style="gap: 12px;">
                            <img src="" alt="" id="sponsorDetailLogo" class="sponsor-logo">
                            <h3 class="name" id="sponsorDetailName"></h3>
                        </div>
                        <div class="sponsor-detail-body">
                            <p class="text-xs description my-3" id="sponsorDetailDescription">

                            </p>
                            <a class="visit-link btn" href="#" id="sponsorDetailWebsite" target="_blank">Visit Website</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

<script>
    window._exh_cfg = {
        myPtoken: <?= json_encode((taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''); ?>,
        eventToken: <?= json_encode($event_token ?? ''); ?>,
        userCountryName: <?= json_encode($user_country_name ?? ''); ?>,
        socialToken: <?= json_encode($social_token); ?>,
        trackingToken: <?= json_encode($trackingtoken); ?>
    };
</script>
<script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/js/events-exhibitors.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
