<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace CBOR\OtherObject;

use CBOR\OtherObject as Base;

final class HalfPrecisionFloatObject extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function supportedAdditionalInformation(): array
    {
        return [25];
    }

    /**
     * {@inheritdoc}
     */
    public static function createFromLoadedData(int $additionalInformation, ?string $data): Base
    {
        return new self($additionalInformation, $data);
    }

    /**
     * @param string $value
     *
     * @return HalfPrecisionFloatObject
     */
    public static function create(string $value): self
    {
        if (mb_strlen($value, '8bit') !== 4) {
            throw new \InvalidArgumentException('The value is not a valid half precision floating point');
        }

        return new self(25, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getNormalizedData(bool $ignoreTags = false)
    {
        $half = gmp_intval(gmp_init(bin2hex($this->data), 16));
        $exp = ($half >> 10) & 0x1f;
        $mant = $half & 0x3ff;

        if ($exp === 0) {
            $val = $mant * pow(2, -24);
        } elseif ($exp !== 0b11111) {
            $val = ($mant + (1 << 10)) * pow(2, $exp - 25);
        } else {
            $val = $mant === 0 ? INF : NAN;
        }

        return $half >> 15 ? -$val : $val;
    }

    /**
     * @return int
     */
    public function getExponent(): int
    {
        $half = gmp_intval(gmp_init(bin2hex($this->data), 16));

        return ($half >> 10) & 0x1f;
    }

    /**
     * @return int
     */
    public function getMantissa(): int
    {
        $half = gmp_intval(gmp_init(bin2hex($this->data), 16));

        return $half & 0x3ff;
    }

    /**
     * @return int
     */
    public function getSign(): int
    {
        $half = gmp_intval(gmp_init(bin2hex($this->data), 16));

        return $half >> 15 ? -1 : 1;
    }
}
