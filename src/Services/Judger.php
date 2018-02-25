<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 22/02/2018
 * Time: 16:32
 */

namespace App\Services;


use App\Entity\Card;
use App\Entity\Judgement;

class Judger
{
    const HIGH_CARD = 0;
    const PAIR = 1;
    const TWO_PAIR = 2;
    const THREE_OF_A_KIND = 3;
    const STRAIGHT = 4;
    const FLUSH = 5;
    const FULL_HOUSE = 6;
    const FOUR_OF_A_KIND = 7;
    const STRAIGHT_FLUSH = 8;
    const ROYAL_FLUSH = 9;




    /** @var array of Cards */
    private $cards;

    /** @var array */
    private $ranks;

    /** @var array */
    private $suits;

    /** @var bool */
    private $flush;
    
    /** @var int */
    private $highCard;
    
    /** @var int */
    private $uniques;
    
    /** @var array */
    private $rankValues;

    /** @var int */
    private $straightRank;

    /**
     * @param array $cards
     * @return Judgement
     */
    public function judge(array $cards): Judgement
    {

        $this->cards = $cards;
        /** @var Card $card */
        foreach ($this->cards as $key => $card){
            $this->ranks[$key] = $card->getRank();
            $this->suits[$key] = $card->getSuit();
        }

        /** @var int $highCard */
        $this->highCard = max($this->ranks);
        $this->rankValues = array_count_values($this->ranks);
        $this->flush = max(array_count_values($this->suits)) >= 5;
        $this->uniques = count(array_unique($this->ranks));

        switch (true) {

            /**
             *
             * Judgement ( score: from 0 - Highcard to 9 - Royal Flush, kicker: described below )
             *
             * @returns kicker:
             *      extra data required if the max score is shared among Hands
             *
             */


            /**
             * @return null, only one hand can have a Royal FLush
             *
             * @case null
             */
            case $this->isRoyalFlush():     return new Judgement(9, null);

            /**
             * @return int, the highest rank in the flush
             *
             * @case 0 (Highest rank)
             */
            case $this->isStraightFlush():  return new Judgement(8, [$this->straightRank]);

            /**
             * @return int, the rank of the Four of a kind
             * no way that two four of a Kinds compete each other
             *
             * @case 0 (Highest rank)
             */
            case $this->isFourOfAKind():    return new Judgement(7, [array_search(4, $this->rankValues)]);

            /**
             * @return array -- [
             *    * 0 => three of a kind rank,
             *    * 1 => two of a kind rank
             * ]
             * 0 => check if the three of a kind has a higher rank
             * 1 => then check the pair for the higher rank
             *
             * @else split pot, it could be that two hands
             *  share all five card ranks
             *
             * @case 1 (map through array)
             */
            case $this->isFullHouse():      return new Judgement(6, $this->getFullHouseKicker());

            /**
             * full suit
             * @return int, the highest card of the flush suit
             *
             * @case 0 (highest rank)
             */
            case $this->isFlush():          return new Judgement(5, [$this->getFlushKicker()]);

            /**
             * ranks consequent of each other
             * @return array, [
             *      * 0 => with one value: highest card of the rank
             * ]
             *
             * @else if more hands have the same high rank
             *
             * @case 1 (map through array)
             */
            case $this->isStraight():       return new Judgement(4, [$this->straightRank]);

            /**
             * @return array -- [
             *      * 0 => three of a kind rank,
             *      1 => (fourth card) highest rank excluding prev rank,
             *      2 => (fifth card) second highest rank
             * ]
             *
             * @else split pot if hands have similar ranks
             *
             * @case 1 (map through array)
            **/
            case $this->isThreeOfAKind():   return new Judgement(3, $this->getThreeOfAKindKicker());

            /**
             * @return array -- [
             *      * 0 => Highest rank of the two pairs,
             *      * 1 => lower rank of the two pairs,
             *      2 => highest kicker
             * ]
             *
             * @else split pot if hands have similar ranks
             *
             * @case 1 (map through array)
             */
            case $this->isTwoPairs():       return new Judgement(2, $this->getTwoPairsKicker());

            /**
             * @return array -- [
             *      * 0 => rank of the pair,
             *      1 => highest loose card,
             *      2 => second highest --,
             *      3 => third
             * ]
             *
             * @else split pot of hands have similar ranks
             *
             * @case 1 (map through array)
             */
            case $this->isPair():           return new Judgement(1, $this->getPairKicker());

            /** KICKER -> highest of the ranks */
            /** KICKER -> second, third..... highest of the ranks */
            /**
             * @return array -- of the 5 highest cards in reverse sort [
             *      0 => highest card
             *      1 => second highest
             *      2 => third
             *      3 => fourth
             *      4 => fifth
             * ]
             *
             * @else split pot if hands have similar hands
             *
             * @case 1 (map through array)
             */
            default:                        return new Judgement(0, $this->getHighCardKicker());
        }
    }

