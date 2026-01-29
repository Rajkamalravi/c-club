<?php
taoh_get_header();
$conttoken = taoh_parse_url(2);
$page = 'Create';
if(isset( $conttoken ) && $conttoken != 'stlo'){
    $page = 'Edit';
    $url = "core.content.get";
    $taoh_vals = array(
        'token'=> taoh_get_dummy_token(1),
        'mod' => 'core',
        'ops' => 'detail',
        'type' => 'reads',
        'conttoken' => $conttoken,
        //'cfcc1d'=> 1, //cfcache newly added
        //'cache' => array ( "name" => taoh_p2us('core.content').'_'.$conttoken.'_detail', "ttl" => 3600 ) ,
    );
    // $cache_name = $url.'_detail_' . hash('sha256', $url . serialize($taoh_vals));
    // $taoh_vals[ 'cfcache' ] = $cache_name;
    // $taoh_vals[ 'cache_name' ] = $cache_name;
    // $taoh_vals[ 'cache' ] = array ( "name" => $cache_name, 'ttl' => 7200 );
    ksort($taoh_vals);

    //echo $taoh_vals['cache']['name'];exit();
    //print_r($taoh_vals);taoh_exit();
    //echo taoh_apicall_get_debug($url, $taoh_vals);taoh_exit();
    $response = json_decode(taoh_apicall_get($url, $taoh_vals), true);
    $items_array = $response['output']['tags'];
    $items = json_encode($items_array);
    //echo'<pre>';print_r($response);die();
}
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
							<h3><?php echo $page; ?> Blog</h3>
						</div>
					</div>
					<hr>
					<form class="mt-3" id="blog_post_form" method="post" action="<?php echo TAOH_ACTION_URL .'/blog'; ?>">
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
								<label class="form-label text-black fw-medium">Blog Title <span style="color:red;">*</span></label>
								<input name="title" type="text" value="<?php echo htmlspecialchars($response['output']['title']); ?>" class="form-control" required>
							</div>
                            <div class="mb-12 col-md-12">
								<label class="form-label text-black fw-medium">Blog Subtitle</label>
								<input name="subtitle" type="text" value="<?php echo htmlspecialchars($response['output']['subtitle']); ?>" class="form-control">
							</div>
                            <div class="mb-12 col-md-12">
								<label class="form-label text-black fw-medium">Recipe Title</label>
								<input name="recipe_title" type="text" value="<?php echo htmlspecialchars($response['output']['recipe_title']); ?>" class="form-control">
							</div>
							<div class="mb-3 col-md-12">
								<label  class="form-label text-black fw-medium">Description <span style="color:red;">*</span></label>
								<textarea class="summernote" id="summernote" name="description" rows="10" cols="80"><?php echo $response['output']['description']; ?></textarea>
							</div>
                            <div class="mb-12 col-md-12 fixme">
								<label for="skills" class="form-label text-black fw-medium">Blog Excerpt ( For Social media )</label>
								<textarea class="form-control" name="excerpt" rows="3" type="text"><?php echo $response['output']['excerpt']; ?></textarea>
							</div>
                            <div class="mb-3 col-md-6">
								<label for="skills" class="skills-field form-label text-black fw-medium">Visiblity <span style="color:red;">*</span></label>
                                    <select class="form-control" name="visiblity" required>
                                        <option value=""  disabled>Select Visiblity</option>
                                        <option <?php echo ($response['output']['visiblity'] == "public" || $response['output']['visiblity'] == '') ?'selected': '';?> value="public">Public</option>
                                        <option <?php echo ($response['output']['visiblity'] == "login") ?'selected': '';?> value="login">Login Required</option>
                                        <option <?php echo ($response['output']['visiblity'] == "password") ?'selected': '';?> value="password">Password Protected</option>
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
                                <input id="blogCategorySelect" value="<?php echo $response['output']['category'][0]; ?>" class="input-tags input--tags" type="text" name="category[]" required>
                                <script>blogCategorySelect()</script>
							</div>
                            <div class="mb-3 col-md-6" id="tags_add">
                                <label class="form-label text-black fw-medium">Tags<br>
                                <input type="text" name="tags[]" placeholder="Enter Tag">
                                <button type="button" onclick="addInput()">Add More</button><br>
							</div>
                            <div class="mb-3 col-md-6">
								<label for="skills" class="form-label text-black fw-medium">Blog Type <span style="color:red;">*</span></label>
                                <select class="form-control" name="blog_type" required>
                                    <option <?php echo ($response['output']['blog_type'] == "internal" || $response['output']['blog_type'] == '') ?'selected': '';?> value="internal">Internal</option>
                                    <option <?php echo ($response['output']['blog_type'] == "external") ?'selected': '';?> value="external" >External</option>
                                </select>
							</div>
                            <div class="mb-3 col-md-6">
								<label for="skills" class="form-label text-black fw-medium">Blog External Link</label>
								<input class="form-control" value="<?php echo $response['output']['media_link']; ?>" name="media_link" type="text">
							</div>
                            <div class="mb-3 col-md-6">
								<label for="skills" class="form-label text-black fw-medium">Featured Media URL</label>
								<input class="form-control" value="<?php echo $response['output']['media_url']; ?>" name="media_url" type="text">
							</div>
                            <div class="mb-3 col-md-6">
								<label for="skills" class="form-label text-black fw-medium">Featured Media Type</label>
                                <select class="form-control" name="media_type">
                                    <option value="" disabled>Select Media Type</option>
                                    <option <?php echo ($response['output']['media_type'] == "image" || $response['output']['media_type'] == '') ?'selected': '';?> value="image">Image</option>
                                    <option <?php echo ($response['output']['media_type'] == "youtube") ?'selected': '';?>  value="youtube">Youtube</option>
                                    <option <?php echo ($response['output']['media_type'] == "soundcloud") ?'selected': '';?> value="soundcloud">SoundCloud</option>
                                </select>
							</div>
                            <div class="mb-3 col-md-6">
								<label class="form-label text-black fw-medium">Source Name</label>
								<input name="source_name" value="<?php echo $response['output']['source_name']; ?>" type="text" class="form-control">
							</div>
                            <div class="mb-3 col-md-6">
								<label class="form-label text-black fw-medium">Source URL</label>
								<input name="source_url" value="<?php echo $response['output']['source_url']; ?>" type="text" class="form-control">
							</div>
                            <div class="mb-3 col-md-6">
								<label class="form-label text-black fw-medium">Via Name</label>
								<input name="via_name" value="<?php echo $response['output']['via_name']; ?>" type="text" class="form-control">
							</div>
                            <div class="mb-3 col-md-6">
								<label class="form-label text-black fw-medium">Via URL</label>
								<input name="via_url" value="<?php echo $response['output']['via_url']; ?>" type="text" class="form-control">
							</div>
                            <div class="mb-3 col-md-6">
								<label class="form-label text-black fw-medium">Status</label>
                                <input type="hidden" id="status_val" value="<?php echo $response['output']['status']; ?>"/>
								<select class="form-control" name="status" id="status" required onfocus="this.size=6;" onblur='this.size=6;' onfocusout='this.size=null;' onchange='this.size=6; this.blur();'>
                                    <?php
                                        if(BLOG_MAXMIUM_STATUS){
                                            for ($x = 0; $x <= BLOG_MAXMIUM_STATUS; $x++) {
                                                if(isset($response['output']['status']) && $response['output']['status'] && $x == $response['output']['status']){
                                                    echo "<option selected value='".$x."'>".$x."</option>";
                                                }else{
                                                    echo "<option value='".$x."'>".$x."</option>";
                                                }
                                            }
                                        }
                                    ?>
                                </select>
							</div>
                            <div class="mb-3 mt-5 col-md-6">
                                <input type="checkbox" id="publish" onchange="return check_enable_off()" <?php echo ($response['output']['publish'] == 'on' || $response['output']['publish'] == '') ? 'checked' : ''; ?>>
                                <input type="hidden" class="publish" name="publish" >
								<span data-toggle="tooltip" data-placement="top" title="Your post will be shared on multiple relevant partner sites for an increased response rate.">
									<label for="publish" class="form-label">Publish this Post globally</label>
								</span>

							</div>
                            <div class="mb-3 col-md-6">
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
<style media="screen">
.z-depth-5 {
-webkit-box-align: 0 27px 24px 0 rgba(0,0,0,0.2), 0 40px 77px 0 rgba(0,0,0,0.22) !important;
border-radius: 1.25rem;
/* box-shadow: 0 27px 24px 0 rgba(0,0,0,0.2), 0 40px 77px 0 rgba(0,0,0,0.22) !important; */
}
@media (min-width:320px)  {
    .fixme-css{
        position: fixed;
        top: 0;
        left: 35px;
        width: 411px;
        z-index: 1;
        background-color: white;
    }
}
@media (min-width:961px)  {
    .fixme-css{
        position: fixed;
        top: 0;
        left: 110px;
        width: 567px;
        z-index: 1;
        background-color: white;
    }
}
@media (min-width:1025px) {
    .fixme-css{
        position:fixed;
        top: 0;
        left: 156px;
        width: 780px;
        z-index: 1;
        background-color: white;
    }
}
#fr-logo{
    display:none;
}
</style>
<script type="text/javascript">
    $(document).ready(function(){
	    check_enable_off();
        <?php if(isset( $conttoken ) && $conttoken != 'stlo'){ ?>
            var items = <?php echo $items; ?>;
            initializeInputs(items);
        <?php } ?>
    });
    function check_enable_off(){
        if ($('#publish').is(":checked")) {
            $(".publish").val('on');
        }else{
            $(".publish").val('off');
        }
    }
    function addInput(value = '') {
        // Create a new input wrapper div
        var inputWrapper = document.createElement('div');

        // Create a new input element
        var newInput = document.createElement('input');
        newInput.type = 'text';
        newInput.name = 'tags[]';
        newInput.value = value;
        newInput.placeholder = 'Enter Tag';

        // Create a remove button
        var removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.innerText = 'Remove';
        removeButton.onclick = function() {
            inputWrapper.remove();
        };

        // Append input and remove button to the wrapper
        inputWrapper.appendChild(newInput);
        inputWrapper.appendChild(removeButton);

        // Append the wrapper to the form
        var form = document.getElementById('tags_add');
        form.appendChild(inputWrapper);
    }

    function initializeInputs(items) {
        items.forEach(item => addInput(item));
    }

</script>
<?php taoh_get_footer(); ?>
