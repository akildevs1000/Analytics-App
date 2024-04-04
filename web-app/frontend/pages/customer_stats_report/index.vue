<template>
  <div v-if="can(`attendance_report_view`)">
    <v-card elevation="0" class="mt-2">
      <v-toolbar dense flat>
        <span class="headline black--text"> Customer Reports </span>
      </v-toolbar>

      <v-card-text class="py-3">
        <v-row>
          <v-col cols="2" v-if="isCompany">
            <v-select
              label="Branch"
              outlined
              dense
              v-model="payload.branch_id"
              x-small
              :items="[{ id: ``, branch_name: `Select All` }, ...branches]"
              item-value="id"
              item-text="branch_name"
              :hide-details="true"
            ></v-select>
          </v-col>
          <v-col cols="2">
            <CustomFilter
              @filter-attr="filterAttr"
              :defaultFilterType="1"
              :height="'40px'"
            />
          </v-col>
          <v-col cols="2">
            <v-btn @click="getDataFromApi()" color="primary" primary fill
              >Generate
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
    <v-card class="mb-5 mt-5" elevation="0">
      <div v-if="can(`attendance_report_access`)">
        <div class="text-center">
          <v-snackbar
            v-model="snackbar"
            top="top"
            color="secondary"
            elevation="24"
          >
            {{ response }}
          </v-snackbar>
          <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
            {{ snackText }}

            <template v-slot:action="{ attrs }">
              <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
            </template>
          </v-snackbar>
        </div>
        <v-card class="mb-5" elevation="0">
          <v-toolbar class="backgrounds" dense flat>
            <v-toolbar-title> </v-toolbar-title>

            <v-spacer></v-spacer>
            <span style="padding-left: 15px"
              ><img
                title="Print"
                style="cursor: pointer"
                @click="process_file('customer-report-print')"
                src="/icons/icon_print.png"
                class="iconsize"
            /></span>
            <span style="padding-left: 15px"
              ><img
                title="Download Pdf"
                style="cursor: pointer"
                @click="process_file('customer-report-download')"
                src="/icons/icon_pdf.png"
                class="iconsize"
            /></span>
          </v-toolbar>

          <v-data-table
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
            fixed-header
            :height="tableHeight"
          >
            <template v-slot:item.id="{ item, index }">
              {{ index + 1 }}
            </template>

            <template v-slot:item.dateTime="{ item, index }">
              {{ item.date }} {{ item.time }}
            </template>

            <template v-slot:item.avg_total_hours="{ item, index }">
              {{
                item.avg_total_hours
                  ? $dateFormat.minutesToHHMM(parseInt(item.avg_total_hours))
                  : "---"
              }}
            </template>

            <template v-slot:item.branch="{ item, index }">
              <span>
                <b>{{
                  item.employee ? item.employee?.branch?.branch_name : "---"
                }}</b
                ><br />
                {{ item.employee ? item.employee?.department?.name : "---" }}
              </span>
            </template>
            <template v-slot:item.in="{ item, index }">
              {{
                item?.in_log?.device?.function !== "out" ||
                item?.in_log?.device?.function !== "Out"
                  ? "In"
                  : "---"
              }}
            </template>
            <template v-slot:item.out="{ item, index }">
              {{
                item?.out_log?.device?.function == "out" ||
                item?.out_log?.device?.function == "Out"
                  ? "Out"
                  : "---"
              }}
            </template>

            <template v-slot:item.status="{ item, index }">
              {{ item.status }}
              <br />
              <small>{{ item.reason ?? "" }}</small>
            </template>

            <template v-slot:item.customer="{ item }" style="padding: 0px">
              <v-row no-gutters>
                <v-col md="2" class="mr-4">
                  <v-avatar
                    v-if="
                      item && item.customer && item.customer.profile_picture
                    "
                  >
                    <v-img
                      :src="
                        item?.customer?.profile_picture ||
                        '/no-profile-image.jpg'
                      "
                    >
                    </v-img>
                  </v-avatar>
                </v-col>
                <v-col style="padding: 3px" md="8">
                  <strong>
                    {{ item.customer ? item.customer.full_name : "---" }}
                  </strong>
                  <div class="secondary-value">
                    {{ item.user_id }}
                  </div>
                </v-col>
              </v-row>
            </template>
          </v-data-table>
        </v-card>
      </div>
    </v-card>
  </div>

  <NoAccess v-else />
