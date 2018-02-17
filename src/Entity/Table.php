<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 17/02/2018
 * Time: 02:10
 */

namespace App\Entity;


/**
 * Class Table
 * @package App\Entity
 */
class Table
{
    /**
     * @var array
     */
    private $players = [
        0 => null,
        1 => null,
        2 => null,
        3 => null,
        4 => null,
        5 => null
    ];


    /**
     * @var bool
     */
    private $isFull = false;

    /**
     * Table constructor.
     * @param Player $player
     * @param int $seat
     */
    public function __construct(Player $player, int $seat = 6)
    {
        $this->addPlayer($player, $seat);
    }

    /**
     * @param int $seat
     * @return Player
     */
    public function getPlayer(int $seat): Player
    {
        return $this->players[$seat];
    }

    /**
     * @return array
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * @param array $players
     */
    public function setPlayers(array $players): void
    {
        $this->players = $players;
    }

    /**
     * @param int $seat
     */
    public function removePlayer(int $seat): void
    {
        $this->players[$seat] = null;
        $this->isFull = false;
    }

    /**
     * @param Player $player
     * @param int $seat
     */
    public function addPlayer(Player $player, int $seat = 6): void
    {
        if ($this->isFull) return;

        if ($seat > 5){
            $empty = [];
            foreach ($this->players as $key => $seat_){
                if (!$seat_) {
                    $empty[] = $key;
                }
            }

            $this->players[$empty[array_rand($empty)]] = $player;
        } else {
            $this->players[$seat] = $player;
        }

        $this->isFull = !in_array(null, $this->players);

    }

    /**
     * @return bool
     */
    public function isFull(): bool
    {
        return $this->isFull;
    }

}