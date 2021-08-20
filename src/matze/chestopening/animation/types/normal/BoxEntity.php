<?php

namespace matze\chestopening\animation\types\normal;

use matze\chestopening\reward\RewardManager;
use matze\chestopening\utils\Settings;
use matze\chestopening\utils\SkinUtils;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\particle\HappyVillagerParticle;
use pocketmine\level\particle\HeartParticle;
use pocketmine\level\particle\HugeExplodeSeedParticle;
use pocketmine\level\sound\PopSound;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use function lcg_value;
use function mt_rand;

class BoxEntity extends Human {

    /** @var float  */
    public $width = 0.45;
    /** @var float  */
    public $height = 0.45;

    /** @var int */
    private int $startY;
    /** @var int  */
    private int $waitTicksForMotion = 0;

    /** @var bool  */
    private bool $isMotionNegative = false;
    /** @var int  */
    private int $waitTicks = 0;

    /**
     * BoxEntity constructor.
     *
     * @param Level $level
     * @param CompoundTag $nbt
     * @param int $chance
     */
    public function __construct(Level $level, CompoundTag $nbt, int $chance){
        $skin = RewardManager::getRarityPicSub($chance);
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
                $this->setMotion(new Vector3(0, 1.75, 0));
                $this->onGround = false;
                $this->waitTicksForMotion = -1;
            }
        }

        $level = $this->getLevel();
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
            $level->addParticle($particle);
        }
        $level->addSound(new PopSound($this, ($this->y - $this->startY) + 0.5));

        $isMotionNegative = $this->motion->y < 0;
        if($isMotionNegative && !$this->isMotionNegative) {
            $this->isMotionNegative = true;
            $this->waitTicks = 20;

            $itemTag = Item::get(Item::DIAMOND)->nbtSerialize();
            $itemTag->setName("Item");
            for($i = 0; $i <= 15; $i++) {
                $motion = new Vector3((mt_rand(0, 1) === 0 ? lcg_value() : -lcg_value()), 0.2, (mt_rand(0, 1) === 0 ? lcg_value() : -lcg_value()));
                $nbt = Entity::createBaseNBT($this, $motion, lcg_value() * 360, 0);
                $nbt->setShort("Age", mt_rand(5900, 5980));
                $nbt->setTag($itemTag);
                $itemEntity = new ItemEntity($level, $nbt);
                $itemEntity->ticksLived = 0;
                $itemEntity->spawnToAll();
            }
        }

        if(--$this->waitTicks > 0) {
            $this->gravity = 0;
            $this->drag = 0;
        } else {
            $this->gravity = 0.08;
            $this->drag = 0.02;
        }

        if($this->isOnGround()) {
            $level->addParticle(new HugeExplodeSeedParticle($this));
            $level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_EXPLODE);
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