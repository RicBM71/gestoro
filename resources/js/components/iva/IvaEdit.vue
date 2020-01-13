<template>
	<div v-show="show">
        <loading :show_loading="show_loading"></loading>

        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="iva.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                <v-container>
                    <v-layout row wrap>
                        <v-flex sm2></v-flex>
                        <v-flex sm3>
                            <v-text-field
                                v-model="iva.nombre"
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
                                v-model="iva.rebu"
                                color="primary"
                                label="rebu"
                            ></v-switch>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="iva.importe"
                                v-validate="'required|decimal:2|min:1'"
                                :error-messages="errors.collect('importe')"
                                label="Importe"
                                data-vv-name="importe"
                                data-vv-as="importe"
                                class="inputPrice"
                                type="number"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm2></v-flex>
                        <v-flex sm10>
                            <v-text-field
                                v-model="iva.leyenda"
                                v-validate="'max:150'"
                                :error-messages="errors.collect('leyenda')"
                                label="Leyenda"
                                data-vv-name="leyenda"
                                data-vv-as="leyenda"
                                required
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
                                v-model="iva.username"
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
                titulo:"Tipo IVA",
                iva: {
                    id:       0,
                    nombre:  "",
                    leyenda: "",
                    rebu: false,
                    username:"",
                    updated_at:"",
                    created_at:"",
                },

        		status: false,
                loading: false,

                show: false,
                show_loading: true,
      		}
        },
        beforeMount(){
            var id = this.$route.params.id;

            if (id > 0)
                axios.get('/mto/ivas/'+id+'/edit')
                    .then(res => {

                        this.iva = res.data.iva;
                        this.show = true;
                        this.show_loading = false;
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name: 'iva.index'})
                    })
        },
        computed: {
            computedFModFormat() {
                moment.locale('es');
                return this.iva.updated_at ? moment(this.iva.updated_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.iva.created_at ? moment(this.iva.created_at).format('D/MM/YYYY H:mm') : '';
            }

        },
    	methods:{
            submit() {

                if (this.loading === false){
                    this.loading = true;
                    var url = "/mto/ivas/"+this.iva.id;
                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.put(url,this.iva)
                                .then(response => {
                                    this.$toast.success(response.data.message);
                                    this.iva = response.data.iva;
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
