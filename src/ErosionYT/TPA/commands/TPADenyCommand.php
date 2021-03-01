<?php

namespace ErosionYT\TPA\commands;

use pocketmine\{
    command\CommandSender, command\PluginCommand, plugin\Plugin, Server, Player, utils\TextFormat as C
};

use ErosionYT\TPA\TPA;

class TPADenyCommand extends PluginCommand{

    public function __construct(TPA $owner){
        parent::__construct("tpadeny", $owner);
        $this->setDescription($this->getPlugin()->getConfig()->get("tpadenyCommandDescription"));
        $this->setPermission("tpa.command");
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool{
        if($player instanceof Player){
            if($this->getPlugin()->hasRequest($player)){
                $this->getPlugin()->denyTPARequest($player);
            }else{
                $player->sendMessage("§6» §7You do not have any teleport requests");
            }
        }
        return true;
    }
}