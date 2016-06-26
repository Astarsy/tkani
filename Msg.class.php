<?php
class Msg{
	public static function encode($txt){
		return urlencode($txt);
	}
	public static function decode($txt){
		return urldecode($txt);
	}
}