<?php

namespace OpenConext\EngineTestStand\Features\Context;

use Behat\Behat\Context\BehatContext;

abstract class AbstractSubContext extends BehatContext
{
    /**
     * @return \FeatureContext
     */
    public function getMainContext()
    {
        return parent::getMainContext();
    }
}
