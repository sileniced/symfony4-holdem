<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 18/02/2018
 * Time: 20:11
 */

namespace App\Entity;

use App\Services\Judge;


/**
 * Class Game
 * @package App\Entity
 */
class Game
{

    const POINT = "point";
    const BUTTON = "button";

    const PRE_HAND = "pre-hand";
    const PRE_FLOP = "pre-flop";
    const FLOP = "flop";
    const RIVER = "river";
    const TURN = "turn";
    const SHOWDOWN = "showdown";
    const ENDED = "ended";


    const SMALL_BLIND = "small blind";
    const BIG_BLIND = "big blind";

    const CALLED = "called";
    const RAISED = "raised";
    const FOLDED = "folded";
    const CHECKED = "checked";
    const BET = "bet";

    const WON = "won";
    const SPLIT_POT = "split pot";

    /**
     * @var Table
     */
    private $table;

    /**
     * @var int
     */
    private $point = 0;

    /**
     * @var int
     */
    private $call;

    /**
     * @var int
     */
    private $folds;

    /**
     * @var array of Hands
     */
    private $hands = [];

    /**
     * @var string
     */
    private $status = self::PRE_HAND;

    /**
     * @var integer
     */
    private $pot = 0;

    /**
     * @var array
     */
    private $cards;

    /**
     * @var Deck
     */
    private $deck;

    /**
     * @var array of Hands
     */
    private $winner = [];

    /**
     * Game constructor.
     * @param Table $table
     * @param bool $shuffle
     */
    public function __construct(Table $table, bool $shuffle = true)
    {
        $this->table = $table;

        foreach ($this->table->getPlayers() as $key => $player) {
            $this->hands[] = new Hand($key);
        }

        $this->folds = $this->countHands() - 1;

        $this->deck = new Deck($shuffle);
    }

    /**
     * @return Deck
     */
    public function getDeck(): Deck
    {
        return $this->deck;
    }

    /**
     * @param Deck $deck
     */
    public function setDeck(Deck $deck): void
    {
        $this->deck = $deck;
    }

    /**
     * @return Table
     */
    public function getTable(): Table
    {
        return $this->table;
    }

    /**
     *
     */
    public function dealCard(): void
    {
        $this->getHand($this->point)->addCard($this->takeTopCard());
    }

    /**
     *
     */
    public function removeUnusedCards()
    {
        $this->deck->setCards($this->deck->takeCards(Deck::FIVE));
    }

    /**
     * @return array
     */
    public function getWinner(): array
    {
        return $this->winner;
    }

    /**
     * @param int $point
     * @return Player
     */
    public function getPlayer(int $point = null): Player
    {
        return $this->table->getSeat($this->getHand($point === null ? $this->point : $point)->getSeat());
    }

    public function getPlayerName(int $point): string
    {
        return $this->getPlayer($point)->getName();
    }


    /**
     * @param int $point
     * @return int
     */
    public function getPlayerChips(int $point = null): int
    {
        return $this->getPlayer($point === null ? $this->point : $point)->getChips();
    }

    /**
     * @param int $point
     * @param int $card
     * @return Card
     */
    public function getHandCard(int $point = null, int $card): Card
    {
        return $this->getHand($point === null ? $this->point : $point)->getCard($card);
    }

    /**
     * @param int $point
     * @return Hand
     */
    public function getHand(int $point): Hand
    {
        return $this->hands[$point];
    }

    /**
     * @return array
     */
    public function getHands(): array
    {
        return $this->hands;
    }

    /**
     * @return int
     */
    public function countHands(): int
    {
        return count($this->hands);
    }

    /**
     * @param string $status
     * @param int|null $point
     */
    public function updateHandStatus(string $status, int $point = null): void
    {
        $this->getHand($point === null ? $this->point : $point)->setStatus($status, $this->status);
    }

    /**
     * @param int $point
     * @return null|string
     */
    public function getHandStatus(int $point = null): ?string
    {
        return $this->getHand($point === null ? $this->point : $point)->getStatus();
    }

    /**
     * @param int $point
     * @return int|null
     */
    public function getHandChips(int $point = null): ?int
    {
        return $this->getHand($point === null ? $this->point : $point)->getChips();
    }

    public function getHandJudgement(int $point): array
    {
        return $this->getHand($point)->getJudgement();
    }


    public function getHandJudgementName(int $point): string
    {
        switch($this->getHand($point)->getJudgement()[0]) {
            case (1): return "pair";
            case (2): return "two pair";
            case (3): return "three of a kind";
            case (4): return "straight";
            case (5): return "flush";
            case (6): return "full house";
            case (7): return "four of a kind";
            case (8): return "straight flush";
            case (9): return "royal flush";
            default: return "high card";
        }
    }

    public function getJudgements(): array
    {
        /** @var Hand $hand */
        foreach ($this->getHands() as $hand) {
            $judgements[] = $hand->getJudgement();
        }

        return $judgements ?? [];
    }

