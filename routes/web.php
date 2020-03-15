<?php


Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/dash', 'HomeController@dash')->name('dash');
Route::post('/profile/avatar', 'HomeController@avatar');
Route::put('/profile/destroy', 'HomeController@destroy');

// Route::get('/test', 'HomeController@test');

Route::get('/expired', 'HomeController@expired')->name('expired');


Route::group([
    //'as' => '.admin' ver php artisan r:l para ver problema admin.admin.
    'prefix' => 'admin',
    'namespace' => 'Admin',
    'middleware' => ['auth']],
    function (){

        Route::post('users/password/update', 'UsersController@updatePassword');

    }
);


Route::group([
    //'as' => '.admin' ver php artisan r:l para ver problema admin.admin.
    'prefix' => 'admin',
    'namespace' => 'Admin',
    'middleware' => ['auth','password']],
    function (){

        Route::resource('parametros', 'ParametrosController', ['only'=>['index','update'], 'as' => 'root']);
        Route::post('parametros/main', 'ParametrosController@main');
        Route::put('parametros/main/delete', 'ParametrosController@deletemain');
        Route::post('parametros/section', 'ParametrosController@section');
        Route::put('parametros/section/delete', 'ParametrosController@deletesection');

        Route::resource('roles', 'RolesController', ['as' => 'root']);
        Route::resource('permissions', 'PermissionsController', ['except'=>'show', 'as' => 'root']);

        Route::resource('users', 'UsersController', ['as' => 'admin']);

        Route::middleware(['role_or_permission:Root|users'])
            ->put('users/{user}/roles','UsersRolesController@update');

        // Route::middleware('role:Root|Admin')
        //     ->put('users/{user}/permissions','UsersPermissionsController@update');
        Route::middleware(['role_or_permission:Root|users'])
            ->put('users/{user}/permissions','UsersPermissionsController@update');

        Route::middleware(['role_or_permission:Root|users'])
            ->put('users/{user}/empresas','UsersEmpresasController@update');


        Route::put('users/{user}/empresa', 'UsersController@updateEmpresa');
        Route::put('users/{user}/reset', 'UsersController@reset');

        Route::post('users/{user}/avatar', 'AvatarsController@store');
        Route::delete('avatars/{user}/delete', 'AvatarsController@destroy');


        Route::resource('empresas', 'EmpresasController', ['except'=>'show','as' => 'admin']);
        Route::post('empresas/{empresa}/logo', 'EmpresasController@logo');
        Route::put('empresas/{empresa}/logo/delete', 'EmpresasController@deletelogo');
        Route::post('empresas/{empresa}/fondo', 'EmpresasController@fondo');
        Route::put('empresas/{empresa}/fondo/delete', 'EmpresasController@deletefondo');

        Route::resource('ipusers', 'IpUsersController', ['only'=>['store','destroy'], 'as' => 'admin']);

    }
);


