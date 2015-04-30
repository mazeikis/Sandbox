<?php

namespace AppBundle\Helpers;

class Paginator
{
	private $totalPages;
	private $page;
	private $resultsPerPage;

	public function __construct($page, $rowCount, $resultsPerPage)
	{
		$this->page = $page;
		$this->resultsPerPage = $resultsPerPage;

		$this->setTotalPages($rowCount, $resultsPerPage);
	}

	public function setTotalPages($rowCount, $resultsPerPage)
	{
		$this->totalPages = ceil($rowCount / $resultsPerPage);
	}

	public function getTotalPages()
	{
		return $this->totalPages;
	}

	public function getPagesList()
	{
		$pageCount = 5;
		$result = array();
		$half = floor($pageCount / 2);

		if($this->totalPages <= $pageCount)
		{
			$i = 1;
			while($i <= $this->totalPages)
			{
				$result[] = $i;
				$i++;
			}
			return $result;
		}
		if($this->page <=3)
		{
			$i = 1;
			while($i <= $this->totalPages && $i < $pageCount) //stop at $pageCount or lower
			{
				$result[] = $i;
				$i++;
			}
			return $result;
		}
		//////////////////////////////
		if ($this->page + $half > $this->totalPages) // Close to end
        {
            while ($pageCount >= 1)
            {
                $result[] = $this->totalPages - $pageCount + 1;
                $pageCount--;
            }
            return $result;
        } else
        {
            while ($pageCount >= 1)
            {
                $result[] = $this->page - $pageCount + $half + 1;
                $pageCount--;
            }
            return $result;
        }

	}

}