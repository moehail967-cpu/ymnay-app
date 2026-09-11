<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class CustomTenantException extends Exception
{
    /**
     * The tenant ID related to this exception
     *
     * @var int|string|null
     */
    protected $tenantId;

    /**
     * The exception context data
     *
     * @var array
     */
    protected $context;

    /**
     * Create a new tenant exception instance.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     * @param int|string|null $tenantId
     * @param array $context
     */
    public function __construct(
        string $message = 'Tenant operation failed',
        int $code = 0,
        \Throwable $previous = null,
        $tenantId = null,
        array $context = []
    ) {
        parent::__construct($message, $code, $previous);
        
        $this->tenantId = $tenantId;
        $this->context = $context;
    }

    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        Log::error('Tenant Exception occurred', [
            'message' => $this->getMessage(),
            'tenant_id' => $this->tenantId,
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
                'error_type' => 'tenant_error',
                'tenant_id' => $this->tenantId,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->danger($this->getMessage());
    }

    /**
     * Get the tenant ID
     *
     * @return int|string|null
     */
    public function getTenantId()
    {
        return $this->tenantId;
    }

    /**
     * Set the tenant ID
     *
     * @param int|string $tenantId
     * @return $this
     */
    public function setTenantId($tenantId)
    {
        $this->tenantId = $tenantId;
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
     * Create a tenant not found exception
     *
     * @param int|string $tenantId
     * @return static
     */
    public static function notFound($tenantId)
    {
        return new static(
            "Tenant with ID '{$tenantId}' was not found",
            404,
            null,
            $tenantId
        );
    }

    /**
     * Create a tenant creation failed exception
     *
     * @param string $reason
     * @param int|string|null $tenantId
     * @return static
     */
    public static function creationFailed(string $reason, $tenantId = null)
    {
        return new static(
            "Tenant creation failed: {$reason}",
            422,
            null,
            $tenantId,
            ['reason' => $reason]
        );
    }

    /**
     * Create a tenant domain exception
     *
     * @param string $domain
     * @param int|string|null $tenantId
     * @return static
     */
    public static function domainError(string $domain, $tenantId = null)
    {
        return new static(
            "Domain operation failed for '{$domain}'",
            422,
            null,
            $tenantId,
            ['domain' => $domain]
        );
    }

    /**
     * Create a tenant migration failed exception
     *
     * @param int|string $tenantId
     * @param string $command
     * @return static
     */
    public static function migrationFailed($tenantId, string $command)
    {
        return new static(
            "Migration failed for tenant '{$tenantId}'",
            500,
            null,
            $tenantId,
            ['command' => $command]
        );
    }

    /**
     * Create a tenant seeding failed exception
     *
     * @param int|string $tenantId
     * @return static
     */
    public static function seedingFailed($tenantId)
    {
        return new static(
            "Database seeding failed for tenant '{$tenantId}'",
            500,
            null,
            $tenantId
        );
    }
}