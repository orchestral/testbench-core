<?php

namespace Orchestra\Testbench\Tests\Foundation\Process\RemoteCommand;

use Orchestra\Testbench\Foundation\Process\RemoteCommand\ConcurrencyEnvelope;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(ConcurrencyEnvelope::class)]
class ConcurrencyEnvelopeTest extends TestCase
{
    #[Test]
    public function it_encrypts_and_decrypts_a_result_envelope()
    {
        $envelope = new ConcurrencyEnvelope;
        $payload = json_encode(['successful' => true, 'result' => serialize('result')]);
        $key = 'test-key';

        $encrypted = $envelope->encrypt($payload, $key);

        $this->assertSame(
            "\n@@LARAVEL-CONCURRENCY-V1@@".base64_encode($payload).'@@'.hash_hmac('sha256', base64_encode($payload), $key)."@@\n",
            $encrypted
        );
        $this->assertSame(
            ['successful' => true, 'result' => serialize('result')],
            $envelope->decrypt($encrypted, $key)
        );
    }

    #[Test]
    public function it_finds_a_valid_envelope_before_trailing_process_output()
    {
        $envelope = new ConcurrencyEnvelope;
        $key = 'test-key';
        $result = ['successful' => true, 'result' => serialize('result')];
        $encrypted = $envelope->encrypt(json_encode($result), $key);

        $this->assertSame($result, $envelope->decrypt($encrypted."output after the envelope \x1f\x8b", $key));
    }

    #[Test]
    public function it_rejects_an_envelope_with_an_invalid_signature()
    {
        $envelope = new ConcurrencyEnvelope;
        $key = 'test-key';
        $encrypted = $envelope->encrypt(json_encode(['successful' => true]), $key);
        $invalid = preg_replace('/@@[0-9a-f]{64}@@/', '@@'.str_repeat('0', 64).'@@', $encrypted);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The process did not return a valid result envelope.');

        $envelope->decrypt($invalid, $key);
    }

    #[Test]
    public function it_rejects_a_signed_payload_without_a_successful_field()
    {
        $envelope = new ConcurrencyEnvelope;

        $this->expectException(RuntimeException::class);

        $envelope->decrypt($envelope->encrypt(json_encode(['result' => serialize('result')]), 'test-key'), 'test-key');
    }
}
