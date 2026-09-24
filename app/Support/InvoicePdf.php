<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Turns an invoice snapshot into a PDF (dompdf). Resolved from the container
 * so tests can swap it for a fake.
 */
class InvoicePdf
{
    public function html(array $snapshot): string
    {
        return view('invoices.pdf', [
            's' => $snapshot,
            'logo' => $this->logoDataUri(),
        ])->render();
    }

    public function render(array $snapshot): string
    {
        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            throw new \RuntimeException(
                'The PDF engine is not installed on this server yet. Run: composer require barryvdh/laravel-dompdf:^3.1'
            );
        }

        return \Barryvdh\DomPDF\Facade\Pdf::loadHTML($this->html($snapshot))
            ->setPaper('a4')
            ->output();
    }

    /** 1234567.5 -> "12,34,567.50" - Indian grouping, always two decimals, no symbol. */
    public static function amount(float $value): string
    {
        $negative = $value < 0;
        [$whole, $fraction] = explode('.', number_format(abs($value), 2, '.', ''));

        $last3 = substr($whole, -3);
        $rest = substr($whole, 0, -3);
        $grouped = $rest !== '' ? preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest) . ',' . $last3 : $last3;

        return ($negative ? '-' : '') . $grouped . '.' . $fraction;
    }

    /** The site logo, inlined so the PDF never has to fetch anything. */
    private function logoDataUri(): ?string
    {
        $file = Setting::find(1)?->logo;
        $path = $file ? base_path('public/uploads/images/logo/' . basename($file)) : null;

        if (! $path || ! is_file($path)) {
            return null;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
            $mime = $extension === 'png' ? 'image/png' : 'image/jpeg';

            return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
        }

        // dompdf cannot read WebP; convert it when the server can
        if ($extension === 'webp' && function_exists('imagecreatefromwebp')) {
            $image = @imagecreatefromwebp($path);
            if ($image) {
                ob_start();
                imagepng($image);
                $png = ob_get_clean();
                imagedestroy($image);

                return 'data:image/png;base64,' . base64_encode($png);
            }
        }

        return null;
    }
}
