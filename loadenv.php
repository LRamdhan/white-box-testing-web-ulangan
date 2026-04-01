<?php

include "./vendor/autoload.php";

use Dotenv\Dotenv;

// Load environment variables from the root directory
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
