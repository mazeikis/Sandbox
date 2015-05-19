<?php

namespace AppBundle\Twig;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RequestStack;

class SortableExtension extends \Twig_Extension
{
  private $router;
  private $request;
  private $twig;

  public function getName()
  {
    return 'sortable';
  }
  public function getFunctions()
  {
    return array('sortable' => new \Twig_SimpleFunction('sortable', array($this, 'sortable'), array('is_safe' => array('html'), 'needs_environment' => 'true')),
                 'isSorted' => new \Twig_SimpleFunction('isSorted', array($this, 'isSorted')));
  }
  public function __construct(Router $router, RequestStack $requestStack, \Twig_Environment $twig)
  {
    $this->router = $router;
    $this->request = $requestStack->getCurrentRequest();
    $this->twig = $twig;
  }
  public function isSorted($key)
  {
    return ($this->request->query->get('sortBy') !== null) && $this->request->query->get('sortBy') === $key;

  }
  public function sortable($newSortBy, $buttonValue)
  {
    $sortBy = $this->request->query->get('sortBy', 'created');
    $order = $this->request->query->get('order', 'desc');
    if($this->isSorted($newSortBy)){
      $order = $order == 'desc' ? 'asc' : 'desc';
    }else{
      $sortBy = $newSortBy;
    }
    $iconKey = $order == 'desc' ? '-alt' : null;
    return $this->twig->render('AppBundle:Twig:sortButtons.html.twig', array('link' => $this->router->generate('_gallery', array(
      'page' => $this->request->query->get('page', 1),
      'sortBy' => $sortBy,
      'order' => $order,
      'q' => $this->request->query->get('q', null)
    ), true), 'iconKey' => $iconKey, 'buttonValue' => $buttonValue));
  }
}
