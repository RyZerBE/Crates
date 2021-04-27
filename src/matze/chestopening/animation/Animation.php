<?php

namespace matze\chestopening\animation;

use matze\chestopening\session\Session;

abstract class Animation {

    /** @var Session|null */
    private $session;

    /**
     * Animation constructor.
     * @param Session|null $session
     */
    public function __construct(?Session $session = null){
        $this->session = $session;
    }

    /**
     * @return Session|null
     */
    public function getSession(): ?Session{
        return $this->session;
    }

    /**
     * @param Session $session
     */
    public function setSession(Session $session): void{
        $this->session = $session;
    }

    /** @var int */
    private $tick = 0;

    public function tick(): void {
        $this->onUpdate($this->tick++);
    }

    public function onFinish(): void {
        $this->getSession()->onFinish();
    }

    abstract public function getName(): string;
    abstract public function onUpdate(int $tick): void;
}