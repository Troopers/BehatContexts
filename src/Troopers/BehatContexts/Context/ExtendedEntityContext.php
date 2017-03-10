<?php

namespace Troopers\BehatContexts\Context;

use Behat\Gherkin\Node\TableNode;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Knp\FriendlyContexts\Context\EntityContext;
use Knp\FriendlyContexts\Utils\Asserter;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class ExtendedEntityContext.
 */
class ExtendedEntityContext extends EntityContext
{
    /* @var array */
    protected $queryParams;

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
    public function shouldFindObjectsLikeFollowing($nbr, $name, TableNode $table, $method = 'assertEquals')
    {
        $objects = $this->findObjectsByParams($name, $table);
        /* @var $asserter Asserter */
        $asserter = $this->getAsserter();
        $asserter->$method(
            (int) $nbr,
            count($objects),
            sprintf(
                'There is %d %s for the following params %s, %d wanted',
                count($objects),
                $name,
                json_encode($this->queryParams),
                $nbr
            )
        );
    }

    /**
     * @Then /^I should not find (\d+) (.*) like:?$/
     *
     * @param $nbr
     * @param $name
     * @param TableNode $table
     *
     * @throws \Exception
     */
    public function shouldNotFindObjectsLikeFollowing($nbr, $name, TableNode $table)
    {
        $this->shouldFindObjectsLikeFollowing($nbr, $name, $table, 'assertNotEquals');
    }

    /**
     * @Then /^I should not find any (.*) like:?$/
     *
     * @param $name
     * @param TableNode $table
     *
     * @throws \Exception
     */
    public function shouldNotAnyFindObjectsLikeFollowing($name, TableNode $table)
    {
        $this->shouldFindObjectsLikeFollowing(0, $name, $table, 'assertEquals');
    }

    /**
     * @param TableNode $table
     *
     * @return object[]
     */
    protected function findObjectsByParams($name, TableNode $table) {
        $rows = $table->getRows();
        $entityName = $this->resolveEntity($name)->getName();
        $this->queryParams = $this->getQueryParams($entityName, $rows[0], $rows[1]);

        return $this->getEntityManager()
            ->getRepository($entityName)
            ->findBy($this->queryParams);
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
        $this->getEntityHydrator()->hydrate($this->getEntityManager(), $entity, $values);

        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($headers as $identifier) {
            $identifiersWithValues[$identifier] = $accessor->getValue($entity, $identifier);
        }

        return $identifiersWithValues;
    }
}
