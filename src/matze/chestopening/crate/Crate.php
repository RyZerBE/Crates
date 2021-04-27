<?php

namespace matze\chestopening\crate;

use pocketmine\math\Vector3;

class Crate {

    /** @var Vector3 */
    private $vector3;
    /** @var bool  */
    private $inUse = false;

    /**
     * Crate constructor.
     * @param Vector3 $vector3
     */
    public function __construct(Vector3 $vector3){
        $this->vector3 = $vector3->floor()->add(0.5, 0, 0.5);
    }

    /**
     * @return Vector3
     */
    public function getVector3(): Vector3{
        return $this->vector3;
    }

    /**
     * @return bool
     */
    public function isInUse(): bool{
        return $this->inUse;
    }

    /**
     * @param bool $inUse
     */
    public function setInUse(bool $inUse): void{
        $this->inUse = $inUse;
    }
}