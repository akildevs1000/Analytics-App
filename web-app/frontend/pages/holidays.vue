<template>
  <div v-if="can(`holiday_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-dialog v-model="dialogFilter" width="300px">
      <v-card elevation="0">
        <v-toolbar class="popup_background" dense flat dark>
          <span> Select year</span>
          <v-spacer></v-spacer>
          <v-icon @click="dialogFilter = false" outlined dark>
            mdi mdi-close-circle
          </v-icon>
        </v-toolbar>

        <v-divider class="py-0 my-0"></v-divider>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col v-if="isCompany" cols="12">
                <v-select
                  @change="getDataFromApi()"
                  v-model="branch_id"
                  :items="[
                    { id: ``, branch_name: `Select All` },
                    ...branchesList,
                  ]"
                  dense
                  placeholder="Select Branch"
                  outlined
                  item-value="id"
                  item-text="branch_name"
                >
                </v-select>
                <v-select
                  @change="getDataFromApi()"
                  outlined
                  dense
                  x-small
                  v-model="filterYear"
                  :items="dataYears"
                  placeholder="Year"
                  solo
                  flat
                ></v-select>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="dialog" width="300px">
      <v-card>
        <v-card-title dense class="popup_background">
          <h5>{{ formTitle }}</h5>
          <v-spacer></v-spacer>
          <v-icon @click="dialog = false" outlined dark>
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col v-if="isCompany" cols="12">
                <label for="" style="padding-bottom: 5px">Branches</label>
                <v-select
                  v-model="editedItem.branch_id"
                  :items="branchesList"
                  dense
                  placeholder="Select Branch"
                  outlined
                  item-value="id"
                  item-text="branch_name"
                  hide-details
                  :error-messages="
                    errors && errors.branch_id ? errors.branch_id[0] : ''
                  "
                >
                </v-select>
              </v-col>

              <v-col cols="12">
                <label for="" style="padding-bottom: 5px">Title</label>
                <v-text-field
                  dense
                  outlined
                  v-model="editedItem.name"
                  placeholder="Title"
                  hide-details
                  :error-messages="errors && errors.name ? errors.name[0] : ''"
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                <label for="" style="padding-bottom: 5px">Date</label>
                <CustomFilter
                  style="float: right; width: 100%; padding-left: 13px"
                  @filter-attr="filterAttr"
                  :default_date_from="editedItem.start_date"
                  :default_date_to="editedItem.end_date"
                  :defaultFilterType="1"
                  :height="'45px '"
                  :width="'228px'"
                  :key="editedItem.start_date + editedItem.end_date"
                />
              </v-col>
              <v-col cols="12">
                <label for="">Today Days : {{ editedItem.total_days }}</label>
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
        <!-- <Back color="primary" /> -->

        <v-card class="mb-5 mt-2 rounded-md" elevation="0">
          <v-toolbar class="rounded-md" dense flat>
            <v-toolbar-title
              ><span> {{ Model }} </span></v-toolbar-title
            >
            <span>
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
            </span>
            <span>
              <v-btn
                dense
                class="ma-0 px-0"
                x-small
                :ripple="false"
                text
                title="Select Year"
              >
                <v-icon class="ml-2" @click="dialogFilter = true" dark
                  >mdi mdi-filter</v-icon
                >
                <!-- {{ filterYear }} -->
              </v-btn>
            </span>

            <v-spacer></v-spacer>
            <span>
              <v-btn
                x-small
                :ripple="false"
                text
                title="Add Holiday"
                @click="addItem()"
              >
                <v-icon dark>mdi-plus-circle</v-icon>
              </v-btn>
            </span>
          </v-toolbar>

          <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
            {{ snackText }}

            <template v-slot:action="{ attrs }">
              <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
            </template>
          </v-snackbar>
          <v-data-table
            v-if="can(`holiday_view`)"
            v-model="ids"
            item-key="id"
            :headers="headers"
            :items="data"
            :loading="loading"
            :footer-props="{
              itemsPerPageOptions: [10, 50, 100, 500, 1000],
            }"
            class="elevation-1"
            :options.sync="options"
            :server-items-length="totalRowsCount"
          >
            <template v-slot:item.name="{ item }">
              {{ item.name }}
            </template>
            <template v-slot:item.start_date="{ item }">
              {{ $dateFormat.format1(item.start_date) }}
            </template>
            <template v-slot:item.end_date="{ item }">
              {{ $dateFormat.format1(item.end_date) }}
            </template>
            <template v-slot:item.total_days="{ item }">
              {{ item.total_days }}
            </template>
            <template v-slot:item.action="{ item }">
              <v-menu bottom left>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn dark-2 icon v-bind="attrs" v-on="on">
                    <v-icon>mdi-dots-vertical</v-icon>
                  </v-btn>
                </template>
                <v-list
                  width="120"
                  dense
                  v-if="can(`holiday_edit`) || can(`holiday_delete`)"
                >
                  <v-list-item
                    @click="editItem(item)"
                    v-if="can(`holiday_edit`)"
                  >
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="secondary" small @click="editItem(item)">
                        mdi-pencil
                      </v-icon>
                      Edit
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item
                    @click="deleteItem(item)"
                    v-if="can(`holiday_delete`)"
                  >
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="error" small @click="deleteItem(item)">
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
    dialogFilter: false,
    options: {},
    totalRowsCount: 0,
    formTitle: "New Holiday  ",
    dialogEmployees: false,
    idsEmployeeList: [],
    //editor
    datatable_search_textbox: "",
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",

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
    Model: "Holidays",
    endpoint: "holidays",
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
      // {
      //   text: "Department ",
      //   align: "left",
      //   sortable: true,
      //   value: "department.name",
      // },
      // {
      //   text: "Designation",
      //   align: "left",
      //   sortable: true,
      //   value: "designation.name",
      // },
    ],
    headers: [
      {
        text: "Name",
        align: "left",
        sortable: true,
        key: "name",
        value: "name",
      },
      {
        text: "Start Date",
        align: "left",
        sortable: true,
        value: "start_date",
      },
      {
        text: "End Date",
        align: "left",
        sortable: true,
        value: "end_date",
      },
      {
        text: "Total Days",
        align: "left",
        sortable: true,
        value: "total_days",
      },

      { text: "Actions", align: "center", value: "action", sortable: false },
    ],
    editedIndex: -1,
    editedItem: {
      name: "",
      total_days: "0",
      start_date: null,
      end_date: null,
      year: null,
    },
    defaultItem: {
      name: "",
      total_days: "",
      start_date: null,
      end_date: null,
      year: null,
    },
    response: "",
    data: [],
    errors: [],
    options_dialog: {},
    employees_dialog: [],
    selectAllDepartment: false,
    selectAllEmployee: false,
    DialogEmployeesData: {},
    dataYears: [],
    filterYear: "",
    branchesList: [],
    branch_id: "",
    isCompany: true,
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
  async created() {
    this.loading = true;

    if (this.$auth.user.branch_id) {
      this.branch_id = this.$auth.user.branch_id;
      this.isCompany = false;
      return;
    }

    let branch_header = [
      {
        text: "Branch",
        align: "left",
        sortable: true,
        key: "branch_id", //sorting
        value: "branch.branch_name", //edit purpose
        width: "300px",
        filterable: true,
        filterSpecial: true,
      },
    ];
    this.headers.splice(0, 0, ...branch_header);

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

    let endDate = new Date();
    this.filterYear = endDate.getFullYear();

    this.getDataFromApi();
    this.lastTenYears();
  },

  methods: {
    filterAttr(data) {
      this.editedItem.start_date = data.from;
      this.editedItem.end_date = data.to;
      this.filterType = "Monthly"; // data.type;
      this.getDayscount();
    },
    lastTenYears() {
      const year = new Date().getFullYear();
      this.dataYears = Array.from({ length: 10 }, (_, i) => year - i);
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
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      if (url == "") url = this.endpoint;
      this.loading = true;

      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";

      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
          year: this.filterYear,
          branch_id: this.branch_id,
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

        this.totalRowsCount = data.total;
        this.dialogFilter = false;
      });
    },
    addItem() {
      this.editedItem = {
        name: "",
        total_days: "0",
        start_date: null,
        end_date: null,
        year: null,
      };
      if (!this.isCompany) {
        this.editedItem.branch_id = this.branch_id;
      }
      this.dialog = true;
    },
    editItem(item) {
      this.formTitle = "Edit Holidays  ";
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
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
      this.editedItem.company_id = this.$auth.user.company_id;

      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, this.editedItem)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              const index = this.data.findIndex(
                (item) => item.id == this.editedItem.id
              );
              this.data.splice(index, 1, {
                id: this.editedItem.id,
                name: this.editedItem.name,
              });
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
            }
          })
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, this.editedItem)
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
