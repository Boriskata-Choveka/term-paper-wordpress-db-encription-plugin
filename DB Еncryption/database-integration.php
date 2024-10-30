<?php

if (!defined('ABSPATH')) {
    exit;
}

class EncryptionDatabaseHandler {
    private $wpdb;
    private $encryption_key;

    public function __construct($encryption_key) {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->encryption_key = $encryption_key;
    }


    public function encryptColumnData() {
        $table = 'test';
        $column_name = 'text';

        $rows = $this->wpdb->get_results("SELECT id, $column_name FROM $table");

        foreach ($rows as $row) {
            $encrypted_data = $this->encryptData($row->$column_name);
            $this->wpdb->update($table, [$column_name => $encrypted_data], ['id' => $row->id]);
        }
    }


    public function decryptColumnData() {
        $table = 'test';
        $column_name = 'text';
    
        $rows = $this->wpdb->get_results("SELECT id, $column_name FROM $table");

        
        foreach ($rows as $row) {
            $decrypted_data = $this->decryptData($row->$column_name);
            $this->wpdb->update($table, [$column_name => $decrypted_data], ['id' => $row->id]);
        }
    }
    


    private function encryptData($data) {
        $iv = substr($this->encryption_key, 0, 16);
        return openssl_encrypt($data, 'aes-256-cbc', $this->encryption_key, 0, $iv);
    }


    private function decryptData($data) {
        $iv = substr($this->encryption_key, 0, 16);
        return openssl_decrypt($data, 'aes-256-cbc', $this->encryption_key, 0, $iv);
    }

    private function logError($message) {
        $log_file = ABSPATH . 'wp-content/uploads/encryption_handler_error_log.txt';
        $log_entry = date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
        file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }
}
