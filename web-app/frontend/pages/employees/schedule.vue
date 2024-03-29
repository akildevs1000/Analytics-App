<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-card>
      <v-toolbar class="rounded-md" color="popup_background" dense flat>
        <v-col cols="12">
          <v-toolbar-title><span>Schedule(s)</span></v-toolbar-title>
        </v-col>
      </v-toolbar>
      <v-data-table
        dense
        :headers="headers_table"
        :items="schdulesList"
        model-value="data.id"
        :loading="loading"
        :options.sync="options"
        :footer-props="{
          itemsPerPageOptions: [10, 50, 100, 500, 1000],
        }"
        class="elevation-1 alternate-rows"
        :server-items-length="totalRowsCount"
      >
        <template v-slot:item.sno="{ item, index }">
          {{
            currentPage
              ? (currentPage - 1) * perPage +
                (cumulativeIndex + schdulesList.indexOf(item))
              : "-"
          }}
        </template>

        <template v-slot:item.shift_name="{ item }"
          >{{ item.shift && item.shift.name }}
        </template>
        <template v-slot:item.on_duty_time="{ item }"
          >{{ item.shift && item.shift.on_duty_time }} to
          {{ item.shift && item.shift.off_duty_time }}
        </template>
        <template v-slot:item.working_hours="{ item }">
          {{ item.shift && item.shift.working_hours }}h
        </template>
        <template v-slot:item.from_date="{ item }"
          >{{ $dateFormat.format1(item.from_date) }} <br />
          {{ $dateFormat.format1(item.to_date) }}
        </template>
        <template v-slot:item.days="{ item, index }">
          <span v-for="(day, index2) in item.shift.days"
            >{{ day }}<span v-if="index2 < item.shift.days.length - 1"> ,</span>
          </span>
        </template>
        <template v-slot:item.isOverTime="{ item }">
          <v-switch
            disabled
            v-model="item.isOverTime"
            lable="item.isOverTime"
          ></v-switch>
        </template>
        <template v-slot:item.shift_type="{ item }"
          >{{ item.shift_type.name }}
        </template>
      </v-data-table>
    </v-card>
    <!-- <v-row>
      <v-col md="12" class="float-right">
        <div class="float-right">
          <v-pagination v-model="pagination.current" :length="pagination.total" @input="onPageChange"
            :total-visible="12"></v-pagination>
        </div>
      </v-col>
    </v-row> -->
  </div>
