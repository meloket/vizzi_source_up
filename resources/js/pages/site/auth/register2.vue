<template>
  <b-row class="vh-100">
    <b-colxx xxs="12" md=10  class="mx-auto my-auto">
      <b-card class="auth-card" no-body v-if="isShow">
        <div class="form-side text-center w-100" v-if="fields.length">
          <div class="d-inline-flex pb-5">
            <img :src="'/data/' + domain.logo" class="logo" />
            <div class="my-auto ml-4">
              <h2 style="letter-spacing: 8px; font-weight: bold; text-transform: capitalize" v-if="domain.title">{{domain.title}}</h2>
              <h5>Creative Studios</h5>
            </div>
          </div>
          <b-form @submit.prevent="formSubmit">
            <b-row>
              <template v-for="(field, index) in fields">
                <b-colxx md="6" sm="12" :key="field.id">
                  <b-form-group :label="getLabel(field)" class="has-float-label mb-4">
                    <b-form-input type="text" v-model="form[field.label]" v-if="field.label != 'password'" />
                    <template v-else>
                      <b-form-input type="password" v-model="form.password"/>
                      <b-form-invalid-feedback v-if="!match" style="display:block">Password doesnt match</b-form-invalid-feedback>
                    </template>
                  </b-form-group>
                </b-colxx>
                <b-colxx md="6" sm="12" v-if="field.label == 'password'" :key="index">
                  <label class="form-group has-float-label mb-4">
                    <input type="password" class="form-control" v-model="form.password_confirmation" @keyup="match = true"/>
                    <span>Confirm Password*</span>
                  </label>
                </b-colxx>
              </template>
            </b-row>
            <div class="d-flex justify-content-end align-items-center">
                <b-button type="submit" variant="primary" size="lg" class="btn-shadow">Register</b-button>
            </div>
          </b-form>
        </div>
        <template v-else>
          <div class="position-relative image-side ">
            <p class=" text-white h2">Welcome to VCS Portal</p>
            <p class="white mb-0">  Please use this form to register. <br />If you are a member, please
              <router-link tag="a" to="/login" class="white">login</router-link>.
            </p>
          </div>
          <div class="form-side text-center">
            <div class="d-inline-flex pb-5">
                <router-link tag="a" to="/">
                    <span class="logo-single" />
                </router-link>
                <div class="my-auto ml-4">
                    <h2 style="letter-spacing: 8px; font-weight: bold">VIRTUAL</h2>
                    <h5>Creative Studios</h5>
                </div>
            </div>
            <b-form @submit.prevent="formSubmit">
              <b-form-group label="Full Name" class="has-float-label mb-4">
                <b-form-input type="text" v-model="form.name"/>
              </b-form-group>
              <b-form-group label="Email" class="has-float-label mb-4">
                <b-form-input type="text" v-model="$v.form.email.$model" :state="!$v.form.email.$error"/>
                <b-form-invalid-feedback v-if="!$v.form.email.required">Please enter your email address</b-form-invalid-feedback>
                <b-form-invalid-feedback v-else-if="!$v.form.email.email">Please enter a valid email address</b-form-invalid-feedback>
                <b-form-invalid-feedback v-else-if="!$v.form.email.minLength">Your email must be minimum 4 characters</b-form-invalid-feedback>
              </b-form-group>
              <b-form-group label="Password" class="has-float-label mb-4">
                <b-form-input type="password" v-model="form.password" @keyup="match = true"/>
                <b-form-invalid-feedback v-if="!match" style="display:block">Password doesnt match</b-form-invalid-feedback>
              </b-form-group>
              <label class="form-group has-float-label mb-4">
                <input type="password" class="form-control" v-model="form.password_confirmation" @keyup="match = true"/>
                <span>Confirm Password</span>
              </label>
              <div class="d-flex justify-content-end align-items-center">
                  <b-button type="submit" variant="primary" size="lg" class="btn-shadow">Register</b-button>
              </div>
            </b-form>
          </div>
        </template>
      </b-card>
    </b-colxx>
  </b-row>
