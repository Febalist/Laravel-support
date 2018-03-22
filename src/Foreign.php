<?php

namespace Febalist\LaravelSupport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;

class Foreign
{
    public static function create(Blueprint $blueprint, $reference, $nullable = false, $cascade = null)
    {
        $reference = static::parseReference($reference);

        $cascade = $cascade === null ? !$nullable : $cascade;
        $onDelete = $cascade ? 'CASCADE' : ($nullable ? 'SET NULL' : 'NO ACTION');

        $fluent = $blueprint->unsignedInteger($reference['column'])->index();
        if ($nullable) {
            $fluent->nullable();
        }
        $blueprint->foreign($reference['foreign'])->references($reference['key'])->on($reference['table'])
            ->onUpdate('CASCADE')->onDelete($onDelete);

        return $fluent;
    }

    public static function drop(Blueprint $blueprint, $reference)
    {
        $reference = static::parseReference($reference);

        $blueprint->dropForeign($reference['foreign']);
        $blueprint->dropColumn($reference['column']);
    }

    protected static function parseReference($reference)
    {
        if (!is_array($reference)) {
            $reference = [$reference];
        }

        $table = $reference[0];
        if (class_exists($table)) {
            $model = new $table();
            if ($model instanceof Model) {
                $table = $model->getTable();
                $key = $model->getKeyName();
                $column = $reference[1] ?? $model->getForeignKey();
            } else {
                throw new \Exception('Invalid column');
            }
        } else {
            $key = $reference[1] ?? 'id';
            $column = $reference[2] ?? str_singular($table).'_'.$key;
        }

        $foreign = [$column];

        return compact('column', 'foreign', 'table', 'key');
    }
}
