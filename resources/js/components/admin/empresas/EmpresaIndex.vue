<template>
    <div>
        <loading :show_loading="show_loading"></loading>
        <v-container>
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
                    <br/>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-data-table
                            :headers="headers"
                            :items="empresas"
                            :search="search"
                            :pagination.sync="pagination"
                            rows-per-page-text="Registros por página"
                            >
                                <template slot="items" slot-scope="props">
                                    <td>{{ props.item.id }}</td>
                                    <td v-if="props.item.flags[0]==true">{{ props.item.nombre }}</td>
                                    <td v-else class="tachado red--text darken-4">{{ props.item.nombre }}</td>
                                    <td>{{ props.item.cif }}</td>
                                    <td v-if="props.item.deleted_at==null">{{ props.item.contacto }}</td>
                                    <td v-else class="red">Borrada {{props.item.deleted_at}}</td>
                                    <td>{{ props.item.telefono1 }}</td>
                                    <td class="justify-center layout px-0">
                                        <v-icon
                                            small
                                            class="mr-2"
                                            @click="editItem(props.item.id)"
                                        >
                                            edit
                                        </v-icon>


                                        <v-icon
                                            v-show="props.item.id!=empresaActiva"
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
        </v-container>
    </div>
</template>
<script>
import MyDialog from '@/components/shared/MyDialog'
import Loading from '@/components/shared/Loading'
import MenuOpe from './MenuOpe'
import {mapGetters} from 'vuex';
  export default {
    components: {
        'my-dialog': MyDialog,
        'menu-ope': MenuOpe,
        'loading': Loading
    },
    data () {
      return {
        titulo: "Empresas",
        search:"",
        headers: [
          {
            text: 'ID',
            align: 'center',
            value: 'id'
          },
          {
            text: 'Nombre',
            align: 'left',
            value: 'titulo'
          },
          {
            text: 'CIF',
            align: 'left',
            value: 'cif'
          },
          {
            text: 'Contacto',
            align: 'left',
            value: 'contacto'
          },
          {
            text: 'Teléfono',
            align: 'Left',
            value: 'telefono1'
          },
          {
            text: 'Acciones',
            align: 'Center',
            value: ''
          }
        ],
        pagination:{
            descending: true,
            page: 1,
            rowsPerPage: 10,
            sortBy: "id",
        },
        empresas:[],
        status: false,
        dialog: false,
        empresa_id: 0,
        titulo:"Empresas",
        show_loading: true
      }
    },
    mounted()
    {

        axios.get('/admin/empresas')
            .then(res => {
                this.empresas = res.data;
                this.show_loading= false;
            })
            .catch(err =>{

                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'dash' })
            })
    },
    computed: {
        ...mapGetters([
            'empresaActiva',
        ]),
    },
    methods:{
        create(){
            this.$router.push({ name: 'empresa.create'})
        },
        editItem (id) {
            this.$router.push({ name: 'empresa.edit', params: { id: id } })
        },
        openDialog (id){
            this.dialog = true;
            this.empresa_id = id;
        },
        destroyReg () {
            this.dialog = false;

            axios.post('/admin/empresas/'+this.empresa_id,{_method: 'delete'})
                .then(response => {
                    this.empresas = response.data;
                    this.$toast.success('Empresa eliminada!');

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
