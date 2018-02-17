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
     * @var Card
     */
    private $card1;

    /**
     * @var Card
     */
    private $card0;

    /**
     * Player constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $this->setName($name);
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

    /**
     * @return mixed
     */
    public function getCard0(): Card
    {
        return $this->card0;
    }

    /**
     * @param mixed $card0
     */
    public function setCard0(Card $card0): void
    {
        $this->card0 = $card0;
    }

    /**
     * @return mixed
     */
    public function getCard1(): Card
    {
        return $this->card1;
    }

    /**
     * @param mixed $card1
     */
    public function setCard1(Card $card1): void
    {
        $this->card1 = $card1;
    }

    /**
     * @return array
     */
    public function getCards(): array
    {
        return [$this->card0, $this->card1];
    }

    /**
     * @param array $cards
     */
    public function setCards(array $cards): void
    {
        $this->card0 = $cards[0];
        $this->card1 = $cards[1];
    }


}