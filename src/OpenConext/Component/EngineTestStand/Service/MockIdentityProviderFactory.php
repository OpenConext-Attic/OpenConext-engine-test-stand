<?php

namespace OpenConext\Component\EngineTestStand\Service;

use OpenConext\Component\EngineTestStand\Fixture\MockIdpsFixture;

/**
 * Class MockIdentityProviderFactory
 * @package OpenConext\Component\EngineTestStand\Service
 */
class MockIdentityProviderFactory
{
    protected $baseUrl;
    protected $path;
    protected $fixture;

    /**
     * @param string $baseUrl
     * @param string $path
     * @param MockIdpsFixture $fixture
     */
    public function __construct(
        $baseUrl,
        $path,
        MockIdpsFixture $fixture
    ) {
        $this->baseUrl      = $baseUrl;
        $this->path         = $path;
        $this->fixture      = $fixture;
    }

    /**
     * @param $idpName
     * @return IdentityProvider
     */
    public function createForName($idpName)
    {
        return new MockIdentityProvider($this->baseUrl, $this->path, $idpName, $this->fixture->get($idpName));
    }
}
