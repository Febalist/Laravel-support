<?php

namespace Febalist\Laravel\Support;

use RuntimeException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;

/** @mixin \Illuminate\Database\Eloquent\Model */
trait Ordered
{
    public function scopeOrdered(Builder $builder)
    {
        foreach (array_wrap($this->orderBy ?? $this->getKeyName()) as $column) {
            $direction = 'asc';

            if (starts_with($column, '-')) {
                $direction = 'desc';
                $column = str_after($column, '-');
            }

            if (str_contains($column, '.')) {
                [$relation, $column] = explode('.', $column);
                $builder->orderByRelation($relation, $column, $direction);
            } else {
                $builder->orderBy($column, $direction);
            }
        }

        return $builder;
    }

    public function scopeOrderByRelation(Builder $builder, $relation, $column, $direction = 'asc')
    {
        /** @var Relation $relation */
        $relation = $this->$relation();

        if ($relation instanceof BelongsTo) {
            $relation_table = $relation->getRelated()->getTable();

            $builder->join(
                $relation_table,
                $relation->getQualifiedOwnerKeyName(),
                '=',
                $relation->getQualifiedForeignKeyName()
            );
        } elseif ($relation instanceof HasOne) {
            $relation_table = $relation->getRelated()->getTable();

            $builder->join(
                $relation_table,
                $relation->getQualifiedParentKeyName(),
                '=',
                $relation->getQualifiedForeignKeyName()
            );
        } else {
            throw new RuntimeException('Invalid relation');
        }

        $table = $builder->getQuery()->from;

        return $builder->orderBy("$relation_table.$column", $direction)
            ->select("$table.*");
    }

    public function scopeOrderByRelationDesc(Builder $builder, $relation, $column)
    {
        return $this->scopeOrderByRelation($builder, $relation, $column, 'desc');
    }
}
