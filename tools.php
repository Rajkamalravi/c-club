<?php

/*
$_POST = [
  'secret' => 'your_super_secret_key',
  'ops' => 'smupdate',
  'type' => 'event',
  'value' => json_encode([
    'loc' => 'https://example.com/events/sheconnects',
    'name' => 'SheConnects: Virtual Job & Networking',
    'start_date' => '2025-05-06T16:00:00-04:00',
    'end_date' => '2025-05-06T18:00:00-04:00',
    'location' => 'Virtual',
    'status' => 'scheduled',
    'performer' => 'NWLB'
  ])
];

$_POST = [
  'secret' => 'your_super_secret_key',
  'ops' => 'smupdate',
  'type' => 'job',
  'value' => json_encode([
    'loc' => 'https://example.com/jobs/frontend-developer',
    'title' => 'Front-End Developer',
    'datePosted' => '2025-04-20',
    'validThrough' => '2025-05-20',
    'employmentType' => 'CONTRACTOR',
    'organization' => 'TAO.ai',
    'location' => 'Remote',
    'description' => 'Build scalable front-end systems.'
  ])
];

$_POST = [
  'secret' => 'your_super_secret_key',
  'ops' => 'smupdate',
  'type' => 'ask',
  'value' => json_encode([
    'loc' => 'https://example.com/asks/learn-ai',
    'question' => 'What is the best way to learn AI?',
    'answer' => 'Start with Python and math fundamentals.',
    'dateCreated' => '2025-04-21',
    'upvoteCount' => 42
  ])
];

$_POST = [
  'secret' => 'your_super_secret_key',
  'ops' => 'smupdate',
  'type' => 'blog',
  'value' => json_encode([
    'loc' => 'https://example.com/blog/digital-literacy',
    'lastmod' => '2025-04-21',
    'changefreq' => 'weekly',
    'priority' => '0.8'
  ])
];


*/

require_once 'config.php';
require_once 'function.php';
ini_set('display_errors', 1); error_reporting(E_ALL);

$SECRET_KEY = TAOH_API_SECRET; // Replace with your real secret

header('Content-Type: application/json');

$secret = $_POST['secret'] ?? '';
$ops    = $_POST['ops'] ?? '';
$type   = $_POST['type'] ?? '';
$value  = $_POST['value'] ?? '';
$valueData = json_decode($value, true);
$old  = $valueData['old'] ?? 0;

$valid_types = ['event', 'job', 'ask', 'blog'];
$valid_ops   = ['smupdate', 'smdelete', 'smlist'];
// $fl = fopen('smlog.log','a');
// fwrite($fl, "POST : ".json_encode($_POST));
// fwrite($fl, "Old ".$old);

// if ($secret !== $SECRET_KEY) {
if($old != 1){
	if (!hash_equals($SECRET_KEY, $secret)) {
		http_response_code(403);
		echo json_encode(['error' => 'Unauthorized']);
		// fwrite($fl, "Unauthorized ");
		exit;
	}
}

if (!in_array($type, $valid_types) || !in_array($ops, $valid_ops)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid type or operation']);
	// fwrite($fl, "Invalid type or operation ");
    exit;
}

$sitemap_file = __DIR__ . '/sitemap.xml';
$site_url     = TAOH_SITE_URL_ROOT;
$sitemap      = new DOMDocument();
$sitemap->preserveWhiteSpace = false;
$sitemap->formatOutput = true;

if (!file_exists($sitemap_file)) {
    $sitemap->loadXML('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:event="http://www.google.com/schemas/sitemap-event/1.0" xmlns:job="http://schema.org/JobPosting" xmlns:qa="http://schema.org/QAPage"></urlset>');
} else {
    if(!$sitemap->load($sitemap_file)) {
		http_response_code(500);
		echo json_encode(['error' => 'Failed to load sitemap file']);
		// fwrite($fl, "Failed to load sitemap file ");
		exit;
	}

}

$xpath = new DOMXPath($sitemap);
$xpath->registerNamespace('sm', 'http://www.sitemaps.org/schemas/sitemap/0.9');

if ($ops === 'smlist') {
    $results = [];
    foreach ($xpath->query("//sm:url[sm:loc]") as $urlNode) {
        $loc = $xpath->evaluate("string(sm:loc)", $urlNode);
        if (strpos($loc, "/{$type}s/") !== false) {
            $results[] = $loc;
        }
    }
    echo json_encode(['type' => $type, 'urls' => $results]);
    exit;
}

// $valueData = json_decode($value, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['error' => 'Invalid JSON input']);
	// fwrite($fl, "Invalid JSON input ");
    exit;
}
$loc = $valueData['loc'] ?? '';
$conttoken = $valueData['conttoken'] ?? '';
// fwrite($fl, "conttoken = $conttoken\n location : ".$loc);
if (!$loc) {
    echo json_encode(['error' => 'Missing loc']);
    exit;
}
if (!$conttoken) {
    echo json_encode(['error' => 'Missing conttoken']);
    exit;
}

/* foreach ($xpath->query("//sm:url[sm:loc='$loc']") as $node) {
    $node->parentNode->removeChild($node);
}
 */

// Remove existing entry with same conttoken
foreach ($xpath->query("//sm:url[sm:loc]") as $node) {
    $locVal = $xpath->evaluate("string(sm:loc)", $node);
    if (strpos($locVal, "conttoken=$conttoken") !== false) {
        $node->parentNode->removeChild($node);
    }
}

if ($ops === 'smdelete') {
    $sitemap->save($sitemap_file);
    // echo json_encode(['status' => 'deleted', 'loc' => $loc, 'success' => true]);
    echo json_encode(['status' => 'deleted', 'conttoken' => $conttoken, 'success' => true]);
    exit;
}

