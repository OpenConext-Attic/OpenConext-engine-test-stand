<?php

namespace OpenConext\EngineTestStand\Fixture;

abstract class RoleFixture extends AbstractFixture
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
        return $this->fixture[$name];
    }
}
