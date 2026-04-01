<?php

namespace App\utils;

include ".loadenv.php";

Class WebUtils {
  public static function url($path) {
    return $_ENV["BASE_URL"] . $path;
  }
}