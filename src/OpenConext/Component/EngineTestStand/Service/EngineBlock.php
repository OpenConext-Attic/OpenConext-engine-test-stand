<?php

namespace OpenConext\Component\EngineTestStand\Service;

use OpenConext\Component\EngineBlockFixtures\IdFixture;
use OpenConext\Component\EngineBlockFixtures\IdFrame;
use OpenConext\Component\EngineBlockFixtures\SuperGlobalsFixture;
use OpenConext\Component\EngineBlockFixtures\TimeFixture;

/**
 * Class EngineBlock
 * @package OpenConext\Component\EngineTestStand\Service
 */
class EngineBlock
{
    const IDP_METADATA_PATH         = '/authentication/idp/metadata';
    const SP_METADATA_PATH          = '/authentication/sp/metadata';
    const SINGLE_SIGN_ON_PATH       = '/authentication/idp/single-sign-on';
    const ASSERTION_CONSUMER_PATH   = '/authentication/sp/consume-assertion';

    protected $baseUrl;
    protected $timeFixture;
    protected $superGlobalFixture;
    protected $idFixture;

    /**
     * @param $baseUrl
     * @param TimeFixture $timeFixture
     * @param SuperGlobalsFixture $superGlobalFixture
     * @param IdFixture $idFixture
     */
    public function __construct(
        $baseUrl,
        TimeFixture $timeFixture,
        SuperGlobalsFixture $superGlobalFixture,
        IdFixture $idFixture
    ) {
        $this->baseUrl              = $baseUrl;
        $this->timeFixture          = $timeFixture;
        $this->superGlobalFixture   = $superGlobalFixture;
        $this->idFixture            = $idFixture;
    }

    public function idpEntityId()
    {
        return $this->baseUrl . self::IDP_METADATA_PATH;
    }

    public function singleSignOnLocation()
    {
        return $this->baseUrl . self::SINGLE_SIGN_ON_PATH;
    }

    public function spEntityId()
    {
        return $this->baseUrl . self::SP_METADATA_PATH;
    }

    public function assertionConsumerLocation()
    {
        return $this->baseUrl . self::ASSERTION_CONSUMER_PATH;
    }

    public function overrideHostname($hostname)
    {
        $this->superGlobalFixture->set(SuperGlobalsFixture::SERVER, 'HTTP_HOST', $hostname);
        return $this;
    }

    public function overrideTime($time)
    {
        $this->timeFixture->set($time);
        return $this;
    }

    public function setNewIdsToUse(IdFrame $idFrame)
    {
        $this->idFixture->addFrame($idFrame);
        return $this;
    }

    public function clearNewIds()
    {
        $this->idFixture->clear();
        return $this;
    }
}
