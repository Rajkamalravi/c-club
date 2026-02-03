<?php
/*
Configure for Google Search Console
*/
// TAO_PAGE_AUTHOR
define('TAO_PAGE_AUTHOR', TAOH_SITE_NAME_SLUG);
// TAO_PAGE_DESCRIPTION
define('TAO_PAGE_DESCRIPTION', 'Interactive flashcards for career development, interview prep, networking, and professional growth at ' . TAOH_SITE_NAME_SLUG . '.');
// TAO_PAGE_IMAGE
define('TAO_PAGE_IMAGE','');
// TAO_PAGE_TITLE
define('TAO_PAGE_TITLE', 'Flashcards - ' . TAOH_SITE_NAME_SLUG);
// TAO_PAGE_TWITTER_SITE
// TAO_PAGE_ROBOT
define('TAO_PAGE_ROBOT', 'index, follow');
define('TAO_PAGE_KEYWORDS', 'flashcards, interview prep, career development, professional growth, ' . TAOH_SITE_NAME_SLUG);
// TAO_PAGE_CANONICAL

// JSON-LD WebPage structured data for SEO/AEO
$jsonld_flash = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => TAO_PAGE_TITLE,
    'description' => TAO_PAGE_DESCRIPTION,
    'url' => TAOH_SITE_URL_ROOT . '/learning/flashcard/',
);
$additive_flash = '<script type="application/ld+json">' . json_encode($jsonld_flash, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
taoh_get_header($additive_flash);

$category = taoh_parse_url(2);
$conttoken = taoh_parse_url(3);

$taoh_category_info = taoh_category_info($category, 'flash');
$all_catagories2 = taoh_get_categories( 'flash');//print_r($all_catagories);exit();
$all_catagories = $all_catagories2;
$taoh_user_vars = taoh_user_all_info();
//print_r($all_catagories);exit();
?>
<style>
  /* Important part */
.modal-dialog{
    overflow-y: initial !important
}
.modal-height .modal-body{
    height: 65vh;
    overflow-y: auto;
}
.toped{
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.spinner-border {
    position: absolute;
    top: 44%;
    right: 46%;
}

#loaderArea {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    background: rgba(255,255,255,0.7);
    border-radius: 0.75rem;
}

#loaderArea:empty {
    display: none;
}

#loaderArea img {
    width: 40px !important;
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
.fs-8{
  font-size:10px;
}


button {
  border: solid 2px;
  padding: .5rem;
  border-radius: 0.25rem;
  font-weight: 700;
}

/* Card container - responsive */
.cards {
    margin-top: 1rem;
    width: 300px;
    height: 474px;
    border-radius: 0.75rem;
    position: relative;
    filter: drop-shadow(0 4px 12px rgba(0,0,0,0.10));
}

/* Force all card images to fill container width */
.cards img {
    width: 100% !important;
    height: auto;
}

.cards .back .inner{
    font-family: var(--theme-font-family, "Ubuntu", sans-serif);
    font-weight: bold;
    position: absolute;
    top: 10%;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 44px;
    line-height: 1.4;
}

.cards .front .inner{
    font-family: var(--theme-font-family, "Ubuntu", sans-serif);
    font-weight: bold;
    position: absolute;
    top: 50%;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 44px 55px;
    line-height: 24px;
}

.cardFront,
.cardBack {
    box-sizing: border-box;
    width: 100%;
    border-radius: 0.75rem;
    transition: transform 0.5s ease;
    position: absolute;
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
    overflow: hidden;
}
.cardBack {
  transform: perspective(1000px) rotateY(180deg);
}

.cardBack.flipped {
  transform: perspective(1000px) rotateY(0deg);
}

.cardFront {
  transform: perspective(1000px) rotateY(0deg);
}

.cardFront.flipped {
  transform: perspective(1000px) rotateY(-180deg);
}

.cards .sq-image{
    position: absolute;
    top: 20%;
    left: 50%;
    transform: translateX(-50%);
    width: 130px !important;
}

.copy-section {
  position: relative;
}

.your-tooltip {
  opacity: 0;
  position: absolute;
  bottom: -35px;
  transition: all .3s;
  font-size: 13px;
  color: #28a745;
  font-weight: 600;
}

.your-tooltip.show {
  opacity: 1;
}

