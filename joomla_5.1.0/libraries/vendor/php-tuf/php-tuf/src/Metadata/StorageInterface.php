<?php

namespace Tuf\Metadata;

/**
 * Defines an interface for saving and loading trusted TUF metadata.
 */
interface StorageInterface
{
    /**
     * Loads trusted root metadata.
     *
     * @return \Tuf\Metadata\RootMetadata
     *   The trusted root metadata.
     *
     * @throws \LogicException
     *   Thrown if the root metadata cannot be loaded.
     */
    public function getRoot(): RootMetadata;

    /**
     * Loads trusted timestamp metadata.
     *
     * @return \Tuf\Metadata\TimestampMetadata|null
     *   The trusted timestamp metadata, or null if none is available.
     */
    public function getTimestamp(): ?TimestampMetadata;

    /**
     * Loads trusted snapshot metadata.
     *
     * @return \Tuf\Metadata\SnapshotMetadata|null
     *   The trusted snapshot metadata, or null if none is available.
     */
    public function getSnapshot(): ?SnapshotMetadata;

    /**
     * Loads trusted targets metadata for a specific role.
     *
     * @param string $role
     *   (optional) The role to load. Defaults to `targets`.
     *
     * @return \Tuf\Metadata\TargetsMetadata|null
     *   The trusted targets metadata, or null if none is available.
     */
    public function getTargets(string $role = 'targets'): ?TargetsMetadata;

    /**
     * Saves trusted metadata.
     *
     * @param \Tuf\Metadata\MetadataBase $metadata
     *   The trusted metadata to save.
     */
    public function save(MetadataBase $metadata): void;

    /**
     * Deletes stored metadata.
     *
     * @param string $name
     *   The name of the metadata to delete, without file extension, e.g.,
     *   `targets` or `1.snapshot`.
     */
    public function delete(string $name): void;
}
