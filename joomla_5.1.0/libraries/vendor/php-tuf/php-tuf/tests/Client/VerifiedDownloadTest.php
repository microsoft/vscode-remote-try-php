<?php

namespace Tuf\Tests\Client;

use GuzzleHttp\Psr7\Utils;
use Prophecy\PhpUnit\ProphecyTrait;
use Tuf\Exception\Attack\InvalidHashException;
use Tuf\Exception\NotFoundException;
use Tuf\Tests\ClientTestBase;

/**
 * Tests transparent download of verified targets.
 */
class VerifiedDownloadTest extends ClientTestBase
{
    use ProphecyTrait;

    /**
     * @testWith ["consistent"]
     *   ["inconsistent"]
     *
     * @covers \Tuf\Client\Updater::download
     */
    public function testVerifiedDownload(string $fixtureVariant): void
    {
        $fixture = 'Simple/' . $fixtureVariant;
        $this->loadClientAndServerFilesFromFixture($fixture);
        $updater = $this->getUpdater();

        $testFilePath = static::getFixturePath($fixture, 'server/targets/testtarget.txt', false);
        $testFileContents = file_get_contents($testFilePath);
        $this->assertSame($testFileContents, $updater->download('testtarget.txt')->wait()->getContents());

        // If the file fetcher returns a file stream, the updater should NOT try
        // to read the contents of the stream into memory.
        $stream = $this->prophesize('\Psr\Http\Message\StreamInterface');
        $stream->getMetadata('uri')->willReturn($testFilePath);
        $stream->getContents()->shouldNotBeCalled();
        $stream->rewind()->shouldNotBeCalled();
        $stream->getSize()->willReturn(strlen($testFileContents));
        $updater->download('testtarget.txt');

        // If the target isn't known, we should get an exception.
        try {
            $updater->download('void.txt');
            $this->fail('Expected a NotFoundException to be thrown, but it was not.');
        } catch (NotFoundException $e) {
            $this->assertSame('Target not found: void.txt', $e->getMessage());
        }

        $stream = Utils::streamFor('invalid data');
        $this->serverFiles['testtarget.txt'] = $stream;
        try {
            $updater->download('testtarget.txt')->wait();
            $this->fail('Expected InvalidHashException to be thrown, but it was not.');
        } catch (InvalidHashException $e) {
            $this->assertSame("Invalid sha256 hash for testtarget.txt", $e->getMessage());
            $this->assertSame($stream, $e->getStream());
        }
    }
}
