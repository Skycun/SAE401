<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Brands
 *
 * Represents a brand entity in the database.
 *
 * @ORM\Entity
 * @ORM\Table(name="brands")
 */
class Brands
{
    /**
     * The unique identifier for the brand.
     *
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $brand_id;

    /**
     * The name of the brand.
     *
     * @var string
     * @ORM\Column(type="string")
     */
    private string $brand_name;

    /**
     * The collection of products associated with this brand.
     *
     * @var Collection<int, Products>
     * @ORM\OneToMany(targetEntity="Products", mappedBy="brand")
     */
    private Collection $products;

    /**
     * Brands constructor.
     * Initializes the products collection.
     */
    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    /**
     * Get the value of brand_id.
     *
     * @return int The brand's unique identifier.
     */
    public function getBrandId(): int
    {
        return $this->brand_id;
    }

    /**
     * Set the value of brand_id.
     *
     * @param int $brand_id The brand's unique identifier.
     * @return self
     */
    public function setBrandId(int $brand_id): self
    {
        $this->brand_id = $brand_id;
        return $this;
    }

    /**
     * Get the value of brand_name.
     *
     * @return string The name of the brand.
     */
    public function getBrandName(): string
    {
        return $this->brand_name;
    }

    /**
     * Set the value of brand_name.
     *
     * @param string $brand_name The name of the brand.
     * @return self
     */
    public function setBrandName(string $brand_name): self
    {
        $this->brand_name = $brand_name;
        return $this;
    }

    /**
     * Get the collection of products associated with this brand.
     *
     * @return Collection<int, Products>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * Serialize the Brands object to an array for JSON representation.
     *
     * @return array
     */
    public function jsonSerialize(){
        $res = [
            'brand_id' => $this->getBrandId(),
            'brand_name' => $this->getBrandName()
        ];
        return $res;
    }

}