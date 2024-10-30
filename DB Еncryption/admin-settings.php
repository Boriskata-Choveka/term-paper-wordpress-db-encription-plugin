<?php

if (!defined('ABSPATH')) {
    exit;
}


function db_encryption_plugin_menu() {
    add_menu_page('DB Encryption', 'DB Encryption', 'manage_options', 'db-encryption', 'db_encryption_settings_page');
}
add_action('admin_menu', 'db_encryption_plugin_menu');


function db_encryption_settings_page() {
    global $is_encrypted;

    $button_text = $is_encrypted ? 'Decrypt Database' : 'Encrypt Database';

    ?>
    
    <div class="wrap">
    <h1>Database Encryption Plugin Settings</h1>
    <form id="encryptionForm" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <?php wp_nonce_field('db_encryption_action', 'db_encryption_nonce'); ?>
        <input type="hidden" name="action" value="toggle_db_encryption">

        <table class="form-table">
            <tr valign="top">
                <th scope="row">Encryption Key</th>
                <td><input type="text" id="db_encryption_key" name="db_encryption_key" value="<?php echo esc_attr(get_option('db_encryption_key')); ?>" /></td>
            </tr>
        </table>

        <?php

        global $is_encrypted;
        if ($is_encrypted) {
            echo '<p style="color: green; font-weight: bold;">Status: The database is currently encrypted.</p>';
        } else {
            echo '<p style="color: red; font-weight: bold;">Status: The database is currently not encrypted.</p>';
        }
        ?>

        <button id="toggleEncryptionButton" name="toggle_encryption" class="button-primary" type="submit"><?php echo $button_text; ?></button>
    </form>

        <script type="text/javascript">
            document.getElementById('encryptionForm').addEventListener('submit', function(event) {
                var key = document.getElementById('db_encryption_key').value;

                if (key.length !== 16) {
                    alert('The encryption key must be exactly 16 bytes. Please enter a key that is 16 characters long.');
                    event.preventDefault(); 
                }
            });
        </script>
    </div>


    <?php
}


function db_encryption_plugin_settings_init() {
    register_setting('db_encryption_plugin_options', 'db_encryption_key');
    add_option('db_encryption_status', false);
}
add_action('admin_init', 'db_encryption_plugin_settings_init');


function db_toggle_encryption_handler() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }
    
    if (isset($_POST['toggle_encryption']) && check_admin_referer('db_encryption_action', 'db_encryption_nonce')) {
        global $is_encrypted;

        $key = sanitize_text_field($_POST['db_encryption_key']);
        update_option('db_encryption_key', $key);
        $encryption_handler = new EncryptionDatabaseHandler($key);

        if ($is_encrypted) {
            $encryption_handler->decryptColumnData();
            $is_encrypted = false;
            update_option('db_encryption_status', false);
        } else {
            $encryption_handler->encryptColumnData();
            $is_encrypted = true;
            update_option('db_encryption_status', true);
        }


        wp_redirect(admin_url('admin.php?page=db-encryption&success=1'));
        exit;
    }
}
add_action('admin_post_toggle_db_encryption', 'db_toggle_encryption_handler');


?>
