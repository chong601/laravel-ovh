<template>
    <div>
        <button class="btn btn-primary" v-on:click="rebootServer" v-bind:disabled="disableButton" v-text="buttonText"></button>
    </div>
</template>

<script>
import axios from "axios";
export default {
    props: {
        serviceName: {type: String, required: true}
    },
    data() {
        return {
            disableButton: false,
            buttonText: 'Reboot'
        }
    },
    methods: {
        rebootServer() {
            let finalRebootUrl = '/api/dedicated/server/' + this.serviceName + '/reboot'
            this.buttonText = 'Rebooting...'
            this.disableButton = true
            axios.post(finalRebootUrl).then(response => {
                console.log(response.data)
            }).catch(error => {
                console.log(error)
            }).finally(() => {
                setTimeout(() => {
                    this.disableButton = false
                    this.buttonText = 'Reboot'
                }, 5000)
            })
        }
    }

}
</script>

<style>

</style>
