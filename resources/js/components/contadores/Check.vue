<template>
    <div>
        <v-card>
            <loading :show_loading="show_loading"></loading>
            <v-card-title color="indigo">
                <h2 color="indigo">{{titulo}}</h2>
            </v-card-title>
        </v-card>
        <v-card v-if="!show_loading">
            <v-form>
                <v-container>
                    <v-layout row wrap>
                        <v-spacer></v-spacer>
                        <v-flex sm2>
                            <v-text-field
                                v-model="ejercicio"
                                v-validate="'required|integer'"
                                :error-messages="errors.collect('ejercicio')"
                                label="Ejercicio"
                                data-vv-name="ejercicio"
                                data-vv-as="ejercicio"
                                required
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                            <v-text-field v-show="false"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm2>
                            <v-btn @click="submit" :loading="show_loading" flat round small block  color="info">
                                Enviar
                            </v-btn>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <div>
                                <table class="v-datatable v-table theme--light">
                                    <thead>
                                        <tr>
                                            <th>Empresa</th>
                                            <th>Tipo</th>
                                            <th>Concepto</th>
                                            <th>Contador</th>
                                            <th>Recuento</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in items" :key="index">
                                            <td>{{ item.empresa}}</td>
                                            <td>{{ item.operacion}}</td>
                                            <td>{{ item.nombre }}</td>
                                            <td class="text-xs-center">{{ item.contador }}</td>
                                            <td class="text-xs-center">{{ item.recuento }}</td>
                                            <td v-if="validar(item)" class="text-xs-center green--text">OK</td>
                                            <td v-else class="text-xs-center red--text">Error Contador!</td>
                                        </tr>
                                    </tbody>
                                </table>
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
import {mapGetters} from 'vuex';
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
            show_loading: false,
            ejercicio:new Date().toISOString().substr(0, 4),
            items: [],
            titulo: 'Check Contadores',
      }
    },
    mounted(){
        this.submit();
    },
    computed: {
        ...mapGetters([
        ]),
    },
    methods:{
        validar(item){
            return item.contador == item.recuento ? true :  false;
        },
        submit(){

            if (this.show_loading === false){
                this.show_loading = true;
                this.$validator.validateAll().then((result) => {
                    if (result){

                        axios({
                            url: '/utilidades/check/'+this.ejercicio,
                            method: 'GET',
                            })
                        .then(res => {
                            this.items = res.data;

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
                        })
                        .finally(()=> {
                             this.show_loading = false;
                        });
                    }

                });

            }
        },
    }
}
</script>
