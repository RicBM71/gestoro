<template>
    <v-dialog v-model="dialog_abono" persistent max-width="420">
        <v-card>
            <v-card-title class="headline">Confirmar Abono</v-card-title>
            <v-card-text>
                <v-flex sm12 v-if="tipo_abono=='C'">
                    Una cancelación no genera los cobros del albarán.
                </v-flex>
                <v-flex sm12 v-else>
                    Un abono, genera los cobros inversos del albarán abonado.
                </v-flex>
                <v-flex sm12>
                    <v-select
                        v-model="motivo_id"
                        v-validate="'required'"
                        :error-messages="errors.collect('motivo_id')"
                        data-vv-name="motivo_id"
                        data-vv-as="motivo"
                        :items="motivos"
                        label="Motivo"
                    ></v-select>
                </v-flex>
            </v-card-text>
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" flat round @click="closeDialog()">Cancelar</v-btn>
            <v-btn color="blue darken-1" flat :loading="loading" round @click="submit()">Aceptar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
export default {
    $_veeValidate: {
        validator: 'new'
    },
    props:['albaran','dialog_abono','tipo_abono'],
    data () {
        return {
            motivos:[],
            motivo_id:"",
            loading: false
        }
    },
    mounted(){
        axios.get("/mto/motivos")
            .then(res => {
                this.motivos = res.data.map(function(obj){
                    var rObj = {
                        value: obj.id,
                        text: obj.nombre,
                    };
                    return rObj;
                });
            })
            .catch(err => {
                this.$toast.error(err.response.data.message);
            })
    },
    methods:{
        closeDialog(){
            this.$emit('update:dialog_abono', false);
        },
        submit(){
            if (this.tipo_abono == "A")
                this.goAbonar();
            else
                this.goCancelar();
        },
        goAbonar(){
            this.$validator.validateAll().then((result) => {
                if (result){
                    this.loading = true;
                    axios.put("/ventas/abonos/"+this.albaran.id+"/abonar", {motivo_id: this.motivo_id})
                        .then(res => {
                            this.$router.push({ name: 'albaran.edit', params: { id: res.data.albaran_id } })
                        })
                        .catch(err => {
                            this.$toast.error(err.response.data.message);
                        })
                        .finally( () =>{
                            // always executed
                        });
                }
            })
        },
        goCancelar(){
            this.$validator.validateAll().then((result) => {
                if (result){
                    this.loading = true;
                    axios.put("/ventas/abonos/"+this.albaran.id+"/cancelar", {motivo_id: this.motivo_id})
                        .then(res => {
                            this.$router.push({ name: 'albaran.edit', params: { id: res.data.albaran_id } })
                        })
                        .catch(err => {
                            this.$toast.error(err.response.data.message);
                        })
                        .finally(() =>{
                            // always executed
                        });
                }
            });

        },
    }
}
</script>
