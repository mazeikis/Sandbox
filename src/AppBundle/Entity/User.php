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

    protected $confirmationToken;

    protected $plainPassword;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $images;

    public function __construct()
    {
        $this->setEnabled(false);
        $this->setCreated(new \Datetime);
        $this->setUpdated(new \Datetime);
        $this->setRoles('ROLE_USER');
        $this->setConfirmationToken(null);

    }//end __construct()


    /**
     * Set id
     *
     * @param  integer $id
     * @return User
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
     * Set login
     *
     * @param  string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;

    }//end setUsername()


    /**
     * Get login
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;

    }//end getUsername()


    /**
     * Set password
     *
     * @param  string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;

    }//end setPassword()


    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;

    }//end getPassword()


    /**
     * Set firstName
     *
     * @param  string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;

    }//end setFirstName()


    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;

    }//end getFirstName()


    /**
     * Set lastName
     *
     * @param  string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;

    }//end setLastName()


    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;

    }//end getLastName()


    /**
     * Set email
     *
     * @param  string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;

    }//end setEmail()


    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;

    }//end getEmail()


    /**
     * Set enabled
     *
     * @param  boolean $enabled
     * @return User
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;

    }//end setEnabled()


    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;

    }//end getEnabled()


    /**
     * Set created
     *
     * @param  \DateTime $created
     * @return Users
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
     * @return Users
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


    public function setRoles($role)
    {
        return $this->roles = $role;

    }//end setRoles()


    public function getRoles()
    {
        return array($this->roles);

    }//end getRoles()


    public function serialize()
    {
        return serialize(
            array(
             $this->id,
             $this->username,
             $this->password,
            )
        );

    }//end serialize()


    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
        ) = unserialize($serialized);

    }//end unserialize()


    public function eraseCredentials()
    {

    }//end eraseCredentials()


    /**
     * Set salt
     *
     * @param  string $salt
     * @return User
     */


    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return null;

    }//end getSalt()


    /**
     * Set plainPassword
     *
     * @param  string $plainPassword
     * @return User
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        $this->setPassword(null);

    }//end setPlainPassword()


    /**
     * Get plainPassword
     *
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;

    }//end getPlainPassword()


    /**
     * Set confirmationToken
     *
     * @param  string $confirmationToken
     * @return User
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

    }//end setConfirmationToken()


    /**
     * Get confirmationToken
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;

    }//end getConfirmationToken()


    /**
     * Add images
     *
     * @param  \AppBundle\Entity\Image $images
     * @return User
     */
    public function addImage(\AppBundle\Entity\Image $images)
    {
        $this->images[] = $images;

        return $this;

    }//end addImage()


    /**
     * Remove images
     *
     * @param \AppBundle\Entity\Image $images
     */
    public function removeImage(\AppBundle\Entity\Image $images)
    {
        $this->images->removeElement($images);

    }//end removeImage()


    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;

    }//end getImages()


    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $votes;


    /**
     * Add votes
     *
     * @param \AppBundle\Entity\Vote $votes
     * @return User
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
