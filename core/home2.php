<?php taoh_get_header(); 
if (taoh_user_is_logged_in() ){
	$title_text = "Welcome ".taoh_user_nice_name().", apps for you";
} else {
	$title_text = "Welcome Guest, apps for you";
}
$reads = json_decode(file_get_contents('https://preapi.tao.ai/sandbox/users.dash.get?&token='.TAOH_API_TOKEN.'&q=SEARCH&ops=flash'),true);
$read = $reads['output'];
$asks = json_decode(file_get_contents('https://preapi.tao.ai/sandbox/users.dash.get?&token='.TAOH_API_TOKEN.'&q=SEARCH&ops=asks'),true);
$ask = $asks['output'];
$jobs = json_decode(file_get_contents('https://preapi.tao.ai/sandbox/users.dash.get?&token='.TAOH_API_TOKEN.'&q=SEARCH&ops=jobs'),true);
$job = $jobs['output'];
$events = json_decode(file_get_contents('https://preapi.tao.ai/sandbox/users.dash.get?&token='.TAOH_API_TOKEN.'&q=SEARCH&ops=events'),true);
$event = $events['output'];
$tricks = json_decode(file_get_contents('https://preapi.tao.ai/sandbox/users.dash.get?&token='.TAOH_API_TOKEN.'&q=SEARCH&ops=tricks'),true);
$trick = $tricks['output'];
$apps = taoh_available_apps();
?>
<style>
	.carousel-control-prev-icon,
	.carousel-control-next-icon {
	height: 60px;
	width: 60px;
	outline: black;
	background-size: 100%, 100%;
	border-radius: 50%;
	background-image: none;
	}

	.carousel-control-next-icon:after
	{
	content: '>';
	font-size: 30px;	
	color: black;
	top: -6px;
    position: absolute;
    right: 85px;
	}

	.carousel-control-prev-icon:after {
	content: '<';
	font-size: 30px;
	color: black;
	top: -6px;
    position: absolute;
    right: 85px;
	}
</style>
<section class="testimonial-area section--padding">
    <div class="container">
		<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
			<div class="carousel-inner">
				<?php foreach($read as $keys => $vals){?>
				<div class="carousel-item active text-center">
					<div class="card card-item">
						<div class="card-body">
							<div class="sidebar-questions pt-3">
								<div class="media media-card media--card media--card-2">
									<div class="media-body">
										<h5>
											<a target="_blank" href="http://localhost/hires/hires-i/learning/3wqf4sb4r4">The buddhist on death row: how one man found light in the darkest place</a>
										</h5>
										<small class="meta">
											<span class="pr-1">by</span>
											<a target="_blank" class="author" href="http://localhost/hires/hires-i/learning/?q=au6fzmb3un&amp;type=author">
												David Sheff                          </a>
										</small>
									</div>
								</div><!-- end media -->
							</div>
						</div><!-- end col-lg-4 -->
					</div>
				</div>
				<?php } ?>
			</div>
			<a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="sr-only">Previous</span>
			</a>
			<a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="sr-only">Next</span>
			</a>
		</div>
	</div><!-- end container -->
</section>
<section class="get-started-area pt-80px pb-50px pattern-bg">
    <div class="container">
        <div class="row pt-50px">
			<div class="col-lg-6 responsive-column-half">
				<div class="card card-item hover-y text-center">
					<a href="http://localhost/hires/hires-i/asks">
						<div class="card-body">
							<img src="https://precdn.tao.ai/app/asks/images/asks_sq_250.png" alt="Asks" width=80>
							<h5 class="card-title pt-4 pb-2">Asks&nbsp;&nbsp;</h5>
							<?php foreach($ask as $akey => $aval){?>
							<p class="card-text text-gray"><?php echo $ask[$akey]['title']; ?></p>
							<?php } ?>
							<br />
						</div>
					</a>
					<div class="m-3">
						<a href="#" class="btn theme-btn theme-btn-outline">More... <i class="la la-arrow-right icon ml-1"></i></a>
					</div>
				</div><!-- end card -->
            </div><!-- end col-lg-4 -->
			<div class="col-lg-6 responsive-column-half">
				<div class="card card-item hover-y text-center">
					<a href="http://localhost/hires/hires-i/events">
						<div class="card-body">
							<img src="https://precdn.tao.ai/app/events/images/events_sq_250.png" alt="Events" width=80>
							<h5 class="card-title pt-4 pb-2">Events&nbsp;&nbsp;</h5>
							<?php foreach($event as $ekey => $eval){?>
							<p class="card-text text-gray"><?php echo $event[$ekey]['title']; ?></p>
							<?php } ?>
							<br />
						</div>
					</a>
					<div class="m-3">
						<a href="#" class="btn theme-btn theme-btn-outline">More... <i class="la la-arrow-right icon ml-1"></i></a>
					</div>
				</div><!-- end card -->
        	</div><!-- end col-lg-4 -->				
		</div><!-- end row -->
		<div class="row pt-50px">	
			<div class="col-lg-6 responsive-column-half">
				<div class="card card-item hover-y text-center">
					<a href="http://localhost/hires/hires-i/asks">
						<div class="card-body">
							<img src="https://precdn.tao.ai/app/jobs/images/jobs_sq_250.png" alt="Asks" width=80>
							<h5 class="card-title pt-4 pb-2">Jobs&nbsp;&nbsp;</h5>
							<?php foreach($job as $jkey => $jval){?>
							<p class="card-text text-gray"><?php echo $job[$jkey]['title']; ?></p>
							<?php } ?>
							<br />
						</div>
					</a>
					<div class="m-3">
						<a href="#" class="btn theme-btn theme-btn-outline">More... <i class="la la-arrow-right icon ml-1"></i></a>
					</div>
                </div><!-- end card -->
            </div><!-- end col-lg-4 -->
			<div class="col-lg-6 responsive-column-half">
				<div class="card card-item hover-y text-center">
					<a href="http://localhost/hires/hires-i/events">
						<div class="card-body">
							<img src="https://precdn.tao.ai/app/events/images/events_sq_250.png" alt="Events" width=80>
							<h5 class="card-title pt-4 pb-2">Events&nbsp;&nbsp;</h5>
							<p class="card-text text-gray">Platform for globally connected events to empower workers to build a successful network through learning and growth events.</p>
							<br />
						</div>
					</a>
					<div class="m-3">
						<a href="#" class="btn theme-btn theme-btn-outline">More... <i class="la la-arrow-right icon ml-1"></i></a>
					</div>
                </div><!-- end card -->
            </div><!-- end col-lg-4 -->				
		</div><!-- end row -->
    </div><!-- end container -->
</section>
<?php taoh_get_footer(); ?>
