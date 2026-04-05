<?php

namespace App\model;

include "./vendor/autoload.php";

use App\conf\DBConnection;
use App\utils\JsonUtils;
use App\utils\HashUtils;
use App\utils\WebUtils;

class SoalSetModel extends DBConnection {
  // create soal_info
  public static function createSoalInfo() {
    // connect
    self::connect();

    // read data in json
    $soalInfo = JsonUtils::readJson('./tests/Data/soal_info.json');

    // insert query
    $sqlInsertSoalInfo = "
      INSERT INTO soal_info (
        id_info_soal,
        id_register,
        status_aktif,
        nama_pelajaran,
        tahun_ajaran,
        soal,
        pilihan,
        waktu,
        waktu_mulai,
        tanggal_mulai,
        nama_guru,
        id_kelas,
        kelas,
        jurusan,
        kode_ulangan,
        kode_key
      ) VALUES (
        ".(int)$soalInfo['id_info_soal'].",
        ".(int)$soalInfo['id_register'].",
        ".(int)$soalInfo['status_aktif'].",
        '".$soalInfo['nama_pelajaran']."',
        ".(int)$soalInfo['tahun_ajaran'].",
        ".(int)$soalInfo['soal'].",
        ".(int)$soalInfo['pilihan'].",
        ".(int)$soalInfo['waktu'].",
        '".$soalInfo['waktu_mulai']."',
        '".$soalInfo['tanggal_mulai']."',
        '".$soalInfo['nama_guru']."',
        '".$soalInfo['id_kelas']."',
        '".$soalInfo['kelas']."',
        '".$soalInfo['jurusan']."',
        '".$soalInfo['kode_ulangan']."',
        '".$soalInfo['kode_key']."'
      );
    ";
    mysqli_query(self::$connection, $sqlInsertSoalInfo);

    // disconnect
    self::disconnect();
  }

  // delete soal_info
  public static function deleteSoalInfo() {
    // connect
    self::connect();

    // query delete
    $idSoalInfo = (int)WebUtils::getSoalInfoProperty("id_info_soal");
    $sqlDeleteSoalInfo = "DELETE FROM soal_info WHERE id_info_soal = $idSoalInfo;";
    mysqli_query(self::$connection, $sqlDeleteSoalInfo);

    // disconnect
    self::disconnect();
  }

  // check soal_info
  public static function checkSoalInfo() {
    // connect
    self::connect();

    // query read
    $idSoalInfo = (int)WebUtils::getSoalInfoProperty("id_info_soal");
    $sqlSelectSoalInfo = "SELECT * FROM soal_info WHERE id_info_soal = $idSoalInfo;";
    $querySelectSoalInfo = mysqli_query(self::$connection, $sqlSelectSoalInfo);

    // disconnect
    self::disconnect();

    // return existance
    if (mysqli_num_rows($querySelectSoalInfo) > 0) {
      return true;
    } else {
      return false;
    }
  }

  // parse soal_list & soal_list_pil from json
  public static function parseSoaListSoalListPil() {
    $idInfoSoal = (int)WebUtils::getSoalInfoProperty("id_info_soal");

    // read data in json
    $soalListSoalPil = JsonUtils::readJson('./tests/Data/soal_list_soal_list_pil.json');

    // change key list
    for($i = 0; $i < count($soalListSoalPil); $i++) {
      $hashSoal = HashUtils::generateAlphabetHash();
      $soalListSoalPil[$i]['id_info_soal'] = $idInfoSoal;
      $soalListSoalPil[$i]['key_list'] = $hashSoal;
      for($z = 0; $z < count($soalListSoalPil[$i]["pilihan_ganda"]); $z++) {
        $hashPilihan = HashUtils::generateAlphabetHash();
        $soalListSoalPil[$i]["pilihan_ganda"][$z]['id_info_soal'] = $idInfoSoal;
        $soalListSoalPil[$i]["pilihan_ganda"][$z]['key_list'] = $hashSoal;
        $soalListSoalPil[$i]["pilihan_ganda"][$z]['key_list_pil'] = $hashPilihan;
      }
    }

    // new soal & pil
    $newSoal = [];
    $newPil = [];
    for($i = 0; $i < count($soalListSoalPil); $i++) {
      $newSoal[] = [
        "id_register" => $soalListSoalPil[$i]["id_register"],
        "id_info_soal" => $soalListSoalPil[$i]["id_info_soal"],
        "key_list" => $soalListSoalPil[$i]["key_list"],
        "number" => $soalListSoalPil[$i]["number"],
        "type" => $soalListSoalPil[$i]["type"],
        "soal" => $soalListSoalPil[$i]["soal"],
        "jawaban" => $soalListSoalPil[$i]["jawaban"],
        "jmlpg" => $soalListSoalPil[$i]["jmlpg"],
      ];
      for($z = 0; $z < count($soalListSoalPil[$i]["pilihan_ganda"]); $z++) {
        $newPil[] = [
          "id_soal_list_pil" => $soalListSoalPil[$i]["pilihan_ganda"][$z]["id_soal_list_pil"],
          "id_info_soal" => $soalListSoalPil[$i]["pilihan_ganda"][$z]["id_info_soal"],
          "key_list" => $soalListSoalPil[$i]["pilihan_ganda"][$z]["key_list"],
          "key_list_pil" => $soalListSoalPil[$i]["pilihan_ganda"][$z]["key_list_pil"],
          "list_pil" => $soalListSoalPil[$i]["pilihan_ganda"][$z]["list_pil"],
          "pilihan" => $soalListSoalPil[$i]["pilihan_ganda"][$z]["pilihan"],
          "typepg" => $soalListSoalPil[$i]["pilihan_ganda"][$z]["typepg"],
        ];
      }
    }

    // return
    return [
      "soal" => $newSoal,
      "pil" => $newPil
    ];
  }

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

