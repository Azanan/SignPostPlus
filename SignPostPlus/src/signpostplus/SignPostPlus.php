<?php

namespace signpostplus;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;

// SignEditのUSE文
use signpostplus\edit\SignEdit;
use signpostplus\edit\utils\SignEditAPI;

// SignRotationのUSE文
use signpostplus\rotation\SignRotation;

// SignClockのUSE文
use signpostplus\clock\SignClock;

class SignPostPlus extends PluginBase
{

    public function onEnable()
    {
        $this->registerUtils();
        $this->registerEvents();
        $this->getLogger()->info("§l§a──────────────§eSignPostPlus§a───────────────────");
        $this->getLogger()->info("  §2製作§r: OtorisanVardo");
        $this->getLogger()->info("   §2連絡§r: §bhttps://twitter.com/10ripon_obs ");
        $this->getLogger()->info("  §2製作§r: aieuo");
        $this->getLogger()->info("   §2連絡§r: §bhttps://twitter.com/aieuo421 ");
        $this->getLogger()->info("§l§a──────────────────────────────────────────────");
        $this->getLogger()->info("  §c二次配布は禁止とします");
        $this->getLogger()->info("  §c同梱のライセンスに従ってください");
        $this->getLogger()->info("  §6何かあればツイッターで連絡お願いします");
        $this->getLogger()->info("§l§a──────────────────────────────────────────────");
    }


    private function registerEvents()
    {
        $pluginManager = $this->getServer()->getPluginManager();
        $pluginManager->registerEvents(new SignEdit($this, $this->api), $this);
        $pluginManager->registerEvents(new SignRotation($this), $this);
        $pluginManager->registerEvents(new SignClock($this), $this);
    }


    private function registerUtils()
    {
        $this->api = new SignEditAPI($this);
    }
}
