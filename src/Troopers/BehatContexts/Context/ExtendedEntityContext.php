<?php

namespace Troopers\BehatContexts\Context;

use Behat\Gherkin\Node\TableNode;
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

    /**
     * @Then /^I should find (\d+) (.*) like:$/
     *
     * @param $nbr
     * @param $name
     * @param TableNode $table
     *
     * @throws \Exception
     */
    public function existObjectLikeFollowing($nbr, $name, TableNode $table)
    {
        $rows = $table->getRows();
        $queryParams = $this->getQueryParams($entityName = $this->resolveEntity($name)->getName(), array_shift($rows), array_shift($rows));
        $objects = $this->getEntityManager()
            ->getRepository($entityName)
            ->findBy($queryParams);

        if (count($objects) === 0) {
            throw new \Exception(sprintf('There is not any %s for the following params: %s', $name, json_encode($queryParams)));
        }
        if (count($objects) !== (int) $nbr) {
            throw new \Exception(sprintf('There is %d %s for the following params %s, %d wanted', count($objects), $name, json_encode($queryParams), $nbr));
        }
    }

    /**
     * @Then /^I should not find (.*) like:?$/
     *
     * @param $nbr
     * @param $name
     * @param TableNode $table
     *
     * @throws \Exception
     */
    public function notExistObjectLikeFollowing($name, TableNode $table)
    {
        $rows = $table->getRows();
        $queryParams = $this->getQueryParams($entityName = $this->resolveEntity($name)->getName(), array_shift($rows), array_shift($rows));
        $objects = $this->getEntityManager()
            ->getRepository($entityName)
            ->findBy($queryParams);

        if (count($objects) !== 0) {
            throw new \Exception(sprintf('Found %d %s for the following params: %s', count($objects), $name, json_encode($queryParams)));
        }
    }

    /**
     * @param $entityName
     * @param $headers
     * @param $row
     *
     * @return array
     */
    protected function getQueryParams($entityName, $headers, $row)
    {
        $identifiersWithValues = [];

        $values = array_combine($headers, $row);
        $entity = new $entityName();
        $this
            ->getEntityHydrator()
            ->hydrate($this->getEntityManager(), $entity, $values);

        foreach ($headers as $identifier) {
            $getter = 'get'.ucfirst($identifier);
            $identifiersWithValues[$identifier] = $entity->$getter();
        }

        return $identifiersWithValues;
    }
}
