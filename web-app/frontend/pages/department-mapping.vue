<template>
  <div v-if="can(`announcement_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <DepartmentMappingCreate
      :isExisted="isExisted"
      :editedItem="editedItem"
      @submitted="getDataFromApi"
      :dialog="childDialogVisible"
      @close="closeChildDialog"
    />
    <v-dialog persistent v-model="dialogEmployees" max-width="60%">
      <v-card>
        <v-card-title dense class="popup_background">
          Employees List
          <v-spacer></v-spacer>
          <v-icon @click="dialogEmployees = false" outlined dark>
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-data-table
                v-model="idsEmployeeList"
                item-key="id"
                :headers="headers_Dialog"
                :items="DialogEmployeesData"
                :loading="loading"
                :footer-props="{
                  itemsPerPageOptions: [10, 50, 100, 500, 1000],
                }"
                class="elevation-1"
              >
                <template v-slot:item.first_name="{ item }">
                  {{ item.first_name ? item.first_name : "---" }}
                  {{ item.last_name }}
                </template>
                <template v-slot:item.employeeId="{ item }">
                  {{ item.employee_id }}
                </template>
                <!-- <template v-slot:item.department.name="{ item }">
                  {{ (item.department.name) }}
                </template>
                <template v-slot:item.designation.name="{ item }">
                  {{ (item.designation.name) }}
                </template> -->
              </v-data-table>
            </v-row>
          </v-container>
        </v-card-text>

        <!-- <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="error" small @click="close"> Cancel </v-btn>
          <v-btn class="primary" small @click="save">Save</v-btn>
        </v-card-actions> -->
      </v-card>
    </v-dialog>

    <v-row>
      <v-col md="12">
        <!-- <Back color="primary" /> -->

        <v-card class="mb-5 mt-2 rounded-md" elevation="0">
          <v-toolbar class="rounded-md" dense flat>
            <v-toolbar-title
              ><span> {{ Model }} </span></v-toolbar-title
            >
            <!-- <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }"> -->
            <v-btn
              dense
              class="ma-0 px-0"
              x-small
              :ripple="false"
              text
              title="Reload"
            >
              <v-icon class="ml-2" @click="getDataFromApi()" dark
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
              <v-icon @click="toggleFilter" class="mx-1 ml-2"
                >mdi mdi-filter</v-icon
              >
            </v-btn>
            <!-- </template>
              <span>Filter</span>
            </v-tooltip> -->
            <v-spacer></v-spacer>

            <!-- <v-tooltip top color="primary" v-if="can(`announcement_create`)">
              <template v-slot:activator="{ on, attrs }"> -->
            <v-btn
              dense
              class="ma-0 px-0"
              x-small
              :ripple="false"
              text
              title="Add Department Mapping"
              @click="openChildDialog"
            >
              <v-icon class="ml-2" dark>mdi mdi-plus-circle</v-icon>
            </v-btn>
            <!-- </template>
              <span>New Department Mapping</span>
            </v-tooltip> -->
          </v-toolbar>
          <!-- <v-text-field class=" form-control py-0 ma-1 mb-0 w-25 float-start custom-text-box floating
                  shadow-none" placeholder="Search..." solo flat @input="searchIt" v-model="search"
                  :hide-details="true"></v-text-field> -->
          <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
            {{ snackText }}

            <template v-slot:action="{ attrs }">
              <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
            </template>
          </v-snackbar>
          <v-data-table
            v-if="can(`announcement_view`)"
            v-model="ids"
            item-key="id"
            :headers="headers"
            :items="data"
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
                <td v-for="(header, index) in headers" :key="index">
                  <v-container>
                    <TextField
                      :header="header"
                      column="title"
                      @entered-value="handleFilter"
                    />
                    <DropDown
                      :disabled="true"
                      :header="header"
                      column="department"
                      @entered-value="handleFilter"
                      :items="departments"
                    />
                    <DropDown
                      :disabled="true"
                      :header="header"
                      column="employees"
                      @entered-value="handleFilter"
                      :items="employees"
                    />
                  </v-container>
                </td>
              </tr>
            </template>

            <template v-slot:item.action="{ item }">
              <v-menu bottom left>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn dark-2 icon v-bind="attrs" v-on="on">
                    <v-icon>mdi-dots-vertical</v-icon>
                  </v-btn>
                </template>
                <v-list width="120" dense>
                  <!-- <v-list-item @click="gotoDialogPage(item)">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="primary" small> mdi-view-list </v-icon>
                      View
                    </v-list-item-title>
                  </v-list-item> -->

                  <v-list-item @click="editItem(item)">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon
                        v-if="can(`announcement_edit`)"
                        color="secondary"
                        small
                      >
                        mdi-pencil
                      </v-icon>
                      Edit
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="deleteItem(item)">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon
                        v-if="can(`announcement_delete`)"
                        color="error"
                        small
                        @click="deleteItem(item)"
                        >mdi-delete
                      </v-icon>
                      Delete
                    </v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </template>

            <template v-slot:item.department.name.id="{ item }">
              <span
                v-for="(dep, index) in item.departments"
                :key="index"
                small
                class="  "
                color="primary"
              >
                {{ dep.name }} <br />
              </span>
            </template>
            <template v-slot:item.employees="{ item }">
              <span
                v-for="(emp, index) in item.employees"
                :key="index"
                small
                class="p-2 ma-1"
                color="primary"
              >
                {{ emp.first_name }} {{ emp.last_name }} -
                {{ emp.employee_id }}

                <br />
              </span>
              <v-chip
                small
                class="ma-1"
                style="color: black; margin-left: 10px !important"
                @click="gotoDialogPage(item)"
                v-if="item.employees.length > 4"
              >
                More...
              </v-chip>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </div>

  <NoAccess v-else />
