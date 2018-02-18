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
    private $seats = [
        0 => null,
        1 => null,
        2 => null,
        3 => null,
        4 => null,
        5 => null,
        6 => null,
        7 => null,
        8 => null,
        9 => null
    ];

    /**
     * @var integer
     */
    private $chips = 0;

    /**
     * @var array
     */
    private $cards;

    /**
     * @var Deck
     */
    private $deck;

    /**
     * @var bool
     */
    private $isFull = false;

    /**
     * @var bool
     */
    private $hasEnough = false;

    /**
     * Table constructor.
     * @param array $size
     */
    public function __construct(array $size = Table::SML_TABLE)
    {
        $this->size = $size;
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
     * @param int $seat
     * @return int
     */
    public function getPlayerChips(int $seat): int
    {
        return $this->getSeat($seat)->getChips();
    }

    /**
     * @param int $seat
     * @param int $card
     * @return Card
     */
    public function getPlayerCard(int $seat, int $card): Card
    {
        return $this->getSeat($seat)->getCard($card);
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
        return array_filter($this->seats);
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

    /**
     * @param Player $player
     * @param int|null $seatChoice
     */
    public function addPlayer(Player $player, int $seatChoice = null): void
    {
        if ($this->isFull) return;

        if ($seatChoice === null){
            $empty = [];
            foreach ($this->seats as $key => $seat) if (!$seat) $empty[] = $key;
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
     * @return int
     */
    public function getChips(): int
    {
        return $this->chips;
    }

    /**
     * @param int $amount
     */
    public function addChips(int $amount): void
    {
        $this->chips += $amount;
    }

    /**
     * @return array
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * @param int $card
     * @return Card
     */
    public function getCard(int $card): Card
    {
        return $this->cards[$card];
    }

    /**
     *
     */
    public function setFlop()
    {
        $this->deck->burn();
        $this->cards = $this->deck->takeFlop();
    }

    /**
     *
     */
    public function setRiverTurn()
    {
        $this->deck->burn();
        $this->cards[] = $this->deck->takeTop();
    }

    /**
     * @return Deck
     */
    public function getDeck(): Deck
    {
        return $this->deck;
    }

    /**
     * @param Deck $deck
     */
    public function setDeck(Deck $deck): void
    {
        $this->deck = $deck;
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

    /**
     *
     */
    public function DealCards(): void
    {
        for ($i = 2; $i; $i--){
            foreach ($this->seats as $key => $player) {
                if ($player instanceof Player) {
                    $player->addCard($this->deck->takeTop());
                    $this->seats[$key] = $player;
                } else {
                    $this->seats[$key] = null;
                }
            }
        }
    }
}