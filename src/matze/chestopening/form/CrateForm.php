<?php

namespace matze\chestopening\form;

use BauboLP\Core\Provider\LanguageProvider;
use jojoe77777\FormAPI\SimpleForm;
use matze\chestopening\animation\types\normal\NormalAnimation;
use matze\chestopening\crate\Crate;
use matze\chestopening\crate\CrateManager;
use matze\chestopening\Loader;
use matze\chestopening\provider\ChestOpeningProvider;
use matze\chestopening\rarity\RarityManager;
use matze\chestopening\session\Session;
use matze\chestopening\session\SessionManager;
use matze\chestopening\utils\AsyncExecuter;
use matze\chestopening\utils\PositionUtils;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use function is_null;

class CrateForm {

    /**
     * @param Player $player
     * @param Crate $crate
     */
    public static function open(Player $player, Crate $crate): void {
        $player = $player->getName();
        $crate = PositionUtils::toString($crate->getPosition());
        AsyncExecuter::submitAsyncTask(function() use ($player): array {
            $result = [];
            $result["Keys"] = ChestOpeningProvider::getKeys($player);
            return $result;
        }, function(Server $server, array $result) use ($player, $crate): void {
            $player = $server->getPlayerExact($player);
            if(is_null($player)) return;
            $crate = CrateManager::getInstance()->getCrate(PositionUtils::fromString($crate));
            if(is_null($crate)) return;
            if($result["Keys"] <= 0) {
                $player->sendMessage(Loader::PREFIX.LanguageProvider::getMessageContainer('crate-no-keys', $player->getName()));
            }
            $form = new SimpleForm(function(Player $player, $data) use ($crate): void {
                if(is_null($data)) return;
                switch($data) {
                    case "close": return;
                    default: {
                        if($crate->isInUse()) {
                            $player->sendMessage(Loader::PREFIX.LanguageProvider::getMessageContainer("crate-in-use", $player->getName()));
                            return;
                        }
                        if(!is_null(SessionManager::getInstance()->getSession($player))) {
                            $player->sendMessage(Loader::PREFIX.LanguageProvider::getMessageContainer("crate-already-opening", $player->getName()));
                            return;
                        }
                        SessionManager::getInstance()->addSession(new Session($player, new NormalAnimation(), $crate));
                    }
                }
            });
            $form->setTitle(Loader::PREFIX);
                $form->addButton(TextFormat::GOLD.TextFormat::GOLD. $result["Keys"]."x Keys\n".TextFormat::GRAY."Click to open", 0, "", "open");

            $form->addButton("§r§cClose", 0, "textures/ui/realms_red_x", "close");
            $form->sendToPlayer($player);
        });
    }
}