<?php

namespace App\Custom;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('formatUserRoles', [$this, 'formatUserRoles']),
        ];
    }

    public function formatUserRoles(array $roles = []): string
    {
        $formattedRoles = '';

        $size = count($roles);
        foreach ($roles as $role) {
            $formattedRole = ucfirst(strtolower(explode("_", $role)[1]));

            $formattedRoles .= $formattedRole;
            $size -= 1;

            if ($size > 0) {
                $formattedRoles .= ", ";
            }
        }
        return $formattedRoles;
    }
}