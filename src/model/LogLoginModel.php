<?php

namespace App\model;

include "./vendor/autoload.php";

use App\conf\DBConnection;
use App\utils\WebUtils;

class LogLoginModel extends DBConnection {

  public static function deleteLogLogin() {
    $dummyUser = WebUtils::getDummyUser();

    // connect
    self::connect();

    // cek apakah log login dummy user sudah ada, jika ada hapus
    $querySelect = "SELECT * FROM log_login WHERE nis='" . $dummyUser["nis"] . "';";
    $selectUser = mysqli_query(self::$connection, $querySelect);
    if(mysqli_num_rows($selectUser) >= 1) {
      $deleteExistUserQuery = "DELETE FROM log_login WHERE nis='" . $dummyUser["nis"] . "';";
      $deleteExistUser = mysqli_query(self::$connection, $deleteExistUserQuery);
    }

    // disconnect
    self::disconnect();
  }

  public static function deleteLogLogin2() {
    $dummyUser = WebUtils::getDummyUser2();

    // connect
    self::connect();

    // cek apakah log login dummy user sudah ada, jika ada hapus
    $querySelect = "SELECT * FROM log_login WHERE nis='" . $dummyUser["nis"] . "';";
    $selectUser = mysqli_query(self::$connection, $querySelect);
    if(mysqli_num_rows($selectUser) >= 1) {
      $deleteExistUserQuery = "DELETE FROM log_login WHERE nis='" . $dummyUser["nis"] . "';";
      $deleteExistUser = mysqli_query(self::$connection, $deleteExistUserQuery);
    }

    // disconnect
    self::disconnect();
  }
}