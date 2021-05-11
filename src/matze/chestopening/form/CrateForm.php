<?php

namespace matze\chestopening\form;

use BauboLP\Core\Player\RyzerPlayerProvider;
use BauboLP\Core\Provider\AsyncExecutor;
use BauboLP\Core\Provider\CoinProvider;
use BauboLP\Core\Provider\LanguageProvider;
use jojoe77777\FormAPI\SimpleForm;
use matze\chestopening\animation\types\normal\NormalAnimation;
use matze\chestopening\crate\Crate;
use matze\chestopening\crate\CrateManager;
use matze\chestopening\Loader;
use matze\chestopening\provider\ChestOpeningProvider;
use matze\chestopening\reward\RewardManager;
use matze\chestopening\session\Session;
use matze\chestopening\session\SessionManager;
use matze\chestopening\utils\PositionUtils;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use function is_null;

class CrateForm
{
    /** @var array  */
    public static $rarityPicture = [
        "legendary" => "https://media.discordapp.net/attachments/711642365550526614/838390217323839488/ChestOpeningTurkis.png?width=358&height=341",
        "epic" => "https://media.discordapp.net/attachments/711642365550526614/838390216200421396/ChestOpeningLila.png?width=358&height=341",
        "common" => "https://media.discordapp.net/attachments/711642365550526614/838390213994217532/ChestOpeningGrun.png?width=358&height=341",
        "rare" => "https://media.discordapp.net/attachments/711642365550526614/838390212862148608/ChestOpeningGold.png?width=358&height=341"
    ];

    /**
     * @param Player $player
     * @param Crate $crate
     */
    public static function open(Player $player, Crate $crate): void
    {
        $playerName = $player->getName();
        $crate = PositionUtils::toString($crate->getPosition());
        if (is_null($crate)) return;

        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (\mysqli $mysqli) use ($playerName) {
            $res = $mysqli->query("SELECT * FROM Crates WHERE playername='$playerName'");
            $keys = 0;
            if ($res->num_rows > 0) {
                while ($data = $res->fetch_assoc())
                    $keys = $data["cratekeys"];
            } else
                $mysqli->query("INSERT INTO `Crates`(`playername`, `cratekeys`) VALUES ('$playerName', '0')");

            return $keys;
        }, function (Server $server, $result) use ($playerName, $crate) {
            $keys = $result;
            $player = $server->getPlayerExact($playerName);
            if (is_null($player)) return;

            /*if ($keys <= 0) {
                $player->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer('crate-no-keys', $playerName));
                return;
            }*/
            $form = new SimpleForm(function (Player $player, $data) use ($crate, $playerName): void {
                if (is_null($data)) return;
                $crate = CrateManager::getInstance()->getCrate(PositionUtils::fromString($crate));
                switch ($data) {
                    case "close":
                        return;
                    case "showcase":
                        $form = new SimpleForm(function (Player $player, $data): void{});
                        $form->setTitle(Loader::PREFIX.TextFormat::GREEN."Showcase");
                        foreach (RewardManager::getRewards() as $reward) {
                            $rarity = RewardManager::getRarity($reward->getChance());
                            $key = strtolower(TextFormat::clean($rarity));
                            $icon = array_key_exists($key, CrateForm::$rarityPicture) ? CrateForm::$rarityPicture[$key] : null;
                            $form->addButton($reward->getName()."\n".TextFormat::DARK_GRAY . "» ".$rarity.TextFormat::RESET.TextFormat::DARK_GRAY . " «", 1, $icon ?? "");
                        }

                        $form->sendToPlayer($player);
                        break;
                    case "buy":
                        if (($ryzerPlayer = RyzerPlayerProvider::getRyzerPlayer($playerName)) != null) {
                            if ($ryzerPlayer->getCoins() >= 6000) {
                                CoinProvider::removeCoins($playerName, 6000);
                                ChestOpeningProvider::addKey($playerName);
                                $ryzerPlayer->getPlayer()->playSound("random.levelup", 5.0, 1.0, [$ryzerPlayer->getPlayer()]);
                            } else {
                                $ryzerPlayer->getPlayer()->playSound("note.bass", 5.0, 1.0, [$ryzerPlayer->getPlayer()]);
                                $player->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer("not-enough-coins", $playerName));
                            }
                        }
                        break;
                    default:
                    {
                        if ($crate->isInUse()) {
                            $player->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer("crate-in-use", $playerName));
                            return;
                        }
                        if (!is_null(SessionManager::getInstance()->getSession($player))) {
                            $player->sendMessage(Loader::PREFIX . LanguageProvider::getMessageContainer("crate-already-opening", $playerName));
                            return;
                        }
                        SessionManager::getInstance()->addSession(new Session($player, new NormalAnimation(), $crate));
                    }
                }
            });

            $form->setTitle(Loader::PREFIX);
            if ($keys > 0)
                $form->addButton(TextFormat::GOLD . TextFormat::BOLD ."   ". $keys . "x Keys\n" .TextFormat::RESET. TextFormat::GRAY . "Click to open", 1, "https://media.discordapp.net/attachments/809475312965910538/837400134211338260/KeyTest.png?width=400&height=400", "open");
            $form->addButton(TextFormat::RED . TextFormat::BOLD . "1x Key\n" . TextFormat::RESET.TextFormat::DARK_GRAY . "» " . TextFormat::GOLD . "6000 Coins" . TextFormat::DARK_GRAY . " «", 1, "https://media.discordapp.net/attachments/412217468287713282/837760624451649587/transparent_shopicon.png?width=702&height=702", "buy");
            $form->addButton(TextFormat::GREEN . TextFormat::BOLD . "Showcase\n" . TextFormat::RESET.TextFormat::DARK_GRAY . "» " . TextFormat::GOLD . "Click" . TextFormat::DARK_GRAY . " «", 1, "https://media.discordapp.net/attachments/602115215307309066/838153378026487848/showcase.png?width=225&height=218", "showcase");

            $form->addButton("§r§cClose", 0, "textures/ui/realms_red_x", "close");
            $form->sendToPlayer($player);
        });
    }
}