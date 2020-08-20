<template>
	<div v-show="!show_loading">
        <v-card>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope :id="fixing.id"></menu-ope>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                 <v-container>
                     <v-layout row wrap>
                        <v-flex sm4></v-flex>
                        <v-flex sm2>
                            <v-menu
                                v-model="menu1"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                            >
                                <v-text-field
                                    slot="activator"
                                    :value="computedFecha"
                                    label="Fecha"
                                    v-validate="'required'"
                                    data-vv-name="fecha"
                                    append-icon="event"
                                    readonly
                                    data-vv-as="Fecha"
                                    :error-messages="errors.collect('fecha')"
                                    ></v-text-field>
                                <v-date-picker
                                    v-model="fixing.fecha"
                                    no-title
                                    locale="es"
                                    first-day-of-week=1
                                    @input="menu1 = false"
                                ></v-date-picker>
                            </v-menu>
                        </v-flex>
                     </v-layout>
                    <v-layout row wrap>
                        <v-flex sm4></v-flex>
                        <v-flex sm2>
                            <v-text-field
                                v-model="fixing.importe"
                                v-validate="'required|decimal:2|min:1'"
                                :error-messages="errors.collect('importe')"
                                label="Importe"
                                data-vv-name="importe"
                                data-vv-as="importe"
                                class="inputPrice"
                                type="number"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>

                        <v-flex sm4></v-flex>
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

	export default {
		$_veeValidate: {
      		validator: 'new'
        },
        components: {
            'menu-ope': MenuOpe,
		},
    	data () {
      		return {
                titulo:"Nuevo",
                fixing: {
                    fecha: new Date().toISOString().substr(0, 10),
                },
        		status: false,
                loading: false,
                show_loading: false,

                show: false,
                menu1: false,

      		}
        },
        computed: {
            computedFecha() {
                moment.locale('es');
                return this.fixing.fecha ? moment(this.fixing.fecha).format('L') : '';
            },
            computedFModFormat() {
                moment.locale('es');
                return this.fixing.updated_at ? moment(this.fixing.updated_at).format('D/MM/YYYY H:mm') : '';
            },
            computedFCreFormat() {
                moment.locale('es');
                return this.fixing.created_at ? moment(this.fixing.created_at).format('D/MM/YYYY H:mm') : '';
            },
        },
    	methods:{
            submit() {
                if (this.loading === false){
                    this.loading = true;

                    var url = "/mto/fixings";

                    this.$validator.validateAll().then((result) => {
                        if (result){
                            axios.post(url, this.fixing)
                                .then(response => {
                                    this.$router.push({ name: 'fixing.edit', params: { id: response.data.registro.id } })
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
