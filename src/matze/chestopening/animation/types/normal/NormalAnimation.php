<?php

namespace matze\chestopening\animation\types\normal;

use matze\chestopening\animation\Animation;
use matze\chestopening\entity\FloatingText;
use pocketmine\entity\Entity;
use pocketmine\level\Position;
use function is_null;

class NormalAnimation extends Animation {

    /**
     * @return string
     */
    public function getName(): string{
        return "Normal";
    }

    /** @var BoxEntity|null */
    private $boxEntity = null;

    /**
     * @param int $tick
     */
    public function onUpdate(int $tick): void{
        $session = $this->getSession();
        if(is_null($session)) return;
        $rarity = $session->getRarity();
        $player = $session->getPlayer();
        $crate = $session->getCrate();
        switch($tick) {
            case 0: {
                $nbt = Entity::createBaseNBT($crate->getPosition());
                $this->boxEntity = new BoxEntity($player->getLevel(), $nbt, $rarity);
                $this->boxEntity->spawnToAll();
                $session->addEntity($this->boxEntity);
                break;
            }
            default: {
                if(!is_null($this->boxEntity)) {
                    if($this->boxEntity->isClosed()) {
                        $session->removeEntity($this->boxEntity);
                        $text = new FloatingText(new Position($crate->getPosition()->x, $crate->getPosition()->y + 1, $crate->getPosition()->z, $player->getLevel()));
                        $text->setText("§r§a" . $session->getReward()->getName());
                        $text->setLifeTime(40);
                        $this->onFinish();
                        return;
                    }
                }
            }
        }
    }
}