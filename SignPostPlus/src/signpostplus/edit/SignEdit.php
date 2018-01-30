<?php

namespace signpostplus\edit;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\item\Item;
use pocketmine\block\SignPost;
use pocketmine\block\WallSign;
use pocketmine\tile\Sign;
use pocketmine\item\Feather;

use signpostplus\edit\utils\SignEditAPI;

class SignEdit implements Listener
{

	public function __construct($owner, $api)
	{
		$this->owner = $owner;
		$this->api = $api;
	}


	public function onTap(PlayerInteractEvent $event)
	{
		$player = $event->getPlayer();
		$item = $event->getItem();
		$block = $event->getBlock();
		if ($item instanceof Feather) {
			if ($block instanceof SignPost || $block instanceof WallSign) {
				$tile = $player->getLevel()->getTile($block);
				if (!($tile instanceof Sign)) return;
				$player->signedit["object"] = $tile;
				if (!isset($player->signedit["copydatas"])) {
					$player->signedit["copydatas"] = [];
				}
				$this->getApi()->requestUI(SignEditAPI::FORM_TYPE_SELECT, $player);
			}
		}
	}


	public function onReceive(DataPacketReceiveEvent $event)
	{
		$pk = $event->getPacket();
		if (!($pk instanceof ModalFormResponsePacket)) return;
		$player = $event->getPlayer();
		$id = $pk->formId;
		$data = json_decode($pk->formData);
		switch ($id) {

			case SignEditAPI::FORM_TYPE_SELECT:

				if ($pk->formData == "null\n") return;

				if ((int)$data == 0) {
					$this->getApi()->requestUI(SignEditAPI::FORM_TYPE_EDIT, $player);
					return;
				}

				if ((int)$data == 1) {
					$this->getApi()->requestUI(SignEditAPI::FORM_TYPE_COPY, $player);
					return;
				}

				if ((int)$data == 2) {
					if (empty($player->signedit["copydatas"])) {
						$player->sendMessage("§c> 看板のコピーが一つもされていません");
						return;
					}
					$this->getApi()->requestUI(SignEditAPI::FORM_TYPE_PASTE, $player);
					return;
				}

				if ((int)$data == 3) {
					$this->getApi()->requestUI(SignEditAPI::FORM_TYPE_INITIAL, $player);
					return;
				}

				if ((int)$data == 4) {
					$this->getApi()->requestUI(SignEditAPI::FORM_TYPE_DELPASTE, $player);
					return;
				}
				break;


			case SignEditAPI::FORM_TYPE_EDIT:
				if ($pk->formData == "null\n") {
					$this->getApi()->requestUI(SignEditAPI::FORM_TYPE_SELECT, $player);
					return;
				}
				if (!is_array($data)) {
					return;
				}
				$sign = $player->signedit["object"];
				foreach ($data as $key => $text) {
					$sign->setLine($key, $text);
				}
				$sign->saveNBT();
				$player->sendMessage("§a> 看板の編集が完了しました");
				break;


			case SignEditAPI::FORM_TYPE_COPY:
			case SignEditAPI::FORM_TYPE_COPY_ERROR:
				if ($pk->formData == "null\n") {
					$this->getApi()->requestUI(SignEditAPI::FORM_TYPE_SELECT, $player);
					return;
				}
				if ($data[0] === null) return;
				$sign = $player->signedit["object"];
				$title = $data[0];
				if (isset($player->signedit["copydatas"][$title])) {
					$this->getApi()->requestUI(SignEditAPI::FORM_TYPE_COPY_ERROR, $player);
					return;
				}
				$player->signedit["copydatas"][$title] = $sign->getText();
				$player->sendMessage("§a> 看板のコピーが完了しました");
				break;


			case SignEditAPI::FORM_TYPE_PASTE:
				if ($pk->formData == "null\n") {
					$this->getApi()->requestUI(SignEditAPI::FORM_TYPE_SELECT, $player);
					return;
				}
				if (!isset($player->signedit["copydatas"])) return;
				$sign = $player->signedit["object"];
				//$texts = array_slice($player->signedit["copydatas"], $data, 1);
				$key = array_keys($player->signedit["copydatas"])[$data];
				$texts = $player->signedit["copydatas"][$key];
				$sign->setText($texts[0], $texts[1], $texts[2], $texts[3]);
				$sign->saveNBT();
				$player->sendMessage("§a> 貼りつけが完了しました");
				break;


			case SignEditAPI::FORM_TYPE_INITIAL:
				if ($pk->formData == "null\n") {
					$this->getApi()->requestUI(SignEditAPI::FORM_TYPE_SELECT, $player);
					return;
				}
				if ($data) {
					$sign = $player->signedit["object"];
					$sign->setText("", "", "", "");
					$sign->saveNBT();
					$player->sendMessage("§a> 看板のコピーが完了しました");
					$player->sendMessage("§a> 看板の白紙化が完了しました");
				} else {
					$player->sendMessage("§b> 看板の白紙化を中断しました");
				}
				break;


			case SignEditAPI::FORM_TYPE_DELPASTE:
				if ($pk->formData == "null\n") {
					$this->getApi()->requestUI(SignEditAPI::FORM_TYPE_SELECT, $player);
					return;
				}
				if (empty($player->signedit["copydatas"])) {
					$player->sendMessage("§c> 看板のコピーデータが一つもありません");
					return;
				}

				$key = array_keys($player->signedit["copydatas"])[$data];
				unset($player->signedit["copydatas"][$key]);
				$player->sendMessage("§a> 指定した看板のコピーデータを消しました");
				break;
		}
	}


	public function getServer()
	{
		return $this->owner->getServer();
	}


	public function getOwner()
	{
		return $this->owner;
	}


	public function getApi()
	{
		return $this->api;
	}
}