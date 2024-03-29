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
            to="/device"
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
      :server-items-length="data.total"
    >
      <template v-slot:item.status_id="{ item }">
        <v-chip
          small
          class="p-2 mx-1"
          :color="item.status.name !== 'active' ? 'error' : 'primary'"
        >
          {{ item.status.name !== "active" ? "offline" : "online" }}
        </v-chip>
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
    Model: "Devices",
    endpoint: "device",
    search: "",
    loading: false,
    total: 0,

    headers: [
      {
        text: "Short Name",
        align: "left",
        sortable: true,
        value: "short_name",
        filterable: true,
      },
      {
        text: "Location",
        align: "left",
        sortable: true,
        value: "location",
        filterable: true,
      },
      {
        text: "Status",
        align: "center",
        sortable: true,
        value: "status_id",
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

      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";
      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
          ...this.filters,
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

        if (data.total == 0) {
          this.headers = [];
        }
        this.data = data.data; //.filter(e => e.status.name !== "active");
        this.total = data.total;
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
  },
};
</script>
