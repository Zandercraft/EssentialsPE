<?php

declare(strict_types = 1);

namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseAPI;
use EssentialsPE\BaseFiles\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class Exp extends BaseCommand {

    public $xplimit = [];
    public $player = [];
    public $xplimitlevel = [];

    /**
     * @param BaseAPI $api
     */
    public function __construct(BaseAPI $api)
    {
        parent::__construct($api, "xp", "Gives experience to yourself or others", "<Level(L)> [player]");
        $this->setPermission("essentials.exp");
    }

    /**
     * @param CommandSender $sender
     * @param string $alias
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $alias, array $args): bool {

        if (!$this->testPermission($sender)) {
            return false;
        }

        if (count($args) > 2 || count($args) === 0) {
            $this->sendUsage($sender, $alias);
            return false;
        }

        if ($args[0] == 0 or $args[0] === "0L" || !is_numeric($args[0]) and $args[0] !== (int)filter_var($args[0], FILTER_SANITIZE_NUMBER_INT) . "L") {
            $sender->sendMessage(TextFormat::RED . "[Error] Invalid experience level");
            return false;
        }

        if (count($args) === 2 && !($player = $this->getAPI()->getPlayer($args[1]))) {
            $sender->sendMessage(TextFormat::RED . "[Error] Player not found");
            return false;
        }

        // Test if command is executed with player name in args 1 or without
        if (count($args) === 1) {
            $this->player = $this->getAPI()->getPlayer($sender->getName());
        } else {
            $this->player = $this->getAPI()->getPlayer($args[1]);
        }
        // If an L is provided in args 0 it will detect if you surpass or "underpass?" the xplimit via current xp level + (-) what you want, else: detection via experience points.
        if (strpos($args[0], "L") !== false) {
            $this->xplimitlevel = $this->player->getXpLevel() + filter_var($args[0], FILTER_SANITIZE_NUMBER_INT);
            $this->xplimit = null;
        } else {
            $this->xplimit = $this->player->getCurrentTotalXp() + filter_var($args[0], FILTER_SANITIZE_NUMBER_INT);
            $this->xplimitlevel = null;
        }
        // Will throw an error if you go below 0 or above 21863 levels (2147483647 experience points)
        if ($this->xplimitlevel < 0 || $this->xplimitlevel > 21863) {
            $sender->sendMessage(TextFormat::RED . "[Error] Cannot go below 0 or above 21863");
            return false;
        }
        if ($this->xplimit < 0 || $this->xplimit > 2147483647) {
            $sender->sendMessage(TextFormat::RED . "[Error] Cannot go below 0 or above 2147483647");
            return false;
        }
        // If you only have 1 argument and it's without an L
        if (count($args) === 1 && is_numeric($args[0])) {
            $this->player->addXp((int)$args[0]);
            //This changes the message depending on a negative or positive
            $sender->sendMessage(($args[0] < 0 ? "Taken " : "Gave ") . preg_replace('/[-]/', '', $args[0]) . " experience points " . ($args[0] < 0 ? "from " : "to ") . TextFormat::YELLOW . $this->player->getDisplayName());
            return true;
        }
        // If you only have 1 argument and it's with an L
        if (count($args) === 1 && strpos($args[0], "L") !== false) {
            $this->player->addXpLevels((int)$args[0]);
            $sender->sendMessage(($args[0] < 0 ? "Taken " : "Gave ") . preg_replace('/[-L]/', '', $args[0]) . " levels " . ($args[0] < 0 ? "from " : "to ") . TextFormat::YELLOW . $this->player->getDisplayName());
            return true;
        }
        // If you have 2 arguments and it's without an L
        if (is_numeric($args[0])) {
            $this->player->addXp((int)$args[0]);
            $sender->sendMessage(($args[0] < 0 ? "Taken " : "Gave ") . preg_replace('/[-]/', '', $args[0]) . " experience points " . ($args[0] < 0 ? "from " : "to ") . TextFormat::YELLOW . $this->player->getDisplayName());
            $this->player->sendMessage(($args[0] < 0 ? "Lost " : "Obtained ") . preg_replace('/[-]/', '', $args[0]) . " experience points");
            return true;
        }
        // If you have 2 arguments and it's with an L
        if (strpos($args[0], "L") !== false) {
            $this->player->addXpLevels((int)$args[0]);
            $sender->sendMessage(($args[0] < 0 ? "Taken " : "Gave ") . preg_replace('/[-L]/', '', $args[0]) . " levels " . ($args[0] < 0 ? "from " : "to ") . TextFormat::YELLOW . $this->player->getDisplayName());
            $this->player->sendMessage(($args[0] < 0 ? "Lost " : "Obtained ") . preg_replace('/[-L]/', '', $args[0]) . " levels");
            return true;
        }
        return false;
    }
}