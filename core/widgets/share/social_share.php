<?php
$share_link = $data['share_data'];
$conttype = $data['conttype'];
$ptoken = $data['ptoken'];
$conttoken = $data['conttoken'];
$fb_share1 = "http://www.facebook.com/sharer.php?s=100&p[url]=".$share_link."&p[images][0]=".( defined( 'TAO_PAGE_IMAGE' )? TAO_PAGE_IMAGE : '' )."&p[title]=".( defined( 'TAO_PAGE_TITLE' )? TAO_PAGE_TITLE : '' )."&p[summary]=".(urlencode( defined( 'TAO_PAGE_DESCRIPTION' )? substr(TAO_PAGE_DESCRIPTION, 0, 240) : '' ));
$tw_share1 = "https://twitter.com/intent/tweet?text=".( urlencode( ( defined( 'TAO_PAGE_TITLE' )? TAO_PAGE_TITLE : '' ) ) )."&url=".$share_link;
$link_share1 = "https://www.linkedin.com/shareArticle?mini=true&url=". ( urlencode($share_link) ) ."&title=".( urlencode( ( defined( 'TAO_PAGE_TITLE' )? TAO_PAGE_TITLE : '' ) ) )."&summary=".(urlencode( defined( 'TAO_PAGE_DESCRIPTION' )? substr(TAO_PAGE_DESCRIPTION, 0, 240) : '' ))."&images=".( defined( 'TAO_PAGE_IMAGE' )? TAO_PAGE_IMAGE : '' )."";
//$email_share = "mailto:?subject=I wanted you to see this site here&amp;body=Check out this site";
//$email_share = "mailto:?subject=I wanted you to see this site&amp;body=Check out this site ".( defined( 'TAO_PAGE_TITLE' )? TAO_PAGE_TITLE : '' )." $share_link.";

$desc = defined( 'TAO_PAGE_DESCRIPTION' ) ? TAO_PAGE_DESCRIPTION : '';
$page_title = defined('TAO_PAGE_TITLE') ? TAO_PAGE_TITLE : '';
$page_image = defined('TAO_PAGE_IMAGE') ? TAO_PAGE_IMAGE : '';


/* $desc = defined( 'TAO_PAGE_DESCRIPTION' ) ? substr(TAO_PAGE_DESCRIPTION, 0, 240) : '';
$fb_share = `https://www.facebook.com/sharer/sharer.php?&u=${share_link}&p[images][0]=".( defined( 'TAO_PAGE_IMAGE' )? TAO_PAGE_IMAGE : '' )."&p[title]=".( defined( 'TAO_PAGE_TITLE' )? TAO_PAGE_TITLE : '' )."&p[summary]=".(urlencode(${desc}))`;
$tw_share = `https://www.linkedin.com/sharing/share-offsite/?url=${share_link}&text=".( urlencode( ( defined( 'TAO_PAGE_TITLE' )? TAO_PAGE_TITLE : '' ) ) ).`;
$link_share = `https://twitter.com/intent/tweet?url=${share_link}&text=${desc}&title=".( urlencode( ( defined( 'TAO_PAGE_TITLE' )? TAO_PAGE_TITLE : '' ) ) )."&summary=".(urlencode( ${desc} ))."&images=".( defined( 'TAO_PAGE_IMAGE' )? TAO_PAGE_IMAGE : '' )."`;
 */
$subject = "I wanted you to see this site";
$email_subject = json_encode($subject);
$email_body = json_encode("Check out this event: " . $page_title . " - " . $desc . " " . $share_link);
$share_text_js = json_encode($desc);
$share_title_js = json_encode($page_title);
?>
<div class="social-icon-box d-flex text-center justify-content-center">
    <a class="ml-3 icon-element icon-element-sm shadow-sm text-gray hover-y share_counts" data-click="facebook" style="background-color:#365899; margin-bottom:10px;cursor:pointer;" target="_blank" title="Share on Facebook">
        <svg focusable="false" class="svg-inline--fa fa-facebook-f fa-w-10" style="color: white;" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"></path></svg>
    </a>
    <a class="ml-3 icon-element icon-element-sm shadow-sm text-gray hover-y share_counts" data-click="twitter" style="background-color:#00acee; margin-bottom:10px;cursor:pointer;" target="_blank" title="Share on Twitter">
        <svg focusable="false" class="svg-inline--fa fa-twitter fa-w-16" style="color: white;" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"></path></svg>
        <!-- <svg focusable="false" class="svg-inline--fa fa-twitter fa-w-16" width="16" height="16" viewBox="0 0 29 26" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.65 0H27.0625L17.425 11.0125L28.7625 26H19.8875L12.9312 16.9125L4.98125 26H0.5625L10.8687 14.2188L0 0H9.1L15.3812 8.30625L22.65 0ZM21.1 23.3625H23.5438L7.76875 2.5H5.14375L21.1 23.3625Z" fill="white"/>
        </svg> -->
    </a>
    <a class="ml-3 icon-element icon-element-sm shadow-sm text-gray hover-y share_counts" data-click="linkedin" style="background-color:#0A66C2; margin-bottom:10px;cursor:pointer;" target="_blank" title="Share on Linkedin">
        <svg focusable="false" class="svg-inline--fa fa-linkedin fa-w-14" style="color: white;" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M416 32H31.9C14.3 32 0 46.5 0 64.3v383.4C0 465.5 14.3 480 31.9 480H416c17.6 0 32-14.5 32-32.3V64.3c0-17.8-14.4-32.3-32-32.3zM135.4 416H69V202.2h66.5V416zm-33.2-243c-21.3 0-38.5-17.3-38.5-38.5S80.9 96 102.2 96c21.2 0 38.5 17.3 38.5 38.5 0 21.3-17.2 38.5-38.5 38.5zm282.1 243h-66.4V312c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9V416h-66.4V202.2h63.7v29.2h.9c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9V416z"></path></svg>
    </a>
    
