<?php

namespace App;

use Illuminate\Support\Facades\DB;
use App\Scopes\EmpresaProductoScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

Class Producto extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'empresa_id','almacen_id', 'nombre','nombre_interno','clase_id',
        'quilates','caracteristicas','peso_gr','precio_coste', 'precio_venta',
        'compra_id', 'ref_pol','estado_id','etiqueta_id','referencia', 'univen',
        'destino_empresa_id','iva_id','cliente_id','online','deleted_at','notas','username',
        'garantia_id','meses_garantia','fecha_ultima_revision','stock','descuento',
        'marca_id', 'categoria_id', 'ecommerce_id','descripcion'
    ];

    protected $casts = [
        'fecha_ultima_revision' => 'date:Y-m-d',
    ];

    protected $appends = [
        'margen',
    ];

    /**
     *
     * Añadimos global scope para filtrado por empresa.
     *
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EmpresaProductoScope);
    }

    public function setNombreAttribute($nombre)
    {
        $this->attributes['nombre'] = strtoupper($nombre);

    }

    public function setNombreInternoAttribute($nombre_interno)
    {
        $this->attributes['nombre_interno'] = strtoupper($nombre_interno);

    }


    // public function setCaracteristicasAttribute($caracteristicas)
    // {
    //     $this->attributes['caracteristicas'] = strtoupper($caracteristicas);

    // }

    public function setReferenciaAttribute($referencia)
    {
        $this->attributes['referencia'] = strtoupper($referencia);

    }

    public function setRefPolAttribute($ref_pol)
    {
        $this->attributes['ref_pol'] = strtoupper($ref_pol);

    }

    // public function getQuilatesAttribute()
    // {
    //     return $this->attributes['quilates'].'K';
    //     //return $this->quilates.'K';
    // }

    public function getMargenAttribute(){

        return $this->precio_venta - $this->precio_coste;

    }

    public function compra()
    {
    	return ($this->belongsTo(Compra::class));
    }

    public function empresa()
    {
    	return ($this->belongsTo(Empresa::class));
    }

    public function clase()
    {
    	return ($this->belongsTo(Clase::class));
    }

    public function estado()
    {
    	return ($this->belongsTo(Estado::class));
    }

    public function iva()
    {
    	return ($this->belongsTo(Iva::class));
    }

    public function garantia()
    {
    	return ($this->belongsTo(Garantia::class));
    }

    public function cliente()
    {
    	return ($this->belongsTo(Cliente::class));
    }

    public function etiqueta()
    {
    	return ($this->belongsTo(Etiqueta::class));
    }

    public function marca()
    {
    	return ($this->belongsTo(Marca::class));
    }

    public function categoria()
    {
    	return ($this->belongsTo(Categoria::class));
    }

    public function albalins(){
        return ($this->hasMany(Albalins::class));
    }

    public function destino(){
        return ($this->belongsTo(Empresa::class,'destino_empresa_id'));
    }

    public function tags(){
        return $this->belongsToMany(Tag::class);
    }

    public static function scopeReferencia($query, $referencia){

        $referencia = \strtoupper($referencia);

        if ($referencia > ''){
            if (strpos($referencia,'.') === false){
                return $query->where('referencia','like', '%'.$referencia.'%');
            }
            else{
                $referencia = str_replace('.','',$referencia);
                return $query->where('id','=', $referencia);
            }
        }

        return $query;

    }

    public static function scopeCompraId($query, $compra_id){

        return $query->where('compra_id', '=', $compra_id);

    }

    public static function scopeFecha($query, $d,$h,$tipo){

        if ($d == null && $h==null)
            return $query;

        if ($d == null && $h != null)
            $d = $h;
        if ($d != null && $h == null)
            $h = $d;

        if ($tipo == "C")
            return $query->whereDate('created_at','>=', $d)
                         ->whereDate('created_at','<=', $h);
        elseif ($tipo == "R")
            return $query->whereDate('fecha_ultima_revision','>=', $d)
                         ->whereDate('fecha_ultima_revision','<=', $h);
        else
            return $query->whereDate('updated_at','>=', $d)
                         ->whereDate('updated_at','<=', $h);
    }


    public static function scopeFechaMod($query, $f){

        if ($f !=null)
            return $query->whereDate('updated_at','=', $f);

        return $query;
    }

    public static function scopeDestino($query, $empresa_id){

        if ($empresa_id !=null)
            return $query->where('destino_empresa_id','=', $empresa_id);

        return $query;
    }

    public static function scopeClase($query, $clase_id){

        if ($clase_id != null)
            return $query->where('clase_id','=', $clase_id);

        return $query;

    }

    public static function scopeEstado($query, $estado_id){

        if ($estado_id !=null)
            return $query->where('estado_id','=', $estado_id);

        return $query;

    }

    public static function scopeEmpresa($query, $empresa_id){

        if ($empresa_id !=null)
            return $query->where('empresa_id','=', $empresa_id);

        return $query;

    }

    public static function scopeLocalizacion($query, $sin_scope){

        if ($sin_scope == true)
            return $query->whereIn('empresa_id', session('empresas_usuario'))->orWhereIn('destino_empresa_id',session('empresas_usuario'));

        return $query->where('empresa_id', session('empresa_id'))->orWhere('destino_empresa_id',session('empresa_id'));

    }

    public static function scopeAlta($query, $alta){
        if ($alta == 'S')
            return $query;
        else if($alta == 'N')
            return $query->onlyTrashed();
        else
            return $query->withTrashed();

    }


    /**
     * hay que hacer join con clases para que funcione
     *
     * @param [type] $query
     * @param [type] $grupo_id
     * @return void
     */
    public static function scopeGrupo($query, $grupo_id){

        if ($grupo_id != null)
            return $query->where('grupo_id','=', $grupo_id);

        return $query;

    }

    public static function scopeNotasNombre($query, $texto){

        if ($texto != null || $texto > ''){

            if ($texto[0] ==':'){
                $texto = str_replace(':','',$texto);
                return $query->where('notas','like', '%'.$texto.'%');
            }
            elseif ($texto[0] =='='){
                 $texto = str_replace('=','',$texto);
                 return $query->where('nombre_interno','like', '%'.$texto.'%');
            }
            else
                return $query->where('nombre','like', '%'.$texto.'%');

        }

        return $query;

    }

    public static function scopeRefPol($query, $ref){

        //return $query->where('ref_pol','like', $ref.'%');

        if ($ref != null){
            if ($ref[0] == ':'){
                $ref = str_replace(':','',$ref);
                return $query->where('ref_pol','like', '%'.$ref.'%');
            }else
                return $query->where('ref_pol','like', '%'.$ref.'%');
        }


        return $query;

    }

    public static function scopeInternos($query, $value){

        if ($value == "I")
            return $query->where('compra_id','>', 0);
        elseif ($value == "E")
            return $query->whereNull('compra_id');

        return $query;

    }


    public static function scopeNombre($query, $nombre){

        if ($nombre != null){
            return $query->where('nombre','like', '%'.$nombre.'%');
        }

        return $query;

    }

    public static function scopeCaracteristicas($query, $caracteristicas){

        if ($caracteristicas != null){
            return $query->where('caracteristicas','like', '%'.$caracteristicas.'%');
        }

        return $query;

    }




    public static function scopePrecioPeso($query, $precio){

        if ($precio != null){
            if ($precio[0] ==':'){
                $precio = str_replace(':','',$precio);
                return $query->where('precio_venta','=', $precio);
            }elseif ($precio[0] == '='){
                $precio = str_replace('=','',$precio);
                return $query->where('precio_coste','=', $precio);
            }else
                return $query->where('peso_gr','=', $precio);
        }

        return $query;

    }

    public static function scopeQuilates($query, $quilates){

        if ($quilates != null){
            return $query->where('quilates', $quilates);
        }

        return $query;

    }

    public static function scopeOnline($query, $online){

        if ($online){
            return $query->where('online',$online);
        }

        return $query;

    }

    public static function scopeAsociado($query, $cliente_id){

        if ($cliente_id == -1)
            return $query->whereNull('cliente_id');
        elseif ($cliente_id > 0)
            return $query->where('cliente_id','=', $cliente_id);

        return $query;

    }

    public static function scopeCategoria($query, $categoria_id){

        if ($categoria_id != null)
            return $query->where('categoria_id','=', $categoria_id);

        return $query;

    }

    public static function scopeEtiqueta($query, $etiqueta_id){

        if ($etiqueta_id != null)
            return $query->where('etiqueta_id','=', $etiqueta_id);

        return $query;

    }


    public static function scopeMarca($query, $marca_id){

        if ($marca_id != null)
            return $query->where('marca_id','=', $marca_id);

        return $query;

    }


    public static function productosREBU($referencia)
    {
        if (strpos($referencia,'.') !== false){
            $nombre = str_replace('.','',$referencia);
            $referencia = null;
        }else{
            $nombre = null;
        }

        // $k = Producto::select(DB::raw('id AS value, CONCAT(referencia, " " , nombre) AS text, (stock - (IFNULL((SELECT SUM(unidades) FROM '.DB::getTablePrefix().'albalins WHERE producto_id = '.DB::getTablePrefix().'productos.id and deleted_at is null), 0))) AS mi_stock'))
        // ->referencia($referencia)
        // ->nombre($nombre)
        // ->where('iva_id', 2)
        // ->whereIn('estado_id', [2,5,6])
        // ->havingRaw('mi_stock >= 1')
        // ->orderBy('referencia', 'asc')
        // ->toSql();

        //select id AS value, CONCAT(referencia, " " , nombre) AS text, IFNULL((select SUM(unidades) from `klt_albalins` where `producto_id` = klt_productos.id and `deleted_at` is null), 0) from `klt_productos` where `referencia` like '%GD30050%' and `iva_id` = 2 and `estado_id` in (2, 5, 6) and `klt_productos`.`deleted_at` is null and (`empresa_id` = 1 or `destino_empresa_id` = 1 or `estado_id` = 5) order by `referencia` asc
        return Producto::select(DB::raw('id AS value, CONCAT(referencia, " " , nombre) AS text, (stock - (IFNULL((SELECT SUM(unidades) FROM '.DB::getTablePrefix().'albalins WHERE producto_id = '.DB::getTablePrefix().'productos.id and deleted_at is null), 0))) AS mi_stock'))
                ->referencia($referencia)
                ->nombre($nombre)
                ->where('iva_id', 2)
                ->whereIn('estado_id', [2,5,6])
                ->havingRaw('mi_stock >= 1')
                ->orderBy('referencia', 'asc')
                ->get();
    }

    public static function productosGenericos($referencia)
    {
        if (strpos($referencia,'.') !== false){
            $nombre = str_replace('.','',$referencia);
            $referencia = null;
        }else{
            $nombre = null;
        }

        return Producto::select(DB::raw('id AS value, CONCAT(referencia, " " , nombre) AS text'))
                    ->referencia($referencia)
                    ->nombre($nombre)
                    ->where('iva_id', '<>', 2)
                    ->whereIn('estado_id', [2,5,6])
                    // ->whereNull('deleted_at')
                    ->orderBy('referencia', 'asc')
                    ->get();

    }

    public static function productosEnvio()
    {

        return Producto::select(DB::raw('id AS value, CONCAT(referencia, " " , nombre) AS text'))
                    ->where('estado_id', 6)
                    ->whereNull('deleted_at')
                    ->orderBy('referencia', 'asc')
                    ->get();

    }

    public static function getStockReal($producto_id){


        $q = Producto::withOutGlobalScope(EmpresaProductoScope::class)
                        ->select(DB::raw(' (stock - (IFNULL((SELECT SUM(unidades) FROM '.DB::getTablePrefix().'albalins,'.DB::getTablePrefix().'albaranes WHERE producto_id = '.DB::getTablePrefix().'productos.id and '.DB::getTablePrefix().'albalins.deleted_at is null AND albaran_id = '.DB::getTablePrefix().'albaranes.id AND fase_id >= 10), 0))) AS mi_stock'))
                        ->where('id', $producto_id)
                        ->first();

        if ($q != null){
            return $q->mi_stock;
        }

        return 0;
    }




}
