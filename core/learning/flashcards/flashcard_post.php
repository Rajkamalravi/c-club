<?php
taoh_get_header();
// https://opslogy.com/scripts/code.php
//$url = TAOH_OPS_PREFIX."/scripts/code.php";
//$response = json_decode(taoh_url_get( $url ), true);
//if ( ! isset( $response[ 'output' ] ) || ! isset( $response[ 'output' ] ) || $_GET[ 'q' ] != $response[ 'output' ] ) taoh_redirect(TAOH_SITE_URL_ROOT."/reads");

$conttoken = taoh_parse_url(3);
if(isset( $conttoken )){
    $taoh_call = "core.content.get";
    $taoh_vals = array(
        'mod'=>'core',
        'token'=>taoh_get_dummy_token(1),
        'ops'=>'detail',
        'type'=>'flash',
        'conttoken'=>$conttoken,
        //'cfcc1d'=> 1, //cfcache newly added
    );
    // $cache_name = $taoh_call.'_flash_detail_' . hash('sha256', $taoh_call . serialize($taoh_vals));
    // $taoh_vals[ 'cfcache' ] = $cache_name;
    // $taoh_vals[ 'cache_name' ] = $cache_name;
    // $taoh_vals[ 'cache' ] = array ( "name" => $cache_name );
    ksort($taoh_vals);

    $response = json_decode(taoh_apicall_get($taoh_call, $taoh_vals), true);
    if(isset($response['output']['profile_pretag'])){
        $pretag = explode(':', $response['output']['profile_pretag']);
    }
}

$page = ( isset( $conttoken ) && $conttoken )? 'Edit':'Create';

?>
<!-- Text editor dependancy -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-te/1.4.0/jquery-te.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-te/1.4.0/jquery-te.min.js"></script>

<!-- Image uploader dependancy -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/filepond/4.30.4/filepond.min.css">
<script src="https://cdn.jsdelivr.net/npm/filepond/dist/filepond.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-filepond/filepond.jquery.js"></script>


