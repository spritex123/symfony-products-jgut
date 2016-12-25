<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name = "user")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type = "integer")
     * @ORM\GeneratedValue(strategy = "AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type = "string", length = 255)
     *
     * @Assert\NotBlank(groups = {"registration", "authorization", "change", "forgotpassword"})
     * @Assert\Email(groups = {"registration", "authorization", "forgotpassword"})
     * @Assert\Length(max = 255, groups = {"registration", "authorization", "change", "forgotpassword"})
     */
    protected $email;

    /**
     * @ORM\Column(type = "string", length = 255)
     *
     * @Assert\NotBlank(groups = {"registration", "authorization", "change", "forgotsetpassword"})
     * @Assert\Length(max = 255, groups = {"registration", "authorization", "change", "forgotsetpassword"})
     */
    protected $password;

    /**
     * @ORM\Column(type = "string", length = 255)
     */
    protected $roles = 'ROLE_USER';

    /**
     * @ORM\Column(type = "string", length = 255)
     *
     * @Assert\NotBlank(groups = {"registration", "change"})
     * @Assert\Length(max = 255, groups = {"registration", "change"})
     */
    protected $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type = "datetime")
     */
    protected $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type = "datetime")
     */
    protected $updated;

    /**
     * @ORM\Column(type = "boolean")
     */
    protected $enabled = false;

    /**
     * @ORM\Column(type = "string", length = 32)
     */
    protected $token;

    /**
     * @ORM\Column(type = "boolean", nullable = true)
     */
    protected $forgotPassword = false;

    /**
     * @ORM\OneToMany(targetEntity = "Product", mappedBy = "user")
     **/

    protected $products;

    protected $username;
    protected $salt;

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($email)
    {
        $this->username = $email;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function eraseCredentials()
    {
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
     * Set roles
     *
     * @param string $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return string
     */
    public function getRoles()
    {
        //return $this->roles;
        return array('ROLE_USER');
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @ORM\PrePersist
     * @return User
     */
    public function setCreated($created)
    {
        $this->created = new \DateTime();

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
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @return User
     */
    public function setUpdated($updated)
    {
        $this->updated = new \DateTime();

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
     * Set token
     *
     * @param string $token
     * @return User
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add products
     *
     * @param \AppBundle\Entity\Product $products
     * @return User
     */
    public function addProduct(\AppBundle\Entity\Product $products)
    {
        $this->products[] = $products;

        return $this;
    }

    /**
     * Remove products
     *
     * @param \AppBundle\Entity\Product $products
     */
    public function removeProduct(\AppBundle\Entity\Product $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set forgotPassword
     *
     * @param boolean $forgotPassword
     * @return User
     */
    public function setForgotPassword($forgotPassword)
    {
        $this->forgotPassword = $forgotPassword;

        return $this;
    }

    /**
     * Get forgotPassword
     *
     * @return boolean
     */
    public function getForgotPassword()
    {
        return $this->forgotPassword;
    }
}
