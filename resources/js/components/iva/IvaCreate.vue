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
                        <v-flex sm4>
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
                            <div class="text-xs-center">
                                        <v-btn @click="submit" round  :loading="loading" block  color="primary">
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
                titulo:"IVA",
                iva: {
                    id:       0,
                    nombre:  "",
                },
        		status: false,
                loading: false,

                show: false,
                show_loading: true
      		}
        },
        mounted(){
            this.show_loading = false;
        },
    	methods:{
            submit() {
                if (this.loading === false){
                    this.loading = true;

                    var url = "/mto/ivas";

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(url,this.iva)
                                .then(response => {

                                    this.$toast.success(response.data.message);

                                    this.loading = false;
                                    this.$router.push({ name: 'iva.edit', params: { id: response.data.iva.id } })
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
