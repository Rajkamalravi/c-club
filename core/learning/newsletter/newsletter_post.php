<?php
taoh_get_header(); 


$today = date('Y-m-d');
// https://opslogy.com/scripts/code.php
//$url = TAOH_OPS_PREFIX."/scripts/code.php";
//$response = json_decode(taoh_url_get( $url ), true);
//if ( ! isset( $response[ 'output' ] ) || ! isset( $response[ 'output' ] ) || $_GET[ 'q' ] != $response[ 'output' ] ) taoh_redirect(TAOH_SITE_URL_ROOT."/reads");

$conttoken = taoh_parse_url(3);
if(isset( $conttoken ) && $conttoken != 'stlo'){
    $url = "core.content.get";
    $taoh_vals = array(
        'token'=> taoh_get_dummy_token(1),
        'mod' => 'core',
        'ops' => 'detail',
        'type' => 'newsletter',
        'conttoken' => $conttoken,
        //'cfcc1d'=> 1, //cfcache newly added
    );
    // $cache_name = $url.'_newsletter_' . hash('sha256', $url . serialize($taoh_vals));
    // $taoh_vals[ 'cfcache' ] = $cache_name;
    // $taoh_vals[ 'cache_name' ] = $cache_name;
    // $taoh_vals[ 'cache' ][ 'name' ] = $cache_name;
    ksort($taoh_vals);
    
    //echo $taoh_vals['cache']['name'];exit();
    //print_r($taoh_vals);taoh_exit();
    // echo taoh_apicall_get_debug($url, $taoh_vals);taoh_exit();
    $response = json_decode(taoh_apicall_get($url, $taoh_vals), true);
    $nl_response = $response['output'];
    // echo "<pre>"; print_r($nl_response); echo "</pre>"; 
    $title = (isset($nl_response['title']))?taoh_title_desc_decode($nl_response['title']):'';
    $subtitle = (isset($nl_response['subtitle']))?taoh_title_desc_decode($nl_response['subtitle']):'';
    $description = (isset($nl_response['description']))?taoh_title_desc_decode($nl_response['description']):'';
    $excerpt = (isset($nl_response['excerpt']))?taoh_title_desc_decode($nl_response['excerpt']):'';
    $visiblity = (isset($nl_response['visiblity']))?$nl_response['visiblity']:'';
    $category = (isset($nl_response['category'][0]))?$nl_response['category'][0]:'';
    $newsletter_type = (isset($nl_response['newsletter_type']))?$nl_response['newsletter_type']:'';
    $media_link = (isset($nl_response['media_link']))?$nl_response['media_link']:'';
    $media_url = (isset($nl_response['media_url']))?$nl_response['media_url']:'';
    $media_type = (isset($nl_response['media_type']))?$nl_response['media_type']:'';
    $source_name = (isset($nl_response['source_name']))?$nl_response['source_name']:'';
    $source_url = (isset($nl_response['source_url']))?$nl_response['source_url']:'';
    $via_name = (isset($nl_response['via_name']))?$nl_response['via_name']:'';
    $via_url = (isset($nl_response['via_url']))?$nl_response['via_url']:'';
    $publish_date = (isset($nl_response['publish_date']))?$nl_response['publish_date']:'';
    $nl_send_to = (isset($nl_response['nl_send_to']))?$nl_response['nl_send_to']:'';
}

$page = ( isset( $conttoken ) && $conttoken != 'stlo' )? 'Edit':'Create';
$post_local = (defined( 'TAOH_READS_POST_LOCAL')) ? TAOH_READS_POST_LOCAL : false;
?>
<!-- Text editor dependancy -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-te/1.4.0/jquery-te.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-te/1.4.0/jquery-te.min.js"></script>

<!-- Image uploader dependancy -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/filepond/4.30.4/filepond.min.css">
<script src="https://cdn.jsdelivr.net/npm/filepond/dist/filepond.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-filepond/filepond.jquery.js"></script>

