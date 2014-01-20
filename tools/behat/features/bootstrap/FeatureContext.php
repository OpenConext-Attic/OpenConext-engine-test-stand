<?php

use OpenConext\EngineTestStand\Config;
use Behat\Behat\Context\BehatContext;
use OpenConext\EngineTestStand\Features\Context\MinkContext;
use OpenConext\EngineTestStand\Features\Context\EngineBlockContext;
use OpenConext\EngineTestStand\Features\Context\MockIdpContext;
use OpenConext\EngineTestStand\Features\Context\MockSpContext;
use OpenConext\EngineTestStand\Features\Context\ReplayContext;

// Require 3rd-party libraries here:
define('OPENCONEXT_ETS_ROOT_DIR', realpath(__DIR__ . '/../../../../'));
require OPENCONEXT_ETS_ROOT_DIR . '/vendor/autoload.php';

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    const CONFIG_FILE = "/config.json";

    const SUB_CONTEXT_MINK          = 'mink';
    const SUB_CONTEXT_ENGINE_BLOCK  = 'engine';
    const SUB_CONTEXT_MOCK_IDP      = 'idp';
    const SUB_CONTEXT_MOCK_SP       = 'sp';
    const SUB_CONTEXT_REPLAY        = 'replay';

    public function __construct()
    {
        $this->useContext(self::SUB_CONTEXT_MINK        , new MinkContext());
        $this->useContext(self::SUB_CONTEXT_ENGINE_BLOCK, new EngineBlockContext());
        $this->useContext(self::SUB_CONTEXT_MOCK_IDP    , new MockIdpContext());
        $this->useContext(self::SUB_CONTEXT_MOCK_SP     , new MockSpContext());
        $this->useContext(self::SUB_CONTEXT_REPLAY      , new ReplayContext());
    }

    /**
     * @return MinkContext
     */
    public function getMinkContext()
    {
        return $this->getSubcontext(self::SUB_CONTEXT_MINK);
    }

    /**
     * @return Config
     */
    public function getApplicationConfig()
    {
        return Config::create(OPENCONEXT_ETS_ROOT_DIR . self::CONFIG_FILE);
    }

    /**
     * @return string
     */
    public function getPageContent()
    {
        return $this->getMinkContext()->getSession()->getPage()->getContent();
    }
}
