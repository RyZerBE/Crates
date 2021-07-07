<?php

namespace matze\chestopening\reward\types\lobby\particle;

class HeartParticle extends LobbyParticle {

    /**
     * @return string
     */
    public function getParticleName(): string{
        return "particles:hearts";
    }
}