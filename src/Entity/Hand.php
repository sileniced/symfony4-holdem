<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 19/02/2018
 * Time: 09:27
 */

namespace App\Entity;


/**
 * Class Hand
 * @package App\Entity
 */
/**
 * Class Hand
 * @package App\Entity
 */
class Hand
{

    /**
     * @var int
     */
    private $seat;

    /**
     * @var array
     */
    private $cards;

    /**
     * @var string
     */
    private $status;

    /**
     * @var int
     */
    private $chips;


    /**
     * @var array of strings of Hand specific @const in Game
     */
    private $history = [
        "game-status" => Game::PRE_HAND,
        "status" => [
            Game::PRE_FLOP => [],
            Game::FLOP => [],
            Game::RIVER => [],
            Game::TURN => [],
            Game::SHOWDOWN => null
        ],
        "chips" => [
            Game::PRE_FLOP => [],
            Game::FLOP => [],
            Game::RIVER => [],
            Game::TURN => [],
        ]
    ];


    /**
     * @var
     */
    private $judgement;

    /**
     * Hand constructor.
     * @param int $seat
     * @param Player $player
     */
    public function __construct(int $seat, Player &$player)
    {
        $this->seat = $seat;
        $this->player = $player;
    }

    /**
     * @return int
     */
    public function getSeat(): int
    {
        return $this->seat;
    }

    /**
     * @param int $seat
     */
    public function setSeat(int $seat): void
    {
        $this->seat = $seat;
    }

//    /**
//     * @return Player
//     */
//    public function getPlayer(): Player
//    {
//        return $this->player;
//    }
//
//    /**
//     * @param Player $player
//     */
//    public function setPlayer(Player $player): void
//    {
//        $this->player = $player;
//    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @param string $game_status
     */
    public function setStatus(string $status, string $game_status): void
    {
        $this->status = $status;

        $this->history['game-status'] = $game_status;
        $this->history['status'][$game_status][] = $status;
    }

    /**
     * @return int
     */
    public function getChips(): int
    {
        return $this->chips['last'];
    }

    /**
     * @param int $chips
     */
    public function setChips(int $chips): void
    {
        $this->chips = $chips;
    }

    /**
     * @param int $chips
     */
    public function addChips(int $chips): void
    {
        $this->chips += $chips;

        $this->history['chips'][$this->history['game-status']][] = $chips;
    }

    /**
     *
     */
    public function resetHandStatus(): void
    {
        $this->chips = 0;
        $this->status = null;
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
     * @param Card $card
     */
    public function addCard(Card $card): void
    {
        $this->cards[] = $card;
    }

    /**
     * @return bool
     */
    public function hasFolded(): bool
    {
        return $this->status == Game::FOLDED;
    }

    /**
     * @return array
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * @param array $cards
     */
    public function setCards(array $cards): void
    {
        $this->cards = $cards;
    }

    /**
     * @return mixed
     */
    public function getJudgement(): Judgement
    {
        return $this->judgement;
    }

    /**
     * @param mixed $judgement
     */
    public function setJudgement(Judgement $judgement): void
    {
        $this->judgement = $judgement;
    }


}