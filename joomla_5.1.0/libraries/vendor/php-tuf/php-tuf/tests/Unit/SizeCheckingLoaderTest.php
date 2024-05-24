<?php

namespace Tuf\Tests\Unit;

use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Tuf\Exception\DownloadSizeException;
use Tuf\Loader\LoaderInterface;
use Tuf\Loader\SizeCheckingLoader;

/**
 * @covers \Tuf\Loader\SizeCheckingLoader
 */
class SizeCheckingLoaderTest extends TestCase implements LoaderInterface
{
    private StreamInterface $stream;

    private SizeCheckingLoader $loader;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loader = new SizeCheckingLoader($this);
    }

    /**
     * {@inheritDoc}
     */
    public function load(string $locator, int $maxBytes): PromiseInterface
    {
        return Create::promiseFor($this->stream);
    }

    public function testKnownSize(): void
    {
        $this->stream = Utils::streamFor('Deep Space Nine is the best Star Trek series. This is a scientific fact.');
        $this->assertGreaterThan(0, $this->stream->getSize());

        // If the size is known, the stream should not be replaced.
        $this->assertSame($this->stream, $this->loader->load('ok.txt', 1024)->wait());

        $this->expectException(DownloadSizeException::class);
        $this->expectExceptionMessage('too_long.txt exceeded 8 bytes');
        $this->loader->load('too_long.txt', 8)->wait();
    }

    public function testSeekableUnknownSize(): void
    {
        $buffer = Utils::streamFor('Deep Space Nine is the best Star Trek series. This is a scientific fact.')
            ->detach();

        $this->stream = new class ($buffer) extends Stream {

            public function getSize(): ?int
            {
                return null;
            }

        };
        $this->assertTrue($this->stream->isSeekable());

        // If the stream is seekable, it should not be replaced.
        $this->assertSame($this->stream, $this->loader->load('ok.txt', 1024)->wait());

        $this->assertSame(0, $this->stream->tell());
        // Move the stream to a different position so we can ensure the size
        // check returns us there.
        $this->stream->seek(8);
        try {
            $this->loader->load('too_long.txt', 8)->wait();
            $this->fail('Expected DownloadSizeException to be thrown, but it was not.');
        } catch (DownloadSizeException $e) {
            $this->assertSame('too_long.txt exceeded 8 bytes', $e->getMessage());
            $this->assertSame(8, $this->stream->tell());
        }
    }

    public function testNonSeekableUnknownSize(): void
    {
        $buffer = Utils::tryFopen('php://temp', 'a+');

        // Make the stream non-seekable, forcing the loader to read from it.
        $this->stream = new class ($buffer) extends Stream {

            public function getSize(): ?int
            {
                return null;
            }

            public function isSeekable(): bool
            {
                return false;
            }

        };

        // Write 8 bytes, and then return to the start of the stream so we can
        // read them back.
        $this->stream->write(str_repeat('*', 8));
        // fseek() returns 0 on success.
        $this->assertSame(0, fseek($buffer, 0), 'Failed to return to the start of the stream.');
        // Even if the stream did not exceed $maxBytes, it should have been
        // replaced with a new stream.
        $replacementStream = $this->loader->load('ok.txt', 8)->wait();
        $this->assertNotSame($this->stream, $replacementStream);
        $this->assertSame(0, $replacementStream->tell());

        // Write another byte, and return to the start of the stream.
        $this->stream->write('*');
        $this->assertSame(0, fseek($buffer, 0), 'Failed to return to the start of the stream.');
        // Since there is now more data to read beyond $maxBytes, we should get
        // an exception.
        $this->expectException(DownloadSizeException::class);
        $this->expectExceptionMessage('too_long.txt exceeded 8 bytes');
        $this->loader->load('too_long.txt', 8)->wait();
    }

    public function testExactSize(): void
    {
        $this->stream = Utils::streamFor('Sisko');
        $this->loader->load('just_right.txt', 5, true);

        $this->expectException(DownloadSizeException::class);
        $this->expectExceptionMessage("Expected too_short.txt to be 1024 bytes.");
        $this->loader->load('too_short.txt', 1024, true)->wait();
    }
}
