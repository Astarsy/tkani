<?php
class Msg{
	public static function encode($txt){
		return base64_encode(convert_uuencode($txt));
	}
	public static function decode($txt){
		return convert_uudecode(base64_decode($txt));
	}
}