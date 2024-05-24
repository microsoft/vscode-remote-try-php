<?php

namespace Tuf\Loader;

use GuzzleHttp\Promise\PromiseInterface;

/**
 * Defines an interface to load data as a stream.
 *
 * The data source can be anything, from anywhere, but it must be returned as an
 * implementation of \Psr\Http\Message\StreamInterface.
 *
 * This is an internal interface used to load untrusted data! External code
 * should not use this directly, but instead rely on \Tuf\Client\Updater to
 * load TUF-verified data.
 */
interface LoaderInterface
{
    /**
     * Loads data as a stream.
     *
     * @param string $locator
     *   A string identifying the data to load. The meaning of this depends on
     *   the implementing class; it could be a URL, a relative or absolute file
     *   path, or something else.
     * @param int $maxBytes
     *   The maximum number of bytes that should be read from the data source.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface<\Psr\Http\Message\StreamInterface>
     *   A promise wrapping a data stream.
     *
     * @throws \Tuf\Exception\RepoFileNotFound
     *   If the data cannot be found.
     */
    public function load(string $locator, int $maxBytes): PromiseInterface;
}