Route::group([
    'prefix' => 'mto',
    'namespace' => 'Mto',
    'middleware' => ['auth','password']],
    function (){
        Route::resource('clientes', 'ClientesController', ['as' => 'mto']);
        Route::put('clientes/{cliente}/obs', 'ClientesController@obs');
        Route::get('clientes/{cliente}/albaranes', 'ClientesController@albaranes');
        Route::post('clientes/filtrar', 'ClientesController@filtrar');
        Route::resource('clidocs', 'ClidocsController', ['only'=>['store','destroy'],'as' => 'mto']);
        Route::get('clidocs/{cliente_id}/{compra_id?}/create', 'ClidocsController@create');

        Route::resource('almacenes', 'AlmacenesController', ['as' => 'mto']);
        Route::resource('grupos', 'GruposController', ['as' => 'mto']);
        Route::resource('ivas', 'IvasController', ['as' => 'mto']);
        Route::resource('cuentas', 'CuentasController', ['as' => 'mto']);
        Route::resource('clases', 'ClasesController', ['as' => 'mto']);
        Route::resource('libros', 'LibrosController', ['as' => 'mto']);
        Route::post('libros/filtrar', 'LibrosController@filtrar');
        Route::resource('garantias', 'GarantiasController', ['as' => 'mto']);
        Route::resource('talleres', 'TalleresController', ['as' => 'mto']);
        Route::resource('cajas', 'CajasController', ['as' => 'mto']);
        Route::resource('apuntes', 'ApuntesController', ['as' => 'mto']);

        Route::resource('cruces', 'CrucesController', ['as' => 'mto']);

        Route::resource('motivos', 'MotivosController', ['as' => 'mto']);
        Route::post('cajas/filtrar', 'CajasController@filtrar');
        Route::post('cajas/cerrar', 'CajasController@cerrar');
        Route::post('cajas/saldo', 'CajasController@saldo');
        Route::post('cajas/excel', 'CajasController@excel');

        Route::resource('productos', 'ProductosController', ['as' => 'mto']);
        Route::post('productos/filtrar', 'ProductosController@filtrar');
        Route::get('productos/print/{id}', 'PrintGarantiaDepositoController@print');
        Route::post('/productos/excel', 'ProductosController@excel');

        Route::resource('traspasos', 'TraspasosController', ['as' => 'mto']);
        Route::post('traspasos/filtrar', 'TraspasosController@filtrar');

        Route::resource('contadores', 'ContadoresController', ['as' => 'mto']);
        Route::post('contadores/filtrar', 'ContadoresController@filtrar');

        Route::post('recuentos/close', 'RecuentosController@close');
        Route::post('recuentos/reset', 'RecuentosController@reset');
        Route::resource('recuentos', 'RecuentosController', ['as' => 'mto']);
        Route::post('recuentos/filtrar', 'RecuentosController@filtrar');
        Route::post('recuentos/excel', 'RecuentosController@excel');

        // Route::middleware('role:Root|Admin')->group(function () {
        //     Route::resource('transferencias', 'TransferenciasController', ['as' => 'mto']);
        //     Route::post('transferencias/filtrar', 'TransferenciasController@filtrar');
        // });
    }
);

Route::group([
    'prefix' => 'compras',
    'namespace' => 'Compras',
    'middleware' => ['auth','password']],
    function (){
        Route::resource('compras', 'ComprasController');
        Route::put('compras/{compra}/obs', 'ComprasController@obs');
        Route::put('compras/{compra}/tipo', 'ComprasController@tipo');
        Route::put('compras/{compra}/fase', 'ComprasController@fase');
        Route::put('compras/{compra}/recogida', 'ComprasController@recogida');
        Route::post('compras/filtrar', 'ComprasController@filtrar');
        Route::post('compras/excel', 'ComprasController@excel');

        Route::get('trasladar', 'TrasladarController@index');
        Route::put('trasladar/{compra}', 'TrasladarController@update');
        Route::get('trasladar/{empresa_id}/grupo', 'TrasladarController@grupo');

        Route::get('find', 'FindComprasController@index');
        Route::post('find/compra', 'FindComprasController@find');
        Route::resource('comlines', 'ComlinesController', ['only'=>['update','store','destroy']]);
        Route::post('comlines/load', 'ComlinesController@load');
        Route::get('liquidar/{compra}/edit','LiquidarController@edit');
        Route::put('liquidar/lote', 'LiquidarController@liquidar');
        Route::get('liquidar', 'LiquidarController@index');
        Route::post('liquidar/preliquidado', 'LiquidarController@preliquidado');
        Route::put('liquidar/masivo', 'LiquidarController@masivo');
        Route::post('liquidar/mostrar', 'LiquidarController@mostrar');
        Route::put('liquidar/deshacer', 'LiquidarController@deshacer');
        Route::put('liquidar/direct', 'LiquidarController@direct');

        Route::resource('depositos', 'DepositosController',['only'=>['show','create','store','destroy']]);
        Route::get('depositos/{deposito}/compra', 'DepositosController@compra');

        Route::resource('comprar', 'ComprarController', ['only'=>['index','store','destroy']]);
        Route::resource('recuperar', 'RecuperarController', ['only'=>['index','show','store','destroy']]);

        Route::get('print/{id}', 'PrintComprasController@print')->name('compra.print');

        Route::resource('ampliaciones', 'AmpliacionesController', ['only'=>['index','show','store','destroy']]);
        // Route::get('ampliaciones/{compra}/create', 'AmpliacionesController@create');
        Route::resource('acuenta', 'AcuentaController', ['only'=>['index','show','store','destroy']]);
        Route::resource('capital', 'AmpliarCapitalController', ['only'=>['index','show','store','destroy']]);

    }
);

