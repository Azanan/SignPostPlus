<?php

namespace signpostplus\clock;

//use文
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\plugin\PluginEnableEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\tile\Sign;
use pocketmine\item\Item;
use pocketmine\item\Clock;
use pocketmine\block\WallSign;

use signpostplus\clock\SignClockTask;

class SignClock implements Listener
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


	public function onStartPlugin(PluginEnableEvent $event)
	{
		$plugin = $event->getPlugin();
		if ($plugin->getName() !== $this->getOwner()->getName()) return;
		date_default_timezone_set("Asia/Tokyo");
	}


	public function onTap(PlayerInteractEvent $event)
	{
		$item = $event->getItem();
		$block = $event->getBlock();
		if ($item instanceof Clock) {
			if ($block instanceof WallSign) {
				$player = $event->getPlayer();
				$tile = $player->getLevel()->getTile($block);
				if ($tile instanceof Sign) {
					if (isset($this->clockingNow[$tile->getFloorX().$tile->getFloorY().$tile->getFloorZ()])) return;
					$xyz = $tile->getFloorX().$tile->getFloorY().$tile->getFloorZ();
					$task = new SignClockTask($this->getOwner(), $this, $tile, $tile->getText(), $xyz, 0);
					$this->getServer()->getScheduler()->scheduleRepeatingTask($task, 1*20);
					$this->clockingNow[$xyz] = $task->getTaskId();
				}
			}
		}
	}


	public function onBreak(BlockBreakEvent $event)
	{
		$block = $event->getBlock();
		if ($block instanceof WallSign) {
			$xyz = $block->getFloorX().$block->getFloorY().$block->getFloorZ();
			if (!isset($this->clockingNow[$xyz])) return;
			$id = $this->clockingNow[$xyz];
			$this->getServer()->getScheduler()->cancelTask($id);
			unset($this->clockingNow[$xyz]);
		}
	}
}