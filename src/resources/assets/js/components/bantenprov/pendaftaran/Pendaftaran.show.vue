<template>
  <div class="card">
    <div class="card-header">
      <i class="fa fa-table" aria-hidden="true"></i> Show pendaftaran 

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

        <div class="form-row mt-4">
          <div class="col-md">
            <b>Tanggal Pendaftaran :</b> {{ model.tanggal_pendaftaran }}
          </div>
        </div>

        <div class="form-row mt-4">
					<div class="col-md">
						<b>Kegiatan :</b> {{ model.kegiatan.label }}
					</div>
				</div>

      </vue-form>
    </div>
     <div class="card-footer text-muted">
        <div class="row">
          <div class="col-md">
            <b>Username :</b> {{ model.user.name }}
          </div>
          <div class="col-md">
            <div class="col-md text-right">Dibuat : {{ model.created_at }}</div>
            <div class="col-md text-right">Diperbaiki : {{ model.updated_at }}</div>
          </div>
        </div>
      </div>
  </div>
</template>

<script>
export default {
  mounted() {
    axios.get('api/pendaftaran/' + this.$route.params.id)
      .then(response => {
        if (response.data.status == true) {
          this.model.tanggal_pendaftaran = response.data.pendaftaran.tanggal_pendaftaran;
          this.model.kegiatan = response.data.kegiatan;
          this.model.user = response.data.user;
          this.model.created_at = response.data.pendaftaran.created_at;
          this.model.updated_at = response.data.pendaftaran.updated_at;
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
      })
      .catch(function(response) {
        alert('Break');
      })
  },
  data() {
    return {
      state: {},
      model: {
        tanggal_pendaftaran: "",
        user:"",
        kegiatan: "",
        created_at: "",
        updated_at: "",
      },
      kegiatan: []
    }
  },
  methods: {
    onSubmit: function() {
      let app = this;

      if (this.state.$invalid) {
        return;
      } else {
        axios.put('api/pendaftaran/' + this.$route.params.id, {
            label: this.model.label,
            description: this.model.description,
            old_label: this.model.old_label,
            kegiatan_id: this.model.kegiatan.id
          })
          .then(response => {
            if (response.data.status == true) {
              if(response.data.message == 'success'){
                alert(response.data.message);
                app.back();
              }else{
                alert(response.data.message);
              }
            } else {
              alert(response.data.message);
            }
          })
          .catch(function(response) {
            alert('Break ' + response.data.message);
          });
      }
    },
    back() {
      window.location = '#/admin/pendaftaran';
    }
  }
}
</script>
