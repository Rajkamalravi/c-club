<?php
/**
 * Events V2 - Page Footer
 *
 * Footer and JavaScript includes
 */
?>
<!-- Footer -->
<footer class="ev2-footer mt-auto py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-0 text-muted">
                    &copy; <?php echo date('Y'); ?> <?php echo TAOH_SITE_NAME_SLUG; ?>. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="<?php echo TAOH_SITE_URL_ROOT; ?>/privacy" class="text-muted me-3">Privacy Policy</a>
                <a href="<?php echo TAOH_SITE_URL_ROOT; ?>/terms" class="text-muted">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5.3.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<!-- Events V2 JavaScript -->
<script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/js/events-v2/utils/helpers.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
<script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/js/events-v2/utils/api.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
<script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/js/events-v2/main.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>

<?php if (isset($page_js)): ?>
<script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/js/events-v2/<?php echo $page_js; ?>.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
<?php endif; ?>

</body>
</html>
