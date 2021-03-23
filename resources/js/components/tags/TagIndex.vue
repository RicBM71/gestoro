<template>
    <div>
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
                <v-layout row wrap  v-if="registros">
                    <v-flex xs3></v-flex>
                    <v-flex xs6>
                        <v-data-table
                        :headers="headers"
                        :items="this.tags"
                        rows-per-page-text="Registros por página"
                        >
                            <template slot="items" slot-scope="props">
                                <td>{{ props.item.nombre }}</td>
                                <td class="justify-center layout px-0">
                                    <v-icon
                                        small
                                        class="mr-2"
                                        @click="editItem(props.item.id)"
                                    >
                                        edit
                                    </v-icon>
                                    <v-icon
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
import MyDialog from '@/components/shared/MyDialog'
import MenuOpe from './MenuOpe'
  export default {
    components: {
        'my-dialog': MyDialog,
        'menu-ope': MenuOpe,
    },
    data () {
      return {
        headers: [
          {
            text: 'Nombre',
            align: 'left',
            value: 'name'
          },
          {
            text: 'Acciones',
            align: 'Center',
            value: ''
          }
        ],
        tags:[],
        status: false,
		registros: false,
        dialog: false,
        tag_id: 0,
        titulo:"Tags Marketing"
      }
    },
    mounted()
    {

        axios.get('/mto/tags')
            .then(res => {
                this.tags = res.data;
                this.registros = true;
            })
            .catch(err =>{

                this.$toast.error(err.response.data.message);
                this.$router.push({ name: 'dash' })
            })
    },
    methods:{
        create(){
            this.$router.push({ name: 'tag.create'})
        },
        editItem (id) {
            this.$router.push({ name: 'tag.edit', params: { id: id } })
        },
        openDialog (id){
            this.dialog = true;
            this.tag_id = id;
        },
        destroyReg () {
            this.dialog = false;

            axios.post('/mto/tags/'+this.tag_id,{_method: 'delete'})
                .then(response => {

                if (response.status == 200){
                    this.$toast.success('Tag eliminada!');
                    this.tags = response.data;
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
