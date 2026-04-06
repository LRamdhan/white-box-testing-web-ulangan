<?php

namespace App\utils;

use App\utils\JsonUtils;
use App\utils\HashUtils;

include "./loadenv.php";

Class WebUtils {
  public static function url($path) {
    return $_ENV["BASE_URL"] . $path;
  }

  public static function getSoalInfoProperty($propertName) {
    $soalInfo = JsonUtils::readJson("./tests/data/soal_info.json");
    return $soalInfo[$propertName];
  }

  public static function parseSoaListSoalListPil() {
    $idInfoSoal = (int)self::getSoalInfoProperty("id_info_soal");

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
}