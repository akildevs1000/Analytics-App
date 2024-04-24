<template>
  <div v-if="can(`logs_access`)">
    <v-card elevation="0" class="mt-2">
      <!-- <v-toolbar dense flat>
        <span class="headline black--text"> Customer Reports </span>
      </v-toolbar> -->

      <v-card-text class="py-3">
        <v-row>
          <v-col md="2" sm="2" v-if="isCompany">
            <v-select
              label="Branch"
              outlined
              dense
              v-model="payload.branch_id"
              x-small
              :items="[{ id: ``, branch_name: `Select All` }, ...branchesList]"
              item-value="id"
              item-text="branch_name"
              :hide-details="true"
            ></v-select>
          </v-col>
          <v-col cols="2">
            <v-autocomplete
              label="Customer"
              density="comfortable"
              outlined
              dense
              v-model="payload.UserID"
              x-small
              :items="[
                { system_user_id: ``, full_name: `Select All` },
                ...customers,
              ]"
              item-value="system_user_id"
              item-text="full_name"
              :hide-details="true"
            ></v-autocomplete>
          </v-col>
          <v-col md="2" sm="2">
            <v-select
              label="Age Group"
              outlined
              dense
              v-model="payload.age_category"
              x-small
              item-text="name"
              item-value="id"
              :items="[
                { name: `Select All`, id: `` },
                { name: `CHILD`, id: `CHILD` },
                { name: `YOUNGER`, id: `YOUNGER` },
                { name: `ADULT`, id: `ADULT` },
                { name: `SENIOR`, id: `SENIOR` },
              ]"
              :hide-details="true"
            ></v-select>
          </v-col>
          <v-col md="2" sm="2">
            <v-select
              label="Gender"
              outlined
              dense
              v-model="payload.Gender"
              x-small
              item-text="name"
              item-value="id"
              :items="[
                { name: `Select All`, id: `` },
                { name: `Male`, id: `Male` },
                { name: `Female`, id: `Female` },
              ]"
              :hide-details="true"
            ></v-select>
          </v-col>
          <v-col md="2" sm="2">
            <v-select
              label="Device"
              outlined
              dense
              v-model="payload.DeviceID"
              x-small
              item-text="name"
              item-value="device_id"
              :items="[{ name: `All Devices`, device_id: `` }, ...devices]"
              :hide-details="true"
            ></v-select>
          </v-col>
          <v-col cols="2">
            <CustomFilter
              @filter-attr="handleDatesFilter"
              :defaultFilterType="1"
              :height="'40px'"
            />
          </v-col>
          <v-col md="2" sm="2">
            <v-btn @click="getDataFromApi()" color="primary" primary fill
              >Generate
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <v-card elevation="0" class="mt-2">
      <v-toolbar class="mb-2 white--text" color="white" dense flat>
        <v-toolbar-title>
          <span style="color: black"> Device Logs</span></v-toolbar-title
        >
        <!-- <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }"> -->
        <v-btn
          title="Reload"
          dense
          class="ma-0 px-0"
          x-small
          :ripple="false"
          @click="getRecords"
          text
        >
          <v-icon class="ml-2" dark>mdi-reload</v-icon>
        </v-btn>

        <v-spacer></v-spacer>
      </v-toolbar>

      <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
        {{ snackText }}

        <template v-slot:action="{ attrs }">
          <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
        </template>
      </v-snackbar>

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
        class="elevation-1"
        :server-items-length="totalRowsCount"
        fixed-header
        :height="tableHeight"
      >
        <template v-slot:item.user="{ item, index }">
          <v-row no-gutters v-if="item.customer" class="pa-1">
            <v-col cols="2" class="mr-4">
              <v-avatar
                v-if="item && item.customer && item.customer.profile_picture"
              >
                <v-img
                  :src="
                    item?.customer?.profile_picture || '/no-profile-image.jpg'
                  "
                >
                </v-img>
              </v-avatar>
            </v-col>
            <v-col class="pt-1">
              <strong v-if="item.customer">
                {{ item.customer.full_name }}
              </strong>
              <br />
              <small v-if="item.customer">
                {{ item.customer.phone_number }}
              </small>
            </v-col>
          </v-row>
        </template>
      </v-data-table>
    </v-card>
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  data: () => ({
    isCompany: true,
    missingLogsDialog: false,
    branchesList: [],
    tableHeight: 750,
    id: "",
    from_menu_filter: "",
    from_date_filter: "",

    showFilters: false,
    filters: {
      user_type: "Customer",
    },
    isFilter: false,
    generateLogsDialog: false,
    totalRowsCount: 0,
    //server_datatable_totalItems: 10,
    datatable_search_textbox: "",
    datatable_searchById: "",
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",
    departments: [],
    Model: "Log",
    endpoint: "attendance_logs",

    from_date: null,
    from_menu: false,
    to_date: null,
    to_menu: false,

    payload: {},

    loading: true,

    date: null,
    menu: false,

    loading: false,
    time_menu: false,

    log_payload: {
      user_id: 41,
      device_id: "OX-8862021010100",
      date: null,
      time: null,
    },
    ids: [],

    data: [],
    devices: [],
    customers: [],

    total: 0,
    pagination: {
      current: 1,
      total: 0,
      itemsPerPage: 1000,
    },
    payloadOptions: {},
    options: {
      current: 1,
      total: 0,
      itemsPerPage: 10,
    },
    errors: [],
    response: "",
    snackbar: false,
    headers: [
      {
        text: "Employee",
        align: "left",
        sortable: true,
        key: "user", //sorting
        value: "user", //edit purpose
        width: "250px",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "DateTime",
        align: "left",
        sortable: false,
        key: "date_range",
        value: "LogTime",
        filterable: true,
        filterSpecial: true,
        fieldType: "date_range_picker",
      },
      {
        text: "Age",
        align: "left",
        sortable: true,
        key: "Age",
        value: "Age",
        width: "150px",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Age Group",
        align: "left",
        sortable: true,
        key: "age_category",
        value: "age_category",
        width: "150px",
        filterable: true,
        filterSpecial: true,
      },
      {
        text: "Gender",
        align: "left",
        sortable: true,
        key: "Gender",
        value: "Gender",
        width: "150px",
        filterable: true,
        filterSpecial: true,
      },
      {
        text: "Clarity",
        align: "left",
        sortable: true,
        key: "Clarity",
        value: "Clarity",
        width: "150px",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Quality",
        align: "left",
        sortable: true,
        key: "Quality",
        value: "Quality",
        width: "150px",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Similarity",
        align: "left",
        sortable: true,
        key: "Similarity",
        value: "Similarity",
        width: "150px",
        filterable: true,
        filterSpecial: false,
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
  }),

  mounted() {
    this.tableHeight = window.innerHeight - 270;
    window.addEventListener("resize", () => {
      this.tableHeight = window.innerHeight - 270;
    });

    setInterval(() => {
      if (this.$route.name == "devicelogs") {
        this.getDataFromApi();
      }
    }, 1000 * 60 * 2);
  },
  created() {
    if (this.$auth.user.user_type == "company") {
      let branch_header = [
        {
          text: "Branch",
          align: "left",
          sortable: true,
          key: "branch_id", //sorting
          value: "branch.branch_name", //edit purpose
          width: "150px",
          filterable: true,
          filterSpecial: true,
        },
      ];
      this.headers.splice(0, 0, ...branch_header);
    }
    this.firstLoad();
  },
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  methods: {
    getCustomers() {
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };

      this.$axios.get(`/customer-list`, options).then(({ data }) => {
        this.customers = data;
      });
    },
    getbranchesList() {
      this.payloadOptions = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };

      this.$axios.get(`branches_list`, this.payloadOptions).then(({ data }) => {
        this.branchesList = data;
      });
    },
    handleDatesFilter(dates) {
      this.payload.from_date = dates.from; // dates[0];
      this.payload.to_date = dates.to; // dates[1];
      this.payload.from_date_txt = dates.from; //dates[0];
      this.payload.to_date_txt = dates.to; //dates[1];
      this.getDataFromApi(this.endpoint, "dates", [dates.from, dates.to]);
    },
    firstLoad() {
      this.loading = true;

      this.payload.from_date = this.getDate();
      this.payload.to_date = this.getDate();
      this.payload.from_date_txt = this.getDate();
      this.payload.to_date_txt = this.getDate();
      this.getDeviceList();
      this.getCustomers();
      this.getbranchesList();
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
    getDeviceList() {
      let payload = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios.get(`/device_list`, payload).then(({ data }) => {
        this.devices = data;
      });
    },
    getDate() {
      const date = new Date();
      const year = date.getFullYear();
      const month = (date.getMonth() + 1).toString().padStart(2, "0");
      const day = date.getDate().toString().padStart(2, "0");
      return `${year}-${month}-${day}`;
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },

    getRecords(filter_column = "", filter_value = "") {
      this.filters = {};
      this.isFilter = false;
      if (filter_value != "" && filter_value.length <= 2) {
        this.snack = true;
        this.snackColor = "error";
        this.snackText = "Minimum 3 Characters to filter ";
        this.loading = false;
        return false;
      }
      this.getDataFromApi(this.endpoint, filter_column, filter_value);
    },
    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";

      this.payloadOptions = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
          ...this.payload,
          ...this.filters,
        },
      };
      if (filter_column != "") {
        this.payloadOptions.params[filter_column] = filter_value;
      }

      this.loading = true;
      this.$axios.get(url, this.payloadOptions).then(({ data }) => {
        this.data = data.data;
        this.total = data.total;
        this.loading = false;
        this.totalRowsCount = data.total;
      });
    },
  },
};
</script>
