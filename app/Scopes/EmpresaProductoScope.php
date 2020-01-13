<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class EmpresaProductoScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {

        $empresa_id =  session()->get('empresa')->id;

        $builder->where('empresa_id', '=', $empresa_id)
                ->orWhere('destino_empresa_id', '=', $empresa_id)
                ->orWhere('estado_id', '=', 5);
    }
}
