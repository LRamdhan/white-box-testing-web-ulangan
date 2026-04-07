<?php

namespace App\model;

include "./vendor/autoload.php";

use App\conf\DBConnection;
use App\utils\WebUtils;

class MemberTesModel extends DBConnection {
  public static function deleteMemberTes() {
    $dummyUser = WebUtils::getDummyUser();
    $keyMember = $dummyUser["keyMember"];

    // connect
    self::connect();

    // query
    $sqlDeleteMemberTes = "DELETE FROM member_tes WHERE key_member = '$keyMember';";
    mysqli_query(self::$connection, $sqlDeleteMemberTes);

    // disconnect
    self::disconnect();
  }
}