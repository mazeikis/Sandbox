<?php

namespace AppBundle\Helpers;

use Symfony\Component\HttpFoundation\Request;

class PageManager
{
	private $request;
	private $page;//
	private $resultsPerPage;
	private $lastPage;
	private $sortBy;//
	private $order;//
	private $startingItem;

	public function __construct(Request $request, $totalRows = null, $resultsPerPage = null)
	{
		$this->setRequest($request);
		$this->setPage();
		$this->setResultsPerPage($resultsPerPage);
		$this->setLastPage($totalRows);
		$this->setSortBy();
		$this->setOrder();
		$this->setStartingItem();
	}
	public function setRequest($request)
	{
		$this->request = $request;
	}
	public function setPage()
	{
		if(!$this->request->query->get('page') || $this->request->query->get('page') < 1){
            $this->page = 1;
        }else{
        	$this->page = $this->request->query->get('page');
        }
	}
	public function getPage()
	{
		return $this->page;
	}
	public function setLastPage($totalRows)
	{
		$this->lastPage = $totalRows? ceil($totalRows / $this->resultsPerPage) : null;

	}
	public function setResultsPerPage($resultsPerPage)
	{
		$this->resultsPerPage = $resultsPerPage;
	}
	public function getResultsPerPage()
	{
		return $this->resultsPerPage;
	}
	public function setSortBy()
	{
		if(!in_array($this->request->query->get('sortBy'), ['created', 'owner', 'title'])){
            $this->sortBy = 'created';
        }else{
        	$this->sortBy = $this->request->query->get('sortBy');
        }
	}
	public function getSortBy()
	{
		return $this->sortBy;
	}
	public function setOrder()
	{
		if(!in_array($this->request->query->get('order'), ['ASC', 'DESC'])){
            $this->order = 'DESC';
        }else{
        	$this->order = $this->request->query->get('order');
        }
	}
	public function getOrder()
	{
		return $this->order;
	}
	public function setStartingItem()
	{
		$this->startingItem = $this->resultsPerPage * ($this->page - 1) ;
	}
	public function getStartingItem()
	{
		return $this->startingItem;
	}
	public function getPagination()
	{
		/* 5 numbers in pagination links, like [1] [2] [3] [4] [5] */
		$pageCount = 5;
		$half = floor($pageCount / 2);
		$result = array();

		/* if total amount of pages less than 5 */
		if($this->lastPage <= $pageCount)
		{
			$result = range(1, $this->lastPage);
			return $result;
		}
		/* current page is the last 3 pages */
		if ($this->page + $half > $this->lastPage){
            $result = range($lastPage - $pageCount + 1, $lastPage);
            return $result;
        }else{
        /* last page is > 5 and it's not in the last 3 */
            $result = range($this->page - $half, $this->page + $half);
            return $result;
        }
	}
	public function getLastPage()
	{
		return $this->lastPage;
	}
}
