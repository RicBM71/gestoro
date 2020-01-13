<?php

namespace App\Rules;

use App\Libro;
use Illuminate\Contracts\Validation\Rule;

class LibroUniqueRule implements Rule
{
    protected $ejercicio;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($ejercicio, $id=0)
    {
        $this->ejercicio = $ejercicio;
        $this->id = $id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

        if ($this->id > 0)
            $libros = Libro::where('grupo_id', $value)
                            ->where('ejercicio', $this->ejercicio)
                            ->where('id', '<>', $this->id)
                            ->count();
        else
            $libros = Libro::where('grupo_id', $value)
                            ->where('ejercicio', $this->ejercicio)
                            ->count();

        return $libros > 0 ? false : true;


    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Ya existe un libro para el ejercicio y grupo';
    }
}
