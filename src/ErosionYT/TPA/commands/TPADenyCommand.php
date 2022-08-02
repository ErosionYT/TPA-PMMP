<?php

namespace ErosionYT\TPA\commands;

use pocketmine\{command\Command,
    command\CommandSender,
    player\Player};

use ErosionYT\TPA\TPA;

class TPADenyCommand extends Command {

    public function __construct(TPA $owner){
        parent::__construct("tpadeny");
        $this->owner = $owner;
        $this->setDescription($this->owner->getConfig()->get("tpadenyCommandDescription"));
        $this->setPermission("tpa.command");
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool{
        if($player instanceof Player){
            if($this->owner->hasRequest($player)){
                $this->owner->denyTPARequest($player);
            }else{
                $player->sendMessage("§6» §7You do not have any teleport requests");
            }
        }
        return true;
    }
}