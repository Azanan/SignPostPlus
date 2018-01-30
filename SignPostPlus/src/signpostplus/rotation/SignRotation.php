<?php

namespace signpostplus\rotation;

//use文
use pocketmine\event\Listener;


class SignRotation implements Listener
{

	public function __construct($owner)
	{
		$this->owner = $owner;
	}


	public function getServer()
	{
		return $this->owner->getServer();
	}


	public function getOwner()
	{
		return $this->owner;
	}


	public function functionName($value='')
	{
		# code...
	}
}