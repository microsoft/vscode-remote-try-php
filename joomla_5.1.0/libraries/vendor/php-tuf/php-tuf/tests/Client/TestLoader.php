<?php

namespace Tuf\Tests\Client;

use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Utils;
use Tuf\Exception\RepoFileNotFound;
use Tuf\Loader\LoaderInterface;

/**
 * Loads files from a simulated, in-memory server.
 */
class TestLoader extends \ArrayObject implements LoaderInterface
{
    /**
     * The $maxBytes arguments passed to ::load(), keyed by file name.
     *
     * This is used to confirm that the updater passes the expected file names
     * and maximum download lengths.
     *
     * @var int[][]
     */
    public array $maxBytes = [];

    /**
     * Populates this object with a fixture's server-side metadata.
     *
     * @param string $basePath
     *   The path of the fixture to read from.
     */
    public function populateFromFixture(string $basePath): void
    {
        $this->exchangeArray([]);

        // Store the file contents in memory so they can be easily altered.
        $fixturesPath = "$basePath/server";
        $files = glob("$fixturesPath/metadata/*.json");
        $targetsPath = "$fixturesPath/targets";
        if (is_dir($targetsPath)) {
            $files = array_merge($files, glob("$targetsPath/*"));
        }
        foreach ($files as $file) {
            $baseName = basename($file);
            if ($this->offsetExists($baseName)) {
                throw new \UnexpectedValueException("For testing fixtures target files should not use metadata file names");
            }
            $this[$baseName] = file_get_contents($file);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function load(string $locator, int $maxBytes): PromiseInterface
    {
        $this->maxBytes[$locator][] = $maxBytes;

        if ($this->offsetExists($locator)) {
            $stream = Utils::streamFor($this[$locator]);
            return Create::promiseFor($stream);
        } else {
            return Create::rejectionFor(new RepoFileNotFound("File $locator not found."));
        }
    }
}
