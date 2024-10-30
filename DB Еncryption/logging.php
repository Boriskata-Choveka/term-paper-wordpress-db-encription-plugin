<?php

if (!defined('ABSPATH')) {
    exit;
}
function logAction($action)
{
    $logFile = plugin_dir_path(__FILE__) . 'log.txt';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $action\n", FILE_APPEND);
}
