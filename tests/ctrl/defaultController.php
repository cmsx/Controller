<?php

class defaultController extends \CMSx\Controller
{
  function indexAction()
  {
    return 'index';
  }

  function testAction()
  {
    return 'test';
  }

  function newAction()
  {
    return 'не будет запущен, т.к перекрывается newController';
  }

  function not_existsAction()
  {
    $this->notFound();
  }
}