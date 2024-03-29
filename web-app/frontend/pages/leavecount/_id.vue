<template>
  <div v-if="can(`leavecount_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <!-- <v-row class="">
      <v-col cols="6">

      </v-col>
      <v-col cols="6">
        <div class="text-right">

          <v-btn small class="primary" to="/leavegroups">
            <v-icon small>mdi-arrow-left</v-icon>&nbsp;Back
          </v-btn>

        </div>
      </v-col>
    </v-row> -->
    <v-dialog persistent v-model="dialogEdit" width="500px">
      <v-card>
        <v-card-title dense class="popup_background">
          Update Group counts
          <v-spacer></v-spacer>
          <v-icon @click="dialogEdit = false" outlined dark>
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row v-for="item in items" :key="index">
              <v-col cols="6">
                {{ item.leave_type && item.leave_type.name }}
              </v-col>

              <v-col cols="6">
                <v-text-field
                  type="number"
                  outlined
                  dense
                  v-model="item.leave_type_count"
                  v-bind="attrs"
                >
                </v-text-field>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <!-- <v-btn class="error" small @click="close"> Cancel </v-btn> -->
          <v-btn class="primary" small @click="update">Update</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog persistent v-model="dialog" width="500px">
      <v-card>
        <v-card-title dense class="popup_background">
          {{ formTitle }}
          <v-spacer></v-spacer>
          <v-icon @click="dialog = false" outlined dark>
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <label for="" style="margin-bottom: 5px"
                  >Select leave Type</label
                >
                <v-autocomplete
                  :items="UpdatedLeaveTypes"
                  item-text="name"
                  item-value="id"
                  placeholder="Select Leave Type"
                  v-model="editedItem.leave_type_id"
                  :hide-details="!errors.leave_type_id"
                  :error="errors.leave_type_id"
                  :error-messages="
                    errors && errors.leave_type_id
                      ? errors.leave_type_id[0]
                      : ''
                  "
                  dense
                  outlined
                  :disabled="editedItem.id > -1"
                ></v-autocomplete>
              </v-col>

              <!-- <v-col cols="12">
                <label for="" style="margin-bottom:5px">Select Designation</label>
                <v-autocomplete :items="designations" item-text="name" item-value="id" placeholder="Select Designation  "
                  v-model="editedItem.group_id" :hide-details="!errors.group_id" :error="errors.group_id" :error-messages="errors && errors.group_id
                    ? errors.group_id[0]
                    : ''
                    " dense outlined></v-autocomplete>
              </v-col> -->

              <v-col cols="12">
                <label for="" style="margin-bottom: 5px">Leaves Count</label>
                <v-text-field
                  type="number"
                  outlined
                  dense
                  v-model="editedItem.leave_type_count"
                  v-bind="attrs"
                  :error-messages="
                    errors && errors.leave_type_count
                      ? errors.leave_type_count[0]
                      : ''
                  "
                >
                </v-text-field>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <!-- <v-btn class="error" small @click="close"> Cancel </v-btn> -->
          <v-btn class="primary" small @click="save">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-row>
      <v-col md="12">
        <v-card class="mb-5 rounded-md" elevation="0">
          <v-toolbar class="rounded-md" dense flat>
            <v-toolbar-title
              ><span> Leave Group - Count List</span></v-toolbar-title
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
            <v-spacer></v-spacer>
            <!-- <v-tooltip v-if="can(`leavecount_create`)" top color="primary">
              <template v-slot:activator="{ on, attrs }"> -->
            <v-btn
              dense
              class="ma-0 px-0"
              x-small
              :ripple="false"
              text
              title="Add Count"
            >
              <v-icon class="ml-2" @click="newDialog()" dark
                >mdi mdi-plus-circle</v-icon
              >
            </v-btn>
            <v-btn
              dense
              class="ma-0 px-0"
              x-small
              :ripple="false"
              text
              title="Add Count"
            >
              <v-icon class="ml-2" @click="dialogEdit = true" dark
                >mdi mdi-plus-circle</v-icon
              >
            </v-btn>
            <!-- </template>
              <span>Add </span>
            </v-tooltip> -->
          </v-toolbar>

          <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
            {{ snackText }}

            <template v-slot:action="{ attrs }">
              <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
            </template>
          </v-snackbar>
          <v-data-table
            v-if="can(`leavecount_view`)"
            v-model="ids"
            item-key="id"
            :headers="headers"
            :items="data"
            :loading="loading"
            :footer-props="{
              itemsPerPageOptions: [10, 50, 100, 500, 1000],
            }"
            class="elevation-1"
          >
            <template v-slot:item.leave_type.name="{ item }">
              {{ item.leave_type.name }}
            </template>
            <template v-slot:item.leave_type.short_name="{ item }">
              {{ item.leave_type.short_name }}
            </template>
            <template v-slot:item.leave_groups.group_name="{ item }">
              {{ item.leave_groups.group_name }}
            </template>
            <template v-slot:item.leave_type_count="{ item }">
              {{ item.leave_type_count }}
            </template>
            <template v-slot:item.action="{ item }">
              <v-menu bottom left>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn dark-2 icon v-bind="attrs" v-on="on">
                    <v-icon>mdi-dots-vertical</v-icon>
                  </v-btn>
                </template>
                <v-list width="120" dense>
                  <v-list-item @click="editItem(item)">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon
                        v-if="can(`leavecount_edit`)"
                        color="secondary"
                        small
                        @click="editItem(item)"
                      >
                        mdi-pencil
                      </v-icon>
                      Edit
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="deleteItem(item)">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon
                        v-if="can(`leavecount_delete`)"
                        color="error"
                        small
                        @click="deleteItem(item)"
                      >
                        {{
                          item.announcement === "customer" ? "" : "mdi-delete"
                        }}
                      </v-icon>
                      Delete
                    </v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </template>
            <template v-slot:no-data>
              <!-- <v-btn color="primary" @click="initialize">Reset</v-btn> -->
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </div>

  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    UpdatedLeaveTypes: [],
    attrs: {},
    leaveTypes: [],
    designations: [],
    formTitle: "New Leave Count",
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
    Model: "Leaves Count",
    endpoint: "leave_count",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    departments: [],
    loading: false,
    total: 0,

    headers: [
      {
        text: "Group Name",
        align: "left",
        sortable: true,
        value: "leave_groups.group_name",
      },
      {
        text: "Leave Type",
        align: "left",
        sortable: true,
        key: "name",
        value: "leave_type.name",
      },
      {
        text: "Short Name",
        align: "left",
        sortable: true,
        value: "leave_type.short_name",
      },

      {
        text: "Leaves   count",
        align: "left",
        sortable: true,
        value: "leave_type_count",
      },

      { text: "Actions", align: "center", value: "action", sortable: false },
    ],
    editedIndex: -1,
    editedItem: {
      leave_type_id: "",
      group_id: "",
      leave_type_count: "",
    },
    defaultItem: {
      leave_type_id: "",
      group_id: "",
      leave_type_count: "",
    },
    items: {},
    response: "",
    data: [],
    errors: [],
    options_dialog: {},
    employees_dialog: [],
    selectAllDepartment: false,
    selectAllEmployee: false,
    DialogEmployeesData: {},
    id: "",
    dialogEdit: false,
  }),

  computed: {},

  watch: {},
  created() {
    this.loading = true;
    this.id = this.$route.params.id;

    this.getDataFromApi();
    this.getDesignations();
  },

  methods: {
    newDialog() {
      this.getLeaveTypes();
      this.editedItem = {
        leave_type_id: "",
        group_id: "",
        leave_type_count: "",
      };
      this.formTitle = "New Leave Count";
      this.errors = [];
      this.dialog = true;
    },
    update_EdititemStart() {
      this.$refs.from_menu.save(this.editedItem.start_date);
      this.from_menu = false;
      this.getDayscount();
    },
    update_EdititemEnd() {
      this.$refs.end_menu.save(this.editedItem.end_date);
      this.end_menu = false;

      this.getDayscount();
    },
    getDayscount() {
      if (!this.editedItem.start_date || !this.editedItem.end_date) {
        return false;
      }
      let startDate = new Date(this.editedItem.start_date);
      let endDate = new Date(this.editedItem.end_date);

      this.editedItem.year = endDate.getFullYear();

      // Calculate the time difference in milliseconds
      let timeDiff = Math.abs(endDate.getTime() - startDate.getTime());

      // Convert the time difference to days
      let diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

      this.editedItem.total_days = diffDays + 1;
    },
    gotoDialogPage(item) {
      this.DialogEmployeesData = item.employees;
      this.dialogEmployees = true;
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

    getLeaveTypes() {
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
        },
      };

      this.UpdatedLeaveTypes = [];

      this.$axios.get(`leave_type`, options).then(({ data }) => {
        this.leaveTypes = data.data;
        // if (this.editedIndex <= -1) {

        this.leaveTypes.forEach((leavetype) => {
          let alreadyExist = false;
          this.data.forEach((group) => {
            if (group.leave_type_id == leavetype.id) {
              alreadyExist = true;
            }
          });

          if (!alreadyExist) this.UpdatedLeaveTypes.push(leavetype);
        });

        // this.leaveTypes = UpdatedLeaveTypes;

        // }
      });
    },
    getDesignations() {
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios.get(`designation`, options).then(({ data }) => {
        this.designations = data.data;
      });
    },
    employeesByDepartment() {
      this.loading_dialog = true;
      const { page, itemsPerPage } = this.options_dialog;

      let options = {
        params: {
          department_ids: this.editedItem.departments,
          per_page: itemsPerPage,
          page: page,
          company_id: this.$auth.user.company_id,
        },
      };

      if (!this.editedItem.departments.length) {
        this.getEmployees();
        return;
      }

      this.$axios.get("employeesByDepartment", options).then(({ data }) => {
        this.employees_dialog = data.data;
        this.loading_dialog = false;
      });
    },

    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      if (url == "") url = this.endpoint;
      this.loading = true;

      let endDate = new Date();

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
          group_id: this.$route.params.id,
          year: endDate.getFullYear(),
        },
      };
      if (filter_column != "") {
        options.params[filter_column] = filter_value;
      }

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        if (filter_column != "" && data.data.length == 0) {
          this.snack = true;
          this.snackColor = "error";
          this.snackText = "No Results Found";
          this.loading = false;
          return false;
        }
        this.data = data.data;
        this.items = this.data;
        this.total = data.total;
        this.loading = false;

        this.getLeaveTypes();
      });
    },
    searchIt(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
      }
    },
    newItem(item) {
      this.getLeaveTypes();
      this.formTitle = "Add leave Type";

      this.dialog = true;
      this.error = [];
      this.editedItem = {
        leave_type_id: "",
        group_id: "",
        leave_type_count: "",
      };
    },
    editItem(item) {
      this.formTitle = "Edit leave Type";
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
      this.error = [];

      this.UpdatedLeaveTypes = this.leaveTypes;
    },

    delteteSelectedRecords() {
      confirm(
        "Are you sure you wish to delete selected records , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .post(`${this.endpoint}/delete/selected`, {
            ids: this.ids.map((e) => e.id),
          })
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.snackbar = data.status;
              this.ids = [];
              this.response = "Selected records has been deleted";
            }
            this.getDataFromApi();
          })
          .catch((err) => console.log(err));
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

    getEmployees(url = "employee") {
      this.loading = true;

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
        },
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.employees_dialog = data.data;
      });
    },

    save() {
      let options = {
        params: {
          leave_type_id: this.editedItem.leave_type_id,
          company_id: this.$auth.user.company_id,
          group_id: this.$route.params.id,
          leave_type_count: this.editedItem.leave_type_count,
        },
      };
      this.editedItem.company_id = this.$auth.user.company_id;

      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, options.params)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              const index = this.data.findIndex(
                (item) => item.id == this.editedItem.id
              );
              // this.data.splice(index, 1, {
              //   id: this.editedItem.id,
              //   name: this.editedItem.name,
              // });
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
            }
          })
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, options.params)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
              this.errors = [];
              this.search = "";
            }
          })
          .catch((res) => console.log(res));
      }
    },
    update() {
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
          group_id: this.$route.params.id,
          items: this.items,
        },
      };

      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.items.group_id, options.params)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              const index = this.data.findIndex(
                (item) => item.id == this.editedItem.id
              );
              // this.data.splice(index, 1, {
              //   id: this.editedItem.id,
              //   name: this.editedItem.name,
              // });
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
            }
          })
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, options.params)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
              this.errors = [];
              this.search = "";
            }
          })
          .catch((res) => console.log(res));
      }
    },
  },
};
</script>
