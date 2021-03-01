<?php

namespace ErosionYT\TPA\commands;

use pocketmine\{
    command\CommandSender, command\PluginCommand, plugin\Plugin, Server, Player, utils\TextFormat as C
};

use ErosionYT\TPA\TPA;

class TPAcceptCommand extends PluginCommand{

    public function __construct(TPA $owner){
        parent::__construct("tpaccept", $owner);
        $this->setDescription($this->getPlugin()->getConfig()->get("tpacceptCommandDescription"));
        $this->setPermission("tpa.command");
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool{
        if($player instanceof Player){
            if($this->getPlugin()->hasRequest($player)){
                if($this->getPlugin()->teleporteeStillOnline($player)){
                    if(isset($this->getPlugin()->tpaReq[$player->getName()]["teleport"])){
                        $player->teleport(($teleportee = $this->getPlugin()->getTeleportee($player)));
                    }else{
                        ($teleportee = $this->getPlugin()->getTeleportee($player))->teleport($player);
                    }

                    $this->getPlugin()->destroyRequest($player);

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