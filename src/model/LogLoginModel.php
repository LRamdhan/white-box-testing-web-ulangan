<?php

namespace App\model;

include "./vendor/autoload.php";

use App\conf\DBConnection;
use Tests\Data\DummyUser;

class LogLoginModel extends DBConnection {

  public static function deleteLogLogin() {
    // connect
    self::connect();

    // cek apakah log login dummy user sudah ada, jika ada hapus
    $querySelect = "SELECT * FROM log_login WHERE nis='" . DummyUser::$nis . "';";
    $selectUser = mysqli_query(self::$connection, $querySelect);
    if(mysqli_num_rows($selectUser) >= 1) {
      $deleteExistUserQuery = "DELETE FROM log_login WHERE nis='" . DummyUser::$nis . "';";
      $deleteExistUser = mysqli_query(self::$connection, $deleteExistUserQuery);
    }

    // disconnect
    self::disconnect();
  }

}