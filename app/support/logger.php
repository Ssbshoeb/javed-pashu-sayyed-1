<?php
/*
|--------------------------------------------------------------------------
| Logging
|--------------------------------------------------------------------------
| The application log, written to storage/logs/app-YYYY-MM.log.
|
| One file per month rather than one per day, so a chamber website that gets a
| handful of submissions a week does not accumulate hundreds of near-empty
| files, and per month rather than one file for ever so the log can be pruned
| without truncating the one being written to.
|
| Nothing personal is written here. Submissions are not logged; only the fact
| that a delivery succeeded or failed, and the anonymised disclaimer records
| the privacy notice describes.
*/

defined('APP_RUNNING') or exit('No direct access.');

function log_write(string $level, string $message, array $context = []): void
{
    $directory = STORAGE_PATH . '/logs';

    if (!is_dir($directory)) {
        // Recursive, and failure is swallowed: a log that cannot be written must
        // never take the site down with it.
        @mkdir($directory, 0755, true);
    }

    $line = sprintf(
        "[%s] %s: %s%s\n",
        date('Y-m-d H:i:s'),
        strtoupper($level),
        $message,
        $context ? ' ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : ''
    );

    @file_put_contents($directory . '/app-' . date('Y-m') . '.log', $line, FILE_APPEND | LOCK_EX);
}

function log_info(string $message, array $context = []): void
{
    log_write('info', $message, $context);
}

function log_error(string $message, array $context = []): void
{
    log_write('error', $message, $context);
}

function log_critical(string $message, array $context = []): void
{
    log_write('critical', $message, $context);
}
