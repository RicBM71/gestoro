<template>
	<div>
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
                        <v-flex sm2 d-flex>
                            <v-select
                                v-model="cruce.comven"
                                v-validate="'required'"
                                data-vv-name="comven"
                                data-vv-as="tipo"
                                :error-messages="errors.collect('comven')"
                                :items="tipos"
                                label="Tipo"
                                ></v-select>
                        </v-flex>
                         <v-flex sm3 d-flex>
                            <v-select
                                v-model="cruce.destino_empresa_id"
                                v-validate="'required'"
                                data-vv-name="destino_empresa_id"
                                data-vv-as="empresa"
                                :error-messages="errors.collect('destino_empresa_id')"
                                :items="empresas"
                                label="Destino"
                                ></v-select>
                        </v-flex>

                        <v-flex sm1>
                            <div class="text-xs-center">
                                        <v-btn @click="submit" round  :loading="loading" block  color="primary">
                                Guardar
                                </v-btn>
                            </div>
                        </v-flex>
                         <v-flex sm1 v-show="show">
                            <v-text-field
                                value=""
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
        </v-card>
	</div>
</template>
<script>
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
                titulo:"Cruces de Caja",
                cruce: {
                    id:       0,
                    comven:  "C",
                    empresa_destino_id: "",
                },
                tipos:[
                    {value: 'C', text: 'Compras'},
                    {value: 'V', text: 'Ventas'},
                ],
        		status: false,
                loading: false,
                empresas: [],
                show: false,
                show_loading: true
      		}
        },
        mounted(){
            axios.get('/mto/cruces/create')
                .then(res => {
                    this.empresas = res.data.empresas;
                })
                .catch(err => {
                    this.$toast.error(err.response.data.message);
                    this.$router.push({ name: this.ruta+'.index'})
                })
                 .finally(()=> {
                    this.show_loading = false;
            });

        },
    	methods:{
            submit() {
                if (this.loading === false){
                    this.loading = true;

                    var url = "/mto/cruces";

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(url,this.cruce)
                                .then(response => {

                                    this.$toast.success(response.data.message);

                                    this.loading = false;
                                    this.$router.push({ name: 'cruce.edit', params: { id: response.data.cruce.id } })
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
