<template>
	<div v-show="show">
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="whatsapp.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                <v-container>
                    <v-layout row wrap>
                        <v-flex sm1></v-flex>
                        <v-flex sm8>
                            <v-textarea
                                v-model="whatsapp.texto"
                                v-validate="'required'"
                                :error-messages="errors.collect('texto')"
                                label="Texto"
                                data-vv-name="texto"
                                data-vv-as="texto"
                                required
                                v-on:keyup.enter="submit"
                            ></v-textarea>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm1></v-flex>
                        <v-flex sm8>
                            <p><span class="font-weight-bold">Campos personalizables:</span>
                            %n = Nombre cliente - %f = Fecha Renovación - %l = Número Lote - %t = Teléfonos empresa</p>
                            <p><span class="font-weight-bold">Tipografía:</span>
                            * = Negrita - _: Cursiva</p>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex sm1></v-flex>
                         <v-flex sm2>
                            <v-text-field
                                v-model="whatsapp.username"
                                label="Usuario"
                                readonly
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="computedFModFormat"
                                label="Modificado"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="computedFCreFormat"
                                label="Creado"
                                readonly
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1>
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

	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe,
		},
    	data () {
      		return {
                titulo:"Plantillas Whatsapps",
                whatsapp: {
                    id:       0,
                    texto:  "",
                    updated_at:"",
                    created_at:"",
                },

        		status: false,
                loading: false,

                show: false,

      		}
        },
        mounted(){
            var id = this.$route.params.id;

            if (id > 0)
                axios.get('/mto/whatsapps/'+id+'/edit')
                    .then(res => {

                        this.whatsapp = res.data.whatsapp;
                        this.show = true;
                    })
                    .catch(err => {
                        this.$toast.error(err.response.data.message);
                        this.$router.push({ name: 'whatsapp.index'})
                    })
        },
        computed: {
            computedFModFormat() {
                moment.locale('es');
                return this.whatsapp.updated_at ? moment(this.whatsapp.updated_at).format('D/MM/YYYY H:mm:ss') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.whatsapp.created_at ? moment(this.whatsapp.created_at).format('D/MM/YYYY H:mm:ss') : '';
            }

        },
    	methods:{
            submit() {

                if (this.loading === false){
                    this.loading = true;

                    var url = "/mto/whatsapps/"+this.whatsapp.id;
                    var metodo = "put";
                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios({
                                method: metodo,
                                url: url,
                                data:
                                    {
                                        texto: this.whatsapp.texto,

                                    }
                                })
                                .then(response => {

                                    this.$toast.success(response.data.message);
                                    this.whatsapp = response.data.whatsapp;
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
