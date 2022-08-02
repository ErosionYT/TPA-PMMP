<?php

namespace ErosionYT\TPA\commands;

use pocketmine\{command\Command,
    command\CommandSender,
    player\Player};

use ErosionYT\TPA\TPA;

class TPAHereCommand extends Command {

    public function __construct(TPA $owner){
        parent::__construct("tpahere");
        $this->owner = $owner;
        $this->setDescription($this->owner->getConfig()->get("tpahereCommandDescription"));
        $this->setPermission("tpa.command");
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool{
        if($player instanceof Player){
            if(isset($args[0])){
                if(($target = $this->owner->getServer()->getPlayerByPrefix($args[0])) !== null){
                    $this->owner->sendTPAHereRequest($target, $player);

                    $player->sendMessage("§6» §7You sent a teleport request successfully");
                }else{
                    $player->sendMessage("§cThat player cannot be found");
                }
            }
        }
        return true;
    }
}