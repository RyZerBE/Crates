<?php

namespace matze\chestopening\entity;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\ChunkLoader;
use pocketmine\level\format\Chunk;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use function is_null;
use function spl_object_id;

/**
 * Class FloatingText
 * @package matze\chestopening\entity
 *
 *
 * Do not complain about this code
 * It´s over 6 months old and I was too lazy to write a new one
 */
class FloatingText extends Entity implements ChunkLoader {

    /** @var int  */
    public const NETWORK_ID = self::VILLAGER;

    /** @var float  */
    public $width = 0.1;
    /** @var float  */
    public $height = 0.1;

    /** @var int  */
    public $gravity = 0;
    /** @var int  */
    public $drag = 0;

    /** @var int|null */
    private $lifeTime = 0;
    /** @var Position */
    private $forcePosition;

    /**
     * FloatingText constructor.
     * @param Position|null $position
     * @param Player ...$viewers
     */
    public function __construct($position = null, ...$viewers) {
        if(!$position instanceof Position) return;
        $this->forcePosition = $position;
        $level = $position->getLevel();
        $nbt = Entity::createBaseNBT($position);
        parent::__construct($level, $nbt);
        $this->setScale(0.000000001);//loooooooooooooooooooooooooooooooool
        if(empty($viewers)) {
            $this->spawnToAll();
        } else {
            foreach ($viewers as $player) {
                if(!$viewers instanceof Player) continue;
                $this->spawnTo($player);
            }
        }

        $this->setGenericFlag(self::DATA_FLAG_SILENT, true);
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void {
        $this->setNameTagAlwaysVisible(!empty($text));
        $this->setNameTagVisible(!empty($text));
        $this->setNameTag($text);
    }

    /**
     * @return string
     */
    public function getText(): string {
        return $this->getNameTag();
    }

    /**
     * @param int|null $ticks
     */
    public function setLifeTime(?int $ticks): void {
        $this->lifeTime = $ticks;
    }

    /**
     * @return int|null
     */
    public function getLifeTime(): ?int {
        return $this->lifeTime;
    }

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick): bool {
        $this->setImmobile(true);
        if(!is_null($this->lifeTime)) {
            if($this->ticksLived >= $this->getLifeTime()) $this->flagForDespawn();
        }
        if($currentTick % 40 === 0) $this->teleport($this->forcePosition);
        return parent::onUpdate($currentTick);
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void{
        $source->setCancelled();
    }

    /**
     * @return bool
     */
    public function canBeMovedByCurrents(): bool {
        return false;
    }

    /**
     * @return bool
     */
    public function isFireProof(): bool {
        return true;
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public function canCollideWith(Entity $entity): bool {
        return false;
    }

    /**
     * @return int
     */
    public function getLoaderId(): int{
        return spl_object_id($this);
    }

    /**
     * @return bool
     */
    public function isLoaderActive(): bool{
        return $this->isAlive() && !$this->isClosed();
    }

    public function onChunkPopulated(Chunk $chunk){
    }

    public function onBlockChanged(Vector3 $block){
    }

    public function onChunkChanged(Chunk $chunk){
    }

    public function onChunkLoaded(Chunk $chunk){
    }

    public function onChunkUnloaded(Chunk $chunk){
    }
}