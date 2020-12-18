<template>
  <b-modal
      id="user_modal"
      ref="user_modal"
      :title="modalTitle"
      centered
  >
    <b-form>
      <b-form-group label="User Name">
        <b-form-input v-model="newItem.name" />
      </b-form-group>
      <b-form-group label="User Email">
        <b-form-input type="email" v-model="newItem.email" />
      </b-form-group>
      <b-form-group label="Role" v-if="role == 0">
        <v-select v-model="userRole" :options="userList"></v-select>
      </b-form-group>
      <b-form-group :label="$t('verify-code')">
          <b-input-group>
            <b-input-group-prepend @click="generate()">
              <b-input-group-text>Generate</b-input-group-text>
            </b-input-group-prepend>
            <b-form-input v-model="newItem.code" />
          </b-input-group>
      </b-form-group>
      <b-form-group label="User Profile">
        <b-textarea v-model="newItem.bio" />
      </b-form-group>
    </b-form>

    <template slot="modal-footer">
      <b-button
        variant="outline-secondary"
        @click="hideModal('user_modal')"
      >Cancel</b-button>
      <b-button variant="primary" @click="addNewItem(newItem, userRole)" class="mr-1">{{submitText}}</b-button>
    </template>
  </b-modal>
</template>
<script>
import vSelect from "vue-select"
import "vue-select/dist/vue-select.css"

export default {
  props: ['modalTitle', 'submitText', 'addNewItem', 'isOpen', 'newItem', 'role'],
  components: {
    vSelect
  },
  data() {
    return {
      userList: [
        {value: 2, label: 'Admin', type: null},
        {value: 3, label: 'Booth Manager', type: 0},
        {value: 3, label: 'Sponsor Manager', type: 1},
        {value: 3, label: 'Poster Manager', type: 2},
        {value: 5, label: 'Front User', type: null},
        {value: 6, label: 'Event Stuff', type: null}
      ],
      userRole: null
    }
  },
  methods: {
    hideModal() {
      this.$refs['user_modal'].hide()
      this.$emit('init-item')
    },
    generate() {
      this.newItem.code = Math.round(Math.random() * 1000000)
    }
  },
  watch: {
    isOpen(val) {
      if (val == true) {
        this.hideModal()
        this.$emit('modal-closed')
      }
    },
    newItem(user) {
      if (user.role == 2) {
        this.userRole = this.userList[0]
      } else if (user.role == 3) {
        if (this.type == 0) {
          this.userRole = this.userList[1]
        } else if (this.type == 1) {
          this.userRole = this.userList[2]
        } else {
          this.userRole = this.userList[3]
        }
      } else if (user.role == 5) {
        this.userRole = this.userList[4]
      } else if (user.role == 6) {
        this.userRole = this.userList[5]
      }
    }
  }
};
</script>