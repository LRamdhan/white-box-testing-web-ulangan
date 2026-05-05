<?php

namespace App\model;

include "./vendor/autoload.php";

use App\conf\DBConnection;
use App\utils\WebUtils;

class TestSoalListModel extends DBConnection {
  // create soal_list
  public static function createTestSoalList($soal) {
    // create sql insert
    $valueList = "";
    for($i = 0; $i < count($soal); $i++) {
      $coma = ($i == (count($soal) - 1)) ? "" : ",";
      $valueList .= "
        (
          ".(int)$soal[$i]["id_register"].",
          ".(int)$soal[$i]["id_info_soal"].",
          '".$soal[$i]["key_list"]."',
          ".(int)$soal[$i]["number"].",
          '".$soal[$i]["type"]."',
          '".$soal[$i]["soal"]."',
          '".$soal[$i]["jawaban"]."',
          ".(int)$soal[$i]["jmlpg"]."
        )$coma
      ";
    }    
    $sqlInserTestSoalList = "
      INSERT INTO test_soal_list (
        id_register,
        id_info_soal,
        key_list,
        number,
        type,
        soal,
        jawaban,
        jmlpg
      ) VALUES $valueList;
    ";

    // connect
    self::connect();

    // execute
    mysqli_query(self::$connection, $sqlInserTestSoalList);

    // disconnect
    self::disconnect();
  }

  // delete test_soal_list
  public static function deleteTestSoalList() {
    // connect
    self::connect();

    // query delete
    $idSoalInfo = (int)WebUtils::getSoalInfoProperty("id_info_soal");
    $sqlDeleteTestSoalList = "DELETE FROM test_soal_list WHERE id_info_soal = $idSoalInfo;";
    mysqli_query(self::$connection, $sqlDeleteTestSoalList);

    // disconnect
    self::disconnect();
  }

  public static function getTestSoalList($keysoal) {
    // connect
    self::connect();

    // query read
    $idSoalInfo = (int)WebUtils::getSoalInfoProperty("id_info_soal");
    $sqlSelectTestSoalList = "SELECT * FROM test_soal_list WHERE id_info_soal = $idSoalInfo AND key_list = '$keysoal';";
    $querySelectTestSoalList = mysqli_query(self::$connection, $sqlSelectTestSoalList);
    $soal = mysqli_fetch_assoc($querySelectTestSoalList);

    // disconnect
    self::disconnect();

    // return
    return $soal;
  }
  

  public static function getAllTestSoalList() {
    // connect
    self::connect();

    // query read
    $idSoalInfo = (int)WebUtils::getSoalInfoProperty("id_info_soal");
    $sqlSelectTestSoalList = "SELECT * FROM test_soal_list WHERE id_info_soal = $idSoalInfo;";
    $querySelectTestSoalList = mysqli_query(self::$connection, $sqlSelectTestSoalList);
    $soalList = [];
    while($soal = mysqli_fetch_assoc($querySelectTestSoalList)) {
      $soalList[] = $soal;
    }

    // disconnect
    self::disconnect();

    // return
    return $soalList;
  }
}