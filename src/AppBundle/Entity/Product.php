<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name = "product")
 */
class Product
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
     * @Assert\NotBlank(groups = {"product"})
     * @Assert\Length(max = 255, maxMessage = "Max Length 255", groups = {"product"})
     */
    protected $name;

    /**
     * @ORM\Column(type = "string", length = 255)
     *
     * @Assert\NotBlank(groups = {"product"})
     * @Assert\Length(max = 255, maxMessage = "Max Length 255", groups = {"product"})
     */
    protected $description;

    /**
     * @ORM\Column(type = "string", length = 255, nullable = true)
     *
     * @Assert\Image(maxSize = "1M", mimeTypes = {"image/jpeg" , "image/png"}, mimeTypesMessage = "Image type jpg, png", groups = {"product"})
     */
    protected $thumbnail;

    /**
     * @ORM\Column(type = "decimal", precision = 10, scale = 2)
     *
     * @Assert\NotBlank(groups = {"product"})
     * @Assert\Type(type="numeric", groups = {"product"})
     * @ASSert\GreaterThanOrEqual(value = 0, groups = {"product"})
     * @Assert\LessThanOrEqual(value = 99999999.99, groups = {"product"})
     */
    protected $price;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity = "User", inversedBy = "products")
     * @ORM\JoinColumn(name = "user_id", referencedColumnName = "id")
     **/
    protected $user;

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
     * Set name
     *
     * @param string $name
     * @return Product
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
     * Set description
     *
     * @param string $description
     * @return Product
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
     * Set thumbnail
     *
     * @param string $thumbnail
     * @return Product
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Get thumbnail
     *
     * @return string
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Product
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
