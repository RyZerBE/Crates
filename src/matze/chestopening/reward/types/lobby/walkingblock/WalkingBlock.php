<?php


namespace matze\chestopening\reward\types\lobby\walkingblock;

use BauboLP\Core\Provider\LanguageProvider;
use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use baubolp\ryzerbe\lobbycore\cosmetic\type\walkingblocks\WalkingBlocksCosmetic;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use matze\chestopening\Loader;
use matze\chestopening\provider\ChestOpeningProvider;
use matze\chestopening\reward\Reward;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

abstract class WalkingBlock extends Reward
{

    abstract public function getWalkingBlockName(): string;


    public function getName(): string {
        $cosmetic = CosmeticManager::getInstance()->getCosmetic($this->getWalkingBlockName());
        if(!$cosmetic instanceof WalkingBlocksCosmetic) return "§cError";
        return TextFormat::LIGHT_PURPLE.$cosmetic->getName()." Walking Blocks ".TextFormat::DARK_GRAY."(".TextFormat::YELLOW."Lobby".TextFormat::DARK_GRAY.")";
    }

    public function onReceive(Player $player): void
    {
        $walkingBlockName = $this->getWalkingBlockName();
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if($lobbyPlayer === null) return;
        $cosmetic = CosmeticManager::getInstance()->getCosmetic($walkingBlockName);
        if(!$cosmetic instanceof WalkingBlocksCosmetic) return;
        $playerName = $player->getName();

        if($lobbyPlayer->isCosmeticUnlocked($cosmetic)) {
            $player->sendMessage(Loader::PREFIX.LanguageProvider::getMessageContainer("crate-already-have", $playerName, ["#article" => $cosmetic->getName()]));
            ChestOpeningProvider::addKey($playerName);
        } else {
            $lobbyPlayer->unlockCosmetic($cosmetic);
        }
    }
}