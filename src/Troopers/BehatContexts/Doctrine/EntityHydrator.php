<?php
namespace Troopers\BehatContexts\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use Knp\FriendlyContexts\Doctrine\EntityHydrator AS BaseEntityHydrator;

class EntityHydrator extends BaseEntityHydrator {
    /**
     * @param ObjectManager $em
     * @param $entity
     * @param $values
     * @return $this
     */
    public function hydrate(ObjectManager $em, $entity, $values)
    {
        if(isset($values['@']))
        {
            unset($values['@']); //dont hydrate entity with aliases
        }
        parent::hydrate($em, $entity, $values);
        return $this;
    }
}