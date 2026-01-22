<?php
/**
 * Consolidated Modal Templates
 * Contains all event-related modal components
 *
 * Usage: Include this file and call the appropriate render function
 *
 * Available functions:
 * - render_upgrade_modal() - Ticket upgrade modal
 * - render_video_modal() - Video player modal
 * - render_sponsor_info_modal() - Sponsor details modal
 *
 * Replaces:
 * - event_upgrade_modal.php
 * - event_video_modal.php
 * - sponsor_details_modal.php
 * - chat-modal.php (removed - content was duplicated in chat.php)
 */

/**
 * Render the upgrade modal
 */
function render_upgrade_modal() {
    include __DIR__ . '/../event_upgrade_modal.php';
}

/**
 * Render the video modal
 */
function render_video_modal() {
    include __DIR__ . '/../event_video_modal.php';
}

/**
 * Render the sponsor info modal
 */
function render_sponsor_info_modal() {
    include __DIR__ . '/../sponsor_details_modal.php';
}

/**
 * Common close button SVG used in modals
 * @return string SVG HTML
 */
function get_modal_close_svg() {
    return '<svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12.6153 2.22013C13.1228 1.71256 13.1228 0.888255 12.6153 0.380681C12.1077 -0.126894 11.2834 -0.126894 10.7758 0.380681L6.5 4.66055L2.22013 0.384741C1.71256 -0.122833 0.888255 -0.122833 0.380681 0.384741C-0.126894 0.892316 -0.126894 1.71662 0.380681 2.22419L4.66055 6.5L0.384742 10.7799C-0.122833 11.2874 -0.122833 12.1117 0.384742 12.6193C0.892316 13.1269 1.71662 13.1269 2.22419 12.6193L6.5 8.33945L10.7799 12.6153C11.2874 13.1228 12.1117 13.1228 12.6193 12.6153C13.1269 12.1077 13.1269 11.2834 12.6193 10.7758L8.33945 6.5L12.6153 2.22013Z" fill="#D3D3D3"/>
    </svg>';
}

/**
 * Render all event modals at once
 * Useful for including in footer
 */
function render_all_event_modals() {
    render_upgrade_modal();
    render_video_modal();
}
