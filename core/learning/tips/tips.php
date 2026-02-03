<?php taoh_get_header(); ?>
<section class="blog-area pt-80px pb-80px">
    <div class="container">
        <div class="row">
					<div class="col-lg-2">
					<?php taoh_leftmenu_widget(); ?>
					</div><!-- end col-lg-2 -->
              <div class="col-lg-6">
              <div class="question-main-bar pb-45px">
                <div class="filters pb-4">
                  <div class="hero-content d-flex flex-wrap align-items-center justify-content-between">
                      <div class=" pb-3">
            			<h2 class="section-title fs-24 mb-1">Tips <span id="loaderArea"></span></h2>
                        <p class="section-desc pb-4">Tips/Hacks/Tricks to succeeed at Work/Jobs, Shared by community</p>
                        <div class="form-group">
                            <input onkeyup="do_search(this)"  type="text" id="search" class="form-control" placeholder="Search for ..">
                        </div>

                        <ul id="listCat" class="nav nav-tabs generic-tabs generic-tabs-layout-2 generic--tabs-layout-2 tabs"  role="tablist">

                        </ul>
                      </div>
                  </div>
                </div><!-- end filters -->

                <div class="questions-snippet border-top border-top-gray">
					<div id="listTips"></div>
					<div class="mb-4" id="pagination"></div>
				</div>


              </div><!-- end question-main-bar -->
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

	function show_pagination(holder) {
    return $(holder).pagination({
        items: totalItems,
        itemsOnPage: itemsPerPage,
        currentPage: currentPage,
        onInit: function() {
          $("#pagination ul").addClass('pagination');
          $("#pagination ul li.disabled").addClass('page-link text-gray');
          $("#pagination ul li.active").addClass('page-link bg-primary text-white');
        },
        onPageClick: function(pageNumber, event) {
          $("#pagination ul").addClass('pagination');
          $("#pagination ul li.disabled").addClass('page-link text-gray');
          $("#pagination ul li.active").addClass('page-link bg-primary text-white');
          currentPage = pageNumber;
          taoh_blogs_init();
        }
    });
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
				<button data-toggle="tab" class="nav-link mb-2 mytips" onclick="cat_change(this, 'mytips')">My Tips</button>
		</li>
		`);
		$.each(cat, function(i, v){
			catSelect.append(`
				  <option selected="selected" value="${v.slug}">${v.title}</option>
			`)

			listCat.append(`
				<li class="nav-item">
						<button data-toggle="tab" class="nav-link mb-2 ${v.title}" onclick="cat_change(this, '${v.slug}')">${v.title}</button>
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
		var cls = data.charAt(0).toUpperCase() + data.slice(1);
		$('.active').removeClass('active');
  		$('.'+cls).addClass('active');
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
		loader(true, loaderArea);
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
			loader(false, loaderArea);
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
					          <h5 class="mb-2 fw-medium">${v.title}
										<font size=1>${v.chat_name}</font>
										${ops == 'mytips' ? `&nbsp;&nbsp;<a onclick="tips_delete('${v.conttoken}')" href="#" class="tag-link text-danger">DELETE</a>`: ''}
										</h5>
					          <div class="tags">
					              <a href="#" onclick="cat_change(this, '${v.cat}')" class="tag-link">${v.cat}</a>
					          </div>
					      </div>
					  </div><!-- end media -->
					`);
				})
				if(totalItems >= 11){
					show_pagination('#pagination');
				}
			}
		}

</script>

<?php taoh_get_footer();  ?>
