<?php

/*
Configure for Google Search Console
*/
// TAO_PAGE_AUTHOR
define('TAO_PAGE_AUTHOR', 'ObviousBaba');
// TAO_PAGE_DESCRIPTION
define('TAO_PAGE_DESCRIPTION', '');
// TAO_PAGE_IMAGE
define('TAO_PAGE_IMAGE','https://obviousbaba.com/images/obviousbaba.png');
// TAO_PAGE_TITLE
define('TAO_PAGE_TITLE', 'ObviousBaba');
// TAO_PAGE_TWITTER_SITE
// TAO_PAGE_ROBOT
define('TAO_PAGE_ROBOT', 'index, follow');
// TAO_PAGE_CANONICAL

taoh_get_header();
?>
<style>

.toped{
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.owl-dots{
  display:none;
}

</style>
<section style="background: #333" class="blog-area pt-40px pb-80px">
  <div class="container text-center" style="position: relative;">
    <img width="360px" src="<?php echo TAOH_OPS_PREFIX."/images/cards/ob1.png"; ?>" alt="avatar">
      <div class="row align-items-center pb-30px toped ml-1" style="width:300px;">
        <div class="col-lg-12" style="height:390px;">
            <img src="<?php echo TAOH_OPS_PREFIX."/images/obviousbaba_logo.png"; ?>" style="width: 155px;" class="mt-1">
            <img src="<?php echo TAOH_OPS_PREFIX."/images/obviousbaba.png"; ?>" style="width: 175px;height:155px;" class="">
								  <h6 class="text-white mr-1">nothing here is not obvious</h6>
                <div id="owl-demo" class="owl-carousel owl-theme">
                    <div id="obviousQoute"></div>
                </div>
        </div>
      </div>
  </div>
  <div class="text-center p-0 mt-4 mb-4 rounded-0 bg-transparent">
    <a class="btn btn-primary text-center" href="<?php echo TAOH_SITE_URL_ROOT."/learning/askobviousbaba"; ?>">Chat with Obvious Baba!</a>
  </div>
</section>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script type="text/javascript">
  let obviousQoute = $('#obviousQoute');

  $(document).ready(function() {
    getQoute();
    //get_nxt_qoute();
  })

  function getQoute() {
    var data = {
       'taoh_action': 'taoh_get_qoute',
     };
     jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
      render_chat_template(response, obviousQoute);
    }).fail(function() {
      obviousQoute.html("Oops! it seems Obvious Baba is unreachable.");
      alert( "Network issue!" );
    })
  }

  function get_nxt_qoute() {
    var data = {
       'taoh_action': 'taoh_get_qoute',
     };
     jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
      $('.owl-carousel').owlCarousel('add', `
      <p>${response.category}</p>
      <h4 class="m-2" style="color:${response.color}">${response.quote}</h4>`).owlCarousel('update');
    }).fail(function() {
      obviousQoute.html("Oops! it seems Obvious Baba is unreachable.");
      alert( "Network issue!" );
    })
  }

  function render_chat_template(data, slot) {
		if(data.length === 0) {
			slot.append("<p>No data found!</p>");
		} else {
        console.log("accounts", data.category)
				slot.append(`
                <div class="item">
                  <p>${data.category}</p>
								  <h4 class="m-2" style="color:${data.color}">${data.quote}</h4>
                </div>
				`);
		}
	}

  var owl = $('.owl-carousel');

  $(document).ready(function() {

    $("#owl-demo").owlCarousel({

      navigation : true, // Show next and prev buttons

      slideSpeed : 300,
      paginationSpeed : 400,

      items : 1,
      itemsDesktop : false,
      itemsDesktopSmall : false,
      itemsTablet: false,
      itemsMobile : false

    });

    // Listen to owl events:
    owl.on('changed.owl.carousel', function(event) {
      get_nxt_qoute();
      get_nxt_qoute();
    })

  });

</script>
<?php taoh_get_footer();  ?>