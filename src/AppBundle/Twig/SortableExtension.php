<?php

namespace AppBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class SortableExtension extends \Twig_Extension
{
  private $request;
  private $router;
  public function getName()
  {
    return 'sortable';
  }
  public function getFunctions()
  {
    return array('sortable' => new \Twig_Function_Method($this, 'sortable'),
                 'isSorted' => new \Twig_Function_Method($this, 'isSorted'));
  }
  public function setRequest(ContainerInterface $container)
  {
    $this->request = $container->get('request');
    $this->router = $container->get('router');
  }
  public function isSorted($key)
  {
    return ($this->request->query->get('sortBy') != null) && $this->request->query->get('sortBy') === $key;

  }
  public function sortable($newSortBy)
  {
    $sortBy = $this->request->query->get('sortBy', 'created');
    $order = $this->request->query->get('order', 'asc');
    if(!in_array($sortBy, array('created', 'owner', 'title'))){
        $sortBy = $newSortBy;
    }
    if($this->isSorted($newSortBy)){
      $order = ($this->request->query->get('order') == 'asc') ? 'desc' : 'asc';
    }else{
      $sortBy = $newSortBp..
    }
    return $this->router->generate('_gallery', array(
      'page' => $this->request->query->get('page', 1),
      'sortBy' => $sortBy,
      'order' => $order
    ), true);
  }
}
