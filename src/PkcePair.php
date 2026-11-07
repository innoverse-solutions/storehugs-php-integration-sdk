<?php

declare(strict_types=1);

namespace StoreHugs\Integration;

final readonly class PkcePair
{
    public function __construct(
        public string $verifier,
        public string $challenge,
    ) {}
}
