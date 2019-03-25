<?php

namespace OopLogin\Model\Table;

use OopLogin\Exception\DuplicateUsernameException;
use OopLogin\Exception\DuplicateEmailException;
use OopLogin\Exception\NotFoundException;
use OopLogin\Model\Entity\Login;
use OopLogin\Model\Table;
use PDO;
use DomainException;
use InvalidArgumentException;
use LengthException;
use PDOException;

/**
 * Performs PDO operations on Logins.
 */
class LoginsTable extends Table
{
    /**
     * Add a Login to the table
     *
     * @param Login $login The login to add to the table
     *
     * @return void
     */
    public function create($login)
    {
        $userId = $login->userId();
        $time = $login->time();

        $this->validateId($userId, 'user id');

        $this->validateDatetime($time, 'time');

        try {
            $stmt = $this->connection->prepare('INSERT INTO logins (user_id, time) VALUES (:user_id, :time)');
            $stmt->execute(array(':user_id' => $userId, ':time' => $time));
        } catch (PDOException $e) {
        }
    }

    /**
     * Retrieve an array of Logins from the table
     *
     * @return Login[]
     */
    public function read()
    {
        $logins = array();
        $stmt = $this->connection->prepare('SELECT * FROM logins');
        if ($stmt->execute()) {
            while ($row = $stmt->fetch()) {
                $login = new Login((int) $row['user_id'], $row['time'], (int) $row['id']);
                $logins[] = $login;
            }
        }
        return $logins;
    }

    /**
     * Retrieve a Login from the table by id
     *
     * @param int $id The id of the login to retrieve
     *
     * @return Login
     */
    public function readById($id)
    {
        $this->validateId($id, 'id');
        $stmt = $this->connection->prepare('SELECT * FROM logins WHERE id = ? LIMIT 1');
        if ($stmt->execute(array($id))) {
            $row = $stmt->fetch();
            if ($row) {
                $login = new Login((int) $row['user_id'], $row['time'], (int) $row['id']);
                return $login;
            }
        }
        return new Login();
    }

    /**
     * Retrieve Logins from the table by user id
     *
     * @param int $userId the id of the user to retrieve logins of
     *
     * @return Login[]
     */
    public function readByUserId($userId)
    {
        $logins = array();
        $this->validateId($userId, 'user id');
        $stmt = $this->connection->prepare('SELECT * FROM logins WHERE user_id = ?');
        if ($stmt->execute(array($userId))) {
            while ($row = $stmt->fetch()) {
                $logins[] = new Login((int) $row['user_id'], $row['time'], (int) $row['id']);
            }
        }
        return $logins;
    }

    /**
     * Delete a User
     *
     * @param int $id The id of the User to delete
     *
     * @return void
     */
    public function delete($id)
    {
        $login = $this->readById($id);
        $stmt = $this->connection->prepare('DELETE FROM logins WHERE id = :id');
        $stmt->execute(array(':id' => $id));
        /*
        $this->validateId($id);
        $user = $this->readById($id);
        if ($user->id() == null) {
            throw new NotFoundException('No user with the given id was found.');
        }
        $stmt = $this->connection->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(array(':id' => $id));
        */
    }
}
