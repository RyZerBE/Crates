<?php

namespace matze\chestopening\crate;

use matze\chestopening\Loader;
use matze\chestopening\utils\InstantiableTrait;
use matze\chestopening\utils\PositionUtils;
use pocketmine\level\Position;
use pocketmine\utils\Config;
use function array_keys;
use function is_null;

class CrateManager {
    use InstantiableTrait;

    /** @var array */
    private $crates = [];

    /**
     * CrateManager constructor.
     */
    public function __construct() {
        $this->load();
    }

    public function load(): void {
        $file = new Config(Loader::getInstance()->getDataFolder() . "crates.json", -1, []);
        foreach($file->getAll() as $crate) {
            $this->addCrate(new Crate(PositionUtils::fromString($crate)));
        }
    }

    public function save(): void {
        $file = new Config(Loader::getInstance()->getDataFolder() . "crates.json", -1, []);
        $file->setAll(array_keys($this->crates));
        $file->save();
    }

    /**
     * @param Crate $crate
     */
    public function addCrate(Crate $crate): void {
        $this->crates[PositionUtils::toString(PositionUtils::floor($crate->getPosition()))] = $crate;
    }

    /**
     * @param Crate $crate
     */
    public function removeCrate(Crate $crate): void {
        if(is_null($this->getCrate(PositionUtils::floor($crate->getPosition())))) return;
        unset($this->crates[PositionUtils::toString(PositionUtils::floor($crate->getPosition()))]);
        $floatingText = $crate->getFloatingText();
        if(!is_null($floatingText) && !$floatingText->isClosed()) $floatingText->flagForDespawn();
    }

    /**
     * @param Position $position
     * @return Crate|null
     */
    public function getCrate(Position $position): ?Crate {
        return $this->crates[PositionUtils::toString(PositionUtils::floor($position))] ?? null;
    }
}