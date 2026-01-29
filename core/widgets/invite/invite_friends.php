<?php
$actual_link = '';
if(isset($data) && $data !=''){
    $actual_link = $data;
}else{
    $actual_link = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}
//echo "============".$actual_link;
//isset(TAO_PAGE_TYPE) && TAO_PAGE_TYPE ? $app = TAO_PAGE_TYPE : $app = 'Networking';
//$app = ( isset(TAO_PAGE_TYPE) && TAO_PAGE_TYPE) ? TAO_PAGE_TYPE : 'Networking';
$url = $_SERVER['HTTP_REFERER'] ?? '';
//$actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; //die();
define('TAOH_INVITE_REFERRAL_URL', $actual_link);
$referral_code = $referral_code[ 'key' ] ?? '';
?>
<div class="card card-item light-dark-card">
	<div class="card-body">
		<div class="sidebar-questions">
			<div class="media">
				<div class="media-body">
					<center>
					<a class="nav-link d-flex flex-column text-center"><img src="<?php echo TAOH_SITE_URL_ROOT."/assets/images/invite_button.png"; ?>" width=100 style="max-width:100px;" /></a>
					<small class="meta">
					<!-- <a target="_blank" class="author" target="_blank" data-toggle="modal" data-target="#invite-modal">
					Let's invite a friend
					</a> -->
					<h5>
						<a>Invite a friend</a>
					</h5>
					<ul class="social-icons">
					<li> <a href="javascript:void(0);" id="invite_modal" target="_blank" class="hover-y light-dark" aria-pressed="true" data-toggle="modal" data-target="#invite-modal"><i class="la la-envelope-o" style="font-size: 18px;"></i></a></li>
					<!-- sample link: https://dev.unmeta.net/hires/go/ref/g0ge17vh2r74lf7-->
					<li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo TAOH_INVITE_REFERRAL_URL; ?>" target="_blank" class="hover-y light-dark"><i class="la la-facebook" style="font-size: 18px;"></i></a></li>
					<li><a href="https://twitter.com/intent/tweet?url=<?php echo TAOH_INVITE_REFERRAL_URL; ?>&text=I%20invite%20you%20to%20checkout%20Hires%2C%20where%20we%20grow%20together" class="hover-y light-dark" target="_blank"><i class="fa-brands fa-x-twitter" style="font-size: 16px;"></i></a></li>
                    <li><a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo TAOH_INVITE_REFERRAL_URL; ?>&title=I%20invite%20you%20to%20checkout%20Hires%2C%20where%20we%20grow%20together" class="hover-y light-dark" target="_blank"><i class="la la-linkedin" style="font-size: 18px;"></i></a></li>
					<!-- <li><a href="#" class="hover-y"><i class="la la-instagram"></i></a></li>
					<li><a href="http://pinterest.com/pin/create/button/?url=<?php //echo TAOH_INVITE_REFERRAL_URL; ?>&media=<?php //echo TAOH_SITE_FAVICON; ?>&description=I%20invite%20you%20to%20checkout%20Hires%2C%20where%20we%20grow%20together" class="hover-y" target="_blank"><i class="la la-pinterest" style="font-size: 18px;"></i></a></li>
					<li><a href="https://www.tumblr.com/widgets/share/tool?canonicalUrl=<?php //echo TAOH_INVITE_REFERRAL_URL; ?>&caption=https://dev.unmeta.net/hires/go/ref/g0ge17vh2r74lf7&tags=tao.ai" class="hover-y" target="_blank"><i class="la la-tumblr" style="font-size: 18px;"></i></a></li>
					<li><a href="https://api.whatsapp.com/send?text=%0a<?php //echo TAOH_INVITE_REFERRAL_URL; ?>" class="hover-y" target="_blank"><i class="la la-whatsapp" style="font-size: 18px;"></i></a></li>-->
				</ul>
				<p class="pb-2">Copy and paste your referral link</p>
				<form action="#" class="copy-to-clipboard d-flex flex-wrap align-items-center">
					<span class="text-success-message">Link Copied!</span>
					<div class="input-group">
						<input type="text" id="copy-text" class="form-control form--control form--control-bg-gray copy-input light-dark" value="<?php echo TAOH_INVITE_REFERRAL_URL."/".$referral_code; ?>">
						<div class="input-group-append">
							<button type="button" onclick="copyText()"  class="btn theme-btn copy-btn"><i class="la la-copy mr-1"></i> Copy</button>
						</div>

					</div>
				</form>
					<!--<a class="btn btn-primary btn-lg active" role="button" aria-pressed="true" style="color: #fff; background-color:#f4876a;font-size: 13px;" data-toggle="modal" data-target="#invite-modal" onclick="invitea_friend();">Invite a friend</a>-->
					</small>
					</center>
				</div>
			</div><!-- end media -->
		</div>
	</div>
</div>

