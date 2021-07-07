<?php


namespace matze\chestopening\reward\types\lobby;


use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use baubolp\ryzerbe\lobbycore\provider\LottoProvider;
use matze\chestopening\reward\Reward;
use pocketmine\Player;
use pocketmine\utils\TextFormat;


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
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if($lobbyPlayer === null) return;

        LottoProvider::addTicket($lobbyPlayer, $this->amount);
    }
}
