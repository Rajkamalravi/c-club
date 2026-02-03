<?php

/*
Configure for Google Search Console
*/
// TAO_PAGE_AUTHOR
define('TAO_PAGE_AUTHOR', 'AskObviousBaba');
// TAO_PAGE_DESCRIPTION
define('TAO_PAGE_DESCRIPTION', '');
// TAO_PAGE_IMAGE
define('TAO_PAGE_IMAGE','');
// TAO_PAGE_TITLE
define('TAO_PAGE_TITLE', 'AskObviousBaba');
// TAO_PAGE_TWITTER_SITE
// TAO_PAGE_ROBOT
define('TAO_PAGE_ROBOT', 'index, follow');
// TAO_PAGE_CANONICAL

$taoh_vals = array(
    
);
$taoh_call = "/health/appload.php?url=".urlencode($_SERVER[ 'REQUEST_URI' ]);
$taoh_call_type = "get";
$out = json_decode(taoh_apicall_get( $taoh_call, $taoh_vals, $prefix=TAOH_OPS_PREFIX ), true);
if (0)
if ( ! isset( $out[ 'success' ] ) || ! $out[ 'success' ]  ){ header( "Location: ".TAOH_SITE_URL_ROOT."/backsoon" ); taoh_exit(); }
taoh_get_header();  
?>
<style>
#chat1 .form-outline .form-control~.form-notch div {
pointer-events: none;
border: 1px solid;
border-color: #eee;
box-sizing: border-box;
background: transparent;
}

#chat1 .form-outline .form-control~.form-notch .form-notch-leading {
left: 0;
top: 0;
height: 100%;
border-right: none;
border-radius: .65rem 0 0 .65rem;
}

#chat1 .form-outline .form-control~.form-notch .form-notch-middle {
flex: 0 0 auto;
max-width: calc(100% - 1rem);
height: 100%;
border-right: none;
border-left: none;
}

#chat1 .form-outline .form-control~.form-notch .form-notch-trailing {
flex-grow: 1;
height: 100%;
border-left: none;
border-radius: 0 .65rem .65rem 0;
}

#chat1 .form-outline .form-control:focus~.form-notch .form-notch-leading {
border-top: 0.125rem solid #39c0ed;
border-bottom: 0.125rem solid #39c0ed;
border-left: 0.125rem solid #39c0ed;
}

#chat1 .form-outline .form-control:focus~.form-notch .form-notch-leading,
#chat1 .form-outline .form-control.active~.form-notch .form-notch-leading {
border-right: none;
transition: all 0.2s linear;
}

#chat1 .form-outline .form-control:focus~.form-notch .form-notch-middle {
border-bottom: 0.125rem solid;
border-color: #39c0ed;
}

#chat1 .form-outline .form-control:focus~.form-notch .form-notch-middle,
#chat1 .form-outline .form-control.active~.form-notch .form-notch-middle {
border-top: none;
border-right: none;
border-left: none;
transition: all 0.2s linear;
}

#chat1 .form-outline .form-control:focus~.form-notch .form-notch-trailing {
border-top: 0.125rem solid #39c0ed;
border-bottom: 0.125rem solid #39c0ed;
border-right: 0.125rem solid #39c0ed;
}

#chat1 .form-outline .form-control:focus~.form-notch .form-notch-trailing,
#chat1 .form-outline .form-control.active~.form-notch .form-notch-trailing {
border-left: none;
transition: all 0.2s linear;
}

#chat1 .form-outline .form-control:focus~.form-label {
color: #39c0ed;
}

#chat1 .form-outline .form-control~.form-label {
color: #bfbfbf;
}

