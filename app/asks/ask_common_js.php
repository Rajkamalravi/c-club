<?php
$curr_page = taoh_parse_url(0);
if (!defined('TAO_PAGE_TYPE')) {
    define('TAO_PAGE_TYPE', $curr_page); // Error log fixing
}
$taoh_user_vars = $data = taoh_user_all_info();
$profile_complete = $data->profile_complete;
$ptoken = $user_ptoken = $data->ptoken;
$log_nolog_token = ( taoh_user_is_logged_in()) ? $ptoken : TAOH_API_TOKEN_DUMMY;
$taoh_url_vars = '';
?>
<script>
var profile_complete = '<?php echo $profile_complete; ?>';
let isLoggedIn = "<?php echo taoh_user_is_logged_in(); ?>";
$(document).on('click','.click_action', function(event) {
		var metrics = $(this).attr("data-metrics");
		
		save_metrics('asks',metrics,conttoken);     
		
		if(metrics == 'comment_click'){
			$('.command_form').attr('action','<?php echo TAOH_ACTION_URL .'/comments'; ?>');
			$('.command_form').submit();
		}else if(metrics == 'network_click'){
			var locc = '<?php echo TAOH_SITE_URL_ROOT."/".$current_app."/club/".$taoh_url_vars."/"; ?>';
			//alert(locc);
			window.location.href = locc;
		}
});

$(document).on("click", ".create_referral", function(event) {
		event.stopPropagation();
		var job_title = $(this).attr("data-title");
		var link = $(this).attr("data-sharelink");
		var data = {
			'taoh_action': 'taoh_apply_job_ask',			
			'from_link' : link,
			'detail_link': window.location.href,
			'ask_title' : job_title,
			};
		$("#loader").show();
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
			var res = JSON.stringify(response);
			//window.location.href = '<?php //echo TAOH_SITE_URL_ROOT.'/login';?>';

			var locc = link;
			var days = 1;
			var name  = '<?php echo TAOH_ROOT_PATH_HASH.'_'.'referral_back_url';?>';
			var value = locc;
			//alert(locc);
			var expires = "";
			if (days) {
				var date = new Date();
				date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
				expires = "; expires=" + date.toUTCString();
			}
			document.cookie = name + "=" + (value || "") + expires + "; path=/";
			localStorage.setItem('email', '');
			$('#config-modal').modal({show:true});
			
			if(res.success ==1){
				$("#loader").hide();
			}else{
				$("#loader").hide();
				$("#error_msg").html("Sorry, Something went wrong. Please try again.");
			}
		});
	});


