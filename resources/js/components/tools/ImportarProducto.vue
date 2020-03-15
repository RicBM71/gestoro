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
                                            v-model="prefijo"
                                            :error-messages="errors.collect('prefijo')"
                                            label="Prefijo"
                                            data-vv-name="prefijo"
                                            data-vv-as="prefijo"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex sm2>
                                        <v-text-field
                                            v-model="referencia"
                                            v-validate="'required'"
                                            :error-messages="errors.collect('referencia')"
                                            label="Referencia"
                                            data-vv-name="referencia"
                                            data-vv-as="referencia"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
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
                titulo:"Importar producto BBDD copia",
                referencia: "",
                prefijo:null,
                status: false,
                loading: false,
                show_loading: true,
      		}
        },
        mounted(){
            if (!this.isRoot){
                this.$toast.error('Permiso Root requerido');
                this.$router.push({ name: 'dash'})
            }
        },
        computed: {
            ...mapGetters([
                'isRoot',
            ]),
        },
    	methods:{

            submit() {
                if (this.loading === false){
                    this.loading = true;
                    this.$validator.validateAll().then((result) => {
                        if (result){

                            axios.post("/utilidades/importar/producto",
                                    {
                                        referencia: this.referencia,
                                        prefijo: this.prefijo
                                    }
                                )
                                .then(res => {
                                    this.$toast.success(res.data.message);
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

                                })
                                .finally(()=> {
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
