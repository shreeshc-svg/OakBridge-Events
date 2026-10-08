<?php

namespace App\Support;

use App\Models\Edition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

/** Bulk "move to year", "copy to year" and "delete" for the year-wise admin lists. */
class YearBulk
{
    /** @return array{ids: int[], action: string, edition: ?Edition} */
    public static function validate(Request $request, array $actions = ['move', 'delete']): array
    {
        $data = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
            'action' => ['required', Rule::in($actions)],
            'edition_id' => ['nullable', 'required_if:action,move', 'required_if:action,copy', 'exists:editions,id'],
        ], [
            'ids.required' => 'Tick at least one item first.',
            'edition_id.required_if' => 'Choose the year to move or copy them to.',
        ]);

        return [
            'ids' => array_map('intval', $data['ids']),
            'action' => $data['action'],
            'edition' => isset($data['edition_id']) ? Edition::find($data['edition_id']) : null,
        ];
    }

    public static function message(string $action, int $count, ?Edition $edition, string $noun): string
    {
        $what = $count . ' ' . $noun . ($count === 1 ? '' : 's');

        return match ($action) {
            'move' => $what . ' moved to ' . $edition->year . '.',
            'copy' => $what . ' copied to ' . $edition->year . '.',
            default => $what . ' deleted.',
        };
    }

    /**
     * Copy an uploaded file so a copied item never shares a file with the
     * original (deleting one must not break the other). Returns the new name/path.
     */
    public static function duplicateFile(string $directory, string $file): string
    {
        $source = rtrim($directory, '/') . '/' . $file;
        if (! is_file($source)) {
            return $file;
        }

        $info = pathinfo($file);
        $copy = ($info['dirname'] !== '.' ? $info['dirname'] . '/' : '')
            . 'copy-' . uniqid() . '-' . $info['basename'];
        File::copy($source, rtrim($directory, '/') . '/' . $copy);

        return $copy;
    }
}
