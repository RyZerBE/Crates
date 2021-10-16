<?php

namespace matze\chestopening\crate;

use matze\chestopening\entity\FloatingText;
use matze\chestopening\Loader;
use pocketmine\level\Position;
use pocketmine\utils\TextFormat;

class Crate {

    /** @var Position */
    private Position $position;
    /** @var bool  */
    private bool $inUse = false;
    /** @var FloatingText|null */
    private ?FloatingText $floatingText = null;

    /** @var bool  */
    private bool $init = false;

    public function __construct(Position $position){
        $vector3 = $position->floor()->add(0.5, 0, 0.5);
        $this->position = new Position($vector3->x, $vector3->y, $vector3->z, $position->getLevel());
    }

    /**
     * @return Position
     */
    public function getPosition(): Position{
        return $this->position;
    }

    /**
     * @return bool
     */
    public function isInUse(): bool{
        return $this->inUse;
    }

    public function getFloatingText(): ?FloatingText{
        return $this->floatingText;
    }

    /**
     * @return bool
     */
    public function isInit(): bool{
        return $this->init;
    }

    /**
     * @param bool $init
     */
    public function setInit(bool $init): void{
        $this->init = $init;
    }

    /**
     * @param bool $inUse
     */
    public function setInUse(bool $inUse): void{
        $this->inUse = $inUse;
        $this->initFloatingText();
    }

    public function initFloatingText(): void {
        $position = $this->getPosition();
        $position->getLevel()->loadChunk($position->getFloorX() >> 4, $position->getFloorZ() >> 4);
        if($this->isInUse()) {
            if($this->floatingText === null || $this->floatingText->isClosed()) return;
            $this->floatingText->flagForDespawn();
            return;
        }
        if($this->floatingText !== null && !$this->floatingText->isClosed()) return;
        $this->floatingText = new FloatingText(new Position($position->x, $position->y + 1, $position->z, $position->getLevel()));
        $this->floatingText->setLifeTime(null);
        $this->floatingText->setText(Loader::PREFIX."\n".TextFormat::GRAY."["."Click"."]");
    }
}
