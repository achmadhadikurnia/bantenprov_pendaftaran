<template>
  <div class="card">
    <div class="card-header">
      <i class="fa fa-table" aria-hidden="true"></i> Edit pendaftaran

      <ul class="nav nav-pills card-header-pills pull-right">
        <li class="nav-item">
          <button class="btn btn-primary btn-sm" role="button" @click="back">
            <i class="fa fa-arrow-left" aria-hidden="true"></i>
          </button>
        </li>
      </ul>
    </div>

    <div class="card-body">
      <vue-form class="form-horizontal form-validation" :state="state" @submit.prevent="onSubmit">
        <div class="form-row">
          <div class="col-md">
            <validate tag="div">
               <input disabled class="form-control" type="datetime"  v-model="model.tanggal_pendaftaran" required name="tanggal_pendaftaran" >
              <field-messages name="tanggal_pendaftaran" show="$invalid && $submitted" class="text-danger">
                <small class="form-text text-success">Looks good!</small>
                <small class="form-text text-danger" slot="required">Tanggal Pendaftaran is a required field</small>
              </field-messages>
            </validate>
          </div>
        </div>

        <div class="form-row mt-4">
					<div class="col-md">
						<validate tag="div">
						<label for="kegiatan">Kegiatan</label>
						<v-select name="kegiatan" v-model="model.kegiatan" :options="kegiatan" class="mb-4"></v-select>

						<field-messages name="kegiatan" show="$invalid && $submitted" class="text-danger">
							<small class="form-text text-success">Looks good!</small>
							<small class="form-text text-danger" slot="required">Label is a required field</small>
						</field-messages>
						</validate>
					</div>
				</div>

        <div class="form-row mt-4">
          <div class="col-md">
            <validate tag="div">
            <label for="sekolah_id">Sekolah tujuan</label>
            <v-select name="sekolah_id" v-model="model.sekolah" :options="sekolah" class="mb-4"></v-select>

            <field-messages name="sekolah_id" show="$invalid && $submitted" class="text-danger">
              <small class="form-text text-success">Looks good!</small>
              <small class="form-text text-danger" slot="required">Label is a required field</small>
            </field-messages>
            </validate>
          </div>
        </div>

        <div class="form-row mt-4">
					<div class="col-md">
						<validate tag="div">
						<label for="user_id">Username</label>
						<v-select name="user_id" v-model="model.user" :options="user" class="mb-4"></v-select>

						<field-messages name="user_id" show="$invalid && $submitted" class="text-danger">
							<small class="form-text text-success">Looks good!</small>
							<small class="form-text text-danger" slot="required">username is a required field</small>
						</field-messages>
						</validate>
					</div>
				</div>

        <div class="form-row mt-4">
          <div class="col-md">
            <button type="submit" class="btn btn-primary">Submit</button>

            <button type="reset" class="btn btn-secondary" @click="reset">Reset</button>
          </div>
        </div>

      </vue-form>
    </div>
  </div>
</template>

<script>
import swal from 'sweetalert2'
import VueMoment from 'vue-moment'
import moment from 'moment-timezone'

Vue.use(VueMoment, {
    moment,
})

var tanggal={}
tanggal.mydate = moment(new Date()).format("YYYY-MM-DD k:mm:ss ");
export default {
  mounted() {
    axios.get('api/pendaftaran/' + this.$route.params.id + '/edit')
      .then(response => {
        if (response.data.status == true && response.data.error == false) {
          this.model.user                 = response.data.user,
          this.model.old_label            = response.data.pendaftaran.label;
          this.model.old_user_id          = response.data.pendaftaran.user_id;
          this.model.tanggal_pendaftaran  = response.data.pendaftaran.tanggal_pendaftaran;
          this.model.sekolah              = response.data.sekolah;
          this.model.kegiatan             = response.data.kegiatan;
       
        } else {
          alert('Failed');
        }
      })
      .catch(function(response) {
        alert('Break');
        window.location.href = '#/admin/pendaftaran';
      }),

      axios.get('api/pendaftaran/create')
      .then(response => {
          response.data.kegiatan.forEach(element => {
            this.kegiatan.push(element);
          });
          response.data.sekolah.forEach(element => {
            this.sekolah.push(element);
          });
          if(response.data.user_special == true){
            response.data.user.forEach(user_element => {
              this.user.push(user_element);
            });
          }else{
            this.user.push(response.data.user);
              swal(
              'Failed',
              'Oops... '+response.data.message,
              'error'
            );

            app.back();
          }
      })
      .catch(function(response) {
        swal(
          'Not Found',
          'Oops... Your page is not found.',
          'error'
        );

        app.back();
      });
      
  },
  data() {
    return {
      state: {},
      model: {
        tanggal_pendaftaran : tanggal.mydate,
        user                : "",
        kegiatan            : "",
        old_label           : "",
        old_user_id         : "",
        sekolah             : "",
      },
      kegiatan    : [],
      user        : [],
      sekolah     : [],
    }
  },
  methods: {
    onSubmit: function() {
      let app = this;

      if (this.state.$invalid) {
        return;
      } else {
        axios.put('api/pendaftaran/' + this.$route.params.id, {

            tanggal_pendaftaran : this.model.tanggal_pendaftaran,
            old_label           : this.model.old_label,
            old_user_id         : this.model.old_user_id,
            kegiatan_id         : this.model.kegiatan.id,
            user_id             : this.model.user.id,
            sekolah_id          : this.model.sekolah.id,
          })
          .then(response => {
            if (response.data.status == true) {
              if(response.data.error == false){
                swal(
                  'Updated',
                  'Yeah!!! Your data has been updated.',
                  'success'
                );

                app.back();
              }else{
                swal(
                  'Failed',
                  'Oops... '+response.data.message,
                  'error'
                );
              }
            } else {
              swal(
                'Failed',
                'Oops... '+response.data.message,
                'error'
              );

              app.back();
            }
          })
          .catch(function(response) {
            swal(
              'Not Found',
              'Oops... Your page is not found.',
              'error'
            );

            app.back();
          });
      }
    },
    reset() {
      axios.get('api/pendaftaran/' + this.$route.params.id + '/edit')
        .then(response => {
          if (response.data.status == true) {
            this.model.tanggal_pendaftaran = response.data.pendaftaran.tanggal_pendaftaran;
          } else {
            alert('Failed');
          }
        })
        .catch(function(response) {
          alert('Break ');
        });
    },
    back() {
      window.location = '#/admin/pendaftaran';
    }
  }
}
</script>
