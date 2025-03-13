<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="brands")
 */
class Brands
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $brand_id;

    /**
     * @ORM\Column(type="string")
     */
    private string $brand_name;

    /**
     * @ORM\OneToMany(targetEntity="Products", mappedBy="brand")
     * @var Collection<int, Products>
     */
    private Collection $products;

    // Constructor

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }


    // Getters and Setters

        /**
     * Get the value of brand_id
     *
     * @return int
     */ 
    public function getBrandId(): int
    {
        return $this->brand_id;
    }

    /**
     * Set the value of brand_id
     *
     * @param int $brand_id
     * @return self
     */ 
    public function setBrandId(int $brand_id): self
    {
        $this->brand_id = $brand_id;
        return $this;
    }

    /**
     * Get the value of brand_name
     *
     * @return string
     */ 
    public function getBrandName(): string
    {
        return $this->brand_name;
    }

    /**
     * Set the value of brand_name
     *
     * @param string $brand_name
     * @return self
     */ 
    public function setBrandName(string $brand_name): self
    {
        $this->brand_name = $brand_name;
        return $this;
    }

    public function jsonSerialize(){
        $res = [
            'brand_id' => $this->getBrandId(),
            'brand_name' => $this->getBrandName()
        ];
        return $res;
    }

}