/* Arrow buttons - use normal flow instead of transform hack */
.arrow-buttons {
  max-width: 320px;
  margin: -20px auto 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  pointer-events: auto;
  position: relative;
  z-index: 2;
}

/* Style for individual arrows */
.arrow-buttons > div {
    padding: 6px 12px;
    border-radius: 50%;
    transition: background 0.2s;
    cursor: pointer;
}
.arrow-buttons > div:hover {
    background: rgba(0,0,0,0.06);
}
.arrow-buttons i {
    font-size: 25px;
    cursor: pointer;
    background: none;
    padding: 5px 10px;
    border-radius: 50%;
}
#flip-btn {
    transition: transform 0.3s;
}
#flip-btn:hover {
    transform: rotate(90deg);
}

/* Category modal */
.modal-content {
    border-radius: 0.75rem;
}
.modal-body .dropdown-item {
    padding: 10px 20px;
    border-radius: 6px;
    margin-bottom: 2px;
    font-weight: 500;
    transition: background 0.15s;
}
.modal-body .dropdown-item:hover {
    background: rgba(0,0,0,0.04);
}

/* ===== Mobile (up to 480px) ===== */
@media (max-width: 480px) {
  .cards {
    width: 260px;
    height: 410px;
  }
  .cards .sq-image {
    width: 100px !important;
    top: 18%;
  }
  .cards .back .inner {
    padding: 30px 28px;
    font-size: 15px;
  }
  .cards .front .inner {
    padding: 30px 36px;
    font-size: 18px;
    line-height: 22px;
    top: 48%;
  }
  .arrow-buttons {
    max-width: 280px;
  }
  .arrow-buttons i {
    font-size: 22px;
  }
  .blog-area.pt-40px {
    padding-top: 20px !important;
  }
  .blog-area.pb-80px {
    padding-bottom: 40px !important;
  }
}

/* ===== Small mobile (up to 360px) ===== */
@media (max-width: 360px) {
  .cards {
    width: 240px;
    height: 379px;
  }
  .cards .sq-image {
    width: 85px !important;
  }
  .cards .back .inner {
    padding: 24px 22px;
    font-size: 14px;
  }
  .cards .front .inner {
    padding: 24px 30px;
    font-size: 16px;
    line-height: 20px;
  }
  .arrow-buttons {
    max-width: 260px;
  }
}

/* ===== Tablet and up (768px+) ===== */
@media (min-width: 768px) {
  .cards {
    width: 340px;
    height: 537px;
  }
  .cards .sq-image {
    width: 145px !important;
    top: 19%;
  }
  .cards .back .inner {
    padding: 50px;
  }
  .cards .front .inner {
    padding: 50px 60px;
  }
  .arrow-buttons {
    max-width: 360px;
  }
}

</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="https://fonts.cdnfonts.com/css/hobo-bt" rel="stylesheet">
<section class="blog-area pt-40px pb-80px">
    <div class="container text-center">
        <div class="d-flex justify-content-center mt-4">
            <div class="cards">
            <div id='loaderArea'></div>
                <div id="back" class="cardBack back"><?php echo "<img width=\"310px\" src=\"".TAOH_SITE_URL_ROOT."/assets/images/flashcard/".( ( isset( $category ) && $category ) ? $category:"flashcard" ).".png\" alt=\"$category\">"; ?>
                    <div id="listquote"></div>
                </div>
                <div id="front" class="cardFront front"><?php echo "<img class=\"sq-image\" width=\"130px\" src=\"".TAOH_SITE_URL_ROOT."/assets/images/flashcard/sq/".( ( isset( $category ) && $category ) ? $category:"flashcard" ).".png\" alt=\"$category\">"; ?><?php echo "<img width=\"310px\" src=\"".TAOH_SITE_URL_ROOT."/assets/images/flashcard/card/".( ( isset( $category ) && $category ) ? $category:"flashcard" ).".png\" alt=\"$category\">"; ?>
                    <div id="listtitle"></div>
                </div>
            </div>
        </div>
        <div class="arrow-buttons">
            <div onclick="resetAndFetch();">
                <i class="fa fa-arrow-left"></i>
            </div>
            <div>
                <i id="flip-btn" class="fa fa-refresh"></i>
            </div>
            <div onclick="resetAndFetch();">
                <i class="fa fa-arrow-right"></i>
            </div>
        </div>
        <div class="row justify-content-center p-0 mt-4 mb-4 rounded-0 bg-transparent">
          <div class="dropdown dropright">
              <a class="btn btn-primary dropdown-toggle" data-toggle="modal" data-target="#exampleModal" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              More Flashcards
              </a>
          </div>
        </div>
        <div class="row justify-content-center p-0 mt-4 mb-4 rounded-0 bg-transparent copy-section">
          <div class="your-tooltip">Copied!</div>
          <div class="d-flex">
              <input type="hidden" class="form-control copy_text" id="copy-text" />
              <button type="button" class="btn btn-primary" onclick="copyText()"><i class="la la-copy mr-1"></i> Copy URL</button>
          </div>
        </div>
        <div class="row justify-content-center p-0 mt-4 mb-4 rounded-0 bg-transparent">
          <?php if(taoh_user_is_logged_in() ) { ?>
              <div id="flashbutton"></div>
          <?php } ?>
        </div>
    </div>
    
