<template>
    <div v-if="productos.length > 0">
        <v-layout row wrap>
            <v-flex xs12>
                <v-data-table
                    :headers="headers"
                    :items="productos"
                    :pagination.sync="pagination"
                    rows-per-page-text="Registros por página"
                >
                <template slot="items" slot-scope="props">
                    <td>{{ props.item.referencia }}</td>
                    <td v-if="props.item.deleted_at==null">{{ props.item.nombre }}</td>
                    <td v-else class="tachado">{{ props.item.nombre }}</td>
                    <td>{{ props.item.clase.nombre }} <span v-if="props.item.quilates != null">{{props.item.quilates}}</span></td>
                    <td class="text-xs-right">{{ props.item.peso_gr | currency('', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                    <td class="text-xs-right">{{ props.item.precio_coste | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                    <td class="text-xs-right">{{ props.item.precio_venta | currency('€', 2, { thousandsSeparator:'.', decimalSeparator: ',', symbolOnLeft: false })}}</td>
                    <td class="justify-center layout px-0">
                        <v-icon
                            small
                            class="mr-2"
                            @click="editItem(props.item.id)"
                        >
                            local_offer
                        </v-icon>
                    </td>
                </template>
                    <template slot="pageText" slot-scope="props">
                        Registros {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
                    </template>
                </v-data-table>
            </v-flex>
        </v-layout>
    </div>
</template>
<script>
import {mapGetters} from 'vuex';
import moment from 'moment'
export default {
    props:{
        productos: Array,
    },
   data () {
      return {
        search:"",
        pagination:{
            descending: true,
            page: 1,
            rowsPerPage: 10,
            sortBy: "id",
        },
        headers: [
            {
                text: 'Referencia',
                align: 'left',
                value: 'referencia'
            },
            {
                text: 'Nombre',
                align: 'left',
                value: 'nombre'
            },
            {
                text: 'Clase',
                align: 'left',
                value: 'clase.nombre'
            },
            {
                text: 'Peso',
                align: 'left',
                value: 'peso_gr'
            },
            {
                text: 'P. Coste',
                align: 'left',
                value: 'precio_coste'
            },
            {
                text: 'PVP',
                align: 'left',
                value: 'precio_venta'
            },
            {
                text: 'Acciones',
                align: 'Center',
                value: ''
            }
        ],

      }
    },
    mounted(){


    },
    methods:{
        editItem (id) {
            this.$router.push({ name:'producto.edit', params: { id: id } })
        },
    }

}
</script>

<style scoped>

table.v-table tbody td, table.v-table tbody th {
    height: 38px;
}
</style>
