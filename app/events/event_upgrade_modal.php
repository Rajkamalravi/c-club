<div class="modal sponsorship-option fade" id="upgradeModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="upgradeModalLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-lg bg-white" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white" style="border: none;">
                <div class="w-100">
                    <button type="button" class="btn pull-right" data-dismiss="modal" aria-label="Close">
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6153 2.22013C13.1228 1.71256 13.1228 0.888255 12.6153 0.380681C12.1077 -0.126894 11.2834 -0.126894 10.7758 0.380681L6.5 4.66055L2.22013 0.384741C1.71256 -0.122833 0.888255 -0.122833 0.380681 0.384741C-0.126894 0.892316 -0.126894 1.71662 0.380681 2.22419L4.66055 6.5L0.384742 10.7799C-0.122833 11.2874 -0.122833 12.1117 0.384742 12.6193C0.892316 13.1269 1.71662 13.1269 2.22419 12.6193L6.5 8.33945L10.7799 12.6153C11.2874 13.1228 12.1117 13.1228 12.6193 12.6153C13.1269 12.1077 13.1269 11.2834 12.6193 10.7758L8.33945 6.5L12.6153 2.22013Z"
                                  fill="#D3D3D3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="modal-body">
                <div id="upgradeCards" class="d-flex">
                    <!-- Upgrade cards will be dynamically inserted here -->
                </div>
            </div>

            <div class="modal-footer py-0" style="border: none;">
                <svg class="my-0" width="347" viewBox="0 0 347 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="174.5" cy="55.5" r="55.5" fill="#C0C0C0"/>
                    <circle cx="291.5" cy="55.5" r="55.5" fill="#FFC107"/>
                    <circle cx="55.5" cy="56.5" r="55.5" fill="#FFC97D"/>
                    <circle cx="174" cy="56" r="27" fill="#F0F3F3"/>
                    <circle cx="291" cy="56" r="27" fill="#FFEA8F"/>
                    <circle cx="55" cy="57" r="27" fill="#FFE6C3"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<?php $um_encodeCurrentUrl = encrypt_url_safe(getCurrentUrl()); ?>
<script>window._um_encodeCurrentUrl = "<?php echo $um_encodeCurrentUrl ?? ''; ?>";</script>
<script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/events/js/event-upgrade-modal.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