<div class="modal top fade" id="invite-modal" tabindex="" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
	<div class="modal-content">
	<div class="modal-header blue_bg">
		<h5 class="modal-title">Invite a friend</h5>
	</div>
	<div class="modal-body">
		<div class="main-box">
			<!-- <div class="learn-title"><h3>Jobs</h3></div>   -->
			<!-- <form> -->
			<div class="row" id="invite_form">
			<div id="error_msg" class="fs-14 text-danger error_msg" style="padding-left:15px;"> </div>
				<div class="col-12" id="invite_formdiv">
					<div class="inline-box">
						<div class="form-floating fs-14 mb-3">
							First Name
							<input type="text" class="form-control form--control fs-14 validate" id="iname" placeholder="Enter your Firstname" name="iname" value="">
						</div>
					</div>
					<div class="inline-box">
						<div class="form-floating fs-14 mb-3">
							Last Name
							<input type="text" class="form-control form--control fs-14 validate" id="ilname" placeholder="Enter your Lastname" name="ilname" value="">
						</div>
					</div>
					<div class="inline-box">
						<div class="form-floating fs-14 mb-3">
							Email
							<input type="text" class="form-control form--control fs-14 validate" id="iemail" placeholder="Email" name="iemail" value="">
							<div id="emailerror_msg" class="fs-14 text-danger error_msg"> </div>
						</div>
					</div>
					<div class="inline-box">
						<div class="form-floating fs-14 mb-3">
							Message
							<textarea class="form-control form--control fs-14 validate" id="icomment" placeholder="I would like to invite you to checkout this page (<?php echo TAOH_INVITE_REFERRAL_URL; ?>)" rows="3" name="icomment"></textarea>
							<div id="commentserror_msg" class="fs-14 text-danger error_msg"> </div>
						</div>
					</div>
					<div class="col-12 row">
						<button onclick="invitationSubmit('#invite_form')" data-login="login" class="fs-14 btn btn-lg btn-primary invbtn">
							<span id="loadingText">Submit</span>
						</button> &nbsp;&nbsp;
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
				</div>
			</div>
			<!-- </form> -->
		</div>
	</div>
	<!-- <div class="modal-footer">

	</div> -->
	</div>
</div>
</div>
<script>
    $('#invite_modal').click(function(){
        $("#error_msg").html('');
        $("#invite_formdiv").show();
    })

    function chk_validations(id) { //alert(id);
        var req = 0;
        $(id + ' .validate').each(function (index) {  //alert();
            $(this).removeClass('error_class');
            if ($(this).val() == '' || ($(this).is('select') && $(this).val().trim() == '')) {
                // $(id + ' #error_msg').html("Please Enter All Fields");
                $(this).addClass('error_class');
                req = 1;
                $(this).keyup(function () {
                    $(this).removeClass('error_class');
                });
            }
        });
        if (req == 1) {
            return false;
        } else
            return true;
    }

    function validateEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }

    $(document).on("focus", "#icomment", function () {
        $('#icomment').text('I would like to invite you to checkout this page (<?php echo TAOH_INVITE_REFERRAL_URL; ?>)');
    });
    // invite a friend
    function invitationSubmit(formid) {
        // e.preventDefault();
        if (chk_validations(formid)) {
            $("#loader").show();
            let iname = $("#iname").val();
            let ilname = $("#ilname").val();
            let iemail = $("#iemail").val();
            let icomment = $("#icomment").val();

            var data = {
                'taoh_action': 'taoh_invite_friend',
                'first_name': iname,
                'last_name': ilname,
                'email': iemail,
                'comment': icomment,
                'from_link': window.location.href,
                'network_title': '<?php echo $title; ?>',
                'app_name' : '<?php echo $app; ?>',
                'referral_link': '<?php echo TAOH_INVITE_REFERRAL_URL; ?>',
            };
            $(".invbtn").prop('disabled', true);
            $("#loader").hide();
            if (!validateEmail(iemail)) {
                $('#emailerror_msg').html("Please Enter Valid Email Address");
                $("#iemail").addClass('error_class');
                $(".invbtn").prop('disabled', false);
                return false;
            } else {
                $("#iemail").removeClass('error_class');
                $(".error_msg").html("");
                $("#loader").show();
                $("#error_textmsg").html("Please wait..!");
                jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function (response) {
                    $("#loader").hide();
                    if (response == 1) {
                        $('#invite_form input').val('');
                        $("#invite_formdiv").hide();
                        $("#error_msg").html("<span class='green_text'>Thank You! Invitation has been sent. <span data-dismiss='modal' style='color:red;cursor:pointer;'><b>Close</b></span></span>");
                    } else {
                        $("#error_msg").html("Sorry, Something went wrong. Please try again.");
                        // $('#invite_form input').val('');
                        $("#invite_formdiv").hide();
                    }
                });

                $(".invbtn").prop('disabled', false);
            }
        }
        return false;
    }

    function copyText() {
        var yourToolTip = document.querySelector('.your-tooltip');
        /* Select text area by id*/
        var Text = document.getElementById("copy-text");
        /* Select the text inside text area. */
        Text.select();
        /* Copy selected text into clipboard */
        var copy_text = navigator.clipboard.writeText(Text.value);
        if (copy_text) {
            $('.text-success-message').addClass('active');
            setTimeout(function () {
                $('.text-success-message').removeClass('active');
            }, 2000);
        }
    }
</script>