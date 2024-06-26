<?php

namespace matze\chestopening\entity;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\ChunkLoader;
use pocketmine\level\format\Chunk;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Server;
use function is_null;
use function spl_object_id;
use function var_dump;

class FloatingText extends Entity implements ChunkLoader {
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
    private ?int $lifeTime = 0;
    /** @var Position */
    private Position $forcePosition;

    public function __construct(Position $position) {
        $this->forcePosition = $position;
        $level = $position->getLevel();
        $nbt = Entity::createBaseNBT($position);
        parent::__construct($level, $nbt);
        $this->setScale(0.000000001);
        $this->spawnToAll();

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
        return !$this->isClosed();
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
        var_dump("This should not happen");
    }
}