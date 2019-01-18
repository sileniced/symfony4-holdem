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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class GameController
 * @package App\Controller
 */
class GameController extends Controller
{

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
        $this->game->potTransfers($this->game->getPoint());
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
     * @Route("/call")
     */
    public function CallAction(): void
    {
        $this->game->playerTransfers($this->game->getCall());
        $this->game->updateHandStatus(Game::CALLED, $this->game->getPoint());
        if ($this->game->nextPoint()) $this->nextPhaseAction();
    }

    /**
     * @Route("/raise/{amount}")
     * @param int $amount
     */
    public function RaiseAction(int $amount): void
    {
        if ($amount <= $this->game->getCall()) return;
        $this->game->setCall($this->game->playerTransfers($amount));
        $this->game->updateHandStatus(Game::RAISED, $this->game->getPoint());
        $this->game->nextPoint();
    }

    /**
     * @Route("/fold")
     */
    public function FoldAction(): void
    {
        $this->game->updateHandStatus(Game::FOLDED, $this->game->getPoint());
        if ($this->game->isFolding()) $this->endAction();
        elseif ($this->game->nextPoint()) $this->nextPhaseAction();
    }

    /**
     * @Route("/check")
     */
    public function CheckAction(): void
    {
        $this->game->updateHandStatus(Game::CHECKED, $this->game->getPoint());
        if ($this->game->nextPoint()) $this->nextPhaseAction();
    }

    /**
     * @Route("/bet/{amount}")
     * @param int $amount
     */
    public function BetAction(int $amount): void
    {
        if ($amount <= $this->game->getCall()) return;
        $this->game->setCall($this->game->playerTransfers($amount));
        $this->game->updateHandStatus(Game::BET, $this->game->getPoint());
        $this->game->nextPoint();
    }


    /**
     *   TESTER ACTIONS
     */


    /**
     * @return Response
     * @Route("/", name="render")
     */
    public function renderAction(): Response
    {
        return $this->render('base.html.twig');
    }

    /**
     * @return JsonResponse
     * @Method("GET")
     * @Route("/data", name="data")
     */
    public function dataAction(): Response
    {
        $game = new Game($this->fakeTable());

        $this->startAction();

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        return new Response($serializer->serialize($game, 'json'));
    }

    private function fakeTable(): Table
    {
        $table = new Table();
        $names = ["Daan","Vrin","Rolf","John","Fizz","Cass","Anda","Tour","Ding","Dong"];
        foreach ($names as $key => $name) {
            $player = new Player($name, $table->getChipsSize());
            $table->addPlayer($player, $key);
        }

        return $table;

//        $this->addGame(new Game($table));
//        $this->startAction();
//        $this->flopAction();
//        $this->riverAction();
//        $this->turnAction();
//
//        $this->showAction();
    }


}