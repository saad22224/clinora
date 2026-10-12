<?php

namespace App\Support\Qa;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

class QaManifest
{
    public const STORAGE_DIR = 'qa-manifests';

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function makeSkeleton(
        string $profile,
        ?int $seed = null,
        array $attributes = [],
    ): array {
        $runId = 'qa_'.now()->format('Ymd_His').'_'.Str::lower(Str::random(8));

        return array_merge([
            'run_id' => $runId,
            'created_at' => now()->toIso8601String(),
            'profile' => $profile,
            'seed' => $seed,
            'dry_run' => true,
            'app_env' => app()->environment(),
            'database' => [
                'connection' => config('database.default'),
                'name' => config('database.connections.'.config('database.default').'.database'),
            ],
            'accounts' => [],
            'records' => [],
            'scenarios' => [],
            'expected_finance' => [
                'note' => 'Populated in a later phase with independently calculated totals.',
                'totals' => [],
            ],
            'private_files' => [],
            'meta' => [
                'phase' => '2A-scaffold',
                'generator_version' => 1,
            ],
        ], $attributes);
    }

    public function directoryPath(): string
    {
        return storage_path('app/'.self::STORAGE_DIR);
    }

    public function pathFor(string $runId): string
    {
        $safeRunId = Str::of($runId)->replaceMatches('/[^A-Za-z0-9_\-]/', '')->toString();

        if ($safeRunId === '') {
            throw new RuntimeException('Invalid QA manifest run id.');
        }

        return $this->directoryPath().DIRECTORY_SEPARATOR.$safeRunId.'.json';
    }

    /**
     * @param  array<string, mixed>  $manifest
     */
    public function write(array $manifest): string
    {
        $runId = (string) ($manifest['run_id'] ?? '');
        if ($runId === '') {
            throw new RuntimeException('Cannot write QA manifest without run_id.');
        }

        File::ensureDirectoryExists($this->directoryPath());

        $path = $this->pathFor($runId);
        File::put($path, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."\n");

        return $path;
    }

    /**
     * @return array<string, mixed>
     */
    public function read(string $runId): array
    {
        $path = $this->pathFor($runId);

        if (! File::exists($path)) {
            throw new RuntimeException("QA manifest not found for run [{$runId}].");
        }

        /** @var array<string, mixed>|null $decoded */
        $decoded = json_decode(File::get($path), true);

        if (! is_array($decoded)) {
            throw new RuntimeException("QA manifest [{$runId}] is unreadable.");
        }

        return $decoded;
    }

    public function latestRunId(): ?string
    {
        if (! File::isDirectory($this->directoryPath())) {
            return null;
        }

        $files = collect(File::files($this->directoryPath()))
            ->filter(fn ($file) => $file->getExtension() === 'json')
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->values();

        if ($files->isEmpty()) {
            return null;
        }

        return $files->first()->getFilenameWithoutExtension();
    }
}
