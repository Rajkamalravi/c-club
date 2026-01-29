<?php
require_once __DIR__.'/vendor/autoload.php';
use Google\Calendar\CalendarClient;
use Google\Calendar\Event;
/* include_once('google-calendar-api.php');
$login_url = 'https://accounts.google.com/o/oauth2/auth?scope='. urlencode('https://www.googleapis.com/auth/calendar') .'&redirect_uri=http://localhost/hires-i/taoh_verify.php&response_type=code&client_id=524701707248-h7tdc062dbuhbbf19uiqnp1098tsdpaa.apps.googleusercontent.com&access_type=online';
if(isset($_GET['code'])) {
	try {
    echo $_GET['code'].'<br>';
		$capi = new GoogleCalendarApi();

		// Get the access token
		$data = $capi->GetAccessToken('524701707248-h7tdc062dbuhbbf19uiqnp1098tsdpaa.apps.googleusercontent.com', 'http://localhost/hires-i/taoh_verify.php', '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', $_GET['code']);

		// Access Token
		$access_token = $data['access_token'];
    echo "<br> Access Token : ".$access_token;

    // Get user calendar timezone
    $user_timezone = $capi->GetUserCalendarTimezone($access_token);

    $calendar_id = 'eventCreation';
    $event_title = 'Testing event';

    // Event starting & finishing at a specific time
    $full_day_event = 0;
    $event_time = [ 'start_time' => '2023-08-28T15:00:00', 'end_time' => '2023-08-28T20:00:00' ];

    // Full day event
    $full_day_event = 1;
    $event_time = [ 'event_date' => '2023-08-28' ];

    // Create event on primary calendar
    $event_id = $capi->CreateCalendarEvent($calendar_id, $event_title, $full_day_event, '0','0',$event_time, $user_timezone, $access_token);
    echo "Event Details<br>";
    print_r($event_id);
		// The rest of the code to add event to Calendar will come here
	}
	catch(Exception $e) {
		echo $e->getMessage();
		exit();
	}
}


 */

// Set the Google Calendar API endpoint.
$endpoint = 'https://www.googleapis.com/calendar/v3/';

// Set the OAuth 2.0 credentials.
 $client = new Google\Client();
$client->setAuthConfig('client_secret.json');
$client->setScopes(['https://www.googleapis.com/auth/calendar']);
$client->addScope(Google\Service\Drive::DRIVE);
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/hires-i/taoh_verify.php');
$client->setAccessType('offline');

$acess = 'ya29.a0AfB_byBckefOJTw5MO6sA7gDUKc0koZAxpxARAVZcifuKA0mCsZ6ovSUch6QoBqO3-s3PeFZ70N1P9paENNLRmhvck2a2sQpRD0Io3F1bwOj9AW-X9XqKGfFVG7NS5QPJ0brIm8RPBIAjHFqT4HzWYN3nY5czX46hDLlZgaCgYKAZASARASFQHsvYlsZt-fb41uCpTuXrtH9lcs9g0173';
$refres = '1//0gsq9lvmAqGLMCgYIARAAGBASNwF-L9Ir-NTCkGP0vrFxbcCIH6Jo9nYhuiD9kqRbHtsbyvYUZJvLV3mxY8s-lTx64fHXDytDtRA';

//$acess = '';

if($acess == ''){
  $auth_url = $client->createAuthUrl();
  if(!isset($_GET['code'])){
  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
  }
  $client->authenticate($_GET['code']);
  $access_token = $client->getAccessToken();
  $refresh_token = $client->getRefreshToken();
  echo "<pre>Access Token";print_r($access_token);echo "</pre>";
  echo "<pre>Refresh Token";print_r($refresh_token);echo "</pre>";

}else{
  $access_token = $acess;
  $refresh_token  = $refres;
}

$client->setAccessToken($access_token);

//Generating new access token using Refresh token
if ($client->isAccessTokenExpired()) {
  echo '<br>token expired';
  $newaccess = $client->refreshToken($refres);
  $access_token = $client->getAccessToken();
  $refresh_token = $client->getRefreshToken();
  $client->setAccessToken($access_token);
  echo "<pre>Access Token new";print_r($access_token);echo "</pre>";
}
$service = new Google_Service_Calendar($client);

$calendarId = "###";

$event = new Google_Service_Calendar_Event(array(
  'summary' => 'Google I/O 2023 - test',
  'location' => '800 Howard St., San Francisco, CA 94103',
  'description' => 'A chance to hear more about Google\'s developer products.',
  'start' => array(
    'dateTime' => '2023-08-29T09:00:00-07:00',
   // 'timeZone' => 'America/Los_Angeles',
  ),
  'end' => array(
    'dateTime' => '2023-08-29T19:00:00-07:00',
    //'timeZone' => 'America/Los_Angeles',
  )/* ,
  'attendees' =>  array(
    array(
      "email" => "kalaiselvikanagaraj0504@gmail.com",
    "organizer" => true),
    array(
      "email" => "kalaiselvi.k.tao.ai@gmail.com",
      "organizer" => false)
  ) */,
  'anyoneCanAddSelf' => true,
  'guestsCanInviteOthers' => true,
  'conferenceData' =>array(
    'createRequest' => array(
        'requestId' => 'sample123',
        'autoJoin' => true,
        'quickAccess' => true,
        'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],

    )
  ),
));
$conference = array('conferenceDataVersion' => '1');

$calendarId = 'primary';
$event = $service->events->insert($calendarId, $event, $conference );

//echo "<pre>";print_r($event);echo "</pre>";
$eventID = $event->id;
echo "<br>Meeting ling -:  ".$eventID;
echo "<br>Meeting ling -:  ".$event->hangoutLink;


//die;

// Create a new event.
//$client = new CalendarClient();

$event = new Google\Calendar\Event();
//$event = new Google_Service_Calendar($client);
$event->setSummary('My meeting');
$event->setLocation('My office');
$event->setStartDateTime('2023-08-27T10:00:00');
$event->setEndDateTime('2023-08-27T11:00:00');

// Set the conference data.
$event->setConferenceData(new Google\Calendar\ConferenceData());
$event->getConferenceData()->setConferenceSolutionKey('hangoutsMeet');
$event->getConferenceData()->setCreateRequest(['requestId' => 'random_string']);

// Create the event.
$response = $client->post($endpoint . 'events', ['body' => $event]);

// Get the meeting link.
$meetingLink = $response->getBody()->getContents()->getConferenceData()->getMeetingUrl();

// Print the meeting link.
echo $meetingLink;

// Delete the event.
$client->delete($endpoint . 'events/' . $response->getBody()->getId());

?>
//echo file_get_contents('http://meet.google.com/new');
//44c26db24490e23a350b39b35b10c8471