<?php

if(isset($_REQUEST['taoh_action'])) {
  if(function_exists($_REQUEST['taoh_action'])) {
    if(taoh_is_wp() == 1) {
      add_action( 'wp_ajax_nopriv_'.$_REQUEST['taoh_action'], $_REQUEST['taoh_action'] );
      add_action( 'wp_ajax_'.$_REQUEST['taoh_action'], $_REQUEST['taoh_action'] );
    } else {
      return $_REQUEST['taoh_action']();taoh_exit();
    }
  } else {
    header("HTTP/1.0 404 NotFound");
    echo "No method defined";
  }
}

function taoh_sitemap_call(){

  $currentDate = date("Ymd");
  $filename = "sitemap_".$currentDate.".sitemap";

  $lasttimestamp_default = 0;
  $currentDate = date("Ymd");
  $filename_check = "sitemap_index.xml";
    $fromtimestamp = (file_exists($filename_check)) ? filemtime($filename_check) : $lasttimestamp_default;
    $taoh_call = "sitemap.get.urls";
    $taoh_vals = array(
        'mod' => 'sitemap',
        'type' => 'sitemap',
        'secret' => TAOH_API_SECRET,
        'token' => taoh_get_dummy_token(),
        //'fromtimestamp'=>$fromtimestamp,
        //'fromtimestamp'=>'',
    );
    $myfile = fopen($filename, "a") or die("Unable to open file!");


  //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals ); die();
  $res = taoh_apicall_get($taoh_call, $taoh_vals);
  $data = json_decode($res);
//echo '<pre>';print_r($data);die();
  if($data){

    //writing index file
    unlink("sitemap_index.xml");
    $myfile = fopen("sitemap_index.xml", "a") or die("Unable to open file!");
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $xml .= "<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    $xml .= "<sitemap>\n";
    $xml .= "<loc>".TAOH_SITE_URL_ROOT."/sitemap.xml</loc>\n";
    $xml .= "</sitemap>\n";
    foreach($data as $key => $val){
        $xml .= "<sitemap>\n";
        $xml .= "  <loc>".TAOH_SITE_URL_ROOT."/sitemap_".$key.".xml</loc>\n";
        $xml .= "</sitemap>\n";
    }
    $xml .= "</sitemapindex>";
    fwrite($myfile, $xml);
    fclose($myfile);

    //writing other sitemap child file
    $xml = '';
    foreach($data as $key => $val){
      if(file_exists("sitemap_".$key.".xml")){
        unlink("sitemap_".$key.".xml");
      }
      $myfile = fopen("sitemap_".$key.".xml", "a") or die("Unable to open file!");
      $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
      $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
     // $xml .= "<urlset xmlns=\"".TAOH_SITE_URL."/schemas/sitemap/0.9\">\n";
      foreach($val as $keys => $value){
        $xml .= "<url>\n";
        $xml .= "<loc>".TAOH_SITE_URL_ROOT."/".$value->loc."</loc>\n";
        $xml .= "<lastmod>".$value->lastmod."</lastmod>\n";
        $xml .= "<changefreq>".$value->changefreq."</changefreq>\n";
        $xml .= "<priority>".$value->priority."</priority>\n";
        $xml .= "<category>".$value->category."</category>\n";
        $xml .= "</url>\n";
      }
      $xml .= "</urlset>";
      fwrite($myfile, $xml);
      fclose($myfile);
    }

    //writing sitemap.xml file
    unlink("sitemap.xml");

    $sitefile = fopen("sitemap.xml", "a") or die("Unable to open file!");
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

    $xml .= "<url>\n";
    $xml .= "<loc>".TAOH_SITE_URL_ROOT."/</loc>\n";
    $xml .= "<lastmod>".date('Y-m-d')."</lastmod>\n";
    $xml .= "<changefreq>monthly</changefreq>\n";
    $xml .= "<priority>0.6</priority>\n";
    $xml .= "</url>\n";

    $xml .= "<url>\n";
    $xml .= "<loc>".TAOH_SITE_URL_ROOT."/jobs</loc>\n";
    $xml .= "<lastmod>".date('Y-m-d')."</lastmod>\n";
    $xml .= "<changefreq>monthly</changefreq>\n";
    $xml .= "<priority>0.6</priority>\n";
    $xml .= "</url>\n";

    $xml .= "<url>\n";
    $xml .= "<loc>".TAOH_SITE_URL_ROOT."/events</loc>\n";
    $xml .= "<lastmod>".date('Y-m-d')."</lastmod>\n";
    $xml .= "<changefreq>monthly</changefreq>\n";
    $xml .= "<priority>0.6</priority>\n";
    $xml .= "</url>\n";

    $xml .= "<url>\n";
    $xml .= "<loc>".TAOH_SITE_URL_ROOT."/asks</loc>\n";
    $xml .= "<lastmod>".date('Y-m-d')."</lastmod>\n";
    $xml .= "<changefreq>monthly</changefreq>\n";
    $xml .= "<priority>0.6</priority>\n";
    $xml .= "</url>\n";

    $xml .= "<url>\n";
    $xml .= "<loc>".TAOH_SITE_URL_ROOT."/networking</loc>\n";
    $xml .= "<lastmod>".date('Y-m-d')."</lastmod>\n";
    $xml .= "<changefreq>monthly</changefreq>\n";
    $xml .= "<priority>0.6</priority>\n";
    $xml .= "</url>\n";

    $xml .= "<url>\n";
    $xml .= "<loc>".TAOH_SITE_URL_ROOT."/notifications</loc>\n";
    $xml .= "<lastmod>".date('Y-m-d')."</lastmod>\n";
    $xml .= "<changefreq>monthly</changefreq>\n";
    $xml .= "<priority>0.6</priority>\n";
    $xml .= "</url>\n";

    $xml .= "<url>\n";
    $xml .= "<loc>".TAOH_SITE_URL_ROOT."/settings</loc>\n";
    $xml .= "<lastmod>".date('Y-m-d')."</lastmod>\n";
    $xml .= "<changefreq>monthly</changefreq>\n";
    $xml .= "<priority>0.6</priority>\n";
    $xml .= "</url>\n";

    $xml .= "<url>\n";
    $xml .= "<loc>".TAOH_SITE_URL_ROOT."/referral</loc>\n";
    $xml .= "<lastmod>".date('Y-m-d')."</lastmod>\n";
    $xml .= "<changefreq>monthly</changefreq>\n";
    $xml .= "<priority>0.6</priority>\n";
    $xml .= "</url>\n";

    $xml .= "<url>\n";
    $xml .= "<loc>".TAOH_SITE_URL_ROOT."/employers</loc>\n";
    $xml .= "<lastmod>".date('Y-m-d')."</lastmod>\n";
    $xml .= "<changefreq>monthly</changefreq>\n";
    $xml .= "<priority>0.6</priority>\n";
    $xml .= "</url>\n";

    $xml .= "<url>\n";
    $xml .= "<loc>".TAOH_SITE_URL_ROOT."/partners</loc>\n";
    $xml .= "<lastmod>".date('Y-m-d')."</lastmod>\n";
    $xml .= "<changefreq>monthly</changefreq>\n";
    $xml .= "<priority>0.6</priority>\n";
    $xml .= "</url>\n";

    $xml .= "<url>\n";
    $xml .= "<loc>".TAOH_SITE_URL_ROOT."/professionals</loc>\n";
    $xml .= "<lastmod>".date('Y-m-d')."</lastmod>\n";
    $xml .= "<changefreq>monthly</changefreq>\n";
    $xml .= "<priority>0.6</priority>\n";
    $xml .= "</url>\n";


    $xml .= "</urlset>";
    fwrite($sitefile, $xml);
    fclose($sitefile);

  }
  echo 1;
  die();
}

?>