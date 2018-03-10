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

final class OtherTest extends BaseTestCase
{
    /**
     * @test
     * @dataProvider getDataSet
     *
     * @param string $data
     */
    public function aSignedIntegerCanBeParsed(string $data)
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, hex2bin($data));
        rewind($stream);

        $object = $this->getDecoder()->decode($stream);
        self::assertEquals($data, bin2hex($object->__toString()));
    }

    /**
     * @return array
     */
    public function getDataSet(): array
    {
        return [
            [
                'f4',
            ], [
                'f5',
            ], [
                'f6',
            ], [
                'f7',
            ], [
                'f0',
            ], [
                'f818',
            ], [
                'f8ff',
            ],
        ];
    }
}