<?php
/**
 * Events V2 - Security redirect
 *
 * Prevents direct access to the app directory
 */
header('Location: ../');
exit;
