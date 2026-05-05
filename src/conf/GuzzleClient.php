<?php

namespace App\conf;

include "./vendor/autoload.php";
include "./loadenv.php";

use GuzzleHttp\Client;

class GuzzleClient {
  public static function createClient() {
    return new Client([
      'base_uri' => $_ENV["BASE_URL"] . "/",
      'timeout' => 5.0
    ]);
  }
}
