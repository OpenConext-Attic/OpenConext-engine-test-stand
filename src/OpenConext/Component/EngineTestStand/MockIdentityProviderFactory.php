<?php

namespace OpenConext\Component\EngineTestStand;

use Symfony\Component\Routing\RouterInterface;

/**
 * Class MockIdentityProviderFactory
 * @package OpenConext\Component\EngineTestStand\Service
 */
class MockIdentityProviderFactory extends AbstractMockEntityFactory
{
    protected $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param $idpName
     * @return MockIdentityProvider
     */
    public function createNew($idpName)
    {
        return new MockIdentityProvider($idpName, $this->generateDefaultEntityMetadata($idpName));
    }

    /**
     * @param string $idpName
     * @return \SAML2_XML_md_EntityDescriptor
     */
    protected function generateDefaultEntityMetadata($idpName)
    {
        $entityMetadata = new \SAML2_XML_md_EntityDescriptor();
        $entityMetadata->entityID = $this->router->generate(
            'mock_idp_metadata',
            array('idpName' => $idpName),
            RouterInterface::ABSOLUTE_URL
        );

        $acsService = new \SAML2_XML_md_IndexedEndpointType();
        $acsService->index = 0;
        $acsService->Binding  = \SAML2_Const::BINDING_HTTP_REDIRECT;
        $acsService->Location = $this->router->generate(
            'mock_idp_sso',
            array('idpName' => $idpName),
            RouterInterface::ABSOLUTE_URL
        );

        $idpSsoDescriptor = new \SAML2_XML_md_IDPSSODescriptor();
        $idpSsoDescriptor->protocolSupportEnumeration = array(\SAML2_Const::NS_SAMLP);
        $idpSsoDescriptor->SingleSignOnService[] = $acsService;

        $idpSsoDescriptor->KeyDescriptor[] = $this->generateDefaultSigningKeyPair();

        $entityMetadata->RoleDescriptor[] = $idpSsoDescriptor;

        return $entityMetadata;
    }
}
