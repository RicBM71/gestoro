<?php

use App\Clidoc;
use App\Cliente;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GdClientesSeeder extends Seeder
{
    protected $bbdd="yajap";
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            Cliente::truncate();

          //  $count = DB::connection($this->bbdd)->table('clientes')->count();


            $reg = DB::connection($this->bbdd)->select('select * from clientes where id in (select cliente from albaranes where empresa=2)');
            $this->insertaClientes($reg);

            return;

                // importar documentos
            Clidoc::truncate();

            $data=array();
            $reg = DB::connection($this->bbdd)->select('select * from clidoc where cola="A" and id > 1 order by id');
            $i=0;

            foreach ($reg as $row){
                $i++;
                $file1 = str_replace('storage/','',$row->file1);
                //$file1 = str_replace('.dat','.jpg',$file1);
                $file2 = str_replace('storage/','',$row->file2);
                //$file2 = str_replace('.dat','.jpg',$file2);

                //$this->encriptarALaravel($file1,$file2);

                $data[]=array(
                    'cliente_id' => $row->id,
                    'file1'=> $file1,
                    'file2'=> $file2,
                    'username' => $row->sysusr,
                    'created_at' => $row->sysfum.' 00:00:00',
                    'updated_at' => $row->sysfum.' '.$row->syshum,
                );

                if ($i % 2000 == 0){
                    DB::table('clidocs')->insert($data);
                    $data=array();
                }
            }

            DB::table('clidocs')->insert($data);

    }

    private function insertaClientes($reg){

        $i=0;
        $data=array();
        foreach ($reg as $row){
            $i++;

            if ($row->fechaalta == "0000-00-00" || is_null($row->fechaalta))
                $row->fechaalta = $row->sysfum." 00:00:00";
            else
                $row->fechaalta = $row->fechaalta." 00:00:00";

            if ($row->sysfum == "0000-00-00" || is_null($row->sysfum) || $row->sysfum==""){
                $row->sysfum = date('Y-m-d');
                $row->syshum = date('h:i:s');
            }

            if (strtotime($row->fecdni==false) || $row->fecdni=="0000-00-00" || $row->fecdni=="8211-00-02")
                $row->fecdni = null;

            try{
                Carbon::parse($row->fecdni);
              } catch(Exception $e){
                  $row->fecdni = null;
              };

            //$dni = str_replace("-","",$row->dni);
            $dni = $row->dni;

              $data[]=array(
                  'id' => $row->id,
                  'nombre' => $row->nombre,
                  'razon'=> $row->razon,
                  'apellidos'=> $row->apellidos,
                  'direccion'=> $row->direccion,
                  'cpostal'=> $row->cpostal,
                  'poblacion'=> $row->poblacion,
                  'provincia'=> $row->provincia,
                  'telefono1'=> $row->telefono1,
                  'telefono2'=> $row->telefono2,
                  'tfmovil'=> $row->tfmovil,
                  'email'=> $row->email,
                  'tipodoc'=> $row->tipodoc,
                  'dni'=> $dni,
                  'fecha_nacimiento'=> $row->fnacimiento=="0000-00-00" ? null : $row->fnacimiento,
                  'fecha_baja'=> $row->fechabaja=="0000-00-00" ? null : $row->fechabaja,
                  'nacpro'=> $row->nacpro,
                  'nacpais'=> $row->nacpob,
                  'fecha_dni'=> $row->fecdni,
                  'bloqueado'=> $row->bloqueado,
                  'iva_no_residente'=> $row->exentoiva=="S" ? true : false,
                  'facturar'=> $row->facturar=="S" ? true : false,
                  'vip'=> $row->vip=="S" ? true : false,
                  'listar_347'=> $row->imptcs=="S" ? true : false,
                  'asociado'=> $row->asociado=="S" ? true : false,
                  'fpago_id'=> 1,
                  'iban'=> null,
                  'bic'=> null,
                  'notas' => $row->notas1=='' ? null : $row->notas1,
                  'username' => $row->sysusr,
                  'created_at' => $row->fechaalta,
                  'updated_at' => $row->sysfum.' '.$row->syshum,
              );

              if ($i % 2000 == 0){
                  DB::table('clientes')->insert($data);
                  $data=array();
              }

          }

          if (count($data) > 0)
            DB::table('clientes')->insert($data);

    }

    private function encriptarALaravel($f1,$f2){

            if (Storage::disk('docs')->exists($f1)){
                $img_ci = Storage::disk('docs')->get($f1);
                $img_ci = str_replace('data:image/jpg;base64,','',$img_ci);

                //Storage::disk('docs')->put($f1, ($img_ci));
                Storage::disk('docs')->put($f1, Crypt::encryptString($img_ci));
            }

            if (Storage::disk('docs')->exists($f2)){
                $img_ci = Storage::disk('docs')->get($f2);
                $img_ci = str_replace('data:image/jpg;base64,','',$img_ci);

                //Storage::disk('docs')->put($f2, ($img_ci));
                Storage::disk('docs')->put($f2, Crypt::encryptString($img_ci));
            }


    }
}
