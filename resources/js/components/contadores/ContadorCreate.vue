<template>
	<div v-show="show">
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="contador.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                 <v-container>
                     <v-layout row wrap>
                         <v-flex sm3 d-flex>
                            <v-select
                                v-model="contador.tipo_id"
                                v-validate="'required'"
                                data-vv-name="tipo_id"
                                data-vv-as="tipo"
                                :error-messages="errors.collect('tipo_id')"
                                :items="tipos"
                                label="Tipo"
                                required
                            ></v-select>
                        </v-flex>
                         <v-flex sm2>
                            <v-text-field
                                v-model="contador.ejercicio"
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
                        <v-flex sm2>
                            <v-text-field
                                v-model="contador.ult_albaran"
                                v-validate="'required|numeric'"
                                :error-messages="errors.collect('ult_albaran')"
                                label="Albarán"
                                data-vv-name="ult_albaran"
                                data-vv-as="albarán"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="contador.serie_albaran"
                                v-validate="'required|max:1'"
                                :error-messages="errors.collect('serie_albaran')"
                                label="Serie Albarán"
                                data-vv-name="serie_albaran"
                                data-vv-as="serie"
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
                titulo:"Contadores",
                contador: {
                    id:       0,
                    tipo_id: "",
                    serie_albaran: "",
                    ult_albaran: 1000,
                    ult_factura: 0,
                    ult_factura_auto: 0,
                    ult_factura_abono: 0,
                    serie_factura:"F",
                    serie_factura_auto:"FA",
                    serie_factura_abono: "FR",
                    ejercicio: new Date().toISOString().substr(0, 4),
                },
                url: "/mto/contadores",
                ruta: "contador",
                tipos: [],

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
                    this.tipos = res.data.tipos;
                    this.contador.tipo_id=this.tipos[0].value;
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
                            axios.post(this.url, this.contador)
                                .then(response => {

                                    this.$toast.success(response.data.message);

                                    this.loading = false;
                                    this.$router.push({ name: this.ruta+'.edit', params: { id: response.data.contador.id } })
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
