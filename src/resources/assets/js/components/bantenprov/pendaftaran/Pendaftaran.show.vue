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
        <dl class="row">

            <dt class="col-4">Nama Siswa</dt>
            <dd class="col-8">{{ model.nama_siswa }}</dd>

            <dt class="col-4">Jenis Pendaftaran</dt>
            <dd class="col-8">{{ model.jenis_pendaftaran }}</dd>

            <dt class="col-4">Sekolah Tujuan</dt>
            <dd class="col-8">{{ model.sekolah_tujuan }}</dd>

            <dt class="col-4">Tanggal Pendaftaran</dt>
            <dd class="col-8">{{ model.tanggal_pendaftaran }}</dd>

            <dt class="col-4">Jenis SKTM</dt>
            <dd class="col-8">{{ model.jenis_sktm }}</dd>

            <dt class="col-4">Nomor SKTM</dt>
            <dd class="col-8">{{ model.no_sktm }}</dd>

            <dt class="col-4">Prestasi</dt>
            <dd class="col-8">{{ model.nama_lomba }}</dd>

            <dt class="col-4">Jenis Prestasi</dt>
            <dd class="col-8">{{ model.jenis_prestasi }}</dd>

            <dt class="col-4">Juara</dt>
            <dd class="col-8">{{ model.juara }}</dd>

            <dt class="col-4">Tingkat</dt>
            <dd class="col-8">{{ model.tingkat }}</dd>

        </dl>
        <workflow-process content-type="Pendaftaran"></workflow-process>
      </vue-form>
    </div>
     <div class="card-footer text-muted">
        <div class="row">
          <div class="col-md">
            <b>Username :</b> {{ model.username }}
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
          this.model.tanggal_pendaftaran    = response.data.tanggal_pendaftaran;
          this.model.nama_siswa             = response.data.nama_siswa;
          this.model.jenis_pendaftaran      = response.data.jenis_pendaftaran;
          this.model.sekolah_tujuan         = response.data.sekolah_tujuan;
          this.model.jenis_sktm             = response.data.jenis_sktm;
          this.model.no_sktm                = response.data.no_sktm;
          this.model.nama_lomba             = response.data.nama_lomba;
          this.model.jenis_prestasi         = response.data.jenis_prestasi;
          this.model.juara                  = response.data.juara;
          this.model.tingkat                = response.data.tingkat;
          this.model.created_at             = response.data.created_at;
          this.model.updated_at             = response.data.updated_at;
      })
      .catch(function(response) {
        // alert('Break');
        // window.location.href = '#/admin/pendaftaran';
      })
  },
  data() {
    return {
      state: {},
      model: {
        nama_siswa          : "",
        jenis_pendaftaran   : "",
        sekolah_tujuan      : "",
        tanggal_pendaftaran : "",
        jenis_sktm          : "",
        no_sktm             : "",
        username            : "",
        nama_lomba          : "",
        jenis_prestasi      : "",
        juara               : "",
        tingkat             : "",
        created_at          : "",
        updated_at          : ""
      },
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
            // alert('Break ' + response.data.message);
          });
      }
    },
    back() {
      window.location = '#/admin/pendaftaran';
    }
  }
}
</script>
