<template>
  <div v-if="can(`leave_group_access`)">
    <div class="text-center ma-2">
      <v-snackbar
        v-model="snackbar"
        top="top"
        :color="snackColor"
        elevation="24"
      >
        {{ response }}
      </v-snackbar>
    </div>

    <v-dialog v-model="dialog" width="500px">
      <v-card>
        <v-card-title dense class="popup_background">
          <span>{{ formTitle }} </span>
          <v-spacer></v-spacer>
          <v-icon @click="dialog = false" outlined dark>
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col v-if="isCompany" cols="12">
                <v-select
                  label="Employee"
                  v-model="editedItem.employees_table_id"
                  :items="employeesList"
                  dense
                  placeholder="Select Employee"
                  outlined
                  item-value="id"
                  item-text="name_with_user_id"
                  :error-messages="
                    errors && errors.employees_table_id
                      ? errors.employees_table_id[0]
                      : ''
                  "
                >
                </v-select>
              </v-col>

              <v-col cols="12">
                <v-select
                  label="Shift Start Hour"
                  v-model="editedItem.shift_start"
                  :items="hoursList"
                  dense
                  placeholder="Select Employee"
                  outlined
                  item-value="id"
                  item-text="value"
                  :error-messages="
                    errors && errors.shift_start ? errors.shift_start[0] : ''
                  "
                >
                </v-select>
              </v-col>
              <v-col cols="12">
                <v-select
                  label="Shift End Hour"
                  v-model="editedItem.shift_end"
                  :items="hoursList"
                  dense
                  placeholder="Select Employee"
                  outlined
                  item-value="id"
                  item-text="value"
                  :error-messages="
                    errors && errors.shift_end ? errors.shift_end[0] : ''
                  "
                >
                </v-select>
              </v-col>
            </v-row>

            <v-row v-for="(item, index) in leaveTypes" :key="index">
              <v-col cols="6">
                {{ item.name }}
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

          <v-btn class="primary" small @click="save">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-row>
      <v-col md="12">
        <!-- <Back color="primary" /> -->

        <v-card class="mb-5 mt-0 rounded-md" elevation="0">
          <v-toolbar class="rounded-md" dense flat>
            <!-- </template>
              <span>Reload</span>
            </v-tooltip> -->
            <v-spacer></v-spacer>
            <!-- <v-tooltip v-if="can(`leave_group_create`)" top color="primary">
              <template v-slot:activator="{ on, attrs }"> -->
            <v-btn
              dense
              class="ma-0 px-0"
              x-small
              :ripple="false"
              text
              title="Add LeaveGroup"
            >
              <v-icon class="ml-2" @click="createNew()" dark
                >mdi mdi-plus-circle</v-icon
              >
            </v-btn>
            <!-- </template>
              <span>New LeaveGroup</span>
            </v-tooltip> -->
          </v-toolbar>

          <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
            {{ snackText }}

            <template v-slot:action="{ attrs }">
              <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
            </template>
          </v-snackbar>
          <v-data-table
            v-if="can(`leave_group_view`)"
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
            <template v-slot:item.first_name="{ item }">
              {{ item.employee.first_name }} {{ item.employee.last_name }}
            </template>
            <template v-slot:item.start="{ item }">
              {{ displayHourName(item.shift_start) }}
            </template>
            <template v-slot:item.end="{ item }">
              {{ displayHourName(item.shift_end) }}
            </template>
            <template v-slot:item.action="{ item }">
              <v-menu bottom left>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn dark-2 icon v-bind="attrs" v-on="on">
                    <v-icon>mdi-dots-vertical</v-icon>
                  </v-btn>
                </template>
                <v-list width="120" dense>
                  <!-- <v-list-item @click="viewItem(item)">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon
                        v-if="can(`leave_group_view`)"
                        color="primary"
                        small
                        @click="viewItem(item)"
                      >
                        mdi-eye
                      </v-icon>
                      Add Count
                    </v-list-item-title>
                  </v-list-item> -->
                  <v-list-item @click="editItem(item)">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon
                        v-if="can(`leave_group_edit`)"
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
                        v-if="can(`leave_group_delete`)"
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
import {
  TiptapVuetify,
  Image,
  Heading,
  Bold,
  Italic,
  Strike,
  Underline,
  Code,
  Paragraph,
  BulletList,
  OrderedList,
  ListItem,
  Link,
  Blockquote,
  HardBreak,
  HorizontalRule,
  History,
} from "tiptap-vuetify";

