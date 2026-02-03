<?php

require_once './assets/tools/excel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!function_exists('taoh_slugify')) {
  function taoh_slugify($string): string
  {
      return strtolower(custom_trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
  }
}
 
$event_token = $_GET['eventtoken'];
$exhibitor_id = $_GET['exh'];

//https://ppapi.tao.ai/core.content.get?conttoken=event_339&conttype=exhibitor&mod=core&ops=get&token=hT93oaWC&type=comment 
//https://ppapi.tao.ai/core.content.get?mod=core&ops=get&conttype=exhibitor&conttoken=event_253&token=hT93oaWC&eventtoken=hj02klqhs5h5xso&exhibitor_id=253&cache_required=0&debug_api=1&source=https%3A%2F%2Flocalhost%2Fhires-i&sub_secret_token=569d89ab77
    $taoh_vals = array(
        'mod' => 'core',
        'ops' => 'get',
        'conttoken' => 'event_'.$exhibitor_id,
        'conttype'  => 'exhibitor',
        'type'  => 'comment',
        'token' => taoh_get_dummy_token(1),
        'eventtoken' => $_GET['eventtoken'],       
        'exhibitor_id' => $exhibitor_id,
        'cache_required' => 0,
    );  
	
    // echo taoh_apicall_get_debug('core.content.get', $taoh_vals);die();
    $response =  taoh_apicall_get('core.content.get', $taoh_vals, '', 1);
    $result = json_decode( $response,1);
    
  $purposeArr = ['product_info'=>'Product Information', 'pricing'=>'Pricing or Quote Request', 'request_demo'=>'Request a Demo', 'partnership_opportunity'=>'Partnership Opportunity', 'tech_support'=>'Technical Support', 'other'=> 'Others'];
  // echo '<pre>';print_r($result);die();

   if($result['success']){
      //echo '<pre>';print_r($result['output']);die();
      $feedback = $result['output']['comment'];
      foreach($feedback as $key=>$value){
        $feedback_comments[$key]['Name'] = $value['comment']['leadgen_username'] ?? $value['chat_name'] ; // ['chat_name'];
        $feedback_comments[$key]['Date'] = taoh_fullyear_convert_time($value['date'], $convert = true);
        // $feedback_comments[$key]['Comments'] = $value['comment'];
        $feedback_comments[$key]['ptoken'] = $value['ptoken'];
        $feedback_comments[$key]['Email'] = $value['comment']['leadgen_useremail'] ?? '';
        $feedback_comments[$key]['Mobile'] = $value['comment']['leadgen_mobile'] ?? '';
        $feedback_comments[$key]['Purpose of Enquiry'] = (isset($value['comment']['leadgen_purpose'])) ? $purposeArr[$value['comment']['leadgen_purpose']] : '';
        $feedback_comments[$key]['Other Purpose'] = $value['comment']['other_purpose'] ?? '';
        $feedback_comments[$key]['Message'] = $value['comment']['leadgen_message'] ?? $value['comment'];
        $feedback_comments[$key]['Updates required'] = (isset($value['comment']['leadgen_updates_required']) && $value['comment']['leadgen_updates_required']) ? 'Yes' : 'No';
      }
     //echo '<pre>-----------';print_r($feedback_comments);die();
   }
   else{
        $key = 0;
       // echo "No data found";
        $feedback_comments[$key]['Name'] = '';
        $feedback_comments[$key]['Date'] = '';
        $feedback_comments[$key]['Comments'] = '';
        $feedback_comments[$key]['ptoken']= '';
     
   }
 
//echo "=========".sys_get_temp_dir();die();
//error_reporting(E_ALL);
//echo'<pre>--111111111------';print_r($feedback_comments[0]);die();
// Create a new spreadsheet
$spreadsheet = new Spreadsheet();
$sheet= $spreadsheet->getActiveSheet();
$sheet->setTitle('feedback Availed Members');
$sheet->getDefaultColumnDimension()->setWidth(20);
    if (!empty($feedback_comments[0])) {
      $headers = array_keys($feedback_comments[0]);
      // echo'<pre>--------';print_r($headers);
      $sheet->fromArray($headers, null, 'A1'); // Set headers in first row
      //Retrieve Highest Column (e.g AE)
      $highestColumn = $sheet->getHighestColumn();

      $sheet->getStyle('A1:' . $highestColumn . '1' )->getFont()->setBold(true);
    }
    $sheet->fromArray($feedback_comments, null, 'A2');
 

// Set the active sheet index to the first sheet (optional)
$spreadsheet->setActiveSheetIndex(0);


// Create a writer to generate the XLSX file
$writer = new Xlsx($spreadsheet);
$filename = "feedback_comments_".$event_token."_".$exhibitor_id.".xlsx";
//echo"<br>=========".$filename;
$get_file_name = TAOH_PLUGIN_PATH . '/cache/general/' . $filename;


// Create a writer to generate the XLSX file
$writer = new Xlsx($spreadsheet);

// Set headers to force download the file


$writer->save($get_file_name);


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0'); // Ensure no caching happens

// Clear any previous output before sending the file
ob_clean(); // Clears the output buffer
flush(); // Flushes the system output buffer

// Output the file directly to the browser
$writer->save('php://output');
unlink($get_file_name);
//echo"<br>=========".$get_file_name;die();
exit; // Make sure the script ends here
die();


?>

