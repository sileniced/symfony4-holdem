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
     * @var string
     */
    private $name = "nigel";

    /**
     * @var int
     */
    private $chips = 50;

    /**
     *
     */
    public function setUp()
    {
        $this->player = new Player($this->name, 50);
    }

    /**
     *
     */
    public function testThatPlayerCanInsertName()
    {
        $this->assertEquals($this->name, $this->player->getName());
    }

    /**
     *
     */
    public function testThatPlayerCanBringChips()
    {
        $this->assertEquals($this->chips, $this->player->getChips());
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

        $this->assertNotEquals(new Card(3, 12), $this->player->getCard(0));
        $this->assertNotEquals($this->player->getCard(0), $this->player->getCard(1));
    }

    public function testThatPlayerGetsDealtTwoCards()
    {
        $deck = new Deck(false);

        $this->player->addCard($deck->takeTop());
        $this->player->addCard($deck->takeTop());

        $this->assertEquals(new Card(3, 12), $this->player->getCard(0));
        $this->assertEquals(new Card(3, 11), $this->player->getCard(1));
    }

    


}