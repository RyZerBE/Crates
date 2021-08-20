<?php

namespace matze\chestopening\animation\types\normal;

use pocketmine\entity\object\ItemEntity as PMItemEntity;

class ItemEntity extends PMItemEntity {

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick): bool{
        $this->ticksLived = 10;
        return parent::onUpdate($currentTick);
    }

    /**
     * @param int $tickDiff
     * @return bool
     */
    public function entityBaseTick(int $tickDiff = 1): bool{
        if($this->age++ > 6000) $this->flagForDespawn();
        return true;
    }

    /**
     * @return bool
     */
    public function canSaveWithChunk(): bool{
        return false;
    }
}