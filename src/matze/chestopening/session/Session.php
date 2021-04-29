<?php

namespace matze\chestopening\session;

use BauboLP\Core\Provider\AsyncExecutor;
use matze\chestopening\animation\Animation;
use matze\chestopening\crate\Crate;
use matze\chestopening\Loader;
use matze\chestopening\provider\ChestOpeningProvider;
use matze\chestopening\rarity\Rarity;
use matze\chestopening\reward\Reward;
use matze\chestopening\reward\RewardManager;
use matze\chestopening\reward\types\MoneyReward;
use matze\chestopening\utils\AsyncExecuter;
use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
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
     *
     * @param Player $player
     * @param Animation $animation
     * @param Crate $crate
     */
    public function __construct(Player $player, Animation $animation, Crate $crate) {
        $this->player = $player;
        $this->reward = RewardManager::getCalculatedReward() ?? new MoneyReward(1);
        $this->animation = $animation;
        $this->crate = $crate;

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
        $this->getReward()->onReceive($this->getPlayer());
        SessionManager::getInstance()->destroySession($this);

        $player = $this->getPlayer()->getName();
        ChestOpeningProvider::removeKey($player);
    }

    public function destroy(): void {
        foreach($this->getEntities() as $entity) {
            if($entity->isClosed() || $entity->isFlaggedForDespawn()) continue;
            $entity->flagForDespawn();
        }
        Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function(int $tick): void {
            $this->getCrate()->setInUse(false);
        }), 40);
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