<template>
	<div v-show="show">
        <loading :show_loading="show_loading"></loading>

        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="cruce.id"></menu-ope>
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
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm1></v-flex>
                        <v-flex sm3>
                            <v-text-field
                                v-model="computedFModFormat"
                                label="Modificado"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm3>
                            <v-text-field
                                v-model="computedFCreFormat"
                                label="Creado"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                       <v-flex sm2>
                            <v-text-field
                                v-model="cruce.username"
                                :error-messages="errors.collect('username')"
                                label="Usuario"
                                data-vv-name="username"
                                readonly
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
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
import Loading from '@/components/shared/Loading'
import MenuOpe from './MenuOpe'

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
                cruce: {},

        		status: false,
                loading: false,

                show: false,
                show_loading: true,

                tipos:[
                    {value: 'C', text: 'Compras'},
                    {value: 'V', text: 'Ventas'},
                ],
      		}
        },
        beforeMount(){
            var id = this.$route.params.id;

            if (id > 0)
                axios.get('/mto/cruces/'+id+'/edit')
                    .then(res => {

                        this.cruce = res.data.cruce;
                        this.empresas = res.data.empresas;
                        this.show = true;
                        this.show_loading = false;
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name: 'cruce.index'})
                    })
        },
        computed: {
            computedFModFormat() {
                moment.locale('es');
                return this.cruce.updated_at ? moment(this.cruce.updated_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.cruce.created_at ? moment(this.cruce.created_at).format('D/MM/YYYY H:mm') : '';
            }

        },
    	methods:{
            submit() {

                if (this.loading === false){
                    this.loading = true;
                    var url = "/mto/cruces/"+this.cruce.id;
                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.put(url,this.cruce)
                                .then(response => {
                                    this.$toast.success(response.data.message);
                                    this.cruce = response.data.cruce;
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
