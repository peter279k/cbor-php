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

namespace CBOR\Test\Type;

use CBOR\ByteStringObject;
use CBOR\StringStream;

/**
 * @covers \CBOR\ByteStringObject
 */
final class ByteStringObjectTest extends BaseTestCase
{
    /**
     * @test
     * @dataProvider getData
     */
    public function aByteStringObjectCanBeCreated(string $string, int $expectedAdditionalInformation, int $expectedLength, string $expectedEncodedObject)
    {
        $object = ByteStringObject::create($string);

        self::assertEquals(0b010, $object->getMajorType());
        self::assertEquals($expectedAdditionalInformation, $object->getAdditionalInformation());
        self::assertEquals($string, $object->getValue());
        self::assertEquals($expectedLength, $object->getLength());
        self::assertEquals($string, $object->getNormalizedData());

        $binary = (string) $object;
        self::assertEquals(hex2bin($expectedEncodedObject), $binary);

        $stream = new StringStream($binary);
        $decoded = $this->getDecoder()->decode($stream);

        self::assertEquals(0b010, $decoded->getMajorType());
        self::assertEquals($expectedAdditionalInformation, $decoded->getAdditionalInformation());
        self::assertEquals($string, $decoded->getValue());
        self::assertEquals($expectedLength, $decoded->getLength());
        self::assertEquals($string, $decoded->getNormalizedData());
    }

    public function getData(): array
    {
        return [
            ['Hello', 5, 5, '4548656c6c6f'],
            ['(｡◕‿◕｡)', 17, 17, '5128efbda1e29795e280bfe29795efbda129'],
            ['HelloHelloHelloHelloHello', 24, 25, '581948656c6c6f48656c6c6f48656c6c6f48656c6c6f48656c6c6f'],
        ];
    }
}
