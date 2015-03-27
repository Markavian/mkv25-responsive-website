<?php

class Time
{
	public static function oneHour()
	{
		return new TimeObject(1);
	}

	public static function oneMinute()
	{
		return new TimeObject(0, 1);
	}
	
	public static function tenMinutes()
	{
		return new TimeObject(0, 10, 0);
	}
	
	public static function tenSeconds()
	{
		return new TimeObject(0, 0, 10);
	}
}

class TimeObject
{
	var $hours;
	var $minutes;
	var $seconds;

	public function __construct($hours=0, $minutes=0, $seconds=0)
	{
		$this->hours = $hours;
		$this->minutes = $minutes;
		$this->seconds = $seconds;
	}

	public function inSeconds()
	{
		return ($this->hours * 3600) + ($this->minutes * 60) + $this->seconds;
	}

	public function inMinutes()
	{
		return round($this->inSeconds() / 60);
	}

	public function inHours()
	{
		return round($this->inSeconds() / 3600);
	}
}