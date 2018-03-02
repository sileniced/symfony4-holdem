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
     *
     */
    const SML_TABLE = [
        "STD" => 50,
        "MIN" => 2,
        "MAX" => 20
    ];

    /**
     *
     */
    const MED_TABLE = [
        "STD" => 250,
        "MIN" => 10,
        "MAX" => 100
    ];

    /**
     *
     */
    const BIG_TABLE = [
        "STD" => 1000,
        "MIN" => 50,
        "MAX" => 500
    ];

    /**
     *
     */
    const TABLE_SEATS = 10;

    /**
     * @var array
     */
    private $size;

    /**
     * @var array
     */
    private $seats = [];

    /**
     * @var bool
     */
    private $isFull = false;

    /**
     * @var bool
     */
    private $hasEnough = false;

    /**
     * @var int
     */
    private $button = 0;

    /**
     * Table constructor.
     * @param array $size
     */
    public function __construct(array $size = Table::SML_TABLE)
    {
        $this->size = $size;
        foreach (range(0,Table::TABLE_SEATS - 1) as $seat) {
            $this->seats[$seat] = null;
        }
    }

    /**
     * @param int $seat
     * @return Player
     */
    public function getSeat(int $seat): ?Player
    {
        return $this->seats[$seat];
    }

    /**
     * @return array
     */
    public function getSeats(): array
    {
        return $this->seats;
    }

    /**
     * @return array
     */
    public function getPlayers(): array
    {
//        var_dump(array_filter($this->seats));
        return array_filter($this->seats);

    }

    /**
     * @return int
     */
    public function getButton(): int
    {
        return $this->button;
    }

    private function isPlayer(int $seat): bool
    {
        return $this->getSeat($seat) instanceof Player;
    }

    /**
     *
     */
    public function nextButton(): void
    {
        while (true) {
            if (++$this->button <= Table::TABLE_SEATS) $this->button = 0;
            if ($this->isPlayer($this->button)) break;
        }
    }

    /**
     * @return int
     */
    public function countPlayers(): int
    {
        return count($this->getPlayers());
    }

    /**
     * @param int $seat
     */
    public function removePlayer(int $seat): void
    {
        $this->seats[$seat] = null;
        $this->isFull = false;
        $this->setHasEnough();
    }

    private function getEmptySeats(): array
    {
        $empty = [];
        foreach ($this->seats as $key => $seat) if (!$seat) $empty[] = $key;
        return $empty;
    }

    /**
     * @param Player $player
     * @param int|null $seatChoice
     */
    public function addPlayer(Player $player, int $seatChoice = null): void
    {
        if ($this->isFull) return;

        if ($seatChoice === null || $this->seats[$seatChoice] instanceof Player){
            $empty = $this->getEmptySeats();
            $this->seats[$empty[array_rand($empty)]] = $player;
        } else {
            $this->seats[$seatChoice] = $player;
        }

        $this->isFull = !in_array(null, $this->seats);
        $this->setHasEnough();
    }

    /**
     * @return bool
     */
    public function isFull(): bool
    {
        return $this->isFull;
    }

    /**
     * @return array
     */
    public function getSize(): array
    {
        return $this->size;
    }

    /**
     * @return int
     */
    public function getChipsSize(): int
    {
        return $this->size['STD'];
    }

    /**
     * @return int
     */
    public function getSmallBlind(): int
    {
        return $this->getBigBlind() / 2;
    }

    /**
     * @return mixed
     */
    public function getBigBlind()
    {
        return $this->size['MIN'];
    }

    /**
     *
     */
    private function setHasEnough(): void
    {
        $this->hasEnough = 1 < count(array_keys($this->seats, !null));
    }

    /**
     * @return bool
     */
    public function hasEnough(): bool
    {
        return $this->hasEnough;
    }
}