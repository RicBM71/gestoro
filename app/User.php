<?php

namespace App;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $dates =['blocked_at','login_at','fecha_expira'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','lastname', 'email', 'username', 'huella','password', 'avatar','blocked',
         'blocked_at','empresa_id','login_at','expira','fecha_expira','username_umod'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setHuellaAttribute($huella)
    {
        $this->attributes['huella'] = strtoupper($huella);

    }
// public function setPasswordAttribute($password)
    // {
    //     $this->attributes['password'] =  Hash::make($password);
    // }

    public function getAvatarAttribute($avatar){
        if (is_null($avatar)) return '#';
        else return $avatar;
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
    * @param
    * $this es una instancia del usuario actual
    */
    public function scopePermitidos($query)
    {

        if (esRoot())
            return $query->with('roles');
        else {
            if (session('aislar_empresas')){
                return $query->join('empresa_user','user_id','=','users.id')
                            ->where('users.id', '>', 1)
                            //->where('blocked', false)
                            ->whereIn('empresa_user.empresa_id',session('empresas_usuario'))
                            ->with('roles');
            }
            else{
                return $query->where('users.id', '>', 1)->with('roles');
            }
        }

        if (auth()->user()->can('view', $this)){ // busca la política e UserPolicy, pasar instancia
            return $query; // retorna el query builder sin restricciones
        }else{
            return $query->where('id', auth()->id());
        }
    }

    // public function getDeletedAtAttribute($value)
    // {

    //     return "2019-02-26";
    //     //return Carbon::now()->toDateTimeString();

    //     return is_null($value)
    //         ? null
    //         : Carbon::$value->format('Y-m-d');
    // }

    // public function setDeletedAtAttribute($deleted_at)
    // {
    //     $this->attributes['deleted_at'] = $deleted_at
    //         ? Carbon::createFromFormat('Y-m-d', $deleted_at)
    //         : null;
    // }


    // creamos la relación uno a uno, un usario tendrá una o varias fotos
    public function fotos()
    {
        return $this->hasMany(Avatar::class);
    }

    /**
    *   Reescribimos método de CanResetPassword
    */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

       // establecemos la relación muchos a muchos
    public function empresas()
    {
        return $this->belongsToMany(Empresa::class);
    }

    public function syncEmpresas($empresas)
    {
        $empresasIds = collect($empresas)->map(function($empresa){
            return Empresa::find($empresa) ? $empresa : false;
        });


         return $this->empresas()->sync($empresasIds);
    }

}
