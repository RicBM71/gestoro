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
                                            v-model="producto_id"
                                            v-validate="'required|numeric'"
                                            :error-messages="errors.collect('producto_id')"
                                            label="Producto"
                                            data-vv-name="producto_id"
                                            data-vv-as="producto_id"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex sm2 v-if="producto.id > 0">
                                        <v-text-field
                                            v-model="empresa_id"
                                            v-validate="'required|numeric'"
                                            :error-messages="errors.collect('empresa_id')"
                                            label="Empresa"
                                            data-vv-name="empresa_id"
                                            data-vv-as="empresa_id"
                                            v-on:keyup.enter="submit"
                                        >
                                        </v-text-field>
                                    </v-flex>
                                </v-layout>

                                <v-layout row wrap>
                                    <v-flex sm2>Empresa {{producto.empresa_id}}</v-flex>
                                    <v-flex sm2>Empresa Destino {{producto.destino_empresa_id}}</v-flex>
                                </v-layout>
                                <v-layout row wrap>
                                    <v-flex sm2></v-flex>
                                    <v-flex sm2>
                                        <div class="text-xs-center">
                                            <v-btn @click="submit"  round  :loading="loading" block  color="primary">
                                                Enviar
                                            </v-btn>
                                        </div>
                                    </v-flex>
                                    <v-flex sm3 v-if="empresa_id > 0">
                                        <div class="text-xs-center">
                                            <v-btn @click="update"  round  :loading="loading" block  color="primary">
                                                Update empresa
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
                titulo:"Tools - find producto",
                ejercicio:new Date().toISOString().substr(0, 4),
                producto_id: "",
                empresa_id:"",
                status: false,
                loading: false,
                show_loading: true,
                producto:{
                    id:"",
                    empresa_id:"",
                    destino_empresa_id:"",
                    nombre:"",
                },
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

                            axios.post("/utilidades/helppro/find",
                                    {
                                        producto_id: this.producto_id
                                    }
                                )
                                .then(res => {
                                    this.producto = res.data.producto;
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
            },
            update(){
                axios.put("/utilidades/reasignar/empresa/producto/"+this.producto.id, {empresa_id: this.empresa_id})
                    .then(res => {
                        
                        this.$toast.success(res.data.message);
                        this.producto = res.data.producto;

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
        }
  }
</script>
