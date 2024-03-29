<template>
  <div>
    <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
      {{ response }}
    </v-snackbar>
    <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
      {{ snackText }}
      <template v-slot:action="{ attrs }">
        <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
      </template>
    </v-snackbar>

    <v-row>
      <v-col style="padding: 0px">
        <v-card class="mb-5 pa-0" style="padding: 0px" elevation="0">
          <v-data-table
            hide-default-footer
            dense
            :headers="headers"
            :items="data"
            :loading="loading"
            :options.sync="options"
            :footer-props="{
              itemsPerPageOptions: [10, 50, 100, 500, 1000],
            }"
            class="elevation-1"
            model-value="data.id"
            :server-items-length="totalRowsCount"
          >
            <template v-slot:item.sno="{ item, index }">
              {{
                currentPage
                  ? (currentPage - 1) * perPage +
                    (cumulativeIndex + data.indexOf(item))
                  : ""
              }}
            </template>
            <template v-slot:item.visitor_first_name="{ item }">
              <v-row no-gutters>
                <v-col
                  style="
                    padding: 5px;
                    padding-left: 0px;

                    max-width: 50px;
                  "
                >
                  <v-img
                    style="
                      border-radius: 10%;
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
                  <div class="secondary-value">
                    {{ item.visitor ? item.visitor.phone_number : "---" }}
                  </div>
                  <div class="secondary-value">
                    {{ item.visitor ? item.visitor.email : "---" }}
                  </div>
                </v-col>
              </v-row>
            </template>
            <template v-slot:item.branch_id="{ item }">
              {{ item.branch && item.branch.branch_name }}
            </template>
            <template v-slot:item.purpose_id="{ item }">
              {{ item.visitor && item.visitor.purpose?.name }}
            </template>

            <template v-slot:item.in="{ item, index }">
              <div>
                {{ item.in ? item.in : "---" }}
                <div class="secondary-value">
                  {{ item.device_in_name ? item.device_in_name.name : "---" }}
                </div>
              </div>
            </template>
            <template v-slot:item.out="{ item, index }">
              <div>
                {{ item.out ? item.out : "---" }}
                <div class="secondary-value">
                  {{ item.device_out_name ? item.device_out_name.name : "---" }}
                </div>
              </div>
            </template>
            <template v-slot:item.overstay="{ item, index }">
              <div :style="item.over_stay ? 'color:red' : ' ;'">
                {{ item.over_stay ? item.over_stay : "---" }}
              </div>
            </template>

            <template v-slot:item.host_id="{ item, index }">
              <div>
                {{
                  item.visitor.host
                    ? item.visitor.host.employee?.first_name
                    : "---"
                }}
                {{
                  item.visitor.host
                    ? item.visitor.host.employee?.last_name
                    : "---"
                }}
              </div>

              <div class="secondary-value">
                {{
                  item.visitor.host
                    ? item.visitor.host.employee?.phone_number
                    : "---"
                }}
              </div>
            </template>

            <template v-slot:item.options="{ item }">
              <v-menu bottom left>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn dark-2 icon v-bind="attrs" v-on="on">
                    <v-icon>mdi-dots-vertical</v-icon>
                  </v-btn>
                </template>
                <v-list width="150" dense>
                  <v-list-item @click="viewItem(item)">
                    <v-list-item-title>
                      <v-icon small> mdi-history </v-icon>
                      Device Logs
                    </v-list-item-title>
                  </v-list-item>

                  <!-- <v-list-item @click="updateStatus(item.id, 3)">
                  <v-list-item-title style="cursor: pointer">
                    <v-icon color="red" small> mdi-cancel</v-icon>
                    Reject
                  </v-list-item-title>
                </v-list-item> -->
                </v-list>
              </v-menu>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
    <v-row justify="center">
      <v-dialog persistent v-model="log_details" max-width="600px">
        <v-card class="darken-1">
          <v-toolbar class="popup_background" dense flat>
            <span class="text-h5 pa-2">Log Details</span>
            <v-spacer></v-spacer>

            <!-- logs<b class="background--text mx-1">({{ log_list.length }})</b> -->

            <v-spacer></v-spacer>
            <v-icon @click="log_details = false"
              >mdi-close-circle-outline</v-icon
            >
          </v-toolbar>
          <v-card-text>
            <div class="pt-5">
              <!-- <span v-for="(log, index) in log_list" :key="index">
                  {{ log.time }}
                  <hr />
                </span> -->

              <v-data-table
                :headers="log_headers"
                :items="log_list"
                class="elevation-1"
                hide-default-footer
              >
                <template v-slot:item.sno="{ item, index }">
                  {{ ++index }}
                </template>
              </v-data-table>
            </div>
          </v-card-text>
        </v-card>
      </v-dialog>
      <v-dialog persistent v-model="renderVisitorDialog" max-width="500px">
        <v-card>
          <v-card-title class="popup_background">
            <span class="headline">Submit Data</span>
            <v-spacer></v-spacer>

            <v-btn
              x-small
              :ripple="false"
              text
              title="Add user"
              @click="addUser"
            >
              <v-icon dark>mdi-plus-circle</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            <v-form @submit.prevent="submitData">
              <v-container>
                <v-row>
                  <v-col cols="12">
                    <v-text-field
                      v-model="formData.date"
                      label="Date"
                      required
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-row v-for="(user, i) in users" :key="i">
                      <v-col cols="10">
                        <v-text-field
                          v-model="user.visitor_id"
                          label="User Id"
                          required
                        ></v-text-field>
                      </v-col>
                      <v-col cols="2">
                        <v-icon color="error" @click="removeUser(i)"
                          >mdi-delete</v-icon
                        >
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
              </v-container>
            </v-form>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn small color="primary" @click="submitData">Submit</v-btn>
            <v-btn small color="gray" @click="renderVisitorDialog = false"
              >Cancel</v-btn
            >
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-row>
  </div>
