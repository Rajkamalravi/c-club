<?php
$conttoken = $data['conttoken'];
$redirect = $_SERVER['REQUEST_URI'];
if(isset($data['redirect'])){
    $redirect = $data['redirect'];
}
if ( taoh_user_is_logged_in()){
?>
<form method="post"  class="command_form card card-item light-dark" id="command_form" onsubmit="showLoading(event)" action="<?php echo TAOH_SITE_URL_ROOT.'/actions/comments';?>">
    <input type="hidden" name="conttoken" value="<?php echo $data['conttoken'];?>">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="redirect" value="<?php echo  $redirect; ?>">
    <input type="hidden" name="conttype" value="<?php echo $data['conttype']; ?>">
    <input type="hidden" name="parentid" class="parentid" value="">
    <div class="card-body row">
        <div class="form-group col-lg-12">
            <h4 class="fs-20">Leave a <?php echo $data['label']; ?></h4>
        </div>

        <div class="form-group col-lg-12">
            <label class="fs-13 text-black lh-20"><?php echo $data['label']; ?></label>
            <textarea class="form-control form--control light-dark-card" id="comment_value" name="comment" rows="5" placeholder="Your <?php echo $data['label']; ?> here..."></textarea>
            <span id="commentresponseMessage"></span>
        </div>
        <div class="form-group col-lg-12 mb-0">
            <button data-metrics="comment_click" id="comment_submit" class="btn theme-btn click_action " type="submit">Post <?php echo $data['label']; ?> </button>
        </div>
    </div>
</form>
<?php } ?>
