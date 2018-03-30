<?php

namespace Febalist\LaravelSupport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use ReflectionClass;
use ReflectionMethod;

class Macro
{
    public static function register()
    {
        $macro = new static();

        $class = new ReflectionClass($macro);
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $name = $method->getName();
            Blueprint::macro($method->name, function (...$arguments) use ($macro, $name) {
                return $macro->$name($this, ...$arguments);
            });
        }
    }

    public function model(Blueprint $blueprint, $reference, $nullable = false, $cascade = null)
    {
        $reference = $this->parseReference($reference);

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

    public function dropModel(Blueprint $blueprint, $reference)
    {
        $reference = $this->parseReference($reference);

        $blueprint->dropForeign($reference['foreign']);
        $blueprint->dropColumn($reference['column']);
    }

    public function name(Blueprint $blueprint, $column = 'name', $length = null)
    {
        return $blueprint->string($column, $length);
    }

    public function description(Blueprint $blueprint, $column = 'description')
    {
        return $blueprint->text($column);
    }

    public function total(Blueprint $blueprint, $column = 'total', $total = 9, $places = 2)
    {
        return $blueprint->float($column, $total, $places);
    }

    public function amount(Blueprint $blueprint, $column = 'amount')
    {
        return $blueprint->unsignedInteger($column);
    }

    protected function parseReference($reference)
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
