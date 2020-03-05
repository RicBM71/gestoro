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
                <v-flex xs12 v-show="!show_loading">
                    <v-data-table
                    :headers="headers"
                    :items="this.arr_reg"
                    :search="search"
                    @update:pagination="updateEventPagina"
                    :pagination.sync="pagination"
                    rows-per-page-text="Registros por página"
                    >
                        <template slot="items" slot-scope="props">
                            <td>{{ props.item.empresa.titulo}}</td>
                            <td>{{ props.item.alb_ser }}</td>
                            <td>{{ formatDate(props.item.fecha_compra) }} {{ props.item.tipo.nombre[0] }}</td>
                            <td class="text-xs-right">{{ props.item.importe | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                            <td>{{ props.item.retraso }}</td>
                            <td :class="props.item.fase.color">{{ props.item.fase.nombre }}</td>
                            <td>{{ props.item.notas }}</td>
                            <td class="justify-center layout px-0">
                                <v-icon
                                    v-if="deEmpresaActiva(props.item.empresa_id)"
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
                <v-flex xs12 v-show="show_loading" class="text-sm-center">
                     <v-progress-linear :indeterminate="true"></v-progress-linear>

                    <span>Cargando...</span>
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
        cliente_id: Number
    },
    data () {
      return {
        paginaActual:{},
        pagination:{
            model: "comcli",
            descending: true,
            page: 1,
            rowsPerPage: 10,
            sortBy: "alb_ser",
        },
        search:"",
        headers: [
            {
                text: 'Empresa',
                align: 'left',
                value: 'empresa.titulo',
                width: '6%'
            },
            {
                text: 'Número',
                align: 'left',
                value: 'alb_ser',
                width: '12%'
            },
            {
                text: 'F. Compra',
                align: 'left',
                value: 'fecha_compra',
                width: '12%'
            },
            {
                text: 'Importe',
                align: 'left',
                value: 'importe',
                width: '8%'
            },
            {
                text: 'Ret',
                align: 'left',
                value: 'retraso',
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
        ruta:'compra',
        show_loading: true,

        url: "/utilidades/helpcli/compras",

      }
    },
    mounted()
    {
        this.show_loading = true;

        // if (this.getPagination.model == this.pagination.model)
        //     this.updatePosPagina(this.getPagination);
        // else
        //     this.unsetPagination();


        axios.post(this.url,{cliente_id: this.cliente_id})
            .then(res => {
                this.arr_reg = res.data;
                this.registros = true;
                this.show_loading = false;
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
        ...mapGetters([
            'getPagination',
            'empresaActiva'
        ])
    },
    methods:{
        ...mapActions([
            'setPagination',
            'unsetPagination'
        ]),
        deEmpresaActiva(empresa_id){
           return true;
           return empresa_id == this.empresaActiva;
        },
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
           // this.setPagination(this.paginaActual);

            var ruta = item.tipo_id == 1 ? 'compra' : 'recompra';

            if (item.fase_id <= 2)
                this.$router.push({ name: this.ruta+'.edit', params: { id: item.id } })
            else
                this.$router.push({ name: ruta+'.close', params: { id: item.id } })

        },
    }
  }
</script>
