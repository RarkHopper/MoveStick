<?php

declare(strict_types = 1);

namespace rarkhopper\movestick;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

final class MoveStickCommand extends Command {
    public function __construct() {
        parent::__construct("movestick", "MoveStickを取得します", "/movestick");
        $this->setPermission("movestick.command.admin");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if (!$this->testPermission($sender)) {
            return false;
        }
        if ($sender instanceof Player) {
            $sender->sendMessage(TextFormat::GREEN . "MoveStickをインベントリに追加しました");
            $sender->getInventory()->addItem(MoveStickPlugin::getInstance()->getMoveStick());
            return true;
        }
        return false;
    }
}
