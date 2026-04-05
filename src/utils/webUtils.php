<?php

namespace App\utils;

use App\utils\JsonUtils;

include "./loadenv.php";

Class WebUtils {
  public static function url($path) {
    return $_ENV["BASE_URL"] . $path;
  }

  public static function getSoalInfoProperty($propertName) {
    $soalInfo = JsonUtils::readJson("./tests/data/soal_info.json");
    return $soalInfo[$propertName];
  }
}