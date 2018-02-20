<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 16/02/2018
 * Time: 22:17
 */

namespace App\Entity;


class Card
{
    /**
     * @var integer
     */
    private $suit;

    /**
     * @var integer
     */
    private $rank;


    /**
     * Card constructor.
     * @param int $suit
     * @param int $rank
     */
    public function __construct(int $suit, int $rank)
    {
        $this->suit = $suit;
        $this->rank = $rank;
    }

    /**
     * @return int
     */
    public function getSuit(): int
    {
        return $this->suit;
    }

    /**
     * @return int
     */
    public function getRank(): int
    {
        return $this->rank;
    }

    /**
     * @return string
     */
    public function getCardName(): string
    {

        switch ($this->rank)
        {
            case 9: $rank = "Jack"; break;
            case 10: $rank = "Queen"; break;
            case 11: $rank = "King"; break;
            case 12: $rank = "Ace"; break;
            default: $rank = $this->rank + 2;
        }

        switch ($this->suit)
        {
            case 0: $suit = "Hearts"; break;
            case 1: $suit = "Diamonds"; break;
            case 2: $suit = "Spades"; break;
            case 3: $suit = "Clubs"; break;
        }

        return sprintf("%s of %s", $rank, $suit);
    }

    /**
     * @return string
     */
    public function getCardCode(): string
    {
        return sprintf("%s_%s", $this->suit, $this->rank);
    }


}