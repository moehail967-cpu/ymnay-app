<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class CustomDatabaseException extends Exception
{
    /**
     * The database connection name
     *
     * @var string|null
     */
    protected $connection;

    /**
     * The database name
     *
     * @var string|null
     */
    protected $database;

    /**
     * The SQL query that caused the exception
     *
     * @var string|null
     */
    protected $query;

    /**
     * Additional context data
     *
     * @var array
     */
    protected $context;

    /**
     * Create a new database exception instance.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     * @param string|null $connection
     * @param string|null $database
     * @param string|null $query
     * @param array $context
     */
    public function __construct(
        string $message = 'Database operation failed',
        int $code = 0,
        \Throwable $previous = null,
        string $connection = null,
        string $database = null,
        string $query = null,
        array $context = []
    ) {
        parent::__construct($message, $code, $previous);
        
        $this->connection = $connection;
        $this->database = $database;
        $this->query = $query;
        $this->context = $context;
    }

    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        Log::error('Database Exception occurred', [
            'message' => $this->getMessage(),
            'connection' => $this->connection,
            'database' => $this->database,
            'query' => $this->query,
            'context' => $this->context,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => $this->getTraceAsString()
        ]);

        return false;
    }

    /**
     * Render the exception as an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $this->getMessage(),
                'error_type' => 'database_error',
                'database' => $this->database,
                'connection' => $this->connection,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->danger($this->getMessage());
    }

    /**
     * Get the database connection name
     *
     * @return string|null
     */
    public function getConnection(): ?string
    {
        return $this->connection;
    }

    /**
     * Set the database connection name
     *
     * @param string $connection
     * @return $this
     */
    public function setConnection(string $connection)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * Get the database name
     *
     * @return string|null
     */
    public function getDatabase(): ?string
    {
        return $this->database;
    }

    /**
     * Set the database name
     *
     * @param string $database
     * @return $this
     */
    public function setDatabase(string $database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * Get the SQL query
     *
     * @return string|null
     */
    public function getQuery(): ?string
    {
        return $this->query;
    }

    /**
     * Set the SQL query
     *
     * @param string $query
     * @return $this
     */
    public function setQuery(string $query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Get the context data
     *
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Set context data
     *
     * @param array $context
     * @return $this
     */
    public function setContext(array $context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Add context data
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function addContext(string $key, $value)
    {
        $this->context[$key] = $value;
        return $this;
    }

    /**
     * Create a connection failed exception
     *
     * @param string $connection
     * @param string $reason
     * @return static
     */
    public static function connectionFailed(string $connection, string $reason = '')
    {
        $message = "Database connection failed for '{$connection}'";
        if ($reason) {
            $message .= ": {$reason}";
        }

        return new static(
            $message,
            500,
            null,
            $connection,
            null,
            null,
            ['reason' => $reason]
        );
    }

    /**
     * Create an access denied exception
     *
     * @param string $database
     * @param string $user
     * @return static
     */
    public static function accessDenied(string $database, string $user = '')
    {
        $message = "Access denied to database '{$database}'";
        if ($user) {
            $message .= " for user '{$user}'";
        }

        return new static(
            $message,
            403,
            null,
            null,
            $database,
            null,
            ['user' => $user]
        );
    }

    /**
     * Create a database exists exception
     *
     * @param string $database
     * @return static
     */
    public static function databaseExists(string $database)
    {
        return new static(
            "Database '{$database}' already exists",
            409,
            null,
            null,
            $database
        );
    }

    /**
     * Create a database not found exception
     *
     * @param string $database
     * @return static
     */
    public static function databaseNotFound(string $database)
    {
        return new static(
            "Database '{$database}' was not found",
            404,
            null,
            null,
            $database
        );
    }

    /**
     * Create a query failed exception
     *
     * @param string $query
     * @param string $error
     * @param string|null $database
     * @return static
     */
    public static function queryFailed(string $query, string $error, string $database = null)
    {
        return new static(
            "Query execution failed: {$error}",
            500,
            null,
            null,
            $database,
            $query,
            ['sql_error' => $error]
        );
    }

    /**
     * Create a migration failed exception
     *
     * @param string $database
     * @param string $migration
     * @param string $error
     * @return static
     */
    public static function migrationFailed(string $database, string $migration = '', string $error = '')
    {
        $message = "Migration failed for database '{$database}'";
        if ($migration) {
            $message .= " (migration: {$migration})";
        }

        return new static(
            $message,
            500,
            null,
            null,
            $database,
            null,
            [
                'migration' => $migration,
                'error' => $error
            ]
        );
    }

    /**
     * Create a duplicate entry exception
     *
     * @param string $table
     * @param string $key
     * @param string $value
     * @return static
     */
    public static function duplicateEntry(string $table, string $key, string $value)
    {
        return new static(
            "Duplicate entry '{$value}' for key '{$key}' in table '{$table}'",
            409,
            null,
            null,
            null,
            null,
            [
                'table' => $table,
                'key' => $key,
                'value' => $value
            ]
        );
    }

    /**
     * Create a timeout exception
     *
     * @param string $operation
     * @param int $timeout
     * @return static
     */
    public static function timeout(string $operation, int $timeout)
    {
        return new static(
            "Database operation '{$operation}' timed out after {$timeout} seconds",
            408,
            null,
            null,
            null,
            null,
            [
                'operation' => $operation,
                'timeout' => $timeout
            ]
        );
    }
}