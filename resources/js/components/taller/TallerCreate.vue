<template>
	<div>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="taller.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                <v-container>
                    <v-layout row wrap>
                        <v-flex sm2 d-flex>
                            <v-select
                            v-model="taller.tipodoc"
                            :items="tiposdoc"
                            label="Documento"
                            ></v-select>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="taller.dni"
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
                        <v-flex sm4>
                            <v-text-field
                                v-model="taller.razon"
                                v-validate="'required'"
                                :error-messages="errors.collect('razon')"
                                label="Razón Social"
                                data-vv-name="razon"
                                data-vv-as="razon"
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
                titulo:"Nuevo Taller",
                taller: {
                    id:0,
                    razon:"",
                    dni:"",
                    tipodoc:"C"
                },

                taller_id: "",

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
        computed: {
            computedFModFormat() {
                moment.locale('es');
                return this.taller.updated_at ? moment(this.taller.updated_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.taller.created_at ? moment(this.taller.created_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFechaBaja() {
                moment.locale('es');
                return this.taller.fechabaja ? moment(this.taller.fechabaja).format('D/MM/YYYY') : '';
            }
        },
    	methods:{
            submit() {
                if (this.loading === false){
                    this.loading = true;

                    var url = "/mto/talleres";

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(url, this.taller)
                                .then(response => {
                                    this.$router.push({ name: 'taller.edit', params: { id: response.data.taller.id } })
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
