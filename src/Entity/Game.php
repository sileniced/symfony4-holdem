<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 18/02/2018
 * Time: 20:11
 */

namespace App\Entity;


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

    const POINT = "point";
    const BUTTON = "button";

    const PRE_HAND = "pre-hand";
    const PRE_FLOP = "pre-flop";
    const FLOP = "flop";
    const RIVER = "river";
    const TURN = "turn";
    const SHOWDOWN = "showdown";

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
     * @var array of Hands
     */
    private $hands = [];

    /**
     * @var string
     */
    private $state = Game::PRE_HAND;

    /**
     * @var integer
     */
    private $chips = 0;

    /**
     * @var array
     */
    private $cards;

    /**
     * @var Deck
     */
    private $deck;

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
        $this->point = $this->table->getButton();
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
     * @return int
     */
    public function countHands(): int
    {
        return count($this->hands);
    }

    /**
     * @param string $action
     */
    public function updateHandStatus(string $action): void
    {
        $this->getHand($this->point)->setStatus($action);
    }

    /**
     * @param int $playerHand
     * @return null|string
     */
    public function getHandStatus(int $playerHand): ?string
    {
        return $this->getHand($playerHand)->getStatus();
    }

    /**
     * @param int $playerHand
     * @return bool
     */
    public function hasFolded(int $playerHand): bool
    {
        return $this->getHandStatus($playerHand) == Game::FOLDED;
    }

    /**
     * @return int
     */
    public function getPoint(): int
    {
        return $this->point;
    }

    /**
     * @param int $amount
     */
    public function nextPoint(int $amount = 1): void
    {
        while ($amount > 0) {
            if (++$this->point == $this->countHands()) $this->point = 0;
            if (!$this->hasFolded($this->point)) $amount--;
        }
    }

    /**
     *
     * @param int $amount
     * @return int
     */
    public function playerTransfers(int $amount): int
    {
        return $this->addChips($this->getPlayer($this->point)->betChips($amount));
    }

    /**
     *
     */
    public function resetChips(): void
    {
        /** @var Hand $hand */
        foreach ($this->hands as $hand) $hand->resetChips();
    }

    /**
     *
     */
    private function transferSmallBlind(): void
    {
        $this->playerTransfers($this->table->getSmallBlind());
        $this->updateHandStatus(Game::SMALL_BLIND);
    }

    /**
     *
     */
    private function transferBigBlind(): void
    {
        $this->betSize = $this->playerTransfers($this->table->getBigBlind());
        $this->updateHandStatus(Game::BIG_BLIND);
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
    public function setFlop()
    {
        $this->cards = (array) $this->deck->takeCards(Deck::FLOP);
    }

    /**
     *
     */
    public function setRiverTurn()
    {
        $this->cards[] = $this->deck->takeTop();
    }

    /**
     * @return int
     */
    public function getChips(): int
    {
        return $this->chips;
    }

    /**
     * @param int $amount
     * @return int
     */
    public function addChips(int $amount): int
    {
        $this->chips += $amount;
        $this->getHand($this->point)->addChips($amount);
        return $amount;
    }

    /**
     * @return array
     */
    public function getCards(): array
    {
        return $this->cards;
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
        return $this->betSize;
    }

    /**
     * @param int $betSize
     */
    public function setBetSize(int $betSize): void
    {
        $this->betSize = $betSize;
    }
}