<?php

namespace Granule\Tests\Util\TO;

use Granule\Util\Enum;

/**
 * @method static Month January()
 * @method static Month February()
 */
class Month extends Enum {
    private const
        January = 1,
        February = 2
    ;

    /** @var int */
    private $position;

    public function __init(int $position) {
        $this->position = $position;
    }

    public function getPosition(): int {
        return $this->position;
    }
}