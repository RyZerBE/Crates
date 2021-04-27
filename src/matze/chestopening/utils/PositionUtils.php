<?php

namespace matze\chestopening\utils;

use pocketmine\level\Position;
use pocketmine\Server;
use function explode;
use function floor;
use function implode;
use function is_null;

class PositionUtils {

    /**
     * @param Position $position
     * @return string
     */
    public static function toString(Position $position): string {
        return implode(":", [
            $position->x,
            $position->y,
            $position->z,
            (is_null($position->getLevel()) ? Server::getInstance()->getDefaultLevel()->getFolderName() : $position->getLevel()->getFolderName())
        ]);
    }

    /**
     * @param string $position
     * @return Position
     */
    public static function fromString(string $position): Position {
        $position = explode(":", $position);
        return new Position(
            (float)(isset($position[0]) ? $position[0] : 0),
            (float)(isset($position[1]) ? $position[1] : 0),
            (float)(isset($position[2]) ? $position[2] : 0),
            (isset($position[3]) ? Server::getInstance()->getLevelByName($position[3]) : Server::getInstance()->getDefaultLevel())
        );
    }

    /**
     * @param Position $position
     * @return Position
     */
    public static function floor(Position $position): Position {
        return new Position(
            floor($position->x),
            floor($position->y),
            floor($position->z),
            $position->getLevel()
        );
    }
}