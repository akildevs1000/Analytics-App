<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
      <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
        {{ snackText }}

        <template v-slot:action="{ attrs }">
          <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
        </template>
      </v-snackbar>
    </div>
    <v-row justify="center">
      <v-dialog persistent v-model="generateLogsDialog" max-width="700px">
        <v-card>
          <v-card-title class="popup_background">
            <span class="headline" dense> Generate Log </span>
            <v-spacer></v-spacer>
            <v-icon dark @click="generateLogsDialog = false" outlined>
              mdi mdi-close-circle
            </v-icon>
          </v-card-title>
          <v-card-text>
            <GenerateLog @update-data-table="getDataFromApi()" />
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-row>
    <!-- <v-row class="pt-2 mt-5">
        <v-col cols="12" sm="8" md="2">
          <v-menu
            ref="from_menu"
            v-model="from_menu"
            :close-on-content-click="false"
            transition="scale-transition"
            offset-y
            min-width="auto"
          >
            <template v-slot:activator="{ on, attrs }">
              <div class="mb-1">From Date</div>
              <v-text-field
                :hide-details="!payload.from_date"
                outlined
                dense
                v-model="payload.from_date_txt"
                readonly
                v-bind="attrs"
                v-on="on"
                placeholder="Date"
              ></v-text-field>
            </template>
            <v-date-picker
              style="height: 350px"
              v-model="payload.from_date"
              no-title
              scrollable
              @change="searchIt"
            >
              <v-spacer></v-spacer>
               
            </v-date-picker>
          </v-menu>
        </v-col>
        <v-col cols="12" sm="8" md="2">
          <div class="mb-1">To Date</div>
          <v-menu
            ref="to_menu"
            v-model="to_menu"
            :close-on-content-click="false"
            transition="scale-transition"
            offset-y
            min-width="auto"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                :hide-details="!payload.to_date"
                outlined
                dense
                v-model="payload.to_date_txt"
                readonly
                v-bind="attrs"
                v-on="on"
                placeholder="Date"
              ></v-text-field>
            </template>
            <v-date-picker
              style="height: 350px"
              v-model="payload.to_date"
              no-title
              scrollable
              @change="searchIt"
            >
              
            </v-date-picker>
          </v-menu>
        </v-col>
        <v-col cols="12" sm="6" md="2">
          <div class="mb-1">System User ID</div>
          <v-text-field
            @input="searchIt"
            v-model="payload.UserID"
            outlined
            dense
            placeholder="Search..."
          ></v-text-field>
        </v-col>
         
      </v-row> -->
    <v-row>
      <v-col>
        <v-card class="mb-5" elevation="0">
          <v-toolbar class="rounded-md" dense flat>
            <v-toolbar-title><span> Visitor Device Logs</span></v-toolbar-title>
            <!-- <v-tooltip top color="primary">
                <template v-slot:activator="{ on, attrs }"> -->
            <v-btn
              dense
              class="ma-0 px-0"
              x-small
              :ripple="false"
              @click="getRecords"
              text
              title="Reload"
            >
              <v-icon class="ml-2" dark>mdi mdi-reload</v-icon>
            </v-btn>
            <!-- </template>
                <span>Reload</span>
              </v-tooltip> -->

            <!-- <v-tooltip top color="primary">
                <template v-slot:activator="{ on, attrs }"> -->
            <v-btn
              x-small
              :ripple="false"
              text
              title="Filter"
              @click="toggleFilter"
            >
              <v-icon dark>mdi-filter</v-icon>
            </v-btn>
            <!-- </template>
                <span>Filter</span>
              </v-tooltip> -->

            <v-spacer></v-spacer>
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
            class="elevation-1"
            :server-items-length="totalRowsCount"
          >
            <template v-slot:header="{ props: { headers } }">
              <tr v-if="isFilter">
                <td v-for="header in headers" :key="header.text">
                  <v-text-field
                    class="ma-3"
                    clearable
                    :hide-details="true"
                    v-if="header.filterable && !header.filterSpecial"
                    v-model="filters[header.key]"
                    :id="header.value"
                    @input="applyFilters(header.key, $event)"
                    outlined
                    dense
                    autocomplete="off"
                  ></v-text-field>
                  <v-select
                    v-if="header.filterSpecial && header.value == 'branch_id'"
                    :hide-details="true"
                    clearable
                    @change="applyFilters('status', $event)"
                    item-value="id"
                    item-text="name"
                    v-model="filters[header.value]"
                    outlined
                    dense
                    :items="[{ name: `All Branches`, id: `` }, ...branchesList]"
                  ></v-select>
                  <v-select
                    v-if="header.filterSpecial && header.value == 'purpose_id'"
                    :hide-details="true"
                    clearable
                    @change="applyFilters('status', $event)"
                    item-value="id"
                    item-text="name"
                    v-model="filters[header.value]"
                    outlined
                    dense
                    :items="[{ name: `All`, id: `` }, ...purposeList]"
                  ></v-select>
                  <v-menu
                    v-if="header.filterSpecial && header.value == 'LogTime'"
                    ref="from_menu_filter"
                    v-model="from_menu_filter"
                    :close-on-content-click="false"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        class="ma-3"
                        :hide-details="!from_date_filter"
                        outlined
                        dense
                        v-model="filters[header.value]"
                        readonly
                        v-bind="attrs"
                        v-on="on"
                        placeholder="Select Date"
                        clearable
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      style="height: 350px"
                      v-model="filters[header.value]"
                      no-title
                      scrollable
                      @input="applyFilters('status', $event)"
                    >
                      <v-spacer></v-spacer>

                      <v-btn
                        text
                        color="primary"
                        @click="
                          filters[header.value] = '';
                          from_menu_filter = false;
                          applyFilters();
                        "
                      >
                        Clear
                      </v-btn>
                    </v-date-picker>
                  </v-menu>
                  <v-select
                    clearable
                    class="ma-3"
                    :id="header.key"
                    :hide-details="true"
                    v-if="header.filterSpecial && header.value == 'device.name'"
                    outlined
                    dense
                    small
                    v-model="filters[header.key]"
                    item-text="name"
                    item-value="id"
                    :items="[{ name: `All Devices`, id: `` }, ...devices]"
                    placeholder="Device Name"
                    flat
                    @change="applyFilters('status', $event)"
                  ></v-select>
                  <v-select
                    class="ma-3"
                    :id="header.key"
                    :hide-details="true"
                    v-if="
                      header.filterSpecial && header.value == 'device.location'
                    "
                    outlined
                    clearable
                    dense
                    small
                    v-model="filters[header.key]"
                    item-text="location"
                    item-value="id"
                    :items="[{ location: `All Locations`, id: '' }, ...devices]"
                    placeholder="Location"
                    flat
                    @change="applyFilters('status', $event)"
                  ></v-select>
                </td>
              </tr>
            </template>
            <template v-slot:item.UserID="{ item }">
              <strong> {{ item.UserID ? item.UserID : "---" }}</strong>
            </template>
            <template v-slot:item.visitor_full_name="{ item, index }">
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
                      item.visitor && item.visitor.logo
                        ? item.visitor.logo
                        : '/no-profile-image.jpg'
                    "
                  >
                  </v-img>
                </v-col>
                <v-col style="padding: 10px">
                  <strong>
                    {{ item.visitor ? item.visitor.first_name : "---" }}
                    {{ item.visitor ? item.visitor.last_name : "---" }}</strong
                  >
                  <div>
                    {{ item.visitor ? caps(item?.visitor?.zone.name) : "---" }}
                  </div>
                </v-col>
              </v-row>
            </template>
            <!-- <template v-slot:item.LogTime="{ item }">
                {{ item.visitor ? item.visitor.reason : "---" }}
              </template> -->
            <template v-slot:item.branch_id="{ item }">
              {{
                (item.visitor?.branch && item.visitor.branch.branch_name) ||
                "---"
              }}
              <div class="secondary-value">
                {{ item.visitor?.host?.floor_number || "---" }} -
                {{ item.visitor?.host?.flat_number || "---" }}
              </div>
            </template>
            <template v-slot:item.purpose_id="{ item }">
              {{ item.visitor?.purpose.name || "---" }}
            </template>

            <template v-slot:item.LogTime="{ item }">
              {{ item.LogTime.split(" ")[1] || "---" }}
              <div class="secondary-value">
                {{ $dateFormat.format1(item.LogTime.split(" ")[0]) || "---" }}
              </div>
            </template>
            <template v-slot:item.device.name="{ item }">
              {{ item.device ? caps(item.device.name) : "---" }}
            </template>
            <template v-slot:item.device.location="{ item }">
              {{ item.device ? caps(item.device.location) : "---" }}
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
    <!-- <v-row class="mt-5">
        <v-col cols="12">
          <v-data-table v-model="ids" item-key="id" :headers="headers" :items="data" :server-items-length="total"
            :loading="loading" :options.sync="options" :footer-props="{
              itemsPerPageOptions: [50, 100, 500, 1000],
            }"></v-data-table>
        </v-col>
      </v-row> -->
  </div>
