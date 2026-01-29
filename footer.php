<!-- Optional JavaScript _____________________________  -->

<div class="dark-con">
    <div class="container pt-83 pb-100">
        <div class="d-flex justify-content-center mb-18">
				<?php
				if(isset($_SESSION[APP_PARENT]) && isset($_SESSION[APP_PARENT]['site_name_logo'])) {

				$logo_parent = $_SESSION[APP_PARENT]['site_name_logo'];


				?>
				<a class="" href="<?php echo APP_URL; ?>">
					<img src="<?php echo SITE_URL;?>/images/tables_im_sq.png" width="50" alt="Tables™ Logo">
				</a>
				<svg style="margin-top:12px;" xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="red">
				  <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5
						   2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09
						   C13.09 3.81 14.76 3 16.5 3
						   19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
				</svg>

				<a class="" href="<?php echo $_SESSION[APP_PARENT]['site_url']; ?>">
					<img src="<?php echo $logo_parent;?>" width="50" alt="<?php echo $_SESSION[APP_PARENT]['site_name_slug']; ?>">
				</a>
				<?php } else { ?>
				<a class="" href="<?php echo APP_URL; ?>">
					<img src="<?php echo SITE_URL;?>/images/tables_im.png" width="200" alt="Tables™ Logo">
				</a>
				<?php }
				?>
        </div>
        <p class="mb-38 text-16 text-center">Instant professional roundtables for meaningful connections</p>

        <nav class="footer-nav">
            <ul>
                <li><a href="<?php echo APP_URL; ?>">Home</a></li>
                <li><a href="<?php echo APP_URL; ?>/about">About</a></li>
                <?php
                if ( ! LANDING ){
                ?>
                <li><a href="<?php echo APP_URL;?>/tables">Tables</a></li>
				<?php if (isAuthenticated()){ ?>
                <li><a href="<?php echo APP_URL;?>/create">Create</a></li>

				<?php } ?>

                <?php
                }

                ?>
                <li><a href="<?php echo defined('PRIVACY_URL') ? PRIVACY_URL : 'https://tao.ai/privacy.php'; ?>">Privacy</a></li>
                <li><a href="<?php echo defined('TERMS_URL') ? TERMS_URL : 'https://tao.ai/terms.php'; ?>">Terms</a></li>
            </ul>
        </nav>

        <hr class="divider">

        <p class="text-center text-16 mb-12">© <?php echo date('Y')?> Tables™ · Built for networking that matters</p>
        <p class="text-center text-16">Made with passion for professional growth</p>

    </div>
</div>

<?php

// Flush output buffer if it was started in header.php
if (ob_get_level()) {
    ob_end_flush();
}
?>