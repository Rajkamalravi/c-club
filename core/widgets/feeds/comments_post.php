<?php

if(!isset($data) && isset($_POST)){
    $data = $_POST;
}
$conttoken = $data['conttoken'];
$redirect = $_SERVER['REQUEST_URI'];
if(isset($data['redirect'])){
    $redirect = $data['redirect'];
}
if ( taoh_user_is_logged_in()){
?>
<form class="command_form" id="command_form_<?php echo $conttoken; ?>">
    <input type="hidden" name="conttoken" value="<?php echo $data['conttoken'];?>">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="redirect" value="<?php echo  $redirect; ?>">
    <input type="hidden" name="conttype" value="<?php echo $data['conttype']; ?>">
    <input type="hidden" name="parentid" class="parentid" value="">
    <div class="col-md-12 py-2 mb-3" style="border: 0.5px solid #D3D3D3; border-radius: 6px;">
        <div class="d-flex justify-content-end align-items-center">
            <!-- <button type="button" class="btn">
                <svg width="8" height="9" viewBox="0 0 8 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6.024 9C5.904 8.76 5.76 8.496 5.592 8.208C5.424 7.912 5.24 7.608 5.04 7.296C4.84 6.976 4.632 6.66 4.416 6.348C4.2 6.028 3.992 5.728 3.792 5.448C3.592 5.728 3.384 6.028 3.168 6.348C2.952 6.66 2.744 6.976 2.544 7.296C2.352 7.608 2.168 7.912 1.992 8.208C1.824 8.496 1.68 8.76 1.56 9H0.276C0.636 8.296 1.052 7.58 1.524 6.852C2.004 6.124 2.512 5.376 3.048 4.608L0.384 0.684H1.728L3.78 3.78L5.808 0.684H7.14L4.524 4.56C5.068 5.336 5.58 6.092 6.06 6.828C6.54 7.564 6.964 8.288 7.332 9H6.024Z" fill="#D3D3D3"/>
                </svg>
            </button> -->
        </div>
        <div class="col-12 row d-flex align-items-center mx-auto px-0 mt-2" style="gap: 6px;">
            <div class="col-12 d-flex align-items-end mb-2" style="gap: 6px;">
                <img src="<?php echo $data['avatar']; ?>" alt="profile" style="width: 55px; height: 55px; border-radius: 50%; border: 2px solid #ddd;" />
                <input type="text" placeholder="Enter your comment" id="comment_value_<?php echo $conttoken; ?>" name="comment" class="form-control ml-2" style="border: 0 !important; border-bottom: 1px solid #D3D3D3 !important; border-radius: 0;"/>
                <div class="mb-2">
                    <button type="button" data-id="<?php echo $conttoken; ?>" data-metrics="comment_click" class="btn col ml-2 click_action save_post" style="background: #2557A7;"><a style="color: #fff;">Post</a></button>
                </div>
            </div>
            <span id="commentresponseMessage<?php echo $conttoken; ?>"></span>
        </div>
    </div>
</form>
<?php } ?>
<script>
    $('#comment_value_<?php echo $conttoken; ?>').on('keypress', function(event) {
        if (event.which === 13) { // Enter key
            event.preventDefault(); // Prevent form submission
        }
    });
</script>
