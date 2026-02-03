<?php
taoh_get_header(); 

?>
<section class="question-area pt-40px pb-40px">
    <div class="container">
        <div class="row">
            <div class="card col-lg-8 z-depth-5 mb-4">
				<div class="card-body">
					<div class="d-flex justify-content-between">
						<div>
							<h3>Bulk Upload</h3>
						</div>
					</div>
					<hr>
					<form class="mt-3" method="post" enctype="multipart/form-data" action="<?php echo TAOH_ACTION_URL .'/bulk'; ?>">
						<div class="row">
                            <div class="mb-12 col-md-12">
								<label class="form-label text-black fw-medium">File <span style="color:red;">*</span></label>
								<input type="file" name="userfile" id="file" class="form-control" required>
                                <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
							</div>
                        </div>
                        <div class="row pt-4">
                                <div class="mb-12 col-md-12">
                                    <div class="d-flex">
                                        
                                    <label class="form-label text-black fw-medium">Type &nbsp;</label>
                                    <select style="margin-left:20px" name="type" id="type">
                                        <option value="reads">Blogs</option>
                                        <option value="flash">Flashcards</option>
                                    </select>
                                    </div>
                                </div>
                        </div>
                        <div class="row pt-4">
                                <div class="mb-12 col-md-12">
                                    <div class="d-flex">
                                        
                                    <label class="form-label text-black fw-medium">Visibility &nbsp;</label>
                                    <select style="margin-left:20px" name="global" id="global">
                                        <option value="1">Global</option>
                                        <option value="0">Private</option>
                                    </select>
                                    </div>
                                </div>
                        </div>
                        <div class="row pt-4">
                            <div class="mb-3 mt-3 col-md-6">
								<div class="d-flex">
                                   
                                    <button type="submit" class="ml-2 btn btn-primary btn-sm">Submit</button>
                                    
                                </div>
							</div>
                        </div>
					</form>
				</div>
            </div>
        </div><!-- end row -->
    </div><!-- end container -->
</section>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
<style media="screen">
.z-depth-5 {
-webkit-box-shadow: 0 27px 24px 0 rgba(0,0,0,0.2), 0 40px 77px 0 rgba(0,0,0,0.22) !important;
border-radius: 1.25rem;
/* box-shadow: 0 27px 24px 0 rgba(0,0,0,0.2), 0 40px 77px 0 rgba(0,0,0,0.22) !important; */
}
</style>
