<?php
$conttoken = taoh_parse_url(2);

$locn = TAOH_SITE_CONTENT_GET."/?mod=core&ops=detail&type=blog&conttoken=".$conttoken."&token=".taoh_get_dummy_token();
$response = json_decode(taoh_url_get_content($locn), true);
$success = $response['success'];
$title = $response['output']['title'];
$description = $response['output']['description'];
$video_link = @$response['output']['media'] ? $response['output']['media'] : "";

$image = $response['output']['image'];
$date = $response['output']['created'];


$categories = $response['output']['category'];

$related_posts = ""; //$related['output;
//Missing fields
$author = $response['output']['author']['fname']." ".$response['output']['author']['lname'];
$profile_picture = $response['output']['author']['avatar'];
$ptoken = $response['output']['author']['ptoken'];

//print_r($response['output']);taoh_exit();

/*
Configure for Google Search Console
*/
// TAO_PAGE_AUTHOR
define('TAO_PAGE_AUTHOR', $author);
// TAO_PAGE_DESCRIPTION
define('TAO_PAGE_DESCRIPTION', $description);
// TAO_PAGE_IMAGE
define('TAO_PAGE_IMAGE', $image[0]);
// TAO_PAGE_TITLE
define('TAO_PAGE_TITLE', $title);
// TAO_PAGE_TWITTER_SITE
// TAO_PAGE_ROBOT
define('TAO_PAGE_ROBOT', 'index, follow');
// TAO_PAGE_CANONICAL
if ( isset( $response['output'][ 'source' ] )  && mb_strtolower( $response['output'][ 'source' ] ) != mb_strtolower( TAOH_SITE_URL ) ){
	$additive = '<link rel="canonical" href="'.$response['output'][ 'source' ].'/hires/learning/blog/'.slugify2($title)."-".$conttoken.'"/>';
    //define('TAO_PAGE_CANONICAL', $response['output'][ 'source' ].'/hires/learning/blog/'.$conttoken);
    define('TAO_PAGE_CANONICAL', $additive );
}

// TAO_PAGE_CATEGORY

