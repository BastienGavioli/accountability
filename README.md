# Accountability

> Calculate the minimal set of transfers needed to fairly balance shared expenses across a group.

SplitEase records who paid what, computes each member's net balance, and reduces the
result to the smallest number of money transfers so everyone ends up even.

---

## Features

- Create groups and invite members
- Log expenses with a payer and a set of participants (equal or custom shares)
- View each member's net balance in real time
- Get the **minimum number of suggested transfers** to settle up
- Works without authentication (Phase 1); ready to plug in login later (Phase 2)

---

## How it works

SplitEase solves the classic **minimum cash flow** problem.

1. **Net balance** — For each member: `balance = total paid − total owed`.
2. **Split** balances into creditors (positive) and debtors (negative).
3. **Greedy match** — Pair the largest creditor with the largest debtor:
   `transfer = min(|creditor|, |debtor|)`, then update both balances.
4. **Repeat** until every balance is `0`.

### Worked example

| Person  | Paid  | Share owed | Balance        |
|---------|-------|------------|----------------|
| Alice   | €100  | €50        | **+€50** (creditor) |
| Bob     | €20   | €50        | **−€30** (debtor)   |
| Charlie | €30   | €50        | **−€20** (debtor)   |

**Suggested transfers:**

- Bob → Alice: **€30**
- Charlie → Alice: **€20**

---

## Tech stack

| Layer       | Technology                       |
|-------------|----------------------------------|
| Framework   | Symfony 7.4                      |
| ORM         | Doctrine                         |
| Database    | PostgreSQL                       |
| PHP         | ≥ 8.4                            |
| Tests       | PHPUnit                          |

---

## Installation

```bash
composer install
cp .env                       # set DATABASE_URL
bin/console doctrine:migrations:migrate
symfony serve                 # http://localhost:8000
