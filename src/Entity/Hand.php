<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 18/02/2018
 * Time: 20:11
 */

namespace App\Entity;


/**
 * Class Hand
 * @package App\Entity
 */
class Hand
{

    /**
     * @var Table
     */
    private $table;

    /**
     * @var int
     */
    private $button = 0;

    /**
     * @var int
     */
    private $turn = 0;

    private $betSize;

    /**
     * Hand constructor.
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    /**
     * @param int $amount
     */
    public function playerTransfers(int $amount): void
    {
        $this->table->addChips($this->getPlayer($this->turn)->betChips($amount));
    }

    public function getPlayer(int $seat): Player
    {
        return $this->table->getSeat($seat);
    }

    /**
     * @return int
     */
    public function getTurn(): int
    {
        return $this->turn;
    }

    /**
     * @return int
     */
    public function getButton(): int
    {
        return $this->button;
    }

    /**
     *
     */
    public function nextTurn(): void
    {
        $this->selectNextPlayer("turn");
    }

    /**
     *
     */
    public function nextButton(): void
    {
        $this->selectNextPlayer("button");
        $this->turn = $this->button;
    }

    /**
     * @param string $type
     */
    private function selectNextPlayer(string $type): void
    {
        if (!$this->table->hasEnough()) return;

        while (true)
        {
            if (++$this->$type >= Table::TABLE_SEATS) $this->$type = 0;
            if ($this->table->getSeat($this->$type) instanceof Player) break;
        }
    }

    /**
     *
     */
    public function takeSmallBigBlind(): void
    {
        $this->nextTurn();
        $this->playerTransfers($this->table->getSmallBlind());
        $this->nextTurn();
        $this->playerTransfers($this->table->getBigBlind());
        $this->betSize = $this->table->getBigBlind();
        $this->nextTurn();
    }

    public function playerCalls(): void
    {
        $this->playerTransfers($this->betSize);
        $this->nextTurn();
    }

    public function playerRaises(int $amount): void
    {
        if ($amount < $this->betSize) return;
        $this->playerTransfers($amount);
        $this->betSize = $amount;
        $this->nextTurn();
    }
}