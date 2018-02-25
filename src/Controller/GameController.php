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

/**
 * Class GameController
 * @package App\Controller
 */
class GameController extends Controller
{
    /**
     * @var Game
     */
    public $game;

    /**
     * GameController constructor.
     * @param Game $game
     */
    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    /**
     *
     */
    public function startAction()
    {
        $this->game->takeSmallBigBlind();
        $this->game->dealCards();
        $this->game->setStatus(Game::PRE_FLOP);
        $this->game->nextPoint(3);
    }

    /**
     *
     */
    private function nextAction(): void
    {
        $this->game->resetHandsStatus();
        switch ($this->game->getStatus()) {
            case Game::PRE_FLOP: $this->flopAction(); return;
            case Game::FLOP: $this->riverAction(); return;
            case Game::RIVER: $this->turnAction(); return;
            case Game::TURN: $this->showAction(); return;
            default: return;
        }
    }

    /**
     *
     */
    private function endAction(): void
    {
        $this->game->nextPoint();
        $this->game->potTransfers();
        $this->game->setStatus(Game::ENDED);
    }

    /**
     *
     */
    private function flopAction(): void
    {
        $this->game->setFlop();
        $this->game->setStatus(Game::FLOP);
        $this->game->resetPoint();
    }

    /**
     *
     */
    private function riverAction(): void
    {
        $this->game->setRiverTurn();
        $this->game->setStatus(Game::RIVER);
        $this->game->resetPoint();
    }

    /**
     *
     */
    private function turnAction(): void
    {
        $this->game->setRiverTurn();
        $this->game->setStatus(Game::TURN);
        $this->game->resetPoint();
    }

    /**
     * @return Hand
     */
    private function showAction(): Hand
    {
        $this->game->setStatus(Game::SHOWDOWN);
        return $this->game->assertWinner();
    }




    /***************************************\
     *                                     *
     *           PLAYER CONTROLS           *
     *                                     *
    \***************************************/


    /**
     *
     */
    public function CallAction(): void
    {
        $this->game->updateHandStatus(Game::CALLED);
        $this->game->playerTransfers($this->game->getCall());
        if ($this->game->nextPoint()) $this->nextAction();
    }

    /**
     * @param int $amount
     */
    public function RaiseAction(int $amount): void
    {
        if ($amount <= $this->game->getCall()) return;
        $this->game->updateHandStatus(Game::RAISED);
        $this->game->setCall($this->game->playerTransfers($amount));
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

    /**
     *
     */
    public function CheckAction(): void
    {
        $this->game->updateHandStatus(Game::CHECKED);
        if ($this->game->nextPoint()) $this->nextAction();
    }

    /**
     * @param int $amount
     */
    public function BetAction(int $amount): void
    {
        if ($amount <= $this->game->getCall()) return;
        $this->game->updateHandStatus(Game::BET);
        $this->game->setCall($this->game->playerTransfers($amount));
        $this->game->nextPoint();
    }
}