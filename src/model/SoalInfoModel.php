<?php

namespace App\model;

include "./vendor/autoload.php";

use App\conf\DBConnection;
use App\utils\JsonUtils;
use App\utils\HashUtils;
use App\utils\WebUtils;

class SoalInfoModel extends DBConnection {
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
}