<section class="hero-area bg-white shadow-sm overflow-hidden">
    <span class="stroke-shape stroke-shape-1"></span>
    <span class="stroke-shape stroke-shape-2"></span>
    <span class="stroke-shape stroke-shape-3"></span>
    <span class="stroke-shape stroke-shape-4"></span>
    <span class="stroke-shape stroke-shape-5"></span>
    <span class="stroke-shape stroke-shape-6"></span>
    <div class="container">
        <div class="hero-content pt-80px pb-80px">
            <h2 class="section-title"><?php echo $page; ?> flashcard</h2>
            <svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 550 125"><defs><style>.cls-3,.cls-6,.cls-7{fill:none;stroke-miterlimit:10}.cls-3{stroke:#3cb1c6}.cls-4{fill:#fff}.cls-5{fill:#f9b851}.cls-6{stroke:#f48024}.cls-7{stroke:#bbc0c4;stroke-dasharray:5}</style></defs><g opacity=".5"><path fill="#cceaff" d="M232.73 73L217.5 92.89V73h-16V26h64v47H232.73z"></path><path class="cls-3" d="M205 47V20.5h14.5M255.5 66.5h-18.51L221 86.06V66.5h-16V58M231.5 20.5H270v46h-6.5"></path><path class="cls-4" d="M222.5 40.79v7.3l8.16 7.29 15.84-14.21V33.3l-15.84 14.31-8.16-6.82z"></path><path class="cls-5" d="M374.36 76l-9.29 11.42-.26-11.42h-5.31V49h45v27h-30.14z"></path><path class="cls-6" d="M379.5 46.5H402V55M402 62v11.5H372.02L363 84.92V73.5h-6v-27h11.5"></path><path class="cls-4" d="M377.5 59h5v5h-5zM367.5 59h5v5h-5zM387.5 59h5v5h-5z"></path><path class="cls-5" d="M180.25 67l5.16 6.77.14-6.77h2.95V51h-25v16h16.75z"></path><path class="cls-6" d="M170.5 65.5h10.73l5.77 6.72V65.5h4V59M170.5 49.5H165V65M191 53v-3.5h-15.5"></path><path class="cls-4" transform="rotate(-180 177 58.5)" d="M175.5 57h3v3h-3z"></path><path class="cls-4" transform="rotate(-180 183 58.5)" d="M181.5 57h3v3h-3z"></path><path class="cls-4" transform="rotate(-180 171 58.5)" d="M169.5 57h3v3h-3z"></path><path class="cls-5" d="M486.95 77l15.55 19.89V77h16V30h-65v47H486.95z"></path><path class="cls-6" d="M515 51V24.5h-14.5M464.5 70.5h18.51L499 90.06V70.5h16V62M488.5 24.5H450v46h6.5"></path><path class="cls-4" d="M470.5 44.71V52l8.16 7.3 15.84-14.21v-7.87l-15.84 14.3-8.16-6.81z"></path><path class="cls-3" d="M533.5 34.5h-2.14L527 40.34V34.5h-3v-12h4.5M545 30v5.5h-7.5M533.5 22.5H545V26M534 28.5h2v2h-2z"></path><path class="cls-3" d="M529 28.5h2v2h-2zM539 28.5h2v2h-2zM280 30V16.5h7.5M305.5 39.5H296l-8 10v-10h-8V35M293.5 15.5H313v24h-3.5"></path><path class="cls-3" d="M289 26.52v3.65l4.08 3.65 7.92-7.11v-3.93l-7.92 7.15-4.08-3.41z"></path><path class="cls-6" d="M124.5 27.5h1.41l3.09 4.23V27.5h2v-10h-2.5M115 23v4.5h6.5M124.5 17.5H115V21"></path><path class="cls-6" transform="rotate(-180 123 22.5)" d="M122 21.5h2v2h-2z"></path><path class="cls-6" transform="rotate(-180 127 22.5)" d="M126 21.5h2v2h-2z"></path><path class="cls-6" transform="rotate(-180 119 22.5)" d="M118 21.5h2v2h-2z"></path><path class="cls-7" d="M188.5 78.19s4.69 18.91 27.5 16.28M131 32.67s21.64-2 33 15M271.12 69.19c23.92 31.24 55.21 35.18 90.64 19.3M402 43.5c.68-6.28 19.53-30.13 45.26-21M503.45 98.26c5.25.1 37.8-10.84 23.09-54.76M272.34 66.5s10.51 0 15.16-13.88"></path><path class="cls-7" d="M97.22 79.67c14.33-2.39 42.51-3.18 55.87 12.65s58.91 20.93 64.49 3"></path></g><path class="cls-3" d="M59.15 84.6v-1.04M71.6 84.81v3.73M53.39 82.51l-3.96-.54 2.96-21.6 27.37 3.75-2.96 21.6-20.05-2.75"></path><path class="cls-3" d="M50.22 76.21l-2.31-.32a2 2 0 01-1.73-2.28l.63-4.61a2 2 0 012.3-1.74l2.3.31zM79.88 80.27L77.59 80l1.18-8.64 2.32.32a2 2 0 011.73 2.27l-.64 4.62a2 2 0 01-2.3 1.7zM66.6 79.11l-.2 1.44M69.48 79.5l-.2 1.44M63.72 78.71l-.2 1.44M60.84 78.32l-.2 1.44M57.96 77.93l-.2 1.44"></path><ellipse class="cls-3" cx="66.45" cy="70.5" rx="2.87" ry="2.94" transform="rotate(-82.2 66.45 70.504)"></ellipse><ellipse class="cls-3" cx="56.76" cy="69.18" rx="2.87" ry="2.94" transform="rotate(-82.2 56.758 69.175)"></ellipse><path class="cls-3" d="M55.42 105.96v7.46M72.84 105.96v7.46M76.57 96.01v1.24M66.62 96.01v1.24M54.18 95.38h7.47v4.98h-7.47z"></path><path class="cls-3" d="M81.55 100.98v4.36H46.71V87.92h5.28M57.43 79.39l-1.98 3"></path><path class="cls-3" d="M54.31 90.61c-2.34-1.55-3.17-4.43-1.85-6.43l1.8-2.73 8.49 5.61L61 89.78c-1.37 2.01-4.34 2.38-6.69.83zM62.27 87.92h19.28v9.33"></path><path class="cls-3" d="M88.32 96.44l1.28-4.17a4.4 4.4 0 00-4.45-4.35h-5.47M79.29 104.84l2.68-3.44M82.72 107.53l2.69-3.44M88.32 96.83c2.22 1.73 2.81 4.67 1.33 6.56l-2 2.57-8-6.27 2-2.57c1.47-1.89 4.46-2.02 6.67-.29zM55.3 91.15a6.72 6.72 0 01-1.74.35H46.5"></path><path class="cls-5" d="M40.82 46.24l7.96 10 .23-10h4.55V22.6H14.98v23.64h25.84z"></path><path class="cls-6" d="M28.67 43.13h14.69l8.33 10.53V43.13h6.22V33.8M26.18 19.49H18.1v24.26M56.67 25.09v-5.6H33.65"></path><path class="cls-4" d="M34.17 33.8a5.9 5.9 0 01.34-2.23 4.5 4.5 0 011.24-1.64A6.41 6.41 0 0037 28.62a2.06 2.06 0 00.3-1.07c0-1.12-.52-1.69-1.56-1.69a1.56 1.56 0 00-1.18.46 1.74 1.74 0 00-.46 1.25h-2.9a3.84 3.84 0 011.23-3 4.82 4.82 0 013.31-1.08 4.84 4.84 0 013.29 1 3.64 3.64 0 011.17 2.89 3.57 3.57 0 01-.43 1.62 6.57 6.57 0 01-1.33 1.68l-.81.77a2.78 2.78 0 00-.87 1.71v.61zM34 36.85a1.34 1.34 0 01.43-1 1.64 1.64 0 012.17 0 1.34 1.34 0 01.43 1 1.32 1.32 0 01-.42 1 1.7 1.7 0 01-2.19 0 1.32 1.32 0 01-.42-1z"></path><path class="cls-3" d="M79.06 124H66.62v-4.21a6.45 6.45 0 015-6.42 6.29 6.29 0 017.47 6.33zM75.33 122.13v1.25M70.35 122.13v1.25M61.64 124H49.2v-4.21a6.47 6.47 0 015-6.42 6.29 6.29 0 017.47 6.33zM57.91 122.13v1.25M52.93 122.13v1.25"></path></svg>
        </div><!-- end hero-content -->
    </div><!-- end container -->