taoh_get_header($additive); ?>
<section class="hero-area pattern-bg-2 bg-white shadow-sm overflow-hidden pt-50px pb-50px">
    <span class="stroke-shape stroke-shape-1"></span>
    <span class="stroke-shape stroke-shape-2"></span>
    <span class="stroke-shape stroke-shape-3"></span>
    <span class="stroke-shape stroke-shape-4"></span>
    <span class="stroke-shape stroke-shape-5"></span>
    <span class="stroke-shape stroke-shape-6"></span>
    <div class="container">
        <div class="hero-content">
            <ul class="breadcrumb-list pb-2">
                <li><a href="<?php echo TAOH_SITE_URL_ROOT; ?>">Home</a><span><svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"/></svg></span></li>
                <li><a href="<?php echo TAOH_READS_URL; ?>">Blogs</a><span><svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"/></svg></span></li>
                <li><?php echo @$title; ?></li>
            </ul>
            <h1 class="section-title"><?php echo @$title; ?></h1>
            <div class="d-flex"><div><h4 class="pb-3 fs-20 pt-2">Tags:</h4>
                <div class="tags pb-3">
                    <?php
                    $categories = explode(',', $categories[0]);
                    foreach ($categories as $category) {?>
                        <a href="<?php echo TAOH_READS_URL."?q=$category"; ?>&type=category" class="tag-link tag-link-md">
                            <?php echo $category?>
                        </a>
                    <?php } ?>
                </div></div>
            <div class="ml-5"><h4 class="pb-2 fs-20 pt-2">Share:</h4>
                <div class="social-icon-box">
                    <a class="mr-1 icon-element icon-element-sm shadow-sm text-gray hover-y d-inline-block" href="#" target="_blank" title="Follow on Facebook">
                        <svg focusable="false" class="svg-inline--fa fa-facebook-f fa-w-10" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"></path></svg>
                    </a>
                    <a class="mr-1 icon-element icon-element-sm shadow-sm text-gray hover-y d-inline-block" href="#" target="_blank" title="Follow on Twitter">
                        <svg focusable="false" class="svg-inline--fa fa-twitter fa-w-16" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"></path></svg>
                    </a>
                    <a class="mr-1 icon-element icon-element-sm shadow-sm text-gray hover-y d-inline-block" href="#" target="_blank" title="Follow on Linkedin">
                        <svg focusable="false" class="svg-inline--fa fa-linkedin fa-w-14" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M416 32H31.9C14.3 32 0 46.5 0 64.3v383.4C0 465.5 14.3 480 31.9 480H416c17.6 0 32-14.5 32-32.3V64.3c0-17.8-14.4-32.3-32-32.3zM135.4 416H69V202.2h66.5V416zm-33.2-243c-21.3 0-38.5-17.3-38.5-38.5S80.9 96 102.2 96c21.2 0 38.5 17.3 38.5 38.5 0 21.3-17.2 38.5-38.5 38.5zm282.1 243h-66.4V312c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9V416h-66.4V202.2h63.7v29.2h.9c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9V416z"></path></svg>
                    </a>
                    <a class="mr-1 icon-element icon-element-sm shadow-sm text-gray hover-y d-inline-block" href="#" target="_blank" title="Follow on Instagram">
                        <svg focusable="false" class="svg-inline--fa fa-instagram-square fa-w-14" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M224,202.66A53.34,53.34,0,1,0,277.36,256,53.38,53.38,0,0,0,224,202.66Zm124.71-41a54,54,0,0,0-30.41-30.41c-21-8.29-71-6.43-94.3-6.43s-73.25-1.93-94.31,6.43a54,54,0,0,0-30.41,30.41c-8.28,21-6.43,71.05-6.43,94.33S91,329.26,99.32,350.33a54,54,0,0,0,30.41,30.41c21,8.29,71,6.43,94.31,6.43s73.24,1.93,94.3-6.43a54,54,0,0,0,30.41-30.41c8.35-21,6.43-71.05,6.43-94.33S357.1,182.74,348.75,161.67ZM224,338a82,82,0,1,1,82-82A81.9,81.9,0,0,1,224,338Zm85.38-148.3a19.14,19.14,0,1,1,19.13-19.14A19.1,19.1,0,0,1,309.42,189.74ZM400,32H48A48,48,0,0,0,0,80V432a48,48,0,0,0,48,48H400a48,48,0,0,0,48-48V80A48,48,0,0,0,400,32ZM382.88,322c-1.29,25.63-7.14,48.34-25.85,67s-41.4,24.63-67,25.85c-26.41,1.49-105.59,1.49-132,0-25.63-1.29-48.26-7.15-67-25.85s-24.63-41.42-25.85-67c-1.49-26.42-1.49-105.61,0-132,1.29-25.63,7.07-48.34,25.85-67s41.47-24.56,67-25.78c26.41-1.49,105.59-1.49,132,0,25.63,1.29,48.33,7.15,67,25.85s24.63,41.42,25.85,67.05C384.37,216.44,384.37,295.56,382.88,322Z"></path></svg>
                    </a>
                    <a class="mr-1 icon-element icon-element-sm shadow-sm text-gray hover-y d-inline-block" href="#" target="_blank" title="Share vai Email">
                        <svg focusable="false" class="svg-inline--fa fa-envelope fa-w-16" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"></path></svg>
                    </a>
                </div></div></div>
            <!-- <div>
                <div class="float-left">
                    <div class="media media-card align-items-center shadow-none p-0 mb-0 rounded-0 mt-4 bg-transparent">
                        <?php //if(isset( $profile_picture ) && $profile_picture) { ?>
                            <a href="#" class="media-img media-img--sm d-block mr-2 rounded-full">
                                    <img src="<?php //echo @$profile_picture; ?>" alt="avatar" class="rounded-full lazy" style="">
                            </a> 
                        <?php //} ?>
                            <div class="media-body">
                                    <?php //if(isset( $author ) && $author) { ?>
                                        <h5 class="fs-14 fw-medium">By <a href="#"><?php //echo $author; ?></a></h5>
                                        <?php //echo taoh_follow_widget(array('token'=> 'omggmumb0z1p', 'type'=>'author')); ?>
                                    <?php //} ?>
                            </div>
                    </div>
                </div>
                <div class="float-left p-4">
                    <?php //echo taoh_likes_widget(array('conttoken'=> $conttoken, 'type'=>'blog')); ?>
                </div>
            </div> -->

        </div><!-- end hero-content -->
    </div><!-- end container -->
