<?php

namespace AppBundle\Twig;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RequestStack;

class SortableExtension extends \Twig_Extension
{
  private $router;
  private $request;

  public function getName()
  {
    return 'sortable';
  }
  public function getFunctions()
  {
    return array('sortable' => new \Twig_SimpleFunction('sortable', array($this, 'sortable')),
                 'isSorted' => new \Twig_SimpleFunction('isSorted', array($this, 'isSorted')));
  }
  public function __construct(Router $router, RequestStack $requestStack)
  {
    $this->router = $router;
    $this->request = $requestStack->getCurrentRequest();

  }
  public function isSorted($key)
  {
    return ($this->request->query->get('sortBy') !== null) && $this->request->query->get('sortBy') === $key;

  }
  public function sortable($newSortBy)
  {
    $sortBy = $this->request->query->get('sortBy', 'created');
    $order = $this->request->query->get('order', 'desc');
    
    if($this->isSorted($newSortBy)){
      $order = ($order == 'desc') ? 'asc' : 'desc';
    }else{
      $sortBy = $newSortBy;
    }
    return $this->router->generate('_gallery', array(
      'page' => $this->request->query->get('page', 1),
      'sortBy' => $sortBy,
      'order' => $order
    ), true);
  }
}
