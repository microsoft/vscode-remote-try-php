<?php

namespace Tuf\Tests\Client;

use Tuf\Exception\Attack\SignatureThresholdException;

/**
 * Runs UpdaterTest's test cases on the fixtures with consistent snapshots.
 *
 * @testdox Updater with consistent snapshots
 *
 * @coversDefaultClass \Tuf\Client\Updater
 */
class ConsistentFixturesUpdaterTest extends UpdaterTest
{
    /**
     * {@inheritdoc}
     */
    protected static function getFixturePath(string $fixtureName, string $subPath = '', bool $isDir = true): string
    {
        return parent::getFixturePath($fixtureName, "consistent/$subPath", $isDir);
    }

    /**
     * {@inheritdoc}
     */
    public function providerRefreshRepository(): array
    {
        $data = parent::providerRefreshRepository();
        $data['Delegated'][1]['root'] = 4;
        $data['NestedDelegated'][1]['root'] = 5;
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function providerExceptionForInvalidMetadata(): array
    {
        $data = parent::providerExceptionForInvalidMetadata();
        $data['add another key to root.json'] = [
            // § 5.3.4
            '4.root.json',
            ['signed', 'newkey'],
            'new value',
            new SignatureThresholdException('Signature threshold not met on root'),
            [
                'root' => 3,
                'timestamp' => 2,
                'snapshot' => 2,
                'targets' => 2,
            ],
        ];
        $data['add key to timestamp.json'][4]['root'] = 4;
        $data['add key to snapshot.json'][0] = '4.snapshot.json';
        $data['add key to snapshot.json'][4]['root'] = 4;
        $data['change version in snapshot.json'][4]['root'] = 4;
        $data['change version in snapshot.json'][0] = '4.snapshot.json';
        $data['add key to targets.json'][4]['root'] = 4;
        $data['add key to targets.json'][0] = '4.targets.json';
        $data['change version in targets.json'][4]['root'] = 4;
        $data['change version in targets.json'][0] = '4.targets.json';
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function providerVerifiedDelegatedDownload(): array
    {
        $data = parent::providerVerifiedDelegatedDownload();
        $data['level_1_target.txt'][2]['root'] = 5;
        $data['level_1_2_target.txt'][2]['root'] = 5;
        $data['level_1_2_terminating_findable.txt'][2]['root'] = 5;
        $data['level_1_2_3_below_non_terminating_target.txt'][2]['root'] = 5;
        $data['level_1_2_terminating_3_target.txt'][2]['root'] = 5;
        $data['level_1_2a_terminating_plus_1_more_findable.txt'][2]['root'] = 5;
        $data['TerminatingDelegation targets.txt'][2]['root'] = 2;
        $data['TerminatingDelegation a.txt'][2]['root'] = 2;
        $data['TerminatingDelegation b.txt'][2]['root'] = 2;
        $data['TerminatingDelegation c.txt'][2]['root'] = 2;
        $data['TerminatingDelegation d.txt'][2]['root'] = 2;
        $data['TopLevelTerminating a.txt'][2]['root'] = 2;
        $data['NestedTerminatingNonDelegatingDelegation a.txt'][2]['root'] = 2;
        $data['NestedTerminatingNonDelegatingDelegation b.txt'][2]['root'] = 2;
        $data['ThreeLevelDelegation targets.txt'][2]['root'] = 2;
        $data['ThreeLevelDelegation a.txt'][2]['root'] = 2;
        $data['ThreeLevelDelegation b.txt'][2]['root'] = 2;
        $data['ThreeLevelDelegation c.txt'][2]['root'] = 2;
        $data['ThreeLevelDelegation d.txt'][2]['root'] = 2;
        $data['ThreeLevelDelegation e.txt'][2]['root'] = 2;
        $data['ThreeLevelDelegation f.txt'][2]['root'] = 2;
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function providerDelegationErrors(): array
    {
        $data = parent::providerDelegationErrors();
        $data['no path match'][2]['root'] = 6;
        $data['matches parent delegation'][2]['root'] = 6;
        $data['delegated path does not match parent'][2]['root'] = 6;
        $data['delegated path does not match role'][2]['root'] = 6;
        $data['delegation is after terminating delegation'][2]['root'] = 6;
        $data['TerminatingDelegation e.txt'][2]['root'] = 2;
        $data['TerminatingDelegation f.txt'][2]['root'] = 2;
        $data['TopLevelTerminating b.txt'][2]['root'] = 2;
        $data['NestedTerminatingNonDelegatingDelegation c.txt'][2]['root'] = 2;
        $data['NestedTerminatingNonDelegatingDelegation d.txt'][2]['root'] = 2;
        $data['ThreeLevelDelegation z.txt'][2]['root'] = 2;
        $data['ThreeLevelDelegation z.zip'][2]['root'] = 2;
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function providerKeyRotation(): array
    {
        $data = parent::providerKeyRotation();
        $data['no keys rotated'][1]['root'] = 2;
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function providerFileNotFoundExceptions(): array
    {
        $data = parent::providerFileNotFoundExceptions();
        $data['timestamp.json in Delegated'][2]['root'] = 4;
        $data['snapshot.json in Delegated'][2]['root'] = 4;
        $data['snapshot.json in Delegated'][1] = '4.snapshot.json';
        $data['targets.json in Delegated'][2]['root'] = 4;
        $data['targets.json in Delegated'][1] = '4.targets.json';
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function providerUnsupportedRepo(): array
    {
        $data = parent::providerUnsupportedRepo();
        $data[0][0]['root'] = 2;
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function providerTimestampAndSnapshotLength(): array
    {
        $data = parent::providerTimestampAndSnapshotLength();
        $data['unknown snapshot length'][1] = '1.snapshot.json';
        $data['unknown targets length'][1] = '1.targets.json';
        $data['known snapshot length'][1] = '1.snapshot.json';
        $data['known targets length'][1] = '1.targets.json';
        return $data;
    }
}
