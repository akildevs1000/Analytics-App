<template>
  <div v-if="can(`attendance_report_access`)">
    <v-card elevation="0" class="mt-2" v-if="can(`attendance_report_view`)">
      <v-card-text class="py-3">
        <v-row>
          <v-col md="2">
            Status
            <v-select
              class="mt-2"
              outlined
              dense
              v-model="payload.status"
              x-small
              :items="statuses"
              item-value="id"
              item-text="name"
              :hide-details="true"
            ></v-select>
          </v-col>
          <!-- <v-col md="2">
            Departments
            <v-autocomplete
              @change="getScheduledEmployees"
              class="mt-2"
              outlined
              dense
              multiple
              v-model="payload.department_ids"
              x-small
              :items="departments"
              item-value="id"
              item-text="name"
              :hide-details="true"
            ></v-autocomplete>
          </v-col> -->
          <v-col md="2">
            Employee ID
            <v-autocomplete
              class="mt-2"
              outlined
              dense
              v-model="payload.employee_id"
              x-small
              :items="scheduled_employees"
              item-value="system_user_id"
              item-text="name_with_user_id"
              :hide-details="true"
            ></v-autocomplete>
          </v-col>

          <v-col md="2">
            <div>Frequency</div>
            <v-autocomplete
              class="mt-2"
              outlined
              dense
              v-model="report_type"
              x-small
              :items="['Daily', 'Weekly', 'Monthly', 'Custom']"
              item-text="['Daily']"
              :hide-details="true"
            ></v-autocomplete>
          </v-col>
          <v-col md="2" v-if="report_type == 'Daily'">
            <div>Date</div>
            <div class="text-left mt-2">
              <v-menu
                class="mt-2"
                ref="daily_menu"
                v-model="daily_menu"
                :close-on-content-click="false"
                :return-value.sync="daily_date"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    :hide-details="payload.daily_date"
                    outlined
                    dense
                    v-model="payload.daily_date"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker v-model="payload.daily_date" no-title scrollable>
                  <v-spacer></v-spacer>
                  <v-btn text color="primary" @click="daily_menu = false">
                    Cancel
                  </v-btn>
                  <v-btn
                    text
                    color="primary"
                    @click="set_date_save($refs.daily_menu, payload.daily_date)"
                  >
                    OK
                  </v-btn>
                </v-date-picker>
              </v-menu>
            </div>
          </v-col>
          <v-col md="2" v-if="report_type !== 'Daily'">
            <div class="text-left">
              <v-menu
                ref="from_menu"
                v-model="from_menu"
                :close-on-content-click="false"
                :return-value.sync="from_date"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <div class="mb-2">From Date</div>
                  <v-text-field
                    :hide-details="payload.from_date"
                    outlined
                    dense
                    v-model="payload.from_date"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker v-model="payload.from_date" no-title scrollable>
                  <v-spacer></v-spacer>
                  <v-btn text color="primary" @click="from_menu = false">
                    Cancel
                  </v-btn>
                  <v-btn
                    text
                    color="primary"
                    @click="set_date_save($refs.from_menu, payload.from_date)"
                  >
                    OK
                  </v-btn>
                </v-date-picker>
              </v-menu>
            </div>
          </v-col>
          <v-col md="2" v-if="report_type !== 'Daily'">
            <div class="mb-2">To Date</div>

            <div class="text-left">
              <v-menu
                ref="to_menu"
                v-model="to_menu"
                :close-on-content-click="false"
                :return-value.sync="to_date"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    :hide-details="payload.to_date"
                    outlined
                    dense
                    v-model="payload.to_date"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  v-model="payload.to_date"
                  :max="max_date"
                  no-title
                  scrollable
                >
                  <v-spacer></v-spacer>
                  <v-btn text color="primary" @click="to_menu = false">
                    Cancel
                  </v-btn>
                  <v-btn
                    text
                    color="primary"
                    @click="set_date_save($refs.to_menu, payload.to_date)"
                  >
                    OK
                  </v-btn>
                </v-date-picker>
              </v-menu>
            </div>
          </v-col>

          <v-col md="2">
            <div>Report Templates</div>
            <v-autocomplete
              class="mt-2"
              outlined
              dense
              v-model="report_template"
              x-small
              :items="['Template1', 'Template2']"
              item-text="['Daily']"
              :hide-details="true"
            ></v-autocomplete>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
    <v-card
      class="mb-5 mt-5"
      elevation="0"
      v-if="can(`attendance_report_view`)"
    >
      <v-tabs v-model="tab" background-color="background" right dark>
        <v-tabs-slider></v-tabs-slider>

        <v-tab href="#tab-1"> Single </v-tab>

        <v-tab href="#tab-2"> Double </v-tab>

        <v-tab href="#tab-3"> Multi </v-tab>
      </v-tabs>

      <v-tabs-items v-model="tab">
        <v-tab-item value="tab-1">
          <AttendanceReport
            title="General Reports"
            shift_type_id="1"
            :headers="generalHeaders"
            :report_template="report_template"
            :report_type="report_type"
            :status="payload.status"
            :department_ids="payload.department_ids"
            :employee_id="payload.employee_id"
            :daily_date="payload.daily_date"
            :from_date="payload.from_date"
            :to_date="payload.to_date"
            process_file_endpoint=""
            render_endpoint="render_general_report"
            :key="key1"
            :key1="payload.key1"
          />
        </v-tab-item>
        <v-tab-item value="tab-2">
          <AttendanceReport
            title="Split Reports"
            shift_type_id="5"
            :headers="doubleHeaders"
            :report_template="report_template"
            :report_type="report_type"
            :status="payload.status"
            :department_ids="payload.department_ids"
            :employee_id="payload.employee_id"
            :daily_date="payload.daily_date"
            :from_date="payload.from_date"
            :to_date="payload.to_date"
            process_file_endpoint="multi_in_out_"
            render_endpoint="render_multi_inout_report"
            :key="key1"
            :key1="payload.key1"
          />
        </v-tab-item>
        <v-tab-item value="tab-3">
          <AttendanceReport
            title="Multi In/Out Reports"
            shift_type_id="2"
            :headers="multiHeaders"
            :report_template="report_template"
            :report_type="report_type"
            :status="payload.status"
            :department_ids="payload.department_ids"
            :employee_id="payload.employee_id"
            :daily_date="payload.daily_date"
            :from_date="payload.from_date"
            :to_date="payload.to_date"
            :key1="payload.key1"
            process_file_endpoint="multi_in_out_"
            render_endpoint="render_multi_inout_report"
            :key="key1"
          />
        </v-tab-item>
      </v-tabs-items>
    </v-card>
  </div>
  <NoAccess v-else />
