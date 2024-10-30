<?php

if (!defined('ABSPATH')) {
    exit;
}
class EncryptionManager {
    private $encryption_key;

    private $DBManager;

    public function __construct($encryption_key) {
        $this->encryption_key = $encryption_key;
        $this->DBManager = new EncryptionDatabaseHandler($this->encryption_key);
    }

    public function encryptData() {
        $this->DBManager->encryptColumnData();
    }

    public function decryptData() {
        $this->DBManager->decryptColumnData();
    }
}
