<?php declare(strict_types=1);

namespace codesaur\Base;

use Throwable;

interface ErrorHandlerInterface
{
    public function error(Throwable $throwable);
}
