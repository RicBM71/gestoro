<template>
    <div>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">Generar Modelo 347</h2>
                <v-spacer></v-spacer>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                <v-container>
                    <v-layout row wrap>
                        <v-flex sm3></v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="ejercicio"
                                v-validate="'required|numeric|min_value:2000'"
                                :error-messages="errors.collect('ejercicio')"
                                label="Ejercicio"
                                data-vv-name="ejercicio"
                                data-vv-as="Ejercicio"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-select
                                v-model="modelo_id"
                                v-validate="'required'"
                                data-vv-name="modelo_id"
                                data-vv-as="modelo"
                                :error-messages="errors.collect('modelo_id')"
                                :items="modelos"
                                label="Modelo de Operación"
                                required
                                ></v-select>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="imp_corte"
                                v-validate="'required|decimal:2'"
                                :error-messages="errors.collect('imp_corte')"
                                label="Importe Corte"
                                data-vv-name="imp_corte"
                                data-vv-as="importe"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-btn @click="submit" :loading="loading" flat round small block  color="info">
                                Enviar
                            </v-btn>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm1></v-flex></v-flex>
                        <v-flex sm10>
                            <p class="caption">La declaración del modelo 347 se genera en base a Adquisiciones de bienes y servicios (COMPRAS) y Entrega de bienes y prestaciones de servicios (VENTAS, VENTAS R.E.B.U Y RECUPERACIONES). Los valores mostrados son con el IVA incluido. Los importes están agrupados por DNI/CIF/NIE. En el caso de ventas, la fecha de referencia y por tanto del trimestre es la fecha de factura de venta y para compras la fecha de recuperación.</p>
                            <p class="caption red--text darken-4">Se mostrarán sólo los clientes que tengan marcada la opción SI en Listar 347 en clientes.</p>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>

                    </v-layout>
                </v-container>
            </v-form>
        </v-card>
    </div>
</template>
<script>
import moment from 'moment'
export default {
    $_veeValidate: {
        validator: 'new'
    },
    data () {
      return {
            modelos:[
                    {value: 'C', text:"Compras"},
                    {value: 'V', text:"Ventas"},
                ],
            modelo_id: 'V',
            loading: false,
            ejercicio:new Date().toISOString().substr(0, 4),
            imp_corte: 3005.06
      }
    },
    methods:{
        submit(){

            if (this.loading === false){
                this.loading = true;
                this.$validator.validateAll().then((result) => {
                    if (result){

                        axios({
                            url: '/exportar/mod347/excel',
                            method: 'POST',
                            responseType: 'blob', // important
                            data:{
                                ejercicio: this.ejercicio,
                                modelo: this.modelo_id,
                                imp_corte: this.imp_corte
                            }
                            })
                        .then(res => {

                            let blob = new Blob([res.data], { type: 'data:"application/xlsx'})
                            let link = document.createElement('a')
                            link.href = window.URL.createObjectURL(blob)

                            link.download = "Mod347.xlsx";

                            document.body.appendChild(link);
                            link.click()
                            document.body.removeChild(link);

                            this.$toast.success('Se ha generado un fichero de Excel');

                            this.loading = false;

                        })
                        .catch(err => {
                            if (err.request.status == 422){ // fallo de validated.
                                const msg_valid = err.response.data.errors;
                                for (const prop in msg_valid) {
                                    // this.$toast.error(`${msg_valid[prop]}`);

                                    this.errors.add({
                                        field: prop,
                                        msg: `${msg_valid[prop]}`
                                    })
                                }
                            }else{
                                if (err.response.status == 403)
                                    this.$toast.error('403 - Acceso denegado!');
                                else
                                    this.$toast.error(err.response.data.message);
                            }
                            this.loading = false;
                        });
                    }


                });

            }
        },


    }
}
</script>
