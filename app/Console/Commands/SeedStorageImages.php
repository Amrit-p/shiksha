<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

class SeedStorageImages extends Command
{
    protected $signature = 'images:seed
                            {--force : Overwrite images that already exist in storage}
                            {--copy-public : Also copy into public/storage as real files, for hosts that disallow symlinks}';

    protected $description = 'Seed the bundled product, category, banner and brand images into storage/app/public';

    public function handle(): int
    {
        $source = database_path('seed-assets');
        $target = storage_path('app/public');

        if (! is_dir($source)) {
            $this->error("Seed assets not found at {$source}");
            $this->line('Make sure database/seed-assets/ was pulled from git.');

            return self::FAILURE;
        }

        $files = iterator_to_array(Finder::create()->files()->in($source)->sortByName(), false);

        if ($files === []) {
            $this->error("No files found in {$source}");

            return self::FAILURE;
        }

        $copied = $skipped = $failed = 0;
        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        foreach ($files as $file) {
            $relative = str_replace(DIRECTORY_SEPARATOR, '/', $file->getRelativePathname());
            $destination = $target.'/'.$relative;

            if (file_exists($destination) && ! $this->option('force')) {
                $skipped++;
                $bar->advance();

                continue;
            }

            if (! is_dir(dirname($destination))) {
                @mkdir(dirname($destination), 0775, true);
            }

            if (@copy($file->getRealPath(), $destination)) {
                $copied++;
            } else {
                $failed++;
                $this->newLine();
                $this->warn("Could not write {$relative}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Copied {$copied}, skipped {$skipped}, failed {$failed}.");

        if ($skipped > 0 && ! $this->option('force')) {
            $this->line('Re-run with --force to overwrite the skipped files.');
        }

        $this->ensurePublicAccess();

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }

    /**
     * The files can all be present and still serve 403/404 without public/storage.
     */
    private function ensurePublicAccess(): void
    {
        $link = public_path('storage');

        if ($this->option('copy-public')) {
            $this->mirrorToPublic($link);

            return;
        }

        if ($this->linkExists($link)) {
            if ($this->isRealDirectory($link)) {
                $this->warn('public/storage is a real directory, not a symlink.');
                $this->line('That is a common cause of 403 errors. Either delete it and run');
                $this->line('"php artisan storage:link", or re-run this command with --copy-public.');
            } else {
                $this->info('public/storage is in place.');
            }

            return;
        }

        $this->line('public/storage is missing, creating the symlink...');
        $this->callSilent('storage:link');

        if ($this->linkExists($link)) {
            $this->info('public/storage symlink created.');
        } else {
            $this->warn('Could not create the symlink. Re-run with --copy-public instead.');
        }
    }

    private function linkExists(string $link): bool
    {
        clearstatcache(true, $link);

        // is_link() and is_dir() both report false for some Windows links, so test broadly.
        return is_link($link) || is_dir($link) || file_exists($link) || @readlink($link) !== false;
    }

    private function isRealDirectory(string $link): bool
    {
        return is_dir($link) && ! is_link($link) && @readlink($link) === false;
    }

    /**
     * Fallback for hosts where symlinks are unavailable.
     */
    private function mirrorToPublic(string $link): void
    {
        $source = storage_path('app/public');
        $count = 0;

        foreach (Finder::create()->files()->in($source) as $file) {
            $relative = str_replace(DIRECTORY_SEPARATOR, '/', $file->getRelativePathname());
            $destination = $link.'/'.$relative;

            if (! is_dir(dirname($destination))) {
                @mkdir(dirname($destination), 0775, true);
            }

            if (@copy($file->getRealPath(), $destination)) {
                $count++;
            }
        }

        $this->info("Mirrored {$count} files into public/storage.");
    }
}
