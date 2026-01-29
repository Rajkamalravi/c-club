<?php

if ( ! defined ( 'TAO_PAGE_TITLE' ) ) { define ( 'TAO_PAGE_TITLE', "Events - Grow your career at ".TAOH_SITE_NAME_SLUG ); }
if ( ! defined ( 'TAO_PAGE_DESCRIPTION' ) ) { define ( 'TAO_PAGE_DESCRIPTION', "Discover a variety of career development events at ".TAOH_SITE_NAME_SLUG.". Boost your professional skills and expand your industry knowledge today." ); }
if ( ! defined ( 'TAO_PAGE_KEYWORDS' ) ) { define ( 'TAO_PAGE_KEYWORDS', 	TAOH_SITE_NAME_SLUG." Career development events, ".TAOH_SITE_NAME_SLUG." Professional skill-building workshops, ".TAOH_SITE_NAME_SLUG." Networking conferences for professionals, ".TAOH_SITE_NAME_SLUG." Career advancement seminars, ".TAOH_SITE_NAME_SLUG." Professional development programs, ".TAOH_SITE_NAME_SLUG." Industry-specific training events, ".TAOH_SITE_NAME_SLUG." Personal branding workshops,".TAOH_SITE_NAME_SLUG." Leadership development conferences, ".TAOH_SITE_NAME_SLUG." Resume writing workshops,".TAOH_SITE_NAME_SLUG." Interview preparation seminars, ".TAOH_SITE_NAME_SLUG." Job search strategies, ".TAOH_SITE_NAME_SLUG." Mentorship programs, ".TAOH_SITE_NAME_SLUG." Job fair events,".TAOH_SITE_NAME_SLUG." Career exploration conferences, ".TAOH_SITE_NAME_SLUG." Personal and professional growth events, ".TAOH_SITE_NAME_SLUG." Professional networking opportunities" ); }
taoh_get_header();

// Share model
$share_link = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$fb_share = "http://www.facebook.com/sharer.php?
s=100
&p[url]=$share_link
&p[images][0]=".( defined( 'TAO_PAGE_IMAGE' )? TAO_PAGE_IMAGE : '' )."
&p[title]=".( defined( 'TAO_PAGE_TITLE' )? TAO_PAGE_TITLE : '' )."
&p[summary]=".(urlencode( defined( 'TAO_PAGE_DESCRIPTION' )? substr(TAO_PAGE_DESCRIPTION, 0, 240) : '' ));
$tw_share = "https://twitter.com/intent/tweet?text=".( urlencode( ( defined( 'TAO_PAGE_TITLE' )? TAO_PAGE_TITLE : '' ) ) )."&url=$share_link";
$link_share = "https://www.linkedin.com/shareArticle?mini=true&url=". ( urlencode($share_link) ) ."&title=".( urlencode( ( defined( 'TAO_PAGE_TITLE' )? TAO_PAGE_TITLE : '' ) ) )."&summary=".(urlencode( defined( 'TAO_PAGE_DESCRIPTION' )? substr(TAO_PAGE_DESCRIPTION, 0, 240) : '' ))."";
$email_share = "mailto:?subject=I wanted you to see this site&amp;body=Check out this site ".( defined( 'TAO_PAGE_TITLE' )? TAO_PAGE_TITLE : '' )." $share_link.";
//End model

$current_app = taoh_parse_url(0);
$app_data = taoh_app_info($current_app);
$taoh_user_vars = taoh_user_all_info();
?>
<style>
.divider{
    border: 0;
    clear:both;
    display:block;
    width: 86%;
    background-color: lightgrey;
    height: 1px;
    margin: 10px;
}
.fixed-bottom{
		width: 100%;
        background: white;
        padding: 10px 10px;
        color: black;
		bottom: 0px;
    }
	.icon-element-sm{
		font-size : 17px;
	}
	.copy-link{
		background-color: maroon;
    	padding: 5px;
    	border-radius: 20px;
		font-size: 30px;
		color: white;
	}
	.edit-status{
		padding-right: 25px;
		line-height: 19px;
	}

