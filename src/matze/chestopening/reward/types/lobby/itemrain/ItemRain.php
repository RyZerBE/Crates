<?php

namespace matze\chestopening\reward\types\lobby\itemrain;

use baubolp\core\provider\LanguageProvider;
use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use baubolp\ryzerbe\lobbycore\cosmetic\type\itemrain\ItemRainCosmetic;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use matze\chestopening\Loader;
use matze\chestopening\provider\ChestOpeningProvider;
use matze\chestopening\reward\Reward;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

abstract class ItemRain extends Reward {

    abstract public function getItemRain(): string;

    public function getName(): string {
        $cosmetic = CosmeticManager::getInstance()->getCosmetic($this->getItemRain());
        if(!$cosmetic instanceof ItemRainCosmetic) return "§cError";
        return TextFormat::LIGHT_PURPLE.$cosmetic->getName() . " Item Rain".TextFormat::DARK_GRAY."(".TextFormat::YELLOW."Lobby".TextFormat::DARK_GRAY.")";
    }

    public function onReceive(Player $player): void
    {
        $itemRain = $this->getItemRain();
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if($lobbyPlayer === null) return;
        $cosmetic = CosmeticManager::getInstance()->getCosmetic($itemRain);
        if(!$cosmetic instanceof ItemRainCosmetic) return;
        $playerName = $player->getName();

        if($lobbyPlayer->isCosmeticUnlocked($cosmetic)) {
            $player->sendMessage(Loader::PREFIX.LanguageProvider::getMessageContainer("crate-already-have", $playerName, ["#article" => $cosmetic->getName()]));
            ChestOpeningProvider::addKey($playerName);
        } else {
            $lobbyPlayer->unlockCosmetic($cosmetic);
        }
    }
}