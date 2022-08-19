<template>
    <div>
        <div class="card-header">IP Addresses</div>
        <div class="card-body">
            <ul>
                <li v-for="(value, name) in ip" v-bind:key="value.ip">
                    {{ name }}: <span class="badge text-bg-primary">{{ value.type }}</span> <span>{{ value.country}}</span>
                </li>
            </ul>
        </div>
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
            ip: null
        }
    },
    mounted() {
        let finalRoute = '/api/dedicated/server/' + this.serviceName + '/ip/detail'
        axios.get(finalRoute).then(response => {
            this.ip = response.data
        }).catch(function (error) {
            console.log(error.response.data.message)
        })
    },
    methods: {
        upperCaseFirstCharacter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        },
        upperCaseEverything(string) {
            return string.toUpperCase()
        }
    }
}
</script>

<style>

</style>
