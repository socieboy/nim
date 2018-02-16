<template>
    <div class="form-group">

        <div class="form-group">
            <label for="name">Computer Name</label>
            <input type="text" v-model="webserver.name" name="name" id="name" class="form-control">
        </div>

        <div class="form-group">
            <label for="port">Port</label>
            <input type="number" v-model.number="webserver.port" name="port" id="port" class="form-control">
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
                webserver:{
                    name: '',
                    port: 80,
                }
            }
        },

        created(){
            axios.get('/api/webserver').then(response => {
                this.webserver = response.data;
            });
        },

        methods:{
            save(){
                axios.post('/api/webserver', this.webserver).then(response => {
                    console.log(response)
                }).catch(({response}) => {
                    console.log(response)
                })
            }
        },

        computed:{
            canSave(){
                return typeof this.webserver.port == 'number' && this.webserver.name.trim() != '';
            }
        }
    }
</script>
