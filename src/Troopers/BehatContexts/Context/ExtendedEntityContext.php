<?php

namespace Troopers\BehatContexts\Context;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Knp\FriendlyContexts\Context\EntityContext;

/**
 * Class ExtendedEntityContext.
 */
class ExtendedEntityContext extends EntityContext
{
    /**
     * @BeforeScenario
     *
     * @param $event
     *
     * @throws DBALException
     */
    public function beforeScenario($event)
    {
        parent::beforeScenario($event);
        if ($this->hasTags(['truncate-data', '~not-truncate-data'])) {
            /** @var EntityManager $entityManager */
            foreach ($this->getEntityManagers() as $entityManager) {
                /* @var $connection Connection */
                $connection = $entityManager->getConnection();
                $platform = $connection->getDatabasePlatform();
                $schemaManager = $connection->getSchemaManager();
                $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');
                $tables = $schemaManager->listTables();

                foreach ($tables as $table) {
                    $connection->executeUpdate($platform->getTruncateTableSQL($table->getName()));
                }
                $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');
            }
        }
    }
}
