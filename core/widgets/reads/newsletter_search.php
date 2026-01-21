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
        <h4 class="session_title"><span>SEARCH NEWSLETTER</span></h4>
        <!-- <div class="divider"><span></span></div> -->
        <form action="<?php echo TAOH_NEWSLETTER_URL; ?>/search" method="get" class="pt-2">
            <div class="form-group mb-0">
                <input class="form-control form--control form--control-bg-gray" value="<?php echo @$_GET['q']; ?>" type="text" name="q" placeholder="Type your search words...">

                <button class="form-btn" type="submit"><i class="la la-search"></i></button>
            </div>
        </form>
    </div>
</div><!-- end card -->
