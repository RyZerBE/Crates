<?php

namespace matze\chestopening\form;

use jojoe77777\FormAPI\SimpleForm;
use matze\chestopening\animation\types\normal\NormalAnimation;
use matze\chestopening\crate\Crate;
use matze\chestopening\crate\CrateManager;
use matze\chestopening\provider\ChestOpeningProvider;
use matze\chestopening\rarity\RarityManager;
use matze\chestopening\session\Session;
use matze\chestopening\session\SessionManager;
use matze\chestopening\utils\AsyncExecuter;
use matze\chestopening\utils\Vector3Utils;
use pocketmine\Player;
use pocketmine\Server;
use function is_int;
use function is_null;

class CrateForm {

    /**
     * @param Player $player
     * @param Crate $crate
     */
    public static function open(Player $player, Crate $crate): void {
        $player = $player->getName();
        $crate = Vector3Utils::toString($crate->getVector3());
        $rarities = [];
        foreach(RarityManager::getInstance()->getRarities() as $rarity) {
            $rarities[] = $rarity->getId();
        }
        AsyncExecuter::submitAsyncTask(function() use ($player, $rarities): array {
            $result = [];
            foreach($rarities as $rarity) {
                $result["Keys"][$rarity] = ChestOpeningProvider::getKeys($player, $rarity);
            }
            return $result;
        }, function(Server $server, array $result) use ($player, $crate): void {
            $player = $server->getPlayerExact($player);
            if(is_null($player)) return;
            $crate = CrateManager::getInstance()->getCrate(Vector3Utils::fromString($crate));
            if(is_null($crate)) return;
            $form = new SimpleForm(function(Player $player, $data) use ($crate): void {
                if(is_null($data)) return;
                switch($data) {
                    case "close": return;
                    default: {
                        $rarity = RarityManager::getInstance()->getRarity($data);
                        if(is_null($rarity)) return;
                        if($crate->isInUse()) {
                            $player->sendMessage("Crate is currently in use. Please wait a moment.");//todo: message
                            return;
                        }
                        SessionManager::getInstance()->addSession(new Session($player, new NormalAnimation(), $rarity, $crate));
                    }
                }
            });
            $form->setTitle("§r§lChestOpening");
            foreach(RarityManager::getInstance()->getRarities() as $rarity) {
                $form->addButton($rarity->getTextFormat() . $rarity->getName() . " §7[§8" . $result["Keys"][$rarity->getId()] . "§7]", 0, "", $rarity->getId());
            }
            $form->addButton("§r§cClose", 0, "textures/ui/realms_red_x", "close");
            $form->sendToPlayer($player);
        });
    }
}