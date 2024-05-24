<?php

namespace Tuf\Tests\Client;

/**
 * Runs UpdaterTest's test cases on the fixtures without consistent snapshots.
 *
 * @testdox Updater with non-consistent snapshots
 *
 * @coversDefaultClass \Tuf\Client\Updater
 */
class InconsistentFixturesUpdaterTest extends UpdaterTest
{
    /**
     * {@inheritdoc}
     */
    protected static function getFixturePath(string $fixtureName, string $subPath = '', bool $isDir = true): string
    {
        return parent::getFixturePath($fixtureName, "inconsistent/$subPath", $isDir);
    }
    /**
     * {@inheritdoc}
     */
    public function providerRefreshRepository(): array
    {
        $data = parent::providerRefreshRepository();
        $data['Delegated'][1]['root'] = 3;
        $data['NestedDelegated'][1]['root'] = 3;
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function providerExceptionForInvalidMetadata(): array
    {
        $data = parent::providerExceptionForInvalidMetadata();
        $data['add key to timestamp.json'][4]['root'] = 3;
        $data['add key to snapshot.json'][4]['root'] = 3;
        $data['change version in snapshot.json'][4]['root'] = 3;
        $data['add key to targets.json'][4]['root'] = 3;
        $data['change version in targets.json'][4]['root'] = 3;
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function providerVerifiedDelegatedDownload(): array
    {
        $data = parent::providerVerifiedDelegatedDownload();
        $data['level_1_target.txt'][2]['root'] = 3;
        $data['level_1_2_target.txt'][2]['root'] = 3;
        $data['level_1_2_terminating_findable.txt'][2]['root'] = 3;
        $data['level_1_2_3_below_non_terminating_target.txt'][2]['root'] = 3;
        $data['level_1_2_terminating_3_target.txt'][2]['root'] = 3;
        $data['level_1_2a_terminating_plus_1_more_findable.txt'][2]['root'] = 3;
        $data['TerminatingDelegation targets.txt'][2]['root'] = 1;
        $data['TerminatingDelegation a.txt'][2]['root'] = 1;
        $data['TerminatingDelegation b.txt'][2]['root'] = 1;
        $data['TerminatingDelegation c.txt'][2]['root'] = 1;
        $data['TerminatingDelegation d.txt'][2]['root'] = 1;
        $data['TopLevelTerminating a.txt'][2]['root'] = 1;
        $data['NestedTerminatingNonDelegatingDelegation a.txt'][2]['root'] = 1;
        $data['NestedTerminatingNonDelegatingDelegation b.txt'][2]['root'] = 1;
        $data['ThreeLevelDelegation targets.txt'][2]['root'] = 1;
        $data['ThreeLevelDelegation a.txt'][2]['root'] = 1;
        $data['ThreeLevelDelegation b.txt'][2]['root'] = 1;
        $data['ThreeLevelDelegation c.txt'][2]['root'] = 1;
        $data['ThreeLevelDelegation d.txt'][2]['root'] = 1;
        $data['ThreeLevelDelegation e.txt'][2]['root'] = 1;
        $data['ThreeLevelDelegation f.txt'][2]['root'] = 1;
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function providerDelegationErrors(): array
    {
        $data = parent::providerDelegationErrors();
        $data['no path match'][2]['root'] = 3;
        $data['matches parent delegation'][2]['root'] = 3;
        $data['delegated path does not match parent'][2]['root'] = 3;
        $data['delegated path does not match role'][2]['root'] = 3;
        $data['delegation is after terminating delegation'][2]['root'] = 3;
        $data['TerminatingDelegation e.txt'][2]['root'] = 1;
        $data['TerminatingDelegation f.txt'][2]['root'] = 1;
        $data['TopLevelTerminating b.txt'][2]['root'] = 1;
        $data['NestedTerminatingNonDelegatingDelegation c.txt'][2]['root'] = 1;
        $data['NestedTerminatingNonDelegatingDelegation d.txt'][2]['root'] = 1;
        $data['ThreeLevelDelegation z.txt'][2]['root'] = 1;
        $data['ThreeLevelDelegation z.zip'][2]['root'] = 1;
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function providerKeyRotation(): array
    {
        $data = parent::providerKeyRotation();
        $data['no keys rotated'][1]['root'] = 1;
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function providerFileNotFoundExceptions(): array
    {
        $data = parent::providerFileNotFoundExceptions();
        $data['timestamp.json in Delegated'][2]['root'] = 3;
        $data['snapshot.json in Delegated'][2]['root'] = 3;
        $data['targets.json in Delegated'][2]['root'] = 3;
        return $data;
    }
}
