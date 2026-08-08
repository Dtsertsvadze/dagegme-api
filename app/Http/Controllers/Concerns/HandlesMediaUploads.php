<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait HandlesMediaUploads
{
    protected function resolveSingleMediaPath(
        Request $request,
        string $field,
        string $directory,
        ?string $currentValue = null
    ): ?string {
        if ($request->hasFile($field)) {
            $request->validate([
                $field => ['image'],
            ]);

            return $request->file($field)->store(
                $directory,
                (string) config('media.disk')
            );
        }

        $value = $request->input($field);

        if ($value === null) {
            return $currentValue;
        }

        return is_string($value) ? $value : $currentValue;
    }

    /**
     * @return list<string>
     */
    protected function resolveMultipleMediaPaths(
        Request $request,
        string $field,
        string $directory
    ): array {
        if ($request->hasFile($field)) {
            $request->validate([
                $field => ['array'],
                $field.'.*' => ['image'],
            ]);

            return collect($request->file($field))
                ->map(fn ($file): string => $file->store(
                    $directory,
                    (string) config('media.disk')
                ))
                ->all();
        }

        $value = $request->input($field);

        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->filter(fn ($item): bool => is_string($item) && $item !== '')
            ->values()
            ->all();
    }
}
