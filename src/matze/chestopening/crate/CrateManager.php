<?php

namespace matze\chestopening\crate;

use matze\chestopening\Loader;
use matze\chestopening\utils\InstantiableTrait;
use matze\chestopening\utils\Vector3Utils;
use pocketmine\math\Vector3;
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
            $this->addCrate(new Crate(Vector3Utils::fromString($crate)));
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
        $this->crates[Vector3Utils::toString($crate->getVector3()->floor())] = $crate;
    }

    /**
     * @param Crate $crate
     */
    public function removeCrate(Crate $crate): void {
        if(is_null($this->getCrate($crate->getVector3()))) return;
        unset($this->crates[Vector3Utils::toString($crate->getVector3()->floor())]);
    }

    /**
     * @param Vector3 $vector3
     * @return Crate|null
     */
    public function getCrate(Vector3 $vector3): ?Crate {
        return $this->crates[Vector3Utils::toString($vector3->floor())] ?? null;
    }
}