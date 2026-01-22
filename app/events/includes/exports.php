<?php
/**
 * Consolidated Export Handler
 * Handles all export functionality for events module
 *
 * Usage: Include this file and call export_handler($type) where $type is:
 * - 'rsvp' - Export RSVP members
 * - 'raffle_entries' - Export raffle entries
 * - 'raffle_feedback' - Export raffle feedback
 *
 * Replaces:
 * - export_rsvp.php
 * - export_raffle_entries.php
 * - export_raffle_feedback.php
 */

require_once './core/excel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!function_exists('taoh_slugify')) {
    function taoh_slugify($string): string
    {
        return strtolower(custom_trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
    }
}

/**
 * Main export handler
 * @param string $type Export type: 'rsvp', 'raffle_entries', 'raffle_feedback'
 */
function export_handler($type) {
    switch ($type) {
        case 'rsvp':
            export_rsvp_data();
            break;
        case 'raffle_entries':
            export_raffle_entries_data();
            break;
        case 'raffle_feedback':
            export_raffle_feedback_data();
            break;
        default:
            die('Invalid export type');
    }
}

/**
 * Export RSVP data
 */
function export_rsvp_data() {
    $event_token = $_GET['eventtoken'];

    $taoh_vals = array(
        'mod' => 'events',
        'token' => taoh_get_dummy_token(),
        'eventtoken' => $_GET['eventtoken'],
    );

    $response = taoh_apicall_get('events.rsvp.download', $taoh_vals);
    $result = json_decode($response, 1);
    $ticket_id = 'members';

    if ($result['success']) {
        exit;
        die();
    }

    $rsvp_members = array();
    if ($result['success']) {
        $rsvp = $result['output'];
        foreach ($rsvp as $key => $value) {
            $ticket_id = $value['ticket']['title'];
            $rsvp_members[$ticket_id][$key]['name'] = $value['user']['fname'] . ' ' . $value['user']['lname'];
            $rsvp_members[$ticket_id][$key]['email'] = $value['user']['email'];
            $rsvp_members[$ticket_id][$key]['ptoken'] = $value['user']['ptoken'];
            $rsvp_members[$ticket_id][$key]['profile'] = $value['user']['type'];
            $rsvp_members[$ticket_id][$key]['ticket_title'] = $value['ticket']['title'];
            $rsvp_members[$ticket_id][$key]['ticket_type'] = $value['ticket']['price'];
            $rsvp_members[$ticket_id][$key]['ticket_cost'] = $value['ticket']['cost'] == '' ? '0' : $value['ticket']['cost'];
            $i = 0;

            foreach ($value['questions'] as $questio) {
                if (isset($value['answer_type'][$i]) && $value['answer_type'][$i] == 'checkbox') {
                    if ($value['answers'][$i] != '') {
                        $ans = unserialize($value['answers'][$i]);
                        $rsvp_members[$ticket_id][$key][$value['questions'][$i]] = implode(',', $ans);
                    }
                } else {
                    $rsvp_members[$ticket_id][$key][$value['questions'][$i]] = $value['answers'][$i] == '' ? '-' : taoh_title_desc_decode($value['answers'][$i]);
                }
                $i++;
            }
        }
    } else {
        $key = 0;
        $rsvp_members[$ticket_id][$key]['name'] = '';
        $rsvp_members[$ticket_id][$key]['email'] = '';
        $rsvp_members[$ticket_id][$key]['ptoken'] = '';
        $rsvp_members[$ticket_id][$key]['profile'] = '';
        $rsvp_members[$ticket_id][$key]['ticket_title'] = '';
        $rsvp_members[$ticket_id][$key]['ticket_type'] = '';
        $rsvp_members[$ticket_id][$key]['ticket_cost'] = '';
    }

    $rsvp = array();
    foreach ($rsvp_members as $key => $value) {
        $s = 0;
        foreach ($value as $k => $v) {
            $rsvp[$key][$s] = $v;
            $s++;
        }
    }

    $spreadsheet = new Spreadsheet();
    $r = 0;
    foreach ($rsvp as $key => $value) {
        if ($r == 0) {
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($key);
            if (!empty($value)) {
                $headers = array_keys($value[0]);
                $headers = array_map('html_entity_decode', $headers);
                $sheet->fromArray($headers, null, 'A1');
                $highestColumn = $sheet->getHighestColumn();
                $sheet->getStyle('A1:' . $highestColumn . '1')->getFont()->setBold(true);
            }
            $value = array_map('html_entity_decode', $value);
            $sheet->fromArray($value, null, 'A2');
        } else {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($key);
            if (!empty($value)) {
                $headers = array_keys($value[0]);
                $headers = array_map('html_entity_decode', $headers);
                $sheet->fromArray($headers, null, 'A1');
                $highestColumn = $sheet->getHighestColumn();
                $sheet->getStyle('A1:' . $highestColumn . '1')->getFont()->setBold(true);
            }
            $value = array_map('html_entity_decode', $value);
            $sheet->fromArray($value, null, 'A2');
        }
        $r++;
    }

    output_spreadsheet($spreadsheet, "rsvp_members_" . $event_token . ".xlsx");
}

/**
 * Export raffle entries data
 */
