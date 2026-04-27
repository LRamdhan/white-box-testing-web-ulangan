<?php


include "./vendor/autoload.php";

use App\model\TestSoalListModel;
use App\model\TestSoalListPilModel;
use App\model\TestMemberTesModel;
use App\model\TokenModel;
use App\utils\WebUtils;
use App\helper\UserHelper;
use App\helper\UlanganHelper;


use GuzzleHttp\Client;


beforeAll(function() {
  // clean up and create user
  UserHelper::cleanupUser();
  UserHelper::setupUser();

  // clean up and create ulangan
  UlanganHelper::clenupUlangan();
  UlanganHelper::setupUlangan();
});

afterAll(function() {
  // clean up user
  UserHelper::cleanupUser();

  // celan up ulangan
  UlanganHelper::clenupUlangan();
});


// Pengujian :
// harus mengimplementasikan api http yang benar.

// api/savepil.php?keymember=1122334455&numsoal=4&keysoal=gAlFKzUqCt&pilsoal=B&keylist=zbIvWhKJEg&info=999&user=41904

// test case :
// keymember salah -> response 400
// numsoal salah -> response 400
// keysoal salah -> response 400
// pilsoal salah -> response 400
// keylist salah -> response 400
// info salah -> response 400
// user salah -> response 400



test("coba", function () {

  // try {

  //   $client = new Client([
  //     // Base URI is used with relative requests
  //     'base_uri' => 'https://www.fruityvice.com/api/',
  //     // You can set any number of default request options.
  //     'timeout'  => 2.0,
  //   ]);
  
  
  //   $response = $client->request('GET', 'fruit/all');
  //   $responseCode = $response->getStatusCode();
  //   $responseBody = $response->getBody()->getContents();
  
  
  //   echo "\n\n";
  //   echo $responseCode;
  //   echo "\n\n";
  //   echo $responseBody;
  //   echo "\n\n";
  // } catch(Exception $error) {
  //   echo "\n\n";
  //   echo "terjadi error";
  //   echo $error->getMessage();
  //   echo "\n\n";
  // }

  // expect($responseCode)->toBe(200);
  expect(true)->toBeTrue();
});