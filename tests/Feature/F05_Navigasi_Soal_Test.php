<?php

include "./vendor/autoload.php";

use App\model\TestSoalListModel;
use App\model\TestSoalListPilModel;
use App\model\TestMemberTesModel;
use App\model\TokenModel;
use App\utils\WebUtils;
use App\helper\UserHelper;
use App\helper\UlanganHelper;

beforeAll(function() {
  UserHelper::cleanupUser(); // clean up user
  UlanganHelper::clenupUlangan(); // clean up ulangan
  
  // create user
  UserHelper::setupUser();

  // create ulangan
  UlanganHelper::setupUlangan();
});

afterAll(function() {
  UserHelper::cleanupUser(); // clean up user
  UlanganHelper::clenupUlangan(); // clean up ulangan
});

describe("F05-P01 | 1-2-3-4-5-6-7", function() {
  afterEach(function() {
    // cleanup pengerjaan
    UlanganHelper::cleanupPengerjaan();
  });

  test("F05-P01-T01 | Menampilkan soal yang sesuai jika mengklik tombol navigasi berikutnya", function() {
    // Prakondisi :
    // - sudah login
    // - berada di halaman pengerjaan ulangan
    // - salah satu soal sudah terjawab (soal pertama)
    // - berada di halaman soal kedua

    // ambil data
    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    // login dan mulai ulangan
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));
    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");
    
    // pilih jawaban di halaman soal saat ini A (soal pertama)
    $memberTest = TestMemberTesModel::getTestMemberTes();
    $keysoal = explode(",", $memberTest["value_random"]);
    $page->click("#A");

    // Kasus Uji :
    // - klik tombol navigasi berikutnya

    $keysoal2 = $keysoal[1];
    $page->click("#$keysoal2");

    // Hasil yang diharapkan :
    // - navigasi kotak untuk soal yang sudah dijawab harus berwarna hijau 
    // - navigasi kotak untuk soal saat ini harus berwarna biru
    // - nomor soal di tampilan dan di basis data harus berbeda
    // - menampilkan soal yang sesuai
    // - menampilkan pilihan ganda yang sesuai
    // - soal saat ini belum dijawab
    
    $keysoal1 = $keysoal[0];

    // - navigasi kotak untuk soal yang sudah dijawab harus berwarna hijau 
    $page->assertAttributeContains("#box-number-$keysoal1", "class", "box-number-sel-g");

    // - navigasi kotak untuk soal saat ini harus berwarna biru
    $page->assertAttributeContains("#box-number-$keysoal2", "class", "box-number-sel-b");

    $soalNo2 = TestSoalListModel::getTestSoalList($keysoal2);
    
    // - nomor soal di tampilan dan di basis data harus berbeda
    $noSoalContent = html_entity_decode($page->script("document.getElementsByClassName(\"soal-no\")[0].children[0].innerHTML;"));
    $noSoalContentRaw = explode(" ", $noSoalContent);
    $noSoalContent = $noSoalContentRaw[count($noSoalContentRaw) - 1];
    expect($soalNo2["number"])->not->tobe($noSoalContent);

    // - menampilkan soal yang sesuai
    $soalNo2Content = html_entity_decode($soalNo2["soal"]);
    $innerHtml = html_entity_decode($page->script("document.getElementsByClassName(\"soal\")[0].innerHTML;"));
    expect($innerHtml)->tobe($soalNo2Content);

    // - menampilkan pilihan ganda yang sesuai
    $pilihanGanda = TestSoalListPilModel::getTestSoalListPil($keysoal2);
    for($i = 0; $i < count($pilihanGanda); $i++) {
      $pilihanGandaContent = html_entity_decode($pilihanGanda[$i]["pilihan"]);
      $alphabet = $pilihanGanda[$i]["list_pil"];
      $option = html_entity_decode($page->script("document.getElementById(\"$alphabet\").parentElement.nextElementSibling.innerHTML;"));
      if($pilihanGanda[$i]["typepg"] == "png") {
        $expected = '<img src='.html_entity_decode($pilihanGanda[$i]['pilihan']).' alt='.$pilihanGanda[$i]['pilihan'].'/>';
        expect($option)->tobe($expected);
      } else {
        expect($option)->tobe($pilihanGandaContent);
      }
    }

    // - soal saat ini belum dijawab
    for($i = 0; $i < count($pilihanGanda); $i++) {
      $alphabet = $pilihanGanda[$i]["list_pil"];
      $checkStatus = $page->script("document.getElementById(\"$alphabet\").checked;");
      expect($checkStatus)->tobe(false);
    }
  });

  test("F05-P01-T02 | Menampilkan soal yang sesuai jika mengklik tombol navigasi sebelumnya", function() {
    // Prakondisi :
    // - sudah login
    // - berada di halaman pengerjaan ulangan
    // - salah satu soal sudah terjawab (soal pertama)
    // - berada di halaman soal ketiga

    // ambil data
    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    // login dan mulai ulangan
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));
    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");
    
    // pilih jawaban A
    $memberTest = TestMemberTesModel::getTestMemberTes();
    $keysoal = explode(",", $memberTest["value_random"]);
    $page->click("#A");

    // navigasi ke halaman soal no 3
    $keysoal3 = $keysoal[2];
    $page-> navigate(WebUtils::url("/test.php?keysoal=$keysoal3"));

    // Kasus Uji :
    // - klik tombol navigasi sebelumnya

    $keysoal2 = $keysoal[1];
    $page->click("#$keysoal2");

    // Hasil yang diharapkan :
    // - navigasi kotak untuk soal yang sudah dijawab harus berwarna hijau 
    // - navigasi kotak untuk soal saat ini harus berwarna biru
    // - nomor soal di tampilan dan di basis data harus berbeda
    // - menampilkan soal yang sesuai
    // - menampilkan pilihan ganda yang sesuai
    // - soal saat ini belum dijawab
    
    $keysoal1 = $keysoal[0];

    // - navigasi kotak untuk soal yang sudah dijawab harus berwarna hijau 
    $page->assertAttributeContains("#box-number-$keysoal1", "class", "box-number-sel-g");

    // - navigasi kotak untuk soal saat ini harus berwarna biru
    $page->assertAttributeContains("#box-number-$keysoal2", "class", "box-number-sel-b");

    $soalNo2 = TestSoalListModel::getTestSoalList($keysoal2);
    
    // - nomor soal di tampilan dan di basis data harus berbeda
    $noSoalContent = html_entity_decode($page->script("document.getElementsByClassName(\"soal-no\")[0].children[0].innerHTML;"));
    $noSoalContentRaw = explode(" ", $noSoalContent);
    $noSoalContent = $noSoalContentRaw[count($noSoalContentRaw) - 1];
    expect($soalNo2["number"])->not->tobe($noSoalContent);

    // - menampilkan soal yang sesuai
    $soalNo2Content = html_entity_decode($soalNo2["soal"]);
    $innerHtml = html_entity_decode($page->script("document.getElementsByClassName(\"soal\")[0].innerHTML;"));
    expect($innerHtml)->tobe($soalNo2Content);

    // - menampilkan pilihan ganda yang sesuai
    $pilihanGanda = TestSoalListPilModel::getTestSoalListPil($keysoal2);
    for($i = 0; $i < count($pilihanGanda); $i++) {
      $pilihanGandaContent = html_entity_decode($pilihanGanda[$i]["pilihan"]);
      $alphabet = $pilihanGanda[$i]["list_pil"];
      $option = html_entity_decode($page->script("document.getElementById(\"$alphabet\").parentElement.nextElementSibling.innerHTML;"));
      if($pilihanGanda[$i]["typepg"] == "png") {
        $expected = '<img src='.html_entity_decode($pilihanGanda[$i]['pilihan']).' alt='.$pilihanGanda[$i]['pilihan'].'/>';
        expect($option)->tobe($expected);
      } else {
        expect($option)->tobe($pilihanGandaContent);
      }
    }

    // - soal saat ini belum dijawab
    for($i = 0; $i < count($pilihanGanda); $i++) {
      $alphabet = $pilihanGanda[$i]["list_pil"];
      $checkStatus = $page->script("document.getElementById(\"$alphabet\").checked;");
      expect($checkStatus)->tobe(false);
    }
  });

  test("F05-P01-T03 | Menampilkan soal yang sesuai jika mengklik tombol navigasi angka", function() {
    // Prakondisi :
    // - sudah login
    // - berada di halaman pengerjaan ulangan
    // - salah satu soal sudah terjawab (soal pertama)
    // - berada di halaman soal ketiga

    // ambil data
    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    // login dan mulai ulangan
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));
    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");
    
    // pilih jawaban A
    $memberTest = TestMemberTesModel::getTestMemberTes();
    $keysoal = explode(",", $memberTest["value_random"]);
    $page->click("#A");

    // navigasi ke soal no 3
    $keysoal3 = $keysoal[2];
    $page-> navigate(WebUtils::url("/test.php?keysoal=$keysoal3"));

    // Kasus Uji :
    // - klik tombol navigasi kotak untuk soal no 2

    $keysoal2 = $keysoal[1];

    // pengecekan alamat pada navigasi kotak 2
    $page->assertAttributeContains(".box-number:nth-child(2)", "href", $keysoal2);

    // click navigasi kotak no 2
    $page->click("#box-number-$keysoal2");

    // Hasil yang diharapkan :
    // - navigasi kotak untuk soal yang sudah dijawab harus berwarna hijau 
    // - navigasi kotak untuk soal saat ini harus berwarna biru    
    // - nomor soal di tampilan dan di basis data harus berbeda
    // - menampilkan soal yang sesuai
    // - menampilkan pilihan ganda yang sesuai
    // - soal belum dijawab
    
    $keysoal1 = $keysoal[0];

    // - navigasi kotak untuk soal yang sudah dijawab harus berwarna hijau 
    $page->assertAttributeContains("#box-number-$keysoal1", "class", "box-number-sel-g");

    // - navigasi kotak untuk soal yang sudah dijawab harus berwarna hijau 
    $page->assertAttributeContains("#box-number-$keysoal2", "class", "box-number-sel-b");

    $soalNo2 = TestSoalListModel::getTestSoalList($keysoal2);
    
    // - nomor soal di tampilan dan di basis data harus berbeda
    $noSoalContent = html_entity_decode($page->script("document.getElementsByClassName(\"soal-no\")[0].children[0].innerHTML;"));
    $noSoalContentRaw = explode(" ", $noSoalContent);
    $noSoalContent = $noSoalContentRaw[count($noSoalContentRaw) - 1];
    expect($soalNo2["number"])->not->tobe($noSoalContent);

    // - menampilkan soal yang sesuai
    $soalNo2Content = html_entity_decode($soalNo2["soal"]);
    $innerHtml = html_entity_decode($page->script("document.getElementsByClassName(\"soal\")[0].innerHTML;"));
    expect($innerHtml)->tobe($soalNo2Content);

    // - menampilkan pilihan ganda yang sesuai
    $pilihanGanda = TestSoalListPilModel::getTestSoalListPil($keysoal2);
    for($i = 0; $i < count($pilihanGanda); $i++) {
      $pilihanGandaContent = html_entity_decode($pilihanGanda[$i]["pilihan"]);
      $alphabet = $pilihanGanda[$i]["list_pil"];
      $option = html_entity_decode($page->script("document.getElementById(\"$alphabet\").parentElement.nextElementSibling.innerHTML;"));
      if($pilihanGanda[$i]["typepg"] == "png") {
        $expected = '<img src='.html_entity_decode($pilihanGanda[$i]['pilihan']).' alt='.$pilihanGanda[$i]['pilihan'].'/>';
        expect($option)->tobe($expected);
      } else {
        expect($option)->tobe($pilihanGandaContent);
      }
    }

    // - soal belum dijawab untuk soal saat ini
    for($i = 0; $i < count($pilihanGanda); $i++) {
      $alphabet = $pilihanGanda[$i]["list_pil"];
      $checkStatus = $page->script("document.getElementById(\"$alphabet\").checked;");
      expect($checkStatus)->tobe(false);
    }
  });

  test("F05-P01-T04 | Menampilkan soal yang sesuai jika mengklik tombol navigasi angka saat pertama masuk", function() {
    // Prakondisi :
    // - sudah login
    // - berada di halaman pengerjaan ulangan
    // - berada di halaman soal pertama
    // - salah satu soal sudah terjawab (soal pertama)

    // ambil data
    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    // login dan mulai ulangan
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));
    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");
    
    // pilih jawaban A
    $memberTest = TestMemberTesModel::getTestMemberTes();
    $keysoal = explode(",", $memberTest["value_random"]);
    $page->click("#A");

    // Kasus Uji :
    // - klik tombol navigasi kotak untuk soal kedua

    $keysoal2 = $keysoal[1];

    // pengecekan alamat pada navigasi kotak 2
    $page->assertAttributeContains(".box-number:nth-child(2)", "href", $keysoal2);

    // pengecekan alamat pada navigasi kotak 2
    $page->click("#box-number-$keysoal2");

    // Hasil yang diharapkan :
    // - navigasi kotak untuk soal yang sudah dijawab harus berwarna hijau 
    // - navigasi kotak untuk soal saat ini harus berwarna biru    
    // - nomor soal di tampilan dan di basis data harus berbeda
    // - menampilkan soal yang sesuai
    // - menampilkan pilihan ganda yang sesuai
    // - soal belum dijawab
    
    $keysoal1 = $keysoal[0];

    // - navigasi kotak untuk soal yang sudah dijawab harus berwarna hijau 
    $page->assertAttributeContains("#box-number-$keysoal1", "class", "box-number-sel-g");

    // - navigasi kotak untuk soal saat ini harus berwarna biru    
    $page->assertAttributeContains("#box-number-$keysoal2", "class", "box-number-sel-b");

    $soalNo2 = TestSoalListModel::getTestSoalList($keysoal2);
    
    // - nomor soal di tampilan dan di basis data harus berbeda
    $noSoalContent = html_entity_decode($page->script("document.getElementsByClassName(\"soal-no\")[0].children[0].innerHTML;"));
    $noSoalContentRaw = explode(" ", $noSoalContent);
    $noSoalContent = $noSoalContentRaw[count($noSoalContentRaw) - 1];
    expect($soalNo2["number"])->not->tobe($noSoalContent);

    // - menampilkan soal yang sesuai
    $soalNo2Content = html_entity_decode($soalNo2["soal"]);
    $innerHtml = html_entity_decode($page->script("document.getElementsByClassName(\"soal\")[0].innerHTML;"));
    expect($innerHtml)->tobe($soalNo2Content);

    // - menampilkan pilihan ganda yang sesuai
    $pilihanGanda = TestSoalListPilModel::getTestSoalListPil($keysoal2);
    for($i = 0; $i < count($pilihanGanda); $i++) {
      $pilihanGandaContent = html_entity_decode($pilihanGanda[$i]["pilihan"]);
      $alphabet = $pilihanGanda[$i]["list_pil"];
      $option = html_entity_decode($page->script("document.getElementById(\"$alphabet\").parentElement.nextElementSibling.innerHTML;"));
      if($pilihanGanda[$i]["typepg"] == "png") {
        $expected = '<img src='.html_entity_decode($pilihanGanda[$i]['pilihan']).' alt='.$pilihanGanda[$i]['pilihan'].'/>';
        expect($option)->tobe($expected);
      } else {
        expect($option)->tobe($pilihanGandaContent);
      }
    }

    // - soal belum dijawab untuk soal saat ini
    for($i = 0; $i < count($pilihanGanda); $i++) {
      $alphabet = $pilihanGanda[$i]["list_pil"];
      $checkStatus = $page->script("document.getElementById(\"$alphabet\").checked;");
      expect($checkStatus)->tobe(false);
    }
  });

  test("F05-P01-T05 | Hanya menampilkan tombol navigasi berikutnya jika berada di halaman pertama", function() {
    // Prakondisi :
    // - sudah login
    // - berada di halaman pengerjaan ulangan
    // - berada di halaman soal pertama

    // ambil data
    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    // login dan mulai ulangan
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));
    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");
    
    // Kasus Uji :
    // - buka halaman pertama

    $memberTest = TestMemberTesModel::getTestMemberTes();
    $keysoal = explode(",", $memberTest["value_random"]);
    $keysoal1 = $keysoal[0];
    $page->navigate(WebUtils::url("/test.php?keysoal=$keysoal1"));

    // Hasil yang diharapkan :
    // - navigasi kotak untuk soal saat ini harus berwarna biru    
    // - nomor soal di tampilan dan di basis data harus berbeda
    // - menampilkan soal yang sesuai
    // - menampilkan pilihan ganda yang sesuai
    // - hanya menampilkan tombol navigasi berikutnya
    
    // - navigasi kotak untuk soal saat ini harus berwarna biru    
    $page->assertAttributeContains("#box-number-$keysoal1", "class", "box-number-sel-b");

    $soalNo1 = TestSoalListModel::getTestSoalList($keysoal1);
    
    // - nomor soal di tampilan dan di basis data harus berbeda
    $noSoalContent = html_entity_decode($page->script("document.getElementsByClassName(\"soal-no\")[0].children[0].innerHTML;"));
    $noSoalContentRaw = explode(" ", $noSoalContent);
    $noSoalContent = $noSoalContentRaw[count($noSoalContentRaw) - 1];
    expect($soalNo1["number"])->not->tobe($noSoalContent);

    // - menampilkan soal yang sesuai
    $soalNo1Content = html_entity_decode($soalNo1["soal"]);
    $innerHtml = html_entity_decode($page->script("document.getElementsByClassName(\"soal\")[0].innerHTML;"));
    expect($innerHtml)->tobe($soalNo1Content);

    // - menampilkan pilihan ganda yang sesuai
    $pilihanGanda = TestSoalListPilModel::getTestSoalListPil($keysoal1);
    for($i = 0; $i < count($pilihanGanda); $i++) {
      $pilihanGandaContent = html_entity_decode($pilihanGanda[$i]["pilihan"]);
      $alphabet = $pilihanGanda[$i]["list_pil"];
      $option = html_entity_decode($page->script("document.getElementById(\"$alphabet\").parentElement.nextElementSibling.innerHTML;"));
      if($pilihanGanda[$i]["typepg"] == "png") {
        $expected = '<img src='.html_entity_decode($pilihanGanda[$i]['pilihan']).' alt='.$pilihanGanda[$i]['pilihan'].'/>';
        expect($option)->tobe($expected);
      } else {
        expect($option)->tobe($pilihanGandaContent);
      }
    }

    // - hanya menampilkan tombol navigasi berikutnya
    $numberOfNavigation = $page->script("document.getElementsByClassName(\"nav-soal\")[0].children.length;");
    expect($numberOfNavigation)->toBe(1);
    $navigationContent = html_entity_decode($page->script("document.getElementsByClassName(\"nav-soal\")[0].children[0].innerHTML;"));
    expect($navigationContent)->toBe("Berikutnya >");
  });

  test("F05-P01-T06 | Hanya menampilkan tombol navigasi berikutnya dan selesai jika berada di halaman terakhir", function() {
    // Prakondisi :
    // - sudah login
    // - berada di halaman pengerjaan ulangan
    // - berada di halaman soal terakhir

    // ambil data
    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    // login dan mulai ulangan
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));
    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");
    
    // Kasus Uji :
    // - buka halaman terakhir

    $memberTest = TestMemberTesModel::getTestMemberTes();
    $keysoal = explode(",", $memberTest["value_random"]);
    $keysoalLast = $keysoal[count($keysoal) - 1];
    $page->navigate(WebUtils::url("/test.php?keysoal=$keysoalLast"));

    // Hasil yang diharapkan :
    // - navigasi kotak untuk soal saat ini harus berwarna biru    
    // - nomor soal di tampilan dan di basis data harus berbeda
    // - menampilkan soal yang sesuai
    // - menampilkan pilihan ganda yang sesuai
    // - hanya menampilkan tombol berikutnya dan tombol selesai
    
    // - navigasi kotak untuk soal saat ini harus berwarna biru    
    $page->assertAttributeContains("#box-number-$keysoalLast", "class", "box-number-sel-b");

    $soalLast = TestSoalListModel::getTestSoalList($keysoalLast);
    
    // - nomor soal di tampilan dan di basis data harus berbeda
    $noSoalContent = html_entity_decode($page->script("document.getElementsByClassName(\"soal-no\")[0].children[0].innerHTML;"));
    $noSoalContentRaw = explode(" ", $noSoalContent);
    $noSoalContent = $noSoalContentRaw[count($noSoalContentRaw) - 1];
    expect($soalLast["number"])->not->tobe($noSoalContent);

    // - menampilkan soal yang sesuai
    $soalLastContent = html_entity_decode($soalLast["soal"]);
    $innerHtml = html_entity_decode($page->script("document.getElementsByClassName(\"soal\")[0].innerHTML;"));
    expect($innerHtml)->tobe($soalLastContent);

    // - menampilkan pilihan ganda yang sesuai
    $pilihanGanda = TestSoalListPilModel::getTestSoalListPil($keysoalLast);
    for($i = 0; $i < count($pilihanGanda); $i++) {
      $pilihanGandaContent = html_entity_decode($pilihanGanda[$i]["pilihan"]);
      $alphabet = $pilihanGanda[$i]["list_pil"];
      $option = html_entity_decode($page->script("document.getElementById(\"$alphabet\").parentElement.nextElementSibling.innerHTML;"));
      if($pilihanGanda[$i]["typepg"] == "png") {
        $expected = '<img src='.html_entity_decode($pilihanGanda[$i]['pilihan']).' alt='.$pilihanGanda[$i]['pilihan'].'/>';
        expect($option)->tobe($expected);
      } else {
        expect($option)->tobe($pilihanGandaContent);
      }
    }

    // - hanya menampilkan tombol berikutnya dan tombol selesai
    $numberOfNavigation = $page->script("document.getElementsByClassName(\"nav-soal\")[0].children.length;");
    expect($numberOfNavigation)->toBe(2);
    $navigationContent1 = html_entity_decode($page->script("document.getElementsByClassName(\"nav-soal\")[0].children[0].innerHTML;"));
    expect($navigationContent1)->toBe("< Sebelumnya");
    $navigationContent2 = html_entity_decode($page->script("document.getElementsByClassName(\"nav-soal\")[0].children[1].innerHTML;"));
    expect($navigationContent2)->toBe("Selesai >");
  });

  test("F05-P01-T07 | Menampilkan pilihan ganda yang sudah dijawab jika masuk ke halaman soal yang sudah dijawab", function() {
    // Prakondisi :
    // - sudah login
    // - berada di halaman pengerjaan ulangan
    // - salah satu soal sudah terjawab (soal pertama)
    // - berada di halaman soal ketiga

    // ambil data
    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    // login dan mulai ulangan
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));
    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");
    
    // pilih jawaban A
    $memberTest = TestMemberTesModel::getTestMemberTes();
    $keysoal = explode(",", $memberTest["value_random"]);
    $page->click("#A");

    // navigasi ke soal no 3
    $keysoal3 = $keysoal[2];
    $page-> navigate(WebUtils::url("/test.php?keysoal=$keysoal3"));

    // Kasus Uji :
    // - navigasi ke soal yang sudah dijawab (soal pertama)

    $keysoal1 = $keysoal[0];

    // pengecekan alamat pada navigasi kotak 1
    $page->assertAttributeContains(".box-number:nth-child(1)", "href", $keysoal1);

    // pengecekan alamat pada navigasi kotak 2
    $page->click("#box-number-$keysoal1");

    // Hasil yang diharapkan :
    // - navigasi kotak untuk soal saat ini harus berwarna biru    
    // - nomor soal di tampilan dan di basis data harus berbeda
    // - menampilkan soal yang sesuai
    // - menampilkan pilihan ganda yang sesuai
    // - salah satu input radio sudah terisi
    
    // - navigasi kotak untuk soal saat ini harus berwarna biru    
    $page->assertAttributeContains("#box-number-$keysoal1", "class", "box-number-sel-b");

    $soalNo1 = TestSoalListModel::getTestSoalList($keysoal1);
    
    // - nomor soal di tampilan dan di basis data harus berbeda
    $noSoalContent = html_entity_decode($page->script("document.getElementsByClassName(\"soal-no\")[0].children[0].innerHTML;"));
    $noSoalContentRaw = explode(" ", $noSoalContent);
    $noSoalContent = $noSoalContentRaw[count($noSoalContentRaw) - 1];
    expect($soalNo1["number"])->not->tobe($noSoalContent);

    // - menampilkan soal yang sesuai
    $soalNo2Content = html_entity_decode($soalNo1["soal"]);
    $innerHtml = html_entity_decode($page->script("document.getElementsByClassName(\"soal\")[0].innerHTML;"));
    expect($innerHtml)->tobe($soalNo2Content);

    // - menampilkan pilihan ganda yang sesuai
    $pilihanGanda = TestSoalListPilModel::getTestSoalListPil($keysoal1);
    for($i = 0; $i < count($pilihanGanda); $i++) {
      $pilihanGandaContent = html_entity_decode($pilihanGanda[$i]["pilihan"]);
      $alphabet = $pilihanGanda[$i]["list_pil"];
      $option = html_entity_decode($page->script("document.getElementById(\"$alphabet\").parentElement.nextElementSibling.innerHTML;"));
      if($pilihanGanda[$i]["typepg"] == "png") {
        $expected = '<img src='.html_entity_decode($pilihanGanda[$i]['pilihan']).' alt='.$pilihanGanda[$i]['pilihan'].'/>';
        expect($option)->tobe($expected);
      } else {
        expect($option)->tobe($pilihanGandaContent);
      }
    }

    // - salah satu input radio sudah terisi
    $checkStatus = $page->script("document.getElementById(\"A\").checked;");
    expect($checkStatus)->tobe(true);
  });
});

