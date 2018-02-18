<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 16/02/2018
 * Time: 21:17
 */

namespace App\Entity;


/**
 * Class Player
 * @package App\Entity
 */
class Player
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $cards;

    /**
     * @var int
     */
    private $chips;

    /**
     * Player constructor.
     * @param string $name
     * @param int $chips
     */
    public function __construct(string $name, int $chips)
    {
        $this->name = $name;
        $this->chips = $chips;
    }

    /**
     * @return mixed
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getCard(int $card): Card
    {
        return $this->cards[$card];
    }

    public function addCard(Card $card): void
    {
        $this->cards[] = $card;
    }

    /**
     * @return array
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * @param array $cards
     */
    public function setCards(array $cards): void
    {
        $this->cards = $cards;
    }

    /**
     * @return int
     */
    public function getChips(): int
    {
        return $this->chips;
    }

    /**
     * @param int $chips
     */
    public function setChips(int $chips): void
    {
        $this->chips = $chips;
    }


    /**
     * @param int $amount
     * @return int
     */
    public function betChips(int $amount): int
    {
        $this->chips -= $amount;
        return $amount;
    }

    /**
     * @param int $amount
     */
    public function winChips(int $amount): void
    {
        $this->chips += $amount;
    }

}