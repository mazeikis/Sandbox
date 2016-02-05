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
      $this->router       = $router;
      $this->requestStack = $requestStack;

  }

  public function isSorted($key){
      return $this->requestStack->getCurrentRequest()->query->get('sortBy') === $key;

  }

  public function sortable($twig, $newSortBy, $buttonValue, $q = null)
  {
      $request = $this->requestStack->getCurrentRequest();
      $sortBy  = $request->query->get('sortBy', 'created');
      $order   = $request->query->get('order', 'desc');

      if($this->isSorted($newSortBy)) {
          $order = $order === 'desc' ? 'asc' : 'desc';
      } else {
          $sortBy = $newSortBy;
      }

      $parameters = array(
         'order'  => $order,
         'sortBy' => $sortBy,
      );

      if ($q !== null) {
          $parameters['q'] = $q;
      }

      $link    = $this->router->generate('_gallery', $parameters, "ABSOLUTE_PATH");
      $iconKey = $order === 'desc' ? 'up' : 'down';

      return $twig->render('AppBundle:Twig:sortButtons.html.twig', 
                            array(
                                  'newSortBy' => $newSortBy,
                                  'link' => $link,
                                  'iconKey' => $iconKey,
                                  'buttonValue' => $buttonValue
                                ));
  
  }

}
