<?php

namespace Tuf\Metadata;

/**
 * Defines a base class for saving and loading trusted TUF metadata.
 */
abstract class StorageBase implements StorageInterface
{
    /**
     * Reads raw metadata.
     *
     * @param string $name
     *   The name of the metadata to read (e.g., `root`, `targets`), without
     *   file extension.
     *
     * @return string|null
     *   The metadata, as a string, or null if the metadata is unavailable.
     */
    abstract protected function read(string $name): ?string;

    /**
     * Writes trusted metadata as a string.
     *
     * @param string $name
     *   The name of the metadata to write (e.g., `root`, `targets`), without
     *   file extension.
     * @param string $data
     *   The metadata to write, fully encoded and normalized as desired.
     */
    abstract protected function write(string $name, string $data): void;

    /**
     * {@inheritdoc}
     */
    public function getRoot(): RootMetadata
    {
        $data = $this->read(RootMetadata::TYPE);
        if ($data) {
            return RootMetadata::createFromJson($data)->trust();
        }
        throw new \LogicException("Could not load root metadata.");
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp(): ?TimestampMetadata
    {
        $data = $this->read(TimestampMetadata::TYPE);
        return $data ? TimestampMetadata::createFromJson($data)->trust() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSnapshot(): ?SnapshotMetadata
    {
        $data = $this->read(SnapshotMetadata::TYPE);
        return $data ? SnapshotMetadata::createFromJson($data)->trust() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets(string $role = 'targets'): ?TargetsMetadata
    {
        $data = $this->read($role);
        return $data ? TargetsMetadata::createFromJson($data, $role)->trust() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function save(MetadataBase $metadata): void
    {
        $metadata->ensureIsTrusted();
        $this->write($metadata->getRole(), $metadata->getSource());
    }
}
