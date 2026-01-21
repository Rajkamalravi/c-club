<?php
taoh_get_header();
//https://preapi.tao.ai/core.content.get?mod=core&ops=random&type=flashcard&token=y2Ds3ugv&category=mindfulness
$query = array("mod"=>"core", "type"=>"flashcard");
$query['token'] = taoh_get_dummy_token(1);
$query['ops'] = 'random';
//$query[ 'cfcache' ] = hash('sha256', 'core.content.get' . serialize($query));
$category = taoh_parse_url(2);
$conttoken = taoh_parse_url(3);

if ($conttoken){
	$query['conttoken'] = urlencode($conttoken);
	$query['ops'] = 'detail';
}

if ($category){
	$query['category'] = urlencode($category);
}

$params = http_build_query($query);
$api = TAOH_API_PREFIX."/core.content.get?".$params;
//echo $api;
$req = taoh_url_get_content($api);
//$req = str_ireplace('\\\\', '\\', $req);
$res = json_decode($req, true);
//print_r($res);


if($res && $res['success']) {
  $title = str_ireplace('\\', '', $res['output']['title']);
	$conttoken = $res['output']['conttoken'];
  $description = strip_tags(html_entity_decode($res['output']['description']));
  $description = str_ireplace('\\', '', $description);
  $image = $res['output']['image'];
  $avatar = $res['output']['author']['avatar'];
	$cat = $res['output']['category'][0];
}
$author = str_ireplace('\\', '', $res['output']['author']['fname']." ".$res['output']['author']['lname']);
//  $author = $res['output']['author']['fname']." ".$res['output']['author']['lname'];

if ( !isset($avatar) || ! $avatar ) $avatar = TAOH_OPS_PREFIX.'/avatar/PNG/128/avatar_074.png';
//print_r($res['output']);taoh_exit();
function category_active($slug) {
  $category = taoh_parse_url(1);
  if($slug == $category ) {
    return 'badge-warning';
  }
  return 'badge-primary';
}

//$api = TAOH_OPS_PREFIX.'/recommend/?type=background';
//$backdrop = json_decode(taoh_url_get_content ($api), true);
//$backdrop[ 'text' ] = 'white';
//if ( $backdrop[ 'type' ] == 'light' ) $backdrop[ 'text' ] = 'black';

$api = TAOH_OPS_PREFIX.'/recommend/?type=color';
$backdrop = json_decode(taoh_url_get_content ($api), true);
?>
<section class="hero-area pt-40px pb-30px bg-white shadow-sm overflow-hidden">
  <span class="stroke-shape stroke-shape-1"></span>
  <span class="stroke-shape stroke-shape-2"></span>
  <span class="stroke-shape stroke-shape-3"></span>
  <span class="stroke-shape stroke-shape-4"></span>
  <span class="stroke-shape stroke-shape-5"></span>
  <span class="stroke-shape stroke-shape-6"></span>
	<div class="container">
      <div class="hero-content d-flex flex-wrap align-items-center justify-content-between">

        <div class="pt-5 pb-3 col-lg-7">
          <h1 class="section-title fs-24 mb-1">Flashcard</h1>
          <p class="section-desc pb-4">Learning in a flash</p>
        </div>
      </div>
  </div>
</section>

