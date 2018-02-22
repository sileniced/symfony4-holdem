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
class Hand
{

    /**
     * @var int
     */
    private $seat;

    /**
     * @var Player
     */
    private $player;

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
    private $chips = 0;

    /**
     * Hand constructor.
     * @param int $seat
     * @param Player $player
     */
    public function __construct(int $seat, Player $player)
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

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @param Player $player
     */
    public function setPlayer(Player $player): void
    {
        $this->player = $player;
    }

    /**
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getChips(): ?int
    {
        return $this->chips;
    }

    /**
     * @param int $chips
     */
    public function addChips(int $chips): void
    {
        $this->chips += $chips;
    }

    public function resetHandStatus(): void
    {
        $this->chips = 0;
        $this->status = null;
    }

    public function getCard(int $card): Card
    {
        return $this->cards[$card];
    }

    public function addCard(Card $card): void
    {
        $this->cards[] = $card;
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


}