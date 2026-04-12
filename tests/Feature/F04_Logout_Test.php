<?php

include "./vendor/autoload.php";

use App\model\UserModel;
use App\model\LogLoginModel;
use App\utils\WebUtils;

beforeAll(function () {
  // buat dummy user
  UserModel::createUser();
});

afterAll(function () {
  // hapus dummy user
  UserModel::deleteUser();

  // hapus log login
  LogLoginModel::deleteLogLogin();
});

describe("F04-P01 | 1-2-3-4-5-6", function() {
  test("F04-P01-T01 | Halaman berpindah ke login, jika tombol logout diklik", function() {
    // Prakondisi :
    // - berada di halaman beranda
    // - sudah login

    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');

    // Kasus uji : 
    // - tombol logout diklik
    $page->click("Keluar");

    // Hasil yang diharapkan :
    // - ke halamann login tanpa error
    // - value field nis kosong
    // - tidak boleh ada "Kata-sandi atau nis tidak ditemukan"

    $page->assertPathEndsWith("/login.php");
    $page->assertValue("#nis", "");
    $page->assertDontSee("Kata-sandi atau nis tidak ditemukan");
    $page->assertDontSee("Warning:");
  });

  test("F04-P01-T02 | Halaman berpindah ke login, jika mengakses url logout.php melaui url bar", function() {
    // Prakondisi :
    // - berada di halaman beranda
    // - belum login
    
    // Kasus uji : 
    // - mengakses url /logout.php melaui url bar
    $page = visit(WebUtils::url("/logout.php"), $this->browserContextOptions());

    // Hasil yang diharapkan :
    // - ke halamann login tanpa error
    // - value field nis kosong
    // - tidak boleh ada "Kata-sandi atau nis tidak ditemukan"

    $page->assertPathEndsWith("/login.php");
    $page->assertValue("#nis", "");
    $page->assertDontSee("Kata-sandi atau nis tidak ditemukan");
    $page->assertDontSee("Warning:");
  });
});