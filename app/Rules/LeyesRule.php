<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class LeyesRule implements Rule
{
    protected $clase_id;
    protected $quilates_oro=array('9','10','14','15','16','17','18','19','20','21','22','23','24');
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($clase_id)
    {
        $this->clase_id = $clase_id;
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
        if ($this->clase_id > 1)
            return true;

        return (in_array($value, $this->quilates_oro));

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Indicar quilates';
    }
}