Route::group([
    'prefix' => 'ventas',
    'namespace' => 'Ventas',
    'middleware' => ['auth','password']],
    function (){
        Route::middleware('permission:factura')->group(function () {
            Route::get('facturacion', 'FacturacionComprasController@index');
            Route::put('facturacion/compras', 'FacturacionComprasController@compras');
            Route::put('facturacion/albaranes', 'FacturacionVentasController@albaranes');
            Route::get('facturacion/alb', 'FacturacionVentasController@index');
        });

        Route::middleware(['role:Gestor'])->group(function () {
            Route::get('facturacion/listar', 'ListarFacturasController@index');
            Route::post('facturacion/compras/listar', 'ListarFacturasController@lisrecu');
            Route::post('facturacion/compras/listar/excel', 'ListarFacturasController@excel');
            Route::post('facturacion/albaranes/listar', 'ListarFacturasController@lisfac');
            Route::post('facturacion/albaranes/listar/excel', 'ListarFacturasController@excel');
        });

        Route::get('print/{id}', 'PrintRecuController@print');
        Route::get('print/{id}/albaran', 'PrintAlbController@print')->name('albaran.print');
        Route::put('print/{albarane}/mail', 'PrintAlbController@mail')->name('albaran.mail');

        Route::resource('albaranes', 'AlbaranesController');
        Route::put('albaranes/{albarane}/facturar', 'AlbaranesController@facturar');
        Route::put('albaranes/{albarane}/desfacturar', 'AlbaranesController@desfacturar');
        Route::put('albaranes/{albarane}/facauto', 'AlbaranesController@facauto');
        Route::put('albaranes/{albarane}/fase', 'AlbaranesController@fase');
        Route::post('albaranes/filtrar', 'AlbaranesController@filtrar');
        Route::post('albaranes/excel', 'AlbaranesController@excel');

        Route::get('find', 'FindAlbaranesController@index');
        Route::post('find/albaranes', 'FindAlbaranesController@find');
        Route::put('albaranes/{albarane}/actfac', 'AlbaranesController@actfac');

        Route::get('print/{id}/taller', 'PrintHojaTallerController@print');

        Route::put('abonos/{albarane}/abonar', 'AbonosController@abonar');
        Route::put('abonos/{albarane}/cancelar', 'AbonosController@cancelar');
        Route::resource('albalins', 'AlbalinsController');
        Route::post('albalins/load', 'AlbalinsController@load');

        Route::resource('cobros', 'CobrosController',['only'=>['show','create','store','destroy']]);
        Route::get('cobros/{cobro}/albaran', 'CobrosController@albaran');

        Route::get('reubicar/{id}/albaran', 'ReubicarAlbaranesController@update');
        Route::post('reubicar', 'ReubicarAlbaranesController@reubicar');

    }
);

Route::group([
    'prefix' => 'utilidades',
    'namespace' => 'Utilidades',
    'middleware' => ['auth','password']],
    function (){
        Route::get('helpgrupos', 'HelpGruposController@index');
      //  Route::get('helpgrupos/productos', 'HelpGruposController@productos');
        Route::get('helpgrupos/{grupo_id}/clases', 'HelpGruposController@clases');
        Route::post('helpcli', 'HelpCliController@index');
        Route::post('helpcli/blacklist', 'HelpCliController@blacklist');
        Route::post('helpcli/compras', 'HelpCliController@compras');
        Route::post('helpcli/ventas', 'HelpCliController@ventas');
        Route::post('helpcli/dni', 'HelpCliController@dni');
        Route::post('helplibro', 'HelpLibroController@index');
        Route::post('helplibro/ejercicio', 'HelpLibroController@ejercicio');
        Route::get('helplibro/abiertos', 'HelpLibroController@abiertos');
        Route::post('helpdepo', 'HelpDepositosController@index');
        Route::post('helppro/vendibles', 'HelpProductoController@vendibles');
        Route::post('helppro/producto', 'HelpProductoController@producto');
        Route::post('helppro/albaranes', 'HelpProductoController@albaranes');
        Route::post('helppro/find', 'HelpProductoController@find');

        Route::get('helppro/filtro', 'HelpProductoController@filtro');
        Route::get('helpfases/compra', 'HelpFasesController@compra');
        Route::get('helpfases/venta', 'HelpFasesController@venta');
        Route::get('helpfiltroalb', 'HelpFiltroAlbController@index');
        Route::post('helptaller/ventas', 'HelpTalleresController@ventas');
        Route::put('reacli', 'ReasignarClienteController@reasignar');
        Route::get('helpapuntes', 'HelpApuntesController@index');

        Route::get('check/{ejercicio?}', 'ContadorCheckController@index');
        Route::post('intercambio', 'IntercambioController@submit');

        Route::put('reasignar/empresa/producto/{producto}', 'ReasignarEmpresaProductoController@update');
        Route::post('importar/producto', 'ImportarProductoController@store');

        Route::post('cierre', 'CierreController@submit');

     //   Route::get('helpbanco', 'HelpBancosController@index');
    }
);


