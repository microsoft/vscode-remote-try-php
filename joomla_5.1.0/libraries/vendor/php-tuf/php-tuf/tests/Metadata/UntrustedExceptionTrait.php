<?php

namespace Tuf\Tests\Metadata;

trait UntrustedExceptionTrait
{

    /**
     * Tests that accessing method that with untrusted metadata throws an exception.
     *
     * @param string $method
     *   The method to call.
     * @param array $args
     *   The arguments for the method.
     *
     * @dataProvider providerUntrustedException
     *
     * @return void
     */
    public function testUntrustedException(string $method, array $args = []): void
    {
        $data = json_decode($this->clientStorage->read($this->validJson), true);
        $metadata = static::callCreateFromJson(json_encode($data));
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Cannot use untrusted '{$this->expectedType}'. metadata.");
        $method = new \ReflectionMethod($metadata, $method);
        $method->invokeArgs($metadata, $args);
    }
}