</template>
<script>
import TextField from "../components/Snippets/Filters/TextField.vue";
import DropDown from "../components/Snippets/Filters/DropDown.vue";
import DepartmentMappingCreate from "../components/widgets/DepartmentMappingCreate.vue";
import Back from "../components/Snippets/Back.vue";

export default {
  components: {
    TextField,
    DropDown,
    DepartmentMappingCreate,
    Back,
  },
  data: () => ({
    childDialogVisible: false,
    totalRowsCount: 0,
    from_menu_filter: "",
    from_date_filter: "",
    to_date_filter: "",
    to_menu_filter: "",
    showFilters: false,
    filters: {},
    isFilter: false,
    options: {},
    dialogEmployees: false,
    idsEmployeeList: [],
    //editor
    datatable_search_textbox: "",
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",

    //end editor
    scrollInvoked: 0,
    start_menu: false,
    end_menu: false,
    title: "",
    des: "",
    desDate: "",
    dept: "",
    options: {},
    Model: "Department Mapping",
    endpoint: "assigned-department-employee",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    departments: [],
    loading: false,
    total: 0,
    headers_Dialog: [
      {
        text: "Name",
        align: "left",
        sortable: true,
        value: "first_name",
      },
      {
        text: "Employee Id",
        align: "left",
        sortable: true,
        value: "employee_id",
      },
    ],
    headers: [
      {
        width: "150px",
        text: "Title",
        align: "left",
        sortable: true,
        key: "title",
        value: "title",
        fieldType: "text",
      },
      {
        width: "200px",
        text: "Departments",
        align: "left",
        sortable: false,
        key: "department",
        value: "department.name.id",
        fieldType: "dropdown",
      },
      {
        width: "250px",
        text: "Employees",
        align: "left",
        sortable: false,
        key: "employees",
        value: "employees",
        fieldType: "dropdown",
      },
      {
        width: "50px",
        text: "Actions",
        align: "center",
        value: "action",
        sortable: false,
      },
    ],
    isExisted: false,
    editedItem: {
      title: "",
      departments: [],
      employees: [],
    },
    defaultItem: {
      title: "",
      departments: [],
      employees: [],
    },
    response: "",
    headerText: "",
    data: [],
    errors: [],
    options_dialog: {},
    employees_dialog: [],
    DialogEmployeesData: {},

    employees: [],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },

    isIndeterminateDepartment() {
      return (
        this.editedItem.departments.length > 0 &&
        this.editedItem.departments.length < this.departments.length
      );
    },
    isIndeterminateEmployee() {
      return (
        this.editedItem.employees.length > 0 &&
        this.editedItem.employees.length < this.employees_dialog.length
      );
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
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

    let options = {
      params: {
        per_page: 1000,
        company_id: this.$auth.user.company_id,
      },
    };

    this.getDataFromApi();
  },

  methods: {
    openChildDialog() {
      this.childDialogVisible = true;
    },
    closeChildDialog() {
      this.childDialogVisible = false;
    },
    handleFilter({ key, search_value }) {
      this.getDataFromApi(this.endpoint, key, search_value);
    },
    handleDatesFilter(dates) {
      if (dates.length > 1) {
        this.getDataFromApi(this.endpoint, "dates", dates);
      }
    },
    gotoDialogPage(item) {
      // console.log('item', item);
      this.DialogEmployeesData = item.employees;
      this.dialogEmployees = true;
    },
    datatable_cancel() {
      this.datatable_search_textbox = "";
    },
    datatable_open() {
      this.datatable_search_textbox = "";
    },
    datatable_close() {
      this.loading = false;
    },
    toggleDepartmentSelection() {
      this.selectAllDepartment = !this.selectAllDepartment;
    },
    toggleEmployeeSelection() {
      this.selectAllEmployee = !this.selectAllEmployee;
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    
    onScroll() {
      this.scrollInvoked++;
    },

    toggleFilter() {
      // this.filters = {};
      this.isFilter = !this.isFilter;
    },
    clearFilters() {
      this.filters = {};

      this.isFilter = false;
      this.getDataFromApi();
    },
    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      if (url == "") url = this.endpoint;
      this.loading = true;

      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let options = {
        params: {
          page,
          sortBy: sortBy ? sortBy[0] : "",
          sortDesc: sortDesc ? sortDesc[0] : "",
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
          ...this.filters,
        },
      };
      if (filter_column != "") {
        options.params[filter_column] = filter_value;
      }

      this.$axios.get(url, options).then(({ data }) => {
        if (filter_column != "" && data.data.length == 0) {
          this.snack = true;
          this.snackColor = "error";
          this.snackText = "No Results Found";
          this.loading = false;
          return false;
        }
        this.totalRowsCount = this.total = data.total;

        this.data = data.data;
        this.loading = false;
      });
    },
    searchIt(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
      }
    },
    editItem(item) {
      this.openChildDialog();
      this.editedItem.departments = item.departments.map((e) => e.id);
      this.editedItem.employees = item.employees.map((e) => e.id);
      this.editedItem.title = item.title;
      this.editedItem.id = item.id;
      this.isExisted = true;
    },
    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(this.endpoint + "/" + item.id)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
            }
          })
          .catch((err) => console.log(err));
    },
    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },
  },
};
</script>
