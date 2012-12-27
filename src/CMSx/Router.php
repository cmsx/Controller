<?php

namespace CMSx;

use CMSx\Controller\Exception;

class Router
{
  /** Путь к контроллеру */
  protected $controllers_path;
  /** @var URL */
  protected $url;

  protected $controller;
  protected $action;
  protected $debug_mode;

  function __construct(URL $url = null, $controllers_path = null)
  {
    if (is_null($url)) {
      $url = new URL();
      $url->load();
    }

    if (!is_null($controllers_path)) {
      $this->setControllersPath($controllers_path);
    }

    $this->setURL($url);
    $this->init();
  }

  /** Выполнение нужного контроллера */
  public function process($return = false)
  {
    try {
      $this->processURL();

      if (!$res = $this->execute()) {
        return false;
      }
    } catch (\Exception $e) {
      return $this->handleException($e);
    }

    if ($return) {
      return $res;
    }

    echo $res;
  }

  /** Текущий URL */
  public function setURL(URL $url)
  {
    $this->url = $url;

    return $this;
  }

  /** Текущий URL */
  public function getURL()
  {
    return $this->url;
  }

  /** Путь к папке с контроллерами */
  public function setControllersPath($path)
  {
    $this->controllers_path = rtrim($path, DIRECTORY_SEPARATOR);

    return $this;
  }

  /** Путь к папке с контроллерами */
  public function getControllersPath()
  {
    return $this->controllers_path;
  }

  /** Режим отладки при выводе информации об ошибках */
  public function enableDebugMode($on = true)
  {
    $this->debug_mode = $on;

    return $this;
  }

  /** Режим отладки при выводе информации об ошибках */
  public function isDebugMode()
  {
    return (bool)$this->debug_mode;
  }

  /** Определяем контроллер и действие */
  protected function processURL()
  {
    $one = $this->url->getArgument(1);
    $two = $this->url->getArgument(2);

    $this->controller = 'default';
    $this->action     = 'index';

    if ($this->checkControllerFileExists($one)) {
      $this->controller = $one;
      if ($two) {
        $this->action = $two;
      }
    } else {
      if (!$this->checkControllerFileExists($this->controller)) {
        throw new Exception($this->controller . 'Controller не существует', Exception::NOT_FOUND);
      }

      if ($one) {
        $this->action = $one;
      }
    }
  }

  /** Запуск нужного контроллера */
  protected function execute()
  {
    $c = $this->controller . 'Controller';
    $a = $this->action . 'Action';

    require_once $this->getControllerFilename($this->controller);

    /** @var $ctrl Controller */
    $ctrl = new $c($this->controller, $this->action, $this->url);

    if (!is_callable(array($ctrl, $a))) {
      $ctrl->notFound(sprintf('Метод "%s" в контроллере "%s" не найден', $a, $c));
    }

    return $ctrl->{$a}();
  }

  /** Обработка ошибок */
  protected function handleException(\Exception $e)
  {
    $p = $this->getErrorPage();

    if ($e instanceof \CMSx\Controller\Exception) {
      $h = $e->getHTTPHeader();

      if ($e->isRedirect()) { //Для редиректа страница ошибки не выводится
        header('Location: ' . $e->getRedirectUrl());
        header($h);

        return false;
      }

      if ($h) {
        header($h);
      }

      $p->set('title', $e->getHumanMessage());
    } else {
      $p->set('title', 'Ошибка!');
    }

    if ($this->isDebugMode()) {
      $p->set('message', $e->getMessage());
      $p->set('stack', $e->getTraceAsString());
    }

    return $p;
  }

  /** @return Page - Объект Page для отображения ошибки */
  protected function getErrorPage()
  {
    return new Page();
  }

  /** Проверка существования файла контроллера */
  protected function checkControllerFileExists($ctrl)
  {
    return is_file($this->getControllerFilename($ctrl));
  }

  /** Путь к файлу контроллера */
  protected function getControllerFilename($ctrl)
  {
    return $this->controllers_path . DIRECTORY_SEPARATOR . $ctrl . 'Controller.php';
  }

  /** Дополнительная инициализация при наследовании */
  protected function init()
  {
  }
}