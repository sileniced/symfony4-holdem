<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 16/02/2018
 * Time: 22:27
 */

namespace App\Services;


/**
 * Class Deck
 * @package App\Entity
 */
class Deck
{
    const FLOP = 3;
    const FIVE = 5;

    /**
     * @var array of Cards
     */
    private $cards = [];

    /**
     *
     */
    public function initialize(): void
    {
        for ($i = 0; $i < 4; $i++){
            for ($ii = 0; $ii < 13; $ii++){
                $this->cards[] = ["suit" => $i, "rank" => $ii];
            }
        }
    }

    public function setCards(array $cards): void
    {
        $this->cards = $cards;
    }

    /**
     *
     */
    public function shuffle(): void
    {
        \shuffle($this->cards);
    }

    /**
     * @return mixed
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return count($this->cards) - 1;
    }

//    /**
//     * @return Card
//     */
//    public function getTop(): Card
//    {
//        return $this->cards[$this->getAmount()];
//    }

    /**
     * @return array of a
     */
    public function takeTop(): array
    {
        return array_pop($this->cards);
    }

    public function takeCards(int $amount): array
    {
        $cards = [];
        for ($i = 0; $i < $amount; $i++) $cards[] = $this->takeTop();
        return $cards;
    }

    public function getCard(int $position): array
    {
        return $this->cards[$position];
    }

}