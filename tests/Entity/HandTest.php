<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 19/02/2018
 * Time: 15:11
 */

namespace App\Tests\Entity;


use App\Entity\Card;
use App\Entity\Deck;
use App\Entity\Hand;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class HandTest extends TestCase
{
    /**
     * @var Hand
     */
    private $hand;
    
    public function setUp()
    {
        $this->hand = new Hand(0);
    }

    /**
     *
     */
    public function testThatHandCanReceiveTwoCards()
    {
        $cards = [new Card(1, 1), new Card(1, 2)];
        $this->hand->setCards($cards);

        $this->assertContainsOnlyInstancesOf(Card::class, $this->hand->getCards());
    }



    /**
     *
     */
    public function testThatHandGetsTwoRandomCards()
    {
        $deck = new Deck();

        $cards = [$deck->takeTop(), $deck->takeTop()];

        $this->hand->setCards($cards);

        $this->assertNotEquals(new Card(3, 12), $this->hand->getCard(0));
        $this->assertNotEquals($this->hand->getCard(0), $this->hand->getCard(1));
    }

    public function testThatHandGetsDealtTwoCards()
    {
        $deck = new Deck(false);

        $this->hand->addCard($deck->takeTop());
        $this->hand->addCard($deck->takeTop());

        $this->assertEquals(new Card(3, 12), $this->hand->getCard(0));
        $this->assertEquals(new Card(3, 11), $this->hand->getCard(1));
    }
}