<template>
	<div v-show="show">
        <loading :show_loading="show_loading"></loading>

        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="cuenta.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                <v-container>
                    <v-layout row wrap>
                        <v-flex sm2></v-flex>
                        <v-flex sm3>
                            <v-text-field
                                v-model="cuenta.nombre"
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
                            <v-switch
                                v-model="cuenta.defecto"
                                color="primary"
                                label="Defecto"
                            ></v-switch>
                        </v-flex>
                        <v-flex sm2>
                            <v-switch
                                v-model="cuenta.activa"
                                color="primary"
                                label="Activa"
                            ></v-switch>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm2></v-flex>
                        <v-flex sm4>
                            <v-text-field
                                v-model="cuenta.iban"
                                v-validate="'max:24'"
                                mask="AA## #### #### #### #### ####"
                                counter=24
                                :error-messages="errors.collect('iban')"
                                label="IBAN"
                                data-vv-name="iban"
                                data-vv-as="iban"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="cuenta.bic"
                                v-validate="'min:11'"
                                counter=11
                                :error-messages="errors.collect('bic')"
                                label="BIC "
                                data-vv-name="bic"
                                data-vv-as="bic"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm2></v-flex>
                        <v-flex sm3>
                            <v-text-field
                                v-model="cuenta.sufijo_adeudo"
                                :error-messages="errors.collect('sufijo_adeudo')"
                                label="Sufijo adeudos SEPA"
                                data-vv-name="sufijo_adeudo"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm3>
                            <v-text-field
                                v-model="cuenta.sufijo_transf"
                                :error-messages="errors.collect('sufijo_transf')"
                                label="Sufijo transferencias SEPA"
                                data-vv-name="sufijo_transf"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm2></v-flex>
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
                                v-model="cuenta.username"
                                :error-messages="errors.collect('username')"
                                label="Usuario"
                                data-vv-name="username"
                                readonly
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm7></v-flex>
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
                titulo:"Cuentas",
                cuenta:{},
        		status: false,
                loading: false,

                show: false,
                show_loading: true,
      		}
        },
        beforeMount(){
            var id = this.$route.params.id;

            if (id > 0)
                axios.get('/mto/cuentas/'+id+'/edit')
                    .then(res => {

                        this.cuenta = res.data.cuenta;
                        this.show = true;
                        this.show_loading = false;
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name: 'cuenta.index'})
                    })
        },
        computed: {
            computedFModFormat() {
                moment.locale('es');
                return this.cuenta.updated_at ? moment(this.cuenta.updated_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.cuenta.created_at ? moment(this.cuenta.created_at).format('D/MM/YYYY H:mm') : '';
            }

        },
    	methods:{
            submit() {

                if (this.loading === false){
                    this.loading = true;
                    var url = "/mto/cuentas/"+this.cuenta.id;
                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.put(url,this.cuenta)
                                .then(response => {
                                    this.$toast.success(response.data.message);
                                    this.cuenta = response.data.cuenta;
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
