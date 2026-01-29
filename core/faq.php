<?php

taoh_get_header();


$data = array();
$current_status = "<a itemprop=\"".TAOH_SITE_URL_ROOT."/settings\" style=\"font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #ffffff; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: block; border-radius: 5px; text-transform: capitalize; background-color: #32A250; margin: 0; border-color: #32A250; border-style: solid; border-width: 10px 20px;\"> Checkout Settings!</a>";

if( ! taoh_user_is_logged_in() ) {
  $current_status = "<a href=\"".TAOH_SITE_URL_ROOT."/login\" itemprop=\"url\" style=\"font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #eaf0f7; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: block; border-radius: 5px; text-transform: capitalize; background-color: #0a80ff; margin: 0; border-color: #0a80ff; border-style: solid; border-width: 10px 20px;\" target=\"_BLANK\">Login/Sign Up *</a><right>* Link opens in new tab</right>";
}
$current_app = TAOH_SITE_CURRENT_APP_SLUG;
$app_config = taoh_app_info($current_app);
$app_temp = @taoh_parse_url(0) ? taoh_parse_url(0):TAOH_PLUGIN_PATH_NAME;
$conttokenvar = @taoh_parse_url(3) ? taoh_parse_url(3) : '';
//echo $conttokenvar;
$tokenname = '';
$detail_name = $app_temp;
if($conttokenvar != ''){
    //@$conttoken = array_pop( explode( '-', $conttokenvar) );
    $detail_name .= '/'.$conttokenvar;
    /* if($app_name == 'events'){
      $detail_name = '/next/'.$conttokenvar;
    }else{
      $detail_name = '/d/'.$conttokenvar;
    } */
}


$app_data = taoh_app_info($current_app);
//$array_json =  taoh_url_get_content( TAOH_CDN_PREFIX."/app/$current_app/faq.php" );
$taoh_vals = array(
);
$taoh_call = "/app/$current_app/faq.php";
$taoh_call_type = "get";
/* $array_json = taoh_apicall_get( $taoh_call, $taoh_vals );
$array = json_decode($array_json);
print_r($array);
 */
//$taoh_vals = '';
$url = TAOH_CDN_PREFIX.'/app/jobs/faq.php';
//echo $url;
/* $array_json = taoh_get( $url, $taoh_vals );
$array = json_decode($array_json); */
//echo "<pre>";print_r($array);echo "</pre>";
//die;
$about_url = TAOH_SITE_URL_ROOT."/about";
$array1 = [];
if ( $current_app != TAOH_PLUGIN_PATH_NAME ) $about_url = TAOH_SITE_URL_ROOT."/".$current_app."/about";

