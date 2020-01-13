<template>
	<div>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="cliente.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                <v-container>
                    <v-layout row wrap>
                        <v-flex sm2 d-flex>
                            <v-select
                            v-model="cliente.tipodoc"
                            :items="tiposdoc"
                            label="Documento"
                            ></v-select>
                        </v-flex>
                        <v-flex sm3>
                            <v-text-field
                                v-model="cliente.dni"
                                v-validate="'required|alpha_num|min:4'"
                                :error-messages="errors.collect('dni')"
                                label="Nº de documento"
                                data-vv-name="dni"
                                data-vv-as="dni"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm3>
                            <v-text-field
                                v-model="cliente.nombre"
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
                        <v-flex sm4>
                            <v-text-field
                                v-model="cliente.apellidos"
                                v-validate="'required'"
                                :error-messages="errors.collect('apellidos')"
                                label="Apellidos"
                                data-vv-name="apellidos"
                                data-vv-as="apellidos"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1></v-flex>
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

	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe,
		},
    	data () {
      		return {
                titulo:"Nuevo Cliente",
                cliente: {
                    id:0,
                    nombre:"",
                    apellidos:"",
                    dni:"",
                    tipodoc:"N"
                },

                cliente_id: "",

                tiposdoc:[
                    {value: 'N', text:"DNI/NIE"},
                    {value: 'C', text:"CIF"},
                    {value: 'O', text:"Otros"},
                ],


        		status: false,
                loading: false,

                calfbaja:false,
                show: false,

      		}
        },
        mounted(){
            // axios.get('/mto/clientes/create')
            //     .then(res => {
            //     })
            //     .catch(err => {
            //         this.$toast.error(err.response.data.message);
            //         this.$router.push({ name: 'cliente.index'})
            //     })
        },
        computed: {
            computedFModFormat() {
                moment.locale('es');
                return this.cliente.updated_at ? moment(this.cliente.updated_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.cliente.created_at ? moment(this.cliente.created_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFechaBaja() {
                moment.locale('es');
                return this.cliente.fechabaja ? moment(this.cliente.fechabaja).format('D/MM/YYYY') : '';
            }

        },
    	methods:{
            submit() {
                if (this.loading === false){
                    this.loading = true;
                    this.cliente.razon = this.cliente.nombre + " " + this.cliente.apellidos;
                    ///this.cliente.empresa_id = 1;

                    var url = "/mto/clientes";

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(url, this.cliente)
                                .then(response => {
                                    this.$router.push({ name: 'cliente.edit', params: { id: response.data.cliente.id } })
                                    this.loading = false;

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
