<?php

/**
 * PHP version 8.1.11
 * 
 * @author Youn Mélois <youn@melois.dev>
 */

require_once 'config.php';
require_once 'library/exceptions.php';

/**
 * Collection of methods to communicate with the database.
 */
class Database
{
    protected $PDO;

    /**
     * Connect to the PostgreSQL database.
     * 
     * @throws PDOException Error thrown if the connection to 
     *                      the database failed.
     */
    public function __construct()
    {
        $this->PDO = new PDO(
            'pgsql:host=' . DB_SERVER . ';port=' . DB_PORT . ';dbname=' . DB_NAME,
            DB_USER,
            DB_PASSWORD
        );
    }

    /**
     * Gets the password hash of a user.
     * 
     * @param string $email
     * 
     * @return ?string The password hash if exists.
     */
    public function getUserPasswordHash(string $email): ?string
    {
        $email = strtolower($email);

        $request = 'SELECT password_hash FROM users 
                        WHERE email = :email';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':email', $email);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);

        if (!$result) {
            return NULL;
        }

        return $result->password_hash;
    }

    /**
     * Verifies the user credentials.
     * 
     * @param string $email
     * @param string $password
     * 
     * @return bool
     */
    public function verifyUserCredentials(
        string $email,
        string $password
    ): bool {
        $password_hash = $this->getUserPasswordHash($email);
        return !empty($password_hash) &&
            password_verify($password, $password_hash);
    }

    /**
     * Verifies the user access token.
     * 
     * @param string $access_token
     * 
     * @return bool
     */
    public function verifyUserAccessToken(
        string $access_token
    ): bool {
        $request = 'SELECT * FROM users
                        WHERE access_token = :access_token';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':access_token', $access_token);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);

        return !empty($result);
    }

    /**
     * Create an user in the database and return a bool to result.
     * 
     * @param string $name     Name of the user. 
     * @param string $email    Email of the user.
     * @param string $password Password of the user.
     * 
     * @throws DuplicateEmailException Error thrown if the email is already used
     */
    public function createUser(string $name, string $email, string $password): bool
    {
        // test if user already exists
        $request = 'SELECT * FROM users
                        WHERE email = :email';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':email', $email);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);

        if ($result) {
            throw new DuplicateEmailException('Email already exists.');
        }

        // create password hash to store in database
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // create user
        $request = 'INSERT INTO users 
                        ("name", email, password_hash)
                        VALUES (:name, :email, :password_hash)';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':name', $name);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':password_hash', $password_hash);
        return $statement->execute();
    }

    /**
     * Deletes a user.
     * 
     * @param string $email
     * @param string $password
     * 
     * @throws AuthenticationException
     */
    public function deleteUser(string $email, string $password): bool
    {
        // test if the credentials are correct
        if (!$this->verifyUserCredentials($email, $password)) {
            throw new AuthenticationException();
        }

        $request = 'DELETE FROM users
                        WHERE email = :email';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':email', $email);
        return $statement->execute();
    }

    /**
     * Connects the user by returning its unique id if the 
     * credentials are valid.
     * 
     * @param string $email
     * @param string $password
     * @param int $session_expire (optional) The lifetime of the session cookie in seconds.
     * 
     * @throws AuthenticationException If the authentication failed.
     */
    public function connectUser(
        string $email,
        string $password,
        int $session_expire = 0
    ): bool {
        // test if the credentials are correct
        if (!$this->verifyUserCredentials($email, $password)) {
            throw new AuthenticationException();
        }

        // make email lowercase in case the user used uppercase letters
        $email = strtolower($email);

        // create a unique token used to identify the user
        $access_token = hash('sha256', $email . $password . microtime(true));

        // Set session hash on the user
        $request = 'UPDATE users SET access_token = :access_token
                        WHERE email = :email';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':access_token', $access_token);
        $success = $statement->execute();

        // Throw an exception if the update failed
        if (!$success) {
            throw new Exception('Failed to connect user.');
        }

        if ($session_expire > 0) {
            $session_expire = time() + $session_expire;
        }

        // set the session cookie
        return setcookie(
            ACCESS_TOKEN_NAME,
            $access_token,
            $session_expire
        );
    }

    /**
     * Disconnects the user by deleting the access token.
     * 
     * @throws AuthenticationException If the access token is invalid.
     */
    public function disconnectUser(): bool
    {
        if (!isset($_COOKIE[ACCESS_TOKEN_NAME])) {
            return false;
        }

        $access_token = $_COOKIE[ACCESS_TOKEN_NAME];

        if (!$this->verifyUserAccessToken($access_token)) {
            throw new AuthenticationException();
        }

        // remove access token from the user
        $request = 'UPDATE users SET access_token = NULL
                        WHERE access_token = :access_token';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':access_token', $access_token);
        $success = $statement->execute();

        // delete the session cookie
        return $success && setcookie(
            ACCESS_TOKEN_NAME,
            '',
            time() - 3600
        );
    }

    /**
     * Gets sport info for an id 
     * 
     * @param int $id
     * 
     * @return ?arry return null if the sport_id don't exist
     */
    public function getSportInfo(int $id): ?array {
        $request = 'SELECT name, description from sports where id = :id';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':id', $id);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Gets all sports and their id and name
     * 
     * @return ?array return null if there is no sport in the db
     */
    public function getSports(): ?Array {
        $request = 'SELECT id, name from sports';

        $statement = $this->PDO->prepare($request);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Gets all infor of a match 
     * 
     * @param int $id
     * 
     * @return ?array return null if the match_id don't exist
    */
    public function getMatchInfo(int $id): ?array {
        $request = 'SELECT m.id, s.name "sport_name", m.type from matches m 
                        left join sports s on sport_id = s.id where m.id = :id';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':id', $id);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Gets all teams for a match
     * 
     * @param int $id of the match
     * 
     * @return ?array return null if there no team on the match
     */
    public function getTeamsOnMatch(int $id): ?array {
        $request = 'SELECT t.name "team_name", team_id, score from participations 
                        left join teams t on team_id = t.id where match_id = :id';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':id', $id);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
