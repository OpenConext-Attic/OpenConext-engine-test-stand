<?php

namespace OpenConext\Component\EngineBlock\Fixture;

class IdFrame
{
    const ID_USAGE_SAML2_RESPONSE   = 'saml2-response';
    const ID_USAGE_SAML2_REQUEST    = 'saml2-request';
    const ID_USAGE_SAML2_ASSERTION  = 'saml2-assertion';
    const ID_USAGE_SAML2_METADATA   = 'saml2-metadata';
    const ID_USAGE_OTHER            = 'other';

    protected $ids = array();

    public function set($usage, $id)
    {
        $this->ids[$usage] = $id;
        return $this;
    }

    public function get($usage)
    {
        return $this->ids[$usage];
    }
}
