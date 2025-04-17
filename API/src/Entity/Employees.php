<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Employees
 *
 * Represents an employee entity in the database.
 *
 * @ORM\Entity
 * @ORM\Table(name="employees")
 */
class Employees
{
    /**
     * The unique identifier for the employee.
     *
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $employees_id;

    /**
     * The store associated with the employee.
     *
     * @var Stores|null
     * @ORM\ManyToOne(targetEntity="Stores", inversedBy="employees")
     * @ORM\JoinColumn(name="store_id", referencedColumnName="store_id", nullable=true)
     */
    private ?Stores $store = null;

    /**
     * The name of the employee.
     *
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private string $employees_name;

    /**
     * The email of the employee.
     *
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $employees_email;

    /**
     * The password of the employee.
     *
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private string $employees_password;

    /**
     * The role of the employee (e.g., employee, chief, it).
     *
     * @var string
     * @ORM\Column(type="string", length=50)
     */
    private string $employees_role;

    /**
     * Get the value of employees_id.
     *
     * @return int The employee's unique identifier.
     */
    public function getEmployeesId(): int
    {
        return $this->employees_id;
    }

    /**
     * Set the value of employees_id.
     *
     * @param int $employees_id The employee's unique identifier.
     * @return self
     */
    public function setEmployeesId(int $employees_id): self
    {
        $this->employees_id = $employees_id;
        return $this;
    }

    /**
     * Get the store id.
     *
     * @return int|null The store's unique identifier.
     */
    public function getStoreId(): ?int
    {
        return $this->store_id;
    }

    /**
     * Set the store id.
     *
     * @param int|null $store_id The store's unique identifier.
     * @return self
     */
    public function setStoreId(?int $store_id): self
    {
        $this->store_id = $store_id;
        return $this;
    }

    /**
     * Get the name of the employee.
     *
     * @return string
     */
    public function getEmployeesName(): string
    {
        return $this->employees_name;
    }

    /**
     * Set the name of the employee.
     *
     * @param string $employees_name
     * @return self
     */
    public function setEmployeesName(string $employees_name): self
    {
        $this->employees_name = $employees_name;
        return $this;
    }

    /**
     * Get the email of the employee.
     *
     * @return string
     */
    public function getEmployeesEmail(): string
    {
        return $this->employees_email;
    }

    /**
     * Set the email of the employee.
     *
     * @param string $employees_email
     * @return self
     */
    public function setEmployeesEmail(string $employees_email): self
    {
        $this->employees_email = $employees_email;
        return $this;
    }

    /**
     * Get the password of the employee.
     *
     * @return string
     */
    public function getEmployeesPassword(): string
    {
        return $this->employees_password;
    }

    /**
     * Set the password of the employee.
     *
     * @param string $employees_password
     * @return self
     */
    public function setEmployeesPassword(string $employees_password): self
    {
        $this->employees_password = $employees_password;
        return $this;
    }

    /**
     * Get the role of the employee.
     *
     * @return string
     */
    public function getEmployeesRole(): string
    {
        return $this->employees_role;
    }

    /**
     * Set the role of the employee.
     *
     * @param string $employees_role
     * @return self
     */
    public function setEmployeesRole(string $employees_role): self
    {
        $this->employees_role = $employees_role;
        return $this;
    }

    /**
     * Get the store entity associated with the employee.
     *
     * @return Stores|null
     */
    public function getStore(): ?Stores
    {
        return $this->store;
    }

    /**
     * Set the store entity associated with the employee.
     *
     * @param Stores|null $store
     * @return self
     */
    public function setStore(?Stores $store): self
    {
        $this->store = $store;
        return $this;
    }

    /**
     * Serialize the Employees object to an array for JSON representation.
     *
     * @return array
     */
    public function jsonSerialize(){
        $res = [
            'employees_id' => $this->getEmployeesId(),
            'store' => $this->getStore() ? $this->getStore()->jsonSerialize() : null,
            'employees_name' => $this->getEmployeesName(),
            'employees_email' => $this->getEmployeesEmail(),
            'employees_password' => $this->getEmployeesPassword(),
            'employees_role' => $this->getEmployeesRole()
        ];
        return $res;
    }
}