</style>
<section class="hero-area pb-10px bg-white shadow-sm overflow-hidden">
	<div class="container">
      <div class="hero-content d-flex flex-wrap align-items-center justify-content-between">
        <div class="pb-3 col-8">
        	<h2 class="section-title fs-24 mb-1">Events </h2>
          	<p class="section-desc pb-4"><?php echo $app_data->desc; ?></p>
			<?php ?>
			  <ul class="nav nav-tabs generic-tabs generic-tabs-layout-2 generic--tabs-layout-2" id="myTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" onclick="changeMod(this, 'my')">Upcoming Events</button>
                </li>
            </ul>
        </div>
        <div class="hero-btn-box">
            <?php if(taoh_user_is_logged_in() ) {
                    if ( isset( $_GET[ 'creator' ] ) && $_GET[ 'creator' ] ){
                        echo "
                        <a href=\"".TAOH_DASH_URL."?app=".$app_data->slug."&from=dash&to=".$app_data->slug."\" class=\"btn theme-btn w-100 mb-3\" style=\"background-color: #38B653;\"><i class=\"icon-line-awesome-wechat\"></i>My Events <i class=\"icon-material-outline-arrow-right-alt\"></i></a>
                        <a href=\"".TAOH_DASH_URL."?app=".$app_data->slug."&from=dash&to=".$app_data->slug."/post\" class=\"btn theme-btn w-100 mb-3\"><i class=\"icon-line-awesome-wrench\"></i> Post an Event <i class=\"icon-material-outline-arrow-right-alt\"></i></a>
                        ";
                    }
                } else {
                        echo "
                        <a href=\"".TAOH_LOGITAOH_SITE_URL_ROOTN_URL."/login/".$app_data->slug."\" class=\"btn theme-btn mb-3\"><i class=\"la la-sign-in mr-1\"></i> Login / Signup</a>
                        ";
                } ?>
        </div>
      </div>
  </div>
</section>

<section class="question-area pt-40px pb-40px">
    <div class="container">
        <div class="tab-content" id="myTabContent">
			<?php //include('search.php'); ?>
            <div class="tab-pane fade show active" id="events" role="tabpanel" aria-labelledby="events-tab">
				<div class="row">
				    <div class="col-lg-2">

				    </div><!-- end col-lg-2 -->
					<div class="col-lg-10">
						<h3 class="mb-3 h-title"></h3>
						<span id="loaderArea"></span>
                            <div id="eventArea" class="row"></div>
                            <div id="pagination"></div>
                    </div>
                </div>
			</div>
		</div>
	</div>
</section>

<!-- Modal -->
<div class="modal fade social-share" id="ShareModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="top:20%;" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Share this event</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  <div class="social-icon-box">
			<a class="mr-1 icon-element icon-element-sm shadow-sm text-gray hover-y" style="background-color:#365899; margin-bottom:10px;" href="<?php echo $fb_share; ?>" target="_blank" title="Share on Facebook">
			<svg focusable="false" class="svg-inline--fa fa-facebook-f fa-w-10" style="color: white;" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"></path></svg> <span style="position: fixed; padding-left: 20px;">Facebook</span>
			</a>
			<a class="mr-1 icon-element icon-element-sm shadow-sm text-gray hover-y" style="background-color:#00acee; margin-bottom:10px;" href="<?php echo $tw_share; ?>" target="_blank" title="Share on Twitter">
			<svg focusable="false" class="svg-inline--fa fa-twitter fa-w-16" style="color: white;" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"></path></svg> <span style="position: fixed; padding-left: 20px;">Twitter</span>
			</a>
			<a class="mr-1 icon-element icon-element-sm shadow-sm text-gray hover-y" style="background-color:#0A66C2; margin-bottom:10px;" href="<?php echo $link_share; ?>" target="_blank" title="Share on Linkedin">
			<svg focusable="false" class="svg-inline--fa fa-linkedin fa-w-14" style="color: white;" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M416 32H31.9C14.3 32 0 46.5 0 64.3v383.4C0 465.5 14.3 480 31.9 480H416c17.6 0 32-14.5 32-32.3V64.3c0-17.8-14.4-32.3-32-32.3zM135.4 416H69V202.2h66.5V416zm-33.2-243c-21.3 0-38.5-17.3-38.5-38.5S80.9 96 102.2 96c21.2 0 38.5 17.3 38.5 38.5 0 21.3-17.2 38.5-38.5 38.5zm282.1 243h-66.4V312c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9V416h-66.4V202.2h63.7v29.2h.9c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9V416z"></path></svg> <span style="position: fixed; padding-left: 20px;">Linkedin</span>
			</a>
			<a class="mr-1 icon-element icon-element-sm shadow-sm text-gray hover-y" style="background-color:#B23121; margin-bottom:10px;" href="<?php echo $email_share; ?>" target="_blank" title="Share vai Email">
			<svg focusable="false" class="svg-inline--fa fa-envelope fa-w-16" style="color: white;" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"></path></svg> <span style="position: fixed; padding-left: 20px;">Email</span>
			</a>
		</div>
		<form action="#" class="copy-to-clipboard d-flex flex-wrap align-items-center">
			<span class="text-success-message">Link Copied!</span>
				<input type="text" style="display:none;" class="form-control form--control form--control-bg-gray copys-input" id="copys-input" value="<?php echo $share_link; ?>">
				<span class="copys-btn" style="cursor: pointer;"> <i class="fas fa-copy copy-link"></i> Copy </span>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- End Model -->
