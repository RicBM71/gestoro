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
                    v-show="id > 0 && isRoot"
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
            <span>Existencias</span>
        </v-tooltip>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-on="on"
                    color="white"
                    icon
                    @click="goBack()"
                >
                    <v-icon color="primary">arrow_back</v-icon>
                </v-btn>
            </template>
                <span>Volver</span>
        </v-tooltip>

    </div>
</template>
<script>
import MyDialog from '@/components/shared/MyDialog'
import {mapGetters} from 'vuex'
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
    computed: {
        ...mapGetters([
            'isRoot'
        ]),
    },
    methods:{
        goBack(){
            this.$router.go(-1);
        },
        goCreate(){
            this.$router.push({ name: 'existencia.create' })
        },
        goIndex(){
            this.$router.push({ name: 'existencia.index' })
        },
        openDialog (){
            this.dialog = true;
        },
        destroyReg () {
            this.dialog = false;

            axios.post('/mto/existencias/'+this.id,{_method: 'delete'})
                .then(response => {

                    this.$router.push({ name: 'existencia.index' })
                    this.$toast.success('Registro eliminado!');
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
