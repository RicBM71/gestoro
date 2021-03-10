<template>
    <div>
        <loading :show_loading="show_loading"></loading>
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
                        <br/>
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-data-table
                                :headers="headers"
                                :items="arr_reg"
                                :pagination.sync="pagination"
                                :search="search"
                                rows-per-page-text="Registros por página"
                                >
                                    <template slot="items" slot-scope="props">
                                        <td>{{ props.item.nombre }}</td>
                                        <td>{{ props.item.username }}</td>
                                        <td class="justify-center layout px-0">


                                            <v-icon
                                                small
                                                class="mr-2"
                                                @click="editItem(props.item.id)"
                                            >
                                                edit
                                            </v-icon>


                                            <v-icon
                                            v-show="delCaj"
                                            small
                                            @click="openDialog(props.item)"
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
    </div>
</template>
<script>
import moment from 'moment'
import MyDialog from '@/components/shared/MyDialog'
import Loading from '@/components/shared/Loading'
import MenuOpe from './MenuOpe'
import {mapGetters} from 'vuex'
import {mapActions} from "vuex";
  export default {
    components: {
        'my-dialog': MyDialog,
        'menu-ope': MenuOpe,
        'loading': Loading,
    },
    data () {
      return {
        titulo:"Apuntes",
        search:"",
        paginaActual:{},
        pagination:{
            model: "apuntes",
            descending: false,
            page: 1,
            rowsPerPage: 10,
            sortBy: "nombre",
            search: ""
        },
        headers: [
            {
            text: 'Nombre',
            align: 'left',
            value: 'nombre'
            },
            {
            text: 'Usuario',
            align: 'left',
            value: 'username'
            },
            {
            text: 'Acciones',
            align: 'Center',
            value: ''
            }
        ],
        item_destroy: {},
        arr_reg:[],
        status: false,
		registros: false,
        dialog: false,
        show_loading: true,
      }
    },
    mounted()
    {


        axios.get('/mto/apuntes')
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
            'delCaj',
        ]),
    },
    methods:{
        editItem (id) {
            this.$router.push({ name: 'apunte.edit', params: { id: id } })
        },
        openDialog (item){
            this.dialog = true;
            this.item_destroy = item;
        },
        destroyReg () {

            this.dialog = false;

            axios.post('/mto/apuntes/'+this.item_destroy.id,{_method: 'delete'})
                .then(response => {

                const index = this.arr_reg.indexOf(this.item_destroy)
                this.arr_reg.splice(index, 1)

                //this.arr_reg = response.data.apuntes;
                this.$toast.success(response.data.msg);


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
.tachado{
    text-decoration: line-through
}
</style>
