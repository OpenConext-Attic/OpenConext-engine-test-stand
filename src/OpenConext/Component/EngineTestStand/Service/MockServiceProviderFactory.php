<?php

namespace OpenConext\Component\EngineTestStand\Service;

use OpenConext\Component\EngineTestStand\Fixture\MockSpsFixture;

/**
 * Class MockServiceProviderFactory
 * @package OpenConext\Component\EngineTestStand\Service
 */
class MockServiceProviderFactory
{
    protected $baseUrl;
    protected $triggerLoginRedirectPath;
    protected $acsPath;
    protected $fixture;

    /**
     * @param string $baseUrl
     * @param string $path
     * @param MockSpsFixture $fixture
     */
    public function __construct(
        $baseUrl,
        $triggerLoginRedirectPath,
        $acsPath,
        MockSpsFixture $fixture
    ) {
        $this->baseUrl                  = $baseUrl;
        $this->triggerLoginRedirectPath = $triggerLoginRedirectPath;
        $this->acsPath                  = $acsPath;
        $this->fixture                  = $fixture;
    }

    /**
     * @param $spName
     * @return ServiceProvider
     */
    public function createForName($spName)
    {
        return new ServiceProvider(
            $this->baseUrl,
            $this->triggerLoginRedirectPath,
            $this->acsPath,
            $spName,
            $this->fixture->get($spName)
        );
    }
}