</section>

<section class="blog-area pt-80px pb-80px">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-item">
                    <div class="card-body">

                      <?php

                        if(isset( $video_link ) && $video_link) {
                          if(is_stream_link($video_link)) {?>
                            <iframe src="<?php echo play_url($video_link); ?>" width="100%" height="350px"></iframe>
                          <?php } else {?>
                            <p>Media Link: <a href="<?php echo $video_link; ?>"><?php echo $video_link; ?></a></p>
                          <?php } ?>
                        <?php
                        } else {
                            echo "<img src=\"".$image[0]."\" width=100%><br /><br />";
                        }
						if ( isset($response['output'][ 'link' ]) ){
							echo "<h3><a href=\"".$response['output']['link']."\">Read Here</a></h3>";
						}
						echo html_content($description);
						?>
                        <hr class="border-top-gray">
						<hr class="border-top-gray">
                    </div><!-- end card-body -->
                </div><!-- end card -->
                <?php echo taoh_comments_widget(array('conttoken'=> $conttoken, 'conttype'=> 'blog', 'label'=> 'Comment')); ?>
            </div><!-- end col-lg-8 -->
            <div class="col-lg-4">
                <div class="sidebar">
                    <?php tags_widget(); ?>
                    <div class="card card-item">
                        <div class="card-body">
                            <h3 class="fs-17 pb-3">Follow &amp; Connect</h3>
                            <div class="divider"><span></span></div>
                            <div class="social-icon-box pt-3">
                                <a class="mr-1 icon-element icon-element-sm shadow-sm text-gray hover-y d-inline-block" href="#" target="_blank" title="Follow on Facebook">
                                    <svg focusable="false" class="svg-inline--fa fa-facebook-f fa-w-10" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"></path></svg>
                                </a>
                                <a class="mr-1 icon-element icon-element-sm shadow-sm text-gray hover-y d-inline-block" href="#" target="_blank" title="Follow on Twitter">
                                    <svg focusable="false" class="svg-inline--fa fa-twitter fa-w-16" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"></path></svg>
                                </a>
                                <a class="mr-1 icon-element icon-element-sm shadow-sm text-gray hover-y d-inline-block" href="#" target="_blank" title="Follow on Twitter">
                                    <svg focusable="false" class="svg-inline--fa fa-twitter fa-w-16" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"></path></svg>
                                </a>
                                <a class="mr-1 icon-element icon-element-sm shadow-sm text-gray hover-y d-inline-block" href="#" target="_blank" title="Follow on Linkedin">
                                    <svg focusable="false" class="svg-inline--fa fa-linkedin fa-w-14" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M416 32H31.9C14.3 32 0 46.5 0 64.3v383.4C0 465.5 14.3 480 31.9 480H416c17.6 0 32-14.5 32-32.3V64.3c0-17.8-14.4-32.3-32-32.3zM135.4 416H69V202.2h66.5V416zm-33.2-243c-21.3 0-38.5-17.3-38.5-38.5S80.9 96 102.2 96c21.2 0 38.5 17.3 38.5 38.5 0 21.3-17.2 38.5-38.5 38.5zm282.1 243h-66.4V312c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9V416h-66.4V202.2h63.7v29.2h.9c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9V416z"></path></svg>
                                </a>
                                <a class="mr-1 icon-element icon-element-sm shadow-sm text-gray hover-y d-inline-block" href="#" target="_blank" title="Follow on Instagram">
                                    <svg focusable="false" class="svg-inline--fa fa-instagram-square fa-w-14" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M224,202.66A53.34,53.34,0,1,0,277.36,256,53.38,53.38,0,0,0,224,202.66Zm124.71-41a54,54,0,0,0-30.41-30.41c-21-8.29-71-6.43-94.3-6.43s-73.25-1.93-94.31,6.43a54,54,0,0,0-30.41,30.41c-8.28,21-6.43,71.05-6.43,94.33S91,329.26,99.32,350.33a54,54,0,0,0,30.41,30.41c21,8.29,71,6.43,94.31,6.43s73.24,1.93,94.3-6.43a54,54,0,0,0,30.41-30.41c8.35-21,6.43-71.05,6.43-94.33S357.1,182.74,348.75,161.67ZM224,338a82,82,0,1,1,82-82A81.9,81.9,0,0,1,224,338Zm85.38-148.3a19.14,19.14,0,1,1,19.13-19.14A19.1,19.1,0,0,1,309.42,189.74ZM400,32H48A48,48,0,0,0,0,80V432a48,48,0,0,0,48,48H400a48,48,0,0,0,48-48V80A48,48,0,0,0,400,32ZM382.88,322c-1.29,25.63-7.14,48.34-25.85,67s-41.4,24.63-67,25.85c-26.41,1.49-105.59,1.49-132,0-25.63-1.29-48.26-7.15-67-25.85s-24.63-41.42-25.85-67c-1.49-26.42-1.49-105.61,0-132,1.29-25.63,7.07-48.34,25.85-67s41.47-24.56,67-25.78c26.41-1.49,105.59-1.49,132,0,25.63,1.29,48.33,7.15,67,25.85s24.63,41.42,25.85,67.05C384.37,216.44,384.37,295.56,382.88,322Z"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div><!-- end card -->
                </div><!-- end sidebar -->
            </div><!-- end col-lg-4 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section>
