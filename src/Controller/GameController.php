<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 19/02/2018
 * Time: 14:27
 */

namespace App\Controller;


use App\Entity\Game;
use App\Entity\Player;
use App\Entity\Table;
use App\Services\Judge;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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
     * @param Game $game
     */
    public function addGame(Game $game)
    {
        $this->game = $game;
    }

    /**
     *
     */
    public function startAction()
    {
        $this->game->resetPoint();

        $this->game->nextPoint();
        $this->game->transferSmallBlind();
        $this->game->nextPoint();
        $this->game->transferBigBlind();

        $this->game->resetPoint();

        $this->game->nextPoint();

        $twice = $this->game->countHands() * 2;
        for ($i = 0; $i < $twice; $i++) {
            $this->game->dealCard();
            $this->game->nextPoint();
        }
        $this->game->removeUnusedCards();

        $this->game->resetPoint();

        $this->game->setStatus(Game::PRE_FLOP);
        $this->game->nextPoint(3);
    }

    /**
     *
     */
    private function nextPhaseAction(): void
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
        $this->game->nextPoint();
    }

    /**
     *
     */
    private function riverAction(): void
    {
        $this->game->setRiverTurn();
        $this->game->setStatus(Game::RIVER);
        $this->game->resetPoint();
        $this->game->nextPoint();
    }

    /**
     *
     */
    private function turnAction(): void
    {
        $this->game->setRiverTurn();
        $this->game->setStatus(Game::TURN);
        $this->game->resetPoint();
        $this->game->nextPoint();
    }

    /**
     *
     */
    private function showAction(): void
    {
        $this->game->setStatus(Game::SHOWDOWN);
        $this->game->assertWinner(new Judge());
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
        $this->game->playerTransfers($this->game->getCall());
        $this->game->updateHandStatus(Game::CALLED);
        if ($this->game->nextPoint()) $this->nextPhaseAction();
    }

    /**
     * @param int $amount
     */
    public function RaiseAction(int $amount): void
    {
        if ($amount <= $this->game->getCall()) return;
        $this->game->setCall($this->game->playerTransfers($amount));
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
        elseif ($this->game->nextPoint()) $this->nextPhaseAction();
    }

    /**
     *
     */
    public function CheckAction(): void
    {
        $this->game->updateHandStatus(Game::CHECKED);
        if ($this->game->nextPoint()) $this->nextPhaseAction();
    }

    /**
     * @param int $amount
     */
    public function BetAction(int $amount): void
    {
        if ($amount <= $this->game->getCall()) return;
        $this->game->setCall($this->game->playerTransfers($amount));
        $this->game->updateHandStatus(Game::BET);
        $this->game->nextPoint();
    }


    /**
     *   TESTER ACTIONS
     */


    /**
     * @return Response
     * @Route("/test", name="testCards")
     */
    public function letsTestAction(): Response
    {
        $table = new Table();
        $names = ["Daan","Vrin","Rolf","John","Fizz","Cass","Anda","Tour","Ding","Dong"];
        foreach ($names as $key => $name) {
            $player = new Player($name, $table->getChipsSize());
            $table->addPlayer($player, $key);
        }

        $this->addGame(new Game($table));
        $this->startAction();
        $this->flopAction();
        $this->riverAction();
        $this->turnAction();

        $this->showAction();
        dump($this->game);

        return $this->render('Game/Game.html.twig', [
            "game" => $this->game
        ]);
    }

    /**
     * @return Response
     * @Route("/test/react", name="testReact")
     */
    public function testReactAction(): Response
    {
        return $this->render('Game/ReactTest.html.twig');
    }
}