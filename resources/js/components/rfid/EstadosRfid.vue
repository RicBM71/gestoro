<template>
    <div>
        <v-card>
            <loading :show_loading="show_loading"></loading>
            <v-card-title color="indigo">
                <h2 color="indigo">Estados RFID</h2>
            </v-card-title>
        </v-card>
        <v-card v-if="items.length > 0">
            <v-container>
                <v-layout row wrap>
                    <v-flex xs6></v-flex>
                    <v-flex xs6>
                        <v-spacer></v-spacer>
                        <v-text-field
                            v-model="search"
                            append-icon="search"
                            label="Buscar"
                            single-line
                            hide-details
                        ></v-text-field>
                    </v-flex>
                </v-layout>
                <br/>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-data-table
                            :headers="headers"
                            :search="search"
                            :pagination.sync="pagination"
                            :items="items"
                            rows-per-page-text="Registros por página"
                        >
                            <template slot="items" slot-scope="props">
                                <td>{{ props.item.empresa}}</td>
                                <td>{{ props.item.estado }}</td>
                                <td class="text-xs-right">{{ props.item.registros | currency('', 0, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                            </template>
                            <template slot="pageText" slot-scope="props">
                                Registros {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
                            </template>
                        </v-data-table>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card>
    </div>
</template>
<script>
import moment from 'moment'
import {mapGetters} from 'vuex'
import {mapActions} from "vuex";
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
            headers: [
            {
                text: 'Empresa Origen',
                align: 'left',
                value: 'empresa',
            },
            {
                text: 'Estado',
                align: 'left',
                value: 'estado',
                width: '30%'
            },
            {
                text: 'Etiquetas',
                align: 'right',
                value: 'registros',
                width: '30%'
            }],
            search:"",
            paginaActual:{},
            pagination:{
                descending: true,
                page: 1,
                rowsPerPage: 10,
                sortBy: "empresa",
            },
            show_loading: true,
            items:[],
      }
    },
    mounted(){

         axios.get('/rfid/estadosr')
            .then(res => {
                this.items = res.data.estados;
            })
            .catch(err =>{
                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'dash' })
            })
            .finally(()=> {
                this.show_loading = false;
            });

    },
    computed: {
    },
    methods:{
    }
}
</script>
