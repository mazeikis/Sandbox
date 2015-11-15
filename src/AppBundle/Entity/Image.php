<?php
namespace AppBundle\Entity;

class Image
{

    protected $id;

    protected $path;

    protected $resolution;

    protected $size;

    protected $title;

    protected $description;

    protected $created;

    protected $updated;

    protected $owner;


    /**
     * Set id
     *
     * @param  integer $id
     * @return Images
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;

    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;

    }


    /**
     * Set path
     *
     * @param  string $path
     * @return Images
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;

    }


    /**
     * Get Path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;

    }


    /**
     * Set resolution
     *
     * @param  string $resolution
     * @return Images
     */
    public function setResolution($resolution)
    {
        $this->resolution = $resolution;

        return $this;

    }


    /**
     * Get resolution
     *
     * @return string
     */
    public function getResolution()
    {
        return $this->resolution;

    }


    /**
     * Set size
     *
     * @param  integer $size
     * @return Images
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;

    }


    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;

    }


    /**
     * Set description
     *
     * @param  string $title
     * @return Images
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;

    }


    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;

    }


    /**
     * Set description
     *
     * @param  string $description
     * @return Images
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;

    }


    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;

    }


    /**
     * Set created
     *
     * @param  \DateTime $created
     * @return Images
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;

    }


    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;

    }


    /**
     * Set updated
     *
     * @param  \DateTime $updated
     * @return Images
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;

        return $this;

    }


    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;

    }


    /**
     * @var \AppBundle\Entity\User
     */


    /**
     * Set owner
     *
     * @param  \AppBundle\Entity\User $owner
     * @return Image
     */
    public function setOwner(\AppBundle\Entity\User $owner=null)
    {
        $this->owner = $owner;

        return $this;

    }


    /**
     * Get owner
     *
     * @return \AppBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;

    }


    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $votes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->votes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add votes
     *
     * @param \AppBundle\Entity\Vote $votes
     * @return Image
     */
    public function addVote(\AppBundle\Entity\Vote $votes)
    {
        $this->votes[] = $votes;

        return $this;
    }

    /**
     * Remove votes
     *
     * @param \AppBundle\Entity\Vote $votes
     */
    public function removeVote(\AppBundle\Entity\Vote $votes)
    {
        $this->votes->removeElement($votes);
    }

    /**
     * Get votes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVotes()
    {
        return $this->votes;
    }
}