<section class="blog-area pt-80px pb-80px">
    <div class="container">
        <div class="row">
        <div class="col-lg-2">
						<div class="sidebar pb-45px position-sticky top-0 mt-2">
							<ul class="generic-list-item generic-list-item-highlight fs-15">
								<li class="lh-26 <?php echo ( strlen( array_pop( explode( TAOH_PLUGIN_PATH_NAME, $_SERVER[ 'REQUEST_URI' ] ) ) ) <= 2 ) ? "active":""; ?>"><a href="<?php echo TAOH_SITE_URL_ROOT; ?>"><i class="la la-home mr-1 text-black"></i>Home</a></li>
								<?php
								foreach (taoh_available_apps() as $app) {
								echo "<li class=\"lh-26 ".(( stristr( $_SERVER[ 'REQUEST_URI' ].'/'.$app ) ) ? "active":"")."\"><a class=\"nav-link\" id=\"".$app."-tab\" href=\"".TAOH_SITE_URL_ROOT."/".$app."\" role=\"tab\" aria-controls=\"".$app."\" aria-selected=\"false\">".ucfirst($app)."</a></li>";
								}
								?>
								<li class="lh-26 <?php echo ( strlen( array_pop( explode( TAOH_PLUGIN_PATH_NAME."/learning", $_SERVER[ 'REQUEST_URI' ] ) ) ) <= 2 ) ? "active":""; ?>"><a href="<?php echo TAOH_SITE_URL_ROOT."/learning"; ?>"><i class="la la-book-open mr-1 text-black"></i>Growth Reads</a></li>
								<li class="lh-26 <?php echo (( stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/learning/flashcard' ) ) ? "active":""); ?>"><a href="<?php echo TAOH_SITE_URL_ROOT."/learning/flashcard"; ?>"><i class="la la-address-card mr-1 text-black"></i>Flashcards</a></li>
								<li class="lh-26 <?php echo (( stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/learning/tips' ) ) ? "active":""); ?>"><a href="<?php echo TAOH_SITE_URL_ROOT."/learning/tips"; ?>"><i class="la la-lightbulb mr-1 text-black"></i>Tips & Tricks</a></li>
								<li class="lh-26 <?php echo (( stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/learning/obviousbaba' ) ) ? "active":""); ?>"><a href="<?php echo TAOH_SITE_URL_ROOT; ?>/learning/obviousbaba"><i class="la la-home mr-1 text-black"></i>Obvious Baba</a></li>
							</ul>
						</div><!-- end sidebar -->
					</div><!-- end col-lg-2 -->
          <?php
          // echo 'style="background: '.$backdrop[0].';'";
          ?>

          <div class="col-lg-6 align-items-top">
            <div class="about-content py-5">
            <center>
              <svg  style="fill: <?php echo $backdrop[1]; ?>" aria-hidden="true" width="96" height="96" viewBox="0 0 96 96"><g color="<?php echo $backdrop[1]; ?>"><path d="M78.2 14.36a1.73 1.73 0 011.27-1.85 37.5 37.5 0 017.66-1.5c1.09-.09 1.98.8 1.9 1.89-.21 2.6-.79 5.19-1.56 7.8a1.71 1.71 0 01-1.66 1.28c-4.27-.16-7.08-3.56-7.62-7.62zM34.55 77.77l3.55-2.84-10-11-3.36 2.69c-.89.7-1 2.02-.23 2.86l7.34 8.08c.7.77 1.88.86 2.7.21zM70.1 37.93a7 7 0 100-14 7 7 0 000 14zm-7 7a7 7 0 11-14 0 7 7 0 0114 0z" opacity=".2"></path><path d="M75.5 27a7 7 0 11-14 0 7 7 0 0114 0zm-7 4a4 4 0 100-8 4 4 0 000 8zm-14 17a7 7 0 100-14 7 7 0 000 14zm4-7a4 4 0 11-8 0 4 4 0 018 0zM27.21 70.41l2.93 3.23a3.47 3.47 0 004.74.37l2.82-2.25c.95.34 2.03.25 2.95-.33.8-.51 1.95-1.26 3.35-2.2v8.29c0 3.02 3.6 4.6 5.82 2.56l8.25-7.56a3.5 3.5 0 001.03-1.77l3.84-16.33c1.7-1.53 3.4-3.13 5.08-4.8 11.26-11.2 22.04-25.83 22.92-41.56a3.32 3.32 0 00-3.5-3.5c-15.71.86-30.38 11.47-41.59 22.54a161.7 161.7 0 00-5.19 5.4 1.5 1.5 0 00-.54.05l-18.35 4.83c-.74.2-1.4.63-1.87 1.25l-5.18 6.8A3.47 3.47 0 0017.48 51H26c-.83 1.21-1.5 2.2-1.95 2.92a3.42 3.42 0 00.38 4.2l.4.43-2.06 2.06a3.47 3.47 0 00-.11 4.79l2.53 2.79-.75.75a1.5 1.5 0 002.12 2.12l.65-.65zM87.95 7.9c-.17 2.9-.7 5.78-1.53 8.61a9.21 9.21 0 01-5.8-2.27 7.13 7.13 0 01-2.6-4.88 39.23 39.23 0 019.59-1.8c.2 0 .35.15.34.34zM47.96 29.24c7.87-7.77 17.2-15.02 27.15-18.87a10.32 10.32 0 003.54 6.13 12.33 12.33 0 006.8 2.95c-3.8 10.29-11.39 19.93-19.54 28.03A169.1 169.1 0 0139.04 68.9c-.15.1-.37.08-.53-.09l-4.68-5.02 10.73-10.73a1.5 1.5 0 00-2.12-2.12L31.78 61.6l-5.16-5.53a.42.42 0 01-.06-.52 167.03 167.03 0 0121.4-26.31zm-18.3 34.48l-2.35 2.35-2.43-2.69a.47.47 0 01.01-.65l1.99-1.99 2.78 2.98zm-.33 4.57l2.37-2.37 3.64 3.89L33 71.67a.47.47 0 01-.65-.05l-3.03-3.33zM47 67.15c3.38-2.4 7.6-5.56 12.06-9.34l-2.88 12.25a.47.47 0 01-.14.24l-8.25 7.56a.47.47 0 01-.79-.34V67.15zm-9.8-30.73A177.28 177.28 0 0028.11 48H17.48a.47.47 0 01-.37-.76l5.17-6.8a.47.47 0 01.26-.16l14.67-3.86zM14.06 81.44a1.5 1.5 0 010 2.12l-7 7a1.5 1.5 0 01-2.12-2.12l7-7a1.5 1.5 0 012.12 0zm9-6.88a1.5 1.5 0 00-2.12-2.12l-5 5a1.5 1.5 0 002.12 2.12l5-5zm-7-4.12a1.5 1.5 0 010 2.12l-7 7a1.5 1.5 0 01-2.12-2.12l7-7a1.5 1.5 0 012.12 0zm9 12.12a1.5 1.5 0 00-2.12-2.12l-5 5a1.5 1.5 0 002.12 2.12l5-5z"></path></g></svg>
            </center><br />
              <h1 class="text-center" style="color: <?php echo $backdrop[1]; ?>"><?php echo $title; ?></h1><br />
              <h5 class="text-justify p-0 mb-0 mt-4" style="color: <?php echo $backdrop[1]; ?>; font-size: 20px; line-height: 1.5; margin-bottom:2.2em;" ><div><?php echo $description; ?></div></h5>
              <div class="text-right p-0 mb-0 rounded-0 mt-4 bg-transparent">
                  <h5 class="fs-14 fw-medium"  style="color: <?php echo $backdrop[1]; ?>">Posted by: <img width="40" src="<?php echo $avatar ?>" alt="avatar" class="rounded-full"> <?php echo $author; ?></h5>
                </div>
                <div class="text-center p-0 mb-0 rounded-0 mt-4 bg-transparent">
                <div class="dropdown dropright">
                  <a class="btn btn-primary dropdown-toggle" href="<?php echo TAOH_FLASHCARD_URL; ?>" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Next Flashcard
                  </a>

                  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                  <?php
                    echo "<a class=\"dropdown-item\" href=\"".TAOH_SITE_URL_ROOT."/learning/flashcard\">Random</a>";

                    foreach (taoh_get_categories('flash') as $category_elem) {
                      echo "<a class=\"dropdown-item ". category_active($category_elem['slug']) ."\" href=\"".TAOH_SITE_URL_ROOT."/learning/flashcard/".$category_elem['slug']."\">".$category_elem['title']." (".$category_elem['count'].")</a>";
                    }
                    ?>
                  </div>
                </div>
              </div>
            </div><!-- end about-content -->
        </div><!-- end col-lg-8 -->

            <div class="col-lg-4">
                <div class="sidebar">
                  <?php if ( taoh_user_is_logged_in() ) { ?>
                  <div class="card card-item">
                      <div class="card-body">
                          <h3 class="fs-17 pb-3">Submit Tips</h3>
                          <div class="divider"><span></span></div>
                          <form action="<?php echo TAOH_ACTION_URL .'/tips'; ?>" method="post" class="pt-4">
                            <div class="form-group">
                                <label class="fs-14 text-black fw-medium lh-20">Tip [250 char or less]</label>
                                <input type="text" id="tip" name="tip" class="form-control form--control fs-14" placeholder="e.g. Some tip/hack here...">
                            </div><!-- end form-group -->
                            <div class="form-group">
                                <label class="fs-14 text-black fw-medium lh-20">Associated URL/Link</label>
                                <input type="text" id="url" name="url" class="form-control form--control fs-14" placeholder="e.g. https://">
                            </div><!-- end form-group -->
                            <div class="form-group">
                              <label class="fs-13 text-black lh-20 fw-medium">Pick a Category</label>
                                <select id="catSelect" class="custom-select custom--select" name='cat'></select>
                            </div>
                            <div class="form-group mb-0">
                                <button id="send-message-btn" class="btn theme-btn" type="submit">Submit Tip</button>
                            </div><!-- end form-group -->
                          </form>
                      </div>
                  </div><!-- end card -->
                  <?php } ?>
						<?php if (function_exists('taoh_obviousbaba_widget')) { taoh_obviousbaba_widget();  } ?>
                        <?php if (function_exists('taoh_readables_widget')) { taoh_readables_widget();  } ?>
                        <?php if (function_exists('taoh_reads_category_widget')) { taoh_reads_category_widget();  } ?>
                        <?php if (function_exists('taoh_ads_widget')) { taoh_ads_widget();  } ?>
            		</div>
        </div><!-- end row -->
    </div><!-- end container -->
