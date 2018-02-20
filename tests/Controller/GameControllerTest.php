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

        $game = new Game($table);

        $this->gameController = new GameController($game);

        $this->gameController->nextAction();
    }

    public function testThatPlayerCanCall()
    {
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
}