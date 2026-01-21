<?php taoh_get_header(); ?>
<style>

#notify-area a {
	color: #007bff;
}

</style>
<section class="hero-area bg-white shadow-sm overflow-hidden pt-60px pb-50px">
    <span class="stroke-shape stroke-shape-1"></span>
    <span class="stroke-shape stroke-shape-2"></span>
    <span class="stroke-shape stroke-shape-3"></span>
    <span class="stroke-shape stroke-shape-4"></span>
    <span class="stroke-shape stroke-shape-5"></span>
    <span class="stroke-shape stroke-shape-6"></span>
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="hero-content d-flex align-items-center">
                    <div class="icon-element shadow-sm flex-shrink-0 mr-3 border border-gray">
                        <svg class="svg-icon-color-5" height="30" viewBox="0 0 512 512" width="30" xmlns="http://www.w3.org/2000/svg"><g><path d="m411 262.862v-47.862c0-69.822-46.411-129.001-110-148.33v-21.67c0-24.813-20.187-45-45-45s-45 20.187-45 45v21.67c-63.59 19.329-110 78.507-110 148.33v47.862c0 61.332-23.378 119.488-65.827 163.756-4.16 4.338-5.329 10.739-2.971 16.267s7.788 9.115 13.798 9.115h136.509c6.968 34.192 37.272 60 73.491 60 36.22 0 66.522-25.808 73.491-60h136.509c6.01 0 11.439-3.587 13.797-9.115s1.189-11.929-2.97-16.267c-42.449-44.268-65.827-102.425-65.827-163.756zm-170-217.862c0-8.271 6.729-15 15-15s15 6.729 15 15v15.728c-4.937-.476-9.94-.728-15-.728s-10.063.252-15 .728zm15 437c-19.555 0-36.228-12.541-42.42-30h84.84c-6.192 17.459-22.865 30-42.42 30zm-177.67-60c34.161-45.792 52.67-101.208 52.67-159.138v-47.862c0-68.925 56.075-125 125-125s125 56.075 125 125v47.862c0 57.93 18.509 113.346 52.671 159.138z"></path><path d="m451 215c0 8.284 6.716 15 15 15s15-6.716 15-15c0-60.1-23.404-116.603-65.901-159.1-5.857-5.857-15.355-5.858-21.213 0s-5.858 15.355 0 21.213c36.831 36.831 57.114 85.8 57.114 137.887z"></path><path d="m46 230c8.284 0 15-6.716 15-15 0-52.086 20.284-101.055 57.114-137.886 5.858-5.858 5.858-15.355 0-21.213-5.857-5.858-15.355-5.858-21.213 0-42.497 42.497-65.901 98.999-65.901 159.099 0 8.284 6.716 15 15 15z"></path></g></svg>
                    </div>
                    <h2 class="section-title fs-30">Notifications</h2>
                </div><!-- end hero-content -->
            </div><!-- end col-lg-8 -->
            <div class="col-lg-4">
                <div class="hero-btn-box text-right py-3">
                    <a href="<?php echo TAOH_SITE_URL_ROOT."/settings"?>" class="btn theme-btn theme-btn-outline theme-btn-outline-gray"><i class="la la-gear mr-1"></i> Edit Profile</a>
                </div>
            </div><!-- end col-lg-4 -->
        </div>
    </div><!-- end container -->
