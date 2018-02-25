<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 18/02/2018
 * Time: 20:11
 */

namespace App\Entity;
use App\Services\Kicker;
use App\Services\Judger;


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

    const WON = true;
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

        foreach ($this->table->getPlayers() as $key => &$player) {
            $this->hands[] = new Hand($key, $player);
        }

        $this->folds = $table->countPlayers() - 1;

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
    }

    /**
     * @param int $hand
     * @return Player
     */
    public function getPlayer(int $hand): Player
    {
        return $this->table->getSeat($this->getHand($hand)->getSeat());
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
     */
    public function updateHandStatus(string $status): void
    {
        $this->getHand($this->point)->setStatus($status, $this->status);
    }

    /**
     * @param int $hand
     * @return null|string
     */
    public function getHandStatus(int $hand): ?string
    {
        return $this->getHand($hand)->getStatus();
    }

    /**
     * @param int $hand
     * @return int|null
     */
    public function getHandChips(int $hand): ?int
    {
        return $this->getHand($hand)->getChips();
    }

    /**
     * @return bool
     */
    public function isFolding(): bool
    {
        return !--$this->folds;
    }

    /**
     * @param int $playerHand
     * @return bool
     */
    public function hasFolded(int $playerHand): bool
    {
        return $this->getHandStatus($playerHand) == self::FOLDED;
    }

    /**
     * @return int
     */
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
        $this->nextPoint();
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
        $this->call = $this->playerTransfers($this->table->getBigBlind());
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
     *
     */
    public function potTransfers(): void
    {
        $this->updateHandStatus(self::WON);
        $this->getPlayer($this->point)->winChips($this->pot);
        $this->winner = $this->getHand($this->point);
    }

    /**
     * @param array $hands
     */
    public function splitPotTransfers(array $hands): void
    {
        $split = $this->pot / count($hands);

        /** @var Hand $hand */
        foreach ($hands as $hand) {
            $this->table->getSeat($hand->getSeat())->winChips($split);
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
     * @return Hand|null
     */
    public function assertWinner(): ?Hand
    {
        $judge = new Judger();
        $winner = [
            "hand" => [],
            "score" => 0
        ];
            null;
        /** @var Hand $hand */
        foreach ($this->getHands() as $key => &$hand) {

            $hand->setJudgement($judge->judge($this->getPlayCards($hand)));
            if ($hand->hasFolded()) continue;
            $score = $hand->getJudgement()->getScore();

            if ($score > $winner["score"]) newWinner($winner, $score, $hand);
            elseif ($score == $winner["score"]) $winner["hand"][] = $hand;

        }

        if (count($winner["hand"]) == 1) return $winner["hand"][0];

        $kicker = new Kicker($winner);
        $winner = $kicker->kick();
        if ($winner instanceof Hand) return $winner;

        $this->splitPotTransfers($winner);
        return null;


        function newWinner(array &$winner, int $score, Hand $hand): void
        {
            $winner['score'] = $score;
            $winner["hand"] = [$hand];
        }
    }
}