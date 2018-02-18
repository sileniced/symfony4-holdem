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

    const SMALL_BLIND = "small blind";
    const BIG_BLIND = "big blind";
    const CALLED = "called";
    const RAISED = "raised";
    const FOLDED = "folded";
    const CHECKED = "checked";
    const BET = "bet";

    const TURN = "turn";
    const BUTTON = "button";

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

    /**
     * @var int
     */
    private $betSize;

    /**
     * @var array
     */
    private $playerStatus = [];

    /**
     * Hand constructor.
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;

        foreach (range(0, Table::TABLE_SEATS - 1) as $player) {
            $this->playerStatus[$player] = [
                "action" => null,
                "chips" => 0
            ];
        }

    }

    public function updateStatus(string $action, int $chips = 0): void
    {
        $this->playerStatus[$this->turn]["action"] = $action;
        $this->playerStatus[$this->turn]["chips"] += $chips;
    }

    public function getPlayerActionStatus(int $seat): string
    {
        return $this->playerStatus[$seat]["action"];
    }

    /**
     * @param int $seat
     * @return Player
     */
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
        $this->selectNextPlayer(Hand::TURN);
    }

    /**
     *
     */
    public function nextButton(): void
    {
        $this->selectNextPlayer(Hand::BUTTON);
        $this->turn = $this->button;
    }

    private function isPlayer(int $seat): bool
    {
        return $this->table->getSeat($seat) instanceof Player;
    }

    private function isFolded(): bool
    {
        return $this->playerStatus[$this->turn]['action'] == Hand::FOLDED;
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
            if ($this->isPlayer($this->$type)) {
                if ($type == Hand::TURN && $this->isFolded()) continue;
                break;
            }
        }
    }

    /**
     *
     * @param int $amount
     * @return int
     */
    public function playerTransfers(int $amount): int
    {
        return $this->table->addChips($this->getPlayer($this->turn)->betChips($amount));
    }

    private function transferSmallBlind(): void
    {
        $blind = $this->table->getSmallBlind();
        $this->playerTransfers($blind);
        $this->updateStatus(Hand::SMALL_BLIND, $blind);
    }

    private function transferBigBlind(): void
    {
        $blind = $this->table->getBigBlind();
        $this->updateStatus(Hand::BIG_BLIND, $blind);
        $this->betSize = $this->playerTransfers($blind);
    }

    /**
     *
     */
    public function takeSmallBigBlind(): void
    {
        $this->nextTurn();
        $this->transferSmallBlind();
        $this->nextTurn();
        $this->transferBigBlind();
        $this->nextTurn();
    }

    /**
     *
     */
    public function playerCalls(): void
    {
        $betSize = $this->betSize;
        $this->playerTransfers($betSize);
        $this->updateStatus(Hand::CALLED, $betSize);
        $this->nextTurn();
    }

    /**
     * @param int $amount
     */
    public function playerRaises(int $amount): void
    {
        if ($amount < $this->betSize) return;
        $this->betSize = $this->playerTransfers($amount);
        $this->updateStatus(Hand::RAISED, $amount);
        $this->nextTurn();
    }

    /**
     *
     */
    public function playerFolds(): void
    {
        $this->updateStatus(Hand::FOLDED);
        $this->nextTurn();
    }
}