<?php

namespace App\model;

include "./vendor/autoload.php";

use App\conf\DBConnection;
use App\utils\WebUtils;

class SoalListPilModel extends DBConnection {
  // create soal_list_pil
  public static function createSoalListPil($pil) {
    // create sql insert
    $valueList = "";
    for($i = 0; $i < count($pil); $i++) {
      $coma = ($i == (count($pil) - 1)) ? "" : ",";
      $valueList .= "
        (
          ".(int)$pil[$i]["id_info_soal"].",
          '".$pil[$i]["key_list"]."',
          '".$pil[$i]["key_list_pil"]."',
          '".$pil[$i]["list_pil"]."',
          '".$pil[$i]["pilihan"]."',
          '".$pil[$i]["typepg"]."'
        )$coma
      ";
    }    
    $sqlInserSoalListPil = "
      INSERT INTO soal_list_pil (
        id_info_soal,
        key_list,
        key_list_pil,
        list_pil,
        pilihan,
        typepg
      ) VALUES $valueList;
    ";

    // connect
    self::connect();

    // execute
    mysqli_query(self::$connection, $sqlInserSoalListPil);

    // disconnect
    self::disconnect();
  }

  // check soal_list_pil
  public static function checkSoalListPil() {
    // connect
    self::connect();

    // query read
    $idSoalInfo = (int)WebUtils::getSoalInfoProperty("id_info_soal");
    $sqlSelectSoalListPil = "SELECT * FROM soal_list_pil WHERE id_info_soal = $idSoalInfo;";
    $querySelectSoalListPil = mysqli_query(self::$connection, $sqlSelectSoalListPil);

    // disconnect
    self::disconnect();

    // return existance
    if (mysqli_num_rows($querySelectSoalListPil) > 0) {
      return true;
    } else {
      return false;
    }
  }

   // delete soal_list_pil
   public static function deleteSoalListPil() {
    // connect
    self::connect();

    // query delete
    $idSoalInfo = (int)WebUtils::getSoalInfoProperty("id_info_soal");
    $sqlDeleteSoalListPil = "DELETE FROM soal_list_pil WHERE id_info_soal = $idSoalInfo;";
    mysqli_query(self::$connection, $sqlDeleteSoalListPil);

    // disconnect
    self::disconnect();
  }

}