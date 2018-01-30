<?php

namespace signpostplus;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;

// SignEditのUSE文
use signpostplus\edit\SignEdit;
use signpostplus\edit\utils\SignEditAPI;

class SignPostPlus extends PluginBase
{

    public function onEnable()
    {
        $this->registerUtils();
        $this->registerEvents();
    }


    private function registerEvents()
    {
        $pluginManager = $this->getServer()->getPluginManager();
        $pluginManager->registerEvents(new SignEdit($this, $this->api), $this);
        # $pluginManager->registerEvents(new クラス名($this), $this);
    }


    private function registerUtils()
    {
        $this->api = new SignEditAPI($this);
    }
}