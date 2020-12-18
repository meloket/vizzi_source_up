<template>
  <div>
    <b-row>
      <b-colxx xxs="12">
        <breadcrumb heading="Profile" />
        <div class="separator mb-5"></div>
      </b-colxx>
    </b-row>
    <div class="container">
      <b-card :title="$t('your_info')">
        <b-row>
          <b-colxx xxs="3">
            <div class="text-right position-relative">
              <label for="upload_avatar">
                <i class="position-absolute simple-icon-pencil icon-btn"></i>
              </label>
              <img :src="'/assets/img/avatar/' + user.avatar" class="profile-avatar" v-if="!url" />
              <img :src="url" class="profile-avatar" v-if="url" />
            </div>
          </b-colxx>
          <b-colxx xxs="9">
              <form @submit.prevent="update" @keydown="form.onKeydown($event)">
                <alert-success :form="form" :message="$t('info_updated')" />

                <!-- Name -->
                <div class="form-group row">
                  <label class="col-md-3 col-form-label text-md-right">{{ $t('name') }}</label>
                  <div class="col-md-7">
                    <input v-model="form.name" :class="{ 'is-invalid': form.errors.has('name') }" class="form-control" type="text" name="name">
                    <has-error :form="form" field="name" />
                  </div>
                </div>

                <!-- Email -->
                <div class="form-group row">
                  <label class="col-md-3 col-form-label text-md-right">{{ $t('email') }}</label>
                  <div class="col-md-7">
                    <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" class="form-control" type="email" name="email">
                    <has-error :form="form" field="email" />
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-md-3 col-form-label text-md-right">{{ $t('timezone') }}</label>
                  <div class="col-md-7">
                    <input v-model="form.user_timezone_region" type="hidden" name="user_timezone_region" id="user_timezone_region">
                    <input v-model="form.user_timezone" type="hidden" name="user_timezone" id="user_timezone">
                    <!-- this holds the timezone widget -->
                    <div id="user_edit_timezone" class="tzwidget"></div>
                  </div>
                </div>
                <!-- Submit Button -->
                <div class="form-group row">
                  <div class="col-md-9 ml-md-auto">
                    <v-button :loading="form.busy" type="success">
                      {{ $t('update') }}
                    </v-button>
                  </div>
                </div>
              </form>
          </b-colxx>
        </b-row>
      </b-card>
    </div>
    <input type="file" id="upload_avatar" class="d-none" @change="onFileChange" />
  </div>

</template>

<script>
import Form from 'vform'
import { mapGetters } from 'vuex'
import Axios from 'axios'

export default {
  scrollToTop: false,
  metaInfo () {
      return { title: `Profile Setting` }
  },
  data: () => ({
    form: new Form({
      name: '',
      email: '',
      user_timezone_region: '',
      user_timezone: ''
    }),
    url: null
  }),

  computed: mapGetters({
    user: 'auth/user'
  }),

  created () {
    // Fill the form with user data.
    this.form.keys().forEach(key => {
      this.form[key] = this.user[key]
    })

    let app = this;

    $.get('/timezones', function (tzData) { 
      $("#user_edit_timezone").timezoneWidget({
        data: tzData,
        onRegionSelect: function (region) {
          app.form.user_timezone_region = region;
        },
        onTimezoneSelect: function (region, timezone, auto) {
          app.form.user_timezone_region = region;
          app.form.user_timezone = timezone;
        },
        guessUserTimezone: true
      });
    }, 'json');    
  },

  methods: {
    async update () {
      console.log(this.form)
      const res = await this.form.patch('settings/profile')
      this.$store.dispatch('auth/updateUser', { user: res.data })
      if (res.status == 200 && res.data.role > 2) {
        this.$router.push('/')
      }
    },
    onFileChange(e) {
      const file = e.target.files[0]
      const validImageTypes = ['image/gif', 'image/jpeg', 'image/png']
      if (!validImageTypes.includes(file['type'])) {
        this.$notify('primary filled', 'Warn!', 'File is not allowed!', { duration: 3000, permanent: false })
      } else {
        if (file.size / 100000 > 1) {
          this.$notify('primary filled', '', 'File is not optimized for the sytem. Please reduce reupload with a size of less than 1mb.', { duration: 3000, permanent: false })
        }
        this.url = URL.createObjectURL(file)
        let formData = new FormData()
        formData.set('file', file)
        Axios.post('settings/avatar', formData).then(res => {
          this.$store.dispatch('auth/updateUser', { user: res.data })
        })
      }
    }
  }
}
</script>

<style scoped>
  .profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 8px;
    object-fit: cover;
  }
  .icon-btn {
    padding: 6px;
    right: 5px;
    top: 5px;
    border-radius: 50%;
    box-shadow: 0 0 5px 0 rgba(0, 0, 0, .4);
    cursor: pointer;
  }
  .icon-btn:hover {
    background: white;
    color: black;
  }
</style>