function export_raffle_entries_data() {
    $event_token = $_GET['eventtoken'];
    $exhibitor_id = $_GET['exh'];

    $taoh_vals = array(
        'mod' => 'events',
        'token' => taoh_get_dummy_token(1),
        'eventtoken' => $_GET['eventtoken'],
        'exhibitor_id' => $exhibitor_id,
        'cache_required' => 0,
    );

    $response = taoh_apicall_get('events.raffle.users.list', $taoh_vals, '', 1);
    $result = json_decode($response, 1);

    $raffle_members = array();
    if ($result['success']) {
        $raffle = $result['output'];
        foreach ($raffle as $key => $value) {
            $raffle_members[$key]['Name'] = $value['fname'] . ' ' . $value['lname'];
            $raffle_members[$key]['Email'] = $value['email'];
            $raffle_members[$key]['ptoken'] = $value['ptoken'];
            $raffle_members[$key]['Profile'] = $value['type'];
            $raffle_members[$key]['Answer'] = $value['answer'];
        }
    } else {
        $key = 0;
        $raffle_members[$key]['Name'] = '';
        $raffle_members[$key]['Email'] = '';
        $raffle_members[$key]['ptoken'] = '';
        $raffle_members[$key]['Profile'] = '';
        $raffle_members[$key]['Answer'] = '';
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Raffle Availed Members');
    if (!empty($raffle_members[0])) {
        $headers = array_keys($raffle_members[0]);
        $sheet->fromArray($headers, null, 'A1');
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFont()->setBold(true);
    }
    $sheet->fromArray($raffle_members, null, 'A2');

    output_spreadsheet($spreadsheet, "raffle_members_" . $event_token . "_" . $exhibitor_id . ".xlsx");
}

/**
 * Export raffle feedback data
 */
function export_raffle_feedback_data() {
    $event_token = $_GET['eventtoken'];
    $exhibitor_id = $_GET['exh'];

    $purposeArr = [
        'product_info' => 'Product Information',
        'pricing' => 'Pricing or Quote Request',
        'request_demo' => 'Request a Demo',
        'partnership_opportunity' => 'Partnership Opportunity',
        'tech_support' => 'Technical Support',
        'other' => 'Others'
    ];

    $taoh_vals = array(
        'mod' => 'core',
        'ops' => 'get',
        'conttoken' => 'event_' . $exhibitor_id,
        'conttype' => 'exhibitor',
        'type' => 'comment',
        'token' => taoh_get_dummy_token(1),
        'eventtoken' => $_GET['eventtoken'],
        'exhibitor_id' => $exhibitor_id,
        'cache_required' => 0,
    );

    $response = taoh_apicall_get('core.content.get', $taoh_vals, '', 1);
    $result = json_decode($response, 1);

    $feedback_comments = array();
    if ($result['success']) {
        $feedback = $result['output']['comment'];
        foreach ($feedback as $key => $value) {
            $feedback_comments[$key]['Name'] = $value['comment']['leadgen_username'] ?? $value['chat_name'];
            $feedback_comments[$key]['Date'] = taoh_fullyear_convert_time($value['date'], $convert = true);
            $feedback_comments[$key]['ptoken'] = $value['ptoken'];
            $feedback_comments[$key]['Email'] = $value['comment']['leadgen_useremail'] ?? '';
            $feedback_comments[$key]['Mobile'] = $value['comment']['leadgen_mobile'] ?? '';
            $feedback_comments[$key]['Purpose of Enquiry'] = (isset($value['comment']['leadgen_purpose'])) ? $purposeArr[$value['comment']['leadgen_purpose']] : '';
            $feedback_comments[$key]['Other Purpose'] = $value['comment']['other_purpose'] ?? '';
            $feedback_comments[$key]['Message'] = $value['comment']['leadgen_message'] ?? $value['comment'];
            $feedback_comments[$key]['Updates required'] = (isset($value['comment']['leadgen_updates_required']) && $value['comment']['leadgen_updates_required']) ? 'Yes' : 'No';
        }
    } else {
        $key = 0;
        $feedback_comments[$key]['Name'] = '';
        $feedback_comments[$key]['Date'] = '';
        $feedback_comments[$key]['Comments'] = '';
        $feedback_comments[$key]['ptoken'] = '';
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('feedback Availed Members');
    $sheet->getDefaultColumnDimension()->setWidth(20);
    if (!empty($feedback_comments[0])) {
        $headers = array_keys($feedback_comments[0]);
        $sheet->fromArray($headers, null, 'A1');
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFont()->setBold(true);
    }
    $sheet->fromArray($feedback_comments, null, 'A2');

    output_spreadsheet($spreadsheet, "feedback_comments_" . $event_token . "_" . $exhibitor_id . ".xlsx");
}

/**
 * Output spreadsheet to browser for download
 * @param Spreadsheet $spreadsheet
 * @param string $filename
 */
function output_spreadsheet($spreadsheet, $filename) {
    $spreadsheet->setActiveSheetIndex(0);
    $writer = new Xlsx($spreadsheet);
    $get_file_name = TAOH_PLUGIN_PATH . '/cache/general/' . $filename;

    $writer->save($get_file_name);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    ob_clean();
    flush();

    $writer->save('php://output');
    unlink($get_file_name);
    exit;
}

// Auto-detect export type from URL if called directly
if (basename($_SERVER['SCRIPT_FILENAME']) === 'exports.php' || isset($_GET['export_type'])) {
    $export_type = $_GET['export_type'] ?? '';
    if ($export_type) {
        export_handler($export_type);
    }
}
