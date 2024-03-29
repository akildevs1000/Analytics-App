<template>
  <div>
    <div>
      <v-toolbar flat>
        <h5>
          <b> Employees Data(Recent Logs) </b>
        </h5>
        <v-spacer />
      </v-toolbar>
      <v-row>
        <v-col>
          <v-card class="mb-5" elevation="0">
            <v-toolbar class="rounded-md" color="background" dense flat dark>
              <v-toolbar-title><span> Recent Logs</span></v-toolbar-title>
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
                    <v-icon
                      color="white"
                      class="ml-2"
                      @click="getRecords()"
                      dark
                      >mdi mdi-reload</v-icon
                    >
                  </v-btn>
                </template>
                <span>Reload</span>
              </v-tooltip>
              <!-- <v-tooltip top color="primary">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn dense class="ma-0 px-0" x-small :ripple="false" text v-bind="attrs" v-on="on">
                    <v-icon color="white" class="ml-2" @click="toggleFilter" dark>mdi
                      mdi-filter</v-icon>
                  </v-btn>
                </template>
                <span>Filter</span>
              </v-tooltip> -->
            </v-toolbar>
            <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
              {{ snackText }}

              <template v-slot:action="{ attrs }">
                <v-btn v-bind="attrs" text @click="snack = false">
                  Close
                </v-btn>
              </template>
            </v-snackbar>

            <v-data-table
              dense
              :headers="headers_table"
              :items="logs"
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
                  <td
                    v-for="header in headers"
                    :key="header.text"
                    class="table-search-header"
                  >
                    <v-text-field
                      style="margin-left: 10px; width: 90% !important"
                      v-if="header.filterable"
                      autocomplete="off"
                      v-model="filters[header.value]"
                      id="header.value"
                      @input="applyFilters(header.value, $event)"
                      outlined
                      height="10px"
                      clearable
                    ></v-text-field>
                  </td>
                </tr>
              </template>

              <template v-slot:item.employee.first_name="{ item, index }">
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
                        width: 50px;
                        max-width: 50px;
                      "
                      :src="
                        item.employee && item.employee.profile_picture
                          ? item.employee.profile_picture
                          : '/no-profile-image.jpg'
                      "
                    >
                    </v-img>
                  </v-col>
                  <v-col style="padding: 10px">
                    <strong>
                      {{ item.employee ? item.employee.first_name : "---" }}
                      {{
                        item.employee ? item.employee.last_name : "---"
                      }}</strong
                    >
                    <div>
                      {{
                        item.employee && item.employee.designation
                          ? caps(item.employee.designation.name)
                          : "---"
                      }}
                    </div>
                  </v-col>
                </v-row>
              </template>
              <template v-slot:item.employee.department="{ item }">
                <strong>{{
                  item.employee && item.employee.department
                    ? caps(item.employee.department.name)
                    : "---"
                }}</strong>
                <div>
                  {{
                    item.employee && item.employee.sub_department
                      ? caps(item.employee.sub_department.name)
                      : "---"
                  }}
                </div>
              </template>

              <template v-slot:item.UserID="{ item }">
                {{ item.UserID }}
              </template>
              <template v-slot:item.employee.employee_id="{ item }">
                {{ item.employee && item.employee.employee_id }}
              </template>
              <template v-slot:item.LogTime="{ item }">
                {{ item.LogTime }}
              </template>
              <template v-slot:item.device.device_name="{ item }">
                {{ item.device ? item.device.name : "---" }}
              </template>
              <!-- <template v-slot:item.device.device_id="{ item }">
                {{ item.DeviceID }}
              </template> -->
              <template v-slot:item.device.location="{ item }">
                {{ item.device ? item.device.location : "---" }}
              </template>
            </v-data-table>
            <!-- <v-data-table dense :headers="headers_table" :items="logs" model-value="data.id" :loading="loading"
                :options.sync="options" :footer-props="{
                  itemsPerPageOptions: [10, 50, 100, 500, 1000],
                }" class="elevation-1" :server-items-length="totalRowsCount">
                <template v-slot:header="{ props: { headers } }">
                  <tr v-if="isFilter">
                    <td v-for="header in  headers " :key="header.text">
                      <v-text-field :hide-details="true" v-if="header.filterable" v-model="filters[header.value]"
                        id="header.value" @input="applyFilters(header.value, $event)" outlined dense
                        autocomplete="off"></v-text-field>


                    </td>
                  </tr>
                </template>
                <template v-slot:item.sno="{ item, index }">

                  <b>{{ ++index }}</b>
                </template>
                <template v-slot:item.profilepic="{ item }">
                  <v-img class="gg" viewBox="0 0 100 100" style="border-radius: 50%;   max-width: 50px !important" :src="(item.employee && item.employee.profile_picture) ||
                    '/no-profile-image.jpg'
                    "></v-img>

                </template>

                <template v-slot:item.employee.first_name="{ item }">
                  <v-edit-dialog large save-text="Reset" cancel-text="Ok" style="margin-left: 4%;" @cancel="getRecords()"
                    @save="getRecords()">
                    {{ item.employee.first_name }} {{ item.employee.last_name }}
                    <template v-slot:input>
                      <v-text-field v-model="datatable_search_textbox" @input="getRecords('search_employee_name', $event)"
                        label="Search Employee Name"></v-text-field>
                    </template>
                  </v-edit-dialog>
                </template>
                <template v-slot:item.UserID="{ item }">
                  <v-edit-dialog large save-text="Reset" cancel-text="Ok" style="margin-left: 4%;" @cancel="getRecords()"
                    @save="getRecords()" @open="datatable_open">
                    {{ item.UserID }}
                    <template v-slot:input>
                      <v-text-field v-model="datatable_search_textbox"
                        @input="getRecords('search_system_user_id', $event)" label="Search System User Id"></v-text-field>
                    </template>
                  </v-edit-dialog>
                </template>
                <template v-slot:item.employee.employee_id="{ item }">
                  <v-edit-dialog large save-text="Reset" cancel-text="Ok" style="margin-left: 4%;" @cancel="getRecords()"
                    @save="getRecords()" @open="datatable_open">
                    {{ item.employee.employee_id }}
                    <template v-slot:input>
                      <v-text-field v-model="datatable_search_textbox" @input="getRecords('search_employee_id', $event)"
                        label="Search Employee ID"></v-text-field>
                    </template>
                  </v-edit-dialog>
                </template>
                <template v-slot:item.time="{ item }">
                  <v-edit-dialog large save-text="Reset" cancel-text="Ok" style="margin-left: 4%;" @cancel="getRecords()"
                    @save="getRecords()" @open="datatable_open">
                    {{ item.time }}
                    <template v-slot:input>
                      <v-text-field v-model="datatable_search_textbox" label="Search Time"
                        @input="getRecords('search_time', $event)"></v-text-field>
                    </template>
                  </v-edit-dialog>
                </template>
                <template v-slot:item.device.device_name="{ item }">
                  <v-edit-dialog large save-text="Reset" cancel-text="Ok" style="margin-left: 4%;" @cancel="getRecords()"
                    @save="getRecords()" @open="datatable_open">
                    {{ item.device ? item.device.device_name : '---' }}
                    <template v-slot:input>
                      <v-text-field v-model="datatable_search_textbox" @input="getRecords('search_device_name', $event)"
                        label="Search Device Name"></v-text-field>
                    </template>
                  </v-edit-dialog>
                </template>
                <template v-slot:item.device.device_id="{ item }">
                  <v-edit-dialog large save-text="Reset" cancel-text="Ok" style="margin-left: 4%;" @cancel="getRecords()"
                    @save="getRecords()" @open="datatable_open">
                    {{ item.device ? item.device.device_id : '---' }}
                    <template v-slot:input>
                      <v-text-field v-model="datatable_search_textbox" @input="getRecords('search_device_id', $event)"
                        label="Search Device ID"></v-text-field>
                    </template>
                  </v-edit-dialog>
                </template>
              </v-data-table> -->
          </v-card>
        </v-col>
      </v-row>
    </div>
  </div>
