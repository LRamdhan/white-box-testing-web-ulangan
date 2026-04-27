<?php

include "./vendor/autoload.php";

use App\model\UserModel;
use App\model\LogLoginModel;
use App\utils\CookieUtils;
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

describe("F03-P01 | 1-2-7", function() {
  test("F03-P01-T01 | Menampilkan teks 'Harap di Isi', jika login dengan nis dan password yang kosong", function() {
    // Prakondisi :
    // - berada di halaman login
    // - belum login

    // Kasus uji : 
    // - field nis dan password kosong
    // - tombol submit/login diklik

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->click('#submit');

    // Hasil yang diharapkan :
    // - tetap berada di halaman login
    // - menampilkan teks "Harap di Isi"

    $page->assertPathEndsWith("/login.php");
    $page->assertSee("Harap di Isi");
  });

  test("F03-P01-T02 | Menampilkan teks 'Harap di Isi', jika login dengan nis yang diisi space kosong", function() {
    // Prakondisi :
    // - berada di halaman login
    // - belum login

    // Kasus uji : 
    // - field nis diisi space kosong
    // - field password diisi
    // - tombol submit/login diklik

    $spaceNis = "     ";
    $wrongPassword = "abcdefg";

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $spaceNis);
    $page->fill("#password", $wrongPassword);
    $page->click('#submit');

    // Hasil yang diharapkan :
    // - tetap berada di halaman login
    // - menampilkan teks "Harap di Isi"

    $page->assertPathEndsWith("/login.php");
    $page->assertSee("Harap di Isi");
  });

  test("F03-P01-T03 | Menampilkan teks 'Harap di Isi', jika login dengan password yang diisi space kosong", function() {
    // Prakondisi :
    // - berada di halaman login
    // - belum login

    // Kasus uji : 
    // - field nis diisi 
    // - field password diisi space kosong
    // - tombol submit/login diklik

    $wrongNis = "1234567890";
    $spacePassword = "    ";

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $wrongNis);
    $page->fill("#password", $spacePassword);
    $page->click('#submit');

    // Hasil yang diharapkan :
    // - tetap berada di halaman login
    // - menampilkan teks "Harap di Isi"

    $page->assertPathEndsWith("/login.php");
    $page->assertSee("Harap di Isi");
  });
});

describe("F03-P02 | 1-2-3-7", function() {
  test("F03-P02-T01 | Halaman menampilkan notifikasi, jika login dengan nis yang salah", function() {
    // Prakondisi :
    // - berada di halaman login
    // - belum login

    // Kasus uji : 
    // - field nis diisi dengan nis salah
    // - field password diisi
    // - tombol submit/login diklik

    $wrongNis = "2937103853";
    $wrongPassword = "dummypass";

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $wrongNis);
    $page->fill("#password", $wrongPassword);
    $page->click('#submit');

    // Hasil yang diharapkan :
    // - tetap berada di halaman login
    // - menampilkan teks "Kata-sandi atau nis tidak ditemukan"
    // - field nis tetap terisi dengan value sebelumnya

    $page->assertPathEndsWith("/login.php");
    $page->assertSee("Kata-sandi atau nis tidak ditemukan");
    $page->assertValue("#nis", $wrongNis);
  });

  test("F03-P02-T02 | Halaman menampilkan notifikasi, jika login dengan nis yang benar dan password yang salah", function() {
    // Prakondisi :
    // - berada di halaman login
    // - belum login

    // Kasus uji : 
    // - field nis diisi dengan nis yang benar
    // - field password diisi dengan password yang salah
    // - tombol submit/login diklik

    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $wrongPassword = "dummypass";

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $wrongPassword);
    $page->click('#submit');

    // Hasil yang diharapkan :
    // - tetap berada di halaman login
    // - menampilkan teks "Kata-sandi atau nis tidak ditemukan"
    // - field nis tetap terisi dengan value sebelumnya

    $page->assertPathEndsWith("/login.php");
    $page->assertSee("Kata-sandi atau nis tidak ditemukan");
    $page->assertValue("#nis", $correctNis);
  });
});

describe("F03-P03 | 1-2-3-4-5-6-7", function() {
  test("F03-P03-T01 | Halaman menampilkan beranda, jika login menggunakan nis dan password yang benar", function() {
    // Prakondisi :
    // - berada di halaman login
    // - belum login
    // - tidak memiliki cookie_nama dan cookie_kelas

    // Kasus uji : 
    // - field nis diisi dengan nis yang benar
    // - field password diisi dengan password yang benar
    // - tombol submit/login diklik

    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $nama = $dummyUser["nama"];
    $kelas = $dummyUser["kelas"];

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');

    // Hasil yang diharapkan :
    // - berpindah ke halaman beranda
    // - menampilkan nama, nis, dan kelas yang benar
    // - cookie_nama dan cookie_kelas terisi dengan value yang benar
    // - PHPSESSID terisi

    $page->assertPathEndsWith("/index.php");
    $page->assertSee($nama);
    $page->assertSee($correctNis);
    $page->assertSee($kelas);

    $cookieNama = CookieUtils::getCookie($page, "cookie_nama");
    $cookieKelas = CookieUtils::getCookie($page, "cookie_kelas");
    $phpsessid = CookieUtils::getCookie($page, "PHPSESSID");

    expect($cookieNama)->toBe($nama);
    expect($cookieKelas)->toBe($kelas);
    expect($phpsessid)->not->toBeNull();
  });
});

