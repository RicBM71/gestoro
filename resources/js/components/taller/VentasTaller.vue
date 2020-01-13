<template>
    <v-card>
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
                    :items="this.arr_reg"
                    :search="search"
                    @update:pagination="updateEventPagina"
                    :pagination.sync="pagination"
                    rows-per-page-text="Registros por página"
                    >
                        <template slot="items" slot-scope="props">
                            <td>{{ props.item.alb_ser }}</td>
                            <td>{{ formatDate(props.item.fecha_albaran) }}</td>
                            <td>{{ props.item.cliente.razon }}</td>
                            <td>{{ formatDate(props.item.fecha_factura) }}</td>
                            <td :class="props.item.fase.color">{{ props.item.fase.nombre }}</td>
                            <td>{{ props.item.notas }}</td>
                            <td class="justify-center layout px-0">
                                <v-icon
                                    small
                                    class="mr-2"
                                    @click="editItem(props.item)"
                                >
                                    edit
                                </v-icon>
                            </td>
                        </template>
                        <template slot="pageText" slot-scope="props">
                            Registros {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
                        </template>
                    </v-data-table>
                </v-flex>
            </v-layout>
        </v-container>
    </v-card>
</template>
<script>
import moment from 'moment'
import {mapGetters} from 'vuex'
import {mapActions} from 'vuex'
  export default {
    props: {
        taller: Object
    },
    data () {
      return {
        paginaActual:{},
        pagination:{
            model: "ventaller",
            descending: true,
            page: 1,
            rowsPerPage: 10,
            sortBy: "alb_ser",
        },
        search:"",
        headers: [
            {
                text: 'Número',
                align: 'left',
                value: 'alb_ser',
                width: '12%'
            },
            {
                text: 'F. Albarán',
                align: 'left',
                value: 'fecha_albaran',
                width: '12%'
            },
            {
                text: 'Cliente',
                align: 'left',
                value: 'cliente.razon',
                width: '8%'
            },
            {
                text: 'Factura',
                align: 'left',
                value: 'fac_ser',
                width: '1%'
            },
            {
                text: 'Fase',
                align: 'left',
                value: 'fase.nombre',
                width: '8%'
            },
            {
                text: 'Observaciones',
                align: 'left',
                value: 'notas'
            },
            {
                text: 'Acciones',
                align: 'Center',
                value: '',
                width: '2%'
            }
        ],
        arr_reg:[],
        status: false,
        registros: false,
        ruta:'taller',

        url: "/utilidades/helptaller/ventas",

      }
    },
    mounted()
    {

        if (this.getPagination.model == this.pagination.model)
            this.updatePosPagina(this.getPagination);
        else
            this.unsetPagination();

        axios.post(this.url,{taller_id: this.taller.id})
            .then(res => {
                this.arr_reg = res.data;
                this.registros = true;
                this.show_loading = false;
            })
            .catch(err =>{
                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'dash' })
            })
    },
    computed: {
        ...mapGetters([
            'getPagination'
        ])
    },
    methods:{
        ...mapActions([
            'setPagination',
            'unsetPagination'
		]),
        updateEventPagina(obj){

            this.paginaActual = obj;

        },
        updatePosPagina(pag){

            this.pagination.page = pag.page;
            this.pagination.descending = pag.descending;
            this.pagination.rowsPerPage= pag.rowsPerPage;
            this.pagination.sortBy = pag.sortBy;

        },
        formatDate(f){
            if (f == null) return null;
            moment.locale('es');
            return moment(f).format('DD/MM/YYYY');
        },
        editItem (item) {
            this.setPagination(this.paginaActual);

            this.$router.push({ name: 'albaran.edit', params: { id: item.id } })

        },
    }
  }
</script>
