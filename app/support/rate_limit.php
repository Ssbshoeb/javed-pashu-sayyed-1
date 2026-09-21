<?php
/*
|--------------------------------------------------------------------------
| Rate limiting
|--------------------------------------------------------------------------
| Replacement for Laravel's `throttle:8,1` middleware on the write paths.
|
| The counters live in storage/cache/ratelimit, one small JSON file per bucket.
| A file rather than an in-memory map because PHP shares nothing between
| requests: the Next.js port kept its counters in the memory of one long-lived
| Node process, which simply does not exist here. The file store is therefore
| STRONGER than what it replaces on a single host — it survives between
| requests, where an in-process map was reset by every worker restart.
|
| KNOWN LIMITATION. This is per-host. On a load-balanced deployment each host
| keeps its own count. That is enough to stop a script hammering the forms from
| one address, which is what it is for; if the site is ever spread across hosts,
| back this with a shared store and keep the same two-function interface.
*/

defined('APP_RUNNING') or exit('No direct access.');

/** Where the buckets live. */
function rate_limit_dir(): string
{
    $directory = STORAGE_PATH . '/cache/ratelimit';

    if (!is_dir($directory)) {
        @mkdir($directory, 0755, true);
    }

    return $directory;
}

/**
 * @param string $key        Bucket identity — the caller combines the route with the client IP.
 * @param int    $limit      Requests allowed per window.
 * @param int    $perSeconds Window length in seconds.
 * @return bool `true` when the request is within the limit.
 */
function rate_limit_allow(string $key, int $limit, int $perSeconds = 60): bool
{
    $directory = rate_limit_dir();

    // The key contains an IP address. Hashing it means no address is ever
    // written to disk, even as a filename — which is what the privacy notice
    // promises — and it makes the name safe for any filesystem.
    $file = $directory . '/' . hash('sha256', $key) . '.json';

    rate_limit_sweep($directory, $perSeconds);

    $now = time();

    $handle = @fopen($file, 'c+');
    if ($handle === false) {
        // A storage directory that cannot be written must not make the forms
        // unusable. Fail open, and say so in the log so it gets fixed.
        log_error('[rate-limit] bucket not writable; request allowed', ['file' => $file]);
        return true;
    }

    $allowed = true;

    if (flock($handle, LOCK_EX)) {
        $raw = stream_get_contents($handle);
        $window = json_decode((string) $raw, true);

        if (!is_array($window) || !isset($window['count'], $window['resetAt']) || $window['resetAt'] <= $now) {
            $window = ['count' => 1, 'resetAt' => $now + $perSeconds];
        } elseif ($window['count'] >= $limit) {
            $allowed = false;
        } else {
            $window['count']++;
        }

        if ($allowed) {
            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, (string) json_encode($window));
            fflush($handle);
        }

        flock($handle, LOCK_UN);
    }

    fclose($handle);

    return $allowed;
}

/**
 * Drops expired buckets so the directory does not grow without bound.
 *
 * Swept at most once a minute, and only by the request that happens to notice
 * the marker is stale, so the cost is one readdir per minute rather than one
 * per submission.
 */
function rate_limit_sweep(string $directory, int $perSeconds): void
{
    $marker = $directory . '/.swept';
    $now = time();

    if (is_file($marker) && ($now - (int) @filemtime($marker)) < 60) {
        return;
    }

    @touch($marker);

    $files = @glob($directory . '/*.json');
    if (!$files) {
        return;
    }

    foreach ($files as $file) {
        // A bucket whose window closed more than one window ago can never be
        // needed again; anything newer might still be counting.
        if (($now - (int) @filemtime($file)) > ($perSeconds * 2)) {
            @unlink($file);
        }
    }
}

/**
 * The throttle every write path shares — `throttle:8,1` on the Laravel routes.
 * Returns true when the request should be REFUSED.
 */
function throttled(string $bucket, int $limit = 8): bool
{
    return !rate_limit_allow($bucket . ':' . client_key(), $limit);
}
