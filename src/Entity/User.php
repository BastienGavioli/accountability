<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, GroupMember>
     */
    #[ORM\OneToMany(targetEntity: GroupMember::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $membership;

    /**
     * @var Collection<int, Expense>
     */
    #[ORM\OneToMany(targetEntity: Expense::class, mappedBy: 'payer')]
    private Collection $paidExpenses;

    public function __construct()
    {
        $this->membership = new ArrayCollection();
        $this->paidExpenses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, GroupMember>
     */
    public function getMembership(): Collection
    {
        return $this->membership;
    }

    public function addMembership(GroupMember $membership): static
    {
        if (!$this->membership->contains($membership)) {
            $this->membership->add($membership);
            $membership->setUser($this);
        }

        return $this;
    }

    public function removeMembership(GroupMember $membership): static
    {
        if ($this->membership->removeElement($membership)) {
            // set the owning side to null (unless already changed)
            if ($membership->getUser() === $this) {
                $membership->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Expense>
     */
    public function getPaidExpenses(): Collection
    {
        return $this->paidExpenses;
    }

    public function addPaidExpense(Expense $paidExpense): static
    {
        if (!$this->paidExpenses->contains($paidExpense)) {
            $this->paidExpenses->add($paidExpense);
            $paidExpense->setPayer($this);
        }

        return $this;
    }

    public function removePaidExpense(Expense $paidExpense): static
    {
        if ($this->paidExpenses->removeElement($paidExpense)) {
            // set the owning side to null (unless already changed)
            if ($paidExpense->getPayer() === $this) {
                $paidExpense->setPayer(null);
            }
        }

        return $this;
    }
}