</template>
<script>
import AttendanceReport from "../../components/attendance_report/reportComponent.vue";
import Back from "../../components/Snippets/Back.vue";

import generalHeaders from "../../headers/general.json";
import multiHeaders from "../../headers/multi.json";
import doubleHeaders from "../../headers/double.json";

export default {
  components: { AttendanceReport, Back },

  props: [
    "title",
    "shift_type_id",
    "render_endpoint",
    "process_file_endpoint",
    "system_user_id",
    "key1",
  ],

  data: () => ({
    CustomComponentKey: 1,
    tab: null,
    generalHeaders,
    multiHeaders,
    doubleHeaders,
    filters: {},
    attendancFilters: false,
    isFilter: false,
    datatable_search_textbox: "",
    datatable_filter_date: "",
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",
    date: null,
    menu: false,
    selectedItems: [],
    time_table_dialog: false,
    log_details: false,
    overtime: false,
    options: {},
    date: null,
    menu: false,
    loading: false,
    time_menu: false,
    manual_time_menu: false,
    Model: "Attendance Reports",
    endpoint: "report",
    search: "",
    snackbar: false,
    add_manual_log: false,
    dialog: false,
    generateLogsDialog: false,
    reportSync: false,
    from_date: null,
    from_menu: false,
    to_date: null,
    to_menu: false,
    ids: [],
    departments: [],
    scheduled_employees: [],
    DateRange: true,
    devices: [],
    valid: true,
    nameRules: [(v) => !!v || "reason is required"],
    timeRules: [(v) => !!v || "time is required"],
    deviceRules: [(v) => !!v || "device is required"],
    daily_menu: false,
    daily_date: null,
    dailyDate: false,
    editItems: {
      attendance_logs_id: "",
      UserID: "",
      device_id: "",
      user_id: "",
      reason: "",
      date: "",
      time: null,
      manual_entry: false,
    },
    loading: false,
    total: 0,

    report_template: "Template1",
    report_type: "Monthly",
    payload: {
      from_date: null,
      to_date: null,
      daily_date: null,
      employee_id: "",
      department_ids: [{ id: "-1", name: "" }],
      status: "-1",
      late_early: "Select All",
      key1: "1",
    },
    log_payload: {
      user_id: null,
      device_id: "",
      date: null,
      time: null,
    },
    log_list: [],
    snackbar: false,
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    data: [],
    shifts: [],
    errors: [],
    custom_options: {},
    statuses: [
      {
        name: `Select All`,
        id: `-1`,
      },
      {
        name: `Present`,
        id: `P`,
      },
      {
        name: `Absent`,
        id: `A`,
      },
      {
        name: `Missing`,
        id: `M`,
      },
      {
        name: `Off`,
        id: `O`,
      },
      {
        name: `Leave`,
        id: `L`,
      },
      {
        name: `Holiday`,
        id: `H`,
      },
      {
        name: `Vaccation`,
        id: `V`,
      },
      {
        name: `Manual Entry`,
        id: `ME`,
      },
    ],
    max_date: null,
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
    system_user_id(val) {
      this.payload.employee_id = val;
      this.CustomComponentKey = this.CustomComponentKey + 1;
    },
    key1(val) {
      this.CustomComponentKey = this.CustomComponentKey + 1;
    },
  },
  async created() {
    this.payload.employee_id = this.system_user_id;
    this.payload.key1 = this.CustomComponentKey + 1;

    this.loading = true;
    // this.setMonthlyDateRange();
    this.payload.daily_date = new Date().toJSON().slice(0, 10);

    this.payload.department_ids = this.$auth.user.assignedDepartments;

    let options = {
      params: {
        per_page: 1000,
        company_id: this.$auth.user.company_id,
        //department_ids: this.$auth.user.assignedDepartments,
      },
    };
    this.getDepartments(options);
    this.getDeviceList(options);
    this.getScheduledEmployees();

    let dt = new Date();
    let y = dt.getFullYear();
    let m = dt.getMonth() + 1;
    let dd = new Date(dt.getFullYear(), m, 0);

    m = m < 10 ? "0" + m : m;

    this.payload.from_date = `${y}-${m}-01`;
    this.payload.to_date = `${y}-${m}-${dd.getDate()}`;

    this.CustomComponentKey = this.CustomComponentKey + 1;
  },

  methods: {
    getScheduledEmployees() {
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
          department_ids: this.payload.department_ids,
          shift_type_id: this.shift_type_id,
        },
      };

      this.$axios
        .get(`/scheduled_employees_with_type`, options)
        .then(({ data }) => {
          this.scheduled_employees = data;
          this.scheduled_employees.unshift({
            system_user_id: "",
            name_with_user_id: "Select All",
          });
        });
    },
    setSevenDays(selected_date) {
      const date = new Date(selected_date);

      date.setDate(date.getDate() + 6);

      let datetime = new Date(date);

      let d = datetime.getDate();
      d = d < "10" ? "0" + d : d;
      let m = datetime.getMonth() + 1;
      m = m < 10 ? "0" + m : m;
      let y = datetime.getFullYear();

      this.max_date = `${y}-${m}-${d}`;
      this.payload.to_date = `${y}-${m}-${d}`;
    },

    setThirtyDays(selected_date) {
      const date = new Date(selected_date);

      date.setDate(date.getDate() + 29);

      let datetime = new Date(date);

      let d = datetime.getDate();
      d = d < "10" ? "0" + d : d;
      let m = datetime.getMonth() + 1;
      m = m < 10 ? "0" + m : m;
      let y = datetime.getFullYear();

      this.max_date = `${y}-${m}-${d}`;
      this.payload.to_date = `${y}-${m}-${d}`;
    },

    set_date_save(from_menu, field) {
      from_menu.save(field);

      if (this.report_type == "Weekly") {
        this.setSevenDays(this.payload.from_date);
      } else if (
        this.report_type == "Monthly" ||
        this.report_type == "Custom"
      ) {
        this.setThirtyDays(this.payload.from_date);
      }
    },
    setFromDate() {
      if (this.payload.from_date == null) {
        const dt = new Date();
        const y = dt.getFullYear();
        const m = dt.getMonth() + 1;
        const formattedMonth = m < 10 ? "0" + m : m;
        this.payload.from_date = `${y}-${formattedMonth}-01`;
      }
    },

    getDeviceList(options) {
      this.$axios.get(`/device_list`, options).then(({ data }) => {
        this.devices = data;
      });
    },

    setDailyDate() {
      this.payload.daily_date = new Date().toJSON().slice(0, 10);
      delete this.payload.from_date;
      delete this.payload.to_date;
    },
    async getDepartments(options) {
      const { employee, user_type } = this.$auth.user;

      let url = "departments";

      try {
        if (user_type === "employee") {
          const id = employee.id;
          url = "assigned-department-employee";
          const { data } = await this.$axios.get(`${url}/${id}`, options);
          this.departments = data;
        } else {
          const { data } = await this.$axios.get(url, options);
          this.departments = data.data;
          // this.payload.department_ids = [data.data[0].id];
        }
      } catch (error) {
        console.error("Error fetching departments:", error);
      }
    },

    caps(str) {
      return str.replace(/_/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
    },

    can(per) {
      return this.$pagePermission.can(per, this);
    },

    setStatusLabel(status) {
      const statuses = {
        A: "Absent",
        P: "Present",
        M: "Missing",
        O: "Week Off",
        L: "Leave",
        H: "Holiday",
        V: "Vaccation",
      };
      return statuses[status];
    },
  },
};
</script>
