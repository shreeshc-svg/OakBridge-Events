<?php

namespace App\Support;

/**
 * Event sessions stored in services.timeline (JSON).
 *
 * The original format was {from, to, title, subheadline, body} where body is
 * "Name, Designation; Name, Designation". Sessions saved from
 * Admin > Events > Schedules also carry `speakers` ([name, role] pairs) and
 * `notes`; `body` is still written so older screens keep working.
 */
class Schedule
{
    public const MAX_SESSIONS = 60;

    public static function sessions(?string $json): array
    {
        $rows = json_decode((string) $json, true);
        if (! is_array($rows)) {
            return [];
        }

        $sessions = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $session = [
                'from' => trim((string) ($row['from'] ?? '')),
                'to' => trim((string) ($row['to'] ?? '')),
                'title' => trim((string) ($row['title'] ?? '')),
                'subheadline' => trim((string) ($row['subheadline'] ?? '')),
                'legacy' => ! array_key_exists('speakers', $row),
                'body' => (string) ($row['body'] ?? ''),
            ];
            if ($session['legacy']) {
                [$session['speakers'], $session['notes']] = self::parseLegacyBody($session['body']);
            } else {
                $session['speakers'] = array_values(array_filter(array_map(fn ($s) => [
                    'name' => trim((string) ($s['name'] ?? '')),
                    'role' => trim((string) ($s['role'] ?? '')),
                ], (array) $row['speakers']), fn ($s) => $s['name'] !== '' || $s['role'] !== ''));
                $session['notes'] = trim((string) ($row['notes'] ?? ''));
            }
            $sessions[] = $session;
        }

        return $sessions;
    }

    /** "Name, Role; Name, Role" (or | / new-line separated) => [speakers, notes] */
    public static function parseLegacyBody(string $body): array
    {
        $speakers = [];
        $notes = [];
        foreach (preg_split('/[;|\r\n]+/u', $body) as $piece) {
            $piece = trim(preg_replace('/^[\s–—-]+/u', '', $piece));
            if ($piece === '') {
                continue;
            }
            if (str_contains($piece, ',')) {
                [$name, $role] = array_map('trim', explode(',', $piece, 2));
                $speakers[] = ['name' => $name, 'role' => $role];
            } else {
                $notes[] = $piece;
            }
        }

        return [$speakers, implode("\n", $notes)];
    }

    /** One speaker per line as "Name, Role" (for the editor). */
    public static function speakersToText(array $speakers): string
    {
        return implode("\n", array_map(
            fn ($s) => $s['role'] !== '' ? $s['name'] . ', ' . $s['role'] : $s['name'],
            $speakers
        ));
    }

    public static function textToSpeakers(string $text): array
    {
        $speakers = [];
        foreach (preg_split('/\R/u', $text) as $line) {
            $line = trim(preg_replace('/^[\s–—•*-]+/u', '', $line));
            if ($line === '') {
                continue;
            }
            [$name, $role] = array_pad(array_map('trim', explode(',', $line, 2)), 2, '');
            $speakers[] = ['name' => $name, 'role' => $role];
        }

        return $speakers;
    }

    /** Sessions from the editor => JSON for services.timeline */
    public static function toJson(array $sessions): string
    {
        $rows = [];
        foreach ($sessions as $s) {
            $rows[] = [
                'from' => $s['from'],
                'to' => $s['to'],
                'title' => $s['title'],
                'subheadline' => $s['subheadline'],
                'speakers' => $s['speakers'],
                'notes' => $s['notes'],
                // kept for older screens
                'body' => implode('; ', array_map(
                    fn ($sp) => $sp['role'] !== '' ? $sp['name'] . ', ' . $sp['role'] : $sp['name'],
                    $s['speakers']
                )) ?: $s['notes'],
            ];
        }

        return json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /** "09:30" => "9:30 AM" */
    public static function formatTime(?string $time): ?string
    {
        if (! $time || ! preg_match('/^(\d{1,2}):(\d{2})/', $time, $m)) {
            return null;
        }

        return \Carbon\Carbon::createFromTime((int) $m[1], (int) $m[2])->format('g:i A');
    }
}