</section>
<section class="user-details-area pt-60px pb-60px">
    <div class="container">
        <div class="row">
            <div class="col-lg-9">
                <div class="notification-content-wrap" id="notify-area">
                    
                </div><!-- end notification-content-wrap -->
                <div class="pager pt-30px mb-50px">
                    <div id="pagination"></div>
                </div>
            </div><!-- end col-lg-9 -->
            <div class="col-lg-3">
                <div class="sidebar" style="display:none">
                    <div class="card card-item">
                        <div class="card-body">
                            <h3 class="fs-17 pb-3">Number Achievement</h3>
                            <div class="divider"><span></span></div>
                            <div class="row no-gutters text-center">
                                <div class="col-lg-6 responsive-column-half">
                                    <div class="icon-box pt-3">
                                        <span class="fs-20 fw-bold text-color">980k</span>
                                        <p class="fs-14">Questions</p>
                                    </div><!-- end icon-box -->
                                </div><!-- end col-lg-6 -->
                                <div class="col-lg-6 responsive-column-half">
                                    <div class="icon-box pt-3">
                                        <span class="fs-20 fw-bold text-color-2">610k</span>
                                        <p class="fs-14">Answers</p>
                                    </div><!-- end icon-box -->
                                </div><!-- end col-lg-6 -->
                                <div class="col-lg-6 responsive-column-half">
                                    <div class="icon-box pt-3">
                                        <span class="fs-20 fw-bold text-color-3">650k</span>
                                        <p class="fs-14">Answer accepted</p>
                                    </div><!-- end icon-box -->
                                </div><!-- end col-lg-6 -->
                                <div class="col-lg-6 responsive-column-half">
                                    <div class="icon-box pt-3">
                                        <span class="fs-20 fw-bold text-color-4">320k</span>
                                        <p class="fs-14">Users</p>
                                    </div><!-- end icon-box -->
                                </div><!-- end col-lg-6 -->
                                <div class="col-lg-12 pt-3">
                                    <p class="fs-14">To get answer of question <a href="signup.html" class="text-color hover-underline">Join<i class="la la-arrow-right ml-1"></i></a></p>
                                </div>
                            </div><!-- end row -->
                        </div>
                    </div><!-- end card -->
                    <div class="card card-item" style="display:none">
                        <div class="card-body">
                            <h3 class="fs-17 pb-3">Trending Questions</h3>
                            <div class="divider"><span></span></div>
                            <div class="sidebar-questions pt-3">
                                <div class="media media-card media--card media--card-2">
                                    <div class="media-body">
                                        <h5><a href="question-details.html">Using web3 to call precompile contract</a></h5>
                                        <small class="meta">
                                            <span class="pr-1">2 mins ago</span>
                                            <span class="pr-1">. by</span>
                                            <a href="#" class="author">Sudhir Kumbhare</a>
                                        </small>
                                    </div>
                                </div><!-- end media -->
                                <div class="media media-card media--card media--card-2">
                                    <div class="media-body">
                                        <h5><a href="question-details.html">Is it true while finding Time Complexity of the algorithm [closed]</a></h5>
                                        <small class="meta">
                                            <span class="pr-1">48 mins ago</span>
                                            <span class="pr-1">. by</span>
                                            <a href="#" class="author">wimax</a>
                                        </small>
                                    </div>
                                </div><!-- end media -->
                                <div class="media media-card media--card media--card-2">
                                    <div class="media-body">
                                        <h5><a href="question-details.html">image picker and store them into firebase with flutter</a></h5>
                                        <small class="meta">
                                            <span class="pr-1">1 hour ago</span>
                                            <span class="pr-1">. by</span>
                                            <a href="#" class="author">Antonin gavrel</a>
                                        </small>
                                    </div>
                                </div><!-- end media -->
                            </div><!-- end sidebar-questions -->
                        </div>
                    </div><!-- end card -->
                </div><!-- end sidebar -->
            </div><!-- end col-lg-3 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section>
<script type="text/javascript">
let $ = jQuery;
let notificationsList = $('#notify-area');

let totalItems = 0; //this will be rewriiten on response of assks on line 307
let itemsPerPage = 9;
let currentPage = 1; //default on first load

$(document).ready(function(){
    taoh_notify_init();
});

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
            taoh_notify_init();
        }
    });
}

function taoh_notify_init(){
    
    notificationsList.html('');
    notificationsList.html(`
        <p class="no-result"><img id="loaderEmail" width="20" src="<?php echo TAOH_LOADER_GIF; ?>"/> </p>`
    );

    var data = {
        'taoh_action': 'taoh_get_notification_list',
        'mod': 'core',
        'ops': "webnotify",
        "type" : "notify",
        "token" : "<?php echo TAOH_API_TOKEN; ?>",
        "ptoken" :  "<?php echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>",
        "offset" :  currentPage-1,
        "perpage" :  itemsPerPage,
        "limit" : itemsPerPage,
        		 
	};
    jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
         data = response;//JSON.parse(response);
         if(data.status ) {
            console.log('-----------',data);
      	    render_notify_list_template(data,notificationsList);
		}else{
            notificationsList.html('<p class="text-danger">No Result Found</p>'); 
         }
    });
}

function render_notify_list_template(data,slot){ 
    slot.empty();
    if(!data.status) {
        slot.append('<p class="text-danger">No Result Found</p>');
    } else {
        totalItems = data.total_num;
        $.each(data.output, function(i, v){
            slot.append(`
            <div class="media media-card media--card shadow-none rounded-0 align-items-center bg-transparent">
                    <div class="media-img media-img-sm flex-shrink-0">
                        <img src="https://opslogy.com/avatar/PNG/128/${v.avatar ? v.avatar : 'default' }.png" alt="avatar">
                        <h5 class="meta d-block lh-24 fs-14 fw-regular">
                            <span>${v.chat_name}</span>
                        </h5>
                    </div>
                    <div class="media-body p-0 border-left-0">
                        <h5 class="fs-17">${v.title}</h5>
                        <h5 class="fs-14 fw-regular">${v.message}</h5>
                        <small class="meta d-block lh-24">
                            <span>${v.timestamp} ago</span>
                        </small>
                    </div>
                </div>
            `);
        })
        if(totalItems >= 10) {
			show_pagination('#pagination');
		}
    }
}
</script>
<?php taoh_get_footer();  ?>
