<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 19/02/2018
 * Time: 14:27
 */

namespace App\Controller;


use App\Entity\Game;
use App\Entity\Hand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GameController extends Controller
{
    /**
     * @var Game
     */
    public $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function startAction()
    {
        $this->game->takeSmallBigBlind();
        $this->game->dealCards();
        $this->game->setState(Game::PRE_FLOP);
        $this->game->nextPoint(3);
    }

    private function nextAction(): void
    {
        $this->game->resetHandsStatus();
        switch ($this->game->getState()) {
            case Game::PRE_FLOP: $this->flopAction(); return;
            case Game::FLOP: $this->riverAction(); return;
            case Game::RIVER: $this->turnAction(); return;
            case Game::TURN: $this->showAction(); return;
            default: return;
        }
    }

    private function endAction(): void
    {
        $this->game->nextPoint();
        $this->game->potTransfers();
        $this->game->setState(Game::ENDED);
    }

    private function flopAction(): void
    {
        $this->game->setFlop();
        $this->game->setState(Game::FLOP);
        $this->game->nextPoint();
    }

    private function riverAction(): void
    {
        $this->game->setRiverTurn();
        $this->game->setState(Game::RIVER);
        $this->game->nextPoint();
    }

    private function turnAction(): void
    {
        $this->game->setRiverTurn();
        $this->game->setState(Game::TURN);
        $this->game->nextPoint();
    }

    private function showAction(): Hand
    {
        $this->game->setState(Game::SHOWDOWN);
        return $this->game->assertWinner();
    }

    /**
     *
     */
    public function CallAction(): void
    {
        $this->game->playerTransfers($this->game->getBetSize());
        $this->game->updateHandStatus(Game::CALLED);
        if ($this->game->nextPoint()) $this->nextAction();
    }

    /**
     * @param int $amount
     */
    public function RaiseAction(int $amount): void
    {
        if ($amount <= $this->game->getBetSize()) return;
        $this->game->setBetSize($this->game->playerTransfers($amount));
        $this->game->updateHandStatus(Game::RAISED);
        $this->game->nextPoint();
    }

    /**
     *
     */
    public function FoldAction(): void
    {
        $this->game->updateHandStatus(Game::FOLDED);
        if ($this->game->isFolding()) $this->endAction();
        elseif ($this->game->nextPoint()) $this->nextAction();
    }
}