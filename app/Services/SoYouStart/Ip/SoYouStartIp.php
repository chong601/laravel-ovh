<?php
namespace App\Services\SoYouStart\Ip;

use Ovh\Api;

/**
 * SoYouStart IP related operations
 *
 * @property AntiHack $antihack
 * @property Arp $arp
 * @property Game $game
 * @property License $license
 * @property MigrationToken $migrationToken
 * @property Move $move
 * @property Park $park
 * @property Phishing $phishing
 * @property Reverse $reverse
 * @property Ripe $ripe
 * @property Service $service
 * @property Spam $spam
 * @property Task $task
 */
class SoYouStartIp
{
    private static $mapStringToClass = [
        'antihack' => AntiHack::class,
        'arp' => Arp::class,
        'game' => Game::class,
        'license' => License::class,
        'migrationToken' => MigrationToken::class,
        'move' => Move::class,
        'park' => Park::class,
        'phishing' => Phishing::class,
        'reverse' => Reverse::class,
        'ripe' => Ripe::class,
        'service' => Service::class,
        'spam' => Spam::class,
        'task' => Task::class
    ];

    public function __construct(Api $ovh_api)
    {
        $this->ovh_api = $ovh_api;
    }

    // Thanks Stripe for this design!
    public function __get($name)
    {
        return array_key_exists($name, self::$mapStringToClass) ? new self::$mapStringToClass[$name]($this->ovh_api) : null;
    }
}
