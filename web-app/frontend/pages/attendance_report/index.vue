<template>
  <div v-if="can(`attendance_report_access`)">
    <!-- <Back class="primary white--text" /> -->
    <v-card elevation="0" class="mt-2" v-if="can(`attendance_report_view`)">
      <v-toolbar dense flat>
        <span class="headline black--text"> Attendance Reports </span>
      </v-toolbar>

      <v-card-text class="py-3">
        <v-row>
          <v-col md="1" sm="2">
            Type
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
          <v-col md="2" sm="2" v-if="isCompany">
            Branch
            <v-select
              @change="getScheduledEmployees"
              placeholder="Branch"
              class="mt-2"
              outlined
              dense
              v-model="payload.branch_id"
              x-small
              clearable
              :items="[{ id: null, branch_name: 'All Branches' }, ...branches]"
              item-value="id"
              item-text="branch_name"
              :hide-details="true"
            ></v-select>
          </v-col>
          <v-col md="2" sm="4">
            Departments
            <!-- <v-autocomplete
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
            ></v-autocomplete> -->

            <v-autocomplete
              class="mt-2"
              outlined
              dense
              @change="getScheduledEmployees"
              v-model="payload.department_ids"
              :items="departments"
              multiple
              item-text="name"
              item-value="id"
              placeholder="Departments"
            >
              <template v-if="departments.length" #prepend-item>
                <v-list-item @click="toggleDepartmentSelection">
                  <v-list-item-action>
                    <v-checkbox
                      @click="toggleDepartmentSelection"
                      v-model="selectAllDepartment"
                      :indeterminate="isIndeterminateDepartment"
                      :true-value="true"
                      :false-value="false"
                    ></v-checkbox>
                  </v-list-item-action>
                  <v-list-item-content>
                    <v-list-item-title>
                      {{ selectAllDepartment ? "Unselect All" : "Select All" }}
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </template>
              <template v-slot:selection="{ item, index }">
                <span
                  v-if="index === 0 && payload.department_ids.length == 1"
                  >{{ item.name }}</span
                >
                <span
                  v-else-if="
                    index === 1 &&
                    payload.department_ids.length == departments.length
                  "
                  class=" "
                  >All Selected
                </span>
                <span v-else-if="index === 1" class=" ">
                  {{ payload.department_ids.length }} Department(s)
                </span>
              </template>
            </v-autocomplete>
          </v-col>
          <v-col md="2" sm="4">
            Employee ID
            <v-autocomplete
              class="mt-2"
              outlined
              dense
              v-model="payload.employee_id"
              :items="scheduled_employees"
              multiple
              item-value="system_user_id"
              item-text="name_with_user_id"
              placeholder="Employees"
            >
              <!-- <v-autocomplete
              density="comfortable"
              class="mt-2"
              outlined
              dense
              v-model="payload.employee_id"
              x-small
              :items="scheduled_employees"
              item-value="system_user_id"
              item-text="name_with_user_id"
              :hide-details="true"
            > -->
              <template v-if="scheduled_employees.length" #prepend-item>
                <v-list-item @click="toggleEmployeesSelection">
                  <v-list-item-action>
                    <v-checkbox
                      @click="toggleEmployeesSelection"
                      v-model="selectAllEmployees"
                      :indeterminate="isIndeterminateEmployee"
                      :true-value="true"
                      :false-value="false"
                    ></v-checkbox>
                  </v-list-item-action>
                  <v-list-item-content>
                    <v-list-item-title>
                      {{ selectAllEmployees ? "Unselect All" : "Select All" }}
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </template>
              <template v-slot:selection="{ item, index }">
                <span v-if="index === 0 && payload.employee_id.length == 1">{{
                  item.name_with_user_id
                }}</span>
                <span
                  v-else-if="
                    index === 1 &&
                    payload.employee_id.length == scheduled_employees.length
                  "
                  class=" "
                  >All Selected
                </span>
                <span v-else-if="index === 1" class=" ">
                  {{ payload.employee_id.length }} Employee(s)
                </span>
              </template>
            </v-autocomplete>
          </v-col>
          <v-col md="1" sm="4">
            <div>Report Templates</div>
            <v-autocomplete
              density="compact"
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
          <v-col md="2" sm="5">
            <div class="mb-2">Date</div>
            <CustomFilter
              @filter-attr="filterAttr"
              :defaultFilterType="1"
              :height="'45px '"
            />
          </v-col>
          <v-col md="2" sm="2">
            <div class="mb-2">&nbsp;</div>
            <v-btn @click="commonMethod()" color="primary" primary fill
              >Generate
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <v-card class="mb-5" elevation="0" v-if="can(`attendance_report_view`)">
      <v-tabs
        class="slidegroup1"
        v-model="tab"
        background-color="popup_background"
        right
        dark
      >
        <v-tabs-slider
          class="violet slidegroup1"
          style="height: 3px"
        ></v-tabs-slider>

        <v-tab
          v-if="showTabs.single == true"
          :key="1"
          style="height: 30px"
          href="#tab-1"
          class="black--text slidegroup1"
        >
          Single
        </v-tab>

        <v-tab
          v-if="showTabs.double == true"
          :key="2"
          @click="commonMethod(2)"
          style="height: 30px"
          href="#tab-2"
          class="black--text slidegroup1"
        >
          Double
        </v-tab>

        <v-tab
          v-if="showTabs.multi == true"
          :key="3"
          @click="commonMethod(3)"
          style="height: 30px"
          href="#tab-3"
          class="black--text slidegroup1"
        >
          Multi
        </v-tab>
      </v-tabs>

      <v-tabs-items v-model="tab">
        <v-tab-item value="tab-1">
          <AttendanceReport
            v-if="showTabs.single == true"
            :key="1"
            title="General Reports"
            shift_type_id="1"
            :headers="generalHeaders"
            :report_template="report_template"
            :payload1="payload11"
            process_file_endpoint=""
            render_endpoint="render_general_report"
          />
        </v-tab-item>
        <v-tab-item value="tab-2">
          <AttendanceReport
            v-if="showTabs.double == true"
            title="Split Reports"
            shift_type_id="5"
            :headers="doubleHeaders"
            :report_template="report_template"
            :payload1="payload11"
            process_file_endpoint="multi_in_out_"
            render_endpoint="render_multi_inout_report"
            :key="2"
            ref="profile"
          />
        </v-tab-item>
        <v-tab-item value="tab-3">
          <AttendanceReport
            v-if="showTabs.multi == true"
            :key="3"
            title="Multi In/Out Reports"
            shift_type_id="2"
            :headers="multiHeaders"
            :report_template="report_template"
            :payload1="payload11"
            process_file_endpoint="multi_in_out_"
            render_endpoint="render_multi_inout_report"
          />
        </v-tab-item>
      </v-tabs-items>
    </v-card>
  </div>
  <NoAccess v-else />
