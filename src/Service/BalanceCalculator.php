<?php

namespace App\Service;

use App\Entity\Group;

class BalanceCalculator {

    /**
     * Summary of compute
     * @param Group $group
     * @return array<int,int>  userId => net balance in cents (positive = is owed money)
     */
    public function compute(Group $group): array
    {
        $balances = [];

        // start every member at 0
        foreach ($group->getMembers() as $member) {
            $balances[$member->getUser()->getId()] = 0;
        }

        foreach ($group->getExpenses() as $expense) {
            // payer gets credit for the full amount
            $balances[$expense->getPayer()->getId()] += $expense->getAmount();

            // each participant's share is subtracted
            foreach ($expense->getShares() as $share) {
                $balances[$share->getUser()->getId()] -= $share->getAmountOwed();
            }
        }

        return $balances;
    }
}