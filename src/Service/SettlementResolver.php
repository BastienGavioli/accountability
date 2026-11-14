<?php

namespace App\Service;

class SettlementResolver
{
    /**
     * @param array<int,int> $balances  userId => net balance in cents
     * @return array<int,array{from:int,to:int,amount:int}>  suggested transfers
     */
    public function resolve(array $balances): array
    {
        $creditors = [];  // positive balance (should receive)
        $debtors   = [];  // negative balance (should pay)

        foreach ($balances as $userId => $balance) {
            if ($balance > 0) $creditors[$userId] = $balance;
            elseif ($balance < 0) $debtors[$userId] = -$balance;
        }

        $transfers = [];

        while ($creditors && $debtors) {
            $creditor = array_key_first($creditors);
            $debtor   = array_key_first($debtors);

            $amount = min($creditors[$creditor], $debtors[$debtor]);

            $transfers[] = ['from' => $debtor, 'to' => $creditor, 'amount' => $amount];

            $creditors[$creditor] -= $amount;
            $debtors[$debtor]     -= $amount;

            if ($creditors[$creditor] == 0) unset($creditors[$creditor]);
            if ($debtors[$debtor] == 0)     unset($debtors[$debtor]);
        }

        return $transfers;
    }
}