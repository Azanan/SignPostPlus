<?php

namespace signpostplus\bring;

//use文
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\tile\Sign;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\block\WallSign;
use pocketmine\block\SignPost;

class SignBring implements Listener
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


	public function onBreak(BlockBreakEvent $event)
	{
		$player = $event->getPlayer();
		$item = $player->getInventory()->getItemInHand();
		$block = $event->getBlock();
		if ($item->getId() == 339 && $player->isSneaking()) {
			if ($block->getId() == 63 || $block->getId() == 68) {
				$tile = $player->getLevel()->getTile($block);
				if ($tile instanceof Sign) {
					$lines = $tile->getText();
					$dropsign = Item::get(323, 0, 1)->setLore($lines);
					$event->setDrops([$dropsign]);
				}
			}
		}
	}


	public function onHeld(PlayerItemHeldEvent $event)
	{
		$item = $event->getItem();
		if ($item->getId() == 323) {
			$lore = $item->getLore();
			if (!isset($lore)) return;
			$player = $event->getPlayer();
			$player->lores = $lore;
		}
	}


	public function onChange(SignChangeEvent $event)
	{
		$player = $event->getPlayer();
		$line = $event->getLines();
		if ($line[0] == "" || $line[1] == "" || $line[2] == "" || $line[3] == "") {
			if (!empty($player->lores)) {
				$lore = $player->lores;
				$event->setLines($lore);
			}
		}
	}
}