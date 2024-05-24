<?php

namespace Tuf\Tests\TestHelpers\DurableStorage;

use Tuf\Metadata\StorageBase;

/**
 * Defines a memory storage for trusted TUF metadata, used for testing.
 */
class TestStorage extends StorageBase
{
    private $container = [];

    public static function createFromDirectory(string $dir): static
    {
        $storage = new static();

        // Loop through and load files in the given path.
        $fsIterator = new \FilesystemIterator($dir, \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::KEY_AS_FILENAME);
        foreach ($fsIterator as $info) {
            // Only load JSON files.
            /** @var $info \SplFileInfo */
            if ($info->isFile() && $info->getExtension() === 'json') {
                $storage->write($info->getBasename('.json'), file_get_contents($info->getRealPath()));
            }
        }
        return $storage;
    }

    public function read(string $name): ?string
    {
        return $this->container[$name] ?? null;
    }

    public function write(string $name, string $data): void
    {
        $this->container[$name] = $data;
    }

    public function delete(string $name): void
    {
        unset($this->container[$name]);
    }
}
