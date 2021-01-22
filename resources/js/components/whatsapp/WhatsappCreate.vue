<template>
	<div v-show="show">
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="whatsapp.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                <v-container>
                    <v-layout row wrap>
                        <v-flex sm1></v-flex>
                        <v-flex sm4>
                            <v-text-field
                                v-model="whatsapp.texto"
                                v-validate="'required'"
                                :error-messages="errors.collect('texto')"
                                label="Texto"
                                data-vv-name="texto"
                                data-vv-as="texto"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1 v-show="show">
                            <v-text-field
                                value=""
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm3>
                        </v-flex>
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
                titulo:"Plantillas WhatsApp",
                whatsapp: {
                    id:       0,
                    texto:  "",
                    updated_at:"",
                    created_at:"",
                },
                whatsapp_id: "",

        		status: false,
                loading: false,

                show: false,
      		}
        },
        mounted(){
            axios.get('/mto/whatsapps/create')
                .then(res => {
                    this.show = true;
                })
                .catch(err => {
                    this.$toast.error(err.response.data.message);
                    this.$router.push({ name: 'whatsapp.index'})
                })
        },

        computed: {
            computedFModFormat() {
                moment.locale('es');
                return this.whatsapp.updated_at ? moment(this.whatsapp.updated_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.whatsapp.created_at ? moment(this.whatsapp.created_at).format('D/MM/YYYY H:mm') : '';
            }

        },
    	methods:{
            submit() {

                if (this.loading === false){
                    this.loading = true;

                    var url = "/mto/whatsapps";
                    var metodo = "post";

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios({
                                method: metodo,
                                url: url,
                                data:
                                    {
                                        texto: this.whatsapp.texto,

                                    }
                                })
                                .then(response => {

                                    this.$toast.success(response.data.message);

                                    this.loading = false;
                                    this.$router.push({ name: 'whatsapp.edit', params: { id: response.data.whatsapp.id } })
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
<style>
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
