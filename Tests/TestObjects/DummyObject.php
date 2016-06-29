<?php
namespace Webdevvie\Nestis\Tests\TestObjects;


class DummyObject
{
    /**
     * @var object
     */
    public $publicObject;

    /**
     * @var string
     */
    private $privateWithGetter='iamprivate';

    /**
     * @var boolean
     */
    private $privateBooleanWithIs=true;

    /**
     * @var string
     */
    private $privateWithoutGetters='you should not get me';

    /**
     * @var string
     */
    public static $staticTest='i am quite static';

    private static $privateStaticTest='i am unreachable!';

    public function __construct()
    {
        $this->publicObject = (object)['test'=>'string'];
    }
    
    public function justAMethod()
    {
        return 'i am just a method';
    }

    /**
     * @return string
     */
    public function getPrivateWithGetter()
    {
        return $this->privateWithGetter;
    }

    /**
     * @return boolean
     */
    public function isPrivateBooleanWithIs()
    {
        return $this->privateBooleanWithIs;
    }
}