<?php $related = blog_related_post($categories[0]);
if($related->success) { ?>
	<section class="blog-area pt-80px pb-50px">
    <div class="container">
        <h2 class="section-title fs-30">Related posts</h2>
        <div class="row mt-40px">
            <?php if($related) {
              foreach ($related->output as $post) {?>
              <div class="col-lg-4">
                    <div class="card card-item hover-y">
						<a href="<?php echo taoh_blog_link($post->conttoken, @$post->link); ?>" class="card-img">
                        <img class="lazy" src="<?php echo @$post->image[0]; ?>" alt="Card image" style=""></a>
                        <div class="card-body pt-0">
                          <h5 class="card-title fw-medium"><a href="<?php echo taoh_blog_link($post->conttoken, @$post->link); ?>">
								<?php echo @$post->title; ?>
								<?php if(isset($post->link)) {
		                          echo external_link_icon();
		                        } ?>
													</a></h5>
                          <div class="media media-card align-items-center shadow-none p-0 mb-0 rounded-0 mt-4 bg-transparent">
                              <?php if(@$post->author->avatar) { ?>
                                <a href="#" class="media-img media-img--sm d-block mr-2 rounded-full">
                                    <img src="<?php echo $post->author->avatar ?>" alt="avatar" class="rounded-full">
                                </a>
                              <?php } ?>
                              <div class="media-body">
                                  <?php if(@$post->author) { ?>
                                    <h5 class="fs-14 fw-medium">By <a href="#"><?php echo @$post->author->fname .' '. @$post->author->lname; ?></a></h5>
                                  <?php } ?>
                              </div>
                          </div>
                      </div><!-- end card-body -->
                    </div><!-- end card -->
              </div><!-- end col-lg-4 -->

            <?php }
          }?>

        </div><!-- end row -->
    </div><!-- end container -->
</section>
<?php } ?>
<?php taoh_get_footer();  ?>
