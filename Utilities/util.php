<?php

ini_set("display_errors",2);
ini_set('error_reporting', E_ALL | E_STRICT);
date_default_timezone_set('UTC');

class utility
{

	public static function debug($message)
	{
		echo "<pre>";print_r($message);"<pre/>";
	}

	public static function get_values_for_keys($mapping, $key) {
		foreach($mapping as $value) {
			$output_arr[] = !is_array($value) ? $value->{$key} : $value[$key];
		}
		return $output_arr;
	}

	public static function getYQLResult($yql_query) {

		$yql_base_url  = "http://query.yahooapis.com/v1/public/yql";
		$yql_query_url = $yql_base_url . "?q=" . rawurlencode($yql_query);
		$yql_query_url .= "&format=json";

		$session = curl_init();
		curl_setopt_array($session, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL            => $yql_query_url,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_SSL_VERIFYPEER =>false,
			CURLOPT_USERAGENT      => 'SNARC'
			));
		$json = curl_exec($session);
		$phpObj =  json_decode($json);

		if(!is_null($phpObj) && !empty($phpObj)){
			if (isset($phpObj->query->results) && count($phpObj->query->results) > 0 )
				return $phpObj->query->results;
		}
	}

	public static function getCURLResult($query) {
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL            => $query,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_SSL_VERIFYPEER =>false,
			CURLOPT_USERAGENT      => 'SNARC'
			));
		$phpObj = curl_exec($curl);
		curl_close($curl);
		if(!is_null($phpObj) && !empty($phpObj)){
			return $phpObj;
		}
	}

	public static function getCURLPostResult($url, $data) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$phpObj = curl_exec ($ch);
		curl_close ($ch);
		if(!is_null($phpObj) && !empty($phpObj)){
			return json_decode($phpObj);
		}
	}

	public static function sort_array_on_time($first_item, $second_item){
		if ($first_item["time"] == $second_item["time"]) return 0;
		return ($first_item["time"] > $second_item["time"]) ? -1 : 1;
	}

	public static function timeAgo($timestamp) {
		$timestamp      = (int) $timestamp;
		$current_time   = time();
		$diff           = $current_time - $timestamp;
		$intervals      = array ('year' => 31556926, 'month' => 2629744, 'week' => 604800, 'day' => 86400, 'hour' => 3600, 'minute'=> 60);
		if ($diff == 0)
		{
			return 'just now';
		}
		if ($diff < 60)
		{
			return $diff == 1 ? $diff . ' second ago' : $diff . ' seconds ago';
		}
		if ($diff >= 60 && $diff < $intervals['hour'])
		{
			$diff = floor($diff/$intervals['minute']);
			return $diff == 1 ? $diff . ' minute ago' : $diff . ' minutes ago';
		}
		if ($diff >= $intervals['hour'] && $diff < $intervals['day'])
		{
			$diff = floor($diff/$intervals['hour']);
			return $diff == 1 ? $diff . ' hour ago' : $diff . ' hours ago';
		}
		if ($diff >= $intervals['day'] && $diff < $intervals['week'])
		{
			$diff = floor($diff/$intervals['day']);
			return $diff == 1 ? $diff . ' day ago' : $diff . ' days ago';
		}
		if ($diff >= $intervals['week'] && $diff < $intervals['month'])
		{
			$diff = floor($diff/$intervals['week']);
			return $diff == 1 ? $diff . ' week ago' : $diff . ' weeks ago';
		}
		if ($diff >= $intervals['month'] && $diff < $intervals['year'])
		{
			$diff = floor($diff/$intervals['month']);
			return $diff == 1 ? $diff . ' month ago' : $diff . ' months ago';
		}
		if ($diff >= $intervals['year'])
		{
			$diff = floor($diff/$intervals['year']);
			return $diff == 1 ? $diff . ' year ago' : $diff . ' years ago';
		}
	}

	public static function parse_youtube_url($url)
	{
		$pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
		preg_match($pattern, $url, $matches);
		return (isset($matches[1])) ? $matches[1] : false;
	}

	public static function makeUrls($text) {
		$text = preg_replace("/((http(s?):\/\/)|(www\.))([\w\.]+)([a-zA-Z0-9?&%.;:\/=+_-]+)/i", "'<a href=\"http$3://$4$5$6\" target=\"_blank\">' . \utility::shortenUrl(\"$2$4$5$6\",25) . '</a>'", $text);
		$text = preg_replace("/(?<=\A|[^A-Za-z0-9_])#([A-Za-z0-9_]+)(?=\Z|[^A-Za-z0-9_])/", "<a href='http://twitter.com/search?q=%23$1' target='_blank'>$0</a>", $text);
		return $text;
	}

	public static function shortenUrl($url,$length) {
		$output = substr($url, 0, $length);
		if(isset($url[$length]))
			$output .= '...';
		return $output;
	}

}