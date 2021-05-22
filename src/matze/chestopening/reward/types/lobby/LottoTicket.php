<?php


namespace matze\chestopening\reward\types\lobby;


use matze\chestopening\reward\Reward;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use BauboLP\LobbySystem\Provider\LottoProvider;
use BauboLP\LobbySystem\LobbySystem;


class LottoTicket extends Reward
{
    /** @var int */
    private $amount;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }

    public function getName(): string
    {
        return TextFormat::LIGHT_PURPLE.$this->amount."x Lottotickets".TextFormat::DARK_GRAY."(".TextFormat::YELLOW."Lobby".TextFormat::DARK_GRAY.")";
    }

    public function onReceive(Player $player): void
    {
        $lobbyPlayer = LobbySystem::getPlayerCache($player->getName());
        if($lobbyPlayer === null) return;

        LottoProvider::addTicket($lobbyPlayer, $this->amount);
    }
}
