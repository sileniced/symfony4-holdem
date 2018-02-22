<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 18/02/2018
 * Time: 20:21
 */

namespace App\Tests\Entity;


use App\Entity\Game;
use App\Entity\Card;
use App\Entity\Deck;
use App\Entity\Player;
use App\Entity\Table;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class GameTest extends TestCase
{

    /**
     * @var Table
     */
    private $table;

    /**
     * @var Game
     */
    private $game;

    /**
     *
     */
    public function setUp()
    {
        $this->table = new Table();

        $names = ["Daan","Vrin","Rolf","John","Fizz","Cass","Anda","Tour","Ding","Dong"];
        foreach ($names as $key => $name) {
            $player = new Player($name, $this->table->getChipsSize());
            $this->table->addPlayer($player, $key);
        }
        $this->game = new Game($this->table);
        $this->game->setDeck(new Deck(false));
        $this->game->DealCards();
    }

    public function testThatDeckEndsUpWithFiveCards()
    {
        $this->assertCount(5, $this->game->getDeck()->getCards());
    }

    public function testThatTableDealsFixedCardsToFullTableOfPlayers()
    {

        $this->assertEquals(new Card(3, 3), $this->game->getHandCard(0,0));
        $this->assertEquals(new Card(2, 6), $this->game->getHandCard(0,1));

        $this->assertEquals(new Card(3, 12), $this->game->getHandCard(1,0));
        $this->assertEquals(new Card(3, 2), $this->game->getHandCard(1,1));
    }

    public function testThatTableDealsFixedCardsToTableWithEmptySeats()
    {
        $this->table->removePlayer(9);
        $this->table->removePlayer(8);
        $this->table->removePlayer(7);
        
        $this->game = new Game($this->table);
        $this->game->setDeck(new Deck(false));
        $this->game->DealCards();

        $this->assertEquals(new Card(3, 6), $this->game->getHandCard(0,0));
        $this->assertEquals(new Card(2, 12), $this->game->getHandCard(0,1));

        $this->assertEquals(new Card(3, 12), $this->game->getHandCard(1,0));
        $this->assertEquals(new Card(3, 5), $this->game->getHandCard(1,1));
    }

    public function testThatTheTableDealsTheFlopRiverAndTurn()
    {

        $this->game->setFlop();
        $this->assertEquals(new Card(2, 1), $this->game->getCard(0), "This is the first card");
        $this->assertEquals(new Card(2, 2), $this->game->getCard(1), "This is the second card");
        $this->assertEquals(new Card(2, 3), $this->game->getCard(2), "This is the third card");

        $this->game->setRiverTurn();
        $this->assertEquals(new Card(2, 4), $this->game->getCard(3), "This is the fourth card");

        $this->game->setRiverTurn();
        $this->assertEquals(new Card(2, 5), $this->game->getCard(4), "This is the fifth card");
    }

    public function testThatTableGivesTurnToNextPlayer()
    {
        $this->assertEquals(0, $this->game->getPoint());
        $this->game->nextPoint();
        $this->assertEquals(1, $this->game->getPoint());
    }

    public function testThatTableCanSkipASeatForNextTurn()
    {
        $this->assertEquals(0, $this->game->getPoint());
        $this->table->removePlayer(1);
        $this->game = new Game($this->table);
        $this->game->nextPoint();
        $this->assertEquals(1, $this->game->getPoint());
        $this->assertEquals(2, $this->game->getHand($this->game->getPoint())->getSeat());
    }

    public function testThatTurnWillLoopOverToFirstSeat()
    {
        $this->assertEquals(0, $this->game->getPoint());
        $this->game->nextPoint();
        $this->assertEquals(1, $this->game->getPoint());
        $this->game->nextPoint();
        $this->assertEquals(2, $this->game->getPoint());
        $this->game->nextPoint();
        $this->assertEquals(3, $this->game->getPoint());
        $this->game->nextPoint();
        $this->assertEquals(4, $this->game->getPoint());
        $this->game->nextPoint();
        $this->assertEquals(5, $this->game->getPoint());
        $this->game->nextPoint();
        $this->assertEquals(6, $this->game->getPoint());
        $this->game->nextPoint();
        $this->assertEquals(7, $this->game->getPoint());
        $this->game->nextPoint();
        $this->assertEquals(8, $this->game->getPoint());
        $this->game->nextPoint();
        $this->assertEquals(9, $this->game->getPoint());
        $this->game->nextPoint();
        $this->assertEquals(0, $this->game->getPoint());
    }

    public function testThatSmallAndBigBlindAreInTheTable()
    {
        $this->game->takeSmallBigBlind();
        $this->assertEquals(3, $this->game->getPot(), "dealer doesn't have the right amount of chips");
        $this->assertEquals(49, $this->game->getPlayerChips(1), "player 1 didn't pay Small Blind");
        $this->assertEquals(48, $this->game->getPlayerChips(2), "player 2 didn't pay Big Blind");
        $this->assertEquals(0, $this->game->getPoint(), "Point didn't return to button");
    }


}