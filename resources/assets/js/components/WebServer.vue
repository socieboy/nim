<template>
    <div class="form-group">
        <div class="form-group">
            <label for="port">Port</label>
            <input type="text" v-model="port" name="port" id="port" class="form-control">
        </div>

        <button class="btn btn-primary pull-right" type="button" :disabled="canSave || submitted" @click="save()">
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

            }
        },

        computed:{
            canSave(){
                return true;
            }
        }
    }
</script>
