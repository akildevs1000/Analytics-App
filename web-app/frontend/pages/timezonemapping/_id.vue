<template>
  <div v-if="can(`employee_access`)">
    <v-row class="mt-5 mb-5">
      <v-col cols="8">
        <h3>{{ Model }} : {{ timeZoneName }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>
      <!-- <v-col cols="4" class="text-right">
        <v-btn @click="goToBackpage()" small dark class="primary pt-4 pb-4"
          >Back
        </v-btn>
      </v-col> -->
      <v-col cols="6">
        <v-toolbar class="rounded-md" dense flat>
          <span> Devices List</span>
        </v-toolbar>
        <v-data-table
          dense
          :headers="devices_headers"
          :items="device_data"
          :loading="loading"
          :options.sync="options"
          :footer-props="{
            itemsPerPageOptions: [50, 100, 500, 1000],
          }"
          class="elevation-1"
        >
          <template v-slot:item.sno="{ item, index }">
            <b>{{ ++index }}</b>
          </template>
         
          <template v-slot:item.name="{ item }">
            {{ item.name }}
          </template>

          <template v-slot:item.device_id="{ item }">
            {{ item.device_id }}
          </template>
          <template v-slot:item.location="{ item }">
            {{ item.location }}
          </template>
        </v-data-table>
        <!-- <table id="devicesTables" class="display nowrap" style="width: 100%">
          <thead>
            <tr>
              <th># sno</th>
              <th>Device Name</th>
              <th>Device Location</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table> -->
      </v-col>

      <v-col cols="6">
        <v-toolbar class="rounded-md" dense flat>
          <span> Employees List</span>
        </v-toolbar>
        <v-data-table
          :headers="employee_headers"
          :items="employee_data"
          :loading="loading"
          :options.sync="options"
          :footer-props="{
            itemsPerPageOptions: [50, 100, 500, 1000],
          }"
          class="elevation-1"
        >
          <template v-slot:item.sno="{ item, index }">
            <b>{{ ++index }}</b>
          </template>
          <template v-slot:item.display_name="{ item }">
            {{ item.display_name }}
          </template>
          <template v-slot:item.employee_id="{ item }">
            {{ item.employee_id }}
          </template>
        </v-data-table>

        <!-- <table id="employeesTable" class="display nowrap" style="width: 100%">
          <thead>
            <tr>
              <th># sno</th>
              <th>Employee Name</th>
              <th>Emp Id</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table> -->
      </v-col>
    </v-row>
  </div>
</template>
<script>
import DataTable from "@andresouzaabreu/vue-data-table";

