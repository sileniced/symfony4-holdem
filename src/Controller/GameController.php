<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 19/02/2018
 * Time: 14:27
 */

namespace App\Controller;


use App\Entity\Game;
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

    public function nextAction()
    {
        $this->game->resetChips();
        switch ($this->game->getState()) {
            case Game::PRE_HAND: $this->preHandAction(); return;
            case Game::PRE_FLOP: $this->preFlopAction(); return;
            default: return;
        }
    }

    public function endAction()
    {

    }

    private function preHandAction(): void
    {
        $this->game->takeSmallBigBlind();
        $this->game->dealCards();
        $this->game->nextPoint(3);
        $this->game->setState(Game::PRE_FLOP);
    }

    private function preFlopAction(): void
    {

    }

    /**
     *
     */
    public function CallAction(): void
    {
        $betSize = (int) $this->game->getBetSize();
        $this->game->playerTransfers($betSize);
        $this->game->updateHandStatus(Game::CALLED);
        $this->game->nextPoint();
    }

    /**
     * @param int $amount
     */
    public function RaiseAction(int $amount): void
    {
        if ($amount < $this->game->getBetSize()) return;
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
        $this->game->nextPoint();
    }
}