<?php


namespace matze\chestopening\reward;


use matze\chestopening\reward\types\BauboStinktReward;
use matze\chestopening\reward\types\MoneyReward;
use pocketmine\utils\TextFormat;

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
            (new MoneyReward(500))->setChance(80),
            (new MoneyReward(5000))->setChance(70),
            (new MoneyReward(50000))->setChance(40),
            (new BauboStinktReward())->setChance(80),
        ];

        foreach ($rewards as $reward)
        self::$rewards[] = $reward;
    }

    /**
     * @param int $percent
     * @return string
     */
    public static function getRarity(int $percent): string
    {
        if($percent <= 10)
            return TextFormat::AQUA.TextFormat::BOLD."LEGENDARY";
        if($percent <= 49)
            return TextFormat::DARK_PURPLE.TextFormat::BOLD."EPIC";
        if($percent <= 70)
            return TextFormat::GOLD.TextFormat::BOLD."RARE";
        if($percent <= 100)
            return TextFormat::GREEN.TextFormat::BOLD."COMMON";

        return TextFormat::DARK_RED."???";
    }

    /**
     * @param int $percent
     * @return string
     */
    public static function getRarityPicSub(int $percent): string
    {
        if($percent <= 10)
            return "Turkis";
        if($percent <= 49)
            return "Lila";
        if($percent <= 70)
            return "Gold";
        if($percent <= 100)
            return "Grun";

        return "Grun";
    }

    /**
     * @param float $chance
     * @return bool
     */
    public static function calculateChance(float $chance): bool
    {
        if($chance <= 0){ // there is no 0% chance, it's either 1 to 100 or 100
            return true;
        }

        $count = strlen(substr(strrchr(strval($chance), "."), 1));
        $multiply = intval("1" . str_repeat("0", $count));

        return mt_rand(1, (100 * $multiply)) <= ($chance * $multiply);
    }

    public static function getCalculatedReward(): ?Reward
    {
        $calculatedReward = null;
        $rewards = self::getRewards();
        while(true){
            /** @var \matze\chestopening\reward\Reward $reward */
            foreach($rewards as $reward){
                if(self::calculateChance($reward->getChance())){
                    $calculatedReward = $reward;
                    break 2;
                }
            }
        }

        return $calculatedReward;
    }
}