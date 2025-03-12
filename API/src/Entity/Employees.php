<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="employees")
 */
class Employees
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $employees_id;

    /**
     * @ORM\ManyToOne(targetEntity="Stores", inversedBy="employees")
     * @ORM\JoinColumn(name="store_id", referencedColumnName="store_id", nullable=true)
     */
    private ?Stores $store = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $employees_name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $employees_email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $employees_password;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private string $employees_role;

    /**
     * Get the value of employees_id
     */ 
    public function getEmployeesId(): int
    {
        return $this->employees_id;
    }

    /**
     * Set the value of employees_id
     *
     * @return  self
     */ 
    public function setEmployeesId(int $employees_id): self
    {
        $this->employees_id = $employees_id;
        return $this;
    }

    /**
     * Get the value of store_id
     */ 
    public function getStoreId(): ?int
    {
        return $this->store_id;
    }

    /**
     * Set the value of store_id
     *
     * @return  self
     */ 
    public function setStoreId(?int $store_id): self
    {
        $this->store_id = $store_id;
        return $this;
    }

    /**
     * Get the value of employees_name
     */ 
    public function getEmployeesName(): string
    {
        return $this->employees_name;
    }

    /**
     * Set the value of employees_name
     *
     * @return  self
     */ 
    public function setEmployeesName(string $employees_name): self
    {
        $this->employees_name = $employees_name;
        return $this;
    }

    /**
     * Get the value of employees_email
     */ 
    public function getEmployeesEmail(): string
    {
        return $this->employees_email;
    }

    /**
     * Set the value of employees_email
     *
     * @return  self
     */ 
    public function setEmployeesEmail(string $employees_email): self
    {
        $this->employees_email = $employees_email;
        return $this;
    }

    /**
     * Get the value of employees_password
     */ 
    public function getEmployeesPassword(): string
    {
        return $this->employees_password;
    }

    /**
     * Set the value of employees_password
     *
     * @return  self
     */ 
    public function setEmployeesPassword(string $employees_password): self
    {
        $this->employees_password = $employees_password;
        return $this;
    }

    /**
     * Get the value of employees_role
     */ 
    public function getEmployeesRole(): string
    {
        return $this->employees_role;
    }

    /**
     * Set the value of employees_role
     *
     * @return  self
     */ 
    public function setEmployeesRole(string $employees_role): self
    {
        $this->employees_role = $employees_role;
        return $this;
    }

    /**
     * Get the store
     */ 
    public function getStore(): ?Stores
    {
        return $this->store;
    }

    /**
     * Set the store
     *
     * @param Stores|null $store
     * @return self
     */ 
    public function setStore(?Stores $store): self
    {
        $this->store = $store;
        return $this;
    }
}