<?php

namespace App\model;

include "./vendor/autoload.php";

use App\conf\DBConnection;
use App\utils\WebUtils;

class TestListTestModel extends DBConnection {
  public static function deleteTestListTest() {
    // connect
    self::connect();

    // sql
    $dummyUser = WebUtils::getDummyUser();
    $keyMember = $dummyUser["keyMember"];
    $sqlDeleteTestListTest = "DELETE FROM test_list_test WHERE key_member='" . $keyMember . "';";
    mysqli_query(self::$connection, $sqlDeleteTestListTest);

    // disconnect
    self::disconnect();
  }
}