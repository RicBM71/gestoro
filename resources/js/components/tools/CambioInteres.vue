<template>
	<div>
        <loading :show_loading="show_loading"></loading>
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                 <v-container>
                     <v-layout row wrap>
                        <v-flex xs2>
                            <v-text-field
                                v-model="interes"
                                label="Nuevo Interes"
                                single-line
                            ></v-text-field>
                        </v-flex>
                        <v-flex sm1></v-flex>
                        <v-flex sm1>
                            <div class="text-xs-center">
                                <v-btn @click="submit" round small flat :loading="loading"  block  color="primary">
                                    Enviar
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
import {mapGetters} from 'vuex';
import moment from 'moment'
import Loading from '@/components/shared/Loading'

	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'loading': Loading
		},
    	data () {
      		return {

                url: "/utilidades/cambiointeres",

                interes: 5,

        		status: false,
                loading: false,

                show_loading: true,
                titulo:'Cambio Masivo interés Recompras'
      		}
        },
        mounted(){
            if (!this.isRoot){
                this.$toast.error('No dispone de los permisos necesarios');
                this.$router.push({ name:'dash'})
            }
            this.show_loading = false;
        },
        computed: {
        ...mapGetters([
               'isRoot'
           ]),
        },
    	methods:{
            submit() {
                if (this.loading === false){

                    this.loading = true;
                    this.show_loading = true;

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(this.url, {
                                interes: this.interes,
                            })
                                .then(res => {

                                    this.$toast.success('Se han recalculado COMPRAS: '+res.data.registros);

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
                                    this.loading = this.show_loading = false;
                                });
                            }
                        else{
                            this.loading = this.show_loading = false;
                        }
                    });
                }

            },

        }
  }
</script>
