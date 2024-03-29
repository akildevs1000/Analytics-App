<template>
  <div v-if="can('setting_company_access')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <div v-if="!preloader">
      <v-card elevation="0" class="pa-3 mb-5">
        <v-card-title>
          <label class="col-form-label pt-0 mt-5"
            ><b>Create Payroll Formula</b></label
          >
          <!-- <v-spacer></v-spacer>
          <v-btn small fab color="background" dark to="/report_notifications">
            <v-icon>mdi-arrow-left</v-icon>
          </v-btn> -->
        </v-card-title>
        <v-container>
          <v-row>
            <v-col cols="2">
              <label class="col-form-label pt-0 mt-5"
                ><b>Salary calculation formula</b></label
              >
            </v-col>
            <v-col cols="10">
              <div style="display: inline-flex">
                <v-radio-group v-model="payload.salary_type" row>
                  <v-radio label="Basic Salary" value="basic_salary"></v-radio>
                  <v-radio label="Net Salary" value="net_salary"></v-radio>
                </v-radio-group>
              </div>
              <span v-if="errors && errors.salary_type" class="text-danger">{{
                errors.salary_type[0]
              }}</span>
            </v-col>

            <v-col cols="2">
              <label class="col-form-label"><b>OT formula</b></label>
            </v-col>
            <v-col cols="10">
              <div style="display: inline-flex">
                <input
                  class="form-control"
                  type="text"
                  outlined
                  dense
                  value="Per Hour Salary"
                  readonly
                />
                <span class="pa-2">x</span>
                <input
                  v-model="payload.ot_value"
                  class="form-control"
                  type="text"
                  outlined
                  dense
                />
              </div>
              <span v-if="errors && errors.ot_value" class="text-danger">{{
                errors.ot_value[0]
              }}</span>
            </v-col>

            <v-col cols="2">
              <label class="col-form-label"
                ><b>Late Deduction formula</b></label
              >
            </v-col>
            <v-col cols="8">
              <div style="display: inline-flex">
                <input
                  class="form-control"
                  type="text"
                  outlined
                  dense
                  value="Per Hour Salary"
                  readonly
                />
                <span class="pa-2">x</span>
                <input
                  v-model="payload.deduction_value"
                  class="form-control"
                  type="text"
                  outlined
                  dense
                />
              </div>
              <span
                v-if="errors && errors.deduction_value"
                class="text-danger"
                d
                >{{ errors.deduction_value[0] }}</span
              >
            </v-col>
            <v-col cols="12">
              <v-btn small color="primary" @click="store"> Submit </v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-card>

      <v-card elevation="0" class="pa-3">
        <v-card-title>
          <label class="col-form-label pt-0 mt-5"
            ><b>Create Payroll Generation Date</b></label
          >
          <!-- <v-spacer></v-spacer>
          <v-btn small fab color="background" dark to="/report_notifications">
            <v-icon>mdi-arrow-left</v-icon>
          </v-btn> -->
        </v-card-title>
        <v-container>
          <v-row>
            <v-col cols="2">
              <label class="col-form-label"
                ><b>Set date for payroll generation</b></label
              >
            </v-col>
            <v-col cols="2">
              <v-autocomplete
                dense
                outlined
                v-model="date"
                :items="dayOptions"
                item-value="value"
                item-text="label"
              ></v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-btn small color="primary" @click="storeDate"> Submit </v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-card>
    </div>
    <Preloader v-else />
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  data: () => ({
    payload: {
      salary_type: "basic_salary",
      ot_value: 1.5,
      deduction_value: 1.5,
    },
    menu: false,
    date: 1,
    preloader: false,
    loading: false,
    response: false,
    snackbar: false,
    dayOptions: Array.from({ length: 31 }, (_, i) => ({
      value: i + 1,
      label: i + 1,
    })),
    errors: [],
  }),

  created() {
    this.payload.company_id = this.$auth?.user?.company?.id;

    this.preloader = false;

    this.$axios
      .get(`/payroll_formula/${this.payload.company_id}`)
      .then(({ data }) => {
        if (data) {
          this.payload = data;
        }
      })
      .catch((e) => console.log(e));

    // this.date = this.getDate();

    this.$axios
      .get(`/payroll_generate_date/${this.payload.company_id}`)
      .then(({ data }) => (this.date = data))
      .catch((e) => console.log(e));
  },

  methods: {
    getDate() {
      const date = new Date();
      const year = date.getFullYear();
      const month = (date.getMonth() + 1).toString().padStart(2, "0");
      const day = date.getDate().toString().padStart(2, "0");
      return `${day}`;
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    

    store() {
      this.errors = [];
      this.$axios
        .post("/payroll_formula", this.payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
            return;
          }

          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch((e) => console.log(e));
    },
    storeDate() {
      this.errors = [];

      let payload = {
        company_id: this.$auth?.user?.company?.id,
        date: this.date,
      };

      this.$axios
        .post("/payroll_generate_date", payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
            return;
          }

          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>
