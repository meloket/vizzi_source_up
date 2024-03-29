<template>
    <div>
        <b-row>
            <b-colxx xxs="12">
                <breadcrumb :heading="title" />
                <div class="top-right-button-container d-flex" v-if="user && user.role < 4">
                    <a href="/assets/sample/sample-exhibit.xlsx" download class="btn btn-outline-primary mr-2">Sample file Download</a>
                    <label for="file" class="btn btn-primary mb-0">Upload</label>
                </div>
                <div class="separator mb-5"></div>
            </b-colxx>
        </b-row>
        <b-card>
            <b-row>
                <b-colxx xxs="12">
                    <div v-html="note" class="p-2 text-center" style="font-size: 16px" v-if="note && note != ''" />
                    <h2 class="mt-4 text-center font-bold">Add Sponsor Information</h2>
                    <div class="separator"></div>
                </b-colxx>
            </b-row>
            <div class="w-60 mx-auto">
                <b-row>
                    <b-colxx lg="6" sm="12">
                        <h6 class="text-muted mt-3">Sponsor Name</h6>
                        <b-form-input v-model="booth" v-if=" user && user.role < 4" />
                        <h6 class="text-muted mt-3">Sponsor Contact Name</h6>
                        <b-form-input v-model="contact.name" />
                        <h6 class="text-muted mt-3">Sponsor Email</h6>
                        <b-form-input v-model="contact.email" />
                    </b-colxx>
                    <b-colxx lg="6" sm="12">
                        <h6 class="text-muted mt-3">Sponsor Description</h6>
                        <b-form-textarea style="height: 115px" />
                        <h6 class="text-muted mt-3">Upload Sponsor Thumbnail</h6>
                        <b-input-group>
                            <b-input-group-append>
                                <b-input-group-text>Upload</b-input-group-text>
                            </b-input-group-append>
                            <b-form-input />
                        </b-input-group>
                    </b-colxx>
                </b-row>
                <h6 class="font-bold text-center mt-5">Add Team Members <small>(Optional)</small></h6>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="ml-auto mr-1 mt-3">
                        <b-form-group label="Name" class="has-float-label">
                            <b-form-input v-model="member.name" />
                        </b-form-group>
                    </div>
                    <div class="mx-1 mt-3">
                        <b-form-group label="Email" class="has-float-label">
                            <b-form-input v-model="member.email" />
                        </b-form-group>
                    </div>
                    <b-button variant="primary" class="mr-auto ml-1" @click="addMember" v-if="buttonText == 'Add to Sponsor'">{{buttonText}}</b-button>
                    <template v-else>
                        <b-button variant="primary" class="mx-1" @click="addMember">{{buttonText}}</b-button>
                        <b-button variant="outline-primary" class="mr-auto ml-1" @click="cancelMember">Cancel</b-button>
                    </template>
                </div>
                <h4 class="mt-5 font-bold text-center">Select Your Sponsor Builder Options</h4>
                <div class="d-flex justify-content-between align-items-center">
                    <b-button variant="primary" class="ml-auto mr-1" @click="getModel()">Sponsor Wizard</b-button>
                    <b-button variant="primary" class="mx-1" @click="request()">Request Help</b-button>
                    <b-button variant="primary" class="mr-auto ml-1" @click="upload">Upload Custom Design</b-button>
                </div>
                <div class="table-responsive mt-4">
                    <b-table
                        hover
                        :items="members"
                        :fields="fields"
                        v-if="members.length"
                    >
                        <template v-slot:cell(#)="data">
                            <div class="mt-2">{{data.index + 1}}</div>
                        </template>
                        <template v-slot:cell(name)="data">
                            <div class="mt-2">{{data.item.name}}</div>
                        </template>
                        <template v-slot:cell(email)="data">
                            <div class="mt-2">{{data.item.email}}</div>
                        </template>
                        <template v-slot:cell(status)="data">
                            <b-badge class="mt-2" pill :variant="'primary'" v-if="data.item.status">Active</b-badge>
                            <b-badge class="mt-2" pill :variant="'danger'" v-else>Inactive</b-badge>
                        </template>
                        <template v-slot:cell(action)="data">
                            <b-dropdown variant="primary" right text="Action">
                                <b-dropdown-item @click="editMember(data.item)">Edit</b-dropdown-item>
                                <b-dropdown-item @click="delMember(data)">Delete</b-dropdown-item>
                            </b-dropdown>
                        </template>
                    </b-table>
                </div>
            </div>
        </b-card>
        <b-modal id="guide_modal" ref="guide_modal" centered hide-footer hide-header>
            <h2 class="font-bold mb-4">{{text.title}}</h2>
            <h4 class="my-4 font-medium">{{text.content}}</h4>
            <button class="btn start-btn" @click="guideConfirm()">Create your Sponsor design</button>
        </b-modal>
        <b-modal id="confirm_modal" ref="confirm_modal" title="Are you sure?">
            If you click confirm, you can't recover this data anymore!
            <template slot="modal-footer">
                <b-button variant="primary" @click="confirm()" class="mr-1">Confirm</b-button>
                <b-button variant="secondary" @click="hideModal('confirm_modal')">Cancel</b-button>
            </template>
        </b-modal>
        <b-modal id="user_modal" ref="user_modal" centered title="Please Confirm the User Data">
            <b-table :fields="modalFields" :items="userData">
                <template v-slot:cell(#)="data">{{data.index + 1}}</template>
            </b-table>
            <template slot="modal-footer">
                <b-button variant="secondary" @click="hideModal('user_modal')" class="mr-1">Cancel</b-button>
                <b-button variant="primary" @click="confirmMember()" class="ml-1">Confirm</b-button>
            </template>
        </b-modal>
        <input type="file" @change="onFileChange" id="file" class="d-none" />
    </div>
</template>

<script>
import exhibitMixins from '../exhibitMixins.js'
import Axios from 'axios'

export default {
    metaInfo () {
        return { title: `Exhibit Sponsor Wizard` }
    },
    data() {
        return {
            title: 'Exhibit Sponsor Wizard',
            buttonText: 'Add to Sponsor',
            storageName: 'isSponsorGuide',
            modalText: 'Sponsor',
            urlText: 'sponsor',
            type: 1,
            tabIndex: 0
        }
    },
    mixins: [exhibitMixins],
    mounted() {
        this.mainInit()
    },
    methods: {
        onFileChange(e) {
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length) return;
            this.createInput(files[0]);
        },
        createInput(file) {
            var reader = new FileReader();
            reader.onload = (e) => {
                var data = new Uint8Array(e.target.result);
                var workbook = XLSX.read(data, {type: 'array'});
                let sheetName = workbook.SheetNames[0]
                let worksheet = workbook.Sheets[sheetName]
                this.submit(worksheet);
            }
            reader.readAsArrayBuffer(file);
        },
        submit(file) {
            var no = 2
            this.userData = []
            while(file['B' + no] && file['C' + no])  {
                this.userData.push({
                    name: file['B' + no].v,
                    email: file['C' + no].v
                })
                no++
            }
            this.$refs['user_modal'].show()
        }
    }
}
</script>