<?php

namespace ErosionYT\TPA\commands;

use pocketmine\{command\Command,
    command\CommandSender,
    player\Player};

use ErosionYT\TPA\TPA;

class TPAcceptCommand extends Command {

    public function __construct(TPA $owner){
        parent::__construct("tpaccept");
        $this->owner = $owner;
        $this->setDescription($this->owner->getConfig()->get("tpacceptCommandDescription"));
        $this->setPermission("tpa.command");
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool{
        if($player instanceof Player){
            if($this->owner->hasRequest($player)){
                if($this->owner->teleporteeStillOnline($player)){
                    if(isset($this->owner->tpaReq[$player->getName()]["teleport"])){
                        $player->teleport(($teleportee = $this->owner->getTeleportee($player))->getPosition());
                    }else{
                        ($teleportee = $this->owner->getTeleportee($player))->teleport($player->getPosition());
                    }

                    $this->owner->destroyRequest($player);

                    $player->sendMessage("§6» §7You have teleported successfully");

                    $teleportee->sendMessage("§6» §7You have teleported successfully");
                }
            }else{
                $player->sendMessage("§6» §7You do not have any teleport requests");
            }
        }
        return true;
    }
}
