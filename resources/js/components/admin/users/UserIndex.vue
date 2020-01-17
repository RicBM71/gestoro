<template>
    <div v-if="registros">
		<my-dialog :dialog.sync="dialog" registro="registro" @destroyReg="destroyReg"></my-dialog>
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
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-data-table
                        :search="search"
                        :pagination.sync="pagination"
                        :headers="headers"
                        :items="usuarios"
                        rows-per-page-text="Registros por página"
                        >
                            <template slot="items" slot-scope="props">
                                <td  v-if="props.item.blocked == false"  class="text-xs-left">{{ props.item.name + " " + props.item.lastname }}</td>
                                <td v-else class="text-xs-left"><span class="red--text">BLOQUEADO -></span></td>
                                <td class="text-xs-left">{{ props.item.username }}</td>
                                <td>{{ props.item.email }}</td>
                                <td class="text-xs-left">{{ formatDate(props.item.login_at) }}</td>
                                <td class="text-xs-left">{{ extrae(props.item.roles) }}</td>
                                <td class="justify-center layout px-0">
                                    <v-icon
                                        small
                                        class="mr-2"
                                        @click="editItem(props.item.id)"
                                    >
                                        edit
                                    </v-icon>


                                    <v-icon
                                        v-if="isRoot"
                                        small
                                        @click="openDialog(props.item.id)"
                                        >
                                        delete
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
import moment from 'moment';
import {mapGetters} from 'vuex';
import MyDialog from '@/components/shared/MyDialog'
import MenuOpe from './MenuOpe'
  export default {
    components: {
        'my-dialog': MyDialog,
        'menu-ope': MenuOpe,
    },
    data () {
      return {
        titulo: "Usuarios",
        search:"",
        pagination:{
            rowsPerPage: 10,
        },
        headers: [
          {
            text: 'Nombre',
            align: 'left',
            value: 'name'
          },
          { text: 'Username', align: 'left', value: 'username' },
           {
            text: 'Mail',
            align: 'left',
            value: 'email'
          },
          { text: 'Login', align: 'left', value: 'login_at' },
          { text: 'Roles', align: 'left', value: 'roles', sortable: false, },
          { text: 'Acciones', align: 'left', value: 'acciones', sortable: false, }
        ],
        usuarios:[],
        status: false,
		registros: false,
        dialog: false,
        user_id: 0
      }
    },
    mounted()
    {

        axios.get('/admin/users')
            .then(res => {

                this.usuarios = res.data;
                this.registros = true;
            })
            .catch(err =>{

                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'dash' })
            })
    },
    computed: {
        ...mapGetters([
	    	'isRoot'
        ]),
    },
    methods:{
        formatDate(f){
            if (f == null) return null;
            moment.locale('es');
            return moment(f).format('DD/MM/YYYY HH:mm:ss');
        },
        create(){
            this.$router.push({ name: 'users_create', params: { id: '0' } })
        },
        extrae: function(role){ // extrae los permisos de cada role.

            var i=0;
            var roles = "";
            role.forEach(function(element) {
            roles += (element.name);
            ++i;
            if (i < role.length)
                roles+=", ";
            });

            return roles;
        },
        editItem (id) {
            this.$router.push({ name: 'users.edit', params: { id: id } })
        },
        openDialog (id){
            this.dialog = true;
            this.user_id = id;
        },
        destroyReg () {
            this.dialog = false;

            axios.post('/admin/users/'+this.user_id,{_method: 'delete'})
                .then(response => {

                if (response.status == 200){
                    this.$toast.success('Usuario borrado correctamente');
                    this.usuarios = response.data;
                }
                })
            .catch(err => {
                this.status = true;
                var msg = err.response.data.message;
                this.$toast.error(msg);

            });

        },

    }
  }
</script>

<style scoped>
  .tachado{text-decoration:line-through;}
</style>
