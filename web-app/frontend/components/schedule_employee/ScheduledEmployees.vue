<template>
  <div>
    <v-toolbar flat dense class="">
      <v-btn small @click="arrangeShift" class="primary" color="primary"
        >Arrange Shift(s)
      </v-btn>
      <v-icon color="primary" class="mx-2" @click="dialogVisible = true"
        >mdi-filter</v-icon
      >
    </v-toolbar>

    <v-data-table
      v-model="employee_ids"
      show-select
      item-key="id"
      :headers="headers"
      :items="employees"
      :server-items-length="total"
      :loading="loading"
      :options.sync="options"
      :footer-props="{
        itemsPerPageOptions: [50, 100, 500, 1000],
      }"
    >
      <template v-slot:item.name="{ item }">
        {{ item.first_name ? item.first_name : "---" }}
        {{ item.last_name ? item.last_name : "---" }}
      </template>
    </v-data-table>
    <v-dialog persistent v-model="dialog" width="900">
      <v-card>
        <v-card-title class="text-h5">
          Arrange Shift(s)
          <v-spacer></v-spacer>
          <v-btn class="primary" small fab @click="addRow(rosterFirstValue)">
            <b>+</b>
          </v-btn>
        </v-card-title>

        <v-divider></v-divider>
        <!-- {{ schedules_temp_list }} <br /> -->
        <!-- {{ rosters }} -->
        <v-card-text v-for="(item, i) in schedules_temp_list" :key="i">
          <v-row>
            <v-col md="3">
              <div class="">Schedule List</div>
              <v-autocomplete
                outlined
                dense
                x-small
                v-model="item.schedule_id"
                :items="rosters"
                item-value="schedule_id"
                item-text="name"
              ></v-autocomplete>
            </v-col>
            <v-col md="3">
              <div class="mb-6">
                <div>From</div>
                <!-- <v-menu
                  v-model="from_menu[i]"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="item.from_date"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      outlined
                      dense
                      :hide-details="true"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="item.from_date"
                    @input="from_menu[i] = false"
                  >
                    <v-spacer></v-spacer>
                    <v-btn text color="primary" @click="from_menu[i] = false">
                      Cancel
                    </v-btn>
                    <v-btn
                      text
                      color="primary"
                      @click="set_date_save($refs.from_menu[i], item.from_date)"
                    >
                      OK
                    </v-btn>
                  </v-date-picker>
                </v-menu> -->

                <v-menu
                  ref="from_menu"
                  v-model="from_menu[i]"
                  :close-on-content-click="false"
                  :return-value.sync="item.from_date"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      :hide-details="true"
                      outlined
                      dense
                      v-model="item.from_date"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker v-model="item.from_date" no-title scrollable>
                    <!-- :min="min_date[i]" -->
                    <v-spacer></v-spacer>
                    <v-btn text color="primary" @click="from_menu[i] = false">
                      Cancel
                    </v-btn>
                    <v-btn
                      text
                      color="primary"
                      @click="
                        set_date_save($refs.from_menu[i], item.from_date, i)
                      "
                    >
                      OK
                    </v-btn>
                  </v-date-picker>
                </v-menu>
              </div>
            </v-col>
            <v-col md="3">
              <div class="mb-6">
                <div>To</div>
                <!-- <v-menu
                  v-model="to_menu[i]"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="item.to_date"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      outlined
                      dense
                      :hide-details="true"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="item.to_date"
                    @input="to_menu[i] = false"
                  ></v-date-picker>
                </v-menu> -->

                <v-menu
                  ref="to_menu"
                  v-model="to_menu[i]"
                  :close-on-content-click="false"
                  :return-value.sync="item.to_date"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      :hide-details="true"
                      outlined
                      dense
                      v-model="item.to_date"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker v-model="item.to_date" no-title scrollable>
                    <!-- :min="max_date[i]" -->

                    <v-spacer></v-spacer>
                    <v-btn text color="primary" @click="to_menu[i] = false">
                      Cancel
                    </v-btn>
                    <v-btn
                      text
                      color="primary"
                      @click="set_date_save($refs.to_menu[i], item.to_date, i)"
                    >
                      OK
                    </v-btn>
                  </v-date-picker>
                </v-menu>
              </div>
            </v-col>
            <v-col md="2">
              <div>
                Overtime Allowed
                <v-checkbox
                  style="margin-top: -8px"
                  v-model="item.is_over_time"
                ></v-checkbox>
              </div>
            </v-col>
            <v-col md="1">
              <div></div>
              <v-icon v-if="i" @click="removeItem(i)" color="error"
                >mdi-delete</v-icon
              >
            </v-col>
          </v-row>
        </v-card-text>
        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>

          <v-btn dark small color="grey" @click="close"> Close </v-btn>
          <v-btn dark small color="primary" @click="save"> Submit </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog persistent v-model="dialogVisible" max-width="500px">
      <v-card>
        <v-card-title dense class="primary white--text background">
          Filter
          <v-spacer></v-spacer>
          <v-icon @click="dialogVisible = false" outlined dark color="white">
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>

        <v-progress-linear
          v-if="filterLoader"
          indeterminate
          color="primary"
        ></v-progress-linear>

        <br />

        <v-card-text>
          <div>
            <v-autocomplete
              placeholder="Department"
              outlined
              dense
              @change="runMultipleFunctions"
              v-model="department_ids"
              multiple
              x-small
              :items="departments"
              item-value="id"
              item-text="name"
              :disabled="is_edit == true ? true : false"
            ></v-autocomplete>
            <v-autocomplete
              placeholder="Sub Department"
              outlined
              dense
              @change="runMultipleFunctions"
              v-model="department_ids"
              multiple
              x-small
              :items="sub_departments"
              item-value="id"
              item-text="name"
              :disabled="is_edit == true ? true : false"
            ></v-autocomplete>
            <v-text-field
              label="Search Employee by ID"
              outlined
              @input="dialogSearchByEmployeeID"
              dense
              v-model="employee_search"
              append-icon="mdi-magnify"
              single-line
              hide-details
            ></v-text-field>
          </div>
        </v-card-text>

        <!-- <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn dark color="background" @click="dialogVisible = false">Close</v-btn>
        </v-card-actions> -->
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
export default {
  data: () => ({
    isActive: false,
    filterLoader: false,
    dialogVisible: false,
    from_date: null,
    from_menu: false,

    from_menu: [],
    to_menu: [],

    Module: "Employee Schedule",
    schedules_temp_list: [
      // {
      //   schedule_id: 1,
      //   from_date: new Date().toJSON().slice(0, 10),
      //   to_date: new Date().toJSON().slice(0, 10),
      //   is_over_time: false,
      // },
    ],
    options: {},
    endpoint: "scheduled_employees",
    snackbar: false,
    dialog: false,
    employee_search: "",
    loading: false,
    loading: false,
    total: 0,
    total: 0,

    department_ids: ["---"],
    sub_department_ids: ["---"],
    employee_ids: [],
    payload: {
      schedule_id: [1],
      from_date: [new Date().toJSON().slice(0, 10)],
      to_date: [new Date().toJSON().slice(0, 10)],
      is_over_time: [false],
    },
    isOverTime: false,
    is_edit: false,
    employees: [],
    departments: [],
    sub_departments: [],
    shifts: [
      {
        id: 1,
        name: "Week 1",
      },
      {
        id: 2,
        name: "Week 2",
      },
    ],
    ids: [],
    response: "",
    data: [],
    rosters: [],
    rosterFirstValue: "",
    max_date: [],
    min_date: [],
    errors: [],

    headers: [
      {
        text: "Emp Id",
        align: "left",
        sortable: false,
        value: "employee_id",
      },
      {
        text: "Name",
        sortable: false,
        value: "name",
      },
      {
        text: "Department",
        sortable: false,
        value: "department.name",
      },
      {
        text: "Sub Department",
        sortable: false,
        value: "sub_department.name",
      },
      {
        text: "Shift",
        sortable: false,
        value: "schedule.shift.name",
      },
    ],
  }),

  computed: {
    activeStyle() {
      return "border-bottom: 1px white solid !important";
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
      if (!this.is_edit) {
        this.getDepartments(this.options);
        this.getDataFromApi();
      }
    },
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  created() {
    this.loading = true;
    // this.loading = true;
    this.get_rosters();
    this.options = {
      params: {
        per_page: 1000,
        company_id: this.$auth.user.company_id,
      },
    };

    this.getDepartments(this.options);
    // this.getDataFromApi();
  },

  methods: {
    arrangeShift() {
      if (!this.employee_ids.length) {
        alert("Atleast one employee must be selected.");
        return;
      }
      this.dialog = true;
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
    removeItem(i) {
      this.schedules_temp_list.splice(i, 1);
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

    set_date_save(from_menu, from, index) {
      from_menu.save(from);
      return;
      let toDate = this.setSevenDays(from, index);
      this.schedules_temp_list[index].to_date = toDate;
    },

    set_to_date_save(from_menu, from, index) {
      from_menu.save(from);
      let toDate = this.setSevenDays(from);
      this.schedules_temp_list[index].to_date = toDate;
    },

    setSevenDays(selected_date, index) {
      const date = new Date(selected_date);
      date.setDate(date.getDate() + 6);
      let datetime = new Date(date);
      let d = datetime.getDate();
      d = d < "10" ? "0" + d : d;
      let m = datetime.getMonth() + 1;
      m = m < 10 ? "0" + m : m;
      let y = datetime.getFullYear();
      this.max_date[index] = `${y}-${m}-${d}`;
      this.min_date[index] = `${y}-${m}-${d}`;

      return `${y}-${m}-${d}`;
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

    close() {
      this.dialog = false;
      this.is_edit = false;
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
      this.loading = true;

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          department_ids: this.department_ids,
          per_page: itemsPerPage,
          page: page,
          company_id: this.$auth.user.company_id,
        },
      };

      if (!this.department_ids.length) {
        this.employees = [];
        this.total = 0;
        this.loading = false;
        return;
      }

      this.$axios.get("employeesByDepartment", options).then(({ data }) => {
        this.employees = data.data;
        this.total = data.total;
        this.loading = false;
      });
    },

    dialogSearchIt(e) {
      this.employeesByDepartment();
      this.getEmployeesBySubDepartment();
    },

    dialogSearchByEmployeeID(e) {
      if (e.length == 0) {
        this.employeesByDepartment();
      } else if (e.length > 2) {
        this.filterLoader = true;
        const { page, itemsPerPage } = this.options;

        let options = {
          params: {
            per_page: itemsPerPage,
            page: page,
            employee_search: this.employee_search,
            company_id: this.$auth.user.company_id,
          },
        };
        this.$axios.get("employeesByEmployeeId", options).then(({ data }) => {
          this.employees = data.data;
          this.total = data.total;
          this.filterLoader = false;
        });
      }
    },

    getEmployeesBySubDepartment() {
      this.loading = true;

      const { page, itemsPerPage } = this.options;

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
        this.loading = false;
        return;
      }

      this.$axios
        .get(`employeesBySubDepartment`, options)
        .then(({ data }) => {
          this.employees = data.data;
          this.total = data.total;
          this.loading = false;
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
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },
    getDataFromApi(url = this.endpoint) {
      this.loading = true;

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          per_page: itemsPerPage,
          page: page,
          company_id: this.$auth.user.company_id,
        },
      };

      this.$axios.get(url, options).then(({ data }) => {
        this.employees = data.data;
        this.total = data.total;
        this.loading = false;
      });
    },
    searchIt() {
      let s = this.search.length;
      let search = this.search;
      if (s == 0) {
        this.getDataFromApi();
      } else if (s > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${search}`);
      }
    },

    delteteSelectedRecords() {
      let just_ids = this.ids.map((e) => e.schedule.id);

      confirm(
        "Are you sure you wish to delete selected records , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .post(`schedule_employee/delete/selected`, {
            ids: just_ids,
          })
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
              alert("1");
            } else {
              this.getDataFromApi();
              this.snackbar = data.status;
              this.ids = [];
              this.response = "Selected records has been deleted";
            }
          })
          .catch((err) => console.log(err));
    },

    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete("schedule_employees/" + item.employee.system_user_id)
          .then(({ data }) => {
            const index = this.employees.indexOf(item);
            this.employees.splice(index, 1);
            this.snackbar = data.status;
            this.response = data.message;
            this.getDataFromApi();
          })
          .catch((err) => console.log(err));
    },

    save() {
      this.loading = true;
      this.errors = [];

      let payload = {
        employee_ids: this.employee_ids.map((e) => e.system_user_id),
        schedules: this.schedules_temp_list,
        company_id: this.$auth.user.company_id,
      };

      // return;
      if (this.is_edit) {
        this.process(
          this.$axios.post(
            `schedule_employees/${payload.employee_ids}`,
            payload
          )
        );
      } else {
        this.process(this.$axios.post(`store_schedule_arrange`, payload));
      }
    },

    process(method) {
      method
        .then(({ data }) => {
          if (!data.status) {
            if (data?.custom_errors) {
              this.custom_errors = data.custom_errors;
              this.errors = [];
            }
            if (data?.errors) {
              this.errors = data.errors;
              this.custom_errors = [];
            }
            this.loading = false;
            return;
          }
          this.dialog = false;
          this.response = data.message;
          this.snackbar = true;
          this.loading = false;
          setTimeout(() => {
            this.$router.push("/employee_schedule");
          }, 1000);
          // this.getDataFromApi();
        })
        .catch((err) => console.log(err));
    },
  },
};
</script>
