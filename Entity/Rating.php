<?php
namespace AppBundle\Entity;

class Rating
{
    protected $imageID;
	protected $userID;
	protected $rating;


    /**
     * Set imageID
     *
     * @param integer $imageID
     * @return Rating
     */
    public function setImageID($imageID)
    {
        $this->imageID = $imageID;

        return $this;
    }

    /**
     * Get imageID
     *
     * @return integer 
     */
    public function getImageID()
    {
        return $this->imageID;
    }

    /**
     * Set userID
     *
     * @param integer $userID
     * @return Rating
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;

        return $this;
    }

    /**
     * Get userID
     *
     * @return integer 
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     * @return Rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return integer 
     */
    public function getRating()
    {
        return $this->rating;
    }

}
