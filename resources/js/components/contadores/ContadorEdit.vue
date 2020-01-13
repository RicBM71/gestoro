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
                        <v-flex sm2></v-flex>
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
                            <v-switch
                                label="Cerrado"
                                v-model="contador.cerrado"
                                color="primary">
                            ></v-switch>
                        </v-flex>
                     </v-layout>
                    <v-layout row wrap>
                        <v-flex sm2></v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="contador.serie_albaran"
                                v-validate="'required|max:3'"
                                :error-messages="errors.collect('serie_albaran')"
                                label="Serie Albarán"
                                data-vv-name="serie_albaran"
                                data-vv-as="serie"
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
                                label="Ult. Albarán"
                                data-vv-name="ult_albaran"
                                data-vv-as="albarán"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="contador.serie_factura"
                                v-validate="'required|max:3'"
                                :error-messages="errors.collect('serie_factura')"
                                label="Serie Factura"
                                data-vv-name="serie_factura"
                                data-vv-as="serie"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="contador.ult_factura"
                                v-validate="'required|numeric'"
                                :error-messages="errors.collect('ult_factura')"
                                label="Ult. Factura"
                                data-vv-name="ult_factura"
                                data-vv-as="factura"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                     </v-layout>
                     <v-layout row wrap>
                        <v-flex sm2></v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="contador.serie_factura_auto"
                                v-validate="'required|max:3'"
                                :error-messages="errors.collect('serie_factura_auto')"
                                label="Serie Auto"
                                data-vv-name="serie_factura_auto"
                                data-vv-as="serie"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="contador.ult_factura_auto"
                                v-validate="'required|numeric'"
                                :error-messages="errors.collect('ult_factura_auto')"
                                label="Ult. Factura Auto"
                                data-vv-name="ult_factura_auto"
                                data-vv-as="factura"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
                            <v-text-field
                                v-model="contador.serie_factura_abono"
                                v-validate="'required|max:3'"
                                :error-messages="errors.collect('serie_factura_abono')"
                                label="Serie Abono"
                                data-vv-name="serie_factura_abono"
                                data-vv-as="serie"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="contador.ult_factura_abono"
                                v-validate="'required|numeric'"
                                :error-messages="errors.collect('ult_factura_abono')"
                                label="Ult. Abono"
                                data-vv-name="ult_factura_abono"
                                data-vv-as="factura"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm2></v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="contador.username"
                                :error-messages="errors.collect('username')"
                                label="Usuario"
                                data-vv-name="username"
                                readonly
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
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
                            <div class="text-xs-center">
                                        <v-btn @click="submit"  round  :loading="loading" block  color="primary">
                                Guardar
                                </v-btn>
                            </div>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>        </v-card>
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
                titulo:"Contadores",
                contador: {},
                url: "/mto/contadores",
                ruta: "contador",
                tipos: [],

        		status: false,
                loading: false,

                show: false,
                show_loading: true,
      		}
        },
        beforeMount(){
            var id = this.$route.params.id;

            if (id > 0)
                axios.get(this.url+'/'+id+'/edit')
                    .then(res => {
                        this.tipos = res.data.tipos;
                        this.contador = res.data.contador;
                        this.show = true;
                        this.show_loading = false;
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name: this.ruta+'.index'})
                    })
        },
        computed: {
            computedFModFormat() {
                moment.locale('es');
                return this.contador.updated_at ? moment(this.contador.updated_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.contador.created_at ? moment(this.contador.created_at).format('D/MM/YYYY H:mm') : '';
            }

        },
    	methods:{
            submit() {

                if (this.loading === false){
                    this.loading = true;

                    this.$validator.validateAll().then((result) => {
                        if (result){
                             axios.put(this.url+"/"+this.contador.id, this.contador)
                                .then(res => {

                                    this.$toast.success(res.data.message);
                                    this.contador = res.data.contador;
                                    this.loading = false;
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
