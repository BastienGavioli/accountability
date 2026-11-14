<?php

namespace App\Tests\Unit;

use App\Service\SettlementResolver;
use PHPUnit\Framework\TestCase;

class SettlementResolverTest extends TestCase
{
    public function testItMinimizesTransfers(): void
    {
        $resolver = new SettlementResolver();

        // Alice +50, Bob -30, Charlie -20  (in cents: 5000 / -3000 / -2000)
        $transfers = $resolver->resolve([1 => 5000, 2 => -3000, 3 => -2000]);

        $this->assertCount(2, $transfers);
        $this->assertSame(3000, $transfers[0]['amount']);
        $this->assertSame(2000, $transfers[1]['amount']);
    }

    public function testBalancedGroupProducesNoTransfers(): void
    {
        $resolver = new SettlementResolver();
        $this->assertSame([], $resolver->resolve([1 => 0, 2 => 0]));
    }
}