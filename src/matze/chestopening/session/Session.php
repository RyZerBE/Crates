<?php

namespace matze\chestopening\session;

use matze\chestopening\animation\Animation;
use matze\chestopening\crate\Crate;
use matze\chestopening\rarity\Rarity;
use matze\chestopening\reward\Reward;
use pocketmine\entity\Entity;
use pocketmine\Player;
use function array_rand;

class Session {

    /** @var Player */
    private $player;
    /** @var Animation */
    private $animation;
    /** @var Rarity */
    private $rarity;
    /** @var Crate */
    private $crate;

    /** @var array  */
    private $entities = [];
    /** @var bool  */
    private $running = true;
    /** @var Reward */
    private $reward;

    /**
     * Session constructor.
     * @param Player $player
     * @param Animation $animation
     * @param Rarity $rarity
     * @param Crate $crate
     */
    public function __construct(Player $player, Animation $animation, Rarity $rarity, Crate $crate) {
        $this->player = $player;
        $this->animation = $animation;
        $this->rarity = $rarity;
        $this->crate = $crate;

        $rewards = [];
        foreach($rarity->getRewards($animation) as $reward) {
            for($int = 0; $int <= $reward->getChance(); $int++) $rewards[] = $reward;
        }
        $this->reward = $rewards[array_rand($rewards)];
        $animation->setSession($this);
        $crate->setInUse(true);
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player{
        return $this->player;
    }

    /**
     * @return Animation
     */
    public function getAnimation(): Animation{
        return $this->animation;
    }

    /**
     * @return Rarity
     */
    public function getRarity(): Rarity{
        return $this->rarity;
    }

    /**
     * @return Reward
     */
    public function getReward(): Reward{
        return $this->reward;
    }

    /**
     * @return Crate
     */
    public function getCrate(): Crate{
        return $this->crate;
    }

    /**
     * @return bool
     */
    public function isRunning(): bool{
        return $this->running;
    }

    /**
     * @param bool $running
     */
    public function setRunning(bool $running): void{
        $this->running = $running;
    }

    public function onFinish(): void {
        $this->running = false;
        $this->getReward()->onReceive();
        SessionManager::getInstance()->destroySession($this);
    }

    public function destroy(): void {
        foreach($this->getEntities() as $entity) {
            if($entity->isClosed() || $entity->isFlaggedForDespawn()) continue;
            $entity->flagForDespawn();
        }
        $this->getCrate()->setInUse(false);
    }

    public function onUpdate(): void {
        $player = $this->getPlayer();
        if(!$player->isOnline()) {
            SessionManager::getInstance()->destroySession($this);
            return;
        }
        $this->getAnimation()->tick();
    }

    /**
     * @return Entity[]
     */
    public function getEntities(): array{
        return $this->entities;
    }

    /**
     * @param Entity $entity
     */
    public function addEntity(Entity $entity): void {
        $this->entities[$entity->getId()] = $entity;
    }

    /**
     * @param Entity $entity
     */
    public function removeEntity(Entity $entity): void {
        if(!isset($this->entities[$entity->getId()])) return;
        unset($this->entities[$entity->getId()]);
    }
}