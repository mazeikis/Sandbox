<?php
namespace AppBundle\Entity;

class Image
{

    protected $id;

    protected $fileName;

    protected $resolution;

    protected $size;

    protected $title;

    protected $description;

    protected $extension;

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

    }//end setId()


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;

    }//end getId()


    /**
     * Set fileName
     *
     * @param  string $fileName
     * @return Images
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;

    }//end setFileName()


    /**
     * Get originalName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;

    }//end getFileName()


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

    }//end setResolution()


    /**
     * Get resolution
     *
     * @return string
     */
    public function getResolution()
    {
        return $this->resolution;

    }//end getResolution()


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

    }//end setSize()


    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;

    }//end getSize()


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

    }//end setTitle()


    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;

    }//end getTitle()


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

    }//end setDescription()


    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;

    }//end getDescription()


    /**
     * Set extension
     *
     * @param  string $extension
     * @return Images
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;

    }//end setExtension()


    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;

    }//end getExtension()


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

    }//end setCreated()


    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;

    }//end getCreated()


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

    }//end setUpdated()


    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;

    }//end getUpdated()


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

    }//end setOwner()


    /**
     * Get owner
     *
     * @return \AppBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;

    }//end getOwner()


}//end class
