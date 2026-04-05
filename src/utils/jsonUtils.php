<?php

namespace App\utils;

class JsonUtils {
  public static function readJson($path) {
    $rawContent = file_get_contents($path);
    return json_decode($rawContent, true);
  }
}