    /**
     * @return bool
     *
     * is a straight flush with Ace high
     */
    private function isRoyalFlush(): bool
    {
        return $this->flush && $this->getFlushKicker() == Card::ACE ? $this->isStraight($this->getFlushRanks()) : false;
    }

    /**
     * @return bool
     */
    private function isStraightFlush(): bool
    {
        return $this->flush && $this->isStraight($this->getFlushRanks());
    }

    /**
     * @return bool
     */
    private function isFourOfAKind(): bool
    {
        return in_array(4, $this->rankValues);
    }

    /**
     * @return bool
     */
    private function isFullHouse(): bool
    {
        return in_array(2, $this->rankValues) && in_array(3, $this->rankValues);
    }

    /**
     * @return bool
     */
    private function isFlush(): bool
    {
        return $this->flush;
    }

    /**
     * @param array|null $ranks
     * @return bool
     */
    private function isStraight(array $ranks = null): bool
    {
        if (!$ranks) $ranks = $this->ranks;
        sort($ranks);

        for ($i = 0; $i < 3; $i++) {

            $need = 5;
            $count = 1;

            if ($ranks[$i] > 12 - 4) return false;
            if ($ranks[$i] == 0 && in_array(12, $ranks)) $need--;

            while ($count < $need) {
                if ($ranks[$i] + $count == $ranks[$i + $count]) {
                    if (++$count >= $need) {
                        $this->straightRank = $ranks[$i + $count - 1];
                        return true;
                    }
                    continue;
                }
                break;
            }

        }
        return false;
    }

    /**
     * @return bool
     */
    private function isThreeOfAKind(): bool
    {
        return in_array(3, $this->rankValues);
    }

    /**
     * @return bool
     */
    private function isTwoPairs(): bool
    {
        return $this->uniques < 6;
    }

    /**
     * @return bool
     */
    private function isPair(): bool
    {
        return $this->uniques < 7;
    }








    /****************************************\
     *           KICKER FUNCTIONS           *
    \****************************************/

    /**
     * @return array -- [
     *    * 0 => three of a kind rank,
     *    * 1 => two of a kind rank
     * ]
     */
    private function getFullHouseKicker(): array
    {
        return [array_search(3, $this->rankValues), array_search(2, $this->rankValues)];
    }

    /**
     * @return int, highest rank of the flush
     */
    private function getFlushKicker(): int
    {
        $suit = $this->getFlushSuit();
        $max = [];
        foreach ($this->suits as $key => $suits)
            if ($suits == $suit) $max[] = $this->ranks[$key];

        return max($max);
    }

    /**
     * @return array -- [
     *      * 0 => three of a kind rank,
     *      1 => (fourth card) highest rank excluding prev rank,
     *      2 => (fifth card) second highest rank
     * ]
     */
    private function getThreeOfAKindKicker(): array
    {
        $threeOfAKind = array_search(3, $this->rankValues);

        return array_merge([$threeOfAKind], $this->getHighCards(2, [$threeOfAKind]));
    }

    /**
     * @return array -- [
     *      * 0 => Highest rank of the two pairs,
     *      * 1 => lower rank of the two pairs,
     *      2 => (fifth card) highest kicker
     * ]
     */
    private function getTwoPairsKicker(): array
    {
        $ranks = array_keys($this->rankValues, 2);
        return array_merge($ranks, $this->getHighCards(1, $ranks));
    }

    /**
     * @return array -- [
     *      * 0 => rank of the pair,
     *      1 => highest loose card,
     *      2 => second highest --,
     *      3 => third
     * ]
     */
    private function getPairKicker(): array
    {
        $ranks = array_search(2, $this->rankValues);
        return array_merge([$ranks], $this->getHighCards(3, [$ranks]));
    }

    /**
     * @return array -- of the 5 highest cards in reverse sort [
     *      0 => highest card
     *      1 => second highest
     *      2 => third
     *      3 => fourth
     *      4 => fifth
     * ]
     */
    private function getHighCardKicker(): array
    {
        return $this->getHighCards(5);
    }








    /****************************************\
     *           HELPER FUNCTIONS           *
    \****************************************/

    /**
     * @return int
     */
    private function getFlushSuit(): int
    {
        $values = array_count_values($this->suits);
        return array_search(max($values), $values);
    }

    /**
     * @return array
     */
    private function getFlushRanks(): array
    {
        foreach (array_keys($this->suits, $this->getFlushSuit()) as $key)
            $cards[] = $this->ranks[$key];

        return $cards ?? [];
    }

    private function getHighCards(int $amount, array $ignoreRanks = []): array
    {
        $ranks = $this->ranks;

        foreach ($ignoreRanks as $ignoreRank)
           foreach (array_keys($ranks, $ignoreRank) as $ignoreKey)
               unset($ranks[$ignoreKey]);

        sort($ranks);

        for ($i = 0; $i < $amount; $i++) $return[] = $ranks[$i];

        return $return ?? [];

    }


}