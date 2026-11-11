<?php

namespace Orchestra\Testbench\Foundation\Process\RemoteCommand;

use Illuminate\Support\Str;
use RuntimeException;

/**
 * @api
 */
final class ConcurrencyEnvelope
{
    /**
     * The marker string for the envelope.
     */
    const string MARKER = 'LARAVEL-CONCURRENCY-V1';

    /**
     * Create a new process envelope instance.
     *
     * @param  string  $algorithm  The hashing algorithm to use for the HMAC signature.
     */
    public function __construct(
        public readonly string $algorithm = 'sha256',
    ) {}

    /**
     * Encrypt the given payload with the provided key.
     *
     * @param  string  $payload
     * @param  string  $key
     * @return string
     */
    public function encrypt(string $payload, string $key): string
    {
        $encoded = base64_encode($payload);

        $signature = hash_hmac($this->algorithm, $encoded, $key);

        return "\n{$this->getMarker()}{$encoded}@@{$signature}@@\n";
    }

    /**
     * Decrypt the given envelope with the provided key.
     *
     * @param  string  $envelope
     * @param  string  $key
     * @return array
     *
     * @throws \RuntimeException if the envelope is invalid or cannot be decrypted.
     */
    public function decrypt(string $envelope, string $key): array
    {
        $marker = $this->getMarker();
        $length = \strlen($envelope);
        $position = strrpos($envelope, $marker);

        while ($position !== false) {
            if (preg_match('/\G([A-Za-z0-9+\/=]+)@@([0-9a-f]{64})@@/', $envelope, $matches, 0, $position + \strlen($marker)) &&
                hash_equals(hash_hmac($this->algorithm, $matches[1], $key), $matches[2])) {
                $payload = base64_decode($matches[1], true);

                $result = \is_string($payload) ? json_decode($payload, true) : null;

                if (\is_array($result) && \array_key_exists('successful', $result)) {
                    return $result;
                }
            }

            $position = $position === 0 ? false : strrpos($envelope, $marker, $position - $length - 1);
        }

        throw new RuntimeException('The process did not return a valid result envelope. Output: '.Str::limit(trim($envelope), 200));
    }

    /**
     * Get the marker string for the envelope.
     *
     * @return string
     */
    protected function getMarker(): string
    {
        return \sprintf('@@%s@@', self::MARKER);
    }
}
