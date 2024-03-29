<template>
  <v-card class="mb-5 rounded-md" elevation="0">
    <v-toolbar class="rounded-md" color="background" dense flat dark>
      <v-toolbar-title
        ><span> {{ Model }} List </span></v-toolbar-title
      >
      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            dense
            class="ma-0 px-0"
            x-small
            :ripple="false"
            text
            v-bind="attrs"
            v-on="on"
          >
            <v-icon color="white" class="ml-2" @click="getDataFromApi()" dark
              >mdi mdi-reload</v-icon
            >
          </v-btn>
        </template>
        <span>Reload</span>
      </v-tooltip>
      <v-spacer></v-spacer>
      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            to="/devicelogs"
            dense
            class="ma-0 px-0"
            x-small
            :ripple="false"
            text
            v-bind="attrs"
            v-on="on"
          >
            <v-icon color="white" class="ml-2" dark>mdi mdi-eye-outline</v-icon>
          </v-btn>
        </template>
        <span>Announcement List</span>
      </v-tooltip>
    </v-toolbar>
    <v-data-table
      item-key="id"
      :headers="headers"
      :items="data"
      :loading="loading"
      :options.sync="options"
      :footer-props="{
        itemsPerPageOptions: [10, 50, 100, 500, 1000],
      }"
      class="elevation-1"
      :server-items-length="total"
    >
      <template v-slot:item.UserID="{ item, index }">
        <v-row no-gutters>
          <v-col
            style="
              padding: 5px;
              padding-left: 0px;
              width: 50px;
              max-width: 50px;
            "
          >
            <v-img
              style="
                border-radius: 50%;
                height: auto;
                width: 30px;
                max-width: 30px;
              "
              :src="item.employee && item.employee.profile_picture"
              lazy-src="/no-profile-image.jpg"
            >
            </v-img>
          </v-col>
          <v-col style="padding: 10px">
            {{ item.employee ? item.employee.full_name : "---" }}
          </v-col>
        </v-row>
      </template>
      <template v-slot:no-data>
        <div class="no-data-container">No data available</div>
      </template>
    </v-data-table>
  </v-card>
</template>
<script>
export default {
  data: () => ({
    options: {},
    Model: "Recent Log",
    endpoint: "attendance_logs",
    search: "",
    loading: false,
    total: 0,

    headers: [
      {
        text: "Employee",
        align: "left",
        sortable: true,
        key: "UserID",
        value: "UserID",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Log Time",
        align: "left",
        sortable: true,
        key: "LogTime", //sorting
        value: "LogTime", //edit purpose
        filterable: true,
        filterSpecial: true,
      },
      {
        text: "Device Name",
        align: "left",
        sortable: true,
        key: "device",
        value: "device.name",
        filterable: true,
        filterSpecial: true,
      },
    ],

    response: "",
    data: [],
    options_dialog: {},
  }),

  watch: {
    selectAllDepartment(value) {
      if (value) {
        this.editedItem.departments = this.departments.map((e) => e.id);
        this.employeesByDepartment();
      } else {
        this.editedItem.departments = [];

        this.getEmployees();
      }
    },

    selectAllEmployee(value) {
      if (value) {
        this.editedItem.employees = this.employees_dialog.map((e) => e.id);
      } else {
        this.editedItem.employees = [];
      }
    },

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

    this.getDataFromApi();
  },

  methods: {
    datatable_cancel() {
      this.datatable_search_textbox = "";
    },
    datatable_open() {
      this.datatable_search_textbox = "";
    },
    datatable_close() {
      this.loading = false;
    },
    toggleEmployeeSelection() {
      this.selectAllEmployee = !this.selectAllEmployee;
    },
    applyFilters() {
      this.from_menu_filter = false;
      this.to_menu_filter = false;
      this.getDataFromApi();
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

      let options = {
        params: {
          per_page: 10,
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

        if (data.total == 0) {
          this.headers = [];
        }

        this.data = data.data;
        this.total = data.total;
        this.loading = false;
      });
    },
  },
};
</script>
