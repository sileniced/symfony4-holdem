<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 16/02/2018
 * Time: 22:24
 */

namespace App\Tests\Entity;

use App\Services\Card;
use App\Services\Deck;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class DeckTest
 * @package App\Tests\Entity
 */
class DeckTest extends TestCase
{

    /**
     *
     */
    public function testThatDeckIsCreated()
    {
        $deck = new Deck(false);

        $classIsCard = true;
        foreach ($deck as $card) {
            if (!$card instanceof Card) $classIsCard = false;
        }

        $this->assertTrue($classIsCard);
        $this->assertCount(52, $deck->getCards());
        $this->assertEquals(new Card(3, 12), $deck->getTop());
    }

    /**
     *
     */
    public function testThatDeckIsShuffled()
    {
        $deck = new Deck();

        $this->assertNotEquals(new Card(3, 12), $deck->getTop());
    }

}