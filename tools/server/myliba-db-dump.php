<?php
/**
 * Myliba production → local database snapshot.
 *
 * Runs from cron on the production host (CLI only). Reads the database
 * credentials from the live wp-config.php, writes a gzipped dump next to this
 * file and records its metadata in latest.json. The folder lives outside the
 * web root; developers fetch the snapshot over FTPS with `make pull-live`.
 *
 * No credentials are stored in this file or in the repository.
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$dir = __DIR__;
$home = dirname($dir);
$config = $home . '/public_html/wp-config.php';
$target = $dir . '/latest.sql.gz';
$tmp = $dir . '/latest.sql.gz.tmp';
$meta = $dir . '/latest.json';
$errorFile = $dir . '/last-error.txt';

$fail = static function (string $message) use ($errorFile, $tmp): void {
    @unlink($tmp);
    file_put_contents($errorFile, gmdate('c') . ' ' . $message . "\n");
    fwrite(STDERR, $message . "\n");
    exit(1);
};

$lock = fopen($dir . '/.lock', 'c');
if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
    exit(0);
}

if (!is_readable($config)) {
    $fail('wp-config.php is not readable.');
}
$source = (string) file_get_contents($config);

$constant = static function (string $name) use ($source): string {
    $pattern = '/define\(\s*[\'"]' . preg_quote($name, '/') . '[\'"]\s*,\s*([\'"])(.*?)\1\s*\)/s';
    return preg_match($pattern, $source, $m) ? stripslashes($m[2]) : '';
};

$dbName = $constant('DB_NAME');
$dbUser = $constant('DB_USER');
$dbPass = $constant('DB_PASSWORD');
$dbHost = $constant('DB_HOST') ?: 'localhost';
$prefix = preg_match('/\$table_prefix\s*=\s*([\'"])(.*?)\1\s*;/', $source, $m) ? $m[2] : 'wp_';

if ($dbName === '' || $dbUser === '') {
    $fail('Database settings could not be read from wp-config.php.');
}

$host = $dbHost;
$port = '';
$socket = '';
if (strpos($dbHost, ':') !== false) {
    [$host, $extra] = explode(':', $dbHost, 2);
    if ($extra !== '' && $extra[0] === '/') {
        $socket = $extra;
    } else {
        $port = $extra;
    }
}

if (!function_exists('proc_open')) {
    $fail('proc_open is disabled for PHP CLI on this host.');
}

$command = ['mysqldump', '--single-transaction', '--quick', '--no-tablespaces', '--skip-lock-tables',
    '--default-character-set=utf8mb4', '--user=' . $dbUser];
if ($host !== '') {
    $command[] = '--host=' . $host;
}
if ($port !== '') {
    $command[] = '--port=' . $port;
}
if ($socket !== '') {
    $command[] = '--socket=' . $socket;
}
$command[] = $dbName;

$env = ['MYSQL_PWD' => $dbPass, 'PATH' => getenv('PATH') ?: '/usr/local/bin:/usr/bin:/bin', 'HOME' => $home];
$process = proc_open($command, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $dir, $env);
if (!is_resource($process)) {
    $fail('mysqldump could not be started.');
}

$gz = gzopen($tmp, 'wb6');
if (!$gz) {
    $fail('Snapshot file could not be opened for writing.');
}
$bytes = 0;
while (!feof($pipes[1])) {
    $chunk = fread($pipes[1], 1048576);
    if ($chunk === false || $chunk === '') {
        continue;
    }
    $bytes += strlen($chunk);
    gzwrite($gz, $chunk);
}
gzclose($gz);
$stderr = trim((string) stream_get_contents($pipes[2]));
fclose($pipes[1]);
fclose($pipes[2]);
$exit = proc_close($process);

if ($exit !== 0 || $bytes < 1024) {
    // mysqldump errors never include the password; keep the first line only.
    $fail('mysqldump failed (exit ' . $exit . '): ' . strtok($stderr, "\n"));
}

chmod($tmp, 0600);
if (!rename($tmp, $target)) {
    $fail('Snapshot could not be moved into place.');
}

file_put_contents($meta, json_encode([
    'created_at' => gmdate('c'),
    'table_prefix' => $prefix,
    'sql_bytes' => $bytes,
    'gz_bytes' => filesize($target),
    'sha256' => hash_file('sha256', $target),
], JSON_UNESCAPED_SLASHES) . "\n");
chmod($meta, 0600);
@unlink($errorFile);
