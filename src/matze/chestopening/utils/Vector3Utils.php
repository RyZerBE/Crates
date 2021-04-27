<?php

namespace matze\chestopening\utils;

use pocketmine\math\Vector3;
use function explode;
use function implode;

class Vector3Utils {

    /**
     * @param Vector3 $vector3
     * @return string
     */
    public static function toString(Vector3 $vector3): string {
        return implode(":", [
            $vector3->x,
            $vector3->y,
            $vector3->z
        ]);
    }

    /**
     * @param string $vector3
     * @return Vector3
     */
    public static function fromString(string $vector3): Vector3 {
        $vector3 = explode(":", $vector3);
        return new Vector3(
            (float)(isset($vector3[0]) ? $vector3[0] : 0),
            (float)(isset($vector3[1]) ? $vector3[1] : 0),
            (float)(isset($vector3[2]) ? $vector3[2] : 0)
        );
    }
}