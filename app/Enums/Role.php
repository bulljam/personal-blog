<?php

namespace App\Enums;

enum Role: string
{
    case AUTHOR = 'author';
    case READER = 'reader';

    public function getLabel(): string
    {
        return match ($this) {
            self::AUTHOR => 'Content Author',
            self::READER => 'Blog Reader',
            default => 'Unknown role'
        };
    }
}