.alert_error {
    background-color: #f95668;
    border: 2px solid #f95668;
}
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
    color: #fff;
    text-transform: uppercase;
    font-size: 12px;
}
.alert {
    position: relative;
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
    border-radius: 0.25rem;
}
</style>
<section  style="background: #333" class="blog-area pt-40px pb-80px">
    <div class="container">
    <!--<div class="alert alert_error w-50 text-center" style="margin-left: 25%;" id="alert_error"> 
        <strong>Message should not be empty!!!</strong> 
    </div> -->
        <div class="row">
            <div class="col-lg-12 text-center">
                <img src="<?php echo TAOH_OBVIOUS_PREFIX.'/images/obviousbaba_logo.png';  ?>" height="60">
                <div>
                    <h3 class="text-white"></h3>
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-12 col-lg-12 col-xl-12">

                            <div class="card" id="chat1" style="border-radius: 15px; background-image: url('<?php echo TAOH_OBVIOUS_PREFIX.'/images/obviousbaba.png';  ?>'); background-color: #333; background-position: center; background-repeat: no-repeat; ">
                                <div
                                class="card-header d-flex justify-content-between align-items-center p-3 text-white border-bottom-0"
                                style="border-top-left-radius: 15px; border-top-right-radius: 15px; background-color: #333;">
                                   <?php //<i class="fas fa-angle-left text-white"></i> ?>
                                    <p class="mb-0 fw-bold">#AskObviousBaba: nothing here is not obvious</p>
                                    <a href="<?php echo TAOH_SITE_URL_ROOT.'/'.TAOH_CURR_APP_SLUG.'/askobviousbaba'; ?>"><i class="fas fa-times text-white"></i></a>
                                </div>
                                <?php
                                if ( taoh_user_is_logged_in() ) {
                                ?>
                                <div class="card-body">
                                    <div id ="obviousChat"></div>
                                    <div class="form-outline">
                                        <div class="d-flex flex-wrap align-items-center">
                                            <div class="d-flex flex-wrap align-items-center flex-grow-1">
                                                <div class="form-group mr-3 flex-grow-1">
                                                    <input class="form-control pl-40px ask" autocomplete="off" type="text" name="ask" placeholder="Type your message">
                                                    <span class="la la-search input-icon"></span>
                                                </div>
                                            </div><!-- end d-flex -->
                                            <div class="search-btn-box mb-3">
                                                <button class="btn theme-btn">Chat <i class="fa fa-spinner fa-spin load"></i> <i class="la la-search ml-1"></i></button>
                                            </div><!-- end search-btn-box -->
                                        </div>
                                        <input type="hidden" name="messages" class="messages" value="">                                            
                                    </div>
                                </div>
                                <?php
                                } else {
                                    ?>
                                    <div class="card-body">
                                        <div id ="obviousChat"></div>
                                        <div class="form-outline">
                                            <div class="d-flex flex-wrap align-items-center">
                                                <div class="d-flex flex-wrap align-items-center flex-grow-1">
                                                    <a href="<?php echo TAOH_LOGIN_URL."?redirect_url=".TAOH_REDIRECT_URL; ?>" class="btn btn-dark btn-lg active" style="width: 100%;" role="button" aria-pressed="true">Login / Signup To Chat With ObviousBaba, Your Success Guru!</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div><p class="text-justify text-white text-sm"><small>* We are currently testing and improving #ObviousBaba to give better answers. So, we welcome your feedback. Lastly, please do not share any sensitive information while using the chatbot. We value your privacy and confidentiality.</small></p></div>
                </div>
            </div>
        </div>
    </div>
</section><!-- end blog-area -->

<script type="text/javascript">
    let $ = jQuery;
    let obviousChat = $('#obviousChat');
    $('#alert_error').hide();

    $(".btn").click(function(){
        if($('.ask').val() == ''){
            $("#alert_error").show();
            setTimeout(function() { $("#alert_error").hide(); }, 3000);
            return false;
        }
        $(".load").show();
        taoh_chat_init();
    });

    $(document).ready(function(){
        taoh_chat_init();
    });

    function taoh_chat_init() {
        let ask = $('.ask').val();
		var data = {
			'taoh_action': 'chat_msg_get',
            'ask' : ask
		 };console.log(data);
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
			console.log(response)
            $('.load').hide();
			render_chat_template(response, obviousChat);
		}).fail(function() {
	    	console.log( "Network issue!" );
	  })
	}

    function render_chat_template(data, slot) {
        $(".load").hide();
		//slot.empty();
		if(data.length === 0) {
			slot.append("<p>No data found!</p>");
		} else {

			$.each(data, function(i, v){
                if(v.sender == "obviousbaba"){
                    slot.append(`
                        <div class="d-flex flex-row justify-content-start mb-4">
                            <img src="<?php echo TAOH_OBVIOUS_PREFIX.'/images/obviousbaba.png';  ?>"
                            alt="Obvious Baba" style="width: 50px; height: 100%;">
                            <div class="p-3 ms-3 text-black" style="border-radius: 15px; background-color: #FFC859;">
                                <p class="medium mb-0 text-justify">${v.message}</p>
                            </div>
                        </div>
				`);
                }else{
                    slot.append(`
                        <div class="d-flex flex-row justify-content-end mb-4">
                            <div class="p-3 me-3 border mr-2" style="border-radius: 15px; background-color: #fbfbfb;">
                                <p class="medium mb-0 text-justify">${v.message}</p>
                            </div>
                            <?php echo taoh_get_profile_image(); ?>
                        </div>
				`); 
                }
			})
          $('.ask').val('');  
		}

	}

</script>
<?php taoh_get_footer();  ?>