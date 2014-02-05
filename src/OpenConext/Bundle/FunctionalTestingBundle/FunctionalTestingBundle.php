<?php

namespace OpenConext\Bundle\FunctionalTestingBundle;

use OpenConext\Component\EngineTestStand\Saml2\Compat\Container;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class FunctionalTestingBundle
 * @package OpenConext\Bundle\FunctionalTestingBundle
 */
class FunctionalTestingBundle extends Bundle
{
}

// @todo Doesn't belong here, should be moved somewhere better
\SAML2_Compat_ContainerSingleton::setContainer(new Container());
