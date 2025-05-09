<?php
declare(strict_types = 1);

namespace rarkhopper\movestick;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;

class EventHandler implements Listener{

	public function onInteract(PlayerInteractEvent $ev):void{
		$player = $ev->getPlayer();

		if(!$player->isOp()) return;
		if(!$ev->getItem()->equals(Main::getMoveStick())) return;
		AddMotionTask::isJoined($player)? AddMotionTask::quit($player): AddMotionTask::join($player);
	}

	public function onQuit(PlayerQuitEvent $ev):void{
		$player = $ev->getPlayer();

		if(AddMotionTask::isJoined($player)) AddMotionTask::quit($player);
	}
}