</template>
<script>
export default {
  props: [],

  data: () => ({
    tableHeight: 750,
    status: "",
    department_ids: "",
    employee_id: "",
    daily_date: "",
    to_date: "",

    isFilter: false,
    totalRowsCount: 0,
    snack: false,
    snackColor: "",
    snackText: "",
    date: null,
    menu: false,
    options: {},
    loading: false,
    time_menu: false,
    Model: "Attendance Reports",
    endpoint: "customer-stats-report",
    search: "",
    snackbar: false,
    add_manual_log: false,
    dialog: false,
    generateLogsDialog: false,
    reportSync: false,
    from_menu: false,
    to_menu: false,
    ids: [],
    departments: [],
    employees: [],
    DateRange: true,
    devices: [],

    total: 0,

    payload: {
      report_type: "Date Wise Report",
      from_date: null,
      to_date: null,
      daily_date: null,
      UserID: "",
      department_ids: [],
      status: ``,
      age_category: ``,
      type: ``,
      Gender: ``,
      DeviceID: "",
      branch_id: "",
      include_device_types: ["all", "Access Control"],
    },

    response: "",
    data: [],
    errors: [],
    report_template: "Template1",
    headers: [
      {
        text: "Date",
        align: "left",
        sortable: true,
        key: "date",
        value: "date",
      },
      {
        text: "Avg Hrs",
        align: "left",
        sortable: true,
        key: "avg_total_hours",
        value: "avg_total_hours",
      },
      {
        text: "Male Count",
        align: "left",
        sortable: true,
        key: "male_count",
        value: "male_count",
      },
      {
        text: "Female Count",
        align: "left",
        sortable: true,
        key: "female_count",
        value: "female_count",
      },
      {
        text: "Child Count",
        align: "left",
        sortable: true,
        key: "child_count",
        value: "child_count",
      },

      {
        text: "Younger count",
        align: "left",
        sortable: true,
        key: "younger_count",
        value: "younger_count",
      },
      {
        text: "Adult Count",
        align: "left",
        sortable: true,
        key: "adult_count",
        value: "adult_count",
      },
      {
        text: "Senior Count",
        align: "left",
        sortable: true,
        key: "senior_count",
        value: "senior_count",
      },
      {
        text: "VIP Customers",
        align: "left",
        sortable: true,
        key: "vip_customer_count",
        value: "vip_customer_count",
      },
      {
        text: "Normal Customers",
        align: "left",
        sortable: true,
        key: "normal_customer_count",
        value: "normal_customer_count",
      },
      {
        text: "Total In",
        align: "left",
        sortable: true,
        key: "in_count",
        value: "in_count",
      },
      {
        text: "Total Out",
        align: "left",
        sortable: true,
        key: "out_count",
        value: "out_count",
      },
    ],
    max_date: null,

    isCompany: true,
    branches: [],
  }),

  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  mounted() {
    this.tableHeight = window.innerHeight - 370;
    window.addEventListener("resize", () => {
      this.tableHeight = window.innerHeight - 370;
    });
  },
  created() {
    let branch_header = [
      {
        text: "Branch",
        align: "left",
        sortable: true,
        key: "branch_id", //sorting
        value: "branch_for_stats_only.branch_name", //edit purpose

        filterable: true,
        filterSpecial: true,
      },
    ];
    this.headers.splice(0, 0, ...branch_header);
    this.setFromDate();
    this.getBranches();
    this.getScheduledEmployees();
    this.getDeviceList();
  },
  methods: {
    filterAttr(data) {
      this.payload.from_date = data.from;
      this.payload.to_date = data.to;
      this.getDataFromApi();
    },
    getBranches() {
      if (this.$auth.user.branch_id) {
        this.payload.branch_id = this.$auth.user.branch_id;
        this.isCompany = false;
        return;
      }

      this.$axios
        .get("branch", {
          params: {
            per_page: 1000,
            company_id: this.$auth.user.company_id,
          },
        })
        .then(({ data }) => {
          this.branches = data.data;
        });
    },

    getScheduledEmployees() {
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
        },
      };

      this.$axios.get(`/customer-list`, options).then(({ data }) => {
        this.employees = data;
      });
    },

    getDeviceList() {
      this.$axios
        .get(`/device_list`, {
          params: {
            per_page: 1000,
            company_id: this.$auth.user.company_id,
          },
        })
        .then(({ data }) => {
          this.devices = data.filter((e) => !e.name.includes("Mobile"));
        });
    },

    setFromDate() {
      if (this.payload.from_date == null) {
        const dt = new Date();
        const y = dt.getFullYear();
        const m = dt.getMonth() + 1;
        const formattedMonth = m < 10 ? "0" + m : m;
        this.payload.from_date = `${y}-${formattedMonth}-01`;
      }
    },
    
    can(per) {
      return this.$pagePermission.can(per, this);
    },

    async getDataFromApi() {
      if (!this.payload.from_date) return false;

      if (this.payload.from_date) {
        this.payload.from_date = this.payload.from_date;
      }

      if (this.payload.to_date) {
        this.payload.to_date = this.payload.to_date;
      }
      this.loading = true;
      const { data, total } = await this.$store.dispatch("fetchData", {
        key: "access_control_report",
        options: this.options,
        refresh: true,
        endpoint: this.endpoint,
        filters: this.payload,
      });
      this.data = data;
      this.totalRowsCount = total;
      this.loading = false;
    },

    pdfDownload() {
      let path = process.env.BACKEND_URL + "/pdf";
      let pdf = document.createElement("a");
      pdf.setAttribute("href", path);
      pdf.setAttribute("target", "_blank");
      pdf.click();
    },

    async process_file(type) {
      try {
        if (!this.data || !this.data.length) {
          alert("No data found");
          return;
        }

        const backendUrl = process.env.BACKEND_URL;
        const queryParams = {
          company_id: this.$auth.user.company_id,
          branch_id: this.payload.branch_id,
          UserID: this.payload.UserID,
          DeviceID: this.payload.DeviceID,
          from_date: this.payload.from_date,
          to_date: this.payload.to_date,
          report_type: this.payload.report_type,
          user_type: this.payload.user_type,
        };

        if (this.payload.from_date) {
          queryParams.from_date = this.payload.from_date;
        }

        if (this.payload.to_date) {
          queryParams.to_date = this.payload.to_date;
        }

        const queryString = Object.keys(queryParams)
          .map(
            (key) =>
              `${encodeURIComponent(key)}=${encodeURIComponent(
                queryParams[key]
              )}`
          )
          .join("&");

        const reportUrl = `${backendUrl}/${type}?${queryString}&include_device_types[]=all&include_device_types[]=Access Control`;

        const report = document.createElement("a");
        report.setAttribute("href", reportUrl);
        report.setAttribute("target", "_blank");
        report.click();
      } catch (error) {
        console.error("Error processing file:", error.message);
        // Handle the error (e.g., show an error message to the user)
      }
    },
  },
};
</script>
