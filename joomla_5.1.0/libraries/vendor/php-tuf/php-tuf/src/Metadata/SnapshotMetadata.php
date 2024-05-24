<?php

namespace Tuf\Metadata;

use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Type;

class SnapshotMetadata extends FileInfoMetadataBase
{

    /**
     * {@inheritdoc}
     */
    public const TYPE = 'snapshot';

    /**
     * {@inheritdoc}
     */
    protected static function getSignedCollectionOptions(): array
    {
        $options = parent::getSignedCollectionOptions();
        $options['fields']['meta'] = new Required([
            new Type('array'),
            new Count(['min' => 1]),
            new All([
                new Collection(
                    [
                        'fields' => static::getMetaPathConstraints(),
                        'allowExtraFields' => true,
                    ]
                ),
            ]),
        ]);
        return $options;
    }

    private static function getMetaPathConstraints(): array
    {
        $fields = static::getVersionConstraints();
        $fields['length'] = new Optional([
            new Type('integer'),
        ]);
        $fields += static::getHashesConstraints();
        $fields['hashes'] = new Optional($fields['hashes']);
        return $fields;
    }
}