</template>
<script>
import AttendanceReport from "../../components/attendance_report/reportComponent.vue";

import generalHeaders from "../../headers/general.json";
import multiHeaders from "../../headers/multi.json";
import doubleHeaders from "../../headers/double.json";

export default {
  components: { AttendanceReport },

  props: ["title", "shift_type_id", "render_endpoint", "process_file_endpoint"],

  data: () => ({
    key: 1,
    payload11: {},
    selectAllDepartment: false,
    selectAllEmployees: false,
    branches: [],
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
    report_type: "monthly11111111",
    payload: {
      from_date: null,
      to_date: null,
      daily_date: null,
      employee_id: "",
      department_ids: [{ id: "-1", name: "" }],
      status: "-1",
      branch_id: null,
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
        name: `All Status`,
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
        name: `Late In`,
        id: `LC`,
      },
      {
        name: `Early Out`,
        id: `EG`,
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
    filter_type_items: [
      {
        id: 1,
        name: "Today",
      },
      {
        id: 2,
        name: "Yesterday",
      },
      {
        id: 3,
        name: "This Week",
      },
      {
        id: 4,
        name: "This Month",
      },
      {
        id: 5,
        name: "Custom",
      },
    ],
    isCompany: true,
    showTabs: { single: true, double: true, multi: true },
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
    isIndeterminateDepartment() {
      return (
        this.payload.department_ids.length > 0 &&
        this.payload.department_ids.length < this.departments.length
      );
    },
    isIndeterminateEmployee() {
      return (
        this.payload.employee_id.length > 0 &&
        this.payload.employee_id.length < this.scheduled_employees.length
      );
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
    selectAllDepartment(value) {
      if (value) {
        this.payload.department_ids = this.departments.map((e) => e.id);
      } else {
        this.payload.department_ids = [];
      }
    },
    selectAllEmployees(value) {
      if (value) {
        this.payload.employee_id = this.scheduled_employees.map(
          (e) => e.system_user_id
        );
      } else {
        this.payload.employee_id = [];
      }
    },
    // tab(value) {
    //   this.payload11 = {
    //     ...this.payload,
    //     tabid: value,
    //   };
    //   this.commonMethod();
    // },
  },
  async created() {
    this.loading = true;
    // this.setMonthlyDateRange();
    this.payload.daily_date = new Date().toJSON().slice(0, 10);
    this.payload.department_ids = [];
    if (this.$auth.user.assignedDepartments)
      this.payload.department_ids = this.$auth.user.assignedDepartments;

    let options = {
      params: {
        per_page: 1000,
        company_id: this.$auth.user.company_id,
        //department_ids: this.$auth.user.assignedDepartments,
      },
    };
    this.getAttendanceTabs();
    setTimeout(() => {
      this.getBranches();
      this.getScheduledEmployees();
      this.getDeviceList(options);
    }, 3000);

    let dt = new Date();
    let y = dt.getFullYear();
    let m = dt.getMonth() + 1;
    let dd = new Date(dt.getFullYear(), m, 0);

    m = m < 10 ? "0" + m : m;

    this.payload.from_date = `${y}-${m}-01`;
    this.payload.from_date = `${y}-${m}-${dd.getDate()}`;
    this.payload.to_date = `${y}-${m}-${dd.getDate()}`;
    setTimeout(() => {
      this.getDepartments(options);
      this.commonMethod();
    }, 1000);

    setTimeout(() => {
      this.tab = "tab-2";
    }, 1000);
    setTimeout(() => {
      this.tab = "tab-3";
    }, 1000);
    setTimeout(() => {
      this.tab = "tab-1";
    }, 1000);
  },

  methods: {
    toggleDepartmentSelection() {
      this.selectAllDepartment = !this.selectAllDepartment;
    },
    toggleEmployeesSelection() {
      this.selectAllEmployees = !this.selectAllEmployees;
    },
    filterAttr(data) {
      this.from_date = data.from;
      this.to_date = data.to;
      this.filterType = "Monthly"; // data.type;

      //this.search = data.search;
      // if (this.from_date && this.to_date) this.commonMethod();
    },

    commonMethod(id = 0) {
      // const today = new Date();
      // switch (this.filterType) {
      //   case 1:
      //     this.from_date = this.currentDate;
      //     this.to_date = this.currentDate;
      //     break;
      //   case 2:
      //     this.from_date = new Date(Date.now() - 86400000)
      //       .toISOString()
      //       .slice(0, 10);
      //     this.to_date = new Date(Date.now() - 86400000)
      //       .toISOString()
      //       .slice(0, 10);
      //     break;
      //   case 3:
      //     this.from_date = this.week[0];
      //     this.to_date = this.week[1];
      //     break;
      //   case 4:
      //     this.from_date = this.getFirstAndLastDay()[0];
      //     this.to_date = this.getFirstAndLastDay()[1];
      //     break;

      //   // default:
      //   //   this.from_date = new Date().toJSON().slice(0, 10);
      //   //   this.to_date = new Date().toJSON().slice(0, 10);
      //   //   break;
      // }
      //this.getDataFromApi();
      let filterDay = this.filter_type_items.filter(
        (e) => e.id == this.filterType
      );
      if (filterDay[0]) {
        if (filterDay[0].name == "Today") this.report_type = "Daily";
        else filterDay = filterDay[0].name;
      }

      if (filterDay == "") {
        filterDay = "Daily";
      }

      this.payload11 = {
        ...this.payload,
        report_type: "Monthly", //filterDay,
        tabselected: id, //this.tab
        from_date: this.from_date,
        to_date: this.to_date,
        filterType: this.filterType,
        key: this.key++,
      };

      this.getAttendanceTabs();
    },
    getFirstAndLastDay() {
      const currentDate = new Date();
      const day = currentDate.getDate();
      const month = (currentDate.getMonth() + 1).toString().padStart(2, "0");
      const year = currentDate.getFullYear();
      const last = new Date(year, month, 0)
        .getDate()
        .toString()
        .padStart(2, "0");

      let firstDay = `${year}-${month}-0${1}`;

      let lastDayFirst = last > 9 ? `${last}` : `0${last}`;

      let lastDay = `${year}-${month}-${lastDayFirst}`;

      return [firstDay, lastDay];
    },
    week() {
      const today = new Date();
      const dayOfWeek = today.getDay(); // Sunday = 0, Monday = 1, ..., Saturday = 6
      const startOfWeek = new Date(
        today.getFullYear(),
        today.getMonth(),
        today.getDate() - dayOfWeek
      );
      const endOfWeek = new Date(
        today.getFullYear(),
        today.getMonth(),
        startOfWeek.getDate() + 6
      );

      return [
        startOfWeek.toISOString().slice(0, 10),
        endOfWeek.toISOString().slice(0, 10),
      ];
    },

    getScheduledEmployees() {
      let options = {
        params: {
          per_page: 1000,
          branch_id: this.payload.branch_id,
          company_id: this.$auth.user.company_id,
          department_ids: this.payload.department_ids,
          shift_type_id: this.shift_type_id,
        },
      };

      this.$axios
        .get(`/scheduled_employees_with_type`, options)
        .then(({ data }) => {
          this.scheduled_employees = data;
          // this.scheduled_employees.unshift({
          //   system_user_id: "",
          //   name_with_user_id: "All Employees",
          // });
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
    getBranches() {
      if (this.$auth.user.branch_id) {
        this.payload.branch_id = this.$auth.user.branch_id;

        this.isCompany = false;
        return;
      }

      this.$axios
        .get("branch", {
          params: {
            per_page: 1000,
            company_id: this.$auth.user.company_id,
          },
        })
        .then(({ data }) => {
          this.branches = data.data;
        });
    },
    getAttendanceTabs() {
      this.$axios
        .get("get_attendance_tabs", {
          params: {
            per_page: 10,
            company_id: this.$auth.user.company_id,
            from_date: this.from_date,
            to_date: this.to_date,
          },
        })
        .then(({ data }) => {
          this.showTabs = data;
          this.payload.showTabs = data;
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
          this.toggleDepartmentSelection();
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
        LC: "Late In",
        EG: "Early Out",
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
<style>
/* .v-slide-group__content {
  height: 30px !important;
}

.v-slide-group__wrapper {
  height: 34px !important;
} */
.slidegroup1 .v-slide-group {
  height: 34px !important;
}
</style>
