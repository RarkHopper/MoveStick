<?php
declare(strict_types = 1);

namespace rarkhopper\movestick;

use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase{

	protected static Item $movestick;

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents(new EventHandler, $this);
		self::$movestick = Item::get(Item::FISHING_ROD, 102);
		self::$movestick->setCustomName('§cMoveStick');
		Item::addCreativeItem(self::$movestick);
		$this->getScheduler()->scheduleRepeatingTask(new AddMotionTask, 1);
		$this->getServer()->getCommandMap()->register($this->getName(), new MoveStickCommand);
	}

	public function onDisable(){
		Item::removeCreativeItem(self::$movestick);
	}

	public static function getMoveStick():Item{
		return clone self::$movestick;
	}
}
