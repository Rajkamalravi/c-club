<?php
/**
 * Raffle Entries Export - Wrapper for consolidated exports
 * @deprecated Use includes/exports.php with export_type=raffle_entries
 */
require_once __DIR__ . '/includes/exports.php';
export_handler('raffle_entries');
