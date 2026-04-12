<?php

namespace App\helper;

include "./vendor/autoload.php";

use App\model\UserModel;
use App\model\LogLoginModel;

class UserHelper {
  public static function setupUser() {
    // buat dummy user
    UserModel::createUser();

    // buat dummy user 2
    UserModel::createUser2();
  }

  public static function cleanupUser() {
    // hapus dummy user
    UserModel::deleteUser();

    // hapus dummy user 2
    UserModel::deleteUser2();

    // hapus log login
    LogLoginModel::deleteLogLogin();

    // hapus log login 2
    LogLoginModel::deleteLogLogin2();
  }
}