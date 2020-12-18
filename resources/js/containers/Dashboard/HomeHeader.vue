<template>
    <b-card no-body>
        <div class="d-flex justify-content-end align-items-center p-3">
            <h6 class="mr-auto my-auto">Begins 2:PM ({{timezone}}) Sunday - 12 July 2020</h6>
            <b-button variant="light default" class="mr-2">
                View Registration Page
            </b-button>
            <b-input-group class="home-header">
                <b-form-input ref="copyPart" class="form-control copy" v-model="host" disabled="" />
                <b-input-group-append>
                    <b-button variant="light default" @click="copy">Copy</b-button>
                </b-input-group-append>
            </b-input-group>
        </div>
    </b-card>
</template>

<script>
export default {
    props: [
        'timezone'
    ],
    computed: {
        host() {
            return 'https://' + window.location.host.split('.')[0] + '.vizzi.live'
        }
    },
    methods: {
        copy() {
            navigator.clipboard.writeText(this.host);
        }
    }
}
</script>

<style lang="css">
    .copy {
        font-size: 16px;
        text-align: center;
    }
    .home-header.input-group {
        width: 320px;
    }
</style>