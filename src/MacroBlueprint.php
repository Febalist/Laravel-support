<?php

namespace Febalist\Laravel\Support;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use RuntimeException;

/** @mixin Blueprint */
class MacroBlueprint
{
    public function model($reference, $nullable = false, $cascade = null)
    {
        $reference = $this->parseReference($reference);

        $cascade = $cascade ?? !$nullable;
        $onDelete = $cascade ? 'CASCADE' : ($nullable ? 'SET NULL' : 'NO ACTION');

        $type = DB::getSchemaBuilder()->getColumnType($reference['table'], $reference['key']);
        $type = $type === 'bigint' ? 'bigInteger' : 'integer';

        $fluent = $this->addColumn($type, $reference['column'], [
            'autoIncrement' => false,
            'unsigned' => true,
        ])->index();
        if ($nullable) {
            $fluent->nullable();
        }
        $this->foreign($reference['foreign'])->references($reference['key'])->on($reference['table'])
            ->onUpdate('CASCADE')->onDelete($onDelete);

        return $fluent;
    }

    public function dropModel($reference)
    {
        $reference = $this->parseReference($reference);

        $this->dropForeign($reference['foreign']);
        $this->dropColumn($reference['column']);
    }

    public function email($column = 'email')
    {
        return $this->string($column, 254)->unique();
    }

    public function password($column = 'password', $length = 95)
    {
        return $this->string($column, $length);
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
                throw new RuntimeException('Invalid column');
            }
        } else {
            $key = $reference[1] ?? 'id';
            $column = $reference[2] ?? Str::singular($table).'_'.$key;
        }

        $foreign = [$column];

        return compact('column', 'foreign', 'table', 'key');
    }
}
