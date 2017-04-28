<?php

    /*
     * This file is part of Raven.
     *
     * (c) Sentry Team
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     */

    /**
     * Serializes a value into a representation that should reasonably suggest
     * both the type and value, and be serializable into JSON.
     *
     * @package raven
     */
    class Raven_ReprSerializer extends Raven_Serializer
    {

        protected function serializeValue($value)
        {
            if ($value === null) {
                return 'null';
            } else if ($value === false) {
                return 'false';
            } else if ($value === true) {
                return 'true';
            } else if (is_float($value) && (int)$value == $value) {
                return $value . '.0';
            } else if (is_integer($value) || is_float($value)) {
                return (string)$value;
            } else if (is_object($value) || gettype($value) == 'object') {
                return 'Object ' . get_class($value);
            } else if (is_resource($value)) {
                return 'Resource ' . get_resource_type($value);
            } else if (is_array($value)) {
                return 'Array of length ' . count($value);
            } else {
                return $this->serializeString($value);
            }
        }
    }
