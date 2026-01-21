<?php
/*
Configure for Google Search Console
*/
// TAO_PAGE_AUTHOR
define('TAO_PAGE_AUTHOR', 'Flashcard');
// TAO_PAGE_DESCRIPTION
define('TAO_PAGE_DESCRIPTION', '');
// TAO_PAGE_IMAGE
define('TAO_PAGE_IMAGE','');
// TAO_PAGE_TITLE
define('TAO_PAGE_TITLE', 'Flashcard');
// TAO_PAGE_TWITTER_SITE
// TAO_PAGE_ROBOT
define('TAO_PAGE_ROBOT', 'index, follow');
// TAO_PAGE_CANONICAL
taoh_get_header();

$category = taoh_parse_url(2);
$conttoken = taoh_parse_url(3);

?>
<style>
  /* Important part */
.modal-dialog{
    overflow-y: initial !important
}
.modal-body{
    height: 65vh;
    overflow-y: auto;
}
.toped{
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.load {
  border: 4px solid #f3f3f3;
  border-radius: 50%;
  border-top: 4px solid #3498db;
  width: 50px;
  height: 50px;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;
  z-index: 1000;
  position: absolute;
  top: 50%;
  left: 49%;
}

/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.owl-dots{
  display:none;
}

#header {
    height:85px;    
}
#footer {
    position:absolute;
    bottom:0;
    height:55px;
    font-size:15px;
}
#container{
  height: 395px;
}
.fs-8{
  font-size:10px;
}

</style>
<!-- <section style="background: #333; background-repeat: no-repeat; background-size: cover; background-image: url('<?php echo ( isset($category) && $category )? TAOH_SITE_URL_ROOT."/assets/images/flashcard/$category.png":TAOH_SITE_URL_ROOT."/assets/images/flashcard/flashcard.png"; ?>');" class="blog-area pt-40px pb-80px"> -->
<section class="blog-area pt-40px pb-80px">
  <div class="container text-center" style="position: relative;">
  <?php echo "<img width=\"350px\" src=\"".TAOH_SITE_URL_ROOT."/assets/images/flashcard/".( ( isset( $category ) && $category ) ? $category:"flashcard" ).".png\" alt=\"$category\">"; ?>
      <div class="row toped ml-2" style="width:300px;height:390px;">
        <div class="col-lg-12">
          <div class="about-content m-0">
            <div id="owl-demo" class="owl-carousel owl-theme">
              <div class="item">
                <div id="listChatRooms" style="margin: 0% 10% 0% 4%;"></div>
              </div>
            </div> 
          </div>
        </div>
      </div>
 </div>
 <div class="text-center p-0 mt-4 mb-4 rounded-0 bg-transparent">
  <div class="dropdown dropright">
    <a class="btn btn-primary dropdown-toggle" data-toggle="modal" data-target="#exampleModal" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      More Flashcards
    </a>
  </div>
