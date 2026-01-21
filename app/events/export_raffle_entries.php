<?php

require_once './core/excel/vendor/autoload.php';

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

//https://papis.tao.ai/events.raffle.users.list?mod=events&token=m20F2ftt&eventtoken=hj02klqhs5h5xso&exhibitor_id=253

    $taoh_vals = array(
        'mod' => 'events',
        'token' => taoh_get_dummy_token(1),
        'eventtoken' => $_GET['eventtoken'],       
        'exhibitor_id' => $exhibitor_id,
        'cache_required' => 0,
        //'cfcc5h'=> 1, //cfcache newly added
    );  
	
    //echo taoh_apicall_get_debug('events.raffle.users.list', $taoh_vals);die();
    $response =  taoh_apicall_get('events.raffle.users.list', $taoh_vals, '', 1);
    $result = json_decode( $response,1);
    

  // echo '<pre>';print_r($result);die();

   if($result['success']){
      //echo '<pre>';print_r($result['output']);die();
      $raffle = $result['output'];
      foreach($raffle as $key=>$value){

        $raffle_members[$key]['Name'] = $value['fname'].' '. $value['lname'];
        $raffle_members[$key]['Email'] = $value['email'];
        $raffle_members[$key]['ptoken'] = $value['ptoken'];
        $raffle_members[$key]['Profile'] = $value['type'];
        $raffle_members[$key]['Answer'] = $value['answer'];
        

        
        
      }
     //echo '<pre>-----------';print_r($raffle_members);die();
   }
   else{
        $key = 0;
       // echo "No data found";
        $raffle_members[$key]['Name'] = '';
        $raffle_members[$key]['Email'] = '';
        $raffle_members[$key]['ptoken'] = '';
        $raffle_members[$key]['Profile']= '';
        $raffle_members[$key]['Answer']= '';

        
     
   }
 
//echo "=========".sys_get_temp_dir();die();
//error_reporting(E_ALL);
//echo'<pre>--111111111------';print_r($raffle_members[0]);die();
// Create a new spreadsheet
$spreadsheet = new Spreadsheet();
$sheet= $spreadsheet->getActiveSheet();
$sheet->setTitle('Raffle Availed Members');
    if (!empty($raffle_members[0])) {
      $headers = array_keys($raffle_members[0]);
      echo'<pre>--------';print_r($headers);
      $sheet->fromArray($headers, null, 'A1'); // Set headers in first row
      //Retrieve Highest Column (e.g AE)
      $highestColumn = $sheet->getHighestColumn();

      $sheet->getStyle('A1:' . $highestColumn . '1' )->getFont()->setBold(true);
    }
    $sheet->fromArray($raffle_members, null, 'A2');
 

// Set the active sheet index to the first sheet (optional)
$spreadsheet->setActiveSheetIndex(0);


// Create a writer to generate the XLSX file
$writer = new Xlsx($spreadsheet);
$filename = "raffle_members_".$event_token."_".$exhibitor_id.".xlsx";
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

