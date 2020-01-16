
<template>
    <div>
        <loading :show_loading="show_loading"></loading>
        <v-container v-if="registros">
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
                            :items="this.cruces"
                            :search="search"
                            :pagination.sync="pagination"
                            rows-per-page-text="Registros por página"
                            >
                                <template slot="items" slot-scope="props">
                                    <td>{{ props.item.id }}</td>
                                    <td>{{ props.item.comven }}</td>
                                    <td>{{ props.item.destino.nombre }}</td>
                                    <td class="justify-center layout px-0">
                                        <v-icon
                                            small
                                            class="mr-2"
                                            @click="editItem(props.item.id)"
                                        >
                                            edit
                                        </v-icon>


                                        <v-icon v-if="isAdmin"
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
import {mapGetters} from 'vuex'
import {mapActions} from "vuex";
  export default {
    components: {
        'my-dialog': MyDialog,
        'menu-ope': MenuOpe,
        'loading': Loading
    },
    data () {
      return {
        titulo: "Cruces de Caja",
        paginaActual:{},
        pagination:{
            model: "cruce",
            descending: false,
            page: 1,
            rowsPerPage: 10,
            sortBy: "id",
        },
        search:"",
        headers: [
            {
                text: 'Id',
                align: 'left',
                value: 'nombre'
            },
            {
                text: 'Operación',
                align: 'left',
                value: 'comven'
            },
            {
                text: 'Destino',
                align: 'left',
                value: 'empresa.nombre'
            },
            {
                text: 'Acciones',
                align: 'Center',
                value: ''
            }
        ],
        cruces:[],
        status: false,
		registros: false,
        dialog: false,
        cruce_id: 0,
        show_loading: true

      }
    },
    mounted()
    {

        axios.get('/mto/cruces')
            .then(res => {
                
                this.cruces = res.data;
                this.registros = true;

            })
            .catch(err =>{
               ;
                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'dash' })
            })
            .finally(()=> {
                    this.show_loading = false;
                });
    },
    computed: {
        ...mapGetters([
            'isAdmin',
        ])
    },
    methods:{
        create(){
            this.$router.push({ name: 'cruce.create'})
        },
        editItem (id) {
            this.$router.push({ name: 'cruce.edit', params: { id: id } })
        },
        openDialog (id){
            this.dialog = true;
            this.cruce_id = id;
        },
        destroyReg () {
            this.dialog = false;

            axios.post('/mto/cruces/'+this.cruce_id,{_method: 'delete'})
                .then(response => {

                if (response.status == 200){
                    this.$toast.success('Registro eliminado!');
                    this.cruces = response.data;
                }
                })
            .catch(err => {
                this.status = true;

                var msg = err.response.data.message;
                this.$toast.error(msg);

            });

        }
    }
  }
</script>

