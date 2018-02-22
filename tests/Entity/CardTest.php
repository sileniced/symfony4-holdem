<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 17/02/2018
 * Time: 12:21
 */

namespace App\Tests\Entity;


use App\Entity\Card;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class CardTest extends TestCase
{
    public function testThatCardIsParsed()
    {
        $card = new Card(Card::HEARTS,0);
        $this->assertEquals("2 of Hearts", $card->getCardName());

        $card = new Card(Card::SPADES,Card::ACE);
        $this->assertEquals("Ace of Spades", $card->getCardName());
    }
}