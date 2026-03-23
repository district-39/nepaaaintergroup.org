<?php

declare(strict_types=1);

namespace ReallySimplePlugins\RSS\Core\Support\Helpers\Storages;

use ReallySimplePlugins\RSS\Core\Support\Helpers\Storage;

/**
 * Request storage helper used in DI container.
 */
final class RequestStorage extends Storage
{
	public function __construct()
	{
		parent::__construct([
			'global' => $_REQUEST,
			'files'  => $_FILES,
		]);
	}
}