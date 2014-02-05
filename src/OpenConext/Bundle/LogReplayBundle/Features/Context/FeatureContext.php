<?php

namespace OpenConext\Bundle\LogReplayBundle\Features\Context;

use OpenConext\Component\EngineTestStand\Features\Context\MinkContext;
use OpenConext\Component\EngineTestStand\Features\Context\EngineBlockContext;
use OpenConext\Component\EngineTestStand\Features\Context\MockIdpContext;
use OpenConext\Component\EngineTestStand\Features\Context\MockSpContext;
use OpenConext\Component\EngineTestStand\Features\Context\ReplayContext;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\Behat\Context\BehatContext;

/**
 * Class FeatureContext
 * @package OpenConext\Bundle\LogReplayBundle\Features\Context
 */
class FeatureContext extends BehatContext implements KernelAwareInterface
{
    const SUB_CONTEXT_MINK          = 'mink';
    const SUB_CONTEXT_ENGINE_BLOCK  = 'engine';
    const SUB_CONTEXT_MOCK_IDP      = 'idp';
    const SUB_CONTEXT_MOCK_SP       = 'sp';
    const SUB_CONTEXT_REPLAY        = 'replay';

    const PARAM_NAME_ENGINE_URL         = 'engineblock_url';
    const PARAM_NAME_ETS_URL            = 'engine_test_stand_url';
    const PARAM_NAME_IDPS_CONFIG_URL    = 'idps_config_url';
    const PARAM_NAME_SPS_CONFIG_URL     = 'sps_config_url';
    const PARAM_NAME_IDP_FIXTURE_FILE   = 'idp_fixture_file';
    const PARAM_NAME_SP_FIXTURE_FILE    = 'sp_fixture_file';

    protected $containerParameters = array(
        self::PARAM_NAME_ENGINE_URL,
        self::PARAM_NAME_ETS_URL,
        self::PARAM_NAME_IDPS_CONFIG_URL,
        self::PARAM_NAME_SPS_CONFIG_URL,
        self::PARAM_NAME_IDP_FIXTURE_FILE,
        self::PARAM_NAME_SP_FIXTURE_FILE,
    );

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    private $parameters;

    /**
     * Initializes context with parameters from behat.yml.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = new ParameterBag($parameters);
    }

    /**
     * @return ParameterBag
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Sets HttpKernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;

        $this->initSubContexts();
    }

    /**
     * The contexts that actually hold the step definitions.
     */
    protected function initSubContexts()
    {
        $container = $this->kernel->getContainer();

        $this->useContext(self::SUB_CONTEXT_MINK        , $container->get('openconext_functional_testing.behat_context.mink'));
        $this->useContext(self::SUB_CONTEXT_ENGINE_BLOCK, $container->get('openconext_functional_testing.behat_context.engine_block'));
        $this->useContext(self::SUB_CONTEXT_MOCK_IDP    , $container->get('openconext_functional_testing.behat_context.mock_idp'));
        $this->useContext(self::SUB_CONTEXT_MOCK_SP     , $container->get('openconext_functional_testing.behat_context.mock_sp'));
        $this->useContext(self::SUB_CONTEXT_REPLAY      , $container->get('openconext_functional_testing.behat_context.replay'));
    }

    /**
     * @return MinkContext
     */
    public function getMinkContext()
    {
        return $this->getSubcontext(self::SUB_CONTEXT_MINK);
    }

    /**
     * @return string
     */
    public function getPageContent()
    {
        return $this->getMinkContext()->getSession()->getPage()->getContent();
    }
}
