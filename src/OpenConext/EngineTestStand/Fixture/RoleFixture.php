<?php

namespace OpenConext\EngineTestStand\Fixture;

abstract class RoleFixture extends AbstractSerializedFixture
{
    public function register($name, $entityId)
    {
        $entity = new \SAML2_XML_md_EntityDescriptor();
        $entity->entityID = $entityId;
        $this->fixture[$name] = $entity;

        $this->save();
    }

    /**
     * @param $name
     * @return \SAML2_XML_md_EntityDescriptor
     */
    public function get($name)
    {
        if (!isset($this->fixture[$name])) {
            throw new \RuntimeException('No fixture for: ' . $name);
        }
        return $this->fixture[$name];
    }
}
