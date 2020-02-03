<template>
	<div>
        <v-container grid-list-md text-xs-center>
            <v-layout row wrap>
                 <v-flex sm2></v-flex>
                  <v-flex sm8>
                      <v-card>
                        <v-card-title color="indigo">
                            <h2 color="indigo">{{titulo}}</h2>
                            <v-spacer></v-spacer>
                        </v-card-title>
                    </v-card>
                    <v-card>
                        <v-form>
                            <v-container grid-list-md text-xs-center>
                                <v-layout row wrap>
                                    <v-flex sm1></v-flex>
                                    <v-flex sm2>
                                        <v-text-field
                                            v-model="ejercicio"
                                            v-validate="'required|numeric'"
                                            :error-messages="errors.collect('ejercicio')"
                                            label="Ejercicio"
                                            data-vv-name="ejercicio"
                                            data-vv-as="Ejercicio"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex sm2>
                                        <v-text-field
                                            v-model="serie"
                                            v-validate="'required|max:3'"
                                            :error-messages="errors.collect('serie')"
                                            label="Serie Origen"
                                            data-vv-name="serie"
                                            data-vv-as="Serie"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex sm4>
                                        <v-text-field
                                            v-model="albaran"
                                            v-validate="'required|numeric'"
                                            :error-messages="errors.collect('albaran')"
                                            label="Nº de Compra Origen"
                                            data-vv-name="albaran"
                                            data-vv-as="Nº de compra"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                </v-layout>
                                <v-layout row wrap>
                                    <v-flex sm5></v-flex>
                                    <v-flex sm4>
                                        <v-text-field
                                            v-model="albaran_d"
                                            v-validate="'required|numeric'"
                                            :error-messages="errors.collect('albaran_d')"
                                            label="Nº de Compra Destino"
                                            data-vv-name="albaran_d"
                                            data-vv-as="Nº de compra"
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

import {mapGetters} from 'vuex';

	export default {
		$_veeValidate: {
      		validator: 'new'
        },
    	data () {
      		return {
                titulo:"Intercambio Operaciones",
                ejercicio:new Date().toISOString().substr(0, 4),
                albaran:"",
                serie: "M",
                albaran_d:"",
                serie_d: "M",
                status: false,
                loading: false,
                show_loading: true,
      		}
        },
        mounted(){
            if (!this.isAdmin){
                this.$toast.error('Permiso Administrador requerido');
                this.$router.push({ name: 'dash'})
            }
        },
        computed: {
            ...mapGetters([
                'isAdmin',
            ]),
        },
    	methods:{

            submit() {
                if (this.loading === false){
                    this.loading = true;
                    this.$validator.validateAll().then((result) => {
                        if (result){

                            axios.post("/utilidades/intercambio",
                                    {   albaran: this.albaran,
                                        serie: this.serie,
                                        albaran_des: this.albaran_d,
                                        serie_des: this.serie_d,
                                        ejercicio: this.ejercicio
                                    }
                                )
                                .then(res => {
                                    this.$toast.success('Intercambio Ok!');
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
