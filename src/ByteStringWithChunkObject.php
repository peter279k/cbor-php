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

namespace CBOR;

final class ByteStringWithChunkObject implements CBORObject
{
    private const MAJOR_TYPE = 0b010;
    private const ADDITION_INFORMATION = 0b00011111;

    /**
     * @var ByteStringObject[]
     */
    private $data;

    /**
     * CBORObject constructor.
     *
     * @param ByteStringObject[] $data
     */
    private function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param ByteStringObject[] $data
     *
     * @return ByteStringWithChunkObject
     */
    public static function create(array $data): self
    {
        return new self($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getMajorType(): int
    {
        return self::MAJOR_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalInformation(): int
    {
        return self::ADDITION_INFORMATION;
    }

    public function getLength(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getNormalizedData(): string
    {
        $result = '';
        foreach ($this->data as $object) {
            $result .= $object->getNormalizedData();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $result = chr(self::MAJOR_TYPE << 5 | self::ADDITION_INFORMATION);
        foreach ($this->data as $object) {
            $result .= $object->__toString();
        }
        $result .= hex2bin('FF');

        return $result;
    }
}