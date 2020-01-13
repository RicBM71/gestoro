<template>
    <div>
        <my-dialog :dialog.sync="dialog" registro="registro" @destroyReg="destroyReg"></my-dialog>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-on="on"
                    color="white"
                    icon
                    @click="goCreate"
                >
                    <v-icon color="primary">add</v-icon>
                </v-btn>
            </template>
                <span>Nuevo</span>
        </v-tooltip>
         <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-show="id > 0"
                    v-on="on"
                    color="white"
                    icon
                    @click="openDialog"
                >
                    <v-icon color="primary">delete</v-icon>
                </v-btn>
            </template>
                <span>Borrar Registro</span>
        </v-tooltip>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-on="on"
                    color="white"
                    icon
                    @click="goIndex"
                >
                    <v-icon color="primary">list</v-icon>
                </v-btn>
            </template>
            <span>Lista</span>
        </v-tooltip>
    </div>
</template>
<script>
import MyDialog from '@/components/shared/MyDialog'
export default {
    props:{
        id: Number
    },
    components: {
        'my-dialog': MyDialog
    },
    data () {
      return {
          dialog: false,
      }
    },
    methods:{
        goCreate(){
            this.$router.push({ name: 'almacen.create' })
        },
        goIndex(){
            this.$router.push({ name: 'almacen.index' })
        },
        openDialog (){
            this.dialog = true;
        },
        destroyReg () {
            this.dialog = false;

            axios.post('/mto/almacenes/'+this.id,{_method: 'delete'})
                .then(response => {
                    this.$router.push({ name: 'almacen.index' })
                    this.$toast.success('Ubicación eliminada!');

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
