<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PersonService
{
    private string $file = 'persons.json';

    // ─── READ ────────────────────────────────────────────────

    public function all(): array
    {
        $this->ensureFileExists();
        $data = json_decode(Storage::get($this->file), true) ?? [];

        // Always return in descending order (newest first)
        usort($data, fn($a, $b) => $b['sn'] <=> $a['sn']);

        return $data;
    }

    public function find(string $id): ?array
    {
        $this->ensureFileExists();
        $data = json_decode(Storage::get($this->file), true) ?? [];

        foreach ($data as $person) {
            if ($person['id'] === $id) return $person;
        }
        return null;
    }

    // ─── WRITE ───────────────────────────────────────────────

    public function add(array $person): array
    {
        $this->ensureFileExists();
        $data = json_decode(Storage::get($this->file), true) ?? [];

        $person['sn']         = $this->nextSn($data);
        $person['id']         = $this->generateUniqueId($data);
        $person['created_at'] = now()->toDateTimeString();
        $person['updated_at'] = now()->toDateTimeString();

        $data[] = $person;

        $this->save($data);

        return $person;
    }

    public function update(string $id, array $updates): ?array
    {
        $this->ensureFileExists();
        $data = json_decode(Storage::get($this->file), true) ?? [];

        foreach ($data as &$person) {
            if ($person['id'] === $id) {
                $updates['updated_at'] = now()->toDateTimeString();
                $person = array_merge($person, $updates);
                $this->save($data);
                return $person;
            }
        }

        return null;
    }

    public function delete(string $id): bool
    {
        $this->ensureFileExists();
        $data     = json_decode(Storage::get($this->file), true) ?? [];
        $filtered = array_filter($data, fn($p) => $p['id'] !== $id);

        if (count($filtered) === count($data)) return false;

        $this->save(array_values($filtered));
        return true;
    }

    public function bulkDelete(array $ids): int
    {
        $this->ensureFileExists();
        $data     = json_decode(Storage::get($this->file), true) ?? [];
        $before   = count($data);
        $filtered = array_filter($data, fn($p) => !in_array($p['id'], $ids));

        $this->save(array_values($filtered));

        return $before - count($filtered);
    }

    // ─── HELPERS ─────────────────────────────────────────────

    private function ensureFileExists(): void
    {
        if (!Storage::exists($this->file)) {
            Storage::put($this->file, '[]');
        }
    }

    private function save(array $data): void
    {
        Storage::put($this->file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function nextSn(array $data): int
    {
        if (empty($data)) return 1;
        return max(array_column($data, 'sn')) + 1;
    }

    private function generateUniqueId(array $data): string
    {
        $existing = array_column($data, 'id');
        do {
            $id = (string) random_int(100000, 999999);
        } while (in_array($id, $existing));
        return $id;
    }
}