export default {
  components: {
    TiptapVuetify,
  },
  props: ["branch_id"],
  data: () => ({
    employeesList: [],
    dialogEdit: false,
    attrs: {},
    leaveTypes: [],
    designations: [],
    hoursList: [
      0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
      21, 22, 23, 24,
    ],
    formTitle: "Branch Shift Manager - New",
    dialogEmployees: false,
    idsEmployeeList: [],
    //editor
    datatable_search_textbox: "",
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",
    extensions: [
      History,
      Blockquote,
      Link,
      Image,
      Underline,
      Strike,
      Italic,
      ListItem,
      BulletList,
      OrderedList,
      [
        Heading,
        {
          options: {
            levels: [1, 2, 3],
          },
        },
      ],
      Bold,
      Link,
      Code,
      HorizontalRule,
      Paragraph,
      HardBreak,
    ],
    // starting editor's content
    content: `
      <h1>Yay Headlines!</h1>
      <p>All these <strong>cool tags</strong> are working now.</p>
        `,

    //end editor
    scrollInvoked: 0,
    start_menu: false,
    end_menu: false,
    title: "",
    des: "",
    desDate: "",
    dept: "",
    options: {},
    Model: "Branch Shift Manager",
    endpoint: "branch-shift-managers",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    departments: [],
    loading: false,
    total: 0,

    headers: [
      {
        text: "Employee",
        align: "left",
        sortable: true,
        value: "first_name",
      },
      {
        text: "Shift Hour Start",
        align: "left",
        sortable: true,
        value: "start",
      },
      {
        text: "Shift Hour End",
        align: "left",
        sortable: true,
        key: "name",
        value: "end",
      },

      { text: "Actions", align: "center", value: "action", sortable: false },
    ],
    editedIndex: -1,
    editedItem: {
      //leave_type_id: "",
      group_name: "",
      //leave_type_count: "",
    },
    defaultItem: {
      //leave_type_id: "",
      group_name: "",
      //leave_type_count: "",
    },
    response: "",
    data: [],
    errors: [],
    options_dialog: {},
    employees_dialog: [],
    selectAllDepartment: false,
    selectAllEmployee: false,
    DialogEmployeesData: {},
    branchesList: [],

    isCompany: true,
  }),

  computed: {},

  watch: {},
  async created() {
    this.loading = true;

    if (this.$auth.user.branch_id) {
      this.branch_id = this.$auth.user.branch_id;
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

    this.getDataFromApi();
    this.getEmployeesList();

    let clockTimeArray = [];

    // Loop from 0 to 23 (representing 12AM to 11PM)
    for (let hour = 0; hour <= 23; hour++) {
      // Convert hour to clock time format
      let clockTime = hour % 12 === 0 ? 12 : hour % 12; // Convert 0 to 12 for 12AM
      let period = hour < 12 ? "AM" : "PM"; // Determine whether it's AM or PM
      // Add leading zero if hour is less than 10
      clockTime = clockTime < 10 ? "0" + clockTime : clockTime;
      // Combine hour and period and push it to the array
      //clockTimeArray.push(`${clockTime}:00 ${period}`);

      let timeObject = { id: hour, value: `${clockTime}:00 ${period}` };
      clockTimeArray.push(timeObject);
    }
    let timeObject = { id: 24, value: "12:00 AM" };
    clockTimeArray.push(timeObject);

    this.hoursList = clockTimeArray;
  },

  methods: {
    displayHourName(id) {
      let filterItem = this.hoursList.filter((item) => item.id == id);

      if (filterItem.length > 0) {
        return filterItem[0].value;
      }
      return "---";
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    async getLeaveTypes() {
      console.log("this.editedItem ", this.editedItem);
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
          branch_id: this.branch_id,
        },
      };
      await this.$axios.get(`leave_type`, options).then(({ data }) => {
        this.leaveTypes = data.data;
      });
    },

    getEmployeesList() {
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
          branch_id: this.branch_id,
        },
      };
      this.$axios.get(`employees-list-dropdown`, options).then(({ data }) => {
        this.employeesList = data;
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
          branch_id: this.branch_id,
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
        this.total = data.total;
        this.loading = false;
      });
    },
    async createNew() {
      this.getLeaveTypes();

      if (!this.isCompany) {
        this.branch_id = this.branch_id;
      }
      this.leaveTypes.forEach((element) => {
        element.leave_type_count = "";
      });
      this.dialog = true;
      this.error = [];
    },
    async editItem(item) {
      this.formTitle = "Edit Branch Shift Manager";
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);

      this.branch_id = item.branch_id;
      await this.getLeaveTypes();
      this.leaveTypes.forEach((element) => {
        element.leave_type_count = this.editedItem.leave_count.filter(
          (e) => e.leave_type_id == element.id
        );
        if (element.leave_type_count[0])
          element.leave_type_count =
            element.leave_type_count[0].leave_type_count;
      });

      this.dialog = true;
      this.error = [];
    },
    viewItem(item) {
      this.$router.push("/leavecount/" + item.id);
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
    save() {
      let options = {
        params: {
          branch_id: this.branch_id,
          company_id: this.$auth.user.company_id,
          employees_table_id: this.editedItem.employees_table_id,
          shift_start: this.editedItem.shift_start,
          shift_end: this.editedItem.shift_end,
        },
      };
      this.editedItem.company_id = this.$auth.user.company_id;

      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, options.params)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
              this.errors = data.errors;
              this.snackColor = "error";
              this.snackbar = true;
              this.response = data.message;
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
        let options = {
          params: {
            branch_id: this.branch_id,
            company_id: this.$auth.user.company_id,
            employees_table_id: this.editedItem.employees_table_id,
            shift_start: this.editedItem.shift_start,
            shift_end: this.editedItem.shift_end,
          },
        };
        this.$axios
          .post(this.endpoint, options.params)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
              //this.snackColor = "error";
              //this.snackbar = true;
              this.response = data.message;

              this.search = "";
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
