<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 22/02/2018
 * Time: 16:32
 */

namespace App\Services;


use App\Entity\Card;

class Judge
{
    /** @var array */
    private $cards;

    /** @var array */
    private $ranks;

    /** @var array */
    private $suits;

    /** @var bool */
    private $flush;

    /**
     * Judge constructor.
     * @param array $cards
     */
    public function __construct(array $cards)
    {
        $this->cards = $cards;
        /** @var Card $card */
        foreach ($this->cards as $card){
            $this->ranks[] = $card->getRank();
            $this->suits[] = $card->getSuit();
        }
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
        return $this->flush() && $this->getFlushSuit() == Card::HEARTS ? $this->straight($this->getFlushRanks()) : false;
    }

    private function straightFlush(): bool
    {
        if (!$this->flush()) return false;
        return $this->straight($this->getFlushRanks());
    }

    private function fourOfAKind(): bool
    {
        return in_array(4, array_count_values($this->ranks));
    }

    private function fullHouse(): bool
    {
        $ranks = array_count_values($this->ranks);
        return in_array(2, $ranks) && in_array(3, $ranks);
    }

    private function flush(): bool
    {
        if (!$this->flush) $this->flush = max(array_count_values($this->suits)) >= 5;
        return $this->flush;
    }

    private function getFlushSuit(): int
    {
        $values = array_count_values($this->suits);
        return array_search(max($values), $values);
    }

    private function getFlushRanks(): array
    {
        foreach (array_keys($this->suits, $this->getFlushSuit()) as $key) {
            $cards[] = $this->ranks[$key];
        }

        return $cards ?? [];
    }

    private function straight(array $ranks = null): bool
    {
        if (!$ranks) $ranks = $this->ranks;
        sort($ranks);

        for ($i = 0; $i < 1; $i++) {

            $need = 5;
            $count = 1;

            if ($ranks[$i] > 12 - 4) return false;
            if ($ranks[$i] == 0 && in_array(12, $ranks)) $need--;

            while ($count < $need) {
                if ($ranks[$i] + $count == $ranks[$i + $count]) {
                    if (++$count >= $need) return true;
                    continue;
                }
                break;
            }

        }
        return false;
    }

    private function threeOfAKind(): bool
    {
        return in_array(3, array_count_values($this->ranks));
    }

    private function twoPair(): bool
    {
        return 6 > count(array_unique($this->ranks));
    }

    private function onePair(): bool
    {
        return 7 > count(array_unique($this->ranks));
    }
}