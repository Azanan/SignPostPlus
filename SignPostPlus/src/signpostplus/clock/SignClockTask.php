<?php

namespace signpostplus\clock;

use pocketmine\scheduler\PluginTask;
use pocketmine\tile\Sign;

class SignClockTask extends PluginTask
{

	public function __construct($owner, $listener, $tile, $line, $time=0)
	{
		parent::__construct($owner);
		$this->listener = $listener;
		$this->tile = $tile;
		$this->line = $line;
		$this->microtime = $time;
	}

	public function onRun(int $tick)
	{
		$sign = $this->tile;
		$count = $this->microtime++;
		if ($count == 10) {
			$sign->setText($this->line[0], $this->line[1], $this->line[2], $this->line[3]);
			$sign->saveNBT();
			$this->getHandler()->cancel();
			unset($this->listener->clockingNow[$sign->getFloorX().$sign->getFloorY().$sign->getFloorZ()]);
			return;
		}
		$sign->setText("§l┌───────┐", "§2§l".date("Y/n/d"), "§a§l".date("H:i:s"), "§l└───────┘");
		$sign->saveNBT();
	}
}