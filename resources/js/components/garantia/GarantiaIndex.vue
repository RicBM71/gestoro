
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
                            :items="this.garantias"
                            :search="search"
                            @update:pagination="updateEventPagina"
                            :pagination.sync="pagination"
                            rows-per-page-text="Registros por página"
                            >
                                <template slot="items" slot-scope="props">
                                    <td>{{ props.item.nombre }}</td>
                                    <td>{{ props.item.leyenda }}</td>
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
        titulo: "Garantias",
        paginaActual:{},
        pagination:{
            model: "garantia",
            descending: false,
            page: 1,
            rowsPerPage: 10,
            sortBy: "id",
        },
        search:"",
        headers: [
            {
                text: 'Nombre',
                align: 'left',
                value: 'nombre'
            },
            {
                text: 'Leyenda',
                align: 'left',
                value: 'leyenda'
            },
            {
                text: 'Acciones',
                align: 'Center',
                value: ''
            }
        ],
        garantias:[],
        status: false,
		registros: false,
        dialog: false,
        garantia_id: 0,
        show_loading: true

      }
    },
    mounted()
    {

        if (this.getPagination.model == this.pagination.model)
            this.updatePosPagina(this.getPagination);
        else
            this.unsetPagination();

        axios.get('/mto/garantias')
            .then(res => {
                this.garantias = res.data;
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
        create(){
            this.$router.push({ name: 'garantia.create'})
        },
        editItem (id) {
            this.setPagination(this.paginaActual);
            this.$router.push({ name: 'garantia.edit', params: { id: id } })
        },
        openDialog (id){
            this.dialog = true;
            this.garantia_id = id;
        },
        destroyReg () {
            this.dialog = false;

            axios.post('/mto/garantias/'+this.garantia_id,{_method: 'delete'})
                .then(response => {

                if (response.status == 200){
                    this.$toast.success('Registro eliminado!');
                    this.garantias = response.data;
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

<style scoped>
  .tachado{text-decoration:line-through;}
</style>
