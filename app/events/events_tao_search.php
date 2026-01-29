<div class="container px-lg-5">
      <form id="searchFilter" onsubmit="searchFilter();return false"  class="search-form search-con d-flex flex-column flex-lg-row p-0">

            <div class="" style="flex: 1;">
               <span id="searchClear" style="display:none" onclick="clearBtn('search')" class="badge badge-danger">
							<i class="la la-close"></i>
						</span>
               <input type="search" id="query" name="query" class="form-control mb-lg-0 first" placeholder="Search for events">
               <!-- <input type="date" name="event" class="form-control mb-lg-0" placeholder="Search with Date"> -->
            </div>
            <div class="" style="flex: 1; position: relative;">
               <img src="<?php echo TAOH_CDN_PREFIX.'/assets/wertual/images/calendar3.svg';?>" style="width: 14px; position: absolute; left: 10px; top: 39px; z-index: 2;" alt="Date">
               <select id="postdate" name="post_date" class="form-control mb-lg-0" style="padding-left: 33px;">
                  <option value="" selected>Select Post Date</option>
                  <option value="today">Today</option>
                  <option value="yesterday">Yesterday</option>
                  <option value="last_week">Last Week</option>
                  <option value="last_month">Last Month</option>
                  <option value="date_range">Date Range</option>
               </select>
               <div class="form-row" id="dateRangeInputs" style="display:none;">
                  <div class="col-6">
                     <div class="icon-position-form">

                        <input type="date" id="from_date" name="from_date" class="form-control" placeholder=" Filter by Date">
                     </div>
                  </div>
                  <div class="col-6">
                     <div class="icon-position-form">

                        <input type="date" id="to_date" name="to_date" class="form-control" placeholder=" Filter by Date">
                     </div>
                  </div>
               </div>
            </div>
            <div class="mb-2 mb-lg-0 loc" style="flex: 1;">
               <span style="display:none" id="locationClear" onclick="clearBtn('geohash')" class="badge badge-danger"></span>
               <?php echo field_locations(); ?>
            </div>
            <div class="">
               <button type="submit" class="btn submit-btn last w-100">Search</button>
            </div>

</div>