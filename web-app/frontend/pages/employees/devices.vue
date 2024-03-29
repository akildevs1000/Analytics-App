<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" small top="top" :color="color">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row class=" ">
      <v-col cols="12">
        <v-card>
          <v-toolbar class="rounded-md" color="popup_background" dense flat>
            <v-col cols="12">
              <v-toolbar-title><span>Device(s)</span></v-toolbar-title>
            </v-col>
          </v-toolbar>

          <v-data-table
            dense
            :headers="headers"
            :items="data"
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
                    (cumulativeIndex + data.indexOf(item))
                  : "-"
              }}
            </template>

            <template v-slot:item.devices="{ item }">
              <div
                v-for="(subitem, index2) in item.device_id"
                :key="'b' + index2"
              >
                <div>
                  {{ subitem.name }} <span style="color: red">in</span>
                  {{ subitem.location }}
                </div>
              </div>
            </template>
          </v-data-table>
        </v-card>

        <!-- <v-card
          v-for="(device, index) in data"
          class="mx-auto mb-5"
          :key="'access' + index"
        >
          <v-card-text>
            <v-row>
              <v-col cols="12" class="text-left">
                <div style="font-weight: bold">
                  {{ device.timezone.timezone_name }}
                </div>
              </v-col>

              <v-col md="10" sm="10" xs="10" cols="12">
                <table style="width: 100%">
                  <tr
                    v-for="(subitem, index2) in device.device_id"
                    :key="'b' + index2"
                  >
                    <td>
                      <v-col cols="12" class="pa-0"
                        >{{ ++index2 }} : {{ caps(subitem.name) }} in
                        {{ caps(subitem.location) }}</v-col
                      >
                    </td>
                  </tr>
                </table>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card> -->
      </v-col>
    </v-row>
  </div>
</template>
<script>
//var nuxtThisobject.$router;
//var nuxtThisobject.$axios;
var nuxtThisobject;
// import DataTable from "@andresouzaabreu/vue-data-table";
var datatableobject;

export default {
  data(vm) {
    return {
      cumulativeIndex: 1,
      perPage: 10,
      currentPage: 1,

      showFilters: false,
      filters: {},
      isFilter: false,
      totalRowsCount: 10,
      filter_employeeid: "",
      snack: false,
      snackColor: "",
      snackText: "",
      datatable_search_textbox: "",
      total: 0,
      options: {},
      data: [],
      name: "",
      endpoint: "gettimezonesinfo",
      Model: "Timezone Mapping List ",
      response: "",
      tableData: [],
      tableColumns: [],
      loading: false,
      snackbar: false,
      color: "black",
      pagination: {
        current: 1,
        total: 0,
        per_page: 10,
      },
      headers: [
        {
          text: "#",
          align: "left",
          sortable: false,
          value: "sno",
          align: "start",
          key: "sno",
          value: "sno",
        },
        {
          text: "Ttile",
          align: "left",
          sortable: false,
          align: "start",
          key: "timezoneName",
          filterable: true,
          filterSpecial: false,
          value: "timezone.timezone_name",
        },

        {
          text: "Devices",
          align: "left",
          sortable: false,
          value: "devices",
          filterable: false,
          filterSpecial: false,
          key: "device",
          placeHolder: "Type Device Name",
        },
      ],
      branchesList: [],
      branch_id: "",
    };
  },
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  // computed: {
  //   data: {
  //     get() {
  //       return this.data
  //     },
  //     set(val) {
  //       this.$emit('update:usersProp', val)
  //     }
  //   }
  // },
  created() {
    //this.getData();
    this.loading = true;

    // if (this.$auth.user.branch_id == null) {
    //   let branch_header = [
    //     {
    //       text: "Branch",
    //       align: "left",
    //       sortable: true,
    //       key: "branch_id", //sorting
    //       value: "branch.branch_name", //edit purpose
    //       width: "300px",
    //       filterable: true,
    //       filterSpecial: true,
    //     },
    //   ];
    //   this.headers.splice(1, 0, ...branch_header);

    //   this.$axios
    //     .get(`branches_list`, {
    //       params: {
    //         per_page: 100,
    //         company_id: this.$auth.user.company_id,
    //       },
    //     })
    //     .then(({ data }) => {
    //       this.branchesList = data;
    //       this.branch_id = this.$auth.user.branch_id || "";
    //     });
    // }
  },
  mounted: function () {
    this.getDataFromApi();
    // this.$nextTick(function () {
    //   // this.snackbar = true;
    //   // this.response = "Data loading...Please wait ";
    //   this.firstCall();

    //   setTimeout(() => {
    //     this.loading = false;
    //   }, 1000 * 2);
    // });
  },
  methods: {
    applyFilters() {
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
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    getDeviceslist(devicesList) {
      let deviceNames = "";
      devicesList.forEach((data1) => {
        deviceNames =
          deviceNames + data1.location + ": " + data1.name + " <br />";
      });

      return deviceNames;
    },
    getEmployeelist(employeesList) {
      let employeeNames = "";
      employeesList.forEach((data1) => {
        employeeNames =
          employeeNames +
          data1.display_name +
          ": " +
          data1.employee_id +
          "<br />";
      });

      return employeeNames;
    },
    displayView(rowId) {
      this.$router.push("/timezonemapping/" + rowId);
    },
    displayEdit(rowId) {
      this.$router.push("/timezonemapping/edit?id=" + rowId);
    },
    deleteItem(rowId, timezone_id) {
      let url = this.$axios.defaults.baseURL + "/deletetimezone";
      let options = {
        timezone_id: timezone_id,
        id: rowId,
        company_id: this.$auth.user.company_id,
      };

      confirm("Are you sure you want to delete this item?") &&
        this.$axios.post(`${url}`, options).then(({ data }) => {
          this.getDataFromApi();
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
          }
        });
    },
    getDataFromApi(url = this.endpoint, additional_params) {
      this.data = [];
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
          branch_id: this.branch_id,
          employee_id: this.$auth.user.employee.employee_id,

          cols: ["id", "employee_id", "display_name"],
          ...this.filters,
        },
      };
      if (additional_params != "")
        options.params["additional_params"] = additional_params;
      this.loading = true;
      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        // if (additional_params != '' && data.data.length == 0) {
        //   this.snack = true;
        //   this.snackColor = 'error';
        //   this.snackText = 'No Results Found';
        //   this.loading = false;
        //   return false;
        // }
        this.data = data.data;
        this.itemsPerPage = this.data.length;

        this.totalRowsCount = data.total;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        // this.pagination.current = data.current_page;
        // this.pagination.total = data.last_page;
        this.loading = false;
      });
    },

    goToCreatePage() {
      this.$router.push("/timezonemapping/new");
    },
    handleAction(actionName, data) {
      //window.alert("check out the console to see the logs");
    },

    // goToViewPage() {
    //   this.$router.push("/timezonemapping/view");
    // },
    // goToEditPage() {
    //   this.$router.push("/timezonemapping/edit");
    // },

    // goToDeletePage() {
    //   this.$router.push("/timezonemapping/delete");
    // },
  },
};
</script>
