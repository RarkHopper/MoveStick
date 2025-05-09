<?php

declare(strict_types = 1);

namespace rarkhopper\movestick;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;

final class AddMotionTask extends Task {
    private const SPEED = 2.5;
    /** @var array<string, Player> */
    protected static array $targets = [];

    public static function join(Player $player) : void {
        self::$targets[$player->getName()] = $player;
        $player->setHasBlockCollision(false);
    }

    public static function quit(Player $player) : void {
        unset(self::$targets[$player->getName()]);
        $player->setHasBlockCollision(true);
    }

    public static function isJoined(Player $player) : bool {
        return isset(self::$targets[$player->getName()]);
    }

    public function onRun() : void {
        foreach (self::$targets as $player) {
            $player->setMotion($player->getDirectionVector()->multiply(self::SPEED));
        }
    }
}
