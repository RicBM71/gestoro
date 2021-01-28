<template>
    <v-form>
        <v-container>
            <v-layout row wrap>
                <v-flex sm2>
                    <v-select
                        v-model="find.clase_id"
                        v-validate="'numeric'"
                        data-vv-name="clase_id"
                        data-vv-as="clase"
                        :error-messages="errors.collect('clase_id')"
                        :items="clases"
                        label="Clase"
                        required
                    ></v-select>
                </v-flex>
                <v-flex sm2>
                    <v-select
                        v-model="find.rfid_id"
                        v-validate="'numeric'"
                        data-vv-name="rfid_id"
                        data-vv-as="estado"
                        :error-messages="errors.collect('rfid_id')"
                        :items="rfids"
                        label="Estado RFID"
                        ></v-select>
                </v-flex>
                <v-flex sm2>
                    <v-select
                        v-model="find.categoria_id"
                        v-validate="'numeric'"
                        data-vv-name="categoria_id"
                        data-vv-as="categoría"
                        :error-messages="errors.collect('categoria_id')"
                        :items="categorias"
                        label="Categoría"
                        ></v-select>
                </v-flex>
                <v-switch
                    label="Activos"
                    v-model="find.alta"
                    color="primary">
                ></v-switch>
                <v-flex sm4></v-flex>
                <v-flex sm1>
                    <v-btn @click="submit"  :loading="loading" round small block  color="info">
                        Filtrar
                    </v-btn>
                </v-flex>
            </v-layout>
        </v-container>
    </v-form>
</template>
<script>
import moment from 'moment'
export default {
    $_veeValidate: {
        validator: 'new'
    },
    props:{
        filtro: Boolean,
        items: Array
    },
    data () {
      return {

            loading: false,
            result: false,

            categorias:[],
            clases: [],
            rfids: [],
            find: {
                rfid_id: 3,
                clase_id: null,
                categoria_id: null,
                alta: false
            },

      }
    },
    mounted(){

        axios.get('/mto/recuentos/create')
            .then(res => {

                this.rfids = res.data.rfids;
                this.clases = res.data.clases;
                this.categorias = res.data.categorias;

                this.clases.push({value:null,text:"---"});
                this.rfids.push({value:null,text:"---"});
                this.categorias.push({value:null,text:"---"});
            })
            .catch(err => {
                console.log(err);
                this.$toast.error('Error al montar <filtro-rec>');
            })
    },
    computed: {
    },
    methods:{

        submit(){

            if (this.loading === false){
                this.$validator.validateAll().then((result) => {
                    if (result){
                        this.loading = true;
                        axios.post('/mto/recuentos/filtrar',this.find)
                        .then(res => {


                            this.$emit('update:items', res.data);

                            if (res.data.length == 0)
                                this.$toast.warning('No se han encontrado referencias');
                            else
                                this.$emit('update:filtro', false);

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
                 });
            }
        },
    }
}
</script>
