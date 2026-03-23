<?php

declare(strict_types=1);

namespace ReallySimplePlugins\RSS\Core\Support\Helpers\Storages;

use ReallySimplePlugins\RSS\Core\Support\Helpers\Storage;

/**
 * Typed wrapper for the JSON request body.
 * Loads from php://input and exposes it as Storage.
 */
final class RequestBody extends Storage
{
	public function __construct()
	{
		$raw = file_get_contents('php://input');

		$decoded = json_decode($raw, true);

		parent::__construct(
			is_array($decoded) ? $decoded : []
		);
	}
}