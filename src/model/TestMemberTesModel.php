<?php

namespace App\model;

include "./vendor/autoload.php";

use App\conf\DBConnection;
use App\utils\WebUtils; 

class TestMemberTesModel extends DBConnection {
  public static function deleteTestMemberTes() {
    $dummyUser = WebUtils::getDummyUser();
    $keyMember = $dummyUser["keyMember"];

    // connect
    self::connect();

    // query
    $sqlDeleteTestMemberTes = "DELETE FROM test_member_tes WHERE key_member = '$keyMember';";
    mysqli_query(self::$connection, $sqlDeleteTestMemberTes);

    // disconnect
    self::disconnect();
  }
}