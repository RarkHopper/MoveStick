<?php

declare(strict_types = 1);

namespace rarkhopper\movestick;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

final class MoveStickPlugin extends PluginBase {
    use SingletonTrait;

    private Item $movestick;

    protected function onEnable() : void {
        self::setInstance($this);

        $this->getServer()->getPluginManager()->registerEvents(new EventHandler(), $this);
        $this->movestick = VanillaItems::FISHING_ROD()
            ->setNamedTag(
                CompoundTag::create()
                    ->setString('MoveStick', 'true')
            );
        $this->movestick = $this->movestick->setCustomName('§cMoveStick');  // HACK: chainすると反映されない

        $this->getScheduler()->scheduleRepeatingTask(new AddMotionTask(), 1);
        $this->getServer()->getCommandMap()->register("movestick", new MoveStickCommand());
    }

    public function getMoveStick() : Item {
        return clone $this->movestick;
    }

    public function isMoveStick(Item $item) : bool {
        return $item->equals($this->movestick);
    }
}
