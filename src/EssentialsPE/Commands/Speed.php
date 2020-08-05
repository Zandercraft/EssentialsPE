<?php

declare(strict_types = 1);

namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseAPI;
use EssentialsPE\BaseFiles\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Speed extends BaseCommand{

    public function __construct(BaseAPI $api){
        parent::__construct($api, "speed", "Change your speed limit", "<speed> [player]");
        $this->setPermission("essentials.speed");
    }

	/**
	 * @param CommandSender $sender
	 * @param string        $alias
	 * @param array         $args
	 *
	 * @return bool
	 */
    public function execute(CommandSender $sender, string $alias, array $args): bool{
        if(!$this->testPermission($sender)){
            return false;
        }
        if(!$sender instanceof Player || count($args) < 1){
            $this->sendUsage($sender, $alias);
            return false;
        }
        if(!is_numeric($args[0])){
            $sender->sendMessage(TextFormat::RED . "[Error] Please provide a valid value");
            return false;
        }
        $player = $sender;
        if(isset($args[1]) && !($player = $this->getAPI()->getPlayer($args[1]))){
            $sender->sendMessage(TextFormat::RED . "[Error] Player not found");
            return false;
        }
        if((int) $args[0] === 0){
            $player->removeEffect(Effect::SPEED);
            $sender->sendMessage(TextFormat::YELLOW . "Speed removed.");

        } elseif ((int) $args[0] > 127) {
            $sender->sendMessage(TextFormat::YELLOW . "Speed cannot be higher than 127.");
            return false;

        } elseif ($player->getEffect(Effect::SPEED)) {
            $player->removeEffect(Effect::SPEED);
            $effect = new EffectInstance(Effect::getEffect(Effect::SPEED), INT32_MAX, (int)$args[0]-1, false);
            $player->addEffect($effect);
            $sender->sendMessage(TextFormat::YELLOW . "Speed re-amplified to " . TextFormat::WHITE . $args[0]);

        } else {
            $effect = new EffectInstance(Effect::getEffect(Effect::SPEED), INT32_MAX, (int)$args[0]-1, false);
            $player->addEffect($effect);
            $sender->sendMessage(TextFormat::YELLOW . "Speed amplified by " . TextFormat::WHITE . $args[0]);
        }
        return true;
    }
}