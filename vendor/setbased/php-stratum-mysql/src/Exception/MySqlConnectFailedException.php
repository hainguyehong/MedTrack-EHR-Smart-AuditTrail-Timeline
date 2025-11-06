<?php
declare(strict_types=1);

namespace SetBased\Stratum\MySql\Exception;

use SetBased\Stratum\Middle\Exception\ConnectFailedException;

/**
 * Exception thrown when an attempt to connect to a MySQL or MariDB instance fails.
 */
class MySqlConnectFailedException extends MySqlDataLayerException implements ConnectFailedException
{
  // Nothing to implement.
}

//----------------------------------------------------------------------------------------------------------------------
