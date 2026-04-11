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
  
  public static function getTestMemberTes() {
    $dummyUser = WebUtils::getDummyUser();
    $keyMember = $dummyUser["keyMember"];

    // connect
    self::connect();

    // query
    $sqlSelectTestMemberTes = "SELECT * FROM test_member_tes WHERE key_member = '$keyMember';";
    $selectTestMemberTesQuery = mysqli_query(self::$connection, $sqlSelectTestMemberTes);
    $testMembertes = mysqli_fetch_assoc($selectTestMemberTesQuery);

    // disconnect
    self::disconnect();

    return $testMembertes;
  }
}