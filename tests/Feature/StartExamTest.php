<?php

include "./vendor/autoload.php";

use App\model\SoalSetModel;

beforeAll(function() {
  // setup/buat soal
  SoalSetModel::setupSoal();
});

afterAll(function() {
  // clear soal
  SoalSetModel::clearSoal();
});

it('example test', function () {
  expect(true)->toBeTrue();
});