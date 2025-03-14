<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="stocks")
 */
class Stocks
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $stock_id;

    /**
     * @ORM\ManyToOne(targetEntity="Stores", inversedBy="stocks")
     * @ORM\JoinColumn(name="store_id", referencedColumnName="store_id")
     */
    private Stores $store;

    /**
     * @ORM\ManyToOne(targetEntity="Products", inversedBy="stocks")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="product_id")
     */
    private Products $product;

    /**
     * @ORM\Column(type="integer")
     */
    private int $quantity;

    // Getters and Setters
    public function getStockId(): int
    {
        return $this->stock_id;
    }

    public function setStockId(int $stock_id): self
    {
        $this->stock_id = $stock_id;
        return $this;
    }

    public function getStore(): Stores
    {
        return $this->store;
    }

    public function setStore(Stores $store): self
    {
        $this->store = $store;
        return $this;
    }

    public function getProduct(): Products
    {
        return $this->product;
    }

    public function setProduct(Products $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function jsonSerialize()
    {
        $res = Array(
            "stock_id" => $this->stock_id,
            "store" => $this->store->jsonSerialize(),
            "product" => $this->product->jsonSerialize(),
            "quantity" => $this->quantity
        );
        return $res;
    }
}