</section>

<section class="question-area pt-80px pb-40px">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-item">
                    <form method="post" class="card-body" action="<?php echo TAOH_ACTION_URL .'/flashcard'; ?>">
                        <div class="hidden">
                          <input type="hidden" name="action" value="save">
                          <?php if( ( isset( $conttoken ) ) && $conttoken ) { ?>
                            <input type="hidden" name="conttoken" value="<?php echo $conttoken ;?>">
                          <?php } ?>
                        </div>
                        <div class="input-box">
                            <label class="fs-14 text-black fw-medium mb-0">Title</label>
                            <div class="form-group">
                                <input class="form-control form--control" type="text" name="title" value="<?php echo $response['output']['title']; ?>" placeholder="e.g. Is there an R function for finding the index of an element in a vector?">
                            </div>
                        </div><!-- end input-box -->
                        <div class="input-box">
                            <label class="fs-14 text-black fw-medium mb-0">Details</label>
                            <div class="form-group">
                                <textarea class="" id="summernote" name="description" rows="10" cols="80"><?php echo $response['output']['description']; ?></textarea>
                            </div>
                        </div><!-- end input-box -->
                        <div class="input-box">
                            <label class="fs-14 text-black fw-medium">Image Links</label>
                            <input class="form-control form--control" name="image[]" type="text" value="<?php echo $response['output']['image'][0]; ?>" placeholder="Image URL Here">
                            <!-- <input type="file" class="filepond" name="filepond"  multiple  data-allow-reorder="true" data-max-file-size="3MB" data-max-files="3"> -->
                        </div> <br>
                        <div class="input-box">
                            <label class="fs-14 text-black fw-medium">External Link</label>
                            <input class="form-control form--control" name="media" type="text" value="<?php echo $response['output']['media']; ?>" placeholder="External Link Here">
                        </div> <br>

                        <div class="input-box">
                            <label class="fs-14 text-black fw-medium">Profile Pre Tag</label>
                            <input class="form-control form--control" name="profile_pretag" type="text" value="Inspired By: <?php echo $pretag[1]; ?>" placeholder="Profile Pre Tag">
                        </div> <br>
                        <div class="input-box">
                            <label class="fs-14 text-black fw-medium">First Name</label>
                            <input class="form-control form--control" name="fname" type="text" value="<?php echo $response['output']['fname']; ?>" placeholder="First Name">
                        </div> <br>
                        <div class="input-box">
                            <label class="fs-14 text-black fw-medium">Last Name</label>
                            <input class="form-control form--control" name="lname" type="text" value="<?php echo $response['output']['lname']; ?>" placeholder="Last Name">
                        </div> <br>
                        <div class="input-box">
                            <label class="fs-14 text-black fw-medium">Profile Image URL</label>
                            <input class="form-control form--control" name="avatar" type="text" value="<?php echo $response['output']['author']['avatar']; ?>" placeholder="Profile Image URL">
                        </div> <br>
                        <div class="input-box">
                            <label class="fs-14 text-black fw-medium">Profile Link</label>
                            <input class="form-control form--control" name="profile_link" type="text" value="<?php echo $response['output']['profile_link']; ?>" placeholder="Profile Link">
                        </div> <br>

                        <div class="input-box">
                            <label class="fs-14 text-black fw-medium">Post Flashcard Text</label>
                            <input class="form-control form--control" name="postblog_text" type="text" value="<?php echo $response['output']['postblog_text']; ?>" placeholder="Post Flashcard Text">
                        </div> <br>
                        <div class="input-box">
                            <label class="fs-14 text-black fw-medium">Post Flashcard Link</label>
                            <input class="form-control form--control" name="posttaoh_blog_link" type="text" value="<?php echo $response['output']['posttaoh_blog_link']; ?>" placeholder="Post Flashcard Link">
                        </div> <br>
                        <div class="input-box">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <label class="fs-14 text-black fw-medium mb-0">Categories</label>
                                    <p class="fs-13 pb-3 lh-20">Add up to 5 Categories to describe what your blog is about:</p>
                                </div>
                            </div>
                            <div class="form-group">
                              <input id="blogCategorySelect" class="input-tags input--tags" type="text" name="category[]" value="<?php echo $response['output']['category'][0]; ?>">
                                <script>blogCategorySelect()</script>
                            </div>
                        </div><!-- end input-box -->

                        <div class="input-box pt-2">
                            <div class="btn-box">
                                <button type="submit" class="btn theme-btn">Publish</button>
                            </div>
                        </div>
                    </form>
                </div><!-- end card -->
            </div><!-- end col-lg-8 -->
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
<style media="screen">
.jqte {
  margin: 0;
  -webkit-box-shadow: none;
  -moz-box-shadow: none;
  box-shadow: none;
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
  border-color: rgba(128, 137, 150, 0.2);
}
.jqte_toolbar {
    background-color: rgba(128, 137, 150, 0.1);
    border-bottom-color: rgba(128, 137, 150, 0.2);
}
.jqte_tool {
    padding: 3px;
}
.jqte_tool.jqte_tool_1 .jqte_tool_label {
    width: 80px;
    height: 24px;
    padding: 0;
    line-height: 24px;
}
.jqte_tool.jqte_tool_1 .jqte_tool_text {
    font-weight: 500;
}
.jqte_editor, .jqte_source {
    min-height: 160px;
}
#fr-logo{
    display:none;
}
</style>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/4.1.1/js/froala_editor.pkgd.min.js" integrity="sha512-FRYRrCKas1htXdUOUGPSL2YmY/QoaSvHwA1fTA8/JEjbKrI5Wy4c/3y1c8WUdmcDgePJ+64eUmC1Z64L+STPog==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/4.1.1/css/froala_editor.pkgd.min.css" integrity="sha512-uE6p2c2S0HynTamtQwckxUdpqmVxpPc/SCiaNEMEy0FGONzjnDH5f6nmGp7YkjQI/enypmyb1vGylKAO3rj4hA==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
<script src="https://cdn.ckeditor.com/4.15.1/full/ckeditor.js"></script>
<script type="text/javascript">
    //var editor = $("#summernote").trumbowyg();
    //var editor = new FroalaEditor('#summernote');
    //$( 'textarea.summernote' ).ckeditor();
    CKEDITOR.replace( 'description' );
</script>
<?php taoh_get_footer(); ?>