</section><!-- end blog-area -->

<script type="text/javascript">
	let listTips = $('#listTips');
	let listCat = $('#listCat');
	let catSelect = $('#catSelect');
	let cat = "";
	let search = "";
	let offset = "";
	let limit = "";
	let ops = "";

	$(document).ready(function() {
		get_cat_list();
		get_tips();
	})

	function do_search(e) {
		search = $(e).val();
		get_tips();
	}

	function get_cat_list() {
		let cat = [
			{"title": "General Search Strategy", "slug": "general-search-strategy"},
			{"title": "Interview", "slug": "interview"},
			{"title": "Job Search", "slug": "job-search"},
			{"title": "Networking", "slug": "networking"},
			{"title": "Resume", "slug": "resume"},
			{"title": "Jobs of Future", "slug": "jobs-of-future"},
			{"title": "General Work Strategy", "slug": "general-work-strategy"},
			{"title": "Branding", "slug": "branding"},
			{"title": "Career Development", "slug": "career-development"},
			{"title": "Conflict Management", "slug": "conflict-management"},
			{"title": "Growth Mindset", "slug": "growth-mindset"},
			{"title": "Handling Change", "slug": "handling-change"},
			{"title": "Leadership", "slug": "leadership"},
			{"title": "Learning", "slug": "learning"},
			{"title": "Mindfulness", "slug": "mindfulness"},
			{"title": "Upskilling", "slug": "upskilling"},
			{"title": "Future of Work", "slug": "upskilling"}
		]


		listCat.append(`
			<li class="nav-item">
				<button data-toggle="tab" class="nav-link mb-2 active" onclick="cat_change(this, '')">All</button>
		</li>
		`);
		listCat.append(`
			<li class="nav-item">
				<button data-toggle="tab" class="nav-link mb-2 " onclick="cat_change(this, 'mytips')">My Tips</button>
		</li>
		`);
		$.each(cat, function(i, v){
			catSelect.append(`
				  <option selected="selected" value="${v.slug}">${v.title}</option>
			`)

			listCat.append(`
				<li class="nav-item">
						<button data-toggle="tab" class="nav-link mb-2" onclick="cat_change(this, '${v.slug}')">${v.title}</button>
				</li>
			`)

		});
	}

	function cat_change(e, data) {
		if(data == "mytips") {
			ops = 'mytips'
		} else {
			cat = data;
			ops = '';
		}
		get_tips();
	}

	function tips_delete(id) {
		var result = confirm("Want to delete?");
		if (result) {
			var data = {
				 'taoh_action': 'delete_tips',
				 'conttoken': id,
			 };
			jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
				get_tips();
			});
		}
	}

	function tips_upvote(id) {
		var data = {
			 'taoh_action': 'upvote_tips',
			 'conttoken': id,
		 };
	 jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
		 get_tips();
	 });
	}


	function get_tips() {
		//loader(true, loaderArea);
		var data = {
			 'taoh_action': 'get_tips',
			 'cat': cat,
			 'ops': ops,
			 'search': search,
			 'offset': offset,
			 'limit': limit,
		 };
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
			console.log(response);
			render_tips_template(response, listTips);
			//loader(false, loaderArea);
		}).fail(function() {
				//loader(false, loaderArea);
				//console.log( "Network issue!" );
				//listChatRooms.empty().append("<p>Server Error. Please Reload the page!</p>");
		})
	}

		function render_tips_template(data, slot) {
			console.log(data);
			slot.empty();
			if(!data.list) {
				slot.append("<p>No data found!</p>");
			} else {
				$.each(data.list, function(i, v){
					slot.append(`
						<div class="media media-card rounded-0 shadow-none mb-0 bg-transparent py-3 px-0 border-bottom border-bottom-gray">
					    <div class="votes text-center votes-2">
					        <div class="vote-block">
					          <span class="vote-counts d-block text-center pr-0 lh-20 fw-medium">${v.votes} <a  onclick="tips_upvote('${v.conttoken}')" <?php echo ( defined('TAOH_API_TOKEN') ) ? 'href="#"': ''; ?>><i class="las la-thumbs-up"></i></a></span>
					            <span class="vote-text d-block fs-13 lh-18">
											${v.votes > 1 ? 'Votes': 'Vote'}
								</span>
					        </div>
					    </div>
					      <div class="media-body">
					          <h5 class="mb-2 fw-medium"><a href="${v.url}">${v.title}</a>
										<font size=1>${v.chat_name}</font>
										${ops == 'mytips' ? `&nbsp;&nbsp;<a onclick="tips_delete('${v.conttoken}')" href="#" class="tag-link text-danger">DELETE</a>`: ''}
										</h5>
					          <div class="tags">
					              <a href="?cat=${v.cat}" class="tag-link">${v.cat}</a>
					          </div>
					      </div>
					  </div><!-- end media -->
					`);
				})
			}
		}

</script>

<?php taoh_get_footer();  ?>
