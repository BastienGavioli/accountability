<?php

namespace App\DataFixtures;

use App\Entity\Expense;
use App\Entity\ExpenseShare;
use App\Entity\Group;
use App\Entity\GroupMember;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public const GROUP_REFERENCE = 'test-group';

    public function load(ObjectManager $manager): void
    {
        // Users
        $alice = (new User())->setName('Alice');
        $bob   = (new User())->setName('Bob');
        $carl  = (new User())->setName('Charlie');
        foreach ([$alice, $bob, $carl] as $u) {
            $manager->persist($u);
        }

        // Group + memberships
        $group = (new Group())->setName('Flatmates');
        $manager->persist($group);
        foreach ([$alice, $bob, $carl] as $u) {
            $manager->persist((new GroupMember())->setGroup($group)->setUser($u));
        }

        // Three expenses, each split equally (3 × 5000 = 15000 owed = 15000 paid)
        $this->addExpense($manager, $group, $alice, 10000);
        $this->addExpense($manager, $group, $bob,   2000);
        $this->addExpense($manager, $group, $carl,  3000);

        $manager->flush();

        // Expose the group so the test can fetch it by reference
        $this->addReference(self::GROUP_REFERENCE, $group);
    }

    private function addExpense(ObjectManager $manager, Group $group, User $payer, int $amount): void {
        $participants = $group->getMembers();
        $expense = (new Expense())
            ->setGroup($group)
            ->setPayer($payer)
            ->setAmount($amount)
            ->setDescription('shared expense');
        $manager->persist($expense);

        // equal split in integer cents, remainder to the payer
        $count = count($participants);
        $base  = intdiv($amount, $count);
        $remainder = $amount - $base * $count;

        foreach ($participants as $p) {
            $owed = $base + ($p === $payer ? $remainder : 0);
            $manager->persist(
                (new ExpenseShare())->setExpense($expense)->setUser($p)->setAmountOwed($owed)
            );
        }
    }
}