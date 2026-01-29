<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/raj/assets/icons/icons.php';
taoh_get_header();

$pagename = 'announcements';
$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
$user_info_obj = $taoh_user_is_logged_in ? taoh_user_all_info() : null;


if (isset($user_info_obj->avatar_image) && $user_info_obj->avatar_image != '') {
    $avatar_image = $user_info_obj->avatar_image;
} else {
    if (isset($user_info_obj->avatar) && $user_info_obj->avatar != 'default') {
        $avatar_image = TAOH_OPS_PREFIX . '/avatar/PNG/128/' . $user_info_obj->avatar . '.png';
    } else {
        $avatar_image = TAOH_OPS_PREFIX . '/avatar/PNG/128/avatar_def.png';
    }
}

$ptoken = ( taoh_user_is_logged_in()) ? $user_info_obj->ptoken : '';
$token = taoh_get_dummy_token();

$is_admin = false;

if(TAOH_ADMIN_TOKENS !='' && TAOH_ADMIN_TOKENS != null){
    $admin_token_get = explode( ',',TAOH_ADMIN_TOKENS);
  //  print_r($admin_token_get);

    $is_admin = (in_array($token,$admin_token_get))?true:false;
    if(!$is_admin){
        $is_admin = (in_array($ptoken,$admin_token_get))?true:false;
    }

}
//echo "=======".$is_admin;die();
if(taoh_user_is_logged_in() && isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->is_super_admin) &&
taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->is_super_admin == 1  ) {
    $is_admin = true;
}

//$is_admin = true;
?>

<style>
    .upload-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .custom-file-upload {
        display: inline-flex;
        align-items: center;
        padding: 10px 0px;
        color: #555555;
        border-radius: 5px;
        cursor: pointer;

    }

    .custom-file-upload:hover {

    }

    .custom-file-upload input[type="file"] {
        display: none; /* Hide the default file input */
    }


    .preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }

    .preview-item {
        position: relative;
    }

    .preview-item img {
        min-width: 80px;
        min-height: 80px;
        max-width: 100px;
        max-height: 100px;
        margin: 0 auto;
        border: 2px solid #ddd;
        border-radius: 8px;
        padding: 10px;
    }

    .remove-icon {
        position: absolute;
        top: -10px;
        right: 0;
        cursor: pointer;
        color: #fff;
        font-weight: bold;
        background: red;
        width: 20px;
        height: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 50%;
    }

    .custom.dropdown-toggle::after {
        display: none;
    }

    .dot {
        width: 5px;
        height: 5px;
        background: #868686;
        margin-bottom: 4px;
        border-radius: 50%;
    }

    .error{
        color:red;
    }

    @media (max-width: 992px) {
        .dropdown-menu {
            transform: translate3d(-120px, 70px, 0px) !important;
        }
    }

    .gallery_sec{
        width:100%;
        padding:30px 0;
    }
    .heading{
        width:100%;
        text-align:center;
    }
    .heading h2{
        font-size:30px;
        font-weight:bold;
        border-bottom:2px solid #000;
        padding-bottom:25px;
    }
    .gallery_sec img{
        width:100%;
        /* margin-bottom:30px; */
        height:200px;
    }

    .gallery_sec a {
        position: relative;
        transition: 0.3s ease-in-out;
        -webkit-transition: 0.3s ease-in-out;
        -moz-transition: 0.3s ease-in-out;
        -ms-transition: 0.3s ease-in-out;
        -o-transition: 0.3s ease-in-out;
    }


    .gallery_sec a::before {
        position: absolute;
        content: "";
        width: 30px;
        height: 30px;
        background: none;
        background-size: contain;
        background-repeat: no-repeat;
        top:45%;
        left:50%;
        transform:translate(-50%, -50%);
    }

    .gallery_sec img {
        width: 150px;
        height: 150px;
        margin: 0 auto;
        border: 2px solid #ddd;
        border-radius: 8px;
        padding: 10px;
        transition: 0.3s ease-in-out;
        -webkit-transition: 0.3s ease-in-out;
        -moz-transition: 0.3s ease-in-out;
        -ms-transition: 0.3s ease-in-out;
        -o-transition: 0.3s ease-in-out;
        object-fit: contain;
    }

    .gallery_sec a:hover img {
        position: relative;
        width: 100%;
    }

    .gallery_sec a:hover img {
        opacity: 0.2;
    }

    .gallery_sec a:hover::before {
        position: absolute;
        content: "";
        width: 50px;
        height: 50px;
        /* background: url(https://i.ibb.co/3fMkjjF/Resize.png); */
        background-size: contain;
        background-repeat: no-repeat;
        z-index: 99;
    }

    .scrollable-div {
        max-height: 190px; /* Set your desired height */
        overflow-y: auto; /* Enable vertical scrolling */
    }

    .min-max-desc {
        /*min-height: 200px;*/
        max-height: 300px;
        overflow-y: auto;
        scrollbar-width: thin;
    }


