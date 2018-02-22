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

    const SMALL_BLIND = "small blind";
    const BIG_BLIND = "big blind";
    const CALLED = "called";
    const RAISED = "raised";
    const FOLDED = "folded";
    const CHECKED = "checked";
    const BET = "bet";
    const WON = "won";

    const POINT = "point";
    const BUTTON = "button";

    const PRE_HAND = "pre-hand";
    const PRE_FLOP = "pre-flop";
    const FLOP = "flop";
    const RIVER = "river";
    const TURN = "turn";
    const SHOWDOWN = "showdown";
    const ENDED = "ended";

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
    private $betSize;

    /**
     * @var int
     */
    private $folded;

    /**
     * @var array of Hands
     */
    private $hands = [];

    /**
     * @var string
     */
    private $state = self::PRE_HAND;

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
     * @var Hand
     */
    private $winner;

    /**
     * Game constructor.
     * @param Table $table
     * @param bool $shuffle
     */
    public function __construct(Table $table, bool $shuffle = true)
    {
        $this->table = $table;

        foreach ($this->table->getPlayers() as $key => $player) {
            $this->hands[] = new Hand($key, $player);
        }

        $this->folded = $table->countPlayers() - 1;

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
    private function dealCard(): void
    {
        $this->getHand($this->point)->addCard($this->takeTopCard());
    }

    /**
     *
     */
    private function removeUnusedCards()
    {
        $this->deck->setCards($this->deck->takeCards(Deck::FIVE));
    }

    /**
     *
     */
    public function dealCards(): void
    {
        $this->nextPoint();
        $twice = $this->countHands() * 2;
        for ($i = 0; $i < $twice; $i++) {
            $this->dealCard();
            $this->nextPoint();
        }
        $this->removeUnusedCards();
        $this->resetPoint();
    }

    /**
     * @param int $hand
     * @return Player
     */
    public function getPlayer(int $hand): Player
    {
        return $this->getHand($hand)->getPlayer();
    }

    /**
     * @param int $hand
     * @return int
     */
    public function getPlayerChips(int $hand): int
    {
        return $this->getPlayer($hand)->getChips();
    }

    /**
     * @param int $hand
     * @param int $card
     * @return Card
     */
    public function getHandCard(int $hand, int $card): Card
    {
        return $this->getHand($hand)->getCard($card);
    }

    /**
     * @param int $hand
     * @return Hand
     */
    public function getHand(int $hand): Hand
    {
        return $this->hands[$hand];
    }

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
     */
    public function updateHandStatus(string $status): void
    {
        $this->getHand($this->point)->setStatus($status);
    }

    /**
     * @param int $hand
     * @return null|string
     */
    public function getHandStatus(int $hand): ?string
    {
        return $this->getHand($hand)->getStatus();
    }

    public function getHandChips(int $hand): ?int
    {
        return $this->getHand($hand)->getChips();
    }

    public function isFolding(): bool
    {
        return !--$this->folded;
    }

    /**
     * @param int $playerHand
     * @return bool
     */
    public function hasFolded(int $playerHand): bool
    {
        return $this->getHandStatus($playerHand) == self::FOLDED;
    }

    public function isBlind(): int
    {
        switch ($this->getHandStatus($this->point)) {
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

    public function hasPhaseEnded(): bool
    {
        return $this->getHandChips($this->point) == $this->betSize &&
            $this->getHandStatus($this->point) != self::BIG_BLIND;
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
        $this->betSize = 0;
    }

    /**
     *
     */
    private function transferSmallBlind(): void
    {
        $this->playerTransfers($this->table->getSmallBlind());
        $this->updateHandStatus(self::SMALL_BLIND);
    }

    /**
     *
     */
    private function transferBigBlind(): void
    {
        $this->betSize = $this->playerTransfers($this->table->getBigBlind());
        $this->updateHandStatus(self::BIG_BLIND);
    }

    /**
     *
     */
    public function takeSmallBigBlind(): void
    {
        $this->nextPoint();
        $this->transferSmallBlind();
        $this->nextPoint();
        $this->transferBigBlind();
        $this->point = $this->table->getButton();
    }

    /**
     *
     */
    public function setFlop(): void
    {
        $this->cards = (array) $this->deck->takeCards(Deck::FLOP);
        $this->resetPoint();
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

    public function potTransfers()
    {
        $this->updateHandStatus(self::WON);
        $this->getPlayer($this->point)->winChips($this->pot);
        $this->winner = $this->getHand($this->point);
    }

    /**
     * @return array
     */
    public function getCards(): array
    {
        return $this->cards;
    }

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
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return int
     */
    public function getBetSize(): int
    {
        return $this->betSize - $this->isBlind();
    }

    /**
     * @param int $betSize
     */
    public function setBetSize(int $betSize): void
    {
        $this->betSize = $betSize;
    }

    public function assertWinner(): Hand
    {
        /** @var Hand $hand */
        foreach ($this->getHands() as $hand) {
            $hands[] = new Judge($this->getPlayCards($hand));
        }


    }
}