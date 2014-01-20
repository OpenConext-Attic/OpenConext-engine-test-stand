<?php

namespace OpenConext\EngineTestStand\Features\Context;

use Behat\Behat\Exception\PendingException;
use OpenConext\EngineTestStand\Fixture\IdpFixture;
use OpenConext\EngineTestStand\Service\EngineBlock;
use OpenConext\EngineTestStand\Service\IdentityProvider;
use OpenConext\EngineTestStand\Service\LogReader;
use OpenConext\EngineTestStand\Fixture\ServiceRegistryFixture;
use OpenConext\EngineTestStand\ServiceRegistry\ServiceRegistryMock;

class MockIdpContext extends AbstractSubContext
{
    const IDP_FIXTURE_CONFIG_NAME = 'idp-fixture-file';

    /**
     * @Given /^an Identity Provider named "([^"]*)" with EntityID "([^"]*)"$/
     */
    public function anIdentityProviderNamedWithEntityid($name, $entityId)
    {
        $idpFixtureFile = $config = $this->getMainContext()->getApplicationConfig()
            ->expect(self::IDP_FIXTURE_CONFIG_NAME);

        $idpFixture = IdpFixture::create(OPENCONEXT_ETS_ROOT_DIR . $idpFixtureFile);
        $idpFixture->register($name, $entityId);
    }

    /**
     * @Given /^IdP "([^"]*)" is configured to return a Response like the one at "([^"]*)"$/
     */
    public function idpIsConfiguredToReturnAResponseLikeTheOneAt($idpName, $responseLogFile)
    {
        // Prefix the filepath with the root dir if it's not an absolute path.
        if ($responseLogFile[0] !== '/') {
            $responseLogFile = OPENCONEXT_ETS_ROOT_DIR . '/' . $responseLogFile;
        }

        // Parse a Response out of the log file
        $logReader = LogReader::create($responseLogFile);
        $response = $logReader->getResponse();

        var_dump($response);

        // Write out how the IDP should behave
        $config = $config = $this->getMainContext()->getApplicationConfig();
        $idpFixtureFile = $config->expect(self::IDP_FIXTURE_CONFIG_NAME);
        $idpFixture = IdpFixture::create(OPENCONEXT_ETS_ROOT_DIR . $idpFixtureFile);
        $idpFixture->configureFromResponse($idpName, $response);

        // Determine the SSO Location for the Mock Idp
        $identityProvider = IdentityProvider::create($idpName, $idpFixture->get($idpName), $config);
        $ssoUrl = $identityProvider->singleSignOnLocation();

        // Override the SSO Location for the IDP used in the response to go to the Mock Idp
        $serviceRegistry = ServiceRegistryMock::create();
        $serviceRegistry->setEntitySsoLocation($response->getIssuer(), $ssoUrl);

        $engineBlock = EngineBlock::create($config);
        $engineBlock->overrideTime($response->getIssueInstant());
    }
}
