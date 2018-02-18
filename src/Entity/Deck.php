<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 16/02/2018
 * Time: 22:27
 */

namespace App\Entity;


/**
 * Class Deck
 * @package App\Entity
 */
class Deck
{
    /**
     * @var
     */
    private $deck = [];

    /**
     * Deck constructor.
     * @param bool $shuffle
     */
    public function __construct(bool $shuffle = true)
    {
        $this->initialize();
        if ($shuffle) $this->shuffle();
    }

    /**
     *
     */
    private function initialize(): void
    {
        for ($i = 0; $i < 4; $i++){
            for ($ii = 0; $ii < 13; $ii++){
                $this->deck[] = new Card($i, $ii);
            }
        }
    }

    /**
     *
     */
    public function shuffle(): void
    {
        \shuffle($this->deck);
    }

    /**
     * @return mixed
     */
    public function getDeck(): array
    {
        return $this->deck;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return count($this->deck) - 1;
    }

    /**
     * @return Card
     */
    public function getTop(): Card
    {
        return $this->deck[$this->getAmount()];
    }

    /**
     * @return Card
     */
    public function takeTop(): Card
    {
        return array_pop($this->deck);
    }

    public function takeFlop(): array
    {
        return [$this->takeTop(), $this->takeTop(), $this->takeTop()];
    }

    /**
     *
     */
    public function burn(): void
    {
        unset($this->deck[$this->getAmount()]);
    }

    public function getCard(int $position): Card
    {
        return $this->deck[$position];
    }

}