</template>
<script>
function getCurrentDate() {
  const today = new Date();
  const year = today.getFullYear();
  const month = String(today.getMonth() + 1).padStart(2, "0");
  const day = String(today.getDate()).padStart(2, "0");
  return `${year}-${month}-${day}`;
}
import Calender from "../Calender.vue";

export default {
  props: ["visitorIdentificationNumber", "visitorFullName"],
  components: { Calender },
  data: () => ({
    purposeList: [],
    branchesList: [],
    hostList: [],
    cumulativeIndex: 1,
    perPage: 10,
    currentPage: 1,
    renderVisitorDialog: false,
    users: [],
    formData: {
      max: 10,
      date: getCurrentDate(),
      company_id: 0,
    },
    attendancFilters: false,
    filters: {},
    isFilter: false,
    totalRowsCount: 0,
    datatable_search_textbox: "",
    datatable_filter_date: "",
    filter_visitor_id: "",
    snack: false,
    snackColor: "",
    snackText: "",
    date: null,
    filter_visitor_id: "",
    generateLogsDialog: false,
    reportSync: false,
    isCompany: true,
    time_table_dialog: false,
    log_details: false,
    overtime: false,
    options: {},
    date: null,
    menu: false,
    loading: false,
    time_menu: false,
    manual_time_menu: false,
    Model: "Visitor Reports",
    endpoint: "visitor_attendance",
    search: "",
    snackbar: false,
    add_manual_log: false,
    dialog: false,
    from_date: null,
    from_menu: false,
    to_date: null,
    to_menu: false,
    ids: [],
    visitors: [],
    DateRange: true,
    devices: [],
    valid: true,
    nameRules: [(v) => !!v || "reason is required"],
    timeRules: [(v) => !!v || "time is required"],
    deviceRules: [(v) => !!v || "device is required"],
    daily_menu: false,
    daily_date: null,
    dailyDate: false,
    editItems: {
      attendance_logs_id: "",
      UserID: "",
      device_id: "",
      user_id: "",
      reason: "",
      date: "",
      time: null,
    },
    loading: false,
    total: 0,
    log_headers: [
      {
        text: "#",
        align: "left",
        sortable: false,
        filterable: false,
        value: "sno",
      },

      {
        text: "Device",
        align: "left",
        sortable: false,
        filterable: false,
        value: "device.name",
      },
      {
        text: "Location",
        align: "left",
        sortable: false,
        filterable: false,
        value: "device.location",
      },
      {
        text: "Time",
        align: "left",
        sortable: false,
        filterable: false,
        value: "time",
      },
    ],
    headers: [
      {
        text: "#",
        align: "left",
        sortable: false,
        filterable: false,
        value: "sno",
      },
      {
        text: "Date",
        align: "left",
        sortable: false,
        filterable: false,
        filterSpecial: true,
        value: "date",
      },

      {
        text: "Host",
        align: "left",
        sortable: false,
        filterable: false,
        filterSpecial: false,
        value: "host_id",
        key: "host_id",
      },
      {
        text: "Purpose",
        align: "left",
        sortable: false,
        filterable: true,
        filterSpecial: true,
        value: "purpose_id",
      },

      {
        text: "Check In",
        align: "left",
        sortable: false,
        filterable: true,
        filterSpecial: false,
        value: "in",
      },
      {
        text: "Check Out",
        align: "left",
        sortable: false,
        filterable: true,
        filterSpecial: false,
        value: "out",
      },
      {
        text: "Duration",
        align: "left",
        sortable: false,
        filterable: true,
        filterSpecial: false,
        value: "total_hrs",
      },
      {
        text: "OverStay",
        align: "left",
        sortable: false,
        filterable: false,
        filterSpecial: true,
        value: "overstay",
      },
      { text: "Options", value: "options", sortable: false },
    ],
    frequency: "Monthly",

    payload: {
      from_date: null,
      to_date: null,
      daily_date: null,
      visitor_id: "",
      status: "All",
    },
    log_payload: {
      user_id: null,
      device_id: "",
      date: null,
      time: null,
    },
    log_list: [],
    snackbar: false,
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    data: [],
    shifts: [],
    errors: [],
    custom_options: {},
    max_date: null,
    item: null,
    viewDialog: false,
    attendance: [],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
  },

  watch: {
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
    this.getVisitors();
    // this.setMonthlyDateRange();
    this.payload.daily_date = new Date().toJSON().slice(0, 10);
    this.custom_options = {
      params: {
        per_page: 1000,
        company_id: this.$auth.user.company_id,
      },
    };
    this.getDeviceList();

    let dt = new Date();
    let y = dt.getFullYear();
    let m = dt.getMonth() + 1;
    let dd = new Date(dt.getFullYear(), m, 0);

    m = m < 10 ? "0" + m : m;

    this.payload.from_date = `${y}-${m}-01`;
    this.payload.to_date = `${y}-${m}-${dd.getDate()}`;

    //this.getDataFromApi();

    if (this.$auth.user.branch_id == null || this.$auth.user.branch_id == 0) {
      let branch_header = [
        {
          text: "Branch",
          align: "left",
          sortable: false,
          value: "branch_id",
          filterable: true,
          filterName: "branch_id",
          filterSpecial: true,
        },
      ];
      this.headers.splice(2, 0, ...branch_header);
    }
  },

  methods: {
    viewInfo(item) {
      this.item = item.visitor;
      this.attendance = item;
      this.viewDialog = true;
    },
    filterAttr(data) {
      this.payload.from_date = data.from;
      this.payload.to_date = data.to;
      this.getDataFromApi();
    },
    addUser() {
      this.users.push({ visitor_id: "" });
    },
    removeUser(index) {
      this.users.splice(index, 1);
    },
    submitData() {
      this.formData.userIds = this.users.map((e) => e.visitor_id);
      this.formData.company_id = this.$auth.user.company_id;

      this.$axios
        .post(`/render_daily_report`, this.formData)
        .then(({ data }) => {
          this.getDataFromApi();
          this.snackbar = true;
          this.response = data.message;
          this.dialog = false;
        })
        .catch(({ message }) => {
          this.snackbar = true;
          this.response = message;
        });
    },
    datatable_cancel() {
      this.datatable_search_textbox = "";
    },
    datatable_open() {
      this.datatable_search_textbox = "";
    },
    datatable_close() {
      this.loading = false;
    },

    processFilterDates() {
      const date = new Date(this.payload.from_date);

      date.setDate(date.getDate() + (this.frequency == "Weekly" ? 6 : 30));

      const d = date.getDate().toString().padStart(2, "0");
      const m = (date.getMonth() + 1).toString().padStart(2, "0");
      const y = date.getFullYear();

      if (this.frequency !== "Custom") {
        this.payload.to_date = `${y}-${m}-${d}`;
      }

      this.max_date = `${y}-${m}-${d}`;

      this.getDataFromApi();
    },

    set_date_save(from_menu, field) {
      from_menu.save(field);
      this.processFilterDates();
    },
    changeReportFrequency() {
      this.processFilterDates();
    },

    applyFilters(name, value) {
      //if (value && value.length < 2) return false;

      this.getDataFromApi();
    },
    async toggleFilter() {
      this.isFilter = !this.isFilter;

      if (this.isFilter) {
        this.refresh = true;
        this.handleChangeEvent();
      }
    },
    async handleChangeEvent() {
      this.branchesList = await this.$store.dispatch("fetchDropDowns", {
        key: "branchList",
        endpoint: "branch-list",
      });
      this.hostList = await this.$store.dispatch("fetchDropDowns", {
        key: "hostList",
        endpoint: "host_list",
      });
      this.purposeList = await this.$store.dispatch("fetchDropDowns", {
        key: "purposeList",
        endpoint: "purpose_list",
      });
    },
    clearFilters() {
      this.filters = {};
      this.isFilter = false;
      this.getDataFromApi();
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
    store_schedule() {
      let { user_id, date, time, device_id } = this.log_payload;
      let log_payload = {
        UserID: user_id,
        LogTime: date + " " + time,
        DeviceID: device_id,
        company_id: this.$auth.user.company_id,
      };
      this.loading = true;

      this.$axios
        .post(`/generate_log`, log_payload)
        .then(({ data }) => {
          this.getDataFromApi();
          this.add_manual_log = false;
          this.loading = false;
        })
        .catch(({ message }) => {
          this.snackbar = true;
          this.response = message;
        });
    },
    setVisitorId(id) {
      this.$store.commit("visitor_id", id);
    },

    async getVisitors() {
      // return;
      let payload = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };

      await this.$axios.get(`/visitors_with_type`, payload).then(({ data }) => {
        this.visitors = data;

        this.visitors.unshift({
          system_user_id: "",
          name_with_user_id: "Select All",
        });
      });
    },
    // getDevices(options) {
    //   this.$axios.get(`/device`, options).then(({ data }) => {
    //     this.devices = data.data;
    //   });
    // },

    caps(str) {
      return str.replace(/_/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      this.loading = true;

      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";

      // if (this.filters) {
      //   page = 1;
      // }

      if (this.frequency == "Custom") {
        if (this.payload.from_date == null) {
          return false;
        }
        if (this.payload.to_date == null) {
          return false;
        }
      }

      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
          ...this.payload,
          frequency: this.frequency,
          // status: this.getStatus(this.payload.status),
          ...this.filters,
          visitorIdentificationNumber: this.visitorIdentificationNumber,
        },
      };

      this.currentPage = page;
      if (filter_column != "") options.params[filter_column] = filter_value;
      this.$axios.get(url, options).then(async ({ data }) => {
        if (filter_column != "" && data.data.length == 0) {
          this.snack = true;
          this.snackColor = "error";
          this.snackText = "No Results Found";
          this.loading = false;
          return false;
        }
        this.data = data.data;

        const uniqueVisitorIds = new Set();
        this.users = await data.data.filter((item) => {
          const visitorId = item["visitor_id"];
          if (!uniqueVisitorIds.has(visitorId)) {
            uniqueVisitorIds.add(visitorId);
            return true;
          }
          return false;
        });

        isFilter: false,
          //  users: [{ userId: 6666 }],
          (this.total = data.total);
        this.loading = false;

        this.totalRowsCount = data.total;
      });
    },

    editItem(item) {
      this.dialog = true;
      this.editItems.UserID = item.visitor_id;
      this.editItems.date = item.edit_date;
    },

    update() {
      if (this.$refs.form.validate()) {
        let payload = {
          UserID: this.editItems.UserID,
          LogTime: this.editItems.date + " " + this.editItems.time,
          DeviceID: this.editItems.device_id,
          user_id: this.editItems.UserID,
          company_id: this.$auth.user.company_id,
          reason: this.editItems.reason,
        };

        this.$axios
          .post("/generate_manual_log", payload)
          .then(({ data }) => {
            this.loading = false;
            if (!data.status) {
              this.errors = data.errors;
              // this.msg = data.message;
            } else {
              this.snackbar = true;
              this.response = data.message;
              // this.editItems = [];
              // this.renderByType("render_general_report");
              this.close();
            }
          })
          .catch((e) => console.log(e));
      }
    },
    renderByType(type) {
      const UserID = this.editItems.UserID;
      const date = this.editItems.date;

      if (!UserID || !date) {
        alert("System User Id and Date field is required");
        return;
      }

      let payload = {
        params: {
          date: this.editItems.date,
          UserID: this.editItems.UserID,
          updated_by: this.$auth.user.id,
          company_id: this.$auth.user.company_id,
          manual_entry: true,
          reason: this.editItems.reason,
        },
      };

      this.$axios
        .get("/" + type, payload)
        .then(({ data }) => {
          this.loading = false;
          this.snackbar = true;
          this.response = data.message;
          this.getDataFromApi();
        })
        .catch((e) => console.log(e));
    },
    viewItem(item) {
      this.log_list = [];
      let options = {
        params: {
          per_page: 500,
          UserID: item.system_user_id,
          LogTime: item.edit_date,
          company_id: this.$auth.user.company_id,
        },
      };
      this.log_details = true;

      this.$axios.get("visitor_log_list", options).then(({ data }) => {
        this.log_list = data.data;
      });

      // this.editedIndex = this.data.indexOf(item);
      // this.editedItem = Object.assign({}, item);
      // this.dialog = true;
    },

    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },

    process_file(action) {
      if (!this.data.length) {
        alert("No record found");
        return;
      }
      const { visitor_id, daily_date, from_date, to_date, status } =
        this.payload;
      const frequency = this.frequency;
      const company_id = this.$auth.user.company_id;
      let { itemsPerPage } = this.options;
      let path = process.env.BACKEND_URL + "/visitor_attendance_report";

      let qs = ``;

      qs += `${path}`;
      qs += `?company_id=${company_id}`;
      qs += `&status=${status}`;
      qs += `&visitor_id=${visitor_id}`;
      qs += `&frequency=${frequency}`;
      qs += `&action=${action}`;
      qs += `&per_page=${20}`;

      if (frequency == "Daily") {
        qs += `&daily_date=${daily_date}`;
      } else {
        qs += `&from_date=${from_date}&to_date=${to_date}`;
      }

      let report = document.createElement("a");
      report.setAttribute("href", qs);
      report.setAttribute("target", "_blank");
      report.click();

      this.getDataFromApi();
      return;
    },
  },
};
</script>
