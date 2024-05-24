<?php

namespace Tuf\Tests\Client;

use Tuf\Tests\ClientTestBase;

class HashedBinsTest extends ClientTestBase
{
    public function test(): void
    {
        $this->loadClientAndServerFilesFromFixture('HashedBins');

        // The fixture contains targets a.txt through z.txt (ASCII 97 through
        // 122), distributed across hashed bins.
        $targets = array_map(fn (int $c) => chr($c) . '.txt', range(97, 122));

        $updater = $this->getUpdater();
        // We should be able to download every single target without trouble.
        foreach ($targets as $name) {
            // By default, the fixture builder puts "Contents: FILENAME" into
            // the target files it creates.
            $this->assertSame("Contents: $name", $updater->download($name)->wait()->getContents());
        }
    }
}
