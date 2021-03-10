<template>
    <div>
        <v-card>
            <v-card-title>
                <h2>{{titulo}}</h2>
                <v-spacer></v-spacer>
                <menu-ope></menu-ope>
            </v-card-title>
        </v-card>
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
            <v-layout row wrap  v-if="registros">
                <v-flex xs12>
                    <v-data-table
                        :search="search"
                        :headers="headers"
                        :items="permisos"
                        :pagination.sync="pagination"
                        rows-per-page-text="Registros por página"
                    >
                        <template slot="items" slot-scope="props">
                            <td class="text-xs-left">{{ props.item.name }}</td>
                            <td class="text-xs-left">{{ props.item.nombre }}</td>
                            <td class="text-xs-left">{{ props.item.guard_name }}</td>
                            <td class="justify-center layout px-0">
                                <v-icon
                                    small
                                    class="mr-2"
                                    @click="editItem(props.item.id)"
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
    </div>
</template>
<script>
import MenuOpe from './MenuOpe'
  export default {
    components: {
        'menu-ope': MenuOpe
    },
    data () {
      return {
        titulo: "Permisos",
        search:'',
        pagination:{
            descending: true,
            page: 1,
            rowsPerPage: 10
        },
        headers: [
          {
            text: 'Name',
            align: 'left',
            value: 'name'
          },
          {
            text: 'Nombre',
            align: 'left',
            value: 'nombre'
          },
          { text: 'Guard', align: 'left', value: 'guard_name' },
          { text: 'Acciones', align: 'left', value: 'acciones', sortable: false, }
        ],
        permisos:[],

		registros: false,
        dialog: false,
      }
    },
    mounted()
    {

        axios.get('admin/permissions')
            .then(res => {

                this.permisos = res.data;
                this.registros = true;
            })
    },
    methods:{
        editItem (id) {
            this.$router.push({ name: 'permisos.edit', params: { id: id } })
        },
    }
  }
</script>
