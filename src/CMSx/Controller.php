<?php

namespace CMSx;

use CMSx\Controller\Exception;

class Controller
{
  /** @var URL */
  protected $url;

  /** Имя контроллера. Для someController = some */
  protected $controller;

  /** Имя действия. Для someAction = some */
  protected $action;

  function __construct($controller, $action, URL $url = null)
  {
    if (is_null($url)) {
      $url = new URL;
      $url->load();
    }

    $this->controller = $controller;
    $this->action     = $action;
    $this->url        = $url;
  }

  /**
   * Редирект на заданный URL
   * $permanent - постоянный или временный редирект
   *
   * @throws Exception
   */
  public function redirect($url, $permanent = false)
  {
    Exception::Redirect($url, $permanent);
  }

  /**
   * Редирект на предыдущую страницу по HTTP REFERER`у
   * Если реферер не указан, отправляет на главную
   */
  public function back()
  {
    $this->redirect(!empty($_SERVER['HTTP_REFERER']) ? : '/', false);
  }

  /**
   * Ошибка страница не найдена
   * @throws Exception
   */
  public function notFound($msg = null)
  {
    throw new Exception($msg, Exception::NOT_FOUND);
  }

  /**
   * Ошибка доступ без авторизации запрещен
   * @throws Exception
   */
  public function unauthorized($msg = null)
  {
    throw new Exception($msg, Exception::UNAUTHORIZED);
  }

  /**
   * Ошибка доступ запрещен
   * @throws Exception
   */
  public function forbidden($msg = null)
  {
    throw new Exception($msg, Exception::FORBIDDEN);
  }

  /**
   * Ошибка Сервис недоступен
   * @throws Exception
   */
  public function unavailable($msg = null)
  {
    throw new Exception($msg, Exception::UNAVAILABLE);
  }

  /**
   * Ошибка "ошибка сервера"
   * @throws Exception
   */
  public function serverError($msg = null)
  {
    throw new Exception($msg, Exception::SERVER_ERROR);
  }
}