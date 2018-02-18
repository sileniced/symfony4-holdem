<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 18/02/2018
 * Time: 20:21
 */

namespace App\Tests\Entity;


use App\Entity\Hand;
use App\Entity\Player;
use App\Entity\Table;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class HandTest extends TestCase
{

    /**
     * @var Table
     */
    private $table;

    /**
     * @var Hand
     */
    private $hand;

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
        
        $this->hand = new Hand($this->table);
    }

    public function testThatTableGivesTurnToNextPlayer()
    {
        $this->assertEquals(0, $this->hand->getTurn());
        $this->hand->nextTurn();
        $this->assertEquals(1, $this->hand->getTurn());
    }

    public function testThatTableCanSkipAPlayerForNextTurn()
    {
        $this->assertEquals(0, $this->hand->getTurn());
        $this->table->removePlayer(1);
        $this->hand->nextTurn();
        $this->assertEquals(2, $this->hand->getTurn());
    }

    public function testThatTurnWillLoopOverToFirstSeat()
    {
        $this->assertEquals(0, $this->hand->getTurn());
        $this->hand->nextTurn();
        $this->assertEquals(1, $this->hand->getTurn());
        $this->hand->nextTurn();
        $this->assertEquals(2, $this->hand->getTurn());
        $this->hand->nextTurn();
        $this->assertEquals(3, $this->hand->getTurn());
        $this->hand->nextTurn();
        $this->assertEquals(4, $this->hand->getTurn());
        $this->hand->nextTurn();
        $this->assertEquals(5, $this->hand->getTurn());
        $this->hand->nextTurn();
        $this->assertEquals(6, $this->hand->getTurn());
        $this->hand->nextTurn();
        $this->assertEquals(7, $this->hand->getTurn());
        $this->hand->nextTurn();
        $this->assertEquals(8, $this->hand->getTurn());
        $this->hand->nextTurn();
        $this->assertEquals(9, $this->hand->getTurn());
        $this->hand->nextTurn();
        $this->assertEquals(0, $this->hand->getTurn());
    }

    public function testThatSmallAndBigBlindAreInTheTable()
    {
        $this->hand->takeSmallBigBlind();
        $this->assertEquals(3, $this->table->getChips());
        $this->assertEquals(49, $this->table->getPlayerChips(1));
        $this->assertEquals(48, $this->table->getPlayerChips(2));
        $this->assertEquals(3, $this->hand->getTurn());
    }

    public function testThatPlayerCanCall()
    {
        $this->hand->takeSmallBigBlind();
        $this->hand->playerCalls();
        $this->assertEquals(48, $this->table->getPlayerChips(3));
        $this->assertEquals(4, $this->hand->getTurn());
    }

    public function testThatPlayerCanRaise()
    {

    }

}