<?php
/**
 * RSVP Export - Wrapper for consolidated exports
 * @deprecated Use includes/exports.php with export_type=rsvp
 */
require_once __DIR__ . '/includes/exports.php';
export_handler('rsvp');
