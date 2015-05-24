<?php

namespace AppBundle\Twig;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RequestStack;

class SortableExtension extends \Twig_Extension
{
  private $router;
  private $requestStack;

  public function getName()
  {
    return 'sortable';
  }
  public function getFunctions()
  {
    return array('sortable' => new \Twig_SimpleFunction('sortable', array($this, 'sortable'), array('is_safe' => array('html'), 'needs_environment' => 'true')),
                 'isSorted' => new \Twig_SimpleFunction('isSorted', array($this, 'isSorted')));
  }
  public function __construct(Router $router, RequestStack $requestStack)
  {
    $this->router = $router;
    $this->requestStack = $requestStack;
  }
  public function isSorted($key){
     return $this->requestStack->getCurrentRequest()->query->get('sortBy') === $key;

  }
  public function sortable($twig, $newSortBy, $buttonValue)
  {
    $request = $this->requestStack->getCurrentRequest();
    $sortBy = $request->query->get('sortBy', 'created');
    $order = $request->query->get('order', 'desc');
    if($this->isSorted($newSortBy)){
      $order = $order === 'desc' ? 'asc' : 'desc';
    }else{
      $sortBy = $newSortBy;
    }
    $link = $this->router->generate('_gallery', array(
      'page' => $request->query->get('page', 1), 
      'order' => $order,
      'sortBy' => $sortBy,
      ), true);
    $iconKey = $order == 'desc' ? '-alt' : null;
    return $twig->render('AppBundle:Twig:sortButtons.html.twig', array('test' => $newSortBy, 'link' => $link, 'iconKey' => $iconKey, 'buttonValue' => $buttonValue));
  }
}
