<template>
    <div class="form-group">

        <div class="form-group">
            <label for="port">Port</label>
            <input type="number" v-model.number="port" name="port" id="port" class="form-control col-md-6">
        </div>

        <button class="btn btn-primary pull-right" type="button" :disabled="!canSave || submitted" @click="save()">
            <i class="fa fa-spin fa-spinner" v-if="submitted"></i> <span v-text="submitted ? 'Saving...' : 'Save'"></span>
        </button>

    </div>
</template>
<script>
    export default{

        data(){
            return{
                submitted: false,
                port: 80,
            }
        },

        methods:{
            save(){
                axios.post('/api/webserver', {port: this.port}).then(response => {
                    console.log(response)
                }).catch(({response}) => {
                    console.log(response)
                })
            }
        },

        computed:{
            canSave(){
                return typeof this.port == 'number';
            }
        }
    }
</script>
