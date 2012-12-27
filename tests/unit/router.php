<?php

require_once __DIR__ . '/../init.php';

use CMSx\Router;
use CMSx\URL;
use CMSx\Controller\Exception;

class RouterTest extends PHPUnit_Framework_TestCase
{
  protected $path;

  public function testRoute()
  {
    $r = new Router(null, $this->path);
    $this->assertEquals('index', $r->process(true), 'Запуск defaultController->indexAction()');

    $r = new Router(new URL('test'), $this->path);
    $this->assertEquals('test', $r->process(true), 'Запуск defaultController->testAction()');

    $r = new Router(new URL('new'), $this->path);
    $this->assertEquals('new index', $r->process(true), 'Запуск newController->indexAction()');

    $r = new Router(new URL('new/test'), $this->path);
    $this->assertEquals('new test', $r->process(true), 'Запуск newController->testAction()');

    $r = new Router(new URL('new/hello'), $this->path);
    $this->assertEquals('you called new hello!', $r->process(true), 'Запуск newController->__call()');
  }

  public function testExceptions()
  {
    $exp = Exception::GetHTTPInfo(Exception::NOT_FOUND, true);

    $r = new Router(new URL('hello'), $this->path);
    $p = $r->process(true);
    $h = $p->render();
    $this->assertEquals($exp, $p->getTitle(), 'Ошибка: страница не найдена');
    $this->assertSelectCount('html body h1', true, $h, 'Заголовок страницы с ошибкой');
    $this->assertSelectCount('html body h3', false, $h, 'Debug=off Описания нет');
    $this->assertSelectCount('html body p', false, $h, 'Debug=off Стека вызовов нет');

    $r = new Router; //Путь к контроллерам не прописан
    $r->enableDebug(); //В режиме отладки выводится доп.информация при ошибках
    $p = $r->process(true);
    $h = $p->render();
    $this->assertEquals($exp, $p->getTitle(), 'Отсутствие контроллера по-умолчанию');
    $this->assertSelectCount('html body h1', true, $h, 'Заголовок страницы с ошибкой');
    $this->assertSelectCount('html body h3', true, $h, 'Описание');
    $this->assertSelectCount('html body p', true, $h, 'Стек вызовов');
  }

  protected function setUp()
  {
    $this->path = realpath(__DIR__ . '/../ctrl');
  }
}