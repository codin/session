<?php

namespace spec\Codin\Session;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Codin\Session\Cookies;
use Codin\Session\Storage\MemoryStorage;

class SessionSpec extends ObjectBehavior
{
    public function it_should_return_a_id(Cookies $cookies, MemoryStorage $storage)
    {
        $this->beConstructedWith($cookies, $storage);
        $cookies->has('PHPSESSID')->willReturn(true);
        $cookies->get('PHPSESSID')->willReturn('1234');
        $storage->exists('1234')->willReturn(true);
        $storage->read('1234')->shouldBeCalled();
        $this->start();
        $this->id()->shouldEqual('1234');
    }

    public function it_should_return_a_name(Cookies $cookies, MemoryStorage $storage)
    {
        $this->beConstructedWith($cookies, $storage, ['name' => 'foo']);
        $this->name()->shouldEqual('foo');
    }

    public function it_should_migrate_id(Cookies $cookies, MemoryStorage $storage)
    {
        $this->beConstructedWith($cookies, $storage);
        $cookies->has('PHPSESSID')->willReturn(true);
        $cookies->get('PHPSESSID')->willReturn('1234');
        $storage->exists('1234')->willReturn(true);
        $storage->read('1234')->shouldBeCalled();
        $this->start();
        $this->id()->shouldEqual('1234');
        $this->migrate();
        $this->id()->shouldNotEqual('1234');
    }

    public function it_should_destroy_session(Cookies $cookies, MemoryStorage $storage)
    {
        $this->beConstructedWith($cookies, $storage);
        $cookies->has('PHPSESSID')->willReturn(true);
        $cookies->get('PHPSESSID')->willReturn('1234');
        $storage->exists('1234')->willReturn(true);
        $storage->read('1234')->shouldBeCalled();
        $this->start();
        $this->id()->shouldEqual('1234');
        $storage->destroy('1234')->shouldBeCalled();
        $this->destroy();
        $this->id()->shouldNotEqual('1234');
    }

    public function it_should_check_if_the_session_has_started(Cookies $cookies, MemoryStorage $storage)
    {
        $this->beConstructedWith($cookies, $storage);
        $this->started()->shouldEqual(false);
        $cookies->has('PHPSESSID')->willReturn(true);
        $cookies->get('PHPSESSID')->willReturn('1234');
        $storage->exists('1234')->willReturn(true);
        $storage->read('1234')->shouldBeCalled();
        $this->start();
        $this->started()->shouldEqual(true);
    }

    public function it_should_return_all_keys(Cookies $cookies, MemoryStorage $storage)
    {
        $this->beConstructedWith($cookies, $storage);
        $this->started()->shouldEqual(false);
        $cookies->has('PHPSESSID')->willReturn(true);
        $cookies->get('PHPSESSID')->willReturn('1234');
        $storage->exists('1234')->willReturn(true);
        $storage->read('1234')->shouldBeCalled();
        $storage->read('1234')->willReturn(['foo' => 'bar', 'baz' => 'qux']);
        $this->start();
        $this->all()->shouldEqual(['foo' => 'bar', 'baz' => 'qux']);
    }
}