export default {
  components: {
    DataTable,
  },
  data() {
    return {
      loading: false,
      employee_data: [],
      options: [],
      name: "fahath",
      endpointUpdatetimezonelist: "employee_timezone_mapping",
      Model: "Timezone Mapping  ",
      response: "",
      tableData: [],
      tableColumns: [],
      Timezone: "",
      timeZoneName: "",
      devices_headers: [
        { text: "#", align: "left", sortable: false, key: "sno", value: "sno" },
        {
          text: "Device Name",
          align: "left",
          sortable: true,
          key: "name",
          value: "name",
        },
        {
          text: "Device Name",
          align: "left",
          sortable: true,
          key: "device_id",
          value: "device_id",
        },
        {
          text: "Device Location",
          align: "left",
          sortable: true,
          key: "location",
          value: "location",
        },
      ],
      employee_headers: [
        { text: "#", align: "left", sortable: false, key: "sno", value: "sno" },
        {
          text: "Employee Name",
          align: "left",
          sortable: true,
          key: "display_name",
          value: "display_name",
        },
        {
          text: "Employee ID",
          align: "left",
          sortable: true,
          key: "employee_id",
          value: "employee_id",
        },
      ],
    };
  },
  computed: {},

  created() {
    //this.getData();
    if (this.$auth.user.branch_id == null) {
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

      let company_branch_header = [
        {
          text: "Branch",
          align: "left",
          sortable: true,
          value: "company_branch.branch_name", //edit purpose
          width: "300px",
          filterable: true,
          filterSpecial: true,
        },
      ];
      this.devices_headers.splice(1, 0, ...company_branch_header);
      this.employee_headers.splice(1, 0, ...branch_header);
    }
  },
  mounted: function () {
    this.Timezone = this.$route.params.id;
    this.$nextTick(function () {
      var options = {
        params: {
          per_page: 1000, //this.pagination.per_page,
          company_id: this.$auth.user.company_id,
          id: this.$route.params.id,
          cols: ["id", "employee_id", "display_name"],
        },
      };
      let url =
        this.$axios.defaults.baseURL +
        "/employee_timezone_mapping/" +
        this.$route.params.id;
      this.$axios.get(`${url}`, options).then(({ data }) => {
        this.deviceTableContent(data.device_id);
        this.employeeTableContent(data.employee_id);

        this.timeZoneName = data.timezone.timezone_name;
      });
    });
  },
  methods: {
    goToBackpage() {
      this.$router.push("/timezonemapping/list");
    },

    deviceTableContent(ajaxData) {
      this.device_data = ajaxData;

      return false;
      // Code that will run only after the
      // entire view has been rendered
      var options = {
        params: {
          per_page: 1000, //this.pagination.per_page,
          company_id: this.$auth.user.company_id,
          id: this.$route.params.id,
          cols: ["id", "employee_id", "display_name"],
        },
      };
      let page = 1;
      // ${url}?page=${page}

      let url =
        this.$axios.defaults.baseURL +
        "/employee_timezone_mapping/" +
        this.$route.params.id;

      this.loading = true;

      $(document).ready(() => {
        //setTimeout(() => {

        var table = $("#devicesTables").DataTable({
          responsive: true,
          colReorder: true,
          paging: false,
          info: false,
          searching: false,
          // dom: "Bfrtip",
          buttons: [],
          order: [
            [0, "asc"], // colonna index1
            [1, "asc"], // colonna index2
          ],
          stateSave: true,
          data: ajaxData,
          // ajax: {
          //   url,
          //   data: options.params,
          //   dataSrc: "device_id",
          //   datatype: "json",
          // },
          columns: [
            {
              data: null,
              render: function (data, type, row, meta) {
                return meta.row + 1;
              },
            },

            //{ data: "timezone_id" },

            {
              data: null,
              render: function (data, type, row) {
                return row.location;
              },
            },
            {
              data: null,
              render: function (data, type, row) {
                return row.device_id;
              },
            },
          ],
        });

        $("a.toggle-vis").on("click", function (e) {
          e.preventDefault();

          // Get the column API object
          var column = table.column($(this).attr("data-column"));

          // Toggle the visibility
          column.visible(!column.visible());
        });
        // }, 1000 * 1);
      });
    },
    employeeTableContent(ajaxData) {
      this.employee_data = ajaxData;

      return false;

      // Code that will run only after the
      // entire view has been rendered
      var options = {
        params: {
          per_page: 1000, //this.pagination.per_page,
          company_id: this.$auth.user.company_id,
          id: this.$route.params.id,
          cols: ["id", "employee_id", "display_name"],
        },
      };
      let page = 1;
      // ${url}?page=${page}

      let url =
        this.$axios.defaults.baseURL +
        "/employee_timezone_mapping/" +
        this.$route.params.id;

      $(document).ready(() => {
        // setTimeout(() => {

        var table = $("#employeesTable").DataTable({
          responsive: true,
          colReorder: true,
          paging: false,
          searching: false,
          info: false,
          // dom: "Bfrtip",
          // buttons: [],

          order: [
            [0, "asc"], // colonna index1
            [1, "asc"], // colonna index2
          ],
          stateSave: true,
          data: ajaxData,
          // ajax: {
          //   url,
          //   data: options.params,
          //   dataSrc: "employee_id",
          //   datatype: "json",
          // },
          columns: [
            {
              data: null,
              render: function (data, type, row, meta) {
                return meta.row + 1;
              },
            },

            //{ data: "timezone_id" },
            {
              data: null,
              render: function (data, type, row) {
                return row.display_name;
              },
            },
            {
              data: null,
              render: function (data, type, row) {
                return row.employee_id;
              },
            },
          ],
        });

        $("a.toggle-vis").on("click", function (e) {
          e.preventDefault();

          // Get the column API object
          var column = table.column($(this).attr("data-column"));

          // Toggle the visibility
          column.visible(!column.visible());
        });
        // }, 1000 * 1);
      });
    },

    handleAction(actionName, data) {
      //window.alert("check out the console to see the logs");
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    
  },
};
</script>
