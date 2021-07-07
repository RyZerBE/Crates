<?php


namespace matze\chestopening\reward\types\lobby\particle;


use BauboLP\Core\Provider\AsyncExecutor;
use BauboLP\Core\Provider\LanguageProvider;
use baubolp\ryzerbe\lobbycore\cosmetic\CosmeticManager;
use baubolp\ryzerbe\lobbycore\cosmetic\type\itemrain\ItemRainCosmetic;
use baubolp\ryzerbe\lobbycore\cosmetic\type\particle\ParticleCosmetic;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use matze\chestopening\Loader;
use matze\chestopening\provider\ChestOpeningProvider;
use matze\chestopening\reward\Reward;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

abstract class LobbyParticle extends Reward
{

    abstract public function getParticleName(): string;

    public function getName(): string
    {
        $cosmetic = CosmeticManager::getInstance()->getCosmetic($this->getParticleName());
        if(!$cosmetic instanceof ParticleCosmetic) return "§cError";
        return TextFormat::LIGHT_PURPLE.$cosmetic->getName()." Particle ".TextFormat::DARK_GRAY."(".TextFormat::YELLOW."Lobby".TextFormat::DARK_GRAY.")";
    }

    public function onReceive(Player $player): void
    {
        $particle = $this->getParticleName();
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if($lobbyPlayer === null) return;
        $cosmetic = CosmeticManager::getInstance()->getCosmetic($particle);
        if(!$cosmetic instanceof ParticleCosmetic) return;
        $playerName = $player->getName();

        if($lobbyPlayer->isCosmeticUnlocked($cosmetic)) {
            $player->sendMessage(Loader::PREFIX.LanguageProvider::getMessageContainer("crate-already-have", $playerName, ["#article" => $cosmetic->getName()]));
            ChestOpeningProvider::addKey($playerName);
        } else {
            $lobbyPlayer->unlockCosmetic($cosmetic);
        }
    }
}