function get_liked_check(conttoken){
		if(jQuery.inArray(conttoken,liked_arr) !== -1){
			var get_liked = 1;
		}else{
			var get_liked = 0;
		} 
		let is_local = localStorage.getItem(app_slug+'_'+conttoken+'_liked');
		if ((get_liked) || (is_local)) {
			var liked_checks = `<a class="fs-25 mr-2 ml-2 already-saved" style="vertical-align: text-bottom;">
				<!-- <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark-fill.svg" alt="bookmark-saved" class="bookmark-saved" style="width: 18px"> -->

				<svg width="20" height="20" viewBox="0 0 20 27" fill="none" xmlns="http://www.w3.org/2000/svg" class="bookmark-saved">
					<path d="M2.5 0.5H17.5C18.6041 0.5 19.5 1.39593 19.5 2.5V25.4014C19.4998 25.823 19.156 26.167 18.7344 26.167C18.5737 26.167 18.4201 26.1185 18.2939 26.0293L18.292 26.0283L10.2871 20.4238L10 20.2227L9.71289 20.4238L1.70801 26.0283L1.70605 26.0293C1.57991 26.1185 1.4263 26.167 1.26562 26.167C0.843959 26.167 0.500177 25.823 0.5 25.4014V2.5C0.5 1.39593 1.39593 0.5 2.5 0.5Z" fill="white" stroke=""/>
				</svg>
			</a>`;
		} else {
			var liked_checks = `<a class="fs-25 asks_like" style="cursor: pointer;">
			<!-- <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark.svg" alt="bookmark" data-cont="${(conttoken)}" class="ask_save" title="Save Ask" style="width: 18px"> -->
				<svg width="20" height="20" viewBox="0 0 20 27" fill="none" xmlns="http://www.w3.org/2000/svg" data-cont="${(conttoken)}" class="ask_save" title="Save Ask">
					<path d="M2.5 0.5H17.5C18.6041 0.5 19.5 1.39593 19.5 2.5V25.4014C19.4998 25.823 19.156 26.167 18.7344 26.167C18.5737 26.167 18.4201 26.1185 18.2939 26.0293L18.292 26.0283L10.2871 20.4238L10 20.2227L9.71289 20.4238L1.70801 26.0283L1.70605 26.0293C1.57991 26.1185 1.4263 26.167 1.26562 26.167C0.843959 26.167 0.500177 25.823 0.5 25.4014V2.5C0.5 1.39593 1.39593 0.5 2.5 0.5Z" fill="" stroke="white"/>
				</svg>
			</a>`;
		}
		//<img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark.svg" alt="bookark" style="width: 18px"> 
		//<i style="cursor:pointer;" data-cont="${(conttoken)}" title="Save Job" class="las la-bookmark ask_save"></i>
		return liked_checks;
	}

	
	
	$(document).on("click", ".ask_save", function(event) {
        event.stopPropagation(); // Stop the event from propagating to the parent
        var savetoken = $(this).attr('data-cont');
		$('.asks_like').find(`[data-cont='${savetoken}']`).attr('src',"<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark-fill.svg");
		$('.asks_like').find(`[data-cont='${savetoken}']`).removeClass('ask_save').addClass("already-saved").removeAttr("style");
		$('.asks_like').find(`[data-cont='${savetoken}']`).parent().removeAttr("style");
		localStorage.setItem(app_slug+'_'+savetoken+'_liked',1);
		delete_asks_into();
		var data = {
			 'taoh_action': 'ask_like_put',
			 'conttoken': savetoken,
			 'ptoken': '<?php echo $ptoken; ?>',
		};
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
			if(response.success){
				taoh_set_success_message('Ask Saved Successfully.');
			}else{
				taoh_set_error_message('Ask Save Failed.');
				console.log( "Like Failed!" );
			}
		}).fail(function() {
			console.log( "Network issue!" );
		})
    });

    $(document).on('click','.share_box', function(event) {
		var datatitle = $(this).attr("data-title");console.log(datatitle);
		var dataptoken = $(this).attr("data-ptoken");
		var datashare = $(this).attr("data-share");
		var dataconttoken = $(this).attr("data-conttoken");
		var share_link = $(this).attr("data-share");
		var dat_ajax = '<?php echo TAOH_SITE_URL_ROOT.'/ajax'?>';
		var image = '<?php echo ( defined( 'TAO_PAGE_IMAGE' )? TAO_PAGE_IMAGE : '' )?>';
		var desc = '<?php echo (urlencode( defined( 'TAO_PAGE_DESCRIPTION' )? substr(TAO_PAGE_DESCRIPTION, 0, 240) : '' ))?>';
		var title = '<?php echo ( defined( 'TAO_PAGE_TITLE' )? TAO_PAGE_TITLE : '' )?>';
		var fb_share = "http://www.facebook.com/sharer.php?s=100&p[url]="+share_link+"&p[images][0]="+image+"&p[title]="+title+"&p[summary]="+desc;
		var tw_share = "https://twitter.com/intent/tweet?text="+title+"&url="+share_link;
		var link_share = "https://www.linkedin.com/shareArticle?mini=true&url="+share_link+"&title="+title+"&summary="+desc+"&images="+image;
		var email_share = "mailto:?subject=I wanted you to see this site&amp;body=Check out this site "+title+share_link;

		$("#share_icon").html(`
						<div class="social-icon-box d-flex text-center" data-ajax="${(dat_ajax)}" data-conttype="<?php echo $app_data?->slug ?? ''; ?>">
							<a data-gtitle="${(datatitle)}" data-gptoken="${(dataptoken)}" data-gshare="${(datashare)}" data-gconntoken="${(dataconttoken)}" data-social="${(fb_share)}" class="ml-3 icon-element icon-element-sm shadow-sm text-gray hover-y media_share share_count" data-click="facebook" style="background-color:#365899; margin-bottom:10px;cursor:pointer;" target="_blank" title="Share on Facebook">
								<svg focusable="false" class="svg-inline--fa fa-facebook-f fa-w-10" style="color: white;" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"></path></svg>
							</a>
							<a data-gtitle="${(datatitle)}" data-gptoken="${(dataptoken)}" data-gshare="${(datashare)}" data-gconntoken="${(dataconttoken)}" data-social="${(tw_share)}" class="ml-3 icon-element icon-element-sm shadow-sm text-gray hover-y media_share share_count" data-click="twitter" style="background-color:#00acee; margin-bottom:10px;cursor:pointer;" target="_blank" title="Share on Twitter">
								<svg focusable="false" class="svg-inline--fa fa-twitter fa-w-16" style="color: white;" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"></path></svg>
							</a>
							<a data-gtitle="${(datatitle)}" data-gptoken="${(dataptoken)}" data-gshare="${(datashare)}" data-gconntoken="${(dataconttoken)}" data-social="${(link_share)}" class="ml-3 icon-element icon-element-sm shadow-sm text-gray hover-y media_share share_count" data-click="linkedin" style="background-color:#0A66C2; margin-bottom:10px;cursor:pointer;" target="_blank" title="Share on Linkedin">
								<svg focusable="false" class="svg-inline--fa fa-linkedin fa-w-14" style="color: white;" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M416 32H31.9C14.3 32 0 46.5 0 64.3v383.4C0 465.5 14.3 480 31.9 480H416c17.6 0 32-14.5 32-32.3V64.3c0-17.8-14.4-32.3-32-32.3zM135.4 416H69V202.2h66.5V416zm-33.2-243c-21.3 0-38.5-17.3-38.5-38.5S80.9 96 102.2 96c21.2 0 38.5 17.3 38.5 38.5 0 21.3-17.2 38.5-38.5 38.5zm282.1 243h-66.4V312c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9V416h-66.4V202.2h63.7v29.2h.9c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9V416z"></path></svg>
							</a>
							<a data-gtitle="${(datatitle)}" data-gptoken="${(dataptoken)}" data-gshare="${(datashare)}" data-gconntoken="${(dataconttoken)}" data-social="${(email_share)}" class="ml-3 icon-element icon-element-sm shadow-sm text-gray hover-y media_share share_count" data-click="email" style="background-color:#B23121; margin-bottom:10px;cursor:pointer;" target="_blank" title="Share vai Email">
								<svg focusable="false" class="svg-inline--fa fa-envelope fa-w-16" style="color: white;" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"></path></svg>
							</a>
						</div>
						<div class="text-center mt-2 mb-2"> or </div>
							<span class="text-success-message">Link Copied!</span>
								<input type="text" style="display:none;" class="form-control form--control form--control-bg-gray copys-input" id="copys-input" value="${(share_link)}">
								<div class="copys-btn text-center" style="cursor: pointer;"> <i class="fas fa-copy"></i> Copy URL</div>
					`);
		$("#exampleModal1").modal('show');
	});

    $(document).on('click','.post_answer', function(event) {
		event.preventDefault();
		if(isLoggedIn){
			if(profile_complete == 0){
                if (typeof showBasicSettingsModal === 'function') {
                    showBasicSettingsModal();
                }
                // taoh_set_error_message('Complete your settings to fully use the platform.');
				//window.location.href = '<?php //echo TAOH_SITE_URL_ROOT; ?>///settings';
				return false;
			}
		}
		//alert();
		$("#scroll_show").show();
		$('html, body').animate({
			scrollTop: $("#scroll_id").offset().top
		}, 2000);
	});

</script>