</div>
</section>
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Categories</h5>
      </div>
      <div class="modal-body">
      <?php
        foreach (taoh_get_categories('flash') as $category_elem) {
          echo "<a class=\"dropdown-item\" href=\"".TAOH_SITE_URL_ROOT."/learning/flashcard/".$category_elem['slug']."\">".$category_elem['title']." (".$category_elem['count'].")</a>";
        }
      ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<?php if($conttoken) { ?>
	<script type="text/javascript">
		
	</script>
<?php } ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script>
  let $ = jQuery;
  let listChatRooms = $('#listChatRooms');

  $(document).ready(function(){
    taoh_flash_init();
    taoh_flash_next();
	})

  function taoh_flash_init() {
		var data = {
			 'taoh_action': 'flashcard_get',
       'category' : '<?php echo $category; ?>'
		 };
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
			//console.log(response)
			render_chat_template(response, listChatRooms);
		}).fail(function() {
	    	console.log( "Network issue!" );
	  })
	}

  function taoh_flash_next() {
		var data = {
			 'taoh_action': 'flashcard_get',
       'category' : '<?php echo $category; ?>'
		 };
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(data) {
        let name = data.output.author.fname+' '+data.output.author.lname;
        let title = data.output.title;
        let fl_title = title.replace('\\','',title);
        let desc = data.output.description;
        let descrpt = desc.replace('\\','',desc);
        descrpt = decodeURI(extractContent(descrpt)).replace('\\\'','\'');
        let cat = data.output.category[0];
        let conttoken = data.output.conttoken;
        let homeurl = "<?php echo TAOH_SITE_URL_ROOT ?>";
        let string = data.output.category[0];
        string = string.replace(/-/g, " ");
        string = string.replace(/\b\w/g, x => x.toUpperCase());
			$('.owl-carousel').owlCarousel('add', `<div style="margin: 0% 10% 0% 2%;"><div id="container"><div id="header"><h5 class="text-center">${fl_title.substring(0,100)}</h5></div>
              <div class="text-center p-0 mb-0 mt-2" id="body" style="font-size: 12px; line-height: 1.5;" >
               
                <div class="fs-12 font-black" style="text-align: justify; margin-top: -20px;">${descrpt}</div>
              </div>
              <?php              
/*
              <div class="p-0 mb-0 rounded-0 mt-2 bg-transparent text-center ml-4" id="footer">
                  <p class="fs-8 fw-medium ml-3">${data.output.profile_pretag} ${name.replace('\\','',name)}</p>
                  <p class="fs-8 fw-medium ml-3">Source: ${data.output.source}</p>
              </div>
*/
?>

              </div></div>`).owlCarousel('update');
      let url = homeurl+'/learning/flashcard/'+cat+'/';
		  window.history.pushState("","", url);
		}).fail(function() {
	    	console.log( "Network issue!" );
	  })
	}

  function render_chat_template(data, slot) {
		slot.empty();
		if(data.length === 0) {
			slot.append("<p>No data found!</p>");
		} else {
        let name = data.output.author.fname+' '+data.output.author.lname;
        let title = data.output.title;
        let fl_title = title.replace('\\','',title);
        let desc = data.output.description;
        let descrpt = desc.replace('\\','',desc);
        let strdescrpt = descrpt.split("");
        let ddescrpt = strdescrpt.join("").replace('\\\'','\'');
        let cat = data.output.category[0];
        let conttoken = data.output.conttoken;
        let homeurl = "<?php echo TAOH_SITE_URL_ROOT ?>";
        let string = data.output.category[0];
        string = string.replace(/-/g, " ");
        string = string.replace(/\b\w/g, x => x.toUpperCase());
				slot.append(`<div id="container"><div id="header">
              <h5 class="text-center">${fl_title.substring(0,100)}</h5></div>
              <div class="text-center p-0 mb-0 mt-2" id="body" style="font-size: 12px; line-height: 1.5;" >
                <div class="fs-12 font-black" style="text-align: justify; margin-top: -20px;">${extractContent(ddescrpt)}</div>
              </div>
<?php              
/*
<center><b>${string}</b></center><br />
              <div class="p-0 mb-0 rounded-0 mt-10 bg-transparent text-center ml-3" id="footer">
                  <p class="fs-8 fw-medium ml-3">${data.output.profile_pretag} ${name.replace('\\','',name)}</p>
                  <p class="fs-8 fw-medium ml-3">Source: ${data.output.source}</p>
              </div>
*/
?>
              </div>
				`);
      $('.hidecat').val(cat);
      let url = homeurl+'/learning/flashcard/'+cat;
		  window.history.pushState("","", url);
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
      taoh_flash_next();
      taoh_flash_next();
    })

    });

  function extractContent(s) {
    var span = document.createElement('span');
    span.innerHTML = s;
    return span.textContent || span.innerText;
  }
</script>
<?php taoh_get_footer(); ?>