</section>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content modal-height">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Categories</h5>
      </div>
      <div class="modal-body">
      <?php
        foreach ($all_catagories as $category_elem) {
          echo "<a style=\"color:".$category_elem['color']."\" class=\"dropdown-item\" href=\"".TAOH_SITE_URL_ROOT."/learning/flashcard/".$category_elem['slug']."/"."\">".$category_elem['title']." (".$category_elem['count'].")</a>";
        }
      ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="deleteAlert" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
        <button type="button" style="padding:0" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" class="del_cont">
        Are you sure, Do you want to delete?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="deleteConfirm()">Yes, I want to delete</button>
      </div>
    </div>
  </div>
</div>

<script>
  let $ = jQuery;
  let listtitle = $('#listtitle');
  let listquote = $('#listquote');
  let flashbutton = $('#flashbutton');
  let ptoken = '<?php echo $taoh_user_vars->ptoken ?? '' ?>';
  let loaderArea = $('#loaderArea');

  /* jQuery.ajaxSetup({
    beforeSend: function() {
        $('.spinner-border').show();
    },
    complete: function(){
        $('.spinner-border').hide();
    },
    success: function() {}
  }); */

  $(document).ready(function(){
    var conttoken = '<?php echo $conttoken; ?>';
    if(conttoken != ''){
      taoh_cont_init();
    }else{
      taoh_flash_init();
    }
  });

  function taoh_flash_init() {
    loader(true, loaderArea);
    var data = {
      'taoh_action': 'flashcard_get',
      'ops': 'random',
      'type': 'flash',
      'category' : '<?php echo $category; ?>'
     };
    jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
    if (!response || typeof response !== "object") {
        console.error("Invalid API response format!", response);
        return;
    }

    if (!response.output) {
        loader(false, loaderArea);
        render_no_flashcards(listtitle, listquote);
        return;
    }

    // If response.output is an object instead of an array, convert it
    let flashcard;
    if (Array.isArray(response.output)) {
        if (response.output.length === 0) {
            console.error("Output array is empty!");
            return;
        }
        flashcard = response.output[0]; // Get first item
    } else if (typeof response.output === "object") {
        flashcard = response.output; // Directly assign if it's already an object
    } else {
        console.error("Unexpected 'output' type:", typeof response.output);
        return;
    }

    console.log("Flashcard:", flashcard);
    render_title_template(flashcard.title, listtitle);
    render_quote_template(flashcard.blurb.description, listquote);
    render_url(flashcard.conttoken);
    render_button(flashcard.conttoken, flashcard.ptoken, flashbutton);
    save_metrics('flashcard', 'view', flashcard.conttoken);
    $('.del_cont').val(flashcard.conttoken);
    }).fail(function() {
        console.log( "Network issue!" );
    })
  }

  function taoh_cont_init() {
    loader(true, loaderArea);
    var data = {
      'taoh_action': 'flashcard_get',
      'category' : '<?php echo $category; ?>',
      'ops' : 'random',
      'type' : 'flash',
      'conttoken' : '<?php echo $conttoken; ?>'
     };
    jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
      console.log(response);
      if (!response || !response.output) {
        loader(false, loaderArea);
        render_no_flashcards(listtitle, listquote);
        return;
      }
      render_title_template(response['output'][0]['title'], listtitle);
      render_quote_template(response['output'][0]['blurb']['description'], listquote);
      render_button(response['output'][0]['conttoken'],response['output'][0]['ptoken'],flashbutton);

      save_metrics('flashcard','view',response['output'][0]['conttoken']);
      $('.del_cont').val(response['output'][0]['conttoken']);
    }).fail(function() {
      loader(false, loaderArea);
        console.log( "Network issue!" );
    })
  }

  

  function render_title_template(data, slot) {
    loader(false, loaderArea);
      slot.empty();
      if(data.length === 0) {
          slot.append("<p>No data found!</p>");
      } else {
          let title = data;
          slot.append(`<div class="inner" style="color: <?php echo $taoh_category_info[ 'text' ]; ?>; font-size: 25px; line-height: 25px;">${title}</div>`);
      }
  }
  function render_quote_template(data, slot) {
    loader(false, loaderArea);
    slot.empty();
    if(data.length === 0) {
      slot.append("<p>No data found!</p>");
    } else {
            let desc = data;
            slot.append(`<div class="inner" style="color: <?php echo $taoh_category_info[ 'text' ]; ?>; font-size: 20px; line-height: 1.3;">${extractContent(desc)}</div>`);
    }
  }
  function render_no_flashcards(titleSlot, quoteSlot) {
    titleSlot.empty();
    quoteSlot.empty();
    titleSlot.append(`<div class="inner" style="color: <?php echo $taoh_category_info[ 'text' ] ?? '#333'; ?>; font-size: 20px; line-height: 25px;">No flashcards available for this category.</div>`);
    flashbutton.empty();
  }

  function render_url(data){
    console.log(data);
    var url = window.location.href;
    var parts = url.split("/");
    var last_part = parts[parts.length-1];
    if(last_part == ''){
      window.history.pushState(null, "null", url+data);
      $('.copy_text').val(url+data);
    }else{
      url = url.replace(/\/[^\/]*$/, '/'+data);
      window.history.pushState(null, "null", url);
      $('.copy_text').val(url);
    }
    //window.history.pushState(null, "null", url+'/'+data);
  }

  function copyText() { 
      var yourToolTip = document.querySelector('.your-tooltip'); 
      /* Select text area by id*/
      var Text = document.getElementById("copy-text");
      /* Select the text inside text area. */
      Text.select();
      /* Copy selected text into clipboard */
      var copy_text = navigator.clipboard.writeText(Text.value);
      if(copy_text){    
        $('.your-tooltip').addClass('show');  
        setTimeout(function() {
            $('.your-tooltip').removeClass('show');
        }, 2000)
      }
  }

  function extractContent(s) {
    var span = document.createElement('span');
    span.innerHTML = s;
    return span.textContent || span.innerText;
  }

  const front = document.getElementById('front')
  const back = document.getElementById('back')
  const btn = document.getElementById('flip-btn')

  function handleFlip() {
  front.classList.toggle('flipped')
  back.classList.toggle('flipped')
  }

  btn.addEventListener('click', handleFlip)

  function resetAndFetch() {
    front.classList.remove('flipped');
    back.classList.remove('flipped');
    taoh_flash_init();
  }

  function render_button(data,flashptoken,slot) {
    slot.empty();
    if(ptoken == flashptoken){
    slot.append(
          `<div class="card-body">
            <div class="d-flex">
              <div class="ml-2">
                <a href="<?php echo TAOH_SITE_URL_ROOT."/learning/flashcard/edit/"?>${data}" target="_blank" class="btn btn-outline-primary" style="border-radius: 15px;">Edit Flash</a>
              </div>
              <div class="ml-5">
                <a class="btn btn-outline-danger" data-toggle="modal" data-target="#deleteAlert" style="border-radius: 15px;">Delete Flash</a>
              </div>
            </div>
          </div>`
      );
    }
    
  }

  function flashDelete() {
    $('#deleteAlert').modal('show');
  }

  function deleteConfirm(){
    let del_cont = $('.del_cont').val();
    var data = {
        'action': 'flash_delete',
        'ops': 'delete',
        'conttoken': del_cont
    };
    jQuery.post("<?php echo TAOH_ACTION_URL .'/flashcard'; ?>", data, function(response) {
      console.log(response);
      if(response.success){
        $('#deleteAlert').modal('hide');
        location.href = '<?php echo TAOH_FLASHCARD_URL.'/'.$category.'/'; ?>';
        
      }
      else{
        $('#deleteAlert').modal('hide');
      }
    }).fail(function() {
        console.log( "Network issue!" );
    })
}
</script>
<?php taoh_get_footer(); ?>
