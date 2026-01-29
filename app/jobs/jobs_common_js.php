<?php
$curr_page = taoh_parse_url(0);
if (!defined('TAO_PAGE_TYPE')) {
    define('TAO_PAGE_TYPE', $curr_page); // Error log fixing
}
// $taoh_user_vars = $data = taoh_user_all_info();
// $ptoken = $user_ptoken = $data->ptoken;
$taoh_user_vars = $data = taoh_user_all_info();

// Ensure $data is an object // Error log fixing
if (!is_object($data)) {
    $data = (object) [];
}

$ptoken = $user_ptoken = $data->ptoken ?? ''; // Avoid accessing undefined properties
$log_nolog_token = ( taoh_user_is_logged_in()) ? $ptoken : TAOH_API_TOKEN_DUMMY;
?>
<script type="application/javascript">
    var _taoh_site_url_root = '<?php echo TAOH_SITE_URL_ROOT; ?>';
    var app_slug = '<?php echo TAO_PAGE_TYPE ?>';
    function delete_jobs_into(){
		getIntaoDb(dbName).then((db) => {
			let dataStoreName = JOBStore;
			const transaction = db.transaction(dataStoreName, 'readwrite');
			const objectStore = transaction.objectStore(dataStoreName);
			const request = objectStore.openCursor();
			request.onsuccess = (event) => {
			const cursor = event.target.result;
			if (cursor) {
				const index_key = cursor.primaryKey;
				if(index_key.includes('job'))
				{
				objectStore.delete(index_key);
				}
				cursor.continue();
			}
			};
		}).catch((err) => {
			console.log('Error in deleting data store');
		});
	}

    $(document).on("click", ".create_referral", function(event) {
		event.stopPropagation();
		alert();
		var job_title = $(this).attr("data-title");
		var link = $(this).attr("data-sharelink");
		var data = {
			'taoh_action': 'taoh_apply_job_referral',
			'from_link' : link,
			'detail_link': window.location.href,
			'job_title' : job_title,
			};
		$("#loader").show();
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
			var res = JSON.stringify(response);
			//window.location.href = '<?php //echo TAOH_SITE_URL_ROOT.'/login';?>';
			var locc = $(location).attr('href');
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

			$('#isCodeSent').hide();
			$('#isCodeNotSent').show();
			$('#config-modal').modal({show:true});
			if(res.success ==1){
				$("#loader").hide();
			}else{
				$("#loader").hide();
				$("#error_msg").html("Sorry, Something went wrong. Please try again.");
			}
		});
	});


	$(document).on("click", ".click_action", function(event) {
		event.stopPropagation();
		var metrics = $(this).attr("data-action");
		var taoh_url_vars = $(this).attr("job-url");
		var conttoken = $(this).attr("data-conttoken");

		save_metrics('jobs',metrics,conttoken);

		if(metrics == 'apply_click'){
            let applyLink = '<?php echo addslashes($apply_link ?? ''); ?>';
            if (applyLink) {
                window.open(applyLink, '_blank');
            }
		}
		else if (metrics == 'apply_through_scout_link') {
			var lin = _taoh_site_url_root+"/"+app_slug+"/professional-dashboard/"+taoh_url_vars+"/apply/";
			//alert( lin);
			window.location.href = lin;
		}
		else if (metrics == 'view_application') {
			var apply_id = $(this).attr("apply-id");
			var linkkk = _taoh_site_url_root+"/"+app_slug+"/professional-dashboard/"+taoh_url_vars+"/view_application/"+apply_id;
			//alert(linkkk);
			window.location.href = linkkk;
		}
		else if (metrics == 'request_through_scout_link') {
			var req_link = _taoh_site_url_root+"/"+app_slug+"/professional-dashboard/"+taoh_url_vars+"/request_to_refer/";
			//alert('--------',req_link);
			window.location.href = req_link;
		}
		else if(metrics == 'scout_dashboard'){
			var req_link = _taoh_site_url_root+"/"+app_slug+"/scout-dashboard/";
			//alert('--------',req_link);
			window.location.href = req_link;
		}
		else if(metrics == 'employer_dashboard'){
			var req_link = _taoh_site_url_root+"/"+app_slug+"/employer-dashboard/";
			//alert('--------',req_link);
			window.location.href = req_link;
		}
		/* else if (metrics == 'email_click') {
			window.location.href = '<?php //echo 'mailto:'.$apllicant_email; ?>';
		} */
	});

    function get_liked_check(conttoken){
		if(jQuery.inArray(conttoken,liked_arr) !== -1){
			var get_liked = 1;
		}else{
			var get_liked = 0;
		}
		let is_local = localStorage.getItem(app_slug+'_'+conttoken+'_liked');
		if ((get_liked) || (is_local)) {
			var liked_checks = `<a class="fs-25 mr-2 ml-2 already-saved" onclick="event.stopPropagation();" style="vertical-align: text-bottom;">
				<!-- <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark-fill.svg" alt="bookmark-saved" class="bookmark-saved" style="width: 18px"> -->
				<svg width="20" height="20" viewBox="0 0 20 27" fill="none" xmlns="http://www.w3.org/2000/svg" class="bookmark-saved">
					<path d="M2.5 0.5H17.5C18.6041 0.5 19.5 1.39593 19.5 2.5V25.4014C19.4998 25.823 19.156 26.167 18.7344 26.167C18.5737 26.167 18.4201 26.1185 18.2939 26.0293L18.292 26.0283L10.2871 20.4238L10 20.2227L9.71289 20.4238L1.70801 26.0283L1.70605 26.0293C1.57991 26.1185 1.4263 26.167 1.26562 26.167C0.843959 26.167 0.500177 25.823 0.5 25.4014V2.5C0.5 1.39593 1.39593 0.5 2.5 0.5Z" fill="white" stroke=""/>
				</svg>
			</a>`;
		} else {
			var liked_checks = `<a class="fs-25 jobs_like" style="cursor: pointer;">
			<!--<img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark.svg" alt="bookmark" data-cont="${(conttoken)}" class="job_save" title="Save Job" style="width: 18px"> -->
				<svg width="20" height="20" viewBox="0 0 20 27" fill="none" xmlns="http://www.w3.org/2000/svg" data-cont="${(conttoken)}" class="job_save" title="Save Job">
					<path d="M2.5 0.5H17.5C18.6041 0.5 19.5 1.39593 19.5 2.5V25.4014C19.4998 25.823 19.156 26.167 18.7344 26.167C18.5737 26.167 18.4201 26.1185 18.2939 26.0293L18.292 26.0283L10.2871 20.4238L10 20.2227L9.71289 20.4238L1.70801 26.0283L1.70605 26.0293C1.57991 26.1185 1.4263 26.167 1.26562 26.167C0.843959 26.167 0.500177 25.823 0.5 25.4014V2.5C0.5 1.39593 1.39593 0.5 2.5 0.5Z" fill="" stroke="white"/>
				</svg>
			</a>`;
		}
		//<img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark.svg" alt="bookark" style="width: 18px">
		//<i style="cursor:pointer;" data-cont="${(conttoken)}" title="Save Job" class="las la-bookmark job_save"></i>
		return liked_checks;
	}

    $(document).on("click", ".job_save", function(event) {
        event.stopPropagation(); // Stop the event from propagating to the parent
        var savetoken = $(this).attr('data-cont');
		$('.jobs_like').find(`[data-cont='${savetoken}']`).attr('src',"<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark-fill.svg");
		$('.jobs_like').find(`[data-cont='${savetoken}']`).removeClass('job_save').addClass("already-saved").removeAttr("style");
		$('.jobs_like').find(`[data-cont='${savetoken}']`).parent().removeAttr("style");
		localStorage.setItem(app_slug+'_'+savetoken+'_liked',1);
		delete_jobs_into();

		save_metrics('jobs','like',savetoken);
		var data = {
			 'taoh_action': 'job_like_put',
			 'conttoken': savetoken,
			 'ptoken': '<?php echo $ptoken; ?>',
		};
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
			if(response.success){
				taoh_set_success_message('Job Saved Successfully.');
			}else{
				taoh_set_error_message('Job Save Failed.');
				console.log( "Like Failed!" );
			}
		}).fail(function() {
			console.log( "Network issue!" );
		})
    });


	function truncateHTML(html, maxLength) {
		// Create a temporary DOM element to parse the HTML
		var tempDiv = document.createElement("div");
		tempDiv.innerHTML = html;

		// Initialize a variable to hold the length and a counter
		var length = 0;
		var truncatedHTML = '';

		// Function to recursively traverse the nodes
		function traverseNodes(nodes) {
			for (var i = 0; i < nodes.length; i++) {
				var node = nodes[i];

				if (node.nodeType === Node.TEXT_NODE) {
					// Add text content to truncatedHTML
					var text = node.textContent;
					length += text.length;

					if (length > maxLength) {
						// If we've reached the max length, truncate and break
						truncatedHTML += text.substring(0, text.length - (length - maxLength)) + "...";
						return true; // Stop traversal
					} else {
						truncatedHTML += text;
					}
				} else if (node.nodeType === Node.ELEMENT_NODE) {
					// Preserve the tag
					truncatedHTML += `<${node.tagName.toLowerCase()}>`;
					if (traverseNodes(node.childNodes)) {
						truncatedHTML += `</${node.tagName.toLowerCase()}>`;
						return true; // Stop traversal
					}
					truncatedHTML += `</${node.tagName.toLowerCase()}>`;
				}
			}
			return false; // Continue traversal
		}

		traverseNodes(tempDiv.childNodes);
		return truncatedHTML;
	}

	function decodeHTMLEntities(str) {
		var doc = new DOMParser().parseFromString(str, "text/html");
		return doc.documentElement.textContent || doc.documentElement.innerText;
	}

	// Event handler for opening the modal
	$(document).on("click", ".open_modal", function(event) {
		event.stopPropagation();
		var mod_conttoken = $(this).attr("data-conttoken");
		var mod_position = taoh_title_desc_decode($(this).attr("data-position"));
		var mod_company = $(this).attr("data-company");
		var mod_toemail = $(this).attr("data-toemail");
		var mod_fname = $(this).attr("data-fname");

		$('.mod_conntoken').val(mod_conttoken);
		$('.recruiter_fname').val(mod_fname);
		$('.to_email').val(mod_toemail);
		$('.position_title').val(mod_position).html(mod_position);
		$('.company_name').val(mod_company).html(mod_company);
		$('.placeType').html($(this).attr("data-placeType"));

		 // Decode and format the description
		var encodedDescription = $(this).attr("data-description");
		var decodedDescription = decodeURIComponent(encodedDescription);  // Decode URL-encoded description

		// Decode HTML entities (e.g., &lt; -> <)
		decodedDescription = decodeHTMLEntities(decodedDescription);
		console.log("Formatted Description:", decodedDescription);

		// Replace + signs with spaces
		decodedDescription = decodedDescription.replace(/\+/g, ' ');
		console.log("Formatted Description after replace + with spaces:", decodedDescription);


		$('.full-text').html(decodedDescription); // Full content

		var $content = $(".full-text");
		var fullText = $content.html(); // Store the full HTML content

		// Generate short text using the truncate function
		var shortText = truncateHTML(fullText, 200); // Adjust length as needed

		// Store the full and short content
		$content.data("full-text", fullText);
		$content.data("short-text", shortText);

		// Display short text
		$content.html(shortText); // Show the truncated version

		$(".toggle-link").show();
		$('.apply_modal').modal('show');
	});

	// Toggle functionality
	$(document).on("click", ".toggle-link", function(event) {
		event.preventDefault();
		var $this = $(this);
		var $content = $(".full-text");

		if ($this.text() === "Show More") {
			$content.html($content.data("full-text")); // Full content
			$this.text("Show Less");
		} else {
			$content.html($content.data("short-text")); // Short content
			$this.text("Show More");
		}
	});


    $('#fileToUpload').change(function() { //alert('file changed');
		var file = $('#fileToUpload')[0].files[0].name;
		$('.custom-file-label').html(file);
		document.getElementById("fileToUpload").style.display = "block";
        var type = $('#fileToUpload')[0].files[0].name.split('.').pop();
        var size = $('#fileToUpload')[0].files[0].size;
        console.log(type);
        if(size > 5242880){
            $('#error2').show();
            return false;
        }
        $('#error2').hide();
        if(type != 'pdf' && type != 'doc' && type != 'docx'){
            document.getElementById("responseMessage").style.display = "none";
            return false;
        }
        $('#fileToUpload-error').hide();
        $('.error').hide();

        // Reference the form by its ID
        var form = document.getElementById('fileUploadForm');
        // Create a FormData object using the form ID
        var formData = new FormData(form);
		document.getElementById("fileToUpload").style.display = "block";
        fetch("<?php echo TAOH_CDN_PREFIX; ?>/cache/upload/now", {
            method: "POST",
            body: formData,
        })
        .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((data) => {
                if (data.success) {
					var data_url = data.output;
                    $('.resume_link').val(data_url);
					document.getElementById("responseMessage").style.color = "green";
					document.getElementById("responseMessage").innerHTML = "File uploaded successfully";
                } else {
                    document.getElementById("responseMessage").style.color = "red";
                    document.getElementById("responseMessage").innerHTML = "File upload failed: " + data.output;
                }
                document.getElementById("responseMessage").style.display = "block";
            })
            .catch((error) => {
                console.error("Error:", error);
                document.getElementById("responseMessage").style.color = "red";
                document.getElementById("responseMessage").innerHTML = "An error occurred: " + error.output;
                document.getElementById("responseMessage").style.display = "block";
            });
	});

    document.getElementById("fileUploadForm").addEventListener("submit", function (event) {
        event.preventDefault();
		if($("#fileUploadForm").valid()){
			$('.submit').prop("disabled", true);
			// add spinner to button
			$('.submit').html(
				`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`
			);
			var serialize = $('#fileUploadForm').serialize();
			var editor_val = $('.summernote').summernote('code');

			var apply_method = enable_scout_apply ? 'scout_apply' : 'apply';
			var contt = $('.mod_conntoken').val();

			save_metrics('jobs','applyform',contt);

				delete_jobs_into();
				var r_data = serialize+"&cover_letter=" + encodeURIComponent(editor_val);
				jQuery.post("<?php echo TAOH_ACTION_URL .'/jobs?uslo=2'; ?>", r_data, function(responses) {
					res = responses;console.log(res.success);
					if(res.success){
						$('.submit').prop("disabled", false);
						// add spinner to button
						$('.submit').html(
							`Submit`
						);
						$('.apply_modal').modal('hide');
						taoh_set_success_message('Your application has been submitted.');
						window.location.reload();
					}
					else{
						$('.submit').prop("disabled", true);
						// add spinner to button
						$('.submit').html(
							`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`
						);
					}
					}).fail(function() {
						console.log( "Network issue111!" );
					})
		}
    });

    $.validator.addMethod('filesize', function(value, element, param) {
	return this.optional(element) || (element.files[0].size <= param)
	}, 'File size must be less than {0} bytes');
	$(function($) {
		"use strict";
		$("#fileUploadForm").validate({
				rules: {
					fname:"required",
					lname : "required",
					email : {
						required : true,
						email : true
					},
					coordinates:"required",
					fileToUpload:{
						required: true,
						extension: "pdf,doc,docx",
						filesize: 5242880 // <- 5 MB
					}
				},
				messages: {
					fname: "First Name is required",
					lname : "Last Name is required",
					email:{
						url : "Please enter vaild email"
					},
					coordinates:"Location is required",
					fileToUpload:{
						filesize:" file size must be less than 1MB.",
						extension:"Please upload .pdf or .doc or .docx file of notice.",
						required:"Please upload file."
					}
				},
		});
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
		var title = datatitle;
		var fb_share = "http://www.facebook.com/sharer.php?s=100&p[url]="+share_link+"&p[images][0]="+image+"&p[title]="+title+"&p[summary]="+desc;
		var tw_share = "https://twitter.com/intent/tweet?text="+title+"&url="+share_link;
		var link_share = "https://www.linkedin.com/shareArticle?mini=true&url="+share_link+"&title="+title+"&summary="+desc+"&images="+image;
		var email_share = "mailto:?subject=I wanted you to see this site&amp;body=Check out this site "+title+share_link;

		$("#share_icon").html(`
						<div class="social-icon-box d-flex text-center" data-ajax="${(dat_ajax)}" data-conttype="<?php echo ($app_data?->slug ?? ''); ?>">
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
								<div class="copys-btn text-center media_share " data-gptoken="${(dataptoken)}" data-gshare="${(datashare)}" data-gconntoken="${(dataconttoken)}" style="cursor: pointer;"> <i class="fas fa-copy"></i> Copy URL</div>
					`);
		$("#exampleModal1").modal('show');
	});

	$(document).on('click','.profile_incomplete', function(event) {
		event.preventDefault();
        if (typeof showBasicSettingsModal === 'function') {
            showBasicSettingsModal();
        }
		// taoh_set_error_message('Complete your settings to fully use the platform.');
		//window.location.href = '<?php //echo TAOH_SITE_URL_ROOT; ?>///settings';
		return false;
	});
</script>