<?php

namespace signpostplus\rotation;

//use文
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\block\SignPost;
use pocketmine\item\Item;
use pocketmine\item\Stick;

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


	public function onTap(PlayerInteractEvent $event)
	{
		$item = $event->getItem();
		$block = $event->getBlock();
		if ($item instanceof Stick) {
			if ($block instanceof SignPost) {
				$damage = $block->getDamage();
				$block->setDamage($damage +1);
			}
		}
	}
}