<section>
	<div > <!--  class="row justify-content-center mx-0 mb-3 px-3" style="gap: 12px;" -->


			<div class="text-center px-3">
				<div class="horizontal-scroll">


					<?php
						$navJustifyClass = 'justify-content-center';
						if (taoh_user_is_logged_in()) {
							if (!empty($_GET['creator'])) {
								$navJustifyClass = 'justify-content-md-center';
							} else {
								$navJustifyClass = 'justify-content-sm-center';
							}
						}
					?>
					<ul class="nav nav-tabs mb-3 <?php echo $navJustifyClass ?> flex-nowrap text-nowrap" id="myTab" role="tablist" style="border-bottom: 0;">
						<li class="nav-item">
							<a class="nav-link active" id="events-tab" data-toggle="tab" href="#events" role="tab" aria-controls="events" onclick="get_event_type()" aria-selected="true">All Events</a>
						</li>
						<?php if (taoh_user_is_logged_in()) { ?>
						<li class="nav-item">
							<a class="nav-link" id="rsvp-tab2" data-toggle="tab" href="#rsvp" role="tab" aria-controls="rsvp" onclick="get_event_type('rsvp_list')" aria-selected="false">Registered Events</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="save-tab2" data-toggle="tab" href="#save" role="tab" aria-controls="save" onclick="get_event_type('saved')" aria-selected="false">Saved Events</a>
						</li>
						<?php } ?>


						<?php
							if (taoh_user_is_logged_in()) {
								if (isset($_GET['creator']) && $_GET['creator']) {

									echo '<li class="mr-3"><a href="' . TAOH_SITE_URL_ROOT . '/' . ($app_data?->slug ?? '') . '/post" class="btn theme-btn">Post Events</a></li>';
									echo '<li><a href="' . TAOH_SITE_URL_ROOT . '/' . ($app_data?->slug ?? '') . '/dash" class="btn theme-btn" style="background-color: #38B653;">My Events</a></li>';

								}
							} else {
								echo '<a  aria-pressed="true" data-toggle="modal" data-target="#config-modal" class="login-button btn theme-btn mr-3"><i class="la la-sign-in mr-1"></i> Login / Signup</a>';
							}
						?>

					</ul>
				</div>
			</div>
			<!-- <div class="" >
				<?php
				//	if (taoh_user_is_logged_in()) {
				//		if (isset($_GET['creator']) && $_GET['creator']) {
				//			echo '<div class="d-flex" style="gap: 12px;">';
				//			echo '<a href="' . TAOH_SITE_URL_ROOT . '/' . ($app_data?->slug ?? '') . '/post" class="btn theme-btn">Post Events</a>';
				//			echo '<a href="' . TAOH_SITE_URL_ROOT . '/' . ($app_data?->slug ?? '') . '/dash" class="btn theme-btn" style="background-color: #38B653;">My Events</a>';
				//			echo '</div>';
				//		}
				//	} else {
				//		echo '<a  class="login-button btn theme-btn mr-3"><i class="la la-sign-in mr-1"></i> Login / Signup</a>';
				//	}
				?>
			</div> -->
	</div>

		<div class="search-filter-section px-3" style="max-width: 900px; margin: 0 auto;">
			<form id="searchFilter" onsubmit="searchFilter();return false" class="search-form p-0 rounded-0 bg-transparent shadow-none position-relative z-index-1">
				<div class="form-row">
					<div class="col col-md-4">
						<span id="searchClear" style="display:none" onclick="clearBtn('search')" class="badge badge-danger">
							<i class="la la-close"></i>
						</span>
						<div class="icon-position-form">
							<img src="<?php echo TAOH_CDN_PREFIX.'/assets/wertual/images/search.svg' ?>" style="width: 14px; position: absolute; left: 10px; top: 10px;" alt="Search">
							<input type="search" class="form-control" id="query" name="query" placeholder="Event Title, Company Name">
						</div>
					</div>
					<div class="col-md-3 mb-2 d-none d-md-block">
						<span id="locationClear" style="display:none" onclick="clearBtn('geohash')" class="badge badge-danger">
							<i class="la la-close"></i>
						</span>
						<div class="icon-position-form">
						<img src="<?php echo TAOH_CDN_PREFIX.'/assets/wertual/images/geo-alt-fill.svg'; ?>" style="width: 14px; position: absolute; left: 10px; top: 12px; z-index: 2;" alt="Location">
							<?php echo field_location(); ?>
						</div>
					</div>
					<div class="col-md-3 date-range-dropdown d-none d-md-block">
						<div class="icon-position-form">
						<img src="<?php echo TAOH_CDN_PREFIX.'/assets/wertual/images/calendar3.svg';?>" style="width: 14px; position: absolute; left: 10px; top: 12px; z-index: 2;" alt="Date">
							<select id="postdate" name="post_date" class="form-control">
								<option value="" selected>Select Post Date</option>
								<option value="today">Today</option>
								<option value="tomorrow">Tomorrow</option>
								<option value="this_week">This Week</option>
								<option value="next_week">Next Week</option>
								<option value="this_month">This Month</option>
								<!-- <option value="next_month">Next Month</option> -->
								<!-- <option value="yesterday">Yesterday</option>
								<option value="last_week">Last Week</option>
								<option value="last_month">Last Month</option> -->
								<option value="date_range">Date Range</option>
							</select>
						</div>
						<div class="form-row" id="dateRangeInputs">
							<div class="col-6">
								<div class="icon-position-form">
								<img src="<?php echo TAOH_CDN_PREFIX.'/assets/wertual/images/calendar3.svg';?>" style="width: 14px; position: absolute; left: 10px; top: 12px; z-index: 2;" alt="Date">
									<input type="date" id="from_date" name="from_date" class="form-control" placeholder=" Filter by Date">
								</div>
							</div>
							<div class="col-6">
								<div class="icon-position-form">
								<img src="<?php echo TAOH_CDN_PREFIX.'/assets/wertual/images/calendar3.svg';?>" style="width: 14px; position: absolute; left: 10px; top: 12px; z-index: 2;" alt="Date">
									<input type="date" id="to_date" name="to_date" class="form-control" placeholder=" Filter by Date">
								</div>
							</div>
						</div>
					</div>
					<div class="col-auto col-md-2">
						<button style="border-radius:15px;" class="btn btn-primary btn-block"><span class="d-none d-md-inline-block mr-1">Search</span> <i class="la la-search"></i></button>
					</div>
				</div>
			</form>
		</div>
</section>