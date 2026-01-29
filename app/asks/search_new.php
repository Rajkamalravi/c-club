<section style="padding: 0 0 30px;">
	<div  class="container">


		<div class="search-filter-section" style="max-width: 900px; margin: 0 auto">
			<form id="searchFilter" onsubmit="searchFilter();return false" class="search-form p-0 rounded-0 bg-transparent shadow-none position-relative z-index-1">
				<div class="form-row justify-content-center">

					<div class="col col-md-4">

						<span id="searchClear" style="display:none" onclick="clearBtn('search')" class="badge badge-danger">
							<i class="la la-close"></i>
						</span>
						<div class="icon-position-form">

							<img src="<?php echo TAOH_CDN_PREFIX.'/assets/wertual/images/search.svg' ?>" style="width: 14px; position: absolute; left: 10px; top: 10px;" alt="Search">
							<input type="search" class="form-control" id="query" name="query" placeholder="Ask Title, Company Name">
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
						<img src="<?php echo TAOH_CDN_PREFIX.'/assets/wertual/images/calendar3.svg'; ?>" style="width: 14px; position: absolute; left: 10px; top: 12px; z-index: 2;" alt="Date">
							<select id="postdate" name="post_date" class="form-control">
								<option value="" selected>Select Post Date</option>
								<option value="today">Today</option>
								<option value="yesterday">Yesterday</option>
								<option value="last_week">Last Week</option>
								<option value="last_month">Last Month</option>
								<option value="date_range">Date Range</option>
							</select>
						</div>
						<div class="form-row" id="dateRangeInputs">
							<div class="col-6">
								<div class="icon-position-form">
								<img src="<?php echo TAOH_CDN_PREFIX.'/assets/wertual/images/calendar3.svg'; ?>" style="width: 14px; position: absolute; left: 10px; top: 12px; z-index: 2;" alt="Date">
									<input type="date" id="from_date" name="from_date" class="form-control" placeholder=" Filter by Date">
								</div>
							</div>
							<div class="col-6">
								<div class="icon-position-form">
								<img src="<?php echo TAOH_CDN_PREFIX.'/assets/wertual/images/calendar3.svg'; ?>" style="width: 14px; position: absolute; left: 10px; top: 12px; z-index: 2;" alt="Date">
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
	</div>
</section>