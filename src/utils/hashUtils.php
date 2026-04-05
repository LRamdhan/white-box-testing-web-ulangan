<?php

namespace App\utils;

class HashUtils {
  public static function generateAlphabetHash($long='10'){
		$var='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$key='';
	
		for($i=0;$i<$long;$i++)
		{
			$key.=$var[rand(0,strlen($var)-1)];
		}
		return $key;
	}
}