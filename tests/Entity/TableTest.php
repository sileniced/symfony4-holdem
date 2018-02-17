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
        $initiator = [
            "name" => "Paul",
            "seat" => 2
        ];
        $names = [
            [
                "name" => "Nigel",
                "seat" => 1
            ],
            [
                "name" => "Nick",
                "seat" => 0
            ],
            [
                "name" => "Akis",
                "seat" => 3
            ],
            [
                "name" => "Daan",
                "seat" => 5
            ],
            [
                "name" => "Meowsy",
                "seat" => 4
            ]
        ];

        $this->table = new Table(new Player($initiator['name']), $initiator['seat']);

        foreach ($names as $name) {
            $this->table->addPlayer(new Player($name['name']), $name['seat']);
        }
    }

    /**
     *
     */
    public function testThatPlayersCanSitInThisTable()
    {
        $this->assertInstanceOf(Player::class, $this->table->getPlayer(0));
        $this->assertEquals("Nick", $this->table->getPlayer(0)->getName());
        $this->assertEquals("Daan", $this->table->getPlayer(5)->getName());
    }

    public function testThatPlayerGetsRemoved()
    {
        $this->table->removePlayer(2);
        $this->assertNull($this->table->getPlayers()[2]);

        $this->table->removePlayer(5);
        $this->assertNull($this->table->getPlayers()[5]);

        $this->assertCount(6, $this->table->getPlayers());
    }

    public function testThatTableStatusIsFullOrNot()
    {
        $this->assertTrue($this->table->isFull(), "setup is full");

        $this->table->removePlayer(2);
        $this->assertFalse($this->table->isFull(), "removed one");

        $this->table->addPlayer(new Player("Gordon"), 2);
        $this->assertTrue($this->table->isFull(), "added one with fixed seat");
    }

    public function testThatAddedPlayerIsInRandomSeat()
    {
        $this->table->removePlayer(5);
        $this->table->addPlayer(new Player("Ringo"));
        $this->assertTrue($this->table->isFull(), "added one randomly");

        $this->table->removePlayer(2);
        $this->table->removePlayer(4);

        $this->table->addPlayer(new Player("Gordon"));
        $this->table->addPlayer(new Player("Flash"));
        $this->assertTrue($this->table->isFull(), "added two randomly");
    }

}