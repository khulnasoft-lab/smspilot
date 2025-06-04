<?php

class MVC_Library_Hash {
	public function encode($val, $salt = false, $padding = 30)
	{
		$hashids = new Hashids\Hashids($salt, $padding);
		return $hashids->encode($val);
	}

	public function decode($hash, $salt = false, $padding = 30)
	{
		$hashids = new Hashids\Hashids($salt, $padding);
		return (isset($hashids->decode($hash)[0]) ? $hashids->decode($hash)[0] : false);
	}
}