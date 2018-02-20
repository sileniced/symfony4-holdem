<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 17/02/2018
 * Time: 01:43
 */

namespace App\Tests\Entity;


use App\Entity\Player;
use App\Entity\Table;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class TableTest
 * @package App\Tests\Entity
 */
class TableTest extends TestCase
{

    /**
     * @var Table
     */
    private $table;

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
    }

    /**
     *
     */
    public function testThatPlayersCanSitInThisTable()
    {
        $this->assertInstanceOf(Player::class, $this->table->getSeat(0));
        $this->assertEquals("Daan", $this->table->getSeat(0)->getName());
        $this->assertEquals("Cass", $this->table->getSeat(5)->getName());
    }

    public function testThatPlayerGetsRemoved()
    {
        $this->table->removePlayer(2);
        $this->assertNull($this->table->getSeat(2));
        $this->assertCount(9, $this->table->getPlayers());

        $this->table->removePlayer(5);
        $this->assertNull($this->table->getSeat(5));
        $this->assertCount(8, $this->table->getPlayers());
        $this->assertCount(10, $this->table->getSeats());


    }

    public function testThatTableStatusIsFull()
    {
        $this->assertTrue($this->table->isFull(), "setup is not full");

        $this->table->removePlayer(2);
        $this->assertFalse($this->table->isFull(), "removed one");

        $this->table->addPlayer(new Player("Gordon",  $this->table->getChipsSize()), 2);
        $this->assertTrue($this->table->isFull(), "added one with fixed seat");
    }

    public function testThatAddedPlayerIsInRandomSeat()
    {
        $this->table->removePlayer(5);
        $this->table->addPlayer(new Player("Ringo",  $this->table->getChipsSize()));
        $this->assertTrue($this->table->isFull(), "added one randomly");

        $this->table->removePlayer(2);
        $this->table->removePlayer(4);

        $this->table->addPlayer(new Player("Gordon",  $this->table->getChipsSize()));
        $this->table->addPlayer(new Player("Flash",  $this->table->getChipsSize()));
        $this->assertTrue($this->table->isFull(), "added two randomly");
    }

    public function testThatTableHasEnoughPlayersToStart()
    {
        $this->assertTrue($this->table->hasEnough());
        $this->table->removePlayer(9);
        $this->table->removePlayer(8);
        $this->table->removePlayer(7);
        $this->table->removePlayer(6);
        $this->table->removePlayer(5);
        $this->table->removePlayer(4);
        $this->table->removePlayer(3);
        $this->table->removePlayer(2);
        $this->table->removePlayer(1);
        $this->assertFalse($this->table->hasEnough(), "There are too much players");
        $this->table->addPlayer(new Player("Gordon",  $this->table->getChipsSize()));
        $this->assertTrue($this->table->hasEnough(), "There are not enough players");
    }


}