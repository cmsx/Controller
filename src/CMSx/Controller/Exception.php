<?php

namespace CMSx\Controller;

class Exception extends \Exception
{
  /** Необходима авторизация */
  const UNAUTHORIZED = 401;
  /** Доступ запрещен */
  const FORBIDDEN = 403;
  /** Страница не найдена */
  const NOT_FOUND = 404;
  /** Ошибка сервера */
  const SERVER_ERROR = 500;
  /** Сервер недоступен */
  const UNAVAILABLE = 503;
  /** Редирект перманентный */
  const REDIRECT_PERM = 301;
  /** Редирект временный */
  const REDIRECT_TEMP = 302;

  protected $redirect_url;

  protected static $valid_codes = array(
    self::UNAUTHORIZED  => array(
      'message' => 'Для доступа к этой странице нужно авторизоваться',
      'status'  => 'Unauthorized'
    ),
    self::FORBIDDEN     => array(
      'message' => 'Доступ запрещен',
      'status'  => 'Forbidden'
    ),
    self::NOT_FOUND     => array(
      'message' => 'Страница не существует',
      'status'  => 'Not Found'
    ),
    self::SERVER_ERROR  => array(
      'message' => 'Ошибка сервера',
      'status'  => 'Internal Server Error'
    ),
    self::UNAVAILABLE   => array(
      'message' => 'Ведутся технические работы',
      'status'  => 'Service Unavailable'
    ),
    self::REDIRECT_TEMP => array(
      'message' => 'Страница временно перемещена',
      'status'  => 'Moved Temporarily'
    ),
    self::REDIRECT_PERM => array(
      'message' => 'Страница перемещена',
      'status'  => 'Moved Permanently'
    )
  );

  /** Текст ошибки по коду исключения */
  public function getHumanMessage()
  {
    return static::GetHTTPInfo($this->getCode(), true);
  }

  /** HTTP статус по коду исключения */
  public function getHTTPStatus()
  {
    return static::GetHTTPInfo($this->getCode(), false);
  }

  /** Получение адреса для перенаправления */
  public function getRedirectUrl()
  {
    return $this->isRedirect() ? $this->redirect_url : false;
  }

  /** Проверка, что исключения является перенаправлением */
  public function isRedirect()
  {
    return in_array($this->code, array(self::REDIRECT_PERM, self::REDIRECT_TEMP));
  }

  /** Установка адреса перенаправления */
  protected function setRedirectUrl($url)
  {
    $this->redirect_url = $url;
    return $this;
  }

  /**
   * Получение информации по коду ошибки
   * $code - код ошибки
   * $human - текст сообщения для человека или HTTP-статус
   */
  public static function GetHTTPInfo($code, $human = false)
  {
    $w = $human ? 'message' : 'status';
    return isset(static::$valid_codes[$code][$w])
      ? static::$valid_codes[$code][$w]
      : false;
  }

  /** @throws Exception */
  public static function Redirect($url, $permanent = false)
  {
    $e = new static('', $permanent ? Exception::REDIRECT_PERM : Exception::REDIRECT_TEMP);
    $e->setRedirectUrl($url);

    throw $e;
  }
}