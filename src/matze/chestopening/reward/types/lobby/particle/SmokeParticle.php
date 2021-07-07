<?php

namespace matze\chestopening\reward\types\lobby\particle;

class SmokeParticle extends LobbyParticle {

    /**
     * @return string
     */
    public function getParticleName(): string{
        return "particle:smoke";
    }
}