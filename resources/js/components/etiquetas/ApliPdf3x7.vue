<template>
    <div>
        <v-card>
            <loading :show_loading="show_loading"></loading>
            <v-card-title color="indigo">
                <h2 color="indigo">Etiquetas Apli Ref. 02414 - 01992</h2>
                <v-spacer></v-spacer>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                <v-container>
                    <v-layout row wrap>
                        <v-flex sm3></v-flex>
                        <v-flex sm3>
                            <v-select
                                v-model="form.clase_id"
                                v-validate="'numeric'"
                                data-vv-name="clase_id"
                                data-vv-as="clase"
                                :error-messages="errors.collect('clase_id')"
                                :items="clases"
                                label="Clase"
                                required
                                ></v-select>
                        </v-flex>
                    </v-layout>
                     <v-layout row wrap>
                        <v-flex sm3></v-flex>
                        <v-flex sm3 d-flex>
                            <v-select
                                v-model="form.etiqueta_id"
                                v-validate="'required'"
                                data-vv-name="etiqueta_id"
                                data-vv-as="estado"
                                :error-messages="errors.collect('etiqueta_id')"
                                :items="etiquetas"
                                label="Etiqueta"
                            ></v-select>
                        </v-flex>
                     </v-layout>
                    <v-layout row wrap>
                        <v-flex sm3></v-flex>
                        <v-flex sm1 d-flex>
                            <v-text-field
                                v-model="form.fila"
                                v-validate="'required|between:1,7'"
                                :error-messages="errors.collect('fila')"
                                label="Fila"
                                data-vv-name="fila"
                                data-vv-as="fila"
                                class="inputPrice"
                                type="number"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1 d-flex></v-flex>
                        <v-flex sm1 d-flex>
                            <v-text-field
                                v-model="form.columna"
                                v-validate="'required|between:1,3'"
                                :error-messages="errors.collect('columna')"
                                label="Columna"
                                data-vv-name="columna"
                                data-vv-as="columna"
                                class="inputPrice"
                                type="number"
                                v-on:keyup.enter="submit"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex sm1 d-flex></v-flex>
                        <v-flex sm1>
                            <v-btn @click="submit" :loading="show_loading" flat round small block  color="info">
                                Enviar
                            </v-btn>
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
        form:{
            etiqueta_id: 3,
            clase_id: 6,
            fila: 1,
            columna: 1,
            limite: 0
        },
        etiquetas: [],
        clases:[],
        show_loading: true
      }
    },
    mounted(){
        axios.get('/etiquetas/aplipdf')
            .then(res => {

                this.etiquetas = res.data.etiquetas;
                this.clases = res.data.clases;
                //this.form.etiqueta_id = this.etiquetas[0].value;
                this.clases.push({value:null,text:"---"});
            })
            .catch(err => {
                this.$toast.error(err.response.data.message);
                //this.$toast.error('Error al montar <apli-pdf>');
            })
            .finally(()=> {
                this.show_loading = false;
            });
    },
    computed: {
        ...mapGetters([
        ]),
    },
    methods:{
        submit(){


            if (this.show_loading === false){
                this.$validator.validateAll().then((result) => {
                    if (result){

                        this.show_loading = true;
                        axios({
                            url: '/etiquetas/aplipdf3x7',
                            method: 'POST',
                            responseType: 'blob',
                            data: this.form,
                            })
                        .then(res => {


                            let blob = new Blob([res.data], { type: 'data:pdf'})
                            let link = document.createElement('a')
                            link.href = window.URL.createObjectURL(blob)

                            link.download = 'Etiquetas Apli.pdf';

                            document.body.appendChild(link);
                            link.click()
                            document.body.removeChild(link);

                            this.$toast.success('Etiquetas generadas correctamente '+link.download);

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
                            }else if(err.request.status == 404){
                                this.$toast.error('No hay etiquetas');
                            }
                            else{
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
<style scoped>

.inputPrice >>> input {
  text-align: center;
  -moz-appearance:textfield;
}
</style>
