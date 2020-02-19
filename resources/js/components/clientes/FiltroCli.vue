<template>
    <v-form>
        <v-container>
            <v-layout row wrap>
                <v-flex sm2 xs4>
                    <v-text-field
                        slot="activator"
                        label="Nombre"
                        v-validate="'min:3'"
                        v-model="query.razon"
                        hint="nom,ape ó razón"
                        data-vv-name="razon"
                        data-vv-as="razon"
                        :error-messages="errors.collect('razon')"
                        v-on:keyup.enter="submit"
                        ></v-text-field>
                </v-flex>
                <v-flex sm2 xs4>
                    <v-text-field
                        slot="activator"
                        label="Nº Documento"
                        v-validate="'min:3'"
                        v-model="query.dni"
                        data-vv-name="dni"
                        data-vv-as="dni"
                        :error-messages="errors.collect('dni')"
                        v-on:keyup.enter="submit"
                        ></v-text-field>
                </v-flex>
                <v-flex sm2 xs4>
                    <v-text-field
                        slot="activator"
                        label="Observaciones"
                        v-validate="'min:4'"
                        v-model="query.notas"
                        data-vv-name="notas"
                        data-vv-as="notas"
                        :error-messages="errors.collect('notas')"
                        v-on:keyup.enter="submit"
                        ></v-text-field>
                </v-flex>
                <v-flex sm1 xs3 d-flex>
                    <v-select
                        v-model="query.baja"
                        :items="bajas"
                        label="Clientes"
                    ></v-select>
                </v-flex>
                <v-flex sm1 xs3 d-flex>
                    <v-select
                        v-model="query.bloqueado"
                        :items="bloqcli"
                        label="Bloqueado"
                    ></v-select>
                </v-flex>
                <v-spacer></v-spacer>
                <v-flex sm2>
                    <v-btn @click="submit"  :loading="loading" round  small block  color="info">
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
        reg: Array
    },
    data () {
      return {
            query:{
                baja: 'A',
                razon: '',
                dni: '',
                bloqueado: 'T',
                notas: ''
            },

            bloqcli:[
                    {value: 'T', text:"-"},
                    {value: 'N', text:"No"},
                    {value: 'L', text:"BlackList"},
                    {value: 'B', text:"Bloqueado"},
                ],

            sino: [
                {text:'Si','value':'S'},
                {text:'No','value':'N'},
                {text:'---','value':''}
            ],

            bajas: [
                {text:'Alta','value':'A'},
                {text:'Baja','value':'B'},
                {text:'Todos','value':'T'}
            ],
            loading: false,
            result: false,

            fecha: new Date().toISOString().substr(0, 10),

      }
    },
    mounted(){


    },
    computed: {
        computedFecha() {
            moment.locale('es');
            return this.fecha ? moment(this.fecha).format('L') : '';
        }
    },
    methods:{
        submit(){

            if (this.loading === false){
                this.$validator.validateAll().then((result) => {
                    if (result){
                        this.loading = true;
                        axios.post('/mto/clientes/filtrar', this.query)
                        .then(res => {

                            this.loading = false;

                            this.$emit('update:reg', res.data);
                             if (res.data.length > 0)
                                this.$emit('update:filtro', false);
                            else
                              this.$toast.warning('No se han encontrado clientes');


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
