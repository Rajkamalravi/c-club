<?php
$response = $data;
//echo"<pre>";print_r($response);//die();
$title = $response['conttoken']['title'];
//echo $title;
$start_at = date("D, M d, Y h:i A", strtotime($response['local_start_at']));
$ends_at = date("D, M d, Y h:i A", strtotime($response['local_end_at']));

$event_start_date = $response['local_start_at'];
$event_print_date = date('d/m/Y',strtotime($event_start_date));
$event_start_time = date("h:i", strtotime($response['local_start_at']));
$event_end_date = $response['local_end_at'];
$event_print_enddate = date('d/m/Y',strtotime($event_end_date));
$event_end_time = date("h:i", strtotime($response['local_end_at']));

$calendar_start_time = date("H:i", strtotime($response['local_start_at']));
$calendar_end_time = date("H:i", strtotime($response['local_end_at']));

?>
<style>
.calendar_dropdown{
  position: absolute;
}

button.calendar{
  position: relative;
}

.calendar_dropdown ul{
  position: absolute;
  margin: 0;
  padding: 0;
  width: 100%;
  background: #ccc;
  transform-origin: top;
  transform: perspective(1000px) rotateX(-90deg);
  transition: 0.5s;
  z-index:10;
}

.calendar_dropdown ul.active{
  transform: perspective(1000px) rotateX(0deg);
}

.calendar_dropdown ul li {
  list-style: none;
}

.calendar_dropdown ul li a {
  display:block;
  text-align: center;
  text-decoration: none;
  background: #262626;
  color: white !important;
  border-bottom: 1px solid rgba(0, 0, 0,.2);
  transition: 0.5s;
  font-size: small;
}

.calendar_dropdown ul li a:hover{
  background: #0d7ad0;
}
.calender{
	color: #009CD0 !important;
	background: #F3F3F3 !important;
	font-weight: 500;
	border: 1px solid #d3d3d3;
}
</style>

	<div class="col-sm-3 col-md-4 pb-3" style="white-space: nowrap;padding-right: 10rem!important;">
		<div class="calendar_dropdown" style="display:inline-block;margin: -6px;">
			<button type="button" class="btn btn-light fs-12 calender" style="padding-right: 10px;" >Add to Calender</button>
			<?php 
					
			?>
			<ul>
				<?php 
					$calendarDate = date('Ymd',strtotime($event_start_date)) .'T'. date('Hi',strtotime($calendar_start_time)). '00/' . date('Ymd',strtotime($event_end_date)) .'T'. date('Hi',strtotime($calendar_end_time)) . '00';
					
					$start_outlook = date('Y-m-d',strtotime($event_start_date)) .'T'. date('H:i:s',strtotime($calendar_start_time)). '' ;

					$end_outlook = date('Y-m-d',strtotime($event_end_date)) .'T'. date('H:i:s',strtotime($calendar_end_time)). '';
					
					$startDate_yahoo = date('Ymd',strtotime($event_start_date)) .'T'. date('Hi',strtotime($calendar_start_time)). '00' ;
					
					$endDate_yahoo =  date('Ymd',strtotime($event_end_date)) .'T'. date('Hi',strtotime($calendar_end_time)) . '00';
				
					$eventDetails = 'To RSVP and see the complete details click here - ' . TAOH_SITE_URL_ROOT . '/events/d/' . taoh_slugify($title) . '-' . $response['eventtoken'];

					$eventLocation = isset($response['conttoken'][ 'full_location' ]) ? $response['conttoken'][ 'full_location' ] : '';
				
				?>
				<li><a target="_blank" href="<?php echo 'https://calendar.google.com/calendar/render?action=TEMPLATE&text='.str_replace("#","",$title).'&dates='.$calendarDate.'&details='.$eventDetails.'&location='.$eventLocation.'&sf=true&output=xml'; ?>">Google Calendar</a></li>

				<li><a target="_blank" href="<?php echo 'https://outlook.live.com/owa?subject='.str_replace("#","",$title).'&body='.$eventDetails.'&startdt='.$start_outlook.'&enddt='.$end_outlook.'&location='.$eventLocation.'&path=%2Fcalendar%2Faction%2Fcompose&rru=addevent'; ?>">Outlook Calendar</a></li>
			</ul>
		</div>
	</div>

<script type="text/javascript">
$(document).ready(function(){

  	$('.calender').click(function(){
  		$('ul').toggleClass('active');
		setTimeout(function(){
			$('ul').removeClass('active');
		}, 5000);
	});

})
</script>