<section class="question-area pt-40px pb-40px">
    <div class="container">
        <div class="row">
            <div class="card col-lg-8 z-depth-5 mb-4">
				<div class="card-body">
					<div class="d-flex justify-content-between">
						<div>
							<h3><?php echo $page; ?> Newsletter</h3>
						</div>
					</div>
					<hr>
					<form class="mt-3" method="post" action="<?php echo TAOH_ACTION_URL .'/newsletter'; ?>">
                        <div class="hidden">
                          <input type="hidden" name="action" value="save">
                          <input type="hidden" name="source" value="<?php echo TAOH_SITE_URL_ROOT;?>">
                          <input type="hidden" name="sub_secret_token" value="<?php echo TAOH_ROOT_PATH_HASH;?>">
                          <input type="hidden" name="local" value="<?php echo $post_local;?>">
                          <?php if( ( isset( $conttoken ) ) && $conttoken != 'stlo' ) { ?>
                            <input type="hidden" name="conttoken" value="<?php echo $conttoken ;?>">
                          <?php } ?>
                        </div>
						<div class="row">
                            <div class="mb-12 col-md-12">
								<label class="form-label text-black fw-medium">Newsletter Title <span style="color:red;">*</span></label>
								<input name="title" type="text" value="<?php echo htmlspecialchars($title); ?>" class="form-control" required>
							</div>
                            <div class="mb-12 col-md-12">
								<label class="form-label text-black fw-medium">Newsletter Subtitle</label>
								<input name="subtitle" type="text" value="<?php echo htmlspecialchars($subtitle); ?>" class="form-control">
							</div>
							<div class="mb-3 col-md-12">
								<label  class="form-label text-black fw-medium">Description <span style="color:red;">*</span></label>
								<textarea class="summernote" id="summernote" name="description" rows="10" cols="80"><?php echo $description; ?></textarea>
							</div>
                            <div class="mb-12 col-md-12 fixme">
								<label for="skills" class="form-label text-black fw-medium">Newsletter Excerpt ( For Social media )</label>
								<textarea class="form-control" name="excerpt" rows="3" type="text"><?php echo $excerpt; ?></textarea>
							</div>
                            <div class="mb-3 col-md-6">
								<label for="skills" class="skills-field form-label text-black fw-medium">Visiblity <span style="color:red;">*</span></label>
                                    <select class="form-control" name="visiblity" required>
                                        <option value=""  disabled>Select Visiblity</option>
                                        <option <?php echo ($visiblity == "public" || $visiblity == '') ?'selected': '';?> value="public">Public</option>
                                        <option <?php echo ($visiblity == "login") ?'selected': '';?> value="login">Login Required</option>
                                        <option <?php echo ($visiblity == "password") ?'selected': '';?> value="password">Password Protected</option>
                                    </select>
                                <!-- <div class="form-check">
                                    <input class="form-check-input" type="radio" name="visiblity" value="public" required>
                                    <label class="form-check-label">Public</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="visiblity" value="login" required>
                                    <label class="form-check-label">Login Required</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="visiblity" value="password" required>
                                    <label class="form-check-label">Password Protected</label>
                                </div>
                                -->
							</div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label text-black fw-medium">Category <span style="color:red;">*</span></label>
                                <input id="blogCategorySelect" value="<?php echo $category; ?>" class="input-tags input--tags" type="text" name="category[]" required>
                                <script>blogCategorySelect()</script>
							</div>
                            <div class="mb-3 col-md-6">
								<label for="skills" class="form-label text-black fw-medium">Newsletter Type <span style="color:red;">*</span></label>
                                <select class="form-control" name="newsletter_type" required>
                                    <option <?php echo ($newsletter_type == "internal" || $newsletter_type == '') ?'selected': '';?> value="internal">Internal</option>
                                    <option <?php echo ($newsletter_type == "external") ?'selected': '';?> value="external" >External</option>
                                </select>
							</div>
                            <div class="mb-3 col-md-6">
								<label for="skills" class="form-label text-black fw-medium">Newsletter External Link</label>
								<input class="form-control" value="<?php echo $media_link; ?>" name="media_link" type="text">
							</div>
                            <div class="mb-3 col-md-6">
								<label for="skills" class="form-label text-black fw-medium">Featured Media URL</label>
								<input class="form-control" value="<?php echo $media_url; ?>" name="media_url" type="text">
							</div>
                            <div class="mb-3 col-md-6">
								<label for="skills" class="form-label text-black fw-medium">Featured Media Type</label>
                                <select class="form-control" name="media_type">
                                    <option value="" disabled>Select Media Type</option>
                                    <option <?php echo ($media_type == "image" || $media_type == '') ?'selected': '';?> value="image">Image</option>
                                    <option <?php echo ($media_type == "youtube") ?'selected': '';?>  value="youtube">Youtube</option>
                                    <option <?php echo ($media_type == "soundcloud") ?'selected': '';?> value="soundcloud">SoundCloud</option>
                                </select>
							</div>
                            <div class="mb-3 col-md-6">
								<label class="form-label text-black fw-medium">Source Name</label>
								<input name="source_name" value="<?php echo $source_name; ?>" type="text" class="form-control">
							</div>
                            <div class="mb-3 col-md-6">
								<label class="form-label text-black fw-medium">Source URL</label>
								<input name="source_url" value="<?php echo $source_url; ?>" type="text" class="form-control">
							</div>
                            <div class="mb-3 col-md-6">
								<label class="form-label text-black fw-medium">Via Name</label>
								<input name="via_name" value="<?php echo $via_name; ?>" type="text" class="form-control">
							</div>
                            <div class="mb-3 col-md-6">
								<label class="form-label text-black fw-medium">Via URL</label>
								<input name="via_url" value="<?php echo $via_url; ?>" type="text" class="form-control">
							</div>
                            <div class="mb-3 col-md-6">
								<label class="form-label text-black fw-medium">Publish Date <span style="color:red;">*</span></label>
								<input name="publish_date" min="<?php echo $today.'T00:00'; ?>" value="<?php echo $publish_date; ?>" type="datetime-local" required class="form-control" id="txtDate">
							</div>
                            <div class="mb-3 col-md-6">
                            <label for="skills" class="form-label text-black fw-medium">Send To</label>
                                <select class="form-control" name="nl_send_to">
                                    <option value="" disabled>Send To</option>
                                    <option <?php echo ($nl_send_to == "all" || $nl_send_to == '') ?'selected': '';?> value="all">All</option>
                                    <option <?php echo ($nl_send_to == "professional") ?'selected': '';?>  value="professional">Professional</option>
                                    <option <?php echo ($nl_send_to == "employer") ?'selected': '';?> value="employer">Employer</option>
                                    <option <?php echo ($nl_send_to == "provider") ?'selected': '';?> value="provider">Service Provider</option>
                                </select>
							</div>
                            <div class="mb-3 mt-3 col-md-12">
								<div class="d-flex">
                                    <!-- <a href="#" class="">Save as draft</a> -->
                                    <button type="submit" class="ml-2 btn btn-primary btn-sm">Publish Now</button>
                                    <!-- <div class="ml-2 btn-group">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Schedule
                                        </button>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" type="button">Action</button>
                                            <button class="dropdown-item" type="button">Another action</button>
                                            <button class="dropdown-item" type="button">Something else here</button>
                                        </div>
                                    </div> -->
                                </div>
							</div>
                        </div>
					</form>
				</div>
            </div>
            <div class="col-lg-4">
                <div class="sidebar">
                    <div class="card card-item p-4">
                        <h3 class="fs-17 pb-3">Step 1: Draft your question</h3>
                        <div class="divider"><span></span></div>
                        <p class="fs-14 lh-22 pb-2 pt-3">The community is here to help you with specific coding, algorithm, or language problems.</p>
                        <p class="fs-14 lh-22">Avoid asking opinion-based questions.</p>
                        <div id="accordion" class="generic-accordion pt-4">
                            <div class="card">
                                <div class="card-header" id="headingOne">
                                    <button class="btn btn-link fs-15" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                       <span><span class="text-color pr-2 fs-16">1.</span> Summarize the problem</span>
                                        <i class="la la-angle-down collapse-icon"></i>
                                    </button>
                                </div>
                                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                    <div class="card-body">
                                        <ul class="generic-list-item generic-list-item-bullet generic-list-item--bullet-2 fs-14">
                                            <li class="lh-18 text-black-50">Include details about your goal</li>
                                            <li class="lh-18 text-black-50">Describe expected and actual results</li>
                                            <li class="lh-18 text-black-50 mb-0">Include any error messages</li>
                                        </ul>
                                    </div>
                                </div>
                            </div><!-- end card -->
                            <div class="card">
                                <div class="card-header" id="headingTwo">
                                    <button class="btn btn-link collapsed fs-15" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        <span><span class="text-color pr-2 fs-16">2.</span> Describe what you’ve tried</span>
                                        <i class="la la-angle-down collapse-icon"></i>
                                    </button>
                                </div>
                                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                                    <div class="card-body">
                                        <p class="fs-14 lh-22 text-black-50">
                                            Show what you’ve tried and tell us what you found (on this site or elsewhere) and why it didn’t meet your needs. You can get better answers when you provide research.
                                        </p>
                                    </div>
                                </div>
                            </div><!-- end card -->
                            <div class="card">
                                <div class="card-header" id="headingThree">
                                    <button class="btn btn-link collapsed fs-15" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        <span><span class="text-color pr-2 fs-16">3.</span> Show some code</span>
                                        <i class="la la-angle-down collapse-icon"></i>
                                    </button>
                                </div>
                                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                                    <div class="card-body">
                                       <p class="fs-14 lh-22 text-black-50">
                                           When appropriate, share the minimum amount of code others need to reproduce your problem (also called a
                                           <a href="#" class="text-color hover-underline">minimum</a>, <a href="#" class="text-color hover-underline">reproducible example</a>)
                                       </p>
                                    </div>
                                </div>
                            </div><!-- end card -->
                        </div><!-- end accordion -->
                    </div><!-- end card -->
                    <div id="accordion-two" class="generic-accordion">
                        <div class="card mb-3">
                            <div class="card-header" id="headingFour">
                                <button class="btn btn-link collapsed fs-15" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    <span>Have a non-programming question?</span>
                                    <i class="la la-angle-down collapse-icon"></i>
                                </button>
                            </div>
                            <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion-two">
                                <div class="card-body">
                                    <p class="fs-14 lh-22 text-black-50 pb-2">
                                        <a class="text-color hover-underline d-block" href="#" target="_blank">Super user</a>
                                        Troubleshooting hardware and software issues
                                    </p>
                                    <p class="fs-14 lh-22 text-black-50 pb-2">
                                        <a class="text-color hover-underline d-block" href="#" target="_blank">Software engineering</a>
                                        For software development methods and process questions
                                    </p>
                                    <p class="fs-14 lh-22 text-black-50 pb-2">
                                        <a class="text-color hover-underline d-block" href="#" target="_blank">Hardware recommendations</a>
                                    </p>
                                    <p class="fs-14 lh-22 text-black-50 pb-2">
                                        <a class="text-color hover-underline d-block" href="#" target="_blank">Software recommendations</a>
                                    </p>
                                    <p class="fs-14 lh-22 text-black-50">Ask questions about the site on <a class="text-color hover-underline" href="#" target="_blank">meta</a></p>
                                </div>
                            </div>
                        </div><!-- end card -->
                        <div class="card">
                            <div class="card-header" id="headingFive">
                                <button class="btn btn-link collapsed fs-15" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                    <span>More helpful links</span>
                                    <i class="la la-angle-down collapse-icon"></i>
                                </button>
                            </div>
                            <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#accordion-two">
                                <div class="card-body">
                                    <p class="fs-14 lh-22 text-black-50 pb-2">
                                        Find more information about <a class="text-color hover-underline" href="#" target="_blank">how to ask a good question here</a>
                                    </p>
                                    <p class="fs-14 lh-22 text-black-50">
                                        Visit the <a class="text-color hover-underline" href="#" target="_blank">help center</a>
                                    </p>
                                </div>
                            </div>
                        </div><!-- end card -->

                    </div><!-- end accordion -->
                </div><!-- end sidebar -->
            </div><!-- end col-lg-4 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
<?php taoh_get_footer(); ?>