</template>
<script>
import Form from 'vform'
import Axios from 'axios'
import { mapGetters } from "vuex"
import timezoneList from '../../../constants/timezone'

import {
    validationMixin
} from "vuelidate";

const {
    required,
    maxLength,
    minLength,
    email
} = require("vuelidate/lib/validators");

export default {
  layout: 'auth',
  middleware: 'guest',
  metaInfo () {
      return { title: `Register` }
  },
  data: () => ({
    form: null,
    match: true,
    mustVerifyEmail: false,
    isShow: false,
    fields: [],
    timezoneList
  }),
  mixins: [validationMixin],
  validations: {
    form: {
        password: {
          required,
          maxLength: maxLength(16),
          minLength: minLength(6)
        },
        email: {
          required,
          email,
          minLength: minLength(4)
        }
    },
  },
  computed: {
    host() {
      return window.location.host.split('.')[0]
    }
  },
  mounted() {
    Axios.post('site/host', {domain: this.host}).then(res => {
      this.domain = res.data.domain
      this.id = this.domain.id
      this.fields = res.data.fields
      if (this.fields.length) {
        var formData = {
          id: '',
          password_confirmation: ''
        }
        this.fields.forEach(data => {
          formData[data.label] = ''
        })
        this.form = formData
      } else {
        this.form = new Form({
          name: '',
          email: '',
          password: '',
          password_confirmation: '',
          id: 0
        })
      }
      this.isShow = true
    })
  },
  methods: {
    getLabel(setting) {
      var label = setting.label
      if (setting.required) {
          label += '*'
      }
      return label.charAt(0).toUpperCase() + label.slice(1)
    },
    async formSubmit () {
      if (this.form.password_confirmation ===  this.form.password) {
          this.form.id = this.id
          if (this.fields.length) {
            var flag = false
            this.fields.forEach(field => {
              if (field.required && this.form[field.label] == '') {
                flag = true
              }
            });
            if (flag) {
              this.$notify('primary filled', '', 'Please Fill the Forms!', { duration: 3000, permanent: false })
            } else {
              const res = await Axios.post('/site-register', this.form)
              this.remember = true
              this.$store.dispatch('auth/saveToken', {
                token: res.data.user.token,
                remember: this.remember
              })
              await this.$store.dispatch('site/saveSite', res.data.site)
              await this.$store.dispatch('auth/setUser', res.data.user)
              this.$router.push('/')
            }
          } else {
            const { data } = await this.form.post('/register')
            if (data.status) {
              this.mustVerifyEmail = true
            } else {
              const { data } = await this.form.post('/login')

              this.$store.dispatch('auth/saveToken', {
                  token: data.token,
                  remember: this.remember
              })

              await this.$store.dispatch('auth/fetchUser')

              var diff = 0
              if (this.user.user_timezone) {
                  this.timezoneList.forEach(listItem => {
                      listItem.utc.forEach(item => {
                          if (item == this.user.user_timezone) {
                              diff = 1 - listItem.offset
                          }
                      })
                  })
              }
                
              this.$store.dispatch('auth/setZone', {
                  diffZone: diff
              })
              this.$router.push('/')
            }
          }
      } else {
        this.match = false
      }
    }
  },
  computed: {
      ...mapGetters({
          siteId: 'site/getSiteId',
          user: "auth/user"
      }),
      host() {
        return window.location.host.split('.')[0]
      }
  }
}
</script>

<style lang="css">
  input[type="file"] {
    width: .1px;
    height: .1px;
    opacity: 0;
    position: absolute;
  }
  .logo {
    width: 110px;
    height: 100px;
    background-position: center center;
    display: inline-block;
    background-size: cover;
    margin-bottom: 0;
  }
  .col-form-label {
    text-transform: capitalize;
  }
</style>
