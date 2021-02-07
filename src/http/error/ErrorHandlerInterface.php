<?php declare(strict_types=1);

namespace codesaur\Http\Error;

use Throwable;

interface ErrorHandlerInterface
{
    public function error(Throwable $throwable);
}
