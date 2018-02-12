<?php

namespace signpostplus\edit\utils;

use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

class SignEditAPI
{

	const FORM_TYPE_SELECT = 42347;
	const FORM_TYPE_EDIT = 42348;
	const FORM_TYPE_COPY = 42349;
	const FORM_TYPE_PASTE = 42350;
	const FORM_TYPE_INITIAL = 42351;
	const FORM_TYPE_COPY_ERROR = 42352;
	const FORM_TYPE_DELPASTE = 42353;

	const FORM_IMAGE_EDIT = "https://i.imgur.com/QmA6UZR.png";
	const FORM_IMAGE_PASTE = "https://i.imgur.com/hA4v71w.png";
	const FORM_IMAGE_COPY = "https://i.imgur.com/vGXIZhS.png";
	const FORM_IMAGE_INITIAL = "https://i.imgur.com/4hBz3Ij.png";
	const FORM_IMAGE_DELPASTE = "https://i.imgur.com/n8W4leS.png";


	public function requestUI($formId, $player, $sign=null)
	{
		switch ($formId) {

			case SignEditAPI::FORM_TYPE_SELECT:
				$json = $this->getSelectFormJson();
				break;

			case SignEditAPI::FORM_TYPE_EDIT:
				$json = $this->getEditFormJson($player);
				break;

			case SignEditAPI::FORM_TYPE_COPY:
				$json = $this->getCopyFormJson();
				break;

			case SignEditAPI::FORM_TYPE_COPY_ERROR:
				$json = $this->getCopyErrorFormJson();
				break;

			case SignEditAPI::FORM_TYPE_PASTE:
				$json = $this->getPasteFormJson($player);
				if ($json == null) {
					$player->sendMessage("§c> 看板のコピーが一つもされていません");
					return;
				}
				break;

			case SignEditAPI::FORM_TYPE_DELPASTE:
				$json = $this->getDelPasteFormJson($player);
				break;

			case SignEditAPI::FORM_TYPE_INITIAL:
				$json = $this->getInitialFormJson();
				break;
		}

		$pk = new ModalFormRequestPacket();
        $pk->formId = $formId;
        $pk->formData = $json;
        $player->dataPacket($pk);
	}


	public function getSelectFormJson()
	{
		$data = [];
		$data["type"] = "form";
		$data["title"] = "§lSignEdit";
		$data["content"] = "行いたい処理を指定してください";

		$replaceset["text"] = "看板の文字を編集します";
		$replaceset["image"]["type"] = "url";
		$replaceset["image"]["data"] = SignEditAPI::FORM_IMAGE_EDIT;
		$data["buttons"][] = $replaceset;

		$copy["text"] = "看板のコピーを行います";
		$copy["image"]["type"] = "url";
		$copy["image"]["data"] = SignEditAPI::FORM_IMAGE_COPY;
		$data["buttons"][] = $copy;

		$paste["text"] = "看板にペーストを行います";
		$paste["image"]["type"] = "url";
		$paste["image"]["data"] = SignEditAPI::FORM_IMAGE_PASTE;
		$data["buttons"][] = $paste;

		$clear["text"] = "看板を白紙化します";
		$clear["image"]["type"] = "url";
		$clear["image"]["data"] = SignEditAPI::FORM_IMAGE_INITIAL;
		$data["buttons"][] = $clear;

		$rmPaste["text"] = "保管データを削除します";
		$rmPaste["image"]["type"] = "url";
		$rmPaste["image"]["data"] = SignEditAPI::FORM_IMAGE_DELPASTE;
		$data["buttons"][] = $rmPaste;

		$json = $this->getEncodedJson($data);
		return $json;
	}


	public function getEditFormJson($player)
	{
		$sign = $player->signedit["object"];
		$data = [];
		$data["type"] = "custom_form";
		$data["title"] = "§lSignEdit > 編集";
		for ($i=0; $i<4; $i++) {
			$content[$i]["type"] = "input";
			$content[$i]["text"] = ($i+1)."行目: ";
			$content[$i]["default"] = $sign->getLine($i);
		}
		$data["content"] = $content;

		$json = $this->getEncodedJson($data);
		return $json;
	}


	public function getCopyFormJson()
	{
		$data = [];
		$data["type"] = "custom_form";
		$data["title"] = "§lSignEdit > コピー";
		$content["type"] = "input";
		$content["text"] = "その看板の文字をコピーします\n任意の題名で保存してください";
		$content["placeholder"] = "わかりやすいキーワード";
		$data["content"][] = $content;
		$json = $this->getEncodedJson($data);
		return $json;
	}


	public function getCopyErrorFormJson()
	{
		$data = [];
		$data["type"] = "custom_form";
		$data["title"] = "§lSignEdit > コピー";
		$content["type"] = "input";
		$content["text"] = "その看板の文字をコピーします\n任意の題名で保存してください";
		$content["placeholder"] = "わかりやすいキーワード";
		$data["content"][] = $content;
		$content["type"] = "label";
		$content["text"] = "§cその題名はすでに使われています。違うものに変えてください";
		$data["content"][] = $content;

		$json = $this->getEncodedJson($data);
		return $json;
	}


	public function getPasteFormJson($player)
	{
		if (empty($player->signedit["copydatas"])) return null;
		$data = [];
		$data["type"] = "form";
		$data["title"] = "§lSignEdit > 貼り付け";
		$data["content"] = "貼り付ける文字を選んでください";

		foreach ($player->signedit["copydatas"] as $keyword => $copyed) {
			$panels["text"] = $keyword;
			$panels["image"]["type"] = "url";
			$panels["image"]["data"] = "";
			$data["buttons"][] = $panels;
		}

		$json = $this->getEncodedJson($data);
		return $json;
	}


	public function getDelPasteFormJson($player)
	{
		if (!isset($player->signedit["copydatas"])) return null;
		$data = [];
		$data["type"] = "form";
		$data["title"] = "§lSignEdit > 保管データ削除";
		$data["content"] = "削除する文字を選んでください";

		foreach ($player->signedit["copydatas"] as $keyword => $copyed) {
			$panels["text"] = $keyword;
			$panels["image"]["type"] = "url";
			$panels["image"]["data"] = "";
			$data["buttons"][] = $panels;
		}

		$json = $this->getEncodedJson($data);
		return $json;
	}


	public function getInitialFormJson()
	{
		$data = [];
		$data["type"] = "modal";
		$data["title"] = "§lSignEdit > 白紙化";
		$data["content"] = "本当に白紙化しますか？";
		$data["button1"] = "する";
		$data["button2"] = "しない";

		$json = $this->getEncodedJson($data);
		return $json;
	}


	public function getEncodedJson($data)
	{
		return json_encode($data, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING | JSON_UNESCAPED_UNICODE);
	}
}