  // check soal_list
  public static function checkSoalList() {
    // connect
    self::connect();

    // query read
    $idSoalInfo = (int)WebUtils::getSoalInfoProperty("id_info_soal");
    $sqlSelectSoalList = "SELECT * FROM soal_list WHERE id_info_soal = $idSoalInfo;";
    $querySelectSoalList = mysqli_query(self::$connection, $sqlSelectSoalList);

    // disconnect
    self::disconnect();

    // return existance
    if (mysqli_num_rows($querySelectSoalList) > 0) {
      return true;
    } else {
      return false;
    }
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

  // create soal_jadwal
  public static function createSoalJadwal() {
    // read json
    $soalJadwal = JsonUtils::readJson("./tests/Data/soal_jadwal.json");

    // create sql insert
    $sqlInsertSoalJadwal = "INSERT INTO soal_jadwal (
      id_info_soal,
      nama_pelajaran,
      id_kelas,
      nama_kelas,
      jurusan,
      tanggal,
      time
    ) VALUE (
      ".(int)$soalJadwal['id_info_soal'].",
      '".$soalJadwal['nama_pelajaran']."',
      ".(int)$soalJadwal['id_kelas'].",
      '".$soalJadwal['nama_kelas']."',
      '".$soalJadwal['jurusan']."',
      '".$soalJadwal['tanggal']."',
      '".$soalJadwal['time']."'
    );";

    // connect
    self::connect();

    // execute
    mysqli_query(self::$connection, $sqlInsertSoalJadwal);

    // disconnect
    self::disconnect();
  }

  // check soal_jadwal
  public static function checkSoalJadwal() {
    // connect
    self::connect();

    // query read
    $idSoalInfo = (int)WebUtils::getSoalInfoProperty("id_info_soal");
    $sqlSelectSoalJadwal = "SELECT * FROM soal_jadwal WHERE id_info_soal = $idSoalInfo;";
    $querySelectSoalJadwal = mysqli_query(self::$connection, $sqlSelectSoalJadwal);

    // disconnect
    self::disconnect();

    // return existance
    if (mysqli_num_rows($querySelectSoalJadwal) > 0) {
      return true;
    } else {
      return false;
    }
  }

  // delete soal_jadwal
  public static function deleteSoalJadwal() {
    // connect
    self::connect();

    // query delete
    $idSoalInfo = (int)WebUtils::getSoalInfoProperty("id_info_soal");
    $sqlDeleteSoalJadwal = "DELETE FROM soal_jadwal WHERE id_info_soal = $idSoalInfo;";
    mysqli_query(self::$connection, $sqlDeleteSoalJadwal);

    // disconnect
    self::disconnect();
  }

  public static function setupSoal() {
    // parse soal_list & soal_list_pil
    $soalPil = self::parseSoaListSoalListPil();
    $soal = $soalPil["soal"];
    $pil = $soalPil["pil"];

    // soal_info
    $soalInfoExist = self::checkSoalInfo();
    if($soalInfoExist) {
      self::deleteSoalInfo();
    }
    self::createSoalInfo();

    // soal_list
    $soalListExist = self::checkSoalList();
    if($soalListExist) {
      self::deleteSoalList();
    }
    self::createSoalList($soal);

    // soal_list_pil
    $soalListPilExist = self::checkSoalListPil();
    if($soalListPilExist) {
      self::deleteSoalListPil();
    }
    self::createSoalListPil($pil);

    // soal_jadwal
    $soalJadwalExist = self::checkSoalJadwal();
    if($soalJadwalExist) {
      self::deleteSoalJadwal();
    }
    self::createSoalJadwal();
  }

  public static function clearSoal() {
    // delete all soal
    self::deleteSoalInfo();
    self::deleteSoalList();
    self::deleteSoalListPil();
    self::deleteSoalJadwal();
  }
}