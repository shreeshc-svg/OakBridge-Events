<?php

namespace App\Console\Commands;

use App\Support\BookingNumber;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Empty the registrations (bookings) table to start a new season.
 * Always saves a CSV backup first, and keeps the booking number sequence.
 */
class ClearRegistrations extends Command
{
    protected $signature = 'registrations:clear
        {--force : Skip the confirmation question}';

    protected $description = 'Back up all registrations to a CSV file, then delete them';

    public function handle(): int
    {
        if (! Schema::hasColumn('settings', 'booking_last_number')) {
            $this->error('Run "php artisan migrate --force" first.');

            return self::FAILURE;
        }

        $total = DB::table('bookings')->count();
        if ($total === 0) {
            $this->info('There are no registrations to clear.');

            return self::SUCCESS;
        }

        // 1. backup
        $dir = storage_path('app/backups');
        if (! is_dir($dir)) {
            mkdir($dir, 0750, true);
        }
        if (! file_exists($dir . '/.htaccess')) {
            file_put_contents($dir . '/.htaccess', "Require all denied\nDeny from all\n");
        }
        $file = $dir . '/registrations-' . now()->format('Y-m-d-His') . '.csv';
        $written = $this->export($file);
        if ($written !== $total) {
            $this->error("Backup incomplete ({$written} of {$total} rows). Nothing was deleted.");

            return self::FAILURE;
        }
        $relative = 'storage/app/backups/' . basename($file);
        $this->info("Backup saved: {$relative} ({$written} registrations)");

        // 2. confirm
        $first = DB::table('bookings')->min('created_at');
        $last = DB::table('bookings')->max('created_at');
        $this->line("Registrations from {$first} to {$last}.");
        if (! $this->option('force')
            && ! $this->confirm("Permanently delete all {$total} registrations from the website?", false)) {
            $this->warn('Cancelled. Nothing was deleted (the backup file is still there).');

            return self::SUCCESS;
        }

        // 3. remember the highest booking number, then empty the table
        $highest = BookingNumber::highestInBookings();
        $stored = (int) DB::table('settings')->where('id', 1)->value('booking_last_number');
        $keep = max($stored, (int) $highest);
        if ($keep > 0) {
            DB::table('settings')->where('id', 1)->update(['booking_last_number' => $keep]);
        }
        DB::table('bookings')->truncate();

        $this->info("Deleted {$total} registrations.");
        $this->line('Next booking ID will be ' . BookingNumber::format($keep > 0 ? $keep + 1 : BookingNumber::FIRST) . '.');

        return self::SUCCESS;
    }

    private function export(string $file): int
    {
        $handle = fopen($file, 'w');
        fwrite($handle, "\xEF\xBB\xBF"); // so Excel reads names correctly
        $columns = Schema::getColumnListing('bookings');
        fputcsv($handle, $columns);
        $rows = 0;
        DB::table('bookings')->orderBy('id')->chunk(500, function ($chunk) use ($handle, $columns, &$rows) {
            foreach ($chunk as $row) {
                $row = (array) $row;
                fputcsv($handle, array_map(function ($column) use ($row) {
                    $value = (string) ($row[$column] ?? '');

                    // stop Excel treating a value as a formula
                    return preg_match('/^[=+\-@]/', $value) ? "'" . $value : $value;
                }, $columns));
                $rows++;
            }
        });
        fclose($handle);
        chmod($file, 0640);

        return $rows;
    }
}