// Build new node
function buildUrlNode($doc, $valueData, $type) {
    $urlNode = $doc->createElement('url');

	$baseLoc = $valueData['loc'];
    $token   = $valueData['conttoken'];
    $locWithToken = strpos($baseLoc, '?') === false
        ? $baseLoc . '?conttoken=' . $token
        : $baseLoc . '&conttoken=' . $token;

    $urlNode->appendChild($doc->createElement('loc', $locWithToken));
    $urlNode->appendChild($doc->createElement('lastmod', date('Y-m-d')));

	/*$urlNode->appendChild($doc->createElement('conttoken', $valueData['conttoken']));
    $urlNode->appendChild($doc->createElement('loc', $valueData['loc']));
    $urlNode->appendChild($doc->createElement('lastmod', date('Y-m-d')));*/

    /*switch ($type) {
        case 'event':
            $eventNode = $doc->createElementNS('http://www.google.com/schemas/sitemap-event/1.0', 'event:event');
            foreach (['name', 'start_date', 'end_date', 'location', 'status', 'performer'] as $tag) {
                if (isset($valueData[$tag])) {
                    $eventNode->appendChild($doc->createElement("event:$tag", $valueData[$tag]));
                }
            }
            $urlNode->appendChild($eventNode);
            break;

        case 'job':
            $jobNode = $doc->createElementNS('http://schema.org/JobPosting', 'job:JobPosting');
            foreach (['title', 'datePosted', 'validThrough', 'employmentType'] as $tag) {
                if (isset($valueData[$tag])) {
                    $jobNode->appendChild($doc->createElement("job:$tag", $valueData[$tag]));
                }
            }
            if (isset($valueData['organization'])) {
                $org = $doc->createElement('job:hiringOrganization');
                $org->appendChild($doc->createElement('job:name', $valueData['organization']));
                $jobNode->appendChild($org);
            }
            if (isset($valueData['location'])) {
                $locNode = $doc->createElement('job:jobLocation');
                $addr = $doc->createElement('job:address');
                $addr->appendChild($doc->createElement('job:addressLocality', $valueData['location']));
                $locNode->appendChild($addr);
                $jobNode->appendChild($locNode);
            }
            if (isset($valueData['description'])) {
                $jobNode->appendChild($doc->createElement('job:description', $valueData['description']));
            }
            $urlNode->appendChild($jobNode);
            break;

        case 'ask':
            $qaNode = $doc->createElementNS('http://schema.org/QAPage', 'qa:QAPage');
            $main = $doc->createElement('qa:mainEntity');
            if (isset($valueData['question'])) {
                $main->appendChild($doc->createElement('qa:name', $valueData['question']));
            }
            if (isset($valueData['answer'])) {
                $ans = $doc->createElement('qa:acceptedAnswer');
                $ans->appendChild($doc->createElement('qa:text', $valueData['answer']));
                if (isset($valueData['upvoteCount'])) {
                    $ans->appendChild($doc->createElement('qa:upvoteCount', $valueData['upvoteCount']));
                }
                if (isset($valueData['dateCreated'])) {
                    $ans->appendChild($doc->createElement('qa:dateCreated', $valueData['dateCreated']));
                }
                $main->appendChild($ans);
            }
            $qaNode->appendChild($main);
            $urlNode->appendChild($qaNode);
            break;

        case 'blog':
            $urlNode->appendChild($doc->createElement('changefreq', $valueData['changefreq'] ?? 'weekly'));
            $urlNode->appendChild($doc->createElement('priority', $valueData['priority'] ?? '0.7'));
            break;
    }*/
    return $urlNode;
}

$newNode = buildUrlNode($sitemap, $valueData, $type);
$sitemap->documentElement->appendChild($newNode);
$sitemap->save($sitemap_file);

echo json_encode(['status' => 'updated', 'loc' => $loc, 'conttoken'=>$conttoken]);

// Ensure /club/sitemap.xml is in root sitemap.xml
$root_sitemap_path = $_SERVER['DOCUMENT_ROOT'] . '/sitemap.xml';
$club_sitemap_url = $site_url . '/sitemap.xml';

if (file_exists($root_sitemap_path)) {
    $rootDoc = new DOMDocument();
    $rootDoc->preserveWhiteSpace = false;
    $rootDoc->formatOutput = true;
    $rootDoc->load($root_sitemap_path);

    $alreadyIncluded = false;
    // foreach ($rootDoc->getElementsByTagName('loc') as $locNode) {
    foreach ($rootDoc->getElementsByTagName('conttoken') as $locNode) {
        if ($locNode->nodeValue === $club_sitemap_url) {
            $alreadyIncluded = true;
			break;
        }
    }

    if (!$alreadyIncluded) {
		$sitemapindex = $rootDoc->documentElement;
        if ($sitemapindex->nodeName !== 'sitemapindex') {
            $urls = $rootDoc->getElementsByTagName('url');
            $sitemapindex = $rootDoc->createElement('sitemapindex');
            $sitemapindex->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
            foreach ($urls as $url) {
                $locText = $url->getElementsByTagName('loc')->item(0)->nodeValue ?? '';
                $sitemap = $rootDoc->createElement('sitemap');
                $sitemap->appendChild($rootDoc->createElement('loc', $locText));
                $sitemapindex->appendChild($sitemap);
            }
            $rootDoc->replaceChild($sitemapindex, $rootDoc->documentElement);
        }

        $sitemap = $rootDoc->createElement('sitemap');
        $sitemap->appendChild($rootDoc->createElement('loc', $club_sitemap_url));
        $rootDoc->documentElement->appendChild($sitemap);
        $rootDoc->save($root_sitemap_path);
    }
}
