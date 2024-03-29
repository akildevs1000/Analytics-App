<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row>
      <v-col md="12">
        <b>Shifts <small>(Single,Night,Split)</small> </b>
        <br />
        <v-skeleton-loader v-if="!shifts" type="card" />

        <v-skeleton-loader v-else-if="shifts.length == 0">
          <!-- francis -->
        </v-skeleton-loader>
        <v-checkbox
          style="float: left"
          class="mr-5"
          v-for="(item, index) in shifts"
          :key="index"
          v-model="payload.shift_ids"
          :label="item.name"
          :value="item.id"
          :error-messages="errors.shift_ids && errors.shift_ids[0]"
        ></v-checkbox>
      </v-col>
      <v-col cols="12">
        <div class="text-left">
          <v-btn
            v-if="can(`shift_create`)"
            small
            color="primary"
            @click="store_shift"
          >
            Submit
          </v-btn>
        </div>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  props: ["shift_type_id"],
  data: () => ({
    menu: false,

    errors: [],
    data: [],
    response: "",
    snackbar: false,

    Model: "Shift",
    e1: 1,
    e6: 1,

    loading: false,

    payload: {
      shift_ids: [],
    },

    errors: [],
    shifts: [],
    data: [],
    response: "",
    snackbar: false,
  }),
  async created() {
    this.getShifts();
  },
  watch: {},
  computed: {},
  methods: {
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    getShifts() {
      let payload = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };

      this.payload.company_id = this.$auth.user.company_id;
      this.payload.shift_type_id = this.shift_type_id;

      this.$axios
        .get("shift_by_types", payload)
        .then(({ data }) => {
          this.shifts = data;
        })
        .catch((err) => console.log(err));
    },

    store_shift() {
      this.payload.company_id = this.$auth.user.company_id;
      this.payload.shift_type_id = this.shift_type_id;
      this.loading = true;
      this.$axios
        .post(`/auto_shift`, this.payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.snackbar = true;
            this.response = "Shift added successfully";
            setTimeout(() => {
              this.$router.push("/shift");
            }, 1000);
          }
        })
        .catch(({ message }) => {
          this.snackbar = true;
          this.response = message;
        });
    },
  },
};
</script>
