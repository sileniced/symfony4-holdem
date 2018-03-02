<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 19/02/2018
 * Time: 14:36
 */

namespace App\Tests\Controller;


use App\Controller\GameController;
use App\Entity\Game;
use App\Entity\Player;
use App\Entity\Table;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class GameControllerTest extends TestCase
{
    /**
     * @var GameController
     */
    private $gameController;

    protected function setUp()
    {
        $table = new Table();
        $table->addPlayer(new Player("Nigel", 50), 0);
        $table->addPlayer(new Player("Daan", 50), 1);
        $table->addPlayer(new Player("Fred", 50), 2);
        $table->addPlayer(new Player("Dong", 50), 3);
        $table->addPlayer(new Player("Drek", 50), 4);
//        $table->addPlayer(new Player("Fred", 50), 5);
//        $table->addPlayer(new Player("Fred", 50), 6);
//        $table->addPlayer(new Player("Nigel", 50), 7);
//        $table->addPlayer(new Player("Daan", 50), 8);
//        $table->addPlayer(new Player("Fred", 50), 9);

        $game = new Game($table, false);

        $this->gameController = new GameController();
        $this->gameController->addGame($game);

        $this->gameController->startAction();
    }

    public function thatThatGameStateIsPreFlop()
    {
        $this->assertEquals(Game::PRE_FLOP, $this->gameController->game->getStatus());
    }

    public function testThatPlayerCanCall()
    {
        $this->assertEquals(3, $this->gameController->game->getPoint());

        $this->gameController->CallAction();
        $this->assertEquals(48, $this->gameController->game->getPlayerChips(3));
        $this->assertEquals(4, $this->gameController->game->getPoint());
    }

    public function testThatPlayerCanRaise()
    {
        $this->gameController->RaiseAction(5);
        $this->assertEquals(45, $this->gameController->game->getPlayerChips(3));
        $this->assertEquals(4, $this->gameController->game->getPoint());
        $this->gameController->CallAction();
        $this->assertEquals(45, $this->gameController->game->getPlayerChips(4));
        $this->assertEquals(0, $this->gameController->game->getPoint());
    }

    public function testThatPlayerCanFold()
    {
        $this->gameController->FoldAction();
        $this->assertEquals(Game::FOLDED, $this->gameController->game->getHandStatus(3));
    }

    public function testThatSmallBigBlindPlayerCallsCorrectly()
    {

        $this->assertEquals(3, $this->gameController->game->getPoint(), "beginning point");
        $this->assertEquals(50, $this->gameController->game->getPlayerChips(0), "before calling; the third / button caller");
        $this->assertEquals(Game::SMALL_BLIND, $this->gameController->game->getHandStatus(1));
        $this->assertEquals(Game::BIG_BLIND, $this->gameController->game->getHandStatus(2));
        $this->assertEquals(3, $this->gameController->game->getPot());

        $this->assertEquals(49, $this->gameController->game->getPlayerChips(1), "one chip should be taken from small blind");

        $this->gameController->CallAction();
        $this->assertEquals(5, $this->gameController->game->getPot());
        $this->assertEquals(48, $this->gameController->game->getPlayerChips(3), "the first caller");

        $this->assertEquals(4, $this->gameController->game->getPoint());
        $this->gameController->CallAction();
        $this->assertEquals(7, $this->gameController->game->getPot());
        $this->assertEquals(48, $this->gameController->game->getPlayerChips(4), "the second caller");


        $this->assertEquals(0, $this->gameController->game->getPoint());
        $this->assertNotEquals(Game::SMALL_BLIND, $this->gameController->game->getHandStatus(0));
        $this->assertEquals(50, $this->gameController->game->getPlayerChips(0), "before calling second check; the third / button caller");
        $this->assertEquals(2, $this->gameController->game->getCall());
        $this->gameController->CallAction();
        $this->assertEquals(9, $this->gameController->game->getPot());
        $this->assertEquals($this->gameController->game->getPlayer(0)->getChips(), $this->gameController->game->getPlayerChips(0));
        $this->assertEquals(48, $this->gameController->game->getPlayerChips(0), "the third / button caller");

        $this->assertEquals(1, $this->gameController->game->getPoint());
        $this->gameController->CallAction();
        $this->assertEquals(48, $this->gameController->game->getPlayerChips(1), "Mr. small blinds called");

        $this->assertEquals(2, $this->gameController->game->getPoint());
        $this->gameController->CallAction();
        $this->assertEquals(48, $this->gameController->game->getPlayerChips(2), "Mr. BIG blinds called");
    }

    public function testThatSmallBigBlindStillCallsCorrectlyAfterRaises()
    {
        $this->assertEquals(49, $this->gameController->game->getPlayerChips(1));
        $this->gameController->RaiseAction(5);
        $this->assertEquals(45, $this->gameController->game->getPlayerChips(3));
        $this->assertEquals(4, $this->gameController->game->getPoint());
        $this->gameController->CallAction();
        $this->assertEquals(45, $this->gameController->game->getPlayerChips(4));
        $this->assertEquals(0, $this->gameController->game->getPoint());
        $this->gameController->RaiseAction(10);
        $this->assertEquals(40, $this->gameController->game->getPlayerChips(0));
        $this->assertEquals(1, $this->gameController->game->getPoint());
        $this->gameController->CallAction();
        $this->assertEquals(40, $this->gameController->game->getPlayerChips(1));
        $this->assertEquals(2, $this->gameController->game->getPoint());
        $this->gameController->CallAction();
        $this->assertEquals(40, $this->gameController->game->getPlayerChips(2));
    }

    public function testThatGameCanGoToTheNextPhase()
    {
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->assertEquals(1, $this->gameController->game->getPoint());
        $this->assertEquals(Game::FLOP, $this->gameController->game->getStatus());
    }

    public function testThatGameCanGoToTheNextPhaseAfterTheFirstPlayerRaises()
    {
        $this->gameController->RaiseAction(5);
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->assertEquals(1, $this->gameController->game->getPoint());
        $this->assertEquals(Game::FLOP, $this->gameController->game->getStatus());
    }

    public function testThatGameCanGoToTheNextPhaseAfterTheSecondPlayerRaises()
    {
        $this->gameController->CallAction();
        $this->gameController->RaiseAction(5);
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->assertEquals(1, $this->gameController->game->getPoint());
        $this->assertEquals(Game::FLOP, $this->gameController->game->getStatus());
    }

    public function testThatEverybodyFoldingLeadsToEndGameAndGivePotToWinner()
    {
        $this->gameController->FoldAction();
        $this->gameController->FoldAction();
        $this->gameController->FoldAction();
        $this->assertNotEquals(Game::ENDED, $this->gameController->game->getStatus());
        $this->gameController->FoldAction();
        $this->assertEquals(Game::ENDED, $this->gameController->game->getStatus());
        $this->assertEquals(51, $this->gameController->game->getPlayerChips(2));
        $this->assertEquals(Game::WON, $this->gameController->game->getHandStatus(2));
    }

    public function testThatTheGameReachesAShowdown()
    {
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->assertEquals(1, $this->gameController->game->getPoint());
        $this->assertEquals(Game::FLOP, $this->gameController->game->getStatus());
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->assertEquals(1, $this->gameController->game->getPoint());
        $this->assertEquals(Game::RIVER, $this->gameController->game->getStatus());
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->assertEquals(1, $this->gameController->game->getPoint());
        $this->assertEquals(Game::TURN, $this->gameController->game->getStatus());
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->gameController->CallAction();
        $this->assertEquals(Game::SHOWDOWN, $this->gameController->game->getStatus());

    }


}