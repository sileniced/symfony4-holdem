<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 22/02/2018
 * Time: 16:32
 */

namespace App\Service;


use App\Entity\Card;

class Judge
{
    /** @var array */
    private $cards;

    /**
     * Judge constructor.
     * @param array $cards
     */
    public function __construct(array $cards)
    {
        $this->cards = $cards;
    }

    public function assertScore(): ?int
    {
        switch (true)
        {
            case $this->royalFlush(): return 0;
            case $this->straightFlush(): return 1;
            case $this->fourOfAKind(): return 2;
            case $this->fullHouse(): return 3;
            case $this->flush(): return 4;
            case $this->straight(): return 5;
            case $this->threeOfAKind(): return 6;
            case $this->twoPair(): return 7;
            case $this->onePair(): return 8;
            default: return 9;
        }
    }

    private function royalFlush(): bool
    {

    }

    private function straightFlush(): bool
    {

    }

    private function fourOfAKind(): bool
    {

    }

    private function fullHouse(): bool
    {

    }

    private function flush(): bool
    {

    }

    private function straight(): bool
    {
        $ranks = sort($this->getRanks());
        for ($i = 0; $i < 3; $i++) {
            if ($ranks[$i] + 1 == $ranks[$i + 1]){
                if ($ranks[$i] + 2 == $ranks[$i + 2]){
                    if ($ranks[$i] + 3 == $ranks[$i + 3]){
                        if ($ranks[$i] + 4 == $ranks[$i + 4]){
                            if ($ranks[$i] + 5 == $ranks[$i + 5]){
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    private function threeOfAKind(): bool
    {
        return 5 > count(array_unique($this->getRanks()));
    }

    private function twoPair(): bool
    {
        return 6 > count(array_unique($this->getRanks()));
    }

    private function onePair(): bool
    {
        return 7 > count(array_unique($this->getRanks()));
    }

    private function getRanks(): array
    {
        /** @var Card $card */
        foreach ($this->cards as $card){
            $ranks[] = $card->getRank();
        }
        return $ranks ?? [];
    }
}