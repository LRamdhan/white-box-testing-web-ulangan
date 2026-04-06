<?php

namespace App\model;

include "./vendor/autoload.php";

use App\conf\DBConnection;
use App\utils\JsonUtils;
use App\utils\WebUtils;

class SoalJadwalModel extends DBConnection {
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
}