<?php

namespace Spatie\LaravelData\Attributes\Validation;

use Attribute;
use Closure;
use Exception;
use Illuminate\Validation\Rules\Unique as BaseUnique;
use Spatie\LaravelData\Support\Validation\ValidationPath;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Unique extends ObjectValidationAttribute
{
    protected BaseUnique $rule;

    public function __construct(
        null|string $table = null,
        null|string $column = 'NULL',
        null|string $connection = null,
        null|string $ignore = null,
        null|string $ignoreColumn = null,
        bool $withoutTrashed = false,
        string $deletedAtColumn = 'deleted_at',
        ?Closure $where = null,
        ?BaseUnique $rule = null
    ) {
        $table = $this->normalizePossibleRouteReferenceParameter($table);
        $column = $this->normalizePossibleRouteReferenceParameter($column);
        $connection = $this->normalizePossibleRouteReferenceParameter($connection);
        $ignore = $this->normalizePossibleRouteReferenceParameter($ignore);
        $ignoreColumn = $this->normalizePossibleRouteReferenceParameter($ignoreColumn);
        $withoutTrashed = $this->normalizePossibleRouteReferenceParameter($withoutTrashed);
        $deletedAtColumn = $this->normalizePossibleRouteReferenceParameter($deletedAtColumn);

        if ($table === null && $rule === null) {
            throw new Exception('Could not create unique validation rule, either table or a rule is required');
        }

        $rule ??= new BaseUnique(
            $connection ? "{$connection}.{$table}" : $table,
            $column
        );

        if ($withoutTrashed) {
            $rule->withoutTrashed($deletedAtColumn);
        }

        if ($ignore) {
            $rule->ignore($ignore, $ignoreColumn);
        }

        if ($where) {
            $rule->where($where);
        }

        $this->rule = $rule;
    }

    public function getRule(ValidationPath $path): object|string
    {
        return $this->rule;
    }

    public static function keyword(): string
    {
        return 'unique';
    }

    public static function create(string ...$parameters): static
    {
        return new static(rule: new BaseUnique($parameters[0], $parameters[1]));
    }
}