</style>

<div class="bg-white">
    <div class="sticky-top bg-white border-bottom border-bottom-gray shadow-sm" style="top: 0; z-index: 9;">
        <?php include 'includes/club_header.php'; ?>
    </div>
    <?php if($taoh_user_is_logged_in && $is_admin){ ?>
        <div class="container row pt-5 pb-4 mx-auto" style="border-bottom: 0.5px solid #000000;">
            <div class="col-lg-11 col-xl-12 mx-auto">
                <div class="d-flex align-items-center" style="gap: 1rem;">
                    <img src="<?php echo $avatar_image; ?>" alt="profile" style="border-radius: 50%;width: 55px; height: 55px; border: 2px solid #ddd;" />
                    <div>
                        <h4 style="font-size: clamp(18px, 3vw + 1rem, 20px); font-weight: 400; color: #555555;">What's on your mind? Share it with your Buddies..</h4>
                    </div>
                </div>
                <div class="py-3 px-lg-5 mx-lg-4">
                    <button type="button" class="btn" style="background: #2557A7; width: 139px; height: 40px; font-size: 17px; border-radius: 20px; color: #fff;" data-toggle="modal" data-target="#postModal">
                        + &nbsp;Post
                    </button>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="container py-5 my-2">
        <div class="row px-3">
            <div class="col-lg-9 pb-5">
                <div class="text-center loaderArea"  id="listloaderArea"></div>
                <div id="listArea" class="mb-3"></div>
                <div id="pagination"></div>
            </div>
            <!-- right side -->
            <div class="col-lg-3 d-flex flex-column flex-sm-row flex-lg-column" style="gap: 8px;">
                <div>
                    <?php if (function_exists('taoh_invite_friends_widget')) { taoh_invite_friends_widget('','club');  } ?>
                </div>
                <div>
                    <?php if (function_exists('taoh_recent_event_widget')) { taoh_recent_event_widget();  } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="postModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content p-lg-3">
            <div class="modal-header px-3" style="background: #fff; border-bottom: 1px solid #D3D3D3 !important;">
                <h5 class="modal-title" id="exampleModalLabel"><span class="edit_cls">Create</span> Post</h5>
                <button type="button" class="btn d-flex align-items-center" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: #D3D3D3; font-size: 19px;">X</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="announcement_form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" value="tc2asi3iida2" name="opscode">
                    <input type="hidden" value="<?php echo $avatar_image; ?>" name="post_avatar">
                    <input type="hidden" value="" name="edit_cont" class="edit_cont">
                    <input type="hidden" value="<?php echo TAOH_SITE_URL_ROOT; ?>" name="source" class="source">
                    <input type="hidden" value="<?php echo TAOH_ROOT_PATH_HASH; ?>" name="sub_secret_token" class="sub_secret_token">
                    <!-- <input type="hidden" value="" name="is_pin" class="is_pin"> -->
                    <div class="form-group">
                        <label for="" style="font-size: 13px;">Title<span style="color: red;">*</span></label>
                        <input type="text" name="feed_title" class="form-control form--control title" style="border-radius: 6px;">
                        <span id="title-error" style="display:none;" class="error">Title is required</span>
                    </div>
                    <div class="form-group">
                        <label for="" style="font-size: 13px;">Content<span style="color: red;">*</span></label>
                        <textarea class ="summernote" name="feed_desc" id="" placeholder="Post Announcements, News etc.," rows="8" style="border: 1px solid #D3D3D3;"></textarea>
                        <span id="textarea-error" style="display:none;" class="error">Description is required</span>
                    </div>
                    <div class="col-md-9">
                        <label class="custom-file-upload mr-3">
                            <input type="file" accept="image/*" multiple id="imageUpload">
                            <?= icon('image', '#555555', 26) ?>
                            <span class="ml-3" style="font-size: 16px; font-weight: 400;">Add Photo</span>
                        </label>
                        <label class="custom-file-upload mr-3">
                            <input type="file" id="fileUpload" multiple accept=".pdf, .doc, .docx">
                            <?= icon('paperclip', '#555555', 28) ?>
                            <span class="ml-3" style="font-size: 16px; font-weight: 400;">Add File</span>
                        </label>
                        <!-- <a href="#" onclick="pin_post()" class="mr-3">
                            <label class="custom-file-upload">
                                <svg width="17" height="23" viewBox="0 0 17 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.42467 1.4375C1.42467 0.642383 2.05751 0 2.84082 0H14.17C14.9533 0 15.5861 0.642383 15.5861 1.4375C15.5861 2.23262 14.9533 2.875 14.17 2.875H12.8645L13.369 9.53242C14.9931 10.4264 16.2765 11.9223 16.8872 13.7865L16.9314 13.9213C17.0775 14.3615 17.0022 14.8422 16.7367 15.215C16.4712 15.5879 16.0419 15.8125 15.5861 15.8125H1.42467C0.968853 15.8125 0.54401 15.5924 0.274058 15.215C0.00410543 14.8377 -0.0667017 14.357 0.079338 13.9213L0.123592 13.7865C0.734304 11.9223 2.01768 10.4264 3.64182 9.53242L4.14632 2.875H2.84082C2.05751 2.875 1.42467 2.23262 1.42467 1.4375ZM7.08925 17.25H9.92153V21.5625C9.92153 22.3576 9.28869 23 8.50539 23C7.72208 23 7.08925 22.3576 7.08925 21.5625V17.25Z" fill="#555555"/>
                                </svg>
                                <span class="ml-3" style="font-size: 16px; font-weight: 400;">Pin this Post</span>
                            </label>
                        </a> -->
                    </div>
                    <div id="previewContainer" class="preview-container"></div>
                    <div class="modal-footer border-0 background-transparent">
                        <button type="submit" class="btn px-4 submit" style="background: #2557A7; color: #fff; border-radius: 20px;"> <span class="edit_btm">Post</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteAlert" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
        <button type="button" style="padding:0" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>

      </div>
      <div class="modal-body">
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
    let existingFiles = [];
    let image_array = [];
    let file_array = [];
    let loaderArea = $('#listloaderArea');
    let listArea = $('#listArea');
    let search = '';
    let itemsPerPage = 10;
    let currentPage = 0;
    let token = '<?php echo taoh_get_dummy_token(); ?>';
    let edit_btn = '';
    let show_files = '';
    let myBlob = '';
    let objectUrl = '';
    let mergedArray = '';
    let owl_carousel = $('.owl-carousel');
    let app_slug = 'announcement';
    let isLoggedIn = "<?php echo taoh_user_is_logged_in(); ?>";
    let liked_check = '';
    let comment_show = '';
    let is_admin = '<?php echo $is_admin; ?>';
    let totalItems = 0;
    let arr_cont = [];
    let like_min = '<?php echo TAOH_SOCIAL_LIKES_THRESHOLD; ?>';

    $(document).ready(function(){
        taoh_feed_init();
    });

    function pin_post(){
        $('.is_pin').val(1);
    }

    $('#postModal').on('hidden.bs.modal', function () {
        $('#announcement_form')[0].reset();  // Reset the form
        $('#previewContainer').empty();
        existingFiles = [];
        $('.summernote').summernote('code', '<p></p>');
        $('.edit_cls').html('Create');
        $('.edit_btm').html('Post');
    });

    function show_pagination(holder) {
		return $(holder).pagination({
				items: totalItems,
				itemsOnPage: itemsPerPage,
				currentPage: currentPage,
				displayedPages: 3,
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
                    taoh_feed_init();
				}
		});
	}

    function taoh_feed_init () {
        loader(true, loaderArea);
		var data = {
            'taoh_action': 'taoh_get_feed_list',
            'ops': 'all',
            'type': 'announcement',
            'search': search,
            'offset': currentPage,
            'limit': itemsPerPage,
		};
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
			render_feed_template(response, listArea);
		    loader(false, loaderArea);
		}).fail(function() {
			loader(false, loaderArea);
			console.log( "Network issue!" );
		})
  	}

    function render_feed_template(data, slot) {
		slot.empty();
		if(data.output === false || data.success  === false) {


            var no_result = `
             <div class="text-center mb-5">
                <img class="no-announcement-img" src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/announce.png'; ?>" alt="">
                <h4 class="no-announcement-title mt-2">It looks like you are Just starting out !</h4>
                <p class="no-announcement-desc pt-4 pb-5">
                We are so excited to have you here!
                Stay tuned for some exciting announcements.</p>
                 <?php if($taoh_user_is_logged_in && $is_admin){ ?>
                <button type="button" class="btn no-announcement-btn px-4 px-lg-5"  data-toggle="modal" data-target="#postModal">
                Make an announcement</button>
                 <?php } ?>

             </div>
            `;


			slot.append(no_result);
			$('#pagination').hide();
			return false;
		}
        totalItems = data.total;
        $.each(data.output, function(i, v){
            arr_cont.push(v.conttoken.toString());
            let show_files = '';
            let pined_post = '';
            load_feeds_comment(v.conttoken);
            if((token == v.token || is_admin) && isLoggedIn){
                edit_btn = `<div class="dropdown float-right">
                                <button class="btn custom dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <div class="dot"></div>
                                    <div class="dot"></div>
                                    <div class="dot"></div>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="#" onclick="feedEdit('${v.conttoken}');">Edit</a>
                                    <a href="#" class="dropdown-item" onclick="feedDelete('${v.conttoken}');">Delete</a>
                                </div>
                            </div>`;
            }
            /* if(v.meta.is_pin){
                pined_post = `<div class="col-xl-4 mb-1 d-flex align-items-center justify-content-end" style="gap: 0.5rem;">
                                    <svg width="17" height="23" viewBox="0 0 17 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.42467 1.4375C1.42467 0.642383 2.05751 0 2.84082 0H14.17C14.9533 0 15.5861 0.642383 15.5861 1.4375C15.5861 2.23262 14.9533 2.875 14.17 2.875H12.8645L13.369 9.53242C14.9931 10.4264 16.2765 11.9223 16.8872 13.7865L16.9314 13.9213C17.0775 14.3615 17.0022 14.8422 16.7367 15.215C16.4712 15.5879 16.0419 15.8125 15.5861 15.8125H1.42467C0.968853 15.8125 0.54401 15.5924 0.274058 15.215C0.00410543 14.8377 -0.0667017 14.357 0.079338 13.9213L0.123592 13.7865C0.734304 11.9223 2.01768 10.4264 3.64182 9.53242L4.14632 2.875H2.84082C2.05751 2.875 1.42467 2.23262 1.42467 1.4375ZM7.08925 17.25H9.92153V21.5625C9.92153 22.3576 9.28869 23 8.50539 23C7.72208 23 7.08925 22.3576 7.08925 21.5625V17.25Z" fill="#2557A7"/>
                                    </svg>
                                    <span style="font-size: clamp(16px, 2vw + 1rem, 21px); color: #B9B9B9; font-weight: 500;">
                                        Pinned Post
                                    </span>
                                </div>`;
            } */
            var img = feedavatardisplay(v.avatar,v.avatar_image,'<?php echo TAOH_OPS_PREFIX;?>');

            if(Array.isArray(v.meta.images)){
                $.each(v.meta.images, function(index, value) {
                    show_files += `<div class="px-3 py-2">
                                        <a href="${value}" data-fancybox="gallery" data-conttoken="${v.conttoken}" class="fancy_box fancybox-item${v.conttoken}">
                                            <img src="${value}" />
                                            </a>
                                    </div>`;
                });
            }
            //alert(v.description)
            if(Array.isArray(v.meta.files)){
                var get_src = '';
                $.each(v.meta.files, function(indexs, values) {
                    var urld = values;
                    var extensiond = urld.split('.').pop();
                    if(extensiond == 'pdf'){
                        get_src = '<?php echo TAOH_SITE_URL_ROOT . '/assets/images/pdf.png'; ?>';
                    }else if(extensiond == 'doc' || extensiond == 'docx'){
                        get_src = '<?php echo TAOH_SITE_URL_ROOT . '/assets/images/word.png'; ?>';
                    }
                    show_files += `<div class="px-3 py-2">
                                        <a href="${urld}" data-fancybox="gallery" data-conttoken="${v.conttoken}" class="fancy_box fancybox-item${v.conttoken}">
                                            <img src="${get_src}" />
                                            </a>
                                    </div>`;
                });
            }
            if(isLoggedIn){
                liked_check = get_liked_check(v.conttoken);
                comment_show = `<a data-conttoken="${v.conttoken}" class="cmtoggle" style="cursor: pointer;"><div class="d-flex align-items-center" style="gap: 6px;">
                                        <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M18 7.07143C18 10.9777 13.9713 14.1429 9.00052 14.1429C7.6963 14.1429 6.45887 13.9253 5.34097 13.5343C4.92263 13.8301 4.24064 14.2346 3.43209 14.5746C2.58839 14.9282 1.57244 15.2308 0.56351 15.2308C0.335008 15.2308 0.131113 15.0982 0.0432276 14.8942C-0.044658 14.6902 0.00455792 14.459 0.162752 14.3026L0.173298 14.2924C0.183844 14.2822 0.197906 14.2686 0.218999 14.2448C0.257668 14.2041 0.31743 14.1395 0.391254 14.0511C0.535387 13.8811 0.728735 13.6295 0.925598 13.3167C1.27714 12.7524 1.61111 12.0112 1.6779 11.1783C0.623272 10.0224 0.0010425 8.6047 0.0010425 7.07143C0.0010425 3.16514 4.02972 0 9.00052 0C13.9713 0 18 3.16514 18 7.07143Z" fill="#D3D3D3"/>
                                        </svg>
                                        <span class="comment_count${v.conttoken}" style="color: #555555;">Comment</span>
                                    </div></a>`;
            }
            slot.append(`<div class="${(i == 0)?'':'mt-5'}">
                            <div class="px-3 pb-3 pt-2" style="border: 1px solid #D3D3D3; border-radius: 3px;">
                                ${edit_btn}
                                <div class="row px-3 py-3" style="border-bottom: 1px solid #D3D3D3;">
                                    <div class="col-12 d-flex align-items-center">
                                        <div class="d-flex align-items-center">
                                            <span data-profile_token="${v.ptoken}" class="openProfileModal"><img src="${img}" alt="profile" style="width: 55px; height: 55px; border-radius: 50%; border: 2px solid #ddd;" /></span>
                                        </div>
                                        <div class="pl-4 d-flex flex-column justify-content-center">
                                            <span data-profile_token="${v.ptoken}" class="openProfileModal"><h4 style="font-size: clamp(16px, 2vw + 1rem, 19px); color: #555555;">${v.fname} ${v.lname}</h4></span>
                                            <p class="" style="font-size: 15px; color: #555555;">${format_Timestamp(v.created)}</p>
                                        </div>
                                    </div>
                                </div>
                                 <h4 class="pl-lg-3 py-3 px-3">
                                    <a class="text-capitalize" style="color: #2557A7; font-size: clamp(16px, 2vw + 1rem, 17px); font-weight: 600;">
                                     ${taoh_desc_decode(v.title)}</a>
                                </h4>
                                <section class="gallery_sec py-0">
                                    <div class="container">
                                        <div class="row">
                                            ${show_files}
                                        </div>
                                    </div>
                                </section>


                                <div class="py-3 px-3 min-max-desc" style="font-size: 16px; line-height: 32px;
                                 font-weight: 400; color: #555555;  max-height: 300px; overflow-y: auto; scrollbar-width: thin;">
                                    ${taoh_desc_decode(v.description)}
                                </div>
                                <div class="">
                                    ${liked_check}
                                    ${comment_show}
                                    </div>
                                    <div class="${v.conttoken}content px-3" style="display:none;">
                                        <div id="${v.conttoken}comment_append"></div>
                                    </div>
                                </div>
                            </div>
                        </div>`);

        });
        if(totalItems >= itemsPerPage) {
            $('#pagination').show();
            show_pagination('#pagination');
        }else{
            $('#pagination').hide();
        }

	}
    function feedavatardisplay(avatar,img,path){
        var avatar_img = '';
        if(img !='' && img!= undefined ){
            avatar_img = ` ${img}`;
        }
        else if(avatar!='' && avatar!= undefined){
            avatar_img = `${path}/avatar/PNG/128/${avatar}.png`;
        }
        else
        avatar_img = ` ${path}/avatar/PNG/128/avatar_def.png`;

        return avatar_img;
    }

    $(document).on("click", ".save_post", function(event) {
        var save_cont = $(this).attr('data-id');
        var textareaValue = document.getElementById("comment_value_"+save_cont).value;
        console.log(textareaValue);
        if(textareaValue == null || textareaValue == ''){
            document.getElementById("commentresponseMessage"+save_cont).style.color = "red";
            document.getElementById("commentresponseMessage"+save_cont).innerHTML = "Please enter comment!";
            return false;
        }
        document.getElementById("commentresponseMessage"+save_cont).innerHTML = "";
        $(this).prop("disabled", true);
        // add spinner to button
        $(this).html(
            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`
        );
        var serializedData = serializeFormById('command_form_'+save_cont);
        console.log(serializedData);
        $.ajax({
            type: 'POST',
            url: '<?php echo TAOH_SITE_URL_ROOT.'/actions/feed_comments';?>', // URL of your PHP script
            data: serializedData,
            success: function(response) {
                taoh_set_success_message('Comment Posted Successfully.');
                load_feeds_comment(save_cont,true);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                taoh_set_error_message('Comment Post Failed!.');
            }
        });
    });

    function serializeFormById(formId) {
        return new URLSearchParams(new FormData(document.getElementById(formId))).toString();
    }

    $(document).on("click", ".cmtoggle", function(event) {
        let cl_conttoken = $(this).attr('data-conttoken');
        $('.'+cl_conttoken+'content').slideToggle(); // Toggle visibility with slide effect
    });

    function load_feeds_comment(com_conttoken,scroll_btm=false){
        const functionName = 'taoh_feeds_comments_widget'; // Change this to call a different function
        const myArray = {
            conttoken: com_conttoken,
            conttype: 'announcement',
            label: 'Comment',
            avatar: '<?php echo $avatar_image; ?>',
            redirect: window.location.href
        };
        var data = {
            'taoh_action': 'post_commentsform',
            'data': myArray,
		};
        $.ajax({
            type: 'POST',
            url: '<?php echo taoh_site_ajax_url(); ?>', // URL of your PHP script
            data: data,
            success: function(response) {
                //console.log('Response:', response);
                $('#'+com_conttoken+'comment_append').html(response);
                var com_count = $('.get_comment'+com_conttoken).text();
                if(com_count > 0){
                    $('.comment_count'+com_conttoken).html(com_count+' '+'Comments');
                }
                if(scroll_btm){
                    const commentsSection = document.getElementById('comments'+com_conttoken);
                    commentsSection.scrollTop = commentsSection.scrollHeight;
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
	};
    function get_liked_check(conttoken){
		var get_liked = 0;
		let is_local = localStorage.getItem(app_slug+'_'+conttoken+'_liked');
		if ((get_liked) || (is_local)) {
			var liked_checks = `<a data-conttoken="${conttoken}">
                <div class="row mx-0 px-3 py-3 d-flex align-items-center" style="gap: 12px;">
                    <div class="d-flex align-items-center" style="gap: 6px;">
                        <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.67344 9.07907L8.02617 15.0099C8.28984 15.256 8.63789 15.3931 9 15.3931C9.36211 15.3931 9.71016 15.256 9.97383 15.0099L16.3266 9.07907C17.3953 8.08415 18 6.68845 18 5.22946V5.02555C18 2.56813 16.2246 0.47282 13.8023 0.0685232C12.1992 -0.198664 10.568 0.325164 9.42188 1.47126L9 1.89313L8.57812 1.47126C7.43203 0.325164 5.80078 -0.198664 4.19766 0.0685232C1.77539 0.47282 0 2.56813 0 5.02555V5.22946C0 6.68845 0.604687 8.08415 1.67344 9.07907Z" fill="#FF0808"/>
                        </svg>
                        <span id="likeCount" data-conts="${(conttoken)}" class="p-0 met_like"></span>
                    <span style="color: #555555;">Like(s)</span>
                </div>
            </a>`;
		} else {
			var liked_checks = `
            <a data-conttoken="${conttoken}" class="feed_liked" style="cursor: pointer;">
                <div class="row mx-0 px-3 py-3 d-flex align-items-center" style="gap: 12px;">
                    <div class="d-flex align-items-center" style="gap: 6px;">
                        <svg width="18" height="16" viewBox="0 0 18 16" fill="none" class="${conttoken}_filled" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.67344 9.07907L8.02617 15.0099C8.28984 15.256 8.63789 15.3931 9 15.3931C9.36211 15.3931 9.71016 15.256 9.97383 15.0099L16.3266 9.07907C17.3953 8.08415 18 6.68845 18 5.22946V5.02555C18 2.56813 16.2246 0.47282 13.8023 0.0685232C12.1992 -0.198664 10.568 0.325164 9.42188 1.47126L9 1.89313L8.57812 1.47126C7.43203 0.325164 5.80078 -0.198664 4.19766 0.0685232C1.77539 0.47282 0 2.56813 0 5.02555V5.22946C0 6.68845 0.604687 8.08415 1.67344 9.07907Z" fill="#D3D3D3"/>
                        </svg>
                        <span id="likeCount" data-conts="${(conttoken)}" class="p-0 met_like"></span>
                    <span style="color: #555555;">Like(s)</span>
                </div>
            </a>`;
		}
		return liked_checks;
	}

    $(document).on("click", ".feed_liked", function(event) {
        //event.stopPropagation(); // Stop the event from propagating to the parent
        var savetoken = $(this).attr('data-conttoken');
        var likes = $('.met_like[data-conts="'+savetoken+'"]').html();
		var count_like = (likes==''?0:parseInt(likes)) + parseInt(1);
		$('.met_like[data-conts="'+savetoken+'"]').html(count_like > like_min ? (count_like):'');
		$('.'+savetoken+'_filled path').attr('fill', '#FF0808');
		$(this).removeAttr("style");
		localStorage.setItem(app_slug+'_'+savetoken+'_liked',1);
		var data = {
			 'taoh_action': 'feed_like_put',
			 'conttoken': savetoken,
			 'ptoken': '<?php echo $ptoken; ?>',
             'slug': 'announcement',
		};
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
			if(response.success){
				taoh_set_success_message('Liked Successfully.');
			}else{
				taoh_set_error_message('Like Failed.');
				console.log( "Like Failed!" );
			}
		}).fail(function() {
			console.log( "Network issue!" );
		})
    });

    function feedDelete(id) {
        $('#deleteAlert .btn.btn-primary').attr('onclick', 'deleteConfirm("'+id+'")');
		$('#deleteAlert').modal('show');
    }

    function deleteConfirm(id) {
		var data = {
			'taoh_action': 'taoh_feed_delete',
			'conttoken': id
		};
        $('#deleteAlert .btn.btn-primary').prop("disabled", true);
        // add spinner to button
        $('#deleteAlert .btn.btn-primary').html(
            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`
        );
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
            console.log(response);
            if (response.success) {
                $('#deleteAlert').modal('hide');
                taoh_set_success_message('Post Deleted Successfully.');
                window.location.reload();
            }
        })
        .fail(function() {
            $('#deleteAlert').modal('hide');
            taoh_set_error_message('Post Delete Failed.');
        });
	}

    function feedEdit(id) {
        $('.edit_cls').html('Edit');
        $('.edit_btm').html('Save');
		$('#postModal').modal('show');
        $('.edit_cont').val(id);
        var data = {
			'taoh_action': 'taoh_get_feed_detail',
			'conttoken': id,
            'type': 'announcement',
		};
		jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
            console.log(response);
            if (response.success) {
                    console.log();
                    $('.title').val(taoh_desc_decode(response.output.title));
                    $('.summernote').summernote('code', taoh_desc_decode(response.output.description));
                    if(Array.isArray(response.output.meta.files) || Array.isArray(response.output.meta.images)){
                        if(Array.isArray(response.output.meta.files)){
                            mergedArray = response.output.meta.files;
                        }
                        if(Array.isArray(response.output.meta.images)){
                            mergedArray = response.output.meta.images;
                        }
                        if(Array.isArray(response.output.meta.files) && Array.isArray(response.output.meta.images)){
                            mergedArray = $.merge(response.output.meta.images, response.output.meta.files);
                        }
                        taoh_img_file_render(mergedArray,true)
                    }
            }
        })
        .fail(function() {

        });
    }

    $('#imageUpload, #fileUpload').on('change', function(event) {
        taoh_img_file_render(event.target.files);
    });

    function taoh_img_file_render(getfiles,from_edit=false){
        const previewContainer = $('#previewContainer');
        // Clear previous previews (optional, uncomment if needed)
        // previewContainer.empty();
        const totalFiles = existingFiles.length + getfiles.length;
        if (totalFiles > 10) {
            alert("You can only upload up to 10 files.");
            return; // Exit the function early
        }

        $.each(getfiles, function(index, file) {
            // Check if the file name already exists
            if (existingFiles.includes(file.name)) {
                console.log('File already exists:', file.name);
                return false; // Skip this file
            }

            if(from_edit){
                var url = file;
                var extension = url.split('.').pop();
                fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.blob();
                })
                .then(blob => {
                    console.log(blob); // This is your Blob object
                    myBlob = blob;
                    // Example: Create an Object URL
                    objectUrl = URL.createObjectURL(blob);
                    console.log(objectUrl); // Use this URL in an <img> tag or elsewhere
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                })
                .finally(() => {
                    let type_array = {};
                    type_array['type'] = url;
                    existingFiles.push(type_array);
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (previewContainer.children().length < 10) {
                            const previewItem = $('<div class="preview-item"></div>');
                            const removeIcon = $('<span class="remove-icon">x</span>');
                            console.log(extension)

                            if (extension == 'jpg' || extension == 'jpeg' || extension == 'png') {
                                const img = $('<img>').attr('src', url);
                                previewItem.append(img);
                                previewItem.append(removeIcon);

                            } else if (extension == 'pdf') {
                                const pdfPreview = $('<img>').attr('src', '<?php echo TAOH_SITE_URL_ROOT . '/assets/images/pdf.png'; ?>');
                                previewItem.append(pdfPreview);
                                previewItem.append(removeIcon);
                            } else if (extension == 'doc' || extension == 'docx') {
                                const wordPreview = $('<img>').attr('src', '<?php echo TAOH_SITE_URL_ROOT . '/assets/images/word.png'; ?>');
                                previewItem.append(wordPreview);
                                previewItem.append(removeIcon);
                            }

                            // Remove icon click event
                            removeIcon.on('click', function() {
                                previewItem.remove();
                                // Remove the file name from existingFiles array
                                existingFiles.splice(existingFiles.indexOf(index), 1);
                            });

                            previewContainer.append(previewItem);
                        }
                    };
                    reader.readAsDataURL(myBlob);
                });
            }

            if(!from_edit){
                existingFiles.push(file); // Add the file name to the existingFiles array
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (previewContainer.children().length < 10) {
                        const previewItem = $('<div class="preview-item"></div>');
                        const removeIcon = $('<span class="remove-icon">x</span>');

                        // Check the file type
                        if (file.type.startsWith('image/')) {
                            const img = $('<img>').attr('src', e.target.result);
                            previewItem.append(img);
                        } else if (file.type === 'application/pdf') {
                            const pdfPreview = $('<img>').attr('src', '<?php echo TAOH_SITE_URL_ROOT . '/assets/images/pdf.png'; ?>');
                            previewItem.append(pdfPreview);
                        } else if (file.type === 'application/msword' || file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                            const wordPreview = $('<img>').attr('src', '<?php echo TAOH_SITE_URL_ROOT . '/assets/images/word.png'; ?>');
                            previewItem.append(wordPreview);
                        } else {
                            const unsupported = $('<span></span>').text('Unsupported file type: ' + file.name);
                            previewItem.append(unsupported);
                        }

                        // Remove icon click event
                        removeIcon.on('click', function() {
                            previewItem.remove();
                            // Remove the file name from existingFiles array
                            existingFiles.splice(existingFiles.indexOf(index), 1);
                        });

                        previewItem.append(removeIcon);
                        previewContainer.append(previewItem);
                    }
                };
                reader.readAsDataURL(file);
            }

        });
    }

    $('#announcement_form').on('submit', function(e) {
        e.preventDefault(); // Prevent form submission

        // Validate title
        if ($('.title').val() === '') {
            $('#title-error').show();
            return false;
        }
        $('#title-error').hide();

        // Validate summernote content
        if ($('.summernote').summernote('isEmpty')) {
            $('#textarea-error').show();
            return false;
        }
        $('#textarea-error').hide();

        const formData = new FormData(this);
        const serializedData = new URLSearchParams(formData).toString();
        console.log(existingFiles);
        $('.submit').prop("disabled", true);
        $('.submit').html(
            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`
        );

        const uploadCount = 0; // Counter for completed uploads
        const totalFiles = existingFiles.length; // Total number of files
        console.log(totalFiles);
        if (totalFiles > 0) {
            uploadFiles(existingFiles, serializedData);
        } else {
            saveAnnouncement(serializedData, 'deleted', 'deleted');
        }
    });

    function uploadFiles(files, serializedData) {
        let uploadCount = 0;
        const totalFiles = files.length;

        $.each(files, function(index, file) {
            const formData = new FormData(); // Create a new FormData for each file
            const isHttpFile = file.type.startsWith('http');

            if (!isHttpFile) {
                // Upload file via AJAX
                formData.append('fileToUpload', file);
                formData.append('opscode', '<?php echo TAOH_OPS_CODE; ?>');

                $.ajax({
                    url: '<?php echo TAOH_CDN_PREFIX; ?>/cache/upload/now',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(`File uploaded successfully: ${file.name}`);
                        handleFileUploadSuccess(response, file);
                    },
                    error: function(xhr, status, error) {
                        console.error(`File upload failed for ${file.name}: ${error}`);
                    },
                    complete: function() {
                        uploadCount++;
                        checkUploadCompletion(uploadCount, totalFiles, serializedData);
                    }
                });
            } else {
                // Handle URL files
                handleUrlFile(file);
                uploadCount++;
                checkUploadCompletion(uploadCount, totalFiles, serializedData);
            }
        });
    }

    function handleFileUploadSuccess(response, file) {
        if (file.type.startsWith('image/')) {
            image_array.push(response.output);
        } else if (['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'].includes(file.type)) {
            file_array.push(response.output);
        }
    }

    function handleUrlFile(file) {
        const extension = file.type.split('.').pop().toLowerCase();

        if (['jpg', 'png'].includes(extension)) {
            image_array.push(file.type);
        } else if (['pdf', 'doc', 'docx'].includes(extension)) {
            file_array.push(file.type);
        } else {
            image_array = 'deleted';
            file_array = 'deleted';
        }
    }

    function checkUploadCompletion(uploadCount, totalFiles, serializedData) {
        if (uploadCount === totalFiles) {
            saveAnnouncement(serializedData, image_array, file_array);
        }
    }

    function saveAnnouncement(serializedData, images, files) {
        const data = {
            'taoh_action': 'taoh_announcement_save',
            'form_data': serializedData,
            'conttoken': $('.edit_cont').val(),
            'images': images,
            'files': files,
            'type': 'announcement',
        };

        $.post("<?php echo taoh_site_ajax_url(); ?>", data)
            .done(function(response) {
                if (response.success) {
                    console.log('Data saved successfully.');
                    taoh_set_success_message('Post Saved Successfully.');
                    $('#postModal').modal('hide');
                    taoh_feed_init ();
                    $('.submit').prop("disabled", false);
                    $('.submit').html(`Submit`);
                    window.location.reload();
                } else {
                    console.error('Failed to save data:', response.message);
                    taoh_set_error_message('Post Save Failed.');
                }
            })
            .fail(function() {
                console.error('Error while saving data.');
            });
    }

    // Custom navigation
    $('.owl-prev').click(function() {
        $('.owl-carousel').trigger('prev.owl.carousel');
    });

    $('.owl-next').click(function() {
        $('.owl-carousel').trigger('next.owl.carousel');
    });

</script>

<?php


taoh_get_footer();