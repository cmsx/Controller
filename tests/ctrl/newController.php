<?php

class newController extends \CMSx\Controller
{
  function indexAction()
  {
    return 'new index';
  }

  function testAction()
  {
    return 'new test';
  }

  function __call($name, $arguments)
  {
    return 'you called ' . $this->controller . ' ' . $this->action . '!';
  }
}