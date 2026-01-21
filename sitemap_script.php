<?php

/*require_once 'config.php';
require_once 'function.php';*/

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$current_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// If your script is sitemap.xml (or in a folder), get base source
$source_url = $protocol . "://" . $_SERVER['HTTP_HOST'];
ini_set('display_errors', 1);
error_reporting(E_ALL);

//$SECRET_KEY = TAOH_API_SECRET;
header('Content-Type: application/json');
$limit = $_GET['limit'];
$offset = $_GET['offset'];
//$source = $_GET['source'];
$source = $source_url.'/club';

$apicall = "https://api.tao.ai/events.get?mod=events&geohash=&key=lmTJXUWD&token=hT93oaWC&local=0&ops=list&search=&limit=".$limit."&offset=".$offset."&postDate=&from_date=&to_date=&filter_type=&call_from=events&filters=&demo=0&debug=0&debug_api=1";
$result = file_get_contents($apicall);
$event_list = json_decode($result,1);
//echo "<pre>============================";print_r($event_list);
$base = $source.'/events/d/';
//$base = 'https://events.tao.ai/pod/events/';;

$localPath = __DIR__ . "/sitemap.xml";
if(file_exists($localPath)){
	//echo 'pppppppp';
	unlink($localPath);
}
//die;
foreach($event_list['output']['list'] as $eventdata){
	$url =  $base . slugify($eventdata['title']).'-'.$eventdata['eventtoken'];	
	$events['loc'] = $url;
	$events['conttoken'] = $eventdata['eventtoken'];
	$events['name'] =  $eventdata['title'];
	$events['start_date'] = $eventdata['utc_start_at_read'];
	$events['end_date'] = $eventdata['utc_end_at_read'];
	$events['status'] = 'scheduled';
	$events['location'] = $eventdata['event_type'];
	$events['performer'] = 'NWLB';
	
	$vars['url'] = $url;
	$ops = 'smupdate';
	$type = 'event';
	$value = json_encode($events);

$secret = $_POST['secret'] ?? '';
/*$ops    = $_POST['ops'] ?? '';
$type   = $_POST['type'] ?? '';
$value  = $_POST['value'] ?? '';*/
$valueData = json_decode($value, true);
$old    = $valueData['old'] ?? 0;

$valid_types = ['event', 'job', 'ask', 'blog'];
$valid_ops   = ['smupdate', 'smdelete', 'smlist'];
/*
if ($old != 1 && !hash_equals($SECRET_KEY, $secret)) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
*/
if (!in_array($type, $valid_types) || !in_array($ops, $valid_ops)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid type or operation']);
    exit;
}

$sitemap_file = __DIR__ . '/sitemap.xml';
echo $sitemap_file;
$sitemap      = new DOMDocument();
$sitemap->preserveWhiteSpace = false;
$sitemap->formatOutput = true;

if (!file_exists($sitemap_file)) {
    $sitemap->loadXML('<?xml version="1.0" encoding="UTF-8"?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
                xmlns:event="http://www.google.com/schemas/sitemap-event/1.0"
                xmlns:job="http://schema.org/JobPosting"
                xmlns:qa="http://schema.org/QAPage">
        </urlset>');
} else {
    if (!$sitemap->load($sitemap_file)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to load sitemap file']);
        exit;
    }
}

$xpath = new DOMXPath($sitemap);
$xpath->registerNamespace('sm', 'http://www.sitemaps.org/schemas/sitemap/0.9');



if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['error' => 'Invalid JSON input']);
    exit;
}

$loc       = $valueData['loc'] ?? '';
$conttoken = $valueData['conttoken'] ?? '';

if (!$loc || !$conttoken) {
    echo json_encode(['error' => 'Missing loc or conttoken']);
    exit;
}

// Remove existing entry with same conttoken
foreach ($xpath->query("//sm:url[sm:loc]") as $node) {
    $locVal = $xpath->evaluate("string(sm:loc)", $node);
    if (strpos($locVal, "conttoken=$conttoken") !== false) {
        $node->parentNode->removeChild($node);
    }
}

if ($ops === 'smdelete') {
    $sitemap->save($sitemap_file);
    echo json_encode(['status' => 'deleted', 'conttoken' => $conttoken, 'success' => true]);
    exit;
}

$newNode = buildUrlNode($sitemap, $valueData, $type);
$sitemap->documentElement->appendChild($newNode);
$sitemap->save($sitemap_file);

echo json_encode([
    'status' => 'updated',
    'loc' => $loc,
   // 'loc_with_conttoken' => $locWithToken,
    'conttoken' => $conttoken
]);

}

// === Build new entry ===
function buildUrlNode($doc, $valueData, $type) {
    $urlNode = $doc->createElement('url');

    $baseLoc = $valueData['loc'];
    $token   = $valueData['conttoken'];
    $locWithToken = strpos($baseLoc, '?') === false
        ? $baseLoc . '?conttoken=' . $token
        : $baseLoc . '&conttoken=' . $token;

    $urlNode->appendChild($doc->createElement('loc', $locWithToken));
    $urlNode->appendChild($doc->createElement('lastmod', date('Y-m-d')));

    

    return $urlNode;
}

function slugify(string $text): string {
        // Replace non-alphanumeric characters with hyphen
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        
        // Transliterate to ASCII
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        
        // Remove remaining non-word characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        
        // Trim hyphens from start and end
        $text = trim($text, '-');
        
        // Replace multiple hyphens with single hyphen
        $text = preg_replace('~-+~', '-', $text);
        
        // Return lowercase result or default
        return strtolower($text ?: 'event');
    }

