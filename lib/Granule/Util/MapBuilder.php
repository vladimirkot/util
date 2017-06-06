<?php

namespace Granule\Util;

interface MapBuilder {
    function add($key, $value): MapBuilder;
    function build(): Map;
}