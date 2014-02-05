<?php

namespace OpenConext\Component\EngineTestStand;

use Symfony\Component\Routing\RouterInterface;

/**
 * Class MockServiceProviderFactory
 * @package OpenConext\Component\EngineTestStand\Service
 */
class MockServiceProviderFactory
{
    protected $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router) {
        $this->router = $router;
    }

    public function createNew($spName)
    {
        $descriptor = $this->generateDefaultEntityMetadata($spName);

        return new MockServiceProvider($spName, $descriptor);
    }

    protected function generateDefaultEntityMetadata($spName)
    {
        $descriptor = new \SAML2_XML_md_EntityDescriptor();
        $descriptor->entityID = $this->router->generate(
            'mock_sp_metadata',
            array('spName' => $spName),
            RouterInterface::ABSOLUTE_URL
        );

        $acsService = new \SAML2_XML_md_IndexedEndpointType();
        $acsService->index = 0;
        $acsService->Binding  = \SAML2_Const::BINDING_HTTP_POST;
        $acsService->Location = $this->router->generate(
            'mock_sp_acs',
            array('spName' => $spName),
            RouterInterface::ABSOLUTE_URL
        );

        $spSsoDescriptor = new \SAML2_XML_md_SPSSODescriptor();
        $spSsoDescriptor->protocolSupportEnumeration = array(\SAML2_Const::NS_SAMLP);
        $spSsoDescriptor->AssertionConsumerService[] = $acsService;

        $descriptor->RoleDescriptor[] = $spSsoDescriptor;

        $descriptor->Extensions['LoginRedirectUrl'] = $this->router->generate(
            'mock_sp_login_redirect',
            array('spName' => $spName),
            RouterInterface::ABSOLUTE_URL
        );
        $descriptor->Extensions['LoginPostUrl'] = $this->router->generate(
            'mock_sp_login_post',
            array('spName' => $spName),
            RouterInterface::ABSOLUTE_URL
        );
        return $descriptor;
    }
}
