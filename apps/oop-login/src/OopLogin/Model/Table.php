<?php

namespace OopLogin\Model;

use OopLogin\Exception\DuplicateUsernameException;
use OopLogin\Exception\DuplicateEmailException;
use OopLogin\Exception\NotFoundException;
use OopLogin\Model\Entity\Login;
use DateTime;
use PDO;
use DomainException;
use InvalidArgumentException;
use LengthException;
use PDOException;

/**
 * Performs PDO operations on MySQL tables.
 */
class Table
{
    /**
     * The table's PDO connection to the database
     * @var PDO
     */
    protected $connection;

    /**
     * Constructor
     *
     * @param PDO $connection The PDO connection to the database
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function __construct($connection)
    {
        if (!($connection instanceof PDO)) {
            throw new InvalidArgumentException('The database connection is not a PDO connection.');
        }

        $this->connection = $connection;
    }

    /**
     * Validate a User field for a varchar(255) column in the table
     *
     * @param string $field The field-value to validate
     * @param string $name The name of the field for Exception messages
     *
     * @throws DomainException
     * @throws InvalidArgumentException
     * @throws LengthException
     *
     * @return void
     */
    protected function validateVarchar255($field, $name)
    {
        if (!is_string($field)) {
            throw new InvalidArgumentException('The ' . $name . ' is not a string.');
        }
        if (empty($field)) {
            throw new DomainException('The ' . $name . ' is empty.');
        }
        if (strlen($field) > 255) {
            throw new LengthException('The ' . $name . ' is too long.');
        }
    }

    /**
     * Validate a User field for an int column in the table
     *
     * @param int $field The field-value to validate
     * @param string $name The name of the field for Exception messages
     *
     * @throws DomainException
     * @throws InvalidArgumentException
     * @throws LengthException
     *
     * @return void
     */
    protected function validateInt($field, $name)
    {
        if (!is_int($field)) {
            throw new InvalidArgumentException('The ' . $name . ' is not an integer.');
        }
        if ($field > 4294967295) {
            throw new DomainException('The ' . $name . ' is outside the valid numerical range.');
        }
    }

    protected function validateNonNegInt($field, $name)
    {
        $this->validateInt($field, $name);

        if ($field < 0) {
            throw new DomainException('The ' . $name . ' is outside the valid numerical range.');
        }
    }

    protected function validateDatetime($field, $name)
    {
        $format = 'Y-m-d H:i:s';
        $t = DateTime::createFromFormat($format, $field);
        if (!$t || ($t->format($format) !== $field)) {
            throw new InvalidArgumentException('The ' . $name . ' is not a datetime value.');
        }
    }

    /**
     * Validate User id
     *
     * @param int $id the User's id
     *
     * @return void
     */
    protected function validateId($field, $name = 'id')
    {
        $this->validateNonNegInt($field, $name);
    }

    /**
     * Validate entity's existence
     * 
     * @param mixed $entity the table entity
     * @param string $entityName the class name of the entity
     * @param string $fieldName the name of the entry field used for search
     * 
     * @throws OopLogin\Exception\NotFoundException
     * 
     * @return void
     */
    protected function validateEntity($entity, $entityName, $fieldName = 'id')
    {
        if (is_null($entity)) {
            throw new NotFoundException('No ' . $entityName . ' with the given ' . $fieldName . ' was found.');
        }
    }

    /**
     * Validate entities' existence in array
     *
     * @param array $entities the array of table entities
     * @param string $entitiesName the plural of the entity's class name
     * @param string $fieldName the name of the entry field used for search
     *
     * @throws OopLogin\Exception\NotFoundException
     *
     * @return void
     */
    protected function validateEntities($entities, $entitiesName, $fieldName = 'id')
    {
        if (empty($entities)) {
            throw new NotFoundException('No ' . $entitiesName . ' with the given ' . $fieldName . ' were found.');
        }
    }

    /**
     * Get the PDO connection
     *
     * @return PDO
     */
    public function connection()
    {
        return $this->connection;
    }
}
