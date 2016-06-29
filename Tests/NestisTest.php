<?php
namespace Webdevvie\Nestis\Tests;

use Webdevvie\Nestis\Nestis;
use Webdevvie\Nestis\Tests\TestObjects\DummyObject;

require_once(__DIR__ . '/../Nestis.php');

class NestisTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Nestis
     */
    private $nestis;

    public function setUp()
    {
        $this->nestis = new Nestis();
    }

    public function testNestis()
    {
        $object = (object)[
            'level1' => [
                'level2' => new DummyObject(),
                'alwaysNull' => null
            ]
        ];
        $this->assertEquals(
            'string',
            $this->nestis->getNestedItem(
                'level1/level2/publicObject/test',
                $object,
                null
            )
        );
        $this->assertEquals(
            'iamprivate',
            $this->nestis->getNestedItem(
                'level1/level2/privateWithGetter',
                $object,
                null
            )
        );
        $this->assertEquals(
            'i am just a method',
            $this->nestis->getNestedItem(
                'level1/level2/justAMethod',
                $object,
                null
            )
        );
        $this->assertNull(
            $this->nestis->getNestedItem(
                'level1/level2/nonExistant',
                $object,
                null
            )
        );
        $this->assertTrue(
            $this->nestis->getNestedItem(
                'level1/level2/privateBooleanWithIs',
                $object,
                null
            )
        );
        $this->assertNull(
            $this->nestis->getNestedItem(
                'level1/level2/privateWithoutGetters',
                $object,
                null
            )
        );
        $this->assertEquals('not found',
            $this->nestis->getNestedItem(
                'level1/notFound',
                $object,
                'not found'
            )
        );
        $this->assertEquals('default',
            $this->nestis->getNestedItem(
                'level1//typototheleft',
                $object,
                'default'
            )
        );
        $this->assertNull(
            $this->nestis->getNestedItem(
                'level1/alwaysNull',
                $object,
                'not found'
            )
        );
    }

    public function testWithException()
    {
        $object = (object)[
            'level1' => [
                'level2' => new DummyObject(),
                'alwaysNull' => null
            ]
        ];
        $this->nestis->getNestedItem(
            'level1/new',
            $object,
            'not found'
        );
    }
    public function testWithPrivateStatic()
    {
        $object = (object)[
            'level1' => [
                'level2' => new DummyObject(),
                'alwaysNull' => null
            ]
        ];
        $this->assertNull(
            $this->nestis->getNestedItem(
                'level1/level2/::privateStaticTest',
                $object,
                null
            )
        );
    }
    public function testWithStatic()
    {
        $object = (object)[
            'level1' => [
                'level2' => new DummyObject(),
                'alwaysNull' => null
            ]
        ];
        $this->assertEquals(
            'i am quite static',
            $this->nestis->getNestedItem(
                'level1/level2/::staticTest',
                $object,
                null
            )
        );
    }
}