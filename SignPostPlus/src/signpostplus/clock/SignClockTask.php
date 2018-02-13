<?php

namespace signpostplus\clock;

use pocketmine\scheduler\PluginTask;
use pocketmine\tile\Sign;

class SignClockTask extends PluginTask
{

	public function __construct($owner, $listener, $tile, $line, $xyz, $time=0)
	{
		parent::__construct($owner);
		$this->listener = $listener;
		$this->tile = $tile;
		$this->line = $line;
		$this->xyz = $xyz;
		$this->microtime = $time;
	}


	public function onRun(int $tick)
	{
		if ($this->tile->getId() == null) {
			$this->getHandler()->cancel();
			return;
		}
		$sign = $this->tile;
		$count = $this->microtime++;
		if ($count == 10) {
			$sign->setText($this->line[0], $this->line[1], $this->line[2], $this->line[3]);
			$sign->saveNBT();
			$this->getHandler()->cancel();
			unset($this->listener->clockingNow[$this->xyz]);
			return;
		}
		$sign->setText("§7§l─────────", "§3§l".date("Y/n/d"), "§b§l".date("H:i:s"), "§7§l─────────");
		$sign->saveNBT();
	}
}