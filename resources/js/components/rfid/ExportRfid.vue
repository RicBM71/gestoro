<template>
    <div>
        <v-card>
            <loading :show_loading="show_loading"></loading>
            <v-card-title color="indigo">
                <h2 color="indigo">Exportar etiquetas RFID</h2>
                <v-spacer></v-spacer>
            </v-card-title>
        </v-card>
        <v-card>
            <v-form>
                <v-container>
                    <v-layout row wrap>
                        <v-spacer></v-spacer>
                        <v-flex sm2>
                            <v-select
                                v-show="!perdidas"
                                v-model="etiqueta_id"
                                v-validate="'required'"
                                data-vv-name="etiqueta_id"
                                data-vv-as="etiqueta"
                                :error-messages="errors.collect('etiqueta_id')"
                                :items="etiquetas"
                                label="Estado Etiqueta"
                            ></v-select>
                       </v-flex>
                       <v-flex sm2>
                            <v-select
                                v-model="tag"
                                v-validate="'required'"
                                data-vv-name="tag"
                                data-vv-as="tag"
                                :error-messages="errors.collect('tag')"
                                :items="tags"
                                label="Grupos de"
                            ></v-select>
                       </v-flex>
                       <v-flex sm1>
                            <v-select
                                v-model="formato"
                                v-validate="'required'"
                                data-vv-name="formato"
                                data-vv-as="formato"
                                :error-messages="errors.collect('formato')"
                                :items="formatos"
                                label="Formato"
                                @change="setTags"
                            ></v-select>
                       </v-flex>
                       <v-flex sm2>
                            <v-switch
                                label="Exportar Perdidas"
                                v-model="perdidas"
                                color="primary">
                            ></v-switch>
                        </v-flex>
                        <v-flex sm2></v-flex>
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

            etiquetas: [],
            etiqueta_id:"",
            tags:[
                {value: 10, text: 10},
                {value: 50, text: 50},
                {value: 100, text: 100},
                {value: 500, text: 500},
            ],
            formato: 1,
            formatos:[
                {value: 1, text: 'RFID'},
                {value: 2, text: 'Simple'},
            ],
            tag: 10,
            perdidas: false,
            show_loading: false,
      }
    },
    mounted(){
        axios.get('/rfid/exportar')
            .then(res => {
                this.etiquetas = res.data.etiquetas;
                this.etiqueta_id = this.etiquetas[1].value;
            })
            .catch(err => {
                this.$toast.error('Error al montar export-rfid');
            })
    },
    computed: {
        ...mapGetters([
        ]),
    },
    methods:{
        setTags(){
            if (this.formato == 2)
                this.tag = 500;
        },
        submit(){


            if (this.show_loading === false){
                this.$validator.validateAll().then((result) => {
                    if (result){

                        this.show_loading = true;
                        axios({
                            url: '/rfid/exportar/download',
                            method: 'POST',
                            responseType: 'blob', // important
                            data:{
                                etiqueta_id: this.etiqueta_id,
                                tag:    this.tag,
                                perdidas: this.perdidas,
                                formato: this.formato
                            }
                            })
                        .then(res => {

                            var extension = this.formato == 1 ? '.rf' : '.csv';

                            let blob = new Blob([res.data])
                            let link = document.createElement('a')
                            link.href = window.URL.createObjectURL(blob)

                            if (this.perdidas)
                                link.download = "Ref. Perdidas."+new Date().getFullYear()+(new Date().getMonth()+1)+(new Date().getDate())+extension;
                            else
                                link.download = "Etiquetas."+new Date().getFullYear()+(new Date().getMonth()+1)+(new Date().getDate())+extension;

                            document.body.appendChild(link);
                            link.click()
                            document.body.removeChild(link);

                            this.$toast.success('Download Ok! '+link.download);

                            this.show_loading = false;
                        })
                        .catch(err => {
                            if (err.response.status == 404)
                                this.$toast.warning("No hay registros para exportar!");
                            else
                                this.$toast.error(err.response.message);

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
