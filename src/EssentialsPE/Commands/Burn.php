<?php

declare(strict_types = 1);

namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseAPI;
use EssentialsPE\BaseFiles\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class Burn extends BaseCommand{
    /**
     * @param BaseAPI $api
     */
    public function __construct(BaseAPI $api){
        parent::__construct($api, "burn", "Set a player on fire", "<seconds> [player]");
        $this->setPermission("essentials.burn");
    }

    /**
     * @param CommandSender $sender
     * @param string $alias
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $alias, array $args): bool{
        if(!$this->testPermission($sender)){
            return false;
        }
        if(count($args) > 2 || count($args) === 0){
            $this->sendUsage($sender, $alias);
            return false;
        }
        if(!is_numeric($time = $args[0])) {
            $sender->sendMessage(TextFormat::RED . "[Error] Invalid burning time");
            return false;
        }
        if(count($args) === 2 && !($player = $this->getAPI()->getPlayer($args[1]))){
                $sender->sendMessage(TextFormat::RED . "[Error] Player not found");
                return false;
        }
        if(count($args) === 1) {
            $player = $this->getAPI()->getPlayer($sender->getName());
            $player->setOnFire((int) $time);
            $sender->sendMessage(TextFormat::YELLOW . $player->getDisplayName() . " is now on fire!");
            return true;

        }
        if(($player = $this->getAPI()->getPlayer($args[1]))) {
            $player->setOnFire((int) $time);
            $sender->sendMessage(TextFormat::YELLOW . $player->getDisplayName() . " is now on fire!");
            return true;

        }
        return false;
    }
}
