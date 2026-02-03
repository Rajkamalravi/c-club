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

    //https://ppapi.tao.ai/events.rsvp.download?mod=events&token=hT93oaWC&eventtoken=6cmogkxhxoizuzq
    $taoh_vals = array(
        'mod' => 'events',
        'token' => taoh_get_dummy_token(), // 1
        'eventtoken' => $_GET['eventtoken'],       
    );  
	
    //echo taoh_apicall_get_debug('events.rsvp.list', $taoh_vals);die();
    // $response =  taoh_apicall_get('events.rsvp.list', $taoh_vals, '', 1);
    // echo taoh_apicall_get_debug('events.rsvp.download', $taoh_vals, '', 1);die();  
    $response =  taoh_apicall_get('events.rsvp.download', $taoh_vals);
    $result = json_decode( $response,1);
    $ticket_id = 'members';

  //  echo '<pre>';print_r($result);die();

  if($result['success']){
    exit; // Make sure the script ends here
    die();
  }

   if($result['success']){
      //echo '<pre>';print_r($result['output']);die();
      $rsvp = $result['output'];
      foreach($rsvp as $key=>$value){

        //$ticket_id = taoh_slugify($value['ticket']['title']);
        $ticket_id = $value['ticket']['title'];
        //echo "=======".[$ticket_id];
        $rsvp_members[$ticket_id][$key]['name'] = $value['user']['fname'].' '. $value['user']['lname'];
        $rsvp_members[$ticket_id][$key]['email'] = $value['user']['email'];
        $rsvp_members[$ticket_id][$key]['ptoken'] = $value['user']['ptoken'];
        $rsvp_members[$ticket_id][$key]['profile'] = $value['user']['type'];
        $rsvp_members[$ticket_id][$key]['ticket_title'] = $value['ticket']['title'];
        $rsvp_members[$ticket_id][$key]['ticket_type'] = $value['ticket']['price'];
        $rsvp_members[$ticket_id][$key]['ticket_cost'] = $value['ticket']['cost'] == '' ? '0' : $value['ticket']['cost'];
        $i=0;
        
        foreach($value['questions'] as $questio){
          //echo "========".$value['questions'][$i]."===========".$value['answers'][$i];
          if(isset($value['answer_type'][$i]) && $value['answer_type'][$i] == 'checkbox' ){

            if($value['answers'][$i] !=''){
              $ans = unserialize($value['answers'][$i]);
              //print_r($ans);
              $rsvp_members[$ticket_id][$key][$value['questions'][$i]] = implode(',',$ans);
            }
            
          }
          else {
            $rsvp_members[$ticket_id][$key][$value['questions'][$i]] = $value['answers'][$i] == '' ?'-' : taoh_title_desc_decode($value['answers'][$i]);
          }
          //$rsvp_members[$key][$value['questions'][$i]] =  $value['answers'][$i];

          $i++;
        }

        
        
      }
     //echo '<pre>-----------';print_r($rsvp_members);die();
   }
   else{
        $key = 0;
       // echo "No data found";
        $rsvp_members[$ticket_id][$key]['name'] = '';
        $rsvp_members[$ticket_id][$key]['email'] = '';
        $rsvp_members[$ticket_id][$key]['ptoken'] = '';
        $rsvp_members[$ticket_id][$key]['profile']= '';
        $rsvp_members[$ticket_id][$key]['ticket_title']= '';
        $rsvp_members[$ticket_id][$key]['ticket_type']= '';
        $rsvp_members[$ticket_id][$key]['ticket_cost'] = '';
   }
  
  
  //echo '<pre>';print_r($rsvp_members);die();
  
    
// Set the appropriate headers to force the browser to download the file
/*header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="rsvp_members_'.$event_token.'.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Open the output stream (directly to the browser)
$output = fopen('php://output', 'w');

// Write the header (column names) to the CSV
$headers = array_keys($rsvp_members[0]);
fputcsv($output, $headers);

// Write each row of the array to the CSV
foreach ($rsvp_members as $row) {
    fputcsv($output, $row);
}

// Close the output stream
fclose($output);*/
$rsvp = array();
foreach($rsvp_members as $key=>$value){
  $s = 0;
  foreach($value as $k=>$v){
    $rsvp[$key][$s] = $v;
    $s++;
  }
}
//echo "=========".sys_get_temp_dir();die();
//echo'<pre>--111111111------';print_r($rsvp);die();
// Create a new spreadsheet
$spreadsheet = new Spreadsheet();


//error_reporting(E_ALL);
$r = 0;
foreach($rsvp as $key=>$value){
  
  if($r == 0) {
    
    $sheet= $spreadsheet->getActiveSheet();
    

    $sheet->setTitle($key);
    if (!empty($value)) {
      $headers = array_keys($value[0]);
      $headers = array_map('html_entity_decode', $headers); 
      $sheet->fromArray($headers, null, 'A1'); // Set headers in first row
      
      //Retrieve Highest Column (e.g AE)
      $highestColumn = $sheet->getHighestColumn();

      $sheet->getStyle('A1:' . $highestColumn . '1' )->getFont()->setBold(true);
    }
    $value = array_map('html_entity_decode', $value); 
    $sheet->fromArray($value, null, 'A2');
    // echo "<pre>"; print_r($value); 
  }
  else{

    $sheet = $spreadsheet->createSheet();
    $sheet->setTitle($key);
   // echo'<pre>----aaaaaa----';print_r($value);
    if (!empty($value)) {
      $headers = array_keys($value[0]);
      $headers = array_map('html_entity_decode', $headers); 
     // echo'<pre>--------';print_r($headers);
      $sheet->fromArray($headers, null, 'A1'); // Set headers in first row
      //Retrieve Highest Column (e.g AE)
      $highestColumn = $sheet->getHighestColumn();

      $sheet->getStyle('A1:' . $highestColumn . '1' )->getFont()->setBold(true);
     }
   // $sheet->fromArray($headers, null, 'A1'); // Set headers in first row
   $value = array_map('html_entity_decode', $value); 
    $sheet->fromArray($value, null, 'A2');
   
  }
  $r++;
}

// die();

/*
// Add the first sheet (General Info)
$sheet1 = $spreadsheet->getActiveSheet();
$sheet1->setTitle('General Info');
$sheet1->fromArray($general_info, null, 'A1');

// Add the second sheet (Details)
$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle('Details');
$sheet2->fromArray($details, null, 'A1');
*/

// Set the active sheet index to the first sheet (optional)
$spreadsheet->setActiveSheetIndex(0);


// Create a writer to generate the XLSX file
$writer = new Xlsx($spreadsheet);
$filename = "rsvp_members_".$event_token.".xlsx";
//echo"<br>=========".$filename;
$get_file_name = TAOH_PLUGIN_PATH . '/cache/general/' . $filename;
//echo"<br>=========".$get_file_name;

//die();

// Set headers to force download the file
//error_reporting(E_ALL);
//ini_set('display_errors', 1);



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

