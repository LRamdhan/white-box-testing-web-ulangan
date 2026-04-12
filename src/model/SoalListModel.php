<?php

namespace App\model;

include "./vendor/autoload.php";

use App\conf\DBConnection;
use App\utils\WebUtils;

class SoalListModel extends DBConnection {
  // create soal_list
  public static function createSoalList($soal) {
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
    $sqlInserSoalList = "
      INSERT INTO soal_list (
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
    mysqli_query(self::$connection, $sqlInserSoalList);

    // disconnect
    self::disconnect();
  }
  
  // delete soal_list
  public static function deleteSoalList() {
    // connect
    self::connect();

    // query delete
    $idSoalInfo = (int)WebUtils::getSoalInfoProperty("id_info_soal");
    $sqlDeleteSoalList = "DELETE FROM soal_list WHERE id_info_soal = $idSoalInfo;";
    mysqli_query(self::$connection, $sqlDeleteSoalList);

    // disconnect
    self::disconnect();
  }
}