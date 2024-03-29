<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="purple" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row justify="center">
      <v-dialog v-model="changeRequestDialog" max-width="700px">
        <v-card>
          <v-card-title dark class="popup_background">
            <span dense> Change Request </span>
            <v-spacer></v-spacer>
            <v-icon @click="changeRequestDialog = false" outlined>
              mdi mdi-close-circle
            </v-icon>
          </v-card-title>
          <v-card-text>
            <ChangeRequest
              @reload="getRecords()"
              @close-change-request-form="() => (changeRequestDialog = false)"
            />
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-row>
    <v-row>
      <v-col>
        <v-card elevation="0" class="mt-2">
          <v-toolbar class="mb-2 white--text" color="white" dense flat>
            <v-toolbar-title>
              <span style="color: black">
                Change Requests</span
              ></v-toolbar-title
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
              <v-icon class="ml-2" dark>mdi mdi-reload</v-icon>
            </v-btn>
            <!-- </template>
              <span>Reload</span>
            </v-tooltip> -->

            <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  title="Filter"
                  x-small
                  :ripple="false"
                  text
                  @click="toggleFilter"
                >
                  <v-icon dark white>mdi-filter</v-icon>
                </v-btn>
              </template>
              <span>Filter</span>
            </v-tooltip>
            <v-spacer></v-spacer>
            <!-- <v-btn
              title="Change Request"
              x-small
              :ripple="false"
              text
              @click="changeRequestDialog = true"
            >
              <v-icon class="">mdi mdi-plus-circle</v-icon>
            </v-btn> -->
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
            :disable-sort="true"
          >
            <template v-slot:header="{ props: { headers } }">
              <tr v-if="isFilter">
                <td v-for="header in headers" :key="header.text">
                  <v-select
                    style="width: 250px"
                    v-if="header.key == 'branch_id' && header.filterable && header.field_type == 'dropdown'"
                    @input="applyFilters(header, $event)"
                    outlined
                    autocomplete="off"
                    dense
                    class="mx-2"
                    hide-details
                    v-model="branch_id"
                    :items="[
                      { id: ``, branch_name: `Select All` },
                      ...branchesList,
                    ]"
                    item-value="id"
                    item-text="branch_name"
                  ></v-select>

                  <v-text-field
                    style="width: 250px"
                    v-if="header.filterable && header.field_type == 'text'"
                    v-model="filters[header.value]"
                    @input="applyFilters(header, $event)"
                    outlined
                    clearable
                    autocomplete="off"
                    hide-details
                    dense
                    class="ma-2"
                  ></v-text-field>

                  <v-select
                    style="width: 250px"
                    v-if="header.key == 'status' && header.filterable && header.field_type == 'dropdown'"
                    @input="applyFilters(header, $event)"
                    outlined
                    autocomplete="off"
                    dense
                    class="ma-2"
                    hide-details
                    v-model="filters[header.value]"
                    :items="[
                      {
                        text: `Select`,
                        value: ``,
                      },
                      {
                        text: `Pending`,
                        value: `P`,
                      },
                      {
                        text: `Approved`,
                        value: `A`,
                      },
                      {
                        text: `Rejected`,
                        value: `R`,
                      },
                    ]"
                    item-text="text"
                    item-value="value"
                  ></v-select>
                </td>
              </tr>
            </template>

            <template v-slot:item.remarks="{ item }">
              <div style="width: 250px">{{ item.remarks }}</div>
            </template>
            <template v-slot:item.status="{ item }">
              <div style="width: 150px">
                <v-chip v-if="item.status == 'P'" class="purple" dark small
                  >New Request
                </v-chip>
                <v-chip v-else-if="item.status == 'A'" class="green" dark small
                  >Approved</v-chip
                >
                <v-chip v-else class="red" dark small>Rejected</v-chip>
              </div>
            </template>

            <template v-slot:item.action="{ item }">
              <v-select
                style="width: 175px"
                outlined
                autocomplete="off"
                dense
                class="ma-2"
                v-model="item.status"
                @change="updateStatus(item)"
                hide-details
                :items="[
                  {
                    text: `New Request`,
                    value: `P`,
                  },
                  {
                    text: `Approved`,
                    value: `A`,
                  },
                  {
                    text: `Rejected`,
                    value: `R`,
                  },
                ]"
                item-text="text"
                item-value="value"
              ></v-select>
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
// import DateRangePicker from "../components/Snippets/Filters/DateRangePicker.vue";

