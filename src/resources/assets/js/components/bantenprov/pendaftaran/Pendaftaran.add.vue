<template>
  <div class="card">
    <div class="card-header">
      <i class="fa fa-table" aria-hidden="true"></i> Add pendaftaran

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
              <label for="model.tanggal_pendaftaran">Tanggal Pendaftaran</label>
               <input disabled class="form-control" type="datetime"  v-model="model.tanggal_pendaftaran" required name="tanggal_pendaftaran" >
            <field-messages name="tanggal_pendaftaran" show="$invalid && $submitted" class="text-danger">
              <small class="form-text text-danger" slot="required">Tanggal Pendaftaran is a required field</small>
            </field-messages>
            </validate>
          </div>
        </div>

        <div class="form-row mt-4">
          <div class="col-md">
            <validate tag="div">
            <label for="sekolah_id">Sekolah Tujuan</label>
            <v-select name="sekolah_id" v-model="model.sekolah" :options="sekolah" class="mb-4"></v-select>

            <field-messages name="sekolah_id" show="$invalid && $submitted" class="text-danger">
              <small class="form-text text-success">Looks good!</small>
              <small class="form-text text-danger" slot="required">Sekolah Tujuan is a required field</small>
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
import VueMoment from 'vue-moment'
import moment from 'moment-timezone'
import swal from 'sweetalert2';

Vue.use(VueMoment, {
    moment,
})

var tanggal={}
tanggal.mydate = moment(new Date()).format("YYYY-MM-DD k:mm:ss ");
export default {

  data(){
    return {
        state: {},
        title: 'Add Pendaftaran',
        model: {
            tanggal_pendaftaran : tanggal.mydate,
            user_id             :     "",
            kegiatan_id         :     "",
            sekolah_id          :     "",

            user                :     "",
            kegiatan            :     "",
            sekolah             :     "",
        
          },
            user      : [],
            kegiatan  : [],
            sekolah   : [],
        }
      },

    mounted(){
    axios.get('api/pendaftaran/create')
    .then(response => {
      if (response.data.status == true) {
        this.model.user = response.data.current_user;
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
  },

  methods: {
    moment: function (date) {
      return moment(date);
    },
    onSubmit: function() {
      let app = this;

      if (this.state.$invalid) {
        return;
      } else {
        axios.post('api/pendaftaran', {
            tanggal_pendaftaran       : this.model.tanggal_pendaftaran,
            kegiatan_id               : this.model.kegiatan.id,
            user_id                   : this.model.user.id,
            sekolah_id                : this.model.sekolah.id,
          })
          .then(response => {
            if (response.data.status == true) {
              if(response.data.error == false){
                swal(
                  'Created',
                  'Yeah!!! Your data has been created.',
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
      this.model = {
          tanggal_pendaftaran: ""
      };
    },
    back() {
      window.location = '#/admin/pendaftaran';
    }
  }
}
</script>
