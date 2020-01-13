<template>
	<div v-show="show">
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="libro.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                 <v-container>
                     <v-layout row wrap>
                        <v-flex sm3 d-flex>
                            <v-select
                            v-model="libro.grupo_id"
                            v-validate="'required'"
                            data-vv-name="grupo_id"
                            data-vv-as="grupo"
                            :error-messages="errors.collect('grupo_id')"
                            :items="grupos"
                            label="Grupo"
                            required
                            ></v-select>
                        </v-flex>

                        <v-flex sm3>
                            <v-text-field
                                v-model="libro.nombre"
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
                         <v-flex sm2>
                            <v-text-field
                                v-model="libro.ejercicio"
                                v-validate="'required|integer'"
                                :error-messages="errors.collect('ejercicio')"
                                label="Ejercicio"
                                data-vv-name="ejercicio"
                                data-vv-as="ejercicio"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm9></v-flex>
                        <v-flex sm2>
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
    	data () {
      		return {
                titulo:"Libro",
                libro: {
                    id:       0,
                    grupo_id: "",
                    nombre:  "",
                    ejercicio:"",
                    ult_compra:1,
                    ult_factura:1,
                    serie_fac:"",
                    serie_com:"",
                    semdia_bloqueo:"",
                    interes:"10",
                    codigo_pol:"",
                    cerrado: false,
                    username:""
                },
                url: "/mto/libros",
                ruta: "libro",
                grupos: [],

        		status: false,
                loading: false,


                show: false,
                show_loading: true
      		}
        },
        mounted(){
            axios.get(this.url+'/create')
                .then(res => {
                    this.show = true;
                    this.grupos = res.data.grupos;
                    this.show_loading = false;
                })
                .catch(err => {
                    this.$toast.error(err.response.data.message);
                    this.$router.push({ name: this.ruta+'.index'})
                })
        },
    	methods:{
            submit() {
                if (this.loading === false){
                    this.loading = true;

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(this.url, this.libro)
                                .then(response => {

                                    this.$toast.success(response.data.message);

                                    this.loading = false;
                                    this.$router.push({ name: this.ruta+'.edit', params: { id: response.data.libro.id } })
                                })
                                .catch(err => {

                                    if (err.request.status == 422){ // fallo de validated.
                                        const msg_valid = err.response.data.errors;
                                        for (const prop in msg_valid) {
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
