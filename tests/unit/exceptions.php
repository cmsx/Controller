<?php

require_once __DIR__.'/../init.php';

use CMSx\Controller;
use CMSx\Controller\Exception;

class ExceptionsTest extends PHPUnit_Framework_TestCase
{
  function testRedirect()
  {
    $c = new Controller('test', 'test');
    $p = '/some/path.html';

    try {
      $c->redirect($p);
      $this->fail('Редирект выбрасывает исключение #1');
    } catch (Exception $e) {
      $this->assertTrue($e->isRedirect(), 'Проверка, что это редирект #1');
      $this->assertEquals(Exception::REDIRECT_TEMP, $e->getCode(), 'Временный редирект #1');
      $this->assertEquals($p, $e->getRedirectUrl(), 'URL совпадает #1');
    }

    try {
      $c->redirect($p, true);
      $this->fail('Редирект выбрасывает исключение #2');
    } catch (Exception $e) {
      $this->assertTrue($e->isRedirect(), 'Проверка, что это редирект #2');
      $this->assertEquals(Exception::REDIRECT_PERM, $e->getCode(), 'Временный редирект #2');
      $this->assertEquals($p, $e->getRedirectUrl(), 'URL совпадает #2');
    }
  }

  function testBack()
  {
    $c = new Controller('test', 'test');
    try {
      $c->back();
      $this->fail('Редирект выбрасывает исключение');
    } catch (Exception $e) {
      $this->assertTrue($e->isRedirect(), 'Проверка, что это редирект');
      $this->assertEquals(Exception::REDIRECT_TEMP, $e->getCode(), 'Временный редирект');
      $this->assertEquals('/', $e->getRedirectUrl(), 'Без REFERER`а отправляет на главную');
    }
  }

  function testNotFound()
  {
    $c = new Controller('test', 'test');
    try {
      $c->notFound();
      $this->fail('Выбрасывает исключение');
    } catch (Exception $e) {
      $this->assertFalse($e->isRedirect());
      $this->assertEquals(Exception::NOT_FOUND, $e->getCode(), 'Код ошибки');
    }
  }

  function testForbidden()
  {
    $c = new Controller('test', 'test');
    try {
      $c->forbidden();
      $this->fail('Выбрасывает исключение');
    } catch (Exception $e) {
      $this->assertEquals(Exception::FORBIDDEN, $e->getCode(), 'Код ошибки');
    }
  }

  function testUnauthorized()
  {
    $c = new Controller('test', 'test');
    try {
      $c->unauthorized();
      $this->fail('Выбрасывает исключение');
    } catch (Exception $e) {
      $this->assertEquals(Exception::UNAUTHORIZED, $e->getCode(), 'Код ошибки');
    }
  }

  function testUnavailable()
  {
    $c = new Controller('test', 'test');
    try {
      $c->unavailable();
      $this->fail('Выбрасывает исключение');
    } catch (Exception $e) {
      $this->assertEquals(Exception::UNAVAILABLE, $e->getCode(), 'Код ошибки');
    }
  }

  function testServerError()
  {
    $c = new Controller('test', 'test');
    try {
      $c->serverError();
      $this->fail('Выбрасывает исключение');
    } catch (Exception $e) {
      $this->assertEquals(Exception::SERVER_ERROR, $e->getCode(), 'Код ошибки');
    }
  }

  function testMessage()
  {
    $c = new Controller('test', 'test');
    try {
      $c->redirect('/some/path.html');
      $this->fail('Редирект выбрасывает исключение');
    } catch (\CMSx\Controller\Exception $e) {
      $m = Exception::GetHTTPInfo($e->getCode(), true);
      $s = Exception::GetHTTPInfo($e->getCode());
      $h = 'HTTP/1.0 ' . $e->getCode() . ' '.$s;
      $this->assertNotEmpty($m, 'Текст ошибки есть');
      $this->assertNotEmpty($s, 'Статус ошибки есть');
      $this->assertEquals($m, $e->getHumanMessage(), 'Текст ошибки для человеков');
      $this->assertEquals($s, $e->getHTTPStatus(), 'HTTP Status для роботов');
      $this->assertEquals($h, $e->getHTTPHeader(), 'Сформированный HTTP Header');
    }
  }
}