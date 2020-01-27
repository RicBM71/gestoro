<template>
    <v-dialog v-model="dialog" persistent max-width="420">
        <v-card>
            <v-card-title class="headline">Borrar {{ registro }}?</v-card-title>
            <v-card-text>¿Está seguro de borrar el {{ registro }} seleccionado?</v-card-text>
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" flat round @click="closeDialog()">Cancelar</v-btn>
            <v-btn color="blue darken-1" flat round @click="deleteItem">Aceptar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
  export default {
    props:{
            dialog: Boolean,
            registro: String
    },
    methods:{
        closeDialog (){
            this.$emit('update:dialog', false)
        },
        deleteItem () {
            this.$emit('update:dialog', false);
            this.$emit('destroyReg');
        }
      }
  }
  </script>