describe("F03-P04 | 1-2-3-4-6-7", function() {
  test("F03-P04-T01 | Value cookie cookie_nama dan cookie_kelas di browser harus sesuai dengan data yang ada di basis data, jika login dengan kondisi value cookie_nama tidak ada di basis data", function() {
    // Prakondisi :
    // - berada di halaman login
    // - belum login
    // - cookie_nama tidak ada di database
    // - cookie_kelas ada di database


    // Kasus uji : 
    // - field nis diisi dengan nis yang benar
    // - field password diisi dengan password yang benar
    // - tombol submit/login diklik

    $dummyUser = WebUtils::getDummyUser();
    $unExistingNama = "Sjieijf";
    $existingNama = $dummyUser["nama"];
    $ExistingKelas = $dummyUser["kelas"];
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    CookieUtils::setCookie($page, "cookie_nama", $unExistingNama);
    CookieUtils::setCookie($page, "cookie_kelas", $existingKelas);
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');

    // Hasil yang diharapkan :
    // - berpindah ke halaman beranda
    // - cookie_nama tersisi dengan value yang ada di database
    // - cookie_kelas tersisi dengan value yang ada di database

    $page->assertPathEndsWith("/index.php");
    $cookieNamaValue = CookieUtils::getCookie($page, "cookie_nama");
    $cookieKelasValue = CookieUtils::getCookie($page, "cookie_kelas");
    expect($cookieNamaValue)->toBe($existingNama);
    expect($cookieKelasValue)->toBe($existingKelas);
  });

  test("F03-P04-T02 | Value cookie cookie_nama dan cookie_kelas harus sesuai dengan data yang ada di basis data, jika login dengan kondisi value cookie_kelas tidak ada di basis data", function() {
    // Prakondisi :
    // - berada di halaman login
    // - belum login
    // - cookie_nama ada di database
    // - cookie_kelas tidak ada di database

    // Kasus uji : 
    // - field nis diisi dengan nis yang benar
    // - field password diisi dengan password yang benar
    // - tombol submit/login diklik

    $dummyUser = WebUtils::getDummyUser();
    $existingNama = $dummyUser["nama"];
    $unExistingKelas = "unexist_class";
    $existingKelas = $dummyUser["kelas"];
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    CookieUtils::setCookie($page, "cookie_nama", $existingNama);
    CookieUtils::setCookie($page, "cookie_kelas", $unExistingKelas);
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');

    // Hasil yang diharapkan :
    // - berpindah ke halaman beranda
    // - cookie_nama tersisi dengan value yang ada di database
    // - cookie_kelas tersisi dengan value yang ada di database
    
    $page->assertPathEndsWith("/index.php");
    $cookieNamaValue = CookieUtils::getCookie($page, "cookie_nama");
    $cookieKelasValue = CookieUtils::getCookie($page, "cookie_kelas");
    expect($cookieKelasValue)->toBe($existingKelas);
    expect($cookieNamaValue)->toBe($existingNama);
  });
  
  test("F03-P04-T03 | Value cookie cookie_nama dan cookie_kelas harus sesuai dengan data yang ada di basis data, jika login dengan kondisi cookie_nama tidak ada", function() {
    // Prakondisi :
    // - berada di halaman login
    // - belum login
    // - cookie_nama tidak ada
    // - cookie_kelas ada

    // Kasus uji : 
    // - field nis diisi dengan nis yang benar
    // - field password diisi dengan password yang benar
    // - tombol submit/login diklik

    $dummyUser = WebUtils::getDummyUser();
    $existingNama = $dummyUser["nama"];
    $existingKelas = $dummyUser["kelas"];
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    CookieUtils::setCookie($page, "cookie_kelas", $existingKelas);
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');

    // Hasil yang diharapkan :
    // - berpindah ke halaman beranda
    // - cookie_nama tersisi dengan value yang ada di database
    // - cookie_kelas tersisi dengan value yang ada di database
    
    $page->assertPathEndsWith("/index.php");
    $cookieNamaValue = CookieUtils::getCookie($page, "cookie_nama");
    $cookieKelasValue = CookieUtils::getCookie($page, "cookie_kelas");
    expect($cookieNamaValue)->toBe($existingNama);
    expect($cookieKelasValue)->toBe($existingKelas);
  });
  
  test("F03-P04-T04 | Value cookie cookie_nama dan cookie_kelas harus sesuai dengan data yang ada di basis data, jika login dengan kondisi cookie_kelas tidak ada", function() {
    // Prakondisi :
    // - berada di halaman login
    // - belum login
    // - cookie_nama ada
    // - cookie_kelas tidak ada

    // Kasus uji : 
    // - field nis diisi dengan nis yang benar
    // - field password diisi dengan password yang benar
    // - tombol submit/login diklik

    $dummyUser = WebUtils::getDummyUser();
    $existingNama = $dummyUser["nama"];
    $existingKelas = $dummyUser["kelas"];
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    CookieUtils::setCookie($page, "cookie_nama", $existingNama);
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');

    // Hasil yang diharapkan :
    // - berpindah ke halaman beranda
    // - cookie_nama tersisi dengan value yang ada di database
    // - cookie_kelas tersisi dengan value yang ada di database
    
    $page->assertPathEndsWith("/index.php");
    $cookieNamaValue = CookieUtils::getCookie($page, "cookie_nama");
    $cookieKelasValue = CookieUtils::getCookie($page, "cookie_kelas");
    expect($cookieNamaValue)->toBe($existingNama);
    expect($cookieKelasValue)->toBe($existingKelas);
  });
});