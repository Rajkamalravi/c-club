<style>
h3.session_title {
  display: flex;
  flex-direction: row;
}

h3.session_title:after {
    content: "";
    flex: 1 1;
    border-bottom: 4px solid #dc3545;
    margin: auto;
    border-radius: 10px;
}
</style>
<div class="border-bottom mb-3 card card-item">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center" style="gap: 12px;">
            <h4 class="session_title search-title-v2"><span>SEARCH BLOG</span></h4>
            <?php  if (isset($_GET['creator']) && $_GET['creator']) { ?>
            <a href="<?php echo TAOH_READS_URL; ?>/post/?post=<?php echo date('Ymd');?>"
            class="btn btn-primary post-btn-v2"
            >
                    <i class="la la-plus  "></i>&nbsp;Post
            </a>
            <?php } ?>
        </div>
        <div class="pt-3 pt-lg-4">
            <form action="<?php echo TAOH_READS_URL; ?>/search" method="get" >
                <div class="form-group mb-2 search-filter-section d-flex" style="gap: 12px;">
                    <input class="form-control form--control form--control-bg-gray mb-0" value="<?php echo @$_GET['q']; ?>" type="text" name="q" placeholder="Type your search words...">

                    <button class="btn btn-primary d-flex align-items-center rounded-pill px-md-4" type="submit"><i class="la la-search fs-20 mr-1"></i> <span class="d-none d-md-inline-block">Search</span></button>
                </div>
            </form>
        </div>
    </div>
</div><!-- end card -->