describe("F05-P02 | 1-2-8-3-4-5-6-7", function() {
  afterEach(function() {
    // cleanup pengerjaan
    UlanganHelper::cleanupPengerjaan();
  });

  test("F05-P02-T01 | Menampilkan kembali status pengerjaan sebelumnya (soal belum dijawab) jika data pengerjaan pada session hilang", function() {
    // prakondisi :
    // - sudah mulai mengerjakan soal sebelumnya
    // - sebelumnya, tidak menjawab soal
    // - session pengerjaan hilang

    // ambil data
    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    // login dan mulai ulangan
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));
    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");

    // buka halaman soal pertama
    $memberTest = TestMemberTesModel::getTestMemberTes();
    $keysoal = explode(",", $memberTest["value_random"]);
    $keysoal1 = $keysoal[0];
    $soalNo1 = TestSoalListModel::getTestSoalList($keysoal1);
    $page->navigate(WebUtils::url("/test.php?keysoal=$keysoal1"));

    // ambil content untuk dibandingkan nanti
    $noSoalContent1 = html_entity_decode($page->script("document.getElementsByClassName(\"soal-no\")[0].children[0].innerHTML;"));
    $noSoalContentRaw1 = explode(" ", $noSoalContent1);
    $noSoalContent1 = $noSoalContentRaw1[count($noSoalContentRaw1) - 1];
    $innerHtml1 = html_entity_decode($page->script("document.getElementsByClassName(\"soal\")[0].innerHTML;"));
    
    // kasus uji :
    // - hapus PHPSESSID di browser
    // - login kembali
    // - masuk ke halaman pengerjaan soal

    // login kembali
    $page2 = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page2->fill("#nis", $correctNis);
    $page2->fill("#password", $correctPassword);
    $page2->click('#submit');

    // lanjut mengerjakan
    $correctToken = TokenModel::getToken()["token"];
    $page2->fill("input[name=\"token\"]", $correctToken);
    $page2->click("input[value=\"Lanjut Mengerjakan\"]");

    // hasil yang diharapkan :
    // status pengerjaan masih sama seperti sebelumnya, mencakup:
    // - soal yang belum dijawab masih tidak terjawab
    // - soal acak masih sama seperti sebelumnya
    // - navigasi kotak masih sama seperti sebelumnya

    // buka halaman soal pertama
    $page2->navigate(WebUtils::url("/test.php?keysoal=$keysoal1"));
    
    // - nomor soal harus sama seperti sebelumnya | soal acak masih sama seperti sebelumnya
    $noSoalContent2 = html_entity_decode($page2->script("document.getElementsByClassName(\"soal-no\")[0].children[0].innerHTML;"));
    $noSoalContentRaw2 = explode(" ", $noSoalContent2);
    $noSoalContent2 = $noSoalContentRaw2[count($noSoalContentRaw2) - 1];
    expect($noSoalContent2)->tobe($noSoalContent1);

    // - menampilkan soal yang sama seperti sebelumnya | soal acak masih sama seperti sebelumnya
    $innerHtml2 = html_entity_decode($page2->script("document.getElementsByClassName(\"soal\")[0].innerHTML;"));
    expect($innerHtml2)->tobe($innerHtml1);

    // - menampilkan pilihan ganda yang sama seperti sebelumnya | soal acak masih sama seperti sebelumnya
    $pilihanGanda = TestSoalListPilModel::getTestSoalListPil($keysoal1); // hanya digunakan untuk jumlah perulangan
    for($i = 0; $i < count($pilihanGanda); $i++) {
      $alphabet = $pilihanGanda[$i]["list_pil"];
      $option1 = html_entity_decode($page->script("document.getElementById(\"$alphabet\").parentElement.nextElementSibling.innerHTML;"));
      $option2 = html_entity_decode($page2->script("document.getElementById(\"$alphabet\").parentElement.nextElementSibling.innerHTML;"));
      expect($option2)->tobe($option1);
    }

    // - soal yang belum dijawab masih tidak terjawab
    for($i = 0; $i < count($pilihanGanda); $i++) {
      $alphabet = $pilihanGanda[$i]["list_pil"];
      $checkStatus = $page2->script("document.getElementById(\"$alphabet\").checked;");
      expect($checkStatus)->tobe(false);
    }

    // - navigasi kotak untuk soal saat ini harus berwarna biru | navigasi kotak masih sama seperti sebelumnya
    $page2->assertAttributeContains("#box-number-$keysoal1", "class", "box-number-sel-b");
  });

  test("F05-P02-T02 | Menampilkan kembali status pengerjaan sebelumnya (soal sudah dijawab) jika data pengerjaan pada session hilang", function() {
    // prakondisi :
    // - sudah mulai mengerjakan soal sebelumnya
    // - sebelumnya, sudah menjawab soal
    // - session pengerjaan hilang

    // ambil data
    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    // login dan mulai ulangan
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));
    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");

    // buka halaman soal pertama dan jawab soal
    $memberTest = TestMemberTesModel::getTestMemberTes();
    $keysoal = explode(",", $memberTest["value_random"]);
    $keysoal1 = $keysoal[0];
    $soalNo1 = TestSoalListModel::getTestSoalList($keysoal1);
    $page->navigate(WebUtils::url("/test.php?keysoal=$keysoal1"));
    $page->click("#A");

    // ambil content di halaman soal pertama untuk dibandingkan nanti
    $noSoalContent1 = html_entity_decode($page->script("document.getElementsByClassName(\"soal-no\")[0].children[0].innerHTML;"));
    $noSoalContentRaw1 = explode(" ", $noSoalContent1);
    $noSoalContent1 = $noSoalContentRaw1[count($noSoalContentRaw1) - 1];
    $innerHtml1 = html_entity_decode($page->script("document.getElementsByClassName(\"soal\")[0].innerHTML;"));
    $pilihanGanda = TestSoalListPilModel::getTestSoalListPil($keysoal1); // hanya digunakan untuk jumlah perulangan
    $options1 = [];
    for($i = 0; $i < count($pilihanGanda); $i++) {
      $alphabet = $pilihanGanda[$i]["list_pil"];
      $options1[] = html_entity_decode($page->script("document.getElementById(\"$alphabet\").parentElement.nextElementSibling.innerHTML;"));
    }

    // buka halaman soal ketiga dan jawab soal
    $keysoal3 = $keysoal[2];
    $page->navigate(WebUtils::url("/test.php?keysoal=$keysoal3"));
    $page->click("#B");
    
    // kasus uji :
    // - hapus PHPSESSID di browser
    // - login kembali
    // - masuk ke halaman pengerjaan soal

    // login kembali
    $page2 = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page2->fill("#nis", $correctNis);
    $page2->fill("#password", $correctPassword);
    $page2->click('#submit');

    // lanjut mengerjakan
    $correctToken = TokenModel::getToken()["token"];
    $page2->fill("input[name=\"token\"]", $correctToken);
    $page2->click("input[value=\"Lanjut Mengerjakan\"]");

    $page2->wait(200000);

    // hasil yang diharapkan :
    // status pengerjaan masih sama seperti sebelumnya, mencakup:
    // - soal yang sudah dijawab masih terjawab
    // - soal acak masih sama seperti sebelumnya
    // - navigasi kotak masih sama seperti sebelumnya

    // buka halaman soal pertama
    $page2->navigate(WebUtils::url("/test.php?keysoal=$keysoal1"));
    
    // - nomor soal harus sama seperti sebelumnya | soal acak masih sama seperti sebelumnya
    $noSoalContent2 = html_entity_decode($page2->script("document.getElementsByClassName(\"soal-no\")[0].children[0].innerHTML;"));
    $noSoalContentRaw2 = explode(" ", $noSoalContent2);
    $noSoalContent2 = $noSoalContentRaw2[count($noSoalContentRaw2) - 1];
    expect($noSoalContent2)->tobe($noSoalContent1);

    // - menampilkan soal yang sama seperti sebelumnya | soal acak masih sama seperti sebelumnya
    $innerHtml2 = html_entity_decode($page2->script("document.getElementsByClassName(\"soal\")[0].innerHTML;"));
    expect($innerHtml2)->tobe($innerHtml1);

    // - menampilkan pilihan ganda yang sama seperti sebelumnya | soal acak masih sama seperti sebelumnya
    for($i = 0; $i < count($pilihanGanda); $i++) {
      $alphabet = $pilihanGanda[$i]["list_pil"];
      $option2 = html_entity_decode($page2->script("document.getElementById(\"$alphabet\").parentElement.nextElementSibling.innerHTML;"));
      expect($option2)->tobe($options1[$i]);
    }

    // - soal saat ini sudah dijawab seperti sebelumnya | soal yang sudah dijawab masih terjawab
    for($i = 0; $i < count($pilihanGanda); $i++) {
      $alphabet = $pilihanGanda[$i]["list_pil"];
      $checkStatus = $page2->script("document.getElementById(\"$alphabet\").checked;");
      if($alphabet == "A") {
        expect($checkStatus)->tobe(true);
      } else {
        expect($checkStatus)->tobe(false);
      }
    }

    // - navigasi kotak untuk soal saat ini harus berwarna biru | navigasi kotak masih sama seperti sebelumnya
    $page2->assertAttributeContains("#box-number-$keysoal1", "class", "box-number-sel-b");

    // - navigasi kotak untuk soal yang sudah dijawab harus berwarna hijau | navigasi kotak masih sama seperti sebelumnya
    $page2->assertAttributeContains("#box-number-$keysoal3", "class", "box-number-sel-g");
  });
});

