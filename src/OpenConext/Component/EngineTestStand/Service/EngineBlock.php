<?php

namespace OpenConext\Component\EngineTestStand\Service;

use OpenConext\Component\EngineBlock\Fixture\IdFixture;
use OpenConext\Component\EngineBlock\Fixture\IdFrame;
use OpenConext\Component\EngineBlock\Fixture\SuperGlobalsFixture;
use OpenConext\Component\EngineBlock\Fixture\TimeFixture;

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
    }

    public function overrideTime($time)
    {
        $this->timeFixture->set($time);
    }

    public function setNewIdsToUse(IdFrame $idFrame)
    {
        $this->idFixture->addFrame($idFrame);
    }

    public function clearNewIds()
    {
        $this->idFixture->clear();
    }
}
