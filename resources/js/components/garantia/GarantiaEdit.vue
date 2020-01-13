<template>
	<div v-show="show">
        <loading :show_loading="show_loading"></loading>

        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="garantia.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                <v-container>
                    <v-layout row wrap>
                        <v-flex sm1></v-flex>
                        <v-flex sm3>
                            <v-text-field
                                v-model="garantia.nombre"
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
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm1></v-flex>
                        <v-flex sm10>

                            <v-textarea
                                v-model="garantia.leyenda"
                                v-validate="'required'"
                                :error-messages="errors.collect('leyenda')"
                                label="Leyenda"
                                data-vv-name="leyenda"
                                data-vv-as="leyenda"
                            ></v-textarea>

                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm1></v-flex>
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
                                v-model="garantia.username"
                                :error-messages="errors.collect('username')"
                                label="Usuario"
                                data-vv-name="username"
                                readonly
                                v-on:keyup.enter="submit"
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
                titulo:"Garantía",
                garantia: {},

        		status: false,
                loading: false,

                show: false,
                show_loading: true,
      		}
        },
        beforeMount(){
            var id = this.$route.params.id;

            if (id > 0)
                axios.get('/mto/garantias/'+id+'/edit')
                    .then(res => {

                        this.garantia = res.data.garantia;
                        this.show = true;
                        this.show_loading = false;
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name: 'garantia.index'})
                    })
        },
        computed: {
            computedFModFormat() {
                moment.locale('es');
                return this.garantia.updated_at ? moment(this.garantia.updated_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.garantia.created_at ? moment(this.garantia.created_at).format('D/MM/YYYY H:mm') : '';
            }

        },
    	methods:{
            submit() {

                if (this.loading === false){
                    this.loading = true;
                    var url = "/mto/garantias/"+this.garantia.id;
                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.put(url,this.garantia)
                                .then(response => {
                                    this.$toast.success(response.data.message);
                                    this.garantia = response.data.garantia;
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
