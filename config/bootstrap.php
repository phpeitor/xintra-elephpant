<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if (!defined('ROOT')) {
	define('ROOT', realpath(__DIR__ . '/../'));
}

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}
