<template>
	<div v-show="!show_loading">
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                 <v-container>
                     <v-layout row wrap>
                         <v-flex sm1></v-flex>
                        <v-flex sm5>
                            <v-text-field
                                v-model="producto.nombre"
                                v-validate="'required'"
                                :error-messages="errors.collect('nombre')"
                                label="Nombre"
                                data-vv-name="nombre"
                                data-vv-as="nombre"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1 v-show="false">
                            <v-text-field>
                            </v-text-field>
                        </v-flex>
                        <!-- <v-flex sm3 d-flex>
                            <v-select
                            v-model="producto.clase_id"
                            v-validate="'required'"
                            data-vv-name="clase_id"
                            data-vv-as="clase_id"
                            :error-messages="errors.collect('clase_id')"
                            :items="clases"
                            label="Clase"
                            ></v-select>
                        </v-flex>
                        <v-flex sm1>
                            <v-select
                                v-model="producto.quilates"
                                :items="quilates"
                                label="Quilates"
                                v-validate="'numeric'"
                                :error-messages="errors.collect('quilates')"
                                data-vv-name="quilates"
                                data-vv-as="quilates"
                            ></v-select>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="producto.peso_gr"
                                v-validate="'required|decimal:2|min:1'"
                                :error-messages="errors.collect('peso_gr')"
                                label="Peso gr."
                                data-vv-name="peso_gr"
                                data-vv-as="peso_gr"
                                class="inputPrice"
                                type="number"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex> -->
                        <v-flex sm1></v-flex>
                        <v-flex sm1>
                            <div class="text-xs-center">
                                        <v-btn @click="submit"  round  :loading="loading" block  color="primary">
                                Guardar
                                </v-btn>
                            </div>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
        </v-card>
	</div>
</template>
<script>
import moment from 'moment'
import MenuOpe from './MenuOpe'
import Loading from '@/components/shared/Loading'
  export default {
    $_veeValidate: {
        validator: 'new'
    },
    components: {
        'menu-ope': MenuOpe,
        'loading': Loading
    },
    data: () => ({
        titulo:"Productos",
        loading: false,
        show: false,
        show_loading: true,

        show_quil: true,
        show_peso: true,

        url: "/mto/productos",
        ruta: "producto",

        clases:[],
        quilates:[],
        producto: {
            id:0,
            peso:"",
            precio_venta:0,
            precio_coste:0,
            compra_id: null,
            nombre:"",
            almacen_id:"",
            clase_id: 5,
            estado_id: 5,
            quilates:"",
            peso_gr: 0,
            ref_pol:"",
            iva_id: 1,
            cliente_id: null,
            univen: 'U',
            destino_empresa_id:null,
        },
    }),
    mounted(){
        axios.get(this.url+'/create')
            .then(res => {
                // this.clases = res.data.clases;
                // this.producto.clase_id = this.clases[0].value;
                // this.quilates = res.data.quilates;
                // this.selClase(this.producto.clase_id);
                this.show_loading=false;
            })
            .catch(err => {
                this.$toast.error(err.response.data.message);
                this.$router.go(-1)
            })

    },
    methods:{
        selClase(id){

            let index = this.clases.findIndex((item) => item.value === id);

            this.show_peso = this.clases[index].peso;
            this.show_quil = this.clases[index].quilates;

            if (!this.show_peso)
                this.producto.peso = 0;

            if (!this.quil)
                this.producto.quilates = "";

        },
        submit() {
            if (this.loading === false){
                this.loading = true;
                this.$validator.validateAll().then((result) => {
                    if (result){

                        axios.post(this.url, this.producto)
                            .then(res => {

                                this.loading = false;
                                this.$router.push({ name: this.ruta+'.edit', params: { id: res.data.producto.id } })

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
                                    this.$toast.error(err.response.data.message);
                                }
                                this.loading = false;
                            });
                        }
                    else{
                        this.loading = false;
                    }
                });
            }
        },
      }
  }
</script>

<style scoped>


.inputPrice >>> input {
  text-align: center;
  -moz-appearance:textfield;
}

input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    margin: 0;
}
</style>
