<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Support\OrderFulfiller;
use Illuminate\Console\Command;

/**
 * One-off: buyers of single-pass orders received a payment confirmation without their
 * pass number. This emails them the corrected confirmation (buyer only, no admin copy).
 */
class ResendPasses extends Command
{
    protected $signature = 'orders:resend-passes
        {--all : every paid order, not only single-pass ones}
        {--since= : only orders paid on or after this date (YYYY-MM-DD)}
        {--dry-run : list who would get it, send nothing}';

    protected $description = 'Email paid buyers their pass confirmation again (with the pass numbers)';

    public function handle(): int
    {
        $orders = Order::where('status', 'paid')
            ->whereHas('bookings')
            ->when(! $this->option('all'), fn ($q) => $q->where('quantity', 1))
            ->when($this->option('since'), fn ($q) => $q->whereDate('paid_at', '>=', $this->option('since')))
            ->orderBy('id')
            ->get();

        $this->info($orders->count() . ' paid ' . ($this->option('all') ? '' : 'single-pass ') . 'orders.');
        foreach ($orders as $order) {
            $this->line($order->order_no . '  ' . $order->buyer_email . '  ' . $order->bookings()->pluck('booking_id')->implode(', '));
        }
        if ($this->option('dry-run') || $orders->isEmpty()) {
            $this->warn($this->option('dry-run') ? 'Dry run – nothing sent.' : 'Nothing to send.');

            return self::SUCCESS;
        }
        if (! $this->confirm('Email these ' . $orders->count() . ' buyers their pass confirmation again?', true)) {
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($orders as $order) {
            if (OrderFulfiller::resendPass($order)) {
                $sent++;
            } else {
                $this->error('Not sent: ' . $order->order_no);
            }
        }
        $this->info($sent . ' sent.');

        return self::SUCCESS;
    }
}
