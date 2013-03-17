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
   * Получение клона объекта текущего URL
   *
   * @return URL
   */
  public function getUrl()
  {
    return clone $this->url;
  }

  /** Проверка сделан ли запрос AJAXом */
  public function isAjax()
  {
    return isset ($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
  }

  /**
   * Редирект на заданный URL
   * $permanent - постоянный или временный редирект
   *
   * @throws Exception
   */
  public static function Redirect($url, $permanent = false)
  {
    Exception::Redirect($url, $permanent);
  }

  /**
   * Редирект на предыдущую страницу по HTTP REFERER`у
   * Если реферер не указан, отправляет на главную
   */
  public static function Back()
  {
    static::Redirect(!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/', false);
  }

  /**
   * Ошибка страница не найдена
   * @throws Exception
   */
  public static function NotFound($msg = null)
  {
    throw new Exception($msg, Exception::NOT_FOUND);
  }

  /**
   * Ошибка доступ без авторизации запрещен
   * @throws Exception
   */
  public static function Unauthorized($msg = null)
  {
    throw new Exception($msg, Exception::UNAUTHORIZED);
  }

  /**
   * Ошибка доступ запрещен
   * @throws Exception
   */
  public static function Forbidden($msg = null)
  {
    throw new Exception($msg, Exception::FORBIDDEN);
  }

  /**
   * Ошибка Сервис недоступен
   * @throws Exception
   */
  public static function Unavailable($msg = null)
  {
    throw new Exception($msg, Exception::UNAVAILABLE);
  }

  /**
   * Ошибка "ошибка сервера"
   * @throws Exception
   */
  public static function ServerError($msg = null)
  {
    throw new Exception($msg, Exception::SERVER_ERROR);
  }
}