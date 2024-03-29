<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row>
      <v-col md="3">
        <v-autocomplete
          label="Employee ID"
          class="mt-5"
          placeholder="Employee Device Id"
          v-model="log_payload.user_id"
          :items="employees"
          :item-text="`name_with_user_id`"
          item-value="system_user_id"
          dense
          outlined
          @change="handleChangeEvent(log_payload.user_id)"
        >
        </v-autocomplete>
        <span v-if="errors && errors.user_id" class="text-danger mt-2">{{
          errors.user_id[0]
        }}</span>
      </v-col>
      <v-col md="3">
        <v-menu
          ref="menu"
          v-model="menu"
          :close-on-content-click="false"
          :return-value.sync="date"
          transition="scale-transition"
          offset-y
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              label="Date"
              class="mt-5"
              dense
              outlined
              placeholder="Date"
              v-model="log_payload.date"
              readonly
              v-bind="attrs"
              v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker v-model="log_payload.date" no-title scrollable>
            <v-spacer></v-spacer>
            <v-btn text color="primary" @click="menu = false"> Cancel </v-btn>
            <v-btn
              text
              color="primary"
              @click="$refs.menu.save(log_payload.date)"
            >
              OK
            </v-btn>
          </v-date-picker>
        </v-menu>
      </v-col>
      <v-col md="3">
        <v-menu
          ref="time_menu_ref"
          v-model="time_menu"
          :close-on-content-click="false"
          :nudge-right="40"
          :return-value.sync="log_payload.time"
          transition="scale-transition"
          offset-y
          max-width="290px"
          min-width="290px"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              label="Time"
              v-model="log_payload.time"
              class="mt-5"
              dense
              outlined
              placeholder="Time"
              readonly
              v-bind="attrs"
              v-on="on"
            ></v-text-field>
          </template>
          <v-time-picker
            v-if="time_menu"
            v-model="log_payload.time"
            full-width
            format="24hr"
          >
            <v-spacer></v-spacer>
            <v-btn x-small color="primary" @click="time_menu = false">
              Cancel
            </v-btn>
            <v-btn
              x-small
              color="primary"
              @click="$refs.time_menu_ref.save(log_payload.time)"
            >
              OK
            </v-btn>
          </v-time-picker>
        </v-menu>
        <span v-if="errors && errors.time" class="text-danger mt-2">{{
          errors.time[0]
        }}</span>
      </v-col>
      <v-col md="3">
        <v-autocomplete
          class="mt-5"
          label="Device Name"
          placeholder="Device Name"
          v-model="log_payload.device_id"
          :items="devices"
          item-text="name"
          item-value="device_id"
          dense
          outlined
          @change="handleChangeEvent(log_payload.user_id)"
        >
        </v-autocomplete>
        <span v-if="errors && errors.user_id" class="text-danger mt-2">{{
          errors.user_id[0]
        }}</span>
      </v-col>
      <v-col cols="12">
        <v-textarea
          class=""
          dense
          outlined
          placeholder="Reason"
          v-model="reason"
          auto-grow
          required
        ></v-textarea>
        <span v-if="errors && errors.reason" class="error--text">
          {{ errors.reason[0] }}
        </span>
      </v-col>
      <v-col col="2" class="text-end">
        <v-btn small color="primary" @click="submit"> Submit </v-btn>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  props: ["endpoint", "system_user_id", "shift_type_id"],
  data: () => ({
    Model: "Manual Log",

    date: null,
    menu: false,

    loading: false,
    time_menu: false,

    reason: null,

    log_payload: {
      user_id: null,
      device_id: "",
      date: null,
      time: null,
      shift_type_id: null,
      branch_id: 0,
      log_type: "auto",
    },
    headers: [
      {
        text: "UserID",
        align: "center",
        sortable: false,
        value: "UserID",
      },
      { text: "DeviceID", align: "center", sortable: false, value: "DeviceID" },
      { text: "LogTime", align: "center", sortable: false, value: "LogTime" },
    ],
    ids: [],
    devices: [],
    data: [],
    total: 0,
    options: {},
    errors: [],
    employees: [],
    response: "",
    snackbar: false,
  }),
  mounted() {
    this.getDeviceList();
  },
  async created() {
    try {
      this.log_payload.user_id = this.system_user_id;

      this.employees = await this.$store.dispatch("fetchDropDowns", {
        key: "employeeList",
        endpoint: "employee-list",
        refresh: true,
      });
    } catch (error) {
      console.error("Error fetching data:", error);
    }
  },
  methods: {
    getDeviceList() {
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios.get(`/device_list_not_manual`, options).then(({ data }) => {
        this.devices = data;
      });
    },
    async getLastLog(UserID) {
      this.$axios
        .get(`attendance_logs`, {
          params: {
            company_id: this.$auth.user.company_id,
            UserID,
          },
        })
        .then(({ data }) => {
          if (
            data?.data?.length &&
            ["in", "auto"].includes(data.data[0].log_type)
          ) {
            this.log_payload.log_type = "out";
          } else {
            this.log_payload.log_type = "in";
          }
        });
    },
    async handleChangeEvent(id) {
      const employee = this.employees.find((e) => id == e.system_user_id);

      if (employee) {
        this.log_payload.branch_id = employee.branch_id || 0;
      }
    },
    async submit() {
      await this.getLastLog(this.log_payload.user_id);
      let { user_id, date, time, branch_id, device_id } = this.log_payload;

      let log_payload = {
        branch_id,
        UserID: user_id,
        LogTime: date + " " + time,
        //DeviceID: "Manual",
        DeviceID: device_id,
        company_id: this.$auth.user.company_id,
        log_type: this.log_payload.log_type,
      };

      this.loading = true;

      if (!user_id || !date || !time) {
        alert("Please enter required fields");
        return;
      }

      this.$axios
        .post(`/generate_log`, log_payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.render_report(date, this.shift_type_id);
            this.$emit("close-popup");
            this.snackbar = true;
            this.response = data.message;
          }
        })
        .catch(({ message }) => {
          this.snackbar = true;
          this.response = message;
        });
    },
    render_report(date, shift_type_id) {
      let payload = {
        params: {
          dates: [date, date],
          UserIds: [this.log_payload.user_id],
          company_ids: [this.$auth.user.company_id],
          user_id: this.$auth.user.id,
          updated_by: this.$auth.user.id,
          reason: this.reason,
          employee_ids: [this.log_payload.user_id],
          shift_type_id: shift_type_id,
        },
      };
      this.$axios
        .get("render_logs", payload)
        .then(({ data }) => {
          this.loading = false;

          let message = "";
          data.forEach((element) => {
            message = message + " \n \n " + element;
          });
          this.response = message;
          this.loading = false;

          this.$emit("update-data-table");
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>