Route::group([
    'prefix' => 'exportar',
    'namespace' => 'Exportar',
    'middleware' => ['auth','password']],
    function (){
        Route::middleware('role:Admin|Gestor')->group(function () {
            Route::get('/libro/index', 'PrintLibroController@index');
            Route::post('/libro/excel', 'PrintLibroController@excel');
            Route::post('/libro/portada', 'PrintLibroController@portada');
            Route::post('/libro/blanco', 'PrintLibroController@blanco');
            Route::post('/libro/completo', 'PrintLibroController@completo');
            Route::post('/libro/detalle', 'PrintLibroController@detalle');

            Route::post('/mod347/excel', 'Mod347Controller@excel');
            Route::post('/balance', 'BalanceController@balance');
            Route::post('/balance/excel', 'BalanceController@excel');
            Route::post('/operaciones', 'OperacionesController@operaciones');
            Route::post('/operaciones/excel', 'OperacionesController@excel');

            Route::post('/vendepo', 'VentasDepositoController@ventas');
            Route::post('/vendepo/excel', 'VentasDepositoController@excel');

            Route::post('/situacion', 'SituacionController@situacion');
            Route::post('/situacion/excel', 'SituacionController@excel');

            Route::post('/resconta', 'ResumenContableController@resconta');
            Route::post('/resconta/excel', 'ResumenContableController@excel');

            Route::post('/liquidados', 'LiquidadosController@liquidados');
            Route::post('/liquidados/excel', 'LiquidadosController@excel');

            Route::get('/detacom', 'DetalleComprasController@index');
            Route::post('/detacom', 'DetalleComprasController@submit');
            Route::post('/detacom/excel', 'DetalleComprasController@excel');

            Route::get('/detaven', 'DetalleVentasController@index');
            Route::post('/detaven', 'DetalleVentasController@submit');
            Route::post('/detaven/excel', 'DetalleVentasController@excel');

            Route::get('/mando', 'CuadroMandoController@index');
            Route::post('/mando', 'CuadroMandoController@submit');
            Route::post('/mando/excel', 'CuadroMandoController@excel');

            Route::get('/service', 'ServiciosTallerController@index');
            Route::post('/service', 'ServiciosTallerController@submit');
            Route::post('/service/excel', 'ServiciosTallerController@excel');

            Route::get('/metdep', 'MetalDepositoController@index');
            Route::post('/metdep', 'MetalDepositoController@submit');
            Route::post('/metdep/excel', 'MetalDepositoController@excel');

            Route::post('/apuban', 'ApuntesBancoController@submit');
            Route::post('/apuban/excel', 'ApuntesBancoController@excel');


        });

        Route::post('/inventario', 'InventarioController@inventario');
        Route::post('/inventario/excel', 'InventarioController@excel');
        Route::post('/recogidas', 'RecogidasController@submit');

    }
);


Route::group([
    'prefix' => 'etiquetas',
    'namespace' => 'Etiquetas',
    'middleware' => ['auth','password']],
    function (){

        Route::get('/aplipdf', 'ApliPdfController@index');
        Route::post('/aplipdf', 'ApliPdfController@submit');


        Route::middleware('role:Admin|Gestor')->group(function () {

            // Route::get('/service', 'ServiciosTallerController@index');
            // Route::post('/service', 'ServiciosTallerController@submit');
            // Route::post('/service/excel', 'ServiciosTallerController@excel');
        });

    }
);



Route::group([
    'prefix' => 'rfid',
    'namespace' => 'Rfid',
    'middleware' => ['auth','password']],
    function (){


        Route::post('/recuento', 'ImportRfidController@recuento');
        Route::post('/localizar', 'ImportRfidController@localizar');

        Route::get('/exportar', 'ExportRfidController@index');
        Route::post('/exportar/download', 'ExportRfidController@download');

        Route::get('/estadosr', 'EstadosRfidController@index');



    }
);

Route::any('{all}', function () {
    return view('welcome');
})->where(['all' => '.*']);
