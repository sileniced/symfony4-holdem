<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 22/02/2018
 * Time: 23:52
 */

namespace App\Tests\Services;


use App\Entity\Card;
use App\Services\Judge;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class JudgeTest extends TestCase
{
    public function testThatOnePairIsAsserted()
    {
        $judge = new Judge([
            new Card(0,1),
            new Card(1,1),
            new Card(2,3),
            new Card(3,5),
            new Card(2,7),
            new Card(1,9),
            new Card(0,11)
        ]);

        $this->assertEquals(8, $judge->assertScore());
    }

    public function testThatTwoPairIsAsserted()
    {
        $judge = new Judge([
            new Card(0,1),
            new Card(1,1),
            new Card(2,3),
            new Card(3,3),
            new Card(2,7),
            new Card(1,9),
            new Card(0,11)
        ]);

        $this->assertEquals(7, $judge->assertScore());
    }

    public function testThatThreeOfAKindIsAsserted()
    {
        $judge = new Judge([
            new Card(0,1),
            new Card(1,1),
            new Card(2,1),
            new Card(3,3),
            new Card(2,7),
            new Card(1,9),
            new Card(0,11)
        ]);

        $this->assertEquals(6, $judge->assertScore());
    }

    public function testThatStraightIsAsserted()
    {
        $judge = new Judge([
            new Card(0,1),
            new Card(1,2),
            new Card(2,3),
            new Card(3,4),
            new Card(2,5),
            new Card(1,9),
            new Card(0,11)
        ]);

        $this->assertEquals(5, $judge->assertScore());

        $judge = new Judge([
            new Card(0,1),
            new Card(1,2),
            new Card(2,3),
            new Card(3,0),
            new Card(2,12),
            new Card(1,9),
            new Card(0,11)
        ]);

        $this->assertEquals(5, $judge->assertScore());

        $judge = new Judge([
            new Card(0,1),
            new Card(1,2),
            new Card(2,3),
            new Card(3,0),
            new Card(2,10),
            new Card(1,9),
            new Card(0,11)
        ]);

        $this->assertNotEquals(5, $judge->assertScore());
    }

    public function testThatFlushIsAsserted()
    {
        $judge = new Judge([
            new Card(0,1),
            new Card(0,8),
            new Card(0,3),
            new Card(0,4),
            new Card(0,5),
            new Card(1,9),
            new Card(0,11)
        ]);

        $this->assertEquals(4, $judge->assertScore());
    }

    public function testThatFullHouseIsAsserted()
    {
        $judge = new Judge([
            new Card(0,1),
            new Card(1,1),
            new Card(2,1),
            new Card(3,2),
            new Card(2,2),
            new Card(1,9),
            new Card(0,11)
        ]);

        $this->assertEquals(3, $judge->assertScore());
    }

    public function testThatFourOfAKindIsAsserted()
    {
        $judge = new Judge([
            new Card(0,1),
            new Card(1,1),
            new Card(2,1),
            new Card(3,1),
            new Card(2,5),
            new Card(1,9),
            new Card(0,11)
        ]);

        $this->assertEquals(2, $judge->assertScore());
    }

    public function testThatStraightFlushIsAsserted()
    {
        $judge = new Judge([
            new Card(1,1),
            new Card(1,2),
            new Card(1,3),
            new Card(1,4),
            new Card(1,5),
            new Card(2,9),
            new Card(0,11)
        ]);

        $this->assertEquals(1, $judge->assertScore());
    }

    public function testThatRoyalFlushIsAsserted()
    {
        $judge = new Judge([
            new Card(Card::HEARTS,12),
            new Card(Card::HEARTS,11),
            new Card(Card::HEARTS,10),
            new Card(Card::HEARTS,9),
            new Card(Card::HEARTS,8),
            new Card(2,9),
            new Card(0,11)
        ]);

        $this->assertEquals(0, $judge->assertScore());
    }

}