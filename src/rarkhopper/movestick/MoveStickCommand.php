<?php
declare(strict_types = 1);

namespace rarkhopper\movestick;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class MoveStickCommand extends Command{

	public function __construct(){
		parent::__construct('movestick');
	}

	public function execute(CommandSender $sender, string $label, array $args){
		if(!$sender instanceof Player){
			$sender->sendMessage('ゲーム内で実行してください');
			return;
		}
		$movestick = Main::getMoveStick();

		if(!$sender->isOp() or !$sender->getInventory()->canAddItem($movestick)) return;
		$sender->getInventory()->addItem($movestick);
	}
}
