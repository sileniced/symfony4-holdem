<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 24/02/2018
 * Time: 02:07
 */

namespace App\Entity;


class Judgement
{
    /** @var int  */
    private $score;

    /** @var array */
    private $kicker;

    public function __construct(int $score, array $kicker)
    {
        $this->score = $score;
        $this->kicker = $kicker;
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @return mixed
     */
    public function getKicker(): array
    {
        return $this->kicker;
    }

}