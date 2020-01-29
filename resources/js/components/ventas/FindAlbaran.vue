<template>
	<div>

        <v-container grid-list-md text-xs-center>
            <v-layout row wrap>
                 <v-flex sm2></v-flex>
                  <v-flex sm8>
                      <v-card>
                        <v-card-title color="indigo">
                            <h2 color="indigo">{{computedTitulo}}</h2>
                            <v-spacer></v-spacer>
                            <!-- <menu-ope></menu-ope> -->
                        </v-card-title>
                    </v-card>
                    <v-card>
                        <v-form>
                            <v-container grid-list-md text-xs-center>
                                <v-layout row wrap>
                                    <v-flex sm3></v-flex>
                                    <v-flex sm2>
                                        <v-text-field
                                            v-model="serie"
                                            v-validate="'required|max:3'"
                                            :error-messages="errors.collect('serie')"
                                            label="Serie"
                                            data-vv-name="serie"
                                            data-vv-as="Serie"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex sm4>
                                        <v-text-field
                                            v-model="albaran"
                                            v-validate="{ required: true, regex:/^[0-9]*[-.]*[0-9]*$/}"
                                            :error-messages="errors.collect('albaran')"
                                            label="Número"
                                            data-vv-name="albaran"
                                            data-vv-as="Número"
                                            hint="NNNNN-AA"
                                            :persistent-hint=true
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                </v-layout>
                                <v-layout row wrap>
                                    <v-flex sm3></v-flex>
                                    <v-flex sm2>
                                        <v-switch
                                            label="Factura"
                                            v-model="factura"
                                            @change="changeSw()"
                                            color="primary">
                                        ></v-switch>
                                    </v-flex>
                                </v-layout>
                                <v-layout row wrap>
                                    <v-flex sm9></v-flex>
                                    <v-flex sm2>
                                        <div class="text-xs-center">
                                            <v-btn @click="submit"  round  :loading="loading" block  color="primary">
                                                Enviar
                                            </v-btn>
                                        </div>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-form>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-container>
	</div>
</template>
<script>

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
                titulo:"Buscar albarán",
                albaran:"",
                serie: "R",
                status: false,
                loading: false,
                factura: false,

                show_loading: true,
                contador: {},
      		}
        },
        mounted(){
            axios.get('/ventas/find')
                .then(res => {
                    this.contador = res.data.contador_def;
                    this.serie = this.contador.serie_albaran;
                })
                .catch(err => {
                    this.$toast.error(err.response.data.message);
                    this.$router.push({ name: 'dash'})
                })

            //this.$nextTick(() => this.$refs.dni.focus())
        },
        computed:{
            computedTitulo(){
                return this.factura ? 'Buscar Factura' : 'Buscar Albarán';
            }
         },
    	methods:{
            changeSw(){
                this.serie = this.factura ? this.contador.serie_factura : this.contador.serie_albaran;
            },
            submit() {
                if (this.loading === false){
                    this.loading = true;
                    this.$validator.validateAll().then((result) => {
                        if (result){

                            axios.post("/ventas/find/albaranes",
                                    {   albaran: this.albaran,
                                        serie: this.serie,
                                        esfactura: this.factura
                                    }
                                )
                                .then(res => {

                                    if(res.data.albaran ==null)
                                        this.$toast.warning("No se ha encontrado el albarán/factura");
                                    else{

                                        this.albaran = res.data.albaran;
                                        this.$router.push({ name: 'albaran.edit', params: { id: res.data.albaran.id } })
                                    }

                                    this.loading = false;
                                })
                                .catch(err => {

                                    this.loading = false;

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

                                });
                            }
                        else{
                            this.loading = false;
                        }
                    });
                }




            }
        }
  }
</script>
