<?php

/**
 * Plugin Name: DB Encryption Plugin
 * Description: Плъгин за криптиране на базата данни в WordPress.
 * Version: 1.0
 * Author: Boris
 */

if (!defined('ABSPATH')) {
    exit;
}


require_once plugin_dir_path(__FILE__) . 'EncryptionManager.php';
require_once plugin_dir_path(__FILE__) . 'admin-settings.php';
require_once plugin_dir_path(__FILE__) . 'database-integration.php';
require_once plugin_dir_path(__FILE__) . 'logging.php';


function db_encryption_plugin_init() {
   
    global $is_encrypted;


    $is_encrypted = get_option('db_encryption_status', true);


    if ($is_encrypted === false) {
        add_option('db_encryption_status', false);
        $is_encrypted = false;
    }

    if (isset($_POST['toggle_encryption'])) {
        toggle_db_encryption();
    }
}

add_action('init', 'db_encryption_plugin_init');


function toggle_db_encryption() {
    $encryption_key = get_option('db_encryption_key');
    $is_encrypted = false;


    $encryption_manager = new EncryptionManager($encryption_key);

    if ($encryption_key) {
        if ($is_encrypted) {

            $encryption_manager->decryptData(); 
            update_option('db_encryption_status', false);
            echo '<div class="updated"><p>Database has been decrypted.</p></div>';
        } else {

            $encryption_manager->encryptData(); 
            update_option('db_encryption_status', true);
            echo '<div class="updated"><p>Database has been encrypted.</p></div>';
            echo 'suck';
        }
    }
}
