<template>
  <div v-if="can(`employee_schedule_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-dialog persistent v-model="editDialog" width="900">
      <v-card>
        <v-card-title dense dark class="popup_background">
          {{ !isEdit ? "View Shift(s)" : "Edit Shift(s)" }}
          <v-spacer></v-spacer>

          <v-icon @click="editDialog = false" outlined dark>
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>

        <v-card-text>
          <v-col col="3" text-right class="text-right"
            ><v-btn class="primary" v-if="isEdit" small @click="addRow(1)">
              <b>Add +</b>
            </v-btn></v-col
          >
          <v-row v-for="(item, i) in schedules_temp_list" :key="i">
            <v-col md="3">
              <div class="">Schedule List</div>
              <v-autocomplete
                :error="errors && errors.shift_id"
                :error-messages="
                  errors && errors.shift_id ? errors.shift_id[0] : ''
                "
                @change="runShiftFunction(item.shift_type_id)"
                outlined
                dense
                v-model="item.shift_id"
                x-small
                :items="shifts"
                item-value="shift_id"
                item-text="name"
              ></v-autocomplete>
            </v-col>
            <v-col md="3">
              <div class="mb-6">
                <div>From</div>

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
                      v-bind="!isEdit || attrs"
                      v-on="!isEdit || on"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    :readonly="!isEdit"
                    v-model="item.from_date"
                    no-title
                    scrollable
                  >
                    <v-spacer></v-spacer>
                    <v-btn text color="primary" @click="from_menu[i] = false">
                      Cancel
                    </v-btn>
                    <v-btn
                      text
                      color="primary"
                      @click="
                        isEdit
                          ? set_date_save($refs.from_menu[i], item.from_date, i)
                          : ''
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
                <v-menu
                  v-model="to_menu[i]"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                  :readonly="!isEdit"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="item.to_date"
                      readonly
                      v-bind="!isEdit || attrs"
                      v-on="!isEdit || on"
                      outlined
                      dense
                      :hide-details="true"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    :readonly="!isEdit"
                    v-model="item.to_date"
                    @input="to_menu[i] = false"
                  ></v-date-picker>
                </v-menu>
              </div>
            </v-col>
            <v-col md="2">
              <div>
                Overtime Allowed
                <v-checkbox
                  :readonly="!isEdit"
                  style="margin-top: -8px"
                  v-model="item.is_over_time"
                ></v-checkbox>
              </div>
            </v-col>
            <v-col md="1" v-if="isEdit">
              <div></div>
              <v-icon @click="removeItem(i, item)" color="error"
                >mdi-delete</v-icon
              >
            </v-col>
          </v-row>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <!-- <v-btn dark small color="grey" @click="editDialog = false">
            Close
          </v-btn> -->
          <v-btn v-if="isEdit" dark small color="primary" @click="update">
            Submit
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog persistent v-model="dialog" width="1300">
      <v-card>
        <v-card-title class="text-h5">
          Schedule Employees
          <v-spacer></v-spacer>
          <v-btn dark small color="grey" @click="close"> Close </v-btn> &nbsp;
          <v-btn dark small color="primary" @click="save"> Submit </v-btn>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text>
          <v-row>
            <v-col md="4">
              <v-row>
                <v-col md="12">
                  <div class="mb-5">
                    <span class="text-h6">Filters</span>
                  </div>
                  <div class="mb-1">Department</div>

                  <v-autocomplete
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
                  <div class="mb-1">Sub Department</div>
                  <v-autocomplete
                    outlined
                    dense
                    @change="getEmployeesBySubDepartment"
                    v-model="sub_department_ids"
                    multiple
                    x-small
                    :items="sub_departments"
                    item-value="id"
                    item-text="name"
                    :disabled="is_edit == true ? true : false"
                  ></v-autocomplete>

                  <div class="mb-1">Shift Types</div>

                  <v-autocomplete
                    :error="errors && errors.shift_type_id"
                    :error-messages="
                      errors && errors.shift_type_id
                        ? errors.shift_type_id[0]
                        : ''
                    "
                    @change="runShiftTypeFunction"
                    outlined
                    dense
                    v-model="shift_type_id"
                    x-small
                    :items="shift_types"
                    item-value="id"
                    item-text="name"
                  ></v-autocomplete>

                  <div class="mb-1">Shifts</div>
                  <v-autocomplete
                    :error="errors && errors.shift_id"
                    :error-messages="
                      errors && errors.shift_id ? errors.shift_id[0] : ''
                    "
                    @change="runShiftFunction(item.shift_type_id)"
                    outlined
                    dense
                    v-model="shift_id"
                    x-small
                    :items="shifts"
                    item-value="id"
                    item-text="name"
                  ></v-autocomplete>
                  <div class="mb-6">
                    <div>From</div>
                    <v-menu
                      v-model="from_menu"
                      :close-on-content-click="false"
                      :nudge-right="40"
                      transition="scale-transition"
                      offset-y
                      min-width="auto"
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                          v-model="from_date"
                          readonly
                          v-bind="attrs"
                          v-on="on"
                          outlined
                          dense
                          :hide-details="true"
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="from_date"
                        @input="from_menu = false"
                      ></v-date-picker>
                    </v-menu>
                  </div>
                  <div class="mb-6">
                    <div>To</div>
                    <v-menu
                      v-model="to_menu"
                      :close-on-content-click="false"
                      :nudge-right="40"
                      transition="scale-transition"
                      offset-y
                      min-width="auto"
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                          v-model="to_date"
                          readonly
                          v-bind="attrs"
                          v-on="on"
                          outlined
                          dense
                          :hide-details="true"
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="to_date"
                        @input="to_menu = false"
                      ></v-date-picker>
                    </v-menu>
                  </div>
                  <v-checkbox
                    dense
                    v-model="isOverTime"
                    label="Overtime Allowed"
                  ></v-checkbox>
                </v-col>
              </v-row>
            </v-col>

            <v-col md="8">
              <v-row>
                <v-col md="6">
                  <div class="mb-5">
                    <span class="text-h6">Employees List</span>
                  </div>
                </v-col>
                <v-col md="6">
                  <div class="text-right">
                    <v-text-field
                      @input="dialogSearchIt"
                      dense
                      v-model="dialog_search"
                      append-icon="mdi-magnify"
                      single-line
                      hide-details
                    ></v-text-field>
                  </div>
                </v-col>
              </v-row>

              <v-data-table
                v-model="employee_ids"
                show-select
                item-key="id"
                :headers="headers_dialog"
                :items="employees_dialog"
                :server-items-length="total_dialog"
                :loading="loading_dialog"
                :options.sync="options_dialog"
                :footer-props="{
                  itemsPerPageOptions: [10, 50, 100, 500, 1000],
                }"
              >
              </v-data-table>
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

    <v-card elevation="0" class="mt-2">
      <v-toolbar class="mb-2 white--text" color="white" dense flat>
        <v-toolbar-title class="black--text"
          ><span> Schedule List</span></v-toolbar-title
        >
        <!-- <v-tooltip top dark>
          <template v-slot:activator="{ on, attrs }"> -->
        <v-btn
          dense
          class="ma-0 px-0"
          x-small
          :ripple="false"
          text
          title="Reload"
        >
          <v-icon class="ml-2" @click="clearFilters" dark
            >mdi mdi-reload</v-icon
          >
        </v-btn>
        <!-- </template>
          <span>Reload</span>
        </v-tooltip> -->
        <!-- <v-tooltip top color="primary">
          <template v-slot:activator="{ on, attrs }"> -->
        <v-btn
          dense
          class="ma-0 px-0"
          x-small
          :ripple="false"
          text
          title="Filter"
        >
          <v-icon class="ml-2" @click="toggleFilter" dark
            >mdi mdi-filter</v-icon
          >
        </v-btn>
        <!-- </template>
          <span>Filter</span>
        </v-tooltip> -->

        <v-spacer></v-spacer>

        <!-- <v-tooltip top color="primary" v-if="can(`employee_schedule_create`)">
          <template v-slot:activator="{ on, attrs }"> -->
        <v-btn
          title="Add Schedule"
          dense
          class="ma-0 px-0"
          x-small
          :ripple="false"
          text
          v-bind="attrs"
          v-on="on"
          @click="gotoCreateSchedule"
        >
          <v-icon class="ml-2" dark>mdi mdi-plus-circle</v-icon>
        </v-btn>
        <!-- </template>  
       <span>Add Schedule</span>  
        </v-tooltip> -->
      </v-toolbar>
      <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
        {{ snackText }}

        <template v-slot:action="{ attrs }">
          <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
        </template>
      </v-snackbar>
      <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
        {{ snackText }}

        <template v-slot:action="{ attrs }">
          <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
        </template>
      </v-snackbar>
      <v-data-table
        dense
        :headers="headers_table"
        :items="employees"
        model-value="data.id"
        :loading="loading"
        :options.sync="options"
        :footer-props="{
          itemsPerPageOptions: [10, 50, 100, 500, 1000],
        }"
        class="elevation-1"
        :server-items-length="totalRowsCount"
      >
        <template v-slot:header="{ props: { headers } }">
          <tr v-if="isFilter">
            <td v-for="header in headers_table" :key="header.text">
              <v-container>
                <v-text-field
                  clearable
                  :hide-details="true"
                  v-if="header.filterable && !header.filterSpecial"
                  v-model="filters[header.value]"
                  :id="header.value"
                  @input="applyFilters(header.key, $event)"
                  outlined
                  dense
                  autocomplete="off"
                ></v-text-field>

                <v-select
                  v-if="
                    header.filterSpecial && header.value == 'branch.branch_name'
                  "
                  :hide-details="true"
                  @change="applyFilter()"
                  item-value="id"
                  item-text="branch_name"
                  v-model="filters[header.value]"
                  outlined
                  dense
                  :items="[
                    { branch_name: `All Branches`, id: `` },
                    ...branchesList,
                  ]"
                ></v-select>
                <v-select
                  v-if="header.filterSpecial && header.value == 'isOverTime'"
                  :hide-details="true"
                  @change="applyFilter()"
                  item-value="value"
                  item-text="title"
                  v-model="filters[header.value]"
                  outlined
                  dense
                  :items="[
                    { value: '', title: 'All' },
                    { value: '1', title: 'Yes' },
                    { value: '0', title: 'No' },
                  ]"
                ></v-select>

                <v-select
                  :id="header.key"
                  :hide-details="true"
                  v-if="header.filterSpecial && header.value == 'shift.name'"
                  outlined
                  dense
                  small
                  v-model="filters[header.filterName]"
                  item-text="name"
                  item-value="id"
                  :items="shifts_for_filter"
                  placeholder="Shift"
                  solo
                  flat
                  @change="applyFilters()"
                ></v-select>
                <v-select
                  :id="header.key"
                  :hide-details="true"
                  v-if="
                    header.filterSpecial && header.value == 'shift_type.name'
                  "
                  outlined
                  dense
                  small
                  v-model="filters[header.filterName]"
                  item-text="name"
                  item-value="id"
                  :items="shiftsTypes_for_filter"
                  placeholder="Shift"
                  solo
                  flat
                  @change="applyFilters()"
                ></v-select>

                <v-menu
                  v-if="header.filterSpecial && header.value == 'from_date'"
                  ref="from_menu_filter"
                  v-model="from_menu_filter"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      :hide-details="!from_date_filter"
                      outlined
                      dense
                      v-model="filters[header.value]"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      placeholder="Schedule Start Date"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    style="height: 350px"
                    v-model="filters[header.value]"
                    no-title
                    scrollable
                    @input="applyFilter()"
                  >
                    <v-spacer></v-spacer>

                    <v-btn
                      text
                      color="primary"
                      @click="
                        filters[header.value] = '';
                        from_menu_filter = false;
                        applyFilter();
                      "
                    >
                      Clear
                    </v-btn>
                  </v-date-picker>
                </v-menu>
                <v-menu
                  v-if="header.filterSpecial && header.value == 'to_date'"
                  ref="to_menu_filter"
                  v-model="to_menu_filter"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      :hide-details="!to_date_filter"
                      outlined
                      dense
                      v-model="filters[header.value]"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      placeholder="Schedule To Date"
                    ></v-text-field>
                  </template>

                  <v-date-picker
                    style="height: 350px"
                    v-model="filters[header.value]"
                    no-title
                    scrollable
                    @input="applyFilter()"
                  >
                    <v-spacer></v-spacer>
                    <v-btn
                      text
                      color="primary"
                      @click="
                        filters[header.value] = '';
                        to_menu_filter = false;
                        applyFilter();
                      "
                    >
                      Clear
                    </v-btn>
                  </v-date-picker>
                </v-menu>
              </v-container>
            </td>
          </tr>
        </template>

        <template v-slot:item.employee_id="{ item }">
          {{ caps(item?.employee?.employee_id || "") }}
        </template>
        <template v-slot:item.employee.first_name="{ item }">
          {{ caps(item.employee && item.employee.first_name) }}
          {{ caps(item.employee && item.employee.last_name) }}
        </template>

        <template v-slot:item.isOverTime="{ item }">
          <v-icon v-if="item && item.isOverTime" color="success darken-1"
            >mdi-check</v-icon
          >
          <v-icon v-else color="error">mdi-close</v-icon>
        </template>

        <template v-slot:item.action="{ item }">
          <v-menu bottom left>
            <template v-slot:activator="{ on, attrs }">
              <v-btn dark-2 icon v-bind="attrs" v-on="on">
                <v-icon>mdi-dots-vertical</v-icon>
              </v-btn>
            </template>
            <v-list width="120" dense>
              <v-list-item @click="ScheduleItem(item, 'view')">
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="secondary" small> mdi-eye </v-icon>
                  View
                </v-list-item-title>
              </v-list-item>
              <v-list-item @click="ScheduleItem(item, 'edit')">
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="secondary" small> mdi-pencil </v-icon>
                  Edit
                </v-list-item-title>
              </v-list-item>
              <v-list-item @click="deleteItem(item, 'edit')">
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="error" small> mdi-delete </v-icon>
                  Delete
                </v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </template>
      </v-data-table>
    </v-card>
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    branchesList: [],
    branch_id: null,
    shifts_for_filter: [],
    shiftsTypes_for_filter: [],

    from_date_filter: "",
    to_menu_filter: false,
    to_menu_filter: "",
    to_date_filter: "",

    from_menu_filter: false,
    from_date: "",

    totalRowsCount: 0,

    showFilters: false,
    filters: {},
    isFilter: false,
    datatable_search_textbox: "",
    datatable_searchById: "",
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",
    displayNoRecords: false,
    from_date: new Date().toJSON().slice(0, 10),
    from_menu: false,
    to_date: new Date().toJSON().slice(0, 10),
    to_menu: false,

    from_menu: [],
    to_menu: [],

    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    Module: "Employee Schedule",
    shift_types: [],
    manual_shift: {},
    options: {},
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

    headers_table: [
      {
        text: "Emp Id",
        align: "left",
        sortable: true,
        value: "employee_id",
        filterable: true,
        filterName: "employee_id",
      },
      {
        text: "Name",
        align: "left",
        sortable: true,
        value: "employee.first_name",
        filterable: true,
        filterName: "employee_first_name",
      },
      {
        text: "Branch",
        align: "left",
        sortable: true,
        value: "branch.branch_name",
        filterable: true,
        filterName: "branch_id",
        filterSpecial: true,
      },
      {
        text: "Current Schedule Name",
        align: "left",
        sortable: true,
        value: "shift.name",
        filterable: true,
        filterName: "shift_id",
        filterSpecial: true,
      },
      {
        text: "Schedule Start",
        align: "left",
        sortable: true,
        value: "from_date",
        filterable: true,
        filterName: "from_date",
        filterSpecial: true,
      },
      {
        text: "Schedule To Date",
        align: "left",
        sortable: true,
        value: "to_date",
        filterable: true,
        filterName: "to_date",
        filterSpecial: true,
      },
      {
        text: "OT",
        align: "left",
        sortable: true,
        value: "isOverTime",
        filterable: false,
        filterName: "isOverTime",
        filterSpecial: true,
      },

      // {
      //   text: "Shift Type",
      //   align: "left",
      //   sortable: true,
      //   value: "shift_type.name",
      //   filterable: true,
      //   filterName: "shift_type_id",
      //   filterSpecial: true,
      // },

      {
        text: "Actions",
        align: "center",
        value: "action",
        sortable: false,
        filterable: false,
        filterName: "",
      },
    ],
    department_ids: [],
    sub_department_ids: ["---"],
    employee_ids: [],
    shift_id: null,
    shift_type_id: "",
    isOverTime: false,
    is_edit: false,
    shift_slug: "",
    employees: [],
    employees_dialog: [],
    departments: [],
    sub_departments: [],
    shifts: [],
    ids: [],
    response: "",
    data: [],

    errors: [],
    headers_ids: [],
    headers_dialog: [
      {
        text: "E.ID",
        align: "left",
        sortable: false,
        value: "system_user_id",
      },
      {
        text: "Name",
        sortable: true,
        value: "employee.first_name",
      },
      {
        text: "Department",
        sortable: false,
        value: "department.name",
      },
    ],

    deleteIds: [],
    schedules_temp_list: [],
    empId: "",
  }),

  computed: {},

  watch: {
    dialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
      if (!this.is_edit) {
        this.getDepartments(this.options);
        this.getDataFromApiForDialog();
      }
      this.getShiftTypes(this.options);
    },
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
    options_dialog: {
      handler() {
        if (!this.is_edit) {
          this.getDataFromApiForDialog();
        }
      },
      deep: true,
    },
    search() {
      this.pagination.current = 1;
      this.searchIt();
    },
  },
  created() {
    this.loading = true;
    this.loading_dialog = true;
    this.getShifts();
    this.getDataFromApi();
    this.options = {
      params: {
        per_page: 1000,
        company_id: this.$auth.user.company_id,
      },
    };

    this.getShiftsForFilter();
    this.getbranchesList();
  },

  methods: {
    getbranchesList() {
      this.payloadOptions = {
        params: {
          company_id: this.$auth.user.company_id,

          branch_id: this.$auth.user.branch_id,
        },
      };

      this.$axios.get(`branches_list`, this.payloadOptions).then(({ data }) => {
        this.branchesList = data;
      });
    },
    applyFilter() {
      this.getDataFromApi();
      this.from_menu_filter = false;
      this.to_menu_filter = false;
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
      console.log(item);

      this.empId = item.employee_id;
      let id = item.employee_id;
      let options = {
        company_id: this.$auth.user.company_id,
      };

      this.empId = item.employee_id;

      this.$axios
        .get(`get_shifts_by_employee/${id}`, { params: options })
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
        employee_ids: [this.empId],
        schedules: this.schedules_temp_list,
        company_id: this.$auth.user.company_id,
        branch_id: this.schedules_temp_list[0].branch_id || 0,
      };

      this.process(this.$axios.post(`schedule_employees`, payload));
    },

    removeItem(i, item) {
      if (item.id) {
        this.deleteIds.push(item.id);
      }
      this.schedules_temp_list.splice(i, 1);
    },

    addRow(id) {
      let item = {
        shift_id: id,
        shift_type_id: 1,
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
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
          branch_id: this.branch_id,
        },
      };
      this.$axios.get("shift", options).then(({ data }) => {
        this.shifts = data.data.map((e) => ({
          shift_id: e.id,
          name: e.name,
          shift_type_id: e.shift_type_id,
          from_date: e.from_date,
          to_date: e.to_date,
        }));
      });
      // if (this.shift_type_id == 3) {
      //   this.shift_id = 0;
      //   this.shifts = [];
      //   return;
      // }

      // let options = {
      //   params: {
      //     shift_type_id: shift_type_id,
      //     company_id: this.$auth.user.company_id,
      //   },
      // };
      // this.$axios
      //   .get("shift_by_type", options)
      //   .then(({ data }) => {
      //     this.shifts = data;
      //   })
      //   .catch((err) => console.log(err));
    },
    getShiftsForFilter() {
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios
        .get("shift", options)
        .then(({ data }) => {
          this.shifts_for_filter = data.data;
          this.shifts_for_filter.unshift({ id: "", name: "All" });
          this.addRow(data[0].id);
        })
        .catch((err) => console.log(err));

      this.$axios
        .get("shift_type", options)
        .then(({ data }) => {
          this.shiftsTypes_for_filter = data;
          this.shiftsTypes_for_filter.unshift({ id: "", name: "All" });
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

    runShiftFunction(shift_type_id) {
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
          //department_ids: this.$auth.user.assignedDepartments,
          company_id: this.$auth.user.company_id,
          ...this.filters,
        },
      };

      //if (filter_value != "") options.params[filter_column] = filter_value;

      this.$axios.get(url, options).then(({ data }) => {
        // if (filter_column != "" && data.data.length == 0) {
        //   this.snack = true;
        //   this.snackColor = "error";
        //   this.snackText = "No Results Found";
        //   this.loading = false;
        //   return false;
        // }
        this.employees = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;

        if (this.employees.length == 0) {
          this.displayNoRecords = true;
        }

        this.totalRowsCount = data.total;
      });

      //this.loading = false;
    },

    getDataFromApiForDialog(url = this.endpoint_dialog) {
      this.loading_dialog = true;

      const { page, itemsPerPage } = this.options_dialog;

      let options = {
        params: {
          per_page: itemsPerPage,
          page: page,
          company_id: this.$auth.user.company_id,
        },
      };

      this.$axios.get(url, options).then(({ data }) => {
        this.employees_dialog = data.data;
        this.total_dialog = data.total;
        this.loading_dialog = false;
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

    dialogSearchIt(e) {
      if (e.length == 0) {
        this.getDataFromApiForDialog();
      } else if (e.length > 2) {
        this.employees_dialog = this.employees.filter(({ display_name: fn }) =>
          fn.includes(e)
        );
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
          .delete("schedule_employees/" + item.id)
          .then(({ data }) => {
            const index = this.employees.indexOf(item);
            this.employees.splice(index, 1);
            this.snackbar = data.status;
            this.response = data.message;
            this.getDataFromApiForDialog();
          })
          .catch((err) => console.log(err));
    },

    save() {
      this.loading_dialog = true;
      if (this.employee_ids && this.employee_ids.length == 0) {
        this.loading_dialog = false;
        alert("Atleast 1 Employee must be selected");
        return;
      }
      this.errors = [];

      let payload = {
        shift_type_id: this.shift_type_id,
        shift_id: this.shift_id,
        company_id: this.$auth.user.company_id,
        isOverTime: this.isOverTime ? 1 : 0,
        employee_ids: this.employee_ids.map((e) => e.system_user_id),

        from_date: this.from_date,
        to_date: this.to_date,
      };

      if (this.is_edit) {
        this.process(
          this.$axios.post(
            `schedule_employees/${payload.employee_ids}`,
            payload
          )
        );
      } else {
        this.process(this.$axios.post(`schedule_employees`, payload));
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
            this.loading_dialog = false;
            return;
          }
          this.response = data.message;
          this.snackbar = true;
          this.loading_dialog = false;
          this.editDialog = false;
          this.getDataFromApi();
          this.getDataFromApiForDialog();
        })
        .catch((err) => console.log(err));
    },
  },
};
</script>
