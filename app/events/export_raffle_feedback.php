<?php
/**
 * Raffle Feedback Export - Wrapper for consolidated exports
 * @deprecated Use includes/exports.php with export_type=raffle_feedback
 */
require_once __DIR__ . '/includes/exports.php';
export_handler('raffle_feedback');
