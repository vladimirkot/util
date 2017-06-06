<?php

namespace Granule\Util;

interface StrictTypedValue {
    function getValueType(): string;
}