<div>
<form id="searchFilter" onsubmit="searchFilter();return false" class="search-form p-0 rounded-0 bg-transparent shadow-none position-relative z-index-1">
        <div class="d-flex flex-wrap align-items-center">
            <div class="d-flex flex-wrap align-items-center flex-grow-1 mob--full-width">
                <div class="form-group mr-3 flex-grow-1">
                    <div class="" >
                        <label for="">Search Keyword <span style="display:none" id="searchClear" onclick="clearBtn('search')" class="badge badge-danger"><i class="la la-close"></i> Clear</span></label>
                        <input class="form-control" type="text" id="query" name="query" placeholder="Search all Events"
                        style="color: #303030;font-family: inherit;font-size: 13px;"
                        >
                       <!--  
                        <input name='query' id="query" class="form-control form--control pl-40px" type="text" name="text" placeholder="Search all events">
                        <span class="la la-search input-icon"></span> -->
                    </div>
                </div>
                <div class="form-group mr-3 flex-grow-1">
                <label for="">Search Location <span style="display:none" id="locationClear" onclick="clearBtn('geohash')" class="badge badge-danger"><i class="la la-close"></i> Clear</span> <span id="geoloaderArea"></span></label>
                    <?php echo field_location(); ?>                    
                </div>

                <?php
                /*
                ?>
                <div class="form-group mr-3 flex-grow-1">
                    <input name='location' class="form-control form--control pl-40px" type="text" name="text" placeholder="Located anywhere">
                    <span class="la la-map-marker input-icon"></span>
                    <div class="km-select-wrap">
                        <select name='radial' class="custom-select custom--select">
                            <option value="5">within 5 km</option>
                            <option value="10">within 10 km</option>
                            <option value="20" selected="">within 20 km</option>
                            <option value="50">within 50 km</option>
                            <option value="100">within 100 km</option>
                        </select>
                    </div>
                </div>
                <div class="form-group mr-3 flex-grow-1">
                    <select name='type' id="sort" class="form-control form--control">
                        <option selected="">Type: All</option>
                        <option value="seminar">Seminar</option>
                        <option value="webinar">Webinar</option>
                        <option value="jobfestival">Job festival</option>
                        <option value="jobfair">Job Fair</option>
                    </select>
                </div>
            <div class="form-group mr-3 flex-grow-1">
                <select name='cost' id="sort" class="form-control form--control">
                    <option  selected="">Cost: All</option>
                    <option value="free">Free</option>
                    <option value="paid">Paid</option>>
                </select>
            </div>
                <?php
                */
                ?>
            </div><!-- end d-flex -->
            <div class="search-btn-box mt-3">
                <button class="btn theme-btn">Search <i class="la la-search ml-1"></i></button>
            </div><!-- end search-btn-box -->
        </div>
   
    </form>
</div>