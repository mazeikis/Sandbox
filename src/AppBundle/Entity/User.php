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

    protected $apiKey;

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
        $this->generateApiKey();

    }


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
     * @param  string $username
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
     * @param  string $password
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
     * @param  string $firstName
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
     * @param  string $lastName
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
     * @param  string $email
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
     * @param  boolean $enabled
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
     * @param  \DateTime $created
     * @return Users
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
     * @return Users
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
        return serialize(
            array(
             $this->id,
             $this->username,
             $this->password,
            )
        );

    }


    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
        ) = unserialize($serialized);

    }


    public function eraseCredentials()
    {
        return null;
    }


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

    }


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
     * Set confirmationToken
     *
     * @param  string $confirmationToken
     * @return User
     */
    public function setConfirmationToken($confirmationToken = null)
    {
        $this->confirmationToken = $confirmationToken;

    }


    /**
     * Get confirmationToken
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;

    }


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

    /**
     * Set apiKey
     *
     * @param string $apiKey
     * @return User
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return string 
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
    * Generate unique apiKey
    *
    * @return User
    */
    public function generateApiKey()
    {
        $this->apiKey = rtrim(strtr(base64_encode(hash('sha256', uniqid(), true)), '+/', '-_'), '=');

        return $this;
    }
}
