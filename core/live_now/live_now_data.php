<?php
$live_now_data = [];

$live_now_url = TAOH_LIVE_NOW_URL . '?y=' . gmdate("YmdH");
$live_now_json = @file_get_contents($live_now_url);
$live_now_data_arr = json_decode($live_now_json, true);
if (!empty($live_now_data_arr) && in_array($live_now_data_arr['success'], [true, 'true']) && !empty($live_now_data_arr['output'])) {
    $live_now_data = $live_now_data_arr['output'];
}

header('Content-Type: application/json');
if (!empty($live_now_data)) {
    $join_now_url = TAOH_SITE_URL_ROOT . '/live/networking';

    echo json_encode([
        'success' => true,
        'live_now_data' => $live_now_data,
        'join_now_url' => $join_now_url
    ]);
} else {
    echo json_encode([
        'success' => false,
        'live_now_data' => [],
        'join_now_url' => ''
    ]);
}