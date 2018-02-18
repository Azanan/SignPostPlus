<?php

namespace signpostplus\command;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\block\SignPost;
use pocketmine\block\WallSign;
use pocketmine\tile\Sign;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use signpostplus\edit\utils\SignEditAPI;

class SignCommand implements Listener{

	public function __construct($owner)
	{
		$this->owner = $owner;
	}

	public function onTap(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		$block = $event->getBlock();
		if ($block instanceof SignPost || $block instanceof WallSign) {
			$tile = $player->getLevel()->getTile($block);
			if (!($tile instanceof Sign)) return;
			 	$texts =$tile->getText();
			 	if(substr($texts[0],0,2) == "##"){
				 	$cmd = "";
				 	foreach ($texts as $key => $value) {
				 		$last = substr($value,-1);
				 		if($last == "-"){
				 			$value = preg_replace("/-$/","",$value);
				 		}
				 		$cmd = $cmd.$value;
				 		if($key == 3)break;
				 	}
				 	$cmd = str_replace("##","",$cmd);
				 	$this->owner->getServer()->dispatchCommand($player, $cmd);
				 }
		}
	}
}