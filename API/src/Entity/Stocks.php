<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Stocks
 *
 * Represents a stock entity in the database.
 *
 * @ORM\Entity
 * @ORM\Table(name="stocks")
 */
class Stocks
{
    /**
     * The unique identifier for the stock.
     *
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $stock_id;

    /**
     * The store associated with this stock.
     *
     * @var Stores
     * @ORM\ManyToOne(targetEntity="Stores", inversedBy="stocks")
     * @ORM\JoinColumn(name="store_id", referencedColumnName="store_id")
     */
    private Stores $store;

    /**
     * The product associated with this stock.
     *
     * @var Products
     * @ORM\ManyToOne(targetEntity="Products", inversedBy="stocks")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="product_id")
     */
    private Products $product;

    /**
     * The quantity of the product in stock.
     *
     * @var int
     * @ORM\Column(type="integer")
     */
    private int $quantity;

    /**
     * Get the value of stock_id.
     *
     * @return int The stock's unique identifier.
     */
    public function getStockId(): int
    {
        return $this->stock_id;
    }

    /**
     * Set the value of stock_id.
     *
     * @param int $stock_id The stock's unique identifier.
     * @return self
     */
    public function setStockId(int $stock_id): self
    {
        $this->stock_id = $stock_id;
        return $this;
    }

    /**
     * Get the store associated with this stock.
     *
     * @return Stores
     */
    public function getStore(): Stores
    {
        return $this->store;
    }

    /**
     * Set the store associated with this stock.
     *
     * @param Stores $store
     * @return self
     */
    public function setStore(Stores $store): self
    {
        $this->store = $store;
        return $this;
    }

    /**
     * Get the product associated with this stock.
     *
     * @return Products
     */
    public function getProduct(): Products
    {
        return $this->product;
    }

    /**
     * Set the product associated with this stock.
     *
     * @param Products $product
     * @return self
     */
    public function setProduct(Products $product): self
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get the quantity of the product in stock.
     *
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * Set the quantity of the product in stock.
     *
     * @param int $quantity
     * @return self
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Serialize the Stocks object to an array for JSON representation.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'stock_id' => $this->getStockId(),
            'store' => [
                'store_id' => $this->getStore()->getStoreId(),
                'store_name' => $this->getStore()->getStoreName()
            ],
            'product' => [
                'product_id' => $this->getProduct()->getProductId(),
                'product_name' => $this->getProduct()->getProductName()
            ],
            'quantity' => $this->getQuantity()
        ];
    }
}