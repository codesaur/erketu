<?php declare(strict_types=1);

namespace codesaur\Http\Error;

use Throwable;

interface ExceptionHandlerInterface
{
    public function exception(Throwable $throwable);
}
