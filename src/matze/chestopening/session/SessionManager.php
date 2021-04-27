<?php

namespace matze\chestopening\session;

use matze\chestopening\animation\Animation;
use matze\chestopening\crate\Crate;
use matze\chestopening\rarity\Rarity;
use matze\chestopening\utils\InstantiableTrait;
use pocketmine\Player;
use function is_null;

class SessionManager {
    use InstantiableTrait;

    /** @var array  */
    private $sessions = [];

    /**
     * @return Session[]
     */
    public function getSessions(): array{
        return $this->sessions;
    }

    /**
     * @param Player $player
     * @param Animation $animation
     * @param Rarity $rarity
     * @param Crate $crate
     * @return Session
     */
    public function createSession(Player $player, Animation $animation, Rarity $rarity, Crate $crate): Session {
        if(!is_null(($session = $this->getSession($player)))) return $session;
        $session = new Session($player, $animation, $rarity, $crate);
        $this->addSession($session);
        return $session;
    }

    /**
     * @param $player
     * @return Session|null
     */
    public function getSession($player): ?Session {
        if($player instanceof Player) $player = $player->getName();
        return $this->sessions[$player] ?? null;
    }

    /**
     * @param Session $session
     */
    public function addSession(Session $session): void {
        $this->sessions[$session->getPlayer()->getName()] = $session;
    }

    /**
     * @param Session $session
     */
    public function destroySession(Session $session): void {
        $session->destroy();
        $this->removeSession($session);
    }

    /**
     * @param Session $session
     */
    public function removeSession(Session $session): void {
        unset($this->sessions[$session->getPlayer()->getName()]);
    }
}