<?php

namespace matze\chestopening\session;

use matze\chestopening\utils\InstantiableTrait;
use pocketmine\Player;

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