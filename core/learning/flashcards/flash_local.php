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

$taoh_category_info = taoh_category_info($category, 'flash');
$all_catagories2 = taoh_get_categories( 'flash');//print_r($all_catagories);exit();
$all_catagories = taoh_category_bucket($all_catagories2, $taoh_category_info[ 'bucket' ]);
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

.cards {
    margin-top: 1rem;
    height: 474px;
    width: 300px;
    border-radius: 0.25rem;
    position: relative;
}

.cards .back .inner{
    font-family: 'Hobo BT', sans-serif;
    font-weight: bold;                                                
    position: absolute;
    top: 10%;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 44px;
}

.cards .front .inner{
    font-family: 'Hobo BT', sans-serif;
    font-weight: bold;
    position: absolute;
    top: 50%;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 44px 55px;
    line-height: 20px;
}

.cardFront,
.cardBack {
    box-sizing: border-box;
    border-radius: 0.25rem;
    transition: transform 0.5s ease;
    position: absolute;
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
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
    top: 93px;
    left: 90px;
}

.copy-section {
  position: relative;
}

.your-tooltip {
  opacity: 0;
  position: absolute;
  bottom: -35px;
  transition: all .3s;
}
 
.your-tooltip.show {
  opacity: 1;
}

/* Style for arrow buttons */
.arrow-buttons {
  /*position: absolute;*/
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transform: translateY(-203%);
  pointer-events: auto;
  margin-left: 2px;
}

/* Style for individual arrows */
.arrow-buttons i {
    font-size: 25px;
    cursor: pointer;
    background : none;
    padding: 5px 10px;
    border-radius: 50%;
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
        <div class="row justify-content-center arrow-buttons">
            <div onclick="taoh_flash_init();">
                <i class="fa fa-arrow-left " onclick="taoh_flash_init(); "  style="font-size:25px;cursor: pointer;"></i>
            </div>
            <div style="width: 198px;">
                <i id="flip-btn" class="fa fa-refresh" style="font-size:25px;"></i>
            </div>
            <div>
            <i class="fa fa-arrow-right" onclick="taoh_flash_init();" style="font-size:25px;cursor: pointer;"></i>
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
        console.error("Response does not contain 'output' key!", response);
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


