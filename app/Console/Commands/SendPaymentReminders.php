<?php

namespace App\Console\Commands;

use App\Support\PaymentReminders;
use Illuminate\Console\Command;

/**
 * Chases unpaid orders: one reminder a day after the order, another at three
 * days, a last one at a week. Run it hourly from cron.
 */
class SendPaymentReminders extends Command
{
    protected $signature = 'orders:remind {--pretend : List who would be emailed without sending anything}';

    protected $description = 'Email buyers whose orders are still waiting for payment';

    public function handle(): int
    {
        $due = PaymentReminders::dueOrders();

        if ($due->isEmpty()) {
            $this->info('No orders are due a reminder.');

            return self::SUCCESS;
        }

        $sent = 0;
        $failed = 0;

        foreach ($due as $order) {
            $stage = PaymentReminders::dueStage($order);

            if ($stage === null) {
                continue;
            }

            if ($this->option('pretend')) {
                $this->line(sprintf('would send stage %d to %s (%s)', $stage, $order->buyer_email, $order->order_no));
                continue;
            }

            $reminder = PaymentReminders::send($order, 'auto', $stage);

            if ($reminder->failed) {
                $failed++;
                $this->error(sprintf('%s -> %s FAILED: %s', $order->order_no, $order->buyer_email, $reminder->error));
            } else {
                $sent++;
                $this->line(sprintf('%s -> %s (stage %d)', $order->order_no, $order->buyer_email, $stage));
            }
        }

        if ($this->option('pretend')) {
            $this->info($due->count() . ' order(s) are due a reminder.');

            return self::SUCCESS;
        }

        $this->info($sent . ' reminder(s) sent' . ($failed ? ', ' . $failed . ' failed' : '') . '.');

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
