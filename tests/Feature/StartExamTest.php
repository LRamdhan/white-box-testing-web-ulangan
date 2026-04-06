<?php

include "./vendor/autoload.php";

use App\model\SoalInfoModel;
use App\model\SoalListModel;
use App\model\SoalListPilModel;
use App\model\SoalJadwalModel;
use App\utils\WebUtils;

beforeAll(function() {
  // parse soal_list & soal_list_pil
  $soalPil = WebUtils::parseSoaListSoalListPil();
  $soal = $soalPil["soal"];
  $pil = $soalPil["pil"];

  // soal_info
  $soalInfoExist = SoalInfoModel::checkSoalInfo();
  if($soalInfoExist) {
    SoalInfoModel::deleteSoalInfo();
  }
  SoalInfoModel::createSoalInfo();

  // soal_list
  $soalListExist = SoalListModel::checkSoalList();
  if($soalListExist) {
    SoalListModel::deleteSoalList();
  }
  SoalListModel::createSoalList($soal);

  // soal_list_pil
  $soalListPilExist = SoalListPilModel::checkSoalListPil();
  if($soalListPilExist) {
    SoalListPilModel::deleteSoalListPil();
  }
  SoalListPilModel::createSoalListPil($pil);

  // soal_jadwal
  $soalJadwalExist = SoalJadwalModel::checkSoalJadwal();
  if($soalJadwalExist) {
    SoalJadwalModel::deleteSoalJadwal();
  }
  SoalJadwalModel::createSoalJadwal();
});

afterAll(function() {
   // delete all soal
   SoalInfoModel::deleteSoalInfo();
   SoalListModel::deleteSoalList();
   SoalListPilModel::deleteSoalListPil();
   SoalJadwalModel::deleteSoalJadwal();
});

it('example test', function () {
  expect(true)->toBeTrue();
});