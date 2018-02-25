<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 24/02/2018
 * Time: 01:52
 */

namespace App\Services;


use App\Entity\Hand;

/**
 * Class Kicker
 * @package App\Services
 */
class Kicker
{
    /** @var array, two-dimensional [
     *      kickers in the hand,
     *      cards in the kicker
     * ]
     * same order as winners
     */
    private $kickers = [];

    /**
     * @var array, of Hands in order
     */
    private $hands;

    /**
     * @var mixed
     */
    private $score;

    /**
     * Kicker constructor.
     * @param array $winners
     */
    public function __construct(array $winners)
    {
        $this->score = $winners["score"];
        $this->hands = $winners["hand"];
        /** @var Hand $hand */
        foreach ($winners["hand"] as $key => $hand)
            $this->kickers[$key] = $hand->getJudgement()->getKicker();
    }


    /**
     * @return mixed
     */
    public function kick()
    {

        /** @var int $count is the length of kicker hands */
        $count = count($this->kickers[0]);

        /** @var int $i is the rank in every hand */
        for ($i = 0; $i < $count; $i++) {

            $winner = [
                "hand" => [],
                "rank" => 0
            ];

            /** @var array $hand is an array of kicker cards for one winner hand */
            foreach ($this->kickers as $key => $hand) {

                $rank = $hand[$i];
                if ($rank > $winner["rank"])
                    newWinner($winner, $rank, $this->hands["hand"][$key]);
                elseif ($rank == $winner["rank"]) $winner["hand"][] = $this->hands["hand"][$key];
            }

            if (count($winner["hand"]) == 1) return $winner["hand"][0];

        }

        return $winner["hand"] ?? [];

        function newWinner(array &$winner, int $score, Hand $hand): void
        {
            $winner['score'] = $score;
            $winner["hand"] = [$hand];
        }
    }
}