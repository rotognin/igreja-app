<?php

namespace Funcoes\Interfaces;

interface Authenticatable
{
    public static function loadFromRequest(): Authenticatable | null;

    public function getUsername(): string;

    public function checkAction(string $action): bool;

    public function logout(): void;

    public static function hashPassword(string $password): string;

    public static function verifyPassword(string $password, string $hash): bool;
}
