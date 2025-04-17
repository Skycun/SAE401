<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Products
 *
 * Represents a product entity in the database.
 *
 * @ORM\Entity
 * @ORM\Table(name="products")
 */
class Products
{
    /**
     * The unique identifier for the product.
     *
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $product_id;

    /**
     * The name of the product.
     *
     * @var string
     * @ORM\Column(type="string")
     */
    private string $product_name;

    /**
     * The brand associated with the product.
     *
     * @var Brands
     * @ORM\ManyToOne(targetEntity="Brands", inversedBy="products")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="brand_id")
     */
    private Brands $brand;

    /**
     * The category associated with the product.
     *
     * @var Categories
     * @ORM\ManyToOne(targetEntity="Categories", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="category_id")
     */
    private Categories $category;

    /**
     * The model year of the product.
     *
     * @var int
     * @ORM\Column(type="integer")
     */
    private int $model_year;

    /**
     * The list price of the product.
     *
     * @var float
     * @ORM\Column(type="float")
     */
    private float $list_price;

    /**
     * The collection of stocks associated with this product.
     *
     * @var Collection<int, Stocks>
     * @ORM\OneToMany(targetEntity="Stocks", mappedBy="product")
     */
    private Collection $stocks;

    /**
     * Products constructor.
     * Initializes the stocks collection.
     */
    public function __construct()
    {
        $this->stocks = new ArrayCollection();
    }

    /**
     * Get the value of product_id.
     *
     * @return int
     */
    public function getProductId(): int
    {
        return $this->product_id;
    }

    /**
     * Set the value of product_id.
     *
     * @param int $product_id
     * @return self
     */
    public function setProductId(int $product_id): self
    {
        $this->product_id = $product_id;
        return $this;
    }

    /**
     * Get the value of product_name.
     *
     * @return string
     */
    public function getProductName(): string
    {
        return $this->product_name;
    }

    /**
     * Set the value of product_name.
     *
     * @param string $product_name
     * @return self
     */
    public function setProductName(string $product_name): self
    {
        $this->product_name = $product_name;
        return $this;
    }

    /**
     * Get the brand associated with the product.
     *
     * @return Brands
     */
    public function getBrand(): Brands
    {
        return $this->brand;
    }

    /**
     * Set the brand associated with the product.
     *
     * @param Brands $brand
     * @return self
     */
    public function setBrand(Brands $brand): self
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * Get the category associated with the product.
     *
     * @return Categories
     */
    public function getCategory(): Categories
    {
        return $this->category;
    }

    /**
     * Set the category associated with the product.
     *
     * @param Categories $category
     * @return self
     */
    public function setCategory(Categories $category): self
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get the model year of the product.
     *
     * @return int
     */
    public function getModelYear(): int
    {
        return $this->model_year;
    }

    /**
     * Set the model year of the product.
     *
     * @param int $model_year
     * @return self
     */
    public function setModelYear(int $model_year): self
    {
        $this->model_year = $model_year;
        return $this;
    }

    /**
     * Get the list price of the product.
     *
     * @return float
     */
    public function getListPrice(): float
    {
        return $this->list_price;
    }

    /**
     * Set the list price of the product.
     *
     * @param float $list_price
     * @return self
     */
    public function setListPrice(float $list_price): self
    {
        $this->list_price = $list_price;
        return $this;
    }

    /**
     * Get the collection of stocks associated with this product.
     *
     * @return Collection<int, Stocks>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    /**
     * Add a stock to the product.
     *
     * @param Stocks $stock
     * @return self
     */
    public function addStock(Stocks $stock): self
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks[] = $stock;
            $stock->setProduct($this);
        }
        return $this;
    }

    /**
     * Remove a stock from the product.
     *
     * @param Stocks $stock
     * @return self
     */
    public function removeStock(Stocks $stock): self
    {
        if ($this->stocks->removeElement($stock)) {
            if ($stock->getProduct() === $this) {
                $stock->setProduct(null);
            }
        }
        return $this;
    }

    /**
     * Serialize the Products object to an array for JSON representation.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $res = [
            'product_id' => $this->getProductId(),
            'product_name' => $this->getProductName(),
            'brand' => $this->getBrand()->jsonSerialize(),
            'category' => $this->getCategory()->jsonSerialize(),
            'model_year' => $this->getModelYear(),
            'list_price' => $this->getListPrice(),
            'stocks' => array_map(function ($stock) {
                return $stock->jsonSerialize();
            }, $this->getStocks()->toArray())
        ];
        return $res;
    }
}