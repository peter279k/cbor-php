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

namespace CBOR\Tag;

use CBOR\CBORObject;
use CBOR\ListObject;
use CBOR\SignedIntegerObject;
use CBOR\TagObject as Base;
use CBOR\UnsignedIntegerObject;

final class BigFloatTag extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function getTagId(): int
    {
        return 5;
    }

    /**
     * {@inheritdoc}
     */
    public static function createFromLoadedData(int $additionalInformation, ?string $data, CBORObject $object): Base
    {
        return new self($additionalInformation, $data, $object);
    }

    /**
     * {@inheritdoc}
     */
    public static function create(CBORObject $object): Base
    {
        if (!$object instanceof ListObject || count($object) !== 2) {
            throw new \InvalidArgumentException('This tag only accepts a ListObject object that contains an exponent and a mantissa.');
        }
        $e = $object->get(0);
        if (!$e instanceof UnsignedIntegerObject && !$e instanceof SignedIntegerObject) {
            throw new \InvalidArgumentException('The exponent must be a Signed Integer or an Unsigned Integer object.');
        }
        $m = $object->get(1);
        if (!$m instanceof UnsignedIntegerObject && !$m instanceof SignedIntegerObject && !$m instanceof NegativeBigIntegerTag && !$m instanceof PositiveBigIntegerTag) {
            throw new \InvalidArgumentException('The mantissa must be a Positive or Negative Signed Integer or an Unsigned Integer object.');
        }

        return new self(5, null, $object);
    }

    /**
     * @param CBORObject $e
     * @param CBORObject $m
     *
     * @return Base
     */
    public static function createFromExponentAndMantissa(CBORObject $e, CBORObject $m): Base
    {
        $object = ListObject::create([$e, $m]);

        return self::create($object);
    }

    /**
     * {@inheritdoc}
     */
    public function getNormalizedData(bool $ignoreTags = false)
    {
        if ($ignoreTags) {
            return $this->object->getNormalizedData($ignoreTags);
        }

        if (!$this->object instanceof ListObject || count($this->object) !== 2) {
            return $this->object->getNormalizedData($ignoreTags);
        }
        $e = $this->object->get(0);
        $m = $this->object->get(1);

        if (!$e instanceof UnsignedIntegerObject && !$e instanceof SignedIntegerObject) {
            return $this->object->getNormalizedData($ignoreTags);
        }
        if (!$m instanceof UnsignedIntegerObject && !$m instanceof SignedIntegerObject && !$m instanceof NegativeBigIntegerTag && !$m instanceof PositiveBigIntegerTag) {
            return $this->object->getNormalizedData($ignoreTags);
        }

        return rtrim(
            bcmul(
                $m->getNormalizedData($ignoreTags),
                bcpow(
                    '2',
                    $e->getNormalizedData($ignoreTags),
                    100),
                100),
            '0'
        );
    }
}
