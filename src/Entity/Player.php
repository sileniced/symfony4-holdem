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
     * @var float
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

    /**
     * @return float
     */
    public function getChips(): float
    {
        return $this->chips;
    }

    /**
     * @param float $chips
     */
    public function setChips(float $chips): void
    {
        $this->chips = $chips;
    }


    /**
     * @param float $amount
     * @return float
     */
    public function betChips(float $amount): float
    {
        $this->chips -= $amount;
        return $amount;
    }

    /**
     * @param float $amount
     */
    public function winChips(float $amount): void
    {
        $this->chips += $amount;
    }

}