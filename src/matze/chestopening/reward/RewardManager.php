<?php


namespace matze\chestopening\reward;


use matze\chestopening\reward\types\flagwars\DemolitionistKit;
use matze\chestopening\reward\types\flagwars\StarterKit;
use matze\chestopening\reward\types\flagwars\VampireKit;
use matze\chestopening\reward\types\lobby\CoinBomb;
use matze\chestopening\reward\types\lobby\itemrain\AquaticItemRain;
use matze\chestopening\reward\types\lobby\itemrain\BedWarsItemRain;
use matze\chestopening\reward\types\lobby\itemrain\NetherItemRain;
use matze\chestopening\reward\types\lobby\itemrain\TheEndItemRain;
use matze\chestopening\reward\types\lobby\itemrain\UHCItemRain;
use matze\chestopening\reward\types\lobby\LottoTicket;
use matze\chestopening\reward\types\lobby\particle\AngryVillagerParticle;
use matze\chestopening\reward\types\lobby\particle\HappyVillagerParticle;
use matze\chestopening\reward\types\lobby\particle\HeartParticle;
use matze\chestopening\reward\types\lobby\particle\SmokeParticle;
use matze\chestopening\reward\types\lobby\walkingblock\DesertWalkingBlock;
use matze\chestopening\reward\types\lobby\walkingblock\GrasslandWalkingBlocks;
use matze\chestopening\reward\types\lobby\walkingblock\NetherWalkingBlocks;
use matze\chestopening\reward\types\lobby\walkingblock\RichRichWalkingBlocks;
use matze\chestopening\reward\types\lobby\walkingblock\TheEndWalkingBlocks;
use matze\chestopening\reward\types\lobby\walkingblock\WoolWalkingBlocks;
use matze\chestopening\reward\types\MoneyReward;
use pocketmine\utils\TextFormat;
use function array_rand;
use function mt_rand;
use function shuffle;

class RewardManager
{
    /** @var array  */
    public static $rewards = [];

    /**
     * @return Reward[]
     */
    public static function getRewards(): array
    {
        return self::$rewards;
    }

    public static function registerRewards(): void
    {
        $rewards = [
            (new MoneyReward(1000))->setChance(70),
            (new MoneyReward(2000))->setChance(70),
            (new MoneyReward(3000))->setChance(70),
            (new GrasslandWalkingBlocks())->setChance(70),
            (new MoneyReward(5000))->setChance(70),
            (new TheEndWalkingBlocks())->setChance(65),
            (new HappyVillagerParticle())->setChance(65),
            (new DesertWalkingBlock())->setChance(65),
            (new NetherWalkingBlocks())->setChance(65),
            (new TheEndWalkingBlocks())->setChance(65),
            (new AngryVillagerParticle())->setChance(65),
            (new HeartParticle())->setChance(65),
            (new SmokeParticle())->setChance(65),
            (new LottoTicket(5))->setChance(65),
            (new LottoTicket(12))->setChance(65),
            (new RichRichWalkingBlocks())->setChance(60),
            (new WoolWalkingBlocks())->setChance(60),
            (new AquaticItemRain())->setChance(45),
            (new BedWarsItemRain())->setChance(45),
            (new NetherItemRain())->setChance(45),
            (new TheEndItemRain())->setChance(45),
            (new UHCItemRain())->setChance(45),
            (new CoinBomb())->setChance(20),
            (new StarterKit())->setChance(5),
            (new VampireKit())->setChance(5),
            (new DemolitionistKit())->setChance(5),
        ];

        foreach ($rewards as $reward) self::$rewards[] = $reward;
    }

    /**
     * @param int $percent
     * @return string
     */
    public static function getRarity(int $percent): string
    {
        if($percent <= 10)  return TextFormat::AQUA.TextFormat::BOLD."LEGENDARY";
        if($percent <= 49)  return TextFormat::DARK_PURPLE.TextFormat::BOLD."EPIC";
        if($percent < 70)   return TextFormat::GOLD.TextFormat::BOLD."RARE";
        if($percent <= 100) return TextFormat::GREEN.TextFormat::BOLD."COMMON";
        return TextFormat::DARK_RED."???";
    }

    /**
     * @param int $percent
     * @return string
     */
    public static function getRarityPicSub(int $percent): string
    {
        if($percent <= 10) return "Turkis";
        if($percent <= 49) return "Lila";
        if($percent < 70) return "Gold";
        //if($percent <= 100) return "Grun";
        return "Grun";
    }

    /**
     * @return Reward|null
     */
    public static function getCalculatedReward(): ?Reward {
        $rewards = [];
        for($i = 1; $i <= 300; $i++) {
            $rewards[] = self::getRewards()[mt_rand(0, 2)];
        }
        foreach(self::getRewards() as $reward) {
            for($chance = 1; $chance <= $reward->getChance(); $chance++) {
                $rewards[] = $reward;
            }
        }
        shuffle($rewards);
        return $rewards[array_rand($rewards)];
    }
}
