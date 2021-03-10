<template>
    <div>
        <my-dialog :dialog.sync="dialog" registro="registro" @destroyReg="destroyReg"></my-dialog>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-show="id > 0 && isAdmin"
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
    </div>
</template>
<script>
import MyDialog from '@/components/shared/MyDialog'
import {mapGetters} from 'vuex'
export default {
    props:{
        id: Number,
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
            'isAdmin',
        ]),
    },
    methods:{
        goCreate(){
            this.$router.push({ name: 'clidoc.create' })
        },
        openDialog (){
            this.dialog = true;
        },
        destroyReg () {
            this.dialog = false;

            axios.post('/mto/clidoc/'+this.id,{_method: 'delete'})
                .then(response => {

                    this.$toast.success('Documentación eliminada!');

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