</template>

<script>
export default {
  data: () => ({
    purposeList: [],
    branchesList: [],
    id: "",
    from_menu_filter: "",
    from_date_filter: "",

    showFilters: false,
    filters: {},
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
    endpoint: "visitor_logs",

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
      device_id: "",
      date: null,
      time: null,
    },

    ids: [],

    data: [],
    devices: [],
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
        text: "Visitor ID",
        align: "left",
        sortable: true,
        key: "UserID",
        value: "UserID",
        width: "150px",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Visitor",
        align: "left",
        sortable: true,
        key: "visitor_full_name", //sorting
        value: "visitor_full_name", //edit purpose
        width: "300px",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Purpose",
        align: "left",
        sortable: false,
        key: "purpose", //sorting
        value: "purpose_id", //edit purpose
        filterable: true,
        filterSpecial: true,
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
      {
        text: "Device Location",
        align: "left",
        sortable: true,
        key: "devicelocation",
        value: "device.location",
        filterable: true,
        filterSpecial: true,
      },
    ],
  }),
  created() {
    this.firstLoad();
    //this.getDepartments();

    if (this.$auth.user.branch_id == null || this.$auth.user.branch_id == 0) {
      let branch_header = [
        {
          text: "Branch",
          align: "left",
          sortable: true,
          value: "branch_id",
          filterable: true,
          filterName: "branch_id",
          filterSpecial: true,
        },
      ];
      this.headers.splice(1, 0, ...branch_header);
    }
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
    getDepartments() {
      let options = {
        params: {
          per_page: 10,
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios.get(`departments`, options).then(({ data }) => {
        this.departments = data.data;
        this.departments.unshift({ name: "All Departments", id: "" });
      });
    },
    // applyFilter() {
    //   this.getDataFromApi();
    //   this.from_menu_filter = false;
    //   this.to_menu_filter = false;
    // },
    applyFilters() {
      this.getDataFromApi();
      this.from_menu_filter = false;
      this.to_menu_filter = false;
    },
    async toggleFilter() {
      this.isFilter = !this.isFilter;

      if (this.isFilter) {
        this.refresh = true;
        this.handleChangeEvent();
        this.getDeviceList();
      }
    },
    async handleChangeEvent() {
      this.purposeList = await this.$store.dispatch("fetchDropDowns", {
        key: "purposeList",
        endpoint: "purpose_list",
      });
      this.branchesList = await this.$store.dispatch("fetchDropDowns", {
        key: "branchList",
        endpoint: "branch-list",
      });
    },
    clearFilters() {
      this.filters = {};

      this.isFilter = false;
      this.getDataFromApi();
    },
    firstLoad() {
      this.loading = true;

      this.payload.from_date = this.getDate();
      this.payload.to_date = this.getDate();
      this.payload.from_date_txt = this.getDate();
      this.payload.to_date_txt = this.getDate();

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
    datatable_save() {},
    datatable_cancel() {
      this.datatable_search_textbox = "";
    },
    datatable_open() {
      this.datatable_search_textbox = "";
    },
    datatable_close() {
      this.loading = false;
      //this.datatable_search_textbox = '';
    },
    getDeviceList() {
      let payload = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios.get(`device-list`, payload).then(({ data }) => {
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
      if (filter_column != "")
        this.payloadOptions.params[filter_column] = filter_value;
      this.loading = true;
      this.$axios
        .get(`${url}?page=${this.options.page}`, this.payloadOptions)
        .then(({ data }) => {
          this.data = data.data;
          this.total = data.total;
          this.loading = false;
          this.totalRowsCount = data.total;
        });
    },
    searchIt() {
      this.payload.from_date_txt = this.payload.from_date;
      this.payload.to_date_txt = this.payload.to_date;

      this.getDataFromApi();
      this.from_menu = false;
      this.to_menu = false;
      return;
    },
  },
};
</script>
