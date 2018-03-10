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

final class UnsignedIntegerObject implements CBORObject
{
    private const MAJOR_TYPE = 0b000;

    /**
     * @var int
     */
    private $additionalInformation;

    /**
     * @var null|mixed
     */
    private $data;

    /**
     * CBORObject constructor.
     *
     * @param int         $additionalInformation
     * @param null|string $data
     */
    private function __construct(int $additionalInformation, ?string $data)
    {
        $this->additionalInformation = $additionalInformation;
        $this->data = $data;
    }

    /**
     * @param int         $additionalInformation
     * @param null|string $data
     *
     * @return UnsignedIntegerObject
     */
    public static function create(int $additionalInformation, ?string $data): self
    {
        return new self($additionalInformation, $data);
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
        return $this->additionalInformation;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getLength(): ?string
    {
        return null;
    }

    /**
     * @return string
     */
    public function getNormalizedData(): string
    {
        if (null === $this->data) {
            return strval($this->additionalInformation);
        }

        return gmp_strval(gmp_init(bin2hex($this->data), 16), 10);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $result = chr(self::MAJOR_TYPE << 5 | $this->additionalInformation);
        if (null !== $this->data) {
            $result .= $this->data;
        }

        return $result;
    }
}