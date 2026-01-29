<?php
if ( ! taoh_user_is_logged_in() ) {
    taoh_redirect( '../' );
}

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
</style>
<section  style="background: #eee" class="blog-area pt-40px pb-80px">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <img src="<?php echo TAOH_CDN_PREFIX."/app/asqs/images/asq_thecoach_64.png"; ?>" height="60">
                <div>
                    <h3 class="text-white"></h3>
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-12 col-lg-12 col-xl-12">

                            <div class="card" id="chat1" style="border-radius: 15px; background-image: url('<?php echo TAOH_CDN_PREFIX."/app/asqs/images/asq_ai_sq3_512.png"; ?>'); background-color: #fff; background-position: center; background-repeat: no-repeat; ">
                                <div
                                class="card-header d-flex justify-content-between align-items-center p-3 text-white border-bottom-0"
                                style="border-top-left-radius: 15px; border-top-right-radius: 15px; background-color: #000;">
                                   <?php //<i class="fas fa-angle-left text-white"></i> ?>
                                    <p class="mb-0 fw-bold text-white">#AsqTheCoach: Community Powered Career Coach</p>
                                    <a href="./"><i class="fas fa-times text-white"></i></a>
                                </div>
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
                                                    <button class="btn theme-btn">Search <i class="fa fa-spinner fa-spin load"></i> <i class="la la-search ml-1"></i></button>
                                                </div><!-- end search-btn-box -->
                                            </div>


                                            <input type="hidden" name="messages" class="messages" value="">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section><!-- end blog-area -->
<?php taoh_get_footer();  ?>
<script type="text/javascript">
    let $ = jQuery;
    let obviousChat = $('#obviousChat');

    $(function () {
        $(document).ajaxStart(function () {
            $(".load").show();
        });

        $(document).ajaxStop(function () {
            $(".load").hide();
        });

        $(document).ajaxError(function () {
            $(".load").hide();
        });
    });

    $(".btn").click(function(){
        if($('.ask').val() == ''){
            alert('Message should not be empty!!!');
            return false;
        }
        taoh_chat_init();
    });

    $(document).ready(function(){
        taoh_chat_init();
    });

    function taoh_chat_init() {
        let ask = $('.ask').val();
		var data = {
			'taoh_action': 'chat_coach_get',
            'ask' : ask
		 };
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
			console.log(response)
            $('.load').hide();
			render_chat_template(response, obviousChat);
		}).fail(function() {
	    	console.log( "Network issue!" );
	  })
	}

    function render_chat_template(data, slot) {
		//slot.empty();
		if(data.length === 0) {
			slot.append("<p>No data found!</p>");
		} else {
			$.each(data, function(i, v){
                if(v.sender == "#JusAskTheCoach"){
                    slot.append(`
                        <div class="d-flex flex-row justify-content-start mb-4">
                            <img src="<?php echo TAOH_CDN_PREFIX."/app/asqs/images/asq_bubble_64.png"; ?>"
                            alt="#AsqTheCoach" style="width: 50px; height: 100%;">
                            <div class="p-3 ms-3 text-white bg-primary ml-2" style="border-radius: 15px;">
                                <p class="medium mb-0">${v.message}</p>
                            </div>
                        </div>
				`);
                }else{
                    slot.append(`
                        <div class="d-flex flex-row justify-content-end mb-4">
                            <div class="p-3 me-3 border mr-2" style="border-radius: 15px; background-color: #fbfbfb;">
                                <p class="medium mb-0">${v.message}</p>
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