</div>
<!-- <div class="text-center mt-2 mb-2"> or </div> -->
    <span class="text-success-message">Link Copied!</span>
        <input type="text" style="display:none;" class="form-control form--control form--control-bg-gray copys-inputs" id="copys-input" value="<?php echo $share_link; ?>">
        <!-- <div class="copys-btn text-center" style="cursor: pointer;"> <i class="fas fa-copy"></i> Copy URL</div> -->

<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '1271794846576386',   // <-- replace with your FB App ID
      cookie     : true,
      xfbml      : true,
      version    : 'v18.0'          // use latest available version
    });
  };
// Load Facebook SDK
(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); 
    js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

function sharOnFacebook(url){
	var description = <?php echo json_encode($share_text_js, JSON_UNESCAPED_UNICODE); ?>;
	const shareData = {
        method: 'share',
        href: url,
        quote:description, // Add description for timeline posts
        
    };
    // Open Facebook share dialog
    FB.ui(shareData, function(response) {
        console.log('Facebook Response:', response);
        if (response && !response.error_message) {
            // Share was successful
            localStorage.setItem('event_'+event_id+'_shared', true);
            alert("Congrats! Discount unlocked 🎉");
        }
	});
    
}

$(document).ready(function(){
	// copy btn 
	$('.copys-btns').click(function(){
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

    $(document).on('click','.share_counts', function(event) {
		//console.log(event)
        var dataId = $(this).attr("data-click");
		save_metrics('<?php echo $conttype;?>','share','<?php echo $conttoken; ?>');	
		//console.log('<?php echo $share_link; ?>')
		
		if(currentShareLink != ''){
			share_url = currentShareLink;
		}else{
			share_url = '<?php echo $share_link; ?>';
		}
		
		if(dataId == 'facebook'){
			taoh_events_enable_share_discount(share_url,'fb');
			/*sharOnFacebook(share_url);
			*/
		}else if (dataId == 'twitter') {
			taoh_events_enable_share_discount(share_url,'tw');
			//window.open("<?php echo $tw_share; ?>", '_blank').focus();
		}else if (dataId == 'linkedin') {
			taoh_events_enable_share_discount(share_url,'li');
			//window.open("<?php echo $link_share; ?>", '_blank').focus();
		}else{
			event.preventDefault(); // Stop default behavior
			let subject = <?php echo $email_subject; ?>;
			let body = <?php echo $email_body; ?>;

			let mailtoLink = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;

			window.open(mailtoLink, "_blank"); // Open email client in new tab
		}
		
	});
	function taoh_events_enable_share_discount(share_url, platform){
		//alert('=========='+currentShareLink)
		if(currentShareLink != ''){
			var shareUrl  = currentShareLink;
		}else{
			var shareUrl  = share_url; //window.location.href; // event page url
		}
		
		//var shareText = encodeURIComponent("Checkout this awesome event!");
		// Use actual event content instead of generic text
		var shareText = encodeURIComponent(<?php echo json_encode($share_text_js, JSON_UNESCAPED_UNICODE); ?>);
		var shareTitle = encodeURIComponent(<?php echo json_encode($share_title_js, JSON_UNESCAPED_UNICODE); ?>);
		var url       = "";

		switch(platform){
			case 'fb':
				// Facebook - include quote parameter for text in timeline
				url = `https://www.facebook.com/sharer/sharer.php?u=${shareUrl}&quote=${shareText}`;
				break;
			case 'li':
				// LinkedIn - include title and summary for professional sharing
				//url = `https://www.linkedin.com/sharing/share-offsite/?url=${shareUrl}&title=${shareTitle}&summary=${shareText}`;
				url = `https://www.linkedin.com/sharing/share-offsite/?url=${shareUrl}&title=${shareTitle}&summary=${shareText}`;
				break;
			case 'tw':
			default:
				// Twitter - include both title and description in tweet
				// Twitter - include image parameter and proper formatting
				var imageUrl = '<?php echo defined('TAO_PAGE_IMAGE') ? TAO_PAGE_IMAGE : ''; ?>';
				var tweetText = `${shareTitle}: ${shareText}`;
				if (imageUrl) {
					url = `https://twitter.com/intent/tweet?text=${tweetText}&url=${shareUrl}&via=&related=`;
				} else {
					url = `https://twitter.com/intent/tweet?text=${tweetText}&url=${shareUrl}`;
				}
				break;
		}
		console.log(event)
		var popup = window.open(url, '_blank','width=600,height=600');
		console.log(popup)
		// poll for popup close
		var timer = setInterval(function(){
			if(popup.closed){
				clearInterval(timer);
				// mark in local indexeddb
				localStorage.setItem('event_'+event_id+'_shared', true);
				// call backend to log share
				//taoh_events_log_share(event_id, platform);
				// unlock discount
				/* document.getElementById('discount_block').classList.remove('disabled');
				document.getElementById('discount_code').value = 'SHARE10';  // example code */
				alert("Congrats! Discount unlocked 🎉");
			}
		},500);
	}

});

</script>