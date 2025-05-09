<?php

declare(strict_types = 1);

namespace rarkhopper\movestick;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerMissSwingEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\ClickSound;
use function max;

final class EventHandler implements Listener {
    public function onItemUse(PlayerItemUseEvent $ev) : void {
        $player = $ev->getPlayer();

        if (!Server::getInstance()->isOp($player->getName())) {
            return;
        }

        if (!MoveStickPlugin::getInstance()->isMoveStick($ev->getItem())) {
            return;
        }

        $player->getWorld()->addSound($player->getPosition(), new ClickSound(), [$player]);
        AddMotionTask::isJoined($player)? AddMotionTask::quit($player): AddMotionTask::join($player);
        $ev->cancel();
    }

    public function onMissSwing(PlayerMissSwingEvent $ev) : void {
        $player = $ev->getPlayer();

        if (!Server::getInstance()->isOp($player->getName())) {
            return;
        }

        $item = $player->getInventory()->getItemInHand();
        if (!MoveStickPlugin::getInstance()->isMoveStick($item)) {
            return;
        }

        // プレイヤーの10ブロック先の位置を計算
        // 視線方向のパス全体にブロックがあるかレイトレーシングで確認する
        $direction = $player->getDirectionVector();
        $eyePos = $player->getEyePos();
        $distance = 10; // テレポートの最大距離
        $step = 0.5; // レイトレーシングのステップサイズ

        // テレポート可能な最遠点を見つける
        $maxDistance = $distance;

        for ($d = 0; $d <= $distance; $d += $step) {
            $checkPos = $eyePos->addVector($direction->multiply($d));
            $block = $player->getWorld()->getBlock($checkPos);

            if (!$block->isTransparent()) {
                // パス上にブロックが見つかった場合、その手前で停止
                $maxDistance = max(0, $d - 0.5);
                break;
            }
        }

        // 最終的な目標位置を設定
        $targetPos = $eyePos->addVector($direction->multiply($maxDistance));
        $direction = $player->getDirectionVector();
        $eyePos = $player->getEyePos();
        $targetPos = $eyePos->addVector($direction->multiply(10));

        $player->teleport($targetPos);
        $player->getWorld()->addSound($player->getPosition(), new ClickSound(), [$player]);
        $ev->cancel();
    }

    public function onBlockBreak(BlockBreakEvent $ev) : void {
        $player = $ev->getPlayer();
        $item = $player->getInventory()->getItemInHand();

        if (MoveStickPlugin::getInstance()->isMoveStick($item)) {
            $ev->cancel();
        }
    }

    public function onQuit(PlayerQuitEvent $ev) : void {
        $player = $ev->getPlayer();

        if (AddMotionTask::isJoined($player)) {
            AddMotionTask::quit($player);
        }
    }

    public function onItemDrop(PlayerDropItemEvent $ev) : void {
        if (!MoveStickPlugin::getInstance()->isMoveStick($ev->getItem())) {
            return;
        }

        $ev->cancel();
        $ev->getPlayer()->sendMessage(TextFormat::RED . "MoveStickは地面に落とせません");
    }
}
