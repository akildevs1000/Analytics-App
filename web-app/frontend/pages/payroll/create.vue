<template>
  <div v-if="can('payroll_settings_create')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <div v-if="!preloader">
      <!-- <Back class="primary white--text" /> -->
      <v-row>
        <v-col md="12"
          ><v-card
            elevation="0"
            class="pa-3 mt-2 mb-5"
            style="min-height: 400px"
          >
            <v-toolbar class=" " dense flat>
              <v-toolbar-title style="color: black"
                ><span> Create Payroll Formula</span></v-toolbar-title
              >
            </v-toolbar>
            <v-container>
              <v-divider></v-divider>
              <v-row v-if="isCompany">
                <v-col cols="4">
                  <label class="col-form-label"><b>Branch List</b></label>
                </v-col>
                <v-col cols="3">
                  <v-autocomplete
                    placeholder="Select Branch"
                    dense
                    outlined
                    v-model="payload.branch_id"
                    :items="branchesList"
                    item-value="id"
                    item-text="branch_name"
                  ></v-autocomplete>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="4">
                  <label class="col-form-label pt-0 mt-5"
                    ><b>Salary calculation formula</b></label
                  >
                </v-col>
                <v-col cols="6">
                  <div style="display: inline-flex">
                    <v-radio-group v-model="payload.salary_type" row>
                      <v-radio
                        label="Basic Salary"
                        value="basic_salary"
                      ></v-radio>
                      <v-radio label="Net Salary" value="net_salary"></v-radio>
                    </v-radio-group>
                  </div>
                  <span
                    v-if="errors && errors.salary_type"
                    class="text-danger"
                    >{{ errors.salary_type[0] }}</span
                  >
                </v-col>

                <v-col cols="4">
                  <label class="col-form-label"><b>OT formula</b></label>
                </v-col>
                <v-col cols="6">
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

                <v-col cols="4">
                  <label class="col-form-label"
                    ><b>Late Deduction formula</b></label
                  >
                </v-col>
                <v-col cols="6">
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

                  <v-col cols="6" class="text-right">
                    <v-btn small color="primary" @click="store"> Submit </v-btn>
                  </v-col>
                </v-col>
              </v-row>
            </v-container>
          </v-card></v-col
        >
        <v-col md="12">
          <v-card
            elevation="0"
            class="pa-3 mt-2 mb-5"
            style="min-height: 400px"
          >
            <v-toolbar class=" " dense flat>
              <v-toolbar-title style="color: black"
                ><span> Create Payroll Generation Date</span></v-toolbar-title
              >
            </v-toolbar>

            <v-container>
              <v-divider></v-divider>
              <v-row v-if="isCompany">
                <v-col cols="4">
                  <label class="col-form-label"><b>Branch List</b></label>
                </v-col>
                <v-col cols="3">
                  <v-autocomplete
                    placeholder="Select Branch"
                    dense
                    outlined
                    v-model="branch_id"
                    :items="branchesList"
                    item-value="id"
                    item-text="branch_name"
                  ></v-autocomplete>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="4">
                  <label class="col-form-label"
                    ><b>Salary Payslip Generation Date (Every Month)</b></label
                  >
                </v-col>
                <v-col cols="3">
                  <v-autocomplete
                    dense
                    outlined
                    v-model="date"
                    :items="dayOptions"
                    item-value="value"
                    item-text="label"
                  ></v-autocomplete>
                </v-col>
                <v-col cols="7" class="align-end text-right">
                  <v-btn small color="primary" @click="storeDate">
                    Submit
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
        </v-col>
      </v-row>
    </div>
    <Preloader v-else />
  </div>
  <NoAccess v-else />
</template>

<script>
import Back from "../../components/Snippets/Back.vue";

export default {
  components: { Back },

  data: () => ({
    isCompany: true,
    branch_id: null,
    branchesList: [],
    payload: {
      salary_type: "basic_salary",
      ot_value: 1.5,
      deduction_value: 1.5,
      branch_id: "",
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

  async created() {
    this.payload.company_id = this.$auth?.user?.company?.id;

    if (this.$auth.user.branch_id) {
      this.branch_id = this.$auth.user.branch_id;
      this.payload.branch_id = this.$auth.user.branch_id;
      this.isCompany = false;
      return;
    }

    try {
      const { data } = await this.$axios.get(`branches_list`, {
        params: {
          per_page: 100,
          company_id: this.$auth.user.company_id,
        },
      });
      this.branchesList = data;
    } catch (error) {
      // Handle the error
      console.error("Error fetching branch list", error);
    }

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
        branch_id: this.branch_id,
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
