<?php

namespace Tuf\Tests\Unit\Client;

use PHPUnit\Framework\TestCase;
use Tuf\Metadata\MetadataBase;
use Tuf\Metadata\Verifier\RootVerifier;
use Tuf\Metadata\Verifier\VerifierBase;

/**
 * @coversDefaultClass \Tuf\Metadata\Verifier\VerifierBase
 */
class VerifierTest extends TestCase
{
    /**
     * Tests that no rollback attack is flagged when one is not performed.
     *
     * @covers ::checkRollbackAttack
     *
     * @return void
     */
    public function testCheckRollbackAttackNoAttack(): void
    {
        // We test lack of an exception in the positive test case.
        $this->expectNotToPerformAssertions();

        $localMetadata = $this->getMockBuilder(MetadataBase::class)->disableOriginalConstructor()->getMock();
        $localMetadata->expects(self::any())->method('getType')->willReturn('any');
        $localMetadata->expects(self::any())->method('getVersion')->willReturn(1);

        $verifier = new class ($localMetadata) extends VerifierBase
        {
            public function __construct($trustedMetadata)
            {
                $this->trustedMetadata = $trustedMetadata;
            }

            public function verify(MetadataBase $untrustedMetadata): void
            {
                $this->checkRollbackAttack($untrustedMetadata);
            }
        };

        // The incoming version is newer than the local version, so no
        // rollback attack is present.
        $incomingMetadata = $this->getMockBuilder(MetadataBase::class)->disableOriginalConstructor()->getMock();
        $incomingMetadata->expects(self::any())->method('getType')->willReturn('any');
        $incomingMetadata->expects(self::any())->method('getVersion')->willReturn(2);
        $verifier->verify($incomingMetadata);

        // Incoming at same version as local.
        $incomingMetadata->expects(self::any())->method('getVersion')->willReturn(2);
        $verifier->verify($incomingMetadata);
    }

    /**
     * Tests that the correct exception is thrown in case of a rollback attack.
     *
     * @covers ::checkRollbackAttack
     *
     * @return void
     */
    public function testCheckRollbackAttack(): void
    {
        $this->expectException('\Tuf\Exception\Attack\RollbackAttackException');
        $this->expectExceptionMessage('Remote any metadata version "$1" is less than previously seen any version "$2"');

        // The incoming version is lower than the local version, so this should
        // be identified as a rollback attack.
        $localMetadata = $this->getMockBuilder(MetadataBase::class)->disableOriginalConstructor()->getMock();
        $localMetadata->expects(self::any())->method('getType')->willReturn('any');
        $localMetadata->expects(self::any())->method('getVersion')->willReturn(2);

        $verifier = new class ($localMetadata) extends VerifierBase
        {
            public function __construct(MetadataBase $trustedMetadata)
            {
                $this->trustedMetadata = $trustedMetadata;
            }

            public function verify(MetadataBase $untrustedMetadata): void
            {
                $this->checkRollbackAttack($untrustedMetadata);
            }
        };

        $incomingMetadata = $this->getMockBuilder(MetadataBase::class)->disableOriginalConstructor()->getMock();
        $incomingMetadata->expects(self::any())->method('getType')->willReturn('any');
        $incomingMetadata->expects(self::any())->method('getVersion')->willReturn(1);
        $verifier->verify($incomingMetadata);
    }

    /**
     * Tests that the correct exception is thrown in case of a rollback attack
     * where the incoming metadata does not match the expected version.
     *
     * § 5.3.5
     *
     * @covers ::checkRollbackAttack
     *
     * @return void
     */
    public function testCheckRollbackAttackAttackExpectedVersion(): void
    {
        $this->expectException('\Tuf\Exception\Attack\RollbackAttackException');
        $this->expectExceptionMessage('Remote \'root\' metadata version "$2" does not the expected version "$3"');

        // The incoming version is lower than the local version, so this should
        // be identified as a rollback attack.
        $localMetadata = $this->getMockBuilder(MetadataBase::class)->disableOriginalConstructor()->getMock();
        $localMetadata->expects(self::any())->method('getType')->willReturn('any');
        $localMetadata->expects(self::any())->method('getVersion')->willReturn(2);

        $verifier = new class ($localMetadata) extends RootVerifier
        {
            public function __construct(MetadataBase $trustedMetadata)
            {
                $this->trustedMetadata = $trustedMetadata;
            }

            public function verify(MetadataBase $untrustedMetadata): void
            {
                $this->checkRollbackAttack($untrustedMetadata);
            }
        };

        $incomingMetadata = $this->getMockBuilder(MetadataBase::class)->disableOriginalConstructor()->getMock();
        $incomingMetadata->expects(self::any())->method('getType')->willReturn('any');
        $incomingMetadata->expects(self::any())->method('getVersion')->willReturn(2);
        $verifier->verify($incomingMetadata);
    }

    /**
     * Tests that no freeze attack is flagged when the data has not expired.
     *
     * @covers ::checkFreezeAttack
     *
     * @return void
     */
    public function testCheckFreezeAttackNoAttack(): void
    {
        // We test lack of an exception in the positive test case.
        $this->expectNotToPerformAssertions();

        $dateFormat = "Y-m-d\TH:i:sT";
        $signedMetadata = $this->getMockBuilder(MetadataBase::class)->disableOriginalConstructor()->getMock();
        $signedMetadata->expects(self::any())->method('getType')->willReturn('any');
        $expiration = \DateTimeImmutable::createFromFormat($dateFormat, '1970-01-01T00:00:01Z');
        $signedMetadata->expects(self::any())->method('getExpires')->willReturn($expiration);
        $nowString = '1970-01-01T00:00:00Z';
        $now = \DateTimeImmutable::createFromFormat($dateFormat, $nowString);

        $method = new \ReflectionMethod(VerifierBase::class, 'checkFreezeAttack');
        $method->setAccessible(true);

        // The update's expiration is later than now, so no freeze attack
        // exception should be thrown.
        $method->invoke(null, $signedMetadata, $now);

        // No exception should be thrown exactly at expiration time.
        $signedMetadata->expects(self::any())->method('getExpires')->willReturn($now);
        $method->invoke(null, $signedMetadata, $now);
    }

    /**
     * Tests that the correct exception is thrown when the update is expired.
     *
     * § 5.3.10
     * § 5.4.4
     * § 5.5.6
     * @covers ::checkFreezeAttack
     *
     * @return void
     */
    public function testCheckFreezeAttackAttack(): void
    {
        $this->expectException('\Tuf\Exception\Attack\FreezeAttackException');

        $dateFormat = "Y-m-d\TH:i:sT";
        $signedMetadata = $this->getMockBuilder(MetadataBase::class)->disableOriginalConstructor()->getMock();
        $signedMetadata->expects(self::any())->method('getType')->willReturn('any');
        $expiration = \DateTimeImmutable::createFromFormat($dateFormat, '1970-01-01T00:00:00Z');
        $signedMetadata->expects(self::any())->method('getExpires')->willReturn($expiration);
        // 1 second later.
        $now = \DateTimeImmutable::createFromFormat($dateFormat, '1970-01-01T00:00:01Z');

        $method = new \ReflectionMethod(VerifierBase::class, 'checkFreezeAttack');
        $method->setAccessible(true);

        // The update has already expired, so a freeze attack exception should
        // be thrown.
        $method->invoke(null, $signedMetadata, $now);
    }
}
