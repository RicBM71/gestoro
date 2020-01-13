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
                    :items="arr_reg"
                    :search="search"
                    @update:pagination="updateEventPagina"
                    :pagination.sync="pagination"
                    rows-per-page-text="Registros por página"
                    >
                        <template slot="items" slot-scope="props">
                            <td>{{ props.item.albaran.alb_ser }}</td>
                            <td>{{ formatDate(props.item.albaran.fecha_albaran) }}</td>
                            <td>{{ props.item.albaran.fac_ser }}</td>
                            <td>{{ formatDate(props.item.albaran.fecha_factura) }}</td>
                            <td :class="props.item.albaran.fase.color">{{ props.item.albaran.fase.nombre }}</td>
                            <td>{{ props.item.albaran.notas }}</td>
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
        producto_id: Number
    },
    data () {
      return {
        paginaActual:{},
        pagination:{
            model: "proalb",
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
                value: 'albaran.alb_ser',
                width: '12%'
            },
            {
                text: 'F. Albarán',
                align: 'left',
                value: 'albaran.fecha_albaran',
                width: '12%'
            },
            {
                text: 'F. Factura',
                align: 'left',
                value: 'albaran.fecha_factura',
                width: '8%'
            },
            {
                text: 'Factura',
                align: 'left',
                value: 'albaran.fac_ser',
                width: '1%'
            },
            {
                text: 'Fase',
                align: 'left',
                value: 'albaran.fase.nombre',
                width: '8%'
            },
            {
                text: 'Observaciones',
                align: 'left',
                value: 'albaran.notas'
            },
            {
                text: 'Acciones',
                align: 'Center',
                value: 'albaran.id',
                width: '2%'
            }
        ],
        arr_reg:[],
        status: false,
        registros: false,
        ruta:'cliente',

        url: "/utilidades/helppro/albaranes",

      }
    },
    mounted()
    {

        if (this.getPagination.model == this.pagination.model)
            this.updatePosPagina(this.getPagination);
        else
            this.unsetPagination();

        axios.post(this.url,{producto_id: this.producto_id})
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

            this.$router.push({ name: 'albaran.edit', params: { id: item.albaran.id } })

        },
    }
  }
</script>
