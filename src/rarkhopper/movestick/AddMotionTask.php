<?php

declare(strict_types = 1);

namespace rarkhopper\movestick;

use pocketmine\Player;
use pocketmine\scheduler\Task;

class AddMotionTask extends Task {
    /** @var Player[string] */
    protected static array $targets = [];

    public static function join(Player $player) : void {
        self::$targets[$player->getName()] = $player;
        $player->namedtag->setInt('movestick.old.gamemode', $player->getGamemode());
        $player->setGamemode(Player::SPECTATOR);
    }

    public static function quit(Player $player) : void {
        unset(self::$targets[$player->getName()]);
        $player->setGamemode($player->namedtag->getInt('movestick.old.gamemode', Player::CREATIVE));
    }

    public static function isJoined(Player $player) : bool {
        return isset(self::$targets[$player->getName()]);
    }

    final public function onRun(int $tick) {
        /** @var Player $player */
        foreach (self::$targets as $player) {
            if (!$player instanceof Player) {
                return;
            } //リフレクション対策
            $player->setMotion($player->getDirectionVector()->multiply(2.3));
        }
    }
}
