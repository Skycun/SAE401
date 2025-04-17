<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Categories
 *
 * Represents a category entity in the database.
 *
 * @ORM\Entity
 * @ORM\Table(name="categories")
 */
class Categories
{
    /**
     * The unique identifier for the category.
     *
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $category_id;

    /**
     * The name of the category.
     *
     * @var string
     * @ORM\Column(type="string")
     */
    private string $category_name;

    /**
     * The collection of products associated with this category.
     *
     * @var Collection<int, Products>
     * @ORM\OneToMany(targetEntity="Products", mappedBy="category")
     */
    private Collection $products;

    /**
     * Categories constructor.
     * Initializes the products collection.
     */
    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    /**
     * Get the value of category_id.
     *
     * @return int The category's unique identifier.
     */
    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    /**
     * Set the value of category_id.
     *
     * @param int $category_id The category's unique identifier.
     * @return self
     */
    public function setCategoryId(int $category_id): self
    {
        $this->category_id = $category_id;
        return $this;
    }

    /**
     * Get the value of category_name.
     *
     * @return string The name of the category.
     */
    public function getCategoryName(): string
    {
        return $this->category_name;
    }

    /**
     * Set the value of category_name.
     *
     * @param string $category_name The name of the category.
     * @return self
     */
    public function setCategoryName(string $category_name): self
    {
        $this->category_name = $category_name;
        return $this;
    }

    /**
     * Get the collection of products associated with this category.
     *
     * @return Collection<int, Products>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * Serialize the Categories object to an array for JSON representation.
     *
     * @return array
     */
    public function jsonSerialize(){
        $res = [
            "category_id" => $this->getCategoryId(),
            "category_name" => $this->getCategoryName()
        ];
        return $res;
    }
}