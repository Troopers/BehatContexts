<?php

namespace Troopers\BehatContexts\Context;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Knp\FriendlyContexts\Context\AliceContext;

/**
 * Class ExtendedAliceContext.
 */
class ExtendedAliceContext extends AliceContext
{
    /**
     * @return array
     */
    private function getPersistableClasses()
    {
        $persistable = [];
        $metadatas = $this->getEntityManager()->getMetadataFactory()->getAllMetadata();

        foreach ($metadatas as $metadata) {
            if (isset($metadata->isEmbeddedClass) && $metadata->isEmbeddedClass) {
                continue;
            }

            $persistable[] = $metadata->getName();
        }

        return $persistable;
    }

    /**
     * @param $loader
     * @param $fixtures
     * @param $files
     *
     * @throws OptimisticLockException
     * @throws ORMInvalidArgumentException
     */
    protected function loadFixtures($loader, $fixtures, $files)
    {
        $persistable = $this->getPersistableClasses();
        /** @var EntityManager $em */
        $em = $this->getEntityManager();
        foreach ($fixtures as $id => $fixture) {
            if (in_array($id, $files, null)) {
                foreach ($loader->load($fixture) as $object) {
                    if (in_array(get_class($object), $persistable, null)) {
                        if (method_exists($object, 'getId') && $object->getId()) {
                            $metadata = $em->getClassMetadata(get_class($object));
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                        }
                        $em->persist($object);
                    }
                }
                $em->flush();
            }
        }
    }
}
