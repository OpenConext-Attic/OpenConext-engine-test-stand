<?php

namespace OpenConext\Component\EngineTestStand\Features\Context;

use Behat\Behat\Context\BehatContext;
use OpenConext\Bundle\LogReplayBundle\Features\Context\FeatureContext;

/**
 * Class AbstractSubContext
 * @package OpenConext\Component\EngineTestStand\Features\Context
 */
abstract class AbstractSubContext extends BehatContext
{
    /**
     * @return FeatureContext
     */
    public function getMainContext()
    {
        return parent::getMainContext();
    }
}
