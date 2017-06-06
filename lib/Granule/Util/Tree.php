<?php

namespace Granule\Util;

interface Tree extends
    \ArrayAccess, \Iterator, \Countable,
    \Serializable, \JsonSerializable, Hashable
{
    function __invoke(string $offset, $default = null);
}