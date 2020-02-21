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
                <v-flex xs12 v-if="registros">
                    <v-data-table
                    :headers="headers"
                    :items="arr_reg"
                    :search="search"
                    @update:pagination="updateEventPagina"
                    :pagination.sync="pagination"
                    rows-per-page-text="Registros por página"
                    >
                        <template slot="items" slot-scope="props">
                            <td>{{ props.item.empresa}}</td>
                            <td>{{ props.item.serie_albaran+props.item.albaran }}</td>
                            <td>{{ formatDate(props.item.fecha_albaran) }}</td>
                            <td v-if="props.item.factura > 0">{{ props.item.serie_factura+props.item.factura }}</td>
                            <td v-else>-</td>
                            <td>{{ formatDate(props.item.fecha_factura) }}</td>
                            <td :class="props.item.color">{{ props.item.fase }}</td>
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
            sortBy: "albaran",
        },
        search:"",
        headers: [
             {
                text: 'Número',
                align: 'left',
                value: 'empresa',
                width: '12%'
            },
            {
                text: 'Número',
                align: 'left',
                value: 'albaran',
                width: '12%'
            },
            {
                text: 'F. Albarán',
                align: 'left',
                value: 'fecha_albaran',
                width: '12%'
            },
            {
                text: 'Factura',
                align: 'left',
                value: 'serie_factura',
                width: '1%'
            },
            {
                text: 'F. Factura',
                align: 'left',
                value: 'fecha_factura',
                width: '8%'
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
                value: 'albaran',
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

        // if (this.getPagination.model == this.pagination.model)
        //     this.updatePosPagina(this.getPagination);
        // else
        //     this.unsetPagination();

        axios.post(this.url,{producto_id: this.producto_id})
            .then(res => {

                this.arr_reg = res.data.albalins;
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
            'getPagination',
            'empresaActiva'
        ])
    },
    methods:{
        ...mapActions([
            'setPagination',
          //  'unsetPagination'
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

            if (f == null) return '';
            moment.locale('es');
            return moment(f).format('DD/MM/YYYY');
        },
        editItem (item) {
            this.setPagination(this.paginaActual);

            this.$router.push({ name: 'albaran.edit', params: { id: item.albaran_id } })

        },
    }
  }
</script>
