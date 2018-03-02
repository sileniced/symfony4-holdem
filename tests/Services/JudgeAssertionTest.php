<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 27/02/2018
 * Time: 14:16
 */

namespace App\Tests\Services;


use App\Services\Judge;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class JudgeAssertionTest extends TestCase
{

    /** @var Judge */
    private $judge;

    protected function setUp()
    {
        $this->judge = new Judge();
    }

    public function testThatRoyalFlushWins()
    {
        $winner = $this->judge->assertWinner([
            7 => [9],
            9 => [2, 5, 4, 3]
        ]);

        $this->assertEquals(7, $winner['hand'][0]);
    }

    public function testThatKickerInFlushWins()
    {
        $winner = $this->judge->assertWinner([
            3 => [5,8,5],
            7 => [5,8,9],
            9 => [5,3]
        ]);

        $this->assertEquals(7, $winner['hand'][0]);
    }


}