?>
<section class="faq-area pt-30px pb-30px">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="pb-2">
                    <h5>Search our help library</h5>
                </div>
                <div class="form-group">

                    <input class="form-control pl-40px" type="text" name="searchSupport" id="searchSupport" placeholder='Ask your question here'>
                    <span class="la la-search input-icon"></span>
                    <button class="search-faq"><span class="la la-paper-plane"></span></button>

                </div>
            </div>
        </div>
       <style>
        .search-faq {
    position: absolute;
    top: 5px;
    right: 10px;
    border: 0;
    background: transparent;
}
        </style>

                <div class="row" id="listChatRooms"> </div>
		        <?php if ( taoh_user_is_logged_in() ){ ?>
                    <div class="sidebar support-message d-none" id="accordion">
                        <div class="card card-item">
                            <form action="<?php echo TAOH_ACTION_URL."/support"; ?>" method="post">
                                <div class="card-body">
                                    <div class="form-group">
                                            <!-- <h3 class="fs-17 pb-3 text-info">If you still need help contact us</h3> -->
                                            <div class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse-faq" aria-expanded="true" aria-controls="collapse-faq">
                                                <span>If you still need help, send us a message</span>
                                                <i class="la la-angle-up collapse-icon"></i>
                                            </div>
                                            <div class="divider"><span></span></div>

                                    </div><!-- end form-group -->
                                    <div id="collapse-faq" class="collapse" aria-labelledby="heading-faq" data-parent="#accordion">
                                        <!-- <div class="form-group">
                                                <label class="fs-14 text-black fw-medium lh-20">First Name<span class="text-gray fs-13"></span></label>
                                                <input type="text" class="form-control form--control fs-14" placeholder="e.g. First Name" name="first_name">
                                        </div>
                                        <div class="form-group">
                                                <label class="fs-14 text-black fw-medium lh-20">Last Name<span class="text-gray fs-13"></span></label>
                                                <input type="text" class="form-control form--control fs-14" placeholder="e.g. Last Name" name="last_name">
                                        </div>
                                        <div class="form-group">
                                                <label class="fs-14 text-black fw-medium lh-20">Email<span class="text-gray fs-13"></span></label>
                                                <input type="text" class="form-control form--control fs-14" placeholder="e.g. Email" name="email">
                                        </div> -->
                                        <div class="form-group">
                                                <label class="fs-14 text-black fw-medium lh-20">Message</label>
                                                <textarea class="form-control form--control fs-14" rows="6" placeholder="Please provide a detailed description of the feedback or issue. For the issue, share the website url, the error message and any other relevant detail. The more you share, the better we will be able to help you." name="we_message"></textarea>
                                        </div><!-- end form-group -->
                                        <div class="form-group mb-0">
                                                <button class="btn theme-btn mt-2" type="submit">Send Message <i class="la la-arrow-right icon ml-1"></i></button>
                                        </div><!-- end form-group -->
                                    </div>
                                </div><!-- end card-body -->
                            </form>
                        </div><!-- end card -->
                    </div><!-- end sidebar -->
                <?php } else { ?>
                    <div class="sidebar support-message d-none" id="accordion">
                        <div class="card card-item">
                            <div class="card-body">
                                <div class="form-group">
                                        <!-- <h3 class="fs-17 pb-3 text-info">If you still need help contact us</h3> -->
                                        <div class="btn btn-link"  aria-expanded="true" aria-controls="collapse-faq">
                                            <span>If you still need help, please provide us with your feedback using the following link:  <a href="https://one.tao.ai/taoreach" target="_blank">https://one.tao.ai/taoreach.</a></span>

                                        </div>
                                        <div class="divider"><span></span></div>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>


    </div><!-- end container -->
</section>


<script type="text/javascript">
	let $ = jQuery;
	//let loaderArea = $('#loaderArea');
	 let listChatRooms = $('#listChatRooms');
    /*let geohashInput = $('#geohash');
	let geohash = geohashInput.val(); */
	let search = "";
	/* let locationClear = $('#locationClear');
  let searchClear = $('#searchClear');
	let filterArea = $('#filterArea'); */

	let totalItems = 0; //this will be rewriiten on response of assks on line 307
	let itemsPerPage = 10;
	let currentPage = 0; //default on first load

	let term = "";
	//let roomSearch = $('#roomSearch');
  let currentMod = '<?php echo $app_config->slug; ?>';
  let faqresult = [];
  $(document).ready(function(){

        $('.ts-control').css('height', '37px');
        taoh_asks_init();
  });
function taoh_asks_init() {
        console.log('------init----')
        var search = '';
		/* if(search) {
			searchClear.show();
		} else {
			searchClear.hide();
		}
		if(geohash) {
			locationClear.show();
		} else {
			locationClear.hide();
		} */
       // console.log("<?php //echo $array_json; ?>")

      var data = {
           'taoh_action': 'get_support',
           'app': '<?php echo $app_temp; ?>',
           'contoken' : '<?php echo $conttokenvar;?>',
           'url':'<?php echo $detail_name; ?>'
         };
      jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
        console.log(response);
        //faqresult = response;
        $.each(response, function(j, data){
            $.each(data, function(i, v){
                if(i != ''){
                    let temp = [];
                    var slotstr = v.substring(0,100);
                    temp['key'] = i;
                    temp['value'] = v;
                    faqresult.push(temp);
                }
            });
        });
        render_asks_template(faqresult, listChatRooms);

      });

	}

	function render_asks_template(response, slot) {
        $('#listChatRooms').show();
       	slot.empty();
        if(response.length > 0){
            //$('.support-message').addClass('d-none');
            $.each(response, function(j, data){
                    var strlen = data.value.length;
                    //console.log(strlen)
                    var slotstr = data.value.substring(0,150);
                    var showtext =  '';
                    if(strlen > 150){
                        showtext =  `<span class="less-content-${j}">${slotstr}... <a class="show-more fs-12" data-id="${j}" style="cursor:pointer;">show more</a></span>
                                    <span class="more-content-${j} d-none">${data.value} <a class="show-less fs-12" data-id="${j}" style="cursor:pointer;">show less</a></span>`
                    }else{
                        showtext =  `<span class="less-content-${j}">${slotstr}</span>`;
                    }
                    slot.append(
                        `<div class="col-lg-6">
                            <div class="col-lg-12  media media-card media--card align-items-center mx-0">
                                <div class="media-body border-left-0">
                                    <h5 class="pb-1">${data.key}</h5>
                                    ${showtext}
                                    </div>
                            </div>
                        </div>`
                        );

            });
            $('.support-message').removeClass('d-none');
           // slot.append(`<div class="col-lg-12  media media-card media--card align-items-center">"Do you have other questions? <a class="btn btn-sm issue-not-found"> Yes </a>, or <a class="btn btn-sm issue-found"> No </a></div>`);

        }else{
            slot.append(`<div class="col-lg-6  media media-card media--card align-items-center">No result found</div>`);
            $('.support-message').removeClass('d-none');
        }
		//if(totalItems >= 11) { enable to hide pagination if no date below 10
			//show_pagination('#pagination')
		//}
	}

    $(document).on('click', '.show-more', function () {
        var key = $(this).attr('data-id');
        $('.more-content-'+key).removeClass('d-none');
        $('.less-content-'+key).addClass('d-none');

    });
    $(document).on('click', '.show-less', function () {
        var key = $(this).attr('data-id');
        $('.more-content-'+key).addClass('d-none');
        $('.less-content-'+key).removeClass('d-none');

    });


    $(document).on('click', '.issue-not-found', function () {
        $('.support-message').removeClass('d-none');
        $('#listChatRooms').hide();
    });

    var searchinput = document.getElementById("searchSupport");

    // Execute a function when the user presses a key on the keyboard
    searchinput.addEventListener("keypress", function(event) {
    // If the user presses the "Enter" key on the keyboard
    if (event.key === "Enter") {
        // Cancel the default action, if needed
        event.preventDefault();

        searchSupportResult();
       // document.getElementById("myBtn").click();
    }
    });
    $(document).on('click', '.search-faq', function () {
        searchSupportResult();
    });

    function searchSupportResult(){
        var searchstrtemp  = $('#searchSupport').val();
        searchstrtemp = searchstrtemp.replace('"','');
        searchstrtemp = searchstrtemp.replace(',','');

        var searchstr = searchstrtemp.toLowerCase();

       let result = faqresult.filter(function(value) {
            var temp =    value.key;
            temp = temp.replace('"','');
            temp = temp.replace(',','');

            var str = (temp).toLowerCase();
            if(str.indexOf(searchstr) > -1){
                return true;
            }else{
                var tempvalue = value.value;
                tempvalue = tempvalue.replace('"','');
                tempvalue = tempvalue.replace(',','');
               str = (tempvalue).toLowerCase();
               if(str.indexOf(searchstr) > -1){
                    return true;
                }
            }

        });


        //console.log(result);
        render_asks_template(result, listChatRooms);
        }

    function checkAdult(str) {
        var searchstr = $('#searchSupport').val();
        console.log(searchstr);
        return (str.indexOf(searchstr) !== 1)
    }

    </script>
    <?php taoh_get_footer();  ?>