</template>

<script>
export default {
  data: () => ({
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
    Model: "Device",
    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    options: {},
    endpoint: "device",
    search: "",
    snackbar: false,
    dialog: false,
    data: [],
    loading: false,
    total: 0,
    headers: [
      { text: "&nbsp #" },
      { text: "Pic" },
      { text: "Name" },
      { text: "E.id" },
      { text: "Last Active" },
      { text: "Device name" },
    ],
    editedIndex: -1,
    response: "",
    errors: [],

    number_of_records: 20,
    logs: [],
    url: process.env.SOCKET_ENDPOINT,
    socket: null,
    headers_table: [
      {
        text: "Employee Name",
        align: "left",
        sortable: true,
        filterable: true,

        value: "employee.first_name",
      },
      {
        text: "Department",
        align: "left",
        sortable: false,
        filterable: true,

        value: "employee.department",
      },
      {
        text: "E.ID",
        align: "left",
        sortable: true,
        filterable: true,

        value: "UserID",
      },
      {
        text: "Employee   Id",
        align: "left",
        sortable: true,
        filterable: true,

        value: "employee.employee_id",
      },
      {
        text: "Time",
        align: "left",
        sortable: true,
        filterable: true,

        value: "LogTime", //edit purpose
      },
      {
        text: "Device Name",
        align: "left",
        sortable: true,
        filterable: true,

        value: "device.device_name",
      },
      {
        text: "Location",
        align: "left",
        sortable: true,
        filterable: true,

        value: "device.location",
      },
      // {
      //   text: "Device ID",
      //   align: "left",
      //   sortable: true,
      //   filterable: true,

      //   value: "device.device_id",
      // },
    ],
  }),
  // data() {
  //   return {
  //     number_of_records: 10,
  //     logs: [],
  //     url: process.env.SOCKET_ENDPOINT,
  //     socket: null
  //   };
  // },
  watch: {
    options: {
      handler() {
        this.getRecords();
      },
      deep: true,
    },
  },
  mounted() {
    this.socketConnection();

    //this.getRecords();
  },
  created() {
    // this.getRecords();
  },
  methods: {
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    toggleFilter() {
      this.isFilter = !this.isFilter;
    },
    applyFilters() {
      this.getRecords();
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
    getRecords(filter_column = "", filter_value = "") {
      this.loading = true;

      if (filter_value != "" && filter_value.length <= 2) {
        this.snack = true;
        this.snackColor = "error";
        this.snackText = "Minimum 3 Characters to filter ";
        this.loading = false;
        return false;
      }

      //let filter_value = this.datatable_search_textbox;
      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";

      // if (this.filters) {
      //   page = 1;
      // }

      let itemsPerPage1 = itemsPerPage;
      if (!itemsPerPage1) itemsPerPage1 = 10;
      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage1,
          filter_column: filter_value,
          ...this.filters,
        },
      };

      if (filter_column != "") options.params[filter_column] = filter_value;
      this.$axios
        .get(
          `device/getLastRecordsHistory/${this.$auth.user.company_id}/${itemsPerPage1}`,
          options
        )
        .then(({ data }) => {
          this.totalRowsCount = data.total;
          this.logs = data.data;
          this.loading = false;
        });
      // .then(res => {
      //   alert(res.data.length)
      //   if (filter_column != '' && res.data.length == 0) {

      //     this.snack = true;
      //     this.snackColor = 'error';
      //     this.snackText = 'No Results Found';
      //     this.loading = false;
      //     return false;
      //   }
      //   this.totalRowsCount = res.total;
      //   this.logs = res.data;
      //   this.loading = false;

      // });
    },
    getShortName(item) {
      if (!item) {
        return false;
      }
      return item.split(" ").slice(0, 2).join(" ");
    },
    socketConnection() {
      this.socket = new WebSocket(this.url);

      this.socket.onmessage = ({ data }) => {
        let json = JSON.parse(data);

        if (json.Status == 200 && json.Data.UserCode > 0) {
          console.log("employee_report_page");
          this.getDetails(json.Data);
        }
      };
    },
    getDetails(item) {
      item.company_id = this.$auth.user.company_id;

      this.$axios.post(`/device/details`, item).then(({ data }) => {
        if (
          data.device &&
          this.$auth.user &&
          data.device.company_id == this.$auth.user.company_id
        ) {
          this.logs.unshift(data);
        }
      });
    },
  },
};
</script>
