<template>
    <div v-if="registros">
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
                <v-flex xs12>
                    <v-data-table
                    :headers="headers"
                    :items="this.roles"
                    rows-per-page-text="Registros por página"
                    >
                        <template slot="items" slot-scope="props">
                            <td>{{ props.item.id }}</td>
                            <td class="text-xs-left">{{ props.item.name }}</td>
                            <td class="text-xs-left">{{ getPermisos(props.item.permissions) }}</td>
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
        titulo: "Roles",
        headers: [
          {
            text: 'ID',
            align: 'center',
            value: 'id'
          },
          {
            text: 'Nombre',
            align: 'left',
            value: 'name'
          },
          {
            text: 'Permisos',
            align: 'left',
            value: 'permissions',
            sortable: false

          },
          { text: 'Guard', align: 'left', value: 'guard_name' },
          { text: 'Acciones', align: 'left', value: 'acciones', sortable: false, }
        ],
        roles:[],

		registros: false,
        dialog: false,
        role_id: 0
      }
    },
    mounted()
    {

        axios.get('admin/roles')
            .then(res => {

                this.roles = res.data.roles;
                this.registros = true;
            })
    },
    methods:{

        getPermisos(per){
            if (per.length == 0) return null;
            var p = per.map(function(x) {
                    return x.nombre;
                });
            return p.toString();
        },
        editItem (id) {
            this.$router.push({ name: 'roles_edit', params: { id: id } })
        },
    }
  }
</script>