<script type="text/javascript">
	let $ = jQuery;
	let loaderArea = $('#loaderArea');
	let searchQuery = $('#searchQuery');
	let eventArea = $('#eventArea');
	let locationSelectInput = $('#locationSelect');
	let activeChatTitle = $('#activeChatTitle');
	let geoHashInput = $('#geohash');
  	let currentMod = '<?php echo ($app_data?->slug ?? ''); ?>';
	let geoHash = "";
	let term = "";

	let totalItems = 0; //this will be rewriiten on response of events on line 207
	let itemsPerPage = 10;
	let currentPage = 0;

	$(document).ready(function(){
        taoh_events_init();
        $('#activeChat').hide();

        $('#share_btn').click(function(){
            $('#ShareModel').modal('show');
        });

        // copy btn
        $('.copys-btn').click(function(){
            var copyText = document.getElementById("copys-input");
            copyText.style.display = 'block';
            copyText.select();
            copyText.setSelectionRange(0, 99999)
            copyText.select();
            document.execCommand("copy");
            copyText.style.display = 'none';
            $(".text-success-message").addClass('active');
            setTimeout(function(){
                $(".text-success-message").removeClass('active'); }, 1000);
        })

	})

	function show_pagination(holder) {
            return $(holder).pagination({
                items: totalItems,
                itemsOnPage: itemsPerPage,
                currentPage: currentPage,
                onInit: function() {
                        $("#pagination ul").addClass('pagination');
                        $("#pagination ul li.disabled").addClass('page-link text-gray');
                        $("#pagination ul li.active").addClass('page-link bg-primary text-white');
                },
                onPageClick: function(pageNumber, event) {
                        $("#pagination ul").addClass('pagination');
                        $("#pagination ul li.disabled").addClass('page-link text-gray');
                        $("#pagination ul li.active").addClass('page-link bg-primary text-white');
                        currentPage = pageNumber;
                        taoh_events_init();
                }
            });
	}

	 locationSelectInput.on("change", function() {
			setTimeout(function(){
				geoHash = geoHashInput.val();
				taoh_events_init();
			}, 1000);
	 })

	searchQuery.keyup(function(){
		term = searchQuery.val();
		taoh_events_init();
	})

	function changeMod(e, item) {
		$('.nav-link').removeClass('active');
		$(e).addClass('active');
    currentMod = item;
		if(currentMod == 'rsvp') {
			//$('.h-title').html('My RSVP');
			taoh_rsvp_init();
		} else {
			//$('.h-title').html('UpComing Event');
			taoh_events_init();
		}
  }

  function searchFilter() {
		var queryString = $('#searchFilter').serialize();
		console.log(queryString);
		taoh_events_init(queryString);
	}

	function taoh_events_init (queryString=""){
		loader(true, loaderArea);
		var data = {
			 'taoh_action': 'events_get',
			 'ops': 'active',
			 'search': term,
			 'geohash': geoHash,
			 'offset': currentPage,
			 'limit': itemsPerPage,
			 'filters': queryString
		 };
		jQuery.get(_taoh_site_ajax_url, data, function(response) {
            response = parseJSONSafely(response);
			res = response;

			//render_events_template(res, eventArea);
			render_events_grid_template(res, eventArea);
			loader(false, loaderArea);
		}).fail(function() {
				loader(false, loaderArea);
	    	console.log( "Network issue!" );

	  })
	}

    function taoh_rsvp_init (){
		loader(true, loaderArea);
		var data = {
			 'taoh_action': 'get_my_rsvp',
			 'search': term,
			 'mod': 'events',
			 'geohash': geoHash,
			 'offset': currentPage,
			 'limit': itemsPerPage,
		 };
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) { console.log(response);
			res = response;

			//render_events_template(res, eventArea);
			render_rsvp_grid_template(res, eventArea);
			loader(false, loaderArea);
		}).fail(function() {
				loader(false, loaderArea);
	    	console.log( "Network issue!" );

	  })
	}

	function getCookie(cname) {
	  let name = cname + "=";
	  let decodedCookie = decodeURIComponent(document.cookie);
	  let ca = decodedCookie.split(';');
	  for(let i = 0; i <ca.length; i++) {
	    let c = ca[i];
	    while (c.charAt(0) == ' ') {
	      c = c.substring(1);
	    }
	    if (c.indexOf(name) == 0) {
	      return c.substring(name.length, c.length);
	    }
	  }
	  return "";
}

	//convert date from 2022-10-02T11:01:00 to readable
	function date_read(date, locality, timezone) {
		let options;
	//	if(locality && timezone) {
			if(locality === 0 ) {
        options = { weekday: 'short', month: 'short', day: 'numeric', hour: 'numeric', minute: "numeric", timeZone: timezone };
			} else {
        options = { weekday: 'short', month: 'short', day: 'numeric', hour: 'numeric', minute: "numeric", timeZone: getCookie('client_time_zone') };
			}
		//}

		let output = new Date(date);
		return output.toLocaleDateString('en-us', options);
	}

	function convertToSlug(Text) {
	  return Text.toLowerCase()
	             .replace(/[^\w ]+/g, '')
	             .replace(/ +/g, '-');
	}

	function format_object(data) {
		const output = {};
		output.total = data.total_numbers;
		output.items = [];
		let keyUpdated = [];

		function search(nameKey, myArray){
			for (var i=0; i < myArray.length; i++) {
					if (myArray[i].id === nameKey) {
							return myArray[i];
					}
			}
		}
		for (let [key, result] of Object.entries(data.output.list)) {

			if (typeof result.company != "undefined") {
				for (const [id, name] of Object.entries(result.company)) {
					var text = name.split(":>");
					result.company = {"id": id, "slug": text[0], name: text[1]};
				}
			}

			if (typeof result.locn != "undefined") {
				for (const [id, name] of Object.entries(result.locn)) {
					var text = name.split(":>");
					result.locn = {"id": id, "slug": text[0], name: text[1]};
				}
			}

			if (typeof result.skill != "undefined") {
				for (const [id, name] of Object.entries(result.skill)) {
					var text = name.split(":>");
					result.skill = {"id": id, "slug": text[0], name: text[1]};
				}
			}

			if (typeof result.rolechat != "undefined") {
				for (const [id, name] of Object.entries(result.rolechat)) {
					var text = name.split(":>");
					result.rolechat = {"id": id, "slug": text[0], name: text[1]};
				}
			}

			if (typeof result.roletype != "undefined") {
				let role = [
					{id: "remo", text: "Remote Work", color: "primary"},
					{id: "full", text: "Full Time", color: "success"},
					{id: "part", text: "Part Time", color: "danger"},
					{id: "temp", text: "Temporary", color: "warning"},
					{id: "free", text: "Freelance", color: "info"},
					{id: "cont", text: "Contract", color: "secondary"},
					{id: "pdin", text: "Paid Internship", color: "dark"},
					{id: "unin", text: "Unpaid Internship", color: "muted"},
					{id: "voln", text: "Volunteer", color: "success"}
				];

				let roles = [];
				$.each(result.roletype, function( index, value ) {
					roles.push(search(value, role));
				});
				result.roletype = roles;
			}
			output.items.push(result);
		 }
		 return output;
	}


	function render_events_grid_template(data, slot) {
		console.log(data);
		slot.empty();
		if(data.output === false ) {
			slot.append("<p>No data found!</p>");
			return false;
		}

		if(data.output.count == 0 ) {
			slot.append("<p>No data found!</p>");
			return false;
		}

		totalItems = data.output.count;
		console.log('total', totalItems)
		//data = format_object(data);
		$.each(data.output.list, function(i, v){
			slot.append(
				`<div class="row  col-md-12">
                    <div class="col-md-3">
                        <a target='_blank' href="<?php echo TAOH_SITE_URL_ROOT."/".($app_data?->slug ?? '')."/d/"; ?>${convertToSlug(v.title)}-${v.eventtoken}" class="card-img">
							<img class="lazy"  width="222" height="125" src="${v.event_image}" data-src="${v.event_image}" alt="${v.title}">
						</a>
                    </div>
                    <div class="col-md-8" style="position: relative;">
                        <span style="color: orange; font-weight: 600;">${date_read(v.local_start_at_read, v.locality, v.local_timezone)}</span>
                        <h5 class="card-title fw-medium">
                            <a href="<?php echo TAOH_SITE_URL_ROOT."/".($app_data?->slug ?? '')."/d/"; ?>${convertToSlug(v.title)}-${v.eventtoken}" style="color: black;"> ${v.title}</a>
                        </h5>
                        <p>
                            <span style="position: absolute; bottom: 0;"> attendee </span>
                            <span  style="position: absolute; bottom: -10px; right: 10%;">
                                <?php
                                    echo "<button type=\"button\" class=\"btn btn-link\" id=\"share_btn\" data-toggle=\"modal\" data-target=\"#ShareModel\"><i class=\"fa fa-share-square\" style=\"font-size:26px\"></i></button>";
                                 ?>

                            </span>
                        </p>
                    </div>
                </div>
                <div class="divider"></div>
                `);
		});
		if(totalItems >= 11) {
			//enable to hide pagination if items is below 10
			show_pagination('#pagination');
		}
	}

	function render_rsvp_grid_template(data, slot) {
		slot.empty();
		if(data.success === true && !data.output) {
			slot.append("<p>No RSVP found!</p>");
			return false;
		}
		totalItems = data.output.count;
		data = format_object(data);

		$.each(data.items, function(i, v){

			slot.append(
				`<div class="col-lg-12  media media-card media--card align-items-center">
					<div class="media-body border-left-0">
						<h5 class="pb-1 text-capitalize">
						<!--<a href="<?php //echo TAOH_SITE_URL_ROOT."/".$app_config[ 'slug' ]."/d/"; ?>${convertToSlug(v.title)}-${v.eventtoken}">${v.title}</a>&nbsp;&nbsp;-->
						 ${v.title}
						<span class="float-right">
							<a href="#" class="tag-link text-primary" onclick="message('${v.conttoken}')">Message</a>
							<a href="<?php echo "/".$app_config[ 'slug' ]."/manage/"; ?>${v.conttoken}" class="tag-link text-success">Manage</a>
						</span>
						</h5>

						${v.company ? `
							<span class="author text-gray">
								<a href="<?php echo TAOH_SITE_URL.'/'.$app_config[ 'slug' ]; ?>/chat/orgchat/${v.company.id}/${v.company.slug}">${v.company.name}</a>
							</span>
							`: ''
						}

						${v.locn ? `
							<span class=\"px-1\">-</span>
							<span> ${v.locn.name}</span>
							`: ''
						}

						${v.rolechat ? `
							<span class=\"px-1\">-</span>
							<span> ${v.rolechat.name}</span>
							<span class=\"px-1\">-</span>`: ''
						}
						${v.skill ? `
							<a href="<?php TAOH_SITE_URL.'/'.$app_config[ 'slug' ];?>/chat/skillchat/${v.skill.id}/${v.skill.slug}" class="tag-link">${v.skill.name}</a>`: ''
						}

					</div>
			</div>`);
		});
		if(totalItems >= 11) {
			//enable to hide pagination if items is below 10
			show_pagination('#pagination');
		}
	}


		function render_active_chat_list_template(data, slot) {
			slot.empty();
			if(!data) {
					slot.append('You dont have any active chat!');
			} else {
				$('#activeChat').hide();
				for (const [key, value] of Object.entries(data)) {
					if ( value[0] ){
						slot.append(`
							<div class="category-list">
								<a href="<?php echo TAOH_SITE_URL_ROOT."/chat/";?>${currentMod}/${value[1]}/${value[0]}" class="cat-item d-flex disabled align-items-center justify-content-between mb-3  hover-y">
										<span class="cat-title">${value[0]}</span>
										<span class="cat-number"></span>
								</a>
							</div>
						`);
					}
				}
			}
		}

		/*function member_runner() {
			var data = {
				'taoh_action': 'taoh_room_member_check_update',
				"ctime": listUpdatedAt,
				"mod" : currentMod
			 };
			jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
				console.log(response)
		    response = response;
		    if(response.status == 1) {
					loader(true, activeListloaderArea);
					//taoh_taoh_room_get_member_active_chat_init();
				}
			})
		}*/

</script>
<?php taoh_get_footer(); ?>