</template>
<script>
export default {
  // props: ["table_id", "employee_id", "system_user_id"],
  data: () => ({
    schdulesList: [],
    employees: [],
    cumulativeIndex: 1,
    perPage: 10,
    currentPage: 1,
    branchesList: [],
    branch_id: null,

    totalRowsCount: 0,

    snack: false,
    snackColor: "",
    snackText: "",
    displayNoRecords: false,

    Module: "Employee Schedule",
    shift_types: [],
    manual_shift: {},
    options: { perPage: 10 },
    options_dialog: {},
    endpoint: "scheduled_employees_index",
    endpoint_dialog: "scheduled_employees_list",
    search: "",
    shifts_for_filter: [],
    dialog_search: "",
    snackbar: false,
    dialog: false,
    editDialog: false,

    loading: false,
    loading_dialog: false,
    isEdit: false,
    total: 0,
    total_dialog: 0,
    system_user_id: 0,

    headers_table: [
      {
        text: "#",
        align: "left",
        sortable: false,
        value: "sno",
        filterable: false,
      },
      {
        text: "Shift Name",
        align: "left",
        sortable: false,
        value: "shift_name",
        filterable: false,
      },
      {
        text: "Time",
        align: "left",
        sortable: false,
        value: "on_duty_time",
        filterable: false,
      },
      {
        text: "Working Hours",
        align: "left",
        sortable: false,
        value: "working_hours",
        filterable: false,
      },

      {
        text: "Date",
        align: "left",
        sortable: false,
        value: "from_date",
        filterable: false,
      },
      {
        text: "Days",
        align: "left",
        sortable: false,
        value: "days",
        filterable: false,
      },
      {
        text: "OverTime",
        align: "left",
        sortable: false,
        value: "isOverTime",
        filterable: false,
      },
      {
        text: "Shift Type",
        align: "left",
        sortable: false,
        value: "shift_type",
        filterable: false,
      },
    ],
    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    response: "",
    data: [],

    errors: [],
    headers_ids: [],
  }),

  computed: {},

  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  created() {
    this.system_user_id = this.$auth.user.employee.system_user_id;
    this.getDataFromApi();
  },

  methods: {
    getCurrentShift(item) {
      // Define an array of day names
      const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
      const dayName = daysOfWeek[new Date().getDay()];
      const { shift_name } = item.roster.json.find((e) => e.day == dayName);

      return shift_name;
    },
    gotoCreateSchedule() {
      this.$router.push(`/employee_schedule/create`);
    },
    datatable_save() {},
    datatable_cancel() {
      this.datatable_search_textbox = "";
    },
    datatable_open() {
      this.datatable_search_textbox = "";
    },
    datatable_close() {
      this.loading = false;
      //this.datatable_search_textbox = '';
    },
    onPageChange() {
      this.getDataFromApi();
    },

    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },

    ScheduleItem(item, type) {
      this.empId = item.employee_id;
      let id = item.employee_id;
      let options = {
        company_id: this.$auth.user.company_id,
      };
      this.$axios
        .get(`get_roster_by_employee/${id}`, { params: options })
        .then(({ data }) => {
          type == "edit" ? (this.isEdit = true) : (this.isEdit = false);
          this.schedules_temp_list = data;
          this.editDialog = true;
        });
    },

    set_date_save(from_menu, from, index) {
      from_menu.save(from);
      let toDate = this.setSevenDays(from);
      this.schedules_temp_list[index].to_date = toDate;
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
      return `${y}-${m}-${d}`;
    },

    update() {
      let payload = {
        schedules: this.schedules_temp_list,
        deleteIds: this.deleteIds,
        company_id: this.$auth.user.company_id,
      };
      this.process(this.$axios.put(`schedule_update/${this.empId}`, payload));
    },

    removeItem(i, item) {
      if (item.id) {
        this.deleteIds.push(item.id);
      }
      this.schedules_temp_list.splice(i, 1);
    },

    get_rosters() {
      let options = {
        company_id: this.$auth.user.company_id,
      };
      this.$axios.get("roster_list", { params: options }).then(({ data }) => {
        this.rosters = data;
        this.addRow(data[0].schedule_id);
        this.rosterFirstValue = data[0].schedule_id;
      });
    },

    addRow(id) {
      let item = {
        schedule_id: id,
        from_date: new Date().toJSON().slice(0, 10),
        to_date: new Date().toJSON().slice(0, 10),
        is_over_time: false,
      };
      if (this.schedules_temp_list.length < 5) {
        this.schedules_temp_list.push(item);
      }
    },

    runShiftTypeFunction() {
      this.getShifts(this.shift_type_id);
    },

    close() {
      this.dialog = false;
      this.is_edit = false;
    },

    getShifts(shift_type_id) {
      if (this.shift_type_id == 3) {
        this.shift_id = 0;
        this.shifts = [];
        return;
      }

      let options = {
        params: {
          shift_type_id: shift_type_id,
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios
        .get("shift_by_type", options)
        .then(({ data }) => {
          this.shifts = data;
          // this.shifts.unshift({ id: "", name: "Select Shift" });
          // this.shifts_for_filter = data;
        })
        .catch((err) => console.log(err));
    },

    getShiftTypes(options) {
      this.$axios
        .get("shift_type", options)
        .then(({ data }) => {
          this.shift_types = data;
          this.shift_types.unshift({ id: "", name: "Select Shift Type" });
        })
        .catch((err) => console.log(err));
    },

    runShiftFunction() {
      this.shifts = this.shifts.filter((e) => e.id !== "---");
    },

    getDepartments(options) {
      this.$axios
        .get("departments", options)
        .then(({ data }) => {
          this.departments = data.data;
          this.departments.unshift({ id: "---", name: "Select All" });
        })
        .catch((err) => console.log(err));
    },

    employeesByDepartment() {
      this.loading_dialog = true;

      const { page, itemsPerPage } = this.options_dialog;

      let options = {
        params: {
          department_ids: this.department_ids,
          per_page: itemsPerPage,
          page: page,
          company_id: this.$auth.user.company_id,
        },
      };

      if (!this.department_ids.length) {
        this.employees_dialog = [];
        this.total_dialog = 0;
        this.loading_dialog = false;
        return;
      }

      this.$axios.get("employeesByDepartment", options).then(({ data }) => {
        this.employees_dialog = data.data;
        this.total_dialog = data.total;
        this.loading_dialog = false;
      });
    },

    getEmployeesBySubDepartment() {
      this.loading_dialog = true;

      const { page, itemsPerPage } = this.options_dialog;

      let options = {
        params: {
          department_ids: this.department_ids,
          sub_department_ids: this.sub_department_ids,
          per_page: itemsPerPage,
          page: page,
          company_id: this.$auth.user.company_id,
        },
      };

      if (!this.sub_department_ids.length) {
        this.loading_dialog = false;
        return;
      }

      this.$axios
        .get(`employeesBySubDepartment`, options)
        .then(({ data }) => {
          this.employees_dialog = data.data;
          this.total_dialog = data.total;
          this.loading_dialog = false;
        })
        .catch((err) => console.log(err));
    },

    subDepartmentsByDepartment() {
      this.options.params.department_ids = this.department_ids;

      this.$axios
        .get(`sub-departments-by-departments`, this.options)
        .then(({ data }) => {
          this.sub_departments = data;
          this.sub_departments.unshift({
            id: "---",
            name: "Select All",
          });
        })
        .catch((err) => console.log(err));
    },

    runMultipleFunctions() {
      this.employeesByDepartment();
      this.subDepartmentsByDepartment();
    },

    can(per) {
      return this.$pagePermission.can(per, this);
    },
    can_old(per) {
      let { permissions } = this.$auth.user;
      return permissions.includes(per);
    },
    getSearchRecords(filter_column = "", filter_value = "") {
      this.getDataFromApi(this.endpoint, filter_column, filter_value);
    },
    applyFilters(name, value) {
      if (value && value.length < 3) return false;
      this.getDataFromApi();
    },
    toggleFilter() {
      this.isFilter = !this.isFilter;
    },
    clearFilters() {
      this.filters = {};

      this.isFilter = false;
      this.getDataFromApi();
    },
    //main
    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      this.loading = true;

      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";

      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          pagination: true,
          company_id: this.$auth.user.company_id,
        },
      };
      this.currentPage = page;
      //if (filter_value != "") options.params[filter_column] = filter_value;
      this.perPage = itemsPerPage;
      this.$axios
        .get(`get_shifts_by_employee/${this.system_user_id}`, {
          params: options.params,
        })
        .then(({ data }) => {
          // if (filter_column != "" && data.data.length == 0) {
          //   this.snack = true;
          //   this.snackColor = "error";
          //   this.snackText = "No Results Found";
          //   this.loading = false;
          //   return false;
          // }
          this.schdulesList = data.data;
          this.pagination.current = data.current_page;
          this.pagination.total = data.last_page;
          this.loading = false;

          if (this.schdulesList.length == 0) {
            this.displayNoRecords = true;
          }

          this.totalRowsCount = data.total;
        });

      //this.loading = false;
    },
  },
};
</script>
