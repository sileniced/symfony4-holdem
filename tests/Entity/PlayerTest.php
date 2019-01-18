<?php
/**
 * Created by PhpStorm.
 * User: Vince
 * Date: 16/02/2018
 * Time: 21:26
 */

namespace App\Tests\Entity;


use App\Services\Player;
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

    


}