export default {
  components: {
    // DateRangePicker,
  },
  data: () => ({
    branchesList: [],
    isCompany: true,
    tableHeight: 750,
    id: "",
    from_menu_filter: "",
    from_date_filter: "",

    showFilters: false,
    filters: {},
    isFilter: false,
    changeRequestDialog: false,
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
    endpoint: "change_request",

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
    headers: [
      {
        text: "EID",
        filterable: true,
        field_type: "text",
        sortable: false,
        value: "employee_device_id",
      },
      {
        text: "Request type",
        filterable: true,
        field_type: "text",
        sortable: false,
        value: "request_type",
      },

      {
        text: "From",
        sortable: false,
        value: "from_date",
      },
      {
        text: "To",
        sortable: false,
        value: "to_date",
      },
      {
        text: "Remarks",
        sortable: false,
        value: "remarks",
      },
      {
        key: "status",
        text: "Status",
        sortable: false,
        filterable: true,
        field_type: "dropdown",
        value: "status",
      },
      {
        text: "Action",
        value: "action",
      },
    ],
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
    snackbar: false,
    branchesList: [],
    branch_id: "",

    responseStatusColor: "",
    response: "",
  }),
  computed: {},
  mounted() {
    this.tableHeight = window.innerHeight - 270;
    window.addEventListener("resize", () => {
      this.tableHeight = window.innerHeight - 270;
    });
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
        field_type: "dropdown",
        text: "Branch",
        align: "left",
        sortable: true,
        key: "branch_id",
        value: "employee.branch.branch_name",
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

    this.getChangeRequests();
  },
  watch: {
    options: {
      handler() {
        this.getChangeRequests();
      },
      deep: true,
    },
  },
  methods: {
    updateStatus(item) {
      this.$axios
        .post("update-change-request/" + item.id, item)
        .then(({ data }) => {
          this.snackbar = true;
          this.responseStatusColor = "green";
          this.response = "Request has been updated succussfully.";
          setTimeout(() => {
            this.snackbar = false;
            this.response = "";
            this.responseStatusColor = "";
          }, 2000);
        })
        .catch(({ response }) => {
          if (!response) {
            return false;
          }
          let { status, data, statusText } = response;
          this.response = status == 422 ? data.message : statusText;
          this.responseStatusColor = "red";
        });
    },
    getRelatedColor(item) {
      let colors = {
        P: "purple",
        R: "red",
        A: "green",
      };
      return colors[item.status];
    },
    handleDatesFilter(dates) {
      if (dates.length > 1) {
        this.getChangeRequests(this.endpoint, "dates", dates);
      }
    },

    applyFilters(item, columnValue) {
      this.getRecords(item, columnValue);
      this.from_menu_filter = false;
      this.to_menu_filter = false;
    },
    toggleFilter() {
      // this.filters = {};
      this.isFilter = !this.isFilter;
    },
    clearFilters() {
      this.filters = {};

      this.isFilter = false;
      this.getChangeRequests();
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
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
    getRecords(item = "", filter_value = "") {
      this.filters = {};
      if (
        filter_value != "" &&
        filter_value.length <= 2 &&
        item.field_type == "text"
      ) {
        this.snack = true;
        this.snackColor = "error";
        this.snackText = "Minimum 3 Characters to filter ";
        this.loading = false;
        return false;
      }
      this.getChangeRequests(this.endpoint, item.value, filter_value);
    },
    getChangeRequests(
      url = this.endpoint,
      filter_column = "",
      filter_value = ""
    ) {
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
          branch_id: this.branch_id,
          ...this.payload,
          ...this.filters,
        },
      };
      if (filter_column != "")
        this.payloadOptions.params[filter_column] = filter_value;
      this.loading = true;
      this.$axios.get(url, this.payloadOptions).then(({ data }) => {
        // if (filter_column != "" && data.data.length == 0) {
        //   this.snack = true;
        //   this.snackColor = "error";
        //   this.snackText = "No Results Found";
        //   this.loading = false;
        //   return false;
        // }
        //this.server_datatable_totalItems = data.total;
        this.data = data.data;
        this.total = data.total;
        this.loading = false;
        this.totalRowsCount = data.total;
      });
    },
  },
};
</script>
