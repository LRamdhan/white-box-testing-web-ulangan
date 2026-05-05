<?php

include "./vendor/autoload.php";

use App\model\LogLoginModel;
use App\model\TokenModel;
use App\model\UserModel;
use App\model\TestListTestModel;
use App\utils\WebUtils;
use App\helper\UlanganHelper;

beforeAll(function() {
  UlanganHelper::clenupUlangan(); // cleanup ulangan
  UserModel::deleteUser(); // hapus dummy user
  UserModel::deleteUser2(); // hapus dummy user 2
  LogLoginModel::deleteLogLogin(); // hapus log login
  LogLoginModel::deleteLogLogin2(); // hapus log login 2
  
  // setup ulangan
  UlanganHelper::setupUlangan();

  // buat dummy user
  UserModel::createUser();

  // buat dummy user 2
  UserModel::createUser2();
});

afterAll(function() {
  UlanganHelper::clenupUlangan(); // cleanup ulangan
  UserModel::deleteUser(); // hapus dummy user
  UserModel::deleteUser2(); // hapus dummy user 2
  LogLoginModel::deleteLogLogin(); // hapus log login
  LogLoginModel::deleteLogLogin2(); // hapus log login 2
});

describe("F04-P01 | 1-2-8-6", function() {
  afterEach(function() {
    // cleanup pengerjaan
    UlanganHelper::cleanupPengerjaan();
  });

  test("F04-P01-T01 | Tetap di halaman mulai ulangan jika memasukan token yang salah", function() {
    // Prakondisi :
    // - sudah login
    // - berada di halaman mulai ulangan
    // - soal sudah aktif

    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));

    // Kasus uji : 
    // 1. memasukan token yang salah
    // 2. klik tombol mulai kerjakan

    $wrongToken = "123456";
    $page->fill("input[name=\"token\"]", $wrongToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");

    // Hasil yang diharapkan :
    // - masih berada di halaman mulai ulangan
    // - menampilkan data ulangan yang sesuai

    $ulanganName = WebUtils::getSoalInfoProperty("nama_pelajaran");
    $page->assertPathEndsWith("/start.php");
    $page->assertSee($ulanganName);
  });
});

describe("F04-P02 | 1-2-3-4-5-6", function() {
  afterEach(function() {
    // cleanup pengerjaan
    UlanganHelper::cleanupPengerjaan();
  });

  test("F04-P02-T01 | Berpindah ke halaman pengerjaan soal jika memasukan token yang benar", function() {
    // Prakondisi :
    // - sudah login
    // - berada di halaman mulai ulangan
    // - soal sudah aktif

    $dummyUser2 = WebUtils::getDummyUser();
    $correctNis = $dummyUser2["nis"];
    $correctPassword = $dummyUser2["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));

    // Kasus uji : 
    // 1. memasukan token yang benar
    // 2. klik tombol mulai kerjakan

    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");

    // Hasil yang diharapkan :
    // - redirect ke halaman pengerjaan ulangan
    // - menampilkan data ulangan yang sesuai

    $ulanganName = WebUtils::getSoalInfoProperty("nama_pelajaran");
    $page->assertPathEndsWith("/test.php");
    $page->assertSee($ulanganName);
  });
});

describe("F04-P03 | 1-2-3-7-5-6", function() {
  afterEach(function() {
    // cleanup pengerjaan
    UlanganHelper::cleanupPengerjaan();
  });

  test("F04-P03-T01 | Berpindah ke halaman pengerjaan soal jika memasukan token yang benar dalam keadaan sebelumnya sudah mulai mengerjakan soal", function() {
    // Prakondisi :
    // - sudah login
    // - berada di halaman mulai ulangan
    // - soal sudah aktif

    $dummyUser2 = WebUtils::getDummyUser();
    $correctNis = $dummyUser2["nis"];
    $correctPassword = $dummyUser2["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));

    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");

    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));

    // Kasus uji : 
    // 1. memasukan token yang benar
    // 2. klik tombol mulai kerjakan

    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Lanjut Mengerjakan\"]");

    // Hasil yang diharapkan :
    // - redirect ke halaman pengerjaan ulangan
    // - menampilkan data ulangan yang sesuai

    $ulanganName = WebUtils::getSoalInfoProperty("nama_pelajaran");
    $page->assertPathEndsWith("/test.php");
    $page->assertSee($ulanganName);
  });
});