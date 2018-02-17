<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 16/02/2018
 * Time: 21:26
 */

namespace App\Tests\Entity;


use App\Entity\Card;
use App\Entity\Deck;
use App\Entity\Player;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class PlayerTest
 * @package App\Tests\Entity
 */
class PlayerTest extends TestCase
{

    /**
     * @var Player
     */
    private $player;

    /**
     *
     */
    public function setUp()
    {
        $name = "Nigel";
        $this->player = new Player($name);
    }

    /**
     *
     */
    public function testThatPlayerCanInsertName()
    {
        $this->assertEquals("Nigel", $this->player->getName());
    }

    /**
     *
     */
    public function testThatPlayerCanReceiveTwoCards()
    {
        $cards = [new Card(1, 1), new Card(1, 2)];
        $this->player->setCards($cards);

        $this->assertContainsOnlyInstancesOf(Card::class, $this->player->getCards());
    }

    /**
     *
     */
    public function testThatPlayerGetsTwoRandomCards()
    {
        $deck = new Deck();

        $cards = [$deck->takeTop(), $deck->takeTop()];

        $this->player->setCards($cards);

        $this->assertNotEquals($this->player->getCard0(), $this->player->getCard1());
    }


}