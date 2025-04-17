<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Stores
 *
 * Represents a store entity in the database.
 *
 * @ORM\Entity
 * @ORM\Table(name="stores")
 */
class Stores
{
    /**
     * The unique identifier for the store.
     *
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $store_id;

    /**
     * The name of the store.
     *
     * @var string
     * @ORM\Column(type="string")
     */
    private string $store_name;

    /**
     * The phone number of the store.
     *
     * @var string
     * @ORM\Column(type="string")
     */
    private string $phone;

    /**
     * The email address of the store.
     *
     * @var string
     * @ORM\Column(type="string")
     */
    private string $email;

    /**
     * The street address of the store.
     *
     * @var string
     * @ORM\Column(type="string")
     */
    private string $street;

    /**
     * The city where the store is located.
     *
     * @var string
     * @ORM\Column(type="string")
     */
    private string $city;

    /**
     * The state where the store is located.
     *
     * @var string
     * @ORM\Column(type="string")
     */
    private string $state;

    /**
     * The zip code of the store.
     *
     * @var string
     * @ORM\Column(type="string")
     */
    private string $zip_code;

    /**
     * The collection of employees working at this store.
     *
     * @var Collection<int, Employees>
     * @ORM\OneToMany(targetEntity="Employees", mappedBy="store")
     */
    private Collection $employees;

    /**
     * The collection of stocks associated with this store.
     *
     * @var Collection<int, Stocks>
     * @ORM\OneToMany(targetEntity="Stocks", mappedBy="store")
     */
    private Collection $stocks;

    /**
     * Stores constructor.
     * Initializes the employees and stocks collections.
     */
    public function __construct()
    {
        $this->employees = new ArrayCollection();
        $this->stocks = new ArrayCollection();
    }

    /**
     * Get the value of store_id.
     *
     * @return int
     */
    public function getStoreId(): int
    {
        return $this->store_id;
    }

    /**
     * Set the value of store_id.
     *
     * @param int $store_id
     * @return self
     */
    public function setStoreId(int $store_id): self
    {
        $this->store_id = $store_id;
        return $this;
    }

    /**
     * Get the value of store_name.
     *
     * @return string
     */
    public function getStoreName(): string
    {
        return $this->store_name;
    }

    /**
     * Set the value of store_name.
     *
     * @param string $store_name
     * @return self
     */
    public function setStoreName(string $store_name): self
    {
        $this->store_name = $store_name;
        return $this;
    }

    /**
     * Get the value of phone.
     *
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * Set the value of phone.
     *
     * @param string $phone
     * @return self
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Get the value of email.
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set the value of email.
     *
     * @param string $email
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get the value of street.
     *
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * Set the value of street.
     *
     * @param string $street
     * @return self
     */
    public function setStreet(string $street): self
    {
        $this->street = $street;
        return $this;
    }

    /**
     * Get the value of city.
     *
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * Set the value of city.
     *
     * @param string $city
     * @return self
     */
    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Get the value of state.
     *
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * Set the value of state.
     *
     * @param string $state
     * @return self
     */
    public function setState(string $state): self
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Get the value of zip_code.
     *
     * @return string
     */
    public function getZipCode(): string
    {
        return $this->zip_code;
    }

    /**
     * Set the value of zip_code.
     *
     * @param string $zip_code
     * @return self
     */
    public function setZipCode(string $zip_code): self
    {
        $this->zip_code = $zip_code;
        return $this;
    }

    /**
     * Get the collection of employees working at this store.
     *
     * @return Collection<int, Employees>
     */
    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    /**
     * Add an employee to the store.
     *
     * @param Employees $employee
     * @return self
     */
    public function addEmployee(Employees $employee): self
    {
        if (!$this->employees->contains($employee)) {
            $this->employees[] = $employee;
            $employee->setStore($this);
        }
        return $this;
    }

    /**
     * Remove an employee from the store.
     *
     * @param Employees $employee
     * @return self
     */
    public function removeEmployee(Employees $employee): self
    {
        if ($this->employees->removeElement($employee)) {
            // set the owning side to null (unless already changed)
            if ($employee->getStore() === $this) {
                $employee->setStore(null);
            }
        }
        return $this;
    }

    /**
     * Get the collection of stocks associated with this store.
     *
     * @return Collection<int, Stocks>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    /**
     * Add a stock to the store.
     *
     * @param Stocks $stock
     * @return self
     */
    public function addStock(Stocks $stock): self
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks[] = $stock;
            $stock->setStore($this);
        }
        return $this;
    }

    /**
     * Remove a stock from the store.
     *
     * @param Stocks $stock
     * @return self
     */
    public function removeStock(Stocks $stock): self
    {
        if ($this->stocks->removeElement($stock)) {
            if ($stock->getStore() === $this) {
                $stock->setStore(null);
            }
        }
        return $this;
    }

    /**
     * Serialize the Stores object to an array for JSON representation.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $res = [
            'store_id' => $this->getStoreId(),
            'store_name' => $this->getStoreName(),
            'phone' => $this->getPhone(),
            'email' => $this->getEmail(),
            'street' => $this->getStreet(),
            'city' => $this->getCity(),
            'state' => $this->getState(),
            'zip_code' => $this->getZipCode()
        ];
        return $res;
    }
}