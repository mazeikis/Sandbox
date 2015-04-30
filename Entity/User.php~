<?php
namespace AppBundle\Entity;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, \Serializable

{
	protected $id;
	protected $username;
	protected $password;
	protected $firstName;
	protected $lastName;
	protected $email;
	protected $roles;
	protected $enabled;
	protected $created;
	protected $updated;

    protected $token;

    protected $plainPassword;
	
    public function __construct()
    {
        $this->setEnabled(false);
        $this->setCreated(new \Datetime);
        $this->setUpdated(new \Datetime);
        $this->setRoles('ROLE_USER');
        $this->setToken(NULL);
    }
    /**
     * Set id
     *
     * @param integer $id
     * @return User
     */
    public function setId($Id)
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
     * Set login
     *
     * @param string $login
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get login
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return User
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Users
     */
    public function setCreated($created)
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
     * @param \DateTime $updated
     * @return Users
     */
    public function setUpdated($updated)
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
    
    public function setRoles($role)
    {
        return $this->roles = $role;
    }

    public function getRoles()
    {
        return array($this->roles);
    }
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
   }
   public function eraseCredentials()
    {
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return NULL;
    }
    /**
    * Set plainPassword
    *
    * @param string $plainPassword
    * @return User
    */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
    * Get plainPassword
    *
    * @return string
    */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
    /**
    * Set token
    *
    * @param string $token
    * @return User
    */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
    *
    * Get token
    * @return string
    */
    public function getToken()
    {
        return $this->token;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $images;


    /**
     * Add images
     *
     * @param \AppBundle\Entity\Image $images
     * @return User
     */
    public function addImage(\AppBundle\Entity\Image $images)
    {
        $this->images[] = $images;

        return $this;
    }

    /**
     * Remove images
     *
     * @param \AppBundle\Entity\Image $images
     */
    public function removeImage(\AppBundle\Entity\Image $images)
    {
        $this->images->removeElement($images);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }
}