describe("F05-P03 | 1-2-3-4-9-6-7", function() {
  afterEach(function() {
    // cleanup pengerjaan
    UlanganHelper::cleanupPengerjaan();
  });
  
  test("F05-P03-T01 | Menampilkan menampilkan pesan \"Soal tidak ditemukan\" jika param keysoal tidak valid", function() {
    // prakondisi :
    // - sudah login
    // - berada di halaman pengerjaan ulangan

    // ambil data
    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    // login dan mulai ulangan
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));
    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");    
    
    // Kasus Uji :
    // - berpindah halaman soal dengan mengubah param keysoal menjadi tidak valid
    $invalidKeysoal = "ieOf92Iw";
    $page->navigate(WebUtils::url("/test.php?keysoal=$invalidKeysoal"));

    // hasil yang diharapkan :
    // - menampilkan pesan "Soal tidak ditemukan."

    $page->assertSee("Soal tidak ditemukan.");
  });

  test("F05-P03-T02 | Menampilkan soal pertama dengan data yang benar jika param keysoal kosong", function() {
    // prakondisi :
    // - sudah login
    // - berada di halaman pengerjaan ulangan

    // ambil data
    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    // login dan mulai ulangan
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));
    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");    
    
    // Kasus Uji :
    // - berpindah halaman soal dengan mengubah param keysoal menjadi kosong
    $page->navigate(WebUtils::url("/test.php?keysoal="));

    // hasil yang diharapkan :
    // - menampilkan soal pertama dengan data yang benar
    // - menampilkan pilihan ganda yang sesuai
    // - navigasi kotak untuk soal pertama harus berwarna biru
    // - nomor di tampilan dan di basis data harus berbeda

    $memberTest = TestMemberTesModel::getTestMemberTes();
    $keysoal = explode(",", $memberTest["value_random"]);
    $keysoal1 = $keysoal[0];
    $soalNo1 = TestSoalListModel::getTestSoalList($keysoal1);


    // - nomor soal di tampilan dan di basis data harus berbeda
    $page->assertPresent(".soal-no");
    $noSoalContent = html_entity_decode($page->script("document.getElementsByClassName(\"soal-no\")[0].children[0].innerHTML;"));
    $noSoalContentRaw = explode(" ", $noSoalContent);
    $noSoalContent = $noSoalContentRaw[count($noSoalContentRaw) - 1];
    expect($soalNo1["number"])->not->tobe($noSoalContent);

    // - menampilkan soal yang sesuai
    $page->assertPresent(".soal");
    $soalNo1Content = html_entity_decode($soalNo1["soal"]);
    $innerHtml = html_entity_decode($page->script("document.getElementsByClassName(\"soal\")[0].innerHTML;"));
    expect($innerHtml)->tobe($soalNo1Content);

    // - menampilkan pilihan ganda yang sesuai
    $pilihanGanda = TestSoalListPilModel::getTestSoalListPil($keysoal1);
    for($i = 0; $i < count($pilihanGanda); $i++) {
      $pilihanGandaContent = html_entity_decode($pilihanGanda[$i]["pilihan"]);
      $alphabet = $pilihanGanda[$i]["list_pil"];
      $page->assertPresent("#$alphabet");
      $option = html_entity_decode($page->script("document.getElementById(\"$alphabet\").parentElement.nextElementSibling.innerHTML;"));
      if($pilihanGanda[$i]["typepg"] == "png") {
        $expected = '<img src='.html_entity_decode($pilihanGanda[$i]['pilihan']).' alt='.$pilihanGanda[$i]['pilihan'].'/>';
        expect($option)->tobe($expected);
      } else {
        expect($option)->tobe($pilihanGandaContent);
      }
    }
  });

  test("F05-P03-T03 | Menampilkan soal pertama dengan data yang benar jika param keysoal tidak ada", function() {
    // prakondisi :
    // - sudah login
    // - berada di halaman pengerjaan ulangan

    // ambil data
    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    // login dan mulai ulangan
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));
    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");    
    
    // Kasus Uji :
    // - berpindah halaman soal dengan mengubah param keysoal menjadi tidak ada
    $page->navigate(WebUtils::url("/test.php"));

    // hasil yang diharapkan :
    // - menampilkan soal pertama dengan data yang benar
    // - menampilkan pilihan ganda yang sesuai
    // - navigasi kotak untuk soal pertama harus berwarna biru
    // - nomor di tampilan dan di basis data harus berbeda

    $memberTest = TestMemberTesModel::getTestMemberTes();
    $keysoal = explode(",", $memberTest["value_random"]);
    $keysoal1 = $keysoal[0];
    $soalNo1 = TestSoalListModel::getTestSoalList($keysoal1);


    // - nomor soal di tampilan dan di basis data harus berbeda
    $page->assertPresent(".soal-no");
    $noSoalContent = html_entity_decode($page->script("document.getElementsByClassName(\"soal-no\")[0].children[0].innerHTML;"));
    $noSoalContentRaw = explode(" ", $noSoalContent);
    $noSoalContent = $noSoalContentRaw[count($noSoalContentRaw) - 1];
    expect($soalNo1["number"])->not->tobe($noSoalContent);

    // - menampilkan soal yang sesuai
    $page->assertPresent(".soal");
    $soalNo1Content = html_entity_decode($soalNo1["soal"]);
    $innerHtml = html_entity_decode($page->script("document.getElementsByClassName(\"soal\")[0].innerHTML;"));
    expect($innerHtml)->tobe($soalNo1Content);

    // - menampilkan pilihan ganda yang sesuai
    $pilihanGanda = TestSoalListPilModel::getTestSoalListPil($keysoal1);
    for($i = 0; $i < count($pilihanGanda); $i++) {
      $pilihanGandaContent = html_entity_decode($pilihanGanda[$i]["pilihan"]);
      $alphabet = $pilihanGanda[$i]["list_pil"];
      $page->assertPresent("#$alphabet");
      $option = html_entity_decode($page->script("document.getElementById(\"$alphabet\").parentElement.nextElementSibling.innerHTML;"));
      if($pilihanGanda[$i]["typepg"] == "png") {
        $expected = '<img src='.html_entity_decode($pilihanGanda[$i]['pilihan']).' alt='.$pilihanGanda[$i]['pilihan'].'/>';
        expect($option)->tobe($expected);
      } else {
        expect($option)->tobe($pilihanGandaContent);
      }
    }
  });
});

describe("F05-P04 | 1-2-3-4-5-6-10-7", function() {
  test("F05-P04-T01 | Menampilkan menampilkan pesan \"Soal tidak ditemukan\" jika soal di basis data hilang", function() {
    // prakondisi :
    // - sudah login
    // - berada di halaman pengerjaan ulangan
    // - soal tidak ada di basis data
  
    // ambil data
    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");
  
    // login dan mulai ulangan
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));
    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");    
  
    // kasus uji
    // - masuk ke halaman ulangan dengan param keysoal yang benar

    TestSoalListModel::deleteTestSoalList();

    $memberTest = TestMemberTesModel::getTestMemberTes();
    $keysoal = explode(",", $memberTest["value_random"]);
    $keysoal1 = $keysoal[0];
    $page->navigate(WebUtils::url("/test.php?keysoal=$keysoal1"));

    // hasil yang diharapkan :
    // - menampilkan pesan "Soal tidak ditemukan."

    $page->assertSee("Soal tidak ditemukan.");
  });
});