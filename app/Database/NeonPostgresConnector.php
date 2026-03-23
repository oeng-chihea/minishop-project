<?php

namespace App\Database;

use Illuminate\Database\Connectors\PostgresConnector;

/**
 * Custom PostgreSQL connector that injects the Neon endpoint ID into the DSN.
 *
 * Neon requires SNI to identify which database endpoint to route the connection to.
 * Newer libpq (14+) handles this automatically via SNI. Older clients (e.g. XAMPP
 * PHP 7.4 with libpq 11.4) don't support SNI, so Neon offers an alternative:
 * pass `options=endpoint=<endpoint-id>` in the connection options.
 *
 * This connector appends that option to the DSN whenever NEON_ENDPOINT_ID is set,
 * making it transparent for both local dev (old libpq) and production (new libpq).
 *
 * @see https://neon.tech/sni
 */
class NeonPostgresConnector extends PostgresConnector
{
    /**
     * Create a DSN string from a configuration.
     * Appends `options=endpoint=<id>` when NEON_ENDPOINT_ID is configured.
     */
    protected function getDsn(array $config): string
    {
        $dsn = parent::getDsn($config);

        $endpointId = $config['neon_endpoint_id'] ?? null;

        if ($endpointId) {
            // Neon reads this option from the startup packet before routing.
            // Works with all libpq versions — no SNI required.
            // Note: DSN format uses literal `=`, not URL-encoded `%3D`.
            $dsn .= ";options=endpoint={$endpointId}";
        }

        return $dsn;
    }
}
