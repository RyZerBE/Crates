<?php

namespace matze\chestopening\animation\types\normal;

use matze\chestopening\rarity\Rarity;
use matze\chestopening\rarity\RarityIds;
use matze\chestopening\utils\Settings;
use matze\chestopening\utils\SkinUtils;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Level;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\particle\HappyVillagerParticle;
use pocketmine\level\particle\HeartParticle;
use pocketmine\level\particle\HugeExplodeSeedParticle;
use pocketmine\level\sound\PopSound;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Server;
use function array_rand;
use function is_null;
use function mt_rand;

class BoxEntity extends Human {

    /** @var float  */
    public $width = 0.45;
    /** @var float  */
    public $height = 0.45;

    /** @var Rarity */
    private $rarity;
    /** @var int */
    private $startY;
    /** @var int  */
    private $waitTicksForMotion = 0;

    /**
     * BoxEntity constructor.
     * @param Level $level
     * @param CompoundTag $nbt
     * @param Rarity|null $rarity
     */
    public function __construct(Level $level, CompoundTag $nbt, ?Rarity $rarity = null){
        if(is_null($rarity)) return;
        $this->rarity = $rarity;
        switch($rarity->getId()) {
            case RarityIds::RARITY_RARE: {
                $skin = "Gold";
                break;
            }
            case RarityIds::RARITY_EPIC: {
                $skin = "Lila";
                break;
            }
            case RarityIds::RARITY_LEGENDARY: {
                $skin = "Turkis";
                break;
            }
            default: {
                $skin = "Grun";
            }
        }
        //$this->skin = Server::getInstance()->getOnlinePlayers()[array_rand(Server::getInstance()->getOnlinePlayers())]->getSkin();
        $this->skin = new Skin("box", SkinUtils::readImage(Settings::SKIN_PATH . "Opening" . $skin . ".png"), "", "geometry.Mobs.Zombie", file_get_contents(Settings::SKIN_PATH . "ChestOpening.json"));
        parent::__construct($level, $nbt);
    }

    public function initEntity(): void{
        parent::initEntity();
        $this->startY = $this->y;
        $this->waitTicksForMotion = 10;
        $this->teleport($this->subtract(0, 5));
        $this->sendSkin();
    }

    /**
     * @return Rarity
     */
    public function getRarity(): Rarity{
        return $this->rarity;
    }

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick): bool{
        if($this->isClosed()) return false;
        if($this->waitTicksForMotion > 0) {
            $this->waitTicksForMotion--;
            return true;
        } else {
            if($this->waitTicksForMotion !== -1) {
                $this->teleport(new Vector3($this->x, $this->startY, $this->z));
                $this->setMotion(new Vector3(0, 1.5, 0));
                $this->onGround = false;
                $this->waitTicksForMotion = -1;
            }
        }
        for($i = 0; $i <= 10; $i++) {
            switch(mt_rand(1, 10)) {
                case 1: {
                    $particle = new FlameParticle($this->add(mt_rand(-5, 5) / 10, mt_rand(-5, 5) / 10, mt_rand(-5, 5) / 10));
                    break;
                }
                case 2: {
                    $particle = new HeartParticle($this->add(mt_rand(-5, 5) / 10, mt_rand(-5, 5) / 10, mt_rand(-5, 5) / 10));
                    break;
                }
                default: {
                    $particle = new HappyVillagerParticle($this->add(mt_rand(-5, 5) / 10, mt_rand(-5, 5) / 10, mt_rand(-5, 5) / 10));
                }
            }
            $this->getLevel()->addParticle($particle);
        }
        $this->getLevel()->addSound(new PopSound($this, ($this->y - $this->startY) + 0.5));

        if($this->isOnGround()) {
            $this->getLevel()->addParticle(new HugeExplodeSeedParticle($this));
            $this->getLevel()->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_EXPLODE);
            $this->flagForDespawn();
        }
        $this->yaw += 10;
        $this->updateMovement();
        return parent::onUpdate($currentTick);
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void{
        $source->setCancelled();
    }
}