    /**
     * @return bool
     */
    public function isFolding(): bool
    {
        return !--$this->folds;
    }

    /**
     * @param int $point
     * @return bool
     */
    public function hasFolded(int $point = null): bool
    {
        return $this->getHandStatus($point === null ? $this->point : $point) == self::FOLDED;
    }

    /**
     * @param int $point
     * @return int
     */
    public function isBlind(int $point = null): int
    {
        switch ($this->getHandStatus($point === null ? $this->point : $point)) {
            case self::SMALL_BLIND: return $this->table->getSmallBlind();
            case self::BIG_BLIND: return $this->table->getBigBlind();
            default: return 0;
        }
    }

    /**
     * @return int
     */
    public function getPoint(): int
    {
        return $this->point;
    }

    /**
     * @return bool
     */
    public function hasPhaseEnded(): bool
    {
        $hand = $this->getHandStatus($this->point);
        return $this->getHandChips($this->point) == $this->call &&
            $hand !== null &&
            $hand !== self::BIG_BLIND;
    }

    /**
     * @param int $amount
     * @return bool
     */
    public function nextPoint(int $amount = 1): bool
    {
        while ($amount > 0) {
            if (++$this->point == $this->countHands()) $this->point = 0;
            if (!$this->hasFolded($this->point)) $amount--;
        }
        return $this->hasPhaseEnded();
    }

    /**
     *
     */
    public function resetPoint(): void
    {
        $this->point = $this->table->getButton();
    }

    /**
     *
     * @param int $amount
     * @return int
     */
    public function playerTransfers(int $amount): int
    {
        return $this->addToPot($this->getPlayer($this->point)->betChips($amount));
    }

    /**
     *
     */
    public function resetHandsStatus(): void
    {
        /** @var Hand $hand */
        foreach ($this->hands as $hand) $hand->resetHandStatus();
        $this->call = 0;
    }

    /**
     *
     */
    public function transferSmallBlind(): void
    {
        $this->playerTransfers($this->table->getSmallBlind());
        $this->updateHandStatus(self::SMALL_BLIND);
    }

    /**
     *
     */
    public function transferBigBlind(): void
    {
        $this->call = $this->playerTransfers($this->table->getBigBlind());
        $this->updateHandStatus(self::BIG_BLIND);
    }

    /**
     *
     */
    public function setFlop(): void
    {
        $this->cards = (array) $this->deck->takeCards(Deck::FLOP);
    }

    /**
     *
     */
    public function setRiverTurn(): void
    {
        $this->cards[] = $this->deck->takeTop();
    }

    /**
     * @return int
     */
    public function getPot(): int
    {
        return $this->pot;
    }

    /**
     * @param int $amount
     * @return int
     */
    public function addToPot(int $amount): int
    {
        $this->pot += $amount;
        $this->getHand($this->point)->addChips($amount);
        return $amount;
    }

    /**
     * @param int|null $point
     * @param int|null $amount
     */
    public function potTransfers(int $point = null, int $amount = null): void
    {
        $this->updateHandStatus(self::WON, $point === null ? $this->point : $point);
        if ($this->winner == []) $this->winner = [
            "iteration" => 0,
            "hand" => $point === null ? $this->point : $point,
            "score" => -1
        ];
        $this->getPlayer($point)->winChips($amount ?: $this->pot);
    }

    /**
     * @param array $points
     */
    public function splitPotTransfers(array $points): void
    {
        $split = $this->pot / count($points);

        /** @var int $point */
        foreach ($points as $point) {
            $this->potTransfers($point, $split);
        }
    }

    /**
     * @return array
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * @param Hand $hand
     * @return array
     */
    public function getPlayCards(Hand $hand): array
    {
        return array_merge($this->getCards(), $hand->getCards());
    }

    /**
     * @param int $card
     * @return Card
     */
    public function getCard(int $card): Card
    {
        return $this->cards[$card];
    }

    /**
     * @return Card
     */
    public function takeTopCard(): Card
    {
        return $this->deck->takeTop();
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }


    /**
     * @return int
     */
    public function getCall(): int
    {
        return $this->call - $this->isBlind();
    }

    /**
     * @param int $call
     */
    public function setCall(int $call): void
    {
        $this->call = $call;
    }

    /**
     * @param Judge $judge
     */
    public function assertWinner(Judge $judge): void
    {
        /** @var Hand $hand */
        foreach ($this->getHands() as $key => $hand) {
            $hand->setJudgement($judge->judge($this->getPlayCards($hand)));
            if (!$hand->hasFolded()) $judgements[$key] = $hand->getJudgement();
        }

        $this->winner = $judge->assertWinner($judgements ?? []);
        if (count($this->winner['hand']) == 1) $this->potTransfers($this->winner['hand'][0]);
        else $this->splitPotTransfers($this->winner['hand']);

    }
}